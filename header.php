<!DOCTYPE html>
<html lang="en">
<title>খাদ্য বান্ধব কর্মসূচি, Step Technology</title>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1">
<link rel="stylesheet" href="assets/w3.css">
<link rel="stylesheet" href="assets/jquery-ui.css">

<script src = "assets/jquery.min.js"></script>
<script>
    $( function() {
        $( ".datepicker" ).datepicker({
            dateFormat: 'dd-mm-yy',
            changeMonth: true,
            changeYear: true
        });
    } );
</script>

<body>
<div class="w3-top">
    <div class="w3-bar w3-black w3-card">
        <a href="index.php" class="w3-bar-item w3-button w3-padding-large">HOME</a>
        <a href="VGDmsAccesstomySQL.php" class="w3-bar-item w3-button w3-padding-large w3-hide-small">Download Data</a>
        <a href="VGDsync.php" class="w3-bar-item w3-button w3-padding-large w3-hide-small">Sync to Live</a>
        <a href="VGDdataProcess.php" class="w3-bar-item w3-button w3-padding-large w3-hide-small">Data Process</a>
        <a href="reports.php" class="w3-bar-item w3-button w3-padding-large w3-hide-small">Reports</a>
        <!--
        <a href="setup.php" class="w3-bar-item w3-button w3-padding-large w3-hide-small">Setup</a>
        -->


    </div>
</div>
<div class="w3-content" style="max-width:2000px;">
    <div class="w3-container w3-content w3-padding-64" style="max-width:1000px;min-height:550px;background: #f1f1f1" id="band" >