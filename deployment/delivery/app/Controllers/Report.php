<?php

namespace App\Controllers;

use App\Models\ReportModel;
use App\Models\MobilesModel;
use App\Models\CommonModel;
use App\Libraries\MakePDF;
use CodeIgniter\I18n\Time;

class Report extends BaseController
{
    protected $sessdata;
    protected $schema;

    public function __construct()
    {
        // Load helpers
        helper(['form', 'url', 'master', 'communication']);

        helper('master_helper');
        $this->db = \Config\Database::connect();
        
        // Check for session data
        if (session()->get('login_sess_data')) {
            $this->sessdata = session()->get('login_sess_data');
            $this->schema = $this->sessdata['schemaname']; // Adjusted for array access
        }

        // Load models
        $this->reportModel = new ReportModel();
        $this->mobilesModel = new MobilesModel();
        $this->commonModel = new CommonModel();
        
        // Load libraries
        // Assuming 'makepdf' and 'excel' are custom libraries
        // $this->makePdf = service('makepdf');
        $this->excel = service('excel');
    }

    public function panic(){ 
        // Check if user is logged in
        if (!session()->has('login_sess_data')) {
            return redirect()->to('/');
        }

        $data = [];
        $data['sessdata'] = $this->sessdata;
        $data['page_title'] = "SOS/Panic Report";
        $data['date_from'] = '';
        $data['usertype'] = 'All';

        if ($this->request->getMethod() == 'POST') {

            $user_id = $this->sessdata['user_id'];
            $subUsers = $this->getSubUsers($user_id);

            // Include logged-in user
            $allowedUsers = array_merge([$user_id], $subUsers);
            $placeholders = implode(',', array_fill(0, count($allowedUsers), '?'));

            $date = date("Y-m-d", strtotime(trim($this->request->getPost('dt'))));
            $data['dtt'] = date("d-m-Y", strtotime(trim($this->request->getPost('dt'))));

            $sql = "SELECT tsd.deviceid, tsd.currentdate, tsd.currenttime, mdd.serial_no, mds.device_name
                FROM {$this->schema}.traker_sosdata tsd
                JOIN public.master_device_details mdd ON mdd.id = tsd.deviceid
                JOIN {$this->schema}.master_device_setup mds ON mds.deviceid = tsd.deviceid
                LEFT JOIN public.user_login ul 
                ON ul.user_id = tsd.user_id   
            LEFT JOIN public.user_login p_ul 
                ON p_ul.user_id = tsd.parent_id  
                WHERE tsd.currentdate = ?  AND ul.group_id = 2";                

            $parameters = [$date];

            $sql .= " AND tsd.user_id IN ($placeholders)";
            $parameters = array_merge($parameters, $allowedUsers);

            $sql .= "ORDER BY tsd.currenttime DESC;";
            $result = $this->db->query($sql, $parameters)->getResult();

            $data['alldata'] = $result;
        }

        $data['middle'] = view('report/sospanicreport', $data);
        return view('mainlayout', $data);
    }

    public function panicExcel(){ 
        // Check if user is logged in
        if (!session()->has('login_sess_data')) {
            return redirect()->to('/');
        }

        $data = [];
        $data['sessdata'] = $this->sessdata;
        $data['page_title'] = "SOS Panic Report";
        $data['date_from'] = '';

        if ($this->request->getMethod() == 'POST') {
            $date = date("Y-m-d", strtotime(trim($this->request->getPost('dt'))));
            $data['dtt'] = date("d-m-Y", strtotime(trim($this->request->getPost('dt'))));

            $sql = "SELECT tsd.deviceid, tsd.currentdate, tsd.currenttime, mdd.serial_no, mds.device_name
                FROM {$this->schema}.traker_sosdata tsd
                JOIN public.master_device_details mdd ON mdd.id = tsd.deviceid
                JOIN {$this->schema}.master_device_setup mds ON mds.deviceid = tsd.deviceid
                LEFT JOIN public.user_login ul 
                ON ul.user_id = tsd.user_id   
            LEFT JOIN public.user_login p_ul 
                ON p_ul.user_id = tsd.parent_id  
                WHERE tsd.currentdate = ?  AND ul.group_id = 2";                

            $parameters = [$date];

            $sql .= " AND tsd.user_id IN ($placeholders)";
            $parameters = array_merge($parameters, $allowedUsers);

            $sql .= "ORDER BY tsd.currenttime DESC;";
            $newresult = $this->db->query($sql, $parameters)->getResult();

            $data['alldata'] = $newresult;
        }

        $dat[0]['A'] = 'SOS Report';
        $dat[1]['A'] = " Generated For - " . $data['dtt'];

        // Initialize header row
        $dat[2]['A'] = "SL No.";           
        $dat[2]['B'] = "IMEI No.";
        $dat[2]['C'] = "Device Name";
        $dat[2]['D'] = "Time";
        
        // Initialize counters
        $Key = 2;
        $i = 1;

        foreach ($newresult as $irow) {

            // Fill data for each row
            $dat[$Key + 1]['A'] = $i;
            $dat[$Key + 1]['B'] = $irow->serial_no;
            $dat[$Key + 1]['C'] = $irow->device_name;
            $dat[$Key + 1]['D'] = $irow->currentdate . ' ' . $irow->currenttime;

            $Key++;
            $i++;
        }
        
        // Create the Excel file
        $filename = "SOS_Report_Generated_For_" . $data['dtt'] . '.xlsx';
        exceldownload($dat, $filename);
    }

    public function panicPdf(){ 
        // Check if user is logged in
        if (!session()->has('login_sess_data')) {
            return redirect()->to('/');
        }

        $data = [];
        $data['sessdata'] = $this->sessdata;
        $data['page_title'] = "SOS/Panic Report";
        $data['date_from'] = '';
        $data['usertype'] = 'All';

        if ($this->request->getMethod() == 'POST') {
            $date = date("Y-m-d", strtotime(trim($this->request->getPost('dt'))));
            $data['dtt'] = date("d-m-Y", strtotime(trim($this->request->getPost('dt'))));

            $sql = "SELECT tsd.deviceid, tsd.currentdate, tsd.currenttime, mdd.serial_no, mds.device_name
                FROM {$this->schema}.traker_sosdata tsd
                JOIN public.master_device_details mdd ON mdd.id = tsd.deviceid
                JOIN {$this->schema}.master_device_setup mds ON mds.deviceid = tsd.deviceid
                LEFT JOIN public.user_login ul 
                ON ul.user_id = tsd.user_id   
            LEFT JOIN public.user_login p_ul 
                ON p_ul.user_id = tsd.parent_id  
                WHERE tsd.currentdate = ?  AND ul.group_id = 2";                

            $parameters = [$date];

            $sql .= " AND tsd.user_id IN ($placeholders)";
            $parameters = array_merge($parameters, $allowedUsers);

            $sql .= "ORDER BY tsd.currenttime DESC;";
            $result = $this->db->query($sql, $parameters)->getResult();

            $data['alldata'] = $result;
        }

        $html = view('report/pdf_panic', $data); // Load view in CI4
        $filename = 'SOS/Panic_Report_' . time();

        // Instantiate the MakePDF class
        $pdf = new MakePDF();

        // Set the filename and content
        $pdf->setFileName($filename);
        $pdf->setContent($html);

        // Generate and stream the PDF to the browser
        $pdf->getPdf();  // true to stream the PDF
    }

    public function geofence(){ 
        // Check if user is logged in
        if (!session()->has('login_sess_data')) {
            return redirect()->to('/');
        }

        $data = [];
        $data['sessdata'] = $this->sessdata;
        $data['page_title'] = "Geofence Report";
        $data['date_from'] = '';

        $data['devicedropdown'] = $this->db->query("SELECT a.*, 
        (SELECT device_name FROM {$this->schema}.master_device_setup  
            WHERE id = (SELECT max(id) 
                        FROM {$this->schema}.master_device_setup 
                        WHERE inserttime::date <= current_date::date  
                        AND deviceid = a.did)) 
        AS device_name 
        FROM public.get_divice_details_record_for_list_for_company('{$this->schema}',{$this->sessdata['user_id']}) AS a 
        WHERE a.group_id = 2 AND a.active = 1")->getResult();

        if ($this->request->getMethod() == 'POST') {

            $user_id = $this->sessdata['user_id'];
            $subUsers = $this->getSubUsers($user_id);

            // Include logged-in user
            $allowedUsers = array_merge([$user_id], $subUsers);
            $placeholders = implode(',', array_fill(0, count($allowedUsers), '?'));

            // echo "<pre>";print_r($this->request->getPost());exit();
            $start_date = date("Y-m-d H:i:s", strtotime(trim($this->request->getPost('stdt'))));
            $end_date = date("Y-m-d H:i:s", strtotime(trim($this->request->getPost('endt'))));
            $data['stdt'] = date("d-m-Y H:i", strtotime(trim($this->request->getPost('stdt'))));
            $data['endt'] = date("d-m-Y H:i", strtotime(trim($this->request->getPost('endt'))));
            $data['schema'] = $schemaname = $this->schema;
            $data['device_id'] = $device_id = trim($this->request->getPost('device_id'));

            $sql = "SELECT 
                {$this->schema}.tbl_trip.deviceid, 
                {$this->schema}.tbl_trip.imeino, 
                {$this->schema}.tbl_trip.stpole as actual_stpole, 
                {$this->schema}.tbl_trip.endpole as actual_endpole,
                {$this->schema}.tbl_trip.sttimestamp as actual_starttime, 
                {$this->schema}.tbl_trip.endtimestamp as actual_endtime,
                {$this->schema}.tbl_trip.totaldistancetravel, {$this->schema}.tbl_trip.timetravelled,
                (
                    SELECT COUNT(*) 
                    FROM public.tbl_trip_stoppage 
                    WHERE tbl_trip_stoppage.trip_id = tbl_trip.trip_id
                ) AS stoppage_count,
                (
                    SELECT string_agg(
                        TRIM(
                            CASE 
                                WHEN EXTRACT(hour FROM stoppage_duration) > 0 THEN EXTRACT(hour FROM stoppage_duration)::int || ' hours '
                                ELSE ''
                            END ||
                            CASE 
                                WHEN EXTRACT(minute FROM stoppage_duration) > 0 THEN EXTRACT(minute FROM stoppage_duration)::int || ' mins '
                                ELSE ''
                            END ||
                            CASE 
                                WHEN EXTRACT(second FROM stoppage_duration) > 0 THEN EXTRACT(second FROM stoppage_duration)::int || ' secs'
                                ELSE '0 sec'
                            END
                        ) || ' at (' || pole || ') on ' || to_char(stoppage_start, 'dd-mm HH24:MI'),
                        E'\n' ORDER BY stoppage_start
                    ) 
                    FROM public.tbl_trip_stoppage 
                    WHERE tbl_trip_stoppage.trip_id = tbl_trip.trip_id
                ) AS stoppage_names

            FROM {$this->schema}.tbl_trip
            LEFT JOIN public.master_device_assign mda 
                ON mda.deviceid = {$this->schema}.tbl_trip.deviceid  
            LEFT JOIN public.user_login ul 
                ON ul.user_id = mda.user_id   
            LEFT JOIN public.user_login p_ul 
                ON p_ul.user_id = mda.parent_id  
            WHERE 
            (
                (sttimestamp BETWEEN ? AND ?)
                OR (endtimestamp BETWEEN ? AND ?)
                OR (sttimestamp < ? AND endtimestamp > ?)
            )
            AND ul.group_id = 2
            AND mda.active = 1 ";

            $parameters = [$start_date, $end_date, $start_date, $end_date, $start_date, $end_date];

            $sql .= " AND mda.user_id IN ($placeholders)";
            $parameters = array_merge($parameters, $allowedUsers);

            if (!empty($data['device_id'])) {
                $sql .= " AND {$this->schema}.tbl_trip.deviceid = ?";
                $parameters[] = $data['device_id'];
            }

            $sql .= " ORDER BY imeino, sttimestamp ASC";

            $result = $this->db->query($sql, $parameters)->getResult();



            $data['alldata'] = $result;
        }

        $data['device_id'] = $device_id = trim($this->request->getPost('device_id'));

        $data['middle'] = view('report/geofence', $data);
        return view('mainlayout', $data);
    }

    public function geofenceExcel(){
  
        // Check if user is logged in
        if (!session()->has('login_sess_data')) {
            return redirect()->to('/');
        }

        $data = [];
        $data['sessdata'] = $this->sessdata;
        $data['page_title'] = "Geofence Report";
        $data['date_from'] = '';

        $user_id = $this->sessdata['user_id'];
        
        if ($this->request->getMethod() == 'POST') {
            // echo "<pre>";print_r($this->request->getPost());exit();
            $start_date = date("Y-m-d H:i:s", strtotime(trim($this->request->getPost('stdt'))));
            $end_date = date("Y-m-d H:i:s", strtotime(trim($this->request->getPost('endt'))));
            $data['stdt'] = date("d-m-Y H:i", strtotime(trim($this->request->getPost('stdt'))));
            $data['endt'] = date("d-m-Y H:i", strtotime(trim($this->request->getPost('endt'))));
            $data['schema'] = $schemaname = $this->schema;
            $data['device_id'] = $device_id = trim($this->request->getPost('device_id'));

            $user_id = $this->sessdata['user_id'];
            $subUsers = $this->getSubUsers($user_id);

            // Include logged-in user
            $allowedUsers = array_merge([$user_id], $subUsers);
            $placeholders = implode(',', array_fill(0, count($allowedUsers), '?'));

            $sql = "SELECT 
                deviceid, imeino, stpole as actual_stpole, endpole as actual_endpole,
                sttimestamp as actual_starttime, endtimestamp as actual_endtime,
                totaldistancetravel, timetravelled,
                (
                    SELECT COUNT(*) 
                    FROM public.tbl_trip_stoppage 
                    WHERE tbl_trip_stoppage.trip_id = tbl_trip.trip_id
                ) AS stoppage_count
                FROM {$this->schema}.tbl_trip
                LEFT JOIN public.master_device_assign mda 
                    ON mda.deviceid = {$this->schema}.tbl_trip.deviceid  
                LEFT JOIN public.user_login ul 
                    ON ul.user_id = mda.user_id   
                LEFT JOIN public.user_login p_ul 
                    ON p_ul.user_id = mda.parent_id  
                WHERE 
                (
                        (sttimestamp BETWEEN ? AND ?)
                    OR  (endtimestamp   BETWEEN ? AND ?) 
                    OR (sttimestamp < ? AND endtimestamp > ?) 
                ) 
                AND ul.group_id = 2
                AND mda.active = 1 ";

                $parameters = [$start_date, $end_date,$start_date, $end_date,$start_date, $end_date];  

                $sql .= " AND mda.user_id IN ($placeholders)";
                $parameters = array_merge($parameters, $allowedUsers);

                if (!empty($data['device_id'])) {
                    $sql .= " AND deviceid = ?";
                    $parameters[] = $data['device_id'];
                }

            $sql .= " ORDER BY imeino, sttimestamp ASC";

            $result = $this->db->query($sql, $parameters)->getResult();

            $data['alldata'] = $result;
        }

        $dat[0]['A'] = " Geofence Report From Date " . date("d-m-Y H:i:s", strtotime($start_date)) . " To " . date("d-m-Y H:i:s", strtotime($end_date));

        // Initialize header row
        $dat[1]['A'] = "Entry";
        $dat[1]['B'] = "";
        $dat[1]['C'] = "Exit";
        $dat[1]['D'] = "";
        $dat[1]['E'] = "Travelled Time";
        $dat[1]['F'] = "Travelled Distance(KM)";
        $dat[1]['G'] = "Stoppages";

        $dat[2]['A'] = "Date & Time";
        $dat[2]['B'] = "Address";
        $dat[2]['C'] = "Date & Time";
        $dat[2]['D'] = "Address";
        $dat[2]['E'] = "";
        $dat[2]['F'] = "";
        $dat[2]['G'] = "";
        
        // Initialize counters
        $Key = 2;

        foreach ($result as $irow) {

            $timetravelled = strtotime($irow->timetravelled);
            $seconds = (int) $timetravelled; // Ensure it's an integer

            $days = floor($seconds / 86400); 
            $seconds %= 86400; 

            $hours = floor($seconds / 3600);
            $seconds %= 3600;

            $minutes = floor($seconds / 60);
            $seconds %= 60;

            $formatted = "{$hours} hours {$minutes} mins {$seconds} sec";

            // Fill data for each row
            $dat[$Key + 1]['A'] = date("Y-m-d H:i:s", strtotime($irow->actual_starttime));
            $dat[$Key + 1]['B'] = ($irow->actual_stpole ?? '');
            $dat[$Key + 1]['C'] = date("Y-m-d H:i:s", strtotime($irow->actual_endtime));
            $dat[$Key + 1]['D'] = ($irow->actual_endpole ?? '');
            $dat[$Key + 1]['E'] = $formatted;
            $dat[$Key + 1]['F'] = number_format($irow->totaldistancetravel,4);
            $dat[$Key + 1]['G'] = $irow->stoppage_count;

            $Key++;
        }

        // Create the Excel file
        $filename = 'Geofence_Report_' . date("d-m-Y H:i", strtotime($start_date)) . '_To_' . date("d-m-Y H:i", strtotime($end_date)) . '.xlsx';
        exceldownload($dat, $filename);

    }

    public function geofencePdf(){ 
        // Check if user is logged in
        if (!session()->has('login_sess_data')) {
            return redirect()->to('/');
        }

        $data = [];
        $data['sessdata'] = $this->sessdata;
        $data['page_title'] = "Geofence Report";
        $data['date_from'] = '';

        $data['devicedropdown'] = $this->db->query("SELECT a.*, 
        (SELECT device_name FROM {$this->schema}.master_device_setup  
            WHERE id = (SELECT max(id) 
                        FROM {$this->schema}.master_device_setup 
                        WHERE inserttime::date <= current_date::date  
                        AND deviceid = a.did)) 
        AS device_name 
        FROM public.get_divice_details_record_for_list_for_company('{$this->schema}',{$this->sessdata['user_id']}) AS a 
        WHERE a.group_id = 2 AND a.active = 1")->getResult();

        if ($this->request->getMethod() == 'POST') {

            $user_id = $this->sessdata['user_id'];
            $subUsers = $this->getSubUsers($user_id);

            // Include logged-in user
            $allowedUsers = array_merge([$user_id], $subUsers);
            $placeholders = implode(',', array_fill(0, count($allowedUsers), '?'));

            $start_date = date("Y-m-d H:i:s", strtotime(trim($this->request->getPost('stdt'))));
            $end_date = date("Y-m-d H:i:s", strtotime(trim($this->request->getPost('endt'))));
            $data['stdt'] = date("d-m-Y H:i", strtotime(trim($this->request->getPost('stdt'))));
            $data['endt'] = date("d-m-Y H:i", strtotime(trim($this->request->getPost('endt'))));
            $data['schema'] = $schemaname = $this->schema;
            $data['device_id'] = $device_id = trim($this->request->getPost('device_id'));

            $sql = "SELECT 
                deviceid, imeino, stpole as actual_stpole, endpole as actual_endpole,
                sttimestamp as actual_starttime, endtimestamp as actual_endtime,
                totaldistancetravel, timetravelled,

                (
                    SELECT COUNT(*) 
                    FROM public.tbl_trip_stoppage 
                    WHERE tbl_trip_stoppage.trip_id = tbl_trip.trip_id
                ) AS stoppage_count

            FROM {$this->schema}.tbl_trip
            LEFT JOIN public.master_device_assign mda 
                ON mda.deviceid = {$this->schema}.tbl_trip.deviceid  
            LEFT JOIN public.user_login ul 
                ON ul.user_id = mda.user_id   
            LEFT JOIN public.user_login p_ul 
                ON p_ul.user_id = mda.parent_id  
            WHERE 
            (
                (sttimestamp BETWEEN ? AND ?)
                OR (endtimestamp BETWEEN ? AND ?)
                OR (sttimestamp < ? AND endtimestamp > ?)
            )
            AND ul.group_id = 2
            AND mda.active = 1 ";

            $parameters = [$start_date, $end_date, $start_date, $end_date, $start_date, $end_date];

            if (!empty($data['device_id'])) {
                $sql .= " AND deviceid = ?";
                $parameters[] = $data['device_id'];
            }

            $sql .= " ORDER BY imeino, sttimestamp ASC";
            $result = $this->db->query($sql, $parameters)->getResult();

            $data['alldata'] = $result;
        }

        $html = view('report/pdf_geofence', $data); // Load view in CI4
        $filename = 'Geofence_Report_' . time();

        // Instantiate the MakePDF class
        $pdf = new MakePDF();

        // Set the filename and content
        $pdf->setFileName($filename);
        $pdf->setContent($html);

        // Generate and stream the PDF to the browser
        $pdf->getPdf();  // true to stream the PDF
    }

    public function geofencegroup(){ 
        // Check if user is logged in
        if (!session()->has('login_sess_data')) {
            return redirect()->to('/');
        }

        $data = [];
        $data['sessdata'] = $this->sessdata;
        $data['page_title'] = "Geofence Group Report";
        $data['date_from'] = '';
        $data['usertype'] = 'All';



        if ($this->request->getMethod() == 'POST') {
            $start_date = date("Y-m-d H:i:s", strtotime(trim($this->request->getPost('stdt'))));
            $end_date = date("Y-m-d H:i:s", strtotime(trim($this->request->getPost('endt'))));
            $data['stdt'] = date("d-m-Y H:i", strtotime(trim($this->request->getPost('stdt'))));
            $data['endt'] = date("d-m-Y H:i", strtotime(trim($this->request->getPost('endt'))));
            $data['usertype'] = trim($this->request->getPost('usertype')) ;
            $data['schema'] = $schemaname = $this->schema;

            $user_id = $this->sessdata['user_id'];
            $subUsers = $this->getSubUsers($user_id);

            // Include logged-in user
            $allowedUsers = array_merge([$user_id], $subUsers);
            $placeholders = implode(',', array_fill(0, count($allowedUsers), '?'));

            $sql = "SELECT 
                {$this->schema}.tbl_trip.deviceid, 
                {$this->schema}.tbl_trip.imeino, 
                {$this->schema}.tbl_trip.stpole as actual_stpole, 
                {$this->schema}.tbl_trip.endpole as actual_endpole,
                {$this->schema}.tbl_trip.sttimestamp as actual_starttime, {$this->schema}.tbl_trip.endtimestamp as actual_endtime,
                {$this->schema}.tbl_trip.totaldistancetravel, {$this->schema}.tbl_trip.timetravelled,
                (
                    SELECT COUNT(*) 
                    FROM public.tbl_trip_stoppage 
                    WHERE tbl_trip_stoppage.trip_id = tbl_trip.trip_id
                ) AS stoppage_count,
                (
                    SELECT string_agg(
                        TRIM(
                            CASE 
                                WHEN EXTRACT(hour FROM stoppage_duration) > 0 THEN EXTRACT(hour FROM stoppage_duration)::int || ' hours '
                                ELSE ''
                            END ||
                            CASE 
                                WHEN EXTRACT(minute FROM stoppage_duration) > 0 THEN EXTRACT(minute FROM stoppage_duration)::int || ' mins '
                                ELSE ''
                            END ||
                            CASE 
                                WHEN EXTRACT(second FROM stoppage_duration) > 0 THEN EXTRACT(second FROM stoppage_duration)::int || ' secs'
                                ELSE '0 sec'
                            END
                        ) || ' at (' || pole || ') on ' || to_char(stoppage_start, 'dd-mm HH24:MI'),
                        E'\n' ORDER BY stoppage_start
                    ) 
                    FROM public.tbl_trip_stoppage 
                    WHERE tbl_trip_stoppage.trip_id = tbl_trip.trip_id
                ) AS stoppage_names
                FROM {$this->schema}.tbl_trip
                JOIN {$this->schema}.trip_schedule_details tsd ON tsd.schedule_details_id = {$this->schema}.tbl_trip.schedule_details_id
                JOIN {$this->schema}.trip_schedule ts ON ts.schedule_id = tsd.schedule_id
                LEFT JOIN public.master_device_assign mda 
                    ON mda.deviceid = {$this->schema}.tbl_trip.deviceid  
                LEFT JOIN public.user_login ul 
                    ON ul.user_id = mda.user_id   
                LEFT JOIN public.user_login p_ul 
                    ON p_ul.user_id = mda.parent_id  
                WHERE 
                (
                        (sttimestamp BETWEEN ? AND ?)
                    OR  (endtimestamp   BETWEEN ? AND ?) 
                    OR (sttimestamp < ? AND endtimestamp > ?) 
                ) 
                AND ul.group_id = 2
                AND mda.active = 1 ";

            $parameters = [$start_date, $end_date,$start_date, $end_date,$start_date, $end_date];  

            $sql .= " AND mda.user_id IN ($placeholders)";
            $parameters = array_merge($parameters, $allowedUsers);

            /// Apply usertype filter only if it is NOT "All"
            if (!empty($data['usertype']) && $data['usertype'] !== "All") {
                $sql .= " AND ts.device_type = ?";
                $parameters[] = $data['usertype'];
            }

            $sql .= " ORDER BY {$this->schema}.tbl_trip.imeino, sttimestamp ASC";

            $result = $this->db->query($sql, $parameters)->getResult();


            $data['alldata'] = $result;
        }

        $data['sse_pwy'] = trim($this->request->getPost('pway_id'));
        $data['pwi_id'] = trim($this->request->getPost('user'));
        $data['pwi_name'] = trim($this->request->getPost('pwi_name'));
        $data['device_id'] = $device_id = trim($this->request->getPost('device_id'));
        // $data['usertype'] = trim($this->request->getPost('usertype')) ;

        $data['middle'] = view('report/geofencegroup', $data);
        return view('mainlayout', $data);
    }

    public function geofencegroupExcel(){
  
        // Check if user is logged in
        if (!session()->has('login_sess_data')) {
            return redirect()->to('/');
        }

        $data = [];
        $data['sessdata'] = $this->sessdata;
        $data['page_title'] = "Geofence Group Report";
        $data['date_from'] = '';

        $user_id = $this->sessdata['user_id'];

        if ($this->request->getMethod() == 'POST') {
            // echo "<pre>";print_r($this->request->getPost());exit();
            $start_date = date("Y-m-d H:i:s", strtotime(trim($this->request->getPost('stdt'))));
            $end_date = date("Y-m-d H:i:s", strtotime(trim($this->request->getPost('endt'))));
            $data['stdt'] = date("d-m-Y H:i", strtotime(trim($this->request->getPost('stdt'))));
            $data['endt'] = date("d-m-Y H:i", strtotime(trim($this->request->getPost('endt'))));
            $data['usertype'] = trim($this->request->getPost('usertype')) ;
            $data['schema'] = $schemaname = $this->schema;

            $user_id = $this->sessdata['user_id'];

            $sql = "SELECT 
                {$this->schema}.tbl_trip.deviceid, 
                {$this->schema}.tbl_trip.imeino, 
                {$this->schema}.tbl_trip.stpole as actual_stpole, 
                {$this->schema}.tbl_trip.endpole as actual_endpole,
                {$this->schema}.tbl_trip.sttimestamp as actual_starttime, {$this->schema}.tbl_trip.endtimestamp as actual_endtime,
                {$this->schema}.tbl_trip.totaldistancetravel, {$this->schema}.tbl_trip.timetravelled,
                (
                    SELECT COUNT(*) 
                    FROM public.tbl_trip_stoppage 
                    WHERE tbl_trip_stoppage.trip_id = tbl_trip.trip_id
                ) AS stoppage_count
                FROM {$this->schema}.tbl_trip
                JOIN {$this->schema}.trip_schedule_details tsd ON tsd.schedule_details_id = {$this->schema}.tbl_trip.schedule_details_id
                JOIN {$this->schema}.trip_schedule ts ON ts.schedule_id = tsd.schedule_id
                WHERE 
                (
                        (sttimestamp BETWEEN ? AND ?)
                    OR  (endtimestamp   BETWEEN ? AND ?) 
                    OR (sttimestamp < ? AND endtimestamp > ?) 
                ) ";

            $parameters = [$start_date, $end_date,$start_date, $end_date,$start_date, $end_date];  

            /// Apply usertype filter only if it is NOT "All"
            if (!empty($data['usertype']) && $data['usertype'] !== "All") {
                $sql .= " AND ts.device_type = ?";
                $parameters[] = $data['usertype'];
            }

            $sql .= " ORDER BY {$this->schema}.tbl_trip.imeino, sttimestamp ASC";

            // echo $sql;exit();

            $result = $this->db->query($sql, $parameters)->getResult();


            $data['alldata'] = $result;
        }

        $dat[0]['A'] = " Geofence Group Report From Date " . date("d-m-Y H:i:s", strtotime($start_date)) . " To " . date("d-m-Y H:i:s", strtotime($end_date));

        // Initialize header row
        $dat[1]['A'] = "Device ID";
        $dat[1]['B'] = "Entry";
        $dat[1]['C'] = "";
        $dat[1]['D'] = "Exit";
        $dat[1]['E'] = "";
        $dat[1]['F'] = "Travelled Time";
        $dat[1]['G'] = "Travelled Distance(KM)";
        $dat[1]['H'] = "Stoppages";

        $dat[2]['A'] = "";
        $dat[2]['B'] = "Date & Time";
        $dat[2]['C'] = "Address";
        $dat[2]['D'] = "Date & Time";
        $dat[2]['E'] = "Address";
        $dat[2]['F'] = "";
        $dat[2]['G'] = "";
        $dat[2]['H'] = "";
        
        // Initialize counters
        $Key = 2;
        $timetravelled = 0;
        foreach ($result as $irow) {
            $timetravelled = strtotime($irow->timetravelled);
            $seconds = (int) $timetravelled; // Ensure it's an integer

            $days = floor($seconds / 86400); 
            $seconds %= 86400; 

            $hours = floor($seconds / 3600);
            $seconds %= 3600;

            $minutes = floor($seconds / 60);
            $seconds %= 60;

            $formatted = "{$hours} hours {$minutes} mins {$seconds} sec";

            // Fill data for each row
            $dat[$Key + 1]['A'] = "ID ".$irow->imeino;
            $dat[$Key + 1]['B'] = date("Y-m-d H:i:s", strtotime($irow->actual_starttime));
            $dat[$Key + 1]['C'] = ($irow->actual_stpole ?? '');
            $dat[$Key + 1]['D'] = date("Y-m-d H:i:s", strtotime($irow->actual_endtime));
            $dat[$Key + 1]['E'] = ($irow->actual_endpole ?? '');
            $dat[$Key + 1]['F'] = $formatted;
            $dat[$Key + 1]['G'] = number_format($irow->totaldistancetravel,4) . 'KM';
            $dat[$Key + 1]['H'] = $irow->stoppage_count;

            $Key++;
        }

        // Create the Excel file
        $filename = 'Geofence_Group_Report_' . date("d-m-Y H:i", strtotime($start_date)) . '_To_' . date("d-m-Y H:i", strtotime($end_date)) . '.xlsx';
        exceldownload($dat, $filename);

    }

    public function geofencegroupPdf(){ 
        // Check if user is logged in
        if (!session()->has('login_sess_data')) {
            return redirect()->to('/');
        }

        $data = [];
        $data['sessdata'] = $this->sessdata;
        $data['page_title'] = "Geofence Group Report";
        $data['date_from'] = '';
        $data['usertype'] = 'All';

        if ($this->request->getMethod() == 'POST') {
            $start_date = date("Y-m-d H:i:s", strtotime(trim($this->request->getPost('stdt'))));
            $end_date = date("Y-m-d H:i:s", strtotime(trim($this->request->getPost('endt'))));
            $data['stdt'] = date("d-m-Y H:i", strtotime(trim($this->request->getPost('stdt'))));
            $data['endt'] = date("d-m-Y H:i", strtotime(trim($this->request->getPost('endt'))));
            $data['usertype'] = trim($this->request->getPost('usertype')) ;
            $data['schema'] = $schemaname = $this->schema;

            $user_id = $this->sessdata['user_id'];

            $sql = "SELECT 
                {$this->schema}.tbl_trip.deviceid, 
                {$this->schema}.tbl_trip.imeino, 
                {$this->schema}.tbl_trip.stpole as actual_stpole, 
                {$this->schema}.tbl_trip.endpole as actual_endpole,
                {$this->schema}.tbl_trip.sttimestamp as actual_starttime, {$this->schema}.tbl_trip.endtimestamp as actual_endtime,
                {$this->schema}.tbl_trip.totaldistancetravel, {$this->schema}.tbl_trip.timetravelled,
                (
                    SELECT COUNT(*) 
                    FROM public.tbl_trip_stoppage 
                    WHERE tbl_trip_stoppage.trip_id = tbl_trip.trip_id
                ) AS stoppage_count
                FROM {$this->schema}.tbl_trip
                JOIN {$this->schema}.trip_schedule_details tsd ON tsd.schedule_details_id = {$this->schema}.tbl_trip.schedule_details_id
                JOIN {$this->schema}.trip_schedule ts ON ts.schedule_id = tsd.schedule_id
                WHERE 
                (
                        (sttimestamp BETWEEN ? AND ?)
                    OR  (endtimestamp   BETWEEN ? AND ?) 
                    OR (sttimestamp < ? AND endtimestamp > ?) 
                ) ";

            $parameters = [$start_date, $end_date,$start_date, $end_date,$start_date, $end_date];  

            /// Apply usertype filter only if it is NOT "All"
            if (!empty($data['usertype']) && $data['usertype'] !== "All") {
                $sql .= " AND ts.device_type = ?";
                $parameters[] = $data['usertype'];
            }

            $sql .= " ORDER BY {$this->schema}.tbl_trip.imeino, sttimestamp ASC";

            $result = $this->db->query($sql, $parameters)->getResult();


            $data['alldata'] = $result;
        }

        $html = view('report/pdf_geofencegroup', $data); // Load view in CI4
        $filename = 'Geofence_Report_' . time();

        // Instantiate the MakePDF class
        $pdf = new MakePDF();

        // Set the filename and content
        $pdf->setFileName($filename);
        $pdf->setContent($html);

        // Generate and stream the PDF to the browser
        $pdf->getPdf();  // true to stream the PDF
    }

    public function patrolling(){ 
        // Check if user is logged in
        if (!session()->has('login_sess_data')) {
            return redirect()->to('/');
        }

        $data = [];
        $data['sessdata'] = $this->sessdata;
        $data['page_title'] = "Patrolling Report";
        $data['date_from'] = '';
        $data['usertype'] = 'All';

        $data['devicedropdown'] = $this->db->query("SELECT a.*, 
        (SELECT device_name FROM {$this->schema}.master_device_setup  
            WHERE id = (SELECT max(id) 
                        FROM {$this->schema}.master_device_setup 
                        WHERE inserttime::date <= current_date::date  
                        AND deviceid = a.did)) 
        AS device_name 
        FROM public.get_divice_details_record_for_list_for_company('{$this->schema}',{$this->sessdata['user_id']}) AS a 
        WHERE a.group_id = 2 AND a.active = 1")->getResult();

        // Get PWAY users
        $data['pway'] = $this->db->query("SELECT organisation, user_id 
        FROM public.user_login 
        WHERE active = 1 AND group_id = 8")->getResult();

        // Get users
        $data['usersdd'] = $this->commonModel->get_users();

        if ($this->request->getMethod() == 'POST') {

            $user_id = $this->sessdata['user_id'];
            $subUsers = $this->getSubUsers($user_id);

            // Include logged-in user
            $allowedUsers = array_merge([$user_id], $subUsers);
            $placeholders = implode(',', array_fill(0, count($allowedUsers), '?'));

            // echo "<pre>";print_r($this->request->getPost());exit();
            $start_date = date("Y-m-d H:i:s", strtotime(trim($this->request->getPost('stdt'))));
            $end_date = date("Y-m-d H:i:s", strtotime(trim($this->request->getPost('endt'))));
            $data['stdt'] = date("d-m-Y H:i", strtotime(trim($this->request->getPost('stdt'))));
            $data['endt'] = date("d-m-Y H:i", strtotime(trim($this->request->getPost('endt'))));
            $data['usertype'] = trim($this->request->getPost('usertype')) ;
            $data['schema'] = $schemaname = $this->schema;
            $data['pwi_id'] = trim($this->request->getPost('user'));
            $data['pwi_name'] = trim($this->request->getPost('pwi_name'));
            $data['device_id'] = $device_id = trim($this->request->getPost('device_id'));
            $data['sse_pwy'] = $sse_pwy = trim($this->request->getPost('pway_id'));

            $user_id = $this->sessdata['user_id'];

            $sql = "SELECT 
                {$this->schema}.tbl_trip.imeino, 
                ul.organisation,     -- ✅ Added organisation
                stpole as actual_stpole, 
                endpole as actual_endpole,
                sttimestamp as actual_starttime, 
                endtimestamp as actual_endtime, 
                totaldistancetravel, startbattery,
                {$this->schema}.tbl_trip.avg_speed, {$this->schema}.tbl_trip.max_speed
                FROM {$this->schema}.tbl_trip
                LEFT JOIN public.master_device_assign mda 
                    ON mda.deviceid = {$this->schema}.tbl_trip.deviceid  -- ✅ Join with `master_device_assign` to get user_id
                LEFT JOIN public.user_login ul 
                    ON ul.user_id = mda.user_id   -- ✅ Get `organisation`
                JOIN {$this->schema}.trip_schedule_details tsd ON tsd.schedule_details_id = {$this->schema}.tbl_trip.schedule_details_id
                JOIN {$this->schema}.trip_schedule ts ON ts.schedule_id = tsd.schedule_id
                WHERE 
                (
                        (sttimestamp BETWEEN ? AND ?)
                    OR  (endtimestamp   BETWEEN ? AND ?) 
                    OR (sttimestamp < ? AND endtimestamp > ?) 
                ) 
                AND ul.group_id = 2
                AND mda.active = 1 ";

                $parameters = [$start_date, $end_date,$start_date, $end_date,$start_date, $end_date];

                $sql .= " AND mda.user_id IN ($placeholders)";
                $parameters = array_merge($parameters, $allowedUsers);

                // Apply usertype filter only if it is NOT "All"
                if (!empty($data['usertype']) && $data['usertype'] !== "All") {
                    $sql .= " AND ts.device_type = ?";
                    $parameters[] = $data['usertype'];
                }

            $sql .= " ORDER BY {$this->schema}.tbl_trip.imeino, sttimestamp ASC";

            $result = $this->db->query($sql, $parameters)->getResult();

            $data['alldata'] = $result;
        }

        $data['sse_pwy'] = trim($this->request->getPost('pway_id'));
        $data['pwi_id'] = trim($this->request->getPost('user'));
        $data['pwi_name'] = trim($this->request->getPost('pwi_name'));
        $data['device_id'] = $device_id = trim($this->request->getPost('device_id'));

        $data['middle'] = view('report/patrolling', $data);
        return view('mainlayout', $data);
    }

    public function patrollingExcel(){
  
        // Check if user is logged in
        if (!session()->has('login_sess_data')) {
            return redirect()->to('/');
        }

        $data = [];
        $data['sessdata'] = $this->sessdata;
        $data['page_title'] = "Trip Details Report";
        $data['date_from'] = '';

        $user_id = $this->sessdata['user_id'];

        if ($this->request->getMethod() == 'POST') {

            $user_id = $this->sessdata['user_id'];
            $subUsers = $this->getSubUsers($user_id);

            // Include logged-in user
            $allowedUsers = array_merge([$user_id], $subUsers);
            $placeholders = implode(',', array_fill(0, count($allowedUsers), '?'));

            // echo "<pre>";print_r($this->request->getPost());exit();
            $start_date = date("Y-m-d H:i:s", strtotime(trim($this->request->getPost('stdt'))));
            $end_date = date("Y-m-d H:i:s", strtotime(trim($this->request->getPost('endt'))));
            $data['stdt'] = date("d-m-Y H:i", strtotime(trim($this->request->getPost('stdt'))));
            $data['endt'] = date("d-m-Y H:i", strtotime(trim($this->request->getPost('endt'))));
            $data['usertype'] = trim($this->request->getPost('usertype')) ;
            $data['schema'] = $schemaname = $this->schema;

            $user_id = $this->sessdata['user_id'];

            $sql = "SELECT 
                {$this->schema}.tbl_trip.imeino, 
                ul.organisation,     -- ✅ Added organisation
                stpole as actual_stpole, 
                endpole as actual_endpole,
                sttimestamp as actual_starttime, 
                endtimestamp as actual_endtime, 
                totaldistancetravel, startbattery,
                {$this->schema}.tbl_trip.avg_speed, {$this->schema}.tbl_trip.max_speed
                FROM {$this->schema}.tbl_trip
                LEFT JOIN public.master_device_assign mda 
                    ON mda.deviceid = {$this->schema}.tbl_trip.deviceid  -- ✅ Join with `master_device_assign` to get user_id
                LEFT JOIN public.user_login ul 
                    ON ul.user_id = mda.user_id   -- ✅ Get `organisation`
                JOIN {$this->schema}.trip_schedule_details tsd ON tsd.schedule_details_id = {$this->schema}.tbl_trip.schedule_details_id
                JOIN {$this->schema}.trip_schedule ts ON ts.schedule_id = tsd.schedule_id
                WHERE 
                (
                        (sttimestamp BETWEEN ? AND ?)
                    OR  (endtimestamp   BETWEEN ? AND ?) 
                    OR (sttimestamp < ? AND endtimestamp > ?) 
                ) 
                AND ul.group_id = 2
                AND mda.active = 1 ";

                $parameters = [$start_date, $end_date,$start_date, $end_date,$start_date, $end_date];

                $sql .= " AND mda.user_id IN ($placeholders)";
                $parameters = array_merge($parameters, $allowedUsers);

                // Apply usertype filter only if it is NOT "All"
                if (!empty($data['usertype']) && $data['usertype'] !== "All") {
                    $sql .= " AND ts.device_type = ?";
                    $parameters[] = $data['usertype'];
                }

            $sql .= " ORDER BY {$this->schema}.tbl_trip.imeino, sttimestamp ASC";

            // echo $sql;exit();

            $result = $this->db->query($sql, $parameters)->getResult();


            $data['alldata'] = $result;
        }

        $dat[0]['A'] = "Patrlling Report From Date " . date("d-m-Y H:i:s", strtotime($start_date)) . " To " . date("d-m-Y H:i:s", strtotime($end_date));

        // Initialize header row
        $dat[1]['A'] = "Device ID";
        $dat[1]['B'] = "Section";
        $dat[1]['C'] = "Start Date Time";
        $dat[1]['D'] = "Start Address";
        $dat[1]['E'] = "End Date Time";
        $dat[1]['F'] = "End Address";
        $dat[1]['G'] = "KM Run";
        $dat[1]['H'] = "Overspeed";
        $dat[1]['I'] = "Avg Speed";
        $dat[1]['J'] = "Max Speed";
        $dat[1]['K'] = "Start Low Battery";
        
        // Initialize counters
        $Key = 1;

        // Grouping Data by Device ID
        $groupedData = [];
        foreach ($result as $row) {
            $deviceId = $row->imeino;
            
            if (!isset($groupedData[$deviceId])) {
                // Initialize the device entry with the first row values
                $groupedData[$deviceId] = $row;
                $groupedData[$deviceId]->actual_starttime = $row->actual_starttime; // First Start Time
                $groupedData[$deviceId]->actual_endtime = $row->actual_endtime; // Last End Time
                $groupedData[$deviceId]->startbattery = $row->startbattery; // Start battery
                $groupedData[$deviceId]->totaldistancetravel = $row->totaldistancetravel;
                $groupedData[$deviceId]->avg_speed = $row->avg_speed;
                $groupedData[$deviceId]->max_speed = $row->max_speed;
            } else {
                // Update the End Time with the latest occurrence
                $groupedData[$deviceId]->actual_endtime = $row->actual_endtime;
                $groupedData[$deviceId]->totaldistancetravel = $row->totaldistancetravel;
                $groupedData[$deviceId]->avg_speed += $row->avg_speed;
                $groupedData[$deviceId]->max_speed += $row->max_speed;
            }
        }

        foreach ($groupedData as $irow) {
            $avg_speed = number_format($irow->avg_speed,4);
            $startbattery = $irow->startbattery;

            if($avg_speed > 5) { $os = 'Yes';} else { $os = 'No';}
            if($startbattery > 50) { $os1 = 'No';} else { $os1 = 'No';}

            // Fill data for each row
            $dat[$Key + 1]['A'] = $irow->imeino;
            $dat[$Key + 1]['B'] = $irow->organisation;
            $dat[$Key + 1]['C'] = date("Y-m-d H:i:s", strtotime($irow->actual_starttime));
            $dat[$Key + 1]['D'] = ($irow->expected_stpole ?? '');
            $dat[$Key + 1]['E'] = date("Y-m-d H:i:s", strtotime($irow->actual_endtime));
            $dat[$Key + 1]['F'] = ($irow->expected_endpole ?? '');
            $dat[$Key + 1]['G'] = number_format($irow->totaldistancetravel,4);
            $dat[$Key + 1]['H'] = $os;
            $dat[$Key + 1]['I'] = number_format($irow->avg_speed,4);
            $dat[$Key + 1]['J'] = number_format($irow->max_speed,4);
            $dat[$Key + 1]['k'] = $os1;

            $Key++;
        }

        // Create the Excel file
        $filename = 'Patrolling_Report_' . date("d-m-Y H:i", strtotime($start_date)) . '_To_' . date("d-m-Y H:i", strtotime($end_date)) . '.xlsx';
        exceldownload($dat, $filename);

    }

    public function patrollingPdf(){ 
        // Check if user is logged in
        if (!session()->has('login_sess_data')) {
            return redirect()->to('/');
        }

        $data = [];
        $data['sessdata'] = $this->sessdata;
        $data['page_title'] = "Patrolling Report";
        $data['date_from'] = '';
        $data['usertype'] = 'All';

        $data['devicedropdown'] = $this->db->query("SELECT a.*, 
        (SELECT device_name FROM {$this->schema}.master_device_setup  
            WHERE id = (SELECT max(id) 
                        FROM {$this->schema}.master_device_setup 
                        WHERE inserttime::date <= current_date::date  
                        AND deviceid = a.did)) 
        AS device_name 
        FROM public.get_divice_details_record_for_list_for_company('{$this->schema}',{$this->sessdata['user_id']}) AS a 
        WHERE a.group_id = 2 AND a.active = 1")->getResult();

        // Get PWAY users
        $data['pway'] = $this->db->query("SELECT organisation, user_id 
        FROM public.user_login 
        WHERE active = 1 AND group_id = 8")->getResult();

        // Get users
        $data['usersdd'] = $this->commonModel->get_users();

        if ($this->request->getMethod() == 'POST') {

            $user_id = $this->sessdata['user_id'];
            $subUsers = $this->getSubUsers($user_id);

            // Include logged-in user
            $allowedUsers = array_merge([$user_id], $subUsers);
            $placeholders = implode(',', array_fill(0, count($allowedUsers), '?'));
            
            // echo "<pre>";print_r($this->request->getPost());exit();
            $start_date = date("Y-m-d H:i:s", strtotime(trim($this->request->getPost('stdt'))));
            $end_date = date("Y-m-d H:i:s", strtotime(trim($this->request->getPost('endt'))));
            $data['stdt'] = date("d-m-Y H:i", strtotime(trim($this->request->getPost('stdt'))));
            $data['endt'] = date("d-m-Y H:i", strtotime(trim($this->request->getPost('endt'))));
            $data['usertype'] = trim($this->request->getPost('usertype')) ;
            $data['schema'] = $schemaname = $this->schema;
            $data['pwi_id'] = trim($this->request->getPost('user'));
            $data['pwi_name'] = trim($this->request->getPost('pwi_name'));
            $data['device_id'] = $device_id = trim($this->request->getPost('device_id'));
            $data['sse_pwy'] = $sse_pwy = trim($this->request->getPost('pway_id'));

            $user_id = $this->sessdata['user_id'];

            $sql = "SELECT 
                {$this->schema}.tbl_trip.imeino, 
                ul.organisation,     -- ✅ Added organisation
                stpole as actual_stpole, 
                endpole as actual_endpole,
                sttimestamp as actual_starttime, 
                endtimestamp as actual_endtime, 
                totaldistancetravel, startbattery,
                {$this->schema}.tbl_trip.avg_speed, {$this->schema}.tbl_trip.max_speed
                FROM {$this->schema}.tbl_trip
                LEFT JOIN public.master_device_assign mda 
                    ON mda.deviceid = {$this->schema}.tbl_trip.deviceid  -- ✅ Join with `master_device_assign` to get user_id
                LEFT JOIN public.user_login ul 
                    ON ul.user_id = mda.user_id   -- ✅ Get `organisation`
                JOIN {$this->schema}.trip_schedule_details tsd ON tsd.schedule_details_id = {$this->schema}.tbl_trip.schedule_details_id
                JOIN {$this->schema}.trip_schedule ts ON ts.schedule_id = tsd.schedule_id
                WHERE 
                (
                        (sttimestamp BETWEEN ? AND ?)
                    OR  (endtimestamp   BETWEEN ? AND ?) 
                    OR (sttimestamp < ? AND endtimestamp > ?) 
                ) 
                AND ul.group_id = 2
                AND mda.active = 1 ";

                $parameters = [$start_date, $end_date,$start_date, $end_date,$start_date, $end_date];

                $sql .= " AND mda.user_id IN ($placeholders)";
                $parameters = array_merge($parameters, $allowedUsers);

                // Apply usertype filter only if it is NOT "All"
                if (!empty($data['usertype']) && $data['usertype'] !== "All") {
                    $sql .= " AND ts.device_type = ?";
                    $parameters[] = $data['usertype'];
                }

            $sql .= " ORDER BY {$this->schema}.tbl_trip.imeino, sttimestamp ASC";

            // echo $sql;exit();

            $result = $this->db->query($sql, $parameters)->getResult();


            $data['alldata'] = $result;
        }

        $html = view('report/pdf_patrolling', $data); // Load view in CI4
        $filename = 'Patrolling_Report_' . time();

        // Instantiate the MakePDF class
        $pdf = new MakePDF();

        // Set the filename and content
        $pdf->setFileName($filename);
        $pdf->setContent($html);

        // Generate and stream the PDF to the browser
        $pdf->getPdf();  // true to stream the PDF
    }

    public function stoppage(){ 
        // Check if user is logged in
        if (!session()->has('login_sess_data')) {
            return redirect()->to('/');
        }

        $data = [];
        $data['sessdata'] = $this->sessdata;
        $data['page_title'] = "Stoppage Report";
        $data['date_from'] = '';
        $data['usertype'] = 'All';

        if ($this->request->getMethod() == 'POST') {
            $user_id = $this->sessdata['user_id'];
            $subUsers = $this->getSubUsers($user_id);

            // Include logged-in user
            $allowedUsers = array_merge([$user_id], $subUsers);
            $placeholders = implode(',', array_fill(0, count($allowedUsers), '?'));

            $start_date = date("Y-m-d H:i:s", strtotime(trim($this->request->getPost('stdt'))));
            $end_date = date("Y-m-d H:i:s", strtotime(trim($this->request->getPost('endt'))));
            $data['stdt'] = date("d-m-Y H:i", strtotime(trim($this->request->getPost('stdt'))));
            $data['endt'] = date("d-m-Y H:i", strtotime(trim($this->request->getPost('endt'))));
            $data['usertype'] = $usertype = trim($this->request->getPost('usertype')) ;

            $sql = " SELECT tts.pole, tts.stoppage_start, tts.stoppage_duration, tts.imeino, mds.device_name
                FROM public.tbl_trip_stoppage tts
                JOIN public.master_device_details mdd ON mdd.serial_no = tts.imeino
                JOIN {$this->schema}.master_device_setup mds ON mds.deviceid = mdd.id
                JOIN {$this->schema}.tbl_trip tt ON tt.trip_id = tts.trip_id
                JOIN {$this->schema}.trip_schedule_details tsd ON tsd.schedule_details_id = tt.schedule_details_id
                JOIN {$this->schema}.trip_schedule ts ON ts.schedule_id = tsd.schedule_id
                LEFT JOIN public.master_device_assign mda 
                    ON mda.deviceid = tt.deviceid  
                LEFT JOIN public.user_login ul 
                    ON ul.user_id = mda.user_id   
                LEFT JOIN public.user_login p_ul 
                    ON p_ul.user_id = mda.parent_id  
                WHERE (tts.stoppage_start BETWEEN ? AND ?) ";

            $parameters = [$start_date, $end_date];

            $sql .= " AND mda.user_id IN ($placeholders)";
            $parameters = array_merge($parameters, $allowedUsers);

            // Apply usertype filter only if it is NOT "All"
            if (!empty($usertype) && $usertype !== "All") {
                $sql .= " AND ts.device_type = ?";
                $parameters[] = $usertype;
            }
            $sql .= "ORDER BY mds.device_name DESC;
            ";
            $result = $this->db->query($sql, $parameters)->getResult();

            $data['alldata'] = $result;
        }

        $data['middle'] = view('report/stoppagereport', $data);
        return view('mainlayout', $data);
    }

    public function stoppageExcel(){ 
        // Check if user is logged in
        if (!session()->has('login_sess_data')) {
            return redirect()->to('/');
        }

        $data = [];
        $data['sessdata'] = $this->sessdata;
        $data['page_title'] = "Stoppage Report";
        $data['date_from'] = '';
        $data['usertype'] = 'All';

        if ($this->request->getMethod() == 'POST') {
            $user_id = $this->sessdata['user_id'];
            $subUsers = $this->getSubUsers($user_id);

            // Include logged-in user
            $allowedUsers = array_merge([$user_id], $subUsers);
            $placeholders = implode(',', array_fill(0, count($allowedUsers), '?'));

            $start_date = date("Y-m-d H:i:s", strtotime(trim($this->request->getPost('stdt'))));
            $end_date = date("Y-m-d H:i:s", strtotime(trim($this->request->getPost('endt'))));
            $data['stdt'] = date("d-m-Y H:i", strtotime(trim($this->request->getPost('stdt'))));
            $data['endt'] = date("d-m-Y H:i", strtotime(trim($this->request->getPost('endt'))));
            $data['usertype'] = $usertype = trim($this->request->getPost('usertype')) ;

            $sql = " SELECT tts.pole, tts.stoppage_start, tts.stoppage_duration, tts.imeino, mds.device_name
                FROM public.tbl_trip_stoppage tts
                JOIN public.master_device_details mdd ON mdd.serial_no = tts.imeino
                JOIN {$this->schema}.master_device_setup mds ON mds.deviceid = mdd.id
                JOIN {$this->schema}.tbl_trip tt ON tt.trip_id = tts.trip_id
                JOIN {$this->schema}.trip_schedule_details tsd ON tsd.schedule_details_id = tt.schedule_details_id
                JOIN {$this->schema}.trip_schedule ts ON ts.schedule_id = tsd.schedule_id
                LEFT JOIN public.master_device_assign mda 
                    ON mda.deviceid = tt.deviceid  
                LEFT JOIN public.user_login ul 
                    ON ul.user_id = mda.user_id   
                LEFT JOIN public.user_login p_ul 
                    ON p_ul.user_id = mda.parent_id  
                WHERE (tts.stoppage_start BETWEEN ? AND ?) ";

            $parameters = [$start_date, $end_date];

            $sql .= " AND mda.user_id IN ($placeholders)";
            $parameters = array_merge($parameters, $allowedUsers);

            // Apply usertype filter only if it is NOT "All"
            if (!empty($usertype) && $usertype !== "All") {
                $sql .= " AND ts.device_type = ?";
                $parameters[] = $usertype;
            }
            $sql .= "ORDER BY mds.device_name DESC;
            ";
            $result = $this->db->query($sql, $parameters)->getResult();

            $data['alldata'] = $result;
        }

        $dat[0]['A'] = "Stoppage Report From Date " . date("d-m-Y H:i:s", strtotime($start_date)) . " To " . date("d-m-Y H:i:s", strtotime($end_date));

        // Initialize header row
        $dat[1]['A'] = "Device No";
        $dat[1]['B'] = "Device Name";
        $dat[1]['C'] = "Stoppage Start Time";
        $dat[1]['D'] = "Stoppage Address";
        $dat[1]['E'] = "Halt Time";
        
        // Initialize counters
        $Key = 1;

        foreach ($result as $irow) {
            // Fill data for each row
            $dat[$Key + 1]['A'] = $irow->imeino;
            $dat[$Key + 1]['B'] = $irow->device_name;
            $dat[$Key + 1]['C'] = date("Y-m-d H:i:s", strtotime($irow->stoppage_start));
            $dat[$Key + 1]['D'] = $irow->pole;
            $dat[$Key + 1]['E'] = $irow->stoppage_duration;

            $Key++;
        }

        // Create the Excel file
        $filename = 'Stoppage_Report_' . date("d-m-Y H:i", strtotime($start_date)) . '_To_' . date("d-m-Y H:i", strtotime($end_date)) . '.xlsx';
        exceldownload($dat, $filename);
    }

    public function stoppagePdf(){ 
        // Check if user is logged in
        if (!session()->has('login_sess_data')) {
            return redirect()->to('/');
        }

        ini_set('memory_limit', '512M'); // or '256M', or '-1' for unlimited

        $data = [];
        $data['sessdata'] = $this->sessdata;
        $data['page_title'] = "Stoppage Report";
        $data['date_from'] = '';
        $data['usertype'] = 'All';

        if ($this->request->getMethod() == 'POST') {
            $user_id = $this->sessdata['user_id'];
            $subUsers = $this->getSubUsers($user_id);

            // Include logged-in user
            $allowedUsers = array_merge([$user_id], $subUsers);
            $placeholders = implode(',', array_fill(0, count($allowedUsers), '?'));

            $start_date = date("Y-m-d H:i:s", strtotime(trim($this->request->getPost('stdt'))));
            $end_date = date("Y-m-d H:i:s", strtotime(trim($this->request->getPost('endt'))));
            $data['stdt'] = date("d-m-Y H:i", strtotime(trim($this->request->getPost('stdt'))));
            $data['endt'] = date("d-m-Y H:i", strtotime(trim($this->request->getPost('endt'))));
            $data['usertype'] = $usertype = trim($this->request->getPost('usertype')) ;

            $sql = " SELECT tts.pole, tts.stoppage_start, tts.stoppage_duration, tts.imeino, mds.device_name
                FROM public.tbl_trip_stoppage tts
                JOIN public.master_device_details mdd ON mdd.serial_no = tts.imeino
                JOIN {$this->schema}.master_device_setup mds ON mds.deviceid = mdd.id
                JOIN {$this->schema}.tbl_trip tt ON tt.trip_id = tts.trip_id
                JOIN {$this->schema}.trip_schedule_details tsd ON tsd.schedule_details_id = tt.schedule_details_id
                JOIN {$this->schema}.trip_schedule ts ON ts.schedule_id = tsd.schedule_id
                LEFT JOIN public.master_device_assign mda 
                    ON mda.deviceid = tt.deviceid  
                LEFT JOIN public.user_login ul 
                    ON ul.user_id = mda.user_id   
                LEFT JOIN public.user_login p_ul 
                    ON p_ul.user_id = mda.parent_id 
                WHERE (tts.stoppage_start BETWEEN ? AND ?) ";

            $parameters = [$start_date, $end_date];

            $sql .= " AND mda.user_id IN ($placeholders)";
            $parameters = array_merge($parameters, $allowedUsers);

            // Apply usertype filter only if it is NOT "All"
            if (!empty($usertype) && $usertype !== "All") {
                $sql .= " AND ts.device_type = ?";
                $parameters[] = $usertype;
            }
            $sql .= "ORDER BY mds.device_name DESC;
            ";
            $result = $this->db->query($sql, $parameters)->getResult();

            $data['alldata'] = $result;
        }

        $html = view('report/pdf_stoppage', $data); // Load view in CI4
        $filename = 'Stoppage_Report_' . time();

        // Instantiate the MakePDF class
        $pdf = new MakePDF();

        // Set the filename and content
        $pdf->setFileName($filename);
        $pdf->setContent($html);

        // Generate and stream the PDF to the browser
        $pdf->getPdf();  // true to stream the PDF
    }

    public function patrollingGraph(){ 
        // Check if user is logged in
        if (!session()->has('login_sess_data')) {
            return redirect()->to('/');
        }

        $data = [];
        $data['sessdata'] = $this->sessdata;
        $data['page_title'] = "Patrolling Graph";
        $data['date_from'] = '';
        $data['devicedropdown'] = $this->db->query("SELECT a.*, 
        (SELECT device_name FROM {$this->schema}.master_device_setup  
            WHERE id = (SELECT max(id) 
                        FROM {$this->schema}.master_device_setup 
                        WHERE inserttime::date <= current_date::date  
                        AND deviceid = a.did)) 
        AS device_name 
        FROM public.get_divice_details_record_for_list_for_company('{$this->schema}',{$this->sessdata['user_id']}) AS a 
        WHERE a.group_id = 2 AND a.active = 1")->getResult();

        if ($this->request->getMethod() == 'POST') {
            $start_date = date("Y-m-d H:i:s", strtotime(trim($this->request->getPost('stdt'))));
            $end_date = date("Y-m-d H:i:s", strtotime(trim($this->request->getPost('endt'))));
            $data['stdt'] = date("d-m-Y H:i", strtotime(trim($this->request->getPost('stdt'))));
            $data['endt'] = date("d-m-Y H:i", strtotime(trim($this->request->getPost('endt'))));
            $data['device_id'] = $device_id = trim($this->request->getPost('device_id'));

            $sql = " SELECT stes.traker_positionaldata.* 
            FROM stes.traker_positionaldata 
            LEFT JOIN public.master_device_details ON public.master_device_details.id = {$this->schema}.traker_positionaldata.deviceid
            WHERE deviceid = ? AND (currentdate+currenttime BETWEEN ? AND ?) AND poleno IS NOT NULL AND poleno != 'null' ORDER BY currentdate DESC, currenttime DESC ";

            /*echo $device_id . "==" . $start_date . "==" . $end_date . "==" . "<pre>";
            echo $sql;exit();*/

            $result = $this->db->query($sql, [$device_id, $start_date, $end_date])->getResult();

            $data['alldata'] = $result;
        }

        $data['middle'] = view('report/patrollinggraph', $data);
        return view('mainlayout', $data);
    }

    function getSubUsers($user_id) {
        $subUsers = [];
    
        $sql = "WITH RECURSIVE sub_users AS (
                    SELECT user_id FROM public.user_login WHERE parent_id = ?
                    UNION
                    SELECT ul.user_id FROM public.user_login ul
                    INNER JOIN sub_users su ON ul.parent_id = su.user_id
                )
                SELECT user_id FROM sub_users";
    
        $query = $this->db->query($sql, [$user_id])->getResult();
    
        foreach ($query as $row) {
            $subUsers[] = $row->user_id;
        }
    
        return $subUsers;
    }
}
