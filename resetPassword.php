<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);

include 'Includes/dbcon.php';
session_start();

if (!isset($_SESSION['email']) || !isset($_SESSION['otp_verified']) || !$_SESSION['otp_verified']) {
    header("Location: forgotPassword.php");
    exit();
}

if (isset($_POST['reset_password'])) {
    $new_password = $_POST['new_password'];
    $confirm_new_password = $_POST['confirm_new_password'];

    if ($new_password != $confirm_new_password) {
        $error = "Passwords do not match.";
    } elseif (strlen($new_password) < 12) {
        $error = "Password must be at least 12 characters long.";
    } elseif (!preg_match('/[A-Z]/', $new_password) || !preg_match('/[a-z]/', $new_password) || !preg_match('/[0-9]/', $new_password) || !preg_match('/[\W]/', $new_password)) {
        $error = "Password must contain at least one uppercase letter, one lowercase letter, one number, and one special character.";
    } else {
        $emailAddress = $_SESSION['email'];
        // Hash the password using MD5
        $hashed_password = md5($new_password);

        $query = "UPDATE tblclassteacher SET password='$hashed_password' WHERE emailAddress='$emailAddress'";

        if ($conn->query($query) === TRUE) {
            echo "<div class='alert alert-success' role='alert'>Password has been reset successfully.</div>";
            session_destroy();
            header("Location: index.php");
            exit();
        } else {
            $error = "Error updating password: " . $conn->error;
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <title>Reset Password</title>
    <link href="vendor/fontawesome-free/css/all.min.css" rel="stylesheet" type="text/css">
    <link href="vendor/bootstrap/css/bootstrap.min.css" rel="stylesheet" type="text/css">
    <link href="css/ruang-admin.min.css" rel="stylesheet">
    <style>
        .password-requirements span {
            display: block;
            font-size: 0.9em;
            margin-top: 5px;
            color: red;
        }
        .password-requirements span.valid {
            color: green;
        }
    </style>
</head>
<body class="bg-gradient-login">
    <div class="container-login">
        <div class="row justify-content-center">
            <div class="col-xl-10 col-lg-12 col-md-9">
                <div class="card shadow-sm my-5">
                    <div class="card-body p-0">
                        <div class="row">
                            <div class="col-lg-12">
                                <div class="login-form">
                                    <div class="text-center">
                                        <img src="img/logo/attnlg.jpg" style="width:100px;height:100px">
                                        <br><br>
                                        <h1 class="h4 text-gray-900 mb-4">Reset Password</h1>
                                    </div>

                                    <form class="user" method="POST" action="">
                                        <div class="form-group">
                                            <input type="password" class="form-control" required name="new_password" id="new_password" placeholder="New Password">
                                        </div>
                                        <div class="form-group">
                                            <input type="password" class="form-control" required name="confirm_new_password" id="confirm_new_password" placeholder="Confirm New Password">
                                        </div>

                                        <div class="password-requirements">
                                            <span id="length">At least 12 characters long</span>
                                            <span id="uppercase">Contains an uppercase letter</span>
                                            <span id="lowercase">Contains a lowercase letter</span>
                                            <span id="number">Contains a number</span>
                                            <span id="symbol">Contains a symbol</span>
                                            <span id="match">Passwords should match</span>
                                        </div>

                                        <?php if (isset($error)): ?>
                                            <div class="alert alert-danger" role="alert">
                                                <?php echo $error; ?>
                                            </div>
                                        <?php endif; ?>

                                        <div class="form-group">
                                            <input type="submit" class="btn btn-primary btn-block" value="Reset Password" name="reset_password" />
                                        </div>
                                    </form>

                                    <hr>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script src="vendor/jquery/jquery.min.js"></script>
    <script src="vendor/bootstrap/js/bootstrap.bundle.min.js"></script>
    <script src="vendor/jquery-easing/jquery.easing.min.js"></script>
    <script src="js/ruang-admin.min.js"></script>
    <script>
        const newPassword = document.getElementById('new_password');
        const confirmNewPassword = document.getElementById('confirm_new_password');
        const lengthRequirement = document.getElementById('length');
        const uppercaseRequirement = document.getElementById('uppercase');
        const lowercaseRequirement = document.getElementById('lowercase');
        const numberRequirement = document.getElementById('number');
        const symbolRequirement = document.getElementById('symbol');
        const matchRequirement = document.getElementById('match');

        function validatePassword() {
            const password = newPassword.value;
            const confirmPassword = confirmNewPassword.value;

            lengthRequirement.classList.toggle('valid', password.length >= 12);
            uppercaseRequirement.classList.toggle('valid', /[A-Z]/.test(password));
            lowercaseRequirement.classList.toggle('valid', /[a-z]/.test(password));
            numberRequirement.classList.toggle('valid', /[0-9]/.test(password));
            symbolRequirement.classList.toggle('valid', /[\W]/.test(password));
            matchRequirement.classList.toggle('valid', password === confirmPassword);
        }

        newPassword.addEventListener('input', validatePassword);
        confirmNewPassword.addEventListener('input', validatePassword);
    </script>
</body>
</html>

