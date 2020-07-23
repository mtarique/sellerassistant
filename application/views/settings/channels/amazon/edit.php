<?php 
$this->load->view('templates/header'); 
$this->load->view('templates/topnav'); 
$this->load->view('templates/wrapper'); 
$this->load->view('templates/settings-nav'); 
$this->load->view('templates/titlebar'); 
$this->load->view('templates/loader'); 

// Check account details exists if not then stop the execution of program
if(!isset($amz_acct_id) || !isset($amz_acct_name) || !isset($marketplace_id) || !isset($seller_id) || !isset($mws_auth_token) || !isset($aws_access_key_id) || !isset($secret_key)) 
{   
    exit(show_alert('warning', 'Requested Amazon account details not found in the system.'));
} 
?>
<!--
PROCESS OF AUTHORIZING A DEVELOPER
https://help.godatafeed.com/hc/en-us/articles/360016189091-Connecting-Amazon-Authorize-a-Developer
-->

<?php 
    
?>

<div class="row">
    <div class="col-md-4">
        <div class="card border-0 rounded-0">
            <div class="card-body p-0">
                <div id="resEditAmzAcct"></div>
                <form id="formEditAmzAcct">
                    <div class="form-group sr-only">
                        <label for="inputAmzAcctId" class="font-weight-bold small req-after">Account Id</label>
                        <input type="text" name="inputAmzAcctId" id="inputAmzAcctId" class="form-control form-control-sm" value="<?php echo $this->encryption->encrypt($amz_acct_id); ?>" placeholder="Name your account (example &ldquo;US Account&rdquo;)" required>
                    </div>
                    <div class="form-group">
                        <label for="inputAmzAcctName" class="font-weight-bold small req-after">Account Name</label>
                        <input type="text" name="inputAmzAcctName" id="inputAmzAcctName" class="form-control form-control-sm" value="<?php echo $amz_acct_name; ?>" placeholder="Name your account (example &ldquo;US Account&rdquo;)" required>
                    </div>
                    <div class="form-group">
                        <label for="inputMpId" class="font-weight-bold small req-after">Marketplace</label>
                        <select name="inputMpId" id="inputMpId" class="custom-select custom-select-sm" required>
                            <?php echo _options_marketplaces($marketplace_id); ?>
                        </select>
                    </div>
                    <div class="form-group">
                        <label for="inputSellerId" class="font-weight-bold small req-after">Seller Id</label>
                        <input type="text" name="inputSellerId" id="inputSellerId" class="form-control form-control-sm" value="<?php echo $seller_id; ?>" required>
                    </div>
                    <div class="form-group">
                        <label for="inputMwsAuthToken" class="font-weight-bold small req-after">MWS Auth Token</label>
                        <input type="text" name="inputMwsAuthToken" id="inputMwsAuthToken" class="form-control form-control-sm" value="<?php echo $mws_auth_token; ?>" required>
                    </div>
                    <div class="form-group">
                        <label for="inputAWSAccessKeyId" class="font-weight-bold small req-after">AWS Access Key Id</label>
                        <input type="text" name="inputAWSAccessKeyId" id="inputAWSAccessKeyId" class="form-control form-control-sm" value="<?php echo $aws_access_key_id; ?>" required>
                    </div>
                    <div class="form-group">
                        <label for="inputSecretKey" class="font-weight-bold small req-after">Secret Key</label>
                        <input type="text" name="inputSecretKey" id="inputSecretKey" class="form-control form-control-sm" value="<?php echo $secret_key; ?>" required>
                    </div>
                    <div class="form-group text-right">
                        <button type="submit" name="btnSaveChanges" id="btnSaveChanges" class="btn btn-sm btn-primary">Save Changes</button>
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
         * Submit edit Amazon account form
         */
        $('#formEditAmzAcct').submit(function(event){
            event.preventDefault(); 

            $.ajax({
                type: "post", 
                url: "<?php echo base_url('settings/channels/amazon/update'); ?>", 
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
                        $('#resEditAmzAcct').html(res.message);  
                    }
                    else {
                        $('#resEditAmzAcct').html(res.message); 
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