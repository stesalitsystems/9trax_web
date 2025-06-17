<?php
error_reporting(0);
defined('BASEPATH') OR exit('No direct script access allowed');

class Deviceslinked extends CI_Controller {
    protected $sessdata;
    protected $schema;
    public function __construct() {
        parent::__construct();
        if ($this->session->has_userdata('login_sess_data')) {
            $this->sessdata = $this->session->userdata('login_sess_data');
            $this->schema = $this->sessdata->schemaname;
        }
        $this->load->model('devices_model');
		$this->load->model('common_model');
		$this->load->helper(array('master'));
    }


    public function lists($deviceid = null) {

        $data = array();
        $data['sessdata'] =  $this->sessdata;
        $cond = "";
        $cond_array = array();      
        $data['page_title'] = "Link Devices";
		
		//$data['usersdd'] = $this->devices_model->getRows($this->schema.".user_masterdetails","user_masterdetails.user_id,user_privilege.userkey,user_privilege.username,user_masterdetails.firstname,user_masterdetails.lastname,user_masterdetails.organisation",
		//				array($this->schema.".user_privilege|inner"=>$this->schema.".user_masterdetails.user_id=".$this->schema.".user_privilege.user_id"),
		//				array($this->schema.".user_masterdetails.id!="=>$this->sessdata->user_id, $this->schema.".user_privilege.parent_id"=>$this->sessdata->user_id));
		//echo $this->db->last_query(); die;
		if($this->sessdata->group_id == 4){
			$data['usersdd'] = $usrs = $this->devices_model->get_users($this->sessdata->user_id);
			$usrs1 = array();
			foreach($usrs as $Key => $val){
				if($val->group_id == '5'){
					$usrs1 = $this->devices_model->get_users($val->user_id);
					$usrs = array_merge($usrs, $usrs1);
				}
			}
			$data['usersdd'] = $usrs;
			//$data['usersdd'] = array_merge($usrs, $usrs1);
			//echo "<pre>";print_r($data);exit;
		}
		else{
			$data['usersdd'] = $this->devices_model->get_users($this->sessdata->user_id);
		}
		
		//echo $this->db->last_query(); die;
		
		$data['selected_deviceid'] = base64_decode(urldecode($deviceid));
        if($this->sessdata->group_id == 1){
			//$data['devicesdd'] = $this->db->query("select * from public.master_device_details where active = 1 AND assigned_to is null AND typeofdevice = 'D'")->result();
			$data['devicesdd'] = $this->db->query("select * from public.master_device_details where assigned_to = ".$this->sessdata->user_id)->result();
			
        }
		else{
			//$data['devicesdd'] = $this->devices_model->getRows($this->schema.".master_device_details","serial_no,superdevid as id",array(),array("active"=>2, "assigned_to" => $this->sessdata->user_id, "group_id" => $this->sessdata->group_id));
			$data['devicesdd'] = $this->devices_model->getRows($this->schema.".master_device_details","serial_no,superdevid as id",array(),array("assigned_to" => $this->sessdata->user_id, "group_id" => $this->sessdata->group_id));
        }
		//echo $this->db->last_query(); die;
		$data['middle'] = $this->load->view('deviceslinked/lists', $data, true);       
        
        $this->load->view('mainlayout', $data);
    }
    
    
    public function add() {      
        
		$data['sessdata'] = $this->sessdata;
        
        if (isset($_POST['add']) && !empty($_POST)) {
			//echo "<pre>"; print_r($_POST);die;
            $this->form_validation->set_rules('userid', 'User', 'required|trim');
			
			if(!empty($_POST['devices'])){
				foreach($_POST['devices'] as $key=>$row){
					 $this->form_validation->set_rules('devices['.$key.']', 'Device', 'required|trim|integer');
				}
			}
			else{
				 $this->form_validation->set_rules('devices', 'Device', 'required|trim|integer');
			}
			
            if ($this->form_validation->run() === FALSE) {
                $this->session->set_flashdata('errmsg', validation_errors()); 
            }
			else{
				
				$this->db->trans_start();
				
				$user_id = trim($this->input->post('userid'));
				$usr = $this->db->query("select * from public.user_login where user_id = ".$user_id)->row();
				$parent_id = $usr->parent_id;
				$group_id = $usr->group_id;
				
				foreach($_POST['devices'] as $key => $dvc){
					$dvce = $this->db->query("select * from public.master_device_details where id = ".$dvc)->row();
					$serial_no = $dvce->serial_no;
					$rec = $this->db->query("select msg from public.data_insert_into_master_device_assignment_details_table_test('".$serial_no."',".$parent_id.",".$user_id.",".$group_id.")")->row();
					if($group_id == 6){
						$this->db->query("UPDATE public.master_device_assign SET active = 1 WHERE group_id = 6 and user_id = $user_id");
						$this->db->query("UPDATE ".$this->schema.".master_device_assign SET active = 1 WHERE group_id = 6 and user_id = $user_id");
					}
				}
				
				$this->db->trans_complete();
				if ($this->db->trans_status() === FALSE) {
                    $this->session->set_flashdata('errmsg', "Failed to Assign device");
                }
				else {
                    $this->session->set_flashdata('sucmsg', "Device assigned successfully");
                }
				
            }
        }
        redirect("deviceslinked/lists");
    }

    
	public function delete(){
        
		$schemaname = $this->sessdata->schemaname;
		$device_id = base64_decode(urldecode($_POST['id']));
		
		$this->db->trans_start();
		if($this->sessdata->group_id == 4){
			
			$this->db->query("update ".$schemaname.".master_device_assign set active = 2 where deviceid = ".$device_id." AND group_id in(5,2)");
			$savedata_details = array('linked' => 2, 'assigned_to' => $this->sessdata->user_id, 'group_id' => $this->sessdata->group_id);
			$this->devices_model->editRecord($schemaname.".master_device_details", $savedata_details, $device_id, 'id');
		}
		else{
			$this->db->query("update ".$schemaname.".master_device_assign set active = 2 where deviceid = ".$device_id." AND group_id in(2)");
			$savedata_details = array('linked' => 2, 'assigned_to' => $this->sessdata->user_id, 'group_id' => $this->sessdata->group_id);
			$this->devices_model->editRecord($schemaname.".master_device_details", $savedata_details, $device_id, 'id');
		}
		$this->db->trans_complete();
		$response = array();
		if ($this->db->trans_status() === FALSE) { 
			$response = array("suc"=>0,"msg"=>"Failed to unlink");
		}
		else{
			$response = array("suc"=>1,"msg"=>"Successfully unlinked"); 
		}
		
        echo json_encode($response);
        exit;
    }

	
	public function deleteold(){
        $device_id = base64_decode(urldecode($_POST['id']));
        if($this->sessdata->group_id == 1){
            $get_assigend_user = $this->devices_model->getRow("public.master_device_details","*",array(),array("id"=>$device_id,"linked"=>1));
        }else{
            $get_assigend_user = $this->devices_model->getRow($this->schema.".master_device_details","*",array(),array("superdevid"=>$device_id,"linked"=>1));
        }
        
       // print_r($get_assigend_user); die;
        $response = array();
        if(!empty($get_assigend_user)){
            $this->db->trans_start();
            if($this->sessdata->group_id == 1){
                 $this->devices_model->editRecord("public.master_device_details",array("linked"=>2),$device_id,"id");
                 $get_assigend_user_schema  = $this->devices_model->getSchemaNameFromUserId($get_assigend_user->assigned_to);
                 if(!empty($get_assigend_user_schema)){
                    $this->devices_model->editRecord($get_assigend_user_schema->schemaname.".master_device_details",array("active"=>2),$device_id,"superdevid");                  
                 } 
                  $this->db->query("UPDATE public.tracker_device_movement SET active = ? WHERE current_user_id = ? AND deviceid = ? AND active = ?",array(2,$get_assigend_user->assigned_to,$device_id,1));   
                   $this->db->query("UPDATE public.master_device_assign set active = ? WHERE deviceid = ? AND parent_user_id = ? AND active = ?",array(2,$device_id,$get_assigend_user->assigned_to,1)); 
                 
            }else{
                //for schema LEvel Users
                 $this->devices_model->editRecord($this->schema.".master_device_details",array("linked"=>2),$device_id,"superdevid");
                  $this->db->query("UPDATE public.master_device_assign SET active = ? WHERE parent_user_id = ? AND deviceid = ? AND active = ?",array(2,$get_assigend_user->assigned_to,$device_id,1));   
                  
                  
                 $this->db->query("UPDATE ".$this->schema.".master_device_assign SET active = ? WHERE deviceid = ? AND active = ?",array(2,$device_id,1));
            }
                                 
            $this->db->trans_complete();
            if ($this->db->trans_status() === FALSE) { 
               $response = array("suc"=>0,"msg"=>"Failed to unlink");
            }else{
               $response = array("suc"=>1,"msg"=>"Successfully unlinked"); 
            }
        }else{
            $response = array("suc"=>0,"msg"=>"Failed to unlink");
        }
        echo json_encode($response);
        exit;
    }
    
    

	public function copydeviceassign($deviceid, $user_id, $schemaname){
		
		
		$master_device_details = $this->db->query("select * from public.master_device_details where id = ".$deviceid)->row();
		$master_device_details->assigned_to = $user_id;
		$this->common_model->addRecord($schemaname.".master_device_details", $master_device_details);
		
		$master_device_assign = $this->db->query("select * from public.master_device_assign where deviceid = ".$deviceid)->row();
		$master_device_assign->user_id = $user_id;
		$this->common_model->addRecord($schemaname.".master_device_assign", $master_device_assign);
		
		$tracker_device_movement = $this->db->query("select * from public.tracker_device_movement where deviceid = ".$deviceid)->row();
		$tracker_device_movement->user_id = $user_id;
		$this->common_model->addRecord($schemaname.".tracker_device_movement", $tracker_device_movement);
		
		
	}
	
	
	
}
