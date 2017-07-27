/*The script for making form with id 'signup and signin' hide and unhide on registration page*/
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

    /*WAI-ARIA: Hide first form from screen reader on click and reveal second form*/
    $("#signup").click(function(){
        $("#first_form").attr("aria-hidden", "true");
        $("#second_form").attr("aria-hidden", "false");
    });

    /*WAI-ARIA: Hide second form from screen reader on click and reveal first form*/
    $("#signin").click(function(){
        $("#second_form").attr("aria-hidden", "true");
        $("#first_form").attr("aria-hidden", "false");
    });

});