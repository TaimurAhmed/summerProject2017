<?php 
/**
 * Connection variable args: ..,user,password,db
 */
$con = mysqli_connect("localhost","root","","social"); 
if(mysqli_connect_errno())
{
   echo "Failed to connect driver: ".mysqli_connect_errno();
}

//Declaring variables to prevent errors
$fname = ""; //first name
$lname = "";//last name
$em = ""; //email 1
$em2 = ""; //email 2
$password = ""; //password 
$password2 = "";// password 2
$date = "";// Sign up date
$error_array = "";// Holds error messages

if(isset($_POST['register_button'])){

    //Registration form values
    //First name
    $fname= strip_tags($_POST['reg_fname']); //Strip html tags to prevent HTML injections
    $fname = str_replace(' ', '', $fname); // Remove spaces subject variable
    $fname = ucfirst(strtolower($fname)); //Convert string to lower then capitalise first letter

    //Last name
    $lname = strip_tags($_POST['reg_lname']); //Strip html tags to prevent HTML injections
    $lname = str_replace(' ', '', $lname); // Remove spaces subject variable
    $lname = ucfirst(strtolower($lname)); //Convert string to lower then capitalise first letter

    //email
    $em = strip_tags($_POST['reg_email']); //Strip html tags to prevent HTML injections
    $em = str_replace(' ', '', $em); // Remove spaces subject variable
    $em = ucfirst(strtolower($em)); //Convert string to lower then capitalise first letter

    //email 2
    $em2 = strip_tags($_POST['reg_email2']); //Strip html tags to prevent HTML injections
    $em2 = str_replace(' ', '', $em2); // Remove spaces subject variable
    $em2 = ucfirst(strtolower($em2)); //Convert string to lower then capitalise first letter

    //Passwords, Note: Did not remove white space or case sensitivity (more password options)
    $password = strip_tags($_POST['reg_password']); //Strip html tags to prevent HTML injections
    $password2 = strip_tags($_POST['reg_password2']); //Strip html tags to prevent HTML injections

    //date
    $date = date("Y-m-d");//current date

    if($em == $em2){
        //Check if email is in valid format
        if(filter_var($em,FILTER_VALIDATE_EMAIL)) {

            $em = filter_var($em,FILTER_VALIDATE_EMAIL);// valid email
        }
        else{
            echo "Invalid email format";
        }

    }
    else{
        echo "Emails dont match";

    }

}




?>

<html>
   <head>
      <title></title>
   </head>
   <body>
      <form action="register.php" method="POST">
         <input type="text" name = "reg_fname" placeholder="First Name" required>
         <br>
         <input type="text" name = "reg_lname" placeholder="Last Name" required>
         <br>
         <input type="email" name = "reg_email" placeholder="Email" required>
         <br>
         <input type="email" name = "reg_email2" placeholder="Confirm Email" required>
         <br>
         <input type="password" name = "reg_password" placeholder="Password" required>
         <br>
         <input type="password" name = "reg_password2" placeholder="Confirm password" required>
         <br>
         <input type="submit" name = "register_button" value="Register">
         <br>
      </form>
   </body>
</html>