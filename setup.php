<?php
include 'header.php';

error_reporting(0);
// default time zone setup
date_default_timezone_set("Asia/Dhaka");
// read ini file
$config = parse_ini_file("settings.ini", true);
if (!isset($config['MS-CONFIG']) || !isset($config['SLAVE']) || !isset($config['sync'])) {
    die("Settings not configure properly.");
}
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
?>
<div class="width100per">
    <h5 class="w3-center color-green">তথ্য গুলো পূরন  করে Save বাটনে ক্লিক করুন</h5>
    <div class="w3-clear"></div>
    <form action="/action_page.php" id="ConfigDataForm">
        <label for="Union Name"><b>Union Name:</b></label><br>
        <input type="text" id="union_name" class="width100per" placeholder="Enter Union Name " value="<?php echo (!empty($setupInfo['union_name'])?$setupInfo['union_name']:'')  ?>" name="union_name" ><br><br>
        <label for="Union Url"><b>Union Url (Web Address):</b></label><br>
        <input type="text" id="union_url" class="width100per" value="<?php echo (!empty($setupInfo['union_url'])?$setupInfo['union_url']:'')  ?>" placeholder="Enter Union Url (Web Address) " name="union_url" ><br><br>
        <label for="Ms Access DB"><b>Ms Access DB Location:</b></label><br>
        <input type="text" value="<?php echo (!empty($setupInfo['access_file_location'])?$setupInfo['access_file_location']:'')  ?>" id="access_db_location" class="width100per"  placeholder="Enter Ms Access DB Location " class="w3-forms" name="access_db_location" >
            <br><br>


        <span class="width20per" style="float: left;text-align: left;">
            <button type="button" id="processBtn" class="button" onclick="dataProcess()" >Save</button>
        </span>
        <span class="width80per"  style="float: left;text-align: left;" >
            <div id="loader" style="display: none;"  >
                <img src="assets/img/ajax-loader.gif" style="margin-left:10%;width:50px;height:50px;">
            </div>
            <div id="showResponse" class="w3-center"></div>
        </span>
    </form>
</div>
<br/>
<?php
include 'footer.php';
?>
<script>
    function dataProcess(){
        $("#loader").show();
        $("#showResponse").html();
        var url = "configurationAction.php";
        $.ajax({
            url: url,
            method: 'POST',
            data: $('#ConfigDataForm').serialize(),
            dataType: 'json',
            crossDomain: true,
            success: function(response){
                $("#loader").hide();
                if(response.status=='error'){
                    $("#showResponse").html('<h3>'+response.message+'</h3>');
                }else{
                    $("#showResponse").html('<h3>'+response.message+'</h3>');
                    setInterval('location.reload()', 2500);
                }

            }
        })

    }
</script>

