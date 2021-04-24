<?php
include 'header.php';
        // read from ini file
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
                die("Sorry!! Slave database connection not set. Please set it first.");
        }

        $checkingExistSetup= "SELECT * FROM  configuration_info where 	is_active =1 ORDER  BY id DESC LIMIT 1";
        $existDataSetup=$slave_db->query($checkingExistSetup);
        $setupInfo = $existDataSetup -> fetch_assoc();

        if(empty($setupInfo['union_url'])){
                die('Please first fill up your setup information then sync. information');
        }else{
                $getWholeUrl =$setupInfo['union_url'];
                if(substr($getWholeUrl , -1)=='/'){
                        $destination=$setupInfo['union_url']."Api";
                }else{
                        $destination=$setupInfo['union_url']."/Api";
                }
        }
        echo  "<h2> Welcome to ".(!empty($setupInfo['union_name'])?ucfirst($setupInfo['union_name']):'')."</h2>";
        echo "This is  Union Parisad Automation (VGD) Software. This Software works with 3 Step. Steps ar below there..";
        echo "<h4><b>Step1:</b> Click Download Data</h4>";
        echo "<h4><b>Step2:</b> Click Sync to live</h4>";
        echo "<h4><b>Step3:</b> Data Process link and click data Process button. </h4>";
        echo "<br/>";
        echo "<h4><b>Reports:</b> </h4>";
        echo "You can get date wise report in reports links";

include 'footer.php';
?>




