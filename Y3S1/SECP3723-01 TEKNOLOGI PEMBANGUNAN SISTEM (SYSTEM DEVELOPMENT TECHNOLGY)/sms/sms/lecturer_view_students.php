<?php
/**
 * Lecturer View Students in Course
 * Requirement: Lecturer can view student list for each course
 */

session_start();
include('dbconnect.php');
include('headerlec.php');
include('pagination_helper.php');

// Security: Ensure User is Lecturer (u_type = 02)
if (!isset($_SESSION['u_id']) || $_SESSION['u_type'] != '02') {
    header("Location: login.php");
    exit();
}

// Get course parameters
if (isset($_GET['c_code']) && isset($_GET['c_section'])) {
    $c_code = htmlspecialchars($_GET['c_code']);
    $c_section = htmlspecialchars($_GET['c_section']);
} elseif (isset($_GET['code']) && isset($_GET['sec'])) {
    $c_code = htmlspecialchars($_GET['code']);
    $c_section = htmlspecialchars($_GET['sec']);
} else {
    header("Location: lecturer.php");
    exit();
}

// Verify lecturer owns this course
$verifySql = "SELECT c.c_code, c.c_name, c.c_credit 
              FROM tb_course c 
              WHERE c.c_code = ? AND c.c_section = ? AND c.u_id_lecturer = ?";
$verifyStmt = mysqli_stmt_init($con);
if (!mysqli_stmt_prepare($verifyStmt, $verifySql)) {
    die("Database error");
}
mysqli_stmt_bind_param($verifyStmt, "ssi", $c_code, $c_section, $_SESSION['u_id']);
mysqli_stmt_execute($verifyStmt);
$verifyRes = mysqli_stmt_get_result($verifyStmt);

if ($verifyRes->num_rows === 0) {
    header("Location: lecturer.php");
    exit();
}

$course = mysqli_fetch_assoc($verifyRes);
mysqli_stmt_close($verifyStmt);

// Get search/filter parameters
$search = htmlspecialchars($_GET['search'] ?? '');
$filter_status = htmlspecialchars($_GET['status'] ?? '');

// Build query with optional search
$whereClause = "WHERE r.c_code = ? AND r.c_section = ?";
$params = [$c_code, $c_section];
$types = "ss";

if (!empty($search)) {
    $whereClause .= " AND (u.u_name LIKE ? OR u.u_email LIKE ?)";
    $searchTerm = "%$search%";
    $params = array_merge($params, [$searchTerm, $searchTerm]);
    $types .= "ss";
}

if (!empty($filter_status)) {
    $whereClause .= " AND r.reg_status = ?";
    $params[] = $filter_status;
    $types .= "s";
}

// Get total count for pagination
$countSql = "SELECT COUNT(*) as total FROM tb_registration r 
             JOIN tb_user u ON r.u_id_student = u.u_id 
             $whereClause";
$countStmt = mysqli_stmt_init($con);
mysqli_stmt_prepare($countStmt, $countSql);
mysqli_stmt_bind_param($countStmt, $types, ...$params);
mysqli_stmt_execute($countStmt);
$totalRows = mysqli_fetch_assoc(mysqli_stmt_get_result($countStmt))['total'];
mysqli_stmt_close($countStmt);

// Pagination
$perPage = 15;
$currentPage = intval($_GET['page'] ?? 1);
$offset = ($currentPage - 1) * $perPage;
$totalPages = ceil($totalRows / $perPage);

// Get students with pagination
$sql = "SELECT r.reg_id, u.u_id, u.u_name, u.u_email, u.u_phone, u.u_phoneperator, 
         u.u_programme, r.reg_status
     FROM tb_registration r
     JOIN tb_user u ON r.u_id_student = u.u_id
     $whereClause
     ORDER BY u.u_name ASC
     LIMIT ? OFFSET ?";

$stmt = mysqli_stmt_init($con);
mysqli_stmt_prepare($stmt, $sql);
$params[] = $perPage;
$params[] = $offset;
$types .= "ii";
mysqli_stmt_bind_param($stmt, $types, ...$params);
mysqli_stmt_execute($stmt);
$result = mysqli_stmt_get_result($stmt);
?>

<div class="container-fluid mt-4">
    <!-- Breadcrumb Navigation -->
    <nav aria-label="breadcrumb">
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="lecturer.php">Dashboard</a></li>
            <li class="breadcrumb-item active" aria-current="page">
                <?php echo htmlspecialchars($c_code) . " - Section " . htmlspecialchars($c_section); ?>
            </li>
        </ol>
    </nav>

    <!-- Course Info Card -->
    <div class="card shadow-sm mb-4">
        <div class="card-header bg-primary text-white">
            <div class="row align-items-center">
                <div class="col">
                    <h4 class="mb-0"><?php echo htmlspecialchars($course['c_name']); ?></h4>
                    <small><?php echo htmlspecialchars($c_code) . " | Section " . htmlspecialchars($c_section); ?></small>
                </div>
                <div class="col-auto">
                    <span class="badge bg-light text-primary">Credits: <?php echo htmlspecialchars($course['c_credit']); ?></span>
                    <span class="badge bg-light text-primary">Total: <?php echo $totalRows; ?> students</span>
                </div>
            </div>
        </div>

        <!-- Search & Filter Section -->
        <div class="card-body bg-light border-bottom">
            <form method="GET" class="row g-3">
                <input type="hidden" name="c_code" value="<?php echo htmlspecialchars($c_code); ?>">
                <input type="hidden" name="c_section" value="<?php echo htmlspecialchars($c_section); ?>">
                
                <div class="col-md-6">
                    <input type="text" class="form-control" name="search" 
                           placeholder="Search by name or email..." 
                           value="<?php echo htmlspecialchars($search); ?>">
                </div>
                <div class="col-md-3">
                    <select class="form-select" name="status">
                        <option value="">All Status</option>
                        <option value="Approved" <?php echo $filter_status === 'Approved' ? 'selected' : ''; ?>>Approved</option>
                        <option value="Pending" <?php echo $filter_status === 'Pending' ? 'selected' : ''; ?>>Pending</option>
                    </select>
                </div>
                <div class="col-md-3">
                    <button type="submit" class="btn btn-primary w-100">Filter</button>
                </div>
            </form>
        </div>

        <!-- Students Table -->
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-hover align-middle">
                    <thead class="table-light">
                        <tr>
                            <th>#</th>
                            <th>Student Name</th>
                            <th>Email</th>
                            <th>Phone</th>
                            <th>Programme</th>
                            <th>Status</th>
                            <th>Registered</th>
                            <th class="text-center">Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php
                        if ($result->num_rows > 0) {
                            $count = $offset + 1;
                            while ($row = mysqli_fetch_assoc($result)) {
                                $statusBadge = $row['reg_status'] === 'Approved' 
                                    ? '<span class="badge bg-success">Approved</span>' 
                                    : '<span class="badge bg-warning">Pending</span>';
                        ?>
                                <tr>
                                    <td><?php echo $count++; ?></td>
                                    <td>
                                        <strong><?php echo htmlspecialchars($row['u_name']); ?></strong><br>
                                        <small class="text-muted">ID: <?php echo htmlspecialchars($row['u_id']); ?></small>
                                    </td>
                                    <td><?php echo htmlspecialchars($row['u_email']); ?></td>
                                    <td>
                                        <?php 
                                        $op = isset($row['u_phoneperator']) ? (string)$row['u_phoneperator'] : '';
                                        if (!empty($op) && substr($op, 0, 1) !== '0') { 
                                            $op = '0' . $op; 
                                        }
                                        $fullPhone = trim($op) . htmlspecialchars($row['u_phone']);
                                        echo htmlspecialchars($fullPhone);
                                        ?>
                                    </td>
                                    <td><span class="badge bg-info"><?php echo htmlspecialchars($row['u_programme']); ?></span></td>
                                    <td><?php echo $statusBadge; ?></td>
                                    <td>-</td>
                                    <td class="text-center">
                                        <a href="lecturer_view_student_detail.php?u_id=<?php echo urlencode($row['u_id']); ?>&c_code=<?php echo urlencode($c_code); ?>&c_section=<?php echo urlencode($c_section); ?>" 
                                           class="btn btn-sm btn-primary">View Details</a>
                                    </td>
                                </tr>
                        <?php
                            }
                        } else {
                            echo "<tr><td colspan='8' class='text-center py-5 text-muted'>
                                    <h5>No Students Found</h5>
                                    <p>There are no students registered for this course section.</p>
                                  </td></tr>";
                        }
                        mysqli_stmt_close($stmt);
                        ?>
                    </tbody>
                </table>
            </div>

            <!-- Pagination -->
            <?php if ($totalPages > 1): ?>
            <nav aria-label="Page navigation">
                <ul class="pagination justify-content-center">
                    <?php if ($currentPage > 1): ?>
                        <li class="page-item">
                            <a class="page-link" href="?c_code=<?php echo urlencode($c_code); ?>&c_section=<?php echo urlencode($c_section); ?>&page=1&search=<?php echo urlencode($search); ?>&status=<?php echo urlencode($filter_status); ?>">First</a>
                        </li>
                        <li class="page-item">
                            <a class="page-link" href="?c_code=<?php echo urlencode($c_code); ?>&c_section=<?php echo urlencode($c_section); ?>&page=<?php echo $currentPage - 1; ?>&search=<?php echo urlencode($search); ?>&status=<?php echo urlencode($filter_status); ?>">Previous</a>
                        </li>
                    <?php endif; ?>

                    <?php for ($i = max(1, $currentPage - 2); $i <= min($totalPages, $currentPage + 2); $i++): ?>
                        <li class="page-item <?php echo $i === $currentPage ? 'active' : ''; ?>">
                            <a class="page-link" href="?c_code=<?php echo urlencode($c_code); ?>&c_section=<?php echo urlencode($c_section); ?>&page=<?php echo $i; ?>&search=<?php echo urlencode($search); ?>&status=<?php echo urlencode($filter_status); ?>"><?php echo $i; ?></a>
                        </li>
                    <?php endfor; ?>

                    <?php if ($currentPage < $totalPages): ?>
                        <li class="page-item">
                            <a class="page-link" href="?c_code=<?php echo urlencode($c_code); ?>&c_section=<?php echo urlencode($c_section); ?>&page=<?php echo $currentPage + 1; ?>&search=<?php echo urlencode($search); ?>&status=<?php echo urlencode($filter_status); ?>">Next</a>
                        </li>
                        <li class="page-item">
                            <a class="page-link" href="?c_code=<?php echo urlencode($c_code); ?>&c_section=<?php echo urlencode($c_section); ?>&page=<?php echo $totalPages; ?>&search=<?php echo urlencode($search); ?>&status=<?php echo urlencode($filter_status); ?>">Last</a>
                        </li>
                    <?php endif; ?>
                </ul>
            </nav>
            <?php endif; ?>

            <!-- Action Buttons -->
            <div class="mt-4">
                <a href="lecturer.php" class="btn btn-secondary">Back to Dashboard</a>
                <button class="btn btn-primary" onclick="window.print()">Print List</button>
            </div>
        </div>
    </div>
</div>

<?php include('footer.php'); ?>


<div class="container mt-4">
    <!-- Breadcrumb for better navigation -->
    <nav aria-label="breadcrumb">
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="lecturer.php">Dashboard</a></li>
            <li class="breadcrumb-item active" aria-current="page"><?php echo htmlspecialchars($c_code); ?></li>
        </ol>
    </nav>

    <div class="card shadow">
        <div class="card-header bg-primary text-white d-flex justify-content-between align-items-center">
            <div>
                <h4 class="mb-0">Student List</h4>
                <small>Section <?php echo htmlspecialchars($c_section); ?></small>
            </div>
            <div>
                 <span class="badge bg-light text-primary"><?php echo htmlspecialchars($displayTitle); ?></span>
            </div>
        </div>
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-hover align-middle">
                    <thead class="table-light">
                        <tr>
                            <th>#</th>
                            <th>Student Details</th>
                            <th>Contact Info</th>
                            <th>Programme</th>
                            <th class="text-center">Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php
                        // Get total students for pagination
                        $count_sql = "SELECT COUNT(*) as total FROM tb_registration WHERE c_code = ? AND c_section = ?";
                        $count_stmt = mysqli_stmt_init($con);
                        if (mysqli_stmt_prepare($count_stmt, $count_sql)) {
                            mysqli_stmt_bind_param($count_stmt, "ss", $c_code, $c_section);
                            mysqli_stmt_execute($count_stmt);
                            $count_res = mysqli_stmt_get_result($count_stmt);
                            $total_students = mysqli_fetch_assoc($count_res)['total'];
                            mysqli_stmt_close($count_stmt);
                        } else {
                            $total_students = 0;
                        }
                        
                        // Get pagination info
                        $pagination = get_pagination_info($total_students, 15);
                        
                        // JOIN Query to get Student Details including phone operator
                        $sql = "SELECT u.u_id, u.u_name, u.u_email, u.u_phone, u.u_phoneperator, u.u_programme 
                            FROM tb_registration r
                            JOIN tb_user u ON r.u_id_student = u.u_id
                            WHERE r.c_code = ? AND r.c_section = ?
                            ORDER BY u.u_name ASC
                            LIMIT " . $pagination['records_per_page'] . " OFFSET " . $pagination['offset'];

                        $stmt = mysqli_stmt_init($con);
                        if (!mysqli_stmt_prepare($stmt, $sql)) {
                            echo "<tr><td colspan='5' class='text-danger text-center py-3'>Database Error</td></tr>";
                        } else {
                            mysqli_stmt_bind_param($stmt, "ss", $c_code, $c_section);
                            mysqli_stmt_execute($stmt);
                            $result = mysqli_stmt_get_result($stmt);

                            if (mysqli_num_rows($result) > 0) {
                                $count = $pagination['offset'] + 1;
                                while ($row = mysqli_fetch_assoc($result)) {
                                    ?>
                                    <tr>
                                        <td><?php echo $count++; ?></td>
                                        <td>
                                            <div class="fw-bold"><?php echo htmlspecialchars($row['u_name']); ?></div>
                                            <small class="text-muted">ID: <?php echo htmlspecialchars($row['u_id']); ?></small>
                                        </td>
                                        <td>
                                            <div class="small">Email: <?php echo htmlspecialchars($row['u_email']); ?></div>
                                            <div class="small text-muted">Phone: <?php 
                                                $op = isset($row['u_phoneperator']) ? (string)$row['u_phoneperator'] : '';
                                                if ($op !== '' && substr($op, 0, 1) !== '0') { $op = '0' . $op; }
                                                $fullPhone = trim($op) . (isset($row['u_phone']) ? (string)$row['u_phone'] : '');
                                                echo htmlspecialchars($fullPhone);
                                            ?></div>
                                        </td>
                                        <td><span class="badge bg-secondary"><?php echo htmlspecialchars($row['u_programme']); ?></span></td>
                                        <td class="text-center">
                                            <a href="lecturer_view_student_detail.php?u_id=<?php echo urlencode($row['u_id']); ?>&amp;c_code=<?php echo urlencode($c_code); ?>&amp;c_section=<?php echo urlencode($c_section); ?>" 
                                               class="btn btn-primary btn-sm rounded-pill px-3">
                                               View Details
                                            </a>
                                        </td>
                                    </tr>
                                    <?php
                                }
                            } else {
                                echo "<tr><td colspan='5' class='text-center py-5 text-muted'>
                                        <h4>No students found</h4>
                                        <p>There are no students registered for this section yet.</p>
                                      </td></tr>";
                            }
                        }
                        mysqli_stmt_close($stmt);
                        ?>
                    </tbody>
                </table>
            </div>
            
            <?php render_pagination($pagination); ?>
            
            <div class="mt-3">
                <a href="lecturer.php" class="btn btn-secondary">Back to Dashboard</a>
            </div>
        </div>
    </div>
</div>

<?php include 'footer.php'; ?>
