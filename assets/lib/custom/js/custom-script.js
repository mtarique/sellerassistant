// Flatpickr - Datepicker
$(document).ready(function(){
    $('.flatpickr-datepicker').flatpickr();
});

// Shows name of the file in bootstrap custom file input
$(document).ready(function(){
    $(".custom-file-input").on("change", function() {
        //var fileName = $(this).val().split("\\").pop();
        // Get the file name
        var fileName = $(this).val().replace('C:\\fakepath\\', "");
        $(this).next(".custom-file-label").html(fileName);
    });
});

