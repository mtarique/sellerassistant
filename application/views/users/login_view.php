<?php $this->load->view('templates/header'); ?>
<?php $this->load->view('templates/loader'); ?>

<div class="d-flex flex-row">
    <div class="d-none d-sm-block col-md-5 bg-light">
        <div class="d-flex align-items-center justify-content-center min-vh-90">
            <img src="<?php echo base_url('assets/img/graphics/graphic3.png'); ?>" alt="" width="500" class="img-fluid">
        </div>
    </div>
    <div class="col-md-7">
        <div class="d-flex align-items-center justify-content-center min-vh-90">
            <div class="col-md-6">
                <div id="resLogin"></div>
                <form id="formLogin">
                    <img src="<?php echo base_url('assets/img/logo/sa_logo_horizontal_dark.png'); ?>" alt="site-logo" class="ml-n2 mb-1 img-fluid" width="250">	
                    <p>Access to the most powerfull tool in the entire design and web industry.</p>   
                    <div class="form-row">
                        <div class="form-group col-md-12">
                            <label for="inputEmail" class="sr-only">Name</label>
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
                        <div class="form-group col-md-3">
                            <button type="submit" name="btnLogin" id="btnLogin" class="btn btn-sm btn-primary px-4">Login</button>
                        </div>
                        <div class="form-group col-md-9">
                            <a href="" class="text-dark text-nowrap text-decoration-none font-weight-bold small pt-1">Forget password?</a>
                        </div>
                    </div>
                    <div class="form-row">
                        <div class="form-group col-md-12">
                            Don't have an account? <a href="<?php echo base_url('users/register'); ?>" class="text-nowrap text-decoration-none">Register</a>
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
        /**
         * Submit user login form
         */
        $('#formLogin').submit(function(event){
            event.preventDefault(); 

            $.ajax({
                type: "post", 
                url: "<?php echo base_url('users/login/authenticate'); ?>", 
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
                        $('#resLogin').html(res.message); 
                    }
                    else {
                        $('#resLogin').html(res.message); 
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