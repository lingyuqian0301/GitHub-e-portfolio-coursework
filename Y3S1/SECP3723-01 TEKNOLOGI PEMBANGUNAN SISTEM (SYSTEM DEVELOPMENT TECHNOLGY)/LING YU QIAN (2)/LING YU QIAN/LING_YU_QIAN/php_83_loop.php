<!DOCTYPE html>
<html>
<body>

<?php  
$x = 0;
 
while($x <= 8) {
  echo "The number is: $x <br>";
  $x++;
} 
?>

<?php  
$x = 1;
 
while($x <= 50) {
  echo "The number is: $x <br>";
  $x+=10;
}
?>

<?php 
$x = 5;

do {
  echo "The number is: $x <br>";
  $x++;
} while ($x <= 10);
?>

<?php 
$x = 4;

do {
  echo "The number is: $x <br>";
  $x++;
} while ($x <= 3);
?>

<?php  
for ($x = 0; $x <= 6; $x++) {
  echo "The number is: $x <br>";
}
?>

<?php  
for ($x = 0; $x <= 100; $x+=20) {
  echo "The number is: $x <br>";
}
?>

<?php  
$colors = array("red", "green", "blue", "black", "white"); 

foreach ($colors as $value) {
  echo "$value <br>";
}
?>

<?php
$age = array("Peter"=>"30", "Ben"=>"27", "Joe"=>"13");

foreach($age as $x => $val) {
  echo "$x = $val<br>";
}
?>

<?php  
for ($x = 0; $x < 6; $x++) {
  if ($x == 3) {
    break;
  }
  echo "The number is: $x <br>";
}
?>

<?php  
for ($x = 0; $x < 6; $x++) {
  if ($x == 3) {
    continue;
  }
  echo "The number is: $x <br>";
}
?>

<?php  
$x = 2;
 
while($x < 10) {
  if ($x == 4) {
    break;
  }
  echo "The number is: $x <br>";
  $x++;
} 
?>

<?php  
$x = 2;
 
while($x < 6) {
  if ($x == 4) {
    $x++;
    continue;
  }
  echo "The number is: $x <br>";
  $x++;
} 
?>

<p><a href="index.html">Back</a></p>

</body>
</html>