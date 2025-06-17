<?php
error_reporting(E_ERROR | E_WARNING | E_PARSE);
$conn = pg_connect('host=localhost port=5432 dbname=nfrtsk user=postgres password=DwtwN6J=fc?*') or die ('<b>DATABASE CONNECTIVITY PROBLEM - Error connecting to postgre:</b></ br>'.pg_last_error());
ini_set('memory_limit', '-1');
set_time_limit(0);
date_default_timezone_set('Asia/Calcutta');
require_once("simplexlsx.class.php");
if($_REQUEST['submit'] == 'submit')
{
	$target_dir = "/var/www/html/nfrtsk/uploads/";
	$target_file = $target_dir . basename($_FILES["fl"]["name"]);
	if (move_uploaded_file($_FILES["fl"]["tmp_name"], $target_file)) 
	{
		$dataFile =  $target_file;		
		$xlsx = new SimpleXLSX($dataFile);
		$xlsx_sheet = $xlsx->rows(1);
		$totxlsxrow = count($xlsx_sheet);
		for($i=1;$i<$totxlsxrow;$i++)
		{
			$r = $xlsx_sheet[$i];
			//print_r($r);
			$imei_no= trim($r[0]);
			$user_type= strtoupper(trim($r[1]));
			$start_pole= trim($r[2]);
			$end_pole= trim($r[3]);
			//$start_pole = number_format(trim($r[2]), 3, '.', '');
			//$end_pole =  number_format(trim($r[3]), 3, '.', '');
			
			$walking_distance= trim($r[4]);
			
			$start_time= trim($r[5]);
			$starttimedetArr = explode(" ",$start_time);
			$starttime = $starttimedetArr[0].':00';
			
			$end_time= trim($r[6]);
			$endtimedetArr = explode(" ",$end_time);
			$endtime = $endtimedetArr[0].':00';
			
			
			if($user_type != 'STOCK')
			{
				echo $sql="select * from stes.device_assigne_pole_data where diviceno='".$imei_no."' and startpole = '".$start_pole."' and stoppol = '".$end_pole."'";
				echo "<br>";
				$query = pg_query($sql);
				while($rs = pg_fetch_array($query))
				{
					$id = $rs['id'];
					echo $update = "update stes.device_assigne_pole_data set walk_org_distance='".$walking_distance."',starttime='".$starttime."',endtime='".$endtime."' where id='".$id."'";
					echo "<br>";
					pg_query($update);
					
				}
				//echo $sql."<br>";
			}
			
		}
	}
	echo "Exception Data Upload Successfully";
}
/*$dataFile =  '/var/www/html/scrbza/cron/Tathagata- device pole to Pole assignment, its distance and time of duty.xlsx';		
$xlsx = new SimpleXLSX($dataFile);
$xlsx_sheet = $xlsx->rows(1);
$totxlsxrow = count($xlsx_sheet);
for($i=5;$i<$totxlsxrow;$i++)
{
	$r = $xlsx_sheet[$i];
	//echo "<pre>";print_r($r);echo "</pre>";exit;
	$block_section = trim($r[10]);
	$start_pole = trim($r[11]);
	$end_pole = trim($r[14]);
	$km = trim($r[17]);
	$starttimedet = trim($r[18]);
	$starttimedetArr = explode(" ",$starttimedet);
	$starttime = $starttimedetArr[0].':00';
	$endtimedet = trim($r[19]);
	$endtimedetArr = explode(" ",$endtimedet);
	$endtime = $endtimedetArr[0].':00';
	$imei_no= trim($r[2]);
	$petrolmanno = trim($r[9]);
	$petrolmantype = trim($r[8]);
	if($petrolmantype != 'STOCK')
	{
		$sql="select * from stes.device_assigne_pole_data where startpole='".$start_pole."' and stoppol='".$end_pole."' and blockname='".$block_section."' and diviceno='".$imei_no."'";
		$query = pg_query($sql);
		while($rs = pg_fetch_array($query))
		{
			$id = $rs['id'];
			echo $update = "update stes.device_assigne_pole_data set walk_org_distance='".$km."',starttime='".$starttime."',endtime='".$endtime."' where id='".$id."'";
			pg_query($update);
		}
		//echo $sql."<br>";
	}
}*/
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1" />
<title>File Upload</title>
</head>

<body>
<form name="frm" id="frm" method="post" action="http://nfrtsk.9trax.com/cron/distanc_time_insert.php" enctype="multipart/form-data">
<input type="file" name="fl" id="fl" />
<input type="submit" name="submit" value="submit" />
</form>
</body>
</html>