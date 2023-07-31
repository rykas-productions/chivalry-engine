<?php
require("globals.php");
if (!isset($MUS))
	$MUS = ($db->fetch_row($db->query("/*qc=on*/SELECT * FROM `mining` WHERE `userid` = {$userid} LIMIT 1")));
if (!isset($_GET['action'])) {
    $_GET['action'] = '';
}
switch ($_GET['action']) {
    case 'level1':
        level(5,1);
        break;
	case 'level2':
        level(25,2);
        break;
	case 'level3':
        level(100,3);
        break;
	case 'level4':
        level(200,4);
        break;
	case 'level5':
        level(300,5);
        break;
	case 'level6':
        level(50,49);
        break;
	case 'level7':
        level(150,50);
        break;
	case 'level8':
        level(500,51);
        break;
	case 'level9':
        level(750,87);
        break;
	case 'level10':
        level(1000,88);
        break;
	case 'level11':
        level(1500,89);
        break;
	case 'level12':
        level(2000,90);
        break;
	case 'bust1':
        busts(25,6);
        break;
	case 'bust2':
        busts(100,7);
        break;
	case 'bust3':
        busts(250,8);
        break;
	case 'bust4':
        busts(500,9);
        break;
	case 'bust5':
        busts(1000,10);
        break;
	case 'mine1':
        mine(10,11);
        break;
	case 'mine2':
        mine(20,12);
        break;
	case 'mine3':
        mine(50,13);
        break;
    case 'mine4':
        mine(75,82);
        break;
    case 'mine5':
        mine(100,83);
        break;
    case 'mine6':
        mine(200,84);
        break;
	case 'mine7':
        mine(300,96);
        break;
	case 'mine8':
        mine(500,97);
        break;
	case 'mine9':
        mine(750,98);
        break;
	case 'mine10':
        mine(1000,99);
        break;
	case 'kill1':
        kills(10,14);
        break;
	case 'kill2':
        kills(50,15);
        break;
	case 'kill3':
        kills(100,16);
        break;
	case 'kill4':
        kills(500,17);
        break;
	case 'kill5':
        kills(1000,18);
        break;
	case 'kill6':
        kills(2500,100);
        break;
	case 'kill7':
        kills(5000,101);
        break;
	case 'kill8':
        kills(10000,102);
        break;
	case 'kill9':
        kills(25000,103);
        break;
	case 'kill10':
        kills(50000,104);
        break;
	case 'death1':
		deaths(10,19);
        break;
	case 'death2':
        deaths(50,20);
        break;
	case 'death3':
        deaths(100,21);
        break;
	case 'death4':
        deaths(500,22);
        break;
	case 'death5':
        deaths(1000,23);
        break;
	case 'refer1':
		refers(1,24);
        break;
	case 'refer2':
        refers(5,25);
        break;
	case 'refer3':
        refers(10,26);
        break;
	case 'refer4':
        refers(50,27);
        break;
	case 'refer5':
        refers(100,28);
        break;
	case 'crimec1':
        crimecopper(500000,29);
        break;
	case 'crimec2':
        crimecopper(1000000,30);
        break;
	case 'crimec3':
        crimecopper(5000000,31);
        break;
	case 'crimec4':
        crimecopper(25000000,32);
        break;
	case 'crimec5':
        crimecopper(100000000,33);
        break;
	case 'travel1':
        travel(1,34);
        break;
	case 'travel2':
        travel(5,35);
        break;
	case 'travel3':
        travel(10,36);
        break;
	case 'travel4':
        travel(50,37);
        break;
	case 'travel5':
        travel(100,38);
        break;
	case 'dam1':
        damage(1000,39);
        break;
	case 'dam2':
        damage(25000,40);
        break;
	case 'dam3':
        damage(100000,41);
        break;
	case 'dam4':
        damage(1000000,42);
        break;
	case 'dam5':
        damage(25000000,43);
        break;
	case 'worth1':
        worth(500000,44);
        break;
	case 'worth2':
        worth(5000000,45);
        break;
	case 'worth3':
        worth(50000000,46);
        break;
	case 'worth4':
        worth(500000000,47);
        break;
	case 'worth5':
        worth(1000000000,48);
        break;
	case 'worth6':
	    worth(5000000000,105);
	    break;
	case 'worth7':
	    worth(10000000000,106);
	    break;
	case 'worth8':
	    worth(25000000000,107);
	    break;
	case 'worth9':
	    worth(10000000000,108);
	    break;
	case 'worth10':
	    worth(100000000000,109);
	    break;
	case 'posts1':
        posts(5,52);
        break;
	case 'posts2':
        posts(25,53);
        break;
	case 'posts3':
        posts(75,54);
        break;
	case 'posts4':
        posts(500,55);
        break;
	case 'posts5':
        posts(1000,56);
        break;
    case 'dayslogged1':
        dayslogged(7,57);
        break;
    case 'dayslogged2':
        dayslogged(14,58);
        break;
    case 'dayslogged3':
        dayslogged(30,59);
        break;
    case 'dayslogged4':
        dayslogged(120,60);
        break;
    case 'dayslogged5':
        dayslogged(365,61);
        break;
    case 'iq1':
        iq(10000,62);
        break;
    case 'iq2':
        iq(25000,63);
        break;
    case 'iq3':
        iq(100000,64);
        break;
    case 'iq4':
        iq(1000000,65);
        break;
    case 'iq5':
        iq(5000000,66);
        break;
	case 'vip1':
        vip(10,67);
        break;
    case 'vip2':
        vip(30,68);
        break;
    case 'vip3':
        vip(90,69);
        break;
    case 'vip4':
        vip(180,70);
        break;
    case 'vip5':
        vip(365,71);
        break;
	case 'labor1':
        labor(100000,72);
        break;
    case 'labor2':
        labor(500000,73);
        break;
    case 'labor3':
        labor(1000000,74);
        break;
    case 'labor4':
        labor(10000000,75);
        break;
    case 'labor5':
        labor(50000000,76);
        break;
    case 'course1':
        course(1,77);
        break;
    case 'course2':
        course(5,78);
        break;
    case 'course3':
        course(10,79);
        break;
    case 'course4':
        course(20,80);
        break;
    case 'course5':
        course(40,81);
        break;
	case 'mastery':
        masterrank(1,91);
        break;
	case 'mastery2':
        masterrank(2,92);
        break;
	case 'mastery3':
        masterrank(3,93);
        break;
	case 'mastery4':
        masterrank(4,94);
        break;
	case 'mastery5':
        masterrank(5,95);
        break;
    default:
        home();
        break;
}
function home()
{
	global $h, $ir, $db, $api, $MUS, $userid, $set;
	$ir['actual_reset'] = $ir['reset'] - 1;
	$ir['courses_done']=$db->fetch_single($db->query("/*qc=on*/SELECT COUNT(`userid`) FROM `academy_done` WHERE `userid` = {$userid}"));
	$ir['forum_posts']=$db->fetch_single($db->query("/*qc=on*/SELECT COUNT('fp_id') FROM `forum_posts` WHERE `fp_poster_id`={$userid}"));
	$estates = $db->fetch_single($db->query("SELECT SUM(`vault`) FROM `user_estates` WHERE `userid` = {$userid}"));
	$ir['net_worth']=$estates+$ir['primary_currency']+$ir['bank']+$ir['bigbank']+($ir['secondary_currency']*$set['token_minimum'])+($ir['tokenbank']*$set['token_minimum'])+$ir['vaultbank'];
	$ir['dmg_dlt']=$db->fetch_single($db->query("/*qc=on*/SELECT SUM(`value`) FROM `user_logging` WHERE `userid` = {$userid} AND `log_name` = 'dmgdone'"));
	$ir['travel_times']=$db->fetch_single($db->query("/*qc=on*/SELECT SUM(`value`) FROM `user_logging` WHERE `userid` = {$userid} AND `log_name` = 'travel'"));
	$ir['crime_copper']=$db->fetch_single($db->query("/*qc=on*/SELECT SUM(`crimecopper`) FROM `crime_logs` WHERE `userid` = {$userid}"));
	$ir['referral_count']=$ref=$db->fetch_single($db->query("/*qc=on*/SELECT COUNT(`referalid`) FROM `referals` WHERE `referal_userid` = {$userid}"));
	
	echo "Here's a list of in-game achievements. Click on an achievement to be rewarded with it. You may only 
	be rewarded once per achievement. Each achievement you complete will get you 1 skill point.";
	$count=1;
	//this # for next achievement number
	while ($count != 110)
	{
		$class[$count]= (userHasAchievement($count)) ? "class='text-success'" : "class='text-danger font-weight-bold'" ;
		$count=$count+1;
	}
	echo "
	<div class='row'>
		<div class='col-sm'>
			<div class='card'>
				<div class='card-header'>
					<div class='row'>
						<div class='col-6'>
							Level
						</div>
						<div class='col-6'>
							" . shortNumberParse($ir['level']) . "
						</div>
					</div>
				</div>
				<div class='card-body'>
					<div class='row'>
						<div class='col'>
							<a {$class[1]} href='?action=level1'>Level 5</a><br />
							<a {$class[2]} href='?action=level2'>Level 25</a><br />
							<a {$class[49]} href='?action=level6'>Level 50</a><br />
							<a {$class[3]} href='?action=level3'>Level 100</a><br />
							<a {$class[50]} href='?action=level7'>Level 150</a><br />
							<a {$class[4]} href='?action=level4'>Level 200</a><br />
							<a {$class[5]} href='?action=level5'>Level 300</a>
						</div>
						<div class='col'>
							<a {$class[51]} href='?action=level8'>Level 500</a><br />
							<a {$class[87]} href='?action=level9'>Level 750</a><br />
							<a {$class[88]} href='?action=level10'>Level 1,000</a><br />
							<a {$class[89]} href='?action=level11'>Level 1,500</a><br />
							<a {$class[90]} href='?action=level12'>Level 2,000</a><br />
						</div>
					</div>
				</div>
			</div>
		</div>
		<div class='col-sm'>
			<div class='card'>
				<div class='card-header'>
					<div class='row'>
						<div class='col-6'>
							Mining Level
						</div>
						<div class='col-6'>
						" . shortNumberParse($MUS['mining_level']) . "
						</div>
					</div>
				</div>
				<div class='card-body'>
					<div class='row'>
						<div class='col'>
							<a {$class[11]} href='?action=mine1'>Level 10</a><br />
							<a {$class[12]} href='?action=mine2'>Level 20</a><br />
							<a {$class[13]} href='?action=mine3'>Level 50</a><br />
							<a {$class[82]} href='?action=mine4'>Level 75</a><br />
							<a {$class[83]} href='?action=mine5'>Level 100</a><br />
							<a {$class[84]} href='?action=mine6'>Level 200</a><br />
						</div>
						<div class='col'>
							<a {$class[96]} href='?action=mine7'>Level 300</a><br />
							<a {$class[97]} href='?action=mine8'>Level 500</a><br />
							<a {$class[98]} href='?action=mine9'>Level 750</a><br />
							<a {$class[99]} href='?action=mine8'>Level 1,000</a><br />
						</div>
					</div>
				</div>
			</div>
		</div>
		<div class='col-sm'>
			<div class='card'>
				<div class='card-header'>
					<div class='row'>
						<div class='col-6'>
							Dungeon Busts
						</div>
						<div class='col-6'>
							" . shortNumberParse($ir['busts']) . "
						</div>
					</div>
				</div>
				<div class='card-body'>
					<a {$class[6]} href='?action=bust1'>25 Busts</a><br />
					<a {$class[7]} href='?action=bust2'>100 Busts</a><br />
					<a {$class[8]} href='?action=bust3'>250 Busts</a><br />
					<a {$class[9]} href='?action=bust4'>500 Busts</a><br />
					<a {$class[10]} href='?action=bust5'>1,000 Busts</a><br />
				</div>
			</div>
		</div>
	</div>
	<br />
	<div class='row'>
		<div class='col-sm'>
			<div class='card'>
				<div class='card-header'>
					<div class='row'>
						<div class='col-6'>
							Kills
						</div>
						<div class='col-6'>
							" . shortNumberParse($ir['kills']) . "
						</div>
					</div>
				</div>
				<div class='card-body'>
					<div class='row'>
						<div class='col'>
							<a {$class[14]} href='?action=kill1'>10 Kills</a><br />
							<a {$class[15]} href='?action=kill2'>50 Kills</a><br />
							<a {$class[16]} href='?action=kill3'>100 Kills</a><br />
							<a {$class[17]} href='?action=kill4'>500 Kills</a><br />
							<a {$class[18]} href='?action=kill5'>1,000 Kills</a><br />
						</div>
						<div class='col'>
							<a {$class[100]} href='?action=kill6'>2,500 Kills</a><br />
							<a {$class[101]} href='?action=kill7'>5,000 Kills</a><br />
							<a {$class[102]} href='?action=kill8'>10,000 Kills</a><br />
							<a {$class[103]} href='?action=kill9'>25,000 Kills</a><br />
							<a {$class[104]} href='?action=kill10'>50,000 Kills</a><br />
						</div>
					</div>
				</div>
			</div>
		</div>
		<div class='col-sm'>
			<div class='card'>
				<div class='card-header'>
					<div class='row'>
						<div class='col-6'>
							Deaths
						</div>
						<div class='col-6'>
							" . shortNumberParse($ir['deaths']) . "
						</div>
					</div>
				</div>
				<div class='card-body'>
					<div class='row'>
						<div class='col'>
							<a {$class[19]} href='?action=death1'>10 Deaths</a><br />
							<a {$class[20]} href='?action=death2'>50 Deaths</a><br />
							<a {$class[21]} href='?action=death3'>100 Deaths</a><br />
							<a {$class[22]} href='?action=death4'>500 Deaths</a><br />
							<a {$class[23]} href='?action=death5'>1,000 Deaths</a><br />
						</div>
						
						<div class='col'>
						
						</div>
					</div>
				</div>
			</div>
		</div>
		<div class='col-sm'>
			<div class='card'>
				<div class='card-header'>
					<div class='row'>
						<div class='col-6'>
							Referrals
						</div>
						<div class='col-6'>
							" . shortNumberParse($ir['referral_count']) . "
						</div>
					</div>
				</div>
				<div class='card-body'>
					<a {$class[24]} href='?action=refer1'>1 Referral</a><br />
					<a {$class[25]} href='?action=refer2'>5 Referrals</a><br />
					<a {$class[26]} href='?action=refer3'>10 Referrals</a><br />
					<a {$class[27]} href='?action=refer4'>50 Referrals</a><br />
					<a {$class[28]} href='?action=refer5'>100 Referrals</a><br />
				</div>
			</div>
		</div>
	</div>
	<br />
	<div class='row'>
		<div class='col-sm'>
			<div class='card'>
				<div class='card-header'>
					<div class='row'>
						<div class='col-6'>
							Crime Copper
						</div>
						<div class='col-6'>
							" . shortNumberParse($ir['crime_copper']) . "
						</div>
					</div>
				</div>
				<div class='card-body'>
					<a {$class[29]} href='?action=crimec1'>500K</a><br />
					<a {$class[30]} href='?action=crimec2'>1M</a><br />
					<a {$class[31]} href='?action=crimec3'>5M</a><br />
					<a {$class[32]} href='?action=crimec4'>25M</a><br />
					<a {$class[33]} href='?action=crimec5'>100M</a><br />
				</div>
			</div>
		</div>
		<div class='col-sm'>
			<div class='card'>
				<div class='card-header'>
					<div class='row'>
						<div class='col-6'>
							Times Travelled
						</div>
						<div class='col-6'>
							" . shortNumberParse($ir['travel_times']) . "
						</div>
					</div>
				</div>
				<div class='card-body'>
					<a {$class[34]} href='?action=travel1'>1 Time</a><br />
					<a {$class[35]} href='?action=travel2'>5 Times</a><br />
					<a {$class[36]} href='?action=travel3'>10 Times</a><br />
					<a {$class[37]} href='?action=travel4'>50 Times</a><br />
					<a {$class[38]} href='?action=travel5'>100 Times</a><br />
				</div>
			</div>
		</div>
		<div class='col-sm'>
			<div class='card'>
				<div class='card-header'>
					<div class='row'>
						<div class='col-6'>
							Damage Dealt
						</div>
						<div class='col-6'>
							" . shortNumberParse($ir['dmg_dlt']) . "
						</div>
					</div>
				</div>
				<div class='card-body'>
					<a {$class[39]} href='?action=dam1'>1K</a><br />
					<a {$class[40]} href='?action=dam2'>2K</a><br />
					<a {$class[41]} href='?action=dam3'>1K</a><br />
					<a {$class[42]} href='?action=dam4'>1M</a><br />
					<a {$class[43]} href='?action=dam5'>25M</a><br />
				</div>
			</div>
		</div>
	</div>
	<br />
	<div class='row'>
		<div class='col-sm'>
			<div class='card'>
				<div class='card-header'>
					<div class='row'>
						<div class='col-6'>
							Networth
						</div>
						<div class='col-6'>
							" . shortNumberParse($ir['net_worth']) . "
						</div>
					</div>
				</div>
				<div class='card-body'>
                    <div class='row'>
						<div class='col'>
        					<a {$class[44]} href='?action=worth1'>500K</a><br />
        					<a {$class[45]} href='?action=worth2'>5M</a><br />
        					<a {$class[46]} href='?action=worth3'>50M</a><br />
        					<a {$class[47]} href='?action=worth4'>500M</a><br />
        					<a {$class[48]} href='?action=worth5'>1B</a><br />
                        </div>
                        <div class='col'>
                            <a {$class[105]} href='?action=worth6'>5B</a><br />
        					<a {$class[106]} href='?action=worth7'>10B</a><br />
        					<a {$class[107]} href='?action=worth8'>25B</a><br />
                            <a {$class[108]} href='?action=worth9'>100B</a><br />
                            <a {$class[109]} href='?action=worth10'>1T</a><br />
                        </div>
                    </div>
				</div>
			</div>
		</div>
		<div class='col-sm'>
			<div class='card'>
				<div class='card-header'>
					<div class='row'>
						<div class='col-6'>
							Forum Posts
						</div>
						<div class='col-6'>
							" . shortNumberParse($ir['forum_posts']) . "
						</div>
					</div>
				</div>
				<div class='card-body'>
					<a {$class[52]} href='?action=posts1'>5 Posts</a><br />
					<a {$class[53]} href='?action=posts2'>25 Posts</a><br />
					<a {$class[54]} href='?action=posts3'>75 Posts</a><br />
					<a {$class[55]} href='?action=posts4'>500 Posts</a><br />
					<a {$class[56]} href='?action=posts5'>1,000 Posts</a><br />
				</div>
			</div>
		</div>
		<div class='col-sm'>
            <div class='card'>
				<div class='card-header'>
					<div class='row'>
						<div class='col-6'>
							Days Logged In
						</div>
						<div class='col-6'>
							" . shortNumberParse($ir['dayslogged']) . "
						</div>
					</div>
				</div>
				<div class='card-body'>
					<a {$class[57]} href='?action=dayslogged1'>7 Days</a><br />
					<a {$class[58]} href='?action=dayslogged2'>14 Days</a><br />
					<a {$class[59]} href='?action=dayslogged3'>30 Days</a><br />
					<a {$class[60]} href='?action=dayslogged4'>120 Days</a><br />
					<a {$class[61]} href='?action=dayslogged5'>365 Days</a><br />
				</div>
			</div>
		</div>
	</div>
	<br />
    <div class='row'>
		<div class='col-sm'>
			<div class='card'>
				<div class='card-header'>
					<div class='row'>
						<div class='col-6'>
							IQ
						</div>
						<div class='col-6'>
							" . shortNumberParse($ir['iq']) . "
						</div>
					</div>
				</div>
				<div class='card-body'>
					<a {$class[62]} href='?action=iq1'>10K</a><br />
					<a {$class[63]} href='?action=iq2'>25K</a><br />
					<a {$class[64]} href='?action=iq3'>100K</a><br />
					<a {$class[65]} href='?action=iq4'>1M</a><br />
					<a {$class[66]} href='?action=iq5'>5M</a><br />
				</div>
			</div>
		</div>
		<div class='col-sm'>
			<div class='card'>
				<div class='card-header'>
					<div class='row'>
						<div class='col-6'>
							VIP Days
						</div>
						<div class='col-6'>
							" . shortNumberParse($ir['vip_days']) . "
						</div>
					</div>
				</div>
				<div class='card-body'>
					<a {$class[67]} href='?action=vip1'>10 VIP Days</a><br />
					<a {$class[68]} href='?action=vip2'>30 VIP Days</a><br />
					<a {$class[69]} href='?action=vip3'>90 VIP Days</a><br />
					<a {$class[70]} href='?action=vip4'>180 VIP Days</a><br />
					<a {$class[71]} href='?action=vip5'>365 VIP Days</a><br />
				</div>
			</div>
		</div>
		<div class='col-sm'>
            <div class='card'>
				<div class='card-header'>
					<div class='row'>
						<div class='col-6'>
							Labor
						</div>
						<div class='col-6'>
							" . shortNumberParse($ir['labor']) . "
						</div>
					</div>
				</div>
				<div class='card-body'>
					<a {$class[72]} href='?action=labor1'>100K</a><br />
					<a {$class[73]} href='?action=labor2'>500K</a><br />
					<a {$class[74]} href='?action=labor3'>1M</a><br />
					<a {$class[75]} href='?action=labor4'>10M</a><br />
					<a {$class[76]} href='?action=labor5'>50M</a><br />
				</div>
			</div>
		</div>
	</div>
	<br />
    <div class='row'>
		<div class='col-sm'>
			<div class='card'>
				<div class='card-header'>
					<div class='row'>
						<div class='col-6'>
							Courses Done
						</div>
						<div class='col-6'>
							" . shortNumberParse($ir['courses_done']) . "
						</div>
					</div>
				</div>
				<div class='card-body'>
					<a {$class[77]} href='?action=course1'>1 Course</a><br />
					<a {$class[78]} href='?action=course2'>5 Courses</a><br />
					<a {$class[79]} href='?action=course3'>10 Courses</a><br />
					<a {$class[80]} href='?action=course4'>20 Courses</a><br />
					<a {$class[81]} href='?action=course5'>40 Courses</a><br />
				</div>
			</div>
		</div>
		<div class='col-sm'>
			<div class='card'>
				<div class='card-header'>
					<div class='row'>
						<div class='col-6'>
							Mastery Rank
						</div>
						<div class='col-6'>
							" . shortNumberParse($ir['actual_reset']) . "
						</div>
					</div>
				</div>
				<div class='card-body'>
					<a {$class[91]} href='?action=mastery'>Mastery Rank I</a><br />
					<a {$class[92]} href='?action=mastery2'>Mastery Rank II</a><br />
					<a {$class[93]} href='?action=mastery3'>Mastery Rank III</a><br />
					<a {$class[94]} href='?action=mastery4'>Mastery Rank IV</a><br />
					<a {$class[95]} href='?action=mastery5'>Mastery Rank V</a><br />
				</div>
			</div>
		</div>
		<div class='col-sm'>
			<div class='card'>
				<div class='card-header'>
					Misc Achievements
				</div>
				<div class='card-body'>
					<a {$class[85]}>Chump Change</a><br />
					<a {$class[86]}>King of the World</a><br />
				</div>
		</div>
	</div>";
	$h->endpage();
}
function level($level,$id)
{
	global $db,$ir,$userid,$api,$h;
	$achieved=$db->query("/*qc=on*/SELECT * FROM `achievements_done` WHERE `userid` = {$userid} and `achievement` = {$id}");
	if ($db->num_rows($achieved) > 0)
	{
		alert('danger',"Uh Oh!","You can only receive each achievement once.",true,'achievements.php');
		die($h->endpage());
	}
	if ($ir['level'] < $level)
	{
		alert('danger',"Uh Oh!","Your level is too low to receive this achievement. You need to be level {$level}. You are level {$ir['level']}.",true,'achievements.php');
		die($h->endpage());
	}
	userCompleteAchievement($userid, $id);
	$api->SystemLogsAdd($userid,'achievement',"Received achievement for level {$level}.");
	$tokens=Random(25000,100000);
	$api->UserGiveCurrency($userid,'primary',$tokens);
	givePoint($userid);
	addToEconomyLog('Achievements', 'copper', $tokens);
	alert('success',"Success!","You have successfully achieved the Level {$level} achievement and were rewarded " . shortNumberParse($tokens) . " Copper Coins.",true,'achievements.php');
	$h->endpage();
}
function busts($level,$id)
{
	global $db,$ir,$userid,$api,$h;
	$achieved=$db->query("/*qc=on*/SELECT * FROM `achievements_done` WHERE `userid` = {$userid} and `achievement` = {$id}");
	if ($db->num_rows($achieved) > 0)
	{
		alert('danger',"Uh Oh!","You can only receive each achievement once.",true,'achievements.php');
		die($h->endpage());
	}
	if ($ir['busts'] < $level)
	{
		alert('danger',"Uh Oh!","Your bust count is too low to receive this achievement. You need to have at least " . shortNumberParse($level) . " busts. You only have {$ir['busts']} dungeon busts.",true,'achievements.php');
		die($h->endpage());
	}
	userCompleteAchievement($userid, $id);
	$api->SystemLogsAdd($userid,'achievement',"Received achievement for total busts {$level}.");
	$tokens=Random(25000,100000);
	$api->UserGiveCurrency($userid,'primary',$tokens);
	givePoint($userid);
	addToEconomyLog('Achievements', 'copper', $tokens);
	alert('success',"Success!","You have successfully achieved the Total Busts " . shortNumberParse($level) . " achievement and were rewarded " . shortNumberParse($tokens) . " Copper Coins.",true,'achievements.php');
	$h->endpage();
}
function mine($level,$id)
{
	global $db,$ir,$userid,$api,$h;
	$achieved=$db->query("/*qc=on*/SELECT * FROM `achievements_done` WHERE `userid` = {$userid} and `achievement` = {$id}");
	if ($db->num_rows($achieved) > 0)
	{
		alert('danger',"Uh Oh!","You can only receive each achievement once.",true,'achievements.php');
		die($h->endpage());
	}
	$minelvl=$db->fetch_single($db->query("/*qc=on*/SELECT `mining_level` FROM `mining` WHERE `userid` = {$userid}"));
	if ($minelvl < $level)
	{
		alert('danger',"Uh Oh!","Your mining level is too low to receive this achievement. You need to be mining level {$level}. You have a mining level of {$minelvl}.",true,'achievements.php');
		die($h->endpage());
	}
	userCompleteAchievement($userid, $id);
	$api->SystemLogsAdd($userid,'achievement',"Received achievement for mining level {$level}.");
	givePoint($userid);
	$api->UserGiveItem($userid, 424, 1);
	alert('success',"Success!","You have successfully achieved the Mining Level {$level} achievement and were rewarded a Powered Miner!",true,'achievements.php');
	$h->endpage();
}
function kills($level,$id)
{
	global $db,$ir,$userid,$api,$h;
	$achieved=$db->query("/*qc=on*/SELECT * FROM `achievements_done` WHERE `userid` = {$userid} and `achievement` = {$id}");
	if ($db->num_rows($achieved) > 0)
	{
		alert('danger',"Uh Oh!","You can only receive each achievement once.",true,'achievements.php');
		die($h->endpage());
	}
	if ($ir['kills'] < $level)
	{
		alert('danger',"Uh Oh!","Your kill count is too low to receive this achievement. You need to have at least " . shortNumberParse($level) . " kills. You only have " . shortNumberParse($ir['kills']) . " kills.",true,'achievements.php');
		die($h->endpage());
	}
	userCompleteAchievement($userid, $id);
	$api->SystemLogsAdd($userid,'achievement',"Received achievement for {$level} kills.");
	$tokens=Random(25000,100000);
	$api->UserGiveCurrency($userid,'primary',$tokens);
	givePoint($userid);
	addToEconomyLog('Achievements', 'copper', $tokens);
	alert('success',"Success!","You have successfully achieved the " . shortNumberParse($level) . " kills achievement and were rewarded " . shortNumberParse($tokens) . " Copper Coins.",true,'achievements.php');
	$h->endpage();
}
function deaths($level,$id)
{
	global $db,$ir,$userid,$api,$h;
	$achieved=$db->query("/*qc=on*/SELECT * FROM `achievements_done` WHERE `userid` = {$userid} and `achievement` = {$id}");
	if ($db->num_rows($achieved) > 0)
	{
		alert('danger',"Uh Oh!","You can only receive each achievement once.",true,'achievements.php');
		die($h->endpage());
	}
	if ($ir['deaths'] < $level)
	{
		alert('danger',"Uh Oh!","Your death count is too low to receive this achievement. You need to have at least " . shortNumberParse($level) . " deaths. You only have " . shortNumberParse($ir['deaths']) . " deaths.",true,'achievements.php');
		die($h->endpage());
	}
	userCompleteAchievement($userid, $id);
	$api->SystemLogsAdd($userid,'achievement',"Received achievement for {$level} deaths.");
	$tokens=Random(25000,100000);
	$api->UserGiveCurrency($userid,'primary',$tokens);
	givePoint($userid);
	addToEconomyLog('Achievements', 'copper', $tokens);
	alert('success',"Success!","You have successfully achieved the " . shortNumberParse($level) . " deaths achievement and were rewarded " . shortNumberParse($tokens) . " Copper Coins.",true,'achievements.php');
	$h->endpage();
}
function refers($level,$id)
{
	global $db,$ir,$userid,$api,$h;
	$achieved=$db->query("/*qc=on*/SELECT * FROM `achievements_done` WHERE `userid` = {$userid} and `achievement` = {$id}");
	if ($db->num_rows($achieved) > 0)
	{
		alert('danger',"Uh Oh!","You can only receive each achievement once.",true,'achievements.php');
		die($h->endpage());
	}
	$ref=$db->fetch_single($db->query("/*qc=on*/SELECT COUNT(`referalid`) FROM `referals` WHERE `referal_userid` = {$userid}"));
	if ($ref < $level)
	{
		alert('danger',"Uh Oh!","Your referral count is too low to receive this achievement. You need to have at least " . shortNumberParse($level) . " referrals. You only have " . shortNumberParse($ref) . " referrals.",true,'achievements.php');
		die($h->endpage());
	}
	userCompleteAchievement($userid, $id);
	$api->SystemLogsAdd($userid,'achievement',"Received achievement for {$level} referrals.");
	$tokens=Random(25000,100000);
	$api->UserGiveCurrency($userid,'primary',$tokens);
	givePoint($userid);
	addToEconomyLog('Achievements', 'copper', $tokens);
	alert('success',"Success!","You have successfully achieved the " . shortNumberParse($level) . " referrals achievement and were rewarded " . shortNumberParse($tokens) . " Copper Coins.",true,'achievements.php');
	$h->endpage();
}
function crimecopper($level,$id)
{
	global $db,$ir,$userid,$api,$h;
	$achieved=$db->query("/*qc=on*/SELECT * FROM `achievements_done` WHERE `userid` = {$userid} and `achievement` = {$id}");
	if ($db->num_rows($achieved) > 0)
	{
		alert('danger',"Uh Oh!","You can only receive each achievement once.",true,'achievements.php');
		die($h->endpage());
	}
	$crimecopper=$db->fetch_single($db->query("/*qc=on*/SELECT SUM(`crimecopper`) FROM `crime_logs` WHERE `userid` = {$userid}"));
	if ($crimecopper < $level)
	{
		alert('danger',"Uh Oh!","You haven't gained enough Copper Coins from crimes yet to receive this achievement. You've only stolen " . shortNumberParse($crimecopper) . " Copper Coins.",true,'achievements.php');
		die($h->endpage());
	}
	userCompleteAchievement($userid, $id);
	$api->SystemLogsAdd($userid,'achievement',"Received achievement for copper crimers {$level}.");
	$tokens=Random(25000,100000);
	$api->UserGiveCurrency($userid,'primary',$tokens);
	givePoint($userid);
	addToEconomyLog('Achievements', 'copper', $tokens);
	alert('success',"Success!","You have successfully achieved the stolen " . shortNumberParse($level) . " Copper Coins achievement and were rewarded " . shortNumberParse($tokens) . " Copper Coins.",true,'achievements.php');
	$h->endpage();
}
function travel($level,$id)
{
	global $db,$ir,$userid,$api,$h;
	$achieved=$db->query("/*qc=on*/SELECT * FROM `achievements_done` WHERE `userid` = {$userid} and `achievement` = {$id}");
	if ($db->num_rows($achieved) > 0)
	{
		alert('danger',"Uh Oh!","You can only receive each achievement once.",true,'achievements.php');
		die($h->endpage());
	}
	$crimecopper=$db->fetch_single($db->query("/*qc=on*/SELECT SUM(`value`) FROM `user_logging` WHERE `userid` = {$userid} AND `log_name` = 'travel'"));
	if (empty($crimecopper))
		$crimecopper=0;
	if ($crimecopper < $level)
	{
		alert('danger',"Uh Oh!","You haven't traveled enough yet to receive this achievement. You've only traveled " . shortNumberParse($crimecopper) . " times.",true,'achievements.php');
		die($h->endpage());
	}
	userCompleteAchievement($userid, $id);
	$api->SystemLogsAdd($userid,'achievement',"Received achievement for traveling {$level} times.");
	$tokens=Random(25000,100000);
	$api->UserGiveCurrency($userid,'primary',$tokens);
	givePoint($userid);
	addToEconomyLog('Achievements', 'copper', $tokens);
	alert('success',"Success!","You have successfully achieved the Travel " . shortNumberParse($level) . " times achievement and were rewarded " . shortNumberParse($tokens) . " Copper Coins.",true,'achievements.php');
	$h->endpage();
}
function damage($level,$id)
{
	global $db,$ir,$userid,$api,$h;
	$achieved=$db->query("/*qc=on*/SELECT * FROM `achievements_done` WHERE `userid` = {$userid} and `achievement` = {$id}");
	if ($db->num_rows($achieved) > 0)
	{
		alert('danger',"Uh Oh!","You can only receive each achievement once.",true,'achievements.php');
		die($h->endpage());
	}
	$crimecopper=$db->fetch_single($db->query("/*qc=on*/SELECT SUM(`value`) FROM `user_logging` WHERE `userid` = {$userid} AND `log_name` = 'dmgdone'"));
	if (empty($crimecopper))
		$crimecopper=0;
	if ($crimecopper < $level)
	{
		alert('danger',"Uh Oh!","You haven't dealt out enough damage yet to receive this achievement. You've only dealt out " . shortNumberParse($crimecopper) . " damage.",true,'achievements.php');
		die($h->endpage());
	}
	userCompleteAchievement($userid, $id);
	$api->SystemLogsAdd($userid,'achievement',"Received achievement for {$level} damage dealt.");
	$tokens=Random(25000,100000);
	$api->UserGiveCurrency($userid,'primary',$tokens);
	givePoint($userid);
	addToEconomyLog('Achievements', 'copper', $tokens);
	alert('success',"Success!","You have successfully achieved the " . shortNumberParse($level) . " damage dealt achievement and were rewarded " . shortNumberParse($tokens) . " Copper Coins.",true,'achievements.php');
	$h->endpage();
}
function worth($level,$id)
{
	global $db,$ir,$userid,$api,$h,$set;
	$achieved=$db->query("/*qc=on*/SELECT * FROM `achievements_done` WHERE `userid` = {$userid} and `achievement` = {$id}");
	if ($db->num_rows($achieved) > 0)
	{
		alert('danger',"Uh Oh!","You can only receive each achievement once.",true,'achievements.php');
		die($h->endpage());
	}
	$estates = $db->fetch_single($db->query("SELECT SUM(`vault`) FROM `user_estates` WHERE `userid` = {$userid}"));
	$worth=$estates+$ir['primary_currency']+$ir['bank']+$ir['bigbank']+($ir['secondary_currency']*$set['token_minimum'])+($ir['tokenbank']*$set['token_minimum'])+$ir['vaultbank'];
	if (empty($worth))
		$worth=0;
	if ($worth < $level)
	{
		alert('danger',"Uh Oh!","Your worth is too low to recieve this achievement. You only have " . shortNumberParse($worth) . " net worth. Remember, this only values Chivalry Tokens at 1,000 Copper Coins each. This also counts your held Copper Coins and Chivalry Tokens, along with all in your bank accounts.",true,'achievements.php');
		die($h->endpage());
	}
	userCompleteAchievement($userid, $id);
	$api->SystemLogsAdd($userid,'achievement',"Received achievement for {$level} Net Worth.");
	$tokens=Random(25000,100000);
	$api->UserGiveCurrency($userid,'primary',$tokens);
	givePoint($userid);
	addToEconomyLog('Achievements', 'copper', $tokens);
	alert('success',"Success!","You have successfully achieved the " . shortNumberParse($level) . " Net Worth achievement and were rewarded " . shortNumberParse($tokens) . " Copper Coins.",true,'achievements.php');
	$h->endpage();
}
function posts($level,$id)
{
	global $db,$ir,$userid,$api,$h;
	$achieved=$db->query("/*qc=on*/SELECT * FROM `achievements_done` WHERE `userid` = {$userid} and `achievement` = {$id}");
	if ($db->num_rows($achieved) > 0)
	{
		alert('danger',"Uh Oh!","You can only receive each achievement once.",true,'achievements.php');
		die($h->endpage());
	}
	$posts=$db->fetch_single($db->query("/*qc=on*/SELECT COUNT('fp_id') FROM `forum_posts` WHERE `fp_poster_id`={$userid}"));
	if (empty($posts))
		$posts=0;
	if ($posts < $level)
	{
		alert('danger',"Uh Oh!","You haven't posted enough to receive this achievement. You've only posted " . shortNumberParse($posts) . " times.",true,'achievements.php');
		die($h->endpage());
	}
	userCompleteAchievement($userid, $id);
	$api->SystemLogsAdd($userid,'achievement',"Received achievement for {$level} forum posts.");
	$tokens=Random(25000,100000);
	$api->UserGiveCurrency($userid,'primary',$tokens);
	givePoint($userid);
	addToEconomyLog('Achievements', 'copper', $tokens);
	alert('success',"Success!","You have successfully achieved the " . shortNumberParse($level) . " forum posts achievement and were rewarded " . shortNumberParse($tokens) . " Copper Coins. Get a life...",true,'achievements.php');
	$h->endpage();
}
function dayslogged($level,$id)
{
	global $db,$ir,$userid,$api,$h;
	$achieved=$db->query("/*qc=on*/SELECT * FROM `achievements_done` WHERE `userid` = {$userid} and `achievement` = {$id}");
	if ($db->num_rows($achieved) > 0)
	{
		alert('danger',"Uh Oh!","You can only receive each achievement once.",true,'achievements.php');
		die($h->endpage());
	}
	$posts=$ir['dayslogged'];
	if (empty($posts))
		$posts=0;
	if ($posts < $level)
	{
		alert('danger',"Uh Oh!","You do not have enough days logged in consecutively to receive this award. Your current record is " . shortNumberParse($posts) . " days.",true,'achievements.php');
		die($h->endpage());
	}
	userCompleteAchievement($userid, $id);
	$api->SystemLogsAdd($userid,'achievement',"Received achievement for {$level} days logged in consecutively.");
	$tokens=Random(25000,100000);
	$api->UserGiveCurrency($userid,'primary',$tokens);
	givePoint($userid);
	addToEconomyLog('Achievements', 'copper', $tokens);
	alert('success',"Success!","You have successfully achieved the " . shortNumberParse($level) . " days logged in consecutively achievement and were rewarded " . shortNumberParse($tokens) . " Copper Coins.",true,'achievements.php');
	$h->endpage();
}
function iq($level,$id)
{
	global $db,$ir,$userid,$api,$h;
	$achieved=$db->query("/*qc=on*/SELECT * FROM `achievements_done` WHERE `userid` = {$userid} and `achievement` = {$id}");
	if ($db->num_rows($achieved) > 0)
	{
		alert('danger',"Uh Oh!","You can only receive each achievement once.",true,'achievements.php');
		die($h->endpage());
	}
	$posts=$ir['iq'];
	if (empty($posts))
		$posts=0;
	if ($posts < $level)
	{
		alert('danger',"Uh Oh!","You do not have enough IQ to receive this award. You only have " . shortNumberParse($posts) . " IQ.",true,'achievements.php');
		die($h->endpage());
	}
	userCompleteAchievement($userid, $id);
	$api->SystemLogsAdd($userid,'achievement',"Received achievement for {$level} IQ.");
	$tokens=Random(25000,100000);
	$api->UserGiveCurrency($userid,'primary',$tokens);
	givePoint($userid);
	addToEconomyLog('Achievements', 'copper', $tokens);
	alert('success',"Success!","You have successfully achieved the " . shortNumberParse($level) . " IQ achievement and were rewarded " . shortNumberParse($tokens) . " Copper Coins.",true,'achievements.php');
	$h->endpage();
}
function vip($level,$id)
{
	global $db,$ir,$userid,$api,$h;
	$achieved=$db->query("/*qc=on*/SELECT * FROM `achievements_done` WHERE `userid` = {$userid} and `achievement` = {$id}");
	if ($db->num_rows($achieved) > 0)
	{
		alert('danger',"Uh Oh!","You can only receive each achievement once.",true,'achievements.php');
		die($h->endpage());
	}
	$posts=$ir['vip_days'];
	if (empty($posts))
		$posts=0;
	if ($posts < $level)
	{
		alert('danger',"Uh Oh!","You do not have enough VIP Days to receive this award. You only have " . shortNumberParse($posts) . " VIP Days.",true,'achievements.php');
		die($h->endpage());
	}
	userCompleteAchievement($userid, $id);
	$api->SystemLogsAdd($userid,'achievement',"Received achievement for {$level} VIP Days.");
	$tokens=Random(25000,100000);
	$api->UserGiveCurrency($userid,'primary',$tokens);
	givePoint($userid);
	addToEconomyLog('Achievements', 'copper', $tokens);
	alert('success',"Success!","You have successfully achieved the " . shortNumberParse($level) . " VIP Days achievement and were rewarded " . shortNumberParse($tokens) . " Copper Coins.",true,'achievements.php');
	$h->endpage();
}
function labor($level,$id)
{
	global $db,$ir,$userid,$api,$h;
	$achieved=$db->query("/*qc=on*/SELECT * FROM `achievements_done` WHERE `userid` = {$userid} and `achievement` = {$id}");
	if ($db->num_rows($achieved) > 0)
	{
		alert('danger',"Uh Oh!","You can only receive each achievement once.",true,'achievements.php');
		die($h->endpage());
	}
	$posts=$ir['labor'];
	if (empty($posts))
		$posts=0;
	if ($posts < $level)
	{
		alert('danger',"Uh Oh!","You do not have enough labor to receive this award. You only have " . shortNumberParse($posts) . " labor.",true,'achievements.php');
		die($h->endpage());
	}
	userCompleteAchievement($userid, $id);
	$api->SystemLogsAdd($userid,'achievement',"Received achievement for {$level} labor.");
	$tokens=Random(25000,100000);
	$api->UserGiveCurrency($userid,'primary',$tokens);
	givePoint($userid);
	addToEconomyLog('Achievements', 'copper', $tokens);
	alert('success',"Success!","You have successfully achieved the " . shortNumberParse($level) . " labor achievement and were rewarded " . shortNumberParse($tokens) . " Copper Coins.",true,'achievements.php');
	$h->endpage();
}
function course($level,$id)
{
	global $db,$ir,$userid,$api,$h;
	$achieved=$db->query("/*qc=on*/SELECT * FROM `achievements_done` WHERE `userid` = {$userid} and `achievement` = {$id}");
	if ($db->num_rows($achieved) > 0)
	{
		alert('danger',"Uh Oh!","You can only receive each achievement once.",true,'achievements.php');
		die($h->endpage());
	}
    
	$posts=$db->fetch_single($db->query("/*qc=on*/SELECT COUNT(`userid`) FROM `academy_done` WHERE `userid` = {$userid}"));
	if (empty($posts))
		$posts=0;
	if ($posts < $level)
	{
		alert('danger',"Uh Oh!","You do not have enough completed courses to receive this award. You only have " . shortNumberParse($posts) . " courses completed.",true,'achievements.php');
		die($h->endpage());
	}
	userCompleteAchievement($userid, $id);
	$api->SystemLogsAdd($userid,'achievement',"Received achievement for {$level} completed courses.");
	$tokens=Random(25000,100000);
	$api->UserGiveCurrency($userid,'primary',$tokens);
	givePoint($userid);
	addToEconomyLog('Achievements', 'copper', $tokens);
	alert('success',"Success!","You have successfully achieved the " . shortNumberParse($level) . " courss completed achievement and were rewarded " . shortNumberParse($tokens) . " Copper Coins.",true,'achievements.php');
	$h->endpage();
}
function masterrank($level,$id)
{
	global $db,$ir,$userid,$api,$h;
	$achieved=$db->query("/*qc=on*/SELECT * FROM `achievements_done` WHERE `userid` = {$userid} and `achievement` = {$id}");
	if ($db->num_rows($achieved) > 0)
	{
		alert('danger',"Uh Oh!","You can only receive each achievement once.",true,'achievements.php');
		die($h->endpage());
	}
    
	$actualReset = $ir['reset'] - 1;
	if ($actualReset < $level)
	{
		alert('danger',"Uh Oh!","You need to be Mastery Rank " . shortNumberParse($level) . " before you can accept this reward.",true,'achievements.php');
		die($h->endpage());
	}
	userCompleteAchievement($userid, $id);
	$api->SystemLogsAdd($userid,'achievement',"Received achievement for Master Rank {$level}.");
	$tokens=Random(25000,100000);
	$api->UserGiveCurrency($userid,'primary',$tokens);
	givePoint($userid);
	addToEconomyLog('Achievements', 'copper', $tokens);
	alert('success',"Success!","You have successfully achieved the Mastery Rank " . shortNumberParse($level) . " achievement and were rewarded " . shortNumberParse($tokens) . " Copper Coins.",true,'achievements.php');
	$h->endpage();
}

/**
 * @param int $userid
 * @deprecated
 */
function givePoint($userid)
{
    giveUserSkillPoint($userid);
}
