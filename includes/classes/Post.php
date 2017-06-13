<?php
class Post{
    private $user_obj;
    private $con;

    public function __construct($con,$user){
        $this->con = $con;
        $this->user_obj = new User($con,$user);
    }

    public function  submitPost($body,$user_to) {
        $body = strip_tags($body); /*Remove HTML tags*/
        $body = mysqli_real_escape_string($this->con,$body);
        $check_empty = preg_replace('/\s+/', '', $body);/*Delete all spaces*/

        /*If string is not empty insert post*/
        if($check_empty != ""){
            /*Current date and time*/
            $date_added = date("Y-m-d H:i:s");
            /*Get userName*/
            $added_by = $this->user_obj->getusername();
            /*If user is not on own profile, user_to is set to 'none'*/
            if($user_to === $added_by){
                $user_to = "none";
            }
                /*Insert post into DB*/
                $create_post_query = "INSERT INTO posts VALUES('',?,?,?,?,'no','no','0')";
                if($stmt = mysqli_prepare($this->con,$create_post_query)){
                    /*Bind parameters for markers, type 's'/string */
                    mysqli_stmt_bind_param($stmt, "ssss",$body,$added_by,$user_to,$date_added);
                    /*Execute query*/
                    mysqli_stmt_execute($stmt);
                    /*The auto-generated surrogate key*/
                    $returned_id = $this->con->insert_id;
                    /*Close prepared stmt*/
                    mysqli_stmt_close($stmt);


                    /*Update Post Count for User*/
                    $num_posts = $this->user_obj->getNumPosts();
                    $num_posts++;
                    $update_user_numPosts_query = "UPDATE users SET num_posts = ? WHERE username = ? ";
                    if($stmt = mysqli_prepare($this->con,$update_user_numPosts_query)){
                        /*Bind parameters for markers, type 's'/string */
                        mysqli_stmt_bind_param($stmt, "ss",$num_posts,$added_by);
                        /*Execute query*/
                        mysqli_stmt_execute($stmt);
                        /*Close prepared stmt*/
                        mysqli_stmt_close($stmt);
                    }
                }
        }
    }
}
?>