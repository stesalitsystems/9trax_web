<?php
namespace App\Models;
use App\Models\UsersModel;

use CodeIgniter\Model;

class CommonModel extends Model
{
    protected $usersModel;

    public function __construct()
    {
        $this->usersModel = new UsersModel();
        $this->db = \Config\Database::connect();
        $this->session = \Config\Services::session();
        if ($this->session->get('login_sess_data')) {
            $this->sessdata = $this->session->get('login_sess_data');
            $this->schema = $this->sessdata['schemaname'];
        }
    }
    
    public function getRows($table, $select = '*', $joins = [], $conds = [], $groupby = null, $orderby = null)
    {
        // Start building the query
        $builder = $this->db->table($table);
        
        // Select fields
        $builder->select($select);

        // Join tables if any
        foreach ($joins as $join) {
            $builder->join($join['table'], $join['condition'], $join['type'] ?? 'INNER');
        }

        // // Apply conditions
        // if (!empty($conds)) {
        //     $builder->where($conds);
        // }
        // Handle WHERE conditions
        if (!empty($conds)) {
            foreach ($conds as $key => $value) {
                if (strpos($key, 'IN') !== false) {
                    $key = str_replace(' IN', '', $key); // Get rid of ' IN'
                    $builder->whereIn($key, $value);
                } else {
                    $builder->where($key, $value);
                }
            }
        }

        // Group by if specified
        if ($groupby) {
            $builder->groupBy($groupby);
        }

        // Order by if specified
        if ($orderby) {
            $builder->orderBy($orderby);
        }

        // Execute the query
        $query = $builder->get();

        // Return results or false if no rows
        if ($query->getNumRows() > 0) {
            return $query->getResult();
        } else {
            return false;
        }
    }

    // Gets total number of rows based on query
    public function getRowstotal(string $table, string $select = '*', array $joins = [], array $conds = [], $groupby = null, $orderby = null): int
    {
        $query = $this->getQueryResult($table, $select, $joins, $conds, $groupby, $orderby);
        return $query->getNumRows();
    }

    // Retrieves rows with pagination
    public function getRowspagin(string $table, string $select, int $perpage, int $page, array $joins = [], array $conds = [], $groupby = null, $orderby = null)
    {
        $query = $this->getQueryResult($table, $select, $joins, $conds, $groupby, $orderby, $perpage, $page);
        return $query->getResult();
    }

    // Retrieves query results with various conditions
    public function getQueryResult(
        string $table,
        string $select = '*',
        array $joins = [],
        array $conds = [],
        $groupby = null,
        $orderby = null,
        $perpage = null,
        $page = null
    ) {
        $builder = $this->builder($table);
        $builder->select($select);
        
        // Joins
        foreach ($joins as $join) {
            $builder->join($join['table'], $join['condition'], $join['type'] ?? 'inner');
        }

        // Conditions
        foreach ($conds as $cond) {
            $field = $cond['field'];
            $value = $cond['value'];
            $type = $cond['type'] ?? 'where';

            if ($type === 'in') {
                $builder->whereIn($field, $value);
            } elseif ($type === 'not in') {
                $builder->whereNotIn($field, $value);
            } elseif ($type === 'like') {
                $builder->like($field, $value);
            } elseif ($type === 'or') {
                $builder->orWhere($field, $value);
            } elseif ($type === 'or_gs') {
                $builder->groupStart();
                $builder->orWhere($field, $value);
            } elseif ($type === 'or_ge') {
                $builder->orWhere($field, $value);
                $builder->groupEnd();
            } else {
                $builder->where($field, $value);
            }
        }

        // Group by and order by
        if ($groupby) {
            $builder->groupBy($groupby);
        }
        if ($orderby) {
            $builder->orderBy($orderby);
        }
        if ($perpage !== null && $page !== null) {
            $builder->limit($perpage, $page);
        }

        return $builder->get();
    }

    public function getMasterbyparamjoin($table, $conds = [], $fieldnm, $fieldval, $lan = 'e', $selectID = null, $joins = [])
    {
        // Start building the query
        $builder = $this->db->table($table);

        // Handle joins
        if (is_array($joins) && count($joins) > 0) {
            foreach ($joins as $ijoin => $ijoinval) {
                $array = explode('|', $ijoin);
                $builder->join($array[0], $ijoinval, $array[1]);
            }
        }

        // Handle conditions
        if (is_array($conds) && count($conds) > 0) {
            foreach ($conds as $icon => $iconval) {
                $builder->where($icon, $iconval);
            }
        }

        // Execute the query
        $query = $builder->get();
        $results = $query->getResult();

        // Start constructing the options string
        $str = '<option value="">Select an Option</option>';
        $val_Col = $fieldval;
        $columnName = $fieldnm;

        // Build options
        foreach ($results as $row) {
            $selected = ($row->$val_Col == $selectID) ? "selected" : "";
            $str .= '<option value="' . esc($row->$val_Col) . '" ' . $selected . '>' . esc($row->$columnName) . '</option>';
        }

        return $str;
    }

    public function get_users()
    {
        $schemaname = $this->schema;
        $current_date = date('Y-m-d');
        $user_id = $this->sessdata['user_id'];

        $query = $this->db->query("SELECT lefttable.user_id, organisation 
                                    FROM public.get_right_panel_data('$schemaname', '$current_date', $user_id) AS lefttable 
                                    LEFT JOIN public.user_login AS ul ON lefttable.user_id = ul.user_id 
                                    WHERE lefttable.group_id = 2 
                                    AND lefttable.deviceid IS NOT NULL 
                                    GROUP BY lefttable.user_id, organisation 
                                    ORDER BY organisation ASC");

        return $query->getResult();
    }

}