<?php
    require_once('dbconnect.php');
    session_start();
    if (isset($_SESSION['user'])) {
        session_unset(); // remove all session variables
        session_destroy();  // destroy the session
    }
    header("Location: ./");
?>