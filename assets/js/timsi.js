//Executed when page loads
$(document).ready(function() {
    //Search Bar in header will expand on click
    $('#search_text_input').focus(function() {
        if(window.matchMedia( "(min-width: 800px)" ).matches) { //if window 800px or larger expand
            $(this).animate({width: '250px'}, 500);
        }
    });

    //Clicking searchbar icon will make it submit form
    $('.button_holder').on('click', function() {
        document.search_form.submit();
    })
    

    /*Button for profile posts*/
    $('#submit_profile_post').click(function(){
        
        $.ajax({
            type: "POST",
            url: "includes/handlers/ajax_submit_profile_post.php",
            data: $('form.profile_post').serialize(),
            success: function(msg) {
                $("#post_form").modal('hide');
                location.reload();
            },
            error: function() {
                alert('Failure');
            }
        });

    });


});

/*Send request to page and when it returns set value of div with returned data (set in messages.php)*/
function getUsers(value, user) {
    $.post("includes/handlers/ajax_friend_search.php", {query:value, userLoggedIn:user}, function(data) {
        $(".results").html(data);
    });
}

/*Nav Bar Drop down in header*/
function getDropdownData(user, type) {
    console.log("FIRST");
    if($(".dropdown_data_window").css("height") == "0px") { /*If div height is zero*/
        console.log("I got here");

        var pageName;

        if(type == 'notification') {
            /* Do something */
            pageName = "ajax_load_notification.php";
            $("span").remove("#unread_notification");

        }
        else if (type == 'message') {
            pageName = "ajax_load_messages.php";
            $("span").remove("#unread_message");
        }

        var ajaxreq = $.ajax({
            url: "includes/handlers/" + pageName,
            type: "POST",
            data: "page=1&userLoggedIn=" + user,
            cache: false,

            success: function(response) {
                $(".dropdown_data_window").html(response);
                $(".dropdown_data_window").css({"padding" : "0px", "height": "212px", "border" : "1px solid #DADADA"});
                $("#dropdown_data_window").val(type);
            }

        });

    }
    else { /*Change div height for next time and close*/
        $(".dropdown_data_window").html("");
        $(".dropdown_data_window").css({"padding" : "0px", "height": "0px", "border" : "none"});
    }
}



function getLiveSearchUsers(value, user) {

    $.post("includes/handlers/ajax_search.php", {query:value, userLoggedIn: user}, function(data) {

        if($(".search_results_footer_empty")[0]) {
            $(".search_results_footer_empty").toggleClass("search_results_footer");
            $(".search_results_footer_empty").toggleClass("search_results_footer_empty");
        }

        $('.search_results').html(data);
        $('.search_results_footer').html("<a href='search.php?q=" + value + "'>See All Results</a>");

        if(data == "") {
            $('.search_results_footer').html("");
            $('.search_results_footer').toggleClass("search_results_footer_empty");
            $('.search_results_footer').toggleClass("search_results_footer");
        }

    });

}