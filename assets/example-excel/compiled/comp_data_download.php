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

if(isset($_GET['compdatalnks'])){
	if($_GET['reprttype'] == 'CompiledData'){
		// Download filename
		$fileName = "CompiledData".date('Ymd');

		// Output header
		header("Content-Type: application/vnd.ms-excel");
		header("Content-Disposition: attachment; filename=\"$fileName.xls\"");

		// Write excel
		echo '
			<html>
			<meta http-equiv=\'Content-Type\' content=\'text/html\' charset=\'windows-1252\'>
			<style>
				table tr th{
					text-align: left;
					border: .5pt solid black;
					background-color: #eee;
				}
				table tr td{
					text-align: left;
					border: .5pt solid black;
				}
				.xl-head{
					border: none !important;
					background-color: #f9e79f !important;
				}
			</style>
			<body>
			<!-- Report Header -->
			<table>
				<tr><th class="xl-head">Report Name: </th><td class="xl-head">Compiled Data</td></tr>
				<tr><th class="xl-head">Downloaded Data: </th><td class="xl-head">'.date('Y-m-d').'</td></tr>
			</table>
			<!-- Report -->
			<table>
				<thead>
					<tr>
						<th>Item Number</th>
						<th>Description</th>
						<th>Material Type</th>
						<th>Item Size</th>
						<th>Item Color</th>
						<th>MOQ</th>
						<th>Item Net Weight (Grams)</th>
						<th>Item Gross Weight (Grams)</th>
						<th>GSM</th>
						<th>GSF</th>
						<th>Longest Side</th>
						<th>Median Side</th>
						<th>Shortest Side</th>
						<th>IP</th>
						<th>MP</th>
						<th>Carton Length (Inches)</th>
						<th>Carton Width (Inches)</th>
						<th>Carton Height (Inches)</th>
						<th>Carton Net Weight (Kgs)</th>
						<th>Carton Gross Weight (Kgs)</th>
						<th>Packing Accessories</th>
						<th>Packing Remarks</th>
						<th>Vendor/Supplier</th>
						<th>Remarks</th>
					</tr>
				</thead>
				<tbody>
		';
		
		// Query to get compiled data
		$query = $conn->query("SELECT invt_compiled.*, invt_warehouse.description 
			FROM invt_compiled 
			LEFT JOIN invt_warehouse ON invt_compiled.item_no = invt_warehouse.item_no
			ORDER BY item_no ASC") 
		or die("An error occurred: ".$conn->error);

		// Loop through the result set
		while($result = $query->fetch_array()){
			echo '
				<tr>
				 	<td>'.sprintf('%05d', $result['item_no']).'</td>
				 	<td>'.$result['description'].'</td>
				 	<td>'.$result['material_type'].'</td>
				 	<td>'.$result['size'].'</td>
				 	<td>'.$result['color'].'</td>
				 	<td>'.$result['moq'].'</td>
				 	<td>'.$result['item_net_wt'].'</td>
				 	<td>'.$result['item_grs_wt'].'</td>
				 	<td>'.$result['gsm'].'</td>
				 	<td>'.$result['gsf'].'</td>
				 	<td>'.$result['longest_side'].'</td>
				 	<td>'.$result['median_side'].'</td>
				 	<td>'.$result['shortest_side'].'</td>
				 	<td>'.$result['internal_pack'].'</td>
				 	<td>'.$result['master_pack'].'</td>
				 	<td>'.$result['ctn_dim_ln'].'</td>
				 	<td>'.$result['ctn_dim_wd'].'</td>
				 	<td>'.$result['ctn_dim_ht'].'</td>
				 	<td>'.$result['net_wt_ctn'].'</td>
				 	<td>'.$result['grs_wt_ctn'].'</td>
				 	<td>'.$result['packing_accessories'].'</td>
				 	<td>'.$result['packing_remarks'].'</td>
				 	<td>'.$result['supplier'].'</td>
				 	<td>'.$result['remarks'].'</td>
				 </tr>
			';
		}

		echo '</tbody></table></body></html>';
	}
}

?>
 