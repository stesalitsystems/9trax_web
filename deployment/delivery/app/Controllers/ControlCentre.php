<?php
namespace App\Controllers;

use App\Models\CommonModel;
use App\Models\ControlCentreModel;
use App\Models\DeviceModel;
use CodeIgniter\Controller;

class ControlCentre extends Controller
{
    protected $sessdata;
    protected $schema;
    protected $device_arr;

    public function __construct()
    {
        // Check if session is set
        if (session()->has('login_sess_data')) {
            $this->sessdata = session()->get('login_sess_data');
            $this->schema = $this->sessdata['schemaname'];
            $this->device_arr = [
                '9T201900996',
                '9T201800617',
                '9T201902007',
                '869867030416194',
                '9T201901967',
                '869867030417218',
                '869867032248934',
                '869867030444477',
                '869867032255806',
                '869867030411542',
                '869867030417523'
            ];
        }

        $this->db = \Config\Database::connect();

        // Load models
        $this->commonModel = new CommonModel();
        $this->controlCentreModel = new ControlCentreModel();
        $this->deviceModel = new DeviceModel();
        $this->uri = service('uri');
        
        // Load helpers (no longer needs to be loaded in the constructor in CI4)
        helper(['master', 'gis']);
    }

    public function view() {
        // Redirect based on group_id
        if ($this->sessdata['group_id'] == 1) {
            return redirect('controlcentre/adminview');
        }

        $data = [];
        $data['sessdata'] = $this->sessdata;
        $data['page_title'] = "Realtime Tracking";

        // Get URI segments
        $data['get_dev_id'] = ($this->uri->getTotalSegments() >= 3) ? $this->uri->getSegment(3) : '';
        
        $data['get_date'] = ($this->uri->getTotalSegments() >= 4) ? $this->uri->getSegment(4) : '';
        $data['get_frm_time'] = ($this->uri->getTotalSegments() >= 5) ? $this->uri->getSegment(5) : '';
        $data['get_to_time'] = ($this->uri->getTotalSegments() >= 6) ? $this->uri->getSegment(6) : '';
        // $data['get_date'] = isset(!empty($this->request->getUri()->getSegment(4))) ? $this->request->getUri()->getSegment(4) : '';
        // $data['get_frm_time'] = isset($this->request->getUri()->getSegment(5)) ? $this->request->getUri()->getSegment(5) : '';
        // $data['get_to_time'] = isset($this->request->getUri()->getSegment(6)) ? $this->request->getUri()->getSegment(6) : '';

        // Fetch data from the model
        $data['alertdd'] = $this->commonModel->getRows("public.master_alart", "id, description", [], ["active" => 1]);
        $data['groupdropdown'] = $this->commonModel->getRows("public.user_group", "name_e, id", [], ["active" => 1]);
        // $data['alertdropdown'] = $this->commonModel->getRows("public.master_alart", "description, id", [], ["active" => 1, "id IN" => [2, 3]]);
        $data['alertdropdown'] = $this->commonModel->getRows(
            "public.master_alart", 
            "description, id", 
            [], 
            [
                "active" => 1,
                "id IN" => [2, 3] // This part needs to be modified
            ]
        );

        $userId = $this->sessdata['user_id'];
        // Get device dropdown data based on group_id
        $deviceQuery = "
            SELECT a.*, 
            (SELECT device_name 
             FROM {$this->schema}.master_device_setup 
             WHERE id = (SELECT MAX(id) 
                         FROM {$this->schema}.master_device_setup 
                         WHERE inserttime::date <= current_date::date 
                         AND deviceid = a.did)) AS device_name 
            FROM public.get_divice_details_record_for_list_for_company('{$this->schema}', $userId) AS a 
            WHERE a.group_id = 2 AND a.active = 1
        ";

        // Execute query based on group_id
        if ($this->sessdata['group_id'] == 3) { // distributor
            $data['devicedropdown'] = $this->db->query($deviceQuery)->getResult();
        } else { // others
            $data['devicedropdown'] = $this->db->query($deviceQuery)->getResult();
        }

        // Load views
        return view('mainlayout', ['middle' => view('controlcentre/view', $data)]);
    }

    public function adminview()
    {
        $data = [];
        $data['sessdata'] = $this->sessdata;
        $data['page_title'] = "Realtime Tracking";
        $data['alertdd'] = [];
        $data['groupdropdown'] = [];
        $data['alertdropdown'] = [];

        // Fetch data based on group_id
        if ($this->sessdata['group_id'] == 4) { // Company
            // Example query for company
            //$data['devicedropdown'] = $this->db->query("SELECT a.*, b.device_name FROM public.get_device_details_record_for_list('{$this->schema}', {$this->sessdata->user_id}) as a LEFT JOIN {$this->schema}.master_device_setup as b ON a.did = b.deviceid WHERE a.group_id = 2")->getResult();
        } else { // Others
            // Example query for others
            //$data['devicedropdown'] = $this->db->query("SELECT a.*, b.device_name FROM public.get_device_details_record_for_list_for_company('{$this->schema}', {$this->sessdata->user_id}) as a LEFT JOIN {$this->schema}.master_device_setup as b ON a.did = b.deviceid WHERE a.group_id = 2")->getResult();
        }

        // Load the view
        return view('mainlayout', ['middle' => view('controlcentre/adminview', $data)]);
    }

    public function getNotificationAndSosData()
    {
        // Accessing session data using CodeIgniter 4's session handling
        $user_groupid = $this->sessdata['group_id'];
        $user_id = $this->sessdata['user_id'];
        $schemaname = $this->schema;

        $final_data = [
            'sos' => [],
            'calls' => [],
            'alerts' => []
        ];

        $get_all_devices = $this->controlCentreModel->getNotificationAndSosData($user_groupid, $user_id, $schemaname);
        
        if (!empty($get_all_devices)) {
            $final_data['sos'] = !empty($get_all_devices['sos']) ? $get_all_devices['sos'] : [];
            $final_data['calls'] = !empty($get_all_devices['calls']) ? $get_all_devices['calls'] : [];
            $final_data['alerts'] = !empty($get_all_devices['alerts']) ? $get_all_devices['alerts'] : [];
        }

        return $this->response->setJSON(["status" => 1, "result" => $final_data]);
    }

    public function getDevicePositionData()
    {
        $finalData = [];

        // Check if POST data is received
        if ($this->request->getPost('allselecteddevices')) {
            $currentDate = date("Y-m-d");

            foreach ($this->request->getPost('allselecteddevices') as $deviceId) {
                try {
                    $devicePosition = $this->controlCentreModel->getDevicePosition($deviceId, $currentDate, 1);
                    if (!empty($devicePosition)) {
                        $finalData[] = (array)$devicePosition;
                    }
                } catch (\Exception $e) {
                    log_message('error', 'Error fetching device position: ' . $e->getMessage());
                }    
            }
        }

        return $this->response->setJSON(['status' => 1, 'result' => $finalData]);
    }

    public function getMyRightMenu()
    {
        $groupid = $this->sessdata['group_id'];
        $parentid = $this->sessdata['user_id'];
        $result = '';
        $activedevice = [];
        $inactivedevice = [];
        $current_date = date("Y-m-d");

        $result_arr = $this->controlCentreModel->getRightPanelWeb($this->schema, $current_date, $groupid, $parentid);
       
        $create_menu = [];
        $create_menu1 = [];
        if (!empty($result_arr)) {
            if ($groupid == 3) {
                foreach ($result_arr as $value) {
                    if (!empty($value->deptuser)) {
                        $create_menu[$value->deptuser][$value->subdeptuser][$value->user_id][] = $value;
                        $create_menu[$value->deptuser]['deptuser'] = $value->deptuser;
                        $create_menu[$value->deptuser]['deptorganisation'] = $value->deptorganisation;
                        $create_menu[$value->deptuser][$value->subdeptuser]['subdeptuser'] = $value->subdeptuser;
                        $create_menu[$value->deptuser][$value->subdeptuser]['subdeptorganisation'] = $value->subdeptorganisation;
                    } else {
                        $create_menu1[$value->user_id][] = $value;
                    }
                }

                if (count($create_menu) > 0) {
                    foreach ($create_menu as $deptkey => $deptvalue) {
                        $result .= '<div class="panel-heading">
                                    <h4 class="panel-title">								
                                    <a data-toggle="collapse" data-parent="#level1-accordion" href="#plast-pro-' . $deptkey . '" class="collapsed">
                                        <span class="accordian-arrow">
                                            <i class="fa fa-minus-square-o" aria-hidden="true"></i>
                                            <i class="fa fa-plus-square-o" aria-hidden="true"></i>
                                        </span>' . $deptvalue['deptorganisation'] . '</a>
                                    </h4>
                                    </div>';
                        $result .= '<div id="plast-pro-' . $deptkey . '" class="panel-collapse collapse">';

                        foreach ($deptvalue as $subdeptkey => $subdeptvalue) {
                            if ($subdeptkey != 'deptorganisation' && $subdeptkey != 'deptuser') {
                                $result .= '<div class="panel-heading">
                                            <h4 class="panel-title">								
                                            <a data-toggle="collapse" href="#plast-pro-' . $subdeptkey . '" class="collapsed">
                                                <span class="accordian-arrow">
                                                    <i class="fa fa-minus-square-o" aria-hidden="true"></i>
                                                    <i class="fa fa-plus-square-o" aria-hidden="true"></i>
                                                </span>' . $subdeptvalue['subdeptorganisation'] . '</a>
                                            </h4>
                                            </div>';
                                $result .= '<div id="plast-pro-' . $subdeptkey . '" class="panel-collapse collapse">';

                                foreach ($subdeptvalue as $userkey => $uservalue) {
                                    if ($userkey != 'subdeptorganisation' && $userkey != 'subdeptuser') {
                                        $result .= '<div class="panel-heading">
                                                    <h4 class="panel-title">								
                                                    <a data-toggle="collapse" href="#plast-pro-' . $userkey . '" class="collapsed">
                                                        <span class="accordian-arrow">
                                                            <i class="fa fa-minus-square-o" aria-hidden="true"></i>
                                                            <i class="fa fa-plus-square-o" aria-hidden="true"></i>
                                                        </span>' . $uservalue[0]->organisation . '</a>
                                                    </h4>
                                                    </div>';								

                                        $result .= '<div id="plast-pro-' . $userkey . '" class="panel-collapse collapse">
                                                    <input type="text" id="myInputDeviceSearch' . $userkey . '" class="myInput" onkeyup="rightpaneldevicesearch(' . $userkey . ')" placeholder="Search device..">
                                                    <input type="checkbox" id="myInputDeviceAll'.$subdeptkey.'" class="myInput" onclick="addsudeptdevice('.$subdeptkey.')" style="width:auto">&nbsp;ALL
                                                    <div class="panel-body">				
                                                    <ul class="list-unstyled" id="myUL' . $userkey . '">';
                                        
                                        foreach ($uservalue as $v) {
                                            $dname_arr = explode("$", $v->device_name);
                                            $aliasname = 'N/A';
                                            $device_name = 'N/A';

                                            if(count($dname_arr)>1){
                                                $device_name = $dname_arr[0];
                                                $silentcallflag = $dname_arr[1];
                                            }
                                            $aliasname = (empty($device_name)) ? "" : "(" . $device_name . ")";
                                            
                                            if (!empty($v->status_color)) {
                                                array_push($activedevice, $v->deviceid);
                                                $result .= '<li>
                                                            <div class="checkbox">
                                                            <div class="dropdown" style="height:0px;">
                                                            <a id="dLabel" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false" style="padding: 3px;">
                                                            <img src="' . base_url() . '/assets/images/vm.png" alt="menu" class="devicemenudd"/>
                                                            </a>
                                                            <ul class="dropdown-menu deviceconfigmenu" aria-labelledby="dLabel">
                                                            <li onclick="playbackdevice(' . $v->deviceid . ')"><span>Live Tracking / History Playback</span></li>
                                                            <li data-toggle="modal" dat_link="' . site_url('devices/deviceconfiguration_ajax/' . urlencode(base64_encode($v->deviceid))) . '" class="mdlBtn"><span>Configuration</span></li>
                                                            
                                                            <li onclick="trackondevice(' . $v->deviceid . ')" id="trackon' . $v->deviceid . '" style="display:none;"><span>Trail On</span></li>
                                                            <li onclick="trackoffdevice(' . $v->deviceid . ')" id="trackoff' . $v->deviceid . '" style="display:none;"><span>Trail Off</span></li>
                                                            <li onclick="zoomtodevice(' . $v->deviceid . ')" id="zoomto' . $v->deviceid . '" style="display:none;"><span>Zoom To</span></li>
                                                            <li onclick="alertmanagement(' . $v->deviceid . ')"><span>Alert</span></li>
                                                            <li onclick="currentlocation(' . $v->deviceid . ')" style="display:none;"><span>Current Location</span></li>
                                                            <li onclick="deviceswitchoff(' . $v->deviceid . ')" style="display:none;"><span>Switch Off</span></li>';
                                                $result .= '</ul>
                                                </div><label style="width: 98%;margin-left: 10px;position:relative;">';
                                                // <li onclick="followdevice(' . $v->deviceid . ')"><span>Follow</span></li>
                                                if ($v->status_color == 'G') {
                                                $result .= '<img src="' . base_url() . '/assets/images/online.png" alt="online" class="user-online-offline"/>';
                                                }
                                                else{
                                                $result .= '<img src="' . base_url() . '/assets/images/mute.png" alt="online" class="user-online-offline"/>';	
                                                }
                                                $result .= '<input type="checkbox" class="clickmetotrack" id="' . $v->deviceid . '" style="margin-right: 5px;"><a href="javascript:void(0);" style="padding:0;">' . $v->serial_no . ' ' . $aliasname . '</a></label>
                                                </div>		
                                                </li>';
                                            } else {
                                                array_push($inactivedevice, $v->deviceid);
                                                $result .= '<li>
                                                            <div class="checkbox">
                                                            <div class="dropdown" style="height:0px;">
                                                            <a id="dLabel" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false" style="padding: 3px;">
                                                            <img src="' . base_url() . '/assets/images/vm.png" alt="menu" class="devicemenudd"/>
                                                            </a>
                                                            <ul class="dropdown-menu deviceconfigmenu" aria-labelledby="dLabel">
                                                            <li onclick="playbackdevice(' . $v->deviceid . ')"><span>Live Tracking / History Playback</span></li>
                                                            <li data-toggle="modal" dat_link="' . site_url('devices/deviceconfiguration_ajax/' . urlencode(base64_encode($v->deviceid))) . '" class="mdlBtn"><span>Configuration</span></li>
                                                            <li onclick="alertmanagement(' . $v->deviceid . ')"><span>Alert</span></li>
                                                            </ul>
                                                            </div>
                                                            <label style="width:98%;margin-left: 10px;position:relative;">
                                                            <img src="' . base_url() . '/assets/images/offline.png" alt="offline" class="user-online-offline" />
                                                            <input type="checkbox" style="margin-right: 5px;" disabled><a href="javascript:void(0);" style="padding:0;">' . $v->serial_no . ' ' . $aliasname . '</a></label>
                                                            </div></li>';
                                            }
                                        }
                                        $result .= '</ul>
                                                    </div>
                                                    </div>';
                                    }
                                }
                                $result .= '</div>';
                            }
                        }
                        $result .= '</div>';
                    }
                }
                
                if (count($create_menu1) > 0) {
                    foreach ($create_menu1 as $key => $value) {
                        $result .= '<div class="panel-heading">
                                   <h4 class="panel-title">								
                                    <a data-toggle="collapse" data-parent="#level1-accordion" href="#plast-pro-' . $key . '" class="collapsed">
                                    <span class="accordian-arrow">
                                    <i class="fa fa-minus-square-o" aria-hidden="true"></i>
                                    <i class="fa fa-plus-square-o" aria-hidden="true"></i>
                                    </span>' . $value[0]->organisation . '</a>
                                    </h4>
                                    </div>';
                        $result .= '<div id="plast-pro-' . $key . '" class="panel-collapse collapse">
                                   <input type="text" id="myInputDeviceSearch' . $key . '" class="myInput" onkeyup="rightpaneldevicesearch(' . $key . ')" placeholder="Search device..">
                                   <div class="panel-body">
                                   <ul class="list-unstyled" id="myUL' . $key . '">';
                        foreach ($value as $v) {
                            $dname_arr = explode("$", $v->device_name);
                            $aliasname = 'N/A';
                            $device_name = 'N/A';

                            if(count($dname_arr)>1){
                                $device_name = $dname_arr[0];
                                $silentcallflag = $dname_arr[1];
                            }
                            $aliasname = (empty($device_name)) ? "" : "(" . $device_name . ")";
                            if (!empty($v->status_color)) {
                                array_push($activedevice, $v->deviceid);
                                $result .= '<li>
                                            <div class="checkbox">
                                            <div class="dropdown" style="height:0px;">
                                            <a id="dLabel" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false" style="padding: 3px;">
                                            <img src="' . base_url() . '/assets/images/vm.png" alt="menu" class="devicemenudd"/>
                                            </a>
                                            <ul class="dropdown-menu deviceconfigmenu" aria-labelledby="dLabel">
                                            <li onclick="playbackdevice(' . $v->deviceid . ')"><span>Live Tracking / History Playback</span></li>
                                            <li data-toggle="modal" dat_link="' . site_url('devices/deviceconfiguration_ajax/' . urlencode(base64_encode($v->deviceid))) . '" class="mdlBtn"><span>Configuration</span></li>
                                            
                                            <li onclick="trackondevice(' . $v->deviceid . ')" id="trackon' . $v->deviceid . '" style="display:none;"><span>Trail On</span></li>
                                            <li onclick="trackoffdevice(' . $v->deviceid . ')" id="trackoff' . $v->deviceid . '" style="display:none;"><span>Trail Off</span></li>
                                            <li onclick="zoomtodevice(' . $v->deviceid . ')" id="zoomto' . $v->deviceid . '" style="display:none;"><span>Zoom To</span></li>
                                            <li onclick="alertmanagement(' . $v->deviceid . ')"><span>Alert</span></li>
                                            <li onclick="currentlocation(' . $v->deviceid . ')" style="display:none;"><span>Current Location</span></li>
                                            <li onclick="deviceswitchoff(' . $v->deviceid . ')" style="display:none;"><span>Switch Off</span></li>';
                                $result .= '</ul>
                                    </div><label style="width: 98%;margin-left: 10px;position:relative;">';
                                    // <li onclick="followdevice(' . $v->deviceid . ')"><span>Follow</span></li>
                                    if ($v->status_color == 'G') {
                                    $result .= '<img src="' . base_url() . '/assets/images/online.png" alt="online" class="user-online-offline"/>';
                                    }
                                    else{
                                    $result .= '<img src="' . base_url() . '/assets/images/mute.png" alt="online" class="user-online-offline"/>';	
                                    }
                                    $result .= '<input type="checkbox" class="clickmetotrack" id="' . $v->deviceid . '" style="margin-right: 5px;"><a href="javascript:void(0);" style="padding:0;">' . $v->serial_no . ' ' . $aliasname . '</a></label>
                                        </div>		
                                    </li>';
                            } else {
                                array_push($inactivedevice, $v->deviceid);
                                $result .= '<li>
                                            <div class="checkbox">
                                            <div class="dropdown" style="height:0px;">
                                            <a id="dLabel" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false" style="padding: 3px;">
                                            <img src="' . base_url() . '/assets/images/vm.png" alt="menu" class="devicemenudd"/>
                                            </a>
                                            <ul class="dropdown-menu deviceconfigmenu" aria-labelledby="dLabel">
                                            <li data-toggle="modal" dat_link="' . site_url('devices/deviceconfiguration_ajax/' . urlencode(base64_encode($v->deviceid))) . '" class="mdlBtn"><span>Configuration</span></li>
                                            <li onclick="alertmanagement(' . $v->deviceid . ')"><span>Alert</span></li>
                                            </ul>
                                            </div>
                                            <label style="width:98%;margin-left: 10px;position:relative;">
                                            <img src="' . base_url() . '/assets/images/offline.png" alt="offline" class="user-online-offline" />
                                            <input type="checkbox" style="margin-right: 5px;" disabled><a href="javascript:void(0);" style="padding:0;">' . $v->serial_no . ' ' . $aliasname . '</a></label>
                                            </div></li>';
                            }
                        }
                        $result .= '</ul>
                                   </div>
                                   </div>';
                    }
                }
            } else if ($groupid == 4) {
                foreach ($result_arr as $value) {
                    if (!empty($value->deptuser)) {
                        $create_menu[$value->deptuser][$value->subdeptuser][$value->user_id][] = $value;
                        $create_menu[$value->deptuser]['deptuser'] = $value->deptuser;
                        $create_menu[$value->deptuser]['deptorganisation'] = $value->deptorganisation;
                        $create_menu[$value->deptuser][$value->subdeptuser]['subdeptuser'] = $value->subdeptuser;
                        $create_menu[$value->deptuser][$value->subdeptuser]['subdeptorganisation'] = $value->subdeptorganisation;
                    } else {
                        $create_menu1[$value->user_id][] = $value;
                    }
                }

                if (count($create_menu) > 0) {
                    foreach ($create_menu as $deptkey => $deptvalue) {
                        $result .= '<div class="panel-heading">
                                        <h4 class="panel-title">
                                            <a data-toggle="collapse" data-parent="#level1-accordion" href="#plast-pro-' . $deptkey . '" class="collapsed">
                                                <span class="accordian-arrow">
                                                    <i class="fa fa-minus-square-o" aria-hidden="true"></i>
                                                    <i class="fa fa-plus-square-o" aria-hidden="true"></i>
                                                </span>' . $deptvalue['deptorganisation'] . '</a>
                                        </h4>
                                    </div>';
                        $result .= '<div id="plast-pro-' . $deptkey . '" class="panel-collapse collapse">';

                        foreach ($deptvalue as $subdeptkey => $subdeptvalue) {
                            if ($subdeptkey != 'deptorganisation' && $subdeptkey != 'deptuser') {
                                $result .= '<div class="panel-heading">
                                                <h4 class="panel-title">
                                                    <a data-toggle="collapse" href="#plast-pro-' . $subdeptkey . '" class="collapsed">
                                                        <span class="accordian-arrow">
                                                            <i class="fa fa-minus-square-o" aria-hidden="true"></i>
                                                            <i class="fa fa-plus-square-o" aria-hidden="true"></i>
                                                        </span>' . $subdeptvalue['subdeptorganisation'] . '</a>
                                                </h4>
                                            </div>';
                                $result .= '<div id="plast-pro-' . $subdeptkey . '" class="panel-collapse collapse">';

                                foreach ($subdeptvalue as $userkey => $uservalue) {
                                    if ($userkey != 'subdeptorganisation' && $userkey != 'subdeptuser') {
                                        $result .= '<div class="panel-heading">
                                                        <h4 class="panel-title">
                                                            <a data-toggle="collapse" href="#plast-pro-' . $userkey . '" class="collapsed">
                                                                <span class="accordian-arrow">
                                                                    <i class="fa fa-minus-square-o" aria-hidden="true"></i>
                                                                    <i class="fa fa-plus-square-o" aria-hidden="true"></i>
                                                                </span>' . $uservalue[0]->organisation . '</a>
                                                        </h4>
                                                    </div>';
                                        $result .= '<div id="plast-pro-' . $userkey . '" class="panel-collapse collapse">
                                                    <input type="text" id="myInputDeviceSearch' . $userkey . '" class="myInput" onkeyup="rightpaneldevicesearch(' . $userkey . ')" placeholder="Search device..">
                                                    <div class="panel-body">
                                                    <ul class="list-unstyled" id="myUL' . $userkey . '">';

                                        foreach ($uservalue as $k => $v) {
                                            $dname_arr = explode("$", $v->device_name);
                                            $aliasname = 'N/A';
                                            $device_name = 'N/A';
                                            if(count($dname_arr) > 1) {
                                                $device_name = $dname_arr[0];
                                                $silentcallflag = $dname_arr[1];
                                                
                                            }
                                            $aliasname = (empty($device_name)) ? "" : "(" . $device_name . ")";
                                            
                                            if (!empty($v->status_color)) {
                                                array_push($activedevice, $v->deviceid);
                                                $result .= '<li>
                                                                <div class="checkbox">
                                                                    <div class="dropdown" style="height:0px;">
                                                                        <a id="dLabel" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false" style="padding: 3px;">
                                                                            <img src="' . base_url() . '/assets/images/vm.png" alt="menu" class="devicemenudd"/>
                                                                        </a>
                                                                        <ul class="dropdown-menu deviceconfigmenu" aria-labelledby="dLabel">
                                                                            <li data-toggle="modal" dat_link="' . site_url('/') . 'devices/deviceconfiguration_ajax/' . urlencode(base64_encode($v->deviceid)) . '" class="mdlBtn"><span>Configuration</span></li>
                                                                            
                                                                            <li onclick="trackondevice(' . $v->deviceid . ')" id="trackon' . $v->deviceid . '" style="display:none;"><span>Trail On</span></li>
                                                                            <li onclick="trackoffdevice(' . $v->deviceid . ')" id="trackoff' . $v->deviceid . '" style="display:none;"><span>Trail Off</span></li>
                                                                            <li onclick="zoomtodevice(' . $v->deviceid . ')" id="zoomto' . $v->deviceid . '" style="display:none;"><span>Zoom To</span></li>
                                                                            <li onclick="alertmanagement(' . $v->deviceid . ')"><span>Alert</span></li>
                                                                        </ul>
                                                                    </div>
                                                                    <label style="width: 98%;margin-left: 10px;position:relative;">';
                                                                    // <li onclick="followdevice(' . $v->deviceid . ')"><span>Follow</span></li>
                                                $result .= '<img src="' . base_url() . '/assets/images/online.png" alt="online" class="user-online-offline"/>';
                                                $result .= '<input type="checkbox" class="clickmetotrack" id="' . $v->deviceid . '" style="margin-right: 5px;"><a href="javascript:void(0);" style="padding:0;">' . $v->serial_no . ' ' . $aliasname . '</a></label>
                                                                </div>
                                                            </li>';
                                            } else {
                                                array_push($inactivedevice, $v->deviceid);
                                                $result .= '<li>
                                                                <div class="checkbox">
                                                                    <div class="dropdown" style="height:0px;">
                                                                        <a id="dLabel" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false" style="padding: 3px;">
                                                                            <img src="' . base_url() . '/assets/images/vm.png" alt="menu" class="devicemenudd"/>
                                                                        </a>
                                                                        <ul class="dropdown-menu deviceconfigmenu" aria-labelledby="dLabel">
                                                                            <li data-toggle="modal" dat_link="' . site_url('/') . 'devices/deviceconfiguration_ajax/' . urlencode(base64_encode($v->deviceid)) . '" class="mdlBtn"><span>Configuration</span></li>
                                                                            <li onclick="alertmanagement(' . $v->deviceid . ')"><span>Alert</span></li>
                                                                        </ul>
                                                                    </div>
                                                                    <label style="width:98%;margin-left: 10px;position:relative;">
                                                                        <img src="' . base_url() . '/assets/images/offline.png" alt="offline" class="user-online-offline" />
                                                                        <input type="checkbox" style="margin-right: 5px;" disabled><a href="javascript:void(0);" style="padding:0;">' . $v->serial_no . ' ' . $aliasname . '</a></label>
                                                                </div>
                                                            </li>';
                                            }
                                        }
                                        $result .= '</ul></div></div>';
                                    }
                                }
                                $result .= '</div>';
                            }
                        }
                        $result .= '</div>';
                    }
                }

                if (count($create_menu1) > 0) {
                    foreach ($create_menu1 as $key => $value) {
                        $result .= '<div class="panel-heading">
                                        <h4 class="panel-title">
                                            <a data-toggle="collapse" data-parent="#level1-accordion" href="#plast-pro-' . $key . '" class="collapsed">
                                                <span class="accordian-arrow">
                                                    <i class="fa fa-minus-square-o" aria-hidden="true"></i>
                                                    <i class="fa fa-plus-square-o" aria-hidden="true"></i>
                                                </span>' . $value[0]->organisation . '</a>
                                        </h4>
                                    </div>';
                        $result .= '<div id="plast-pro-' . $key . '" class="panel-collapse collapse">
                                        <input type="text" id="myInputDeviceSearch' . $key . '" class="myInput" onkeyup="rightpaneldevicesearch(' . $key . ')" placeholder="Search device..">
                                        <div class="panel-body">
                                            <ul class="list-unstyled" id="myUL' . $key . '">';

                        foreach ($value as $k => $v) {
                            $dname_arr = explode("$", $v->device_name);
                            $device_name = $dname_arr[0];
                            $silentcallflag = $dname_arr[1];
                            $aliasname = (empty($device_name)) ? "" : "(" . $device_name . ")";
                            if (!empty($v->status_color)) {
                                array_push($activedevice, $v->deviceid);
                                $result .= '<li>
                                                <div class="checkbox">
                                                    <div class="dropdown" style="height:0px;">
                                                        <a id="dLabel" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false" style="padding: 3px;">
                                                            <img src="' . base_url() . '/assets/images/vm.png" alt="menu" class="devicemenudd"/>
                                                        </a>
                                                        <ul class="dropdown-menu deviceconfigmenu" aria-labelledby="dLabel">
                                                            <li data-toggle="modal" dat_link="' . site_url('/') . 'devices/deviceconfiguration_ajax/' . urlencode(base64_encode($v->deviceid)) . '" class="mdlBtn"><span>Configuration</span></li>
                                                            
                                                            <li onclick="trackondevice(' . $v->deviceid . ')" id="trackon' . $v->deviceid . '" style="display:none;"><span>Trail On</span></li>
                                                            <li onclick="trackoffdevice(' . $v->deviceid . ')" id="trackoff' . $v->deviceid . '" style="display:none;"><span>Trail Off</span></li>
                                                            <li onclick="zoomtodevice(' . $v->deviceid . ')" id="zoomto' . $v->deviceid . '" style="display:none;"><span>Zoom To</span></li>
                                                            <li onclick="alertmanagement(' . $v->deviceid . ')"><span>Alert</span></li>
                                                        </ul>
                                                    </div>
                                                    <label style="width: 98%;margin-left: 10px;position:relative;">';
                                                    // <li onclick="followdevice(' . $v->deviceid . ')"><span>Follow</span></li>
                                $result .= '<img src="' . base_url() . '/assets/images/online.png" alt="online" class="user-online-offline"/>';
                                $result .= '<input type="checkbox" class="clickmetotrack" id="' . $v->deviceid . '" style="margin-right: 5px;"><a href="javascript:void(0);" style="padding:0;">' . $v->serial_no . ' ' . $aliasname . '</a></label>
                                                </div>
                                            </li>';
                            } else {
                                array_push($inactivedevice, $v->deviceid);
                                $result .= '<li>
                                                <div class="checkbox">
                                                    <div class="dropdown" style="height:0px;">
                                                        <a id="dLabel" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false" style="padding: 3px;">
                                                            <img src="' . base_url() . '/assets/images/vm.png" alt="menu" class="devicemenudd"/>
                                                        </a>
                                                        <ul class="dropdown-menu deviceconfigmenu" aria-labelledby="dLabel">
                                                            <li data-toggle="modal" dat_link="' . site_url('/') . 'devices/deviceconfiguration_ajax/' . urlencode(base64_encode($v->deviceid)) . '" class="mdlBtn"><span>Configuration</span></li>
                                                            <li onclick="alertmanagement(' . $v->deviceid . ')"><span>Alert</span></li>
                                                        </ul>
                                                    </div>
                                                    <label style="width:98%;margin-left: 10px;position:relative;">
                                                        <img src="' . base_url() . '/assets/images/offline.png" alt="offline" class="user-online-offline" />
                                                        <input type="checkbox" style="margin-right: 5px;" disabled><a href="javascript:void(0);" style="padding:0;">' . $v->serial_no . ' ' . $aliasname . '</a></label>
                                                </div>
                                            </li>';
                            }
                        }
                        $result .= '</ul></div></div>';
                    }
                }
            } else if ($groupid == 5) {
                foreach ($result_arr as $value) {
                    if (!empty($value->subdeptuser)) {
                        $create_menu[$value->subdeptuser][$value->user_id][] = $value;
                        $create_menu[$value->subdeptuser]['subdeptorganisation'] = $value->subdeptorganisation;
                    } else {
                        $create_menu1[$value->user_id][] = $value;
                    }
                }
    
                // Build the menu
                foreach ($create_menu as $subdeptkey => $subdeptvalue) {
                    $result .= '<div class="panel-heading">
                                    <h4 class="panel-title">
                                        <a data-toggle="collapse" href="#plast-pro-' . $subdeptkey . '" class="collapsed">
                                            <span class="accordian-arrow">
                                                <i class="fa fa-minus-square-o" aria-hidden="true"></i>
                                                <i class="fa fa-plus-square-o" aria-hidden="true"></i>
                                            </span>' . $subdeptvalue['subdeptorganisation'] . '
                                        </a>
                                    </h4>
                                </div>
                                <div id="plast-pro-' . $subdeptkey . '" class="panel-collapse collapse">';
    
                    foreach ($subdeptvalue as $userkey => $uservalue) {
                        if ($userkey != 'subdeptorganisation' && $userkey != 'subdeptuser') {
                            $result .= '<div class="panel-heading">
                                            <h4 class="panel-title">
                                                <a data-toggle="collapse" href="#plast-pro-' . $userkey . '" class="collapsed">
                                                    <span class="accordian-arrow">
                                                        <i class="fa fa-minus-square-o" aria-hidden="true"></i>
                                                        <i class="fa fa-plus-square-o" aria-hidden="true"></i>
                                                    </span>' . $uservalue[0]->organisation . '
                                                </a>
                                            </h4>
                                        </div>
                                        <div id="plast-pro-' . $userkey . '" class="panel-collapse collapse">
                                            <input type="text" id="myInputDeviceSearch' . $userkey . '" class="myInput" onkeyup="rightpaneldevicesearch(' . $userkey . ')" placeholder="Search device..">
                                            <div class="panel-body">
                                                <ul class="list-unstyled" id="myUL' . $userkey . '">';
    
                            foreach ($uservalue as $v) {
                                $dname_arr = explode("$", $v->device_name);
                                $aliasname = 'N/A';
                                $device_name = 'N/A';
                                if(count($dname_arr) > 1) {
                                    $device_name = $dname_arr[0];
                                    $silentcallflag = $dname_arr[1];
                                }
                                
                                $aliasname = (empty($device_name)) ? "" : "(" . $device_name . ")";
    
                                if (!empty($v->status_color)) { // active
                                    array_push($activedevice, $v->deviceid);
                                    $result .= '<li>
                                                    <div class="checkbox">
                                                        <div class="dropdown" style="height:0px;">
                                                            <a id="dLabel" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false" style="padding: 3px;">
                                                                <img src="' . base_url() . '/assets/images/vm.png" alt="menu" class="devicemenudd"/>
                                                            </a>
                                                            <ul class="dropdown-menu deviceconfigmenu" aria-labelledby="dLabel">
                                                                <li data-toggle="modal" dat_link="' . site_url('devices/deviceconfiguration_ajax/' . urlencode(base64_encode($v->deviceid))) . '" class="mdlBtn"><span>Configuration</span></li>
                                                                
                                                                <li onclick="alertmanagement(' . $v->deviceid . ')"><span>Alert</span></li>
                                                            </ul>
                                                        </div>
                                                        <label style="width: 98%;margin-left: 10px;position:relative;">';
                                                        // <li onclick="followdevice(' . $v->deviceid . ')"><span>Follow</span></li>
                                    $result .= ($v->status_color == 'G') ? '<img src="' . base_url() . '/assets/images/online.png" alt="online" class="user-online-offline"/>' : '<img src="' . base_url() . '/assets/images/mute.png" alt="offline" class="user-online-offline"/>';
                                    $result .= '<input type="checkbox" class="clickmetotrack" id="' . $v->deviceid . '" style="margin-right: 5px;"><a href="javascript:void(0);" style="padding:0;">' . $v->serial_no . ' ' . $aliasname . '</a></label>
                                                    </div>
                                                </li>';
                                } else { // offline
                                    array_push($inactivedevice, $v->deviceid);
                                    $result .= '<li>
                                                    <div class="checkbox">
                                                        <div class="dropdown" style="height:0px;">
                                                            <a id="dLabel" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false" style="padding: 3px;">
                                                                <img src="' . base_url() . '/assets/images/vm.png" alt="menu" class="devicemenudd"/>
                                                            </a>
                                                            <ul class="dropdown-menu deviceconfigmenu" aria-labelledby="dLabel">
                                                                <li data-toggle="modal" dat_link="' . site_url('devices/deviceconfiguration_ajax/' . urlencode(base64_encode($v->deviceid))) . '" class="mdlBtn"><span>Configuration</span></li>
                                                                <li onclick="alertmanagement(' . $v->deviceid . ')"><span>Alert</span></li>
                                                            </ul>
                                                        </div>
                                                        <label style="width:98%;margin-left: 10px;position:relative;">
                                                            <img src="' . base_url() . '/assets/images/offline.png" alt="offline" class="user-online-offline" />
                                                            <input type="checkbox" style="margin-right: 5px;" disabled><a href="javascript:void(0);" style="padding:0;">' . $v->serial_no . ' ' . $aliasname . '</a>
                                                        </label>
                                                    </div>
                                                </li>';
                                }
                            }
                            $result .= '</ul></div></div>';
                        }
                    }
                    $result .= '</div>'; // Close panel-collapse
                }
    
                // Build the menu for users without sub-departments
                foreach ($create_menu1 as $key => $value) {
                    $result .= '<div class="panel-heading">
                                    <h4 class="panel-title">
                                        <a data-toggle="collapse" data-parent="#level1-accordion" href="#plast-pro-' . $key . '" class="collapsed">
                                            <span class="accordian-arrow">
                                                <i class="fa fa-minus-square-o" aria-hidden="true"></i>
                                                <i class="fa fa-plus-square-o" aria-hidden="true"></i>
                                            </span>' . $value[0]->organisation . '
                                        </a>
                                    </h4>
                                </div>
                                <div id="plast-pro-' . $key . '" class="panel-collapse collapse">
                                    <input type="text" id="myInputDeviceSearch' . $key . '" class="myInput" onkeyup="rightpaneldevicesearch(' . $key . ')" placeholder="Search device..">
                                    <div class="panel-body">
                                        <ul class="list-unstyled" id="myUL' . $key . '">';
    
                    foreach ($value as $v) {
                        $dname_arr = explode("$", $v->device_name);
                        $device_name = $dname_arr[0];
                        $silentcallflag = $dname_arr[1];
                        $aliasname = (empty($device_name)) ? "" : "(" . $device_name . ")";
    
                        if (!empty($v->status_color)) { // active
                            array_push($activedevice, $v->deviceid);
                            $result .= '<li>
                                            <div class="checkbox">
                                                <div class="dropdown" style="height:0px;">
                                                    <a id="dLabel" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false" style="padding: 3px;">
                                                        <img src="' . base_url() . '/assets/images/vm.png" alt="menu" class="devicemenudd"/>
                                                    </a>
                                                    <ul class="dropdown-menu deviceconfigmenu" aria-labelledby="dLabel">
                                                        <li data-toggle="modal" dat_link="' . site_url('devices/deviceconfiguration_ajax/' . urlencode(base64_encode($v->deviceid))) . '" class="mdlBtn"><span>Configuration</span></li>
                                                        
                                                        <li onclick="alertmanagement(' . $v->deviceid . ')"><span>Alert</span></li>
                                                    </ul>
                                                </div>
                                                <label style="width: 98%;margin-left: 10px;position:relative;">';
                                                // <li onclick="followdevice(' . $v->deviceid . ')"><span>Follow</span></li>
                            $result .= ($v->status_color == 'G') ? '<img src="' . base_url() . '/assets/images/online.png" alt="online" class="user-online-offline"/>' : '<img src="' . base_url() . '/assets/images/mute.png" alt="offline" class="user-online-offline"/>';
                            $result .= '<input type="checkbox" class="clickmetotrack" id="' . $v->deviceid . '" style="margin-right: 5px;"><a href="javascript:void(0);" style="padding:0;">' . $v->serial_no . ' ' . $aliasname . '</a></label>
                                            </div>
                                        </li>';
                        } else { // offline
                            array_push($inactivedevice, $v->deviceid);
                            $result .= '<li>
                                            <div class="checkbox">
                                                <div class="dropdown" style="height:0px;">
                                                    <a id="dLabel" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false" style="padding: 3px;">
                                                        <img src="' . base_url() . '/assets/images/vm.png" alt="menu" class="devicemenudd"/>
                                                    </a>
                                                    <ul class="dropdown-menu deviceconfigmenu" aria-labelledby="dLabel">
                                                        <li data-toggle="modal" dat_link="' . site_url('devices/deviceconfiguration_ajax/' . urlencode(base64_encode($v->deviceid))) . '" class="mdlBtn"><span>Configuration</span></li>
                                                        <li onclick="alertmanagement(' . $v->deviceid . ')"><span>Alert</span></li>
                                                    </ul>
                                                </div>
                                                <label style="width:98%;margin-left: 10px;position:relative;">
                                                    <img src="' . base_url() . '/assets/images/offline.png" alt="offline" class="user-online-offline" />
                                                    <input type="checkbox" style="margin-right: 5px;" disabled><a href="javascript:void(0);" style="padding:0;">' . $v->serial_no . ' ' . $aliasname . '</a>
                                                </label>
                                            </div>
                                        </li>';
                        }
                    }
                    $result .= '</ul></div></div>'; // Close ul and divs
                }
            } else {
                foreach ($result_arr as $value) {
                    $create_menu[$value->user_id][] = $value;
                }
    
                foreach ($create_menu as $key => $value) {
                    $result .= '<div class="panel-heading">
                                    <h4 class="panel-title">                                
                                        <a data-toggle="collapse" data-parent="#level1-accordion" href="#plast-pro-' . $key . '" class="collapsed">
                                            <span class="accordian-arrow">
                                                <i class="fa fa-minus-square-o" aria-hidden="true"></i>
                                                <i class="fa fa-plus-square-o" aria-hidden="true"></i>
                                            </span>' . esc($value[0]->organisation) . '
                                        </a>
                                    </h4>
                                </div>';
                    $result .= '<div id="plast-pro-' . $key . '" class="panel-collapse collapse">
                                    <input type="text" id="myInputDeviceSearch' . $key . '" class="myInput" onkeyup="rightpaneldevicesearch(' . $key . ')" placeholder="Search device..">
                                    <div class="panel-body">                
                                        <ul class="list-unstyled" id="myUL' . $key . '">';
                                        
                    foreach ($value as $v) {
                        $dname_arr = explode("$", $v->device_name);
                        $aliasname = 'N/A';
                        $device_name = 'N/A';
                        if(count($dname_arr) > 1) {
                            $device_name = $dname_arr[0];
                            $silentcallflag = $dname_arr[1];
                        }
                        $aliasname = empty($device_name) ? "" : "(" . esc($device_name) . ")";
    
                        $dropdown = '<div class="dropdown" style="height:0px;">
                                        <a id="dLabel" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false" style="padding: 3px;">
                                            <img src="' . base_url('assets/images/vm.png') . '" alt="menu" class="devicemenudd"/>
                                        </a>
                                        <ul class="dropdown-menu deviceconfigmenu" aria-labelledby="dLabel">
                                            <li data-toggle="modal" dat_link="' . site_url('devices/deviceconfiguration_ajax/' . urlencode(base64_encode($v->deviceid))) . '" class="mdlBtn"><span>Configuration</span></li>
                                            <li onclick="alertmanagement(' . $v->deviceid . ')"><span>Alert</span></li>' .
                                            ($v->status_color ? '' : '') .
                                        '</ul>
                                    </div>';
                                    // <li onclick="followdevice(' . $v->deviceid . ')"><span>Follow</span></li>
    
                        if (!empty($v->status_color)) { // active                            
                            $activedevice[] = $v->deviceid;
                            $statusIcon = $v->status_color == 'G' ? 'online.png' : 'mute.png';
                            $result .= '<li>
                                            <div class="checkbox">' . $dropdown . '
                                                <label style="width: 98%;margin-left: 10px;position:relative;">
                                                    <img src="' . base_url('assets/images/' . $statusIcon) . '" alt="status" class="user-online-offline"/>
                                                    <input type="checkbox" class="clickmetotrack" id="' . $v->deviceid . '" style="margin-right: 5px;">
                                                    <a href="javascript:void(0);" style="padding:0;">' . esc($v->serial_no) . ' ' . $aliasname . '</a>
                                                </label>
                                            </div>		
                                        </li>';
                        } else { // offline
                            $inactivedevice[] = $v->deviceid;
                            $result .= '<li>
                                            <div class="checkbox">' . $dropdown . '
                                                <label style="width:98%;margin-left: 10px;position:relative;">
                                                    <img src="' . base_url('assets/images/offline.png') . '" alt="offline" class="user-online-offline" />
                                                    <input type="checkbox" style="margin-right: 5px;" disabled><a href="javascript:void(0);" style="padding:0;">' . esc($v->serial_no) . ' ' . $aliasname . '</a>
                                                </label>
                                            </div>
                                        </li>';
                        }
                    }
                    $result .= '</ul>
                                </div>
                            </div>';
                }
            }
        }
        return $this->response->setJSON(['status' => 1, 'result' => $result, 'activeDevices' => $activedevice, 'inactiveDevices' => $inactivedevice]);
    }

    public function onlivedutydevice()
    {
        // Check if the user is logged in
        if (!session()->has('login_sess_data')) {
            return redirect()->to('/');
        }

        $data = [];
        $data['sessdata'] = $this->sessdata; // Get session data
        $data['page_title'] = "On Live Duty Device";

        $current_date = date("Y-m-d");
        $schema = $this->schema; // Adjust according to your session structure
        $user_id = $this->sessdata['user_id'];
        $group_id = $this->sessdata['group_id'];

        if ($group_id == 3) {
            /*$query = $this->db->query("
                SELECT a.*, b.device_name
                FROM public.get_right_panel_data('{$schema}', '{$current_date}', {$user_id}) AS a
                LEFT JOIN {$this->schema}.master_device_setup AS b ON (a.deviceid = b.deviceid)
                WHERE a.group_id = 2 AND a.deviceid IS NOT NULL AND a.status_color IS NOT NULL
            ");*/
            $query = $this->db->query("
                SELECT a.*, b.device_name
                FROM public.get_right_panel_data('{$schema}', '{$current_date}', {$user_id}) AS a
                LEFT JOIN {$this->schema}.master_device_setup AS b ON (a.deviceid = b.deviceid)
                WHERE a.group_id = 2 AND a.deviceid IS NOT NULL AND a.status_color = 'G'
            ");
            $data_online = $query->getResult();
        } else {
            /*$query = $this->db->query("
                SELECT a.*, b.device_name
                FROM public.get_right_panel_data('{$schema}', '{$current_date}', {$user_id}) AS a
                LEFT JOIN {$this->schema}.master_device_setup AS b ON (a.deviceid = b.deviceid)
                WHERE a.group_id = 2 AND a.deviceid IS NOT NULL AND a.status_color IS NOT NULL
            ");*/
            $query = $this->db->query("
                SELECT a.*, b.device_name
                FROM public.get_right_panel_data('{$schema}', '{$current_date}', {$user_id}) AS a
                LEFT JOIN {$this->schema}.master_device_setup AS b ON (a.deviceid = b.deviceid)
                WHERE a.group_id = 2 AND a.deviceid IS NOT NULL AND a.status_color = 'G'
            ");
            $data_online = $query->getResult();
        }
    
        // Count devices that don't contain 'stock' in the device name
        $counter = 0;
        foreach ($data_online as $device) {
            if (strpos(strtolower($device->device_name), 'stock') === false) {
                $counter++;
            }
        }
        $data['online'] = $counter;

        // Load the view
        $data['middle'] =  view('controlcentre/onlivedutydevice', $data);

        // Load the main layout view
        return view('mainlayout', $data);
    }

    public function ondutydevice()
    {
        // Check if the user is logged in
        if (!session()->has('login_sess_data')) {
            return redirect()->to('/');
        }

        $data = [];
        $data['sessdata'] = $this->sessdata; // Get session data
        $data['page_title'] = "On Duty Device";

        $current_date = date("Y-m-d");
        $schema = $this->schema; // Adjust according to your session structure
        $user_id = $this->sessdata['user_id'];
        $group_id = $this->sessdata['group_id'];

        if ($group_id == 3) {
            $query = $this->db->query("
                SELECT a.*, b.device_name
                FROM public.get_right_panel_data('{$schema}', '{$current_date}', {$user_id}) AS a
                LEFT JOIN {$this->schema}.master_device_setup AS b ON (a.deviceid = b.deviceid)
                WHERE a.group_id = 2 AND a.deviceid IS NOT NULL AND a.status_color IS NOT NULL
            ");
            $data_online = $query->getResult();
        } else {
            $query = $this->db->query("
                SELECT a.*, b.device_name
                FROM public.get_right_panel_data('{$schema}', '{$current_date}', {$user_id}) AS a
                LEFT JOIN {$this->schema}.master_device_setup AS b ON (a.deviceid = b.deviceid)
                WHERE a.group_id = 2 AND a.deviceid IS NOT NULL AND a.status_color IS NOT NULL
            ");
            $data_online = $query->getResult();
        }
    
        // Count devices that don't contain 'stock' in the device name
        $counter = 0;
        foreach ($data_online as $device) {
            if (strpos(strtolower($device->device_name), 'stock') === false) {
                $counter++;
            }
        }
        $data['online'] = $counter;

        // Load the view
        $data['middle'] =  view('controlcentre/ondutydevice', $data);

        // Load the main layout view
        return view('mainlayout', $data);
    }

    public function offDutyDevice()
    {
        if (!session()->has('login_sess_data')) {
            return redirect()->to('/');
        }

        $data = [];
        $data['sessdata'] = $this->sessdata;
        $data['page_title'] = "Off Duty Device";

        $current_date = date("Y-m-d");
        $user_id = $this->sessdata['user_id'];
        $group_id = $this->sessdata['group_id'];

        $offlineCountQuery = "
            SELECT COUNT(*) AS offline 
            FROM public.get_right_panel_data('{$this->schema}', '{$current_date}', {$user_id}) AS a
            LEFT JOIN {$this->schema}.master_device_setup AS b ON (a.deviceid = b.deviceid)
            WHERE a.group_id = 2 
              AND a.deviceid IS NOT NULL 
              AND a.status_color IS NULL 
              AND b.device_name IS NOT NULL 
              AND POSITION('Stock' IN b.device_name) = 0
        ";

        $data_offline = $this->db->query($offlineCountQuery)->getRow();
        $data['offline'] = $data_offline->offline;

        $data['middle'] = view('controlcentre/offdutydevice', $data);
        return view('mainlayout', $data);
    }

    public function onLiveDutyDeviceLoad()
    {
        $groupId = $this->sessdata['group_id']; 
        $parentId = $this->sessdata['user_id']; 
        $result = '';
        $currentDate = date("Y-m-d"); 
        
        $resultArr = $this->controlCentreModel->getRightPanelWeb($this->schema, $currentDate, $groupId, $parentId);
        $createMenu = [];
        $createMenu1 = []; // This should be defined if used
        
        if (!empty($resultArr)) {
            if ($groupId == 4) {
                foreach ($resultArr as $value) {
                    if (!empty($value->deptuser)) {
                        $createMenu[$value->deptuser][$value->subdeptuser][$value->user_id][] = $value;
                        $createMenu[$value->deptuser]['deptuser'] = $value->deptuser;
                        $createMenu[$value->deptuser]['deptorganisation'] = $value->deptorganisation;
                        $createMenu[$value->deptuser][$value->subdeptuser]['subdeptuser'] = $value->subdeptuser;
                        $createMenu[$value->deptuser][$value->subdeptuser]['subdeptorganisation'] = $value->subdeptorganisation;
                    } else {
                        $createMenu1[$value->user_id][] = $value;
                    }
                }

                if (count($createMenu) > 0) {
                    foreach ($createMenu as $deptKey => $deptValue) {
                        $result .= '<div class="panel-heading">
                                        <h4 class="panel-title">								
                                            <a data-toggle="collapse" data-parent="#level1-accordion" href="#plast-pro-' . $deptKey . '" class="collapsed">
                                                <span class="accordian-arrow">
                                                    <i class="fa fa-minus-square-o" aria-hidden="true"></i>
                                                    <i class="fa fa-plus-square-o" aria-hidden="true"></i>
                                                </span>' . $deptValue['deptorganisation'] . '</a>
                                        </h4>
                                    </div>';
                        $result .= '<div id="plast-pro-' . $deptKey . '" class="panel-collapse collapse">';
                        
                        foreach ($deptValue as $subDeptKey => $subDeptValue) {
                            if ($subDeptKey != 'deptorganisation' && $subDeptKey != 'deptuser') {
                                $result .= '<div class="panel-heading">
                                                <h4 class="panel-title">								
                                                    <a data-toggle="collapse" href="#plast-pro-' . $subDeptKey . '" class="collapsed">
                                                        <span class="accordian-arrow">
                                                            <i class="fa fa-minus-square-o" aria-hidden="true"></i>
                                                            <i class="fa fa-plus-square-o" aria-hidden="true"></i>
                                                        </span>&nbsp;&nbsp;' . $subDeptValue['subdeptorganisation'] . '</a>
                                                </h4>
                                            </div>';
                                $result .= '<div id="plast-pro-' . $subDeptKey . '" class="panel-collapse collapse">';
                                
                                foreach ($subDeptValue as $userKey => $userValue) {
                                    if ($userKey != 'subdeptorganisation' && $userKey != 'subdeptuser') {
                                        $counter = 0;
                                        foreach ($userValue as $device) {
                                            if (empty($device->status_color)) {
                                                $counter++;
                                            }
                                        }
                                        $result .= '<div class="panel-heading">
                                                        <h4 class="panel-title">								
                                                            <a data-toggle="collapse" href="#plast-pro-' . $userKey . '" class="collapsed">
                                                                <span class="accordian-arrow">
                                                                    <i class="fa fa-minus-square-o" aria-hidden="true"></i>
                                                                    <i class="fa fa-plus-square-o" aria-hidden="true"></i>
                                                                </span>&nbsp;&nbsp;&nbsp;&nbsp;' . $userValue[0]->organisation .' ('.$counter.')</a>
                                                        </h4>
                                                    </div>';									
                                        $result .= '<div id="plast-pro-' . $userKey . '" class="panel-collapse collapse">
                                                        <input type="text" id="myInputDeviceSearch'.$userKey.'" class="myInput" onkeyup="rightpaneldevicesearch('.$userKey.')" placeholder="Search device..">
                                                        <select class="myInputSelect" onchange="dropdowndevicesearch('.$userKey.', this.value)" style="display:none;">
                                                            <option value="">All</option>
                                                            <option value="key">Keyman</option>
                                                            <option value="patrol">Patrolman</option>
                                                        </select>
                                                        <div class="panel-body">				
                                                            <ul class="list-unstyled" id="myUL'.$userKey.'">';
                                        $activeCount = 0;
                                        
                                        foreach ($userValue as $device) {
                                            $dnameArr = explode("$", $device->device_name);
                                            
                                            $aliasName = 'N/A';
                                            $deviceName = 'N/A';

                                            if(count($dnameArr)>1){
                                                $deviceName = $dnameArr[0];
                                                $silentCallFlag = $dnameArr[1];
                                            }
                                            $aliasName = (empty($deviceName)) ? "" : "(" . $deviceName . ")";
                                            if ((!empty($device->status_color)) && $device->status_color == 'G') {
                                                $activeCount++;
                                                $result .= '<li>
                                                                <div class="checkbox">
                                                                    <div class="dropdown" style="height:0px;">
                                                                        <a id="dLabel" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false" style="padding: 3px;">
                                                                            <img src="' . base_url() . '/assets/images/vm.png" alt="menu" class="devicemenudd"/>
                                                                        </a>
                                                                        <ul class="dropdown-menu deviceconfigmenu" aria-labelledby="dLabel">
                                                                            
                                                                        </ul>
                                                                    </div>
                                                                    <label style="width: 98%;margin-left: 10px;position:relative;">';
                                                                    // <li onclick="followdevice(' . $device->deviceid . ')"><span>View On Map</span></li>
                                                if ($device->status_color == 'G') {
                                                    $result .= '<img src="' . base_url() . '/assets/images/online.png" alt="online" class="user-online-offline"/>';
                                                } // else {
                                                //     $result .= '<img src="' . base_url() . '/assets/images/mute.png" alt="online" class="user-online-offline"/>';	
                                                // }
                                                $result .= '<a href="javascript:void(0);" style="padding:0;">' . $device->serial_no . ' ' . $aliasName . '</a></label>
                                                                </div>		
                                                            </li>';
                                            }
                                        }
                                        
                                        if ($activeCount == 0) {
                                            $result .= '<li><div class="checkbox">
                                                            <div class="dropdown" style="height:0px;"></div> 
                                                            <label style="width:98%;margin-left: 10px;position:relative;">No Device</label></div></li>';
                                        }
                                        
                                        $result .= '</ul>
                                                    </div>
                                                </div>';
                                    }
                                }
                                $result .= '</div>';
                            }
                        }
                        $result .= '</div>';
                    }
                }
                
                if (count($createMenu1) > 0) {
                    foreach ($createMenu1 as $key => $value) {
                        $result .= '<div class="panel-heading">
                                        <h4 class="panel-title">								
                                            <a data-toggle="collapse" data-parent="#level1-accordion" href="#plast-pro-' . $key . '" class="collapsed">
                                                <span class="accordian-arrow">
                                                    <i class="fa fa-minus-square-o" aria-hidden="true"></i>
                                                    <i class="fa fa-plus-square-o" aria-hidden="true"></i>
                                                </span>' . $value[0]->organisation . '</a>
                                        </h4>
                                    </div>';
                        $result .= '<div id="plast-pro-' . $key . '" class="panel-collapse collapse">
                                        <input type="text" id="myInputDeviceSearch'.$key.'" class="myInput" onkeyup="rightpaneldevicesearch('.$key.')" placeholder="Search device..">
                                        <select class="myInputSelect" onchange="dropdowndevicesearch('.$key.', this.value)" style="display:none;">
                                            <option value="">All</option>
                                            <option value="key">Keyman</option>
                                            <option value="patrol">Patrolman</option>
                                        </select>
                                        <div class="panel-body">				
                                            <ul class="list-unstyled" id="myUL'.$key.'">';
                        $activeCount = 0;
                        
                        foreach ($value as $device) {
                            $dnameArr = explode("-", $device->device_name);
                            $aliasName = 'N/A';
                            $deviceName = 'N/A';

                            if(count($dnameArr)>1){
                                $deviceName = $dnameArr[0];
                                $silentCallFlag = $dnameArr[1];
                            }
                            $aliasName = (empty($deviceName)) ? "" : "(" . $deviceName . ")";
                            if (!empty($device->status_color)) {
                                $activeCount++;
                                $result .= '<li>
                                                <div class="checkbox">
                                                    <div class="dropdown" style="height:0px;">
                                                        <a id="dLabel" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false" style="padding: 3px;">
                                                            <img src="' . base_url() . '/assets/images/vm.png" alt="menu" class="devicemenudd"/>
                                                        </a>
                                                        <ul class="dropdown-menu deviceconfigmenu" aria-labelledby="dLabel">
                                                            
                                                        </ul>
                                                    </div>
                                                    <label style="width: 98%;margin-left: 10px;position:relative;">';
                                                    // <li onclick="followdevice(' . $device->deviceid . ')"><span>View On Map</span></li>
                                if ($device->status_color == 'G') {
                                    $result .= '<img src="' . base_url() . '/assets/images/online.png" alt="online" class="user-online-offline"/>';
                                } else {
                                    $result .= '<img src="' . base_url() . '/assets/images/mute.png" alt="online" class="user-online-offline"/>';	
                                }
                                $result .= '<a href="javascript:void(0);" style="padding:0;">' . $device->serial_no . ' ' . $aliasName . '</a></label>
                                                </div>		
                                            </li>';
                            }
                        }
                        
                        if ($activeCount == 0) {
                            $result .= '<li><div class="checkbox">
                                            <div class="dropdown" style="height:0px;"></div> 
                                            <label style="width:98%;margin-left: 10px;position:relative;">No Device</label></div></li>';
                        }
                        
                        $result .= '</ul>
                                    </div>
                                </div>';
                    }
                }
            } else {
                foreach ($resultArr as $value) {
                    $createMenu[$value->user_id][] = $value;
                }
                
                foreach ($createMenu as $key => $value) {
                    $counter = 0;
                    foreach ($value as $device) {
                        if (!empty($device->status_color) && $device->status_color == 'G') {
                            $counter++;
                        }
                    }
                    $result .= '<div class="panel-heading">
                                    <h4 class="panel-title">								
                                        <a data-toggle="collapse" data-parent="#level1-accordion" href="#plast-pro-' . $key . '" class="collapsed">
                                            <span class="accordian-arrow">
                                                <i class="fa fa-minus-square-o" aria-hidden="true"></i>
                                                <i class="fa fa-plus-square-o" aria-hidden="true"></i>
                                            </span>' . $value[0]->organisation .' ('.$counter.')</a>
                                    </h4>
                                </div>';
                    $result .= '<div id="plast-pro-' . $key . '" class="panel-collapse collapse">
                                    <input type="text" id="myInputDeviceSearch'.$key.'" class="myInput" onkeyup="rightpaneldevicesearch('.$key.')" placeholder="Search device..">
                                    <select class="myInputSelect" onchange="dropdowndevicesearch('.$key.', this.value)" style="display:none;">
                                        <option value="">All</option>
                                        <option value="key">Keyman</option>
                                        <option value="patrol">Patrolman</option>
                                    </select>
                                    <div class="panel-body">				
                                        <ul class="list-unstyled" id="myUL'.$key.'">';
                    $activeCount = 0;		
                    foreach ($value as $device) {
                        $dnameArr = explode("-", $device->device_name);
                        $aliasName = 'N/A';
                        $deviceName = 'N/A';

                        if(count($dnameArr)>1){
                            $deviceName = $dnameArr[0];
                            $silentCallFlag = $dnameArr[1];
                        }

                        $aliasName = (empty($deviceName)) ? "" : "(" . $deviceName . ")";
                        if (!empty($device->status_color) && $device->status_color == 'G') {
                            $activeCount++;
                            $result .= '<li>
                                            <div class="checkbox">
                                                <div class="dropdown" style="height:0px;">
                                                    <a id="dLabel" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false" style="padding: 3px;">
                                                        <img src="' . base_url() . '/assets/images/vm.png" alt="menu" class="devicemenudd"/>
                                                    </a>
                                                    <ul class="dropdown-menu deviceconfigmenu" aria-labelledby="dLabel">
                                                        
                                                    </ul>
                                                </div>
                                                <label style="width: 98%;margin-left: 10px;position:relative;">';
                                                // <li onclick="followdevice(' . $device->deviceid . ')"><span>View On Map</span></li>
                            if ($device->status_color == 'G') {
                                $result .= '<img src="' . base_url() . '/assets/images/online.png" alt="online" class="user-online-offline"/>';
                            } // else {
                            //     $result .= '<img src="' . base_url() . '/assets/images/mute.png" alt="online" class="user-online-offline"/>';	
                            // }
                            $result .= '<a href="javascript:void(0);" style="padding:0;">' . $device->serial_no . ' ' . $aliasName . '</a></label>
                                            </div>		
                                        </li>';
                        }
                    }
                    
                    if ($activeCount == 0) {
                        $result .= '<li><div class="checkbox">
                                        <div class="dropdown" style="height:0px;"></div> 
                                        <label style="width:98%;margin-left: 10px;position:relative;">No Device</label></div></li>';
                    }
                    
                    $result .= '</ul>
                                </div>
                            </div>';
                }
            }
        }

        return $this->response->setJSON(["status" => 1, "result" => $result]);
    }

    public function onDutyDeviceLoad()
    {
        $groupId = $this->sessdata['group_id']; 
        $parentId = $this->sessdata['user_id']; 
        $result = '';
        $currentDate = date("Y-m-d"); 
        
        $resultArr = $this->controlCentreModel->getRightPanelWeb($this->schema, $currentDate, $groupId, $parentId);
        $createMenu = [];
        $createMenu1 = []; // This should be defined if used
        
        if (!empty($resultArr)) {
            if ($groupId == 4) {
                foreach ($resultArr as $value) {
                    if (!empty($value->deptuser)) {
                        $createMenu[$value->deptuser][$value->subdeptuser][$value->user_id][] = $value;
                        $createMenu[$value->deptuser]['deptuser'] = $value->deptuser;
                        $createMenu[$value->deptuser]['deptorganisation'] = $value->deptorganisation;
                        $createMenu[$value->deptuser][$value->subdeptuser]['subdeptuser'] = $value->subdeptuser;
                        $createMenu[$value->deptuser][$value->subdeptuser]['subdeptorganisation'] = $value->subdeptorganisation;
                    } else {
                        $createMenu1[$value->user_id][] = $value;
                    }
                }

                if (count($createMenu) > 0) {
                    foreach ($createMenu as $deptKey => $deptValue) {
                        $result .= '<div class="panel-heading">
                                        <h4 class="panel-title">								
                                            <a data-toggle="collapse" data-parent="#level1-accordion" href="#plast-pro-' . $deptKey . '" class="collapsed">
                                                <span class="accordian-arrow">
                                                    <i class="fa fa-minus-square-o" aria-hidden="true"></i>
                                                    <i class="fa fa-plus-square-o" aria-hidden="true"></i>
                                                </span>' . $deptValue['deptorganisation'] . '</a>
                                        </h4>
                                    </div>';
                        $result .= '<div id="plast-pro-' . $deptKey . '" class="panel-collapse collapse">';
                        
                        foreach ($deptValue as $subDeptKey => $subDeptValue) {
                            if ($subDeptKey != 'deptorganisation' && $subDeptKey != 'deptuser') {
                                $result .= '<div class="panel-heading">
                                                <h4 class="panel-title">								
                                                    <a data-toggle="collapse" href="#plast-pro-' . $subDeptKey . '" class="collapsed">
                                                        <span class="accordian-arrow">
                                                            <i class="fa fa-minus-square-o" aria-hidden="true"></i>
                                                            <i class="fa fa-plus-square-o" aria-hidden="true"></i>
                                                        </span>&nbsp;&nbsp;' . $subDeptValue['subdeptorganisation'] . '</a>
                                                </h4>
                                            </div>';
                                $result .= '<div id="plast-pro-' . $subDeptKey . '" class="panel-collapse collapse">';
                                
                                foreach ($subDeptValue as $userKey => $userValue) {
                                    if ($userKey != 'subdeptorganisation' && $userKey != 'subdeptuser') {
                                        $counter = 0;
                                        foreach ($userValue as $device) {
                                            if (empty($device->status_color)) {
                                                $counter++;
                                            }
                                        }
                                        $result .= '<div class="panel-heading">
                                                        <h4 class="panel-title">								
                                                            <a data-toggle="collapse" href="#plast-pro-' . $userKey . '" class="collapsed">
                                                                <span class="accordian-arrow">
                                                                    <i class="fa fa-minus-square-o" aria-hidden="true"></i>
                                                                    <i class="fa fa-plus-square-o" aria-hidden="true"></i>
                                                                </span>&nbsp;&nbsp;&nbsp;&nbsp;' . $userValue[0]->organisation .' ('.$counter.')</a>
                                                        </h4>
                                                    </div>';									
                                        $result .= '<div id="plast-pro-' . $userKey . '" class="panel-collapse collapse">
                                                        <input type="text" id="myInputDeviceSearch'.$userKey.'" class="myInput" onkeyup="rightpaneldevicesearch('.$userKey.')" placeholder="Search device..">
                                                        <select class="myInputSelect" onchange="dropdowndevicesearch('.$userKey.', this.value)" style="display:none;">
                                                            <option value="">All</option>
                                                            <option value="key">Keyman</option>
                                                            <option value="patrol">Patrolman</option>
                                                        </select>
                                                        <div class="panel-body">				
                                                            <ul class="list-unstyled" id="myUL'.$userKey.'">';
                                        $activeCount = 0;
                                        
                                        foreach ($userValue as $device) {
                                            $dnameArr = explode("$", $device->device_name);
                                            
                                            $aliasName = 'N/A';
                                            $deviceName = 'N/A';

                                            if(count($dnameArr)>1){
                                                $deviceName = $dnameArr[0];
                                                $silentCallFlag = $dnameArr[1];
                                            }
                                            $aliasName = (empty($deviceName)) ? "" : "(" . $deviceName . ")";
                                            if (!empty($device->status_color)) {
                                                $activeCount++;
                                                $result .= '<li>
                                                                <div class="checkbox">
                                                                    <div class="dropdown" style="height:0px;">
                                                                        <a id="dLabel" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false" style="padding: 3px;">
                                                                            <img src="' . base_url() . '/assets/images/vm.png" alt="menu" class="devicemenudd"/>
                                                                        </a>
                                                                        <ul class="dropdown-menu deviceconfigmenu" aria-labelledby="dLabel">
                                                                            
                                                                        </ul>
                                                                    </div>
                                                                    <label style="width: 98%;margin-left: 10px;position:relative;">';
                                                                    // <li onclick="followdevice(' . $device->deviceid . ')"><span>View On Map</span></li>
                                                if ($device->status_color == 'G') {
                                                    $result .= '<img src="' . base_url() . '/assets/images/online.png" alt="online" class="user-online-offline"/>';
                                                } else {
                                                    $result .= '<img src="' . base_url() . '/assets/images/mute.png" alt="online" class="user-online-offline"/>';	
                                                }
                                                $result .= '<a href="javascript:void(0);" style="padding:0;">' . $device->serial_no . ' ' . $aliasName . '</a></label>
                                                                </div>		
                                                            </li>';
                                            }
                                        }
                                        
                                        if ($activeCount == 0) {
                                            $result .= '<li><div class="checkbox">
                                                            <div class="dropdown" style="height:0px;"></div> 
                                                            <label style="width:98%;margin-left: 10px;position:relative;">No Device</label></div></li>';
                                        }
                                        
                                        $result .= '</ul>
                                                    </div>
                                                </div>';
                                    }
                                }
                                $result .= '</div>';
                            }
                        }
                        $result .= '</div>';
                    }
                }
                
                if (count($createMenu1) > 0) {
                    foreach ($createMenu1 as $key => $value) {
                        $result .= '<div class="panel-heading">
                                        <h4 class="panel-title">								
                                            <a data-toggle="collapse" data-parent="#level1-accordion" href="#plast-pro-' . $key . '" class="collapsed">
                                                <span class="accordian-arrow">
                                                    <i class="fa fa-minus-square-o" aria-hidden="true"></i>
                                                    <i class="fa fa-plus-square-o" aria-hidden="true"></i>
                                                </span>' . $value[0]->organisation . '</a>
                                        </h4>
                                    </div>';
                        $result .= '<div id="plast-pro-' . $key . '" class="panel-collapse collapse">
                                        <input type="text" id="myInputDeviceSearch'.$key.'" class="myInput" onkeyup="rightpaneldevicesearch('.$key.')" placeholder="Search device..">
                                        <select class="myInputSelect" onchange="dropdowndevicesearch('.$key.', this.value)" style="display:none;">
                                            <option value="">All</option>
                                            <option value="key">Keyman</option>
                                            <option value="patrol">Patrolman</option>
                                        </select>
                                        <div class="panel-body">				
                                            <ul class="list-unstyled" id="myUL'.$key.'">';
                        $activeCount = 0;
                        
                        foreach ($value as $device) {
                            $dnameArr = explode("-", $device->device_name);
                            $aliasName = 'N/A';
                            $deviceName = 'N/A';

                            if(count($dnameArr)>1){
                                $deviceName = $dnameArr[0];
                                $silentCallFlag = $dnameArr[1];
                            }
                            $aliasName = (empty($deviceName)) ? "" : "(" . $deviceName . ")";
                            if (!empty($device->status_color)) {
                                $activeCount++;
                                $result .= '<li>
                                                <div class="checkbox">
                                                    <div class="dropdown" style="height:0px;">
                                                        <a id="dLabel" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false" style="padding: 3px;">
                                                            <img src="' . base_url() . '/assets/images/vm.png" alt="menu" class="devicemenudd"/>
                                                        </a>
                                                        <ul class="dropdown-menu deviceconfigmenu" aria-labelledby="dLabel">
                                                            
                                                        </ul>
                                                    </div>
                                                    <label style="width: 98%;margin-left: 10px;position:relative;">';
                                                    // <li onclick="followdevice(' . $device->deviceid . ')"><span>View On Map</span></li>
                                if ($device->status_color == 'G') {
                                    $result .= '<img src="' . base_url() . '/assets/images/online.png" alt="online" class="user-online-offline"/>';
                                } else {
                                    $result .= '<img src="' . base_url() . '/assets/images/mute.png" alt="online" class="user-online-offline"/>';	
                                }
                                $result .= '<a href="javascript:void(0);" style="padding:0;">' . $device->serial_no . ' ' . $aliasName . '</a></label>
                                                </div>		
                                            </li>';
                            }
                        }
                        
                        if ($activeCount == 0) {
                            $result .= '<li><div class="checkbox">
                                            <div class="dropdown" style="height:0px;"></div> 
                                            <label style="width:98%;margin-left: 10px;position:relative;">No Device</label></div></li>';
                        }
                        
                        $result .= '</ul>
                                    </div>
                                </div>';
                    }
                }
            } else {
                foreach ($resultArr as $value) {
                    $createMenu[$value->user_id][] = $value;
                }
                
                foreach ($createMenu as $key => $value) {
                    $counter = 0;
                    foreach ($value as $device) {
                        if (!empty($device->status_color)) {
                            $counter++;
                        }
                    }
                    $result .= '<div class="panel-heading">
                                    <h4 class="panel-title">								
                                        <a data-toggle="collapse" data-parent="#level1-accordion" href="#plast-pro-' . $key . '" class="collapsed">
                                            <span class="accordian-arrow">
                                                <i class="fa fa-minus-square-o" aria-hidden="true"></i>
                                                <i class="fa fa-plus-square-o" aria-hidden="true"></i>
                                            </span>' . $value[0]->organisation .' ('.$counter.')</a>
                                    </h4>
                                </div>';
                    $result .= '<div id="plast-pro-' . $key . '" class="panel-collapse collapse">
                                    <input type="text" id="myInputDeviceSearch'.$key.'" class="myInput" onkeyup="rightpaneldevicesearch('.$key.')" placeholder="Search device..">
                                    <select class="myInputSelect" onchange="dropdowndevicesearch('.$key.', this.value)" style="display:none;">
                                        <option value="">All</option>
                                        <option value="key">Keyman</option>
                                        <option value="patrol">Patrolman</option>
                                    </select>
                                    <div class="panel-body">				
                                        <ul class="list-unstyled" id="myUL'.$key.'">';
                    $activeCount = 0;		
                    foreach ($value as $device) {
                        $dnameArr = explode("-", $device->device_name);
                        $aliasName = 'N/A';
                        $deviceName = 'N/A';

                        if(count($dnameArr)>1){
                            $deviceName = $dnameArr[0];
                            $silentCallFlag = $dnameArr[1];
                        }

                        $aliasName = (empty($deviceName)) ? "" : "(" . $deviceName . ")";
                        if (!empty($device->status_color)) {
                            $activeCount++;
                            $result .= '<li>
                                            <div class="checkbox">
                                                <div class="dropdown" style="height:0px;">
                                                    <a id="dLabel" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false" style="padding: 3px;">
                                                        <img src="' . base_url() . '/assets/images/vm.png" alt="menu" class="devicemenudd"/>
                                                    </a>
                                                    <ul class="dropdown-menu deviceconfigmenu" aria-labelledby="dLabel">
                                                        
                                                    </ul>
                                                </div>
                                                <label style="width: 98%;margin-left: 10px;position:relative;">';
                                                // <li onclick="followdevice(' . $device->deviceid . ')"><span>View On Map</span></li>
                            if ($device->status_color == 'G') {
                                $result .= '<img src="' . base_url() . '/assets/images/online.png" alt="online" class="user-online-offline"/>';
                            } else {
                                $result .= '<img src="' . base_url() . '/assets/images/mute.png" alt="online" class="user-online-offline"/>';	
                            }
                            $result .= '<a href="javascript:void(0);" style="padding:0;">' . $device->serial_no . ' ' . $aliasName . '</a></label>
                                            </div>		
                                        </li>';
                        }
                    }
                    
                    if ($activeCount == 0) {
                        $result .= '<li><div class="checkbox">
                                        <div class="dropdown" style="height:0px;"></div> 
                                        <label style="width:98%;margin-left: 10px;position:relative;">No Device</label></div></li>';
                    }
                    
                    $result .= '</ul>
                                </div>
                            </div>';
                }
            }
        }

        return $this->response->setJSON(["status" => 1, "result" => $result]);
    }

    public function offDutyDeviceLoad()
    {
        $groupid = $this->sessdata['group_id'];
        $parentid = $this->sessdata['user_id'];
        $result = '';
        $currentDate = date("Y-m-d");
        $resultArr = $this->controlCentreModel->getRightPanelWeb($this->schema, $currentDate, $groupid, $parentid);
        $createMenu = [];
        $createMenu1 = [];

        if (!empty($resultArr)) {
            if ($groupid == 4) {
                foreach ($resultArr as $value) {
                    if (!empty($value->deptuser)) {
                        $createMenu[$value->deptuser][$value->subdeptuser][$value->user_id][] = $value;
                        $createMenu[$value->deptuser]['deptuser'] = $value->deptuser;
                        $createMenu[$value->deptuser]['deptorganisation'] = $value->deptorganisation;
                        $createMenu[$value->deptuser][$value->subdeptuser]['subdeptuser'] = $value->subdeptuser;
                        $createMenu[$value->deptuser][$value->subdeptuser]['subdeptorganisation'] = $value->subdeptorganisation;
                    } else {
                        $createMenu1[$value->user_id][] = $value;
                    }
                }

                if (count($createMenu) > 0) {
                    foreach ($createMenu as $deptkey => $deptvalue) {
                        $result .= '<div class="panel-heading">
                                        <h4 class="panel-title">
                                            <a data-toggle="collapse" data-parent="#level1-accordion" href="#plast-pro-' . $deptkey . '" class="collapsed">
                                                <span class="accordian-arrow">
                                                    <i class="fa fa-minus-square-o" aria-hidden="true"></i>
                                                    <i class="fa fa-plus-square-o" aria-hidden="true"></i>
                                                </span>' . $deptvalue['deptorganisation'] . '</a>
                                        </h4>
                                    </div>';
                        $result .= '<div id="plast-pro-' . $deptkey . '" class="panel-collapse collapse">';

                        foreach ($deptvalue as $subdeptkey => $subdeptvalue) {
                            if ($subdeptkey != 'deptorganisation' && $subdeptkey != 'deptuser') {
                                $result .= '<div class="panel-heading">
                                                <h4 class="panel-title">
                                                    <a data-toggle="collapse" data-parent="#level1-accordion" href="#plast-pro-' . $subdeptkey . '" class="collapsed">
                                                        <span class="accordian-arrow">
                                                            <i class="fa fa-minus-square-o" aria-hidden="true"></i>
                                                            <i class="fa fa-plus-square-o" aria-hidden="true"></i>
                                                        </span>&nbsp;&nbsp;' . $subdeptvalue['subdeptorganisation'] . '</a>
                                                </h4>
                                            </div>';
                                $result .= '<div id="plast-pro-' . $subdeptkey . '" class="panel-collapse collapse">';
                                foreach ($subdeptvalue as $userkey => $uservalue) {
                                    if ($userkey != 'subdeptorganisation' && $userkey != 'subdeptuser') {
                                        $counter = 0;
                                        foreach ($uservalue as $v) {
                                            if (empty($v->status_color)) {
                                                $counter++;
                                            }
                                        }
                                        $result .= '<div class="panel-heading">
                                                        <h4 class="panel-title">
                                                            <a data-toggle="collapse" data-parent="#level1-accordion" href="#plast-pro-' . $userkey . '" class="collapsed">
                                                                <span class="accordian-arrow">
                                                                    <i class="fa fa-minus-square-o" aria-hidden="true"></i>
                                                                    <i class="fa fa-plus-square-o" aria-hidden="true"></i>
                                                                </span>&nbsp;&nbsp;&nbsp;&nbsp;' . $uservalue[0]->organisation . ' (' . $counter . ')</a>
                                                        </h4>
                                                    </div>';

                                        $result .= '<div id="plast-pro-' . $userkey . '" class="panel-collapse collapse">
                                                        <input type="text" id="myInputDeviceSearch' . $userkey . '" class="myInput" onkeyup="rightpaneldevicesearch(' . $userkey . ')" placeholder="Search device..">
                                                        <select class="myInputSelect" onchange="dropdowndevicesearch(' . $userkey . ', this.value)" style="display:none;">
                                                            <option value="">All</option>
                                                            <option value="key">Keyman</option>
                                                            <option value="patrol">Patrolman</option>
                                                        </select>
                                                        <div class="panel-body">				
                                                            <ul class="list-unstyled" id="myUL' . $userkey . '">';
                                        $inactiveCount = 0;

                                        foreach ($uservalue as $v) {
                                            $dnameArr = explode("-", $v->device_name);
                                            $aliasName = 'N/A';
                                            $deviceName = 'N/A';

                                            if(count($dnameArr)>1){
                                                $deviceName = $dnameArr[0];
                                                // $silentCallFlag = $dnameArr[1];
                                            }
                                            $aliasName = (empty($deviceName)) ? "" : "(" . $deviceName . ")";
                                            if (empty($v->status_color)) { // inactive
                                                $inactiveCount++;
                                                $result .= '<li>
                                                                <div class="checkbox">
                                                                    <div class="dropdown" style="height:0px;"></div>
                                                                    <label style="width:98%;margin-left: 10px;position:relative;">
                                                                        <img src="' . base_url() . '/assets/images/offline.png" alt="offline" class="user-online-offline" />
                                                                        <a href="javascript:void(0);" style="padding:0;">' . $v->serial_no . ' ' . $aliasName . '</a>
                                                                    </label>
                                                                </div>
                                                            </li>';
                                            }
                                        }

                                        if ($inactiveCount == 0) {
                                            $result .= '<li><div class="checkbox">
                                                            <div class="dropdown" style="height:0px;"></div>
                                                            <label style="width:98%;margin-left: 10px;position:relative;">No Device</label>
                                                        </div></li>';
                                        }

                                        $result .= '</ul>
                                                    </div>
                                                </div>';
                                    }
                                }
                                $result .= '</div>';
                            }
                        }
                        $result .= '</div>';
                    }
                }

                if (count($createMenu1) > 0) {
                    foreach ($createMenu1 as $key => $value) {
                        $result .= '<div class="panel-heading">
                                        <h4 class="panel-title">
                                            <a data-toggle="collapse" data-parent="#level1-accordion" href="#plast-pro-' . $key . '" class="collapsed">
                                                <span class="accordian-arrow">
                                                    <i class="fa fa-minus-square-o" aria-hidden="true"></i>
                                                    <i class="fa fa-plus-square-o" aria-hidden="true"></i>
                                                </span>' . $value[0]->organisation . '</a>
                                        </h4>
                                    </div>';
                        $result .= '<div id="plast-pro-' . $key . '" class="panel-collapse collapse">
                                        <input type="text" id="myInputDeviceSearch' . $key . '" class="myInput" onkeyup="rightpaneldevicesearch(' . $key . ')" placeholder="Search device..">
                                        <select class="myInputSelect" onchange="dropdowndevicesearch(' . $key . ', this.value)" style="display:none;">
                                            <option value="">All</option>
                                            <option value="key">Keyman</option>
                                            <option value="patrol">Patrolman</option>
                                        </select>
                                        <div class="panel-body">				
                                            <ul class="list-unstyled" id="myUL' . $key . '">';
                        $inactiveCount = 0;

                        foreach ($value as $v) {
                            $dnameArr = explode("-", $v->device_name);
                            $deviceName = $dnameArr[0];
                            $aliasName = (empty($deviceName)) ? "" : "(" . $deviceName . ")";
                            if (empty($v->status_color)) { // inactive
                                $inactiveCount++;
                                $result .= '<li>
                                                <div class="checkbox">
                                                    <div class="dropdown" style="height:0px;"></div>
                                                    <label style="width:98%;margin-left: 10px;position:relative;">
                                                        <img src="' . base_url() . '/assets/images/offline.png" alt="offline" class="user-online-offline" />
                                                        <a href="javascript:void(0);" style="padding:0;">' . $v->serial_no . ' ' . $aliasName . '</a>
                                                    </label>
                                                </div>
                                            </li>';
                            }
                        }

                        if ($inactiveCount == 0) {
                            $result .= '<li><div class="checkbox">
                                            <div class="dropdown" style="height:0px;"></div>
                                            <label style="width:98%;margin-left: 10px;position:relative;">No Device</label>
                                        </div></li>';
                        }

                        $result .= '</ul>
                                    </div>
                                    </div>';
                    }
                }
            } else {
                foreach ($resultArr as $value) {
                    $createMenu[$value->user_id][] = $value;
                }

                foreach ($createMenu as $key => $value) {
                    $counter = 0;
                    foreach ($value as $v) {
                        if (empty($v->status_color)) {
                            $counter++;
                        }
                    }
                    $result .= '<div class="panel-heading">
                                    <h4 class="panel-title">
                                        <a data-toggle="collapse" data-parent="#level1-accordion" href="#plast-pro-' . $key . '" class="collapsed">
                                            <span class="accordian-arrow">
                                                <i class="fa fa-minus-square-o" aria-hidden="true"></i>
                                                <i class="fa fa-plus-square-o" aria-hidden="true"></i>
                                            </span>' . $value[0]->organisation . ' (' . $counter . ')</a>
                                    </h4>
                                </div>';
                    $result .= '<div id="plast-pro-' . $key . '" class="panel-collapse collapse">
                                    <input type="text" id="myInputDeviceSearch' . $key . '" class="myInput" onkeyup="rightpaneldevicesearch(' . $key . ')" placeholder="Search device..">
                                    <select class="myInputSelect" onchange="dropdowndevicesearch(' . $key . ', this.value)" style="display:none;">
                                        <option value="">All</option>
                                        <option value="key">Keyman</option>
                                        <option value="patrol">Patrolman</option>
                                    </select>
                                    <div class="panel-body">				
                                        <ul class="list-unstyled" id="myUL' . $key . '">';
                    $inactiveCount = 0;

                    foreach ($value as $v) {
                        $dnameArr = explode("-", $v->device_name);
                        $deviceName = $dnameArr[0];
                        $aliasName = (empty($deviceName)) ? "" : "(" . $deviceName . ")";
                        if (empty($v->status_color)) { // inactive
                            $inactiveCount++;
                            $result .= '<li>
                                            <div class="checkbox">
                                                <div class="dropdown" style="height:0px;"></div>
                                                <label style="width:98%;margin-left: 10px;position:relative;">
                                                    <img src="' . base_url() . '/assets/images/offline.png" alt="offline" class="user-online-offline" />
                                                    <a href="javascript:void(0);" style="padding:0;">' . $v->serial_no . ' ' . $aliasName . '</a>
                                                </label>
                                            </div>
                                        </li>';
                        }
                    }

                    if ($inactiveCount == 0) {
                        $result .= '<li><div class="checkbox">
                                        <div class="dropdown" style="height:0px;"></div>
                                        <label style="width:98%;margin-left: 10px;position:relative;">No Device</label>
                                    </div></li>';
                    }

                    $result .= '</ul>
                                </div>
                                </div>';
                }
            }
        }

        return $this->response->setJSON(["status" => 1, "result" => $result]);
    }

    public function getAllInteractionList()
    {
        $parent_id = $this->sessdata['parent_id'];
        $user_id = $this->sessdata['user_id'];
        $group_id = $this->sessdata['group_id'];

        // POI Query
        $poiData = $this->getGeofenceData($group_id, $user_id, 2); // Geofence type 2 for POI
        $poiHtml = $this->generateHtmlList($poiData, 'poi');

        // Route Query
        $routeData = $this->getGeofenceData($group_id, $user_id, 3); // Geofence type 3 for Route
        $routeHtml = $this->generateHtmlList($routeData, 'route');

        // Geofence Query
        $geofenceData = $this->getGeofenceData($group_id, $user_id, 1); // Geofence type 1 for Geofence
        $geofenceHtml = $this->generateHtmlList($geofenceData, 'geofence');

        // Preparing result
        $result = [
            'poihtml' => $poiHtml,
            'routehtml' => $routeHtml,
            'geofencehtml' => $geofenceHtml
        ];

        // Return response as JSON
        return $this->response->setJSON([
            'status' => 1,
            'result' => $result
        ]);
    }

    private function getGeofenceData($group_id, $user_id, $geofenceType)
    {
        // Querying geofences based on the group and user id
        $builder = $this->db->table($this->schema . '.master_geofence');
        $builder->where('geofencetype', $geofenceType);
        $builder->where('active', 1);
        
        if ($group_id == 4 || $group_id == 5) {
            // Company or Department level
            $builder->groupStart()
                    ->where('parent_id', $user_id)
                    ->orWhere('user_id', $user_id)
                    ->groupEnd();
        } else {
            // User level
            $builder->where('user_id', $user_id);
        }

        $builder->orderBy('id', 'desc');
        return $builder->get()->getResult();
    }

    private function generateHtmlList($data, $type)
    {
        if (count($data) > 0) {
            $html = '';
            foreach ($data as $item) {
                $html .= '<li><input type="checkbox" class="click' . $type . '" id="' . $item->id . '" /> ' . $item->geoname . '</li>';
            }
        } else {
            $html = 'No ' . ucfirst($type) . ' Available';
        }
        return $html;
    }

    public function getGeofencing()
    {
        $request = $this->request; // CodeIgniter 4 request object
        
        $deviceid = $request->getPost('deviceid');
        $schemaname = $this->schema;
        
        $parent_id = $this->sessdata['user_id'];
        $user_groupid = $this->sessdata['group_id'];
        $resultAssignedLonLat = $resultAvailableLonLat = [];
        $geofenceattachedid = '-1';

        // Already assigned LONS Lats
        if ($deviceid) {
            // Refactor get_assigned_geofence logic
            $get_geofences = $this->getAssignedGeofence($deviceid, $schemaname, $parent_id, $user_groupid);
            
            if (!empty($get_geofences)) {
                $lonLat = explode(",", $get_geofences->lonlat);
                $countLonLat = count($lonLat) - 1;

                for ($i = 0; $i < $countLonLat; $i += 2) {
                    $resultAssignedLonLat[] = [$lonLat[$i], $lonLat[$i + 1]];
                }
                $geofenceattachedid = $get_geofences->id;
            }
        }

        // Available stored data
        $resultAvailableLonLat = $this->getExistingGeofences();

        return $this->response->setJSON([
            'status' => 1,
            'result' => [
                "resultAssignedLonLat" => $resultAssignedLonLat,
                "resultAvailableLonLat" => $resultAvailableLonLat,
                "assignedid" => $geofenceattachedid
            ]
        ]);
    }

    public function getAssignedGeofence($deviceid, $schemaname, $parent_id, $group_id)
    {
        $builder = $this->db->table($schemaname . '.device_geofence_config');
        
        // Query to get assigned geofence
        $builder->select($schemaname . '.master_geofence.*');
        $builder->join($schemaname . '.master_geofence', $schemaname . '.master_geofence.id = ' . $schemaname . '.device_geofence_config.geomaster_id');
        $builder->where([
            $schemaname . '.device_geofence_config.device_id' => $deviceid,
            $schemaname . '.device_geofence_config.parent_id' => $parent_id,
            $schemaname . '.device_geofence_config.active' => 1,
            $schemaname . '.device_geofence_config.group_id' => $group_id
        ]);

        $query = $builder->get();
        return $query->getRow(); // Get single result
    }

    public function getExistingGeofences()
    {
        $parent_id = $this->sessdata['user_id'];
        $schemaname =  $this->schema; // You should get this from session or another source
        $builder = $this->db->table($schemaname . '.master_geofence');
        
        // Query to get existing geofences
        $builder->select('geoname, lonlat, id');
        $builder->where([
            'parent_id' => $parent_id,
            'active' => 1,
            'geofencetype' => 1
        ]);
        $builder->orderBy('geoname', 'asc');
        
        $query = $builder->get();
        $result = [];
        
        foreach ($query->getResult() as $row) {
            $result[$row->id] = $row;
        }

        return $result;
    }

    public function getDetailsofDevice()
    {
        // Retrieving POST data
        $deviceid = $this->request->getPost('deviceid');
        $user_id = $this->sessdata['user_id']; // Parent ID from session
        $schemaname = $this->schema;
        $user_groupid = $this->sessdata['group_id'];

        $result = [
            'sosdata' => '',
            'alertdata' => '',
            'calldata' => '',
            'devicedata' => '',
        ];

        $fetchdate = date("Y-m-d");

        // Getting device details
        $get_details = $this->controlCentreModel->getDeviceCurrentDetails($deviceid, $fetchdate);

        $cond = " AND deviceid = {$deviceid}";
        $cond_alert = " AND alertsp.deviceid = {$deviceid}";

        // Fetching calls
        $get_calls = $this->controlCentreModel->getCalls($user_groupid, $user_id, $schemaname, null, $cond);
        if (!empty($get_calls)) {
            $html = '<table class="table table-condensed"><tbody>';
            $html .= "<tr><th>Date-Time</th><th>I/O</th><th>Number</th><th>Location</th></tr>";
            foreach ($get_calls as $value) {
                $sendorrcv = ($value->sendorrecive == 'I') ? "Incoming" : "Outgoing";
                $html .= "<tr><td>" . date('d-m-Y', strtotime($value->currentdate)) . "<br>" . $value->currenttime . "</td><td>{$sendorrcv}</td><td>{$value->connectto}</td><td><a href='javascript:void(0)' onclick='setFocusFromNotificationListCall({$value->sosid})'>Locate</a></td></tr>";
            }
            $html .= '</tbody></table>';
            $result['calldata'] = $html;
        }

        // Fetching alerts
        $get_alerts = $this->controlCentreModel->getAlerts($user_groupid, $user_id, $schemaname, null, $cond_alert);
        if (!empty($get_alerts)) {
            $html = '<table class="table table-condensed"><tbody>';
            $html .= "<tr><th>Date-Time</th><th>Description</th><th>Location</th><th>Status</th></tr>";
            foreach ($get_alerts as $value) {
                if (!empty($value->resolve)) {
                    $html .= "<tr><td>" . date('d-m-Y', strtotime($value->currentdate)) . "<br>" . $value->currenttime . "</td><td>{$value->description}</td><td><a href='javascript:void(0)' onclick='setFocusFromNotificationListAlert({$value->sosid})'>Locate</a></td><td>Resolved</td></tr>";
                } else {
                    $html .= "<tr style='background-color:#ffb9b9' id='alertid_{$value->sosid}'><td>" . date('d-m-Y', strtotime($value->currentdate)) . "<br>" . $value->currenttime . "</td><td>{$value->description}</td><td><a href='javascript:void(0)' onclick='setFocusFromNotificationListAlert({$value->sosid})'>Locate</a></td><td id='alerttdid_{$value->sosid}'><a href='javascript:void(0)' onclick='resolveAlert({$value->sosid},\"{$value->schema_name_}\")'>Resolve</a></td></tr>";
                }
            }
            $html .= '</tbody></table>';
            $result['alertdata'] = $html;
        }

        // Fetching SOS data
        $get_sos = $this->controlCentreModel->getSos($user_groupid, $user_id, $schemaname, null, $cond);
        if (!empty($get_sos)) {
            $html = '<table class="table table-condensed"><tbody>';
            $html .= "<tr><th>Date</th><th>Time</th><th>Location</th><th>Status</th></tr>";
            foreach ($get_sos as $value) {
                if (!empty($value->resolve)) {
                    $html .= "<tr id='sosid_{$value->sosid}'><td>" . date('d-m-Y', strtotime($value->currentdate)) . "</td><td>{$value->currenttime}</td><td><a href='javascript:void(0)' onclick='setFocusFromNotificationListSos({$value->sosid})'>Locate</a></td><td>Resolved</td></tr>";
                } else {
                    $html .= "<tr style='background-color:#ffb9b9' id='sosid_{$value->sosid}'><td>" . date('d-m-Y', strtotime($value->currentdate)) . "</td><td>{$value->currenttime}</td><td><a href='javascript:void(0)' onclick='setFocusFromNotificationListSos({$value->sosid})'>Locate</a></td><td id='sostdid_{$value->sosid}'><a href='javascript:void(0)' onclick='resolveSos({$value->sosid},\"{$value->schema_name_}\")'>Resolve</a></td></tr>";
                }
            }
            $html .= '</tbody></table>';
            $result['sosdata'] = $html;
        }

        // Device Data
        $actionHtml = '<table class="table table-condensed"><tbody>';
        if (!empty($get_details)) {
            $address = $this->getAddressModified($get_details->latitude_, $get_details->longitude_, $get_details->divise_serial);
            $health_status = ($get_details->temperature <= 98.5) ? "Normal ({$get_details->temperature}° F)" : "Fever({$get_details->temperature}° F)";

            $html = '<table class="table table-condensed"><tbody>';
            $html .= "<tr><th>Device Id</th><td>{$get_details->divise_serial}</td></tr>";
            if ($get_details->temperature > 0) {
                $html .= "<tr><th>Health Status</th><td>{$health_status}</td></tr>";
            }
            $nearest_pole = '';
			$sql = "select name from {$this->schema}.master_polldata where ST_Contains(ST_Buffer(ST_Transform(ST_GeomFromText('POINT(".$get_details->longitude_." ".$get_details->latitude_.")',4326),26986),100,'quad_segs=8'),ST_Transform(geom,26986)) is true limit 1";
			$query = $this->db->query($sql);
			$row = $query->getRow();
			if(isset($row) && $row->name != '')
			{
				$nearest_pole = $nearest_pole.$row->name.' ';
			}
			$sql = "select name from {$this->schema}.master_kmpdata where ST_Contains(ST_Buffer(ST_Transform(ST_GeomFromText('POINT(".$get_details->longitude_." ".$get_details->latitude_.")',4326),26986),100,'quad_segs=8'),ST_Transform(geom,26986)) is true limit 1";
			$query = $this->db->query($sql);
			$row = $query->getRow();
			if(isset($row) && $row->name != '')
			{
				$nearest_pole = $nearest_pole.$row->name.' ';
			}
			$sql = "select name from {$this->schema}.master_stationdata where ST_Contains(ST_Buffer(ST_Transform(ST_GeomFromText('POINT(".$get_details->longitude_." ".$get_details->latitude_.")',4326),26986),100,'quad_segs=8'),ST_Transform(geom,26986)) is true limit 1";
			$query = $this->db->query($sql);
			$row = $query->getRow();
			if(isset($row) && $row->name != '')
			{
				$nearest_pole = $nearest_pole.$row->name.' ';
			}
            $html .= "<tr><th>Phone</th><td>{$get_details->mobile_no_}</td></tr>";
            $html .= "<tr><th>Address</th><td style='width:150px;'>{$address}</td></tr>";
            $html .= "<tr><th>Latitude</th><td>" . number_format($get_details->latitude_, 6, '.', '') . "</td></tr>";
            $html .= "<tr><th>Longitude</th><td>" . number_format($get_details->longitude_, 6, '.', '') . "</td></tr>";
            $html .= "<tr><th>Battery Status</th><td>{$get_details->batterystats_} %</td></tr>";
            $html .= "<tr><th>Speed</th><td>" . number_format($get_details->trakerspeed_, 3, '.', '') . " Km/hr</td></tr>";
            $html .= "<tr><th>Last Data Drawn</th><td>" . date("d-m-Y", strtotime($get_details->currentdate_)) . ' ' . $get_details->currenttime_ . "</td></tr>";
            $html .='<tr><th>Nearest Pole</th><td>' .$nearest_pole. '</td></tr>';
            $html .= "<tr><th>Source</th><td>{$get_details->sourcetype}</td></tr>";
            $html .= '</tbody></table>';
            $result['devicedata'] = $html;

            // Device Header
            $result['deviceheader'] = $get_details->divise_serial;
            if (strlen($result['deviceheader']) >= 25) {
                $result['deviceheader'] = substr($result['deviceheader'], 0, 20) . "...";
            }

            // Fetching assigned alerts
            $alert_assigned = $this->getAssignedAlerts($deviceid, $schemaname, $user_id);
            $alert_assigned_strings = '';
            if (!empty($alert_assigned)) {
                foreach ($alert_assigned as $value) {
                    $alert_assigned_strings .= $value->description . ', ';
                }
                $alert_assigned_strings = substr(trim($alert_assigned_strings), 0, -1);
            }

            $actionHtml .= "<tr><td><button class='btn btn-info actionbuttons-popup' title='Set Alert' onclick='alertmanagement({$get_details->deviceid})'><i class='fa fa-exclamation-triangle' aria-hidden='true'></i></button><span style='cursor:pointer' title='{$alert_assigned_strings}'>" . ((strlen($alert_assigned_strings) >= 30) ? substr($alert_assigned_strings, 0, 30) . "..." : $alert_assigned_strings) . "</span></td></tr>";
            $actionHtml .= "<tr><td><button class='btn btn-primary actionbuttons-popup' title='Choose Icon-set' onclick='changeIconSet({$get_details->deviceid})'><i class='fa fa-star' aria-hidden='true'></i></button></td></tr>";
        }

        $actionHtml .= '</tbody></table>';
        $result['actionhtmls'] = $actionHtml;

        // Returning JSON response
        return $this->response->setJSON(['status' => 1, 'result' => $result]);
    }

    // Method to call external API for reverse geocoding
    public function getAddressModified($lat, $lon, $serial_no)
    {
        // Prepare POST data
        $postData = [
            'lat' => $lat,
            'lon' => $lon,
            'device' => $serial_no,
            'database' => 'stesalit'
        ];

        // URL of the external API
        $url = 'http://120.138.8.188:7014/reversegeocode';

        // Get the HTTP client instance
        $client = \Config\Services::curlrequest();

        // Send the POST request with the data
        try {
            $response = $client->request('POST', $url, [
                'json' => $postData, // Automatically converts the array to JSON
            ]);

            // Check if the request was successful
            if ($response->getStatusCode() == 200) {
                return $response->getBody();  // Return the response body
            } else {
                return null;  // Handle failure or return null
            }
        } catch (\Exception $e) {
            // Handle exceptions, maybe log or rethrow
            log_message('error', 'Error in getAddressModified: ' . $e->getMessage());
            return null;
        }
    }

    // Method to get assigned alerts
    public function getAssignedAlerts($deviceid, $schemaname, $parent_id)
    {
        // Get the user ID from the device assignment table
        $query = $this->db->table($schemaname . '.' . 'device_asign_details')
                          ->select('current_user_id')
                          ->where('deviceid', $deviceid)
                          ->where('active', 1)
                          ->get();
        
        // Check if the query returned a result
        $user = $query->getRow();

        if ($user) {
            // If user exists, perform the join query to get assigned alerts
            $builder = $this->db->table($schemaname . '.' . 'master_device_alart_conf')
                                ->select('*')
                                ->join('public.master_alart', 'public.master_alart' . '.id = ' . $schemaname . '.master_device_alart_conf.alart_code', 'inner')
                                ->where([
                                    $schemaname . '.master_device_alart_conf.device_id' => $deviceid,
                                    $schemaname . '.master_device_alart_conf.user_id' => $user->current_user_id,
                                    'parent_id' => $parent_id,
                                    $schemaname . '.master_device_alart_conf.active' => 1
                                ]);
            
            $alerts = $builder->get()->getResult();

            return $alerts;
        } else {
            // Return an empty array if no user is found for the given device
            return [];
        }
    }

    public function getDeviceTodayCoordinates()
    {
        $return_arr = [];
        $from_date = date("Y-m-d") . ' 00:05:00';  
        $to_date = date("Y-m-d H:i:s");      
        
        $deviceid = $this->request->getPost('deviceid');
        if (!$deviceid) {
            return $this->response->setJSON(['error' => 'Device ID is required'])->setStatusCode(400);
        }

        if ($deviceid == '2745' || $deviceid == '2746') {
            $query = "SELECT * FROM public.get_positional_record_of_eatch_divice_date_to_date(?, ?, ?) 
                      ORDER BY currentdate, currenttime ASC";
        } else {
            $query = "SELECT * FROM public.get_positional_record_of_eatch_divice_date_to_date(?, ?, ?) 
                      ORDER BY currentdate, currenttime DESC LIMIT 1";
        }
        
        $getCoordinates = $this->db->query($query, [$deviceid, $from_date, $to_date])->getResult();
        
        if (!empty($getCoordinates)) {
            $return_arr = $getCoordinates;
        }
        
        $result = [
            'getcoordinates' => $return_arr,
            'getpoledata' => [],
            'getpolelinedata' => []
        ];

        echo json_encode($result); 
        exit;
    }

    public function getFollowLocation()
    {
        $final_data = [];
        $currentdate = date("Y-m-d");
        $deviceid = $this->request->getPost('deviceid');
        
        if ($deviceid) {
            $getDevicePosition = $this->controlCentreModel->getDevicePosition($deviceid, $currentdate, 1);
            if (!empty($getDevicePosition)) {
                $final_data[] = (array) $getDevicePosition;
            }
        }
        
        $devData = $this->controlCentreModel->getDevice($deviceid);
        
        echo json_encode([
            "status" => 1,
            "result" => $final_data,
            "dev_data" => $devData
        ]); 
        exit();
    }

    public function getAllDevicesUDept()
    {

        // Retrieve department key from POST request
        $deptKey = trim($this->request->getPost('deptkey'));

        // Construct the query
        $query = $this->db->query("SELECT sup_pid, sup_gid, serial_no, imei_no, did, mobile_no, warranty_date, linked, parent_id, 
                            user_id, issudate, refunddate, active, issold, apply_scheam, group_id, role_id, email, address, 
                            pincode, state_name, country, username, firstname, lastname, organisation, group_name, 
                            '' as list_item, '' as list_item_name 
                            FROM public.get_divice_details_record_for_list('{$this->schema}', ?) 
                            WHERE user_id = ? AND active = 1 ORDER BY did ASC", [$deptKey, $deptKey]);

        $deviceList = $query->getResult();

        $devices = '';
        $dids = '';

        foreach ($deviceList as $device) {
            if (empty($devices)) {
                $devices .= $device->did;
                $dids .= $device->did;
            } else {
                $devices .= "," . $device->did;
                $dids .= "," . $device->did;
            }
        }

        $result['data'] = $dids;

        // Return JSON response
        echo json_encode($result); 
        exit;
    }

}
