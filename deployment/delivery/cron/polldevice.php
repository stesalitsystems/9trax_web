<?php
error_reporting(E_ERROR | E_WARNING | E_PARSE);
$conn = pg_connect('host=120.138.8.188 port=5432 dbname=ser_ckp user=postgres password=G@Th:pa54H"Kh(qqd$Ri') or die ('<b>DATABASE CONNECTIVITY PROBLEM - Error connecting to postgre:</b></ br>'.pg_last_error());
ini_set('memory_limit', '-1');
set_time_limit(0);
date_default_timezone_set('Asia/Calcutta');
require_once('/var/www/html/ser_ckp/cron/php_excel/excel_reader2.php');
$filename_path = "/var/www/html/ser_ckp/cron/polldevice.xls";
$xls = new Spreadsheet_Excel_Reader($filename_path);
$lonlat = '';
for($row=2;$row<=$xls->rowcount();$row++) 
{
	//echo trim($xls->val($row,1))."<br>";
	if (trim($xls->val($row,1)) != '')
	{
		$lineid = 2;
		$diviceno = trim($xls->val($row,1));
		$startpole = trim($xls->val($row,2));
		$stoppol = trim($xls->val($row,3));
		$typeuser = trim($xls->val($row,4));
		$blockname = trim($xls->val($row,5));
		if($diviceno != ''){
			$select_serialno = "select id from public.master_device_details where serial_no = '".$diviceno."'";
			$query_serialno = pg_query($select_serialno);
			$rs_serialno = pg_fetch_array($query_serialno);
			$deviceid = $rs_serialno['id'];
			if($deviceid != ''){
				/*echo $insert = "INSERT INTO stes.device_assigne_pole_data(diviceno, deviceid, startpole, stoppol, typeuser, blockname, active)
								VALUES ('{$diviceno}', {$deviceid}, '{$startpole}', '{$stoppol}', '{$typeuser}', '{$blockname}', 1)";*/
				echo $update = "update stes.device_assigne_pole_data set startpole = '{$startpole}', stoppol = '{$stoppol}', typeuser = '{$typeuser}', blockname = '{$blockname}' where diviceno = '{$diviceno}'";
				echo "<br><br>";
				//pg_query($insert);
				//pg_query($update);
			}
		}
	}
}
echo 'DONE';
?>