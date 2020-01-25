<?php
$endtime = microtime();
$endtime = explode(' ', $endtime);
$endtime = $endtime[1] + $endtime[0];
$finish = $endtime;
$total_time = round(($finish - $start), 4);
$total_time = $total_time * 1000;
echo 'Page generated in '.$total_time.' ms.';
?>