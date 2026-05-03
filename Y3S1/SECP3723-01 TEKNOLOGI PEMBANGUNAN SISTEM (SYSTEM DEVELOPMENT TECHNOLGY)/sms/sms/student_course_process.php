<?php
/**
 * Student Course Registration Processing
 * Handles: Register, Cancel, Modify Registration
 * Requirement 8g: Auto-approve if seats available
 */

session_start();
include('dbconnect.php');
include('email_helper.php');
include('csrf.php');

// Security: Check if user is logged in as student
if (!isset($_SESSION['u_id']) || $_SESSION['u_type'] != '03') {
    header("Location: login.php");
    exit();
}

$action = htmlspecialchars($_POST['action'] ?? $_GET['action'] ?? '');
$u_id = $_SESSION['u_id'];
$CURRENT_SEM = '2024/2025';

// Validate CSRF token for POST requests
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    if (!isset($_POST['csrf_token']) || !csrf_verify($_POST['csrf_token'])) {
        $_SESSION['error'] = "Security token validation failed";
        header("Location: student_course_register.php");
        exit();
    }
}

// ===== REGISTER FOR COURSE =====
if ($action === 'register' || $_SERVER['REQUEST_METHOD'] == 'POST') {
    $c_code = htmlspecialchars($_POST['c_code'] ?? '');
    $c_section = htmlspecialchars($_POST['c_section'] ?? '');
    // Use configured current semester to keep new registrations in current tab
    $semester = htmlspecialchars($_POST['semester'] ?? $CURRENT_SEM);
    
    if (empty($c_code) || empty($c_section)) {
        $_SESSION['error'] = "Invalid course selection";
        header("Location: student_course_register.php");
        exit();
    }
    
    try {
        // Check if student is already registered for this course/section
        $checkSql = "SELECT reg_id FROM tb_registration 
                     WHERE u_id_student = ? AND c_code = ? AND c_section = ?";
        $stmtCheck = mysqli_stmt_init($con);
        
        if (!mysqli_stmt_prepare($stmtCheck, $checkSql)) {
            throw new Exception("Database error: " . mysqli_error($con));
        }
        
        mysqli_stmt_bind_param($stmtCheck, "iss", $u_id, $c_code, $c_section);
        mysqli_stmt_execute($stmtCheck);
        
        if (mysqli_stmt_get_result($stmtCheck)->num_rows > 0) {
            $_SESSION['error'] = "You are already registered for this course section";
            mysqli_stmt_close($stmtCheck);
            header("Location: student_course_register.php");
            exit();
        }
        mysqli_stmt_close($stmtCheck);
        
        // Get course details and current enrollment
        $courseSql = "SELECT c_name, c_max_students, 
                     COUNT(DISTINCT r.u_id_student) as enrolled 
                     FROM tb_course c
                     LEFT JOIN tb_registration r ON c.c_code = r.c_code AND c.c_section = r.c_section AND r.reg_status = 'Approved'
                     WHERE c.c_code = ? AND c.c_section = ?
                     GROUP BY c.c_code, c.c_section";
        
        $stmtCourse = mysqli_stmt_init($con);
        if (!mysqli_stmt_prepare($stmtCourse, $courseSql)) {
            throw new Exception("Database error: " . mysqli_error($con));
        }
        
        mysqli_stmt_bind_param($stmtCourse, "ss", $c_code, $c_section);
        mysqli_stmt_execute($stmtCourse);
        $courseResult = mysqli_stmt_get_result($stmtCourse);
        
        if ($courseResult->num_rows === 0) {
            $_SESSION['error'] = "Course not found";
            mysqli_stmt_close($stmtCourse);
            header("Location: student_course_register.php");
            exit();
        }
        
        $course = mysqli_fetch_assoc($courseResult);
        mysqli_stmt_close($stmtCourse);
        
        // Requirement 8g: Auto-approve if seats available
        $status = ($course['enrolled'] < $course['c_max_students']) ? 'Approved' : 'Pending';

        // Insert registration (schema has no reg_date / reg_approval_date columns)
        $insertSql = "INSERT INTO tb_registration 
             (u_id_student, c_code, c_section, reg_semester, reg_status) 
             VALUES (?, ?, ?, ?, ?)";
        
        $stmtInsert = mysqli_stmt_init($con);
        if (!mysqli_stmt_prepare($stmtInsert, $insertSql)) {
            throw new Exception("Database error: " . mysqli_error($con));
        }
        
        mysqli_stmt_bind_param($stmtInsert, "issss", $u_id, $c_code, $c_section, $semester, $status);
        
        if (!mysqli_stmt_execute($stmtInsert)) {
            throw new Exception("Failed to register: " . mysqli_error($con));
        }
        mysqli_stmt_close($stmtInsert);
        
        // Get student email for notification
        $userSql = "SELECT u_name, u_email FROM tb_user WHERE u_id = ?";
        $stmtUser = mysqli_stmt_init($con);
        mysqli_stmt_prepare($stmtUser, $userSql);
        mysqli_stmt_bind_param($stmtUser, "i", $u_id);
        mysqli_stmt_execute($stmtUser);
        $userResult = mysqli_stmt_get_result($stmtUser);
        $user = mysqli_fetch_assoc($userResult);
        mysqli_stmt_close($stmtUser);
        
        // Send appropriate email notification
        if ($status === 'Approved') {
            send_registration_approval_email($user['u_email'], $user['u_name'], 
                                             $course['c_name'], $c_code, $c_section, $semester);
            $_SESSION['success'] = "Successfully registered for {$course['c_name']} (Approved)";
        } else {
            send_registration_pending_email($user['u_email'], $user['u_name'], 
                                           $course['c_name'], $c_code, $c_section, $semester);
            $_SESSION['success'] = "Registration submitted. Status: Pending (Course is full)";
        }
        
        header("Location: student_courses_view.php");
        exit();
        
    } catch (Exception $e) {
        $_SESSION['error'] = "Error: " . $e->getMessage();
        header("Location: student_course_register.php");
        exit();
    }
}

// ===== CANCEL REGISTRATION =====
elseif ($action === 'cancel') {
    $reg_id = intval($_POST['reg_id'] ?? 0);
    
    if ($reg_id <= 0) {
        $_SESSION['error'] = "Invalid registration ID";
        header("Location: student_courses_view.php");
        exit();
    }
    
    try {
        // Verify this registration belongs to the current student
        $verifySql = "SELECT c_code, c_section FROM tb_registration 
                     WHERE reg_id = ? AND u_id_student = ?";
        $stmtVerify = mysqli_stmt_init($con);
        mysqli_stmt_prepare($stmtVerify, $verifySql);
        mysqli_stmt_bind_param($stmtVerify, "ii", $reg_id, $u_id);
        mysqli_stmt_execute($stmtVerify);
        $verifyResult = mysqli_stmt_get_result($stmtVerify);
        
        if ($verifyResult->num_rows === 0) {
            $_SESSION['error'] = "Unauthorized action";
            mysqli_stmt_close($stmtVerify);
            header("Location: student_courses_view.php");
            exit();
        }
        
        $regData = mysqli_fetch_assoc($verifyResult);
        mysqli_stmt_close($stmtVerify);
        
        // Delete the registration
        $deleteSql = "DELETE FROM tb_registration WHERE reg_id = ?";
        $stmtDelete = mysqli_stmt_init($con);
        mysqli_stmt_prepare($stmtDelete, $deleteSql);
        mysqli_stmt_bind_param($stmtDelete, "i", $reg_id);
        
        if (!mysqli_stmt_execute($stmtDelete)) {
            throw new Exception("Failed to cancel registration");
        }
        mysqli_stmt_close($stmtDelete);
        
        // Get course name for email
        $courseSql = "SELECT c_name FROM tb_course WHERE c_code = ? AND c_section = ?";
        $stmtCourse = mysqli_stmt_init($con);
        mysqli_stmt_prepare($stmtCourse, $courseSql);
        mysqli_stmt_bind_param($stmtCourse, "ss", $regData['c_code'], $regData['c_section']);
        mysqli_stmt_execute($stmtCourse);
        $courseResult = mysqli_stmt_get_result($stmtCourse);
        $course = mysqli_fetch_assoc($courseResult);
        mysqli_stmt_close($stmtCourse);
        
        // Get student email
        $userSql = "SELECT u_name, u_email FROM tb_user WHERE u_id = ?";
        $stmtUser = mysqli_stmt_init($con);
        mysqli_stmt_prepare($stmtUser, $userSql);
        mysqli_stmt_bind_param($stmtUser, "i", $u_id);
        mysqli_stmt_execute($stmtUser);
        $userResult = mysqli_stmt_get_result($stmtUser);
        $user = mysqli_fetch_assoc($userResult);
        mysqli_stmt_close($stmtUser);
        
        // Send cancellation email
        send_cancellation_email($user['u_email'], $user['u_name'], 
                               $course['c_name'], $regData['c_code'], $regData['c_section']);
        
        $_SESSION['success'] = "Course registration cancelled successfully";
        header("Location: student_courses_view.php");
        exit();
        
    } catch (Exception $e) {
        $_SESSION['error'] = "Error: " . $e->getMessage();
        header("Location: student_courses_view.php");
        exit();
    }
}

// ===== MODIFY REGISTRATION (Change Section) =====
elseif ($action === 'modify') {
    $reg_id = intval($_POST['reg_id'] ?? 0);
    $new_c_section = htmlspecialchars($_POST['new_c_section'] ?? '');
    
    if ($reg_id <= 0 || empty($new_c_section)) {
        $_SESSION['error'] = "Invalid request";
        header("Location: student_courses_view.php");
        exit();
    }
    
    try {
        // Get current registration details
        $currentSql = "SELECT u_id_student, c_code FROM tb_registration WHERE reg_id = ?";
        $stmtCurrent = mysqli_stmt_init($con);
        mysqli_stmt_prepare($stmtCurrent, $currentSql);
        mysqli_stmt_bind_param($stmtCurrent, "i", $reg_id);
        mysqli_stmt_execute($stmtCurrent);
        $currentResult = mysqli_stmt_get_result($stmtCurrent);
        
        if ($currentResult->num_rows === 0) {
            $_SESSION['error'] = "Registration not found";
            mysqli_stmt_close($stmtCurrent);
            header("Location: student_courses_view.php");
            exit();
        }
        
        $current = mysqli_fetch_assoc($currentResult);
        mysqli_stmt_close($stmtCurrent);
        
        // Check if user owns this registration
        if ($current['u_id_student'] != $u_id) {
            $_SESSION['error'] = "Unauthorized action";
            header("Location: student_courses_view.php");
            exit();
        }
        
        // Check if already registered for new section
        $checkSql = "SELECT reg_id FROM tb_registration 
                     WHERE u_id_student = ? AND c_code = ? AND c_section = ?";
        $stmtCheck = mysqli_stmt_init($con);
        mysqli_stmt_prepare($stmtCheck, $checkSql);
        mysqli_stmt_bind_param($stmtCheck, "iss", $u_id, $current['c_code'], $new_c_section);
        mysqli_stmt_execute($stmtCheck);
        
        if (mysqli_stmt_get_result($stmtCheck)->num_rows > 0) {
            $_SESSION['error'] = "You are already registered for this section";
            mysqli_stmt_close($stmtCheck);
            header("Location: student_courses_view.php");
            exit();
        }
        mysqli_stmt_close($stmtCheck);
        
        // Update registration with new section (schema has no reg_approval_date)
        $updateSql = "UPDATE tb_registration SET c_section = ?, reg_status = 'Approved' 
                 WHERE reg_id = ?";
        $stmtUpdate = mysqli_stmt_init($con);
        mysqli_stmt_prepare($stmtUpdate, $updateSql);
        mysqli_stmt_bind_param($stmtUpdate, "si", $new_c_section, $reg_id);
        
        if (!mysqli_stmt_execute($stmtUpdate)) {
            throw new Exception("Failed to modify registration");
        }
        mysqli_stmt_close($stmtUpdate);
        
        $_SESSION['success'] = "Registration modified successfully";
        header("Location: student_courses_view.php");
        exit();
        
    } catch (Exception $e) {
        $_SESSION['error'] = "Error: " . $e->getMessage();
        header("Location: student_courses_view.php");
        exit();
    }
}

else {
    $_SESSION['error'] = "Invalid action";
    header("Location: student_course_register.php");
    exit();
}

?>
