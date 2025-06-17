<?php

use CodeIgniter\Router\RouteCollection;


/**
 * @var RouteCollection $routes
 */
// $routes->get('/', 'Home::index');

$routes->get('/', 'Users::index');

$routes->post('/users', 'Users::index');

$routes->get('/dashboard', 'Dashboard::index');

$routes->get('/dashboard/devicelists/(:any)', 'Dashboard::devicelists/$1');

$routes->get('/users/profile', 'Users::profile');

$routes->get('/users/lists', 'Users::lists');

$routes->post('/users/getallusers', 'Users::getallusers');

$routes->get('/users/logout', 'Users::logout');

$routes->get('/register', 'Account::register');

$routes->get('/forgetpassword', 'Account::forgetpassword');

$routes->post('/account/passwordreset', 'Account::passwordreset');

$routes->get('/controlcentre/view', 'ControlCentre::view');

$routes->get('/controlcentre/view/(:any)/(:any)/(:any)/(:any)', 'ControlCentre::view');

$routes->get('/controlcentre/adminview', 'ControlCentre::adminview');

$routes->post('/controlcentre/get_notification_and_sos_data', 'ControlCentre::getNotificationAndSosData');

$routes->post('/controlcentre/get_device_position_data', 'ControlCentre::getDevicePositionData');

$routes->get('/controlcentre/get_my_rightmenu', 'ControlCentre::getMyRightMenu');

$routes->get('/controlcentre/onlivedutydevice', 'ControlCentre::onlivedutydevice');

$routes->get('/controlcentre/ondutydevice', 'ControlCentre::ondutydevice');

$routes->get('/controlcentre/offdutydevice', 'ControlCentre::offDutyDevice');

$routes->get('/controlcentre/onlivedutydeviceload', 'ControlCentre::onLiveDutyDeviceLoad');

$routes->get('/controlcentre/ondutydeviceload', 'ControlCentre::onDutyDeviceLoad');

$routes->get('/controlcentre/offdutydeviceload', 'ControlCentre::offDutyDeviceLoad');

$routes->get('/controlcentre/getallinteractionlist', 'ControlCentre::getAllInteractionList');

$routes->post('/controlcentre/getgeofencing', 'ControlCentre::getGeofencing');

$routes->post('/controlcentre/getdetailsofdevice', 'ControlCentre::getDetailsofDevice');

$routes->post('/controlcentre/getdevicetodaycoordinates', 'ControlCentre::getDeviceTodayCoordinates');

$routes->post('/controlcentre/getfollowlocation', 'ControlCentre::getFollowLocation');

$routes->post('/controlcentre/getalldevicesudept', 'ControlCentre::getAllDevicesUDept');

$routes->post('/historyplayback/getdevicecoordinates', 'HistoryPlayback::getDeviceCoordinates');

$routes->post('/account/imageupload1/(:any)', 'Account::imageupload1/$1');

$routes->post('/account/imageupload2/(:num)', 'Account::imageupload2/$1');

$routes->post('/account/newUserRegistration', 'Account::newUserRegistration');

$routes->get('/users/add', 'Users::add');

$routes->post('/users/add', 'Users::add');

$routes->get('/users/edit/(:any)', 'Users::edit/$1');

$routes->post('/users/edit/(:any)', 'Users::edit/$1');

$routes->get('/users/accountdetails/(:any)', 'Users::accountdetails/$1');

$routes->post('/users/statusChange/(:any)/(:any)/(:any)', 'Users::statusChange/$1/$2/$3');

$routes->post('/common/getdrpdwnrole', 'Common::getdrpdwnrole');

$routes->get('/devices/lists', 'Devices::lists');

$routes->post('/devices/getAllDevicesAdmin', 'Devices::getAllDevicesAdmin');

$routes->post('/devices/getAllDevices', 'Devices::getAllDevices');

$routes->get('/devices/devicesAdmin/add', 'Devices::devicesAdmin');

$routes->post('/devices/devicesAdmin/add', 'Devices::devicesAdmin');

$routes->post('/devices/getapnsettings', 'Devices::getapnsettings');

$routes->get('/devices/uploadDeviceCsv', 'Devices::uploadDeviceCsv');

$routes->post('/devices/uploadDeviceCsv', 'Devices::uploadDeviceCsv');

$routes->get('/devices/assigndevicetousercsv', 'Devices::assigndevicetousercsv');

$routes->post('/devices/assigndevicetousercsv', 'Devices::assigndevicetousercsv');

$routes->get('/devices/devicesAdmin/edit/(:any)', 'Devices::devicesAdmin');

$routes->post('/devices/devicesAdmin/edit/(:any)', 'Devices::devicesAdmin');

$routes->get('/devices/devicesAdmin/editmode/(:any)', 'Devices::devicesAdmin');

$routes->post('/devices/devicesAdmin/editmode/(:any)', 'Devices::devicesAdmin');

$routes->get('/devices/devices_delete_admin/(:any)', 'Devices::devices_delete_admin');

$routes->get('/devices/unassigndevicetousercsv', 'Devices::unassigndevicetousercsv');

$routes->post('/devices/unassigndevicetousercsv', 'Devices::unassigndevicetousercsv');

$routes->post('devices/statuschange/(:any)/(:num)', 'Devices::statusChange');

$routes->get('/devices/uploadsoscallcsv', 'Devices::uploadsoscallcsv');

$routes->post('/devices/uploadsoscallcsv', 'Devices::uploadsoscallcsv');

$routes->get('/devices/deviceconfiguration_ajax/(:any)', 'Devices::deviceconfiguration_ajax');

$routes->get('/masters/settings/(:any)', 'Masters::settings');

$routes->get('/masters/settings/(:any)/(:any)', 'Masters::settings');

$routes->post('/masters/settings/(:any)/(:any)', 'Masters::settings');

$routes->post('masters/getallsettings/(:any)', 'Masters::getallsettings');

$routes->get('masters/scheduleupdatecsv', 'Masters::scheduleUpdateCSV'); //--

$routes->post('masters/scheduleupdatecsv', 'Masters::scheduleUpdateCSV'); //--

$routes->get('masters/scheduleupdatecsvnew', 'Masters::scheduleupdatecsvnew'); //--

$routes->post('masters/scheduleupdatecsvnew', 'Masters::scheduleupdatecsvnew');

$routes->get('masters/schedulelist', 'Masters::scheduleList'); //--

$routes->get('masters/deleteschedule/(:num)', 'Masters::deleteSchedule/$1'); //--

$routes->get('/traxreport/beatcompletionreport', 'Traxreport::beatcompletionreport');

$routes->post('/traxreport/beatcompletionreport', 'Traxreport::beatcompletionreport');

$routes->post('/traxreport/beatcompletionreportexcel', 'Traxreport::beatcompletionreportexcel');

$routes->post('/traxreport/getUser', 'Traxreport::getUser');

$routes->get('comment/marketingcomment', 'Comment::marketingcomment');

$routes->post('comment/marketingcomment', 'Comment::marketingcomment');

$routes->get('/traxreport/reportlist', 'Traxreport::reportList');

$routes->post('/traxreport/reportlist', 'Traxreport::reportList');

$routes->post('/traxreport/reportexcel', 'Traxreport::reportexcel');

$routes->post('/traxreport/reportpdf', 'Traxreport::reportpdf');

$routes->get('/traxreport/activitysummeryreport', 'Traxreport::activitySummaryReport');

$routes->post('/traxreport/activitysummeryreport', 'Traxreport::activitySummaryReport');

$routes->post('/traxreport/activitysummeryreportexcel', 'Traxreport::activitySummaryReportExcel');

$routes->post('/traxreport/getdevicecoordinates', 'Traxreport::getDeviceCoordinates');

$routes->get('/traxreport/stoppagereport', 'Traxreport::stoppageReport');

$routes->post('/traxreport/stoppagereport', 'Traxreport::stoppageReport');

$routes->post('/traxreport/stoppagereportexcel', 'Traxreport::stoppagereportexcel');

$routes->post('/traxreport/stoppagereportpdf', 'Traxreport::stoppagereportpdf');

$routes->get('/traxreport/devallotreport', 'Traxreport::devallotreport');

$routes->post('/traxreport/devallotreport', 'Traxreport::devallotreport');

$routes->post('/traxreport/devallotreportexcel', 'Traxreport::devallotreportexcel');

$routes->post('/traxreport/devallotreportpdf', 'Traxreport::devallotreportpdf');

$routes->get('/traxreport/duitycompletionreport', 'Traxreport::dutyCompletionReport');

$routes->post('/traxreport/duitycompletionreport', 'Traxreport::dutyCompletionReport');

$routes->post('/traxreport/duitycompletionreportexcel', 'Traxreport::duitycompletionreportexcel');

$routes->get('/traxreport/distancexception', 'Traxreport::distancexception');

$routes->post('/traxreport/distancexception', 'Traxreport::distancexception');

$routes->get('/traxreport/offdutyreport', 'Traxreport::offDutyReport');

$routes->post('/traxreport/offdutyreport', 'Traxreport::offDutyReport');

$routes->post('/traxreport/offdutyreportexcel', 'Traxreport::offdutyreportexcel');

$routes->post('/traxreport/offdutyreportpdf', 'Traxreport::offDutyReportPdf');

$routes->get('/traxreport/timeexception', 'Traxreport::timeexception');

$routes->post('/traxreport/timeexception', 'Traxreport::timeexception');

$routes->get('/traxreport/batterypercentage', 'Traxreport::batterypercentage');

$routes->post('/traxreport/batterypercentage', 'Traxreport::batterypercentage');

$routes->get('/traxreport/devicedetails/(:any)/(:any)/(:any)/(:any)', 'Traxreport::devicedetails');

$routes->get('/traxreport/totaldevicedetails/(:any)/(:any)/(:any)/(:any)', 'Traxreport::totalDeviceDetails');

// $routes->get('/traxreport/tripdetailsreport', 'Traxreport::tripDetailsReport');

// $routes->post('/traxreport/tripdetailsreport', 'Traxreport::tripDetailsReport');

// $routes->post('/traxreport/tripDetailsReportExcel', 'Traxreport::tripDetailsReportExcel');


$routes->get('/traxreport/tripdetailsreport', 'Traxreport::tripDetailsReport');

$routes->post('/traxreport/tripdetailsreport', 'Traxreport::tripDetailsReport');

$routes->post('/traxreport/tripDetailsReportExcel', 'Traxreport::tripDetailsReportExcel');

$routes->get('/traxreport/tripDetailsSummaryReport', 'Traxreport::tripDetailsSummaryReport');

$routes->post('/traxreport/tripDetailsSummaryReport', 'Traxreport::tripDetailsSummaryReport');

$routes->post('/traxreport/tripDetailsSummaryReportExcel', 'Traxreport::tripDetailsSummaryReportExcel');

$routes->post('/traxreport/tripDetailsSummaryReportPdf', 'Traxreport::tripDetailsSummaryReportPdf');

$routes->get('/traxreport/tripDetailsSummaryReportDetails/(:any)/(:any)/(:any)/(:any)/(:any)', 'Traxreport::tripDetailsSummaryReportDetails');

$routes->post('/traxreport/tripDetailsSummaryReportDetails/(:any)/(:any)/(:any)/(:any)/(:any)', 'Traxreport::tripDetailsSummaryReportDetails');

$routes->post('/traxreport/tripDetailsSummaryReportDetailsExcel/(:any)/(:any)/(:any)/(:any)/(:any)', 'Traxreport::tripDetailsSummaryReportDetailsExcel');

$routes->get('/traxreport/activitysummeryreport1', 'Traxreport::activitySummaryReport1');

$routes->post('/traxreport/activitysummeryreport1', 'Traxreport::activitySummaryReport1');

$routes->get('/traxreport/deviceperformancereport', 'Traxreport::devicePerformanceReport');

$routes->post('/traxreport/deviceperformancereport', 'Traxreport::devicePerformanceReport');

$routes->get('/traxreport/tripSummaryReport', 'Traxreport::tripSummaryReport');

$routes->post('/traxreport/tripSummaryReport', 'Traxreport::tripSummaryReport');

$routes->post('/traxreport/tripSummaryReportExcel', 'Traxreport::tripSummaryReportExcel');


$routes->get('trip-schedule', 'TripScheduleController::index');
$routes->post('trip-schedule', 'TripScheduleController::index'); // Show all schedules
// $routes->get('trip-schedule', 'TripScheduleListController::index');

$routes->get('trip-schedule/details/(:any)', 'TripScheduleController::details/$1'); // Show trip details

$routes->get('trip-schedule/upload', 'TripScheduleController::upload');
$routes->post('trip-schedule/upload', 'TripScheduleController::upload');
$routes->get('trip-schedule/delete/(:num)', 'TripScheduleController::delete/$1');

$routes->get('trip/checkimei', 'TripScheduleController::checkimei');
$routes->get('trip/individual-upload/(:any)', 'TripScheduleController::individualUpload');

$routes->get('/report/panic', 'Report::panic');
$routes->post('/report/panic', 'Report::panic');
$routes->post('/report/panicExcel', 'Report::panicExcel');
$routes->post('/report/panicPdf', 'Report::panicPdf');

$routes->get('/report/geofence', 'Report::geofence');
$routes->post('/report/geofence', 'Report::geofence');
$routes->post('/report/geofenceExcel', 'Report::geofenceExcel');
$routes->post('/report/geofencePdf', 'Report::geofencePdf');

$routes->get('/report/geofencegroup', 'Report::geofencegroup');
$routes->post('/report/geofencegroup', 'Report::geofencegroup');
$routes->post('/report/geofencegroupExcel', 'Report::geofencegroupExcel');
$routes->post('/report/geofencegroupPdf', 'Report::geofencegroupPdf');

$routes->get('/report/patrolling', 'Report::patrolling');
$routes->post('/report/patrolling', 'Report::patrolling');
$routes->post('/report/patrollingExcel', 'Report::patrollingExcel');
$routes->post('/report/patrollingPdf', 'Report::patrollingPdf');

$routes->get('/report/stoppage', 'Report::stoppage');
$routes->post('/report/stoppage', 'Report::stoppage');
$routes->post('/report/stoppageExcel', 'Report::stoppageExcel');
$routes->post('/report/stoppagePdf', 'Report::stoppagePdf');

$routes->get('/report/patrollingGraph', 'Report::patrollingGraph');
$routes->post('/report/patrollingGraph', 'Report::patrollingGraph');
$routes->post('/report/patrollingGraphExcel', 'Report::patrollingGraphExcel');

/*--------R-------*/
$routes->get('device-details', 'DeviceStatusController::index');
$routes->get('device-status-list', 'DeviceStatusController::deviceStatusList');
$routes->get('scheduled-patrolling-report', 'DeviceStatusController::scheduledPetrollingReport');
$routes->get('download-patrolling-report-pdf', 'DeviceStatusController::downloadPatrollingReportPDF');
$routes->get('download-patrolling-report-xlsx', 'DeviceStatusController::downloadPatrollingReportXLSX');

$routes->get('scheduled-patrolling-summery', 'DeviceStatusController::scheduledPatrollingSummery');
$routes->get('exception-report-device', 'DeviceStatusController::exceptionReport');
$routes->get('summery-report-device', 'DeviceStatusController::summeryReportNew');
$routes->get('keyman-summary', 'DeviceStatusController::keyManSummary');
