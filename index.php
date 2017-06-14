<?php 
include("./includes/header.php");
include("./includes/classes/User.php");
include("./includes/classes/Post.php");


if(isset($_POST['post'])){
     $post = new Post($con,$userLoggedIn);
     $post->submitPost($_POST['post_text'],'none');
     /*Consider patch at later point to avoid posting again on refresh?*/
}


?>
    <div class ="user_details column">
        <a href="<?php echo $userLoggedIn; ?>"><img src="
            <?php if(isset($meta_person["profile_pic"])){echo $meta_person["profile_pic"];}?>" 
           alt="">
        </a>

        <div class="user_details_left_right">
            <a href="<?php echo $userLoggedIn; ?>">
                <?php if(isset($meta_person["first_name"])){echo $meta_person["first_name"];}            
                      if(isset($meta_person["last_name"])){echo " " . $meta_person["last_name"];}
                ?>
                
            </a>
            <br>
            <?php if(isset($meta_person["num_posts"])){echo "Posts:". $meta_person["num_posts"]."<br>";}?>
            <?php if(isset($meta_person["num_likes"])){echo "Likes:". $meta_person["num_likes"];}?>  
        </div>
    </div>

        <div class="main_column column">
            
            <form class="post_form" action="index.php" method="POST">
                <textarea name="post_text" id="post_text" placeholder="Got something to say ?"></textarea>
                <input type="submit" name = "post" id ="post_button" value = "Post ">
                <hr> 
            </form>

        
            <div class="posts_area"></div>
            <div id='#loading'> 
                <i class="fa fa-refresh fa-spin fa-3x fa-fw" ></i>
            </div>

        </div>

        <script>
        var userLoggedIn = '<?php echo $userLoggedIn; ?>';

        $(document).ready(function() {
            /*Show the newsfeed loading symbol*/
            $('#loading').show();

            /*Ajax request for more posts on news feeds*/ 
            $.ajax({
                url: "./includes/handlers/ajax_load_posts.php",
                type: "POST",
                data: "page=1&userLoggedIn=" + userLoggedIn,
                cache:false,

                success: function(data) {
                    $('loading').hide();
                    $('.posts_area').html(data);
                }
            });

            $(window).scroll(function() {
                var height = $('.posts_area').height(); //Height of div containing posts
                var scroll_top = $(this).scrollTop();
                var page = $('.posts_area').find('.nextPage').val();/*Sets hidden inputs field*/ 
                var noMorePosts = $('.posts_area').find('.noMorePosts').val();

                /*If no more posts via Post class is set to true, do no execute*/ 
                if ((document.body.scrollHeight == document.body.scrollTop + window.innerHeight) && noMorePosts == 'false') {
                    
                    $('#loading').show();
                    //alert("Test: I am being called");

                    var ajaxReq = $.ajax({
                        url: "./includes/handlers/ajax_load_posts.php",
                        type: "POST",
                        data: "page=" + page + "&userLoggedIn=" + userLoggedIn,
                        cache:false,

                        success: function(response) {
                            $('.posts_area').find('.nextPage').remove(); //Removes current .nextpage 
                            $('.posts_area').find('.noMorePosts').remove(); //Removes current .nextpage 

                            $('#loading').hide();
                            $('.posts_area').append(response);
                        }
                    });

                } //End if statement

                return false;

            }); //End (window).scroll(function())


        });


        </script>
</div>
</body>
</html>
