<style>
.leftnav {
  position: fixed;
  top: 55px;
  height: calc(100vh - 55px);
  bottom: 0;
  left: 0;
  max-width: 230px !important;
  min-width: 230px !important; 
  z-index: 100; /* Behind the navbar */
  padding: 0;
  box-shadow: inset -1px 0 0 rgba(0, 0, 0, .1);
}

.leftnav-sticky {
  position: -webkit-sticky;
  position: sticky;
  padding-top: .5rem;
  overflow-x: hidden;
  overflow-y: auto; 
}

.leftnav .nav-link {
  font-weight: 400;
  color: #333;
}

.leftnav .nav-link:hover .feather,
.leftnav .nav-link.active .feather {
  color: inherit;
}

ul.collapse > li.nav-item > a:hover {
    color: #a9a9a9 !important;  
}
ul.collapse-not-in-use {
    border-left: 4px solid #f48024; 
}
.nav-icon-width {
    width: 30px !important;
}

.font-weight-bold-min {
    -webkit-text-stroke: 0.05em rgba(50, 50, 50, .5);
}

</style>

<nav class="leftnav bg-light">
	<div class="leftnav-sticky">
    	<ul class="nav flex-column">
    		<li class="nav-item">
        		<a class="nav-link pb-0" href="<?php echo base_url('home/dashboard'); ?>">
                    <span class="float-left nav-icon-width"><i class="far fa-tachometer-alt"></i></span>
                    <span class="font-weight-bold fs-14">Dashboard</span>
        		</a>
            </li>
            <!-- 
            NOTES: Will be enabled when development starts
            <li class="nav-item">
        		<a class="nav-link pb-0" href="#subProducts" data-toggle="collapse" aria-expanded="false">
                    <span class="float-left nav-icon-width"><i class="far fa-box-full"></i></span>
                    <span class="font-weight-bold fs-14">Products</span>
                    <span class="float-right"><i class="fas fa-angle-right leftnav-caret"></i></span>
        		</a>
                <ul class="collapse flex-column list-unstyled animated fadeIn" id="subProducts">
                    <li class="nav-item">
                        <a href="<?php echo base_url('products/amazon/fba_prod'); ?>" class="nav-link fs-14 pl-5 pb-0">FBA Products</a>
                    </li>
                </ul>
            </li>
            -->
            <li class="nav-item">
        		<a class="nav-link pb-0" href="#subPricing" data-toggle="collapse" aria-expanded="false">
                    <span class="float-left nav-icon-width"><i class="far fa-tags"></i></span>
                    <span class="font-weight-bold fs-14">Pricing</span>
                    <span class="float-right"><i class="fas fa-angle-right leftnav-caret"></i></span>
        		</a>
                <ul class="collapse flex-column list-unstyled animated fadeIn" id="subPricing">
                    <li class="nav-item">
                        <a href="<?php echo base_url('pricing/fba_fees_calc'); ?>" class="nav-link fs-14 pl-5 pb-0">FBA Fees Calculator</a>
                    </li>
                </ul>
        	</li>
            <li class="nav-item">
        		<a class="nav-link pb-0" href="#subPayments" data-toggle="collapse" aria-expanded="false">
                    <span class="float-left nav-icon-width"><i class="fad fa-credit-card"></i></span>
                    <span class="font-weight-bold fs-14">Payments</span>
                    <span class="float-right"><i class="fas fa-angle-right leftnav-caret"></i></span>
        		</a>
                <ul class="collapse flex-column list-unstyled animated fadeIn" id="subPayments">
                    <li class="nav-item">
                        <a href="<?php echo base_url('payments/amazon/payments'); ?>" class="nav-link fs-14 pl-5 pb-0">Fee Preview</a>
                    </li>
                    <li class="nav-item">
                        <a href="<?php echo base_url('payments/amazon/payments'); ?>" class="nav-link fs-14 pl-5 pb-0">Amazon Payments</a>
                    </li>
                </ul>
        	</li>
    	</ul>
    </div>
</nav>

<script>
    $(document).ready(function(){
        $('.nav-link').each(function(){
            $(this).click(function(){
                $(this).find('.leftnav-caret').toggleClass('fa-angle-right fa-angle-down'); 
            });
        });
    });
</script>