<?php

namespace App\Models;

use CodeIgniter\Model;

class AccountModel extends Model
{
    public function __construct()
    {
        $this->session = session();
        $this->db = \Config\Database::connect();
    }
    
    public function callbackCheckDevice($deviceid, $deviceimei) {
    
        // Prepare the query
        $builder = $this->db->table('master_device_details');
        $builder->where('serial_no', $deviceid);
        $builder->where('imei_no', $deviceimei);
        $builder->where('active', 2);
    
        // Execute the query and get the result
        $query = $builder->get();
        
        // Check if rows were returned
        return $query->getNumRows() > 0;
    }

    public function checkUsername($username) {
        $builder = $this->db->table('public.user_login');
        // Use the query builder to find the username
        $builder->where('username', $username);
        $query = $builder->get();

        $numRow = $query->getNumRows();

        // Check if any rows are returned
        if ($numRow > 0) {
            return true;
        } else {
            return false;
        }
    }

    public function passwordReset($username)
    {
        $newPassword = md5('sil123'); // Generate the new password

        // Write the raw SQL query
        $sql = "UPDATE public.user_login SET password = ? WHERE username = ?";
        
        // Execute the query with bound parameters
        return $this->db->query($sql, [$newPassword, $username]);
    }
    
}
