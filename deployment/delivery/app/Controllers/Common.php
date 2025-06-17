<?php

namespace App\Controllers;

use App\Models\AccountModel;
use App\Models\UsersModel;
use CodeIgniter\Controller;
use CodeIgniter\Validation\Validation;

class Common extends Controller
{
    protected $accountModel;
    protected $usersModel;

    public function __construct()
    {
        $this->accountModel = new AccountModel();
        helper(['form', 'url', 'master', 'communication']);
        $this->usersModel = new UsersModel();
        $this->session = \Config\Services::session();
        if ($this->session->get('login_sess_data')) {
            $this->sessdata = $this->session->get('login_sess_data');
            $this->schema = $this->sessdata['schemaname'];
        }

        $this->db = \Config\Database::connect();
    }

    public function getDrpdwnRole()
    {
        $group_id = $this->sessdata['group_id'];

        // Determine role IDs based on group_id
        if ($group_id == 1) {
            $roleids = [1];
        } else {
            $roleids = [2];
        }

        // Load the user model
        $rws = $this->db->table('user_role')
                    ->where('active', 1)
                        ->whereIn('id', $roleids)
                        ->get()
                        ->getResult();

        // echo "<pre>";print_r($rws);exit();

        // Build the dropdown options
        $res = '<option value="">Select</option>';
        foreach ($rws as $val) {
            $res .= '<option value="' . $val->id . '">' . esc($val->name_e) . '</option>';
        }

        // Return the response
        return $this->response->setBody($res);
    }
}
