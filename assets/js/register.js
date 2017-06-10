$(document).ready(function() {
    
    //On click , hide login and show registration form
    $("#signup").click(function() {
        /* Act on the event */
        $("#first_form").slideUp("slow",function(){
            $("#second_form").slideDown("slow");
        });
    });

    //On click , hide login and show registration form
    $("#signin").click(function() {
        /* Act on the event */
        $("#second_form").slideUp("slow",function(){
            $("#first_form").slideDown("slow");
        });
    });



});