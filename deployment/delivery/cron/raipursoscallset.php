<?php
error_reporting(E_ERROR | E_WARNING | E_PARSE);
$conn = pg_connect('host=120.138.8.188 port=5432 dbname=ser_ckp user=postgres password=G@Th:pa54H"Kh(qqd$Ri') or die ('<b>DATABASE CONNECTIVITY PROBLEM - Error connecting to postgre:</b></ br>'.pg_last_error());
ini_set('memory_limit', '-1');
set_time_limit(0);
date_default_timezone_set('Asia/Calcutta');
require_once('/var/www/html/ser_ckp/cron/php_excel/excel_reader2.php');
$filename_path = "/var/www/html/ser_ckp/cron/raipur.xls";
$xls = new Spreadsheet_Excel_Reader($filename_path);
$lonlat = '';
for($row=2;$row<=$xls->rowcount();$row++) 
{
	//echo trim($xls->val($row,1))."<br>";die();
	if (trim($xls->val($row,1)) != '')
	{	
		$lineid = 2;
		$serial_no = trim($xls->val($row,1));
		$no1 = trim($xls->val($row,2));
		$no2 = trim($xls->val($row,3));
		$no3 = trim($xls->val($row,4));
		
		$select_serialno = "select id from public.master_device_details where serial_no = '".$serial_no."'";
		$query_serialno = pg_query($select_serialno);
		$rs_serialno = pg_fetch_array($query_serialno);
		$deviceid = $rs_serialno['id'];
		if($deviceid != ''){
			$device_name = trim($xls->val($row,5)).'/SSE/PW/'.trim($xls->val($row,6)).'('.trim($xls->val($row,7)).' - '.trim($xls->val($row,8)).')';
			echo $update = "update stes.master_device_setup SET sos1_no = '".$no1."', sos2_no = '".$no2."',sos3_no = '".$no3."',call1_no = '".$no1."',call2_no = '".$no2."',call3_no = '".$no3."', device_name = '".$device_name."' where deviceid = '".$deviceid."'";
			echo "<br><br>";
			//pg_query($update);
		}
		//echo $insert = "insert into public.gail_line_data(lineid,distance,altitude,latitude,longitude,geom) values ('".$lineid."','".$distance."','".$altitude."','".$latitude."','".$longitude."',ST_GeomFromText('POINT(".$longitude." ".$latitude.")',4326))";
		//echo "<br><br>";
		//pg_query($insert);
	}
}
echo 'DONE';
?>