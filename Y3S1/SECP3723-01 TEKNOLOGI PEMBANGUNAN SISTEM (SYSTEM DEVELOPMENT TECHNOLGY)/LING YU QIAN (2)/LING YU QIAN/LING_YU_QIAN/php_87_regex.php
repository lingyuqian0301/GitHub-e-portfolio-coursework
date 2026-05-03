<!DOCTYPE html>
<html lang="en-US">
<body>

<?php
$str = "Go pro!";
$pattern = "/Pro/i";
echo preg_match($pattern, $str); 
?>

<?php
$str = "when the names of the trainees are the same names that have been selected for the new job post.";
$pattern = "/me/i";
echo "<br>" . preg_match_all($pattern, $str);
?>

<?php
$str = "Join UTM GDSC";
$pattern = "/gdsc/i";
echo "<br>" . preg_replace($pattern, "DIABOLO", $str);
?>

<?php
$str = "Dio and broJojo.";
$pattern = "/bro(jo){2}/i";
echo "<br>" . preg_match($pattern, $str);
?>

<p><a href="index.html">Back</a></p>

</body>
</html>