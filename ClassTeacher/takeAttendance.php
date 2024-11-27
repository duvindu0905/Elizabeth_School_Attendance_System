<?php 
error_reporting(0);
include '../Includes/dbcon.php';
include '../Includes/session.php';

// Fetch class and class arm details
$query = "SELECT tblclass.className, tblclassarms.classArmName 
          FROM tblclassteacher
          INNER JOIN tblclass ON tblclass.Id = tblclassteacher.classId
          INNER JOIN tblclassarms ON tblclassarms.Id = tblclassteacher.classArmId
          WHERE tblclassteacher.Id = '$_SESSION[userId]'";
$rs = $conn->query($query);
$rrw = $rs->fetch_assoc();

// Fetch active session term
$query = "SELECT * FROM tblsessionterm WHERE isActive = '1'";
$sessionResult = mysqli_query($conn, $query);
$sessionTerm = mysqli_fetch_array($sessionResult);
$sessionTermId = $sessionTerm['Id'];

$dateTaken = date("Y-m-d");

// Check if attendance record exists for today
$query = "SELECT * FROM tblattendance  
          WHERE classId = '$_SESSION[classId]' 
          AND classArmId = '$_SESSION[classArmId]' 
          AND dateTimeTaken = '$dateTaken'";
$attendanceResult = mysqli_query($conn, $query);
$attendanceCount = mysqli_num_rows($attendanceResult);

if ($attendanceCount == 0) { // If record does not exist, insert new records
    $query = "SELECT * FROM tblstudents  
              WHERE classId = '$_SESSION[classId]' 
              AND classArmId = '$_SESSION[classArmId]'";
    $studentsResult = mysqli_query($conn, $query);
    while ($student = $studentsResult->fetch_assoc()) {
        $query = "INSERT INTO tblattendance (admissionNo, classId, classArmId, sessionTermId, status, dateTimeTaken) 
                  VALUES ('{$student['admissionNumber']}', '$_SESSION[classId]', '$_SESSION[classArmId]', '$sessionTermId', '0', '$dateTaken')";
        mysqli_query($conn, $query);
    }
}

if (isset($_POST['save'])) {
    $admissionNo = $_POST['admissionNo'];
    $check = $_POST['check'];
    $numChecks = count($admissionNo);

    // Check if attendance has already been taken for today
    $query = "SELECT * FROM tblattendance  
              WHERE classId = '$_SESSION[classId]' 
              AND classArmId = '$_SESSION[classArmId]' 
              AND dateTimeTaken = '$dateTaken' 
              AND status = '1'";
    $attendanceResult = mysqli_query($conn, $query);
    $attendanceCount = mysqli_num_rows($attendanceResult);

    if ($attendanceCount > 0) {
        $statusMsg = "<div class='alert alert-danger' style='margin-right:700px;'>Attendance has already been taken for today!</div>";
    } else {
        // Update status for checked checkboxes
        for ($i = 0; $i < $numChecks; $i++) {
            if (isset($check[$i])) {
                $query = "UPDATE tblattendance 
                          SET status = '1' 
                          WHERE admissionNo = '{$check[$i]}'";
                $updateResult = mysqli_query($conn, $query);

                if ($updateResult) {
                    $statusMsg = "<div class='alert alert-success' style='margin-right:700px;'>Attendance Taken Successfully!</div>";
                } else {
                    $statusMsg = "<div class='alert alert-danger' style='margin-right:700px;'>An error occurred!</div>";
                }
            }
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="utf-8">
  <meta http-equiv="X-UA-Compatible" content="IE=edge">
  <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
  <meta name="description" content="">
  <meta name="author" content="">
  <link href="img/logo/attnlg.jpg" rel="icon">
  <title>Dashboard</title>
  <link href="../vendor/fontawesome-free/css/all.min.css" rel="stylesheet" type="text/css">
  <link href="../vendor/bootstrap/css/bootstrap.min.css" rel="stylesheet" type="text/css">
  <link href="css/ruang-admin.min.css" rel="stylesheet">
  <script>
    function classArmDropdown(str) {
        if (str === "") {
            document.getElementById("txtHint").innerHTML = "";
            return;
        }
        const xmlhttp = new XMLHttpRequest();
        xmlhttp.onreadystatechange = function() {
            if (this.readyState === 4 && this.status === 200) {
                document.getElementById("txtHint").innerHTML = this.responseText;
            }
        };
        xmlhttp.open("GET", "ajaxClassArms2.php?cid=" + str, true);
        xmlhttp.send();
    }
  </script>
</head>
<body id="page-top">
  <div id="wrapper">
    <!-- Sidebar -->
    <?php include "Includes/sidebar.php"; ?>
    <!-- Sidebar -->
    <div id="content-wrapper" class="d-flex flex-column">
      <div id="content">
        <!-- TopBar -->
        <?php include "Includes/topbar.php"; ?>
        <!-- Topbar -->
        <!-- Container Fluid-->
        <div class="container-fluid" id="container-wrapper">
          <div class="d-sm-flex align-items-center justify-content-between mb-4">
            <h1 class="h3 mb-0 text-gray-800">Take Attendance (Today's Date: <?php echo date("m-d-Y"); ?>)</h1>
            <ol class="breadcrumb">
              <li class="breadcrumb-item"><a href="./">Home</a></li>
              <li class="breadcrumb-item active" aria-current="page">All Students in Class</li>
            </ol>
          </div>
          <div class="row">
            <div class="col-lg-12">
              <!-- Form Basic -->
              <form method="post">
                <div class="row">
                  <div class="col-lg-12">
                    <div class="card mb-4">
                      <div class="card-header py-3 d-flex flex-row align-items-center justify-content-between">
                        <h6 class="m-0 font-weight-bold text-primary">All Students in (<?php echo $rrw['className'] . ' - ' . $rrw['classArmName']; ?>) Class</h6>
                        <h6 class="m-0 font-weight-bold text-danger">Note: <i>Click on the checkboxes beside each student to take attendance!</i></h6>
                      </div>
                      <div class="table-responsive p-3">
                        <?php echo $statusMsg; ?>
                        <table class="table align-items-center table-flush table-hover">
                          <thead class="thead-light">
                            <tr>
                              <th>#</th>
                              <th>First Name</th>
                              <th>Last Name</th>
                              <th>Other Name</th>
                              <th>Admission No</th>
                              <th>Class</th>
                              <th>Class Arm</th>
                              <th>Check</th>
                            </tr>
                          </thead>
                          <tbody>
                            <?php
                              $query = "SELECT tblstudents.Id, tblstudents.admissionNumber, tblclass.className, tblclass.Id AS classId, 
                                               tblclassarms.classArmName, tblclassarms.Id AS classArmId, tblstudents.firstName, 
                                               tblstudents.lastName, tblstudents.otherName, tblstudents.dateCreated
                                        FROM tblstudents
                                        INNER JOIN tblclass ON tblclass.Id = tblstudents.classId
                                        INNER JOIN tblclassarms ON tblclassarms.Id = tblstudents.classArmId
                                        WHERE tblstudents.classId = '$_SESSION[classId]' 
                                        AND tblstudents.classArmId = '$_SESSION[classArmId]'";
                              $studentsResult = $conn->query($query);
                              $sn = 0;

                              if ($studentsResult->num_rows > 0) { 
                                while ($student = $studentsResult->fetch_assoc()) {
                                  $sn++;
                                  echo "
                                    <tr>
                                      <td>{$sn}</td>
                                      <td>{$student['firstName']}</td>
                                      <td>{$student['lastName']}</td>
                                      <td>{$student['otherName']}</td>
                                      <td>{$student['admissionNumber']}</td>
                                      <td>{$student['className']}</td>
                                      <td>{$student['classArmName']}</td>
                                      <td><input name='check[]' type='checkbox' value='{$student['admissionNumber']}' class='form-control'></td>
                                    </tr>
                                    <input name='admissionNo[]' value='{$student['admissionNumber']}' type='hidden' class='form-control'>
                                  ";
                                }
                              } else {
                                echo "<div class='alert alert-danger' role='alert'>No Record Found!</div>";
                              }
                            ?>
                          </tbody>
                        </table>
                        <br>
                        <button type="submit" name="save" class="btn btn-primary">Save Attendance</button>
                      </div>
                    </div>
                  </div>
                </div> 
              </form>
            </div>
          </div>
          <!-- Row -->
        </div>
        <!-- Container Fluid -->
      </div>
      <!-- Footer -->
      <?php include "Includes/footer.php"; ?>
      <!-- Footer -->
    </div>
  </div>
  <!-- Scroll to top -->
  <a class="scroll-to-top rounded" href="#page-top">
    <i class="fas fa-angle-up"></i>
  </a>
  <!-- Logout Modal-->
  <?php include "Includes/logoutmodal.php"; ?>
  <!-- Bootstrap core JavaScript-->
  <script src="../vendor/jquery/jquery.min.js"></script>
  <script src="../vendor/bootstrap/js/bootstrap.bundle.min.js"></script>
  <script src="../vendor/jquery-easing/jquery.easing.min.js"></script>
  <!-- Custom scripts for all pages-->
  <script src="js/ruang-admin.min.js"></script>
</body>
</html>

