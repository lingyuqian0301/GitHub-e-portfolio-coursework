<!DOCTYPE html>
<html lang="en-US">
<body>

<?php
$a = date("H");

if ($a < "20") {
  echo "Morning, Have a good day!<br>";
}
?>

<?php
$t = date("X");

if ($t < "13") {
  echo "Morning, Have a good day!<br>";
} else {
  echo "Bye, Have a good night!<br>";
}
?>

<?php
$b = date("H");
echo "<p>The hour (of the server) is " . $b; 
echo ", and will give the following message:</p>";

if ($b < "9") {
  echo "Hi, Have a good morning!";
} elseif ($b < "20") {
  echo "Morning, Have a good day!";
} else {
  echo "Bye, Have a good night!";
}
?>

<p><a href="index.html">Back</a></p>
 
</body>
</html>