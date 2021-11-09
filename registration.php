<?php
/*
    session_start();
    require_once('dbconnect.php');
    
    function fileUpload($fileName,$id){
        if($fileName !='' && $id != ''){
            $err = "";
            $target_dir = "client_uploads/";
            $target_file = $target_dir . basename($_FILES[$fileName]["name"]);
            $uploadOk = 1;
            $imageFileType = strtolower(pathinfo($target_file,PATHINFO_EXTENSION));
            
            $fiveMb = 5242880;
            
            $check = getimagesize($_FILES[$fileName]["tmp_name"]);
            if($check !== false) { // file is image
               if ($_FILES[$fileName]["size"] > $fiveMb) $err = "Sorry, your file is too large. File upload limit is 5MB.";
               else{
                   if($imageFileType != "jpg" && $imageFileType != "png" && $imageFileType != "jpeg" && $imageFileType != "gif" ) $err = "Sorry, only JPG, JPEG, PNG & GIF files are allowed.";
                   else{
                       $uploadedFileName = $fileName.$id.".".$imageFileType;
                       
                       if (move_uploaded_file($_FILES[$fileName]["tmp_name"], $target_dir.$uploadedFileName )) {
                            global $conn;
                            $sql = " UPDATE `o7_client_temp` SET ".$fileName."='".$uploadedFileName."' WHERE id='".$id."'";
                            mysqli_query($conn, $sql);
                        } else $err = "Sorry, there was an error uploading your file.";
                       
                   } // accepted
               } // is file size within 5 mb 
            } else $err.= $fileName." is not an image."; // is file image 
            if($err !='') return $err;
        }
    }
    
    function customer_type(){
        global $conn;
        $sql = " SELECT * FROM  `o7_customer_type` WHERE status='1' ";
        $result = @mysqli_query($conn, $sql);

        if (@$result->num_rows > 0) {
            $output = "<select name='customer_type_id' class='form-control' required><option value=''>Select</option>";
            while($row = $result->fetch_assoc()) {
                $output.="<option value='".$row['id']."'>".$row['name']."</option>";
            }
            $output.="</select>";
            echo $output;
        }
    }
    function fetch_object($query)
    {
        global $conn;
        if($query!='')
        {
            if($result=mysqli_query($conn, $query))
            {
                if($r=mysqli_fetch_array($result))
                return $r[0];
            }
        }
    }
    
    function first($table, $client_id, $input=''){
        global $conn;
        
        $query = "SELECT * FROM ".$table." WHERE ";
    
        if($input != '' AND is_array($input)){
            $i=0;
            foreach($input as $key => $val){
                if($i==0) $query.= " ".$key."='".$val."'";  
                else $query.= " AND ".$key."='".$val."'";  
            }
        } 
        
        $info = $conn->query($query);
        if($info->num_rows > 0){
            if($row = $info->fetch_assoc()) {
                return $row;
            }
        }
         
    }
    function between($beg, $end, $str)
    {
        list($a,$b) = explode($beg, $str);
        list($x,$y) = explode($end, $b);
        return $x;
    }

    
    function sendSmsToClient($name, $sms_to){
        if($name!='' AND $sms_to!=''){
            global $conn;
            $msg = "Dear ".$name."\nThanks for your registration. Your all information is under our review! our executive may be calling you soon. With Thanks AIMS7 ERP";
            
            $no_of_sms_by_content = ceil(strlen($msg) /160);
            $total_mobile = 1;
            $total_sms_by_msg = $total_mobile * $no_of_sms_by_content;
            
            $sms_available_id = fetch_object("SELECT id FROM `o7_sms_available` WHERE client_id='1' AND available >= ".$total_sms_by_msg." ORDER By id ASC");
            $sms_category_id = fetch_object("SELECT sms_category_id FROM `o7_sms_available` WHERE id='".$sms_available_id."' ");
            $sms_vendor_id = fetch_object("SELECT sms_vendor_id FROM `o7_sms_category` WHERE id='".$sms_category_id."' ");
            $type = fetch_object("SELECT type FROM `o7_sms_category` WHERE id='".$sms_category_id."' ");
            
            $senderId = '';
            if($type=='1') $senderId = fetch_object("SELECT sender_id FROM `o7_sender_id_for_client` WHERE client_id='1' AND sms_vendor_id = ".$sms_vendor_id." ");
        
            $sms_vendor = first('o7_sms_vendor', '1', array('id' => $sms_vendor_id));
            
            $vendorName = $sms_vendor['name'];
            $apiUrl = $sms_vendor['api_url'];
            $apiKey = $sms_vendor['api_key'];
            $conf_seperator_start = $sms_vendor['conf_seperator_start'];
            $conf_seperator_end = $sms_vendor['conf_seperator_end'];
            if(trim($senderId) =='') $senderId = $sms_vendor['sender_id'];
            
            
            if($vendorName == '71bulksms'){
                $post_data['api_key'] = $apiKey;
                $post_data['sender_id'] = $senderId;
                
                $post_data['message'] = $msg;
                $post_data['mobile_no'] = $sms_to;
            }
            else if($vendorName == 'mimsms'){
                $post_data['api_key'] = $apiKey;
                $post_data['senderid'] = $senderId;
                $post_data['type'] = 'text';
                $post_data['contacts'] = $sms_to;
                $post_data['msg'] = $msg;
            }
            else if($vendorName == 'mdls'){
                $post_data['api_key'] = $apiKey;
                $post_data['senderid'] = $senderId;
                $post_data['type'] = 'text';
                $post_data['contacts'] = $sms_to;
                $post_data['msg'] = $msg;
            }
            
            foreach ( $post_data as $key => $value) {
                $post_items[] = $key . '=' . $value;
            }
            
            $post_string = implode ('&', $post_items);
            $curl_connection = curl_init($apiUrl);
            
            //set options
            curl_setopt($curl_connection, CURLOPT_CONNECTTIMEOUT, 30);
            curl_setopt($curl_connection, CURLOPT_USERAGENT, "Mozilla/4.0 (compatible; MSIE 6.0; Windows NT 5.1)");
            curl_setopt($curl_connection, CURLOPT_RETURNTRANSFER, true);
            curl_setopt($curl_connection, CURLOPT_SSL_VERIFYPEER, false);
            curl_setopt($curl_connection, CURLOPT_FOLLOWLOCATION, 1);
            curl_setopt($curl_connection, CURLOPT_POSTFIELDS, $post_string);
            
            $result = curl_exec($curl_connection);
            curl_close($curl_connection);
            
            $sms_confirmation_id = '';
            if($conf_seperator_start !='' AND $conf_seperator_end !='') $sms_confirmation_id = between($conf_seperator_start, $conf_seperator_end, $result);
            else if($conf_seperator_start !='' AND $conf_seperator_end =='') $sms_confirmation_id = between($conf_seperator_start, '|', $result.'|');
            else if($conf_seperator_start =='' AND $conf_seperator_end !='') $sms_confirmation_id = between('|', $conf_seperator_end, '|'.$result);
            else if($conf_seperator_start =='' AND $conf_seperator_end =='') $sms_confirmation_id = $result;
            
            $no_of_sms = ceil(strlen($msg) /160);
            
            mysqli_query($conn, "UPDATE `o7_sms_available` SET available = available-".$no_of_sms.", used = used+".$no_of_sms." WHERE id='".$sms_available_id."'");

           $sql = "INSERT INTO `o7_sms_send` (client_id,sms_available_id, to_mobile, from_message, message, sms_type,sms_char, total_sms, total_mobile,total_sms_by_msg, created_at, updated_at,sms_confirmation_id) 
                    VALUES ('1', '".$sms_available_id."', '".$sms_to."', '".$senderId."', '".$msg."', 'probable client', '".strlen($msg)."', '1','1', '".$total_sms_by_msg."', '".date('Y-m-d H:i:s')."', '".date('Y-m-d H:i:s')."', '".$sms_confirmation_id."' )";
            mysqli_query($conn, $sql);
        }
        //End SMS
    }
    
    function sendEmail($responsible_person, $hotline, $email, $password){
        $subject = "Account has been created at AIMS7";
        $message ="<!DOCTYPE html>
                        <html>
                        <head>
                            <meta charset=\"UTF-8\">
                            <style>
                                body,div{font-family:verdana}
                            </style>
                        </head>
                        <body>
                            <h3>Dear ".$responsible_person.",</h3><br>
                            <div>Thanks for your registration with aims7.com Your provided information is under our review, Our one of a salesperson will be calling you soon</div>
                            <br />
                            <div>Here are your account details to login when your account activated!  </div>
                            <br />
                            <div>Your Mobile: ".$hotline."</div>
                            <div>Your User ID: ".$email."</div>
                            <div>Password is: ".$password."</div>
                            <br />
                            <div style=\"font-size:15px;color:#f00;font-weight:bold\">
                                Your User ID and Password will be remaining same for all login panel with our AIMS7 SP app and 
                                our software admin, branch, and staff. 
                            </div>
                            <br />
                            <div>For more information or any other queries, you can call / Message us at Viber / IMO / WhatsApp # 01611123134 or email at cc@aims7.com.</div>
                            <br /><br />
                            <div>
                                With Thanks<br/>
                                AIMS7 ERP Team<br/>
                                website: <a href='https://www.aims7.com'>https://www.aims7.com</a><br/>
                            </div>
                            <div><small>(This is an auto-generated email from our system. Please don't reply the message )</small></div>
                        </body> 
                        </html>";

        $headers = "From: info@aims7.com\r\n";
        $headers .= "Reply-To: info@aims7.com\r\n";
        $headers .= "MIME-Version: 1.0\r\n";
        $headers .= 'Bcc: pmamun@gmail.com, mtaslim@gmail.com' . "\r\n";
        $headers .= "Content-Type: text/html; charset=UTF-8\r\n";
        mail($email, $subject, $message, $headers);
    }
    


            
 
    $result_ct = mysqli_query($conn, " SELECT * FROM  `o7_customer_type` WHERE status='1' ");
    if ($result_ct->num_rows > 0) {
        $customer_type = "<select name='customer_type_id' class='form-control' required><option value=''>Select</option>";
        while($row_ct = $result_ct->fetch_assoc()) {
            $customer_type.="<option value='".$row_ct['id']."'>".$row_ct['name']."</option>";
        }
        $customer_type.="</select>";
    }
    
    
    if(isset($_POST['action']) && $_POST['action'] =="Add"){
        $responsible_person = $_POST['responsible_person'];
        $name = $_POST['name'];
        $hotline = $_POST['hotline'];
        $address = $_POST['address'];
        $email = $_POST['email'];
        $password = $_POST['password'];
        $web = $_POST['web'];
        $logo = $_POST['logo'];
        $signature = $_POST['signature'];
        $total = $_POST['total'];
        $customer_type_id = $_POST['customer_type_id'];
        $package_id = $_POST['package_id'];
        
        
        if($_SESSION["num1"]+$_SESSION["num2"]==$total){
            if($responsible_person !='' && $name !='' && $hotline !='' && $email !='' && $password !=''){
                $checking_existing_result = mysqli_query($conn, "SELECT name FROM `o7_client_temp` WHERE email ='".$email."' ");
                if (mysqli_num_rows($checking_existing_result) > 0) $err = $email." already exists. Please try another.";
                else{
                    $sql = "INSERT INTO `o7_client_temp` (customer_type_id,package_id,name,responsible_person,address,hotline,email,password,web,created_at,updated_at) 
                    VALUES ('".$customer_type_id."', '".$package_id."', '".$name."', '".$responsible_person."', '".$address."', '".$hotline."', '".$email."', '".$password."', '".$web."', date('Y-m-d H:i:s'),date('Y-m-d H:i:s'))";
        
                    if (mysqli_query($conn, $sql)) {
                        sendSmsToClient($name, $hotline);
                        sendEmail($responsible_person, $hotline, $email, $password);
                        
                        $id = $conn->insert_id;
                        if($_FILES['logo']['name'] != "") $err.= fileUpload('logo', $id);
                        if($_FILES['signature']['name'] != "") $err.= fileUpload('signature', $id);
                        $msg = "Your information added successfully. We'll review and if acceptable, we'll contact you.";
                    } else {
                       
                       $err = "Error: " . $sql . "" . mysqli_error($conn);
                    }
                }
                
            }else{
                $err = "Required field is empty.";
            }
        }
        else $err = "Sum of two numbers is not valid.";
        
        
        $conn->close();
        
        if($msg!='') $msg = "<div class=\"alert alert-success\" role=\"alert\">".$msg."</div>";
        else if($err!='') $err = "<div class=\"alert alert-danger\" role=\"alert\">".$err."</div>";
    }
    else{
        $_SESSION["num1"] = mt_rand(1, 9);
        $_SESSION["num2"] = mt_rand(1, 9);
    }    
  */  
?>




<!doctype html>
<html class="no-js" lang=""> 
    <head> 
        <meta charset="utf-8">
        <meta http-equiv="x-ua-compatible" content="ie=edge">
        <title>AIMS7</title>
        <meta name="description" content="">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <link rel="shortcut icon" type="image/png" href="img/favicon.png"/>

        <!-- Place favicon.ico in the root directory -->
        <link href="https://fonts.googleapis.com/css?family=Lato:400,700|Roboto:400,700" rel="stylesheet">
        <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/font-awesome/4.7.0/css/font-awesome.min.css"><!-- 
        <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.0.0/css/bootstrap.min.css"> -->
        <link rel="stylesheet" href="css/swiper.min.css">
        <link  href="https://cdnjs.cloudflare.com/ajax/libs/twitter-bootstrap/3.3.7/css/bootstrap.min.css" rel="stylesheet" media="all">
        <link href="https://cdnjs.cloudflare.com/ajax/libs/animate.css/3.5.2/animate.min.css" rel="stylesheet" media="all">
        <link rel="stylesheet" href="style.css">
         <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.1.1/jquery.min.js"></script>
    </head>
    <body>
        <!-- header Section Start -->
        <nav class="navbar navbar-default sticker silderrespons" role="navigation">
          <div class="container-fluid">
            <!-- Brand and toggle get grouped for better mobile display -->
            <div class="navbar-header">
              <button type="button" class="navbar-toggle collapsed" data-toggle="collapse" data-target="#bs-megadropdown-tabs">
                <span class="sr-only">Toggle navigation</span>
                <span class="icon-bar"></span>
                <span class="icon-bar"></span>
                <span class="icon-bar"></span>
              </button>
              <a class="navbar-brand logo" href="index.html"><img src="img/logo.png" alt=""></a>
              <a class="navbar-brand logonext" href="index.html"><img src="img/miniLogo.png" alt=""></a>
            </div>

            <div class="headertop d-flex">
                <div class="card">
                    <div class="box">
                        <span>
                        <h1><strong><a href="">Get Started</a></strong></h1>
                            <ul>
                                <li><a href="#"><i class="fa fa-facebook" aria-hidden="true"></i></a></li>
                                <li><a href="#"><i class="fa fa-twitter" aria-hidden="true"></i></a></li>
                                <li><a href="#"><i class="fa fa-linkedin" aria-hidden="true"></i></a></li>
                                <li><a href="#"><i class="fa fa-instagram" aria-hidden="true"></i></a></li>
                            </ul>
                        </span>
                    </div>
                </div>
            </div> 

            <!-- Collect the nav links, forms, and other content for toggling -->
            <div class="collapse navbar-collapse headermenu" id="bs-megadropdown-tabs">
                <ul class="nav navbar-nav">

                    <li class="dropdown">
                      <a href="#" class="dropdown-toggle" data-toggle="dropdown">About<span class="fa fa-angle-double-down" style="padding-left: 4px;"></span></a>
                      <ul class="dropdown-menu" role="menu">
                        <li><a href="aboutus.html"><i class="fa fa-chevron-right" style="margin-right: 4px"></i>About AIMS7 ERP</a></li>

                        <li><a href="workingArea.html"><i class="fa fa-chevron-right" style="margin-right: 4px"></i>Working Area</a></li>

                        <li><a href="product.html"><i class="fa fa-chevron-right" style="margin-right: 4px"></i>Portfolio</a></li>
                      </ul>
                    </li>

                    <li class="dropdown">
                      <a href="#" class="dropdown-toggle" data-toggle="dropdown">Softwares<span class="fa fa-angle-double-down" style="padding-left: 4px;"></span></a>
                      <ul class="dropdown-menu" role="menu">
                        <li><a href="version.html"><i class="fa fa-chevron-right" style="margin-right: 4px"></i>AIMS7 ISP</a></li>
                      </ul>
                    </li>

                    <li class="dropdown">
                      <a href="#" class="dropdown-toggle" data-toggle="dropdown">Apps<span class="fa fa-angle-double-down" style="padding-left: 4px;"></span></a>
                      <ul class="dropdown-menu" role="menu">
                        <li><a href="version.html"><i class="fa fa-chevron-right" style="margin-right: 4px"></i>Version</a></li>

                        <li><a href="versiondetails.html"><i class="fa fa-chevron-right" style="margin-right: 4px"></i>Version Details</a></li>
                      </ul>
                    </li>

                    <li><a href="pricing.html">Pricing</a></li>

                    <li class="dropdown">
                      <a href="#" class="dropdown-toggle" data-toggle="dropdown">Support<span class="fa fa-angle-double-down" style="padding-left: 4px;"></span></a>
                      <ul class="dropdown-menu" role="menu">
                        <li><a href="versiondetails.html"><i class="fa fa-chevron-right" style="margin-right: 4px"></i>Documentation</a></li>

                        <li><a href="versiondetails.html"><i class="fa fa-chevron-right" style="margin-right: 4px"></i>Video Tutorials</a></li>

                        <li><a href="faq.html"><i class="fa fa-chevron-right" style="margin-right: 4px"></i>FAQ's</a></li>

                        <li><a href="#ve"><i class="fa fa-chevron-right" style="margin-right: 4px"></i>Support Forum</a></li>
                      </ul>
                    </li>

                    <li><a href="contact.html">Contact</a></li>
                    
                    <li><a href="registration.php">Registration</a></li>

                    <li class="dropdown">
                      <a href="#" class="dropdown-toggle" data-toggle="dropdown">Login<span class="fa fa-angle-double-down" style="padding-left: 4px;"></span></a>
                      <ul class="dropdown-menu" role="menu">
                        <li><a href="http://isp.aims7.com/"><i class="fa fa-chevron-right" style="margin-right: 4px"></i>AIMS7 Login</a></li>
                      </ul>
                    </li>
                </ul>
            </div><!-- /.navbar-collapse -->
          </div><!-- /.container-fluid -->
        </nav>
        <!-- header Section end -->   
         
        <!-- slider Section Start -->       
        <section class="faqaboutslider" style="background-image: url(img/sliderimgall.jpg);">
            <div class="container">
              <div class="row">
                <div class="col-md-7 col-sm-7 col-xs-7">
                    <h1><strong> Contact</strong> AIMS7 </h1>
                    <h2>Consectetur Adipisicing Seddo Eiusmod Tempor Ipsum dolor uidomsn go hamperti.</h2>
                </div>
              </div>             
            </div>
         </section>
        <!-- slider Section end -->
       <!-- body Section start -->
            <section class="contactbody">
                <div class="container"> 
                    <div class="row">
                        <div class="col-md-6 col-sm-12 col-xs-12">
                            <div class="contactop">
                                <h1><strong>Online Registration</strong></h1>
                            </div>

                            <div class="contactmiddle">
                                <h1><strong><a href="#">Register yourself </a></strong></h1>
                                <h2>Or Call Us: +8801750044055</h2>
                            </div>

                            <div class="contactbottom">
                                <h1>Address</h1>
                                <p>Gulfesha Plaza
                                    <br>
                                    12th Floor
                                    <br>
                                    Sahid Sangbadik Selina Parvicn Sarok
                                    <br>
                                    Moghbazar, Dhaka, Bangladesh </p>
                            </div>
                        </div>
                        <div class="col-md-6 col-sm-12 col-xs-12 contactfrom">
                            <?php if($msg!='') echo $msg; ?>
                            <?php if($err!='') echo $err; ?>
                            <form action="registration.php" method="post" enctype="multipart/form-data">
                                <input type="hidden" name="action" value="Add">
                                <div class="col-md-12">
                                    <div class="contactfiled">
                                        <input name="responsible_person" type="text" class="form-control" placeholder="Name*" required>
                                    </div>
                                </div> 
                                <div class="col-md-12">
                                    Interested Software and Apps *
                                    <div class="contactfiled">
                                        <?php echo $customer_type; ?>
                                    </div>
                                </div> 
                                <div class="col-md-12">
                                    <div class="contactfiled">
                                        Package<br>
                                        <select id="package_id" name="package_id" class="form-control">
                                            <option value="">Select Package</option>
                                        </select>
                                    </div>
                                </div>

                                <div class="col-md-12">
                                    <div class="contactfiled">
                                        <input name="name" type="text" class="form-control" placeholder="Company Name *" required>
                                    </div>
                                </div>

                                <div class="col-md-12">
                                    <div class="contactfiled">
                                        <input name="hotline" type="text" class="form-control" placeholder="Mobile Number *" required>
                                    </div>
                                </div>

                                
                                <div class="col-md-12">
                                    <div class="contactfiled">
                                        <textarea name="address" class="form-control"  placeholder="Company Address" rows="4"></textarea>
                                    </div>
                                </div>

                                <div class="col-md-12">
                                    <div class="contactfiled">
                                        <input name="email" type="email" class="form-control" placeholder="Email Address *" required>
                                    </div>
                                </div>

                                <div class="col-md-12">
                                    <div class="contactfiled">
                                        <input name="password" type="text" class="form-control" placeholder="Password *" required>
                                    </div>
                                </div>

                                <div class="col-md-12">
                                    <div class="contactfiled">
                                        <input name="web" type="text" class="form-control" placeholder="Website">
                                        <smal>e.g. http://example.cpm</smal>
                                    </div>
                                </div>

                                <div class="col-md-12">
                                    <div class="contactfiled">
                                        <div class="text-right">Logo</div>
                                        <input name="logo" type="file" class="form-control" placeholder="Logo">
                                    </div>
                                </div>

                                <div class="col-md-12">
                                    <div class="contactfiled">
                                        <div class="text-right">Signature</div>
                                        <input name="signature" type="file" class="form-control" placeholder="Signature">
                                    </div>
                                </div>
                                <div class="col-md-12">
                                    
                                        <div class='col-sm-6'>
                                           <label style='padding:10px'><?php echo $_SESSION["num1"]; ?></label>
                                           <label style='padding:10px'>+</label>
                                           <label style='padding:10px'><?php echo $_SESSION["num2"]; ?></label>
                                           <label style='padding:10px'>=</label>
                                        </div>
                                        <div class='col-sm-6'>
                                            <div class="contactfiled">
                                                <input name="total" type="text" class="form-control"  required>
                                            </div>
                                        </div>
                                </div>
                                

                                <div class="col-md-12">
                                     <div class="contactbutoon">
                                        <button type="submit" class="btn btn-outline-primary">Submit</button>
                                    </div>
                                </div>

                            </form>
                        </div>
                    </div>
                </div>
             </section>
        <!-- body Section End -->


         <!-- footer Section start-->
        <section class="footer_areatop">
            <div class="container">
                <div class="row">
                    <div class="col-lg-4 col-md-12 col-sm-12 col-xs-12">
                        <div class="footercontent text-center">
                            <a href="index.html"><img src="img/logo.png" alt=""></a>
                            <h1><p><i class="fa fa-copyright"></i>2018-20 aims7.com</p></h1>
                        </div>
                    </div>
                    <div class="col-lg-2 col-md-6 col-sm-6 col-xs-6">
                        <div class="footercategory">
                            <h5>AIMS7 ERP</h5>
                            <ul>
                                <li><i class="fa fa-chevron-right"></i><a href="index.html">Home</a></li>
                                <li><i class="fa fa-chevron-right"></i><a href="aboutus.html">About Us</a></li>
                                <li><i class="fa fa-chevron-right"></i><a href="version.html">Versions</a></li>
                                <li><i class="fa fa-chevron-right"></i><a href="pricing.html">Pricing</a></li>
                                <li><i class="fa fa-chevron-right"></i><a href="faq.html">FAQ'<small>s</small></a></li>
                            </ul>
                        </div>
                    </div>
                    <div class="col-lg-2 col-md-6 col-sm-6 col-xs-6">
                        <div class="track">
                            <ul>
                                
                                <li><i class="fa fa-chevron-right"></i><a href="product.html">Portfolio</a></li>
                                <li><i class="fa fa-chevron-right"></i><a href="download.html">Download</a></li>
                                <li><i class="fa fa-chevron-right"></i><a href="contact.html">Contact Us</a></li>
                                <li><i class="fa fa-chevron-right"></i><a href="privacy.html">Privacy Policy</a></li>
                                <li><i class="fa fa-chevron-right"></i><a href="terms.html">Terms of Use</a></li>
                            </ul>
                        </div>
                    </div>
                    <div class="col-lg-2 col-md-6 col-sm-6 col-xs-6">
                        <div class="footercategory">
                            <h5>Follow Us On</h5>
                            <ul>
                                <li><i class="fa fa-facebook-official"></i><a href="#">Facebook</a></li>
                                <li><i class="fa fa-twitter"></i><a href="#">Twitter</a></li>
                                <li><i class="fa fa-linkedin-square"></i><a href="#">Linkedin</a></li>
                                <li><i class="fa fa-instagram"></i><a href="#">Instagram</a></li>
                            </ul>
                        </div>
                    </div> 
                    <div class="col-lg-2 col-md-6 col-sm-6 col-xs-6">
                        <div class="footercategory">
                            <h5>We Support</h5>
                            <ul>
                                <li><i class="fa fa-paypal"></i><a href="#">Paypal</a></li>
                                <li><i class="fa fa-cc-mastercard"></i><a href="#">Mastercard</a></li>
                                <li class="bkash"><a href="#">bkash</a></li>
                                <li class="skrill"><a href=""><img src="img/skrill.png"></a></li>
                            </ul>
                        </div>
                    </div>
                </div>
            </div>
        </section>
        <!-- footer Section End -->
      

        <!-- Return to Top -->
        <a href="javascript:" id="return-to-top"><i class="fa fa-angle-up"></i></a>
        
        <script>
        function package_list(){
            var customer_type_id = $('select[name="customer_type_id"] option:selected').val();
            var url = "package.php?id="+customer_type_id;
            package_id
            $.get(url, function(data, status){
                $("#package_id").html(data);
            });
        }
        
        $('select[name="customer_type_id"]').on('change', function() {
            package_list();
        });
        
        </script>
      
       
        <script src="https://cdnjs.cloudflare.com/ajax/libs/twitter-bootstrap/3.3.7/js/bootstrap.min.js"></script>
        <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery.touchswipe/1.6.18/jquery.touchSwipe.min.js"></script>
        <!-- Bootstrap bootstrap-touch-slider Slider Main JS File -->
        <script src="js/bootstrap-touch-slider.js"></script>
        
        <script type="text/javascript">
            $('#bootstrap-touch-slider').bsTouchSlider();
        </script>
        <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.3.1/jquery.min.js"></script>
        <script src="https://maxcdn.bootstrapcdn.com/bootstrap/4.0.0/js/bootstrap.bundle.min.js"></script>
        <script src="js/plugins.js"></script>
        <script src="https://cdnjs.cloudflare.com/ajax/libs/Swiper/4.3.3/js/swiper.min.js"></script>
        <script src="js/main.js"></script>
    </body>
</html>
