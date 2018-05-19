<?php
require('globals.php');
echo "<h3><i class='fas fa-shield-alt'></i> VIP Store</h3><hr />";
if (!isset($_GET['action'])) {
    $_GET['action'] = '';
}
switch ($_GET['action']) {
    case "purchase1":
        purchase(12,240);
        break;
	case "purchase2":
        purchase(68,240);
        break;
	case "purchase3":
        purchase(159,600);
        break;
	case "purchase4":
        purchase(163,600);
        break;
	case "purchase5":
        purchase(32,600);
        break;
	case "purchase7":
        purchase(13,720);
        break;
	case "purchase8":
        purchase(128,720);
        break;
	case "purchase9":
        purchase(122,720);
        break;
	case "purchase10":
        purchase(117,720);
        break;
	case "purchase11":
        purchase(17,240);
        break;
	case "purchase12":
        purchase(59,1200);
        break;
	case "purchase13":
        purchase(92,1200);
        break;
	case "purchase14":
        purchase(91,1200);
        break;
	case "purchase15":
        purchase(14,1200);
        break;
	case "purchase16":
        purchase(124,1200);
        break;
	case "purchase17":
        purchase(15,2400);
        break;
	case "purchase18":
        purchase(16,4800);
        break;
    default:
        home();
        break;
}
function home()
{
	global $ir, $api, $userid;
	echo "Hey, {$ir['username']}! You have " . number_format($ir['premium_currency']) . " Mutton that you can spend today. How do you wish to spend it? Don't have enough 
	Mutton for what you want? You can always <a href='donator.php?user={$userid}'>donate</a> for more!<br />
	<table class='table table-bordered'>
		<tr>
			<th>
				Pack
			</th>
			<th>
				Pack Info
			</th>
			<th>
				Purchase
			</th>
		</tr>
		<tr>
			<td>
				<a href='iteminfo.php?ID=12'>Small VIP Pack</a>
			</td>
			<td>
				10 VIP Days, 100 IQ and 55 Chivalry Tokens
			</td>
			<td>
				<a href='?action=purchase1'>240 Mutton</a>
			</td>
		</tr>
		<tr>
			<td>
				<a href='iteminfo.php?ID=17'>Will Potion</a>
			</td>
			<td>
				Refills your Will to 100%.
			</td>
			<td>
				<a href='?action=purchase11'>240 Mutton</a>
			</td>
		</tr>
		<tr>
			<td>
				<a href='iteminfo.php?ID=68'>Invisibility Potion</a>
			</td>
			<td>
				Allows the user to appear offline for 3 hours. However, all their actions will be visible.
			</td>
			<td>
				<a href='?action=purchase2'>240 Mutton</a>
			</td>
		</tr>
		<tr>
			<td>
				<a href='iteminfo.php?ID=159'>VIP Shield Badge</a>
			</td>
			<td>
				A profile badge exclusive to the VIP Store.
			</td>
			<td>
				<a href='?action=purchase3'>600 Mutton</a>
			</td>
		</tr>
		<tr>
			<td>
				<a href='iteminfo.php?ID=163'>Salty Badge</a>
			</td>
			<td>
				A profile badge exclusive to the VIP Store.
			</td>
			<td>
				<a href='?action=purchase4'>600 Mutton</a>
			</td>
		</tr>
		<tr>
			<td>
				<a href='iteminfo.php?ID=32'>Theft Protection</a>
			</td>
			<td>
				This item will make it so you cannot be robbed by others. Note, that mugging will still take your cash.
			</td>
			<td>
				<a href='?action=purchase5'>600 Mutton</a>
			</td>
		</tr>
		<tr>
			<td>
				<a href='iteminfo.php?ID=32'>Normal VIP Pack</a>
			</td>
			<td>
				30 VIP Days, 300 IQ and 165 Chivalry Tokens
			</td>
			<td>
				<a href='?action=purchase7'>720 Mutton</a>
			</td>
		</tr>
		<tr>
			<td>
				<a href='iteminfo.php?ID=128'>VIP Color Changer</a>
			</td>
			<td>
				Changes the color of your name while you have VIP Days.
			</td>
			<td>
				<a href='?action=purchase8'>720 Mutton</a>
			</td>
		</tr>
		<tr>
			<td>
				<a href='iteminfo.php?ID=122'>Skill Reset Scroll</a>
			</td>
			<td>
				Allows a single reset of your skill tree.
			</td>
			<td>
				<a href='?action=purchase9'>720 Mutton</a>
			</td>
		</tr>
		<tr>
			<td>
				<a href='iteminfo.php?ID=117'>Class Reset Scroll</a>
			</td>
			<td>
				Allows a single reset of your class.
			</td>
			<td>
				<a href='?action=purchase10'>720 Mutton</a>
			</td>
		</tr>
		<tr>
			<td>
				<a href='iteminfo.php?ID=59'>Basic Chivalry Token Pack</a>
			</td>
			<td>
				3,250 Chivalry Tokens
			</td>
			<td>
				<a href='?action=purchase12'>1,200 Mutton</a>
			</td>
		</tr>
		<tr>
			<td>
				<a href='iteminfo.php?ID=92'>Auto Boxes of Random Opener</a>
			</td>
			<td>
				Open 30,000 Boxes of Random in an automated fashion.
			</td>
			<td>
				<a href='?action=purchase13'>1,200 Mutton</a>
			</td>
		</tr>
		<tr>
			<td>
				<a href='iteminfo.php?ID=91'>Auto Hexbags Opener</a>
			</td>
			<td>
				Open 6,000 Hexbags in an automated fashion.
			</td>
			<td>
				<a href='?action=purchase14'>1,200 Mutton</a>
			</td>
		</tr>
		<tr>
			<td>
				<a href='iteminfo.php?ID=14'>Large VIP Pack</a>
			</td>
			<td>
				50 VIP Days, 750 IQ and 275 Chivalry Tokens
			</td>
			<td>
				<a href='?action=purchase15'>1,200 Mutton</a>
			</td>
		</tr>
		<tr>
			<td>
				<a href='iteminfo.php?ID=124'>Basic IQ Pack</a>
			</td>
			<td>
				1,250 IQ
			</td>
			<td>
				<a href='?action=purchase16'>1,200 Mutton</a>
			</td>
		</tr>
		<tr>
			<td>
				<a href='iteminfo.php?ID=15'>Huge VIP Pack</a>
			</td>
			<td>
				110 VIP Days, 1,750 IQ and 605 Chivalry Tokens
			</td>
			<td>
				<a href='?action=purchase17'>2,400 Mutton</a>
			</td>
		</tr>
		<tr>
			<td>
				<a href='iteminfo.php?ID=16'>Excessive VIP Pack</a>
			</td>
			<td>
				220 VIP Days, 4,098 IQ and 1,210 Chivalry Tokens
			</td>
			<td>
				<a href='?action=purchase18'>4,800 Mutton</a>
			</td>
		</tr>
	</table>";
}
function purchase($itmid,$mutton)
{
	global $api,$userid,$h,$db,$ir;
	if ($ir['premium_currency'] < $mutton)
	{
		alert('danger',"Uh Oh!","You need " . number_format($mutton) . " Mutton to buy {$api->SystemItemIDtoName($itmid)}, but you only have {$ir['premium_currency']}.",true,'vipstore.php');
		die($h->endpage());
	}
	else
	{
		$db->query("UPDATE `users` SET `premium_currency` = `premium_currency` - {$mutton} WHERE `userid` = {$userid}");
		$api->UserGiveItem($userid,$itmid,1);
		alert('success',"Success!","You have successfully traded " . number_format($mutton) . " Mutton to buy {$api->SystemItemIDtoName($itmid)}.",true,'vipstore.php');
		$api->SystemLogsAdd($userid,'vipstore',"Traded {$mutton} for 1 {$api->SystemItemIDtoName($itmid)}.");
	}
}
$h->endpage();