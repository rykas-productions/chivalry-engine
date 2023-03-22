<?php
require('globals.php');
if ($api->UserStatus($userid,'dungeon') || $api->UserStatus($userid,'infirmary'))
{
	alert('danger',"Uh Oh!","You cannot visit the Mission Center while in the dungeon or infirmary.",true,'explore.php');
	die($h->endpage());
}
$am=$db->query("SELECT * FROM `missions` WHERE `mission_userid` = {$userid}");
if ($db->num_rows($am) == 0)
{
	if (isset($_GET['accept']))
	{
		$days=Random(2, 7);   //upped from 3
		$kills=(Random(5,15)+Random($ir['level']/4,$ir['level']/2))*$days;
		$reward = 0;
		$loops = 0;
		while ($loops != $kills)
		{
		    $random = Random(4000, 20000);
		    $reward = $reward + round($random + ($random * levelMultiplier($ir['level'], $ir['reset'])));
		    $loops++;
		}
		$endtime=time()+($days*86400);    //create end timestamp in seconds
		$db->query("INSERT INTO `missions` 
		(`mission_userid`, `mission_kills`, 
		`mission_end`, `mission_kill_count`, `mission_reward`) 
		VALUES ('{$userid}', '{$kills}', '{$endtime}', '0', '{$reward}')");
		//tablet coding is pita but doable...
		echo "<div class='card'>
                <div class='card-header'>
                    Current Mission
                </div>
                <div class='card-body'>
                    <div class='row'>
                        <div class='col-12 col-sm-6 col-lg'>
                            <div class='row'>
                                 <div class='col-12'>
                                    <small>Kills Requiresd</small>
                                </div>
                                <div class='col-12'>
                                    " . shortNumberParse($kills) . "
                                </div>
                            </div>
                        </div>
                        <div class='col-12 col-sm-6 col-lg'>
                            <div class='row'>
                                 <div class='col-12'>
                                    <small>Kill Count</small>
                                </div>
                                <div class='col-12'>
                                    " . shortNumberParse(0) . "
                                </div>
                            </div>
                        </div>
                        <div class='col-12 col-sm-6 col-lg'>
                            <div class='row'>
                                 <div class='col-12'>
                                    <small>Reward</small>
                                </div>
                                <div class='col-12'>
                                    " . shortNumberParse($reward) . " Copper Coins
                                </div>
                            </div>
                        </div>
                        <div class='col-12 col-sm-6 col-lg'>
                            <div class='row'>
                                 <div class='col-12'>
                                    <small>Mission Duration</small>
                                </div>
                                <div class='col-12'>
                                    " . TimeUntil_Parse($endtime) . "
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>";
	}
	else
	{
	    echo "<div class='card'>
                <div class='card-header'>
                    Mission Center
                </div>
                <div class='card-body'>
                    <div class='row'>
                        <div class='col-12'>
                            You may accept a mission here. Missions will involve killing a number of players in battle in a specified 
                            time frame. If you succeeed, you will be rewarded nicely.
                        </div>
                        <div class='col-6'>
                            <a href='?accept' class='btn btn-primary btn-block'>Accept</a>
                        </div>
                        <div class='col-6'>
                            <a href='?accept' class='btn btn-danger btn-block'>Decline</a>
                        </div>
                    </div>
                </div>
            </div>";
	}
}
else
{
	$mr=$db->fetch_row($am);
	
	echo "<div class='card'>
                <div class='card-header'>
                    Current Mission
                </div>
                <div class='card-body'>
                    <div class='row'>
                        <div class='col-12 col-sm-6 col-lg'>
                            <div class='row'>
                                 <div class='col-12'>
                                    <small>Kills Required</small>
                                </div>
                                <div class='col-12'>
                                    " . shortNumberParse($mr['mission_kills']) . "
                                </div>
                            </div>
                        </div>
                        <div class='col-12 col-sm-6 col-lg'>
                            <div class='row'>
                                 <div class='col-12'>
                                    <small>Kill Count</small>
                                </div>
                                <div class='col-12'>
                                    " . shortNumberParse($mr['mission_kill_count']) . "
                                </div>
                            </div>
                        </div>
                        <div class='col-12 col-sm-6 col-lg'>
                            <div class='row'>
                                 <div class='col-12'>
                                    <small>Reward</small>
                                </div>
                                <div class='col-12'>
                                    " . shortNumberParse($mr['mission_reward']) . " Copper Coins
                                </div>
                            </div>
                        </div>
                        <div class='col-12 col-sm-6 col-lg'>
                            <div class='row'>
                                 <div class='col-12'>
                                    <small>Mission Duration</small>
                                </div>
                                <div class='col-12'>
                                    " . TimeUntil_Parse($mr['mission_end']) . "
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>";
}
$h->endpage();