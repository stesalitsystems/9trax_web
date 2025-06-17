<?php

namespace App\Commands;

use CodeIgniter\CLI\BaseCommand;
use CodeIgniter\CLI\CLI;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use PhpOffice\PhpSpreadsheet\Cell\Coordinate;
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;
use App\Libraries\MakePDF;


class ExportReportCommand extends BaseCommand
{
    protected $group       = 'Reports';
    protected $name        = 'report:export';
    protected $description = 'Exports query results as an XLSX file (with dynamic date filtering) and emails it.';

    public function run(array $params)
    {
        // Set the timezone to IST (Asia/Kolkata)
        date_default_timezone_set('Asia/Kolkata');

        // Determine the current hour (in 24-hour format)
        $currentHour = date('H'); // e.g., "10" or "18"

       
        // Set variables based on the run time.
        $desiredUserType = '';
        $startTime = '';
        $endTime   = '';
        
        if ($currentHour === '10') {
            // For the 10:00 AM run:
            // - Use usertype 'Patrolman'
            // - Time range: current date 00:00:00 to current date 10:00:00
            $desiredUserType = 'Patrolman';
            $startTime = date('Y-m-d') . " 00:00:00";
            $endTime   = date('Y-m-d') . " 10:00:00";
        } elseif ($currentHour === '18') {
            // For the 6:00 PM run:
            // - Use usertype 'Keyman'
            // - Time range: current date 06:00:00 to current date 15:00:00
            $desiredUserType = 'Keyman';
            $startTime = date('Y-m-d') . " 05:00:00";
            $endTime   = date('Y-m-d') . " 15:00:00";
        } else {
            CLI::write("This command should only be run at 10:00 or 18:00. Exiting.", 'red');
            return;
        }
        
        // Get the database connection
        $db = \Config\Database::connect();

        // Build the SQL query with dynamic date filters and usertype.

        $arr['srdenhqrjt@gmail.com']['user_id'] = 486;
        $arr['srdenhqrjt@gmail.com']['user_type'] = 'All';
        $arr['srdenhqrjt@gmail.com']['parent_id'] = 0; // Level 1

        // $arr['deneastrjt@gmail.com']['user_id'] = 493;
        // $arr['deneastrjt@gmail.com']['user_type'] = 'DEN';
        // $arr['deneastrjt@gmail.com']['parent_id'] = 486; // Level 2

        // $arr['denwrjt11@gmail.com']['user_id'] = 494;
        // $arr['denwrjt11@gmail.com']['user_type'] = 'DEN';
        // $arr['denwrjt11@gmail.com']['parent_id'] = 486; // Level 2

        // $arr['adensunr@gmail.com']['user_id'] = 495;
        // $arr['adensunr@gmail.com']['user_type'] = 'PWI';
        // $arr['adensunr@gmail.com']['parent_id'] = 493; // Level 3
        $arr['ssepwayltr@gmail.com']['user_id'] = 500;
        $arr['ssepwayltr@gmail.com']['user_type'] = 'Section';
        $arr['ssepwayltr@gmail.com']['parent_id'] = 495; // Level 
        $arr['ssepwaysunr@gmail.com']['user_id'] = 501; 
        $arr['ssepwaysunr@gmail.com']['user_type'] = 'Section';
        $arr['ssepwaysunr@gmail.com']['parent_id'] = 495; // Level 


        // $arr['adenwkr@gmail.com']['user_id'] = 496;
        // $arr['adenwkr@gmail.com']['user_type'] = 'PWI';
        // $arr['adenwkr@gmail.com']['user_id'] = 493; // Level 3
        $arr['ssepwaythan@gmail.com']['user_id'] = 502;
        $arr['ssepwaythan@gmail.com']['user_type'] = 'Section';
        $arr['ssepwaythan@gmail.com']['parent_id'] = 496; // Level 4
        $arr['ssep.waywkr218@gmail.com']['user_id'] = 503;
        $arr['ssep.waywkr218@gmail.com']['user_type'] = 'Section';
        $arr['ssep.waywkr218@gmail.com']['parent_id'] = 496; // Level 4

         // $arr['adenrjt@gmail.com']['user_id'] = 497;
        // $arr['adenrjt@gmail.com']['user_type'] = 'PWI';
        // $arr['adenrjt@gmail.com']['parent_id'] = 493; // Level 3
        $arr['ssepwayrjte@gmail.com']['user_id'] = 504;
        $arr['ssepwayrjte@gmail.com']['user_type'] = 'Section';
        $arr['ssepwayrjte@gmail.com']['parent_id'] = 497; // Level 4
        $arr['ssepwaymvi@gmail.com']['user_id'] = 505;
        $arr['ssepwaymvi@gmail.com']['user_type'] = 'Section';
        $arr['ssepwaymvi@gmail.com']['parent_id'] = 497; // Level 4

        // $arr['adenejamrjt@gmail.com']['user_id'] = 498;
        // $arr['adenejamrjt@gmail.com']['user_type'] = 'PWI';
        // $arr['adenejamrjt@gmail.com']['parent_id'] = 494; // Level 3
        $arr['ssepwayrjtw@gmail.com']['user_id'] = 506;
        $arr['ssepwayrjtw@gmail.com']['user_type'] = 'Section';
        $arr['ssepwayrjtw@gmail.com']['parent_id'] = 498; // Level 4
        $arr['ssepwayhapa@gmail.com']['user_id'] = 507;
        $arr['ssepwayhapa@gmail.com']['user_type'] = 'Section';
        $arr['ssepwayhapa@gmail.com']['parent_id'] = 498; // Level 4

        // $arr['adenwjamrjt@gmail.com']['user_id'] = 499;
        // $arr['adenwjamrjt@gmail.com']['user_type'] = 'PWI';
        // $arr['adenwjamrjt@gmail.com']['parent_id'] = 494; // Level 3
        $arr['ssepwaykmbl@gmail.com']['user_id'] = 508;
        $arr['ssepwaykmbl@gmail.com']['user_type'] = 'Section';
        $arr['ssepwaykmbl@gmail.com']['parent_id'] = 499; // Level 4
        $arr['ssepwaydwk@gmail.com']['user_id'] = 509;
        $arr['ssepwaydwk@gmail.com']['user_type'] = 'Section';
        $arr['ssepwaydwk@gmail.com']['parent_id'] = 499; // Level 4
        // CLI::write(json_encode($arr), 'green');
        foreach($arr as $key => $val){
            $userId = $val['user_id'];
            $sql_user = '';
            if (!empty($val['user_type']) && $val['user_type'] != "All") {
                $sql_user = " AND p_ul.user_id = ".$userId;
                
            }



        
            $sql = <<<SQL
                WITH base_data AS (
                                SELECT 
                                    ul.organisation,
                                    ul.user_id,
                                    p_ul.organisation AS parent_organisation,
                                    md.serial_no AS device_imei,
                                    msd.device_name AS device_name,
                                    CASE 
                                        WHEN lower(msd.device_name) LIKE '%stock%' 
                                            OR msd.device_name IS NULL 
                                            OR trim(msd.device_name) = '' 
                                        THEN 'Not Allocated'
                                        ELSE 'Allocated'
                                    END AS allocation_status,
                                    t.imeino AS trip_imeino,
                                    su.distance_travelled,
                                    t.totaldistancetravel,
                                    CASE 
                                        WHEN (
                                                TRIM(su.stpole) = TRIM(t.stpole)
                                                OR TRIM(su.stpole) = ANY (
                                                    ARRAY (
                                                        SELECT trim(elem)
                                                        FROM unnest(string_to_array(t.beats_covered, ',')) AS elem
                                                    )
                                                )
                                            )
                                            AND (
                                                TRIM(su.endpole) = TRIM(t.endpole)
                                                OR TRIM(su.endpole) = ANY (
                                                    ARRAY (
                                                        SELECT trim(elem)
                                                        FROM unnest(string_to_array(t.beats_covered, ',')) AS elem
                                                    )
                                                )
                                            )
                                        THEN 'Patroling Completed'
                                        ELSE NULL
                                    END AS trip_status ,
                                    CASE WHEN t.avg_speed >= 5 THEN 1 ELSE 0 END AS has_break_speed 
                                FROM public.master_device_assign mda
                                LEFT JOIN public.user_login ul 
                                    ON ul.user_id = mda.user_id
                                LEFT JOIN public.master_device_details md 
                                    ON md.id = mda.deviceid
                                LEFT JOIN stes.master_device_setup msd 
                                    ON msd.deviceid = mda.deviceid
                                LEFT JOIN public.user_login p_ul 
                                    ON p_ul.user_id = mda.parent_id
                                LEFT JOIN (
                                    SELECT stes.tbl_trip.* 
                                    FROM stes.tbl_trip 
                                    LEFT JOIN stes.tbl_device_schedule_updated su 
                                        ON su.imeino = tbl_trip.imeino 
                    WHERE ((sttimestamp BETWEEN '$startTime' AND '$endTime')
                        OR (endtimestamp BETWEEN '$startTime' AND '$endTime')
                        OR (sttimestamp < '$startTime' AND endtimestamp > '$endTime'))
                    AND  su.usertype = '$desiredUserType'
                ) t 
                                    ON t.deviceid = mda.deviceid
                                LEFT JOIN stes.tbl_device_schedule_updated su 
                                    ON su.imeino = md.serial_no AND su.usertype = '$desiredUserType'    
                WHERE 
                    ul.group_id = 2
                    AND mda.active = 1
                    AND su.usertype = '$desiredUserType'
                    $sql_user
                    ),
                                    device_status AS (
                                        SELECT 
                                            organisation,
                                            user_id,
                                            parent_organisation,
                                            allocation_status,
                                            device_imei,
                                            device_name,
                                            MAX(CASE WHEN trip_imeino IS NOT NULL THEN 1 ELSE 0 END) AS has_trip,
                                            MAX(CASE WHEN trip_status = 'Patroling Completed' THEN 1 ELSE 0 END) AS covered,
                                            MAX(COALESCE(distance_travelled, 0)) AS expected_distance ,
                                            SUM(COALESCE(totaldistancetravel, 0)) AS actual_distance,
                                            MAX(has_break_speed) AS has_break_speed
                                        FROM base_data
                                        GROUP BY organisation, user_id, parent_organisation, allocation_status, device_imei, device_name
                                    )
                                    SELECT 
                                        organisation,
                                        user_id,
                                        parent_organisation,
                                        COUNT(CASE WHEN allocation_status = 'Not Allocated' THEN 1 END) AS not_allocated_count,
                                        string_agg(
                                            CASE WHEN allocation_status = 'Not Allocated' THEN device_imei END, ',' 
                                            ORDER BY device_imei
                                        ) AS not_allocated_imeino,
                                        string_agg(
                                            CASE WHEN allocation_status = 'Not Allocated' THEN device_name END, ',' 
                                            ORDER BY device_imei
                                        ) AS not_allocated_devicename,
                                        COUNT(CASE 
                                            WHEN allocation_status = 'Allocated' 
                                            AND has_trip = 0 
                                            THEN 1 
                                        END) AS device_off_count,
                                        string_agg(
                                            CASE 
                                                WHEN allocation_status = 'Allocated' 
                                                AND has_trip = 0 
                                                THEN device_imei 
                                            END, ',' 
                                            ORDER BY device_imei
                                        ) AS device_off_imeino,
                                        string_agg(
                                            CASE 
                                                WHEN allocation_status = 'Allocated' 
                                                AND has_trip = 0 
                                                THEN device_name 
                                            END, ',' 
                                            ORDER BY device_imei
                                        ) AS device_off_devicename,
                                        COUNT(CASE 
                                            WHEN allocation_status = 'Allocated' 
                                            AND has_trip = 1 
                                            AND (covered = 1 OR (covered = 0 AND actual_distance >= expected_distance)) 
                                            THEN 1 
                                        END) AS beats_covered_count,

                                        string_agg(
                                            CASE 
                                            WHEN allocation_status = 'Allocated'
                                            AND has_trip = 1 
                                            AND (covered = 1 OR (covered = 0 AND actual_distance >= expected_distance)) 
                                            THEN device_imei 
                                            END, ',' 
                                            ORDER BY device_imei
                                        ) AS beats_covered_imeino,
                                        string_agg(
                                            CASE 
                                            WHEN allocation_status = 'Allocated'
                                            AND has_trip = 1 
                                            AND (covered = 1 OR (covered = 0 AND actual_distance >= expected_distance)) 
                                            THEN device_name 
                                            END, ',' 
                                            ORDER BY device_imei
                                        ) AS beats_covered_devicename,
                                        
                                        COUNT(CASE 
                                            WHEN allocation_status = 'Allocated' 
                                            AND has_trip = 1 
                                            AND covered = 0 
                                            AND actual_distance < expected_distance 
                                            THEN 1 
                                        END) AS beats_not_covered_count,
                                        string_agg(
                                            CASE 
                                            WHEN allocation_status = 'Allocated'
                                            AND has_trip = 1 
                                            AND covered = 0 
                                            AND actual_distance < expected_distance 
                                            THEN device_imei 
                                            END, ',' 
                                            ORDER BY device_imei
                                        ) AS beats_not_covered_imeino,
                                        string_agg(
                                            CASE 
                                            WHEN allocation_status = 'Allocated'
                                            AND has_trip = 1 
                                            AND covered = 0 
                                            AND actual_distance < expected_distance 
                                            THEN device_name 
                                            END, ',' 
                                            ORDER BY device_imei
                                        ) AS beats_not_covered_devicename ,
                                        COUNT(CASE WHEN has_break_speed = 1 THEN 1 END) AS break_speed_device_count,
                                        string_agg(CASE WHEN has_break_speed = 1 THEN device_imei END, ',' ORDER BY device_imei) AS break_speed_device_imeino,
                                        string_agg(CASE WHEN has_break_speed = 1 THEN device_name END, ',' ORDER BY device_imei) AS break_speed_device_name
                                    FROM device_status
                                    GROUP BY organisation, user_id, parent_organisation;
                SQL;

            //CLI::write($sql);
            // Execute the query
            $query = $db->query($sql);
            $results = $query->getResultArray();

            // Create a new Spreadsheet object
            $spreadsheet = new Spreadsheet();
            $sheet = $spreadsheet->getActiveSheet();

            // Set Metadata: Report Type & Generation Date
            $timestamp = date("Y-m-d H:i:s");
            $reportType = "Automated Report - $desiredUserType";

            // Write Report Type & Generation Date
            $sheet->setCellValue("A1", "Report Type: $reportType");
            $sheet->setCellValue("A2", "Generated On: $timestamp");

            // Apply Styling to Metadata
            $sheet->mergeCells("A1:E1"); // Merge cells across columns
            $sheet->mergeCells("A2:E2");
            $sheet->getStyle("A1")->getFont()->setBold(true)->setSize(14);
            $sheet->getStyle("A2")->getFont()->setItalic(true);

            // Set header row (only the required columns)
            $headers = [
                'PWAY', 
                'Section', 
                'Device Off', 
                'Beats Covered', 
                'Beats Not Covered',
                'OverSpeed'
            ];

            $col = 1;
            $row = 4;
            foreach ($headers as $header) {
                // Convert column number to letter
                $colLetter = \PhpOffice\PhpSpreadsheet\Cell\Coordinate::stringFromColumnIndex($col);
                $sheet->setCellValue($colLetter . $row, $header);
                $col++;
            }

            // Apply Styling to Headers
            $headerStyle = $sheet->getStyle("A4:F4");
            $headerStyle->getFont()->setBold(true);
            $headerStyle->getAlignment()->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER);
            $headerStyle->getAlignment()->setVertical(\PhpOffice\PhpSpreadsheet\Style\Alignment::VERTICAL_CENTER);
            $headerStyle->getAlignment()->setWrapText(true);
            $headerStyle->getFill()->setFillType(\PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID)
                                    ->getStartColor()->setARGB('FFC0C0C0'); // Light Gray Background


            // Write data rows
            $row = 5;
            foreach ($results as $result) {
                $col = 1;
                // Parent Organisation
                $colLetter = \PhpOffice\PhpSpreadsheet\Cell\Coordinate::stringFromColumnIndex($col);
                $sheet->setCellValue($colLetter . $row, $result['parent_organisation']);
                $col++;

                // Organisation
                $colLetter = \PhpOffice\PhpSpreadsheet\Cell\Coordinate::stringFromColumnIndex($col);
                $sheet->setCellValue($colLetter . $row, $result['organisation']);
                $col++;

                // Device Off IMEINO
                $colLetter = \PhpOffice\PhpSpreadsheet\Cell\Coordinate::stringFromColumnIndex($col);
                $sheet->setCellValue($colLetter . $row, $this->mergeIMEIWithName($result['device_off_imeino'] ?? '', $result['device_off_devicename'] ?? ''));
                $col++;

                

                // Beats Covered IMEINO
                $colLetter = \PhpOffice\PhpSpreadsheet\Cell\Coordinate::stringFromColumnIndex($col);
                $sheet->setCellValue($colLetter . $row, $this->mergeIMEIWithName($result['beats_covered_imeino'] ?? '', $result['beats_covered_devicename'] ?? ''));
                $col++;

                

                // Beats Not Covered IMEINO
                $colLetter = \PhpOffice\PhpSpreadsheet\Cell\Coordinate::stringFromColumnIndex($col);
                $sheet->setCellValue($colLetter . $row, $this->mergeIMEIWithName($result['beats_not_covered_imeino'] ?? '', $result['beats_not_covered_devicename'] ?? ''));
                $col++;


                // Overspeed IMEINO
                $colLetter = \PhpOffice\PhpSpreadsheet\Cell\Coordinate::stringFromColumnIndex($col);
                $sheet->setCellValue($colLetter . $row, $this->mergeIMEIWithName($result['break_speed_device_imeino'] ?? '', $result['break_speed_device_name'] ?? ''));

                

                $row++;
            }

            // Apply Borders to Table (Headers + Data)
            $lastRow = $row - 1;  // Last row with data
            $borderRange = "A4:F$lastRow";
            $sheet->getStyle($borderRange)->getBorders()->getAllBorders()->setBorderStyle(\PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN);

            // Enable Word Wrap for all data
            $sheet->getStyle($borderRange)->getAlignment()->setWrapText(true);

            // Auto-adjust column width
            foreach (range('A', 'F') as $columnID) {
                $sheet->getColumnDimension($columnID)->setAutoSize(true);
            }

            // Create an XLSX writer and generate a dynamic filename with timestamp.
            $writer = new Xlsx($spreadsheet);
            $timestamp = date("Ymd_His");
            $outputFilePath = WRITEPATH . "reports/report_{$desiredUserType}_{$timestamp}_{$userId}.xlsx";
            $writer->save($outputFilePath);

            

            $html = '<html>
                <head>
                    <style>
                        body { font-family: Arial, sans-serif; }
                        h2 { text-align: center; }
                        .report-meta { text-align: center; font-size: 12px; margin-bottom: 20px; }
                        table { width: 100%; border-collapse: collapse; }
                        th, td { border: 1px solid black; padding: 8px; text-align: center; }
                        th { background-color: #f2f2f2; }
                    </style>
                </head>
                <body>

                <h2>Report Type: ' . $desiredUserType . '</h2>
                <p class="report-meta">Generated On: ' . $timestamp . '</p>

                <table>
                    <tr>
                        <th>Pway</th>
                        <th>Section</th>
                        <th>Device was off</th>
                        <th>Beat not covered</th>
                        <th>Beat completed successfully</th>
                        <th>Overspeed</th>
                    </tr>';

                // Populate table rows
                foreach ($results as $result) {
                    $html .= '<tr>
                        <td>' . $result['parent_organisation'] . '</td>
                        <td>' . $result['organisation'] . '</td>
                        <td>' . $this->mergeIMEIWithName($result['device_off_imeino'] ?? '', $result['device_off_devicename'] ?? '') . '</td>
                        <td>' . $this->mergeIMEIWithName($result['beats_not_covered_imeino'] ?? '', $result['beats_not_covered_devicename'] ?? '') . '</td>
                        <td>' . $this->mergeIMEIWithName($result['beats_covered_imeino'] ?? '', $result['beats_covered_devicename'] ?? '') . '</td>
                        <td>'.$this->mergeIMEIWithName($result['break_speed_device_imeino'] ?? '', $result['break_speed_device_name'] ?? '').'</td>  
                    </tr>';
                }

                $html .= '</table>
                </body>
                </html>';

            // Generate report filename
            $timestamp = date("Ymd_His");
            $filename = "report_{$desiredUserType}_{$timestamp}_{$userId}.pdf";

            // Save PDF file
            $pdfFilePath = WRITEPATH . "reports/report_" . date("Ymd_His") . ".pdf";

            // Generate PDF from HTML
            $pdf = new MakePDF();
            $pdf->setFileName($filename);
            $pdf->setContent($html);
            $pdfFilePath = WRITEPATH . "reports/" . $filename;
            $pdf->getPdf(false);
            
            CLI::write("PDF report generated: " . $pdfFilePath, 'green');

            // Prepare email configuration for SMTP
            /* $config = [
                'protocol'   => 'smtp',
                'SMTPHost'   => 'mail.bbxvisible.com',
                'SMTPPort'   => 587, // or 587
                'SMTPUser'   => 'alerts@bbxvisible.com',
                'SMTPPass'   => 'Sil@12345',
                'mailType'   => 'html',
                'SMTPCrypto' => 'tls',
                'newline'    => "\r\n",
            ];*/

            $config = [
                'protocol'   => 'smtp',
                'SMTPHost'   => '9trax.com',
                'SMTPPort'   => 587, // or 587
                'SMTPUser'   => 'alert@9trax.com',
                'SMTPPass'   => 'R2@KUf3d8',
                'mailType'   => 'html',
                'SMTPCrypto' => 'tls',
                'newline'    => "\r\n",
            ];


            $email = \Config\Services::email($config);
            $email->setFrom('alert@9trax.com', '9Trax Alert');
            // $email->setTo('Srdenhqrjt@gmail.com');
            // $email->setTo($key);
            

            $email->setBCC('stesalitgpsrjt@gmail.com');
            // $email->setBCC('ritesh.das@blackbox.com');
            $email->setBCC('sreejita@stesalitsystems.com');
            // Include the desired usertype in the email subject.
            $email->setSubject("Automated Report - {$timestamp} - {$desiredUserType}");
            $email->setMessage(
                "Please find attached the automated report generated at {$timestamp}.<br><br>" .
                "Report Time Range:<br>From: {$startTime}<br>To: {$endTime}"
            );
            $email->attach($outputFilePath);
            $email->attach($pdfFilePath);    // PDF File


            if ($email->send()) {
                CLI::write("Report exported and emailed successfully to rupak.stesalit@gmail.com", 'green');
            } else {
                CLI::write("Email sending failed. Check email configuration.", 'red');
            }

            if (!empty($val['user_type']) && $val['user_type'] != "All") {
                // Insert each record into tbl_daily_report_snapshot
                foreach ($results as $result) {
                    $auditData = [
                        'report_name'        => $reportType,
                        'dttime'             => date('H:i:s'),
                        'usertype'           => $desiredUserType,
                        'pway'               => $result['parent_organisation'],
                        'section'            => $result['organisation'],
                        'device_off'         => $this->mergeIMEIWithName($result['device_off_imeino'] ?? '', $result['device_off_devicename'] ?? ''),
                        'beats_covered'      => $this->mergeIMEIWithName($result['beats_covered_imeino'] ?? '', $result['beats_covered_devicename'] ?? ''),
                        'beats_not_covered'  => $this->mergeIMEIWithName($result['beats_not_covered_imeino'] ?? '', $result['beats_not_covered_devicename'] ?? ''),
                        'overspeed'          => $this->mergeIMEIWithName($result['break_speed_device_imeino'] ?? '', $result['break_speed_device_name'] ?? ''),
                        'excel'              => $outputFilePath,
                        'pdf'                => $pdfFilePath
                    ];

                    $db->table('tbl_daily_report_snapshot')->insert($auditData);
                }
            }
            $email->clear(TRUE);
        }
    }

    private function mergeIMEIWithName($imeiString, $deviceString)
    {
        if (empty($imeiString) || empty($deviceString)) {
            return ''; // Return empty if no data
        }

        $imeis = explode(',', $imeiString);
        $devices = explode(',', $deviceString);

        if (count($imeis) !== count($devices)) {
            return 'Mismatch in IMEI and Device Name count';
        }

        return implode(', ', array_map(function ($imei, $device) {
            $deviceParts = explode(':', $device);
            $lastPart = trim(end($deviceParts)); // Extract last part after ":"
            return trim($imei) . " -- " . $lastPart ;
        }, $imeis, $devices));
    }
}
