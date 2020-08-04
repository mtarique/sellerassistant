<?php 
$this->load->view('templates/header'); 
$this->load->view('templates/topnav'); 
$this->load->view('templates/wrapper'); 
$this->load->view('templates/titlebar'); 
$this->load->view('templates/loader'); 
?>

<style>
    .prod-img{
	   width: 100%!important;
	   min-height: 100px !important;
	   max-height: 100px !important;
	   object-fit: contain;
	}
</style>

<div class="card bg-light border-0 rounded-0 mb-3">
    <div class="card-body">
        <h5 class="card-title">Amazon Account</h5>
        <form id="formPrevFees" class="form-inline">
            <label class="mr-2 sr-only" for="txtAmzAcctId">Amazon Account</label>
            <select name="txtAmzAcctId" id="txtAmzAcctId" class="custom-select custom-select-sm rounded-0 w-25 mr-2" required>
                <?php echo _options_amz_accts($this->session->userdata('_userid')); ?>
            </select>
            <button type="submit" name="btnPrevFees" id="btnPrevFees"class="btn btn-sm btn-primary rounded-0 shadowsm">Preview Fees</button>
        </form>
    </div>
</div>

<div id="resPrevFees"></div>     

<?php $this->load->view('templates/footer'); ?>

<script type="text/javascript">
    $(document).ready(function(){
        
        // Submit preview fba estimated fees form
        $('#formPrevFees').submit(function(event){

            event.preventDefault(); 

            const form_data = $(this).serialize(); 

            get_report(form_data);             
        }); 

        /*
         * Get fee preview report - AJAX request
         *
         * 1. Check for most recent _DONE_ reports and fetch it
         * 2. If no _DONE_ reports are available then request a new report and check report request status
         *
         * @param   form_data   Contains Amazon account id [Better to use GET method and send data as amz_acct_id]
         * @return  html 
         *
         */
        function get_report(form_data)
        {
            $.ajax({
                type: "post", 
                url: "<?php echo base_url('payments/amazon/fees/get_done_reports');  ?>", 
                data: form_data, 
                dataType: "json", 
                beforeSend: function()
                {
                    $('#loader').removeClass("d-none");
                }, 
                success: function(res)
                {   
                    if(res.status)
                    {   
                        if(res.message == "REPORT_GENERATED")
                        {
                            $('#resPrevFees').html(res.report); 

                            $('#loader').addClass("d-none");
                        }
                        else {
                            // Wait 10 seconds and check report request status
                            setTimeout(function(){
                                get_report_status(form_data, res.rep_req_id[0]); 
                            }, 10000); 
                        }
                    }
                    else {
                        $('#resPrevFees').html(res.message);
                        $('#loader').addClass("d-none");
                    }
                }, 
                error: function(xhr)
                {
                    const xhr_text = xhr.status+" "+xhr.statusText;
                    swal({title: "Request error!", text: xhr_text, icon: "error"});
                }
            }); 
        }

        function get_report_status(form_data, rep_req_id)
        {
            // Check report status
            $.ajax({
                type: "get", 
                url: "<?php echo base_url('payments/amazon/fees/get_report_status'); ?>", 
                data: form_data+"&repreqid="+rep_req_id, 
                dataType: "json", 
                success: function(res)
                {
                    if(res.status)
                    {
                        if(res.report_status == "_DONE_")
                        {
                            get_report(form_data); 
                        }
                        else {
                            setTimeout(function(){
                                get_report_status(form_data, rep_req_id); 
                            }, 5000)
                        }
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
        }
    });
</script>