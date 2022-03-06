<?php
require('globals.php');
echo "<div class='row'>
    <div class='col-12 col-sm-4 col-xl-3 col-xxl'>
        <a href='economy.php' class='btn btn-block btn-primary'>Today</a><br />
    </div>
    <div class='col-12 col-sm-4 col-xl-3 col-xxl'>
        <a href='?action=thisweek' class='btn btn-block btn-primary'>Last 7 Days</a><br />
    </div>
    <div class='col-12 col-sm-4 col-xl-3 col-xxl'>
        <a href='?action=thismonth' class='btn btn-block btn-primary'>Last 30 Days</a><br />
    </div>
    <div class='col-12 col-sm-4 col-xl-3 col-xxl'>
        <a href='?action=last3month' class='btn btn-block btn-primary'>Last 90 Days</a><br />
    </div>
    <div class='col-12 col-sm-4 col-xl-3 col-xxl'>
        <a href='?action=last6month' class='btn btn-block btn-primary'>Last 180 Days</a><br />
    </div>
    <div class='col-12 col-sm-4 col-xl-3 col-xxl'>
        <a href='?action=lastyear' class='btn btn-block btn-primary'>Last 365 Days</a><br />
    </div>
</div>";
if (!isset($_GET['action'])) {
    $_GET['action'] = '';
}
switch ($_GET['action']) {
    case 'thisweek':
        thisweek();
        break;
    case 'thismonth':
        thismonth();
        break;
    case 'last6month':
        lastsixmonths();
        break;
    case 'last3month':
        lastthreemonths();
        break;
    case 'lastyear':
        lastyear();
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
	$q=$db->query("SELECT * FROM `economy_log` WHERE `ecDate` = '{$todayLogID}'");
	echo "<div class='row'>
            <div class='col-12'>
            <div class='card'>
                <div class='card-header'>
                    Daily Economy Data for {$todayFormat}
                </div>
            <div class='card-body'>
            <div class='row'>
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
					" . shortNumberParse($r['ecChange']) . " {$type}
				</div>
			</div>
			<hr />";
	}
	echo "<div class='row'>
				<div class='col-sm'>
					<b>Total Change
				</div>
				<div class='col-sm'>
					" . shortNumberParse($copper) . " Copper Coins / " . shortNumberParse($token) . " Chivalry Tokens</b>
				</div>
                </div>
                </div>
			</div>
			<hr />";
}

function thisweek()
{
    loadDaysAgoToToday(7);
}
function loadDaysAgoToToday($daysBack)
{
    global $db;
    $cycle = $daysBack - 1;
    $day = array();
    while ($cycle != -1)
    {
        if ($cycle == 0)
            $day[$cycle]=date("Ymd", strtotime("today midnight"));
        else
            $day[$cycle]=date("Ymd", strtotime("today -{$cycle} days"));
        $cycle--;
    }
    $copperChange = 0;
    $tokenChange = 0;
    
    $changeTypeCopper = array();
    $changeTypeToken = array();
    echo "<div class='row'>
            <div class='col-12'>
            <div class='card'>
                <div class='card-header'>
                    Economy for Last {$daysBack} Days
                </div>
            <div class='card-body'>
            <div class='row'>
			<div class='col-sm'>
				<h5>Source</h3>
			</div>
			<div class='col-sm'>
				<h5>Change</h6>
			</div>
		</div>
		<hr />";
    foreach ($day as $k)
    {
        $q=$db->query("SELECT * FROM `economy_log` WHERE `ecDate` = '{$k}'");
        while ($r = $db->fetch_row($q))
        {
            if ($r['ecCurrency'] == 'copper')
            {
                if (!in_array($r['ecSource'], $changeTypeCopper))
                {
                    $changeTypeCopper[$r['ecSource']] = $r['ecChange'];
                }
                else
                {
                    $changeTypeCopper[$r['ecSource']] = $changeTypeCopper[$r['ecSource']] + ($r['ecChange']);
                }
                $copperChange = $copperChange + ($r['ecChange']);
            }
            elseif ($r['ecCurrency'] == 'token')
            {
                if (!in_array($r['ecSource'], $changeTypeToken))
                {
                    $changeTypeToken[$r['ecSource']] = $r['ecChange'];
                }
                else
                {
                    $changeTypeToken[$r['ecSource']] = $changeTypeToken[$r['ecSource']] + ($r['ecChange']);
                }
                $tokenChange = $tokenChange + ($r['ecChange']);
            }
        }
    }
    foreach ($changeTypeCopper as $k => $v)
    {
        if ($v > 0)
            $class="text-success";
        elseif ($v == 0)
            $class="text-default";
        else
            $class="text-danger";
        echo "<div class='row'>
				<div class='col-sm'>
					{$k}
				</div>
				<div class='col-sm {$class}'>
					" . shortNumberParse($v) . " Copper Coins
				</div>
			</div>
			<hr />";
    }
    foreach ($changeTypeToken as $k => $v)
    {
        if ($v > 0)
            $class="text-success";
        elseif ($v == 0)
            $class="text-default";
        else
            $class="text-danger";
        echo "<div class='row'>
				<div class='col-sm'>
					{$k}
				</div>
				<div class='col-sm {$class}'>
					" . shortNumberParse($v) . " Chivalry Tokens
				</div>
			</div>
			<hr />";
    }
    echo "<div class='row'>
				<div class='col-sm'>
					<b>Total Change
				</div>
				<div class='col-sm'>
					" . shortNumberParse($copperChange) . " Copper Coins / " . shortNumberParse($tokenChange) . " Chivalry Tokens</b>
				</div>
                </div>
                </div>
			</div>
			<hr />";
}

function thismonth()
{
    loadDaysAgoToToday(30);
}

function lastthreemonths()
{
    loadDaysAgoToToday(90);
}

function lastsixmonths()
{
    loadDaysAgoToToday(180);
}

function lastyear()
{
    loadDaysAgoToToday(365);
}
$h->endpage();