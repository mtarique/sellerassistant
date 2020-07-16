<style>
    .navbar-toggler, 
    .navbar-toggler:focus,
    .navbar-toggler:active,
    .navbar-toggler-icon:focus {
        outline: none;
        box-shadow: none;
    }
</style>
<nav class="navbar navbar-expand-lg fixed-top navbar-dark" id="topnav" style="background-color: #000000 !important;">
    <div class="container-fluid px-0">
        <a href="#" class="navbar-brand py-0">
            <img src="<?php echo base_url('assets/img/logo/sa_logo_horizontal_blue.jpg'); ?>" class="img-fluid ml-n1" alt="site-logo" width="190">
        </a>

        <button class="navbar-toggler border-0" type="button" data-toggle="collapse" data-target="#navbarText" aria-controls="navbarText" aria-expanded="false" aria-label="Toggle navigation">
            <span class="navbar-toggler-icon border-0"></span>
        </button>

        <div class="collapse navbar-collapse" id="navbarText">
            <ul class="navbar-nav ml-auto">
                <li class="nav-item pr-3">
                    <select name="inputMpIdTop" id="inputMpIdTop" class="custom-select-sm border-secondary rounded-0 bg-dark text-white" required>
                        <?php echo _options(get_amz_accts($this->session->userdata('_userid'))); ?>
                    </select>
                </li>
                <li class="nav-item pt-1 pr-3">
                    <a href="<?php echo base_url('settings/channels/amazon'); ?>" class="text-decoration-none text-nowrap text-white"><i class="fad fa-cog fa-lg"></i> Settings</a>
                </li>
                <li class="nav-item pt-1">
                    <div class="dropdown">
                        <a href="#" id="lnkUserAccount" class="dropdown-toggle text-decoration-none text-nowrap text-white" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                            <i class="fad fa-user-circle fa-lg"></i> <?php echo $this->session->userdata('_username'); ?> 
                        </a>
                        <div class="dropdown-menu dropdown-menu-right rounded-0 mt-2 shadow animated fadeInDown faster" aria-labelledby="lnkUserAccount">
                            <a class="dropdown-item" href="<?php echo base_url('users/logout'); ?>">
                                <i class="fas fa-power-off"></i> Log out
                            </a>
                        </div>
                    </div>
                </li>
            </ul>
        </div>
    </div>
</nav>