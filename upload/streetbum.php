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
	$rand=Random(1,20);
	if ($rand <= 5)
	{
		$copper=Random(200,950);
		$txt = "You beg on the street and receive " . number_format($copper) . " Copper Coins.";
		$api->UserGiveCurrency($userid, 'primary', $copper);
		addToEconomyLog('Begging', 'copper', $copper);
		$api->SystemLogsAdd($userid,"begging","Received " . number_format($copper) . " Copper Coins.");
	}
	elseif (($rand <= 8) && ($rand > 6))
	{
	    $min = 10 + (10 * (getUserSkill($userid, 10) * getSkillBonus(10)) / 100);
	    $max = 24 + (24 * (getUserSkill($userid, 10) * getSkillBonus(10)) / 100);
	    $copper = Random($min, $max);
		$txt = "You stumble upon " . number_format($copper) . " Chivalry Tokens while begging on the street.";
		$api->UserGiveCurrency($userid, 'secondary', $copper);
		addToEconomyLog('Begging', 'token', $copper);
		$api->SystemLogsAdd($userid,"begging","Received " . number_format($copper) . " Chivalry Tokens.");
	}
	elseif ($rand == 9)
	{
		$txt = "While laying out in the street, someone smacks you with a rotten fish, simply because you smelled like rotten fish. You shoo 'em off and get a nice fish to nom down on.";
		$api->UserGiveItem($userid,107,1);
		$api->SystemLogsAdd($userid,"begging","Received {$api->SystemItemIDtoName(107)}.");
	}
	elseif ($rand == 10)
	{
		$txt = "You sneak behind a market and snag yourself a half-eaten apple. I guess beggars can't be choosers.";
		$api->UserGiveItem($userid,111,1);
		$api->SystemLogsAdd($userid,"begging","Received {$api->SystemItemIDtoName(111)}.");
	}
	elseif ($rand == 11)
	{
		$txt = "While exploring, you notice a pompous asshole throwing out a perfectly fine piece of ham. You snag it after he disposes of it.";
		$api->UserGiveItem($userid,109,1);
		$api->SystemLogsAdd($userid,"begging","Received {$api->SystemItemIDtoName(109)}.");
	}
	elseif ($rand == 12)
	{
		$txt = "While down on your luck, a very sweet little girl walks by and gives you a piece of chocolate. How sweet.";
		$api->UserGiveItem($userid,139,1);
		$api->SystemLogsAdd($userid,"begging","Received {$api->SystemItemIDtoName(139)}.");
	}
	elseif ($rand == 13)
	{
		$txt = "You begin to beg, but trip on a Heavy Rock. What a coincidence, you wanted one!";
		$api->UserGiveItem($userid,2,1);
		$api->SystemLogsAdd($userid,"begging","Received {$api->SystemItemIDtoName(2)}.");
	}
	elseif ($rand == 14)
	{
		$txt = "You should go urinate before you beg... you stand near a bush and let 'er rip. Once done, you notice a stick. Its yours now, you exhibitionist, you!";
		$api->UserGiveItem($userid,1,1);
		$api->SystemLogsAdd($userid,"begging","Received {$api->SystemItemIDtoName(1)}.");
	}
	elseif ($rand == 15)
	{
	    $txt = "You found an empty bucket while begging.";
	    $api->UserGiveItem($userid,295,1);
	    $api->SystemLogsAdd($userid,"begging","Received {$api->SystemItemIDtoName(295)}.");
	}
	elseif ($rand == 16)
	{
	    $txt = "You found two logs while begging!";
	    $api->UserGiveItem($userid,410,2);
	    $api->SystemLogsAdd($userid,"begging","Received 2 x {$api->SystemItemIDtoName(410)}.");
	}
	else
	{
		$txt = "You did not receive anything while begging.";
	}
	echo $txt . " <b>You can beg " . number_format($ir['searchtown']) . " more times this hour.</b><hr />
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