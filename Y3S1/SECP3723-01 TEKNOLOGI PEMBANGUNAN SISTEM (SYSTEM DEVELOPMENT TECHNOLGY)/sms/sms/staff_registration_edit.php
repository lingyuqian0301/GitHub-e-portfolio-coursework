<?php
include 'dbconnect.php';
include 'headerstaff.php';
include 'email_helper.php';

// Security
if (!isset($_SESSION['u_type']) || $_SESSION['u_type'] != '01') {
    header('Location: login.php');
    exit();
}

$msg = "";
$reg_id = $_GET['id'] ?? null;
$row = null;

if (!$reg_id) {
    header("Location: staff_registrations_view.php");
    exit();
}

// Handle Update
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $reg_id = $_POST['reg_id'];
    $new_course_str = $_POST['course_select']; // Format: CODE|SECTION

    if ($new_course_str) {
        list($new_code, $new_section) = explode('|', $new_course_str);
        
        // Get the student ID and current course for this registration
        $getStudentSql = "SELECT u_id_student, c_code, c_section FROM tb_registration WHERE reg_id = ?";
        $stmtCheck = mysqli_stmt_init($con);
        if (mysqli_stmt_prepare($stmtCheck, $getStudentSql)) {
            mysqli_stmt_bind_param($stmtCheck, "i", $reg_id);
            mysqli_stmt_execute($stmtCheck);
            $resultCheck = mysqli_stmt_get_result($stmtCheck);
            $regData = mysqli_fetch_assoc($resultCheck);
            
            // Check if student is already registered for the new course code (different registration)
            if ($regData && $new_code !== $regData['c_code']) {
                $checkDupSql = "SELECT reg_id FROM tb_registration WHERE u_id_student = ? AND c_code = ? AND reg_id != ?";
                $stmtDup = mysqli_stmt_init($con);
                if (mysqli_stmt_prepare($stmtDup, $checkDupSql)) {
                    mysqli_stmt_bind_param($stmtDup, "isi", $regData['u_id_student'], $new_code, $reg_id);
                    mysqli_stmt_execute($stmtDup);
                    $resultDup = mysqli_stmt_get_result($stmtDup);
                    
                    if (mysqli_num_rows($resultDup) > 0) {
                        $msg = "<div class='alert alert-danger'>Error: Student is already registered for course <strong>$new_code</strong>. Students cannot register for multiple sections of the same course.</div>";
                        mysqli_stmt_close($stmtDup);
                        mysqli_stmt_close($stmtCheck);
                        goto skip_update;
                    }
                    mysqli_stmt_close($stmtDup);
                }
            }
            mysqli_stmt_close($stmtCheck);
        }
        
        // Update
        $sql = "UPDATE tb_registration SET c_code = ?, c_section = ? WHERE reg_id = ?";
        $stmt = mysqli_stmt_init($con);
        if (mysqli_stmt_prepare($stmt, $sql)) {
            mysqli_stmt_bind_param($stmt, "ssi", $new_code, $new_section, $reg_id);
            try {
                if (mysqli_stmt_execute($stmt)) {
                    // Send email notification to student
                    $student_sql = "SELECT u.u_email, u.u_name FROM tb_registration r JOIN tb_user u ON r.u_id_student = u.u_id WHERE r.reg_id = ?";
                    $stmt_email = mysqli_stmt_init($con);
                    if (mysqli_stmt_prepare($stmt_email, $student_sql)) {
                        mysqli_stmt_bind_param($stmt_email, "i", $reg_id);
                        mysqli_stmt_execute($stmt_email);
                        $res_email = mysqli_stmt_get_result($stmt_email);
                        if ($email_row = mysqli_fetch_assoc($res_email)) {
                            $old_course = $regData['c_code'] . " (Section " . $regData['c_section'] . ")";
                            $new_course = $new_code . " (Section " . $new_section . ")";
                            $subject = "Registration Updated: Course Changed";
                            $msg_email = "Dear " . $email_row['u_name'] . ",\n\nYour registration has been amended by staff.\n\nFrom: " . $old_course . "\nTo: " . $new_course . "\n\nRegards,\nSMS Admin";
                            send_notification_email($email_row['u_email'], $subject, $msg_email);
                        }
                        mysqli_stmt_close($stmt_email);
                    }
                    
                    header("Location: staff_registrations_view.php?msg=Registration Amended Successfully");
                    exit();
                } else {
                    $msg = "<div class='alert alert-danger'>Error updating registration: " . mysqli_error($con) . "</div>";
                }
            } catch (mysqli_sql_exception $e) {
                if (strpos($e->getMessage(), 'unique_student_course') !== false || strpos($e->getMessage(), 'Duplicate entry') !== false) {
                    $msg = "<div class='alert alert-danger'>Error: Student is already registered for course <strong>$new_code</strong>. Students cannot register for multiple sections of the same course.</div>";
                } else {
                    $msg = "<div class='alert alert-danger'>Error updating registration: " . $e->getMessage() . "</div>";
                }
            }
        }
    }
    skip_update:
}

// Fetch Registration Details
$sql = "SELECT r.reg_id, r.c_code, r.c_section, u.u_name, u.u_id 
        FROM tb_registration r 
        JOIN tb_user u ON r.u_id_student = u.u_id 
        WHERE r.reg_id = ?";
$stmt = mysqli_stmt_init($con);
mysqli_stmt_prepare($stmt, $sql);
mysqli_stmt_bind_param($stmt, "i", $reg_id);
mysqli_stmt_execute($stmt);
$res = mysqli_stmt_get_result($stmt);
$row = mysqli_fetch_assoc($res);

if (!$row) {
    echo "Registration not found.";
    exit();
}

// Fetch All Courses for Dropdown
$courses = [];
$c_res = mysqli_query($con, "SELECT c_code, c_section, c_name FROM tb_course ORDER BY c_code");
while ($c = mysqli_fetch_assoc($c_res)) {
    $courses[] = $c;
}

?>

<div class="container mt-5">
    <div class="card shadow">
        <div class="card-header bg-warning text-dark">
            <h4>Amend Registration</h4>
        </div>
        <div class="card-body">
            <?php if ($msg): ?>
                <?php echo $msg; ?>
            <?php endif; ?>

            <form method="POST">
                <input type="hidden" name="reg_id" value="<?php echo $row['reg_id']; ?>">
                
                <div class="mb-3">
                    <label class="form-label">Student Name</label>
                    <input type="text" class="form-control" value="<?php echo htmlspecialchars($row['u_name']); ?>" disabled>
                </div>

                <div class="mb-3">
                    <label class="form-label">Select Course</label>
                    <select name="course_select" class="form-select" required>
                        <?php foreach ($courses as $c): ?>
                            <?php 
                                $val = $c['c_code'] . "|" . $c['c_section'];
                                $display = $c['c_code'] . " - " . $c['c_name'] . " (Sec " . $c['c_section'] . ")";
                                $selected = ($c['c_code'] == $row['c_code'] && $c['c_section'] == $row['c_section']) ? 'selected' : '';
                            ?>
                            <option value="<?php echo $val; ?>" <?php echo $selected; ?>>
                                <?php echo htmlspecialchars($display); ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>

                <button type="submit" class="btn btn-primary">Save Changes</button>
                <a href="staff_registrations_view.php" class="btn btn-secondary">Cancel</a>
            </form>
        </div>
    </div>
</div>

<?php include 'footer.php'; ?>