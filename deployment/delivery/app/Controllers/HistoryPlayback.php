<?php

namespace App\Controllers;

use CodeIgniter\Controller;
use CodeIgniter\Database\Query\Builder;

class HistoryPlayback extends Controller
{
    protected $schema;
    protected $sessdata;

    public function __construct()
    {
        helper(['url', 'form']);
        
        // Load the session library
        $session = \Config\Services::session();
        
        // Check if the session exists
        if ($session->has('login_sess_data')) {
            $this->sessdata = $session->get('login_sess_data');
            $this->schema = $this->sessdata['schemaname'];
        }
    
        $this->db = \Config\Database::connect();
    }

    public function getDeviceCoordinates()
    {

        $from_date = date("Y-m-d H:i:s", strtotime($this->request->getPost('todate')));
        $to_date = date("Y-m-d", strtotime($this->request->getPost('todate')));
        $from_time = date("H:i:s", strtotime($this->request->getPost('fromdate')));
        $to_time = date("H:i:s", strtotime($this->request->getPost('todate')));
        $from_datetime = date("Y-m-d H:i:s", strtotime($this->request->getPost('fromdate')));
        $to_datetime = date("Y-m-d H:i:s", strtotime($this->request->getPost('todate')));
        $deviceid = trim($this->request->getPost('deviceid'));

        // Query to get coordinates
        $query = $this->db->query("SELECT * FROM public.get_positional_record_of_eatch_divice_date_to_date(?, ?, ?) ORDER BY currentdate, currenttime ASC", [$deviceid, $from_datetime, $to_datetime]);
        $getcoordinates = $query->getResult();

        // Process coordinates
        $return_arr = $return_arr1 = [];
        if (!empty($getcoordinates)) {
            $return_arr = $getcoordinates;
            $coord_length = count($getcoordinates);
            $k = $st = 1;
            $en = $st + 99;
            for ($z = $st; $z <= $coord_length; ) {
                $getsnaptoroad = $this->getSnapToRoad($getcoordinates, $st, $en);
                $getsnaptoroad = json_decode($getsnaptoroad);
                
                if (!empty($getsnaptoroad)) {
                    foreach ($getsnaptoroad->snappedPoints as $getsnaptoroad_each) {
                        if ($k < 99) {
                            $return_arr1[$k - 1] = (object)[
                                'latitude' => $getsnaptoroad_each->location->latitude,
                                'longitude' => $getsnaptoroad_each->location->longitude,
                                'deviceid' => $getcoordinates[$getsnaptoroad_each->originalIndex]->deviceid,
                                'currentdate' => $getcoordinates[$getsnaptoroad_each->originalIndex]->currentdate,
                                'currenttime' => $getcoordinates[$getsnaptoroad_each->originalIndex]->currenttime,
                                'trakerspeed' => $getcoordinates[$getsnaptoroad_each->originalIndex]->trakerspeed,
                            ];
                        } else {
                            if (isset($getcoordinates[$getsnaptoroad_each->originalIndex + $z]->deviceid) && $getcoordinates[$getsnaptoroad_each->originalIndex + $z]->deviceid != '') {
                                $return_arr1[$k - 1] = (object)[
                                    'latitude' => $getsnaptoroad_each->location->latitude,
                                    'longitude' => $getsnaptoroad_each->location->longitude,
                                    'deviceid' => $getcoordinates[$getsnaptoroad_each->originalIndex + $z]->deviceid,
                                    'currentdate' => $getcoordinates[$getsnaptoroad_each->originalIndex + $z]->currentdate,
                                    'currenttime' => $getcoordinates[$getsnaptoroad_each->originalIndex + $z]->currenttime,
                                    'trakerspeed' => $getcoordinates[$getsnaptoroad_each->originalIndex + $z]->trakerspeed,
                                ];
                            }
                        }
                        $k++;
                    }
                }
                $st = $z = $z + 99;
                $en = $st + 99;
            }
        }

        $historySummaryQuery = $this->db->query("SELECT * FROM public.get_histry_play_data_summary(?, ?, ?)", [$deviceid, $from_datetime, $to_datetime]);
        $get_history_summary = $historySummaryQuery->getResult();

        // Get history details
        $get_history_details_final = [];
        $get_history_details = [];  // You can replace this with actual query or model call to fetch history details.
        
        // If there are history details, process them
        if (count($get_history_details) > 0) {
            foreach ($get_history_details as $i => $get_history_details_each) {
                $get_history_details_final[$i] = [
                    'id' => $get_history_details_each->id,
                    'user_id' => $get_history_details_each->user_id,
                    'deviceid' => $get_history_details_each->deviceid,
                    'currentdate' => $get_history_details_each->currentdate,
                    'currenttime' => $get_history_details_each->currenttime,
                    'event_list' => $get_history_details_each->event_list,
                    'latitude' => $get_history_details_each->latitude,
                    'longitude' => $get_history_details_each->longitude,
                    'geom' => $get_history_details_each->geom,
                    'geoanceid' => $get_history_details_each->geoanceid,
                    'geofancegeom' => $get_history_details_each->geofancegeom,
                    'geofancegeomwithbuffer' => $get_history_details_each->geofancegeomwithbuffer,
                    'geomtype' => $get_history_details_each->geomtype,
                    'lonlat' => $get_history_details_each->lonlat,
                    'refname' => $get_history_details_each->refname,
                    'faetureid' => $get_history_details_each->id
                ];

                // Check if event contains SOS, ALERT, or CALL
                if (strpos($get_history_details_each->event_list, 'SOS') !== false) {
                    $alertresult = $db->query("SELECT * FROM public.get_sos_record_of_eatch_divice({$this->sessdata['parent_id']},{$this->schema}) WHERE currentdate = ? AND deviceid = ? AND currenttime = ?", [
                        $get_history_details_each->currentdate, $get_history_details_each->deviceid, $get_history_details_each->currenttime
                    ])->getRow();
                    $get_history_details_final[$i]['faetureid'] = $alertresult->sosid;
                } elseif (strpos($get_history_details_each->event_list, 'ALERT') !== false) {
                    $alertresult = $db->query("SELECT * FROM public.get_alart_record_of_eatch_divice({$this->sessdata['parent_id']},{$this->schema}) WHERE currentdate = ? AND deviceid = ? AND currenttime = ?", [
                        $get_history_details_each->currentdate, $get_history_details_each->deviceid, $get_history_details_each->currenttime
                    ])->getRow();
                    $get_history_details_final[$i]['faetureid'] = $alertresult->sosid;
                } elseif (strpos($get_history_details_each->event_list, 'CALL') !== false) {
                    $alertresult = $db->query("SELECT * FROM public.get_call_record_of_eatch_divice({$this->sessdata['parent_id']},{$this->schema}) WHERE currentdate = ? AND deviceid = ? AND currenttime = ?", [
                        $get_history_details_each->currentdate, $get_history_details_each->deviceid, $get_history_details_each->currenttime
                    ])->getRow();
                    $get_history_details_final[$i]['faetureid'] = $alertresult->sosid;
                }
            }
        }

        // Prepare the response data
        $result = [
            'history_details' => $get_history_details_final,
            'getcoordinates' => $return_arr,
            'getcoordinates1' => $return_arr1,
            'history_summary' => $get_history_summary,
            'getpoledata' => [],
            'getpolelinedata' => []
        ];

        return $this->response->setJSON($result);
    }

    // Define the getSnapToRoad method here (assuming it’s an API call to some service)
    private function getSnapToRoad($coordinates, $st, $en)
    {
        // Example code (Replace with actual API logic)
        return json_encode(['snappedPoints' => []]);
    }
}
