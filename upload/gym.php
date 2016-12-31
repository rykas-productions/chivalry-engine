<?php
$macropage = "gym.php";
require("globals.php");
if (user_infirmary($ir['userid']) == true)
{
	alert("danger","{$lang["GEN_INFIRM"]}","{$lang['GYM_INFIRM']}");
	die($h->endpage());
}
if (user_dungeon($ir['userid']) == true)
{
	alert("danger","{$lang["GEN_DUNG"]}","{$lang['GYM_DUNG']}");
	die($h->endpage());
}
$statnames =  array("Strength" => "strength", "Agility" => "agility", "Guard" => "guard", "Labor" => "labor");
if (!isset($_POST["amnt"]))
{
    $_POST["amnt"] = 0;
}
$_POST["amnt"] = abs((int) $_POST["amnt"]);
echo "<h3>Training</h3>";
if (isset($_POST["stat"]) && $_POST["amnt"])
{
	if (!isset($statnames[$_POST['stat']]))
    {
		alert("danger","{$lang['ERROR_INVALID']}","{$lang['GYM_INVALIDSTAT']}");
		die($h->endpage());
	}
	if (!isset($_POST['verf']) || !verify_csrf_code('gym_train', stripslashes($_POST['verf'])))
	{
		alert('danger',"{$lang["CSRF_ERROR_TITLE"]}","{$lang["CSRF_ERROR_TEXT"]}");
		die($h->endpage());
	}
	$stat = $statnames[$_POST['stat']];
    if ($_POST['amnt'] > $ir['energy'])
    {
        alert("warning","{$lang['GYM_NEG']}","{$lang['GYM_NEG_DETAIL']}");
    }
	else
	{
		$gain = 0;
		$extraecho='';
        for ($i = 0; $i < $_POST['amnt']; $i++)
        {
            $gain +=
                    Random(1, 4) / Random(600, 1000) * Random(500, 1000)
                            * (($ir['will'] + 25) / 175);
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
            echo "	You begin lifting some weights.<br />
					You have gained {$gain} strength by doing {$_POST['amnt']} sets of weights.<br />
					You now have {$NewStatAmount} strength and {$EnergyLeft} energy left.";
			$str_select="selected";
        }
        elseif ($stat == "agility")
        {
			echo "	You begin running on a treadmill.<br />
					  You have gained {$gain} agility by doing {$_POST['amnt']} minutes of running.<br />
					  You now have {$NewStatAmount} agility and {$EnergyLeft} energy left.";
			$agl_select="selected";
        }
        elseif ($stat == "guard")
        {
            echo "	You jump into the pool and begin swimming.<br />
					You have gained {$gain} guard by doing {$_POST['amnt']} minutes of swimming.<br />
					You now have {$NewStatAmount} guard and {$EnergyLeft} energy left.";
			$grd_select="selected";
        }
        elseif ($stat == "labor")
        {
            echo "	You walk over to some boxes filled with gym equipment and start moving them.<br />
					You have gained {$gain} labour by moving {$_POST['amnt']} boxes.<br />
					You now have {$NewStatAmount} labour and {$EnergyLeft} energy left.";
			$lab_select="selected";
        }
		$api->SystemLogsAdd($userid,'training',"Trained their {$stat} and gained {$gain}.");
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
echo "Choose the stat you want to train and the times you want to train it.<br />
You can train up to {$ir['energy']} times.<hr />
<table class='table table-bordered'>
	<tr>
		<form action='gym.php' method='post'>
			<th>Stat to Train</th>
			<td><select type='dropdown' name='stat' class='form-control'>
<option {$str_select} value='Strength'>Strength (Have {$ir['strength']}, Ranked {$ir['strank']})
<option {$agl_select} value='Agility'>Agility (Have {$ir['agility']}, Ranked {$ir['agirank']})
<option {$grd_select} value='Guard'>Guard (Have {$ir['guard']}, Ranked {$ir['guarank']})
<option {$lab_select} value='Labor'>Labor (Have {$ir['labor']}, Ranked {$ir['labrank']})
</select></td>
	</tr>
	<tr>
		<th>Training Duration?</th>
		<td><input type='number' class='form-control' min='1' max='{$ir['energy']}' name='amnt' value='{$ir['energy']}' /></td>
	</tr>
	<tr>
		<td colspan='2'><input type='submit' class='btn btn-default' value='Train' /></td>
	</tr>
	{$code}
	</form>
</table>";
$h->endpage();