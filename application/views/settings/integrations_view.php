<?php 
$this->load->view('templates/header'); 
$this->load->view('templates/topnav'); 
$this->load->view('templates/wrapper'); 
$this->load->view('templates/titlebar'); 
$this->load->view('templates/loader'); 
?>
<!--
PROCESS OF AUTHORIZING A DEVELOPER
https://help.godatafeed.com/hc/en-us/articles/360016189091-Connecting-Amazon-Authorize-a-Developer
-->

<div class="row">
    <div class="col-md-6">
        <div class="card rounded-0 shadowsm">
            <div class="card-body">
                <div id="resConnectMWS"></div>
                <p>Connecting your Amazon account is easy! You just need an Amazon professional seller account to sell on Amazon.</p>
                <form id="formConnectMWS">
                    <div class="form-group">
                        <label for="inputMpId" class="font-weight-bold small">Marketplace</label>
                        <select name="inputMpId" id="inputMpId" class="custom-select custom-select-sm">
                            <option value="ATVPDKIKX0DER">Amazon.com (United States)</option>
                        </select>
                    </div>
                    <div class="form-group">
                        <label for="inputSellerId" class="font-weight-bold small">Seller Id</label>
                        <input type="text" name="inputSellerId" id="inputSellerId" class="form-control form-control-sm">
                    </div>
                    <div class="form-group">
                        <label for="inputMwsAuthToken" class="font-weight-bold small">MWS Auth Token</label>
                        <input type="text" name="inputMwsAuthToken" id="inputMwsAuthToken" class="form-control form-control-sm">
                    </div>
                    <div class="form-group text-right">
                        <button type="submit" name="btnConnectMWS" id="btnConnectMWS" class="btn btn-sm btn-success">Connect</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<?php $this->load->view('templates/footer'); ?>

<script>
    $(document).ready(function(){

        /**
         * Submit connect MWS account form
         */
        $('#formConnectMWS').submit(function(event){
            event.preventDefault(); 

            $.ajax({
                type: "post", 
                url: "<?php echo base_url('settings/integrations/connect_mws'); ?>", 
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
                        //swal({title: "Connected!", text: res.message, icon: "success"});
                        $('#resConnectMWS').html(res.message);  
                    }
                    else {
                        //swal({title: "Oops!", text: res.message, icon: "error"}); 
                        $('#resConnectMWS').html(res.message); 
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