<?php 
$this->load->view('templates/header'); 
$this->load->view('templates/topnav'); 
$this->load->view('templates/wrapper'); 
$this->load->view('templates/titlebar'); 
$this->load->view('templates/loader'); 
?>

<div class="row">
    <div class="col-md-8">
        <table class="table table-sm table-hover">
            <thead>
                <tr>
                    <th>Marketplace</th>
                    <th>Seller Id</th>
                    <th>MWS Authentication</th>
                    <th>AWS Access Key</th>
                    <th>Secret Key</th>
                </tr>
            </thead>
        </table>
    </div>
    <div class="col-md-4">
        <div class="card rounded-0 shadow-sm">
            <div class="card-header">
                Connect Amazon MWS
            </div>
            <div class="card-body">
                <form>
                    <div class="form-row">
                        <div class="form-group col-md-12">
                            <label for="" class="">Marketplace</label>
                            <select name="" id="" class="form-control form-control-sm">
                                <option value="">North America</option>
                            </select>
                        </div>
                    </div>
                    <div class="form-row">
                        <div class="form-group col-md-12">
                            <label for="" class="">Seller Id</label>
                            <input type="text" class="form-control form-control-sm">
                        </div>
                    </div>
                    <div class="form-row">
                        <div class="form-group col-md-12">
                            <label for="" class="">MWS Authentication</label>
                            <input type="text" class="form-control form-control-sm">
                        </div>
                    </div>
                    <div class="form-row">
                        <div class="form-group col-md-12">
                            <label for="" class="">AWS Access Key</label>
                            <input type="text" class="form-control form-control-sm">
                        </div>
                    </div>
                    <div class="form-row">
                        <div class="form-group col-md-12">
                            <label for="" class="">Secret Key</label>
                            <input type="text" class="form-control form-control-sm">
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>



<?php $this->load->view('templates/footer'); ?>