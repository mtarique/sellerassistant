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

$tmpl = new PageTempelate(); //new tempelate object
$lnks = new PageLinks();	 //new lnks object

// Set max execution time to infinite
ini_set('max_execution_time', 0);

// Form input variable
$itemNo = mysqli_real_escape_string($conn, $_POST['txtItemSearchKey']);

// Query
$sql = $conn->query("SELECT  invt_warehouse.*, invt_compiled.* 
	FROM invt_warehouse
	LEFT JOIN invt_compiled 
	ON invt_warehouse.item_no = invt_compiled.item_no 
	WHERE invt_warehouse.item_no = '$itemNo'") 
or die("An error occurred: ".$conn->error);

if($sql->num_rows > 0){

$row = $sql->fetch_array();

echo '
<div class="panel panel-default small" style="background:#fafafa; border-radius: 0px;">
	<div class="panel-body">
		<form action="/oms/apps/invt/compiled/comp_data_update.php" method="POST"  class="form-horizontal" id="formUpdCompData">
			<fieldset>
				<legend>Product Details</legend>
				<h5 class="text-primary" style="margin-top: -5px !important; margin-bottom: 15px !important;"><abbr title="Product Description">'.ucwords(strtolower($row['description'])).'</abbr></h5>

				<div class="form-group">
					<input type="text" name="txtItemNo" id="txtItemNo" value="'.$itemNo.'" class="form-control input-xs" style="display: none;">
					<label for="txtMaterialType" class="col-xs-2">MaterialType: </label>
					<div class="col-xs-1">
						<input type="text" name="txtMaterialType" id="txtMaterialType" value="'.$row['material_type'].'" class="form-control input-xs">
					</div>
					<label for="txtSize" class="col-xs-2">Item Size: </label>
					<div class="col-xs-1">
						<input type="text" name="txtSize" id="txtSize" value="'.$row['size'].'" class="form-control input-xs">
					</div>
					<label for="txtColor" class="col-xs-2">Item Color: </label>
					<div class="col-xs-1">
				  		<input type="text" name="txtColor" id="txtColor" value="'.$row['color'].'" class="form-control input-xs">
					</div>
					<label for="txtMOQ" class="col-xs-2">MOQ: </label>
					<div class="col-xs-1">
						<input type="text" name="txtMOQ" id="txtMOQ" value="'.$row['moq'].'" class="form-control input-xs text-right">
					</div>
				</div>
				<div class="form-group">
					<label for="txtItemNetWt" class="col-xs-2">Item Net Weight (Gram): </label>
					<div class="col-xs-1">
						<input type="text" name="txtItemNetWt" id="txtItemNetWt" value="'.$row['item_net_wt'].'" class="form-control input-xs text-right">
					</div>
					<label for="txtItemGrsWt" class="col-xs-2">Item Gross Weight (Gram): </label>
					<div class="col-xs-1">
						<input type="text" name="txtItemGrsWt" id="txtItemGrsWt" value="'.$row['item_grs_wt'].'" class="form-control input-xs text-right">
					</div>
					<label for="txtGSM" class="col-xs-2">GSM: </label>
					<div class="col-xs-1">
						<input type="text" name="txtGSM" id="txtGSM" value="'.$row['gsm'].'" class="form-control input-xs text-right">
					</div>
					<label for="txtGSF" class="col-xs-2">GSF: </label>
					<div class="col-xs-1">
						<input type="text" name="txtGSF" id="txtGSF" value="'.$row['gsf'].'" class="form-control input-xs text-right">
					</div>
				</div>
				<div class="form-group">
					<label for="txtLongestSide" class="col-xs-2">Longest Side (Inches): </label>
					<div class="col-xs-1 text-left">
						<input type="text" name="txtLongestSide" id="txtLongestSide" value="'.$row['longest_side'].'" class="form-control input-xs text-right">
					</div>
					<label for="txtMedianSide" class="col-xs-2">Median Side (Inches): </label>
					<div class="col-xs-1">
						<input type="text" name="txtMedianSide" id="txtMedianSide" value="'.$row['median_side'].'" class="form-control input-xs text-right">
					</div>
					<label for="txtShortestSide" class="col-xs-2">Shortest Side (Inches): </label>
					<div class="col-xs-1">
						<input type="text" name="txtShortestSide" id="txtShortestSide" value="'.$row['shortest_side'].'" class="form-control input-xs text-right">
					</div>
					<label for="txtVendor" class="col-xs-2">Vendor/Supplier: </label>
					<div class="col-xs-1">
						<input type="text" name="txtVendor" id="txtVendor" value="'.$row['supplier'].'" class="form-control input-xs">
					</div>
				</div>
			</fieldset>

			<!-- Packaging Details -->
			<fieldset>
				<legend>Packaging Details</legend>
				<div class="form-group">
					<label for="txtIP" class="col-xs-2">Internal Pack (IP): </label>
					<div class="col-xs-1">
						<input type="text" name="txtIP" id="txtIP" value="'.$row['internal_pack'].'" class="form-control input-xs text-right">
					</div>
					<label for="txtMP" class="col-xs-2">Master Pack (MP): </label>
					<div class="col-xs-1">
						<input type="text" name="txtMP" id="txtMP" value="'.$row['master_pack'].'" class="form-control input-xs text-right">
					</div>
					<label for="txtCtnNetWt" class="col-xs-2">Net Weight of Carton (Kg): </label>
					<div class="col-xs-1">
						<input type="text" name="txtCtnNetWt" id="txtCtnNetWt" value="'.$row['net_wt_ctn'].'" class="form-control input-xs text-right">
					</div>
					<label for="txtCtnGrsWt" class="col-xs-2">Grs Weight of Carton (Kg): </label>
					<div class="col-xs-1">
						<input type="text" name="txtCtnGrsWt" id="txtCtnGrsWt" value="'.$row['grs_wt_ctn'].'" class="form-control input-xs text-right">
					</div>
				</div>
				<div class="form-group">
					<label for="txtCtnLn" class="col-xs-2">Carton Length (Inches): </label>
					<div class="col-xs-1">
						<input type="text" name="txtCtnLn" id="txtCtnLn" value="'.$row['ctn_dim_ln'].'" class="form-control input-xs text-right">
					</div>
					<label for="txtCtnWd" class="col-xs-2">Carton Width (Inches): </label>
					<div class="col-xs-1">
						<input type="text" name="txtCtnWd" id="txtCtnWd" value="'.$row['ctn_dim_wd'].'" class="form-control input-xs text-right">
					</div>
					<label for="txtCtnHt" class="col-xs-2">Carton Height (Inches): </label>
					<div class="col-xs-1">
						<input type="text" name="txtCtnHt" id="txtCtnHt" value="'.$row['ctn_dim_ht'].'" class="form-control input-xs text-right">
					</div>
				</div>
				<div class="form-group">
					<label for="txtPckgAccs" class="col-xs-2">Packing Accessories: </label>
					<div class="col-xs-4">
						<input type="text" name="txtPckgAccs" id="txtPckgAccs" value="'.$row['packing_accessories'].'" class="form-control input-xs">
					</div>
					<label for="txtPckgRemarks" class="col-xs-2">Packing Remarks: </label>
					<div class="col-xs-4">
						<input type="text" name="txtPckgRemarks" id="txtPckgRemarks" value="'.$row['packing_remarks'].'" class="form-control input-xs">
					</div>
				</div>
				<div class="form-group">
					<label for="txtRemarks" class="col-xs-2">Remarks: </label>
					<div class="col-xs-8">
						<input type="text" name="txtRemarks" id="txtRemarks" value="'.$row['remarks'].'" class="form-control input-xs">
					</div>
					<div class="col-xs-2">
						<button type="submit" name="btnUpdCompData" id="btnUpdate" class="btn btn-xs btn-block btn-warning">Update</button>
					</div>
				</div>
				<div class="form-group">
					<div class="col-xs-12 text-right" id="resUpdCompData"></div>
				</div>
			</fieldset>
		</form>
	</div>
</div>
';
}
else{
	echo "Item not found!";
}
?>

<script>
	//loading animation during form processing
	$(document).ajaxStart(function(){
	    $('#loading').show();
	});
	$(document).ajaxComplete(function(){
	   $('#loading').hide();
	});

	$('#formUpdCompData').ajaxForm(function(response){
		$('#resUpdCompData').html(response);
	});
</script>
