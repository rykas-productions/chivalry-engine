<?php
///ALTER TABLE `user_settings` ADD `searchtown` INT(11) UNSIGNED NOT NULL DEFAULT '25' AFTER `reset`;
$macropage = ('streetbum.php');
require('globals.php');
echo "<h2>Street Begging</h2><hr />";
if ($api->UserStatus($userid, 'infirmary')) {
    alert('danger', "Unconscious!", "You cannot go begging if you're in the infirmary.");
    die($h->endpage());
}
if ($api->UserStatus($userid, 'dungeon')) {
    alert('danger', "Locked Up!", "You cannot go begging if you're in the dungeon.");
    die($h->endpage());
}
if (!isset($_GET['action'])) 
{
    $_GET['action'] = '';
}
switch ($_GET['action']) 
{
    case 'beg':
        dobum();
        break;
    default:
        home();
        break;
}
function home()
{
	global $h, $ir;
	echo "Take to the streets to beg for currency. You may receive things, you may not. 
	Your attempts to beg increase 25 per hour, capped at 100 at any one time.<br />
	<b>You can currently beg {$ir['searchtown']} times.</b><br />
	<a href='?action=beg' class='btn btn-primary'>Start Begging</a>";
	$h->endpage();
}

function dobum()
{
	global $ir, $userid, $api, $h, $db;
	if ($ir['searchtown'] == 0)
	{
		alert('danger',"Uh Oh!","You cannot bum anymore this hour. Come back in an hour.",true,'explore.php');
		die($h->endpage());
	}
	$db->query("UPDATE `user_settings` SET `searchtown` = `searchtown` - 1 WHERE `userid` = {$userid}");
	$ir['searchtown']--;
	$rand=Random(1,10);
	if ($rand <= 5)
	{
		$copper=Random(200,950);
		$copper=round($copper+($copper*levelMultiplier($ir['level'])));
		$txt = "You beg on the street and receive " . number_format($copper) . " Copper Coins.";
		$api->UserGiveCurrency($userid, 'primary', $copper);
		addToEconomyLog('Begging', 'copper', $copper);
	}
	elseif (($rand <= 8) && ($rand > 6))
	{
		$copper=Random(10,24);
		$copper=round($copper+($copper*levelMultiplier($ir['level'])));
		$txt = "You stumble upon " . number_format($copper) . " Chivalry Tokens while begging on the street.";
		$api->UserGiveCurrency($userid, 'secondary', $copper);
		addToEconomyLog('Begging', 'token', $copper);
	}
	else
	{
		$txt = "You did not receive anything while begging.";
	}
	echo $txt . " <b>You can beg {$ir['searchtown']} more times this hour.</b><hr />
	<div class='row'>
		<div class='col'>
			<a href='?action=beg' class='btn btn-primary'>Beg Again</a>
		</div>
		<div class='col'>
			<a href='explore.php' class='btn btn-danger'>Explore</a>
		</div>
	</div>";
	$h->endpage();
}