<?php

namespace App\Controllers;

use App\Models\TripScheduleModel;
use App\Models\TripScheduleDetailsModel;
use CodeIgniter\Controller;
use App\Libraries\MakePDF;

class DeviceStatusController extends Controller
{
    protected $sessdata;
    protected $schema;

    public function __construct()
    {
        // Load helpers
        helper(['form', 'url', 'master', 'communication']);
        helper('master_helper');

        // Connect to Database
        $this->db = \Config\Database::connect();

        // Check for session data
        if (session()->get('login_sess_data')) {
            $this->sessdata = session()->get('login_sess_data');
            $this->schema = $this->sessdata['schemaname']; // Get schema from session
        }

        // Load models
        $this->reportModel = new \App\Models\ReportModel();
        $this->mobilesModel = new \App\Models\MobilesModel();
        $this->commonModel = new \App\Models\CommonModel();
        
        // Load libraries
        $this->excel = service('excel');
    }


    public function index()
    {
        if (!session()->has('login_sess_data')) {
            return redirect()->to('/');
        }
        $db = \Config\Database::connect();

        $data = [];
        $data['sessdata'] = $this->sessdata;
        $data['page_title'] = "Device Details Report";
        $data['usersdd'] = $this->commonModel->get_users();

        // Get input parameters
        $deviceId = $deviceid = $this->request->getGet('deviceid');
        $startDate = $this->request->getGet('start_date');
        $endDate = $this->request->getGet('end_date');
        $data['device_id'] = $deviceid = $device_id = trim($this->request->getGet('device_id'));
        $data['stdt'] = $from_datetime = $this->request->getGet('stdt') ? date("Y-m-d H:i:s", strtotime($this->request->getGet('stdt'))) : null;
        $data['endt'] = $to_datetime = $this->request->getGet('endt') ? date("Y-m-d H:i:s", strtotime($this->request->getGet('endt'))) : null;

        // Fetch device dropdown
        $data['devicedropdown'] = $this->db->query("SELECT a.*, 
            (SELECT device_name FROM {$this->schema}.master_device_setup  
                WHERE id = (SELECT max(id) 
                            FROM {$this->schema}.master_device_setup 
                            WHERE inserttime::date <= current_date::date  
                            AND deviceid = a.did)) 
            AS device_name 
            FROM public.get_divice_details_record_for_list_for_company('{$this->schema}',{$this->sessdata['user_id']}) AS a 
            WHERE a.group_id = 2 AND a.active = 1")->getResult();

        // Handle download functionality
        if ($this->request->getGet('download') == 'xlsx' || $this->request->getGet('download') == 'pdf') {
            if (!empty($deviceid) && !empty($from_datetime) && !empty($to_datetime)) {
                // Fetch all records (ignoring pagination)
                $query = $this->db->query(
                    "SELECT * FROM public.get_positional_record_of_eatch_divice_date_to_date(?, ?, ?) 
                    ORDER BY currentdate DESC, currenttime DESC",
                    [$deviceid, $from_datetime, $to_datetime]
                );
                $allRecords = $query->getResult();

                if ($this->request->getGet('download') == 'xlsx') {
                    // Generate XLSX
                    $filename = 'Device_Details_Report_' . date('Y-m-d_H:i:s') . '.xlsx';
                    $dat[0]['A'] = "Device Details Report from $from_datetime to $to_datetime";

                    $dat[1]['A'] = 'Date';
                    $dat[1]['B'] = 'Time';
                    $dat[1]['C'] = 'Latitude';
                    $dat[1]['D'] = 'Longitude';
                    $dat[1]['E'] = 'Speed';
                    $dat[1]['F'] = 'Location';

                    $count = 2;
                    foreach ($allRecords as $record) {
                        $dat[$count]['A'] = $record->currentdate;
                        $dat[$count]['B'] = $record->currenttime;
                        $dat[$count]['C'] = $record->latitude;
                        $dat[$count]['D'] = $record->longitude;
                        $dat[$count]['E'] = $record->trakerspeed;
                        $dat[$count]['F'] = $record->poleno;
                        $count++;
                    }

                    exceldownload($dat, $filename);
                } elseif ($this->request->getGet('download') == 'pdf') {
                    // Generate PDF
                    $html = '<html>
                        <head>
                            <style>
                                body { font-family: Arial, sans-serif; font-size: 11px; }
                                h2 { text-align: center; margin-bottom: 5px; }
                                .report-meta { text-align: center; font-size: 10px; margin-bottom: 15px; }
                                table { width: 100%; border-collapse: collapse; }
                                th, td { border: 1px solid black; padding: 6px; text-align: center; }
                                th { background-color: #f2f2f2; }
                            </style>
                        </head>
                        <body>

                        <h2>Device Details Report</h2>
                        <p class="report-meta">Generated On: ' . date("Y-m-d H:i:s") . '</p>

                        <table>
                            <thead>
                                <tr>
                                    <th>Date</th>
                                    <th>Time</th>
                                    <th>Latitude</th>
                                    <th>Longitude</th>
                                    <th>Speed</th>
                                    <th>Location</th>
                                </tr>
                            </thead>
                            <tbody>';

                    foreach ($allRecords as $record) {
                        $html .= '<tr>
                            <td>' . $record->currentdate . '</td>
                            <td>' . $record->currenttime . '</td>
                            <td>' . $record->latitude . '</td>
                            <td>' . $record->longitude . '</td>
                            <td>' . $record->trakerspeed . '</td>
                            <td>' . $record->poleno . '</td>
                        </tr>';
                    }

                    $html .= '</tbody></table></body></html>';

                    $filename = 'Device_Details_Report_' . date('Y-m-d_H:i:s') . '.pdf';

                    try {
                        ini_set('memory_limit', '2048M');
                        $makePDF = new \App\Libraries\MakePDF();
                        $makePDF->setFileName($filename);
                        $makePDF->setContent($html);
                        $makePDF->getPdf(true);
                        return;
                    } catch (\Exception $e) {
                        log_message('error', 'PDF generation failed: ' . $e->getMessage());
                        return redirect()->back()->with('error', 'Failed to generate PDF. Please try again.');
                    }
                }
            } else {
                return redirect()->back()->with('error', 'Please provide valid input parameters to download the report.');
            }
        }

        // Pagination setup
        $perPage = 50; // Number of records per page
        $page = $this->request->getGet('page') ?? 1; // Current page
        $offset = ($page - 1) * $perPage;

        $data['tripSchedules'] = [];
        $data['getcoordinates'] = [];

        if (!empty($deviceid) && !empty($from_datetime) && !empty($to_datetime)) {
            // Query to get coordinates with LIMIT and OFFSET for pagination
            try {
                $query = $this->db->query(
                    "SELECT * FROM public.get_positional_record_of_eatch_divice_date_to_date(?, ?, ?) 
                    ORDER BY currentdate DESC, currenttime DESC 
                    LIMIT ? OFFSET ?",
                    [$deviceid, $from_datetime, $to_datetime, $perPage, $offset]
                );
                $getcoordinates = $query->getResult();

                // Query to count total records for pagination
                $countQuery = $this->db->query(
                    "SELECT COUNT(*) AS total FROM public.get_positional_record_of_eatch_divice_date_to_date(?, ?, ?)",
                    [$deviceid, $from_datetime, $to_datetime]
                );
                $totalRecords = $countQuery->getRow()->total;

                // Initialize pagination
                $pager = \Config\Services::pager();
                $data['pager'] = $pager->makeLinks($page, $perPage, $totalRecords);
            } catch (\Exception $e) {
                log_message('error', "Query failed: " . $e->getMessage());
                throw new \Exception("Failed to execute query: " . $e->getMessage());
            }

            // Process coordinates
            $return_arr = [];
            if (!empty($getcoordinates)) {
                $return_arr = $getcoordinates;
            }

            $data['getcoordinates'] = $return_arr;
        } else {
            $data['getcoordinates'] = [];
            $data['error'] = "Please provide valid input parameters to view the report.";
        }

        $data['request'] = $this->request;
        $data['middle'] = view('devices/device_details_list', $data);
        return view('mainlayout', $data);
    }


    public function deviceStatusList_old()
    {
        $db = \Config\Database::connect();
        $data = [];
        $data['sessdata'] = $this->sessdata;
        $data['page_title'] = "Keyman Report";
        $data['page_titl2'] = "Petrolman Report";
        $data['usersdd'] = $this->commonModel->get_users();

        $perPage = 20; 
        $keymanPage = $this->request->getGet('keyman_page') ?? 1; 
        $patrolmanPage = $this->request->getGet('patrolman_page') ?? 1; 

        // Keyman Devices Query
        $keymanDevices = $this->db->query("
            SELECT DISTINCT ts.deviceid AS device_id
            FROM stes.trip_schedule ts
            LEFT JOIN public.master_device_details md
                ON ts.imeino = md.imei_no
            WHERE ts.device_type = 'Keyman' AND md.id IS NOT NULL
        ")->getResult();

        $deviceIds = array_map(fn($row) => $row->device_id, $keymanDevices);

        if (!empty($deviceIds)) {
            $idString = implode(',', $deviceIds);

            // Get total count for Keyman
            $keymanCountQuery = $this->db->query("
                SELECT COUNT(DISTINCT tp.deviceid) AS total
                FROM {$this->schema}.traker_positionaldata tp
                WHERE tp.deviceid IN ($idString)
            ");
            $keymanTotal = $keymanCountQuery->getRow()->total;

            // Fetch paginated Keyman data
            $offset = ($keymanPage - 1) * $perPage;
            $data['keyman'] = $this->db->query("
                SELECT DISTINCT ON (tp.deviceid)
                    tp.deviceid,
                    md.imei_no,
                    md.serial_no AS devicename,
                    md.mobile_no AS mobileNumber,
                    tp.trakerspeed AS speed,
                    tp.latitude,
                    tp.longitude,
                    tp.currentdate,
                    tp.currenttime,
                    tp.recivestamp,
                    tp.trakerspeed,
                    tp.devicestatus,
                    tp.batterystats,
                    tp.misc,
                    tp.poleno
                FROM {$this->schema}.traker_positionaldata tp
                LEFT JOIN public.master_device_details md ON tp.deviceid = md.id
                WHERE tp.deviceid IN ($idString)
                ORDER BY tp.deviceid, tp.recivestamp DESC
                LIMIT $perPage OFFSET $offset
            ")->getResult();

            // Initialize pagination for Keyman
            $pager = \Config\Services::pager();
            $data['keymanPager'] = $pager->makeLinks($keymanPage, $perPage, $keymanTotal);
        } else {
            $data['keyman'] = [];
            $data['keymanPager'] = '';
        }

        // Patrolman Devices Query
        $patrolmanDevices = $this->db->query("
            SELECT DISTINCT ts.deviceid AS device_id
            FROM stes.trip_schedule ts
            LEFT JOIN public.master_device_details md
                ON ts.imeino = md.imei_no
            WHERE ts.device_type = 'Patrolman' AND md.id IS NOT NULL
        ")->getResult();

        $devicePatrolmanIds = array_map(fn($row) => $row->device_id, $patrolmanDevices);

        if (!empty($devicePatrolmanIds)) {
            $idPetrolmanString = implode(',', $devicePatrolmanIds);

            // Get total count for Patrolman
            $patrolmanCountQuery = $this->db->query("
                SELECT COUNT(DISTINCT tp.deviceid) AS total
                FROM {$this->schema}.traker_positionaldata tp
                WHERE tp.deviceid IN ($idPetrolmanString)
            ");
            $patrolmanTotal = $patrolmanCountQuery->getRow()->total;

            // Fetch paginated Patrolman data
            $offset = ($patrolmanPage - 1) * $perPage;
            $data['petrolman'] = $this->db->query("
                SELECT DISTINCT ON (tp.deviceid)
                    tp.deviceid,
                    md.imei_no,
                    md.serial_no AS devicename,
                    md.mobile_no AS mobileNumber,
                    tp.trakerspeed AS speed,
                    tp.latitude,
                    tp.longitude,
                    tp.currentdate,
                    tp.currenttime,
                    tp.recivestamp,
                    tp.trakerspeed,
                    tp.devicestatus,
                    tp.batterystats,
                    tp.misc
                FROM {$this->schema}.traker_positionaldata tp
                LEFT JOIN public.master_device_details md ON tp.deviceid = md.id
                WHERE tp.deviceid IN ($idPetrolmanString)
                ORDER BY tp.deviceid, tp.recivestamp DESC
                LIMIT $perPage OFFSET $offset
            ")->getResult();

            // Initialize pagination for Patrolman
            $pager = \Config\Services::pager();
            $data['patrolmanPager'] = $pager->makeLinks($patrolmanPage, $perPage, $patrolmanTotal);
        } else {
            $data['petrolman'] = [];
            $data['patrolmanPager'] = '';
        }

        $data['middle'] = view('devices/device_status_list', $data);
        return view('mainlayout', $data);
    }

    
    public function deviceStatusList_old_15052025_1847()
    {   

        $db = \Config\Database::connect();

        $user_id = $this->sessdata['user_id'];
        $subUsers = getSubUsers($user_id, $db);
        $allowedUsers = array_merge([$user_id], $subUsers);
        $placeholders = implode(',', array_fill(0, count($allowedUsers), '?'));


       
        $data = [];
        $data['sessdata'] = $this->sessdata;
        $data['page_title'] = "Keyman Report";
        $data['page_titl2'] = "Patrolman Report";
        $data['usersdd'] = $this->commonModel->get_users();

        // Pagination logic for Keyman and Patrolman (unchanged)
        $perPage = 20; 
        $keymanPage = $this->request->getGet('keyman_page') ?? 1; 
        $patrolmanPage = $this->request->getGet('patrolman_page') ?? 1; 
        $keymanOffset = ($keymanPage - 1) * $perPage;
        $patrolmanOffset = ($patrolmanPage - 1) * $perPage;

        $keymanDevices = $db->query("
            SELECT DISTINCT ts.deviceid AS device_id
            FROM stes.trip_schedule ts
            LEFT JOIN public.master_device_details md ON ts.imeino = md.imei_no
            LEFT JOIN public.master_device_assign mda ON md.id = mda.deviceid
            LEFT JOIN public.user_login ul ON mda.user_id = ul.user_id
            WHERE ts.device_type = 'Keyman' AND md.id IS NOT NULL
            AND mda.user_id IN ($placeholders)
            ORDER BY ts.deviceid
            LIMIT $perPage OFFSET $keymanOffset
        ", $allowedUsers)->getResult();

        $allKeymanDevices = $db->query("
            SELECT DISTINCT ts.deviceid AS device_id
            FROM stes.trip_schedule ts
            LEFT JOIN public.master_device_details md ON ts.imeino = md.imei_no
            LEFT JOIN public.master_device_assign mda ON md.id = mda.deviceid
            LEFT JOIN public.user_login ul ON mda.user_id = ul.user_id
            WHERE ts.device_type = 'Keyman' AND md.id IS NOT NULL
            AND mda.user_id IN ($placeholders)
        ", $allowedUsers)->getResult();

        $deviceIds = array_map(fn($row) => $row->device_id, $keymanDevices);
        $allDeviceIds = array_map(fn($row) => $row->device_id, $allKeymanDevices);

        $patrolmanDevices = $db->query("
            SELECT DISTINCT ts.deviceid AS device_id
            FROM stes.trip_schedule ts
            LEFT JOIN public.master_device_details md ON ts.imeino = md.imei_no
            LEFT JOIN public.master_device_assign mda ON md.id = mda.deviceid
            LEFT JOIN public.user_login ul ON mda.user_id = ul.user_id
            WHERE ts.device_type = 'Patrolman' AND md.id IS NOT NULL
            AND mda.user_id IN ($placeholders)
            ORDER BY ts.deviceid
            LIMIT $perPage OFFSET $patrolmanOffset
        ", $allowedUsers)->getResult();

        $allPatrolmanDevices = $db->query("
            SELECT DISTINCT ts.deviceid AS device_id
            FROM stes.trip_schedule ts
            LEFT JOIN public.master_device_details md ON ts.imeino = md.imei_no
            LEFT JOIN public.master_device_assign mda ON md.id = mda.deviceid
            LEFT JOIN public.user_login ul ON mda.user_id = ul.user_id
            WHERE ts.device_type = 'Patrolman' AND md.id IS NOT NULL
            AND mda.user_id IN ($placeholders)
        ", $allowedUsers)->getResult();

        $devicePatrolmanIds = array_map(fn($row) => $row->device_id, $patrolmanDevices);
        $allDevicePatrolmanIds = array_map(fn($row) => $row->device_id, $allPatrolmanDevices);

        $keymanPager = \Config\Services::pager();
        $data['keymanLinks'] = $keymanPager->makeLinks(
            $keymanPager->getCurrentPage('keyman'),
            10,
            $keymanPager->getPageCount('keyman') * 10,
            'default_full',
            0,
            'keyman_page' // query string variable name
        );

        $patrolmanPager = \Config\Services::pager();
        $data['patrolmanLinks'] = $patrolmanPager->makeLinks(
            $patrolmanPager->getCurrentPage('patrolman'),
            10,
            $patrolmanPager->getPageCount('patrolman') * 10,
            'default_full',
            0,
            'patrolman_page'
        );

        // Handle Download Functionality
        if ($this->request->getGet('download') == 'xlsx' || $this->request->getGet('download') == 'pdf') {
            $type = $this->request->getGet('type'); // 'keyman' or 'patrolman'
            $deviceIdsToFetch = ($type == 'keyman') ? $allDeviceIds : $allDevicePatrolmanIds;

            if (!empty($deviceIdsToFetch)) {
                $idString = implode(',', $deviceIdsToFetch);

                // Fetch all data (no pagination)
                $records = $this->db->query("
                    SELECT DISTINCT ON (tp.deviceid)
                        tp.deviceid,
                        md.imei_no,
                        md.serial_no AS devicename,
                        md.mobile_no AS mobileNumber,
                        tp.trakerspeed AS speed,
                        tp.latitude,
                        tp.longitude,
                        tp.currentdate,
                        tp.currenttime,
                        tp.recivestamp,
                        tp.trakerspeed,
                        tp.devicestatus,
                        tp.batterystats,
                        tp.misc,
                        tp.poleno
                    FROM {$this->schema}.traker_positionaldata tp
                    LEFT JOIN public.master_device_details md ON tp.deviceid = md.id
                    WHERE tp.deviceid IN ($idString)
                    ORDER BY tp.deviceid, tp.recivestamp DESC
                ")->getResult();

                if ($this->request->getGet('download') == 'xlsx') {
                    // Generate XLSX
                    $filename = ucfirst($type) . '_Report_' . date('Y-m-d_H:i:s') . '.xlsx';
                    $dat[0]['A'] = ucfirst($type) . " Report";

                    $dat[1]['A'] = 'Device';
                    $dat[1]['B'] = 'Last Update';
                    $dat[1]['C'] = 'Mobile Number';
                    $dat[1]['D'] = 'Speed (km/h)';
                    $dat[1]['E'] = 'Battery (%)';
                    $dat[1]['F'] = 'Address';

                    $count = 2;
                    foreach ($records as $record) {
                        $dat[$count]['A'] = $record->devicename;
                        $dat[$count]['B'] = $record->currentdate . ' ' . $record->currenttime;
                        $dat[$count]['C'] = $record->mobilenumber;
                        $dat[$count]['D'] = $record->speed;
                        $dat[$count]['E'] = $record->batterystats;
                        $dat[$count]['F'] = $record->poleno ?? '--';
                        $count++;
                    }

                    exceldownload($dat, $filename);
                } elseif ($this->request->getGet('download') == 'pdf') {
                    // Generate PDF
                    $html = '<html>
                        <head>
                            <style>
                                body { font-family: Arial, sans-serif; font-size: 11px; }
                                h2 { text-align: center; margin-bottom: 5px; }
                                .report-meta { text-align: center; font-size: 10px; margin-bottom: 15px; }
                                table { width: 100%; border-collapse: collapse; }
                                th, td { border: 1px solid black; padding: 6px; text-align: center; }
                                th { background-color: #f2f2f2; }
                            </style>
                        </head>
                        <body>

                        <h2>' . ucfirst($type) . ' Report</h2>
                        <p class="report-meta">Generated On: ' . date("Y-m-d H:i:s") . '</p>

                        <table>
                            <thead>
                                <tr>
                                    <th>Device</th>
                                    <th>Last Update</th>
                                    <th>Mobile Number</th>
                                    <th>Speed (km/h)</th>
                                    <th>Battery (%)</th>
                                    <th>Address</th>
                                </tr>
                            </thead>
                            <tbody>';

                    foreach ($records as $record) {
                        $html .= '<tr>
                            <td>' . $record->devicename . '</td>
                            <td>' . $record->currentdate . ' ' . $record->currenttime . '</td>
                            <td>' . $record->mobilenumber . '</td>
                            <td>' . $record->speed . '</td>
                            <td>' . $record->batterystats . '</td>
                            <td>' . ($record->poleno ?? '--') . '</td>
                        </tr>';
                    }

                    $html .= '</tbody></table></body></html>';

                    $filename = ucfirst($type) . '_Report_' . date('Y-m-d_H:i:s') . '.pdf';

                    try {
                        ini_set('memory_limit', '2048M');
                        $makePDF = new \App\Libraries\MakePDF();
                        $makePDF->setFileName($filename);
                        $makePDF->setContent($html);
                        $makePDF->getPdf(true);
                        return;
                    } catch (\Exception $e) {
                        log_message('error', 'PDF generation failed: ' . $e->getMessage());
                        return redirect()->back()->with('error', 'Failed to generate PDF. Please try again.');
                    }
                }
            } else {
                return redirect()->back()->with('error', 'No data available for download.');
            }
        }

        

        // Keyman Devices Query
        if (!empty($deviceIds)) {
            $idString = implode(',', $deviceIds);

            // Get total count for Keyman
            $keymanCountQuery = $this->db->query("
                SELECT COUNT(DISTINCT tp.deviceid) AS total
                FROM {$this->schema}.traker_positionaldata tp
                WHERE tp.deviceid IN ($idString)
            ");
            $keymanTotal = $keymanCountQuery->getRow()->total;

            // Fetch paginated Keyman data
            $offset = ($keymanPage - 1) * $perPage;
            $data['keyman'] = $this->db->query("
                SELECT DISTINCT ON (tp.deviceid)
                    tp.deviceid,
                    md.imei_no,
                    md.serial_no AS devicename,
                    md.mobile_no AS mobileNumber,
                    tp.trakerspeed AS speed,
                    tp.latitude,
                    tp.longitude,
                    tp.currentdate,
                    tp.currenttime,
                    tp.recivestamp,
                    tp.trakerspeed,
                    tp.devicestatus,
                    tp.batterystats,
                    tp.misc,
                    tp.poleno
                FROM {$this->schema}.traker_positionaldata tp
                LEFT JOIN public.master_device_details md ON tp.deviceid = md.id
                WHERE tp.deviceid IN ($idString)
                ORDER BY tp.deviceid, tp.recivestamp DESC
            ")->getResult();

            // Initialize pagination for Keyman
           
        } else {
            $data['keyman'] = [];
            $data['keymanPager'] = '';
        }

        // Patrolman Devices Query
        if (!empty($devicePatrolmanIds)) {
            $idPetrolmanString = implode(',', $devicePatrolmanIds);

            // Get total count for Patrolman
            $patrolmanCountQuery = $this->db->query("
                SELECT COUNT(DISTINCT tp.deviceid) AS total
                FROM {$this->schema}.traker_positionaldata tp
                WHERE tp.deviceid IN ($idPetrolmanString)
            ");
            $patrolmanTotal = $patrolmanCountQuery->getRow()->total;

            // Fetch paginated Patrolman data
            $offset = ($patrolmanPage - 1) * $perPage;
            $data['petrolman'] = $this->db->query("
                SELECT DISTINCT ON (tp.deviceid)
                    tp.deviceid,
                    md.imei_no,
                    md.serial_no AS devicename,
                    md.mobile_no AS mobileNumber,
                    tp.trakerspeed AS speed,
                    tp.latitude,
                    tp.longitude,
                    tp.currentdate,
                    tp.currenttime,
                    tp.recivestamp,
                    tp.trakerspeed,
                    tp.devicestatus,
                    tp.batterystats,
                    tp.misc
                FROM {$this->schema}.traker_positionaldata tp
                LEFT JOIN public.master_device_details md ON tp.deviceid = md.id
                WHERE tp.deviceid IN ($idPetrolmanString)
                ORDER BY tp.deviceid, tp.recivestamp DESC
            ")->getResult();

            
        } else {
            $data['petrolman'] = [];
            $data['patrolmanPager'] = '';
        }

        $data['middle'] = view('devices/device_status_list', $data);
        return view('mainlayout', $data);
    }


    public function deviceStatusList()
    {
        $db = \Config\Database::connect();
        $user_id = $this->sessdata['user_id'];
        $subUsers = getSubUsers($user_id, $db);
        $allowedUsers = array_merge([$user_id], $subUsers);
        $placeholders = implode(',', array_fill(0, count($allowedUsers), '?'));

        $data = [];
        $data['sessdata'] = $this->sessdata;
        $data['page_title'] = "Keyman Report";
        $data['page_titl2'] = "Patrolman Report";
        $data['usersdd'] = $this->commonModel->get_users();

        $perPage = 20;
        $keymanPage = (int) ($this->request->getGet('page_keyman_page') ?? 1);
        $patrolmanPage = (int) ($this->request->getGet('page_patrolman_page') ?? 1);
        $keymanOffset = ($keymanPage - 1) * $perPage;
        $patrolmanOffset = ($patrolmanPage - 1) * $perPage;

        // Fetch Keyman Device IDs (Paginated and Full)
        $keymanDevices = $db->query("
            SELECT DISTINCT ts.deviceid AS device_id
            FROM stes.trip_schedule ts
            LEFT JOIN public.master_device_details md ON ts.imeino = md.imei_no
            LEFT JOIN public.master_device_assign mda ON md.id = mda.deviceid
            WHERE ts.device_type = 'Keyman' AND md.id IS NOT NULL
            AND mda.user_id IN ($placeholders)
            ORDER BY ts.deviceid
            LIMIT $perPage OFFSET $keymanOffset
        ", $allowedUsers)->getResult();

        $allKeymanDevices = $db->query("
            SELECT DISTINCT ts.deviceid AS device_id
            FROM stes.trip_schedule ts
            LEFT JOIN public.master_device_details md ON ts.imeino = md.imei_no
            LEFT JOIN public.master_device_assign mda ON md.id = mda.deviceid
            WHERE ts.device_type = 'Keyman' AND md.id IS NOT NULL
            AND mda.user_id IN ($placeholders)
        ", $allowedUsers)->getResult();

        $deviceIds = array_map(fn($row) => $row->device_id, $keymanDevices);
        $allDeviceIds = array_map(fn($row) => $row->device_id, $allKeymanDevices);

        // Fetch Patrolman Device IDs (Paginated and Full)
        $patrolmanDevices = $db->query("
            SELECT DISTINCT ts.deviceid AS device_id
            FROM stes.trip_schedule ts
            LEFT JOIN public.master_device_details md ON ts.imeino = md.imei_no
            LEFT JOIN public.master_device_assign mda ON md.id = mda.deviceid
            WHERE ts.device_type = 'Patrolman' AND md.id IS NOT NULL
            AND mda.user_id IN ($placeholders)
            ORDER BY ts.deviceid
            LIMIT $perPage OFFSET $patrolmanOffset
        ", $allowedUsers)->getResult();

        $allPatrolmanDevices = $db->query("
            SELECT DISTINCT ts.deviceid AS device_id
            FROM stes.trip_schedule ts
            LEFT JOIN public.master_device_details md ON ts.imeino = md.imei_no
            LEFT JOIN public.master_device_assign mda ON md.id = mda.deviceid
            WHERE ts.device_type = 'Patrolman' AND md.id IS NOT NULL
            AND mda.user_id IN ($placeholders)
        ", $allowedUsers)->getResult();

        $devicePatrolmanIds = array_map(fn($row) => $row->device_id, $patrolmanDevices);
        $allDevicePatrolmanIds = array_map(fn($row) => $row->device_id, $allPatrolmanDevices);

        // Download: XLSX or PDF
        if ($this->request->getGet('download') == 'xlsx' || $this->request->getGet('download') == 'pdf') {
            $type = $this->request->getGet('type'); // 'keyman' or 'patrolman'
            $deviceIdsToFetch = ($type == 'keyman') ? $allDeviceIds : $allDevicePatrolmanIds;

            if (!empty($deviceIdsToFetch)) {
                $idString = implode(',', $deviceIdsToFetch);
                $records = $this->db->query("
                    SELECT DISTINCT ON (tp.deviceid)
                        tp.deviceid,
                        md.imei_no,
                        md.serial_no AS devicename,
                        md.mobile_no AS mobilenumber,
                        tp.trakerspeed AS speed,
                        tp.latitude,
                        tp.longitude,
                        tp.currentdate,
                        tp.currenttime,
                        tp.recivestamp,
                        tp.devicestatus,
                        tp.batterystats,
                        tp.misc,
                        tp.poleno
                    FROM {$this->schema}.traker_positionaldata tp
                    LEFT JOIN public.master_device_details md ON tp.deviceid = md.id
                    WHERE tp.deviceid IN ($idString)
                    ORDER BY tp.deviceid, tp.recivestamp DESC
                ")->getResult();

                if ($this->request->getGet('download') == 'xlsx') {
                    $filename = ucfirst($type) . '_Report_' . date('Y-m-d_H:i:s') . '.xlsx';
                    $dat[0]['A'] = ucfirst($type) . " Report";
                    $dat[1] = [
                        'A' => 'Device',
                        'B' => 'Last Update',
                        'C' => 'Mobile Number',
                        'D' => 'Speed (km/h)',
                        'E' => 'Battery (%)',
                        'F' => 'Address',
                    ];

                    $count = 2;
                    foreach ($records as $r) {
                        $dat[$count++] = [
                            'A' => $r->devicename,
                            'B' => $r->currentdate . ' ' . $r->currenttime,
                            'C' => $r->mobilenumber,
                            'D' => $r->speed,
                            'E' => $r->batterystats,
                            'F' => $r->poleno ?? '--',
                        ];
                    }

                    exceldownload($dat, $filename);
                } elseif ($this->request->getGet('download') == 'pdf') {
                    $html = '<html><head><style>
                        body { font-family: Arial; font-size: 11px; }
                        h2 { text-align: center; }
                        .meta { text-align: center; font-size: 10px; }
                        table { width: 100%; border-collapse: collapse; margin-top: 10px; }
                        th, td { border: 1px solid black; padding: 5px; text-align: center; }
                        th { background-color: #f2f2f2; }
                        </style></head><body>';
                    $html .= "<h2>" . ucfirst($type) . " Report</h2>";
                    $html .= "<p class='meta'>Generated on " . date('Y-m-d H:i:s') . "</p><table><thead><tr>
                        <th>Device</th><th>Last Update</th><th>Mobile Number</th>
                        <th>Speed</th><th>Battery</th><th>Address</th></tr></thead><tbody>";

                    foreach ($records as $r) {
                        $html .= "<tr><td>{$r->devicename}</td><td>{$r->currentdate} {$r->currenttime}</td>
                            <td>{$r->mobilenumber}</td><td>{$r->speed}</td><td>{$r->batterystats}</td>
                            <td>" . ($r->poleno ?? '--') . "</td></tr>";
                    }
                    $html .= '</tbody></table></body></html>';

                    try {
                        ini_set('memory_limit', '2048M');
                        $makePDF = new \App\Libraries\MakePDF();
                        $makePDF->setFileName(ucfirst($type) . '_Report_' . date('Y-m-d_H:i:s') . '.pdf');
                        $makePDF->setContent($html);
                        $makePDF->getPdf(true);
                        return;
                    } catch (\Exception $e) {
                        log_message('error', 'PDF generation failed: ' . $e->getMessage());
                        return redirect()->back()->with('error', 'Failed to generate PDF.');
                    }
                }
            } else {
                return redirect()->back()->with('error', 'No data available for download.');
            }
        }

        // Keyman Data
        $data['keyman'] = !empty($deviceIds) ? $this->fetchDeviceData($deviceIds) : [];
        $keymanTotal = count($allDeviceIds);

        $keymanPager = \Config\Services::pager();
        $data['keymanLinks'] = $keymanPager->makeLinks($keymanPage, $perPage, $keymanTotal, 'default_full', 0, 'keyman_page');

        // Patrolman Data
        $data['petrolman'] = !empty($devicePatrolmanIds) ? $this->fetchDeviceData($devicePatrolmanIds) : [];
        $patrolmanTotal = count($allDevicePatrolmanIds);

        $patrolmanPager = \Config\Services::pager();
        $data['patrolmanLinks'] = $patrolmanPager->makeLinks($patrolmanPage, $perPage, $patrolmanTotal, 'default_full', 0, 'patrolman_page');

        $data['middle'] = view('devices/device_status_list', $data);
        return view('mainlayout', $data);
    }


    private function fetchDeviceData(array $deviceIds)
    {
        $db = \Config\Database::connect();
        $idString = implode(',', $deviceIds);
        return $db->query("
            SELECT DISTINCT ON (tp.deviceid)
                tp.deviceid,
                md.imei_no,
                md.serial_no AS devicename,
                md.mobile_no AS mobileNumber,
                tp.trakerspeed AS speed,
                tp.latitude,
                tp.longitude,
                tp.currentdate,
                tp.currenttime,
                tp.recivestamp,
                tp.devicestatus,
                tp.batterystats,
                tp.misc,
                tp.poleno
            FROM {$this->schema}.traker_positionaldata tp
            LEFT JOIN public.master_device_details md ON tp.deviceid = md.id
            WHERE tp.deviceid IN ($idString)
            ORDER BY tp.deviceid, tp.recivestamp DESC
        ")->getResult();
    }


    public function exceptionReport()
    { 
        // Check if user is logged in
        if (!session()->has('login_sess_data')) {
            return redirect()->to('/');
        }

        $user_id = $this->sessdata['user_id'];
        $subUsers = getSubUsers($user_id, $this->db);
        $allowedUsers = array_merge([$user_id], $subUsers);
        $placeholders = implode(',', array_fill(0, count($allowedUsers), '?'));


        $data = [];
        $data['sessdata'] = $this->sessdata;
        $data['page_title'] = "Exception Report";
        $data['date_from'] = '';
        $data['usertype'] = 'All';
        $data['usersdd'] = $this->commonModel->get_users();
        $data['pway'] = $this->db->query("SELECT organisation, user_id FROM public.user_login WHERE active = 1 AND group_id = 8")->getResult();

        $start_date = $this->request->getGet('stdt') 
            ? date("Y-m-d H:i:s", strtotime(trim($this->request->getGet('stdt')))) 
            : date("Y-m-d 00:00:00");

        $end_date = $this->request->getGet('endt') 
            ? date("Y-m-d H:i:s", strtotime(trim($this->request->getGet('endt')))) 
            : date("Y-m-d 23:59:59");
        
        $data['stdt'] = date("d-m-Y H:i", strtotime($start_date));
        $data['endt'] = date("d-m-Y H:i", strtotime($end_date));
        $data['stdt1'] = date("d-m-Y_H:i", strtotime($start_date));
        $data['endt1'] = date("d-m-Y_H:i", strtotime($end_date));
        $data['usertype'] = $this->request->getGet('usertype') ? trim($this->request->getGet('usertype')) : 'Keyman';
        $data['distance_range'] = $this->request->getGet('distance_range') ? (int) trim($this->request->getGet('distance_range')) : 4;

        $data['schema'] = $schemaname = $this->schema;
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
                        tsd.expected_distance AS exp_distance_travelled,
                        t.totaldistancetravel,
                        t.avg_speed AS average_speed, -- Include avg_speed column
                        t.startbattery AS start_battery, -- Include start battery column
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
                        LEFT JOIN {$this->schema}.trip_schedule_details tsd
                            ON tsd.schedule_details_id = tbl_trip.schedule_details_id 
                        LEFT JOIN {$this->schema}.trip_schedule ts 
                            ON ts.schedule_id = tsd.schedule_id 
                        WHERE ( (sttimestamp BETWEEN ? AND ?)
                                OR (endtimestamp   BETWEEN ? AND ?)
                                OR (sttimestamp < ? AND endtimestamp > ?))
                    ) t 
                    ON t.deviceid = mda.deviceid
                    LEFT JOIN {$this->schema}.trip_schedule_details tsd
                        ON tsd.schedule_details_id = t.schedule_details_id 
                    WHERE 
                        ul.group_id = 2
                        AND mda.active = 1
                        AND mda.user_id IN ($placeholders)
                ),
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
                        COALESCE(exp_distance_travelled, 0) AS expected_distance,
                        COALESCE(totaldistancetravel, 0) AS actual_distance,
                       (CASE WHEN average_speed > 5 THEN 1 END) AS over_speed, -- Count devices exceeding 3 km/h
                       (CASE WHEN start_battery < 50 THEN 1 END) AS low_battery 
                    FROM base_data
                    GROUP BY organisation, user_id, parent_organisation, allocation_status, device_imei, device_name, exp_distance_travelled, totaldistancetravel, start_battery, over_speed
                )
                SELECT 
                    organisation,
                    user_id,
                    parent_organisation,
                    COUNT(CASE 
                        WHEN allocation_status = 'Allocated' 
                        AND has_trip = 1 
                        AND actual_distance < ? 
                        THEN 1 
                    END) AS defaulter_count,
                    ROUND(
                        COUNT(CASE 
                            WHEN allocation_status = 'Allocated' 
                            AND has_trip = 1 
                            AND actual_distance < ? 
                            THEN 1 
                        END) * 100.0 / NULLIF(COUNT(CASE WHEN allocation_status = 'Allocated' THEN 1 END), 0), 2
                    ) AS defaulter_percentage,      
                    COUNT(CASE 
                        WHEN allocation_status = 'Not Allocated' 
                        THEN 1 
                    END) AS not_allocated_count,
                    COUNT(CASE 
                        WHEN allocation_status = 'Allocated' 
                        AND has_trip = 0 
                        THEN 1 
                    END) AS device_off_count,
                    COALESCE(STRING_AGG(CASE 
                        WHEN allocation_status = 'Allocated' 
                        AND has_trip = 0  
                        THEN device_name END, ', '), '') AS off_device_names,
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
                    END) AS beats_not_covered_count,
                    COALESCE(STRING_AGG(CASE 
                        WHEN allocation_status = 'Allocated' 
                         AND has_trip = 1 
                        AND covered = 0 
                        AND actual_distance < expected_distance  
                        THEN device_name END, ', '), '') AS not_covered_device_names,
                    SUM(over_speed) AS over_speed_count, 
                    SUM(low_battery) AS low_battery_count 
                FROM device_status
                GROUP BY organisation, user_id, parent_organisation;";

        $parameters = [$start_date, $end_date, $start_date, $end_date, $start_date, $end_date];
        $parameters = array_merge($parameters, $allowedUsers);
        $parameters = array_merge($parameters, array($data['distance_range'], $data['distance_range']));

        $result = $this->db->query($sql, $parameters)->getResult();

        $alldata = $data['alldata'] = $result;
        

        if ($this->request->getGet('download') == 'xlsx') {
            $filename = 'Exception_Report_' . date('Y-m-d_H:i:s') . '.xlsx';

            $count = 1;
            $dat = [];

            $dat[0]['A'] = "Exception Report". $start_date .' to '. $end_date;
            
            $dat[1]['A'] = 'PWay';
            $dat[1]['B'] = 'Section';
            $dat[1]['C'] = 'Total Device';
            $dat[1]['D'] = 'Unallocated';
            $dat[1]['E'] = 'Petrolling Done';
            $dat[1]['F'] = 'Petrolling Not Done';
            $dat[1]['G'] = 'Off Device';
            $dat[1]['H'] = 'Start Low Battery';
            $dat[1]['I'] = 'Over Speed';
            $dat[1]['J'] = 'Defaulter(%)';


            if (!empty($alldata)) {
                foreach ($alldata as $irow) {
                    $notCoveredDevices = is_array($irow->not_covered_device_names) ? $irow->not_covered_device_names : explode(',', $irow->not_covered_device_names);
                    $offDevices = is_array($irow->off_device_names) ? $irow->off_device_names : explode(',', $irow->off_device_names);

                    $dat[$count + 1]['A'] = $irow->parent_organisation;
                    $dat[$count + 1]['B'] = $irow->organisation;
                    $dat[$count + 1]['C'] = $irow->beats_not_covered_count + $irow->device_off_count + $irow->beats_covered_count + $irow->not_allocated_count;
                    $dat[$count + 1]['D'] = $irow->not_allocated_count;
                    $dat[$count + 1]['E'] = $irow->beats_covered_count;
                    $dat[$count + 1]['F'] = $irow->beats_not_covered_count . (count($notCoveredDevices) > 0 ? ' (' . implode(', ', $notCoveredDevices) . ')' : '');
                    $dat[$count + 1]['G'] = $irow->device_off_count . (count($offDevices) > 0 ? ' (' . implode(', ', $offDevices) . ')' : '');
                    $dat[$count + 1]['H'] = $irow->low_battery_count;
                    $dat[$count + 1]['I'] = $irow->over_speed_count;
                    $dat[$count + 1]['J'] = round($irow->defaulter_percentage, 2);
                    ++$count;
                }

                // Add total summary row
                $totalDevices = array_sum(array_column($alldata, 'beats_not_covered_count')) +
                                array_sum(array_column($alldata, 'device_off_count')) +
                                array_sum(array_column($alldata, 'beats_covered_count')) +
                                array_sum(array_column($alldata, 'not_allocated_count'));
                $totalUnallocated = array_sum(array_column($alldata, 'not_allocated_count'));
                $totalPetrollingDone = array_sum(array_column($alldata, 'beats_covered_count'));
                $totalPetrollingNotDone = array_sum(array_column($alldata, 'beats_not_covered_count'));
                $totalOffDevices = array_sum(array_column($alldata, 'device_off_count'));
                $totalLowBattery = array_sum(array_column($alldata, 'low_battery_count'));
                $totalOverSpeed = array_sum(array_column($alldata, 'over_speed_count'));
                $totalDefaulter = array_sum(array_column($alldata, 'defaulter_count'));

                $dat[$count + 1]['A'] = '';
                $dat[$count + 1]['B'] = 'Total';
                $dat[$count + 1]['C'] = $totalDevices;
                $dat[$count + 1]['D'] = $totalUnallocated;
                $dat[$count + 1]['E'] = $totalPetrollingDone;
                $dat[$count + 1]['F'] = $totalPetrollingNotDone;
                $dat[$count + 1]['G'] = $totalOffDevices;
                $dat[$count + 1]['H'] = $totalLowBattery;
                $dat[$count + 1]['I'] = $totalOverSpeed;
                $dat[$count + 1]['J'] = round($totalDefaulter, 2);
            } else {
                $dat[2]['A'] = 'No Data available!';
            }

            exceldownload($dat, $filename);
        }


        if($this->request->getGet('download') == 'pdf'){
            
            $html = '<html>
                <head>
                    <style>
                        body { font-family: Arial, sans-serif; font-size: 8px; }
                        h2 { text-align: center; margin-bottom: 5px; }
                        .report-meta { text-align: center; font-size: 10px; margin-bottom: 15px; }
                        table { width: 100%; border-collapse: collapse; }
                        th, td { border: 1px solid black; padding: 6px; text-align: center; }
                        th { background-color: #f2f2f2; }
                    </style>
                </head>
                <body>

                <h2>Exception Report '. $start_date .' to '. $end_date .'</h2>
                <p class="report-meta">Generated On: ' . date("Y-m-d H:i:s") . '</p>

                <table>
                    <thead>
                        <tr>
                            <th>PWay</th>
                            <th>Section</th>
                            <th>Total Device</th>
                            <th>Unallocated</th>
                            <th>Petrolling Done</th>
                            <th>Petrolling Not Done</th>
                            <th>Off Device</th>
                            <th>Start Low Battery</th>
                            <th>Over Speed</th>
                            <th>Defaulter(%)</th>
                        </tr>
                    </thead>
                    <tbody>';
            if (!empty($alldata)) {
                foreach ($alldata as $irow) {
                    $notCoveredDevices = is_array($irow->not_covered_device_names) ? $irow->not_covered_device_names : explode(',', $irow->not_covered_device_names);
                    $offDevices = is_array($irow->off_device_names) ? $irow->off_device_names : explode(',', $irow->off_device_names);

                    $html .= '<tr>
                        <td>' . $irow->parent_organisation . '</td>
                        <td>' . $irow->organisation . '</td>
                        <td>' . ($irow->beats_not_covered_count + $irow->device_off_count + $irow->beats_covered_count + $irow->not_allocated_count) . '</td>
                        <td>' . $irow->not_allocated_count . '</td>
                        <td>' . $irow->beats_covered_count . '</td>
                        <td>' . $irow->beats_not_covered_count . (count($notCoveredDevices) > 0 ? ' (' . implode(', ', $notCoveredDevices) . ')' : '') . '</td>
                        <td>' . $irow->device_off_count . (count($offDevices) > 0 ? ' (' . implode(', ', $offDevices) . ')' : '') . '</td>
                        <td>' . $irow->low_battery_count . '</td>
                        <td>' . $irow->over_speed_count . '</td>
                        <td>' . round($irow->defaulter_percentage, 2) . '</td>
                    </tr>';
                }

                $totalDevices = array_sum(array_column($alldata, 'beats_not_covered_count')) +
                                array_sum(array_column($alldata, 'device_off_count')) +
                                array_sum(array_column($alldata, 'beats_covered_count')) +
                                array_sum(array_column($alldata, 'not_allocated_count'));
                $totalUnallocated = array_sum(array_column($alldata, 'not_allocated_count'));
                $totalPetrollingDone = array_sum(array_column($alldata, 'beats_covered_count'));
                $totalPetrollingNotDone = array_sum(array_column($alldata, 'beats_not_covered_count'));
                $totalOffDevices = array_sum(array_column($alldata, 'device_off_count'));
                $totalLowBattery = array_sum(array_column($alldata, 'low_battery_count'));
                $totalOverSpeed = array_sum(array_column($alldata, 'over_speed_count'));
                $totalDefaulter = array_sum(array_column($alldata, 'defaulter_count'));

                $html .= '<tr>
                    <td colspan="2">Total</td>
                    <td>' . ($totalDevices) . '</td>
                    <td>' . ($totalUnallocated) . '</td>
                    <td>' . ($totalPetrollingDone) . '</td>
                    <td>' . ($totalPetrollingNotDone) . '</td>
                    <td>' . ($totalOffDevices) . '</td>
                    <td>' . ($totalLowBattery) . '</td>
                    <td>' . ($totalOverSpeed) . '</td>
                    <td>' . round($totalDefaulter, 2) . '</td>
                    </tr>';

            } else {
                $html .= '<tr><td colspan="10">No Data available!</td></tr>';
            }
            $html .= '</tbody></table></body></html>';

            $filename = 'Exception_Report_' . date('Y-m-d_H:i:s') . '.pdf';

            try{

                ini_set('memory_limit', '2048M');
                $makePDF = new \App\Libraries\MakePDF();
                $makePDF->setFileName($filename);
                $makePDF->setContent($html);

                $makePDF->getPdf(true); 
                return;
            }catch (\Exception $e) {
                log_message('error', 'PDF generation failed: ' . $e->getMessage());
                return redirect()->back()->with('error', 'Failed to generate PDF. Please try again.');;
            }
        }


        $data['middle'] = view('devices/exception_report_new', $data);
        return view('mainlayout', $data);
    }


    public function scheduledPetrollingReport()
    {
        if (!session()->has('login_sess_data')) {
            return redirect()->to('/');
        }

        $tripScheduleModel = new TripScheduleModel();
        $tripDetailsModel = new TripScheduleDetailsModel();
        $db = \Config\Database::connect();

        $user_id = $this->sessdata['user_id'];
        $subUsers = getSubUsers($user_id, $this->db);
        $allowedUsers = array_merge([$user_id], $subUsers);
        $placeholders = implode(',', array_fill(0, count($allowedUsers), '?'));

        $data = [];
        $data['sessdata'] = $this->sessdata;
        $data['page_title'] = "Scheduled Patrolling Report";
        $data['usersdd'] = $this->commonModel->get_users();

        $startDate = $this->request->getGet('start_date');

        $schema = $this->sessdata['schemaname'] ?? 'public';
        $table = "{$schema}.trip_schedule";
        $detailsTable = "{$schema}.trip_schedule_details";
        $tripTable = "{$schema}.tbl_trip";
        $userTable = "public.user_login";

        $query = $tripScheduleModel->select("
                t.totaldistancetravel,
                ts.*,
                tsd.*
            ")
            ->from("$table AS ts")
            ->join("$detailsTable AS tsd ", "ts.schedule_id = tsd.schedule_id", 'left')
            ->join("$tripTable AS t ", "t.schedule_details_id = tsd.schedule_details_id", 'left')
            ->join("public.master_device_assign mda", "ts.deviceid = mda.deviceid", 'left')
            ->join("public.user_login ul", "ul.user_id = mda.user_id", 'left')
            ->whereIn("mda.user_id", $allowedUsers)
            ->groupBy("ts.schedule_id, ts.deviceid, ts.imeino, ts.section_id, ts.pwi_id, 
                ts.device_type, ts.expected_start_date, ts.expected_start_time, 
                ts.expected_end_date, ts.expected_end_time, ts.active, tsd.schedule_details_id, t.totaldistancetravel")
            ->orderBy("ts.expected_start_date", "ASC");

        if (!empty($startDate)) {
            $query->where("ts.expected_start_date =", $startDate);
        }else{
            $query->where("ts.expected_start_date =", date('Y-m-d'));
        }

        $dateQuery = $db->query("
            SELECT 
                MIN(expected_start_date) AS start_date, 
                MAX(expected_end_date) AS end_date
            FROM $table
        ")->getRowArray();

        $data['stdt'] = ($startDate) ? $startDate : date("d-m-Y");
        

        $data['tripSchedules'] = $query->paginate(10);
        $data['pager'] = $tripScheduleModel->pager;

        // print_r($data['tripSchedules']);die();
        $data['request'] = $this->request;
        $data['middle'] = view('devices/scheduled_petrolling_report', $data);
        return view('mainlayout', $data);
    }


    public function downloadPatrollingReportXLSX()
    {
        if (!session()->has('login_sess_data')) {
            return redirect()->to('/');
        }
        $tripScheduleModel = new TripScheduleModel();
        $tripDetailsModel = new TripScheduleDetailsModel();
        $db = \Config\Database::connect();

        $user_id = $this->sessdata['user_id'];
        $subUsers = getSubUsers($user_id, $this->db);
        $allowedUsers = array_merge([$user_id], $subUsers);
        $placeholders = implode(',', array_fill(0, count($allowedUsers), '?'));

        $data = [];
        $data['sessdata'] = $this->sessdata;
        $data['page_title'] = "Scheduled Patrolling Report";
        $data['usersdd'] = $this->commonModel->get_users();

        $startDate = $this->request->getGet('start_date');

        $schema = $this->sessdata['schemaname'] ?? 'public';
        $table = "{$schema}.trip_schedule";
        $detailsTable = "{$schema}.trip_schedule_details";
        $tripTable = "{$schema}.tbl_trip";
        $userTable = "public.user_login";

        $query = $tripScheduleModel->select("
                t.totaldistancetravel,
                ts.*,
                tsd.*
            ")
            ->from("$table AS ts")
            ->join("$detailsTable AS tsd ", "ts.schedule_id = tsd.schedule_id", 'left')
            ->join("$tripTable AS t ", "t.schedule_details_id = tsd.schedule_details_id", 'left')
            ->join("public.master_device_assign mda", "ts.deviceid = mda.deviceid", 'left')
            ->join("public.user_login ul", "ul.user_id = mda.user_id", 'left')
            ->whereIn("mda.user_id", $allowedUsers)
            ->groupBy("ts.schedule_id, ts.deviceid, ts.imeino, ts.section_id, ts.pwi_id, 
                ts.device_type, ts.expected_start_date, ts.expected_start_time, 
                ts.expected_end_date, ts.expected_end_time, ts.active, tsd.schedule_details_id, t.totaldistancetravel")
            ->orderBy("ts.expected_start_date", "ASC");

        if (!empty($startDate)) {
            $query->where("ts.expected_start_date =", $startDate);
        }else{
            $query->where("ts.expected_start_date =", date('Y-m-d'));
        }

        $dateQuery = $db->query("
            SELECT 
                MIN(expected_start_date) AS start_date, 
                MAX(expected_end_date) AS end_date
            FROM $table
        ")->getRowArray();

        $tripSchedules = $query->findAll();



        $dat[0]['A'] = "Patrolling Report ". $startDate;

        $dat[1]['A'] = 'SL No.';
        $dat[1]['B'] = 'Device';
        $dat[1]['C'] = 'Device Name';
        $dat[1]['D'] = 'Trip No.';
        $dat[1]['E'] = 'Actual Start Time';
        $dat[1]['F'] = 'Expected Start Time';
        $dat[1]['G'] = 'Actual Start Beat';
        $dat[1]['H'] = 'Expected Start Beat';
        $dat[1]['I'] = 'Actual End Time';
        $dat[1]['J'] = 'Expected End Time';
        $dat[1]['K'] = 'Actual End Beat';
        $dat[1]['L'] = 'Expected End Beat';
        $dat[1]['M'] = 'Total Distance Covered';
        $dat[1]['N'] = 'Expected Distance Covered';
        $dat[1]['O'] = 'Remark';


        if (!empty($tripSchedules)) {
            foreach ($tripSchedules as $key => $row) {
               
                    $dat[$key+2]['A'] = $key + 1;
                    $dat[$key+2]['B'] = $row['imeino'] ?? 'N/A';
                    $dat[$key+2]['C'] = $row['deviceid'] ?? 'N/A';
                    $dat[$key+2]['D'] = $row['trip_no'] ?? 'N/A';
                    $dat[$key+2]['E'] = $row['actual_start_datetime'] ?? 'N/A';
                    $dat[$key+2]['F'] = $row['expected_start_datetime'] ?? 'N/A';
                    $dat[$key+2]['G'] = $row['actual_stpole'] ?? 'N/A';
                    $dat[$key+2]['H'] = $row['expected_stpole'] ?? 'N/A';
                    $dat[$key+2]['I'] = $row['actual_end_datetime'] ?? 'N/A';
                    $dat[$key+2]['J'] = $row['expected_end_datetime'] ?? 'N/A';
                    $dat[$key+2]['K'] = $row['actual_endpole'] ?? 'N/A';
                    $dat[$key+2]['L'] = $row['expected_endpole'] ?? 'N/A';
                    $dat[$key+2]['M'] = $row['totaldistancetravel'] ?? 'N/A';
                    $dat[$key+2]['N'] = $row['expected_distance'] ?? 'N/A';
                    $dat[$key+2]['O'] = $row['trip_status'] ?? 'N/A';
              
            }
        } else {
            $dat[2]['A'] = 'No Data available!';
        }

        $filename = date('Y-m-d').'_Patrolling_Report_'.time().'.xlsx';
        exceldownload($dat, $filename);
    }
    

    public function downloadPatrollingReportPDF()
    {
        $db = \Config\Database::connect();
        $startDate = $this->request->getGet('start_date');
        $schema = $this->sessdata['schemaname'] ?? 'public';

        $user_id = $this->sessdata['user_id'];
        $subUsers = getSubUsers($user_id, $this->db);
        $allowedUsers = array_merge([$user_id], $subUsers);
        $placeholders = implode(',', array_fill(0, count($allowedUsers), '?'));

        $table = "{$schema}.trip_schedule";
        $detailsTable = "{$schema}.trip_schedule_details";
        $tripTable = "{$schema}.tbl_trip";

        // Build query using query builder
        $query = $db->table("$table AS ts")
            ->select("t.totaldistancetravel, ts.*, tsd.*")
            ->join("$detailsTable AS tsd", "ts.schedule_id = tsd.schedule_id", 'left')
            ->join("$tripTable AS t", "t.schedule_details_id = tsd.schedule_details_id", 'left')
            ->join("public.master_device_assign mda", "ts.deviceid = mda.deviceid", 'left')
            ->join("public.user_login ul", "ul.user_id = mda.user_id", 'left')
            ->whereIn("mda.user_id", $allowedUsers)
            ->where("ts.expected_start_date", $startDate ?? date('Y-m-d'))
            ->groupBy("ts.schedule_id, ts.deviceid, ts.imeino, ts.section_id, ts.pwi_id, 
                    ts.device_type, ts.expected_start_date, ts.expected_start_time, 
                    ts.expected_end_date, ts.expected_end_time, ts.active, 
                    tsd.schedule_details_id, t.totaldistancetravel")
            ->orderBy("ts.expected_start_date", "ASC");

        $tripSchedules = $query->get()->getResultArray();

        if (empty($tripSchedules)) {
            return redirect()->back()->with('error', 'No data available for the selected date.');
        }

        $timestamp = date("Y-m-d H:i:s");

        // Start HTML content
        $html = '<html>
            <head>
                <style>
                    body { font-family: Arial, sans-serif; font-size: 11px; }
                    h2 { text-align: center; margin-bottom: 5px; }
                    .report-meta { text-align: center; font-size: 5px; margin-bottom: 15px; }
                    table { width: 100%; border-collapse: collapse; }
                    th, td { border: 1px solid black; padding: 6px; text-align: center; }
                    th { background-color: #f2f2f2; }
                </style>
            </head>
            <body>

            <h2>Scheduled Patrolling Report</h2>
            <p class="report-meta">Generated On: ' . $timestamp . '</p>

            <table>
                <thead>
                    <tr>
                        <th>SL No.</th>
                        <th>Device</th>
                        <th>Device Name</th>
                        <th>Trip No.</th>
                        <th>Actual Start Time</th>
                        <th>Expected Start Time</th>
                        <th>Actual Start Beat</th>
                        <th>Expected Start Beat</th>
                        <th>Actual End Time</th>
                        <th>Expected End Time</th>
                        <th>Actual End Beat</th>
                        <th>Expected End Beat</th>
                        <th>Total Distance</th>
                        <th>Expected Distance</th>
                        <th>Remarks</th>
                    </tr>
                </thead>
                <tbody>';

        foreach ($tripSchedules as $key => $row) {
            $html .= '<tr>
                <td>' . ($key + 1) . '</td>
                <td>' . ($row['imeino'] ?? 'N/A') . '</td>
                <td>' . ($row['deviceid'] ?? 'N/A') . '</td>
                <td>' . ($row['trip_no'] ?? 'N/A') . '</td>
                <td>' . ($row['actual_start_datetime'] ?? 'N/A') . '</td>
                <td>' . ($row['expected_start_datetime'] ?? 'N/A') . '</td>
                <td>' . ($row['actual_stpole'] ?? 'N/A') . '</td>
                <td>' . ($row['expected_stpole'] ?? 'N/A') . '</td>
                <td>' . ($row['actual_end_datetime'] ?? 'N/A') . '</td>
                <td>' . ($row['expected_end_datetime'] ?? 'N/A') . '</td>
                <td>' . ($row['actual_endpole'] ?? 'N/A') . '</td>
                <td>' . ($row['expected_endpole'] ?? 'N/A') . '</td>
                <td>' . ($row['totaldistancetravel'] ?? 'N/A') . '</td>
                <td>' . ($row['expected_distance'] ?? 'N/A') . '</td>
                <td>' . ($row['trip_status'] ?? 'N/A') . '</td>
            </tr>';
        }

        $html .= '</tbody></table></body></html>';

        $filename = "Patrolling_Report_" . date("Ymd_His") . ".pdf";

        try {
            ini_set('memory_limit', '2048M');
            $makePDF = new \App\Libraries\MakePDF();
            $makePDF->setFileName($filename);
            $makePDF->setContent($html);
           
            $makePDF->getPdf(true); 
            return;
        } catch (\Exception $e) {
            log_message('error', 'PDF generation failed: ' . $e->getMessage());
            return redirect()->back()->with('error', 'Failed to generate PDF. Please try again.');
        }
    }


    public function scheduledPatrollingSummery() {
        if (!session()->has('login_sess_data')) {
            return redirect()->to('/');
        }
        

        $user_id = $this->sessdata['user_id'];
        $subUsers = getSubUsers($user_id, $this->db);
        $allowedUsers = array_merge([$user_id], $subUsers);
        $placeholders = implode(',', array_fill(0, count($allowedUsers), '?'));

        $data = [];
        $data['sessdata'] = $this->sessdata;
        $data['page_title'] = "Scheduled Patrolling Summary";
        $data['usertype'] = 'All';
        $data['usersdd'] = $this->commonModel->get_users();
        $data['pway'] = $this->db->query("SELECT organisation, user_id FROM public.user_login WHERE active = 1 AND group_id = 8")->getResult();
    
        $data['stdt'] = $start_date = $this->request->getGet('stdt')
            ? date("Y-m-d 00:00:00", strtotime(trim($this->request->getGet('stdt'))))
            : date("Y-m-d 00:00:00");
    
        $end_date = $this->request->getGet('stdt')
            ? date("Y-m-d 23:59:59", strtotime(trim($this->request->getGet('stdt'))))
            : date("Y-m-d 23:59:59");
    
        $data['stdt1'] = $start_date;
        $data['endt1'] = $end_date;
        $user_id = $this->sessdata['user_id'];
    
        $sql = "WITH base_data AS (
                    SELECT
                        ul.organisation,
                        ul.user_id,
                        p_ul.organisation AS parent_organisation,
                        md.serial_no AS device_imei,
                        msd.device_name AS device_name,
                        CASE
                            WHEN lower(msd.device_name) LIKE '%stock%' OR msd.device_name IS NULL OR trim(msd.device_name) = ''
                            THEN 'Not Allocated'
                            ELSE 'Allocated'
                        END AS allocation_status,
                        ts.imeino AS trip_imeino,
                        tsd.total_distance,
                        tsd.expected_distance,
                        tsd.max_speed AS average_speed,
                        tsd.delay_minutes,
                        CASE
                            WHEN tsd.trip_status = 'Completed' THEN 'Patroling Completed'
                            ELSE NULL
                        END AS trip_status
                    FROM public.master_device_assign mda
                    LEFT JOIN public.user_login ul ON ul.user_id = mda.user_id
                    LEFT JOIN public.master_device_details md ON md.id = mda.deviceid
                    LEFT JOIN {$this->schema}.master_device_setup msd ON msd.deviceid = mda.deviceid
                    LEFT JOIN public.user_login p_ul ON p_ul.user_id = mda.parent_id
                    LEFT JOIN {$this->schema}.trip_schedule ts ON ts.deviceid = mda.deviceid
                    LEFT JOIN {$this->schema}.trip_schedule_details tsd ON tsd.schedule_id = ts.schedule_id
                    WHERE ul.group_id = 2 AND mda.active = 1
                        AND (
                            (tsd.expected_start_datetime BETWEEN ? AND ?)
                            OR (tsd.expected_end_datetime BETWEEN ? AND ?)
                            OR (tsd.expected_start_datetime < ? AND tsd.expected_end_datetime > ?)
                        ) 
                        AND mda.user_id IN ($placeholders) 
                ),
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
                        MAX(COALESCE(expected_distance, 0)) AS expected_distance,
                        SUM(COALESCE(total_distance, 0)) AS actual_distance,
                        COUNT(CASE WHEN average_speed > 5 THEN 1 END) AS over_speed_count,
                        SUM(COALESCE(delay_minutes, 0)) AS delayed_start_count
                    FROM base_data
                    GROUP BY organisation, user_id, parent_organisation, allocation_status, device_imei, device_name
                )
                SELECT
                    organisation,
                    user_id,
                    parent_organisation,
                    COUNT(CASE WHEN allocation_status = 'Not Allocated' THEN 1 END) AS not_allocated_count,
                    string_agg(CASE WHEN allocation_status = 'Not Allocated' THEN device_imei END, ',' ORDER BY device_imei) AS not_allocated_imeino,
                    string_agg(CASE WHEN allocation_status = 'Not Allocated' THEN device_name END, ',' ORDER BY device_imei) AS not_allocated_devicename,
                    COUNT(CASE WHEN allocation_status = 'Allocated' AND has_trip = 0 THEN 1 END) AS device_off_count,
                    string_agg(CASE WHEN allocation_status = 'Allocated' AND has_trip = 0 THEN device_imei END, ',' ORDER BY device_imei) AS device_off_imeino,
                    string_agg(CASE WHEN allocation_status = 'Allocated' AND has_trip = 0 THEN device_name END, ',' ORDER BY device_imei) AS device_off_devicename,
                    COUNT(CASE WHEN allocation_status = 'Allocated' AND has_trip = 1 AND (covered = 1 OR (covered = 0 AND actual_distance >= expected_distance)) THEN 1 END) AS beats_covered_count,
                    COUNT(CASE WHEN allocation_status = 'Allocated' AND has_trip = 1 AND covered = 0 AND actual_distance < expected_distance THEN 1 END) AS beats_not_covered_count,
                    SUM(over_speed_count) AS over_speed_count,
                    SUM(delayed_start_count) AS delayed_start_count
                FROM device_status
                GROUP BY organisation, user_id, parent_organisation;";
    
        $parameters = [$start_date, $end_date, $start_date, $end_date, $start_date, $end_date];
        $parameters = array_merge($parameters, $allowedUsers);
    
        $result = $this->db->query($sql, $parameters)->getResult();
        $alldata = $data['alldata'] = $result;

        /* --------- XLSX Download --------- */
        if($this->request->getGet('download') == 'xlsx') {

            $count = 1;
            $dat = [];
            $dat[0]['A'] = "Scheduled Patrolling Summary Report from ". $start_date . " to " . $end_date;

            $dat[1]['A'] = 'PWay';
            $dat[1]['B'] = 'Section';
            $dat[1]['C'] = 'Total Device';
            $dat[1]['D'] = 'Inactive Devices';
            $dat[1]['E'] = 'Active Devices';
            $dat[1]['F'] = 'Patrolling Completed in all respect';
            $dat[1]['G'] = 'Incompleted Beat';
            $dat[1]['H'] = 'Not Allocated';
            $dat[1]['I'] = 'Delay';
            $dat[1]['J'] = 'Overspeed';

            if (!empty($alldata)) {
                foreach ($alldata as $key => $row) {
                    $dat[$key + 2]['A'] = $row->parent_organisation;
                    $dat[$key + 2]['B'] = $row->organisation;
                    $dat[$key + 2]['C'] = $row->beats_not_covered_count + $row->device_off_count + $row->beats_covered_count + $row->not_allocated_count;
                    $dat[$key + 2]['D'] = $row->device_off_count;
                    $dat[$key + 2]['E'] = $row->beats_not_covered_count + $row->beats_covered_count;
                    $dat[$key + 2]['F'] = $row->beats_covered_count;
                    $dat[$key + 2]['G'] = $row->beats_not_covered_count;
                    $dat[$key + 2]['H'] = $row->not_allocated_count;
                    $dat[$key + 2]['I'] = $row->delayed_start_count;
                    $dat[$key + 2]['J'] = $row->over_speed_count;

                    $count++;
                }
            
                // Add total summary row
                $totalDevices = array_sum(array_column($alldata, 'beats_not_covered_count')) +
                                array_sum(array_column($alldata, 'device_off_count')) +
                                array_sum(array_column($alldata, 'beats_covered_count')) +
                                array_sum(array_column($alldata, 'not_allocated_count'));
                $totalInactive = array_sum(array_column($alldata, 'device_off_count'));
                $totalActive = array_sum(array_column($alldata, 'beats_not_covered_count')) +
                            array_sum(array_column($alldata, 'beats_covered_count'));
                $totalCompleted = array_sum(array_column($alldata, 'beats_covered_count'));
                $totalIncomplete = array_sum(array_column($alldata, 'beats_not_covered_count'));
                $totalNotAllocated = array_sum(array_column($alldata, 'not_allocated_count'));
                $totalDelay = array_sum(array_column($alldata, 'delayed_start_count'));
                $totalOverspeed = array_sum(array_column($alldata, 'over_speed_count'));
                
                $dat[$count +1]['A'] = ''; 
                $dat[$count +1]['B'] = 'Total Summery';
                $dat[$count +1]['C'] = $totalDevices;
                $dat[$count +1]['D'] = $totalInactive;
                $dat[$count +1]['E'] = $totalActive;
                $dat[$count +1]['F'] = $totalCompleted;
                $dat[$count +1]['G'] = $totalIncomplete;
                $dat[$count +1]['H'] = $totalNotAllocated;
                $dat[$count +1]['I'] = $totalDelay;
                $dat[$count +1]['J'] = $totalOverspeed;

            } else {
                $dat[2]['A'] = 'No Data available!';
            }

            $filename = date('Y-m-d').'_Scheduled_Patrolling_Summary_'.time().'.xlsx';
            exceldownload($dat, $filename);
            exit;
        }

        /* --------- PDF Download --------- */

        if($this->request->getGet('download') == 'pdf') {
            $html = '<html>
                <head>
                    <style>
                        body { font-family: Arial, sans-serif; font-size: 11px; }
                        h2 { text-align: center; margin-bottom: 5px; }
                        .report-meta { text-align: center; font-size: 10px; margin-bottom: 15px; }
                        table { width: 100%; border-collapse: collapse; }
                        th, td { border: 1px solid black; padding: 6px; text-align: center; }
                        th { background-color: #f2f2f2; }
                    </style>
                </head>
                <body>

                <h2>Scheduled Patrolling Summary Report</h2>
                <p class="report-meta">Generated On: ' . date("Y-m-d H:i:s") . '</p>

                <table>
                    <thead>
                        <tr>
                            <th>PWay</th>
                            <th>Section</th>
                            <th>Total Device</th>
                            <th>Inactive Devices</th>
                            <th>Active Devices</th>
                            <th>Patrolling Completed in all respect</th>
                            <th>Incompleted Beat</th>
                            <th>Not Allocated</th>
                            <th>Delay</th>
                            <th>Overspeed</th>
                        </tr>
                    </thead>
                    <tbody>';


            $total_devices = 0;
            $total_inactive = 0;
            $total_active = 0;
            $total_completed = 0;
            $total_incomplete = 0;
            $total_not_allocated = 0;
            $total_delay = 0;
            $total_overspeed = 0;


            if (!empty($alldata)) {
                foreach ($alldata as $key => $row) {
                    $devices = $row->beats_not_covered_count + $row->device_off_count + $row->beats_covered_count + $row->not_allocated_count;
                    $active = $row->beats_not_covered_count + $row->beats_covered_count;

                    $html .= '<tr>
                        <td>' . $row->parent_organisation . '</td>
                        <td>' . $row->organisation . '</td>
                        <td>' . ($row->beats_not_covered_count + $row->device_off_count + $row->beats_covered_count + $row->not_allocated_count) . '</td>
                        <td>' . $row->device_off_count . '</td>
                        <td>' . ($row->beats_not_covered_count + $row->beats_covered_count) . '</td>
                        <td>' . $row->beats_covered_count . '</td>
                        <td>' . $row->beats_not_covered_count . '</td>
                        <td>' . $row->not_allocated_count . '</td>
                        <td>' . $row->delayed_start_count . '</td>
                        <td>' . $row->over_speed_count . '</td>
                        </tr>';


                        $total_devices += $devices;
                    $total_inactive += $row->device_off_count;
                    $total_active += $active;
                    $total_completed += $row->beats_covered_count;
                    $total_incomplete += $row->beats_not_covered_count;
                    $total_not_allocated += $row->not_allocated_count;
                    $total_delay += $row->delayed_start_count;
                    $total_overspeed += $row->over_speed_count;
                }               

                 // Add total row
                $html .= '<tr style="font-weight:bold;background:#eaeaea;">
                    <td colspan="2">Total Summery</td>
                    <td>' . $total_devices . '</td>
                    <td>' . $total_inactive . '</td>
                    <td>' . $total_active . '</td>
                    <td>' . $total_completed . '</td>
                    <td>' . $total_incomplete . '</td>
                    <td>' . $total_not_allocated . '</td>
                    <td>' . $total_delay . '</td>
                    <td>' . $total_overspeed . '</td>
                </tr>';
            }

            $html .= '</tbody></table></body></html>';
            $filename = "Scheduled_Patrolling_Summary_" . date("Ymd_His") . ".pdf";

            try{
                ini_set('memory_limit', '2048M');
                $makePDF = new \App\Libraries\MakePDF();
                $makePDF->setFileName($filename);
                $makePDF->setContent($html);
                $makePDF->getPdf(true); // true means download
                return;
            } catch (\Exception $e) {
                log_message('error', 'PDF generation failed: ' . $e->getMessage());
                return redirect()->back()->with('error', 'Failed to generate PDF. Please try again.');
            }

        }
    
        $data['middle'] = view('devices/scheduled_patrolling_summery', $data);
        return view('mainlayout', $data);
    }
  
    
    public function summeryReportNew()
    { 
        // Check if user is logged in
        if (!session()->has('login_sess_data')) {
            return redirect()->to('/');
        }

        $user_id = $this->sessdata['user_id'];
        $subUsers = getSubUsers($user_id, $this->db);
        $allowedUsers = array_merge([$user_id], $subUsers);
        $placeholders = implode(',', array_fill(0, count($allowedUsers), '?'));


        $data = [];
        $data['sessdata'] = $this->sessdata;
        $data['page_title'] = "Summery Report";
        $data['date_from'] = '';
        $data['usertype'] = 'All';
        $data['usersdd'] = $this->commonModel->get_users();
        $data['pway'] = $this->db->query("SELECT organisation, user_id FROM public.user_login WHERE active = 1 AND group_id = 8")->getResult();

        $start_date = $this->request->getGet('stdt') 
            ? date("Y-m-d H:i:s", strtotime(trim($this->request->getGet('stdt')))) 
            : date("Y-m-d 00:00:00");

        $end_date = $this->request->getGet('endt') 
            ? date("Y-m-d H:i:s", strtotime(trim($this->request->getGet('endt')))) 
            : date("Y-m-d 23:59:59");
        
        $data['stdt'] = date("d-m-Y H:i", strtotime($start_date));
        $data['endt'] = date("d-m-Y H:i", strtotime($end_date));
        $data['stdt1'] = date("d-m-Y_H:i", strtotime($start_date));
        $data['endt1'] = date("d-m-Y_H:i", strtotime($end_date));
        $data['usertype'] = $this->request->getGet('usertype') ? trim($this->request->getGet('usertype')) : 'Keyman';
        $data['distance_range'] = $this->request->getGet('distance_range') ? (int) trim($this->request->getGet('distance_range')) : 4;

        $data['schema'] = $schemaname = $this->schema;
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
                        tsd.expected_distance AS exp_distance_travelled,
                        t.totaldistancetravel,
                        t.avg_speed AS average_speed, -- Include avg_speed column
                        t.startbattery AS start_battery, -- Include start battery column
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
                        LEFT JOIN {$this->schema}.trip_schedule_details tsd 
                            ON tsd.schedule_details_id = tbl_trip.schedule_details_id 
                        LEFT JOIN {$this->schema}.trip_schedule ts 
                            ON ts.schedule_id = tsd.schedule_id 
                        WHERE ( (sttimestamp BETWEEN ? AND ?)
                                OR (endtimestamp   BETWEEN ? AND ?)
                                OR (sttimestamp < ? AND endtimestamp > ?))
                    ) t 
                        ON t.deviceid = mda.deviceid
                    LEFT JOIN {$this->schema}.trip_schedule_details tsd 
                        ON tsd.schedule_details_id = t.schedule_details_id 
                    WHERE 
                        ul.group_id = 2
                        AND mda.active = 1
                        AND mda.user_id IN ($placeholders)
                ),  
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
                        COALESCE(exp_distance_travelled, 0) AS expected_distance,
                        COALESCE(totaldistancetravel, 0) AS actual_distance,
                        COUNT(CASE WHEN average_speed > 5 THEN 1 END) AS over_speed_count, 
                        COUNT(CASE WHEN start_battery < 50 THEN 1 END) AS low_battery_count 
                    FROM base_data
                    GROUP BY organisation, user_id, parent_organisation, allocation_status, device_imei, device_name, exp_distance_travelled, totaldistancetravel
                )
                SELECT 
                    organisation,
                    user_id,
                    parent_organisation,
                    COUNT(CASE 
                        WHEN allocation_status = 'Allocated' 
                        AND has_trip = 1 
                        AND actual_distance < ? 
                        THEN 1 
                    END) AS defaulter_count,
                    COUNT(CASE 
                        WHEN allocation_status = 'Not Allocated' 
                        THEN 1 
                    END) AS not_allocated_count,
                    COUNT(CASE 
                        WHEN allocation_status = 'Allocated' 
                        AND has_trip = 0 
                        THEN 1 
                    END) AS device_off_count,
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
                    END) AS beats_not_covered_count,
                    SUM(over_speed_count) AS over_speed_count, -- Total over-speeding devices
                    SUM(low_battery_count) AS low_battery_count -- Total devices with low battery
                FROM device_status
                GROUP BY organisation, user_id, parent_organisation;";

        $parameters = [$start_date, $end_date, $start_date, $end_date, $start_date, $end_date];
        $parameters = array_merge($parameters, $allowedUsers);
        $parameters = array_merge($parameters, array($data['distance_range']));

        $result = $this->db->query($sql, $parameters)->getResult();

        $alldata = $data['alldata'] = $result;

        if ($this->request->getGet('download') == 'xlsx') {
            // Define headers
            $dat[1]['A'] = "PWay";
            $dat[1]['B'] = "Section";
            $dat[1]['C'] = "No. Of Devices";
            $dat[1]['D'] = "Patrolling Done";
            $dat[1]['E'] = "Patrolling Not Completed";
            $dat[1]['F'] = "No Data Available";

            // Initialize counters
            $Key = 1;
            $total_devices = 0;
            $patrolling_done = 0;
            $patrolling_not_completed = 0;
            $no_data_available = 0;

            // Populate data rows
            foreach ($alldata as $irow) {
                $devices = $irow->beats_not_covered_count + $irow->device_off_count + $irow->beats_covered_count + $irow->not_allocated_count;

                $total_devices += $devices;
                $patrolling_done += $irow->beats_covered_count;
                $patrolling_not_completed += $irow->beats_not_covered_count;
                $no_data_available += $irow->device_off_count;

                // Fill data for each row
                $dat[$Key + 1]['A'] = $irow->parent_organisation;
                $dat[$Key + 1]['B'] = $irow->organisation;
                $dat[$Key + 1]['C'] = $devices;
                $dat[$Key + 1]['D'] = $irow->beats_covered_count;
                $dat[$Key + 1]['E'] = $irow->beats_not_covered_count;
                $dat[$Key + 1]['F'] = $irow->device_off_count;

                $Key++;
            }

            // Add summary row
            $dat[$Key + 1]['A'] = '';
            $dat[$Key + 1]['B'] = 'Total';
            $dat[$Key + 1]['C'] = $total_devices;
            $dat[$Key + 1]['D'] = $patrolling_done;
            $dat[$Key + 1]['E'] = $patrolling_not_completed;
            $dat[$Key + 1]['F'] = $no_data_available;

            // Call the helper function to download the Excel file
            excelDownload($dat, 'Summary_Report_' . date('Ymd') . '.xlsx');
        }

         if ($this->request->getGet('download') == 'pdf') {
            $html = '<html>
                <head>
                    <style>
                        body { font-family: Arial, sans-serif; font-size: 11px; }
                        h2 { text-align: center; margin-bottom: 5px; }
                        .report-meta { text-align: center; font-size: 10px; margin-bottom: 15px; }
                        table { width: 100%; border-collapse: collapse; }
                        th, td { border: 1px solid black; padding: 6px; text-align: center; }
                        th { background-color: #f2f2f2; }
                    </style>
                </head>
                <body>

                <h2>Summery Report '. $start_date .' to '. $end_date .'</h2>
                <p class="report-meta">Generated On: ' . date("Y-m-d H:i:s") . '</p>

                <table>
                    <thead>
                        <tr>
                            <th>PWay</th>
                            <th>Section</th>
                            <th>No. Of Devices</th>
                            <th>Patrolling Done</th>
                            <th>Patrolling Not Completed</th>
                            <th>No Data Available</th>
                        </tr>
                    </thead>
                    <tbody>';

            if (!empty($alldata)) {
                foreach ($alldata as $irow) {
                    $devices = $irow->beats_not_covered_count + $irow->device_off_count + $irow->beats_covered_count + $irow->not_allocated_count;

                    $html .= '<tr>
                        <td>' . $irow->parent_organisation . '</td>
                        <td>' . $irow->organisation . '</td>
                        <td>' . $devices . '</td>
                        <td>' . $irow->beats_covered_count . '</td>
                        <td>' . $irow->beats_not_covered_count . '</td>
                        <td>' . $irow->device_off_count . '</td>
                    </tr>';
                }

                $totalDevices = array_sum(array_column($alldata, 'beats_not_covered_count')) + 
                                array_sum(array_column($alldata, 'device_off_count')) + 
                                array_sum(array_column($alldata, 'beats_covered_count')) + 
                                array_sum(array_column($alldata, 'not_allocated_count'));

                $total_beats_covered_count = array_sum(array_column($alldata,'beats_covered_count'));
                $total_beats_not_covered_count = array_sum(array_column($alldata,'beats_not_covered_count'));
                $total_device_off_count = array_sum(array_column($alldata,'device_off_count'));

                $html .= '<tr>
                    <td colspan="2">Total</td>
                    <td>' . ($totalDevices) . '</td>
                    <td>' . ($total_beats_covered_count) . '</td>
                    <td>' . ($total_beats_not_covered_count) . '</td>
                    <td>' . ($total_device_off_count) . '</td>
                    </tr>';

            } else {
                $html .= '<tr><td colspan="5">No Data available!</td></tr>';
            }
            $html .= '</tbody></table></body></html>';

            $filename = 'Summery_Report_' . date('Y-m-d_H:i:s') . '.pdf';
            try{

                ini_set('memory_limit', '2048M');
                $makePDF = new \App\Libraries\MakePDF();
                $makePDF->setFileName($filename);
                $makePDF->setContent($html);

                $makePDF->getPdf(true); 
                return;
            }catch (\Exception $e) {
                log_message('error', 'PDF generation failed: ' . $e->getMessage());
                return redirect()->back()->with('error', 'Failed to generate PDF. Please try again.');;
            }

         }

        $data['middle'] = view('devices/summery_report_new', $data);
        return view('mainlayout', $data);
    }

 
    public function keyManSummary()
    {
         if (!session()->has('login_sess_data')) {
            return redirect()->to('/');
        }

        $db = \Config\Database::connect();
        $data = [];
        $data['sessdata'] = $this->sessdata;
        $data['page_title'] = "Keymen Summary Report";
        $data['usersdd'] = $this->commonModel->get_users();

        $user_id = $this->sessdata['user_id'];
        $subUsers = getSubUsers($user_id, $this->db);
        $allowedUsers = array_merge([$user_id], $subUsers);
        $placeholders = implode(',', array_fill(0, count($allowedUsers), '?'));

        // Get search parameters from the request
        $fromDate = $this->request->getGet('fromDate');
        $fromTime = $this->request->getGet('fromTime');
        $toDate = $this->request->getGet('toDate');
        $toTime = $this->request->getGet('toTime');

        // Default values if no search parameters are provided
        $fromDateTime = $fromDate && $fromTime ? "$fromDate $fromTime" : date('Y-m-d 00:00:00');
        $toDateTime = $toDate && $toTime ? "$toDate $toTime" : date('Y-m-d 23:59:59');

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
                        tsd.expected_distance AS exp_distance_travelled,
                        t.totaldistancetravel,
                        t.avg_speed AS average_speed,
                        t.startbattery AS start_battery,
                        t.sttimestamp,
                        t.endtimestamp,
                        EXTRACT(EPOCH FROM (t.endtimestamp - t.sttimestamp))/3600 AS trip_duration,
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
                        WHERE ( (sttimestamp BETWEEN ? AND ?)
                                OR (endtimestamp   BETWEEN ? AND ?)
                                OR (sttimestamp < ? AND endtimestamp > ?))
                    ) t 
                    ON t.deviceid = mda.deviceid
                    LEFT JOIN {$this->schema}.trip_schedule_details tsd 
                    ON tsd.schedule_details_id = t.schedule_details_id 
                    WHERE 
                        ul.group_id = 2
                        AND mda.active = 1
                        AND mda.user_id IN ($placeholders)
                ),
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
                        COALESCE(exp_distance_travelled, 0) AS expected_distance,
                        COALESCE(totaldistancetravel, 0) AS actual_distance,
                        COUNT(CASE WHEN average_speed > 5 THEN 1 END) AS over_speed_count,
                        COUNT(CASE WHEN start_battery < 50 THEN 1 END) AS low_battery_count,
                        MAX(sttimestamp) AS sttimestamp,
                        MAX(endtimestamp) AS endtimestamp,
                        MAX(trip_duration) AS trip_duration
                    FROM base_data
                    GROUP BY organisation, user_id, parent_organisation, allocation_status, device_imei, device_name, exp_distance_travelled, totaldistancetravel
                )
                SELECT 
                    organisation,
                    user_id,
                    parent_organisation,
                    COUNT(*) AS total_devices,
                    COUNT(CASE WHEN actual_distance > 0 THEN 1 END) AS working_devices,
                    COUNT(CASE WHEN has_trip = 0 THEN 1 END) AS off_gps_count,
                    STRING_AGG(CASE WHEN has_trip = 0 THEN device_name END, ', ') AS off_gps_details,
                    STRING_AGG(CASE WHEN trip_duration < 2 THEN device_name END, ', ') AS less_than_2_hour_working,
                    STRING_AGG(CASE WHEN trip_duration BETWEEN 2 AND 4 THEN device_name END, ', ') AS two_to_four_hour_working,
                    STRING_AGG(CASE WHEN actual_distance < 4 THEN device_name END, ', ') AS less_than_4_km_travelling,
                    STRING_AGG(CASE WHEN DATE_PART('hour', sttimestamp) > 5 THEN device_name END, ', ') AS on_device_after_5am,
                    STRING_AGG(CASE WHEN DATE_PART('hour', endtimestamp) < 15 THEN device_name END, ', ') AS off_device_before_3pm
                FROM device_status
                GROUP BY organisation, user_id, parent_organisation;";

        $parameters = [$fromDateTime, $toDateTime, $fromDateTime, $toDateTime, $fromDateTime, $toDateTime];
        $parameters = array_merge($parameters, $allowedUsers);

        $result = $db->query($sql, $parameters)->getResult();
        // print_r($result); die();
        // Pass data to the view
        $data['alldata'] = $result;
        $data['fromDate'] = $fromDate;
        $data['fromTime'] = $fromTime;
        $data['toDate'] = $toDate;
        $data['toTime'] = $toTime;

        // Handle XLSX download
        if ($this->request->getGet('download') == 'xlsx') {
            $filename = 'Keyman_Summary_Report_' . date('Y-m-d_H:i:s') . '.xlsx';

            $dat[0]['A'] = "Keyman Summary Report from $fromDateTime to $toDateTime";
            
            $dat[1]['A'] = 'PWay';
            $dat[1]['B'] = 'Section';
            $dat[1]['C'] = 'Total Devices';
            $dat[1]['D'] = 'Working Devices';
            $dat[1]['E'] = 'Off GPS Count';
            $dat[1]['F'] = 'Off GPS Details';
            $dat[1]['G'] = 'Less than 2 Hour Working';
            $dat[1]['H'] = '2 to 4 Hour Working';
            $dat[1]['I'] = 'Less than 4 KM Travelling';
            $dat[1]['J'] = 'On Device After 5AM';
            $dat[1]['H'] = 'Off Device Before 3PM';

            $count = 2;
            foreach ($result as $row) {
                $dat[$count]['A'] = $row->parent_organisation;
                $dat[$count]['B'] = $row->organisation;
                $dat[$count]['C'] = $row->total_devices;
                $dat[$count]['D'] = $row->working_devices;
                $dat[$count]['E'] = $row->off_gps_count;
                $dat[$count]['F'] = $row->off_gps_details;
                $dat[$count]['G'] = $row->less_than_2_hour_working;
                $dat[$count]['H'] = $row->two_to_four_hour_working;
                $dat[$count]['I'] = $row->less_than_4_km_travelling;
                $dat[$count]['J'] = $row->on_device_after_5am;
                $dat[$count]['H'] = $row->off_device_before_3pm;
                $count++;
            }

            exceldownload($dat, $filename);
        }

        // Handle PDF download
        if ($this->request->getGet('download') == 'pdf') {
            $html = '<html>
                <head>
                    <style>
                        body { font-family: Arial, sans-serif; font-size: 8px; }
                        h2 { text-align: center; margin-bottom: 5px; }
                        .report-meta { text-align: center; font-size: 10px; margin-bottom: 15px; }
                        table { width: 100%; border-collapse: collapse; }
                        th, td { border: 1px solid black; padding: 3px; text-align: center; }
                        th { background-color: #f2f2f2; }
                    </style>
                </head>
                <body>

                <h2>Keyman Summary Report</h2>
                <p class="report-meta">Generated On: ' . date("Y-m-d H:i:s") . '</p>

                <table>
                    <thead>
                        <tr>
                            <th>PWay</th>
                            <th>Section</th>
                            <th>Total Devices</th>
                            <th>Working Devices</th>
                            <th>Off GPS Count</th>
                            <th>Off GPS Details</th>
                            <th>Less than 2 Hour Working</th>
                            <th>2 to 4 Hour Working</th>
                            <th>Less than 4 KM Travelling</th>
                            <th>On Device After 5AM</th>
                            <th>Off Device Before 3PM</th>
                        </tr>
                    </thead>
                    <tbody>';

            foreach ($result as $row) {
                $html .= '<tr>
                    <td>' . $row->parent_organisation . '</td>
                    <td>' . $row->organisation . '</td>
                    <td>' . $row->total_devices . '</td>
                    <td>' . $row->working_devices . '</td>
                    <td>' . $row->off_gps_count . '</td>
                    <td>' . $row->off_gps_details . '</td>
                    <td>' . $row->less_than_2_hour_working . '</td>
                    <td>' . $row->two_to_four_hour_working . '</td>
                    <td>' . $row->less_than_4_km_travelling . '</td>
                    <td>' . $row->on_device_after_5am . '</td>
                    <td>' . $row->off_device_before_3pm . '</td>
                </tr>';
            }

            $html .= '</tbody></table></body></html>';

            $filename = 'Keyman_Summary_Report_' . date('Y-m-d_H:i:s') . '.pdf';

            try {
                ini_set('memory_limit', '2048M');
                $makePDF = new \App\Libraries\MakePDF();
                $makePDF->setFileName($filename);
                $makePDF->setContent($html);
                $makePDF->getPdf(true);
                return;
            } catch (\Exception $e) {
                log_message('error', 'PDF generation failed: ' . $e->getMessage());
                return redirect()->back()->with('error', 'Failed to generate PDF. Please try again.');
            }
        }

        $data['middle'] = view('devices/keyman_summary', $data);
        return view('mainlayout', $data);
    }

}
