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

<div class="modal fade" id="mdl-upd-dim" tabindex="-1" aria-labelledby="mdlUpdDimLbl" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content rounded-0">
            <form id="formUpdProdDim">
                <div class="modal-header rounded-0 bg-blue-800 text-light">
                    <h5 class="modal-title py-0" id="mdlUpdDimLbl">Update Dimensions</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="close">
                        <span class="text-light" aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body bglight">
                    <ol>
                        <li class="sr-only">
                            <p class="small">Selected Amazon Account ID.</p>
                            <label for="inputAmzAcctId" class="sr-only">Amazon Account Id</label>
                            <input type="text" name="inputAmzAcctId" id="inputAmzAcctId" class="form-control form-control-sm" required>
                        </li>
                        <li>
                            <p class="small">Download product dimension excel uploader with missing weight and dimensions SKU's.</p>
                            <button type="button" name="btnDownProdDimUploader" id="btnDownProdDimUploader" class="btn btn-light border-grey-300">Download <i class="fas fa-download"></i></button>
                        </li>
                        <li>
                            <p class="small">Update product's weight and dimension in downloaded uploader file, save it and upload.</p>
                            <div class="input-group mb-3">
                                <div class="custom-file">
                                    <input type="file" class="custom-file-input" id="fileProdDimUploader" aria-describedby="inputGroupFileAddon01" accept=".xlsx" required>
                                    <label class="custom-file-label" for="fileProdDimUploader">Choose file</label>
                                </div>
                            </div>
                        </li>
                    </ol>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-sm btn-secondary" data-dismiss="modal">Close</button>
                    <button type="button" class="btn btn-sm btn-primary">Save changes</button>
                </div>
            </form>
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

                                                $('#inputAmzAcctId').val(amz_acct_id);
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

                            // Array of missing dimension SKU's
                            const ws_data = [
                                ['SKU', 'ASIN', 'Packaged Product Weight (gsrams)', 'Longest Side (inches)', 'Median Side (inches)', 'Shortest Side (inches)']
                            ];

                            // Loop through datatable rows
                            dt_fees.rows().every(function(){

                                // Current iteration row
                                var dt_row = this.data();

                                const ws_row = new Array(); 

                                // If calculated fulfillment fees is zero 
                                if(dt_row[5] === '0.00')
                                {
                                    ws_row.push(dt_row[1]); 
                                    ws_row.push(dt_row[2]); 

                                    ws_data.push(ws_row);
                                }
                            });

                            // Download SKUs with missing dimensions
                            $("#btnDownProdDimUploader").click(function(){
                                download_excel('Bulk.Update.Prod-Dimensions.v01', 'Template', ws_data); 
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

        /**
         * Download excel file using SheetJS and FileSaverJS
         * 
         * @param string    file_name     Name of the file
         * @param string    sheet_name    Name of the sheet
         * @param array     sheet_data    Array to outputted in sheet
         * @return 
         */
        function download_excel(file_name, sheet_name, sheet_data) {

            // New workbook
            var wb = XLSX.utils.book_new(); 

            // Set excel property
            wb.props = {
                Title: file_name, 
                Subject: "Testing", 
                Author: "MD TARIQUE ANWER", 
                CreatedDate: new Date() 
            }

            // Create new sheet
            wb.SheetNames.push(sheet_name); 

            // Get data to sheet
            var ws = XLSX.utils.aoa_to_sheet(sheet_data);

            wb.Sheets[sheet_name] = ws; 

            var wbout = XLSX.write(wb, {bookType: 'xlsx', type: 'binary'});  

            // Convert binary to octet-stream 
            function s2ab(s) {
                var buf = new ArrayBuffer(s.length);

                var view = new Uint8Array(buf); 

                for (var i = 0; i<s.length; i++) view[i] = s.charCodeAt(i) & 0xFF; 

                return buf;    
            }

            // Download excel file
            saveAs(new Blob([s2ab(wbout)],{type:"application/octet-stream"}), file_name+'.xlsx');
        }
    }); 

</script>