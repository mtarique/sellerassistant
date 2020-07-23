<?php 
$this->load->view('templates/header'); 
$this->load->view('templates/topnav'); 
$this->load->view('templates/wrapper'); 
$this->load->view('templates/titlebar'); 
$this->load->view('templates/loader'); 
?>

<div class="row">
    <div class="col-md-4">
        <div class="card rounded-0 shadow-sm">
            <div class="card-body">
                <form id="formCalcFBAFees">
                    <div class="form-group">
                        <label for="txtMp" class="req-after font-weight-bold small">Select Marketplace</label>
                        <select name="txtMp" id="txtMp" class="custom-select custom-select-sm">
                            <?php echo _options_marketplaces('ATVPDKIKX0DER'); ?>
                        </select>
                    </div>
                    <div class="form-group">
                        <label for="txtProdDim" class="font-weight-bold small req-after">Product Dimension (Inches)</label>
                        <div class="d-flex flex-row">
                            <input type="number" step="any" name="txtLongestSide" id="txtLongestSide" class="form-control form-control-sm text-center" placeholder="Length" required>
                            <span class="px-1">x</span>
                            <input type="number" step="any" name="txtMedianSide" id="txtMedianSide" class="form-control form-control-sm text-center" placeholder="Width" required>
                            <span class="px-1">x</span>
                            <input type="number" step="any" name="txtShortestSide" id="txtShortestSide" class="form-control form-control-sm text-center" placeholder="Height" required>
                        </div>
                    </div>
                    <div class="form-group">
                        <label for="txtProdWeight" class="font-weight-bold small req-after">Product Weight (Pounds)</label>
                        <input type="number" step="any" name="txtProdWeight" id="txtProdWeight" class="form-control form-control-sm text-right" placeholder="Weight" required>
                    </div>
                    <div class="form-group">
                        <label for="txtOrderDate" class="font-weight-bold small req-after">Order Date</label>
                        <input type="text" name="txtOrderDate" id="txtOrderDate" class="form-control form-control-sm flatpickr-datepicker" value="<?php echo date('Y-m-d'); ?>" placeholder="Select date..." required>
                    </div>
                    <div class="form-group text-righ">
                        <div class="clearfix">
                            <div class="float-left"><em class="text-muted small req-before">Indicates mandatory fields</em></div>
                            <div class="float-right">
                                <button type="submit" name="btnCalcFBAFees" id="btnCalcFBAFees" class="btn btn-sm btn-primary shadow-sm">Calculate FBA Fees</button>
                            </div>
                        </div>
                    </div>
                </form>
            </div>       
        </div>
    </div>    
    <div class="col-md-5">
        <div class="card bg-dark rounded-0 shadow-sm">
            <div class="card-body bg-dark text-white">
                <div class="card-title text-center">
                    <h3 class="mb-0">$<span id="txtFulFees">0.00</span></h3>
                    <p class="text-secondary">Fulfillment fees</p>
                </div>
                <div class="clearfix pb-2">
                    <div class="float-left text-nowrap pr-3">Storage fees</div>
                    <div class="float-right text-nowrap text-light-blue-200 pl-3">$<span id="txtStgFees">0.00</span> per month</div>
                </div>
                <div class="clearfix pb-2">
                    <div class="float-left text-nowrap pr-3">Product size</div>
                    <div class="float-right text-nowrap text-light-blue-200 pl-3"><span id="txtProdSize">Standard-Size</span></div>
                </div>
                <div class="clearfix pb-2">
                    <div class="float-left text-nowrap pr-3">Product size tier</div>
                    <div class="float-right text-nowrap text-light-blue-200 pl-3"><span id="txtProdSizeTier">Large Standard (3 to 20 lb.)</span></div>
                </div>
                <div class="clearfix pb-2">
                    <div class="float-left text-nowrap pr-3">Dimensional Weight</div>
                    <div class="float-right text-nowrap text-light-blue-200 pl-3"><span id="txtDimWt">0.00</span> lb</div>
                </div>
                <div class="clearfix pb-2">
                    <div class="float-left text-nowrap pr-3">Shipping Weight</div>
                    <div class="float-right text-nowrap text-light-blue-200 pl-3"><span id="txtShpWt">0.00</span> lb</div>
                </div>
                <div class="clearfix pb-2">
                    <div class="float-left text-nowrap pr-3">Product volume</div>
                    <div class="float-right text-nowrap text-light-blue-200 pl-3"><span id="txtProdVol">0.00</span> cu. ft.</div>
                </div>
            </div>    
            <div class="card-footer">
                <p class="text-warning">
                    This is an estimated result, actual result may vary.
                </p>
            </div>   
        </div>
    </div>
    <div class="col-md-3">
        <p>Amazon USA fulfilment and storage fees calculator is effective from <span class="text-nowrap font-weight-bold">22<sup>nd</sup> February, 2017</span>.</p>
        <p><a href="https://sellercentral.amazon.com/gp/help/external/G201411300?language=en_US&amp;ref=au_G201411300_cont_GABBX6GZPA8MSZGW" target="_blank">Click here</a> to learn more about FBA fees for USA marketplace.</p>
    </div>
</div>

<?php $this->load->view('templates/footer'); ?>

<script>
    $(document).ready(function(){
        /**
         * Submit Calculate FBA fees form event
         */
        $('#formCalcFBAFees').submit(function(event){
            event.preventDefault();

            $.ajax({
                type: "post", 
                url: "<?php echo base_url('pricing/fba_fees_calc/calculate'); ?>",
                data: $(this).serialize(), 
                dataType: "json", 
                beforeSend: function()
                {
                    $('#loader').removeClass("d-none");
                }, 
                complete: function()
                {
                    $('#loader').addClass("d-none");
                }, 
                success: function(res)
                {
                    if(res.status)
                    {
                        $('#txtFulFees').text(res.fba_ful_fees); 
                        $('#txtStgFees').text(res.fba_stg_fees); 
                        $('#txtProdSize').text(res.prod_size); 
                        $('#txtProdSizeTier').text(res.prod_size_tier); 
                        $('#txtDimWt').text(res.dim_wt); 
                        $('#txtShipWt').text(res.shp_wt); 
                        $('#txtProdVol').text(res.prod_vol); 
                    }
                    else {
                        swal({title: "Oops!", text: res.message, icon: "error"});
                    }
                }, 
                error: function(xhr)
                {
                    var xhr_text = xhr.status+" "+xhr.statusText;
					swal({title: "Request error!", text: xhr_text, icon: "error"});
                }
            }); 
        });
    }); 
</script>