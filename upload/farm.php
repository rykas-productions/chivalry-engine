<?php
$expMod=1.0;
$macropage = ('farm.php');
require('globals.php');
echo "<h3>" . loadImageAsset("explore/farming.svg", 1.8) . " Farming</h3><hr />";
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
$farmconfig['farmlandCost']		=	round(500000*levelMultiplier($ir['level']));
$farmconfig['startingFields'] 	=	2;
$farmconfig['maxFields']		=	round(8 * levelMultiplier($ir['level']));
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
				<a href='?action=fill' class='btn btn-primary btn-sm'>Fill Bucket</a>
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
	case 'editseed':
	    editseed();
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
	case 'torchland':
	    torchland();
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
    alert('info',"","Welcome to the farmlands, {$ir['username']}. You may tend to your crops and plots of farm here.", true, "?action=buyland", "Buy Farm Plots");
    $q=$db->query("/*qc=on*/SELECT * FROM `farm_data` WHERE `farm_owner` = {$userid}");
    if ($db->num_rows($q) == 0)
    {
        alert('info','',"You don't have any farmland!",true,'?action=buyland',"Buy Land");
    }
    else
    {
		echo "<div class='card'>
                <div class='card-body'>";
        while ($r=$db->fetch_row($q))
        {
			$seedID=$r['farm_seed'];
			if ($r['farm_seed'] == 0)
				$r['farm_seed'] = 'Unplanted';
			else
				$r['farm_seed'] = $api->SystemItemIDtoName($r['farm_seed']);
			$r2 = $db->fetch_row($db->query("SELECT * FROM `farm_produce` WHERE `seed_item` = {$seedID}"));
			echo "<div class='row'>
					<div class='col-12 col-sm-6 col-lg-3 col-xl-2'>
                        <div class='row'>
                            <div class='col-12 col-sm-6 col-lg-12'>
                                " . returnIcon($seedID, 4) . "<br />
                            </div>
                            <div class='col-12 col-sm-6 col-lg-12'>
                                <small>{$r['farm_seed']}</small><br />
                            </div>
                        </div>
					</div>
					<div class='col-12 col-sm-6 col-lg-9 col-xl-4 col-xxl-3'>
						" . returnStageDetail($r['farm_stage'], $r['farm_time'], $r2['seed_safe_time'], $r['farm_id']) . "
                        " . createFarmStageBar(returnCurrentStage($r['farm_id']), returnTotalStages($r['farm_id']), returnStagebyID($r['farm_stage'], $r['farm_time'])) . "
                        " . createWellnessBar($r['farm_wellness']) . "
					</div>
					<div class='col-12 col-xl-6 col-xxl-7'>
						" . returnStageActions($r['farm_stage'],$r['farm_id'], $r['farm_time'],$seedID) . "
					</div>
				</div>
				<hr />";
        }
        echo "</div></div>";
    }
    $h->endpage();
}
function buyland()
{
    global $userid,$api,$h,$ir,$farmconfig;
    $farmconfig['farmlandCost'] = $farmconfig['farmlandCost'] + ((countFarmland($userid) * $farmconfig['farmlandCost']) * 2.2);
	if (isset($_GET['buy']))
	{
		if (!($api->UserHasCurrency($userid,'primary',$farmconfig['farmlandCost'])))
		{
		    alert('danger',"Uh Oh!", "You do not have enough Copper Coins to buy farmland. You need " . shortNumberParse($farmconfig['farmlandCost']) . ", but you only have " . shortNumberParse($ir['primary_currency']) . " Copper Coins.", true, 'farm.php');
		}
		elseif (countFarmland($userid) == $farmconfig['maxFields'])
		{
			alert('danger',"Uh Oh!", "You may only have a maximum of {$farmconfig['maxFields']} plots of farmland at this time.", true, 'farm.php');
		}
		else
		{
			createField($userid);
			alert('success',"Success!", "You have purchased a plot of farmland for " . shortNumberParse($farmconfig['farmlandCost']) . " Copper Coins!", true, 'farm.php');
			$api->UserTakeCurrency($userid,'primary',$farmconfig['farmlandCost']);
		}
	}
	else
	{
	    echo "<div class='row'>
            <div class='col-12'>
                <div class='card'>
                    <div class='card-header'>
                        Farmland Purchase
                    </div>
                    <div class='card-body'>
                        Would you like to purchase a farmland? One will cost you 
                        " . shortNumberParse($farmconfig['farmlandCost']) . " Copper Coins. You currently own 
                        " . countFarmland($userid) . " plots of farmland, and you may own a maximum of {$farmconfig['maxFields']}.
                        <div class='row'>
                            <div class='col-12 col-sm-6'>
                                <a href='?action=buyland&buy=1' class='btn btn-primary btn-block'>Buy Land</a><br />
                            </div>
                            <div class='col-12 col-sm-6'>
                                <a href='farm.php' class='btn btn-danger btn-block'>Go Back</a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>";
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
		$cropSeedName = $api->SystemItemIDtoName($sr['seed_item']);
		if ($sr['seed_lvl_requirement'] > $FU['farm_level'])
		{
			alert('danger', "Uh Oh!", "You need to be have a better Farming level to plant {$cropSeedName}. You need to be level {$sr['seed_lvl_requirement']}, and you have level {$FU['farm_level']}.", true, 'farm.php');
			die($h->endpage());
		}
		elseif ($sr['seed_wellness_plant'] > $r['farm_wellness'])
		{
			alert('danger', "Uh Oh!", "{$cropSeedName} requires that your plot's wellness be at least {$sr['seed_wellness_plant']}%, and this plot is at {$r['farm_wellness']}%.", true, 'farm.php');
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
		alert('success', "Success!", "You have successfully planted a {$cropSeedName} in this plot.", false, 'farm.php');
		die(home());
	}
	else
	{
		echo "<div class='row'>
            <div class='col-12'>
                <div class='card'>
                    <div class='card-header'>
                        Plant Seeds
                    </div>
                    <div class='card-body'>
                        Select the seed you wish to plant in this plot. Remember that growing crops takes loads of TLC! 
                        Simply planting the crop will be enough to drop the plot's wellness.
                        <form method='post' action='?action=plant&id={$_GET['id']}'>
                        <div class='row'>
                            <div class='col-12'>
                                " . seed_dropdown() . "<br />
                            </div>
                            <div class='col-12 col-sm-6'>
                                <input type='submit' value='Plant Seed' class='btn btn-primary btn-block'>
                            </div>
                            <div class='col-12 col-sm-6'>
                                <a href='farm.php' class='btn btn-danger btn-block'>Go Back</a>
                            </div>
                        </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>";
	}
	$h->endpage();
}

function waterland()
{
	global $db,$userid,$api,$h,$ir,$farmconfig,$FU;
	$_GET['id'] = (isset($_GET['id']) && is_numeric($_GET['id'])) ? abs($_GET['id']) : '';
    if (empty($_GET['id'])) 
	{
	    alert('danger', "Uh Oh!", "Please specify the farm plot you wish to interact with.", false);
        die(home());
    }
    $q = $db->query("/*qc=on*/SELECT * FROM `farm_data` WHERE `farm_id` = {$_GET['id']} AND `farm_owner` = {$userid}");
    if ($db->num_rows($q) == 0) 
	{
	    alert('danger', "Uh Oh!", "The farm plot you wish to interact with does not exist, or does not belong to you.", false);
	    die(home());
    }
    $r = $db->fetch_row($q);
	if ($r['farm_wellness'] >= 100)
	{
	    alert('danger', "Uh Oh!", "You may only increase this plot's wellness to 100% using water.", false);
	    die(home());
	}
	if (!doWaterAttempt($userid))
	{
		alert('danger', "Uh Oh!", "You do not have enough water in your well, and in your inventory, to water this farmland.", false);
		die(home());
	}
	else
	{
		$random=Random(2,6);
		if (Random(1, 100) == 64)
		{
		     $timegone=Random(30,100);
		     if ($r['farm_time'] > 0)
		         removeStageTime($_GET['id'],$timegone);
		}
			$db->query("UPDATE `farm_data` SET `farm_wellness` = `farm_wellness` + {$random} WHERE `farm_id` = {$_GET['id']}");
		$db->query("UPDATE `farm_users` SET `farm_xp` = `farm_xp` + {$random} WHERE `userid` = {$userid}");
		alert('success', "Success", "You have successfully watered this farmland, increasing its wellness by {$random}%.", false);
		die(home());
	}
	$h->endpage();
}

function fertilize()
{
	global $db,$userid,$api,$h,$FU;
	$_GET['id'] = (isset($_GET['id']) && is_numeric($_GET['id'])) ? abs($_GET['id']) : '';
    if (empty($_GET['id'])) 
	{
        alert('danger', "Uh Oh!", "Please specify the farm plot you wish to interact with.", false);
        die(home());
    }
    $q = $db->query("/*qc=on*/SELECT * FROM `farm_data` WHERE `farm_id` = {$_GET['id']} AND `farm_owner` = {$userid}");
    if ($db->num_rows($q) == 0) 
	{
	    alert('danger', "Uh Oh!", "The farm plot you wish to interact with does not exist, or does not belong to you.", false);
	    die(home());;
    }
    $r = $db->fetch_row($q);
	if ($r['farm_wellness'] >= 150)
	{
	    alert('danger', "Uh Oh!", "You may only increase this plot's wellness to 150% using fertilizer.", false);
	    die(home());
	}
	if (isset($_GET['do']))
	{
		if (!$api->UserHasItem($userid,311,1))
		{
		    alert('danger', "Uh Oh!", "You do not have any fertilizer in your inventory. You can create some in the workshop.", false);
		    die(home());
		}
		else
		{
		    $timegone=Random(300,800);
		    if ($r['farm_time'] > 0)
		        removeStageTime($_GET['id'],$timegone);
			$random=Random(6,18);
			$db->query("UPDATE `farm_data` SET `farm_wellness` = `farm_wellness` + {$random} WHERE `farm_id` = {$_GET['id']}");
			$db->query("UPDATE `farm_users` SET `farm_xp` = `farm_xp` + {$random} WHERE `userid` = {$userid}");
			$api->UserTakeItem($userid,311,1);
			alert('success', "Success", "You have successfully fertilized this farmland, increasing its wellness by {$random}%.", false);
			die(home());
		}
	}
	else
	{
	    echo "
            <div class='row'>
            <div class='col-12'>
                <div class='card'>
                    <div class='card-header'>
                        Fertilize Plot
                    </div>
                    <div class='card-body'>
                        Are you sure you wish to fertilize this plot? You need at least one Fertilizer. This will increase your plot's 
                        wellness by 6-18%, and grant you similar farming experience. This will also decrease your plot's stage time by 5-12 minutes.
                        <div class='row'>
                            <div class='col-12 col-sm-6'>
                                <a href='?action=fertilize&id={$_GET['id']}&do=1' class='btn btn-primary btn-block'>Fertilize</a>
                            </div>
                            <div class='col-12 col-sm-6'>
                                <a href='farm.php' class='btn btn-danger btn-block'>Go Back</a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>";
	}
	$h->endpage();
}

function torchland()
{
    global $db,$userid,$api,$h,$ir,$farmconfig,$FU;
    $_GET['id'] = (isset($_GET['id']) && is_numeric($_GET['id'])) ? abs($_GET['id']) : '';
    if (empty($_GET['id']))
    {
        alert('danger', "Uh Oh!", "Please specify the farm plot you wish to interact with.", false);
        die(home());
    }
    $q = $db->query("/*qc=on*/SELECT * FROM `farm_data` WHERE `farm_id` = {$_GET['id']} AND `farm_owner` = {$userid}");
    if ($db->num_rows($q) == 0)
    {
        alert('danger', "Uh Oh!", "The farm plot you wish to interact with does not exist, or does not belong to you.", false);
        die(home());
    }
    $r = $db->fetch_row($q);
    if (isset($_GET['do']))
    {
        deleteField($_GET['id']);
        alert('success',"Success!","You have successfully torched this plot. It is now forever lost to the sands of time...", false);
        die(home());
    }
    else
    {
        echo "<div class='row'>
            <div class='col-12'>
                <div class='card'>
                    <div class='card-header'>
                        Plot Torching
                    </div>
                    <div class='card-body'>
                        Are you sure you wish to torch this plot? This will permenantly delete it, and any seeds you may have 
                        growing on it.
                        <div class='row'>
                            <div class='col-12 col-sm-6'>
                                <a href='?action=torchland&id={$_GET['id']}&do=1' class='btn btn-primary btn-block'>Burn It</a>
                            </div>
                            <div class='col-12 col-sm-6'>
                                <a href='farm.php' class='btn btn-danger btn-block'>Go Back</a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>";
    }
}
function harvest()
{
	global $db,$userid,$api,$h,$ir,$farmconfig,$FU,$expMod;
	$_GET['id'] = (isset($_GET['id']) && is_numeric($_GET['id'])) ? abs($_GET['id']) : '';
	$q = $db->query("/*qc=on*/SELECT * FROM `farm_data` WHERE `farm_id` = {$_GET['id']} AND `farm_owner` = {$userid}");
	if ($db->num_rows($q) == 0)
	{
	    alert('danger', "Uh Oh!", "The farm plot you wish to interact with does not exist, or does not belong to you.", false);
	    die(home());
	}
	$r = $db->fetch_row($q);
	
	$sq=$db->query("/*qc=on*/SELECT * FROM `farm_produce` WHERE `seed_item` = {$r['farm_seed']}");
	$sr=$db->fetch_row($sq);
	
	$cropOutput = round(Random($sr['seed_qty']/2, $sr['seed_qty']*2));
	$cropSeedName = $api->SystemItemIDtoName($sr['seed_item']);
    if (empty($_GET['id'])) 
	{
	    alert('danger', "Uh Oh!", "Please specify the farm plot you wish to interact with.", false);
        die(home());
    }
	if ($r['farm_stage'] != 2)
	{
	    alert('danger', "Uh Oh!", "This plot is not ready for harvest.", false);
	    die(home());
	}
	if (isset($_POST['water']))
	{
		if ($sr['seed_wellness_plant'] > $r['farm_wellness'])
		{
		    alert('danger', "Uh Oh!", "Improve the plot's wellness to {$sr['seed_wellness_plant']}% before you attempt to harvest this plot.", false);
		    die(home());
		}
		$xp = round(($cropOutput * $sr['seed_xp']) * $expMod);
		$api->UserGiveItem($userid, $sr['seed_output'], $cropOutput);
		$db->query("UPDATE `farm_users` SET `farm_xp` = `farm_xp` + {$xp} WHERE `userid` = {$userid}");
		if (($r['farm_wellness']-$farmconfig['wellnessPerHarv']) <= 0)
			$farmconfig['wellnessPerHarv']=$r['farm_wellness'];
		$db->query("UPDATE `farm_data` SET `farm_time` = 0, `farm_stage` = 0, `farm_wellness` = `farm_wellness` - {$farmconfig['wellnessPerHarv']}, `farm_seed` = 0 WHERE `farm_id` = {$_GET['id']}");
		alert('success', "Success!", "You have successfully harvested your {$cropSeedName} and received {$cropOutput} {$api->SystemItemIDtoName($sr['seed_output'])}s and " . number_format($xp) . " farming experience.", false);
		die(home());
	}
	else
	{
		echo "<div class='row'>
            <div class='col-12'>
                <div class='card'>
                    <div class='card-header'>
                        Crop Harvesting
                    </div>
                    <div class='card-body'>
                        Please confirm you want to harvest your {$api->SystemItemIDtoName($sr['seed_item'])}. This will grant you 
                        between " . round($sr['seed_qty'] / 2) . "-" . round($sr['seed_qty'] * 2) . " 
                        <a href='iteminfo.php?ID={$sr['seed_output']}'>{$api->SystemItemIDtoName($sr['seed_output'])}s</a>.
                        <form method='post' action='?action=harvest&id={$_GET['id']}'>
                        <div class='row'>
                            <div class='col-12 col-sm-6'>
                                <input type='submit' value='Harvest Plot' class='btn btn-primary btn-block'><br />
                            </div>
                            <div class='col-12 col-sm-6'>
                                <a href='farm.php' class='btn btn-danger btn-block'>Go Back</a>
                            </div>
                        </div>
                        <input type='hidden' value='water' name='water' class='btn btn-primary'>
                        </form>
                    </div>
                </div>
            </div>
        </div>";
	}
	$h->endpage();
}

function collect()
{
	global $db,$userid,$api,$h,$ir,$farmconfig,$FU,$expMod;
	$_GET['id'] = (isset($_GET['id']) && is_numeric($_GET['id'])) ? abs($_GET['id']) : '';
    if (empty($_GET['id'])) 
	{
	    alert('danger', "Uh Oh!", "Please specify the farm plot you wish to interact with.", false);
	    die(home());
    }
    $q = $db->query("/*qc=on*/SELECT * FROM `farm_data` WHERE `farm_id` = {$_GET['id']} AND `farm_owner` = {$userid}");
    if ($db->num_rows($q) == 0) 
	{
	    alert('danger', "Uh Oh!", "The farm plot you wish to interact with does not exist, or does not belong to you.", false);
	    die(home());
    }
    $r = $db->fetch_row($q);
    $sq=$db->query("/*qc=on*/SELECT * FROM `farm_produce` WHERE `seed_item` = {$r['farm_seed']}");
    $sr=$db->fetch_row($sq);
    $cropOutput = round(Random($sr['seed_qty'], $sr['seed_qty']*2));
    $xpPerSeed = ($sr['seed_xp']*0.75) * $expMod;
    $xp = round($cropOutput * $xpPerSeed);
    $cropSeedName = $api->SystemItemIDtoName($sr['seed_item']);
	if ($r['farm_stage'] != 2)
	{
	    alert('danger', "Uh Oh!", "This plot is not ready for harvest.", false);
	    die(home());
	}
	if (isset($_POST['water']))
	{
		if ($sr['seed_wellness_plant'] > $r['farm_wellness'])
		{
		    alert('danger', "Uh Oh!", "Improve the plot's wellness to {$sr['seed_wellness_plant']}% before you attempt to harvest this plot.", false);
		    die(home());
		}
		
		$db->query("UPDATE `farm_users` SET `farm_xp` = `farm_xp` + {$xp} WHERE `userid` = {$userid}");
		$api->UserGiveItem($userid, $r['farm_seed'], $cropOutput);
		if (($r['farm_wellness']-$farmconfig['wellnessPerHarv']) <= 0)
			$farmconfig['wellnessPerHarv']=$r['farm_wellness'];
		$db->query("UPDATE `farm_data` SET `farm_time` = 0, `farm_stage` = 0, `farm_wellness` = `farm_wellness` - {$farmconfig['wellnessPerHarv']}, `farm_seed` = 0 WHERE `farm_id` = {$_GET['id']}");
		alert('success', "Success!", "You have successfully harvested this plot and received {$cropOutput} {$api->SystemItemIDtoName($r['farm_seed'])}(s).", false);
		die(home());
	}
	else
	{
		echo "<div class='row'>
            <div class='col-12'>
                <div class='card'>
                    <div class='card-header'>
                        Seed Harvesting
                    </div>
                    <div class='card-body'>
                        Please confirm you wish to harvest the seeds from this plant? You will receive 
                        {$sr['seed_qty']}-" . round($sr['seed_qty'] * 2) . " {$cropSeedName}s, and {$xpPerSeed} 
                        farming experience per seed gathered.
                        <form method='post' action='?action=collect&id={$_GET['id']}'>
                        <div class='row'>
                            <div class='col-12 col-sm-6'>
                                <input type='submit' value='Harvest Seeds' class='btn btn-primary btn-block'>
                            </div>
                            <div class='col-12 col-sm-6'>
                                <a href='farm.php' class='btn btn-danger btn-block'>Go Back</a>
                            </div>
                        </div>
                        <input type='hidden' value='water' name='water' class='btn btn-primary'>
                        </form>
                    </div>
                </div>
            </div>
        </div>";
	}
	$h->endpage();
}

function tend()
{
	global $db,$userid,$api,$h,$ir,$farmconfig,$FU;
	$_GET['id'] = (isset($_GET['id']) && is_numeric($_GET['id'])) ? abs($_GET['id']) : '';
    if (empty($_GET['id'])) 
	{
	    alert('danger', "Uh Oh!", "Please specify the farm plot you wish to interact with.", false);
	    die(home());
    }
    $q = $db->query("/*qc=on*/SELECT * FROM `farm_data` WHERE `farm_id` = {$_GET['id']} AND `farm_owner` = {$userid}");
    if ($db->num_rows($q) == 0) 
	{
	    alert('danger', "Uh Oh!", "The farm plot you wish to interact with does not exist, or does not belong to you.", false);
	    die(home());
    }
    $r = $db->fetch_row($q);
    $sq=$db->query("/*qc=on*/SELECT * FROM `farm_produce` WHERE `seed_item` = {$r['farm_seed']}");
    $sr=$db->fetch_row($sq);
    $cropSeedName = $api->SystemItemIDtoName($sr['seed_item']);
	if (($r['farm_stage'] != 1) && ($r['farm_stage'] < 10))
	{
		alert('danger', "Uh Oh!", "Invalid stage.", true, 'farm.php');
        die($h->endpage());
	}
	if ($r['farm_time'] > time())
	{
	    alert('danger', "Uh Oh!", "This field is not ready to be tended yet.", false);
	    die(home());
	}
	if (isset($_POST['water']))
	{
		if (getSkillLevel($userid, 36) > 0)
			$sr['seed_time']=$sr['seed_time']/2;
		if ($sr['seed_wellness_plant'] > $r['farm_wellness'])
		{
		    alert('danger', "Uh Oh!", "Improve the plot's wellness to {$sr['seed_wellness_plant']}% before you attempt to tend this plot.", false);
		    die(home());
		}
		if (($r['farm_wellness']-$farmconfig['wellnessPerTend']) <= 0)
			$farmconfig['wellnessPerTend']=$r['farm_wellness'];
		if (($r['farm_stage'] - 1) == 10)
		{
			$stagetime=time()+$sr['seed_time'];
			$stagesafetime=$stagetime+$sr['seed_safe_time'];
			$db->query("UPDATE `farm_data` SET `farm_time` = {$stagetime}, `farm_stage` = 2, `farm_wellness` = `farm_wellness` - {$farmconfig['wellnessPerTend']} WHERE `farm_id` = {$_GET['id']}");
			alert('success', "Success!", "You've tended your {$cropSeedName}. It appears you're closing in on the final harvest.", true, 'farm.php');
		}
		elseif ($r['farm_stage'] == 1)
		{
			$stagetime=time()+$sr['seed_time'];
			$stagesafetime=$stagetime+$sr['seed_safe_time'];
			$db->query("UPDATE `farm_data` SET `farm_time` = {$stagetime}, `farm_stage` = 10, `farm_wellness` = `farm_wellness` - {$farmconfig['wellnessPerTend']} WHERE `farm_id` = {$_GET['id']}");
			alert('success', "Success!", "You've tended your {$cropSeedName}. You will seriously maximize your harvest by keeping up with tending the plot.", true, 'farm.php');
		}
		else
		{
			$stagetime=time()+$sr['seed_time'];
			$stagesafetime=$stagetime+$sr['seed_safe_time'];
			$db->query("UPDATE `farm_data` SET `farm_time` = {$stagetime}, `farm_stage` = `farm_stage` + 1, `farm_wellness` = `farm_wellness` - {$farmconfig['wellnessPerTend']} WHERE `farm_id` = {$_GET['id']}");
			alert('success', "Success!", "You've tended your {$cropSeedName}. Remember to keep up with the TLC.", true, 'farm.php');
		}
		$random=Random(2 * $r['farm_stage'], 6 * $r['farm_stage']);
		$db->query("UPDATE `farm_users` SET `farm_xp` = `farm_xp` + {$random} WHERE `userid` = {$userid}");
		die(home());
	}
	else
	{
	    echo "<div class='row'>
            <div class='col-12'>
                <div class='card'>
                    <div class='card-header'>
                        Crop Tending
                    </div>
                    <div class='card-body'>
                        Please confirm you wish to tend your {$cropSeedName}. This will start the next stage of the farming process,
                         and will drop this plot's wellness slightly.
                        <form method='post' action='?action=tend&id={$_GET['id']}'>
                        <div class='row'>
                            <div class='col-12 col-sm-6'>
                                <input type='submit' value='Tend Plot' class='btn btn-primary btn-block'><br />
                            </div>
                            <div class='col-12 col-sm-6'>
                                <a href='farm.php' class='btn btn-danger btn-block'>Go Back</a>
                            </div>
                        </div>
                        <input type='hidden' value='water' name='water' class='btn btn-primary'>
                        </form>
                    </div>
                </div>
            </div>
        </div>";
	}
	$h->endpage();
}

function fillbucket()
{
	global $FU, $h;
	echo "
    <span id='wellSuccess'></span>
    <div class='row'>
        <div class='col-6 col-md-4 col-xl-3 col-xxl-2'>
            <a id='farmWellFillSingle' class='btn btn-primary btn-block updateHoverBtn'>Fill 1 Bucket</a><br />
        </div>
        <div class='col-6 col-md-4 col-xl-3 col-xxl-2'>
            <a id='farmWellFillFive' class='btn btn-primary btn-block updateHoverBtn'>Fill 5 Buckets</a><br />
        </div>
        <div class='col-6 col-md-4 col-xl-3 col-xxl-2'>
            <a id='farmWellFillTen' class='btn btn-primary btn-block updateHoverBtn'>Fill 10 Buckets</a><br />
        </div>
        <div class='col-6 col-md-4 col-xl-3 col-xxl-2'>
            <a id='farmWellFillTwentyFive' class='btn btn-primary btn-block updateHoverBtn'>Fill 25 Buckets</a><br />
        </div>
        <div class='col-6 col-md-4 col-xl-3 col-xxl-2'>
            <a id='farmWellFillFifty' class='btn btn-primary btn-block updateHoverBtn'>Fill 50 Buckets</a><br />
        </div>
        <div class='col-6 col-md-4 col-xl-3 col-xxl-2'>
            <a id='farmWellFillSeventyFive' class='btn btn-primary btn-block updateHoverBtn'>Fill 75 Buckets</a><br />
        </div>
        <div class='col-6 col-md-4 col-xl-3 col-xxl-2'>
            <a id='farmWellFillHundred' class='btn btn-primary btn-block updateHoverBtn'>Fill 100 Buckets</a><br />
        </div>
        <div class='col-6 col-md-4 col-xl-3 col-xxl-2'>
            <a id='farmWellFillOneFifty' class='btn btn-primary btn-block updateHoverBtn'>Fill 150 Buckets</a><br />
        </div>
        <div class='col-6 col-md-4 col-xl-3 col-xxl-2'>
            <a id='farmWellFillTwoHundred' class='btn btn-primary btn-block updateHoverBtn'>Fill 200 Buckets</a><br />
        </div>
        <div class='col-6 col-md-4 col-xl-3 col-xxl-2'>
            <a id='farmWellFillFiveHundred' class='btn btn-primary btn-block updateHoverBtn'>Fill 500 Buckets</a><br />
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

function editseed()
{
    global $db, $api, $userid, $ir, $h;
    if ($ir['user_level'] != 'Admin')
    {
        alert('danger',"Uh Oh!","You do not have access to this place.", true, 'farm.php');
        die($h->endpage());
    }
    if (!isset($_POST['step']))
        $_POST['step'] = 0;
    if ($_POST['step'] == 1)
    {
        if (!isset($_POST['verf']) || !verify_csrf_code('staff_editseed1', stripslashes($_POST['verf']))) 
        {
            alert('danger', "Action Blocked!", "Forms expire fairly quickly after opening them. Go back and submit the form quicker!");
            die($h->endpage());
        }
        $seed = (isset($_POST['seed']) && is_numeric($_POST['seed'])) ? abs(intval($_POST['seed'])) : 0;
        if (empty($seed)) 
        {
            alert('danger', "Uh Oh!", "Please fill out the form completely before submitting it.");
            die($h->endpage());
        }
        $q = $db->query("SELECT * FROM `farm_produce` WHERE `seed_item` = {$seed}");
        if ($db->num_rows($q) == 0)
        {
            alert('danger', "Uh Oh!", "The seed you selected is not set up as a valid seed.");
            die($h->endpage());
        }
        $r = $db->fetch_row($q);
        $csrf = request_csrf_html('staff_editseed2');
        echo "<div class='card'>
            <div class='card-header'>
                Editing {$api->SystemItemIDtoName($r['seed_item'])}...
            </div>
            <form method='post'>
            <div class='card-body'>
                <div class='row'>
                    <div class='col-12'>
                        <small>Seed Output</small>
                    </div>
                    <div class='col-12'>
                        " . item_dropdown("output", $r['seed_output']) . "
                    </div>
                    <div class='col-12'>
                        <small>Seed Output Qty</small>
                    </div>
                    <div class='col-12'>
                        <input type='number' name='seed_qty' value='{$r['seed_qty']}' class='form-control'>
                    </div>
                    <div class='col-12'>
                        <small>Seed Stages</small>
                    </div>
                    <div class='col-12'>
                        <input type='number' name='seed_stages' value='{$r['seed_stages']}' class='form-control'>
                    </div>
                    <div class='col-12'>
                        <small>Seed Stage Duration</small>
                    </div>
                    <div class='col-12'>
                        <input type='number' name='seed_time' value='{$r['seed_time']}' class='form-control'>
                    </div>
                    <div class='col-12'>
                        <small>Seed Safe Time</small>
                    </div>
                    <div class='col-12'>
                        <input type='number' name='seed_safe_time' value='{$r['seed_safe_time']}' class='form-control'>
                    </div>
                    <div class='col-12'>
                        <small>Seed Min. Level</small>
                    </div>
                    <div class='col-12'>
                        <input type='number' name='seed_lvl_requirement' value='{$r['seed_lvl_requirement']}' class='form-control'>
                    </div>
                    <div class='col-12'>
                        <small>Seed Min. Wellness</small>
                    </div>
                    <div class='col-12'>
                        <input type='number' name='seed_wellness_plant' value='{$r['seed_wellness_plant']}' class='form-control'>
                    </div>
                    <div class='col-12'>
                        <small>Seed Rotten</small>
                    </div>
                    <div class='col-12'>
                        <input type='number' name='seed_wellness_bad' value='{$r['seed_wellness_bad']}' class='form-control'>
                    </div>
                    <div class='col-12'>
                        <small>Seed XP</small>
                    </div>
                    <div class='col-12'>
                        <input type='number' name='seed_xp' value='{$r['seed_xp']}' class='form-control'>
                    </div>
                    <div class='col-12'>
                        <input type='submit' value='Edit Seed' class='btn btn-primary btn-block'>
                    </div>
                </div>
            </div>
        </div>
        {$csrf}
        <input type='hidden' value='2' name='step'>
        <input type='hidden' value='{$r['seed_item']}' name='seed'>
        </form>";
    }
    elseif ($_POST['step'] == 2)
    {
        if (!isset($_POST['verf']) || !verify_csrf_code('staff_editseed2', stripslashes($_POST['verf'])))
        {
            alert('danger', "Action Blocked!", "Forms expire fairly quickly after opening them. Go back and submit the form quicker!");
            die($h->endpage());
        }
        $seed = (isset($_POST['seed']) && is_numeric($_POST['seed'])) ? abs(intval($_POST['seed'])) : 0;
        $output = (isset($_POST['output']) && is_numeric($_POST['output'])) ? abs(intval($_POST['output'])) : 0;
        $qty = (isset($_POST['seed_qty']) && is_numeric($_POST['seed_qty'])) ? abs(intval($_POST['seed_qty'])) : 0;
        $stages = (isset($_POST['seed_stages']) && is_numeric($_POST['seed_stages'])) ? abs(intval($_POST['seed_stages'])) : 0;
        $time = (isset($_POST['seed_time']) && is_numeric($_POST['seed_time'])) ? abs(intval($_POST['seed_time'])) : 0;
        $safe_time = (isset($_POST['seed_safe_time']) && is_numeric($_POST['seed_safe_time'])) ? abs(intval($_POST['seed_safe_time'])) : 0;
        $lvl_requirement = (isset($_POST['seed_lvl_requirement']) && is_numeric($_POST['seed_lvl_requirement'])) ? abs(intval($_POST['seed_lvl_requirement'])) : 0;
        $wellness_plant = (isset($_POST['seed_wellness_plant']) && is_numeric($_POST['seed_wellness_plant'])) ? abs(intval($_POST['seed_wellness_plant'])) : 0;
        $wellness_bad = (isset($_POST['seed_wellness_bad']) && is_numeric($_POST['seed_wellness_bad'])) ? abs(intval($_POST['seed_wellness_bad'])) : 0;
        $xp = (isset($_POST['seed_xp']) && is_numeric($_POST['seed_xp'])) ? abs(intval($_POST['seed_xp'])) : 0;
        
        if (empty($seed))
        {
            alert('danger', "Uh Oh!", "Please make sure the form isn't broken before you submit it.");
            die($h->endpage());
        }
        if (empty($output))
        {
            alert('danger', "Uh Oh!", "You specified an invalid crop output item.");
            die($h->endpage());
        }
        if (empty($qty))
        {
            alert('danger', "Uh Oh!", "You have specified an invalid crop output quantity.");
            die($h->endpage());
        }
        if (empty($stages))
        {
            alert('danger', "Uh Oh!", "You have specified an invalid amount of stages.");
            die($h->endpage());
        }
        if (empty($time))
        {
            alert('danger', "Uh Oh!", "You have specified an invalid amount of seed stage time.");
            die($h->endpage());
        }
        if (empty($safe_time))
        {
            alert('danger', "Uh Oh!", "You have specified an invalid safe time for seed's finishing their stage.");
            die($h->endpage());
        }
        if (empty($lvl_requirement))
        {
            alert('danger', "Uh Oh!", "You have specified an invalid level requirement.");
            die($h->endpage());
        }
        if (empty($wellness_bad))
        {
            alert('danger', "Uh Oh!", "You have specified an invalid crop rotten level.");
            die($h->endpage());
        }
        if (empty($wellness_plant))
        {
            alert('danger', "Uh Oh!", "You have input an invalid minimum wellness plant requirement for this seed.");
            die($h->endpage());
        }
        if (empty($xp))
        {
            alert('danger', "Uh Oh!", "Invalid XP per seed specified.");
            die($h->endpage());
        }
        $q = $db->query("SELECT * FROM `farm_produce` WHERE `seed_item` = {$seed}");
        if ($db->num_rows($q) == 0)
        {
            alert('danger', "Uh Oh!", "The seed you selected is not set up as a valid seed.");
            die($h->endpage());
        }
        if (!$api->SystemItemIDtoName($output))
        {
            alert('danger', "Uh Oh!", "The item you've selected as a crop output does not exist.");
            die($h->endpage());
        }
        elseif (($qty < 1) || ($qty > 1024))
        {
            alert("danger","Uh Oh!","Crop output must be at least 1, and at most, 1024.");
        }
        elseif (($stages < 1) || ($stages > 1024))
        {
            alert("danger","Uh Oh!","Crop stage count must be at least 1, and at most, 1024.");
        }
        elseif ($time < 60)
        {
            alert("danger","Uh Oh!","Crop stage count must be at least 60 seconds.");
        }
        elseif ($safe_time < 60)
        {
            alert("danger","Uh Oh!","Crop stage count must be at least 60 seconds.");
        }
        elseif ($lvl_requirement < 1)
        {
            alert("danger","Uh Oh!","Seed minimum level must be at least 1.");
        }
        elseif ($wellness_bad < 0)
        {
            alert("danger","Uh Oh!","Seed wellness rotten percent must be at least 0.");
        }
        elseif ($wellness_plant < 0)
        {
            alert("danger","Uh Oh!","Seed wellness plant percent must be at least 0.");
        }
        elseif ($xp < 1)
        {
            alert("danger","Uh Oh!","Seed experience gain must be at least 1.");
        }
        $db->query("UPDATE `farm_produce` SET
                    `seed_time` = {$time},
                    `seed_safe_time` = {$safe_time},
                    `seed_stages` = {$stages},
                    `seed_output` = {$output},
                    `seed_qty` = {$qty},
                    `seed_lvl_requirement` = {$lvl_requirement},
                    `seed_wellness_plant` = {$wellness_plant},
                    `seed_wellness_bad` = {$wellness_bad},
                    `seed_xp` = {$xp}
                    WHERE `seed_item` = {$seed}");
        alert('success',"Success!","You have successfully edited the {$api->SystemItemIDtoName($seed)} seed.", true, 'farm.php');
        die($h->endpage());
    }
    elseif ($_POST['step'] == 0)
    {
        $csrf = request_csrf_html('staff_editseed1');
        echo "<div class='card'>
                <div class='card-body'>
                    Select the seed from the dropdown you wish to edit.
                    <form method='post'>
                        <input type='hidden' name='step' value='1'>
                        " . seed_dropdown() . "<br />
                        <input type='submit' class='btn btn-primary btn-block' value='Edit Seed'>
                        {$csrf}
                    </form>
                </div>
            </div>";
    }
    else
    {
        alert('danger',"Uh Oh!","Invalid step specified.", true, '?action=editseed');
        die($h->endpage());
    }
}