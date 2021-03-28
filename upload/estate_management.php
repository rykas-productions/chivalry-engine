<?php
require('globals.php');
if (!isset($_GET['action'])) {
    $_GET['action'] = '';
}
$estate=$db->fetch_row($db->query("SELECT * FROM `user_estates` WHERE `ue_id` = {$ir['estate']}"));
$edb=$db->fetch_row($db->query("SELECT * FROM `estates` WHERE `house_id` = {$estate['estate']}"));
echo "<h4>Estate Management</h3><hr/>";
switch ($_GET['action']) {
    case 'constructGarden':
        construct_garden();
        break;
    case 'constructSleep':
        construct_sleep();
        break;
	case 'constructVault':
        construct_vault();
        break;
	case 'upgradeGarden':
        upgrade_garden();
        break;
	case 'upgradeVault':
        upgrade_vault();
        break;
	case 'upgradeSleep':
	    upgrade_sleep();
	    break;
	case 'propertyList':
        property_list();
        break;
	case 'moveOut':
        move_out();
        break;
	case 'doSleep':
	    doSleep();
	    break;
	case 'moveIn':
        move_in();
        break;
	case 'vault':
        vault();
        break;
	case 'convert':
        convert();
        break;
	case 'purchase':
        buy_property();
        break;
	case 'sellList':
        game_properties();
        break;
	case 'sell':
		sell_property();
		break;
	case 'estateMarket':
		estate_market();
		break;
	case 'marketBuy':
		estate_market_buy();
		break;
	case 'marketSell':
		estate_market_sell();
		break;
	case 'marketRemove':
		estate_market_remove();
		break;
    default:
        home();
        break;
}
function home()
{
	global $db, $ir, $userid, $estate, $edb;
	$gardenLevel = ($estate['gardenUpgrade'] > 0) ? number_format($estate['gardenUpgrade']) . " / " . number_format($edb['upgradeLevel']) : "<i>Needs constructed</i>";
	$sleepLevel = ($estate['sleepUpgrade'] > 0) ? number_format($estate['sleepUpgrade']) . " / " . number_format($edb['upgradeLevel']) : "<i>Needs constructed</i>";
	$vaultLevel = ($estate['vaultUpgrade'] > 0) ? number_format($estate['vaultUpgrade']) . " / " . number_format($edb['upgradeLevel']) : "<i>Needs constructed</i>";
	$gLink = ($estate['gardenUpgrade'] > 0) ? "<a href='?action=upgradeGarden'>Upgrade</a>" : "<a href='?action=constructGarden'>Construct</a>" ;
	$vLink = ($estate['vaultUpgrade'] > 0) ? "<a href='?action=upgradeVault'>Upgrade</a>" : "<a href='?action=constructVault'>Construct</a>" ;
	$sLink = ($estate['sleepUpgrade'] > 0) ? "<a href='?action=upgradeSleep'>Upgrade</a>" : "<a href='?action=constructSleep'>Construct</a>" ;
	echo"
	<div class='row'>
		<div class='col-12 col-lg-6 col-xl-4'>
			<div class='card'>
				<div class='card-header'>
					<div class='row'>
						<div class='col-8'>
							<b>{$edb['house_name']}</b>
						</div>
						<div class='col-4'>
							<small>ID: {$estate['ue_id']}</small>
						</div>
					</div>
				</div>
				<div class='card-body'>
					<div class='row'>
						<div class='col-6 col-sm-5'>
							Base Will
						</div>
						<div class='col-6 col-sm-7'>
							" . number_format($edb['house_will']) . "
						</div>
					</div>
					<div class='row'>
						<div class='col-6 col-sm-5'>
							Max Will
						</div>
						<div class='col-6 col-sm-7'>
							" . number_format($ir['maxwill']) . "
						</div>
					</div>
					<div class='row'>
						<div class='col-5'>
							Buy Value
						</div>
						<div class='col-7'>
							" . number_format($edb['house_price']) . " Copper Coins
						</div>
					</div>
					<div class='row'>
						<div class='col-5'>
							Current Value
						</div>
						<div class='col-7'>
							" . number_format(calculateSellPrice($estate['ue_id'])) . " Copper Coins
						</div>
					</div>
				</div>
			</div>
			<br />
		</div>
		<div class='col-12 col-lg-6 col-xl-4'>
			<div class='card'>
				<div class='card-header'>
					<div class='row'>
						<div class='col-8'>
							Zen Garden
						</div>
						<div class='col-4'>";
							if ($estate['gardenUpgrade'] == $edb['upgradeLevel'])
								echo "<b>Max</b>";
							else
							echo $gLink;
						echo"
						</div>
					</div>
				</div>
				<div class='card-body'>
					<div class='row'>
						<div class='col-4'>
							Garden Level
						</div>
						<div class='col-8'>
							{$gardenLevel}
						</div>
					</div>
					<div class='row'>
						<div class='col-4'>
							Bonus Will
						</div>
						<div class='col-8'>
							" . number_format(calcExtraWill($estate['gardenUpgrade'], $edb['house_will'])+$estate['bonusWill']) . "
						</div>
					</div>
				</div>
			</div>
			<br />
		</div>
		<div class='col-12 col-lg-6 col-xl-4'>
			<div class='card'>
				<div class='card-header'>
					<div class='row'>
						<div class='col-8'>
							Sleeping Quarters
						</div>
						<div class='col-4'>";
							if ($estate['sleepUpgrade'] == $edb['upgradeLevel'])
								echo "<b>Max</b>";
							else
							echo $sLink;
						echo"
						</div>
					</div>
				</div>
				<div class='card-body'>
					<div class='row'>
						<div class='col-5'>
							Quarters Level
						</div>
						<div class='col-7'>
							{$sleepLevel}
						</div>
					</div>
					<div class='row'>
						<div class='col-5'>
							Efficiency
						</div>
						<div class='col-7'>
							" . calcSleepEfficiency($estate['sleepUpgrade'], $edb['house_will']) . " / Minute
						</div>
					</div>
                    <div class='row'>
						<div class='col'>
							<a href='?action=doSleep' class='btn btn-primary btn-block'>Sleep</a>
						</div>
					</div>
				</div>
			</div>
			<br />
		</div>
		<div class='col-12 col-lg-6 col-xl-4'>
			<div class='card'>
				<div class='card-header'>
					<div class='row'>
						<div class='col-8'>
							Vault
						</div>
						<div class='col-4'>";
							if ($estate['vaultUpgrade'] == $edb['upgradeLevel'])
								echo "<b>Max</b>";
							else
							echo $vLink;
						echo"
						</div>
					</div>
				</div>
				<div class='card-body'>
					<div class='row'>
						<div class='col-4'>
							Vault Level
						</div>
						<div class='col-8'>
							{$vaultLevel}
						</div>
					</div>
					<div class='row'>
						<div class='col-4'>
							Stored
						</div>
						<div class='col-8'>
							" . number_format($estate['vault']) . " Copper Coins
						</div>
					</div>
					<div class='row'>
						<div class='col-4'>
							Capacity
						</div>
						<div class='col-8'>
							" . number_format(calcVaultCapacity($estate['vaultUpgrade'], $edb['house_price'])) . " Copper Coins
						</div>
					</div>
					<div class='row'>
						<div class='col-12'>
							<a href='#' data-toggle='modal' data-target='#estate_vault' class='btn btn-primary btn-block'>Access Vault</a>
						</div>
					</div>
				</div>
			</div>
			<br />
		</div>
		<div class='col-12 col-lg-12 col-xl-8'>
			<div class='row'>
				<div class='col-6 col-md-4'>
					<a href='?action=moveOut' class='btn btn-info btn-block'>Move Out</a>
					<br />
				</div>
				<div class='col-6 col-md-4'>
					<a href='?action=propertyList' class='btn btn-success btn-block'>Your Properties</a>
					<br />
				</div>
				<div class='col-12 col-md-4 col-xl-8'>
					<a href='?action=sellList' class='btn btn-block btn-warning'>Avaliable Properties</a>
					<br />
				</div>
			</div>
		</div>
	</div>";
}

function move_out()
{
	global $db, $ir, $userid, $edb, $estate, $h, $api;
	if ($estate['estate'] <= 1)
	{
		alert('danger',"Uh Oh!","You cannot move out of homelessness.", true, 'estate_management.php');
		die($h->endpage());
	}
	doLeaveHouse($userid);
	$api->SystemLogsAdd($userid, "estate", "Moved out of their current estate.");
	alert("success","Success!","You have successfully moved out of this estate.",true, 'estate_management.php');
}
function move_in()
{
	global $db, $ir, $userid, $edb, $estate, $h, $api;
	$_GET['id'] = (isset($_GET['id']) && is_numeric($_GET['id'])) ? abs($_GET['id']) : '';
	if (empty($_GET['id']))
	{
		alert('danger',"Uh Oh!","Please specify the estate you wish to move into.", true, '?action=propertyList');
		die($h->endpage());
	}
	$q=$db->query("SELECT * FROM `user_estates` WHERE `userid` = {$userid} AND `ue_id` = {$_GET['id']}");
	if ($db->num_rows($q) == 0)
	{
		alert('danger',"Uh Oh!","You cannot move into an estate you do not own.", true, '?action=propertyList');
		die($h->endpage());
	}
	doMoveIn($_GET['id'],$userid);
	$api->SystemLogsAdd($userid, "estate", "Moved into estate ID {$_GET['id']}.");
	alert("success","Success!","You have moved into this property!",true, 'estate_management.php');
}
function property_list()
{
	global $db, $ir, $userid;
	$q=$db->query("SELECT * FROM `user_estates`
	INNER JOIN `estates` as `e`
	ON `estate` = `e`.`house_id`
	WHERE `userid` = {$userid}");
	echo "<div class='row'>
			<div class='col-12 col-md-6 col-lg-3'>
				<a href='estate_management.php' class='btn btn-block btn-info'>Current Estate</a>
				<br />
			</div>
			<div class='col-12 col-md-6 col-lg-3'>
				<a href='?action=sellList' class='btn btn-block btn-primary'>Avaliable Properties</a>
				<br />
			</div>
			<div class='col-12 col-md-6 col-lg-3'>
				<a href='?action=estateMarket' class='btn btn-block btn-danger'>Player Properties</a>
				<br />
			</div>
			<div class='col-12 col-md-6 col-lg-3'>
				<a href='#' class='btn btn-block btn-warning disabled'>For Rent</a>
				<br />
			</div>
		</div>
		<hr />";
	while ($r = $db->fetch_row($q))
	{
		if ($r['ue_id'] == $ir['estate'])
		{
			$act1="<a href='estate_management.php' class='btn btn-info btn-block'>Manage</a>";
		}
		else
		{
			$act1="<a href='?action=moveIn&id={$r['ue_id']}' class='btn btn-success btn-block'>Move In</a>";
		}
		if ($r['estate'] <= 1)
		{
			$act2="<a href='#' class='btn btn-danger btn-block disabled'>N/A</a>";
			$act3="<a href='#' class='btn btn-secondary btn-block disabled'>N/A</a>";
		}
		elseif ($r['ue_id'] == $ir['estate'])
		{
			$act2="<a href='#' class='btn btn-danger btn-block disabled'>Live here</a>";
			$act3="<a href='#' class='btn btn-secondary btn-block disabled'>Live here</a>";
		}
		else
		{
			$act2="<a href='?action=sell&id={$r['ue_id']}' class='btn btn-danger btn-block'>Sell</a>";
			$act3="<a href='?action=marketSell&id={$r['ue_id']}' class='btn btn-secondary btn-block'>Market</a>";
		}
		echo "<div class='row'>
			<div class='col-12'>
				<div class='card'>
					<div class='card-body'>
						<div class='row'>
							<div class='col-6 col-md-3'>
								<div class='row'>
									<b>{$r['house_name']}</b>
								</div>
								<div class='row'>
									<small>Property ID: {$r['ue_id']}</small>
								</div>
							</div>
							<div class='col-6 col-md-3'>
								<div class='row'>
									<div class='col-12'>
										<b>Base Will</b>
									</div>
									<div class='col-12'>
										" . number_format($r['house_will']) . "
									</div>
								</div>
								<div class='row'>
									<div class='col-12'>
										<b>Max Will</b>
									</div>
									<div class='col-12'>
										" . number_format(calcExtraWill($r['gardenUpgrade'], $r['house_will'])+$r['house_will']+$r['bonusWill']) . "
									</div>
								</div>
							</div>
							<div class='col-6'>
								<div class='row'>
									<div class='col-6'>
										<b>Garden</b>
									</div>
									<div class='col-6'>
										{$r['gardenUpgrade']}
									</div>
								</div>
								<div class='row'>
									<div class='col-6'>
										<b>Vault</b>
									</div>
									<div class='col-6'>
										{$r['vaultUpgrade']}
									</div>
								</div>
								<div class='row'>
									<div class='col-6'>
										<b>Sleep</b>
									</div>
									<div class='col-6'>
										{$r['sleepUpgrade']}
									</div>
								</div>
								<div class='row'>
									<div class='col-12 col-md-6'>
										<b>Current Value</b>
									</div>
									<div class='col-12 col-md-6'>
										<small>" . number_format(calculateSellPrice($r['ue_id'])) . " Copper Coins</small>
									</div>
								</div>
							</div>
							<div class='col-12'>
								<div class='row'>
									<div class='col-4'>
										{$act1}
									</div>
									<div class='col-4'>
										{$act2}
									</div>
									<div class='col-4'>
										{$act3}
									</div>
								</div>
							</div>
						</div>
					</div>
				</div>
			</div>
		</div>";
	}
}

function construct_garden()
{
	global $db, $ir, $userid, $estate, $edb, $h, $api;
	$gardenLevel = $estate['gardenUpgrade'];
	$waterCost = calcWaterCosts($gardenLevel, $edb['house_will']);
	$stoneCost = calcStoneCosts($gardenLevel, $edb['house_will']);
	$stickCost = calcStickCosts($gardenLevel, $edb['house_will']);
	if ($gardenLevel >= $edb['upgradeLevel'])
	{
		alert('danger',"Uh Oh!","You cannot construct a garden here.", true, 'estate_management.php');
		die($h->endpage());
	}
	if ($estate['estate'] <= 1)
	{
		alert('danger',"Uh Oh!","You must purchase an estate before you can construct a garden.", true, 'estate_management.php');
		die($h->endpage());
	}
	if ($gardenLevel > 0)
	{
		alert('danger',"Uh Oh!","You have no need to construct a garden if you already have one.", true, 'estate_management.php');
		die($h->endpage());
	}
	if (isset($_POST['construct']))
	{
		if (!$api->UserHasItem($userid, 296, $waterCost))
		{
			alert('danger',"Uh Oh!","You need " . number_format($waterCost) . " Bucket(s) of Water to construct a garden at this estate.", true, 'estate_management.php');
			die($h->endpage());
		}
		if (!$api->UserHasItem($userid, 2, $stoneCost))
		{
			alert('danger',"Uh Oh!","You need " . number_format($stoneCost) . " Heavy Rock(s) to construct a garden at this estate.", true, 'estate_management.php');
			die($h->endpage());
		}
		if (!$api->UserHasItem($userid, 1, $stickCost))
		{
			alert('danger',"Uh Oh!","You need " . number_format($stickCost) . " Sharpened Stick(s) to construct a garden at this estate.", true, 'estate_management.php');
			die($h->endpage());
		}
		$newWill = calcExtraWill($gardenLevel+1, $edb['house_will']);
		$api->UserTakeItem($userid, 296, $waterCost);
		$api->UserTakeItem($userid, 2, $stoneCost);
		$api->UserTakeItem($userid, 1, $stickCost);
		$db->query("UPDATE `user_estates` SET `gardenUpgrade` = 1 WHERE `ue_id` = {$estate['ue_id']}");
		$db->query("UPDATE `users` SET `maxwill` = `maxwill` + {$newWill} WHERE `userid` = {$userid}");
		$api->SystemLogsAdd($userid, "estate", "Constructed Garden at Estate ID: {$estate['ue_id']}. ({$waterCost} Water Buckets, {$stoneCost} Heavy Rocks, {$stickCost} Sharpened Sticks.)");
		alert("success","Success!","You have traded " . number_format($waterCost) . " Bucket(s) of Water, " . number_format($stoneCost) . " Heavy Rock(s), and " . number_format($stickCost) . " Sharpened Stick(s) to construct your garden.", true, 'estate_management.php');
	}
	else
	{
		echo "Are you sure you wish to construct a garden? You need " . number_format($waterCost) . " Bucket(s) of Water, 
		" . number_format($stoneCost) . " Heavy Rock(s), and " . number_format($stickCost) . " Sharpened Stick(s). You will 
		gain an extra 10% maxium will per garden level. Each garden level will increase your estate's sell value by roughly 6%.
		<hr />
		<form method='post'>
		<input type='hidden' name='construct' value='yes'>
			<div class='row'>
				<div class='col-6'>
					<input type='submit' class='btn btn-block btn-success' value='Construct Garden'>
				</div>
				<div class='col-6'>
					<a href='?action=propertyList' class='btn btn-danger btn-block'>Go Back</a>
				</div>
			</div>
		</form>";
	}
}

function upgrade_garden()
{
	global $db, $ir, $userid, $estate, $edb, $h, $api;
	$gardenLevel = $estate['gardenUpgrade'];
	$waterCost = calcWaterCosts($gardenLevel, $edb['house_will']);
	$stoneCost = calcStoneCosts($gardenLevel, $edb['house_will']);
	$stickCost = calcStickCosts($gardenLevel, $edb['house_will']);
	if ($gardenLevel >= $edb['upgradeLevel'])
	{
		alert('danger',"Uh Oh!","You cannot upgrade your garden anymore at this estate.", true, 'estate_management.php');
		die($h->endpage());
	}
	if ($gardenLevel == 0)
	{
		alert('danger',"Uh Oh!","You cannot upgrade a garden if you haven't constructed it.", true, 'estate_management.php');
		die($h->endpage());
	}
	if ($estate['estate'] <= 1)
	{
		alert('danger',"Uh Oh!","You must purchase an estate before you can construct a garden.", true, 'estate_management.php');
		die($h->endpage());
	}
	if (isset($_POST['construct']))
	{
		if (!$api->UserHasItem($userid, 296, $waterCost))
		{
			alert('danger',"Uh Oh!","You need " . number_format($waterCost) . " Bucket(s) of Water to upgrade your garden at this estate.", true, 'estate_management.php');
			die($h->endpage());
		}
		if (!$api->UserHasItem($userid, 2, $stoneCost))
		{
			alert('danger',"Uh Oh!","You need " . number_format($stoneCost) . " Heavy Rock(s) to upgrade your garden at this estate.", true, 'estate_management.php');
			die($h->endpage());
		}
		if (!$api->UserHasItem($userid, 1, $stickCost))
		{
			alert('danger',"Uh Oh!","You need " . number_format($stickCost) . " Sharpened Stick(s) to upgrade your garden at this estate.", true, 'estate_management.php');
			die($h->endpage());
		}
		$newWill = calcExtraWill($gardenLevel+1, $edb['house_will']);
		$api->UserTakeItem($userid, 296, $waterCost);
		$api->UserTakeItem($userid, 2, $stoneCost);
		$api->UserTakeItem($userid, 1, $stickCost);
		$db->query("UPDATE `users` SET `maxwill` = `maxwill` + {$newWill} WHERE `userid` = {$userid}");
		$db->query("UPDATE `user_estates` SET `gardenUpgrade` = `gardenUpgrade` + 1 WHERE `ue_id` = {$estate['ue_id']}");
		$api->SystemLogsAdd($userid, "estate", "Upgraded garden at Estate ID: {$estate['ue_id']} to level " . ($gardenLevel + 1) . ". ({$waterCost} Water Buckets, {$stoneCost} Heavy Rocks, {$stickCost} Sharpened Sticks.)");
		alert("success","Success!","You have traded " . number_format($waterCost) . " Bucket(s) of Water, " . number_format($stoneCost) . " Heavy Rock(s), and " . number_format($stickCost) . " Sharpened Stick(s) to upgrade your garden to level " . number_format($gardenLevel+1) . ".", true, 'estate_management.php');
	}
	else
	{
		echo "Are you sure you wish to upgrade your garden? You need " . number_format($waterCost) . " Bucket(s) of Water, 
		" . number_format($stoneCost) . " Heavy Rock(s), and " . number_format($stickCost) . " Sharpened Stick(s). You will 
		gain an extra 10% maxium will per garden level. Each garden level will increase your estate's sell value by roughly 6%.<br />
		<b>Your garden is level {$gardenLevel}.</b>
		<hr />
		<form method='post'>
		<input type='hidden' name='construct' value='yes'>
			<div class='row'>
				<div class='col-6'>
					<input type='submit' class='btn btn-block btn-success' value='Upgrade Garden'>
				</div>
				<div class='col-6'>
					<a href='?action=propertyList' class='btn btn-danger btn-block'>Go Back</a>
				</div>
			</div>
		</form>";
	}
}

function construct_vault()
{
	global $db, $ir, $userid, $estate, $edb, $h, $api;
	$gardenLevel = $estate['vaultUpgrade'];
	$ironCost = calcIronCosts($gardenLevel, $edb['house_price']);
	if ($gardenLevel >= $edb['upgradeLevel'])
	{
		alert('danger',"Uh Oh!","You cannot construct a vault here.", true, 'estate_management.php');
		die($h->endpage());
	}
	if ($gardenLevel > 0)
	{
		alert('danger',"Uh Oh!","You have no need to construct a vault if you already have one.", true, 'estate_management.php');
		die($h->endpage());
	}
	if ($estate['estate'] <= 1)
	{
		alert('danger',"Uh Oh!","You must purchase an estate before you can construct a vault.", true, 'estate_management.php');
		die($h->endpage());
	}
	if (isset($_POST['construct']))
	{
		if (!$api->UserHasItem($userid, 23, $ironCost))
		{
			alert('danger',"Uh Oh!","You need " . number_format($ironCost) . " Copper Flakes to construct a vault at this estate.", true, 'estate_management.php');
			die($h->endpage());
		}
		$api->UserTakeItem($userid, 23, $ironCost);
		$db->query("UPDATE `user_estates` SET `vaultUpgrade` = 1 WHERE `ue_id` = {$estate['ue_id']}");
		$api->SystemLogsAdd($userid, "estate", "Constructed vault at Estate ID: {$estate['ue_id']}. ({$ironCost} Copper Flakes.)");
		alert("success","Success!","You have traded " . number_format($ironCost) . " Copper Flakes to construct your vault.", true, 'estate_management.php');
	}
	else
	{
		echo "Are you sure you wish to construct a vault? You need " . number_format($ironCost) . " Copper Flakes. You will gain 
		roughly 18% vault capacity each upgrade, relative to the estate's purchase value. Each vault level will increase
		your estate's sell value by roughly 6%.
		<hr />
		<form method='post'>
		<input type='hidden' name='construct' value='yes'>
			<div class='row'>
				<div class='col-6'>
					<input type='submit' class='btn btn-block btn-success' value='Construct Vault'>
				</div>
				<div class='col-6'>
					<a href='?action=propertyList' class='btn btn-danger btn-block'>Go Back</a>
				</div>
			</div>
		</form>";
	}
}

function upgrade_vault()
{
	global $db, $ir, $userid, $estate, $edb, $h, $api;
	$gardenLevel = $estate['vaultUpgrade'];
	$ironCost = calcIronCosts($gardenLevel, $edb['house_price']);
	if ($gardenLevel >= $edb['upgradeLevel'])
	{
		alert('danger',"Uh Oh!","You cannot upgrade your vault anymore at this estate.", true, 'estate_management.php');
		die($h->endpage());
	}
	if ($gardenLevel == 0)
	{
		alert('danger',"Uh Oh!","You need to construct a vault first before you can upgrade it.", true, 'estate_management.php');
		die($h->endpage());
	}
	if ($estate['estate'] <= 1)
	{
		alert('danger',"Uh Oh!","You must purchase an estate before you can upgrade your vault.", true, 'estate_management.php');
		die($h->endpage());
	}
	if (isset($_POST['construct']))
	{
		if (!$api->UserHasItem($userid, 23, $ironCost))
		{
			alert('danger',"Uh Oh!","You need " . number_format($ironCost) . " Copper Flakes to upgrade your vault at this estate.", true, 'estate_management.php');
			die($h->endpage());
		}
		$api->UserTakeItem($userid, 23, $ironCost);
		$db->query("UPDATE `user_estates` SET `sleepUpgrade` = `sleepUpgrade` + 1 WHERE `ue_id` = {$estate['ue_id']}");
		$api->SystemLogsAdd($userid, "estate", "Upgraded vault at Estate ID: {$estate['ue_id']} to level " . ($gardenLevel + 1) . ". ({$ironCost} Copper Flakes.)");
		alert("success","Success!","You have traded " . number_format($ironCost) . " Copper Flakes to upgrade your vault.", true, 'estate_management.php');
	}
	else
	{
		echo "Are you sure you wish to upgrade your vault? You need " . number_format($ironCost) . " Copper Flakes. You will gain 
		roughly 18% vault capacity each upgrade, relative to the estate's purchase value. Each vault level will increase
		your estate's sell value by roughly 6%<br />
		<b>Your vault is level {$gardenLevel}. Maximum Store: " . number_format(calcVaultCapacity($gardenLevel, $edb['house_price'])) . " Copper Coins.</b>
		<hr />
		<form method='post'>
		<input type='hidden' name='construct' value='yes'>
			<div class='row'>
				<div class='col-6'>
					<input type='submit' class='btn btn-block btn-success' value='Upgrade Vault'>
				</div>
				<div class='col-6'>
					<a href='?action=propertyList' class='btn btn-danger btn-block'>Go Back</a>
				</div>
			</div>
		</form>";
	}
}

function construct_sleep()
{
    global $db, $ir, $userid, $estate, $edb, $h, $api;
    $gardenLevel = $estate['sleepUpgrade'];
    $waterCost = calcStickCosts($gardenLevel, $edb['house_will'])*0.5;
    $stoneCost = calcIronCosts($gardenLevel, $edb['house_will'])*0.1;
    if ($gardenLevel >= $edb['upgradeLevel'])
    {
        alert('danger',"Uh Oh!","You cannot construct your sleeping quarters here.", true, 'estate_management.php');
        die($h->endpage());
    }
    if ($estate['estate'] <= 1)
    {
        alert('danger',"Uh Oh!","You must purchase an estate before you can construct sleeping quarters.", true, 'estate_management.php');
        die($h->endpage());
    }
    if ($gardenLevel > 0)
    {
        alert('danger',"Uh Oh!","You have no need to construct sleeping quarters if you already have one.", true, 'estate_management.php');
        die($h->endpage());
    }
    if (isset($_POST['construct']))
    {
        if (!$api->UserHasItem($userid, 1, $waterCost))
        {
            alert('danger',"Uh Oh!","You need " . number_format($waterCost) . " Sharpened Sticks to construct a garden at this estate.", true, 'estate_management.php');
            die($h->endpage());
        }
        if (!$api->UserHasItem($userid, 23, $stoneCost))
        {
            alert('danger',"Uh Oh!","You need " . number_format($stoneCost) . " Copper Flakes to construct a garden at this estate.", true, 'estate_management.php');
            die($h->endpage());
        }
        $api->UserTakeItem($userid, 1, $waterCost);
        $api->UserTakeItem($userid, 23, $stoneCost);
        $db->query("UPDATE `user_estates` SET `sleepUpgrade` = 1 WHERE `ue_id` = {$estate['ue_id']}");
        $api->SystemLogsAdd($userid, "estate", "Constructed sleeping quarters at Estate ID: {$estate['ue_id']}. ({$waterCost} Sharpened Sticks, {$stoneCost} Copper Flakes.)");
        alert("success","Success!","You have traded " . number_format($waterCost) . " Sharpened Sticks and " . number_format($stoneCost) . "Copper Flakes to construct your sleeping quarters.", true, 'estate_management.php');
    }
    else
    {
        echo "Are you sure you wish to construct your sleeping quarters? You need " . number_format($waterCost) . " Sharpened Sticks and 
        " . number_format($stoneCost) . " Copper Flakes. Your sleeping quarters will start with an effiency of 
        " . calcSleepEfficiency(1, $edb['house_will']) . " / Minute.
		<hr />
		<form method='post'>
		<input type='hidden' name='construct' value='yes'>
			<div class='row'>
				<div class='col-6'>
					<input type='submit' class='btn btn-block btn-success' value='Construct Quarters'>
				</div>
				<div class='col-6'>
					<a href='?action=propertyList' class='btn btn-danger btn-block'>Go Back</a>
				</div>
			</div>
		</form>";
    }
}

function upgrade_sleep()
{
    global $db, $ir, $userid, $estate, $edb, $h, $api;
    $gardenLevel = $estate['sleepUpgrade'];
    $waterCost = calcStickCosts($gardenLevel, $edb['house_will'])*0.5;
    $stoneCost = calcIronCosts($gardenLevel, $edb['house_will'])*0.1;
    if ($gardenLevel >= $edb['upgradeLevel'])
    {
        alert('danger',"Uh Oh!","You cannot upgrade your sleeping quarters anymore at this estate.", true, 'estate_management.php');
        die($h->endpage());
    }
    if ($gardenLevel == 0)
    {
        alert('danger',"Uh Oh!","You cannot upgrade your sleeping quarters if you haven't constructed it.", true, 'estate_management.php');
        die($h->endpage());
    }
    if ($estate['estate'] <= 1)
    {
        alert('danger',"Uh Oh!","You must purchase an estate before you can construct your sleeping quarters.", true, 'estate_management.php');
        die($h->endpage());
    }
    if (isset($_POST['construct']))
    {
        if (!$api->UserHasItem($userid, 1, $waterCost))
        {
            alert('danger',"Uh Oh!","You need " . number_format($waterCost) . " Sharpened Sticks to upgrade your sleeping quarters at this estate.", true, 'estate_management.php');
            die($h->endpage());
        }
        if (!$api->UserHasItem($userid, 23, $stoneCost))
        {
            alert('danger',"Uh Oh!","You need " . number_format($stoneCost) . " Copper Flakes to upgrade your sleeping quarters at this estate.", true, 'estate_management.php');
            die($h->endpage());
        }
        $api->UserTakeItem($userid, 1, $waterCost);
        $api->UserTakeItem($userid, 23, $stoneCost);
        $db->query("UPDATE `user_estates` SET `sleepUpgrade` = `sleepUpgrade` + 1 WHERE `ue_id` = {$estate['ue_id']}");
        $api->SystemLogsAdd($userid, "estate", "Upgraded sleeping quarters at Estate ID: {$estate['ue_id']} to level " . ($gardenLevel + 1) . ". ({$waterCost} Sharpened Sticks, {$stoneCost} Copper Flakes.)");
        alert("success","Success!","You have traded " . number_format($waterCost) . " Sharpened Sticks and " . number_format($stoneCost) . " Copper Flakes to upgrade your sleeping quarters to level " . number_format($gardenLevel+1) . ".", true, 'estate_management.php');
    }
    else
    {
        echo "Are you sure you wish to upgrade your sleeping quarters? You need " . number_format($waterCost) . " Sharpened Sticks and 
        " . number_format($stoneCost) . " Copper Flakes. Your sleeping quarters will have an effiency of 
        " . calcSleepEfficiency(($gardenLevel+1), $edb['house_will']) . " / Minute once upgraded.<br />
		<b>Your sleeping quarters is level {$gardenLevel}.</b>
		<hr />
		<form method='post'>
		<input type='hidden' name='construct' value='yes'>
			<div class='row'>
				<div class='col-6'>
					<input type='submit' class='btn btn-block btn-success' value='Upgrade Quarters'>
				</div>
				<div class='col-6'>
					<a href='?action=propertyList' class='btn btn-danger btn-block'>Go Back</a>
				</div>
			</div>
		</form>";
    }
}

function doSleep()
{
    global $db, $ir, $userid, $estate, $edb, $h, $api;
    $gardenLevel = $estate['sleepUpgrade'];
    if ($gardenLevel == 0)
    {
        alert('danger',"Uh Oh!","You cannot upgrade your sleeping quarters if you haven't constructed it.", true, 'estate_management.php');
        die($h->endpage());
    }
    if ($estate['estate'] <= 1)
    {
        alert('danger',"Uh Oh!","You must purchase an estate before you can construct your sleeping quarters.", true, 'estate_management.php');
        die($h->endpage());
    }
    if (isset($_POST['construct']))
    {
        $fxt = (60*60)*8;
        userGiveEffect($userid, "sleep", $fxt);
        alert("success","Success!", "You have chosen to go to sleep.");
    }
    else
    {
        echo "Are you sure you wish to sleep? Your sleeping quarters has an effiency of " . calcSleepEfficiency(($gardenLevel), $edb['house_will']) . "
         / Minute. You will not be able to do most actions until you wake up. You will sleep for 8 hours, however, you may make yourself up at any time.
		<hr />
		<form method='post'>
		<input type='hidden' name='construct' value='yes'>
			<div class='row'>
				<div class='col-6'>
					<input type='submit' class='btn btn-block btn-success' value='Sleep'>
				</div>
				<div class='col-6'>
					<a href='?action=propertyList' class='btn btn-danger btn-block'>Go Back</a>
				</div>
			</div>
		</form>";
    }
}

function convert()
{
	/*global $db, $ir, $userid, $estate, $h, $api;
	if ($userid != 1)
	{
		alert('danger',"Uh Oh!","Bad.", true, 'estate_management.php');
		die($h->endpage());
	}
	$q=$db->query("SELECT * FROM `users` WHERE `maxwill` > 100");
	while ($r = $db->fetch_row($q))
	{
		$r2=$db->fetch_row($db->query("SELECT * FROM `estates` WHERE `house_will` = {$r['maxwill']}"));
		$db->query("INSERT INTO `user_estates` 
					(`userid`, `estate`, `vault`, 
					`vaultUpgrade`, `gardenUpgrade`, 
					`sleepUpgrade`, `bonusWill`)
					VALUES 
					('{$r['userid']}', '{$r2['house_id']}', '0', 
					'0', '0', '0', '0')");
		$i = $db->insert_id();
		$db->query("UPDATE `users` SET `estate` = {$i} WHERE `userid` = {$r['userid']}");
	}
	$q=$db->query("SELECT * FROM `users`");
	while ($r = $db->fetch_row($q))
	{
		$r2=$db->fetch_row($db->query("SELECT * FROM `estates` WHERE `house_id` = 1"));
		$db->query("INSERT INTO `user_estates` 
					(`userid`, `estate`, `vault`, 
					`vaultUpgrade`, `gardenUpgrade`, 
					`sleepUpgrade`, `bonusWill`)
					VALUES 
					('{$r['userid']}', '{$r2['house_id']}', '0', 
					'0', '0', '0', '0')");
		$i = $db->insert_id();
		$db->query("UPDATE `users` SET `estate` = {$i} WHERE `userid` = {$r['userid']}");
	}
	echo "done";*/
}

function buy_property()
{
	global $db, $ir, $userid, $estate, $edb, $h, $api;
	$_GET['id'] = (isset($_GET['id']) && is_numeric($_GET['id'])) ? abs($_GET['id']) : '';
	if (empty($_GET['id']))
	{
		alert('danger',"Uh Oh!","Please specify the estate you wish to purchase.", true, '?action=sellList');
		die($h->endpage());
	}
	$q=$db->query("SELECT * FROM `estates` WHERE `house_id` = {$_GET['id']}");
	if ($db->num_rows($q) == 0)
	{
		alert('danger',"Uh Oh!","You cannot buy a non-existent estate.", true, '?action=sellList');
		die($h->endpage());
	}
	$r=$db->fetch_row($q);
	if (!$api->UserHasCurrency($userid, 'primary', $r['house_price']))
	{
		alert('danger',"Uh Oh!","You need " . number_format($r['house_price']) . " Copper Coins to purchase this property. 
		You only have " . number_format($ir['primary_currency']) . " Copper Coins.", true, '?action=sellList');
		die($h->endpage());
	}
	if ($ir['level'] < $r['house_level'])
	{
		alert('danger',"Uh Oh!","You need to be at least level " . number_format($r['house_level']) . " before you can 
		purchase this property.", true, '?action=sellList');
		die($h->endpage());
	}
	buyEstate($userid, $_GET['id']);
	$api->UserTakeCurrency($userid, 'primary', $r['house_price']);
	alert('success',"Success!","You have successfully bought {$r['house_name']} for " . number_format($r['house_price']) . " 
	Copper Coins. You may move into this property by checking your property list.", true, '?action=sellList');
	addToEconomyLog('Estates', 'copper', $r['house_price']*-1);
	die($h->endpage());
}

function sell_property()
{
	global $db, $ir, $userid, $estate, $edb, $h, $api;
	$_GET['id'] = (isset($_GET['id']) && is_numeric($_GET['id'])) ? abs($_GET['id']) : '';
	if (empty($_GET['id']))
	{
		alert('danger',"Uh Oh!","Please specify the estate you wish to purchase.", true, '?action=propertyList');
		die($h->endpage());
	}
	$q=$db->query("SELECT * FROM `user_estates` WHERE `ue_id` = {$_GET['id']} AND `userid` = {$userid}");
	if ($db->num_rows($q) == 0)
	{
		alert('danger',"Uh Oh!","You either do not own this property, or it does not exist.", true, '?action=propertyList');
		die($h->endpage());
	}
	$r=$db->fetch_row($q);
	if ($ir['estate'] == $_GET['id'])
	{
		alert('danger',"Uh Oh!","You must move out of your property before you can sell it.", true, '?action=propertyList');
		die($h->endpage());
	}
	if ($r['estate'] == 1)
	{
		alert('danger',"Uh Oh!","You cannot sell this property.", true, '?action=propertyList');
		die($h->endpage());
	}
	if ($r['vault'] > 0)
	{
		alert('danger',"Uh Oh!","You should clear out your vault before you try to sell this property. :)", true, '?action=propertyList');
		die($h->endpage());
	}
	$sellPrice = calculateSellPrice($_GET['id']);
	$sellPrice =  $sellPrice - ($sellPrice * 0.07);
	if (isset($_POST['sell']))
	{
		$api->UserGiveCurrency($userid, 'primary', $sellPrice);
		alert('success',"Success!","You have sold this property back to the game for " . number_format($sellPrice) . " Copper Coins.", true, '?action=propertyList');
		sellEstate($_GET['id']);
		addToEconomyLog('Estates', 'copper', $sellPrice);
	}
	else
	{
		echo "Are you sure you wish to sell this property? You will receive " . number_format($sellPrice) . " Copper 
		Coins if you do. Note this is including the 7% processing fee.
		<hr />
		<form method='post' action='?action=sell&id={$_GET['id']}'>
		<input type='hidden' name='sell' value='yes'>
			<div class='row'>
				<div class='col-6'>
					<input type='submit' class='btn btn-block btn-success' value='Sell!'>
				</div>
				<div class='col-6'>
					<a href='?action=propertyList' class='btn btn-danger btn-block'>Go Back</a>
				</div>
			</div>
		</form>";
	}
}

function game_properties()
{
	global $db, $ir, $userid, $estate, $edb, $h, $api;
	$hq = $db->query("/*qc=on*/SELECT * FROM `estates` WHERE `house_will` > 100 ORDER BY `house_will` ASC");
	echo "<div class='row'>
			<div class='col-12 col-md-4'>
				<a href='?action=propertyList' class='btn btn-block btn-success'>Your Properties</a>
			</div>
			<div class='col-12 col-md-4'>
				<a href='?action=estateMarket' class='btn btn-block btn-danger'>Player Properties</a>
			</div>
			<div class='col-12 col-md-4'>
				<a href='#' class='btn btn-block btn-warning disabled'>For Rent</a>
			</div>
		</div>
		<hr />";
	while ($r = $db->fetch_row($hq))
	{
		$class = ($ir['primary_currency'] >= $r['house_price']) ? "" : "text-danger";
		$lvl = ($ir['level'] >= $r['house_level']) ? "" : "text-danger";
		$disabled = (!empty($class)) ? "disabled" : "";
		$disabled1 = (!empty($lvl)) ? "disabled" : "";
		echo "<div class='row'>
			<div class='col-12'>
				<div class='card'>
					<div class='card-body'>
						<div class='row'>
							<div class='col-6 col-md-3 col-lg-4'>
								<div class='row'>
									<b>{$r['house_name']}</b>
								</div>
							</div>
							<div class='col-6 col-md-3 col-lg-2'>
								<div class='row'>
									Max Will
								</div>
								<div class='row'>
									<small>" . number_format($r['house_will']) . "</small>
								</div>
							</div>
							<div class='col-6 col-md-3 col-lg-1'>
								<div class='row'>
									Level
								</div>
								<div class='row'>
									<small class='{$lvl}'>" . number_format($r['house_level']) . "</small>
								</div>
							</div>
							<div class='col-6 col-md-3'>
								<div class='row'>
									Cost
								</div>
								<div class='row'>
									<small class='{$class}'>" . number_format($r['house_price']) . " Copper Coins</small>
								</div>
							</div>
							<div class='col-12 col-md-12 col-lg-2'>
								<a href='?action=purchase&id={$r['house_id']}' class='btn btn-block btn-primary {$disabled} {$disabled1}'>Buy</a>
							</div>
						</div>
					</div>
				</div>
			</div>
		</div>";
	}
}

function estate_market()
{
	global $db, $ir, $userid, $estate, $edb, $h, $api;
	$q =
        $db->query(
            "/*qc=on*/SELECT `em`.*, `ue`.*, `e`.*
                     FROM `estate_market` AS `em`
                     INNER JOIN `user_estates` AS `ue`
                     ON `em`.`em_estate_id` = `ue`.`ue_id`
                     INNER JOIN `estates` AS `e`
                     ON `e`.`house_id` = `ue`.`estate`
                     ORDER BY `e`.`house_will`, `em`.`em_cost` ASC");
	echo "<div class='row'>
			<div class='col-12 col-md-4'>
				<a href='?action=propertyList' class='btn btn-block btn-success'>Your Properties</a>
			</div>
			<div class='col-12 col-md-4'>
				<a href='?action=sellList' class='btn btn-block btn-primary'>Available Properties</a>
			</div>
			<div class='col-12 col-md-4'>
				<a href='#' class='btn btn-block btn-warning disabled'>For Rent</a>
			</div>
		</div>
		<hr />";
	while ($r = $db->fetch_row($q)) 
	{
		$r['username'] = parseUsername($r['em_adder']);
		$r['em_cost_style'] = ($ir['primary_currency'] >= $r['em_cost']) ? number_format($r['em_cost']) : "<span class='text-danger'>" . number_format($r['em_cost']) . "</span>";
		$link = ($r['em_adder'] == $userid) ? "<a href='?action=marketRemove&id={$r['em_id']}' class='btn btn-danger btn-block'>Remove</a>" : "<a href='?action=marketBuy&id={$r['em_id']}' class='btn btn-primary btn-block'>Purchase</a>";
		echo "<div class='row'>
			<div class='col-12'>
				<div class='card'>
					<div class='card-body'>
						<div class='row'>
							<div class='col-6 col-md-4 col-lg-3'>
								<a href='profile.php?user={$r['em_adder']}'>{$r['username']}</a> [{$r['em_adder']}]
							</div>
							<div class='col-6 col-md-4 col-lg-3'>
								<div class='row'>
									<div class='col-12'>
										{$r['house_name']}
									</div>
									<div class='col-12'>
										<small>Value " . number_format(calculateSellPrice($r['em_id'])) . " Copper Coins</small>
									</div>
								</div>
							</div>
							<div class='col-6 col-md-4 col-lg-3'>
								<div class='row'>
									<div class='col-6'>
										Garden
									</div>
									<div class='col-6'>
										{$r['gardenUpgrade']}
									</div>
								</div>
								<div class='row'>
									<div class='col-6'>
										Vault
									</div>
									<div class='col-6'>
										{$r['vaultUpgrade']}
									</div>
								</div>
								<div class='row'>
									<div class='col-6'>
										Sleep
									</div>
									<div class='col-6'>
										{$r['sleepUpgrade']}
									</div>
								</div>
							</div>
							<div class='col-6 col-md-12 col-lg-3'>
								<div class='row'>
									<div class='col-12'>
										{$r['em_cost_style']} <small>Copper Coins</small>
									</div>
									<div class='col-12'>
										{$link}
									</div>
								</div>
							</div>
						</div>
					</div>
				</div>
			</div>
		</div>";
	}
}
function estate_market_buy()
{
	global $db, $ir, $userid, $estate, $edb, $h, $api;
	$_GET['id'] = (isset($_GET['id']) && is_numeric($_GET['id'])) ? abs($_GET['id']) : '';
	if (empty($_GET['id']))
	{
		alert('danger',"Uh Oh!","Please specify the estate you wish to purchase.", true, '?action=estateMarket');
		die($h->endpage());
	}
	$q=$db->query("SELECT * FROM `estate_market` WHERE `em_id` = {$_GET['id']}");
	if ($db->num_rows($q) == 0)
	{
		alert('danger',"Uh Oh!","This estate offer is invalid or does not exist.", true, '?action=estateMarket');
		die($h->endpage());
	}
	$r=$db->fetch_row($q);
	if ($r['em_adder'] == $userid)
	{
		alert('danger',"Uh Oh!","You cannot buy your own estate offers. O_o Come on dude.", true, '?action=estateMarket');
		die($h->endpage());
	}
	if ($ir['primary_currency'] < $r['em_cost'])
	{
		alert('danger',"Uh Oh!","You need " . number_format($r['em_cost']) . " Copper Coins to buy this estate. You only have " . number_format($ir['primary_currency']) . " Copper Coins.", true, '?action=estateMarket');
		die($h->endpage());
	}
	if ($api->SystemCheckUsersIPs($userid, $r['em_adder']))
	{
		alert('danger',"Uh Oh!","You cannot purchase an estate from another player who shares an IP address with you.", true, '?action=estateMarket');
		die($h->endpage());
	}
	$remove = 0.13;
	$removePerc = $remove * 100;
	$toRemove = $r['em_cost'] * $remove;
	$toPlayer = $r['em_cost'] - $toRemove;
	addToEconomyLog('Market Fees', 'copper', ($toRemove)*-1);
	$api->GameAddNotification($r['em_adder'],"Your estate has been sold for " . number_format($r['em_cost']) . " Copper Coins. You were charged a {$removePerc}% fee of " . number_format($toRemove) . " Copper Coins. You received a total of " . number_format($toPlayer) . " Copper Coins.");
	$api->UserTakeCurrency($userid, 'primary', $r['em_cost']);
	$api->UserGiveCurrency($r['em_adder'], 'primary', $toPlayer);
	alert('success',"Success!","You have purchased this property for " . number_format($r['em_cost']) . " Copper Coins! Congratulations!", true, '?action=estateMarket');
	$db->query("UPDATE `user_estates` SET `userid` = {$userid} WHERE `ue_id` = {$r['em_estate_id']}");
	$db->query("DELETE FROM `estate_market` WHERE `em_id` = {$r['em_id']}");
	estate_market();
}
function estate_market_sell()
{
	global $db, $ir, $userid, $estate, $edb, $h, $api;
	$_GET['id'] = (isset($_GET['id']) && is_numeric($_GET['id'])) ? abs($_GET['id']) : '';
	if (empty($_GET['id']))
	{
		alert('danger',"Uh Oh!","Please specify the estate you wish to sell.", true, '?action=propertyList');
		die($h->endpage());
	}
	$q=$db->query("SELECT * FROM `user_estates` WHERE `ue_id` = {$_GET['id']} AND `userid` = {$userid}");
	if ($db->num_rows($q) == 0)
	{
		alert('danger',"Uh Oh!","This estate offer is invalid or does not exist.", true, '?action=propertyList');
		die($h->endpage());
	}
	$r=$db->fetch_row($q);
	if (isset($_POST['price']))
	{
		$_POST['price'] = (isset($_POST['price']) && is_numeric($_POST['price'])) ? abs($_POST['price']) : '';
		if (empty($_POST['price']))
		{
			alert('danger',"Uh Oh!","Please specify a valid estate to sell.", true, '?action=propertyList');
			die($h->endpage());
		}
		$db->query("INSERT INTO `estate_market` 
		(`em_adder`, `em_estate_id`, `em_cost`) VALUES 
		('{$userid}', '{$_GET['id']}', '{$_POST['price']}')");
		$db->query("UPDATE `user_estates` SET `userid` = 0 WHERE `ue_id` = {$_GET['id']}");
		alert('success',"Success!","You have listed this property for " . number_format($_POST['price']) . " Copper Coins! You will be charged 13% upon selling this estate.", true, '?action=estateMarket');
	}
	else
	{
		echo "
		<form method='post'>
			<div class='row'>
				<div class='col-12'>
					<div class='card'>
						<div class='card-body'>
							<div class='row'>
								How much do you wish to list your property for? You will be charged a 13% fee upon a completed buy.
							</div>
							<div class='row'>
								<div class='col-12'>
									<b>Copper Coins</b>
								</div>
								<div class='col-8 col-md-9'>
									<input type='number' class='form-control' required='1' name='price' id='price'>
								</div>
								<div class='col-4 col-md-3'>
									<input type='submit' class='btn btn-secondary btn-block' value='Sell'>
								</div>
							</div>
						</div>
					</div>
				</div>
			</div>
		</form>";
	}
}
function estate_market_remove()
{
	global $db, $ir, $userid, $estate, $edb, $h, $api;
	$_GET['id'] = (isset($_GET['id']) && is_numeric($_GET['id'])) ? abs($_GET['id']) : '';
	if (empty($_GET['id']))
	{
		alert('danger',"Uh Oh!","Please specify the estate you wish to remove.", true, '?action=estateMarket');
		die($h->endpage());
	}
	$q=$db->query("SELECT * FROM `estate_market` WHERE `em_id` = {$_GET['id']}");
	if ($db->num_rows($q) == 0)
	{
		alert('danger',"Uh Oh!","This estate offer is invalid or does not exist.", true, '?action=estateMarket');
		die($h->endpage());
	}
	$r=$db->fetch_row($q);
	if ($r['em_adder'] != $userid)
	{
		alert('danger',"Uh Oh!","You cannot remove market offers that do not belong to you.", true, '?action=estateMarket');
		die($h->endpage());
	}
	$db->query("UPDATE `user_estates` SET `userid` = {$userid} WHERE `ue_id` = {$r['em_estate_id']}");
	$db->query("DELETE FROM `estate_market` WHERE `em_id` = {$_GET['id']}");
	alert("success","Success!","Estate listing has been removed successfully. Your property is now accessible via your property list.",false);
	estate_market();
}
include('forms/estate_popup.php');
$h->endpage();