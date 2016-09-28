<?php
$menuhide = 1;
$atkpage = 1;
$tresder = mt_rand(100, 999);
require("globals.php");
$_GET['user'] =  (isset($_GET['user']) && is_numeric($_GET['user']))  ? abs(intval($_GET['user'])) : '';
if (empty($_GET['nextstep']))
{
	$_GET['nextstep']=0;
}
if ($_GET['nextstep'] > 0)
   {
	      $_GET['tresde'] =
        (isset($_GET['tresde']) && is_numeric($_GET['tresde']))
                ? abs(intval($_GET['tresde'])) : 0;
		if (!isset($_SESSION['tresde']))
		{
			$_SESSION['tresde'] = 0;
		}
		if (($_SESSION['tresde'] == $_GET['tresde']) || $_GET['tresde'] < 100)
		{
			alert("danger","Oh no!","Refreshing while attacking is a bannable offense. You can lose all your experience for that. <a href='index.php'>Go Home</a>.");
			die($h->endpage());
		}
		$_SESSION['tresde'] = $_GET['tresde'];
   }
if (!$_GET['user'])
{
    alert("danger","Oh no!","You cannot attack no-one. Please make sure you are using the attack link found on profiles. <a href='index.php'>Go Home</a>.");
    die($h->endpage());
}
else if ($_GET['user'] == $userid)
{
    alert("danger","Are You Depressed?","No matter how depressed you are, you cannot attack yourself! <a href='index.php'>Go Home</a>.");
    die($h->endpage());
}
else if ($ir['hp'] <= 1)
{
    alert("danger","Unconscious","In order to fight someone, you need HP. Restore your HP by drinking a potion, or wait for it to regenerate on its own. <a href='index.php'>Go Home</a>.");
    die($h->endpage());
}
else if (isset($_SESSION['attacklost']) && $_SESSION['attacklost'] == 1)
{
    $_SESSION['attacklost'] = 0;
    alert("danger","Uh Oh!","You cannot win another battle if you lost and ran from another. <a href='index.php'>Go Home</a>.");
    die($h->endpage());
}
$youdata = $ir;
$q = $db->query("SELECT `u`.`userid`, `hp`, `equip_armor`, `username`,
	       `equip_primary`, `equip_secondary`, `guild`, `location`, `maxhp`,
	       `guard`, `agility`, `strength`, `gender`
	FROM `users` AS `u`
	INNER JOIN `userstats` AS `us` ON `u`.`userid` = `us`.`userid`
	WHERE `u`.`userid` = {$_GET['user']}
	LIMIT 1");
if ($db->num_rows($q) == 0)
{
    alert("danger","Hold Up!","The person you are trying to attack just doesn't exist. Confirm the user you're trying to attack and try again! <a href='index.php'>Go Home</a>.");
    die($h->endpage());
}
$odata = $db->fetch_row($q);
$db->free_result($q);
if ($ir['gender'] == 'Male')
{
	$youabbr='his';
}
else
{
	$youabbr='her';
}
if ($odata['gender'] == 'Male')
{
	$oabbr = 'his';
}
else
{
	$oabbr = 'her';
}
if ($ir['attacking'] && $ir['attacking'] != $_GET['user'])
{
    $_SESSION['attacklost'] = 0;
    alert("danger","O_o?","Something bad happened. We don't know what, but its okay. Nothing has effected your account. <a href='index.php'>Go Home</a>.");
	$db->query("UPDATE `users` SET `attacking` = 0 WHERE `userid` = {$userid}");
    die($h->endpage());
}
if ($odata['hp'] == 1)
{
    $_SESSION['attacking'] = 0;
    $ir['attacking'] = 0;
    $db->query("UPDATE `users`
	SET `attacking` = 0
	WHERE `userid` = {$userid}");
    alert("danger","Opponent Unconscious!","{$odata['username']} happens to have no HP. Come back after they have at least half their HP! <a href='index.php'>Go Home</a>.");
    die($h->endpage());
}
else if (user_infirmary($_GET['user']) == true)
{
    $_SESSION['attacking'] = 0;
    $ir['attacking'] = 0;
    $db->query("UPDATE `users`
	SET `attacking` = 0
	WHERE `userid` = {$userid}");
	alert("danger","In the Infirmary!","{$odata['username']} happens to be in the infirmary at the moment. Try attacking later when they are not! <a href='index.php'>Go Home</a>.");
    die($h->endpage());
}
else if (user_infirmary($ir['userid']) == true)
{
    $_SESSION['attacking'] = 0;
    $ir['attacking'] = 0;
    $db->query("UPDATE `users`
	SET `attacking` = 0
	WHERE `userid` = {$userid}");
   alert("danger","You're in the Infirmary!","You're in the infirmary at the moment. Try attacking later when you are feeling better! <a href='index.php'>Go Home</a>.");
    die($h->endpage());
}
else if (user_dungeon($_GET['user']) == true)
{
    $_SESSION['attacking'] = 0;
    $ir['attacking'] = 0;
    $db->query("UPDATE `users`
	SET `attacking` = 0
	WHERE `userid` = {$userid}");
    alert("danger","Someone's in Trouble!","{$odata['username']} is in the dungeon at the moment. Try attacking later when they pay their debt to society! <a href='index.php'>Go Home</a>.");
    die($h->endpage());
}
else if (user_dungeon($userid) == true)
{
    $_SESSION['attacking'] = 0;
    $ir['attacking'] = 0;
    $db->query("UPDATE `users`
	SET `attacking` = 0
	WHERE `userid` = {$userid}");
    alert("danger","You're in Trouble!","You're in the dungeon at the moment. Try attacking later when you pay your debt to society! <a href='index.php'>Go Home</a>.");
    die($h->endpage());
}
$_GET['weapon'] =
        (isset($_GET['weapon']) && is_numeric($_GET['weapon']))
                ? abs(intval($_GET['weapon'])) : '';
if ($_GET['weapon'])
{
    $_GET['nextstep'] =
            (isset($_GET['nextstep']) && is_numeric($_GET['nextstep']))
                    ? abs(intval($_GET['nextstep'])) : 1;
    if (!$_GET['nextstep'])
    {
        $_GET['nextstep'] = 1;
    }
	if ($_GET['nextstep'] >= $set['MaxAttacksPerSession'])
	{
		$_SESSION['attacking'] = 0;
		$ir['attacking'] = 0;
		$db->query("UPDATE `users`
		SET `attacking` = 'false'
		WHERE `userid` = {$userid}");
		alert("warning","Stalemate!","You and your opponent are quite tired of this fight. Maybe go home and train? This fight ends in stalemate. <a href='index.php'>Go Home</a>.");
		die($h->endpage());
	}
    if ($_SESSION['attacking'] == 0 && $ir['attacking'] == 0)
    {
        if ($youdata['energy'] >= $youdata['maxenergy'] / $set['AttackEnergyCost'])
        {
            $youdata['energy'] -= floor($youdata['maxenergy'] / $set['AttackEnergyCost']);
            $cost = floor($youdata['maxenergy'] / $set['AttackEnergyCost']);
            $db->query(
                    "UPDATE `users` SET `energy` = `energy` - {$cost} "
                            . "WHERE `userid` = {$userid}");
            $_SESSION['attacklog'] = '';
            $_SESSION['attackdmg'] = 0;
        }
        else
        {
			$EnergyPercent=floor(100/$set['AttackEnergyCost']);
			$UserCurrentEnergy=floor($ir['maxenergy']/$ir['energy']);
            alert("danger","Not Enough Energy!","To attack someone, you need at least {$EnergyPercent}% energy. You only have {$UserCurrentEnergy}% energy. Try again later! <a href='index.php'>Go Home</a>.");
            die($h->endpage());
        }
    }
    $_SESSION['attacking'] = 1;
    $ir['attacking'] = $odata['userid'];
    $attackstatus_sql =
            <<<SQL
   		UPDATE `users`
    	SET `attacking` = {$ir['attacking']}
    	WHERE `userid` = {$userid}
SQL;
    $db->query($attackstatus_sql);
    $_GET['nextstep'] =
            (isset($_GET['nextstep']) && is_numeric($_GET['nextstep']))
                    ? abs(intval($_GET['nextstep'])) : '';
    if ($_GET['weapon'] != $ir['equip_primary']
            && $_GET['weapon'] != $ir['equip_secondary'])
    {
        $abuse_sql =
                <<<SQL
        	UPDATE `users`
        	SET `exp` = 0
        	WHERE `userid` = {$userid}
SQL;
        $db->query($abuse_sql);
        alert("danger","Bug Abuse!","You are trying to abuse a game bug. Don't do that! You lost all your XP for that! <a href='index.php'>Go Home</a>.");
        die($h->endpage());
    }
    $winfo_sql =
            <<<SQL
    	SELECT `itmname`, `weapon`
    	FROM `items`
    	WHERE `itmid` = {$_GET['weapon']}
    	LIMIT 1
SQL;
    $qo = $db->query($winfo_sql);
    if ($db->num_rows($qo) == 0)
    {
        alert("danger","Weapon Non-existent!","You're trying to attack with either a non-existent weapon or an item not setup to be a weapon. Please contact an admin if the latter is true! <a href='index.php'>Go Home</a>.");
        die($h->endpage());
    }
    $r1 = $db->fetch_row($qo);
    $db->free_result($qo);
    $mydamage =
            (int) (($r1['weapon'] * $youdata['strength']
                    / ($odata['guard'] / 1.5)) * (mt_rand(10000, 12000) / 10000));
    $hitratio = max(10, min(60 * $ir['agility'] / $odata['agility'], 95));
    if (mt_rand(1, 100) <= $hitratio)
    {
        if ($odata['equip_armor'] > 0)
        {
            $armorinfo_sql =
                    <<<SQL
            	SELECT `armor`
            	FROM `items`
            	WHERE `itmid` = {$odata['equip_armor']}
            	LIMIT 1
SQL;
            $q3 = $db->query($armorinfo_sql);
            if ($db->num_rows($q3) > 0)
            {
                $mydamage -= $db->fetch_single($q3);
            }
            $db->free_result($q3);
        }
        if ($mydamage < -100000)
        {
            $mydamage = abs($mydamage);
        }
        else if ($mydamage < 1)
        {
            $mydamage = 1;
        }
        $crit = mt_rand(1, 40);
        if ($crit == 17)
        {
            $mydamage *= mt_rand(20, 40) / 10;
        }
        else if ($crit == 25 OR $crit == 8)
        {
            $mydamage /= (mt_rand(20, 40) / 10);
        }
        $mydamage = round($mydamage);
        $odata['hp'] -= $mydamage;
        if ($odata['hp'] == 1)
        {
            $odata['hp'] = 0;
            $mydamage += 1;
        }
		if (($odata['hp'] - $mydamage) < 0)
		{
			$mydamage=$mydamage+$odata['hp'];
		}
		if ($odata['hp'] < 0)
		{
			$odata['hp'] = 0;
		}
        $db->query(
                "UPDATE `users` SET `hp` = `hp` - $mydamage WHERE `userid` = {$_GET['user']}");
        echo "<font color=red>{$_GET['nextstep']}. Using your {$r1['itmname']} you hit {$odata['username']} doing $mydamage damage ({$odata['hp']})</font><br />\n";
        $_SESSION['attackdmg'] += $mydamage;
        $_SESSION['attacklog'] .=
                "<font color=red>{$_GET['nextstep']}. Using {$youabbr} {$r1['itmname']} {$ir['username']} hit {$odata['username']} doing $mydamage damage ({$odata['hp']})</font><br />\n";
    }
    else
    {
        echo "<font color=red>{$_GET['nextstep']}. You tried to hit {$odata['username']} but missed ({$odata['hp']})</font><br />\n";
        $_SESSION['attacklog'] .=
                "<font color=red>{$_GET['nextstep']}. {$ir['username']} tried to hit {$odata['username']} but missed ({$odata['hp']})</font><br />\n";
    }
    if ($odata['hp'] <= 0)
    {
        $odata['hp'] = 0;
        $_SESSION['attackwon'] = $_GET['user'];
        $db->query(
                "UPDATE `users` SET `hp` = 0 WHERE `userid` = {$_GET['user']}");
        echo "
<br />
<b>What do you want to do with {$odata['username']} now?</b><br />
<form action='attackwon.php?ID={$_GET['user']}' method='post'><input class='btn btn-default' type='submit' value='Mug Them' /></form>
<form action='attackbeat.php?ID={$_GET['user']}' method='post'><input class='btn btn-default' type='submit' value='Hospitalize Them' /></form>
<form action='attacktake.php?ID={$_GET['user']}' method='post'><input class='btn btn-default' type='submit' value='Leave Them' /></form>
   ";
    }
    else
    {

        $eq =
                $db->query(
                        "SELECT `itmname`,`weapon` FROM  `items` WHERE `itmid` IN({$odata['equip_primary']}, {$odata['equip_secondary']})");
        if ($db->num_rows($eq) == 0)
        {
            $wep = "Fists";
            $dam =
                    (int) ((((int) ($odata['strength'] / $ir['guard'] / 100))
                            + 1) * (mt_rand(10000, 12000) / 10000));
        }
        else
        {
            $cnt = 0;
            while ($r = $db->fetch_row($eq))
            {
                $enweps[] = $r;
                $cnt++;
            }
            $db->free_result($eq);
            $weptouse = mt_rand(0, $cnt - 1);
            $wep = $enweps[$weptouse]['itmname'];
            $dam =
                    (int) (($enweps[$weptouse]['weapon'] * $odata['strength']
                            / ($youdata['guard'] / 1.5))
                            * (mt_rand(10000, 12000) / 10000));
        }
        $hitratio =
                max(10, min(60 * $odata['agility'] / $ir['agility'], 95));
        if (mt_rand(1, 100) <= $hitratio)
        {
            if ($ir['equip_armor'] > 0)
            {
                $q3 =
                        $db->query(
                                "SELECT `armor` FROM `items` WHERE `itmid` = {$ir['equip_armor']} LIMIT 1");
                if ($db->num_rows($q3) > 0)
                {
                    $dam -= $db->fetch_single($q3);
                }
                $db->free_result($q3);
            }
            if ($dam < -100000)
            {
                $dam = abs($dam);
            }
            else if ($dam < 1)
            {
                $dam = 1;
            }
            $crit = mt_rand(1, 40);
            if ($crit == 17)
            {
                $dam *= mt_rand(20, 40) / 10;
            }
            else if ($crit == 25 OR $crit == 8)
            {
                $dam /= (mt_rand(20, 40) / 10);
            }
            $dam = round($dam);
            $youdata['hp'] -= $dam;
            if ($youdata['hp'] == 1)
            {
                $dam += 1;
                $youdata['hp'] = 0;
            }
			if (($ir['hp'] - $dam) < 0)
			{
				$dam=$ir['hp'];
				$youdata['hp']=0;
			}
			if ($ir['hp'] < 0)
			{
				$ir['hp'] = 0;
			}
            $db->query("UPDATE `users` SET `hp` = `hp` - $dam WHERE `userid` = $userid");
            $ns = $_GET['nextstep'] + 1;
            echo "<font color=blue>{$ns}. Using $oabbr $wep {$odata['username']} hit you doing $dam damage ({$youdata['hp']})</font><br />\n";
            $_SESSION['attacklog'] .=
                    "<font color=blue>{$ns}. Using $oabbr $wep {$odata['username']} hit {$ir['username']} doing $dam damage ({$youdata['hp']})</font><br />\n";
        }
        else
        {
            $ns = $_GET['nextstep'] + 1;
            echo "<font color=red>{$ns}. {$odata['username']} tried to hit you but missed ({$youdata['hp']})</font><br />\n";
            $_SESSION['attacklog'] .=
                    "<font color=blue>{$ns}. {$odata['username']} tried to hit {$ir['username']} but missed ({$youdata['hp']})</font><br />\n";
        }
        if ($youdata['hp'] <= 0)
        {
            $youdata['hp'] = 0;
            $_SESSION['attacklost'] = 1;
            $db->query("UPDATE `users` SET `hp` = 0 WHERE `userid` = $userid");
            echo "<form action='attacklost.php?ID={$_GET['user']}' method='post'><input type='submit' class='btn btn-default' value='Continue' />";
        }
    }
}
else if ($odata['hp'] < 5)
{
    alert("danger","Opponent Weak!","You can only attack someone when they have HP. <a href='index.php'>Go Home</a>.");
    die($h->endpage());
}
else if ($ir['guild'] == $odata['guild'] && $ir['guild'] > 0)
{
    alert("danger","In the Infirmary!","You are in the same guild as {$odata['username']}! Hit the bong, not your guild mates! <a href='index.php'>Go Home</a>.");
    die($h->endpage());
}
else if ($youdata['energy'] < $youdata['maxenergy'] / $set['AttackEnergyCost'])
{
    $EnergyPercent=floor(100/$set['AttackEnergyCost']);
	$UserCurrentEnergy=floor($ir['energy']/$ir['maxenergy']);
	alert("danger","Low Energy!","You can only attack someone when you have at least {$EnergyPercent}% energy. You only have {$UserCurrentEnergy}% energy. Come back after your energy has refilled. <a href='index.php'>Go Home</a>.");
    die($h->endpage());
}
else if ($youdata['location'] != $odata['location'])
{
    alert("danger","Not Even Close!","You can only attack someone who is in the same town as you! <a href='index.php'>Go Home</a>.");
    die($h->endpage());
}
if ($youdata['hp'] <= 0 OR $odata['hp'] <= 0)
{ }
else
{
    $vars['hpperc'] = round($youdata['hp'] / $youdata['maxhp'] * 100);
    $vars['hpopp'] = 100 - $vars['hpperc'];
    $vars2['hpperc'] = round($odata['hp'] / $odata['maxhp'] * 100);
    $vars2['hpopp'] = 100 - $vars2['hpperc'];
    $mw =
            $db->query(
                    "SELECT `itmid`,`itmname` FROM  `items`  WHERE `itmid` IN({$ir['equip_primary']}, {$ir['equip_secondary']})");
    echo '
		<tr>
	<td colspan="2" align="center">Attack with:<br />
   ';
    if ($db->num_rows($mw) > 0)
    {
        while ($r = $db->fetch_row($mw))
        {
            if (!isset($_GET['nextstep']))
            {
                $ns = 1;
            }
            else
            {
                $ns = $_GET['nextstep'] + 2;
            }
            if ($r['itmid'] == $ir['equip_primary'])
            {
                echo '<b>Primary Weapon:</b> ';
            }
            if ($r['itmid'] == $ir['equip_secondary'])
            {
                echo '<b>Secondary Weapon:</b> ';
            }
            echo "<a href='attack.php?nextstep=$ns&user={$_GET['user']}&weapon={$r['itmid']}&tresde=$tresder'>{$r['itmname']}</a><br />";
        }
    }
    else
    {
        alert("warning","Check Yourself!","You have no weapons to attack with! <a href='index.php'>Go Home</a>.");
    }
    $db->free_result($mw);
    echo "</table>";
	echo "<table width='50%' class='table'>
		<tr>
			<td>
				<div class='progress'>
				  <div class='progress-bar progress-bar-success' role='progressbar' aria-valuenow='{$vars['hpperc']}'
				  aria-valuemin='0' aria-valuemax='100' style='width:{$vars['hpperc']}%'>
					Your Health: {$vars['hpperc']}% ({$youdata['hp']} / {$youdata['maxhp']})
				  </div>
				</div>
			</td>
		</tr>
		<tr>
			<td>
				<div class='progress'>
				  <div class='progress-bar progress-bar-success' role='progressbar' aria-valuenow='{$vars2['hpperc']}'
				  aria-valuemin='0' aria-valuemax='100' style='width:{$vars2['hpperc']}%'>
					{$odata['username']}'s Health: {$vars2['hpperc']}% ({$odata['hp']} / {$odata['maxhp']})
				  </div>
				</div>
			</td>
		</tr>
		</table>";
}
$h->endpage();