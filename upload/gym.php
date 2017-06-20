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
if ($api->UserStatus($ir['userid'],'infirmary') == true)
{
	alert("danger",$lang["GEN_INFIRM"],$lang['GYM_INFIRM'],true,'index.php');
	die($h->endpage());
}
if ($api->UserStatus($ir['userid'],'dungeon') == true)
{
	alert("danger",$lang["GEN_DUNG"],$lang['GYM_DUNG']);
	die($h->endpage());
}
$statnames =  array("Strength" => "strength", "Agility" => "agility", "Guard" => "guard", "Labor" => "labor");
if (!isset($_POST["amnt"]))
{
    $_POST["amnt"] = 0;
}
$_POST["amnt"] = abs($_POST["amnt"]);
echo "<h3>{$lang['GYM_INFO']}</h3>";
if (isset($_POST["stat"]) && $_POST["amnt"])
{
	if (!isset($statnames[$_POST['stat']]))
    {
		alert("danger",$lang['ERROR_INVALID'],$lang['GYM_INVALIDSTAT'],true,'back');
		die($h->endpage());
	}
	if (!isset($_POST['verf']) || !verify_csrf_code('gym_train', stripslashes($_POST['verf'])))
	{
		alert('danger',$lang["CSRF_ERROR_TITLE"],$lang["CSRF_ERROR_TEXT"],true,'index.php');
		die($h->endpage());
	}
	$stat = $statnames[$_POST['stat']];
    if ($_POST['amnt'] > $ir['energy'])
    {
        alert("danger",$lang['GYM_NEG'],$lang['GYM_NEG_DETAIL'],false);
    }
	else
	{
		$gain = 0;
		$extraecho='';
        for ($i = 0; $i < $_POST['amnt']; $i++)
        {
            $gain +=
                    Random(1, 4) / Random(600, 1000) * Random(500, 1000) * (($ir['will'] + 25) / 175);
            $ir['will'] -= Random(1, 3);
            if ($ir['will'] < 0)
            {
                $ir['will'] = 0;
            }
        }
		if ($ir['class'] == 'Warrior')
		{
			if ($stat == 'strength')
			{
				$gain *= 2;
			}
			if ($stat == 'guard')
			{
				$gain /= 2;
			}
		}
		if ($ir['class'] == 'Rogue')
		{
			if ($stat == 'agility')
			{
				$gain *= 2;
			}
			if ($stat == 'strength')
			{
				$gain /= 2;
			}
		}
		if ($ir['class'] == 'Defender')
		{
			if ($stat == 'guard')
			{
				$gain *= 2;
			}
			if ($stat == 'agility')
			{
				$gain /= 2;
			}
		}
		$gain=floor($gain);
		$db->query(
                "UPDATE `userstats`
        		 SET `{$stat}` = `{$stat}` + $gain
        		 WHERE `userid` = $userid");
        $db->query(
                "UPDATE `users`
                 SET `will` = {$ir['will']},
                 `energy` = `energy` - {$_POST['amnt']}
                 WHERE `userid` = $userid");
		$NewStatAmount = $ir[$stat] + $gain;
        $EnergyLeft = $ir['energy'] - $_POST['amnt'];
		if ($stat == "strength")
        {
			alert('success',$lang['ERROR_SUCCESS'],"{$lang['GYM_STR']} {$gain} {$lang['GEN_STR']} {$lang['GYM_STR1']} {$_POST['amnt']} {$lang['GYM_STR2']} {$NewStatAmount} {$lang['GEN_STR']} {$lang['GEN_AND']} {$EnergyLeft} {$lang['GYM_STR3']}", false);
			$str_select="selected";
        }
        elseif ($stat == "agility")
        {
			alert('success',$lang['ERROR_SUCCESS'],"{$lang['GYM_AGL']} {$gain} {$lang['GEN_AGL']} {$lang['GYM_STR1']} {$_POST['amnt']} {$lang['GYM_AGL1']} {$lang['GYM_YNH']} {$NewStatAmount} {$lang['GEN_AGL']} {$lang['GEN_AND']} {$EnergyLeft} {$lang['GYM_STR3']}", false);
			$agl_select="selected";
        }
        elseif ($stat == "guard")
        {
			alert('success',$lang['ERROR_SUCCESS'],"{$lang['GYM_GRD']} {$gain} {$lang['GEN_GRD']} {$lang['GYM_STR1']} {$_POST['amnt']} {$lang['GYM_GRD1']} {$lang['GYM_YNH']} {$NewStatAmount} {$lang['GEN_GRD']} {$lang['GEN_AND']} {$EnergyLeft} {$lang['GYM_STR3']}", false);
			$grd_select="selected";
        }
        elseif ($stat == "labor")
        {
            alert('success',$lang['ERROR_SUCCESS'],"{$lang['GYM_LAB']} {$gain} {$lang['GEN_LAB']} {$lang['GYM_STR1']} {$_POST['amnt']} {$lang['GYM_LAB1']} {$lang['GYM_YNH']} {$NewStatAmount} {$lang['GEN_LAB']} {$lang['GEN_AND']} {$EnergyLeft} {$lang['GYM_STR3']}", false);
			$lab_select="selected";
        }
		$api->SystemLogsAdd($userid,'training',"Trained their {$stat} {$_POST['amnt']} times and gained {$gain}.");
        echo "<hr />";
        $ir['energy'] -= $_POST['amnt'];
        $ir[$stat] += $gain;
	}
}
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
$ir['strank'] = get_rank($ir['strength'], 'strength');
$ir['agirank'] = get_rank($ir['agility'], 'agility');
$ir['guarank'] = get_rank($ir['guard'], 'guard');
$ir['labrank'] = get_rank($ir['labor'], 'labor');
$code = request_csrf_html('gym_train');
echo "{$lang['GYM_FRM1']} {$ir['energy']} {$lang['GYM_FRM2']}<hr />
<table class='table table-bordered'>
	<tr>
		<form action='gym.php' method='post'>
			<th>{$lang['GYM_TH']}</th>
			<td><select type='dropdown' name='stat' class='form-control'>
<option {$str_select} value='Strength'>{$lang['GEN_STR']} ({$lang['GEN_HAVE']} {$ir['strength']}, {$lang['GEN_RANK']} {$ir['strank']})
<option {$agl_select} value='Agility'>{$lang['GEN_AGL']} ({$lang['GEN_HAVE']} {$ir['agility']}, {$lang['GEN_RANK']} {$ir['agirank']})
<option {$grd_select} value='Guard'>{$lang['GEN_GRD']} ({$lang['GEN_HAVE']} {$ir['guard']}, {$lang['GEN_RANK']} {$ir['guarank']})
<option {$lab_select} value='Labor'>{$lang['GEN_LAB']} ({$lang['GEN_HAVE']} {$ir['labor']}, {$lang['GEN_RANK']} {$ir['labrank']})
</select></td>
	</tr>
	<tr>
		<th>{$lang['GYM_TH1']}</th>
		<td><input type='number' class='form-control' min='1' max='{$ir['energy']}' name='amnt' value='{$ir['energy']}' /></td>
	</tr>
	<tr>
		<td colspan='2'><input type='submit' class='btn btn-secondary' value='{$lang['GYM_BTN']}' /></td>
	</tr>
	{$code}
	</form>
</table>";
$h->endpage();