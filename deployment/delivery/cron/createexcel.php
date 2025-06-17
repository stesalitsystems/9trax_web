<?php
//ini_set("display_errors", 1);
//ini_set('log_errors', 1);
//error_reporting(E_ALL);
//define("CONSTANT", "ENT_SUBSTITUTE");
error_reporting(0);
$conn = pg_connect('host=localhost port=5432 dbname=nfrtsk user=postgres password=DwtwN6J=fc?*') or die ('<b>DATABASE CONNECTIVITY PROBLEM - Error connecting to postgre:</b></ br>'.pg_last_error());
//ini_set('memory_limit', '-1');
set_time_limit(0);
date_default_timezone_set('Asia/Calcutta');
include_once("/var/www/html/nfrtsk/cron/xlsxwriter.class.php");
$curentdate = date('Y-m-d');

/***** Exception Summary report generate and save on previous daya *******/
$date = date('Y-m-d', strtotime($curentdate .' -1 day'));
$typeofuser = 'All';
$schemaname = 'stes';
$user_id = 199;
$sql = "select '".$date."' as date,lefttable.user_id, organisation from  public.get_right_panel_data('{$schemaname}','{$date}',{$user_id}) as lefttable left join public.user_login as ul  on lefttable.user_id = ul.user_id where lefttable.group_id = 2 and lefttable.deviceid IS NOT NULL group by lefttable.user_id, organisation order by organisation asc";
$query = pg_query($conn,$sql);
$row = pg_fetch_all($query);
for($i=0;$i<count($row);$i++)
{
	$sql_pwi = "select b.organisation as pwi
							from public.user_login as a
							left join public.user_login as b on (a.parent_id = b.id)
							where a.organisation='".$row[$i]['organisation']."'";
	$query_pwi = pg_query($sql_pwi);
	$row_pwi = pg_fetch_array($query_pwi);
	$row[$i]['pwi'] = $row_pwi['pwi'];
	$new_date = $row[$i]['date'];
	$user_id = $row[$i]['user_id'];
	$devices = '';
	$dids='';
	$sql_devicelists = "select sup_pid ,sup_gid ,serial_no,imei_no,did,mobile_no,warranty_date,linked,parent_id,user_id,issudate,
						refunddate,active,issold,apply_scheam,group_id,role_id,email,address,pincode,state_name,country,username,firstname,
						lastname,organisation,group_name,'' as list_item, '' as list_item_name from public.get_divice_details_record_for_list('stes',".$user_id.") where user_id=".$user_id." and active = 1 order by did asc";
	$query_devicelists = pg_query($sql_devicelists);
	$row_devicelists = pg_fetch_all($query_devicelists);
	$row[$i]['total_devices'] = count($row_devicelists);
	$row[$i]['off_devices'] = count($row_devicelists);
	$beat_covered = 0;$beat_not_covered = 0;$active_device=0;$inactive_device = 0;$not_alloted_devices = 0;$duration = '00:00:00';
	if(count($row_devicelists)>0)
	{
		foreach($row_devicelists as $devicelist_each)
		{
			if($devices == ""){
				$devices .= $devicelist_each['did'];
				$dids .= $devicelist_each['did'];
			}
			else{
				$devices .= ",".$devicelist_each['did'];
				$dids .= ",".$devicelist_each['did'];
			}
			$deviceid = $devicelist_each['did'];
			$sql_mdddevicename = "select device_name from stes.master_device_setup where deviceid='".$deviceid."'";
			$query_mdddevicename = pg_query($sql_mdddevicename);
			$row_mdddevicename = pg_fetch_array($query_mdddevicename);
			$mdddevicename = $row_mdddevicename['device_name'];
			$mdddevicename_arr = explode("/",$mdddevicename);
			if (strpos(strtolower($mdddevicename), 'stock') !== false) 
			{
				$not_alloted_devices++;
			}
			else
			{
				$sql_record1 = "select a.*, mdd.serial_no from public.get_histry_play_data_summary('".$deviceid."', '".$new_date." 00:00:00'::timestamp without time zone, '".$date." 23:59:59'::timestamp without time zone) as a left join stes.master_device_details as mdd on (mdd.superdevid = a.deviceid)";
				$query_record1 = pg_query($sql_record1);
				$row_record1 = pg_fetch_all($query_record1);
				$distance_cover = $row_record1[0]['distance_cover'];
				if(count($row_record1)>0)
				{
					$active_device++;
					$sql_timedetails = "select walk_org_distance from stes.device_assigne_pole_data where deviceid='".$deviceid."' and startpole <> '0' and stoppol <> '0' and walk_org_distance is not null";
					$query_timedetails = pg_query($sql_timedetails);
					$row_timedetails = pg_fetch_array($query_timedetails);
					$actual_distance = $row_timedetails['walk_org_distance'];
					if(($distance_cover >= $actual_distance) && ($actual_distance != '') && ($distance_cover > 0))
					{
						$beat_covered++;
					}
					else
					{
						$beat_not_covered++;
					}
					$actual_duration = $row_record1[0]['duration'];
					if($actual_duration != '')
					{
						$duration = sum_the_time($duration,$actual_duration);
					}
				}
				else
				{
					$inactive_device++;
				}
			}
		}
	}
	$row[$i]['beat_covered'] = $beat_covered;
	$row[$i]['beat_not_covered'] = $beat_not_covered;
	$row[$i]['not_alloted_devices'] = $not_alloted_devices;
	$row[$i]['duration'] = $duration;
	$tot_dev = $active_device+$inactive_device;
	$row[$i]['total_devices'] = $tot_dev;
	if($beat_covered != '0')
	{ 
		$beat_coverage_percentage = (($beat_not_covered/$tot_dev)*100);
		$beat_coverage_percentage = number_format($beat_coverage_percentage,2);
	}
	else
	{
		$beat_coverage_percentage = '0';
	}
	$row[$i]['beat_coverage_percentage'] = $beat_coverage_percentage;
}
$result = array();
for($i=0;$i<count($row);$i++)
{
	$result[$i]['date'] = $row[$i]['date'];
	$result[$i]['PWI'] = $row[$i]['pwi'];
	$result[$i]['section_name'] = $row[$i]['organisation'];
	$result[$i]['off_device'] = $row[$i]['off_devices'];
	$result[$i]['beat_not_covered'] = $row[$i]['beat_not_covered'];
	$result[$i]['beat_covered'] = $row[$i]['beat_covered'];
	
	$result[$i]['device_not_alloted'] = $row[$i]['not_alloted_devices'];
	$result[$i]['total_device'] = $row[$i]['total_devices'];
	$result[$i]['remark'] = '';
}
//echo "<pre>";print_r($result);echo "</pre>";
$fp = fopen('/var/www/html/nfrtsk/cron/Work_Status_Count_Report.csv', 'w');
$header = array(
	'Date' => 'date',
	'PWI'=>'string',
	'Section Name'=>'string',
	'Off Device'=>'string',
	'Beat Not Covered'=>'string',
	'Beat Covered Successfully'=>'string',
	'Device Not Alloted'=>'string',
	'Total Device'=>'string',
	'Remarks'=>'string',
);
$list = array();
array_push($list,$header);
for($i=0;$i<count($result);$i++)
{
	array_push($list,$result[$i]);
}
foreach ($list as $fields) {
    fputcsv($fp, $fields);
}
fclose($fp);
$devices = '';
$dids = '';
/*$header = array(
	'Date' => 'date',
	'PWI'=>'string',
	'Section Name'=>'string',
	'Off Device'=>'string',
	'Beat Not Covered'=>'string',
	'Beat Covered Successfully'=>'string',
	'Device Not Alloted'=>'string',
	'Total Device'=>'string',
	'Effective Hours Of Working'=>'string',
	'Status'=>'string',
	'Remarks'=>'string',
);
$writer = new XLSXWriter();
$writer->setAuthor('Some Author');
$writer->writeSheet($result,'Sheet1',$header);
$writer->writeToFile('Work_Status_Count_Report.xlsx');
$devices = '';
$dids = '';
echo "hi";exit;*/
/**************************Distance Exception Report*********************************/
$date = date('Y-m-d', strtotime($curentdate .' -1 day'));
$typeofuser = 'All';
$schemaname = 'stes';
$user_id = 199;
$group_id = 3;
$date_from = $date;
$date_to = $date;
$time_from = "00:00:00";//date("H:i:s", strtotime(trim($this->input->post('date_from'))));
$time_to = "23:59:00";//date("H:i:s", strtotime(trim($this->input->post('date_to'))));
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
			$devices .= $devicelist_each['did'].",";
			$dids .= $devicelist_each['did'].",";
		}
	}
}
$dids = substr($dids,0,-1);
$sql_record = "select mddd,mdddevicename,mddserialno,divicename, result_date, deviceid, acting_trip, start_time, endtime,duration, distance_cover, sos_no, alert_no, call_no, totalnoofstop, totalstoptime, polno, rd, dv, devicename, acting_triped, starttime,   end_time, duration1, distancecover, sosno, alertno, callno, totalnoof_stop,  totalsto_ptime, pol_no, polename1, polnoend, polenameend1, polename, polenameend, genid from (( SELECT distinct deviceid as mddd,(SELECT device_name FROM ".$schemaname.".master_device_setup  where deviceid=ax.deviceid and id=(SELECT  max(id) FROM ".$schemaname.".master_device_setup where inserttime::date<='$date_to'::date  and deviceid=ax.deviceid )) as mdddevicename,(SELECT  coalesce(serial_no,'Absent')  FROM ".$schemaname.".master_device_details  where superdevid=ax.deviceid or id=ax.deviceid) as mddserialno   FROM ".$schemaname.".master_device_assign as ax where deviceid in ($dids) and group_id=2 )  masterdevice left outer join (select * from public.trip_spesified_device where (genid,deviceid,result_date,acting_trip) in(select max(genid),deviceid,result_date,acting_trip from public.trip_spesified_device where  totalstoptime > '00:00:00' and deviceid in ($dids) and (result_date||' '||start_time >= '".$date_from." ".$time_from."' and result_date||' '||end_time <= '".$date_to." ".$time_to."') group by deviceid,result_date,acting_trip) order by devicename,result_date,acting_trip)resultdivice on masterdevice.mddd=resultdivice.deviceid)resultset order by result_date,mddd asc";
$query_record = pg_query($sql_record);
$row_record = pg_fetch_all($query_record);
$alldatanew = array();
for($i=0;$i<count($row_record);$i++)
{
	$deviceidnew = $row_record[$i]['deviceid'];
	if($deviceidnew != '')
	{
		$serialno = $row_record[$i]['mddserialno'];
		$sql_device_assignment = "select a.parent_user_id,a.current_user_id,b.organisation as pwy,
									c.organisation as section,d.startpole,d.stoppol
									from public.device_asign_details as a
									left join public.user_login as b on (a.parent_user_id = b.user_id)
									left join public.user_login as c on (a.current_user_id = c.user_id)
									left join stes.device_assigne_pole_data as d on (a.serial_no = d.diviceno)
									where a.serial_no='".$serialno."'
									limit 1";
		$query_device_assignment = pg_query($sql_device_assignment);
		$row_device_assignment = pg_fetch_array($query_device_assignment);
		$row_record[$i]['pwy'] = $row_device_assignment['pwy'];
		$row_record[$i]['section'] = $row_device_assignment['section'];
		$devicename = $row_record[$i]['mdddevicename'];
		if($devicename != '')
		{
			$devicenameArr = explode(':',$devicename);
			$poledetailsnew = trim($devicenameArr[1]);
			$poledetailsnewArr = explode('(',$poledetailsnew);
			$polenamenew = trim($poledetailsnewArr[1]);
			$polenamenew = str_replace(')','',$polenamenew);
			$polenamenewArr = explode('-',$polenamenew);
			$row_record[$i]['startpole'] = $polenamenewArr[0];
			$row_record[$i]['stoppol'] = $polenamenewArr[1];
			$row_record[$i]['bit'] = $row_record[$i]['startpole'].'-'.$row_record[$i]['stoppol'];
			$sql_actual_distance = "select walk_org_distance 
									from stes.device_assigne_pole_data where deviceid='".$deviceidnew."'
									and endtime is not null and starttime is not null";
			$query_actual_distance = pg_query($sql_actual_distance);
			$row_actual_distance = pg_fetch_array($query_actual_distance);
			$row_record[$i]['walk_org_distance_out'] = $row_actual_distance['walk_org_distance'];
			$datetimefrom = $row_record[$i]['result_date']." ".$row_record[$i]['start_time'];
			$datetimeto = $row_record[$i]['result_date']." ".$row_record[$i]['endtime'];
			$from_datetime = date("Y-m-d H:i:s",strtotime($datetimefrom));  
			$to_datetime = date("Y-m-d  H:i:s",strtotime($datetimeto));
			$sql_history = "select distance_cover from public.get_histry_play_data_summary('".$deviceidnew."','".$from_datetime."','".$to_datetime."')";
			$query_history = pg_query($sql_history);
			$row_history = pg_fetch_array($query_history);
			$row_record[$i]['totaldistancecovere_taa'] = $row_history['distance_cover'];
			$row_record[$i]['deviation_distance1'] = ($row_actual_distance['walk_org_distance']*1000) - $row_record[$i]['totaldistancecovere_taa'];
			if($row_record[$i]['deviation_distance1'] != '')
			{
				if($row_record[$i]['totaldistancecovere_taa'] > ($row_actual_distance['walk_org_distance']*1000) && $row_actual_distance['walk_org_distance'] != '')
				{
					$row_record[$i]['status'] = 'Duty Completed';
				}
				else if(hasMinusSign($row_record[$i]['deviation_distance1']))
				{
					$row_record[$i]['status'] = 'Duty Not Completed';
				}
				else if($row_record[$i]['deviation_distance1'] > 0)
				{
					$row_record[$i]['status'] = 'Duty Not Completed';
				}
				else
				{
					$row_record[$i]['status'] = 'Duty Completed';
				}
			}
			else
			{
				$row_record[$i]['walk_org_distance_out'] = '0.0';
				$row_record[$i]['deviation_distance1'] = '0.0';
				$row_record[$i]['status'] = 'Duty Not Completed';
			}
		}
	}
	else
	{
		$row_record[$i]['walk_org_distance_out'] = '0.0';
		$row_record[$i]['deviation_distance1'] = '0.0';
		$row_record[$i]['status'] = 'Duty Not Completed';
	}
}
for($dv=0;$dv<count($row_record);$dv++)
{
	if($row_record[$dv]['mdddevicename'] != '' && $row_record[$dv]['result_date'] != '')
	{
		if($row_record[$dv]['status'] != 'Duty Completed')
		{
			$mdddevicename_arr = explode("/",$row_record[$dv]['mdddevicename']);
			if (strpos(strtolower($mdddevicename_arr[0]), 'stock') !== false) {
				$type = 'Stock';
			}
			else if (strpos(strtolower($mdddevicename_arr[0]), 'keyman') !== false) {
				$type = 'Keyman';
			}
			else if (strpos(strtolower($mdddevicename_arr[0]), 'patrolman') !== false) {
				$type = 'Patrolman';
			}
			else
			{
				$type = 'NA';
			}
			$row_record[$dv]['usertype'] = $type;
			array_push($alldatanew,$row_record[$dv]);
		}
	}
}
$result = array();
for($i=0;$i<count($alldatanew);$i++)
{
	$result[$i]['date'] = date("d-m-Y", strtotime($alldatanew[$i]['result_date']));;
	$result[$i]['mdddevicename'] = $alldatanew[$i]['mdddevicename'];
	$result[$i]['mddserialno'] = $alldatanew[$i]['mddserialno'];
	$result[$i]['pwy'] = $alldatanew[$i]['pwy'];
	$result[$i]['section'] = $alldatanew[$i]['section'];
	$result[$i]['bit'] = $alldatanew[$i]['bit'];
	$result[$i]['usertype'] = $alldatanew[$i]['usertype'];
	$result[$i]['acting_trip'] = $alldatanew[$i]['acting_trip'];
	$result[$i]['startpole'] = $alldatanew[$i]['startpole'];
	$result[$i]['stoppol'] = $alldatanew[$i]['stoppol'];
	$result[$i]['distancecover'] = round($alldatanew[$i]['distancecover']/1000,2).' km';
	$result[$i]['walk_org_distance_out'] = $alldatanew[$i]['walk_org_distance_out'];
	$result[$i]['deviation_distance1'] = round($alldatanew[$i]['deviation_distance1']/1000,2).' km';
	$result[$i]['status'] = $alldatanew[$i]['status'];
}
$fp = fopen('/var/www/html/nfrtsk/cron/Distance_Exception_Report.csv', 'w');
$header = array(
	'Date' => 'date',
	'DeviceName'=>'string',
	'Device SerialNo'=>'string',
	'SSE/PWAY'=>'string',
	'Section'=>'string',
	'BIT'=>'string',
	'UserType'=>'string',
	'TripNo'=>'string',
	'StartPole'=>'string',
	'EndPole'=>'string',
	'Travelled Distance'=>'string',
	'Actual Distance'=>'string',
	'Deviation Distance'=>'string',
	'Status'=>'string',
);
$list = array();
array_push($list,$header);
for($i=0;$i<count($result);$i++)
{
	array_push($list,$result[$i]);
}
foreach ($list as $fields) {
    fputcsv($fp, $fields);
}
fclose($fp);
/*$writer = new XLSXWriter();
$writer->setAuthor('Some Author');
$writer->writeSheet($result,'Sheet1',$header);
$writer->writeToFile('Distance_Exception_Report.xlsx');*/
$devices = '';
$dids = '';
//echo "hi";exit;
/***********************************************************/
/**************************Time Exception Report*********************************/
$date = date('Y-m-d', strtotime($curentdate .' -1 day'));
$typeofuser = 'All';
$schemaname = 'stes';
$user_id = 199;
$group_id = 3;
$date_from = $date;
$date_to = $date;
$time_from = "00:00:00";//date("H:i:s", strtotime(trim($this->input->post('date_from'))));
$time_to = "23:59:00";//date("H:i:s", strtotime(trim($this->input->post('date_to'))));
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
			$devices .= $devicelist_each['did'].",";
			$dids .= $devicelist_each['did'].",";
		}
	}
}
$dids = substr($dids,0,-1);
$sql_record = "select mddd,mdddevicename,mddserialno,divicename, result_date, deviceid, acting_trip, start_time, endtime,duration, distance_cover, sos_no, alert_no, call_no, totalnoofstop, totalstoptime, polno, rd, dv, devicename, acting_triped, starttime,   end_time, duration1, distancecover, sosno, alertno, callno, totalnoof_stop,  totalsto_ptime, pol_no, polename1, polnoend, polenameend1, polename, polenameend, genid from (( SELECT distinct deviceid as mddd,(SELECT device_name FROM ".$schemaname.".master_device_setup  where deviceid=ax.deviceid and id=(SELECT  max(id) FROM ".$schemaname.".master_device_setup where inserttime::date<='$date_to'::date  and deviceid=ax.deviceid )) as mdddevicename,(SELECT  coalesce(serial_no,'Absent')  FROM ".$schemaname.".master_device_details  where superdevid=ax.deviceid or id=ax.deviceid) as mddserialno   FROM ".$schemaname.".master_device_assign as ax where deviceid in ($dids) and group_id=2 )  masterdevice left outer join (select * from public.trip_spesified_device where (genid,deviceid,result_date,acting_trip) in(select max(genid),deviceid,result_date,acting_trip from public.trip_spesified_device where  totalstoptime > '00:00:00' and deviceid in ($dids) and (result_date||' '||start_time >= '".$date_from." ".$time_from."' and result_date||' '||end_time <= '".$date_to." ".$time_to."') group by deviceid,result_date,acting_trip) order by devicename,result_date,acting_trip)resultdivice on masterdevice.mddd=resultdivice.deviceid)resultset order by result_date,mddd asc";
$query_record = pg_query($sql_record);
$row_record = pg_fetch_all($query_record);
$alldatanew = array();
for($i=0;$i<count($row_record);$i++)
{
	$deviceidnew = $row_record[$i]['deviceid'];
	if($deviceidnew != '')
	{
		$serialno = $row_record[$i]['mddserialno'];
		$sql_device_assignment = "select a.parent_user_id,a.current_user_id,b.organisation as pwy,
									c.organisation as section,d.startpole,d.stoppol
									from public.device_asign_details as a
									left join public.user_login as b on (a.parent_user_id = b.user_id)
									left join public.user_login as c on (a.current_user_id = c.user_id)
									left join stes.device_assigne_pole_data as d on (a.serial_no = d.diviceno)
									where a.serial_no='".$serialno."'
									limit 1";
		$query_device_assignment = pg_query($sql_device_assignment);
		$row_device_assignment = pg_fetch_array($query_device_assignment);
		$row_record[$i]['pwy'] = $row_device_assignment['pwy'];
		$row_record[$i]['section'] = $row_device_assignment['section'];
		$devicename = $row_record[$i]['mdddevicename'];
		$devicenameArr = explode(':',$devicename);
		$poledetailsnew = trim($devicenameArr[1]);
		$poledetailsnewArr = explode('(',$poledetailsnew);
		$polenamenew = trim($poledetailsnewArr[1]);
		$polenamenew = str_replace(')','',$polenamenew);
		$polenamenewArr = explode('-',$polenamenew);
		$row_record[$i]['startpole'] = $polenamenewArr[0];
		$row_record[$i]['stoppol'] = $polenamenewArr[1];
		$row_record[$i]['bit'] = $row_record[$i]['startpole'].'-'.$row_record[$i]['stoppol'];
		$sql_actual_distance = "select starttime,endtime,justify_interval(endtime - starttime) as durationorgtime
								from stes.device_assigne_pole_data where deviceid='".$deviceidnew."'
								and endtime is not null and starttime is not null";
		$query_actual_distance = pg_query($sql_actual_distance);
		$row_actual_distance = pg_fetch_array($query_actual_distance);
		$row_record[$i]['starttime_org_out'] = $row_actual_distance['starttime'];
		$row_record[$i]['endtime_org_out'] = $row_actual_distance['endtime'];
		$row_record[$i]['durationorgtime_org_out'] = $row_actual_distance['durationorgtime'];
		if($row_record[$i]['starttime_org_out'] != '' && $row_record[$i]['endtime_org_out'] != '')
		{
			$row_record[$i]['durationorgtime_org_out'] = abs($row_record[$i]['durationorgtime_org_out']);
			if((strtotime($row_record[$i]['starttime_org_out']) < strtotime($row_record[$i]['start_time'])) && (convertsecond($row_record[$i]['durationorgtime_org_out']) > convertsecond($row_record[$i]['duration'])))
			{
				$row_record[$i]['status'] = 'Duty Not Completed';
			}
			else
			{
				$row_record[$i]['status'] = 'Duty Completed';
			}
		}
		else
		{
			$row_record[$i]['starttime_org_out'] = '00:00:00';
			$row_record[$i]['endtime_org_out'] = '00:00:00';
			$row_record[$i]['durationorgtime_org_out'] = '00:00:00';
			$row_record[$i]['status'] = 'Duty Not Completed';
		}
	}
	else
	{
		$row_record[$i]['starttime_org_out'] = '00:00:00';
		$row_record[$i]['endtime_org_out'] = '00:00:00';
		$row_record[$i]['durationorgtime_org_out'] = '00:00:00';
		$row_record[$i]['status'] = 'Duty Not Completed';
	}
}
for($dv=0;$dv<count($row_record);$dv++)
{
	if($row_record[$dv]['mdddevicename'] != '' && $row_record[$dv]['result_date'] != '')
	{
		if($row_record[$dv]['status'] != 'Duty Completed')
		{
			$mdddevicename_arr = explode("/",$row_record[$dv]['mdddevicename']);
			if (strpos(strtolower($mdddevicename_arr[0]), 'stock') !== false) {
				$type = 'Stock';
			}
			else if (strpos(strtolower($mdddevicename_arr[0]), 'keyman') !== false) {
				$type = 'Keyman';
			}
			else if (strpos(strtolower($mdddevicename_arr[0]), 'patrolman') !== false) {
				$type = 'Patrolman';
			}
			else
			{
				$type = 'NA';
			}
			$row_record[$dv]['usertype'] = $type;
			array_push($alldatanew,$row_record[$dv]);
		}
	}
}
$result = array();
for($i=0;$i<count($alldatanew);$i++)
{
	$result[$i]['date'] = date("d-m-Y", strtotime($alldatanew[$i]['result_date']));;
	$result[$i]['mdddevicename'] = $alldatanew[$i]['mdddevicename'];
	$result[$i]['mddserialno'] = $alldatanew[$i]['mddserialno'];
	$result[$i]['pwy'] = $alldatanew[$i]['pwy'];
	$result[$i]['section'] = $alldatanew[$i]['section'];
	$result[$i]['bit'] = $alldatanew[$i]['bit'];
	$result[$i]['usertype'] = $alldatanew[$i]['usertype'];
	$result[$i]['acting_trip'] = $alldatanew[$i]['acting_trip'];
	$result[$i]['startpole'] = $alldatanew[$i]['startpole'];
	$result[$i]['stoppol'] = $alldatanew[$i]['stoppol'];
	$result[$i]['start_time'] = $alldatanew[$i]['start_time'];
	$result[$i]['endtime'] = $alldatanew[$i]['endtime'];
	$result[$i]['duration'] = $alldatanew[$i]['duration'];
	$result[$i]['starttime_org_out'] = $alldatanew[$i]['starttime_org_out'];
	$result[$i]['endtime_org_out'] = $alldatanew[$i]['endtime_org_out'];
	$result[$i]['durationorgtime_org_out'] = $alldatanew[$i]['durationorgtime_org_out'];
	$result[$i]['status'] = $alldatanew[$i]['status'];
}
$fp = fopen('/var/www/html/nfrtsk/cron/Time_Exception_Report.csv', 'w');
$header = array(
	'Date' => 'date',
	'DeviceName'=>'string',
	'Device SerialNo'=>'string',
	'SSE/PWAY'=>'string',
	'Section'=>'string',
	'BIT'=>'string',
	'UserType'=>'string',
	'TripNo'=>'string',
	'StartPole'=>'string',
	'EndPole'=>'string',
	'StartTime'=>'string',
	'EndTime'=>'string',
	'Duration'=>'string',
	'Actual StartTime'=>'string',
	'Actual EndTime'=>'string',
	'Actual Duration'=>'string',
	'Status'=>'string',
);
$list = array();
array_push($list,$header);
for($i=0;$i<count($result);$i++)
{
	array_push($list,$result[$i]);
}
foreach ($list as $fields) {
    fputcsv($fp, $fields);
}
fclose($fp);
/*$writer = new XLSXWriter();
$writer->setAuthor('Some Author');
$writer->writeSheet($result,'Sheet1',$header);
$writer->writeToFile('Time_Exception_Report.xlsx');*/
$devices = '';
$dids = '';
/***********************************************************/
/**************************Activity Summary Report*********************************/
$date = $curentdate;
$typeofuser = 'All';
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
			$devices .= $devicelist_each['did'].",";
			$dids .= $devicelist_each['did'].",";
		}
	}
}
$dids = substr($dids,0,-1);
$devices_arr = explode(',',$dids);
$report_data = array();
for($dv=0;$dv<count($devices_arr);$dv++)
{
	$length = count($report_data);
	$device_id = $devices_arr[$dv];
	if($device_id != '' && $device_id != '6838')
	{
		$sql_device_name = "select device_name FROM stes.master_device_setup where deviceid = ".$device_id."";
		$query_device_name = pg_query($sql_device_name);
		$row_device_name = pg_fetch_array($query_device_name);
		$device_name = $row_device_name['device_name'];
		if(pg_num_rows($query_device_name) > 0)
		{
			$device_name_arr = explode('/',$device_name);
			$user_type = $device_name_arr[0];
			$sql_device_assignment = "SELECT count(*) as counter  FROM public.master_device_assign where deviceid='".$device_id."' and group_id=2 and active = 1";
			$query_device_assignment = pg_query($sql_device_assignment);
			$row_device_assignment = pg_fetch_array($query_device_assignment);
			if(pg_num_rows($query_device_assignment) > 0)
			{
				if($user_type == 'Patrolman')
				{
					$dt = $date;
					$date_from = date("Y-m-d", strtotime('-1 day',strtotime(trim($dt)))).' 20:00:00';
					$date_from1 = date("Y-m-d", strtotime('-1 day',strtotime(trim($dt)))).' 23:59:59';
					$sql_record_from = "select a.*, mdd.serial_no,msd.device_name from public.get_histry_play_data_summary('".$device_id."', '".$date_from."'::timestamp without time zone, '".$date_from1."'::timestamp without time zone) as a left join stes.master_device_details as mdd on (mdd.superdevid = a.deviceid) left join stes.master_device_setup as msd on (msd.deviceid = a.deviceid)";
					$query_record_from = pg_query($sql_record_from);
					$row_record_from = pg_fetch_all($query_record_from);
					$date_to = date('Y-m-d', strtotime($dt)).' 00:00:00';
					$date_to1 = date('Y-m-d', strtotime($dt)).' 08:59:59';
					$sql_record_to = "select a.*, mdd.serial_no,msd.device_name from public.get_histry_play_data_summary('".$device_id."', '".$date_to."'::timestamp without time zone, '".$date_to1."'::timestamp without time zone) as a left join stes.master_device_details as mdd on (mdd.superdevid = a.deviceid) left join stes.master_device_setup as msd on (msd.deviceid = a.deviceid)";
					$query_record_to = pg_query($sql_record_to);
					$row_record_to = pg_fetch_all($query_record_to);
					if($row_record_from[0]['serial_no'] != '' && $row_record_to[0]['serial_no'] != '')
					{
						$serialno = $row_record_from[0]['serial_no'];
						$sql_device_assignment_details = "select a.parent_user_id,a.current_user_id,b.organisation as pwy,c.organisation as section
												from public.device_asign_details as a
												left join public.user_login as b on (a.parent_user_id = b.user_id)
												left join public.user_login as c on (a.current_user_id = c.user_id)
												where a.serial_no='".$serialno."'";
						$query_device_assignment_details = pg_query($sql_device_assignment_details);
						$row_device_assignment_details = pg_fetch_array($query_device_assignment_details);
						$report_data[$length][0]['pwy'] = $row_device_assignment_details['pwy'];
						$report_data[$length][0]['result_date_from'] = $row_record_from[0]['result_date'];
						$report_data[$length][0]['result_date_to'] = $row_record_to[0]['result_date'];
						$report_data[$length][0]['deviceid'] = $row_record_from[0]['deviceid'];
						$report_data[$length][0]['user_type'] = $user_type;
						$report_data[$length][0]['parent_id'] = $row_record_from[0]['parent_id'];
						$report_data[$length][0]['user_id'] = $row_record_from[0]['user_id'];
						$report_data[$length][0]['group_id'] = $row_record_from[0]['group_id'];
						$report_data[$length][0]['start_time'] = $row_record_from[0]['result_date']." ".$row_record_from[0]['start_time'];
						$report_data[$length][0]['end_time'] = $row_record_to[0]['result_date']." ".$row_record_to[0]['end_time'];
						$sql_duration = "select age('".$row_record_to[0]['result_date']." ".$row_record_to[0]['end_time']."','".$row_record_from[0]['result_date']." ".$row_record_from[0]['start_time']."') as duration";
						$query_duration = pg_query($sql_duration);
						$row_duration = pg_fetch_array($query_duration);
						$report_data[$length][0]['duration'] = $row_duration['duration'];
						$report_data[$length][0]['distance_cover'] = $row_record_from[0]['distance_cover']+$row_record_to[0]['distance_cover'];
						$report_data[$length][0]['sos_no'] = 0;
						$report_data[$length][0]['alert_no'] = 0;
						$report_data[$length][0]['call_no'] = 0;
						$report_data[$length][0]['serial_no'] = $row_record_from[0]['serial_no'];
						$report_data[$length][0]['device_name'] = $row_record_from[0]['device_name'];
						$sql_organism = "select organisation from public.user_login where user_id = '".$row_record_from[0]['user_id']."' and active = 1";
						$query_organism = pg_query($sql_organism);
						$row_organism = pg_fetch_array($query_organism);
						$report_data[$length][0]['organisation'] = $row_organism['organisation'];
						$devicename = $row_record_from[0]['device_name'];
						$devicenameArr = explode(':',$devicename);
						$poledetailsnew = trim($devicenameArr[1]);
						$poledetailsnewArr = explode('(',$poledetailsnew);
						$polenamenew = trim($poledetailsnewArr[1]);
						$polenamenew = str_replace(')','',$polenamenew);
						$polenamenewArr = explode('-',$polenamenew);
						$report_data[$length][0]['startpole'] = $polenamenewArr[0];
						$report_data[$length][0]['stoppol'] = $polenamenewArr[1];
						$report_data[$length][0]['bit'] = $report_data[$length][0]['startpole'].'-'.$report_data[$length][0]['stoppol'];
					}
				}
				else
				{
					$dt = $date;
					$date_from = date('Y-m-d', strtotime($dt)).' 06:00:00';
					$date_to = date('Y-m-d', strtotime($dt)).' 16:00:00';
					$sql_record = "select a.*, mdd.serial_no,msd.device_name from public.get_histry_play_data_summary('".$device_id."', '".$date_from."'::timestamp without time zone, '".$date_to."'::timestamp without time zone) as a left join stes.master_device_details as mdd on (mdd.superdevid = a.deviceid) left join stes.master_device_setup as msd on (msd.deviceid = a.deviceid)";
					$query_record = pg_query($sql_record);
					$row_record = pg_fetch_all($query_record);
					if($row_record[0]['serial_no'] != '')
					{
						$serialno = $row_record[0]['serial_no'];
						$sql_device_assignment_details = "select a.parent_user_id,a.current_user_id,b.organisation as pwy,c.organisation as section
											from public.device_asign_details as a
											left join public.user_login as b on (a.parent_user_id = b.user_id)
											left join public.user_login as c on (a.current_user_id = c.user_id)
											where a.serial_no='".$serialno."'";
						$query_device_assignment_details = pg_query($sql_device_assignment_details);
						$row_device_assignment_details = pg_fetch_array($query_device_assignment_details);
						$report_data[$length][0]['pwy'] = $row_device_assignment_details['pwy'];
						$report_data[$length][0]['result_date_from'] = $row_record[0]['result_date'];
						$report_data[$length][0]['result_date_to'] = $row_record[0]['result_date'];
						$report_data[$length][0]['deviceid'] = $row_record[0]['deviceid'];
						$report_data[$length][0]['user_type'] = $user_type;
						$report_data[$length][0]['parent_id'] = $row_record[0]['parent_id'];
						$report_data[$length][0]['user_id'] = $row_record[0]['user_id'];
						$report_data[$length][0]['group_id'] = $row_record[0]['group_id'];
						$report_data[$length][0]['start_time'] = $row_record[0]['result_date']." ".$row_record[0]['start_time'];
						$report_data[$length][0]['end_time'] = $row_record[0]['result_date']." ".$row_record[0]['end_time'];
						$sql_duration = "select age('".$row_record[0]['result_date']." ".$row_record[0]['end_time']."','".$row_record[0]['result_date']." ".$row_record[0]['start_time']."') as duration";
						$query_duration = pg_query($sql_duration);
						$row_duration = pg_fetch_array($query_duration);
						$report_data[$length][0]['duration'] = $row_duration['duration'];
						$report_data[$length][0]['distance_cover'] = $row_record[0]['distance_cover']+$row_record[0]['distance_cover'];
						$report_data[$length][0]['sos_no'] = 0;
						$report_data[$length][0]['alert_no'] = 0;
						$report_data[$length][0]['call_no'] = 0;
						$report_data[$length][0]['serial_no'] = $row_record[0]['serial_no'];
						$report_data[$length][0]['device_name'] = $row_record[0]['device_name'];
						$sql_organism = "select organisation from public.user_login where user_id = '".$row_record[0]['user_id']."' and active = 1";
						$query_organism = pg_query($sql_organism);
						$row_organism = pg_fetch_array($query_organism);
						$report_data[$length][0]['organisation'] = $row_organism['organisation'];
						$devicename = $row_record[0]['device_name'];
						$devicenameArr = explode(':',$devicename);
						$poledetailsnew = trim($devicenameArr[1]);
						$poledetailsnewArr = explode('(',$poledetailsnew);
						$polenamenew = trim($poledetailsnewArr[1]);
						$polenamenew = str_replace(')','',$polenamenew);
						$polenamenewArr = explode('-',$polenamenew);
						$report_data[$length][0]['startpole'] = $polenamenewArr[0];
						$report_data[$length][0]['stoppol'] = $polenamenewArr[1];
						$report_data[$length][0]['bit'] = $report_data[$length][0]['startpole'].'-'.$report_data[$length][0]['stoppol'];
					}
				}
			  }
			}
		}
	}
//}
$result = array();
for($i=0;$i<count($report_data);$i++)
{
	$result[$i]['date'] = date("d-m-Y", strtotime($report_data[$i][0]['result_date_from']));;
	$result[$i]['mdddevicename'] = $report_data[$i][0]['serial_no'];
	$result[$i]['mddserialno'] = $report_data[$i][0]['device_name'];
	$result[$i]['pwy'] = $report_data[$i][0]['bit'];
	$result[$i]['section'] = $report_data[$i][0]['pwy'];
	$result[$i]['bit'] = $report_data[$i][0]['organisation'];
	$result[$i]['usertype'] = $report_data[$i][0]['user_type'];
	$result[$i]['acting_trip'] = $report_data[$i][0]['start_time'];
	$result[$i]['startpole'] = $report_data[$i][0]['end_time'];
	$result[$i]['stoppol'] = round($report_data[$i][0]['distance_cover']/1000).' km';
	$result[$i]['start_time'] = $report_data[$i][0]['call_no'];
	$result[$i]['endtime'] = $report_data[$i][0]['sos_no'];
}
//echo "<pre>";print_r($result);echo "</pre>";
//exit;
$fp = fopen('/var/www/html/nfrtsk/cron/Activity_Summary_Report.csv', 'w');
$header = array(
	'Date' => 'string',
	'Device ID'=>'string',
	'DeviceName'=>'string',
	'BIT'=>'string',
	'SSE/PWY'=>'string',
	'Section'=>'string',
	'User Type'=>'string',
	'Start Date Time'=>'string',
	'End Date Time'=>'string',
	'Travelled Distance(KM)'=>'string',
	'Total Call'=>'string',
	'Total SOS'=>'string',
);
$list = array();
array_push($list,$header);
for($i=0;$i<count($result);$i++)
{
	array_push($list,$result[$i]);
}
foreach ($list as $fields) {
    fputcsv($fp, $fields);
}
fclose($fp);
/*$writer = new XLSXWriter();
$writer->setAuthor('Some Author');
$writer->writeSheet($result,'Sheet1',$header);
$writer->writeToFile('Activity_Summary_Report.xlsx');*/
//exit;
$devices = '';
$dids = '';
/***********************************************************/
/**************************Activity Summary Report Patrolman*********************************/
$date = $curentdate;
$typeofuser = 'All';
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
			$devices .= $devicelist_each['did'].",";
			$dids .= $devicelist_each['did'].",";
		}
	}
}
$dids = substr($dids,0,-1);
$devices_arr = explode(',',$dids);
$report_data = array();
for($dv=0;$dv<count($devices_arr);$dv++)
{
	$length = count($report_data);
	$device_id = $devices_arr[$dv];
	if($device_id != '' && $device_id != '6838')
	{
		$sql_device_name = "select device_name FROM stes.master_device_setup where deviceid = ".$device_id."";
		$query_device_name = pg_query($sql_device_name);
		$row_device_name = pg_fetch_array($query_device_name);
		$device_name = $row_device_name['device_name'];
		if(pg_num_rows($query_device_name) > 0)
		{
			$device_name_arr = explode('/',$device_name);
			$user_type = $device_name_arr[0];
			$sql_device_assignment = "SELECT count(*) as counter  FROM public.master_device_assign where deviceid='".$device_id."' and group_id=2 and active = 1";
			$query_device_assignment = pg_query($sql_device_assignment);
			$row_device_assignment = pg_fetch_array($query_device_assignment);
			if(pg_num_rows($query_device_assignment) > 0)
			{
				if($user_type == 'Patrolman')
				{
					$dt = $date;
					$date_from = date("Y-m-d", strtotime('-1 day',strtotime(trim($dt)))).' 20:00:00';
					$date_from1 = date("Y-m-d", strtotime('-1 day',strtotime(trim($dt)))).' 23:59:59';
					$sql_record_from = "select a.*, mdd.serial_no,msd.device_name from public.get_histry_play_data_summary('".$device_id."', '".$date_from."'::timestamp without time zone, '".$date_from1."'::timestamp without time zone) as a left join stes.master_device_details as mdd on (mdd.superdevid = a.deviceid) left join stes.master_device_setup as msd on (msd.deviceid = a.deviceid)";
					$query_record_from = pg_query($sql_record_from);
					$row_record_from = pg_fetch_all($query_record_from);
					$date_to = date('Y-m-d', strtotime($dt)).' 00:00:00';
					$date_to1 = date('Y-m-d', strtotime($dt)).' 08:59:59';
					$sql_record_to = "select a.*, mdd.serial_no,msd.device_name from public.get_histry_play_data_summary('".$device_id."', '".$date_to."'::timestamp without time zone, '".$date_to1."'::timestamp without time zone) as a left join stes.master_device_details as mdd on (mdd.superdevid = a.deviceid) left join stes.master_device_setup as msd on (msd.deviceid = a.deviceid)";
					$query_record_to = pg_query($sql_record_to);
					$row_record_to = pg_fetch_all($query_record_to);
					if($row_record_from[0]['serial_no'] != '' && $row_record_to[0]['serial_no'] != '')
					{
						$serialno = $row_record_from[0]['serial_no'];
						$sql_device_assignment_details = "select a.parent_user_id,a.current_user_id,b.organisation as pwy,c.organisation as section
												from public.device_asign_details as a
												left join public.user_login as b on (a.parent_user_id = b.user_id)
												left join public.user_login as c on (a.current_user_id = c.user_id)
												where a.serial_no='".$serialno."'";
						$query_device_assignment_details = pg_query($sql_device_assignment_details);
						$row_device_assignment_details = pg_fetch_array($query_device_assignment_details);
						$report_data[$length][0]['pwy'] = $row_device_assignment_details['pwy'];
						$report_data[$length][0]['result_date_from'] = $row_record_from[0]['result_date'];
						$report_data[$length][0]['result_date_to'] = $row_record_to[0]['result_date'];
						$report_data[$length][0]['deviceid'] = $row_record_from[0]['deviceid'];
						$report_data[$length][0]['user_type'] = $user_type;
						$report_data[$length][0]['parent_id'] = $row_record_from[0]['parent_id'];
						$report_data[$length][0]['user_id'] = $row_record_from[0]['user_id'];
						$report_data[$length][0]['group_id'] = $row_record_from[0]['group_id'];
						$report_data[$length][0]['start_time'] = $row_record_from[0]['result_date']." ".$row_record_from[0]['start_time'];
						$report_data[$length][0]['end_time'] = $row_record_to[0]['result_date']." ".$row_record_to[0]['end_time'];
						$sql_duration = "select age('".$row_record_to[0]['result_date']." ".$row_record_to[0]['end_time']."','".$row_record_from[0]['result_date']." ".$row_record_from[0]['start_time']."') as duration";
						$query_duration = pg_query($sql_duration);
						$row_duration = pg_fetch_array($query_duration);
						$report_data[$length][0]['duration'] = $row_duration['duration'];
						$report_data[$length][0]['distance_cover'] = $row_record_from[0]['distance_cover']+$row_record_to[0]['distance_cover'];
						$report_data[$length][0]['sos_no'] = 0;
						$report_data[$length][0]['alert_no'] = 0;
						$report_data[$length][0]['call_no'] = 0;
						$report_data[$length][0]['serial_no'] = $row_record_from[0]['serial_no'];
						$report_data[$length][0]['device_name'] = $row_record_from[0]['device_name'];
						$sql_organism = "select organisation from public.user_login where user_id = '".$row_record_from[0]['user_id']."' and active = 1";
						$query_organism = pg_query($sql_organism);
						$row_organism = pg_fetch_array($query_organism);
						$report_data[$length][0]['organisation'] = $row_organism['organisation'];
						$devicename = $row_record_from[0]['device_name'];
						$devicenameArr = explode(':',$devicename);
						$poledetailsnew = trim($devicenameArr[1]);
						$poledetailsnewArr = explode('(',$poledetailsnew);
						$polenamenew = trim($poledetailsnewArr[1]);
						$polenamenew = str_replace(')','',$polenamenew);
						$polenamenewArr = explode('-',$polenamenew);
						$report_data[$length][0]['startpole'] = $polenamenewArr[0];
						$report_data[$length][0]['stoppol'] = $polenamenewArr[1];
						$report_data[$length][0]['bit'] = $report_data[$length][0]['startpole'].'-'.$report_data[$length][0]['stoppol'];
					}
				}
			  }
			}
		}
	//}
}
$result = array();
for($i=0;$i<count($report_data);$i++)
{
	$result[$i]['date'] = date("d-m-Y", strtotime($report_data[$i][0]['result_date_from']));;
	$result[$i]['mdddevicename'] = $report_data[$i][0]['serial_no'];
	$result[$i]['mddserialno'] = $report_data[$i][0]['device_name'];
	$result[$i]['pwy'] = $report_data[$i][0]['bit'];
	$result[$i]['section'] = $report_data[$i][0]['pwy'];
	$result[$i]['bit'] = $report_data[$i][0]['organisation'];
	$result[$i]['usertype'] = $report_data[$i][0]['user_type'];
	$result[$i]['acting_trip'] = $report_data[$i][0]['start_time'];
	$result[$i]['startpole'] = $report_data[$i][0]['end_time'];
	$result[$i]['stoppol'] = round($report_data[$i][0]['distance_cover']/1000).' km';
	$result[$i]['start_time'] = $report_data[$i][0]['call_no'];
	$result[$i]['endtime'] = $report_data[$i][0]['sos_no'];
}
//echo "<pre>";print_r($result);echo "</pre>";
//exit;
$fp = fopen('/var/www/html/nfrtsk/cron/Activity_Summary_Report_Patrolman.csv', 'w');
$header = array(
	'Date' => 'string',
	'Device ID'=>'string',
	'DeviceName'=>'string',
	'BIT'=>'string',
	'SSE/PWY'=>'string',
	'Section'=>'string',
	'User Type'=>'string',
	'Start Date Time'=>'string',
	'End Date Time'=>'string',
	'Travelled Distance(KM)'=>'string',
	'Total Call'=>'string',
	'Total SOS'=>'string',
);
$list = array();
array_push($list,$header);
for($i=0;$i<count($result);$i++)
{
	array_push($list,$result[$i]);
}
foreach ($list as $fields) {
    fputcsv($fp, $fields);
}
fclose($fp);
/*$writer = new XLSXWriter();
$writer->setAuthor('Some Author');
$writer->writeSheet($result,'Sheet1',$header);
$writer->writeToFile('Activity_Summary_Report_Patrolman.xlsx');*/
//exit;
$devices = '';
$dids = '';
/***********************************************************/


require '/var/www/html/nfrtsk/phpmailer/class.phpmailer.php';
require '/var/www/html/nfrtsk/phpmailer/class.smtp.php';
$mail = new PHPMailer(); // create a new object
$mail->IsSMTP(); // enable SMTP
$mail->SMTPDebug = SMTP::DEBUG_LOWLEVEL; // debugging: 1 = errors and messages, 2 = messages only
$mail->Debugoutput = 'html';
$mail->SMTPAuth = true; // authentication enabled
$file1 = '/var/www/html/nfrtsk/cron/Work_Status_Count_Report.csv';
$file2 = '/var/www/html/nfrtsk/cron/Distance_Exception_Report.csv';
$file3 = '/var/www/html/nfrtsk/cron/Time_Exception_Report.csv';
$file4 = '/var/www/html/nfrtsk/cron/Activity_Summary_Report.csv';
$file5 = '/var/www/html/nfrtsk/cron/Activity_Summary_Report_Patrolman.csv';
$mail->Host = "mail.maargdarshak-mpso.com";
$mail->Port = 25; // or 587
$mail->IsHTML(false);
$mail->Username = "info@maargdarshak-mpso.com";
$mail->Password = "Sil@12345";
$mail->SMTPAutoTLS = false; 
$mail->setFrom('info@maargdarshak-mpso.com', 'Stesalit Systems Ltd');
$mail->addAddress('srdentskoffice@gmail.com','Sr. Den C TSK');
$mail->AddCC('tathagata.81@gmail.com', 'Tathagata Lahiri Majumder');
$mail->AddCC('akpaul.stesalit.kol@gmail.com', 'Amit Kumar Paul');
$mail->Subject = "Stesalit- Exception reports of GPS Tracking device";
$mail->CharSet = 'UTF-8';
$mail->msgHTML("<p>Dear Sir,</p><p>Please find the Exception reports & Activation Summary report.</p><p>With regards,</p><p>Stesalit Systems Ltd </p>");
$mail->AltBody = 'This is a plain-text message body';
$mail->addAttachment($file1);
$mail->addAttachment($file2);
$mail->addAttachment($file3);
$mail->addAttachment($file4);
$mail->addAttachment($file5);
if (!$mail->send()) {
         $errorno = $mail->ErrorInfo;
          echo "Mailer Error: " . $mail->ErrorInfo;
         $msg = 0;
}
else {
	 $success = "Message sent!";
	 echo "Message sent!";
	 $msg = 1;
}


exit(0);
function sum_the_time($time1, $time2) 
{
	  $times = array($time1, $time2);
	  $seconds = 0;
	  foreach ($times as $time)
	  {
		list($hour,$minute,$second) = explode(':', $time);
		$seconds += $hour*3600;
		$seconds += $minute*60;
		$seconds += $second;
	  }
	  $hours = floor($seconds/3600);
	  $seconds -= $hours*3600;
	  $minutes  = floor($seconds/60);
	  $seconds -= $minutes*60;
	  if($seconds < 9)
	  {
	  $seconds = "0".$seconds;
	  }
	  if($minutes < 9)
	  {
	  $minutes = "0".$minutes;
	  }
		if($hours < 9)
	  {
	  $hours = "0".$hours;
	  }
	  return "{$hours}:{$minutes}:{$seconds}";
}
function sendEmailNewSmtp($from, $to, $cc, $bcc, $subject, $data, $file1=null, $file2=null) {
	$ci = & get_instance();
	$ci->load->library('email');
	
	$config = Array(
		'protocol' => 'smtp',
		'smtp_host' => 'ssl://mail.9trax.com',
		'smtp_port' => 465,
		'smtp_user' => 'alert@9trax.com',
		'smtp_pass' => 'Sil@45123',
		'mailtype'  => 'html', 
		'charset'   => 'utf-8'
	);
	$ci->email->initialize($config);
	$ci->email->set_mailtype("html");
	$ci->email->set_newline("\r\n");
	$ci->email->from($from, '9trax'); 
    $ci->email->to($to);
    if($cc != null){
        $ci->email->cc($cc);
    }
    if($bcc != null){
        $ci->email->bcc($bcc);
    }
    $ci->email->subject($subject); 
    $ci->email->message($data);
    if($file1 != null){
        $ci->email->attach($file1);
    }
    if($file2 != null){
        $ci->email->attach($file2);
    }
  
    if($ci->email->send()){
        $ret = 1;
    }
    else{
		$ci->email->print_debugger();
        $ret = 0;
    }
    
    return $ret;
}
function hasMinusSign($value)
{
	return (substr(strval($value), 0, 1) == "-");
}
function convertsecond($duration) 
{
	$duration_arr = explode(":",$duration);
	$second = ($duration_arr[0]*3600) + ($duration_arr[1]*60) + $duration_arr[2];
	return $second;
}
?>