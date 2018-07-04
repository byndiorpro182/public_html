<?php
	include ('../function/function_basic.php');
    

	$mac = $_GET['ap_mac'];
	$version = $_GET['version'];
	if ($mac == "" || $version == "" || strlen($mac) != 17)
	{
		echo "false";
		exit();
	}
	// tìm phiên bản mới nhất
	$result_query = $connect_mysqli->query("SELECT MAX(version) version
    										FROM version 
                                        	");
    $result = mysqli_fetch_array($result_query);
    $version_max = $result['version'];
    $result_query = $connect_mysqli->query("SELECT version, link
    										FROM version
    										WHERE version = $version_max
                                        	");
    $result = mysqli_fetch_array($result_query);
    $new_version = $result['version'];   // Phiên bản mới nhất
	if($version < $new_version){               // check version
		echo $result['link'];
		exit();
	}    

	$result_query = $connect_mysqli->query("SELECT id, auth
											FROM  hardware
											WHERE mac = '$mac'");
	if (mysqli_num_rows($result_query) > 0) {
		$result = mysqli_fetch_array($result_query);
		if (strlen($result['auth']) == 32)
		{
			$auth = $result['auth'];
			echo "$auth";
		}
		else
		{
			$id = $result['id'];
			$result_query = $connect_mysqli->query("SELECT auth
													FROM auth
													WHERE id = '$id'
													");
			$result = mysqli_fetch_array($result_query);
			$set_auth = $result['auth'];
			$result_query = $connect_mysqli->query("UPDATE hardware
													SET auth = '$set_auth'
													WHERE mac = '$mac'
													");
			echo "$set_auth";
		}
		$time_now = time_now();
		$result_query = $connect_mysqli->query("UPDATE hardware 
												SET last_time = '$time_now'
												WHERE mac = '$mac'
												");
	}else{
		$time_now = time_now();
		$result_query = $connect_mysqli->query("INSERT INTO hardware(start_time,mac, last_time)
												VALUES ('$time_now','$mac', '$time_now')");
		$result_query = $connect_mysqli->query("INSERT INTO user(mac)
												VALUES ('$mac')");
		if(!$result_query)
		{
			echo "false";
			exit();
		}
		$result_query = $connect_mysqli->query("SELECT id
												FROM  hardware
												WHERE mac = '$mac'");
		$result = mysqli_fetch_array($result_query);
		$id = $result['id'];
		$result_query = $connect_mysqli->query("SELECT auth
												FROM auth
												WHERE id = '$id'
												");
		$result = mysqli_fetch_array($result_query);
		$set_auth = $result['auth'];
		$result_query = $connect_mysqli->query("UPDATE hardware
												SET auth = '$set_auth'
												WHERE mac = '$mac'
												");
		echo "$set_auth";
	}
?>