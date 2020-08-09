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
// PHP Mailer
require_once($_SERVER['DOCUMENT_ROOT']."/oms/lib/phpmailer/PHPMailerAutoload.php");
// PHPExcel
require_once($_SERVER['DOCUMENT_ROOT']."/oms/lib/PHPExcel-1.8/Classes/PHPExcel.php");

// Create excel object
$objPHPExcel = new PHPExcel();

/*
|-----------------------------------
|      RATE OF SALE REPORT
|-----------------------------------
*/
if($_GET['reportid'] == 'rosrpt'){
	// Set excel file properties
	$objPHPExcel->getProperties()
		->setCreator("Muhammad Tarique")
		->setLastModifiedBy("Muhammad Tarique")
		->setTitle("Rate of Sale Report")
		->setSubject("Rate of Sale Report")
		->setDescription("Contains all rate of sales")
		->setCategory("Inventory Fulfilment Reports");

	// Get active working sheet
	$objPHPExcel->getActiveSheetIndex(0);

	// Set auto filter
	$objPHPExcel->getActiveSheet()->setAutoFilter("A1:N1");

	// Heading row style
	$objPHPExcel->getActiveSheet()->getStyle("A1:N1")
		->getAlignment()
		->applyFromArray(
			array(
				'horizontal' => PHPExcel_Style_Alignment::HORIZONTAL_CENTER,
				'vertical'   => PHPExcel_Style_Alignment::VERTICAL_CENTER,
				'rotation'   => 0,
				'wrap'       => true
			)
		);

	// Set bold fonts for header
	$objPHPExcel->getActiveSheet()->getStyle("A1:N1")->getFont()->setBold(true);

	// Set color for header cells
	$objPHPExcel->getActiveSheet()
	    ->getStyle("A1:B1")
	    ->getFill()
	    ->setFillType(PHPExcel_Style_Fill::FILL_SOLID)
	    ->getStartColor()
	    ->setARGB('2E9AFE');
	$objPHPExcel->getActiveSheet()
	    ->getStyle("C1:E1")
	    ->getFill()
	    ->setFillType(PHPExcel_Style_Fill::FILL_SOLID)
	    ->getStartColor()
	    ->setARGB('FF0080');
	$objPHPExcel->getActiveSheet()
	    ->getStyle("F1:H1")
	    ->getFill()
	    ->setFillType(PHPExcel_Style_Fill::FILL_SOLID)
	    ->getStartColor()
	    ->setARGB('4EDA50');
	$objPHPExcel->getActiveSheet()
	    ->getStyle("I1:K1")
	    ->getFill()
	    ->setFillType(PHPExcel_Style_Fill::FILL_SOLID)
	    ->getStartColor()
	    ->setARGB('B1B3B1');
	$objPHPExcel->getActiveSheet()
	    ->getStyle("L1:M1")
	    ->getFill()
	    ->setFillType(PHPExcel_Style_Fill::FILL_SOLID)
	    ->getStartColor()
	    ->setARGB('FACC2E');
	$objPHPExcel->getActiveSheet()
	    ->getStyle("N1")
	    ->getFill()
	    ->setFillType(PHPExcel_Style_Fill::FILL_SOLID)
	    ->getStartColor()
	    ->setARGB('9FF781');

	// Set auto columns width for one column
	$objPHPExcel->getActiveSheet()->getColumnDimension('B')->setAutoSize(true);

	// Show/Hide columns
	$objPHPExcel->getActiveSheet()->getColumnDimension('C')->setVisible(false);
	$objPHPExcel->getActiveSheet()->getColumnDimension('F')->setVisible(false);
	$objPHPExcel->getActiveSheet()->getColumnDimension('I')->setVisible(false);
	
	// Freeze row and columns
	$objPHPExcel->getActiveSheet()->freezePane("D2");

	// Define name for active sheet and header row cells
	$objPHPExcel->getActiveSheet()
		->setTitle("ROS Report")
		->setCellValue("A1", "Item Number")
		->setCellValue("B1", "Description")
		->setCellValue("C1", "Historical Sales Qty")
		->setCellValue("D1", "Historical Sale Weeks")
		->setCellValue("E1", "Historical ROS")
		->setCellValue("F1", "Current Sales Qty")
		->setCellValue("G1", "Current Sale Weeks")
		->setCellValue("H1", "Current ROS")
		->setCellValue("I1", "Forcasted Sales Qty")
		->setCellValue("J1", "Forcasted Sale Weeks")
		->setCellValue("K1", "Forcasted ROS")
		->setCellValue("L1", "Historical VS Forcasted (%)")
		->setCellValue("M1", "Projected ROS")
		->setCellValue("N1", "Default ROS");

	// Query to get ros details
	$query = $conn->query("SELECT amazon_ros.*, invt_warehouse.description, invt_config.amz_def_ros 
		FROM amazon_ros 
		LEFT JOIN invt_warehouse 
		ON amazon_ros.item_no = invt_warehouse.item_no 
		LEFT JOIN invt_config 
		ON amazon_ros.item_no = invt_config.item_no") 
	or die("An error occurred: ".$conn->error);

	// Start row count from 2nd row
	$rn = 2;

	// Loop through the query results
	while($result = $query->fetch_array()){
		// Print details in excel sheet
		$objPHPExcel->getActiveSheet()
			->setCellValue("A$rn", $result['item_no'])
			->setCellValue("B$rn", $result['description'])
			->setCellValue("C$rn", $result['hist_sales_qty'])
			->setCellValue("D$rn", $result['hist_sale_weeks'])
			->setCellValue("E$rn", $result['hist_ros'])
			->setCellValue("F$rn", $result['curr_sales_qty'])
			->setCellValue("G$rn", $result['curr_sale_weeks'])
			->setCellValue("H$rn", $result['curr_ros'])
			->setCellValue("I$rn", $result['fcst_sales_qty'])
			->setCellValue("J$rn", $result['fcst_sale_weeks'])
			->setCellValue("K$rn", $result['fcst_ros'])
			->setCellValue("L$rn", "=iferror((K$rn-E$rn)/E$rn*100, 0)")
			->setCellValue("M$rn", "=(H$rn*L$rn)/100+H$rn")
			->setCellValue("N$rn", "=M$rn");

			// Decimal places
			$objPHPExcel->getActiveSheet()->getStyle("L$rn:N$rn")->getNumberFormat()->setFormatCode('0');

		// Increament row number by 1
		$rn++;
	} // While loop ends

	// Output format
	header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
	header('Content-Disposition: attachment;filename="ROSReport.xlsx"');
	header('Cache-Control: max-age=0');

	// Output excel
	$objWriter = PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel2007');
	ob_end_clean();
	$objWriter->save('php://output'); 
	exit();
}
/*
|-----------------------------------
|      MASTER FBA REPORT
|-----------------------------------
*/
if($_GET['reportid'] == 'masterfbarpt'){
	// Set excel file properties
	$objPHPExcel->getProperties()
		->setCreator("Muhammad Tarique")
		->setLastModifiedBy("Muhammad Tarique")
		->setTitle("Master FBA Report")
		->setSubject("Master FBA Report")
		->setDescription("Contains Inventory availability, ROS, Weeks of Coverage, Recommended Order Qty etc.")
		->setCategory("Inventory Fulfilment Reports");

	// Get active working sheet
	$objPHPExcel->getActiveSheetIndex(0);

	// Set auto filter
	$objPHPExcel->getActiveSheet()->setAutoFilter("A2:AB2");

	// Heading row style
	$objPHPExcel->getActiveSheet()->getStyle("A1:AB2")
		->getAlignment()
		->applyFromArray(
			array(
				'horizontal' => PHPExcel_Style_Alignment::HORIZONTAL_CENTER,
				'vertical'   => PHPExcel_Style_Alignment::VERTICAL_CENTER,
				'rotation'   => 0,
				'wrap'       => true
			)
		);

	// Set bold fonts for header
	$objPHPExcel->getActiveSheet()->getStyle("A1:AB2")->getFont()->setBold(true);

	// Set color for header cells
	$objPHPExcel->getActiveSheet()
	    ->getStyle("A1:AB2")
	    ->getFill()
	    ->setFillType(PHPExcel_Style_Fill::FILL_SOLID)
	    ->getStartColor()
	    ->setARGB('B1B3B1');
	/*$objPHPExcel->getActiveSheet()
	    ->getStyle("D1:F1")
	    ->getFill()
	    ->setFillType(PHPExcel_Style_Fill::FILL_SOLID)
	    ->getStartColor()
	    ->setARGB('FF0080');
	$objPHPExcel->getActiveSheet()
	    ->getStyle("G1:I1")
	    ->getFill()
	    ->setFillType(PHPExcel_Style_Fill::FILL_SOLID)
	    ->getStartColor()
	    ->setARGB('4EDA50');
	$objPHPExcel->getActiveSheet()
	    ->getStyle("J1:L1")
	    ->getFill()
	    ->setFillType(PHPExcel_Style_Fill::FILL_SOLID)
	    ->getStartColor()
	    ->setARGB('B1B3B1');
	$objPHPExcel->getActiveSheet()
	    ->getStyle("U1:W1")
	    ->getFill()
	    ->setFillType(PHPExcel_Style_Fill::FILL_SOLID)
	    ->getStartColor()
	    ->setARGB('FACC2E');
	$objPHPExcel->getActiveSheet()
	    ->getStyle("X1:Y1")
	    ->getFill()
	    ->setFillType(PHPExcel_Style_Fill::FILL_SOLID)
	    ->getStartColor()
	    ->setARGB('9FF781');*/

	// Set auto columns width for one column
	$objPHPExcel->getActiveSheet()->getColumnDimension('B')->setAutoSize(true);

	// Show/Hide columns
	$objPHPExcel->getActiveSheet()->getRowDimension('1')->setVisible(false);
	
	// Freeze row and columns
	$objPHPExcel->getActiveSheet()->freezePane("D3");

	// Define name for active sheet and header row cells
	$objPHPExcel->getActiveSheet()
		->setTitle("Master FBA Report")
		->setCellValue("A1", "Item Number")
		->setCellValue("B1", "Description")
		->setCellValue("C1", "Retail Price")
		->setCellValue("D1", "On Order")
		->setCellValue("E1", "On Water")
		->setCellValue("F1", "On Hand")
		->setCellValue("G1", "FBA Working Qty")
		->setCellValue("H1", "FBA Total Qty")
		->setCellValue("I1", "Current Sales Qty")
		->setCellValue("J1", "Default ROS")
		->setCellValue("K1", "Current ROS")
		->setCellValue("L1", "Current Sale Weeks")
		->setCellValue("M1", "WOC FBA")
		->setCellValue("N1", "WOC NJ")
		->setCellValue("O1", "WOC WATER")
		->setCellValue("P1", "WOC USA")
		->setCellValue("Q1", "WOC ORDER")
		->setCellValue("R1", "Total WOC")
		->setCellValue("S1", "Recommended")
		->setCellValue("T1", "Actual Order")
		->setCellValue("U1", "Actual Order of Weeks")
		->setCellValue("V1", "Item Status")
		->setCellValue("W1", "Vendor")
		->setCellValue("X1", "DIVISOR > 6")
		->setCellValue("Y1", "WKS < 27");

	// Define name for active sheet and header row cells
	$objPHPExcel->getActiveSheet()
		->setTitle("Master FBA Report")
		->setCellValue("A2", "ITEM")
		->setCellValue("B2", "DESCRIPTION")
		->setCellValue("C2", "Retail")
		->setCellValue("D2", "ON ORDER")
		->setCellValue("E2", "ON Water")
		->setCellValue("F2", "ON HAND")
		->setCellValue("G2", "WORKING")
		->setCellValue("H2", "FBA")
		->setCellValue("I2", "RESERVED FC TRANS & PROS")
		->setCellValue("J2", "TTL QTY")
		->setCellValue("K2", "OMS ROS")
		->setCellValue("L2", "DEF ROS")
		->setCellValue("M2", "ROS")
		->setCellValue("N2", "NBR WKS")
		->setCellValue("O2", "WKS FBA")
		->setCellValue("P2", "WKS NJ")
		->setCellValue("Q2", "WKS WATER")
		->setCellValue("R2", "WKS USA")
		->setCellValue("S2", "WKS ON ORDER")
		->setCellValue("T2", "TTL WKS")
		->setCellValue("U2", "RECOMMENDED")
		->setCellValue("V2", "Actual Order")
		->setCellValue("W2", "Total Wks after Order")
		->setCellValue("X2", "ACTUAL ORDER OF WKS")
		->setCellValue("Y2", "DC")
		->setCellValue("Z2", "VENDOR")
		->setCellValue("AA2", "DIVISOR > 6")
		->setCellValue("AB2", "WKS < 27");

	// Adding comment
	$objPHPExcel->getActiveSheet()->getComment('D1')->getText()->createTextRun("Defination: \r\n")->getFont()->setBold(true);
	$objPHPExcel->getActiveSheet()->getComment('D1')->getText()->createTextRun('On Order Qty = VP On Order Qty - On Water Qty');
	$objPHPExcel->getActiveSheet()->getComment('H1')->getText()->createTextRun("Defination: \r\n")->getFont()->setBold(true);
	$objPHPExcel->getActiveSheet()->getComment('H1')->getText()->createTextRun('FBA Total Qty = afn-warehouse-qty + inbound-receiving-qty');

	// Query to get ros and other details for items that has sales in last 13 weeks
	/*
	$query = $conn->query("SELECT amazon_ros.*, invt_warehouse.*, invt_config.*, amazon_fba_invt.*, imp_shp_qty.on_water_qty   
		FROM amazon_ros 
		LEFT JOIN invt_warehouse 
		ON amazon_ros.item_no = invt_warehouse.item_no 
		LEFT JOIN invt_config 
		ON amazon_ros.item_no = invt_config.item_no 
		LEFT JOIN amazon_fba_invt 
		ON amazon_ros.item_no = amazon_fba_invt.item_no 
		LEFT JOIN 
			(SELECT imp_shp_lines.item_no, SUM(imp_shp_lines.shp_qty) 
			AS on_water_qty, imp_shp_header.imp_shp_status  
			FROM imp_shp_lines 
			LEFT JOIN imp_shp_header 
			ON imp_shp_lines.imp_shp_id = imp_shp_header.imp_shp_id 
			WHERE imp_shp_header.imp_shp_status = 'Active' 
			OR imp_shp_header.imp_shp_status = 'Draft' 
			GROUP BY imp_shp_lines.item_no)
		AS imp_shp_qty
		ON amazon_ros.item_no = imp_shp_qty.item_no") 
	or die("An error occurred(000b4u): ".$conn->error);
	*/

	// Query to get ros and other details for all amazon_fba_invt items
	$query = $conn->query("SELECT amazon_fba_invt.*, invt_warehouse.description, IFNULL(invt_warehouse.on_order_qty, '0') AS on_order_qty, IFNULL(invt_warehouse.on_hand_qty, '0') AS on_hand_qty, invt_warehouse.supplier, IFNULL(invt_config.amz_def_ros, '0') AS amz_def_ros, invt_config.item_status, IFNULL(amazon_ros.curr_sales_qty, '0') AS curr_sales_qty, IFNULL(amazon_ros.curr_sale_weeks, '0') AS curr_sale_weeks, IFNULL(amazon_ros.curr_ros, '0') AS curr_ros, IFNULL(amazon_ros.hist_ros, '0') AS hist_ros, 
		IFNULL(amazon_ros.fcst_ros, '0') AS fcst_ros, 
		IFNULL(amazon_reserved_invt.reserved_fc_transfers, '0') AS rsvd_fctr,
		IFNULL(amazon_reserved_invt.reserved_fc_processing, '0') AS rsvd_fcpr,
		imp_shp_qty.on_water_qty   
		FROM amazon_fba_invt 
		LEFT JOIN invt_warehouse 
		ON amazon_fba_invt.item_no = invt_warehouse.item_no 
		LEFT JOIN invt_config 
		ON amazon_fba_invt.item_no = invt_config.item_no 
		LEFT JOIN amazon_ros 
		ON amazon_fba_invt.item_no = amazon_ros.item_no 
		LEFT JOIN amazon_reserved_invt 
		ON amazon_fba_invt.item_no = amazon_reserved_invt.item_no 
		LEFT JOIN 
			(SELECT imp_shp_lines.item_no, SUM(imp_shp_lines.shp_qty) 
			AS on_water_qty, imp_shp_header.imp_shp_status  
			FROM imp_shp_lines 
			LEFT JOIN imp_shp_header 
			ON imp_shp_lines.imp_shp_id = imp_shp_header.imp_shp_id 
			WHERE imp_shp_header.imp_shp_status = 'Active' 
			OR imp_shp_header.imp_shp_status = 'Draft' 
			GROUP BY imp_shp_lines.item_no)
		AS imp_shp_qty
		ON amazon_fba_invt.item_no = imp_shp_qty.item_no
		WHERE amazon_fba_invt.item_no NOT LIKE 'CA%'") 
	or die("An error occurred(000b4u): ".$conn->error);

	// Start row count from 2nd row
	$rn = 3;

	// Loop through the query results
	while($result = $query->fetch_array()){

		// Get zero for blank result for on_water_qty
		if($result['on_water_qty'] != ''){
			$onWaterQty = $result['on_water_qty'];
		}
		else{
			$onWaterQty = 0;	
		}

		/* NOT IN USE */
		// Get zero for blank result for default ROS
		/*if($result['amz_def_ros'] != ''){
			$amzDefROS = $result['amz_def_ros'];
		}
		else{
			$amzDefROS = 0;	
		}*/

		// Calculate projected ROS as default ROS
		if($result['hist_ros'] != 0){
			$pct_change = ($result['fcst_ros']-$result['hist_ros'])/$result['hist_ros']*100;
			$proj_ros = (($result['curr_ros']*$pct_change)/100)+$result['curr_ros'];
		}
		else{
			$proj_ros = 0;
		}

		// Print details in excel sheet
		$objPHPExcel->getActiveSheet()
			->setCellValue("A$rn", $result['item_no'])
			->setCellValue("B$rn", $result['description'])
			->setCellValue("C$rn", $result['item_price'])
			->setCellValue("D$rn", $result['on_order_qty']-$onWaterQty)
			->setCellValue("E$rn", $onWaterQty)
			->setCellValue("F$rn", $result['on_hand_qty'])
			//->setCellValue("G$rn", $result['afn_inbound_working_qty']+$result['afn_inbound_shipped_qty'])
			// New working quantity as per RInku calculation email dt 11/22/2018
			->setCellValue("G$rn", $result['afn_inbound_working_qty']+$result['afn_inbound_shipped_qty']+$result['afn_inbound_receiving_qty'])
			//->setCellValue("H$rn", $result['afn_warehouse_qty'])
			// FBA quantity changed to fba fulfillable quantity as per RInku calculation email dt 11/22/2018
			->setCellValue("H$rn", $result['afn_fulfillable_qty'])
			//->setCellValue("I$rn", $result['afn_reserved_qty'])
			// From reservered inventory report as per RInku calculation email dt 11/22/2018
			->setCellValue("I$rn", $result['rsvd_fctr']+$result['rsvd_fcpr'])
			->setCellValue("J$rn", $result['curr_sales_qty'])
			->setCellValue("K$rn", $proj_ros)
			->setCellValue("L$rn", $result['amz_def_ros'])
			->setCellValue("M$rn", $result['curr_ros'])
			->setCellValue("N$rn", $result['curr_sale_weeks'])
			->setCellValue("O$rn", "=iferror((H$rn+I$rn)/L$rn, 0)")
			->setCellValue("P$rn", "=iferror(F$rn/L$rn, 0)")
			->setCellValue("Q$rn", "=iferror(E$rn/L$rn, 0)")
			->setCellValue("R$rn", "=O$rn+P$rn+Q$rn")
			->setCellValue("S$rn", "=iferror(D$rn/L$rn, 0)")
			->setCellValue("T$rn", "=R$rn+S$rn")
			->setCellValue("U$rn", "=if(Y$rn=\"DISCONTINUED\", 0, if((AA$rn+AB$rn) = 2, (34-T$rn)*L$rn, 0))")
			->setCellValue("V$rn", "=if(X$rn>4, 4*L$rn, U$rn)")
			->setCellValue("W$rn", "=iferror(T$rn+(V$rn/L$rn), 0)")
			->setCellValue("X$rn", "=iferror(U$rn/L$rn, 0)")
			->setCellValue("Y$rn", $result['item_status'])
			->setCellValue("Z$rn", $result['supplier'])
			->setCellValue("AA$rn", "=IF(N$rn>6,1,0)")
			->setCellValue("AB$rn", "=IF(T$rn<27,1,0)");
		
			// Decimal places
			$objPHPExcel->getActiveSheet()->getStyle("K$rn:M$rn")->getNumberFormat()->setFormatCode('0');
			$objPHPExcel->getActiveSheet()->getStyle("O$rn:X$rn")->getNumberFormat()->setFormatCode('0');
			//$objPHPExcel->getActiveSheet()->getStyle("U$rn:V$rn")->getNumberFormat()->setFormatCode('0');
			//$objPHPExcel->getActiveSheet()->getStyle("W$rn")->getNumberFormat()->setFormatCode('0');
			/*$objPHPExcel->getActiveSheet()
					    ->getStyle("G$rn")
					    ->getFill()
					    ->setFillType(PHPExcel_Style_Fill::FILL_SOLID)
					    ->getStartColor()
					    ->setARGB('#8ea9db');
			$objPHPExcel->getActiveSheet()
					    ->getStyle("H$rn")
					    ->getFill()
					    ->setFillType(PHPExcel_Style_Fill::FILL_SOLID)
					    ->getStartColor()
					    ->setARGB('#a9d08e');
			$objPHPExcel->getActiveSheet()
					    ->getStyle("O$rn")
					    ->getFill()
					    ->setFillType(PHPExcel_Style_Fill::FILL_SOLID)
					    ->getStartColor()
					    ->setARGB('#f4b084');
			$objPHPExcel->getActiveSheet()
					    ->getStyle("R$rn")
					    ->getFill()
					    ->setFillType(PHPExcel_Style_Fill::FILL_SOLID)
					    ->getStartColor()
					    ->setARGB('#f4b084');	*/			    			    
			    

		// Increament row number by 1
		$rn++;
	} // While loop ends

	// Output format
	header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
	header('Content-Disposition: attachment;filename="Master FBAREPT '.date('m-d-Y').'.xlsx"');
	header('Cache-Control: max-age=0');

	// Output excel
	$objWriter = PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel2007');
	ob_end_clean();
	$objWriter->save('php://output'); 
	exit();
}

/*
|-----------------------------------
|      RE-ORDER REPORT
|-----------------------------------
*/
if($_GET['reportid'] == 'reorderrpt'){
	// Query to get all requierd deails
	$query = $conn->query("SELECT 
		amazon_ros.item_no, amazon_ros.working_status, 
		IFNULL(amazon_ros.curr_sales_qty, '0') AS curr_sales_qty, 
		IFNULL(amazon_ros.curr_sale_weeks, '0') AS curr_sale_weeks, 
		IFNULL(amazon_ros.curr_ros, '0') AS curr_ros, 
		IFNULL(amazon_ros.hist_ros, '0') AS hist_ros, 
		IFNULL(amazon_ros.fcst_ros, '0') AS fcst_ros, 
		invt_warehouse.description, 
		IFNULL(amazon_fba_invt.item_price, '0') AS item_price,
		IFNULL(amazon_fba_invt.afn_inbound_working_qty, '0') AS afn_inbound_working_qty, 
		IFNULL(amazon_fba_invt.afn_inbound_shipped_qty, '0') AS afn_inbound_shipped_qty, 
		IFNULL(amazon_fba_invt.afn_inbound_receiving_qty, '0') AS afn_inbound_receiving_qty, 
		IFNULL(amazon_fba_invt.afn_fulfillable_qty, '0') AS afn_fulfillable_qty, 
		IFNULL(invt_warehouse.on_order_qty, '0') AS on_order_qty, 
		IFNULL(invt_warehouse.on_hand_qty, '0') AS on_hand_qty, 
		invt_warehouse.supplier, 
		IFNULL(invt_compiled.ctn_dim_ln, '0') AS ctn_ln, 
		IFNULL(invt_compiled.ctn_dim_wd, '0') AS ctn_wd, 
		IFNULL(invt_compiled.ctn_dim_ht, '0') AS ctn_ht, 
		IFNULL(invt_compiled.master_pack, '0') AS mp,
		IFNULL(invt_compiled.moq, '0') AS moq, 
		IFNULL(invt_config.amz_def_ros, '0') AS amz_def_ros, 
		IFNULL(invt_config.lead_time_days, '0') AS lead_time_days,
		invt_config.item_status, invt_config.warehouse, 
		IFNULL(amazon_reserved_invt.reserved_fc_transfers, '0') AS rsvd_fctr,
		IFNULL(amazon_reserved_invt.reserved_fc_processing, '0') AS rsvd_fcpr,
		imp_shp_qty.on_water_qty   
		FROM amazon_ros 
		LEFT JOIN invt_warehouse 
		ON amazon_ros.item_no = invt_warehouse.item_no 
		LEFT JOIN invt_compiled 
		ON amazon_ros.item_no = invt_compiled.item_no
		LEFT JOIN invt_config 
		ON amazon_ros.item_no = invt_config.item_no 
		LEFT JOIN amazon_fba_invt 
		ON amazon_ros.item_no = amazon_fba_invt.item_no 
		LEFT JOIN amazon_reserved_invt 
		ON amazon_ros.item_no = amazon_reserved_invt.item_no
		LEFT JOIN 
			(SELECT imp_shp_lines.item_no, 
			SUM(imp_shp_lines.shp_qty) AS on_water_qty, 
			imp_shp_header.imp_shp_status   
			FROM imp_shp_lines 
			LEFT JOIN imp_shp_header 
			ON imp_shp_lines.imp_shp_id = imp_shp_header.imp_shp_id 
			WHERE imp_shp_header.imp_shp_status = 'Active' 
			OR imp_shp_header.imp_shp_status = 'Draft' 
			GROUP BY imp_shp_lines.item_no)
		AS imp_shp_qty
		ON amazon_ros.item_no = imp_shp_qty.item_no 
		WHERE amazon_ros.item_no NOT LIKE 'NONSTOCK%'") 
	or die("An error occurred(000b4u): ".$conn->error);

	// Declare an empty array for storing query results
	$results = array();

	// Loop through query rows and store value to results array
	while($rows = $query->fetch_array()) $results[] = $rows; 

	/**
	 * [write_sheet - Write multiple sheets for working and not working item]
	 * @param  [object] $objPHPExcel   [description]
	 * @param  [string] $workingstatus [description]
	 * @param  [array] $results       [description]
	 * @return [type]                [description]
	 */
	function write_sheet($objPHPExcel, $workingstatus, $sheetid, $results){
		// Set auto filter
		$objPHPExcel->getActiveSheet()->setAutoFilter("A1:AJ1");

		// Set allignment and rotations for header row
		$objPHPExcel->getActiveSheet()->getStyle("A1:AJ1")
			->getAlignment()
			->applyFromArray(
				array(
					'horizontal' => PHPExcel_Style_Alignment::HORIZONTAL_CENTER,
					'vertical'	 => PHPExcel_Style_Alignment::VERTICAL_CENTER,
					'rotation'	 => 0,
					'wrap'		 => true
				)
			);

		// Set font weight for header row
		$objPHPExcel->getActiveSheet()->getStyle("A1:AJ1")->getFont()->setBold(true);

		// Create cells array to store cells color
		$cell_colors['A1:I1'] 	= '76BED0';
		$cell_colors['J1:L1'] 	= 'CDCDCD';
		$cell_colors['M1:P1'] 	= 'F7CB15';
		$cell_colors['Q1:V1'] 	= 'F55D3E';
		$cell_colors['W1:AB1'] 	= 'DDD9C4';
		$cell_colors['AC1:AE1'] = '76BED0';
		$cell_colors['AF1:AG1'] = 'F694C1';
		$cell_colors['AH1:AI1'] = '46E121';
		$cell_colors['AJ1'] 	= 'FCD5B4';

		// Loop through cells color array
		foreach($cell_colors as $key=>$value){
			// Set background color for heading row
			$objPHPExcel->getActiveSheet()
				->getStyle($key)
				->getFill()
				->setFillType(PHPExcel_Style_Fill::FILL_SOLID)
				->getStartColor()
				->setARGB($value);
		}

		// Set description column width to auto
		$objPHPExcel->getActiveSheet()->getColumnDimension('B')->setAutoSize(true);

		// Show/Hide rows and columns
		$objPHPExcel->getActiveSheet()->getColumnDimension('F')->setVisible(false);
		$objPHPExcel->getActiveSheet()->getColumnDimension('G')->setVisible(false);
		$objPHPExcel->getActiveSheet()->getColumnDimension('H')->setVisible(false);
		/*$objPHPExcel->getActiveSheet()->getColumnDimension('I')->setVisible(false);*/
		//$objPHPExcel->getActiveSheet()->getColumnDimension('AC')->setVisible(false);
		$objPHPExcel->getActiveSheet()->getColumnDimension('AH')->setVisible(false);
		$objPHPExcel->getActiveSheet()->getColumnDimension('AI')->setVisible(false);

		// Freeze row and column
		$objPHPExcel->getActiveSheet()->freezePane("D2");

		// Set name of column
		$objPHPExcel->getActiveSheet()
			->setTitle($workingstatus)
			// Columns for product information
			->setCellValue("A1", "Item Number")
			->setCellValue("B1", "Description")
			->setCellValue("C1", "FBA Retail Price")
			->setCellValue("D1", "Item Status")
			->setCellValue("E1", "Vendor")
			->setCellValue("F1", "Carton Length (Inches)")
			->setCellValue("G1", "Carton Width (Inches)")
			->setCellValue("H1", "Carton Height (Inches)")
			->setCellValue("I1", "Warehouse")
			->setCellValue("J1", "Lead Time (WKS)")
			->setCellValue("K1", "Transit TIme (WKS)")
			->setCellValue("L1", "Replenishment Time (WKS)")
			// Column for Rate of Sale
			->setCellValue("M1", "Current Sales QTY")
			->setCellValue("N1", "Current Sales WKS")
			->setCellValue("O1", "Current ROS")
			->setCellValue("P1", "Default ROS")
			// Columns for Inventory
			->setCellValue("Q1", "ON ORDER QTY")
			->setCellValue("R1", "ON WATER QTY")
			->setCellValue("S1", "ON HAND QTY")
			->setCellValue("T1", "FBA WORKING QTY")
			->setCellValue("U1", "FBA Fulfillable QTY")
			->setCellValue("V1", "FBA Reserved FC Trans & Pros")
			// Weeks of coverage at different stages
			->setCellValue("W1", "ON ORDER WKS")
			->setCellValue("X1", "ON WATER WKS")
			->setCellValue("Y1", "ON HAND WKS")
			->setCellValue("Z1", "FBA WKS")
			->setCellValue("AA1", "USA WKS")
			->setCellValue("AB1", "TOTAL WKS")
			// MP & MOQ
			->setCellValue("AC1", "MP")
			->setCellValue("AD1", "MOQ")
			->setCellValue("AE1", "MOQ WKS")
			// Column for orders related data
			->setCellValue("AF1", "Recommended Order WKS")
			->setCellValue("AG1", "Recommended Order QTY")
			->setCellValue("AH1", "Actual Order WKS")
			->setCellValue("AI1", "Actual Order QTY")
			->setCellValue("AJ1", "TOTAL WKS AFTER ORDER")
			// Constant values
			->setCellValue("BA1", "Minimum Stock Wks")
			->setCellValue("BB1", "8")
			->setCellValue("BC1", "Maximim Stock Wks")
			->setCellValue("BD1", "13")
			->setCellValue("BE1", "Order Processing Weeks")
			->setCellValue("BF1", "1");

		// Add comments
		$objPHPExcel->getActiveSheet()->getComment('D1')->getText()->createTextRun("AC = ACTIVE \r\n DC = DISCONTINUED");
		$objPHPExcel->getActiveSheet()->getComment('I1')->getText()->createTextRun("Master Pack");
		$objPHPExcel->getActiveSheet()->getComment('J1')->getText()->createTextRun("Minimum Order Quantity");
		$objPHPExcel->getActiveSheet()->getComment('O1')->getText()->createTextRun("Last 13 weeks sales quantity");
		$objPHPExcel->getActiveSheet()->getComment('P1')->getText()->createTextRun("Number of weeks in which sales took place in last 13 weeks");
		$objPHPExcel->getActiveSheet()->getComment('Q1')->getText()->createTextRun("Rate of sale based on last 13 weeks sales");
		$objPHPExcel->getActiveSheet()->getComment('R1')->getText()->createTextRun("Custom defined rate of sale");

		// Define named range
		$objPHPExcel->addNamedRange(new PHPExcel_NamedRange('MIN_STK_WKS_'.$sheetid, $objPHPExcel->getActiveSheet(), 'BB1'));
		$objPHPExcel->addNamedRange(new PHPExcel_NamedRange('MAX_STK_WKS_'.$sheetid, $objPHPExcel->getActiveSheet(), 'BD1'));
		$objPHPExcel->addNamedRange(new PHPExcel_NamedRange('ORD_PRO_WKS_'.$sheetid, $objPHPExcel->getActiveSheet(), 'BF1'));
		$objPHPExcel->addNamedRange(new PHPExcel_NamedRange('REPLN_TIME_'.$sheetid, $objPHPExcel->getActiveSheet(), 'L:L'));
		$objPHPExcel->addNamedRange(new PHPExcel_NamedRange('DEF_ROS_'.$sheetid, $objPHPExcel->getActiveSheet(), 'P:P'));
		$objPHPExcel->addNamedRange(new PHPExcel_NamedRange('MP_'.$sheetid, $objPHPExcel->getActiveSheet(), 'AC:AC'));
		$objPHPExcel->addNamedRange(new PHPExcel_NamedRange('MOQ_'.$sheetid, $objPHPExcel->getActiveSheet(), 'AD:AD'));
		$objPHPExcel->addNamedRange(new PHPExcel_NamedRange('ITEM_STATUS_'.$sheetid, $objPHPExcel->getActiveSheet(), 'D:D'));
		$objPHPExcel->addNamedRange(new PHPExcel_NamedRange('WH_NAME_'.$sheetid, $objPHPExcel->getActiveSheet(), 'I:I'));

		// Start row counter
		$rn = 2;	

		// Loop through query results
		foreach($results as $result){
			// Set on order qty to zero for blank query response
			if($result['on_water_qty'] != '') $onWaterQty = $result['on_water_qty'];
			else $onWaterQty = 0;

			// Insert data only if parameter matches with working status 
			if($result['working_status'] == $workingstatus){
				// Print detail data in excel sheet
				$objPHPExcel->getActiveSheet()
					// Data related to products information 
					->setCellValue("A$rn", $result['item_no'])
					->setCellValue("B$rn", $result['description'])
					->setCellValue("C$rn", $result['item_price'])
					->setCellValue("D$rn", $result['item_status'])
					->setCellValue("E$rn", $result['supplier'])
					->setCellValue("F$rn", $result['ctn_ln'])
					->setCellValue("G$rn", $result['ctn_wd'])
					->setCellValue("H$rn", $result['ctn_ht'])
					->setCellValue("I$rn", $result['warehouse'])
					->setCellValue("J$rn", "=ROUND(".$result['lead_time_days']."/7, 0)")
					// 3PL 9 WK | ORG NJ 11 WK | DEF 8 WK
					->setCellValue("K$rn", "=if(WH_NAME_".$sheetid."=\"3PL\", 9, if(WH_NAME_".$sheetid."=\"ORG NJ\", 11, 8))")
					->setCellValue("L$rn", "=J$rn+K$rn")
					// Data related to ROS
					->setCellValue("M$rn", $result['curr_sales_qty'])
					->setCellValue("N$rn", $result['curr_sale_weeks'])
					->setCellValue("O$rn", $result['curr_ros'])
					->setCellValue("P$rn", $result['amz_def_ros'])
					// Data related to inventory
					->setCellValue("Q$rn", $result['on_order_qty']-$onWaterQty)
					->setCellValue("R$rn", $onWaterQty)
					->setCellValue("S$rn", $result['on_hand_qty'])
					->setCellValue("T$rn", $result['afn_inbound_working_qty']+$result['afn_inbound_shipped_qty']+$result['afn_inbound_receiving_qty'])
					->setCellValue("U$rn", $result['afn_fulfillable_qty'])
					->setCellValue("V$rn", $result['rsvd_fctr']+$result['rsvd_fcpr'])
					// Data related to weeks of coverage
					->setCellValue("W$rn", "=iferror(Q$rn/DEF_ROS_".$sheetid.", 0)")
					->setCellValue("X$rn", "=iferror(R$rn/DEF_ROS_".$sheetid.", 0)")
					->setCellValue("Y$rn", "=iferror(S$rn/DEF_ROS_".$sheetid.", 0)")
					->setCellValue("Z$rn", "=iferror((U$rn+V$rn)/DEF_ROS_".$sheetid.", 0)")
					->setCellValue("AA$rn", "=iferror(sum(X$rn:Z$rn), 0)")
					->setCellValue("AB$rn", "=iferror(W$rn+AA$rn, 0)")
					// MP & MOQ
					->setCellValue("AC$rn", $result['mp'])
					->setCellValue("AD$rn", $result['moq'])
					->setCellValue("AE$rn", "=iferror(MOQ_".$sheetid."/DEF_ROS_".$sheetid.", 0)")
					// Data related to orders
					->setCellValue("AF$rn", "=IF(ITEM_STATUS_".$sheetid."=\"DC\", 0, IF(AB$rn<(REPLN_TIME_".$sheetid."+MIN_STK_WKS_".$sheetid."+ORD_PRO_WKS_".$sheetid."), ((REPLN_TIME_".$sheetid."+MAX_STK_WKS_".$sheetid.")-AB$rn), 0))")
					->setCellValue("AG$rn", "=AE$rn*DEF_ROS_".$sheetid)
					->setCellValue("AG$rn", "=iferror((AF$rn*DEF_ROS_".$sheetid.")-MOD(AF$rn*DEF_ROS_".$sheetid.", MP_".$sheetid."), 0)")
					->setCellValue("AH$rn", "=iferror(round(AI$rn/DEF_ROS_".$sheetid.",0),AI$rn)")
					->setCellValue("AI$rn", "=IF(MOQ_".$sheetid."=0, \"MOQ MISSING\", IF(AG$rn>=MOQ_".$sheetid.",IF(MP_".$sheetid."=0,\"MP MISSING\", AG$rn-MOD(AG$rn,MP_".$sheetid.")),\"ORDER QTY IS LESS THAN MOQ\"))")
					->setCellValue("AJ$rn", "=IFERROR(ROUND(AB$rn+(AG$rn/DEF_ROS_".$sheetid."), 0),0)");

				// Format cells to accept decimal places
				$objPHPExcel->getActiveSheet()->getStyle("W$rn:AG$rn")->getNumberFormat()->setFormatCode('0');
				$objPHPExcel->getActiveSheet()->getStyle("C$rn")->getNumberFormat()->setFormatCode('_("$"* #,##0.00_);_("$"* \(#,##0.00\);_("$"* "-"??_);_(@_)');

				// Increae the row counter by 1
				$rn++;
			}
		}
	} // End write_sheet();

	/*
	|------------------------------------------------
	|	EXCEL NEW WORKBOOK INTIALIZATION
	|------------------------------------------------
	 */
	$objPHPExcel->getProperties()
		->setCreator("MUHAMMAD TARIQUE")
		->setLastModifiedBy("MUHAMMAD TARIQUE")
		->setTitle("RE-ORDER REPORT_V01")
		->setSubject("INVENTORY FULFILLMENT REPORT")
		->setDescription("Contains recommended reorder report to place orders based on out of stock algorithm flags.")
		->setCategory("OMS REPORTS");

	/*
	|---------------------------------------------------
	|	WORKSHEET-1: RE-ORDER REPORT NOT WORKING ITEMS
	|---------------------------------------------------
	*/
	// Set active sheet
	$objPHPExcel->getActiveSheetIndex(0);
	// Write data
	write_sheet($objPHPExcel, "PENDING", "P", $results);
	/*
	|---------------------------------------------------
	|	WORKSHEET-2: RE-ORDER REPORT WORKING ITEMS
	|---------------------------------------------------
	 */
	// Create a new sheet for data datafinaltions
	$worksheet_2 = new PHPExcel_Worksheet($objPHPExcel);
	$objPHPExcel->addSheet($worksheet_2);
	// Set this new sheet as active
	$objPHPExcel->setActiveSheetIndex(1);
	// Write data
	write_sheet($objPHPExcel, "WORKING", "W", $results);

	// Set back first sheet as active
	$objPHPExcel->setActiveSheetIndex(0);
	
	/*
	|----------------------------------
	|	OUTPUT EXCEL FILE
	|----------------------------------
	 */
	// Configure output format for excel file
	$file_name = "Re-Order_Report_".date('m-d-Y').".xlsx";
	header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
	header('Content-Disposition: attachment; filename="'.$file_name.'"');
	header('Cache-Control: max-age=0');

	// Output excel
	$objWriter = PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel2007');
	ob_end_clean();
	ob_start();
	$objWriter->save('php://output');

	// Save excel content to a string
	$excel_data = ob_get_contents();

	// Send email if there is data 
	if(!empty($excel_data)){
		$mail = new PHPMailer;
		$mail->setFrom('omsadmin@orientoriginals.net', 'OMS-Admin');
		$mail->addAddress('rinku@rituraj.com');
		//$mail->addAddress('kelli@orientoriginals.com');
		$mail->addCustomHeader("CC: rb@orientoriginals.com, jyoti@rituraj.com, tarique@rituraj.com");
		$mail->Subject = 'Re-order report - '.date('m-d-Y');
		$mail->isHTML(true);
		$mail->Body  = 'Hello,';
		$mail->Body .= '<p>Please review the attached recommended re-order report.</p>';
		$mail->Body .= '
			<h4>Best Regards,<br>
            OMS Admin,<br>
            Orient Originals, Inc.</h4>';
	    $mail->Body .= '<p><em>This is an automatically system generated email, please do not reply to it.</em></p>';
	    $mail->addStringAttachment($excel_data, $file_name);
	    $mail->send();
	}
	exit();
}
/*
|-----------------------------------
|     GET WORKAROUND SKU LIST
|-----------------------------------
*/
if($_GET['reportid'] == 'fbaworkingskulist'){
	/*
	|---------------------------------------------
	|	FBA WORKING SKU'S 
	|---------------------------------------------
	|   Only those SKU's which have inventory 
	|   in any stage and should not be taken for 
	| 	work.
	|---------------------------------------------
	 */
	$query = $conn->query("SELECT invt_warehouse.item_no, 
		invt_warehouse.description,
		IFNULL(IFNULL(invt_warehouse.on_order_qty, '0')-IFNULL(imp_shp.on_water_qty, '0'), '0') AS on_order_qty, 
		IFNULL(imp_shp.on_water_qty, '0') AS on_water_qty, 
		IFNULL(invt_warehouse.on_hand_qty, '0') AS on_hand_qty, 
		IFNULL(amazon_fba_invt.afn_inbound_working_qty, '0') AS fba_in_wrk_qty,
		IFNULL(amazon_fba_invt.afn_inbound_shipped_qty, '0') AS fba_in_shp_qty, 
		IFNULL(amazon_fba_invt.afn_inbound_receiving_qty, '0') AS fba_in_rec_qty, 
		IFNULL(amazon_fba_invt.afn_fulfillable_qty, '0') AS fba_ful_qty, 
		IFNULL(amazon_reserved_invt.reserved_fc_transfers, '0') AS rsvd_fctr_qty,
		IFNULL(amazon_reserved_invt.reserved_fc_processing, '0') AS rsvd_fcpr_qty, 
		IFNULL(invt_config.work_exp_date, '0000-00-00') AS work_exp_date 
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

	// Get fields
	$headers = $query->fetch_fields();

	// Loop through headers
	foreach($headers as $header){
		$head[] = $header->name;
	}

	// Open file to write
	$fp = fopen('php://output', 'w');

	// Check for successfull query and file opening 
	if($fp && $query){
	 	header('Content-Type: text/csv');
		header('Content-Disposition: attachment; filename="active_sku_list_'.date('Y-m-d').'.csv"');
		header('Pragma: no-cache');
		header('Expires: 0');
		fputcsv($fp, array_values($head));

		// Loop through query resulta and write data into CSV
		while($result = $query->fetch_array(MYSQLI_NUM)){
			// Check item of current iteration should not be in working
			if(date('Y-m-d', strtotime($result[11])) < date('Y-m-d')){
				fputcsv($fp, array_values($result));
			}
		}
	}
}