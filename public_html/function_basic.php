<?php

$connect_mysqli = mysqli_connect('Localhost','id4017829_0','0912226204','id4017829_0');
$connect_mysqli->set_charset('utf8');
if(mysqli_connect_errno()){
    echo 'Connect Failed: '.mysqli_connect_error();
    exit();
}

function time_now()
{
	date_default_timezone_set('Asia/Ho_Chi_Minh');
	return date("Y-m-j H-i-s");
} 
	

function start_xls()
{  
		header("Pragma: public"); 
		header("Expires: 0"); 
		header("Cache-Control: must-revalidate, post-check=0, pre-check=0"); 
		header("Content-Type: application/force-download"); 
		header("Content-Type: application/octet-stream"); 
		header("Content-Type: application/download");; 
		header("Content-Disposition: attachment;filename=export.xls "); 
		header("Content-Transfer-Encoding: binary ");
		echo pack("ssssss", 0x809, 0x8, 0x0, 0x10, 0x0, 0x0); 
		return; 
}

function end_xls()
{ 
	 echo pack("ss", 0x0A, 0x00); 
	 return; 
}

function write_xls($Row, $Col, $Value )
{ 
	 $L = strlen($Value); 
	 echo pack("ssssss", 0x204, 8 + $L, $Row - 1, $Col - 1, 0x0, $L); 
	 echo $Value; 
	 return;
}

function check_admin(){
	if(!isset($_COOKIE["mac"]))
	{
		header( "Location:  ../../admin/login.php");
	    exit();
	}else{
		$result_query = $connect_mysqli->query("SELECT pass
	                                        	FROM  user 
	                                        	WHERE user = 'admin'
	                                        	");
		$result = mysqli_fetch_array($result_query);
		if($_COOKIE["mac"] != $result['pass']){
			setcookie("mac", "$mac", time() - 10, "/admin","hoangvanhieu182.000webhostapp.com", 0);
			header( "Location:  ../admin/login.php");
	    	exit();
		}
	}
	return;
}

?>