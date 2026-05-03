<!DOCTYPE html>
<html lang="en-US">
<body>

<?php
// Check if the type of a variable is integer   
$x = 145;
var_dump(is_int($x));

echo "<br>";

// Check again... 
$x = 23.891;
var_dump(is_int($x));
?>

<?php
// Check if the type of a variable is float
echo "<br>";
$x = 12.77;
var_dump(is_float($x));
?>

<?php
// Check if a numeric value is finite or infinite
echo "<br>";
$x = 2.9e55678;
var_dump($x);
?>

<?php
// Invalid calculation will return a NaN value
echo "<br>";
$x = acos(10);
var_dump($x);
?>

<?php
// Check if the variable is numeric
echo "<br>"; 
$x = 6789;
var_dump(is_numeric($x));

echo "<br>";

$x = "6789";
var_dump(is_numeric($x));

echo "<br>";

$x = "10.12" + 10;
var_dump(is_numeric($x));

echo "<br>";

$x = "Bye";
var_dump(is_numeric($x));
?>

<?php
// Cast float to int
echo "<br>";
$x = 23456.7891;
$int_cast = (int)$x;
echo $int_cast;
  
echo "<br>";

// Cast string to int
$x = "23456.7891";
$int_cast = (int)$x;
echo $int_cast;
?>

<p><a href="index.html">Back</a></p>

</body>
</html>