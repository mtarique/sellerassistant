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
                url: "<?php echo base_url('payments/amazon/fees/get_done_reports'); ?>", 
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

                        check_report_status(amz_acct_id, rep_req_id);

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
                        if(res.pro_status[0] == "_DONE_")
                        {
                            fetch_report(amz_acct_id, res.gen_rep_id[0])
                        }
                        else if(res.pro_status[0] == "_SUBMITTED_" || res.pro_status[0] == "_IN_PROGRESS_")
                        {
                            setTimeout(function() {
                                check_report_status(amz_acct_id, rep_req_id)
                            }, 10000);
                        } 
                        else if(res.pro_status[0] == "_CANCELLED_" || res.pro_status[0]== "_DONE_NO_DATA_")
                        {
                            //$("#resPrevFees").html(res.message); 
                            get_done_reports(amz_acct_id); 
                            
                        } 
                    }
                    else $("#resPrevFees").html(res.message); 
                }, 
                error: function(xhr) 
                {
                    const xhr_text = xhr.status+" "+xhr.statusText;
                    swal({title: "Request error!", text: xhr_text, icon: "error"});  
                }
            }); 
        }

        function get_done_reports(amz_acct_id)
        {
            $.ajax({
                type: "get", 
                url: "<?php echo base_url('payments/amazon/fees/get_done_reports'); ?>", 
                data: "amzacctid="+amz_acct_id, 
                dataType: "json", 
                success: function(res)
                {
                    if(res.status) 
                    {
                        fetch_report(amz_acct_id, res.gen_rep_id[0])
                    }
                    else $("#resPrevFees").html(res.message); 
                }, 
                error: function(xhr) 
                {
                    const xhr_text = xhr.status+" "+xhr.statusText;
                    swal({title: "Request error!", text: xhr_text, icon: "error"});  
                }
            }); 
        }

        function fetch_report(amz_acct_id, gen_rep_id)
        {
            $.ajax({
                type: "get", 
                url: "<?php echo base_url('payments/amazon/fees/get_report'); ?>", 
                data: "amzacctid="+amz_acct_id+"&genrepid="+gen_rep_id, 
                dataType: "json", 
                success: function(res)
                {
                    if(res.status) 
                    {
                        $("#resPrevFees").html(res.message); 
                    }
                    else {
                        $("#resPrevFees").html(res.message); 
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