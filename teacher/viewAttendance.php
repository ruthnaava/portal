<?php
include_once('main.php');
include_once('includes/topbar.php');
include_once('includes/sidebar.php');
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>My Attendance</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="stylesheet" href="../../source/CSS/style.css">
    <link rel="icon" type="image/x-icon" href="../../source/favicon.ico">
    <script type="text/javascript" src="jquery-1.12.3.js"></script>
    <script type="text/javascript" src="Attendance.js"></script>
</head>
<body onload="ajaxRequestToGetAttendancePresentThisMonth();">
    <div class="main-content" id="mainContent" style="min-height:100vh; padding:2rem; text-align:center; color:green;">
        <div class="page-header">
            <h1 class="page-title">My Attendance</h1>
        </div>
        <div class="dashboard-grid">
            <div class="stat-card">
                <div class="text-center bg-warning py-2 rounded mb-3">
                    <strong>Select Attendance that you are present:</strong><br>
                    <span class="me-2">Current Month:</span>
                    <div class="form-check form-check-inline" style="vertical-align: middle;">
                        <input class="form-check-input" type="radio" onclick="ajaxRequestToGetAttendancePresentThisMonth();" value="thismonth" id="present" name="present" checked="checked" style="width:18px;height:18px;">
                    </div>
                    <span class="mx-2">ALL:</span>
                    <div class="form-check form-check-inline" style="vertical-align: middle;">
                        <input class="form-check-input" type="radio" onclick="ajaxRequestToGetAttendancePresentAll();" value="all" id="present" name="present" style="width:18px;height:18px;">
                    </div>
                </div>
                <div class="text-center mb-4">
                    <table id="mypresent" class="table table-bordered table-striped table-sm"></table>
                </div>
                <div class="text-center bg-secondary py-2 rounded mb-3">
                    <strong>Select Attendance that you are absent:</strong><br>
                    <span class="me-2">Current Month:</span>
                    <div class="form-check form-check-inline" style="vertical-align: middle;">
                        <input class="form-check-input" type="radio" onclick="ajaxRequestToGetAttendanceAbsentThisMonth();" value="thismonth" id="absent" name="absent" checked="checked" style="width:18px;height:18px;">
                    </div>
                    <span class="mx-2">ALL:</span>
                    <div class="form-check form-check-inline" style="vertical-align: middle;">
                        <input class="form-check-input" type="radio" onclick="ajaxRequestToGetAttendanceAbsentAll();" value="all" id="absent" name="absent" style="width:18px;height:18px;">
                    </div>
                </div>
                <div class="text-center">
                    <table id="myabsent" class="table table-bordered table-striped table-sm"></table>
                </div>
            </div>
        </div>
    </div>
</body>
</html>

