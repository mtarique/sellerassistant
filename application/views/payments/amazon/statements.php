<?php 
$this->load->view('templates/header'); 
$this->load->view('templates/topnav'); 
$this->load->view('templates/wrapper'); 
$this->load->view('templates/titlebar'); 
$this->load->view('templates/loader'); 
?>


<div id="res1"></div>

<style>
      .form-control-xs, .custom-select-xs {
            height: calc(1em + .375rem + 2px) !important;
            padding: .125rem .125rem !important;
            font-size: .75rem !important;
            line-height: 0.5;
            border-radius: .2rem;
        }
</style>

<div class="card bg-light rounded-0 mb-3">
    <div class="card-body py-2">
        <form class="form-inline" id="formViewPmts">
            <label class="mr-2" for="inputAmzAcctId">Amazon Account: </label>
            <select name="inputAmzAcctId" id="inputAmzAcctId" class="custom-select custom-select-sm rounded-0 mr-2" required>
                <?php echo _options(get_amz_accts($this->session->userdata('_userid'))); ?>
            </select>

            <label for="inputPmtDateFm" class="mr-2">From: </label>
            <input type="text" name="inputPmtDateFm" id="inputPmtDateFm" class="form-control form-control-sm rounded-0 flatpickr-datepicker mr-2" placeholder="Select from date..." required>

            <label for="inputPmtDateTo" class="mr-2">To: </label>
            <input type="text" name="inputPmtDateTo" id="inputPmtDateTo" class="form-control form-control-sm rounded-0 flatpickr-datepicker mr-2" placeholder="Select to date..." required>

            <button type="submit" name="btnViewPmts" id="btnViewPmts"class="btn btn-sm btn-primary shadow-sm">View Payments</button>
        </form>
    </div>
</div>

<div id="resAmzPmts"></div>

<table class="table table-hover table-sm border small" id="tblAmzPmts">
    <thead>
        <tr class="bg-grey-100">
            <th class="align-middle text-left">Settlement Period</th>
            <th class="align-middle text-center">Deposit Total</th>
            <th class="align-middle text-center">Fund Transfer Date</th>
            <th class="align-middle text-left">Processing Status</th>
            <th class="align-middle text-center"></th>
        </tr>
    </thead>
    <tbody></tbody>
</table>

<div id="resLoadMore"></div>

<?php $this->load->view('templates/footer'); ?>

<script>
    $(document).ready(function(){

        /**
         * Get list of Amazon settlement report or payments
         */
        $('#formViewPmts').submit(function(event){
            event.preventDefault(); 

            $.ajax({
                type: "post",
                url:  "<?php echo base_url('payments/amazon_payments/view_payments'); ?>", 
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
                        $('#tblAmzPmts > tbody').html(res.report_list); 
                        $('#resLoadMore').html(res.load_more);
                        //load_more_payments(); 
                        comp_fba_fees(); 
                        /* $.when(comp_fba_fees()).done(function(){
                            $.each(json_res, function(key, value){
                                $('#resAmzPmts').text(JSON.stringify(value), null, ''); 
                            });
                        }); */ 
                    }
                    else {
                        $('#tblAmzPmts > tbody').html(res.message); 
                    }
                }, 
                error: function(xhr)
                {
                    var xhr_text = xhr.status+" "+xhr.statusText;
                    swal({title: "Request error!", text: xhr_text, icon: "error"});
                }
            });
        });

        /**
         * Load more amazon payments
         */
        function load_more_payments()
        {
            $('#btnLoadMore').click(function(){
                $.ajax({
                    type: "get", 
                    url: "<?php echo base_url('payments/amazon_payments/get_payments_by_next_token'); ?>",
                    data: "nexttoken="+encodeURIComponent($(this).attr('next-token')),  
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
                            $('#tblAmzPmts > tbody').append(res.report_list); 
                            $('#resLoadMore').html(res.load_more);
                            load_more_payments(); 
                        }
                        else {
                            $('#tblAmzPmts > tbody').append(res.message); 
                        }
                    }, 
                    error: function(xhr)
                    {
                        var xhr_text = xhr.status+" "+xhr.statusText;
                        swal({title: "Request error!", text: xhr_text, icon: "error"});
                    }
                }); 
            }); 
        }

        /**
         * Compare FBA Fees
         *
         * @return void
         */
        function comp_fba_fees()
        {
            req_fba_fees(); 
        }

        /**
         * Request and fetch FBA fees data
         *
         * @return void
         */
        function req_fba_fees()
        {
            $('.btn-comp-fba-fees').each(function(){
                $(this).click(function(){
                    // Clicked link attribute value
                    const fin_event_grp_id    = $(this).attr('fin-event-grp-id'); 
                    const fin_event_grp_start = $(this).attr('fin-event-grp-start'); 
                    const fin_event_grp_end   = $(this).attr('fin-event-grp-end');
                    const amz_acct_id         = $(this).attr('amz-acct-id'); 

                    $.ajax({
                        type: "get", 
                        url: "<?php echo base_url('payments/amazon_payments/fetch_fba_fees'); ?>", 
                        data: "fineventgrpid="+fin_event_grp_id+"&fineventgrpstart="+fin_event_grp_start+"&fineventgrpend="+fin_event_grp_end+"&amzacctid="+amz_acct_id, 
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
                                    req_fba_fees_by_next_token(res.next_token[0], fin_event_grp_id, fin_event_grp_start, fin_event_grp_end, amz_acct_id); 
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
                }); 
            });
        }

        /**
         * Request and fetch FBA Fees by Next Token 
         * 
         * @param string    NextToken           Next Token
         * @param string    FinEventGrpId       Financial Event Group Id
         * @param date      FinEventGrpStart    Financial Event Group Start Date
         * @param date      FinEventGrpEnd      Financial Event Group End Date
         */
        function req_fba_fees_by_next_token(NextToken, FinEventGrpId, FinEventGrpStart, FinEventGrpEnd, AmzAcctId)
        {   
            $.ajax({
                type: "get", 
                url: "<?php echo base_url('payments/amazon_payments/fetch_fba_fees_by_next_token'); ?>", 
                data: "nexttoken="+NextToken+"&fineventgrpid="+FinEventGrpId+"&fineventgrpstart="+FinEventGrpStart+"&fineventgrpend="+FinEventGrpEnd+"&amzacctid="+AmzAcctId,   
                dataType: "json", 
        
                success: function(res)
                {
                    if(res.status)
                    {  
                       // Request FBA Fees by Next Token
                        if("next_token" in res)
                        {
                            req_fba_fees_by_next_token(res.next_token[0], FinEventGrpId, FinEventGrpStart, FinEventGrpEnd, AmzAcctId);                       
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