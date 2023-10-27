<?php

$name = $_POST['name'];
$location = $_POST['location'];
$myFile = "test.txt";
$fh = fopen($myFile, 'w') or die("can't open file");
fwrite($fh, $name);
fwrite($fh, $location);
fclose($fh);

?>