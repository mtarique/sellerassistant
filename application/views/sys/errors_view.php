<?php $this->load->view('templates/header'); ?>

<div class="d-flex align-items-center justify-content-center min-vh-85">
	<div class="card bg-pink-50 text-pink-700 border-pink w-75 rounded-0">
		<div class="card-body pt-2 pb-1 text-center">
			<h3 class=""><?php echo $title; ?></h3>
			<p><?php echo $descr; ?></p>
		</div>
	</div>	
</div>

<?php $this->load->view('templates/footer'); ?>