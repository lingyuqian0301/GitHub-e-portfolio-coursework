<?php
include 'dbconnect.php';
include 'headerstudent.php';
include 'csrf.php';

// Security Check
if (!isset($_SESSION['u_type']) || $_SESSION['u_type'] != '03') {
    header("Location: login.php");
    exit();
}

$current_sem = '2024/2025';

// Handle Search
$search = "";
if (isset($_GET['search'])) {
    $search = $_GET['search'];
}

// Get Registered Courses for this student
$my_courses = [];
$checkSql = "SELECT c_code FROM tb_registration WHERE u_id_student = ?";
$stmtReg = mysqli_stmt_init($con);
if (mysqli_stmt_prepare($stmtReg, $checkSql)) {
    mysqli_stmt_bind_param($stmtReg, "i", $_SESSION['u_id']);
    mysqli_stmt_execute($stmtReg);
    $resReg = mysqli_stmt_get_result($stmtReg);
    while ($rowReg = mysqli_fetch_assoc($resReg)) {
        $my_courses[] = $rowReg['c_code'];
    }
}

// Get unique courses
$sql = "SELECT c_code, c_name, c_credit, COUNT(*) as section_count 
        FROM tb_course";
if (!empty($search)) {
    $sql .= " WHERE c_code LIKE '%" . mysqli_real_escape_string($con, $search) . "%' 
              OR c_name LIKE '%" . mysqli_real_escape_string($con, $search) . "%'";
}
$sql .= " GROUP BY c_code, c_name, c_credit ORDER BY c_code ASC";

$result = mysqli_query($con, $sql);
?>

<div class="container mt-5">
    <h2> Course Registration</h2>
    <p class="lead">Browse courses and view available sections</p>

    <?php if (isset($_GET['msg'])): ?>
        <div class="alert alert-info alert-dismissible fade show" role="alert">
            <strong>Notice:</strong> <?php echo htmlspecialchars($_GET['msg']); ?>
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    <?php endif; ?>

    <div class="card shadow-sm mb-4">
        <div class="card-body">
            <form action="" method="GET" class="d-flex gap-2">
                <input type="text" name="search" class="form-control" 
                       placeholder="🔍 Search by Course Code or Name..." 
                       value="<?php echo htmlspecialchars($search); ?>">
                <button type="submit" class="btn btn-primary">Search</button>
                <a href="student_course_register.php" class="btn btn-secondary">Reset</a>
            </form>
        </div>
    </div>

    <div class="row row-cols-1 row-cols-md-2 row-cols-lg-3 g-4">
        <?php
        if (mysqli_num_rows($result) > 0) {
            while ($course = mysqli_fetch_assoc($result)) {
                $c_code = $course['c_code'];
                $c_name = $course['c_name'];
                $c_credit = $course['c_credit'];
                $section_count = $course['section_count'];
                $isRegistered = in_array($c_code, $my_courses);
                ?>
                <div class="col">
                    <div class="card h-100 shadow-sm <?php echo $isRegistered ? 'border-success' : ''; ?>">
                        <div class="card-body">
                            <h5 class="card-title">
                                <?php echo htmlspecialchars($c_code); ?>
                                <?php if ($isRegistered): ?>
                                    <span class="badge bg-success float-end">Enrolled</span>
                                <?php endif; ?>
                            </h5>
                            <h6 class="card-subtitle mb-2 text-muted"><?php echo htmlspecialchars($c_name); ?></h6>
                            <p class="card-text">
                                <strong>Credits:</strong> <?php echo $c_credit; ?><br>
                                <strong>Sections:</strong> <?php echo $section_count; ?>
                            </p>
                        </div>
                        <div class="card-footer bg-transparent">
                            <button class="btn btn-primary btn-sm w-100" 
                                    data-bs-toggle="modal" 
                                    data-bs-target="#modal<?php echo $c_code; ?>">
                                 View Sections & Register
                            </button>
                        </div>
                    </div>
                </div>

                <!-- Modal -->
                <div class="modal fade" id="modal<?php echo $c_code; ?>" tabindex="-1">
                    <div class="modal-dialog modal-lg">
                        <div class="modal-content">
                            <div class="modal-header">
                                <h5 class="modal-title">
                                    <?php echo htmlspecialchars($c_code); ?> - <?php echo htmlspecialchars($c_name); ?>
                                </h5>
                                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                            </div>
                            <div class="modal-body">
                                <p><strong>Credits:</strong> <?php echo $c_credit; ?></p>
                                
                                <?php if ($isRegistered): ?>
                                    <div class="alert alert-success">
                                        ✓ You are already enrolled in this course.
                                    </div>
                                <?php endif; ?>

                                <h6 class="mb-3">Available Sections:</h6>
                                <div class="table-responsive">
                                    <table class="table table-bordered table-hover">
                                        <thead class="table-light">
                                            <tr>
                                                <th>Section</th>
                                                <th>Lecturer</th>
                                                <th>Enrollment</th>
                                                <th>Status</th>
                                                <th>Action</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <?php
                                            $sectionSql = "SELECT c.c_section, c.c_max_students, c.u_id_lecturer, u.u_name as lecturer_name,
                                                           (SELECT COUNT(*) FROM tb_registration r 
                                                            WHERE r.c_code = c.c_code AND r.c_section = c.c_section 
                                                            AND r.reg_status = 'Approved') as enrolled
                                                           FROM tb_course c
                                                           LEFT JOIN tb_user u ON c.u_id_lecturer = u.u_id
                                                           WHERE c.c_code = '" . mysqli_real_escape_string($con, $c_code) . "'
                                                           ORDER BY c.c_section ASC";
                                            $sectionResult = mysqli_query($con, $sectionSql);
                                            
                                            while ($sec = mysqli_fetch_assoc($sectionResult)) {
                                                $section = $sec['c_section'];
                                                $max = $sec['c_max_students'];
                                                $enrolled = $sec['enrolled'];
                                                $lecturer = $sec['lecturer_name'] ?: 'TBA';
                                                $isFull = ($enrolled >= $max);
                                                $percent = $max > 0 ? round(($enrolled / $max) * 100) : 0;
                                                ?>
                                                <tr>
                                                    <td><strong>Sec <?php echo $section; ?></strong></td>
                                                    <td><?php echo htmlspecialchars($lecturer); ?></td>
                                                    <td>
                                                        <div class="progress" style="height: 20px;">
                                                            <div class="progress-bar <?php echo $isFull ? 'bg-danger' : ($percent >= 80 ? 'bg-warning' : 'bg-success'); ?>" 
                                                                 style="width: <?php echo $percent; ?>%">
                                                                <?php echo $enrolled; ?>/<?php echo $max; ?>
                                                            </div>
                                                        </div>
                                                    </td>
                                                    <td>
                                                        <?php if ($isFull): ?>
                                                            <span class="badge bg-danger">Full</span>
                                                        <?php elseif ($percent >= 80): ?>
                                                            <span class="badge bg-warning text-dark">Almost Full</span>
                                                        <?php else: ?>
                                                            <span class="badge bg-success">Available</span>
                                                        <?php endif; ?>
                                                    </td>
                                                    <td>
                                                        <?php if ($isRegistered): ?>
                                                            <button class="btn btn-sm btn-success" disabled>Enrolled</button>
                                                        <?php elseif ($isFull): ?>
                                                            <button class="btn btn-sm btn-secondary" disabled>Full</button>
                                                        <?php else: ?>
                                                            <form action="student_course_process.php" method="POST" style="display:inline;">
                                                                <?php echo csrf_input(); ?>
                                                                <input type="hidden" name="semester" value="<?php echo htmlspecialchars($current_sem); ?>">
                                                                <input type="hidden" name="action" value="register">
                                                                <input type="hidden" name="c_code" value="<?php echo $c_code; ?>">
                                                                <input type="hidden" name="c_section" value="<?php echo $section; ?>">
                                                                <button type="submit" class="btn btn-sm btn-primary"
                                                                        onclick="return confirm('Register for <?php echo $c_code; ?> Section <?php echo $section; ?>?');">
                                                                    Register
                                                                </button>
                                                            </form>
                                                        <?php endif; ?>
                                                    </td>
                                                </tr>
                                                <?php
                                            }
                                            ?>
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                            <div class="modal-footer">
                                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                            </div>
                        </div>
                    </div>
                </div>
                <?php
            }
        } else {
            echo '<div class="col-12"><div class="alert alert-info">No courses found matching your search.</div></div>';
        }
        ?>
    </div>
</div>

<style>
.card:hover {
    transform: translateY(-5px);
    transition: transform 0.3s ease;
}
.card-footer {
    border-top: none;
}
</style>

<?php include 'footer.php'; ?>
