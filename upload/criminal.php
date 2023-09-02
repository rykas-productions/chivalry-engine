<?php
/*
	File:		criminal.php
	Created: 	4/4/2016 at 11:55PM Eastern Time
	Info: 		Lists created crimes for players to commit.
	Author:		TheMasterGeneral
	Website: 	https://github.com/MasterGeneral156/chivalry-engine
*/
$macropage = ('criminal.php');
require('globals.php');
include('class/class_evalmath.php');
$m = new EvalMath;
echo "<h3><i class='game-icon game-icon-robber'></i> Criminal Center</h3>";
if ($api->UserStatus($ir['userid'], 'infirmary') || $api->UserStatus($ir['userid'], 'dungeon')) {
    alert('danger', "Uh Oh!", "You cannot commit crimes while in the infirmary or dungeon.");
    die($h->endpage());
}
if ($api->UserInfoGet($userid, 'will', true) > 100)
{
	alert('danger', "Uh Oh!", "You cannot commit crimes while your will is over 100%!");
    die($h->endpage());
}
if (!isset($_GET['action'])) {
    $_GET['action'] = '';
}
switch ($_GET['action']) {
    case 'crime':
        crime();
        break;
    default:
        home();
        break;
}
function home()
{
    global $db, $h, $ir, $m, $userid, $api;
    $crimes = array();
    $q2 = $db->query("/*qc=on*/SELECT `crimeGROUP`, `crimeNAME`, `crimeBRAVE`, `crimeID`, `crimePERCFORM` FROM `crimes` ORDER BY `crimeBRAVE` ASC");
    while ($r2 = $db->fetch_row($q2)) {
        $crimes[] = $r2;
    }
    $db->free_result($q2);
    $q = $db->query("/*qc=on*/SELECT `cgID`, `cgNAME` FROM `crimegroups` ORDER BY `cgORDER` ASC");
    while ($r = $db->fetch_row($q)) {
        echo "<div class='card'>
                <div class='card-header'>
                    {$r['cgNAME']} Crimes
                </div>
                <div class='card-body'>";
        foreach ($crimes as $v) {
            if ($v['crimeGROUP'] == $r['cgID']) {
				//Fix from Kyle Massacre. Thanks!
				//https://github.com/KyleMassacre
				$ec = str_ireplace(array("LEVEL", "EXP", "WILL", "IQ"), array($ir['level'], $ir['xp'], $ir['will'], $ir['iq']), $v['crimePERCFORM']) . ";";
				$tokens = token_get_all("<?php {$ec}");
				$expr = '';
				foreach($tokens as $token)
				{
					if(is_string($token))
					{
						if(in_array($token, array('(', ')', '+', '-', '/', '*'), true))
							$expr .= $token;
						continue;
					}
					list($id, $text) = $token;
					if(in_array($id, array(T_DNUMBER, T_LNUMBER)))
						$expr .= $text;
				}
				$v['sucrate']=$m->evaluate($expr);
				try 
				{
					$v['sucrate']=$m->evaluate($expr); 
				}
				catch (\Error $e)
				{
					alert('danger',"Uh Oh!","There's an issue with this crime. Please contact the game administration.",true,'criminal.php');
					die($h->endpage());
				}
				$specialnumber=((getSkillLevel($userid,17)*20)/100);
				$v['sucrate']=$v['sucrate']+($v['sucrate']*$specialnumber);
				if (hasNecklaceEquipped($userid,284))
				{
					$v['sucrate']=$v['sucrate']+($v['sucrate']*0.1);
				}
				if ($v['sucrate'] > 100)
					$v['sucrate']=100;
				$v['sucrate']=round($v['sucrate']);
				echo "  <div class='row'>
                            <div class='col-auto col-sm-6 col-md-4 col-xxl-3 col-xxxl-2'>
                                <div class='row'>
                                    <div class='col-12'>
                                        <small><b>Crime</b></small>
                                    </div>
                                    <div class='col-12'>
                                        {$v['crimeNAME']}
                                    </div>
                                </div>
                            </div>
                            <div class='col-auto col-sm-6 col-md-4 col-xl'>
                                <div class='row'>
                                    <div class='col-12'>
                                        <small><b>Success Chance</b></small>
                                    </div>
                                    <div class='col-12'>
                                        " . scaledColorProgressBar($v['sucrate'], 0, 100, true) . "
                                    </div>
                                </div>
                            </div>
                            <div class='col-auto col-sm-6 col-md-4 col-xl-3 col-xxl-auto'>
                                <div class='row'>
                                    <div class='col-12'>
                                        <a href='?action=crime&c={$v['crimeID']}' class='btn btn-primary btm-sm'>Commit - " . shortNumberParse($v['crimeBRAVE']) . " Brave</a>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <hr />";
            }
        }
        echo "</div>
			</div>";
    }
    $db->free_result($q);
    $h->endpage();
}

function crime()
{
    global $db, $userid, $ir, $h, $api, $m;
    if (!isset($_GET['c'])) {
        $_GET['c'] = 0;
    }
    $_GET['c'] = abs($_GET['c']);
    if ($_GET['c'] <= 0) {
        alert('danger', "Invalid Crime!", "You have chosen to commit and invalid crime.", true, 'criminal.php');
    } else {
        $q = $db->query("/*qc=on*/SELECT * FROM `crimes` WHERE `crimeID` = {$_GET['c']} LIMIT 1");
        if ($db->num_rows($q) == 0) {
            alert('danger', "Invalid Crime!", "You are trying to commit a non-existent crime.", true, 'criminal.php');
            die($h->endpage());
        }
        $r = $db->fetch_row($q);
        $db->free_result($q);
        if ($ir['brave'] < $r['crimeBRAVE']) {
            alert('danger', "Uh Oh!", "You do not have enough Bravery to commit this crime. You only have {$ir['brave']} Brave.", true, 'criminal.php');
            die($h->endpage());
        } else {
            //Fix from Kyle Massacre. Thanks!
            //https://github.com/KyleMassacre
            $ec = str_ireplace(array("LEVEL", "EXP", "WILL", "IQ"), array($ir['level'], $ir['xp'], $ir['will'], $ir['iq']), $r['crimePERCFORM']) . ";";
            $tokens = token_get_all("<?php {$ec}");
            $expr = '';
            foreach($tokens as $token)
            {
                if(is_string($token))
                {
                    if(in_array($token, array('(', ')', '+', '-', '/', '*'), true))
                        $expr .= $token;
                    continue;
                }
                list($id, $text) = $token;
                if(in_array($id, array(T_DNUMBER, T_LNUMBER)))
                    $expr .= $text;
            }
            $sucrate=$m->evaluate($expr);
            try 
            {
                $sucrate=$m->evaluate($expr); 
            }
            catch (\Error $e)
            {
                alert('danger',"Uh Oh!","There's an issue with this crime. Please contact the game administration.",true,'criminal.php');
                die($h->endpage());
            }
            if (!$sucrate)
            {
                alert('danger',"Uh Oh!","There's an issue with this crime. Please contact the game administration.",true,'criminal.php');
                die($h->endpage());
            }
			$specialnumber=((getSkillLevel($userid,17)*20)/100);
			$sucrate=$sucrate+($sucrate*$specialnumber);
			if (hasNecklaceEquipped($userid,284))
					$sucrate=$sucrate+($sucrate*0.1);
            $ir['brave'] -= $r['crimeBRAVE'];
            $api->UserInfoSet($userid, "brave", "-{$r['crimeBRAVE']}");
            $lvlMulti = levelMultiplier($ir['level'], $ir['reset']);
            if (Random(1, 100) <= $sucrate) {
                if (!empty($r['crimePRICURMIN'])) {
                    $prim_currency = Random($r['crimePRICURMIN'], $r['crimePRICURMAX']);
                    if ($lvlMulti > 0)
                        $prim_currency = $prim_currency * levelMultiplier($ir['level'], $ir['reset']);
                    $api->UserGiveCurrency($userid, 'primary', $prim_currency);
					crime_log($_GET['c'],true,'copper',$prim_currency);
					addToEconomyLog('Criminal Activities', 'copper', $prim_currency);
                }
                if (!empty($r['crimeSECCURMIN'])) {
                    $sec_currency = Random($r['crimeSECCURMIN'], $r['crimeSECURMAX']);
                    if ($lvlMulti > 0)
                        $sec_currency = $sec_currency * levelMultiplier($ir['level'], $ir['reset']);
                    $api->UserGiveCurrency($userid, 'secondary', $sec_currency);
					crime_log($_GET['c'],true,'token',$sec_currency);
					addToEconomyLog('Criminal Activities', 'token', $sec_currency);
                }
                if (!empty($r['crimeITEMSUC'])) {
                    item_add($userid, $r['crimeITEMSUC'], 1);
					crime_log($_GET['c'],true,'item',1);
                }
                if (empty($prim_currency)) {
                    $prim_currency = 0;
                }
                if (empty($sec_currency)) {
                    $sec_currency = 0;
                }
                if (empty($r['crimeITEMSUC'])) {
                    $r['crimeITEMSUC'] = 0;
                }
                if ($lvlMulti > 0)
                    $r['crimeXP'] = $r['crimeXP']  * levelMultiplier($ir['level'], $ir['reset']);
				if ($_GET['c'] == 18)
				{
					$achieved=$db->query("/*qc=on*/SELECT * FROM `achievements_done` WHERE `userid` = {$userid} and `achievement` = 86");
					if ($db->num_rows($achieved) == 0)
					{
						$api->GameAddNotification($userid,"You have received the 'King of the World' achievement for slaying the tyrant emperor. We've also given you a special badge, too!");
						$api->UserGiveItem($userid,294,1);
						$db->query("INSERT INTO `achievements_done` (`userid`, `achievement`) VALUES ('{$userid}', '86')");
						$db->query("UPDATE `user_settings` SET `skill_points` = `skill_points` + 1 WHERE `userid` = {$userid}");
					}
				}
				$text = str_ireplace(array("{money}","{secondary}","{item}"), array(shortNumberParse($prim_currency),shortNumberParse($sec_currency),$api->SystemItemIDtoName($r['crimeITEMSUC'])), $r['crimeSTEXT']);
                $title = "Success!";
                $type = 'success';
                $api->UserInfoSetStatic($userid, "xp", $ir['xp'] + $r['crimeXP']);
                $api->SystemLogsAdd($userid, 'crime', "Successfully committed the {$r['crimeNAME']} crime.");
            } else {
                $title = "Uh Oh!";
                $type = 'danger';
                $dtime = Random($r['crimeDUNGMIN'], $r['crimeDUNGMAX']);
                $text = str_replace("{time}", shortNumberParse($dtime), $r['crimeFTEXT']);
                $api->UserStatusSet($userid, 'dungeon', $dtime, $r['crimeDUNGREAS']);
                $api->SystemLogsAdd($userid, 'crime', "Failed to commit the {$r['crimeNAME']} crime.");
				crime_log($_GET['c'],false,0,0);
            }
			$api->SystemLogsAdd($userid, 'xp_gain', "+" . number_format($r['crimeXP']) . "XP");
            alert("{$type}", "{$title}", "{$r['crimeITEXT']} {$text}", true, "?action=crime&c={$_GET['c']}", "Attempt Again");
            die($h->endpage());
        }
    }
}
function crime_log($crimeid,$won,$wontype,$wonqty)
{
	global $db,$userid,$api;
	$q=$db->query("/*qc=on*/SELECT * FROM `crime_logs` WHERE `userid` = {$userid} AND `crimeid` = {$crimeid}");
	if ($db->num_rows($q) == 0)
	{
		$db->query("INSERT INTO `crime_logs` 
					(`userid`, `crimeid`, `crimetotal`, `crimesuccess`,
					`crimecopper`, `crimetoken`, `crimeitem`) 
					VALUES 
					('{$userid}', '{$crimeid}', '0', '0', '0', '0', '0')");
	}
	$db->free_result($q);
	$q=$db->query("/*qc=on*/SELECT * FROM `crime_logs` WHERE `userid` = {$userid} AND `crimeid` = {$crimeid}");
	$r=$db->fetch_row($q);
	$db->query("UPDATE `crime_logs` SET `crimetotal` = `crimetotal` + 1 WHERE `userid` = {$userid} AND `crimeid` = {$crimeid}");
	if ($won)
	{
		if ($wontype == 'copper')
		{
			$db->query("UPDATE `crime_logs` SET `crimecopper` = `crimecopper` + {$wonqty} WHERE `userid` = {$userid} AND `crimeid` = {$crimeid}");
		}
		if ($wontype == 'token')
		{
			$db->query("UPDATE `crime_logs` SET `crimetoken` = `crimetoken` + {$wonqty} WHERE `userid` = {$userid} AND `crimeid` = {$crimeid}");
		}
		if ($wontype == 'item')
		{
			$db->query("UPDATE `crime_logs` SET `crimeitem` = `crimeitem` + 1 WHERE `userid` = {$userid} AND `crimeid` = {$crimeid}");
		}
		$db->query("UPDATE `crime_logs` SET `crimesuccess` = `crimesuccess` + 1 WHERE `userid` = {$userid} AND `crimeid` = {$crimeid}");
	}
}