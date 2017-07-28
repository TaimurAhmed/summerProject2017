/**
 * Purpose: On click toggles b/w registration and sign in form by hiding one of them.
 * Purpose: On click when toggling changes ARIA labels to help screen readers understand that form is hidden or visible
 */
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