<?php
require('globals.php');
/*
	SQL to execute
	INSERT INTO `users` (`userid`, `username`, `user_level`, `email`, `password`, `level`, `xp`, `gender`, `class`, `lastip`, `loginip`, `registerip`, `laston`, `last_login`, `registertime`, `will`, `maxwill`, `hp`, `maxhp`, `energy`, `maxenergy`, `brave`, `maxbrave`, `primary_currency`, `secondary_currency`, `job`, `jobrank`, `jobwork`, `bank`, `attacking`, `vip_days`, `force_logout`, `display_pic`, `signature`, `personal_notes`, `announcements`, `equip_primary`, `equip_secondary`, `equip_armor`, `guild`, `fedjail`, `staff_notes`, `location`, `description`, `last_verified`, `need_verify`, `course`, `course_complete`, `email_optin`, `hexbags`, `bor`, `kills`, `deaths`, `busts`, `tokenbank`, `theme`, `rewarded`, `dayslogged`, `winnings_this_hour`, `protection`, `lastcrime`, `invis`, `ditem`, `iitem`, `playsound`) VALUES ('10', 'Turkey', 'NPC', 'asdg@adsgasdg.c', 'no', '1', '0', 'Male', 'Rogue', '127.0.0.1', '127.0.0.1', '127.0.0.1', '0', '0', '0', '100', '100', '500', '500', '24', '24', '10', '10', '0', '0', '0', '0', '0', '-1', '0', '', 'false', '', '', '', '', '85', '85', '86', '0', '0', '', '1', 'Gobble gobble.', '0', '0', '0', '0', '1', '50', '500', '0', '0', '0', '-1', '1', '0', '0', '0', '0', '0', '0', '1', '1', '1')
	INSERT INTO `userstats` (`userid`, `strength`, `agility`, `guard`, `iq`, `labor`) VALUES ('10', '1100', '1100', '1100', '1100', '1100')
	INSERT INTO `botlist` (`botid`, `botuser`, `botitem`, `botcooldown`) VALUES (NULL, '10', '87', '300')
	INSERT INTO `smelt_recipes` (`smelt_id`, `smelt_time`, `smelt_items`, `smelt_quantity`, `smelt_output`, `smelt_qty_output`) VALUES (NULL, '0', '88,42', '500,1', '86', '1')
	$api->GameAddAnnouncement("Greetings everyone and Happy Thanksgiving! Today we have a few nifty treats for you today.<br />Firstly, if you check Explore under the Shopping District, you will see we have Thanksgiving Trivia! Here you can answer trivia questions about Thanksgiving. Something to note here is that you will gain 1 Chivalry Gym Scroll per correct answer. You're also on a timer, so there will be a reward for the player who gets the most correct answers in the least amount of time.<br />Secondly, if you check the NPC Battle List, you will see we have a little bit of a Turkey Hunt going on. Mugging the Turkey will get you a Turkey Leg AND a handful of turkey feathers. <b>Be sure you mug the Turkey to get your rewards.</b> The turkey feathers you gain while mugging the turkey can be used to craft a set of armor, available in the Blacksmith Smeltery.<br />Finally, just for logging in today, you've received a Thanksgiving Scratch Ticket.<br />If you notice any bugs or issues today, please let me know ASAP. (Note, this is an automated announcement. I will likely not be around most of the day today. I will respond as I can.)");
	
*/
if (!isset($_GET['action'])) {
    $_GET['action'] = '';
}
switch ($_GET['action']) {
    case "ticket":
        ticket();
        break;
	case "trivia":
        trivia();
        break;
    default:
        alert('danger',"Uh Oh!","Please specify an action.",true,'index.php');
		$h->endpage();
        break;
}
function ticket()
{
	global $h,$db,$api,$userid;
	if (!$api->UserHasItem($userid,391,1))
	{
		alert('danger',"Uh Oh!","You need a 2020 Thanksgiving Scratch Ticket to be here.",true,'inventory.php');
		die($h->endpage());
	}
	if (isset($_GET['scratch']))
	{
		$rng=Random(1,6);
		if ($rng == 1)
		{
			$cash=Random(5000,100000);
			alert("success","Success!","You scratch this spot off and you win {$cash} Copper Coins. Congratulations!",true,'inventory.php');
			$api->UserGiveCurrency($userid,'primary',$cash);
		}
		elseif ($rng == 2)
		{
			$cash=Random(1000,2500);
			alert("success","Success!","You scratch this spot off and you win {$cash} Chivalry Tokens. Congratulations!",true,'inventory.php');
			$api->UserGiveCurrency($userid,'secondary',$cash);
		}
		elseif ($rng == 3)
		{
			alert("success","Success!","You scratch this spot off and you win 1 Invisibility Potion. Congratulations!",true,'inventory.php');
			$api->UserGiveItem($userid,68,1);
		}
		elseif ($rng == 4)
		{
			$cash=Random(15,50);
			alert("success","Success!","You scratch this spot off and you win {$cash} Chivalry Gym Scrolls. Congratulations!",true,'inventory.php');
			$api->UserGiveItem($userid,18,$cash);
		}
		elseif ($rng == 5)
		{
			$cash=Random(5,15);
			alert("success","Success!","You scratch this spot off and you win {$cash} Medium Explosives. Congratulations!",true,'inventory.php');
			$api->UserGiveItem($userid,61,$cash);
		}
		else
		{
			$cash=Random(2,7);
			alert("success","Success!","You scratch this spot off and you win {$cash} VIP Days. Congratulations!",true,'inventory.php');
			$db->query("UPDATE `users` SET `vip_days` = `vip_days` + {$cash} WHERE `userid` = {$userid}");
		}
		$api->UserTakeItem($userid,391,1);
	}
	else
	{
		echo "Select the spot you wish to scratch off. You shall receive rewards.<br />
		<div class='row'>
			<div class='col-sm'>
				<a href='?action=ticket&scratch=1'><img src='assets/img/turkey-thanksgiving.png' class='img-fluid'></a>
			</div>
			<div class='col-sm'>
				<a href='?action=ticket&scratch=1'><img src='assets/img/turkey-thanksgiving.png' class='img-fluid'></a>
			</div>
			<div class='col-sm'>
				<a href='?action=ticket&scratch=1'><img src='assets/img/turkey-thanksgiving.png' class='img-fluid'></a>
			</div>
			<div class='col-sm'>
				<a href='?action=ticket&scratch=1'><img src='assets/img/turkey-thanksgiving.png' class='img-fluid'></a>
			</div>
			<div class='col-sm'>
				<a href='?action=ticket&scratch=1'><img src='assets/img/turkey-thanksgiving.png' class='img-fluid'></a>
			</div>
			<div class='col-sm'>
				<a href='?action=ticket&scratch=1'><img src='assets/img/turkey-thanksgiving.png' class='img-fluid'></a>
			</div>
		</div>";
	}
	$h->endpage();
}
function trivia()
{
	global $db,$userid,$api,$h;
	$time=time();
	if (isset($_POST['1']))
	{
		$timeend=time();
		$q2=$db->query("/*qc=on*/SELECT `timeend` FROM `2020_thanksgiving_trivia` WHERE `userid` = {$userid} AND `timeend` < {$time} AND `timeend` != 0");
		if ($db->num_rows($q2) > 0)
		{
			alert('danger',"Uh Oh!","You have already submitted your answers for this year's trivia.",true,'explore.php');
			die($h->endpage());
		}
		$db->query("UPDATE `2020_thanksgiving_trivia` SET `timeend` = {$timeend} WHERE `userid` = {$userid}");
		$correct=0;
		if ($_POST['1'] == 1)
		{
			$db->query("UPDATE `2020_thanksgiving_trivia` SET `1` = 1 WHERE `userid` = {$userid}");
			$correct++;
		}
		if ($_POST['2'] == 3)
		{
			$db->query("UPDATE `2020_thanksgiving_trivia` SET `2` = 1 WHERE `userid` = {$userid}");
			$correct++;
		}
		if ($_POST['3'] == 1)
		{
			$db->query("UPDATE `2020_thanksgiving_trivia` SET `3` = 1 WHERE `userid` = {$userid}");
			$correct++;
		}
		if ($_POST['4'] == 2)
		{
			$db->query("UPDATE `2020_thanksgiving_trivia` SET `4` = 1 WHERE `userid` = {$userid}");
			$correct++;
		}
		if ($_POST['5'] == 2)
		{
			$db->query("UPDATE `2020_thanksgiving_trivia` SET `5` = 1 WHERE `userid` = {$userid}");
			$correct++;
		}
		if ($_POST['6'] == 2)
		{
			$db->query("UPDATE `2020_thanksgiving_trivia` SET `6` = 1 WHERE `userid` = {$userid}");
			$correct++;
		}
		if ($_POST['7'] == 1)
		{
			$db->query("UPDATE `2020_thanksgiving_trivia` SET `7` = 1 WHERE `userid` = {$userid}");
			$correct++;
		}
		if ($_POST['8'] == 2)
		{
			$db->query("UPDATE `2020_thanksgiving_trivia` SET `8` = 1 WHERE `userid` = {$userid}");
			$correct++;
		}
		if ($_POST['9'] == 3)
		{
			$db->query("UPDATE `2020_thanksgiving_trivia` SET `9` = 1 WHERE `userid` = {$userid}");
			$correct++;
		}
		if ($_POST['10'] == 3)
		{
			$db->query("UPDATE `2020_thanksgiving_trivia` SET `10` = 1 WHERE `userid` = {$userid}");
			$correct++;
		}
		if ($_POST['11'] == 1)
		{
			$db->query("UPDATE `2020_thanksgiving_trivia` SET `11` = 1 WHERE `userid` = {$userid}");
			$correct++;
		}
		if ($_POST['12'] == 2)
		{
			$db->query("UPDATE `2020_thanksgiving_trivia` SET `12` = 1 WHERE `userid` = {$userid}");
			$correct++;
		}
		if ($_POST['13'] == 3)
		{
			$db->query("UPDATE `2020_thanksgiving_trivia` SET `13` = 1 WHERE `userid` = {$userid}");
			$correct++;
		}
		if ($_POST['14'] == 1)
		{
			$db->query("UPDATE `2020_thanksgiving_trivia` SET `14` = 1 WHERE `userid` = {$userid}");
			$correct++;
		}
		if ($_POST['15'] == 3)
		{
			$db->query("UPDATE `2020_thanksgiving_trivia` SET `15` = 1 WHERE `userid` = {$userid}");
			$correct++;
		}
		if (isset($_POST['16']))
		{
			$db->query("UPDATE `users` SET `vip_days` = `vip_days` + 3 WHERE `userid` = {$userid}");
		}
		$correct=$correct*3;
		$api->UserGiveItem($userid,205,$correct);
		alert("success","Success!","You have successfully submitted the trivia. You got {$correct} answers correct. You have received {$correct} CID Admin Gym Access Scrolls, and 3 VIP Days. Congratulations, and Happy Thanksgiving.",true,'explore.php');
	}
	else
	{
		$q=$db->query("/*qc=on*/SELECT * FROM `2020_thanksgiving_trivia` WHERE `userid` = {$userid}");
		if ($db->num_rows($q) == 0)
		{
			$db->query("INSERT INTO `2020_thanksgiving_trivia` (`userid`, `timestart`, `timeend`, `1`, `2`, `3`, `4`, `5`, `6`, `7`, `8`, `9`, `10`, `11`, `12`, `13`, `14`, `15`) VALUES ('{$userid}', '{$time}', '0', '0', '0', '0', '0', '0', '0', '0', '0', '0', '0', '0', '0', '0', '0', '0')");
		}
		$q2=$db->query("/*qc=on*/SELECT `timeend` FROM `2020_thanksgiving_trivia` WHERE `userid` = {$userid} AND `timeend` < {$time} AND `timeend` != 0");
		if ($db->num_rows($q2) > 0)
		{
			$rf=$db->fetch_single($q2);
			echo DateTime_Parse($rf);
			alert('danger',"Uh Oh!","You have already submitted your answers for this year's trivia.",true,'explore.php');
			die($h->endpage());
		}
		echo "
		<form method='post'>
		<div class='row'>
			<div class='col-12 col-lg-6 col-xl-7'>
				<b>When does Thanksgiving occur?</b>
			</div>
			<div class='col-12 col-lg-6 col-xl-5'>
				<select name='1' id='class' class='form-control' type='dropdown'>
					<option value='1'>Fourth Thursday in November</option>
					<option value='2'>Third Thursday in November</option>
					<option value='3'>November 26th each year</option>
				</select>
			</div>
		</div>
		<br />
		<div class='row'>
			<div class='col-12 col-lg-6 col-xl-7'>
				<b>How long did the first Thankgiving last?</b>
			</div>
			<div class='col-12 col-lg-6 col-xl-5'>
				<select name='2' id='class' class='form-control' type='dropdown'>
					<option value='1'>1 day</option>
					<option value='2'>2 days</option>
					<option value='3'>3 days</option>
				</select>
			</div>
		</div>
		<br />
		<div class='row'>
			<div class='col-12 col-lg-6 col-xl-7'>
				<b>Of what is listed, which was NOT served at the Pilgrims Thanksgiving meal?</b>
			</div>
			<div class='col-12 col-lg-6 col-xl-5'>
				<select name='3' id='class' class='form-control' type='dropdown'>
					<option value='1'>Cranberries, corn, and mashed potatoes</option>
					<option value='2'>Rabbit, chicken, wild turkey, and dried fruit</option>
					<option value='3'>Venison (deer meat), fish, goose</option>
				</select>
			</div>
		</div>
		<br />
		<div class='row'>
			<div class='col-12 col-lg-6 col-xl-7'>
				<b>What was the name of the indian tribe that taught the Pilgrims to farm, and were invited to the first Thanksgiving meal?</b>
			</div>
			<div class='col-12 col-lg-6 col-xl-5'>
				<select name='4' id='class' class='form-control' type='dropdown'>
					<option value='1'>Apache</option>
					<option value='2'>Wampanoag</option>
					<option value='3'>Cherokee</option>
				</select>
			</div>
		</div>
		<br />
		<div class='row'>
			<div class='col-12 col-lg-6 col-xl-7'>
				<b>During Thanksgiving in the United States, approximately how many turkeys are eaten each year?</b>
			</div>
			<div class='col-12 col-lg-6 col-xl-5'>
				<select name='5' id='class' class='form-control' type='dropdown'>
					<option value='1'>100,000,000</option>
					<option value='2'>280,000,000</option>
					<option value='3'>500,000,000</option>
				</select>
			</div>
		</div>
		<br />
		<div class='row'>
			<div class='col-12 col-lg-6 col-xl-7'>
				<b>Which southern state was the first to adopt a Thanksgiving Day in 1855?</b>
			</div>
			<div class='col-12 col-lg-6 col-xl-5'>
				<select name='6' id='class' class='form-control' type='dropdown'>
					<option value='1'>South Carolina</option>
					<option value='2'>Virginia</option>
					<option value='3'>Georgia</option>
				</select>
			</div>
		</div>
		<br />
		<div class='row'>
			<div class='col-12 col-lg-6 col-xl-7'>
				<b>What's a snood?</b>
			</div>
			<div class='col-12 col-lg-6 col-xl-5'>
				<select name='7' id='class' class='form-control' type='dropdown'>
					<option value='1'>The loose skin under a male turkey's neck.</option>
					<option value='2'>A hat worn by a Pilgrim.</option>
					<option value='3'>A hot cider drink served at Thanksgiving.</option>
				</select>
			</div>
		</div>
		<br />
		<div class='row'>
			<div class='col-12 col-lg-6 col-xl-7'>
				<b>What eating utsensil wasn't used at the Thankgiving dinner by the Pilgrims?</b>
			</div>
			<div class='col-12 col-lg-6 col-xl-5'>
				<select name='8' id='class' class='form-control' type='dropdown'>
					<option value='1'>Knife</option>
					<option value='2'>Fork</option>
					<option value='3'>Spoon</option>
				</select>
			</div>
		</div>
		<br />
		<div class='row'>
			<div class='col-12 col-lg-6 col-xl-7'>
				<b>It's considered best practice to put the meat thermometer in where, on the turkey?</b>
			</div>
			<div class='col-12 col-lg-6 col-xl-5'>
				<select name='9' id='class' class='form-control' type='dropdown'>
					<option value='1'>The breast</option>
					<option value='2'>The middle of the back</option>
					<option value='3'>The thigh</option>
					<option value='4'>The booty</option>
				</select>
			</div>
		</div>
		<br />
		<div class='row'>
			<div class='col-12 col-lg-6 col-xl-7'>
				<b>Which president is believed to be the first to pardon a turkey and start this annual tradition?</b>
			</div>
			<div class='col-12 col-lg-6 col-xl-5'>
				<select name='10' id='class' class='form-control' type='dropdown'>
					<option value='1'>President Lincoln in 1863</option>
					<option value='2'>President Roosevelt in 1939</option>
					<option value='3'>President Harry Truman in 1947</option>
				</select>
			</div>
		</div>
		<br />
		<div class='row'>
			<div class='col-12 col-lg-6 col-xl-7'>
				<b>The Pilgrims came to the New world seeking religious freedom and were also called what?</b>
			</div>
			<div class='col-12 col-lg-6 col-xl-5'>
				<select name='11' id='class' class='form-control' type='dropdown'>
					<option value='1'>The Puritans</option>
					<option value='2'>The Great Explorers</option>
					<option value='3'>The Wanderers</option>
				</select>
			</div>
		</div>
		<br />
		<div class='row'>
			<div class='col-12 col-lg-6 col-xl-7'>
				<b>Today, our Thanksgiving is the fourth Thursday of November because why?</b>
			</div>
			<div class='col-12 col-lg-6 col-xl-5'>
				<select name='12' id='class' class='form-control' type='dropdown'>
					<option value='1'>It is the date the Pilgrims landed in the New World.</option>
					<option value='2'>This was the date set by President Franklin D. Roosevelt in 1939 and approved by Congress in 1941.</option>
					<option value='3'>It was the date people voted to have it on.</option>
				</select>
			</div>
		</div>
		<br />
		<div class='row'>
			<div class='col-12 col-lg-6 col-xl-7'>
				<b>What is a baby turkey called?</b>
			</div>
			<div class='col-12 col-lg-6 col-xl-5'>
				<select name='13' id='class' class='form-control' type='dropdown'>
					<option value='1'>A chick</option>
					<option value='2'>A nestling</option>
					<option value='3'>A poult</option>
				</select>
			</div>
		</div>
		<br />
		<div class='row'>
			<div class='col-12 col-lg-6 col-xl-7'>
				<b>What's the estimated amount of Americans that eat turkey at Thanksgiving?</b>
			</div>
			<div class='col-12 col-lg-6 col-xl-5'>
				<select name='14' id='class' class='form-control' type='dropdown'>
					<option value='1'>88%</option>
					<option value='2'>50%</option>
					<option value='3'>75%</option>
				</select>
			</div>
		</div>
		<br />
		<div class='row'>
			<div class='col-12 col-lg-6 col-xl-7'>
				<b>What's the estimated amount of feathers a fully grown turkey has?</b>
			</div>
			<div class='col-12 col-lg-6 col-xl-5'>
				<select name='15' id='class' class='form-control' type='dropdown'>
					<option value='1'>1,000,000</option>
					<option value='2'>Too many to count.</option>
					<option value='3'>3,500</option>
				</select>
			</div>
		</div>
		<br />
		<div class='row'>
			<div class='col-12 col-lg-6 col-xl-7'>
				<b>What'd you like from CID Admin, as thanks for playing CID?</b>
			</div>
			<div class='col-12 col-lg-6 col-xl-5'>
				<select name='16' id='class' class='form-control' type='dropdown'>
					<option value='1'>3 VIP Days</option>
					<option value='2'>3 VIP Days</option>
					<option value='3'>3 VIP Days</option>
				</select>
			</div>
		</div>
		<br />
		<div class='row'>
			<div class='col-12'>
				<input type='submit' value='Submit Answers' class='btn btn-primary btn-block'>
			</div>
		</div>
		<tr>
			<td colspan='2'>
				
			</td>
		</tr>
		</table>
		</form>";
	}
	$h->endpage();
}