<?php 
/*Order matters in PHP :'( */
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
   </head>
  
   <body>



   
        <div class = "wrapper">

            <div class="login_box">
                
                <div class="login_header">
                    <h1>TimsiFeed</h1>
                    Log in or Sign Up below !
                </div>
              
                <!-- Sign in form:  For existing users-->
                <div id = "first_form">
                    <form action="register.php" method="POST">
                            <input  type="email" name="log_email" placeholder="Email Address"
                                    value = "<?php 
                                                if(isset($_SESSION['log_email'])){
                                                echo $_SESSION['log_email'];
                                            }?>"
                                    required
                            >
                            <br>
                            <input type="password" name = "log_password" placeholder="Password">
                            <br>
                            <input type="submit" name="login_button" value="Login">
                            <br>
                            <?php
                                if (in_array ("Email or password credentials are incorrect <br>",$error_array)){
                                        echo "Email or password credentials are incorrect <br>";
                                    }
                            ?>
                            <a href="#" id ="signup" class = "signup"> Dont have an account ? Click here to register !</a>
                       </form>
                </div>
              

                <!--Sign up form: For new users-->
                <div id="second_form">
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

                    <?php
                        if(in_array("<span style='color: #14C800;'> Sucesfully created new user ! </span> <br>",$error_array))
                            echo "<span style='color: #14C800;'> Sucesfully created new user ! </span> <br>";
                     ?>

                     <br>
                     <a href="#" id= "signin" class = "signin" >Already have an account ? Sign in here !</a>
                  </form>
                    
                </div>

           </body>
            
            </div>
        </div>
</html>