<?php

include("includes/header.php");

/*Send me back to where i came from...hopefully?*/
if(isset($_POST['cancel'])) {
    header("Location: settings.php");
}

if(isset($_POST['close_account'])) {
    $close_query = "UPDATE users SET user_closed='yes' WHERE username=?";
    if($stmt = mysqli_prepare($con,$close_query)){
        mysqli_stmt_bind_param($stmt, "s",$userLoggedIn);
        mysqli_stmt_execute($stmt);
        mysqli_stmt_close($stmt);
        session_destroy();
        header("Location: register.php");
    }

}


?>

<div role='alert' aria-relevant='all' class="main_column column" id='closeAccountOptions' aria-labelledby='closeAccountOptions' aria-describedby='pageOptions' >

    <h4>Close Account</h4>
        Are you sure you want to close your account?
        <br>
        <br>
        Closing your account will hide your profile and all your activity from other users.
        <br>
        <br>
        You can re-open your account at any time by simply logging in.
        <br>
        <br>

    <form action="close_account.php" method="POST">

        <input role='button' id='closeAccount' aria-labelledby='closeAccountOptions' aria-describedby='pageOptions' title='Click to close account' type="submit" name="close_account" id="close_account" value="Yes, Close my account!" class="danger settings_submit">

        <input role='button' id='doNotCloseAccount' aria-labelledby='closeAccountOptions' aria-describedby='pageOptions' title='Click to go back to settings page' type="submit" name="cancel" id="update_details" value="No way!" class="success settings_submit">

    </form>

</div>