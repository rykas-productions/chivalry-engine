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
*/
//Module config
$districtConfig['MaxSizeX'] = 5;
$districtConfig['MaxSizeY'] = 5;
$districtConfig['BarracksMaxWarriors'] = 3000;
$districtConfig['BarracksMaxArchers'] = 1500;
$districtConfig['GeneralBuff'] = 0.1851715;
$districtConfig['GeneralTroops'] = 2500;
$districtConfig['GeneralCost'] = 125000;
$districtConfig['GeneralCostDaily'] = 12500;
$districtConfig['WarriorCost'] = 5000;
$districtConfig['WarriorCostDaily'] = 500;
$districtConfig['ArcherCost'] = 8500;
$districtConfig['ArcherCostDaily'] = 1000;
$districtConfig['copperPerFortify']=5000;
$districtConfig['xpPerFortify']=125;
$districtConfig['xpPerFortifyMulti']=2.2578721;
$districtConfig['fortifyBuffMulti']=0.0613655;
$districtConfig['attackRangeDmgMulti']=1.194115;
$districtConfig['attackDmgWeakeness']=0.741294;
$districtConfig['attackDmgStrength']=1.04901724;
$districtConfig['attackDefenseAdvantage']=1.1582412;
$districtConfig['maxGenerals'] = 2;
$districtConfig['maxFortify'] = 5;
//end module config

require('globals.php');
echo "<h3>Guild Districts</h3><hr />
    <div class='row'>
        <div class='col-12 col-sm-6 col-md-4 col-xl-12 col-xxl'>
            <a href='#' data-toggle='modal' data-target='#district_info' class='btn btn-info btn-block'>Info</a>
            <br />
        </div>
        <div class='col-12  col-sm-6 col-md-4 col-xl-3 col-xxl'>
            <a href='guild_district.php' class='btn btn-primary btn-block'>Home</a>
            <br />
        </div>
        <div class='col-12  col-sm-6 col-md-4 col-xl-3 col-xxl'>
            <a href='?action=guildinfo' class='btn btn-success btn-block'>Your Guild Info</a>
            <br />
        </div>
        <div class='col-12  col-sm-6 col-xl-3 col-xxl'>
            <a href='?action=buy' class='btn btn-danger btn-block'>Buy Troops</a>
            <br />
        </div>
        <div class='col-12  col-sm-12 col-md-6 col-xl-3 col-xxl'>
            <a href='?action=general' class='btn btn-secondary btn-block'>Hire General</a>
            <br />
        </div>
    </div>
    <hr />";
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
	$gdi = ($db->fetch_row($db->query("/*qc=on*/SELECT * FROM `guild_district_info` WHERE `guild_id` = {$ir['guild']}")));
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
	default:
        home();
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
function home()
{
	global $districtConfig, $userid, $db, $api, $h, $ir;
	$currentY=1;
	echo "<table class='table table-bordered table-dark'>";
	while ($currentY <= $districtConfig['MaxSizeY'])
	{
		echo "<tr>";
		$q=$db->query("SELECT * FROM `guild_districts` WHERE `district_y` = {$currentY} ORDER BY `district_x` ASC LIMIT {$districtConfig['MaxSizeX']}");
		while ($r=$db->fetch_row($q))
		{
			$color='#f5c6cb';
			$border='#dee2e6';
			$thicc='tiny';
			if (($r['district_owner'] == $ir['guild']) && ($ir['guild'] != 0))
				$color='#c3e6cb';
			if ($r['district_type'] == 'river')
			{
				$color='#b8daff';
			}
			if ($r['district_type'] == 'elevated')
			{
				$thicc='medium';
				$border='#ffc107';
			}
			if ($r['district_type'] == 'outpost')
			{
				$thicc='medium';
				$border='#9a6790';
			}
			if ($r['district_type'] == 'lowered')
			{
				$thicc='medium';
				$border='#f8f9fa';
			}
			if ($r['district_type'] == 'market')
			{
				$border='#17a2b8';
				$thicc='medium';
			}
			if ($r['district_fortify'] > 0)
			{
				if (($r['district_owner'] == $ir['guild']) && ($ir['guild'] != 0))
					$border='#28a745';
				else
					$border='#343a40';
				
				$thicc='medium';
			}
			echo "
			<td width='20%' style='background-color:{$color}; border-color:{$border}; border-width:{$thicc};'>
				<b>Y: {$r['district_y']}; X: {$r['district_x']}</b><br />
				Guild: <a href='guilds.php?action=view&id={$r['district_owner']}'>{$api->GuildFetchInfo($r['district_owner'],'guild_name')}</a><br />
				[<a href='?action=view&id={$r['district_id']}'>View Info</a>]
			</td>";
		}
		echo "</tr>";
		$currentY++;
	}
	echo "</table>";
}
function view()
{
	global $districtConfig, $userid, $db, $api, $h, $ir;
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
	$r=$db->fetch_row($central);
	$NW=$db->query("SELECT * FROM `guild_districts` WHERE `district_x` = ({$r['district_x']} - 1) AND `district_y` = ({$r['district_y']} - 1)");
	$N=$db->query("SELECT * FROM `guild_districts` WHERE `district_x` = {$r['district_x']} AND `district_y` = ({$r['district_y']} - 1)");
	$NE=$db->query("SELECT * FROM `guild_districts` WHERE `district_x` = ({$r['district_x']} + 1) AND `district_y` = ({$r['district_y']} - 1)");
	
	$W=$db->query("SELECT * FROM `guild_districts` WHERE `district_x` = ({$r['district_x']} - 1) AND `district_y` = {$r['district_y']}");
	$C=$db->query("SELECT * FROM `guild_districts` WHERE `district_x` = {$r['district_x']} AND `district_y` = {$r['district_y']}");
	$E=$db->query("SELECT * FROM `guild_districts` WHERE `district_x` = ({$r['district_x']} + 1) AND `district_y` = {$r['district_y']}");
	
	$SW=$db->query("SELECT * FROM `guild_districts` WHERE `district_x` = ({$r['district_x']} - 1) AND `district_y` = ({$r['district_y']} + 1)");
	$S=$db->query("SELECT * FROM `guild_districts` WHERE `district_x` = {$r['district_x']} AND `district_y` = ({$r['district_y']} + 1)");
	$SE=$db->query("SELECT * FROM `guild_districts` WHERE `district_x` = ({$r['district_x']} + 1) AND `district_y` = ({$r['district_y']} + 1)");
	
	echo "<table class='table table-bordered table-dark'>";
	//Top
	echo "<tr>";
		//North West
		if ($db->num_rows($NW) > 0)
		{
			$r=$db->fetch_row($NW);
			$color='#f5c6cb';
			$border='#dee2e6';
			$thicc='tiny';
			if (($r['district_owner'] == $ir['guild']) && ($ir['guild'] != 0))
				$color='#c3e6cb';
			if ($r['district_type'] == 'river')
			{
				$color='#b8daff';
			}
			if ($r['district_type'] == 'outpost')
			{
				$thicc='medium';
				$border='#9a6790';
			}
			if ($r['district_type'] == 'elevated')
			{
				$thicc='medium';
				$border='#ffc107';
			}
			if ($r['district_type'] == 'lowered')
			{
				$thicc='medium';
				$border='#f8f9fa';
			}
			if ($r['district_type'] == 'market')
			{
				$border='#17a2b8';
				$thicc='medium';
			}
			if ($r['district_fortify'] > 0)
			{
				if (($r['district_owner'] == $ir['guild']) && ($ir['guild'] != 0))
					$border='#28a745';
				else
					$border='#343a40';
				
				$thicc='medium';
			}
			echo "
			<td width='33%' style='background-color:{$color}; border-color:{$border}; border-width:{$thicc};'>
				<b>Y: {$r['district_y']}; X: {$r['district_x']}</b><br />
				Guild: <a href='guilds.php?action=view&id={$r['district_owner']}'>{$api->GuildFetchInfo($r['district_owner'],'guild_name')}</a><br />";
				if (isDistrictAccessible($r['district_id']))
				{
					echo "Warriors: " . number_format($r['district_melee']) . "<br />
					Archers: " . number_format($r['district_range']) . "<br />
					Generals: " . number_format($r['district_general']) . "<br />
					Fortification: " . returnForticationLevel($r['district_id']) . "<br />";
				}
				echo "[<a href='?action=view&id={$r['district_id']}'>View Info</a>]
			</td>";
		}
		//North
		if ($db->num_rows($N) > 0)
		{
			$r=$db->fetch_row($N);
			$color='#f5c6cb';
			$border='#dee2e6';
			$thicc='tiny';
			if (($r['district_owner'] == $ir['guild']) && ($ir['guild'] != 0))
				$color='#c3e6cb';
			if ($r['district_type'] == 'river')
			{
				$color='#b8daff';
			}
			if ($r['district_type'] == 'elevated')
			{
				$thicc='medium';
				$border='#ffc107';
			}
			if ($r['district_type'] == 'outpost')
			{
				$thicc='medium';
				$border='#9a6790';
			}
			if ($r['district_type'] == 'lowered')
			{
				$thicc='medium';
				$border='#f8f9fa';
			}
			if ($r['district_type'] == 'market')
			{
				$border='#17a2b8';
				$thicc='medium';
			}
			if ($r['district_fortify'] > 0)
			{
				if (($r['district_owner'] == $ir['guild']) && ($ir['guild'] != 0))
					$border='#28a745';
				else
					$border='#343a40';
				
				$thicc='medium';
			}
			echo "
			<td width='33%' style='background-color:{$color}; border-color:{$border}; border-width:{$thicc};'>
				<b>Y: {$r['district_y']}; X: {$r['district_x']}</b><br />
				Guild: <a href='guilds.php?action=view&id={$r['district_owner']}'>{$api->GuildFetchInfo($r['district_owner'],'guild_name')}</a><br />";
				if (isDistrictAccessible($r['district_id']))
				{
					echo "Warriors: " . number_format($r['district_melee']) . "<br />
					Archers: " . number_format($r['district_range']) . "<br />
					Generals: " . number_format($r['district_general']) . "<br />
					Fortification: " . returnForticationLevel($r['district_id']) . "<br />";
				}
				echo "[<a href='?action=view&id={$r['district_id']}'>View Info</a>]
			</td>";
		}
		//North East
		if ($db->num_rows($NE) > 0)
		{
			$r=$db->fetch_row($NE);
			$color='#f5c6cb';
			$border='#dee2e6';
			$thicc='tiny';
			if (($r['district_owner'] == $ir['guild']) && ($ir['guild'] != 0))
				$color='#c3e6cb';
			if ($r['district_type'] == 'river')
			{
				$color='#b8daff';
			}
			if ($r['district_type'] == 'elevated')
			{
				$thicc='medium';
				$border='#ffc107';
			}
			if ($r['district_type'] == 'lowered')
			{
				$thicc='medium';
				$border='#f8f9fa';
			}
			if ($r['district_type'] == 'outpost')
			{
				$thicc='medium';
				$border='#9a6790';
			}
			if ($r['district_type'] == 'market')
			{
				$border='#17a2b8';
				$thicc='medium';
			}
			if ($r['district_fortify'] > 0)
			{
				if (($r['district_owner'] == $ir['guild']) && ($ir['guild'] != 0))
					$border='#28a745';
				else
					$border='#343a40';
				
				$thicc='medium';
			}
			echo "
			<td width='33%' style='background-color:{$color}; border-color:{$border}; border-width:{$thicc};'>
				<b>Y: {$r['district_y']}; X: {$r['district_x']}</b><br />
				Guild: <a href='guilds.php?action=view&id={$r['district_owner']}'>{$api->GuildFetchInfo($r['district_owner'],'guild_name')}</a><br />";
				if (isDistrictAccessible($r['district_id']))
				{
					echo "Warriors: " . number_format($r['district_melee']) . "<br />
					Archers: " . number_format($r['district_range']) . "<br />
					Generals: " . number_format($r['district_general']) . "<br />
					Fortification: " . returnForticationLevel($r['district_id']) . "<br />";
				}
				echo "[<a href='?action=view&id={$r['district_id']}'>View Info</a>]
			</td>";
		}
	echo "</tr>";
	//Center
	echo"<tr>";
	//West
		if ($db->num_rows($W) > 0)
		{
			$r=$db->fetch_row($W);
			$color='#f5c6cb';
			$border='#dee2e6';
			$thicc='tiny';
			if (($r['district_owner'] == $ir['guild']) && ($ir['guild'] != 0))
				$color='#c3e6cb';
			if ($r['district_type'] == 'river')
			{
				$color='#b8daff';
			}
			if ($r['district_type'] == 'elevated')
			{
				$thicc='medium';
				$border='#ffc107';
			}
			if ($r['district_type'] == 'lowered')
			{
				$thicc='medium';
				$border='#f8f9fa';
			}
			if ($r['district_type'] == 'market')
			{
				$border='#17a2b8';
				$thicc='medium';
			}
			if ($r['district_type'] == 'outpost')
			{
				$thicc='medium';
				$border='#9a6790';
			}
			if ($r['district_fortify'] > 0)
			{
				if (($r['district_owner'] == $ir['guild']) && ($ir['guild'] != 0))
					$border='#28a745';
				else
					$border='#343a40';
				
				$thicc='medium';
			}
			echo "
			<td width='33%' style='background-color:{$color}; border-color:{$border}; border-width:{$thicc};'>
				<b>Y: {$r['district_y']}; X: {$r['district_x']}</b><br />
				Guild: <a href='guilds.php?action=view&id={$r['district_owner']}'>{$api->GuildFetchInfo($r['district_owner'],'guild_name')}</a><br />";
				if (isDistrictAccessible($r['district_id']))
				{
					echo "Warriors: " . number_format($r['district_melee']) . "<br />
					Archers: " . number_format($r['district_range']) . "<br />
					Generals: " . number_format($r['district_general']) . "<br />
					Fortification: " . returnForticationLevel($r['district_id']) . "<br />";
				}
				echo "[<a href='?action=view&id={$r['district_id']}'>View Info</a>]
			</td>";
		}
		//central
		if ($db->num_rows($C) > 0)
		{
			$r=$db->fetch_row($C);
			$color='#f5c6cb';
			$border='#dee2e6';
			$thicc='tiny';
			if (($r['district_owner'] == $ir['guild']) && ($ir['guild'] != 0))
				$color='#c3e6cb';
			if ($r['district_type'] == 'river')
			{
				$color='#b8daff';
			}
			if ($r['district_type'] == 'elevated')
			{
				$thicc='medium';
				$border='#ffc107';
			}
			if ($r['district_type'] == 'lowered')
			{
				$thicc='medium';
				$border='#f8f9fa';
			}
			if ($r['district_type'] == 'market')
			{
				$border='#17a2b8';
				$thicc='medium';
			}
			if ($r['district_type'] == 'outpost')
			{
				$thicc='medium';
				$border='#9a6790';
			}
			if ($r['district_fortify'] > 0)
			{
				if (($r['district_owner'] == $ir['guild']) && ($ir['guild'] != 0))
					$border='#28a745';
				else
					$border='#343a40';
				
				$thicc='medium';
			}
			echo "
			<td width='33%' style='background-color:{$color}; border-color:{$border}; border-width:{$thicc};'>
				<b>Y: {$r['district_y']}; X: {$r['district_x']}</b><br />
				Guild: <a href='guilds.php?action=view&id={$r['district_owner']}'>{$api->GuildFetchInfo($r['district_owner'],'guild_name')}</a><br />";
				if (isDistrictAccessible($r['district_id']))
				{
					echo "Warriors: " . number_format($r['district_melee']) . "<br />
					Archers: " . number_format($r['district_range']) . "<br />
					Generals: " . number_format($r['district_general']) . "<br />
					Fortification: " . returnForticationLevel($r['district_id']) . "<br />";
					if (!isGuildDistrict($r['district_id']))
						echo "[<a href='?action=attack&id={$r['district_id']}'>Attack Tile</a>]<br />";
					if (isGuildDistrict($r['district_id']))
					{
						echo "[<a href='?action=moveto&id={$r['district_id']}'>Move Troops</a>]<br />
						[<a href='?action=movebarracks&id={$r['district_id']}'>Move from Barracks</a>]<br />
						[<a href='?action=fortify&id={$r['district_id']}'>Fortify</a>]<br />";
					}
				}
			echo "</td>";
		}
		//East
		if ($db->num_rows($E) > 0)
		{
			$r=$db->fetch_row($E);
			$color='#f5c6cb';
			$border='#dee2e6';
			$thicc='tiny';
			if (($r['district_owner'] == $ir['guild']) && ($ir['guild'] != 0))
				$color='#c3e6cb';
			if ($r['district_type'] == 'river')
			{
				$color='#b8daff';
			}
			if ($r['district_type'] == 'elevated')
			{
				$thicc='medium';
				$border='#ffc107';
			}
			if ($r['district_type'] == 'lowered')
			{
				$thicc='medium';
				$border='#f8f9fa';
			}
			if ($r['district_type'] == 'market')
			{
				$border='#17a2b8';
				$thicc='medium';
			}
			if ($r['district_type'] == 'outpost')
			{
				$thicc='medium';
				$border='#9a6790';
			}
			if ($r['district_fortify'] > 0)
			{
				if (($r['district_owner'] == $ir['guild']) && ($ir['guild'] != 0))
					$border='#28a745';
				else
					$border='#343a40';
				
				$thicc='medium';
			}
			echo "
			<td width='33%' style='background-color:{$color}; border-color:{$border}; border-width:{$thicc};'>
				<b>Y: {$r['district_y']}; X: {$r['district_x']}</b><br />
				Guild: <a href='guilds.php?action=view&id={$r['district_owner']}'>{$api->GuildFetchInfo($r['district_owner'],'guild_name')}</a><br />";
				if (isDistrictAccessible($r['district_id']))
				{
					echo "Warriors: " . number_format($r['district_melee']) . "<br />
					Archers: " . number_format($r['district_range']) . "<br />
					Generals: " . number_format($r['district_general']) . "<br />
					Fortification: " . returnForticationLevel($r['district_id']) . "<br />";
				}
				echo "[<a href='?action=view&id={$r['district_id']}'>View Info</a>]
			</td>";
		}
	echo "</tr>";
	//Bottom
	echo "<tr>";
	//South West
		if ($db->num_rows($SW) > 0)
		{
			$r=$db->fetch_row($SW);
			$color='#f5c6cb';
			$border='#dee2e6';
			$thicc='tiny';
			if (($r['district_owner'] == $ir['guild']) && ($ir['guild'] != 0))
				$color='#c3e6cb';
			if ($r['district_type'] == 'river')
			{
				$color='#b8daff';
			}
			if ($r['district_type'] == 'elevated')
			{
				$thicc='medium';
				$border='#ffc107';
			}
			if ($r['district_type'] == 'lowered')
			{
				$thicc='medium';
				$border='#f8f9fa';
			}
			if ($r['district_type'] == 'market')
			{
				$border='#17a2b8';
				$thicc='medium';
			}
			if ($r['district_type'] == 'outpost')
			{
				$thicc='medium';
				$border='#9a6790';
			}
			if ($r['district_fortify'] > 0)
			{
				if (($r['district_owner'] == $ir['guild']) && ($ir['guild'] != 0))
					$border='#28a745';
				else
					$border='#343a40';
				
				$thicc='medium';
			}
			echo "
			<td width='33%' style='background-color:{$color}; border-color:{$border}; border-width:{$thicc};'>
				<b>Y: {$r['district_y']}; X: {$r['district_x']}</b><br />
				Guild: <a href='guilds.php?action=view&id={$r['district_owner']}'>{$api->GuildFetchInfo($r['district_owner'],'guild_name')}</a><br />";
				if (isDistrictAccessible($r['district_id']))
				{
					echo "Warriors: " . number_format($r['district_melee']) . "<br />
					Archers: " . number_format($r['district_range']) . "<br />
					Generals: " . number_format($r['district_general']) . "<br />
					Fortification: " . returnForticationLevel($r['district_id']) . "<br />";
				}
				echo "[<a href='?action=view&id={$r['district_id']}'>View Info</a>]
			</td>";
		}
		//South
		if ($db->num_rows($S) > 0)
		{
			$r=$db->fetch_row($S);
			$color='#f5c6cb';
			$border='#dee2e6';
			$thicc='tiny';
			if (($r['district_owner'] == $ir['guild']) && ($ir['guild'] != 0))
				$color='#c3e6cb';
			if ($r['district_type'] == 'river')
			{
				$color='#b8daff';
			}
			if ($r['district_type'] == 'elevated')
			{
				$thicc='medium';
				$border='#ffc107';
			}
			if ($r['district_type'] == 'lowered')
			{
				$thicc='medium';
				$border='#f8f9fa';
			}
			if ($r['district_type'] == 'market')
			{
				$border='#17a2b8';
				$thicc='medium';
			}
			if ($r['district_type'] == 'outpost')
			{
				$thicc='medium';
				$border='#9a6790';
			}
			if ($r['district_fortify'] > 0)
			{
				if (($r['district_owner'] == $ir['guild']) && ($ir['guild'] != 0))
					$border='#28a745';
				else
					$border='#343a40';
				
				$thicc='medium';
			}
			echo "
			<td width='33%' style='background-color:{$color}; border-color:{$border}; border-width:{$thicc};'>
				<b>Y: {$r['district_y']}; X: {$r['district_x']}</b><br />
				Guild: <a href='guilds.php?action=view&id={$r['district_owner']}'>{$api->GuildFetchInfo($r['district_owner'],'guild_name')}</a><br />";
				if (isDistrictAccessible($r['district_id']))
				{
					echo "Warriors: " . number_format($r['district_melee']) . "<br />
					Archers: " . number_format($r['district_range']) . "<br />
					Generals: " . number_format($r['district_general']) . "<br />
					Fortification: " . returnForticationLevel($r['district_id']) . "<br />";
				}
				echo "[<a href='?action=view&id={$r['district_id']}'>View Info</a>]
			</td>";
		}
		//South East
		if ($db->num_rows($SE) > 0)
		{
			$r=$db->fetch_row($SE);
			$color='#f5c6cb';
			$border='#dee2e6';
			$thicc='tiny';
			if (($r['district_owner'] == $ir['guild']) && ($ir['guild'] != 0))
				$color='#c3e6cb';
			if ($r['district_type'] == 'river')
			{
				$color='#b8daff';
			}
			if ($r['district_type'] == 'elevated')
			{
				$thicc='medium';
				$border='#ffc107';
			}
			if ($r['district_type'] == 'lowered')
			{
				$thicc='medium';
				$border='#f8f9fa';
			}
			if ($r['district_type'] == 'market')
			{
				$border='#17a2b8';
				$thicc='medium';
			}
			if ($r['district_type'] == 'outpost')
			{
				$thicc='medium';
				$border='#9a6790';
			}
			if ($r['district_fortify'] > 0)
			{
				if (($r['district_owner'] == $ir['guild']) && ($ir['guild'] != 0))
					$border='#28a745';
				else
					$border='#343a40';
				
				$thicc='medium';
			}
			echo "
			<td width='33%' style='background-color:{$color}; border-color:{$border}; border-width:{$thicc};'>
				<b>Y: {$r['district_y']}; X: {$r['district_x']}</b><br />
				Guild: <a href='guilds.php?action=view&id={$r['district_owner']}'>{$api->GuildFetchInfo($r['district_owner'],'guild_name')}</a><br />";
				if (isDistrictAccessible($r['district_id']))
				{
					echo "Warriors: " . number_format($r['district_melee']) . "<br />
					Archers: " . number_format($r['district_range']) . "<br />
					Generals: " . number_format($r['district_general']) . "<br />
					Fortification: " . returnForticationLevel($r['district_id']) . "<br />";
				}
				echo "[<a href='?action=view&id={$r['district_id']}'>View Info</a>]
			</td>";
		}
	echo "</tr>";
	echo "</table>";
	
}
function moveto()
{
	global $districtConfig, $userid, $db, $api, $h, $ir, $gdi;
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
	$r=$db->fetch_row($central);
	$NW=$db->query("SELECT * FROM `guild_districts` WHERE `district_x` = ({$r['district_x']} - 1) AND `district_y` = ({$r['district_y']} - 1)");
	$N=$db->query("SELECT * FROM `guild_districts` WHERE `district_x` = {$r['district_x']} AND `district_y` = ({$r['district_y']} - 1)");
	$NE=$db->query("SELECT * FROM `guild_districts` WHERE `district_x` = ({$r['district_x']} + 1) AND `district_y` = ({$r['district_y']} - 1)");
	
	$W=$db->query("SELECT * FROM `guild_districts` WHERE `district_x` = ({$r['district_x']} - 1) AND `district_y` = {$r['district_y']}");
	$C=$db->query("SELECT * FROM `guild_districts` WHERE `district_x` = {$r['district_x']} AND `district_y` = {$r['district_y']}");
	$E=$db->query("SELECT * FROM `guild_districts` WHERE `district_x` = ({$r['district_x']} + 1) AND `district_y` = {$r['district_y']}");
	
	$SW=$db->query("SELECT * FROM `guild_districts` WHERE `district_x` = ({$r['district_x']} - 1) AND `district_y` = ({$r['district_y']} + 1)");
	$S=$db->query("SELECT * FROM `guild_districts` WHERE `district_x` = {$r['district_x']} AND `district_y` = ({$r['district_y']} + 1)");
	$SE=$db->query("SELECT * FROM `guild_districts` WHERE `district_x` = ({$r['district_x']} + 1) AND `district_y` = ({$r['district_y']} + 1)");
	
	echo "<table class='table table-bordered table-dark'>";
	//Top
	echo "<tr>";
		//North West
		if ($db->num_rows($NW) > 0)
		{
			$r=$db->fetch_row($NW);
			$color='#f5c6cb';
			$border='#dee2e6';
			$thicc='tiny';
			if (($r['district_owner'] == $ir['guild']) && ($ir['guild'] != 0))
				$color='#c3e6cb';
			if ($r['district_type'] == 'river')
			{
				$color='#b8daff';
			}
			if ($r['district_type'] == 'elevated')
			{
				$thicc='medium';
				$border='#ffc107';
			}
			if ($r['district_type'] == 'lowered')
			{
				$thicc='medium';
				$border='#f8f9fa';
			}
			if ($r['district_type'] == 'market')
			{
				$border='#17a2b8';
				$thicc='medium';
			}
			if ($r['district_type'] == 'outpost')
			{
				$thicc='medium';
				$border='#9a6790';
			}
			if ($r['district_fortify'] > 0)
			{
				if (($r['district_owner'] == $ir['guild']) && ($ir['guild'] != 0))
					$border='#28a745';
				else
					$border='#343a40';
				
				$thicc='medium';
			}
			echo "
			<td width='33%' style='background-color:{$color}; border-color:{$border}; border-width:{$thicc};'>
				<b>Y: {$r['district_y']}; X: {$r['district_x']}</b><br />
				Guild: <a href='guilds.php?action=view&id={$r['district_owner']}'>{$api->GuildFetchInfo($r['district_owner'],'guild_name')}</a><br />";
				if (isDistrictAccessible($r['district_id']))
				{
					echo "Warriors: " . number_format($r['district_melee']) . "<br />
					Archers: " . number_format($r['district_range']) . "<br />
					Generals: " . number_format($r['district_general']) . "<br />
					Fortification: " . returnForticationLevel($r['district_id']) . "<br />";
					if (isGuildDistrict($r['district_id']))
					{
						if (isAccessibleFromTile($r['district_id'], $district_id))
							echo "[<a href='?action=moveform&from={$r['district_id']}&to={$district_id}'>Move to Here</a>]";
					}
				}
				"</td>";
		}
		//North
		if ($db->num_rows($N) > 0)
		{
			$r=$db->fetch_row($N);
			$color='#f5c6cb';
			$border='#dee2e6';
			$thicc='tiny';
			if (($r['district_owner'] == $ir['guild']) && ($ir['guild'] != 0))
				$color='#c3e6cb';
			if ($r['district_type'] == 'river')
			{
				$color='#b8daff';
			}
			if ($r['district_type'] == 'elevated')
			{
				$thicc='medium';
				$border='#ffc107';
			}
			if ($r['district_type'] == 'lowered')
			{
				$thicc='medium';
				$border='#f8f9fa';
			}
			if ($r['district_type'] == 'market')
			{
				$border='#17a2b8';
				$thicc='medium';
			}
			if ($r['district_type'] == 'outpost')
			{
				$thicc='medium';
				$border='#9a6790';
			}
			if ($r['district_fortify'] > 0)
			{
				if (($r['district_owner'] == $ir['guild']) && ($ir['guild'] != 0))
					$border='#28a745';
				else
					$border='#343a40';
				
				$thicc='medium';
			}
			echo "
			<td width='33%' style='background-color:{$color}; border-color:{$border}; border-width:{$thicc};'>
				<b>Y: {$r['district_y']}; X: {$r['district_x']}</b><br />
				Guild: <a href='guilds.php?action=view&id={$r['district_owner']}'>{$api->GuildFetchInfo($r['district_owner'],'guild_name')}</a><br />";
				if (isDistrictAccessible($r['district_id']))
				{
					echo "Warriors: " . number_format($r['district_melee']) . "<br />
					Archers: " . number_format($r['district_range']) . "<br />
					Generals: " . number_format($r['district_general']) . "<br />
					Fortification: " . returnForticationLevel($r['district_id']) . "<br />";
				}
				if (isGuildDistrict($r['district_id']))
					{
						if (isAccessibleFromTile($r['district_id'], $district_id))
							echo "[<a href='?action=moveform&from={$r['district_id']}&to={$district_id}'>Move to Here</a>]";
					}
				"</td>";
		}
		//North East
		if ($db->num_rows($NE) > 0)
		{
			$r=$db->fetch_row($NE);
			$color='#f5c6cb';
			$border='#dee2e6';
			$thicc='tiny';
			if (($r['district_owner'] == $ir['guild']) && ($ir['guild'] != 0))
				$color='#c3e6cb';
			if ($r['district_type'] == 'river')
			{
				$color='#b8daff';
			}
			if ($r['district_type'] == 'elevated')
			{
				$thicc='medium';
				$border='#ffc107';
			}
			if ($r['district_type'] == 'lowered')
			{
				$thicc='medium';
				$border='#f8f9fa';
			}
			if ($r['district_type'] == 'market')
			{
				$border='#17a2b8';
				$thicc='medium';
			}
			if ($r['district_type'] == 'outpost')
			{
				$thicc='medium';
				$border='#9a6790';
			}
			if ($r['district_fortify'] > 0)
			{
				if (($r['district_owner'] == $ir['guild']) && ($ir['guild'] != 0))
					$border='#28a745';
				else
					$border='#343a40';
				
				$thicc='medium';
			}
			echo "
			<td width='33%' style='background-color:{$color}; border-color:{$border}; border-width:{$thicc};'>
				<b>Y: {$r['district_y']}; X: {$r['district_x']}</b><br />
				Guild: <a href='guilds.php?action=view&id={$r['district_owner']}'>{$api->GuildFetchInfo($r['district_owner'],'guild_name')}</a><br />";
				if (isDistrictAccessible($r['district_id']))
				{
					echo "Warriors: " . number_format($r['district_melee']) . "<br />
					Archers: " . number_format($r['district_range']) . "<br />
					Generals: " . number_format($r['district_general']) . "<br />
					Fortification: " . returnForticationLevel($r['district_id']) . "<br />";
					if (isGuildDistrict($r['district_id']))
					{
						if (isAccessibleFromTile($r['district_id'], $district_id))
							echo "[<a href='?action=moveform&from={$r['district_id']}&to={$district_id}'>Move to Here</a>]";
					}
				}
				"</td>";
		}
	echo "</tr>";
	//Center
	echo"<tr>";
	//West
		if ($db->num_rows($W) > 0)
		{
			$r=$db->fetch_row($W);
			$color='#f5c6cb';
			$border='#dee2e6';
			$thicc='tiny';
			if (($r['district_owner'] == $ir['guild']) && ($ir['guild'] != 0))
				$color='#c3e6cb';
			if ($r['district_type'] == 'river')
			{
				$color='#b8daff';
			}
			if ($r['district_type'] == 'elevated')
			{
				$thicc='medium';
				$border='#ffc107';
			}
			if ($r['district_type'] == 'lowered')
			{
				$thicc='medium';
				$border='#f8f9fa';
			}
			if ($r['district_type'] == 'market')
			{
				$border='#17a2b8';
				$thicc='medium';
			}
			if ($r['district_type'] == 'outpost')
			{
				$thicc='medium';
				$border='#9a6790';
			}
			if ($r['district_fortify'] > 0)
			{
				if (($r['district_owner'] == $ir['guild']) && ($ir['guild'] != 0))
					$border='#28a745';
				else
					$border='#343a40';
				
				$thicc='medium';
			}
			echo "
			<td width='33%' style='background-color:{$color}; border-color:{$border}; border-width:{$thicc};'>
				<b>Y: {$r['district_y']}; X: {$r['district_x']}</b><br />
				Guild: <a href='guilds.php?action=view&id={$r['district_owner']}'>{$api->GuildFetchInfo($r['district_owner'],'guild_name')}</a><br />";
				if (isDistrictAccessible($r['district_id']))
				{
					echo "Warriors: " . number_format($r['district_melee']) . "<br />
					Archers: " . number_format($r['district_range']) . "<br />
					Generals: " . number_format($r['district_general']) . "<br />
					Fortification: " . returnForticationLevel($r['district_id']) . "<br />";
					if (isGuildDistrict($r['district_id']))
					{
						if (isAccessibleFromTile($r['district_id'], $district_id))
							echo "[<a href='?action=moveform&from={$r['district_id']}&to={$district_id}'>Move to Here</a>]";
					}
				}
				"</td>";
		}
		//central
		if ($db->num_rows($C) > 0)
		{
			$r=$db->fetch_row($C);
			$color='#f5c6cb';
			$border='#dee2e6';
			$thicc='tiny';
			if (($r['district_owner'] == $ir['guild']) && ($ir['guild'] != 0))
				$color='#c3e6cb';
			if ($r['district_type'] == 'river')
			{
				$color='#b8daff';
			}
			if ($r['district_type'] == 'elevated')
			{
				$thicc='medium';
				$border='#ffc107';
			}
			if ($r['district_type'] == 'lowered')
			{
				$thicc='medium';
				$border='#f8f9fa';
			}
			if ($r['district_type'] == 'market')
			{
				$border='#17a2b8';
				$thicc='medium';
			}
			if ($r['district_type'] == 'outpost')
			{
				$thicc='medium';
				$border='#9a6790';
			}
			if ($r['district_fortify'] > 0)
			{
				if (($r['district_owner'] == $ir['guild']) && ($ir['guild'] != 0))
					$border='#28a745';
				else
					$border='#343a40';
				
				$thicc='medium';
			}
			echo "
			<td width='33%' style='background-color:{$color}; border-color:{$border}; border-width:{$thicc};'>
				<b>Y: {$r['district_y']}; X: {$r['district_x']}</b><br />
				Guild: <a href='guilds.php?action=view&id={$r['district_owner']}'>{$api->GuildFetchInfo($r['district_owner'],'guild_name')}</a><br />";
				if (isDistrictAccessible($r['district_id']))
				{
					echo "Warriors: " . number_format($r['district_melee']) . "<br />
					Archers: " . number_format($r['district_range']) . "<br />
					Generals: " . number_format($r['district_general']) . "<br />
					Fortification: " . returnForticationLevel($r['district_id']) . "<br />
					<i>Moving from here</i>";
				}
				"</td>";
		}
		//East
		if ($db->num_rows($E) > 0)
		{
			$r=$db->fetch_row($E);
			$color='#f5c6cb';
			$border='#dee2e6';
			$thicc='tiny';
			if (($r['district_owner'] == $ir['guild']) && ($ir['guild'] != 0))
				$color='#c3e6cb';
			if ($r['district_type'] == 'river')
			{
				$color='#b8daff';
			}
			if ($r['district_type'] == 'elevated')
			{
				$thicc='medium';
				$border='#ffc107';
			}
			if ($r['district_type'] == 'lowered')
			{
				$thicc='medium';
				$border='#f8f9fa';
			}
			if ($r['district_type'] == 'market')
			{
				$border='#17a2b8';
				$thicc='medium';
			}
			if ($r['district_type'] == 'outpost')
			{
				$thicc='medium';
				$border='#9a6790';
			}
			if ($r['district_fortify'] > 0)
			{
				if (($r['district_owner'] == $ir['guild']) && ($ir['guild'] != 0))
					$border='#28a745';
				else
					$border='#343a40';
				
				$thicc='medium';
			}
			echo "
			<td width='33%' style='background-color:{$color}; border-color:{$border}; border-width:{$thicc};'>
				<b>Y: {$r['district_y']}; X: {$r['district_x']}</b><br />
				Guild: <a href='guilds.php?action=view&id={$r['district_owner']}'>{$api->GuildFetchInfo($r['district_owner'],'guild_name')}</a><br />";
				if (isDistrictAccessible($r['district_id']))
				{
					echo "Warriors: " . number_format($r['district_melee']) . "<br />
					Archers: " . number_format($r['district_range']) . "<br />
					Generals: " . number_format($r['district_general']) . "<br />
					Fortification: " . returnForticationLevel($r['district_id']) . "<br />";
					if (isGuildDistrict($r['district_id']))
					{
						if (isAccessibleFromTile($r['district_id'], $district_id))
							echo "[<a href='?action=moveform&from={$r['district_id']}&to={$district_id}'>Move to Here</a>]";
					}
				}
				"</td>";
		}
	echo "</tr>";
	//Bottom
	echo "<tr>";
	//South West
		if ($db->num_rows($SW) > 0)
		{
			$r=$db->fetch_row($SW);
			$color='#f5c6cb';
			$border='#dee2e6';
			$thicc='tiny';
			if (($r['district_owner'] == $ir['guild']) && ($ir['guild'] != 0))
				$color='#c3e6cb';
			if ($r['district_type'] == 'river')
			{
				$color='#b8daff';
			}
			if ($r['district_type'] == 'elevated')
			{
				$thicc='medium';
				$border='#ffc107';
			}
			if ($r['district_type'] == 'lowered')
			{
				$thicc='medium';
				$border='#f8f9fa';
			}
			if ($r['district_type'] == 'market')
			{
				$border='#17a2b8';
				$thicc='medium';
			}
			if ($r['district_type'] == 'outpost')
			{
				$thicc='medium';
				$border='#9a6790';
			}
			if ($r['district_fortify'] > 0)
			{
				if (($r['district_owner'] == $ir['guild']) && ($ir['guild'] != 0))
					$border='#28a745';
				else
					$border='#343a40';
				
				$thicc='medium';
			}
			echo "
			<td width='33%' style='background-color:{$color}; border-color:{$border}; border-width:{$thicc};'>
				<b>Y: {$r['district_y']}; X: {$r['district_x']}</b><br />
				Guild: <a href='guilds.php?action=view&id={$r['district_owner']}'>{$api->GuildFetchInfo($r['district_owner'],'guild_name')}</a><br />";
				if (isDistrictAccessible($r['district_id']))
				{
					echo "Warriors: " . number_format($r['district_melee']) . "<br />
					Archers: " . number_format($r['district_range']) . "<br />
					Generals: " . number_format($r['district_general']) . "<br />
					Fortification: " . returnForticationLevel($r['district_id']) . "<br />";
					if (isGuildDistrict($r['district_id']))
					{
						if (isAccessibleFromTile($r['district_id'], $district_id))
							echo "[<a href='?action=moveform&from={$r['district_id']}&to={$district_id}'>Move to Here</a>]";
					}
				}
				"</td>";
		}
		//South
		if ($db->num_rows($S) > 0)
		{
			$r=$db->fetch_row($S);
			$color='#f5c6cb';
			$border='#dee2e6';
			$thicc='tiny';
			if (($r['district_owner'] == $ir['guild']) && ($ir['guild'] != 0))
				$color='#c3e6cb';
			if ($r['district_type'] == 'river')
			{
				$color='#b8daff';
			}
			if ($r['district_type'] == 'elevated')
			{
				$thicc='medium';
				$border='#ffc107';
			}
			if ($r['district_type'] == 'lowered')
			{
				$thicc='medium';
				$border='#f8f9fa';
			}
			if ($r['district_type'] == 'market')
			{
				$border='#17a2b8';
				$thicc='medium';
			}
			if ($r['district_type'] == 'outpost')
			{
				$thicc='medium';
				$border='#9a6790';
			}
			if ($r['district_fortify'] > 0)
			{
				if (($r['district_owner'] == $ir['guild']) && ($ir['guild'] != 0))
					$border='#28a745';
				else
					$border='#343a40';
				
				$thicc='medium';
			}
			echo "
			<td width='33%' style='background-color:{$color}; border-color:{$border}; border-width:{$thicc};'>
				<b>Y: {$r['district_y']}; X: {$r['district_x']}</b><br />
				Guild: <a href='guilds.php?action=view&id={$r['district_owner']}'>{$api->GuildFetchInfo($r['district_owner'],'guild_name')}</a><br />";
				if (isDistrictAccessible($r['district_id']))
				{
					echo "Warriors: " . number_format($r['district_melee']) . "<br />
					Archers: " . number_format($r['district_range']) . "<br />
					Generals: " . number_format($r['district_general']) . "<br />
					Fortification: " . returnForticationLevel($r['district_id']) . "<br />";
					if (isGuildDistrict($r['district_id']))
					{
						if (isAccessibleFromTile($r['district_id'], $district_id))
							echo "[<a href='?action=moveform&from={$r['district_id']}&to={$district_id}'>Move to Here</a>]";
					}
				}
				"</td>";
		}
		//South East
		if ($db->num_rows($SE) > 0)
		{
			$r=$db->fetch_row($SE);
			$color='#f5c6cb';
			$border='#dee2e6';
			$thicc='tiny';
			if (($r['district_owner'] == $ir['guild']) && ($ir['guild'] != 0))
				$color='#c3e6cb';
			if ($r['district_type'] == 'river')
			{
				$color='#b8daff';
			}
			if ($r['district_type'] == 'elevated')
			{
				$thicc='medium';
				$border='#ffc107';
			}
			if ($r['district_type'] == 'lowered')
			{
				$thicc='medium';
				$border='#f8f9fa';
			}
			if ($r['district_type'] == 'market')
			{
				$border='#17a2b8';
				$thicc='medium';
			}
			if ($r['district_type'] == 'outpost')
			{
				$thicc='medium';
				$border='#9a6790';
			}
			if ($r['district_fortify'] > 0)
			{
				if (($r['district_owner'] == $ir['guild']) && ($ir['guild'] != 0))
					$border='#28a745';
				else
					$border='#343a40';
				
				$thicc='medium';
			}
			echo "
			<td width='33%' style='background-color:{$color}; border-color:{$border}; border-width:{$thicc};'>
				<b>Y: {$r['district_y']}; X: {$r['district_x']}</b><br />
				Guild: <a href='guilds.php?action=view&id={$r['district_owner']}'>{$api->GuildFetchInfo($r['district_owner'],'guild_name')}</a><br />";
				if (isDistrictAccessible($r['district_id']))
				{
					echo "Warriors: " . number_format($r['district_melee']) . "<br />
					Archers: " . number_format($r['district_range']) . "<br />
					Generals: " . number_format($r['district_general']) . "<br />
					Fortification: " . returnForticationLevel($r['district_id']) . "<br />";
					if (isGuildDistrict($r['district_id']))
					{
						if (isAccessibleFromTile($r['district_id'], $district_id))
							echo "[<a href='?action=moveform&from={$r['district_id']}&to={$district_id}'>Move to Here</a>]";
					}
				}
				"</td>";
		}
	echo "</tr>";
	echo "</table>";
	
}
function attack()
{
	global $districtConfig, $userid, $db, $api, $h, $ir, $gdi;
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
	$r=$db->fetch_row($central);
	$NW=$db->query("SELECT * FROM `guild_districts` WHERE `district_x` = ({$r['district_x']} - 1) AND `district_y` = ({$r['district_y']} - 1)");
	$N=$db->query("SELECT * FROM `guild_districts` WHERE `district_x` = {$r['district_x']} AND `district_y` = ({$r['district_y']} - 1)");
	$NE=$db->query("SELECT * FROM `guild_districts` WHERE `district_x` = ({$r['district_x']} + 1) AND `district_y` = ({$r['district_y']} - 1)");
	
	$W=$db->query("SELECT * FROM `guild_districts` WHERE `district_x` = ({$r['district_x']} - 1) AND `district_y` = {$r['district_y']}");
	$C=$db->query("SELECT * FROM `guild_districts` WHERE `district_x` = {$r['district_x']} AND `district_y` = {$r['district_y']}");
	$E=$db->query("SELECT * FROM `guild_districts` WHERE `district_x` = ({$r['district_x']} + 1) AND `district_y` = {$r['district_y']}");
	
	$SW=$db->query("SELECT * FROM `guild_districts` WHERE `district_x` = ({$r['district_x']} - 1) AND `district_y` = ({$r['district_y']} + 1)");
	$S=$db->query("SELECT * FROM `guild_districts` WHERE `district_x` = {$r['district_x']} AND `district_y` = ({$r['district_y']} + 1)");
	$SE=$db->query("SELECT * FROM `guild_districts` WHERE `district_x` = ({$r['district_x']} + 1) AND `district_y` = ({$r['district_y']} + 1)");
	
	echo "<table class='table table-bordered table-dark'>";
	//Top
	echo "<tr>";
		//North West
		if ($db->num_rows($NW) > 0)
		{
			$r=$db->fetch_row($NW);
			$color='#f5c6cb';
			$border='#dee2e6';
			$thicc='tiny';
			if (($r['district_owner'] == $ir['guild']) && ($ir['guild'] != 0))
				$color='#c3e6cb';
			if ($r['district_type'] == 'river')
			{
				$color='#b8daff';
			}
			if ($r['district_type'] == 'elevated')
			{
				$thicc='medium';
				$border='#ffc107';
			}
			if ($r['district_type'] == 'lowered')
			{
				$thicc='medium';
				$border='#f8f9fa';
			}
			if ($r['district_type'] == 'market')
			{
				$border='#17a2b8';
				$thicc='medium';
			}
			if ($r['district_type'] == 'outpost')
			{
				$thicc='medium';
				$border='#9a6790';
			}
			if ($r['district_fortify'] > 0)
			{
				if (($r['district_owner'] == $ir['guild']) && ($ir['guild'] != 0))
					$border='#28a745';
				else
					$border='#343a40';
				
				$thicc='medium';
			}
			echo "
			<td width='33%' style='background-color:{$color}; border-color:{$border}; border-width:{$thicc};'>
				<b>Y: {$r['district_y']}; X: {$r['district_x']}</b><br />
				Guild: <a href='guilds.php?action=view&id={$r['district_owner']}'>{$api->GuildFetchInfo($r['district_owner'],'guild_name')}</a><br />";
				if (isDistrictAccessible($r['district_id']))
				{
					echo "Warriors: " . number_format($r['district_melee']) . "<br />
					Archers: " . number_format($r['district_range']) . "<br />
					Generals: " . number_format($r['district_general']) . "<br />
					Fortification: " . returnForticationLevel($r['district_id']) . "<br />";
					if (isGuildDistrict($r['district_id']))
					{
						if (isAccessibleFromTile($r['district_id'], $district_id))
							echo "[<a href='?action=attackform&from={$r['district_id']}&to={$district_id}'>Attack from Here</a>]";
					}
				}
				"</td>";
		}
		//North
		if ($db->num_rows($N) > 0)
		{
			$r=$db->fetch_row($N);
			$color='#f5c6cb';
			$border='#dee2e6';
			$thicc='tiny';
			if (($r['district_owner'] == $ir['guild']) && ($ir['guild'] != 0))
				$color='#c3e6cb';
			if ($r['district_type'] == 'river')
			{
				$color='#b8daff';
			}
			if ($r['district_type'] == 'elevated')
			{
				$thicc='medium';
				$border='#ffc107';
			}
			if ($r['district_type'] == 'lowered')
			{
				$thicc='medium';
				$border='#f8f9fa';
			}
			if ($r['district_type'] == 'market')
			{
				$border='#17a2b8';
				$thicc='medium';
			}
			if ($r['district_type'] == 'outpost')
			{
				$thicc='medium';
				$border='#9a6790';
			}
			if ($r['district_fortify'] > 0)
			{
				if (($r['district_owner'] == $ir['guild']) && ($ir['guild'] != 0))
					$border='#28a745';
				else
					$border='#343a40';
				
				$thicc='medium';
			}
			echo "
			<td width='33%' style='background-color:{$color}; border-color:{$border}; border-width:{$thicc};'>
				<b>Y: {$r['district_y']}; X: {$r['district_x']}</b><br />
				Guild: <a href='guilds.php?action=view&id={$r['district_owner']}'>{$api->GuildFetchInfo($r['district_owner'],'guild_name')}</a><br />";
				if (isDistrictAccessible($r['district_id']))
				{
					echo "Warriors: " . number_format($r['district_melee']) . "<br />
					Archers: " . number_format($r['district_range']) . "<br />
					Generals: " . number_format($r['district_general']) . "<br />
					Fortification: " . returnForticationLevel($r['district_id']) . "<br />";
				}
				if (isGuildDistrict($r['district_id']))
					{
						if (isAccessibleFromTile($r['district_id'], $district_id))
							echo "[<a href='?action=attackform&from={$r['district_id']}&to={$district_id}'>Attack from Here</a>]";
					}
				"</td>";
		}
		//North East
		if ($db->num_rows($NE) > 0)
		{
			$r=$db->fetch_row($NE);
			$color='#f5c6cb';
			$border='#dee2e6';
			$thicc='tiny';
			if (($r['district_owner'] == $ir['guild']) && ($ir['guild'] != 0))
				$color='#c3e6cb';
			if ($r['district_type'] == 'river')
			{
				$color='#b8daff';
			}
			if ($r['district_type'] == 'elevated')
			{
				$thicc='medium';
				$border='#ffc107';
			}
			if ($r['district_type'] == 'lowered')
			{
				$thicc='medium';
				$border='#f8f9fa';
			}
			if ($r['district_type'] == 'market')
			{
				$border='#17a2b8';
				$thicc='medium';
			}
			if ($r['district_type'] == 'outpost')
			{
				$thicc='medium';
				$border='#9a6790';
			}
			if ($r['district_fortify'] > 0)
			{
				if (($r['district_owner'] == $ir['guild']) && ($ir['guild'] != 0))
					$border='#28a745';
				else
					$border='#343a40';
				
				$thicc='medium';
			}
			echo "
			<td width='33%' style='background-color:{$color}; border-color:{$border}; border-width:{$thicc};'>
				<b>Y: {$r['district_y']}; X: {$r['district_x']}</b><br />
				Guild: <a href='guilds.php?action=view&id={$r['district_owner']}'>{$api->GuildFetchInfo($r['district_owner'],'guild_name')}</a><br />";
				if (isDistrictAccessible($r['district_id']))
				{
					echo "Warriors: " . number_format($r['district_melee']) . "<br />
					Archers: " . number_format($r['district_range']) . "<br />
					Generals: " . number_format($r['district_general']) . "<br />
					Fortification: " . returnForticationLevel($r['district_id']) . "<br />";
					if (isGuildDistrict($r['district_id']))
					{
						if (isAccessibleFromTile($r['district_id'], $district_id))
							echo "[<a href='?action=attackform&from={$r['district_id']}&to={$district_id}'>Attack from Here</a>]";
					}
				}
				"</td>";
		}
	echo "</tr>";
	//Center
	echo"<tr>";
	//West
		if ($db->num_rows($W) > 0)
		{
			$r=$db->fetch_row($W);
			$color='#f5c6cb';
			$border='#dee2e6';
			$thicc='tiny';
			if (($r['district_owner'] == $ir['guild']) && ($ir['guild'] != 0))
				$color='#c3e6cb';
			if ($r['district_type'] == 'river')
			{
				$color='#b8daff';
			}
			if ($r['district_type'] == 'elevated')
			{
				$thicc='medium';
				$border='#ffc107';
			}
			if ($r['district_type'] == 'lowered')
			{
				$thicc='medium';
				$border='#f8f9fa';
			}
			if ($r['district_type'] == 'market')
			{
				$border='#17a2b8';
				$thicc='medium';
			}
			if ($r['district_type'] == 'outpost')
			{
				$thicc='medium';
				$border='#9a6790';
			}
			if ($r['district_fortify'] > 0)
			{
				if (($r['district_owner'] == $ir['guild']) && ($ir['guild'] != 0))
					$border='#28a745';
				else
					$border='#343a40';
				
				$thicc='medium';
			}
			echo "
			<td width='33%' style='background-color:{$color}; border-color:{$border}; border-width:{$thicc};'>
				<b>Y: {$r['district_y']}; X: {$r['district_x']}</b><br />
				Guild: <a href='guilds.php?action=view&id={$r['district_owner']}'>{$api->GuildFetchInfo($r['district_owner'],'guild_name')}</a><br />";
				if (isDistrictAccessible($r['district_id']))
				{
					echo "Warriors: " . number_format($r['district_melee']) . "<br />
					Archers: " . number_format($r['district_range']) . "<br />
					Generals: " . number_format($r['district_general']) . "<br />
					Fortification: " . returnForticationLevel($r['district_id']) . "<br />";
					if (isGuildDistrict($r['district_id']))
					{
						if (isAccessibleFromTile($r['district_id'], $district_id))
							echo "[<a href='?action=attackform&from={$r['district_id']}&to={$district_id}'>Attack from Here</a>]";
					}
				}
				"</td>";
		}
		//central
		if ($db->num_rows($C) > 0)
		{
			$r=$db->fetch_row($C);
			$color='#f5c6cb';
			$border='#dee2e6';
			$thicc='tiny';
			if (($r['district_owner'] == $ir['guild']) && ($ir['guild'] != 0))
				$color='#c3e6cb';
			if ($r['district_type'] == 'river')
			{
				$color='#b8daff';
			}
			if ($r['district_type'] == 'elevated')
			{
				$thicc='medium';
				$border='#ffc107';
			}
			if ($r['district_type'] == 'lowered')
			{
				$thicc='medium';
				$border='#f8f9fa';
			}
			if ($r['district_type'] == 'market')
			{
				$border='#17a2b8';
				$thicc='medium';
			}
			if ($r['district_type'] == 'outpost')
			{
				$thicc='medium';
				$border='#9a6790';
			}
			if ($r['district_fortify'] > 0)
			{
				if (($r['district_owner'] == $ir['guild']) && ($ir['guild'] != 0))
					$border='#28a745';
				else
					$border='#343a40';
				
				$thicc='medium';
			}
			echo "
			<td width='33%' style='background-color:{$color}; border-color:{$border}; border-width:{$thicc};'>
				<b>Y: {$r['district_y']}; X: {$r['district_x']}</b><br />
				Guild: <a href='guilds.php?action=view&id={$r['district_owner']}'>{$api->GuildFetchInfo($r['district_owner'],'guild_name')}</a><br />";
				if (isDistrictAccessible($r['district_id']))
				{
					echo "Warriors: " . number_format($r['district_melee']) . "<br />
					Archers: " . number_format($r['district_range']) . "<br />
					Generals: " . number_format($r['district_general']) . "<br />
					Fortification: " . returnForticationLevel($r['district_id']) . "<br />
					[<a href='?action=attackformbarracks&to={$district_id}'>Attack from Barracks</a>]";
				}
				"</td>";
		}
		//East
		if ($db->num_rows($E) > 0)
		{
			$r=$db->fetch_row($E);
			$color='#f5c6cb';
			$border='#dee2e6';
			$thicc='tiny';
			if (($r['district_owner'] == $ir['guild']) && ($ir['guild'] != 0))
				$color='#c3e6cb';
			if ($r['district_type'] == 'river')
			{
				$color='#b8daff';
			}
			if ($r['district_type'] == 'elevated')
			{
				$thicc='medium';
				$border='#ffc107';
			}
			if ($r['district_type'] == 'lowered')
			{
				$thicc='medium';
				$border='#f8f9fa';
			}
			if ($r['district_type'] == 'market')
			{
				$border='#17a2b8';
				$thicc='medium';
			}
			if ($r['district_type'] == 'outpost')
			{
				$thicc='medium';
				$border='#9a6790';
			}
			if ($r['district_fortify'] > 0)
			{
				if (($r['district_owner'] == $ir['guild']) && ($ir['guild'] != 0))
					$border='#28a745';
				else
					$border='#343a40';
				
				$thicc='medium';
			}
			echo "
			<td width='33%' style='background-color:{$color}; border-color:{$border}; border-width:{$thicc};'>
				<b>Y: {$r['district_y']}; X: {$r['district_x']}</b><br />
				Guild: <a href='guilds.php?action=view&id={$r['district_owner']}'>{$api->GuildFetchInfo($r['district_owner'],'guild_name')}</a><br />";
				if (isDistrictAccessible($r['district_id']))
				{
					echo "Warriors: " . number_format($r['district_melee']) . "<br />
					Archers: " . number_format($r['district_range']) . "<br />
					Generals: " . number_format($r['district_general']) . "<br />
					Fortification: " . returnForticationLevel($r['district_id']) . "<br />";
					if (isGuildDistrict($r['district_id']))
					{
						if (isAccessibleFromTile($r['district_id'], $district_id))
							echo "[<a href='?action=attackform&from={$r['district_id']}&to={$district_id}'>Attack from Here</a>]";
					}
				}
				"</td>";
		}
	echo "</tr>";
	//Bottom
	echo "<tr>";
	//South West
		if ($db->num_rows($SW) > 0)
		{
			$r=$db->fetch_row($SW);
			$color='#f5c6cb';
			$border='#dee2e6';
			$thicc='tiny';
			if (($r['district_owner'] == $ir['guild']) && ($ir['guild'] != 0))
				$color='#c3e6cb';
			if ($r['district_type'] == 'river')
			{
				$color='#b8daff';
			}
			if ($r['district_type'] == 'elevated')
			{
				$thicc='medium';
				$border='#ffc107';
			}
			if ($r['district_type'] == 'lowered')
			{
				$thicc='medium';
				$border='#f8f9fa';
			}
			if ($r['district_type'] == 'market')
			{
				$border='#17a2b8';
				$thicc='medium';
			}
			if ($r['district_type'] == 'outpost')
			{
				$thicc='medium';
				$border='#9a6790';
			}
			if ($r['district_fortify'] > 0)
			{
				if (($r['district_owner'] == $ir['guild']) && ($ir['guild'] != 0))
					$border='#28a745';
				else
					$border='#343a40';
				
				$thicc='medium';
			}
			echo "
			<td width='33%' style='background-color:{$color}; border-color:{$border}; border-width:{$thicc};'>
				<b>Y: {$r['district_y']}; X: {$r['district_x']}</b><br />
				Guild: <a href='guilds.php?action=view&id={$r['district_owner']}'>{$api->GuildFetchInfo($r['district_owner'],'guild_name')}</a><br />";
				if (isDistrictAccessible($r['district_id']))
				{
					echo "Warriors: " . number_format($r['district_melee']) . "<br />
					Archers: " . number_format($r['district_range']) . "<br />
					Generals: " . number_format($r['district_general']) . "<br />
					Fortification: " . returnForticationLevel($r['district_id']) . "<br />";
					if (isGuildDistrict($r['district_id']))
					{
						if (isAccessibleFromTile($r['district_id'], $district_id))
							echo "[<a href='?action=attackform&from={$r['district_id']}&to={$district_id}'>Attack from Here</a>]";
					}
				}
				"</td>";
		}
		//South
		if ($db->num_rows($S) > 0)
		{
			$r=$db->fetch_row($S);
			$color='#f5c6cb';
			$border='#dee2e6';
			$thicc='tiny';
			if (($r['district_owner'] == $ir['guild']) && ($ir['guild'] != 0))
				$color='#c3e6cb';
			if ($r['district_type'] == 'river')
			{
				$color='#b8daff';
			}
			if ($r['district_type'] == 'elevated')
			{
				$thicc='medium';
				$border='#ffc107';
			}
			if ($r['district_type'] == 'lowered')
			{
				$thicc='medium';
				$border='#f8f9fa';
			}
			if ($r['district_type'] == 'market')
			{
				$border='#17a2b8';
				$thicc='medium';
			}
			if ($r['district_type'] == 'outpost')
			{
				$thicc='medium';
				$border='#9a6790';
			}
			if ($r['district_fortify'] > 0)
			{
				if (($r['district_owner'] == $ir['guild']) && ($ir['guild'] != 0))
					$border='#28a745';
				else
					$border='#343a40';
				
				$thicc='medium';
			}
			echo "
			<td width='33%' style='background-color:{$color}; border-color:{$border}; border-width:{$thicc};'>
				<b>Y: {$r['district_y']}; X: {$r['district_x']}</b><br />
				Guild: <a href='guilds.php?action=view&id={$r['district_owner']}'>{$api->GuildFetchInfo($r['district_owner'],'guild_name')}</a><br />";
				if (isDistrictAccessible($r['district_id']))
				{
					echo "Warriors: " . number_format($r['district_melee']) . "<br />
					Archers: " . number_format($r['district_range']) . "<br />
					Generals: " . number_format($r['district_general']) . "<br />
					Fortification: " . returnForticationLevel($r['district_id']) . "<br />";
					if (isGuildDistrict($r['district_id']))
					{
						if (isAccessibleFromTile($r['district_id'], $district_id))
							echo "[<a href='?action=attackform&from={$r['district_id']}&to={$district_id}'>Attack from Here</a>]";
					}
				}
				"</td>";
		}
		//South East
		if ($db->num_rows($SE) > 0)
		{
			$r=$db->fetch_row($SE);
			$color='#f5c6cb';
			$border='#dee2e6';
			$thicc='tiny';
			if (($r['district_owner'] == $ir['guild']) && ($ir['guild'] != 0))
				$color='#c3e6cb';
			if ($r['district_type'] == 'river')
			{
				$color='#b8daff';
			}
			if ($r['district_type'] == 'elevated')
			{
				$thicc='medium';
				$border='#ffc107';
			}
			if ($r['district_type'] == 'lowered')
			{
				$thicc='medium';
				$border='#f8f9fa';
			}
			if ($r['district_type'] == 'market')
			{
				$border='#17a2b8';
				$thicc='medium';
			}
			if ($r['district_type'] == 'outpost')
			{
				$thicc='medium';
				$border='#9a6790';
			}
			if ($r['district_fortify'] > 0)
			{
				if (($r['district_owner'] == $ir['guild']) && ($ir['guild'] != 0))
					$border='#28a745';
				else
					$border='#343a40';
				
				$thicc='medium';
			}
			echo "
			<td width='33%' style='background-color:{$color}; border-color:{$border}; border-width:{$thicc};'>
				<b>Y: {$r['district_y']}; X: {$r['district_x']}</b><br />
				Guild: <a href='guilds.php?action=view&id={$r['district_owner']}'>{$api->GuildFetchInfo($r['district_owner'],'guild_name')}</a><br />";
				if (isDistrictAccessible($r['district_id']))
				{
					echo "Warriors: " . number_format($r['district_melee']) . "<br />
					Archers: " . number_format($r['district_range']) . "<br />
					Generals: " . number_format($r['district_general']) . "<br />
					Fortification: " . returnForticationLevel($r['district_id']) . "<br />";
					if (isGuildDistrict($r['district_id']))
					{
						if (isAccessibleFromTile($r['district_id'], $district_id))
							echo "[<a href='?action=attackform&from={$r['district_id']}&to={$district_id}'>Attack from Here</a>]";
					}
				}
				"</td>";
		}
	echo "</tr>";
	echo "</table>";
	
}
function attackfromtile()
{
	global $districtConfig, $userid, $db, $api, $h, $ir, $gdi;
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
		alert('danger',"Uh Oh!","You do not have access to this tile!.",true,'guild_district.php');
		die($h->endpage());
	}
	if (!isAccessibleFromTile($attack_from, $attack_to))
	{
		alert('danger',"Uh Oh!","You do not have access to this tile!.",true,'guild_district.php');
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
		if (($archers + $warriors) == 0)
		{
			alert('danger',"Uh Oh!","You must attack with at least one unit.",true,'guild_district.php');
			die($h->endpage());
		}
		$attBuff = 1.0;
		$defBuff = 1.0;
		if ($r['district_type'] == 'elevated')
		{
			$attBuff=$attBuff + 0.25;
		}
		if ($r2['district_type'] == 'elevated')
		{
			$defBuff=$defBuff + 0.25;
		}
		if ($r2['district_type'] == 'lowered')
		{
			$attBuff=$attBuff + 0.25;
		}
		if ($r['district_type'] == 'lowered')
		{
			$defBuff=$defBuff + 0.25;
		}
		if ($r2['district_fortify'] > 0)
		{
			$defBuff = $defBuff + ($r2['district_fortify'] * $districtConfig['fortifyBuffMulti']);
		}
		$results=json_decode(doAttack($warriors,$archers,$r2['district_melee'],$r2['district_range'], $r2['district_general'], $attBuff, $defBuff), true);
		updateTileTroops($r2['district_id'],
					$results['defense_warrior_lost']*-1,
					$results['defense_archer_lost']*-1,
					$results['defense_general_lost']*-1);
		updateTileTroops($r['district_id'],
					$results['attack_warrior_lost']*-1,
					$results['attack_archer_lost']*-1, 0);
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
		$i=logBattle($ir['guild'], $r2['district_owner'], $warriors, $archers, $results['attack_warrior_lost'], 
		$results['attack_archer_lost'], $r2['district_melee'], $r2['district_range'], $results['defense_warrior_lost'], 
		$results['defense_archer_lost'], $r2['district_general'], $r2['district_fortify'], $winner);
		echo "You deploy " . number_format($warriors) . " Warriors and " . number_format($archers) . " Archers to take on 
		" . number_format($r2['district_melee']) . " Warriors and " . number_format($r2['district_range']) . " Archers. There is 
		" . number_format($r2['district_general']) . " enemy generals on the battlefield today.<br />
		<b>Battle-log</b><br />
		<i>You {$status}!</i><br />
		Friendly Warriors Killed: " . number_format($results['attack_warrior_lost']) . "<br />
		Friendly Archers Killed: " . number_format($results['attack_archer_lost']) . "<br />
		Enemy Warriors Killed: " . number_format($results['defense_warrior_lost']) . "<br />
		Enemy Archers Killed: " . number_format($results['defense_archer_lost']) . "<br />
		Enemy Generals Executed: " . number_format($results['defense_general_lost']) . "<br />";
		$db->query("UPDATE `guild_district_info` SET `moves` = `moves` - 1 WHERE `guild_id` = {$ir['guild']}");
		if ($status == 'won')
		{
			$db->query("UPDATE `guild_district_info` SET `moves` = `moves` + 2 WHERE `guild_id` = {$ir['guild']}");
			echo "<i>This tile now belongs to your guild. Remember to move troops to this tile or it may be conquered from you.</i>";
			setTileOwnership($r2['district_id'], $ir['guild']);
			$api->GuildAddNotification($ir['guild'],"Your guild attacked a district and emerged victorious! View the battle report <a href='guild_district.php?action=viewreport&id={$i}'>here</a>.");
			$api->GuildAddNotification($r2['district_owner'],"A district owned by your guild was attacked and lost. View the battle report <a href='guild_district.php?action=viewreport&id={$i}'>here</a>.");
			$api->SystemLogsAdd($userid,"district","Conquered tile  " . resolveCoordinates($r2['district_id']) . " using " . number_format($warriors) . " Warriors (Lost " . number_format($results['attack_warrior_lost']) . ") and " . number_format($archers) . " Archers (Lost " . number_format($results['attack_archer_lost']) . "), launched from tile " . resolveCoordinates($r['district_id']) . ". Enemy lost " . number_format($results['defense_warrior_lost']) . " Warriors, " . number_format($results['defense_archer_lost']) . " Archers and " . number_format($results['defense_general_lost']) . " Generals.");
		}
		if ($status == 'lost')
		{
			echo "<i>You have failed to capture this tile. Rebuild your army and try again.</i>";
			$api->GuildAddNotification($ir['guild'],"Your guild attacked a district and lost! View the battle report <a href='guild_district.php?action=viewreport&id={$i}'>here</a>.");
			$api->GuildAddNotification($r2['district_owner'],"A district owned by your guild was attacked and  and your guild emerged victorious. View the battle report <a href='guild_district.php?action=viewreport&id={$i}'>here</a>.");
			$api->SystemLogsAdd($userid,"district","Lost attacking tile  " . resolveCoordinates($r2['district_id']) . " using " . number_format($warriors) . " Warriors (Lost " . number_format($results['attack_warrior_lost']) . ") and " . number_format($archers) . " Archers (Lost " . number_format($results['attack_archer_lost']) . "), launched from tile " . resolveCoordinates($r2['district_id']) . " . Enemy lost " . number_format($results['defense_warrior_lost']) . " Warriors, " . number_format($results['defense_archer_lost']) . " Archers and " . number_format($results['defense_general_lost']) . " Generals.");
		}
	}
	else
	{
		echo "You are attempting to invade a district... Input how many units you wish to deploy to invade this tile.<br />
		<form method='post'>
			<b>Warriors</b> (vs " . number_format($r2['district_melee']) . ")<br />
			<input type='number' name='warriors' class='form-control' value='{$r['district_melee']}' max='{$r['district_melee']}' min='0'><br />
			<b>Archers</b> (vs " . number_format($r2['district_range']) . ")<br />
			<input type='number' name='archers' class='form-control' value='{$r['district_range']}' max='{$r['district_range']}' min='0'><br />
			<input type='submit' class='btn btn-success' value='Invade'><br />
			<small>+{$r2['district_general']} generals on this tile.</small>
		</form>";
	}
	
}
function attackfrombarracks()
{
	global $districtConfig, $userid, $db, $api, $h, $ir, $gdi;
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
		alert('danger',"Uh Oh!","You do not have access to this tile!.",true,'guild_district.php');
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
		if ($warriors > $gdi['barracks_warriors'])
		{
			alert('danger',"Uh Oh!","You do not have that many Warriors in your barracks!",true,'guild_district.php');
			die($h->endpage());
		}
		if ($archers > $gdi['barracks_warriors'])
		{
			alert('danger',"Uh Oh!","You do not have that many Archers in your barracks!",true,'guild_district.php');
			die($h->endpage());
		}
		if (($archers + $warriors) == 0)
		{
			alert('danger',"Uh Oh!","You must attack with at least one unit.",true,'guild_district.php');
			die($h->endpage());
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
		$results=json_decode(doAttack($warriors,$archers,$r2['district_melee'],$r2['district_range'], $r2['district_general'], $attBuff, $defBuff), true);
		updateTileTroops($r2['district_id'],
					$results['defense_warrior_lost']*-1,
					$results['defense_archer_lost']*-1,
					$results['defense_general_lost']*-1);
		updateBarracksTroops($ir['guild'],
					$results['attack_warrior_lost']*-1,
					$results['attack_archer_lost']*-1, 0);
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
		$i=logBattle($ir['guild'], $r2['district_owner'], $warriors, $archers, $results['attack_warrior_lost'], 
		$results['attack_archer_lost'], $r2['district_melee'], $r2['district_range'], $results['defense_warrior_lost'], 
		$results['defense_archer_lost'], $r2['district_general'], $r2['district_fortify'], $winner);
		echo "You deploy " . number_format($warriors) . " Warriors and " . number_format($archers) . " Archers to take on 
		" . number_format($r2['district_melee']) . " Warriors and " . number_format($r2['district_range']) . " Archers. There is 
		" . number_format($r2['district_general']) . " enemy generals on the battlefield today.<br />
		<b>Battle-log</b><br />
		<i>You {$status}!</i><br />
		Friendly Warriors Killed: " . number_format($results['attack_warrior_lost']) . "<br />
		Friendly Archers Killed: " . number_format($results['attack_archer_lost']) . "<br />
		Enemy Warriors Killed: " . number_format($results['defense_warrior_lost']) . "<br />
		Enemy Archers Killed: " . number_format($results['defense_archer_lost']) . "<br />
		Enemy Generals Executed: " . number_format($results['defense_general_lost']) . "<br />";
		$db->query("UPDATE `guild_district_info` SET `moves` = `moves` - 1 WHERE `guild_id` = {$ir['guild']}");
		if ($status == 'won')
		{
			$db->query("UPDATE `guild_district_info` SET `moves` = `moves` + 2 WHERE `guild_id` = {$ir['guild']}");
			echo "<i>This tile now belongs to your guild. Remember to move troops to this tile or it may be conquered from you.</i>";
			setTileOwnership($r2['district_id'], $ir['guild']);
			$api->GuildAddNotification($ir['guild'],"Your guild attacked a district and emerged victorious! View the battle report <a href='guild_district.php?action=viewreport&id={$i}'>here</a>.");
			$api->GuildAddNotification($r2['district_owner'],"A district owned by your guild was attacked and lost. View the battle report <a href='guild_district.php?action=viewreport&id={$i}'>here</a>.");
			$api->SystemLogsAdd($userid,"district","Conquered tile  " . resolveCoordinates($r2['district_id']) . " using " . number_format($warriors) . " Warriors (Lost " . number_format($results['attack_warrior_lost']) . ") and " . number_format($archers) . " Archers (Lost " . number_format($results['attack_archer_lost']) . "), launched from tile " . resolveCoordinates($r2['district_id']) . ". Enemy lost " . number_format($results['defense_warrior_lost']) . " Warriors, " . number_format($results['defense_archer_lost']) . " Archers and " . number_format($results['defense_general_lost']) . " Generals.");
		}
		if ($status == 'lost')
		{
			echo "<i>You have failed to capture this tile. Rebuild your army and try again.</i>";
			$api->GuildAddNotification($ir['guild'],"Your guild attacked a district and lost! View the battle report <a href='guild_district.php?action=viewreport&id={$i}'>here</a>.");
			$api->GuildAddNotification($r2['district_owner'],"A district owned by your guild was attacked and  and your guild emerged victorious. View the battle report <a href='guild_district.php?action=viewreport&id={$i}'>here</a>.");
			$api->SystemLogsAdd($userid,"district","Lost attacking tile  " . resolveCoordinates($r2['district_id']) . " using " . number_format($warriors) . " Warriors (Lost " . number_format($results['attack_warrior_lost']) . ") and " . number_format($archers) . " Archers (Lost " . number_format($results['attack_archer_lost']) . "), launched from tile " . resolveCoordinates($r2['district_id']) . " . Enemy lost " . number_format($results['defense_warrior_lost']) . " Warriors, " . number_format($results['defense_archer_lost']) . " Archers and " . number_format($results['defense_general_lost']) . " Generals.");
		}
	}
	else
	{
		echo "You are attempting to invade a district... Input how many units you wish to deploy to invade this tile.<br />
		<form method='post'>
			<b>Warriors</b> (vs " . number_format($r2['district_melee']) . ")<br />
			<input type='number' name='warriors' class='form-control' value='{$gdi['barracks_warriors']}' max='{$gdi['barracks_warriors']}' min='0'><br />
			<b>Archers</b> (vs " . number_format($r2['district_range']) . ")<br />
			<input type='number' name='archers' class='form-control' value='{$gdi['barracks_archers']}' max='{$gdi['barracks_archers']}' min='0'><br />
			<input type='submit' class='btn btn-success' value='Invade'><br />
			<small>+{$r2['district_general']} generals on this tile.</small>
		</form>";
	}
	
}
function battlereport()
{
	global $districtConfig, $userid, $db, $api, $h, $ir, $gdi;
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
	$q = $db->query("/*qc=on*/SELECT * FROM `guild_district_battlelog` WHERE `log_id` = {$id} AND (`attacker` = {$ir['guild']} OR `defender` = {$ir['guild']})");
	if ($db->num_rows($q) == 0) 
	{
		alert('danger', "Uh Oh!", "This battle report does not exist, or does not involve your guild.", true, 'index.php');
		die($h->endpage());
	}
	$r = $db->fetch_row($q);
	$db->free_result($q);
	echo "
	<div class='row'>
		<div class='col-md'>
			Attacker: {$api->GuildFetchInfo($r['attacker'],'guild_name')}<br />
			Warriors: " . number_format($r['attack_war']) . " (-" . number_format($r['attack_war_lost']) . ")<br />
			Archers: " . number_format($r['attack_arch']) . " (-" . number_format($r['attack_arch_lost']) . ")<br />
			Time: " . DateTime_Parse($r['log_time']) . "
		</div>
		<div class='col-md'>
			{$api->GuildFetchInfo($r['defender'],'guild_name')}<br />
			Warriors: " . number_format($r['defend_war']) . " (-" . number_format($r['defend_war_lost']) . ")<br />
			Archers: " . number_format($r['defend_arch']) . " (-" . number_format($r['defend_archer_lost']) . ")<br />
			Generals: " . number_format($r['defend_general']) . "<br />
			Fortification Level: " . number_format($r['defend_fortify']) . "
		</div>
	</div>";
}
function movefromtile()
{
	global $districtConfig, $userid, $db, $api, $h, $ir, $gdi;
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
		alert('danger',"Uh Oh!","You do not have access to this tile!.",true,'guild_district.php');
		die($h->endpage());
	}
	if (!isAccessibleFromTile($attack_from, $attack_to))
	{
		alert('danger',"Uh Oh!","You do not have access to this tile!.",true,'guild_district.php');
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
		if ($generals > 0)
		{
			if (($generals + $r['district_general']) > $districtConfig['maxGenerals'])
			{
				alert('danger',"Uh Oh!","You may only have {$districtConfig['maxGenerals']} Generals on a tile.",true,'guild_district.php');
				die($h->endpage());
			}
		}
		$db->query("UPDATE `guild_district_info` SET `moves` = `moves` - 1 WHERE `guild_id` = {$ir['guild']}");
		$api->SystemLogsAdd($userid,"district","Moved " . number_format($warriors) . " Warriors, " . number_format($archers) . " Archers and  " . number_format($generals) . " Generals from Tile " . resolveCoordinates($r2['district_id']) . " to Tile " . resolveCoordinates($r['district_id']) .".");
		updateTileTroops($r2['district_id'], $warriors*-1, $archers*-1, $generals*-1);
		updateTileTroops($r['district_id'], $warriors, $archers, $generals);
		alert('success',"","You have successfully moved " . number_format($warriors) . " Warriors, " . number_format($archers) . " Archers and " . number_format($generals) . " Generals to this tile.");
	}
	else
	{
		echo "You are attempting to move units from one district to another. This will cost you one movement. Please enter how many units you wish to send 
		to the receiving district.<br />
		<form method='post'>
			<b>Warriors</b> (Have " . number_format($r2['district_melee']) . ")<br />
			<input type='number' name='warriors' class='form-control' value='{$r2['district_melee']}' max='{$r2['district_melee']}' min='0'><br />
			<b>Archers</b> (Have " . number_format($r2['district_range']) . ")<br />
			<input type='number' name='archers' class='form-control' value='{$r2['district_range']}' max='{$r2['district_range']}' min='0'><br />
			<b>Generals</b> (Have " . number_format($r2['district_general']) . ")<br />
			<input type='number' name='generals' class='form-control' value='{$r2['district_general']}' max='{$r2['district_general']}' min='0'><br />
			<input type='submit' class='btn btn-success' value='Move'>
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
		alert('danger',"Uh Oh!","You do not have access to this tile!.",true,'guild_district.php');
		die($h->endpage());
	}
	$q2=$db->query("SELECT * FROM `guild_districts` WHERE `district_id` = {$attack_to}");
	$r2=$db->fetch_row($q2);
	$neededTokens=$districtConfig['copperPerFortify']*($r2['district_fortify'] + 1);
	$guildcurr = $db->fetch_single($db->query("SELECT `guild_seccurr` FROM `guild` WHERE `guild_id` = {$ir['guild']}"));
	$neededXP = round($districtConfig['xpPerFortify'] * (($r2['district_fortify'] + 1) * $districtConfig['xpPerFortifyMulti']));
	if (isset($_POST['warriors']))
	{
		if ($r2['district_fortify'] >= $districtConfig['maxFortify'])
		{
			alert('danger',"Uh Oh!","You cannot fortify this tile anymore.",true,'guild_district.php');
			die($h->endpage());
		}
		if ($guildcurr < $neededTokens)
		{
			alert('danger',"Uh Oh!","Your guild needs " . number_format($neededTokens) . " Chivalry Tokens before you can fortify this district.",true,'guild_district.php');
			die($h->endpage());
		}
		if (!($api->GuildHasXP($ir['guild'], $neededXP)))
		{
			alert('danger',"Uh Oh!","Your guild needs " . number_format($neededXP) . " Guild Experience before you can fortify this district.",true,'guild_district.php');
			die($h->endpage());
		}
		$api->SystemLogsAdd($userid,"district","Spent " . number_format($neededXP) . " Guild Experience and " . number_format($neededTokens) . " Chivalry Tokens to fortify tile " . resolveCoordinates($attack_to) .".");
		$api->GuildRemoveXP($ir['guild'],$neededXP);
		$db->query("UPDATE `guild` SET `guild_seccurr` = `guild_seccurr` - {$neededTokens} WHERE `guild_id` = {$ir['guild']}");
		addToEconomyLog('Districts','token', $neededTokens*-1);
		$db->query("UPDATE `guild_districts` SET `district_fortify` = `district_fortify` + 1 WHERE `district_id` = {$attack_to}");
		alert('success',"","You have successfully fortified this tile at the cost of " . number_format($neededXP) . " Guild XP and " . number_format($neededTokens) . " Chivalry Tokens.",true,'guild_district.php');
	}
	else
	{
		echo "You are attempting to fortify this tile. Please click the button to confirm.<br />
		For this district, you will need " . number_format($neededXP) . " Guild XP and " . number_format($neededTokens) . " Chivalry Tokens.<br />
		Districts may be fortified up to a maximum of {$districtConfig['maxFortify']} times.<br />
		<form method='post'>
			<input type='hidden' name='warriors' value='true'>
			<input type='submit' class='btn btn-success' value='Fortify'>
		</form>";
	}
	
}
function movefrombarracks()
{
	global $districtConfig, $userid, $db, $api, $h, $ir, $gdi;
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
		alert('danger',"Uh Oh!","You do not have access to this tile!.",true,'guild_district.php');
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
		if (($generals + $r2['district_general']) > $districtConfig['maxGenerals'])
		{
			alert('danger',"Uh Oh!","You may only have {$districtConfig['maxGenerals']} Generals on a tile.",true,'guild_district.php');
			die($h->endpage());
		}
		$db->query("UPDATE `guild_district_info` SET `moves` = `moves` - 1 WHERE `guild_id` = {$ir['guild']}");
		updateBarracksTroops($ir['guild'], $warriors*-1, $archers*-1, $generals*-1);
		updateTileTroops($attack_to, $warriors, $archers, $generals);
		alert('success',"","You have successfully moved " . number_format($warriors) . " Warriors, " . number_format($archers) . " Archers and " . number_format($generals) . " Generals from your barracks to this tile.", true, 'guild_district.php');
		$api->SystemLogsAdd($userid,"district","Moved " . number_format($warriors) . " Warriors and " . number_format($archers) . " Archers to Tile " . resolveCoordinates($attack_to) . " from their barracks.");
	}
	else
	{
		echo "You are attempting to deploy units from your barracks to the battlefield. This will cost you one movement. Units cannot be returned to the barracks after being deployed. Please enter how many units you wish to send 
		to the receiving district.<br />
		<form method='post'>
			<b>Warriors</b> (Have " . number_format($gdi['barracks_warriors']) . ")<br />
			<input type='number' name='warriors' class='form-control' value='{$gdi['barracks_warriors']}' max='{$gdi['barracks_warriors']}' min='0'><br />
			<b>Archers</b> (Have " . number_format($gdi['barracks_archers']) . ")<br />
			<input type='number' name='archers' class='form-control' value='{$gdi['barracks_archers']}' max='{$gdi['barracks_archers']}' min='0'><br />
			<b>Generals</b> (Have " . number_format($gdi['barracks_generals']) . ")<br />
			<input type='number' name='generals' class='form-control' value='{$gdi['barracks_generals']}' max='{$gdi['barracks_generals']}' min='0'><br />
			<input type='submit' class='btn btn-success' value='Deploy Units'>
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
	$districtOwn=$db->num_rows($db->query("SELECT `district_id` FROM `guild_districts` WHERE `district_owner` = {$gdi['guild_id']}"));
	echo "
    <div class='row'>
        <div class='col-12 col-md-6 col-xxl-4 col-xxxl-3'>
            <div class='card'>
                <div class='card-header'>
                    Barracks
                </div>
                <div class='card-body'>
                    <div class='row'>
                        <div class='col-3 col-md-6'>
                            Warriors
                        </div>
                        <div class='col-3 col-md-6'>
                            " . number_format($gdi['barracks_warriors']) . "
                        </div>
                        <div class='col-3 col-md-6'>
                            Archers
                        </div>
                        <div class='col-3 col-md-6'>
                            " . number_format($gdi['barracks_archers']) . "
                        </div>
                        <div class='col-3 col-md-6'>
                            Generals
                        </div>
                        <div class='col-3 col-md-6'>
                            " . number_format($gdi['barracks_generals']) . "
                        </div>
                    </div>
                </div>
            </div>
            <br />
        </div>
        <div class='col-12 col-md-6 col-xxl-4 col-xxxl-3'>
            <div class='card'>
                <div class='card-header'>
                    Upkeep
                </div>
                <div class='card-body'>
                    <div class='row'>
                        <div class='col-4'>
                            Warriors
                        </div>
                        <div class='col-8'>
                            " . number_format(countDeployedWarriors($gdi['guild_id']) * $districtConfig['WarriorCostDaily']) . " Copper
                        </div>
                        <div class='col-4'>
                            Archers
                        </div>
                        <div class='col-8'>
                            " . number_format(countDeployedArchers($gdi['guild_id']) * $districtConfig['ArcherCostDaily']) . " Copper
                        </div>
                        <div class='col-4'>
                            Generals
                        </div>
                        <div class='col-8'>
                            " . number_format(countDeployedGenerals($gdi['guild_id']) * $districtConfig['GeneralCostDaily']) . " Copper
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
                        <div class='col-4 col-sm-6'>
                            Warriors
                        </div>
                        <div class='col-8 col-sm-6'>
                            " . number_format(countDeployedWarriors($gdi['guild_id'])) . "
                        </div>
                        <div class='col-4 col-sm-6'>
                            Archers
                        </div>
                        <div class='col-8 col-sm-6'>
                            " . number_format(countDeployedArchers($gdi['guild_id'])) . "
                        </div>
                        <div class='col-4 col-sm-6'>
                            Generals
                        </div>
                        <div class='col-8 col-sm-6'>
                            " . number_format(countDeployedGenerals($gdi['guild_id'])) . "
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
                        <div class='col-6 col-md-7 col-lg-8 col-xl-6 col-xxl-8'>
                            Moves Available
                        </div>
                        <div class='col-6 col-md-5 col-lg-4 col-xl-2 col-xxl-4'>
                            " . number_format($gdi['moves']) . "
                        </div>
                        <div class='col-6 col-md-7 col-lg-8 col-xl-6 col-xxl-8'>
                            Districts Controlled
                        </div>
                        <div class='col-6 col-md-5 col-lg-4 col-xl-3 col-xxl-4'>
                            " . number_format($districtOwn) . "
                        </div>
                        <div class='col-6 col-md-7 col-lg-8 col-xl-6 col-xxl-8'>
                            Warriors Bought
                        </div>
                        <div class='col-6 col-md-5 col-lg-4'>
                            " . number_format($gdi['warriors_bought']) . "
                        </div>
                        <div class='col-6 col-md-7 col-lg-8 col-xl-6 col-xxl-8'>
                            Archers Bought
                        </div>
                        <div class='col-6 col-md-5 col-lg-4'>
                            " . number_format($gdi['archers_bought']) . "
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
	global $db, $api, $userid, $ir, $gdi, $districtConfig, $h;
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
	$gi = $db->fetch_row($db->query("SELECT * FROM `guild` WHERE `guild_id` = {$ir['guild']}"));
	if (countTowns() > 0)
	{
		$districtConfig['ArcherCost'] = round($districtConfig['ArcherCost'] - ($districtConfig['ArcherCost']*(0.15*countTowns())));
		$districtConfig['WarriorCost'] = round($districtConfig['WarriorCost'] - ($districtConfig['WarriorCost']*(0.15*countTowns())));
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
		$dailyBuyWarriors = round($dailyBuyWarriors + ($dailyBuyWarriors * (0.15 * countOutposts())));
		$dailyBuyArchers = round($dailyBuyArchers + ($dailyBuyArchers * (0.15 * countOutposts())));
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
			alert('danger',"Uh Oh!","You do not have enough room in your barracks for " . number_format($warriors) . " more warriors. The maximum your barracks may support is " . number_format($districtConfig['BarracksMaxWarriors']) . " Warriors.",true,'guild_district.php');
			die($h->endpage());
		}
		//Recruited archer and current barracks is over maximum barracks size
		if (($archers + $gdi['barracks_archers']) > $districtConfig['BarracksMaxArchers'])
		{
			alert('danger',"Uh Oh!","You do not have enough room in your barracks for " . number_format($archers) . " more warriors. The maximum your barracks may support is " . number_format($districtConfig['BarracksMaxArchers']) . " Archers.",true,'guild_district.php');
			die($h->endpage());
		}
		//Recruited warriors and current barracks is over maximum barracks size
		if ($warriors > $currentBuyWarriors)
		{
			alert('danger',"Uh Oh!","You cannot buy " . number_format($warriors) . " Warriors right now. You may only buy " . number_format($currentBuyWarriors) . " Warriors right now.",true,'guild_district.php');
			die($h->endpage());
		}
		if ($archers > $currentBuyArchers)
		{
			alert('danger',"Uh Oh!","You cannot buy " . number_format($archers) . " Warriors right now. You may only buy " . number_format($currentBuyArchers) . " Archers right now.",true,'guild_district.php');
			die($h->endpage());
		}
		$archerTotal=$archers*$districtConfig['ArcherCost'];
		$warriorTotal=$warriors*$districtConfig['WarriorCost'];
		$allTotal = $warriorTotal + $archerTotal;
		if ($gi['guild_primcurr'] < $allTotal)
		{
			alert('danger',"Uh Oh!","Your guild needs " . number_format($allTotal) . " Copper Coins in it's vault before you can purchase that many units.",true,'guild_district.php');
			die($h->endpage());
		}
		$db->query("UPDATE `guild_district_info` SET 
					`warriors_bought` = `warriors_bought` + {$warriors},
					`archers_bought` = `archers_bought` + {$archers}
					WHERE `guild_id` = {$ir['guild']}");
		updateBarracksTroops($ir['guild'], $warriors, $archers, 0);
		$db->query("UPDATE `guild` SET `guild_primcurr` = `guild_primcurr` - {$allTotal} WHERE `guild_id` = {$ir['guild']}");
		alert('success',"Success!","You have spent " . number_format($allTotal) . " Copper Coins for " . number_format($warriors) . " Warriors and " . number_format($archers) . " Archers for your guild.",true,'guild_district.php');
		$api->GuildAddNotification($ir['guild'],"<a href='profile.php?user={$userid}'>{$ir['username']}</a> has spent " . number_format($allTotal) . " Copper Coins for " . number_format($warriors) . " Warriors and " . number_format($archers) . " Archers.");
		$api->SystemLogsAdd($userid,"district","spent " . number_format($allTotal) . " Copper Coins for " . number_format($warriors) . " Warriors and " . number_format($archers) . " Archers");
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
                        has " . number_format($gi['guild_primcurr']) . " Copper Coins in its vault. Warriors have 
                        a daily upkeep fee of " . number_format($districtConfig['WarriorCostDaily']) . " Copper Coins, and
                        Archers have a daily upkeep fee of " . number_format($districtConfig['ArcherCostDaily']) . " Copper Coins.
                        This fee is taken from your guild's vault every day at midnight gametime.
                        <hr />
                        <div class='row'>
                            <div class='col-4 col-md-2'>
                                Warriors
                            </div>
                            <div class='col-8 col-md-4 col-xl-3'>
                                <small>" . number_format($districtConfig['WarriorCost']) . " Copper Coins each</small>
                            </div>
                            <div class='col-12 col-md-6 col-xl-7'>
                                <input type='number' name='warriors' value='{$currentBuyWarriors}' max='{$currentBuyWarriors}' min='0' required='1' class='form-control'>
                            </div>
                            <div class='col-4 col-md-2'>
                                Archers
                            </div>
                            <div class='col-8 col-md-4 col-xl-3'>
                                <small>" . number_format($districtConfig['ArcherCost']) . " Copper Coins each</small>
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
	global $db, $api, $userid, $ir, $gdi, $districtConfig, $h;
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
	$gi = $db->fetch_row($db->query("SELECT * FROM `guild` WHERE `guild_id` = {$ir['guild']}"));
	$availableGenerals = (floor(countActiveTroops() / $districtConfig['GeneralTroops']) - countGenerals());
	if ($availableGenerals < 0)
		$availableGenerals = 0;
	if (countTowns() > 0)
		$districtConfig['GeneralCost'] = round($districtConfig['GeneralCost'] - ($districtConfig['GeneralCost']*(0.15*countTowns())));
	if (isset($_POST['warriors']))
	{
	
		$generals = (isset($_POST['warriors']) && is_numeric($_POST['warriors'])) ? abs($_POST['warriors']) : 0;
		//is the form completely submitted?
		if ($generals == 0)
		{
			alert('danger',"Uh Oh!","Please fill out the form completely before submitting.",true,'guild_district.php');
			die($h->endpage());
		}
		$generalsTotal=$generals*$districtConfig['GeneralCost'];
		if ($gi['guild_primcurr'] < $generalsTotal)
		{
			alert('danger',"Uh Oh!","Your guild needs " . number_format($generalsTotal) . " Copper Coins in it's vault before you can hire that many generals.",true,'guild_district.php');
			die($h->endpage());
		}
		updateBarracksTroops($ir['guild'], 0, 0, $generals);
		$db->query("UPDATE `guild` SET `guild_primcurr` = `guild_primcurr` - {$generalsTotal} WHERE `guild_id` = {$ir['guild']}");
		alert('success',"Success!","You have spent " . number_format($generalsTotal) . " Copper Coins and hired " . number_format($generals) . " Warriors for your guild.",true,'guild_district.php');
		$api->GuildAddNotification($ir['guild'],"<a href='profile.php?user={$userid}'>{$ir['username']}</a> has spent " . number_format($generalsTotal) . " Copper Coins and hired" . number_format($generals) . " Generals for your Guild District.");
		$api->SystemLogsAdd($userid,"district","Spent " . number_format($generalsTotal) . " Copper Coins for " . number_format($generals) . " Generals.");
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
                        Hire General
                    </div>
                    <div class='card-body'>
                        Generals are a purely defensive unit. You may place them on tiles you own for a 
                        " . round($districtConfig['GeneralBuff']*100) . "% defensive buff. You may only place {$districtConfig['maxGenerals']}
                        Generals on a tile at a time. If the tile is lost, your general will be executed.
                        How many generals do you wish to hire? You may hire {$availableGenerals} at this time. Your guild has 
                        " . number_format($gi['guild_primcurr']) . " Copper Coins in its vault. Generals have 
                        a daily upkeep fee of " . number_format($districtConfig['GeneralCostDaily']) . " Copper Coins.
                        This fee is taken from your guild's vault every day at midnight gametime.
                        <hr />
                        <div class='row'>
                            <div class='col-4 col-md-2'>
                                Warriors
                            </div>
                            <div class='col-8 col-md-4 col-xl-3'>
                                <small>" . number_format($districtConfig['GeneralCost']) . " Copper Coins each</small>
                            </div>
                            <div class='col-12 col-md-6 col-xl-7'>
                                <input type='number' name='warriors' value='{$availableGenerals}' max='{$availableGenerals}' min='0' required='1' class='form-control'>
                            </div>
                            <div class='col-12'>
                                <input type='submit' value='Hire General' class='btn btn-success btn-block'>
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
$h->endpage();

//Functions needed to make the module work.