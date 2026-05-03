<!DOCTYPE html>
<html lang="en-US">
<body>

<?php
// case-sensitive constant name
define("GREETING", "Welcome to da House!");
echo GREETING . "<br>";
?> 

<?php
// case-insensitive constant name
define("WELCOMMING", "Welcome to da Board!", true);
// echo welcomming . "<br>"; no longer support case-insensitive
echo WELCOMMING . "<br>";
?>

<?php
const MYCAR = "Myvi";

echo MYCAR . "<br>";
?>

<?php
define("cars", [
  "Tesla",
  "Audi",
  "Proton",
  "Honda"
]);
echo cars[1] . "<br>";
?>

<?php
define("GREETING1", "Welcome to UTM!");

function myTest() {
  echo GREETING1;
}
 
myTest();
?>

<p><a href="index.html">Back</a></p>

</body>
</html>