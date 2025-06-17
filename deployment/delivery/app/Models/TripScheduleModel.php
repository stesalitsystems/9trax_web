<?php

namespace App\Models;

use CodeIgniter\Model;

class TripScheduleModel extends Model
{
    protected $table;
    protected $primaryKey = 'schedule_id';
    protected $allowedFields = [
        'deviceid', 'devicename', 'imeino', 'section_id', 'pwi_id', 'device_type',
        'expected_start_date', 'expected_start_time',
        'expected_end_date', 'expected_end_time',
        'active'
    ];

    public function __construct()
    {
        parent::__construct();
        $session = session()->get('login_sess_data');
        $schema = $session ? $session['schemaname'] : 'public'; // Default to 'public' schema
        $this->setTable("{$schema}.trip_schedule"); // ✅ Properly set table name
    }
}
