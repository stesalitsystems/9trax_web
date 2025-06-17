<?php
error_reporting(E_ERROR | E_WARNING | E_PARSE);
$conn = pg_connect('host=localhost port=5432 dbname=personal_track user=postgres password=Admin@123') or die ('<b>DATABASE CONNECTIVITY PROBLEM - Error connecting to postgre:</b></ br>'.pg_last_error());
ini_set('memory_limit', '-1');
set_time_limit(0);
date_default_timezone_set('Asia/Calcutta');

$currentdate = $_REQUEST['date'];
//$currentdate = '2019-04-01';
$userid=101;
$deviceid = $_REQUEST['deviceid'];;
$timefrom = $_REQUEST['timefrom'];
$timeto = $_REQUEST['timeto'];

$sqlweeklytable = "SELECT    date_trunc('week', '".$currentdate."'::timestamp)::date
   || ' '
   || (date_trunc('week', '".$currentdate."'::timestamp)+ '6 days'::interval)::date as weekly";
$queryweeklytable = pg_query($sqlweeklytable);
$rsweeklytable = pg_fetch_array($queryweeklytable);
$weeklytable = $rsweeklytable['weekly'];
$weeklytable = str_replace("-","",$weeklytable);
$weeklytable = str_replace(" ","_",$weeklytable);
$eventable = "dist.traker_positionaldata_101_".$weeklytable;

echo $sql = "select * from ".$eventable." where deviceid = ".$deviceid." and currentdate = '".$currentdate."' and currenttime between '".$timefrom."' and '".$timeto."' and  ST_Contains(ST_Transform(ST_Buffer(ST_Transform(ST_GeomFromText('POINT(88.394703 22.626276)',4326),26986),30,'quad_segs=8'),4326),ST_GeomFromText('POINT('||longitude||' '||latitude||')',4326)) is false";
$query = pg_query($sql);
while($rs = pg_fetch_array($query))
{
	$positionalid = $rs['id'];
	$update = "update ".$eventable." set latitude='22.626276', longitude='88.394703' where id = '".$positionalid."'";
	pg_query($update);
}
?>