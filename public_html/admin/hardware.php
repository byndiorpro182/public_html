
<?php include('../function/function_basic.php'); check_admin();?>
<?php include('include/top.php'); ?>
<div class="row">
    <div class="col-lg-12">
        <ol class="breadcrumb">
            <li>
                <i class="fa fa-dashboard"></i>  <a href="../admin/admin.php">Admin</a>
            </li>
            <li class="active">
                <i class="fa fa-file"></i> <a href="/admin/hardware.php">Hardware</a>
            </li>
        </ol>
    </div>
</div>

<div class="col-lg-12">
	<h3>
		DATA HARDWARE
	</h3>
	<?php
	$result_query = $connect_mysqli->query("SELECT id,start_time,mac,auth,last_time
                                            FROM  hardware
                                            WHERE id > 0
                                            ");
    ?>
	<div class="table-responsive">
		<table class="table table-bordered table-hover">
			<thead>
			    <tr>
			    	<th>STT</th>
			        <th>START_TIME</th>
			        <th>MAC</th>
			        <th>AUTH</th>
			        <th>LAST_TIME</th>
			        <th>QR</th>
			    </tr>
			</thead>
			<tbody>
				<?php
					while ($result = mysqli_fetch_array($result_query))
					{
						echo "<tr>";
						echo "<td>".$result['id']."</td>";
						echo "<td>".$result['start_time']."</td>";
						echo "<td>".$result['mac']."</td>";
						echo "<td><a href='project.php?auth=".$result['auth']."'>".$result['auth']."</a>"."</td>";
						echo "<td>".$result['last_time']."</td>";
						echo '<td><a target=_blank href=http://chart.googleapis.com/chart?cht=qr&chs=500x500&chl='.$result['mac'].">Hiện</a></td>";
						echo "</tr>";
					}
				?>
			</tbody>
		</table>
	</div>



</div>

<?php include('include/bot.php'); ?>