<?php
include 'dbconnect.php';
include 'mysession.php';
include 'email_helper.php';

// 1. Security Check: Only Staff Allowed
if (!isset($_SESSION['u_type']) || $_SESSION['u_type'] != '01') {
    header("Location: login.php");
    exit();
}

// 2. Validate Input
if (isset($_GET['code']) && isset($_GET['sec'])) {
    $c_code = $_GET['code'];
    $c_section = $_GET['sec'];

    // Get affected students BEFORE deletion for email notifications
    $affected_students = [];
    $student_sql = "SELECT DISTINCT u.u_email, u.u_name FROM tb_registration r 
                    JOIN tb_user u ON r.u_id_student = u.u_id 
                    WHERE r.c_code = ? AND r.c_section = ?";
    $stmt_students = mysqli_stmt_init($con);
    if (mysqli_stmt_prepare($stmt_students, $student_sql)) {
        mysqli_stmt_bind_param($stmt_students, "ss", $c_code, $c_section);
        mysqli_stmt_execute($stmt_students);
        $res_students = mysqli_stmt_get_result($stmt_students);
        while ($row = mysqli_fetch_assoc($res_students)) {
            $affected_students[] = $row;
        }
        mysqli_stmt_close($stmt_students);
    }

    // 3. Prepare Delete Query
    // FIRST: Delete related registrations to prevent Foreign Key Error
    $delRegSql = "DELETE FROM tb_registration WHERE c_code = ? AND c_section = ?";
    $stmtReg = mysqli_stmt_init($con);
    if (mysqli_stmt_prepare($stmtReg, $delRegSql)) {
        mysqli_stmt_bind_param($stmtReg, "ss", $c_code, $c_section);
        mysqli_stmt_execute($stmtReg);
        mysqli_stmt_close($stmtReg);
    }

    // SECOND: Delete the course
    $sql = "DELETE FROM tb_course WHERE c_code = ? AND c_section = ?";
    $stmt = mysqli_stmt_init($con);

    if (mysqli_stmt_prepare($stmt, $sql)) {
        mysqli_stmt_bind_param($stmt, "ss", $c_code, $c_section);

        // 4. Execute with Error Handling
        if (mysqli_stmt_execute($stmt)) {
            // Send notification to affected students
            foreach ($affected_students as $student) {
                $subject = "Course Deleted: $c_code";
                $msg = "Dear " . $student['u_name'] . ",\n\nThe course $c_code (Section $c_section) has been deleted from the system.\n\nYour registration for this course has been cancelled.\n\nRegards,\nSMS Admin";
                send_notification_email($student['u_email'], $subject, $msg);
            }
            
            header("Location: staff.php?msg=" . urlencode('Course Deleted Successfully'));
            exit();
        } else {
            $errno = mysqli_errno($con);
            if ($errno == 1451) {
                $error_msg = "Cannot delete course. Students are currently registered for it.";
            } else {
                $error_msg = "Database Error: " . mysqli_error($con);
            }
            header("Location: staff.php?msg=" . urlencode($error_msg));
            exit();
        }
    } else {
        header("Location: staff.php?msg=" . urlencode('SQL Prepare Error'));
        exit();
    }
    mysqli_stmt_close($stmt);
} else {
    // Missing parameters
    header("Location: staff.php");
    exit();
}

?>
