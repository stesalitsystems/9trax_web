<?php
namespace App\Models;

use CodeIgniter\Model;

class DeviceModel extends Model
{
    protected $table = 'master_device_details'; // Default table name
    protected $primaryKey = 'id'; // Adjust according to your table structure
    protected $allowedFields = ['serial_no', 'active']; // specify your fields here

    public function __construct()
    {
        $this->session = \Config\Services::session();
        if ($this->session->get('login_sess_data')) {
            $this->sessdata = $this->session->get('login_sess_data');
            $this->schema = $this->sessdata['schemaname'];
        }
        $this->db = \Config\Database::connect();
    }

    public function getDeviceSerialNumbers($usermasterid)
    {
        // Use Query Builder to fetch the data
        return $this->select('serial_no')
                    ->where('assigned_to', $usermasterid)
                    ->get()
                    ->getResult();
    }

    public function getDevicesListAdmin($offset, $limit, $conditions)
    {
        $schemaname = $this->schema; // Assuming sessdata is set earlier in the controller

        $builder = $this->db->table('public.master_device_details as a')
            ->select('a.*, b.user_id, c.firstname, c.lastname')
            ->join('public.master_device_assign as b', 'a.id = b.deviceid AND b.group_id = 3 AND b.active = 1', 'left')
            ->join('public.user_login as c', 'b.user_id = c.id', 'left')
            ->where('a.serial_no IS NOT NULL');

        // Applying conditions
        if (!empty($conditions['serial_no'])) {
            $builder->where('a.serial_no', trim($conditions['serial_no']));
        }
        if (!empty($conditions['mobile_no'])) {
            $builder->where('a.mobile_no', trim($conditions['mobile_no']));
        }
        if (!empty($conditions['imei_no'])) {
            $builder->where('a.imei_no', trim($conditions['imei_no']));
        }
        if (!empty($conditions['active'])) {
            $builder->where('a.active', trim($conditions['active']));
        }

        // Getting filtered data
        $finaldata['filtereddata'] = $builder->limit($limit, $offset)->get()->getResult();

        // Getting total data for pagination
        $finaldata['totaldata'] = $builder->get()->getResult();

        return $finaldata;
    }

    public function getDevicesList($user_id, $offset, $limit, $conditions)
    {
        $schemaname = $this->schema;
        $builder = $this->db->table('public.get_divice_details_record_for_list(' . $schemaname . ',' . $user_id . ')');

        $cond = [];
        
        // Build conditions
        if (!empty($conditions['serial_no'])) {
            $cond[] = "serial_no = " . $this->db->escape(trim($conditions['serial_no']));
        }
        if (!empty($conditions['mobile_no'])) {
            $cond[] = "mobile_no = " . $this->db->escape(trim($conditions['mobile_no']));
        }
        if (!empty($conditions['imei_no'])) {
            $cond[] = "imei_no = " . $this->db->escape(trim($conditions['imei_no']));
        }
        if (!empty($conditions['active'])) {
            $cond[] = "active = " . intval(trim($conditions['active']));
        }

        $whereClause = implode(' AND ', $cond);
        $query = '';

        // Construct the query based on user group
        if (session()->get('group_id') == 2) {
            $query = "SELECT * FROM (" .
                "SELECT sup_pid, sup_gid, serial_no, imei_no, did, mobile_no, warranty_date, linked, parent_id, user_id, issudate, " .
                "refunddate, active, issold, apply_scheam, group_id, role_id, email, address, pincode, state_name, country, username, firstname, " .
                "lastname, organisation, group_name, '' AS list_item, '' AS list_item_name " .
                "FROM public.get_divice_details_record_for_list('$schemaname', $user_id) " .
                "WHERE user_id = $user_id AND active = 1";

            if ($whereClause) {
                $query .= " AND $whereClause";
            }
            $query .= ") AS x";
        } elseif (session()->get('group_id') == 3) {
            $query = "SELECT * FROM (" .
                "SELECT sup_pid, sup_gid, serial_no, imei_no, did, mobile_no, warranty_date, linked, parent_id, user_id, issudate, " .
                "refunddate, active, issold, apply_scheam, group_id, role_id, email, address, pincode, state_name, country, username, firstname, " .
                "lastname, organisation, group_name, '' AS list_item, '' AS list_item_name " .
                "FROM public.get_divice_details_record_for_list('$schemaname', $user_id) " .
                "WHERE parent_id = $user_id AND active = 1";
            
            if ($whereClause) {
                $query .= " AND $whereClause";
            }

            $query .= " UNION " .
                "SELECT sup_pid, sup_gid, serial_no, imei_no, did, mobile_no, warranty_date, linked, parent_id, user_id, issudate, " .
                "refunddate, active, issold, apply_scheam, group_id, role_id, email, address, pincode, state_name, country, username, firstname, " .
                "lastname, organisation, group_name, '' AS list_item, '' AS list_item_name " .
                "FROM public.get_divice_details_record_for_list('$schemaname', $user_id) " .
                "WHERE user_id = $user_id AND active = 1 " .
                "AND did NOT IN (SELECT did FROM public.get_divice_details_record_for_list('$schemaname', $user_id) WHERE parent_id = $user_id AND active = 1)";
            $query .= ") AS y ORDER BY group_id";
        } else {
            $query = "SELECT * FROM (" .
                "SELECT sup_pid, sup_gid, serial_no, imei_no, did, mobile_no, warranty_date, linked, parent_id, user_id, issudate, " .
                "refunddate, active, issold, apply_scheam, group_id, role_id, email, address, pincode, state_name, country, username, firstname, " .
                "lastname, organisation, group_name, '' AS list_item, '' AS list_item_name " .
                "FROM public.get_divice_details_record_for_list('$schemaname', $user_id) " .
                "WHERE parent_id = $user_id AND active = 1";

            if ($whereClause) {
                $query .= " AND $whereClause";
            }

            $query .= " UNION " .
                "SELECT sup_pid, sup_gid, serial_no, imei_no, did, mobile_no, warranty_date, linked, parent_id, user_id, issudate, " .
                "refunddate, active, issold, apply_scheam, group_id, role_id, email, address, pincode, state_name, country, username, firstname, " .
                "lastname, organisation, group_name, '' AS list_item, '' AS list_item_name " .
                "FROM public.get_divice_details_record_for_list('$schemaname', $user_id) " .
                "WHERE user_id = $user_id AND active = 1 " .
                "AND did NOT IN (SELECT did FROM public.get_divice_details_record_for_list('$schemaname', $user_id) WHERE parent_id = $user_id AND active = 1)";
            $query .= ") AS z ORDER BY group_id";
        }

        // Finalize the query with limits
        $finaldata['filtereddata'] = $this->db->query($query . " LIMIT $limit OFFSET $offset")->getResult();
        $finaldata['totaldata'] = $this->db->query($query)->getResult();
        
        return $finaldata;
    }

    public function editRecord($table, $data, $id, $idField)
    {
        return $this->db->table($table)
            ->where($idField, $id)
            ->update($data);
    }

    public function getDevices($schema, $current_date, $user_id, $group_id)
    {
        $builder = $this->db->table('public.get_right_panel_data');
        $builder->select('a.*, b.device_name')
                ->join('stes.master_device_setup b', 'a.deviceid = b.deviceid')
                ->where(['a.group_id' => 2, 'a.deviceid IS NOT NULL', 'a.status_color IS NOT NULL'])
                ->where('a.user_id', $user_id)
                ->where('a.schema', $schema);

        return $builder->get()->getResult();
    }

}
