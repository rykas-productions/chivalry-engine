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
        echo "What a cheater you are.";
    }
    else
    {
        echo "You beat {$r['username']} ";
        $qe = $r['level'] * $r['level'] * $r['level'];
        $expgain = mt_rand($qe / 2, $qe);
		if ($expgain < 0)
		{
			$expgain=$expgain*-1;
		}
        $expperc = round($expgain / $ir['xp_needed'] * 100);
        echo "and gained $expperc% ($expgain) EXP!<br />
You hide your weapons and drop {$r['username']} off outside the hospital entrance. Feeling satisfied, you walk home.";
        $hosptime = mt_rand(10, 40);
        $db->query(
                "UPDATE `users` SET `xp` = `xp` + $expgain WHERE `userid` = $userid");
        $hospreason = $db->escape("Used for experience by <a href='viewuser.php?u={$userid}'>{$ir['username']}</a>");
        $db->query("UPDATE `users` SET `hp` = 1 WHERE `userid` = {$r['userid']}");
		put_infirmary($r['userid'],$hosptime,$hospreason);
        event_add($r['userid'],
                "<a href='viewuser.php?u=$userid'>{$ir['username']}</a> attacked you and left you for experience.",
                $c, 'combat');
        $atklog = $db->escape($_SESSION['attacklog']);
        $db->query(
                "INSERT INTO `attacklogs` VALUES(NULL, $userid, {$_GET['ID']},
                        'won', " . time() . ", -2, '$atklog')");
						
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
