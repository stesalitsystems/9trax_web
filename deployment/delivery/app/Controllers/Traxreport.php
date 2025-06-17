<?php

namespace App\Controllers;

use App\Models\ReportModel;
use App\Models\MobilesModel;
use App\Models\CommonModel;
use App\Libraries\MakePDF;
use CodeIgniter\I18n\Time;

class Traxreport extends BaseController
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

    public function beatcompletionreport()
    {
        // Check if user is logged in
        if (!session()->has('login_sess_data')) {
            return redirect()->to('/');
        }

        $data = [];
        $data['sessdata'] = $this->sessdata;
        $data['page_title'] = "Summary Of Exception Report";
        $data['usersdd'] = $this->commonModel->get_users();
        $data['date_from'] = '';

        if ($this->request->getMethod() == 'POST') {
            // echo "<pre>";print_r($this->request->getPost());exit();
            $start_date = date("Y-m-d", strtotime(trim($this->request->getPost('stdt'))));
            $end_date = date("Y-m-d", strtotime(trim($this->request->getPost('endt'))));
            $data['typeofuser'] = $typeofuser = $this->request->getPost('typeofuser');
            $data['stdt'] = date("d-m-Y", strtotime(trim($this->request->getPost('stdt'))));
            $data['endt'] = date("d-m-Y", strtotime(trim($this->request->getPost('endt'))));
            $data['pwi_id'] = $pwi_id = trim($this->request->getPost('user'));
            $data['pwi_name'] = trim($this->request->getPost('pwi_name'));
            $data['schema'] = $schemaname = $this->schema;
            $data['sse_pwy'] = $sse_pwy = trim($this->request->getPost('pway_id'));
            $user_id = $this->sessdata['user_id'];
            $pwilist = [];

            if ($data['pwi_name'] == '') {
                $data['pwi_name'] = 'All';
            }

            if ($data['pwi_name'] == 'All') {
                if ($sse_pwy == 'All') {
                    // Date series generation
                    $sql_date_series = "SELECT day::date FROM generate_series(timestamp '{$start_date}', '{$end_date}', '1 day') as day";
                    $query_date_series = $this->db->query($sql_date_series);
                    foreach ($query_date_series->getResultArray() as $rs_date_series) {
                        $date = $rs_date_series['day'];

                        $date_from = date("Y-m-d", strtotime('-1 day', strtotime(trim($date)))) . ' 19:00:00';
                        $date_to = date('Y-m-d', strtotime($date)) . ' 06:59:59';

                        $result = $this->db->query("SELECT '{$date}' as date, lefttable.user_id, ul.organisation 
                                                    FROM public.get_right_panel_data_datetime('{$schemaname}', '{$date_from}', '{$date_to}', {$user_id}) as lefttable 
                                                    LEFT JOIN public.user_login as ul ON lefttable.user_id = ul.user_id
                                                    LEFT JOIN public.user_login as ull ON ul.parent_id = ull.user_id
                                                    WHERE lefttable.group_id = 2 AND lefttable.deviceid IS NOT NULL 
                                                    GROUP BY lefttable.user_id, ull.lastname, ul.lastname, ul.organisation
                                                    ORDER BY ull.lastname, ul.lastname ASC")->getResult();

                        $pwilist = array_merge($pwilist, $result);
                    }
                } else {
                    $sql_date_series = "SELECT day::date FROM generate_series(timestamp '{$start_date}', '{$end_date}', '1 day') as day";
                    $query_date_series = $this->db->query($sql_date_series);
                    foreach ($query_date_series->getResultArray() as $rs_date_series) {
                        $date = $rs_date_series['day'];
                        $result = $this->db->query("SELECT '{$date}' as date, lefttable.user_id, organisation 
                                                    FROM public.get_right_panel_data('{$schemaname}', '{$date}', {$user_id}) as lefttable 
                                                    LEFT JOIN public.user_login as ul ON lefttable.user_id = ul.user_id 
                                                    WHERE lefttable.group_id = 2 AND lefttable.deviceid IS NOT NULL AND lefttable.parent_id='{$sse_pwy}'  
                                                    GROUP BY lefttable.user_id, ul.lastname, organisation 
                                                    ORDER BY ul.lastname ASC")->getResult();
                        $pwilist = array_merge($pwilist, $result);
                    }
                }
            } else {
                $sql_date_series = "SELECT day::date FROM generate_series(timestamp '{$start_date}', '{$end_date}', '1 day') as day";
                $query_date_series = $this->db->query($sql_date_series);
                foreach ($query_date_series->getResultArray() as $rs_date_series) {
                    $date = $rs_date_series['day'];
                    $result = $this->db->query("SELECT '{$date}' as date, lefttable.user_id, organisation 
                        FROM public.get_right_panel_data('{$schemaname}', '{$date}', {$user_id}) as lefttable 
                        LEFT JOIN public.user_login as ul ON lefttable.user_id = ul.user_id 
                        WHERE lefttable.group_id = 2 AND lefttable.deviceid IS NOT NULL AND lefttable.user_id='{$pwi_id}'  
                        GROUP BY lefttable.user_id, ul.lastname, organisation 
                        ORDER BY ul.lastname ASC")->getResult();
                    $pwilist = array_merge($pwilist, $result);
                }
            }

            // Additional processing for devices and coverage
            for ($i = 0; $i < count($pwilist); $i++) {
                $sql_pwi = "SELECT b.organisation as pwi, a.id as section_id
                            FROM public.user_login as a
                            LEFT JOIN public.user_login as b ON (a.parent_id = b.id)
                            WHERE a.organisation='{$pwilist[$i]->organisation}'";
                $query_pwi = $this->db->query($sql_pwi)->getRow();
                $pwilist[$i]->pwi = $query_pwi->pwi;
                $pwilist[$i]->section_id = $query_pwi->section_id;
                $new_date = $pwilist[$i]->date;
                $user_id = $pwilist[$i]->user_id;

                /*$query = "SELECT sup_pid, sup_gid, serial_no, imei_no, did, mobile_no, warranty_date, linked, parent_id, user_id, issudate,
                                refunddate, active, issold, apply_scheam, group_id, role_id, email, address, pincode, state_name, country, username, firstname,
                                lastname, organisation, group_name, '' as list_item, '' as list_item_name 
                        FROM public.get_divice_details_record_for_list('{$this->schema}', {$user_id}) 
                        WHERE user_id={$user_id} AND active = 1 ORDER BY did ASC";*/
                $query = "select a.deviceid as did,b.device_name 
                from public.get_right_panel_data('{$this->schema}','{$date}',$user_id) as a
                left join {$this->schema}.master_device_setup as b on(a.deviceid = b.deviceid)
                where a.deviceid is not null order by a.deviceid";
                $devicelist = $this->db->query($query)->getResult();

                $pwilist[$i]->total_devices = count($devicelist);
                $beat_covered = 0; 
                $beat_not_covered = 0; 
                $active_device = 0; 
                $inactive_device = 0; 
                $not_alloted_devices = 0; 
                $duration = '00:00:00';

                if (count($devicelist) > 0) {
                    foreach ($devicelist as $devicelist_each) {
                        // Initialize device tracking
                        $deviceid = $devicelist_each->did;
                        $mdddevicename = $this->db->query("SELECT device_name FROM {$this->schema}.master_device_setup WHERE deviceid='{$deviceid}'")->getRow();
                        $device_name = 'N/A';
                        if(!empty($mdddevicename->device_name)) {
                            $device_name = $mdddevicename->device_name;
                        }
                        $mdddevicename_arr = explode("/", $device_name);

                        // Determine type of device
                        if (strpos(strtolower($mdddevicename_arr[0]), 'stock') !== false) {
                            $type = 'Others';
                        } elseif (strpos(strtolower($mdddevicename_arr[0]), 'keyman') !== false) {
                            $type = 'Keyman';
                        } elseif (strpos(strtolower($mdddevicename_arr[0]), 'patrolman') !== false) {
                            $type = 'Patrolman';
                        }

                        // Determine active/inactive devices
                        // Fetch the device name from the 'master_device_setup' table
                        /*$query = $this->db->query("SELECT device_name FROM {$this->schema}.master_device_setup WHERE deviceid = $deviceid");
                        $mdddevicename = $query->getRow();*/
                        $mdddevicename = $devicelist_each->device_name;
                        if($typeofuser != 'All') {
                            if(strtoupper($typeofuser) == strtoupper($type)) {
                                // Assuming $mdddevicename is provided from another source in CI4
                                $mdddevicename_arr = explode("/", $mdddevicename);
                                
                                if (strpos(strtolower($mdddevicename), 'stock') !== false) {
                                    $not_alloted_devices++;
                                } else if($mdddevicename == '') {
                                    $not_alloted_devices++;
                                } else {
                                    $date_from = date("Y-m-d", strtotime('-1 day',strtotime(trim($new_date)))).' 19:00:00';
									$date_to = date('Y-m-d', strtotime($new_date)).' 06:00:00';
									
									$date_from = $new_date.' 00:00:00';
									$date_to = $new_date.' 21:00:00';
                        
                                    // New approach with CodeIgniter 4's Query Builder
                                    $record1 = $this->db->query("SELECT a.*, mdd.serial_no, b.walk_org_distance 
                                    FROM public.get_histry_play_data_summary(?, ?, ?) AS a 
                                    LEFT JOIN {$schemaname}.master_device_details AS mdd ON mdd.superdevid = a.deviceid
                                    LEFT JOIN {$schemaname}.device_assigne_pole_data AS b ON b.deviceid = a.deviceid 
                                        AND b.startpole <> '0' AND b.stoppol <> '0' 
                                    WHERE b.walk_org_distance IS NOT NULL", [
                                        $deviceid, "{$new_date} 00:00:00", "{$new_date} 23:59:59"
                                    ])->getResult();
                        
                                    // If the query returns records
                                    if(count($record1) > 0) {
                                        $distance_cover = $record1[0]->distance_cover;
                                        $distance_cover = number_format($distance_cover / 1000, 3);
                        
                                        $active_device++;
                        
                                        $actual_distance = $record1[0]->walk_org_distance;
                        
                                        if ($distance_cover >= $actual_distance) {
                                            $beat_covered++;
                                        } else {
                                            $beat_not_covered++;
                                        }
                        
                                        $actual_duration = $record1[0]->duration;
                                        $duration = $this->sum_the_time($duration, $actual_duration);
                                    } else {
                                        // Query to check tracker positional data
                                        $data_count = $this->db->query("SELECT COUNT(*) AS counter 
                                        FROM {$this->schema}.traker_positionaldata WHERE currentdate = ? AND deviceid = ?", [
                                            $new_date, $deviceid
                                        ])->getRow();
                        
                                        if($data_count->counter > 0) {
                                            $active_device++;
                                            $beat_not_covered++;
                                        } else {
                                            $inactive_device++;
                                        }
                                    }
                                }
                            }
                        } else {
                            // Query for device name
                            $mdddevicename_arr = explode("/", $mdddevicename);
                            if (strpos(strtolower($mdddevicename), 'stock') !== false) {
                                $not_alloted_devices++;
                            } else if (empty($mdddevicename)) {
                                $not_alloted_devices++;
                            } else {
                                $date_from = date("Y-m-d", strtotime('-1 day', strtotime(trim($new_date)))) . ' 19:00:00';
                                $date_to = date('Y-m-d', strtotime($new_date)) . ' 06:00:00';

                                // Query to get historical play data and device details
                                $record1 = $this->db->query("
                                    SELECT a.*, mdd.serial_no, b.walk_org_distance 
                                    FROM public.get_histry_play_data_summary(?, ?, ?) AS a 
                                    LEFT JOIN {$schemaname}.master_device_details AS mdd ON mdd.superdevid = a.deviceid
                                    LEFT JOIN {$schemaname}.device_assigne_pole_data AS b ON b.deviceid = a.deviceid 
                                        AND b.startpole <> '0' AND b.stoppol <> '0' 
                                    WHERE b.walk_org_distance IS NOT NULL
                                ", [$deviceid, $new_date . ' 00:00:00', $new_date . ' 23:59:59'])->getResult();

                                $distance_cover = isset($record1[0]) ? $record1[0]->distance_cover : 0;
                                $distance_cover = number_format($distance_cover / 1000, 3);

                                if (count($record1) > 0) {
                                    $active_device++;
                                    $actual_distance = isset($record1[0]) ? $record1[0]->walk_org_distance : 0;

                                    if ($distance_cover >= $actual_distance && !empty($actual_distance) && $distance_cover > 0) {
                                        $beat_covered++;
                                    } else {
                                        $beat_not_covered++;
                                    }

                                    $actual_duration = isset($record1[0]) ? $record1[0]->duration : 0;
                                    $duration = $this->sum_the_time($duration, $actual_duration);
                                } else {
                                    // Query to check data count
                                    $data_count = $this->db->query("
                                        SELECT COUNT(*) AS counter 
                                        FROM {$this->schema}.traker_positionaldata 
                                        WHERE currentdate = ? AND deviceid = ?
                                    ", [$new_date, $deviceid])->getRow();

                                    $count = $data_count->counter;

                                    if ($count > 0) {
                                        $active_device++;
                                        $beat_not_covered++;
                                    } else {
                                        $inactive_device++;
                                    }
                                }
                            }
                        }
                    }
                }

                // Set coverage results
                $pwilist[$i]->active_device = $active_device;
                $pwilist[$i]->inactive_device = $inactive_device;
                $pwilist[$i]->beat_covered = $beat_covered;
                $pwilist[$i]->beat_not_covered = $beat_not_covered;
                $pwilist[$i]->not_alloted_devices = $not_alloted_devices;
                $pwilist[$i]->duration = $duration;
                $tot_dev = $active_device + $inactive_device;

                if ($beat_covered != '0') { 
                    $beat_coverage_percentage = (($beat_not_covered / $tot_dev) * 100);
                    $beat_coverage_percentage = number_format($beat_coverage_percentage, 2);
                } else {
                    $beat_coverage_percentage = '100';
                }
                $pwilist[$i]->beat_coverage_percentage = $beat_coverage_percentage;
            }

            $data['report_type'] = 7;
            $data['alldata'] = $pwilist;
        }

        $data['pway'] = $this->db->query("SELECT organisation, user_id FROM public.user_login WHERE active = 1 AND group_id = 8")->getResult();
        $data['middle'] = view('traxreport/beatcompletionreport', $data);
        return view('mainlayout', $data);
    }

    public function beatcompletionreportexcel()
    {
        // Start by handling the session
        if (!session()->has('login_sess_data')) {
            return redirect()->to('/');
        }

        // Initialize data array
        $data = [];
        $data['sessdata'] = $this->sessdata;
        $data['page_title'] = "Summary Of Exception Report";
        $data['usersdd'] = $this->commonModel->get_users();
        $data['date_from'] = '';

        // Handle form data submission
        $stdt = $this->request->getPost('stdt');
        $endt = $this->request->getPost('endt');
        $typeofuser = $this->request->getPost('typeofuser');

        // Check if POST data exists
        if ($this->request->getPost()) {
            // Get the input data
            $start_date = new Time(trim($this->request->getPost('stdt')), 'Asia/Kolkata');  // Adjust time zone as necessary
            $end_date = new Time(trim($this->request->getPost('endt')), 'Asia/Kolkata');
            $typeofuser = $this->request->getPost('typeofuser');
            $pwi_id = trim($this->request->getPost('user'));
            $pwi_name = trim($this->request->getPost('pwi_name'));
            $sse_pwy = trim($this->request->getPost('pway_id'));
            $user_id = $this->sessdata['user_id'];
            $group_id = $this->sessdata['group_id'];

            $devices = $dids = '';
            $pwilist = [];

            // Format dates
            $data['stdt'] = $start_date->format('d-m-Y');
            $data['endt'] = $end_date->format('d-m-Y');
            $data['typeofuser'] = $typeofuser;
            $data['pwi_id'] = $pwi_id;
            $data['pwi_name'] = $pwi_name ?: 'All';  // Default to 'All' if empty
            $data['schema'] = $schemaname = $this->schema;
            $data['sse_pwy'] = $sse_pwy;

            if ($data['pwi_name'] == 'All') {
                if ($sse_pwy == 'All') {
                    // Date series generation
                    $sql_date_series = "SELECT day::date FROM generate_series(timestamp '{$start_date}', '{$end_date}', '1 day') as day";
                    $query_date_series = $this->db->query($sql_date_series);
                    foreach ($query_date_series->getResultArray() as $rs_date_series) {
                        $date = $rs_date_series['day'];

                        $date_from = date("Y-m-d", strtotime('-1 day', strtotime(trim($date)))) . ' 19:00:00';
                        $date_to = date('Y-m-d', strtotime($date)) . ' 06:59:59';

                        $result = $this->db->query("SELECT '{$date}' as date, lefttable.user_id, ul.organisation 
                                                    FROM public.get_right_panel_data_datetime('{$schemaname}', '{$date_from}', '{$date_to}', {$user_id}) as lefttable 
                                                    LEFT JOIN public.user_login as ul ON lefttable.user_id = ul.user_id
                                                    LEFT JOIN public.user_login as ull ON ul.parent_id = ull.user_id
                                                    WHERE lefttable.group_id = 2 AND lefttable.deviceid IS NOT NULL 
                                                    GROUP BY lefttable.user_id, ull.lastname, ul.lastname, ul.organisation
                                                    ORDER BY ull.lastname, ul.lastname ASC")->getResult();

                        $pwilist = array_merge($pwilist, $result);
                    }
                } else {
                    $sql_date_series = "SELECT day::date FROM generate_series(timestamp '{$start_date}', '{$end_date}', '1 day') as day";
                    $query_date_series = $this->db->query($sql_date_series);
                    foreach ($query_date_series->getResultArray() as $rs_date_series) {
                        $date = $rs_date_series['day'];
                        $result = $this->db->query("SELECT '{$date}' as date, lefttable.user_id, organisation 
                                                    FROM public.get_right_panel_data('{$schemaname}', '{$date}', {$user_id}) as lefttable 
                                                    LEFT JOIN public.user_login as ul ON lefttable.user_id = ul.user_id 
                                                    WHERE lefttable.group_id = 2 AND lefttable.deviceid IS NOT NULL AND lefttable.parent_id='{$sse_pwy}'  
                                                    GROUP BY lefttable.user_id, ul.lastname, organisation 
                                                    ORDER BY ul.lastname ASC")->getResult();
                        $pwilist = array_merge($pwilist, $result);
                    }
                }
            } else {
                $sql_date_series = "SELECT day::date FROM generate_series(timestamp '{$start_date}', '{$end_date}', '1 day') as day";
                $query_date_series = $this->db->query($sql_date_series);
                foreach ($query_date_series->getResultArray() as $rs_date_series) {
                    $date = $rs_date_series['day'];
                    $result = $this->db->query("SELECT '{$date}' as date, lefttable.user_id, organisation 
                        FROM public.get_right_panel_data('{$schemaname}', '{$date}', {$user_id}) as lefttable 
                        LEFT JOIN public.user_login as ul ON lefttable.user_id = ul.user_id 
                        WHERE lefttable.group_id = 2 AND lefttable.deviceid IS NOT NULL AND lefttable.user_id='{$pwi_id}'  
                        GROUP BY lefttable.user_id, ul.lastname, organisation 
                        ORDER BY ul.lastname ASC")->getResult();
                    $pwilist = array_merge($pwilist, $result);
                }
            }

            // Additional processing for devices and coverage
            for ($i = 0; $i < count($pwilist); $i++) {
                $sql_pwi = "SELECT b.organisation as pwi, a.id as section_id
                            FROM public.user_login as a
                            LEFT JOIN public.user_login as b ON (a.parent_id = b.id)
                            WHERE a.organisation='{$pwilist[$i]->organisation}'";
                $query_pwi = $this->db->query($sql_pwi)->getRow();
                $pwilist[$i]->pwi = $query_pwi->pwi;
                $pwilist[$i]->section_id = $query_pwi->section_id;
                $new_date = $pwilist[$i]->date;
                $user_id = $pwilist[$i]->user_id;

                /*$query = "SELECT sup_pid, sup_gid, serial_no, imei_no, did, mobile_no, warranty_date, linked, parent_id, user_id, issudate,
                                refunddate, active, issold, apply_scheam, group_id, role_id, email, address, pincode, state_name, country, username, firstname,
                                lastname, organisation, group_name, '' as list_item, '' as list_item_name 
                        FROM public.get_divice_details_record_for_list('{$this->schema}', {$user_id}) 
                        WHERE user_id={$user_id} AND active = 1 ORDER BY did ASC";*/
                $query = "select a.deviceid as did,b.device_name 
                from public.get_right_panel_data('{$this->schema}','{$date}',$user_id) as a
                left join {$this->schema}.master_device_setup as b on(a.deviceid = b.deviceid)
                where a.deviceid is not null order by a.deviceid";
                $devicelist = $this->db->query($query)->getResult();

                $pwilist[$i]->total_devices = count($devicelist);
                $beat_covered = 0; 
                $beat_not_covered = 0; 
                $active_device = 0; 
                $inactive_device = 0; 
                $not_alloted_devices = 0; 
                $duration = '00:00:00';

                if (count($devicelist) > 0) {
                    foreach ($devicelist as $devicelist_each) {
                        // Initialize device tracking
                        $deviceid = $devicelist_each->did;
                        $mdddevicename = $this->db->query("SELECT device_name FROM {$this->schema}.master_device_setup WHERE deviceid='{$deviceid}'")->getRow();
                        $device_name = 'N/A';
                        if(!empty($mdddevicename->device_name)) {
                            $device_name = $mdddevicename->device_name;
                        }
                        $mdddevicename_arr = explode("/", $device_name);

                        // Determine type of device
                        if (strpos(strtolower($mdddevicename_arr[0]), 'stock') !== false) {
                            $type = 'Others';
                        } elseif (strpos(strtolower($mdddevicename_arr[0]), 'keyman') !== false) {
                            $type = 'Keyman';
                        } elseif (strpos(strtolower($mdddevicename_arr[0]), 'patrolman') !== false) {
                            $type = 'Patrolman';
                        }

                        // Determine active/inactive devices
                        // Fetch the device name from the 'master_device_setup' table
                        /*$query = $this->db->query("SELECT device_name FROM {$this->schema}.master_device_setup WHERE deviceid = $deviceid");
                        $mdddevicename = $query->getRow();*/
                        $mdddevicename = $devicelist_each->device_name;
                        if($typeofuser != 'All') {
                            if(strtoupper($typeofuser) == strtoupper($type)) {
                                // Assuming $mdddevicename is provided from another source in CI4
                                $mdddevicename_arr = explode("/", $mdddevicename);
                                
                                if (strpos(strtolower($mdddevicename), 'stock') !== false) {
                                    $not_alloted_devices++;
                                } else if($mdddevicename == '') {
                                    $not_alloted_devices++;
                                } else {
                                    $date_from = date("Y-m-d", strtotime('-1 day',strtotime(trim($new_date)))).' 19:00:00';
									$date_to = date('Y-m-d', strtotime($new_date)).' 06:00:00';
									
									$date_from = $new_date.' 00:00:00';
									$date_to = $new_date.' 21:00:00';
                        
                                    // New approach with CodeIgniter 4's Query Builder
                                    $record1 = $this->db->query("SELECT a.*, mdd.serial_no, b.expected_distance AS walk_org_distance
                                    FROM public.get_histry_play_data_summary(?, ?, ?) AS a
                                    LEFT JOIN {$schemaname}.master_device_details AS mdd ON mdd.superdevid = a.deviceid
                                    LEFT JOIN {$schemaname}.trip_details_with_pole AS b ON b.deviceid = a.deviceid
                                    WHERE b.result_date = ?", [
                                        $deviceid, "{$new_date} 00:00:00", "{$new_date} 23:59:59", $new_date
                                    ])->getResult();
                        
                                    // If the query returns records
                                    if(count($record1) > 0) {
                                        $distance_cover = $record1[0]->distance_cover;
                                        $distance_cover = number_format($distance_cover / 1000, 3);
                        
                                        $active_device++;
                        
                                        $actual_distance = $record1[0]->walk_org_distance;
                        
                                        if ($distance_cover >= $actual_distance) {
                                            $beat_covered++;
                                        } else {
                                            $beat_not_covered++;
                                        }
                        
                                        $actual_duration = $record1[0]->duration;
                                        $duration = $this->sum_the_time($duration, $actual_duration);
                                    } else {
                                        // Query to check tracker positional data
                                        $data_count = $this->db->query("SELECT COUNT(*) AS counter 
                                        FROM {$this->schema}.traker_positionaldata WHERE currentdate = ? AND deviceid = ?", [
                                            $new_date, $deviceid
                                        ])->getRow();
                        
                                        if($data_count->counter > 0) {
                                            $active_device++;
                                            $beat_not_covered++;
                                        } else {
                                            $inactive_device++;
                                        }
                                    }
                                }
                            }
                        } else {
                            // Query for device name
                            $mdddevicename_arr = explode("/", $mdddevicename);
                            if (strpos(strtolower($mdddevicename), 'stock') !== false) {
                                $not_alloted_devices++;
                            } else if (empty($mdddevicename)) {
                                $not_alloted_devices++;
                            } else {
                                $date_from = date("Y-m-d", strtotime('-1 day', strtotime(trim($new_date)))) . ' 19:00:00';
                                $date_to = date('Y-m-d', strtotime($new_date)) . ' 06:00:00';

                                // Query to get historical play data and device details
                                $record1 = $this->db->query("
                                    SELECT a.*, mdd.serial_no, b.walk_org_distance 
                                    FROM public.get_histry_play_data_summary(?, ?, ?) AS a 
                                    LEFT JOIN {$schemaname}.master_device_details AS mdd ON mdd.superdevid = a.deviceid
                                    LEFT JOIN {$schemaname}.device_assigne_pole_data AS b ON b.deviceid = a.deviceid 
                                        AND b.startpole <> '0' AND b.stoppol <> '0' 
                                    WHERE b.walk_org_distance IS NOT NULL
                                ", [$deviceid, $new_date . ' 00:00:00', $new_date . ' 23:59:59'])->getResult();

                                $distance_cover = isset($record1[0]) ? $record1[0]->distance_cover : 0;
                                $distance_cover = number_format($distance_cover / 1000, 3);

                                if (count($record1) > 0) {
                                    $active_device++;
                                    $actual_distance = isset($record1[0]) ? $record1[0]->walk_org_distance : 0;

                                    if ($distance_cover >= $actual_distance && !empty($actual_distance) && $distance_cover > 0) {
                                        $beat_covered++;
                                    } else {
                                        $beat_not_covered++;
                                    }

                                    $actual_duration = isset($record1[0]) ? $record1[0]->duration : 0;
                                    $duration = $this->sum_the_time($duration, $actual_duration);
                                } else {
                                    // Query to check data count
                                    $data_count = $this->db->query("
                                        SELECT COUNT(*) AS counter 
                                        FROM {$this->schema}.traker_positionaldata 
                                        WHERE currentdate = ? AND deviceid = ?
                                    ", [$new_date, $deviceid])->getRow();

                                    $count = $data_count->counter;

                                    if ($count > 0) {
                                        $active_device++;
                                        $beat_not_covered++;
                                    } else {
                                        $inactive_device++;
                                    }
                                }
                            }
                        }
                    }
                }

                // Set coverage results
                $pwilist[$i]->active_device = $active_device;
                $pwilist[$i]->inactive_device = $inactive_device;
                $pwilist[$i]->beat_covered = $beat_covered;
                $pwilist[$i]->beat_not_covered = $beat_not_covered;
                $pwilist[$i]->not_alloted_devices = $not_alloted_devices;
                $pwilist[$i]->duration = $duration;
                $tot_dev = $active_device + $inactive_device;

                if ($beat_covered != '0') { 
                    $beat_coverage_percentage = (($beat_not_covered / $tot_dev) * 100);
                    $beat_coverage_percentage = number_format($beat_coverage_percentage, 2);
                } else {
                    $beat_coverage_percentage = '100';
                }
                $pwilist[$i]->beat_coverage_percentage = $beat_coverage_percentage;
            }

            // Set data to pass to the view
            $data['report_type'] = $report_type = 7;
            $data['alldata'] = $alldata = $pwilist;
        }

        // echo "<pre>";print_r($pwilist);exit();

        $dat[0]['A'] = $typeofuser . " Work Status Count Report Date " . date("d-m-Y", strtotime($stdt)) . " To " . date("d-m-Y", strtotime($endt));

        // Initialize header row
        $dat[1]['A'] = "Date";
        $dat[1]['B'] = "PWI";
        $dat[1]['C'] = "Section Name";
        $dat[1]['D'] = "Off Device";
        $dat[1]['E'] = "Beat Not Covered";
        $dat[1]['F'] = "Beat Covered Successfully";
        $dat[1]['G'] = "Device Not Alloted";
        $dat[1]['H'] = "Total Device";
        // $dat[1]['I'] = "Effective Hours Of Working";
        // $dat[1]['J'] = "Status";
        // $dat[1]['K'] = "Remarks";

        // Initialize counters
        $Key = 1;
        $inactive_device = 0;
        $beat_not_covered = 0;
        $beat_covered = 0;
        $totaldevice = 0;
        $total_not_alloted_devices = 0;
        $total_duration = '00:00:00';

        

        foreach ($pwilist as $irow) {
            $inactive_device += $irow->inactive_device;
            $beat_not_covered += $irow->beat_not_covered;
            $beat_covered += $irow->beat_covered;

            if ($typeofuser != 'All') {
                $total_devices = $irow->inactive_device + $irow->active_device;
            } else {
                $total_devices = $irow->total_devices;
            }
            
            $totaldevice += $total_devices;
            $total_not_alloted_devices += $irow->not_alloted_devices;
            $total_duration = $this->sum_the_time($total_duration, $irow->duration);

            // Fill data for each row
            $dat[$Key + 1]['A'] = $irow->date;
            $dat[$Key + 1]['B'] = $irow->pwi;
            $dat[$Key + 1]['C'] = $irow->organisation;
            $dat[$Key + 1]['D'] = $irow->inactive_device;
            $dat[$Key + 1]['E'] = $irow->beat_not_covered;
            $dat[$Key + 1]['F'] = $irow->beat_covered;
            $dat[$Key + 1]['G'] = $irow->not_alloted_devices;
            $dat[$Key + 1]['H'] = $total_devices;
            // $dat[$Key + 1]['I'] = $irow->duration;
            // $dat[$Key + 1]['J'] = $irow->beat_coverage_percentage. '% Beat Not Covered';
            // $dat[$Key + 1]['K'] = '';  // Empty for now, adjust if needed

            $Key++;
        }

        // Calculate the summary row
        $beat_coverage_percentage = (($beat_not_covered / $totaldevice) * 100);
        $beat_coverage_percentage = number_format($beat_coverage_percentage, 2);

        // Summary row
        $dat[$Key + 1]['A'] = '';
        $dat[$Key + 1]['B'] = '';
        $dat[$Key + 1]['C'] = 'Total';
        $dat[$Key + 1]['D'] = $inactive_device;
        $dat[$Key + 1]['E'] = $beat_not_covered;
        $dat[$Key + 1]['F'] = $beat_covered;
        $dat[$Key + 1]['G'] = $total_not_alloted_devices;
        $dat[$Key + 1]['H'] = $totaldevice;
        // $dat[$Key + 1]['I'] = $total_duration;
        // $dat[$Key + 1]['J'] = '';
        // $dat[$Key + 1]['K'] = '';

        // Create the Excel file
        $filename = $typeofuser . ' Work_Status_Count_Report_' . date("d-m-Y", strtotime($stdt)) . '_To_' . date("d-m-Y", strtotime($endt)) . '.xlsx';
        exceldownload($dat, $filename);
    }

    public function handleDeviceCoverage($devicelist_each, $new_date, $beat_covered, $beat_not_covered, $active_device, $inactive_device, $duration)
    {
        $deviceid = $devicelist_each->did;
        $date_from = date("Y-m-d", strtotime('-1 day', strtotime(trim($new_date)))) . ' 19:00:00';
        $date_to = date('Y-m-d', strtotime($new_date)) . ' 06:00:00';
        $record1 = $this->db->query("SELECT a.*, mdd.serial_no 
                                    FROM public.get_histry_play_data_summary('{$deviceid}', '{$date_from}'::timestamp without time zone, '{$date_to}'::timestamp without time zone) as a 
                                    LEFT JOIN {$this->schema}.master_device_details as mdd ON (mdd.superdevid = a.deviceid)")->getResult();

        $distance_cover = $record1[0]->distance_cover ?? 0;

        if (count($record1) > 0) {
            $active_device++;
            $timedetails = $this->db->query("SELECT walk_org_distance 
                                            FROM {$this->schema}.device_assigne_pole_data 
                                            WHERE deviceid='{$deviceid}' AND startpole <> '0' AND stoppol <> '0' AND walk_org_distance IS NOT NULL")->getRow();
            $actual_distance = $timedetails->walk_org_distance ?? 0;

            if (($distance_cover >= $actual_distance) && ($actual_distance != '') && ($distance_cover > 0)) {
                $beat_covered++;
            } else {
                $beat_not_covered++;
            }

            $actual_duration = $record1[0]->duration ?? '00:00:00';
            $duration = $this->sum_the_time($duration, $actual_duration);
        } else {
            $inactive_device++;
        }
    }

    public function getUser()
    {
        $parent_id = $this->request->getPost('user_id');
        $pwi_id = $this->request->getPost('pwi_id');

        if (!empty($parent_id) && $parent_id != 'All') {
            echo $this->commonModel->getMasterbyparamjoin("user_login", [
                'parent_id' => $parent_id,
                'active' => 1
            ], 'organisation', 'user_id', null, $pwi_id);

            // return $this->response->setJSON($result);
        }

        // Optionally handle the 'All' case or return a default response
        // return $this->response->setJSON(['message' => 'All']);
    }

    function sum_the_time($time1, $time2)
    {
        $times = array($time1, $time2);
        $seconds = 0;

        foreach ($times as $time) {
            list($hour, $minute, $second) = explode(':', $time);
            $seconds += $hour * 3600;
            $seconds += $minute * 60;
            $seconds += $second;
        }

        $hours = floor($seconds / 3600);
        $seconds -= $hours * 3600;
        $minutes = floor($seconds / 60);
        $seconds -= $minutes * 60;

        // Format the output
        $hours = str_pad($hours, 2, '0', STR_PAD_LEFT);
        $minutes = str_pad($minutes, 2, '0', STR_PAD_LEFT);
        $seconds = str_pad($seconds, 2, '0', STR_PAD_LEFT);

        return "{$hours}:{$minutes}:{$seconds}";
    }

    public function reportList()
    {
        if (!session()->get('login_sess_data')) {
            return redirect()->to('/');
        }

        $data = [];
        $data['sessdata'] = $this->sessdata;
        $data['page_title'] = "Trip Summary Report With Exception";
        $data['usersdd'] = $this->commonModel->get_users();
        $data['reporttype'] = $this->db->query("SELECT * FROM public.report_type WHERE active = 1 AND id NOT IN (1,2,3,4,5,6,8,11,10)")->getResult();
        $data['pway'] = $this->db->query("SELECT organisation, user_id FROM public.user_login WHERE active = 1 AND group_id = 8")->getResult();
        $data['date_from'] = '';
        $data['date_to'] = '';
        $data['report_type'] = 7;

        if ($this->sessdata['group_id'] == 3) { // distributor
            $data['devicedropdown'] = $this->db->query("SELECT a.*, (SELECT device_name FROM {$this->schema}.master_device_setup WHERE id=(SELECT max(id) FROM {$this->schema}.master_device_setup WHERE inserttime::date <= current_date::date AND deviceid = a.did)) AS device_name FROM public.get_divice_details_record_for_list_for_company('{$this->schema}', {$this->sessdata['user_id']}) AS a WHERE a.group_id = 2 AND a.active = 1")->getResult();
        } else { // others
            $data['devicedropdown'] = $this->db->query("SELECT a.*, (SELECT device_name FROM {$this->schema}.master_device_setup WHERE id=(SELECT max(id) FROM {$this->schema}.master_device_setup WHERE inserttime::date <= current_date::date AND deviceid = a.did)) AS device_name FROM public.get_divice_details_record_for_list_for_company('{$this->schema}', {$this->sessdata['user_id']}) AS a WHERE a.group_id = 2 AND a.active = 1")->getResult();
        }

        if ($this->request->getMethod() == 'POST') {
            $user_id = $this->sessdata['user_id'];
            $group_id = $this->sessdata['group_id'];
            
            $devices = $alerts = $geofences = $routes = $dids = null;
            $data['duty_status'] = $duty_status = trim($this->request->getPost('duty_status'));
            $report_type = trim($this->request->getPost('report_type'));
            
            $date_from = date("Y-m-d H:i:s", strtotime(trim($this->request->getPost('date_from'))));
            $date_to = date("Y-m-d H:i:s", strtotime(trim($this->request->getPost('date_to'))));

            $time_from = "09:00:00";
			$time_to = "08:59:00";			
    
            $data['date_from'] = date("d-m-Y H:i", strtotime(trim($this->request->getPost('date_from'))));
            $data['date_to'] = date("d-m-Y H:i", strtotime(trim($this->request->getPost('date_to'))));
    
            $data['pway_id'] = $pway_id = trim($this->request->getPost('pway_id'));
            $data['pwi_id'] = $pwi_id = trim($this->request->getPost('user'));
            $data['pwi_name'] = trim($this->request->getPost('pwi_name'));
            $data['schema'] = $this->schema;
    
            $sse_pwy = trim($this->request->getPost('pway_id'));
            // Initialize devices variable
            $devices = "{";

            // Check if the PWI name is 'All'
            if ($data['pwi_name'] == 'All') {
                if ($sse_pwy == 'All') {
                    // Query for 'All' section
                    $query = $this->db->query("
                        SELECT 
                            sup_pid, sup_gid, serial_no, imei_no, did, mobile_no, 
                            warranty_date, linked, parent_id, user_id, issudate, 
                            refunddate, active, issold, apply_scheam, group_id, role_id, 
                            email, address, pincode, state_name, country, username, 
                            firstname, lastname, organisation, group_name, 
                            '' AS list_item, '' AS list_item_name 
                        FROM public.get_divice_details_record_for_list('{$this->schema}', $user_id) 
                        WHERE sup_gid = $group_id AND user_id = $user_id AND active = 1 
                        ORDER BY did ASC
                    ");
        
                    // Fetch result
                    $devicelist = $query->getResult();
        
                } else {
                    // Query for specific sections
                    $sectionListQuery = $this->db->query("
                        SELECT user_id 
                        FROM public.user_login 
                        WHERE parent_id = $sse_pwy AND active = 1
                    ");
        
                    $sectionList = $sectionListQuery->getResult();
        
                    // Loop over each user_id
                    foreach ($sectionList as $section) {
                        $sectionId = $section->user_id;
        
                        // Query for each section's devices
                        $deviceQuery = $this->db->query("
                            SELECT 
                                sup_pid, sup_gid, serial_no, imei_no, did, mobile_no, 
                                warranty_date, linked, parent_id, user_id, issudate, 
                                refunddate, active, issold, apply_scheam, group_id, role_id, 
                                email, address, pincode, state_name, country, username, 
                                firstname, lastname, organisation, group_name, 
                                '' AS list_item, '' AS list_item_name 
                            FROM public.get_divice_details_record_for_list({$this->schema}, $sectionId) 
                            WHERE user_id = $sectionId AND active = 1 
                            ORDER BY issudate ASC
                        ");
        
                        // Merge the results into the device list
                        $devicelist = array_merge($deviceList, $deviceQuery->getResult());
                    }
                }
            } else {
                // Prepare the query for specific devices
                $query = "SELECT sup_pid, sup_gid, serial_no, imei_no, did, mobile_no, warranty_date, linked, parent_id, user_id, 
                                issudate, refunddate, active, issold, apply_scheam, group_id, role_id, email, address, pincode, 
                                state_name, country, username, firstname, lastname, organisation, group_name, '' AS list_item, 
                                '' AS list_item_name 
                        FROM public.get_divice_details_record_for_list('{$this->schema}', $pwi_id) 
                        WHERE user_id = $pwi_id AND active = 1 
                        ORDER BY issudate ASC";
                
                // Execute the query
                $devicelist = $this->db->query($query)->getResult();
            }

            // Initialize dids variable
            $dids = "";

            // Check if there are devices in the list
            if (count($devicelist) > 0) {
                foreach ($devicelist as $devicelist_each) {
                    // Add device ID to devices and dids
                    if ($devices == "{") {
                        $devices .= $devicelist_each->did;
                        $dids .= $devicelist_each->did;
                    } else {
                        $devices .= "," . $devicelist_each->did;
                        $dids .= "," . $devicelist_each->did;
                    }
                }
            }

            // Close the devices variable
            $devices .= "}";
            
            /*if ($report_type == 7) {
                $data['device_id'] = $device_id = trim($this->request->getPost('device_id'));
            
                // Check if device_id is not empty
                if (!empty($device_id)) {
                    $dids = $device_id;
                }
            
                // Prepare the SQL query
                $query = "
                    SELECT mddd, mdddevicename, mddserialno, divicename, result_date, deviceid, acting_trip, 
                           start_time, endtime, duration, distance_cover, sos_no, alert_no, call_no, 
                           totalnoofstop, totalstoptime, polno, rd, dv, devicename, acting_triped, 
                           starttime, end_time, duration1, distancecover, sosno, alertno, callno, 
                           totalnoof_stop, totalsto_ptime, pol_no, polename1, polnoend, 
                           polenameend1, polename, polenameend, genid 
                    FROM (
                        SELECT DISTINCT deviceid AS mddd, 
                            (SELECT device_name FROM {$this->schema}.master_device_setup 
                             WHERE deviceid = ax.deviceid 
                             AND id = (SELECT MAX(id) FROM {$this->schema}.master_device_setup 
                                       WHERE inserttime::date <= '$date_to'::date 
                                       AND deviceid = ax.deviceid)) AS mdddevicename,
                            (SELECT COALESCE(serial_no, 'Absent') FROM {$this->schema}.master_device_details 
                             WHERE superdevid = ax.deviceid OR id = ax.deviceid) AS mddserialno 
                        FROM {$this->schema}.master_device_assign AS ax 
                        WHERE deviceid IN ($dids) AND group_id = 2
                    ) masterdevice 
                    LEFT OUTER JOIN (
                        SELECT * FROM public.trip_spesified_device 
                        WHERE (genid, deviceid, result_date, acting_trip) IN (
                            SELECT MAX(genid), deviceid, result_date, acting_trip 
                            FROM public.trip_spesified_device 
                            WHERE deviceid IN ($dids) 
                            AND (result_date || ' ' || start_time >= '{$date_from} {$time_from}' 
                                 AND result_date || ' ' || end_time <= '{$date_to} {$time_to}') 
                            GROUP BY deviceid, result_date, acting_trip
                        )
                    ) resultdivice ON masterdevice.mddd = resultdivice.deviceid
                    ORDER BY result_date, start_time ASC
                ";
            
                // Execute the query and get results
                $alldata = $this->db->query($query)->getResult();
            
                $alldataall = [];
                $ddddd = $alldata[0]->mddd ?? null; // Safeguard against empty result
            
                for ($x = 0; $x < count($alldata); $x++) {
                    if (isset($alldata[$x + 1]) && 
                        ($ddddd == $alldata[$x + 1]->mddd) && 
                        ($alldata[$x]->start_time > '17:59:59') && 
                        ($alldata[$x + 1]->end_time < '07:30:00')) {
                        
                        // Calculate new duration
                        $newDurationQuery = "SELECT age('{$alldata[$x]->result_date} {$alldata[$x]->start_time}', 
                                                         '{$alldata[$x + 1]->result_date} {$alldata[$x + 1]->endtime}') AS duration";
                        $newduration = $this->db->query($newDurationQuery)->getResult();
                        $alldata[$x + 1]->duration = ltrim($newduration[0]->duration ?? '', '-');
                        $alldata[$x + 1]->start_time = $alldata[$x]->start_time;
                        $alldata[$x + 1]->result_date = $alldata[$x]->result_date;
            
                        // Aggregate values
                        $alldata[$x + 1]->distance_cover += $alldata[$x]->distance_cover;
                        $alldata[$x + 1]->sos_no += $alldata[$x]->sos_no;
                        $alldata[$x + 1]->alert_no += $alldata[$x]->alert_no;
                        $alldata[$x + 1]->call_no += $alldata[$x]->call_no;
                        $alldata[$x + 1]->totalnoofstop += $alldata[$x]->totalnoofstop;
                        $alldata[$x + 1]->totalstoptime += $alldata[$x]->totalstoptime;
                    } else {
                        $alldataall[] = $alldata[$x]; // Push the current item to the result array
                        $ddddd = $alldata[$x + 1]->mddd ?? null; // Update for the next iteration
                    }
                }
            
                // Store the aggregated data in the data array
                $data['alldata'] = $alldataall;
            }*/
            if ($report_type == 7) {
                $data['device_id'] = $device_id = trim($this->request->getPost('device_id'));
				if(!empty($device_id)){
					$dids = $device_id;
				}
				if($dids == '')
				{
					$dids = 0;
				}
    
                // Build SQL query
                $alldatanew = $this->db->query("
                    select mddd,mdddevicename,mddserialno,divicename, result_date, deviceid, acting_trip, 
				start_time, endtime,duration, distance_cover, sos_no, alert_no, call_no, totalnoofstop, 
				totalstoptime, polno, rd, dv, devicename, acting_triped, starttime,   end_time, duration1, 
				distancecover, sosno, alertno, callno, totalnoof_stop,  totalsto_ptime, pol_no, polename1, 
				polnoend, polenameend1, polename, polenameend, genid from (( SELECT distinct deviceid as mddd,
				(SELECT device_name FROM ".$this->schema.".master_device_setup  where deviceid=ax.deviceid 
				and id=(SELECT  max(id) FROM ".$this->schema.".master_device_setup 
				where inserttime::date<='$date_to'::date  and deviceid=ax.deviceid )) as mdddevicename,
				(SELECT  coalesce(serial_no,'Absent')  FROM ".$this->schema.".master_device_details  
				where superdevid=ax.deviceid or id=ax.deviceid) as mddserialno   
				FROM ".$this->schema.".master_device_assign as ax where deviceid in ($dids) and group_id=2 )  masterdevice 
				left outer join (select * from public.trip_spesified_device where (genid,deviceid,result_date,acting_trip) 
				in(select max(genid),deviceid,result_date,acting_trip from public.trip_spesified_device 
				where deviceid in ($dids) and (result_date||' '||start_time >= '".$date_from." ".$time_from."' 
				and result_date||' '||end_time <= '".$date_to." ".$time_to."') group by deviceid,result_date,acting_trip) 
				order by devicename,result_date,acting_trip)resultdivice on masterdevice.mddd=resultdivice.deviceid)resultset 
				order by mddd,result_date,start_time asc
                ")->getResult();
    
                $alldata = [];
                foreach ($alldatanew as $data1) {
                    // echo $data['mddserialno'];
                    $serialno = $data1->mddserialno;
                    $device_assign_details = $this->db->query("
                        SELECT a.parent_user_id, a.current_user_id, b.organisation AS pwy, c.organisation AS section
                        FROM public.device_asign_details AS a
                        LEFT JOIN public.user_login AS b ON a.parent_user_id = b.user_id
                        LEFT JOIN public.user_login AS c ON a.current_user_id = c.user_id
                        WHERE a.serial_no = '$serialno'
                    ")->getResult();
    
                    // Attach additional data
                    $data1->pwy = $device_assign_details[0]->pwy ?? null;
                    $data1->section = $device_assign_details[0]->section ?? null;
    
                    // Parsing devicename
                    if($data1->mdddevicename) {
                        $devicenameArr = explode(':', $data1->mdddevicename);
                        if(count($devicenameArr) > 0 && !empty($devicenameArr[1])) {
                            $poledetailsnew = trim($devicenameArr[1]);
                        } else {
                            $poledetailsnew = $data1->mdddevicename;
                        }
                        $poledetailsnewArr = explode('(', $poledetailsnew);
                        $polenamenew = trim($poledetailsnewArr[1]);
                        $polenamenew = str_replace(')', '', $polenamenew);
                        $polenamenewArr = explode('-', $polenamenew);
                        $data1->startpole = $polenamenewArr[0];
                        $data1->stoppol = $polenamenewArr[1] ?? '';
                        $data1->bit = $data1->startpole . '-' . $data1->stoppol;
                    } else {
                        $data1->startpole = 'NA';
                        $data1->stoppol = 'NA';
                        $data1->bit = 'NA';
                    }
                    
    
                    if ($data1->divicename != '') {
                        $alldata[] = $data1;
                    }
                }

                // echo "<pre>";print_r($alldata);exit();
                // Handling trip merging
                $ddddd = !empty($alldata) ? $alldata[0]->mddd : 0;
                $alldataall = [];
                for ($x = 0; $x < count($alldata); $x++) {
                    // Check if next item exists and apply conditions
                    if (isset($alldata[$x+1]) && 
                        ($ddddd == $alldata[$x+1]->mddd) && 
                        ($alldata[$x]->start_time > '17:59:59') && 
                        ($alldata[$x+1]->end_time < '07:30:00')) {
                        
                        // Calculate the duration between two records
                        $newduration = $this->db->query("
                            SELECT AGE('{$alldata[$x]->result_date} {$alldata[$x]->start_time}', 
                                    '{$alldata[$x+1]->result_date} {$alldata[$x+1]->endtime}') AS duration
                        ")->getResult();

                        // Assign calculated duration and merge fields
                        $alldata[$x+1]->duration = ltrim($newduration[0]->duration, '-');
                        $alldata[$x+1]->start_time = $alldata[$x]->start_time;
                        $alldata[$x+1]->result_date = $alldata[$x]->result_date;
                        $alldata[$x+1]->distance_cover += $alldata[$x]->distance_cover;
                        $alldata[$x+1]->sos_no += $alldata[$x]->sos_no;
                        $alldata[$x+1]->alert_no += $alldata[$x]->alert_no;
                        $alldata[$x+1]->call_no += $alldata[$x]->call_no;
                        $alldata[$x+1]->totalnoofstop += $alldata[$x]->totalnoofstop;
                        $alldata[$x+1]->totalstoptime += $alldata[$x]->totalstoptime;
                    } else {
                        // Add the current item to the final list
                        $alldataall[] = $alldata[$x];
                        if (isset($alldata[$x+1])) {
                            $ddddd = $alldata[$x+1]->mddd;
                        }
                    }
                }

                // Handling trip numbers
                $deviceid = '';
                $tripNo = 1;  // Initialize trip number
                foreach ($alldataall as $y => $data2) {
                    // Check if the current device matches the previous one
                    if ($data2->mddd == $deviceid) {
                        $tripNo++;
                    } else {
                        $deviceid = $data2->mddd;  // Update the device ID
                        $tripNo = 1;  // Reset trip number for new device
                    }

                    // Assign the trip number to the current data
                    $alldataall[$y]->acting_trip = $tripNo;
                }

                $alldataall = (array) $alldataall;

                $data['alldata'] = $alldataall;  // Assign the final data to the response

            }
            
            if ($report_type == 8) {
                $data['alldata'] = [];
                
                // Prepare the SQL query
                $query = $this->db->query("
                    SELECT 
                        typeofuser, 
                        deviceid, 
                        devicename, 
                        sessiondate, 
                        sesectionstartttime, 
                        sesectionendttime, 
                        starttime, 
                        endtime, 
                        duration, 
                        orginallength, 
                        withinpolelength AS totalwitpole,
                        polesequenct, 
                        (SELECT device_name 
                         FROM ".$this->schema.".master_device_setup 
                         WHERE deviceid = mdd.superdevid 
                         AND id = (SELECT MAX(id) 
                                   FROM ".$this->schema.".master_device_setup 
                                   WHERE inserttime::date <= '$date_from'::date 
                                   AND deviceid = mdd.superdevid)
                        ) AS mdddevicename, 
                        mdd.serial_no 
                    FROM public.analysish_patrol_man_and_key_man_work_patrol('$date_from'::date, '".$this->schema."'::character varying) AS ax 
                    RIGHT JOIN ".$this->schema.".master_device_details AS mdd 
                    ON (ax.deviceid = mdd.superdevid) 
                    WHERE mdd.superdevid IN ($dids)
                ");
                
                $result = $query->getResult();
            
                $deviceserial = '';
                $i = 0;
                foreach ($result as $result_each) {
                    if ($deviceserial != $result_each->serial_no) {
                        if ($deviceserial == '') {
                            $i = 0;
                        } else {
                            $i++;
                        }
                        $deviceserial = $result_each->serial_no;
            
                        $data['alldata'][$i] = new \stdClass();
                        $data['alldata'][$i]->typeofuser = $result_each->deviceid ? $result_each->typeofuser : 'NA';
                        $data['alldata'][$i]->deviceid = $result_each->deviceid ?: 'NA';
                        $data['alldata'][$i]->devicealiasname = $result_each->mdddevicename;
                        $data['alldata'][$i]->devicename = $result_each->serial_no;
                        $data['alldata'][$i]->sessiondate = $result_each->sessiondate;
                        $data['alldata'][$i]->starttime = $result_each->deviceid ? $result_each->starttime : '00:00:00';
                        $data['alldata'][$i]->endtime = $result_each->deviceid ? $result_each->endtime : '00:00:00';
                        $data['alldata'][$i]->duration = $result_each->deviceid ? $result_each->duration : 'NA';
                        $data['alldata'][$i]->distance = $result_each->totalwitpole;
                        $data['alldata'][$i]->orginallength = $result_each->orginallength;
                    } else {
                        $data['alldata'][$i]->distance += $result_each->totalwitpole;
                    }
                }
            }
            if ($report_type == 9) {
                if (empty($dids)) {
                    $dids = 0;
                }
            
                // First query to fetch alldata
                $query = $this->db->query("
                    SELECT 
                        mddd,
                        mdddevicename,
                        mddserialno,
                        divicename, 
                        result_date, 
                        deviceid, 
                        acting_trip, 
                        start_time, 
                        endtime,
                        duration, 
                        distance_cover, 
                        sos_no, 
                        alert_no, 
                        call_no, 
                        totalnoofstop, 
                        totalstoptime, 
                        polno, 
                        rd, 
                        dv, 
                        devicename, 
                        acting_triped, 
                        starttime,   
                        end_time, 
                        duration1, 
                        distancecover, 
                        sosno, 
                        alertno, 
                        callno, 
                        totalnoof_stop,  
                        totalsto_ptime, 
                        pol_no, 
                        polename1, 
                        polnoend, 
                        polenameend1, 
                        polename, 
                        polenameend, 
                        genid
                    FROM (
                        (SELECT DISTINCT 
                            deviceid AS mddd,
                            (SELECT device_name 
                             FROM ".$this->schema.".master_device_setup  
                             WHERE deviceid = ax.deviceid 
                             AND id = (SELECT MAX(id) 
                                       FROM ".$this->schema.".master_device_setup 
                                       WHERE inserttime::date <= '$date_to'::date  
                                       AND deviceid = ax.deviceid)
                            ) AS mdddevicename,
                            (SELECT COALESCE(serial_no, 'Absent')  
                             FROM ".$this->schema.".master_device_details  
                             WHERE superdevid = ax.deviceid OR id = ax.deviceid) AS mddserialno   
                        FROM ".$this->schema.".master_device_assign AS ax 
                        WHERE deviceid IN ($dids) AND group_id = 2) masterdevice 
                        LEFT OUTER JOIN (
                            SELECT * 
                            FROM public.trip_spesified_device 
                            WHERE (genid, deviceid, result_date, acting_trip) IN (
                                SELECT MAX(genid), deviceid, result_date, acting_trip 
                                FROM public.trip_spesified_device 
                                WHERE deviceid IN ($dids) 
                                AND (result_date || ' ' || start_time >= '".$date_from." ".$time_from."' 
                                AND result_date || ' ' || end_time <= '".$date_to." ".$time_to."') 
                                GROUP BY deviceid, result_date, acting_trip
                            ) 
                            ORDER BY devicename, result_date, acting_trip
                        ) resultdivice 
                        ON masterdevice.mddd = resultdivice.deviceid
                    ) resultset 
                    ORDER BY result_date, mddd ASC
                ");
                
                $data['alldata'] = $alldata = $query->getResult();
            
                for ($i = 0; $i < count($alldata); $i++) {
                    $serialno = $alldata[$i]->mddserialno;
            
                    // Fetch device assignment details
                    $device_assign_query = $this->db->query("
                        SELECT 
                            a.parent_user_id,
                            a.current_user_id,
                            b.organisation AS pwy,
                            c.organisation AS section
                        FROM public.device_asign_details AS a
                        LEFT JOIN public.user_login AS b ON (a.parent_user_id = b.user_id)
                        LEFT JOIN public.user_login AS c ON (a.current_user_id = c.user_id)
                        WHERE a.serial_no = '".$serialno."'
                    ");
                    $device_assign_details = $device_assign_query->getRow();
            
                    if ($device_assign_details) {
                        $alldata[$i]->pwy = $device_assign_details->pwy;
                        $alldata[$i]->section = $device_assign_details->section;
                    } else {
                        $alldata[$i]->pwy = null;
                        $alldata[$i]->section = null;
                    }
            
                    // Process device name
                    $devicename = $alldata[$i]->mdddevicename;
                    $devicenameArr = explode(':', $devicename);
                    if(count($devicenameArr) > 1) {
                        $poledetailsnew = trim($devicenameArr[1]);
                        $poledetailsnewArr = explode('(', $poledetailsnew);
                        $polenamenew = trim($poledetailsnewArr[1]);
                        $polenamenew = str_replace(')', '', $polenamenew);
                        $polenamenewArr = explode('-', $polenamenew);
                
                        $alldata[$i]->startpole = $polenamenewArr[0];
                        $alldata[$i]->stoppol = $polenamenewArr[1];
                        $alldata[$i]->bit = $alldata[$i]->startpole . '-' . $alldata[$i]->stoppol;
                    } else {
                        $alldata[$i]->startpole = '';
                        $alldata[$i]->stoppol = '';
                        $alldata[$i]->bit = '';
                    }
                    
                }
            }   
            if ($report_type == 10) {
                $schemaName = $this->schema;
                $userId = $this->sessdata['user_id'];
            
                if ($this->sessdata['group_id'] == 2) {
                    $query = $this->db->query("
                        SELECT * 
                        FROM public.get_divice_details_record_for_list('$schemaName', $userId) 
                        WHERE did IN ($dids) 
                        AND (issudate::date BETWEEN '$date_from' AND '$date_to')
                    ");
                } else {
                    $query = $this->db->query("
                        SELECT * 
                        FROM get_divice_details_record_for_list_for_company('$schemaName', $userId) 
                        WHERE did IN ($dids) 
                        AND (issudate::date BETWEEN '$date_from' AND '$date_to') 
                        AND sup_gid = 2
                    ");
                }
            
                $data['alldata'] = $query->getResult();
            }
            if ($report_type == 11) {
                $data['alldata'] = [];
                
                // Execute the query
                $query = $this->db->query("
                    SELECT 
                        typeofuser, 
                        deviceid, 
                        devicename,  
                        sesectionstartttime, 
                        sesectionendttime, 
                        starttime, 
                        endtime, 
                        duration,
                        startpoletime,
                        startpole,
                        endpointtime,
                        endpoint, 
                        (SELECT device_name 
                         FROM ".$this->schema.".master_device_setup 
                         WHERE deviceid = mdd.superdevid 
                         AND id = (SELECT max(id) 
                                    FROM ".$this->schema.".master_device_setup 
                                    WHERE inserttime::date <= '$date_from'::date 
                                    AND deviceid = mdd.superdevid)) AS mdddevicename, 
                        mdd.serial_no 
                    FROM public.analysish_work_patrol_time_schudele('$date_from'::date, '".$this->schema."'::character varying) AS ax 
                    RIGHT JOIN ".$this->schema.".master_device_details AS mdd 
                    ON (ax.deviceid = mdd.superdevid) 
                    WHERE mdd.superdevid IN ($dids)
                ");
            
                $result = $query->getResult();
            
                $i = 0;
                foreach ($result as $result_each) {
                    if (strpos($result_each->mdddevicename, 'Patrolman') !== false) {
                        $data['alldata'][$i] = new \stdClass();
                        $data['alldata'][$i]->typeofuser = $result_each->typeofuser;
                        $data['alldata'][$i]->deviceid = $result_each->deviceid;
                        $data['alldata'][$i]->devicealiasname = $result_each->mdddevicename;
                        $data['alldata'][$i]->devicename = $result_each->serial_no;
                        $data['alldata'][$i]->sessiondate = $result_each->sessiondate;
                        $data['alldata'][$i]->schedulestarttime = date('d-m-Y H:i:s', strtotime($result_each->starttime));
                        $data['alldata'][$i]->scheduleendtime = date('d-m-Y H:i:s', strtotime($result_each->endtime));
                        
                        $data['alldata'][$i]->startpoletime = ($result_each->startpoletime == '2000-10-01 00:00:00') 
                            ? 'NA' 
                            : date('d-m-Y H:i:s', strtotime($result_each->startpoletime));
                            
                        $data['alldata'][$i]->endpointtime = ($result_each->endpointtime == '2000-10-01 00:00:00') 
                            ? 'NA' 
                            : date('d-m-Y H:i:s', strtotime($result_each->endpointtime));
                            
                        $data['alldata'][$i]->startpole = $result_each->startpole;
                        $data['alldata'][$i]->endpole = $result_each->endpoint;
            
                        $i++;
                    }
                }
            
                // Optional debug outputs (uncomment if needed)
                // echo $this->db->getLastQuery(); exit;
                // echo '<pre>'; print_r($data['alldata']); exit;
            }            
                     
    
            // Continue with other report types...
        }
    

        $data['middle'] = view('traxreport/report_view', $data);
        return view('mainlayout', $data);
    }

    public function reportexcel()
    {
        ini_set('max_execution_time', 3000);
        ini_set('memory_limit', '-1');

        // Check if the request is a POST
        if ($this->request->getMethod() == 'POST') {
            $data = [];
            $user_id = $this->sessdata['user_id'];
            $group_id = $this->sessdata['group_id'];
            $devices = $alerts = $geofences = $routes = $dids = '';
            $data['report_type'] = $report_type = trim($this->request->getPost('report_type'));

            // Process date inputs
            $date_from = date("Y-m-d H:i:s", strtotime(trim($this->request->getPost('date_from'))));
            $date_to = date("Y-m-d H:i:s", strtotime(trim($this->request->getPost('date_to'))));
            $data['date_from'] = date("d-m-Y H:i", strtotime(trim($this->request->getPost('date_from'))));
            $data['date_to'] = date("d-m-Y H:i", strtotime(trim($this->request->getPost('date_to'))));
            $time_from = "00:00:00";
            $time_to = "23:59:59";
            
            $data['pwi_id'] = $pwi_id = trim($this->request->getPost('user'));
            $data['pwi_name'] = $pwi_name = trim($this->request->getPost('pwi_name'));
            $sse_pwy = trim($this->request->getPost('pway_id'));
            $devices = "{";

            // Check if the PWI name is 'All'
            if ($data['pwi_name'] == 'All') {
                if ($sse_pwy == 'All') {
                    // Query for 'All' section
                    $query = $this->db->query("
                        SELECT 
                            sup_pid, sup_gid, serial_no, imei_no, did, mobile_no, 
                            warranty_date, linked, parent_id, user_id, issudate, 
                            refunddate, active, issold, apply_scheam, group_id, role_id, 
                            email, address, pincode, state_name, country, username, 
                            firstname, lastname, organisation, group_name, 
                            '' AS list_item, '' AS list_item_name 
                        FROM public.get_divice_details_record_for_list('{$this->schema}', $user_id) 
                        WHERE sup_gid = $group_id AND user_id = $user_id AND active = 1 
                        ORDER BY did ASC
                    ");
        
                    // Fetch result
                    $devicelist = $query->getResult();
        
                } else {
                    // Query for specific sections
                    $sectionListQuery = $this->db->query("
                        SELECT user_id 
                        FROM public.user_login 
                        WHERE parent_id = $sse_pwy AND active = 1
                    ");
        
                    $sectionList = $sectionListQuery->getResult();
        
                    // Loop over each user_id
                    foreach ($sectionList as $section) {
                        $sectionId = $section->user_id;
        
                        // Query for each section's devices
                        $deviceQuery = $this->db->query("
                            SELECT 
                                sup_pid, sup_gid, serial_no, imei_no, did, mobile_no, 
                                warranty_date, linked, parent_id, user_id, issudate, 
                                refunddate, active, issold, apply_scheam, group_id, role_id, 
                                email, address, pincode, state_name, country, username, 
                                firstname, lastname, organisation, group_name, 
                                '' AS list_item, '' AS list_item_name 
                            FROM public.get_divice_details_record_for_list(:schema, :section_id) 
                            WHERE user_id = :section_id AND active = 1 
                            ORDER BY issudate ASC
                        ", [
                            'schema' => $this->schema,
                            'section_id' => $sectionId
                        ]);
        
                        // Merge the results into the device list
                        $devicelist = array_merge($deviceList, $deviceQuery->getResult());
                    }
                }
            } else {
                // Prepare the query for specific devices
                $query = "SELECT sup_pid, sup_gid, serial_no, imei_no, did, mobile_no, warranty_date, linked, parent_id, user_id, 
                                issudate, refunddate, active, issold, apply_scheam, group_id, role_id, email, address, pincode, 
                                state_name, country, username, firstname, lastname, organisation, group_name, '' AS list_item, 
                                '' AS list_item_name 
                        FROM public.get_divice_details_record_for_list('{$this->schema}', $pwi_id) 
                        WHERE user_id = $pwi_id AND active = 1 
                        ORDER BY issudate ASC";
                
                // Execute the query
                $devicelist = $this->db->query($query)->getResult();
            }

            // Initialize dids variable
            $dids = "";

            // Check if there are devices in the list
            if (count($devicelist) > 0) {
                foreach ($devicelist as $devicelist_each) {
                    // Add device ID to devices and dids
                    if ($devices == "{") {
                        $devices .= $devicelist_each->did;
                        $dids .= $devicelist_each->did;
                    } else {
                        $devices .= "," . $devicelist_each->did;
                        $dids .= "," . $devicelist_each->did;
                    }
                }
            }

            // Close the devices variable
            $devices .= "}";

            if ($report_type == 7) {
                $data['device_id'] = $device_id = trim($this->request->getPost('device_id'));
				if(!empty($device_id)){
					$dids = $device_id;
				}
				if($dids == '')
				{
					$dids = 0;
				}
    
                // Build SQL query
                $alldatanew = $this->db->query("
                    select mddd,mdddevicename,mddserialno,divicename, result_date, deviceid, acting_trip, 
				start_time, endtime,duration, distance_cover, sos_no, alert_no, call_no, totalnoofstop, 
				totalstoptime, polno, rd, dv, devicename, acting_triped, starttime,   end_time, duration1, 
				distancecover, sosno, alertno, callno, totalnoof_stop,  totalsto_ptime, pol_no, polename1, 
				polnoend, polenameend1, polename, polenameend, genid from (( SELECT distinct deviceid as mddd,
				(SELECT device_name FROM ".$this->schema.".master_device_setup  where deviceid=ax.deviceid 
				and id=(SELECT  max(id) FROM ".$this->schema.".master_device_setup 
				where inserttime::date<='$date_to'::date  and deviceid=ax.deviceid )) as mdddevicename,
				(SELECT  coalesce(serial_no,'Absent')  FROM ".$this->schema.".master_device_details  
				where superdevid=ax.deviceid or id=ax.deviceid) as mddserialno   
				FROM ".$this->schema.".master_device_assign as ax where deviceid in ($dids) and group_id=2 )  masterdevice 
				left outer join (select * from public.trip_spesified_device where (genid,deviceid,result_date,acting_trip) 
				in(select max(genid),deviceid,result_date,acting_trip from public.trip_spesified_device 
				where deviceid in ($dids) and (result_date||' '||start_time >= '".$date_from." ".$time_from."' 
				and result_date||' '||end_time <= '".$date_to." ".$time_to."') group by deviceid,result_date,acting_trip) 
				order by devicename,result_date,acting_trip)resultdivice on masterdevice.mddd=resultdivice.deviceid)resultset 
				order by mddd,result_date,start_time asc
                ")->getResult();
    
                $alldata = [];
                foreach ($alldatanew as $data1) {
                    // echo $data['mddserialno'];
                    $serialno = $data1->mddserialno;
                    $device_assign_details = $this->db->query("
                        SELECT a.parent_user_id, a.current_user_id, b.organisation AS pwy, c.organisation AS section
                        FROM public.device_asign_details AS a
                        LEFT JOIN public.user_login AS b ON a.parent_user_id = b.user_id
                        LEFT JOIN public.user_login AS c ON a.current_user_id = c.user_id
                        WHERE a.serial_no = '$serialno'
                    ")->getResult();
    
                    // Attach additional data
                    $data1->pwy = $device_assign_details[0]->pwy ?? null;
                    $data1->section = $device_assign_details[0]->section ?? null;
    
                    // Parsing devicename
                    if($data1->mdddevicename) {
                        $devicenameArr = explode(':', $data1->mdddevicename);
                        if(count($devicenameArr) > 0 && !empty($devicenameArr[1])) {
                            $poledetailsnew = trim($devicenameArr[1]);
                        } else {
                            $poledetailsnew = $data1->mdddevicename;
                        }
                        $poledetailsnewArr = explode('(', $poledetailsnew);
                        $polenamenew = trim($poledetailsnewArr[1]);
                        $polenamenew = str_replace(')', '', $polenamenew);
                        $polenamenewArr = explode('-', $polenamenew);
                        $data1->startpole = $polenamenewArr[0];
                        $data1->stoppol = $polenamenewArr[1];
                        $data1->bit = $data1->startpole . '-' . $data1->stoppol;
                    } else {
                        $data1->startpole = 'NA';
                        $data1->stoppol = 'NA';
                        $data1->bit = 'NA';
                    }
                    
    
                    if ($data1->divicename != '') {
                        $alldata[] = $data1;
                    }
                }

                // echo "<pre>";print_r($alldata);exit();
                // Handling trip merging
                $ddddd = !empty($alldata) ? $alldata[0]->mddd : 0;
                $alldataall = [];
                for ($x = 0; $x < count($alldata); $x++) {
                    // Check if next item exists and apply conditions
                    if (isset($alldata[$x+1]) && 
                        ($ddddd == $alldata[$x+1]->mddd) && 
                        ($alldata[$x]->start_time > '17:59:59') && 
                        ($alldata[$x+1]->end_time < '07:30:00')) {
                        
                        // Calculate the duration between two records
                        $newduration = $this->db->query("
                            SELECT AGE('{$alldata[$x]->result_date} {$alldata[$x]->start_time}', 
                                    '{$alldata[$x+1]->result_date} {$alldata[$x+1]->endtime}') AS duration
                        ")->getResult();

                        // Assign calculated duration and merge fields
                        $alldata[$x+1]->duration = ltrim($newduration[0]->duration, '-');
                        $alldata[$x+1]->start_time = $alldata[$x]->start_time;
                        $alldata[$x+1]->result_date = $alldata[$x]->result_date;
                        $alldata[$x+1]->distance_cover += $alldata[$x]->distance_cover;
                        $alldata[$x+1]->sos_no += $alldata[$x]->sos_no;
                        $alldata[$x+1]->alert_no += $alldata[$x]->alert_no;
                        $alldata[$x+1]->call_no += $alldata[$x]->call_no;
                        $alldata[$x+1]->totalnoofstop += $alldata[$x]->totalnoofstop;
                        $alldata[$x+1]->totalstoptime += $alldata[$x]->totalstoptime;
                    } else {
                        // Add the current item to the final list
                        $alldataall[] = $alldata[$x];
                        if (isset($alldata[$x+1])) {
                            $ddddd = $alldata[$x+1]->mddd;
                        }
                    }
                }

                // Handling trip numbers
                $deviceid = '';
                $tripNo = 1;  // Initialize trip number
                foreach ($alldataall as $y => $data2) {
                    // Check if the current device matches the previous one
                    if ($data2->mddd == $deviceid) {
                        $tripNo++;
                    } else {
                        $deviceid = $data2->mddd;  // Update the device ID
                        $tripNo = 1;  // Reset trip number for new device
                    }

                    // Assign the trip number to the current data
                    $alldataall[$y]->acting_trip = $tripNo;
                }

                $alldataall = (array) $alldataall;
    
                // Prepare data for Excel export
                $dat[0] = [
                    'A' => "Date",
                    'B' => "Device Name",
                    'C' => "Device ID",
                    'D' => "BIT",
                    'E' => "SSE/PWAY",
                    'F' => "Section",
                    'G' => "Type",
                    'H' => "Trip No.",
                    'I' => "Start Time(HH:MM:SS)",
                    'J' => "End Time(HH:MM:SS)",
                    'K' => "Travelled Distance(KM)",
                    'L' => "Travelled Time(HH:MM:SS)",
                    'M' => "Stop Duration(HH:MM:SS)",
                    'N' => "No. of SOS",
                    'O' => "No. of Call",
                    'P' => "No. of Alert",
                ];
    
                foreach ($alldataall as $Key => $val) {
                    $mdddevicename_arr = explode("/", $val->mdddevicename);
                    $type = '';
    
                    // Determine the type based on the device name
                    if (strpos(strtolower($mdddevicename_arr[0]), 'stock') !== false) {
                        $type = 'Stock';
                    } elseif (strpos(strtolower($mdddevicename_arr[0]), 'keyman') !== false) {
                        $type = 'Keyman';
                    } elseif (strpos(strtolower($mdddevicename_arr[0]), 'patrolman') !== false) {
                        $type = 'Patrolman';
                    }
    
                    $PWI = explode("(", $val->mdddevicename);
                    $newPwI = '(' . implode('', array_slice($PWI, 1));
    
                    if ($val->acting_trip != '') {
                        $dat[$Key + 1] = [
                            'A' => date("d-m-Y", strtotime($val->result_date)),
                            'B' => $val->mdddevicename,
                            'C' => $val->mddserialno,
                            'D' => $val->bit ?? '',
                            'E' => $val->pwy,
                            'F' => $val->section,
                            'G' => $type,
                            'H' => $val->acting_trip,
                            'I' => $val->start_time,
                            'J' => $val->end_time,
                            'K' => round($val->distance_cover / 1000, 2),
                            'L' => $val->duration,
                            'M' => $val->totalstoptime,
                            'N' => $val->sos_no,
                            'O' => $val->call_no,
                            'P' => $val->alert_no,
                        ];
                    } else {
                        $dat[$Key + 1] = [
                            'A' => 'NA',
                            'B' => $val->mdddevicename,
                            'C' => $val->mddserialno,
                            'D' => $newPwI,
                            'E' => $val->pwy,
                            'F' => $val->section,
                            'G' => $type,
                            'H' => 'NA',
                            'I' => 'NA',
                            'J' => 'NA',
                            'K' => 'NA',
                            'L' => 'NA',
                            'M' => 'NA',
                            'N' => 'NA',
                            'O' => 'NA',
                            'P' => 'NA',
                        ];
                    }
                }
    
                $filename = 'General_Report_' . $data['pwi_name'] . '_' . time() . '.xlsx';
                // Code to generate Excel file goes here...
    
                // Return the result or view as needed
            }
            if ($report_type == 8) {
                $data['alldata'] = [];
            
                // Use the Query Builder or Raw Queries
                $query = $this->db->query("SELECT typeofuser, deviceid, devicename, sessiondate, sesectionstartttime, 
                                            sesectionendttime, starttime, endtime, duration, orginallength, 
                                            withinpolelength AS totalwitpole, polesequenct, 
                                            (SELECT device_name FROM " . $this->schema . ".master_device_setup 
                                            WHERE deviceid = mdd.superdevid 
                                            AND id = (SELECT MAX(id) FROM " . $this->schema . ".master_device_setup 
                                            WHERE inserttime::date <= '$date_from'::date 
                                            AND deviceid = mdd.superdevid)) AS mdddevicename, 
                                            mdd.serial_no 
                                            FROM public.analysish_patrol_man_and_key_man_work_patrol('$date_from') AS ax 
                                            RIGHT JOIN " . $this->schema . ".master_device_details AS mdd 
                                            ON (ax.deviceid = mdd.superdevid) 
                                            WHERE mdd.superdevid IN ($dids)");
            
                $result = $query->getResult();
            
                $deviceserial = '';
                $i = 0;
                
                foreach ($result as $result_each) {
                    if ($deviceserial != $result_each->serial_no) {
                        if ($deviceserial == '') {
                            $i = 0;
                        } else {
                            $i++;
                        }
            
                        $deviceserial = $result_each->serial_no;
            
                        if ($result_each->deviceid != '') {
                            $data['alldata'][$i] = (object) [
                                'typeofuser' => $result_each->typeofuser,
                                'deviceid' => $result_each->deviceid,
                                'devicealiasname' => $result_each->mdddevicename,
                                'devicename' => $result_each->serial_no,
                                'sessiondate' => $result_each->sessiondate,
                                'starttime' => $result_each->starttime,
                                'endtime' => $result_each->endtime,
                                'duration' => $result_each->duration,
                                'distance' => $result_each->totalwitpole,
                                'orginallength' => $result_each->orginallength
                            ];
                        } else {
                            $data['alldata'][$i] = (object) [
                                'typeofuser' => 'NA',
                                'deviceid' => 'NA',
                                'devicealiasname' => $result_each->mdddevicename,
                                'devicename' => $result_each->serial_no,
                                'sessiondate' => $result_each->sessiondate,
                                'starttime' => '00:00:00',
                                'endtime' => '00:00:00',
                                'duration' => 'NA',
                                'distance' => $result_each->totalwitpole,
                                'orginallength' => $result_each->orginallength
                            ];
                        }
                    } else {
                        $data['alldata'][$i]->distance += $result_each->totalwitpole;
                    }
                }
            
                // Prepare headers
                $dat[0] = [
                    'A' => "User Type",
                    'B' => "Device Name",
                    'C' => "Device ID",
                    'D' => "Start(DD-MM-YYYY HH:MM:SS)",
                    'E' => "End(DD-MM-YYYY HH:MM:SS)",
                    'F' => "Within Pole Distance(KM)",
                    'G' => "Total Distance(KM)",
                    'H' => "Travelled Time(HH:MM:SS)"
                ];
            
                foreach ($data['alldata'] as $Key => $val) {
                    if ($val->typeofuser == 'PatrolMan') {
                        $start = date("d-m-Y", strtotime($val->sessiondate)) . ' ' . $val->starttime;
                        $end = date("d-m-Y", strtotime($val->sessiondate . ' +1 day')) . ' ' . $val->endtime;
                    } elseif ($val->typeofuser == 'Key Man') {
                        $start = date("d-m-Y", strtotime($val->sessiondate)) . ' ' . $val->starttime;
                        $end = date("d-m-Y", strtotime($val->sessiondate)) . ' ' . $val->endtime;
                    } else {
                        $start = 'NA';
                        $end = 'NA';
                    }
            
                    if ($val->orginallength < $val->distance) {
                        $val->orginallength = $val->distance;
                    }
            
                    $dat[$Key + 1] = [
                        'A' => $val->typeofuser,
                        'B' => $val->devicealiasname,
                        'C' => $val->devicename,
                        'D' => $start,
                        'E' => $end,
                        'F' => round($val->distance / 1000, 2),
                        'G' => round($val->orginallength / 1000, 2),
                        'H' => $val->duration
                    ];
                }
            
                $filename = 'Trip_Date_Wise_Report_' . $data['pwi_name'] . '_' . time() . '.xlsx';
            }
            if ($report_type == 9) {
                $data['alldata'] = [];
            
                // Execute the main query
                $query = $this->db->query("
                    SELECT mddd, mdddevicename, mddserialno, divicename, result_date, deviceid, acting_trip, 
                           start_time, endtime, duration, distance_cover, sos_no, alert_no, call_no, totalnoofstop, 
                           totalstoptime, polno, rd, dv, devicename, acting_triped, starttime, end_time, 
                           duration1, distancecover, sosno, alertno, callno, totalno_of_stop, totalsto_ptime, 
                           pol_no, polename1, polnoend, polenameend1, polename, polenameend, genid 
                    FROM (
                        SELECT DISTINCT deviceid AS mddd, 
                            (SELECT device_name FROM " . $this->schema . ".master_device_setup 
                             WHERE deviceid = ax.deviceid AND id = (SELECT MAX(id) 
                                                                     FROM " . $this->schema . ".master_device_setup 
                                                                     WHERE inserttime::date <= '$date_to'::date 
                                                                     AND deviceid = ax.deviceid)) AS mdddevicename, 
                            (SELECT COALESCE(serial_no, 'Absent') 
                             FROM " . $this->schema . ".master_device_details 
                             WHERE superdevid = ax.deviceid OR id = ax.deviceid) AS mddserialno 
                        FROM " . $this->schema . ".master_device_assign AS ax 
                        WHERE deviceid IN ($dids) AND group_id = 2
                    ) masterdevice 
                    LEFT OUTER JOIN (
                        SELECT * 
                        FROM public.trip_spesified_device 
                        WHERE (genid, deviceid, result_date, acting_trip) IN (
                            SELECT MAX(genid), deviceid, result_date, acting_trip 
                            FROM public.trip_spesified_device 
                            WHERE deviceid IN ($dids) 
                            AND (result_date || ' ' || start_time >= '" . $date_from . " " . $time_from . "' 
                            AND result_date || ' ' || end_time <= '" . $date_to . " " . $time_to . "') 
                            GROUP BY deviceid, result_date, acting_trip
                        ) 
                        ORDER BY devicename, result_date, acting_trip
                    ) resultdivice ON masterdevice.mddd = resultdivice.deviceid
                ORDER BY result_date, start_time, mddd ASC
                ");
            
                $alldata = $query->getResult();
            
                for ($x = 0; $x < count($alldata); $x++) {
                    $serialno = $alldata[$x]->mddserialno;
            
                    // Get device assignment details
                    $device_assign_details = $this->db->query("
                        SELECT a.parent_user_id, a.current_user_id, b.organisation AS pwy, c.organisation AS section 
                        FROM public.device_asign_details AS a 
                        LEFT JOIN public.user_login AS b ON (a.parent_user_id = b.user_id) 
                        LEFT JOIN public.user_login AS c ON (a.current_user_id = c.user_id) 
                        WHERE a.serial_no = '" . $serialno . "'
                    ")->getResult();
            
                    // Assign details to alldata
                    $alldata[$x]->pwy = isset($device_assign_details[0]) ? $device_assign_details[0]->pwy : 'NA';
                    $alldata[$x]->section = isset($device_assign_details[0]) ? $device_assign_details[0]->section : 'NA';
            
                    // Process device name for BIT
                    $devicename = $alldata[$x]->mdddevicename;
                    $devicenameArr = explode(':', $devicename);
                    $poledetailsnew = trim($devicenameArr[1]);
                    $poledetailsnewArr = explode('(', $poledetailsnew);
                    $polenamenew = trim($poledetailsnewArr[1]);
                    $polenamenew = str_replace(')', '', $polenamenew);
                    $polenamenewArr = explode('-', $polenamenew);
                    
                    // Store pole information
                    $alldata[$x]->startpole = trim($polenamenewArr[0]);
                    $alldata[$x]->stoppol = trim($polenamenewArr[1]);
                    $alldata[$x]->bit = $alldata[$x]->startpole . '-' . $alldata[$x]->stoppol;
                }
            
                // Prepare headers
                $dat[0] = [
                    'A' => "Date",
                    'B' => "Device Name",
                    'C' => "Device ID",
                    'D' => "SSE/PWAY",
                    'E' => "Section",
                    'F' => "BIT",
                    'G' => "UserType",
                    'H' => "Trip No.",
                    'I' => "Travelled Distance(KM)"
                ];
            
                foreach ($alldata as $Key => $val) {
                    if ($val->result_date != '') {
                        if ($val->acting_trip != '') {
                            $devicename = $val->mdddevicename;
                            $sectionArr = explode("(", $devicename);
                            $str = $sectionArr[1];
                            $bit = str_replace(')', '', $str);
                            $sectionArr1 = explode("/", $devicename);
                            $usertype = $sectionArr1[0];
            
                            $dat[$Key + 1] = [
                                'A' => date("d-m-Y", strtotime($val->result_date)),
                                'B' => $val->mdddevicename,
                                'C' => $val->mddserialno,
                                'D' => $val->pwy,
                                'E' => $val->section,
                                'F' => $val->bit,
                                'G' => $usertype,
                                'H' => $val->acting_trip,
                                'I' => round($val->distance_cover / 1000, 2)
                            ];
                        } else {
                            $dat[$Key + 1] = [
                                'A' => 'NA',
                                'B' => $val->mdddevicename,
                                'C' => $val->mddserialno,
                                'D' => $val->pwy,
                                'E' => $val->section,
                                'F' => $val->bit,
                                'G' => $usertype ?? 'NA',
                                'H' => 'NA',
                                'I' => 'NA'
                            ];
                        }
                    }
                }
            
                $filename = 'Movement_Summery_Report_' . $data['pwi_name'] . '_' . time() . '.xlsx';
            }            
            if ($report_type == 10) {
                // Check user group and fetch device details accordingly
                if ($group_id == 2) {
                    $query = $this->db->query("
                        SELECT * 
                        FROM public.get_divice_details_record_for_list(
                            '" . $this->schema . "', " . $user_id . "
                        ) 
                        WHERE did IN ($dids) 
                        AND (issudate::date BETWEEN '" . $date_from . "' AND '" . $date_to . "')
                    ");
                } else {
                    $query = $this->db->query("
                        SELECT * 
                        FROM get_divice_details_record_for_list_for_company(
                            '" . $this->schema . "', " . $user_id . "
                        ) 
                        WHERE did IN ($dids) 
                        AND (issudate::date BETWEEN '" . $date_from . "' AND '" . $date_to . "') 
                        AND sup_gid = 2
                    ");
                }
            
                $data['alldata'] = $query->getResult(); // Fetch the results
            
                // Prepare the headers for the output
                $dat[0] = [
                    'A' => "Device ID",
                    'B' => "IMEI No.",
                    'C' => "Allotee Name",
                    'D' => "Allotment Date"
                ];
            
                // Populate the data rows
                foreach ($data['alldata'] as $Key => $val) {
                    $dat[$Key + 1] = [
                        'A' => $val->serial_no,
                        'B' => $val->imei_no,
                        'C' => $val->organisation,
                        'D' => date("d-m-Y", strtotime($val->issudate))
                    ];
                }
            
                // Set the filename for the report
                $filename = 'Device_Allotment_Report_' . $data['pwi_name'] . '_' . time() . '.xlsx';
            }
            if ($report_type == 11) {
                $data['alldata'] = [];
                
                // Fetch data from the database
                $result = $this->db->query("
                    SELECT 
                        typeofuser, 
                        deviceid, 
                        devicename, 
                        sesectionstartttime, 
                        sesectionendttime, 
                        starttime, 
                        endtime, 
                        duration, 
                        startpoletime, 
                        startpole, 
                        endpointtime, 
                        endpoint, 
                        (SELECT device_name 
                         FROM " . $this->schema . ".master_device_setup 
                         WHERE deviceid = mdd.superdevid 
                         AND id = (SELECT max(id) 
                                    FROM " . $this->schema . ".master_device_setup 
                                    WHERE inserttime::date <= '$date_from'::date 
                                    AND deviceid = mdd.superdevid)) AS mdddevicename, 
                        mdd.serial_no 
                    FROM 
                        public.analysish_work_patrol_time_schudele('$date_from') AS ax 
                    RIGHT JOIN 
                        " . $this->schema . ".master_device_details AS mdd 
                    ON 
                        (ax.deviceid = mdd.superdevid) 
                    WHERE 
                        mdd.superdevid IN ($dids)
                ")->getResult();
            
                $deviceserial = '';
                $i = 0;
            
                foreach ($result as $result_each) {
                    if (strpos($result_each->mdddevicename, 'Patrolman') !== false) {
                        $data['alldata'][$i] = new \stdClass(); // Create an empty object for each entry
                        $data['alldata'][$i]->typeofuser = $result_each->typeofuser;
                        $data['alldata'][$i]->deviceid = $result_each->deviceid;
                        $data['alldata'][$i]->devicealiasname = $result_each->mdddevicename;
                        $data['alldata'][$i]->devicename = $result_each->serial_no;
                        $data['alldata'][$i]->sessiondate = $result_each->sessiondate;
                        $data['alldata'][$i]->schedulestarttime = date('d-m-Y H:i:s', strtotime($result_each->starttime));
                        $data['alldata'][$i]->scheduleendtime = date('d-m-Y H:i:s', strtotime($result_each->endtime));
            
                        // Handle startpoletime
                        $data['alldata'][$i]->startpoletime = ($result_each->startpoletime == '2000-10-01 00:00:00') ? 'NA' : date('d-m-Y H:i:s', strtotime($result_each->startpoletime));
            
                        // Handle endpointtime
                        $data['alldata'][$i]->endpointtime = ($result_each->endpointtime == '2000-10-01 00:00:00') ? 'NA' : date('d-m-Y H:i:s', strtotime($result_each->endpointtime));
            
                        $data['alldata'][$i]->startpole = $result_each->startpole;
                        $data['alldata'][$i]->endpole = $result_each->endpoint;
            
                        $i++;
                    }
                }
            
                // Prepare headers for the output
                $dat[0] = [
                    'A' => "User Type",
                    'B' => "Device Name",
                    'C' => "Device ID",
                    'D' => "Start Pole",
                    'E' => "End Pole",
                    'F' => "Scheduled Start(DD-MM-YYYY HH:MM:SS)",
                    'G' => "Scheduled End(DD-MM-YYYY HH:MM:SS)",
                    'H' => "Actual Start(DD-MM-YYYY HH:MM:SS)",
                    'I' => "Actual End(DD-MM-YYYY HH:MM:SS)"
                ];
            
                // Populate the data rows
                foreach ($data['alldata'] as $Key => $val) {
                    $schedulestarttime = $val->schedulestarttime ?? 'NA';
                    $scheduleendtime = $val->scheduleendtime ?? 'NA';
                    $startpoletime = $val->startpoletime ?? 'NA';
                    $endpointtime = $val->endpointtime ?? 'NA';
                    $startpole = $val->startpole ?? 'NA';
                    $endpole = $val->endpole ?? 'NA';
            
                    $dat[$Key + 1] = [
                        'A' => $val->typeofuser,
                        'B' => $val->devicealiasname,
                        'C' => $val->devicename,
                        'D' => $startpole,
                        'E' => $endpole,
                        'F' => $schedulestarttime,
                        'G' => $scheduleendtime,
                        'H' => $startpoletime,
                        'I' => $endpointtime
                    ];
                }
            
                // Set the filename for the report
                $filename = 'Trip_Deviation_Date_Wise_Report_' . $data['pwi_name'] . '_' . time() . '.xlsx';
            }            

            helper('master');
            // Call the excelDownload function (implement as shown previously)
            excelDownload($dat, $filename);
        }
    }

    public function reportpdf() {
        $sessdata = session()->get(); // Using session() helper in CI4
        ini_set('max_execution_time', 3000);
        ini_set('memory_limit', '-1');
    
        if (!empty($this->request->getPost())) {
            $user_id = $this->sessdata['user_id'];
            $group_id = $this->sessdata['group_id'];
            $devices = $alerts = $geofences = $routes = $dids = null;
            $data['report_type'] = $report_type = trim($this->request->getPost('report_type'));
    
            $date_from = date("Y-m-d H:i:s", strtotime(trim($this->request->getPost('date_from'))));
            $date_to = date("Y-m-d H:i:s", strtotime(trim($this->request->getPost('date_to'))));
            $data['date_from'] = date("d-m-Y H:i", strtotime(trim($this->request->getPost('date_from'))));
            $data['date_to'] = date("d-m-Y H:i", strtotime(trim($this->request->getPost('date_to'))));
            $time_from = "00:00:00";
            $time_to = "23:59:59";
    
            $data['pwi_id'] = $pwi_id = trim($this->request->getPost('user'));
            $data['pwi_name'] = trim($this->request->getPost('pwi_name'));
            $data['schema'] = $this->schema;
    
            $sse_pwy = trim($this->request->getPost('pway_id'));
            // Initialize devices variable
            $devices = "{";

            // Check if the PWI name is 'All'
            if ($data['pwi_name'] == 'All') {
                if ($sse_pwy == 'All') {
                    // Query for 'All' section
                    $query = $this->db->query("
                        SELECT 
                            sup_pid, sup_gid, serial_no, imei_no, did, mobile_no, 
                            warranty_date, linked, parent_id, user_id, issudate, 
                            refunddate, active, issold, apply_scheam, group_id, role_id, 
                            email, address, pincode, state_name, country, username, 
                            firstname, lastname, organisation, group_name, 
                            '' AS list_item, '' AS list_item_name 
                        FROM public.get_divice_details_record_for_list('{$this->schema}', $user_id) 
                        WHERE sup_gid = $group_id AND user_id = $user_id AND active = 1 
                        ORDER BY did ASC
                    ");
        
                    // Fetch result
                    $devicelist = $query->getResult();
        
                } else {
                    // Query for specific sections
                    $sectionListQuery = $this->db->query("
                        SELECT user_id 
                        FROM public.user_login 
                        WHERE parent_id = $sse_pwy AND active = 1
                    ");
        
                    $sectionList = $sectionListQuery->getResult();
        
                    // Loop over each user_id
                    foreach ($sectionList as $section) {
                        $sectionId = $section->user_id;
        
                        // Query for each section's devices
                        $deviceQuery = $this->db->query("
                            SELECT 
                                sup_pid, sup_gid, serial_no, imei_no, did, mobile_no, 
                                warranty_date, linked, parent_id, user_id, issudate, 
                                refunddate, active, issold, apply_scheam, group_id, role_id, 
                                email, address, pincode, state_name, country, username, 
                                firstname, lastname, organisation, group_name, 
                                '' AS list_item, '' AS list_item_name 
                            FROM public.get_divice_details_record_for_list(:schema, :section_id) 
                            WHERE user_id = :section_id AND active = 1 
                            ORDER BY issudate ASC
                        ", [
                            'schema' => $this->schema,
                            'section_id' => $sectionId
                        ]);
        
                        // Merge the results into the device list
                        $devicelist = array_merge($deviceList, $deviceQuery->getResult());
                    }
                }
            } else {
                // Prepare the query for specific devices
                $query = "SELECT sup_pid, sup_gid, serial_no, imei_no, did, mobile_no, warranty_date, linked, parent_id, user_id, 
                                issudate, refunddate, active, issold, apply_scheam, group_id, role_id, email, address, pincode, 
                                state_name, country, username, firstname, lastname, organisation, group_name, '' AS list_item, 
                                '' AS list_item_name 
                        FROM public.get_divice_details_record_for_list('{$this->schema}', $pwi_id) 
                        WHERE user_id = $pwi_id AND active = 1 
                        ORDER BY issudate ASC";
                
                // Execute the query
                $devicelist = $this->db->query($query)->getResult();
            }

            // Initialize dids variable
            $dids = "";

            // Check if there are devices in the list
            if (count($devicelist) > 0) {
                foreach ($devicelist as $devicelist_each) {
                    // Add device ID to devices and dids
                    if ($devices == "{") {
                        $devices .= $devicelist_each->did;
                        $dids .= $devicelist_each->did;
                    } else {
                        $devices .= "," . $devicelist_each->did;
                        $dids .= "," . $devicelist_each->did;
                    }
                }
            }

            // Close the devices variable
            $devices .= "}";

            $data['alldata'] = [];
    
            if ($report_type == 7) {
                $data['device_id'] = $device_id = trim($this->request->getPost('device_id'));
				if(!empty($device_id)){
					$dids = $device_id;
				}
				if($dids == '')
				{
					$dids = 0;
				}
    
                // Build SQL query
                $alldatanew = $this->db->query("
                    select mddd,mdddevicename,mddserialno,divicename, result_date, deviceid, acting_trip, 
				start_time, endtime,duration, distance_cover, sos_no, alert_no, call_no, totalnoofstop, 
				totalstoptime, polno, rd, dv, devicename, acting_triped, starttime,   end_time, duration1, 
				distancecover, sosno, alertno, callno, totalnoof_stop,  totalsto_ptime, pol_no, polename1, 
				polnoend, polenameend1, polename, polenameend, genid from (( SELECT distinct deviceid as mddd,
				(SELECT device_name FROM ".$this->schema.".master_device_setup  where deviceid=ax.deviceid 
				and id=(SELECT  max(id) FROM ".$this->schema.".master_device_setup 
				where inserttime::date<='$date_to'::date  and deviceid=ax.deviceid )) as mdddevicename,
				(SELECT  coalesce(serial_no,'Absent')  FROM ".$this->schema.".master_device_details  
				where superdevid=ax.deviceid or id=ax.deviceid) as mddserialno   
				FROM ".$this->schema.".master_device_assign as ax where deviceid in ($dids) and group_id=2 )  masterdevice 
				left outer join (select * from public.trip_spesified_device where (genid,deviceid,result_date,acting_trip) 
				in(select max(genid),deviceid,result_date,acting_trip from public.trip_spesified_device 
				where deviceid in ($dids) and (result_date||' '||start_time >= '".$date_from." ".$time_from."' 
				and result_date||' '||end_time <= '".$date_to." ".$time_to."') group by deviceid,result_date,acting_trip) 
				order by devicename,result_date,acting_trip)resultdivice on masterdevice.mddd=resultdivice.deviceid)resultset 
				order by mddd,result_date,start_time asc
                ")->getResult();
    
                $alldata = [];
                foreach ($alldatanew as $data1) {
                    // echo $data['mddserialno'];
                    $serialno = $data1->mddserialno;
                    $device_assign_details = $this->db->query("
                        SELECT a.parent_user_id, a.current_user_id, b.organisation AS pwy, c.organisation AS section
                        FROM public.device_asign_details AS a
                        LEFT JOIN public.user_login AS b ON a.parent_user_id = b.user_id
                        LEFT JOIN public.user_login AS c ON a.current_user_id = c.user_id
                        WHERE a.serial_no = '$serialno'
                    ")->getResult();
    
                    // Attach additional data
                    $data1->pwy = $device_assign_details[0]->pwy ?? null;
                    $data1->section = $device_assign_details[0]->section ?? null;
    
                    // Parsing devicename
                    if($data1->mdddevicename) {
                        $devicenameArr = explode(':', $data1->mdddevicename);
                        if(count($devicenameArr) > 0 && !empty($devicenameArr[1])) {
                            $poledetailsnew = trim($devicenameArr[1]);
                        } else {
                            $poledetailsnew = $data1->mdddevicename;
                        }
                        $poledetailsnewArr = explode('(', $poledetailsnew);
                        $polenamenew = trim($poledetailsnewArr[1]);
                        $polenamenew = str_replace(')', '', $polenamenew);
                        $polenamenewArr = explode('-', $polenamenew);
                        $data1->startpole = $polenamenewArr[0];
                        $data1->stoppol = $polenamenewArr[1];
                        $data1->bit = $data1->startpole . '-' . $data1->stoppol;
                    } else {
                        $data1->startpole = 'NA';
                        $data1->stoppol = 'NA';
                        $data1->bit = 'NA';
                    }
                    
    
                    if ($data1->divicename != '') {
                        $alldata[] = $data1;
                    }
                }

                // echo "<pre>";print_r($alldata);exit();
                // Handling trip merging
                $ddddd = !empty($alldata) ? $alldata[0]->mddd : 0;
                $alldataall = [];
                for ($x = 0; $x < count($alldata); $x++) {
                    // Check if next item exists and apply conditions
                    if (isset($alldata[$x+1]) && 
                        ($ddddd == $alldata[$x+1]->mddd) && 
                        ($alldata[$x]->start_time > '17:59:59') && 
                        ($alldata[$x+1]->end_time < '07:30:00')) {
                        
                        // Calculate the duration between two records
                        $newduration = $this->db->query("
                            SELECT AGE('{$alldata[$x]->result_date} {$alldata[$x]->start_time}', 
                                    '{$alldata[$x+1]->result_date} {$alldata[$x+1]->endtime}') AS duration
                        ")->getResult();

                        // Assign calculated duration and merge fields
                        $alldata[$x+1]->duration = ltrim($newduration[0]->duration, '-');
                        $alldata[$x+1]->start_time = $alldata[$x]->start_time;
                        $alldata[$x+1]->result_date = $alldata[$x]->result_date;
                        $alldata[$x+1]->distance_cover += $alldata[$x]->distance_cover;
                        $alldata[$x+1]->sos_no += $alldata[$x]->sos_no;
                        $alldata[$x+1]->alert_no += $alldata[$x]->alert_no;
                        $alldata[$x+1]->call_no += $alldata[$x]->call_no;
                        $alldata[$x+1]->totalnoofstop += $alldata[$x]->totalnoofstop;
                        $alldata[$x+1]->totalstoptime += $alldata[$x]->totalstoptime;
                    } else {
                        // Add the current item to the final list
                        $alldataall[] = $alldata[$x];
                        if (isset($alldata[$x+1])) {
                            $ddddd = $alldata[$x+1]->mddd;
                        }
                    }
                }

                // Handling trip numbers
                $deviceid = '';
                $tripNo = 1;  // Initialize trip number
                foreach ($alldataall as $y => $data2) {
                    // Check if the current device matches the previous one
                    if ($data2->mddd == $deviceid) {
                        $tripNo++;
                    } else {
                        $deviceid = $data2->mddd;  // Update the device ID
                        $tripNo = 1;  // Reset trip number for new device
                    }

                    // Assign the trip number to the current data
                    $alldataall[$y]->acting_trip = $tripNo;
                }

                $alldataall = (array) $alldataall;
    
                $data['alldata'] = $alldataall;
                $html = view('traxreport/pdf_stoppage', $data); // Load view in CI4
                $filename = 'General_Report_' . $data['pwi_name'] . '_' . time();
            }
            if ($report_type == 8) {
                
                $result = $this->db->query("SELECT typeofuser, deviceid, devicename, sessiondate, sesectionstartttime, sesectionendttime, starttime, endtime, duration, 
                    (orginallength), (withinpolelength) AS totalwitpole, polesequenct, 
                    (SELECT device_name FROM {$this->schema}.master_device_setup WHERE deviceid = mdd.superdevid AND id = 
                        (SELECT MAX(id) FROM {$this->schema}.master_device_setup WHERE inserttime::date <= '$date_from'::date AND deviceid = mdd.superdevid)) AS mdddevicename, 
                    mdd.serial_no 
                    FROM public.analysish_patrol_man_and_key_man_work_patrol('$date_from') AS ax 
                    RIGHT JOIN {$this->schema}.master_device_details AS mdd ON (ax.deviceid = mdd.superdevid) 
                    WHERE mdd.superdevid IN ($dids)")->getResult();
            
                $deviceserial = '';
                $i = 0;
            
                foreach ($result as $result_each) {
                    if ($deviceserial != $result_each->serial_no) {
                        if ($deviceserial == '') {
                            $i = 0;
                        } else {
                            $i++;
                        }
                        $deviceserial = $result_each->serial_no;
            
                        $data['alldata'][$i] = new \stdClass(); // Initialize an object for each entry
                        if ($result_each->deviceid != '') {
                            $data['alldata'][$i]->typeofuser = $result_each->typeofuser;
                            $data['alldata'][$i]->deviceid = $result_each->deviceid;
                            $data['alldata'][$i]->devicealiasname = $result_each->mdddevicename;
                            $data['alldata'][$i]->devicename = $result_each->serial_no;
                            $data['alldata'][$i]->sessiondate = $result_each->sessiondate;
                            $data['alldata'][$i]->starttime = $result_each->starttime;
                            $data['alldata'][$i]->endtime = $result_each->endtime;
                            $data['alldata'][$i]->duration = $result_each->duration;
                            $data['alldata'][$i]->distance = $result_each->totalwitpole;
                            $data['alldata'][$i]->orginallength = $result_each->orginallength;
                        } else {
                            $data['alldata'][$i]->typeofuser = 'NA';
                            $data['alldata'][$i]->deviceid = 'NA';
                            $data['alldata'][$i]->devicealiasname = $result_each->mdddevicename;
                            $data['alldata'][$i]->devicename = $result_each->serial_no;
                            $data['alldata'][$i]->sessiondate = $result_each->sessiondate;
                            $data['alldata'][$i]->starttime = '00:00:00';
                            $data['alldata'][$i]->endtime = '00:00:00';
                            $data['alldata'][$i]->duration = 'NA';
                            $data['alldata'][$i]->distance = $result_each->totalwitpole;
                            $data['alldata'][$i]->orginallength = $result_each->orginallength;
                        }
                    } else {
                        $data['alldata'][$i]->distance += $result_each->totalwitpole; // Accumulate distance
                    }
                }
            
                // Optionally uncomment to see the last query for debugging
                // echo $this->db->getLastQuery(); exit;
            
                $html = view('traxreport/pdf_trip', $data); // Use the view helper to load the view
                $filename = 'Trip_Date_Wise_Report_' . $data['pwi_name'] . '_' . time();
            }       
            if ($report_type == 9) {                
                $data['alldata'] = $this->db->query("SELECT mddd, mdddevicename, mddserialno, divicename, result_date, deviceid, acting_trip, start_time, endtime, 
                    duration, distance_cover, sos_no, alert_no, call_no, totalnoofstop, totalstoptime, polno, rd, dv, devicename, 
                    acting_triped, starttime, end_time, duration1, distancecover, sosno, alertno, callno, totalnoof_stop, 
                    totalsto_ptime, pol_no, polename1, polnoend, polenameend1, polename, polenameend, genid 
                    FROM (( 
                        SELECT DISTINCT deviceid AS mddd, 
                            (SELECT device_name FROM {$this->schema}.master_device_setup WHERE deviceid = ax.deviceid AND id = 
                                (SELECT MAX(id) FROM {$this->schema}.master_device_setup WHERE inserttime::date <= '$date_to'::date AND deviceid = ax.deviceid)) AS mdddevicename,
                            (SELECT COALESCE(serial_no, 'Absent') FROM {$this->schema}.master_device_details WHERE superdevid = ax.deviceid OR id = ax.deviceid) AS mddserialno 
                        FROM {$this->schema}.master_device_assign AS ax 
                        WHERE deviceid IN ($dids) AND group_id = 2 
                    ) masterdevice 
                    LEFT OUTER JOIN (
                        SELECT * 
                        FROM public.trip_spesified_device 
                        WHERE (genid, deviceid, result_date, acting_trip) IN (
                            SELECT MAX(genid), deviceid, result_date, acting_trip 
                            FROM public.trip_spesified_device 
                            WHERE deviceid IN ($dids) AND 
                            (result_date || ' ' || start_time >= '{$date_from} {$time_from}' AND result_date || ' ' || end_time <= '{$date_to} {$time_to}') 
                            GROUP BY deviceid, result_date, acting_trip
                        ) 
                        ORDER BY devicename, result_date, acting_trip
                    ) resultdivice ON masterdevice.mddd = resultdivice.deviceid
                ) resultset 
                ORDER BY result_date, start_time, mddd ASC")->getResult();
            
                // Optionally uncomment to see the last query for debugging
                // echo $this->db->getLastQuery(); exit;
            
                $html = view('traxreport/pdf_movementsummery', $data); // Use the view helper to load the view
                $filename = 'Movement_Summery_Report_' . $data['pwi_name'] . '_' . time();
            }
            if ($report_type == 10) {
                if ($this->sessdata['group_id'] == 2) {
                    $data['alldata'] = $this->db->query("SELECT * 
                        FROM public.get_divice_details_record_for_list('{$this->schema}', {$this->sessdata->user_id}) 
                        WHERE did IN ($dids) AND (issudate::date BETWEEN '{$date_from}' AND '{$date_to}')")->getResult();
                } else {
                    $data['alldata'] = $this->db->query("SELECT * 
                        FROM get_divice_details_record_for_list_for_company('{$this->schema}', {$this->sessdata->user_id}) 
                        WHERE did IN ($dids) AND (issudate::date BETWEEN '{$date_from}' AND '{$date_to}') AND sup_gid = 2")->getResult();
                }
            
                // Optionally uncomment to see the last query for debugging
                // echo $this->db->getLastQuery(); exit;
            
                $html = view('traxreport/pdf_allotment', $data); // Use the view helper to load the view
                $filename = 'Device_Allotment_Report_' . $data['pwi_name'] . '_' . time();
            }     
            if ($report_type == 11) {
                $result = $this->db->query("SELECT typeofuser, deviceid, devicename, sesectionstartttime, sesectionendttime, 
                    starttime, endtime, duration, startpoletime, startpole, endpointtime, endpoint, 
                    (SELECT device_name FROM {$this->schema}.master_device_setup 
                     WHERE deviceid = mdd.superdevid AND id = 
                        (SELECT MAX(id) FROM {$this->schema}.master_device_setup 
                         WHERE inserttime::date <= '$date_from'::date AND deviceid = mdd.superdevid)) AS mdddevicename, 
                    mdd.serial_no 
                    FROM public.analysish_work_patrol_time_schudele('$date_from') AS ax 
                    RIGHT JOIN {$this->schema}.master_device_details AS mdd 
                    ON (ax.deviceid = mdd.superdevid) 
                    WHERE mdd.superdevid IN ($dids)")->getResult();
            
                $deviceserial = '';
                $i = 0;
            
                foreach ($result as $result_each) {
                    if (strpos($result_each->mdddevicename, 'Patrolman') !== false) {
                        $data['alldata'][$i]->typeofuser = $result_each->typeofuser;
                        $data['alldata'][$i]->deviceid = $result_each->deviceid;
                        $data['alldata'][$i]->devicealiasname = $result_each->mdddevicename;
                        $data['alldata'][$i]->devicename = $result_each->serial_no;
                        $data['alldata'][$i]->sessiondate = $result_each->sessiondate;
            
                        $data['alldata'][$i]->schedulestarttime = date('d-m-Y H:i:s', strtotime($result_each->starttime));
                        $data['alldata'][$i]->scheduleendtime = date('d-m-Y H:i:s', strtotime($result_each->endtime));
            
                        $data['alldata'][$i]->startpoletime = ($result_each->startpoletime == '2000-10-01 00:00:00') ? 'NA' : date('d-m-Y H:i:s', strtotime($result_each->startpoletime));
                        $data['alldata'][$i]->endpointtime = ($result_each->endpointtime == '2000-10-01 00:00:00') ? 'NA' : date('d-m-Y H:i:s', strtotime($result_each->endpointtime));
            
                        $data['alldata'][$i]->startpole = $result_each->startpole;
                        $data['alldata'][$i]->endpole = $result_each->endpoint;
            
                        $i++;
                    }
                }
            
                // Optional debugging
                // echo $this->db->getLastQuery(); exit;
                // echo '<pre>'; print_r($data['alldata']); exit;
            
                $html = view('traxreport/pdf_trip_deviation', $data); // Use the view helper
                $filename = 'Trip_Deviation_Date_Wise_Report_' . $data['pwi_name'] . '_' . time();
            }                        
    
            // $this->makePdf->setFileName($filename);
            // $this->makePdf->setContent($html);
            // $this->makePdf->getPdf();

            // Instantiate the MakePDF class
            $pdf = new MakePDF();

            // Set the filename and content
            $pdf->setFileName($filename);
            $pdf->setContent($html);

            // Generate and stream the PDF to the browser
            $pdf->getPdf();  // true to stream the PDF
        }
    }    

    public function activitySummaryReport()
    {
        // Check if the user is logged in
        if (!session()->has('login_sess_data')) {
            return redirect('/');
        }
        
        // Initialize data array
        $data = [];
        $data['sessdata'] = $this->sessdata;

        if ($this->request->getPost()) {
            $user_id = $this->sessdata['user_id'];
            $group_id = $this->sessdata['group_id'];
            $date = date("Y-m-d");

            // Get the POST data
            $data = [
                'dt' => date("d-m-Y H:i:s", strtotime(trim($this->request->getPost('dt')))),
                'pwi_id' => trim($this->request->getPost('user')),
                'pwi_name' => trim($this->request->getPost('pwi_name')),
                'device_id' => $device_id = trim($this->request->getPost('device_id')),
                'typeofuser' => $typeofuser = trim($this->request->getPost('typeofuser')),
                'sse_pwy' => trim($this->request->getPost('pway_id')),
                'usertype' => trim($this->request->getPost('usertype'))
            ];

            $sse_pwy = trim($this->request->getPost('pway_id'));

            $pwi_id = $data['pwi_id'];
            // Initialize the devices and dids variables
            $devices = "";
            $dids = "";

            // If device_id is empty
            if(empty($device_id))
			{
				$devices .= "";
				if($data['pwi_name'] == 'All'){
					if($sse_pwy == 'All')
					{
						$query = "SELECT sup_pid ,sup_gid ,serial_no,imei_no,did,mobile_no,warranty_date,linked,parent_id,user_id,issudate,
							refunddate,active,issold,apply_scheam,group_id,role_id,email,address,pincode,state_name,country,username,firstname,
							lastname,organisation,group_name,'' as list_item, '' as list_item_name from public.get_divice_details_record_for_list('".$this->schema."',".$user_id.") where sup_gid = $group_id and user_id=".$user_id." and active = 1 order by did asc";
						$devicelist = $this->db->query($query)->getResult();
					}
					else
					{
						$sectionlist = $this->db->query("select user_id from public.user_login where parent_id ='".$sse_pwy."' and active=1")->getResult();
						$devicelist = array();
						for($q=0;$q<count($sectionlist);$q++)
						{
							$sectionid = $sectionlist[$q]->user_id;
							$query = "SELECT sup_pid ,sup_gid ,serial_no,imei_no,did,mobile_no,warranty_date,linked,parent_id,user_id,issudate,
					        refunddate,active,issold,apply_scheam,group_id,role_id,email,address,pincode,state_name,country,username,firstname,
					        lastname,organisation,group_name,'' as list_item, '' as list_item_name from public.get_divice_details_record_for_list('".$this->schema."',".$sectionid.") where user_id = ".$sectionid." and active = 1 order by issudate asc";
							$lists = $this->db->query($query)->getResult();
							for($z=0;$z<count($lists);$z++)
							{
								array_push($devicelist,$lists[$z]);
							}
						}
					}
				}
				else{
					$query = "SELECT sup_pid ,sup_gid ,serial_no,imei_no,did,mobile_no,warranty_date,linked,parent_id,user_id,issudate,
					refunddate,active,issold,apply_scheam,group_id,role_id,email,address,pincode,state_name,country,username,firstname,
					lastname,organisation,group_name,'' as list_item, '' as list_item_name from public.get_divice_details_record_for_list('".$this->schema."',".$pwi_id.") where user_id = ".$pwi_id." and active = 1 order by issudate asc";
					$devicelist = $this->db->query($query)->getResult();
				}
				if(count($devicelist)>0){
					foreach($devicelist as $devicelist_each){
						if($devices == ""){
							$devices .= $devicelist_each->did;
							$dids .= $devicelist_each->did;
						}
						else{
							$devices .= ",".$devicelist_each->did;
							$dids .= ",".$devicelist_each->did;
						}
						
					}
				}
				$devices_arr = explode(',',$devices);
			}
			else
			{
				$devices_arr[] = $device_id;
			}
			$new_devices_arr = array();
			if($typeofuser != 'All')
			{
				for($aw=0;$aw<count($devices_arr);$aw++)
				{
					$device_id = $devices_arr[$aw];
					if($device_id != '')
					{
						$device_name_details = $this->db->query("select device_name FROM {$this->schema}.master_device_setup where deviceid = ".$device_id."")->getResult();
						$device_name = $device_name_details[0]->device_name;
						$device_name_arr = explode('/',$device_name);
						$user_type = $device_name_arr[0];
						if(strtoupper($user_type) == strtoupper($typeofuser))
						{
							array_push($new_devices_arr,$devices_arr[$aw]);
						}
					}
				}
			}
			else
			{
				for($aw=0;$aw<count($devices_arr);$aw++)
				{
					array_push($new_devices_arr,$devices_arr[$aw]);
				}
			}
            $report_data = [];
			for($dv=0;$dv<count($new_devices_arr);$dv++)
			{
				$device_id = $new_devices_arr[$dv];
                // echo $device_id."<pre>";
				if($device_id != '')
				{
					$device_name_details = $this->db->query("SELECT device_name FROM {$this->schema}.master_device_setup where deviceid = ".$device_id."")->getResult();
                    if(empty($device_name_details)) {
                        $device_name = 'N/A';
                    } else {
                        $device_name = $device_name_details[0]->device_name;
                    }
					
					$device_name_arr = explode('/',$device_name);
					$user_type = $device_name_arr[0];
                    // echo $user_type."<pre>";
					$assignment_details = $this->db->query("SELECT count(*) as counter  FROM public.master_device_assign where deviceid='".$device_id."' and group_id=2 and active = 1")->getResult();
					$counter = $assignment_details[0]->counter;
					if($counter > 0)
					{
						if(strtoupper($user_type) == 'PATROLMAN') {
                            // echo 1;
							$dt = date("d-m-Y H:i:s", strtotime(trim($this->request->getPost('dt'))));
							$date_from = date("Y-m-d", strtotime('-1 day',strtotime(trim($dt)))).' 22:00:00';
							$date_from1 = date("Y-m-d", strtotime('-1 day',strtotime(trim($dt)))).' 23:59:59';
							// $date_from = date("Y-m-d", strtotime(trim($dt))).' 09:00:00';
							// $date_to = date("Y-m-d", strtotime(trim($dt))).' 17:00:00';
							$data = $this->db->query("SELECT a.*, mdd.serial_no,msd.device_name from public.get_histry_play_data_summary('".$device_id."', '".$date_from."'::timestamp without time zone, '".$date_from1."'::timestamp without time zone) as a left join {$this->schema}.master_device_details as mdd on (mdd.superdevid = a.deviceid) left join {$this->schema}.master_device_setup as msd on (msd.deviceid = a.deviceid)")->getResult();
                            // echo "<pre>";print_r($data);//exit();
							$date_to = date('Y-m-d', strtotime($dt)).' 00:00:00';
							$date_to1 = date('Y-m-d', strtotime($dt)).' 08:59:59';
							$data1 = $this->db->query("select a.*, mdd.serial_no,msd.device_name from public.get_histry_play_data_summary('".$device_id."', '".$date_to."'::timestamp without time zone, '".$date_to1."'::timestamp without time zone) as a left join {$this->schema}.master_device_details as mdd on (mdd.superdevid = a.deviceid) left join {$this->schema}.master_device_setup as msd on (msd.deviceid = a.deviceid)")->getResult();
							$length = count($report_data);
							if(count($data) > 0 && count($data1))
							// if(count($data) > 0)
							{					
								$serialno = $data[0]->serial_no;
								$device_assign_details = $this->db->query("SELECT a.parent_user_id,a.current_user_id,b.organisation as pwy,c.organisation as section
												from public.device_asign_details as a
												left join public.user_login as b on (a.parent_user_id = b.user_id)
												left join public.user_login as c on (a.current_user_id = c.user_id)
												where a.serial_no='".$serialno."'")->getResult();
								$report_data[$length][0]['pwy'] = $device_assign_details[0]->pwy;
								$report_data[$length][0]['section'] = $device_assign_details[0]->section;
								$report_data[$length][0]['result_date'] = $data[0]->result_date;
								$report_data[$length][0]['deviceid'] = $data[0]->deviceid;
								$report_data[$length][0]['user_type'] = $user_type;
								$report_data[$length][0]['parent_id'] = $data[0]->parent_id;
								$report_data[$length][0]['user_id'] = $data[0]->user_id;
								$report_data[$length][0]['group_id'] = $data[0]->group_id;
								$report_data[$length][0]['start_time'] = $data[0]->result_date." ".$data[0]->start_time;
								//$report_data[$length][0]['end_time'] = $data1[0]->result_date." ".$data1[0]->end_time;
								$report_data[$length][0]['end_time'] = $data[0]->result_date." ".$data[0]->end_time;							
								//$newduration = $this->db->query("select age('".$data1[0]->result_date." ".$data1[0]->end_time."','".$data[0]->result_date." ".$data[0]->start_time."') as duration")->result();
								$newduration = $this->db->query("SELECT age('".$data[0]->result_date." ".$data[0]->end_time."','".$data[0]->result_date." ".$data[0]->start_time."') as duration")->getResult();						
								$report_data[$length][0]['duration'] = $newduration[0]->duration;
								$report_data[$length][0]['distance_cover'] = $data[0]->distance_cover+$data1[0]->distance_cover;
								$report_data[$length][0]['sos_no'] = 0;
								$report_data[$length][0]['alert_no'] = 0;
								$report_data[$length][0]['call_no'] = 0;
								$report_data[$length][0]['serial_no'] = $data[0]->serial_no;
								$report_data[$length][0]['device_name'] = $data[0]->device_name;
								$organisation = $this->db->query("SELECT organisation from public.user_login where user_id = {$data[0]->user_id} and active = 1")->getRow();
								$report_data[$length][0]['organisation'] = $organisation->organisation;
								$PWI = explode("(",$data[0]->device_name);
								$newPwI = '';
								for($a=1;$a<count($PWI);$a++)
								{
									$newPwI = $newPwI.$PWI[$a];
								}
								$newPwI = '('.$newPwI;
								$report_data[$length][0]['newPwI'] = $newPwI;
                                $devicename = $data[0]->device_name;
								$devicenameArr = explode(':',$devicename);
								$poledetailsnew = trim($devicenameArr[1]);
								$poledetailsnewArr = explode('(',$poledetailsnew);
								$polenamenew = trim($poledetailsnewArr[1]);
								$polenamenew = str_replace(')','',$polenamenew);
								$polenamenewArr = explode('-',$polenamenew);
								$report_data[$length][0]['startpole'] = $polenamenewArr[0];
								$report_data[$length][0]['stoppol'] = $polenamenewArr[1];
								$report_data[$length][0]['bit'] = $report_data[$length][0]['startpole'].'-'.$report_data[$length][0]['stoppol'];
								
								
								$timedetails = $this->db->query("select walk_org_distance,starttime,endtime,justify_interval(endtime - starttime) as durationorgtime
								from {$this->schema}.device_assigne_pole_data where deviceid='".$device_id."' and walk_org_distance is not null")->getResult();
                                if(!empty($timedetails) && count($timedetails) > 0) {
                                    $report_data[$length][0]['walk_org_distance_out'] = $timedetails[0]->walk_org_distance;
                                    $report_data[$length][0]['starttime_org_out'] = $timedetails[0]->starttime;
                                    $report_data[$length][0]['endtime_org_out'] = $timedetails[0]->endtime;
                                    $report_data[$length][0]['durationorgtime_org_out'] = abs(intval($timedetails[0]->durationorgtime));
                                    $actual_distance = (float)$report_data[$length][0]['distance_cover'];								
                                    $original_distance = (float)$timedetails[0]->walk_org_distance*1000;
                                    $report_data[$length][0]['deviation_distance'] = $deviation_distance = $actual_distance - $original_distance;
                                    if($actual_distance > $original_distance && $original_distance != '')
                                    {
                                        $report_data[$length][0]['distance_status'] = 'No';
                                    }
                                    else if($this->hasMinusSign($deviation_distance))
                                    {
                                        $report_data[$length][0]['distance_status'] = 'Yes';
                                    }
                                    else if($deviation_distance > 0)
                                    {
                                        $report_data[$length][0]['distance_status'] = 'Yes';
                                    }
                                    else
                                    {
                                        $report_data[$length][0]['distance_status'] = 'No';
                                    }
                                    if((isset($timedetails[0]->starttime_org_out) && $timedetails[0]->starttime_org_out != '') && (isset($timedetails[0]->endtime_org_out) && $timedetails[0]->endtime_org_out != ''))
                                    {
                                        if($this->convertsecond($report_data[$length][0]['durationorgtime_org_out']) > $this->convertsecond($report_data[$length][0]['duration']))
                                        {
                                            $report_data[$length][0]['time_status'] = 'Yes';
                                        }
                                        else
                                        {
                                            $report_data[$length][0]['time_status'] = 'No';
                                        }
                                    }
                                    else
                                    {
                                        $report_data[$length][0]['time_status'] = 'Yes';
                                    }
                                }

								$avg_speed1 = $data[0]->avg_speed ?? 0;
								$avg_speed2 = $data1[0]->avg_speed ?? 0;
								$avg_speed = ($avg_speed1+$avg_speed2)/2;
								$max_speed = $data[0]->max_speed ?? 0;
                                $max_speed1 = $data1[0]->max_speed ?? 0;
								if($max_speed1 > $max_speed)
								{
									$max_speed = $max_speed1;
								}
								$report_data[$length][0]['avg_speed'] = $avg_speed;
								$report_data[$length][0]['max_speed'] = $max_speed;
							}
						}
						else {
                            // echo 2;
							$dt = date("d-m-Y H:i:s", strtotime(trim($this->request->getPost('dt'))));
							/*$date_from = date('Y-m-d', strtotime($dt)).' 00:00:00';
							$date_to = date('Y-m-d H:i:s', strtotime($dt));*/
							$date_from = date('Y-m-d', strtotime($dt)).' 00:00:00';
							$date_to = date('Y-m-d', strtotime($dt)).' 16:00:00';
							// echo $date_to;exit;
							// $date_to = date('Y-m-d', strtotime($dt)).' 23:59:59';
							$data = $this->db->query("select a.*, mdd.serial_no,msd.device_name from public.get_histry_play_data_summary('".$device_id."', '".$date_from."'::timestamp without time zone, '".$date_to."'::timestamp without time zone) as a left join {$this->schema}.master_device_details as mdd on (mdd.superdevid = a.deviceid) left join {$this->schema}.master_device_setup as msd on (msd.deviceid = a.deviceid)")->getResult();
							$length = count($report_data);
							if(count($data) > 0)
							{							
								$serialno = $data[0]->serial_no;
								$device_assign_details = $this->db->query("select a.parent_user_id,a.current_user_id,b.organisation as pwy,c.organisation as section
												from public.device_asign_details as a
												left join public.user_login as b on (a.parent_user_id = b.user_id)
												left join public.user_login as c on (a.current_user_id = c.user_id)
												where a.serial_no='".$serialno."'")->getResult();
								$report_data[$length][0]['pwy'] = $device_assign_details[0]->pwy;
								$report_data[$length][0]['section'] = $device_assign_details[0]->section;
								$report_data[$length][0]['result_date'] = $data[0]->result_date;
								$report_data[$length][0]['deviceid'] = $data[0]->deviceid;
								$report_data[$length][0]['user_type'] = $user_type;
								$report_data[$length][0]['parent_id'] = $data[0]->parent_id;
								$report_data[$length][0]['user_id'] = $data[0]->user_id;
								$report_data[$length][0]['group_id'] = $data[0]->group_id;
								$report_data[$length][0]['start_time'] = $data[0]->result_date." ".$data[0]->start_time;
								$report_data[$length][0]['end_time'] = $data[0]->result_date." ".$data[0]->end_time;
								$report_data[$length][0]['duration'] = $data[0]->duration;
								$report_data[$length][0]['distance_cover'] = $data[0]->distance_cover;
								$report_data[$length][0]['sos_no'] = 0;
								$report_data[$length][0]['alert_no'] = 0;
								$report_data[$length][0]['call_no'] = 0;
								$report_data[$length][0]['serial_no'] = $data[0]->serial_no;
								$report_data[$length][0]['device_name'] = $data[0]->device_name;
								$organisation = $this->db->query("select organisation from public.user_login where user_id = {$data[0]->user_id} and active = 1")->getRow();
								$report_data[$length][0]['organisation'] = $organisation->organisation;
								
								
								$PWI = explode("(",$data[0]->device_name);
								$newPwI = '';
								for($a=1;$a<count($PWI);$a++)
								{
									$newPwI = $newPwI.$PWI[$a];
								}
								$newPwI = '('.$newPwI;
								$report_data[$length][0]['newPwI'] = $newPwI;

                                $devicename = $data[0]->device_name;
								$devicenameArr = explode(':',$devicename);
								/*$poledetailsnew = trim($devicenameArr[1]);
								$poledetailsnewArr = explode('(',$poledetailsnew);
								$polenamenew = trim($poledetailsnewArr[1]);
								$polenamenew = str_replace(')','',$polenamenew);
								$polenamenewArr = explode('-',$polenamenew);
								$report_data[$length][0]['startpole'] = $polenamenewArr[0];
								$report_data[$length][0]['stoppol'] = $polenamenewArr[1];
								$report_data[$length][0]['bit'] = $report_data[$length][0]['startpole'].'-'.$report_data[$length][0]['stoppol'];*/

                                if(count($devicenameArr) > 1) {
                                    $poledetailsnew = trim($devicenameArr[1]);
                                    $poledetailsnewArr = explode('(', $poledetailsnew);
                                    $polenamenew = trim($poledetailsnewArr[1]);
                                    $polenamenew = str_replace(')', '', $polenamenew);
                                    $polenamenewArr = explode('-', $polenamenew);
                                    $report_data[$length][0]['startpole'] = $polenamenewArr[0];
                                    $report_data[$length][0]['stoppol'] = $polenamenewArr[1];
                                    $report_data[$length][0]['bit'] = $report_data[$length][0]['startpole'].'-'.$report_data[$length][0]['stoppol'];
                                } else {
                                    $report_data[$length][0]['startpole'] = '';
                                    $report_data[$length][0]['stoppol'] = '';
                                    $report_data[$length][0]['bit'] = '';
                                }
								
								$timedetails = $this->db->query("select walk_org_distance,starttime,endtime,justify_interval(endtime - starttime) as durationorgtime
								from {$this->schema}.device_assigne_pole_data where deviceid='".$device_id."'
								and endtime is not null and starttime is not null")->getResult();
                                if(!empty($timedetails) && count($timedetails) > 0) {
                                    $report_data[$length][0]['walk_org_distance_out'] = $timedetails[0]->walk_org_distance;
                                    $report_data[$length][0]['starttime_org_out'] = $timedetails[0]->starttime;
                                    $report_data[$length][0]['endtime_org_out'] = $timedetails[0]->endtime;
                                    $report_data[$length][0]['durationorgtime_org_out'] = abs(intval($timedetails[0]->durationorgtime));
                                    $actual_distance = (float)$report_data[$length][0]['distance_cover'];								
                                    $original_distance = (float)$timedetails[0]->walk_org_distance*1000;
                                    $report_data[$length][0]['deviation_distance'] = $deviation_distance = $actual_distance - $original_distance;
                                    if($actual_distance > $original_distance && $original_distance != '')
                                    {
                                        $report_data[$length][0]['distance_status'] = 'No';
                                    }
                                    else if($this->hasMinusSign($deviation_distance))
                                    {
                                        $report_data[$length][0]['distance_status'] = 'Yes';
                                    }
                                    else if($deviation_distance > 0)
                                    {
                                        $report_data[$length][0]['distance_status'] = 'Yes';
                                    }
                                    else
                                    {
                                        $report_data[$length][0]['distance_status'] = 'No';
                                    }
                                    
                                    if((isset($timedetails[0]->starttime_org_out) && $timedetails[0]->starttime_org_out != '') && (isset($timedetails[0]->endtime_org_out) && $timedetails[0]->endtime_org_out != ''))
                                    {
                                        if($this->convertsecond($report_data[$length][0]['durationorgtime_org_out']) > $this->convertsecond($report_data[$length][0]['duration']))
                                        {
                                            $report_data[$length][0]['time_status'] = 'Yes';
                                        }
                                        else
                                        {
                                            $report_data[$length][0]['time_status'] = 'No';
                                        }
                                    }
                                    else
                                    {
                                        $report_data[$length][0]['time_status'] = 'Yes';
                                    }
                                }
								$avg_speed1 = $data[0]->avg_speed ?? 0;
								$avg_speed2 = $data1[0]->avg_speed ?? 0;
								$avg_speed = ($avg_speed1+$avg_speed2)/2;
								$max_speed = $data[0]->max_speed ?? 0;
                                $max_speed1 = $data1[0]->max_speed ?? 0;
								if($max_speed1 > $max_speed)
								{
									$max_speed = $max_speed1;
								}
								$report_data[$length][0]['avg_speed'] = $avg_speed;
								$report_data[$length][0]['max_speed'] = $max_speed;
							}
							//echo "<pre>";print_r($report_data);echo "</pre>";exit;
						}
					}
				}
			}

            // echo"<pre>report_data=>";print_r($report_data);

            // exit();

            // Assign the report data to the view
            $data['report_data'] = $report_data;

            // echo "<pre>data-report_data=";print_r($data['report_data']);exit();

            if(!empty($report_data)) {
                $data['map_device_id'] = $report_data[0][0]['deviceid'];
                if($report_data[0][0]['user_type'] == 'Patrolman')
                {
                    $startdetails = $report_data[0][0]['start_time'];
                    $startdetailsarr = explode(' ',$startdetails);				
                    $data['map_start_date'] = $startdetailsarr[0];
                    $starttimedetailsarr = explode(":",$startdetailsarr[1]);
                    if(count($starttimedetailsarr) > 1) {
                        $start_time = $starttimedetailsarr[0].":".$starttimedetailsarr[1];
                    } else {
                        $start_time = "00:00";
                    }
                    
                    $data['map_start_time'] = $start_time;
                    $enddetails = $report_data[0][0]['end_time'];
                    $enddetailsarr = explode(' ',$enddetails);				
                    $data['map_end_date'] = $enddetailsarr[0];
                    $enddetailsarrarr = explode(":",$enddetailsarr[1]);
                    if(count($enddetailsarrarr) > 1) {
                        $end_time = $enddetailsarrarr[0].":".$enddetailsarrarr[1];
                    } else {
                        $end_time = "00:00";
                    }
                    $data['map_end_time'] = $end_time;
                }
                else
                {
                    $startdetails = $report_data[0][0]['start_time'];
                    $startdetailsarr = explode(' ',$startdetails);				
                    $data['map_start_date'] = $startdetailsarr[0];
                    $starttimedetailsarr = explode(":",$startdetailsarr[1]);
                    $start_time = $starttimedetailsarr[0].":".$starttimedetailsarr[1];
                    $data['map_start_time'] = $start_time;
                    $enddetails = $report_data[0][0]['end_time'];
                    $enddetailsarr = explode(' ',$enddetails);				
                    $data['map_end_date'] = $enddetailsarr[0];
                    $enddetailsarrarr = explode(":",$enddetailsarr[1]);
                    $end_time = $enddetailsarrarr[0].":".$enddetailsarrarr[1];
                    $data['map_end_time'] = $end_time;
                }
            }
        }

        // Determine if the user is a distributor
        if ($this->sessdata['group_id'] == 3) {
            // Distributor logic
            $data['devicedropdown'] = $this->db->query("SELECT a.*, 
                (SELECT device_name FROM {$this->schema}.master_device_setup  
                 WHERE id = (SELECT max(id) 
                             FROM {$this->schema}.master_device_setup 
                             WHERE inserttime::date <= current_date::date  
                             AND deviceid = a.did)) 
                AS device_name 
                FROM public.get_divice_details_record_for_list_for_company('{$this->schema}',{$this->sessdata['user_id']}) AS a 
                WHERE a.group_id = 2 AND a.active = 1")->getResult();
        } else {
            // Other user logic
            $data['devicedropdown'] = $this->db->query("SELECT a.*, 
                (SELECT device_name FROM {$this->schema}.master_device_setup  
                 WHERE id = (SELECT max(id) 
                             FROM {$this->schema}.master_device_setup 
                             WHERE inserttime::date <= current_date::date  
                             AND deviceid = a.did)) 
                AS device_name 
                FROM public.get_divice_details_record_for_list_for_company('{$this->schema}',{$this->sessdata['user_id']}) AS a 
                WHERE a.group_id = 2 AND a.active = 1")->getResult();
        }

        // Get users
        $data['usersdd'] = $this->commonModel->get_users();

        // Handle form inputs
        $data['dt'] = $this->request->getPost('dt') ? date("d-m-Y", strtotime(trim($this->request->getPost('dt')))) : date("d-m-Y");
        $data['pwi_id'] = trim($this->request->getPost('user'));
        $data['pwi_name'] = trim($this->request->getPost('pwi_name'));
        $data['device_id'] = trim($this->request->getPost('device_id'));
        $data['typeofuser'] = trim($this->request->getPost('typeofuser'));
        $data['usertype'] = trim($this->request->getPost('usertype'));
        $data['page_title'] = "Activity Summary Report";

        // Get PWAY users
        $data['pway'] = $this->db->query("SELECT organisation, user_id 
                                          FROM public.user_login 
                                          WHERE active = 1 AND group_id = 8")->getResult();

        // Handle PWAY ID
        $data['sse_pwy'] = trim($this->request->getPost('pway_id'));

        // Load the view
        $data['middle'] = view('traxreport/activitysummeryreport1', $data);
        return view('mainlayout', $data);
    }

    public function activitySummaryReportExcel()
    {
        // Check if the user is logged in
        if (!session()->has('login_sess_data')) {
            return redirect('/');
        }
        
        // Initialize data array
        $data = [];
        $data['sessdata'] = $this->sessdata;

        if ($this->request->getPost()) {
            $user_id = $this->sessdata['user_id'];
            $group_id = $this->sessdata['group_id'];
            $date = date("Y-m-d");

            // Get the POST data
            $data = [
                'dt' => date("d-m-Y H:i:s", strtotime(trim($this->request->getPost('dt')))),
                'pwi_id' => trim($this->request->getPost('user')),
                'pwi_name' => trim($this->request->getPost('pwi_name')),
                'device_id' => $device_id = trim($this->request->getPost('device_id')),
                'typeofuser' => $typeofuser = trim($this->request->getPost('typeofuser')),
                'sse_pwy' => trim($this->request->getPost('pway_id'))
            ];

            // echo "<pre>";print_r($data);exit();

            $sse_pwy = trim($this->request->getPost('pway_id'));

            $pwi_id = $data['pwi_id'];
            // Initialize the devices and dids variables
            $devices = "";
            $dids = "";

            // If device_id is empty
            if(empty($device_id))
			{
				$devices .= "";
				if($data['pwi_name'] == 'All'){
					if($sse_pwy == 'All')
					{
						$query = "SELECT sup_pid ,sup_gid ,serial_no,imei_no,did,mobile_no,warranty_date,linked,parent_id,user_id,issudate,
							refunddate,active,issold,apply_scheam,group_id,role_id,email,address,pincode,state_name,country,username,firstname,
							lastname,organisation,group_name,'' as list_item, '' as list_item_name from public.get_divice_details_record_for_list('".$this->schema."',".$user_id.") where sup_gid = $group_id and user_id=".$user_id." and active = 1 order by did asc";
						$devicelist = $this->db->query($query)->getResult();
					}
					else
					{
						$sectionlist = $this->db->query("select user_id from public.user_login where parent_id ='".$sse_pwy."' and active=1")->getResult();
						$devicelist = array();
						for($q=0;$q<count($sectionlist);$q++)
						{
							$sectionid = $sectionlist[$q]->user_id;
							$query = "SELECT sup_pid ,sup_gid ,serial_no,imei_no,did,mobile_no,warranty_date,linked,parent_id,user_id,issudate,
					        refunddate,active,issold,apply_scheam,group_id,role_id,email,address,pincode,state_name,country,username,firstname,
					        lastname,organisation,group_name,'' as list_item, '' as list_item_name from public.get_divice_details_record_for_list('".$this->schema."',".$sectionid.") where user_id = ".$sectionid." and active = 1 order by issudate asc";
							$lists = $this->db->query($query)->getResult();
							for($z=0;$z<count($lists);$z++)
							{
								array_push($devicelist,$lists[$z]);
							}
						}
					}
				}
				else{
					$query = "SELECT sup_pid ,sup_gid ,serial_no,imei_no,did,mobile_no,warranty_date,linked,parent_id,user_id,issudate,
					refunddate,active,issold,apply_scheam,group_id,role_id,email,address,pincode,state_name,country,username,firstname,
					lastname,organisation,group_name,'' as list_item, '' as list_item_name from public.get_divice_details_record_for_list('".$this->schema."',".$pwi_id.") where user_id = ".$pwi_id." and active = 1 order by issudate asc";
					$devicelist = $this->db->query($query)->getResult();
				}
				if(count($devicelist)>0){
					foreach($devicelist as $devicelist_each){
						if($devices == ""){
							$devices .= $devicelist_each->did;
							$dids .= $devicelist_each->did;
						}
						else{
							$devices .= ",".$devicelist_each->did;
							$dids .= ",".$devicelist_each->did;
						}
						
					}
				}
				$devices_arr = explode(',',$devices);
			}
			else
			{
				$devices_arr[] = $device_id;
			}
			$new_devices_arr = array();
			if($typeofuser != 'All')
			{
				for($aw=0;$aw<count($devices_arr);$aw++)
				{
					$device_id = $devices_arr[$aw];
					if($device_id != '')
					{
						$device_name_details = $this->db->query("select device_name FROM {$this->schema}.master_device_setup where deviceid = ".$device_id."")->getResult();
						$device_name = $device_name_details[0]->device_name;
						$device_name_arr = explode('/',$device_name);
						$user_type = $device_name_arr[0];
						if(strtoupper($user_type) == strtoupper($typeofuser))
						{
							array_push($new_devices_arr,$devices_arr[$aw]);
						}
					}
				}
			}
			else
			{
				for($aw=0;$aw<count($devices_arr);$aw++)
				{
					array_push($new_devices_arr,$devices_arr[$aw]);
				}
			}
            $report_data = [];
			for($dv=0;$dv<count($new_devices_arr);$dv++)
			{
				$device_id = $new_devices_arr[$dv];
                // echo $device_id."<pre>";
				if($device_id != '')
				{
					$device_name_details = $this->db->query("SELECT device_name FROM {$this->schema}.master_device_setup where deviceid = ".$device_id."")->getResult();
                    if(empty($device_name_details)) {
                        $device_name = 'N/A';
                    } else {
                        $device_name = $device_name_details[0]->device_name;
                    }
					
					$device_name_arr = explode('/',$device_name);
					$user_type = $device_name_arr[0];
                    // echo $user_type."<pre>";
					$assignment_details = $this->db->query("SELECT count(*) as counter  FROM public.master_device_assign where deviceid='".$device_id."' and group_id=2 and active = 1")->getResult();
					$counter = $assignment_details[0]->counter;
					if($counter > 0)
					{
						if(strtoupper($user_type) == 'PATROLMAN') {
                            // echo 1;
							$dt = date("d-m-Y H:i:s", strtotime(trim($this->request->getPost('dt'))));
							$date_from = date("Y-m-d", strtotime('-1 day',strtotime(trim($dt)))).' 22:00:00';
							$date_from1 = date("Y-m-d", strtotime('-1 day',strtotime(trim($dt)))).' 23:59:59';
							// $date_from = date("Y-m-d", strtotime(trim($dt))).' 09:00:00';
							// $date_to = date("Y-m-d", strtotime(trim($dt))).' 17:00:00';
							$data = $this->db->query("SELECT a.*, mdd.serial_no,msd.device_name from public.get_histry_play_data_summary('".$device_id."', '".$date_from."'::timestamp without time zone, '".$date_from1."'::timestamp without time zone) as a left join {$this->schema}.master_device_details as mdd on (mdd.superdevid = a.deviceid) left join {$this->schema}.master_device_setup as msd on (msd.deviceid = a.deviceid)")->getResult();
                            // echo "<pre>";print_r($data);//exit();
							$date_to = date('Y-m-d', strtotime($dt)).' 00:00:00';
							$date_to1 = date('Y-m-d', strtotime($dt)).' 08:59:59';
							$data1 = $this->db->query("select a.*, mdd.serial_no,msd.device_name from public.get_histry_play_data_summary('".$device_id."', '".$date_to."'::timestamp without time zone, '".$date_to1."'::timestamp without time zone) as a left join {$this->schema}.master_device_details as mdd on (mdd.superdevid = a.deviceid) left join {$this->schema}.master_device_setup as msd on (msd.deviceid = a.deviceid)")->getResult();
							$length = count($report_data);
							if(count($data) > 0 && count($data1))
							// if(count($data) > 0)
							{					
								$serialno = $data[0]->serial_no;
								$device_assign_details = $this->db->query("SELECT a.parent_user_id,a.current_user_id,b.organisation as pwy,c.organisation as section
												from public.device_asign_details as a
												left join public.user_login as b on (a.parent_user_id = b.user_id)
												left join public.user_login as c on (a.current_user_id = c.user_id)
												where a.serial_no='".$serialno."'")->getResult();
								$report_data[$length][0]['pwy'] = $device_assign_details[0]->pwy;
								$report_data[$length][0]['section'] = $device_assign_details[0]->section;
								$report_data[$length][0]['result_date'] = $data[0]->result_date;
								$report_data[$length][0]['deviceid'] = $data[0]->deviceid;
								$report_data[$length][0]['user_type'] = $user_type;
								$report_data[$length][0]['parent_id'] = $data[0]->parent_id;
								$report_data[$length][0]['user_id'] = $data[0]->user_id;
								$report_data[$length][0]['group_id'] = $data[0]->group_id;
								$report_data[$length][0]['start_time'] = $data[0]->result_date." ".$data[0]->start_time;
								//$report_data[$length][0]['end_time'] = $data1[0]->result_date." ".$data1[0]->end_time;
								$report_data[$length][0]['end_time'] = $data[0]->result_date." ".$data[0]->end_time;							
								//$newduration = $this->db->query("select age('".$data1[0]->result_date." ".$data1[0]->end_time."','".$data[0]->result_date." ".$data[0]->start_time."') as duration")->result();
								$newduration = $this->db->query("SELECT age('".$data[0]->result_date." ".$data[0]->end_time."','".$data[0]->result_date." ".$data[0]->start_time."') as duration")->getResult();						
								$report_data[$length][0]['duration'] = $newduration[0]->duration;
								$report_data[$length][0]['distance_cover'] = $data[0]->distance_cover+$data1[0]->distance_cover;
								$report_data[$length][0]['sos_no'] = 0;
								$report_data[$length][0]['alert_no'] = 0;
								$report_data[$length][0]['call_no'] = 0;
								$report_data[$length][0]['serial_no'] = $data[0]->serial_no;
								$report_data[$length][0]['device_name'] = $data[0]->device_name;
								$organisation = $this->db->query("SELECT organisation from public.user_login where user_id = {$data[0]->user_id} and active = 1")->getRow();
								$report_data[$length][0]['organisation'] = $organisation->organisation;
								$PWI = explode("(",$data[0]->device_name);
								$newPwI = '';
								for($a=1;$a<count($PWI);$a++)
								{
									$newPwI = $newPwI.$PWI[$a];
								}
								$newPwI = '('.$newPwI;
								$report_data[$length][0]['newPwI'] = $newPwI;
							}
						}
						else {
                            // echo 2;
							$dt = date("d-m-Y H:i:s", strtotime(trim($this->request->getPost('dt'))));
							/*$date_from = date('Y-m-d', strtotime($dt)).' 00:00:00';
							$date_to = date('Y-m-d H:i:s', strtotime($dt));*/
							$date_from = date('Y-m-d', strtotime($dt)).' 00:00:00';
							$date_to = date('Y-m-d', strtotime($dt)).' 16:00:00';
							// echo $date_to;exit;
							// $date_to = date('Y-m-d', strtotime($dt)).' 23:59:59';
							$data = $this->db->query("select a.*, mdd.serial_no,msd.device_name from public.get_histry_play_data_summary('".$device_id."', '".$date_from."'::timestamp without time zone, '".$date_to."'::timestamp without time zone) as a left join {$this->schema}.master_device_details as mdd on (mdd.superdevid = a.deviceid) left join {$this->schema}.master_device_setup as msd on (msd.deviceid = a.deviceid)")->getResult();
							$length = count($report_data);
							if(count($data) > 0)
							{							
								$serialno = $data[0]->serial_no;
								$device_assign_details = $this->db->query("select a.parent_user_id,a.current_user_id,b.organisation as pwy,c.organisation as section
												from public.device_asign_details as a
												left join public.user_login as b on (a.parent_user_id = b.user_id)
												left join public.user_login as c on (a.current_user_id = c.user_id)
												where a.serial_no='".$serialno."'")->getResult();
								$report_data[$length][0]['pwy'] = $device_assign_details[0]->pwy;
								$report_data[$length][0]['section'] = $device_assign_details[0]->section;
								$report_data[$length][0]['result_date'] = $data[0]->result_date;
								$report_data[$length][0]['deviceid'] = $data[0]->deviceid;
								$report_data[$length][0]['user_type'] = $user_type;
								$report_data[$length][0]['parent_id'] = $data[0]->parent_id;
								$report_data[$length][0]['user_id'] = $data[0]->user_id;
								$report_data[$length][0]['group_id'] = $data[0]->group_id;
								$report_data[$length][0]['start_time'] = $data[0]->result_date." ".$data[0]->start_time;
								$report_data[$length][0]['end_time'] = $data[0]->result_date." ".$data[0]->end_time;
								$report_data[$length][0]['duration'] = $data[0]->duration;
								$report_data[$length][0]['distance_cover'] = $data[0]->distance_cover;
								$report_data[$length][0]['sos_no'] = 0;
								$report_data[$length][0]['alert_no'] = 0;
								$report_data[$length][0]['call_no'] = 0;
								$report_data[$length][0]['serial_no'] = $data[0]->serial_no;
								$report_data[$length][0]['device_name'] = $data[0]->device_name;
								$organisation = $this->db->query("select organisation from public.user_login where user_id = {$data[0]->user_id} and active = 1")->getRow();
								$report_data[$length][0]['organisation'] = $organisation->organisation;
								
								
								$PWI = explode("(",$data[0]->device_name);
								$newPwI = '';
								for($a=1;$a<count($PWI);$a++)
								{
									$newPwI = $newPwI.$PWI[$a];
								}
								$newPwI = '('.$newPwI;
								$report_data[$length][0]['newPwI'] = $newPwI;
							}
							//echo "<pre>";print_r($report_data);echo "</pre>";exit;
						}
					}
				}
			}

            // echo"<pre>report_data=>";print_r($report_data);

            // exit();

            // Assign the report data to the view
            $data['report_data'] = $report_data;

            $dat[0]['A'] = "Date";
            $dat[0]['B'] = "Device ID";
            $dat[0]['C'] = "DeviceName";
            $dat[0]['D'] = "BIT";
            $dat[0]['E'] = "SSE/PWY";
            $dat[0]['F'] = "Section";
            $dat[0]['G'] = "User Type";
            $dat[0]['H'] = "Start Date Time";
            $dat[0]['I'] = "End Date Time";
            $dat[0]['J'] = "Travelled Distance(KM)";
            $dat[0]['K'] = "Total Call";
            $dat[0]['L'] = "Total SOS";
            $Key = 1;
            foreach($report_data as $report_data_each){
                $dat[$Key+1]['A'] = date("d-m-Y", strtotime($report_data_each[0]['result_date']));
                $dat[$Key+1]['B'] = $report_data_each[0]['serial_no'];
                $dat[$Key+1]['C'] = $report_data_each[0]['device_name'];
                $dat[$Key+1]['D'] = $report_data_each[0]['newPwI'];
                $dat[$Key+1]['E'] = $report_data_each[0]['pwy'];
                $dat[$Key+1]['F'] = $report_data_each[0]['organisation'];
                $dat[$Key+1]['G'] = $report_data_each[0]['user_type'];
                $dat[$Key+1]['H'] = $report_data_each[0]['start_time'];
                $dat[$Key+1]['I'] = $report_data_each[0]['end_time'];
                $dat[$Key+1]['J'] = round($report_data_each[0]['distance_cover']/1000).' km';
                $dat[$Key+1]['K'] = $report_data_each[0]['call_no'];
                $dat[$Key+1]['L'] = $report_data_each[0]['sos_no'];
                $Key++;
            }
            $pwi_name = trim($this->request->getPost('pwi_name'));
            $filename = 'Activity_Summary_Report_'.$pwi_name.'_'.time().'.xlsx';
            exceldownload($dat, $filename);
        }
    }

    public function getDeviceCoordinates()
    {
        $returnArr = [];

        // Get data from POST request

        $dt = date("Y-m-d", strtotime($this->request->getPost('todate')));
        $fromDateTime = date("Y-m-d H:i:s", strtotime($this->request->getPost('fromdate')));
        $toDateTime = date("Y-m-d H:i:s", strtotime($this->request->getPost('todate')));
        $deviceId = trim($this->request->getPost('deviceid'));

        // Query to fetch positional record of the device
        try {
            $getCoordinates = $this->db->query(
                "SELECT * FROM public.get_positional_record_of_eatch_divice_date_to_date(?, ?, ?) 
                 ORDER BY currentdate, currenttime ASC", 
                [$deviceId, $fromDateTime, $toDateTime]
            )->getResultArray();

            if (!empty($getCoordinates)) {
                $returnArr = $getCoordinates;
            }

            // Query to fetch device assignment details
            $userIdQuery = $this->db->query(
                "SELECT * FROM {$this->schema}.device_asign_details WHERE deviceid = ? AND active = 1", 
                [$deviceId]
            )->getRow();

            // Get history data details
            $getHistoryDetails = $this->db->query(
                "SELECT positional_id, deviceid, currentdate, starttime, event_list, latitude, longitude, 
                        stopduration, geom, upper(event_list) || ' Du.(' || stopduration || ')' AS event_list,
                        parent_id, user_id, group_id
                 FROM (SELECT min(positional_id) AS positional_id, deviceid, currentdate, min(currenttime) AS starttime,
                             event_list, latitude, longitude, max(p_time) - min(p_time) AS stopduration,
                             geom, parent_id, user_id, group_id
                       FROM (SELECT DISTINCT positional_id, deviceid, currentdate, currenttime, event_list, latitude,
                                    longitude, max(p_time) AS p_time, geom, parent_id, user_id, group_id
                             FROM public.get_history_data_details_data_test_first(?, ?, ?) 
                             WHERE positional_id IS NOT NULL 
                             GROUP BY parent_id, user_id, group_id, positional_id, deviceid, currentdate, currenttime, 
                                      event_list, latitude, longitude, geom
                             ORDER BY deviceid, currentdate, currenttime, event_list, latitude, longitude ASC) ddd
                       GROUP BY latitude, longitude, deviceid, currentdate, event_list, geom, parent_id, user_id, group_id
                       ORDER BY 4 ASC) ss 
                 WHERE stopduration > INTERVAL '00:00:01'"
            , [$this->sessdata['user_id'], $dt, $deviceId])->getResultArray();

            $getHistoryDetailsFinal = [];
            if (count($getHistoryDetails) > 0) {
                foreach ($getHistoryDetails as $index => $history) {
                    $getHistoryDetailsFinal[$index] = [
                        'id' => $history['positional_id'],
                        'user_id' => $history['user_id'],
                        'deviceid' => $history['deviceid'],
                        'currentdate' => $history['currentdate'],
                        'currenttime' => $history['currenttime'],
                        'event_list' => $history['event_list'],
                        'latitude' => $history['latitude'],
                        'longitude' => $history['longitude'],
                        'geom' => $history['geom'],
                        'geoanceid' => '',
                        'geofancegeom' => '',
                        'geofancegeomwithbuffer' => '',
                        'geomtype' => '',
                        'lonlat' => '',
                        'refname' => '',
                        'faetureid' => $history['positional_id'],
                    ];
                }
            }

            $result = [
                'history_details' => $getHistoryDetailsFinal,
                'getcoordinates' => $returnArr,
                'history_summary' => [],
                'getpoledata' => [],
                'getpolelinedata' => []
            ];

            return $this->response->setJSON($result);

        } catch (DatabaseException $e) {
            return $this->response->setStatusCode(500)->setJSON([
                'error' => 'Database error',
                'message' => $e->getMessage(),
            ]);
        }
    }

    public function stoppageReport()
    {
        // Check if the user is logged in
        if (!session()->has('login_sess_data')) {
            return redirect('/');
        }

        $data = [];
        
        $data['sessdata'] = $this->sessdata;
        $data['page_title'] = "Stoppage Report";
        $data['usersdd'] = $this->commonModel->get_users(); // Ensure this method exists in the model
        $data['reporttype'] = $this->db->query("
            SELECT * 
            FROM public.report_type 
            WHERE active = 1 AND id NOT IN (1, 2, 3, 4, 5, 6, 8, 11)
        ")->getResult();

        // Initialize date fields
        $data['date_from'] = '';
        $data['date_to'] = '';
        $data['report_type'] = 7;

        // Query device dropdown based on user group
        $userId =  $this->sessdata['user_id']; // Assuming session data is an associative array
        $schema = $this->schema; // Make sure $this->schema is set

        $query = "
            SELECT a.*, 
                (SELECT device_name 
                FROM {$schema}.master_device_setup  
                WHERE id = (SELECT MAX(id) 
                            FROM {$schema}.master_device_setup 
                            WHERE inserttime::date <= CURRENT_DATE::date AND deviceid = a.did)) AS device_name 
            FROM public.get_divice_details_record_for_list_for_company('{$schema}', $userId) AS a 
            WHERE a.group_id = 2 AND a.active = 1
        ";

        $data['devicedropdown'] = $this->db->query($query)->getResult();

        // Check if the form has been submitted
        if ($this->request->getMethod() == 'POST') {
            $user_id = $this->sessdata['user_id'];
            $group_id = $this->sessdata['group_id'];        
            $devices = $alerts = $geofences = $routes = $dids = null;
            
            // Retrieve and format input data
            $data['report_type'] = $report_type = trim($this->request->getPost('report_type'));
            
            $date_from = date("Y-m-d", strtotime(trim($this->request->getPost('date_from'))));
            $date_to = date("Y-m-d", strtotime(trim($this->request->getPost('date_to'))));
            $time_from = "00:00:00";
            $time_to = "23:59:00";            
            $data['date_from'] = date("d-m-Y H:i", strtotime(trim($this->request->getPost('date_from'))));
            $data['date_to'] = date("d-m-Y H:i", strtotime(trim($this->request->getPost('date_to'))));
            
            $data['pwi_id'] = $pwi_id = trim($this->request->getPost('user'));
            $data['pwi_name'] = trim($this->request->getPost('pwi_name'));
            $data['schema'] = $this->schema;

            // Initialize devices variable
            $devices .= "{";

            // Build the query based on the pwi_name
            if ($data['pwi_name'] === 'All') {
                $query = "
                    SELECT sup_pid, sup_gid, serial_no, imei_no, did, mobile_no, warranty_date, linked, parent_id, user_id, 
                        issudate, refunddate, active, issold, apply_scheam, group_id, role_id, email, address, pincode, 
                        state_name, country, username, firstname, lastname, organisation, group_name, '' AS list_item, 
                        '' AS list_item_name 
                    FROM public.get_divice_details_record_for_list('{$this->schema}', {$user_id}) 
                    WHERE sup_gid = {$group_id} AND user_id = {$user_id} AND active = 1 
                    ORDER BY did ASC
                ";
            } else {
                $query = "
                    SELECT sup_pid, sup_gid, serial_no, imei_no, did, mobile_no, warranty_date, linked, parent_id, user_id, 
                        issudate, refunddate, active, issold, apply_scheam, group_id, role_id, email, address, pincode, 
                        state_name, country, username, firstname, lastname, organisation, group_name, '' AS list_item, 
                        '' AS list_item_name 
                    FROM public.get_divice_details_record_for_list('{$this->schema}', {$pwi_id}) 
                    WHERE user_id = {$pwi_id} AND active = 1 
                    ORDER BY issudate ASC
                ";
            }

            // Execute the query and fetch results
            $devicelist = $this->db->query($query)->getResult();

            // Process the device list
            if (count($devicelist) > 0) {
                foreach ($devicelist as $devicelist_each) {
                    if ($devices === "{") {
                        $devices .= $devicelist_each->did;
                        $dids .= $devicelist_each->did;
                    } else {
                        $devices .= "," . $devicelist_each->did;
                        $dids .= "," . $devicelist_each->did;
                    }
                }
            }
            $devices .= "}";
            
            if ($report_type == 7) {
                // Get the device ID from POST data
                $data['device_id'] = $device_id = trim($this->request->getPost('device_id'));
                $dids = null;
            
                // If device ID is not empty, set the $dids variable
                if (!empty($device_id)) {
                    $dids = $device_id;
                }
            
                // Build the SQL query
                $query = "
                    SELECT mddd, mdddevicename, mddserialno, divicename, result_date, deviceid, acting_trip, 
                           start_time, endtime, duration, distance_cover, sos_no, alert_no, call_no, 
                           totalnoofstop, totalstoptime, polno, rd, dv, devicename, acting_triped, 
                           starttime, end_time, duration1, distancecover, sosno, alertno, callno, 
                           totalnoof_stop, totalsto_ptime, pol_no, polename1, polnoend, polenameend1, 
                           polename, polenameend, genid 
                    FROM (
                        SELECT DISTINCT deviceid AS mddd,
                            (SELECT device_name 
                             FROM {$this->schema}.master_device_setup  
                             WHERE deviceid = ax.deviceid 
                             AND id = (SELECT MAX(id) 
                                        FROM {$this->schema}.master_device_setup 
                                        WHERE inserttime::date <= '$date_to'::date  
                                        AND deviceid = ax.deviceid)
                            ) AS mdddevicename,
                            (SELECT COALESCE(serial_no, 'Absent')  
                             FROM {$this->schema}.master_device_details  
                             WHERE superdevid = ax.deviceid OR id = ax.deviceid
                            ) AS mddserialno   
                        FROM {$this->schema}.master_device_assign AS ax 
                        WHERE deviceid IN ($dids) AND group_id = 2
                    ) masterdevice 
                    LEFT OUTER JOIN (
                        SELECT * 
                        FROM public.trip_spesified_device 
                        WHERE (genid, deviceid, result_date, acting_trip) IN (
                            SELECT MAX(genid), deviceid, result_date, acting_trip 
                            FROM public.trip_spesified_device 
                            WHERE totalstoptime > '00:00:00' 
                            AND deviceid IN ($dids) 
                            AND (result_date || ' ' || start_time >= '{$date_from} {$time_from}' 
                            AND result_date || ' ' || end_time <= '{$date_to} {$time_to}')
                            GROUP BY deviceid, result_date, acting_trip
                        )
                        ORDER BY devicename, result_date, acting_trip
                    ) resultdivice ON masterdevice.mddd = resultdivice.deviceid
                ORDER BY result_date, mddd ASC";
            
                // Execute the query and store the result
                $data['alldata'] = $this->db->query($query)->getResult();
            
                // Optional: Uncomment to debug the last query
                // echo $this->db->getLastQuery(); exit;
            }

            if ($report_type == 8) {
                $data['alldata'] = [];
                
                // Build the SQL query
                $query = "
                    SELECT typeofuser, deviceid, devicename, sessiondate, 
                           sesectionstartttime, sesectionendttime, starttime, 
                           endtime, duration, (orginallength), 
                           (withinpolelength) AS totalwitpole, polesequenct, 
                           (SELECT device_name 
                            FROM {$this->schema}.master_device_setup 
                            WHERE deviceid = mdd.superdevid 
                            AND id = (SELECT MAX(id) 
                                      FROM {$this->schema}.master_device_setup 
                                      WHERE inserttime::date <= '$date_from'::date 
                                      AND deviceid = mdd.superdevid)
                           ) AS mdddevicename, 
                           mdd.serial_no 
                    FROM public.analysish_patrol_man_and_key_man_work_patrol('$date_from'::date, '{$this->schema}'::character varying) AS ax 
                    RIGHT JOIN {$this->schema}.master_device_details AS mdd ON (ax.deviceid = mdd.superdevid) 
                    WHERE mdd.superdevid IN ($dids)
                ";
            
                // Execute the query and store the result
                $result = $this->db->query($query)->getResult();
            
                $deviceserial = '';
                $i = 0;
            
                foreach ($result as $result_each) {
                    if ($deviceserial != $result_each->serial_no) {
                        if ($deviceserial == '') {
                            $i = 0;
                        } else {
                            $i++;
                        }
                        $deviceserial = $result_each->serial_no;
            
                        if ($result_each->deviceid != '') {
                            $data['alldata'][$i] = (object) [
                                'typeofuser' => $result_each->typeofuser,
                                'deviceid' => $result_each->deviceid,
                                'devicealiasname' => $result_each->mdddevicename,
                                'devicename' => $result_each->serial_no,
                                'sessiondate' => $result_each->sessiondate,
                                'starttime' => $result_each->starttime,
                                'endtime' => $result_each->endtime,
                                'duration' => $result_each->duration,
                                'distance' => $result_each->totalwitpole,
                                'orginallength' => $result_each->orginallength,
                            ];
                        } else {
                            $data['alldata'][$i] = (object) [
                                'typeofuser' => 'NA',
                                'deviceid' => 'NA',
                                'devicealiasname' => $result_each->mdddevicename,
                                'devicename' => $result_each->serial_no,
                                'sessiondate' => $result_each->sessiondate,
                                'starttime' => '00:00:00',
                                'endtime' => '00:00:00',
                                'duration' => 'NA',
                                'distance' => $result_each->totalwitpole,
                                'orginallength' => $result_each->orginallength,
                            ];
                        }
                    } else {
                        $data['alldata'][$i]->distance += $result_each->totalwitpole;
                    }
                }
            
                // Optional: Uncomment to debug the last query
                // echo $this->db->getLastQuery(); exit;
                // echo '<pre>'; print_r($data['alldata']); exit;
            }
            
            if ($report_type == 9) {
                $data['alldata'] = $this->db->query("
                    SELECT mddd, mdddevicename, mddserialno, divicename, result_date, deviceid, acting_trip, start_time, 
                           endtime, duration, distance_cover, sos_no, alert_no, call_no, totalnoofstop, totalstoptime, 
                           polno, rd, dv, devicename, acting_triped, starttime, end_time, duration1, distancecover, 
                           sosno, alertno, callno, totalnoof_stop, totalsto_ptime, pol_no, polename1, polnoend, 
                           polenameend1, polename, polenameend, genid 
                    FROM (
                        SELECT DISTINCT deviceid AS mddd, 
                            (SELECT device_name 
                             FROM {$this->schema}.master_device_setup  
                             WHERE deviceid = ax.deviceid 
                             AND id = (SELECT MAX(id) 
                                        FROM {$this->schema}.master_device_setup 
                                        WHERE inserttime::date <= '$date_to'::date  
                                        AND deviceid = ax.deviceid)) AS mdddevicename,
                            (SELECT COALESCE(serial_no, 'Absent')  
                             FROM {$this->schema}.master_device_details  
                             WHERE superdevid = ax.deviceid OR id = ax.deviceid) AS mddserialno   
                        FROM {$this->schema}.master_device_assign AS ax 
                        WHERE deviceid IN ($dids) AND group_id = 2 
                    ) masterdevice 
                    LEFT OUTER JOIN (
                        SELECT * 
                        FROM public.trip_spesified_device 
                        WHERE (genid, deviceid, result_date, acting_trip) IN (
                            SELECT MAX(genid), deviceid, result_date, acting_trip 
                            FROM public.trip_spesified_device 
                            WHERE deviceid IN ($dids) 
                            AND (result_date || ' ' || start_time >= '{$date_from} {$time_from}' 
                            AND result_date || ' ' || end_time <= '{$date_to} {$time_to}') 
                            GROUP BY deviceid, result_date, acting_trip
                        ) 
                        ORDER BY devicename, result_date, acting_trip
                    ) resultdivice ON masterdevice.mddd = resultdivice.deviceid
                ")->getResult();
            
                // Optional: Debugging last query
                // echo $this->db->getLastQuery(); exit;
            }
            
            if ($report_type == 10) {
                if ($this->sessdata->group_id == 2) {
                    $data['alldata'] = $this->db->query("
                        SELECT * 
                        FROM public.get_divice_details_record_for_list('{$this->schema}', {$this->sessdata->user_id}) 
                        WHERE did IN ($dids) 
                        AND (issudate::date BETWEEN '$date_from' AND '$date_to')
                    ")->getResult();
                } else {
                    $data['alldata'] = $this->db->query("
                        SELECT * 
                        FROM get_divice_details_record_for_list_for_company('{$this->schema}', {$this->sessdata->user_id}) 
                        WHERE did IN ($dids) 
                        AND (issudate::date BETWEEN '$date_from' AND '$date_to') 
                        AND sup_gid = 2
                    ")->getResult();
                }
                
                // Optional: Debugging last query
                // echo $this->db->getLastQuery(); exit;
            }
            
            if ($report_type == 11) {
                $data['alldata'] = [];
                $result = $this->db->query("
                    SELECT typeofuser, deviceid, devicename, sesectionstartttime, sesectionendttime, starttime, 
                           endtime, duration, startpoletime, startpole, endpointtime, endpoint, 
                           (SELECT device_name 
                            FROM {$this->schema}.master_device_setup 
                            WHERE deviceid = mdd.superdevid 
                            AND id = (SELECT MAX(id) 
                                      FROM {$this->schema}.master_device_setup 
                                      WHERE inserttime::date <= '$date_from'::date 
                                      AND deviceid = mdd.superdevid)) AS mdddevicename, 
                           mdd.serial_no 
                    FROM public.analysish_work_patrol_time_schudele('$date_from'::date, '{$this->schema}'::character varying) AS ax 
                    RIGHT JOIN {$this->schema}.master_device_details AS mdd ON (ax.deviceid = mdd.superdevid) 
                    WHERE mdd.superdevid IN ($dids)
                ")->getResult();
            
                $deviceserial = '';
                $i = 0;
            
                foreach ($result as $result_each) {
                    if (strpos($result_each->mdddevicename, 'Patrolman') !== false) {
                        $data['alldata'][$i] = (object) [
                            'typeofuser' => $result_each->typeofuser,
                            'deviceid' => $result_each->deviceid,
                            'devicealiasname' => $result_each->mdddevicename,
                            'devicename' => $result_each->serial_no,
                            'sessiondate' => $result_each->sessiondate,
                            'schedulestarttime' => date('d-m-Y H:i:s', strtotime($result_each->starttime)),
                            'scheduleendtime' => date('d-m-Y H:i:s', strtotime($result_each->endtime)),
                            'startpoletime' => $result_each->startpoletime == '2000-10-01 00:00:00' ? 'NA' : date('d-m-Y H:i:s', strtotime($result_each->startpoletime)),
                            'endpointtime' => $result_each->endpointtime == '2000-10-01 00:00:00' ? 'NA' : date('d-m-Y H:i:s', strtotime($result_each->endpointtime)),
                            'startpole' => $result_each->startpole,
                            'endpole' => $result_each->endpoint,
                        ];
                        $i++;
                    }
                }
            
                // Optional: Debugging last query
                // echo $this->db->getLastQuery(); exit;
                // echo '<pre>'; print_r($data['alldata']); exit;
            }
            
        }

        // Load the view
        $data['middle'] = view('traxreport/stoppagereport_view', $data);
        return view('mainlayout', $data);
    }

    public function stoppagereportexcel() {
		$sessdata = $this->sessdata;			
		ini_set('max_execution_time', 3000); 
		ini_set('memory_limit', '-1');
		if ($this->request->getMethod() == 'POST') {
			$dat = array();
			$user_id = $this->sessdata['user_id'];
			$group_id = $this->sessdata['group_id'];		
			$devices = $alerts = $geofences = $routes = $dids = null;
			$data['report_type'] = $report_type = trim($this->request->getPost('report_type'));
			
			$date_from = date("Y-m-d", strtotime(trim($this->request->getPost('date_from'))));
			$date_to = date("Y-m-d", strtotime(trim($this->request->getPost('date_to'))));
			$time_from = "00:00:00";//date("H:i:s", strtotime(trim($this->request->getPost('date_from'))));
			$time_to = "23:59:00";//date("H:i:s", strtotime(trim($this->request->getPost('date_to'))));			
			$data['date_from'] = date("d-m-Y H:i", strtotime(trim($this->request->getPost('date_from'))));
			$data['date_to'] = date("d-m-Y H:i", strtotime(trim($this->request->getPost('date_to'))));
			
			$data['pwi_id'] = $pwi_id = trim($this->request->getPost('user'));
			$data['pwi_name'] = trim($this->request->getPost('pwi_name'));
			$data['schema'] = $this->schema;
			$devices .= "{";
			if($data['pwi_name'] == 'All'){
				$query = "select sup_pid ,sup_gid ,serial_no,imei_no,did,mobile_no,warranty_date,linked,parent_id,user_id,issudate,
						refunddate,active,issold,apply_scheam,group_id,role_id,email,address,pincode,state_name,country,username,firstname,
						lastname,organisation,group_name,'' as list_item, '' as list_item_name from public.get_divice_details_record_for_list('".$this->schema."',".$user_id.") where sup_gid = $group_id and user_id=".$user_id." order by did asc";
			}
			else{
				$query = "select sup_pid ,sup_gid ,serial_no,imei_no,did,mobile_no,warranty_date,linked,parent_id,user_id,issudate,
					refunddate,active,issold,apply_scheam,group_id,role_id,email,address,pincode,state_name,country,username,firstname,
					lastname,organisation,group_name,list_item,list_item_name from ((select sup_pid ,sup_gid ,serial_no,imei_no,did,mobile_no,warranty_date,linked,parent_id,user_id,issudate,
					refunddate,active,issold,apply_scheam,group_id,role_id,email,address,pincode,state_name,country,username,firstname,
					lastname,organisation,group_name,'' as list_item1, '' as list_item_name2 from public.get_divice_details_record_for_list('".$this->schema."',".$pwi_id.")) ss 
					inner join
					(select serial_no as top_serial_no  , imei_no as top_imei_no ,max(issudate) as max_issdate,string_agg(group_name,' - ') as list_item,string_agg(organisation,' - ') as list_item_name
						 from public.get_divice_details_record_for_list('".$this->schema."',".$pwi_id.") group by serial_no,imei_no )ssi
						 on ss.issudate=ssi.max_issdate  and ss.serial_no=ssi.top_serial_no  )xvsd order by issudate asc";
			}
			$devicelist = $this->db->query($query)->getResult();
			if(count($devicelist)>0){
				foreach($devicelist as $devicelist_each){
					if($devices == "{"){
						$devices .= $devicelist_each->did;
						$dids .= $devicelist_each->did;
					}
					else{
						$devices .= ",".$devicelist_each->did;
						$dids .= ",".$devicelist_each->did;
					}
					
				}
			}
			$devices .= "}";
			if($report_type == 7){
				$data['device_id'] = $device_id = trim($this->request->getPost('device_id'));
				if(!empty($device_id)){
					$dids = $device_id;
				}
				$data['alldata'] = $this->db->query("select mddd,mdddevicename,mddserialno,divicename, 
                result_date, deviceid, acting_trip, start_time, endtime,duration, distance_cover, sos_no, 
                alert_no, call_no, totalnoofstop, totalstoptime, polno, rd, dv, devicename, acting_triped, 
                starttime,   end_time, duration1, distancecover, sosno, alertno, callno, totalnoof_stop,  
                totalsto_ptime, pol_no, polename1, polnoend, polenameend1, polename, polenameend, genid 
                from (( SELECT distinct deviceid as mddd,(SELECT device_name FROM ".$this->schema.".master_device_setup  
                where deviceid=ax.deviceid and id=(SELECT  max(id) FROM ".$this->schema.".master_device_setup 
                where inserttime::date<='$date_to'::date  and deviceid=ax.deviceid )) as mdddevicename,
                (SELECT  coalesce(serial_no,'Absent')  FROM ".$this->schema.".master_device_details  
                where superdevid=ax.deviceid or id=ax.deviceid) as mddserialno   
                FROM ".$this->schema.".master_device_assign as ax 
                where deviceid in ($dids) and group_id=2 )  masterdevice 
                left outer join (select * from public.trip_spesified_device where (genid,deviceid,result_date,acting_trip) in(select max(genid),deviceid,
                result_date,acting_trip from public.trip_spesified_device 
                where deviceid in ($dids) and (result_date||' '||start_time >= '".$date_from." ".$time_from."' and result_date||' '||end_time <= '".$date_to." ".$time_to."') 
                group by deviceid,result_date,acting_trip) order by devicename,result_date,acting_trip)resultdivice on masterdevice.mddd=resultdivice.deviceid)resultset 
                order by result_date,mddd asc")->getResult();
				//echo $this->db->last_query();exit;
				$dat[0]['A'] = "Date";
				$dat[0]['B'] = "Device Name";
				$dat[0]['C'] = "Device ID";
				$dat[0]['D'] = "PWI";
				$dat[0]['E'] = "Type";
				$dat[0]['F'] = "Trip No.";
				$dat[0]['G'] = "Start Time(HH:MM:SS)";
				$dat[0]['H'] = "End Time(HH:MM:SS)";
				$dat[0]['I'] = "Travelled Distance(KM)";
				$dat[0]['J'] = "Travelled Time(HH:MM:SS)";
				$dat[0]['K'] = "Stop Duration(HH:MM:SS)";
				
				foreach($data['alldata'] as $Key => $val){
					$mdddevicename_arr = explode("/",$val->mdddevicename);
                    if(count($mdddevicename_arr) > 1) {
                        $mdddevicename_arr[0] = $mdddevicename_arr[0];
                        $mdddevicename_arr[1] = $mdddevicename_arr[1];
                    } else {
                        $mdddevicename_arr[0] = 'NA';
                        $mdddevicename_arr[1] = 'NA';
                    }
					if (strpos(strtolower($mdddevicename_arr[0]), 'stock') !== false) {
						$type = 'Stock';
					}
					else if (strpos(strtolower($mdddevicename_arr[0]), 'keyman') !== false) {
						$type = 'Keyman';
					}
					else if (strpos(strtolower($mdddevicename_arr[0]), 'patrolman') !== false) {
						$type = 'Patrolman';
					} else {
                        $type = '';
                    }
					if($val->acting_trip != ''){
						$dat[$Key+1]['A'] = date("d-m-Y", strtotime($val->result_date));
						$dat[$Key+1]['B'] = $val->mdddevicename;
						$dat[$Key+1]['C'] = $val->mddserialno;
						$dat[$Key+1]['D'] = $mdddevicename_arr[1];
						$dat[$Key+1]['E'] = $type;
						$dat[$Key+1]['F'] = $val->acting_trip;
						$dat[$Key+1]['G'] = $val->start_time;
						$dat[$Key+1]['H'] = $val->end_time;
						$dat[$Key+1]['I'] = round($val->distance_cover/1000,2);
						$dat[$Key+1]['J'] = $val->duration;
						$dat[$Key+1]['K'] = $val->totalstoptime;
					}
					else{
						$dat[$Key+1]['A'] = 'NA';
						$dat[$Key+1]['B'] = $val->mdddevicename;
						$dat[$Key+1]['C'] = $val->mddserialno;
						$dat[$Key+1]['D'] = $mdddevicename_arr[1];
						$dat[$Key+1]['E'] = $type;
						$dat[$Key+1]['F'] = 'NA';
						$dat[$Key+1]['G'] = 'NA';
						$dat[$Key+1]['H'] = 'NA';
						$dat[$Key+1]['I'] = 'NA';
						$dat[$Key+1]['J'] = 'NA';
						$dat[$Key+1]['K'] = 'NA';
					}
				}
				
				$filename = 'Stoppage_Report_'.$data['pwi_name'].'_'.time().'.xlsx';
			}
			if($report_type == 8){				
				$data['alldata'] = array();
				 $result = $this->db->query("select typeofuser, deviceid , devicename , sessiondate , sesectionstartttime, sesectionendttime , 
                 starttime , endtime , duration , (orginallength) , (withinpolelength) as totalwitpole,polesequenct, 
                 (SELECT device_name FROM ".$this->schema.".master_device_setup where deviceid=mdd.superdevid 
                 and id=(SELECT max(id) FROM ".$this->schema.".master_device_setup where inserttime::date<='$date_from'::date and deviceid=mdd.superdevid )) as mdddevicename, mdd.serial_no 
                 from public.analysish_patrol_man_and_key_man_work_patrol('$date_from') as ax 
                 right join ".$this->schema.".master_device_details as mdd on (ax.deviceid = mdd.superdevid) 
                 where  mdd.superdevid in ($dids)")->getResult();
				 $deviceserial = '';
				 $i = 0;
				 foreach($result as $result_each){
					 if($deviceserial != $result_each->serial_no){
						if($deviceserial == ''){
							$i = 0;
						}
						else{
							$i++;
						}
						$deviceserial = $result_each->serial_no;
						if($result_each->deviceid != ''){
							$data['alldata'][$i]->typeofuser = $result_each->typeofuser;
							$data['alldata'][$i]->deviceid = $result_each->deviceid;
							$data['alldata'][$i]->devicealiasname = $result_each->mdddevicename;
							$data['alldata'][$i]->devicename = $result_each->serial_no;
							$data['alldata'][$i]->sessiondate = $result_each->sessiondate;
							$data['alldata'][$i]->starttime = $result_each->starttime;
							$data['alldata'][$i]->endtime = $result_each->endtime;
							$data['alldata'][$i]->duration = $result_each->duration;
							$data['alldata'][$i]->distance = $result_each->totalwitpole;
							$data['alldata'][$i]->orginallength = $result_each->orginallength;
						}
						else{
							$data['alldata'][$i]->typeofuser = 'NA';
							$data['alldata'][$i]->deviceid = 'NA';
							$data['alldata'][$i]->devicealiasname = $result_each->mdddevicename;
							$data['alldata'][$i]->devicename = $result_each->serial_no;
							$data['alldata'][$i]->sessiondate = $result_each->sessiondate;
							$data['alldata'][$i]->starttime = '00:00:00';
							$data['alldata'][$i]->endtime = '00:00:00';
							$data['alldata'][$i]->duration = 'NA';
							$data['alldata'][$i]->distance = $result_each->totalwitpole;
							$data['alldata'][$i]->orginallength = $result_each->orginallength;
						}
					 }
					 else{
						 $data['alldata'][$i]->distance = ($data['alldata'][$i]->distance + $result_each->totalwitpole);
					 }
				 }
				//echo $this->db->last_query();exit;
				$dat[0]['A'] = "User Type";
				$dat[0]['B'] = "Device Name";
				$dat[0]['C'] = "Device ID";
				$dat[0]['D'] = "Start(DD-MM-YYYY HH:MM:SS)";
				$dat[0]['E'] = "End(DD-MM-YYYY HH:MM:SS)";
				$dat[0]['F'] = "Within Pole Distance(KM)";
				$dat[0]['G'] = "Total Distance(KM)";
				$dat[0]['H'] = "Travelled Time(HH:MM:SS)";
				
				foreach($data['alldata'] as $Key => $val){
					if($val->typeofuser == 'PatrolMan'){
						$start = date("d-m-Y", strtotime($val->sessiondate)).' '.$val->starttime;
						$end = date("d-m-Y", strtotime($val->sessiondate . ' +1 day')).' '.$val->endtime;
					}
					else if($val->typeofuser == 'Key Man'){
						$start = date("d-m-Y", strtotime($val->sessiondate)).' '.$val->starttime;
						$end = date("d-m-Y", strtotime($val->sessiondate)).' '.$val->endtime;
					}
					else{
						$start = 'NA';
						$end = 'NA';
					}
					if($val->orginallength < $val->distance){
						$val->orginallength = $val->distance;
					}
					$dat[$Key+1]['A'] = $val->typeofuser;
					$dat[$Key+1]['B'] = $val->devicealiasname;
					$dat[$Key+1]['C'] = $val->devicename;
					$dat[$Key+1]['D'] = $start;
					$dat[$Key+1]['E'] = $end;
					$dat[$Key+1]['F'] = round($val->distance/1000,2);
					$dat[$Key+1]['G'] = round($val->orginallength/1000,2);
					$dat[$Key+1]['H'] = $val->duration;
				}
				
				$filename = 'Trip_Date_Wise_Report_'.$data['pwi_name'].'_'.time().'.xlsx';
			}
			if($report_type == 9){				
				$data['alldata'] = $this->db->query("select mddd,mdddevicename,mddserialno,divicename, result_date, deviceid, acting_trip, start_time, endtime,duration, distance_cover, sos_no, alert_no, call_no, totalnoofstop, totalstoptime, polno, rd, dv, devicename, acting_triped, starttime,   end_time, duration1, distancecover, sosno, alertno, callno, totalnoof_stop,  totalsto_ptime, pol_no, polename1, polnoend, polenameend1, polename, polenameend, genid from (( SELECT distinct deviceid as mddd,(SELECT device_name FROM ".$this->schema.".master_device_setup  where deviceid=ax.deviceid and id=(SELECT  max(id) FROM ".$this->schema.".master_device_setup where inserttime::date<='$date_to'::date  and deviceid=ax.deviceid )) as mdddevicename,(SELECT  coalesce(serial_no,'Absent')  FROM ".$this->schema.".master_device_details  where superdevid=ax.deviceid or id=ax.deviceid) as mddserialno   FROM ".$this->schema.".master_device_assign as ax where deviceid in ($dids) and group_id=2 )  masterdevice left outer join (select * from public.trip_spesified_device where (genid,deviceid,result_date,acting_trip) in(select max(genid),deviceid,result_date,acting_trip from public.trip_spesified_device where deviceid in ($dids) and (result_date||' '||start_time >= '".$date_from." ".$time_from."' and result_date||' '||end_time <= '".$date_to." ".$time_to."') group by deviceid,result_date,acting_trip) order by devicename,result_date,acting_trip)resultdivice on masterdevice.mddd=resultdivice.deviceid)resultset order by result_date,mddd asc")->getResult();
				//echo $this->db->last_query();exit;
				$dat[0]['A'] = "Date";
				$dat[0]['B'] = "Device Name";
				$dat[0]['C'] = "Device ID";
				$dat[0]['D'] = "Trip No.";
				$dat[0]['E'] = "Travelled Distance(KM)";			
				
				foreach($data['alldata'] as $Key => $val){
					if($val->acting_trip != ''){
						$dat[$Key+1]['A'] = date("d-m-Y", strtotime($val->result_date));
						$dat[$Key+1]['B'] = $val->mdddevicename;
						$dat[$Key+1]['C'] = $val->mddserialno;
						$dat[$Key+1]['D'] = $val->acting_trip;
						$dat[$Key+1]['E'] = round($val->distance_cover/1000,2);
					}
					else{
						$dat[$Key+1]['A'] = 'NA';
						$dat[$Key+1]['B'] = $val->mdddevicename;
						$dat[$Key+1]['C'] = $val->mddserialno;
						$dat[$Key+1]['D'] = 'NA';
						$dat[$Key+1]['E'] = 'NA';
					}
					
				}
				
				$filename = 'Movement_Summery_Report_'.$data['pwi_name'].'_'.time().'.xlsx';
			}
			if($report_type == 10){
				if($this->sessdata->group_id == 2){
				$data['alldata'] = $this->db->query("select * from public.get_divice_details_record_for_list('".$this->sessdata->schemaname."',".$this->sessdata->user_id.") where did in ($dids) and (issudate::date between '".$date_from."' and '".$date_to."')")->getResult();
				}
				else{
					$data['alldata'] = $this->db->query("select * from get_divice_details_record_for_list_for_company('".$this->sessdata->schemaname."',".$this->sessdata->user_id.") where did in ($dids) and (issudate::date between '".$date_from."' and '".$date_to."') and sup_gid = 2")->getResult();
				}
				//echo $this->db->last_query();exit;
				$dat[0]['A'] = "Device ID";
				$dat[0]['B'] = "IMEI No.";
				$dat[0]['C'] = "Allotee Name";
				$dat[0]['D'] = "Allotment Date";			
				
				foreach($data['alldata'] as $Key => $val){
					
					$dat[$Key+1]['A'] = $val->serial_no;
					$dat[$Key+1]['B'] = $val->imei_no;
					$dat[$Key+1]['C'] = $val->organisation;
					$dat[$Key+1]['D'] = date("d-m-Y", strtotime($val->issudate));
					
				}
				
				$filename = 'Device_Allotment_Report_'.$data['pwi_name'].'_'.time().'.xlsx';
			}
			if($report_type == 11){				
				$data['alldata'] = array();
				 $result = $this->db->query("select typeofuser , deviceid , devicename ,  sesectionstartttime, sesectionendttime, starttime, endtime,duration ,startpoletime,startpole,endpointtime,endpoint, (SELECT device_name FROM ".$this->sessdata->schemaname.".master_device_setup where deviceid=mdd.superdevid and id=(SELECT max(id) FROM ".$this->sessdata->schemaname.".master_device_setup where inserttime::date<='$date_from'::date and deviceid=mdd.superdevid )) as mdddevicename, mdd.serial_no from public.analysish_work_patrol_time_schudele('$date_from') as ax right join ".$this->sessdata->schemaname.".master_device_details as mdd on (ax.deviceid = mdd.superdevid) where mdd.superdevid in ($dids)")->getResult();
				 $deviceserial = '';
				 $i = 0;
				 foreach($result as $result_each){
					 
					if (strpos($result_each->mdddevicename, 'Patrolman') !== false) {
						$data['alldata'][$i]->typeofuser = $result_each->typeofuser;
						$data['alldata'][$i]->deviceid = $result_each->deviceid;
						$data['alldata'][$i]->devicealiasname = $result_each->mdddevicename;
						$data['alldata'][$i]->devicename = $result_each->serial_no;
						$data['alldata'][$i]->sessiondate = $result_each->sessiondate;
						$data['alldata'][$i]->schedulestarttime = date('d-m-Y H:i:s',strtotime($result_each->starttime));
						$data['alldata'][$i]->scheduleendtime = date('d-m-Y H:i:s',strtotime($result_each->endtime));
						if ($result_each->startpoletime == '2000-10-01 00:00:00') {
							$data['alldata'][$i]->startpoletime = 'NA';
						} else {
							$data['alldata'][$i]->startpoletime = date('d-m-Y H:i:s',strtotime($result_each->startpoletime));
						}
						if ($result_each->endpointtime == '2000-10-01 00:00:00') {
							$data['alldata'][$i]->endpointtime = 'NA';
						} else {
							$data['alldata'][$i]->endpointtime = date('d-m-Y H:i:s',strtotime($result_each->endpointtime));
						}
						$data['alldata'][$i]->startpole = $result_each->startpole;
						$data['alldata'][$i]->endpole = $result_each->endpoint;
							
						$i++;
					}
					 
				 }
				//echo $this->db->last_query();exit;
				$dat[0]['A'] = "User Type";
				$dat[0]['B'] = "Device Name";
				$dat[0]['C'] = "Device ID";
				$dat[0]['D'] = "Start Pole";
				$dat[0]['E'] = "End Pole";
				$dat[0]['F'] = "Scheduled Start(DD-MM-YYYY HH:MM:SS)";
				$dat[0]['G'] = "Scheduled End(DD-MM-YYYY HH:MM:SS)";
				$dat[0]['H'] = "Actual Start(DD-MM-YYYY HH:MM:SS)";
				$dat[0]['I'] = "Actual End(DD-MM-YYYY HH:MM:SS)";
				
				foreach($data['alldata'] as $Key => $val){
					if($val->typeofuser == 'PatrolMan'){
						$schedulestarttime = $val->schedulestarttime;
						$scheduleendtime = $val->scheduleendtime;
						$startpoletime = $val->startpoletime;
						$endpointtime = $val->endpointtime;
						$startpole = $val->startpole;
						$endpole = $val->endpole;
					}
					else if($val->typeofuser == 'Key Man'){
						$schedulestarttime = $val->schedulestarttime;
						$scheduleendtime = $val->scheduleendtime;
						$startpoletime = $val->startpoletime;
						$endpointtime = $val->endpointtime;
						$startpole = $val->startpole;
						$endpole = $val->endpole;
					}
					else{
						$schedulestarttime = 'NA';
						$scheduleendtime = 'NA';
						$startpoletime = 'NA';
						$endpointtime = 'NA';
						$startpole = 'NA';
						$endpole = 'NA';
					}
					$dat[$Key+1]['A'] = $val->typeofuser;
					$dat[$Key+1]['B'] = $val->devicealiasname;
					$dat[$Key+1]['C'] = $val->devicename;
					$dat[$Key+1]['D'] = $startpole;
					$dat[$Key+1]['E'] = $endpole;
					$dat[$Key+1]['F'] = $schedulestarttime;
					$dat[$Key+1]['G'] = $scheduleendtime;
					$dat[$Key+1]['H'] = $startpoletime;
					$dat[$Key+1]['I'] = $endpointtime;
				}
				
				$filename = 'Trip_Deviation_Date_Wise_Report_'.$data['pwi_name'].'_'.time().'.xlsx';
			}
			
			exceldownload($dat, $filename);
		}			
	}

    public function stoppagereportpdf()
    {
        $user_id = $this->sessdata['user_id'];
        $group_id = $this->sessdata['group_id'];
        $devices = $alerts = $geofences = $routes = $dids = null;
        
        if ($this->request->getPost()) {
            $data = [];
            $report_type = trim($this->request->getPost('report_type'));

            // Date handling
            $date_from = Time::parse(trim($this->request->getPost('date_from')))->toDateString();
            $date_to = Time::parse(trim($this->request->getPost('date_to')))->toDateString();
            $time_from = "00:00:00";
            $time_to = "23:59:00";
            $data['date_from'] = Time::parse(trim($this->request->getPost('date_from')))->format('d-m-Y H:i');
            $data['date_to'] = Time::parse(trim($this->request->getPost('date_to')))->format('d-m-Y H:i');

            // Additional fields
            $data['pwi_id'] = $pwi_id = trim($this->request->getPost('user'));
            $data['pwi_name'] = trim($this->request->getPost('pwi_name'));
            $data['schema'] = $this->schema; // This should be defined as per your application context
            
            // Setting up devices string
            $devices .= "{";

            if ($data['pwi_name'] == 'All') {
                $query = "SELECT sup_pid, sup_gid, serial_no, imei_no, did, mobile_no, warranty_date, linked, parent_id, user_id, issudate,
                    refunddate, active, issold, apply_scheam, group_id, role_id, email, address, pincode, state_name, country, username, firstname,
                    lastname, organisation, group_name, '' AS list_item, '' AS list_item_name 
                    FROM public.get_divice_details_record_for_list('{$this->schema}', $user_id) 
                    WHERE sup_gid = $group_id AND user_id = $user_id 
                    ORDER BY did ASC";
                $query = $this->db->query($query);
            } else {
                $query = "SELECT sup_pid, sup_gid, serial_no, imei_no, did, mobile_no, warranty_date, linked, parent_id, user_id, issudate,
                    refunddate, active, issold, apply_scheam, group_id, role_id, email, address, pincode, state_name, country, username, firstname,
                    lastname, organisation, group_name, list_item, list_item_name 
                    FROM ( 
                        SELECT sup_pid, sup_gid, serial_no, imei_no, did, mobile_no, warranty_date, linked, parent_id, user_id, issudate,
                        refunddate, active, issold, apply_scheam, group_id, role_id, email, address, pincode, state_name, country, username, firstname,
                        lastname, organisation, group_name, '' AS list_item1, '' AS list_item_name2 
                        FROM public.get_divice_details_record_for_list('{$this->schema}', $pwi_id) 
                    ) ss 
                    INNER JOIN (
                        SELECT serial_no AS top_serial_no, imei_no AS top_imei_no, MAX(issudate) AS max_issdate,
                            STRING_AGG(group_name, ' - ') AS list_item,
                            STRING_AGG(organisation, ' - ') AS list_item_name
                        FROM public.get_divice_details_record_for_list('{$this->schema}', $pwi_id) 
                        GROUP BY serial_no, imei_no
                    ) ssi ON ss.issudate = ssi.max_issdate AND ss.serial_no = ssi.top_serial_no
                    ORDER BY issudate ASC";
                $query = $this->db->query($query);
            }

            $devicelist = $query->getResult();
            if (count($devicelist) > 0) {
                foreach ($devicelist as $devicelist_each) {
                    if ($devices == "{") {
                        $devices .= $devicelist_each->did;
                        $dids .= $devicelist_each->did;
                    } else {
                        $devices .= "," . $devicelist_each->did;
                        $dids .= "," . $devicelist_each->did;
                    }
                }
            }
            $devices .= "}";

            if ($report_type == 7) {
                $device_id = trim($this->request->getPost('device_id'));
                if (!empty($device_id)) {
                    $dids = $device_id;
                }

                $data['alldata'] = $this->db->query("SELECT mddd, mdddevicename, mddserialno, divicename, result_date, deviceid, acting_trip, start_time, endtime,
                    duration, distance_cover, sos_no, alert_no, call_no, totalnoofstop, totalstoptime, polno, rd, dv, devicename, acting_triped, 
                    starttime, end_time, duration1, distancecover, sosno, alertno, callno, totalnoof_stop, totalsto_ptime, pol_no, polename1, 
                    polnoend, polenameend1, polename, polenameend, genid 
                    FROM (
                        SELECT DISTINCT deviceid AS mddd,
                            (SELECT device_name FROM {$this->schema}.master_device_setup WHERE deviceid = ax.deviceid AND id = (SELECT MAX(id) FROM {$this->schema}.master_device_setup WHERE inserttime::date<='$date_to'::date AND deviceid = ax.deviceid)) AS mdddevicename,
                            (SELECT COALESCE(serial_no, 'Absent') FROM {$this->schema}.master_device_details WHERE superdevid = ax.deviceid OR id = ax.deviceid) AS mddserialno
                        FROM {$this->schema}.master_device_assign AS ax 
                        WHERE deviceid IN ($dids) AND group_id = 2
                    ) masterdevice
                    LEFT OUTER JOIN (
                        SELECT * 
                        FROM public.trip_spesified_device 
                        WHERE (genid, deviceid, result_date, acting_trip) IN (
                            SELECT MAX(genid), deviceid, result_date, acting_trip
                            FROM public.trip_spesified_device 
                            WHERE deviceid IN ($dids) 
                            AND (result_date || ' ' || start_time >= '$date_from $time_from'
                                AND result_date || ' ' || end_time <= '$date_to $time_to')
                            GROUP BY deviceid, result_date, acting_trip
                        )
                        ORDER BY devicename, result_date, acting_trip
                    ) resultdevice ON masterdevice.mddd = resultdevice.deviceid
                    ORDER BY result_date, mddd ASC")->getResult();

                $html = view('traxreport/pdf_devicestatus', $data);
                $filename = 'Device_Status_Report_' . $data['pwi_name'] . '_' . time();
            }
            if($report_type == 8){                
                $data['alldata'] = [];
                
                // Load the database service and perform the query
                $builder = $this->db->query("SELECT typeofuser, deviceid, devicename, sessiondate, sesectionstartttime, sesectionendttime, starttime, endtime, duration, orginallength, 
                                                withinpolelength as totalwitpole, polesequence, 
                                                (SELECT device_name FROM ".$this->schema.".master_device_setup WHERE deviceid = mdd.superdevid 
                                                    AND id = (SELECT max(id) FROM ".$this->schema.".master_device_setup 
                                                    WHERE inserttime::date <= '$date_from'::date AND deviceid = mdd.superdevid)) AS mdddevicename, 
                                                mdd.serial_no 
                                            FROM public.analysish_patrol_man_and_key_man_work_patrol('$date_from') AS ax 
                                            RIGHT JOIN ".$this->schema.".master_device_details AS mdd 
                                            ON (ax.deviceid = mdd.superdevid) 
                                            WHERE mdd.superdevid IN ($dids)");

                $result = $builder->getResult();
                $deviceserial = '';
                $i = 0;

                // Loop through the result set and build data
                foreach($result as $result_each){
                    if($deviceserial != $result_each->serial_no){
                        if($deviceserial == ''){
                            $i = 0;
                        }
                        else{
                            $i++;
                        }
                        $deviceserial = $result_each->serial_no;
                        if($result_each->deviceid != ''){
                            $data['alldata'][$i]->typeofuser = $result_each->typeofuser;
                            $data['alldata'][$i]->deviceid = $result_each->deviceid;
                            $data['alldata'][$i]->devicealiasname = $result_each->mdddevicename;
                            $data['alldata'][$i]->devicename = $result_each->serial_no;
                            $data['alldata'][$i]->sessiondate = $result_each->sessiondate;
                            $data['alldata'][$i]->starttime = $result_each->starttime;
                            $data['alldata'][$i]->endtime = $result_each->endtime;
                            $data['alldata'][$i]->duration = $result_each->duration;
                            $data['alldata'][$i]->distance = $result_each->totalwitpole;
                            $data['alldata'][$i]->orginallength = $result_each->orginallength;
                        }
                        else{
                            $data['alldata'][$i]->typeofuser = 'NA';
                            $data['alldata'][$i]->deviceid = 'NA';
                            $data['alldata'][$i]->devicealiasname = $result_each->mdddevicename;
                            $data['alldata'][$i]->devicename = $result_each->serial_no;
                            $data['alldata'][$i]->sessiondate = $result_each->sessiondate;
                            $data['alldata'][$i]->starttime = '00:00:00';
                            $data['alldata'][$i]->endtime = '00:00:00';
                            $data['alldata'][$i]->duration = 'NA';
                            $data['alldata'][$i]->distance = $result_each->totalwitpole;
                            $data['alldata'][$i]->orginallength = $result_each->orginallength;
                        }
                    }
                    else{
                        $data['alldata'][$i]->distance = ($data['alldata'][$i]->distance + $result_each->totalwitpole);
                    }
                }

                // Load the view to generate the PDF
                $html = view('traxreport/pdf_trip', $data);
                
                // Set the filename
                $filename = 'Trip_Date_Wise_Report_'.$data['pwi_name'].'_'.time();
            }
            if ($report_type == 9) {
                // Raw SQL query
                $sql = "SELECT mddd, mdddevicename, mddserialno, divicename, result_date, deviceid, acting_trip, 
                    start_time, endtime, duration, distance_cover, sos_no, alert_no, call_no, 
                    totalnoofstop, totalstoptime, polno, rd, dv, devicename, acting_triped, starttime, 
                    end_time, duration1, distancecover, sosno, alertno, callno, totalnoof_stop, 
                    totalsto_ptime, pol_no, polename1, polnoend, polenameend1, polename, polenameend, genid 
                FROM (
                    SELECT DISTINCT deviceid AS mddd,
                        (SELECT device_name 
                            FROM {$this->schema}.master_device_setup 
                            WHERE deviceid = ax.deviceid 
                            AND id = (SELECT max(id) 
                                    FROM {$this->schema}.master_device_setup 
                                    WHERE to_timestamp(inserttime) <= '$date_to'::timestamp  
                                    AND deviceid = ax.deviceid )) AS mdddevicename,
                        (SELECT COALESCE(serial_no,'Absent')  
                            FROM {$this->schema}.master_device_details  
                            WHERE superdevid = ax.deviceid OR id = ax.deviceid) AS mddserialno   
                    FROM {$this->schema}.master_device_assign AS ax 
                    WHERE deviceid IN ($dids) 
                    AND group_id = 2 
                ) masterdevice 
                LEFT OUTER JOIN (
                    SELECT * 
                    FROM public.trip_spesified_device 
                    WHERE (genid, deviceid, result_date, acting_trip) 
                    IN (
                        SELECT max(genid), deviceid, result_date, acting_trip 
                        FROM public.trip_spesified_device 
                        WHERE deviceid IN ($dids) 
                        AND (result_date || ' ' || start_time >= '$date_from $time_from' 
                            AND result_date || ' ' || end_time <= '$date_to $time_to') 
                        GROUP BY deviceid, result_date, acting_trip
                    ) 
                    ORDER BY devicename, result_date, acting_trip
                ) resultdivice 
                ON masterdevice.mddd = resultdivice.deviceid
                ORDER BY result_date, mddd ASC";
                
                // Execute the query
                $data['alldata'] = $this->db->query($sql)->getResult();
    
                // Load the view
                $html = view('traxreport/pdf_movementsummery', $data);
    
                // Define filename for the report
                $filename = 'Movement_Summery_Report_' . $data['pwi_name'] . '_' . time();
            }
            if($report_type == 10){
                if($this->sessdata['group_id'] == 2){
                    $data['alldata'] = $this->db->query("select * from public.get_divice_details_record_for_list('".$this->schema."',".$this->sessdata['user_id'].") where did in ($dids) and (issudate::date between '".$date_from."' and '".$date_to."')")->getResult();
                }
                else{
                    $data['alldata'] = $this->db->query("select * from get_divice_details_record_for_list_for_company('".$this->schema."',".$this->sessdata['user_id'].") where did in ($dids) and (issudate::date between '".$date_from."' and '".$date_to."') and sup_gid = 2")->getResult();
                }
                $html = $this->load->view('traxreport/pdf_allotment', $data, true);
                $filename = 'Device_Allotment_Report_'.$data['pwi_name'].'_'.time();
            }
            if($report_type == 11){
                $data['alldata'] = array();
                $result = $this->db->query("select typeofuser, deviceid, devicename, sesectionstartttime, sesectionendttime, starttime, endtime, duration, startpoletime, startpole, endpointtime, endpoint, 
                (SELECT device_name FROM ".$this->sessdata->schemaname.".master_device_setup where deviceid=mdd.superdevid and id=(SELECT max(id) FROM ".$this->sessdata->schemaname.".master_device_setup where inserttime::date<='$date_from'::date and deviceid=mdd.superdevid )) as mdddevicename, mdd.serial_no 
                from public.analysish_work_patrol_time_schudele('$date_from') as ax 
                right join ".$this->sessdata->schemaname.".master_device_details as mdd 
                on (ax.deviceid = mdd.superdevid) where mdd.superdevid in ($dids)")->getResult();
                
                $deviceserial = '';
                $i = 0;
                foreach($result as $result_each){
                    if (strpos($result_each->mdddevicename, 'Patrolman') !== false) {
                        $data['alldata'][$i]->typeofuser = $result_each->typeofuser;
                        $data['alldata'][$i]->deviceid = $result_each->deviceid;
                        $data['alldata'][$i]->devicealiasname = $result_each->mdddevicename;
                        $data['alldata'][$i]->devicename = $result_each->serial_no;
                        $data['alldata'][$i]->sessiondate = $result_each->sessiondate;
                        $data['alldata'][$i]->schedulestarttime = date('d-m-Y H:i:s', strtotime($result_each->starttime));
                        $data['alldata'][$i]->scheduleendtime = date('d-m-Y H:i:s', strtotime($result_each->endtime));
                        $data['alldata'][$i]->startpoletime = ($result_each->startpoletime == '2000-10-01 00:00:00') ? 'NA' : date('d-m-Y H:i:s', strtotime($result_each->startpoletime));
                        $data['alldata'][$i]->endpointtime = ($result_each->endpointtime == '2000-10-01 00:00:00') ? 'NA' : date('d-m-Y H:i:s', strtotime($result_each->endpointtime));
                        $data['alldata'][$i]->startpole = $result_each->startpole;
                        $data['alldata'][$i]->endpole = $result_each->endpoint;
                        $i++;
                    }
                }
                $html = $this->load->view('traxreport/pdf_trip_deviation', $data, true);
                $filename = 'Trip_Deviation_Date_Wise_Report_'.$data['pwi_name'].'_'.time();
            }            

            // Instantiate the MakePDF class
            $pdf = new MakePDF();

            // Set the filename and content
            $pdf->setFileName($filename);
            $pdf->setContent($html);

            // Generate and stream the PDF to the browser
            $pdf->getPdf();  // true to stream the PDF
        }
    }

    public function devallotreport_old()
    {
        // Check if user is logged in
        if (!session()->has('login_sess_data')) {
            return redirect()->to("/");
        }

        $data = [];
        $data['sessdata'] = $this->sessdata;
        $data['page_title'] = "Device Allotment Report";
        $data['usersdd'] = $this->commonModel->get_users(); // Updated to use the CI4 model
        $data['reporttype'] = $this->db->table('public.report_type')
            ->where('active', 1)
            ->whereNotIn('id', [1, 2, 3, 4, 5, 6, 8, 11])
            ->get()
            ->getResult();
        
        $data['date_from'] = '';
        $data['date_to'] = '';
        $data['report_type'] = 7;
        $data['pway'] = $this->db->table('public.user_login')
            ->select('organisation, user_id')
            ->where('active', 1)
            ->where('group_id', 8)
            ->get()
            ->getResult();
        
        $data['sse_pwy'] = $sse_pwy = trim($this->request->getPost('pway_id'));

        // Device dropdown based on user group
        if ($this->sessdata['group_id'] == 3) { // Distributor
            $data['devicedropdown'] = $this->db->query("
                SELECT a.*, 
                    (SELECT device_name 
                    FROM {$this->schema}.master_device_setup  
                    WHERE id = (SELECT MAX(id) 
                                FROM {$this->schema}.master_device_setup 
                                WHERE inserttime::date <= current_date::date 
                                AND deviceid = a.did)) AS device_name 
                FROM public.get_divice_details_record_for_list_for_company('{$this->schema}', {$this->sessdata['user_id']}) AS a 
                WHERE a.group_id = 2 AND a.active = 1
            ")->getResult();
        } else { // Others
            $data['devicedropdown'] = $this->db->query("
                SELECT a.*, 
                    (SELECT device_name 
                    FROM {$this->schema}.master_device_setup  
                    WHERE id = (SELECT MAX(id) 
                                FROM {$this->schema}.master_device_setup 
                                WHERE inserttime::date <= current_date::date 
                                AND deviceid = a.did)) AS device_name 
                FROM public.get_divice_details_record_for_list_for_company('{$this->schema}', {$this->sessdata['user_id']}) AS a 
                WHERE a.group_id = 2 AND a.active = 1
            ")->getResult();
        }

        if ($this->request->getMethod() == 'POST') {
            $user_id = $this->sessdata['user_id'];
            $group_id = $this->sessdata['group_id'];
            $devices = $alerts = $geofences = $routes = $dids = null;

            $data['report_type'] = $report_type = trim($this->request->getPost('report_type'));

            $date_from = date("Y-m-d", strtotime(trim($this->request->getPost('date_from'))));
            $date_to = date("Y-m-d", strtotime(trim($this->request->getPost('date_to'))));
            $time_from = "00:00:00";
            $time_to = "23:59:00";

            $data['date_from'] = date("d-m-Y H:i", strtotime(trim($this->request->getPost('date_from'))));
            $data['date_to'] = date("d-m-Y H:i", strtotime(trim($this->request->getPost('date_to'))));

            $data['pwi_id'] = $pwi_id = trim($this->request->getPost('user'));
            $data['pwi_name'] = trim($this->request->getPost('pwi_name'));
            $data['schema'] = $this->schema;
            $data['sse_pwy'] = $sse_pwy = trim($this->request->getPost('pway_id'));

            $devices .= "{";
            if ($data['pwi_name'] == 'All') {
                if ($sse_pwy == 'All') {
                    $query = "SELECT sup_pid, sup_gid, serial_no, imei_no, did, mobile_no, warranty_date, linked, parent_id, user_id, issudate,
                            refunddate, active, issold, apply_scheam, group_id, role_id, email, address, pincode, state_name, country, username, firstname,
                            lastname, organisation, group_name, '' AS list_item, '' AS list_item_name 
                            FROM public.get_divice_details_record_for_list('{$this->schema}',{$this->sessdata['user_id']}) 
                            WHERE sup_gid = $group_id AND user_id = $user_id AND active = 1 
                            ORDER BY did ASC";
                    $devicelist = $this->db->query($query)->getResult();
                } else {
                    $sectionlist = $this->db->table('public.user_login')
                        ->select('user_id')
                        ->where('parent_id', $sse_pwy)
                        ->where('active', 1)
                        ->get()
                        ->getResult();

                    $devicelist = [];
                    foreach ($sectionlist as $section) {
                        $sectionid = $section->user_id;
                        $query = "SELECT sup_pid, sup_gid, serial_no, imei_no, did, mobile_no, warranty_date, linked, parent_id, user_id, issudate,
                                refunddate, active, issold, apply_scheam, group_id, role_id, email, address, pincode, state_name, country, username, firstname,
                                lastname, organisation, group_name, '' AS list_item, '' AS list_item_name 
                                FROM public.get_divice_details_record_for_list('{$this->schema}',{$sectionid}) 
                                WHERE user_id = $sectionid AND active = 1 
                                ORDER BY issudate ASC";
                        $lists = $this->db->query($query)->getResult();
                        $devicelist = array_merge($devicelist, $lists);
                    }
                }
            } else {
                $query = "SELECT sup_pid, sup_gid, serial_no, imei_no, did, mobile_no, warranty_date, linked, parent_id, user_id, issudate,
                        refunddate, active, issold, apply_scheam, group_id, role_id, email, address, pincode, state_name, country, username, firstname,
                        lastname, organisation, group_name, '' AS list_item, '' AS list_item_name 
                        FROM public.get_divice_details_record_for_list('".$this->schema."',".$pwi_id.") 
                        WHERE user_id = {$pwi_id} AND active = 1 
                        ORDER BY issudate ASC";
                $devicelist = $this->db->query($query)->getResult();
            }

            if (count($devicelist) > 0) {
                foreach ($devicelist as $devicelist_each) {
                    if ($devices == "{") {
                        $devices .= $devicelist_each->did;
                        $dids .= $devicelist_each->did;
                    } else {
                        $devices .= "," . $devicelist_each->did;
                        $dids .= "," . $devicelist_each->did;
                    }
                }
            }
            $devices .= "}";

            if ($report_type == 7) {
                $data['device_id'] = $device_id = trim($this->request->getPost('device_id'));
                if (!empty($device_id)) {
                    $dids = $device_id;
                }
                if (empty($dids)) {
                    $dids = 0;
                }

                $data['alldata'] = $this->db->query("
                    SELECT mddd, mdddevicename, mddserialno, divicename, result_date, deviceid, acting_trip, start_time, endtime, duration, distance_cover, sos_no, alert_no, call_no, totalnoofstop, totalstoptime, polno, rd, dv, devicename, acting_triped, starttime, end_time, duration1, distancecover, sosno, alertno, callno, totalnoof_stop, totalsto_ptime, pol_no, polename1, polnoend, polenameend1, polename, polenameend, genid 
                    FROM (
                        SELECT DISTINCT deviceid AS mddd,
                            (SELECT device_name FROM ".$this->schema.".master_device_setup 
                            WHERE deviceid = ax.deviceid 
                            AND id = (SELECT MAX(id) FROM ".$this->schema.".master_device_setup 
                                        WHERE inserttime::date <= '$date_to'::date AND deviceid = ax.deviceid)) AS mdddevicename,
                            (SELECT COALESCE(serial_no, 'Absent') FROM ".$this->schema.".master_device_details 
                            WHERE superdevid = ax.deviceid OR id = ax.deviceid) AS mddserialno   
                        FROM ".$this->schema.".master_device_assign AS ax 
                        WHERE deviceid IN ($dids) AND group_id = 2
                    ) masterdevice 
                    LEFT OUTER JOIN (
                        SELECT * 
                        FROM public.trip_spesified_device 
                        WHERE (genid, deviceid, result_date, acting_trip) IN (
                            SELECT MAX(genid), deviceid, result_date, acting_trip 
                            FROM public.trip_spesified_device 
                            WHERE deviceid IN ($dids) 
                            AND (result_date || ' ' || start_time >= '{$date_from} {$time_from}' 
                            AND result_date || ' ' || end_time <= '{$date_to} {$time_to}') 
                            GROUP BY deviceid, result_date, acting_trip
                        ) 
                        ORDER BY devicename, result_date, acting_trip
                    ) resultdivice ON masterdevice.mddd = resultdivice.deviceid
                ")->getResult();

                // Additional processing for alldata
                for ($i = 0; $i < count($data['alldata']); $i++) {
                    $serialno = $data['alldata'][$i]->mddserialno;
                    $device_assign_details = $this->db->query("
                        SELECT a.parent_user_id, a.current_user_id, b.organisation AS pwy,
                            c.organisation AS section, d.startpole, d.stoppol
                        FROM public.device_asign_details AS a
                        LEFT JOIN public.user_login AS b ON (a.parent_user_id = b.user_id)
                        LEFT JOIN public.user_login AS c ON (a.current_user_id = c.user_id)
                        LEFT JOIN {$this->schema}.device_assigne_pole_data AS d ON (a.serial_no = d.diviceno)
                        WHERE a.serial_no = '".$serialno."'
                        LIMIT 1
                    ")->getResult();

                    if (!empty($device_assign_details)) {
                        $data['alldata'][$i]->pwy = $device_assign_details[0]->pwy;
                        $data['alldata'][$i]->section = $device_assign_details[0]->section;
                    }

                    $devicename = $data['alldata'][$i]->mdddevicename;
                    $devicenameArr = explode(':', $devicename);
                    if(count($devicenameArr) > 1) {
                        $poledetailsnew = trim($devicenameArr[1]);
                        $poledetailsnewArr = explode('(', $poledetailsnew);
                        $polenamenew = trim($poledetailsnewArr[1]);
                        $polenamenew = str_replace(')', '', $polenamenew);
                        $polenamenewArr = explode('-', $polenamenew);
                        $data['alldata'][$i]->startpole = $polenamenewArr[0];
                        $data['alldata'][$i]->stoppol = $polenamenewArr[1];
                        $data['alldata'][$i]->bit = $data['alldata'][$i]->startpole . '-' . $data['alldata'][$i]->stoppol;
                    } else {
                        $data['alldata'][$i]->startpole = '';
                        $data['alldata'][$i]->stoppol = '';
                        $data['alldata'][$i]->bit = '';
                    }
                    
                }
            }
        }
        // echo $data['pwi_id']. $data['pwi_name']; exit();
        $data['middle'] = view('traxreport/devallotreport_view', $data);
        return view('mainlayout', $data);
    }

    public function devallotreport()
    {
        // Check if user is logged in
        if (!session()->has('login_sess_data')) {
            return redirect()->to("/");
        }

        $data = [];
        $data['sessdata'] = $this->sessdata;
        $data['page_title'] = "Device Allotment Report";
        $data['usersdd'] = $this->commonModel->get_users(); // Updated to use the CI4 model
        $data['reporttype'] = $this->db->table('public.report_type')
            ->where('active', 1)
            ->whereNotIn('id', [1, 2, 3, 4, 5, 6, 8, 11])
            ->get()
            ->getResult();
        
        $data['date_from'] = '';
        $data['date_to'] = '';
        $data['report_type'] = 7;
        $data['pway'] = $this->db->table('public.user_login')
            ->select('organisation, user_id')
            ->where('active', 1)
            ->where('group_id', 8)
            ->get()
            ->getResult();
        
        $data['sse_pwy'] = $sse_pwy = trim($this->request->getPost('pway_id'));

        // Device dropdown based on user group
        if ($this->sessdata['group_id'] == 3) { // Distributor
            $data['devicedropdown'] = $this->db->query("
                SELECT a.*, 
                    (SELECT device_name 
                    FROM {$this->schema}.master_device_setup  
                    WHERE id = (SELECT MAX(id) 
                                FROM {$this->schema}.master_device_setup 
                                WHERE inserttime::date <= current_date::date 
                                AND deviceid = a.did)) AS device_name 
                FROM public.get_divice_details_record_for_list_for_company('{$this->schema}', {$this->sessdata['user_id']}) AS a 
                WHERE a.group_id = 2 AND a.active = 1
            ")->getResult();
        } else { // Others
            $data['devicedropdown'] = $this->db->query("
                SELECT a.*, 
                    (SELECT device_name 
                    FROM {$this->schema}.master_device_setup  
                    WHERE id = (SELECT MAX(id) 
                                FROM {$this->schema}.master_device_setup 
                                WHERE inserttime::date <= current_date::date 
                                AND deviceid = a.did)) AS device_name 
                FROM public.get_divice_details_record_for_list_for_company('{$this->schema}', {$this->sessdata['user_id']}) AS a 
                WHERE a.group_id = 2 AND a.active = 1
            ")->getResult();
        }

        if ($this->request->getMethod() == 'POST') {
            $user_id = $this->sessdata['user_id'];
            $group_id = $this->sessdata['group_id'];
            $devices = $alerts = $geofences = $routes = $dids = null;

            $data['report_type'] = $report_type = trim($this->request->getPost('report_type'));

            $date_from = date("Y-m-d", strtotime(trim($this->request->getPost('date_from'))));
            $date_to = date("Y-m-d", strtotime(trim($this->request->getPost('date_to'))));
            $time_from = "00:00:00";
            $time_to = "23:59:00";

            $data['date_from'] = date("d-m-Y H:i", strtotime(trim($this->request->getPost('date_from'))));
            $data['date_to'] = date("d-m-Y H:i", strtotime(trim($this->request->getPost('date_to'))));

            $data['pwi_id'] = $pwi_id = trim($this->request->getPost('user'));
            $data['pwi_name'] = trim($this->request->getPost('pwi_name'));
            $data['schema'] = $this->schema;
            $data['sse_pwy'] = $sse_pwy = trim($this->request->getPost('pway_id'));

            $devices .= "{";
            if ($data['pwi_name'] == 'All') {
                if ($sse_pwy == 'All') {
                    $query = "SELECT sup_pid, sup_gid, serial_no, imei_no, did, mobile_no, warranty_date, linked, parent_id, user_id, issudate,
                            refunddate, active, issold, apply_scheam, group_id, role_id, email, address, pincode, state_name, country, username, firstname,
                            lastname, organisation, group_name, '' AS list_item, '' AS list_item_name 
                            FROM public.get_divice_details_record_for_list('{$this->schema}',{$this->sessdata['user_id']}) 
                            WHERE sup_gid = $group_id AND user_id = $user_id AND active = 1 
                            ORDER BY did ASC";
                    $devicelist = $this->db->query($query)->getResult();
                } else {
                    $sectionlist = $this->db->table('public.user_login')
                        ->select('user_id')
                        ->where('parent_id', $sse_pwy)
                        ->where('active', 1)
                        ->get()
                        ->getResult();

                    $devicelist = [];
                    foreach ($sectionlist as $section) {
                        $sectionid = $section->user_id;
                        $query = "SELECT sup_pid, sup_gid, serial_no, imei_no, did, mobile_no, warranty_date, linked, parent_id, user_id, issudate,
                                refunddate, active, issold, apply_scheam, group_id, role_id, email, address, pincode, state_name, country, username, firstname,
                                lastname, organisation, group_name, '' AS list_item, '' AS list_item_name 
                                FROM public.get_divice_details_record_for_list('{$this->schema}',{$sectionid}) 
                                WHERE user_id = $sectionid AND active = 1 
                                ORDER BY issudate ASC";
                        $lists = $this->db->query($query)->getResult();
                        $devicelist = array_merge($devicelist, $lists);
                    }
                }
            } else {
                $query = "SELECT sup_pid, sup_gid, serial_no, imei_no, did, mobile_no, warranty_date, linked, parent_id, user_id, issudate,
                        refunddate, active, issold, apply_scheam, group_id, role_id, email, address, pincode, state_name, country, username, firstname,
                        lastname, organisation, group_name, '' AS list_item, '' AS list_item_name 
                        FROM public.get_divice_details_record_for_list('".$this->schema."',".$pwi_id.") 
                        WHERE user_id = {$pwi_id} AND active = 1 
                        ORDER BY issudate ASC";
                $devicelist = $this->db->query($query)->getResult();
            }

            if (count($devicelist) > 0) {
                foreach ($devicelist as $devicelist_each) {
                    if ($devices == "{") {
                        $devices .= $devicelist_each->did;
                        $dids .= $devicelist_each->did;
                    } else {
                        $devices .= "," . $devicelist_each->did;
                        $dids .= "," . $devicelist_each->did;
                    }
                }
            }
            $devices .= "}";

            if ($report_type == 7) {
                $data['device_id'] = $device_id = trim($this->request->getPost('device_id'));
                if (!empty($device_id)) {
                    $dids = $device_id;
                }
                if (empty($dids)) {
                    $dids = 0;
                }

                $data['alldata'] = $this->db->query("
                    SELECT mddd, mdddevicename, mddserialno, divicename, result_date, deviceid, acting_trip, start_time, endtime, duration, distance_cover, sos_no, alert_no, call_no, totalnoofstop, totalstoptime, polno, rd, dv, devicename, acting_triped, starttime, end_time, duration1, distancecover, sosno, alertno, callno, totalnoof_stop, totalsto_ptime, pol_no, polename1, polnoend, polenameend1, polename, polenameend, genid 
                    FROM (
                        SELECT DISTINCT deviceid AS mddd,
                            (SELECT device_name FROM ".$this->schema.".master_device_setup 
                            WHERE deviceid = ax.deviceid 
                            AND id = (SELECT MAX(id) FROM ".$this->schema.".master_device_setup 
                                        WHERE inserttime::date <= '$date_to'::date AND deviceid = ax.deviceid)) AS mdddevicename,
                            (SELECT COALESCE(serial_no, 'Absent') FROM ".$this->schema.".master_device_details 
                            WHERE superdevid = ax.deviceid OR id = ax.deviceid) AS mddserialno   
                        FROM ".$this->schema.".master_device_assign AS ax 
                        WHERE deviceid IN ($dids) AND group_id = 2
                    ) masterdevice 
                    LEFT OUTER JOIN (
                        SELECT * 
                        FROM public.trip_spesified_device 
                        WHERE (genid, deviceid, result_date, acting_trip) IN (
                            SELECT MAX(genid), deviceid, result_date, acting_trip 
                            FROM public.trip_spesified_device 
                            WHERE deviceid IN ($dids) 
                            AND (result_date || ' ' || start_time >= '{$date_from} {$time_from}' 
                            AND result_date || ' ' || end_time <= '{$date_to} {$time_to}') 
                            GROUP BY deviceid, result_date, acting_trip
                        ) 
                        ORDER BY devicename, result_date, acting_trip
                    ) resultdivice ON masterdevice.mddd = resultdivice.deviceid
                ")->getResult();

                // Additional processing for alldata
                for ($i = 0; $i < count($data['alldata']); $i++) {
                    $serialno = $data['alldata'][$i]->mddserialno;
                    $device_assign_details = $this->db->query("
                        SELECT a.parent_user_id, a.current_user_id, b.organisation AS pwy,
                            c.organisation AS section, d.startpole, d.stoppol
                        FROM public.device_asign_details AS a
                        LEFT JOIN public.user_login AS b ON (a.parent_user_id = b.user_id)
                        LEFT JOIN public.user_login AS c ON (a.current_user_id = c.user_id)
                        LEFT JOIN {$this->schema}.device_assigne_pole_data AS d ON (a.serial_no = d.diviceno)
                        WHERE a.serial_no = '".$serialno."'
                        LIMIT 1
                    ")->getResult();

                    if (!empty($device_assign_details)) {
                        $data['alldata'][$i]->pwy = $device_assign_details[0]->pwy;
                        $data['alldata'][$i]->section = $device_assign_details[0]->section;
                    }

                    $devicename = $data['alldata'][$i]->mdddevicename;
                    $devicenameArr = explode(':', $devicename);
                    if(count($devicenameArr) > 1) {
                        $poledetailsnew = trim($devicenameArr[1]);
                        $poledetailsnewArr = explode('(', $poledetailsnew);
                        $polenamenew = trim($poledetailsnewArr[1]);
                        $polenamenew = str_replace(')', '', $polenamenew);
                        $polenamenewArr = explode('-', $polenamenew);
                        if(count($polenamenewArr) > 1) {
                            $data['alldata'][$i]->startpole = $polenamenewArr[0];
                            $data['alldata'][$i]->stoppol = $polenamenewArr[1];
                            $data['alldata'][$i]->bit = $data['alldata'][$i]->startpole . '-' . $data['alldata'][$i]->stoppol;
                        } else {
                            $data['alldata'][$i]->startpole = '';
                            $data['alldata'][$i]->stoppol = '';
                            $data['alldata'][$i]->bit = '-';
                        }
                        
                    } else {
                        $data['alldata'][$i]->startpole = '';
                        $data['alldata'][$i]->stoppol = '';
                        $data['alldata'][$i]->bit = '';
                    }
                    
                }
            }
        }
        // echo $data['pwi_id']. $data['pwi_name']; exit();
        // echo "<pre>"; print_r($data['alldata']);exit();

        $data['middle'] = view('traxreport/devallotreport_view', $data);
        return view('mainlayout', $data);
    }
    
    public function devallotreportexcel()
    {
        // Set execution time and memory limit
        ini_set('max_execution_time', 3000);
        ini_set('memory_limit', '-1');

        // Check if POST data is available
        if ($this->request->getPost()) {
            $dat = [];
            // Fetch user and group data from the session
            $user_id = $this->sessdata['user_id'];
            $group_id = $this->sessdata['group_id'];

            // Initialize variables
            $devices = $alerts = $geofences = $routes = $dids = null;

            // Retrieve 'report_type' from POST data
            $data['report_type'] = $report_type = trim($this->request->getPost('report_type'));

            // Handle the 'date_from' and 'date_to' using CodeIgniter 4's Time class for date handling
            $date_from = new Time(trim($this->request->getPost('date_from')), 'UTC');
            $date_to = new Time(trim($this->request->getPost('date_to')), 'UTC');
            $time_from = "00:00:00";
            $time_to = "23:59:00";

            // Format date for display purposes
            $data['date_from'] = $date_from->format('d-m-Y H:i');
            $data['date_to'] = $date_to->format('d-m-Y H:i');

            // Retrieve additional parameters from POST data
            $data['pwi_id'] = $pwi_id = trim($this->request->getPost('section_id'));
            $data['pwi_name'] = trim($this->request->getPost('pwi_name'));
            $data['schema'] = $this->schema;
            $sse_pwy = trim($this->request->getPost('pway_id'));

            // Initialize the devices list as an empty JSON string
            $devices = "{";

            // Database query for fetching device data
            if ($data['pwi_name'] == 'All') {
                // If 'pwi_name' is "All", fetch devices for the group or user
                if ($sse_pwy == 'All') {
                    $query = "SELECT sup_pid, sup_gid, serial_no, imei_no, did, mobile_no, warranty_date, linked, 
                              parent_id, user_id, issudate, refunddate, active, issold, apply_scheam, group_id, 
                              role_id, email, address, pincode, state_name, country, username, firstname, 
                              lastname, organisation, group_name, '' AS list_item, '' AS list_item_name
                              FROM public.get_divice_details_record_for_list('{$this->schema}', $user_id)
                              WHERE sup_gid = $group_id AND user_id = $user_id AND active = 1
                              ORDER BY did ASC";

                    // Fetch devices using parameterized query
                    $devicelist = $this->db->query($query)->getResult();
                } else {
                    // Fetch section list for specific parent user
                    $sectionlist = $this->db->query("SELECT user_id FROM public.user_login WHERE parent_id = :sse_pwy AND active = 1", [
                        'sse_pwy' => $sse_pwy
                    ])->getResult();

                    $devicelist = [];
                    foreach ($sectionlist as $section) {
                        $sectionid = $section->user_id;

                        // Fetch devices for each section user
                        $query = "SELECT sup_pid, sup_gid, serial_no, imei_no, did, mobile_no, warranty_date, linked, 
                                  parent_id, user_id, issudate, refunddate, active, issold, apply_scheam, group_id, 
                                  role_id, email, address, pincode, state_name, country, username, firstname, 
                                  lastname, organisation, group_name, '' AS list_item, '' AS list_item_name
                                  FROM public.get_divice_details_record_for_list('{$this->schema}', $sectionid)
                                  WHERE user_id = $sectionid AND active = 1
                                  ORDER BY issudate ASC";
                        $lists = $this->db->query($query)->getResult();

                        // Add the result to the main device list
                        foreach ($lists as $list) {
                            $devicelist[] = $list;
                        }
                    }
                }
            } else {
                // If 'pwi_name' is not "All", fetch devices for a specific user
                $query = "SELECT sup_pid, sup_gid, serial_no, imei_no, did, mobile_no, warranty_date, linked, 
                          parent_id, user_id, issudate, refunddate, active, issold, apply_scheam, group_id, 
                          role_id, email, address, pincode, state_name, country, username, firstname, 
                          lastname, organisation, group_name, '' AS list_item, '' AS list_item_name
                          FROM public.get_divice_details_record_for_list('{$this->schema}', $pwi_id)
                          WHERE user_id = $pwi_id AND active = 1
                          ORDER BY issudate ASC";

                $devicelist = $this->db->query($query)->getResult();
            }

            // Construct device string and list device IDs
            if (count($devicelist) > 0) {
                foreach ($devicelist as $devicelist_each) {
                    if ($devices == "{") {
                        $devices .= $devicelist_each->did;
                        $dids .= $devicelist_each->did;
                    } else {
                        $devices .= "," . $devicelist_each->did;
                        $dids .= "," . $devicelist_each->did;
                    }
                }
            }

            // Close the devices string
            $devices .= "}";

            if($report_type == 7){
                // Get device ID from POST request
                $data['device_id'] = $device_id = trim($this->request->getPost('device_id'));

                // If device ID is not empty, assign it to $dids, otherwise set $dids to 0
                if (!empty($device_id)) {
                    $dids = $device_id;
                }

                if (empty($dids)) {
                    $dids = 0;
                }

                // Query to get all the data from the database
                $data['alldata'] = $alldata = $this->db->query("SELECT 
                    mddd, mdddevicename, mddserialno, divicename, result_date, deviceid, acting_trip, start_time, endtime,
                    duration, distance_cover, sos_no, alert_no, call_no, totalnoofstop, totalstoptime, polno, rd, dv, 
                    devicename, acting_triped, starttime, end_time, duration1, distancecover, sosno, alertno, callno, 
                    totalnoof_stop, totalsto_ptime, pol_no, polename1, polnoend, polenameend1, polename, polenameend, 
                    genid 
                FROM 
                    (SELECT DISTINCT deviceid AS mddd, 
                            (SELECT device_name FROM ".$this->schema.".master_device_setup WHERE deviceid=ax.deviceid 
                                AND id = (SELECT MAX(id) FROM ".$this->schema.".master_device_setup WHERE inserttime::date <= '$date_to'::date 
                                AND deviceid=ax.deviceid)) AS mdddevicename,
                            (SELECT COALESCE(serial_no, 'Absent') FROM ".$this->schema.".master_device_details WHERE superdevid=ax.deviceid 
                                OR id=ax.deviceid) AS mddserialno 
                    FROM ".$this->schema.".master_device_assign AS ax WHERE deviceid IN ($dids) AND group_id=2
                    ) masterdevice 
                LEFT OUTER JOIN (
                    SELECT * FROM public.trip_spesified_device WHERE (genid, deviceid, result_date, acting_trip) IN (
                        SELECT MAX(genid), deviceid, result_date, acting_trip FROM public.trip_spesified_device 
                        WHERE deviceid IN ($dids) 
                        AND (result_date || ' ' || start_time >= '$date_from $time_from' 
                        AND result_date || ' ' || end_time <= '$date_to $time_to') 
                        GROUP BY deviceid, result_date, acting_trip
                    ) 
                    ORDER BY devicename, result_date, acting_trip
                ) resultdivice ON masterdevice.mddd = resultdivice.deviceid
                ORDER BY result_date, mddd ASC")->getResult();

                // Iterate through the retrieved data and fetch additional details
                foreach ($alldata as $key => $val) {
                    $serialno = $val->mddserialno;
                    $device_assign_details = $this->db->query("SELECT 
                        a.parent_user_id, a.current_user_id, b.organisation AS pwy, c.organisation AS section
                        FROM public.device_asign_details AS a
                        LEFT JOIN public.user_login AS b ON (a.parent_user_id = b.user_id)
                        LEFT JOIN public.user_login AS c ON (a.current_user_id = c.user_id)
                        WHERE a.serial_no = '".$serialno."'")->getResult();

                    if (!empty($device_assign_details)) {
                        $alldata[$key]->pwy = $device_assign_details[0]->pwy;
                        $alldata[$key]->section = $device_assign_details[0]->section;
                    }
                }

                // Prepare headers for the export data
                $dat[0] = [
                    'A' => 'Date',
                    'B' => 'Device Name',
                    'C' => 'Device ID',
                    'D' => 'SSE/PWAY',
                    'E' => 'Section',
                    'F' => 'BIT',
                    'G' => 'UserType'
                ];

                // Process each record in alldata to prepare it for export
                foreach ($alldata as $key => $val) {
                    if (!empty($val->deviceid)) {
                        // Determine the device type (e.g., Stock, Keyman, Patrolman)
                        $mdddevicename_arr = explode("/", $val->mdddevicename);
                        $type = '';
                        if (strpos(strtolower($mdddevicename_arr[0]), 'stock') !== false) {
                            $type = 'Stock';
                        } elseif (strpos(strtolower($mdddevicename_arr[0]), 'keyman') !== false) {
                            $type = 'Keyman';
                        } elseif (strpos(strtolower($mdddevicename_arr[0]), 'patrolman') !== false) {
                            $type = 'Patrolman';
                        }

                        // Extract BIT and UserType from the device name
                        $devicename = $val->mdddevicename;
                        $sectionArr = explode("(", $devicename);
                        $bit = str_replace(')', '', $sectionArr[1]);
                        $sectionArr1 = explode("/", $devicename);
                        $usertype = $sectionArr1[0];

                        // Populate the export data for each row
                        $dat[$key + 1] = [
                            'A' => !empty($val->acting_trip) ? date("d-m-Y", strtotime($val->result_date)) : 'NA',
                            'B' => $val->mdddevicename,
                            'C' => $val->mddserialno,
                            'D' => $val->pwy,
                            'E' => $val->section,
                            'F' => $bit,
                            'G' => $usertype
                        ];
                    }
                }

                // Generate the file name for the export
                $filename = 'Device_Allotment_Report_' . $data['pwi_name'] . '_' . time() . '.xlsx';
            }
            if($report_type == 8){
                // Initialize data array to hold the result
                $data['alldata'] = [];

                // Execute the database query to get the data
                $query = $this->db->query("
                    SELECT 
                        typeofuser, deviceid, devicename, sessiondate, sesectionstartttime, sesectionendttime, 
                        starttime, endtime, duration, orginallength, 
                        withinpolelength AS totalwitpole, polesequenct,
                        (SELECT device_name 
                        FROM ".$this->schema.".master_device_setup 
                        WHERE deviceid = mdd.superdevid 
                        AND id = (SELECT max(id) 
                                FROM ".$this->schema.".master_device_setup 
                                WHERE inserttime::date <= '$date_from'::date 
                                AND deviceid = mdd.superdevid)
                        ) AS mdddevicename, 
                        mdd.serial_no 
                    FROM public.analysish_patrol_man_and_key_man_work_patrol('$date_from') AS ax 
                    RIGHT JOIN ".$this->schema.".master_device_details AS mdd 
                        ON ax.deviceid = mdd.superdevid 
                    WHERE mdd.superdevid IN ($dids)
                ")->getResult();

                // Initialize variables
                $deviceserial = '';
                $i = 0;

                // Process the results and organize the data
                foreach ($query as $result_each) {
                    if ($deviceserial != $result_each->serial_no) {
                        if ($deviceserial != '') {
                            $i++;
                        }
                        
                        $deviceserial = $result_each->serial_no;
                        
                        // If device ID is available, populate the data; else set to 'NA'
                        if (!empty($result_each->deviceid)) {
                            $data['alldata'][$i] = (object) [
                                'typeofuser' => $result_each->typeofuser,
                                'deviceid' => $result_each->deviceid,
                                'devicealiasname' => $result_each->mdddevicename,
                                'devicename' => $result_each->serial_no,
                                'sessiondate' => $result_each->sessiondate,
                                'starttime' => $result_each->starttime,
                                'endtime' => $result_each->endtime,
                                'duration' => $result_each->duration,
                                'distance' => $result_each->totalwitpole,
                                'orginallength' => $result_each->orginallength
                            ];
                        } else {
                            $data['alldata'][$i] = (object) [
                                'typeofuser' => 'NA',
                                'deviceid' => 'NA',
                                'devicealiasname' => $result_each->mdddevicename,
                                'devicename' => $result_each->serial_no,
                                'sessiondate' => $result_each->sessiondate,
                                'starttime' => '00:00:00',
                                'endtime' => '00:00:00',
                                'duration' => 'NA',
                                'distance' => $result_each->totalwitpole,
                                'orginallength' => $result_each->orginallength
                            ];
                        }
                    } else {
                        // Accumulate the distance if the serial number is the same
                        $data['alldata'][$i]->distance += $result_each->totalwitpole;
                    }
                }

                // Initialize header row for export
                $dat[0] = [
                    'A' => 'User Type',
                    'B' => 'Device Name',
                    'C' => 'Device ID',
                    'D' => 'Start(DD-MM-YYYY HH:MM:SS)',
                    'E' => 'End(DD-MM-YYYY HH:MM:SS)',
                    'F' => 'Within Pole Distance(KM)',
                    'G' => 'Total Distance(KM)',
                    'H' => 'Travelled Time(HH:MM:SS)'
                ];

                // Prepare data for export
                foreach ($data['alldata'] as $key => $val) {
                    // Format start and end times based on user type
                    if ($val->typeofuser == 'PatrolMan') {
                        $start = date("d-m-Y", strtotime($val->sessiondate)) . ' ' . $val->starttime;
                        $end = date("d-m-Y", strtotime($val->sessiondate . ' +1 day')) . ' ' . $val->endtime;
                    } elseif ($val->typeofuser == 'Key Man') {
                        $start = date("d-m-Y", strtotime($val->sessiondate)) . ' ' . $val->starttime;
                        $end = date("d-m-Y", strtotime($val->sessiondate)) . ' ' . $val->endtime;
                    } else {
                        $start = 'NA';
                        $end = 'NA';
                    }

                    // Adjust the original length if it is less than the total distance
                    if ($val->orginallength < $val->distance) {
                        $val->orginallength = $val->distance;
                    }

                    // Populate the data array for each row
                    $dat[$key + 1] = [
                        'A' => $val->typeofuser,
                        'B' => $val->devicealiasname,
                        'C' => $val->devicename,
                        'D' => $start,
                        'E' => $end,
                        'F' => round($val->distance / 1000, 2),
                        'G' => round($val->orginallength / 1000, 2),
                        'H' => $val->duration
                    ];
                }

                // Generate the filename for the export
                $filename = 'Trip_Date_Wise_Report_' . $data['pwi_name'] . '_' . time() . '.xlsx';
            }
            if ($report_type == 9) {
                // Initialize data array
                $data['alldata'] = [];
                
                // Build the query using CodeIgniter 4 query builder
                $query = $this->db->query("
                    SELECT mddd, mdddevicename, mddserialno, divicename, result_date, deviceid, acting_trip, start_time, endtime, duration,
                        distance_cover, sos_no, alert_no, call_no, totalnoofstop, totalstoptime, polno, rd, dv, devicename, acting_triped,
                        starttime, end_time, duration1, distancecover, sosno, alertno, callno, totalnoof_stop, totalsto_ptime, pol_no,
                        polename1, polnoend, polenameend1, polename, polenameend, genid
                    FROM (
                        SELECT DISTINCT deviceid AS mddd,
                            (SELECT device_name 
                            FROM ".$this->schema.".master_device_setup 
                            WHERE deviceid = ax.deviceid 
                            AND id = (SELECT MAX(id) 
                                        FROM ".$this->schema.".master_device_setup 
                                        WHERE inserttime::date <= '$date_to'::date 
                                        AND deviceid = ax.deviceid)
                            ) AS mdddevicename,
                            (SELECT COALESCE(serial_no, 'Absent') 
                            FROM ".$this->schema.".master_device_details 
                            WHERE superdevid = ax.deviceid OR id = ax.deviceid
                            ) AS mddserialno
                        FROM ".$this->schema.".master_device_assign AS ax 
                        WHERE deviceid IN ($dids) 
                        AND group_id = 2
                    ) masterdevice 
                    LEFT OUTER JOIN (
                        SELECT * 
                        FROM public.trip_spesified_device 
                        WHERE (genid, deviceid, result_date, acting_trip) IN (
                            SELECT MAX(genid), deviceid, result_date, acting_trip 
                            FROM public.trip_spesified_device 
                            WHERE deviceid IN ($dids) 
                            AND (result_date || ' ' || start_time >= '$date_from $time_from' 
                                AND result_date || ' ' || end_time <= '$date_to $time_to')
                            GROUP BY deviceid, result_date, acting_trip
                        ) 
                        ORDER BY devicename, result_date, acting_trip
                    ) resultdivice 
                    ON masterdevice.mddd = resultdivice.deviceid
                ") ;
                
                $data['alldata'] = $query->getResult();

                // Initialize header row for export
                $dat[0] = [
                    'A' => "Date",
                    'B' => "Device Name",
                    'C' => "Device ID",
                    'D' => "Trip No.",
                    'E' => "Travelled Distance(KM)"
                ];

                // Prepare data for export
                foreach ($data['alldata'] as $Key => $val) {
                    if ($val->acting_trip != '') {
                        $dat[$Key + 1] = [
                            'A' => date("d-m-Y", strtotime($val->result_date)),
                            'B' => $val->mdddevicename,
                            'C' => $val->mddserialno,
                            'D' => $val->acting_trip,
                            'E' => round($val->distance_cover / 1000, 2)
                        ];
                    } else {
                        $dat[$Key + 1] = [
                            'A' => 'NA',
                            'B' => $val->mdddevicename,
                            'C' => $val->mddserialno,
                            'D' => 'NA',
                            'E' => 'NA'
                        ];
                    }
                }

                // Generate the filename for the report
                $filename = 'Movement_Summery_Report_' . $data['pwi_name'] . '_' . time() . '.xlsx';
            }
            if ($report_type == 10) {
                // Initialize data array
                $data['alldata'] = [];
                
                // Check the group_id from session and run different queries based on user role
                if ($this->sessdata['group_id'] == 2) {
                    // Query for group 2
                    $query = $this->db->query("
                        SELECT * 
                        FROM public.get_divice_details_record_for_list('".$this->schema."', ".$this->sessdata['user_id'].") 
                        WHERE did IN ($dids) 
                        AND (issudate::date BETWEEN '$date_from' AND '$date_to')
                    ");
                } else {
                    // Query for other groups
                    $query = $this->db->query("
                        SELECT * 
                        FROM get_divice_details_record_for_list_for_company('".$this->schema."', ".$this->sessdata['user_id'].") 
                        WHERE did IN ($dids) 
                        AND (issudate::date BETWEEN '$date_from' AND '$date_to') 
                        AND sup_gid = 2
                    ");
                }

                $data['alldata'] = $query->getResult();

                // Initialize header row for export
                $dat[0] = [
                    'A' => "Device ID",
                    'B' => "IMEI No.",
                    'C' => "Allotee Name",
                    'D' => "Allotment Date"
                ];

                // Prepare data for export
                foreach ($data['alldata'] as $Key => $val) {
                    $dat[$Key + 1] = [
                        'A' => $val->serial_no,
                        'B' => $val->imei_no,
                        'C' => $val->organisation,
                        'D' => date("d-m-Y", strtotime($val->issudate))
                    ];
                }

                // Generate the filename for the report
                $filename = 'Device_Allotment_Report_' . $data['pwi_name'] . '_' . time() . '.xlsx';
            }
            if ($report_type == 11) {
                $data['alldata'] = [];
    
                // Use DB query in CodeIgniter 4
                $builder = $this->db->query("SELECT typeofuser, deviceid, devicename, sesectionstartttime, 
                    sesectionendttime, starttime, endtime, duration, startpoletime, startpole, endpointtime, endpoint, 
                    (SELECT device_name 
                     FROM {$this->schema}.master_device_setup 
                     WHERE deviceid = mdd.superdevid 
                       AND id = (SELECT max(id) 
                                 FROM {$this->schema}.master_device_setup 
                                 WHERE inserttime::date <= :date_from::date 
                                   AND deviceid = mdd.superdevid)) AS mdddevicename, 
                     mdd.serial_no 
                FROM public.analysish_work_patrol_time_schudele(:date_from) AS ax 
                RIGHT JOIN {$this->schema}.master_device_details AS mdd 
                ON ax.deviceid = mdd.superdevid 
                WHERE mdd.superdevid IN ($dids)", 
                ['date_from' => $date_from]);
    
                $result = $builder->getResult();
    
                $i = 0;
                foreach ($result as $result_each) {
                    if (strpos($result_each->mdddevicename, 'Patrolman') !== false) {
                        // Date format conversion
                        $data['alldata'][$i]->typeofuser = $result_each->typeofuser;
                        $data['alldata'][$i]->deviceid = $result_each->deviceid;
                        $data['alldata'][$i]->devicealiasname = $result_each->mdddevicename;
                        $data['alldata'][$i]->devicename = $result_each->serial_no;
                        $data['alldata'][$i]->sessiondate = $result_each->sessiondate;
                        $data['alldata'][$i]->schedulestarttime = (new Time($result_each->starttime))->toDateString();
                        $data['alldata'][$i]->scheduleendtime = (new Time($result_each->endtime))->toDateString();
    
                        $data['alldata'][$i]->startpoletime = ($result_each->startpoletime == '2000-10-01 00:00:00') ? 'NA' : (new Time($result_each->startpoletime))->toDateString();
                        $data['alldata'][$i]->endpointtime = ($result_each->endpointtime == '2000-10-01 00:00:00') ? 'NA' : (new Time($result_each->endpointtime))->toDateString();
                        
                        $data['alldata'][$i]->startpole = $result_each->startpole;
                        $data['alldata'][$i]->endpole = $result_each->endpoint;
    
                        $i++;
                    }
                }
    
                // Prepare export header
                $dat[0]['A'] = "User Type";
                $dat[0]['B'] = "Device Name";
                $dat[0]['C'] = "Device ID";
                $dat[0]['D'] = "Start Pole";
                $dat[0]['E'] = "End Pole";
                $dat[0]['F'] = "Scheduled Start(DD-MM-YYYY HH:MM:SS)";
                $dat[0]['G'] = "Scheduled End(DD-MM-YYYY HH:MM:SS)";
                $dat[0]['H'] = "Actual Start(DD-MM-YYYY HH:MM:SS)";
                $dat[0]['I'] = "Actual End(DD-MM-YYYY HH:MM:SS)";
    
                foreach ($data['alldata'] as $Key => $val) {
                    // Assign each field for export
                    $schedulestarttime = $val->schedulestarttime;
                    $scheduleendtime = $val->scheduleendtime;
                    $startpoletime = $val->startpoletime;
                    $endpointtime = $val->endpointtime;
                    $startpole = $val->startpole;
                    $endpole = $val->endpole;
    
                    $dat[$Key + 1]['A'] = $val->typeofuser;
                    $dat[$Key + 1]['B'] = $val->devicealiasname;
                    $dat[$Key + 1]['C'] = $val->devicename;
                    $dat[$Key + 1]['D'] = $startpole;
                    $dat[$Key + 1]['E'] = $endpole;
                    $dat[$Key + 1]['F'] = $schedulestarttime;
                    $dat[$Key + 1]['G'] = $scheduleendtime;
                    $dat[$Key + 1]['H'] = $startpoletime;
                    $dat[$Key + 1]['I'] = $endpointtime;
                }
    
                $filename = 'Trip_Deviation_Date_Wise_Report_' . $data['pwi_name'] . '_' . time() . '.xlsx';
            }

            // Now you can handle the excel download logic or other processing here
            // For example:
            exceldownload($dat, $filename);
        }
    }

    public function devallotreportpdf()
    {
        $sessionData = $this->sessdata;  // Get all session data

        ini_set('max_execution_time', 3000);
        ini_set('memory_limit', '-1');

        if ($this->request->getPost()) {
            $user_id = $sessionData['user_id'];
            $group_id = $sessionData['group_id'];
            $devices = $alerts = $geofences = $routes = $dids = null;
            $data['report_type'] = $report_type = trim($this->request->getPost('report_type'));

            $date_from = date("Y-m-d", strtotime(trim($this->request->getPost('date_from'))));
            $date_to = date("Y-m-d", strtotime(trim($this->request->getPost('date_to'))));
            $time_from = "00:00:00"; // default start time
            $time_to = "23:59:00"; // default end time

            $data['date_from'] = date("d-m-Y H:i", strtotime(trim($this->request->getPost('date_from'))));
            $data['date_to'] = date("d-m-Y H:i", strtotime(trim($this->request->getPost('date_to'))));

            $data['pwi_id'] = $pwi_id = trim($this->request->getPost('user'));
            $data['pwi_name'] = trim($this->request->getPost('pwi_name'));
            $data['schema'] = $this->schema;
 
            $devices .= "{";
            if ($data['pwi_name'] == 'All') {
                // Query to get devices
                $query = "SELECT sup_pid, sup_gid, serial_no, imei_no, did, mobile_no, warranty_date, linked, parent_id, user_id, issudate,
                          refunddate, active, issold, apply_scheam, group_id, role_id, email, address, pincode, state_name, country, username, firstname,
                          lastname, organisation, group_name, '' AS list_item, '' AS list_item_name
                          FROM public.get_divice_details_record_for_list('".$this->schema."',".$user_id.") 
                          WHERE sup_gid = $group_id AND user_id = $user_id
                          ORDER BY did ASC";
            } else {
                // Query for specific user devices
                $query = "SELECT sup_pid, sup_gid, serial_no, imei_no, did, mobile_no, warranty_date, linked, parent_id, user_id, issudate,
                          refunddate, active, issold, apply_scheam, group_id, role_id, email, address, pincode, state_name, country, username, firstname,
                          lastname, organisation, group_name, list_item, list_item_name
                          FROM ((SELECT sup_pid, sup_gid, serial_no, imei_no, did, mobile_no, warranty_date, linked, parent_id, user_id, issudate,
                                 refunddate, active, issold, apply_scheam, group_id, role_id, email, address, pincode, state_name, country, username, firstname,
                                 lastname, organisation, group_name, '' AS list_item1, '' AS list_item_name2 
                                 FROM public.get_divice_details_record_for_list('".$this->schema."',".$pwi_id.")) ss
                                 INNER JOIN
                                 (SELECT serial_no AS top_serial_no, imei_no AS top_imei_no, MAX(issudate) AS max_issdate, 
                                  STRING_AGG(group_name, ' - ') AS list_item, STRING_AGG(organisation, ' - ') AS list_item_name
                                  FROM public.get_divice_details_record_for_list('".$this->schema."',".$pwi_id.")
                                  GROUP BY serial_no, imei_no) ssi
                                 ON ss.issudate = ssi.max_issdate AND ss.serial_no = ssi.top_serial_no) xvsd
                          ORDER BY issudate ASC";
            }

            $devicelist = $this->db->query($query)->getResult();

            if (count($devicelist) > 0) {
                foreach ($devicelist as $devicelist_each) {
                    if ($devices == "{") {
                        $devices .= $devicelist_each->did;
                        $dids .= $devicelist_each->did;
                    } else {
                        $devices .= "," . $devicelist_each->did;
                        $dids .= "," . $devicelist_each->did;
                    }
                }
            }

            $devices .= "}";

            // For different report types (7, 8, 9, 10, 11)
            if ($report_type == 7) {
                $device_id = trim($this->request->getPost('device_id'));
                if (!empty($device_id)) {
                    $dids = $device_id;
                }
                $data['alldata'] = $this->db->query("
                    SELECT mddd, mdddevicename, mddserialno, divicename, result_date, deviceid, acting_trip, start_time, endtime, 
                           duration, distance_cover, sos_no, alert_no, call_no, totalnoofstop, totalstoptime, polno, rd, dv, devicename, 
                           acting_triped, starttime, end_time, duration1, distancecover, sosno, alertno, callno, totalnoof_stop, totalsto_ptime, 
                           pol_no, polename1, polnoend, polenameend1, polename, polenameend, genid 
                    FROM (
                        SELECT DISTINCT deviceid AS mddd, 
                               (SELECT device_name FROM ".$this->schema.".master_device_setup WHERE deviceid = ax.deviceid AND id = (
                                   SELECT MAX(id) FROM ".$this->schema.".master_device_setup WHERE inserttime::date <= '$date_to'::date AND deviceid = ax.deviceid
                               )) AS mdddevicename, 
                               (SELECT COALESCE(serial_no, 'Absent') FROM ".$this->schema.".master_device_details WHERE superdevid = ax.deviceid OR id = ax.deviceid) AS mddserialno
                        FROM ".$this->schema.".master_device_assign AS ax 
                        WHERE deviceid IN ($dids) AND group_id = 2
                    ) masterdevice 
                    LEFT OUTER JOIN (
                        SELECT * FROM public.trip_spesified_device 
                        WHERE (genid, deviceid, result_date, acting_trip) IN (
                            SELECT MAX(genid), deviceid, result_date, acting_trip 
                            FROM public.trip_spesified_device 
                            WHERE deviceid IN ($dids) AND (result_date || ' ' || start_time >= '".$date_from." ".$time_from."' AND result_date || ' ' || end_time <= '".$date_to." ".$time_to."') 
                            GROUP BY deviceid, result_date, acting_trip
                        ) 
                        ORDER BY devicename, result_date, acting_trip
                    ) resultdivice ON masterdevice.mddd = resultdivice.deviceid
                ")->getResult();
    
                // Load the PDF view
                $html = view('traxreport/pdf_deviceallotment', $data);
                $filename = 'Device_Allotment_Report_' . $data['pwi_name'] . '_' . time();
            } 
            if ($report_type == 8) {
                // Query to fetch the analysis data
                $result = $this->db->query("
                    SELECT typeofuser, deviceid, devicename, sessiondate, sesectionstartttime, sesectionendttime, starttime, 
                           endtime, duration, (orginallength), (withinpolelength) AS totalwitpole, polesequenct, 
                           (SELECT device_name FROM ".$this->schema.".master_device_setup WHERE deviceid = mdd.superdevid AND 
                            id = (SELECT MAX(id) FROM ".$this->schema.".master_device_setup WHERE inserttime::date <= '$date_from'::date 
                            AND deviceid = mdd.superdevid)) AS mdddevicename, mdd.serial_no 
                    FROM public.analysish_patrol_man_and_key_man_work_patrol('$date_from') AS ax 
                    RIGHT JOIN ".$this->schema.".master_device_details AS mdd 
                    ON (ax.deviceid = mdd.superdevid) 
                    WHERE mdd.superdevid IN ($dids)
                ")->getResult();
    
                // Prepare data for the report
                $deviceserial = '';
                $i = 0;
                $data['alldata'] = [];
                foreach ($result as $result_each) {
                    if ($deviceserial != $result_each->serial_no) {
                        if ($deviceserial == '') {
                            $i = 0;
                        } else {
                            $i++;
                        }
                        $deviceserial = $result_each->serial_no;
    
                        if ($result_each->deviceid != '') {
                            $data['alldata'][$i] = (object)[
                                'typeofuser' => $result_each->typeofuser,
                                'deviceid' => $result_each->deviceid,
                                'devicealiasname' => $result_each->mdddevicename,
                                'devicename' => $result_each->serial_no,
                                'sessiondate' => $result_each->sessiondate,
                                'starttime' => $result_each->starttime,
                                'endtime' => $result_each->endtime,
                                'duration' => $result_each->duration,
                                'distance' => $result_each->totalwitpole,
                                'orginallength' => $result_each->orginallength,
                            ];
                        } else {
                            $data['alldata'][$i] = (object)[
                                'typeofuser' => 'NA',
                                'deviceid' => 'NA',
                                'devicealiasname' => $result_each->mdddevicename,
                                'devicename' => $result_each->serial_no,
                                'sessiondate' => $result_each->sessiondate,
                                'starttime' => '00:00:00',
                                'endtime' => '00:00:00',
                                'duration' => 'NA',
                                'distance' => $result_each->totalwitpole,
                                'orginallength' => $result_each->orginallength,
                            ];
                        }
                    } else {
                        $data['alldata'][$i]->distance += $result_each->totalwitpole;
                    }
                }
    
                // Render the report view
                $html = view('traxreport/pdf_trip', $data);
                $filename = 'Trip_Date_Wise_Report_' . $data['pwi_name'] . '_' . time();
            } 
            if ($report_type == 9) {
                $result = $this->db->query("
                    SELECT mddd, mdddevicename, mddserialno, divicename, result_date, deviceid, acting_trip, start_time, 
                           endtime, duration, distance_cover, sos_no, alert_no, call_no, totalnoofstop, totalstoptime, 
                           polno, rd, dv, devicename, acting_triped, starttime, end_time, duration1, distancecover, sosno, 
                           alertno, callno, totalnoof_stop, totalsto_ptime, pol_no, polename1, polnoend, polenameend1, 
                           polename, polenameend, genid 
                    FROM (
                        SELECT DISTINCT deviceid AS mddd,
                            (SELECT device_name FROM ".$this->schema.".master_device_setup WHERE deviceid=ax.deviceid 
                             AND id=(SELECT MAX(id) FROM ".$this->schema.".master_device_setup 
                                     WHERE inserttime::date <= '$date_to'::date 
                                     AND deviceid=ax.deviceid )) AS mdddevicename,
                            (SELECT COALESCE(serial_no,'Absent') FROM ".$this->schema.".master_device_details  
                             WHERE superdevid=ax.deviceid OR id=ax.deviceid) AS mddserialno   
                        FROM ".$this->schema.".master_device_assign AS ax 
                        WHERE deviceid IN ($dids) AND group_id=2
                    ) masterdevice 
                    LEFT OUTER JOIN (
                        SELECT * FROM public.trip_spesified_device 
                        WHERE (genid, deviceid, result_date, acting_trip) IN (
                            SELECT MAX(genid), deviceid, result_date, acting_trip 
                            FROM public.trip_spesified_device 
                            WHERE deviceid IN ($dids) 
                            AND (result_date || ' ' || start_time >= '".$date_from." ".$time_from."' 
                                 AND result_date || ' ' || end_time <= '".$date_to." ".$time_to."') 
                            GROUP BY deviceid, result_date, acting_trip
                        )
                        ORDER BY devicename, result_date, acting_trip
                    ) resultdivice 
                    ON masterdevice.mddd = resultdivice.deviceid
                ")->getResult();
    
                // Prepare the data for the view
                $data['alldata'] = $result;
                $html = view('traxreport/pdf_movementsummery', $data);
                $filename = 'Movement_Summery_Report_'.$data['pwi_name'].'_'.time();
            }
            if ($report_type == 10) {
                if ($this->sessdata('group_id') == 2) {
                    $result = $this->db->query("
                        SELECT * 
                        FROM public.get_divice_details_record_for_list('".$this->session->get('schemaname')."',".$this->session->get('user_id').") 
                        WHERE did IN ($dids) 
                        AND (issudate::date BETWEEN '".$date_from."' AND '".$date_to."')
                    ")->getResult();
                } else {
                    $result = $this->db->query("
                        SELECT * 
                        FROM get_divice_details_record_for_list_for_company('".$this->session->get('schemaname')."',".$this->session->get('user_id').") 
                        WHERE did IN ($dids) 
                        AND (issudate::date BETWEEN '".$date_from."' AND '".$date_to."') 
                        AND sup_gid = 2
                    ")->getResult();
                }
    
                // Prepare the data for the view
                $data['alldata'] = $result;
                $html = view('traxreport/pdf_allotment', $data);
                $filename = 'Device_Allotment_Report_'.$data['pwi_name'].'_'.time();
            } 
            if ($report_type == 11) {
                $result = $this->db->query("
                    SELECT typeofuser, deviceid, devicename, sesectionstartttime, sesectionendttime, 
                           starttime, endtime, duration, startpoletime, startpole, endpointtime, endpoint, 
                           (SELECT device_name FROM ".$this->schema.".master_device_setup 
                            WHERE deviceid=mdd.superdevid 
                            AND id=(SELECT MAX(id) FROM ".$this->schema.".master_device_setup 
                                    WHERE inserttime::date <= '$date_from'::date 
                                    AND deviceid=mdd.superdevid)) AS mdddevicename, 
                           mdd.serial_no 
                    FROM public.analysish_work_patrol_time_schudele('$date_from') AS ax
                    RIGHT JOIN ".$this->schema.".master_device_details AS mdd 
                    ON (ax.deviceid = mdd.superdevid) 
                    WHERE mdd.superdevid IN ($dids)
                ")->getResult();
    
                // Process the query results
                $deviceserial = '';
                $i = 0;
                $data['alldata'] = [];
                foreach ($result as $result_each) {
                    if (strpos($result_each->mdddevicename, 'Patrolman') !== false) {
                        $data['alldata'][$i] = new \stdClass();  // Initialize the object
                        $data['alldata'][$i]->typeofuser = $result_each->typeofuser;
                        $data['alldata'][$i]->deviceid = $result_each->deviceid;
                        $data['alldata'][$i]->devicealiasname = $result_each->mdddevicename;
                        $data['alldata'][$i]->devicename = $result_each->serial_no;
                        $data['alldata'][$i]->sessiondate = $result_each->sessiondate;
    
                        // Format the start and end times
                        $data['alldata'][$i]->schedulestarttime = date('d-m-Y H:i:s', strtotime($result_each->starttime));
                        $data['alldata'][$i]->scheduleendtime = date('d-m-Y H:i:s', strtotime($result_each->endtime));
    
                        // Format the pole times
                        if ($result_each->startpoletime == '2000-10-01 00:00:00') {
                            $data['alldata'][$i]->startpoletime = 'NA';
                        } else {
                            $data['alldata'][$i]->startpoletime = date('d-m-Y H:i:s', strtotime($result_each->startpoletime));
                        }
    
                        if ($result_each->endpointtime == '2000-10-01 00:00:00') {
                            $data['alldata'][$i]->endpointtime = 'NA';
                        } else {
                            $data['alldata'][$i]->endpointtime = date('d-m-Y H:i:s', strtotime($result_each->endpointtime));
                        }
    
                        $data['alldata'][$i]->startpole = $result_each->startpole;
                        $data['alldata'][$i]->endpole = $result_each->endpoint;
    
                        $i++;
                    }
                }
    
                // Render the view with data
                $html = view('traxreport/pdf_trip_deviation', $data);
                $filename = 'Trip_Deviation_Date_Wise_Report_' . $data['pwi_name'] . '_' . time();
            }

            // Instantiate the MakePDF class
            $pdf = new MakePDF();

            // Set the filename and content
            $pdf->setFileName($filename);
            $pdf->setContent($html);

            // Generate and stream the PDF to the browser
            $pdf->getPdf();  // true to stream the PDF
        }
    }

    public function dutyCompletionReport()
    {
        if (!session()->get('login_sess_data')) {
            return redirect()->to('/');
        }

        $data = [];
        $data['sessdata'] = $this->sessdata;
        $data['page_title'] = "Duty Completion Report";
        $data['usersdd'] = $this->commonModel->get_users();
        $data['pway'] = $this->db->query("SELECT organisation, user_id FROM public.user_login WHERE active = 1 AND group_id = 8")->getResult();
        $data['sse_pwy'] = trim($this->request->getPost('pway_id'));
        $data['date_from'] = '';
        $data['date_to'] = '';

        if ($this->sessdata['group_id'] == 3) { // distributor
            $data['devicedropdown'] = $this->db->query("SELECT a.*, (SELECT device_name FROM {$this->schema}.master_device_setup WHERE id=(SELECT MAX(id) FROM {$this->schema}.master_device_setup WHERE inserttime::date <= CURRENT_DATE::date AND deviceid=a.did)) || '-' || a.serial_no AS device_name FROM public.get_divice_details_record_for_list_for_company('{$this->schema}', {$this->sessdata['user_id']}) AS a WHERE a.group_id = 2 AND a.active = 1")->getResult();
        } else { // others
            $data['devicedropdown'] = $this->db->query("SELECT a.*, (SELECT device_name FROM {$this->schema}.master_device_setup WHERE id=(SELECT MAX(id) FROM {$this->schema}.master_device_setup WHERE inserttime::date <= CURRENT_DATE::date AND deviceid=a.did)) || '-' || a.serial_no AS device_name FROM public.get_divice_details_record_for_list_for_company('{$this->schema}', {$this->sessdata['user_id']}) AS a WHERE a.group_id = 2 AND a.active = 1")->getResult();
        }

        if ($this->request->getPost()) {
            $user_id = $this->sessdata['user_id'];
            $group_id = $this->sessdata['group_id'];
            $devices = $alerts = $geofences = $routes = $dids = null;

            $date_from = date("Y-m-d", strtotime(trim($this->request->getPost('date_from'))));
            $date_to = date("Y-m-d", strtotime(trim($this->request->getPost('date_to'))));
            $time_from = "00:00:00"; // Default time
            $time_to = "23:59:00"; // Default time
            $data['date_from'] = date("d-m-Y H:i", strtotime(trim($this->request->getPost('date_from'))));
            $data['date_to'] = date("d-m-Y H:i", strtotime(trim($this->request->getPost('date_to'))));
            $data['pwi_id'] = trim($this->request->getPost('user'));
            $data['pwi_name'] = trim($this->request->getPost('pwi_name'));
            $data['duty_status'] = $duty_status = trim($this->request->getPost('duty_status'));
            $data['schema'] = $this->schema;
            $data['sse_pwy'] = trim($this->request->getPost('pway_id'));
            $devices = "{";

            if ($data['pwi_name'] == 'All') {
                if ($data['sse_pwy'] == 'All') {
                    $query = "SELECT sup_pid, sup_gid, serial_no, imei_no, did, mobile_no, warranty_date, linked, parent_id, user_id, issudate,
                              refunddate, active, issold, apply_scheam, group_id, role_id, email, address, pincode, state_name, country, username, firstname,
                              lastname, organisation, group_name, '' AS list_item, '' AS list_item_name FROM public.get_divice_details_record_for_list('".$this->schema."', ".$user_id.") WHERE sup_gid = $group_id AND user_id = ".$user_id." AND active = 1 ORDER BY did ASC";
                    $devicelist = $this->db->query($query)->getResult();
                } else {
                    $sectionlist = $this->db->query("SELECT user_id FROM public.user_login WHERE parent_id = '".$data['sse_pwy']."' AND active = 1")->getResult();
                    $devicelist = [];

                    foreach ($sectionlist as $section) {
                        $sectionid = $section->user_id;
                        $query = "SELECT sup_pid, sup_gid, serial_no, imei_no, did, mobile_no, warranty_date, linked, parent_id, user_id, issudate,
                                  refunddate, active, issold, apply_scheam, group_id, role_id, email, address, pincode, state_name, country, username, firstname,
                                  lastname, organisation, group_name, '' AS list_item, '' AS list_item_name FROM public.get_divice_details_record_for_list('".$this->schema."', ".$sectionid.") WHERE user_id = ".$sectionid." AND active = 1 ORDER BY issudate ASC";
                        $lists = $this->db->query($query)->getResult();
                        $devicelist = array_merge($devicelist, $lists);
                    }
                }
            } else {
                $query = "SELECT sup_pid, sup_gid, serial_no, imei_no, did, mobile_no, warranty_date, linked, parent_id, user_id, issudate,
                          refunddate, active, issold, apply_scheam, group_id, role_id, email, address, pincode, state_name, country, username, firstname,
                          lastname, organisation, group_name, '' AS list_item, '' AS list_item_name FROM public.get_divice_details_record_for_list('".$this->schema."', ".$data['pwi_id'].") WHERE user_id = {$data['pwi_id']} AND active = 1 ORDER BY issudate ASC";
                $devicelist = $this->db->query($query)->getResult();
            }

            if (count($devicelist) > 0) {
                foreach ($devicelist as $devicelist_each) {
                    $devices .= ($devices == "{") ? $devicelist_each->did : "," . $devicelist_each->did;
                    $dids .= ($dids == "") ? $devicelist_each->did : "," . $devicelist_each->did;
                }
            }
            $devices .= "}";

            $data['device_id'] = trim($this->request->getPost('device_id'));
            $data['report_type'] = trim($this->request->getPost('report_type'));
            if (!empty($data['device_id'])) {
                $dids = $data['device_id'];
            }
            $dids = $dids ?: 0; // Default to 0 if empty
            $data['typeofuser'] = trim($this->request->getPost('typeofuser'));

            $alldata = $this->db->query("SELECT mddd, mdddevicename, mddserialno, devicename, result_date, deviceid, acting_trip, start_time, endtime, duration, distance_cover, sos_no, alert_no, call_no, totalnoofstop, totalstoptime, polno, rd, dv, devicename, acting_triped, starttime, end_time, duration1, distancecover, sosno, alertno, callno, totalnoof_stop, totalsto_ptime, pol_no, polename1, polnoend, polenameend1, polename, polenameend, genid 
              FROM (
                  SELECT DISTINCT deviceid AS mddd, 
                      (SELECT device_name FROM ".$this->schema.".master_device_setup WHERE deviceid=ax.deviceid AND id=(SELECT MAX(id) FROM ".$this->schema.".master_device_setup WHERE inserttime::date <= '$date_to'::date AND deviceid=ax.deviceid)) AS mdddevicename,
                      (SELECT COALESCE(serial_no, 'Absent') FROM ".$this->schema.".master_device_details WHERE superdevid=ax.deviceid OR id=ax.deviceid) AS mddserialno 
                  FROM ".$this->schema.".master_device_assign AS ax 
                  WHERE deviceid IN ($dids) AND group_id=2
              ) masterdevice 
              LEFT OUTER JOIN (
                  SELECT * 
                  FROM public.trip_spesified_device 
                  WHERE (genid, deviceid, result_date, acting_trip) IN (
                      SELECT MAX(genid), deviceid, result_date, acting_trip 
                      FROM public.trip_spesified_device 
                      WHERE totalstoptime > '00:00:00' AND deviceid IN ($dids) 
                      AND (result_date BETWEEN '$date_from' AND '$date_to') 
                      GROUP BY deviceid, result_date, acting_trip
                  )
              ) trip_details 
              ON masterdevice.mddd = trip_details.deviceid
              ORDER BY deviceid, result_date ASC")->getResult();
            
            $data['alldata'] = $alldata;
            $data['date_from'] = $date_from;
            $data['date_to'] = $date_to;
            $data['devices'] = $devices;
            $data['alert'] = $alerts;
            $data['geofence'] = $geofences;
            $data['route'] = $routes;

        }

        $data['middle'] = view('traxreport/duitycompletionreport_view', $data);
        return view('mainlayout', $data);
    
    }

    public function duitycompletionreportexcel()
    {
        $data = [];
        $data['sessdata'] = $this->sessdata; // Assuming session stores user data
        $data['page_title'] = "Duty Completion Report";
        $data['usersdd'] = $this->commonModel->get_users();
        $data['date_from'] = '';
        $data['date_to'] = '';
        $data['report_type'] = 7;
        $data['pway'] = $this->db->query("SELECT organisation, user_id FROM public.user_login WHERE active = 1 AND group_id = 8")->getResult();
        $data['sse_pwy'] = $this->request->getPost('pway_id', FILTER_SANITIZE_STRING);
        
        if ($this->sessdata['group_id'] == 3) { // distributor
            $data['devicedropdown'] = $this->db->query("SELECT a.*, 
                (SELECT device_name 
                 FROM {$this->schema}.master_device_setup 
                 WHERE id = (SELECT max(id) FROM {$this->schema}.master_device_setup WHERE inserttime::date <= current_date::date AND deviceid = a.did)) || '-' || a.serial_no AS device_name 
                 FROM public.get_divice_details_record_for_list_for_company('{$this->schema}', {$this->sessdata['user_id']}) AS a 
                 WHERE a.group_id = 2 AND a.active = 1")->getResult();
        } else {
            $data['devicedropdown'] = $this->db->query("SELECT a.*, 
                (SELECT device_name 
                 FROM {$this->schema}.master_device_setup 
                 WHERE id = (SELECT max(id) FROM {$this->schema}.master_device_setup WHERE inserttime::date <= current_date::date AND deviceid = a.did)) || '-' || a.serial_no AS device_name 
                 FROM public.get_divice_details_record_for_list_for_company('{$this->schema}', {$this->sessdata['user_id']}) AS a 
                 WHERE a.group_id = 2 AND a.active = 1")->getResult();
        }

        if ($this->request->getPost()) {
            $user_id = $this->sessdata['user_id'];
            $group_id = $this->sessdata['group_id'];
            $date_from = date("Y-m-d", strtotime(trim($this->request->getPost('date_from'))));
            $date_to = date("Y-m-d", strtotime(trim($this->request->getPost('date_to'))));
            $time_from = "00:00:00";
            $time_to = "23:59:00";
            $data['date_from'] = date("d-m-Y H:i", strtotime(trim($this->request->getPost('date_from'))));
            $data['date_to'] = date("d-m-Y H:i", strtotime(trim($this->request->getPost('date_to'))));
            $data['pwi_id'] = $pwi_id = trim($this->request->getPost('user'));
            $data['pwi_name'] = trim($this->request->getPost('pwi_name'));
            $data['duty_status'] = $duty_status = trim($this->request->getPost('duty_status'));
            $data['schema'] = $this->schema;
            $data['sse_pwy'] = $sse_pwy = trim($this->request->getPost('pway_id'));
            $devices = $alerts = $geofences = $routes = $dids = null;

            // Fetch device details based on post data
            if ($data['pwi_name'] == 'All') {
                if ($sse_pwy == 'All') {
                    $devicelist = $this->db->query("SELECT * FROM public.get_divice_details_record_for_list('{$this->schema}', {$user_id}) WHERE sup_gid = $group_id AND user_id = $user_id AND active = 1 ORDER BY did ASC")->getResult();
                } else {
                    $sectionlist = $this->db->query("SELECT user_id FROM public.user_login WHERE parent_id = '{$sse_pwy}' AND active = 1")->getResult();
                    $devicelist = [];
                    foreach ($sectionlist as $section) {
                        $sectionid = $section->user_id;
                        $devicelist = array_merge($devicelist, $this->db->query("SELECT * FROM public.get_divice_details_record_for_list('{$this->schema}', {$sectionid}) WHERE user_id = {$sectionid} AND active = 1 ORDER BY issudate ASC")->getResult());
                    }
                }
            } else {
                $devicelist = $this->db->query("SELECT * FROM public.get_divice_details_record_for_list('{$this->schema}', {$pwi_id}) WHERE user_id = {$pwi_id} AND active = 1 ORDER BY issudate ASC")->getResult();
            }

            // Build device list
            if (count($devicelist) > 0) {
                foreach ($devicelist as $devicelist_each) {
                    $devices .= $devices == "{" ? $devicelist_each->did : "," . $devicelist_each->did;
                    $dids .= $dids == "" ? $devicelist_each->did : "," . $devicelist_each->did;
                }
                $devices .= "}";
            }

            // Get the device ID and other filter parameters
            $device_id = trim($this->request->getPost('device_id'));
            $report_type = trim($this->request->getPost('report_type'));

            if ($device_id != '') {
                $dids = $device_id;
            }
            if ($dids == '') {
                $dids = 0;
            }

            $typeofuser = trim($this->request->getPost('typeofuser'));

            // Query the required data for the report
            $alldata = $this->db->query("
                SELECT * FROM ((SELECT DISTINCT deviceid AS mddd, 
                    (SELECT device_name FROM {$this->schema}.master_device_setup 
                     WHERE deviceid = ax.deviceid AND id = (SELECT max(id) 
                         FROM {$this->schema}.master_device_setup 
                         WHERE inserttime::date <= '$date_to'::date AND deviceid = ax.deviceid)) 
                         AS mdddevicename,
                    (SELECT COALESCE(serial_no, 'Absent') 
                     FROM {$this->schema}.master_device_details 
                     WHERE superdevid = ax.deviceid OR id = ax.deviceid) AS mddserialno   
                FROM {$this->schema}.master_device_assign AS ax 
                WHERE deviceid IN ($dids) AND group_id = 2)
                masterdevice 
                LEFT OUTER JOIN (SELECT * FROM public.trip_spesified_device 
                WHERE (genid, deviceid, result_date, acting_trip) IN 
                (SELECT max(genid), deviceid, result_date, acting_trip 
                 FROM public.trip_spesified_device 
                 WHERE totalstoptime > '00:00:00' 
                 AND deviceid IN ($dids) 
                 AND (result_date || ' ' || start_time >= '{$date_from} {$time_from}' 
                 AND result_date || ' ' || end_time <= '{$date_to} {$time_to}')
                 GROUP BY deviceid, result_date, acting_trip) 
                ORDER BY devicename, result_date, acting_trip) resultdivice 
                ON masterdevice.mddd = resultdivice.deviceid) resultset 
                ORDER BY result_date, mddd ASC
            ")->getResult();

            // Process and filter data for reporting
            $newalldata = [];
            $alldatanew = [];
            for ($i = 0; $i < count($alldata); $i++) {
                $deviceidnew = $alldata[$i]->deviceid;
                if ($deviceidnew != '') {
                    // Fetch device assignment details
                    $serialno = $alldata[$i]->mddserialno;
                    $device_assign_details = $this->deviceModel->getDeviceAssignDetails($serialno);
                    $alldata[$i]->pwy = $device_assign_details->pwy ?? null;
                    $alldata[$i]->section = $device_assign_details->section ?? null;
    
                    // Parse devicename and get pole details
                    $devicename = $alldata[$i]->mdddevicename;
                    $devicenameArr = explode(':', $devicename);
                    $poledetailsnew = trim($devicenameArr[1]);
                    $poledetailsnewArr = explode('(', $poledetailsnew);
                    $polenamenew = trim($poledetailsnewArr[1]);
                    $polenamenew = str_replace(')', '', $polenamenew);
                    $polenamenewArr = explode('-', $polenamenew);
                    $alldata[$i]->startpole = $polenamenewArr[0];
                    $alldata[$i]->stoppol = $polenamenewArr[1];
                    $alldata[$i]->bit = $alldata[$i]->startpole . '-' . $alldata[$i]->stoppol;
    
                    // Fetch time-related data
                    $timedetails = $this->deviceModel->getTimedetails($deviceidnew);
                    $alldata[$i]->walk_org_distance_out = $timedetails->walk_org_distance ?? 0.0;
    
                    // Calculate from and to datetime
                    $datetimefrom = $alldata[$i]->result_date . " " . $alldata[$i]->start_time;
                    $datetimeto = $alldata[$i]->result_date . " " . $alldata[$i]->endtime;
                    $from_datetime = date("Y-m-d H:i:s", strtotime($datetimefrom));
                    $to_datetime = date("Y-m-d H:i:s", strtotime($datetimeto));
    
                    // Get history summary
                    $get_history_summary = $this->deviceModel->getHistorySummary($deviceidnew, $from_datetime, $to_datetime);
                    $alldata[$i]->totaldistancecovere_taa = $get_history_summary->distance_cover ?? 0.0;
    
                    // Calculate deviation distance
                    $alldata[$i]->deviation_distance1 = ($timedetails->walk_org_distance * 1000) - $alldata[$i]->totaldistancecovere_taa;
    
                    // Set status based on deviation distance
                    if ($alldata[$i]->deviation_distance1 !== '') {
                        if ($alldata[$i]->totaldistancecovere_taa > ($timedetails->walk_org_distance * 1000) && $timedetails->walk_org_distance !== '') {
                            $alldata[$i]->status = 'Duty Completed';
                        } elseif ($this->hasMinusSign($alldata[$i]->deviation_distance1)) {
                            $alldata[$i]->status = 'Duty Not Completed';
                        } elseif ($alldata[$i]->deviation_distance1 > 0) {
                            $alldata[$i]->status = 'Duty Not Completed';
                        } else {
                            $alldata[$i]->status = 'Duty Completed';
                        }
                    } else {
                        $alldata[$i]->walk_org_distance_out = 0.0;
                        $alldata[$i]->deviation_distance1 = 0.0;
                        $alldata[$i]->status = 'NA';
                    }
                } else {
                    $alldata[$i]->walk_org_distance_out = 0.0;
                    $alldata[$i]->deviation_distance1 = 0.0;
                    $alldata[$i]->status = 'NA';
                }
    
                // Parsing devicename again (if needed)
                $mdddevicename = $alldata[$i]->devicename;
                $mdddevicename_arr = explode("/", $mdddevicename);
            }
            if ($typeofuser != 'All') {
                // Filter based on the user type
                foreach ($alldata as $dv) {
                    if (!empty($dv->mdddevicename)) {
                        // Fetch the device name from the master device setup
                        $mdddevicename = $this->deviceModel->getDeviceNameById($dv->mddd);
    
                        if ($mdddevicename) {
                            $mdddevicename_arr = explode("/", $mdddevicename);
                            $u_type = $mdddevicename_arr[0];
    
                            // Compare the extracted user type with the given type
                            if (strtoupper($u_type) == strtoupper($typeofuser)) {
                                $newalldata[] = $dv;
                            }
                        }
                    }
                }
            } else {
                // If the user type is 'All', include all devices
                foreach ($alldata as $dv) {
                    if (!empty($dv->mdddevicename)) {
                        $newalldata[] = $dv;
                    }
                }
            }
            // If no duty status filter is provided, use all filtered data
            if (empty($duty_status)) {
                $alldatanew = $newalldata;
            } else {
                // If duty status is '1' (Duty Completed)
                if ($duty_status == '1') {
                    // Filter out devices where the status is 'Duty Completed'
                    foreach ($newalldata as $device) {
                        if ($device->status == 'Duty Completed') {
                            $alldatanew[] = $device;  // Add to the result array
                        }
                    }
                }
                // If duty status is '2' (Duty Not Completed)
                else if ($duty_status == '2') {
                    // Filter out devices where the status is not 'Duty Completed'
                    foreach ($newalldata as $device) {
                        if ($device->status != 'Duty Completed') {
                            $alldatanew[] = $device;  // Add to the result array
                        }
                    }
                }
            }

            // Initialize the headers for the report
            $dat[0] = [
                'A' => "Date",
                'B' => "DeviceName",
                'C' => "Device SerialNo",
                'D' => "SSE/PWAY",
                'E' => "Section",
                'F' => "BIT",
                'G' => "UserType",
                'H' => "TripNo.",
                'I' => "StartPole",
                'J' => "EndPole",
                'K' => "TravelledDistance",
                'L' => "Actual Distance",
                'M' => "Deviation Distance",
                'N' => "Status"
            ];

            $Key = 0;


            // Loop through the data
            foreach ($alldatanew as $irow) {
                $query = $this->db->query("SELECT device_name FROM {$this->schema}.master_device_setup WHERE deviceid = ?", [$irow->mddd]);

                // Fetch the result
                $mdddevicename = $query->getRowArray(); // Using getRowArray() to fetch the result as an associative array

                if ($mdddevicename) {
                    $irow->mdddevicename = $mdddevicename['device_name']; // Access the device_name
                    
                    // Explode the device name by "/"
                    $mdddevicename_arr = explode("/", $irow->mdddevicename);

                    // Determine the type based on the device name
                    $device_type = strtolower($mdddevicename_arr[0]);  // Convert to lowercase once to optimize

                    if (strpos($device_type, 'stock') !== false) {
                        $type = 'Stock';
                    }
                    else if (strpos($device_type, 'keyman') !== false) {
                        $type = 'Keyman';
                    }
                    else if (strpos($device_type, 'patrolman') !== false) {
                        $type = 'Patrolman';
                    }
                    else {
                        $type = 'NA';
                    }
                } else {
                    // Handle the case where device is not found, if necessary
                    $irow->mdddevicename = '';
                    $type = 'NA';  // Default type if not found
                }

                // Step 1: Split the device name to extract details inside parentheses
                $mdddevicename_arr_new = explode("(", $irow->mdddevicename);

                if (isset($mdddevicename_arr_new[1])) {
                    // Step 2: Extract the part after the "(" and before ")"
                    $poledetails = $mdddevicename_arr_new[1];
                    $poledetails = str_replace(")", "", $poledetails); // Remove closing parenthesis

                    // Step 3: Split the pole details by "-"
                    $poledetails_arr = explode("-", $poledetails);

                    // Step 4: Assuming you want to process pole details, you can use $poledetails_arr now
                    // For example, access the first part of the pole details (if applicable)
                    $first_pole_detail = $poledetails_arr[0];
                } else {
                    // Handle the case where the parentheses structure is not found in the device name
                    $poledetails_arr = [];
                    $first_pole_detail = null;
                }

                // Step 5: Construct the from and to datetime values
                $datetimefrom = $irow->result_date . " " . $irow->start_time;
                $from_datetime = date("H:i:s", strtotime($datetimefrom));

                $datetimeto = $irow->result_date . " " . $irow->endtime;
                $to_datetime = date("H:i:s", strtotime($datetimeto));

                if ($irow->acting_trip != '') {
                    $dat[$Key + 1]['A'] = date("d-m-Y", strtotime($irow->result_date));
                    $dat[$Key + 1]['B'] = $irow->mdddevicename;
                    $dat[$Key + 1]['C'] = $irow->mddserialno;
                    $dat[$Key + 1]['D'] = $irow->pwy;
                    $dat[$Key + 1]['E'] = $irow->section;
                    $dat[$Key + 1]['F'] = $irow->bit;
                    $dat[$Key + 1]['G'] = $type;
                    $dat[$Key + 1]['H'] = $irow->acting_trip;
                    $dat[$Key + 1]['I'] = $irow->startpole;
                    $dat[$Key + 1]['J'] = $irow->stoppol;
                    $dat[$Key + 1]['K'] = round($irow->distancecover / 1000, 2) . ' km';
                    $dat[$Key + 1]['L'] = $irow->walk_org_distance_out;
                    $dat[$Key + 1]['M'] = round($irow->deviation_distance1 / 1000, 2) . ' km';
                    $dat[$Key + 1]['N'] = $irow->status;
                } else {
                    $dat[$Key + 1]['A'] = 'NA';
                    $dat[$Key + 1]['B'] = $irow->mdddevicename;
                    $dat[$Key + 1]['C'] = $irow->mddserialno;
                    $dat[$Key + 1]['D'] = $irow->pwy;
                    $dat[$Key + 1]['E'] = $irow->section;
                    $dat[$Key + 1]['F'] = $irow->bit;
                    $dat[$Key + 1]['G'] = $type;
                    $dat[$Key + 1]['H'] = 'NA';
                    $dat[$Key + 1]['I'] = $irow->startpole;
                    $dat[$Key + 1]['J'] = $irow->stoppol;
                    $dat[$Key + 1]['K'] = 'NA';
                    $dat[$Key + 1]['L'] = 'NA';
                    $dat[$Key + 1]['M'] = 'NA';
                    $dat[$Key + 1]['N'] = 'NA';
                }
                
                // Increment Key
                $Key++;
            }

            $filename = 'Duty_Completion_Report_'.'_'.time().'.xlsx';
            // Prepare the response (like Excel download, PDF, etc.)
            exceldownload($dat, $filename);
        }
    }

    public function distancexception()
    {
        if (!session()->has('login_sess_data')) {
            return redirect("/");
        }

        $data = [];
        $data['sessdata'] = $this->sessdata;
        $data['page_title'] = "Distance Exception Report";
        $data['usersdd'] = $this->commonModel->get_users();
        $data['date_from'] = '';
        $data['date_to'] = '';
        $data['report_type'] = 7;

        $data['pway'] = $this->db->table('public.user_login')
            ->select('organisation, user_id')
            ->where('active', 1)
            ->where('group_id', 8)
            ->get()
            ->getResult();

        $data['sse_pwy'] = $sse_pwy = trim($this->request->getPost('pway_id'));

        // Device dropdown based on group_id
        $query = "SELECT a.*, 
                  (SELECT device_name FROM {$this->schema}.master_device_setup 
                   WHERE id=(SELECT max(id) FROM {$this->schema}.master_device_setup 
                             WHERE inserttime::date <= current_date::date AND deviceid=a.did)) AS device_name 
                  FROM public.get_divice_details_record_for_list_for_company('{$this->schema}', {$this->sessdata['user_id']}) AS a 
                  WHERE a.group_id = 2 AND a.active = 1";

        $data['devicedropdown'] = $this->db->query($query)->getResult();
        if ($this->request->getMethod() == 'POST') {
            $user_id = $this->sessdata['user_id'];
            $group_id = $this->sessdata['group_id'];        
            $devices = $alerts = $geofences = $routes = $dids = null;

            $date_from = date("Y-m-d", strtotime(trim($this->request->getPost('date_from'))));
            $date_to = date("Y-m-d", strtotime(trim($this->request->getPost('date_to'))));
            $time_from = "00:00:00";
            $time_to = "23:59:00";          
            $data['date_from'] = date("d-m-Y H:i", strtotime(trim($this->request->getPost('date_from'))));
            $data['date_to'] = date("d-m-Y H:i", strtotime(trim($this->request->getPost('date_to'))));
            
            $data['pwi_id'] = $pwi_id = trim($this->request->getPost('user'));
            $data['pwi_name'] = trim($this->request->getPost('pwi_name'));
            $data['schema'] = $this->schema;
            $data['sse_pwy'] = $sse_pwy = trim($this->request->getPost('pway_id'));

            $devices .= "{";
            if ($data['pwi_name'] == 'All') {
                if ($sse_pwy == 'All') {
                    $query = "SELECT sup_pid, sup_gid, serial_no, imei_no, did, mobile_no, warranty_date, linked, 
                              parent_id, user_id, issudate, refunddate, active, issold, apply_scheam, 
                              group_id, role_id, email, address, pincode, state_name, country, username, 
                              firstname, lastname, organisation, group_name, '' AS list_item, 
                              '' AS list_item_name 
                              FROM public.get_divice_details_record_for_list('{$this->schema}', {$user_id}) 
                              WHERE sup_gid = {$group_id} AND user_id = {$user_id} AND active = 1 
                              ORDER BY did ASC";
                    $devicelist = $this->db->query($query)->getResult();
                } else {
                    $sectionlist = $this->db->table('public.user_login')
                        ->select('user_id')
                        ->where('parent_id', $sse_pwy)
                        ->where('active', 1)
                        ->get()
                        ->getResult();

                    $devicelist = [];
                    foreach ($sectionlist as $section) {
                        $sectionid = $section->user_id;
                        $query = "SELECT sup_pid, sup_gid, serial_no, imei_no, did, mobile_no, warranty_date, linked, 
                                  parent_id, user_id, issudate, refunddate, active, issold, apply_scheam, 
                                  group_id, role_id, email, address, pincode, state_name, country, username, 
                                  firstname, lastname, organisation, group_name, '' AS list_item, 
                                  '' AS list_item_name 
                                  FROM public.get_divice_details_record_for_list('{$this->schema}', {$sectionid}) 
                                  WHERE user_id = {$sectionid} AND active = 1 
                                  ORDER BY issudate ASC";
                        $lists = $this->db->query($query)->getResult();
                        $devicelist = array_merge($devicelist, $lists);
                    }
                }
            } else {
                $query = "SELECT sup_pid, sup_gid, serial_no, imei_no, did, mobile_no, warranty_date, linked, 
                          parent_id, user_id, issudate, refunddate, active, issold, apply_scheam, 
                          group_id, role_id, email, address, pincode, state_name, country, username, 
                          firstname, lastname, organisation, group_name, '' AS list_item, 
                          '' AS list_item_name 
                          FROM public.get_divice_details_record_for_list('{$this->schema}', {$pwi_id}) 
                          WHERE user_id = {$pwi_id} AND active = 1 
                          ORDER BY issudate ASC";
                $devicelist = $this->db->query($query)->getResult();
            }

            if (count($devicelist) > 0) {
                foreach ($devicelist as $devicelist_each) {
                    if ($devices == "{") {
                        $devices .= $devicelist_each->did;
                        $dids .= $devicelist_each->did;
                    } else {
                        $devices .= "," . $devicelist_each->did;
                        $dids .= "," . $devicelist_each->did;
                    }
                }
            }
            $devices .= "}";

            $data['device_id'] = $device_id = trim($this->request->getPost('device_id'));
            $data['report_type'] = $report_type = trim($this->request->getPost('report_type'));

            if (!empty($device_id)) {
                $dids = $device_id;
            }
            if ($dids == '') {
                $dids = 0;
            }

            $data['typeofuser'] = $typeofuser = trim($this->request->getPost('typeofuser'));

            $alldata = $this->db->query("SELECT mddd,mdddevicename,mddserialno,divicename, result_date, deviceid, acting_trip, 
            start_time, endtime,duration, distance_cover, sos_no, alert_no, call_no, totalnoofstop, totalstoptime, 
            polno, rd, dv, devicename, acting_triped, starttime,   end_time, duration1, distancecover, sosno, 
            alertno, callno, totalnoof_stop,  totalsto_ptime, pol_no, polename1, polnoend, polenameend1, polename, 
            polenameend, genid 
                                           FROM ( 
                                               (SELECT DISTINCT deviceid AS mddd, 
                                                       (SELECT device_name FROM {$this->schema}.master_device_setup 
                                                        WHERE deviceid=ax.deviceid 
                                                        AND id=(SELECT MAX(id) 
                                                                FROM {$this->schema}.master_device_setup 
                                                                WHERE inserttime::date<='$date_to'::date  
                                                                AND deviceid=ax.deviceid)) AS mdddevicename, 
                                                       (SELECT COALESCE(serial_no, 'Absent')  
                                                        FROM {$this->schema}.master_device_details  
                                                        WHERE superdevid=ax.deviceid OR id=ax.deviceid) AS mddserialno   
                                               FROM {$this->schema}.master_device_assign AS ax 
                                               WHERE deviceid IN ($dids) AND group_id=2) masterdevice 
                                               LEFT OUTER JOIN ( 
                                                   SELECT * 
                                                   FROM public.trip_spesified_device 
                                                   WHERE (genid, deviceid, result_date, acting_trip) IN ( 
                                                       SELECT MAX(genid), deviceid, result_date, acting_trip 
                                                       FROM public.trip_spesified_device 
                                                       WHERE totalstoptime > '00:00:00' 
                                                       AND deviceid IN ($dids) 
                                                       AND (result_date || ' ' || start_time >= '{$date_from} {$time_from}' 
                                                       AND result_date || ' ' || end_time <= '{$date_to} {$time_to}') 
                                                       GROUP BY deviceid, result_date, acting_trip) 
                                                   ORDER BY devicename, result_date, acting_trip
                                               ) resultdivice ON masterdevice.mddd=resultdivice.deviceid 
                                           ) resultset 
                                           ORDER BY result_date, mddd ASC")->getResult();

            $alldatanew = [];
            for ($i = 0; $i < count($alldata); $i++) {
                $deviceidnew = $alldata[$i]->deviceid;
                if ($deviceidnew != '') {
                    $serialno = $alldata[$i]->mddserialno;
                    $device_assign_details = $this->db->query("SELECT a.parent_user_id, a.current_user_id, b.organisation AS pwy,
                                                            c.organisation AS section, d.startpole, d.stoppol
                                                            FROM public.device_asign_details AS a
                                                            LEFT JOIN public.user_login AS b ON (a.parent_user_id = b.user_id)
                                                            LEFT JOIN public.user_login AS c ON (a.current_user_id = c.user_id)
                                                            LEFT JOIN {$this->schema}.device_assigne_pole_data AS d ON (a.serial_no = d.diviceno)
                                                            WHERE a.serial_no='{$serialno}' 
                                                            LIMIT 1")->getResult();
                    
                    $alldata[$i]->pwy = $device_assign_details[0]->pwy;
                    $alldata[$i]->section = $device_assign_details[0]->section;
                    
                    $devicename = $alldata[$i]->mdddevicename;
                    $devicenameArr = explode(':', $devicename);
                    $poledetailsnew = trim($devicenameArr[1]);
                    $poledetailsnewArr = explode('(', $poledetailsnew);
                    $polenamenew = trim($poledetailsnewArr[1]);
                    $polenamenew = str_replace(')', '', $polenamenew);
                    $polenamenewArr = explode('-', $polenamenew);
                    $alldata[$i]->startpole = $polenamenewArr[0];
                    $alldata[$i]->stoppol = $polenamenewArr[1];
                    $alldata[$i]->bit = $alldata[$i]->startpole . '-' . $alldata[$i]->stoppol;

                    $timedetails = $this->db->query("SELECT walk_org_distance 
                                                       FROM {$this->schema}.device_assigne_pole_data 
                                                       WHERE deviceid='{$deviceidnew}' 
                                                       AND endtime IS NOT NULL AND starttime IS NOT NULL")->getResult();
                    $alldata[$i]->walk_org_distance_out = $timedetails[0]->walk_org_distance;

                    $datetimefrom = $alldata[$i]->result_date . " " . $alldata[$i]->start_time;
                    $datetimeto = $alldata[$i]->result_date . " " . $alldata[$i]->endtime;
                    $from_datetime = date("Y-m-d H:i:s", strtotime($datetimefrom));  
                    $to_datetime = date("Y-m-d H:i:s", strtotime($datetimeto));
                    $get_history_summary = $this->db->query("SELECT distance_cover 
                                                               FROM public.get_histry_play_data_summary(?, ?, ?)", 
                                                               [$deviceidnew, $from_datetime, $to_datetime])->getResult();
                    $alldata[$i]->totaldistancecovere_taa = $get_history_summary[0]->distance_cover;
                    $alldata[$i]->deviation_distance1 = ($timedetails[0]->walk_org_distance * 1000) - $alldata[$i]->totaldistancecovere_taa;

                    if ($alldata[$i]->deviation_distance1 != '') {
                        if ($alldata[$i]->totaldistancecovere_taa > ($timedetails[0]->walk_org_distance * 1000) && $timedetails[0]->walk_org_distance != '') {
                            $alldata[$i]->status = 'Duty Completed';
                        } else if ($this->hasMinusSign($alldata[$i]->deviation_distance1)) {
                            $alldata[$i]->status = 'Duty Not Completed';
                        } else if ($alldata[$i]->deviation_distance1 > 0) {
                            $alldata[$i]->status = 'Duty Not Completed';
                        } else {
                            $alldata[$i]->status = 'Duty Completed';
                        }
                    } else {
                        $alldata[$i]->walk_org_distance_out = '0.0';
                        $alldata[$i]->deviation_distance1 = '0.0';
                        $alldata[$i]->status = 'Duty Not Completed';
                    }
                } else {
                    $alldata[$i]->walk_org_distance_out = '0.0';
                    $alldata[$i]->deviation_distance1 = '0.0';
                    $alldata[$i]->status = 'Duty Not Completed';
                }
            }

            foreach ($alldata as $dv) {
                if ($dv->mdddevicename != '' && $dv->result_date != '') {
                    if ($typeofuser != 'All') {
                        $mdddevicename = $this->db->query("SELECT device_name 
                                                             FROM {$this->schema}.master_device_setup 
                                                             WHERE deviceid='{$dv->mddd}'")->getResult();
                        $mdddevicename = $mdddevicename[0]->device_name;
                        $mdddevicename_arr = explode("/", $mdddevicename);
                        $u_type = $mdddevicename_arr[0];

                        if ((strtoupper($u_type) == strtoupper($typeofuser)) && ($dv->status != 'Duty Completed')) {
                            array_push($alldatanew, $dv);
                        }
                    } else {
                        if ($dv->status != 'Duty Completed') {
                            array_push($alldatanew, $dv);
                        }
                    }
                }
            }

            $data['alldata'] = $alldatanew;
        }

        $data['middle'] = view('traxreport/distancexceptionreport_view', $data);
        return view('mainlayout', $data);
    }

    public function offDutyReport()
    {
        ini_set('max_execution_time', 3000);
        ini_set('memory_limit', '-1');

        if (!session()->has('login_sess_data')) {
            return redirect("/");
        }

        $data = [];
        $data['sessdata'] = session()->get('login_sess_data');
        $user_id = $this->sessdata['user_id'];
        $data['page_title'] = "Off Duty Report";
        if ($this->request->getMethod() == 'POST') {
            $data['dt'] = $dt = $this->request->getPost('dt');
            $dt = date('Y-m-d', strtotime($dt));
            $data['type'] = $type = $this->request->getPost('type');
            $data['pwi'] = $pwi = $this->request->getPost('pwi');

            $cond = "1=1";
            if (!empty($type)) {
                $cond .= " AND LOWER(device_name) LIKE '%" . esc($type) . "%'";
            }
            if (!empty($pwi)) {
                $pwi = strtolower($pwi);
                $cond .= " AND LOWER(device_name) LIKE '%" . esc($pwi) . "%'";
            }

            $query = "SELECT * FROM (
                          SELECT lefttable.*, 
                          (SELECT device_name FROM {$this->schema}.master_device_setup 
                           WHERE id = (SELECT MAX(id) FROM {$this->schema}.master_device_setup 
                                        WHERE inserttime::date <= CURRENT_DATE::date AND deviceid = lefttable.deviceid)) AS device_name 
                          FROM public.get_right_panel_data('{$this->schema}', '{$dt}', {$user_id}) AS lefttable 
                          WHERE lefttable.group_id = 2 AND lefttable.deviceid IS NOT NULL AND status_color IS NULL
                      ) AS a 
                      WHERE {$cond} 
                      ORDER BY device_name";

            $data['report_data'] = $this->db->query($query)->getResult();
        }

        // Handling different user groups
        if ($this->sessdata['group_id'] == 3) {
            $data['pwidd'] = $this->db->query("SELECT lastname FROM public.user_login WHERE group_id = 8 ORDER BY lastname")->getResult();
        } elseif ($this->sessdata['group_id'] == 4) {
            $data['pwidd'] = $this->db->query("SELECT lastname FROM public.user_login WHERE group_id = 8 AND user_id IN (SELECT user_id FROM public.user_login WHERE parent_id IN (SELECT user_id FROM public.user_login WHERE parent_id IN ())) ORDER BY lastname")->getResult();
        } elseif ($this->sessdata['group_id'] == 5) {
            $data['pwidd'] = $this->db->query("SELECT lastname FROM public.user_login WHERE group_id = 8 AND user_id IN (SELECT user_id FROM public.user_login WHERE parent_id IN ({$user_id})) ORDER BY lastname")->getResult();
        } elseif ($this->sessdata['group_id'] == 8) {
            $data['pwidd'] = $this->db->query("SELECT lastname FROM public.user_login WHERE group_id = 8 AND user_id IN ({$user_id}) ORDER BY lastname")->getResult();
        } elseif ($this->sessdata['group_id'] == 2) {
            $data['pwidd'] = $this->db->query("SELECT lastname FROM public.user_login WHERE group_id = 8 AND user_id IN (SELECT parent_id FROM public.user_login WHERE user_id IN ({$user_id})) ORDER BY lastname")->getResult();
        }

        $data['middle'] = view('traxreport/offdutyreport', $data);
        return view('mainlayout', $data);
    }

    public function offdutyreportexcel() {
        // Check if the user session is valid
        if (!session()->has('login_sess_data')) {
            return redirect()->to('/');
        }
    
        // Load necessary session data
        $data['sessdata'] = session()->get('login_sess_data');
        $schema = $this->schema;
        $user_id = $this->sessdata['user_id'];
        
        // Check if POST data is available
        if ($this->request->getPost()) {
            // Retrieve form data
            $data['dt'] = $this->request->getPost('dt');
            $dt = date('Y-m-d', strtotime($data['dt']));
            $data['type'] = $type = $this->request->getPost('type');
            $data['pwi'] = $pwi = $this->request->getPost('pwi');
            
            // Initialize query conditions
            $cond = "1=1";
            if (!empty($type)) {
                $cond .= " and lower(device_name) like '%" . strtolower($type) . "%'";
            }
            if (!empty($pwi)) {
                $pwi = strtolower($pwi);
                $cond .= " and lower(device_name) like '%" . $pwi . "%'";
            }
    
            // Build query using Query Builder
            $query = "SELECT * FROM (
                          SELECT lefttable.*, 
                                 (SELECT device_name 
                                  FROM {$schema}.master_device_setup  
                                  WHERE id = (
                                      SELECT max(id) 
                                      FROM {$schema}.master_device_setup 
                                      WHERE inserttime::date <= current_date::date  
                                      AND deviceid = lefttable.deviceid 
                                  )) AS device_name 
                          FROM public.get_right_panel_data('{$schema}', '{$dt}', {$user_id}) AS lefttable 
                          WHERE lefttable.group_id = 2 
                            AND lefttable.deviceid IS NOT NULL 
                            AND status_color IS NULL
                      ) AS a 
                      WHERE {$cond} 
                      ORDER BY device_name";
    
            // Execute query and get result
            $report_data = $this->db->query($query)->getResult();
    
            // Prepare data for Excel
            $dat[0]['A'] = "Device ID";
            $dat[0]['B'] = "PWI";
            $dat[0]['C'] = "Type";
    
            $key = 0;
            foreach ($report_data as $report_data_each) {
                $mdddevicename_arr = explode("/", $report_data_each->device_name);

                if(count($mdddevicename_arr) >1) {
                    if (strpos(strtolower($mdddevicename_arr[2]), '(') !== false) {
                        $pwi = $mdddevicename_arr[1];
                    } else {
                        $pwi_arr = explode("(", $mdddevicename_arr[3]);
                        $pwi = $pwi_arr[0];
                    }
        
                    // Determine device type
                    if (strpos(strtolower($mdddevicename_arr[0]), 'stock') !== false) {
                        $type = 'Stock';
                    } elseif (strpos(strtolower($mdddevicename_arr[0]), 'keyman') !== false) {
                        $type = 'Keyman';
                    } elseif (strpos(strtolower($mdddevicename_arr[0]), 'patrolman') !== false) {
                        $type = 'Patrolman';
                    }
                } else {
                    $type = '';
                    $pwi = '';
                }
    
                
    
                // Fill the Excel data array
                $dat[$key + 1]['A'] = $report_data_each->serial_no . ' (' . $report_data_each->device_name . ')';
                $dat[$key + 1]['B'] = $pwi;
                $dat[$key + 1]['C'] = $type;
                $key++;
            }
    
            // Set filename for the report
            $filename = 'Off_Duty_Report_' . time() . '.xlsx';
    
            // Call the function to download the Excel file
            exceldownload($dat, $filename);
        }
    }   
    
    public function offDutyReportPdf()
    {
        // Check if session exists, if not, redirect
        if (!session()->has('login_sess_data')) {
            return redirect()->to('/');
        }
        
        $sessdata = $this->sessdata;
        $user_id = $sessdata['user_id']; // Assuming session data is stored as an associative array

        // Handle POST data
        if ($this->request->getPost()) {
            $data = [];
            $data['dt'] = $this->request->getPost('dt');
            $dt = date('Y-m-d', strtotime($data['dt'])); // Format date as 'Y-m-d'
            $data['type'] = $type = $this->request->getPost('type');
            $data['pwi'] = $pwi = $this->request->getPost('pwi');

            // Building the condition for the query
            $cond = "1=1";
            if (!empty($type)) {
                $cond .= " and lower(device_name) like '%{$type}%'";
            }
            if (!empty($pwi)) {
                $pwi = strtolower($pwi);
                $cond .= " and lower(device_name) like '%{$pwi}%'";
            }

            // Query to fetch report data
            $query = "SELECT * FROM (
                        SELECT lefttable.*, 
                               (SELECT device_name 
                                FROM {$this->schema}.master_device_setup 
                                WHERE id = (SELECT MAX(id) 
                                            FROM {$this->schema}.master_device_setup 
                                            WHERE inserttime::date <= CURRENT_DATE::date 
                                            AND deviceid = lefttable.deviceid)) AS device_name 
                        FROM public.get_right_panel_data('{$this->schema}', '{$dt}', {$user_id}) AS lefttable 
                        WHERE lefttable.group_id = 2 
                        AND lefttable.deviceid IS NOT NULL 
                        AND status_color IS NULL
                    ) AS a 
                    WHERE {$cond} 
                    ORDER BY device_name";

            $report_data = $this->db->query($query)->getResult(); // Query the database

            // Start building the HTML for the PDF
            $html = '<table width="100%">
                        <tr>
                            <td align="center"><strong>Off Duty Report</strong></td>
                        </tr>
                    </table>';

            $html .= '<table width="100%" style="border-collapse: collapse; margin-top: 30px;" border="1">
                        <tr>
                            <th>Device ID</th>
                            <th>PWI</th>
                            <th>Type</th>
                        </tr>';

            foreach ($report_data as $report_data_each) {

                $mdddevicename_arr = explode("/", $report_data_each->device_name);
                if(count($mdddevicename_arr) >1) {
                    if (strpos(strtolower($mdddevicename_arr[2]), '(') !== false) {
                        $pwi = $mdddevicename_arr[1];
                    } else {
                        $pwi_arr = explode("(", $mdddevicename_arr[3]);
                        $pwi = $pwi_arr[0];
                    }

                    if (strpos(strtolower($mdddevicename_arr[0]), 'stock') !== false) {
                        $type = 'Stock';
                    } elseif (strpos(strtolower($mdddevicename_arr[0]), 'keyman') !== false) {
                        $type = 'Keyman';
                    } elseif (strpos(strtolower($mdddevicename_arr[0]), 'patrolman') !== false) {
                        $type = 'Patrolman';
                    }
                } else {
                    $type = '';
                    $pwi = '';
                }

                $html .= '<tr>
                            <td>' . $report_data_each->serial_no . ' (' . $report_data_each->device_name . ')</td>
                            <td>' . $pwi . '</td>
                            <td>' . $type . '</td>
                        </tr>';
            }

            $html .= '</table>';

            // Set PDF file name and generate the PDF
            // $this->makepdf->setFileName('Off_Duty_Report_' . time());
            // $this->makepdf->setContent($html);
            // $this->makepdf->getPdf(); // Output the PDF

            // Instantiate the MakePDF class
            $pdf = new MakePDF();

            // Set the filename and content
            $pdf->setFileName('Off_Duty_Report_' . time());
            $pdf->setContent($html);

            // Generate and stream the PDF to the browser
            $pdf->getPdf();  // true to stream the PDF
        }
    }
    
    public function timeexception() {
        if (!session()->get('login_sess_data')) {
            return redirect()->to("/");
        }

        $user_id = $this->sessdata['user_id'];
        $group_id = $this->sessdata['group_id'];

        $data = [];
        $data['sessdata'] = $this->sessdata;
        $data['page_title'] = "Time Exception Report";
        $data['usersdd'] = $this->commonModel->get_users();
        $data['date_from'] = '';
        $data['date_to'] = '';
        $data['report_type'] = 7;
        $data['pway'] = $this->db->query("SELECT organisation, user_id FROM public.user_login WHERE active = 1 AND group_id = 8")->getResult();

        $data['sse_pwy'] = $sse_pwy = trim($this->request->getPost('pway_id'));

        if ($group_id == 3) { // distributor
            $data['devicedropdown'] = $this->db->query("SELECT a.*, (SELECT device_name FROM {$this->schema}.master_device_setup WHERE id = (SELECT MAX(id) FROM {$this->schema}.master_device_setup WHERE inserttime::date <= current_date::date AND deviceid = a.did)) AS device_name FROM public.get_divice_details_record_for_list_for_company('{$this->schema}', {$user_id}) AS a WHERE a.group_id = 2 AND a.active = 1")->getResult();
        } else { // others
            $data['devicedropdown'] = $this->db->query("SELECT a.*, (SELECT device_name FROM {$this->schema}.master_device_setup WHERE id = (SELECT MAX(id) FROM {$this->schema}.master_device_setup WHERE inserttime::date <= current_date::date AND deviceid = a.did)) AS device_name FROM public.get_divice_details_record_for_list_for_company('{$this->schema}', {$user_id}) AS a WHERE a.group_id = 2 AND a.active = 1")->getResult();
        }

        if ($this->request->getPost()) {

            $devices = $alerts = $geofences = $routes = $dids = null;

            $date_from = date("Y-m-d", strtotime(trim($this->request->getPost('date_from'))));
            $date_to = date("Y-m-d", strtotime(trim($this->request->getPost('date_to'))));
            $time_from = "00:00:00"; 
            $time_to = "23:59:00"; 
            $data['date_from'] = date("d-m-Y H:i", strtotime(trim($this->request->getPost('date_from'))));
            $data['date_to'] = date("d-m-Y H:i", strtotime(trim($this->request->getPost('date_to'))));

            $data['pwi_id'] = $pwi_id = trim($this->request->getPost('user'));
            $data['pwi_name'] = trim($this->request->getPost('pwi_name'));
            $data['schema'] = $this->schema;
            $data['sse_pwy'] = $sse_pwy = trim($this->request->getPost('pway_id'));

            $devices .= "{";
            if ($data['pwi_name'] == 'All') {
                if ($sse_pwy == 'All') {
                    $query = "SELECT sup_pid, sup_gid, serial_no, imei_no, did, mobile_no, warranty_date, linked, parent_id, user_id, issudate,
                                refunddate, active, issold, apply_scheam, group_id, role_id, email, address, pincode, state_name, country, username, firstname,
                                lastname, organisation, group_name, '' AS list_item, '' AS list_item_name 
                              FROM public.get_divice_details_record_for_list('".$this->schema."',".$user_id.") 
                              WHERE sup_gid = $group_id AND user_id = ".$user_id." AND active = 1 
                              ORDER BY did ASC";
                    $devicelist = $this->db->query($query)->getResult();
                } else {
                    $sectionlist = $this->db->query("SELECT user_id FROM public.user_login WHERE parent_id = '".$sse_pwy."' AND active = 1")->getResult();
                    $devicelist = [];
                    foreach ($sectionlist as $section) {
                        $sectionid = $section->user_id;
                        $query = "SELECT sup_pid, sup_gid, serial_no, imei_no, did, mobile_no, warranty_date, linked, parent_id, user_id, issudate,
                                    refunddate, active, issold, apply_scheam, group_id, role_id, email, address, pincode, state_name, country, username, firstname,
                                    lastname, organisation, group_name, '' AS list_item, '' AS list_item_name 
                                  FROM public.get_divice_details_record_for_list('".$this->schema."',".$sectionid.") 
                                  WHERE user_id = ".$sectionid." AND active = 1 
                                  ORDER BY issudate ASC";
                        $lists = $this->db->query($query)->getResult();
                        $devicelist = array_merge($devicelist, $lists);
                    }
                }
            } else {
                $query = "SELECT sup_pid, sup_gid, serial_no, imei_no, did, mobile_no, warranty_date, linked, parent_id, user_id, issudate,
                            refunddate, active, issold, apply_scheam, group_id, role_id, email, address, pincode, state_name, country, username, firstname,
                            lastname, organisation, group_name, '' AS list_item, '' AS list_item_name 
                          FROM public.get_divice_details_record_for_list('".$this->schema."',".$pwi_id.") 
                          WHERE user_id = {$pwi_id} AND active = 1 
                          ORDER BY issudate ASC";
                $devicelist = $this->db->query($query)->getResult();
            }

            if (count($devicelist) > 0) {
                foreach ($devicelist as $devicelist_each) {
                    $devices .= $devices == "{" ? $devicelist_each->did : "," . $devicelist_each->did;
                    $dids .= $dids == "" ? $devicelist_each->did : "," . $devicelist_each->did;
                }
            }
            $devices .= "}";
            $data['device_id'] = $device_id = trim($this->request->getPost('device_id'));
            $data['report_type'] = $report_type = trim($this->request->getPost('report_type'));

            if (!empty($device_id)) {
                $dids = $device_id;
            }
            if ($dids == '') {
                $dids = 0;
            }
            $data['typeofuser'] = $typeofuser = trim($this->request->getPost('typeofuser'));

            $alldata = $this->db->query("SELECT mddd, mdddevicename, mddserialno, devicename, result_date, deviceid, acting_trip, start_time, endtime, duration, distance_cover, sos_no, alert_no, call_no, totalnoofstop, totalstoptime, polno, rd, dv, devicename, acting_triped, starttime, end_time, duration1, distancecover, sosno, alertno, callno, totalnoof_stop, totalsto_ptime, pol_no, polename1, polnoend, polenameend1, polename, polenameend, genid 
                                FROM ((SELECT DISTINCT deviceid AS mddd, (SELECT device_name FROM ".$this->schema.".master_device_setup WHERE deviceid = ax.deviceid AND id = (SELECT MAX(id) FROM ".$this->schema.".master_device_setup WHERE inserttime::date <= '$date_to'::date AND deviceid = ax.deviceid)) AS mdddevicename, (SELECT COALESCE(serial_no, 'Absent') FROM ".$this->schema.".master_device_details WHERE superdevid = ax.deviceid OR id = ax.deviceid) AS mddserialno 
                                        FROM ".$this->schema.".master_device_assign AS ax WHERE deviceid IN ($dids) AND group_id = 2) masterdevice 
                                LEFT OUTER JOIN (SELECT * FROM public.trip_spesified_device WHERE (genid, deviceid, result_date, acting_trip) IN (SELECT MAX(genid), deviceid, result_date, acting_trip FROM public.trip_spesified_device WHERE totalstoptime > '00:00:00' AND deviceid IN ($dids) AND (result_date || ' ' || start_time >= '".$date_from." ".$time_from."' AND result_date || ' ' || end_time <= '".$date_to." ".$time_to."') GROUP BY deviceid, result_date, acting_trip) ORDER BY devicename, result_date, acting_trip) resultdivice ON masterdevice.mddd = resultdivice.deviceid) resultset 
                                ORDER BY result_date, mddd ASC")->getResult();

            $alldatanew = [];
            foreach ($alldata as $item) {
                $deviceidnew = $item->deviceid;
                if ($deviceidnew != '') {
                    $serialno = $item->mddserialno;
                    $device_assign_details = $this->db->query("SELECT a.parent_user_id, a.current_user_id, b.organisation AS pwy, c.organisation AS section, d.startpole, d.stoppol 
                                                                FROM public.device_asign_details AS a 
                                                                LEFT JOIN public.user_login AS b ON (a.parent_user_id = b.user_id) 
                                                                LEFT JOIN public.user_login AS c ON (a.current_user_id = c.user_id) 
                                                                LEFT JOIN {$this->schema}.device_assigne_pole_data AS d ON (a.serial_no = d.diviceno) 
                                                                WHERE a.serial_no = '".$serialno."' 
                                                                LIMIT 1")->getResult();
                    $item->pwy = $device_assign_details[0]->pwy ?? null;
                    $item->section = $device_assign_details[0]->section ?? null;

                    $devicename = $item->mdddevicename;
                    $devicenameArr = explode(':', $devicename);
                    $poledetailsnew = trim($devicenameArr[1]);
                    $poledetailsnewArr = explode('(', $poledetailsnew);
                    $polenamenew = trim($poledetailsnewArr[1]);
                    $polenamenew = str_replace(')', '', $polenamenew);
                    $polenamenewArr = explode('-', $polenamenew);
                    $item->startpole = $polenamenewArr[0];
                    $item->stoppol = $polenamenewArr[1];
                    $item->bit = $item->startpole.'-'.$item->stoppol;

                    $timedetails = $this->db->query("SELECT starttime, endtime, justify_interval(endtime - starttime) AS durationorgtime 
                                                        FROM {$this->schema}.device_assigne_pole_data WHERE deviceid = '".$deviceidnew."' 
                                                        AND endtime IS NOT NULL AND starttime IS NOT NULL")->getResult();
                    $item->starttime_org_out = $timedetails[0]->starttime ?? '00:00:00';
                    $item->endtime_org_out = $timedetails[0]->endtime ?? '00:00:00';
                    $item->durationorgtime_org_out = $timedetails[0]->durationorgtime ?? '00:00:00';

                    if ($item->starttime_org_out != '' && $item->endtime_org_out != '') {
                        $item->durationorgtime_org_out = abs($item->durationorgtime_org_out);
                        if ((strtotime($item->starttime_org_out) < strtotime($item->start_time)) && ($this->convertsecond($item->durationorgtime_org_out) > $this->convertsecond($item->duration))) {
                            $item->status = 'Duty Not Completed';
                        } else {
                            $item->status = 'Duty Completed';
                        }
                    } else {
                        $item->starttime_org_out = '00:00:00';
                        $item->endtime_org_out = '00:00:00';
                        $item->durationorgtime_org_out = '00:00:00';
                        $item->status = 'Duty Not Completed';
                    }
                } else {
                    $item->starttime_org_out = '00:00:00';
                    $item->endtime_org_out = '00:00:00';
                    $item->durationorgtime_org_out = '00:00:00';
                    $item->status = 'Duty Not Completed';
                }

                if ($item->mdddevicename != '' && $item->result_date != '') {
                    if ($typeofuser != 'All') {
                        $mdddevicename = $this->db->query("SELECT device_name FROM {$this->schema}.master_device_setup WHERE deviceid = '".$item->mddd."'")->getResult();
                        $mdddevicename = $mdddevicename[0]->device_name;
                        $mdddevicename_arr = explode("/", $mdddevicename);
                        $u_type = $mdddevicename_arr[0];
                        if ((strtoupper($u_type) == strtoupper($typeofuser)) && ($item->status != 'Duty Completed')) {
                            $alldatanew[] = $item;
                        }
                    } else {
                        if ($item->status != 'Duty Completed') {
                            $alldatanew[] = $item;
                        }
                    }
                }
            }
            $data['alldata'] = $alldatanew;
        }

        $data['middle'] = view('traxreport/timeexceptionreport_view', $data);
        return view('mainlayout', $data);
    }

    public function batterypercentage() {
        if (!session()->get('login_sess_data')) {
            return redirect()->to("/");
        }

        $user_id = $this->sessdata['user_id'];
        $group_id = $this->sessdata['group_id'];

        $data = [];
        $data['sessdata'] = $this->sessdata;
        $data['page_title'] = "Battery Percentage Report";
        $data['usersdd'] = $this->commonModel->get_users();
        $data['date_from'] = '';

        if ($this->request->getPost()) {
            $data['stdt'] = date("d-m-Y", strtotime(trim($this->request->getPost('stdt'))));
            $data['pwi_id'] = $pwi_id = trim($this->request->getPost('user'));
            $data['pwi_name'] = trim($this->request->getPost('pwi_name'));
            $sse_pwy = trim($this->request->getPost('pway_id'));
            $date = date("Y-m-d", strtotime(trim($this->request->getPost('stdt'))));
            $data['date_from'] = date("d-m-Y H:i", strtotime(trim($this->request->getPost('stdt'))));
            $data['strt'] = $starttime = $this->request->getPost('strt');
            $starttimemain = $starttime . ':00';
            $data['endtime'] = $endtime = $this->request->getPost('endtime');
            $endtimemain = $endtime . ':00';

            if ($data['pwi_name'] == 'All') {
                if ($sse_pwy == 'All') {
                    $query = "SELECT sup_pid, sup_gid, serial_no, imei_no, did, mobile_no, warranty_date, linked, parent_id, user_id, issudate,
                        refunddate, active, issold, apply_scheam, group_id, role_id, email, address, pincode, state_name, country, username, firstname,
                        lastname, organisation, group_name, '' AS list_item, '' AS list_item_name FROM public.get_divice_details_record_for_list('" . $this->schema . "', $user_id) WHERE sup_gid = $group_id AND user_id = $user_id AND active = 1 ORDER BY did ASC";
                    $devicelist = $this->db->query($query)->getResult();
                } else {
                    $sectionlist = $this->db->query("SELECT user_id FROM public.user_login WHERE parent_id = '" . $sse_pwy . "' AND active = 1")->getResult();
                    $devicelist = [];
                    foreach ($sectionlist as $section) {
                        $sectionid = $section->user_id;
                        $query = "SELECT sup_pid, sup_gid, serial_no, imei_no, did, mobile_no, warranty_date, linked, parent_id, user_id, issudate,
                            refunddate, active, issold, apply_scheam, group_id, role_id, email, address, pincode, state_name, country, username, firstname,
                            lastname, organisation, group_name, '' AS list_item, '' AS list_item_name FROM public.get_divice_details_record_for_list('" . $this->schema . "', $sectionid) WHERE user_id = $sectionid AND active = 1 ORDER BY issudate ASC";
                        $lists = $this->db->query($query)->getResult();
                        $devicelist = array_merge($devicelist, $lists);
                    }
                }
            } else {
                $query = "SELECT sup_pid, sup_gid, serial_no, imei_no, did, mobile_no, warranty_date, linked, parent_id, user_id, issudate,
                    refunddate, active, issold, apply_scheam, group_id, role_id, email, address, pincode, state_name, country, username, firstname,
                    lastname, organisation, group_name, '' AS list_item, '' AS list_item_name FROM public.get_divice_details_record_for_list('" . $this->schema . "', $pwi_id) WHERE user_id = $pwi_id AND active = 1 ORDER BY issudate ASC";
                $devicelist = $this->db->query($query)->getResult();
            }

            $dids = [];
            if (count($devicelist) > 0) {
                foreach ($devicelist as $devicelist_each) {
                    $dids[] = $devicelist_each->did;
                }
                $dids = implode(',', $dids);
            } else {
                $dids = 0;
            }

            $devicelist = $this->db->query("SELECT DISTINCT(a.deviceid), b.device_name, c.serial_no
                                            FROM {$this->schema}.traker_positionaldata AS a
                                            LEFT JOIN {$this->schema}.master_device_details AS c ON (a.deviceid = c.superdevid)
                                            LEFT JOIN {$this->schema}.master_device_setup AS b ON (c.superdevid = b.deviceid)
                                            WHERE a.currentdate = '$date'
                                            AND a.currenttime BETWEEN '$starttimemain' AND '$endtimemain'
                                            AND a.deviceid IN ($dids)")->getResult();

            foreach ($devicelist as $device) {
                $deviceid = $device->deviceid;
                $startbattery = $this->db->query("SELECT batterystats, currenttime FROM {$this->schema}.traker_positionaldata WHERE deviceid = '$deviceid' AND currentdate = '$date' AND currenttime > '$starttimemain' ORDER BY currenttime ASC LIMIT 1")->getResult();
                $device->startbatterypercentage = !empty($startbattery) ? $startbattery[0]->batterystats . ' %' : 'N/A';
                $device->starttime = !empty($startbattery) ? $startbattery[0]->currenttime : 'N/A';

                $endbattery = $this->db->query("SELECT batterystats, currenttime FROM {$this->schema}.traker_positionaldata WHERE deviceid = '$deviceid' AND currentdate = '$date' AND currenttime < '$endtimemain' ORDER BY currenttime DESC LIMIT 1")->getResult();
                $device->endbatterypercentage = !empty($endbattery) ? $endbattery[0]->batterystats . ' %' : 'N/A';
                $device->endtime = !empty($endbattery) ? $endbattery[0]->currenttime : 'N/A';
            }

            $data['alldata'] = $devicelist;
            $data['sse_pwy'] = $sse_pwy;
        }

        $data['pway'] = $this->db->query("SELECT organisation, user_id FROM public.user_login WHERE active = 1 AND group_id = 8")->getResult();
        $data['middle'] = view('traxreport/batterypercentagereport_view', $data);
        return view('mainlayout', $data);
    }

    private function hasMinusSign($value)
    {
        return strpos($value, '-') !== false;
    }

    public function convertsecond($duration) {
		$duration_arr = explode(":",$duration);
		$second = ($duration_arr[0]*3600) + ($duration_arr[1]*60) + $duration_arr[2];
		return $second;
	}

    public function alertReport()
    {
        // Check if the user session exists
        if (!session()->has('login_sess_data')) {
            return redirect()->to('/');
        }

        $data = [];
        $data['sessdata'] = $this->sessdata;
        $data['page_title'] = "Alert Report";

        $schema = $this->schema; // Adjust this if schema prefix is different

        // Handle form submission
        if ($this->request->getMethod() == 'POST' && $this->request->getPost('search')) {
            $device_id = $this->request->getPost('device_id') ?? '';
            $config_id = $this->request->getPost('config_id') ?? '';
            $dt = $this->request->getPost('dt') ?? '';

            $cond = "WHERE 1 = 1";
            if (!empty($device_id)) {
                $cond .= " AND tdad.deviceid = " . $this->db->escape($device_id);
            }
            if (!empty($config_id)) {
                $cond .= " AND tdad.config_code = " . $this->db->escape($config_id);
            }
            if (!empty($dt)) {
                $cond .= " AND tdad.currentdate = " . $this->db->escape(date('Y-m-d', strtotime($dt)));
            }

            $query = "SELECT tdad.*, mdd.serial_no 
                    FROM {$schema}.traker_device_alart_data AS tdad
                    LEFT JOIN {$schema}.master_device_details AS mdd ON mdd.superdevid = tdad.deviceid 
                    INNER JOIN {$schema}.generate_sms_mail AS gsm ON tdad.id = gsm.alert_id 
                    {$cond} 
                    ORDER BY tdad.currentdate, tdad.currenttime DESC";

            $data['report_data'] = $this->db->query($query)->getResult();
        }

        // Fetch device dropdown data
        $user_id = $this->sessdata['user_id'];
        $group_id = $this->sessdata['group_id'];

        $deviceQuery = "SELECT a.*, 
                            (SELECT device_name 
                                FROM {$schema}.master_device_setup 
                                WHERE id = (SELECT MAX(id) 
                                            FROM {$schema}.master_device_setup 
                                            WHERE inserttime::date <= CURRENT_DATE::date AND deviceid = a.did)) 
                                AS device_name 
                        FROM public.get_divice_details_record_for_list_for_company('{$schema}', {$user_id}) AS a 
                        WHERE a.group_id = 2 AND a.active = 1";

        $data['devicedropdown'] = $this->db->query($deviceQuery)->getResult();

        // Fetch alert dropdown data
        $data['master_alart_dd'] = $this->db->query("SELECT id, description FROM public.master_alart WHERE active = 1 ORDER BY description")->getResult();

        // Load the view
        $data['middle'] = view('traxreport/alertreport', $data);
        return view('mainlayout', $data);
    }

    public function activityTimeSegment()
    {
        // Check if user is logged in
        if (!session()->has('login_sess_data')) {
            return redirect()->to('/');
        }

        // Prepare data
        $data = [];
        $data['sessdata'] = $this->sessdata;

        if ($this->request->getMethod() == 'POST') {
            $user_id = $this->sessdata['user_id'];
            $group_id = $this->sessdata['group_id'];
            $data['dt'] = date("d-m-Y", strtotime(trim($this->request->getPost('stdt'))));
            $data['pwi_id'] = $pwi_id = trim($this->request->getPost('user'));
            $data['pwi_name'] = trim($this->request->getPost('pwi_name'));
            $data['device_id'] = $device_id = trim($this->request->getPost('device_id'));
            $data['typeofuser'] = $typeofuser = trim($this->request->getPost('typeofuser'));
            $sse_pwy = trim($this->request->getPost('pway_id'));
            $data['strt'] = $starttime = $this->request->getVar('strt');
            $starttime = $starttime . ':00';
            $data['endtime'] = $endtime = $this->request->getVar('endtime');
            $endtime = $endtime . ':00';

            $date_from = date("Y-m-d", strtotime(trim($this->request->getPost('stdt')))) . " " . $starttime;
            $date_to = date("Y-m-d", strtotime(trim($this->request->getPost('stdt')))) . " " . $endtime;

            if ($device_id == '') {
                $devices = '';
                $dids = '';
                if ($data['pwi_name'] == 'All') {
                    if ($sse_pwy == 'All') {
                        $query = "SELECT sup_pid, sup_gid, serial_no, imei_no, did, mobile_no, warranty_date, linked, parent_id, user_id, issudate,
                            refunddate, active, issold, apply_scheam, group_id, role_id, email, address, pincode, state_name, country, username, firstname,
                            lastname, organisation, group_name, '' as list_item, '' as list_item_name
                            FROM public.get_divice_details_record_for_list('{$this->schema}', {$user_id})
                            WHERE sup_gid = {$group_id} AND user_id = {$user_id} AND active = 1
                            ORDER BY did ASC";
                        $devicelist = $this->db->query($query)->getResult();
                    } else {
                        $sectionlist = $this->db->query("SELECT user_id FROM public.user_login WHERE parent_id = '{$sse_pwy}' AND active = 1")->getResult();
                        $devicelist = [];
                        foreach ($sectionlist as $section) {
                            $sectionid = $section->user_id;
                            $query = "SELECT sup_pid, sup_gid, serial_no, imei_no, did, mobile_no, warranty_date, linked, parent_id, user_id, issudate,
                                refunddate, active, issold, apply_scheam, group_id, role_id, email, address, pincode, state_name, country, username, firstname,
                                lastname, organisation, group_name, '' as list_item, '' as list_item_name
                                FROM public.get_divice_details_record_for_list('{$this->schema}', {$sectionid})
                                WHERE user_id = {$sectionid} AND active = 1
                                ORDER BY issudate ASC";
                            $lists = $this->db->query($query)->getResult();
                            foreach ($lists as $list) {
                                $devicelist[] = $list;
                            }
                        }
                    }
                } else {
                    $query = "SELECT sup_pid, sup_gid, serial_no, imei_no, did, mobile_no, warranty_date, linked, parent_id, user_id, issudate,
                        refunddate, active, issold, apply_scheam, group_id, role_id, email, address, pincode, state_name, country, username, firstname,
                        lastname, organisation, group_name, '' as list_item, '' as list_item_name
                        FROM public.get_divice_details_record_for_list('{$this->schema}', {$pwi_id})
                        WHERE user_id = {$pwi_id} AND active = 1
                        ORDER BY issudate ASC";
                    $devicelist = $this->db->query($query)->getResult();
                }

                if (count($devicelist) > 0) {
                    $devices_arr = [];
                    foreach ($devicelist as $devicelist_each) {
                        if ($devices == "") {
                            $devices .= $devicelist_each->did;
                            $dids .= $devicelist_each->did;
                        } else {
                            $devices .= "," . $devicelist_each->did;
                            $dids .= "," . $devicelist_each->did;
                        }
                    }
                    $devices_arr = explode(',', $devices);
                }
            } else {
                $devices_arr[] = $device_id;
            }
            // Assume $devices_arr and $typeofuser are already set (from previous logic)
            $new_devices_arr = [];
            if ($typeofuser != 'All') {
                // Loop through devices array to filter based on user type
                foreach ($devices_arr as $device_id) {
                    if ($device_id != '') {
                        // Query device name details
                        $device_name_details = $this->db->query("SELECT device_name FROM {$this->schema}.master_device_setup WHERE deviceid = ?", [$device_id])->getRow();
                        
                        if ($device_name_details) {
                            $device_name = $device_name_details->device_name;
                            $device_name_arr = explode('/', $device_name);
                            $user_type = $device_name_arr[0];
                            
                            // If the user type matches the specified one, add to new devices array
                            if (strtoupper($user_type) == strtoupper($typeofuser)) {
                                $new_devices_arr[] = $device_id;  // Add device to new devices array
                            }
                        }
                    }
                }
            } else {
                // If 'All', just add all devices
                $new_devices_arr = $devices_arr;  // Copy all devices from devices_arr
            }
            $report_data = [];
            // Loop through the new_devices_arr (array of device IDs)
            foreach ($new_devices_arr as $device_id) {
                if ($device_id != '') {
                    // Get device name details using query builder
                    $device_name_details = $this->db->table($this->schema.'.master_device_setup')
                        ->select('device_name')
                        ->where('deviceid', $device_id)
                        ->get()
                        ->getRow();

                    if ($device_name_details) {
                        $device_name = $device_name_details->device_name;
                        $device_name_arr = explode('/', $device_name);
                        $user_type = $device_name_arr[0];

                        // Check if the device is assigned
                        $assignment_details = $this->db->table('public.master_device_assign')
                            ->select('count(*) as counter')
                            ->where('deviceid', $device_id)
                            ->where('group_id', 2)
                            ->where('active', 1)
                            ->get()
                            ->getRow();

                        $counter = $assignment_details->counter;

                        if ($counter > 0) {
                            if ($user_type == 'Patrolman') {
                                // Get data for Patrolman
                                $data = $this->db->query(
                                    "SELECT a.*, mdd.serial_no, msd.device_name
                                    FROM public.get_histry_play_data_summary(?, ?, ?) AS a
                                    LEFT JOIN {$this->schema}.master_device_details AS mdd ON mdd.superdevid = a.deviceid
                                    LEFT JOIN {$this->schema}.master_device_setup AS msd ON msd.deviceid = a.deviceid",
                                    [$device_id, $date_from, $date_to]
                                )->getResult();

                                if (count($data) > 0) {
                                    $serialno = $data[0]->serial_no;
                                    // Get assignment details
                                    $device_assign_details = $this->db->query("select a.parent_user_id,a.current_user_id,b.organisation as pwy,c.organisation as section
                                    from public.device_asign_details as a
                                    left join public.user_login as b on (a.parent_user_id = b.user_id)
                                    left join public.user_login as c on (a.current_user_id = c.user_id)
                                    where a.serial_no='".$serialno."'")->getRow();

                                    // Add to report data
                                    $length = count($report_data);
                                    $report_data[$length][0] = [
                                        'pwy' => $device_assign_details->pwy,
                                        'section' => $device_assign_details->section,
                                        'result_date' => $data[0]->result_date,
                                        'deviceid' => $data[0]->deviceid,
                                        'user_type' => $user_type,
                                        'parent_id' => $data[0]->parent_id,
                                        'user_id' => $data[0]->user_id,
                                        'group_id' => $data[0]->group_id,
                                        'start_time' => $data[0]->start_time,
                                        'end_time' => $data[0]->end_time,
                                    ];

                                    $newduration = $this->db->query(
                                        "SELECT age(?::timestamp, ?::timestamp) AS duration",
                                        [$data[0]->result_date . ' ' . $data[0]->end_time, $data[0]->result_date . ' ' . $data[0]->start_time]
                                    )->getRow();

                                    $report_data[$length][0]['duration'] = $newduration->duration;
                                    $report_data[$length][0]['distance_cover'] = $data[0]->distance_cover;  // Assuming $data1 is available elsewhere
                                    $report_data[$length][0]['sos_no'] = 0;
                                    $report_data[$length][0]['alert_no'] = 0;
                                    $report_data[$length][0]['call_no'] = 0;
                                    $report_data[$length][0]['serial_no'] = $data[0]->serial_no;
                                    $report_data[$length][0]['device_name'] = $data[0]->device_name;

                                    // Get organisation details
                                    $organisation = $this->db->table('public.user_login')
                                        ->select('organisation')
                                        ->where('user_id', $data[0]->user_id)
                                        ->where('active', 1)
                                        ->get()
                                        ->getRow();

                                    $report_data[$length][0]['organisation'] = $organisation->organisation;

                                    // Modify device name (example manipulation)
                                    $PWI = explode("(", $data[0]->device_name);
                                    $newPwI = '';
                                    for ($a = 1; $a < count($PWI); $a++) {
                                        $newPwI = $newPwI . $PWI[$a];
                                    }
                                    $newPwI = '(' . $newPwI;
                                    $report_data[$length][0]['newPwI'] = $newPwI;
                                }
                            } else {
                                // Similar logic for other user types (non-patrolman)
                                $data = $this->db->query(
                                    "SELECT a.*, mdd.serial_no, msd.device_name
                                    FROM public.get_histry_play_data_summary(?, ?, ?) AS a
                                    LEFT JOIN {$this->schema}.master_device_details AS mdd ON mdd.superdevid = a.deviceid
                                    LEFT JOIN {$this->schema}.master_device_setup AS msd ON msd.deviceid = a.deviceid",
                                    [$device_id, $date_from, $date_to]
                                )->getResult();

                                if (count($data) > 0) {
                                    $serialno = $data[0]->serial_no;
                                    // Get assignment details
                                    $device_assign_details = $this->db->query("select a.parent_user_id,a.current_user_id,b.organisation as pwy,c.organisation as section
                                    from public.device_asign_details as a
                                    left join public.user_login as b on (a.parent_user_id = b.user_id)
                                    left join public.user_login as c on (a.current_user_id = c.user_id)
                                    where a.serial_no='".$serialno."'")->getRow();

                                    // Add to report data
                                    $length = count($report_data);
                                    $report_data[$length][0] = [
                                        'pwy' => $device_assign_details->pwy,
                                        'section' => $device_assign_details->section,
                                        'result_date' => $data[0]->result_date,
                                        'deviceid' => $data[0]->deviceid,
                                        'user_type' => $user_type,
                                        'parent_id' => $data[0]->parent_id,
                                        'user_id' => $data[0]->user_id,
                                        'group_id' => $data[0]->group_id,
                                        'start_time' => $data[0]->start_time,
                                        'end_time' => $data[0]->end_time,
                                        'duration' => $data[0]->duration,
                                        'distance_cover' => $data[0]->distance_cover,
                                        'sos_no' => 0,
                                        'alert_no' => 0,
                                        'call_no' => 0,
                                        'serial_no' => $data[0]->serial_no,
                                        'device_name' => $data[0]->device_name,
                                    ];

                                    // Get organisation details
                                    $organisation = $this->db->table('public.user_login')
                                        ->select('organisation')
                                        ->where('user_id', $data[0]->user_id)
                                        ->where('active', 1)
                                        ->get()
                                        ->getRow();

                                    $report_data[$length][0]['organisation'] = $organisation->organisation;

                                    // Modify device name (example manipulation)
                                    $PWI = explode("(", $data[0]->device_name);
                                    $newPwI = '';
                                    for ($a = 1; $a < count($PWI); $a++) {
                                        $newPwI = $newPwI . $PWI[$a];
                                    }
                                    $newPwI = '(' . $newPwI;
                                    $report_data[$length][0]['newPwI'] = $newPwI;
                                }
                            }
                        }
                    }
                }
            }

            // Initialize the 'data' array to hold the final data
            $data = [];

            // Returning report data
            $data['report_data'] = $report_data;
            $data['map_device_id'] = isset($report_data[0][0]['deviceid']) ? $report_data[0][0]['deviceid'] : null;

            if(!empty($report_data)) {
                if($report_data[0][0]['user_type'] == 'Patrolman')
                {
                    $startdetails = $report_data[0][0]['start_time'];
                    $startdetailsarr = explode(' ',$startdetails);				
                    $data['map_start_date'] = $report_data[0][0]['result_date'];
                    if(count($startdetailsarr) > 1) {
                        $starttimedetailsarr = explode(":",$startdetailsarr[1]);
                    } else {
                        $starttimedetailsarr = explode(":",$startdetails);
                    }
                    $start_time = $starttimedetailsarr[0].":".$starttimedetailsarr[1];
                    $data['map_start_time'] = $report_data[0][0]['start_time'];
                    $enddetails = $report_data[0][0]['end_time'];
                    $enddetailsarr = explode(' ',$enddetails);				
                    $data['map_end_date'] = $report_data[0][0]['result_date'];
                    if(count($enddetailsarr) > 1) {
                        $enddetailsarrarr = explode(":",$enddetailsarr[1]);
                    } else {
                        $enddetailsarrarr = explode(":",$enddetails);
                    }
                    $end_time = $enddetailsarrarr[0].":".$enddetailsarrarr[1];
                    $data['map_end_time'] = $report_data[0][0]['end_time'];
                }
                else
                {
                    $startdetails = $report_data[0][0]['start_time'];		
                    $data['map_start_date'] = $report_data[0][0]['result_date'];
                    $data['map_start_time'] = $report_data[0][0]['start_time'];
                    
                    $enddetails = $report_data[0][0]['end_time'];
                    $enddetailsarr = explode(' ',$enddetails);				
                    $data['map_end_date'] = $report_data[0][0]['result_date'];
                    if(count($enddetailsarr) > 1) {
                        $enddetailsarrarr = explode(":",$enddetailsarr[1]);
                    } else {
                        $enddetailsarrarr = explode(":",$enddetails);
                    }
                    $end_time = $enddetailsarrarr[0].":".$enddetailsarrarr[1];
                    $data['map_end_time'] = $report_data[0][0]['end_time'];
                }
            }
            
            
        }

        // Prepare the device dropdown query based on the user group
        if ($this->sessdata['group_id'] == 3) { // distributor
            $deviceQuery = "SELECT a.*, 
                            (SELECT device_name FROM {$this->schema}.master_device_setup  
                             WHERE id = (SELECT MAX(id) FROM {$this->schema}.master_device_setup 
                                         WHERE inserttime::date <= current_date::date 
                                         AND deviceid = a.did)) as device_name 
                            FROM public.get_divice_details_record_for_list_for_company('{$this->schema}',{$this->sessdata['user_id']}) as a 
                            WHERE a.group_id = 2 AND a.active = 1";
        } else { // others
            $deviceQuery = "SELECT a.*, 
                            (SELECT device_name FROM {$this->schema}.master_device_setup  
                             WHERE id = (SELECT MAX(id) FROM {$this->schema}.master_device_setup 
                                         WHERE inserttime::date <= current_date::date 
                                         AND deviceid = a.did)) as device_name 
                            FROM public.get_divice_details_record_for_list_for_company('{$this->schema}',{$this->sessdata['user_id']}) as a 
                            WHERE a.group_id = 2 AND a.active = 1";
        }

        // Execute device query and assign to $data['devicedropdown']
        $data['devicedropdown'] = $this->db->query($deviceQuery)->getResult();

        // Get user list for users dropdown
        $data['usersdd'] = $this->db->table('user_login')->where('active', 1)->where('group_id', 8)->get()->getResult();

        // Get form data from POST or request
        $data['dt'] = date("d-m-Y", strtotime(trim($this->request->getPost('stdt'))));
        $data['pwi_id'] = trim($this->request->getPost('user'));
        $data['pwi_name'] = trim($this->request->getPost('pwi_name'));
        $data['device_id'] = $device_id = trim($this->request->getPost('device_id'));
        $data['typeofuser'] = $typeofuser = trim($this->request->getPost('typeofuser'));
        $data['pway'] = $this->db->query("SELECT organisation, user_id FROM public.user_login WHERE active = 1 AND group_id = 8")->getResult();

        // Handle default date if none provided
        if ($this->request->getPost('stdt') == '') {
            $data['dt'] = date("d-m-Y");
        }

        // Get start and end time
        $data['strt'] = $starttime = $this->request->getVar('strt');
        $data['endtime'] = $endtime = $this->request->getVar('endtime');

        // Get pway ID if present
        $data['sse_pwy'] = $sse_pwy = trim($this->request->getPost('pway_id'));

        $data['page_title'] = "Activity Time Segment Report";

        // Load the view
        $data['middle'] = view('traxreport/activitytimesegmentreport', $data);
        return view('mainlayout', $data);
    }

    public function activitytimesegmentreportexcel()
    {
        // Get session data
        $user_id = $this->sessdata['user_id'];
        $group_id = $this->sessdata['group_id'];

        // Get post data
        $dt = date("d-m-Y H:i:s", strtotime(trim($this->request->getPost('stdt'))));
        $pwi_id = trim($this->request->getPost('section_id'));
        $pwi_name = trim($this->request->getPost('pwi_name'));
        $device_id = trim($this->request->getPost('device_id'));
        $sse_pwy = trim($this->request->getPost('pway_id'));
        $typeofuser = trim($this->request->getPost('typeofuser'));

        // Get start time and end time from request
        $starttime = $this->request->getVar('strt') . ':00'; // Use getVar() for global variables
        $endtime = $this->request->getVar('endtime') . ':00';

        // Format date and time
        $date_from = date("Y-m-d", strtotime(trim($this->request->getPost('stdt')))) . " " . $starttime;
        $date_to = date("Y-m-d", strtotime(trim($this->request->getPost('stdt')))) . " " . $endtime;

        if ($device_id == '') {
            $devices = '';
            $dids = '';
            if ($pwi_name == 'All') {
                if ($sse_pwy == 'All') {
                    $query = "SELECT sup_pid, sup_gid, serial_no, imei_no, did, mobile_no, warranty_date, linked, parent_id, user_id, issudate,
                        refunddate, active, issold, apply_scheam, group_id, role_id, email, address, pincode, state_name, country, username, firstname,
                        lastname, organisation, group_name, '' as list_item, '' as list_item_name
                        FROM public.get_divice_details_record_for_list('{$this->schema}', {$user_id})
                        WHERE sup_gid = {$group_id} AND user_id = {$user_id} AND active = 1
                        ORDER BY did ASC";
                    $devicelist = $this->db->query($query)->getResult();
                } else {
                    $sectionlist = $this->db->query("SELECT user_id FROM public.user_login WHERE parent_id = '{$sse_pwy}' AND active = 1")->getResult();
                    $devicelist = [];
                    foreach ($sectionlist as $section) {
                        $sectionid = $section->user_id;
                        $query = "SELECT sup_pid, sup_gid, serial_no, imei_no, did, mobile_no, warranty_date, linked, parent_id, user_id, issudate,
                            refunddate, active, issold, apply_scheam, group_id, role_id, email, address, pincode, state_name, country, username, firstname,
                            lastname, organisation, group_name, '' as list_item, '' as list_item_name
                            FROM public.get_divice_details_record_for_list('{$this->schema}', {$sectionid})
                            WHERE user_id = {$sectionid} AND active = 1
                            ORDER BY issudate ASC";
                        $lists = $this->db->query($query)->getResult();
                        foreach ($lists as $list) {
                            $devicelist[] = $list;
                        }
                    }
                }
            } else {
                $query = "SELECT sup_pid, sup_gid, serial_no, imei_no, did, mobile_no, warranty_date, linked, parent_id, user_id, issudate,
                    refunddate, active, issold, apply_scheam, group_id, role_id, email, address, pincode, state_name, country, username, firstname,
                    lastname, organisation, group_name, '' as list_item, '' as list_item_name
                    FROM public.get_divice_details_record_for_list('{$this->schema}', {$pwi_id})
                    WHERE user_id = {$pwi_id} AND active = 1
                    ORDER BY issudate ASC";
                $devicelist = $this->db->query($query)->getResult();
            }

            if (count($devicelist) > 0) {
                $devices_arr = [];
                foreach ($devicelist as $devicelist_each) {
                    if ($devices == "") {
                        $devices .= $devicelist_each->did;
                        $dids .= $devicelist_each->did;
                    } else {
                        $devices .= "," . $devicelist_each->did;
                        $dids .= "," . $devicelist_each->did;
                    }
                }
                $devices_arr = explode(',', $devices);
            }
        } else {
            $devices_arr[] = $device_id;
        }
        // Assume $devices_arr and $typeofuser are already set (from previous logic)
        $new_devices_arr = [];
        if ($typeofuser != 'All') {
            // Loop through devices array to filter based on user type
            foreach ($devices_arr as $device_id) {
                if ($device_id != '') {
                    // Query device name details
                    $device_name_details = $this->db->query("SELECT device_name FROM {$this->schema}.master_device_setup WHERE deviceid = ?", [$device_id])->getRow();
                    
                    if ($device_name_details) {
                        $device_name = $device_name_details->device_name;
                        $device_name_arr = explode('/', $device_name);
                        $user_type = $device_name_arr[0];
                        
                        // If the user type matches the specified one, add to new devices array
                        if (strtoupper($user_type) == strtoupper($typeofuser)) {
                            $new_devices_arr[] = $device_id;  // Add device to new devices array
                        }
                    }
                }
            }
        } else {
            // If 'All', just add all devices
            $new_devices_arr = $devices_arr;  // Copy all devices from devices_arr
        }
        $report_data = [];
        // Loop through the new_devices_arr (array of device IDs)
        foreach ($new_devices_arr as $device_id) {
            if ($device_id != '') {
                // Get device name details using query builder
                $device_name_details = $this->db->table($this->schema.'.master_device_setup')
                    ->select('device_name')
                    ->where('deviceid', $device_id)
                    ->get()
                    ->getRow();

                if ($device_name_details) {
                    $device_name = $device_name_details->device_name;
                    $device_name_arr = explode('/', $device_name);
                    $user_type = $device_name_arr[0];

                    // Check if the device is assigned
                    $assignment_details = $this->db->table('public.master_device_assign')
                        ->select('count(*) as counter')
                        ->where('deviceid', $device_id)
                        ->where('group_id', 2)
                        ->where('active', 1)
                        ->get()
                        ->getRow();

                    $counter = $assignment_details->counter;

                    if ($counter > 0) {
                        if ($user_type == 'Patrolman') {
                            // Get data for Patrolman
                            $data = $this->db->query(
                                "SELECT a.*, mdd.serial_no, msd.device_name
                                FROM public.get_histry_play_data_summary(?, ?, ?) AS a
                                LEFT JOIN {$this->schema}.master_device_details AS mdd ON mdd.superdevid = a.deviceid
                                LEFT JOIN {$this->schema}.master_device_setup AS msd ON msd.deviceid = a.deviceid",
                                [$device_id, $date_from, $date_to]
                            )->getResult();

                            if (count($data) > 0) {
                                $serialno = $data[0]->serial_no;
                                // Get assignment details
                                $device_assign_details = $this->db->query("select a.parent_user_id,a.current_user_id,b.organisation as pwy,c.organisation as section
                                from public.device_asign_details as a
                                left join public.user_login as b on (a.parent_user_id = b.user_id)
                                left join public.user_login as c on (a.current_user_id = c.user_id)
                                where a.serial_no='".$serialno."'")->getRow();

                                // Add to report data
                                $length = count($report_data);
                                $report_data[$length][0] = [
                                    'pwy' => $device_assign_details->pwy,
                                    'section' => $device_assign_details->section,
                                    'result_date' => $data[0]->result_date,
                                    'deviceid' => $data[0]->deviceid,
                                    'user_type' => $user_type,
                                    'parent_id' => $data[0]->parent_id,
                                    'user_id' => $data[0]->user_id,
                                    'group_id' => $data[0]->group_id,
                                    'start_time' => $data[0]->start_time,
                                    'end_time' => $data[0]->end_time,
                                ];

                                $newduration = $this->db->query(
                                    "SELECT age(?::timestamp, ?::timestamp) AS duration",
                                    [$data[0]->result_date . ' ' . $data[0]->end_time, $data[0]->result_date . ' ' . $data[0]->start_time]
                                )->getRow();

                                $report_data[$length][0]['duration'] = $newduration->duration;
                                $report_data[$length][0]['distance_cover'] = $data[0]->distance_cover;  // Assuming $data1 is available elsewhere
                                $report_data[$length][0]['sos_no'] = 0;
                                $report_data[$length][0]['alert_no'] = 0;
                                $report_data[$length][0]['call_no'] = 0;
                                $report_data[$length][0]['serial_no'] = $data[0]->serial_no;
                                $report_data[$length][0]['device_name'] = $data[0]->device_name;

                                // Get organisation details
                                $organisation = $this->db->table('public.user_login')
                                    ->select('organisation')
                                    ->where('user_id', $data[0]->user_id)
                                    ->where('active', 1)
                                    ->get()
                                    ->getRow();

                                $report_data[$length][0]['organisation'] = $organisation->organisation;

                                // Modify device name (example manipulation)
                                $PWI = explode("(", $data[0]->device_name);
                                $newPwI = '';
                                for ($a = 1; $a < count($PWI); $a++) {
                                    $newPwI = $newPwI . $PWI[$a];
                                }
                                $newPwI = '(' . $newPwI;
                                $report_data[$length][0]['newPwI'] = $newPwI;
                            }
                        } else {
                            // Similar logic for other user types (non-patrolman)
                            $data = $this->db->query(
                                "SELECT a.*, mdd.serial_no, msd.device_name
                                FROM public.get_histry_play_data_summary(?, ?, ?) AS a
                                LEFT JOIN {$this->schema}.master_device_details AS mdd ON mdd.superdevid = a.deviceid
                                LEFT JOIN {$this->schema}.master_device_setup AS msd ON msd.deviceid = a.deviceid",
                                [$device_id, $date_from, $date_to]
                            )->getResult();

                            if (count($data) > 0) {
                                $serialno = $data[0]->serial_no;
                                // Get assignment details
                                $device_assign_details = $this->db->query("select a.parent_user_id,a.current_user_id,b.organisation as pwy,c.organisation as section
                                from public.device_asign_details as a
                                left join public.user_login as b on (a.parent_user_id = b.user_id)
                                left join public.user_login as c on (a.current_user_id = c.user_id)
                                where a.serial_no='".$serialno."'")->getRow();

                                // Add to report data
                                $length = count($report_data);
                                $report_data[$length][0] = [
                                    'pwy' => $device_assign_details->pwy,
                                    'section' => $device_assign_details->section,
                                    'result_date' => $data[0]->result_date,
                                    'deviceid' => $data[0]->deviceid,
                                    'user_type' => $user_type,
                                    'parent_id' => $data[0]->parent_id,
                                    'user_id' => $data[0]->user_id,
                                    'group_id' => $data[0]->group_id,
                                    'start_time' => $data[0]->start_time,
                                    'end_time' => $data[0]->end_time,
                                    'duration' => $data[0]->duration,
                                    'distance_cover' => $data[0]->distance_cover,
                                    'sos_no' => 0,
                                    'alert_no' => 0,
                                    'call_no' => 0,
                                    'serial_no' => $data[0]->serial_no,
                                    'device_name' => $data[0]->device_name,
                                ];

                                // Get organisation details
                                $organisation = $this->db->table('public.user_login')
                                    ->select('organisation')
                                    ->where('user_id', $data[0]->user_id)
                                    ->where('active', 1)
                                    ->get()
                                    ->getRow();

                                $report_data[$length][0]['organisation'] = $organisation->organisation;

                                // Modify device name (example manipulation)
                                $PWI = explode("(", $data[0]->device_name);
                                $newPwI = '';
                                for ($a = 1; $a < count($PWI); $a++) {
                                    $newPwI = $newPwI . $PWI[$a];
                                }
                                $newPwI = '(' . $newPwI;
                                $report_data[$length][0]['newPwI'] = $newPwI;
                            }
                        }
                    }
                }
            }
        }

        // Prepare the data array for Excel
        $dat[0]['A'] = "Date";
        $dat[0]['B'] = "Device ID";
        $dat[0]['C'] = "DeviceName";
        $dat[0]['D'] = "BIT";
        $dat[0]['E'] = "SSE/PWY";
        $dat[0]['F'] = "Section";
        $dat[0]['G'] = "User Type";
        $dat[0]['H'] = "Start Time";
        $dat[0]['I'] = "End Time";
        $dat[0]['J'] = "Travelled Distance(KM)";
        $dat[0]['K'] = "Total Call";
        $dat[0]['L'] = "Total SOS";

        $Key = 1;
        foreach ($report_data as $report_data_each) {
            $dat[$Key+1]['A'] = date("d-m-Y", strtotime($report_data_each[0]['result_date']));
            $dat[$Key+1]['B'] = $report_data_each[0]['serial_no'];
            $dat[$Key+1]['C'] = $report_data_each[0]['device_name'];
            $dat[$Key+1]['D'] = $report_data_each[0]['newPwI'];
            $dat[$Key+1]['E'] = $report_data_each[0]['pwy'];
            $dat[$Key+1]['F'] = $report_data_each[0]['organisation'];
            $dat[$Key+1]['G'] = $report_data_each[0]['user_type'];
            $dat[$Key+1]['H'] = $report_data_each[0]['start_time'];
            $dat[$Key+1]['I'] = $report_data_each[0]['end_time'];
            $dat[$Key+1]['J'] = round($report_data_each[0]['distance_cover'] / 1000) . ' km';
            $dat[$Key+1]['K'] = $report_data_each[0]['call_no'];
            $dat[$Key+1]['L'] = $report_data_each[0]['sos_no'];
            $Key++;
        }

        // Excel file name
        $filename = 'Activity_Time_Segment_Report_' . $pwi_name . '_' . time() . '.xlsx';

        // Call to download the Excel file
        exceldownload($dat, $filename);

        
    }

    public function devicedetails()
    {
        $schemaname = $this->schema; // CI4 session handling
        $userid = $this->sessdata['user_id'];
        $data['page_title'] = 'Device details';
        $data['sessdata'] = $this->sessdata;
        
        // Get URI segments using the CI4 way
        $data['date'] = $date = $this->request->getUri()->getSegment(4);
        $data['type'] = $type = $this->request->getUri()->getSegment(5);
        $data['section_id'] = $section_id = $this->request->getUri()->getSegment(6);
        $data['typeofuser'] = $typeofuser = $this->request->getUri()->getSegment(7);
        // echo $date.' '.$type.' '.$section_id;exit();
        
        // Query to fetch data from the database using CI4 query builder
        $query = "select '$date' as date,lefttable.user_id, ul.organisation as section,lefttable.deviceid,
        pl.organisation as pwy,lefttable.serial_no from public.get_right_panel_data('{$schemaname}','{$date}',{$userid}) as lefttable 
        left join public.user_login as ul  on lefttable.user_id = ul.user_id 
        left join public.user_login as pl on lefttable.parent_id = pl.user_id 
        where lefttable.group_id = 2 and lefttable.deviceid IS NOT NULL and lefttable.user_id=$section_id";
        $result = $this->db->query($query)->getResult();
        
        // Process the result set
        foreach ($result as $i => $row) {
            $deviceid = $row->deviceid;
            
            // Fetch device name from the master_device_setup table
            $mdddevicename = $this->db->table($this->schema.'.master_device_setup')
                                ->select('device_name')
                                ->where('deviceid', $deviceid)
                                ->get()
                                ->getRow();
            
            $result[$i]->mdddevicename = $mdddevicename->device_name;
            $mdddevicename_arr = explode("/", $result[$i]->mdddevicename);
            
            // Determine the user type
            if (strpos(strtolower($mdddevicename_arr[0]), 'stock') !== false) {
                $usertype = 'Others';
            } else if (strpos(strtolower($mdddevicename_arr[0]), 'keyman') !== false) {
                $usertype = 'Keyman';
            } else if (strpos(strtolower($mdddevicename_arr[0]), 'patrolman') !== false) {
                $usertype = 'Patrolman';
            } else {
                $usertype = '';
            }
            $result[$i]->usertype = $usertype;
        }

        $newresult = [];

        // Check for inactive devices based on type
        if ($type == 'inactivedevicesection') {
            foreach ($result as $i => $row) {
                $deviceid = $row->deviceid;
                $devicename = $row->mdddevicename;

                // Query for the historical data
                $record1 = $this->db->query("select a.*, mdd.serial_no from public.get_histry_play_data_summary($deviceid, '$date 00:00:00'::timestamp without time zone, '$date 23:59:59'::timestamp without time zone) as a
                                       left join {$schemaname}.master_device_details as mdd on (mdd.superdevid = a.deviceid)")->getResult();
                if (count($record1) == 0) {
                    if ($typeofuser == 'All') {
                        array_push($newresult, $result[$i]);
                    } else {
                        $position = stripos($devicename, $typeofuser);
                        if ($position !== false) {
                            array_push($newresult, $result[$i]);
                        }
                    }
                }
            }
            $data['type'] = 'Inactive';
        }

        if ($type == 'beatcovereddevicesection') {
            foreach ($result as $row) {
                $deviceid = $row->deviceid;
                $devicename = $row->mdddevicename;
                $mdddevicename_arr = explode("/", $devicename);

                // echo $deviceid;

                $d1 = $date.' 00:00:00';
                $d2 = $date.' 23:59:59';
                
                // Check if the device name doesn't contain 'stock'
                if (strpos(strtolower($mdddevicename_arr[0]), 'stock') == false) {
                    // echo "<pre>---if---";
                    // Query to get historical play data and associated details
                    $record1 = $this->db->query("SELECT a.*, mdd.serial_no, b.walk_org_distance 
                                    FROM public.get_histry_play_data_summary($deviceid, '$d1', '$d2') AS a 
                                    LEFT JOIN {$schemaname}.master_device_details AS mdd ON mdd.superdevid = a.deviceid
                                    LEFT JOIN {$schemaname}.device_assigne_pole_data AS b ON b.deviceid = a.deviceid 
                                        AND b.startpole <> '0' AND b.stoppol <> '0' 
                                    WHERE b.walk_org_distance IS NOT NULL")->getResult();

                    // echo "<pre>";print_r($record1);//exit();
    
                    $distance_cover = isset($record1[0]) ? $record1[0]->distance_cover : 0;
                    $distance_cover = number_format($distance_cover / 1000, 3);
    
                    if (count($record1) > 0) {
                        $actual_distance = isset($record1[0]) ? $record1[0]->walk_org_distance : 0;
    
                        if ($typeofuser == 'All') {
                            if ($distance_cover >= $actual_distance) {
                                $newresult[] = $row;
                            }
                        } else {
                            $mdddevicename_arr = explode("/", $devicename);
                            $devtypeuser = strtoupper($mdddevicename_arr[0]);
    
                            if ($devtypeuser == strtoupper($typeofuser)) {
                                if ($distance_cover >= $actual_distance) {
                                    $newresult[] = $row;
                                }
                            }
                        }
                    }
                }
            }
    
            // Set the response type message
            $data['type'] = 'Beat Covered Successfully';
        }

        if ($type == 'totaldevicedevicesection') {
            // Iterate through the result
            foreach ($result as $row) {
                $devicename = $row->mdddevicename;
                
                // If the user type is 'All', add all results to $newresult
                if ($typeofuser == 'All') {
                    $newresult[] = $row;
                } else {
                    // Split the device name to check the user type
                    $mdddevicename_arr = explode("/", $devicename);
                    $devtypeuser = strtoupper($mdddevicename_arr[0]);
                    
                    // If the device user type matches the provided type, add it to the result
                    if ($devtypeuser == strtoupper($typeofuser)) {
                        $newresult[] = $row;
                    }
                }
            }
            
            // Set the response type message
            $data['type'] = 'Total';
        }

        if ($type == 'notalloteddevicesection') {
            // Loop through the result using foreach
            foreach ($result as $row) {
                $devicename = $row->mdddevicename;
    
                // Check if 'stock' is in the device name (case insensitive) or if the device name is empty
                if (strpos(strtolower($devicename), 'stock') !== false) {
                    $newresult[] = $row;  // Append to the result
                }

                if(empty($devicename)) {
                    $newresult[] = $row;  // Append to the result
                }
            }
    
            // Set the response type message
            $data['type'] = 'Not Alloted';
        }

        if ($type == 'beatnotcovereddevicesection') {
            foreach ($result as $row) {
                $deviceid = $row->deviceid;
                $devicename = $row->mdddevicename;
                $mdddevicename_arr = explode("/", $devicename);

                // echo $deviceid;

                $d1 = $date.' 00:00:00';
                $d2 = $date.' 23:59:59';
                
                // Check if the device name doesn't contain 'stock'
                if (strpos(strtolower($mdddevicename_arr[0]), 'stock') == false) {
                    // echo "<pre>---if---";
                    // Query to get historical play data and associated details
                    $record1 = $this->db->query("SELECT a.*, mdd.serial_no, b.walk_org_distance 
                                    FROM public.get_histry_play_data_summary($deviceid, '$d1', '$d2') AS a 
                                    LEFT JOIN {$schemaname}.master_device_details AS mdd ON mdd.superdevid = a.deviceid
                                    LEFT JOIN {$schemaname}.device_assigne_pole_data AS b ON b.deviceid = a.deviceid 
                                        AND b.startpole <> '0' AND b.stoppol <> '0' 
                                    WHERE b.walk_org_distance IS NOT NULL")->getResult();

                    // echo "<pre>";print_r($record1);//exit();
    
                    $distance_cover = isset($record1[0]) ? $record1[0]->distance_cover : 0;
                    $distance_cover = number_format($distance_cover / 1000, 3);
    
                    if (count($record1) > 0) {
                        $actual_distance = isset($record1[0]) ? $record1[0]->walk_org_distance : 0;
    
                        if ($typeofuser == 'All') {
                            if ($distance_cover < $actual_distance) {
                                $newresult[] = $row;
                            }
                        } else {
                            $mdddevicename_arr = explode("/", $devicename);
                            $devtypeuser = strtoupper($mdddevicename_arr[0]);
    
                            if ($devtypeuser == strtoupper($typeofuser)) {
                                if ($distance_cover < $actual_distance) {
                                    $newresult[] = $row;
                                }
                            }
                        }
                    }
                }
            }
    
            // Setting the response type
            $data['type'] = 'Beat Not Covered';
        }

        // Assign the results to the data array
        $data['newresult'] = $newresult;

       // Load the view
       $data['middle'] = view('traxreport/devicedetails', $data);
       return view('mainlayout', $data);
    }

    public function totalDeviceDetails()
    {
        $schemaname = $this->schema;
        $userid = $this->sessdata['user_id'];
        $data['date'] = $date = $this->request->getUri()->getSegment(4);
        $data['type'] = $type = $this->request->getUri()->getSegment(5);
        $data['section_id'] = $section_id = $this->request->getUri()->getSegment(6);
        $data['typeofuser'] = $typeofuser = $this->request->getUri()->getSegment(7);
        $data['sessdata'] = $this->sessdata;
        $data['page_title'] = 'Total Device details';

        $builder = $this->db->query("select '$date' as date,lefttable.user_id, ul.organisation as section,
        lefttable.deviceid,pl.organisation as pwy,lefttable.serial_no from  
        public.get_right_panel_data('{$schemaname}','{$date}',{$userid}) as lefttable 
        left join public.user_login as ul  on lefttable.user_id = ul.user_id 
        left join public.user_login as pl on lefttable.parent_id = pl.user_id 
        where lefttable.group_id = 2 and lefttable.deviceid IS NOT NULL 
        order by ul.organisation asc");

        $result = $builder->getResult();

        foreach ($result as $i => $row) {
            $deviceid = $row->deviceid;
            $mdddevicename = $this->db->query("SELECT device_name FROM {$this->schema}.master_device_setup WHERE deviceid = ?", [$deviceid])->getResult();
            $result[$i]->mdddevicename = $mdddevicename[0]->device_name;
            $mdddevicename_arr = explode("/", $result[$i]->mdddevicename);
            if (strpos(strtolower($mdddevicename_arr[0]), 'stock') !== false) {
                $usertype = 'Others';
            } else if (strpos(strtolower($mdddevicename_arr[0]), 'keyman') !== false) {
                $usertype = 'Keyman';
            } else if (strpos(strtolower($mdddevicename_arr[0]), 'patrolman') !== false) {
                $usertype = 'Patrolman';
            }
            $result[$i]->usertype = $usertype;
        }

        $newresult = [];
        if ($type == 'inactivedevicesection') {
            foreach ($result as $i => $row) {
                $deviceid = $row->deviceid;
                $devicename = $row->mdddevicename;
                $data_count = $this->db->query("SELECT count(*) as counter FROM {$this->schema}.traker_positionaldata WHERE currentdate = ? AND deviceid = ?", [$date, $deviceid])->getResult();
                $count = $data_count[0]->counter;
                $record1 = $this->db->query("SELECT a.*, mdd.serial_no, b.expected_distance as walk_org_distance 
                    FROM public.get_histry_play_data_summary(?, ?::timestamp, ?::timestamp) as a 
                    LEFT JOIN {$schemaname}.master_device_details as mdd ON mdd.superdevid = a.deviceid 
                    LEFT JOIN {$schemaname}.trip_details_with_pole as b ON b.deviceid = a.deviceid 
                    WHERE b.result_date = ?", [$deviceid, "{$date} 00:00:00", "{$date} 23:59:59", $date])->getResult();

                if (count($record1) == 0 && $count == 0) {
                    if ($devicename != '') {
                        $mdddevicename_arr = explode("/", $devicename);
                        if ($typeofuser == 'All') {
                            array_push($newresult, $result[$i]);
                        } else {
                            $position = stripos($devicename, $typeofuser);
                            if ($position !== false) {
                                array_push($newresult, $result[$i]);
                            }
                        }
                    }
                }
            }
            $data['type'] = 'Inactive';
        }

        if ($type == 'beatcovereddevicesection') {
            // Loop through the results
            foreach ($result as $i => $row) {
                $deviceid = $row->deviceid;
                $devicename = $row->mdddevicename;
                $mdddevicename_arr = explode("/", $devicename);

                // Check if device is not stock
                if (strpos(strtolower($mdddevicename_arr[0]), 'stock') === false) {
                    // Query to get device data
                    $record1 = $this->db->query("
                        SELECT a.*, mdd.serial_no, b.expected_distance as walk_org_distance 
                        FROM public.get_histry_play_data_summary(?, ?::timestamp, ?::timestamp) as a 
                        LEFT JOIN {$schemaname}.master_device_details as mdd ON mdd.superdevid = a.deviceid 
                        LEFT JOIN {$schemaname}.trip_details_with_pole as b ON b.deviceid = a.deviceid 
                        WHERE b.result_date = ?", 
                        [$deviceid, "{$date} 00:00:00", "{$date} 23:59:59", $date]
                    )->getResult();

                    // Get distance covered and format it
                    if (count($record1) > 0) {
                        $distance_cover = $record1[0]->distance_cover;
                        $distance_cover = number_format($distance_cover / 1000, 3);
                        $actual_distance = $record1[0]->walk_org_distance;

                        // Check if distance covered is greater than or equal to actual distance
                        if ($typeofuser == 'All') {
                            if ($distance_cover >= $actual_distance) {
                                $newresult[] = $result[$i];
                            }
                        } else {
                            // Check for device type user match
                            $position = stripos($devicename, $typeofuser);
                            if ($position !== false) {
                                if ($distance_cover >= $actual_distance) {
                                    $newresult[] = $result[$i];
                                }
                            }
                        }
                    }
                }
            }

            // Set the data for type
            $data['type'] = 'Beat Covered Successfully';
        }

        if ($type == 'totaldevicedevicesection') {
            // Loop through the results
            foreach ($result as $i => $row) {
                $devicename = $row->mdddevicename;

                if ($typeofuser == 'All') {
                    // If the type of user is 'All', add the result directly
                    $newresult[] = $result[$i];
                } else {
                    // Check if the 'typeofuser' matches the device name using case-insensitive search
                    $position = stripos($devicename, $typeofuser);
                    if ($position !== false) {
                        $newresult[] = $result[$i];
                    }
                }
            }

            // Set the data for type
            $data['type'] = 'Total';
        }

        if ($type == 'notalloteddevicesection') {
            // Loop through the results
            foreach ($result as $i => $row) {
                $deviceid = $row->deviceid;
                $devicename = $row->mdddevicename;

                // Get historical play data for the device
                $query = $this->db->query(
                    "SELECT a.*, mdd.serial_no, b.expected_distance AS walk_org_distance 
                    FROM public.get_histry_play_data_summary(?, ?, ?) AS a 
                    LEFT JOIN {$schemaname}.master_device_details AS mdd ON (mdd.superdevid = a.deviceid) 
                    LEFT JOIN {$schemaname}.trip_details_with_pole AS b ON (b.deviceid = a.deviceid) 
                    WHERE b.result_date = ?",
                    [$deviceid, "{$date} 00:00:00", "{$date} 23:59:59", $date]
                );
                $record1 = $query->getResult();

                // Check if the device name contains 'stock' or is empty
                if (strpos(strtolower($devicename), 'stock') !== false || $devicename == '') {
                    // Add to the result if conditions are met
                    $newresult[] = $result[$i];
                }

                // Set the type to 'Not Alloted'
                $data['type'] = 'Not Alloted';
            }
        }

        if ($type == 'beatnotcovereddevicesection') {
            $inactive_device = $active_device = $beat_not_covered = $beat_covered = 0;
            // Loop through the results (assumed to be pre-fetched in the controller)
            foreach ($result as $i => $row) {
                $deviceid = $row->deviceid;
                $devicename = $row->mdddevicename;
                $mdddevicename_arr = explode("/", $devicename);

                // Skip if device name contains 'stock'
                if (strpos(strtolower($mdddevicename_arr[0]), 'stock') == false) {
                    // Get historical play data for the device
                    $query = $this->db->query(
                        "SELECT a.*, mdd.serial_no, b.expected_distance AS walk_org_distance 
                        FROM public.get_histry_play_data_summary(?, ?, ?) AS a 
                        LEFT JOIN {$schemaname}.master_device_details AS mdd ON (mdd.superdevid = a.deviceid) 
                        LEFT JOIN {$schemaname}.trip_details_with_pole AS b ON (b.deviceid = a.deviceid) 
                        WHERE b.result_date = ?",
                        [$deviceid, "{$date} 00:00:00", "{$date} 23:59:59", $date]
                    );
                    $record1 = $query->getResult();

                    if (count($record1) > 0) {
                        $distance_cover = $record1[0]->distance_cover;
                        $distance_cover = number_format($distance_cover / 1000, 3);
                        $actual_distance = $record1[0]->walk_org_distance;

                        // Check conditions based on 'All' or specific user type
                        if ($typeofuser == 'All') {
                            if ($distance_cover >= $actual_distance) {
                                $beat_covered++;
                            } else {
                                $newresult[] = $result[$i];
                            }
                        } else {
                            $devtypeuser = strtoupper($mdddevicename_arr[0]);
                            if ($devtypeuser == strtoupper($typeofuser)) {
                                if ($distance_cover >= $actual_distance) {
                                    $beat_covered++;
                                } else {
                                    $newresult[] = $result[$i];
                                }
                            }
                        }
                    } else {
                        // Check if there is positional data for the device
                        $data_count = $this->db->query(
                            "SELECT COUNT(*) AS counter 
                            FROM {$schemaname}.traker_positionaldata 
                            WHERE currentdate = ? AND deviceid = ?",
                            [$date, $deviceid]
                        );
                        $count = $data_count->getRow()->counter;

                        if ($count > 0) {
                            if ($typeofuser == 'All') {
                                $active_device++;
                                $beat_not_covered++;
                                $newresult[] = $result[$i];
                            } else {
                                $devtypeuser = strtoupper($mdddevicename_arr[0]);
                                if ($devtypeuser == strtoupper($typeofuser)) {
                                    $active_device++;
                                    $beat_not_covered++;
                                    $newresult[] = $result[$i];
                                }
                            }
                        } else {
                            if ($typeofuser == 'All') {
                                $inactive_device++;
                            } else {
                                $devtypeuser = strtoupper($mdddevicename_arr[0]);
                                if ($devtypeuser == strtoupper($typeofuser)) {
                                    $inactive_device++;
                                }
                            }
                        }
                    }
                }
            }

            // Set the data for the type
            $data['type'] = 'Beat Not Covered';
        }

        // Similar refactoring is required for 'beatcovereddevicesection', 'totaldevicedevicesection', etc.
        // For simplicity, I'm omitting the other sections, but follow the same logic for them.

        $data['newresult'] = $newresult;
        $data['middle'] = view('traxreport/devicedetails', $data); // View rendering in CI4
        return view('mainlayout', $data); // Passing data to layout
    }

    public function tripDetailsReport_old()
    { 
        // Check if user is logged in
        if (!session()->has('login_sess_data')) {
            return redirect()->to('/');
        }

        $data = [];
        $data['sessdata'] = $this->sessdata;
        $data['page_title'] = "Trip Details Report";
        $data['date_from'] = '';
        $data['usertype'] = 'All';

        if ($this->request->getMethod() == 'POST') {
            // echo "<pre>";print_r($this->request->getPost());exit();
            $start_date = date("Y-m-d H:i:s", strtotime(trim($this->request->getPost('stdt'))));
            $end_date = date("Y-m-d H:i:s", strtotime(trim($this->request->getPost('endt'))));
            $data['stdt'] = date("d-m-Y H:i", strtotime(trim($this->request->getPost('stdt'))));
            $data['endt'] = date("d-m-Y H:i", strtotime(trim($this->request->getPost('endt'))));
            $data['usertype'] = trim($this->request->getPost('usertype')) ;
            $data['schema'] = $schemaname = $this->schema;
            $user_id = $this->sessdata['user_id'];

            $sql = "SELECT deviceid, {$this->schema}.tbl_trip.imeino, 

         {$this->schema}.tbl_trip.stpole as actual_stpole, 
         {$this->schema}.tbl_trip.endpole as actual_endpole, 
         usertype,
         distance_travelled,
         sttimestamp as actual_starttime, 
         endtimestamp as actual_endtime, 
         totaldistancetravel, timetravelled, startbattery, endbattery, beats_covered ,
         devicename,
         {$this->schema}.tbl_device_schedule.stpole as expected_stpole, 
         {$this->schema}.tbl_device_schedule.endpole as expected_endpole, 
         
         {$this->schema}.tbl_device_schedule.sttime as expected_starttime, 
         {$this->schema}.tbl_device_schedule.endtime  as expected_endtime
                FROM {$this->schema}.tbl_trip
                LEFT JOIN {$this->schema}.tbl_device_schedule ON {$this->schema}.tbl_device_schedule.imeino = {$this->schema}.tbl_trip.imeino
                WHERE ((sttimestamp BETWEEN ? AND ?)
                OR  (endtimestamp   BETWEEN ? AND ?) OR (sttimestamp < ? AND endtimestamp > ?) ) ";

            $parameters = [$start_date, $end_date,$start_date, $end_date,$start_date, $end_date];

            /*

            ({$this->schema}.tbl_trip.startdate::timestamp + {$this->schema}.tbl_trip.starttime::interval) <= ? 
                AND ({$this->schema}.tbl_trip.enddate::timestamp + {$this->schema}.tbl_trip.endtime::interval)   >= ? 

            WHERE 
                ((startdate || ' ' || starttime) BETWEEN ? AND ?)
                AND 
                (({$this->schema}.tbl_trip.enddate || ' ' || {$this->schema}.tbl_trip.endtime) BETWEEN ? AND ?)";
            */

            // Apply usertype filter only if it is NOT "All"
            if (!empty($data['usertype']) && $data['usertype'] !== "All") {
                $sql .= " AND usertype = ?";
                $parameters[] = $data['usertype'];
            }

            $sql .= " ORDER BY {$this->schema}.tbl_trip.imeino, sttimestamp ASC";

            $result = $this->db->query($sql, $parameters)->getResult();


            $data['alldata'] = $result;
        }

        $data['middle'] = view('traxreport/tripdetailsreport', $data);
        return view('mainlayout', $data);
    }

    public function tripDetailsReportExcel_old()
    {
  
        // Check if user is logged in
        if (!session()->has('login_sess_data')) {
            return redirect()->to('/');
        }

        $data = [];
        $data['sessdata'] = $this->sessdata;
        $data['page_title'] = "Trip Details Report";
        $data['date_from'] = '';

        if ($this->request->getMethod() == 'POST') {
            // echo "<pre>";print_r($this->request->getPost());exit();
            $start_date = date("Y-m-d H:i:s", strtotime(trim($this->request->getPost('stdt'))));
            $end_date = date("Y-m-d H:i:s", strtotime(trim($this->request->getPost('endt'))));
            $data['stdt'] = date("d-m-Y H:i", strtotime(trim($this->request->getPost('stdt'))));
            $data['endt'] = date("d-m-Y H:i", strtotime(trim($this->request->getPost('endt'))));
            $data['usertype'] = trim($this->request->getPost('usertype')) ;
            $data['schema'] = $schemaname = $this->schema;
            $user_id = $this->sessdata['user_id'];

            $sql = "SELECT deviceid, {$this->schema}.tbl_trip.imeino, 

         {$this->schema}.tbl_trip.stpole as actual_stpole, 
         {$this->schema}.tbl_trip.endpole as actual_endpole, 
         usertype,
         distance_travelled,
		 devicename,

         sttimestamp as actual_starttime, 
         endtimestamp as actual_endtime, 
         totaldistancetravel, timetravelled, startbattery, endbattery, beats_covered ,
         
         {$this->schema}.tbl_device_schedule.stpole as expected_stpole, 
         {$this->schema}.tbl_device_schedule.endpole as expected_endpole, 
         
         {$this->schema}.tbl_device_schedule.sttime as expected_starttime, 
         {$this->schema}.tbl_device_schedule.endtime  as expected_endtime
                FROM {$this->schema}.tbl_trip
                LEFT JOIN {$this->schema}.tbl_device_schedule ON {$this->schema}.tbl_device_schedule.imeino = {$this->schema}.tbl_trip.imeino
                WHERE ((sttimestamp BETWEEN ? AND ?)
                OR  (endtimestamp   BETWEEN ? AND ?) OR (sttimestamp < ? AND endtimestamp > ?) ) ";

            $parameters = [$start_date, $end_date,$start_date, $end_date,$start_date, $end_date];
                /*(startdate || ' ' || starttime >= ? 
                AND {$this->schema}.tbl_trip.enddate || ' ' || {$this->schema}.tbl_trip.endtime <= ?)*/


            // Apply usertype filter only if it is NOT "All"
            if (!empty($data['usertype']) && $data['usertype'] !== "All") {
                $sql .= " AND device_type = ?";
                $parameters[] = $data['usertype'];
            }

            $sql .= " ORDER BY {$this->schema}.tbl_trip.imeino, sttimestamp ASC";

            $result = $this->db->query($sql, $parameters)->getResult();


            $data['alldata'] = $result;
        }

        $dat[0]['A'] = " Trip Details Report From Date " . date("d-m-Y H:i:s", strtotime($start_date)) . " To " . date("d-m-Y H:i:s", strtotime($end_date));

        // Initialize header row
        $dat[1]['A'] = "Device ID";
        $dat[1]['B'] = "User Type";
        $dat[1]['C'] = "Expected Start Pole";
        $dat[1]['D'] = "Actual Start Pole";
        $dat[1]['E'] = "Expected Start Time";
        $dat[1]['F'] = "Actual Start Time";
        $dat[1]['G'] = "Start Battery Percentage";
        $dat[1]['H'] = "Expected End Pole";
        $dat[1]['I'] = "Actual End Pole";
        $dat[1]['J'] = "Expected End Time";
        $dat[1]['K'] = "Actual End Time";
        $dat[1]['L'] = "End Battery Percentage";
        $dat[1]['M'] = "Expected Distance Travel";
        $dat[1]['N'] = "Actual Distance Travel";
        $dat[1]['O'] = "Time Travelled";
        $dat[1]['P'] = "Beats Covered";
        
        // Initialize counters
        $Key = 1;

        foreach ($result as $irow) {
            // Convert the string into an array
            $items = array_map('trim', explode(",", $irow->beats_covered));
                
            // Filter unique values
            $unique_items = array_unique($items);
            
            // Output result as a string
            $beats_covered = implode(", ", $unique_items);

            // Fill data for each row
            $dat[$Key + 1]['A'] = $irow->imeino;
            $dat[$Key + 1]['B'] = $irow->usertype;
            $dat[$Key + 1]['C'] = $irow->expected_stpole;
            $dat[$Key + 1]['D'] = $irow->actual_stpole;
            $dat[$Key + 1]['E'] = date("Y-m-d", strtotime($irow->actual_starttime)) . ' ' . $irow->expected_starttime;
            $dat[$Key + 1]['F'] = date("Y-m-d H:i:s", strtotime($irow->actual_starttime));
            $dat[$Key + 1]['G'] = $irow->startbattery;
            $dat[$Key + 1]['H'] = $irow->expected_endpole;
            $dat[$Key + 1]['I'] = $irow->actual_endpole;
            $dat[$Key + 1]['J'] = date("Y-m-d H:i:s", strtotime($irow->expected_endtime));
            $dat[$Key + 1]['K'] = date("Y-m-d H:i:s", strtotime($irow->actual_endtime));
            $dat[$Key + 1]['L'] = $irow->endbattery;
            $dat[$Key + 1]['M'] = number_format($irow->distance_travelled,4);
            $dat[$Key + 1]['N'] = number_format($irow->totaldistancetravel,4);
            $dat[$Key + 1]['O'] = date("H:i:s", strtotime($irow->timetravelled));
            $dat[$Key + 1]['P'] = $beats_covered;

            $Key++;
        }

        // Create the Excel file
        $filename = 'Trip_Details_Report_' . date("d-m-Y H:i", strtotime($start_date)) . '_To_' . date("d-m-Y H:i", strtotime($end_date)) . '.xlsx';
        exceldownload($dat, $filename);

    }

    public function tripDetailsReport()
    { 
        // Check if user is logged in
        if (!session()->has('login_sess_data')) {
            return redirect()->to('/');
        }

        $data = [];
        $data['sessdata'] = $this->sessdata;
        $data['page_title'] = "Trip Details Report";
        $data['date_from'] = '';
        $data['usertype'] = 'All';

        $user_id = $this->sessdata['user_id'];
        $subUsers = $this->getSubUsers($user_id, $this->db);

        // Include logged-in user
        $allowedUsers = array_merge([$user_id], $subUsers);
        $placeholders = implode(',', array_fill(0, count($allowedUsers), '?'));

        // print_r($allowedUsers); die();

        if ($this->request->getMethod() == 'POST') {
            // echo "<pre>";print_r($this->request->getPost());exit();
            $start_date = date("Y-m-d H:i:s", strtotime(trim($this->request->getPost('stdt'))));
            $end_date = date("Y-m-d H:i:s", strtotime(trim($this->request->getPost('endt'))));
            $data['stdt'] = date("d-m-Y H:i", strtotime(trim($this->request->getPost('stdt'))));
            $data['endt'] = date("d-m-Y H:i", strtotime(trim($this->request->getPost('endt'))));
            $data['usertype'] = trim($this->request->getPost('usertype')) ;
            $data['pwi_id'] = trim($this->request->getPost('pway_id')) ;
            $data['section_id'] = trim($this->request->getPost('user')) ;
            $data['schema'] = $schemaname = $this->schema;
            $user_id = $this->sessdata['user_id'];

            $sql = "SELECT 
                {$this->schema}.tbl_trip.deviceid, 
                {$this->schema}.tbl_trip.imeino, 
                ul.organisation,                 
                p_ul.organisation AS parent_organisation,  
                {$this->schema}.tbl_trip.stpole as actual_stpole, 
                {$this->schema}.tbl_trip.endpole as actual_endpole, 
                ts.device_type as usertype,
                trip_schedule_details.expected_distance as distance_travelled,
                sttimestamp as actual_starttime, 
                endtimestamp as actual_endtime, 
                totaldistancetravel, timetravelled, startbattery, endbattery, beats_covered,
                devicename,
                {$this->schema}.trip_schedule_details.expected_stpole as expected_stpole, 
                {$this->schema}.trip_schedule_details.expected_endpole as expected_endpole, 
         
                {$this->schema}.trip_schedule_details.expected_start_datetime as expected_starttime, 
                {$this->schema}.trip_schedule_details.expected_end_datetime  as expected_endtime
                FROM {$this->schema}.tbl_trip
                LEFT JOIN {$this->schema}.trip_schedule_details 
                     ON {$this->schema}.trip_schedule_details.schedule_details_id = {$this->schema}.tbl_trip.schedule_details_id
                LEFT JOIN {$this->schema}.trip_schedule  ts  
                     ON ts.schedule_id = {$this->schema}.trip_schedule_details.schedule_id  
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

                       // Apply usertype filter only if it is NOT "All"
            if (!empty($data['usertype']) && $data['usertype'] !== "All") {
                $sql .= " AND device_type = ?";
                $parameters[] = $data['usertype'];
            }

            if (!empty($data['pwi_id']) && $data['pwi_id'] !== "All") {
                $sql .= " AND p_ul.user_id = ? ";
                $parameters[] = $data['pwi_id'];
            }

            if (!empty($data['section_id']) && $data['section_id'] !== "All") {
                $sql .= " AND ul.user_id = ? ";
                $parameters[] = $data['section_id'];
            }

            $sql .= " ORDER BY {$this->schema}.tbl_trip.imeino, sttimestamp ASC";

            $result = $this->db->query($sql, $parameters)->getResult();
           // echo $this->db->getLastQuery(); die('----');


            $data['alldata'] = $result;
        }

        if ($this->sessdata['group_id'] == 3) {
            // Distributor logic
            $data['devicedropdown'] = $this->db->query("SELECT a.*, 
                (SELECT device_name FROM {$this->schema}.master_device_setup  
                 WHERE id = (SELECT max(id) 
                             FROM {$this->schema}.master_device_setup 
                             WHERE inserttime::date <= current_date::date  
                             AND deviceid = a.did)) 
                AS device_name 
                FROM public.get_divice_details_record_for_list_for_company('{$this->schema}',{$this->sessdata['user_id']}) AS a 
                WHERE a.group_id = 2 AND a.active = 1")->getResult();
        } else {
            
            // $data['devicedropdown'] = $this->db->query("SELECT a.*, 
            //     (SELECT device_name FROM {$this->schema}.master_device_setup  
            //      WHERE id = (SELECT max(id) 
            //                  FROM {$this->schema}.master_device_setup 
            //                  WHERE inserttime::date <= current_date::date  
            //                  AND deviceid = a.did)) 
            //     AS device_name 
            //     FROM public.get_divice_details_record_for_list_for_company('{$this->schema}',{$this->sessdata['user_id']}) AS a 
            //     WHERE a.group_id = 2 AND a.active = 1")->getResult();

            $data['devicedropdown'] = $this->db->query("SELECT a.*, 
                (SELECT device_name FROM {$this->schema}.master_device_setup  
                WHERE id = (SELECT max(id) 
                            FROM {$this->schema}.master_device_setup 
                            WHERE inserttime::date <= current_date::date  
                            AND deviceid = a.did)) 
                AS device_name 
                FROM public.get_divice_details_record_for_list_for_company('{$this->schema}',{$this->sessdata['user_id']}) AS a 
                WHERE a.group_id = 2 AND a.active = 1 AND a.user_id IN ($placeholders)", $allowedUsers)->getResult();
        }

        // Get users
        $data['usersdd'] = $this->commonModel->get_users();

        $data['pwi_id'] = trim($this->request->getPost('user'));

        // $data['pway'] = $this->db->query("SELECT organisation, user_id 
        //                                   FROM public.user_login 
        //                                   WHERE active = 1 AND group_id = 8")->getResult();

        $data['pway'] = $this->db->query("SELECT organisation, user_id 
            FROM public.user_login 
            WHERE active = 1 AND group_id = 8 AND user_id IN ($placeholders)", $allowedUsers)->getResult();

        $data['sse_pwy'] = trim($this->request->getPost('pway_id'));


        $data['middle'] = view('traxreport/tripdetailsreport', $data);
        return view('mainlayout', $data);
    }

    public function tripDetailsReportExcel(){
  
        // Check if user is logged in
        if (!session()->has('login_sess_data')) {
            return redirect()->to('/');
        }

        $data = [];
        $data['sessdata'] = $this->sessdata;
        $data['page_title'] = "Trip Details Report";
        $data['date_from'] = '';

        $user_id = $this->sessdata['user_id'];
        $subUsers = $this->getSubUsers($user_id, $this->db);

        // Include logged-in user
        $allowedUsers = array_merge([$user_id], $subUsers);
        $placeholders = implode(',', array_fill(0, count($allowedUsers), '?'));

        if ($this->request->getMethod() == 'POST') {
            // echo "<pre>";print_r($this->request->getPost());exit();
            $start_date = date("Y-m-d H:i:s", strtotime(trim($this->request->getPost('stdt'))));
            $end_date = date("Y-m-d H:i:s", strtotime(trim($this->request->getPost('endt'))));
            $data['stdt'] = date("d-m-Y H:i", strtotime(trim($this->request->getPost('stdt'))));
            $data['endt'] = date("d-m-Y H:i", strtotime(trim($this->request->getPost('endt'))));
            $data['pwi_id'] = trim($this->request->getPost('pway_id')) ;
            $data['section_id'] = trim($this->request->getPost('user')) ;
            $data['usertype'] = trim($this->request->getPost('usertype')) ;
            $data['schema'] = $schemaname = $this->schema;
            $user_id = $this->sessdata['user_id'];

            $sql = "SELECT 
                {$this->schema}.tbl_trip.deviceid, 
                {$this->schema}.tbl_trip.imeino, 
                ul.organisation,                 -- ✅ Added organisation
                p_ul.organisation AS parent_organisation,  -- ✅ Added parent_organisation
                {$this->schema}.tbl_trip.stpole as actual_stpole, 
                {$this->schema}.tbl_trip.endpole as actual_endpole, 
                ts.device_type as usertype,
                trip_schedule_details.expected_distance as distance_travelled,
                sttimestamp as actual_starttime, 
                endtimestamp as actual_endtime, 
                totaldistancetravel, timetravelled, startbattery, endbattery, beats_covered ,
                devicename,
                {$this->schema}.trip_schedule_details.expected_stpole as expected_stpole, 
                {$this->schema}.trip_schedule_details.expected_endpole as expected_endpole,  
         
                {$this->schema}.trip_schedule_details.expected_start_datetime as expected_starttime, 
                {$this->schema}.trip_schedule_details.expected_end_datetime  as expected_endtime
                FROM {$this->schema}.tbl_trip
                LEFT JOIN {$this->schema}.trip_schedule_details 
                 ON {$this->schema}.trip_schedule_details.schedule_details_id = {$this->schema}.tbl_trip.schedule_details_id
                LEFT JOIN {$this->schema}.trip_schedule  ts  
                 ON ts.schedule_id = {$this->schema}.trip_schedule_details.schedule_id
                LEFT JOIN public.master_device_assign mda 
                    ON mda.deviceid = {$this->schema}.tbl_trip.deviceid  -- ✅ Join with `master_device_assign` to get user_id
                LEFT JOIN public.user_login ul 
                    ON ul.user_id = mda.user_id   -- ✅ Get `organisation`
                LEFT JOIN public.user_login p_ul 
                    ON p_ul.user_id = mda.parent_id  -- ✅ Get `parent_organisation`
                WHERE 
                (
                        (sttimestamp BETWEEN ? AND ?)
                    OR  (endtimestamp   BETWEEN ? AND ?) 
                    OR (sttimestamp < ? AND endtimestamp > ?) 
                ) 
                AND ul.group_id = 2
                AND mda.active = 1";

                $parameters = [$start_date, $end_date,$start_date, $end_date,$start_date, $end_date];

                $sql .= " AND mda.user_id IN ($placeholders)";
                $parameters = array_merge($parameters, $allowedUsers);

                       // Apply usertype filter only if it is NOT "All"
            if (!empty($data['usertype']) && $data['usertype'] !== "All") {
                $sql .= " AND device_type = ?";
                $parameters[] = $data['usertype'];
            }

            if (!empty($data['pwi_id']) && $data['pwi_id'] !== "All") {
                $sql .= " AND p_ul.user_id = ? ";
                $parameters[] = $data['pwi_id'];
            }

            if (!empty($data['section_id']) && $data['section_id'] !== "All") {
                $sql .= " AND ul.user_id = ? ";
                $parameters[] = $data['section_id'];
            }

            $sql .= " ORDER BY {$this->schema}.tbl_trip.imeino, sttimestamp ASC";

            $result = $this->db->query($sql, $parameters)->getResult();


            $data['alldata'] = $result;
        }

        $dat[0]['A'] = " Trip Details Report From Date " . date("d-m-Y H:i:s", strtotime($start_date)) . " To " . date("d-m-Y H:i:s", strtotime($end_date));

        // Initialize header row
        $dat[1]['A'] = "Device ID";
        $dat[1]['B'] = "User Type";
        $dat[1]['C'] = "PWI";
        $dat[1]['D'] = "Section";
        $dat[1]['E'] = "Expected Start Pole";
        $dat[1]['F'] = "Actual Start Pole";
        $dat[1]['G'] = "Expected Start Time";
        $dat[1]['H'] = "Actual Start Time";
        $dat[1]['I'] = "Start Battery Percentage";
        $dat[1]['J'] = "Expected End Pole";
        $dat[1]['K'] = "Actual End Pole";
        $dat[1]['L'] = "Expected End Time";
        $dat[1]['M'] = "Actual End Time";
        $dat[1]['N'] = "End Battery Percentage";
        $dat[1]['O'] = "Expected Distance Travel";
        $dat[1]['P'] = "Actual Distance Travel";
        $dat[1]['Q'] = "Time Travelled";
        // $dat[1]['P'] = "Beats Covered";
        
        // Initialize counters
        $Key = 1;

        foreach ($result as $irow) {
            // Convert the string into an array
            $items = array_map('trim', explode(",", $irow->beats_covered));
                
            // Filter unique values
            $unique_items = array_unique($items);
            
            // Output result as a string
            $beats_covered = implode(", ", $unique_items);

            // Fill data for each row
            $dat[$Key + 1]['A'] = $irow->imeino;
            $dat[$Key + 1]['B'] = $irow->usertype;
            $dat[$Key + 1]['C'] = $irow->parent_organisation;
            $dat[$Key + 1]['D'] = $irow->organisation;
            $dat[$Key + 1]['E'] = $irow->expected_stpole;
            $dat[$Key + 1]['F'] = $irow->actual_stpole;
            $dat[$Key + 1]['G'] = date("Y-m-d", strtotime($irow->actual_starttime)) . ' ' . $irow->expected_starttime;
            $dat[$Key + 1]['H'] = date("Y-m-d H:i:s", strtotime($irow->actual_starttime));
            $dat[$Key + 1]['I'] = $irow->startbattery;
            $dat[$Key + 1]['J'] = $irow->expected_endpole;
            $dat[$Key + 1]['K'] = $irow->actual_endpole;
            $dat[$Key + 1]['L'] = date("Y-m-d H:i:s", strtotime($irow->expected_endtime));
            $dat[$Key + 1]['M'] = date("Y-m-d H:i:s", strtotime($irow->actual_endtime));
            $dat[$Key + 1]['N'] = $irow->endbattery;
            $dat[$Key + 1]['O'] = number_format($irow->distance_travelled,4);
            $dat[$Key + 1]['P'] = number_format($irow->totaldistancetravel,4);
            $dat[$Key + 1]['Q'] = date("H:i:s", strtotime($irow->timetravelled));
            // $dat[$Key + 1]['P'] = $beats_covered;

            $Key++;
        }

        // Create the Excel file
        $filename = 'Trip_Details_Report_' . date("d-m-Y H:i", strtotime($start_date)) . '_To_' . date("d-m-Y H:i", strtotime($end_date)) . '.xlsx';
        exceldownload($dat, $filename);

    }

    public function tripDetailsSummaryReport()
    { 

        // Check if user is logged in
        if (!session()->has('login_sess_data')) {
            return redirect()->to('/');
        }

        $data = [];
        $data['sessdata'] = $this->sessdata;
        $data['page_title'] = "Summary Of Exception Report";
        $data['date_from'] = '';
        $data['usertype'] = 'All';
        $data['usersdd'] = $this->commonModel->get_users();
        $data['pway'] = $this->db->query("SELECT organisation, user_id FROM public.user_login WHERE active = 1 AND group_id = 8")->getResult();

        if ($this->request->getMethod() == 'POST') {
            // echo "<pre>";print_r($this->request->getPost());exit();
            $start_date = date("Y-m-d H:i:s", strtotime(trim($this->request->getPost('stdt'))));
            $end_date = date("Y-m-d H:i:s", strtotime(trim($this->request->getPost('endt'))));
            $data['stdt'] = date("d-m-Y H:i", strtotime(trim($this->request->getPost('stdt'))));
            $data['endt'] = date("d-m-Y H:i", strtotime(trim($this->request->getPost('endt'))));
            $data['stdt1'] = date("d-m-Y_H:i", strtotime(trim($this->request->getPost('stdt'))));
            $data['endt1'] = date("d-m-Y_H:i", strtotime(trim($this->request->getPost('endt'))));
            $data['usertype'] = trim($this->request->getPost('usertype')) ;
            $data['schema'] = $schemaname = $this->schema;
            $data['sse_pwy'] = $sse_pwy = trim($this->request->getPost('pway_id'));
            $data['pwi_id'] = $pwi_id = trim($this->request->getPost('user'));
            $user_id = $this->sessdata['user_id'];

            $sql = "WITH base_data AS (
                SELECT 
                    ul.organisation,
                    ul.user_id,
                    p_ul.organisation AS parent_organisation,
                    md.serial_no AS device_imei,
                    msd.device_name AS device_name,
                    CASE 
                        WHEN lower(msd.device_name) LIKE '%stock%' 
                             OR msd.device_name IS NULL 
                             OR trim(msd.device_name) = '' 
                        THEN 'Not Allocated'
                        ELSE 'Allocated'
                    END AS allocation_status,
                    t.imeino AS trip_imeino,
                    tsd.expected_distance AS distance_travelled,
                    t.totaldistancetravel,
                    CASE 
                        WHEN (
                                TRIM(tsd.expected_stpole) = TRIM(t.stpole)
                                OR TRIM(tsd.expected_stpole) = ANY (
                                    ARRAY (
                                        SELECT trim(elem)
                                        FROM unnest(string_to_array(t.beats_covered, ',')) AS elem
                                    )
                                )
                             )
                             AND (
                                TRIM(tsd.expected_endpole) = TRIM(t.endpole)
                                OR TRIM(tsd.expected_endpole) = ANY (
                                    ARRAY (
                                        SELECT trim(elem)
                                        FROM unnest(string_to_array(t.beats_covered, ',')) AS elem
                                    )
                                )
                             )
                        THEN 'Patroling Completed'
                        ELSE NULL
                    END AS trip_status
                FROM public.master_device_assign mda
                LEFT JOIN public.user_login ul 
                    ON ul.user_id = mda.user_id
                LEFT JOIN public.master_device_details md 
                    ON md.id = mda.deviceid
                LEFT JOIN {$this->schema}.master_device_setup msd 
                    ON msd.deviceid = mda.deviceid
                LEFT JOIN public.user_login p_ul 
                    ON p_ul.user_id = mda.parent_id
                LEFT JOIN (
                    SELECT DISTINCT ON (stes.tbl_trip.deviceid)
                    {$this->schema}.tbl_trip.*
                    FROM {$this->schema}.tbl_trip 
                    LEFT JOIN {$this->schema}.trip_schedule_details tsd 
                     ON tsd.schedule_details_id = {$this->schema}.tbl_trip.schedule_details_id 
                    LEFT JOIN {$this->schema}.trip_schedule ts 
                        ON tsd.schedule_id = ts.schedule_id
                    WHERE ( (sttimestamp BETWEEN ? AND ?)
                            OR (endtimestamp   BETWEEN ? AND ?)
                            OR (sttimestamp < ? AND endtimestamp > ?))
                    ";
                    $parameters = [$start_date, $end_date,$start_date, $end_date,$start_date, $end_date];
                    
                    if (!empty($data['usertype']) && $data['usertype'] != "All") {
                        $sql .= " AND ts.device_type = ?";
                        $parameters[] = $data['usertype'];
                    }

                    $sql .= ") t 
                    ON t.deviceid = mda.deviceid
                    LEFT JOIN {$this->schema}.trip_schedule_details tsd 
                     ON tsd.schedule_details_id = t.schedule_details_id 
                    LEFT JOIN {$this->schema}.trip_schedule ts
                     ON ts.schedule_id = tsd.schedule_id";
                    
                    if (!empty($data['usertype']) && $data['usertype'] != "All") {
                        $sql .= " AND ts.device_type = ?";
                        $parameters[] = $data['usertype'];
                    }

                    $sql .= " WHERE 
                        ul.group_id = 2
                        AND mda.active = 1 ";
                        
                    if (!empty($data['usertype']) && $data['usertype'] != "All") {
                        $sql .= " AND ts.device_type = ?";
                        $parameters[] = $data['usertype'];
                    }

                    if (!empty($data['sse_pwy']) && $data['sse_pwy'] != "All") {
                        $sql .= " AND p_ul.user_id = ?";
                        $parameters[] = $data['sse_pwy'];
                    }

                    if (!empty($data['pwi_id']) && $data['pwi_id'] != "All") {
                        $sql .= " AND ul.user_id = ?";
                        $parameters[] = $data['pwi_id'];
                    }

                    $sql .= "),
                    device_status AS (
                        SELECT 
                            organisation,
                            user_id,
                            parent_organisation,
                            allocation_status,
                            device_imei,
                            device_name,
                            MAX(CASE WHEN trip_imeino IS NOT NULL THEN 1 ELSE 0 END) AS has_trip,
                            MAX(CASE WHEN trip_status = 'Patroling Completed' THEN 1 ELSE 0 END) AS covered,
                            COALESCE(distance_travelled, 0) AS expected_distance  ,
                            COALESCE(totaldistancetravel, 0) AS actual_distance
                        FROM base_data
                        GROUP BY organisation, user_id, parent_organisation, allocation_status, device_imei, device_name, distance_travelled, totaldistancetravel
                    )
                    SELECT 
                        organisation,
                        user_id,
                        parent_organisation,
                        COUNT(CASE WHEN allocation_status = 'Not Allocated' THEN 1 END) AS not_allocated_count,
                        string_agg(
                            CASE WHEN allocation_status = 'Not Allocated' THEN device_imei END, ',' 
                            ORDER BY device_imei
                        ) AS not_allocated_imeino,
                        string_agg(
                            CASE WHEN allocation_status = 'Not Allocated' THEN device_name END, ',' 
                            ORDER BY device_imei
                        ) AS not_allocated_devicename,
                        COUNT(CASE 
                            WHEN allocation_status = 'Allocated' 
                            AND has_trip = 0 
                            THEN 1 
                        END) AS device_off_count,
                        string_agg(
                            CASE 
                                WHEN allocation_status = 'Allocated' 
                                AND has_trip = 0 
                                THEN device_imei 
                            END, ',' 
                            ORDER BY device_imei
                        ) AS device_off_imeino,
                        string_agg(
                            CASE 
                                WHEN allocation_status = 'Allocated' 
                                AND has_trip = 0 
                                THEN device_name 
                            END, ',' 
                            ORDER BY device_imei
                        ) AS device_off_devicename,
                        COUNT(CASE 
                            WHEN allocation_status = 'Allocated' 
                            AND has_trip = 1 
                            AND (covered = 1 OR (covered = 0 AND actual_distance >= expected_distance)) 
                            THEN 1 
                        END) AS beats_covered_count,
                        COUNT(CASE 
                            WHEN allocation_status = 'Allocated' 
                            AND has_trip = 1 
                            AND covered = 0 
                            AND actual_distance < expected_distance 
                            THEN 1 
                        END) AS beats_not_covered_count
                    FROM device_status
                    GROUP BY organisation, user_id, parent_organisation; ";

            $result = $this->db->query($sql, $parameters)->getResult();

            // echo $this->db->getLastQuery();



            $data['alldata'] = $result;
        }

        $data['middle'] = view('traxreport/tripdetailssnapshot', $data);
        return view('mainlayout', $data);
    }

    public function tripDetailsSummaryReportExcel()
    { 

        // Check if user is logged in
        if (!session()->has('login_sess_data')) {
            return redirect()->to('/');
        }

        $data = [];
        $data['sessdata'] = $this->sessdata;
        $data['page_title'] = "Trip Details Report";
        $data['date_from'] = '';
        $data['usertype'] = 'All';

        if ($this->request->getMethod() == 'POST') {
            // echo "<pre>";print_r($this->request->getPost());exit();
            $start_date = date("Y-m-d H:i:s", strtotime(trim($this->request->getPost('stdt'))));
            $end_date = date("Y-m-d H:i:s", strtotime(trim($this->request->getPost('endt'))));
            $data['stdt'] = date("d-m-Y H:i", strtotime(trim($this->request->getPost('stdt'))));
            $data['endt'] = date("d-m-Y H:i", strtotime(trim($this->request->getPost('endt'))));
            $data['stdt1'] = date("d-m-Y_H:i", strtotime(trim($this->request->getPost('stdt'))));
            $data['endt1'] = date("d-m-Y_H:i", strtotime(trim($this->request->getPost('endt'))));
            $data['usertype'] = trim($this->request->getPost('usertype')) ;
            $data['schema'] = $schemaname = $this->schema;
            $data['sse_pwy'] = $sse_pwy = trim($this->request->getPost('pway_id'));
            $data['pwi_id'] = $pwi_id = trim($this->request->getPost('user'));
            $user_id = $this->sessdata['user_id'];

            /* ----- Old Table Structure ----- */
            // $sql = "WITH base_data AS (
            //     SELECT 
            //         ul.organisation,
            //         ul.user_id,
            //         p_ul.organisation AS parent_organisation,
            //         md.serial_no AS device_imei,
            //         msd.device_name AS device_name,
            //         CASE 
            //             WHEN lower(msd.device_name) LIKE '%stock%' 
            //                  OR msd.device_name IS NULL 
            //                  OR trim(msd.device_name) = '' 
            //             THEN 'Not Allocated'
            //             ELSE 'Allocated'
            //         END AS allocation_status,
            //         t.imeino AS trip_imeino,
            //         su.distance_travelled,
            //         t.totaldistancetravel,
            //         CASE 
            //             WHEN (
            //                     TRIM(su.stpole) = TRIM(t.stpole)
            //                     OR TRIM(su.stpole) = ANY (
            //                         ARRAY (
            //                             SELECT trim(elem)
            //                             FROM unnest(string_to_array(t.beats_covered, ',')) AS elem
            //                         )
            //                     )
            //                  )
            //                  AND (
            //                     TRIM(su.endpole) = TRIM(t.endpole)
            //                     OR TRIM(su.endpole) = ANY (
            //                         ARRAY (
            //                             SELECT trim(elem)
            //                             FROM unnest(string_to_array(t.beats_covered, ',')) AS elem
            //                         )
            //                     )
            //                  )
            //             THEN 'Patroling Completed'
            //             ELSE NULL
            //         END AS trip_status
            //     FROM public.master_device_assign mda
            //     LEFT JOIN public.user_login ul 
            //         ON ul.user_id = mda.user_id
            //     LEFT JOIN public.master_device_details md 
            //         ON md.id = mda.deviceid
            //     LEFT JOIN {$this->schema}.master_device_setup msd 
            //         ON msd.deviceid = mda.deviceid
            //     LEFT JOIN public.user_login p_ul 
            //         ON p_ul.user_id = mda.parent_id
            //     LEFT JOIN (
            //         SELECT {$this->schema}.tbl_trip.* 
            //         FROM {$this->schema}.tbl_trip 
            //         LEFT JOIN {$this->schema}.tbl_device_schedule_updated su 
            //             ON su.imeino = tbl_trip.imeino 
            //         WHERE ( (sttimestamp BETWEEN ? AND ?)
            //                 OR (endtimestamp   BETWEEN ? AND ?)
            //                 OR (sttimestamp < ? AND endtimestamp > ?))
            //         ";
            //         $parameters = [$start_date, $end_date,$start_date, $end_date,$start_date, $end_date];
                    
            //         if (!empty($data['usertype']) && $data['usertype'] != "All") {
            //             $sql .= " AND su.usertype = ?";
            //             $parameters[] = $data['usertype'];
            //         }

            //         $sql .= ") t 
            //         ON t.deviceid = mda.deviceid
            //         LEFT JOIN {$this->schema}.tbl_device_schedule_updated su 
            //         ON su.imeino = md.serial_no ";

                    
            //         if (!empty($data['usertype']) && $data['usertype'] != "All") {
            //             $sql .= " AND su.usertype = ?";
            //             $parameters[] = $data['usertype'];
            //         }

            //         $sql .= " WHERE 
            //             ul.group_id = 2
            //             AND mda.active = 1 ";
                        
            //         if (!empty($data['usertype']) && $data['usertype'] != "All") {
            //             $sql .= " AND su.usertype = ?";
            //             $parameters[] = $data['usertype'];
            //         }

            //         if (!empty($data['sse_pwy']) && $data['sse_pwy'] != "All") {
            //             $sql .= " AND p_ul.user_id = ?";
            //             $parameters[] = $data['sse_pwy'];
            //         }

            //         if (!empty($data['pwi_id']) && $data['pwi_id'] != "All") {
            //             $sql .= " AND ul.user_id = ?";
            //             $parameters[] = $data['pwi_id'];
            //         }

            //         $sql .= "),
            //         device_status AS (
            //             SELECT 
            //                 organisation,
            //                 user_id,
            //                 parent_organisation,
            //                 allocation_status,
            //                 device_imei,
            //                 device_name,
            //                 MAX(CASE WHEN trip_imeino IS NOT NULL THEN 1 ELSE 0 END) AS has_trip,
            //                 MAX(CASE WHEN trip_status = 'Patroling Completed' THEN 1 ELSE 0 END) AS covered,
            //                 MAX(COALESCE(distance_travelled, 0)) AS actual_distance,
            //                 SUM(COALESCE(totaldistancetravel, 0)) AS expected_distance
            //             FROM base_data
            //             GROUP BY organisation, user_id, parent_organisation, allocation_status, device_imei, device_name
            //         )
            //         SELECT 
            //             organisation,
            //             user_id,
            //             parent_organisation,
            //             COUNT(CASE WHEN allocation_status = 'Not Allocated' THEN 1 END) AS not_allocated_count,
            //             string_agg(
            //                 CASE WHEN allocation_status = 'Not Allocated' THEN device_imei END, ',' 
            //                 ORDER BY device_imei
            //             ) AS not_allocated_imeino,
            //             string_agg(
            //                 CASE WHEN allocation_status = 'Not Allocated' THEN device_name END, ',' 
            //                 ORDER BY device_imei
            //             ) AS not_allocated_devicename,
            //             COUNT(CASE 
            //                 WHEN allocation_status = 'Allocated' 
            //                 AND has_trip = 0 
            //                 THEN 1 
            //             END) AS device_off_count,
            //             string_agg(
            //                 CASE 
            //                     WHEN allocation_status = 'Allocated' 
            //                     AND has_trip = 0 
            //                     THEN device_imei 
            //                 END, ',' 
            //                 ORDER BY device_imei
            //             ) AS device_off_imeino,
            //             string_agg(
            //                 CASE 
            //                     WHEN allocation_status = 'Allocated' 
            //                     AND has_trip = 0 
            //                     THEN device_name 
            //                 END, ',' 
            //                 ORDER BY device_imei
            //             ) AS device_off_devicename,
            //             COUNT(CASE 
            //                 WHEN allocation_status = 'Allocated' 
            //                 AND has_trip = 1 
            //                 AND (covered = 1 OR (covered = 0 AND actual_distance >= expected_distance)) 
            //                 THEN 1 
            //             END) AS beats_covered_count,
            //             COUNT(CASE 
            //                 WHEN allocation_status = 'Allocated' 
            //                 AND has_trip = 1 
            //                 AND covered = 0 
            //                 AND actual_distance < expected_distance 
            //                 THEN 1 
            //             END) AS beats_not_covered_count
            //         FROM device_status
            //         GROUP BY organisation, user_id, parent_organisation; ";

            // $result = $this->db->query($sql, $parameters)->getResult();

            /* ---- New Table Structure Updated ---- */

            $sql = "WITH base_data AS (
                SELECT 
                    ul.organisation,
                    ul.user_id,
                    p_ul.organisation AS parent_organisation,
                    md.serial_no AS device_imei,
                    msd.device_name AS device_name,
                    CASE 
                        WHEN lower(msd.device_name) LIKE '%stock%' 
                             OR msd.device_name IS NULL 
                             OR trim(msd.device_name) = '' 
                        THEN 'Not Allocated'
                        ELSE 'Allocated'
                    END AS allocation_status,
                    t.imeino AS trip_imeino,
                    tsd.expected_distance AS distance_travelled,
                    t.totaldistancetravel,
                    CASE 
                        WHEN (
                                TRIM(tsd.expected_stpole) = TRIM(t.stpole)
                                OR TRIM(tsd.expected_stpole) = ANY (
                                    ARRAY (
                                        SELECT trim(elem)
                                        FROM unnest(string_to_array(t.beats_covered, ',')) AS elem
                                    )
                                )
                             )
                             AND (
                                TRIM(tsd.expected_endpole) = TRIM(t.endpole)
                                OR TRIM(tsd.expected_endpole) = ANY (
                                    ARRAY (
                                        SELECT trim(elem)
                                        FROM unnest(string_to_array(t.beats_covered, ',')) AS elem
                                    )
                                )
                             )
                        THEN 'Patroling Completed'
                        ELSE NULL
                    END AS trip_status
                FROM public.master_device_assign mda
                LEFT JOIN public.user_login ul 
                    ON ul.user_id = mda.user_id
                LEFT JOIN public.master_device_details md 
                    ON md.id = mda.deviceid
                LEFT JOIN {$this->schema}.master_device_setup msd 
                    ON msd.deviceid = mda.deviceid
                LEFT JOIN public.user_login p_ul 
                    ON p_ul.user_id = mda.parent_id
                LEFT JOIN (
                    SELECT {$this->schema}.tbl_trip.* 
                    FROM {$this->schema}.tbl_trip 
                    LEFT JOIN {$this->schema}.trip_schedule ts 
                        ON ts.deviceid = {$this->schema}.tbl_trip.deviceid
                    WHERE ( (sttimestamp BETWEEN ? AND ?)
                            OR (endtimestamp   BETWEEN ? AND ?)
                            OR (sttimestamp < ? AND endtimestamp > ?))
                    ";
                    $parameters = [$start_date, $end_date,$start_date, $end_date,$start_date, $end_date];
                    
                    if (!empty($data['usertype']) && $data['usertype'] != "All") {
                        $sql .= " AND ts.device_type = ?";
                        $parameters[] = $data['usertype'];
                    }

                    $sql .= ") t 
                    ON t.deviceid = mda.deviceid
                    LEFT JOIN {$this->schema}.trip_schedule_details tsd 
                     ON tsd.schedule_details_id = t.schedule_details_id 
                    LEFT JOIN {$this->schema}.trip_schedule ts
                     ON ts.schedule_id = tsd.schedule_id";
                    
                    if (!empty($data['usertype']) && $data['usertype'] != "All") {
                        $sql .= " AND ts.device_type = ?";
                        $parameters[] = $data['usertype'];
                    }

                    $sql .= " WHERE 
                        ul.group_id = 2
                        AND mda.active = 1 ";
                        
                    if (!empty($data['usertype']) && $data['usertype'] != "All") {
                        $sql .= " AND ts.device_type = ?";
                        $parameters[] = $data['usertype'];
                    }

                    if (!empty($data['sse_pwy']) && $data['sse_pwy'] != "All") {
                        $sql .= " AND p_ul.user_id = ?";
                        $parameters[] = $data['sse_pwy'];
                    }

                    if (!empty($data['pwi_id']) && $data['pwi_id'] != "All") {
                        $sql .= " AND ul.user_id = ?";
                        $parameters[] = $data['pwi_id'];
                    }

                    $sql .= "),
                    device_status AS (
                        SELECT 
                            organisation,
                            user_id,
                            parent_organisation,
                            allocation_status,
                            device_imei,
                            device_name,
                            MAX(CASE WHEN trip_imeino IS NOT NULL THEN 1 ELSE 0 END) AS has_trip,
                            MAX(CASE WHEN trip_status = 'Patroling Completed' THEN 1 ELSE 0 END) AS covered,
                            COALESCE(distance_travelled, 0) AS expected_distance  ,
                            COALESCE(totaldistancetravel, 0) AS actual_distance
                        FROM base_data
                        GROUP BY organisation, user_id, parent_organisation, allocation_status, device_imei, device_name, distance_travelled, totaldistancetravel
                    )
                    SELECT 
                        organisation,
                        user_id,
                        parent_organisation,
                        COUNT(CASE WHEN allocation_status = 'Not Allocated' THEN 1 END) AS not_allocated_count,
                        string_agg(
                            CASE WHEN allocation_status = 'Not Allocated' THEN device_imei END, ',' 
                            ORDER BY device_imei
                        ) AS not_allocated_imeino,
                        string_agg(
                            CASE WHEN allocation_status = 'Not Allocated' THEN device_name END, ',' 
                            ORDER BY device_imei
                        ) AS not_allocated_devicename,
                        COUNT(CASE 
                            WHEN allocation_status = 'Allocated' 
                            AND has_trip = 0 
                            THEN 1 
                        END) AS device_off_count,
                        string_agg(
                            CASE 
                                WHEN allocation_status = 'Allocated' 
                                AND has_trip = 0 
                                THEN device_imei 
                            END, ',' 
                            ORDER BY device_imei
                        ) AS device_off_imeino,
                        string_agg(
                            CASE 
                                WHEN allocation_status = 'Allocated' 
                                AND has_trip = 0 
                                THEN device_name 
                            END, ',' 
                            ORDER BY device_imei
                        ) AS device_off_devicename,
                        COUNT(CASE 
                            WHEN allocation_status = 'Allocated' 
                            AND has_trip = 1 
                            AND (covered = 1 OR (covered = 0 AND actual_distance >= expected_distance)) 
                            THEN 1 
                        END) AS beats_covered_count,
                        COUNT(CASE 
                            WHEN allocation_status = 'Allocated' 
                            AND has_trip = 1 
                            AND covered = 0 
                            AND actual_distance < expected_distance 
                            THEN 1 
                        END) AS beats_not_covered_count
                    FROM device_status
                    GROUP BY organisation, user_id, parent_organisation; ";

                


            

            $result = $this->db->query($sql, $parameters)->getResult();

            $data['alldata'] = $result;
        }

        $dat[0]['A'] = " Trip Details Summary Report From Date " . date("d-m-Y H:i:s", strtotime($start_date)) . " To " . date("d-m-Y H:i:s", strtotime($end_date));

        // Initialize header row
        $dat[1]['A'] = "SL No.";
        $dat[1]['B'] = "PWI";
        $dat[1]['C'] = "Section Name";
        $dat[1]['D'] = "Off Device";
        $dat[1]['E'] = "Beat Covered";
        $dat[1]['F'] = "Beat Not Covered";
        $dat[1]['G'] = "Not Allocated";
        $dat[1]['H'] = "Total";
        
        // Initialize counters
        $Key = 1;
        $device_off_count = 0;
        $beats_covered_count = 0;
        $beats_not_covered_count = 0;
        $not_allocated_count = 0;

        foreach ($result as $irow) {

            // Fill data for each row
            $dat[$Key + 1]['A'] = $Key;
            $dat[$Key + 1]['B'] = $irow->parent_organisation;
            $dat[$Key + 1]['C'] = $irow->organisation;
            $dat[$Key + 1]['D'] = $irow->device_off_count;
            $dat[$Key + 1]['E'] = $irow->beats_covered_count;
            $dat[$Key + 1]['F'] = $irow->beats_not_covered_count;
            $dat[$Key + 1]['G'] = $irow->not_allocated_count;
            $dat[$Key + 1]['H'] = $irow->beats_not_covered_count+$irow->device_off_count+$irow->beats_covered_count+$irow->not_allocated_count;

            $Key++;
            $device_off_count = $irow->device_off_count + $device_off_count;
            $beats_covered_count = $irow->beats_covered_count + $beats_covered_count;
            $beats_not_covered_count = $irow->beats_not_covered_count + $beats_not_covered_count;
            $not_allocated_count = $irow->not_allocated_count + $not_allocated_count;
        }

        $dat[$Key + 1]['C'] = 'Total';
        $dat[$Key + 1]['D'] = $device_off_count;
        $dat[$Key + 1]['E'] = $beats_covered_count;
        $dat[$Key + 1]['F'] = $beats_not_covered_count;
        $dat[$Key + 1]['G'] = $not_allocated_count;
        $dat[$Key + 1]['H'] = $not_allocated_count+$device_off_count+$beats_covered_count+$beats_not_covered_count;

        // Create the Excel file
        $filename = 'Trip_Details_Summary_Report_' . date("d-m-Y H:i", strtotime($start_date)) . '_To_' . date("d-m-Y H:i", strtotime($end_date)) . '.xlsx';
        exceldownload($dat, $filename);

    }

    public function tripDetailsSummaryReportPdf()
    { 

        // Check if user is logged in
        if (!session()->has('login_sess_data')) {
            return redirect()->to('/');
        }

        $data = [];
        $data['sessdata'] = $this->sessdata;
        $data['page_title'] = "Trip Details Report";
        $data['date_from'] = '';
        $data['usertype'] = 'All';

        if ($this->request->getMethod() == 'POST') {
            // echo "<pre>";print_r($this->request->getPost());exit();
            $start_date = date("Y-m-d H:i:s", strtotime(trim($this->request->getPost('stdt'))));
            $end_date = date("Y-m-d H:i:s", strtotime(trim($this->request->getPost('endt'))));
            $data['stdt'] = date("d-m-Y H:i", strtotime(trim($this->request->getPost('stdt'))));
            $data['endt'] = date("d-m-Y H:i", strtotime(trim($this->request->getPost('endt'))));
            $data['stdt1'] = date("d-m-Y_H:i", strtotime(trim($this->request->getPost('stdt'))));
            $data['endt1'] = date("d-m-Y_H:i", strtotime(trim($this->request->getPost('endt'))));
            $data['usertype'] = trim($this->request->getPost('usertype')) ;
            $data['schema'] = $schemaname = $this->schema;
            $data['sse_pwy'] = $sse_pwy = trim($this->request->getPost('pway_id'));
            $data['pwi_id'] = $pwi_id = trim($this->request->getPost('user'));
            $user_id = $this->sessdata['user_id'];

            /* ---- Old Table Structure ----- */
            // $sql = "WITH base_data AS (
            //     SELECT 
            //         ul.organisation,
            //         ul.user_id,
            //         p_ul.organisation AS parent_organisation,
            //         md.serial_no AS device_imei,
            //         msd.device_name AS device_name,
            //         CASE 
            //             WHEN lower(msd.device_name) LIKE '%stock%' 
            //                  OR msd.device_name IS NULL 
            //                  OR trim(msd.device_name) = '' 
            //             THEN 'Not Allocated'
            //             ELSE 'Allocated'
            //         END AS allocation_status,
            //         t.imeino AS trip_imeino,
            //         su.distance_travelled,
            //         t.totaldistancetravel,
            //         CASE 
            //             WHEN (
            //                     TRIM(su.stpole) = TRIM(t.stpole)
            //                     OR TRIM(su.stpole) = ANY (
            //                         ARRAY (
            //                             SELECT trim(elem)
            //                             FROM unnest(string_to_array(t.beats_covered, ',')) AS elem
            //                         )
            //                     )
            //                  )
            //                  AND (
            //                     TRIM(su.endpole) = TRIM(t.endpole)
            //                     OR TRIM(su.endpole) = ANY (
            //                         ARRAY (
            //                             SELECT trim(elem)
            //                             FROM unnest(string_to_array(t.beats_covered, ',')) AS elem
            //                         )
            //                     )
            //                  )
            //             THEN 'Patroling Completed'
            //             ELSE NULL
            //         END AS trip_status
            //     FROM public.master_device_assign mda
            //     LEFT JOIN public.user_login ul 
            //         ON ul.user_id = mda.user_id
            //     LEFT JOIN public.master_device_details md 
            //         ON md.id = mda.deviceid
            //     LEFT JOIN {$this->schema}.master_device_setup msd 
            //         ON msd.deviceid = mda.deviceid
            //     LEFT JOIN public.user_login p_ul 
            //         ON p_ul.user_id = mda.parent_id
            //     LEFT JOIN (
            //         SELECT {$this->schema}.tbl_trip.* 
            //         FROM {$this->schema}.tbl_trip 
            //         LEFT JOIN {$this->schema}.tbl_device_schedule_updated su 
            //             ON su.imeino = tbl_trip.imeino 
            //         WHERE ( (sttimestamp BETWEEN ? AND ?)
            //                 OR (endtimestamp   BETWEEN ? AND ?)
            //                 OR (sttimestamp < ? AND endtimestamp > ?))
            //         ";
            //         $parameters = [$start_date, $end_date,$start_date, $end_date,$start_date, $end_date];
                    
            //         if (!empty($data['usertype']) && $data['usertype'] != "All") {
            //             $sql .= " AND su.usertype = ?";
            //             $parameters[] = $data['usertype'];
            //         }

            //         $sql .= ") t 
            //         ON t.deviceid = mda.deviceid
            //         LEFT JOIN {$this->schema}.tbl_device_schedule_updated su 
            //         ON su.imeino = md.serial_no ";

                    
            //         if (!empty($data['usertype']) && $data['usertype'] != "All") {
            //             $sql .= " AND su.usertype = ?";
            //             $parameters[] = $data['usertype'];
            //         }

            //         $sql .= " WHERE 
            //             ul.group_id = 2
            //             AND mda.active = 1 ";
                        
            //         if (!empty($data['usertype']) && $data['usertype'] != "All") {
            //             $sql .= " AND su.usertype = ?";
            //             $parameters[] = $data['usertype'];
            //         }

            //         if (!empty($data['sse_pwy']) && $data['sse_pwy'] != "All") {
            //             $sql .= " AND p_ul.user_id = ?";
            //             $parameters[] = $data['sse_pwy'];
            //         }

            //         if (!empty($data['pwi_id']) && $data['pwi_id'] != "All") {
            //             $sql .= " AND ul.user_id = ?";
            //             $parameters[] = $data['pwi_id'];
            //         }

            //         $sql .= "),
            //         device_status AS (
            //             SELECT 
            //                 organisation,
            //                 user_id,
            //                 parent_organisation,
            //                 allocation_status,
            //                 device_imei,
            //                 device_name,
            //                 MAX(CASE WHEN trip_imeino IS NOT NULL THEN 1 ELSE 0 END) AS has_trip,
            //                 MAX(CASE WHEN trip_status = 'Patroling Completed' THEN 1 ELSE 0 END) AS covered,
            //                 MAX(COALESCE(distance_travelled, 0)) AS actual_distance,
            //                 SUM(COALESCE(totaldistancetravel, 0)) AS expected_distance
            //             FROM base_data
            //             GROUP BY organisation, user_id, parent_organisation, allocation_status, device_imei, device_name
            //         )
            //         SELECT 
            //             organisation,
            //             user_id,
            //             parent_organisation,
            //             COUNT(CASE WHEN allocation_status = 'Not Allocated' THEN 1 END) AS not_allocated_count,
            //             string_agg(
            //                 CASE WHEN allocation_status = 'Not Allocated' THEN device_imei END, ',' 
            //                 ORDER BY device_imei
            //             ) AS not_allocated_imeino,
            //             string_agg(
            //                 CASE WHEN allocation_status = 'Not Allocated' THEN device_name END, ',' 
            //                 ORDER BY device_imei
            //             ) AS not_allocated_devicename,
            //             COUNT(CASE 
            //                 WHEN allocation_status = 'Allocated' 
            //                 AND has_trip = 0 
            //                 THEN 1 
            //             END) AS device_off_count,
            //             string_agg(
            //                 CASE 
            //                     WHEN allocation_status = 'Allocated' 
            //                     AND has_trip = 0 
            //                     THEN device_imei 
            //                 END, ',' 
            //                 ORDER BY device_imei
            //             ) AS device_off_imeino,
            //             string_agg(
            //                 CASE 
            //                     WHEN allocation_status = 'Allocated' 
            //                     AND has_trip = 0 
            //                     THEN device_name 
            //                 END, ',' 
            //                 ORDER BY device_imei
            //             ) AS device_off_devicename,
            //             COUNT(CASE 
            //                 WHEN allocation_status = 'Allocated' 
            //                 AND has_trip = 1 
            //                 AND (covered = 1 OR (covered = 0 AND actual_distance >= expected_distance)) 
            //                 THEN 1 
            //             END) AS beats_covered_count,
            //             COUNT(CASE 
            //                 WHEN allocation_status = 'Allocated' 
            //                 AND has_trip = 1 
            //                 AND covered = 0 
            //                 AND actual_distance < expected_distance 
            //                 THEN 1 
            //             END) AS beats_not_covered_count
            //         FROM device_status
            //         GROUP BY organisation, user_id, parent_organisation; ";

            // $result = $this->db->query($sql, $parameters)->getResult();

            /* ---- New Table Structure Updated ---- */

            $sql = "WITH base_data AS (
                SELECT 
                    ul.organisation,
                    ul.user_id,
                    p_ul.organisation AS parent_organisation,
                    md.serial_no AS device_imei,
                    msd.device_name AS device_name,
                    CASE 
                        WHEN lower(msd.device_name) LIKE '%stock%' 
                             OR msd.device_name IS NULL 
                             OR trim(msd.device_name) = '' 
                        THEN 'Not Allocated'
                        ELSE 'Allocated'
                    END AS allocation_status,
                    t.imeino AS trip_imeino,
                    tsd.expected_distance AS distance_travelled,
                    t.totaldistancetravel,
                    CASE 
                        WHEN (
                                TRIM(tsd.expected_stpole) = TRIM(t.stpole)
                                OR TRIM(tsd.expected_stpole) = ANY (
                                    ARRAY (
                                        SELECT trim(elem)
                                        FROM unnest(string_to_array(t.beats_covered, ',')) AS elem
                                    )
                                )
                             )
                             AND (
                                TRIM(tsd.expected_endpole) = TRIM(t.endpole)
                                OR TRIM(tsd.expected_endpole) = ANY (
                                    ARRAY (
                                        SELECT trim(elem)
                                        FROM unnest(string_to_array(t.beats_covered, ',')) AS elem
                                    )
                                )
                             )
                        THEN 'Patroling Completed'
                        ELSE NULL
                    END AS trip_status
                FROM public.master_device_assign mda
                LEFT JOIN public.user_login ul 
                    ON ul.user_id = mda.user_id
                LEFT JOIN public.master_device_details md 
                    ON md.id = mda.deviceid
                LEFT JOIN {$this->schema}.master_device_setup msd 
                    ON msd.deviceid = mda.deviceid
                LEFT JOIN public.user_login p_ul 
                    ON p_ul.user_id = mda.parent_id
                LEFT JOIN (
                    SELECT {$this->schema}.tbl_trip.* 
                    FROM {$this->schema}.tbl_trip 
                    LEFT JOIN {$this->schema}.trip_schedule ts 
                        ON ts.deviceid = {$this->schema}.tbl_trip.deviceid
                    WHERE ( (sttimestamp BETWEEN ? AND ?)
                            OR (endtimestamp   BETWEEN ? AND ?)
                            OR (sttimestamp < ? AND endtimestamp > ?))
                    ";
                    $parameters = [$start_date, $end_date,$start_date, $end_date,$start_date, $end_date];
                    
                    if (!empty($data['usertype']) && $data['usertype'] != "All") {
                        $sql .= " AND ts.device_type = ?";
                        $parameters[] = $data['usertype'];
                    }

                    $sql .= ") t 
                    ON t.deviceid = mda.deviceid
                    LEFT JOIN {$this->schema}.trip_schedule_details tsd 
                     ON tsd.schedule_details_id = t.schedule_details_id 
                    LEFT JOIN {$this->schema}.trip_schedule ts
                     ON ts.schedule_id = tsd.schedule_id";
                    
                    if (!empty($data['usertype']) && $data['usertype'] != "All") {
                        $sql .= " AND ts.device_type = ?";
                        $parameters[] = $data['usertype'];
                    }

                    $sql .= " WHERE 
                        ul.group_id = 2
                        AND mda.active = 1 ";
                        
                    if (!empty($data['usertype']) && $data['usertype'] != "All") {
                        $sql .= " AND ts.device_type = ?";
                        $parameters[] = $data['usertype'];
                    }

                    if (!empty($data['sse_pwy']) && $data['sse_pwy'] != "All") {
                        $sql .= " AND p_ul.user_id = ?";
                        $parameters[] = $data['sse_pwy'];
                    }

                    if (!empty($data['pwi_id']) && $data['pwi_id'] != "All") {
                        $sql .= " AND ul.user_id = ?";
                        $parameters[] = $data['pwi_id'];
                    }

                    $sql .= "),
                    device_status AS (
                        SELECT 
                            organisation,
                            user_id,
                            parent_organisation,
                            allocation_status,
                            device_imei,
                            device_name,
                            MAX(CASE WHEN trip_imeino IS NOT NULL THEN 1 ELSE 0 END) AS has_trip,
                            MAX(CASE WHEN trip_status = 'Patroling Completed' THEN 1 ELSE 0 END) AS covered,
                            COALESCE(distance_travelled, 0) AS expected_distance  ,
                            COALESCE(totaldistancetravel, 0) AS actual_distance
                        FROM base_data
                        GROUP BY organisation, user_id, parent_organisation, allocation_status, device_imei, device_name, distance_travelled, totaldistancetravel
                    )
                    SELECT 
                        organisation,
                        user_id,
                        parent_organisation,
                        COUNT(CASE WHEN allocation_status = 'Not Allocated' THEN 1 END) AS not_allocated_count,
                        string_agg(
                            CASE WHEN allocation_status = 'Not Allocated' THEN device_imei END, ',' 
                            ORDER BY device_imei
                        ) AS not_allocated_imeino,
                        string_agg(
                            CASE WHEN allocation_status = 'Not Allocated' THEN device_name END, ',' 
                            ORDER BY device_imei
                        ) AS not_allocated_devicename,
                        COUNT(CASE 
                            WHEN allocation_status = 'Allocated' 
                            AND has_trip = 0 
                            THEN 1 
                        END) AS device_off_count,
                        string_agg(
                            CASE 
                                WHEN allocation_status = 'Allocated' 
                                AND has_trip = 0 
                                THEN device_imei 
                            END, ',' 
                            ORDER BY device_imei
                        ) AS device_off_imeino,
                        string_agg(
                            CASE 
                                WHEN allocation_status = 'Allocated' 
                                AND has_trip = 0 
                                THEN device_name 
                            END, ',' 
                            ORDER BY device_imei
                        ) AS device_off_devicename,
                        COUNT(CASE 
                            WHEN allocation_status = 'Allocated' 
                            AND has_trip = 1 
                            AND (covered = 1 OR (covered = 0 AND actual_distance >= expected_distance)) 
                            THEN 1 
                        END) AS beats_covered_count,
                        COUNT(CASE 
                            WHEN allocation_status = 'Allocated' 
                            AND has_trip = 1 
                            AND covered = 0 
                            AND actual_distance < expected_distance 
                            THEN 1 
                        END) AS beats_not_covered_count
                    FROM device_status
                    GROUP BY organisation, user_id, parent_organisation; ";


            $result = $this->db->query($sql, $parameters)->getResult();

            $data['alldata'] = $result;
        }

        $html = view('traxreport/pdf_tripsummarydetails', $data); // Load view in CI4
        $filename = 'Trip_Details_Summary_Report_' . time();

        // Instantiate the MakePDF class
        $pdf = new MakePDF();

        // Set the filename and content
        $pdf->setFileName($filename);
        $pdf->setContent($html);

        // Generate and stream the PDF to the browser
        $pdf->getPdf();  // true to stream the PDF

    }

    public function tripDetailsSummaryReportDetails()
    { 

        // Check if user is logged in
        if (!session()->has('login_sess_data')) {
            return redirect()->to('/');
        }

        $data = [];
        $data['sessdata'] = $this->sessdata;
        $data['page_title'] = "Trip Summary Device Details Report";
        $data['date_from'] = '';
        $data['usertype'] = 'All';

        //if ($this->request->getMethod() == 'POST') {
            $data['stdt1'] = $stdt = $this->request->getUri()->getSegment(3);
            $data['stdt'] = $stdt = str_replace("_"," ",$stdt);
            $data['endt1'] = $endt = $this->request->getUri()->getSegment(4);
            $data['endt'] = $endt = str_replace("_"," ",$endt);
            $start_date = date("Y-m-d H:i:s", strtotime($stdt));
            $end_date = date("Y-m-d H:i:s", strtotime($endt));
            $data['usertype'] = $this->request->getUri()->getSegment(5);
            $data['user_id'] = $user_id = $this->request->getUri()->getSegment(6);
            $data['type'] = $type = $this->request->getUri()->getSegment(7);

            $sql = "WITH base_data AS (
                SELECT 
                    ul.organisation,
                    ul.user_id,
                    p_ul.organisation AS parent_organisation,
                    md.serial_no AS device_imei,
                    msd.device_name AS device_name,
                    CASE 
                        WHEN lower(msd.device_name) LIKE '%stock%' 
                             OR msd.device_name IS NULL 
                             OR trim(msd.device_name) = '' 
                        THEN 'Not Allocated'
                        ELSE 'Allocated'
                    END AS allocation_status,
                    t.imeino AS trip_imeino,
                    tsd.expected_distance AS distance_travelled,
                    t.totaldistancetravel,
                    CASE 
                        WHEN (
                                TRIM(tsd.expected_stpole) = TRIM(t.stpole)
                                OR TRIM(tsd.expected_stpole) = ANY (
                                    ARRAY (
                                        SELECT trim(elem)
                                        FROM unnest(string_to_array(t.beats_covered, ',')) AS elem
                                    )
                                )
                             )
                             AND (
                                TRIM(tsd.expected_endpole) = TRIM(t.endpole)
                                OR TRIM(tsd.expected_endpole) = ANY (
                                    ARRAY (
                                        SELECT trim(elem)
                                        FROM unnest(string_to_array(t.beats_covered, ',')) AS elem
                                    )
                                )
                             )
                        THEN 'Patroling Completed'
                        ELSE NULL
                    END AS trip_status
                FROM public.master_device_assign mda
                LEFT JOIN public.user_login ul 
                    ON ul.user_id = mda.user_id
                LEFT JOIN public.master_device_details md 
                    ON md.id = mda.deviceid
                LEFT JOIN {$this->schema}.master_device_setup msd 
                    ON msd.deviceid = mda.deviceid
                LEFT JOIN public.user_login p_ul 
                    ON p_ul.user_id = mda.parent_id
                LEFT JOIN (
                    SELECT {$this->schema}.tbl_trip.* 
                    FROM {$this->schema}.tbl_trip 
                    LEFT JOIN {$this->schema}.trip_schedule ts 
                        ON ts.deviceid = {$this->schema}.tbl_trip.deviceid
                    WHERE ( (sttimestamp BETWEEN ? AND ?)
                            OR (endtimestamp   BETWEEN ? AND ?)
                            OR (sttimestamp < ? AND endtimestamp > ?))
                    ";
                    $parameters = [$start_date, $end_date,$start_date, $end_date,$start_date, $end_date];
                    
                    if (!empty($data['usertype']) && $data['usertype'] != "All") {
                        $sql .= " AND ts.device_type = ?";
                        $parameters[] = $data['usertype'];
                    }

                    $sql .= ") t 
                    ON t.deviceid = mda.deviceid
                LEFT JOIN {$this->schema}.trip_schedule_details tsd 
                    ON tsd.schedule_details_id = t.schedule_details_id 
                LEFT JOIN {$this->schema}.trip_schedule ts
                    ON ts.schedule_id = tsd.schedule_id";

                    
                    if (!empty($data['usertype']) && $data['usertype'] != "All") {
                         $sql .= " AND ts.device_type = ?";
                         $parameters[] = $data['usertype'];
                     }

                    $sql .= " WHERE 
                        ul.group_id = 2
                        AND mda.active = 1 ";
                        
                    if (!empty($data['usertype']) && $data['usertype'] != "All") {
                        $sql .= " AND ts.device_type = ?";
                        $parameters[] = $data['usertype'];
                    }



                    if($user_id !='All'){
                            $sql .= " AND mda.user_id = ".$user_id;
                    }
                       

                  


                    $sql .= "),
                    device_status AS (
                        SELECT 
                            organisation,
                            user_id,
                            parent_organisation,
                            allocation_status,
                            device_imei,
                            device_name,
                            MAX(CASE WHEN trip_imeino IS NOT NULL THEN 1 ELSE 0 END) AS has_trip,
                            MAX(CASE WHEN trip_status = 'Patroling Completed' THEN 1 ELSE 0 END) AS covered,
                            COALESCE(distance_travelled, 0) AS expected_distance ,
                            COALESCE(totaldistancetravel, 0) AS actual_distance
                        FROM base_data
                        GROUP BY organisation, user_id, parent_organisation, allocation_status, device_imei, device_name, distance_travelled, totaldistancetravel
                    )
                    SELECT 
                        organisation,
                        user_id,
                        parent_organisation,
                        COUNT(CASE WHEN allocation_status = 'Not Allocated' THEN 1 END) AS not_allocated_count,
                        string_agg(
                            CASE WHEN allocation_status = 'Not Allocated' THEN device_imei END, ',' 
                            ORDER BY device_imei
                        ) AS not_allocated_imeino,
                        string_agg(
                            CASE WHEN allocation_status = 'Not Allocated' THEN device_name END, ',' 
                            ORDER BY device_imei
                        ) AS not_allocated_devicename,
                        COUNT(CASE 
                            WHEN allocation_status = 'Allocated' 
                            AND has_trip = 0 
                            THEN 1 
                        END) AS device_off_count,
                        string_agg(
                            CASE 
                                WHEN allocation_status = 'Allocated' 
                                AND has_trip = 0 
                                THEN device_imei 
                            END, ',' 
                            ORDER BY device_imei
                        ) AS device_off_imeino,
                        string_agg(
                            CASE 
                                WHEN allocation_status = 'Allocated' 
                                AND has_trip = 0 
                                THEN device_name 
                            END, ',' 
                            ORDER BY device_imei
                        ) AS device_off_devicename,
                        COUNT(CASE 
                            WHEN allocation_status = 'Allocated' 
                            AND has_trip = 1 
                            AND (covered = 1 OR (covered = 0 AND actual_distance >= expected_distance)) 
                            THEN 1 
                        END) AS beats_covered_count,

                        string_agg(
                            CASE 
                              WHEN allocation_status = 'Allocated'
                               AND has_trip = 1 
                               AND (covered = 1 OR (covered = 0 AND actual_distance >= expected_distance)) 
                              THEN device_imei 
                            END, ',' 
                            ORDER BY device_imei
                          ) AS beats_covered_imeino,
                          string_agg(
                            CASE 
                              WHEN allocation_status = 'Allocated'
                               AND has_trip = 1 
                               AND (covered = 1 OR (covered = 0 AND actual_distance >= expected_distance)) 
                              THEN device_name 
                            END, ',' 
                            ORDER BY device_imei
                          ) AS beats_covered_devicename,
                        
                        COUNT(CASE 
                            WHEN allocation_status = 'Allocated' 
                            AND has_trip = 1 
                            AND covered = 0 
                            AND actual_distance < expected_distance 
                            THEN 1 
                        END) AS beats_not_covered_count,
                        string_agg(
                            CASE 
                              WHEN allocation_status = 'Allocated'
                               AND has_trip = 1 
                               AND covered = 0 
                               AND actual_distance < expected_distance 
                              THEN device_imei 
                            END, ',' 
                            ORDER BY device_imei
                          ) AS beats_not_covered_imeino,
                          string_agg(
                            CASE 
                              WHEN allocation_status = 'Allocated'
                               AND has_trip = 1 
                               AND covered = 0 
                               AND actual_distance < expected_distance 
                              THEN device_name 
                            END, ',' 
                            ORDER BY device_imei
                          ) AS beats_not_covered_devicename
                    FROM device_status
                    GROUP BY organisation, user_id, parent_organisation; ";

            // echo "<pre>".$sql;exit;
            $result = $this->db->query($sql, $parameters)->getResult();
            
            /*echo "<pre>";
            print_r($result);
            die();*/
            
            

            $newresult = [];

            if (!empty($result) && is_array($result) && isset($result[0]->organisation)) {

                foreach ($result as $j=>$entry) {
                    if($this->request->getUri()->getSegment(7) == 'offDevice') {
                        $deviceIMEINO = explode(',',$result[$j]->device_off_imeino);
                        $deviceOffName = explode(',',$result[$j]->device_off_devicename);
                        //echo "<pre>";
                        //print_r($deviceOffName);
                        foreach($deviceIMEINO as $i=>$data1) {
                            if($data1){
                                $newresult[] = [
                                    'device_no' => $data1,
                                    'pwi' => $result[$j]->parent_organisation,
                                    'section_name' => $result[$j]->organisation,
                                    'start_date' => $start_date,
                                    'end_date' => $end_date,
                                    'devicename' => $deviceOffName[$i]
                                ];
                            }
                            
                        }

                    } else if($this->request->getUri()->getSegment(7) == 'beatCovered') {
                        $deviceIMEINOCovered = explode(',',$result[$j]->beats_covered_imeino);
                        $deviceCoveredName = explode(',',$result[$j]->beats_covered_devicename);
                        foreach($deviceIMEINOCovered as $i=>$data1) {
                            if($data1){
                                $newresult[] = [
                                    'device_no' => $data1,
                                    'pwi' => $result[$j]->parent_organisation,
                                    'section_name' => $result[$j]->organisation,
                                    'start_date' => $start_date,
                                    'end_date' => $end_date,
                                    'devicename' => $deviceCoveredName[$i]
                                ];
                            }
                        }
                        
                    } else if($this->request->getUri()->getSegment(7) == 'beatNotCovered') {
                        $deviceIMEINONotCovered = explode(',',$result[$j]->beats_not_covered_imeino);
                        $deviceNotCoveredName = explode(',',$result[$j]->beats_not_covered_devicename);
                        foreach($deviceIMEINONotCovered as $i=>$data1) {
                            if($data1){
                                $newresult[] = [
                                    'device_no' => $data1,
                                    'pwi' => $result[$j]->parent_organisation,
                                    'section_name' => $result[$j]->organisation,
                                    'start_date' => $start_date,
                                    'end_date' => $end_date,
                                    'devicename' => $deviceNotCoveredName[$i]
                                ];
                            }
                        }
                    } else if($this->request->getUri()->getSegment(7) == 'notallocated') {
                        $deviceIMEINONotAllocated = explode(',',$result[$j]->not_allocated_imeino);
                        $deviceNotAllocatedName = explode(',',$result[$j]->not_allocated_devicename);
                        foreach($deviceIMEINONotAllocated as $i=>$data1) {
                            if($data1){
                                $newresult[] = [
                                    'device_no' => $data1,
                                    'pwi' => $result[$j]->parent_organisation,
                                    'section_name' => $result[$j]->organisation,
                                    'start_date' => $start_date,
                                    'end_date' => $end_date,
                                    'devicename' => $deviceNotAllocatedName[$i] ?? null
                                ];
                            }
                        }
                    }else if($this->request->getUri()->getSegment(7) == 'alltypes') {
                        $deviceIMEINONotAllocated = explode(',',$result[$j]->not_allocated_imeino);
                        $deviceNotAllocatedName = explode(',',$result[$j]->not_allocated_devicename);
                        foreach($deviceIMEINONotAllocated as $i=>$data1) {
                            if($data1){
                                $newresult[] = [
                                    'device_no' => $data1,
                                    'pwi' => $result[$j]->parent_organisation,
                                    'section_name' => $result[$j]->organisation,
                                    'start_date' => $start_date,
                                    'end_date' => $end_date,
                                    'devicename' => $deviceNotAllocatedName[$i] ?? null
                                ];
                            }
                        }

                        $deviceIMEINOCovered = explode(',',$result[$j]->beats_covered_imeino);
                        $deviceCoveredName = explode(',',$result[$j]->beats_covered_devicename);
                        foreach($deviceIMEINOCovered as $i=>$data1) {
                            if($data1){
                                $newresult[] = [
                                    'device_no' => $data1,
                                    'pwi' => $result[$j]->parent_organisation,
                                    'section_name' => $result[$j]->organisation,
                                    'start_date' => $start_date,
                                    'end_date' => $end_date,
                                    'devicename' => $deviceCoveredName[$i]
                                ];
                            }
                        }

                        $deviceIMEINO = explode(',',$result[$j]->device_off_imeino);
                        $deviceOffName = explode(',',$result[$j]->device_off_devicename);
                        //echo "<pre>";
                        //print_r($deviceOffName);
                        foreach($deviceIMEINO as $i=>$data1) {
                            if($data1){
                                $newresult[] = [
                                    'device_no' => $data1,
                                    'pwi' => $result[$j]->parent_organisation,
                                    'section_name' => $result[$j]->organisation,
                                    'start_date' => $start_date,
                                    'end_date' => $end_date,
                                    'devicename' => $deviceOffName[$i]
                                ];
                            }
                            
                        }

                        $deviceIMEINONotCovered = explode(',',$result[$j]->beats_not_covered_imeino);
                        $deviceNotCoveredName = explode(',',$result[$j]->beats_not_covered_devicename);
                        foreach($deviceIMEINONotCovered as $i=>$data1) {
                            if($data1){
                                $newresult[] = [
                                    'device_no' => $data1,
                                    'pwi' => $result[$j]->parent_organisation,
                                    'section_name' => $result[$j]->organisation,
                                    'start_date' => $start_date,
                                    'end_date' => $end_date,
                                    'devicename' => $deviceNotCoveredName[$i]
                                ];
                            }
                        }


                    }
                }

            }
            
                
           // echo "<pre>";print_r($newresult);
           // exit();
            $data['alldata'] = $newresult;
    

        $data['middle'] = view('traxreport/tripdetailssnapshotDetails', $data);
        return view('mainlayout', $data);
    }

    public function tripDetailsSummaryReportDetailsExcel()
    { 

        // Check if user is logged in
        if (!session()->has('login_sess_data')) {
            return redirect()->to('/');
        }

        $data = [];
        $data['sessdata'] = $this->sessdata;
        $data['page_title'] = "Trip Summary Device Details Report";
        $data['date_from'] = '';
        $data['usertype'] = 'All';

        //if ($this->request->getMethod() == 'POST') {
            $stdt = $this->request->getUri()->getSegment(3);
            $data['stdt'] = $stdt = str_replace("_"," ",$stdt);
            $endt = $this->request->getUri()->getSegment(4);
            $data['endt'] = $endt = str_replace("_"," ",$endt);
            $start_date = date("Y-m-d H:i:s", strtotime($stdt));
            $end_date = date("Y-m-d H:i:s", strtotime($endt));
            $data['usertype'] = $this->request->getUri()->getSegment(5);
            $user_id = $this->request->getUri()->getSegment(6);
            $data['type'] = $type = $this->request->getUri()->getSegment(7);

            /* ----- old table structure ----- */
            // $sql = "WITH base_data AS (
            //     SELECT 
            //         ul.organisation,
            //         ul.user_id,
            //         p_ul.organisation AS parent_organisation,
            //         md.serial_no AS device_imei,
            //         msd.device_name AS device_name,
            //         CASE 
            //             WHEN lower(msd.device_name) LIKE '%stock%' 
            //                  OR msd.device_name IS NULL 
            //                  OR trim(msd.device_name) = '' 
            //             THEN 'Not Allocated'
            //             ELSE 'Allocated'
            //         END AS allocation_status,
            //         t.imeino AS trip_imeino,
            //         su.distance_travelled,
            //         t.totaldistancetravel,
            //         CASE 
            //             WHEN (
            //                     TRIM(su.stpole) = TRIM(t.stpole)
            //                     OR TRIM(su.stpole) = ANY (
            //                         ARRAY (
            //                             SELECT trim(elem)
            //                             FROM unnest(string_to_array(t.beats_covered, ',')) AS elem
            //                         )
            //                     )
            //                  )
            //                  AND (
            //                     TRIM(su.endpole) = TRIM(t.endpole)
            //                     OR TRIM(su.endpole) = ANY (
            //                         ARRAY (
            //                             SELECT trim(elem)
            //                             FROM unnest(string_to_array(t.beats_covered, ',')) AS elem
            //                         )
            //                     )
            //                  )
            //             THEN 'Patroling Completed'
            //             ELSE NULL
            //         END AS trip_status
            //     FROM public.master_device_assign mda
            //     LEFT JOIN public.user_login ul 
            //         ON ul.user_id = mda.user_id
            //     LEFT JOIN public.master_device_details md 
            //         ON md.id = mda.deviceid
            //     LEFT JOIN {$this->schema}.master_device_setup msd 
            //         ON msd.deviceid = mda.deviceid
            //     LEFT JOIN public.user_login p_ul 
            //         ON p_ul.user_id = mda.parent_id
            //     LEFT JOIN (
            //         SELECT {$this->schema}.tbl_trip.* 
            //         FROM {$this->schema}.tbl_trip 
            //         LEFT JOIN {$this->schema}.tbl_device_schedule_updated su 
            //             ON su.imeino = tbl_trip.imeino 
            //         WHERE ( (sttimestamp BETWEEN ? AND ?)
            //                 OR (endtimestamp   BETWEEN ? AND ?)
            //                 OR (sttimestamp < ? AND endtimestamp > ?))
            //         ";
            //         $parameters = [$start_date, $end_date,$start_date, $end_date,$start_date, $end_date];
                    
            //         if (!empty($data['usertype']) && $data['usertype'] != "All") {
            //             $sql .= " AND su.usertype = ?";
            //             $parameters[] = $data['usertype'];
            //         }

            //         $sql .= ") t 
            //         ON t.deviceid = mda.deviceid
            //     LEFT JOIN {$this->schema}.tbl_device_schedule_updated su 
            //         ON su.imeino = md.serial_no ";

                    
            //         if (!empty($data['usertype']) && $data['usertype'] != "All") {
            //              $sql .= " AND su.usertype = ?";
            //              $parameters[] = $data['usertype'];
            //          }

            //         $sql .= " WHERE 
            //             ul.group_id = 2
            //             AND mda.active = 1 ";
                        
            //         if (!empty($data['usertype']) && $data['usertype'] != "All") {
            //             $sql .= " AND su.usertype = ?";
            //             $parameters[] = $data['usertype'];
            //         }



            //         if($user_id !='All'){
            //                 $sql .= " AND mda.user_id = ".$user_id;
            //         }
                       

                  


            //         $sql .= "),
            //         device_status AS (
            //             SELECT 
            //                 organisation,
            //                 user_id,
            //                 parent_organisation,
            //                 allocation_status,
            //                 device_imei,
            //                 device_name,
            //                 MAX(CASE WHEN trip_imeino IS NOT NULL THEN 1 ELSE 0 END) AS has_trip,
            //                 MAX(CASE WHEN trip_status = 'Patroling Completed' THEN 1 ELSE 0 END) AS covered,
            //                 MAX(COALESCE(distance_travelled, 0)) AS expected_distance ,
            //                 SUM(COALESCE(totaldistancetravel, 0)) AS actual_distance
            //             FROM base_data
            //             GROUP BY organisation, user_id, parent_organisation, allocation_status, device_imei, device_name
            //         )
            //         SELECT 
            //             organisation,
            //             user_id,
            //             parent_organisation,
            //             COUNT(CASE WHEN allocation_status = 'Not Allocated' THEN 1 END) AS not_allocated_count,
            //             string_agg(
            //                 CASE WHEN allocation_status = 'Not Allocated' THEN device_imei END, ',' 
            //                 ORDER BY device_imei
            //             ) AS not_allocated_imeino,
            //             string_agg(
            //                 CASE WHEN allocation_status = 'Not Allocated' THEN device_name END, ',' 
            //                 ORDER BY device_imei
            //             ) AS not_allocated_devicename,
            //             COUNT(CASE 
            //                 WHEN allocation_status = 'Allocated' 
            //                 AND has_trip = 0 
            //                 THEN 1 
            //             END) AS device_off_count,
            //             string_agg(
            //                 CASE 
            //                     WHEN allocation_status = 'Allocated' 
            //                     AND has_trip = 0 
            //                     THEN device_imei 
            //                 END, ',' 
            //                 ORDER BY device_imei
            //             ) AS device_off_imeino,
            //             string_agg(
            //                 CASE 
            //                     WHEN allocation_status = 'Allocated' 
            //                     AND has_trip = 0 
            //                     THEN device_name 
            //                 END, ',' 
            //                 ORDER BY device_imei
            //             ) AS device_off_devicename,
            //             COUNT(CASE 
            //                 WHEN allocation_status = 'Allocated' 
            //                 AND has_trip = 1 
            //                 AND (covered = 1 OR (covered = 0 AND actual_distance >= expected_distance)) 
            //                 THEN 1 
            //             END) AS beats_covered_count,

            //             string_agg(
            //                 CASE 
            //                   WHEN allocation_status = 'Allocated'
            //                    AND has_trip = 1 
            //                    AND (covered = 1 OR (covered = 0 AND actual_distance >= expected_distance)) 
            //                   THEN device_imei 
            //                 END, ',' 
            //                 ORDER BY device_imei
            //               ) AS beats_covered_imeino,
            //               string_agg(
            //                 CASE 
            //                   WHEN allocation_status = 'Allocated'
            //                    AND has_trip = 1 
            //                    AND (covered = 1 OR (covered = 0 AND actual_distance >= expected_distance)) 
            //                   THEN device_name 
            //                 END, ',' 
            //                 ORDER BY device_imei
            //               ) AS beats_covered_devicename,
                        
            //             COUNT(CASE 
            //                 WHEN allocation_status = 'Allocated' 
            //                 AND has_trip = 1 
            //                 AND covered = 0 
            //                 AND actual_distance < expected_distance 
            //                 THEN 1 
            //             END) AS beats_not_covered_count,
            //             string_agg(
            //                 CASE 
            //                   WHEN allocation_status = 'Allocated'
            //                    AND has_trip = 1 
            //                    AND covered = 0 
            //                    AND actual_distance < expected_distance 
            //                   THEN device_imei 
            //                 END, ',' 
            //                 ORDER BY device_imei
            //               ) AS beats_not_covered_imeino,
            //               string_agg(
            //                 CASE 
            //                   WHEN allocation_status = 'Allocated'
            //                    AND has_trip = 1 
            //                    AND covered = 0 
            //                    AND actual_distance < expected_distance 
            //                   THEN device_name 
            //                 END, ',' 
            //                 ORDER BY device_imei
            //               ) AS beats_not_covered_devicename
            //         FROM device_status
            //         GROUP BY organisation, user_id, parent_organisation; ";



            /* ----- new table structure ----- */
            $sql = "WITH base_data AS (
                SELECT 
                    ul.organisation,
                    ul.user_id,
                    p_ul.organisation AS parent_organisation,
                    md.serial_no AS device_imei,
                    msd.device_name AS device_name,
                    CASE 
                        WHEN lower(msd.device_name) LIKE '%stock%' 
                             OR msd.device_name IS NULL 
                             OR trim(msd.device_name) = '' 
                        THEN 'Not Allocated'
                        ELSE 'Allocated'
                    END AS allocation_status,
                    t.imeino AS trip_imeino,
                    tsd.expected_distance AS distance_travelled,
                    t.totaldistancetravel,
                    CASE 
                        WHEN (
                                TRIM(tsd.expected_stpole) = TRIM(t.stpole)
                                OR TRIM(tsd.expected_stpole) = ANY (
                                    ARRAY (
                                        SELECT trim(elem)
                                        FROM unnest(string_to_array(t.beats_covered, ',')) AS elem
                                    )
                                )
                             )
                             AND (
                                TRIM(tsd.expected_endpole) = TRIM(t.endpole)
                                OR TRIM(tsd.expected_endpole) = ANY (
                                    ARRAY (
                                        SELECT trim(elem)
                                        FROM unnest(string_to_array(t.beats_covered, ',')) AS elem
                                    )
                                )
                             )
                        THEN 'Patroling Completed'
                        ELSE NULL
                    END AS trip_status
                FROM public.master_device_assign mda
                LEFT JOIN public.user_login ul 
                    ON ul.user_id = mda.user_id
                LEFT JOIN public.master_device_details md 
                    ON md.id = mda.deviceid
                LEFT JOIN {$this->schema}.master_device_setup msd 
                    ON msd.deviceid = mda.deviceid
                LEFT JOIN public.user_login p_ul 
                    ON p_ul.user_id = mda.parent_id
                LEFT JOIN (
                    SELECT {$this->schema}.tbl_trip.* 
                    FROM {$this->schema}.tbl_trip 
                    LEFT JOIN {$this->schema}.trip_schedule ts 
                        ON ts.deviceid = {$this->schema}.tbl_trip.deviceid
                    WHERE ( (sttimestamp BETWEEN ? AND ?)
                            OR (endtimestamp   BETWEEN ? AND ?)
                            OR (sttimestamp < ? AND endtimestamp > ?))
                    ";
                    $parameters = [$start_date, $end_date,$start_date, $end_date,$start_date, $end_date];
                    
                    if (!empty($data['usertype']) && $data['usertype'] != "All") {
                        $sql .= " AND ts.device_type = ?";
                        $parameters[] = $data['usertype'];
                    }

                    $sql .= ") t 
                    ON t.deviceid = mda.deviceid
                LEFT JOIN {$this->schema}.trip_schedule_details tsd 
                    ON tsd.schedule_details_id = t.schedule_details_id 
                LEFT JOIN {$this->schema}.trip_schedule ts
                    ON ts.schedule_id = tsd.schedule_id";

                    
                    if (!empty($data['usertype']) && $data['usertype'] != "All") {
                         $sql .= " AND ts.device_type = ?";
                         $parameters[] = $data['usertype'];
                     }

                    $sql .= " WHERE 
                        ul.group_id = 2
                        AND mda.active = 1 ";
                        
                    if (!empty($data['usertype']) && $data['usertype'] != "All") {
                        $sql .= " AND ts.device_type = ?";
                        $parameters[] = $data['usertype'];
                    }



                    if($user_id !='All'){
                            $sql .= " AND mda.user_id = ".$user_id;
                    }
                       

                  


                    $sql .= "),
                    device_status AS (
                        SELECT 
                            organisation,
                            user_id,
                            parent_organisation,
                            allocation_status,
                            device_imei,
                            device_name,
                            MAX(CASE WHEN trip_imeino IS NOT NULL THEN 1 ELSE 0 END) AS has_trip,
                            MAX(CASE WHEN trip_status = 'Patroling Completed' THEN 1 ELSE 0 END) AS covered,
                            COALESCE(distance_travelled, 0) AS expected_distance ,
                            COALESCE(totaldistancetravel, 0) AS actual_distance
                        FROM base_data
                        GROUP BY organisation, user_id, parent_organisation, allocation_status, device_imei, device_name, distance_travelled, totaldistancetravel
                    )
                    SELECT 
                        organisation,
                        user_id,
                        parent_organisation,
                        COUNT(CASE WHEN allocation_status = 'Not Allocated' THEN 1 END) AS not_allocated_count,
                        string_agg(
                            CASE WHEN allocation_status = 'Not Allocated' THEN device_imei END, ',' 
                            ORDER BY device_imei
                        ) AS not_allocated_imeino,
                        string_agg(
                            CASE WHEN allocation_status = 'Not Allocated' THEN device_name END, ',' 
                            ORDER BY device_imei
                        ) AS not_allocated_devicename,
                        COUNT(CASE 
                            WHEN allocation_status = 'Allocated' 
                            AND has_trip = 0 
                            THEN 1 
                        END) AS device_off_count,
                        string_agg(
                            CASE 
                                WHEN allocation_status = 'Allocated' 
                                AND has_trip = 0 
                                THEN device_imei 
                            END, ',' 
                            ORDER BY device_imei
                        ) AS device_off_imeino,
                        string_agg(
                            CASE 
                                WHEN allocation_status = 'Allocated' 
                                AND has_trip = 0 
                                THEN device_name 
                            END, ',' 
                            ORDER BY device_imei
                        ) AS device_off_devicename,
                        COUNT(CASE 
                            WHEN allocation_status = 'Allocated' 
                            AND has_trip = 1 
                            AND (covered = 1 OR (covered = 0 AND actual_distance >= expected_distance)) 
                            THEN 1 
                        END) AS beats_covered_count,

                        string_agg(
                            CASE 
                              WHEN allocation_status = 'Allocated'
                               AND has_trip = 1 
                               AND (covered = 1 OR (covered = 0 AND actual_distance >= expected_distance)) 
                              THEN device_imei 
                            END, ',' 
                            ORDER BY device_imei
                          ) AS beats_covered_imeino,
                          string_agg(
                            CASE 
                              WHEN allocation_status = 'Allocated'
                               AND has_trip = 1 
                               AND (covered = 1 OR (covered = 0 AND actual_distance >= expected_distance)) 
                              THEN device_name 
                            END, ',' 
                            ORDER BY device_imei
                          ) AS beats_covered_devicename,
                        
                        COUNT(CASE 
                            WHEN allocation_status = 'Allocated' 
                            AND has_trip = 1 
                            AND covered = 0 
                            AND actual_distance < expected_distance 
                            THEN 1 
                        END) AS beats_not_covered_count,
                        string_agg(
                            CASE 
                              WHEN allocation_status = 'Allocated'
                               AND has_trip = 1 
                               AND covered = 0 
                               AND actual_distance < expected_distance 
                              THEN device_imei 
                            END, ',' 
                            ORDER BY device_imei
                          ) AS beats_not_covered_imeino,
                          string_agg(
                            CASE 
                              WHEN allocation_status = 'Allocated'
                               AND has_trip = 1 
                               AND covered = 0 
                               AND actual_distance < expected_distance 
                              THEN device_name 
                            END, ',' 
                            ORDER BY device_imei
                          ) AS beats_not_covered_devicename
                    FROM device_status
                    GROUP BY organisation, user_id, parent_organisation; ";
            
            $result = $this->db->query($sql, $parameters)->getResult();
          

            $newresult = [];

            if (!empty($result) && is_array($result) && isset($result[0]->organisation)) {

                foreach ($result as $j=>$entry) {
                    if($this->request->getUri()->getSegment(7) == 'offDevice') {
                        $deviceIMEINO = explode(',',$result[$j]->device_off_imeino);
                        $deviceOffName = explode(',',$result[$j]->device_off_devicename);
                        //echo "<pre>";
                        //print_r($deviceOffName);
                        foreach($deviceIMEINO as $i=>$data1) {
                            if($data1){
                                $newresult[] = [
                                    'device_no' => $data1,
                                    'pwi' => $result[$j]->parent_organisation,
                                    'section_name' => $result[$j]->organisation,
                                    'start_date' => $start_date,
                                    'end_date' => $end_date,
                                    'devicename' => $deviceOffName[$i]
                                ];
                            }
                            
                        }

                    } else if($this->request->getUri()->getSegment(7) == 'beatCovered') {
                        $deviceIMEINOCovered = explode(',',$result[$j]->beats_covered_imeino);
                        $deviceCoveredName = explode(',',$result[$j]->beats_covered_devicename);
                        foreach($deviceIMEINOCovered as $i=>$data1) {
                            if($data1){
                                $newresult[] = [
                                    'device_no' => $data1,
                                    'pwi' => $result[$j]->parent_organisation,
                                    'section_name' => $result[$j]->organisation,
                                    'start_date' => $start_date,
                                    'end_date' => $end_date,
                                    'devicename' => $deviceCoveredName[$i]
                                ];
                            }
                        }
                        
                    } else if($this->request->getUri()->getSegment(7) == 'beatNotCovered') {
                        $deviceIMEINONotCovered = explode(',',$result[$j]->beats_not_covered_imeino);
                        $deviceNotCoveredName = explode(',',$result[$j]->beats_not_covered_devicename);
                        foreach($deviceIMEINONotCovered as $i=>$data1) {
                            if($data1){
                                $newresult[] = [
                                    'device_no' => $data1,
                                    'pwi' => $result[$j]->parent_organisation,
                                    'section_name' => $result[$j]->organisation,
                                    'start_date' => $start_date,
                                    'end_date' => $end_date,
                                    'devicename' => $deviceNotCoveredName[$i]
                                ];
                            }
                        }
                    } else if($this->request->getUri()->getSegment(7) == 'notallocated') {
                        $deviceIMEINONotAllocated = explode(',',$result[$j]->not_allocated_imeino);
                        $deviceNotAllocatedName = explode(',',$result[$j]->not_allocated_devicename);
                        foreach($deviceIMEINONotAllocated as $i=>$data1) {
                            if($data1){
                                $newresult[] = [
                                    'device_no' => $data1,
                                    'pwi' => $result[$j]->parent_organisation,
                                    'section_name' => $result[$j]->organisation,
                                    'start_date' => $start_date,
                                    'end_date' => $end_date,
                                    'devicename' => $deviceNotAllocatedName[$i] ?? null
                                ];
                            }
                        }
                    }else if($this->request->getUri()->getSegment(7) == 'alltypes') {
                        $deviceIMEINONotAllocated = explode(',',$result[$j]->not_allocated_imeino);
                        $deviceNotAllocatedName = explode(',',$result[$j]->not_allocated_devicename);
                        foreach($deviceIMEINONotAllocated as $i=>$data1) {
                            if($data1){
                                $newresult[] = [
                                    'device_no' => $data1,
                                    'pwi' => $result[$j]->parent_organisation,
                                    'section_name' => $result[$j]->organisation,
                                    'start_date' => $start_date,
                                    'end_date' => $end_date,
                                    'devicename' => $deviceNotAllocatedName[$i] ?? null
                                ];
                            }
                        }

                        $deviceIMEINOCovered = explode(',',$result[$j]->beats_covered_imeino);
                        $deviceCoveredName = explode(',',$result[$j]->beats_covered_devicename);
                        foreach($deviceIMEINOCovered as $i=>$data1) {
                            if($data1){
                                $newresult[] = [
                                    'device_no' => $data1,
                                    'pwi' => $result[$j]->parent_organisation,
                                    'section_name' => $result[$j]->organisation,
                                    'start_date' => $start_date,
                                    'end_date' => $end_date,
                                    'devicename' => $deviceCoveredName[$i]
                                ];
                            }
                        }

                        $deviceIMEINO = explode(',',$result[$j]->device_off_imeino);
                        $deviceOffName = explode(',',$result[$j]->device_off_devicename);
                        //echo "<pre>";
                        //print_r($deviceOffName);
                        foreach($deviceIMEINO as $i=>$data1) {
                            if($data1){
                                $newresult[] = [
                                    'device_no' => $data1,
                                    'pwi' => $result[$j]->parent_organisation,
                                    'section_name' => $result[$j]->organisation,
                                    'start_date' => $start_date,
                                    'end_date' => $end_date,
                                    'devicename' => $deviceOffName[$i]
                                ];
                            }
                            
                        }

                        $deviceIMEINONotCovered = explode(',',$result[$j]->beats_not_covered_imeino);
                        $deviceNotCoveredName = explode(',',$result[$j]->beats_not_covered_devicename);
                        foreach($deviceIMEINONotCovered as $i=>$data1) {
                            if($data1){
                                $newresult[] = [
                                    'device_no' => $data1,
                                    'pwi' => $result[$j]->parent_organisation,
                                    'section_name' => $result[$j]->organisation,
                                    'start_date' => $start_date,
                                    'end_date' => $end_date,
                                    'devicename' => $deviceNotCoveredName[$i]
                                ];
                            }
                        }


                    }
                }

            }
                
            // echo "<pre>";print_r($newresult);exit();
            $data['alldata'] = $newresult;

            $dat[0]['A'] = "Trip Summary Device Details Report ". $type ." From Date " . date("d-m-Y H:i:s", strtotime($start_date)) . " To " . date("d-m-Y H:i:s", strtotime($end_date));

            // Initialize header row
            $dat[1]['A'] = "SL No.";           
            $dat[1]['B'] = "Device No";
            $dat[1]['C'] = "PWI";
            $dat[1]['D'] = "Section Name";
            $dat[1]['E'] = "Device Name";
            
            // Initialize counters
            $Key = 1;
    
            foreach ($newresult as $irow) {
    
                // Fill data for each row
                $dat[$Key + 1]['A'] = $Key;
                $dat[$Key + 1]['B'] = (string)$irow['device_no'];
                $dat[$Key + 1]['C'] = $irow['pwi'];
                $dat[$Key + 1]['D'] = $irow['section_name'];
                $dat[$Key + 1]['E'] = $irow['devicename'];
    
                $Key++;
            }
            
            // Create the Excel file
            $filename = 'Trip_Summary_Device_Details_Report_'. $type.'_'. date("d-m-Y H:i", strtotime($start_date)) . '_To_' . date("d-m-Y H:i", strtotime($end_date)) . '.xlsx';
            exceldownload($dat, $filename);
    
    }

    public function activitySummaryReport1()
    { 
        // Check if user is logged in
        if (!session()->has('login_sess_data')) {
            return redirect()->to('/');
        }

        $data = [];
        $data['sessdata'] = $this->sessdata;
        $data['page_title'] = "Activity Summary Report";
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
                {$this->schema}.tbl_trip.deviceid, 
                {$this->schema}.tbl_trip.imeino, 
                ul.organisation,                 -- ✅ Added organisation
                p_ul.organisation AS parent_organisation,  -- ✅ Added parent_organisation
                {$this->schema}.tbl_trip.stpole as actual_stpole, 
                {$this->schema}.tbl_trip.endpole as actual_endpole, 
                ts.device_type as usertype,
                trip_schedule_details.expected_distance as distance_travelled,
                sttimestamp as actual_starttime, 
                endtimestamp as actual_endtime, 
                totaldistancetravel, timetravelled, startbattery, endbattery, beats_covered ,
                devicename,
                {$this->schema}.trip_schedule_details.expected_stpole as expected_stpole, 
                {$this->schema}.trip_schedule_details.expected_endpole as expected_endpole,  
         
                {$this->schema}.trip_schedule_details.expected_start_datetime as expected_starttime, 
                {$this->schema}.trip_schedule_details.expected_end_datetime  as expected_endtime
                FROM {$this->schema}.tbl_trip
                LEFT JOIN {$this->schema}.trip_schedule_details 
                 ON {$this->schema}.trip_schedule_details.schedule_details_id = {$this->schema}.tbl_trip.schedule_details_id
                LEFT JOIN {$this->schema}.trip_schedule  ts  
                 ON ts.schedule_id = {$this->schema}.trip_schedule_details.schedule_id
                LEFT JOIN public.master_device_assign mda 
                    ON mda.deviceid = {$this->schema}.tbl_trip.deviceid  -- ✅ Join with `master_device_assign` to get user_id
                LEFT JOIN public.user_login ul 
                    ON ul.user_id = mda.user_id   -- ✅ Get `organisation`
                LEFT JOIN public.user_login p_ul 
                    ON p_ul.user_id = mda.parent_id  -- ✅ Get `parent_organisation`


                WHERE 
                (
                        (sttimestamp BETWEEN ? AND ?)
                    OR  (endtimestamp   BETWEEN ? AND ?) 
                    OR (sttimestamp < ? AND endtimestamp > ?) 
                ) 
                AND ul.group_id = 2
                AND mda.active = 1 ";

                $parameters = [$start_date, $end_date,$start_date, $end_date,$start_date, $end_date];

                // Apply usertype filter only if it is NOT "All"
                if (!empty($data['usertype']) && $data['usertype'] !== "All") {
                    $sql .= " AND device_type = ?";
                    $parameters[] = $data['usertype'];
                }

                if (!empty($data['sse_pwy']) && $data['sse_pwy'] != "All") {
                    $sql .= " AND p_ul.user_id = ?";
                    $parameters[] = $data['sse_pwy'];
                }

                if (!empty($data['pwi_id']) && $data['pwi_id'] != "All") {
                    $sql .= " AND ul.user_id = ?";
                    $parameters[] = $data['pwi_id'];
                }

                if (!empty($data['device_id'])) {
                    $sql .= " AND {$this->schema}.tbl_trip.deviceid = ?";
                    $parameters[] = $data['device_id'];
                }

            $sql .= " ORDER BY {$this->schema}.tbl_trip.imeino, sttimestamp ASC";

            // echo $sql;exit();

            $result = $this->db->query($sql, $parameters)->getResult();


            $data['alldata'] = $result;
        }

        $data['sse_pwy'] = trim($this->request->getPost('pway_id'));
        $data['pwi_id'] = trim($this->request->getPost('user'));
        $data['pwi_name'] = trim($this->request->getPost('pwi_name'));
        $data['device_id'] = $device_id = trim($this->request->getPost('device_id'));

        $data['middle'] = view('traxreport/activitysummeryreport1', $data);
        return view('mainlayout', $data);
    }

    public function tripSummaryReport()
    { 
        // Check if user is logged in
        if (!session()->has('login_sess_data')) {
            return redirect()->to('/');
        }

        $data = [];
        $data['sessdata'] = $this->sessdata;
        $data['page_title'] = "Trip Summary Report";
        $data['date_from'] = '';
        $data['usertype'] = 'All';

        if ($this->request->getMethod() == 'POST') {
            $date = date("Y-m-d", strtotime(trim($this->request->getPost('dt'))));
            $data['dtt'] = date("d-m-Y", strtotime(trim($this->request->getPost('dt'))));

            $data['usertype'] = $usertype = $this->request->getPost('usertype');

            // $result = $this->db->query("SELECT * 
            //     FROM public.tbl_daily_report_snapshot drs
            //     WHERE dt = '$date' AND usertype = '$usertype' ")->getResult();
            // print_r($result); die();

            $loggedInUserId = $this->sessdata['user_id']; 
            $sql = "
                WITH RECURSIVE sub_users AS (
                    SELECT user_id, organisation as pway FROM public.user_login WHERE user_id = ?
                    UNION
                    SELECT ul.user_id, ul.organisation as pway 
                    FROM public.user_login ul
                    INNER JOIN sub_users su ON ul.parent_id = su.user_id
                )
                SELECT drs.*
                FROM public.tbl_daily_report_snapshot drs
                WHERE drs.pway IN (SELECT pway FROM sub_users) AND dt = '$date' AND usertype = '$usertype'
                ORDER BY drs.dt DESC;
            ";
            $result = $this->db->query($sql, [$loggedInUserId])->getResult();

            $data['alldata'] = $result;
        }

        $data['middle'] = view('traxreport/tripsummeryreport', $data);
        return view('mainlayout', $data);
    }

    public function tripSummaryReportExcel()
    { 
        // Check if user is logged in
        if (!session()->has('login_sess_data')) {
            return redirect()->to('/');
        }

        $data = [];
        $data['sessdata'] = $this->sessdata;
        $data['page_title'] = "Trip Summary Report";
        $data['date_from'] = '';
        $data['usertype'] = 'All';

        if ($this->request->getMethod() == 'POST') {
            $date = date("Y-m-d", strtotime(trim($this->request->getPost('dt'))));
            $data['dtt'] = date("d-m-Y", strtotime(trim($this->request->getPost('dt'))));

            $data['usertype'] = $usertype = $this->request->getPost('usertype');

            // $newresult = $this->db->query("SELECT * 
            //     FROM public.tbl_daily_report_snapshot drs
            //     WHERE dt = '$date' AND usertype = '$usertype' ")->getResult();

            $loggedInUserId = $this->sessdata['user_id']; 
            $sql = "
                WITH RECURSIVE sub_users AS (
                    SELECT user_id, organisation as pway FROM public.user_login WHERE user_id = ?
                    UNION
                    SELECT ul.user_id, ul.organisation as pway 
                    FROM public.user_login ul
                    INNER JOIN sub_users su ON ul.parent_id = su.user_id
                )
                SELECT drs.*
                FROM public.tbl_daily_report_snapshot drs
                WHERE drs.pway IN (SELECT pway FROM sub_users) AND dt = '$date' AND usertype = '$usertype'
                ORDER BY drs.dt DESC;
            ";
            $newresult = $this->db->query($sql, [$loggedInUserId])->getResult();

            $data['alldata'] = $newresult;
        }

        $dat[0]['A'] = $newresult[0]->report_name;
        $dat[1]['A'] = " Generated On - " . $data['dtt'];

        // Initialize header row
        $dat[2]['A'] = "SL No.";           
        $dat[2]['B'] = "Time";
        $dat[2]['C'] = "SSE/PWY";
        $dat[2]['D'] = "Section Name";
        $dat[2]['E'] = "Off Device";
        $dat[2]['F'] = "Beat Covered";
        $dat[2]['G'] = "Beat Not Covered";
        $dat[2]['H'] = "Over Speed";
        
        // Initialize counters
        $Key = 2;
        $i = 1;

        foreach ($newresult as $irow) {

            // Fill data for each row
            $dat[$Key + 1]['A'] = $i;
            $dat[$Key + 1]['B'] = $irow->dttime;
            $dat[$Key + 1]['C'] = $irow->pway;
            $dat[$Key + 1]['D'] = $irow->section;
            $dat[$Key + 1]['E'] = $irow->device_off;
            $dat[$Key + 1]['F'] = $irow->beats_covered;
            $dat[$Key + 1]['G'] = $irow->beats_not_covered;
            $dat[$Key + 1]['H'] = $irow->overspeed;

            $Key++;
            $i++;
        }
        
        // Create the Excel file
        $filename = $newresult[0]->report_name . "_Generated On_" . $data['dtt'] . '.xlsx';
        exceldownload($dat, $filename);
    }

    public function devicePerformanceReport()
    { 
        // Check if user is logged in
        if (!session()->has('login_sess_data')) {
            return redirect()->to('/');
        }

        $data = [];
        $data['sessdata'] = $this->sessdata;
        $data['page_title'] = "Device Performence Report";
        $data['date_from'] = '';
        $data['usertype'] = 'All';

        $user_id = $this->sessdata['user_id'];
        $subUsers = $this->getSubUsers($user_id, $this->db);
        $allowedUsers = array_merge([$user_id], $subUsers);
        $placeholders = implode(',', array_fill(0, count($allowedUsers), '?'));

        // $data['devicedropdown'] = $this->db->query("SELECT a.*, 
        // (SELECT device_name FROM {$this->schema}.master_device_setup  
        //     WHERE id = (SELECT max(id) 
        //                 FROM {$this->schema}.master_device_setup 
        //                 WHERE inserttime::date <= current_date::date  
        //                 AND deviceid = a.did)) 
        // AS device_name 
        // FROM public.get_divice_details_record_for_list_for_company('{$this->schema}',{$this->sessdata['user_id']}) AS a 
        // WHERE a.group_id = 2 AND a.active = 1")->getResult();

        if ($this->sessdata['group_id'] == 3) {
            // Distributor logic
            $data['devicedropdown'] = $this->db->query("SELECT a.*, 
                (SELECT device_name FROM {$this->schema}.master_device_setup  
                WHERE id = (SELECT max(id) 
                            FROM {$this->schema}.master_device_setup 
                            WHERE inserttime::date <= current_date::date  
                            AND deviceid = a.did)) 
                AS device_name 
                FROM public.get_divice_details_record_for_list_for_company('{$this->schema}',{$this->sessdata['user_id']}) AS a 
                WHERE a.group_id = 2 AND a.active = 1")->getResult();
        } else {
            
            $data['devicedropdown'] = $this->db->query("SELECT a.*, 
                (SELECT device_name FROM {$this->schema}.master_device_setup  
                WHERE id = (SELECT max(id) 
                            FROM {$this->schema}.master_device_setup 
                            WHERE inserttime::date <= current_date::date  
                            AND deviceid = a.did)) 
                AS device_name 
                FROM public.get_divice_details_record_for_list_for_company('{$this->schema}',{$this->sessdata['user_id']}) AS a 
                WHERE a.group_id = 2 AND a.active = 1 AND a.user_id IN ($placeholders)", $allowedUsers)->getResult();
        }


        $data['pway'] = $this->db->query("SELECT organisation, user_id 
                        FROM public.user_login 
                        WHERE active = 1 AND group_id = 8 AND user_id IN ($placeholders)", $allowedUsers)->getResult();

        // Get users
        $data['usersdd'] = $this->commonModel->get_users();

        if ($this->request->getMethod() == 'POST') {
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
                {$this->schema}.tbl_trip.deviceid, 
                {$this->schema}.tbl_trip.imeino, 
                ul.organisation,                 -- ✅ Added organisation
                p_ul.organisation AS parent_organisation,  -- ✅ Added parent_organisation
                {$this->schema}.tbl_trip.stpole as actual_stpole, 
                {$this->schema}.tbl_trip.endpole as actual_endpole, 
                ts.device_type as usertype,
                trip_schedule_details.expected_distance as distance_travelled,
                sttimestamp as actual_starttime, 
                endtimestamp as actual_endtime, 
                totaldistancetravel, timetravelled, startbattery, endbattery, beats_covered ,
                devicename,
                {$this->schema}.trip_schedule_details.expected_stpole as expected_stpole, 
                {$this->schema}.trip_schedule_details.expected_endpole as expected_endpole, 
         
                {$this->schema}.trip_schedule_details.expected_start_datetime as expected_starttime, 
                {$this->schema}.trip_schedule_details.expected_end_datetime  as expected_endtime
                FROM {$this->schema}.tbl_trip
                LEFT JOIN {$this->schema}.trip_schedule_details 
                 ON {$this->schema}.trip_schedule_details.schedule_details_id = {$this->schema}.tbl_trip.schedule_details_id
                LEFT JOIN {$this->schema}.trip_schedule  ts  
                 ON ts.schedule_id = {$this->schema}.trip_schedule_details.schedule_id  
                LEFT JOIN public.master_device_assign mda 
                    ON mda.deviceid = {$this->schema}.tbl_trip.deviceid  -- ✅ Join with `master_device_assign` to get user_id
                LEFT JOIN public.user_login ul 
                    ON ul.user_id = mda.user_id   -- ✅ Get `organisation`
                LEFT JOIN public.user_login p_ul 
                    ON p_ul.user_id = mda.parent_id  -- ✅ Get `parent_organisation`


                WHERE 
                (
                        (sttimestamp BETWEEN ? AND ?)
                    OR  (endtimestamp   BETWEEN ? AND ?) 
                    OR (sttimestamp < ? AND endtimestamp > ?) 
                ) 
                AND ul.group_id = 2
                AND mda.active = 1 ";

                $parameters = [$start_date, $end_date,$start_date, $end_date,$start_date, $end_date];

                // Apply usertype filter only if it is NOT "All"
                if (!empty($data['usertype']) && $data['usertype'] !== "All") {
                    $sql .= " AND usertype = ?";
                    $parameters[] = $data['usertype'];
                }

                if (!empty($data['sse_pwy']) && $data['sse_pwy'] != "All") {
                    $sql .= " AND p_ul.user_id = ?";
                    $parameters[] = $data['sse_pwy'];
                }

                if (!empty($data['pwi_id']) && $data['pwi_id'] != "All") {
                    $sql .= " AND ul.user_id = ?";
                    $parameters[] = $data['pwi_id'];
                }

                if (!empty($data['device_id'])) {
                    $sql .= " AND {$this->schema}.tbl_trip.deviceid = ?";
                    $parameters[] = $data['device_id'];
                }

            $sql .= " ORDER BY {$this->schema}.tbl_trip.imeino, sttimestamp ASC";

            // echo $sql;exit();

            $result = $this->db->query($sql, $parameters)->getResult();


            $data['alldata'] = $result;
        }

        $data['sse_pwy'] = trim($this->request->getPost('pway_id'));
        $data['pwi_id'] = trim($this->request->getPost('user'));
        $data['pwi_name'] = trim($this->request->getPost('pwi_name'));
        $data['device_id'] = $device_id = trim($this->request->getPost('device_id'));

        $data['middle'] = view('traxreport/deviceperformancereport', $data);
        return view('mainlayout', $data);
    }

    function getSubUsers($user_id, $db) {
        $subUsers = [];
    
        $sql = "WITH RECURSIVE sub_users AS (
                    SELECT user_id FROM public.user_login WHERE parent_id = ?
                    UNION
                    SELECT ul.user_id FROM public.user_login ul
                    INNER JOIN sub_users su ON ul.parent_id = su.user_id
                )
                SELECT user_id FROM sub_users";
    
        $query = $db->query($sql, [$user_id])->getResult();
    
        foreach ($query as $row) {
            $subUsers[] = $row->user_id;
        }
    
        return $subUsers;
    }
}
