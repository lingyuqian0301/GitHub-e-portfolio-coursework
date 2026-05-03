<!DOCTYPE html>
<html lang="en-US">
<body>

<?php
$i = 8; // global scope
 
function myTest() {
  // using i inside this function will generate an error
  echo "<p>Variable i inside function is: $i</p>";
} 
myTest();

echo "<p>Variable i outside function is: $i</p>";
?>

<?php
function myTest1() {
  $x = 5; // local scope
  echo "<br><p>Variable x inside function is: $x</p>";
} 
myTest1();

// using x outside the function will generate an error
echo "<p>Variable x outside function is: $x</p><br>";
?>

<?php
$x = 5;
$y = 10;

function myTest2() {
  global $x, $y;
  $y = $y - $x;
} 

myTest2();  // run function
echo $y; // output the new value for variable $y
?>

<?php
$x = 5;
$y = 3;

function myTest3() {
  echo "<br><br>";
  $GLOBALS['y'] = $GLOBALS['x'] * $GLOBALS['y'];
} 

myTest3();
echo $y . "<br><br>";
?>

<?php
function myTest4() {
  static $j = 0;
  echo $j;
  $j++;
}

myTest4();
echo "<br>";
myTest4();
echo "<br>";
myTest4();
?>

<p><a href="index.html">Back</a></p>

</body>
</html>