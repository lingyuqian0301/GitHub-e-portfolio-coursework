<!DOCTYPE html>
<html lang="en-US">
<body>

<?php 
$x = "Hello bye!";
$y = 'Hi bay!';

echo "<br>"; 
echo $x;
echo "<br>"; 
echo $y;
?>

<?php  
echo "<br>"; 
$z = 4569;
var_dump($z);
?>

<?php
echo "<br>";
$x = 20.6712;
var_dump($x);
?>

<?php  
$cars = array("Tesla","Mers","Audi","Proton");
echo "<br>";
var_dump($cars);
?>

<?php
class Car {
  public $color;
  public $model;
  public function __construct($color, $model) {
    $this->color = $color;
    $this->model = $model;
  }
  public function message() {
    return "That car is a " . $this->color . " " . $this->model . "?";
  }
}

$myCar = new Car("silver", "Hilux");
echo "<br>";
echo $myCar -> message();
echo "<br>";
$myCar = new Car("grey", "BMW");
echo $myCar -> message();
?>

<?php
echo "<br>";
$x = "Bye world!";
$x = null;
var_dump($x);
?>

<p><a href="index.html">Back</a></p>

</body>
</html>