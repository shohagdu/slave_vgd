<?php
include 'header.php';
// read from ini file
$config = parse_ini_file("settings.ini", true);

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
        $destination=$setupInfo['union_url']."Api/vgd_data_sync";
    }else{
        $destination=$setupInfo['union_url']."/Api/vgd_data_sync";
    }
}


// get un-processed data from slave table
$query = $slave_db->query("SELECT * FROM vgd_attendance_logs WHERE is_process = 0 LIMIT 100");

$data = [];
if($query->num_rows > 0){
    while ($row = $query->fetch_assoc()) {
        $data[] = $row;
    }
    $consume_record_ids = implode(',', array_column($data, "id"));
    $data = json_encode($data);

// call cURL
    $ch = curl_init();

    $data = ['data' => $data];

    curl_setopt($ch, CURLOPT_URL, $destination);
// curl_setopt($ch, CURLOPT_HEADER, true);
    curl_setopt($ch, CURLOPT_POST, 1);
// curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 60);    // 60 seconds
// curl_setopt($ch, CURLOPT_SAFE_UPLOAD, false);
    curl_setopt($ch, CURLOPT_POSTFIELDS, $data);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
// curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);

    $output = curl_exec($ch);
    curl_close($ch);
  // echo "<pre>";
 //  print_r($output);
 ///  exit;
    $response = json_decode($output, TRUE);

 // echo "<pre>";
  //  print_r($response);
  //  exit;
    if($response['status'] == 'success'){
        // update is_process = 1
        $query = $slave_db->query("UPDATE vgd_attendance_logs SET is_process=1 WHERE id IN($consume_record_ids)");
    }
    echo "<h3 class='w3-center'>".$response['message']."</h3>";
    if(!empty($response['status']) && $response['status']=='success'){
        ?>
        <table rules="all" class="w3-table w3-bordered w3-border w3-striped" id="table-style">
            <tr>
                <th class="width5per">S/N</th>
                <th class="width20per">Card No</th>
                <th class="width20per">NID No</th>
                <th class="width20per">Sync. Date</th>
            </tr>
            <?php
            $i=1;
            if(!empty($response['data'])) {
                foreach ($response['data'] as $data) {
                    echo "<tr>";
                    echo "<td>".$i++."</td>";
                    echo "<td>".(!empty($data['card_no'])?$data['card_no']:'')."</td>";
                    echo "<td>".(!empty($data['nid_no'])?$data['nid_no']:'')."</td>";
                    echo "<td>".(!empty($data['attendance_date'])?date('d-m-Y',strtotime($data['attendance_date'])):'')."</td>";
                    echo "</tr>";
                }
            }
            ?>

        </table>
        <?php
    }

} else {
    echo "No data remaining for sync. All are up-to-date.";
}


?>
    <div class="w3-clear"></div>
<?php
include 'footer.php';