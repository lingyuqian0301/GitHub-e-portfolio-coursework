
<?php
include 'header.php';
?>

<div class="container my-5">
    <div class="row align-items-center">
        <!-- Hero Section -->
        <div class="col-lg-6 mb-4 mb-lg-0">
            <h1 class="display-4 fw-bold text-primary mb-3">Student Management System</h1>
            <p class="lead text-muted mb-4">Efficient course registration and management for students, lecturers, and administrators.</p>
            <p class="mb-4">Welcome to the Student Management System (SMS). This platform streamlines academic operations including course registration, student enrollment management, and course administration.</p>
            <div class="d-grid gap-2 d-sm-flex">
                <a href="login.php" class="btn btn-primary btn-lg px-4 gap-3">Get Started</a>
                <a href="#features" class="btn btn-outline-secondary btn-lg px-4">Learn More</a>
            </div>
        </div>

    </div>
</div>

<!-- Features Section -->
<section id="features" class="py-5 bg-light">
    <div class="container">
        <h2 class="text-center mb-5">System Features</h2>
        <div class="row g-4">
            <!-- Students Feature -->
            <div class="col-md-4">
                <div class="card h-100 shadow-sm border-0">
                    <div class="card-body text-center">
                        <i class="fas fa-user-graduate mb-3" style="font-size: 40px; color: #0d6efd;"></i>
                        <h5 class="card-title">For Students</h5>
                        <ul class="list-unstyled text-start mt-3">
                            <li><i class="fas fa-check text-success me-2"></i>Register courses</li>
                            <li><i class="fas fa-check text-success me-2"></i>View course details</li>
                            <li><i class="fas fa-check text-success me-2"></i>Manage registrations</li>
                            <li><i class="fas fa-check text-success me-2"></i>Track enrollment</li>
                        </ul>
                    </div>
                </div>
            </div>
            <!-- Lecturers Feature -->
            <div class="col-md-4">
                <div class="card h-100 shadow-sm border-0">
                    <div class="card-body text-center">
                        <i class="fas fa-chalkboard-user mb-3" style="font-size: 40px; color: #198754;"></i>
                        <h5 class="card-title">For Lecturers</h5>
                        <ul class="list-unstyled text-start mt-3">
                            <li><i class="fas fa-check text-success me-2"></i>View assigned courses</li>
                            <li><i class="fas fa-check text-success me-2"></i>Access student lists</li>
                            <li><i class="fas fa-check text-success me-2"></i>Review course details</li>
                            <li><i class="fas fa-check text-success me-2"></i>Monitor enrollment</li>
                        </ul>
                    </div>
                </div>
            </div>
            <!-- Staff Feature -->
            <div class="col-md-4">
                <div class="card h-100 shadow-sm border-0">
                    <div class="card-body text-center">
                        <i class="fas fa-cogs mb-3" style="font-size: 40px; color: #fd7e14;"></i>
                        <h5 class="card-title">For Administrators</h5>
                        <ul class="list-unstyled text-start mt-3">
                            <li><i class="fas fa-check text-success me-2"></i>Manage courses</li>
                            <li><i class="fas fa-check text-success me-2"></i>Handle registrations</li>
                            <li><i class="fas fa-check text-success me-2"></i>View analytics</li>
                            <li><i class="fas fa-check text-success me-2"></i>System dashboard</li>
                        </ul>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- Key Features Section -->
<section class="py-5">
    <div class="container">
        <h2 class="text-center mb-5">Why Choose SMS?</h2>
        <div class="row align-items-center">
            <div class="col-md-6 mb-4 mb-md-0">
                <div class="row g-3">
                    <div class="col-12">
                        <div class="d-flex">
                            <div class="flex-shrink-0">
                                <div class="d-inline-flex align-items-center justify-content-center" style="width: 50px; height: 50px; background-color: #e7f1ff; border-radius: 10px;">
                                    <i class="fas fa-lock text-primary" style="font-size: 24px;"></i>
                                </div>
                            </div>
                            <div class="flex-grow-1 ms-3">
                                <h5>Secure & Reliable</h5>
                                <p class="text-muted">Password encryption and secure database with SQL injection prevention</p>
                            </div>
                        </div>
                    </div>
                    <div class="col-12">
                        <div class="d-flex">
                            <div class="flex-shrink-0">
                                <div class="d-inline-flex align-items-center justify-content-center" style="width: 50px; height: 50px; background-color: #e8f5e9; border-radius: 10px;">
                                    <i class="fas fa-envelope text-success" style="font-size: 24px;"></i>
                                </div>
                            </div>
                            <div class="flex-grow-1 ms-3">
                                <h5>Email Notifications</h5>
                                <p class="text-muted">Automated email confirmations and alerts via Gmail SMTP</p>
                            </div>
                        </div>
                    </div>
                    <div class="col-12">
                        <div class="d-flex">
                            <div class="flex-shrink-0">
                                <div class="d-inline-flex align-items-center justify-content-center" style="width: 50px; height: 50px; background-color: #fff3e0; border-radius: 10px;">
                                    <i class="fas fa-mobile-alt text-warning" style="font-size: 24px;"></i>
                                </div>
                            </div>
                            <div class="flex-grow-1 ms-3">
                                <h5>Responsive Design</h5>
                                <p class="text-muted">Works seamlessly on desktop, tablet, and mobile devices</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- CTA Section -->
<section class="py-5 bg-primary text-white">
    <div class="container text-center">
        <h2 class="mb-3">Ready to Get Started?</h2>
        <p class="lead mb-4">Access the Student Management System and streamline your academic operations today.</p>
        <a href="login.php" class="btn btn-light btn-lg px-4">Sign In Now</a>
    </div>
</section>

<?php include 'footer.php'; ?>