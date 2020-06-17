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
            <th>Report Type</th>
            <th>Report Id</th>
        </tr>
    </thead>
    <tbody></tbody>
</table>
<div id="resShowMore"></div>
<?php $this->load->view('templates/footer'); ?>

<script>
    $(document).ready(function(){
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
                    $('#resShowMore').html(res.load_more);
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
</script>