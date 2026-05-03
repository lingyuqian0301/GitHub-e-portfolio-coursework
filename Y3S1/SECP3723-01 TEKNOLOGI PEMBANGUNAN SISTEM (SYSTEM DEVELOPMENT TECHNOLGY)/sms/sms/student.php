<?php
include 'dbconnect.php';
include 'headerstudent.php';

// Security Check
if (!isset($_SESSION['u_type']) || $_SESSION['u_type'] != '03') {
    header("Location: login.php");
    exit();
}

// Get User Data
$u_id = $_SESSION['u_id'];
$u_name = "";
$u_programme = "";

// Fetch simple details to welcome them
$sql = "SELECT u.u_name, p.p_name 
        FROM tb_user u 
        JOIN tb_programme p ON u.u_programme = p.p_id 
        WHERE u.u_id = ?";
$stmt = mysqli_stmt_init($con);
if (mysqli_stmt_prepare($stmt, $sql)) {
    mysqli_stmt_bind_param($stmt, "i", $u_id);
    mysqli_stmt_execute($stmt);
    $result = mysqli_stmt_get_result($stmt);
    if ($row = mysqli_fetch_assoc($result)) {
        $u_name = $row['u_name'];
        $u_programme = $row['p_name'];
    }
}
?>

<div class="container mt-5">
   <div class="row">
      <div class="col-md-12 mb-4">
         <div class="p-5 bg-light rounded-3 shadow-sm border">
            <h1 class="display-5 fw-bold">Welcome, <?php echo htmlspecialchars($u_name); ?>!</h1>
            <p class="col-md-8 fs-4">Student Dashboard</p>
            <p class="text-muted"><?php echo htmlspecialchars($u_programme); ?></p>
         </div>
      </div>
   </div>

   <div class="row">
      <div class="col-md-6">
         <div class="card shadow-sm h-100">
            <div class="card-body text-center">
               <h3 class="card-title"><i class="bi bi-journal-plus"></i> Register Courses</h3>
               <p class="card-text">Search for new subjects and enroll in classes for the upcoming semester.</p>
               <a href="student_course_register.php" class="btn btn-primary btn-lg">Go to Registration</a>
            </div>
         </div>
      </div>

      <div class="col-md-6">
         <div class="card shadow-sm h-100">
            <div class="card-body text-center">
               <h3 class="card-title"><i class="bi bi-list-check"></i> My Registration Slip</h3>
               <p class="card-text">View your approved courses, check sections, or drop a course.</p>
               <a href="student_courses_view.php" class="btn btn-success btn-lg">View My Courses</a>
            </div>
         </div>
      </div>
   </div>
</div>

<?php include 'footer.php'; ?>