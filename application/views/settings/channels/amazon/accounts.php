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

                    // Deletes amazon account on delete button click
                    del_amz_acct(); 
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

        /**
         * Deletes amazon account on delete button click
         *
         * @return void
         */
        function del_amz_acct()
        {
            $('.lnk-del-amz-acct').each(function(){
                $(this).click(function(){

                    const amz_acct_id = $(this).attr('amz-acct-id'); 
                    const deleted_row = $(this).closest('tr'); 

                    swal({
                        title: "Confirm delete!", 
                        text: "Are you sure you want to delete this account?", 
                        icon: "warning", 
                        buttons: ["No", "Yes"], 
                        dangerMode: true
                    }).then((is_deleted) => {
                        if(is_deleted) 
                        {
                            $.ajax({
                                type: "get", 
                                url: "<?php echo base_url('settings/channels/amazon/delete'); ?>", 
                                data: "amzacctid="+amz_acct_id, 
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
                                        swal({title: "Deleted!", text: res.message, icon: "success"});    

                                        // Remove deleted row
                                        deleted_row.hide('slow', function(){
                                            deleted_row.remove();
                                        });
                                    }
                                    else swal({title: "Oops!", text: res.message, icon: "error"});
                                }, 
                                error: function(xhr)
                                {
                                    var xhr_text = xhr.status+" "+xhr.statusText;
                                    swal({title: "Request error!", text: xhr_text, icon: "error"});
                                }
                            }); 
                        }
                    }); 
                }); 
            }); 
        }
    }); 
</script>

