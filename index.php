<?php 
include 'Includes/dbcon.php';
session_start();
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
    <title>Elizabeth School - Login</title>
    <link href="vendor/fontawesome-free/css/all.min.css" rel="stylesheet" type="text/css">
    <link href="vendor/bootstrap/css/bootstrap.min.css" rel="stylesheet" type="text/css">
    <link href="css/ruang-admin.min.css" rel="stylesheet">
    <script>
        function validateForm() {
            // Get the form fields
            var email = document.getElementById("exampleInputEmail").value;
            var password = document.getElementById("exampleInputPassword").value;
            var emailPattern = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;

            // Email format validation
            if (!emailPattern.test(email)) {
                alert("Please enter a valid email address.");
                return false;
            }

           

            return true;
        }
    </script>
</head>

<body class="bg-gradient-login" style="background-image: url('img/logo/loral1.jpg');">
    <!-- Login Content -->
    <div class="container-login">
        <div class="row justify-content-center">
            <div class="col-xl-10 col-lg-12 col-md-9">
                <div class="card shadow-sm my-5">
                    <div class="card-body p-0">
                        <div class="row">
                            <div class="col-lg-12">
                                <div class="login-form">
                                    <h5 align="center">STUDENT ATTENDANCE SYSTEM</h5>
                                    <div class="text-center">
                                        <img src="img/logo/attnlg.jpg" style="width:100px;height:100px">
                                        <br><br>
                                        <h1 class="h4 text-gray-900 mb-4">Login Panel</h1>
                                    </div>
                                    <form class="user" method="post" action="" onsubmit="return validateForm();">
                                        <div class="form-group">
                                            <select required name="userType" class="form-control mb-3">
                                                <option value="">Select User Roles</option>
                                                <option value="Administrator">Administrator</option>
                                                <option value="ClassTeacher">ClassTeacher</option>
                                                <option value="Student">Student</option>
                                            </select>
                                        </div>
                                        <div class="form-group">
                                            <input type="text" class="form-control" required name="username" id="exampleInputEmail" placeholder="Enter Email Address ">
                                        </div>
                                        <div class="form-group">
                                            <input type="password" name="password" required class="form-control" id="exampleInputPassword" placeholder="Enter Password">
                                        </div>
                                        <div class="form-group">
                                            <div class="custom-control custom-checkbox small" style="line-height: 1.5rem;">
                                                <input type="checkbox" class="custom-control-input" id="customCheck">
                                            </div>
                                        </div>
                                        <div class="form-group">
                                            <input type="submit" class="btn btn-success btn-block" value="Login" name="login" />
                                        </div>
                                    </form>

                                    <?php
                                    if (isset($_POST['login'])) {
                                        $userType = $_POST['userType'];
                                        $username = $_POST['username'];
                                        $password = md5($_POST['password']);

                                        if ($userType == "Administrator") {
                                            $query = "SELECT * FROM tbladmin WHERE emailAddress = '$username' AND password = '$password'";
                                            $rs = $conn->query($query);
                                            $num = $rs->num_rows;
                                            $rows = $rs->fetch_assoc();

                                            if ($num > 0) {
                                                $_SESSION['userId'] = $rows['Id'];
                                                $_SESSION['firstName'] = $rows['firstName'];
                                                $_SESSION['lastName'] = $rows['lastName'];
                                                $_SESSION['emailAddress'] = $rows['emailAddress'];

                                                echo "<script type='text/javascript'>
                                                window.location = ('Admin/index.php')
                                                </script>";
                                            } else {
                                                echo "<div class='alert alert-danger' role='alert'>Invalid Username/Password!</div>";
                                            }
                                        } else if ($userType == "ClassTeacher") {
                                            $query = "SELECT * FROM tblclassteacher WHERE emailAddress = '$username'";
                                            $rs = $conn->query($query);
                                            $rows = $rs->fetch_assoc();

                                            if ($rows['failed_login_attempts'] >= 3) {
                                                echo "<div class='alert alert-danger' role='alert'>Account locked due to too many failed login attempts!</div>";
                                            } else {
                                                $query = "SELECT * FROM tblclassteacher WHERE emailAddress = '$username' AND password = '$password'";
                                                $rs = $conn->query($query);
                                                $num = $rs->num_rows;

                                                if ($num > 0) {
                                                    $userId = $rows['Id'];
                                                    $currentDateTime = date('Y-m-d H:i:s');
                                                    $conn->query("UPDATE tblclassteacher SET failed_login_attempts = 0, last_login_time = '$currentDateTime' WHERE Id = '$userId'");

                                                    $_SESSION['userId'] = $rows['Id'];
                                                    $_SESSION['firstName'] = $rows['firstName'];
                                                    $_SESSION['lastName'] = $rows['lastName'];
                                                    $_SESSION['emailAddress'] = $rows['emailAddress'];
                                                    $_SESSION['classId'] = $rows['classId'];
                                                    $_SESSION['classArmId'] = $rows['classArmId'];

                                                    echo "<script type='text/javascript'>
                                                    window.location = ('ClassTeacher/index.php')
                                                    </script>";
                                                } else {
                                                    $failed_attempts = $rows['failed_login_attempts'] + 1;
                                                    $conn->query("UPDATE tblclassteacher SET failed_login_attempts = '$failed_attempts' WHERE emailAddress = '$username'");
                                                    echo "<div class='alert alert-danger' role='alert'>Invalid Username/Password!</div>";
                                                }
                                            }
                                         } else if ($userType == "Student") {
                                            $query = "SELECT * FROM tblregisterstudents WHERE email = '$username'";
                                            $rs = $conn->query($query);
                                            $rows = $rs->fetch_assoc();

                                            if ($rows['failedLoginAttempts'] >= 3) {
                                                echo "<div class='alert alert-danger' role='alert'>Account locked due to too many failed login attempts!</div>";
                                            } else {
                                                $query = "SELECT * FROM tblregisterstudents WHERE email = '$username' AND password = '$password'";
                                                $rs = $conn->query($query);
                                                $num = $rs->num_rows;

                                                if ($num > 0) {
                                                    $userId = $rows['Id'];
                                                    $currentDateTime = date('Y-m-d H:i:s');
                                                    $conn->query("UPDATE tblregisterstudents SET failedLoginAttempts = 0, lastLogin = '$currentDateTime' WHERE email = '$username'");

                                                    $_SESSION['userId'] = $rows['Id'];
                                                    $_SESSION['fullName'] = $rows['fullname'];
                                                    $_SESSION['email'] = $rows['email'];
                                                    $_SESSION['phoneNumber'] = $rows['phonenumber'];
                                                    $_SESSION['admissionNumber'] = $rows['admissionNumber'];

                                                    echo "<script type='text/javascript'>
                                                    window.location = ('Student/viewMyattendance.php');
                                                    </script>";
                                                } else {
                                                    $failed_attempts = $rows['failedLoginAttempts'] + 1;
                                                    $conn->query("UPDATE tblregisterstudents SET failedLoginAttempts = '$failed_attempts' WHERE email = '$username'");
                                                    echo "<div class='alert alert-danger' role='alert'>Invalid Email/Password!</div>";
                                                }
                                            }
                                        } else {
                                            echo "<div class='alert alert-danger' role='alert'>Invalid User Type!</div>";
                                        }
                                    }
                                    ?>

                                    <hr>
                                    <div class="text-center">
                                        <a class="small" href="forgotPassword.php">Forgot Password?</a>
                                    </div>
                                    <div class="text-center">
                                        <a class="small" href="register.php">Create an Account!</a>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <!-- Login Content -->
    <script src="vendor/jquery/jquery.min.js"></script>
    <script src="vendor/bootstrap/js/bootstrap.bundle.min.js"></script>
    <script src="vendor/jquery-easing/jquery.easing.min.js"></script>
    <script src="js/ruang-admin.min.js"></script>
</body>

</html>
