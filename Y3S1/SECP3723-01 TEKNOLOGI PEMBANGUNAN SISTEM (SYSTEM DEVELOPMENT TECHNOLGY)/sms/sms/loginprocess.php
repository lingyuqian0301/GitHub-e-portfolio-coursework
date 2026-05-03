<?php
ini_set('display_errors', 1);
error_reporting(E_ALL);
session_start();
include('dbconnect.php');

// 1. Get data (Do not use $_POST directly in SQL queries)
$femail = $_POST['femail'];
$fpwd = $_POST['fpwd'];

// 2. PREPARED STATEMENT (Fixes SQL Injection )
// We check the Email first. We check the password later using PHP.
$sql = "SELECT u_id, u_pwd, u_name, u_type FROM tb_user WHERE u_email = ?";
// Initialize statement
$stmt = mysqli_stmt_init($con);

if (!mysqli_stmt_prepare($stmt, $sql)) {
    echo "SQL Statement Failed";
} else { 
    // Bind parameters to the placeholder (?)
    mysqli_stmt_bind_param($stmt, "s", $femail);
    
    // Run parameters inside database
    mysqli_stmt_execute($stmt);
    
    // Get result
    $result = mysqli_stmt_get_result($stmt);
    
    // Check if ID exists
    if ($row = mysqli_fetch_assoc($result)) {
        
        // 3. PASSWORD VERIFICATION (Fixes Password Hashing )
        // checks if the typed password matches the Hash in the database
        $pwdCheck = password_verify($fpwd, $row['u_pwd']);
        
        if ($pwdCheck == false) {
            // Wrong password
            header('Location: login.php?error=wrongpwd');
            exit();
        } elseif ($pwdCheck == true) {
            // Login Success
            
            // Security: Prevent session fixation
            session_regenerate_id(true); 

            // Set Session Variables
            $_SESSION['u_id'] = $row['u_id'];
            $_SESSION['u_type'] = $row['u_type']; // Store type for permission checks later

            // 4. REDIRECTION LOGIC (Fixes Login Redirection [cite: 37])
            if ($row['u_type'] == '01') {
                header('Location: staff.php');
            } elseif ($row['u_type'] == '02') {
                header('Location: lecturer.php');
            } elseif ($row['u_type'] == '03') {
                header('Location: student.php');
            } else {
                // Unknown user type
                header('Location: login.php?error=nousertype');
            }
            exit();
        }
    } else {
        // ID does not exist
        header('Location: login.php?error=nouser');
        exit();
    }
}
?>








<!-- <?php
session_start();
//Connect to database
include ('dbconnect.php');
//Retrieve data from registration form
$fid=$_POST['fid'];
$fpwd=$_POST['fpwd'];

//retrieve data from database sql-RETRIEVE
$sql="SELECT * FROM tb_user
      WHERE u_id='$fid' AND u_pwd='$fpwd'";
//Execute SQL
$result=mysqli_query($con,$sql);
$row=mysqli_fetch_array($result);

//Redirect to the corresponding page -Simple rule-based AI solution
$count=mysqli_num_rows($result);
if($count==1)
{
      $_SESSION['u_id'] = session_id();
      $_SESSION['uid'] =$fid;

    if($row['u_type']=='01')
    {
      

        header('Location: staff.php');
    }
    if($row['u_type']=='02')
    {
         header('Location: lecturer.php');
    }
    if($row['u_type']=='03')
    {
         header('Location: student.php');
    }
}
else{
    header('Location: login.php');
}


?> -->