<?php
include 'dbconnect.php';
include 'headerlec.php';

// Security: Ensure User is Lecturer
if (!isset($_SESSION['u_type']) || $_SESSION['u_type'] != '02') {
    if (isset($_SESSION['u_type']) && $_SESSION['u_type'] == '01') {
        header("Location: staff.php");
    } else {
        header("Location: login.php");
    }
    exit();
}

// Validate input
if (!isset($_GET['u_id']) || !isset($_GET['c_code']) || !isset($_GET['c_section'])) {
    echo '<div class="container mt-5"><div class="alert alert-danger">Missing parameters.</div></div>';
    include 'footer.php';
    exit(); 
}

$u_id = $_GET['u_id'];
$c_code = $_GET['c_code'];
$c_section = $_GET['c_section'];
$lecturer_id = $_SESSION['u_id'];

// Verify lecturer owns the course
$sqlCourse = "SELECT u_id_lecturer FROM tb_course WHERE c_code = ? AND c_section = ?";
$stmtCourse = mysqli_stmt_init($con);
if (!mysqli_stmt_prepare($stmtCourse, $sqlCourse)) {
    echo '<div class="container mt-5"><div class="alert alert-danger">Unable to verify course.</div></div>';
    include 'footer.php';
    exit();
}
mysqli_stmt_bind_param($stmtCourse, "ss", $c_code, $c_section);
mysqli_stmt_execute($stmtCourse);
$cres = mysqli_stmt_get_result($stmtCourse);
if ($crow = mysqli_fetch_assoc($cres)) {
    if ($crow['u_id_lecturer'] != $lecturer_id) {
        echo '<div class="container mt-5"><div class="alert alert-danger">Unauthorized.</div></div>';
        include 'footer.php';
        exit();
    }
} else {
    echo '<div class="container mt-5"><div class="alert alert-info">Course not found.</div></div>';
    include 'footer.php';
    exit();
}
mysqli_stmt_close($stmtCourse);

// Fetch student details (include phone operator)
$sql = "SELECT u.u_id, u.u_name, u.u_email, u.u_phone, u.u_phoneperator, u.u_gender, p.p_name
    FROM tb_user u
    LEFT JOIN tb_programme p ON u.u_programme = p.p_id
    WHERE u.u_id = ?";
$stmt = mysqli_stmt_init($con);
if (!mysqli_stmt_prepare($stmt, $sql)) {
    echo '<div class="container mt-5"><div class="alert alert-danger">Unable to load student details.</div></div>';
    include 'footer.php';
    exit();
}
mysqli_stmt_bind_param($stmt, "s", $u_id);
mysqli_stmt_execute($stmt);
$res = mysqli_stmt_get_result($stmt);

if ($student = mysqli_fetch_assoc($res)) {
    ?>
    <div class="container mt-5">
        <h2>Student Details</h2>
        <table class="table table-bordered" style="max-width:800px;">
            <tr><th>Student ID</th><td><?php echo htmlspecialchars($student['u_id']); ?></td></tr>
            <tr><th>Name</th><td><?php echo htmlspecialchars($student['u_name']); ?></td></tr>
            <tr><th>Email</th><td><?php echo htmlspecialchars($student['u_email']); ?></td></tr>
            <tr><th>Phone</th><td><?php 
                $op = isset($student['u_phoneperator']) ? (string)$student['u_phoneperator'] : '';
                if ($op !== '' && substr($op, 0, 1) !== '0') { $op = '0' . $op; }
                $fullPhone = trim($op) . (isset($student['u_phone']) ? (string)$student['u_phone'] : '');
                echo htmlspecialchars($fullPhone);
            ?></td></tr>
            <tr><th>Gender</th><td><?php echo htmlspecialchars($student['u_gender']); ?></td></tr>
            <tr><th>Programme</th><td><?php echo htmlspecialchars($student['p_name'] ?? '-'); ?></td></tr>
        </table>

        <a class="btn btn-secondary" href="lecturer_view_students.php?c_code=<?php echo urlencode($c_code); ?>&c_section=<?php echo urlencode($c_section); ?>">Back to Student List</a>
        <a class="btn btn-primary" href="lecturer.php">Dashboard</a>
    </div>
    <?php
} else {
    echo '<div class="container mt-5"><div class="alert alert-info">Student not found.</div></div>';
}

mysqli_stmt_close($stmt);
include 'footer.php';
?>
