$(document).ready(function(){
    /**
     * Activate bootstrap tooltip
     */
    $('[data-toggle="tooltip"]').tooltip();

    /**
     * Activate bootstrap popover
     */
    $('[data-toggle="popover"]').popover();
    // Disable popover on clicking anywhere in body
    $('[data-toggle="popover"]').click(function (e) {
        e.preventDefault();
        $('[data-toggle="popover"]').not(this).popover('hide');
        $(this).popover('toggle');
    });
    $(document).click(function (e) {
        if ($(e.target).parent().find('[data-toggle="popover"]').length > 0) {
            $('[data-toggle="popover"]').popover('hide');
        }
    });  

    /**
     * Initialize wow.js when using animate.js
     */
    //new WOW().init();
});