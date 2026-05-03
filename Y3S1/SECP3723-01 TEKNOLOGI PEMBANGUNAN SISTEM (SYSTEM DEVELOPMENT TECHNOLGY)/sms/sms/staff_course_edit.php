<?php
include 'dbconnect.php';
include 'headerstaff.php';

// Security Check
if (!isset($_SESSION['u_type']) || $_SESSION['u_type'] != '01') {
    header("Location: login.php");
    exit();
}

// Get ID from URL
if (!isset($_GET['code']) || !isset($_GET['sec'])) {
    header("Location: staff.php");
    exit();
}

$c_code = $_GET['code'];
$c_section = $_GET['sec'];

// Fetch Current Data
$sql = "SELECT * FROM tb_course WHERE c_code = ? AND c_section = ?";
$stmt = mysqli_stmt_init($con);
mysqli_stmt_prepare($stmt, $sql);
mysqli_stmt_bind_param($stmt, "si", $c_code, $c_section);
mysqli_stmt_execute($stmt);
$result = mysqli_stmt_get_result($stmt);
$row = mysqli_fetch_assoc($result);

if (!$row) {
    echo "Course not found.";
    exit();
}
?>

<div class="container mt-5">
    <?php if (isset($_GET['msg'])): ?>
        <div class="alert alert-danger alert-dismissible fade show" role="alert">
            <?php echo htmlspecialchars($_GET['msg']); ?>
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    <?php endif; ?>
    
    <div class="card shadow">
        <div class="card-header bg-warning text-dark">
            <h4>Edit Course Details</h4>
        </div>
        <div class="card-body">
            <form action="staff_course_modify_process.php" method="POST">
                
                <input type="hidden" name="old_code" value="<?php echo $row['c_code']; ?>">
                <input type="hidden" name="old_section" value="<?php echo $row['c_section']; ?>">

                <div class="row">
                    <div class="col-md-6 mb-3">
                        <label>Course Code</label>
                        <input type="text" name="c_code" class="form-control" value="<?php echo $row['c_code']; ?>" required>
                    </div>
                    <div class="col-md-6 mb-3">
                        <label>Section</label>
                        <input type="number" name="c_section" class="form-control" value="<?php echo $row['c_section']; ?>" required>
                    </div>
                </div>

                <div class="mb-3">
                    <label>Course Name</label>
                    <input type="text" name="c_name" class="form-control" value="<?php echo $row['c_name']; ?>" required>
                </div>

                <div class="row">
                    <div class="col-md-6 mb-3">
                        <label>Credit</label>
                        <input type="number" name="c_credit" class="form-control" value="<?php echo $row['c_credit']; ?>" required>
                    </div>
                    <div class="col-md-6 mb-3">
                        <label>Max Students</label>
                        <input type="number" name="c_max_students" class="form-control" value="<?php echo $row['c_max_students']; ?>" required>
                    </div>
                </div>

                <div class="mb-3">
                    <label>Assign Lecturer</label>
                    <select name="u_id_lecturer" class="form-select">
                        <option value="">-- Unassigned --</option>
                        <?php
                        // Fetch Lecturers
                        $l_sql = "SELECT u_id, u_name FROM tb_user WHERE u_type = '02'";
                        $l_result = mysqli_query($con, $l_sql);
                        while ($l_row = mysqli_fetch_assoc($l_result)) {
                            $selected = ($row['u_id_lecturer'] == $l_row['u_id']) ? "selected" : "";
                            echo "<option value='".$l_row['u_id']."' $selected>".$l_row['u_name']."</option>";
                        }
                        ?>
                    </select>
                </div>

                <button type="submit" class="btn btn-warning">Update Course</button>
                <a href="staff.php" class="btn btn-secondary">Cancel</a>
            </form>
        </div>
    </div>
</div>

<?php include 'footer.php'; ?>
