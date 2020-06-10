<?php 
$this->load->view('templates/header'); 
$this->load->view('templates/topnav'); 
$this->load->view('templates/wrapper'); 
$this->load->view('templates/titlebar'); 
$this->load->view('templates/loader'); 
?>

<div class="row">
    <div class="col-md-4">
        <div class="card rounded-0 shadow-sm bg-primary text-white">
            <div class="card-body">
                <h3 class="card-title text-right">0</h3>
                <p class="text-right">SKU's with excess fulfillment fees</p>
            </div>
        </div>
    </div>
</div>

<?php $this->load->view('templates/footer'); ?>