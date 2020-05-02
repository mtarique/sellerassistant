<?php $this->load->view('templates/header'); ?>
<?php $this->load->view('templates/loader'); ?>

<div class="container">
    <h3>Hello, <?php echo $this->session->userdata('_username'); ?></h3>
    <p>Welcome to your brand new dashboard.</p>
    <a href="<?php echo base_url('users/logout'); ?>" class="btn btn-sm btn-outline-primary">Log out</a>
</div>

<?php $this->load->view('templates/footer'); ?>