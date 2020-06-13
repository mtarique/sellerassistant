<?php 
$this->load->view('templates/header'); 
$this->load->view('templates/topnav'); 
$this->load->view('templates/wrapper'); 
$this->load->view('templates/titlebar'); 
$this->load->view('templates/loader'); 
?>

<!-- <ul class="nav flex-column">
    <li class="nav-item">
        <a href="<?php echo base_url('settings/amazon'); ?>" class="nav-link">Amazon MWS Connect</a>
    </li>
    <li class="nav-item">
        <a href="<?php echo base_url('settings/amazon/mws_developers'); ?>" class="nav-link">Amazon MWS Developers</a>
    </li>
    <li class="nav-item disabled">
        <a href="#" class="nav-link">Amazon MWS Endpoints</a>
    </li>
</ul> -->

<style>
    a.menu-item:hover {
        background-color: #f6f8fa !important;
    }
</style>
<div class="row">
    <div class="col-md-3">
        <a href="<?php echo base_url('settings/integrations'); ?>" class="card card-link rounded-0 py0 menu-item">
            <div class="card-body">
                <h5 class="card-title text-nowrap my-0">Amazon Integration</h5>
                <p class="card-text small text-secondary">Connect your Amazon MWS account.</p>
            </div>
        </a>
    </div>
</div>

<?php $this->load->view('templates/footer'); ?>