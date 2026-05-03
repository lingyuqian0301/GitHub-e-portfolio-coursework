<?php
// Database Configuration - Choose Local or Hosting
$environment = 'hosting'; // Change to 'hosting' for production

if ($environment === 'local') {
    // LOCAL DEVELOPMENT (XAMPP)
    $servername = "localhost";
    $username   = "root";
    $password   = "";
    $dbname     = "db_sms";
} else {
    // INFINITYFREE HOSTING
    $servername = "sql105.infinityfree.com";
    $username   = "if0_40444175";
    $password   = "XNOgpemIah";
    $dbname     = "if0_40444175_db_sms";
}

// Create connection with error reporting
$con = mysqli_connect($servername, $username, $password, $dbname);

// Check connection
if (!$con) {
    die("Connection failed: " . mysqli_connect_error());
}

// Set character set to UTF-8
mysqli_set_charset($con, "utf8mb4");

// Enable error reporting (for development only)
mysqli_report(MYSQLI_REPORT_ERROR | MYSQLI_REPORT_STRICT);

?>







