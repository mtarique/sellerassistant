<?php $this->load->view('templates/header'); ?>

<div class="ui container">
	<div class="ui middle aligned center aligned grid" style="height: 100vh;">
		<div class="column">
			<div class="ui info message br-0">
				<h2 class="ui"><?php echo $title; ?></h2>
				<p><?php echo $descr; ?></p>
			</div>
		</div>
	</div>		
</div>

<?php $this->load->view('templates/footer.php'); ?>