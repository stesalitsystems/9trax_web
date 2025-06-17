<?php

namespace App\Models;

use CodeIgniter\Model;

class TripScheduleDetailsModel extends Model
{
    protected $table;
    protected $primaryKey = 'schedule_details_id';
    protected $allowedFields = [
        'schedule_id', 'expected_stpole', 'expected_stlat', 'expected_stlon',
        'expected_start_datetime', 'expected_endpole', 'expected_endlat', 
        'expected_endlon', 'expected_end_datetime', 'trip_status', 'expected_distance', 'trip_no'
    ];

    public function __construct()
    {
        parent::__construct();
        $session = session()->get('login_sess_data');
        $schema = $session ? $session['schemaname'] : 'public'; // Default to 'public' schema
        $this->table = "{$schema}.trip_schedule_details";
    }
}
