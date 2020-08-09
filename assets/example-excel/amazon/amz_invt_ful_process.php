<?php  
// Modify PHP default settings
ini_set('max_execution_time', 0);
ini_set('memory_limit','1024M');

// Connect to database
require_once($_SERVER['DOCUMENT_ROOT']."/oms/config/ConConfig.php");
// Custom defined functions
require_once($_SERVER['DOCUMENT_ROOT']."/oms/lib/mtdefined/mt-function.php");
// Start a php session, Get Active User Info
require_once($_SERVER['DOCUMENT_ROOT']."/oms/config/Session.php");
// File to track users access, must include Session.php and ConConfig.php before this
require_once($_SERVER['DOCUMENT_ROOT']."/oms/config/UsersLog.php");

// Form input variables
$mktp_id = $_POST['txtMarketplaceId'];
$fm_date = date('Y-m-d', strtotime($_POST['txtFromDate']));
$to_date = date('Y-m-d', strtotime($_POST['txtToDate']));
$hs_year = $_POST['txtNumHistYears'];
$sku_list= $_POST['txtSkuList'];

/*
|---------------------------------------
|        CALCULATE CURRENT ROS
|---------------------------------------
*/
if(isset($_POST['btnCalCurrROS'])){
	
	// First delete all records in amazon_ros table
	$query = $conn->query("DELETE FROM amazon_ros") 
	or die("An error occurred: ".$conn->error);

	// Query to get last 13 week numbers
	$query = $conn->query("SELECT DISTINCT YEARWEEK(purchase_date, 6) 
		AS purchase_week 
		FROM amazon_sales 
		WHERE purchase_date >= '$fm_date' 
		AND purchase_date <= '$to_date' 
		AND marketplace_id = '$mktp_id' 
		ORDER BY YEARWEEK(purchase_date) DESC") 
	or die("An error occurred: ".$conn->error);

	// Store query result into an array
	$purchase_weeks = array();

	// Loop through query result
	while($result = $query->fetch_array()){
		// Store results in purchase_weeks array
		$purchase_weeks[] = $result['purchase_week'];
	} 

	// Calculate current ROS for selected SKU list type
	// FBA SOLD SKUs
	if($sku_list == 'skulist1'){
		// Query to get unique items sold in given date range
		$query = $conn->query("SELECT DISTINCT item_no 
			FROM amazon_sales 
			WHERE purchase_date >= '$fm_date' 
			AND purchase_date <= '$to_date' 
			AND marketplace_id = '$mktp_id' 
			ORDER BY item_no ASC") 
		or die("An error occurred: ".$conn->error);
	}
	// FBA SKUs
	elseif($sku_list == 'skulist2'){
		$query = $conn->query("SELECT item_no 
			FROM amazon_fba_invt 
			WHERE item_no NOT LIKE 'CA%'") 
		or die("An error occurred: ".$conn->error);
	}
	/*
	|---------------------------------------------
	|	FBA WORKING SKU'S 
	|---------------------------------------------
	|   Only those SKU's which have inventory 
	|   in any stage and should not be taken for 
	| 	work.
	|---------------------------------------------
	 */
	elseif($sku_list == 'skulist3'){
		$query = $conn->query("SELECT invt_warehouse.item_no, IFNULL(invt_config.work_exp_date, '0000-00-00') AS work_exp_date 
			FROM invt_warehouse 
			LEFT JOIN amazon_fba_invt 
			ON invt_warehouse.item_no = amazon_fba_invt.item_no 
			LEFT JOIN amazon_reserved_invt 
			ON invt_warehouse.item_no = amazon_reserved_invt.item_no 
			LEFT JOIN invt_config 
			ON invt_warehouse.item_no = invt_config.item_no 
			LEFT JOIN 
				(SELECT imp_shp_lines.item_no, SUM(imp_shp_lines.shp_qty) 
				AS on_water_qty, imp_shp_header.imp_shp_status  
				FROM imp_shp_lines 
				LEFT JOIN imp_shp_header 
				ON imp_shp_lines.imp_shp_id = imp_shp_header.imp_shp_id 
				WHERE imp_shp_header.imp_shp_status = 'Active' 
				OR imp_shp_header.imp_shp_status = 'Draft' 
				GROUP BY imp_shp_lines.item_no)
			AS imp_shp 
			ON invt_warehouse.item_no = imp_shp.item_no 
			WHERE invt_warehouse.on_order_qty > 0 
			OR invt_warehouse.on_hand_qty > 0 
			OR amazon_fba_invt.afn_inbound_working_qty > 0 
			OR amazon_fba_invt.afn_inbound_shipped_qty > 0 
			OR amazon_fba_invt.afn_inbound_receiving_qty > 0 
			OR amazon_fba_invt.afn_fulfillable_qty > 0 
			OR amazon_reserved_invt.reserved_fc_transfers > 0 
			OR amazon_reserved_invt.reserved_fc_processing > 0 
			OR imp_shp.on_water_qty > 0") 
		or die("An error occurred: ".$conn->error);
	}

	// Set error flags
	$flag = false;
	
	// Loop through item numbers query result
	while($result = $query->fetch_array()){
		// Get current iteration item number
		$itemNo = $result['item_no'];
		if(date('Y-m-d', strtotime($result['work_exp_date'])) > date('Y-m-d')){
			$workingStatus = "WORKING";
		}
		else $workingStatus = "PENDING";
		// Create an empty array to store weekly sale qty
		$week_sale_qty = array();

		// Loop through $purchase_weeks[] array
		foreach($purchase_weeks as $pur_wk){
			// Query to get total shipped quantity in week
			$query_qty = $conn->query("SELECT SUM(quantity_shipped) 
				AS tot_qty_shp 
				FROM amazon_sales 
				WHERE item_no = '$itemNo' 
				AND YEARWEEK(purchase_date, 6) = '$pur_wk' 
				AND marketplace_id = '$mktp_id'") 
			or die("An error occurred: ".$conn->error);

			// Store query result into an array
			$result_qty = $query_qty->fetch_array();

			// Insert tot_qty_shp into an array
			if($result_qty['tot_qty_shp'] > 0){
				array_push($week_sale_qty, $result_qty['tot_qty_shp']);
			}
		} // End foreach week loop

		// Total sale qty in last 13 weeks
		$tot13WksSale = array_sum(array_slice($week_sale_qty, 0, 13));
		// Number of weeks that has sales in last 13 weeks
		$numOfSaleWks = count(array_slice($week_sale_qty, 0, 13));
		// Calculate current ROS for last 13 weeks sale
		if($numOfSaleWks > 0){
			
			$currentROS = round($tot13WksSale/$numOfSaleWks);
		}
		else{
			$currentROS = 0;	
		}

		// Query to insert current ros details into database
		$query_insert = $conn->query("INSERT INTO 
			amazon_ros(item_no, working_status, curr_sales_qty, curr_sale_weeks, curr_ros) 
			VALUES('$itemNo', '$workingStatus', '$tot13WksSale', '$numOfSaleWks', '$currentROS')") 
		or die("An error occurred: ".$conn->error);

		if($query_insert) $flag = true;
		else $flag = false; 
	} // End unique item_no loop

	// Check for successfull execution program
	if($flag){
		echo '
			<div class="alert alert-success alert-dismissible">
			  <a href="#" class="close" data-dismiss="alert" aria-label="close">&times;</a>
			  <strong>Success!</strong> Current ROS calculations completed and data saved into database.
			</div>
		';
	}
	else{
		// Delete stored data on failed execution of program
		$query_delete = $conn->query("DELETE FROM amazon_ros") 
		or die("An error occurred: ".$conn->error);

		if($query_delete){
			echo '
				<div class="alert alert-danger alert-dismissible">
				  <a href="#" class="close" data-dismiss="alert" aria-label="close">&times;</a>
				  <strong>Oops!</strong> An error occurred during calculation of current ROS. Please go back by clicking on previous button and try again.
				</div>
			';
		}
	}
}
/*
|---------------------------------------
|        CALCULATE HISTORICAL ROS
|---------------------------------------
*/
elseif(isset($_POST['btnCalHistROS'])){
	// Query to get item numbers to calculate historical ros
	/*$query = $conn->query("SELECT item_no FROM amazon_ros") 
	or die("An error occurred: ".$conn->error);

	if($query->num_rows > 0){*/
	if(count($_POST['cbxSKUsList']) > 0){
		// Set error flags
		$flag = false;

		// Loop through the query result
		//while($result = $query->fetch_array()){
		foreach($_POST['cbxSKUsList'] as $itemNo){
			// Get current item number
			//$itemNo = $result['item_no'];

			// Get yearly totals for all calculated values in an array
 			$year['totals'] = array();

 			// Loop through number of historical years
 			for($hy=1; $hy<=$hs_year; $hy++){
 				// Get historical 13 weeks date from given carrent to_date
		 		$hst_fm_date = get_date($fm_date, "-$hy year");
		 		$hst_to_date = get_date($to_date, "-$hy year");

		 		// Create an empty array to store historical weeks sales
		 		$hst_week_sale_qty = array();

		 		// Loop through 13 weeks
		 		for($wk=1; $wk<=13; $wk++){
		 			// Days of interval
		 			$interval = $wk*7;

		 			// Start and end date of week for 13 weeks historical date
		 			$hst_wk_end_date = get_date($hst_fm_date, "+$interval days");
		 			$hst_wk_str_date = get_date($hst_wk_end_date, "-7 days");

		 			// Query to get total shipped qty in historical date
		 			$query_hstqty = $conn->query("SELECT SUM(quantity_shipped) 
		 				AS tot_hst_qty_shp 
		 				FROM amazon_sales 
						WHERE item_no = '$itemNo' 
						AND purchase_date >= '$hst_wk_str_date' 
						AND purchase_date <= '$hst_wk_end_date' 
						AND marketplace_id = '$mktp_id'") 
					or die("An error occurred: ".$conn->error);

					// Get query results
					$result_hstqty = $query_hstqty->fetch_array();

					// Store into array if historical sales quantity is greater than zero
					if($result_hstqty['tot_hst_qty_shp'] > 0){
						array_push($hst_week_sale_qty, $result_hstqty['tot_hst_qty_shp']);
					}
		 		}// End for loop 13 weeks

		 		// Calculate historical 13 weeks ROS for each year
			 	if(count($hst_week_sale_qty) > 0){
			 		$yearly_ros = round(array_sum($hst_week_sale_qty)/count($hst_week_sale_qty));
			 	}
			 	else{
			 		$yearly_ros = 0;
			 	}

			 	// Yearly total sales, num of sale weeks and ROS into array
			 	// Year indexing may create errors please check back here when they starts appearing
				$year['totals']['qty'][date('Y', strtotime($hst_fm_date))] = array_sum($hst_week_sale_qty);
				$year['totals']['wks'][date('Y', strtotime($hst_fm_date))] = count($hst_week_sale_qty);
				$year['totals']['ros'][date('Y', strtotime($hst_fm_date))] = $yearly_ros;
 			} // End for loop through historical years

 			// Total sales quantity for all historical years
		 	$totHstSaleQty = array_sum($year['totals']['qty']);
		 	// Total number of historical sale weeks
		 	$totHstSaleWks = array_sum($year['totals']['wks']);
		 	// Calculate historical ROS if number of totHstSaleWks is greater than 0
		 	if($totHstSaleWks > 0){
		 		$historicalROS = round($totHstSaleQty/$totHstSaleWks);
		 	}
		 	else{
		 		$historicalROS = 0;
		 	}

		 	// Insert into database
		 	$query_insert = $conn->query("UPDATE amazon_ros 
		 		SET hist_sales_qty = '$totHstSaleQty', 
		 		hist_sale_weeks = '$totHstSaleWks', hist_ros = '$historicalROS' 
		 		WHERE item_no = '$itemNo'") 
		 	or die("An error occurred: ".$conn->error);

		 	if($query_insert){
		 		$flag = true;
		 	}
		 	else{
		 		$flag = false;
		 	}
		} // End while loop of item numbers

		if($flag){
			echo '
				<div class="alert alert-success alert-dismissible">
				  <a href="#" class="close" data-dismiss="alert" aria-label="close">&times;</a>
				  <strong>Success!</strong> Historical ROS calculations completed and data saved into database.
				</div>
			';
		}
		else{
			echo '
				<div class="alert alert-danger alert-dismissible">
				  <a href="#" class="close" data-dismiss="alert" aria-label="close">&times;</a>
				  <strong>Oops!</strong> An error occurred during calculation of historical ROS. Please go back by clicking on previous button and try again.
				</div>
			';
		}
	}/*x*/
	else{
		echo '
			<div class="alert alert-danger alert-dismissible">
			  <a href="#" class="close" data-dismiss="alert" aria-label="close">&times;</a>
			  <strong>Oops!</strong> No items found to calculate historical ROS. Please go to previous step and calculate current ROS first.
			</div>
		';
	}
}
/*
|---------------------------------------
|        CALCULATE FORCASTED ROS
|---------------------------------------
*/
elseif(isset($_POST['btnCalForcROS'])){
	// Query to get item numbers to calculate historical ros
	/*$query = $conn->query("SELECT item_no FROM amazon_ros") 
	or die("An error occurred: ".$conn->error);

	if($query->num_rows > 0){*/
	if(count($_POST['cbxSKUsList']) > 0){	
		// Set error flags
		$flag = false;

		// Loop through the query result
		//while($result = $query->fetch_array()){
		foreach($_POST['cbxSKUsList'] as $itemNo){
			// Get current item number
			//$itemNo = $result['item_no'];

			// Get yearly totals for all calculated values in an array
 			$year['totals'] = array();

 			// Loop through number of historical years
 			for($hy=1; $hy<=$hs_year; $hy++){
 				// Get future forcasted historical 13 weeks date from given carrent to_date
		 		$hst_fm_date = get_date($to_date, "-$hy year +1 day");
		 		$hst_to_date = get_date($to_date, "-$hy year +13 weeks");

		 		// Create an empty array to store historical weeks sales
		 		$hst_week_sale_qty = array();

		 		// Loop through 13 weeks
		 		for($wk=1; $wk<=13; $wk++){
		 			// Days of interval
		 			$interval = $wk*7;

		 			// Start and end date of week for 13 weeks historical date
		 			$hst_wk_end_date = get_date($hst_fm_date, "+$interval days");
		 			$hst_wk_str_date = get_date($hst_wk_end_date, "-7 days");

		 			// Query to get total shipped qty in historical date
		 			$query_hstqty = $conn->query("SELECT SUM(quantity_shipped) 
		 				AS tot_hst_qty_shp 
		 				FROM amazon_sales 
						WHERE item_no = '$itemNo' 
						AND purchase_date >= '$hst_wk_str_date' 
						AND purchase_date <= '$hst_wk_end_date' 
						AND marketplace_id = '$mktp_id'") 
					or die("An error occurred: ".$conn->error);

					// Get query results
					$result_hstqty = $query_hstqty->fetch_array();

					// Store into array if historical sales quantity is greater than zero
					if($result_hstqty['tot_hst_qty_shp'] > 0){
						array_push($hst_week_sale_qty, $result_hstqty['tot_hst_qty_shp']);
					}
		 		}// End for loop 13 weeks

		 		// Calculate historical 13 weeks ROS for each year
			 	if(count($hst_week_sale_qty) > 0){
			 		$yearly_ros = round(array_sum($hst_week_sale_qty)/count($hst_week_sale_qty));
			 	}
			 	else{
			 		$yearly_ros = 0;
			 	}

			 	// Yearly total sales, num of sale weeks and ROS into array
			 	// Year indexing may create errors please check back here when they starts appearing
				$year['totals']['qty'][date('Y', strtotime($hst_fm_date))] = array_sum($hst_week_sale_qty);
				$year['totals']['wks'][date('Y', strtotime($hst_fm_date))] = count($hst_week_sale_qty);
				$year['totals']['ros'][date('Y', strtotime($hst_fm_date))] = $yearly_ros;
 			} // End for loop through historical years

 			// Total forcasted sales quantity for all historical years
		 	$totFcstSaleQty = array_sum($year['totals']['qty']);
		 	// Total number of historical sale weeks
		 	$totFcstSaleWks = array_sum($year['totals']['wks']);
		 	// Calculate historical ROS if number of totHstSaleWks is greater than 0
		 	if($totFcstSaleWks > 0){
		 		$forcastedROS = round($totFcstSaleQty/$totFcstSaleWks);
		 	}
		 	else{
		 		$forcastedROS = 0;
		 	}

		 	// Insert into database
		 	$query_insert = $conn->query("UPDATE amazon_ros 
		 		SET fcst_sales_qty = '$totFcstSaleQty', 
		 		fcst_sale_weeks = '$totFcstSaleWks', fcst_ros = '$forcastedROS' 
		 		WHERE item_no = '$itemNo'") 
		 	or die("An error occurred: ".$conn->error);

		 	if($query_insert){
		 		$flag = true;
		 	}
		 	else{
		 		$flag = false;
		 	}
		} // End while loop of item numbers

		if($flag){
			echo '
				<div class="alert alert-success alert-dismissible">
				  <a href="#" class="close" data-dismiss="alert" aria-label="close">&times;</a>
				  <strong>Success!</strong> Forcasted ROS calculations completed and data saved into database.
				</div>
			';
		}
		else{
			echo '
				<div class="alert alert-danger alert-dismissible">
				  <a href="#" class="close" data-dismiss="alert" aria-label="close">&times;</a>
				  <strong>Oops!</strong> An error occurred during calculation of forcasted ROS. Please go back by clicking on previous button and try again.
				</div>
			';
		}
	}
	else{
		echo '
			<div class="alert alert-danger alert-dismissible">
			  <a href="#" class="close" data-dismiss="alert" aria-label="close">&times;</a>
			  <strong>Oops!</strong> No items found to calculate historical ROS. Please go to previous step and calculate current ROS first.
			</div>
		';
	}
}



?>