<?php 
session_start(); //To start new session or resume old one in case of errors in reg data
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
$error_array = array();// Holds error messages




if(isset($_POST['register_button'])){

    //Registration form values
    //First name
    $fname= strip_tags($_POST['reg_fname']); //Strip html tags to prevent HTML injections
    $fname = str_replace(' ', '', $fname); // Remove spaces subject variable
    $fname = ucfirst(strtolower($fname)); //Convert string to lower then capitalise first letter
    $_SESSION['reg_fname'] = $fname; //Store reg form values in session variable initialised above

    //Last name
    $lname = strip_tags($_POST['reg_lname']); //Strip html tags to prevent HTML injections
    $lname = str_replace(' ', '', $lname); // Remove spaces subject variable
    $lname = ucfirst(strtolower($lname)); //Convert string to lower then capitalise first letter
    $_SESSION['reg_lname'] = $lname; //Store reg form values in session variable initialised above

    //email
    $em = strip_tags($_POST['reg_email']); //Strip html tags to prevent HTML injections
    $em = str_replace(' ', '', $em); // Remove spaces subject variable
    $em = ucfirst(strtolower($em)); //Convert string to lower then capitalise first letter
    $_SESSION['reg_email'] = $em; //Store reg form values in session variable initialised above

    //email 2
    $em2 = strip_tags($_POST['reg_email2']); //Strip html tags to prevent HTML injections
    $em2 = str_replace(' ', '', $em2); // Remove spaces subject variable
    $em2 = ucfirst(strtolower($em2)); //Convert string to lower then capitalise first letter
    $_SESSION['reg_email2'] = $em2; //Store reg form values in session variable initialised above


    //Passwords, Note: Did not remove white space or case sensitivity (more password options)
    $password = strip_tags($_POST['reg_password']); //Strip html tags to prevent HTML injections
    $password2 = strip_tags($_POST['reg_password2']); //Strip html tags to prevent HTML injections

    //date
    $date = date("Y-m-d");//current date

    if($em == $em2){
        //Check if email is in valid format
        if(filter_var($em,FILTER_VALIDATE_EMAIL)) {
            
            // Is email valid ?
            $em = filter_var($em,FILTER_VALIDATE_EMAIL);
            
            //Check if email already exists
            $e_check = mysqli_query($con, "SELECT email from users WHERE email = '$em'");
            
            //Count the number of rows returned inside e_check
            $num_rows = mysqli_num_rows($e_check);

            if($num_rows > 0){
                array_push($error_array,"Email already being used <br>");
            }
        }
        else{
            array_push($error_array,"Invalid email format <br>");
        }
    }
    else{
        array_push($error_array,"Emails dont match <br>");
    }
    
    if(strlen($fname) >25 || strlen($fname) < 2) {
        array_push($error_array,"Your first name must be between 2 and 25 characters <br>");
    }

    if(strlen($lname) >25 || strlen($lname) < 2) {
        array_push($error_array,"Your last name must be between 2 and 25 characters <br>");
    }

    if($password != $password2) {
        array_push($error_array,"Your passwords do not match <br>");
    }
    else{
        if(preg_match('/[^A-Za-z0-9]/', $password)){
            array_push($error_array,"Your password can only contain standard english characters or numbers <br>");
        }
    }

    if(strlen($password) > 30 || strlen($password) < 5) {
            array_push($error_array,"Your password must be between 5 to 30 characters <br>");
    }


    /*If no errors in form*/
    if (empty($error_array)){
        $password = password_hash($something,PASSWORD_DEFAULT);//

    }
        
    
    
}

?>

<html>
   <head>
      <title></title>
   </head>
   <body>
      <form action="register.php" method="POST">

         
         <input type="text" name = "reg_fname" placeholder="First Name" value = "<?php
            if(isset($_SESSION['reg_fname'])){
                echo $_SESSION['reg_fname'];
            }
         ?>" required>
         <br>
         <?php if(in_array("Your first name must be between 2 and 25 characters <br>", $error_array)) echo "Your first name must be between 2 and 25 characters <br>"?>
         

         <input type="text" name = "reg_lname" placeholder="Last Name" value = "<?php
            if(isset($_SESSION['reg_lname'])){
                echo $_SESSION['reg_lname'];
            }
         ?>" required>
         <br>
         <?php if(in_array("Your last name must be between 2 and 25 characters <br>",$error_array)) echo"Your last name must be between 2 and 25 characters <br>"?>


         <input type="email" name = "reg_email" placeholder="Email" value = "<?php
            if(isset($_SESSION['reg_email'])){
                echo $_SESSION['reg_email'];
            }
         ?>" required>
         <br>


         <input type="email" name = "reg_email2" placeholder="Confirm Email" value = "<?php
            if(isset($_SESSION['reg_email2'])){
                echo $_SESSION['reg_email2'];
            }
         ?>" required>
         <br>
         <?php
            if(in_array("Email already being used <br>",$error_array)) echo "Email already being used <br>";
            else if (in_array("Invalid email format <br>",$error_array)) echo "Invalid email format <br>";
            else if (in_array("Emails dont match <br>",$error_array)) echo "Emails dont match <br>";
         ?>


         <input type="password" name = "reg_password" placeholder="Password" required>
         <br>


         <input type="password" name = "reg_password2" placeholder="Confirm password" required>
         <br>
         <?php
            if(in_array("Your passwords do not match <br>",$error_array))
                echo "Your passwords do not match <br>";
            else if (in_array("Your password can only contain standard english characters or numbers <br>",$error_array))
                echo "Your password can only contain standard english characters or numbers <br>";
            else if (in_array("Your password must be between 5 to 30 characters <br>",$error_array))
                echo "Your password must be between 5 to 30 characters <br>";
         ?>


         <input type="submit" name = "register_button" value="Register">
         <br>
      </form>
   </body>
</html>