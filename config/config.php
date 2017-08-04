<?php
/*
See:

OWASP PHP (Session Hijacking):https://www.owasp.org/index.php/PHP_Security_Cheat_Sheet#Session_Expiration
Reference of implementation: https://www.youtube.com/watch?v=KnX0p2Ey3Ek

*/

    /*Turns on output buffering*/
    ob_start(); 
    /*Set: Cookie expiration/lifetime, cookie works on all domain paths, cookie domains, no SSL, http only i.e. to prevent JS injection*/
    /*Reccomended: Consider using an SSL protocol to prevent session hijacking. In that case set parameter to true instead*/
    session_set_cookie_params(time()+30,'/','localhost',false,true);
    session_start();
    /*Set a random session variable. My alternative to binding to IP, as this would cause problems on open Wifi*/
    $_SESSION["ses_var"] = "green";
    $time_zone = date_default_timezone_set("Europe/London");
    $con = mysqli_connect("localhost","root","","social"); /*Connection Variable*/
    if(mysqli_connect_errno()){
       echo "Connection failed: ". mysqli_connect_errno();
       exit();
    }
    $con->set_charset('utf8'); //Check API !!!!! Many new attack vectors rely on encoding bypassing. Use UTF-8 as your database and application charset unless you have a mandatory requirement to use another encoding

?>