<?php

$data = $_POST['data'];
$location = $_POST['location'];
$myFile = "test.txt";
$fh = fopen($myFile, 'w') or die("can't open file");
fwrite($fh, $data);
fwrite($fh, $location);
fclose($fh);

?>