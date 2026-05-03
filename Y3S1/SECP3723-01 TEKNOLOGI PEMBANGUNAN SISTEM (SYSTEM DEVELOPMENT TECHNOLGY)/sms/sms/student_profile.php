<?php
include 'dbconnect.php';
include 'headerstudent.php';

// Check if user is student
if (!isset($_SESSION['u_id']) || $_SESSION['u_type'] !== '03') {
    header('Location: login.php');
    exit();
}

$u_id = $_SESSION['u_id'];

// Fetch current data
$sql = "SELECT * FROM tb_user WHERE u_id = ?";
$stmt = mysqli_stmt_init($con);
if (mysqli_stmt_prepare($stmt, $sql)) {
    mysqli_stmt_bind_param($stmt, "i", $u_id);
    mysqli_stmt_execute($stmt);
    $result = mysqli_stmt_get_result($stmt);
    $user = mysqli_fetch_assoc($result);
} else {
    echo "Error loading profile.";
    exit();
}
?>

<div class="container mt-5 mb-5">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card shadow">
                <div class="card-header bg-primary text-white">
                    <h4>Edit Profile</h4>
                </div>
                <div class="card-body">
                    <?php if (isset($_GET['msg'])): ?>
                        <div class="alert alert-info"><?php echo htmlspecialchars($_GET['msg']); ?></div>
                    <?php endif; ?>

                    <form action="student_profile_process.php" method="POST">
                        <div class="mb-3">
                            <label class="form-label">Full Name</label>
                            <input type="text" name="u_name" class="form-control" value="<?php echo htmlspecialchars($user['u_name']); ?>" required>
                        </div>
                        
                        <div class="mb-3">
                            <label class="form-label">Email</label>
                            <input type="email" name="u_email" class="form-control" value="<?php echo htmlspecialchars($user['u_email']); ?>" required>
                        </div>

                        <div class="mb-3">
                            <label class="form-label">Phone Number</label>
                            <input type="text" name="u_phone" class="form-control" placeholder="e.g., 0123456789" value="<?php echo htmlspecialchars($user['u_phone']); ?>" required>
                        </div>

                        <div class="mb-3">
                            <label class="form-label">Change Password (leave blank to keep current)</label>
                            <input type="password" name="new_password" class="form-control" placeholder="New Password">
                        </div>

                         <div class="mb-3">
                            <label class="form-label">Confirm New Password</label>
                            <input type="password" name="confirm_password" class="form-control" placeholder="Confirm New Password">
                        </div>

                        <div class="d-grid gap-2">
                            <button type="submit" class="btn btn-primary">Update Profile</button>
                            <a href="student.php" class="btn btn-secondary">Cancel</a>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

<?php include 'footer.php'; ?>
