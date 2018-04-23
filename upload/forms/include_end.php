<?php
$endtime = microtime();
$endtime = explode(' ', $endtime);
$endtime = $endtime[1] + $endtime[0];
$finish = $endtime;
$total_time = round(($finish - $start), 4);
echo 'Page generated in '.$total_time.' seconds.';
?>