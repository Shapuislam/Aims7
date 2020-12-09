<?php
$database = include( $_SERVER['DOCUMENT_ROOT'] . "/../isp.aims7.com/app/config/database.php" );
$mysql = $database['connections']['mysql'];
$dbhost = $mysql['host'];
$dbuser = $mysql['username'];
$dbpass = $mysql['password'];
$db = $mysql['database'];
$prefix = $mysql['prefix'];
$conn = mysqli_connect($dbhost, $dbuser, $dbpass, $db);
if(! $conn )  die('Could not connect: ' . mysqli_error());
$msg = "";
$err = "";

?>