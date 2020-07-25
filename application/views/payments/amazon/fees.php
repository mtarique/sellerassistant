<?php 
$this->load->view('templates/header'); 
$this->load->view('templates/topnav'); 
$this->load->view('templates/wrapper'); 
$this->load->view('templates/titlebar'); 
$this->load->view('templates/loader'); 
?>

<div class="card bg-light border-0 rounded-0 mb-3">
    <div class="card-body">
        <h5 class="card-title">Amazon Account</h5>
        <!-- <p class="card-subtitle">Select account to preview your FBA fees.</p> -->
        <form id="formPrevFees" class="form-inline">
            <label class="mr-2 sr-only" for="inputAmzAcctId">Amazon Account</label>
            <select name="inputAmzAcctId" id="inputAmzAcctId" class="custom-select custom-select-sm rounded-0 w-25 mr-2" required>
                <?php echo _options_amz_accts($this->session->userdata('_userid')); ?>
            </select>
            <button type="submit" name="btnPrevFees" id="btnPrevFees"class="btn btn-sm btn-primary rounded-0 shadowsm">Preview Fees</button>
        </form>
    </div>
</div>

<div id="resPrevFees"></div>     

<?php $this->load->view('templates/footer'); ?>

<script>
    $(document).ready(function(){
        $("#formPrevFees").submit(function(event){
            
            event.preventDefault(); 

            const amz_acct_id = $('#inputAmzAcctId').val(); 

            $.ajax({
                type: "post", 
                url: "<?php echo base_url('payments/amazon/fees/req_fba_est_fees_report'); ?>", 
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
                        const rep_req_id = res.report_request_id[0]; 

                        // Check report generation status
                        $('#resPrevFees').text("Report Request Id: "+rep_req_id); 

                        

                        // Work here
                        var refreshId = setInterval(function() {
                            /* var properID = CheckReload();
                            if (properID > 0) {
                                clearInterval(refreshId);
                            } */

                            var pro_status = check_report_status(amz_acct_id, rep_req_id); 

                            if(pro_status == "_DONE_")
                            {
                                $('#resPrevFees').append("<br>Generated Report Id: "+ check_report_status(amz_acct_id, rep_req_id)); 
                                clearInterval(refreshId);
                            }
                            else if(pro_status == "_IN_PROGRESS_") {
                                $('#resPrevFees').append("<br>Processing Status: "+ check_report_status(amz_acct_id, rep_req_id)); 
                                //clearInterval(refreshId);
                            }
                            else {
                                clearInterval(refreshId);
                            }

                        }, 10000);
                    }
                    else {
                        $('#resPrevFees').html(res.message); 
                    }
                }, 
                error: function(xhr) 
                {
                    const xhr_text = xhr.status+" "+xhr.statusText;
                    swal({title: "Request error!", text: xhr_text, icon: "error"});
                }
            }); 
        }); 

        
        function check_report_status(amz_acct_id, rep_req_id)
        {
            $.ajax({
                type: "get", 
                url: "<?php echo base_url('payments/amazon/fees/get_report_status'); ?>", 
                data: "amzacctid="+amz_acct_id+"&repreqid="+rep_req_id, 
                dataType: "json", 
                success: function(res)
                {
                    if(res.status) 
                    {
                       return res.gen_rep_id[0]; 
                    }
                    else {
                        return res.pro_status[0]; 
                    }
                }, 
                error: function(xhr) 
                {
                    const xhr_text = xhr.status+" "+xhr.statusText;
                    swal({title: "Request error!", text: xhr_text, icon: "error"});
                }
            }); 
        }
    }); 
</script>