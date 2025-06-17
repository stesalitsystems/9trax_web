<?php
error_reporting(0);
error_reporting(0);
$conn = pg_connect('host=localhost port=5432 dbname=scrned user=postgres password=G@Th:pa54H"Kh(qqd$Ri') or die ('<b>DATABASE CONNECTIVITY PROBLEM - Error connecting to postgre:</b></ br>'.pg_last_error());
ini_set('memory_limit', '-1');
set_time_limit(0);
date_default_timezone_set('Asia/Calcutta');

$sqldevice = "SELECT table_name FROM information_schema.tables WHERE table_schema='stes' AND table_type='BASE TABLE'";
$querydevice = pg_query($sqldevice);
while($rsdevice = pg_fetch_array($querydevice))
{
	$table_name = $rsdevice['table_name'];
	if (strpos($table_name,'traker_positionaldata_') !== false) {
	  if($table_name != 'traker_positionaldata_unregister_device')
	  {
		//echo $table_name."<br>";
		echo $sql_delete = "DROP TABLE stes.".$table_name."";echo "<br>";
		pg_query($sql_delete);
	   }
	}
	/*$sql = "select * from public.set_corn_for_expaire_account(".$account_id.",1,2)";
	pg_query($sql);*/
}
?>