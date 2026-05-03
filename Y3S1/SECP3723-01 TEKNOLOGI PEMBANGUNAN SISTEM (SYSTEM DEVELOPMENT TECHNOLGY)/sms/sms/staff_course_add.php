<?php
include 'dbconnect.php';
include 'headerstaff.php';

// Security Check
if (!isset($_SESSION['u_type']) || $_SESSION['u_type'] != '01') {
    header("Location: login.php");
    exit();
}
?>

<div class="container mt-5">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <?php if (isset($_GET['msg'])): ?>
                <div class="alert alert-danger alert-dismissible fade show" role="alert">
                    <?php echo htmlspecialchars($_GET['msg']); ?>
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                </div>
            <?php endif; ?>
            
            <div class="card shadow-sm">
                <div class="card-header bg-success text-white">
                    <h4 class="mb-0">Add New Course</h4>
                </div>
                <div class="card-body">
                    <form action="staff_course_insert.php" method="POST">
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label for="c_code" class="form-label">Course Code</label>
                                <input type="text" class="form-control" name="c_code" placeholder="e.g. SECP3723" required>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label for="c_section" class="form-label">Section</label>
                                <input type="text" class="form-control" name="c_section" placeholder="e.g. A" required>
                            </div>
                        </div>

                        <div class="mb-3">
                            <label for="c_name" class="form-label">Course Name</label>
                            <input type="text" class="form-control" name="c_name" placeholder="e.g. System Development Technology" required>
                        </div>

                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label for="c_credit" class="form-label">Credit Hours</label>
                                <input type="number" class="form-control" name="c_credit" value="3" min="0" required>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label for="c_max_students" class="form-label">Max Students</label>
                                <input type="number" class="form-control" name="c_max_students" value="30" min="1" required>
                            </div>
                        </div>

                        <div class="mb-3">
                            <label for="u_id_lecturer" class="form-label">Assign Lecturer</label>
                            <select class="form-select" name="u_id_lecturer">
                                <option value="">-- Select Lecturer (Optional) --</option>
                                <?php
                                // Fetch all Lecturers (u_type = '02')
                                $sql = "SELECT u_id, u_name FROM tb_user WHERE u_type = '02' ORDER BY u_name";
                                $stmt = mysqli_stmt_init($con);
                                if (mysqli_stmt_prepare($stmt, $sql)) {
                                    mysqli_stmt_execute($stmt);
                                    $res = mysqli_stmt_get_result($stmt);
                                    while ($r = mysqli_fetch_assoc($res)) {
                                        echo "<option value='" . htmlspecialchars($r['u_id']) . "'>" . htmlspecialchars($r['u_name']) . "</option>";
                                    }
                                    mysqli_stmt_close($stmt);
                                }
                                ?>
                            </select>
                        </div>

                        <div class="d-flex justify-content-end">
                            <a href="staff.php" class="btn btn-secondary me-2">Cancel</a>
                            <button type="submit" class="btn btn-success">Save Course</button>
                        </div>

                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

<?php include 'footer.php'; ?>
