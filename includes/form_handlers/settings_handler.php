<?php
if(isset($_POST['update_details'])) {

    $first_name = $_POST['first_name'];
    $last_name = $_POST['last_name'];
    $email = $_POST['email'];

    $email_check_query = "SELECT username FROM users WHERE email=?";
    if($stmt = mysqli_prepare($con,$email_check_query)){
        mysqli_stmt_bind_param($stmt, "s",$email);
        mysqli_stmt_execute($stmt);
        mysqli_stmt_bind_result($stmt,$matched_user);
        mysqli_stmt_fetch($stmt);
        mysqli_stmt_close($stmt);
    }

    /*i) If email is not already used. ii) If it belongs to the person who is logged in */
    if($matched_user == "" || $matched_user == $userLoggedIn) {
        $message = "Details updated!<br><br>";

        $update_meta = "UPDATE users SET first_name=?, last_name=?, email=? WHERE username=?";
        if($stmt = mysqli_prepare($con,$update_meta)){
            mysqli_stmt_bind_param($stmt, "ssss",$first_name,$last_name,$email,$userLoggedIn);
            mysqli_stmt_execute($stmt);
            mysqli_stmt_close($stmt);
        }else{
            "Update failed. Try again later or report problem to admin";
        }    
    }else{ 
        $message = "That email is already in use!<br><br>";
    }

/*If nothing is set*/
}else{
        $message = "";
}





?>