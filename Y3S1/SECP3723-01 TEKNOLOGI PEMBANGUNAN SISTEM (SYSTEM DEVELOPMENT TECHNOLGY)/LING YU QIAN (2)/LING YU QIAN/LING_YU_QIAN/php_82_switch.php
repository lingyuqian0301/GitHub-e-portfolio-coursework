<!DOCTYPE html>
<html lang="en-US">
<body>

<?php
$favcolor = "blue";

switch ($favcolor) {
  case "red":
    echo "Your favorite color is red!";
    break;
  case "blue":
    echo "Your favorite color is blue!";
    break;
  case "yellow":
    echo "Your favorite color is yellow!";
    break;
  default:
    echo "Your favorite color is neither red, blue, nor yellow!";
}
?>

<p><a href="index.html">Back</a></p>
 
</body>
</html>