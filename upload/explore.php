<?php
require("globals.php");
if (user_infirmary($ir['userid']) == true)
{
	alert('danger',"{$lang["GEN_INFIRM"]}","{$lang['ERRDE_EXPLORE']}");
	die($h->endpage());
}
echo"<h4>{$lang['EXPLORE_INTRO']}</h4><ul class='nav nav-tabs'>
	<li><a data-toggle='tab' href='#SHOPS'>{$lang['EXPLORE_SHOP']}</a></li>
	<li><a data-toggle='tab' href='#FD'>{$lang['EXPLORE_FD']}</a></li>
	<li><a data-toggle='tab' href='#HL'>{$lang['EXPLORE_HL']}</a></li>
	<li><a data-toggle='tab' href='#ADMIN'>{$lang['EXPLORE_ADMIN']}</a></li>
	<li><a data-toggle='tab' href='#GAMES'>{$lang['EXPLORE_GAMES']}</a></li>
	<li><a data-toggle='tab' href='#GUILDS'>{$lang['EXPLORE_GUILDS']}</a></li>
	<li><a data-toggle='tab' href='#ACT'>{$lang['EXPLORE_ACT']}</a></li>
	<li><a data-toggle='tab' href='#PINTER'>{$lang['EXPLORE_PINTER']}</a></li>
</ul>

<div class='tab-content'>
	<div id='SHOPS' class='tab-pane fade in'>
		<div class='panel panel-default'>
			<div class='panel-body'>
				<a href='shop.php'>{$lang['EXPLORE_LSHOP']}</a><br />
				<a href='poshop.php'>{$lang['EXPLORE_POSHOP']}</a><br />
				<a href='market.php'>{$lang['EXPLORE_IMARKET']}</a><br />
				<a href='auction.php'>{$lang['EXPLORE_IAUCTION']}</a><br />
				<a href='trade.php'>{$lang['EXPLORE_TRADE']}</a><br />
				<a href='auction.php'>{$lang['EXPLORE_SCMARKET']}</a><br />	
			</div>
		</div>
	</div>
	<div id='FD' class='tab-pane fade'>
		<div class='panel panel-default'>
			<div class='panel-body'>
				<a href='bank.php'>{$lang['EXPLORE_BANK']}</a><br />
				<a href='estate.php'>{$lang['EXPLORE_ESTATES']}</a><br />
			</div>
		</div>
	</div>
	<div id='HL' class='tab-pane fade'>
		<div class='panel panel-default'>
			<div class='panel-body'>
				<a href='mine.php'>{$lang['EXPLORE_MINE']}</a><br />
				<a href='forest.php'>{$lang['EXPLORE_WC']}</a><br />
				<a href='farm.php'>{$lang['EXPLORE_FARM']}</a><br />
			</div>
		</div>
	</div>
	<div id='ADMIN' class='tab-pane fade'>
		<div class='panel panel-default'>
			<div class='panel-body'>
				<a href='users.php'>{$lang['EXPLORE_USERLIST']}</a><br />
				<a href='staff.php'>{$lang['EXPLORE_STAFFLIST']}</a><br />
				<a href='fedjail.php'>{$lang['EXPLORE_FED']}</a><br />
				<a href='stats.php'>{$lang['EXPLORE_STATS']}</a><br />
				<a href='playerreport.php'>{$lang['EXPLORE_REPORT']}</a><br />
			</div>
		</div>
	</div>
	<div id='GAMES' class='tab-pane fade'>
		<div class='panel panel-default'>
			<div class='panel-body'>
				<a href='rroulette.php'>{$lang['EXPLORE_RR']}</a><br />
				<a href='hlow.php'>{$lang['EXPLORE_HILO']}</a><br />
				<a href='roulette.php'>{$lang['EXPLORE_ROULETTE']}</a><br />
			</div>
		</div>
	</div>
	<div id='GUILDS' class='tab-pane fade'>
		<div class='panel panel-default'>
			<div class='panel-body'>
				
			</div>
		</div>
	</div>
	<div id='ACT' class='tab-pane fade'>
		<div class='panel panel-default'>
			<div class='panel-body'>
				<a href='dungeon.php'>{$lang['EXPLORE_DUNG']}</a><br />
				<a href='infirmary.php'>{$lang['EXPLORE_INFIRM']}</a><br />
				<a href='gym.php'>{$lang['EXPLORE_GYM']}</a><br />
				<a href='job.php'>{$lang['EXPLORE_JOB']}</a><br />
				<a href='academy.php'>{$lang['EXPLORE_ACADEMY']}</a><br />
			</div>
		</div>
	</div>
	<div id='PINTER' class='tab-pane fade'>
		<div class='panel panel-default'>
			<div class='panel-body'>
				<a href='forums.php'>{$lang['EXPLORE_FORUMS']}</a><br />
				<a href='newspaper.php'>{$lang['EXPLORE_NEWSPAPER']}</a><br />
			</div>
		</div>
	</div>
</div>";
echo "<br /><code>http://{$domain}/register.php?REF={$userid}</code><br />
	{$lang['EXPLORE_REF']}";
$h->endpage();