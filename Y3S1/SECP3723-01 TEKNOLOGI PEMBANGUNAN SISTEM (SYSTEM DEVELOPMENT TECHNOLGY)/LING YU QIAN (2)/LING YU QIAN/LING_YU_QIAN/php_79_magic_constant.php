<?php
namespace myArea;

function myValue1(){
  return __NAMESPACE__;
}
?>

<?php
namespace myArea;

class Food {
  public function myValue2(){
    return Food::class;
  }
}
?>

<!DOCTYPE html>
<html lang="en-US">
<body>

<?php
class Fruits {
  public function myValue(){
    return __CLASS__;
  }
}
$apple = new Fruits();
echo $apple->myValue();
?>

<?php
echo __DIR__;
?>

<?php
echo __FILE__;
?>

<?php
function myFunc(){
  return __FUNCTION__;
}
echo myFunc();
?>

<?php
echo __LINE__;
?>

<?php
class Fruit {
  public function myFruit(){
    return __METHOD__;
  }
}
$kiwi = new Fruit();
echo $kiwi->myFruit();
?>

<?php
echo myValue1();
?>

<?php
trait message1 {
  public function msg1() {
    echo __TRAIT__; 
  }
}

class Welcome {
  use message1;
}

$obj = new Welcome();
$obj->msg1();
?>

<?php
$bun = new Food();
echo $bun->myValue2();
?>

<p><a href="index.html">Back</a></p>

</body>
</html>