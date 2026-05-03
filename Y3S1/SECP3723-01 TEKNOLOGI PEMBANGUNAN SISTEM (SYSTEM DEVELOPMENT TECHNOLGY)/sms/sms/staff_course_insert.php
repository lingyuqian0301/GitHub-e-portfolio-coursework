<?php
include 'dbconnect.php';
include 'mysession.php'; // Critical: Protect this processing file too!
include 'email_helper.php';

// Security Check: Only Staff (01) allowed
if (!isset($_SESSION['u_type']) || $_SESSION['u_type'] != '01') {
    header("Location: login.php");
    exit();
}

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // 1. Retrieve Data from Form
    $c_code = strtoupper(trim($_POST['c_code'])); // Force uppercase for consistency
    $c_section = $_POST['c_section'];
    $c_name = trim($_POST['c_name']);
    $c_credit = $_POST['c_credit'];
    $c_max = $_POST['c_max_students'];
    $u_id_lecturer = $_POST['u_id_lecturer'];

    // 2. Logic: Handle "No Lecturer Selected"
    // If the dropdown value is empty, set it to NULL for the database
    if (empty($u_id_lecturer)) {
        $u_id_lecturer = NULL;
    }

    // 3. Prepare SQL Insert
    $sql = "INSERT INTO tb_course (c_code, c_section, c_name, c_credit, c_max_students, u_id_lecturer) 
            VALUES (?, ?, ?, ?, ?, ?)";
    
    $stmt = mysqli_stmt_init($con);

    if (!mysqli_stmt_prepare($stmt, $sql)) {
        // Technical error
        echo "SQL Error: " . mysqli_error($con);
    } else {
        // 4. Bind Parameters
        // s = string, i = integer
        mysqli_stmt_bind_param($stmt, "sisiii", $c_code, $c_section, $c_name, $c_credit, $c_max, $u_id_lecturer);

        // 5. Execute and Redirect
        try {
            if (mysqli_stmt_execute($stmt)) {
                // Success: Send email notification to staff and lecturer
                $staff_email = $_SESSION['u_email'] ?? 'admin@sms.utm.my';
                $staff_subject = "Course Created: $c_code";
                $staff_msg = "Dear Staff,\n\nA new course has been created:\n\nCourse Code: $c_code\nSection: $c_section\nName: $c_name\nCredits: $c_credit\nMax Students: $c_max\n\nRegards,\nSMS Admin";
                send_notification_email($staff_email, $staff_subject, $staff_msg);
                
                // If lecturer is assigned, notify them
                if (!empty($u_id_lecturer) && $u_id_lecturer != null) {
                    $lec_sql = "SELECT u_email, u_name FROM tb_user WHERE u_id = ?";
                    $lec_stmt = mysqli_stmt_init($con);
                    if (mysqli_stmt_prepare($lec_stmt, $lec_sql)) {
                        mysqli_stmt_bind_param($lec_stmt, "i", $u_id_lecturer);
                        mysqli_stmt_execute($lec_stmt);
                        $lec_res = mysqli_stmt_get_result($lec_stmt);
                        if ($lec_row = mysqli_fetch_assoc($lec_res)) {
                            $lec_email = $lec_row['u_email'];
                            $lec_name = $lec_row['u_name'];
                            $lec_subject = "New Course Assignment: $c_code";
                            $lec_msg = "Dear $lec_name,\n\nYou have been assigned to teach a new course:\n\nCourse Code: $c_code\nSection: $c_section\nName: $c_name\nCredits: $c_credit\nMax Students: $c_max\n\nRegards,\nSMS Admin";
                            send_notification_email($lec_email, $lec_subject, $lec_msg);
                        }
                        mysqli_stmt_close($lec_stmt);
                    }
                }
                
                header("Location: staff.php?msg=Course Added Successfully");
                exit();
            } else {
                // Failure: Usually a duplicate Primary Key (Code + Section already exists)
                $error_msg = mysqli_error($con);
                if (strpos($error_msg, 'Duplicate') !== false) {
                    header("Location: staff_course_add.php?msg=Error: Course $c_code Section $c_section already exists. Please use a different code or section.");
                } else {
                    header("Location: staff_course_add.php?msg=Error: Could not add course. Database error occurred.");
                }
                exit();
            }
        } catch (mysqli_sql_exception $e) {
            if (strpos($e->getMessage(), 'Duplicate') !== false) {
                header("Location: staff_course_add.php?msg=Error: Course $c_code Section $c_section already exists. Please use a different code or section.");
            } else {
                header("Location: staff_course_add.php?msg=Error: " . urlencode($e->getMessage()));
            }
            exit();
        }
    }
} else {
    // If someone tries to open this file directly without submitting the form
    header("Location: staff.php");
    exit();
}
?>
