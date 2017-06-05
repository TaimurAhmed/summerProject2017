<?php 
/**
 * Connection variable args: ..,user,password,db testing git out
 */
$con = mysqli_connect("localhost","root","","social"); 
if(mysqli_connect_errno())
{
   echo "Failed to connect driver: ".mysqli_connect_errno();
}

/**
 * Sample Query
 */

$query = mysqli_query($con,"INSERT INTO test(name) VALUES('doof')");

?>

<!DOCTYPE html>
<html lang="en">
<head>
   <meta charset="UTF-8">
   <title>Project </title>
</head>
<body>
Hello chimp
</body>
</html
