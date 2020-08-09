<?php 
// Database connection
require_once($_SERVER['DOCUMENT_ROOT'].'/oms/config/ConConfig.php');
// mt defined functions
include_once($_SERVER['DOCUMENT_ROOT'].'/oms/lib/mtdefined/mt-function.php');
//Start a php session, Get Active User Info
require_once($_SERVER['DOCUMENT_ROOT']."/oms/config/Session.php");
//file to track users access, must include Session.php and ConConfig.php before this
require_once($_SERVER['DOCUMENT_ROOT']."/oms/config/UsersLog.php");
// Page tempelate
require_once($_SERVER['DOCUMENT_ROOT']."/oms/src/PageTemp.php");
//phpexcel classe
require_once($_SERVER['DOCUMENT_ROOT']."/oms/lib/PHPExcel-1.8/Classes/PHPExcel/IOFactory.php");

// Initiate template objects
$tmpl = new PageTempelate(); //new tempelate object
$lnks = new PageLinks();	 //new lnks object

// Set max execution time to infinite
ini_set('max_execution_time', 0);

if(isset($_POST['btnUpdCompData'])){
// Form input variables
$itemNo = mysqli_real_escape_string($conn, $_POST['txtItemNo']);
$mtrlType = mysqli_real_escape_string($conn, $_POST['txtMaterialType']);
$itemSize = mysqli_real_escape_string($conn, $_POST['txtSize']);
$itemColor = mysqli_real_escape_string($conn, $_POST['txtColor']);
$moq = mysqli_real_escape_string($conn, $_POST['txtMOQ']);
$itemNetWt = mysqli_real_escape_string($conn, $_POST['txtItemNetWt']);
$itemGrsWt = mysqli_real_escape_string($conn, $_POST['txtItemGrsWt']);
$gsm = mysqli_real_escape_string($conn, $_POST['txtGSM']);
$gsf = mysqli_real_escape_string($conn, $_POST['txtGSF']);
$lSide = mysqli_real_escape_string($conn, $_POST['txtLongestSide']);
$mSide = mysqli_real_escape_string($conn, $_POST['txtMedianSide']);
$sSide = mysqli_real_escape_string($conn, $_POST['txtShortestSide']);
$vendor = mysqli_real_escape_string($conn, $_POST['txtVendor']);
$ip = mysqli_real_escape_string($conn, $_POST['txtIP']);
$mp = mysqli_real_escape_string($conn, $_POST['txtMP']);
$ctnNetWt = mysqli_real_escape_string($conn, $_POST['txtCtnNetWt']);
$ctnGrsWt = mysqli_real_escape_string($conn, $_POST['txtCtnGrsWt']);
$ctnLn = mysqli_real_escape_string($conn, $_POST['txtCtnLn']);
$ctnWd = mysqli_real_escape_string($conn, $_POST['txtCtnWd']);
$ctnHt = mysqli_real_escape_string($conn, $_POST['txtCtnHt']);
$pckgAccs = mysqli_real_escape_string($conn, $_POST['txtPckgAccs']);
$pckgRemarks = mysqli_real_escape_string($conn, $_POST['txtPckgRemarks']);
$remarks = mysqli_real_escape_string($conn, $_POST['txtRemarks']);

// Query to check item exist in compiled database
$sql = $conn->query("SELECT item_no FROM invt_compiled WHERE item_no = '$itemNo'") or die("An error occurred: ".$conn->error);

if($sql->num_rows > 0){
	// Update
	$sql = $conn->query("UPDATE invt_compiled 
		SET material_type = '$mtrlType', size = '$itemSize', 
		color = '$itemColor',  moq = '$moq', 
		item_net_wt = '$itemNetWt', item_grs_wt = '$itemGrsWt', 
		gsm = '$gsm', gsf = '$gsf', 
		longest_side = '$lSide', median_side = '$mSide', shortest_side = '$sSide', 
		internal_pack = '$ip', master_pack = '$mp', 
		ctn_dim_ln = '$ctnLn', ctn_dim_wd = '$ctnWd', ctn_dim_ht = '$ctnHt', 
		net_wt_ctn = '$ctnNetWt', grs_wt_ctn = '$ctnGrsWt', 
		packing_accessories = '$pckgAccs', packing_remarks = '$pckgRemarks', 
		supplier = '$vendor', remarks = '$remarks' 
		WHERE item_no = '$itemNo'") 
	or die("An error occurred: ".$conn->error);
	// Show message
	if($sql){
		echo '
		<h1><img src="/oms/img/icons/actions/Ok-32.png" alt="Ok">Update successful!</h1>
		';
	}
	else{
		echo '
		<h1><img src="/oms/img/ /actions/attention-32.png" alt="Ok">An error occurred!</h1>
		';
	}
}
else{
	// Insert
	$sql = $conn->query("INSERT INTO invt_compiled(item_no, material_type, size, color, moq, item_net_wt, item_grs_wt, gsm, gsf, longest_side, median_side, shortest_side, internal_pack, master_pack, ctn_dim_ln, ctn_dim_wd, ctn_dim_ht, net_wt_ctn, grs_wt_ctn, packing_accessories, packing_remarks, supplier, remarks) VALUES('$itemNo', '$mtrlType', '$itemSize', '$itemColor', '$moq', '$itemNetWt', '$itemGrsWt', '$gsm', '$gsf', '$lSide', '$mSide', '$sSide', '$ip', '$mp', '$ctnLn', '$ctnWd', '$ctnHt', '$ctnNetWt', '$ctnGrsWt', '$pckgAccs', '$pckgRemarks', '$vendor', '$remarks')") or die("An error occurred during inserts: ".$conn->error);
	if($sql){
		echo '
		<h1><img src="/oms/img/icons/actions/Ok-32.png" alt="Ok">Update successful!</h1>
		';
	}
	else{
		echo '
		<h1><img src="/oms/img/icons/actions/attention-32.png" alt="Ok">An error occurred!</h1>
		';
	}
}
}

/*
|-----------------------------------------------
|		BULK UPDATE COMPILED DATA NEW VER
|-----------------------------------------------
*/
if(isset($_POST['btnBulkUpdCompData'])){
	// Uploaded files data
	$fileName = $_FILES['fileBulkUpdCompData']['name'];
	$tempName = $_FILES['fileBulkUpdCompData']['tmp_name'];
	$fileType = $_FILES['fileBulkUpdCompData']['type'];
	$fileSize = $_FILES['fileBulkUpdCompData']['size'];

	// Create excel file reader
	$xls_r = PHPExcel_IOFactory::createReader('Excel2007');
	$xls_r->setReadDataOnly(true);

	// Create excel object
	$xls = $xls_r->load($tempName);

	// Set worksheet
	$ws = $xls->getSheetByName('Template');

	/*
	|-------------------------------------------
	|	Validate existence of Template sheet
	|-------------------------------------------
	 */
	if($ws){ 
		/*
		|-----------------------------------------------
		|	Validate uploaded template sheet version
		|-----------------------------------------------
		*/
		// File name and version
		$file_name = $ws->getCellByColumnAndRow(1, 1)->getValue();
		$file_version = $ws->getCellByColumnAndRow(1, 2)->getValue();

		if($file_name === 'Bulk Update Compiled Data' && $file_version === 'MT2018.02X2007BUCD'){
			/*
			|------------------------------------------------------
			|	Loop through row 4 columns to load update columns
			|------------------------------------------------------
			*/
			// Get array of fields to be updated
			$fields = array();

			for($col = 1; $col <= 22; $col++){
				// Get column name, number and data
				switch($col){
					case 1:
						$col_num  = 1;
						$col_name = 'material_type';
						$col_data = $ws->getCellByColumnAndRow(1, 4)->getValue();
						break;
					case 2:
						$col_num  = 2;
						$col_name = 'size';
						$col_data = $ws->getCellByColumnAndRow(2, 4)->getValue();
						break;
					case 3:
						$col_num  = 3;
						$col_name = 'color';
						$col_data = $ws->getCellByColumnAndRow(3, 4)->getValue();
						break;
					case 4:
						$col_num  = 4;
						$col_name = 'moq';
						$col_data = $ws->getCellByColumnAndRow(4, 4)->getValue();
						break;
					case 5:
						$col_num  = 5;
						$col_name = 'item_net_wt';
						$col_data = $ws->getCellByColumnAndRow(5, 4)->getValue();
						break;
					case 6:
						$col_num  = 6;
						$col_name = 'item_grs_wt';
						$col_data = $ws->getCellByColumnAndRow(6, 4)->getValue();
						break;
					case 7:
						$col_num  = 7;
						$col_name = 'gsm';
						$col_data = $ws->getCellByColumnAndRow(7, 4)->getValue();
						break;
					case 8:
						$col_num  = 8;
						$col_name = 'gsf';
						$col_data = $ws->getCellByColumnAndRow(8, 4)->getValue();
						break;
					case 9:
						$col_num  = 9;
						$col_name = 'longest_side';
						$col_data = $ws->getCellByColumnAndRow(9, 4)->getValue();
						break;
					case 10:
						$col_num  = 10;
						$col_name = 'median_side';
						$col_data = $ws->getCellByColumnAndRow(10, 4)->getValue();
						break;
					case 11:
						$col_num  = 11;
						$col_name = 'shortest_side';
						$col_data = $ws->getCellByColumnAndRow(11, 4)->getValue();
						break;
					case 12:
						$col_num  = 12;
						$col_name = 'internal_pack';
						$col_data = $ws->getCellByColumnAndRow(12, 4)->getValue();
						break;
					case 13:
						$col_num  = 13;
						$col_name = 'master_pack';
						$col_data = $ws->getCellByColumnAndRow(13, 4)->getValue();
						break;
					case 14:
						$col_num  = 14;
						$col_name = 'ctn_dim_ln';
						$col_data = $ws->getCellByColumnAndRow(14, 4)->getValue();
						break;
					case 15:
						$col_num  = 15;
						$col_name = 'ctn_dim_wd';
						$col_data = $ws->getCellByColumnAndRow(15, 4)->getValue();
						break;
					case 16:
						$col_num  = 16;
						$col_name = 'ctn_dim_ht';
						$col_data = $ws->getCellByColumnAndRow(16, 4)->getValue();
						break;
					case 17:
						$col_num  = 17;
						$col_name = 'net_wt_ctn';
						$col_data = $ws->getCellByColumnAndRow(17, 4)->getValue();
						break;
					case 18:
						$col_num  = 18;
						$col_name = 'grs_wt_ctn';
						$col_data = $ws->getCellByColumnAndRow(18, 4)->getValue();
						break;
					case 19:
						$col_num  = 19;
						$col_name = 'packing_accessories';
						$col_data = $ws->getCellByColumnAndRow(19, 4)->getValue();
						break;
					case 20:
						$col_num  = 20;
						$col_name = 'packing_remarks';
						$col_data = $ws->getCellByColumnAndRow(20, 4)->getValue();
						break;		
					case 21:
						$col_num  = 21;
						$col_name = 'supplier';
						$col_data = $ws->getCellByColumnAndRow(21, 4)->getValue();
						break;	
					case 22:
						$col_num  = 22;
						$col_name = 'remarks';
						$col_data = $ws->getCellByColumnAndRow(22, 4)->getValue();
						break;	
					default:
						$col_num  = '';
						$col_name = '';
						$col_data = '';
						break;
				} // End switch statement

				// Add columns with update status to $fields array
				if($col_data == 'Update') $fields[$col_name] = $col_num;	

			} // End $col for loop

			// Get highest data row of worksheet
			$rowCount = $ws->getHighestDataRow();

			// Initialize loop processing flags
			$processing_flag = false;
			$item_with_error = array();

			/*
			|--------------------------------------------
			|	Loop through each data row
			|--------------------------------------------
			*/
			for($row = 6; $row <= $rowCount; $row++){
				// Get item number
				$itemNo = $ws->getCellByColumnAndRow(0, $row)->getValue();

				// Loop through the columns that user wants to update
				foreach($fields as $col_name=>$col_num){
					// Get selected column cell data
					$cell_data = $ws->getCellByColumnAndRow($col_num, $row)->getValue();

					// Query to check item existense in database
					$query = $conn->query("SELECT item_no 
						FROM invt_compiled 
						WHERE item_no = '$itemNo'") 
					or die("An error occurred: ".$conn->error);

					// Check for empty response
					if($query->num_rows > 0){
						// Update query
						$query = $conn->query("UPDATE invt_compiled 
							SET ".$col_name." = '$cell_data' 
							WHERE item_no = '$itemNo'") 
						or die("An error occurred during update: ".$conn->error);

						// Check for successfull query
						if($query) $processing_flag = true;
						else{
							$processing_flag = false;
							array_push($item_with_error, $itemNo);
						}
					}
					else{
						// Insert query
						$query = $conn->query("INSERT INTO invt_compiled(item_no, 
							".$col_name.") VALUES('$itemNo', '$cell_data')") 
						or die("An error occurred during insert: ".$conn->error);

						// Check for successfull query
						if($query) $processing_flag = true;
						else{
							$processing_flag = false;
							array_push($item_with_error, $itemNo);
						}
					}
				} // End foreach loop for $fields array
			} // End $row for loop

			/*
			|--------------------------------------------
			|	Check for loop processing final status
			|--------------------------------------------
			*/
			if(empty($item_with_error)){
				// Success
				echo '
					<div class="alert alert-success alert-dismissable">
					  	<a href="#" class="close" data-dismiss="alert" aria-label="close">&times;</a>
					  	<strong>Congrats!</strong> Compiled data successfully updated for uploaded file!
					</div>
				';
			}
			else{
				// Errors
				echo '
					<div class="alert alert-warning alert-dismissable">
					  	<a href="#" class="close" data-dismiss="alert" aria-label="close">&times;</a>
					  	<strong>Warning!</strong> Following items compiled data not updated. Please check and correct the items data and re-upload the file!
					  	<table class="table table-hover table-condensed">
							<tr class="warning">
								<th class="text-center">Item Numbers With Errors</th>
							</tr>
					  	
				';
				// Print error items in parents table rows
				foreach($item_with_error as $errorItemNo){
					echo '<tr><td class="text-center">'.$errorItemNo.'</td></tr>';
				}
				echo '</table></div>';
			}
		}
		else{
			echo '
				<div class="alert alert-danger alert-dismissable">
				  	<a href="#" class="close" data-dismiss="alert" aria-label="close">&times;</a>
				  	<strong>Oops!</strong> Please upload a valid file. Uploaded file version did not match!
				</div>
			';
		} // End uploaded template sheet version validation
	} // End of sheet existence check
	else{
		echo '
			<div class="alert alert-danger alert-dismissable">
			  	<a href="#" class="close" data-dismiss="alert" aria-label="close">&times;</a>
			  	<strong>Oops!</strong> Template sheet not found!
			</div>
		';
	}
}	
?>