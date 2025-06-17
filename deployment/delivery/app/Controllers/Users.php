<?php

namespace App\Controllers;

use App\Models\UsersModel;
use App\Models\RolesModel;
use App\Models\DeviceModel;
use CodeIgniter\Controller;

use CodeIgniter\HTTP\Client;

class Users extends Controller
{
    protected $session;
    protected $usersModel;
    protected $rolesModel;
    protected $deviceModel;

    public function __construct()
    {
        $this->session = session();
        $this->usersModel = new UsersModel();
        $this->rolesModel = new RolesModel();
        $this->deviceModel = new DeviceModel();
        $this->db = \Config\Database::connect();

        helper(['form', 'url', 'master', 'communication']);

        if ($this->session->get('login_sess_data')) {
            $this->sessdata = $this->session->get('login_sess_data');
            $this->schema = $this->sessdata['schemaname'];
        }
    }

    public function index()
    {
        echo "hii";
        
        if ($this->session->get('login_sess_data')) {
            if ($this->session->get('login_sess_data')['group_id'] == 1) {
                return redirect()->to('controlcentre/adminview');
            } else {
                return redirect()->to('dashboard');
            }
        }

        $appName = getenv('app.appName');

        $data = [];
        $data['page_title'] = $appName;

        if ($this->request->getMethod() == 'POST') {

            $rules = [
                'username' => 'required|trim',
                'password' => 'required',
            ];

            if (!$this->validate($rules)) {
                $data['errmsg'] = $this->validator->listErrors();
            } else {
                $username = $this->request->getPost('username');
                $password = md5($this->request->getPost('password'));
                // $password = password_hash($this->request->getPost('password'), PASSWORD_BCRYPT);

                $check_username_password = $this->usersModel->check_login($username, $password);

                if ($check_username_password) {
                    unset($check_username_password['id']);
                    unset($check_username_password['password']);
                    unset($check_username_password['insertby']);
                    unset($check_username_password['updateby']);
                    unset($check_username_password['inserttime']);
                    unset($check_username_password['updatetime']);

                    $parentusergroup = $this->usersModel->getParentGroupId($check_username_password['parent_id']);
                    if ($parentusergroup) {
                        $check_username_password['parent_group_id'] = $parentusergroup['group_id'];
                    }

                    $this->session->set('login_sess_data', $check_username_password);
                    // echo "<pre>";print_r($this->session->get('login_sess_data')['firstname']);
                    // echo "<pre>";print_r($check_username_password);
                    // exit();

                    if ($this->session->get('login_sess_data')['group_id'] == 1) {
                        return redirect()->to('controlcentre/adminview');
                    } else {
                        return redirect()->to('dashboard');
                    }
                } else {
                    $this->session->setFlashdata('msg', "Username or Password is incorrect");
                }
            }
        }


        return view('users/login', $data);
    }

    public function profile()
    {
        $data = [];

        // Get user ID from session
        $userId = $this->sessdata['user_id'];

        $data['page_title'] = "Account Details";
        $data['userid'] = $userId;

        // Fetch user data
        $data['userdata'] = $this->usersModel->getUserData($userId);

        // Load the profile view
        $data['middle'] = view('users/profile', $data);

        // Load the main layout view
        return view('mainlayout', $data);
    }

    public function lists()
    {
        // Check if user is logged in
        if (!session()->has('login_sess_data')) {
            return redirect()->to("/");
        }

        $data = [];
        $cond = " AND active = ?";
        $condArray = [1];

        if ($this->session->get('login_sess_data')['group_id'] == 1) {
            $data['groupdd'] = $this->usersModel->getgroupdd($cond, $condArray);
            $data['roledd'] = $this->usersModel->getroledd($cond . " AND id != ?", [1, 2]);
        } else {
            $data['roledd'] = $this->usersModel->getroledd($cond, $condArray);
        }

        $data['sessdata'] = $this->session->get('login_sess_data');
        $data['page_title'] = "User Management";
        
        // Count users
        $userCount = $this->db->query("SELECT COUNT(*) AS count FROM public.user_login WHERE parent_id = ?", [$data['sessdata']['user_id']])->getRow();
        $data['usercount'] = $userCount->count;

        // Load views
        $data['middle'] = view('users/lists', $data);
        return view('mainlayout', $data);
    }

    public function getAllUsers()
    {
        $limit = $this->request->getVar('length');
        $offset = $this->request->getVar('start');
        $draw = $this->request->getVar('draw');

        $conditions = [
            'name' => $this->request->getVar('name'),
            'email' => $this->request->getVar('email'),
            'contact' => $this->request->getVar('contact'),
            'group' => $this->request->getVar('group'),
            'role' => $this->request->getVar('role'),
            'active' => $this->request->getVar('active')
        ];

        // Check user group
        if ($this->session->get('login_sess_data')['group_id'] == 1) {
            $conditions['group'] = $this->request->getVar('group');
        }

        $data_all = $this->usersModel->getUsers($offset, $limit, $conditions);

        $data = $data_all['filtereddata'];
        // echo "<pre>";print_r($data_all['totaldata']);exit();
        $recordsTotal = $data_all['totaldata'];
        $datafinal = [];

        if (!empty($data)) {
            foreach ($data as $key => $row) {
                $datafinal[$key]['name'] = $row->firstname . " " . $row->lastname . " (" . $row->username . ")";

                // Build actions
                $action = '<div class="dropdown">
                            <button class="btn ddaction dropdown-toggle" type="button" data-toggle="dropdown">
                                <i class="fa fa-gear"></i>
                                <span class="caret"></span>
                            </button>
                            <ul class="dropdown-menu action-dd">';

                $action .= '<li>
                                <a href="' . base_url('/') . 'users/edit/' . urlencode(base64_encode($row->user_id)) . '">
                                    <i class="fa fa-pencil" aria-hidden="true"></i> Edit
                                </a>
                            </li>';

                $action .= '<li class="divider"></li>
                            <li>
                                <a href="' . base_url('/') . 'users/accountdetails/' . urlencode(base64_encode($row->user_id)) . '">
                                    <i class="fa fa-search" aria-hidden="true"></i> View 
                                </a>
                            </li>';

                $action .= '<li class="divider"></li>
                            <li>
                                <a href="javascript:void(0)">
                                    <i class="fa fa-trash-o" aria-hidden="true"></i> Delete
                                </a>
                            </li>';

                $url = base_url() . 'users/statusChange/' . urlencode(base64_encode($row->user_id));
                if ($row->loginactive == 'Active') {
                    $action .= '<li class="divider"></li>
                                <li>
                                    <a href="' . $url . '/2/1" class="statuschange">
                                        <i class="fa fa-arrow-down" aria-hidden="true"></i> Deactive
                                    </a>
                                </li>';
                } else {
                    $action .= '<li class="divider"></li>
                                <li>
                                    <a href="' . $url . '/1/2" class="statuschange">
                                        <i class="fa fa-arrow-up" aria-hidden="true"></i> Active
                                    </a>
                                </li>';
                }
                $action .= '</ul></div>';

                $datafinal[$key]['mobile'] = $row->mobile;
                $datafinal[$key]['email'] = $row->email;
                $datafinal[$key]['group'] = ($row->user_group == 'User') ? "Individual" : $row->user_group;
                $datafinal[$key]['role'] = $row->role;
                $datafinal[$key]['active'] = $row->loginactive;
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

    public function logout() {
        $sessdata = $this->session->get('login_sess_data');

        // Destroy the session
        $this->session->destroy();

        // Redirect based on user group ID
        if (isset($sessdata['usergroupid']) && $sessdata['usergroupid'] == 1) {
            return redirect()->to('/superusers');
        } else {
            return redirect()->to('/');
        }
    }

    public function add()
    {
        $userCount = $this->db->query("SELECT COALESCE(COUNT(*), 0) AS count, group_id FROM public.user_login WHERE parent_id = ? GROUP BY group_id", [$this->sessdata['user_id']])->getRow();
        if ($userCount !== null) {
            // If userCount is not null, proceed with the logic
            $data['usercount'] = $userCount->count;
        
            if (!empty($userCount->count) && ($data['usercount'] > 1 && $userCount->group_id == 3)) {
                session()->setFlashdata('msg', "Maximum limit exceeded for creating account");
                return redirect()->to("users/lists");
            }
        }
        // $data['usercount'] = $userCount->count;
        // if(!empty($userCount->count) && ($data['usercount'] > 1 && $userCount->group_id == 3)) {
        //     session()->setFlashdata('msg', "Maximum limit exceeded for creating account");
        //     return redirect()->to("users/lists");
        // }

        $data = [
            'page_title' => "Add User",
        ];
        
        if ($this->request->getMethod() == 'POST') {

            $rules = [
                'firstname' => 'required|trim',
                'lastname' => 'required|trim',
                'organisation' => 'required|trim',
                'email' => 'required|trim|valid_email|is_unique[user_login.email]',
                'mobile' => 'required|trim|integer|min_length[10]|max_length[10]',
                'active' => 'required|trim|integer',
            ];

            if ($this->sessdata['group_id'] == 3) {
                $rules = array_merge($rules, [
                    'numberofdevice' => 'required|trim|integer',
                    'expirydate' => 'required|trim',
                    'configurationsms' => 'required|trim',
                    'notificationsms' => 'required|trim',
                    'neotificationemail' => 'required|trim',
                    'notificationtotalsms' => 'required|trim',
                    'numberofadmin' => 'required|trim',
                    'allowedtocreateuser' => 'required|trim',
                    'numberofuser' => 'required|trim',
                ]);
            }

            // Set the combined rules

            if (!$this->validate($rules)) {
                $data['errmsg'] = $this->validator->listErrors();
            } else {
                $userkey = trim($this->request->getPost('organisation'));
                if ($this->sessdata['group_id'] == 1) {
                    $userkey = str_replace(' ', '', $userkey);
                    $schemaname = strtolower(substr($userkey, 0, 4));
                    $check_schmaname = $this->usersModel->where('schemaname', $schemaname)->countAllResults();
                    if ($check_schmaname > 0) {
                        $schemaname .= $check_schmaname;
                    }
                } else {
                    $schemaname = $this->sessdata['schemaname'];
                }

                $getdat = [
                    'parent_id' => $this->sessdata['user_id'],
                    'userkey' => $userkey,
                    'schemaname' => $schemaname,
                    'insertby' => $this->sessdata['user_id'],
                ];

                $savedata = [];
                foreach ($this->request->getPost() as $key => $postarr) {
                    if ($key !== "add") {
                        $val = trim($postarr);
                        if ($key == "password") {
                            // $val = password_hash($val, PASSWORD_BCRYPT);
                            $val = md5($val);
                        } elseif ($val == "") {
                            $val = null;
                        }
                        $savedata[$key] = $val;
                    }
                }

                $savedata = array_merge($savedata, $getdat);

                if ($this->usersModel->addusers($savedata)) {
                    if ($this->sessdata['group_id'] == 3) {
                        $data = [
                            'username' => $this->request->getPost('username')
                        ];
                        $superadmin_msg = view('email/accountcreate_mail', $data);
                        // Uncomment and configure email sending as needed
                        // sendEmailReg('notification@9trax.com', $this->request->getPost('username'), '', '', 'Account', $superadmin_msg, null, null);
                    }

                    if ($this->sessdata['group_id'] == 1) {
                        // Initialize the HTTP client
                        $client = \Config\Services::curlrequest();
                        $databaseConfig = config('Database');
                        $dbName = $databaseConfig->default['database'];
                        
                        $username = $this->request->getPost('username');
                        $username = strstr($username, '@', true);

                        //////// add workspace
                        // Prepare the request data
                        $data = [
                            'workspace' => [
                                'name' => $dbName
                            ]
                        ];

                        // Set the request options
                        $response = $client->request('POST', 'http://localhost:8080/geoserver/rest/workspaces', [
                            'headers' => [
                                'Content-Type' => 'application/json',
                                'Authorization' => 'Basic YWRtaW46Z2Vvc2VydmVy'
                            ],
                            'json' => $data,
                            'timeout' => 0,
                            'http_errors' => false, // Disable HTTP errors if you want to handle them manually
                        ]);

                        // Get the response body
                        $responseBody = $response->getBody();


                        //////// add stores
                        // Prepare the request data

                        

                        $dataStore = [
                            "dataStore" => [
                                "name" => $dbName,
                                "connectionParameters" => [
                                    "entry" => [
                                        ["@key" => "host", "$" => "localhost"],
                                        ["@key" => "port", "$" => "5432"],
                                        ["@key" => "database", "$" => $dbName],
                                        ["@key" => "schema", "$" => $savedata['schemaname']],
                                        ["@key" => "user", "$" => "postgres"],
                                        ["@key" => "passwd", "$" => "DwtwN6J=fc?*"],
                                        ["@key" => "dbtype", "$" => "postgis"],
                                    ]
                                ]
                            ]
                        ];

                        // Set the request options
                        $responseStore = $client->request('POST', 'http://localhost:8080/geoserver/rest/workspaces/'.$dbName.'/datastores', [
                            'headers' => [
                                'Content-Type' => 'application/json',
                                'Authorization' => 'Basic YWRtaW46Z2Vvc2VydmVy'
                            ],
                            'json' => $dataStore,
                            'timeout' => 0,
                            'http_errors' => false, // Disable HTTP errors if you want to handle them manually
                        ]);

                        // Get the response body
                        $responseBodyStore = $responseStore->getBody();

                        //////// add layer
                        $arr = [
                            'master_polldata',
                            'master_cabindata',
                            'master_kmpdata',
                            'master_bridgedata',
                            'master_stationdata',
                            'master_signaldata',
                            'master_lcdata',
                        ];

                        foreach($arr as $ar) {
                            // Prepare the request data
                            $dataLayer = [
                                "featureType" => [
                                    "name" => $ar,
                                    "nativeName" => $ar,
                                    "title" => $ar,
                                    "srs" => "EPSG:4326",
                                    "enabled"=> true,
                                    "nativeBoundingBox"=> [
                                        "minx"=> -180.0,
                                        "maxx"=> 180.0,
                                        "miny"=> -90.0,
                                        "maxy"=> 90.0,
                                        "crs"=> "EPSG:4326"
                                    ],
                                    "latLonBoundingBox"=> [
                                        "minx"=> -180.0,
                                        "maxx"=> 180.0,
                                        "miny"=> -90.0,
                                        "maxy"=> 90.0,
                                        "crs"=> "EPSG:4326"
                                    ]
                                ]                         
                            ];

                            // Set the request options
                            $responseLayer = $client->request('POST', 'http://localhost:8080/geoserver/rest/workspaces/'.$dbName.'/datastores/'.$dbName.'/featuretypes', [
                                'headers' => [
                                    'Content-Type' => 'application/json',
                                    'Authorization' => 'Basic YWRtaW46Z2Vvc2VydmVy'
                                ],
                                'json' => $dataLayer,
                                'timeout' => 0,
                                'http_errors' => false, // Disable HTTP errors if you want to handle them manually
                            ]);

                            // Get the response body
                            $responseLayer = $responseLayer->getBody();
                           // echo $responseLayer;
                           // die();
                            //$dataLayer = [];
                        }

                        

                    }
                    return redirect()->to('/users/lists')->with('msg', "Account created successfully");
                } else {
                    $data['errmsg'] = "Failed to create account";
                }
            }
        }

        // exit();

        $data['pass'] = $this->random_password();
        // Fetching group data based on user group
        $data['groupdd'] = $this->getGroupDropdownData();
        $data['roledd'] = [];

        $data['sessdata'] = $this->sessdata;
        $data['middle'] = view('users/add', $data);
        return view('mainlayout', $data);
    }

    protected function random_password($length = 8)
    {
        $chars = "abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789!@#$%^&*()_-=+;:,.?";
        return substr(str_shuffle($chars), 0, $length);
    }

    protected function getGroupDropdownData()
    {
        $groupIds = [];

        switch ($this->sessdata['group_id']) {
            case 1:
                $groupIds = [3];
                break;
            case 3:
                $groupIds = [4, 2];
                break;
            case 4:
                $groupIds = [5, 2];
                break;
            case 5:
                $groupIds = [8, 2];
                break;
            case 8:
                $groupIds = [2];
                break;
            case 2:
                $groupIds = [0];
                break;
            default:
                return [];
        }

        return $this->usersModel->getUserGroupData($groupIds);
    }

    public function edit($usermasterid)
    {
        $data = [];
        $data['page_title'] = "Edit User";
        $data['userid'] = $usermasterid;
        $usermasterid = base64_decode(urldecode($usermasterid));
        $userdata = $this->usersModel->get_user($usermasterid);
        
        if ($this->request->getMethod() == 'POST') {
            // Validation Rules
            $validation = \Config\Services::validation();
            $rules = [
                'firstname' => 'required|trim',
                'lastname' => 'required|trim',
                'mobile' => 'required|trim|max_length[15]|min_length[10]|integer',
            ];
            
            // Conditional Rules for specific group
            if ($this->sessdata['group_id'] == 3) {
                $rules = array_merge($rules, [
                    'numberofdevice' => 'required|integer',
                    'expirydate' => 'required|trim',
                    'configurationsms' => 'required|trim',
                    'notificationsms' => 'required|trim',
                    'neotificationemail' => 'required|trim',
                    'notificationtotalsms' => 'required|trim',
                    'numberofadmin' => 'required|trim',
                    'allowedtocreateuser' => 'required|trim',
                    'numberofuser' => 'required|trim',
                ]);
            }

            if (!$this->validate($rules)) {
                $userdata = (object) array_merge((array) $userdata, $this->request->getPost());
                $data['errmsg'] = implode(', ', $this->validator->getErrors());
            } else {
                // Prepare data for saving
                $savedata = $this->request->getPost();
                $savedata['updateby'] = $this->sessdata['user_id'];
                $savedata['updatetime'] = date('Y-m-d H:i:s');

                if ($this->usersModel->editusers($savedata, $usermasterid)) {
                    // Handle device data
                    $device_data = $this->deviceModel->getDeviceSerialNumbers($usermasterid);
                    foreach ($device_data as $device) {
                        sleep(1);
                        $result['MSG'] = $device->serial_no;
                        $result['freefallalert'] = $savedata['freefallalert'];
                        $message = json_encode($result);
                        send_socket($message);
                        
                        sleep(1);
                        $result['networklocation'] = $savedata['networklocation'];
                        $message = json_encode($result);
                        send_socket($message);
                    }
                    $this->session->setFlashdata('msg', "Account updated successfully");
                    return redirect()->to('users/lists');
                } else {
                    $data['errmsg'] = "Failed to update account";
                }
            }
        }

        
        // Load groups and roles based on user group
        $data['groupdd'] = $this->getGroupDropdownData();
        $cond = " AND active = ?";
        $condArray = [1];
        if ($this->sessdata['group_id'] == 1) {
            $data['roledd'] = $this->usersModel->getroledd($cond . " AND id != ?", [1, 2]);
        } else {
            $data['roledd'] = $this->usersModel->getroledd($cond, $condArray);
        }
        
        $data['userdata'] = $userdata;
        $data['sessdata'] = $this->sessdata;
        $data['middle'] = view('users/edit', $data);
        return view('mainlayout', $data);
    }

    public function accountDetails($usermasterid)
    {
        $data = [];
        
        $data['page_title'] = "Account Details";
        $data['userid'] = $usermasterid;

        // Decode the user ID
        $usermasterid = base64_decode(urldecode($usermasterid));

        // Retrieve user data using the model
        $query = $this->db->query("
            SELECT * FROM public.useraccountsetup 
            WHERE id = (
                SELECT accountid 
                FROM public.user_login 
                WHERE user_id = ?
            )", [$usermasterid]);

        $data['userdata'] = $query->getRow();


        // Set session data if needed (example shown; adjust based on actual usage)
        $data['sessdata'] = $this->sessdata;
        

        // Load the view with the data
        $data['middle'] = view('users/accountdetails', $data);
        return view('mainlayout', $data);
    }

    public function statusChange($userid, $newstatusval, $oldstatusval)
    {
        // echo 1;exit();
        $schemaname = $this->schema;
        $userid = base64_decode(urldecode($userid));
        // $newstatusval = $this->request->uri->getSegment(4);
        // $oldstatusval = $this->request->uri->getSegment(5);
        
        $response = [];

        if ($this->sessdata['parent_group_id'] == 3) {
            $userCount = $this->usersModel->select('COUNT(*) as count')
                ->where('parent_id', $this->sessdata['user_id'])
                ->where('active', 1)
                ->first();

            if ($newstatusval == 1) {
                // Active
                if ($userCount->count < $this->sessdata['numberofuser']) {
                    $this->db->query("SELECT public.set_enabled_and_disable_account(?, ?, ?)", [$userid, $oldstatusval, $newstatusval]);
                    $response = ["suc" => 1, "msg" => "Status Changed Successfully..."];
                } else {
                    $response = ["suc" => 0, "msg" => "Maximum number of users already activated..."];
                }
            } else {
                // Inactive
                $this->db->query("SELECT public.set_enabled_and_disable_account(?, ?, ?)", [$userid, $oldstatusval, $newstatusval]);
                $response = ["suc" => 1, "msg" => "Status Changed Successfully..."];
            }
        } else {
            if ($newstatusval == 1) {
                $this->db->transStart();
                $this->db->query("UPDATE public.user_login SET active = 1, status = 1 WHERE user_id = ?", [$userid]);
                $this->db->query("UPDATE public.master_device_assign SET active = 1 WHERE user_id = ?", [$userid]);
                $this->db->transComplete();

                if ($this->db->transStatus() !== FALSE) {
                    $response = ["suc" => 1, "msg" => "Status Changed Successfully..."];
                } else {
                    $response = ["suc" => 0, "msg" => "Error in Status Change..."];
                }
            } else {
                $this->db->query("SELECT public.set_enabled_and_disable_account(?, ?, ?)", [$userid, $oldstatusval, $newstatusval]);
                $response = ["suc" => 1, "msg" => "Status Changed Successfully..."];
            }
        }

        return $this->response->setJSON($response); // Return JSON response
    }
}
