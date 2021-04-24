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

    $checkingExist= "SELECT id FROM  configuration_info where 	is_active =1 ORDER  BY id DESC LIMIT 1";
    $existData=$slave_db->query($checkingExist);
    $existRow = $existData -> fetch_assoc();

    $unionName              =   (!empty($_POST['union_name'])?$_POST['union_name']:'');
    $union_url              =   (!empty($_POST['union_url'])?$_POST['union_url']:'');
    $access_db_location     =   (!empty($_POST['access_db_location'])?str_replace("\\", '/',$_POST['access_db_location']):'');

    $created_by_ip          =   '103.205.71.20';
    $created_at             =   date('Y-m-d H:i:s');

    if(empty($existRow['id'])) {
        $slave_sql = "INSERT INTO configuration_info  (`union_name`, `union_url`, `access_file_location`, `created_ip`, `created_time`) VALUES( '${unionName}', '${union_url}', '${access_db_location}','${created_by_ip}','${created_at}')";
        $slave_db->query($slave_sql);
        if ($slave_db->commit()) {
            $message = 'Your provided information has been successfully recorded';
            echo json_encode(['status'=>'success','message'=>$message,'data'=>'']);exit;
        } else {
            // rollback
            $slave_db->rollback();
            $message = 'Sorry!! Your provided information failed to recorded';
            echo json_encode(['status'=>'error','message'=>$message,'data'=>'']);exit;
        }

    }else{

        $update = $slave_db->query("UPDATE configuration_info SET
        union_name='${unionName}',
        union_url='${union_url}',
        access_file_location='${access_db_location}',
        updated_time='${created_at}'
         WHERE id ={$existRow['id']}");

        if ($update) {
            $message = 'Your provided information has been successfully updated';
            echo json_encode(['status'=>'success','message'=>$message,'data'=>'']);exit;
        } else {
            // rollback
            $slave_db->rollback();
            $message = 'Sorry!! Your provided information failed to updated';
            echo json_encode(['status'=>'error','message'=>$message,'data'=>'']);exit;
        }
     }
?>