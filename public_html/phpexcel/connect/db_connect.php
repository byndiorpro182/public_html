<?php

$mysqli = mysqli_connect('localhost','id4017829_hieuql','0912226204','id4017829_du_lieu');
$mysqli->set_charset('utf8');
if(mysqli_connect_errno()){
	echo 'Connect Failed: '.mysqli_connect_error();
	exit;
}


?>