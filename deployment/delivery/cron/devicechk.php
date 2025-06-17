<?php
error_reporting(E_ERROR | E_WARNING | E_PARSE);
$conn = pg_connect('host=120.138.8.188 port=5432 dbname=ersdah_new user=postgres password=G@Th:pa54H"Kh(qqd$Ri') or die ('<b>DATABASE CONNECTIVITY PROBLEM - Error connecting to postgre:</b></ br>'.pg_last_error());
ini_set('memory_limit', '-1');
set_time_limit(0);
date_default_timezone_set('Asia/Calcutta');
require_once('/var/www/html/ersdah_magikk/cron/php_excel/excel_reader2.php');
$filename_path = "/var/www/html/ersdah_magikk/cron/devicechk.xls";
$xls = new Spreadsheet_Excel_Reader($filename_path);
$lonlat = '';
$a=array();
for($row=2;$row<=$xls->rowcount();$row++) 
{
	//echo trim($xls->val($row,1))."<br>";
	if (trim($xls->val($row,1)) != '')
	{
		$lineid = 2;
		$serial_no = trim($xls->val($row,1));
		
		$select_serialno = "select id from public.master_device_details where serial_no = '".$serial_no."' and warranty_date = '2021-06-24'";
		$query_serialno = pg_query($select_serialno);
		$rs_serialno = pg_fetch_array($query_serialno);
		$deviceid = $rs_serialno['id'];
		/*if($deviceid != ''){
			echo $deviceid." ".$serial_no." OK<br>";
		}
		else {
			echo $deviceid." ".$serial_no." NOT OK<br>";
		}*/
		if (in_array($serial_no, $a))
		{
			echo $deviceid." ".$serial_no." NOT OK<br>";
		}
		else {
			array_push($a,$serial_no);
		}
	}
}
echo 'DONE';
?>