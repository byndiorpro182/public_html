
<?php
include ('function/function_basic.php');      //define(DEBUG_PHP,DEBUG_PHP,false);

if (!$_GET['ap_mac']||!$_GET['version'])
{
	echo "false";
	exit();
}
$GET_ap_mac = $_GET['ap_mac'];
$GET_version = $_GET['version'];

// Kiem tra xem mac cua thiet bi da co tren server chua , neu chua thi them, neu roi thi update last time
$result_query = $connect_mysqli->query("SELECT mac
										FROM  quanlythietbi 
										WHERE mac = '$GET_ap_mac'");
$result = mysqli_fetch_array($result_query);
if(!$result['mac'])
{
	$start_time = time_now();
	$result_query = $connect_mysqli->query("INSERT INTO quanlythietbi(start_time,mac, version, last_time)
											VALUES ('$start_time','$GET_ap_mac','$GET_version', '$start_time')");
	if(!$result_query)
	{
		echo "false";
		exit();
	}
	$result_query = $connect_mysqli->query("SELECT id
											FROM quanlythietbi
											WHERE mac = '$GET_ap_mac'
											");
	$result = mysqli_fetch_array($result_query);
	$id = $result['id'];
	if($id != "")
	{
		$result_query = $connect_mysqli->query("SELECT auth
												FROM auth
												WHERE id = '$id'
												");
		$result = mysqli_fetch_array($result_query);
		$set_auth = $result['auth'];
		$result_query = $connect_mysqli->query("UPDATE quanlythietbi 
												SET auth = '$set_auth'
												WHERE mac = '$GET_ap_mac'
												");
	}
}
else
{
	$start_time = time_now();
	$result_query = $connect_mysqli->query("UPDATE quanlythietbi 
											SET last_time = '$start_time'
											WHERE mac = '$GET_ap_mac'");
	if(!$result_query)
	{
		echo "false";
		exit();
	}
}

// Kiem tra xem co tin nhan o cot message khong, neu co thi se gui du lieu, neu khong thi update du lieu
$result_query = $connect_mysqli->query("SELECT message
										FROM  quanlythietbi
										WHERE mac = '$GET_ap_mac'");
$result = mysqli_fetch_array($result_query);

if($result['message'] == "")   // khong co tin nhan, thi server se nhan du lieu
{
	$SET_SQL = "";
	$auth = $_GET['auth'];
	$ap_ssid = $_GET['ap_ssid'];
	$ap_pass = $_GET['ap_pass'];
	$ssid_now = $_GET['ssid_now'];
	$pass_now = $_GET['pass_now'];
	$ssid1 = $_GET['ssid1'];
	$ssid2 = $_GET['ssid2'];
	$ssid3 = $_GET['ssid3'];
	$ssid4 = $_GET['ssid4'];
	$ssid5 = $_GET['ssid5'];
	$pass1 = $_GET['pass1'];
	$pass2 = $_GET['pass2'];
	$pass3 = $_GET['pass3'];
	$pass4 = $_GET['pass4'];
	$pass5 = $_GET['pass5'];
	$local_ip = $_GET['local_ip'];
	$gateway = $_GET['gateway'];
	$subnet = $_GET['subnet'];
	$version = $_GET['version'];
	if ($_GET['auth'])
	{
		$SET_SQL = "$SET_SQL auth = '$auth',";
	}
	if ($_GET['ap_ssid'])
	{
		$SET_SQL = "$SET_SQL ap_ssid = '$ap_ssid',";
	}
	if ($_GET['ap_pass'])
	{
		$SET_SQL = "$SET_SQL ap_pass = '$ap_pass',";
	}
	if ($_GET['ssid_now'])
	{
		$SET_SQL = "$SET_SQL ssid_now = '$ssid_now',";
	}
	if ($_GET['pass_now'])
	{
		$SET_SQL = "$SET_SQL pass_now = '$pass_now',";
	}
	if ($_GET['ssid1'])
	{
		$SET_SQL = "$SET_SQL ssid1 = '$ssid1', ";
	}
	if ($_GET['ssid2'])
	{
		$SET_SQL = "$SET_SQL ssid2 = '$ssid2', ";
	}
	if ($_GET['ssid3'])
	{
		$SET_SQL = "$SET_SQL ssid3 = '$ssid3', ";
	}
	if ($_GET['ssid4'])
	{
		$SET_SQL = "$SET_SQL ssid4 = '$ssid4', ";
	}
	if ($_GET['ssid5'])
	{
		$SET_SQL = "$SET_SQL ssid5 = '$ssid5', ";
	}
	if ($_GET['pass1'])
	{
		$SET_SQL = "$SET_SQL pass1 = '$pass1', ";
	}
	if ($_GET['pass2'])
	{
		$SET_SQL = "$SET_SQL pass2 = '$pass2', ";
	}
	if ($_GET['pass3'])
	{
		$SET_SQL = "$SET_SQL pass3 = '$pass3', ";
	}
	if ($_GET['pass4'])
	{
		$SET_SQL = "$SET_SQL pass4 = '$pass4', ";
	}
	if ($_GET['pass5'])
	{
		$SET_SQL = "$SET_SQL pass5 = '$pass5', ";
	}
	if ($_GET['local_ip'])
	{
		$SET_SQL = "$SET_SQL local_ip = '$local_ip', ";
	}
	if ($_GET['getway'])
	{
		$SET_SQL = "$SET_SQL getway = '$getway', ";
	}
	if ($_GET['subnet'])
	{
		$SET_SQL = "$SET_SQL subnet = '$subnet', ";
	}
	if ($_GET['version'])
	{
		$SET_SQL = "$SET_SQL version = '$version'";
		$result_query = $connect_mysqli->query("UPDATE quanlythietbi 
												SET $SET_SQL
												WHERE mac = '$GET_ap_mac'");
		if(!$result_query)
		{
			echo "false";
			exit();
		}
		else
		{
		    echo "true";
		    exit();
		}
	}
	else
	{
		echo "false";
		exit();
	}
}
else 						// message co tin nhan thi se gui du lieu xuong server
{
	$result_query = $connect_mysqli->query("SELECT *
											FROM  quanlythietbi
											WHERE mac = '$GET_ap_mac'");
	$result = mysqli_fetch_array($result_query);
	$reply = "auth=".$result['auth'].
	        "&ap_ssid=".$result['ap_ssid'].
			"&ap_pass=".$result['ap_pass'].
			"&ssid_now=".$result['ssid_now'].
			"&pass_now=".$result['pass_now'].
			"&ssid1=".$result['ssid1'].
			"&ssid2=".$result['ssid2'].
			"&ssid3=".$result['ssid3'].
			"&ssid4=".$result['ssid4'].
			"&ssid5=".$result['ssid5'].
			"&pass1=".$result['pass1'].
			"&pass2=".$result['pass2'].
			"&pass3=".$result['pass3'].
			"&pass4=".$result['pass4'].
			"&pass5=".$result['pass5'];
	$buf = $result['local_ip'];
	$ip1 = (int)($buf/16777216);
	$ip2 = (int)(($buf - (int)$ip1*16777216)/65536);
	$ip3 = (int)(($buf - (int)($ip1*16777216) - (int)($ip2*65536))/256);
	$ip4 = (int)(($buf - (int)($ip1*16777216) - (int)($ip2*65536) - (int)($ip3*256)));
	if ( $ip1 >= 0 && $ip1 <= 255){
		if ( $ip2 >= 0 && $ip2 <= 255){
			if ( $ip3 >= 0 && $ip3 <= 255){
				if ( $ip4 >= 0 && $ip4 <= 255){
					$reply = "$reply".
					"&local_ip1=".$ip1.
					"&local_ip2=".$ip2.
					"&local_ip3=".$ip3.
					"&local_ip4=".$ip4;
				}
			}
		}
	}
	$buf = $result['gateway'];
	$ip1 = (int)($buf/16777216);
	$ip2 = (int)(($buf - (int)$ip1*16777216)/65536);
	$ip3 = (int)(($buf - (int)($ip1*16777216) - (int)($ip2*65536))/256);
	$ip4 = (int)(($buf - (int)($ip1*16777216) - (int)($ip2*65536) - (int)($ip3*256)));
	if ( $ip1 >= 0 && $ip1 <= 255){
		if ( $ip2 >= 0 && $ip2 <= 255){
			if ( $ip3 >= 0 && $ip3 <= 255){
				if ( $ip4 >= 0 && $ip4 <= 255){
					$reply = "$reply".
					"&gateway1=".$ip1.
					"&gateway2=".$ip2.
					"&gateway3=".$ip3.
					"&gateway4=".$ip4;
				}
			}
		}
	}
	$buf = $result['subnet'];
	$ip1 = (int)($buf/16777216);
	$ip2 = (int)(($buf - (int)$ip1*16777216)/65536);
	$ip3 = (int)(($buf - (int)($ip1*16777216) - (int)($ip2*65536))/256);
	$ip4 = (int)(($buf - (int)($ip1*16777216) - (int)($ip2*65536) - (int)($ip3*256)));
	if ( $ip1 >= 0 && $ip1 <= 255){
		if ( $ip2 >= 0 && $ip2 <= 255){
			if ( $ip3 >= 0 && $ip3 <= 255){
				if ( $ip4 >= 0 && $ip4 <= 255){
					$reply = "$reply".
					"&subnet1=".$ip1.
					"&subnet2=".$ip2.
					"&subnet3=".$ip3.
					"&subnet4=".$ip4;
				}
			}
		}
	}
	$reply = "$reply".
			"&ota_update=".$result['ota_update'].
			"&message=".$result['message'];
	echo $reply;
	$result_query = $connect_mysqli->query("UPDATE quanlythietbi 
											SET message = ''
											WHERE mac = '$GET_ap_mac'");
}
mysql_close($connect_mysqli);
?>