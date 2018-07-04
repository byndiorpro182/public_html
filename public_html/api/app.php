<?php
	include ('../function/function_basic.php');

	if ($_GET['mac'] != "") {
		$mac = $_GET['mac'];
		if(isset($_GET['infrared']) && $_GET['infrared'] == 'get_data'){
			$result_query = $connect_mysqli->query("SELECT name,value
													FROM  display 
													WHERE mac = '$mac'");
			if(mysqli_num_rows($result_query) > 0){
				while ($result = mysqli_fetch_array($result_query)){
					echo "{".$result['name']."=>".$result['value']."}";
				}	
			}else{
				echo "ok";
			}
		}else if(isset($_GET['infrared']) && $_GET['infrared'] == 'delete' && isset($_GET['name'])){
			$result_query = $connect_mysqli->query("DELETE FROM display 
                                                	WHERE mac='$mac' AND name=\"".$_GET['name']."\"");
            if($result_query){
                echo "true";
            }else{
                echo "false";
            }
		}else if(isset($_GET['infrared']) && $_GET['infrared'] == 'set_data' && isset($_GET['name']) && isset($_GET['value'])){
			$name = $_GET['name'];
            $value = $_GET['value'];
            $result_query = $connect_mysqli->query("INSERT INTO display(mac,name,value,type)
                                                    VALUES ('$mac','$name','$value','button')
                                                    ");
            if($result_query){
                echo "true";
            }else{
                echo "false";
            }
		}else{
			$result_query = $connect_mysqli->query("SELECT id,auth
													FROM  hardware 
													WHERE mac = '$mac'");
			if (mysqli_num_rows($result_query) > 0) {
				$result = mysqli_fetch_array($result_query);
				if (strlen($result['auth']) == 32) {
					$auth = $result['auth'];
					echo "$auth";
				}else{
					echo "auth";	
				}
			}else{
				echo "mac";
			}
		}
	}else{
		echo "ok";
	}
?>