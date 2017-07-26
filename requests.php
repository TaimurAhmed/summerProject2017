<?php
require './includes/header.php';
?>

<div class="main_column column" id="main_column">
    <h4>Friend Requests</h4>
    <?php
    $count_friend_requests = "SELECT COUNT(id) FROM friend_requests WHERE user_to = ?";
    $result = "";
    if($stmt = mysqli_prepare($con,$count_friend_requests)){
        mysqli_stmt_bind_param($stmt, "s",$userLoggedIn);
        mysqli_stmt_execute($stmt);
        mysqli_stmt_bind_result($stmt,$number_of_reqs);
        mysqli_stmt_fetch($stmt);
        mysqli_stmt_close($stmt);
    }

    $get_friend_requests = "SELECT user_from FROM friend_requests WHERE user_to = ?";
    $n=0;
    $friend_requests = array();
    if($stmt = mysqli_prepare($con,$get_friend_requests)){
        mysqli_stmt_bind_param($stmt, "s",$userLoggedIn);
        mysqli_stmt_execute($stmt);
        mysqli_stmt_bind_result($stmt,$friend_requests[$n]);
        while(mysqli_stmt_fetch($stmt)){
            $n++;
            mysqli_stmt_bind_result($stmt,$friend_requests[$n]);
        }
        mysqli_stmt_close($stmt);
    }

    if($number_of_reqs === 0){
        echo "You have no friend requests ! :(";
    }else{
        for($i=0; $i<$n; $i++){
           $user_from = $friend_requests[$i];
           $user_from_obj = new User($con,$user_from);

           echo $user_from_obj->getFirstandLastName() . " sent you a friend request !";
           $user_from_friend_array = $user_from_obj->getFriendArray();

           if(isset($_POST['accept_request' . $user_from])){
                $add_friend_query = "UPDATE users SET friend_array=CONCAT(friend_array,?) WHERE username = ?";
                if($stmt = mysqli_prepare($con,$add_friend_query)){
                    $temp = $user_from . ",";
                    mysqli_stmt_bind_param($stmt, "ss",$temp,$userLoggedIn);
                    mysqli_stmt_execute($stmt);
                    mysqli_stmt_close($stmt);
                }
                /*Same thing with users swapped so that they both get friend request accepted*/
                $add_friend_query = "UPDATE users SET friend_array=CONCAT(friend_array,?) WHERE username = ?";
                if($stmt = mysqli_prepare($con,$add_friend_query)){
                    $temp = $userLoggedIn . ",";
                    mysqli_stmt_bind_param($stmt, "ss",$temp,$user_from);
                    mysqli_stmt_execute($stmt);
                    mysqli_stmt_close($stmt);
                }

                $delete_friend_request_query = "DELETE from friend_requests WHERE user_to = ? AND user_from = ?";
                if($stmt = mysqli_prepare($con,$delete_friend_request_query)){
                    mysqli_stmt_bind_param($stmt, "ss",$userLoggedIn,$user_from);
                    mysqli_stmt_execute($stmt);
                    mysqli_stmt_close($stmt);
                }
                echo "You are now friends !";
                header("Location:requests.php"); 


           }

           if(isset($_POST['ignore_request' . $user_from])){
                /*Exactly same as above can be refactored to make it dry*/
                $delete_friend_request_query = "DELETE from friend_requests WHERE user_to = ? AND user_from = ?";
                if($stmt = mysqli_prepare($con,$delete_friend_request_query)){
                    mysqli_stmt_bind_param($stmt, "ss",$userLoggedIn,$user_from);
                    mysqli_stmt_execute($stmt);
                    mysqli_stmt_close($stmt);
                }
                echo "Request ignored!";
                header("Location:requests.php"); 
           }
           ?>

           <form action="requests.php" method="POST">
               <input type="submit" name="accept_request<?php echo $user_from ;?>" id="accept_button" value="Accept">
               <input type="submit" name="ignore_request<?php echo $user_from ;?>" id="ignore_button" value="Ignore">
           </form>

           <?php
        }
    }



    ?>
</div>