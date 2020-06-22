<nav class="navbar fixed-top navbar-dark" id="topnav" style="background-color: #000000 !important;">
    <div class="container-fluid px-0">
        <a href="#" class="navbar-brand py-0">
            <img src="<?php echo base_url('assets/img/logo/sa_logo_horizontal_blue.jpg'); ?>" class="img-fluid ml-n1" alt="site-logo" width="190">
        </a>
        <div class="d-flex flex-row ml-auto">
            <div class="pr-3">
                <a href="<?php echo base_url('settings/home'); ?>" class="text-decoration-none text-nowrap text-white"><i class="fad fa-cog fa-lg"></i> Settings</a>
            </div>
            <div class="dropdown">
                <a href="#" id="lnkUserAccount" class="dropdown-toggle text-decoration-none text-nowrap text-white" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                    <i class="fad fa-user-circle fa-lg"></i> <?php echo $this->session->userdata('_username'); ?> 
                </a>
                <div class="dropdown-menu dropdown-menu-right rounded-0 mt-2 shadow animated fadeInDown faster" aria-labelledby="lnkUserAccount">
                    <!--  <a class="dropdown-item" href="<?php echo base_url('settings/home'); ?>">
                        <i class="fas fa-cog"></i> Settings
                    </a> -->
                    <a class="dropdown-item" href="<?php echo base_url('users/logout'); ?>">
                        <i class="fas fa-power-off"></i> Log out
                    </a>
                </div>
            </div>
        </div>
    </div>
</nav>