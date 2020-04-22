<?php $this->load->view('templates/header'); ?>

<div class="d-flex align-items-center justify-content-center min-vh-85">
	<div class="card bg-cyan-50 text-cyan-700 border-cyan w-75 rounded-0">
		<div class="card-body pt-2 pb-1 text-center">
			<h3 class=""><?php echo $title; ?></h3>
			<p>
				<?php echo $descr; ?><br>
				Developed with <i class="fas fa-heart text-danger"></i> by <a href="mailto: <?php echo $this->config->item('email_developer'); ?>">Muhammad Tarique</a>
			</p>
		</div>
	</div>	
</div>

<?php $this->load->view('templates/footer.php'); ?>