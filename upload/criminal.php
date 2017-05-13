<?php
/*
	File:		criminal.php
	Created: 	4/4/2016 at 11:55PM Eastern Time
	Info: 		Lists created crimes for players to commit.
	Author:		TheMasterGeneral
	Website: 	https://github.com/MasterGeneral156/chivalry-engine
*/
$macropage=('criminal.php');
require('globals.php');
echo "<h3>{$lang['CRIME_TITLE']}</h3>";
if ($api->UserStatus($ir['userid'],'infirmary') == true || $api->UserStatus($ir['userid'],'dungeon') == true)
{
	alert('danger',"{$lang['ERROR_GENERIC']}","{$lang['CRIME_ERROR_JI']}");
	die($h->endpage());
}
if (!isset($_GET['action']))
{
    $_GET['action'] = '';
}
switch ($_GET['action'])
{
    case 'crime':
        crime();
        break;
    default:
        home();
        break;
}
function home()
{
	global $db,$h,$lang;
	$crimes = array();
	$q2 = $db->query("SELECT `crimeGROUP`, `crimeNAME`, `crimeBRAVE`, `crimeID` FROM `crimes` ORDER BY `crimeBRAVE` ASC");
	while ($r2 = $db->fetch_row($q2))
	{
		$crimes[] = $r2;
	}
	$db->free_result($q2);
	$q = $db->query("SELECT `cgID`, `cgNAME` FROM `crimegroups` ORDER BY `cgORDER` ASC");
	echo "
	<table class='table table-bordered table-responsive'>
		<tr>
			<th>
				{$lang['CRIME_TABLE_CRIME']}
			</th>
			<th>
				{$lang['CRIME_TABLE_COST']}
			</th>
			<th>
				{$lang['CRIME_TABLE_COMMIT']}
			</th>
		</tr>";
	while ($r = $db->fetch_row($q))
	{
		echo "<tr><td colspan='3' class='h'>{$r['cgNAME']} {$lang['CRIME_TABLE_CRIMES']}</td></tr>";
		foreach ($crimes as $v)
		{
			if ($v['crimeGROUP'] == $r['cgID'])
			{
				echo "<tr><td>{$v['crimeNAME']}</td><td>{$v['crimeBRAVE']} {$lang['INDEX_BRAVE']}</td><td><a href='?action=crime&c={$v['crimeID']}'>{$lang['CRIME_TABLE_COMMIT']}</a></td></tr>";
			}
		}
	}
	$db->free_result($q);
	echo "</table>";
	$h->endpage();
}
function crime()
{
	global $db,$lang,$userid,$ir,$h,$api;
	if (!isset($_GET['c']))
	{
		$_GET['c'] = 0;
	}
	$_GET['c'] = abs($_GET['c']);
	if ($_GET['c'] <= 0)
	{
		alert('danger',"{$lang['ERROR_INVALID']}","{$lang['CRIME_COMMIT_INVALID']}",true,'criminal.php');
	}
	else
	{
		$q =  $db->query("SELECT * FROM `crimes` WHERE `crimeID` = {$_GET['c']} LIMIT 1");
		if ($db->num_rows($q) == 0)
		{
			alert('danger',"{$lang['ERROR_INVALID']}","{$lang['CRIME_COMMIT_INVALID']}",true,'criminal.php');
			die($h->endpage());
		}
		$r = $db->fetch_row($q);
		$db->free_result($q);
		if ($ir['brave'] < $r['crimeBRAVE'])
		{
			alert('danger',"{$lang['ERROR_GENERIC']}","{$lang['CRIME_COMMIT_BRAVEBAD']}",true,'criminal.php');
			die($h->endpage());
		}
		else
		{
			$ec = "\$sucrate=" . str_replace(array("LEVEL", "EXP", "WILL", "IQ"), array($ir['level'], $ir['xp'], $ir['will'], $ir['iq']), $r['crimePERCFORM']) . ";";
			eval($ec);
			$ir['brave'] -= $r['crimeBRAVE'];
			$api->UserInfoSet($userid,"brave","-{$r['crimeBRAVE']}");
			if (Random(1, 100) <= $sucrate)
			{
				if (!empty($r['crimePRICURMIN']))
				{
					$prim_currency=Random($r['crimePRICURMIN'],$r['crimePRICURMAX']);
					$api->UserGiveCurrency($userid,'primary',$prim_currency);
				}
				if (!empty($r['crimeSECURMIN']))
				{
					$sec_currency=Random($r['crimeSECURMIN'],$r['crimeSECCURMAX']);
					$api->UserGiveCurrency($userid,'secondary',$sec_currency);
				}
				if (!empty($r['crimeSUCCESSITEM']))
				{
					$api->UserGiveItem($userid, $r['crimeSUCCESSITEM'], 1);
				}
				if (empty($prim_currency))
				{
					$prim_currency=0;
				}
				if (empty($sec_currency))
				{
					$sec_currency=0;
				}
				if (empty($r['crimeSUCCESSITEM']))
				{
					$r['crimeSUCCESSITEM']=0;
				}
				$text = str_replace("{money}", $prim_currency, $r['crimeSTEXT']);
				$text = str_replace("{secondary}", $sec_currency, $r['crimeSTEXT']);
				$text = str_replace("{item}", $api->SystemItemIDtoName($r['crimeSUCCESSITEM']), $r['crimeSTEXT']);
				$title=$lang['ERROR_SUCCESS'];
				$type='success';
				$api->UserInfoSetStatic($userid,"xp",$ir['xp']+$r['crimeXP']);
				$api->SystemLogsAdd($userid,'crime',"Successfully commited the {$r['crimeNAME']} crime.");
			}
			else
			{
					$title=$lang['ERROR_GENERIC'];
					$type='danger';
					$dtime=Random($r['crimeDUNGMIN'],$r['crimeDUNGMAX']);
					$text = str_replace("{time}", $dtime, $r['crimeFTEXT']);
					$api->UserStatusSet($userid,'dungeon',$dtime,$r['crimeDUNGREAS']);
					$api->SystemLogsAdd($userid,'crime',"Failed to commit the {$r['crimeNAME']} crime.");
			}
			alert("{$type}","{$title}","{$r['crimeITEXT']} {$text}",true,'criminal.php');
			die($h->endpage());
		}
	}
}