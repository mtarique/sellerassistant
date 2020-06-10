<?php 
$this->load->view('templates/header'); 
$this->load->view('templates/topnav'); 
$this->load->view('templates/wrapper'); 
$this->load->view('templates/titlebar'); 
$this->load->view('templates/loader'); 
?>

<ul class="nav flex-column">
    <li class="nav-item">
        <a href="<?php echo base_url('settings/amazon'); ?>" class="nav-link">Amazon MWS Connect</a>
    </li>
    <li class="nav-item">
        <a href="<?php echo base_url('settings/amazon/mws_developers'); ?>" class="nav-link">Amazon MWS Developers</a>
    </li>
    <li class="nav-item disabled">
        <a href="#" class="nav-link">Amazon MWS Endpoints</a>
    </li>
</ul>

<?php $this->load->view('templates/footer'); ?>