<?php
$conn = pg_connect('host=localhost port=5432 dbname=stesalit user=postgres password=Admin@123') or die ('<b>DATABASE CONNECTIVITY PROBLEM - Error connecting to postgre:</b></ br>'.pg_last_error());
ini_set('memory_limit', '-1');
set_time_limit(1);
date_default_timezone_set('Asia/Calcutta');

// Transporter Upload 
include("/var/www/html/web/cron/simplexlsx.class.php");
$xlsx = new SimpleXLSX( '/var/www/html/web/cron/newpoint.xlsx' );
$xlsx_sheet = $xlsx->rows(1);
for($i=1;$i<count($xlsx_sheet);$i++)
{
	$r = $xlsx_sheet[$i];
	$old_lat = $r[1];
	$old_lon = $r[2];
	$new_lat = $r[7];
	$new_lon = $r[8]; 
	$time = $r[4]; 
	$date = '2020-01-27';
	$deviceid = 2725;
	
	$update = "update stes.traker_positionaldata set latitude = '".$new_lat."',longitude='".$new_lon."' where deviceid = '".$deviceid."' and currentdate = '".$date."' and currenttime = '".$time."'";
	pg_query($update);
	
	/*$sql = "select * from public.master_transporter where name_e='".$name_e."' and sap_code = '".$sap_code."'";
	$query = pg_query($sql);
	if(pg_num_rows($query) > 0)
	{
		$rs = pg_fetch_array($query);
		$transporter_id = $rs['id'];
		$update = "update public.master_transporter set contactno = '".$contactno."',email='".$email."' where id = '".$transporter_id."'";
		pg_query($update);
		
		$sql_user = "select * from public.usermaster where employee_no='".$sap_code."'";
		$query_user = pg_query($sql_user);
		if(pg_num_rows($query_user) == 0)
		{
			$password = md5('sil123');
			$insert_user = "INSERT INTO public.usermaster(fullname, designation, phone, alternatephoneno,username,email,password,roleid,active,employee_no) values ('".$name_e."','Transporter','".$contactno."','".$contactno."','".$email."','".$email."','".$password."',10,1,'".$sap_code."')";
			pg_query($insert_user);
		}
	}
	else
	{
		echo $insert = "INSERT INTO public.master_transporter(name_e, sap_code, createdby, updateby) values ('".$name_e."','".$sap_code."','1','1')";
		pg_query($insert);		
		$password = md5('sil123');
		$insert_user = "INSERT INTO public.usermaster(fullname, designation, phone, alternatephoneno,username,email,password,roleid,active,employee_no) values ('".$name_e."','Transporter','".$contactno."','".$contactno."','".$email."','".$email."','".$password."',10,1,'".$sap_code."')";
		pg_query($insert_user);
	}*/
}