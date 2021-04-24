<?php
include 'header.php';
?>
<div class="width100per">

    <h5 class="w3-center color-green">তারিখ চিহ্নিত করে Search বাটনে ক্লিক করুন</h5>
    <div class="w3-clear"></div>
    <form action="" id="searchReportForm">
        <span class="width50per" style="float: left;text-align: right;">
            <table>
                <tr>
                    <td><input type="text" value="<?php echo date('d-m-Y') ?>" placeholder="Enter From Date" class="datepicker" name="fromDt"  id="fromDt"></td>
                    <td><input type="text" value="<?php echo date('d-m-Y') ?>" placeholder="Enter To Date" class="datepicker" name="toDt" id="toDt"></td>
                    <td><button type="button" name="searchBn" onclick="searchReports()" id="searchBtn">Search</button> </td>


                </tr>
            </table>
        </span>
         <span class="width50per"  style="float: left;text-align: left;" >
            <div id="loader" style="display: none;"  >
                <img src="assets/img/ajax-loader.gif" style="margin-left:10%;width:50px;height:50px;">
            </div>
        </span>
    </form>
</div>
<br/>
<br/>
<div id="showResponse" class="w3-center"></div>
<table rules="all" class="w3-table w3-bordered w3-border w3-striped" id="table-style">
    <thead>
    <tr>
        <th>S/N</th>
        <th>Card No</th>
        <th>NID No</th>
        <th>Status</th>
        <th>Date</th>
    </tr>
    </thead>
    <tbody id="showInfo">
    <tr>
        <td colspan="6" class="w3-center">No Record Found</td>
    </tr>
    </tbody>
</table>
<?php
include 'footer.php';
?>
<script>
    function searchReports(){
        $("#showResponse").html('');
        $("#showInfo").html('');
        var url = 'reportAction.php';
        $.ajax({
            url: url,
            data: $('#searchReportForm').serialize(),
            method: 'POST',
            dataType: 'json',
            crossDomain: true,
            success: function(response){
                $("#loader").hide();
                if(response.status=='error'){
                    $("#showInfo").html('');
                    $("#showResponse").html('<h3>'+response.message+'</h3>');
                }else{
                    $("#showResponse").html('<h3>'+response.message+'</h3>');
                    var data=response.data;
                    console.log(data);
                    var p_sl=1;
                    $('#showInfo').html('');
                    var scntDynamicDeptProgram = $('#showInfo');
                    $.each(data, function (index, Obj) {
                        $(`<tr>
                                <td>${p_sl} </td>
                                <td>${Obj.card_no}</td>
                                <td>${Obj.nid_no}</td>
                                <td>${Obj.uploadStatus}</td>
                                <td>${Obj.createdTitle}</td>
                            </tr>`).appendTo(scntDynamicDeptProgram);
                        p_sl++;
                    })
                }

            }
        })

    }
</script>
