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
if (!isset($_GET['c_code']) || !isset($_GET['c_section'])) {
    echo '<div class="container mt-5"><div class="alert alert-danger">Missing course code or section.</div></div>';
    include 'footer.php';
    exit();
}

$c_code = $_GET['c_code'];
$c_section = $_GET['c_section'];
$lecturer_id = $_SESSION['u_id'];

// Fetch course and ensure it belongs to this lecturer
$sql = "SELECT c_code, c_name, c_section, c_credit, c_max_students, u_id_lecturer FROM tb_course WHERE c_code = ? AND c_section = ?";
$stmt = mysqli_stmt_init($con);
if (!mysqli_stmt_prepare($stmt, $sql)) {
    echo '<div class="container mt-5"><div class="alert alert-danger">Unable to load course details.</div></div>';
    include 'footer.php';
    exit();
}
mysqli_stmt_bind_param($stmt, "ss", $c_code, $c_section);
mysqli_stmt_execute($stmt);
$res = mysqli_stmt_get_result($stmt);
if ($course = mysqli_fetch_assoc($res)) {
    // Authorization: only the assigned lecturer can view
    if ($course['u_id_lecturer'] != $lecturer_id) {
        echo '<div class="container mt-5"><div class="alert alert-danger">Unauthorized to view this course.</div></div>';
        include 'footer.php';
        exit();
    }

    // Count current students
    $countSQL = "SELECT COUNT(*) AS total FROM tb_registration WHERE c_code = ? AND c_section = ?";
    $cstmt = mysqli_stmt_init($con);
    mysqli_stmt_prepare($cstmt, $countSQL);
    mysqli_stmt_bind_param($cstmt, "ss", $c_code, $c_section);
    mysqli_stmt_execute($cstmt);
    $cres = mysqli_stmt_get_result($cstmt);
    $crow = mysqli_fetch_assoc($cres);
    mysqli_stmt_close($cstmt);

    ?>
    <div class="container mt-5">
        <h2>Course Details: <?php echo htmlspecialchars($course['c_code'] . ' - ' . $course['c_name']); ?></h2>
        <table class="table table-bordered" style="max-width:800px;">
            <tr><th>Code</th><td><?php echo htmlspecialchars($course['c_code']); ?></td></tr>
            <tr><th>Name</th><td><?php echo htmlspecialchars($course['c_name']); ?></td></tr>
            <tr><th>Section</th><td><?php echo htmlspecialchars($course['c_section']); ?></td></tr>
            <tr><th>Credit</th><td><?php echo htmlspecialchars($course['c_credit']); ?></td></tr>
            <tr><th>Max Students</th><td><?php echo htmlspecialchars($course['c_max_students']); ?></td></tr>
            <tr><th>Current Registered</th><td><?php echo htmlspecialchars($crow['total']); ?></td></tr>
        </table>

        <a class="btn btn-primary" href="lecturer_view_students.php?c_code=<?php echo urlencode($c_code); ?>&c_section=<?php echo urlencode($c_section); ?>">View Students</a>
        <a class="btn btn-secondary" href="lecturer.php">Back to Dashboard</a>
    </div>
    <?php

} else {
    echo '<div class="container mt-5"><div class="alert alert-info">Course not found.</div></div>';
}

mysqli_stmt_close($stmt);
include 'footer.php';
?>
