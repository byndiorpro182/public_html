<?php include('../function/function_basic.php'); check_admin();?>
<?php include('include/top.php'); ?>
<?php

	if(isset($_POST['search'])){
		$status_search = true;

		if($_POST['mac'] != "" && $status_search){
			$result_query = $connect_mysqli->query("SELECT *
	                                                FROM  user
	                                                WHERE mac LIKE '%".$_POST['mac']."%'");
			$status_search = false;
		}
		if($_POST['user'] != "" && $status_search){
			$result_query = $connect_mysqli->query("SELECT *
	                                                FROM  user
	                                                WHERE user LIKE '%".$_POST['user']."%'");
			$status_search = false;
		}
		if($_POST['name'] != "" && $status_search){
			$result_query = $connect_mysqli->query("SELECT *
	                                                FROM  user
	                                                WHERE name LIKE '%".$_POST['name']."%'");
			$status_search = false;
		}
		if($_POST['phone_number'] != "" && $status_search){
			$result_query = $connect_mysqli->query("SELECT *
	                                                FROM  user
	                                                WHERE phone_number LIKE '%".$_POST['phone_number']."%'");
			$status_search = false;
		}
		if($_POST['address'] != "" && $status_search){
			$result_query = $connect_mysqli->query("SELECT *
	                                                FROM  user
	                                                WHERE address LIKE '%".$_POST['address']."%'");
			$status_search = false;
		}
		if($_POST['day_of_sale'] != "" && $status_search){
			$result_query = $connect_mysqli->query("SELECT *
	                                                FROM  user
	                                                WHERE day_of_sale='".$_POST['mac']."'");
			$status_search = false;
		}
        if(mysqli_num_rows($result_query) == 0){
    		$message = "No data found";
    	}                                     
	}
	if(isset($_GET['search_mac'])){
		$result_query = $connect_mysqli->query("SELECT *
                                                FROM  user
                                                WHERE mac='".$_GET['search_mac']."'");
	 	if(mysqli_num_rows($result_query) == 0){
    		$message = "No data found";
    	}
	}
	if(isset($_POST['save'])){

		if($_POST['price'] < 10000){
			$result_query = $connect_mysqli->query("SELECT log
	                                                FROM  user
	                                                WHERE mac='".$_POST['save']."'");
			$result = mysqli_fetch_array($result_query);
			date_default_timezone_set('Asia/Ho_Chi_Minh');
			$LOG = $result['log']."\n".date("j/m/Y H:i:s")."\nuser : ".$_POST['user']."\n";
			$LOG = $LOG."pass : ".$_POST['pass']."\n";
			$LOG = $LOG."name : ".$_POST['name']."\n";
			$LOG = $LOG."phone_number : ".$_POST['phone_number']."\n";
			$LOG = $LOG."address : ".$_POST['address']."\n";
			$LOG = $LOG."day_of_sale : ".$_POST['day_of_sale']."\n";
			$LOG = $LOG."log : ".$_POST['log']."\n";
			$LOG = $LOG."price : ".$_POST['price']."\n";
			$LOG = $LOG."--------------------------------------";

			$SET_SQL = "";
			$SET_SQL = $SET_SQL."user='".$_POST['user']."',";
			$SET_SQL = $SET_SQL."pass='".$_POST['pass']."',";
			$SET_SQL = $SET_SQL."name='".$_POST['name']."',";
			$SET_SQL = $SET_SQL."phone_number='".$_POST['phone_number']."',";
			$SET_SQL = $SET_SQL."address='".$_POST['address']."',";
			$SET_SQL = $SET_SQL."day_of_sale='".$_POST['day_of_sale']."',";
			$SET_SQL = $SET_SQL."price='".$_POST['price']."',";
			$SET_SQL = $SET_SQL."log='".$LOG."'";
			$result_query = $connect_mysqli->query("UPDATE user
	                                                SET $SET_SQL
	                                                WHERE mac='".$_POST['save']."'");
	        if($result_query)
	        {
	            $message = "Save finished"; 
	        }
	        else
	        {
	            $message = "Save failed";
	        }	
		}else{
			$message = "Đơn vị tiền tệ là : K nên hãy nhập số nhỏ hơn 10000";
		}
	
		$result_query = $connect_mysqli->query("SELECT *
                                                FROM  user
                                                WHERE mac='".$_POST['save']."'");
	 	if(mysqli_num_rows($result_query) == 0){
    		$message = "No data found";
    	}
	}
 
	
?>
<div class="row">
    <div class="col-lg-12">
        <ol class="breadcrumb">
            <li>
                <i class="fa fa-dashboard"></i>  <a href="admin.php">Admin</a>
            </li>
            <li class="active">
                <i class="fa fa-file"></i> <a href="manage.php">Manage</a>
            </li>
        </ol>
    </div>
</div>
<form role="form" method="POST" class="page-header" action="manage.php">
	<h4><?php echo $message; ?></h4>
	<H3>
		Data Search
	</H3>
	<div class="row">
		<div class="col-lg-4">
			<div class="form-group has-success">
                <label class="control-label" for="inputSuccess">Mac</label>
                <input type="text" class="form-control" name="mac" >
            </div>
            <div class="form-group has-success">
                <label class="control-label" for="inputSuccess">Phone number</label>
                <input type="text" class="form-control" name="phone_number" >
        	</div>
		</div>
		<div class="col-lg-4">
            <div class="form-group has-success">
                <label class="control-label" for="inputSuccess">User</label>
                <input type="text" class="form-control" name="user" >
            </div>
            <div class="form-group has-success">
                <label class="control-label" for="inputSuccess">Address</label>
                <input type="text" class="form-control" name="address">
            </div>
		</div>
		<div class="col-lg-4">
			 <div class="form-group has-success">
                <label class="control-label" for="inputSuccess">Name</label>
                <input type="text" class="form-control" name="name" >
            </div>
            <div class="form-group has-success">
                <label class="control-label" for="inputSuccess">Day of sale</label>
                <input type="text" class="form-control" name="day_of_sale" placeholder="<?php date_default_timezone_set('Asia/Ho_Chi_Minh'); echo date("Y-m-j"); ?>">
            </div>
		</div>
	</div>
	<div class="row">
		<div class="col-lg-12">
			<div class="form-group input-group">
                <button type="submit" class="btn btn-info" name="search">
      				<span class="glyphicon glyphicon-search"></span> Search
    			</button>
            </div>
		</div>
	</div>
</form>
<?php if(isset($_POST['search']) || isset($_GET['search_mac']) || isset($_POST['save'])){ ?>
	<?php if(mysqli_num_rows($result_query) > 0){ ?>
		<form role="form" method="POST" class="page-header">
			<div class="row">
		        <div class="col-lg-12">
					<table class="table table-hover">
				    <thead>
					    <tr>
					        <th>Stt</th>
					        <th>Mac</th>
					        <th>User</th>
					        <th>Pass</th>
					        <th>Name</th>
					        <th>Phone number</th>
					        <th>Address</th>
					        <th>Day of sale</th>
					    </tr>
				    </thead>
				    <tbody>
				    	<?php $stt = 1; while($result = mysqli_fetch_array($result_query)){		
				    		if(mysqli_num_rows($result_query) == 1){
					    		$mac = $result['mac'];
					    		$user = $result['user'];
					    		$pass = $result['pass'];
					    		$name = $result['name'];
					    		$phone_number = $result['phone_number'];
					    		$address = $result['address'];
					    		$day_of_sale = $result['day_of_sale'];
					    		$log = $result['log'];
					    		$price = $result['price'];				    			
				    		}			    	
				    	?>
				      	<tr>
					        <td><?php echo $stt++; ?></td>
					        <td><a href='manage.php?search_mac=<?php echo $result['mac']; ?>' ><?php echo $result['mac']; ?></a></td>
					        <td><?php echo $result['user']; ?></td>
					        <td><?php echo $result['pass']; ?></td>
					        <td><?php echo $result['name']; ?></td>
					        <td><?php echo $result['phone_number']; ?></td>
					        <td><?php echo $result['address']; ?></td>
					        <td><?php echo $result['day_of_sale']; ?></td>
				      	</tr>
				      <?php } ?>
				    </tbody>
				  </table>
				</div>
			</div>
		</form>
		<?php if(mysqli_num_rows($result_query) == 1){ ?>
			<form role="form" method="POST">
				<h3>
					Edit Data
				</h3>
				<div class="row">
		        	<div class="col-lg-12">
		        		<div class="form-group has-success"> 
		        			<label class="control-label" for="inputSuccess">Mac</label>
		        			<input type="text" class="form-control" name="mac" value="<?php echo $mac; ?>" disabled>
		        		</div>
		        		<div class="form-group has-success"> 
		        			<label class="control-label" for="inputSuccess">User</label>
		        			<input type="text" class="form-control" name="user" value="<?php echo $user; ?>">
		        		</div>
		        		<div class="form-group has-success"> 
		        			<label class="control-label" for="inputSuccess">Pass</label>
		        			<input type="text" class="form-control" name="pass" value="<?php echo $pass; ?>">
		        		</div>
		        		<div class="form-group has-success"> 
		        			<label class="control-label" for="inputSuccess">Name</label>
		        			<input type="text" class="form-control" name="name" value="<?php echo $name; ?>">
		        		</div>
		        		<div class="form-group has-success"> 
		        			<label class="control-label" for="inputSuccess">Phone number</label>
		        			<input type="text" class="form-control" name="phone_number" value="<?php echo $phone_number; ?>">
		        		</div>
		        		<div class="form-group has-success"> 
		        			<label class="control-label" for="inputSuccess">Address</label>
		        			<input type="text" class="form-control" name="address" value="<?php echo $address; ?>">
		        		</div>
		        		<div class="form-group has-success"> 
		        			<label class="control-label" for="inputSuccess">Day of sale</label>
		        			<input type="text" class="form-control" name="day_of_sale" value="<?php echo $day_of_sale; ?>">
		        		</div>
		        		<div class="form-group has-success"> 
		        			<label class="control-label" for="inputSuccess">Price</label>
		        			<input type="text" class="form-control" name="price" value="<?php echo $price; ?>">
		        		</div>
		        		<div class="form-group has-success"> 
		        			<label class="control-label" for="inputSuccess">Note</label>
		        			<input type="text" class="form-control" name="log">
		        		</div>
		        		<div class="form-group has-success"> 
		        			<label class="control-label" for="inputSuccess">Log(Note)</label>
		        			<textarea type="text" class="form-control" name="const_log" rows="15" disabled><?php echo $log; ?></textarea>
		        		</div>
		        		<div class="form-group input-group">
			                <button type="submit" class="btn btn-info" name="save" value="<?php echo $mac; ?>">
			      				<span class="glyphicon glyphicon-save"></span> Save
			    			</button>
			            </div>
		        	</div>
		        </div>
			</form>
		<?php } ?>
	<?php } ?>
<?php } ?>

<?php include('include/bot.php'); ?>