<?php
require 'vendor/vendor/autoload.php'; // Adjust the path as necessary
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

include 'Includes/dbcon.php';
session_start();

if (isset($_POST['submit'])) {
    $email = $_POST['email'];
    $_SESSION['email'] = $email;

    // Validate the email format
    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $error = "Invalid email format.";
    } else {
        // Check if the email exists in the database
        $query = "SELECT * FROM tblclassteacher WHERE emailAddress = ?";
        $stmt = $conn->prepare($query);
        $stmt->bind_param("s", $email);
        $stmt->execute();
        $result = $stmt->get_result();

        if ($result->num_rows > 0) {
            // Generate OTP
            $otp = rand(100000, 999999);
            $_SESSION['otp'] = $otp;

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
                $mail->Subject = 'Password Reset OTP';
                $mail->Body    = "Your OTP for password reset is: <b>$otp</b>";

                $mail->send();
                header("Location: verifyOtp.php");
                exit();
            } catch (Exception $e) {
                $error = "Failed to send OTP. Mailer Error: {$mail->ErrorInfo}";
            }
        } else {
            $error = "Email not found in our records.";
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <title>Elizabeth School - Forgot Password</title>
    <link href="vendor/fontawesome-free/css/all.min.css" rel="stylesheet" type="text/css">
    <link href="vendor/bootstrap/css/bootstrap.min.css" rel="stylesheet" type="text/css">
    <link href="css/ruang-admin.min.css" rel="stylesheet">
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
                                        <h1 class="h4 text-gray-900 mb-4">Forgot Password</h1>
                                    </div>

                                    <form class="user" method="POST" action="">
                                        <div class="form-group">
                                            <input type="email" class="form-control" required name="email" placeholder="Enter Email Address">
                                        </div>
                                        <?php if (isset($error)): ?>
                                            <div class="alert alert-danger" role="alert">
                                                <?php echo $error; ?>
                                            </div>
                                        <?php endif; ?>
                                        <div class="form-group">
                                            <input type="submit" class="btn btn-primary btn-block" value="Submit" name="submit" />
                                        </div>
                                    </form>

                                    <hr>
                                    <div class="text-center">
                                        <a class="font-weight-bold small" href="index.php">Back to Login Page</a>
                                    </div>
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
</body>
</html>
