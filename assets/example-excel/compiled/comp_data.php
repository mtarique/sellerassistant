<?php 
//database connection
require_once($_SERVER['DOCUMENT_ROOT'].'/oms/config/ConConfig.php');
//mt defined functions
include_once($_SERVER['DOCUMENT_ROOT'].'/oms/lib/mtdefined/mt-function.php');
//Start a php session, Get Active User Info
require_once($_SERVER['DOCUMENT_ROOT']."/oms/config/Session.php");
//file to track users access, must include Session.php and ConConfig.php before this
require_once($_SERVER['DOCUMENT_ROOT']."/oms/config/UsersLog.php");
//page tempelate
require_once($_SERVER['DOCUMENT_ROOT']."/oms/src/PageTemp.php");

$tmpl = new PageTempelate(); //new tempelate object
$lnks = new PageLinks();	 //new lnks object

?>

<!DOCTYPE html>
<html lang="en">
<head>
	<meta charset="UTF-8">
	<title>Compiled Data | OMS</title>
	<?php $lnks->getCssLinks(); 			   	// Add CSS links ?>
	<style>
		.input-xs {
		  height: 22px;
		  /* width: 100px; */
		  padding: 2px 5px;
		  font-size: 12px;
		  line-height: 1.5; /* If Placeholder of the input is moved up, rem/modify this. */
		  border-radius: 0px;
		}
	</style>
</head>
<body style="margin-top: -10px;">
<div class="container">
	<h3 style="color: #000000 !important;">Compiled Data</h3>
	<a href="#" data-toggle="modal" data-target="#mdlBulkUpdCompData" data-backdrop="static" data-keyboard="false" class="" id="" product="">Bulk Update Compiled Data</a> | 
	<a href="/oms/apps/invt/compiled/comp_data_download.php?action=Download&compdatalnks=1&reprttype=CompiledData" class="small">Download Compiled Data</a>
	<!-- LOADING ANIMATION -->
	<div class="row loading-overlay-mt" id="loading" style="display: none;">
		<div class="col-sm-12 text-center" style="margin-top: 200px;">
			<img src="/oms/img/icons/loading-gears.gif"/><br><br>
			<p class="text-danger">Your request is being processed, Please wait...</p>
		</div>
	</div>
	
	<!-- SEARCH ITEM -->
	<div class="panel panel-default small" style="background:#fafafa; border-radius: 0px;">
		<div class="panel-body">
			<form action="/oms/apps/invt/compiled/comp_data_fetch.php" method="POST" class="form-inline" id="formSearchItem">
				<div class="form-group">
					<label for="txtItemSearchKey">Enter Item Number: </label>
					<input type="text" name="txtItemSearchKey" id="txtItemSearchKey" class="form-control input-xs" placeholder="Example: 20045">
					<button type="submit" name="btnSearchItem" id="btnSearchItem" class="btn btn-xs btn-warning">Search</button>
					<span class="text-primary">* Enter item number to get item compiled data.</span>
				</div>
			</form>
		</div>
	</div>

	<div id="resSearchItem"></div>

	<!-- Bulk update modal dialogue -->
	<div class="modal fade small" id="mdlBulkUpdCompData" role="dialog">
		<div class="modal-dialog modal-md">
			<div class="modal-content">
				<form action="/oms/apps/invt/compiled/comp_data_update.php" method="POST" id="formBulkUpdCompData">
				<div class="modal-header">
					<button type="button" class="close" data-dismiss="modal">&times;</button>
					<h4 class="modal-title" style="color: #000000 !important;">Bulk Update Compiled Data</h4>
				</div>
				<div class="modal-body" id="wrapper">
					<div id="resBulkUpdCompData"></div>
					<div class="form-group">
						<label for="fileBulkUpdCompData">Select compiled data bulk uploader file:</label>
						<input type="file" name="fileBulkUpdCompData" id="fileBulkUpdCompData" accept="application/vnd.openxmlformats-officedocument.spreadsheetml.sheet" data-buttonname="btn-default" data-size="sm" class="filestyle" list="itemsList">
					</div>
				</div>
				<div class="modal-footer">
					<div class="row">
						<div class="col-sm-12 left-left" style="text-align: left !important;">Click on Download Template button to download uploader file..</div>
					</div>
					<div class="row">
						<div class="col-sm-6 text-left">
						<div class="btn-group">
							<!-- <a href="/oms/files/Bulk.Update.Compiled-Data.Lite.xlsx" class="btn btn-sm btn-default text-left">Lite</a> -->
							<a href="/oms/files/Bulk.Update.Compiled-Data.v02.xlsx" class="btn btn-sm btn-default text-left">Download Template</a>
						</div>
						</div>
						<div class="col-sm-6">
						<img src="/oms/img/icons/Rolling.gif" alt="loding" id="mdlLoadingIcon" style="display: none;">
						<!-- <button type="submit" name="btnBulkUpdCompDataOldVer" class="btn btn-sm btn-warning">Upload</button> -->
						<button type="submit" name="btnBulkUpdCompData" class="btn btn-sm btn-info">Update</button>
						<button type="button" class="btn btn-sm btn-warning" data-dismiss="modal">Close</button>
						</div>
						</div>
				</div>
				</form>
			</div>
		</div>
	</div><!-- End compiled data bulk update -->

</div>
<?php $lnks->getJsLinks(); //add JS links ?>
<script>
	//loading animation during form processing
	$(document).ajaxStart(function(){
	    $('#loading').show();
	});
	$(document).ajaxComplete(function(){
	   $('#loading').hide();
	});

	$('#formSearchItem').ajaxForm(function(response){
		$('#resSearchItem').html(response);
	});

	// Process bulk upload form
	$('#formBulkUpdCompData').ajaxForm(function(response){
		$('#resBulkUpdCompData').html(response);
	});


</script>
<script>
	$('#mdlBulkUpdCompData').on('hidden.bs.modal', function(){
		$('#resBulkUpdCompData').text('');
		$(this).find('form').trigger('reset');
	});
</script>

<script>
	$(document).ready(function(){
		// Intitialize jquery date picker
		$(".datepicker").datepicker({
	      	dateFormat: "yy-mm-dd",
          	showWeek: true,
		  	firstDay: 1,
		  	changeMonth: true,
      		changeYear: true,
	    });
	});
</script>
</body>
</html>