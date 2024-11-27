<?php
require 'vendor/vendor/autoload.php'; // Adjust the path if necessary
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

include 'Includes/dbcon.php';
session_start();

if (isset($_POST['register'])) {
    $fullName = $_POST['fullName'];
    $email = $_POST['email'];
    $phone = $_POST['phone'];
    $admissionNumber = $_POST['admissionNumber'];
    $password = $_POST['password'];
    $confirmPassword = $_POST['confirmPassword'];

    // Set timezone to ensure consistency
    date_default_timezone_set('Asia/Colombo');

    // Validate Full Name (only letters and spaces allowed)
    if (!preg_match("/^[a-zA-Z ]*$/", $fullName)) {
        echo "<div class='alert alert-danger' role='alert'>
        Full Name can only contain letters and spaces.
        </div>";
    }
    // Validate Email
    else if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        echo "<div class='alert alert-danger' role='alert'>
        Invalid email format.
        </div>";
    }
    // Validate Phone Number (only numbers allowed and 10 digits)
    else if (!preg_match('/^[0-9]{10}$/', $phone)) {
        echo "<div class='alert alert-danger' role='alert'>
        Phone number must be 10 digits long.
        </div>";
    }
    // Password validation
    else if (!preg_match('/^(?=.*[A-Z])(?=.*[a-z])(?=.*\d)(?=.*[@$!%*?&])[A-Za-z\d@$!%*?&]{12,}$/', $password)) {
        echo "<div class='alert alert-danger' role='alert'>
        Password must be at least 12 characters long and include a combination of uppercase letters, lowercase letters, numbers, and symbols.
        </div>";
    }
    else if ($password != $confirmPassword) {
        echo "<div class='alert alert-danger' role='alert'>
        Passwords do not match!
        </div>";
    } else {
        // Encrypt the password
        $password = md5($password);
        
        // Generate the current date and time
        $currentDateTime = date("Y-m-d H:i:s");

        // Generate a unique OTP
        $otp = mt_rand(100000, 999999); // Generates a 6-digit OTP
        $otpExpiry = date("Y-m-d H:i:s", strtotime('+5 minutes')); // OTP expires in 5 minutes
        
        // Insert into the database
        $query = "INSERT INTO tblregisterstudents (fullname, email, phonenumber, admissionNumber, password, registerDate, lastLogin, failedLoginAttempts, otp, otpExpiry, isConfirmed, currentDateTime) 
                  VALUES ('$fullName', '$email', '$phone', '$admissionNumber', '$password', NOW(), NULL, 0, '$otp', '$otpExpiry', 0, NOW())";
        if ($conn->query($query) === TRUE) {
            // PHPMailer settings
            $mail = new PHPMailer(true);
            try {
                //Server settings
                $mail->isSMTP();
                $mail->Host       = 'smtp.gmail.com'; // Set the SMTP server to send through
                $mail->SMTPAuth   = true;
                $mail->Username   = 'duvindudushan@gmail.com'; // SMTP username
                $mail->Password   = 'jehn wwyq kgwy yqkp'; // Use the App Password here
                $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
                $mail->Port       = 587; // TCP port to connect to

                //Recipients
                $mail->setFrom('your-email@gmail.com', 'Elizabeth School Attendance System');
                $mail->addAddress($email); // Add a recipient

                // Content
                $mail->isHTML(true); // Set email format to HTML
                $mail->Subject = 'Your OTP Code';
                $mail->Body    = "Your OTP code is: <b>$otp</b><br>Please enter this code on the OTP verification page to complete your registration.";

                $mail->send();
                // Store email and OTP in session
                $_SESSION['email'] = $email;
                $_SESSION['otp'] = $otp;
                
                echo "<div class='alert alert-success' role='alert'>
                Registration successful! Please check your email for the OTP code.
                </div>";
                // Redirect to OTP verification page
                header("Location: registerOtp.php");
                exit();
            } catch (Exception $e) {
                echo "<div class='alert alert-danger' role='alert'>
                Registration successful, but failed to send OTP. Mailer Error: {$mail->ErrorInfo}
                </div>";
            }
        } else {
            echo "<div class='alert alert-danger' role='alert'>
            Error: " . $query . "<br>" . $conn->error . "
            </div>";
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
    <title>Elizabeth School - Register</title>
    <link href="vendor/fontawesome-free/css/all.min.css" rel="stylesheet" type="text/css">
    <link href="vendor/bootstrap/css/bootstrap.min.css" rel="stylesheet" type="text/css">
    <link href="css/ruang-admin.min.css" rel="stylesheet">
    <style>
        .password-requirements {
            font-size: 0.9em;
        }
        .password-requirements span {
            display: block;
            color: red;
        }
        .password-requirements .valid {
            color: green;
        }
    </style>
    <script>
        function validatePassword() {
            var password = document.getElementById("password").value;
            var confirmPassword = document.getElementById("confirmPassword").value;
            var length = document.getElementById("length");
            var uppercase = document.getElementById("uppercase");
            var lowercase = document.getElementById("lowercase");
            var number = document.getElementById("number");
            var symbol = document.getElementById("symbol");
            var match = document.getElementById("match");

            var regexLength = /.{12,}/;
            var regexUppercase = /[A-Z]/;
            var regexLowercase = /[a-z]/;
            var regexNumber = /\d/;
            var regexSymbol = /[@$!%*?&]/;

            // Check password requirements
            length.classList.toggle("valid", regexLength.test(password));
            uppercase.classList.toggle("valid", regexUppercase.test(password));
            lowercase.classList.toggle("valid", regexLowercase.test(password));
            number.classList.toggle("valid", regexNumber.test(password));
            symbol.classList.toggle("valid", regexSymbol.test(password));
            match.classList.toggle("valid", password === confirmPassword);

            // Overall validation
            return regexLength.test(password) &&
                   regexUppercase.test(password) &&
                   regexLowercase.test(password) &&
                   regexNumber.test(password) &&
                   regexSymbol.test(password) &&
                   password === confirmPassword;
        }

        document.addEventListener("DOMContentLoaded", function() {
            document.getElementById("password").addEventListener("input", validatePassword);
            document.getElementById("confirmPassword").addEventListener("input", validatePassword);
        });
    </script>
</head>

<body class="bg-gradient-login" style="background-image: url('img/logo/loral1.jpg');">
    <!-- Register Content -->
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
                                        <h1 class="h4 text-gray-900 mb-4">Register Panel</h1>
                                    </div>
                                    <form class="user" method="Post" action="register.php" onsubmit="return validatePassword()">
                                        <div class="form-group">
                                            <input type="text" class="form-control" required name="fullName" placeholder="Enter Full Name">
                                        </div>
                                        <div class="form-group">
                                            <input type="email" class="form-control" required name="email" placeholder="Enter Email">
                                        </div>
                                        <div class="form-group">
                                            <input type="text" class="form-control" required name="phone" placeholder="Enter Phone Number">
                                        </div>
                                        <div class="form-group">
                                            <input type="text" class="form-control" required name="admissionNumber" placeholder="Enter Admission Number">
                                        </div>
                                        <div class="form-group">
                                            <input type="password" class="form-control" required name="password" id="password" placeholder="Enter Password">
                                            <small class="password-requirements">
                                                <span id="length">Minimum 12 characters long</span>
                                                <span id="uppercase">At least 1 uppercase letter</span>
                                                <span id="lowercase">At least 1 lowercase letter</span>
                                                <span id="number">At least 1 number</span>
                                                <span id="symbol">At least 1 symbol (@$!%*?&)</span>
                                            </small>
                                        </div>
                                        <div class="form-group">
                                            <input type="password" class="form-control" required name="confirmPassword" id="confirmPassword" placeholder="Confirm Password">
                                            <small class="password-requirements">
                                                <span id="match">Passwords must match</span>
                                            </small>
                                        </div>
                                        <div class="form-group">
                                            <button type="submit" name="register" class="btn btn-primary btn-block">Register</button>
                                        </div>
                                    </form>
                                    <hr>
                                    <div class="text-center">
                                        <a class="font-weight-bold small" href="index.php">Already have an account?</a>
                                    </div>
                                    <hr>
                                
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <!-- Register Content -->
</body>

</html>


