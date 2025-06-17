<?php

namespace App\Controllers;

use App\Models\TripScheduleModel;
use App\Models\TripScheduleDetailsModel;
use CodeIgniter\Controller;

class TripScheduleController extends Controller
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
        $tripScheduleModel = new TripScheduleModel();

        $data = [];
        $data['sessdata'] = $this->sessdata;
        $data['page_title'] = "Trip Schedule";
        $data['usersdd'] = $this->commonModel->get_users();

        // ✅ Ensure Schema Reference is Available
        $schema = $this->sessdata['schemaname'] ?? 'public';
        $table = "{$schema}.trip_schedule";
        $detailsTable = "{$schema}.trip_schedule_details";
        $userTable = "public.user_login";

        $data['tripSchedules'] = []; // Default empty
        $data['pager'] = ''; // Prevent undefined error

        /*if ($this->request->getMethod() == 'POST') {
            // echo "<pre>";print_r($this->request->getPost());exit();
            $start_date = date("Y-m-d", strtotime(trim($this->request->getPost('start_date'))));
            $end_date = date("Y-m-d", strtotime(trim($this->request->getPost('end_date'))));
            $data['stdt'] = date("d-m-Y", strtotime(trim($this->request->getPost('start_date'))));
            $data['endt'] = date("d-m-Y", strtotime(trim($this->request->getPost('end_date'))));
            $data['schema'] = $schemaname = $this->schema;
            $data['device_id'] = $device_id = trim($this->request->getPost('deviceid'));

            $user_id = $this->sessdata['user_id'];

            $sql = "SELECT 
                ts.*, 
                (SELECT COUNT(*) FROM $detailsTable d WHERE d.schedule_id = ts.schedule_id) AS trip_count, 
                pwi.organisation AS pwi_name, 
                section.organisation AS section_name
                FROM $table ts
                LEFT JOIN $userTable pwi ON pwi.user_id = ts.pwi_id
                LEFT JOIN $userTable section ON section.user_id = ts.section_id
                WHERE 
                (
                        (ts.expected_start_date BETWEEN ? AND ?)
                    AND  (ts.expected_end_date   BETWEEN ? AND ?) 
                ) 
                 ";

                $parameters = [$start_date, $end_date,$start_date, $end_date];


                // ✅ Apply Filters
                if (!empty($deviceId)) {
                    $sql .= " AND ts.deviceid = ?";
                    $parameters[] = $deviceId;
                }

            $sql .= " ORDER BY ts.expected_start_date ASC";

            // echo $sql;exit();

            $result = $this->db->query($sql, $parameters)->paginate(20);


            $data['tripSchedules'] = $result;
            $data['pager'] = $result->pager;
        }*/

        if ($this->request->getMethod() == 'POST') {
            $start_date = date("Y-m-d", strtotime(trim($this->request->getPost('start_date'))));
            $end_date = date("Y-m-d", strtotime(trim($this->request->getPost('end_date'))));
            $data['stdt'] = date("d-m-Y", strtotime($start_date));
            $data['endt'] = date("d-m-Y", strtotime($end_date));
            $data['schema'] = $schemaname = $this->schema;
            $device_id = trim($this->request->getPost('deviceid'));
            $data['device_id'] = $device_id;
        
            $parameters = [$start_date, $end_date, $start_date, $end_date];
        
            $sql = "FROM $table ts
                LEFT JOIN $userTable pwi ON pwi.user_id = ts.pwi_id
                LEFT JOIN $userTable section ON section.user_id = ts.section_id
                WHERE (ts.expected_start_date BETWEEN ? AND ?)
                  AND (ts.expected_end_date BETWEEN ? AND ?)";
        
            if (!empty($device_id)) {
                $sql .= " AND ts.deviceid = ?";
                $parameters[] = $device_id;
            }
        
            // Count total records
            $countResult = $this->db->query("SELECT COUNT(*) as total $sql", $parameters)->getRow();
            $total = $countResult->total ?? 0;
        
            // Pagination setup
            $perPage = 20;
            $page = (int) ($this->request->getGet('page') ?? 1);
            $offset = ($page - 1) * $perPage;
        
            // Final SQL with LIMIT and OFFSET
            $finalSQL = "
                SELECT 
                    ts.*, 
                    (SELECT COUNT(*) FROM $detailsTable d WHERE d.schedule_id = ts.schedule_id) AS trip_count,
                    pwi.organisation AS pwi_name,
                    section.organisation AS section_name
                $sql
                ORDER BY ts.expected_start_date ASC
                --LIMIT $perPage OFFSET $offset
            ";
        
            $result = $this->db->query($finalSQL, $parameters)->getResult();
            /*$pager = \Config\Services::pager();
            $pagerLinks = $pager->makeLinks($page, $perPage, $total);*/
        
            $data['tripSchedules'] = $result;
            // $data['pager'] = $pagerLinks;
        }        
        
        // ✅ Load View
        $data['middle'] = view('trips/trip_schedule_list', $data);
        return view('mainlayout', $data);
    }

    public function delete($scheduleId)
    {
        $tripScheduleModel = new TripScheduleModel();
        $tripDetailsModel = new TripScheduleDetailsModel();

        // ✅ Ensure the schedule exists
        $schedule = $tripScheduleModel->find($scheduleId);
        if (!$schedule) {
            return redirect()->to(site_url('trip-schedule'))->with('error', 'Schedule not found.');
        }

        try {
            // ✅ Delete all related trips first
            $tripDetailsModel->where('schedule_id', $scheduleId)->delete();

            // ✅ Delete the schedule
            $tripScheduleModel->softDelete($scheduleId);

            return redirect()->to(site_url('trip-schedule'))->with('success', 'Schedule deleted successfully.');
        } catch (\Exception $e) {
            return redirect()->to(site_url('trip-schedule'))->with('error', 'Error deleting schedule: ' . $e->getMessage());
        }
    }

    public function softDelete($scheduleId)
    {
        return $this->update($scheduleId, ['active' => false]);
    }

    public function upload__old()
    {
        $data = [];
        $data['sessdata'] = $this->sessdata;
        $data['page_title'] = "Upload Trip Schedule";
        $data['usersdd'] = $this->commonModel->get_users();
        $data['pwiMapping'] = $this->getPwiSectionMapping();
        $deviceMapping = $this->getDeviceMapping(); // IMEI → deviceid mapping

        $errorLogs = [];

        if ($this->request->getMethod() == 'POST') {
            $file = $this->request->getFile('csv_file');
            $startDate = $this->request->getPost('start_date');
            $endDate = $this->request->getPost('end_date');
        
            if (!$file->isValid() || empty($startDate) || empty($endDate)) {
                $data['error'] = 'Invalid file upload or missing dates.';
            } else {
                $csvData = array_map('str_getcsv', file($file->getTempName()));
                array_shift($csvData); // Remove header row

                $tripScheduleModel = new TripScheduleModel();
                $tripDetailsModel = new TripScheduleDetailsModel();
                $scheduleIds = [];
                $insertedCount = 0;
                $skippedCount = 0;

                // 🔹 Convert Start & End Dates to Loopable Format
                $startDate = new \DateTime($startDate);
                $endDate = new \DateTime($endDate);
                $interval = new \DateInterval('P1D'); // 1 Day Increment
                $dateRange = new \DatePeriod($startDate, $interval, $endDate->modify('+1 day'));

            

                foreach ($dateRange as $date) {
                    $currentDate = $date->format('Y-m-d');
                    $deviceEndTimes = []; // Store last end time for each device per day
                    foreach ($csvData as $rowIndex => $row) {
                        
                        if (count($row) < 10) {
                            $errorLogs[] = "Row $rowIndex: Invalid data format (expected 10+ columns). Skipping.";
                            continue;
                        }

                        $imei = trim($row[0]);
                        if (!isset($deviceMapping[$imei])) {
                            $errorLogs[] = "Row $rowIndex: IMEI Not Found ($imei). Skipping.";
                            continue;
                        }
                        
                        $deviceid = $deviceMapping[$imei]; // Get the mapped deviceid
                        $sectionName = trim($row[1]);
                        $userType = trim($row[2]);
                        $startBeat = trim($row[3]);
                        $startLat = !empty($row[4]) ? trim($row[4]) : null;
                        $startLon = !empty($row[5]) ? trim($row[5]) : null;
                        $endBeat = trim($row[6]);
                        $endLat = !empty($row[7]) ? trim($row[7]) : null;
                        $endLon = !empty($row[8]) ? trim($row[8]) : null;
                        $startTime = !empty($row[9]) ? trim($row[9]) : null;;
                        $endTime = trim($row[10]);
                        
                        $travelled_dist = trim($row[11]);
                        $tripno = trim($row[12]);
                        $block_name = trim($row[13]);
                        $pway = trim($row[14]);
                        
                        
                        // 🔹 Ensure `endTime` is not null
                        if (empty($endTime)) {
                            $errorLogs[] = "Row $rowIndex: Missing end time for device $deviceid. Setting it to start time.";
                            $endTime = $startTime; // Temporary fix to avoid NULL violation
                        }

                        if (!isset($deviceEndTimes[$deviceid]) || strtotime($endTime) > strtotime($deviceEndTimes[$deviceid])) {
                            $deviceEndTimes[$deviceid] = $endTime; // Keep track of the latest end time
                        }

                        if (!isset($data['pwiMapping'][$sectionName])) {
                            $errorLogs[] = "Row $rowIndex: Invalid Section Name ($sectionName). Skipping.";
                            continue;
                        }

                        $pwiId = $data['pwiMapping'][$sectionName]['pwi_id'];
                        $sectionId = $data['pwiMapping'][$sectionName]['section_id'];

                        // 🔹 Check if schedule already exists for this device on this date
                        if (!isset($scheduleIds[$deviceid][$currentDate])) {
                            $existingSchedule = $tripScheduleModel->where([
                                'deviceid' => $deviceid,
                                'expected_start_date' => $currentDate,
                                'expected_end_date' => $currentDate
                            ])->first();

                            if (!$existingSchedule) {
                                try {
                                    //Assign Device to pole
                                    if ($deviceid && $startBeat && $endBeat && $userType && $block_name) {
                                        // Call the stored function (using a query for example)
                                        $this->db->query("SELECT public.copy_master_assigne_pole_data_table(?, ?, ?, ?, ?)", [$deviceid, $startBeat, $endBeat, $userType, $block_name]);
                                    }

                                    $query3 = $this->db->query("SELECT msd.device_name AS device_name 
                                        FROM  {$this->schema}.master_device_setup msd
                                        WHERE msd.deviceid = ?
                                    ", [$deviceid]);

                                    $result3 = $query3->getRow();

                                    // 🔹 Insert New Schedule (ONE PER DEVICE PER DAY)
                                    $scheduleId = $tripScheduleModel->insert([
                                        'deviceid' => $deviceid,
                                        'imeino' => $imei,
                                        'section_name' => $sectionName,
                                        'pwi_id' => $pwiId,
                                        'section_id' => $sectionId,
                                        'device_type' => $userType,
                                        'expected_start_date' => $currentDate,
                                        'expected_start_time' => $startTime,
                                        'expected_end_date' => $currentDate,
                                        'expected_end_time' => $endTime, // ✅ Ensured Not NULL
                                        'active' => true,
                                        'devicename' => $result3->device_name ?? "", 
                                    ], true);
                                    
                                    $scheduleIds[$deviceid][$currentDate] = $scheduleId;
                                    $insertedCount++;

                                } catch (\Exception $e) {
                                    $errorLogs[] = "Row $rowIndex: SQL Error (Schedule Insert) - " . $e->getMessage();
                                    continue;
                                }
                            } else {
                            
                                $scheduleId = $existingSchedule['schedule_id'];
                                $scheduleIds[$deviceid][$currentDate] = $scheduleId;
                            }
                        } else {
                            $scheduleId = $scheduleIds[$deviceid][$currentDate];
                        }

                        try {
                            // 🔹 Insert ALL TRIPS under the same `schedule_id`
                            $tripDetailsModel->insert([
                                'schedule_id' => $scheduleId,
                                'expected_stpole' => $startBeat,
                                'expected_stlat' => $startLat,
                                'expected_stlon' => $startLon,
                                'expected_start_datetime' => $currentDate . ' ' . $startTime,
                                'expected_endpole' => $endBeat,
                                'expected_endlat' => $endLat,
                                'expected_endlon' => $endLon,
                                'expected_end_datetime' => $currentDate . ' ' . $endTime,
                                'trip_status' => 'Not Started',
                                'expected_distance' => $travelled_dist,
                                'trip_no' => $tripno
                            ]);
                        } catch (\Exception $e) {
                            $errorLogs[] = "Row $rowIndex: SQL Error (Trip Insert) - " . $e->getMessage();
                        }
                    }

                    // 🔹 Update `expected_end_time` for each schedule
                    foreach ($deviceEndTimes as $deviceid => $finalEndTime) {
                        $tripScheduleModel->where([
                            'deviceid' => $deviceid,
                            'expected_start_date' => $currentDate,
                            'expected_end_date' => $currentDate
                        ])->set([
                            'expected_end_time' => $finalEndTime
                        ])->update();
                    }
                }

                if(count($errorLogs) > 1) {
                    $data['error'] = $errorLogs;
                } else {
                    $data['success'] = "CSV uploaded successfully. Inserted: $insertedCount schedules.";
                }
                
            }
        }

        $data['middle'] = view('trips/trip_upload', $data);
        return view('mainlayout', $data);
    }

    public function upload__()
    {
        $data = [];
        $data['sessdata'] = $this->sessdata;
        $data['page_title'] = "Upload Trip Schedule";
        $data['usersdd'] = $this->commonModel->get_users();
        $data['pwiMapping'] = $this->getPwiSectionMapping();
        $deviceMapping = $this->getDeviceMapping(); // IMEI → deviceid mapping

        $errorLogs = [];

        if ($this->request->getMethod() == 'POST') {
            $file = $this->request->getFile('csv_file');
            $startDate = $this->request->getPost('start_date');
            $endDate = $this->request->getPost('end_date');
        
            if (!$file->isValid() || empty($startDate) || empty($endDate)) {
                $data['error'] = 'Invalid file upload or missing dates.';
            } else {
                $csvData = array_map('str_getcsv', file($file->getTempName()));
                array_shift($csvData); // Remove header row

                $tripScheduleModel = new TripScheduleModel();
                $tripDetailsModel = new TripScheduleDetailsModel();
                $scheduleIds = [];
                $insertedCount = 0;
                $skippedCount = 0;

                // 🔹 Convert Start & End Dates to Loopable Format
                $startDate = new \DateTime($startDate);
                $endDate = new \DateTime($endDate);
                $interval = new \DateInterval('P1D'); // 1 Day Increment
                $dateRange = new \DatePeriod($startDate, $interval, $endDate->modify('+1 day'));

            

                foreach ($dateRange as $date) {
                    $currentDate = $date->format('Y-m-d');
                    $deviceEndTimes = []; // Store last end time for each device per day
                    foreach ($csvData as $rowIndex => $row) {
                        
                        if (count($row) < 10) {
                            $errorLogs[] = "Row $rowIndex: Invalid data format (expected 10+ columns). Skipping.";
                            continue;
                        }

                        $imei = trim($row[0]);
                        if (!isset($deviceMapping[$imei])) {
                            $errorLogs[] = "Row $rowIndex: IMEI Not Found ($imei). Skipping.";
                            continue;
                        }
                        
                        $deviceid = $deviceMapping[$imei]; // Get the mapped deviceid
                        $sectionName = trim($row[1]);
                        $userType = trim($row[2]);
                        $startBeat = trim($row[3]);
                        $startLat = !empty($row[4]) ? trim($row[4]) : null;
                        $startLon = !empty($row[5]) ? trim($row[5]) : null;
                        $endBeat = trim($row[6]);
                        $endLat = !empty($row[7]) ? trim($row[7]) : null;
                        $endLon = !empty($row[8]) ? trim($row[8]) : null;
                        $startTime = !empty($row[9]) ? trim($row[9]) : null;;
                        $endTime = trim($row[10]);
                        
                        $travelled_dist = trim($row[11]);
                        $tripno = trim($row[12]);
                        $block_name = trim($row[13]);
                        $pway = trim($row[14]);
                        
                        
                        // 🔹 Ensure `endTime` is not null
                        if (empty($endTime)) {
                            $errorLogs[] = "Row $rowIndex: Missing end time for device $deviceid. Setting it to start time.";
                            $endTime = $startTime; // Temporary fix to avoid NULL violation
                        }

                        if (!isset($deviceEndTimes[$deviceid]) || strtotime($endTime) > strtotime($deviceEndTimes[$deviceid])) {
                            $deviceEndTimes[$deviceid] = $endTime; // Keep track of the latest end time
                        }

                        if (!isset($data['pwiMapping'][$sectionName])) {
                            $errorLogs[] = "Row $rowIndex: Invalid Section Name ($sectionName). Skipping.";
                            continue;
                        }

                        $pwiId = $data['pwiMapping'][$sectionName]['pwi_id'];
                        $sectionId = $data['pwiMapping'][$sectionName]['section_id'];

                        // 🔹 Check if schedule already exists for this device on this date
                        if (!isset($scheduleIds[$deviceid][$currentDate])) {
                            $existingSchedule = $tripScheduleModel->where([
                                'deviceid' => $deviceid,
                                'expected_start_date' => $currentDate,
                                'expected_end_date' => $currentDate
                            ])->first();

                            if ($existingSchedule) {
                                $this->db->table($this->schema.'.trip_schedule')->delete(['schedule_id' => $existingSchedule['schedule_id']]);
                            }

                            try {
                                //Assign Device to pole
                                if ($deviceid && $startBeat && $endBeat && $userType && $block_name) {
                                    // Call the stored function (using a query for example)
                                    $this->db->query("SELECT public.copy_master_assigne_pole_data_table(?, ?, ?, ?, ?)", [$deviceid, $startBeat, $endBeat, $userType, $block_name]);
                                }

                                $query3 = $this->db->query("SELECT msd.device_name AS device_name 
                                    FROM  {$this->schema}.master_device_setup msd
                                    WHERE msd.deviceid = ?
                                ", [$deviceid]);

                                $result3 = $query3->getRow();

                                // 🔹 Insert New Schedule (ONE PER DEVICE PER DAY)
                                $scheduleId = $tripScheduleModel->insert([
                                    'deviceid' => $deviceid,
                                    'imeino' => $imei,
                                    'section_name' => $sectionName,
                                    'pwi_id' => $pwiId,
                                    'section_id' => $sectionId,
                                    'device_type' => $userType,
                                    'expected_start_date' => $currentDate,
                                    'expected_start_time' => $startTime,
                                    'expected_end_date' => $currentDate,
                                    'expected_end_time' => $endTime, // ✅ Ensured Not NULL
                                    'active' => true,
                                    'devicename' => $result3->device_name ?? "", 
                                ], true);
                                
                                $scheduleIds[$deviceid][$currentDate] = $scheduleId;
                                $insertedCount++;

                            } catch (\Exception $e) {
                                $errorLogs[] = "Row $rowIndex: SQL Error (Schedule Insert) - " . $e->getMessage();
                                continue;
                            }
                            /*} else {
                            
                                $scheduleId = $existingSchedule['schedule_id'];
                                $scheduleIds[$deviceid][$currentDate] = $scheduleId;
                            }*/
                        } else {
                            $scheduleId = $scheduleIds[$deviceid][$currentDate];
                        }

                        try {
                            // 🔹 Insert ALL TRIPS under the same `schedule_id`
                            $tripDetailsModel->insert([
                                'schedule_id' => $scheduleId,
                                'expected_stpole' => $startBeat,
                                'expected_stlat' => $startLat,
                                'expected_stlon' => $startLon,
                                'expected_start_datetime' => $currentDate . ' ' . $startTime,
                                'expected_endpole' => $endBeat,
                                'expected_endlat' => $endLat,
                                'expected_endlon' => $endLon,
                                'expected_end_datetime' => $currentDate . ' ' . $endTime,
                                'trip_status' => 'Not Started',
                                'expected_distance' => $travelled_dist,
                                'trip_no' => $tripno
                            ]);
                        } catch (\Exception $e) {
                            $errorLogs[] = "Row $rowIndex: SQL Error (Trip Insert) - " . $e->getMessage();
                        }
                    }

                    // 🔹 Update `expected_end_time` for each schedule
                    foreach ($deviceEndTimes as $deviceid => $finalEndTime) {
                        $tripScheduleModel->where([
                            'deviceid' => $deviceid,
                            'expected_start_date' => $currentDate,
                            'expected_end_date' => $currentDate
                        ])->set([
                            'expected_end_time' => $finalEndTime
                        ])->update();
                    }
                }

                if(count($errorLogs) > 1) {
                    $data['error'] = $errorLogs;
                } else {
                    $data['success'] = "CSV uploaded successfully. Inserted: $insertedCount schedules.";
                }
                
            }
        }

        $data['middle'] = view('trips/trip_upload', $data);
        return view('mainlayout', $data);
    }

    public function upload()
    {
        // Increase limits if needed
        ini_set('memory_limit', '512M');
        ini_set('max_execution_time', 600); // 10 minutes
        
        $data = [];
        $data['sessdata'] = $this->sessdata;
        $data['page_title'] = "Upload Trip Schedule";
        $data['usersdd'] = $this->commonModel->get_users();
        $data['pwiMapping'] = $this->getPwiSectionMapping();
        $deviceMapping = $this->getDeviceMapping(); // IMEI → deviceid mapping

        $errorLogs = [];

        if ($this->request->getMethod() == 'POST') {
            $file = $this->request->getFile('csv_file');
            $start_date = date("Y-m-d", strtotime(trim($this->request->getPost('start_date'))));
            $end_date = date("Y-m-d", strtotime(trim($this->request->getPost('end_date'))));
        
            if (!$file->isValid() || empty($start_date) || empty($end_date)) {
                $data['error'] = 'Invalid file upload or missing dates.';
            } else {
                $csvData = array_map('str_getcsv', file($file->getTempName()));
                array_shift($csvData); // Remove header row


                //delete existing
                $devArr = [];
                foreach ($csvData as $rowIndex => $row) {
                    array_push($devArr,"'".trim($row[0])."'");
                }

                $devPlaceholders = implode(',', $devArr);

                $sql = "SELECT schedule_id FROM {$this->schema}.trip_schedule WHERE 
                (expected_start_date BETWEEN ? AND ?) AND 
                (expected_end_date BETWEEN ? AND ?) AND 
                imeino IN ($devPlaceholders)
                ";

                $parameters = [$start_date, $end_date,$start_date, $end_date];
                $scheduleIds = $this->db->query($sql, $parameters)->getResult();
                
                // If found, delete them
                if (!empty($scheduleIds)) {
                    $idsToDelete = array_column($scheduleIds, 'schedule_id');

                    // Delete using whereIn
                    $this->db->table("{$this->schema}.trip_schedule")
                            ->whereIn('schedule_id', $idsToDelete)
                            ->delete();
                }

                $tripScheduleModel = new TripScheduleModel();
                $tripDetailsModel = new TripScheduleDetailsModel();
                $scheduleIds = [];
                $insertedCount = 0;
                $skippedCount = 0;

                // 🔹 Convert Start & End Dates to Loopable Format
                $startDate = new \DateTime($start_date);
                $endDate = new \DateTime($end_date);
                $interval = new \DateInterval('P1D'); // 1 Day Increment
                $dateRange = new \DatePeriod($startDate, $interval, $endDate->modify('+1 day'));

                $scheduleDetailsInsert = [];
                foreach ($dateRange as $date) {
                    $currentDate = $date->format('Y-m-d');
                    $deviceEndTimes = []; // Store last end time for each device per day
                    
                    foreach ($csvData as $rowIndex => $row) {
                        
                        if (count($row) < 10) {
                            $errorLogs[] = "Row $rowIndex: Invalid data format (expected 10+ columns). Skipping.";
                            continue;
                        }

                        $imei = trim($row[0]);
                        if (!isset($deviceMapping[$imei])) {
                            $errorLogs[] = "Row $rowIndex: IMEI Not Found ($imei). Skipping.";
                            continue;
                        }
                        
                        $deviceid = $deviceMapping[$imei]; // Get the mapped deviceid
                        $sectionName = trim($row[1]);
                        $userType = trim($row[2]);
                        $startBeat = trim($row[3]);
                        $startLat = !empty($row[4]) ? trim($row[4]) : null;
                        $startLon = !empty($row[5]) ? trim($row[5]) : null;
                        $endBeat = trim($row[6]);
                        $endLat = !empty($row[7]) ? trim($row[7]) : null;
                        $endLon = !empty($row[8]) ? trim($row[8]) : null;
                        $startTime = !empty($row[9]) ? trim($row[9]) : null;;
                        $endTime = trim($row[10]);
                        
                        $travelled_dist = trim($row[11]);
                        $tripno = trim($row[12]);
                        $block_name = trim($row[13]);
                        $pway = trim($row[14]);
                        
                        
                        // 🔹 Ensure `endTime` is not null
                        if (empty($endTime)) {
                            $errorLogs[] = "Row $rowIndex: Missing end time for device $deviceid. Setting it to start time.";
                            $endTime = $startTime; // Temporary fix to avoid NULL violation
                        }

                        if (!isset($deviceEndTimes[$deviceid]) || strtotime($endTime) > strtotime($deviceEndTimes[$deviceid])) {
                            $deviceEndTimes[$deviceid] = $endTime; // Keep track of the latest end time
                        }

                        if (!isset($data['pwiMapping'][$sectionName])) {
                            $errorLogs[] = "Row $rowIndex: Invalid Section Name ($sectionName). Skipping.";
                            continue;
                        }

                        $pwiId = $data['pwiMapping'][$sectionName]['pwi_id'];
                        $sectionId = $data['pwiMapping'][$sectionName]['section_id'];

                        // 🔹 Check if schedule already exists for this device on this date
                        if (!isset($scheduleIds[$deviceid][$currentDate])) {
                            /*$existingSchedule = $tripScheduleModel->where([
                                'deviceid' => $deviceid,
                                'expected_start_date' => $currentDate,
                                'expected_end_date' => $currentDate
                            ])->first();

                            if ($existingSchedule) {
                                $this->db->table($this->schema.'.trip_schedule')->delete(['schedule_id' => $existingSchedule['schedule_id']]);
                            }*/

                            try {
                                //Assign Device to pole
                                if ($deviceid && $startBeat && $endBeat && $userType && $block_name) {
                                    // Call the stored function (using a query for example)
                                    $this->db->query("SELECT public.copy_master_assigne_pole_data_table(?, ?, ?, ?, ?)", [$deviceid, $startBeat, $endBeat, $userType, $block_name]);
                                }

                                $query3 = $this->db->query("SELECT msd.device_name AS device_name 
                                    FROM  {$this->schema}.master_device_setup msd
                                    WHERE msd.deviceid = ?
                                ", [$deviceid]);

                                $result3 = $query3->getRow();

                                // 🔹 Insert New Schedule (ONE PER DEVICE PER DAY)
                                $scheduleId = $tripScheduleModel->insert([
                                    'deviceid' => $deviceid,
                                    'imeino' => $imei,
                                    'section_name' => $sectionName,
                                    'pwi_id' => $pwiId,
                                    'section_id' => $sectionId,
                                    'device_type' => $userType,
                                    'expected_start_date' => $currentDate,
                                    'expected_start_time' => $startTime,
                                    'expected_end_date' => $currentDate,
                                    'expected_end_time' => $endTime, // ✅ Ensured Not NULL
                                    'active' => true,
                                    'devicename' => $result3->device_name ?? "", 
                                ], true);
                                
                                $scheduleIds[$deviceid][$currentDate] = $scheduleId;
                                $insertedCount++;
                                

                            } catch (\Exception $e) {
                                $errorLogs[] = "Row $rowIndex: SQL Error (Schedule Insert) - " . $e->getMessage();
                                continue;
                            }

                        } else {
                            $scheduleId = $scheduleIds[$deviceid][$currentDate];
                        }

                        try {
                            // 🔹 Insert ALL TRIPS under the same `schedule_id`
                            $scheduleDetailsInsert[] = [
                                'schedule_id' => $scheduleId,
                                'expected_stpole' => $startBeat,
                                'expected_stlat' => $startLat,
                                'expected_stlon' => $startLon,
                                'expected_start_datetime' => $currentDate . ' ' . $startTime,
                                'expected_endpole' => $endBeat,
                                'expected_endlat' => $endLat,
                                'expected_endlon' => $endLon,
                                'expected_end_datetime' => $currentDate . ' ' . $endTime,
                                'trip_status' => 'Not Started',
                                'expected_distance' => $travelled_dist,
                                'trip_no' => $tripno
                            ];
                        } catch (\Exception $e) {
                            $errorLogs[] = "Row $rowIndex: SQL Error (Trip Insert) - " . $e->getMessage();
                        }
                        
                    }

                   

                    // 🔹 Update `expected_end_time` for each schedule
                    /*foreach ($deviceEndTimes as $deviceid => $finalEndTime) {
                        $tripScheduleModel->where([
                            'deviceid' => $deviceid,
                            'expected_start_date' => $currentDate,
                            'expected_end_date' => $currentDate
                        ])->set([
                            'expected_end_time' => $finalEndTime
                        ])->update();
                    }*/
                }

                
                $chunksDetails = array_chunk($scheduleDetailsInsert, 1500);
                $this->db->table($this->schema.'.trip_schedule_details')->insertBatch($scheduleDetailsInsert);

                // Get the last executed query
                // echo $this->db->getLastQuery();

                if(count($errorLogs) > 1) {
                    $data['error'] = $errorLogs;
                } else {
                    $data['success'] = "CSV uploaded successfully. Inserted: $insertedCount schedules.";
                }
                
            }
        }

        $data['middle'] = view('trips/trip_upload', $data);
        return view('mainlayout', $data);
    }

    private function getDeviceMapping()
    {
        $query = "SELECT imei_no, id AS deviceid FROM public.master_device_details 
        --WHERE active = 2
        ";
        $result = $this->db->query($query)->getResultArray();

        $deviceMapping = [];
        foreach ($result as $row) {
            $deviceMapping[$row['imei_no']] = $row['deviceid'];
        }

        return $deviceMapping;
    }

    public function details($scheduleId)
    {
        $data['page_title'] = "Trip Schedule Details";
        $tripDetailsModel = new TripScheduleDetailsModel();

        $data['tripDetails'] = $tripDetailsModel->where('schedule_id', $scheduleId)->findAll();
        // print_r($data); die('----');
        // Render the view inside `mainlayout`
        $data['middle'] = view('trips/trip_schedule_details', $data);
        return view('mainlayout', $data);
    }

    private function getPwiSectionMapping()
    {
        $query = "
            SELECT 
                pwi.user_id AS pwi_id, 
                section.user_id AS section_id, 
                section.organisation AS section_name
            FROM public.user_login AS pwi
            JOIN public.user_login AS section 
                ON pwi.user_id = section.parent_id 
            WHERE pwi.active = 1 
            AND pwi.group_id = 8
            AND section.active = 1
            AND section.group_id = 2;
        ";

        $result = $this->db->query($query)->getResultArray();

        $pwiSectionMap = [];
        foreach ($result as $row) {
            $pwiSectionMap[$row['section_name']] = [
                'pwi_id' => $row['pwi_id'],
                'section_id' => $row['section_id']
            ];
        }

        return $pwiSectionMap; // Returns an associative array for mapping
    }

    public function checkimei()
    {
        $data = [];
        $data['sessdata'] = $this->sessdata;
        $data['page_title'] = "Upload Individual Trip Schedule";

        if ($this->request->getMethod() == 'POST') {
            $file = $this->request->getFile('csv_file');
            $startDate = $this->request->getPost('start_date');
            $endDate = $this->request->getPost('end_date');
        
            if (!$file->isValid() || empty($startDate) || empty($endDate)) {
                $data['error'] = 'Invalid file upload or missing dates.';
            } else {
                $csvData = array_map('str_getcsv', file($file->getTempName()));
                array_shift($csvData); // Remove header row

                $tripScheduleModel = new TripScheduleModel();
                $tripDetailsModel = new TripScheduleDetailsModel();
                $scheduleIds = [];
                $insertedCount = 0;
                $skippedCount = 0;

                // 🔹 Convert Start & End Dates to Loopable Format
                $startDate = new \DateTime($startDate);
                $endDate = new \DateTime($endDate);
                $interval = new \DateInterval('P1D'); // 1 Day Increment
                $dateRange = new \DatePeriod($startDate, $interval, $endDate->modify('+1 day'));

            

                foreach ($dateRange as $date) {
                    $currentDate = $date->format('Y-m-d');
                    $deviceEndTimes = []; // Store last end time for each device per day
                    foreach ($csvData as $rowIndex => $row) {
                        
                        if (count($row) < 10) {
                            $errorLogs[] = "Row $rowIndex: Invalid data format (expected 10+ columns). Skipping.";
                            continue;
                        }

                        $imei = trim($row[0]);
                        if (!isset($deviceMapping[$imei])) {
                            $errorLogs[] = "Row $rowIndex: IMEI Not Found ($imei). Skipping.";
                            continue;
                        }
                        
                        $deviceid = $deviceMapping[$imei]; // Get the mapped deviceid
                        $sectionName = trim($row[1]);
                        $userType = trim($row[2]);
                        $startBeat = trim($row[3]);
                        $startLat = !empty($row[4]) ? trim($row[4]) : null;
                        $startLon = !empty($row[5]) ? trim($row[5]) : null;
                        $endBeat = trim($row[6]);
                        $endLat = !empty($row[7]) ? trim($row[7]) : null;
                        $endLon = !empty($row[8]) ? trim($row[8]) : null;
                        $startTime = trim($row[9]);
                        $endTime = trim($row[10]);
                        
                        $travelled_dist = trim($row[11]);
                        $tripno = trim($row[12]);
                        $block_name = trim($row[13]);
                        $pway = trim($row[14]);
                        
                        
                        // 🔹 Ensure `endTime` is not null
                        if (empty($endTime)) {
                            $errorLogs[] = "Row $rowIndex: Missing end time for device $deviceid. Setting it to start time.";
                            $endTime = $startTime; // Temporary fix to avoid NULL violation
                        }

                        if (!isset($deviceEndTimes[$deviceid]) || strtotime($endTime) > strtotime($deviceEndTimes[$deviceid])) {
                            $deviceEndTimes[$deviceid] = $endTime; // Keep track of the latest end time
                        }

                        if (!isset($data['pwiMapping'][$sectionName])) {
                            $errorLogs[] = "Row $rowIndex: Invalid Section Name ($sectionName). Skipping.";
                            continue;
                        }

                        $pwiId = $data['pwiMapping'][$sectionName]['pwi_id'];
                        $sectionId = $data['pwiMapping'][$sectionName]['section_id'];

                        // 🔹 Check if schedule already exists for this device on this date
                        if (!isset($scheduleIds[$deviceid][$currentDate])) {
                            $existingSchedule = $tripScheduleModel->where([
                                'deviceid' => $deviceid,
                                'expected_start_date' => $currentDate,
                                'expected_end_date' => $currentDate
                            ])->first();

                            if (!$existingSchedule) {
                                try {
                                    //Assign Device to pole
                                    if ($deviceid && $startBeat && $endBeat && $userType && $block_name) {
                                        // Call the stored function (using a query for example)
                                        $this->db->query("SELECT public.copy_master_assigne_pole_data_table(?, ?, ?, ?, ?)", [$deviceid, $startBeat, $endBeat, $userType, $block_name]);
                                    }

                                    $query3 = $this->db->query("SELECT msd.device_name AS device_name 
                                        FROM  {$this->schema}.master_device_setup msd
                                        WHERE msd.deviceid = ?
                                    ", [$deviceid]);

                                    $result3 = $query3->getRow();

                                    // 🔹 Insert New Schedule (ONE PER DEVICE PER DAY)
                                    $scheduleId = $tripScheduleModel->insert([
                                        'deviceid' => $deviceid,
                                        'imeino' => $imei,
                                        'section_name' => $sectionName,
                                        'pwi_id' => $pwiId,
                                        'section_id' => $sectionId,
                                        'device_type' => $userType,
                                        'expected_start_date' => $currentDate,
                                        'expected_start_time' => $startTime,
                                        'expected_end_date' => $currentDate,
                                        'expected_end_time' => $endTime, // ✅ Ensured Not NULL
                                        'active' => true,
                                        'devicename' => $result3->device_name ?? "", 
                                    ], true);
                                    
                                    $scheduleIds[$deviceid][$currentDate] = $scheduleId;
                                    $insertedCount++;

                                } catch (\Exception $e) {
                                    $errorLogs[] = "Row $rowIndex: SQL Error (Schedule Insert) - " . $e->getMessage();
                                    continue;
                                }
                            } else {
                            
                                $scheduleId = $existingSchedule['schedule_id'];
                                $scheduleIds[$deviceid][$currentDate] = $scheduleId;
                            }
                        } else {
                            $scheduleId = $scheduleIds[$deviceid][$currentDate];
                        }

                        try {
                            // 🔹 Insert ALL TRIPS under the same `schedule_id`
                            $tripDetailsModel->insert([
                                'schedule_id' => $scheduleId,
                                'expected_stpole' => $startBeat,
                                'expected_stlat' => $startLat,
                                'expected_stlon' => $startLon,
                                'expected_start_datetime' => $currentDate . ' ' . $startTime,
                                'expected_endpole' => $endBeat,
                                'expected_endlat' => $endLat,
                                'expected_endlon' => $endLon,
                                'expected_end_datetime' => $currentDate . ' ' . $endTime,
                                'trip_status' => 'Not Started',
                                'expected_distance' => $travelled_dist,
                                'trip_no' => $tripno
                            ]);
                        } catch (\Exception $e) {
                            $errorLogs[] = "Row $rowIndex: SQL Error (Trip Insert) - " . $e->getMessage();
                        }
                    }

                    // 🔹 Update `expected_end_time` for each schedule
                    foreach ($deviceEndTimes as $deviceid => $finalEndTime) {
                        $tripScheduleModel->where([
                            'deviceid' => $deviceid,
                            'expected_start_date' => $currentDate,
                            'expected_end_date' => $currentDate
                        ])->set([
                            'expected_end_time' => $finalEndTime
                        ])->update();
                    }
                }

                if(count($errorLogs) > 1) {
                    $data['error'] = $errorLogs;
                } else {
                    $data['success'] = "CSV uploaded successfully. Inserted: $insertedCount schedules.";
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

        $data['middle'] = view('trips/checkimei', $data);
        return view('mainlayout', $data);
    }

    public function individualUpload()
    {
        $data = [];
        $data['sessdata'] = $this->sessdata;
        $data['page_title'] = "Upload Individual Trip Schedule";
        $data['usersdd'] = $this->commonModel->get_users();
        $data['pwiMapping'] = $this->getPwiSectionMapping();
        $deviceMapping = $this->getDeviceMapping(); // IMEI → deviceid mapping

        $errorLogs = [];

        if ($this->request->getMethod() == 'POST') {
            $file = $this->request->getFile('csv_file');
            $startDate = $this->request->getPost('start_date');
            $endDate = $this->request->getPost('end_date');
        
            if (!$file->isValid() || empty($startDate) || empty($endDate)) {
                $data['error'] = 'Invalid file upload or missing dates.';
            } else {
                $csvData = array_map('str_getcsv', file($file->getTempName()));
                array_shift($csvData); // Remove header row

                $tripScheduleModel = new TripScheduleModel();
                $tripDetailsModel = new TripScheduleDetailsModel();
                $scheduleIds = [];
                $insertedCount = 0;
                $skippedCount = 0;

                // 🔹 Convert Start & End Dates to Loopable Format
                $startDate = new \DateTime($startDate);
                $endDate = new \DateTime($endDate);
                $interval = new \DateInterval('P1D'); // 1 Day Increment
                $dateRange = new \DatePeriod($startDate, $interval, $endDate->modify('+1 day'));

            

                foreach ($dateRange as $date) {
                    $currentDate = $date->format('Y-m-d');
                    $deviceEndTimes = []; // Store last end time for each device per day
                    foreach ($csvData as $rowIndex => $row) {
                        
                        if (count($row) < 10) {
                            $errorLogs[] = "Row $rowIndex: Invalid data format (expected 10+ columns). Skipping.";
                            continue;
                        }

                        $imei = trim($row[0]);
                        if (!isset($deviceMapping[$imei])) {
                            $errorLogs[] = "Row $rowIndex: IMEI Not Found ($imei). Skipping.";
                            continue;
                        }
                        
                        $deviceid = $deviceMapping[$imei]; // Get the mapped deviceid
                        $sectionName = trim($row[1]);
                        $userType = trim($row[2]);
                        $startBeat = trim($row[3]);
                        $startLat = !empty($row[4]) ? trim($row[4]) : null;
                        $startLon = !empty($row[5]) ? trim($row[5]) : null;
                        $endBeat = trim($row[6]);
                        $endLat = !empty($row[7]) ? trim($row[7]) : null;
                        $endLon = !empty($row[8]) ? trim($row[8]) : null;
                        $startTime = trim($row[9]);
                        $endTime = trim($row[10]);
                        
                        $travelled_dist = trim($row[11]);
                        $tripno = trim($row[12]);
                        $block_name = trim($row[13]);
                        $pway = trim($row[14]);
                        
                        
                        // 🔹 Ensure `endTime` is not null
                        if (empty($endTime)) {
                            $errorLogs[] = "Row $rowIndex: Missing end time for device $deviceid. Setting it to start time.";
                            $endTime = $startTime; // Temporary fix to avoid NULL violation
                        }

                        if (!isset($deviceEndTimes[$deviceid]) || strtotime($endTime) > strtotime($deviceEndTimes[$deviceid])) {
                            $deviceEndTimes[$deviceid] = $endTime; // Keep track of the latest end time
                        }

                        if (!isset($data['pwiMapping'][$sectionName])) {
                            $errorLogs[] = "Row $rowIndex: Invalid Section Name ($sectionName). Skipping.";
                            continue;
                        }

                        $pwiId = $data['pwiMapping'][$sectionName]['pwi_id'];
                        $sectionId = $data['pwiMapping'][$sectionName]['section_id'];

                        // 🔹 Check if schedule already exists for this device on this date
                        if (!isset($scheduleIds[$deviceid][$currentDate])) {
                            $existingSchedule = $tripScheduleModel->where([
                                'deviceid' => $deviceid,
                                'expected_start_date' => $currentDate,
                                'expected_end_date' => $currentDate
                            ])->first();

                            if (!$existingSchedule) {
                                try {
                                    //Assign Device to pole
                                    if ($deviceid && $startBeat && $endBeat && $userType && $block_name) {
                                        // Call the stored function (using a query for example)
                                        $this->db->query("SELECT public.copy_master_assigne_pole_data_table(?, ?, ?, ?, ?)", [$deviceid, $startBeat, $endBeat, $userType, $block_name]);
                                    }

                                    $query3 = $this->db->query("SELECT msd.device_name AS device_name 
                                        FROM  {$this->schema}.master_device_setup msd
                                        WHERE msd.deviceid = ?
                                    ", [$deviceid]);

                                    $result3 = $query3->getRow();

                                    // 🔹 Insert New Schedule (ONE PER DEVICE PER DAY)
                                    $scheduleId = $tripScheduleModel->insert([
                                        'deviceid' => $deviceid,
                                        'imeino' => $imei,
                                        'section_name' => $sectionName,
                                        'pwi_id' => $pwiId,
                                        'section_id' => $sectionId,
                                        'device_type' => $userType,
                                        'expected_start_date' => $currentDate,
                                        'expected_start_time' => $startTime,
                                        'expected_end_date' => $currentDate,
                                        'expected_end_time' => $endTime, // ✅ Ensured Not NULL
                                        'active' => true,
                                        'devicename' => $result3->device_name ?? "", 
                                    ], true);
                                    
                                    $scheduleIds[$deviceid][$currentDate] = $scheduleId;
                                    $insertedCount++;

                                } catch (\Exception $e) {
                                    $errorLogs[] = "Row $rowIndex: SQL Error (Schedule Insert) - " . $e->getMessage();
                                    continue;
                                }
                            } else {
                            
                                $scheduleId = $existingSchedule['schedule_id'];
                                $scheduleIds[$deviceid][$currentDate] = $scheduleId;
                            }
                        } else {
                            $scheduleId = $scheduleIds[$deviceid][$currentDate];
                        }

                        try {
                            // 🔹 Insert ALL TRIPS under the same `schedule_id`
                            $tripDetailsModel->insert([
                                'schedule_id' => $scheduleId,
                                'expected_stpole' => $startBeat,
                                'expected_stlat' => $startLat,
                                'expected_stlon' => $startLon,
                                'expected_start_datetime' => $currentDate . ' ' . $startTime,
                                'expected_endpole' => $endBeat,
                                'expected_endlat' => $endLat,
                                'expected_endlon' => $endLon,
                                'expected_end_datetime' => $currentDate . ' ' . $endTime,
                                'trip_status' => 'Not Started',
                                'expected_distance' => $travelled_dist,
                                'trip_no' => $tripno
                            ]);
                        } catch (\Exception $e) {
                            $errorLogs[] = "Row $rowIndex: SQL Error (Trip Insert) - " . $e->getMessage();
                        }
                    }

                    // 🔹 Update `expected_end_time` for each schedule
                    foreach ($deviceEndTimes as $deviceid => $finalEndTime) {
                        $tripScheduleModel->where([
                            'deviceid' => $deviceid,
                            'expected_start_date' => $currentDate,
                            'expected_end_date' => $currentDate
                        ])->set([
                            'expected_end_time' => $finalEndTime
                        ])->update();
                    }
                }

                if(count($errorLogs) > 1) {
                    $data['error'] = $errorLogs;
                } else {
                    $data['success'] = "CSV uploaded successfully. Inserted: $insertedCount schedules.";
                }
                
            }
        }

        $data['middle'] = view('trips/trip_individual_upload', $data);
        return view('mainlayout', $data);
    }

}
