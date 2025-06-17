<?php
error_reporting(E_ERROR | E_WARNING | E_PARSE);
$conn = pg_connect('host=localhost port=5432 dbname=personal_track user=postgres password=Admin@123') or die ('<b>DATABASE CONNECTIVITY PROBLEM - Error connecting to postgre:</b></ br>'.pg_last_error());
ini_set('memory_limit', '-1');
set_time_limit(0);
date_default_timezone_set('Asia/Calcutta');
$date = date('Y-m-d');
$data_start = date('Y-m-d', strtotime($date .' -1 day'));
$time1 = "06:00:01";
$time2 = "23:59:59";

$sql = "select public.cron_daly_trip_summary('".$data_start." ".$time1."'::timestamp without time zone,'".$data_start." ".$time2."'::timestamp without time zone)";
pg_query($sql);
?>