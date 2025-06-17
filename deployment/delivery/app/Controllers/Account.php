<?php

namespace App\Controllers;

use App\Models\AccountModel;
use CodeIgniter\Controller;
use CodeIgniter\Validation\Validation;

class Account extends Controller
{
    protected $accountModel;

    public function __construct()
    {
        $this->accountModel = new AccountModel();
        helper(['form', 'url', 'master', 'communication']);
        $this->session = \Config\Services::session();
    }

    public function register()
    {
        return view('account/register');
    }

    public function newUserRegistration()
    {
        // Validation rules
        $validation =  \Config\Services::validation();

        $validation->setRules([
            'name' => 'required|trim',
            'mobile' => 'required|trim',
            'email' => 'required|trim|valid_email',
            'address' => 'required|trim',
            'pin' => 'required|trim',
            'deviceid' => 'required|trim',
            'deviceimei' => 'required|trim',
        ]);

        $return = [
            'error' => true,
            'msg'   => '',
        ];

        // Validate input
        if (!$this->validate($validation->getRules())) {
            $return['msg'] = implode(' ', $validation->getErrors());
            return $this->response->setJSON($return);
        } else {
            $deviceCheck = $this->accountModel->callbackCheckDevice($this->request->getPost('deviceid'), $this->request->getPost('deviceimei'));

            if ($deviceCheck) {
                $data = [
                    'name' => $this->request->getPost('name'),
                    'mobile' => $this->request->getPost('mobile'),
                    'email' => $this->request->getPost('email'),
                    'address' => $this->request->getPost('address'),
                    'pin' => $this->request->getPost('pin'),
                    'deviceid' => $this->request->getPost('deviceid'),
                    'deviceimei' => $this->request->getPost('deviceimei'),
                    'ekyc1' => $this->request->getPost('ekyc1'),
                    'ekyc2' => $this->request->getPost('ekyc2'),
                    'imgfolder' => $this->request->getPost('imgfolder'),
                ];
                
                $this->sendMailToSuperAdmin($data);
                
                // $return['error'] = false;
                $return['msg'] = 'Thanks for registering with us.';
            } else {
                $return['msg'] = 'Invalid Device!!';
            }

            return $this->response->setJSON($return);
        }
    }

    protected function sendMailToSuperAdmin($data)
    {
        $file1 = WRITEPATH . 'uploads/account/' . $data['imgfolder'] . '/' . $data['ekyc1'];
        $file2 = !empty($data['ekyc2']) ? WRITEPATH . 'uploads/account/' . $data['imgfolder'] . '/' . $data['ekyc2'] : null;

        // Load the email view
        $superadmin_msg = view('email/register_after_mail_superadmin', $data);

        // Use a helper function or service to send the email
        $to = 'notification@9trax.com';
        $cc = 'help@9trax.com,sales@9trax.com';
        $subject = 'Request for Account';

        $emailService = \Config\Services::email();

        $emailService->setFrom('your_email@example.com', 'Your Name'); // Set your from email
        $emailService->setTo($to);
        $emailService->setCC($cc);
        $emailService->setSubject($subject);
        $emailService->setMessage($superadmin_msg);

        // Attach files if they exist
        if (file_exists($file1)) {
            $emailService->attach($file1);
        }
        if ($file2 && file_exists($file2)) {
            $emailService->attach($file2);
        }

        // Send email and check for success
        if (!$emailService->send()) {
            // Handle the error
            log_message('error', $emailService->printDebugger());
        }
    }

    public function imageupload1($imgfolder) {
        $return = [];
    
        // Check if files are uploaded
        if ($this->request->getFile('files')->isValid() && !$this->request->getFile('files')->hasMoved()) {
            $file = $this->request->getFile('files');
            $path = "uploads/account/" . $imgfolder . "/";
    
            // Create the directory if it doesn't exist
            if (!is_dir($path)) {
                mkdir($path, 0755, true);
            }
    
            // Define allowed file types
            $allowedTypes = ['image/jpg', 'image/jpeg', 'image/png'];
    
            // Validate file type
            if (in_array($file->getClientMimeType(), $allowedTypes)) {
                $filename = $file->getName();
    
                // Move the file to the desired directory
                if ($file->move($path, $filename)) {
                    $return['succ'] = 1;
                    $return['filename'] = $filename;
                } else {
                    $return['succ'] = 0;
                    $return['filename'] = strip_tags($file->getErrorString());
                }
            } else {
                $return['succ'] = 0;
                $return['filename'] = 'Invalid file type. Only JPG and PNG are allowed.';
            }
        } else {
            $return['succ'] = 0;
            $return['filename'] = 'No file uploaded or the file has already been moved.';
        }
    
        return $this->response->setJSON($return);
    }    

    public function imageupload2($imgfolder)
    {
        // Check if the request is valid
        if ($this->request->getFile('files2')) {
            $file = $this->request->getFile('files2');

            // Check if the file is valid
            if ($file->isValid() && !$file->hasMoved()) {
                // Define the upload path
                $path = WRITEPATH . "uploads/account/" . $imgfolder . "/";
                
                // Create the directory if it does not exist
                if (!is_dir($path)) {
                    mkdir($path, 0755, true);
                }

                // Set the new filename and move the uploaded file
                $filename = $file->getRandomName();
                if ($file->move($path, $filename)) {
                    $return['succ'] = 1;
                    $return['filename'] = $filename;
                } else {
                    $return['succ'] = 0;
                    $return['filename'] = strip_tags($file->getErrorString());
                }
            } else {
                $return['succ'] = 0;
                $return['filename'] = 'No valid file uploaded.';
            }
        } else {
            $return['succ'] = 0;
            $return['filename'] = 'No file uploaded.';
        }

        // Return the response as JSON
        return $this->response->setJSON($return);
    }

    function forgetpassword() {
		return view('account/forgetpassword');
	}

    public function passwordReset()
    {
        // Validate the input
        $validation = \Config\Services::validation();
        $validation->setRules([
            'username' => 'required|trim'
        ]);

        if (!$this->validate($validation->getRules())) {
            $return['msg'] = implode(', ', $validation->getErrors());
            return $this->response->setJSON($return);
        } else {
            $username = $this->request->getPost('username');
            $result = $this->accountModel->checkUsername($username);

            if ($result) {
                $this->accountModel->passwordReset($username);
                $data = ['username' => $username];

                // Load the view for the email content
                $superadmin_msg = view('email/resetpassword_mail', $data);
                $this->sendEmail('notification@9trax.com', $username, 'Password Reset', $superadmin_msg);

                $return['msg'] = 'Password has been reset successfully. Please check your mail.';
            } else {
                $return['msg'] = 'Invalid Username!!';
            }

            return $this->response->setJSON($return);
        }
    }

    private function sendEmail($from, $to, $subject, $message)
    {
        $email = \Config\Services::email();
        $email->setFrom($from);
        $email->setTo($to);
        $email->setSubject($subject);
        $email->setMessage($message);

        if (!$email->send()) {
            // Handle email sending errors if necessary
        }
    }
}
