<?php
namespace App\Models;

use CodeIgniter\Model;

class ReportModel extends Model
{
    public function get_users($sessdata)
    {
        $schemaname = $sessdata['schemaname'];
        $current_date = date('Y-m-d');
        $user_id = $sessdata['user_id'];

        $query = "SELECT lefttable.user_id, organisation 
                  FROM public.get_right_panel_data('{$schemaname}', '{$current_date}', {$user_id}) AS lefttable 
                  LEFT JOIN public.user_login AS ul ON lefttable.user_id = ul.user_id 
                  WHERE lefttable.group_id = 2 
                  AND lefttable.deviceid IS NOT NULL 
                  GROUP BY lefttable.user_id, organisation 
                  ORDER BY organisation ASC";

        return $this->db->query($query)->getResult();
    }
}