<nav class="navbar fixed-top navbar-dark bg-dark">
    <div class="container-fluid px-0">
        <a href="#" class="navbar-brand font-weight-bold text-light-blue-200">Seller Assistant</a>
        <div class="d-flex flex-row ml-auto">
            <div class="pr-3">
                <a href="#" class="text-decoration-none text-nowrap text-white"><i class="fas fa-bell fa-lg"></i></a>
            </div>
            <div class="dropdown">
                <a href="#" id="lnkUserAccount" class="dropdown-toggle text-decoration-none text-nowrap text-white" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                   <?php echo $this->session->userdata('_username'); ?> 
                </a>
                <div class="dropdown-menu dropdown-menu-right rounded-0 mt-2 shadow animated fadeInDown faster" aria-labelledby="lnkUserAccount">
                    <a class="dropdown-item" href="<?php echo base_url('settings/home'); ?>">
                        <i class="fas fa-cog"></i> Settings
                    </a>
                    <a class="dropdown-item" href="<?php echo base_url('users/logout'); ?>">
                        <i class="fas fa-power-off"></i> Log out
                    </a>
                </div>
            </div>
        </div>
    </div>
</nav>