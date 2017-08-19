<?php
/*
	File:		gym.php
	Created: 	4/5/2016 at 12:07AM Eastern Time
	Info: 		Allows players to train their stats at the cost of
				will and energy. Players can replenish their energy
				at the Secondary Curreny Temple, and will can be
				increased by buying new estates.
	Author:		TheMasterGeneral
	Website: 	https://github.com/MasterGeneral156/chivalry-engine
*/
$macropage=('gym.php');
require("globals.php");
//User is in the infirmary
if ($api->UserStatus($ir['userid'],'infirmary'))
{
	alert("danger",$lang["GEN_INFIRM"],$lang['GYM_INFIRM'],true,'index.php');
	die($h->endpage());
}
//User is in the dungeon.
if ($api->UserStatus($ir['userid'],'dungeon'))
{
	alert("danger",$lang["GEN_DUNG"],$lang['GYM_DUNG']);
	die($h->endpage());
}
//Convert POST values to Stat Names.
$statnames =  array("Strength" => "strength", "Agility" => "agility", "Guard" => "guard", "Labor" => "labor", "All" => "all");
//Training amount is not set, so set to 0.
if (!isset($_POST["amnt"]))
{
    $_POST["amnt"] = 0;
}
$_POST["amnt"] = abs($_POST["amnt"]);
echo "<h3>{$lang['GYM_INFO']}</h3>";
if (isset($_POST["stat"]) && $_POST["amnt"])
{
    //User trained stat does not exist.
	if (!isset($statnames[$_POST['stat']]))
    {
		alert("danger",$lang['ERROR_INVALID'],$lang['GYM_INVALIDSTAT'],true,'back');
		die($h->endpage());
	}
    //User fails CSRF check.
	if (!isset($_POST['verf']) || !verify_csrf_code('gym_train', stripslashes($_POST['verf'])))
	{
		alert('danger',$lang["CSRF_ERROR_TITLE"],$lang["CSRF_ERROR_TEXT"],true,'index.php');
		die($h->endpage());
	}
	$stat = $statnames[$_POST['stat']];
    //User is trying to train using more energy than they have.
    if ($_POST['amnt'] > $ir['energy'])
    {
        alert("danger",$lang['GYM_NEG'],$lang['GYM_NEG_DETAIL'],false);
    }
	else
	{
		$gain = 0;
		$extraecho='';
		if ($stat == 'all')
		{
			$gainstr=$api->UserTrain($userid,'strength',$_POST['amnt']/4);
			$gainagl=$api->UserTrain($userid,'agility',$_POST['amnt']/4);
			$gaingrd=$api->UserTrain($userid,'guard',$_POST['amnt']/4);
			$gainlab=$api->UserTrain($userid,'labor',$_POST['amnt']/4);
		}
		else
		{
			$gain=$api->UserTrain($userid,$_POST['stat'],$_POST['amnt']);
		}
        //Update energy left and stat's new count.
		if ($stat != 'all')
			$NewStatAmount = $ir[$stat] + $gain;
        $EnergyLeft = $ir['energy'] - $_POST['amnt'];
        //Strength is chosen stat
		if ($stat == "strength")
        {
			alert('success',$lang['ERROR_SUCCESS'],"{$lang['GYM_STR']} {$gain} {$lang['GEN_STR']} {$lang['GYM_STR1']}
			    {$_POST['amnt']} {$lang['GYM_STR2']} {$NewStatAmount} {$lang['GEN_STR']} {$lang['GEN_AND']}
			    {$EnergyLeft} {$lang['GYM_STR3']}", false);
			//Have strength selected for the next training.
            $str_select="selected";
        }
        //Agility is the chosen stat.
        elseif ($stat == "agility")
        {
			alert('success',$lang['ERROR_SUCCESS'],"{$lang['GYM_AGL']} {$gain} {$lang['GEN_AGL']} {$lang['GYM_STR1']}
			    {$_POST['amnt']} {$lang['GYM_AGL1']} {$lang['GYM_YNH']} {$NewStatAmount} {$lang['GEN_AGL']}
			    {$lang['GEN_AND']} {$EnergyLeft} {$lang['GYM_STR3']}", false);
			//Have agility selected for the next training.
            $agl_select="selected";
        }
        //Guard is the chosen stat.
        elseif ($stat == "guard")
        {
			alert('success',$lang['ERROR_SUCCESS'],"{$lang['GYM_GRD']} {$gain} {$lang['GEN_GRD']} {$lang['GYM_STR1']}
			    {$_POST['amnt']} {$lang['GYM_GRD1']} {$lang['GYM_YNH']} {$NewStatAmount} {$lang['GEN_GRD']}
			    {$lang['GEN_AND']} {$EnergyLeft} {$lang['GYM_STR3']}", false);
			//Have guard selected for the next training.
            $grd_select="selected";
        }
        //Labor is the chosen stat.
        elseif ($stat == "labor")
        {
            alert('success',$lang['ERROR_SUCCESS'],"{$lang['GYM_LAB']} {$gain} {$lang['GEN_LAB']} {$lang['GYM_STR1']}
                {$_POST['amnt']} {$lang['GYM_LAB1']} {$lang['GYM_YNH']} {$NewStatAmount} {$lang['GEN_LAB']}
                {$lang['GEN_AND']} {$EnergyLeft} {$lang['GYM_STR3']}", false);
			//Have guard selected for the next training.
            $lab_select="selected";
        }
		elseif ($stat == "all")
		{
            alert('success',$lang['ERROR_SUCCESS'],"{$lang['GYM_ALL_BEGIN']} {$gainstr} {$lang['GYM_ALL_BEGIN1']}
                {$gainagl} {$lang['GYM_ALL_BEGIN2']} {$gaingrd} {$lang['GYM_ALL_BEGIN3']} {$gainlab}
                {$lang['GYM_ALL_BEGIN4']} {$EnergyLeft} {$lang['GYM_STR3']}");
			$all_select="selected";
		}
        //Log the user's training attempt.
		$api->SystemLogsAdd($userid,'training',"Trained their {$stat} {$_POST['amnt']} times and gained {$gain}.");
        echo "<hr />";
        $ir['energy'] -= $_POST['amnt'];
		if ($stat != 'all')
			$ir[$stat] += $gain;
	}
}
//Small logic to keep the last trained stat selected.
if (!isset($str_select))
{
	$str_select='';
}
if (!isset($agl_select))
{
	$agl_select='';
}
if (!isset($grd_select))
{
	$grd_select='';
}
if (!isset($lab_select))
{
	$lab_select='';
}
if (!isset($all_select))
{
	$all_select='';
}
//Grab the user's stat ranks.
$ir['strank'] = get_rank($ir['strength'], 'strength');
$ir['agirank'] = get_rank($ir['agility'], 'agility');
$ir['guarank'] = get_rank($ir['guard'], 'guard');
$ir['labrank'] = get_rank($ir['labor'], 'labor');
$ir['all_four'] = ($ir['labor']+$ir['strength']+$ir['agility']+$ir['guard']);
$ir['af_rank'] = get_rank($ir['all_four'], 'all');
//Request CSRF code.
$code = request_csrf_html('gym_train');
echo "{$lang['GYM_FRM1']} {$ir['energy']} {$lang['GYM_FRM2']}<hr />
<table class='table table-bordered'>
	<tr>
		<form action='gym.php' method='post'>
			<th>
			    {$lang['GYM_TH']}
            </th>
			<td>
				<select type='dropdown' name='stat' class='form-control'>
					<option {$str_select} value='Strength'>
					    {$lang['GEN_STR']} ({$lang['GEN_HAVE']} {$ir['strength']}, {$lang['GEN_RANK']} {$ir['strank']})
                    </option>
					<option {$agl_select} value='Agility'>
					    {$lang['GEN_AGL']} ({$lang['GEN_HAVE']} {$ir['agility']}, {$lang['GEN_RANK']} {$ir['agirank']})
                    </option>
					<option {$grd_select} value='Guard'>
					    {$lang['GEN_GRD']} ({$lang['GEN_HAVE']} {$ir['guard']}, {$lang['GEN_RANK']} {$ir['guarank']})
                    </option>
					<option {$lab_select} value='Labor'>
					    {$lang['GEN_LAB']} ({$lang['GEN_HAVE']} {$ir['labor']}, {$lang['GEN_RANK']} {$ir['labrank']})
                    </option>
					<option {$all_select} value='All'>
					    {$lang['GYM_ALL']} ({$lang['GEN_HAVE']} {$ir['all_four']}, {$lang['GEN_RANK']} {$ir['af_rank']})
                    </option>
				</select>
			</td>
	</tr>
	<tr>
		<th>
		    {$lang['GYM_TH1']}
        </th>
		<td>
		    <input type='number' class='form-control' min='1' max='{$ir['energy']}' name='amnt' value='{$ir['energy']}' />
        </td>
	</tr>
	<tr>
		<td colspan='2'>
		    <input type='submit' class='btn btn-primary' value='{$lang['GYM_BTN']}' />
        </td>
	</tr>
	    {$code}
	    </form>
</table>";
$h->endpage();