<!DOCTYPE HTML>  
<html lang="en-US">
<head>
</head>
<body>  

<?php
// define variables and set to empty values
$name = $email = $gender = $feedback = $matric = "";

if ($_SERVER["REQUEST_METHOD"] == "POST") {
  $name = test_input($_POST["name"]);
  $email = test_input($_POST["email"]);
  $matric = test_input($_POST["matric"]);
  $feedback = test_input($_POST["feedback"]);
  $gender = test_input($_POST["gender"]);
}

function test_input($data) {
  $data = trim($data);
  $data = stripslashes($data);
  $data = htmlspecialchars($data);
  return $data;
}
?>

<h2>PHP Form Validation Example</h2>
<form method="post" action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]);?>">  
  Name: <input type="text" name="name">
  <br><br>
  Matric No: <input type="text" name="matric">
  <br><br>
  E-mail: <input type="email" name="email">
  <br><br>
  Feedback: <textarea name="feedback" rows="5" cols="35"></textarea>
  <br><br>
  Gender:
  <input type="radio" name="gender" value="female">Female
  <input type="radio" name="gender" value="male">Male
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