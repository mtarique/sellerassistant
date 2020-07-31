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

        function get_report(form_data)
        {
            $.ajax({
                type: "post", 
                url: "<?php echo base_url('payments/amazon/fees/get_done_reports');  ?>", 
                //data: $(this).serialize(), 
                data: form_data, 
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
                    //JSON.parse(res)
                    if(res.status)
                    {   
                        if(res.message == "REPORT_GENERATED")
                        {
                            $('#resPrevFees').html(res.report); 
                        }
                        else {
                            setTimeout(function(){
                                //get_report(form_data)
                                get_report_status(form_data, res.rep_req_id[0]); 
                                
                            }, 10000); 
                            //$('#resPrevFees').html("Report Request Id: "+res.rep_req_id[0]); 
                        }
                        
                    }
                    else {
                        $('#resPrevFees').html(res.message);
                        /* if(res.message == "REQUEST_REPORT")
                        {
                            swal({title: "Oops!", text: res.text, icon: "error"});
                        }
                        else {
                            $('#resPrevFees').html(res.message); 
                        } */
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