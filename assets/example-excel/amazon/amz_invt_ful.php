<?php  
//database connection
require_once($_SERVER['DOCUMENT_ROOT'].'/oms/config/ConConfig.php');
//mt defined functions
include_once($_SERVER['DOCUMENT_ROOT'].'/oms/lib/mtdefined/mt-function.php');
// Start a php session, Get Active User Info
require_once($_SERVER['DOCUMENT_ROOT']."/oms/config/Session.php");
// File to track users access, must include Session.php and ConConfig.php before this
require_once($_SERVER['DOCUMENT_ROOT']."/oms/config/UsersLog.php");
// Page tempelate
require_once($_SERVER['DOCUMENT_ROOT']."/oms/src/PageTemp.php");

$tmpl = new PageTempelate(); //new tempelate object
$lnks = new PageLinks();	 //new lnks object
?>
<!DOCTYPE html>
<html lang="en">
<head>
	<meta charset="UTF-8">
	<title>Inventory Fulfilment Reports | OMS</title>
	<?php $lnks->getCssLinks(); ?>
	<style>
		li {
		  width: 2em;
		  height: 2em;
		  text-align: center;
		  overflow-wrap: break-word;
		  word-wrap: break-word;
		  line-height: 5em;
		  border-radius: 2em;
		  background: #2ECC71;
		  margin: 0 8vw 0 8vw;
		  display: inline-block;
		  color: #000000;
		  position: relative;
		}
		
		li::before{
		  content: '';
		  position: absolute;
		  top: .9em;
		  left: -17vw;
		  width: 17vw;
		  height: .2em;
		  background: #2ECC71;
		  z-index: -1;
		}

		li:first-child::before {
		  display: none;
		}

		.active {
		  background: #2ECC71;
		  color: #000000;
		}

		.active ~ li {
		  background: #efefef;
		  color: #000000;
		}

		.active ~ li::before {
		  background: #efefef;
		  color: #000000;
		}

		#formAmzInvtFul fieldset:not(:first-of-type){
			display: none;
		}

		legend{
			margin-left: -15px !important;
		}
	</style>
</head>
<body style="margin-top: -10px;">
	<div class="container">
		<!-- Page Heading -->
		<h3 style="color: #000000 !important;">Inventory Fulfilment <br><small>Calculate rate of sale and generates inventory fulfillment reports</small></h3>

		<!-- Page Links -->
		<a href="/oms/apps/sales/amazon/amz_sales.php">Update Amazon Sales</a><br>
		<!-- Loading Animation -->
		<div class="row loading-overlay-mt" id="loading" style="display: none;">
			<div class="col-sm-12 text-center" style="margin-top: 200px;">
				<img src="/oms/img/icons/loading-gears.gif"/><br><br>
				<p class="text-danger">We are processing your request, Please wait...</p>
			</div>
		</div>
		
		<!-- Form processing results -->
		<div id="resultAmzInvtFul"></div>

		<!-- Steps processing line -->
		<ul class="steps-line text-nowrap">
			<li id="step_1" class="active">Calculate Current ROS</li>
			<li id="step_2">Calculate Historical &amp; Forcasted ROS</li>
			<!-- <li id="step_3">Calculate Forcasted ROS</li> -->
			<li id="step_4">Download Reports</li>
		</ul>

		<!-- Form to generate amazon inventory fulfilment report -->
		<div class="panel panel-default" style="background:#fafafa; border-radius: 0px;">
			<div class="panel-body">
				<div class="col-sm-4"></div>
				<div class="col-sm-4">
				<form action="amz_invt_ful_process.php" method="POST" class="form-horizontal" id="formAmzInvtFul">
					<fieldset class="step_1">
						<legend>Calculate Current ROS</legend>
						<div class="form-group">
							<label for="txtMarketplaceId">Marketplace: </label>
							<select name="txtMarketplaceId" id="txtMarketplaceId" class="form-control input-sm" readonly="true" required>
								<!--option value="A2EUQ1WTGCTBG2">North America - CA</option>
								<option value="A1AM78C64UM0Y8">North America - MX</option-->
								<option value="ATVPDKIKX0DER" selected>North America - US</option>
								<!--option value="A1PA6795UKMFR9">Europe - DE</option>
								<option value="A1RKKUPIHCS9HS">Europe - ES</option>
								<option value="A13V1IB3VIYZZH">Europe - FR</option>
								<option value="APJ6JRA9NG5V4">Europe - IT</option>
								<option value="A1F83G8C2ARO7P">Europe - UK</option>
								<option value="A21TJRUUN4KGV">India - IN</option>
								<option value="A1VC38T7YXB528">Japan - JP</option>
								<option value="AAHKV2X7AFYLW">China - CN</option-->
							</select>
						</div>
						<div class="form-group">
							<label for="txtDateRange">Date Range: </label>
							<select name="txtDateRange" id="txtDateRange" class="form-control input-sm">
								<option value="13" selected>Last 13 weeks</option>
								<option value="0">Exact dates</option>
							</select>
							<?php  
								$to_date = date('Y-m-d', strtotime("last saturday"));

								$fm_date = date('Y-m-d', strtotime("-13 weeks sunday", strtotime($to_date)));
							?>
							<table id="tblExactDate" style="display: none;">
								<tr>
									<th>From: </th>
									<th></th>
									<th>To: </th>
								</tr>
								<tr>
									<td>
										<input type="text" name="txtFromDate" id="txtFromDate" class="datepicker form-control input-xs" value="<?php echo $fm_date; ?>" required>
									</td>
									<td>-</td>
									<td>
										<input type="text" name="txtToDate" id="txtToDate" class="datepicker form-control input-xs" value="<?php echo $to_date; ?>" required>
									</td>
								</tr>
							</table>
						</div>
						<div class="form-group">
							<label for="txtNumHistYears">Historical Years:</label>
							<select name="txtNumHistYears" id="txtNumHistYears" class="form-control input-sm">
								<?php  
									$query = $conn->query("SELECT DISTINCT 
										YEAR(purchase_date)  
										FROM amazon_sales") 
									or die("An error occurred: ".$conn->error);

									for($hy=1; $hy <= $query->num_rows-1; $hy++)
									{
										if($hy == 3){
											$opt_attr = "selected";
										}
										else{
											$opt_attr = "";	
										}
										echo '<option value="'.$hy.'" '.$opt_attr.'>'.$hy.'</option>';
									}
								?>
							</select>
						</div>
						<div class="form-group">
							<label for="txtSkuList">SKU's List: </label>
							<select name="txtSkuList" id="txtSkuList" class="form-control input-sm" required>
								<option value="">Select</option>
								<option value="skulist1">FBA SOLD SKUs</option>
								<option value="skulist2">FBA SKUs</option>
								<option value="skulist3">FBA WORKING SKUs</option>
							</select>
						</div>
						<div id="customSkuListUploader" class="form-group" style="display: none;">
							<!-- BLOCKED NO NEED TO DOWNLOAD AND UPLOAD LIST 2019/01/09
							<label for="fileCustomSkuList">Select FBA working sku list csv file</label>
							<input type="file" name="fileCustomSkuList" id="fileCustomSkuList" accept=".csv" data-buttonname="btn-default" data-size="sm" class="filestyle" list="custom-sku-list"> 
							-->
							<a href="amz_invt_ful_download.php?reportid=fbaworkingskulist">Download FBA working SKUs list.</a>
						</div>
						<div class="form-group">
							<div class="row">
								<div class="col-sm-6">
									<button type="button" id="btnSkipCalCurrROS" class="btn btn-sm btn-info btn-block">Download Reports</button>
								</div>
								<div class="col-sm-6">
									<button type="submit" name="btnCalCurrROS" id="btnCalCurrROS" class="btn btn-sm btn-warning btn-block">Calculate Current ROS</button>
								</div>
							</div>
						</div>
						<div class="form-group">
							<div class="row">
								<div class="col-sm-12">
									<button type="button" class="next btn btn-sm btn-default btn-block">Next</button>
								</div>
							</div>
						</div>
						
					</fieldset>
					<fieldset class="step_2">
						<legend style="font-size: 20px !important;">Calculate Historical &amp; Forcasted ROS</legend>
						<div class="form-group">
							<a href="#" id="getSKUsList">CLICK TO GET SKU LIST IN SETS OF 200 SKU'S</a>
						</div>
						<div class="form-group panel panel-warning" id="resultSKUsList"></div>
						<div class="form-group">
							<div class="row">
								<div class="col-sm-6">
									<button type="submit" name="btnCalHistROS" id="btnCalHistROS" class="btn btn-sm btn-info btn-block">Calculate Historical ROS</button>
								</div>
								<div class="col-sm-6">
									<button type="submit" name="btnCalForcROS" id="btnCalForcROS" class="btn btn-sm btn-warning btn-block">Calculate Forcasted ROS</button>
								</div>
							</div>
						</div>
						<div class="form-group">
							<div class="row">
								<div class="col-sm-6">
									<button type="button" class="prev btn btn-sm btn-default btn-block">Previous</button>
								</div>
								<div class="col-sm-6">
									<button type="button" class="next btn btn-sm btn-default btn-block">Next</button>
								</div>
							</div>
						</div>
						<div class="form-group">
							<em><strong class="text-danger">Note: </strong> Calculate weekly or only if required.</em>
						</div>
					</fieldset>
					<!-- <fieldset class="step_3">
						<legend>Calculate Forcasted ROS</legend>
						<div class="form-group">
							<button type="button" class="prev btn btn-sm btn-default">Previous</button>
							<button type="submit" name="btnCalForcROS" id="btnCalForcROS" class="btn btn-sm btn-info next">
								Calculate Forcasted ROS
							</button>
						</div>
					</fieldset> -->
					<fieldset class="step_3">
						<legend>Download Reports</legend>
						<div class="form-group">
							<div class="list-group">
								<div class="list-group-item disabled">
									<h4 class="list-group-item-heading" style="color: #000000 !important;">Reports List</h4>
									<p class="text-muted small">Click on the download button to download a particular report.</p>
								</div>
								<!-- #1: ROS Report -->  
								<div class="list-group-item">
							  		<h4 class="list-group-item-heading" style="color: #000000 !important;">Rate of Sale Report</h4>
							  		<p class="list-group-item-text small text-muted">Contains Default, Current, Historical and Forcasted ROS as per last calculations.</p>
						  			<div class="text-right">
						  				<a href="amz_invt_ful_download.php?reportid=rosrpt" class="btn btn-sm"><strong>DOWNLOAD<img src="/oms/img/icons/actions/down-arrow-16.png" alt="excel"></strong></a>
						  			</div>
							  	</div>
							  	<!-- #2: Master FBA Report -->
							  	<div class="list-group-item">
							  		<h4 class="list-group-item-heading" style="color: #000000 !important;">Master FBA Report</h4>
							  		<p class="list-group-item-text small text-muted">Contains Inventory availability, ROS, Weeks of Coverage, Recommended Order Qty etc.</p>
						  			<div class="text-right">
						  				<a href="amz_invt_ful_download.php?reportid=masterfbarpt" class="btn btn-sm"><strong>DOWNLOAD<img src="/oms/img/icons/actions/down-arrow-16.png" alt="excel"></strong></a>
						  			</div>
							  	</div>
							  	<!-- #3: Re-Order Report -->
							  	<div class="list-group-item">
							  		<h4 class="list-group-item-heading" style="color: #000000 !important;">Re-Order Report</h4>
							  		<p class="list-group-item-text small text-muted">Generates flags for items that needs to be ordered and create order report accordingly.</p>
						  			<div class="text-right">
						  				<a href="amz_invt_ful_download.php?reportid=reorderrpt" class="btn btn-sm" download><strong>EMAIL &amp; DOWNLOAD<img src="/oms/img/icons/actions/down-arrow-16.png" alt="excel"></strong></a>
						  			</div>
							  	</div>
							</div>
						</div>
						<div class="form-group">
							<button type="button" class="prev btn btn-sm btn-default">Previous</button>
						</div>
					</fieldset>
				</form>
				</div>
				<div class="col-sm-4">
					<!-- Amazon sales data last update date -->
					<?php  
						// Query to get last shipment date or sale date
						$query = $conn->query("SELECT MAX(purchase_date) AS last_sale_date FROM amazon_sales") or die("An error occurred: ".$conn->error);
						$result = $query->fetch_array();

						echo '
							<div class="alert alert-info alert-dismissible text-center" style="margin-top: 130px !important;">
							  	<a href="#" class="close" data-dismiss="alert" aria-label="close">&times;</a>
							  	<strong>Amazon sales data last updated on: </strong>
								<h3 style="margin-top: 0px !important;">'.date('l, dS M Y', strtotime("-1 day", strtotime($result['last_sale_date']))).'</h3>
								<a href="/oms/apps/sales/amazon/amz_sales.php" target="OmsAppsWorkSpace" class="top-nav-link">UPDATE AMAZON SALES DATA</a>
							</div>
						';
					?>
				</div>
			</div>
		</div>

		
	</div><!-- container -->
	<?php $lnks->getJsLinks(); ?>
	<script>
		$(document).ready(function(){
			$(".datepicker").datepicker({
		      	dateFormat: "yy-mm-dd",
	          	showWeek: true,
			  	//firstDay: 2, // Week Start from sunday
			  	changeMonth: true,
	      		changeYear: true,
		    });

		    // Show from date and to date fields
		    $("#txtDateRange").change(function(){
		    	if($(this).val() == 0){
		    		$("#tblExactDate").show();
		    	}
		    	else{
		    		$("#tblExactDate").hide();	
		    	}
		    });
		    /* BLOCKED CUSTOM SKU LIST UPLOADER 2019/01/09 */
		    // Show custome sku list uploader
		    $("#txtSkuList").change(function(){
		    	if($(this).val() =='skulist3'){
		    		$("#customSkuListUploader").fadeIn();
		    	}
		    	else{
		    		$("#customSkuListUploader").fadeOut();	
		    	}
		    });
			
			/*
			|------------------------------------------------------------------
			|	SKU LIST TO CALCULATE HIST AND FORC ROS IN SETS OF 200 ITEMS
			|------------------------------------------------------------------
			*/
		    $('#getSKUsList').click(function(){
				// Show loading animation on ajax start
				$(document).ajaxStart(function(){
				    $('#loading').show();  
				});
				// Hide loading animation on ajax complete
				$(document).ajaxComplete(function(){
				   $('#loading').hide();
				});

				$.ajax({
					type: "GET",
					url: "<?php echo 'amz_invt_ful_getsku.php'; ?>",
					data: 'getskuslist=true',
					dataType: "json",
					success: function(data){
						if(data.success){
							$('#resultSKUsList').html(data.message);

							/*
							|--------------------------------------
							|	CHECK/UNCHECK ALL ITEMS OF A SET
							|--------------------------------------
							*/
							$('.cbxSkuSet').each(function(){
								// Detect checkbox change event 
								$(this).change(function(){
									// Get the set id
									var set_id = $(this).val();

									// Check whether check box is checked
									if($(this).is(':checked')){
										// Check all sku of parent set
										 $('.cbxSkuSet'+set_id).prop('checked', true);
									}
									else{
										// Uncheck all sku of parent set
										$('.cbxSkuSet'+set_id).prop('checked', false);
									}
									
								});
							});
						}
						else{
							$('#resultSKUsList').html(data.message);
						}
					}
				});
			});
		});
	</script>
	<script>
		$(document).ready(function(){
			var current = 1, current_step, next_step, prev_step, steps;

			//
			$("#btnSkipCalCurrROS").click(function(){
				// Get current
				current_step = $(this).closest("fieldset");
				// Last step
				last_step = $("fieldset").eq(2);

				// Show and Hide Steps
				last_step.show();
				current_step.hide();

				// Move success pointer on step-line
				$("#"+current_step.attr("class")).removeClass("active");
				$("#"+last_step.attr("class")).addClass("active");
			});

			// 
			$(".next").click(function(){
				// Get current and next step
				current_step = $(this).closest("fieldset");
				next_step = $(this).closest("fieldset").next();
				
				// Show and Hide Steps
				next_step.show();
				current_step.hide();

				// Move success pointer on step-line
				$("#"+current_step.attr("class")).removeClass("active");
				$("#"+next_step.attr("class")).addClass("active");
			});

			// 
			$(".prev").click(function(){
				// Get current and previuos step
				current_step = $(this).closest("fieldset");
				prev_step = $(this).closest("fieldset").prev();

				// Show and Hide Steps
				prev_step.show();
				current_step.hide();

				// Move success pointer on step-line
				$("#"+current_step.attr("class")).removeClass("active");
				$("#"+prev_step.attr("class")).addClass("active");
			});
		});
	</script>
	<script>
		// Show loading animation on ajax start
		$(document).ajaxStart(function(){
		    $('#loading').show();  
		});

		// Hide loading animation on ajax complete
		$(document).ajaxComplete(function(){
		   $('#loading').hide();
		});

		$('#formAmzInvtFul').ajaxForm({
			success: function(response) { 
	            $('#resultAmzInvtFul').html(response);
	        },
	        error: function() {
	        	///var html_str = 
	        $('#resultAmzInvtFul').html('<p class="bg-danger">CGI activity timeout</p>');
	        } 
		});
	</script>

	<script>
		function dldAmzFulRpt(rpt_id){
			var mktp_id = document.getElementById("txtMarketplaceId").value;
			var fm_date = document.getElementById("txtFromDate").value;
			var to_date = document.getElementById("txtToDate").value;
			var hs_year = document.getElementById("txtNumHistYears").value;

			window.location.href = "amz_invt_ful_download.php?reportid="+rpt_id+"&marketplaceid="+mktp_id+"&fromdate="+fm_date+"&todate="+to_date+"&historicalyears="+hs_year;
		}
	</script>
</body>
</html>
