<?php
include 'header.php';
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
    die('Please first fill up your setup information then process information');
}else{
    $getWholeUrl =$setupInfo['union_url'];
    if(substr($getWholeUrl , -1)=='/'){
        $url=$setupInfo['union_url']."Api/vgd_process_log_data";
    }else{
        $url=$setupInfo['union_url']."/Api/vgd_process_log_data";
    }
}
?>
<div class="width100per">
        <span class="width50per" style="float: left;text-align: right;">
            <button type="button" id="processBtn" class="button" onclick="vgDdataProcess()" >Data Process( VGD)</button>
        </span>
    <span class="width50per"  style="float: left;text-align: left;" >
            <div id="loader" style="display: none;"  >
                <img src="assets/img/ajax-loader.gif" style="margin-left:10%;width:50px;height:50px;">
            </div>
        </span>

    <div class="w3-clear"></div>
    <h5 class="w3-center color-green">তথ্য গুলো প্রসেস করতে উপরের Data Process বাটনে ক্লিক করুন</h5>
</div>
<br/>
<div id="showResponse" class="w3-center"></div>
<table rules="all" class="w3-table w3-bordered w3-border w3-striped" id="table-style">
    <thead>
    <tr>
        <th>S/N</th>
        <th>Card No</th>
        <th>NID No</th>
        <th>Name</th>
        <th>Fathers Name</th>
        <th>Status</th>
    </tr>
    </thead>
    <tbody id="showInfo">
    <tr>
        <td colspan="6" class="w3-center">Please click Data Process Button</td>
    </tr>
    </tbody>
</table>
<?php
    include 'footer.php';
?>
<script>
    function vgDdataProcess(){
        $("#loader").show();
        $("#showResponse").html();
        var url = '<?php echo $url; ?>';
        $.ajax({
            url: url,
            contentType: 'application/json',
            method: 'GET',
            dataType: 'json',
            crossDomain: true,
            success: function(response){
                $("#loader").hide();
                if(response.status=='error'){
                    $('#showInfo').html('');
                    $("#showResponse").html('<h3>'+response.message+'</h3>');
                }else{
                    $("#showResponse").html('<h3>'+response.message+'</h3>');
                    var data=response.data;
                    var p_sl=1;
                    $('#showInfo').html('');
                    var scntDynamicDeptProgram = $('#showInfo');
                    $.each(data, function (index, Obj) {
                        $(`<tr>
                                    <td>${p_sl} </td>
                                    <td>${Obj.applicant_nid}</td>
                                    <td>${Obj.applicant_nid}</td>
                                    <td>${Obj.name}</td>
                                    <td>${Obj.father_name}</td>
                                    <td>${Obj.upload_status}</td>
                                </tr>`).appendTo(scntDynamicDeptProgram);
                        p_sl++;
                    })
                }

            }
        })

    }
</script>
