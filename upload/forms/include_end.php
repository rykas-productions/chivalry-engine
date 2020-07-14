<?php
$endtime = microtime();
$endtime = explode(' ', $endtime);
$endtime = $endtime[1] + $endtime[0];
$finish = $endtime;
$total_time = $finish - $start;
$total_time = $total_time * 1000;
echo 'Page generated in '. number_format($total_time) .' ms.';
?>