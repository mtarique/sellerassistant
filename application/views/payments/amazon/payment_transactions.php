<?php 
$this->load->view('templates/header'); 
$this->load->view('templates/topnav'); 
$this->load->view('templates/wrapper'); 
$this->load->view('templates/titlebar'); 
$this->load->view('templates/loader'); 
?>

<table class="table table-sm" id="tblPmtsTrans">
    <thead>
        <tr>
            <th class="align-middle text-left">Amazon Order Id</th>
            <th class="align-middle text-center">Order Date</th>
            <th class="align-middle text-center">Marketplace</th>
            <th class="align-middle text-center">SKU</th>
            <th class="align-middle text-center">Quantity Shipped</th>
            <th class="align-middle text-center">Amount Description</th>
            <th class="align-middle text-center">Amount</th>
        </tr>
    </thead>
    <tbody>

    </tbody>
</table>

<?php $this->load->view('templates/footer'); ?>

<script>
    $(document).ready(function(){
        /**
         * List financial events
         */
        $.ajax({
            type: "get", 
            url: "<?php echo base_url('payments/amazon/payment_analyzer/get_transactions'); ?>", 
            data: "fingroupid=<?php echo $fin_group_id; ?>", 
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
                    //$('#resLoadMore').html(res.load_more);
                    //load_more_payments(); 
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
    }); 
</script>