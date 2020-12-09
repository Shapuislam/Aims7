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

function first($table, $input=''){
    global $conn,$prefix;
    
    $query = "SELECT * FROM `".$prefix.$table."` WHERE ";

    if($input != '' AND is_array($input)){
        $i=0;
        foreach($input as $key => $val){
            if($i==0) $query.= " ".$key."='".$val."'";  
            else $query.= " AND ".$key."='".$val."'";  
            $i++;
        }
    } 

    $info = $conn->query($query);
    if($info->num_rows > 0){
        if($row = $info->fetch_assoc()) {
            return $row;
        }
    }
     
}
?>