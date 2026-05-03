<?php
/**
 * Staff Course Modify and Delete Processing
 * Handles course updates and deletion with email notifications
 */

session_start();
include('dbconnect.php');
include('email_helper.php');
include('csrf.php');

// Security Check: Only Staff (01) allowed
if (!isset($_SESSION['u_id']) || $_SESSION['u_type'] != '01') {
    header("Location: login.php");
    exit();
}

// Validate CSRF token for POST requests
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    if (!isset($_POST['csrf_token']) || !csrf_verify($_POST['csrf_token'])) {
        $_SESSION['error'] = "Security token validation failed";
        header("Location: staff.php");
        exit();
    }
}

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $action = htmlspecialchars($_POST['action'] ?? '');
    $old_code = strtoupper(htmlspecialchars($_POST['old_code'] ?? ''));
    $old_section = htmlspecialchars($_POST['old_section'] ?? '');
    
    // ===== UPDATE COURSE =====
    if ($action === 'edit') {
        // New Data (SET clause)
        $c_code = strtoupper(htmlspecialchars(trim($_POST['c_code'] ?? '')));
        $c_section = htmlspecialchars($_POST['c_section'] ?? '');
        $c_name = htmlspecialchars(trim($_POST['c_name'] ?? ''));
        $c_credit = intval($_POST['c_credit'] ?? 0);
        $c_max = intval($_POST['c_max_students'] ?? 0);
        $u_id_lecturer = !empty($_POST['u_id_lecturer']) ? intval($_POST['u_id_lecturer']) : NULL;
        
        $errors = [];
        if (empty($c_code)) $errors[] = "Course code is required";
        if (empty($c_name)) $errors[] = "Course name is required";
        if ($c_credit <= 0) $errors[] = "Credits must be greater than 0";
        if ($c_max <= 0) $errors[] = "Max students must be greater than 0";
        
        if (!empty($errors)) {
            $_SESSION['errors'] = $errors;
            header("Location: staff_course_edit.php?code=$old_code&sec=$old_section");
            exit();
        }
        
        try {
            $sql = "UPDATE tb_course 
                   SET c_code=?, c_section=?, c_name=?, c_credit=?, c_max_students=?, u_id_lecturer=? 
                   WHERE c_code=? AND c_section=?";
            
            $stmt = mysqli_stmt_init($con);
            if (!mysqli_stmt_prepare($stmt, $sql)) {
                throw new Exception("Database error: " . mysqli_error($con));
            }
            
            mysqli_stmt_bind_param($stmt, "sisiisss", $c_code, $c_section, $c_name, $c_credit, $c_max, $u_id_lecturer, $old_code, $old_section);
            
            if (!mysqli_stmt_execute($stmt)) {
                throw new Exception("Failed to update course: " . mysqli_error($con));
            }
            
            mysqli_stmt_close($stmt);
            
            $_SESSION['success'] = "Course updated successfully";
            
            // Notify lecturer if assigned
            if ($u_id_lecturer) {
                $lecSql = "SELECT u_email, u_name FROM tb_user WHERE u_id = ?";
                $lecStmt = mysqli_stmt_init($con);
                if (mysqli_stmt_prepare($lecStmt, $lecSql)) {
                    mysqli_stmt_bind_param($lecStmt, "i", $u_id_lecturer);
                    mysqli_stmt_execute($lecStmt);
                    $lecRes = mysqli_stmt_get_result($lecStmt);
                    if ($lec = mysqli_fetch_assoc($lecRes)) {
                        $subject = "Course Updated: $c_code";
                        $msg = "Dear {$lec['u_name']},\n\nThe course you're teaching has been updated:\n\n**Code:** $c_code\n**Name:** $c_name\n**Credits:** $c_credit\n**Max Students:** $c_max";
                        send_notification_email($lec['u_email'], $subject, $msg);
                    }
                    mysqli_stmt_close($lecStmt);
                }
            }
            
        } catch (Exception $e) {
            $_SESSION['error'] = "Error: " . $e->getMessage();
        }
    }
    
    // ===== DELETE COURSE =====
    elseif ($action === 'delete') {
        try {
            // Check if course has registered students
            $checkSql = "SELECT COUNT(*) as count FROM tb_registration WHERE c_code = ? AND c_section = ?";
            $checkStmt = mysqli_stmt_init($con);
            if (!mysqli_stmt_prepare($checkStmt, $checkSql)) {
                throw new Exception("Database error");
            }
            mysqli_stmt_bind_param($checkStmt, "ss", $old_code, $old_section);
            mysqli_stmt_execute($checkStmt);
            $checkRes = mysqli_stmt_get_result($checkStmt);
            $check = mysqli_fetch_assoc($checkRes);
            mysqli_stmt_close($checkStmt);
            
            if ($check['count'] > 0) {
                $_SESSION['error'] = "Cannot delete course: {$check['count']} students are registered";
                header("Location: staff.php");
                exit();
            }
            
            // Get course details before deleting
            $detailSql = "SELECT c_name, u_id_lecturer FROM tb_course WHERE c_code = ? AND c_section = ?";
            $detailStmt = mysqli_stmt_init($con);
            if (!mysqli_stmt_prepare($detailStmt, $detailSql)) {
                throw new Exception("Database error");
            }
            mysqli_stmt_bind_param($detailStmt, "ss", $old_code, $old_section);
            mysqli_stmt_execute($detailStmt);
            $detailRes = mysqli_stmt_get_result($detailStmt);
            $course = mysqli_fetch_assoc($detailRes);
            mysqli_stmt_close($detailStmt);
            
            if (!$course) {
                $_SESSION['error'] = "Course not found";
                header("Location: staff.php");
                exit();
            }
            
            // Delete the course
            $deleteSql = "DELETE FROM tb_course WHERE c_code = ? AND c_section = ?";
            $deleteStmt = mysqli_stmt_init($con);
            if (!mysqli_stmt_prepare($deleteStmt, $deleteSql)) {
                throw new Exception("Database error");
            }
            mysqli_stmt_bind_param($deleteStmt, "ss", $old_code, $old_section);
            
            if (!mysqli_stmt_execute($deleteStmt)) {
                throw new Exception("Failed to delete course");
            }
            mysqli_stmt_close($deleteStmt);
            
            // Notify lecturer if assigned
            if ($course['u_id_lecturer']) {
                $lecSql = "SELECT u_email, u_name FROM tb_user WHERE u_id = ?";
                $lecStmt = mysqli_stmt_init($con);
                if (mysqli_stmt_prepare($lecStmt, $lecSql)) {
                    mysqli_stmt_bind_param($lecStmt, "i", $course['u_id_lecturer']);
                    mysqli_stmt_execute($lecStmt);
                    $lecRes = mysqli_stmt_get_result($lecStmt);
                    if ($lec = mysqli_fetch_assoc($lecRes)) {
                        $subject = "Course Deleted: $old_code";
                        $msg = "Dear {$lec['u_name']},\n\nThe course assignment has been removed:\n\n**Code:** $old_code\n**Name:** {$course['c_name']}";
                        send_notification_email($lec['u_email'], $subject, $msg);
                    }
                    mysqli_stmt_close($lecStmt);
                }
            }
            
            $_SESSION['success'] = "Course deleted successfully";
            
        } catch (Exception $e) {
            $_SESSION['error'] = "Error: " . $e->getMessage();
        }
    }
}

header("Location: staff.php");
exit();

?>

