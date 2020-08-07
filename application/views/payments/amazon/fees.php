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

    .col-wd-500{
        word-wrap: break-word;
        min-width: 500px;
        max-width: 500px;
    }
</style>

<div class="modal fade" id="mdl-upd-dim" tabindex="-1" aria-labelledby="mdl-upd-dim-lbl" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="mdl-upd-dim-lbl">Update Dimensions</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                Lorem ipsum dolor, sit amet consectetur adipisicing elit. Accusantium exercitationem iste eius molestias eum est, animi labore culpa sint, tempora reiciendis sequi at dignissimos, veritatis fugiat officiis et totam in!
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                <button type="button" class="btn btn-primary">Save changes</button>
            </div>
        </div>
    </div>
</div>

<div class="card bg-light border border-grey-300 rounded-0 mb-3">
    <div class="card-body py-2">
        <div class="form-inline">
            <label class="mr-2 font-weight-bold small" for="txtAmzAcctId">Amazon Account: </label>
            <select id="txtAmzAcctId" class="custom-select custom-select-sm rounded-0 w-25 mr-2">
                <?php echo _options_amz_accts($this->session->userdata('_userid')); ?>
            </select>
            <button type="button" name="btnPrevFees" id="btnPrevFees" class="btn btn-sm btn-primary" data-toggle="button" aria-pressed="false">Preview Fees</button>
        </div>
    </div>
</div>

<div id="resPrevFees"></div>

<?php $this->load->view('templates/footer'); ?>

<script>
    $(document).ready(function(){
        /**
         * Preview fees on button click
         */
        $('#btnPrevFees').click(function(){

            // Check for non empty amazon account id
            if($('#txtAmzAcctId').val() != "")
            {
                const amz_acct_id = $('#txtAmzAcctId').val(); 
                
                // Get fee preview report
                get_done_report(amz_acct_id); 
            }
            else swal({title: "Oops!", text: "Please select an account.", icon: "error"});
            
        }); 

        /*
         * Ajax request to get done report
         * 
         * If _DONE_ reports are available then fetch it 
         * else request a new report check status till it gets _DONE_ 
         *
         * @return mixed 
         */
        function get_done_report(amz_acct_id)
        {
            $.ajax({
                type: "get", 
                url: "<?php echo base_url('payments/amazon/fees/get_done_report');  ?>", 
                data: "amzacctid="+amz_acct_id, 
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
                            // Output report
                            $('#resPrevFees').html(res.report); 

                            const dt_fees = $('#tblFeePrev').DataTable({
                                language: {
                                    'search' : '' /*Empty to remove the label*/
                                },
                                paging: false, 
                                lengthChange: false, 
                                dom: 
                                    "<'row mb-0'<'col-md-2'f><'col-md-10'B>>" + 
                                    "<'row'<'col-sm-12'tr>>" +
                                    "<'row'<'col-sm-12 col-md-5'i><'col-sm-12 col-md-7'p>>",
                                buttons: {
                                    dom: {
                                        button: {
                                            className: 'btn'
                                        }
                                    }, 
                                    buttons: [
                                        {
                                            extend: 'colvis', 
                                            text: '<i class="fas fa-eye-slash"></i> Show/Hide Columns', 
                                            className: 'btn btn-sm btn-light text-secondary border-grey-300', 
                                        },
                                        {
                                            extend: 'excel', 
                                            text: '<i class="fas fa-file-export"></i> Export to Excel', 
                                            className: 'btn btn-sm btn-light text-secondary border-grey-300', 
                                        },
                                        {   
                                            text: '<i class=""></i> Update Dimensions', 
                                            className: 'btn btn-sm btn-light text-secondary border-grey-300', 
                                            action: function(e, dt, node, config) {
                                                $('#mdl-upd-dim').modal({
                                                    show: true, 
                                                    backdrop: false, 
                                                    keyboard: false
                                                });
                                            }

                                        }
                                    ]
                                },  
                                scrollY: '60vh', 
                                scrollX: true, 
                                scrollCollapse: true, 
                                fixedColumns:   {
                                    leftColumns: 7
                                }
                            }); 

                            // Customize 
                            $('.dataTables_filter input').attr({type: "search", placeholder:"Search..."});
                            $('.dataTables_filter input').addClass('ml-0');
                            $('.dt-buttons').removeClass('btn-group'); 

                            // Hide loading animation
                            $('#loader').addClass("d-none");
                        }
                        else {
                            // Wait 10 seconds and check report request status
                            setTimeout(function(){
                                get_report_status(amz_acct_id, res.rep_req_id[0]); 
                            }, 10000); 
                        }
                    }
                    else {
                        // Show error message
                        $('#resPrevFees').html(res.message);

                        // Hide loading animation
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

        /*
         * Ajax request to get report status
         * 
         * @return mixed 
         */
        function get_report_status(amz_acct_id, rep_req_id)
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
                        if(res.report_status == "_DONE_")
                        {
                            get_done_report(amz_acct_id); 
                        }
                        else {
                            // Wait for 5 more seconds and check report status again
                            setTimeout(function(){
                                get_report_status(amz_acct_id, rep_req_id); 
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