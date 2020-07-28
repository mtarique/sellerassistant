<?php 
$this->load->view('templates/header'); 
$this->load->view('templates/topnav'); 
$this->load->view('templates/wrapper'); 
$this->load->view('templates/titlebar'); 
$this->load->view('templates/loader'); 
?>

<div class="card bg-light border-0 rounded-0 mb-3">
    <div class="card-body">
        <h5 class="card-title">Amazon Account</h5>
        <form id="formPrevFees" class="form-inline">
            <label class="mr-2 sr-only" for="txtAmzAcctId">Amazon Account</label>
            <select name="txtAmzAcctId" id="txtAmzAcctId" class="custom-select custom-select-sm rounded-0 w-25 mr-2" required>
                <?php echo _options_amz_accts($this->session->userdata('_userid')); ?>
            </select>
            <button type="submit" name="btnPrevFees" id="btnPrevFees"class="btn btn-sm btn-primary rounded-0 shadowsm">Preview Fees</button>
        </form>
    </div>
</div>

<div id="resPrevFees"></div>     

<?php $this->load->view('templates/footer'); ?>

<script type="text/javascript">
    $(document).ready(function(){
        
        // Submit preview fba estimated fees form
        $('#formPrevFees').submit(function(event){

            event.preventDefault(); 

            $.ajax({
                type: "post", 
                url: "<?php echo base_url('payments/amazon/fees/get_done_reports');  ?>", 
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
                    //JSON.parse(res)
                    if(res.status)
                    {
                        $('#resPrevFees').html(res.message); 
                    }
                    else {
                        if(res.message == "REQUEST_REPORT")
                        {
                            swal({title: "Oops!", text: res.text, icon: "error"});
                        }
                        else {
                            $('#resPrevFees').html(res.message); 
                        }
                    }
                }, 
                error: function(xhr)
                {
                    const xhr_text = xhr.status+" "+xhr.statusText;
                    swal({title: "Request error!", text: xhr_text, icon: "error"});
                }
            }); 
        }); 
    });
</script>