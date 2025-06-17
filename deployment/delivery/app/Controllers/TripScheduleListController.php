<?php

namespace App\Controllers;

use App\Models\TripScheduleModel;
use App\Models\TripScheduleDetailsModel;
use CodeIgniter\Controller;

class TripScheduleListController extends Controller
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
        $tripDetailsModel = new TripScheduleDetailsModel();
        $db = \Config\Database::connect();

        $data = [];
        $data['sessdata'] = $this->sessdata;
        $data['page_title'] = "Trip Schedule";
        $data['usersdd'] = $this->commonModel->get_users();

        // ✅ Get Filter Inputs
        $deviceId = $this->request->getGet('deviceid');
        $startDate = $this->request->getGet('start_date');
        $endDate = $this->request->getGet('end_date');

        // ✅ Ensure Schema Reference is Available
        $schema = $this->sessdata['schemaname'] ?? 'public';
        $table = "{$schema}.trip_schedule";
        $detailsTable = "{$schema}.trip_schedule_details";
        $userTable = "public.user_login";

        // ✅ Fetch Trip Schedules with `user_login` Join
        $query = $tripScheduleModel->select("
                ts.*, 
                pwi.organisation AS pwi_name, 
                section.organisation AS section_name
            ")
            ->from("$table AS ts")
            ->join("$userTable AS pwi", "pwi.user_id = ts.pwi_id", 'left')
            ->join("$userTable AS section", "section.user_id = ts.section_id", 'left')
            ->groupBy("
                ts.schedule_id, ts.deviceid, ts.imeino, ts.section_id, ts.pwi_id, 
                ts.device_type, ts.expected_start_date, ts.expected_start_time, 
                ts.expected_end_date, ts.expected_end_time, ts.active,
                pwi.organisation, section.organisation
            ")
            ->orderBy("ts.expected_start_date", "ASC");
           

        // ✅ Apply Filters
        if (!empty($deviceId)) {
            $query->where("ts.imeino", $deviceId);
        }
        if (!empty($startDate) && !empty($endDate)) {
            $query->where("ts.expected_start_date >=", $startDate)
                ->where("ts.expected_end_date <=", $endDate);
        } elseif (!empty($startDate)) {
            $query->where("ts.expected_start_date >=", $startDate);
        } elseif (!empty($endDate)) {
            $query->where("ts.expected_end_date <=", $endDate);
        }

        // ✅ Fix: Ensure `FROM` Clause is Correct in Date Query
        $dateQuery = $db->query("
            SELECT 
                MIN(expected_start_date) AS start_date, 
                MAX(expected_end_date) AS end_date
            FROM $table
        ")->getRowArray();

        $data['stdt'] = $dateQuery['start_date'] ?? null;
        $data['endt'] = $dateQuery['end_date'] ?? null;

        // ✅ Fetch Results with Pagination
        $data['tripSchedules'] = $query->paginate(10);
        $data['pager'] = $tripScheduleModel->pager;
        // echo $db->getLastQuery();
        // die();
        $data['request'] = $this->request;

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

public function upload()
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
                    $startTime = trim($row[9]);
                    $endTime = trim($row[10]);
                    
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
                                    'active' => true
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
                            'trip_status' => 'Not Started'
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





    private function getDeviceMapping()
    {
        $query = "SELECT imei_no, id AS deviceid FROM public.master_device_details WHERE active = 2";
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

}
