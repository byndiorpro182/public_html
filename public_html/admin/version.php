<?php
	include('../function/function_basic.php');
	check_admin();
	if(isset($_POST['uploadclick']))
	{
		if (isset($_FILES['bin']))
		{
			if(isset($_FILES['ino'])){
				if ($_FILES['bin']['error'] == 0)
				{
					if($_FILES['bin']['size'] < 10000000)    // size nhỏ hơn 10MB
					{
						$name_file = $_FILES['bin']['name'];
						$link_file = "http://hoangvanhieu182.000webhostapp.com/file/".$name_file;
						move_uploaded_file($_FILES['bin']['tmp_name'], '../file/'.$_FILES['bin']['name']);
						move_uploaded_file($_FILES['ino']['tmp_name'], '../file/ino/'.$_FILES['ino']['name']);
						$time_now = time_now();
						if(strlen($name_file) == 12 && strpos($name_file, ".bin") > 0)
						{
							$version = substr($name_file,0,8);
						}else
						{
							$version = 0;
						}
						
						$result_query = $connect_mysqli->query("INSERT INTO version(date_update,name,version,link)
				                                                VALUES ('$time_now','$name_file',$version,'$link_file')
				                                                ");
						if(!$result_query)
						{
							echo "false";
							exit();
						}
						$message = "upload finish!";
					}
				}
			}else{$message = "Thiếu tệp .ino";}

		}else{$message = "Thiếu tệp .bin";}
	}
	if(isset($_POST['update_hardware'])){
		if($_POST['text_mac'] != ""){
			$umac = $_POST['text_mac'];
		}else{
			if($_POST['select_mac'] != "00:00:00:00"){
				$umac = $_POST['select_mac'];
			}else{
				$message = "Chưa chọn mã mac";
			}
		}
		if(isset($_FILES['file_bin']) && $_FILES['file_bin']['error'] == 0 && $_FILES['file_bin']['size'] < 10000000){
			$name_file = $_FILES['file_bin']['name'];
			$link_file = "http://hoangvanhieu182.000webhostapp.com/file/test/".$name_file;
			move_uploaded_file($_FILES['file_bin']['tmp_name'], '../file/test/'.$_FILES['file_bin']['name']);
		}else{
			if($_POST['select_vesion'] != "0"){
				$name_file = $_POST['select_vesion'];
				$link_file = "http://hoangvanhieu182.000webhostapp.com/file/".$name_file.".bin";
			}else{
				$message = "Chưa thấy file";
			}
		}
		$notifical = $umac."---".$link_file;
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
                <i class="fa fa-file"></i> <a href="/admin/version.php">Version</a>
            </li>
        </ol>
    </div>
</div>

<div class="col-lg-4">
	<form role="form" method="post" action="version.php" enctype="multipart/form-data">
		<div class="form-group">
			<label><?php echo $message; ?></label><br />
            <H3>Update OS Hardware</H3>
            <label>File .bin</label>
            <input type="file" name="bin"><br />
            <label>File .ino</label>
            <input type="file" name="ino"><br />
            <button type="submit" class="btn btn-default" name="uploadclick">Send</button>
            <?php
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
            ?>
            <br /><br /><br /><br /><br />
            <label>==========================</label><br /><br /><br /><br />
            <br /><br /><label>Version new : <?php echo $result['version']; ?></label><br />
            <label>Link : <?php echo $result['link']; ?></label>
        </div>
	</form>
</div>
<div class="col-lg-8">
    <h2>UPDATE HARDWARE</h2>
    <form role="form" method="post" action="version.php" enctype="multipart/form-data">
    	<div class="form-group">
    		<label><?php echo $notifical; ?></label>
    		<label>Selection hardware</label>
            <input type="text" class="form-control" placeholder="MAC" name="text_mac"><br>
                <?php
					$result_query = $connect_mysqli->query("SELECT mac
			                                                FROM  hardware
			                                                WHERE id>0
			                                                ");
			    ?>
			<select class="form-control" name="select_mac">
                <option>00:00:00:00</option>
                <?php while ($result = mysqli_fetch_array($result_query)) { ?>
                <option><?php echo $result['mac']; ?></option>
            	<?php } ?>
            </select>
        </div>
        <div class="form-group">
        	<label>Selection version</label>
        	<input type="file" name="file_bin"><br />
        	    <?php
					$result_query = $connect_mysqli->query("SELECT version
			                                                FROM  version 
			                                                ORDER BY id DESC;
			                                                ");
			    ?>
        	<select class="form-control" name="select_vesion">
                <option>0</option>
                <?php while ($result = mysqli_fetch_array($result_query)) { ?>
                <option><?php echo $result['version']; ?></option>
                <?php } ?>
            </select>
        </div>
    	
    	<button type="submit" class="btn btn-default" name="update_hardware">Update</button>
    </form>
    <h2>DATA BASE</h2>
    <?php
		$result_query = $connect_mysqli->query("SELECT id,date_update,name,version,link
                                                FROM  version 
                                                ORDER BY id DESC;
                                                ");
    ?>
	<div class="table-responsive">
		<table class="table table-bordered table-hover">
			<thead>
			    <tr>
			    	<th>STT</th>
			        <th>DATE</th>
			        <th>NAME</th>
			        <th>VERSION</th>
			        <th>LINK</th>
			    </tr>
			</thead>
			<tbody>
				<?php 
					while ($result = mysqli_fetch_array($result_query))
					{
						echo "<tr>";
						echo "<td>".$result['id']."</td>";
						echo "<td>".$result['date_update']."</td>";
						echo "<td>".$result['name']."</td>";
						echo "<td>".$result['version']."</td>";
						echo "<td>".$result['link']."</td>";
						echo "</tr>";
					}
				?>
			</tbody>
		</table>
	</div>
	
</div>

<?php include('include/bot.php'); ?>