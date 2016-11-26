<?php
$atkpage = 1;
require_once('globals.php');
$_GET['ID'] =
        (isset($_GET['ID']) && is_numeric($_GET['ID']))
                ? abs((int) $_GET['ID']) : 0;
$_SESSION['attacking'] = 'false';
$ir['attacking'] = 'false';
$db->query("UPDATE `users` SET `attacking` = 0 WHERE `userid` = $userid");
$od =
        $db->query(
                "SELECT * FROM `users` WHERE `userid` = {$_GET['ID']}");
if (!isset($_SESSION['attackwon']) || $_SESSION['attackwon'] != $_GET['ID'])
{
    die("Cheaters don't get anywhere.");
}
if ($db->num_rows($od))
{
    $r = $db->fetch_row($od);
    $db->free_result($od);
    if ($r['hp'] == 1)
    {
        alert('danger',"{$lang['ERROR_GENERIC']}","{$lang['ATT_HP']}");
		exit($h->endpage());
    }
    else
    {
        $qe = $r['level'] * $r['level'] * $r['level'];
        $expgain = mt_rand($qe / 2, $qe);
		if ($expgain < 0)
		{
			$expgain=$expgain*-1;
		}
        $expperc = round($expgain / $ir['xp_needed'] * 100);
		alert('success',"{$lang['ATT_BEAT']} {$r['username']}!","{$lang['ATT_BEAT']} {$r['username']} {$lang['ATT_XP_1']} $expperc% ($expgain) {$lang['GEN_EXP']}. {$lang['ATT_XP_2']} {$r['username']} {$lang['ATT_XP_3']}");
        $hosptime = mt_rand(10, 40);
        $db->query(
                "UPDATE `users` SET `xp` = `xp` + $expgain WHERE `userid` = $userid");
        $hospreason = $db->escape("Used for experience by <a href='profile.php?user={$userid}'>{$ir['username']}</a>");
        $db->query("UPDATE `users` SET `hp` = 1 WHERE `userid` = {$r['userid']}");
		put_infirmary($r['userid'],$hosptime,$hospreason);
        event_add($r['userid'],
                "<a href='profile.php?u=$userid'>{$ir['username']}</a> attacked you and left you for experience.",
                $c, 'combat');
		$api->SystemLogsAdd($userid,'attacking',"Attacked {$r['username']} [{$r['userid']}] and gained {$expperc}% Experience.");			
        $_SESSION['attackwon'] = 0;
		if ($r['user_level'] == 'NPC')
		{
		    $db->query("UPDATE `users` SET `hp` = `maxhp`  WHERE `userid` = {$r['userid']}");
			$db->query("UPDATE `infirmary` SET `infirmary_out` = 0 WHERE `infirmary_user` ={$r['userid']}");
		}
	}
}
else
{
    $db->free_result($od);
    echo "You beat Mr. non-existant!";
}

$h->endpage();
