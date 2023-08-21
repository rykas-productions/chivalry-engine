<?php
/*
CREATE TABLE `guild_district_info` 
	( `guild_id` INT(11) UNSIGNED NULL DEFAULT NULL , 
	`barracks_warriors` INT(11) UNSIGNED NOT NULL , 
	`barracks_archers` INT(11) UNSIGNED NOT NULL , 
	`barracks_generals` INT(11) UNSIGNED NOT NULL , 
	`moves` INT(11) UNSIGNED NOT NULL , 
	PRIMARY KEY (`guild_id`)
	) ENGINE = InnoDB;
	
CREATE TABLE `guild_districts` 
	( `district_id` INT(11) UNSIGNED NULL DEFAULT NULL AUTO_INCREMENT , 
	`district_owner` INT(11) UNSIGNED NOT NULL DEFAULT '0' , 
	`district_x` INT(11) NOT NULL DEFAULT '0' , 
	`district_y` INT(11) NOT NULL , 
	`district_type` ENUM('normal','elevated','market','lowered','river') NOT NULL DEFAULT 'normal' , 
	`district_melee` INT(11) UNSIGNED NOT NULL DEFAULT '2000' , 
	`district_range` INT(11) UNSIGNED NOT NULL DEFAULT '1000' , 
	`district_general` INT(11) UNSIGNED NOT NULL DEFAULT '0' , 
	`district_fortify` INT(11) NOT NULL DEFAULT '0' , 
	UNIQUE (`district_id`)
	) ENGINE = InnoDB;
	
CREATE TABLE `guild_district_attacklogs` (
  `log_ig` bigint(11) UNSIGNED NOT NULL,
  `attacker` bigint(11) UNSIGNED NOT NULL,
  `defender` bigint(11) UNSIGNED NOT NULL,
  `time` bigint(11) UNSIGNED NOT NULL,
  `winner` text NOT NULL,
  `battle_json_info` text NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;
	
ALTER TABLE `guild_district_attacklogs`
  ADD UNIQUE KEY `log_ig` (`log_ig`);
ALTER TABLE `guild_district_attacklogs`
  MODIFY `log_ig` bigint(11) UNSIGNED NOT NULL AUTO_INCREMENT;

ALTER TABLE `guild_district_battlelog` ADD `attack_captains` INT(11) UNSIGNED NOT NULL AFTER `attack_arch_lost`;
ALTER TABLE `guild_district_info` ADD `barracks_captains` INT(11) UNSIGNED NOT NULL AFTER `barracks_generals`;
*/
require('globals.php');
echo "
<div class='card'>
    <div class='card-header'>
        " . loadImageAsset("explore/guild_district.svg") . " Guild Districts
    </div>
    <div class='card-body'>
        <div class='row'>
            <div class='col-auto'>
                <a href='#' data-toggle='modal' data-target='#district_info' class='btn btn-info'>Info</a>
            </div>
            <div class='col-auto'>
                <a href='#' data-toggle='modal' data-target='#district_stats' class='btn btn-info'>Stats</a>
            </div>
            <div class='col-auto'>
                <a href='guild_district.php' class='btn btn-primary'>Home</a>
            </div>
            <div class='col-auto'>
                <a href='?action=guildinfo' class='btn btn-success'>Your Guild Info</a>
            </div>
            <div class='col-auto'>
                <a href='?action=buy' class='btn btn-danger'>Buy Troops</a>
            </div>
            <div class='col-auto'>
                <a href='?action=general' class='btn btn-secondary'>Unique Units</a>
            </div>
        </div>
    </div>
</div><br />";
if ($ir['guild'] > 0)
{
	$distQ=$db->query("/*qc=on*/SELECT * FROM `guild_district_info` WHERE `guild_id` = {$ir['guild']}");
	if ($db->num_rows($distQ) == 0)
	{
		$db->query("INSERT INTO `guild_district_info` 
					(`guild_id`, `barracks_warriors`, `barracks_archers`, `barracks_generals`, `moves`) 
					VALUES 
					('{$ir['guild']}', '0', '0', '0', '2')");
	}
	$gdi = ($db->fetch_row($db->query("SELECT * FROM `guild_district_info` WHERE `guild_id` = {$ir['guild']}")));
	$gi = $db->fetch_row($db->query("SELECT * FROM `guild` WHERE `guild_id` = {$ir['guild']}"));
}
if (!isset($_GET['action'])) 
{
    $_GET['action'] = '';
}
switch ($_GET['action']) 
{
    case 'view':
        view();
        break;
	case 'attackform':
        attackfromtile();
        break;
	case 'attackformbarracks':
        attackfrombarracks();
        break;
	case 'attack':
        attack();
        break;
	case 'moveto':
        moveto();
        break;
	case 'moveform':
        movefromtile();
        break;
	case 'movebarracks':
        movefrombarracks();
        break;
	case 'fortify':
        fortify();
        break;
	case 'guildinfo':
        guild_info();
        break;
	case 'buy':
        guild_buy();
        break;
	case 'general':
        hireGeneral();
        break;
	case 'test':
        test();
        break;
	case 'attacklog':
        attlog();
        break;
	case 'viewreport':
		battlereport();
        break;
	case 'sabotage':
	    explodedistrict();
	    break;
	case 'homeBeta':
	     home2();
	     break;
	default:
        home2();
        break;
}
function test()
{
	global $districtConfig, $userid, $db, $api, $h, $ir;
	if ($userid > 1)
	{
		alert('danger',"","yo wtf",true,'guild_district.php');
		die($h->endpage());
	}
	$db->query("UPDATE `guild_districts` SET `district_owner` = 0, `district_melee` = 2000, `district_range` = 1000, `district_general` = 0, `district_fortify` = 0");
	$db->query("TRUNCATE `guild_district_info`");
}

function home2()
{
    global $districtConfig, $db, $api;
    $currentY=1;
    while ($currentY <= $districtConfig['MaxSizeY'])
    {
        echo "
        <div class='card'>
            <div class='card-body'>
                <div class='row'>";
        $q=$db->query("SELECT * FROM `guild_districts` WHERE `district_y` = {$currentY} ORDER BY `district_x` ASC LIMIT {$districtConfig['MaxSizeX']}");
        while ($r=$db->fetch_row($q))
        {
            $class = returnTileClass($r['district_id']);
            echo "<div class='col-12 col-sm-6 col-md-4 col-xl'>
                    <div class='card {$class} text-muted'>
                        <div class='card-header'>
                            Y: {$r['district_y']}; X: {$r['district_x']}
                        </div>
                        <div class='card-body'>
                            <div class='row'>
                                <div class='col-auto'>
                                    Guild
                                </div>
                                <div class='col-12 col-xxxl-8'>
                                    <a href='guilds.php?action=view&id={$r['district_owner']}'>{$api->GuildFetchInfo($r['district_owner'],'guild_name')}</a>
                                </div>
                            </div>
                            <div class='row'>
                                <div class='col-12'>
                                    <a href='?action=view&id={$r['district_id']}' class='btn btn-info btn-sm btn-block'>View Info</a>
                                </div>
                            </div>
                        </div>
                    </div>
                    <br />
                </div>";
        }
            echo "</div>
            </div>
        </div>";
        $currentY++;
    }
}
function view()
{
	global $db, $h;
	$district_id = (isset($_GET['id']) && is_numeric($_GET['id'])) ? abs($_GET['id']) : '';
	if (empty($district_id))
	{
		alert('danger',"Uh Oh!","You are attempting to view an invalid district.",true,'guild_district.php');
		die($h->endpage());
	}
	$central=$db->query("SELECT * FROM `guild_districts` WHERE `district_id` = {$district_id}");
	if ($db->num_rows($central) == 0)
	{
		alert('danger',"Uh Oh!","You are attempting to view a non-existent district.",true,'guild_district.php');
		die($h->endpage());
	}
	echo "<div class='card'>
            <div class='card-body'>
                <div class='row'>
                    <div class='col-12 col-lg'>";
                	   parseNWtile($district_id, "info");
                	echo "</div><div class='col-12 col-lg'>";
                	   parseNTile($district_id, "info");
            	   echo "</div><div class='col-12 col-lg'>";
                	parseNEtile($district_id, "info");
	echo"          <br />
                   </div>
                </div>
                <div class='row'>
                    <div class='col-12 col-lg'>";
                	   parseWtile($district_id, "info");
                	echo "</div><div class='col-12 col-lg'>";
                	   parseTile($district_id, "info");
            	   echo "</div><div class='col-12 col-lg'>";
                	parseEtile($district_id, "info");
	echo"          <br />
                   </div>
                </div>
                <div class='row'>
                    <div class='col-12 col-lg'>";
                	   parseSWtile($district_id, "info");
                	echo "</div><div class='col-12 col-lg'>";
                	   parseSTile($district_id, "info");
            	   echo "</div><div class='col-12 col-lg'>";
                	parseSEtile($district_id, "info");
	echo"          <br />
                   </div>
                </div>
            </div>
        </div>";
}
function moveto()
{
	global $userid, $db, $h, $ir, $gdi;
	$district_id = (isset($_GET['id']) && is_numeric($_GET['id'])) ? abs($_GET['id']) : '';
	if (!isset($gdi))
	{
		alert('danger',"Uh Oh!","You cannot move units while not in a guild.",true,'guild_district.php');
		die($h->endpage());
	}
	if (!isGuildLeaders($ir['guild'], $userid))
	{
		alert('danger',"Uh Oh!","You must be a guild leader or co-leader to move units.",true,'guild_district.php');
		die($h->endpage());
	}
	if (blockAccess($ir['guild']))
	{
		alert('danger',"Uh Oh!","You cannot move units while not in a guild.",true,'guild_district.php');
		die($h->endpage());
	}
	if (empty($district_id))
	{
		alert('danger',"Uh Oh!","You are attempting to interact with an invalid district.",true,'guild_district.php');
		die($h->endpage());
	}
	$central=$db->query("SELECT * FROM `guild_districts` WHERE `district_id` = {$district_id}");
	if ($db->num_rows($central) == 0)
	{
		alert('danger',"Uh Oh!","You are attempting to interact with a non-existent district.",true,'guild_district.php');
		die($h->endpage());
	}
	if (!isGuildDistrict($district_id))
	{
		alert('danger',"Uh Oh!","This district is not your's to control.",true,'guild_district.php');
		die($h->endpage());
	}
	echo "<div class='card'>
            <div class='card-body'>
                <div class='row'>
                    <div class='col-12 col-lg'>";
                    	parseNWtile($district_id, "moveto");
                    	echo "</div><div class='col-12 col-lg'>";
                    	parseNTile($district_id, "moveto");
                    	echo "</div><div class='col-12 col-lg'>";
                    	parseNEtile($district_id, "moveto");
                    	echo"          <br />
                   </div>
                </div>
                <div class='row'>
                    <div class='col-12 col-lg'>";
                    	parseWtile($district_id, "moveto");
                    	echo "</div><div class='col-12 col-lg'>";
                    	parseTile($district_id, "moveto");
                    	echo "</div><div class='col-12 col-lg'>";
                    	parseEtile($district_id, "moveto");
                    	echo"          <br />
                   </div>
                </div>
                <div class='row'>
                    <div class='col-12 col-lg'>";
                    	parseSWtile($district_id, "moveto");
                    	echo "</div><div class='col-12 col-lg'>";
                    	parseSTile($district_id, "moveto");
                    	echo "</div><div class='col-12 col-lg'>";
                    	parseSEtile($district_id, "moveto");
	echo"          <br />
                   </div>
                </div>
            </div>
        </div>";
}
function attack()
{
	global $userid, $db, $api, $h, $ir, $gdi;
	$district_id = (isset($_GET['id']) && is_numeric($_GET['id'])) ? abs($_GET['id']) : '';
	if (!isset($gdi))
	{
		alert('danger',"Uh Oh!","You cannot attack while not in a guild.",true,'guild_district.php');
		die($h->endpage());
	}
	if (!isGuildLeaders($ir['guild'], $userid))
	{
		alert('danger',"Uh Oh!","You must be a guild leader or co-leader to coordinate attacks.",true,'guild_district.php');
		die($h->endpage());
	}
	if (blockAccess($ir['guild']))
	{
		alert('danger',"Uh Oh!","You cannot attack while not in a guild.",true,'guild_district.php');
		die($h->endpage());
	}
	if (empty($district_id))
	{
		alert('danger',"Uh Oh!","You are attempting to attack an invalid district.",true,'guild_district.php');
		die($h->endpage());
	}
	$central=$db->query("SELECT * FROM `guild_districts` WHERE `district_id` = {$district_id}");
	if ($db->num_rows($central) == 0)
	{
		alert('danger',"Uh Oh!","You are attempting to attack a non-existent district.",true,'guild_district.php');
		die($h->endpage());
	}
	if (isGuildDistrict($district_id))
	{
		alert('danger',"Uh Oh!","You cannot attack a friendly district.",true,'guild_district.php');
		die($h->endpage());
	}
	echo "<div class='card'>
            <div class='card-body'>
                <div class='row'>
                    <div class='col-12 col-lg'>";
                    	parseNWtile($district_id, "attack");
                    	echo "</div><div class='col-12 col-lg'>";
                    	parseNTile($district_id, "attack");
                    	echo "</div><div class='col-12 col-lg'>";
                    	parseNEtile($district_id, "attack");
                    	echo"          <br />
                   </div>
                </div>
                <div class='row'>
                    <div class='col-12 col-lg'>";
                    	parseWtile($district_id, "attack");
                    	echo "</div><div class='col-12 col-lg'>";
                    	parseTile($district_id, "attack");
                    	echo "</div><div class='col-12 col-lg'>";
                    	parseEtile($district_id, "attack");
                    	echo"          <br />
                   </div>
                </div>
                <div class='row'>
                    <div class='col-12 col-lg'>";
                	parseSWtile($district_id, "attack");
                	echo "</div><div class='col-12 col-lg'>";
                	parseSTile($district_id, "attack");
                	echo "</div><div class='col-12 col-lg'>";
                	parseSEtile($district_id, "attack");
                	echo"          <br />
                   </div>
                </div>
            </div>
        </div>";
}
function explodedistrict()
{
    global $districtConfig, $userid, $db, $api, $h, $ir, $gdi;
    $attack_to = (isset($_GET['id']) && is_numeric($_GET['id'])) ? abs($_GET['id']) : '';
    if (blockAccess($ir['guild']))
    {
        alert('danger',"Uh Oh!","You cannot sabotage a tile while not in a guild.",true,'guild_district.php');
        die($h->endpage());
    }
    if (!isGuildLeaders($ir['guild'], $userid))
    {
        alert('danger',"Uh Oh!","You must be a guild leader or co-leader to sabotage tiles.",true,'guild_district.php');
        die($h->endpage());
    }
    if (empty($attack_to))
    {
        alert('danger',"Uh Oh!","You are attempting to interact with an invalid district.",true,'guild_district.php');
        die($h->endpage());
    }
    $c=$db->query("SELECT * FROM `guild_districts` WHERE `district_id` = {$attack_to}");
    if ($db->num_rows($c) == 0)
    {
        alert('danger',"Uh Oh!","You are attempting to interact with a non-existent district.",true,'guild_district.php');
        die($h->endpage());
    }
    if (!isDistrictAccessible($attack_to))
    {
        alert('danger',"Uh Oh!","You do not have direct access to this tile. Please ensure that you're interacting with tiles on the outer border first, and then tiles adjacent to ones your guild owns.",true,'guild_district.php');
        die($h->endpage());
    }
    if ($gdi['moves'] == 0)
    {
        alert('danger',"Uh Oh!","You have ran out of moves for today. Try again tomorrow.",true,'guild_district.php');
        die($h->endpage());
    }
    $q2=$db->query("SELECT * FROM `guild_districts` WHERE `district_id` = {$attack_to}");
    $r2=$db->fetch_row($q2);
    $guildcurr = $db->fetch_single($db->query("SELECT `guild_seccurr` FROM `guild` WHERE `guild_id` = {$ir['guild']}"));
    if (isset($_POST['warriors']))
    {
        if ($r2['district_fortify'] == 0)
        {
            alert('danger',"Uh Oh!","You cannot sabotage this district tile, as it isn't fortified.",true,'guild_district.php');
            die($h->endpage());
        }
        if (!$api->GuildHasItem($ir['guild'], $districtConfig['sabotageItem']))
        {
            alert('danger',"","You do not have any large explosives in your guild's armory to use.");
            die($h->endpage());
        }
        $random = Random(1,100);
        if ($random <= 60)
        {
            $db->query("UPDATE `guild_districts` SET `district_fortify` = `district_fortify` - 1 WHERE `district_id` = {$attack_to}");
            alert('success',"","You have successfully sabotage this tile (" . resolveCoordinates($attack_to) .") using a large explosive.",true,'guild_district.php');
            $api->GuildAddNotification($r2['district_owner'], "Your guild has suffered a sabotage at (" . resolveCoordinates($attack_to) .") in the guild districts. You've lost a fortification level.");
        }
        else
        {
            alert('success',"","You have used a large explosive and have failed to sabotage this tile (" . resolveCoordinates($attack_to) .").",true,'guild_district.php');
            $api->GuildAddNotification($r2['district_owner'], "Your guild has has stopped an attempted a sabotage at (" . resolveCoordinates($attack_to) .") in the guild districts.");
        }
        $api->SystemLogsAdd($userid,"district","Used a large explosive to attempt a sabotage on tile " . resolveCoordinates($attack_to) .".");
        $db->query("UPDATE `guild_district_info` SET `moves` = `moves` - 1 WHERE `guild_id` = {$ir['guild']}");
        $api->GuildAddNotification($ir['guild'], "<a href='profile.php?user={$userid}'>" . parseUsername($userid) . "</a> has used one Large Explosive from your guild's armory to sabotage district tile (" . resolveCoordinates($attack_to) .").");
        $api->GuildRemoveItem($ir['guild'], 62, 1);
    }
    else
    {
        echo "
        <div class='card'>
            <div class='card-body'>
                <div class='row'>
                    <div class='col-12'>
                        You are attempting to sabotage this tile. Please click the button to confirm.<br />
                		Your guild needs at least one <a href='iteminfo.php?ID=62'>Large Explosive</a> in 
                        its armory to sabotage another district. Success is not guaranteed.<br />
                        Sabotaging a tile may drop its fortification level or troop count.
                    </div>
                </div>
                <div class='row'>
                    <div class='col-12'>
                        <form method='post'>
                			<input type='hidden' name='warriors' value='true'>
                			<input type='submit' class='btn btn-success btn-block' value='Sabotage'>
                		</form>
                    </div>
                </div>
            </div>
        </div>";
    }
    
}
function attackfromtile()
{
	global $districtConfig, $userid, $db, $api, $h, $ir, $gdi, $gi;
	$attack_from = (isset($_GET['from']) && is_numeric($_GET['from'])) ? abs($_GET['from']) : '';
	$attack_to = (isset($_GET['to']) && is_numeric($_GET['to'])) ? abs($_GET['to']) : '';
	if (blockAccess($ir['guild']))
	{
		alert('danger',"Uh Oh!","You cannot attack while not in a guild.",true,'guild_district.php');
		die($h->endpage());
	}
	if (!isGuildLeaders($ir['guild'], $userid))
	{
		alert('danger',"Uh Oh!","You must be a guild leader or co-leader to coordinate attacks.",true,'guild_district.php');
		die($h->endpage());
	}
	if ($gdi['moves'] == 0)
	{
		alert('danger',"Uh Oh!","You have ran out of moves for today. Try again tomorrow.",true,'guild_district.php');
		die($h->endpage());
	}
	if (empty($attack_from))
	{
		alert('danger',"Uh Oh!","You are attempting to attack from an invalid district.",true,'guild_district.php');
		die($h->endpage());
	}
	if (empty($attack_to))
	{
		alert('danger',"Uh Oh!","You are attempting to attack an invalid district.",true,'guild_district.php');
		die($h->endpage());
	}
	$c=$db->query("SELECT * FROM `guild_districts` WHERE `district_id` = {$attack_from}");
	if ($db->num_rows($c) == 0)
	{
		alert('danger',"Uh Oh!","You are attempting to attack from a non-existent district.",true,'guild_district.php');
		die($h->endpage());
	}
	$c=$db->query("SELECT * FROM `guild_districts` WHERE `district_id` = {$attack_to}");
	if ($db->num_rows($c) == 0)
	{
		alert('danger',"Uh Oh!","You are attempting to attack a non-existent district.",true,'guild_district.php');
		die($h->endpage());
	}
	if (!isDistrictAccessible($attack_to))
	{
		alert('danger',"Uh Oh!","You do not have direct access to this tile. Please ensure that you're interacting with tiles on the outer border first, and then tiles adjacent to ones your guild owns.",true,'guild_district.php');
		die($h->endpage());
	}
	if (!isAccessibleFromTile($attack_from, $attack_to))
	{
		alert('danger',"Uh Oh!","You do not have direct access to this tile. Please ensure that you're interacting with tiles on the outer border first, and then tiles adjacent to ones your guild owns.",true,'guild_district.php');
		die($h->endpage());
	}
	if (isGuildDistrict($attack_to))
	{
		alert('danger',"Uh Oh!","You cannot attack a friendly district.",true,'guild_district.php');
		die($h->endpage());
	}
	$q=$db->query("SELECT * FROM `guild_districts` WHERE `district_id` = {$attack_from}");
	$q2=$db->query("SELECT * FROM `guild_districts` WHERE `district_id` = {$attack_to}");
	$r=$db->fetch_row($q);
	$r2=$db->fetch_row($q2);
	if (isset($_POST['warriors']))
	{
		$archers = (isset($_POST['archers']) && is_numeric($_POST['archers'])) ? abs($_POST['archers']) : 0;
		$warriors = (isset($_POST['warriors']) && is_numeric($_POST['warriors'])) ? abs($_POST['warriors']) : 0;
		$captains = (isset($_POST['captains']) && is_numeric($_POST['captains'])) ? abs($_POST['captains']) : 0;
		if ($warriors > $r['district_melee'])
		{
			alert('danger',"Uh Oh!","You do not have that many Warriors on that tile!",true,'guild_district.php');
			die($h->endpage());
		}
		if ($archers > $r['district_range'])
		{
			alert('danger',"Uh Oh!","You do not have that many Archers on that tile!",true,'guild_district.php');
			die($h->endpage());
		}
		if ($captains > $gdi['barracks_captains'])
		{
		    alert('danger',"Uh Oh!","You do not have that many Captains in your barracks!",true,'guild_district.php');
		    die($h->endpage());
		}
		if (($archers + $warriors) == 0)
		{
		    alert('danger',"Uh Oh!","You must attack with at least one unit.",true,'guild_district.php');
		    die($h->endpage());
		}
		if ($captains > 0)
		{
		    $costToCaptain = $captains * $districtConfig['CaptainCostUse'];
		    if ($gi['guild_primcurr'] < $costToCaptain)
		    {
		        alert('danger',"Uh Oh!","You need at least " . shortNumberParse($costToCaptain) . " Copper Coins in your vault before you can deploy that many captains.",true,'guild_district.php');
		        die($h->endpage());
		    }
		    else
		    {
		        $api->GuildRemoveCurrency($ir['guild'], "primary", $costToCaptain);
		    }
		}
		$attBuff = 1.0;
		$defBuff = 1.0;
		if ($r['district_type'] == 'elevated')
			$attBuff=$attBuff + 0.25;
		if ($r2['district_type'] == 'elevated')
			$defBuff=$defBuff + 0.25;
		if ($r2['district_type'] == 'lowered')
			$attBuff=$attBuff + 0.25;
		if ($r['district_type'] == 'lowered')
			$defBuff=$defBuff + 0.25;
		if ($r2['district_fortify'] > 0)
			$defBuff = $defBuff + ($r2['district_fortify'] * $districtConfig['fortifyBuffMulti']);
		if (guildOwnsAsset($ir['guild'], "guild_upgrade_district_trophy"))
		    $attBuff = $attBuff + 0.05;
	    if (guildOwnsAsset($r2['district_owner'], "guild_upgrade_district_trophy"))
	        $defBuff = $defBuff + 0.05;
		$results=json_decode(doAttack($warriors,$archers,$captains,$r2['district_melee'],$r2['district_range'], $r2['district_general'], $attBuff, $defBuff), true);
		updateTileTroops($r2['district_id'],
					$results['defense_warrior_lost']*-1,
					$results['defense_archer_lost']*-1,
					$results['defense_general_lost']*-1);
		updateTileTroops($r['district_id'],
					$results['attack_warrior_lost']*-1,
					$results['attack_archer_lost']*-1, 0);
		updateBarracksTroops($ir['guild'], 0, 0, 0, $results['attack_captain_lost']*-1);
		if ($results['winner'] == 'attack')
		{
			$status = "won";
			$winner = 'attacker';
		}
		elseif ($results['winner'] == 'defense')
		{
			$status = "lost";
			$winner = 'defender';
		}
		elseif ($results['winner'] == 'draw')
		{
		    $status = "draw";
		    $winner = '';
		}
		$i=newLogBattle($ir['guild'], $r2['district_owner'], $winner, $warriors, $archers, $results['attack_warrior_lost'],
		    $results['attack_archer_lost'], $captains, $results['attack_luck'], $r2['district_melee'], 
		    $r2['district_range'], $results['defense_warrior_lost'], $results['defense_archer_lost'], 
		    $r2['district_general'], $r2['district_fortify'], $results['defense_luck']);
		
		echo "You deploy " . shortNumberParse($warriors) . " Warriors and " . shortNumberParse($archers) . " Archers, lead by " . shortNumberParse($captains) . " Captains, to take on
		" . shortNumberParse($r2['district_melee']) . " Warriors and " . shortNumberParse($r2['district_range']) . " Archers and 
		" . shortNumberParse($r2['district_general']) . " enemy generals.<br />
		<b>Battle-log</b><br />
		<i>You {$status}!</i><br />
		Friendly Warriors Killed: " . shortNumberParse($results['attack_warrior_lost']) . "<br />
		Friendly Archers Killed: " . shortNumberParse($results['attack_archer_lost']) . "<br />
        Friendly Captains Executed: " . shortNumberParse($results['attack_captain_lost']) . "<br />
		Enemy Warriors Killed: " . shortNumberParse($results['defense_warrior_lost']) . "<br />
		Enemy Archers Killed: " . shortNumberParse($results['defense_archer_lost']) . "<br />
		Enemy Generals Executed: " . shortNumberParse($results['defense_general_lost']) . "<br />";
		$db->query("UPDATE `guild_district_info` SET `moves` = `moves` - 1 WHERE `guild_id` = {$ir['guild']}");
		if ($status == 'won')
		{
			$db->query("UPDATE `guild_district_info` SET `moves` = `moves` + 2 WHERE `guild_id` = {$ir['guild']}");
			echo "<i>This tile now belongs to your guild. Remember to move troops to this tile or it may be conquered from you.</i>";
			setTileOwnership($r2['district_id'], $ir['guild']);
			$api->GuildAddNotification($ir['guild'],"Your guild attacked district tile (" . resolveCoordinates($r2['district_id']) . ") and emerged victorious! View the battle report <a href='guild_district.php?action=viewreport&id={$i}'>here</a>.");
			$api->GuildAddNotification($r2['district_owner'],"A tile controlled by your guild (" . resolveCoordinates($r2['district_id']) . ") was attacked and lost.  View the battle report <a href='guild_district.php?action=viewreport&id={$i}'>here</a>.");
			$api->SystemLogsAdd($userid,"district","Conquered tile  " . resolveCoordinates($r2['district_id']) . " using " . shortNumberParse($warriors) . " Warriors (Lost " . shortNumberParse($results['attack_warrior_lost']) . ") and " . shortNumberParse($archers) . " Archers (Lost " . shortNumberParse($results['attack_archer_lost']) . "), launched from tile " . resolveCoordinates($r['district_id']) . ". Enemy lost " . shortNumberParse($results['defense_warrior_lost']) . " Warriors, " . shortNumberParse($results['defense_archer_lost']) . " Archers and " . shortNumberParse($results['defense_general_lost']) . " Generals.");
		}
		if ($status == 'lost')
		{
			echo "<i>You have failed to capture this tile. Rebuild your army and try again.</i>";
			$api->GuildAddNotification($ir['guild'],"Your guild attacked district tile (" . resolveCoordinates($r2['district_id']) . ") and lost! View the battle report <a href='guild_district.php?action=viewreport&id={$i}'>here</a>.");
			$api->GuildAddNotification($r2['district_owner'],"A tile controlled by your guild (" . resolveCoordinates($r2['district_id']) . ") was attacked and  and your guild emerged victorious. View the battle report <a href='guild_district.php?action=viewreport&id={$i}'>here</a>.");
			$api->SystemLogsAdd($userid,"district","Lost attacking tile  " . resolveCoordinates($r2['district_id']) . " using " . shortNumberParse($warriors) . " Warriors (Lost " . shortNumberParse($results['attack_warrior_lost']) . ") and " . shortNumberParse($archers) . " Archers (Lost " . shortNumberParse($results['attack_archer_lost']) . "), launched from tile " . resolveCoordinates($r2['district_id']) . " . Enemy lost " . shortNumberParse($results['defense_warrior_lost']) . " Warriors, " . shortNumberParse($results['defense_archer_lost']) . " Archers and " . shortNumberParse($results['defense_general_lost']) . " Generals.");
		}
	}
	else
	{
	    echo "<form method='post'>
        <div class='card'>
            <div class='card-body'>
                <div class='row'>
                    <div class='col-12'>
                        You are attempting to invade a district... Input how many units you wish to deploy to invade this tile. This tile has {$r2['district_general']} generals on defense.
                    </div>
                </div>
                <div class='row'>
                    <div class='col-12 col-md-6 col-xl-4'>
                        <div class='row'>
                            <div class='col-12'>
                                <b>Warriors (vs " . shortNumberParse($r2['district_melee']) . ")</b>
                            </div>
                            <div class='col-12'>
                                <input type='number' name='warriors' class='form-control' value='{$r['district_melee']}' max='{$r['district_melee']}' min='0'>
                            </div>
                        </div>
                    </div>
                    <div class='col-12 col-md-6 col-xl-4'>
                        <div class='row'>
                            <div class='col-12'>
                                <b>Archers (vs " . shortNumberParse($r2['district_range']) . ")</b>
                            </div>
                            <div class='col-12'>
                                <input type='number' name='archers' class='form-control' value='{$r['district_range']}' max='{$r['district_range']}' min='0'>
                            </div>
                        </div>
                    </div>
                    <div class='col-12 col-xl-4'>
                        <div class='row'>
                            <div class='col-12'>
                                <b>Captains</b>
                            </div>
                            <div class='col-12'>
                                <input type='number' name='captains' class='form-control' value='{$gdi['barracks_captains']}' max='{$gdi['barracks_captains']}' min='0'>
                            </div>
                        </div>
                    </div>
                    <div class='col-12'>
                        <div class='row'>
                            <div class='col-12'>
                                <br /><input type='submit' class='btn btn-success btn-block' value='Invade'>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        </form>";
	}
	
}
function attackfrombarracks()
{
	global $districtConfig, $userid, $db, $api, $h, $ir, $gdi, $gi;
	$attack_to = (isset($_GET['to']) && is_numeric($_GET['to'])) ? abs($_GET['to']) : '';
	if (blockAccess($ir['guild']))
	{
		alert('danger',"Uh Oh!","You cannot attack while not in a guild.",true,'guild_district.php');
		die($h->endpage());
	}
	if (!isGuildLeaders($ir['guild'], $userid))
	{
		alert('danger',"Uh Oh!","You must be a guild leader or co-leader to coordinate attacks.",true,'guild_district.php');
		die($h->endpage());
	}
	if ($gdi['moves'] == 0)
	{
		alert('danger',"Uh Oh!","You have ran out of moves for today. Try again tomorrow.",true,'guild_district.php');
		die($h->endpage());
	}
	if (empty($attack_to))
	{
		alert('danger',"Uh Oh!","You are attempting to attack an invalid district.",true,'guild_district.php');
		die($h->endpage());
	}
	$c=$db->query("SELECT * FROM `guild_districts` WHERE `district_id` = {$attack_to}");
	if ($db->num_rows($c) == 0)
	{
		alert('danger',"Uh Oh!","You are attempting to attack a non-existent district.",true,'guild_district.php');
		die($h->endpage());
	}
	if (!isDistrictAccessible($attack_to))
	{
		alert('danger',"Uh Oh!","You do not have direct access to this tile. Please ensure that you're interacting with tiles on the outer border first, and then tiles adjacent to ones your guild owns.",true,'guild_district.php');
		die($h->endpage());
	}
	if (isGuildDistrict($attack_to))
	{
		alert('danger',"Uh Oh!","You cannot attack a friendly district.",true,'guild_district.php');
		die($h->endpage());
	}
	$q2=$db->query("SELECT * FROM `guild_districts` WHERE `district_id` = {$attack_to}");
	$r2=$db->fetch_row($q2);
	if (isset($_POST['warriors']))
	{
		$archers = (isset($_POST['archers']) && is_numeric($_POST['archers'])) ? abs($_POST['archers']) : 0;
		$warriors = (isset($_POST['warriors']) && is_numeric($_POST['warriors'])) ? abs($_POST['warriors']) : 0;
		$captains = (isset($_POST['captains']) && is_numeric($_POST['captains'])) ? abs($_POST['captains']) : 0;
		if ($warriors > $gdi['barracks_warriors'])
		{
			alert('danger',"Uh Oh!","You do not have that many Warriors in your barracks!",true,'guild_district.php');
			die($h->endpage());
		}
		if ($archers > $gdi['barracks_archers'])
		{
			alert('danger',"Uh Oh!","You do not have that many Archers in your barracks!",true,'guild_district.php');
			die($h->endpage());
		}
		if ($captains > $gdi['barracks_captains'])
		{
		    alert('danger',"Uh Oh!","You do not have that many Captains in your barracks!",true,'guild_district.php');
		    die($h->endpage());
		}
		if (($archers + $warriors) == 0)
		{
			alert('danger',"Uh Oh!","You must attack with at least one unit.",true,'guild_district.php');
			die($h->endpage());
		}
		if ($captains > 0)
		{
		    $costToCaptain = $captains * $districtConfig['CaptainCostUse'];
		    if ($gi['guild_primcurr'] < $costToCaptain)
		    {
		        alert('danger',"Uh Oh!","You need at least " . shortNumberParse($costToCaptain) . " Copper Coins in your vault before you can deploy that many captains.",true,'guild_district.php');
		        die($h->endpage());
		    }
		    else
		    {
		         $api->GuildRemoveCurrency($ir['guild'], "primary", $costToCaptain);
		    }
		}
		$attBuff = 1.0;
		$defBuff = 1.0;
		if ($r2['district_type'] == 'elevated')
		{
			$defBuff=$defBuff + 0.25;
		}
		if ($r2['district_type'] == 'lowered')
		{
			$attBuff=$attBuff + 0.25;
		}
		if ($r2['district_fortify'] > 0)
		{
			$defBuff = $defBuff + ($r2['district_fortify'] * $districtConfig['fortifyBuffMulti']);
		}
		$results=json_decode(doAttack($warriors,$archers,$captains,$r2['district_melee'],$r2['district_range'], $r2['district_general'], $attBuff, $defBuff), true);
		updateTileTroops($r2['district_id'],
					$results['defense_warrior_lost']*-1,
					$results['defense_archer_lost']*-1,
					$results['defense_general_lost']*-1);
		updateBarracksTroops($ir['guild'],
					$results['attack_warrior_lost']*-1,
					$results['attack_archer_lost']*-1, 0,
		            $results['attack_captain_lost']*-1);
		if ($results['winner'] == 'attack')
		{
			$status = "won";
			$winner = 'attacker';
		}
		else
		{
			$status = "lost";
			$winner = 'defender';
		}
		$i=newLogBattle($ir['guild'], $r2['district_owner'], $winner, $warriors, $archers, $results['attack_warrior_lost'],
		    $results['attack_archer_lost'], $captains, $results['attack_luck'], $r2['district_melee'],
		    $r2['district_range'], $results['defense_warrior_lost'], $results['defense_archer_lost'],
		    $r2['district_general'], $r2['district_fortify'], $results['defense_luck']);
		
		echo "You deploy " . shortNumberParse($warriors) . " Warriors and " . shortNumberParse($archers) . " Archers, lead by " . shortNumberParse($captains) . " Captains, to take on 
		" . shortNumberParse($r2['district_melee']) . " Warriors and " . shortNumberParse($r2['district_range']) . " Archers. There is 
		" . shortNumberParse($r2['district_general']) . " enemy generals on the battlefield today.<br />
		<b>Battle-log</b><br />
		<i>You {$status}!</i><br />
		Friendly Warriors Killed: " . shortNumberParse($results['attack_warrior_lost']) . "<br />
		Friendly Archers Killed: " . shortNumberParse($results['attack_archer_lost']) . "<br />
        Friendly Captains Executed: " . shortNumberParse($results['attack_captain_lost']) . "<br />
		Enemy Warriors Killed: " . shortNumberParse($results['defense_warrior_lost']) . "<br />
		Enemy Archers Killed: " . shortNumberParse($results['defense_archer_lost']) . "<br />
		Enemy Generals Executed: " . shortNumberParse($results['defense_general_lost']) . "<br />";
		$db->query("UPDATE `guild_district_info` SET `moves` = `moves` - 1 WHERE `guild_id` = {$ir['guild']}");
		if ($status == 'won')
		{
			$db->query("UPDATE `guild_district_info` SET `moves` = `moves` + 2 WHERE `guild_id` = {$ir['guild']}");
			echo "<i>This tile now belongs to your guild. Remember to move troops to this tile or it may be conquered from you.</i>";
			setTileOwnership($r2['district_id'], $ir['guild']);
			$api->GuildAddNotification($ir['guild'],"Your guild attacked a tile (" . resolveCoordinates($r2['district_id']) . ") and emerged victorious! View the battle report <a href='guild_district.php?action=viewreport&id={$i}'>here</a>.");
			$api->GuildAddNotification($r2['district_owner'],"Your guild's tile (" . resolveCoordinates($r2['district_id']) . ") attacked and lost. View the battle report <a href='guild_district.php?action=viewreport&id={$i}'>here</a>.");
			$api->SystemLogsAdd($userid,"district","Conquered tile  " . resolveCoordinates($r2['district_id']) . " using " . shortNumberParse($warriors) . " Warriors (Lost " . shortNumberParse($results['attack_warrior_lost']) . ") and " . shortNumberParse($archers) . " Archers (Lost " . shortNumberParse($results['attack_archer_lost']) . "), launched from tile " . resolveCoordinates($r2['district_id']) . ". Enemy lost " . shortNumberParse($results['defense_warrior_lost']) . " Warriors, " . shortNumberParse($results['defense_archer_lost']) . " Archers and " . shortNumberParse($results['defense_general_lost']) . " Generals.");
		}
		if ($status == 'lost')
		{
			echo "<i>You have failed to capture this tile. Rebuild your army and try again.</i>";
			$api->GuildAddNotification($ir['guild'],"Your guild attacked a tile (" . resolveCoordinates($r2['district_id']) . ") and lost! View the battle report <a href='guild_district.php?action=viewreport&id={$i}'>here</a>.");
			$api->GuildAddNotification($r2['district_owner'],"Your guild's tile (" . resolveCoordinates($r2['district_id']) . ") was attacked and your guild emerged victorious. View the battle report <a href='guild_district.php?action=viewreport&id={$i}'>here</a>.");
			$api->SystemLogsAdd($userid,"district","Lost attacking tile  " . resolveCoordinates($r2['district_id']) . " using " . shortNumberParse($warriors) . " Warriors (Lost " . shortNumberParse($results['attack_warrior_lost']) . ") and " . shortNumberParse($archers) . " Archers (Lost " . shortNumberParse($results['attack_archer_lost']) . "), launched from tile " . resolveCoordinates($r2['district_id']) . " . Enemy lost " . shortNumberParse($results['defense_warrior_lost']) . " Warriors, " . shortNumberParse($results['defense_archer_lost']) . " Archers and " . shortNumberParse($results['defense_general_lost']) . " Generals.");
		}
	}
	else
	{
	    echo "<form method='post'>
        <div class='card'>
            <div class='card-body'>
                <div class='row'>
                    <div class='col-12'>
                        You are attempting to invade a district... Input how many units you wish to deploy to invade this tile. This tile has {$r2['district_general']} generals on defense.
                    </div>
                </div>
                <div class='row'>
                    <div class='col-12 col-md-6 col-xl-4'>
                        <div class='row'>
                            <div class='col-12'>
                                <b>Warriors (vs " . shortNumberParse($r2['district_melee']) . ")</b>
                            </div>
                            <div class='col-12'>
                                <input type='number' name='warriors' class='form-control' value='{$gdi['barracks_warriors']}' max='{$gdi['barracks_warriors']}' min='0'>
                            </div>
                        </div>
                    </div>
                    <div class='col-12 col-md-6 col-xl-4'>
                        <div class='row'>
                            <div class='col-12'>
                                <b>Archers (vs " . shortNumberParse($r2['district_range']) . ")</b>
                            </div>
                            <div class='col-12'>
                                <input type='number' name='archers' class='form-control' value='{$gdi['barracks_archers']}' max='{$gdi['barracks_archers']}' min='0'>
                            </div>
                        </div>
                    </div>
                    <div class='col-12 col-md-6 col-xl-4'>
                        <div class='row'>
                            <div class='col-12'>
                                <b>Captains</b>
                            </div>
                            <div class='col-12'>
                                <input type='number' name='captains' class='form-control' value='{$gdi['barracks_captains']}' max='{$gdi['barracks_captains']}' min='0'>
                            </div>
                        </div>
                    </div>
                    <div class='col-12'>
                        <div class='row'>
                            <div class='col-12'>
                                <br /><input type='submit' class='btn btn-success btn-block' value='Invade'>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        </form>";
	}
	
}
function battlereport()
{
	global $db, $api, $h, $ir, $gdi, $districtConfig;
	$id = (isset($_GET['id']) && is_numeric($_GET['id'])) ? abs(intval($_GET['id'])) : 0;
	if (blockAccess($ir['guild']))
	{
		alert('danger',"Uh Oh!","You cannot attack while not in a guild.",true,'guild_district.php');
		die($h->endpage());
	}
	if (empty($id)) 
	{
		alert('danger', "Uh Oh!", "Please specify the battle log you wish to view.", true, 'guild_district.php');
		die($h->endpage());
	}
	$q = $db->query("/*qc=on*/SELECT * FROM `guild_district_attacklogs` WHERE `log_ig` = {$id} AND (`attacker` = {$ir['guild']} OR `defender` = {$ir['guild']})");
	if ($db->num_rows($q) == 0) 
	{
		alert('danger', "Uh Oh!", "This battle report does not exist, or does not involve your guild.", true, 'index.php');
		die($h->endpage());
	}
	$r = $db->fetch_row($q);
	$result = json_decode($r['battle_json_info'], true);
	$db->free_result($q);
	$def=$api->GuildFetchInfo($r['defender'],'guild_name');
	if (empty($def))
	    $def = "N/A";
	echo "
    <div class='card'>
        <div class='card-body'>
        	<div class='row'>
        		<div class='col-md'>
        			Attacker: <a href='guilds.php?action=view&id={$r['attacker']}'>{$api->GuildFetchInfo($r['attacker'],'guild_name')}</a><br />
        			Warriors: " . shortNumberParse($result['attack_warrior']) . " <span class='text-danger'>(-" . shortNumberParse($result['attack_warrior_lost']) . ")</span><br />
        			Archers: " . shortNumberParse($result['attack_archer']) . " <span class='text-danger'>(-" . shortNumberParse($result['attack_archer_lost']) . ")</span><br />
                    Captains: " . shortNumberParse($result['attack_captain']) . "<br />
                    Luck: {$result['attack_luck']}<br />
                    Captain Cost: " . shortNumberParse($result['attack_captain'] * $districtConfig['CaptainCostUse']) . " Copper Coins<br />
        			Time: " . DateTime_Parse($r['time']) . "<br />";
                	if ($r['winner'] == 'defender')
                	{
                	    if ($result['attack_captain'] > 0)
                	        echo "<span class='text-danger'>Captains have been executed.</span>";
                	}
	echo "
        		</div>
        		<div class='col-md'>
        			Defender: <a href='guilds.php?action=view&id={$r['defender']}'>{$def}</a><br />
        			Warriors: " . shortNumberParse($result['defense_warrior']) . " <span class='text-danger'>(-" . shortNumberParse($result['defense_warrior_lost']) . ")</span><br />
        			Archers: " . shortNumberParse($result['defense_archer']) . " <span class='text-danger'>(-" . shortNumberParse($result['defense_archer_lost']) . ")</span><br />
        			Generals: " . shortNumberParse($result['defense_general']) . "<br />
                    Luck: {$result['defense_luck']}<br />
        			Fortification Level: " . ($result['defense_fortify']) . "<br />";
                    if ($r['winner'] == 'attacker')
                    {
                        if ($result['defense_general'] > 0)
                            echo "<span class='text-danger'>Generals have been executed.</span>";
                    }
                    echo "
        		</div>
        	</div>
        </div>
	</div>";
}
function movefromtile()
{
	global $userid, $db, $api, $h, $ir, $gdi;
	$attack_from = (isset($_GET['from']) && is_numeric($_GET['from'])) ? abs($_GET['from']) : '';
	$attack_to = (isset($_GET['to']) && is_numeric($_GET['to'])) ? abs($_GET['to']) : '';
	if (blockAccess($ir['guild']))
	{
		alert('danger',"Uh Oh!","You cannot move units while not in a guild.",true,'guild_district.php');
		die($h->endpage());
	}
	if (!isGuildLeaders($ir['guild'], $userid))
	{
		alert('danger',"Uh Oh!","You must be a guild leader or co-leader to move troops.",true,'guild_district.php');
		die($h->endpage());
	}
	if ($gdi['moves'] == 0)
	{
		alert('danger',"Uh Oh!","You have ran out of moves for today. Try again tomorrow.",true,'guild_district.php');
		die($h->endpage());
	}
	if (empty($attack_from))
	{
		alert('danger',"Uh Oh!","You are attempting to interact from an invalid district.",true,'guild_district.php');
		die($h->endpage());
	}
	if (empty($attack_to))
	{
		alert('danger',"Uh Oh!","You are attempting to interact with an invalid district.",true,'guild_district.php');
		die($h->endpage());
	}
	$c=$db->query("SELECT * FROM `guild_districts` WHERE `district_id` = {$attack_from}");
	if ($db->num_rows($c) == 0)
	{
		alert('danger',"Uh Oh!","You are attempting to interact from a non-existent district.",true,'guild_district.php');
		die($h->endpage());
	}
	$c=$db->query("SELECT * FROM `guild_districts` WHERE `district_id` = {$attack_to}");
	if ($db->num_rows($c) == 0)
	{
		alert('danger',"Uh Oh!","You are attempting to interact with a non-existent district.",true,'guild_district.php');
		die($h->endpage());
	}
	if (!isDistrictAccessible($attack_to))
	{
		alert('danger',"Uh Oh!","You do not have direct access to this tile. Please ensure that you're interacting with tiles on the outer border first, and then tiles adjacent to ones your guild owns.",true,'guild_district.php');
		die($h->endpage());
	}
	if (!isAccessibleFromTile($attack_from, $attack_to))
	{
		alert('danger',"Uh Oh!","You do not have direct access to this tile. Please ensure that you're interacting with tiles on the outer border first, and then tiles adjacent to ones your guild owns.",true,'guild_district.php');
		die($h->endpage());
	}
	$q=$db->query("SELECT * FROM `guild_districts` WHERE `district_id` = {$attack_from}");
	$q2=$db->query("SELECT * FROM `guild_districts` WHERE `district_id` = {$attack_to}");
	$r=$db->fetch_row($q);
	$r2=$db->fetch_row($q2);
	if (isset($_POST['warriors']))
	{
		$archers = (isset($_POST['archers']) && is_numeric($_POST['archers'])) ? abs($_POST['archers']) : 0;
		$warriors = (isset($_POST['warriors']) && is_numeric($_POST['warriors'])) ? abs($_POST['warriors']) : 0;
		$generals = (isset($_POST['generals']) && is_numeric($_POST['generals'])) ? abs($_POST['generals']) : 0;
		if ($warriors > $r2['district_melee'])
		{
			alert('danger',"Uh Oh!","You do not have that many Warriors on that tile!",true,'guild_district.php');
			die($h->endpage());
		}
		if ($archers > $r2['district_range'])
		{
			alert('danger',"Uh Oh!","You do not have that many Archers on that tile!",true,'guild_district.php');
			die($h->endpage());
		}
		if ($generals > $r2['district_general'])
		{
			alert('danger',"Uh Oh!","You do not have that many Generals on that tile!",true,'guild_district.php');
			die($h->endpage());
		}
		if (($archers + $warriors + $generals) == 0)
		{
			alert('danger',"Uh Oh!","You must move at least one unit.",true,'guild_district.php');
			die($h->endpage());
		}
		/*if ($generals > 0)
		{
			if (($generals + $r['district_general']) > $districtConfig['maxGenerals'])
			{
				alert('danger',"Uh Oh!","You may only have {$districtConfig['maxGenerals']} Generals on a tile.",true,'guild_district.php');
				die($h->endpage());
			}
		}*/
		$db->query("UPDATE `guild_district_info` SET `moves` = `moves` - 1 WHERE `guild_id` = {$ir['guild']}");
		$api->SystemLogsAdd($userid,"district","Moved " . shortNumberParse($warriors) . " Warriors, " . shortNumberParse($archers) . " Archers and  " . shortNumberParse($generals) . " Generals from Tile " . resolveCoordinates($r2['district_id']) . " to Tile " . resolveCoordinates($r['district_id']) .".");
		updateTileTroops($r2['district_id'], $warriors*-1, $archers*-1, $generals*-1);
		updateTileTroops($r['district_id'], $warriors, $archers, $generals);
		alert('success',"","You have successfully moved " . shortNumberParse($warriors) . " Warriors, 
                " . shortNumberParse($archers) . " Archers and " . shortNumberParse($generals) . " Generals to (" . resolveCoordinates($r['district_id']) . ") from (" . resolveCoordinates($r2['district_id']) . ").");
	}
	else
	{
	    echo "<form method='post'>
        <div class='card'>
            <div class='card-body'>
                <div class='row'>
                    <div class='col-12'>
                        You are attempting to move units from one district to another. This will cost you one movement. Please enter how many units you wish to send 
		                  to the receiving district.
                    </div>
                </div>
                <div class='row'>
                    <div class='col-12 col-xl-4'>
                        <div class='row'>
                            <div class='col-12'>
                                <b>Warriors (Have " . shortNumberParse($r2['district_melee']) . ")</b>
                            </div>
                            <div class='col-12'>
                                <input type='number' name='warriors' class='form-control' value='{$r2['district_melee']}' max='{$r2['district_melee']}' min='0'>
                            </div>
                        </div>
                    </div>
                    <div class='col-12 col-xl-4'>
                        <div class='row'>
                            <div class='col-12'>
                                <b>Archers (Have " . shortNumberParse($r2['district_range']) . ")</b>
                            </div>
                            <div class='col-12'>
                                <input type='number' name='archers' class='form-control' value='{$r2['district_range']}' max='{$r2['district_range']}' min='0'>
                            </div>
                        </div>
                    </div>
                    <div class='col-12 col-xl-4'>
                        <div class='row'>
                            <div class='col-12'>
                                <b>Generals (Have " . shortNumberParse($r2['district_general']) . ")</b>
                            </div>
                            <div class='col-12'>
                                <input type='number' name='generals' class='form-control' value='{$r2['district_general']}' max='{$r2['district_general']}' min='0'>
                            </div>
                        </div>
                    </div>
                    <div class='col-12'>
                        <div class='row'>
                            <div class='col-12'>
                                <br /><input type='submit' class='btn btn-success btn-block' value='Move Units'>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        </form>";
	}
	
}
function fortify()
{
	global $districtConfig, $userid, $db, $api, $h, $ir, $gdi;
	$attack_to = (isset($_GET['id']) && is_numeric($_GET['id'])) ? abs($_GET['id']) : '';
	if (blockAccess($ir['guild']))
	{
		alert('danger',"Uh Oh!","You cannot fortify a tile while not in a guild.",true,'guild_district.php');
		die($h->endpage());
	}
	if (!isGuildLeaders($ir['guild'], $userid))
	{
		alert('danger',"Uh Oh!","You must be a guild leader or co-leader to fortify tiles.",true,'guild_district.php');
		die($h->endpage());
	}
	if (empty($attack_to))
	{
		alert('danger',"Uh Oh!","You are attempting to interact with an invalid district.",true,'guild_district.php');
		die($h->endpage());
	}
	$c=$db->query("SELECT * FROM `guild_districts` WHERE `district_id` = {$attack_to}");
	if ($db->num_rows($c) == 0)
	{
		alert('danger',"Uh Oh!","You are attempting to interact with a non-existent district.",true,'guild_district.php');
		die($h->endpage());
	}
	if (!isDistrictAccessible($attack_to))
	{
		alert('danger',"Uh Oh!","You do not have direct access to this tile. Please ensure that you're interacting with tiles on the outer border first, and then tiles adjacent to ones your guild owns.",true,'guild_district.php');
		die($h->endpage());
	}
	if ($gdi['moves'] == 0)
	{
	    alert('danger',"Uh Oh!","You have ran out of moves for today. Try again tomorrow.",true,'guild_district.php');
	    die($h->endpage());
	}
	$q2=$db->query("SELECT * FROM `guild_districts` WHERE `district_id` = {$attack_to}");
	$r2=$db->fetch_row($q2);
	$neededTokens=$districtConfig['copperPerFortify']*($r2['district_fortify'] + 1);
	$guildcurr = $db->fetch_single($db->query("SELECT `guild_seccurr` FROM `guild` WHERE `guild_id` = {$ir['guild']}"));
	$neededXP = round($districtConfig['xpPerFortify'] * (($r2['district_fortify'] + 1) * $districtConfig['xpPerFortifyMulti']));
	if (isset($_POST['warriors']))
	{
	    $newLvl = ($r2['district_fortify'] + 1);
	    if ($newLvl > $districtConfig['maxFortify'])
		{
			alert('danger',"Uh Oh!","You cannot fortify this tile anymore.",true,'guild_district.php');
			die($h->endpage());
		}
		if ($guildcurr < $neededTokens)
		{
		    alert('danger',"Uh Oh!","Your guild needs " . shortNumberParse($neededTokens) . " Chivalry Tokens before you can fortify this district. Your guild only has " . shortNumberParse($guildcurr) . " Chivalry Tokens in its vault.",true,'guild_district.php');
			die($h->endpage());
		}
		if (!($api->GuildHasXP($ir['guild'], $neededXP)))
		{
		    alert('danger',"Uh Oh!","Your guild needs " . shortNumberParse($neededXP) . " Guild Experience before you can fortify this district.",true,'guild_district.php');
			die($h->endpage());
		}
		if ($newLvl >= 3)
		{
		    if ($newLvl < 4)
		    {
		        if ($r2['district_general'] < 1)
		        {
		            alert('danger',"Uh Oh!","Your guild must have at least one general on this tile to fortify any further.",true,'guild_district.php');
		            die($h->endpage());
		        }
		    }
		    else 
		    {
		        if ($r2['district_general'] < 2)
		        {
		            alert('danger',"Uh Oh!","Your guild must have at least two generals on this tile to fortify any further.",true,'guild_district.php');
		            die($h->endpage());
		        }
		    }
		}
		$db->query("UPDATE `guild_district_info` SET `moves` = `moves` - 1 WHERE `guild_id` = {$ir['guild']}");
		$api->SystemLogsAdd($userid,"district","Spent " . shortNumberParse($neededXP) . " Guild Experience and " . shortNumberParse($neededTokens) . " Chivalry Tokens to fortify tile " . resolveCoordinates($attack_to) .".");
		$api->GuildRemoveXP($ir['guild'],$neededXP);
		$db->query("UPDATE `guild` SET `guild_seccurr` = `guild_seccurr` - {$neededTokens} WHERE `guild_id` = {$ir['guild']}");
		addToEconomyLog('Districts','token', $neededTokens*-1);
		$db->query("UPDATE `guild_districts` SET `district_fortify` = `district_fortify` + 1 WHERE `district_id` = {$attack_to}");
		alert('success',"","You have successfully fortified this tile at the cost of " . shortNumberParse($neededXP) . " Guild XP and " . shortNumberParse($neededTokens) . " Chivalry Tokens.",true,'guild_district.php');
	   $api->GuildAddNotification($ir['guild'], "Your guild has spent " . shortNumberParse($neededTokens) . " Chivalry Tokens and " . shortNumberParse($neededXP) . " Guild Experience to fortify district tile (" . resolveCoordinates($attack_to) .").");
	}
	else
	{
		echo "
        <div class='card'>
            <div class='card-body'>
                <div class='row'>
                    <div class='col-12'>
                        You are attempting to fortify this tile. Please click the button to confirm.<br />
                		For this district, you will need " . shortNumberParse($neededXP) . " Guild XP and " . shortNumberParse($neededTokens) . " 
                        Chivalry Tokens. This is taken from your guild's vault.<br />
                		Districts may be fortified up to a maximum of {$districtConfig['maxFortify']} times.<br />
                        Fortifying will also cost your guild one movement.
                    </div>
                </div>
                <div class='row'>
                    <div class='col-12'>
                        <form method='post'>
                			<input type='hidden' name='warriors' value='true'>
                			<input type='submit' class='btn btn-success btn-block' value='Fortify'>
                		</form>
                    </div>
                </div>
            </div>
        </div>";
	}
	
}
function movefrombarracks()
{
	global $userid, $db, $api, $h, $ir, $gdi;
	$attack_to = (isset($_GET['id']) && is_numeric($_GET['id'])) ? abs($_GET['id']) : '';
	if (blockAccess($ir['guild']))
	{
		alert('danger',"Uh Oh!","You cannot move units while not in a guild.",true,'guild_district.php');
		die($h->endpage());
	}
	if (!isGuildLeaders($ir['guild'], $userid))
	{
		alert('danger',"Uh Oh!","You must be a guild leader or co-leader to coordinate attacks.",true,'guild_district.php');
		die($h->endpage());
	}
	if ($gdi['moves'] == 0)
	{
		alert('danger',"Uh Oh!","Your guild has ran out of moves for today. Try again tomorrow.",true,'guild_district.php');
		die($h->endpage());
	}
	if (empty($attack_to))
	{
		alert('danger',"Uh Oh!","You are attempting to interact with an invalid district.",true,'guild_district.php');
		die($h->endpage());
	}
	$c=$db->query("SELECT * FROM `guild_districts` WHERE `district_id` = {$attack_to}");
	if ($db->num_rows($c) == 0)
	{
		alert('danger',"Uh Oh!","You are attempting to interact with a non-existent district.",true,'guild_district.php');
		die($h->endpage());
	}
	if (!isDistrictAccessible($attack_to))
	{
		alert('danger',"Uh Oh!","You do not have direct access to this tile. Please ensure that you're interacting with tiles on the outer border first, and then tiles adjacent to ones your guild owns.",true,'guild_district.php');
		die($h->endpage());
	}
	$q2=$db->query("SELECT * FROM `guild_districts` WHERE `district_id` = {$attack_to}");
	$r2=$db->fetch_row($q2);
	if (isset($_POST['warriors']))
	{
		$archers = (isset($_POST['archers']) && is_numeric($_POST['archers'])) ? abs($_POST['archers']) : 0;
		$warriors = (isset($_POST['warriors']) && is_numeric($_POST['warriors'])) ? abs($_POST['warriors']) : 0;
		$generals = (isset($_POST['generals']) && is_numeric($_POST['generals'])) ? abs($_POST['generals']) : 0;
		if ($warriors > $gdi['barracks_warriors'])
		{
			alert('danger',"Uh Oh!","You do not have that many Warriors in your barracks!",true,'guild_district.php');
			die($h->endpage());
		}
		if ($archers > $gdi['barracks_archers'])
		{
			alert('danger',"Uh Oh!","You do not have that many Archers in your barracks!",true,'guild_district.php');
			die($h->endpage());
		}
		if ($generals > $gdi['barracks_generals'])
		{
			alert('danger',"Uh Oh!","You do not have that many Generals in your barracks!",true,'guild_district.php');
			die($h->endpage());
		}
		if (($archers + $warriors + $generals) == 0)
		{
			alert('danger',"Uh Oh!","You must move at least one unit.",true,'guild_district.php');
			die($h->endpage());
		}
		/*if (($generals + $r2['district_general']) > $districtConfig['maxGenerals'])
		{
			alert('danger',"Uh Oh!","You may only have {$districtConfig['maxGenerals']} Generals on a tile.",true,'guild_district.php');
			die($h->endpage());
		}*/
		$db->query("UPDATE `guild_district_info` SET `moves` = `moves` - 1 WHERE `guild_id` = {$ir['guild']}");
		updateBarracksTroops($ir['guild'], $warriors*-1, $archers*-1, $generals*-1, 0);
		updateTileTroops($attack_to, $warriors, $archers, $generals);
		alert('success',"","You have successfully moved " . shortNumberParse($warriors) . " Warriors, " . shortNumberParse($archers) . " Archers and " . shortNumberParse($generals) . " Generals from your barracks to this tile.", true, 'guild_district.php');
		$api->SystemLogsAdd($userid,"district","Moved " . shortNumberParse($warriors) . " Warriors and " . shortNumberParse($archers) . " Archers to Tile " . resolveCoordinates($attack_to) . " from their barracks.");
		$api->GuildAddNotification($ir['guild'], "<a href='profile.php?user=1'>{$ir['username']}</a> has moved " . shortNumberParse($warriors) . " Warriors, " . shortNumberParse($archers) . " Archers and " . shortNumberParse($generals) . " Generals to Tile " . resolveCoordinates($attack_to) . " from the barracks.");
	}
	else
	{
	    echo "<form method='post'>
        <div class='card'>
            <div class='card-body'>
                <div class='row'>
                    <div class='col-12'>
                        You are attempting to deploy units from your barracks to the battlefield. This will cost you one movement. Units cannot be returned to the barracks after being deployed. Please enter how many units you wish to send 
		to the receiving district.
                    </div>
                </div>
                <div class='row'>
                    <div class='col-12 col-xl-4'>
                        <div class='row'>
                            <div class='col-12'>
                                <b>Warriors (Have " . shortNumberParse($gdi['barracks_warriors']) . ")</b>
                            </div>
                            <div class='col-12'>
                                <input type='number' name='warriors' class='form-control' value='{$gdi['barracks_warriors']}' max='{$gdi['barracks_warriors']}' min='0'>
                            </div>
                        </div>
                    </div>
                    <div class='col-12 col-xl-4'>
                        <div class='row'>
                            <div class='col-12'>
                                <b>Archers (Have " . shortNumberParse($gdi['barracks_archers']) . ")</b>
                            </div>
                            <div class='col-12'>
                                <input type='number' name='archers' class='form-control' value='{$gdi['barracks_archers']}' max='{$gdi['barracks_archers']}' min='0'>
                            </div>
                        </div>
                    </div>
                    <div class='col-12 col-xl-4'>
                        <div class='row'>
                            <div class='col-12'>
                                <b>Generals (Have " . shortNumberParse($gdi['barracks_generals']) . ")</b>
                            </div>
                            <div class='col-12'>
                                <input type='number' name='generals' class='form-control' value='{$gdi['barracks_generals']}' max='{$gdi['barracks_generals']}' min='0'>
                            </div>
                        </div>
                    </div>
                    <div class='col-12'>
                        <div class='row'>
                            <div class='col-12'>
                                <br /><input type='submit' class='btn btn-success btn-block' value='Move Units'>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        </form>";
	}
	
}

function guild_info()
{
	global $db, $api, $userid, $ir, $gdi, $districtConfig;
	if (blockAccess($ir['guild']))
	{
		alert('danger',"Uh Oh!","You cannot view information about your guild if you're not in a guild.",true,'guild_district.php');
		die($h->endpage());
	}
	$districtOwn=countOwnedDistricts($gdi['guild_id']);
	$warriorCost = countDeployedWarriors($gdi['guild_id']) * $districtConfig['WarriorCostDaily'];
	$archerCost = countDeployedArchers($gdi['guild_id']) * $districtConfig['ArcherCostDaily'];
	$generalCost = countDeployedGenerals($gdi['guild_id']) * $districtConfig['GeneralCostDaily'];
	$tileCost = countOwnedDistricts($gdi['guild_id']) * $districtConfig['upkeepPerTile'];
	$totalDailyCost = $generalCost + $archerCost + $warriorCost + $tileCost;
	echo "
    <div class='row'>
        <div class='col-12 col-md-6 col-xxl-4 col-xxxl-3'>
            <div class='card'>
                <div class='card-header'>
                    <b>Barracks</b>
                </div>
                <div class='card-body'>
                    <div class='row'>
                        <div class='col-auto'>
                            <div class='row'>
                                <div class='col-12'>
                                    <small><b>Warriors</b></small>
                                </div>
                                <div class='col-12'>
                                    " . shortNumberParse($gdi['barracks_warriors']) . "
                                </div>
                            </div>
                        </div>
                        <div class='col-auto'>
                            <div class='row'>
                                <div class='col-12'>
                                    <small><b>Archers</b></small>
                                </div>
                                <div class='col-12'>
                                    " . shortNumberParse($gdi['barracks_archers']) . "
                                </div>
                            </div>
                        </div>
                        <div class='col-auto'>
                            <div class='row'>
                                <div class='col-12'>
                                    <small><b>Generals</b></small>
                                </div>
                                <div class='col-12'>
                                    " . shortNumberParse($gdi['barracks_generals']) . "
                                </div>
                            </div>
                        </div>
                        <div class='col-auto'>
                            <div class='row'>
                                <div class='col-12'>
                                    <small><b>Captains</b></small>
                                </div>
                                <div class='col-12'>
                                    " . shortNumberParse($gdi['barracks_captains']) . "
                                </div>
                            </div>
                        </div>
                        <div class='col-12'>
                            <span class='text-muted'><i><small>Troops in the barracks don't count towards daily upkeep.</small></i></span>
                        </div>
                    </div>
                </div>
            </div>
            <br />
        </div>
        <div class='col-12 col-md-6 col-xxl-4 col-xxxl-3'>
            <div class='card'>
                <div class='card-header'>
                    <b>Upkeep (Copper Coins)</b>
                </div>
                <div class='card-body'>
                    <div class='row'>
                        <div class='col-auto'>
                            <div class='row'>
                                <div class='col-12'>
                                    <small><b>Warriors</b></small>
                                </div>
                                <div class='col-12'>
                                    " . shortNumberParse($warriorCost) . "
                                </div>
                            </div>
                        </div>
                        <div class='col-auto'>
                            <div class='row'>
                                <div class='col-12'>
                                    <small><b>Archers</b></small>
                                </div>
                                <div class='col-12'>
                                    " . shortNumberParse($archerCost) . "
                                </div>
                            </div>
                        </div>
                        <div class='col-auto'>
                            <div class='row'>
                                <div class='col-12'>
                                    <small><b>Generals</b></small>
                                </div>
                                <div class='col-12'>
                                    " . shortNumberParse($generalCost) . "
                                </div>
                            </div>
                        </div>
                        <div class='col-auto'>
                            <div class='row'>
                                <div class='col-12'>
                                    <small><b>Captains</b></small>
                                </div>
                                <div class='col-12'>
                                    " . shortNumberParse(0) . "
                                </div>
                            </div>
                        </div>
                        <div class='col-auto'>
                            <div class='row'>
                                <div class='col-12'>
                                    <small><b>Tile Upkeep</b></small>
                                </div>
                                <div class='col-12'>
                                    " . shortNumberParse($tileCost) . "
                                </div>
                            </div>
                        </div>
                        <div class='col-auto'>
                            <div class='row'>
                                <div class='col-12'>
                                    <small><b>Total Costs</b></small>
                                </div>
                                <div class='col-12'>
                                    " . shortNumberParse($totalDailyCost) . "
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <br />
        </div>
        <div class='col-12 col-md-6 col-xxl-4 col-xxxl-3'>
            <div class='card'>
                <div class='card-header'>
                    Active Troops
                </div>
                <div class='card-body'>
                    <div class='row'>
                        <div class='col-auto'>
                            <div class='row'>
                                <div class='col-12'>
                                    <small><b>Warriors</b></small>
                                </div>
                                <div class='col-12'>
                                    " . shortNumberParse(countDeployedWarriors($gdi['guild_id'])) . "
                                </div>
                            </div>
                        </div>
                        <div class='col-auto'>
                            <div class='row'>
                                <div class='col-12'>
                                    <small><b>Archers</b></small>
                                </div>
                                <div class='col-12'>
                                    " . shortNumberParse(countDeployedArchers($gdi['guild_id'])) . "
                                </div>
                            </div>
                        </div>
                        <div class='col-auto'>
                            <div class='row'>
                                <div class='col-12'>
                                    <small><b>Generals</b></small>
                                </div>
                                <div class='col-12'>
                                    " . shortNumberParse(countDeployedGenerals($gdi['guild_id'])) . "
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <br />
        </div>
        <div class='col-12 col-md-6 col-xxl-4 col-xxxl-3'>
            <div class='card'>
                <div class='card-header'>
                    Daily Info
                </div>
                <div class='card-body'>
                    <div class='row'>
                        <div class='col-auto'>
                            <div class='row'>
                                <div class='col-12'>
                                    <small><b>Moves Available</b></small>
                                </div>
                                <div class='col-12'>
                                    " . shortNumberParse($gdi['moves']) . "
                                </div>
                            </div>
                        </div>
                        <div class='col-auto'>
                            <div class='row'>
                                <div class='col-12'>
                                    <small><b>Tiles Controlled</b></small>
                                </div>
                                <div class='col-12'>
                                    " . shortNumberParse($districtOwn) . "
                                </div>
                            </div>
                        </div>
                        <div class='col-auto'>
                            <div class='row'>
                                <div class='col-12'>
                                    <small><b>Warriors Bought</b></small>
                                </div>
                                <div class='col-12'>
                                    " . shortNumberParse($gdi['warriors_bought']) . "
                                </div>
                            </div>
                        </div>
                        <div class='col-auto'>
                            <div class='row'>
                                <div class='col-12'>
                                    <small><b>Archerss Bought</b></small>
                                </div>
                                <div class='col-12'>
                                    " . shortNumberParse($gdi['archers_bought']) . "
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <br />
        </div>
    </div>";
}

function guild_buy()
{
	global $db, $api, $userid, $ir, $gdi, $districtConfig, $h, $gi;
	if (blockAccess($ir['guild']))
	{
		alert('danger',"Uh Oh!","You cannot purchase units while not in a guild.",true,'guild_district.php');
		die($h->endpage());
	}
	if (!isGuildLeaders($ir['guild'], $userid))
	{
		alert('danger',"Uh Oh!","You must be a guild leader or co-leader to purchase troops.",true,'guild_district.php');
		die($h->endpage());
	}
	if (countTowns() > 0)
	{
	    $districtConfig['ArcherCost'] = round($districtConfig['ArcherCost'] - ($districtConfig['ArcherCost']*($districtConfig['townLessCost']*countTowns())));
	    $districtConfig['WarriorCost'] = round($districtConfig['WarriorCost'] - ($districtConfig['WarriorCost']*($districtConfig['townLessCost']*countTowns())));
	}
	$maxDailyWarriors = 400;
	$maxDailyArchers = $maxDailyWarriors / 2;
	
	$dailyBuyWarriors = $gi['guild_level'] * 20;
	$dailyBuyArchers = $gi['guild_level'] * 10;
	
	if ($dailyBuyArchers > $maxDailyArchers)
		$dailyBuyArchers = $maxDailyArchers;
	if ($dailyBuyWarriors > $maxDailyWarriors)
		$dailyBuyWarriors = $maxDailyWarriors;
	
	if (countOutposts() > 0)
	{
	    $dailyBuyWarriors = round($dailyBuyWarriors + ($dailyBuyWarriors * ($districtConfig['outpostExtraTroops'] * countOutposts())));
	    $dailyBuyArchers = round($dailyBuyArchers + ($dailyBuyArchers * ($districtConfig['outpostExtraTroops'] * countOutposts())));
	}
	
	$currentBuyWarriors = $dailyBuyWarriors - $gdi['warriors_bought'];
	$currentBuyArchers = $dailyBuyArchers - $gdi['archers_bought'];

	if ($currentBuyArchers < 0)
		$currentBuyArchers = 0;
	if ($currentBuyWarriors < 0)
		$currentBuyWarriors = 0;
	
	if ($currentBuyArchers > $dailyBuyArchers)
		$currentBuyArchers = $dailyBuyArchers;
	if ($currentBuyWarriors > $dailyBuyWarriors)
		$currentBuyWarriors = $dailyBuyWarriors;
	if (isset($_POST['warriors']))
	{
		$archers = (isset($_POST['archers']) && is_numeric($_POST['archers'])) ? abs($_POST['archers']) : 0;
		$warriors = (isset($_POST['warriors']) && is_numeric($_POST['warriors'])) ? abs($_POST['warriors']) : 0;
		//is the form completely submitted?
		if (($archers + $warriors) == 0)
		{
			alert('danger',"Uh Oh!","Please fill out the form completely before submitting.",true,'guild_district.php');
			die($h->endpage());
		}
		//Recruited warriors and current barracks is over maximum barracks size
		if (($warriors + $gdi['barracks_warriors']) > $districtConfig['BarracksMaxWarriors'])
		{
			alert('danger',"Uh Oh!","You do not have enough room in your barracks for " . shortNumberParse($warriors) . " more warriors. The maximum your barracks may support is " . shortNumberParse($districtConfig['BarracksMaxWarriors']) . " Warriors.",true,'guild_district.php');
			die($h->endpage());
		}
		//Recruited archer and current barracks is over maximum barracks size
		if (($archers + $gdi['barracks_archers']) > $districtConfig['BarracksMaxArchers'])
		{
			alert('danger',"Uh Oh!","You do not have enough room in your barracks for " . shortNumberParse($archers) . " more warriors. The maximum your barracks may support is " . shortNumberParse($districtConfig['BarracksMaxArchers']) . " Archers.",true,'guild_district.php');
			die($h->endpage());
		}
		//Recruited warriors and current barracks is over maximum barracks size
		if ($warriors > $currentBuyWarriors)
		{
			alert('danger',"Uh Oh!","You cannot buy " . shortNumberParse($warriors) . " Warriors right now. You may only buy " . shortNumberParse($currentBuyWarriors) . " Warriors right now.",true,'guild_district.php');
			die($h->endpage());
		}
		if ($archers > $currentBuyArchers)
		{
			alert('danger',"Uh Oh!","You cannot buy " . shortNumberParse($archers) . "Archers right now. You may only buy " . shortNumberParse($currentBuyArchers) . " Archers right now.",true,'guild_district.php');
			die($h->endpage());
		}
		$archerTotal=$archers*$districtConfig['ArcherCost'];
		$warriorTotal=$warriors*$districtConfig['WarriorCost'];
		$allTotal = $warriorTotal + $archerTotal;
		if ($gi['guild_primcurr'] < $allTotal)
		{
			alert('danger',"Uh Oh!","Your guild needs " . shortNumberParse($allTotal) . " Copper Coins in it's vault before you can purchase that many units.",true,'guild_district.php');
			die($h->endpage());
		}
		$db->query("UPDATE `guild_district_info` SET 
					`warriors_bought` = `warriors_bought` + {$warriors},
					`archers_bought` = `archers_bought` + {$archers}
					WHERE `guild_id` = {$ir['guild']}");
		updateBarracksTroops($ir['guild'], $warriors, $archers, 0, 0);
		$db->query("UPDATE `guild` SET `guild_primcurr` = `guild_primcurr` - {$allTotal} WHERE `guild_id` = {$ir['guild']}");
		alert('success',"Success!","You have spent " . shortNumberParse($allTotal) . " Copper Coins for " . shortNumberParse($warriors) . " Warriors and " . shortNumberParse($archers) . " Archers for your guild.",true,'guild_district.php');
		$api->GuildAddNotification($ir['guild'],"<a href='profile.php?user={$userid}'>{$ir['username']}</a> has spent " . shortNumberParse($allTotal) . " Copper Coins for " . shortNumberParse($warriors) . " Warriors and " . shortNumberParse($archers) . " Archers.");
		$api->SystemLogsAdd($userid,"district","spent " . shortNumberParse($allTotal) . " Copper Coins for " . shortNumberParse($warriors) . " Warriors and " . shortNumberParse($archers) . " Archers");
		addToEconomyLog('Districts', 'copper', $allTotal*-1);
	}
	else
	{
		echo "
        <form method='post'>
		<div class='row'>
            <div class='col'>
                <div class='card'>
                    <div class='card-header'>
                        Purchase Troops
                    </div>
                    <div class='card-body'>
                        How many troops do you wish to buy? Fill and submit the form to confirm. Your guild 
                        has " . shortNumberParse($gi['guild_primcurr']) . " Copper Coins in its vault. Warriors have 
                        a daily upkeep fee of " . shortNumberParse($districtConfig['WarriorCostDaily']) . " Copper Coins, and
                        Archers have a daily upkeep fee of " . shortNumberParse($districtConfig['ArcherCostDaily']) . " Copper Coins.
                        This fee is taken from your guild's vault every day at midnight gametime.
                        <hr />
                        <div class='row'>
                            <div class='col-4 col-md-2'>
                                Warriors
                            </div>
                            <div class='col-8 col-md-4 col-xl-3'>
                                <small>" . shortNumberParse($districtConfig['WarriorCost']) . " Copper Coins each</small>
                            </div>
                            <div class='col-12 col-md-6 col-xl-7'>
                                <input type='number' name='warriors' value='{$currentBuyWarriors}' max='{$currentBuyWarriors}' min='0' required='1' class='form-control'>
                            </div>
                            <div class='col-4 col-md-2'>
                                Archers
                            </div>
                            <div class='col-8 col-md-4 col-xl-3'>
                                <small>" . shortNumberParse($districtConfig['ArcherCost']) . " Copper Coins each</small>
                            </div>
                            <div class='col-12 col-md-6 col-xl-7'>
                                <input type='number' name='archers' value='{$currentBuyArchers}' max='{$currentBuyArchers}' min='0' required='1' class='form-control'>
                            </div>
                            <div class='col-12'>
                                <input type='submit' value='Buy Troops' class='btn btn-success btn-block'>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        </form>";
	}
	
}

function hireGeneral()
{
	global $db, $api, $userid, $ir, $gdi, $districtConfig, $h, $gi;
	if (blockAccess($ir['guild']))
	{
		alert('danger',"Uh Oh!","You cannot purchase units while not in a guild.",true,'guild_district.php');
		die($h->endpage());
	}
	if (!isGuildLeaders($ir['guild'], $userid))
	{
		alert('danger',"Uh Oh!","You must be a guild leader or co-leader to purchase troops.",true,'guild_district.php');
		die($h->endpage());
	}
	//active troops divided by troops required per general, then subtract current active generals from that.
	$availableGenerals = (floor(countActiveTroops() / $districtConfig['GeneralTroops']) - countGenerals());
	if ($availableGenerals < 0)
		$availableGenerals = 0;
	$availableCaptains = (floor(countActiveTroops() / $districtConfig['CaptainTroops']) - countCaptains());
	if ($availableCaptains < 0)
	    $availableCaptains = 0;
	if (countTowns() > 0)
	{
	    $districtConfig['GeneralCost'] = round($districtConfig['GeneralCost'] - ($districtConfig['GeneralCost']*($districtConfig['townLessCost']*countTowns())));
	    $districtConfig['CaptainCost'] = round($districtConfig['CaptainCost'] - ($districtConfig['CaptainCost']*($districtConfig['townLessCost']*countTowns())));
	}
	if (isset($_POST['warriors']))
	{
	
		$generals = (isset($_POST['warriors']) && is_numeric($_POST['warriors'])) ? abs($_POST['warriors']) : 0;
		$captains = (isset($_POST['captains']) && is_numeric($_POST['captains'])) ? abs($_POST['captains']) : 0;
		//is the form completely submitted?
		if ($generals == 0 && $captains == 0)
		{
			alert('danger',"Uh Oh!","Please fill out the form completely before submitting.",true,'guild_district.php');
			die($h->endpage());
		}
		if ($generals > $availableGenerals)
		{
		    alert('danger',"Uh Oh!","You cannot hire that many generals at this time.",true,'guild_district.php');
		    die($h->endpage());
		}
		if ($captains > $availableCaptains)
		{
		    alert('danger',"Uh Oh!","You cannot hire that many captains at this time.",true,'guild_district.php');
		    die($h->endpage());
		}
		$generalsTotal=$generals*$districtConfig['GeneralCost'];
		$captainsTotal=$captains*$districtConfig['CaptainCost'];
		$totalCost = $captainsTotal + $generalsTotal;
		if ($gi['guild_primcurr'] < $totalCost)
		{
		    alert('danger',"Uh Oh!","Your guild needs " . shortNumberParse($totalCost) . " Copper Coins in it's vault before you can hire that many unique units.",true,'guild_district.php');
		    die($h->endpage());
		}
		updateBarracksTroops($ir['guild'], 0, 0, $generals, $captains);
		$db->query("UPDATE `guild` SET `guild_primcurr` = `guild_primcurr` - {$totalCost} WHERE `guild_id` = {$ir['guild']}");
		alert('success',"Success!","You have spent " . shortNumberParse($totalCost) . " Copper Coins and hired " . shortNumberParse($generals) . " Generals and " . shortNumberParse($captains) . " Captains for your guild.",true,'guild_district.php');
		$api->GuildAddNotification($ir['guild'],"<a href='profile.php?user={$userid}'>{$ir['username']}</a> has spent " . shortNumberParse($totalCost) . " Copper Coins and hired " . shortNumberParse($generals) . " Generals and " . shortNumberParse($captains) . " Captains for your Guild District.");
		$api->SystemLogsAdd($userid,"district","Spent " . shortNumberParse($totalCost) . " Copper Coins for " . shortNumberParse($generals) . " Generals and " . shortNumberParse($captains) . " Captains.");
		addToEconomyLog('Districts', 'copper', $generalsTotal*-1);
	}
	else
	{
        echo "
        <form method='post'>
		<div class='row'>
            <div class='col'>
                <div class='card'>
                    <div class='card-header'>
                        Hire Specialized Units
                    </div>
                    <div class='card-body'>
                        Generals are a purely defensive unit. You may place them on tiles you own for a 
                        " . round($districtConfig['GeneralBuff']*100) . "% defensive buff. You may only place {$districtConfig['maxGenerals']}
                        Generals on a tile at a time. If the tile is lost, your general will be executed.
                        Generals have a daily upkeep fee of " . shortNumberParse($districtConfig['GeneralCostDaily']) . " Copper Coins.<hr />
                        Captains are purely offensive units. Captains increase your army's offensive abilities by " . round($districtConfig['CaptainBuff']*100) . "%. 
                        Captains do not have a daily upkeep fee, seeing as they spend their time relaxing in the barracks when not in battle.  Captains are lost 
                        when their attack fails. Captains cost " . shortNumberParse($districtConfig['CaptainCost']) . " Copper Coins to recruit, and 
                        " . shortNumberParse($districtConfig['CaptainCostUse']) . " Copper Coins per battle used.
                        <hr />
                        Your guild has 
                        " . shortNumberParse($gi['guild_primcurr']) . " Copper Coins in its vault.
                        Upkeep fees are taken from your guild's vault every day at midnight gametime.
                        <br />
                        You may hire {$availableCaptains} Captains and {$availableGenerals} Generals at this time.
                        <hr />
                        <div class='row'>
                            <div class='col-4 col-md-2'>
                                General
                            </div>
                            <div class='col-8 col-md-4 col-xl-3'>
                                <small>" . shortNumberParse($districtConfig['GeneralCost']) . " Copper Coins each</small>
                            </div>
                            <div class='col-12 col-md-6 col-xl-7'>
                                <input type='number' name='warriors' value='{$availableGenerals}' max='{$availableGenerals}' min='0' required='1' class='form-control'>
                            </div>
                            <div class='col-4 col-md-2'>
                                Captain
                            </div>
                            <div class='col-8 col-md-4 col-xl-3'>
                                <small>" . shortNumberParse($districtConfig['CaptainCost']) . " Copper Coins each</small>
                            </div>
                            <div class='col-12 col-md-6 col-xl-7'>
                                <input type='number' name='captains' value='{$availableCaptains}' max='{$availableCaptains}' min='0' required='1' class='form-control'>
                            </div>
                            <div class='col-12'>
                                <input type='submit' value='Hire Units' class='btn btn-success btn-block'>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        </form>";
	}
}
include('forms/district_popup.php');
//include('forms/district_stats.php');
include('forms/popup_district_new.php');
$h->endpage();
echo "<link rel='stylesheet' href='css/modules/districts.css'>";
//Functions needed to make the module work.