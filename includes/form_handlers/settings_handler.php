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
        $message = "Details sucessfully updated!<br><br>";

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





/**Password Handler**/




if(isset($_POST['update_password'])) {
    // To set hash cost. Please refactor when deployed.
    $options = [
    'cost' => 12,
    ];


    $old_password = strip_tags($_POST['old_password']);
    $new_password_1 = strip_tags($_POST['new_password_1']);
    $new_password_2 = strip_tags($_POST['new_password_2']);


    $password_query = "SELECT password FROM users WHERE username= ?";
    if($stmt = mysqli_prepare($con,$password_query)){
        mysqli_stmt_bind_param($stmt, "s",$userLoggedIn);
        mysqli_stmt_execute($stmt);
        mysqli_stmt_bind_result($stmt,$encrypted_password);
        mysqli_stmt_fetch($stmt);
        mysqli_stmt_close($stmt);

        if(! password_verify($old_password,$encrypted_password)){
            $password_message = "Incorrect password old password";
            echo $password_message;
        }else if (strlen($new_password_1) <= 5){
            $password_message = "Password should be longer than 5 characters";
                        echo $password_message;

        }else if ($new_password_1 != $new_password_2){
            $password_message = "New passwords did not match";
                        echo $password_message;

        }else{
            echo "got here";

            $new_password_encrypted = password_hash($new_password_1,PASSWORD_DEFAULT,$options); 
            $update_password_query = "UPDATE users SET password = ? WHERE username=?";
                if($stmt = mysqli_prepare($con,$update_password_query)){
                    mysqli_stmt_bind_param($stmt,"ss",$new_password_encrypted,$userLoggedIn);
                    mysqli_stmt_execute($stmt);
                    mysqli_stmt_close($stmt);
                    $password_message = "Password has been changed !";
                    echo "got here too";
                                echo $password_message;

                }
        }
    }
}






?>