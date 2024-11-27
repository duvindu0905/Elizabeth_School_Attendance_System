<?php 
include '../Includes/dbcon.php';

session_start();

// Variables
$admissionNumber = '';
$selectedTerm = '';
$selectedSession = '';
$attendanceResults = null;

// Fetch terms and sessions for dropdowns
$termsQuery = "SELECT Id, termName FROM tblterm";
$termsResult = $conn->query($termsQuery);

$sessionsQuery = "SELECT Id, sessionName FROM tblsessionterm";
$sessionsResult = $conn->query($sessionsQuery);

// Form submission handling
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['admissionNumber']) && isset($_POST['term']) && isset($_POST['session'])) {
    $admissionNumber = $_POST['admissionNumber'];
    $selectedTerm = $_POST['term'];
    $selectedSession = $_POST['session'];
    
    // Prevent SQL Injection
    $admissionNumber = $conn->real_escape_string($admissionNumber);
    $selectedTerm = $conn->real_escape_string($selectedTerm);
    $selectedSession = $conn->real_escape_string($selectedSession);

    $query = "SELECT tblattendance.Id, tblattendance.status, tblattendance.dateTimeTaken, 
                     tblclassarms.classArmName, tblsessionterm.sessionName, 
                     tblterm.termName, tblstudents.firstName, tblstudents.lastName,  
                     tblstudents.admissionNumber 
              FROM tblattendance
              INNER JOIN tblclass ON tblclass.Id = tblattendance.classId
              INNER JOIN tblclassarms ON tblclassarms.Id = tblattendance.classArmId
              INNER JOIN tblsessionterm ON tblsessionterm.Id = tblattendance.sessionTermId
              INNER JOIN tblterm ON tblterm.Id = tblsessionterm.termId
              INNER JOIN tblstudents ON tblstudents.admissionNumber = tblattendance.admissionNo
              WHERE tblattendance.admissionNo = '$admissionNumber' 
              AND tblattendance.sessionTermId = '$selectedSession' 
              AND tblterm.Id = '$selectedTerm'";

    if (!empty($_SESSION['classId'])) {
        $query .= " AND tblattendance.classId = '".$_SESSION['classId']."'";
    }

    if (!empty($_SESSION['classArmId'])) {
        $query .= " AND tblattendance.classArmId = '".$_SESSION['classArmId']."'";
    }

    $result = $conn->query($query);

    if ($result) {
        if ($result->num_rows > 0) {
            $attendanceResults = $result;
        } else {
            echo "<div class='alert alert-danger' role='alert'>No records found for the selected criteria.</div>";
        }
    } else {
        echo "Error: " . $query . "<br>" . $conn->error;
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
    <title>View Student Attendance</title>
    <link href="../vendor/fontawesome-free/css/all.min.css" rel="stylesheet" type="text/css">
    <link href="../vendor/bootstrap/css/bootstrap.min.css" rel="stylesheet" type="text/css">
    <link href="css/ruang-admin.min.css" rel="stylesheet">
    <style>
        .card-header {
            background-color: #4e73df;
            color: #fff;
            text-align: center;
        }
        .status-present {
            background-color: #00FF00;
            color: #000;
        }
        .status-absent {
            background-color: #FF0000;
            color: #FFF;
        }
        .form-control {
            border-radius: 0.35rem;
        }
        .btn-primary {
            background-color: #4e73df;
            border-color: #4e73df;
        }
        .btn-primary:hover {
            background-color: #2e59d9;
            border-color: #2653d4;
        }
        .breadcrumb-item a {
            color: #4e73df;
        }
        .breadcrumb-item.active {
            color: #5a5c69;
        }
        table th, table td {
            text-align: center;
            vertical-align: middle;
        }
        .alert-danger {
            text-align: center;
        }
    </style>
</head>

<body class="bg-gradient-login" style="background-image: url('img/logo/loral1.jpg');">

    <div id="content-wrapper" class="d-flex flex-column">
        <div id="content">
            <!-- TopBar -->
            <?php include "Includes/topbar.php"; ?>
            <!-- Topbar -->
            <button id="sidebarToggleTop" class="btn btn-link d-md-none rounded-circle mr-3">
                <i class="fa fa-bars"></i>
            </button>

            <!-- Topbar Navbar -->
            <ul class="navbar-nav ml-auto">
                <!-- Nav Item - User Information -->
                <li class="nav-item dropdown no-arrow">
                    
                    <!-- Dropdown - User Information -->
                    <div class="dropdown-menu dropdown-menu-right shadow animated--grow-in" aria-labelledby="userDropdown">
                        <a class="dropdown-item" href="#">
                            <i class="fas fa-user fa-sm fa-fw mr-2 text-gray-400"></i>
                            Profile
                        </a>
                        <a class="dropdown-item" href="#">
                            <i class="fas fa-cogs fa-sm fa-fw mr-2 text-gray-400"></i>
                            Settings
                        </a>
                        <div class="dropdown-divider"></div>
                        <a class="dropdown-item" href="logout.php">
                            <i class="fas fa-sign-out-alt fa-sm fa-fw mr-2 text-gray-400"></i>
                            Logout
                        </a>
                    </div>
                </li>
            </ul>
            <!-- End of Topbar -->

            <!-- Attendance Content -->
            <div class="container">
                <div class="row justify-content-center">
                    <div class="col-xl-10 col-lg-12 col-md-9">
                        <div class="card shadow-sm my-5">
                            <div class="card-body p-0">
                                <div class="row">
                                    <div class="col-lg-12">
                                        <div class="attendance-table">
                                            <h5 class="text-center">STUDENT ATTENDANCE SYSTEM</h5>
                                            <div class="text-center">
                                                <img src="img/logo/attnlg.jpg" style="width:100px;height:100px" alt="Logo">
                                                <br><br>
                                                <h1 class="h4 text-gray-900 mb-4">View My Attendance</h1>
                                            </div>
                                            <form method="post" action="">
                                                <div class="form-group">
                                                    <label for="admissionNumber">Enter Admission Number:</label>
                                                    <input type="text" class="form-control" id="admissionNumber" name="admissionNumber" required>
                                                </div>
                                                <div class="form-group">
                                                    <label for="term">Select Term:</label>
                                                    <select class="form-control" id="term" name="term" required>
                                                        <option value="">Select Term</option>
                                                        <?php while ($term = $termsResult->fetch_assoc()): ?>
                                                            <option value="<?php echo htmlspecialchars($term['Id']); ?>"><?php echo htmlspecialchars($term['termName']); ?></option>
                                                        <?php endwhile; ?>
                                                    </select>
                                                </div>
                                                <div class="form-group">
                                                    <label for="session">Select Session:</label>
                                                    <select class="form-control" id="session" name="session" required>
                                                        <option value="">Select Session</option>
                                                        <?php while ($session = $sessionsResult->fetch_assoc()): ?>
                                                            <option value="<?php echo htmlspecialchars($session['Id']); ?>"><?php echo htmlspecialchars($session['sessionName']); ?></option>
                                                        <?php endwhile; ?>
                                                    </select>
                                                </div>
                                                <div class="text-center">
                                                    <button type="submit" class="btn btn-primary">View Attendance</button>
                                                </div>
                                            </form>
                                            <br>
                                            <?php if ($attendanceResults && $attendanceResults->num_rows > 0): ?>
                                            <table class="table table-bordered">
                                                <thead>
                                                    <tr>
                                                        <th>#</th>
                                                        <th>First Name</th>
                                                        <th>Last Name</th>
                                                     
                                                        <th>Admission No</th>
                                                      
                                                        <th>Class Arm</th>
                                                        <th>Session</th>
                                                        <th>Term</th>
                                                        <th>Status</th>
                                                        <th>Date</th>
                                                    </tr>
                                                </thead>
                                                <tbody>
                                                    <?php $count = 1; ?>
                                                    <?php while ($row = $attendanceResults->fetch_assoc()): ?>
                                                        <?php
                                                            $status = ($row['status'] == '1') ? 'Present' : 'Absent';
                                                            $statusClass = ($row['status'] == '1') ? 'status-present' : 'status-absent';
                                                        ?>
                                                        <tr>
                                                            <td><?php echo htmlspecialchars($count++); ?></td>
                                                            <td><?php echo htmlspecialchars($row['firstName']); ?></td>
                                                            <td><?php echo htmlspecialchars($row['lastName']); ?></td>
                                                          
                                                            <td><?php echo htmlspecialchars($row['admissionNumber']); ?></td>
                                                           
                                                            <td><?php echo htmlspecialchars($row['classArmName']); ?></td>
                                                            <td><?php echo htmlspecialchars($row['sessionName']); ?></td>
                                                            <td><?php echo htmlspecialchars($row['termName']); ?></td>
                                                            <td class="<?php echo $statusClass; ?>"><?php echo htmlspecialchars($status); ?></td>
                                                            <td><?php echo htmlspecialchars($row['dateTimeTaken']); ?></td>
                                                        </tr>
                                                    <?php endwhile; ?>
                                                </tbody>
                                            </table>
                                            <?php endif; ?>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <!-- Card Body -->
                        </div>
                    </div>
                </div>
            </div>
            <!-- End Attendance Content -->
        </div>
    </div>
    <!-- Scripts -->
    <script src="../vendor/jquery/jquery.min.js"></script>
    <script src="../vendor/bootstrap/js/bootstrap.bundle.min.js"></script>
    <script src="../vendor/jquery-easing/jquery.easing.min.js"></script>
    <script src="js/ruang-admin.min.js"></script>
</body>
</html>


