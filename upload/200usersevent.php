<?php
require('globals.php');
echo "<h3>200 Users Event</h3><hr />";
$multi=levelMultiplier($ir['level']);
if (!isset($_GET['action'])) {
    $_GET['action'] = '';
}
$cost[1]=round(5*$multi);
$cost[2]=round(50*$multi);
$cost[3]=round(50*$multi);
$cost[4]=round(100*$multi);
$cost[5]=round(75*$multi);
$cost[6]=round(75*$multi);
$cost[7]=round(115*$multi);
$cost[8]=round(115*$multi);
$cost[9]=round(130*$multi);
switch ($_GET['action']) {
    case "purchase1":
        purchase(164,$cost[1]);
        break;
	case "purchase2":
        purchase(165,$cost[2]);
        break;
	case "purchase3":
        purchase(166,$cost[3]);
        break;
	case "purchase4":
        purchase(152,$cost[4]);
        break;
	case "purchase5":
        purchase(116,$cost[5]);
        break;
	case "purchase6":
        purchase(93,$cost[6]);
        break;
	case "purchase7":
        purchase(167,$cost[7]);
        break;
	case "purchase8":
        purchase(168,$cost[8]);
        break;
	case "purchase9":
        purchase(148,$cost[9]);
        break;
    default:
        home();
        break;
}
function home()
{
	global $db,$ir,$userid,$api,$h,$cost;
	echo "Welcome to the 200 Registered Users Event, {$ir['username']}. Admittingly, the only few things significant about the number 200 were just war, 
	we've decided that we should have a sort of 'war' inside of Chivalry is Dead to celebrate. This event will continue until the end of the referral contest, May 12th. 
	More items <i>may</i> roll out as the event progresses, so it might be worthwhile to keep some points in reserve.<br />
	The unique items in this event can be purchased using <b>Points</b>. Points are earned by simply besting other players in battle. 
	No tricks. No gotchas. Just simple combat.<br />
	<b><u>You have {$ir['holiday']} Points.</u></b><br />
	<div class='row'>
		<div class='col-sm-4'>
				<div class='card'>
					<div class='card-header box-shadow'>
						<a href='iteminfo.php?ID=164'>200 Users Badge</a>
					</div>
					<div class='card-body'>
						" . returnIcon(164,7) . "<br />
						<a href='?action=purchase1' class='btn btn-primary'>Purchase - {$cost[1]} Points</a>
					</div>
				</div>
		</div>
		<div class='col-sm-4'>
				<div class='card'>
					<div class='card-header box-shadow'>
						<a href='iteminfo.php?ID=165'>Scimitar of Early Traditions</a>
					</div>
					<div class='card-body'>
						" . returnIcon(165,7) . "<br />
						<a href='?action=purchase2' class='btn btn-primary'>Purchase - {$cost[2]} Points</a>
					</div>
				</div>
		</div>
		<div class='col-sm-4'>
				<div class='card'>
					<div class='card-header box-shadow'>
						<a href='iteminfo.php?ID=166'>Iron Scaled Armor</a>
					</div>
					<div class='card-body'>
						" . returnIcon(166,7) . "<br />
						<a href='?action=purchase3' class='btn btn-primary'>Purchase - {$cost[3]} Points</a>
					</div>
				</div>
		</div>
	</div>
	<div class='row'>
		<div class='col-sm-4'>
				<div class='card'>
					<div class='card-header box-shadow'>
						<a href='iteminfo.php?ID=152'>Baetylus</a>
					</div>
					<div class='card-body'>
						" . returnIcon(152,7) . "<br />
						<a href='?action=purchase4' class='btn btn-primary'>Purchase - {$cost[4]} Points</a>
					</div>
				</div>
		</div>
		<div class='col-sm-4'>
				<div class='card'>
					<div class='card-header box-shadow'>
						<a href='iteminfo.php?ID=116'>Skull Ring</a>
					</div>
					<div class='card-body'>
						" . returnIcon(116,7) . "<br />
						<a href='?action=purchase5' class='btn btn-primary'>Purchase - {$cost[5]} Points</a>
					</div>
				</div>
		</div>
		<div class='col-sm-4'>
				<div class='card'>
					<div class='card-header box-shadow'>
						<a href='iteminfo.php?ID=93'>Experience Coin</a>
					</div>
					<div class='card-body'>
						" . returnIcon(93,7) . "<br />
						<a href='?action=purchase6' class='btn btn-primary'>Purchase - {$cost[6]} Points</a>
					</div>
				</div>
		</div>
	</div>
		<div class='row'>
		<div class='col-sm-4'>
				<div class='card'>
					<div class='card-header box-shadow'>
						<a href='iteminfo.php?ID=167'>Tome of Labor</a>
					</div>
					<div class='card-body'>
						" . returnIcon(167,7) . "<br />
						<a href='?action=purchase7' class='btn btn-primary'>Purchase - {$cost[7]} Points</a>
					</div>
				</div>
		</div>
		<div class='col-sm-4'>
				<div class='card'>
					<div class='card-header box-shadow'>
						<a href='iteminfo.php?ID=168'>Tome of Intelligence</a>
					</div>
					<div class='card-body'>
						" . returnIcon(168,7) . "<br />
						<a href='?action=purchase8' class='btn btn-primary'>Purchase - {$cost[8]} Points</a>
					</div>
				</div>
		</div>
		<div class='col-sm-4'>
				<div class='card'>
					<div class='card-header box-shadow'>
						<a href='iteminfo.php?ID=148'>Tome of Experience</a>
					</div>
					<div class='card-body'>
						" . returnIcon(148,7) . "<br />
						<a href='?action=purchase6' class='btn btn-primary'>Purchase - {$cost[9]} Points</a>
					</div>
				</div>
		</div>
	</div>";
	
	$h->endpage();
}
function purchase($itmid,$cost)
{
	global $db,$userid,$api,$h,$ir;
	$itmname=$api->SystemItemIDtoName($itmid);
	if ($ir['holiday'] < $cost)
	{
		alert('danger',"Uh Oh!","You need {$cost} points to buy {$itmname}. You only have {$ir['holiday']}.",true,'200usersevent.php');
		die($h->endpage());
	}
	else
	{
		$db->query("UPDATE `user_settings` SET `holiday` = `holiday` - {$cost} WHERE `userid` = {$userid}");
		$api->UserGiveItem($userid,$itmid,1);
		alert('success',"Success!","You have successfully traded {$cost} Points for one {$itmname}.",true,'200usersevent.php');
		$h->endpage();
	}
}