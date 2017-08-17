<?php 
/*Note: Order matters in PHP :'( */
require './config/config.php';
require './includes/form_handlers/register_handler.php';
require './includes/form_handlers/login_handler.php';
?>


<html>


<head>
  <title>Welcome to UoB Social Network</title>
  <link rel="stylesheet" type="text/css" href="./assets/css/register_style.css">
  <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.2.1/jquery.min.js"></script>
  <script src="./assets/js/register.js"></script>
  <!--Favicon of UoB logo used purely for demonstration, please replace if deploying-->
  <link rel="icon" href="./assets/favicon/logo-colour.jpg" type="image/ico">
   <!--CSS: Font Awesome CDN-->
   <script src="https://use.fontawesome.com/342380e526.js"></script>
</head>

  
<body> 

<!--Self XSS Warning-->
<?php
    include './selfXSSwarning.php';
?>

<!--Script-->
<?php
    if(isset($_POST['register_button'])){
        echo '
            <script>
                $(document).ready(function(){
                    $("#first_form").hide();
                    $("#second_form").show();
                });
            </script>
        ';
    } 
?>


<div class = "wrapper">
    <div class="login_box">
        <div class="login_header">
            <h1>Artemis</h1>
            Log in or Sign Up below !
        </div>
        <!-- Sign in form:  For existing users-->
        <!--Aria Role:Sign In-->
        <div id = "first_form" role="sign_in" aria-hidden="false">
            <form action="register.php" method="POST">
                    <input  aria-required="true" aria-invalid="false" type="email" name="log_email" placeholder="Email Address"
                            value = "<?php 
                                        if(isset($_SESSION['log_email'])){
                                        echo $_SESSION['log_email'];
                                    }?>"
                            required
                    >
                    <br>
                    <input aria-required="true" aria-invalid="false" type="password" name = "log_password" placeholder="Password">
                    <br>
                    <input aria-label ="Click to submit Login details" title='Click to submit Login details' type="submit" name="login_button" value="Login">
                    <br>
                    <?php
                        if (in_array ("Email or password credentials are incorrect <br>",$error_array)){
                                echo "<div class='reg_errors' role='alert' aria-relevant='all'> Email or password credentials are incorrect !</div> <br>";
                            }
                    ?>
                    <br>
                    <a aria-label ="Click to access registration form instead" title="Click to access registration form instead" href="#" id ="signup" class = "signup"> Dont have an account ? <br>Click here to register !</a>
                    <br>
                    <br>
                    <br> 
                    <!--Contact Dev Team-->
                    <?php include './includes/register_contact.php'; ?>
               </form>
        </div>
      

        <!--Sign up form: For new users-->
        <div id="second_form" aria-hidden="true">
            <form action="register.php" method="POST">

             
             <input type="text" name = "reg_fname" placeholder="First Name" value = "<?php
                if(isset($_SESSION['reg_fname'])){
                    echo $_SESSION['reg_fname'];
                }
             ?>" required>
             <br>
             <?php if(in_array("Your first name must be between 2 and 25 characters <br>", $error_array)) echo "<div class='reg_errors' role='alert' aria-relevant='all'>Your first name must be between 2 and 25 characters</div> <br>"?>
             

             <input type="text" name = "reg_lname" placeholder="Last Name" value = "<?php
                if(isset($_SESSION['reg_lname'])){
                    echo $_SESSION['reg_lname'];
                }
             ?>" required>
             <br>
             <?php if(in_array("Your last name must be between 2 and 25 characters <br>",$error_array)) echo"<div class='reg_errors' role='alert' aria-relevant='all'>Your last name must be between 2 and 25 characters</div> <br>"?>


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
                if(in_array("Email already being used <br>",$error_array)) echo "<div class='reg_errors' role='alert' aria-relevant='all'>Email already being used </div><br>";
                else if (in_array("Invalid email format <br>",$error_array)) echo "<div class='reg_errors' role='alert' aria-relevant='all'>Invalid email format </div><br>";
                else if (in_array("Emails dont match <br>",$error_array)) echo "<div class='reg_errors' role='alert' aria-relevant='all'>Emails dont match </div><br>";
                else if (in_array("Use your UoB email that ends with :". $email_options['valid_email'] . " <br>",$error_array)) echo "<div class='reg_errors' role='alert' aria-relevant='all'>Use your UoB email that ends with :". $email_options['valid_email'] . "</div> <br>" ;
             ?>


             <input type="password" name = "reg_password" placeholder="Password" required>
             <br>


             <input type="password" name = "reg_password2" placeholder="Confirm password" required>
             <br>
             <?php
                if(in_array("Your passwords do not match <br>",$error_array))
                    echo "<div class='reg_errors' role='alert' aria-relevant='all'>Your passwords do not match </div><br>";
                else if (in_array("Your password can only contain standard english characters or numbers <br>",$error_array))
                    echo "<div class='reg_errors' role='alert' aria-relevant='all'>Your password can only contain standard english characters or numbers </div><br>";
                else if (in_array("Your password must be between 5 to 30 characters <br>",$error_array))
                    echo "<div class='reg_errors' role='alert' aria-relevant='all'>Your password must be between 5 to 30 characters </div><br>";
             ?>


             <input type="submit" name = "register_button" value="Register">
             <br>

            <?php
                if(in_array("<span style='color: #14C800;'> Sucesfully created new user ! </span> <br>",$error_array))
                    echo "<div class='reg_success' role='alert' aria-relevant='all'><span style='color: #14C800;'> Sucesfully created new user ! </span> </div><br>";
             ?>

             <br>
             <a aria-label ="Click to access sign in form instead" title="Click to access sign in form instead" href="#" id= "signin" class = "signin" >Already have an account ? Sign in here !</a>
             <br>
             <br>   
             <!--Contact Dev Team-->
             <?php include './includes/register_contact.php'; ?>
          </form>
        </div>
    </div>
</div>

</body>
</html>
