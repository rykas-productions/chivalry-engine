<?php
$endtime = microtime();
$endtime = explode(' ', $endtime);
$endtime = $endtime[1] + $endtime[0];
$finish = $endtime;
$total_time = round(($finish - $start), 4);
$unit = 'seconds';
if ($total_time > (55*60))
{
	$total_time = ($total_time / 60) / 60;
	$unit = 'hours';
}
elseif ($total_time > 55)
{
	$total_time = $total_time / 60;
	$unit = 'minutes';
}
elseif ($total_time < 0.95)
{
	$total_time = $total_time * 1000;
	$unit = 'ms';
}
elseif ($total_time < (0.95 / 1000))
{
	$total_time = ($total_time * 1000) / 1000;
	$unit = 'μs';
}
echo "Page generated in {$total_time} {$unit}.";
?>