<?php

include('../function/function_basic.php');
require('Classes/PHPExcel.php');


if(isset($_POST['btnGui'])){
	$file = $_FILES['file']['tmp_name'];

	$objReader = PHPExcel_IOFactory::createReaderForFile($file);
	$objReader->setLoadSheetsOnly('10A1');

	$objExcel = $objReader->load($file);
	$sheetData = $objExcel->getActiveSheet()->toArray('null',true,true,true);

	$highestRow = $objExcel->setActiveSheetIndex()->getHighestRow();
	//echo $highestRow; 
	for($row = 2; $row<=$highestRow; $row++){
		$auth = $sheetData[$row]['B'];
		$result_query = $connect_mysqli->query("INSERT INTO auth(auth)
												VALUES ('$auth')
												");
	}
	echo 'Inserted!';
}

?>
<!DOCTYPE html>
<html>
<head>
	<meta charset="utf-8">
	<meta http-equiv="X-UA-Compatible" content="IE=edge">
	<title>Import data to database</title>
	<link rel="stylesheet" href="">
</head>
<body>
	<form method="POST" enctype="multipart/form-data">
		<input type="file" name="file">
		<button type="submit" name="btnGui">Import</button>
	</form>
</body>
</html>