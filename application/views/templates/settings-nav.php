<nav class="navbar navbar-expand-lg navbar-light bg-light ml-n4 mr-n3 mt-n3 mb-2 py-1 border-bottom">
    <a class="navbar-brand mb-0" href="#"><i class="fad fa-cog"></i> <span class="text-primary">Settings</span></a>
    
    <button class="navbar-toggler border-0" type="button" data-toggle="collapse" data-target="#settings-nav" aria-controls="navbarNav" aria-expanded="false" aria-label="Toggle navigation">
        <span class="navbar-toggler-icon"></span>
    </button>

    <div class="collapse navbar-collapse" id="settings-nav">
        <ul class="navbar-nav pt-1">
            <li class="nav-item">
                <div class="dropdown">
                    <a href="#" id="" class="dropdown-toggle nav-link" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                        Sales Channel
                    </a>
                    <div class="dropdown-menu dropdown-menu-left rounded-0 mt2 shadow-sm animated fadeInDown faster" aria-labelledby="lnkUserAccount">
                        <a class="dropdown-item" href="<?php echo base_url('settings/channels/amazon'); ?>">
                            <i class="fab fa-amazon"></i> Amazon
                        </a>
                    </div>
                </div>
            </li>
            <li class="nav-item sr-only">
                <a class="nav-link" href="#">Features</a>
            </li>            
        </ul>
    </div>
</nav>