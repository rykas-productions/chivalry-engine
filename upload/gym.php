<?php
$marcopage = "gym.php";
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
$statnames =  array("Strength" => "strength", "Agility" => "agility", "Guard" => "guard", "Labour" => "labour");
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
                    mt_rand(1, 4) / mt_rand(600, 1000) * mt_rand(500, 1000)
                            * (($ir['will'] + 25) / 175);
            $ir['will'] -= mt_rand(1, 3);
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
            echo "You begin lifting some weights.<br />
      You have gained {$gain} strength by doing {$_POST['amnt']} sets of weights.<br />
      You now have {$NewStatAmount} strength and {$EnergyLeft} energy left.";
        }
        elseif ($stat == "agility")
        {
            echo "You begin running on a treadmill.<br />
      You have gained {$gain} agility by doing {$_POST['amnt']} minutes of running.<br />
      You now have {$NewStatAmount} agility and {$EnergyLeft} energy left.";
        }
        elseif ($stat == "guard")
        {
            echo "You jump into the pool and begin swimming.<br />
      You have gained {$gain} guard by doing {$_POST['amnt']} minutes of swimming.<br />
      You now have {$NewStatAmount} guard and {$EnergyLeft} energy left.";
        }
        elseif ($stat == "labor")
        {
            echo "You walk over to some boxes filled with gym equipment and start moving them.<br />
      You have gained {$gain} labour by moving {$_POST['amnt']} boxes.<br />
      You now have {$NewStatAmount} labour and {$EnergyLeft} energy left.";
        }
		traininglog_add($userid,$stat,$gain);
        echo "<hr />";
        $ir['energy'] -= $_POST['amnt'];
        $ir[$stat] += $gain;
	}
}
$ir['strank'] = get_rank($ir['strength'], 'strength');
$ir['agirank'] = get_rank($ir['agility'], 'agility');
$ir['guarank'] = get_rank($ir['guard'], 'guard');
$ir['labrank'] = get_rank($ir['labor'], 'labor');
echo "Choose the stat you want to train and the times you want to train it.<br />
You can train up to {$ir['energy']} times.<hr />
<table class='table table-bordered'>
	<tr>
		<form action='gym.php' method='post'>
			<th>Stat to Train</th>
			<td><select type='dropdown' name='stat' class='form-control'>
<option value='Strength'>Strength (Have {$ir['strength']}, Ranked {$ir['strank']})
<option value='Agility'>Agility (Have {$ir['agility']}, Ranked {$ir['agirank']})
<option value='Guard'>Guard (Have {$ir['guard']}, Ranked {$ir['guarank']})
<option value='Labor'>Labor (Have {$ir['labor']}, Ranked {$ir['labrank']})
</select></td>
	</tr>
	<tr>
		<th>Training Duration?</th>
		<td><input type='number' class='form-control' min='1' max='{$ir['energy']}' name='amnt' value='{$ir['energy']}' /></td>
	</tr>
	<tr>
		<td colspan='2'><input type='submit' class='btn btn-default' value='Train' /></td>
	</tr>
	</form>
</table>";
$h->endpage();