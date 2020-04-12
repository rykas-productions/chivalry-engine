<?php
require('globals.php');
if (!isset($_GET['action'])) {
    $_GET['action'] = '';
}
switch ($_GET['action']) {
    case 'mine':
        mine();
        break;
    case 'buypower':
        buypower();
        break;
	case 'herb':
		mine_item();
		break;
	case 'potion':
		potion();
		break;
    default:
        home();
        break;
}
function home()
{
	global $db, $h;
	$todayLogID = date('Ymd');
	$todayFormat = date('Y/m/d');
	echo "<h3>Daily Economy Data for {$todayFormat}</h3><hr />";
	$q=$db->query("SELECT * FROM `economy_log` WHERE `ecDate` = '{$todayLogID}'");
	echo "<div class='row'>
			<div class='col-sm'>
				<h5>Source</h3>
			</div>
			<div class='col-sm'>
				<h5>Change</h6>
			</div>
		</div>
		<hr />";
		$copper = 0;
		$token = 0;
	while ($r = $db->fetch_row($q))
	{
		if ($r['ecCurrency'] == 'copper')
		{
			$type = "Copper Coins";
			$copper = $copper + $r['ecChange'];
		}
		else
		{
			$type = "Chivalry Tokens";
			$token = $token + $r['ecChange'];
		}
		if ($r['ecChange'] > 0)
			$class="text-success";
		elseif ($r['ecChange'] == 0)
			$class="text-default";
		else
			$class="text-danger";
		
		echo "<div class='row'>
				<div class='col-sm'>
					{$r['ecSource']}
				</div>
				<div class='col-sm {$class}'>
					" . number_format($r['ecChange']) . " {$type}
				</div>
			</div>
			<hr />";
	}
	echo "<div class='row'>
				<div class='col-sm'>
					<b>Total Change
				</div>
				<div class='col-sm'>
					" . number_format($copper) . " Copper Coins / " . number_format($token) . " Chivalry Tokens</b>
				</div>
			</div>
			<hr />";
	$h->endpage();
}