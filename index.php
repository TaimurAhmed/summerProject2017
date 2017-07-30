<?php 
include("./includes/header.php");



if(isset($_POST['post'])){
     $post = new Post($con,$userLoggedIn);
     $post->submitPost($_POST['post_text'],'none');
     /*Consider patch at later point to avoid posting again on refresh?*/
}


?>
    <div 
        class ="user_details column">
        <a 
            aria-label="Personal Wall" role="Personal Wall" title='Personal Wall' href="<?php echo $userLoggedIn; ?>"><img src="
            <?php if(isset($meta_person["profile_pic"])){echo $meta_person["profile_pic"];}?>" 
           alt="">
        </a>

        <div 
            class="user_details_left_right">
            <a 
                aria-label="Personal Wall" 
                role="Personal Wall" 
                title='Personal Wall' 
                href="<?php echo $userLoggedIn; ?>">
                <?php if(isset($meta_person["first_name"])){echo $meta_person["first_name"];}            
                      if(isset($meta_person["last_name"])){echo " " . $meta_person["last_name"];}
                ?>
                
            </a>
            <br>
            <?php if(isset($meta_person["num_posts"])){echo "<div role='Number of Posts by User' title='Number of Posts by User'>Posts:". $meta_person["num_posts"]."</div><br>";}?>
            <?php if(isset($meta_person["num_likes"])){echo "<div role='Number of Posts liked by User' title='Number of Posts liked by User'>Likes:". $meta_person["num_likes"]."</div>";}?>  
        </div>
    </div>

        <div 
            class="main_column column">
            
            <form 
                class="post_form" 
                action="index.php" 
                method="POST">
                <textarea 
                    aria-label="Type Post Here to Share With Friends" 
                    role="Type Post Here to Share With Friends" 
                    title='Type Post Here to Share With Friends' 
                    name="post_text" id="post_text" 
                    placeholder="Got something to say ?">
                </textarea>
                <input 
                    aria-label="lic" 
                    role="Personal Wall" 
                    title='Personal Wall' 
                    type="submit" 
                    name = "post" 
                    id ="post_button" 
                    value = "Post ">
                <hr> 
            </form>

        
            <div class="posts_area"></div>
            <div role='alert' aria-relevant='all' role="No more messages to show" title="No more messages to show" id='#loading'> 
                <i aria-hidden="true" class="fa fa-refresh fa-spin fa-3x fa-fw" ></i>
            </div>

        </div> 

        <script>
            var userLoggedIn = '<?php echo $userLoggedIn; ?>';

            $(document).ready(function() {
                /*Show the newsfeed loading symbol*/
                $('#loading').show();

                /*Ajax request for more posts on news feeds*/ 
                $.ajax({
                    url: "includes/handlers/ajax_load_posts.php",
                    type: "POST",
                    data: "page=1&userLoggedIn=" + userLoggedIn,
                    cache:false,

                    success: function(data) {
                        $('#loading').hide();
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
