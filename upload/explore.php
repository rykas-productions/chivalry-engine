<?php
require("globals.php");
$domain=determine_game_urlbase();
if (user_infirmary($ir['userid']) == true)
{
	alert('danger',"{$lang["GEN_INFIRM"]}","{$lang['ERRDE_EXPLORE']}");
	die($h->endpage());
}
?>
<i><?php echo $lang['EXPLORE_INTRO']; ?></i>
<div class="row">
	<div class="col-sm-4">
		<div class="panel panel-default">
			<div class="panel-heading">
				<b>
					<?php echo $lang['EXPLORE_SHOP']; ?>
				</b>
			</div>
			<div class="panel-body">
				<a href='shop.php'><?php echo $lang['EXPLORE_LSHOP']; ?></a><br />
				<a href='poshop.php'><?php echo $lang['EXPLORE_POSHOP']; ?></a><br />
				<a href='market.php'><?php echo $lang['EXPLORE_IMARKET']; ?></a><br />
				<a href='auction.php'><?php echo $lang['EXPLORE_IAUCTION']; ?></a><br />
				<a href='trade.php'><?php echo $lang['EXPLORE_TRADE']; ?></a><br />
				<a href='auction.php'><?php echo $lang['EXPLORE_SCMARKET']; ?></a><br />
			</div>
		</div>
	</div>
	<div class="col-sm-4">
		<div class="panel panel-default">
			<div class="panel-heading">
				<b><?php echo $lang['EXPLORE_FD']; ?></b>
			</div>
			<div class="panel-body">
				<a href='bank.php'><?php echo $lang['EXPLORE_BANK']; ?></a><br />
				<a href='estate.php'><?php echo $lang['EXPLORE_ESTATES']; ?></a><br />
			</div>
		</div>
	</div>
	<div class="col-sm-4">
		<div class="panel panel-default">
			<div class="panel-heading">
				<b><?php echo $lang['EXPLORE_HL']; ?></b>
			</div>
			<div class="panel-body">
				<a href='mine.php'><?php echo $lang['EXPLORE_MINE']; ?></a><br />
				<a href='forest.php'><?php echo $lang['EXPLORE_WC']; ?></a><br />
				<a href='farm.php'><?php echo $lang['EXPLORE_FARM']; ?></a><br />
			</div>
		</div>
	</div>
</div>
<div class="row">
	<div class="col-sm-4">
		<div class="panel panel-default">
			<div class="panel-heading">
				<b><?php echo $lang['EXPLORE_ADMIN']; ?></b>
			</div>
			<div class="panel-body">
				<a href='users.php'><?php echo $lang['EXPLORE_USERLIST']; ?></a><br />
				<a href='staff.php'><?php echo $lang['EXPLORE_STAFFLIST']; ?></a><br />
				<a href='fedjail.php'><?php echo $lang['EXPLORE_FED']; ?></a><br />
				<a href='stats.php'><?php echo $lang['EXPLORE_STATS']; ?></a><br />
				<a href='playerreport.php'><?php echo $lang['EXPLORE_REPORT']; ?></a><br />
			</div>
		</div>
	</div>
	<div class="col-sm-4">
		<div class="panel panel-default">
			<div class="panel-heading">
				<b><?php echo $lang['EXPLORE_GAMES']; ?></b>
			</div>
			<div class="panel-body">
				<a href='rroulette.php'><?php echo $lang['EXPLORE_RR']; ?></a><br />
				<a href='hlow.php'><?php echo $lang['EXPLORE_HILO']; ?></a><br />
				<a href='roulette.php'><?php echo $lang['EXPLORE_ROULETTE']; ?></a><br />
			</div>
		</div>
	</div>
	<div class="col-sm-4">
		<div class="panel panel-default">
			<div class="panel-heading">
				<b><?php echo $lang['EXPLORE_GUILDS']; ?></b>
			</div>
			<div class="panel-body">
				
			</div>
		</div>
	</div>
</div>
<div class="row">
	<div class="col-sm-6">
		<div class="panel panel-default">
			<div class="panel-heading">
				<b><?php echo $lang['EXPLORE_ACT']; ?></b>
			</div>
			<div class="panel-body">
				<a href='dungeon.php'><?php echo $lang['EXPLORE_DUNG']; ?></a><br />
				<a href='infirmary.php'><?php echo $lang['EXPLORE_INFIRM']; ?></a><br />
				<a href='gym.php'><?php echo $lang['EXPLORE_GYM']; ?></a><br />
				<a href='job.php'><?php echo $lang['EXPLORE_JOB']; ?></a><br />
				<a href='academy.php'><?php echo $lang['EXPLORE_ACADEMY']; ?></a><br />
			</div>
		</div>
	</div>
	<div class="col-sm-6">
		<div class="panel panel-default">
			<div class="panel-heading">
				<b><?php echo $lang['EXPLORE_PINTER']; ?></b>
			</div>
			<div class="panel-body">
				<a href='forums.php'><?php echo $lang['EXPLORE_FORUMS']; ?></a><br />
				<a href='newspaper.php'><?php echo $lang['EXPLORE_NEWSPAPER']; ?></a><br />
			</div>
		</div>
	</div>
</div>
<?php
echo "<code>http://{$domain}/register.php?REF={$userid}</code><br />
	{$lang['EXPLORE_REF']}";
$h->endpage();