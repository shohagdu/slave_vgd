<?php

// Name of the file
$filename = 'assets/db/attendance_slave.sql';

// read from ini file
$config = parse_ini_file("settings.ini", true);

// Database name
$mysql_database = 'attendance_slave';

// MySQLi connection
$slave_db = new mysqli($config['SLAVE']['hostname'], $config['SLAVE']['username'], $config['SLAVE']['password']);

// if server connection error
if($slave_db->connect_errno > 0){
    die("Error!! : ".$slave_db->connect_error);
}

// Creating database
$db_creating = $slave_db->query("CREATE DATABASE IF NOT EXISTS $mysql_database");

// if database create error
if($slave_db->connect_errno > 0){
    die("Error!! : ".$slave_db->connect_error);
}

// Select database
$slave_db->select_db($mysql_database);

// if database create error
if($slave_db->connect_errno > 0){
    die("Error!! : ".$slave_db->connect_error);
}

// Temporary variable, used to store current query
$templine = '';
// Read in entire file
$lines = file($filename);
// Loop through each line
foreach ($lines as $line)
{
// Skip it if it's a comment
if (substr($line, 0, 2) == '--' || $line == '')
    continue;

// Add this line to the current segment
$templine .= $line;
// If it has a semicolon at the end, it's the end of the query
if (substr(trim($line), -1, 1) == ';')
{
    // Perform the query
    $import_db = $slave_db->query($templine);
    // Reset temp variable to empty
    $templine = '';
}
}
 echo "Database imported successfully";
?>