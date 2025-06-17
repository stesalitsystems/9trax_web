<?php
namespace App\Controllers;

use App\Models\CommonModel; // Adjust the namespace if necessary
use CodeIgniter\Controller;

class Masters extends Controller
{
    protected $sessdata;
    protected $schema;

    public function __construct()
    {
        // Load the session library and check for login session data
        $this->session = session();
        $this->db = \Config\Database::connect();
        if ($this->session->get('login_sess_data')) {
            $this->sessdata = $this->session->get('login_sess_data');
            $this->schema = $this->sessdata['schemaname'];
        } else {
            return redirect("/");
        }

        // Load the model
        $this->commonModel = new CommonModel(); // Ensure the model is properly namespaced
        helper(['master', 'form']);
    }

    public function settings()
    {
        ini_set('memory_limit', '512M'); // or '256M', or '-1' for unlimited

        $data = [];
        $data['category'] = $cate = trim($this->request->getUri()->getSegment(3));
        $table = "master_" . $cate;
        $orderby = 'inserttime desc';

        if ($this->request->getUri()->getSegment(4) == 'upload') {
            if ($this->request->getMethod() == 'POST' && $this->request->getFile('csvfile')->isValid()) {
                $file = $this->request->getFile('csvfile');
                if ($file->getMimeType() == 'application/vnd.ms-excel' || $file->getMimeType() == 'text/csv' || $file->getMimeType() == 'text/plain') {
                    $count = 0;
                    $errcount = 0;
                    $fp = fopen($file->getTempName(), 'r') or die("can't open file");

                    $this->db->transStart();
                    $dataToInsert = []; // Array to hold data for batch insert
                    while ($csv_line = fgetcsv($fp, 1024)) {
                        $count++;
                        if ($count == 1) {
                            continue; // Skip header row
                        }

                        $name = trim($csv_line[0]);
                       $latitude = trim($csv_line[1]);
                        $longitude = trim($csv_line[2]);
                         $description = trim($csv_line[3]);

                        if ($name != "" || $latitude != "" || $longitude != "") {
                            // echo $cate."xxxxxxxxxx";

                            if($cate == 'polldata') {
                              //  echo "111111111111111111111";

                                $old_pole = $this->db->table($this->schema . '.'. $table)
                                ->select('name')
                                ->where('name', $name)
                                ->where('latitude', $latitude)
                                ->where('longitude', $longitude)
                                ->get()
                                ->getRow();
                                /*$queryPole = $this->db->table($this->schema . '.' . $table)
                                    ->select('name')
                                    ->where('name', $name)
                                    ->where('latitude', $latitude)
                                    ->where('longitude', $longitude)
                                    ->get();

                                if ($queryPole !== false) {
                                    $old_pole = $queryPole->getRow();
                                } else {
                                    // Handle query error (log it or show a message)
                                    // log_message('error', 'Database query failed in Masters.php line 69');
                                    $old_pole = null;
                                }*/

                            //    echo $this->db->getLastQuery();

                              //   echo "<pre>";print_r($old_pole);exit();
                                if(empty($old_pole) || $old_pole->name != $name) {
                                    $dataToInsert[] = [
                                        'name' => $name,
                                        'latitude' => $latitude,
                                        'longitude' => $longitude,
                                        'description' => $description
                                        // Add other fields as necessary
                                    ];

                                    $poledescription = trim($csv_line[4]);
                                    $parent_polno = trim($csv_line[5]);
                                    // $dataToInsert[] = [
                                    //     'poledescription' => $poledescription,
                                    //     'parent_polno' => $parent_polno,
                                    // ];

                                    // Add additional fields to the same associative array
                                    $dataToInsert[count($dataToInsert) - 1]['poledescription'] = $poledescription;
                                    $dataToInsert[count($dataToInsert) - 1]['parent_polno'] = $parent_polno;
                                }else{
                                    $errcount++;
                                }
                            } else {
                                // Prepare data for batch insert
                                $dataToInsert[] = [
                                    'name' => $name,
                                    'latitude' => $latitude,
                                    'longitude' => $longitude,
                                    'description' => $description
                                    // Add other fields as necessary
                                ];
                            }
                        } else {
                            $errcount++;
                        }
                    }

                    fclose($fp);
                    // echo "<pre>";print_r($dataToInsert);
                     
                    // exit();
                    // Perform batch insert if there are records to insert
                    if (!empty($dataToInsert)) {
                        if (!$this->insertDataBatch($table, $dataToInsert)) {
                            $this->session->setFlashdata('msg', "Error occurred during batch insert.");
                        }else{
                            $this->session->setFlashdata('msg', "Data inserted succesfully.");
                        }
                    }
                    $this->db->transComplete();

                    if ($this->db->transStatus() !== false) {
                        $this->session->setFlashdata('msg', $count == 1 ? "No data in File." : ($errcount == 0 ? "File Saved successfully." : "Error occurred at the time of uploading, all data not saved."));
                        return redirect()->to("/masters/settings/" . $cate);
                    }
                } else {
                    $this->session->setFlashdata('msg', "Invalid file format.");
                }

                return redirect()->to("/masters/settings/" . $cate);
            }
        } else if ($this->request->getUri()->getSegment(4) == 'upload_assignment') {
            // Check if file is being uploaded
            if ($this->request->getMethod() == 'POST' && $this->request->getFile('csvfile')) {

                $file = $this->request->getFile('csvfile');

                // Check if the uploaded file is CSV or Excel type
                if ($file->isValid() && ($file->getMimeType() == 'application/vnd.ms-excel' || $file->getMimeType() == 'text/csv' || $file->getMimeType() == 'text/plain')) {
                    $count = 0;
                    $errcount = 0;
                    // Open the file for reading
                    $filePath = $file->getTempName();
                    $fp = fopen($filePath, 'r') or die("can't open file");

                    // Start database transaction
                    $this->db->transStart();

                    // Read the CSV file line by line
                    while ($csv_line = fgetcsv($fp, 1024)) {
                        $count++;

                        // Skip the first row (header row)
                        if ($count == 1) {
                            continue;
                        }

                        // Process the CSV line
                        $device_serial = str_replace("ID ", "", trim($csv_line[0]));
                        $dev_data = $this->db->query("SELECT id FROM master_device_details WHERE serial_no = ?", [$device_serial])->getRow();
                        $device_id = $dev_data ? $dev_data->id : null;

                        // Extract other fields
                        $serial_no = trim($csv_line[0]);
                        $start_pole = trim($csv_line[1]);
                        $end_pole = trim($csv_line[2]);
                        $type_of_user = trim($csv_line[3]);
                        $block_name = trim($csv_line[4]);
                        $pway = trim($csv_line[5]);
                        $user = trim($csv_line[6]);

                        // Validate data before proceeding
                        if ($device_id && $start_pole && $end_pole && $type_of_user && $block_name) {
                            // Call the stored function (using a query for example)
                            $this->db->query("SELECT public.copy_master_assigne_pole_data_table(?, ?, ?, ?, ?)", [$device_id, $start_pole, $end_pole, $type_of_user, $block_name]);

                            //echo $this->db->getLastQuery();

                            //echo "<pre>";print_r($old_pole);
                            
                            //exit();

                        } else {
                            $errcount++;
                        }
                    }

                    fclose($fp);

                    // Complete the transaction
                    $this->db->transComplete();

                    // Check transaction status
                    if ($this->db->transStatus() == false) {
                        $this->db->transRollback();
                        $this->session->setFlashdata('msg', 'Error occurred during uploading, all data not saved.');
                    } else {
                        $this->db->transCommit();

                        // Flash message based on result
                        if ($count == 1) {
                            $this->session->setFlashdata('msg', 'No data in File.');
                        } else {
                            if ($errcount == 0) {
                                $this->session->setFlashdata('msg', 'File saved successfully.');
                            } else {
                                $this->session->setFlashdata('msg', 'Error occurred at the time of uploading, all data not saved.');
                            }
                        }
                    }

                    // Redirect to settings page
                    return redirect()->to("masters/settings/{$cate}");
                } else {
                    // Invalid file type
                    $this->session->setFlashdata('msg', 'Invalid file format.');
                }
            } else {
                // Invalid file type
                $this->session->setFlashdata('msg', 'Invalid file format.');
            }
        } else {
            $data['sessdata'] = $this->sessdata;
            $data['parentdata'] = $this->commonModel->getRows($table, "*", [], [], null, $orderby);
            // echo $table."<pre>";print_r($data['parentdata']);exit();
        }

        $pghead = ucwords(substr($cate, 0, -4));
        if ($pghead == "Poll") {
            $pghead = 'Pole';
        }
        if ($pghead == "Lc") {
            $pghead = 'Level Crossing';
        }
        if ($pghead == "Kmp") {
            $pghead = 'Kilometre Post';
        }

        $data['page_title'] = $pghead;
        $data['segment'] = $this->request->getUri()->getSegment(4);
        $data['segment3'] = $this->request->getUri()->getSegment(3);
        $data['sessdata'] = $this->sessdata;
        $data['middle'] = view('masters/settings', $data);
        return view('mainlayout', $data);
    }

    private function insertData($cate, $name, $latitude, $longitude, $description, $csv_line)
    {
        // Insert data based on category
        if ($cate == 'polldata') {
            $poledescription = trim($csv_line[4]);
            $parent_polno = trim($csv_line[5]);
            $sql = "INSERT INTO ".$this->schema.".master_polldata (name, latitude, longitude, description, geom, poledescription, parent_polno) VALUES (?, ?, ?, ?, ST_SetSRID(ST_MakePoint(?, ?), 4326), ?, ?)";
            $this->db->query($sql, [$name, $latitude, $longitude, $description, $longitude, $latitude, $poledescription, $parent_polno]);
        } else if ($cate == 'stationdata') {
            $sql = "INSERT INTO ".$this->schema.".master_stationdata (name, latitude, longitude, description, geom) VALUES (?, ?, ?, ?, ST_SetSRID(ST_MakePoint(?, ?), 4326))";
            $this->db->query($sql, [$name, $latitude, $longitude, $description, $longitude, $latitude]);
        } else if ($cate == 'kmpdata') {
            $sql = "INSERT INTO ".$this->schema.".master_kmpdata (name, latitude, longitude, description, geom) VALUES (?, ?, ?, ?, ST_SetSRID(ST_MakePoint(?, ?), 4326))";
            $this->db->query($sql, [$name, $latitude, $longitude, $description, $longitude, $latitude]);
        } else if ($cate == 'lcdata') {
            $sql = "INSERT INTO ".$this->schema.".master_lcdata (name, latitude, longitude, description, geom) VALUES (?, ?, ?, ?, ST_SetSRID(ST_MakePoint(?, ?), 4326))";
            $this->db->query($sql, [$name, $latitude, $longitude, $description, $longitude, $latitude]);
        } else {
            $this->db->query("SELECT public._sil_add_update_multiple_geotable(?, ?, ?, ?, ?, ?)", [$this->schema, $table, $name, $latitude, $longitude, $description]);
        }
    }

    protected function insertDataBatch($cate, $data)
    {
        ini_set('memory_limit', '512M'); // or '256M', or '-1' for unlimited
        if (empty($data)) {
            return false; // No data to insert
        }
        if($cate == 'master_polldata') {
            // Construct the SQL query with the appropriate schema and table name
            $sql = "INSERT INTO " . $this->schema . '.' . $cate . " (name, latitude, longitude, description, geom, poledescription, parent_polno) VALUES ";
            $values = [];

            foreach ($data as $row) {
                $values[] = "('" . $this->db->escapeString($row['name']) . "', '" .
                    $this->db->escapeString($row['latitude']) . "', '" .
                    $this->db->escapeString($row['longitude']) . "', '" .
                    $this->db->escapeString($row['description']) . "', " .
                    "ST_SetSRID(ST_MakePoint(" . 
                    $this->db->escapeString($row['longitude']) . ", " .
                    $this->db->escapeString($row['latitude']) . "), 4326), '" .
                    $this->db->escapeString($row['poledescription']) . "', '" .
                    $this->db->escapeString($row['parent_polno']) . "')";
            }
        } else {
            // Construct the SQL query
            $sql = "INSERT INTO " . $this->schema . '.' .$cate . " (name, latitude, longitude, description, geom) VALUES ";
            $values = [];

            foreach ($data as $row) {
                $values[] = "('" . $this->db->escapeString($row['name']) . "', '" .
                        $this->db->escapeString($row['latitude']) . "', '" .
                        $this->db->escapeString($row['longitude']) . "', '" .
                        $this->db->escapeString($row['description']) . "', " .
                        "ST_SetSRID(ST_MakePoint(" . 
                        $this->db->escapeString($row['longitude']) . ", " .
                        $this->db->escapeString($row['latitude']) . "), 4326)" . 
                        ")";
            }
        }
        

        echo $sql .= implode(', ', $values);

        // Execute the query
        return $this->db->query($sql);
    }


    public function getAllSettings()
    {
        $userId = $this->sessdata['user_id'];
        $category = trim($this->request->getVar('category'));
        $length = $this->request->getVar('length');
        $draw = $this->request->getVar('draw');
        $page = ($draw == "" || $draw == 0) ? 0 : $this->request->getVar('start');

        $table = $this->schema . ".master_" . $category;
        $orderby = 'inserttime DESC';
        $joins = [];
        $where = [];
        
        if ($this->request->getVar("name") != "") {
            $where = [$table . '.name' => $this->request->getVar("name")];
        }

        $data = $this->commonModel->getRowspagin($table, '*', $length, $page, $joins, $where, null, $orderby);
        $totalData = $this->commonModel->getRowstotal($table, '*', $joins, $where, "", "");

        $dataFinal = [];
        if (!empty($data)) {
            foreach ($data as $key => $row) {
                $dataFinal[$key]['name'] = $row->name;
                $dataFinal[$key]['latitude'] = $row->latitude;
                $dataFinal[$key]['longitude'] = $row->longitude;
                $dataFinal[$key]['description'] = $row->description;

                if ($category == 'polldata') {
                    $dataFinal[$key]['poledescription'] = $row->poledescription;
                    $dataFinal[$key]['parent_polno'] = $row->parent_polno;
                }
            }
        }

        $response = [
            "draw" => intval($draw),
            "recordsTotal" => $totalData,
            "recordsFiltered" => $totalData,
            "data" => $dataFinal
        ];
        
        return $this->response->setJSON($response);
    }


    public function scheduleUpdateCSV()
    {
        $data = [];
              
       
            if ($this->request->getMethod() == 'POST' && $this->request->getFile('csvfile')->isValid()) {
               
                $file = $this->request->getFile('csvfile');
                // echo $file->getMimeType(); exit();
                if ($file->getMimeType() == 'application/vnd.ms-excel' || $file->getMimeType() == 'text/csv' || $file->getMimeType() == 'text/plain') {
                    
                    $count = 0;
                    $errcount = 0;
                    $fp = fopen($file->getTempName(), 'r') or die("can't open file");
        
                    $this->db->transStart();
                    $dataToInsert = []; 
                    $errors = [];

                    while ($csv_line = fgetcsv($fp, 1024)) {                     
                        
                        $count++;
        
                        $imeino = trim($csv_line[0]);                   
                        $usertype = trim($csv_line[1]);                   
                        $stpole = trim($csv_line[2]);                  
                        $endpole = trim($csv_line[3]);                   
                        $sttime = trim($csv_line[4]);                   
                        $endtime = trim($csv_line[5]);                   
                        $distance_travelled = trim($csv_line[6]);                   
                        $stpolelat = trim($csv_line[7]);                   
                        $stpolelon = trim($csv_line[8]);                   
                        $endpolelat = trim($csv_line[9]);                   
                        $endpolelon = trim($csv_line[10]);                   
                        $trip = trim($csv_line[11]);
                        $block_name = trim($csv_line[12]);
                   

                        if ($imeino == "" || $usertype == "" || $stpole == "" || $endpole == "" || $sttime == "" || $endtime == "" || $stpolelat == "" || $stpolelon == "" || $endpolelat == "" || $endpolelon == "" || $trip == "" || $distance_travelled == "") {
                            $errcount++;
                            $missingFields = [];

                            if ($imeino == "") $missingFields[] = "IMEI No";
                            if ($usertype == "") $missingFields[] = "User Type";
                            if ($stpole == "") $missingFields[] = "Start Pole";
                            if ($endpole == "") $missingFields[] = "End Pole";
                            if ($sttime == "") $missingFields[] = "Start Time";
                            if ($endtime == "") $missingFields[] = "End Time";
                            if ($distance_travelled == "") $missingFields[] = "Travelled Distance";
                            if ($stpolelat == "") $missingFields[] = "Start Pole Latitude";
                            if ($stpolelon == "") $missingFields[] = "Start Pole Longitude";
                            if ($endpolelat == "") $missingFields[] = "End Pole Latitude";
                            if ($endpolelon == "") $missingFields[] = "End Pole Longitude";
                            if ($trip == "") $missingFields[] = "Trip";

                            $errors[] = "Row $count: ($imeino) Missing fields - " . implode(", ", $missingFields);
                            continue; 
                        }

                        // $device_serial = str_replace("ID ", "", trim($csv_line[0]));
                        $imeino = preg_replace('/[^\d]/', '', trim($csv_line[0])); 
                        $sql = " UPDATE ".$this->schema.".tbl_device_schedule_updated SET status = 0 WHERE imeino = '".$imeino."'" ;
                        $this->db->query($sql);

                        // echo $csv_line[0];exit();

                        $query = $this->db->query("SELECT ul.username AS username, 
                                        p_ul.user_id AS pwi_id,
                                        ul.group_id AS group_id
                            FROM public.master_device_assign mda
                            LEFT JOIN public.user_login ul ON ul.user_id = mda.user_id
                            LEFT JOIN public.user_login p_ul ON p_ul.user_id = mda.parent_id
                            WHERE mda.deviceid = (SELECT id FROM master_device_details 
                            WHERE imei_no = ?) 
                            AND ul.group_id = 8 
                            AND mda.active = 1
                        ", [$imeino]);

                        $query1 = $this->db->query("SELECT ul.username AS username, 
                                        p_ul.user_id AS section_id,
                                        ul.group_id AS group_id
                            FROM public.master_device_assign mda
                            LEFT JOIN public.user_login ul ON ul.user_id = mda.user_id
                            LEFT JOIN public.user_login p_ul ON p_ul.user_id = mda.parent_id
                            WHERE mda.deviceid = (SELECT id FROM master_device_details 
                            WHERE imei_no = ?) 
                            AND ul.group_id = 2 
                            AND mda.active = 1
                        ", [$imeino]);

                        $query2 = $this->db->query("SELECT ul.username AS username, 
                                msd.device_name  AS device_name 
                            FROM public.master_device_assign mda
                            LEFT JOIN public.user_login ul 
                                ON ul.user_id = mda.user_id
                            LEFT JOIN public.user_login p_ul 
                                ON p_ul.user_id = mda.parent_id
                            LEFT JOIN public.master_device_details md 
                                ON md.id = mda.deviceid
                            LEFT JOIN {$this->schema}.master_device_setup msd 
                                ON msd.deviceid = mda.deviceid
                            WHERE md.imei_no = ? 
                            AND ul.group_id = 2 
                            AND mda.active = 1
                        ", [$imeino]);

                        $result = $query->getRow();
                        $result1 = $query1->getRow();
                        $result2 = $query2->getRow();

                        
                        if (empty($result2) || empty($result1) || empty($result) || $result2->device_name == "" || $result->pwi_id == "" || $result1->section_id == "") {
                            
                            $errcount++;
                            $missingFields = [];

                            if (empty($result) || $result->pwi_id == "") $missingFields[] = "PWI ID";
                            if (empty($result1) || $result1->section_id == "") $missingFields[] = "SECTION ID";
                            if (empty($result2) || $result2->device_name == "") $missingFields[] = "DEVICE NAME";

                            $errors[] = "Row $count: ($imeino)" . implode(", ", $missingFields)." not found for this device.";
                            continue; 
                        }
                        
                      
                        // Loop through trips (casting $trip to integer for safety)
                        // for ($i = 1; $i <= (int)$trip; $i++) {
                            /*if ($i % 2 == 1) {
                                // For odd-numbered trips, swap start and end poles and their coordinates.
                                $toggle_stpole       = $endpole;
                                $toggle_endpole      = $stpole;
                                $toggle_stpolelat    = $endpolelat;
                                $toggle_stpolelon    = $endpolelon;
                                $toggle_endpolelat   = $stpolelat;
                                $toggle_endpolelon   = $stpolelon;
                            } else {
                                // For even-numbered trips, keep the original order.
                                $toggle_stpole       = $stpole;
                                $toggle_endpole      = $endpole;
                                $toggle_stpolelat    = $stpolelat;
                                $toggle_stpolelon    = $stpolelon;
                                $toggle_endpolelat   = $endpolelat;
                                $toggle_endpolelon   = $endpolelon;
                            }*/

                            $dev_data = $this->db->query("SELECT id FROM master_device_details WHERE serial_no = ?", [$imeino])->getRow();
                            $device_id = $dev_data ? $dev_data->id : null;
                            if ($device_id && $stpole && $endpole && $usertype && $block_name) {
                                // Call the stored function (using a query for example)
                                $this->db->query("SELECT public.copy_master_assigne_pole_data_table(?, ?, ?, ?, ?)", [$device_id, $stpole, $endpole, $usertype, $block_name]);
                            }

                            $query3 = $this->db->query("SELECT msd.device_name AS device_name 
                                FROM  {$this->schema}.master_device_setup msd
                                WHERE msd.deviceid = ?
                            ", [$device_id]);

                            $result3 = $query3->getRow();

                            $dataToInsert[] = [
                                'imeino' => $imeino,
                                'usertype' => $usertype,
                                'stpole' => $stpole,
                                'endpole' => $endpole,
                                'sttime' => $sttime,
                                'endtime' => $endtime,
                                'distance_travelled' => $distance_travelled,
                                'stpolelat' => $stpolelat,
                                'stpolelon' => $stpolelon,
                                'endpolelat' => $endpolelat,
                                'endpolelon' => $endpolelon,
                                'trip' => $trip,
                                'stgeom' => "ST_SetSRID(ST_MakePoint($stpolelon, $stpolelat), 4326)",
                                'endgeom' => "ST_SetSRID(ST_MakePoint($endpolelon, $endpolelat), 4326)",
                                'devicename' =>$result3->device_name ?? "", 
                                'pwi_id' => $result->pwi_id ?? "", 
                                'section_id' => $result1->section_id ?? ""
                            ];
                        // }

                    }
                   
                    fclose($fp);


                    if (!empty($errors)) {
                        $this->session->setFlashdata('errors', $errors);
                    }

                    // echo "<pre>";print_r($dataToInsert);exit();
        
                    if (!empty($dataToInsert)) {
                        if (!$this->insertScheduleDataBatch($this->schema.'.tbl_device_schedule_updated', $dataToInsert)) {
                            $this->session->setFlashdata('msg', "Error occurred during batch insert.");
                        } else {
                            $this->session->setFlashdata('msg', "Data inserted successfully.");
                        }
                    }
                    $this->db->transComplete();
        
                    if ($this->db->transStatus() !== false) {
                        $this->session->setFlashdata('msg', $count == 1 ? "No data in File." : ($errcount == 0 ? "File Saved successfully." : " Uploaded data are saved.But $errcount rows data not saved. Please check error information."));
                        // return redirect()->to("/masters/scheduleupdatecsv");
                    }
                } else {
                    $this->session->setFlashdata('msg', "Invalid file format.");
                    // return redirect()->to("/masters/scheduleupdatecsv");
                }

                return redirect()->to("/masters/scheduleupdatecsv");
            }
       
        
        $data['page_title'] = 'Device Schedule Bulk CSV Upload';
        $data['sessdata'] = $this->sessdata;
        $data['middle'] = view('masters/deviceschedulecsvupload', $data);
        return view('mainlayout', $data);
    }


    
    protected function insertScheduleDataBatch($table, $data)
    {
        if (empty($data)) {
            return false; // No data to process
        }

        try {
                
            $sql = "INSERT INTO " . $table . " (imeino, usertype, stpole, endpole, sttime, endtime, distance_travelled, stpolelat, stpolelon, endpolelat, endpolelon, trip, stgeom, endgeom, devicename, pwi_id, section_id) VALUES ";
                $values = [];
                
                foreach ($data as $row) {
                    $values[] = "(" . 
                        "'" . $this->db->escapeString($row['imeino']) . "', " .
                        "'" . $this->db->escapeString($row['usertype']) . "', " .
                        "'" . $this->db->escapeString($row['stpole']) . "', " .
                        "'" . $this->db->escapeString($row['endpole']) . "', " .
                        "'" . $this->db->escapeString($row['sttime']) . "', " .
                        "'" . $this->db->escapeString($row['endtime']) . "', " .
                        "'" . $this->db->escapeString($row['distance_travelled']) . "', " .
                        "'" . $this->db->escapeString($row['stpolelat']) . "', " .
                        "'" . $this->db->escapeString($row['stpolelon']) . "', " .
                        "'" . $this->db->escapeString($row['endpolelat']) . "', " .
                        "'" . $this->db->escapeString($row['endpolelon']) . "', " .
                        "'" . $this->db->escapeString((string)$row['trip']) . "', " .
                        "ST_SetSRID(ST_MakePoint(" . $this->db->escapeString($row['stpolelon']) . ", " . $this->db->escapeString($row['stpolelat']) . "), 4326), " .
                        "ST_SetSRID(ST_MakePoint(" . $this->db->escapeString($row['endpolelon']) . ", " . $this->db->escapeString($row['endpolelat']) . "), 4326), " .
                        "'" . $this->db->escapeString($row['devicename']) . "', " .
                        "'" . $this->db->escapeString($row['pwi_id']) . "', " .
                        "'" . $this->db->escapeString($row['section_id']) . "'" .
                    ")";
                }

                $sql .= implode(', ', $values);

                // echo $sql;
                // exit();

                return $this->db->query($sql);
                
        } catch (\Exception $e) {
            // echo 2;
            // exit();
            // Optionally log the error: error_log($e->getMessage());
            return false;
        }
        
    }


    public function scheduleList()
    {
        $data = [];
        $data['page_title'] = 'Schedule List';
        $data['sessdata'] = $this->sessdata;

        $imei_no = $this->request->getVar('imei_no');
        $data['imei_no'] = $imei_no;
        $page = $this->request->getVar('page') ?? 1;
        $perPage = 100;

        $builder = $this->db->table($this->schema . '.tbl_device_schedule_updated');
        $builder->select('schedule_id as id, imeino, usertype, stpole, endpole, sttime, endtime, distance_travelled, stpolelat, stpolelon, endpolelat, endpolelon, trip, stgeom, endgeom, devicename, pwi_id, section_id');
        if ($imei_no) {
            $builder->like('imeino', $imei_no);
        }
        $builder->orderBy('schedule_id', 'DESC');
        $total = $builder->countAllResults(false);
        $data['schedules'] = $builder->limit($perPage, ($page - 1) * $perPage)->get()->getResult();

        $pager = \Config\Services::pager();
        // $data['pager'] = $pager->makeLinks($page, $perPage, $total);
        $data['pager'] = $pager;
        $data['total'] = $total;
        $data['perPage'] = $perPage;
        $data['page'] = $page;


        $data['page_title'] = 'Schedule List';
        $data['sessdata'] = $this->sessdata;
        $data['middle'] = view('masters/schedulelist', $data);
        return view('mainlayout', $data);
    }

    public function deleteSchedule($id)
    {
        $this->db->table($this->schema . '.tbl_device_schedule_updated')->delete(['schedule_id' => $id]);
        $this->session->setFlashdata('msg', 'Schedule entry deleted successfully.');
        return redirect()->to('/masters/schedulelist');
    }

    public function scheduleupdatecsvnew()
    {
        $data = [];
        
        $data['page_title'] = 'Device Schedule CSV Upload';
        $data['sessdata'] = $this->sessdata;
        $data['middle'] = view('masters/deviceschedulecsvuploadnew', $data);
        return view('mainlayout', $data);
    }
}
