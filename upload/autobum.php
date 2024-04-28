<?php
$macropage = ('autobum.php');
require('globals.php');
if ($ir['autobum'] == 0)
{
	alert('danger',"Uh Oh!","You need to have an Auto Street Begger redeemed on your account to use this feature.",true,'explore.php');
	die($h->endpage());
}
if ($ir['searchtown'] == 0)
{
    alert('danger',"Uh Oh!","You've begged all you can for now. Try again at the top of the hour.",true,'explore.php');
    die($h->endpage());
}
if ($api->UserStatus($userid, 'infirmary')) 
{
    alert('danger', "Unconscious!", "You cannot go begging if you're in the infirmary.");
    die($h->endpage());
}
if ($api->UserStatus($userid, 'dungeon')) 
{
    alert('danger', "Locked Up!", "You cannot go begging if you're in the dungeon.");
    die($h->endpage());
}
if (isset($_POST['open']))
{
	$_POST['open'] = abs($_POST['open']);
	if (empty($_POST['open']))
	{
		alert('danger',"Uh Oh!","Please specify how many Hexbags you would like to open using this system.");
		die($h->endpage());
	}
	if ($_POST['open'] > $ir['searchtown'])
	{
		alert('danger',"Uh Oh!","You cannot beg that many times at this moment.");
		die($h->endpage());
	}
	if ($_POST['open'] > $ir['autobum'])
	{
		alert('danger',"Uh Oh!","You do not have enough auto beggings available right now.");
		die($h->endpage());
	}
	$number=0;
	$copper=0;
	$tokens=0;
	$fish=0;
	$apple=0;
	$choco=0;
	$ham=0;
	$stick=0;
	$rock=0;
	$log=0;
	$bucket=0;
	while($number < $_POST['open'])
	{
		$number=$number+1;
		$chance=Random(1,20);
		if ($chance <= 5)
		{
			$copper = $copper + Random(200, 950);
		}
		elseif (($chance <= 8) && ($chance > 6))
		{
		    $min = 10 + (10 * (getUserSkill($userid, 10) * getSkillBonus(10)) / 100);
		    $max = 24 + (24 * (getUserSkill($userid, 10) * getSkillBonus(10)) / 100);
			$tokens = $tokens + Random($min, $max);
		}
		elseif ($chance == 9)
		{
			$fish++;
		}
		elseif ($chance == 10)
		{
			$apple++;
		}
		elseif ($chance == 11)
		{
			$ham++;
		}
		elseif ($chance == 12)
		{
			$choco++;
		}
		elseif ($chance == 13)
		{
			$rock++;
		}
		elseif ($chance == 14)
		{
			$stick++;
		}
		elseif ($chance == 15)
		{
		    $bucket++;
		}
		elseif ($chance == 16)
		{
		    $log = $log + 2;
		}
	}
	$db->query("UPDATE `user_settings` SET `autobum` = `autobum` - {$_POST['open']}, `searchtown` = `searchtown` - {$_POST['open']} WHERE `userid` = {$userid}");
	echo "<div class='card'>
            <div class='card-header'>
                After " . shortNumberParse($number) . " Street beg attempts...
            </div>
            <div class='card-body'>
                <i>You've gained the following...</i>
                <div class='row'>
                    <div class='col-auto'>
                        " . shortNumberParse($copper) . " Copper Coins
                    </div>
                    <div class='col-auto'>
                        " . shortNumberParse($tokens) . " Chivalry Tokens
                    </div>
                    <div class='col-auto'>
                        " . shortNumberParse($fish) . " Fish
                    </div>
                    <div class='col-auto'>
                        " . shortNumberParse($apple) . " Apples
                    </div>
                    <div class='col-auto'>
                        " . shortNumberParse($ham) . " Ham Shanks
                    </div>
                    <div class='col-auto'>
                        " . shortNumberParse($choco) . " Chocolate Bars
                    </div>
                    <div class='col-auto'>
                        " . shortNumberParse($rock) . " Heavy Rocks
                    </div>
                    <div class='col-auto'>
                        " . shortNumberParse($bucket) . " Empty Buckets
                    </div>
                    <div class='col-auto'>
                        " . shortNumberParse($log) . " Wood Logs
                    </div>
                </div>
            </div>
    </div>";
	
	//Give rewards
	$api->UserGiveCurrency($userid,'primary',$copper);
	$api->UserGiveCurrency($userid,'secondary',$tokens);
	$api->UserGiveItem($userid,107,$fish);
	$api->UserGiveItem($userid,111,$apple);
	$api->UserGiveItem($userid,109,$ham);
	$api->UserGiveItem($userid,139,$choco);
	$api->UserGiveItem($userid,2,$rock);
	$api->UserGiveItem($userid,1,$stick);
	$api->UserGiveItem($userid, 295, $bucket);
	$api->UserGiveItem($userid, 410, $log);
	
	//Eco logs
	addToEconomyLog('Begging', 'copper', $copper);
	addToEconomyLog('Begging', 'token', $tokens);
	//Logs
	$api->SystemLogsAdd($userid,"begging","Received " . shortNumberParse($copper) . " Copper Coins.");
	$api->SystemLogsAdd($userid,"begging","Received " . shortNumberParse($tokens) . " Chivalry Tokens.");
	$api->SystemLogsAdd($userid,"begging","Received " . shortNumberParse($fish) . " Fish.");
	$api->SystemLogsAdd($userid,"begging","Received " . shortNumberParse($apple) . " Apple(s).");
	$api->SystemLogsAdd($userid,"begging","Received " . shortNumberParse($ham) . " Ham Shank(s).");
	$api->SystemLogsAdd($userid,"begging","Received " . shortNumberParse($choco) . " Chocolate Bar(s).");
	$api->SystemLogsAdd($userid,"begging","Received " . shortNumberParse($rock) . " Heavy Rocks(s).");
	$api->SystemLogsAdd($userid,"begging","Received " . shortNumberParse($stick) . " Sharpened Stick(s).");
	$api->SystemLogsAdd($userid,"begging","Received " . shortNumberParse($bucket) . " Empty Bucket(s).");
	$api->SystemLogsAdd($userid,"begging","Received " . shortNumberParse($log) . " Wood Log(s).");
}
else
{
	$maxnumber = ($ir['autobum'] > $ir['searchtown']) ? $ir['searchtown'] : $ir['autobum'] ;
	echo "<div class='card'>
            <div class='card-header'>
                Auto Street Begger (" . shortNumberParse($ir['autobum']) . " remaining)
            </div>
            <div class='card-body'>
                How many attempts do you wish to automatically street beg? You may beg up to a maximum of 
                " . shortNumberParse($maxnumber) . " at this time.
                <form method='post'>
                <div class='row'>
                    <div class='col-12 col-sm'>
                        <input type='number' min='1' max='{$maxnumber}' name='open' class='form-control' required='1' value='{$maxnumber}'>
                    </div>
                    <div class='col-12 col-sm'>
                        <input type='submit' value='Start Begging' class='btn btn-primary btn-block'>
                    </div>
                </div>
                </form>
            </div>
    </div>";
}
$h->endpage();