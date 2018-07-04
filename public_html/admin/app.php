<?php
include('../function/function_basic.php');
	check_admin();
	if(isset($_POST['uploadclick']))
	{
		if (isset($_FILES['apk']))
		{
			if ($_FILES['apk']['error'] == 0)
			{
				if($_FILES['apk']['size'] < 10000000)    // size nhỏ hơn 10MB
				{
					$name_file = $_FILES['apk']['name'];
					$link_file = "http://hoangvanhieu182.000webhostapp.com/file/app/".$name_file;
					if(strpos($name_file, ".apk") > 0)
					{
						$message = "upload finish!";
						move_uploaded_file($_FILES['apk']['tmp_name'], '../file/app/'.$_FILES['apk']['name']);
					}else
					{
						$message = "chỉ tải lên được file .apk!";
					}
					
				}else{$message = "file > 10MB";}
			}else{$message = "file false";}
		}else{$message = "no file";}
	}
	if(isset($_POST['set_link'])){
		if(isset($_POST['link_app']) && $_POST['link_app'] != ""){
			$result_query = $connect_mysqli->query("UPDATE app
	                                                SET link_app='".$_POST['link_app']."' WHERE id=1");
			if($result_query){
	            $message = "Lưu thành công"; 
	        }
	        else{
	            $message = "Lưu thất bại";
	        }
		}
	}

?>

<?php include('include/top.php'); ?>
<div class="row">
    <div class="col-lg-12">
        <ol class="breadcrumb">
            <li>
                <i class="fa fa-dashboard"></i>  <a href="admin.php">Admin</a>
            </li>
            <li class="active">
                <i class="fa fa-file"></i> <a href="app.php">App</a>
            </li>
        </ol>
    </div>
</div>
<div class="row">
	<div class="col-lg-6">
		<form role="form" method="post" action="app.php" enctype="multipart/form-data">
			<div class="form-group">
				<label><?php echo $message ?></label><br />
	            <label>Update APP</label>
	            <input type="file" name="apk"><br />
	            <button type="submit" class="btn btn-default" name="uploadclick">Send</button>
	        </div>
		</form><br>
		<br><label>---------------------</label><br>
		<?php
			$result_query = $connect_mysqli->query("SELECT link_app
            										FROM app
            										WHERE id=1
                                                	");
			$result = mysqli_fetch_array($result_query);
		?>
		<form method="post" action="app.php">
			<div class="form-group">
				<label> Link app hiện tại </label>
				<input type="text" name="link_app" class="form-control" value="<?php echo $result['link_app'];?>"><br>
				<button type="submit" class="btn btn-default" name="set_link">SEND</button>			
			</div>
		</form>
	</div>
	<?php
		$ch = curl_init();

		curl_setopt($ch, CURLOPT_URL, "http://hoangvanhieu182.000webhostapp.com/file/app/");
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, TRUE);
		curl_setopt($ch, CURLOPT_HEADER, FALSE);

		$content = curl_exec($ch);
		curl_close($ch);
	?>
	<div class="col-lg-6">
		<?php echo $content; ?>
	</div>
	
</div>


<?php include('include/bot.php'); ?>