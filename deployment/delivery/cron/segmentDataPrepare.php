<?php
ini_set('display_errors', 1);
ini_set('log_errors', 1);
error_reporting(E_ALL & ~E_NOTICE);
$conn = pg_connect('host=localhost port=5432 dbname=nfrtsk user=postgres password=DwtwN6J=fc?*') or die ('<b>DATABASE CONNECTIVITY PROBLEM - Error connecting to postgre:</b></ br>'.pg_last_error());
ini_set('memory_limit', '-1');
set_time_limit(0);
date_default_timezone_set('Asia/Calcutta');
$curentdate = date('Y-m-d');
$startdate = date('Y-m-d', strtotime($curentdate .' -1 day'));
$enddate = date('Y-m-d');


$date_from = date("Y-m-d", strtotime('-1 day',strtotime(trim($curentdate)))).' 20:00:00';
$date_from1 = date("Y-m-d", strtotime('-1 day',strtotime(trim($curentdate)))).' 23:59:59';
$date_to = date('Y-m-d', strtotime($curentdate)).' 00:00:00';
$date_to1 = date('Y-m-d', strtotime($curentdate)).' 09:00:00';

// For Patrolman Time segment array //
$patrolman_time_segment_Arr = array();
$startTime = new DateTime($date_from);
$endTime = new DateTime($date_to1);
$i=0;
while ($startTime < $endTime) {
	$patrolman_time_segment_Arr[$i]['starttime'] = $startTime->format('Y-m-d H:i:s');
	$patrolman_time_segment_Arr[$i]['endtime'] = $startTime->modify('+30 minutes')->format('Y-m-d H:i:s');
	$i++;
}
//================================//

// For Keyman Time segment array //
$keyman_time_segment_Arr = array();
$startTime = new DateTime($date_to);
$endTime = new DateTime($date_to1);
$i=0;
while ($startTime < $endTime) {
	$keyman_time_segment_Arr[$i]['starttime'] = $startTime->format('Y-m-d H:i:s');
	$keyman_time_segment_Arr[$i]['endtime'] = $startTime->modify('+30 minutes')->format('Y-m-d H:i:s');
	$i++;
}
//echo "<pre>";print_r($keyman_time_segment_Arr);echo "</pre>";
//exit;
//================================//


$schemaname = 'stes';
$user_id = 199;
$group_id = 3;
$sql_devicelists = "select sup_pid ,sup_gid ,serial_no,imei_no,did,mobile_no,warranty_date,linked,parent_id,user_id,issudate,
refunddate,active,issold,apply_scheam,group_id,role_id,email,address,pincode,state_name,country,username,firstname,
lastname,organisation,group_name,'' as list_item, '' as list_item_name from public.get_divice_details_record_for_list('".$schemaname."',".$user_id.") where sup_gid = $group_id and user_id=".$user_id." and active = 1 order by did asc";
$query_devicelists = pg_query($sql_devicelists);
$row_devicelists = pg_fetch_all($query_devicelists);
if(count($row_devicelists)>0)
{
	foreach($row_devicelists as $devicelist_each)
	{
		if($devicelist_each['did'] != '')
		{
			if($devices == "{"){
				$devices .= $devicelist_each['did'];
				$dids .= $devicelist_each['did'];
			}
			else{
				$devices .= $devicelist_each['did'].",";
				$dids .= $devicelist_each['did'].",";
			}
		}
	}
}
$dids = substr($dids,0,-1);
$devices_arr = explode(',',$dids);
$patrolman_activity = array();
$patrolman_index=0;
$keyman_activity = array();
$keyman_index=0;
for($dv=0;$dv<count($devices_arr);$dv++)
{
	$device_id = $devices_arr[$dv];
	$sql_device_name = "select a.device_name,b.serial_no 
								FROM stes.master_device_setup as a
								left join stes.master_device_details as b on (a.deviceid=b.superdevid)
								where a.deviceid = '".$device_id."'";
	$query_device_name = pg_query($sql_device_name);
	$row_device_name = pg_fetch_array($query_device_name);
	$device_name = $row_device_name['device_name'];
	$device_name_arr = explode('/',$device_name);
	$user_type = $device_name_arr[0];
	$serialno = $row_device_name['serial_no'];
	$sql_device_assignment = "SELECT count(*) as counter  FROM public.master_device_assign where deviceid='".$device_id."' and group_id=2 and active = 1";
	$query_device_assignment = pg_query($sql_device_assignment);
	$row_device_assignment = pg_fetch_array($query_device_assignment);
	if(pg_num_rows($query_device_assignment) > 0)
	{
		if($user_type == 'Patrolman')
		{
			$patrolman_activity[$patrolman_index]['deviceid'] = $device_id;
			$patrolman_activity[$patrolman_index]['device_name'] = $device_name;
			$patrolman_activity[$patrolman_index]['time_segment_details'] = array();
			for($i=0;$i<count($patrolman_time_segment_Arr);$i++)
			{
				$date_from = $patrolman_time_segment_Arr[$i]['starttime'];
				$date_to = $patrolman_time_segment_Arr[$i]['endtime'];
				$sql_record = "select a.*, mdd.serial_no,msd.device_name from public.get_histry_play_data_summary('".$device_id."', '".$date_from."'::timestamp without time zone, '".$date_to."'::timestamp without time zone) as a left join stes.master_device_details as mdd on (mdd.superdevid = a.deviceid) left join stes.master_device_setup as msd on (msd.deviceid = a.deviceid)";
				$query_record = pg_query($sql_record);
				$row_record = pg_fetch_array($query_record);				
				$patrolman_activity[$patrolman_index]['time_segment_details'][$i]['starttime'] = $patrolman_time_segment_Arr[$i]['starttime'];
				$patrolman_activity[$patrolman_index]['time_segment_details'][$i]['endtime'] = $patrolman_time_segment_Arr[$i]['endtime'];
				if($row_record['distance_cover'] == '')
				{
					$row_record['distance_cover'] = 0;
				}
				$patrolman_activity[$patrolman_index]['time_segment_details'][$i]['distance_cover'] = round($row_record['distance_cover']/1000,3);				
			}
			$sql_device_assignment_details = "select a.parent_user_id,a.current_user_id,b.organisation as pwy,c.organisation as section
											from public.device_asign_details as a
											left join public.user_login as b on (a.parent_user_id = b.user_id)
											left join public.user_login as c on (a.current_user_id = c.user_id)
											where a.serial_no='".$serialno."'";
			$query_device_assignment_details = pg_query($sql_device_assignment_details);
			$row_device_assignment_details = pg_fetch_array($query_device_assignment_details);
			$patrolman_activity[$patrolman_index]['pwy'] = $row_device_assignment_details['pwy'];
			$patrolman_activity[$patrolman_index]['section'] = $row_device_assignment_details['section'];
			$patrolman_activity[$patrolman_index]['serial_no'] = $serial_no;
			$patrolman_index++;
		}
		else
		{
			$keyman_activity[$keyman_index]['deviceid'] = $device_id;
			$keyman_activity[$keyman_index]['device_name'] = $device_name;
			$keyman_activity[$keyman_index]['time_segment_details'] = array();
			for($i=0;$i<count($keyman_time_segment_Arr);$i++)
			{
				$date_from = $keyman_time_segment_Arr[$i]['starttime'];
				$date_to = $keyman_time_segment_Arr[$i]['endtime'];
				$sql_record = "select a.*, mdd.serial_no,msd.device_name from public.get_histry_play_data_summary('".$device_id."', '".$date_from."'::timestamp without time zone, '".$date_to."'::timestamp without time zone) as a left join stes.master_device_details as mdd on (mdd.superdevid = a.deviceid) left join stes.master_device_setup as msd on (msd.deviceid = a.deviceid)";
				$query_record = pg_query($sql_record);
				$row_record = pg_fetch_array($query_record);				
				$keyman_activity[$keyman_index]['time_segment_details'][$i]['starttime'] = $keyman_time_segment_Arr[$i]['starttime'];
				$keyman_activity[$keyman_index]['time_segment_details'][$i]['endtime'] = $keyman_time_segment_Arr[$i]['endtime'];
				if($row_record['distance_cover'] == '')
				{
					$row_record['distance_cover'] = 0;
				}
				$keyman_activity[$keyman_index]['time_segment_details'][$i]['distance_cover'] = round($row_record['distance_cover']/1000,3);
				$serialno = $row_record['serial_no'];
				$sql_device_assignment_details = "select a.parent_user_id,a.current_user_id,b.organisation as pwy,c.organisation as section
												from public.device_asign_details as a
												left join public.user_login as b on (a.parent_user_id = b.user_id)
												left join public.user_login as c on (a.current_user_id = c.user_id)
												where a.serial_no='".$serialno."'";
				$query_device_assignment_details = pg_query($sql_device_assignment_details);
				$row_device_assignment_details = pg_fetch_array($query_device_assignment_details);
				$patrolman_activity[$patrolman_index]['pwy'] = $row_device_assignment_details['pwy'];
				$patrolman_activity[$patrolman_index]['section'] = $row_device_assignment_details['section'];
				$patrolman_activity[$patrolman_index]['serial_no'] = $serial_no;
			}
			$sql_device_assignment_details = "select a.parent_user_id,a.current_user_id,b.organisation as pwy,c.organisation as section
											from public.device_asign_details as a
											left join public.user_login as b on (a.parent_user_id = b.user_id)
											left join public.user_login as c on (a.current_user_id = c.user_id)
											where a.serial_no='".$serialno."'";
			$query_device_assignment_details = pg_query($sql_device_assignment_details);
			$row_device_assignment_details = pg_fetch_array($query_device_assignment_details);
			$patrolman_activity[$patrolman_index]['pwy'] = $row_device_assignment_details['pwy'];
			$patrolman_activity[$patrolman_index]['section'] = $row_device_assignment_details['section'];
			$patrolman_activity[$patrolman_index]['serial_no'] = $serial_no;
			$keyman_index++;
		}
	}
}

$data_json = json_encode($patrolman_activity,JSON_FORCE_OBJECT);
$prevdatestring = date('Ymd');
$filename = "patrolman_activity_".$prevdatestring.".json";
$filepath = '/var/www/html/nfrtsk/activity_json/'.$filename;
$fp = fopen($filepath, 'w') or die('not open file');
fwrite($fp, json_encode($data_json)) or die('not write file');
fclose($fp);

$data_json = json_encode($keyman_activity,JSON_FORCE_OBJECT);
$prevdatestring = date('Ymd');
$filename = "keyman_activity_".$prevdatestring.".json";
$filepath = '/var/www/html/nfrtsk/activity_json/'.$filename;
$fp = fopen($filepath, 'w') or die('not open file');
fwrite($fp, json_encode($data_json)) or die('not write file');
fclose($fp);

echo "<pre>";print_r($patrolman_activity);echo "</pre>";
exit;
?>