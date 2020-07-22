<?php 
$this->load->view('templates/header'); 
$this->load->view('templates/topnav'); 
$this->load->view('templates/wrapper'); 
$this->load->view('templates/settings-nav');
$this->load->view('templates/titlebar'); 
$this->load->view('templates/loader'); 
?>

<div class="d-flex flex-row mb-3">
    <a href="<?php echo base_url('settings/channels/amazon/new'); ?>" class="btn btn-sm btn-success shadow-sm"><i class="fas fa-plus"></i> Connect New Account</a>
</div>

<div id="list-amz-accts"></div>

<?php $this->load->view('templates/footer'); ?>

<script>
    $(document).ready(function(){
        $.ajax({
            type: "get", 
            url: "<?php echo base_url('settings/channels/amazon/list_amz_accts'); ?>", 
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
                    $('#list-amz-accts').html(res.message);  
                }
                else {
                    $('#list-amz-accts').html(res.message); 
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

