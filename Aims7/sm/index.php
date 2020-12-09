<?php
    require_once('dbconnect.php');
    session_start();
    if (isset($_SESSION['user'])) header("Location: dashboard.php");
    
    if(isset($_POST['action']) && $_POST['action'] =="Login"){
        $user_id = $_POST['user_id'];
        $password = $_POST['password'];
        
        if($user_id !='' && $password !=''){
            $checking_existing_result = first('reference', array('user_id' => $user_id, 'raw_password' => $password));
            if (is_array($checking_existing_result)) {
                $_SESSION['user'] = $checking_existing_result;
                header("Location: dashboard.php");
            }
            else{
                $err = "Your provided information is not match.";
            }
        }else{
            $err = "Both User ID and password fields are required.";
        }
        $conn->close();
        if($err!='') $err = "<div class=\"alert alert-danger\" role=\"alert\">".$err."</div>";
    }
?>
<!doctype html>
<html class="no-js" lang=""> 
    <head> 
        <meta charset="utf-8">
        <meta http-equiv="x-ua-compatible" content="ie=edge">
        <title>Login :: AIMS7</title>      
        <link rel="shortcut icon" type="image/png" href="favicon.png"/>
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <link rel="stylesheet" href="assets/css/bootstrap.min.css">
        <?php /*
        <script src="assets/js/jquery.min.js"></script>
        <script src="assets/js/bootstrap.min.js"></script>
        */ ?>
    </head>
    <body>
        <div class="container" style="margin-top:20px;">
            <?php
                if(isset($_POST['action']) && $_POST['action'] =="Login"){
                    if($err !='') echo $err;
                }
            ?>
            <div class="row">
            <div class="col-md-3 col-sm-12">
            <h4 style="margin-bottom:50px;">AIMS7 ERP Official Login</h4>
            <form method="post">
                <input type="hidden" name="action" value="Login">
                <div class="form-group">
                  <label for="name">User ID *</label>
                  <input type="text" name="user_id" class="form-control">
                </div>
                <div class="form-group">
                  <label for="mobile">Password *</label>
                  <input type="password" name="password" class="form-control">
                </div>
                <div class="text-left">
                  <button type="submit" class="btn btn-primary-lg">Login</button>
                </div>
            </form>
            </div>
            </div>
        </div>
    </body>
</html>
