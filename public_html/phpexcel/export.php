<?php
include('../function/function_basic.php'); 
require('Classes/PHPExcel.php');

$message = "";
if(isset($_POST['btnExport'])){
	$objExcel = new PHPExcel;
	$objExcel->setActiveSheetIndex(0);
	$sheet = $objExcel->getActiveSheet()->setTitle('Sheet1');  //10A1

	$rowCount = 1;
	$sheet->setCellValue('A'.$rowCount,'id');
	$sheet->setCellValue('B'.$rowCount,'auth');

	$result = $connect_mysqli->query("SELECT id,auth
                                      FROM  auth
                                      WHERE id > 0
                                      ");
	while($row = mysqli_fetch_array($result)){
		$rowCount++;
		$sheet->setCellValue('A'.$rowCount,$row['id']);
		$sheet->setCellValue('B'.$rowCount,$row['auth']);
	}

	$objWriter = new PHPExcel_Writer_Excel2007($objExcel);
	$filename = 'export.xlsx';
	$objWriter->save($filename);

	header('Content-Disposition: attachment; filename="' . $filename . '"');  
	header('Content-Type: application/vnd.openxmlformatsofficedocument.spreadsheetml.sheet');  
	header('Content-Length: ' . filesize($filename));  
	header('Content-Transfer-Encoding: binary');  
	header('Cache-Control: must-revalidate');  
	header('Pragma: no-cache');  
	readfile($filename);  
	return;
	

}

if(isset($_POST['btnGui'])){
	$file = $_FILES['file']['tmp_name'];

	$objReader = PHPExcel_IOFactory::createReaderForFile($file);
	$objReader->setLoadSheetsOnly('Sheet1');

	$objExcel = $objReader->load($file);
	$sheetData = $objExcel->getActiveSheet()->toArray('null',true,true,true);

	$highestRow = $objExcel->setActiveSheetIndex()->getHighestRow();
	//echo $highestRow; 
	
	if($sheetData['1']['A'] == "auth"){
		$col = "A";
	}else if($sheetData['1']['B'] == "auth"){
		$col = "B";
	}else if($sheetData['1']['C'] == "auth"){
		$col = "C";
	}else if($sheetData['1']['D'] == "auth"){
		$col = "D";
	}else if($sheetData['1']['E'] == "auth"){
		$col = "E";
	}else if($sheetData['1']['F'] == "auth"){
		$col = "F";
	}else{
		$col = "null";
		$message = "Not data found";
	}
	if($col != "null")
	{
		for($row = 2; $row<=$highestRow; $row++){
			$auth = $sheetData[$row][$col];
			if(strlen($auth) == 32)
			{
				
				$result_query = $connect_mysqli->query("INSERT INTO auth(auth)
														VALUES ('$auth')
														");
														
			}
		}
		$message = "Finished";
	}
}


?>
<?php include('../admin/include/top.php'); ?>
<div class="row">
    <div class="col-lg-12">
        <ol class="breadcrumb">
            <li>
                <i class="fa fa-dashboard"></i>  <a href="../admin/admin.php">Admin</a>
            </li>
            <li class="active">
                <i class="fa fa-file"></i> <a href="/admin/auth.php">Auth</a>
            </li>
        </ol>
    </div>
</div>

<div class="col-lg-6">
	<h4>
		<label> <?php echo $message; ?></label>
	</h4>
	<h3>
		Excel
	</h3>
	<form method="POST" enctype="multipart/form-data">
		<div class="form-group">
			<input type="file" name="file"><br />
			<button type="submit" name="btnGui">Import</button>
		</div>
	</form>
	<form method="POST">
		<button name="btnExport" type="submit">Xuất file</button>
	</form>
</div>

<div class="col-lg-6">
	<h3>
		DATA AUTH
	</h3>
	<?php
	$result_query = $connect_mysqli->query("SELECT id,auth
                                            FROM  auth
                                            WHERE id > 0
                                            ");
    ?>
    <div class="table-responsive">
		<table class="table table-bordered table-hover">
			<thead>
			    <tr>
			    	<th>STT</th>
			        <th>AUTH</th>
			    </tr>
			</thead>
			<tbody>
				<?php
					while ($result = mysqli_fetch_array($result_query))
					{
						echo "<tr>";
						echo "<td>".$result['id']."</td>";
						echo "<td>".$result['auth']."</td>";
						echo "</tr>";
					}
				?>
			</tbody>
		</table>
	</div>

</div>










<?php include('../admin/include/bot.php'); ?>