<?php

namespace App\Controllers;

use App\Models\UsersModel; // Adjust according to your model namespace
use App\Models\MobilesModel;
use App\Models\ControlCentreModel;
use App\Models\AccountModel;
use App\Models\CommonModel;
use CodeIgniter\Controller;

class Dashboard extends Controller {
    protected $sessdata;
    protected $schema;

    public function __construct() {
        // Load session service
        $session = session();
        $this->db = \Config\Database::connect();

        if ($session->has('login_sess_data')) {
            $this->sessdata = $session->get('login_sess_data');
            $this->schema = $this->sessdata['schemaname'];
        }

        // Load models
        $this->usersModel = new UsersModel();
        $this->mobilesModel = new MobilesModel();
        $this->controlCentreModel = new ControlCentreModel();
        $this->accountModel = new AccountModel();
        $this->commonModel = new CommonModel();

		helper('menu');
    }

    public function index() {

        // Check if the user is logged in
        if (!session()->has('login_sess_data')) {
            return redirect()->to('/');
        }

        $data = [];
        $data['sessdata'] = session()->get('login_sess_data');
        $data['page_title'] = "Dashboard";
        $current_date = date("Y-m-d");
        $schema = $this->schema; // Assuming this is defined in your controller
        $user_id = $this->sessdata['user_id'];
        $parent_id = $this->sessdata['parent_id'];
		$group_id = $this->sessdata['group_id'];

        
        // Fetch parent users
        $parent_users = $this->db->query("SELECT DISTINCT parent_id FROM public.get_right_panel_data('{$schema}', '{$current_date}', {$user_id}) WHERE group_id = 2 AND deviceid IS NOT NULL ORDER BY parent_id")->getResult();

        // Fetch online data
        $data_online = $this->db->query("SELECT lefttable.*, 
            (SELECT device_name FROM {$schema}.master_device_setup WHERE id=(SELECT max(id) FROM {$schema}.master_device_setup WHERE inserttime::date<=current_date::date AND deviceid=lefttable.deviceid)) AS device_name 
            FROM public.get_right_panel_data('{$schema}', '{$current_date}', {$user_id}) AS lefttable 
            WHERE lefttable.group_id = 2 AND lefttable.deviceid IS NOT NULL AND status_color IS NOT NULL")->getResult();

        // Initialize counters
        $data['online_mate'] = $data['online_usfd'] = $data['online_keyman'] = 0;
        $data['online_patrolman'] = $data['online_stock'] = $data['online_keyman_g'] = 0;
        $data['online_keyman_y'] = $data['online_patrolman_g'] = $data['online_patrolman_y'] = 0;
        $data['online_stock_g'] = $data['online_stock_y'] = $data['online_mate_g'] = $data['online_mate_y'] = 0;
        $data['online_others'] = $data['online_others_g'] = $data['online_others_y'] = 0;

        // Process online data
        foreach ($data_online as $data_online_each) {
            $deviceName = strtolower($data_online_each->device_name);
            if (strpos($deviceName, 'stock') !== false) {
                $data['online_stock']++;
                $data['online_stock_' . strtolower($data_online_each->status_color)]++;
            } elseif (strpos($deviceName, 'key') !== false) {
                $data['online_keyman']++;
                $data['online_keyman_' . strtolower($data_online_each->status_color)]++;
            } elseif (strpos($deviceName, 'patrol') !== false || strpos($deviceName, 'petrol') !== false) {
                $data['online_patrolman']++;
                $data['online_patrolman_' . strtolower($data_online_each->status_color)]++;
            } elseif (strpos($deviceName, 'mate') !== false) {
                $data['online_mate']++;
                $data['online_mate_' . strtolower($data_online_each->status_color)]++;
            } elseif (strpos($deviceName, 'others') !== false) {
                $data['online_others']++;
                $data['online_others_' . strtolower($data_online_each->status_color)]++;
            } elseif (strpos($deviceName, 'usfd') !== false) {
                $data['online_usfd']++;
            }
        }

        $data['online'] = $data['online_keyman'] + $data['online_patrolman'] + $data['online_mate'] + $data['online_usfd'] + $data['online_others'];
		$data_offline = $this->db->query("select lefttable.*, (SELECT device_name FROM {$schema}.master_device_setup  where id=(SELECT  max(id) FROM {$schema}.master_device_setup where inserttime::date<=current_date::date  and deviceid=lefttable.deviceid )) as device_name from  public.get_right_panel_data('{$schema}','{$current_date}',{$user_id}) as lefttable where lefttable.group_id = 2 and lefttable.deviceid IS NOT NULL and status_color IS NULL")->getResult();
		
		$data['offline_keyman'] = 0;
		$data['offline_patrolman'] = 0;
		$data['offline_mate'] = 0;
		$data['offline_stock'] = 0;
		$data['offline_usfd'] = 0;
		$data['offline_others'] = 0;
		
		foreach($data_offline as $data_offline_each){
			if (strpos(strtolower($data_offline_each->device_name), 'stock') !== false) {
				$data['offline_stock']++;
			}
			else if (strpos(strtolower($data_offline_each->device_name), 'key') !== false) {
				$data['offline_keyman']++;
			}
			else if (strpos(strtolower($data_offline_each->device_name), 'patrol') !== false) {
				$data['offline_patrolman']++;
			}
			else if (strpos(strtolower($data_offline_each->device_name), 'petrol') !== false) {
				$data['offline_patrolman']++;
			}
			else if (strpos(strtolower($data_offline_each->device_name), 'mate') !== false) {
				$data['offline_mate']++;
			}
			else if (strpos(strtolower($data_offline_each->device_name), 'usfd') !== false) {
				$data['offline_usfd']++;
			}
			else if (strpos(strtolower($data_offline_each->device_name), 'others') !== false) {
				$data['offline_others']++;
			}
		}
		
		$data['offline'] = $data['offline_keyman'] + $data['offline_patrolman'] + $data['offline_mate'] + $data['offline_usfd'] + $data['offline_others'];
		
		$data['stock'] = $data['online_stock'] + $data['offline_stock'];
		
		$data['call'] = 0;
		$data['sos'] = 0;
		
		foreach($parent_users as $parent_users_each){
			$data_call = $this->db->query("select count(*) as call from public.get_call_record_of_eatch_divice({$parent_users_each->parent_id},'{$schema}') where currentdate = '{$current_date}'")->getRow();
			
			$data['call'] = $data['call'] + $data_call->call;
			
			$data_sos = $this->db->query("select count(*) as sos from public.get_sos_record_of_eatch_divice({$parent_users_each->parent_id},'{$schema}') where currentdate = '{$current_date}'")->getRow();
			
			$data['sos'] = $data['sos'] + $data_sos->sos;
		}
		
		//*************** section wise onduty offduty ************//
		
		$sectiondata = $this->db->query("select lefttable.*, (SELECT device_name FROM {$schema}.master_device_setup  where id=(SELECT  max(id) FROM {$schema}.master_device_setup where inserttime::date<=current_date::date  and deviceid=lefttable.deviceid )) as device_name, organisation, firstname, lastname from  public.get_right_panel_data('{$schema}','{$current_date}',{$user_id}) as lefttable left join public.user_login as ul  on lefttable.user_id = ul.user_id where lefttable.group_id = 2 and lefttable.deviceid IS NOT NULL order by lastname, firstname asc")->getResult();

		$create_menu = array();
		foreach ($sectiondata as $key => $value) {
			$create_menu[$value->user_id][] = $value;
		}
		//echo '<pre>';print_r($create_menu);die();
        $details = $subdetails = $subdetails2 = $subdetails3 = [];
		$x=0;
		$onduty=$offduty=0;
		$ondutykeyman_g=$ondutykeyman_y=0;
		$offdutykeyman=0;
		$ondutypatrolman_g=$ondutypatrolman_y=0;
		$offdutypatrolman=0;
		$ondutystock=$offdutystock=0;
		$ondutymate=$offdutymate=0;
		$offdutyothers=0;
		$ondutyothers_y=$ondutyothers_g=0;
		//echo "<pre>";print_r($sectiondata);echo "</pre>";exit;
		foreach ($create_menu as $key => $value) {
			$details[$x]['organisation'] = $value[0]->organisation;
			foreach ($value as $k => $v) {
				if (!empty($v->status_color)) { 
					if (strpos(strtolower($v->device_name), 'key') !== false) {
						if($v->status_color == 'G'){
							$ondutykeyman_g++;
						}
						else {
							$ondutykeyman_y++;
						}
					}
					else if (strpos(strtolower($v->device_name), 'patrol') !== false) {
						if($v->status_color == 'G'){
							$ondutypatrolman_g++;
						}
						else {
							$ondutypatrolman_y++;
						}
					}
					else if (strpos(strtolower($v->device_name), 'petrol') !== false) {
						if($v->status_color == 'G'){
							$ondutypatrolman_g++;
						}
						else {
							$ondutypatrolman_y++;
						}
					}
					else if (strpos(strtolower($v->device_name), 'others') !== false) {
						if($v->status_color == 'G'){
							$ondutyothers_g++;
						}
						else {
							$ondutyothers_y++;
						}
					}					
					else if (strpos(strtolower($v->device_name), 'stock') !== false) {						
						$ondutystock++;
					}
					else if (strpos(strtolower($v->device_name), 'mate') !== false) {
						$ondutymate++;
					}
					$onduty++;
				}
				else {
					if (strpos(strtolower($v->device_name), 'key') !== false) {
						$offdutykeyman++;
					}
					else if (strpos(strtolower($v->device_name), 'patrol') !== false) {
						$offdutypatrolman++;
					}
					else if (strpos(strtolower($v->device_name), 'petrol') !== false) {
						$offdutypatrolman++;
					}
					else if (strpos(strtolower($v->device_name), 'stock') !== false) {						
						$offdutystock++;
					}
					else if (strpos(strtolower($v->device_name), 'mate') !== false) {
						$offdutymate++;
					}
					else if (strpos(strtolower($v->device_name), 'others') !== false) {
						$offdutyothers++;
					}
					$offduty++;
				}
			}
			$details[$x]['onduty'] = $onduty;
			$details[$x]['offduty'] = $offduty;
			$details[$x]['ondutykeyman_g'] = $ondutykeyman_g;
			$details[$x]['ondutykeyman_y'] = $ondutykeyman_y;
			$details[$x]['ondutypatrolman_g'] = $ondutypatrolman_g;
			$details[$x]['ondutypatrolman_y'] = $ondutypatrolman_y;
			$details[$x]['offdutykeyman'] = $offdutykeyman;
			$details[$x]['offdutypatrolman'] = $offdutypatrolman;
			$details[$x]['ondutystock'] = $ondutystock;
			$details[$x]['offdutystock'] = $offdutystock;
			$details[$x]['ondutymate'] = $ondutymate;
			$details[$x]['offdutymate'] = $offdutymate;
			$details[$x]['ondutyothers_g'] = $ondutyothers_g;
			$details[$x]['ondutyothers_y'] = $ondutyothers_y;
			$details[$x]['offdutyothers'] = $offdutyothers;
			$details[$x]['user_id'] = $value[0]->user_id;
			$x++;
			$onduty=$offduty=0;
			$ondutykeyman_g=$ondutykeyman_y=0;
			$offdutykeyman=0;
			$ondutypatrolman_g=$ondutypatrolman_y=0;
			$offdutypatrolman=0;
			$ondutystock=$offdutystock=0;
			$ondutymate=$offdutymate=0;
			$offdutyothers=0;
			$ondutyothers_y=$ondutyothers_g=0;
		}
		$data['sectiondetails'] = $details;
		//************* end ****************//
		//************* sub level green / yellow count start ****************//
		if($group_id == 3){
			// Sr DEN level green / yellow count
			$subleveldata = $this->db->query("select lefttable.*, organisation, firstname, lastname, (SELECT device_name FROM {$schema}.master_device_setup  where id=(SELECT  max(id) FROM {$schema}.master_device_setup where inserttime::date<=current_date::date  and deviceid=lefttable.deviceid )) as device_name from  public.get_right_panel_data_updated_blackbox('{$schema}','{$current_date}',{$user_id}) as lefttable left join public.user_login as ul  on lefttable.user_id = ul.user_id where lefttable.group_id = 4 and lefttable.deviceid IS NOT NULL order by lastname, firstname asc")->getResult();
			
			$subcreate_menu = array();
			foreach ($subleveldata as $key => $value) {
				$subcreate_menu[$value->user_id][] = $value;
			}
			//echo '<pre>';print_r($subcreate_menu);die();
			$x=0;
			$sub_keyman_g=$sub_keyman_y=0;
			$sub_patrolman_g=$sub_patrolman_y=0;
			$sub_mate_g=$sub_mate_y=0;
			$sub_others_g=$sub_others_y=0;
			
			$sub_offduty=0;
			$sub_offdutystock=0;
			$sub_offdutymate=0;
			$sub_offdutyothers=0;
			$sub_offdutypatrolman=0;
			$sub_offdutykeyman=0;

			foreach ($subcreate_menu as $key => $value) {
				$subdetails[$x]['organisation'] = $value[0]->organisation;
				foreach ($value as $k => $v) {
					if (!empty($v->status_color)) { 						
						if($v->status_color == 'G'){
							if (strpos(strtolower($v->device_name), 'key') !== false) {
								$sub_keyman_g++;
							}
							else if (strpos(strtolower($v->device_name), 'patrol') !== false) {
								$sub_patrolman_g++;
							}
							else if (strpos(strtolower($v->device_name), 'petrol') !== false) {
								$sub_patrolman_g++;
							}
							else if (strpos(strtolower($v->device_name), 'mate') !== false) {
								$sub_mate_g++;
							}
							else if (strpos(strtolower($v->device_name), 'others') !== false) {
								$sub_others_g++;
							}
						}
						else {
							if (strpos(strtolower($v->device_name), 'key') !== false) {
								$sub_keyman_y++;
							}
							else if (strpos(strtolower($v->device_name), 'patrol') !== false) {
								$sub_patrolman_y++;
							}
							else if (strpos(strtolower($v->device_name), 'petrol') !== false) {
								$sub_patrolman_y++;
							}
							else if (strpos(strtolower($v->device_name), 'mate') !== false) {
								$sub_mate_y++;
							}
							else if (strpos(strtolower($v->device_name), 'others') !== false) {
								$sub_others_y++;
							}
						}
					}
					else
					{
						if (strpos(strtolower($v->device_name), 'key') !== false) {
							$sub_offdutykeyman++;
						}
						else if (strpos(strtolower($v->device_name), 'patrol') !== false) {
							$sub_offdutypatrolman++;
						}
						else if (strpos(strtolower($v->device_name), 'stock') !== false) {						
							$sub_offdutystock++;
						}
						else if (strpos(strtolower($v->device_name), 'mate') !== false) {
							$sub_offdutymate++;
						}
						else if (strpos(strtolower($v->device_name), 'others') !== false) {
							$sub_offdutyothers++;
						}
						$sub_offduty++;
					}
				}
				$subdetails[$x]['sub_keyman_g'] = $sub_keyman_g;
				$subdetails[$x]['sub_keyman_y'] = $sub_keyman_y;
				$subdetails[$x]['sub_patrolman_g'] = $sub_patrolman_g;
				$subdetails[$x]['sub_patrolman_y'] = $sub_patrolman_y;
				$subdetails[$x]['sub_mate_g'] = $sub_mate_g;
				$subdetails[$x]['sub_mate_y'] = $sub_mate_y;
				$subdetails[$x]['sub_others_g'] = $sub_others_g;
				$subdetails[$x]['sub_others_y'] = $sub_others_y;
				
				$subdetails[$x]['sub_offduty'] = $sub_offduty;
				$subdetails[$x]['sub_offdutykeyman'] = $sub_offdutykeyman;
				$subdetails[$x]['sub_offdutypatrolman'] = $sub_offdutypatrolman;
				$subdetails[$x]['sub_offdutystock'] = $sub_offdutystock;
				$subdetails[$x]['sub_offdutymate'] = $sub_offdutymate;
				$subdetails[$x]['sub_offdutyothers'] = $sub_offdutyothers;
				$subdetails[$x]['user_id'] = $value[0]->user_id;
				$x++;
				$sub_keyman_g=0;
				$sub_keyman_y=0;
				$sub_patrolman_g=0;
				$sub_patrolman_y=0;
				$sub_mate_g=0;
				$sub_mate_y=0;
				$sub_others_g=0;
			    $sub_others_y=0;
				
				$sub_offduty=0;
				$sub_offdutystock=0;
				$sub_offdutymate=0;
				$sub_offdutyothers=0;
				$sub_offdutypatrolman=0;
				$sub_offdutykeyman=0;
			}
			$data['subleveldetails'] = $subdetails;

			// echo "<pre>";print_r($subdetails);exit();
			
			// Sr DEN level green / yellow count
			$subleveldata2 = $this->db->query("select lefttable.*, organisation, firstname, lastname, (SELECT device_name FROM {$this->schema}.master_device_setup  where id=(SELECT  max(id) FROM {$this->schema}.master_device_setup where inserttime::date<=current_date::date and deviceid=lefttable.deviceid )) as device_name from public.get_right_panel_data_updated_blackbox('{$this->schema}','{$current_date}',{$user_id}) as lefttable left join public.user_login as ul  on lefttable.user_id = ul.user_id where lefttable.group_id = 5 and lefttable.deviceid IS NOT NULL order by lastname, firstname asc")->getResult();
			// echo $schema."<pre>";print_r($subleveldata2);echo "</pre>";exit;
			$subcreate_menu2 = array();
			foreach ($subleveldata2 as $key => $value) {
				$subcreate_menu2[$value->user_id][] = $value;
			}
			//echo '<pre>';print_r($subcreate_menu2);die();
			$x=0;
			$sub_keyman_g=$sub_keyman_y=0;
			$sub_patrolman_g=$sub_patrolman_y=0;
			$sub_mate_g=$sub_mate_y=0;
			$sub_others_g=$sub_others_y=0;
			
			$sub_offduty=0;
			$sub_offdutystock=0;
			$sub_offdutymate=0;
			$sub_offdutyothers=0;
			$sub_offdutypatrolman=0;
			$sub_offdutykeyman=0;

			foreach ($subcreate_menu2 as $key => $value) {
				$subdetails2[$x]['organisation'] = $value[0]->organisation;
				foreach ($value as $k => $v) {
					if (!empty($v->status_color)) { 						
						if($v->status_color == 'G'){
							if (strpos(strtolower($v->device_name), 'key') !== false) {
								$sub_keyman_g++;
							}
							else if (strpos(strtolower($v->device_name), 'patrol') !== false) {
								$sub_patrolman_g++;
							}
							else if (strpos(strtolower($v->device_name), 'petrol') !== false) {
								$sub_patrolman_g++;
							}
							else if (strpos(strtolower($v->device_name), 'mate') !== false) {
								$sub_mate_g++;
							}
							else if (strpos(strtolower($v->device_name), 'others') !== false) {
								$sub_others_g++;
							}
						}
						else {
							if (strpos(strtolower($v->device_name), 'key') !== false) {
								$sub_keyman_y++;
							}
							else if (strpos(strtolower($v->device_name), 'patrol') !== false) {
								$sub_patrolman_y++;
							}
							else if (strpos(strtolower($v->device_name), 'petrol') !== false) {
								$sub_patrolman_y++;
							}
							else if (strpos(strtolower($v->device_name), 'mate') !== false) {
								$sub_mate_y++;
							}
							else if (strpos(strtolower($v->device_name), 'others') !== false) {
								$sub_others_y++;
							}
						}
					}
					else
					{
						if (strpos(strtolower($v->device_name), 'key') !== false) {
							$sub_offdutykeyman++;
						}
						else if (strpos(strtolower($v->device_name), 'patrol') !== false) {
							$sub_offdutypatrolman++;
						}
						else if (strpos(strtolower($v->device_name), 'stock') !== false) {						
							$sub_offdutystock++;
						}
						else if (strpos(strtolower($v->device_name), 'mate') !== false) {
							$sub_offdutymate++;
						}
						else if (strpos(strtolower($v->device_name), 'others') !== false) {
							$sub_offdutyothers++;
						}
						$sub_offduty++;
					}
				}
				$subdetails2[$x]['sub_keyman_g'] = $sub_keyman_g;
				$subdetails2[$x]['sub_keyman_y'] = $sub_keyman_y;
				$subdetails2[$x]['sub_patrolman_g'] = $sub_patrolman_g;
				$subdetails2[$x]['sub_patrolman_y'] = $sub_patrolman_y;
				$subdetails2[$x]['sub_mate_g'] = $sub_mate_g;
				$subdetails2[$x]['sub_mate_y'] = $sub_mate_y;
				$subdetails2[$x]['sub_others_g'] = $sub_others_g;
				$subdetails2[$x]['sub_others_y'] = $sub_others_y;
				
				$subdetails2[$x]['sub_offduty'] = $sub_offduty;
				$subdetails2[$x]['sub_offdutykeyman'] = $sub_offdutykeyman;
				$subdetails2[$x]['sub_offdutypatrolman'] = $sub_offdutypatrolman;
				$subdetails2[$x]['sub_offdutystock'] = $sub_offdutystock;
				$subdetails2[$x]['sub_offdutymate'] = $sub_offdutymate;
				$subdetails2[$x]['sub_offdutyothers'] = $sub_offdutyothers;
				$subdetails2[$x]['user_id'] = $value[0]->user_id;
				$x++;
				$sub_keyman_g=0;
				$sub_keyman_y=0;
				$sub_patrolman_g=0;
				$sub_patrolman_y=0;
				$sub_mate_g=0;
				$sub_mate_y=0;
				$sub_others_g=0;
			    $sub_others_y=0;
				
				$sub_offduty=0;
				$sub_offdutystock=0;
				$sub_offdutymate=0;
				$sub_offdutyothers=0;
				$sub_offdutypatrolman=0;
				$sub_offdutykeyman=0;
			}
			$data['subleveldetails2'] = $subdetails2;
			//echo "<pre>";print_r($subdetails2);echo "</pre>";exit;
			
			
			$subleveldata3 = $this->db->query("select lefttable.*, organisation, firstname, lastname, (SELECT device_name FROM {$schema}.master_device_setup  where id=(SELECT  max(id) FROM {$schema}.master_device_setup where inserttime::date<=current_date::date  and deviceid=lefttable.deviceid )) as device_name from  public.get_right_panel_data_updated_blackbox('{$schema}','{$current_date}',{$user_id}) as lefttable left join public.user_login as ul  on lefttable.user_id = ul.user_id where lefttable.group_id = 8 and lefttable.deviceid IS NOT NULL order by lastname, firstname asc")->getResult();
			// echo "<pre>";print_r($subleveldata3);echo "</pre>";exit;
			$subcreate_menu3 = array();
			foreach ($subleveldata3 as $key => $value) {
				$subcreate_menu3[$value->user_id][] = $value;
			}
			//echo '<pre>';print_r($subcreate_menu2);die();
			$x=0;
			$sub_keyman_g=0;
			$sub_keyman_y=0;
			$sub_patrolman_g=0;
			$sub_patrolman_y=0;
			$sub_mate_g=0;
			$sub_mate_y=0;
			$sub_others_g=0;
			$sub_others_y=0;
			
			$sub_offduty=0;
			$sub_offdutystock=0;
			$sub_offdutymate=0;
			$sub_offdutyothers=0;
			$sub_offdutypatrolman=0;
			$sub_offdutykeyman=0;

			foreach ($subcreate_menu3 as $key => $value) {
				$subdetails3[$x]['organisation'] = $value[0]->organisation;
				foreach ($value as $k => $v) {
					if (!empty($v->status_color)) { 						
						if($v->status_color == 'G'){
							if (strpos(strtolower($v->device_name), 'key') !== false) {
								$sub_keyman_g++;
							}
							else if (strpos(strtolower($v->device_name), 'patrol') !== false) {
								$sub_patrolman_g++;
							}
							else if (strpos(strtolower($v->device_name), 'petrol') !== false) {
								$sub_patrolman_g++;
							}
							else if (strpos(strtolower($v->device_name), 'mate') !== false) {
								$sub_mate_g++;
							}
							else if (strpos(strtolower($v->device_name), 'others') !== false) {
								$sub_others_g++;
							}
						}
						else {
							if (strpos(strtolower($v->device_name), 'key') !== false) {
								$sub_keyman_y++;
							}
							else if (strpos(strtolower($v->device_name), 'patrol') !== false) {
								$sub_patrolman_y++;
							}
							else if (strpos(strtolower($v->device_name), 'petrol') !== false) {
								$sub_patrolman_y++;
							}
							else if (strpos(strtolower($v->device_name), 'mate') !== false) {
								$sub_mate_y++;
							}
							else if (strpos(strtolower($v->device_name), 'others') !== false) {
								$sub_others_y++;
							}
						}
					}
					else
					{
						if (strpos(strtolower($v->device_name), 'key') !== false) {
							$sub_offdutykeyman++;
						}
						else if (strpos(strtolower($v->device_name), 'patrol') !== false) {
							$sub_offdutypatrolman++;
						}
						else if (strpos(strtolower($v->device_name), 'stock') !== false) {						
							$sub_offdutystock++;
						}
						else if (strpos(strtolower($v->device_name), 'mate') !== false) {
							$sub_offdutymate++;
						}
						else if (strpos(strtolower($v->device_name), 'others') !== false) {
							$sub_offdutyothers++;
						}
						$sub_offduty++;
					}
				}
				$subdetails3[$x]['sub_keyman_g'] = $sub_keyman_g;
				$subdetails3[$x]['sub_keyman_y'] = $sub_keyman_y;
				$subdetails3[$x]['sub_patrolman_g'] = $sub_patrolman_g;
				$subdetails3[$x]['sub_patrolman_y'] = $sub_patrolman_y;
				$subdetails3[$x]['sub_mate_g'] = $sub_mate_g;
				$subdetails3[$x]['sub_mate_y'] = $sub_mate_y;
				$subdetails3[$x]['sub_others_g'] = $sub_others_g;
				$subdetails3[$x]['sub_others_y'] = $sub_others_y;
				
				$subdetails3[$x]['sub_offduty'] = $sub_offduty;
				$subdetails3[$x]['sub_offdutykeyman'] = $sub_offdutykeyman;
				$subdetails3[$x]['sub_offdutypatrolman'] = $sub_offdutypatrolman;
				$subdetails3[$x]['sub_offdutystock'] = $sub_offdutystock;
				$subdetails3[$x]['sub_offdutymate'] = $sub_offdutymate;
				$subdetails3[$x]['sub_offdutyothers'] = $sub_offdutyothers;
				$subdetails3[$x]['user_id'] = $value[0]->user_id;
				$x++;
				$sub_keyman_g=0;
				$sub_keyman_y=0;
				$sub_patrolman_g=0;
				$sub_patrolman_y=0;
				$sub_mate_g=0;
				$sub_mate_y=0;
				$sub_others_g=0;
			    $sub_others_y=0;
				
				$sub_offduty=0;
				$sub_offdutystock=0;
				$sub_offdutymate=0;
				$sub_offdutyothers=0;
				$sub_offdutypatrolman=0;
				$sub_offdutykeyman=0;
			}
			$data['subleveldetails3'] = $subdetails3;
			
			
			
		}
		else if($group_id == 4){
			$subleveldata = $this->db->query("select lefttable.*, organisation, firstname, lastname, (SELECT device_name FROM {$schema}.master_device_setup  where id=(SELECT  max(id) FROM {$schema}.master_device_setup where inserttime::date<=current_date::date  and deviceid=lefttable.deviceid )) as device_name from  public.get_right_panel_data_updated_blackbox('{$schema}','{$current_date}',{$user_id}) as lefttable left join public.user_login as ul  on lefttable.user_id = ul.user_id where lefttable.group_id = 5 and lefttable.deviceid IS NOT NULL order by lastname, firstname asc")->getResult();
			
			$subcreate_menu = array();
			foreach ($subleveldata as $key => $value) {
				$subcreate_menu[$value->user_id][] = $value;
			}
			//echo '<pre>';print_r($subcreate_menu);die();
			$x=0;
			$sub_keyman_g=0;
			$sub_keyman_y=0;
			$sub_patrolman_g=0;
			$sub_patrolman_y=0;
			$sub_mate_g=0;
			$sub_mate_y=0;
			$sub_others_g=0;
			$sub_others_y=0;
			
			$sub_offduty=0;
			$sub_offdutystock=0;
			$sub_offdutymate=0;
			$sub_offdutyothers=0;
			$sub_offdutypatrolman=0;
			$sub_offdutykeyman=0;

			foreach ($subcreate_menu as $key => $value) {
				$subdetails[$x]['organisation'] = $value[0]->organisation;
				foreach ($value as $k => $v) {
					if (!empty($v->status_color)) { 						
						if($v->status_color == 'G'){
							if (strpos(strtolower($v->device_name), 'key') !== false) {
								$sub_keyman_g++;
							}
							else if (strpos(strtolower($v->device_name), 'patrol') !== false) {
								$sub_patrolman_g++;
							}
							else if (strpos(strtolower($v->device_name), 'petrol') !== false) {
								$sub_patrolman_g++;
							}
							else if (strpos(strtolower($v->device_name), 'mate') !== false) {
								$sub_mate_g++;
							}
							else if (strpos(strtolower($v->device_name), 'others') !== false) {
								$sub_others_g++;
							}
						}
						else {
							if (strpos(strtolower($v->device_name), 'key') !== false) {
								$sub_keyman_y++;
							}
							else if (strpos(strtolower($v->device_name), 'patrol') !== false) {
								$sub_patrolman_y++;
							}
							else if (strpos(strtolower($v->device_name), 'petrol') !== false) {
								$sub_patrolman_y++;
							}
							else if (strpos(strtolower($v->device_name), 'mate') !== false) {
								$sub_mate_y++;
							}
							else if (strpos(strtolower($v->device_name), 'others') !== false) {
								$sub_others_y++;
							}
						}
					}
					else
					{
						if (strpos(strtolower($v->device_name), 'key') !== false) {
							$sub_offdutykeyman++;
						}
						else if (strpos(strtolower($v->device_name), 'patrol') !== false) {
							$sub_offdutypatrolman++;
						}
						else if (strpos(strtolower($v->device_name), 'stock') !== false) {						
							$sub_offdutystock++;
						}
						else if (strpos(strtolower($v->device_name), 'mate') !== false) {
							$sub_offdutymate++;
						}
						else if (strpos(strtolower($v->device_name), 'others') !== false) {
							$sub_offdutyothers++;
						}
						$sub_offduty++;
					}
				}
				$subdetails[$x]['sub_keyman_g'] = $sub_keyman_g;
				$subdetails[$x]['sub_keyman_y'] = $sub_keyman_y;
				$subdetails[$x]['sub_patrolman_g'] = $sub_patrolman_g;
				$subdetails[$x]['sub_patrolman_y'] = $sub_patrolman_y;
				$subdetails[$x]['sub_mate_g'] = $sub_mate_g;
				$subdetails[$x]['sub_mate_y'] = $sub_mate_y;
				$subdetails[$x]['sub_others_g'] = $sub_others_g;
				$subdetails[$x]['sub_others_y'] = $sub_others_y;
				
				$subdetails[$x]['sub_offduty'] = $sub_offduty;
				$subdetails[$x]['sub_offdutykeyman'] = $sub_offdutykeyman;
				$subdetails[$x]['sub_offdutypatrolman'] = $sub_offdutypatrolman;
				$subdetails[$x]['sub_offdutystock'] = $sub_offdutystock;
				$subdetails[$x]['sub_offdutymate'] = $sub_offdutymate;
				$subdetails[$x]['sub_offdutyothers'] = $sub_offdutyothers;
				$subdetails[$x]['user_id'] = $value[0]->user_id;
				$x++;
				$sub_keyman_g=0;
				$sub_keyman_y=0;
				$sub_patrolman_g=0;
				$sub_patrolman_y=0;
				$sub_mate_g=0;
				$sub_mate_y=0;
				$sub_others_g=0;
			    $sub_others_y=0;
				
				$sub_offduty=0;
				$sub_offdutystock=0;
				$sub_offdutymate=0;
				$sub_offdutyothers=0;
				$sub_offdutypatrolman=0;
				$sub_offdutykeyman=0;
			}
			$data['subleveldetails'] = $subdetails;
			
			$subleveldata2 = $this->db->query("select lefttable.*, organisation, firstname, lastname, (SELECT device_name FROM {$schema}.master_device_setup  where id=(SELECT  max(id) FROM {$schema}.master_device_setup where inserttime::date<=current_date::date  and deviceid=lefttable.deviceid )) as device_name from  public.get_right_panel_data_updated_blackbox('{$schema}','{$current_date}',{$user_id}) as lefttable left join public.user_login as ul  on lefttable.user_id = ul.user_id where lefttable.group_id = 8 and lefttable.deviceid IS NOT NULL order by lastname, firstname asc")->getResult();
			
			$subcreate_menu2 = array();
			foreach ($subleveldata2 as $key => $value) {
				$subcreate_menu2[$value->user_id][] = $value;
			}
			//echo '<pre>';print_r($subcreate_menu2);die();
			$x=0;
			$sub_keyman_g=0;
			$sub_keyman_y=0;
			$sub_patrolman_g=0;
			$sub_patrolman_y=0;
			$sub_mate_g=0;
			$sub_mate_y=0;
			$sub_others_g=0;
			$sub_others_y=0;
			
			$sub_offduty=0;
			$sub_offdutystock=0;
			$sub_offdutymate=0;
			$sub_offdutyothers=0;
			$sub_offdutypatrolman=0;
			$sub_offdutykeyman=0;

			foreach ($subcreate_menu2 as $key => $value) {
				$subdetails2[$x]['organisation'] = $value[0]->organisation;
				foreach ($value as $k => $v) {
					if (!empty($v->status_color)) { 						
						if($v->status_color == 'G'){
							if (strpos(strtolower($v->device_name), 'key') !== false) {
								$sub_keyman_g++;
							}
							else if (strpos(strtolower($v->device_name), 'patrol') !== false) {
								$sub_patrolman_g++;
							}
							else if (strpos(strtolower($v->device_name), 'petrol') !== false) {
								$sub_patrolman_g++;
							}
							else if (strpos(strtolower($v->device_name), 'mate') !== false) {
								$sub_mate_g++;
							}
							else if (strpos(strtolower($v->device_name), 'others') !== false) {
								$sub_others_g++;
							}
						}
						else {
							if (strpos(strtolower($v->device_name), 'key') !== false) {
								$sub_keyman_y++;
							}
							else if (strpos(strtolower($v->device_name), 'patrol') !== false) {
								$sub_patrolman_y++;
							}
							else if (strpos(strtolower($v->device_name), 'petrol') !== false) {
								$sub_patrolman_y++;
							}
							else if (strpos(strtolower($v->device_name), 'mate') !== false) {
								$sub_mate_y++;
							}
							else if (strpos(strtolower($v->device_name), 'others') !== false) {
								$sub_others_y++;
							}
						}
					}
					else
					{
						if (strpos(strtolower($v->device_name), 'key') !== false) {
							$sub_offdutykeyman++;
						}
						else if (strpos(strtolower($v->device_name), 'patrol') !== false) {
							$sub_offdutypatrolman++;
						}
						else if (strpos(strtolower($v->device_name), 'stock') !== false) {						
							$sub_offdutystock++;
						}
						else if (strpos(strtolower($v->device_name), 'mate') !== false) {
							$sub_offdutymate++;
						}
						else if (strpos(strtolower($v->device_name), 'others') !== false) {
							$sub_offdutyothers++;
						}
						$sub_offduty++;
					}
				}
				$subdetails2[$x]['sub_keyman_g'] = $sub_keyman_g;
				$subdetails2[$x]['sub_keyman_y'] = $sub_keyman_y;
				$subdetails2[$x]['sub_patrolman_g'] = $sub_patrolman_g;
				$subdetails2[$x]['sub_patrolman_y'] = $sub_patrolman_y;
				$subdetails2[$x]['sub_mate_g'] = $sub_mate_g;
				$subdetails2[$x]['sub_mate_y'] = $sub_mate_y;
				$subdetails2[$x]['sub_others_g'] = $sub_others_g;
				$subdetails2[$x]['sub_others_y'] = $sub_others_y;
				
				$subdetails2[$x]['sub_offduty'] = $sub_offduty;
				$subdetails2[$x]['sub_offdutykeyman'] = $sub_offdutykeyman;
				$subdetails2[$x]['sub_offdutypatrolman'] = $sub_offdutypatrolman;
				$subdetails2[$x]['sub_offdutystock'] = $sub_offdutystock;
				$subdetails2[$x]['sub_offdutymate'] = $sub_offdutymate;
				$subdetails2[$x]['sub_offdutyothers'] = $sub_offdutyothers;
				$subdetails2[$x]['user_id'] = $value[0]->user_id;
				$x++;
				$sub_keyman_g=0;
				$sub_keyman_y=0;
				$sub_patrolman_g=0;
				$sub_patrolman_y=0;
				$sub_mate_g=0;
				$sub_mate_y=0;
				$sub_others_g=0;
			    $sub_others_y=0;
				
				$sub_offduty=0;
				$sub_offdutystock=0;
				$sub_offdutymate=0;
				$sub_offdutyothers=0;
				$sub_offdutypatrolman=0;
				$sub_offdutykeyman=0;
			}
			$data['subleveldetails2'] = $subdetails2;
			
		}
		else if($group_id == 5){
			$subleveldata = $this->db->query("select lefttable.*, organisation, firstname, lastname, (SELECT device_name FROM {$schema}.master_device_setup  where id=(SELECT  max(id) FROM {$schema}.master_device_setup where inserttime::date<=current_date::date  and deviceid=lefttable.deviceid )) as device_name from  public.get_right_panel_data_updated_blackbox('{$schema}','{$current_date}',{$user_id}) as lefttable left join public.user_login as ul  on lefttable.user_id = ul.user_id where lefttable.group_id = 8 and lefttable.deviceid IS NOT NULL order by lastname, firstname asc")->getResult();
			
			$subcreate_menu = array();
			foreach ($subleveldata as $key => $value) {
				$subcreate_menu[$value->user_id][] = $value;
			}
			//echo '<pre>';print_r($subcreate_menu);die();
			$x=0;
			$sub_keyman_g=0;
			$sub_keyman_y=0;
			$sub_patrolman_g=0;
			$sub_patrolman_y=0;
			$sub_mate_g=0;
			$sub_mate_y=0;
			$sub_others_g=0;
			$sub_others_y=0;

			foreach ($subcreate_menu as $key => $value) {
				$subdetails[$x]['organisation'] = $value[0]->organisation;
				foreach ($value as $k => $v) {
					if (!empty($v->status_color)) { 						
						if($v->status_color == 'G'){
							if (strpos(strtolower($v->device_name), 'key') !== false) {
								$sub_keyman_g++;
							}
							else if (strpos(strtolower($v->device_name), 'patrol') !== false) {
								$sub_patrolman_g++;
							}
							else if (strpos(strtolower($v->device_name), 'petrol') !== false) {
								$sub_patrolman_g++;
							}
							else if (strpos(strtolower($v->device_name), 'mate') !== false) {
								$sub_mate_g++;
							}
							else if (strpos(strtolower($v->device_name), 'others') !== false) {
								$sub_others_g++;
							}
						}
						else {
							if (strpos(strtolower($v->device_name), 'key') !== false) {
								$sub_keyman_y++;
							}
							else if (strpos(strtolower($v->device_name), 'patrol') !== false) {
								$sub_patrolman_y++;
							}
							else if (strpos(strtolower($v->device_name), 'petrol') !== false) {
								$sub_patrolman_y++;
							}
							else if (strpos(strtolower($v->device_name), 'mate') !== false) {
								$sub_mate_y++;
							}
							else if (strpos(strtolower($v->device_name), 'others') !== false) {
								$sub_others_y++;
							}
						}
					}
				}
				$subdetails[$x]['sub_keyman_g'] = $sub_keyman_g;
				$subdetails[$x]['sub_keyman_y'] = $sub_keyman_y;
				$subdetails[$x]['sub_patrolman_g'] = $sub_patrolman_g;
				$subdetails[$x]['sub_patrolman_y'] = $sub_patrolman_y;
				$subdetails[$x]['sub_mate_g'] = $sub_mate_g;
				$subdetails[$x]['sub_mate_y'] = $sub_mate_y;
				$subdetails[$x]['sub_others_g'] = $sub_others_g;
				$subdetails[$x]['sub_others_y'] = $sub_others_y;
				$subdetails[$x]['user_id'] = $value[0]->user_id;
				$x++;
				$sub_keyman_g=0;
				$sub_keyman_y=0;
				$sub_patrolman_g=0;
				$sub_patrolman_y=0;
				$sub_mate_g=0;
				$sub_mate_y=0;
				$sub_others_g=0;
		    	$sub_others_y=0;
			}
			$data['subleveldetails'] = $subdetails;
		}
		else if($group_id == 8){
			$subleveldata = $this->db->query("select lefttable.*, organisation, firstname, lastname, (SELECT device_name FROM {$schema}.master_device_setup  where id=(SELECT  max(id) FROM {$schema}.master_device_setup where inserttime::date<=current_date::date  and deviceid=lefttable.deviceid )) as device_name from  public.get_right_panel_data_updated_blackbox('{$schema}','{$current_date}',{$user_id}) as lefttable left join public.user_login as ul  on lefttable.user_id = ul.user_id where lefttable.group_id = 2 and lefttable.deviceid IS NOT NULL order by lastname, firstname asc")->getResult();
			
			$subcreate_menu = array();
			foreach ($subleveldata as $key => $value) {
				$subcreate_menu[$value->user_id][] = $value;
			}
			// echo '<pre>';print_r($subcreate_menu);die();
			$x=0;
			$sub_keyman_g=0;
			$sub_keyman_y=0;
			$sub_patrolman_g=0;
			$sub_patrolman_y=0;
			$sub_mate_g=0;
			$sub_mate_y=0;
			$sub_others_g=0;
			$sub_others_y=0;

			foreach ($subcreate_menu as $key => $value) {
				$subdetails[$x]['organisation'] = $value[0]->organisation;
				foreach ($value as $k => $v) {
					if (!empty($v->status_color)) { 						
						if($v->status_color == 'G'){
							if (strpos(strtolower($v->device_name), 'key') !== false) {
								$sub_keyman_g++;
							}
							else if (strpos(strtolower($v->device_name), 'patrol') !== false) {
								$sub_patrolman_g++;
							}
							else if (strpos(strtolower($v->device_name), 'petrol') !== false) {
								$sub_patrolman_g++;
							}
							else if (strpos(strtolower($v->device_name), 'mate') !== false) {
								$sub_mate_g++;
							}
							else if (strpos(strtolower($v->device_name), 'others') !== false) {
								$sub_others_g++;
							}
						}
						else {
							if (strpos(strtolower($v->device_name), 'key') !== false) {
								$sub_keyman_y++;
							}
							else if (strpos(strtolower($v->device_name), 'patrol') !== false) {
								$sub_patrolman_y++;
							}
							else if (strpos(strtolower($v->device_name), 'petrol') !== false) {
								$sub_patrolman_y++;
							}
							else if (strpos(strtolower($v->device_name), 'mate') !== false) {
								$sub_mate_y++;
							}
							else if (strpos(strtolower($v->device_name), 'others') !== false) {
								$sub_others_y++;
							}
						}
					}
				}
				$subdetails[$x]['sub_keyman_g'] = $sub_keyman_g;
				$subdetails[$x]['sub_keyman_y'] = $sub_keyman_y;
				$subdetails[$x]['sub_patrolman_g'] = $sub_patrolman_g;
				$subdetails[$x]['sub_patrolman_y'] = $sub_patrolman_y;
				$subdetails[$x]['sub_mate_g'] = $sub_mate_g;
				$subdetails[$x]['sub_mate_y'] = $sub_mate_y;
				$subdetails[$x]['sub_others_g'] = $sub_others_g;
				$subdetails[$x]['sub_others_y'] = $sub_others_y;
				$subdetails[$x]['user_id'] = $value[0]->user_id;
				$x++;
				$sub_keyman_g=0;
				$sub_keyman_y=0;
				$sub_patrolman_g=0;
				$sub_patrolman_y=0;
				$sub_mate_g=0;
				$sub_mate_y=0;
				$sub_others_g=0;
			    $sub_others_y=0;
			}
			$data['subleveldetails'] = $subdetails;
		}
		
		//************* sub level green / yellow count end ****************//

        // print_r($data);
        // exit();
		

        // Load the dashboard view and capture its output
        $data['middle'] = view('dashboard/dashboard_view', $data);

        

        // Load the main layout view
        return view('mainlayout', $data);

    }

	public function devicelists($device_user_id)
    {
        // Check if the session exists
        if (!session()->has('login_sess_data')) {
            return redirect()->to('/');
        }

        // Get session data
        $data = [];
        $data['sessdata'] = $this->sessdata;
        $data['page_title'] = "Device Listing";
        
        // Get current date
        $current_date = date("Y-m-d");
        
        // Get user data from session
        $user_id = $this->sessdata['user_id'];
        $parent_id = $this->sessdata['parent_id'];
        $group_id = $this->sessdata['group_id'];

        // Build the SQL query
        $schema = $this->schema;
        $deviceListings = $this->db->query("SELECT lefttable.*, organisation, firstname, lastname, 
            (SELECT device_name 
             FROM {$schema}.master_device_setup  
             WHERE id = (SELECT MAX(id) 
                         FROM {$schema}.master_device_setup 
                         WHERE inserttime::date <= current_date::date  
                         AND deviceid = lefttable.deviceid)) as device_name 
            FROM public.get_right_panel_data_updated_blackbox('{$schema}', '{$current_date}', {$user_id}) as lefttable 
            LEFT JOIN public.user_login as ul ON lefttable.user_id = ul.user_id 
            WHERE lefttable.user_id = {$device_user_id} 
            AND lefttable.deviceid IS NOT NULL 
            ORDER BY lastname, firstname ASC"
        )->getResult();

        // Count the devices with a non-empty status color
        $countEven = count(array_filter($deviceListings, function ($i) {
            return $i->status_color != '';
        }));

        // Prepare data to be passed to the view
        $data['count'] = $countEven;
        $data['deviceListings'] = $deviceListings;

        // Load the middle section view
        $data['middle'] = view('dashboard/device_listing', $data);

        // Load the main layout with the device listings
        return view('mainlayout', $data);
    }
}
