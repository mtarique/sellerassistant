<?php 
$this->load->view('templates/header'); 
$this->load->view('templates/topnav'); 
$this->load->view('templates/wrapper'); 
$this->load->view('templates/titlebar'); 
$this->load->view('templates/loader'); 
?>

<table class="table table-sm" id="tblAmzPmts">
    <thead>
        <tr>
            <th class="align-middle text-left">Settlement Period</th>
            <th class="align-middle text-center">Deposit Total</th>
            <th class="align-middle text-center">Processing Status</th>
            <th class="align-middle text-center">Fund Transfer Date</th>
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
         * Get list of Amazon payments 
         */
        $.ajax({
            type: "get", 
            url: "<?php echo base_url('payments/amazon/payment_analyzer/get_payments_list'); ?>",
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
                    load_more_payments(); 
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

        /**
         * Load more amazon payments
         */
        function load_more_payments()
        {
            $('#btnLoadMore').click(function(){
                $.ajax({
                    type: "get", 
                    url: "<?php echo base_url('payments/amazon/payment_analyzer/get_payment_list_by_next_token'); ?>",
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

        function view_payment_summary()
        {
            $('.btn-view-summary').each(function(){
                $(this).click(function(){
                    //var fin_grp_id = $(this).attr('financial-group-id'); 

                    $.ajax({
                        type: "get", 
                        url: "<?php echo base_url('payments/amazon/payment_analyzer/view_payment_summary'); ?>",
                        data: "fingrpid="+encodeURIComponent($(this).attr('financial-group-id')),  
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
            }); 
        }
    }); 
</script>