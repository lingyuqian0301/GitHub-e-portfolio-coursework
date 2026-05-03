<?php
include 'mysession.php';
include 'dbconnect.php';

// Check if user is student
if (!isset($_SESSION['u_id']) || $_SESSION['u_type'] !== '03') {
    header('Location: login.php');
    exit();
}

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: student_profile.php');
    exit();
}

$u_id = $_SESSION['u_id'];
$u_name = trim($_POST['u_name']);
$u_email = trim($_POST['u_email']);
$u_phone = strval(trim($_POST['u_phone'])); // Preserve leading zero
$new_pass = $_POST['new_password'];
$conf_pass = $_POST['confirm_password'];

// Basic validation
if (empty($u_name) || empty($u_email) || empty($u_phone)) {
    header('Location: student_profile.php?msg=Please fill in all required fields');
    exit();
}

// Password update logic
if (!empty($new_pass)) {
    if ($new_pass !== $conf_pass) {
        header('Location: student_profile.php?msg=Passwords do not match');
        exit();
    }
    $hashed_pwd = password_hash($new_pass, PASSWORD_DEFAULT);
    $sql = "UPDATE tb_user SET u_name=?, u_email=?, u_phone=?, u_pwd=? WHERE u_id=?";
    
    $stmt = mysqli_stmt_init($con);
    if (mysqli_stmt_prepare($stmt, $sql)) {
        mysqli_stmt_bind_param($stmt, "ssisi", $u_name, $u_email, $u_phone, $hashed_pwd, $u_id);
    } else {
        header('Location: student_profile.php?msg=SQL Error');
        exit();
    }
} else {
    // No password change
    $sql = "UPDATE tb_user SET u_name=?, u_email=?, u_phone=? WHERE u_id=?";
    
    $stmt = mysqli_stmt_init($con);
    if (mysqli_stmt_prepare($stmt, $sql)) {
        mysqli_stmt_bind_param($stmt, "ssii", $u_name, $u_email, $u_phone, $u_id);
    } else {
        header('Location: student_profile.php?msg=SQL Error');
        exit();
    }
}

if (mysqli_stmt_execute($stmt)) {
    header('Location: student_profile.php?msg=Profile Updated Successfully');
} else {
    header('Location: student_profile.php?msg=Error Updating Profile');
}

mysqli_stmt_close($stmt);
mysqli_close($con);
?>
