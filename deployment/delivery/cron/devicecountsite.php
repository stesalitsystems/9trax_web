<?php
error_reporting(E_ERROR | E_WARNING | E_PARSE);
$conn = pg_connect('host=localhost port=5432 dbname=personal_track user=postgres password=Admin@123') or die ('<b>DATABASE CONNECTIVITY PROBLEM - Error connecting to postgre:</b></ br>'.pg_last_error());
ini_set('memory_limit', '-1');
set_time_limit(0);
date_default_timezone_set('Asia/Calcutta');
if($_POST['data']){
	$post_data = json_decode($_POST['data']);
	if($post_data->email != '' && $post_data->device_count != ''){
		$email = $post_data->email;
		$device_count = $post_data->device_count;
		$sql = "SELECT  count(distinct deviceid) as nodivice
			FROM public.master_device_assign  where user_id=(select user_id from user_login where username='".$email."') and group_id=2 
			group by user_id";
		$query = pg_query($sql);
		$rs = pg_fetch_assoc($query);
		
		if ($rs['nodivice'] == $device_count) {
			$result = array("status"=>"success");
		} else {
			$result = array("status"=>"fail");
		}
	}
	else {
		$result = array("status"=>"invalid");
	}
	echo json_encode($result);
    exit;
}
?>