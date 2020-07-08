<style>
    .bg-overlay {
        position: fixed;
        top: 0; 
        left: 0;
        height: 100%; 
        width: 100%;
        z-index: 10;
        background-color: rgba(255, 255, 255, 0.7);
    }

    .spinner-3 {
        width: 3rem; 
        height: 3rem;
    }
</style>

<div id="loader" class="d-none">
    <div class="d-flex flex-column align-items-center justify-content-center bg-overlay min-vh-90">
        <div class="row">
	       	<div class="spinner-border text-primary spinner-3" role="status"></div>
	    </div>
	    <div class="row">
            <p>Processing...</p>
	    </div>
    </div>
</div>

<!-- <div id="progress-loader" class="dnone bgoverlay min-vh-90">
    <div class="d-flex-flex-column align-items-center justify-content-center  ">
        <div class="progress">
       
            <div class="progress-bar progress-bar-striped bg-success" role="progressbar" style="width: 25%" aria-valuenow="25" aria-valuemin="0" aria-valuemax="100"></div>
        
	    </div>
    </div>
</div> -->

<!-- <div class="bg-overlay">
    <div class="progress">
        <div class="progress-bar progress-bar-striped bg-success" role="progressbar" style="width: 25%" aria-valuenow="25" aria-valuemin="0" aria-valuemax="100"></div>
    </div>
</div> -->


