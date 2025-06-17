<?php
header('Content-Type: text/html; charset=utf-8');
error_reporting(E_ERROR | E_WARNING | E_PARSE);
$conn = pg_connect('host=localhost port=5432 dbname=personal_track user=postgres password=Admin@123') or die ('<b>DATABASE CONNECTIVITY PROBLEM - Error connecting to postgre:</b></ br>'.pg_last_error());
ini_set('memory_limit', '-1');
ini_set('default_charset', 'UTF-8');
set_time_limit(0);
date_default_timezone_set('Asia/Calcutta');
require_once('/var/www/html/track/cron/php_excel/excel_reader2.php');
$filename_path = "/var/www/html/track/cron/Station_data_ckp.xls";
$xls = new Spreadsheet_Excel_Reader($filename_path);
for($row=2;$row<=$xls->rowcount();$row++) 
{
	//echo $xls->val($row,6)."<br>";
	$stationname = trim($xls->val($row,7));
	//echo $xls->val($row,3);
	echo $latitude = convertDMSToDecimal($xls->val($row,8));exit;
	$longitude = convertDMSToDecimal($xls->val($row,4));
	$insert = "insert into stes.master_stationdata(name,latitude,longitude,geom) values ('".$stationname."','".$latitude."','".$longitude."',ST_GeomFromText('POINT(".$longitude." ".$latitude.")',4326))";
	echo "<br><br>";exit;
	//pg_query($insert);
}

function DMStoDD($input){
       
       $deg = " " ;
       $min = " " ;
       $sec = " " ;  
       $inputM = " " ;        
   
   
       //print "<br> Input is ".$input." <br>";
   
       for ($i=0; $i < strlen($input); $i++){                     
           $tempD = $input[$i];
            print "<br> TempD [$i] is : $tempD"; 
   
           if ($tempD == iconv("UTF-8", "ISO-8859-1//TRANSLIT", '°') ){ 
               $newI = $i + 1 ;
               print "<br> newI is : $newI"; 
               $inputM =  substr($input, $newI, -1) ;
               break; 
           }//close if degree
   
           $deg .= $tempD ;                    
       }//close for degree
   
        //print "InputM is ".$inputM." <br>";
   
       for ($j=0; $j < strlen($inputM); $j++){ 
           $tempM = $inputM[$j];
            print "<br> TempM [$j] is : $tempM"; 
   
           if ($tempM == "'"){                     
               $newI = $j + 1 ;
                print "<br> newI is : $newI"; 
               $sec =  substr($inputM, $newI, -1) ;
               break; 
            }//close if minute
            $min .= $tempM ;                    
       }//close for min
   
            $result =  $deg+( (( $min*60)+($sec) ) /3600 );
    
    
            print "<br> Degree is ". $deg*1 ;
            print "<br> Minutes is ". $min ;
            print "<br> Seconds is ". $sec ;
            print "<br> Result is ". $result ;
   
            return $deg + ($min / 60) + ($sec / 3600);
   
      }
	  function convertDMSToDecimal($latlng) {
    $valid = false;
    $decimal_degrees = 0;
    $degrees = 0; $minutes = 0; $seconds = 0; $direction = 1;
    // Determine if there are extra periods in the input string
    $num_periods = substr_count($latlng, '.');
    if ($num_periods > 1) {
        $temp = preg_replace('/\./', ' ', $latlng, $num_periods - 1); // replace all but last period with delimiter
        $temp = trim(preg_replace('/[a-zA-Z]/','',$temp)); // when counting chunks we only want numbers
        $chunk_count = count(explode(" ",$temp));
        if ($chunk_count > 2) {
            $latlng = preg_replace('/\./', ' ', $latlng, $num_periods - 1); // remove last period
        } else {
            $latlng = str_replace("."," ",$latlng); // remove all periods, not enough chunks left by keeping last one
        }
    }
    
    // Remove unneeded characters°
    $latlng = trim($latlng);
	
	if (strpos($latlng, '°') !== false) {
  echo "hi5";
}
else
{
echo "hi6";
}
	
    $latlng = str_replace("°"," ",$latlng);
	
    $latlng = str_replace("'"," ",$latlng);
    $latlng = str_replace("\""," ",$latlng);
    $latlng = str_replace("  "," ",$latlng);
    echo $latlng = substr($latlng,0,1) . str_replace('-', ' ', substr($latlng,1)); // remove all but first dash
    if ($latlng != "") {
    	// DMS with the direction at the start of the string
        if (preg_match("/^([nsewNSEW]?)\s*(\d{1,3})\s+(\d{1,3})\s+(\d+\.?\d*)$/",$latlng,$matches)) {
            $valid = true;
            $degrees = intval($matches[2]);
            $minutes = intval($matches[3]);
            $seconds = floatval($matches[4]);
            if (strtoupper($matches[1]) == "S" || strtoupper($matches[1]) == "W")
                $direction = -1;
        }
        // DMS with the direction at the end of the string
        elseif (preg_match("/^(-?\d{1,3})\s+(\d{1,3})\s+(\d+(?:\.\d+)?)\s*([nsewNSEW]?)$/",$latlng,$matches)) {
            $valid = true;
            $degrees = intval($matches[1]);
            $minutes = intval($matches[2]);
            $seconds = floatval($matches[3]);
            if (strtoupper($matches[4]) == "S" || strtoupper($matches[4]) == "W" || $degrees < 0) {
                $direction = -1;
                $degrees = abs($degrees);
            }
        }
        if ($valid) {
            // A match was found, do the calculation
            $decimal_degrees = ($degrees + ($minutes / 60) + ($seconds / 3600)) * $direction;
        } else {
            // Decimal degrees with a direction at the start of the string
            if (preg_match("/^([nsewNSEW]?)\s*(\d+(?:\.\d+)?)$/",$latlng,$matches)) {
                $valid = true;
                if (strtoupper($matches[1]) == "S" || strtoupper($matches[1]) == "W")
                    $direction = -1;
                $decimal_degrees = $matches[2] * $direction;
            }
            // Decimal degrees with a direction at the end of the string
            elseif (preg_match("/^(-?\d+(?:\.\d+)?)\s*([nsewNSEW]?)$/",$latlng,$matches)) {
                $valid = true;
                if (strtoupper($matches[2]) == "S" || strtoupper($matches[2]) == "W" || $degrees < 0) {
                    $direction = -1;
                    $degrees = abs($degrees);
                }
                $decimal_degrees = $matches[1] * $direction;
            }
        }
    }
    if ($valid) {
        return $decimal_degrees;
    } else {
        return false;
    }
}
?>