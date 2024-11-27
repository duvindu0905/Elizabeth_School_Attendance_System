<?php
include 'Includes/dbcon.php';
session_start();

if (isset($_POST['verify'])) {
    $enteredOtp = $_POST['otp'];
    $email = $_SESSION['email'];

    $query = "SELECT otp, otpExpiry FROM tblregisterstudents WHERE email='$email'";
    $result = $conn->query($query);
    if ($result->num_rows > 0) {
        $row = $result->fetch_assoc();
        $storedOtp = $row['otp'];
        $otpExpiry = $row['otpExpiry'];

        if ($enteredOtp == $storedOtp && strtotime($otpExpiry) > time()) {
            // OTP is correct and not expired
            $updateQuery = "UPDATE tblregisterstudents SET isConfirmed=1 WHERE email='$email'";
            if ($conn->query($updateQuery) === TRUE) {
                echo "<div class='alert alert-success' role='alert'>
                OTP verified successfully! Your registration is complete.
                </div>";
                
                header("Location: index.php");
                exit();
            } else {
                echo "<div class='alert alert-danger' role='alert'>
                Error updating confirmation status: " . $conn->error . "
                </div>";
            }
        } else {
            echo "<div class='alert alert-danger' role='alert'>
            Invalid OTP or OTP has expired.
            </div>";
        }
    } else {
        echo "<div class='alert alert-danger' role='alert'>
        No record found for the given email.
        </div>";
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Verify OTP</title>
    <link href="vendor/bootstrap/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
    <div class="container">
        <h2 class="text-center">Verify OTP</h2>
        <form method="post" action="registerOtp.php">
            <div class="form-group">
                <label for="otp">Enter OTP:</label>
                <input type="text" class="form-control" id="otp" name="otp" required>
            </div>
            <button type="submit" class="btn btn-primary" name="verify">Verify OTP</button>
        </form>
    </div>
</body>
</html>


