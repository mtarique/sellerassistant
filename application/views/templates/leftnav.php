<style>
.leftnav {
  position: fixed;
  top: 55px;
  height: calc(100vh - 55px);
  bottom: 0;
  left: 0;
  max-width: 220px !important;
  min-width: 220px !important; 
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
    color: #81d4fa !important;  
}
</style>

<nav class="leftnav bg-light">
	<div class="leftnav-sticky">
    	<ul class="nav flex-column">
    		<li class="nav-item">
        		<a class="nav-link pb-0" href="<?php echo base_url('home/dashboard'); ?>">
                    <span><i class="far fa-home"></i></span>
                    <span class="pl-2">Dashboard</span>
        		</a>
        	</li>
        	<li class="nav-item">
        		<a class="nav-link pb-0" href="#subPricing" data-toggle="collapse" aria-expanded="false">
                    <span class="float-left"><i class="far fa-wallet"></i></span>
                    <span class="pl-3">Pricing</span>
                    <span class="float-right"><i class="fas fa-angle-right leftnav-caret"></i></span>
        		</a>
                <ul class="collapse flex-column list-unstyled animated fadeIn" id="subPricing">
                    <li class="nav-item">
                        <a href="<?php echo base_url('pricing/fba_fees_calc'); ?>" class="nav-link pl-5 pb-0">FBA Fees Calculator</a>
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