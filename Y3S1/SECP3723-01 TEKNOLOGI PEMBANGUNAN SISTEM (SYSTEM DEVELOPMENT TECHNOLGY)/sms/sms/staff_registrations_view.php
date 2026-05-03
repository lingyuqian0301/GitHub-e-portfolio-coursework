<?php
include 'dbconnect.php';
include 'headerstaff.php';
include 'pagination_helper.php';
include 'email_helper.php';

// Security
if (!isset($_SESSION['u_type']) || $_SESSION['u_type'] != '01') {
    header('Location: login.php');
    exit();
}

$msg = "";
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action'])) {
    if ($_POST['action'] === 'approve' || $_POST['action'] === 'delete') {
         $reg_id = intval($_POST['reg_id']);
          
         // Get student info for email
         $info_sql = "SELECT r.c_code, r.c_section, r.reg_status, u.u_email, u.u_name 
                      FROM tb_registration r 
                      JOIN tb_user u ON r.u_id_student = u.u_id 
                      WHERE r.reg_id = ?";
         $info_stmt = mysqli_stmt_init($con);
         $student_email = "";
         $student_name = "";
         $c_code = "";
         $c_section = "";
         
         if (mysqli_stmt_prepare($info_stmt, $info_sql)) {
             mysqli_stmt_bind_param($info_stmt, "i", $reg_id);
             mysqli_stmt_execute($info_stmt);
             $info_res = mysqli_stmt_get_result($info_stmt);
             if ($info_row = mysqli_fetch_assoc($info_res)) {
                 $student_email = $info_row['u_email'];
                 $student_name = $info_row['u_name'];
                 $c_code = $info_row['c_code'];
                 $c_section = $info_row['c_section'];
             }
             mysqli_stmt_close($info_stmt);
         }
         
         if ($_POST['action'] === 'approve') {
             $sql = "UPDATE tb_registration SET reg_status = 'Approved' WHERE reg_id = ?";
             $action_name = "approved";
             $email_subject = "Registration Approved: $c_code";
             $email_msg = "Dear $student_name,\n\nYour registration for $c_code (Section $c_section) has been approved.\n\nRegards,\nSMS Admin";
         } else {
             $sql = "DELETE FROM tb_registration WHERE reg_id = ?";
             $action_name = "cancelled";
             $email_subject = "Registration Cancelled: $c_code";
             $email_msg = "Dear $student_name,\n\nYour registration for $c_code (Section $c_section) has been cancelled.\n\nRegards,\nSMS Admin";
         }

         $stmt = mysqli_stmt_init($con);
         if (mysqli_stmt_prepare($stmt, $sql)) {
             mysqli_stmt_bind_param($stmt, "i", $reg_id);
             if (mysqli_stmt_execute($stmt)) {
                 $msg = "Registration $action_name successfully.";
                 // Send email notification to student
                 if (!empty($student_email)) {
                     send_notification_email($student_email, $email_subject, $email_msg);
                 }
             } else {
                $msg = "Error processing request.";
             }
         }
    }
}
?>

<div class="container mt-5">
    <h2>Manage Student Registrations</h2>
    <?php if ($msg): ?>
        <div class="alert alert-success"><?php echo htmlspecialchars($msg); ?></div>
    <?php endif; ?>

    <div class="card shadow-sm">
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-hover">
                    <thead class="table-dark">
                        <tr>
                            <th>Reg ID</th>
                            <th>Student Name</th>
                            <th>Course</th>
                            <th>Section</th>
                            <th>Status</th>
                            <th>Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php
                        $sql = "SELECT r.reg_id, r.reg_status, r.c_code, r.c_section, u.u_name 
                                FROM tb_registration r
                                JOIN tb_user u ON r.u_id_student = u.u_id
                                ORDER BY r.reg_id DESC";
                        // Get total registrations for pagination
                        $count_sql = "SELECT COUNT(*) as total FROM tb_registration";
                        $count_res = mysqli_query($con, $count_sql);
                        $total_regs = mysqli_fetch_assoc($count_res)['total'];
                        
                        // Get pagination info
                        $pagination = get_pagination_info($total_regs, 15);
                        
                        $sql .= " LIMIT " . $pagination['records_per_page'] . " OFFSET " . $pagination['offset'];
                        
                        $result = mysqli_query($con, $sql);
                        if (mysqli_num_rows($result) > 0) {
                            while ($row = mysqli_fetch_assoc($result)) {
                                ?>
                                <tr>
                                    <td><?php echo $row['reg_id']; ?></td>
                                    <td><?php echo htmlspecialchars($row['u_name']); ?></td>
                                    <td><?php echo htmlspecialchars($row['c_code']); ?></td>
                                    <td><?php echo htmlspecialchars($row['c_section']); ?></td>
                                    <td><?php echo htmlspecialchars($row['reg_status']); ?></td>
                                    <td>
                                        <a href="staff_registration_edit.php?id=<?php echo $row['reg_id']; ?>" class="btn btn-warning btn-sm">Amend</a>
                                        <form method="POST" class="d-inline" onsubmit="return confirm('Confirm cancellation?');">
                                            <input type="hidden" name="reg_id" value="<?php echo $row['reg_id']; ?>">
                                            <input type="hidden" name="action" value="delete">
                                            <button type="submit" class="btn btn-danger btn-sm">Cancel</button>
                                        </form>
                                    </td>
                                </tr>
                                <?php
                            }
                        } else {
                            echo "<tr><td colspan='6'>No registrations found.</td></tr>";
                        }
                        ?>
                    </tbody>
                </table>
            </div>
            
            <!-- Pagination -->
            <?php render_pagination($pagination); ?>
        </div>
    </div>
</div>

<?php include 'footer.php'; ?>
