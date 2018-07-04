<?php
include('../function/function_basic.php');
check_admin();

$auth = $_GET['auth'];

if (!isset($_GET['auth']) || strlen($_GET['auth']) != 32){
	header("Location:  auth.php");
	exit();
}else{
	$ch = curl_init();

	curl_setopt($ch, CURLOPT_URL, "http://188.166.206.43/$auth/isHardwareConnected");
	curl_setopt($ch, CURLOPT_RETURNTRANSFER, TRUE);
	curl_setopt($ch, CURLOPT_HEADER, FALSE);

	$status_hardware = curl_exec($ch);
	if($status_hardware == "Invalid token."){
		header("Location:  auth.php");
		exit();
	}
	curl_close($ch);
	$result_query = $connect_mysqli->query("SELECT mac,last_time
                                            FROM  hardware
                                            WHERE auth = '$auth'
                                            ");
	$result = mysqli_fetch_array($result_query);
	$mac = $result['mac'];
	$last_time = $result['last_time'];

	if(!$mac)
	{
		$mac = "NOT HARDWARE";
		$last_time = "0";
	}
	

}

if(isset($_POST['read_pin'])){
	$selected = $_POST['virtual_pin'];
	$ch = curl_init();

	curl_setopt($ch, CURLOPT_URL, "http://188.166.206.43/$auth/get/v$selected");
	curl_setopt($ch, CURLOPT_RETURNTRANSFER, TRUE);
	curl_setopt($ch, CURLOPT_HEADER, FALSE);

	$content = curl_exec($ch);
	curl_close($ch);
}

if(isset($_POST['write_pin'])){
	$selected = $_POST['virtual_pin'];
	$content = $_POST['content'];
	$ch = curl_init();
	
	curl_setopt($ch, CURLOPT_URL, "http://188.166.206.43/$auth/update/v$selected?value=".rawurlencode($content));
	curl_setopt($ch, CURLOPT_RETURNTRANSFER, TRUE);
	curl_setopt($ch, CURLOPT_HEADER, FALSE);

	$buff = curl_exec($ch);

	curl_close($ch);
	$message_write = "Update finished !";
}


?>

<?php include('include/top.php'); ?>
<div class="row">
    <div class="col-lg-12">
        <ol class="breadcrumb">
            <li>
                <i class="fa fa-dashboard"></i>  <a href="../admin/admin.php">Admin</a>
            </li>
            <li class="active">
                <i class="fa fa-file"></i> <a href="/admin/auth.php">Auth</a>
            </li>
            <li class="active">
                <a href='/admin/project.php?auth=<?php echo $_GET['auth'];?>'><?php echo $_GET['auth'];?></a>
            </li>
        </ol>
    </div>
</div>

<div class="col-lg-6">
	<div class="form-group">
		<label>Mac : </label>
		<label><?php echo $mac; ?></label>
	</div>
	<div class="form-group">
		<label>Status : </label>
		<label><?php echo $status_hardware; ?></label>
	</div>
	<div class="form-group">
		<label>Last time : </label>
		<label><?php echo $last_time; ?></label>
	</div>
	<div class="table-responsive">
		<table class="table table-bordered table-hover">
			<thead>
			    <tr>
			    	<th>PIN</th>
			        <th>VALUE</th>
			    </tr>
			</thead>
			<tbody>
				<?php
				
					for ($i=0; $i < 17; $i++) {
						if($i > 5 && $i< 12 )
							continue;
						$ch = curl_init();

						curl_setopt($ch, CURLOPT_URL, "http://188.166.206.43/$auth/get/d$i");
						curl_setopt($ch, CURLOPT_RETURNTRANSFER, TRUE);
						curl_setopt($ch, CURLOPT_HEADER, FALSE);

						$response = trim(curl_exec($ch), '["]');
						echo "<tr>";
						echo "<th>D".$i."</th>";
						echo "<th>".$response."</th>";						
						echo "</tr>";

						curl_close($ch);
					}
					
				?>
			</tbody>
		</table>
	</div>
	<form method="POST">
		<div class="form-group">
			<label>Virtual PIN : </label>
			<label>
				<select class="form-control" name="virtual_pin">
	                <?php
	                for ($i=1; $i < 128 ; $i++) {	                	
	                	if($selected == $i)
	                	{
	                		echo "<option selected>".$i."</option>";
	                	}else{
	                		echo "<option >".$i."</option>";
	                	}	                	
	                }
	                ?>
	            </select>	
			</label>
			<label>
				<button type="submit" class="btn btn-default" name="read_pin"> READ </button>
			</label>
			<label>
				<button type="submit" class="btn btn-default" name="write_pin"> WRITE </button>
			</label>
			<label><?php echo $message_write; ?></label>
		</div>
		<div class="form-group">
            <label>Content</label>
            <textarea type="text" class="form-control" rows="5" name="content"><?php echo trim($content, '["]' ); ?></textarea>
        </div>
            
            
		
	</form>
</div>

<?php include('include/bot.php'); ?>