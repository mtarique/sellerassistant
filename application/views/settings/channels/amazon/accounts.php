<?php 
$this->load->view('templates/header'); 
$this->load->view('templates/topnav'); 
$this->load->view('templates/wrapper'); 
$this->load->view('templates/settings-nav');
$this->load->view('templates/titlebar'); 
$this->load->view('templates/loader'); 
?>

<a href="<?php echo base_url('settings/channels/amazon/new'); ?>" class="btn btn-sm btn-success shadow-sm"><i class="fas fa-plus"></i> Connect New Account</a>

<?php $this->load->view('templates/footer'); ?>

