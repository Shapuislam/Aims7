<?php
    require_once('dbconnect.php');
    session_start();
    if (!isset($_SESSION['user'])) header("Location: ./");
    
    if(isset($_POST['action']) && $_POST['action'] =="Add"){
        $social = $_POST['social'];
        $name = $_POST['name'];
        $mobile = $_POST['mobile'];
        $main_comments = $_POST['main_comments'];
        $unique_field = $_POST['unique_field'];
        $soial_posting_time = $_POST['soial_posting_time'];
        
        $reference_id = $_SESSION['user']['id'];
        $now = date('Y-m-d H:i:s');

        if($social !='' && $mobile !='' && $soial_posting_time !='' && $main_comments !=''){
            $checking_existing_result = mysqli_query($conn, "SELECT id FROM `".$prefix."social_query` WHERE mobile ='".$mobile."' ");
            if (mysqli_num_rows($checking_existing_result) > 0) $err = "This record already exists.";
            else{
                $sql = "INSERT INTO `".$prefix."social_query` (social, name, mobile, main_comments, soial_posting_time, reference_id, unique_field, created_at, updated_at) 
                VALUES ('".$social."', '".$name."', '".$mobile."', '".$main_comments."', '".$soial_posting_time."', '".$reference_id."', '".$unique_field."', '".$now."', '".$now."')";
    
                if (mysqli_query($conn, $sql)) $msg = "Your information added successfully.";
                else $err = "Error: " . $sql . "" . mysqli_error($conn);
            }
        }else{
            $err = "Required field is empty.";
        }
        
        
        $conn->close();
        if($msg!='') $msg = "<div class=\"alert alert-success\" role=\"alert\">".$msg."</div>";
        else if($err!='') $err = "<div class=\"alert alert-danger\" role=\"alert\">".$err."</div>";
    }
?>
<!doctype html>
<html class="no-js" lang=""> 
    <head> 
        <meta charset="utf-8">
        <meta http-equiv="x-ua-compatible" content="ie=edge">
        <title>Dashboard::AIMS7</title>      
        <link rel="shortcut icon" type="image/png" href="img/favicon.png"/>
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.4.1/css/bootstrap.min.css">
        <?php /*
        <script src="assets/js/jquery.min.js"></script>
        <script src="assets/js/bootstrap.min.js"></script>
        */ ?>
    </head>
    <body>
        <div class="container">
        <div style="margin-bottom:20px; margin-top:20px;" class="row col-md-4 col-sm-12">
        <div class="col-md-6"><img src="https://aims7.com/img/logo.png" width="100px;"></div>
        <div class="col-md-6">
            <div class="text-right">
                <a href="logout.php"><button type="submit" class="btn btn-danger btn-sm">Logout</button></a>
            </div>
        </div>
            <div style="margin-top:20px; margin-bottom:20px; margin-left:-15px" class="col-md-12">
                <div style="margin-left:-15px; color:#ff0000; font-size:18px;" class="col-md-12"><strong>SM Interested Customers Entry</strong></div></div>

            <?php
                if(isset($_POST['action']) && $_POST['action'] =="Add"){
                    if($msg !='') echo $msg;
                    else if($err !='') echo $err;
                }
            ?>
            
            
            <form method="post">
                <input type="hidden" name="action" value="Add">
                <input type="hidden" name="unique_field" value="<?php echo date('dmYHis'); ?>">
                <div class="form-group">
                    <label for="social">Requested Media *</label>
                    <select name="social" class="form-control" required>
                        <option value="">Select</option>
                        <option value="Facebook" selected="selected">Facebook</option>
                        <option value="callbyclient">Call By Client</option>
                        <option value="Callbyus">Call By Us</option>
                        <option value="Imo">Imo</option>
                        <option value="Instagram">Instagram</option>
                        <option value="Linkedin">Linkedin</option>
                        <option value="Pinterest">Pinterest</option>
                        <option value="Twitter">Twitter</option>
                        <option value="Whatsapp">Whatsapp</option>
                        <option value="Youtube">Youtube</option>
                        <option value="Other">Other</option>
                    </select>
                </div>
                <div class="form-group">
                  <label for="name">Interested Persons Name</label>
                  <input type="text" name="name" class="form-control">
                </div>
                <div class="form-group">
                  <label for="mobile">Mobile *</label>
                  <input type="tel" name="mobile" class="form-control" required>
                </div>
                <div class="form-group">
                  <label for="time">Requested Time *</label>
                  <input type="text" name="soial_posting_time" class="form-control" required>
                </div>
                <div class="form-group">
                  <label for="main_comments">Remarkable Comments *</label>
                  <textarea name="main_comments" class="form-control" required></textarea>
                </div>
                
                <div class="text-left">
                  <button type="submit" class="btn btn-info btn-lg">Submit</button>
                </div>
            </form>
            </div>
        </div>
        
    </body>
</html>
