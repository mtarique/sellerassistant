<?php $this->load->view('templates/header'); ?>
<?php $this->load->view('templates/loader'); ?>

<div class="d-flex flex-row">
    <div class="d-none d-sm-block col-md-5 bg-light">
        <div class="d-flex align-items-center justify-content-center min-vh-90">
            <img src="<?php echo base_url('assets/img/graphics/graphic1.svg'); ?>" alt="" width="350" class="img-fluid">
        </div>
    </div>
    <div class="col-md-7">
        <h3 class="mt-3">Seller Assistant</h3>
        <div class="d-flex align-items-center justify-content-center min-vh-90">
            <div class="col-md-6">
                <div id="resCreateAccount"></div>
                <form id="formCreateAccount">
                    <h3 class="font-weight-bold">Get more things done with Loggin platform.</h3>	
                    <p>Access to the most powerfull tool in the entire design and web industry.</p>   
                    <div class="form-row">
                        <div class="form-group col-md-12">
                            <label for="inputName" class="sr-only">Name</label>
                            <input type="text" name="inputName" id="inputName" class="form-control form-control-md bg-light border-0" placeholder="Name" required> 
                        </div>
                    </div>
                    <div class="form-row">
                        <div class="form-group col-md-12">
                            <label for="inputEmail" class="sr-only">Email</label>
                            <input type="email" name="inputEmail" id="inputEmail" class="form-control form-control-md bg-light border-0" placeholder="Email" required> 
                        </div>
                    </div>
                    <div class="form-row">
                        <div class="form-group col-md-12">
                            <label for="inputPassword" class="sr-only">Password</label>
                            <input type="password" name="inputPassword" id="inputPassword" class="form-control form-control-md bg-light border-0" placeholder="Password" required> 
                        </div>
                    </div>
                    <div class="form-row">
                        <div class="form-group col-md-6">
                            <button type="submit" id="btnCreateAccount" class="btn btn-sm btn-primary px-4">Create Account</button>
                        </div>
                    </div>
                    <div class="form-row">
                        <div class="form-group col-md-12">
                            Already have an account? <a href="<?php echo base_url('users/login'); ?>" class="text-nowrap text-decoration-none">Login</a>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

</body>
</html>

<script>
    $(document).ready(function(){
        $('#formCreateAccount').submit(function(event){
            event.preventDefault(); 

            $.ajax({
                type: "post", 
                url: "<?php echo base_url('users/register/create_account'); ?>", 
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
                        $('#resCreateAccount').html(res.message); 
                    }
                    else {
                        $('#resCreateAccount').html(res.message); 
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
</script>