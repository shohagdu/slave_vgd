<?php
error_reporting(0);
// default time zone setup
date_default_timezone_set("Asia/Dhaka");
// read ini file
$config = parse_ini_file("settings.ini", true);
// slave mysql location database connection
if (isset($config['SLAVE'])) {
    if (isset($config['SLAVE']['hostname']) && isset($config['SLAVE']['database']) && isset($config['SLAVE']['username']) && isset($config['SLAVE']['password'])) {

        // MySQLi connection
        $slave_db = new mysqli($config['SLAVE']['hostname'], $config['SLAVE']['username'], $config['SLAVE']['password'], $config['SLAVE']['database']);

        // if database connection error
        if ($slave_db->connect_errno > 0) {
            die("Error!! : " . $slave_db->connect_error);
        }
    } else {
        die("Slave database connection error. Hostname or database or username or password not set.");
    }
} else {
    die("Slave database connection not set. Please set it first.");
}

$fromDt=!empty($_POST['fromDt'])?date('Y-m-d',strtotime($_POST['fromDt'])):'';
$toDt=!empty($_POST['toDt'])?date('Y-m-d',strtotime($_POST['toDt'])):'';

if(empty($fromDt) || empty($toDt)){
    die(json_encode(['status'=>'error','message'=>'Date Range is required','data'=>'']));
}
$query = $slave_db->query("SELECT vgd_attendance_logs.*,IF(is_process=1,'Upload Done','Pending') as uploadStatus,DATE_FORMAT(attendance_date, '%d-%m-%Y %h:%i %p') as createdTitle FROM vgd_attendance_logs WHERE is_process = 1 AND (date(attendance_date) >= '$fromDt' and date(attendance_date)  <= '$toDt')");
//echo "<pre>";
//print_r($query);
//exit;
// final data
$data = [];
if($query->num_rows > 0) {
    while ($row = $query->fetch_assoc()) {
        $data[] = $row;
    }
    die(json_encode(['status'=>'success','message'=>'Successfully data Found','data'=>$data]));
}else{
    die(json_encode(['status'=>'error','message'=>'No Data Found','data'=>'']));
}

?>