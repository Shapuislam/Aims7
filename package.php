<?php
    session_start();
    require_once('dbconnect.php');
    function fetch_rows($query){
        global $conn,$prefix;

        if(trim($query)!='')
    	{
    		$info= $conn->query($query);
    		if($info->num_rows > 0){
        		while($r = $info->fetch_array())
        		{
        			$result[] = $r;
        		}
        		return isset($result) ?  $result : false;
    		}
    	}
    	else
    	{
    		return false;
    	}
    }
    
    $customer_type_id = $_GET['id'];
    if($customer_type_id !=''){
        $options="<option value=''>Select Package</option>";
        $list = fetch_rows("SELECT * FROM `".$prefix."client_package` WHERE customer_type_id='".$customer_type_id."' AND name NOT LIKE '%Service%' ");
        foreach($list as $data){
            $options.="<option value='".$data['id']."'>".$data['name']." (BDT ".$data['package_price']."/=)</option>";
        }
        echo $options;
    }
    
    
	

    
   
?>