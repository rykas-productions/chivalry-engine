<?php
$expMod=1.0;
$macropage = ('farm.php');
require('globals.php');
echo "<h3>Farmlands</h3><hr />";
if ($api->UserStatus($userid,'dungeon') || $api->UserStatus($userid,'infirmary'))
{
	alert('danger',"Uh Oh!","You cannot visit your farm while in the infirmary or dungeon.",true,'explore.php');
	die($h->endpage());
}
$q=$db->query("/*qc=on*/SELECT * FROM `farm_users` WHERE `userid` = {$userid}");
if ($db->num_rows($q) == 0)
{
    $db->query("INSERT INTO `farm_users` (`userid`) VALUES ('{$userid}')");
}
$FU = ($db->fetch_row($db->query("/*qc=on*/SELECT * FROM `farm_users` WHERE `userid` = {$userid}")));

//Config options
$farmconfig['farmlandCost']		=	round(500000+(500000*levelMultiplier($ir['level'])));
$farmconfig['startingFields'] 	=	2;
$farmconfig['maxFields']		=	12;
$farmconfig['wellnessPerTend']	=	Random(3,9);
$farmconfig['wellnessPerHarv']	=	Random(15,30);
$farmconfig['wellnessPerPlant']	=	Random(7,14);

if (getSkillLevel($userid, 33) > 0)
{
	$specialnumber=((getSkillLevel($userid,33)*25)/100);
    $farmconfig['wellnessPerTend']=$farmconfig['wellnessPerTend']-($farmconfig['wellnessPerTend']*$specialnumber);
	$farmconfig['wellnessPerHarv']=$farmconfig['wellnessPerHarv']-($farmconfig['wellnessPerHarv']*$specialnumber);
	$farmconfig['wellnessPerPlant']=$farmconfig['wellnessPerPlant']-($farmconfig['wellnessPerPlant']*$specialnumber);
}

//End Config options
if ($FU['farm_water_max'] == 0)
{
    if (isset($_GET['buy']))
    {
        if (!$api->UserHasItem($userid,2,500))
        {
            alert('danger',"Uh Oh!","You do not have enough Heavy Rocks to construct your well.",true,'farm.php');
            die($h->endpage());
        }
        else
        {
            $api->UserTakeItem($userid,2,500);
            $db->query("UPDATE `farm_users` SET `farm_water_available` = 5, `farm_water_max` = 5 WHERE `userid` = {$userid}");
            alert('success',"Success!","You have successfully constructed your farm's well and filled it with water.",true,'farm.php','Get Farming!');
			$loop=0;
			while ($loop < $farmconfig['startingFields'] )
			{
				createField($userid);
				$loop=$loop+1;
			}
            die($h->endpage());
        }
    }
    alert('info','',"You must construct your well before you can tend to your farms. It will cost you 500 Heavy Rocks.",true,'?buy','Construct Well');
    die($h->endpage());
}
doFarmTick();
$frmeen = min(round($FU['farm_water_available'] / $FU['farm_water_max'] * 100), 100);
$frmexp = min(round($FU['farm_xp'] / $FU['xp_needed'] * 100), 100);
echo "<div class='card'>
        <div class='card-body'>
	<div class='row'>
        <div class='col-md-4' align='left'>
			Well Capacity - <span id='wellPercent'>{$frmeen}%</span><br />
			<small>
				[<a href='?action=fill'>Fill Bucket</a>]
			</small>
		</div>
		<div class='col-md'>
			<div class='progress' style='height: 1rem;'>
				<div class='progress-bar bg-success progress-bar-striped progress-bar-animated' role='progressbar' id='wellBar' aria-valuenow='{$FU['farm_water_available']}' aria-valuemin='0' aria-valuemax='100' style='width:{$frmeen}%'>
					<span id='wellBarInfo'>
						{$frmeen}% (" . number_format($FU['farm_water_available']) . " Buckets / " . number_format($FU['farm_water_max']) . " Buckets)
					</span>
				</div>
			</div>
		</div>
	</div>
	<hr />
	<div class='row'>
        <div class='col-sm-6 col-md-4' align='left'>
			Farming Experience - {$frmexp}%<br />
			<small>
				Farming Level {$FU['farm_level']}
			</small>
		</div>
		<div class='col-md'>
			<div class='progress' style='height: 1rem;'>
				<div class='progress-bar bg-warning progress-bar-striped progress-bar-animated' role='progressbar' aria-valuenow='{$FU['farm_xp']}' aria-valuemin='0' aria-valuemax='100' style='width:{$frmexp}%'>
					<span>
						{$frmexp}% (" . number_format($FU['farm_xp']) . " / " . number_format($FU['xp_needed']) . ")
					</span>
				</div>
			</div>
		</div>
	</div>
</div>
</div>
	<hr />";
if (!isset($_GET['action'])) {
    $_GET['action'] = '';
}
switch ($_GET['action']) {
    case 'buyland':
        buyland();
        break;
	case 'plant':
        plantland();
        break;
	case 'createseed':
        createseed();
        break;
	case 'water':
        waterland();
        break;
	case 'fill':
        fillbucket();
        break;
	case 'harvest':
        harvest();
        break;
	case 'tend':
        tend();
        break;
	case 'collect':
        collect();
        break;
	case 'fertilize':
        fertilize();
        break;
    default:
        home();
        break;
}
function home()
{
    global $db,$userid,$api,$h,$ir,$FD;
    echo "Welcome to the farmlands, {$ir['username']}. Tend to your land here.<br />
	<a href='?action=buyland'>Buy Farmland</a><br />";
    $q=$db->query("/*qc=on*/SELECT * FROM `farm_data` WHERE `farm_owner` = {$userid}");
    if ($db->num_rows($q) == 0)
    {
        alert('info','',"You don't have any farmland!",true,'?action=buyland',"Buy Land");
    }
    else
    {
		echo "<div class='row'>
				<div class='col-sm'>
					<h3>Farm ID</h3>
				</div>
				<div class='col-sm'>
					<h3>Farm Info</h3>
				</div>
				<div class='col-sm'>
					<h3>Farm Actions</h3>
				</div>
			</div>
			<hr />";
        while ($r=$db->fetch_row($q))
        {
			$seedID=$r['farm_seed'];
            if ($r['farm_time'] > time())
                $color='text-info';
            else
                $color='text-success';
			if ($r['farm_seed'] == 0)
				$r['farm_seed'] = 'Unplanted';
			else
				$r['farm_seed'] = $api->SystemItemIDtoName($r['farm_seed']);
			echo "<div class='row'>
					<div class='col-sm'>
						Farm ID: {$r['farm_id']}
					</div>
					<div class='col-sm'>
						Seed: {$r['farm_seed']}<br />
						Stage: " . returnStageDetail($r['farm_stage'], $r['farm_time'], $seedID) . " (" . returnCurrentStage($r['farm_id']) . "/" . returnTotalStages($r['farm_id']) . ")<br />
						Wellness: {$r['farm_wellness']}%
					</div>
					<div class='col-sm'>
						" . returnStageActions($r['farm_stage'],$r['farm_id'], $r['farm_time'],$r['farm_seed']) . "
					</div>
				</div>
				<hr />";
        }
    }
    $h->endpage();
}
function buyland()
{
    global $db,$userid,$api,$h,$ir,$farmconfig;
	if (isset($_GET['buy']))
	{
		if (!($api->UserHasCurrency($userid,'primary',$farmconfig['farmlandCost'])))
		{
			alert('danger',"Uh Oh!", "You do not have enough Copper Coins to buy farmland. You need " . number_format($farmconfig['farmlandCost']) . ", but you only have " . number_format($ir['primary_currency']) . " Copper Coins.", true, 'farm.php');
		}
		elseif (countFarmland($userid) == $farmconfig['maxFields'])
		{
			alert('danger',"Uh Oh!", "You may only have a maximum of {$farmconfig['maxFields']} plots of farmland at this time.", true, 'farm.php');
		}
		else
		{
			createField($userid);
			alert('success',"Success!", "You have purchase a plot of farmland!", true, 'farm.php');
			$api->UserTakeCurrency($userid,'primary',$farmconfig['farmlandCost']);
		}
	}
	else
	{
		echo "Would you like to buy a farmland? It'll cost you " . number_format($farmconfig['farmlandCost']) . " Copper Coins.
		<br />
		<a href='?action=buyland&buy=1' class='btn btn-primary'>Buy Land</a> 
		<a href='farm.php' class='btn btn-danger'>Go Back</a>";
	}
	$h->endpage();
}

function plantland()
{
	global $db,$userid,$api,$h,$ir,$farmconfig,$FU;
	$_GET['id'] = (isset($_GET['id']) && is_numeric($_GET['id'])) ? abs($_GET['id']) : '';
    if (empty($_GET['id'])) 
	{
        alert('danger', "Uh Oh!", "Please specify the farm plot you wish to interact with.", true, 'farm.php');
        die($h->endpage());
    }
    $q = $db->query("/*qc=on*/SELECT * FROM `farm_data` WHERE `farm_id` = {$_GET['id']} AND `farm_owner` = {$userid}");
    if ($db->num_rows($q) == 0) 
	{
        alert('danger', "Uh Oh!", "The farm plot you wish to interact with does not exist, or does not belong to you.", true, 'farm.php');
        die($h->endpage());
    }
    $r = $db->fetch_row($q);
	if ($r['farm_stage'] != 0)
	{
		alert('danger', "Uh Oh!", "This plot is not ready to be planted.", true, 'farm.php');
        die($h->endpage());
	}
	if (isset($_POST['seed']))
	{
		$_POST['seed'] = (isset($_POST['seed']) && is_numeric($_POST['seed'])) ? abs($_POST['seed']) : '';
		if (empty($_POST['seed'])) 
		{
			alert('danger', "Uh Oh!", "Please select a valid seed.");
			die($h->endpage());
		}
		elseif (!($api->UserHasItem($userid,$_POST['seed'],1)))
		{
			alert('danger', "Uh Oh!", "You do not have a {$api->SystemItemIDtoName($_POST['seed'])} to plant.");
			die($h->endpage());
		}
		$sq=$db->query("/*qc=on*/SELECT * FROM `farm_produce` WHERE `seed_item` = {$_POST['seed']}");
		if ($db->num_rows($sq) == 0) 
		{
			alert('danger', "Uh Oh!", "That item is not a valid seed, it seems.", true, 'farm.php');
			die($h->endpage());
		}
		$sr = $db->fetch_row($sq);
		if ($sr['seed_lvl_requirement'] > $FU['farm_level'])
		{
			alert('danger', "Uh Oh!", "You need to be have a better Farming level to plant this seed. This seed requires level {$sr['seed_lvl_requirement']}, and you have level {$FU['farm_level']}.", true, 'farm.php');
			die($h->endpage());
		}
		elseif ($sr['seed_wellness_plant'] > $r['farm_wellness'])
		{
			alert('danger', "Uh Oh!", "This seed requires that your plot's wellness be at least {$sr['seed_wellness_plant']}%, and this plot is at {$r['farm_wellness']}%.", true, 'farm.php');
			die($h->endpage());
		}
		if (($r['farm_wellness']-$farmconfig['wellnessPerPlant']) <= 0)
			$farmconfig['wellnessPerPlant']=$r['farm_wellness'];
		if (getSkillLevel($userid, 36) > 0)
			$sr['seed_time']=$sr['seed_time']/2;
		$stagetime=time()+$sr['seed_time'];
		$stagesafetime=$stagetime+$sr['seed_safe_time'];
		$db->query("UPDATE `farm_data` SET `farm_seed` = {$_POST['seed']}, `farm_time` = {$stagetime}, `farm_stage` = 1, `farm_wellness` = `farm_wellness` - {$farmconfig['wellnessPerPlant']} WHERE `farm_id` = {$_GET['id']}");
		$api->UserTakeItem($userid,$_POST['seed'],1);
		$random = Random(2,8);
		$db->query("UPDATE `farm_users` SET `farm_xp` = `farm_xp` + {$random} WHERE `userid` = {$userid}");
		alert('success', "Success!", "Seed has been planted!", true, 'farm.php');
		
	}
	else
	{
		echo "/*qc=on*/SELECT the seed you wish to plant in this field.
		<form method='post' action='?action=plant&id={$_GET['id']}'>
			" . seed_dropdown() . "<br />
			<input type='submit' value='Plant Seed' class='btn btn-primary'>
			<a href='farm.php' class='btn btn-danger'>Go Back</a>
		</form>";
	
	}
	$h->endpage();
}

function waterland()
{
	global $db,$userid,$api,$h,$ir,$farmconfig,$FU;
	$_GET['id'] = (isset($_GET['id']) && is_numeric($_GET['id'])) ? abs($_GET['id']) : '';
    if (empty($_GET['id'])) 
	{
        alert('danger', "Uh Oh!", "Please specify the farm plot you wish to interact with.", true, 'farm.php');
        die($h->endpage());
    }
    $q = $db->query("/*qc=on*/SELECT * FROM `farm_data` WHERE `farm_id` = {$_GET['id']} AND `farm_owner` = {$userid}");
    if ($db->num_rows($q) == 0) 
	{
        alert('danger', "Uh Oh!", "The farm plot you wish to interact with does not exist, or does not belong to you.", true, 'farm.php');
        die($h->endpage());
    }
    $r = $db->fetch_row($q);
	if ($r['farm_wellness'] >= 100)
	{
		alert('danger', "Uh Oh!", "You may only increase this plot's wellness to 100% using water.", true, 'farm.php');
        die($h->endpage());
	}
	if (isset($_GET['do']))
	{
		if (!doWaterAttempt($userid))
		{
			alert('danger', "Uh Oh!", "You do not have enough water in your well, and in your inventory, to water this farmland.", true, 'farm.php');
			die($h->endpage());
		}
		else
		{
			$random=Random(2,6);
			$timegone=Random(300,800);
			$db->query("UPDATE `farm_data` SET `farm_wellness` = `farm_wellness` + {$random} WHERE `farm_id` = {$_GET['id']}");
			if ($r['farm_time'] > 0)
				removeStageTime($_GET['id'],$timegone);
			$db->query("UPDATE `farm_users` SET `farm_xp` = `farm_xp` + {$random} WHERE `userid` = {$userid}");
			alert('success', "Success", "You have successfully watered this farmland, increasing its wellness by {$random}%.", true, 'farm.php');
		}
	}
	else
	{
		echo "Please confirm you wish to water this plot.<br />
			<a href='?action=water&id={$_GET['id']}&do=1' class='btn btn-primary'>Water Plot</a> 
			<a href='farm.php' class='btn btn-danger'>Go Back</a>";
	
	}
	$h->endpage();
}

function fertilize()
{
	global $db,$userid,$api,$h,$ir,$farmconfig,$FU;
	$_GET['id'] = (isset($_GET['id']) && is_numeric($_GET['id'])) ? abs($_GET['id']) : '';
    if (empty($_GET['id'])) 
	{
        alert('danger', "Uh Oh!", "Please specify the farm plot you wish to interact with.", true, 'farm.php');
        die($h->endpage());
    }
    $q = $db->query("/*qc=on*/SELECT * FROM `farm_data` WHERE `farm_id` = {$_GET['id']} AND `farm_owner` = {$userid}");
    if ($db->num_rows($q) == 0) 
	{
        alert('danger', "Uh Oh!", "The farm plot you wish to interact with does not exist, or does not belong to you.", true, 'farm.php');
        die($h->endpage());
    }
    $r = $db->fetch_row($q);
	if ($r['farm_wellness'] >= 150)
	{
		alert('danger', "Uh Oh!", "You may only increase this plot's wellness to 150% using fertilizer.", true, 'farm.php');
        die($h->endpage());
	}
	if (isset($_GET['do']))
	{
		if (!$api->UserHasItem($userid,311,1))
		{
			alert('danger', "Uh Oh!", "You do not have any fertilizer.", true, 'farm.php');
			die($h->endpage());
		}
		else
		{
			$random=Random(6,18);
			$db->query("UPDATE `farm_data` SET `farm_wellness` = `farm_wellness` + {$random} WHERE `farm_id` = {$_GET['id']}");
			$db->query("UPDATE `farm_users` SET `farm_xp` = `farm_xp` + {$random} WHERE `userid` = {$userid}");
			$api->UserTakeItem($userid,311,1);
			alert('success', "Success", "You have successfully fertilized this farmland, increasing its wellness by {$random}%.", true, 'farm.php');
		}
	}
	else
	{
		echo "Please confirm you wish to fertilize this plot.<br />
			<a href='?action=fertilize&id={$_GET['id']}&do=1' class='btn btn-primary'>Fertilize Plot</a> 
			<a href='farm.php' class='btn btn-danger'>Go Back</a>";
	
	}
	$h->endpage();
}
function harvest()
{
	global $db,$userid,$api,$h,$ir,$farmconfig,$FU,$expMod;
	$_GET['id'] = (isset($_GET['id']) && is_numeric($_GET['id'])) ? abs($_GET['id']) : '';
    if (empty($_GET['id'])) 
	{
        alert('danger', "Uh Oh!", "Please specify the farm plot you wish to interact with.", true, 'farm.php');
        die($h->endpage());
    }
    $q = $db->query("/*qc=on*/SELECT * FROM `farm_data` WHERE `farm_id` = {$_GET['id']} AND `farm_owner` = {$userid}");
    if ($db->num_rows($q) == 0) 
	{
        alert('danger', "Uh Oh!", "The farm plot you wish to interact with does not exist, or does not belong to you.", true, 'farm.php');
        die($h->endpage());
    }
    $r = $db->fetch_row($q);
	if ($r['farm_stage'] != 2)
	{
		alert('danger', "Uh Oh!", "This plot is not ready for harvest.", true, 'farm.php');
        die($h->endpage());
	}
	if (isset($_POST['water']))
	{
		$sq=$db->query("/*qc=on*/SELECT * FROM `farm_produce` WHERE `seed_item` = {$r['farm_seed']}");
		$sr=$db->fetch_row($sq);
		if ($sr['seed_wellness_plant'] > $r['farm_wellness'])
		{
			alert('danger', "Uh Oh!", "Improve the plot's wellness to {$sr['seed_wellness_plant']}% before you attempt to harvest this plot.", true, 'farm.php');
			die($h->endpage());
		}
		$cropOutput = round(Random($sr['seed_qty']/2, $sr['seed_qty']*2));
		$xp = round(($cropOutput * $sr['seed_xp']) * $expMod);
		$api->UserGiveItem($userid, $sr['seed_output'], $cropOutput);
		$db->query("UPDATE `farm_users` SET `farm_xp` = `farm_xp` + {$xp} WHERE `userid` = {$userid}");
		if (($r['farm_wellness']-$farmconfig['wellnessPerHarv']) <= 0)
			$farmconfig['wellnessPerHarv']=$r['farm_wellness'];
		$db->query("UPDATE `farm_data` SET `farm_time` = 0, `farm_stage` = 0, `farm_wellness` = `farm_wellness` - {$farmconfig['wellnessPerHarv']}, `farm_seed` = 0 WHERE `farm_id` = {$_GET['id']}");
		alert('success', "Success!", "You have successfully harvested this plot and received {$cropOutput} {$api->SystemItemIDtoName($sr['seed_output'])}(s) and " . number_format($xp) . " farming experience.", true, 'farm.php');
	}
	else
	{
		echo "Please confirm you wish to harvest this plot.
		<form method='post' action='?action=harvest&id={$_GET['id']}'>
			<input type='hidden' value='water' name='water' class='btn btn-primary'>
			<input type='submit' value='Harvest Plot' class='btn btn-primary'>
			<a href='farm.php' class='btn btn-danger'>Go Back</a>
		</form>";
	
	}
	$h->endpage();
}

function collect()
{
	global $db,$userid,$api,$h,$ir,$farmconfig,$FU,$expMod;
	$_GET['id'] = (isset($_GET['id']) && is_numeric($_GET['id'])) ? abs($_GET['id']) : '';
    if (empty($_GET['id'])) 
	{
        alert('danger', "Uh Oh!", "Please specify the farm plot you wish to interact with.", true, 'farm.php');
        die($h->endpage());
    }
    $q = $db->query("/*qc=on*/SELECT * FROM `farm_data` WHERE `farm_id` = {$_GET['id']} AND `farm_owner` = {$userid}");
    if ($db->num_rows($q) == 0) 
	{
        alert('danger', "Uh Oh!", "The farm plot you wish to interact with does not exist, or does not belong to you.", true, 'farm.php');
        die($h->endpage());
    }
    $r = $db->fetch_row($q);
	if ($r['farm_stage'] != 2)
	{
		alert('danger', "Uh Oh!", "This plot is not ready for harvest.", true, 'farm.php');
        die($h->endpage());
	}
	if (isset($_POST['water']))
	{
		$sq=$db->query("/*qc=on*/SELECT * FROM `farm_produce` WHERE `seed_item` = {$r['farm_seed']}");
		$sr=$db->fetch_row($sq);
		if ($sr['seed_wellness_plant'] > $r['farm_wellness'])
		{
			alert('danger', "Uh Oh!", "Improve the plot's wellness to {$sr['seed_wellness_plant']}% before you attempt to harvest this plot.", true, 'farm.php');
			die($h->endpage());
		}
		$cropOutput = round(Random($sr['seed_qty'], $sr['seed_qty']*1.25));
		$xp = round(($cropOutput * ($sr['seed_xp']*0.25)) * $expMod);
		$db->query("UPDATE `farm_users` SET `farm_xp` = `farm_xp` + {$xp} WHERE `userid` = {$userid}");
		$api->UserGiveItem($userid, $r['farm_seed'], $cropOutput);
		if (($r['farm_wellness']-$farmconfig['wellnessPerHarv']) <= 0)
			$farmconfig['wellnessPerHarv']=$r['farm_wellness'];
		$db->query("UPDATE `farm_data` SET `farm_time` = 0, `farm_stage` = 0, `farm_wellness` = `farm_wellness` - {$farmconfig['wellnessPerHarv']}, `farm_seed` = 0 WHERE `farm_id` = {$_GET['id']}");
		alert('success', "Success!", "You have successfully harvested this plot and received {$cropOutput} {$api->SystemItemIDtoName($r['farm_seed'])}(s).", true, 'farm.php');
	}
	else
	{
		echo "Please confirm you wish to collect the seeds from this plot.
		<form method='post' action='?action=collect&id={$_GET['id']}'>
			<input type='hidden' value='water' name='water' class='btn btn-primary'>
			<input type='submit' value='Collect Seeds' class='btn btn-primary'>
			<a href='farm.php' class='btn btn-danger'>Go Back</a>
		</form>";
	
	}
	$h->endpage();
}

function tend()
{
	global $db,$userid,$api,$h,$ir,$farmconfig,$FU;
	$_GET['id'] = (isset($_GET['id']) && is_numeric($_GET['id'])) ? abs($_GET['id']) : '';
    if (empty($_GET['id'])) 
	{
        alert('danger', "Uh Oh!", "Please specify the farm plot you wish to interact with.", true, 'farm.php');
        die($h->endpage());
    }
    $q = $db->query("/*qc=on*/SELECT * FROM `farm_data` WHERE `farm_id` = {$_GET['id']} AND `farm_owner` = {$userid}");
    if ($db->num_rows($q) == 0) 
	{
        alert('danger', "Uh Oh!", "The farm plot you wish to interact with does not exist, or does not belong to you.", true, 'farm.php');
        die($h->endpage());
    }
    $r = $db->fetch_row($q);
	if (($r['farm_stage'] != 1) && ($r['farm_stage'] < 10))
	{
		alert('danger', "Uh Oh!", "Invalid stage.", true, 'farm.php');
        die($h->endpage());
	}
	if ($r['farm_time'] > time())
	{
		alert('danger', "Uh Oh!", "This field is not ready to be tended yet.", true, 'farm.php');
        die($h->endpage());
	}
	if (isset($_POST['water']))
	{
		$sq=$db->query("/*qc=on*/SELECT * FROM `farm_produce` WHERE `seed_item` = {$r['farm_seed']}");
		$sr=$db->fetch_row($sq);
		if (getSkillLevel($userid, 36) > 0)
			$sr['seed_time']=$sr['seed_time']/2;
		if ($sr['seed_wellness_plant'] > $r['farm_wellness'])
		{
			alert('danger', "Uh Oh!", "Improve the plot's wellness to {$sr['seed_wellness_plant']}% before you attempt to tend this plot.", true, 'farm.php');
			die($h->endpage());
		}
		if (($r['farm_wellness']-$farmconfig['wellnessPerTend']) <= 0)
			$farmconfig['wellnessPerTend']=$r['farm_wellness'];
		if (($r['farm_stage'] - 1) == 10)
		{
			$stagetime=time()+$sr['seed_time'];
			$stagesafetime=$stagetime+$sr['seed_safe_time'];
			$db->query("UPDATE `farm_data` SET `farm_time` = {$stagetime}, `farm_stage` = 2, `farm_wellness` = `farm_wellness` - {$farmconfig['wellnessPerTend']} WHERE `farm_id` = {$_GET['id']}");
			alert('success', "Success!", "You've tended this plot. It appears you're closing in on the final harvest.", true, 'farm.php');
		}
		elseif ($r['farm_stage'] == 1)
		{
			$stagetime=time()+$sr['seed_time'];
			$stagesafetime=$stagetime+$sr['seed_safe_time'];
			$db->query("UPDATE `farm_data` SET `farm_time` = {$stagetime}, `farm_stage` = 10, `farm_wellness` = `farm_wellness` - {$farmconfig['wellnessPerTend']} WHERE `farm_id` = {$_GET['id']}");
			alert('success', "Success!", "You've tended this plot. You will seriously maximize your harvest by keeping up with tending the plot.", true, 'farm.php');
		}
		else
		{
			$stagetime=time()+$sr['seed_time'];
			$stagesafetime=$stagetime+$sr['seed_safe_time'];
			$db->query("UPDATE `farm_data` SET `farm_time` = {$stagetime}, `farm_stage` = `farm_stage` + 1, `farm_wellness` = `farm_wellness` - {$farmconfig['wellnessPerTend']} WHERE `farm_id` = {$_GET['id']}");
			alert('success', "Success!", "You've tended this plot.", true, 'farm.php');
		}
		$random=Random(2,6);
		$db->query("UPDATE `farm_users` SET `farm_xp` = `farm_xp` + {$random} WHERE `userid` = {$userid}");
	}
	else
	{
		echo "Please confirm you wish to tend to this plot.
		<form method='post' action='?action=tend&id={$_GET['id']}'>
			<input type='hidden' value='water' name='water' class='btn btn-primary'>
			<input type='submit' value='Tend Plot' class='btn btn-primary'>
			<a href='farm.php' class='btn btn-danger'>Go Back</a>
		</form>";
	
	}
	$h->endpage();
}

function fillbucket()
{
	global $db,$userid,$api,$h,$ir,$farmconfig,$FU;
	echo "
    <span id='wellSuccess'></span>
    <div class='row'>
        <div class='col-6 col-md-4 col-xl-3'>
            <a id='farmWellFillSingle' class='btn btn-primary btn-block updateHoverBtn'>Fill 1 Bucket</a><br />
        </div>
        <div class='col-6 col-md-4 col-xl-3'>
            <a id='farmWellFillFive' class='btn btn-primary btn-block updateHoverBtn'>Fill 5 Buckets</a><br />
        </div>
        <div class='col-6 col-md-4 col-xl-3'>
            <a id='farmWellFillTen' class='btn btn-primary btn-block updateHoverBtn'>Fill 10 Buckets</a><br />
        </div>
        <div class='col-6 col-md-4 col-xl-3'>
            <a id='farmWellFillTwentyFive' class='btn btn-primary btn-block updateHoverBtn'>Fill 25 Buckets</a><br />
        </div>
        <div class='col-6 col-md-4 col-xl-3'>
            <a id='farmWellFillFifty' class='btn btn-primary btn-block updateHoverBtn'>Fill 50 Buckets</a><br />
        </div>
        <div class='col-6 col-md-4 col-xl-3'>
            <a id='farmWellFillHundred' class='btn btn-primary btn-block updateHoverBtn'>Fill 100 Buckets</a><br />
        </div>
    </div>";
	$h->endpage();
}

function createseed()
{
	global $db,$userid,$api,$h,$ir,$farmconfig,$FU;
	if ($userid != 1)
	{
		echo "Go back, now. This will place you in fed in 6 seconds...";
		die($h->endpage());
	}
	if (isset($_POST['seed']))
	{
		$seed = (isset($_POST['seed']) && is_numeric($_POST['seed'])) ? abs($_POST['seed']) : '';
		$output = (isset($_POST['output']) && is_numeric($_POST['output'])) ? abs($_POST['output']) : '';
		$qty = (isset($_POST['qty']) && is_numeric($_POST['qty'])) ? abs($_POST['qty']) : '';
		$stages = (isset($_POST['stages']) && is_numeric($_POST['stages'])) ? abs($_POST['stages']) : '';
		$stagetime = (isset($_POST['stagetime']) && is_numeric($_POST['stagetime'])) ? abs($_POST['stagetime']) : '';
		$stagesafe = (isset($_POST['stagesafe']) && is_numeric($_POST['stagesafe'])) ? abs($_POST['stagesafe']) : '';
		$level = (isset($_POST['level']) && is_numeric($_POST['level'])) ? abs($_POST['level']) : '';
		$well_rot = (isset($_POST['well_rot']) && is_numeric($_POST['well_rot'])) ? abs($_POST['well_rot']) : '';
		$well_plant = (isset($_POST['well_plant']) && is_numeric($_POST['well_plant'])) ? abs($_POST['well_plant']) : '';
		$seed_xp = (isset($_POST['xp']) && is_numeric($_POST['xp'])) ? abs($_POST['xp']) : '';
		if (!$api->SystemItemIDtoName($seed))
		{
			alert("danger","Uh Oh!","That is not a valid seed, or the item does not exist.");
		}
		elseif (!$api->SystemItemIDtoName($output))
		{
			alert("danger","Uh Oh!","That is not a valid crop output, or the item does not exist.");
		}
		elseif (($qty < 1) || ($qty > 1024))
		{
			alert("danger","Uh Oh!","Crop output must be at least 1, and at most, 1024.");
		}
		elseif (($stages < 1) || ($stages > 1024))
		{
			alert("danger","Uh Oh!","Crop stage count must be at least 1, and at most, 1024.");
		}
		elseif ($stagetime < 60)
		{
			alert("danger","Uh Oh!","Crop stage count must be at least 60 seconds.");
		}
		elseif ($stagesafe < 60)
		{
			alert("danger","Uh Oh!","Crop stage count must be at least 60 seconds.");
		}
		elseif ($level < 1)
		{
			alert("danger","Uh Oh!","Seed minimum level must be at least 1.");
		}
		elseif ($well_rot < 0)
		{
			alert("danger","Uh Oh!","Seed wellness rotten percent must be at least 0.");
		}
		elseif ($well_plant < 0)
		{
			alert("danger","Uh Oh!","Seed wellness plant percent must be at least 0.");
		}
		elseif ($seed_xp < 1)
		{
			alert("danger","Uh Oh!","Seed experience gain must be at least 1.");
		}
		$query="INSERT INTO `farm_produce` 
				(`seed_item`, `seed_time`, `seed_safe_time`, 
				`seed_stages`, `seed_output`, `seed_qty`, 
				`seed_lvl_requirement`, `seed_wellness_plant`,
				`seed_wellness_bad`, `seed_xp`) 
				VALUES 
				('{$seed}', '{$stagetime}', '{$stagesafe}', '{$stages}', '{$output}', '{$qty}', '{$level}', '{$well_plant}', '{$well_rot}', '{$seed_xp}')";
		if ($db->query($query))
		{
			alert('success',"Success!","You have created a new seed!", true, 'farm.php');
			$api->SystemLogsAdd($userid,'staff',"Created {$api->SystemItemIDtoName($seed)} seed.");
		}
		else
		{
			alert('danger',"Big oof!","The query failed...");
		}
	}
	else
	{
		echo "Create a seed by filling out this form.
		<form method='post' >
			<table class='table table-bordered'>
				<tr>
					<th>
						Seed Item<br />
						<small>What's the seed base item?</small>
					</th>
					<td>
						" . item_dropdown('seed') . "
					</td>
				</tr>
				<tr>
					<th>
						Seed Output<br />
						<small>What item will this seed produce?</small>
					</th>
					<td>
						" . item_dropdown('output') . "
					</td>
				</tr>
				<tr>
					<th>
						Seed Output Qty<br />
						<small>How many crops will be output by this seed?<br />
						<b>Actual gain will be +/- 50% this number.</b>
						</small>
					</th>
					<td>
						<input type='number' min='1' name='qty' max='1024' required='1' class='form-control' value='1'>
					</td>
				</tr>
				<tr>
					<th>
						Seed Stage Count<br />
						<small>How many stages will this crop have?</small>
					</th>
					<td>
						<input type='number' name='stages' min='1' max='1024' required='1' class='form-control' value='1'>
					</td>
				</tr>
				<tr>
					<th>
						Seed Stage Duration (Seconds)<br />
						<small>How long is a stage for this seed?<br />
							60 = 1 Minute<br />
							3,600 = 1 Hour<br />
							86,400 = 1 Day<br />
							604,800 = 1 Week<br />
							‭2,592,000‬ = 30 Days
						</small>
					</th>
					<td>
						<input type='number' name='stagetime' min='1' max='1000000' required='1' class='form-control' value='3600'>
					</td>
				</tr>
				<tr>
					<th>
						Seed Stage Safe Duration (Seconds)<br />
						<small>How long the plant will rot away if not attended to after each stage.<br />
						<b>We recommend 3X the Stage Duration to not be a dick.</b><br />
							60 = 1 Minute<br />
							3,600 = 1 Hour<br />
							86,400 = 1 Day<br />
							604,800 = 1 Week<br />
							‭2,592,000‬ = 30 Days
						</small>
					</th>
					<td>
						<input type='number' name='stagesafe' min='1' max='1000000' required='1' class='form-control' value='10800'>
					</td>
				</tr>
				<tr>
					<th>
						Seed Level Requirement<br />
						<small>The minimum farming level someone must be to plant this seed.
						</small>
					</th>
					<td>
						<input type='number' name='level' min='1' max='1000000' required='1' class='form-control' value='1'>
					</td>
				</tr>
				<tr>
					<th>
						Seed Wellness Plant<br />
						<small>The minimum wellness a plot should be for this seed to be planted.
						</small>
					</th>
					<td>
						<input type='number' name='well_plant' min='1' max='100' required='1' class='form-control' value='50'>
					</td>
				</tr>
				<tr>
					<th>
						Seed Wellness Rotten<br />
						<small>At this plot wellness, the crops will be lost.<br />
						<b>We recommend 10% of the Wellness Plant number</b>
						</small>
					</th>
					<td>
						<input type='number' name='well_rot' min='1' max='100' required='1' class='form-control' value='5'>
					</td>
				</tr>
				<tr>
					<th>
						Seed Experience<br />
						<small>Flat experience points.<br />
						<b>This is per produce received at harvest, not per seed.</b>
						</small>
					</th>
					<td>
						<input type='number' name='xp' min='1' max='100' required='1' class='form-control' value='5'>
					</td>
				</tr>
				<tr>
					<td colspan='2'>
						<input type='submit' class='btn btn-primary' value='Create Seed'>
					</td>
				</tr>
			</table>
		</form>";
	
	}
	$h->endpage();
}