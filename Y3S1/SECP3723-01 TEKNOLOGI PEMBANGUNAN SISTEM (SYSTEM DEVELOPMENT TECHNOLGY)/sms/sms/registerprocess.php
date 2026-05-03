<?php
session_start();

// Connect to database
include('dbconnect.php');
include('email_helper.php');
include('csrf.php');

// Security: Validate CSRF token
if (empty($_POST['csrf_token']) || !csrf_verify($_POST['csrf_token'])) {
    header("Location: register.php?error=security");
    exit();
}

// 1. Retrieve and sanitize data from registration form
$fname = htmlspecialchars(trim($_POST['fname'] ?? ''));
$fpwd = $_POST['fpwd'] ?? '';
$fpwd_confirm = $_POST['fpwd_confirm'] ?? '';
$femail = filter_var(trim($_POST['femail'] ?? ''), FILTER_SANITIZE_EMAIL);
$foperator = htmlspecialchars($_POST['foperator'] ?? '');
$fphone = htmlspecialchars(trim($_POST['fphone'] ?? ''));
$fgender = htmlspecialchars($_POST['fgender'] ?? '');
$fprog = htmlspecialchars($_POST['fprog'] ?? '');
$fcol = htmlspecialchars($_POST['fcol'] ?? '');

// Store old values for re-displaying form
$_SESSION['form_data'] = [
    'fname' => $fname,
    'femail' => $femail,
    'foperator' => $foperator,
    'fphone' => $fphone,
    'fgender' => $fgender,
    'fprog' => $fprog,
    'fcol' => $fcol
];

$errors = [];

// Validate required fields
if (empty($fname)) $errors[] = "Full name is required";
if (empty($femail)) $errors[] = "Email is required";
if (!filter_var($femail, FILTER_VALIDATE_EMAIL)) $errors[] = "Invalid email format";
if (empty($fpwd)) $errors[] = "Password is required";
if (empty($fgender)) $errors[] = "Gender is required";
if (empty($fprog)) $errors[] = "Programme is required";

// 2. PASSWORD VALIDATION (Requirement 8e - Password must match)
if ($fpwd !== $fpwd_confirm) {
    $errors[] = "Passwords do not match";
}

// Password Strength Check (Requirement 8a - Password complexity)
if (!empty($fpwd)) {
    $passwordErrors = validatePasswordStrength($fpwd);
    $errors = array_merge($errors, $passwordErrors);
}

// Check if email already exists
if (!empty($femail)) {
    $checkEmailSql = "SELECT u_id FROM tb_user WHERE u_email = ?";
    $stmtCheck = mysqli_stmt_init($con);
    if (mysqli_stmt_prepare($stmtCheck, $checkEmailSql)) {
        mysqli_stmt_bind_param($stmtCheck, "s", $femail);
        mysqli_stmt_execute($stmtCheck);
        if (mysqli_stmt_get_result($stmtCheck)->num_rows > 0) {
            $errors[] = "Email already registered";
        }
        mysqli_stmt_close($stmtCheck);
    }
}

// If validation errors exist, redirect back to form
if (!empty($errors)) {
    $_SESSION['errors'] = $errors;
    header("Location: register.php");
    exit();
}

// 3. PASSWORD HASHING (Requirement 8a - Encrypt/Hash password)
$hashed_password = password_hash($fpwd, PASSWORD_BCRYPT, ['cost' => 12]);

// 4. PREPARED STATEMENT (Requirement 8c - SQL Injection prevention)
$sql = "INSERT INTO tb_user(u_pwd, u_name, u_phoneperator, u_email, u_gender, u_programme, u_residential, u_type, u_phone)
        VALUES (?, ?, ?, ?, ?, ?, ?, '03', ?)";

$stmt = mysqli_stmt_init($con);

if (!mysqli_stmt_prepare($stmt, $sql)) {
    $_SESSION['errors'] = ["Database error: " . mysqli_error($con)];
    header("Location: register.php");
    exit();
}

// Bind parameters: "s" = string, "i" = integer
mysqli_stmt_bind_param($stmt, "sssssssi", $hashed_password, $fname, $foperator, $femail, $fgender, $fprog, $fcol, $fphone);

// Execute the query
if (mysqli_stmt_execute($stmt)) {
    // Get the newly created user ID
    $new_user_id = mysqli_insert_id($con);
    
    // 5. SEND WELCOME EMAIL (Requirement 9 - Email notification)
    send_welcome_email($femail, $fname, 'student');
    
    // Log successful registration
    error_log("New user registered: Email=$femail, Name=$fname, ID=$new_user_id");
    
    // Clear form data from session
    unset($_SESSION['form_data']);
    
    // Success - Redirect to Login Page
    mysqli_stmt_close($stmt);
    $_SESSION['success'] = "Registration successful! Please login with your credentials.";
    header('Location: login.php');
    exit();
} else {
    $_SESSION['errors'] = ["Registration failed: " . mysqli_error($con)];
    header("Location: register.php");
    exit();
}

/**
 * Validate password strength
 * Requirements: At least 8 chars, 1 uppercase, 1 lowercase, 1 number, 1 special char
 */
function validatePasswordStrength($password) {
    $errors = [];
    
    if (strlen($password) < 8) {
        $errors[] = "Password must be at least 8 characters long";
    }
    if (!preg_match('/[A-Z]/', $password)) {
        $errors[] = "Password must contain at least one uppercase letter";
    }
    if (!preg_match('/[a-z]/', $password)) {
        $errors[] = "Password must contain at least one lowercase letter";
    }
    if (!preg_match('/[0-9]/', $password)) {
        $errors[] = "Password must contain at least one number";
    }
    if (!preg_match('/[!@#$%^&*(),.?":{}|<>]/', $password)) {
        $errors[] = "Password must contain at least one special character (!@#$%^&*...)";
    }
    
    return $errors;
}
?>



















<!-- <?php
//Connect to database
include ('dbconnect.php');
//Retrieve data from registration form
$fname=$_POST['fname'];
$fpwd=$_POST['fpwd'];
$femail=$_POST['femail'];
$foperator=$_POST['foperator'];
$fphone=$_POST['fphone'];
$fgender=$_POST['fgender'];
$fprog=$_POST['fprog'];
$fcol=$_POST['fcol'];

//SQL inser operation -CREATE NEW DATA
$sql="INSERT INTO tb_user(u_pwd,u_name,u_phoneperator,u_email,u_gender,u_programme ,u_residential ,u_type ,u_phone)
VALUES('$fpwd','$fname','$foperator','$femail','$fgender','$fprog','$fcol','03','$fphone')";

//Execute the sql operation
mysqli_query($con,$sql);
//Close the database connection
mysqli_close($con);

//Notification -sucess or fail

//Redirect-Success
header('Location:registerlogin.php');


?> -->