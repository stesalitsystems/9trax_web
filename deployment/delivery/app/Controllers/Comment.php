<?php

namespace App\Controllers;

use App\Models\DevicesModel;
use App\Models\ControlCentreModel;
use CodeIgniter\Controller;
use App\Libraries\Notification;

class Comment extends Controller
{
    protected $sessdata;
    protected $schema;

    public function __construct()
    {
        $session = session();
        if ($session->has('login_sess_data')) {
            $this->sessdata = $session->get('login_sess_data');
            $this->schema = $this->sessdata['schemaname'];
        }
        $this->db = \Config\Database::connect();
    }

    public function marketingcomment()
    {
        if (!session()->has('login_sess_data')) {
            return redirect()->to('/');
        }

        $data['sessdata'] = $this->sessdata;
        $data['page_title'] = "Marketing Message";

        if ($this->request->getPost('add') == "add") {
            $appLoggedUserDetails = $this->db->table('public.user_token_app')
                ->where(['closetime' => null, 'active' => 1])
                ->get()
                ->getResult();

            foreach ($appLoggedUserDetails as $user) {
                $token = $user->token_id;
                $notification = new Notification();
                $firebase_token = base64_encode($token);
                $message = base64_encode($this->request->getPost('message'));
                $title = base64_encode($this->request->getPost('title'));
                $post = [
                    'firebase_token' => $firebase_token,
                    'message' => $message,
                    'title' => $title
                ];

                $ch = curl_init('http://103.233.79.35/pt/cron/googlenotification.php');
                curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
                curl_setopt($ch, CURLOPT_POSTFIELDS, $post);
                $response = curl_exec($ch);
                //var_dump($response);
                curl_close($ch);
            }
        }

        $data['middle'] = view('comment/marketingcomment', $data);
        return view('mainlayout', $data);
    }
}
