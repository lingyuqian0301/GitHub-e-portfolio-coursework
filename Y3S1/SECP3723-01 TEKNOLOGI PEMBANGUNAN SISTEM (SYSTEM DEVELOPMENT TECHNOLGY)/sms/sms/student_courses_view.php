<?php
include 'dbconnect.php';
include 'headerstudent.php';
include 'pagination_helper.php';
include 'csrf.php';

// Security Check
// Security Check
if (!isset($_SESSION['u_type']) || $_SESSION['u_type'] != '03') {
    header("Location: login.php");
    exit();
}

$u_id = $_SESSION['u_id'];
$msg = "";
if (isset($_GET['msg'])) {
    $msg = $_GET['msg'];
}
$current_sem = "2024/2025";

// Handle Drop
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action']) && $_POST['action'] === 'drop') {
    $token = $_POST['csrf_token'] ?? '';
    if (!csrf_verify($token)) {
        $msg = 'CSRF verification failed.';
    } else {
        $reg_id = intval($_POST['reg_id'] ?? 0);
        if ($reg_id > 0) {
            $delSql = "DELETE FROM tb_registration WHERE reg_id = ? AND u_id_student = ?";
            $stmt = mysqli_stmt_init($con);
            if (mysqli_stmt_prepare($stmt, $delSql)) {
                mysqli_stmt_bind_param($stmt, "ii", $reg_id, $u_id);
                if (mysqli_stmt_execute($stmt)) {
                    $msg = "Course dropped successfully.";
                } else {
                    $msg = "Error dropping course.";
                }
                mysqli_stmt_close($stmt);
            }
        }
    }
}

// Handle Change Section
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action']) && $_POST['action'] === 'change_section') {
    $token = $_POST['csrf_token'] ?? '';
    if (!csrf_verify($token)) {
        $msg = 'CSRF verification failed.';
    } else {
        $reg_id = intval($_POST['reg_id'] ?? 0);
        $new_section = intval($_POST['new_section'] ?? 0);

        if ($reg_id > 0 && $new_section > 0) {
            // Fetch current registration
            $regSql = "SELECT c_code, c_section FROM tb_registration WHERE reg_id = ? AND u_id_student = ?";
            $stmtReg = mysqli_stmt_init($con);
            if (mysqli_stmt_prepare($stmtReg, $regSql)) {
                mysqli_stmt_bind_param($stmtReg, "ii", $reg_id, $u_id);
                mysqli_stmt_execute($stmtReg);
                $resReg = mysqli_stmt_get_result($stmtReg);
                $regRow = mysqli_fetch_assoc($resReg);
                mysqli_stmt_close($stmtReg);

                if ($regRow) {
                    $c_code = $regRow['c_code'];
                    $current_section = (int)$regRow['c_section'];

                    if ($new_section === $current_section) {
                        $msg = "You are already in section $new_section for $c_code.";
                    } else {
                        // Validate target section exists and capacity
                        $courseSql = "SELECT c_max_students FROM tb_course WHERE c_code = ? AND c_section = ?";
                        $stmtCourse = mysqli_stmt_init($con);
                        if (mysqli_stmt_prepare($stmtCourse, $courseSql)) {
                            mysqli_stmt_bind_param($stmtCourse, "si", $c_code, $new_section);
                            mysqli_stmt_execute($stmtCourse);
                            $resCourse = mysqli_stmt_get_result($stmtCourse);
                            $courseRow = mysqli_fetch_assoc($resCourse);
                            mysqli_stmt_close($stmtCourse);

                            if (!$courseRow) {
                                $msg = "Selected section does not exist for $c_code.";
                            } else {
                                $max_students = (int)$courseRow['c_max_students'];

                                // Count current approved students in target section
                                $countSql = "SELECT COUNT(*) as total FROM tb_registration WHERE c_code = ? AND c_section = ? AND reg_status = 'Approved'";
                                $stmtCount = mysqli_stmt_init($con);
                                if (mysqli_stmt_prepare($stmtCount, $countSql)) {
                                    mysqli_stmt_bind_param($stmtCount, "si", $c_code, $new_section);
                                    mysqli_stmt_execute($stmtCount);
                                    $resCount = mysqli_stmt_get_result($stmtCount);
                                    $countRow = mysqli_fetch_assoc($resCount);
                                    mysqli_stmt_close($stmtCount);

                                    $current_total = (int)$countRow['total'];
                                    if ($current_total >= $max_students) {
                                        $msg = "The selected section ($new_section) for $c_code is full.";
                                    } else {
                                        // Update registration to new section
                                        $updSql = "UPDATE tb_registration SET c_section = ? WHERE reg_id = ? AND u_id_student = ?";
                                        $stmtUpd = mysqli_stmt_init($con);
                                        if (mysqli_stmt_prepare($stmtUpd, $updSql)) {
                                            mysqli_stmt_bind_param($stmtUpd, "iii", $new_section, $reg_id, $u_id);
                                            if (mysqli_stmt_execute($stmtUpd)) {
                                                $msg = "Section changed to $c_code (Section $new_section) successfully.";
                                            } else {
                                                $msg = "Error updating section. Please try again.";
                                            }
                                            mysqli_stmt_close($stmtUpd);
                                        }
                                    }
                                }
                            }
                        }
                    }
                } else {
                    $msg = "Registration not found.";
                }
            }
        } else {
            $msg = "Invalid section change request.";
        }
    }
}
?>

<div class="container mt-5">
    <h2>My Courses</h2>
    
    <?php if (!empty($msg)): ?>
        <?php $alertType = (strpos($msg, 'Error') !== false) ? 'alert-danger' : 'alert-success'; ?>
        <div class="alert <?php echo $alertType; ?> alert-dismissible fade show" role="alert">
            <?php echo htmlspecialchars($msg); ?>
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    <?php endif; ?>

    <!-- Tabs -->
    <ul class="nav nav-tabs mb-4" id="courseTabs" role="tablist">
        <li class="nav-item" role="presentation">
            <button class="nav-link active" id="current-tab" data-bs-toggle="tab" data-bs-target="#current" type="button" role="tab">Current Semester (<?php echo $current_sem; ?>)</button>
        </li>
        <li class="nav-item" role="presentation">
            <button class="nav-link" id="previous-tab" data-bs-toggle="tab" data-bs-target="#previous" type="button" role="tab">Previous Semesters</button>
        </li>
    </ul>

    <div class="tab-content">
        <!-- Current Semester Tab -->
        <div class="tab-pane fade show active" id="current" role="tabpanel">
            <?php render_course_table($con, $u_id, $current_sem, true); ?>
        </div>

        <!-- Previous Semester Tab -->
        <div class="tab-pane fade" id="previous" role="tabpanel">
            <?php render_course_table($con, $u_id, $current_sem, false); ?>
        </div>
    </div>
</div>

<?php 
include 'footer.php'; 

function render_course_table($con, $u_id, $current_sem, $is_current) {
    if ($is_current) {
        $sql = "SELECT r.reg_id, r.reg_status, r.reg_semester, c.c_code, c.c_name, c.c_section, c.c_credit 
                FROM tb_registration r
                JOIN tb_course c ON r.c_code = c.c_code AND r.c_section = c.c_section
                WHERE r.u_id_student = ? AND r.reg_semester = ?
                ORDER BY c.c_code ASC";
    } else {
        $sql = "SELECT r.reg_id, r.reg_status, r.reg_semester, c.c_code, c.c_name, c.c_section, c.c_credit 
                FROM tb_registration r
                JOIN tb_course c ON r.c_code = c.c_code AND r.c_section = c.c_section
                WHERE r.u_id_student = ? AND r.reg_semester != ?
                ORDER BY r.reg_semester DESC, c.c_code ASC";
    }

    $stmt = mysqli_stmt_init($con);
    if (mysqli_stmt_prepare($stmt, $sql)) {
        mysqli_stmt_bind_param($stmt, "is", $u_id, $current_sem);
        mysqli_stmt_execute($stmt);
        $result = mysqli_stmt_get_result($stmt);

        if (mysqli_num_rows($result) > 0) {
            echo '<div class="card shadow-sm"><div class="card-body"><div class="table-responsive">';
            echo '<table class="table table-striped table-hover align-middle">';
            echo '<thead class="table-dark"><tr>
                    <th>Semester</th>
                    <th>Code</th>
                    <th>Course Name</th>
                    <th>Section</th>
                    <th>Credit</th>
                    <th>Status</th>
                    '.($is_current ? '<th>Action</th>' : '').'
                  </tr></thead><tbody>';
            
            $total_credit = 0;
            while ($row = mysqli_fetch_assoc($result)) {
                $total_credit += $row['c_credit'];
                echo '<tr>';
                echo '<td>'.htmlspecialchars($row['reg_semester']).'</td>';
                echo '<td>'.htmlspecialchars($row['c_code']).'</td>';
                echo '<td>'.htmlspecialchars($row['c_name']).'</td>';
                echo '<td>'.htmlspecialchars($row['c_section']).'</td>';
                echo '<td>'.htmlspecialchars($row['c_credit']).'</td>';
                echo '<td><span class="badge bg-success">'.htmlspecialchars($row['reg_status']).'</span></td>';
                
                if ($is_current) {
                    // Fetch all sections for this course to allow switching
                    $sectionOptions = '';
                    $sectionSql = "SELECT c_section, c_max_students, (SELECT COUNT(*) FROM tb_registration r2 WHERE r2.c_code = c.c_code AND r2.c_section = c.c_section AND r2.reg_status = 'Approved') AS enrolled FROM tb_course c WHERE c.c_code = ? ORDER BY c_section";
                    $stmtSections = mysqli_stmt_init($con);
                    if (mysqli_stmt_prepare($stmtSections, $sectionSql)) {
                        mysqli_stmt_bind_param($stmtSections, "s", $row['c_code']);
                        mysqli_stmt_execute($stmtSections);
                        $resSections = mysqli_stmt_get_result($stmtSections);
                        while ($sRow = mysqli_fetch_assoc($resSections)) {
                            $sec = (int)$sRow['c_section'];
                            $enrolled = (int)$sRow['enrolled'];
                            $cap = (int)$sRow['c_max_students'];
                            $isCurrentSec = ($sec === (int)$row['c_section']);
                            $isFull = ($enrolled >= $cap);
                            $disabled = ($isFull || $isCurrentSec) ? 'disabled' : '';
                            $percent = round(($enrolled / $cap) * 100);
                            
                            if ($isCurrentSec) {
                                $label = "Section $sec ✓ (current)";
                            } elseif ($isFull) {
                                $label = "Section $sec (FULL - $enrolled/$cap)";
                            } else {
                                $label = "Section $sec ($enrolled/$cap - $percent% full)";
                            }
                            $sectionOptions .= '<option value="'.$sec.'" '.$disabled.'>'.htmlspecialchars($label).'</option>';
                        }
                        mysqli_stmt_close($stmtSections);
                    }

                    echo '<td>';
                    // Change section dropdown and action buttons
                    echo '<div style="display: flex; gap: 0.5rem; flex-wrap: wrap; align-items: center;">';
                    
                    // Change section form
                    echo '<form method="POST" action="" style="display: flex; gap: 0.5rem; align-items: center; flex-wrap: wrap; flex: 1; min-width: 280px;">';
                    echo csrf_input();
                    echo '<input type="hidden" name="action" value="change_section">';
                    echo '<input type="hidden" name="reg_id" value="'.htmlspecialchars($row['reg_id']).'">';
                    echo '<select name="new_section" class="form-select form-select-sm" style="flex: 1; min-width: 180px;">';
                    echo '<option value="">⇄ Switch section...</option>'; 
                    echo $sectionOptions; 
                    echo '</select>'; 
                    echo '<button type="submit" class="btn btn-warning btn-sm" style="white-space: nowrap; font-weight: 500;">
                            Switch
                          </button>';
                    echo '</form>';

                    // Drop course form - inline
                    echo '<form method="POST" action="" style="display: inline;">';
                    echo csrf_input();
                    echo '<input type="hidden" name="action" value="drop">';
                    echo '<input type="hidden" name="reg_id" value="'.htmlspecialchars($row['reg_id']).'">';
                    echo '<button type="submit" class="btn btn-danger btn-sm" 
                            onclick="return confirm(\'Drop this course?\');" style="white-space: nowrap; font-weight: 500;">
                            Drop
                          </button>';
                    echo '</form>';
                    
                    echo '</div>';
                    echo '</td>';
                }
                echo '</tr>';
            }
            echo "<tr class='table-light'><td colspan='4' class='text-end fw-bold'>Total Credits:</td><td colspan='".($is_current ? 3 : 2)."' class='fw-bold'>".htmlspecialchars($total_credit)."</td></tr>";
            echo '</tbody></table></div></div></div>';
        } else {
            echo '<div class="alert alert-info">No courses found for this category.</div>';
        }
    }
}
?>
