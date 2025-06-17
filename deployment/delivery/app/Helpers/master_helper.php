<?php
// namespace App\Helpers;

use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;


function send_socket($message)
{
    $output = array();
    echo $message . "<br/>";

    if (!empty($message)) {
        $confPort = getenv('configuration_port');
        $host = "120.138.8.188";
        $port = $confPort;

        // Create socket
        $socket = socket_create(AF_INET, SOCK_STREAM, 0);
        if ($socket === false) {
            log_message('error', 'Could not create socket: ' . socket_strerror(socket_last_error()));
            return ['flag' => false];
        }

        // Connect to server
        $result = socket_connect($socket, $host, $port);
        if ($result === false) {
            log_message('error', 'Could not connect to server: ' . socket_strerror(socket_last_error($socket)));
            socket_close($socket);
            return ['flag' => false];
        }

        // Encode the message
        $message = mb_convert_encoding($message, 'UTF-8', 'HTML-ENTITIES');

        // Send string to server
        $bytesWritten = socket_write($socket, $message, strlen($message));
        if ($bytesWritten === false) {
            log_message('error', 'Could not send data to server: ' . socket_strerror(socket_last_error($socket)));
            socket_close($socket);
            return ['flag' => false];
        }

        // Close socket
        socket_close($socket);
        $output['flag'] = true;
    } else {
        $output['flag'] = false;
    }

    return $output;
}



function send_socket_test($message)
{
    $output = array();

    $confPort = getenv('configuration_port');

    if (!empty($message)) {
        $host = "120.138.8.188";
        $port = $confPort;
        // Create socket
        $socket = socket_create(AF_INET, SOCK_STREAM, 0);
        if ($socket === false) {
            log_message('error', 'Could not create socket: ' . socket_strerror(socket_last_error()));
            return ['flag' => false];
        }

        // Connect to server
        $result = socket_connect($socket, $host, $port);
        if ($result === false) {
            log_message('error', 'Could not connect to server: ' . socket_strerror(socket_last_error($socket)));
            socket_close($socket);
            return ['flag' => false];
        }

        // Encode the message
        $message = mb_convert_encoding($message, 'UTF-8', 'HTML-ENTITIES');

        // Send string to server
        $bytesWritten = socket_write($socket, $message, strlen($message));
        if ($bytesWritten === false) {
            log_message('error', 'Could not send data to server: ' . socket_strerror(socket_last_error($socket)));
            socket_close($socket);
            return ['flag' => false];
        }

        // Close socket
        socket_close($socket);
        $output['flag'] = true;
    } else {
        $output['flag'] = false;
    }

    return $output;
}

function excelDownload(array $data, string $filename = 'test.xlsx') {
    // Create a new Spreadsheet object
    $spreadsheet = new Spreadsheet();

    // Set active sheet index to the first sheet
    $sheet = $spreadsheet->getActiveSheet();
    
    // Name the worksheet
    $sheet->setTitle('worksheet1');

    $i = 1;

    // Populate the spreadsheet with data
    foreach ($data as $key => $val) {
        foreach ($val as $iKey => $dval) {
            $cell_no = $iKey . $i;
            $sheet->setCellValue($cell_no, $dval);

            if ($key == 0) {
                // Change the font size
                $sheet->getStyle($cell_no)->getFont()->setSize(12);
                // Make the font bold
                $sheet->getStyle($cell_no)->getFont()->setBold(true);
                // Set column dimension
                $sheet->getColumnDimension($iKey)->setWidth(20);
                // Set alignment to center
                $sheet->getStyle($cell_no)->getAlignment()->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER);
            }
        }
        $i++;
    }

    // Set headers for the download
    header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet'); // Mime type for .xlsx
    header('Content-Disposition: attachment; filename="' . $filename . '"'); // Tell browser the file name
    header('Cache-Control: max-age=0'); // No cache

    // Create a writer for the spreadsheet
    $writer = new Xlsx($spreadsheet);

    // Clear the output buffer
    ob_end_clean();

    // Save the file to the output
    $writer->save('php://output');
    exit; // Ensure no further output is sent after the file
}


function getSubUsers($user_id, $db) {
    $subUsers = [];

    $sql = "WITH RECURSIVE sub_users AS (
                SELECT user_id FROM public.user_login WHERE parent_id = ?
                UNION
                SELECT ul.user_id FROM public.user_login ul
                INNER JOIN sub_users su ON ul.parent_id = su.user_id
            )
            SELECT user_id FROM sub_users";

    $query = $db->query($sql, [$user_id])->getResult();

    foreach ($query as $row) {
        $subUsers[] = $row->user_id;
    }

    return $subUsers;
}
