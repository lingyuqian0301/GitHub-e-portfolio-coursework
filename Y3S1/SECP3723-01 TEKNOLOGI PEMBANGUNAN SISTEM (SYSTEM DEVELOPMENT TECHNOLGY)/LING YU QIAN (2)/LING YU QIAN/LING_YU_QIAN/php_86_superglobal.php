<!DOCTYPE html>
<html lang="en-US">
<body>

<?php 
$a = 32;
$b = 12;

function addition() {
  $GLOBALS['c'] = $GLOBALS['a'] + $GLOBALS['b'];
}

addition();
echo $c;
?>

<?php
echo $_SERVER['PHP_SELF'];
echo "<br>";
echo $_SERVER['SERVER_NAME'];
echo "<br>";
echo $_SERVER['HTTP_HOST'];
echo "<br>";
echo $_SERVER['HTTP_REFERER'];
echo "<br>";
echo $_SERVER['HTTP_USER_AGENT'];
echo "<br>";
echo $_SERVER['SCRIPT_NAME'];
?>

<form method="post" action="<?php echo $_SERVER['PHP_SELF'];?>">
  Your name: <input type="text" name="fname">
  <input type="submit">
</form>

<?php
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // collect value of input field
    $name = htmlspecialchars($_REQUEST['fname']);
    if (empty($name)) {
        echo "No name has entered";
    } else {
        echo $name;
    }
}
?>

<form method="post" action="<?php echo $_SERVER['PHP_SELF'];?>">
  Your name: <input type="text" name="fname">
  <input type="submit">
</form>

<?php
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // collect value of input field
    $name = $_POST['fname'];
    if (empty($name)) {
        echo "No name has entered";
    } else {
        echo $name;
    }
}
?>

<!-- <br><a href="test_get.php?subject=PHP&web=W3schools.com">Test $_GET</a>-->

<?php
/*echo "Study " . $_GET['subject'] . " at " . $_GET['web'];*/
?>

<p><a href="index.html">Back</a></p>

</body>
</html>