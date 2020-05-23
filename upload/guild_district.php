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
$districtConfig['GeneralBuff'] = 0.5;
$districtConfig['GeneralTroops'] = 7500;
$districtConfig['GeneralCost'] = 125000;
$districtConfig['GeneralCostDaily'] = 12500;
$districtConfig['WarriorCost'] = 5000;
$districtConfig['WarriorCostDaily'] = 500;
$districtConfig['ArcherCost'] = 8500;
$districtConfig['ArcherCostDaily'] = 1000;
//end module config

require('globals.php');
echo "<h3>Guild Districts</h3><hr />
	[<a href='#' data-toggle='modal' data-target='#district_info'>Info</a>] || [<a href='guild_district.php'>Home</a>] || [<a href='?action=guildinfo'>Your Guild Info</a>] || [<a href='?action=buy'>Buy Troops</a>]<hr />";
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
	case 'test':
        test();
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
	updateBarracksTroops(1,5000,2500,5);
	$db->query("UPDATE `guild_district_info` SET `moves` = `moves` + 2 WHERE `guild_id` = 1");
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
				Guild ID: {$r['district_owner']}<br />
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
				Guild ID: {$r['district_owner']}<br />";
				if (isDistrictAccessible($r['district_id']))
				{
					echo "Warriors: " . number_format($r['district_melee']) . "<br />
					Archers: " . number_format($r['district_range']) . "<br />
					Generals: " . number_format($r['district_general']) . "<br />";
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
				Guild ID: {$r['district_owner']}<br />";
				if (isDistrictAccessible($r['district_id']))
				{
					echo "Warriors: " . number_format($r['district_melee']) . "<br />
					Archers: " . number_format($r['district_range']) . "<br />
					Generals: " . number_format($r['district_general']) . "<br />";
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
				Guild ID: {$r['district_owner']}<br />";
				if (isDistrictAccessible($r['district_id']))
				{
					echo "Warriors: " . number_format($r['district_melee']) . "<br />
					Archers: " . number_format($r['district_range']) . "<br />
					Generals: " . number_format($r['district_general']) . "<br />";
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
				Guild ID: {$r['district_owner']}<br />";
				if (isDistrictAccessible($r['district_id']))
				{
					echo "Warriors: " . number_format($r['district_melee']) . "<br />
					Archers: " . number_format($r['district_range']) . "<br />
					Generals: " . number_format($r['district_general']) . "<br />";
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
				Guild ID: {$r['district_owner']}<br />";
				if (isDistrictAccessible($r['district_id']))
				{
					echo "Warriors: " . number_format($r['district_melee']) . "<br />
					Archers: " . number_format($r['district_range']) . "<br />
					Generals: " . number_format($r['district_general']) . "<br />";
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
				Guild ID: {$r['district_owner']}<br />";
				if (isDistrictAccessible($r['district_id']))
				{
					echo "Warriors: " . number_format($r['district_melee']) . "<br />
					Archers: " . number_format($r['district_range']) . "<br />
					Generals: " . number_format($r['district_general']) . "<br />";
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
				Guild ID: {$r['district_owner']}<br />";
				if (isDistrictAccessible($r['district_id']))
				{
					echo "Warriors: " . number_format($r['district_melee']) . "<br />
					Archers: " . number_format($r['district_range']) . "<br />
					Generals: " . number_format($r['district_general']) . "<br />";
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
				Guild ID: {$r['district_owner']}<br />";
				if (isDistrictAccessible($r['district_id']))
				{
					echo "Warriors: " . number_format($r['district_melee']) . "<br />
					Archers: " . number_format($r['district_range']) . "<br />
					Generals: " . number_format($r['district_general']) . "<br />";
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
				Guild ID: {$r['district_owner']}<br />";
				if (isDistrictAccessible($r['district_id']))
				{
					echo "Warriors: " . number_format($r['district_melee']) . "<br />
					Archers: " . number_format($r['district_range']) . "<br />
					Generals: " . number_format($r['district_general']) . "<br />";
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
				Guild ID: {$r['district_owner']}<br />";
				if (isDistrictAccessible($r['district_id']))
				{
					echo "Warriors: " . number_format($r['district_melee']) . "<br />
					Archers: " . number_format($r['district_range']) . "<br />
					Generals: " . number_format($r['district_general']) . "<br />";
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
				Guild ID: {$r['district_owner']}<br />";
				if (isDistrictAccessible($r['district_id']))
				{
					echo "Warriors: " . number_format($r['district_melee']) . "<br />
					Archers: " . number_format($r['district_range']) . "<br />
					Generals: " . number_format($r['district_general']) . "<br />";
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
				Guild ID: {$r['district_owner']}<br />";
				if (isDistrictAccessible($r['district_id']))
				{
					echo "Warriors: " . number_format($r['district_melee']) . "<br />
					Archers: " . number_format($r['district_range']) . "<br />
					Generals: " . number_format($r['district_general']) . "<br />";
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
				Guild ID: {$r['district_owner']}<br />";
				if (isDistrictAccessible($r['district_id']))
				{
					echo "Warriors: " . number_format($r['district_melee']) . "<br />
					Archers: " . number_format($r['district_range']) . "<br />
					Generals: " . number_format($r['district_general']) . "<br />";
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
				Guild ID: {$r['district_owner']}<br />";
				if (isDistrictAccessible($r['district_id']))
				{
					echo "Warriors: " . number_format($r['district_melee']) . "<br />
					Archers: " . number_format($r['district_range']) . "<br />
					Generals: " . number_format($r['district_general']) . "<br />
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
				Guild ID: {$r['district_owner']}<br />";
				if (isDistrictAccessible($r['district_id']))
				{
					echo "Warriors: " . number_format($r['district_melee']) . "<br />
					Archers: " . number_format($r['district_range']) . "<br />
					Generals: " . number_format($r['district_general']) . "<br />";
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
				Guild ID: {$r['district_owner']}<br />";
				if (isDistrictAccessible($r['district_id']))
				{
					echo "Warriors: " . number_format($r['district_melee']) . "<br />
					Archers: " . number_format($r['district_range']) . "<br />
					Generals: " . number_format($r['district_general']) . "<br />";
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
				Guild ID: {$r['district_owner']}<br />";
				if (isDistrictAccessible($r['district_id']))
				{
					echo "Warriors: " . number_format($r['district_melee']) . "<br />
					Archers: " . number_format($r['district_range']) . "<br />
					Generals: " . number_format($r['district_general']) . "<br />";
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
				Guild ID: {$r['district_owner']}<br />";
				if (isDistrictAccessible($r['district_id']))
				{
					echo "Warriors: " . number_format($r['district_melee']) . "<br />
					Archers: " . number_format($r['district_range']) . "<br />
					Generals: " . number_format($r['district_general']) . "<br />";
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
				Guild ID: {$r['district_owner']}<br />";
				if (isDistrictAccessible($r['district_id']))
				{
					echo "Warriors: " . number_format($r['district_melee']) . "<br />
					Archers: " . number_format($r['district_range']) . "<br />
					Generals: " . number_format($r['district_general']) . "<br />";
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
				Guild ID: {$r['district_owner']}<br />";
				if (isDistrictAccessible($r['district_id']))
				{
					echo "Warriors: " . number_format($r['district_melee']) . "<br />
					Archers: " . number_format($r['district_range']) . "<br />
					Generals: " . number_format($r['district_general']) . "<br />";
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
				Guild ID: {$r['district_owner']}<br />";
				if (isDistrictAccessible($r['district_id']))
				{
					echo "Warriors: " . number_format($r['district_melee']) . "<br />
					Archers: " . number_format($r['district_range']) . "<br />
					Generals: " . number_format($r['district_general']) . "<br />";
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
				Guild ID: {$r['district_owner']}<br />";
				if (isDistrictAccessible($r['district_id']))
				{
					echo "Warriors: " . number_format($r['district_melee']) . "<br />
					Archers: " . number_format($r['district_range']) . "<br />
					Generals: " . number_format($r['district_general']) . "<br />";
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
				Guild ID: {$r['district_owner']}<br />";
				if (isDistrictAccessible($r['district_id']))
				{
					echo "Warriors: " . number_format($r['district_melee']) . "<br />
					Archers: " . number_format($r['district_range']) . "<br />
					Generals: " . number_format($r['district_general']) . "<br />
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
				Guild ID: {$r['district_owner']}<br />";
				if (isDistrictAccessible($r['district_id']))
				{
					echo "Warriors: " . number_format($r['district_melee']) . "<br />
					Archers: " . number_format($r['district_range']) . "<br />
					Generals: " . number_format($r['district_general']) . "<br />";
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
				Guild ID: {$r['district_owner']}<br />";
				if (isDistrictAccessible($r['district_id']))
				{
					echo "Warriors: " . number_format($r['district_melee']) . "<br />
					Archers: " . number_format($r['district_range']) . "<br />
					Generals: " . number_format($r['district_general']) . "<br />";
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
				Guild ID: {$r['district_owner']}<br />";
				if (isDistrictAccessible($r['district_id']))
				{
					echo "Warriors: " . number_format($r['district_melee']) . "<br />
					Archers: " . number_format($r['district_range']) . "<br />
					Generals: " . number_format($r['district_general']) . "<br />";
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
				Guild ID: {$r['district_owner']}<br />";
				if (isDistrictAccessible($r['district_id']))
				{
					echo "Warriors: " . number_format($r['district_melee']) . "<br />
					Archers: " . number_format($r['district_range']) . "<br />
					Generals: " . number_format($r['district_general']) . "<br />";
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
			$defBuff=$defBuff - 0.25;
		}
		if ($r2['district_type'] == 'elevated')
		{
			$attBuff=$attBuff - 0.25;
			$defBuff=$defBuff + 0.25;
		}
		if ($r2['district_type'] == 'lowered')
		{
			$attBuff=$attBuff + 0.25;
			$defBuff=$defBuff - 0.25;
		}
		if ($r['district_type'] == 'lowered')
		{
			$attBuff=$attBuff - 0.25;
			$defBuff=$defBuff + 0.25;
		}
		if ($r['district_fortify'] > 0)
		{
			$attBuff = $attBuff + ($r['district_fortify'] * 0.1);
			$defBuff = $defBuff - ($r['district_fortify'] * 0.1);
		}
		if ($r2['district_fortify'] > 0)
		{
			$defBuff = $defBuff + ($r2['district_fortify'] * 0.1);
			$attBuff = $attBuff - ($r2['district_fortify'] * 0.1);
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
			$status = "won";
		else
			$status = "lost";
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
			$api->GuildAddNotification($ir['guild'],"Your guild attacked a district and emerged victorious! You lost " . number_format($results['attack_warrior_lost']) . " Warriors and " . number_format($results['attack_archer_lost']) . " Archers.");
			$api->GuildAddNotification($r2['district_owner'],"A district owned by your guild was attacked and lost. While defending, you lost " . number_format($results['defense_warrior_lost']) . " Warriors, " . number_format($results['defense_archer_lost']) . " Archers and " . number_format($results['defense_general_lost']) . " Generals.");
			$api->SystemLogsAdd($userid,"district","Conquered tile  " . resolveCoordinates($r2['district_id']) . " using " . number_format($warriors) . " Warriors (Lost " . number_format($results['attack_warrior_lost']) . ") and " . number_format($archers) . " Archers (Lost " . number_format($results['attack_archer_lost']) . "), launched from tile " . resolveCoordinates($r['district_id']) . ". Enemy lost " . number_format($results['defense_warrior_lost']) . " Warriors, " . number_format($results['defense_archer_lost']) . " Archers and " . number_format($results['defense_general_lost']) . " Generals.");
		}
		if ($status == 'lost')
		{
			echo "<i>You have failed to capture this tile. Rebuild your army and try again.</i>";
			$api->GuildAddNotification($ir['guild'],"Your guild attacked a district and lost! You lost " . number_format($results['attack_warrior_lost']) . " Warriors and " . number_format($results['attack_archer_lost']) . " Archers.");
			$api->GuildAddNotification($r2['district_owner'],"A district owned by your guild was attacked and emerged victorious. While defending, you lost " . number_format($results['defense_warrior_lost']) . " Warriors, " . number_format($results['defense_archer_lost']) . " Archers and " . number_format($results['defense_general_lost']) . " Generals.");
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
			$attBuff=$attBuff - 0.25;
			$defBuff=$defBuff + 0.25;
		}
		if ($r2['district_type'] == 'lowered')
		{
			$attBuff=$attBuff + 0.25;
			$defBuff=$defBuff - 0.25;
		}
		if ($r2['district_fortify'] > 0)
		{
			$defBuff = $defBuff + ($r2['district_fortify'] * 0.1);
			$attBuff = $attBuff - ($r2['district_fortify'] * 0.1);
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
			$status = "won";
		else
			$status = "lost";
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
			$api->GuildAddNotification($ir['guild'],"Your guild attacked a district and emerged victorious! You lost " . number_format($results['attack_warrior_lost']) . " Warriors and " . number_format($results['attack_archer_lost']) . " Archers.");
			$api->GuildAddNotification($r2['district_owner'],"A district owned by your guild was attacked and lost. While defending, you lost " . number_format($results['defense_warrior_lost']) . " Warriors, " . number_format($results['defense_archer_lost']) . " Archers and " . number_format($results['defense_general_lost']) . " Generals.");
			$api->SystemLogsAdd($userid,"district","Conquered tile  " . resolveCoordinates($r2['district_id']) . " using " . number_format($warriors) . " Warriors (Lost " . number_format($results['attack_warrior_lost']) . ") and " . number_format($archers) . " Archers (Lost " . number_format($results['attack_archer_lost']) . "), launched from their barracks. Enemy lost " . number_format($results['defense_warrior_lost']) . " Warriors, " . number_format($results['defense_archer_lost']) . " Archers and " . number_format($results['defense_general_lost']) . " Generals.");
		}
		if ($status == 'lost')
		{
			echo "<i>You have failed to capture this tile. Rebuild your army and try again.</i>";
			$api->GuildAddNotification($ir['guild'],"Your guild attacked a district and lost! You lost " . number_format($results['attack_warrior_lost']) . " Warriors and " . number_format($results['attack_archer_lost']) . " Archers.");
			$api->GuildAddNotification($r2['district_owner'],"A district owned by your guild was attacked and emerged victorious. While defending, you lost " . number_format($results['defense_warrior_lost']) . " Warriors, " . number_format($results['defense_archer_lost']) . " Archers and " . number_format($results['defense_general_lost']) . " Generals.");
			$api->SystemLogsAdd($userid,"district","Lost attacking tile  " . resolveCoordinates($r2['district_id']) . " using " . number_format($warriors) . " Warriors (Lost " . number_format($results['attack_warrior_lost']) . ") and " . number_format($archers) . " Archers (Lost " . number_format($results['attack_archer_lost']) . "), launched from their barracks. Enemy lost " . number_format($results['defense_warrior_lost']) . " Warriors, " . number_format($results['defense_archer_lost']) . " Archers and " . number_format($results['defense_general_lost']) . " Generals.");
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
	if ($gdi['moves'] == 0)
	{
		alert('danger',"Uh Oh!","You have ran out of moves for today. Try again tomorrow.",true,'guild_district.php');
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
	$neededCoal=1500*($r2['district_fortify']+1);
	$neededCopper=3200*($r2['district_fortify']+1);
	if (isset($_POST['warriors']))
	{
		if ($r2['district_fortify'] == 9)
		{
			alert('danger',"Uh Oh!","You cannot fortify this tile anymore.",true,'guild_district.php');
			die($h->endpage());
		}
		if (!($api->GuildHasItem($ir['guild'],22,$neededCoal)))
		{
			alert('danger',"Uh Oh!","Your guild needs " . number_format($neededCoal) . " Coal in its armory before you can fortify this tile.",true,'guild_district.php');
			die($h->endpage());
		}
		if (!($api->GuildHasItem($ir['guild'],23,$neededCopper)))
		{
			alert('danger',"Uh Oh!","Your guild needs " . number_format($neededCopper) . " Copper Flakes in its armory before you can fortify this tile.",true,'guild_district.php');
			die($h->endpage());
		}
		if (!($api->GuildHasXP($ir['guild'], 125)))
		{
			alert('danger',"Uh Oh!","Your guild needs 125 Guild Experience before you can fortify this district.",true,'guild_district.php');
			die($h->endpage());
		}
		$api->SystemLogsAdd($userid,"district","Spent 125 Guild Experience, " . number_format($neededCoal) . " Coal and " . number_format($neededCopper) . " Copper Flakes to fortify tile " . resolveCoordinates($attack_to) .".");
		$api->GuildTakeItem($ir['guild'],22,$neededCoal);
		$api->GuildTakeItem($ir['guild'],23,$neededCopper);
		$api->GuildRemoveXP($ir['guild'],125);
		$db->query("UPDATE `guild_district_info` SET `moves` = `moves` - 1 WHERE `guild_id` = {$ir['guild']}");
		$db->query("UPDATE `guild_districts` SET `district_fortify` = `district_fortify` + 1 WHERE `district_id` = {$attack_to}");
		alert('success',"","You have successfully fortified this tile at the cost of 125 Guild XP, " . number_format($neededCoal) . " Coal and " . number_format($neededCopper) . " Copper Flakes.");
	}
	else
	{
		echo "You are attempting to fortify this tile. Please click the button to confirm. It costs 125 Guild XP each upgrade.<br />
		For this district, you will also need " . number_format($neededCoal) . " Coal and " . number_format($neededCopper) . " Copper Flakes.<br />
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
		alert('danger',"Uh Oh!","You have ran out of moves for today. Try again tomorrow.",true,'guild_district.php');
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
	echo "<b>Barracks Warriors:</b> " . number_format($gdi['barracks_warriors']) . "<br />
	<b>Barracks Archers:</b> " . number_format($gdi['barracks_archers']) . "<br />
	<b>Barracks Generals:</b> " . number_format($gdi['barracks_generals']) . "<br />
	<b>Moves Available:</b> " . number_format($gdi['moves']) . "<br />
	<b>Districts Controlled:</b> " . number_format($districtOwn) . "<br />
	<b>Warriors Bought:</b> " . number_format($gdi['warriors_bought']) . "<br />
	<b>Archers Bought:</b> " . number_format($gdi['archers_bought']) . "<br />
	<b>Deployed Warriors:</b> " . number_format(countDeployedWarriors($gdi['guild_id'])) . "<br />
	<b>Deployed Archers:</b> " . number_format(countDeployedArchers($gdi['guild_id'])) . "<br />
	<b>Active Generals:</b> " . number_format(countDeployedGenerals($gdi['guild_id'])) . "<br />
	<b>Warrior Upkeep Cost:</b> " . number_format(countDeployedWarriors($gdi['guild_id']) * $districtConfig['WarriorCostDaily']) . " Copper Coins<br />
	<b>Archer Upkeep Cost:</b> " . number_format(countDeployedArchers($gdi['guild_id']) * $districtConfig['ArcherCostDaily']) . " Copper Coins<br />
	<b>General Upkeep Cost:</b> " . number_format(countDeployedGenerals($gdi['guild_id']) * $districtConfig['GeneralCostDaily']) . " Copper Coins<br />";
	
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
	$guildVault = $db->fetch_single($db->query("SELECT `guild_primcurr` FROM `guild` WHERE `guild_id` = {$ir['guild']}"));
	$perActiveWarrior=40;
	$perActiveArcher=20;
	
	$canBuyArcher = $perActiveArcher * countActiveGuildMembers24Hr($gdi['guild_id']);
	$canBuyWarrior = $perActiveWarrior * countActiveGuildMembers24Hr($gdi['guild_id']);
	
	$maxWarrior=400;
	$maxArcher=200;
	
	$maxBuyWarriors = $maxWarrior - $gdi['warriors_bought'];
	$maxBuyArchers = $maxArcher - $gdi['archers_bought'];
	
	if ($canBuyWarrior > $maxBuyWarriors)
		$canBuyWarrior = $maxBuyWarriors;
	if ($canBuyArcher > $maxBuyArchers)
		$canBuyArcher = $maxBuyArchers;
	$fixedMaxWar = $canBuyWarrior - $gdi['warriors_bought'];
	$fixedMaxArc = $canBuyArcher - $gdi['archers_bought'];

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
		if (($warriors+$gdi['warriors_bought']) > $canBuyWarrior)
		{
			alert('danger',"Uh Oh!","You are trying to buy more warriors than you are allowed to right now.",true,'guild_district.php');
			die($h->endpage());
		}
		if (($archers+$gdi['archers_bought']) > $canBuyArcher)
		{
			alert('danger',"Uh Oh!","You are trying to buy more archers than you are allowed to right now.",true,'guild_district.php');
			die($h->endpage());
		}
		$archerTotal=$archers*$districtConfig['ArcherCost'];
		$warriorTotal=$warriors*$districtConfig['WarriorCost'];
		$allTotal = $warriorTotal + $archerTotal;
		if ($guildVault < $allTotal)
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
		echo "How many troops do you wish to buy? Fill and submit the form to confirm. Your guild has " . number_format($guildVault) . " Copper Coins in its vault.<br />
		<form method='post'>
			<b>Warriors</b> (" . number_format($districtConfig['WarriorCost']) . " Copper Coins each)<br />
			<input type='number' name='warriors' value='{$fixedMaxWar}' max='{$maxBuyWarriors}' min='0' required='1' class='form-control'>
			<b>Archers</b> (" . number_format($districtConfig['ArcherCost']) . " Copper Coins each)<br />
			<input type='number' name='archers' value='{$fixedMaxArc}' max='{$maxBuyArchers}' min='0' required='1' class='form-control'>
			<input type='submit' value='Buy Troops' class='btn btn-success'>
		</form>";
	}
	
}
include('forms/district_popup.php');
$h->endpage();

//Functions needed to make the module work.
function isDistrictAccessible($district_id)
{
	global $db, $ir, $districtConfig;
	$q=$db->query("SELECT * FROM `guild_districts` WHERE `district_id` = {$district_id}");
	if ($ir['guild'] == 0)
		$guild=-1;
	else
		$guild=$ir['guild'];
	$r=$db->fetch_row($q);
	$minX=$r['district_x'] - 1;
	$minY=$r['district_y'] - 1;
	$maxX=$r['district_x'] + 1;
	$maxY=$r['district_y'] + 1;
	$count = $db->num_rows($db->query("SELECT `district_id` 
											FROM `guild_districts` 
											WHERE `district_owner` = {$guild}
											AND (`district_x` >= {$minX} AND `district_x` <= {$maxX})
											AND (`district_y` >= {$minY} AND `district_y` <= {$maxY})"));
	if ($r['district_type'] == 'river')
	{
		return false;
		exit;
	}
	if ($count > 0)
		return true;
	elseif ($r['district_y'] == 1)
		return true;
	elseif ($r['district_y'] == $districtConfig['MaxSizeY'])
		return true;
	elseif ($r['district_x'] == $districtConfig['MaxSizeX'])
		return true;
	elseif ($r['district_x'] == 1)
		return true;
	else
		return false;
}

function isGuildDistrict($district_id)
{
	global $db, $ir, $districtConfig;
	$q=$db->query("SELECT `district_owner` FROM `guild_districts` WHERE `district_id` = {$district_id}");
	if ($ir['guild'] == 0)
		$guild=-1;
	else
		$guild=$ir['guild'];
	$r=$db->fetch_single($q);
	if ($r == $guild)
		return true;
	else
		return false;
}

function doAttack($attackWarrior, $attackArcher, $defenseWarrior, $defenseArcher, $defenseGeneral = 0, $attackBuff = 1.0, $defenseBuff = 1.0)
{
	global $api, $userid, $districtConfig;
	
	$attackTotal = $attackWarrior + $attackArcher;
	$defenseTotal = $defenseWarrior + $defenseArcher;
	
	//results
	$result = array();
	$result['winner'] = '';
	$result['attack_warrior_lost'] = 0;
	$result['attack_archer_lost'] = 0;
	$result['defense_warrior_lost'] = 0;
	$result['defense_archer_lost'] = 0;
	$result['defense_general_lost'] = 0;
	while (empty($result['winner']))
	{
		$defenderRating=0;
		$attackerRating=0;
		$dfndr=0;
		$fght=0;
		
		//Pick defender
		if (($defenseWarrior > 0) && ($defenseArcher > 0))
		{
			$dfndr=mt_rand(1,2);
		}
		elseif (($defenseArcher == 0) && ($defenseWarrior > 0))
		{
			$dfndr = 1;
		}
		elseif (($defenseArcher > 0) && ($defenseWarrior == 0))
		{
			$dfndr = 2;
		}
		else
		{
			$dfndr = 0;
		}
		
		if ($dfndr > 0)
		{
			//Pick attacker
			if (($attackWarrior > 0) && ($attackArcher > 0))
			{
				$fght=mt_rand(1,2);
			}
			elseif (($attackArcher == 0) && ($attackWarrior > 0))
			{
				$fght = 1;
			}
			elseif (($attackArcher > 0) && ($attackWarrior == 0))
			{
				$fght = 2;
			}
			else
			{
				$fght = 0;
			}
			if ($fght > 0)
			{
				//attacker is warrior
				if ($fght == 1)
				{
					//defender is warrior
					if ($dfndr == 1)
					{
						$defenderRating = mt_rand(100,300) * $defenseBuff;
						$defenderRating = $defenderRating + ($defenderRating * ($defenseGeneral*0.5));
						$attackerRating = mt_rand(100,300) * $attackBuff;
						//Attacker warrior beats defender warrior
						if ($attackerRating >= $defenderRating)
						{
							$defenseWarrior--;
							$defenseTotal--;
							$result['defense_warrior_lost']++;
						}
						//Attack warrior loses to defender warrior
						else
						{
							$attackWarrior--;
							$attackTotal--;
							$result['attack_warrior_lost']++;
						}
					}
					//defender is archer
					else
					{
						$defenderRating = mt_rand(50,268) * $defenseBuff;
						$defenderRating = $defenderRating + ($defenderRating * ($defenseGeneral*0.5));
						$attackerRating = mt_rand(50,268) * $attackBuff;
						//Attacker warrior beats defender archer
						if ($attackerRating >= $defenderRating)
						{
							$defenseArcher--;
							$defenseTotal--;
							$result['defense_archer_lost']++;
						}
						//Attack warrior loses to defender archer
						else
						{
							$attackWarrior--;
							$attackTotal--;
							$result['attack_warrior_lost']++;
						}
					}
						
				}
				//attacker is archer
				else
				{
					//defender is warrior
					if ($dfndr == 1)
					{
						$defenderRating = mt_rand(150,350) * $defenseBuff;
						$defenderRating = $defenderRating + ($defenderRating * ($defenseGeneral* $districtConfig['GeneralBuff']));
						$attackerRating = mt_rand(75,400) * $attackBuff;
						//Attacker warrior beats defender warrior
						if ($attackerRating >= $defenderRating)
						{
							$defenseWarrior--;
							$defenseTotal--;
							$result['defense_warrior_lost']++;
						}
						//Attack archer loses to defender warrior
						else
						{
							$attackArcher--;
							$attackTotal--;
							$result['attack_archer_lost']++;
						}
					}
					//defender is archer
					else
					{
						$defenderRating = mt_rand(75,400) * $defenseBuff;
						$defenderRating = $defenderRating + ($defenderRating * ($defenseGeneral*0.5));
						$attackerRating = mt_rand(150,350) * $attackBuff;
						//Attacker archer beats defender archer
						if ($attackerRating >= $defenderRating)
						{
							$defenseArcher--;
							$defenseTotal--;
							$result['defense_archer_lost']++;
						}
						//Attack archer loses to defender archer
						else
						{
							$attackArcher--;
							$attackTotal--;
							$result['attack_archer_lost']++;
						}
					}
				}
			}
			else
			{
				//defender wins battle
				$result['winner'] = 'defense';
				$winner=true;
			}
		}
		else
		{
			//attacker wins battle
			$result['winner'] = 'attack';
			$result['defense_general_lost'] = $defenseGeneral;
			$winner=true;
		}
	}
	return json_encode($result);
}

function isAccessibleFromTile($from_tile, $to_tile)
{
	global $db;
	$q=$db->query("SELECT * FROM `guild_districts` WHERE `district_id` = {$from_tile}");
	$r=$db->fetch_row($q);
	$minX=$r['district_x'] - 1;
	$minY=$r['district_y'] - 1;
	$maxX=$r['district_x'] + 1;
	$maxY=$r['district_y'] + 1;
	$count = $db->num_rows($db->query("SELECT `district_id` 
											FROM `guild_districts` 
											WHERE `district_id` = {$to_tile}
											AND (`district_x` >= {$minX} AND `district_x` <= {$maxX})
											AND (`district_y` >= {$minY} AND `district_y` <= {$maxY})"));
	if ($count > 0)
		return true;
	else
		return false;
}
function setTileOwnership($district_id, $new_owner)
{
	global $db;
	$db->query("UPDATE `guild_districts` SET `district_owner` = {$new_owner}, `district_fortify` = 0 WHERE `district_id` = {$district_id}");
}
function updateTileTroops($district_id, $warriorChange, $archerChange, $generalChange)
{
	global $db;
	$db->query("UPDATE `guild_districts` 
				SET `district_melee` = `district_melee` + ({$warriorChange}),
				`district_range` = `district_range` + ({$archerChange}),
				`district_general` = `district_general` + ({$generalChange})
				WHERE `district_id` = {$district_id}");
}
function updateBarracksTroops($guild_id, $warriorChange, $archerChange, $generalChange)
{
	global $db;
	$db->query("UPDATE `guild_district_info` 
				SET `barracks_warriors` = `barracks_warriors` + ({$warriorChange}),
				`barracks_archers` = `barracks_archers` + ({$archerChange}),
				`barracks_generals` = `barracks_generals` + ({$generalChange})
				WHERE `guild_id` = {$guild_id}");
}
function blockAccess($guild)
{
	if ($guild == 0)
		return true;
	else
		false;
}
function resolveCoordinates($district_id)
{
	global $db;
	$q=$db->query("SELECT `district_x`, `district_y` FROM `guild_districts` WHERE `district_id` = {$district_id}");
	$r=$db->fetch_row($q);
	return "X: {$r['district_x']}; Y: {$r['district_y']}";
}
function isGuildLeaders($guild, $user)
{
	global $db;
	$q=$db->query("SELECT `guild_owner`, `guild_coowner` FROM `guild` WHERE `guild_id` = {$guild}");
	$r=$db->fetch_row($q);
	if ($r['guild_owner'] == $user)
		return true;
	elseif ($r['guild_coowner'] == $user)
		return true;
	else
		return false;
}