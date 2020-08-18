<?php 
$this->load->view('templates/header'); 
$this->load->view('templates/topnav'); 
$this->load->view('templates/wrapper'); 
$this->load->view('templates/titlebar'); 
$this->load->view('templates/loader'); 
?>

<div class="card bg-light border border-grey-300 rounded-0 mb-3">
    <div class="card-body py-2">
        <form class="form-inline" id="formGetPmts">
            <label class="font-weight-bold small mr-2" for="txtAmzAcctId">Amazon Account: </label>
            <select name="txtAmzAcctId" id="txtAmzAcctId" class="custom-select custom-select-sm rounded-0 mr-2" required>
                <?php echo _options_amz_accts($this->session->userdata('_userid')); ?>
            </select>
            <label for="txtPmtDateFm" class="font-weight-bold small mr-2">From: </label>
            <input type="text" name="txtPmtDateFm" id="txtPmtDateFm" class="form-control form-control-sm rounded-0 flatpickr-datepicker mr-2" value="<?php echo date('Y-m-d', strtotime('-30 days')); ?>" placeholder="Select from date..." required>
            <label for="txtPmtDateTo" class="font-weight-bold small mr-2">To: </label>
            <input type="text" name="txtPmtDateTo" id="txtPmtDateTo" class="form-control form-control-sm rounded-0 flatpickr-datepicker mr-2" value="<?php echo date('Y-m-d'); ?>" placeholder="Select to date...">
            <button type="submit" name="btnGetPmts" id="btnGetPmts"class="btn btn-sm btn-primary">Get Payments</button>
        </form>
    </div>
</div>

<div id="resAmzPmts"></div>
<div id="resGetPmts"></div>

<?php $this->load->view('templates/footer'); ?>

<script>
    $(document).ready(function(){
        /**
         * Get Payments
         * 
         * Submit get payments form 
         */
        $('#formGetPmts').submit(function(event){
            // Prevent form's default behaviour 
            event.preventDefault(); 

            // Ajax post request
            $.ajax({
                type: "post",
                url:  "<?php echo base_url('payments/amazon/payments/get_payments'); ?>", 
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
                        // Amazon payment table
                        $('#resGetPmts').html(res.message); 

                        // Initialize Datatable
                        const dt_amz_pmts = $('#tblAmzPmts').DataTable({
                            /** WILL BE USED IN FUTURE **/
                            /*
                            language: {
                                'search' : '' 
                            },
                            dom: 
                                "<'row mb-0'<'col-md-2'f><'col-md-10'B>>" + 
                                "<'row'<'col-sm-12'tr>>" +
                                "<'row'<'col-sm-12 col-md-5'i><'col-sm-12 col-md-7'p>>",
                            */
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
                                        text: '<i class="fas fa-upload"></i> Update Dimensions', 
                                        className: 'btn btn-sm btn-light text-secondary border-grey-300', 
                                        action: function(e, dt, node, config) {
                                            $('#mdl-upd-dim').modal({
                                                show: true, 
                                                backdrop: false, 
                                                keyboard: false
                                            });

                                            $('#inputAmzAcctId').val(amz_acct_id);
                                            $('#titleAmzActName').text(amz_acct_name); 
                                        }

                                    }
                                ]
                            }, 
                            aaSorting: [], 
						    order: [[ 0, "desc" ]],
                            info: false, 
                            paging: false, 
                            lengthChange: false, 
                            fixedHeader: {
                                headerOffset: $('#topnav').outerHeight()
                            }
                        }); 

                        // Get payment fees on button comapre fees click
                        /**
                         * Compare payment fees
                         * 
                         * 1. It will request and save fees data in database from payment transactions
                         * 2. On succcess will generate a comparison report download link 
                         * 3. Download link will loop through all fees database rows and  
                         *    then output the comparison to excel file and upon completion 
                         *    will download the comparison report.
                         */
                        $('.btn-comp-pmt-fees').each(function(){
                            $(this).click(function(){
                                // Clicked link attribute value
                                const fin_event_grp_id    = $(this).attr('fin-event-grp-id'); 
                                const fin_event_grp_start = $(this).attr('fin-event-grp-start'); 
                                const fin_event_grp_end   = $(this).attr('fin-event-grp-end');
                                const fin_event_curr      = $(this).attr('fin-event-curr');
                                const beg_bal_amt         = $(this).attr('beg-bal-amt');
                                const deposit_amt         = $(this).attr('deposit-amt');
                                const fund_trf_date       = $(this).attr('fund-trf-date');
                                const amz_acct_id         = $(this).attr('amz-acct-id');

                                // Request, get and save fees from payments
                                req_pmt_fees(fin_event_grp_id, fin_event_grp_start, fin_event_grp_end, amz_acct_id, fin_event_curr, beg_bal_amt, deposit_amt, fund_trf_date)
                            });
                        });

                    }
                    else {
                        $('#resGetPmts').html(res.message); 
                    }
                }, 
                error: function(xhr)
                {
                    const xhr_text = xhr.status+" "+xhr.statusText;
                    swal({title: "Request error!", text: xhr_text, icon: "error"});
                }
            });
        }); 

        /**
         * Request and save payment fees data
         *
         * @param string    FinEventGrpId       Financial Event Group Id
         * @param date      FinEventGrpStart    Financial Event Group Start Date
         * @param date      FinEventGrpEnd      Financial Event Group End Date
         * @param integer   AmzAcctId           Amazon account id
         * @return void
         */
        function req_pmt_fees(FinEventGrpId, FinEventGrpStart, FinEventGrpEnd, AmzAcctId, FinEventCurr, BegBalAmt, DepositAmt, FundTrfDate)
        {
            // Ajax get request
            $.ajax({
                type: "get", 
                url: "<?php echo base_url('payments/amazon/payments/get_pmt_fees'); ?>", 
                data: "fineventgrpid="+FinEventGrpId+"&fineventgrpstart="+FinEventGrpStart+"&fineventgrpend="+FinEventGrpEnd+"&amzacctid="+AmzAcctId+"&fineventcurr="+FinEventCurr+"&begbalamt="+BegBalAmt+"&depositamt="+DepositAmt+"&fundtrfdate="+FundTrfDate, 
                dataType: "json", 
                beforeSend: function()
                {
                    $('#loader').removeClass("d-none");
                }, 
                success: function(res)
                {
                    if(res.status)
                    {      
                        // Request FBA fees by Next Token
                        if("next_token" in res)
                        {   
                            req_pmt_fees_by_next_token(res.next_token[0], FinEventGrpId, FinEventGrpStart, FinEventGrpEnd, AmzAcctId); 
                        } 
                        else {
                            $('#loader').addClass("d-none");
                            $('#resAmzPmts').html(res.message);
                        }
                    }
                    else {
                        $('#resAmzPmts').html(res.message);
                        $('#loader').addClass("d-none");
                    }
                }, 
                error: function(xhr)
                {
                    var xhr_text = xhr.status+" "+xhr.statusText;
                    swal({title: "Request error!", text: xhr_text, icon: "error"});
                }
            });
        }

        /**
         * Request and save payment fees by Next Token 
         * 
         * @param string    NextToken           Next Token
         * @param string    FinEventGrpId       Financial Event Group Id
         * @param date      FinEventGrpStart    Financial Event Group Start Date
         * @param date      FinEventGrpEnd      Financial Event Group End Date
         * @param integer   AmzAcctId           Amazon account id
         * @return void
         */
        function req_pmt_fees_by_next_token(NextToken, FinEventGrpId, FinEventGrpStart, FinEventGrpEnd, AmzAcctId)
        {   
            $.ajax({
                type: "get", 
                url: "<?php echo base_url('payments/amazon/payments/get_pmt_fees_by_next_token'); ?>", 
                data: "nexttoken="+NextToken+"&fineventgrpid="+FinEventGrpId+"&fineventgrpstart="+FinEventGrpStart+"&fineventgrpend="+FinEventGrpEnd+"&amzacctid="+AmzAcctId,   
                dataType: "json", 
        
                success: function(res)
                {
                    if(res.status)
                    {  
                       // Request FBA Fees by Next Token
                        if("next_token" in res)
                        {
                            req_pmt_fees_by_next_token(res.next_token[0], FinEventGrpId, FinEventGrpStart, FinEventGrpEnd, AmzAcctId);                       
                        }
                        else {
                            $('#loader').addClass("d-none");
                            $('#resAmzPmts').html(res.message); 
                        }
                    }
                    else {
                        $('#resAmzPmts').html(res.message);
                        $('#loader').addClass("d-none");
                    }
                }, 
                error: function(xhr)
                {
                    var xhr_text = xhr.status+" "+xhr.statusText;
                    swal({title: "Request error!", text: xhr_text, icon: "error"});
                }
            });
        }
    }); 
</script>