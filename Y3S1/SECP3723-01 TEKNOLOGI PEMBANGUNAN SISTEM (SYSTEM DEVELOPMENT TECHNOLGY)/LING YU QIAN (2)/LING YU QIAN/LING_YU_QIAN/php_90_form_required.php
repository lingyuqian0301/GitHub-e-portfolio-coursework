<!DOCTYPE HTML>  
<html lang="en-US">
<head>
  <style>
    .error {color: #FF0000;}
  </style>
</head>
<body>

<?php
// define variables and set to empty values
$nameErr = $emailErr = $genderErr = $feedbackErr = $matricErr = "";
$name = $email = $gender = $feedback = $matric = "";

if ($_SERVER["REQUEST_METHOD"] == "POST") {
  if (empty($_POST["name"])) {
    $nameErr = "Name is required";
  } else {
    $name = test_input($_POST["name"]);
  }
  
  if (empty($_POST["email"])) {
    $emailErr = "Email is required";
  } else {
    $email = test_input($_POST["email"]);
  }
    
  if (empty($_POST["matric"])) {
    $matricErr = "Matric no is required";
  } else {
    $matric = test_input($_POST["matric"]);
  }

  if (empty($_POST["feedback"])) {
    $feedback = "";
  } else {
    $feedback = test_input($_POST["feedback"]);
  }

  if (empty($_POST["gender"])) {
    $genderErr = "Gender is required";
  } else {
    $gender = test_input($_POST["gender"]);
  }
}

function test_input($data) {
  $data = trim($data);
  $data = stripslashes($data);
  $data = htmlspecialchars($data);
  return $data;
}
?>

<h2>PHP Form Validation Example</h2>
<p><span class="error">* required field</span></p>
<form method="post" action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]);?>">  
  Name: <input type="text" name="name">
  <span class="error">* <?php echo $nameErr;?></span>
  <br><br>
  Matric No: <input type="text" name="matric">
  <span class="error">* <?php echo $matricErr;?></span>
  <br><br>
  E-mail: <input type="email" name="email">
  <span class="error">* <?php echo $emailErr;?></span>
  <br><br>
  Feedback: <textarea name="feedback" rows="5" cols="35"></textarea>
  <br><br>
  Gender:
  <input type="radio" name="gender" value="female">Female
  <input type="radio" name="gender" value="male">Male
  <span class="error">* <?php echo $genderErr;?></span>
  <br><br>
  <input type="submit" name="submit" value="Submit feedback">  
</form>

<?php
echo "<h2>Your Input:</h2>";
echo $name;
echo "<br>";
echo $matric;
echo "<br>";
echo $email;
echo "<br>";
echo $feedback;
echo "<br>";
echo $gender;
?>

<p><a href="index.html">Back</a></p>

</body>
</html>