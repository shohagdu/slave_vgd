<?php
include 'header.php';
// PHP error off
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

$checkingExistSetup= "SELECT * FROM  configuration_info where 	is_active =1 ORDER  BY id DESC LIMIT 1";
$existDataSetup=$slave_db->query($checkingExistSetup);
$setupInfo = $existDataSetup -> fetch_assoc();

// pdo db connection

//try {
//    $raw_db = new PDO("odbc:DRIVER={Microsoft Access Driver (*.mdb)}; Dbq=$source_db; Uid=; Pwd=;");
//    $raw_db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
//} catch (PDOException $e) {
//    die('Connection failed: ' . $e->getMessage());
//}

// Location to file
if(empty($setupInfo['access_file_location'])){
    die('Sorry!! Your MS Access file is field is empty');
}
$db = $setupInfo['access_file_location'];
$db_param["name"]=$db;

if(!file_exists($db)){
    die('Error finding access database');
}
// Connection to ms access
$db = new PDO("odbc:Driver={Microsoft Access Driver (*.mdb, *.accdb)};Dbq=".$db.";Uid=; Pwd=;");
$db->setAttribute( PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION );

if ($db) {
    $dbConnectStatus = "Connection success";
}else {
    echo "<br>pdo connection failed\n"; exit;
}

//        $raw_sql = "UPDATE CHECKINOUT SET is_process=0 ";
//        exit;

$sql = "SELECT TOP 50 CKUT.CHECKTIME, CKUT.sn, CKUT.is_process, CKUT.id, UF.Badgenumber AS cardNo, UF.Name as nidNo FROM CHECKINOUT CKUT INNER JOIN USERINFO UF ON UF.USERID = CKUT.USERID WHERE CKUT.is_process = 0 OR CKUT.is_process IS NULL ";
$result = $db->query($sql);
$data = $result->fetchAll(PDO::FETCH_ASSOC);

// exit;
$success = 0;
$failed = 0;
$exit_alrady = 0;
$updatedData=[];
if (count($data) > 0) {
    foreach ($data as $sl=> $item) {
        // initialize variable
        $raw_id                  = $item['id'];

        $cardNo                 =   (!empty($item['cardNo'])?$item['cardNo']:'');
        $nidNo                  =   (!empty($item['nidNo'])?$item['nidNo']:'');
        $attendance_date_time   =   $item['CHECKTIME'];
        $created_by_ip          =   '103.205.71.20';
        $created_at             =   date('Y-m-d H:i:s');
        $status                 =   0;

        // start transaction
        $slave_db->begin_transaction();
        // auto commit off
        $slave_db->autocommit(false);

        // Set data for display
        $updatedData[$sl]=[
            'cardNo' =>  $cardNo,
            'nidNo' =>  $nidNo,
            'attendance_date_time' =>  $attendance_date_time,
            'updateStatus' =>  '',
            'already_uploaded_id' =>  '',
            'new_uploaded_id' =>  '',
        ];

        // checking already put finger print
        $checkingDt=date('Y-m-d',strtotime($attendance_date_time));

        $checkingExist= "SELECT id FROM vgd_attendance_logs where nid_no='$nidNo' and  DATE(attendance_date)= '$checkingDt' and  is_process=0 ";
        $existData=$slave_db->query($checkingExist);
        $existRow = $existData -> fetch_assoc();

        if(!empty($existRow['id'])){
            $updatedData[$sl]['updateStatus'] = 'already_download';
            $updatedData[$sl]['already_uploaded_id'] = $existRow['id'];

            // set process status 3 defined already uploaded
            $raw_sql = "UPDATE CHECKINOUT SET is_process=3 WHERE id=?";
            $update = $db->prepare($raw_sql)->execute([$raw_id]);
            $exit_alrady++;
        }else {
            // execute insert query
            $slave_sql = "INSERT INTO vgd_attendance_logs  (`card_no`, `nid_no`, `attendance_date`, `status`, `is_process`, `created_by_ip`,  `created_at`) VALUES( '${cardNo}', '${nidNo}', '${attendance_date_time}', 0, 0,'${created_by_ip}','${created_at}')";
            $slave_db->query($slave_sql);
            $updatedData[$sl]['new_uploaded_id'] = $slave_db->insert_id;


            // execute device db
            $raw_sql = "UPDATE CHECKINOUT SET is_process=1 WHERE id=?";
            $update = $db->prepare($raw_sql)->execute([$raw_id]);

            if ($slave_db->commit() && $update) {
                $success++;
                $updatedData[$sl]['updateStatus'] = 'success_downloaded';
            } else {
                // rollback
                $failed++;
                $slave_db->rollback();
                $updatedData[$sl]['updateStatus'] = 'failed_downloaded';
            }
        }

    }
    if (!empty($updatedData)) {
        //die("Total <b>" . $success . "</b> record updated.");
        // echo "<pre>";
        //  print_r($updatedData);
        ?>
        <div style="width: 40%;float: left;">
            <h6>Data Download From Device to Database</h6>
        </div>
        <div style="width: 40%;float: left;">
            <h6><?php echo $dbConnectStatus; ?></h6>
        </div>
        <div style="width: 20%;float: right;" class="w3-center">
            <a href="msAccesstomySQL.php" class="w3-tag">Download Data</a>
        </div>

        <div class="w3-clear"></div>


        <table class="w3-table w3-bordered w3-border w3-striped" rules="all" style="width:60%;margin-bottom: 20px;margin-top:15px;" id="table-style">
            <tr>
                <th>Successfully Download</th>
                <th>Failed to Download</th>
                <th>Already Uploaded</th>
            </tr>
            <tr>
                <td class="w3-center"><?php echo $success;  ?></td>
                <td class="w3-center"><?php echo $failed;  ?></td>
                <td class="w3-center"><?php echo $exit_alrady;  ?></td>
            </tr>

        </table>
        <table rules="all" class="w3-table w3-bordered w3-border w3-striped" id="table-style">
            <tr>
                <th class="width5per">S/N</th>
                <th class="width20per">Card No</th>
                <th class="width20per">NID No</th>
                <th class="width20per">Collection Date</th>
                <th class="width10per">Status</th>
            </tr>
            <?php
            $i=1;
            if(!empty($updatedData)) {
                foreach ($updatedData as $upData) {
                    echo "<tr>";
                    echo "<td>".$i++."</td>";
                    echo "<td>".(!empty($upData['cardNo'])?$upData['cardNo']:'')."</td>";
                    echo "<td>".(!empty($upData['nidNo'])?$upData['nidNo']:'')."</td>";
                    echo "<td>".(!empty($upData['attendance_date_time'])?date('d-m-Y',strtotime($upData['attendance_date_time'])):'')."</td>";
                    echo "<td>".(!empty($upData['updateStatus'])?$upData['updateStatus']:'')."</td>";


                    echo "</tr>";
                }
            }
            ?>

        </table>

        <?php
    } else {
        echo "No data imported due db error.";
    }
} else {
    echo "No new record found to update.";
}

include 'footer.php';
