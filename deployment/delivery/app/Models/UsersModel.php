<?php
namespace App\Models;

use CodeIgniter\Model;

class UsersModel extends Model
{
    protected $table = 'user_login'; // Your table name
    protected $primaryKey = 'id';

    public function __construct()
    {
        $this->session = \Config\Services::session();
        if ($this->session->get('login_sess_data')) {
            $this->sessdata = $this->session->get('login_sess_data');
            $this->schema = $this->sessdata['schemaname'];
        }
        $this->db = \Config\Database::connect();
    }

    public function check_login($username, $password) {
        // Use Query Builder
        $builder = $this->db->table('user_login');
        $builder->select('*');
        $builder->join('useraccountsetup', 'useraccountsetup.id = user_login.accountid','LEFT');
        $builder->where('username', $username);
        $builder->where('password', $password);
        $builder->where('user_login.active', 1);
    
        // Get the result
        $query = $builder->get();
        
        // Return a single row
        return $query->getRowArray();
    }

    public function getParentGroupId($parentId)
    {
        return $this->db->table($this->table)
            ->select('group_id')
            ->where('user_id', $parentId)
            ->get()
            ->getRowArray();
    }

    public function getUserData($userId)
    {
        return $this->db->table('public.useraccountsetup')
            ->select('*')
            ->join($this->table, $this->table.'.accountid = public.useraccountsetup.id')
            ->where($this->table.'.user_id', $userId)
            ->get()
            ->getRow();
    }

    public function getgroupdd($cond, $cond_array) {
	
        return $this->db->query("select id,name_e from user_group where 1=1 $cond", $cond_array)->getResult();

    }

    public function getroledd($cond, $cond_array) {
        return $this->db->query("select id,name_e from user_role where 1=1 $cond", $cond_array)->getResult();
    }

    public function getUsers($offset, $limit, $conditions)
    {
        $this->session = session();
        $schemaname = $this->session->get('login_sess_data')['schemaname'];
        $builder = $this->db->table("{$schemaname}.user_masterdetails");
        
        // Apply conditions
        if (!empty($conditions['name'])) {
            $builder->groupStart()
                ->like('LOWER(user_login.firstname)', strtolower($conditions['name']))
                ->orLike('LOWER(user_login.lastname)', strtolower($conditions['name']))
                ->groupEnd();
        }

        if (!empty($conditions['email'])) {
            $builder->like('LOWER(user_login.email)', strtolower($conditions['email']));
        }

        if (!empty($conditions['contact'])) {
            $builder->groupStart()
                ->like('mobile', strtolower($conditions['contact']))
                ->orLike('phone', strtolower($conditions['contact']))
                ->groupEnd();
        }

        if (!empty($conditions['group'])) {
            $builder->where('user_group.id', $conditions['group']);
        }

        if (!empty($conditions['role'])) {
            $builder->where('user_role.id', $conditions['role']);
        }

        if (!empty($conditions['active'])) {
            $builder->where('user_privilege.active', $conditions['active']);
        }

        // Select the necessary fields
        $builder->select("{$schemaname}.user_masterdetails.user_id, user_login.firstname, user_login.group_id, user_login.username,
            user_login.schemaname, user_login.lastname, user_login.email, user_login.mobile, user_role.name_e as role,
            user_group.name_e as user_group, CASE WHEN user_privilege.active=1 THEN 'Active' ELSE 'Inactive' END as active,
            CASE WHEN user_login.active=1 THEN 'Active' ELSE 'Inactive' END as loginactive")
            ->join("{$schemaname}.user_privilege", "{$schemaname}.user_privilege.user_id = {$schemaname}.user_masterdetails.user_id")
            ->join('public.user_group', "user_group.id = {$schemaname}.user_privilege.group_id")
            ->join('public.user_role', "user_role.id = {$schemaname}.user_privilege.role_id")
            ->join('public.user_login', "user_login.user_id = {$schemaname}.user_masterdetails.user_id")
            ->where("{$schemaname}.user_masterdetails.user_id !=", $this->session->get('login_sess_data')['user_id'])
            ->where('user_login.parent_id', $this->session->get('login_sess_data')['user_id'])
            ->orderBy('user_group.id', 'ASC')
            ->orderBy("{$schemaname}.user_masterdetails.firstname", 'ASC')
            ->limit($limit, $offset);

        // Execute the query and get filtered data
        $filteredData = $builder->get()->getResult();

        // Reset the query builder to get total data
        $builder->resetQuery();
        $builder->select("{$schemaname}.user_masterdetails.user_id, user_login.firstname, user_login.group_id, user_login.username,
            user_login.schemaname, user_login.lastname, user_login.email, user_login.mobile, user_role.name_e as role,
            user_group.name_e as user_group, CASE WHEN user_privilege.active=1 THEN 'Active' ELSE 'Inactive' END as active,
            CASE WHEN user_login.active=1 THEN 'Active' ELSE 'Inactive' END as loginactive")
            ->join("{$schemaname}.user_privilege", "{$schemaname}.user_privilege.user_id = {$schemaname}.user_masterdetails.user_id")
            ->join('public.user_group', "user_group.id = {$schemaname}.user_privilege.group_id")
            ->join('public.user_role', "user_role.id = {$schemaname}.user_privilege.role_id")
            ->join('public.user_login', "user_login.user_id = {$schemaname}.user_masterdetails.user_id")
            ->where("{$schemaname}.user_masterdetails.user_id !=", $this->session->get('login_sess_data')['user_id'])
            ->where('user_login.parent_id', $this->session->get('login_sess_data')['user_id']);

        // Get total count of data
        $totalData = $builder->countAllResults();

        return [
            'filtereddata' => $filteredData,
            'totaldata' => $totalData
        ];
    }

    public function getUserGroupData($groupIds)
    {
        return $this->db->table('public.user_group')
            ->select('*')
            ->where('active', 1)
            ->whereIn('id', $groupIds)
            ->get()
            ->getResult();
    }

    public function addusers(array $savedata)
    {
        // echo "<pre>";print_r($savedata);exit();
        // Start the transaction
        $this->db->transStart();
        
        $parentgroupid = $this->sessdata['group_id'];

        unset($savedata['csrf_test_name']);

        // echo "<pre>";print_r($savedata);exit();

        if ($parentgroupid == 1) {
            // Add user login record
            $login_add = $this->addRecord('public.user_login', $savedata);
            
            // Prepare master details
            $masterdetails = [
                'user_id' => $login_add,
                'firstname' => $savedata['firstname'],
                'lastname' => $savedata['lastname'],
                'email' => $savedata['email'],
                'mobile' => $savedata['mobile'],
                'phone' => $savedata['phone'],
                'address' => $savedata['address'],
                'state_name' => $savedata['state_name'],
                'country' => $savedata['country'],
                'pincode' => $savedata['pincode'],
                'insertby' => $savedata['insertby'],
                'active' => $savedata['active'],
                'organisation' => $savedata['organisation'],
            ];

            

            // Add master details record
            $this->addRecord($this->schema . '.user_masterdetails', $masterdetails);

            // Prepare privilege data
            $privilege = [
                'user_id' => $login_add,
                'group_id' => $savedata['group_id'],
                'parent_id' => $savedata['parent_id'],
                'username' => $savedata['username'],
                'password' => $savedata['password'],
                'role_id' => $savedata['role_id'],
                'password_type' => isset($savedata['password_type']) ? $savedata['password_type'] : 2,
                'prev_add' => isset($savedata['prev_add']) ? $savedata['prev_add'] : 2,
                'prev_update' => isset($savedata['prev_update']) ? $savedata['prev_update'] : 2,
                'prev_view' => isset($savedata['prev_view']) ? $savedata['prev_view'] : 2,
                'prev_delete' => isset($savedata['prev_delete']) ? $savedata['prev_delete'] : 2,
                'prev_download' => isset($savedata['prev_download']) ? $savedata['prev_download'] : 2,
                'prev_dtm' => isset($savedata['prev_dtm']) ? $savedata['prev_dtm'] : 2,
                'prev_demo' => isset($savedata['prev_demo']) ? $savedata['prev_demo'] : 2,
                'prev_approval' => isset($savedata['prev_approval']) ? $savedata['prev_approval'] : 2,
                'active' => $savedata['active'],
                'status' => $savedata['status'],
                'userkey' => $savedata['userkey'],
                'schemaname' => $savedata['schemaname'],
                'prev_list' => isset($savedata['prev_list']) ? $savedata['prev_list'] : 2,
            ];

            // Add privilege record
            $this->addRecord($this->schema . '.user_privilege', $privilege);

            if ($privilege['group_id'] != 2) {
                // Clone schema
                // $this->db->query("SELECT public.clone_schema('public', '{$savedata['schemaname']}')");
                $schemaName = $this->db->escapeString($savedata['schemaname']);
                $sql = "SELECT public.clone_schema('public', '{$schemaName}')";
                $result = $this->db->query($sql);
                // $this->db->query("SELECT public.clone_schema_alter_request('public', '{$savedata['schemaname']}')");

                // Add schema data
                $this->addRecord($savedata['schemaname'] . '.user_masterdetails', $masterdetails);
                $this->addRecord($savedata['schemaname'] . '.user_privilege', $privilege);
            }

        } elseif ($parentgroupid == 3) {
            $useraccountsetup = [
                'numberofdevice' => $savedata['numberofdevice'],
                'expirydate' => date('Y-m-d', strtotime($savedata['expirydate'])),
                'configurationsms' => $savedata['configurationsms'],
                'notificationsms' => $savedata['notificationsms'],
                'neotificationemail' => $savedata['neotificationemail'],
                'notificationtotalsms' => $savedata['notificationtotalsms'],
                'freefallalert' => $savedata['freefallalert'],
                'networklocation' => $savedata['networklocation'],
                'numberofpoi' => $savedata['numberofpoi'],
                'numberofroute' => $savedata['numberofroute'],
                'numberofgeofence' => $savedata['numberofgeofence'],
                'numberofadmin' => $savedata['numberofadmin'],
                'allowedtocreateuser' => $savedata['allowedtocreateuser'],
                'numberofuser' => $savedata['numberofuser'],
                'insertby' => $savedata['insertby'],
                'updateby' => $savedata['insertby'],
                'active' => $savedata['active'],
            ];

            // Add user account setup
            $this->addRecord('public.useraccountsetup', $useraccountsetup);
            $get_account = $this->db->query("SELECT id FROM public.useraccountsetup ORDER BY id DESC LIMIT 1")->getRow();
            $savedata['accountid'] = $accountid = $get_account->id;

            // Clean up unnecessary savedata fields
            unset($savedata['numberofdevice'], $savedata['expirydate'], $savedata['configurationsms'],
                $savedata['notificationsms'], $savedata['neotificationemail'], 
                $savedata['notificationtotalsms'], $savedata['freefallalert'], 
                $savedata['networklocation'], $savedata['numberofpoi'], 
                $savedata['numberofroute'], $savedata['numberofgeofence'], 
                $savedata['numberofadmin'], $savedata['allowedtocreateuser'], 
                $savedata['numberofuser'], $savedata['poi_unlimited'], 
                $savedata['route_unlimited'], $savedata['geofence_unlimited']);

            // Add user login record
            $login_add = $this->addRecord('public.user_login', $savedata);

            // Prepare master details
            $masterdetails = [
                'user_id' => $login_add,
                'firstname' => $savedata['firstname'],
                'lastname' => $savedata['lastname'],
                'email' => $savedata['email'],
                'mobile' => $savedata['mobile'],
                'phone' => $savedata['phone'],
                'address' => $savedata['address'],
                'state_name' => $savedata['state_name'],
                'country' => $savedata['country'],
                'pincode' => $savedata['pincode'],
                'insertby' => $savedata['insertby'],
                'active' => $savedata['active'],
                'organisation' => $savedata['organisation'],
            ];

            // Add master details record
            $this->addRecord($this->schema . '.user_masterdetails', $masterdetails);

            // Prepare privilege data
            $privilege = [
                'user_id' => $login_add,
                'group_id' => $savedata['group_id'],
                'parent_id' => $savedata['parent_id'],
                'username' => $savedata['username'],
                'password' => $savedata['password'],
                'role_id' => $savedata['role_id'],
                'password_type' => isset($savedata['password_type']) ? $savedata['password_type'] : 2,
                'prev_add' => isset($savedata['prev_add']) ? $savedata['prev_add'] : 2,
                'prev_update' => isset($savedata['prev_update']) ? $savedata['prev_update'] : 2,
                'prev_view' => isset($savedata['prev_view']) ? $savedata['prev_view'] : 2,
                'prev_delete' => isset($savedata['prev_delete']) ? $savedata['prev_delete'] : 2,
                'prev_download' => isset($savedata['prev_download']) ? $savedata['prev_download'] : 2,
                'prev_dtm' => isset($savedata['prev_dtm']) ? $savedata['prev_dtm'] : 2,
                'prev_demo' => isset($savedata['prev_demo']) ? $savedata['prev_demo'] : 2,
                'prev_approval' => isset($savedata['prev_approval']) ? $savedata['prev_approval'] : 2,
                'active' => $savedata['active'],
                'status' => isset($savedata['status']) ? $savedata['status'] : 2,
                'userkey' => $savedata['userkey'],
                'schemaname' => $savedata['schemaname'],
                'prev_list' => isset($savedata['prev_list']) ? $savedata['prev_list'] : 2,
            ];

            // echo "<pre>";print_r($privilege);exit();

            // Add privilege record
            $this->addRecord($this->schema . '.user_privilege', $privilege);

        } else {
            $savedata['accountid'] = $this->sessdata['accountid'];
            $savedata['active'] = 1; // Setting active to 1 for group_id 6
            
            // Add user login record
            $login_add = $this->addRecord('public.user_login', $savedata);
            
            // Prepare master details
            $masterdetails = [
                'user_id' => $login_add,
                'firstname' => $savedata['firstname'],
                'lastname' => $savedata['lastname'],
                'email' => $savedata['email'],
                'mobile' => $savedata['mobile'],
                'phone' => $savedata['phone'],
                'address' => $savedata['address'],
                'state_name' => $savedata['state_name'],
                'country' => $savedata['country'],
                'pincode' => $savedata['pincode'],
                'insertby' => $savedata['insertby'],
                'active' => $savedata['active'],
                'organisation' => $savedata['organisation'],
            ];

            // Add master details record
            $this->addRecord($this->schema . '.user_masterdetails', $masterdetails);

            // Prepare privilege data
            $privilege = [
                'user_id' => $login_add,
                'group_id' => $savedata['group_id'],
                'parent_id' => $savedata['parent_id'],
                'username' => $savedata['username'],
                'password' => $savedata['password'],
                'role_id' => $savedata['role_id'],
                'password_type' => isset($savedata['password_type']) ? $savedata['password_type'] : 2,
                'prev_add' => isset($savedata['prev_add']) ? $savedata['prev_add'] : 2,
                'prev_update' => isset($savedata['prev_update']) ? $savedata['prev_update'] : 2,
                'prev_view' => isset($savedata['prev_view']) ? $savedata['prev_view'] : 2,
                'prev_delete' => isset($savedata['prev_delete']) ? $savedata['prev_delete'] : 2,
                'prev_download' => isset($savedata['prev_download']) ? $savedata['prev_download'] : 2,
                'prev_dtm' => isset($savedata['prev_dtm']) ? $savedata['prev_dtm'] : 2,
                'prev_demo' => isset($savedata['prev_demo']) ? $savedata['prev_demo'] : 2,
                'prev_approval' => isset($savedata['prev_approval']) ? $savedata['prev_approval'] : 2,
                'active' => $savedata['active'],
                'status' => isset($savedata['status']) ? $savedata['status'] : 2,
                'userkey' => $savedata['userkey'],
                'schemaname' => $savedata['schemaname'],
                'prev_list' => isset($savedata['prev_list']) ? $savedata['prev_list'] : 2,
            ];

            // Add privilege record
            $this->addRecord($this->schema . '.user_privilege', $privilege);
        }

        // Complete the transaction
        $this->db->transComplete();

        // Check if transaction was successful
        if ($this->db->transStatus() === false) {
            return 0; // Transaction failed
        } else {
            return 1; // Transaction successful
        }
    }

    // Add a method to insert a record into the database
    protected function addRecord($table, array $data)
    {
        $this->db->table($table)->insert($data);
        return $this->db->insertID();
    }

    public function get_user($userid)
    {
        // Use the query builder to perform the join and get the user details
        return $this->db->table($this->table)
            ->select('user_login.*, useraccountsetup.*') // Specify the columns you need
            ->join('public.useraccountsetup', 'useraccountsetup.id = user_login.accountid', 'left')
            ->where('user_id', $userid)
            ->get()
            ->getRow();
    }

    public function editUsers($savedata, $usermasterid)
    {
        // Start transaction
        $this->db->transStart();

        unset($savedata['csrf_test_name']);
        unset($savedata['edit']);

        $savedata['pincode'] = $savedata['pincode'] ? $savedata['pincode'] : 0;
        
        if ($this->sessdata['group_id'] == 3) {
            $useraccountsetup = [
                'numberofdevice' => $savedata['numberofdevice'],
                'expirydate' => date('Y-m-d', strtotime($savedata['expirydate'])),
                'configurationsms' => $savedata['configurationsms'],
                'notificationsms' => $savedata['notificationsms'],
                'neotificationemail' => $savedata['neotificationemail'],
                'notificationtotalsms' => $savedata['notificationtotalsms'],
                'freefallalert' => $savedata['freefallalert'],
                'networklocation' => $savedata['networklocation'],
                'numberofpoi' => $savedata['numberofpoi'],
                'numberofroute' => $savedata['numberofroute'],
                'numberofgeofence' => $savedata['numberofgeofence'],
                'numberofadmin' => $savedata['numberofadmin'],
                'allowedtocreateuser' => $savedata['allowedtocreateuser'],
                'numberofuser' => $savedata['numberofuser'],
                'updateby' => $this->sessdata['user_id'],
                'updatetime' => date('Y-m-d H:i:s')
            ];

            $this->editRecord("public.useraccountsetup", $useraccountsetup, $savedata['accountid'], "id");
            // Unset unnecessary fields
            $fieldsToUnset = [
                'numberofdevice', 'expirydate', 'configurationsms', 'notificationsms', 
                'neotificationemail', 'notificationtotalsms', 'freefallalert', 
                'networklocation', 'numberofpoi', 'numberofroute', 
                'numberofgeofence', 'numberofadmin', 'allowedtocreateuser', 
                'numberofuser', 'poi_unlimited', 'route_unlimited', 'geofence_unlimited'
            ];
            foreach ($fieldsToUnset as $field) {
                unset($savedata[$field]);
            }
        }
        
        // echo "<pre>";print_r($savedata);exit();
        // Update user_login
        $this->editRecord("public.user_login", $savedata, $usermasterid, "user_id");
        
        $masterdetails = [
            'firstname' => $savedata['firstname'],
            'lastname' => $savedata['lastname'],
            'email' => $savedata['email'],
            'mobile' => $savedata['mobile'],
            'phone' => $savedata['phone'],
            'address' => $savedata['address'],
            'state_name' => $savedata['state_name'],
            'country' => $savedata['country'],
            'pincode' => $savedata['pincode'] ? $savedata['pincode'] : 0,
            'organisation' => $savedata['organisation'],
        ];
        
        $this->editRecord($this->schema . ".user_masterdetails", $masterdetails, $usermasterid, "user_id");
        
        $privilege = [
            'prev_add' => 2,//trim($this->request->getPost('prev_add', FILTER_SANITIZE_NUMBER_INT) ?? 2),
            'prev_update' => 2,//trim($this->request->getPost('prev_update', FILTER_SANITIZE_NUMBER_INT) ?? 2),
            'prev_view' => 2,//trim($this->request->getPost('prev_view', FILTER_SANITIZE_NUMBER_INT) ?? 2),
            'prev_delete' => 2,//trim($this->request->getPost('prev_delete', FILTER_SANITIZE_NUMBER_INT) ?? 2),
            'prev_download' => 2,//trim($this->request->getPost('prev_download', FILTER_SANITIZE_NUMBER_INT) ?? 2),
            'prev_dtm' => 2,//trim($this->request->getPost('prev_dtm', FILTER_SANITIZE_NUMBER_INT) ?? 2),
            'prev_demo' => 2,//trim($this->request->getPost('prev_demo', FILTER_SANITIZE_NUMBER_INT) ?? 2),
            'prev_approval' => 2,//trim($this->request->getPost('prev_approval', FILTER_SANITIZE_NUMBER_INT) ?? 2),
            'prev_list' => 2,//trim($this->request->getPost('prev_list', FILTER_SANITIZE_NUMBER_INT) ?? 2),
            'status' => $savedata['status'] ?? 2
        ];
        
        $this->editRecord($this->schema . ".user_privilege", $privilege, $usermasterid, "user_id");
        
        // Complete transaction
        $this->db->transComplete();
        
        // Check if transaction was successful
        if ($this->db->transStatus() === false) {
            return 0; // Transaction failed
        } else {
            return 1; // Transaction successful
        }
    }

    private function editRecord($table, $data, $id, $idField)
    {
        return $this->db->table($table)
            ->where($idField, $id)
            ->update($data);
    }

  
}
