<?php

ini_set("memory_limit","50M");
$time_start = microtime(true);

include('RecommendSystem.php');
$man = new RecommendSystem("ratings.txt", 1);

$result = $man->process();
print_r($result);


$time_end = microtime(true);
$time = $time_end - $time_start;
echo "Did in $time seconds\n";

?>