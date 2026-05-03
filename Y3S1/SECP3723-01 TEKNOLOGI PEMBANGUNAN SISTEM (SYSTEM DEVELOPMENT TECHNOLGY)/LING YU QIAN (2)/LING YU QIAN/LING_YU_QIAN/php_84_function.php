<?php declare(strict_types=1); // strict requirement ?>

<!DOCTYPE html>
<html lang="en-US">
<body>

<?php
function writeMsg() {
  echo "Hello to da club!<br>";
}

writeMsg();
?>

<?php
function familyName($fname) {
  echo "$fname _03.<br>";
}

familyName("Ling");
familyName("Abu");
familyName("Raju");
familyName("Kim Yong");
familyName("Joan");
?>

<?php
function familyName1($fname, $month) {
  echo "$fname _03. Born in $month <br>";
}

familyName1("Ling","May");
familyName1("Ali","June");
familyName1("Raju","Jan");
?>

<?php
function addNumbers(int $a, int $b) {
  return $a + $b . "<br>";
}
echo addNumbers(8, 8); 
// since strict is NOT enabled "8 days" is changed to int(8), and it will return 16
?>

<?php
function setHeight(int $minheight = 60) {
  echo "The height is : $minheight <br>";
}

setHeight(330);
setHeight();
setHeight(125);
setHeight(80);
?>

<?php
function sum(int $x, int $y) {
  $z = $x + $y;
  return $z;
}

echo "3 + 8 = " . sum(3,8) . "<br>";
echo "10 + 13 = " . sum(10,13) . "<br>";
echo "2 + 3 = " . sum(2,3) . "<br>";
?>

<?php
function addNumbers1(float $a, float $b) : float {
  return $a + $b;
}
echo addNumbers1(5.6, 2.9); 
?>

<?php
function addNumbers2(float $a, float $b) : int {
  return (int)($a + $b);
}
echo addNumbers2(1.4, 7.1); 
?>

<?php
function add_three(&$value) {
  $value += 3;
}

$num = 6;
add_three($num);
echo $num;
?>

<p><a href="index.html">Back</a></p>

</body>
</html>