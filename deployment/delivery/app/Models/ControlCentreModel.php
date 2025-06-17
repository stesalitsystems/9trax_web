<?php
namespace App\Models;

use CodeIgniter\Model;

class ControlCentreModel extends Model
{
    protected $schema;
    protected $sessdata;
    protected $DBGroup = 'default'; // Specify your database connection group if needed

    public function __construct()
    {
        // Access the session service
        $session = session();

        // Check if the session has user data
        if ($session->has('login_sess_data')) {
            $this->sessdata = $session->get('login_sess_data');
            $this->schema = $this->sessdata['schemaname']; // Use array access for session data
        } else {
            $this->schema = 'public';
        }

        $this->db = \Config\Database::connect();
    }

    public function getNotificationAndSosData($user_groupid, $user_id, $schemaname)
    {
        // No need to set $user_groupid to 0 here; use the parameter directly.
        $result = [];
        
        $result['calls'] = $this->getCalls($user_groupid, $user_id, $schemaname);
        $result['sos'] = $this->getSos($user_groupid, $user_id, $schemaname);
        $result['alerts'] = $this->getAlerts($user_groupid, $user_id, $schemaname);
        
        return $result;
    }

    public function getCalls($user_groupid, $user_id, $schemaname, $cond='')
    {
        $fromdt = date('Y-m-d', strtotime('-7 days'));
        $todt = date('Y-m-d');

        // Build the query
        $query = "SELECT * FROM public.get_call_record_of_eatch_divice(?, ?) 
                  WHERE currentdate = ? " . $cond . " 
                  ORDER BY currentdate DESC, currenttime DESC";

        // Prepare the query parameters
        $params = [$this->sessdata['parent_id'], $schemaname, $todt];

        // Execute the query
        $result = $this->db->query($query, $params);

        // Return result based on isRow
        if (!empty($isRow)) {
            return $result->getRow(); // Use getRow() for a single row
        } else {
            return $result->getResult(); // Use getResult() for multiple rows
        }
    }

    public function getSos($user_groupid, $user_id, $schemaname, $isRow = null, $cond = '')
    {
        $fromdt = date('Y-m-d', strtotime('-7 days'));
        $todt = date('Y-m-d');

        // Build the query using Query Builder
        $query = "SELECT * FROM public.get_sos_record_of_eatch_divice(?, ?) 
                  WHERE currentdate = ? " . $cond . " 
                  ORDER BY currentdate DESC, currenttime DESC";

        // Prepare the query parameters
        $params = [$this->sessdata['parent_id'], $schemaname, $todt];

        // Execute the query
        $result = $this->db->query($query, $params);

        // Return result based on isRow
        if (!empty($isRow)) {
            return $result->getRow(); // Use getRow() for a single row
        } else {
            return $result->getResult(); // Use getResult() for multiple rows
        }
    }

    public function getAlerts($user_groupid, $user_id, $schemaname, $cond='')
    {
        $fromdt = date('Y-m-d', strtotime('-7 days'));
        $todt = date('Y-m-d');

        // Build the query
        $query = "SELECT alertsp.* 
                  FROM public.get_alart_record_of_eatch_divice(?, ?) AS alertsp 
                  INNER JOIN $schemaname.generate_sms_mail AS gsm 
                  ON (alertsp.sosid = gsm.alert_id) 
                  WHERE alertsp.currentdate = ? " . $cond . " 
                  ORDER BY currentdate DESC, currenttime DESC";

        // Prepare the query parameters
        $params = [$this->sessdata['parent_id'], $schemaname, $todt];

        // Execute the query
        $result = $this->db->query($query, $params);

        // Return result based on isRow
        if (!empty($isRow)) {
            return $result->getRow(); // Use getRow() for a single row
        } else {
            return $result->getResult(); // Use getResult() for multiple rows
        }
    }

    public function getDevicePosition($deviceId, $currentDate, $limit)
    {
        $schemaName = $this->schema; // Set your schema name

        if ($this->sessdata['group_id'] == 1) {
            return $this->db->query("SELECT * FROM public.positionaldata_unregister_device_with_details WHERE deviceid = ?", [$deviceId])->getRow();
        } else {
            return $this->db->query("SELECT lefttable.*, icon_details 
                FROM public.get_positional_record_of_eatch_divice(?, ?, ?) AS lefttable 
                LEFT JOIN {$schemaName}.master_device_setup AS mds ON lefttable.deviceid = mds.deviceid 
                ORDER BY lefttable.currenttime DESC LIMIT 1", [$deviceId, $currentDate, $limit])->getRow();
        }
    }

    public function getRightPanelWeb($schemaname, $current_date, $mygroupid, $parentid)
    {
        // echo $mygroupid."xxxxxxxx";
        // echo $schemaname."xxxxxxxx";
        // echo $current_date."xxxxxxxxxxxxx";
        // echo $parentid."xxxxxxxxxxxxx";

        if ($mygroupid == 1) {
            return $this->db->query("SELECT * FROM public.positionaldata_unregister_device_with_details")->getResult();
        } elseif ($mygroupid == 4) {
            return $this->db->query("
                SELECT lefttable.*, dept.user_id AS deptuser, dept.organisation AS deptorganisation,
                subdept.user_id AS subdeptuser, subdept.organisation AS subdeptorganisation,
                (SELECT device_name || '$' || silentcallflag 
                 FROM {$schemaname}.master_device_setup  
                 WHERE id = (SELECT MAX(id) 
                             FROM {$schemaname}.master_device_setup 
                             WHERE inserttime::date <= current_date::date AND deviceid = lefttable.deviceid)) AS device_name,
                u.organisation
                FROM public.get_right_panel_data_updated_blackbox('{$schemaname}', '{$current_date}', {$parentid}) AS lefttable
                LEFT JOIN (SELECT a.user_id, a.group_id, a.deviceid, ul.organisation 
                           FROM public.get_right_panel_data_updated_blackbox('{$schemaname}', '{$current_date}', {$parentid}) AS a 
                           LEFT JOIN public.user_login AS ul ON a.user_id = ul.user_id
                           WHERE a.deviceid IS NOT NULL AND a.group_id = 5) AS dept ON (dept.deviceid = lefttable.deviceid)
                LEFT JOIN (SELECT a.user_id, a.group_id, a.deviceid, ul.organisation 
                           FROM public.get_right_panel_data_updated_blackbox('{$schemaname}', '{$current_date}', {$parentid}) AS a 
                           LEFT JOIN public.user_login AS ul ON a.user_id = ul.user_id
                           WHERE a.deviceid IS NOT NULL AND a.group_id = 8) AS subdept ON (subdept.deviceid = lefttable.deviceid)
                LEFT JOIN public.user_login AS u ON lefttable.user_id = u.user_id
                WHERE lefttable.deviceid IS NOT NULL AND lefttable.group_id = 2 
                ORDER BY status_color ASC
            ")->getResult();
        } elseif ($mygroupid == 5) {
            return $this->db->query("
                SELECT lefttable.*, subdept.user_id AS subdeptuser, subdept.organisation AS subdeptorganisation,
                (SELECT device_name || '$' || silentcallflag 
                 FROM {$schemaname}.master_device_setup  
                 WHERE id = (SELECT MAX(id) 
                             FROM {$schemaname}.master_device_setup 
                             WHERE inserttime::date <= current_date::date AND deviceid = lefttable.deviceid)) AS device_name,
                u.organisation
                FROM public.get_right_panel_data_updated_blackbox('{$schemaname}', '{$current_date}', {$parentid}) AS lefttable
                LEFT JOIN (SELECT a.user_id, a.group_id, a.deviceid, ul.organisation 
                           FROM public.get_right_panel_data_updated_blackbox('{$schemaname}', '{$current_date}', {$parentid}) AS a 
                           LEFT JOIN public.user_login AS ul ON a.user_id = ul.user_id
                           WHERE a.deviceid IS NOT NULL AND a.group_id = 8) AS subdept ON (subdept.deviceid = lefttable.deviceid)
                LEFT JOIN public.user_login AS u ON lefttable.user_id = u.user_id
                WHERE lefttable.deviceid IS NOT NULL AND lefttable.group_id = 2 
                ORDER BY status_color ASC
            ")->getResult();
        } elseif ($mygroupid == 3) {
            

            return $this->db->query("
                SELECT lefttable.*, dept.user_id AS deptuser, dept.organisation AS deptorganisation,
                subdept.user_id AS subdeptuser, subdept.organisation AS subdeptorganisation,
                (SELECT device_name || '$' || silentcallflag 
                 FROM {$schemaname}.master_device_setup  
                 WHERE id = (SELECT MAX(id) 
                             FROM {$schemaname}.master_device_setup 
                             WHERE inserttime::date <= current_date::date AND deviceid = lefttable.deviceid)) AS device_name,
                u.organisation
                FROM public.get_right_panel_data_updated_blackbox('{$schemaname}', '{$current_date}', {$parentid}) AS lefttable
                LEFT JOIN (SELECT a.user_id, a.group_id, a.deviceid, ul.organisation 
                           FROM public.get_right_panel_data_updated_blackbox('{$schemaname}', '{$current_date}', {$parentid}) AS a 
                           LEFT JOIN public.user_login AS ul ON a.user_id = ul.user_id
                           WHERE a.deviceid IS NOT NULL AND a.group_id = 5) AS dept ON (dept.deviceid = lefttable.deviceid)
                LEFT JOIN (SELECT a.user_id, a.group_id, a.deviceid, ul.organisation 
                           FROM public.get_right_panel_data_updated_blackbox('{$schemaname}', '{$current_date}', {$parentid}) AS a 
                           LEFT JOIN public.user_login AS ul ON a.user_id = ul.user_id
                           WHERE a.deviceid IS NOT NULL AND a.group_id = 8) AS subdept ON (subdept.deviceid = lefttable.deviceid)
                LEFT JOIN public.user_login AS u ON lefttable.user_id = u.user_id
                WHERE lefttable.deviceid IS NOT NULL AND lefttable.group_id = 2 
                ORDER BY status_color ASC
            ")->getResult();
        } else {
           /* echo "
                SELECT lefttable.*, 
                (SELECT device_name ||'$'|| silentcallflag 
                 FROM $schemaname.master_device_setup  
                 WHERE id = (SELECT MAX(id) 
                             FROM $schemaname.master_device_setup 
                             WHERE inserttime::date <= current_date::date AND deviceid = lefttable.deviceid)) AS device_name,
                organisation 
                FROM public.get_right_panel_data('{$schemaname}', '{$current_date}', {$parentid}) AS lefttable 
                LEFT JOIN public.user_login AS ul ON lefttable.user_id = ul.user_id 
                WHERE lefttable.group_id = 2 AND lefttable.deviceid IS NOT NULL 
                ORDER BY status_color ASC
            ";*/
            return $this->db->query("
                SELECT lefttable.*, 
                (SELECT device_name ||'$'|| silentcallflag 
                 FROM $schemaname.master_device_setup  
                 WHERE id = (SELECT MAX(id) 
                             FROM $schemaname.master_device_setup 
                             WHERE inserttime::date <= current_date::date AND deviceid = lefttable.deviceid)) AS device_name,
                organisation 
                FROM public.get_right_panel_data('{$schemaname}', '{$current_date}', {$parentid}) AS lefttable 
                LEFT JOIN public.user_login AS ul ON lefttable.user_id = ul.user_id 
                WHERE lefttable.group_id = 2 AND lefttable.deviceid IS NOT NULL 
                ORDER BY status_color ASC
            ")->getResult();
        }
    }

    // Method to get device current details
    public function getDeviceCurrentDetails($deviceid, $fetchdate)
    {
        // Check if the user's group_id is 1
        if ($this->sessdata['group_id'] == 1) {
            // If group_id is 1, run a direct query
            $query = $this->db->table('public.positionaldata_unregister_device_with_details')
                              ->select('*')
                              ->where('deviceid', $deviceid)
                              ->get();

            // Return the first row of the result (as a single object)
            return $query->getRow();
        } else {
            // For other group_ids, run the function query with parameters
            $query = $this->db->query('SELECT * FROM public.get_divicedetails_of_eatch_divice(?, ?)', [$deviceid, $fetchdate]);

            // Return the first row of the result (as a single object)
            return $query->getRow();
        }
    }

    public function getDevice($device)
    {
        $groupId = $this->sessdata['group_id'];
        
        if ($groupId == 1) {
            $query = "SELECT master_device_details.id, master_device_details.serial_no, master_device_details.mobile_no, 
                      master_device_details.mac_add, master_device_details.sdcard_no, master_device_details.active, 
                      master_device_details.linked, master_device_details.imei_no, master_device_details.sim_icc_id, 
                      master_device_details.warranty_date, master_device_details.assigned_to, 
                      (SELECT device_name FROM {$this->schema}.master_device_setup WHERE id = 
                      (SELECT MAX(id) FROM {$this->schema}.master_device_setup WHERE inserttime::date <= CURRENT_DATE::date 
                      AND deviceid = master_device_details.superdevid)) AS device_name 
                      FROM {$this->schema}.master_device_details WHERE id = ?";
        } else {
            $query = "SELECT master_device_details.id, master_device_details.serial_no, master_device_details.mobile_no, 
                      master_device_details.mac_add, master_device_details.sdcard_no, master_device_details.active, 
                      master_device_details.linked, master_device_details.imei_no, master_device_details.sim_icc_id, 
                      master_device_details.warranty_date, master_device_details.assigned_to, 
                      (SELECT device_name FROM {$this->schema}.master_device_setup WHERE id = 
                      (SELECT MAX(id) FROM {$this->schema}.master_device_setup WHERE inserttime::date <= CURRENT_DATE::date 
                      AND deviceid = master_device_details.superdevid)) AS device_name 
                      FROM {$this->schema}.master_device_details WHERE superdevid = ?";
        }
        
        return $this->db->query($query, [$device])->getRow();
    }

}