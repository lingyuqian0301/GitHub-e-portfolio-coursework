<!DOCTYPE html>
<html lang="en-US">
<body>

<?php
$x = 12;  
$y = 6;

echo $x / $y . "<br>";
?>

<?php
$x = 50;  
$x += 100;

echo $x . "<br>";
?>

<?php
$x = 5;  
$y = "5";

var_dump($x <> $y) . "<br>"; // returns false because values are equal
?>

<?php
$x = 8;  
echo ++$x;
?>

<?php
$x = 80;  
$y = 30;

if ($x == 80 xor $y == 50) {
    echo "Good morning!<br>";
}
?>

<?php
$txt1 = "Hello";
$txt2 = " guys!";
$txt1 .= $txt2;
echo $txt1 . "<br>";
?>

<?php
$x = array("a" => "red", "b" => "blue");  
$y = array("c" => "white", "d" => "yellow");  

var_dump($x === $y);
?>

<?php
   // if empty($user) = TRUE, set $status = "anonymous"
   echo $status = (empty($user)) ? "<br>anonymous" : "logged out";
   echo("<br>");

   $user = "Ling";
   // if empty($user) = FALSE, set $status = "logged out"
   echo $status = (empty($user)) ? "anonymous" : "logged out";
?>

<p><a href="index.html">Back</a></p>

</body>
</html>