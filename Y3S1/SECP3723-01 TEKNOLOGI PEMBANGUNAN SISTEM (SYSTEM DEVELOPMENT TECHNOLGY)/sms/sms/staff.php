<?php
include 'dbconnect.php';
include 'headerstaff.php';
include 'pagination_helper.php';

// Ensure user is Staff
if (!isset($_SESSION['u_type']) || $_SESSION['u_type'] !== '01') {
   header('Location: login.php');
   exit();
}

// Get Staff Info
$u_id = $_SESSION['u_id'];
$staff_name = "";
$sql_staff = "SELECT u_name FROM tb_user WHERE u_id = ?";
$stmt_staff = mysqli_stmt_init($con);
if (mysqli_stmt_prepare($stmt_staff, $sql_staff)) {
    mysqli_stmt_bind_param($stmt_staff, "i", $u_id);
    mysqli_stmt_execute($stmt_staff);
    $result = mysqli_stmt_get_result($stmt_staff);
    if ($row = mysqli_fetch_assoc($result)) {
        $staff_name = $row['u_name'];
    }
}

// Get Dashboard Statistics
$total_courses = mysqli_fetch_assoc(mysqli_query($con, "SELECT COUNT(*) as c FROM tb_course"))['c'];
$total_students = mysqli_fetch_assoc(mysqli_query($con, "SELECT COUNT(*) as c FROM tb_user WHERE u_type='03'"))['c'];
$total_lecturers = mysqli_fetch_assoc(mysqli_query($con, "SELECT COUNT(*) as c FROM tb_user WHERE u_type='02'"))['c'];
$total_registrations = mysqli_fetch_assoc(mysqli_query($con, "SELECT COUNT(*) as c FROM tb_registration"))['c'];
$pending_registrations = mysqli_fetch_assoc(mysqli_query($con, "SELECT COUNT(*) as c FROM tb_registration WHERE reg_status='Pending'"))['c'] ?? 0;

// ---------------------------
// FILTERS
// ---------------------------
$filter_code = isset($_GET['filter_code']) ? $_GET['filter_code'] : '';
$filter_lecturer = isset($_GET['filter_lecturer']) ? $_GET['filter_lecturer'] : '';
$filter_status = isset($_GET['filter_status']) ? $_GET['filter_status'] : '';

?>
<div class="container mt-5 mb-5">
   <!-- Welcome Banner -->
   <div class="row mb-4">
      <div class="col-md-12">
         <div class="p-5 bg-light rounded-3 shadow-sm border">
            <h1 class="display-5 fw-bold">Welcome, <?php echo htmlspecialchars($staff_name); ?>!</h1>
            <p class="col-md-8 fs-4">Staff Management Dashboard</p>
            <p class="text-muted">Manage courses, registrations, and system administration</p>
         </div>
      </div>
   </div>

   <!-- Quick Stats Cards -->
   <div class="row mb-4">
      <div class="col-md-3">
         <div class="card shadow-sm h-100">
            <div class="card-body text-center">
               <h3 class="text-primary fw-bold"><?php echo $total_courses; ?></h3>
               <p class="card-text text-muted">Total Courses</p>
            </div>
         </div>
      </div>
      <div class="col-md-3">
         <div class="card shadow-sm h-100">
            <div class="card-body text-center">
               <h3 class="text-info fw-bold"><?php echo $total_registrations; ?></h3>
               <p class="card-text text-muted">Total Registrations</p>
            </div>
         </div>
      </div>
      <div class="col-md-3">
         <div class="card shadow-sm h-100">
            <div class="card-body text-center">
               <h3 class="text-warning fw-bold"><?php echo $pending_registrations; ?></h3>
               <p class="card-text text-muted">Pending Approvals</p>
            </div>
         </div>
      </div>
      <div class="col-md-3">
         <div class="card shadow-sm h-100">
            <div class="card-body text-center">
               <h3 class="text-success fw-bold"><?php echo $total_students; ?></h3>
               <p class="card-text text-muted">Total Students</p>
            </div>
         </div>
      </div>
   </div>

   <!-- Quick Actions -->
   <div class="row mb-4">
      <div class="col-md-4">
         <div class="card shadow-sm h-100">
            <div class="card-body text-center">
               <h3 class="card-title"><i class="bi bi-plus-circle"></i> Add Course</h3>
               <p class="card-text">Create a new course or section for the next semester.</p>
               <a href="staff_course_add.php" class="btn btn-primary btn-lg">Add Course</a>
            </div>
         </div>
      </div>

      <div class="col-md-4">
         <div class="card shadow-sm h-100">
            <div class="card-body text-center">
               <h3 class="card-title"><i class="bi bi-pencil-square"></i> Manage Courses</h3>
               <p class="card-text">Edit, delete, or view details of existing courses.</p>
               <a href="#coursesSection" class="btn btn-info btn-lg">View Courses</a>
            </div>
         </div>
      </div>

      <div class="col-md-4">
         <div class="card shadow-sm h-100">
            <div class="card-body text-center">
               <h3 class="card-title"><i class="bi bi-clipboard-check"></i> Registrations</h3>
               <p class="card-text">Approve or reject pending student registrations.</p>
               <a href="staff_registrations_view.php" class="btn btn-warning btn-lg">View Registrations</a>
            </div>
         </div>
      </div>
   </div>

   <?php if (isset($_GET['msg'])): ?>
      <div class="alert alert-success alert-dismissible fade show" role="alert">
         <?php echo htmlspecialchars($_GET['msg']); ?>
         <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
      </div>
   <?php endif; ?>

   <!-- Courses Section -->
   <div id="coursesSection" class="card shadow-sm">
      <div class="card-header bg-light">
         <div class="d-flex justify-content-between align-items-center">
            <h5 class="mb-0">Course Management</h5>
            <button class="btn btn-outline-secondary btn-sm" type="button" data-bs-toggle="collapse" data-bs-target="#filterPanel">
               <i class="bi bi-funnel"></i> Filters
            </button>
         </div>
      </div>
      <div class="card-body">

         <!-- Filter Panel -->
         <div class="collapse mb-4" id="filterPanel">
            <div class="card border-0 shadow-sm" style="background-color: #f8f9fa;">
               <div class="card-body">
                  <h6 class="card-title fw-bold mb-3" style="color: #495057;">
                     <i class="bi bi-funnel"></i> Filter Courses
                  </h6>
                  <form method="GET" action="staff.php" class="row g-3">
                     <div class="col-md-3">
                        <label class="form-label fw-semibold small text-muted">Course Code</label>
                        <input type="text" name="filter_code" class="form-control form-control-sm" placeholder="e.g., SECJ" value="<?php echo htmlspecialchars($filter_code); ?>">
                     </div>
                     <div class="col-md-3">
                        <label class="form-label fw-semibold small text-muted">Lecturer</label>
                        <select name="filter_lecturer" class="form-select form-select-sm">
                           <option value="">All Lecturers</option>
                           <?php
                           $lec_res = mysqli_query($con, "SELECT DISTINCT u.u_id, u.u_name FROM tb_user u JOIN tb_course c ON u.u_id = c.u_id_lecturer WHERE u.u_type='02' ORDER BY u.u_name");
                           while ($lec = mysqli_fetch_assoc($lec_res)) {
                              $selected = ($filter_lecturer == $lec['u_id']) ? 'selected' : '';
                              echo "<option value='{$lec['u_id']}' $selected>" . htmlspecialchars($lec['u_name']) . "</option>";
                           }
                           ?>
                        </select>
                     </div>
                     <div class="col-md-3">
                        <label class="form-label fw-semibold small text-muted">Capacity Status</label>
                        <select name="filter_status" class="form-select form-select-sm">
                           <option value="">All Statuses</option>
                           <option value="full" <?php echo ($filter_status === 'full') ? 'selected' : ''; ?>>Full (100%)</option>
                           <option value="near_full" <?php echo ($filter_status === 'near_full') ? 'selected' : ''; ?>>Near Full (80-99%)</option>
                           <option value="available" <?php echo ($filter_status === 'available') ? 'selected' : ''; ?>>Available (&lt;80%)</option>
                        </select>
                     </div>
                     <div class="col-md-3 d-flex align-items-end gap-2">
                        <button type="submit" class="btn btn-primary btn-sm flex-grow-1">
                           <i class="bi bi-search"></i> Apply
                        </button>
                        <a href="staff.php" class="btn btn-outline-secondary btn-sm">
                           <i class="bi bi-x-circle"></i> Clear
                        </a>
                     </div>
                  </form>
               </div>
            </div>
         </div>

         <div class="table-responsive">
            <table class="table table-hover align-middle mb-0">
               <thead class="table-light">
                  <tr>
                     <th>Course Code</th>
                     <th>Section</th>
                     <th>Course Name</th>
                     <th>Credit</th>
                     <th>Lecturer</th>
                     <th>Enrollment</th>
                     <th class="text-center">Actions</th>
                  </tr>
               </thead>
               <tbody>
               <?php
               // Build dynamic query based on filters - COUNT first
               $count_sql = "SELECT COUNT(*) as total FROM tb_course c WHERE 1=1";
               
               // Apply filters to count query
               if (!empty($filter_code)) {
                  $count_sql .= " AND c.c_code LIKE '%" . mysqli_real_escape_string($con, $filter_code) . "%'";
               }
               if (!empty($filter_lecturer)) {
                  $count_sql .= " AND c.u_id_lecturer = " . intval($filter_lecturer);
               }
               
               // Handle capacity filter in separate step
               $count_res = mysqli_query($con, $count_sql);
               $total_courses_filtered = mysqli_fetch_assoc($count_res)['total'];
               
               // Get pagination info
               $pagination = get_pagination_info($total_courses_filtered, 10);
               
               // Build data query with pagination
               $sql = "SELECT c.c_code, c.c_section, c.c_name, c.c_credit, c.c_max_students, c.u_id_lecturer, 
                              u.u_name as lecturer_name,
                              (SELECT COUNT(*) FROM tb_registration r WHERE r.c_code = c.c_code AND r.c_section = c.c_section) as curr_count
                     FROM tb_course c
                     LEFT JOIN tb_user u ON c.u_id_lecturer = u.u_id
                     WHERE 1=1";
               
               // Apply filters
               if (!empty($filter_code)) {
                  $sql .= " AND c.c_code LIKE '%" . mysqli_real_escape_string($con, $filter_code) . "%'";
               }
               if (!empty($filter_lecturer)) {
                  $sql .= " AND c.u_id_lecturer = " . intval($filter_lecturer);
               }
               
               $sql .= " ORDER BY c.c_code ASC, c.c_section ASC";
               $sql .= " LIMIT " . $pagination['records_per_page'] . " OFFSET " . $pagination['offset'];

               $res = mysqli_query($con, $sql);
               if (!$res) {
                  echo "<tr><td colspan='7' class='text-danger text-center p-4'>Unable to load courses.</td></tr>";
               } else {
                  $filtered_results = [];
                  while ($row = mysqli_fetch_assoc($res)) {
                     $percent = ($row['c_max_students'] > 0) ? ($row['curr_count'] / $row['c_max_students']) * 100 : 0;
                     
                     // Apply capacity filter
                     if (!empty($filter_status)) {
                        if ($filter_status === 'full' && $percent < 100) continue;
                        if ($filter_status === 'near_full' && ($percent < 80 || $percent >= 100)) continue;
                        if ($filter_status === 'available' && $percent >= 80) continue;
                     }
                     
                     $filtered_results[] = $row;
                  }
                  
                  if (count($filtered_results) > 0) {
                     foreach ($filtered_results as $row) {
                        $percent = ($row['c_max_students'] > 0) ? ($row['curr_count'] / $row['c_max_students']) * 100 : 0;
                        ?>
                        <tr style="border-bottom: 1px solid #e9ecef; transition: background-color 0.2s;">
                           <td style="padding: 1.2rem; font-weight: 600; color: #078282;"><?php echo htmlspecialchars($row['c_code']); ?></td>
                           <td style="padding: 1.2rem;"><?php echo htmlspecialchars($row['c_section']); ?></td>
                           <td style="padding: 1.2rem;"><?php echo htmlspecialchars($row['c_name']); ?></td>
                           <td style="padding: 1.2rem;"><span class="badge bg-light text-dark" style="font-weight: 600;"><?php echo htmlspecialchars($row['c_credit']); ?></span></td>
                           <td style="padding: 1.2rem;"><?php echo $row['lecturer_name'] ? htmlspecialchars($row['lecturer_name']) : '<span class="text-muted">Not Assigned</span>'; ?></td>
                           <td style="padding: 1.2rem;">
                              <div class="d-flex align-items-center gap-2">
                                 <div style="flex: 1;">
                                    <div class="progress" style="height: 6px; border-radius: 3px;">
                                       <?php 
                                          $percent = ($row['c_max_students'] > 0) ? ($row['curr_count'] / $row['c_max_students']) * 100 : 0;
                                          $color = ($percent >= 100) ? 'bg-danger' : (($percent >= 80) ? 'bg-warning' : 'bg-success');
                                       ?>
                                       <div class="progress-bar <?php echo $color; ?>" style="width: <?php echo min($percent, 100); ?>%;"></div>
                                    </div>
                                 </div>
                                 <span style="font-weight: 600; color: #495057; min-width: 55px; text-align: right;"><?php echo $row['curr_count'] . "/" . $row['c_max_students']; ?></span>
                              </div>
                           </td>
                           <td style="padding: 1.2rem; text-align: center;">
                              <a href="staff_course_edit.php?code=<?php echo urlencode($row['c_code']); ?>&amp;sec=<?php echo urlencode($row['c_section']); ?>" class="btn btn-sm btn-outline-primary" style="border-radius: 6px; font-weight: 600; font-size: 0.85rem;">
                                 <i class="bi bi-pencil"></i> Edit
                              </a>
                              <a href="staff_course_delete_process.php?code=<?php echo urlencode($row['c_code']); ?>&amp;sec=<?php echo urlencode($row['c_section']); ?>" class="btn btn-sm btn-outline-danger" onclick="return confirm('Are you sure you want to delete this course? All registrations will also be deleted.');" style="border-radius: 6px; font-weight: 600; font-size: 0.85rem;">
                                 <i class="bi bi-trash"></i> Delete
                              </a>
                           </td>
                        </tr>
                        <?php
                     }
                  } else {
                     echo "<tr><td colspan='7' class='text-center p-4' style='color: #6c757d;'>No courses match your filters. Try adjusting the criteria.</td></tr>";
                  }
               }
               ?>
               </tbody>
            </table>
         </div>

         <?php render_pagination($pagination); ?>
      </div>
   </div>
</div>

<?php include 'footer.php'; ?>
