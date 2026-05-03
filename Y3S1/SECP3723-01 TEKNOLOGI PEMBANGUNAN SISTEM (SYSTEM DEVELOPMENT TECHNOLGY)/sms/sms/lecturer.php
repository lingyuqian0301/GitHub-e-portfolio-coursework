<?php
include 'dbconnect.php';
include 'headerlec.php';
include 'pagination_helper.php';

// Security: Ensure User is Lecturer
if (!isset($_SESSION['u_type']) || $_SESSION['u_type'] != '02') {
    if (isset($_SESSION['u_type']) && $_SESSION['u_type'] == '01') {
        header("Location: staff.php");
    } else {
        header("Location: login.php");
    }
    exit();
}

$lecturer_id = $_SESSION['u_id'];

?>
<div class="container mt-5">
    <h2>Lecturer Dashboard</h2>

    <?php
    // Get total courses for pagination
    $count_sql = "SELECT COUNT(*) as total FROM tb_course WHERE u_id_lecturer = ?";
    $count_stmt = mysqli_stmt_init($con);
    if (mysqli_stmt_prepare($count_stmt, $count_sql)) {
        mysqli_stmt_bind_param($count_stmt, "s", $lecturer_id);
        mysqli_stmt_execute($count_stmt);
        $count_res = mysqli_stmt_get_result($count_stmt);
        $total_courses = mysqli_fetch_assoc($count_res)['total'];
        mysqli_stmt_close($count_stmt);
    } else {
        $total_courses = 0;
    }
    
    // Get pagination info
    $pagination = get_pagination_info($total_courses, 10);

    $sql = "SELECT c_code, c_name, c_section, c_credit, c_max_students FROM tb_course WHERE u_id_lecturer = ? ORDER BY c_code ASC LIMIT " . $pagination['records_per_page'] . " OFFSET " . $pagination['offset'];
    $stmt = mysqli_stmt_init($con);
    if (!mysqli_stmt_prepare($stmt, $sql)) {
        echo '<div class="alert alert-danger">Unable to load courses.</div>';
    } else {
        mysqli_stmt_bind_param($stmt, "s", $lecturer_id);
        mysqli_stmt_execute($stmt);
        $res = mysqli_stmt_get_result($stmt);

        if (mysqli_num_rows($res) === 0) {
            echo '<div class="alert alert-info">No courses assigned yet.</div>';
        } else {
            echo '<table class="table table-hover">';
            echo '<thead><tr><th>Code</th><th>Name</th><th>Section</th><th>Credits</th><th>Max Students</th><th>Actions</th></tr></thead>';
            echo '<tbody>';
            while ($row = mysqli_fetch_assoc($res)) {
                $code = htmlspecialchars($row['c_code']);
                $name = htmlspecialchars($row['c_name']);
                $section = htmlspecialchars($row['c_section']);
                $credit = htmlspecialchars($row['c_credit']);
                $max = htmlspecialchars($row['c_max_students']);

                $hrefStudents = 'lecturer_view_students.php?c_code=' . urlencode($row['c_code']) . '&c_section=' . urlencode($row['c_section']);
                $hrefDetails = 'lecturer_course_details.php?c_code=' . urlencode($row['c_code']) . '&c_section=' . urlencode($row['c_section']);

                echo '<tr>';
                echo "<td>{$code}</td>";
                echo "<td>{$name}</td>";
                echo "<td>{$section}</td>";
                echo "<td>{$credit}</td>";
                echo "<td>{$max}</td>";
                echo "<td>";
                echo "<a class='btn btn-info btn-sm me-1' href='{$hrefDetails}'>View Details</a>";
                echo "<a class='btn btn-primary btn-sm' href='{$hrefStudents}'>View Students</a>";
                echo "</td>";
                echo '</tr>';
            }
            echo '</tbody></table>';
            
            // Render pagination
            render_pagination($pagination);
        }
        mysqli_stmt_close($stmt);
    }
    ?>

</div>

<?php include 'footer.php'; ?>
