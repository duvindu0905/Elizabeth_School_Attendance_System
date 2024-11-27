<?php 
error_reporting(0);
include '../Includes/dbcon.php';
include '../Includes/session.php';

//------------------------SAVE--------------------------------------------------

if(isset($_POST['save'])){
    $firstName = $_POST['firstName'];
    $lastName = $_POST['lastName'];
    $emailAddress = $_POST['emailAddress'];
    $phoneNo = $_POST['phoneNo'];
    $classId = $_POST['classId'];
    $classArmId = $_POST['classArmId'];
    $dateCreated = date("Y-m-d");
    
    // Validate email format
    if (!filter_var($emailAddress, FILTER_VALIDATE_EMAIL)) {
        $statusMsg = "<div class='alert alert-danger' style='margin-right:700px;'>Invalid Email Address!</div>";
    } else {
        // Validate phone number (assuming it should be numeric and of a specific length)
        if (!preg_match('/^[0-9]{10,15}$/', $phoneNo)) {
            $statusMsg = "<div class='alert alert-danger' style='margin-right:700px;'>Invalid Phone Number!</div>";
        } else {
            $query = mysqli_query($conn, "SELECT * FROM tblclassteacher WHERE emailAddress = '$emailAddress'");
            $ret = mysqli_fetch_array($query);

            $sampPass = "pass123";
            $sampPass_2 = md5($sampPass);

            if($ret > 0){ 
                $statusMsg = "<div class='alert alert-danger' style='margin-right:700px;'>This Email Address Already Exists!</div>";
            } else {
                $query = mysqli_query($conn, "INSERT INTO tblclassteacher(firstName, lastName, emailAddress, password, phoneNo, classId, classArmId, dateCreated) 
                                             VALUES('$firstName', '$lastName', '$emailAddress', '$sampPass_2', '$phoneNo', '$classId', '$classArmId', '$dateCreated')");

                if ($query) {
                    $qu = mysqli_query($conn, "UPDATE tblclassarms SET isAssigned='1' WHERE Id = '$classArmId'");
                    if ($qu) {
                        $statusMsg = "<div class='alert alert-success' style='margin-right:700px;'>Created Successfully!</div>";
                    } else {
                        $statusMsg = "<div class='alert alert-danger' style='margin-right:700px;'>An error Occurred!</div>";
                    }
                } else {
                    $statusMsg = "<div class='alert alert-danger' style='margin-right:700px;'>An error Occurred!</div>";
                }
            }
        }
    }
}

//---------------------------------------EDIT-------------------------------------------------------------

if (isset($_GET['Id']) && isset($_GET['action']) && $_GET['action'] == "edit") {
    $Id = $_GET['Id'];

    $query = mysqli_query($conn, "SELECT * FROM tblclassteacher WHERE Id = '$Id'");
    $row = mysqli_fetch_array($query);

    //------------UPDATE-----------------------------

    if(isset($_POST['update'])){
        $firstName = $_POST['firstName'];
        $lastName = $_POST['lastName'];
        $emailAddress = $_POST['emailAddress'];
        $phoneNo = $_POST['phoneNo'];
        $classId = $_POST['classId'];
        $classArmId = $_POST['classArmId'];
        $dateCreated = date("Y-m-d");

        // Validate email format
        if (!filter_var($emailAddress, FILTER_VALIDATE_EMAIL)) {
            $statusMsg = "<div class='alert alert-danger' style='margin-right:700px;'>Invalid Email Address!</div>";
        } else {
            // Validate phone number
            if (!preg_match('/^[0-9]{10,15}$/', $phoneNo)) {
                $statusMsg = "<div class='alert alert-danger' style='margin-right:700px;'>Invalid Phone Number!</div>";
            } else {
                $query = mysqli_query($conn, "UPDATE tblclassteacher SET firstName='$firstName', lastName='$lastName', 
                                             emailAddress='$emailAddress', phoneNo='$phoneNo', classId='$classId', classArmId='$classArmId'
                                             WHERE Id='$Id'");
                if ($query) {
                    echo "<script type='text/javascript'>
                            window.location = ('createClassTeacher.php')
                          </script>"; 
                } else {
                    $statusMsg = "<div class='alert alert-danger' style='margin-right:700px;'>An error Occurred!</div>";
                }
            }
        }
    }
}

//--------------------------------DELETE------------------------------------------------------------------

if (isset($_GET['Id']) && isset($_GET['classArmId']) && isset($_GET['action']) && $_GET['action'] == "delete") {
    $Id = $_GET['Id'];
    $classArmId = $_GET['classArmId'];

    $query = mysqli_query($conn, "DELETE FROM tblclassteacher WHERE Id = '$Id'");

    if ($query == TRUE) {
        $qu = mysqli_query($conn, "UPDATE tblclassarms SET isAssigned='0' WHERE Id = '$classArmId'");
        if ($qu) {
            echo "<script type='text/javascript'>
                    window.location = ('createClassTeacher.php')
                  </script>"; 
        } else {
            $statusMsg = "<div class='alert alert-danger' style='margin-right:700px;'>An error Occurred!</div>";
        }
    } else {
        $statusMsg = "<div class='alert alert-danger' style='margin-right:700px;'>An error Occurred!</div>"; 
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
  <?php include 'includes/title.php';?>
  <link href="../vendor/fontawesome-free/css/all.min.css" rel="stylesheet" type="text/css">
  <link href="../vendor/bootstrap/css/bootstrap.min.css" rel="stylesheet" type="text/css">
  <link href="css/ruang-admin.min.css" rel="stylesheet">
  <script>
    function classArmDropdown(str) {
        if (str == "") {
            document.getElementById("txtHint").innerHTML = "";
            return;
        } else { 
            if (window.XMLHttpRequest) {
                xmlhttp = new XMLHttpRequest();
            } else {
                xmlhttp = new ActiveXObject("Microsoft.XMLHTTP");
            }
            xmlhttp.onreadystatechange = function() {
                if (this.readyState == 4 && this.status == 200) {
                    document.getElementById("txtHint").innerHTML = this.responseText;
                }
            };
            xmlhttp.open("GET", "ajaxClassArms.php?cid=" + str, true);
            xmlhttp.send();
        }
    }
  </script>
</head>

<body id="page-top">
  <div id="wrapper">
    <!-- Sidebar -->
    <?php include "Includes/sidebar.php";?>
    <!-- Sidebar -->
    <div id="content-wrapper" class="d-flex flex-column">
      <div id="content">
        <!-- TopBar -->
        <?php include "Includes/topbar.php";?>
        <!-- Topbar -->
        <!-- Container Fluid-->
        <div class="container-fluid" id="container-wrapper">
          <div class="d-sm-flex align-items-center justify-content-between mb-4">
            <h1 class="h3 mb-0 text-gray-800">Create Class Teachers</h1>
            <ol class="breadcrumb">
              <li class="breadcrumb-item"><a href="./">Home</a></li>
              <li class="breadcrumb-item active" aria-current="page">Create Class Teachers</li>
            </ol>
          </div>

          <div class="row">
            <div class="col-lg-12">
              <!-- Form Basic -->
              <div class="card mb-4">
                <div class="card-header py-3 d-flex flex-row align-items-center justify-content-between">
                  <h6 class="m-0 font-weight-bold text-primary">Create Class Teachers</h6>
                  <?php echo $statusMsg; ?>
                </div>
                <div class="card-body">
                  <form method="post">
                   <div class="form-group row mb-3">
                        <div class="col-xl-6">
                        <label class="form-control-label">Firstname<span class="text-danger ml-2">*</span></label>
                        <input type="text" class="form-control" required name="firstName" value="<?php echo isset($row['firstName']) ? $row['firstName'] : ''; ?>" id="exampleInputFirstName">
                        </div>
                        <div class="col-xl-6">
                        <label class="form-control-label">Lastname<span class="text-danger ml-2">*</span></label>
                        <input type="text" class="form-control" required name="lastName" value="<?php echo isset($row['lastName']) ? $row['lastName'] : ''; ?>" id="exampleInputLastName">
                        </div>
                    </div>
                     <div class="form-group row mb-3">
                        <div class="col-xl-6">
                        <label class="form-control-label">Email Address<span class="text-danger ml-2">*</span></label>
                        <input type="email" class="form-control" required name="emailAddress" value="<?php echo isset($row['emailAddress']) ? $row['emailAddress'] : ''; ?>" id="exampleInputEmailAddress">
                        </div>
                        <div class="col-xl-6">
                        <label class="form-control-label">Phone Number<span class="text-danger ml-2">*</span></label>
                        <input type="text" class="form-control" required name="phoneNo" value="<?php echo isset($row['phoneNo']) ? $row['phoneNo'] : ''; ?>" id="exampleInputPhoneNo">
                        </div>
                    </div>
                    <div class="form-group row mb-3">
                      <div class="col-xl-6">
                        <label class="form-control-label">Select Class<span class="text-danger ml-2">*</span></label>
                        <select class="form-control" name="classId" id="classId" onchange="classArmDropdown(this.value)" required>
                          <option value="">Select Class</option>
                          <?php 
                            $queryClass = mysqli_query($conn, "SELECT * FROM tblclass");
                            while ($rowClass = mysqli_fetch_array($queryClass)) {
                              echo "<option value='".$rowClass['Id']."'>".$rowClass['className']."</option>";
                            }
                          ?>
                        </select>
                      </div>
                      <div class="col-xl-6">
                        <label class="form-control-label">Select Class Arm<span class="text-danger ml-2">*</span></label>
                        <select class="form-control" name="classArmId" id="txtHint" required>
                          <option value="">Select Class Arm</option>
                            
                        </select>
                        
                      </div>
                    </div>

                    <?php if (isset($_GET['action']) && $_GET['action'] == "edit"): ?>
                      <input type="submit" name="update" value="Update" class="btn btn-primary">
                    <?php else: ?>
                      <input type="submit" name="save" value="Save" class="btn btn-primary">
                    <?php endif; ?>
                    
                    <a href="createClassTeacher.php" class="btn btn-danger">Cancel</a>
                  </form>
                </div>
              </div>
              <!-- Form Basic -->
            </div>
          </div>

          <!-- DataTable -->
          <div class="row">
            <div class="col-lg-12">
              <div class="card mb-4">
                <div class="card-header py-3">
                  <h6 class="m-0 font-weight-bold text-primary">Class Teachers List</h6>
                </div>
                <div class="card-body">
                  <div class="table-responsive">
                    <table class="table align-items-center table-flush" id="dataTable">
                      <thead class="thead-light">
                        <tr>
                          <th>Name</th>
                          <th>Email</th>
                          <th>Phone</th>
                          <th>Class</th>
                          <th>Class Arm</th>
                          <th>Date Created</th>
                          <th>Last Login</th>
                        </tr>
                      </thead>
                      <tbody>
                        <?php 
                        $query = mysqli_query($conn, "SELECT tblclassteacher.*, tblclass.className, tblclassarms.classArmName FROM tblclassteacher
                            JOIN tblclass ON tblclassteacher.classId = tblclass.Id
                            JOIN tblclassarms ON tblclassteacher.classArmId = tblclassarms.Id");
                         while ($row = mysqli_fetch_array($query)) {
                           $lastLogin = $row['last_login_time'] ? date("Y-m-d H:i:s", strtotime($row['last_login_time'])) : 'Never';
                           echo "<tr>
                                    <td>{$row['firstName']} {$row['lastName']}</td>
                                    <td>{$row['emailAddress']}</td>
                                    <td>{$row['phoneNo']}</td>
                                    <td>{$row['className']}</td>
                                    <td>{$row['classArmName']}</td>
                                    <td>{$row['dateCreated']}</td>
                                     <td>{$lastLogin}</td>
                                    <td>
                                        <a href='createClassTeacher.php?Id={$row['Id']}&action=edit' class='btn btn-warning btn-sm'>Edit</a>
                                        <a href='createClassTeacher.php?Id={$row['Id']}&classArmId={$row['classArmId']}&action=delete' class='btn btn-danger btn-sm' onclick=\"return confirm('Are you sure you want to delete?')\">Delete</a>
                                    </td>
                                  </tr>";
                        }
                        ?>
                      </tbody>
                    </table>
                  </div>
                </div>
              </div>
            </div>
          </div>
          <!-- DataTable -->
        </div>
      </div>
      <!-- Footer -->
      <?php include "Includes/footer.php";?>
      <!-- Footer -->
    </div>
  </div>
  <!-- Scroll to Top Button-->
  <a class="scroll-to-top rounded" href="#page-top">
    <i class="fas fa-angle-up"></i>
  </a>
  <!-- Logout Modal-->
  <?php include "Includes/logoutmodal.php";?>
  <!-- Scripts-->
  <script src="../vendor/jquery/jquery.min.js"></script>
  <script src="../vendor/bootstrap/js/bootstrap.bundle.min.js"></script>
  <script src="../vendor/jquery-easing/jquery.easing.min.js"></script>
  <script src="../vendor/datatables/jquery.dataTables.min.js"></script>
  <script src="../vendor/datatables/dataTables.bootstrap4.min.js"></script>
  <script src="js/ruang-admin.min.js"></script>
  <script src="js/demo/datatables-demo.js"></script>
</body>
</html>
