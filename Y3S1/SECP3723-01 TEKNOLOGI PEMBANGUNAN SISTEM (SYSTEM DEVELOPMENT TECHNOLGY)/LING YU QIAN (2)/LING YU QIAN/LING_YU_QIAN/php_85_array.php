<!DOCTYPE html>
<html lang="en-US">
<body>

<?php
$cars = array("Audi", "BMW", "Perdua"); 
echo "I have " . $cars[0] . ", " . $cars[1] . " and " . $cars[2] . ". <br>";
?>

<?php
$cars = array("Audi", "BMW", "Perdua");
echo count($cars);
?>

<?php
$cars = array("Audi", "BMW", "Perdua"); 
echo "<br>I have " . $cars[0] . ", " . $cars[1] . " and " . $cars[2] . ".<br>";
?>

<?php
$cars = array("Audi", "BMW", "Perdua", "Mers");
$arrlength = count($cars);

for($x = 0; $x < $arrlength; $x++) {
  echo $cars[$x];
  echo "<br>";
}
?>

<?php
$age = array("Nic"=>"20", "Jolyn"=>"21", "Royin"=>"20");
echo "Nicholas is " . $age['Nic'] . " years old.<br>";
?>

<?php
$age = array("Nic"=>"20", "Jolyn"=>"21", "Royin"=>"20");

foreach($age as $x => $x_value) {
  echo "<br>Key=" . $x . ", Value=" . $x_value;
  echo "<br>";
}
?>

<?php
$cars = array (
  array("Volvo",28,17),
  array("BMW",10,13),
  array("Saab",6,1),
  array("Land Rover",15,10)
);
  
echo "<br>" . $cars[0][0].": In stock: ".$cars[0][1].", sold: ".$cars[0][2].".<br>";
echo $cars[1][0].": In stock: ".$cars[1][1].", sold: ".$cars[1][2].".<br>";
echo $cars[2][0].": In stock: ".$cars[2][1].", sold: ".$cars[2][2].".<br>";
echo $cars[3][0].": In stock: ".$cars[3][1].", sold: ".$cars[3][2].".<br>";
?>

<?php
$cars = array (
  array("Volvo",20,13),
  array("BMW",19,10),
  array("Saab",3,2),
  array("Land Rover",11,5)
);
    
for ($row = 0; $row < 4; $row++) {
  echo "<p><b>Row number $row</b></p>";
  echo "<ul>";
  for ($col = 0; $col < 3; $col++) {
    echo "<li>".$cars[$row][$col]."</li>";
  }
  echo "</ul>";
}
?>

<?php
$cars = array (
  array("Proton",22,18),
  array("BMW",15,13),
  array("Myvi",5,2),
  array("Tesla",17,15)
);
    
for ($row = 0; $row < 4; $row++) {
  echo "<p><b>Row number $row</b></p>";
  echo "<ul>";
  for ($col = 0; $col < 3; $col++) {
    echo "<li>".$cars[$row][$col]."</li>";
  }
  echo "</ul>";
}
?>

<?php
$cars = array("Mers", "BMW", "RR");
sort($cars);

$clength = count($cars);
for($x = 0; $x < $clength; $x++) {
  echo $cars[$x];
  echo "<br>";
}
?>

<?php
$numbers = array(1, 2, 3, 4, 5);
sort($numbers);

$arrlength = count($numbers);
for($x = 0; $x < $arrlength; $x++) {
  echo $numbers[$x];
  echo "<br>";
}
?>

<?php
$cars = array("BMW", "Mazda", "Kia");
rsort($cars);

$clength = count($cars);
for($x = 0; $x < $clength; $x++) {
  echo $cars[$x];
  echo "<br>";
}
?>

<?php
$numbers = array(4, 6, 8, 10, 12);
rsort($numbers);

$arrlength = count($numbers);
for($x = 0; $x < $arrlength; $x++) {
  echo $numbers[$x];
  echo "<br>";
}
?>

<?php
$age = array("Nick"=>"30", "Benjamin"=>"19", "Joey"=>"28");
asort($age);

foreach($age as $x => $x_value) {
  echo "Key=" . $x . ", Value=" . $x_value;
  echo "<br>";
}
?>

<?php
$age = array("Peter"=>"25", "Key"=>"37", "Chloe"=>"23");
ksort($age);

foreach($age as $x => $x_value) {
  echo "Key=" . $x . ", Value=" . $x_value;
  echo "<br>";
}
?>

<?php
$age = array("Pick"=>"35", "Benmark"=>"33", "Jolyn"=>"29");
arsort($age);

foreach($age as $x => $x_value) {
  echo "Key=" . $x . ", Value=" . $x_value;
  echo "<br>";
}
?>

<?php
$age = array("Peter"=>"26", "Lee"=>"14", "Dell"=>"33");
krsort($age);

foreach($age as $x => $x_value) {
  echo "Key=" . $x . ", Value=" . $x_value;
  echo "<br>";
}
?>

<p><a href="index.html">Back</a></p>

</body>
</html>