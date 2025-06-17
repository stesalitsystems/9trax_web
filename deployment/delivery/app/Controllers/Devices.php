<?php

namespace App\Controllers;

use App\Models\DeviceModel;
use App\Models\UsersModel;
use CodeIgniter\Controller;

use finfo; 

class Devices extends Controller
{
    protected $sessdata;
    protected $schema;
    protected $device_arr;
    protected $deviceModel;
    protected $userModel;

    public function __construct()
    {
        $this->session = session();
        $this->deviceModel = new DeviceModel();
        $this->usersModel = new UsersModel();
        $this->db = \Config\Database::connect();
        $this->uri = service('uri');

        helper('MasterHelper');

        if ($this->session->get('login_sess_data')) {
            $this->sessdata = $this->session->get('login_sess_data');
            $this->schema = $this->sessdata['schemaname'];

            // Initialize the device array
            $this->device_arr = [
                '9T201900996', '9T201800617', '9T201902007', '869867030416194',
                '9T201901967', '869867030417218', '869867032248934', '869867030444477',
                '869867032255806', '869867030411542', '869867030417523', '869867030416111',
                '869867030441122'
            ];
        }
    }

    public function lists()
    {
        if (!$this->session->has('login_sess_data')) {
            return redirect()->to('/');
        }

        $data = [];
        $data['sessdata'] = $this->sessdata;

        $data['parentdata'] = $this->usersModel->where('user_id', $this->sessdata['parent_id'])->get()->getRow();
        
        $data['page_title'] = "Device Management";

        if ($this->sessdata['group_id'] == 1) {
            $data['middle'] = view('devices/lists_admin', $data);
        } else {
            $data['middle'] = view('devices/lists', $data);
        }

        return view('mainlayout', $data);
    }

    public function getAllDevicesAdmin()
    {
        $user_id = $this->sessdata['user_id']; // Assuming sessdata is set earlier in the controller
        $limit = $this->request->getVar('length');
        $draw = $this->request->getVar('draw');
        $offset = ($draw == 1) ? 0 : $this->request->getVar('start');

        $conditions = [
            'serial_no' => $this->request->getVar('serial_no'),
            'imei_no' => $this->request->getVar('imei_no'),
            'mobile_no' => $this->request->getVar('mobile_no'),
            'active' => $this->request->getVar('active'),
        ];

        $data_all = $this->deviceModel->getDevicesListAdmin($offset, $limit, $conditions); // Make sure to load devicesModel
        $dat = $data_all['filtereddata'];
        $recordsTotal = count($data_all['totaldata']);

        $datafinal = [];
        if (!empty($dat)) {
            foreach ($dat as $key => $row) {
                $deviceidForEncode = $row->serial_no;
                $datafinal[$key]['serial_no'] = $row->serial_no;
                $datafinal[$key]['imei_no'] = $row->imei_no;
                $datafinal[$key]['mobile_no'] = $row->mobile_no;
                $datafinal[$key]['assigned_user'] = !empty($row->firstname) ? $row->firstname . ' ' . $row->lastname : "Not Assigned";
                $datafinal[$key]['active'] = ($row->active == 1) ? "Active" : "Not Active";
                $datafinal[$key]['warranty_date'] = !empty($row->warranty_date) ? date("d/m/Y", strtotime($row->warranty_date)) : $row->warranty_date;

                $action = '<div class="dropdown">
                            <button class="btn ddaction dropdown-toggle" type="button" data-toggle="dropdown">
                                <i class="fa fa-gear"></i>
                                <span class="caret"></span>
                            </button>
                            <ul class="dropdown-menu action-dd">';
                
                $action .= '<li>
                                <a href="' . site_url("devices/devicesAdmin/edit/" . $deviceidForEncode) . '">
                                    <i class="fa fa-pencil" aria-hidden="true"></i> Edit
                                </a>
                            </li>';

                $action .= '<li class="divider"></li>
                            <li>
                                <a href="' . site_url("devices/devicesAdmin/editmode/" . $deviceidForEncode) . '">
                                    <i class="fa fa-pencil" aria-hidden="true"></i> Edit Mode
                                </a>
                            </li>';

                $action .= '<li class="divider"></li>
                            <li>
                                <a href="' . site_url('devices/devices_delete_admin/' . $deviceidForEncode) . '">
                                    <i class="fa fa-trash-o" aria-hidden="true"></i> Delete
                                </a>
                            </li>';
                
                if (!empty($row->firstname)) {
                    $deviceid = rtrim(strtr(base64_encode($row->id), '+/', '-_'), '=');
                    // $deviceid = urlencode(base64_encode($row->id));
                    $url = site_url('devices/statuschange/' . $deviceid . '/2');
                    $action .= '<li class="divider"></li>
                                <li>
                                    <a href="' . $url . '" class="statuschange">
                                        <i class="fa fa-arrow-down" aria-hidden="true"></i> Unassign
                                    </a>
                                </li>';
                }
                $action .= '</ul></div>';
                $datafinal[$key]['action'] = $action;
            }
        }

        $response = [
            "draw" => intval($draw),
            "recordsTotal" => $recordsTotal,
            "recordsFiltered" => $recordsTotal,
            "data" => $datafinal
        ];

        return $this->response->setJSON($response);
    }

    public function getAllDevices()
    {
        $user_id = $this->sessdata['user_id']; // Assuming you are using session service
        $limit = $this->request->getVar('length');
        $draw = $this->request->getVar('draw');
        $offset = ($draw == 1) ? 0 : $this->request->getVar('start');

        // Collect filter conditions
        $conditions = [
            'serial_no' => $this->request->getVar('serial_no'),
            'imei_no' => $this->request->getVar('imei_no'),
            'mobile_no' => $this->request->getVar('mobile_no'),
            'active' => $this->request->getVar('active')
        ];

        // Fetch device data
        $data_all = $this->deviceModel->getDevicesList($user_id, $offset, $limit, $conditions);
        $dat = $data_all['filtereddata'];
        $recordsTotal = count($data_all['totaldata']);
        
        $datafinal = [];
        if (!empty($dat)) {
            foreach ($dat as $key => $row) {
                $deviceidForEncode = $row->did;

                // Prepare device data for the response
                $datafinal[$key] = [
                    'serial_no' => $row->serial_no,
                    'imei_no' => $row->imei_no,
                    'mobile_no' => $row->mobile_no,
                    'assigned_user' => !empty($row->organisation) ? "{$row->organisation} ({$row->firstname} {$row->lastname})" : "",
                    'active' => ($row->active == 1) ? "Active" : "Not Active",
                    'warranty_date' => !empty($row->warranty_date) ? date("d/m/Y", strtotime($row->warranty_date)) : $row->warranty_date,
                    'action' => $this->generateActionDropdown($row, $deviceidForEncode)
                ];
            }
        }

        // Prepare the response
        $response = [
            "draw" => intval($draw),
            "recordsTotal" => $recordsTotal,
            "recordsFiltered" => $recordsTotal,
            "data" => $datafinal
        ];

        return $this->response->setJSON($response);
    }

    /**
     * Generate action dropdown for each device.
     */
    private function generateActionDropdown($row, $deviceidForEncode)
    {
        if ($this->sessdata['group_id'] == 2) {
            return ''; // No actions for group_id 2
        }

        $deviceid = urlencode(base64_encode($deviceidForEncode));
        $dropdown = '<div class="dropdown">
                        <button class="btn ddaction dropdown-toggle" type="button" data-toggle="dropdown">
                            <i class="fa fa-gear"></i>
                            <span class="caret"></span>
                        </button>
                        <ul class="dropdown-menu action-dd">';

        if (!empty($row->organisation)) {
            $deviceid = rtrim(strtr(base64_encode($deviceidForEncode), '+/', '-_'), '=');
            $url = site_url('devices/statuschange/' . $deviceid . '/2');
            $dropdown .= '<li class="divider"></li>
                          <li>
                              <a href="' . $url . '" class="statuschange">
                                  <i class="fa fa-arrow-down" aria-hidden="true"></i> Unassign
                              </a>
                          </li>';
        }

        $dropdown .= '</ul></div>';
        return $dropdown;
    }

    public function devicesAdmin()
    {
        $data['sessdata'] = $this->sessdata;

        if ($this->request->getUri()->getSegment(3) == 'add') {
            $data['page_title'] = "Add Device";

            if ($this->request->getMethod() == 'POST') {
                // Validate form inputs
                $rules = [
                    'serial_no' => 'required|trim|max_length[20]',
                    'imei_no' => 'required|trim|max_length[20]',
                    'target_ip' => 'required|trim',
                    'target_port' => 'required|trim',
                    'mobile_no' => 'trim',
                    'apn_name' => 'trim',
                ];
                
                if ($this->request->getPost('siminstalled') == 2) {
                    $rules['mobile_no'] = 'required|trim';
                    $rules['apn_name'] = 'required|trim';
                }

                if (!$this->validate($rules)) {
                    $data['errmsg'] = $this->validator->listErrors();
                } else {
                    $arr = [];
                    foreach ($this->request->getPost() as $key => $postarr) {
                        if ($key != "add") {
                            $val = trim($postarr);
                            if ($key == 'warranty_date' && $val != "") {
                                $val = date('Y-m-d', strtotime($val));
                            }
                            $arr[$key] = $val ?: null;
                        }
                    }

                    $operation = "add";
                    $serial_no = $arr['serial_no'] ?? '';
                    $mobile_no = $arr['mobile_no'] ?? '';
                    $mac_add = $arr['mac_add'] ?? 'NULL';
                    $sdcard_no = $arr['sdcard_no'] ?? 'NULL';
                    $active = $arr['active'] ?? 2; // Default value
                    $linked = $arr['linked'] ?? 1; // Default value
                    $imei_no = $arr['imei_no'] ?? '';
                    $sim_icc_id = $arr['sim_icc_id'] ?? 'NULL';

                    // Set warranty date to today's date if not provided
                    $warranty_date = empty($arr['warranty_date']) ? date('Y-m-d') : $arr['warranty_date'];

                    // Fetch session data with defaults
                    $insertby = $this->sessdata['user_id'] ?? 0; // Default user_id as 0 if session is missing
                    $updateby = $this->sessdata['user_id'] ?? 0;
                    $assigned_to = $this->sessdata['user_id'] ?? 0;
                    $superdevid = 0;
                    $group_id = $this->sessdata['group_id'] ?? 0; // Default group_id as 0 if session is missing
                    $typeofdevice = 'D';
                    $dynamiccode = rand(100000, 999999);
                    $siminstalled = $arr['siminstalled'] ?? 0; // Default value for siminstalled
                    $duration = $arr['duration'] ?? 0; // Default value for duration
                    $apn_name = $arr['apn_name'] ?? '';
                    $apn_username = $arr['apn_username'] ?? '';
                    $apn_pswd = $arr['apn_pswd'] ?? '';
                    $target_ip = $arr['target_ip'] ?? '';
                    $target_port = $arr['target_port'] ?? '';
                    $type = $arr['type'] ?? '';

                    // Use prepared statements to make it more secure
                    $sql = "SELECT msg FROM data_insert_into_master_device_details_table(
                        ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?
                    )";

                    $bindData = [
                        $operation,
                        $serial_no,
                        $mobile_no,
                        $mac_add,
                        $sdcard_no,
                        $active,
                        $linked,
                        $imei_no,
                        $sim_icc_id,
                        $warranty_date,
                        $insertby,
                        $updateby,
                        $assigned_to,
                        $superdevid,
                        $group_id,
                        $typeofdevice,
                        $dynamiccode,
                        $siminstalled,
                        $duration,
                        $apn_name,
                        $apn_username,
                        $apn_pswd,
                        $target_ip,
                        $target_port,
                        $type
                    ];

                    // Execute the prepared statement
                    $query = $this->db->query($sql, $bindData);

                    // Fetch the result row
                    $rec = $query->getRow();

                    if ($rec->msg == 'Added Successfully') {
                        $get_deviceid = $this->db->query("SELECT id FROM public.master_device_details WHERE serial_no = '{$serial_no}'")->getRow();
                        $added_deviceid = $get_deviceid->id;
                        $token = $this->random_strings(10);
                        $this->db->query("INSERT INTO public.master_device_token(deviceid, token) VALUES ({$added_deviceid}, '{$token}')");
                        session()->setFlashdata('msg', "Device {$rec->msg}");
                        return redirect()->to("devices/lists");
                    } else {
                        $data['errmsg'] = $rec->msg;
                    }
                }
            }

            // Fetch APN data
            $data['apn'] = $this->db->table('public.master_device_apn')->select('*')
                ->where('active', 1)
                ->get()
                ->getResult();

             // Get the segment value
            
        } else if ($this->request->getUri()->getSegment(3) == 'edit') {
            $data['page_title'] = "Edit Device";
            $data['sessdata'] = $this->sessdata;
            $device_serial = $this->request->getUri()->getSegment(4);

            // Validate form input
            if ($this->request->getMethod() == 'POST') {
                $rules = [
                    'serial_no' => 'required|max_length[20]|trim',
                    'imei_no' => 'required|max_length[20]|trim',
                    'target_ip' => 'required|trim',
                    'target_port' => 'required|trim',
                ];

                if ($this->request->getPost('siminstalled') == '2') {
                    $rules['mobile_no'] = 'required|trim';
                    $rules['apn_name'] = 'required|trim';
                }

                if (!$this->validate($rules)) {
                    $data['errmsg'] = $this->validator->listErrors();
                } else {
                    $arr = $this->request->getPost();
                    $arr = array_filter($arr, function($key) {
                        return $key !== 'add';
                    }, ARRAY_FILTER_USE_KEY);

                    // Handle the warranty date format
                    if (isset($arr['warranty_date']) && $arr['warranty_date'] != "") {
                        $arr['warranty_date'] = date('Y-m-d', strtotime(trim($arr['warranty_date'])));
                    }

                    // Set default values for nullable fields
                    $arr['mac_add'] = $arr['mac_add'] ?? 'NULL';
                    $arr['sdcard_no'] = $arr['sdcard_no'] ?? 'NULL';
                    $arr['sim_icc_id'] = $arr['sim_icc_id'] ?? 'NULL';

                    $arr['insertby'] = $this->sessdata['user_id'];
                    $arr['updateby'] = $this->sessdata['user_id'];
                    $arr['assigned_to'] = $this->sessdata['user_id'];
                    $arr['superdevid'] = 0;
                    $arr['group_id'] = $this->sessdata['group_id'];
                    $arr['typeofdevice'] = 'D';

                    $old_apn = $this->db->table('public.master_device_setup')
                        ->select('apn_name')
                        ->join('public.master_device_details', 'public.master_device_details.id = public.master_device_setup.deviceid')
                        ->where('public.master_device_details.serial_no', $arr['serial_no'])
                        ->get()
                        ->getRow()->apn_name;

                        // echo "<pre>";print_r($arr);exit();

                        $sql = "SELECT msg FROM data_insert_into_master_device_details_table(
                            ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?
                        )";

                        
                        $query = $this->db->query($sql, [
                            'edit',
                            $arr['serial_no'],
                            $arr['mobile_no'],
                            $arr['mac_add'],
                            $arr['sdcard_no'],
                            $arr['active'],
                            1,
                            $arr['imei_no'],
                            $arr['sim_icc_id'],
                            $arr['warranty_date'],
                            $arr['insertby'],
                            $arr['updateby'],
                            $arr['assigned_to'],
                            $arr['superdevid'],
                            $arr['group_id'],
                            $arr['typeofdevice'],
                            $arr['dynamiccode'],
                            $arr['siminstalled'],
                            $arr['duration'],
                            $arr['apn_name'],
                            $arr['apn_username'],
                            $arr['apn_pswd'],
                            $arr['target_ip'],
                            $arr['target_port'],
                            $arr['type']
                        ]);
                        
                        $rec = $query->getRow();

                    if ($rec->msg == 'Updated Successfully') {
                        if ($old_apn != $arr['apn_name']) {
                            $smsmessage = '*999#01#' . $arr['apn_name'] . '###';
                            // $this->devices_model->sendSMS($arr['mobile_no'], $smsmessage);
                        }

                        session()->setFlashdata('msg', "Device {$rec->msg}");
                        return redirect()->to("devices/lists");
                    } else {
                        $data['errmsg'] = $rec->msg;
                    }
                }
            }

            // Fetch device details for editing
            $data['main'] = $this->db->table('public.master_device_details')
                ->select('*,public.master_device_details.active as dev_active')
                ->join('public.master_device_setup', 'public.master_device_setup.deviceid = public.master_device_details.id')
                ->where('public.master_device_details.serial_no', $device_serial)
                ->get()
                ->getRow();

            $data['apn'] = $this->db->table('public.master_device_apn')
                ->select('*')
                ->where(['active' => 1])
                ->get()
                ->getResult();
        } else {
            $data['page_title'] = "Edit Device Configuration Mode";
            $data['sessdata'] = session()->get('sessdata');
            $data['serial_no'] = $device_serial = $this->request->getUri()->getSegment(4);
        
            // Handle form submission
            if ($this->request->getMethod() == 'POST') {
                // Set validation rules
                $rules = [
                    'devicemodeflag' => 'required|trim',
                ];
        
                // Validate input
                if (!$this->validate($rules)) {
                    $data['errmsg'] = $this->validator->listErrors();
                } else {
                    $devicemodeflag = $this->request->getPost('devicemodeflag');
                    $result['type'] = 'Configuration';
                    $result['serial_no'] = $device_serial;
                    $result['MSG'] = ($devicemodeflag == 1) ? '*999#47#0#%' : '*999#47#1#%';
                   // helper('MasterHelper');
                    $message = json_encode($result);
                    if (in_array($device_serial, $this->device_arr)) {
                        send_socket_test($message);
                    } else {
                        send_socket($message);
                    }
        
                    session()->setFlashdata('msg', "Device mode change request has been sent, please check after a few minutes");
                    return redirect()->to("devices/lists");
                }
            }
        
            // Fetch device data
            $device_data = $this->db->table('public.master_device_details AS mdd')
                ->select('mdd.id, mdd.assigned_to, ul.schemaname')
                ->join('public.user_login AS ul', 'ul.id = mdd.assigned_to', 'left')
                ->where('mdd.serial_no', $device_serial)
                ->get()
                ->getRow();
        
            $schema = $device_data->schemaname;
            $deviceid = $device_data->id;
        
            // Fetch device mode flag
            $data['main'] = $this->db->table("{$schema}.master_device_setup")
                ->select('devicemodeflag')
                ->where('deviceid', $deviceid)
                ->get()
                ->getRow();
        }

        $data['segment'] = $this->request->getUri()->getSegment(3);

        $data['middle'] = view('devices/devices_admin', $data);
        return view('mainlayout', $data);
    }

    public function getapnsettings()
    {
        // Get the APN name from the POST request
        $apn_name = trim($this->request->getPost('apn_name'));

        // Prepare and execute the query
        $query = $this->db->query("SELECT * FROM master_device_apn WHERE apn_value = ?", [$apn_name]);
        $prsnt = $query->getRow();

        // Return the result as JSON
        return $this->response->setJSON($prsnt);
    }

    public function uploadDeviceCsv()
    {
        // Check if the user is logged in
        if (!session()->has('login_sess_data')) {
            return redirect('/');
        }

        $data = [];
        $data['page_title'] = "Upload CSV Of Device";

        if ($this->request->getMethod() == 'POST' && $this->request->getFile('csvfile')) {
           
            $file = $this->request->getFile('csvfile');


            
            $data = [
                'name' => $file->getName(),
                'type' => $file->getMimeType(),
                'size' => $file->getSize(),
            ];



            // Validate the file type
            if ($file->isValid() && ($file->getMimeType() == 'application/vnd.ms-excel' || $file->getMimeType() == 'text/csv' || $file->getMimeType() == 'text/plain')) {
                $count = 0;
                $errcount = 0;

                // Open the CSV file for reading
                $fp = fopen($file->getTempName(), 'r');

                while (($csv_line = fgetcsv($fp, 1024)) !== FALSE) {
                    $count++;
                    if ($count == 1) {
                        continue; // Skip header row
                    }

                    if (isset($csv_line[0], $csv_line[1], $csv_line[2], $csv_line[3])) {
                        $insert_csv = [
                            'serial_no' => trim(str_replace("ID ", "", $csv_line[0])),
                            'imei_no' => trim(str_replace("IMEI ", "", $csv_line[1])),
                            'mobile_no' => trim(str_replace("Mob ", "", $csv_line[2])),
                            'apn_name' => trim($csv_line[3]),
                            'type' => trim($csv_line[4]),
                        ];

                        $operation = "add";
						$serial_no = $insert_csv['serial_no'];
						$mobile_no = $insert_csv['mobile_no'];
						$mac_add = 'null';
						$sdcard_no = 'null';
						$active = 2;
						$linked = 1;
						$imei_no = $insert_csv['imei_no'];
						$sim_icc_id = 'null';
						$warranty_date = date('Y-m-d');
						$insertby = $this->sessdata['user_id'];
						$updateby = $this->sessdata['user_id'];
						$assigned_to = $this->sessdata['user_id'];
						$superdevid = 0;
						$group_id = $this->sessdata['group_id'];
						$typeofdevice = 'D';
						$dynamiccode = rand(100000,999999);
						$siminstalled = 2;
						$duration = 20;
						$apn_name = $insert_csv['apn_name'];
						$apn_username = '';
						$apn_pswd = '';
						$target_ip = '120.138.8.188';
						$target_port = '7007';
						if($insert_csv['type'] == "P"){
							$type = "1";
					    }
						else {
							$type = "2";
						}
						
						// Use prepared statements to make it more secure
                        $sql = "SELECT msg FROM data_insert_into_master_device_details_table(
                            ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?
                        )";
    
                        $bindData = [
                            $operation,
                            $serial_no,
                            $mobile_no,
                            $mac_add,
                            $sdcard_no,
                            $active,
                            $linked,
                            $imei_no,
                            $sim_icc_id,
                            $warranty_date,
                            $insertby,
                            $updateby,
                            $assigned_to,
                            $superdevid,
                            $group_id,
                            $typeofdevice,
                            $dynamiccode,
                            $siminstalled,
                            $duration,
                            $apn_name,
                            $apn_username,
                            $apn_pswd,
                            $target_ip,
                            $target_port,
                            $type
                        ];
    
                        // Execute the prepared statement
                        $query = $this->db->query($sql, $bindData);
    
                        // Fetch the result row
                        $rec = $query->getRow();
                        if ($rec && $rec->msg == 'Added Successfully') {
                            $get_deviceid = $this->db->query("SELECT id FROM public.master_device_details WHERE serial_no = ?", [$serial_no])->getRow();
                            if ($get_deviceid) {
                                $added_deviceid = $get_deviceid->id;
                                $token = $this->random_strings(10);
                                $this->db->query("INSERT INTO public.master_device_token(deviceid, token) VALUES (?, ?)", [$added_deviceid, $token]);
                            }
                        } else {
                            $errcount++;
                        }
                    } else {
                        $errcount++;
                    }
                }
                fclose($fp);

                // Set flashdata message
                if ($count == 1) {
                    session()->setFlashdata('msg', "No device list uploaded.");
                } else {
                    if ($errcount == 0) {
                        session()->setFlashdata('msg', "Devices uploaded successfully.");
                    } else {
                        session()->setFlashdata('msg', "Error occurred at the time of uploading, all data not uploaded.");
                    }
                }
                return redirect()->to("devices/lists");
            } else {
                session()->setFlashdata('msg', "Invalid file format.");
                return redirect()->to("devices/lists");
            }
        }

        $data['sessdata'] = $this->sessdata;
        // Load views
        $data['middle'] = view('devices/uploaddevicecsv', $data);
        return view('mainlayout', $data);
    }

    private function random_strings($length_of_string)
    {
        // Generate a random string
        return substr(str_shuffle("0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ"), 0, $length_of_string);
    }

    public function assigndevicetousercsv()
    {
        // Check if user is logged in
        if (!session()->has('login_sess_data')) {
            return redirect()->to("/");
        }

        $data = [];
        $data['page_title'] = "Assign Device";
    
        if ($this->request->getMethod() == 'POST' && $this->request->getFile('csvfile')) {
            $file = $this->request->getFile('csvfile');

          
            // Check if the file is a valid CSV
            if ($file && ($file->getMimeType() == 'application/vnd.ms-excel' || $file->getMimeType() == 'text/csv' || $file->getMimeType() == 'text/plain')) {
                $count = 0;
                $errcount = 0;
                $fileStream = fopen($file->getTempName(), 'r') or die("Can't open file");

                while ($csvLine = fgetcsv($fileStream, 1024)) {
                    $count++;
                    if ($count == 1) {
                        continue; // Skip header row
                    }
                    
                    if (isset($csvLine[0])) {
                        $serial_no = trim(str_replace("ID ", "", $csvLine[0]));

                        // Prepare variables from session
                        $parent_id = (int) $this->sessdata['parent_id'];
                        $user_id = (int) $this->sessdata['user_id'];
                        $group_id = 2;//$this->sessdata['group_id'];
                        $schemaname = $this->sessdata['schemaname'];

                        // Check if serial number exists
                        $prsnt = $this->db->table('public.master_device_details')
                                          ->where('serial_no', $serial_no)
                                          ->countAllResults();
                        
                        // echo "<pre>";print_r($prsnt);exit();

                        if ($prsnt != 1) {
                            $errcount++;
                        } else {
                            $user_id = (int) $this->request->getPost('user_id');

                            $userdata = $this->db->table('public.user_login')
                                                 ->where('id', $user_id)
                                                 ->get()
                                                 ->getRow();

                            if ($userdata) {
                                // echo $userdata->parent_id;exit();
                              //  print_r([$serial_no, $user_id, $group_id, 'assign']);
                              //  exit;
                                $rec = $this->db->query("SELECT msg FROM device_assignment(?,?,?,?)", 
                                    [$serial_no, $user_id, $group_id, 'assign'])->getRow();

                                echo "<pre>";print_r($rec);
                                //exit();
                                if ($rec->msg == 'Device successfully registered') {
                                    $devc = $this->db->table('public.master_device_details')
                                                     ->where('serial_no', $serial_no)
                                                     ->get()
                                                     ->getRow();
                                    $devicemasterid = $devc->id;
                                    $statusval = 1;

                                    $this->db->transStart();

                                    $sel_link = $this->db->table("{$schemaname}.master_device_assign")
                                                         ->where(['group_id' => 2, 'deviceid' => $devicemasterid])
                                                         ->countAllResults();

                                                         echo $sel_link,"xxxxxxxxxxxx";


                                    if ($sel_link > 0) {

                                        $setup_exist = $this->db->table("{$schemaname}.master_device_setup")
                                            ->where('deviceid', $devicemasterid)
                                            ->countAllResults();

                                        if ($setup_exist == 0) {
                                           /* echo "INSERT INTO {$schemaname}.master_device_setup
                                            (parent_id, user_id, group_id, deviceid, device_name, icon_details, images_details, sos1_no, sos2_no, sos3_no, duration, call1_no, call2_no, call3_no, apn_name, apn_username, apn_pswd, target_ip, target_port, insertby, updateby, inserttime, updatetime, active, working_start_time, working_end_time, alertemails, alertphnumbers, flag, switchoffrestrict_start_time, switchoffrestrict_end_time)
                                            SELECT ?, ?, ?, deviceid, device_name, icon_details, images_details, sos1_no, sos2_no, sos3_no, duration, call1_no, call2_no, call3_no, apn_name, apn_username, apn_pswd, target_ip, target_port, insertby, updateby, inserttime, updatetime, active, working_start_time, working_end_time, alertemails, alertphnumbers, flag, switchoffrestrict_start_time, switchoffrestrict_end_time 
                                            FROM public.master_device_setup 
                                            WHERE deviceid = ?";
                                            print_r([$parent_id, $user_id, $group_id, $devicemasterid]);
                                            die();*/
                                            $this->db->query("INSERT INTO {$schemaname}.master_device_setup
                                                (parent_id, user_id, group_id, deviceid, device_name, icon_details, images_details, sos1_no, sos2_no, sos3_no, duration, call1_no, call2_no, call3_no, apn_name, apn_username, apn_pswd, target_ip, target_port, insertby, updateby, inserttime, updatetime, active, working_start_time, working_end_time, alertemails, alertphnumbers, flag, switchoffrestrict_start_time, switchoffrestrict_end_time)
                                                SELECT ?, ?, ?, deviceid, device_name, icon_details, images_details, sos1_no, sos2_no, sos3_no, duration, call1_no, call2_no, call3_no, apn_name, apn_username, apn_pswd, target_ip, target_port, insertby, updateby, inserttime, updatetime, active, working_start_time, working_end_time, alertemails, alertphnumbers, flag, switchoffrestrict_start_time, switchoffrestrict_end_time 
                                                FROM public.master_device_setup 
                                                WHERE deviceid = ?", [$parent_id, $user_id, $group_id, $devicemasterid]);
                                        }
                                    }

                                    $devc = $this->db->table('public.master_device_details')
                                                     ->where('id', $devicemasterid)
                                                     ->get()
                                                     ->getRow();

                                    $this->db->transComplete();

                                    $response = [];
                                    if ($this->db->transStatus() !== false) {
                                        if ($statusval == 1 && $group_id == 2) {
                                            $result['MSG'] = "{$devc->serial_no},{$parent_id},{$devicemasterid},{$user_id},{$schemaname},{$group_id}";
                                            $result['type'] = "Device Link";
                                            $message = json_encode($result);

                                            if (in_array($devc->serial_no, $this->device_arr)) {
                                                // send_socket_test($message);
                                            } else {
                                                // send_socket($message);
                                            }
                                        }
                                    }
                                } else {
                                    $errcount++;
                                }
                            }
                        }
                    } else {
                        $errcount++;
                    }
                }

                fclose($fileStream);

                if ($count == 1) {
                    session()->setFlashdata('msg', "No device list uploaded.");
                } else {
                    if ($errcount == 0) {
                        session()->setFlashdata('msg', "Devices assigned successfully.");
                    } else {
                        session()->setFlashdata('msg', "Error occurred at the time of uploading, all data not assigned.");
                    }
                }

                return redirect()->to("devices/lists");
            } else {
                session()->setFlashdata('msg', "Invalid file format.");
                return redirect()->to("devices/lists");
            }
        }

        $data['sessdata'] = $this->sessdata;
        $data['userdd'] = $this->db->table('user_login')
                                    ->where('parent_id', $data['sessdata']['user_id'])
                                    ->get()
                                    ->getResult();
        $data['middle'] = view('devices/assigndevicetousercsv', $data);
        return view('mainlayout', $data);
    }

    public function devices_delete_admin($device_serial = null) 
    {
        // Get the device serial number from the URL segment
        $device_serial = $this->request->getUri()->getSegment(3) ?? $device_serial;

        // Query to check if the device is assigned
        $devc = $this->db->table('public.master_device_details')
            ->select('serial_no, master_device_details.id')
            ->join('public.master_device_assign', 'master_device_details.id = master_device_assign.deviceid', 'left')
            ->where('serial_no', $device_serial)
            ->groupBy('master_device_details.id')
            ->get()
            ->getRow();

        if (empty($devc->no_of_record)) {
            // Start database transaction
            $this->db->transStart();

            // Get device ID
            $devid = $this->db->table('public.master_device_details')
                ->select('id')
                ->where('serial_no', $device_serial)
                ->get()
                ->getRow();

            if ($devid) {
                // Delete from master_device_setup
                $this->db->table('public.master_device_setup')
                    ->where('deviceid', $devid->id)
                    ->delete();

                // Delete from master_device_details
                $this->db->table('public.master_device_details')
                    ->where('serial_no', $device_serial)
                    ->delete();
            }

            // Complete the transaction
            $this->db->transComplete();

            if ($this->db->transStatus() === false) {
                session()->setFlashdata('msg', "Failed to Delete device");
            } else {
                session()->setFlashdata('msg', "Device Deleted successfully");
            }
        } else {
            session()->setFlashdata('msg', "Device already assigned to Account, Cannot Be Deleted");
        }

        // Redirect to the device lists
        return redirect()->to("devices/lists");
    }

    public function unassignDeviceToUserCsv()
    {
        $data = [];
        $data['page_title'] = "Unassign Device";

        if ($this->request->getMethod() == 'POST') {
            // && $this->request->getFile('csvfile')->isValid()) {

            $file = $this->request->getFile('csvfile');

            if (!$file->isValid()) {
                // Handle error
                $error = $file->getErrorString();
                echo "File upload error: " . $error; 
                exit();
            }
            // Check if the file is a CSV
            if ($file->getMimeType() == 'text/csv' || $file->getMimeType() == 'text/plain' || $file->getMimeType() == 'directory') {
                $count = 0;
                $errcount = 0;

                // Open the uploaded file
                if (($handle = fopen($file->getTempName(), 'r')) !== false) {
                    while (($csv_line = fgetcsv($handle, 1024)) !== false) {
                        $count++;
                        if ($count == 1) {
                            continue; // Skip header row
                        }

                        $serial_no = isset($csv_line[0]) ? trim(str_replace("ID ", "", $csv_line[0])) : null;

                        if ($serial_no) {
                            $parent_id = $this->sessdata['parent_id'];
                            $user_id = $this->sessdata['user_id'];
                            $schemaname = $this->sessdata['schemaname'];
                            $group_id = 2;

                            // Check if the device exists
                            $device = $this->deviceModel->where('serial_no', $serial_no)->get()->getRow();

                            if (!$device) {
                                $errcount++;
                            } else {
                                $userdata = $this->db->table('public.user_login')
                                                 ->where('id', $device->assigned_to)
                                                 ->get()
                                                 ->getRow();
                               /* $rec = $this->db->query("SELECT msg FROM device_assignment_details_update(?,?,?,?,?)", 
                                    [$serial_no,(int) $userdata->parent_id,(int) $device->assigned_to,$group_id,'unassign'])->getRow();*/


                                $rec = $this->db->query("SELECT msg FROM device_assignment(?,?,?,?)", 
                                    [$serial_no,(int) $device->assigned_to,$group_id,'unassign'])->getRow();

                             

                                if ($rec->msg == 'Device successfully unregistered') {
                                    // $this->deviceModel->update($device->id, ['active' => false]);
                                    $result = [
                                        'MSG' => $serial_no,
                                        'type' => "Device Unlink"
                                    ];
                                    $message = json_encode($result);

                                    // Sending socket notification
                                    if (in_array($serial_no, $this->device_arr)) {
                                        // send_socket_test($message);
                                    } else {
                                        // send_socket($message);
                                    }
                                } else {
                                    $errcount++;
                                }
                            }
                        } else {
                            $errcount++;
                        }
                    }
                    fclose($handle);
                }

                if ($count == 1) {
                    session()->setFlashdata('msg', "No device list uploaded.");
                } else {
                    if ($errcount == 0) {
                        session()->setFlashdata('msg', "Devices unassigned successfully.");
                    } else {
                        session()->setFlashdata('msg', "Error occurred at the time of uploading, all data not unassigned.");
                    }
                }

                return redirect()->to("devices/lists");
            } else {
                session()->setFlashdata('msg', "Invalid file format.");
                return redirect()->to("devices/lists");
            }
        }

        $data['sessdata'] = $this->sessdata;
        $data['middle'] = view('devices/unassigndevicetousercsv', $data);
        return view('mainlayout', $data);
    }

    public function statusChange()
    {
        // Retrieve session data
        $schemaname = $this->sessdata['schemaname'];
        $group_id = 2; //$this->session->get('group_id');
        $devicemasterid = $this->request->getUri()->getSegment(3);
        $statusval = $this->request->getUri()->getSegment(4);
        // $devicemasterid = base64_decode(urldecode($devicemasterid));
        $devicemasterid .= str_repeat('=', (4 - strlen($devicemasterid) % 4) % 4); // Add padding if necessary
        $devicemasterid = base64_decode(strtr($devicemasterid, '-_', '+/'));

        // echo $devicemasterid; exit();

        // Start transaction
        $this->db->transStart();

        $device = $this->deviceModel->where('id', $devicemasterid)->get()->getRow();

        $userdata = $this->db->table('public.user_login')
            ->where('id', $device->assigned_to)
            ->get()
            ->getRow();

        

        // Query to check unregistration status
        $rec = $this->db->query("SELECT msg FROM device_assignment(?,?,?,?)", 
            [$device->serial_no,(int) $device->assigned_to,$group_id,'unassign'])->getRow();

        // echo $rec->msg; exit();

        if ($rec->msg == 'Device successfully unregistered') {
            $savedata_details = ['active' => $statusval];
            
            $this->db->query("UPDATE {$schemaname}.master_device_details SET active = $statusval WHERE superdevid = ?", [$device->id]);
            $this->deviceModel->update($device->id, $savedata_details);
        }

        // Complete transaction
        $this->db->transComplete();
        $response = [];
        // echo $this->db->transStatus(); exit();

        if ($this->db->transStatus() && $rec->msg == 'Device successfully unregistered') {
            $result = [];
            $result['MSG'] = $device->serial_no . ',' . $userdata->parent_id . ',' . $devicemasterid . ',' . $userdata->id . ',' . $schemaname . ',' . $group_id;
            $result['type'] = $statusval == 1 ? "Device Link" : "Device Unlink";
            $message = json_encode($result);

            if (in_array($device->serial_no, $this->device_arr)) {
                // send_socket_test($message);
            } else {
                // send_socket($message);
            }

            $response = ["suc" => 1, "msg" => "Status Changed Successfully..."];
        } else {
            $response = ["suc" => 2, "msg" => "Error in Status Change..."];
        }

        return $this->response->setJSON($response);
    }

    public function uploadsoscallcsv()
    {
        $schemaname = $this->schema; // Assuming you have set this in your class

        if ($this->request->getMethod() == 'POST' && $this->request->getFile('csvfile')->isValid()) {
            $file = $this->request->getFile('csvfile');

            // echo $file->getMimeType();exit();

            // Check for valid file types
            if ($file->getMimeType() == 'application/vnd.ms-excel' || $file->getMimeType() == 'text/csv' || $file->getMimeType() == 'text/plain') {
                $count = 0;
                $errcount = 0;

                // Open the uploaded CSV file
                $fp = fopen($file->getTempName(), 'r') or die("can't open file");

                // Start transaction
                $this->db->transStart();

                while ($csv_line = fgetcsv($fp, 1024)) {
                    $count++;
                    if ($count == 1) {
                        continue; // Skip the first row
                    }

                    // Assuming the first column is the device serial number
                    $device_serial = str_replace("ID ", "", trim($csv_line[0]));
                    $dev_data = $this->db->table('master_device_details')->select('id')->where('serial_no', $device_serial)->get()->getRow();

                    if ($dev_data) {
                        $deviceid = $dev_data->id;
                        $sos1_no = str_replace("Mob ", "", trim($csv_line[1]));
                        $sos2_no = str_replace("Mob ", "", trim($csv_line[2]));
                        $sos3_no = str_replace("Mob ", "", trim($csv_line[3]));
                        $call1_no = str_replace("Mob ", "", trim($csv_line[4]));
                        $call2_no = str_replace("Mob ", "", trim($csv_line[5]));
                        $call3_no = str_replace("Mob ", "", trim($csv_line[6]));

                        $savedata = [
                            'sos1_no' => $sos1_no,
                            'sos2_no' => $sos2_no,
                            'sos3_no' => $sos3_no,
                            'call1_no' => $call1_no,
                            'call2_no' => $call2_no,
                            'call3_no' => $call3_no,
                        ];

                        if (!empty($deviceid) || !empty($sos1_no) || !empty($call1_no)) {
                            // Update the device setup table
                            $this->db->table("{$schemaname}.master_device_setup")->update($savedata, ['deviceid' => $deviceid]);

                            $result['type'] = 'Configuration';
                            $result['serial_no'] = $device_serial;
                            $result['MSG'] = '*999#47#1#' . '%*999#11#' . $sos1_no . '#'
                                . $sos2_no . '#' . $sos3_no . '#'
                                . '%*999#12#' . $call1_no . '#' . $call2_no . '#' . $call3_no . '#%';

                            $message = json_encode($result);
                            // send_socket($message); // Adjust this function if needed
                        } else {
                            $errcount++;
                        }
                    }
                }

                fclose($fp); // Close the file after processing

                // Complete transaction
                $this->db->transComplete();
                if ($this->db->transStatus()) {
                    if ($count == 1) {
                        session()->setFlashdata('msg', "No data in File.");
                    } else {
                        if ($errcount == 0) {
                            session()->setFlashdata('msg', "File Saved successfully.");
                        } else {
                            session()->setFlashdata('msg', "Error occurred at the time of uploading, all data not saved.");
                        }
                    }
                    return redirect()->to("devices/lists");
                }
            } else {
                session()->setFlashdata('msg', "Invalid file format.");
            }
        }

        $data['page_title'] = "SOS/Call Setup";
        $data['middle'] = view('devices/uploadsoscallcsv', $data);
        return view('mainlayout', $data);
    }

    public function deviceconfiguration_ajax() {
        $encoded_dev_id = $this->uri->getSegment(3);
        $data['dev_id'] = $dev_id = base64_decode(trim(urldecode($encoded_dev_id)));
        $data['sessdata'] = $this->sessdata;
        
        $user_id = $this->session->get('user_id');

        $select = "devicesetup.*,(SELECT device_name FROM {$this->schema}.master_device_setup 
                    WHERE id=(SELECT MAX(id) FROM {$this->schema}.master_device_setup 
                    WHERE inserttime::date<=CURRENT_DATE AND deviceid= devicesetup.deviceid)) AS device_name_new,
                    config1.flag AS intervalflag, config2.flag AS sosflag,
                    config3.flag AS apnflag, config4.flag AS callflag";

        $builder = $this->db->table("{$this->schema}.master_device_setup AS devicesetup");
        $builder->select($select);
        $builder->join("{$this->schema}.master_device_setup_config_string AS config1", 'config1.deviceid = devicesetup.deviceid AND config1.configtype = 1 AND config1.flag = 1', 'left');
        $builder->join("{$this->schema}.master_device_setup_config_string AS config2", 'config2.deviceid = devicesetup.deviceid AND config2.configtype = 2 AND config2.flag = 1', 'left');
        $builder->join("{$this->schema}.master_device_setup_config_string AS config3", 'config3.deviceid = devicesetup.deviceid AND config3.configtype = 3 AND config3.flag = 1', 'left');
        $builder->join("{$this->schema}.master_device_setup_config_string AS config4", 'config4.deviceid = devicesetup.deviceid AND config4.configtype = 4 AND config4.flag = 1', 'left');
        $builder->where(['devicesetup.deviceid' => $dev_id, 'devicesetup.active' => 1]);

        $data['getdev'] = $builder->get()->getRowArray();
        
        $data['getddm'] = $this->db->table("{$this->schema}.master_device_details AS ddm")
                            ->select("ddm.typeofdevice, ddm.dynamiccode, ddm.siminstalled, ddm.serial_no")
                            ->where("ddm.superdevid", $dev_id)
                            ->get()
                            ->getRowArray();
        
        
        $common_icons = $this->db->query("SELECT id, name, icon_path FROM public.device_icons WHERE active = 1")->getResultArray();
        
        $custom_icons = $this->db->query("SELECT id, name, icon_path FROM public.device_user_icons WHERE active = 1 AND user_id = ?", [$user_id])->getResultArray();
        
        $data['icons'] = array_merge($common_icons, $custom_icons);
        $data['page_title'] = "Device Configuration";
        
        $data['middle'] = view('devices/deviceconfiguration_ajax', $data);
        return view('ajax', $data);
    }




}
