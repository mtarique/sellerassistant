<?php 
$this->load->view('templates/header'); 
$this->load->view('templates/topnav'); 
$this->load->view('templates/wrapper'); 
$this->load->view('templates/titlebar'); 
$this->load->view('templates/loader'); 
?>

<table class="table table-sm table-hover border small" id="tblPmtsTrans">
    <thead>
        <tr class="bg-grey-100">
            <th class="align-middle text-center">Amazon Order Id</th>
            <th class="align-middle text-center">Order Date</th>
            <th class="align-middle text-left">Marketplace</th>
            <th class="align-middle text-center">Order Item Id</th>
            <th class="align-middle text-center">SKU</th>
            <th class="align-middle text-center">Quantity Shipped</th>
            <th class="align-middle text-left">Amount Description</th>
            <th class="align-middle text-center">Amount</th>
        </tr>
    </thead>
    <tbody></tbody>
</table>

<div id="resLoadMore"></div>

<?php $this->load->view('templates/footer'); ?>

<script>
    $(document).ready(function(){
        /**
         * List financial events 
         * or 
         * Get payments transactions
         */
        $.ajax({
            type: "get", 
            url: "<?php echo base_url('payments/amazon_payments/get_pmt_trans'); ?>", 
            data: "fineventgrpid=<?php echo $fin_event_grp_id; ?>&amzacctid=<?php echo $amz_acct_id; ?>", 
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
                    $('#tblPmtsTrans > tbody').html(res.transactions); 

                    // Jquery datatable
                    const dt_Pmts_trans = $('#tblPmtsTrans').DataTable({
                        /* scrollY: "200px", 
                        "scrollCollapse": true,  */
                        info: false, 
                        paging: false, 
                        fixedHeader: {
                            headerOffset: $('#topnav').outerHeight()
                        },
                        /* rowGroup: {
                            startRender: null, 
                            endRender: function(rows, group) 
                            {
                                return 'Order Id: '+ group;
                            }, 
                            dataSrc: 0
                        }, */ 
                        "drawCallback": function(settings, json)
                        {   
                            
                            if("load_more" in res)
                            {
                                $('#resLoadMore').html(res.load_more);
                                
                                req_pmt_trans_by_next_token();
                            }
                        }
                    }); 

                    
                }
                else {
                    $('#tblPmtsTrans > tbody').html(res.message); 
                }
            }, 
            error: function(xhr)
            {
                var xhr_text = xhr.status+" "+xhr.statusText;
                swal({title: "Request error!", text: xhr_text, icon: "error"});
            }
        }); 

        /**
         * Load more payments transactions by clicing on Load more... button
         * 
         * Load more button link contains next token
         *
         * @return void
         */
        function req_pmt_trans_by_next_token()
        {   
            $('#btnLoadMore').click(function(){
                $.ajax({
                    type: "get", 
                    url: "<?php echo base_url('payments/amazon_payments/get_pmt_trans_by_next_token'); ?>", 
                    data: "nexttoken="+encodeURIComponent($(this).attr('next-token'))+"&amzacctid=<?php echo $amz_acct_id; ?>", 
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
                            $('#tblPmtsTrans > tbody').append(res.transactions); 

                            if("load_more" in res)
                            {
                                $('#resLoadMore').html(res.load_more); 

                                req_pmt_trans_by_next_token(); 
                            }                    
                        }
                        else {
                            $('#tblPmtsTrans > tbody').append(res.message); 
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
    }); 
</script>