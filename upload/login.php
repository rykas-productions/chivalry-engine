<?php
/*
	File:		login.php
	Created: 	4/5/2016 at 12:17AM Eastern Time
	Info: 		The main page when not logged in.
	Author:		TheMasterGeneral
	Website: 	https://github.com/MasterGeneral156/chivalry-engine
*/
if ((!file_exists('./installer.lock')) && (file_exists('installer.php')))
{
	header("Location: installer.php");
	die();
}
require("globals_nonauth.php");
$currentpage = $_SERVER['REQUEST_URI'];
$cpage = strip_tags(stripslashes($currentpage));
$domain=determine_game_urlbase();
if ($set['HTTPS_Support'] == 'true')
{
	header("Location: https://{$domain}/");
}
			echo "<div class='jumbotron'>
				  <div class='container'>
					<h1>{$set['WebsiteName']}</h1>
					<p>{$set['Website_Description']}</p>
					<p><a class='btn btn-primary btn-lg' href='register.php' role='button'>{$lang['LOGIN_REGISTER']} &raquo;</a></p>
				  </div>
				</div>";
				$AnnouncementQuery=$db->query("SELECT `ann_text`,`ann_time` FROM `announcements` ORDER BY `ann_time` desc LIMIT 1");
				$ANN=$db->fetch_row($AnnouncementQuery);
				echo"
			<div class='row'>
				<div class='col-sm-4'>
					<div class='card'>
						<div class='card-header'>
							{$lang['LOGIN_LA']} (" . date("F j, Y, g:i a",$ANN['ann_time']) . ")
						</div>
						<div class='card-body'>
							{$ANN['ann_text']}
						</div>
					</div>
				</div>
				<div class='col-sm-4'>
					<div class='card'>
						<div class='card-header'>
							{$lang['LOGIN_TP']}
						</div>
						<div class='card-body'>";
							$Rank=0;
							$RankPlayerQuery = 
							$db->query("SELECT u.`userid`, `level`, `username`,
							`strength`, `agility`, `guard`, `labor`, `IQ`
							FROM `users` AS `u`
							INNER JOIN `userstats` AS `us`
							 ON `u`.`userid` = `us`.`userid`
							WHERE `u`.`user_level` != 'Admin' AND `u`.`user_level` != 'NPC'
							ORDER BY (`strength` + `agility` + `guard` + `labor` + `IQ`) 
							DESC, `u`.`userid` ASC
							LIMIT 10");
							while ($pdata=$db->fetch_row($RankPlayerQuery))
							{
								$Rank=$Rank+1;
								echo "{$Rank}) {$pdata['username']} [{$pdata['userid']}] ({$lang['INDEX_LEVEL']} {$pdata['level']})<br />";
							}
						echo "</div>
					</div>
				</div>
				<div class='col-sm-4'>
					<div class='card'>
						<div class='card-header'>
							{$lang['LOGIN_TG']}
						</div>";
							$GRank=0;
							$RankGuildQuery=$db->query("SELECT `guild_name`,`guild_level` FROM `guild` ORDER BY `guild_level` desc LIMIT 10");
						echo"
						<div class='card-body'>";
							while ($gdata=$db->fetch_row($RankGuildQuery))
							{
								$GRank=$GRank+1;
								echo "{$GRank}) {$gdata['guild_name']} ({$lang['INDEX_LEVEL']} {$gdata['guild_level']})<br />";
							}
						echo"</div>
					</div>
				</div>
			</div>";
$h->endpage();