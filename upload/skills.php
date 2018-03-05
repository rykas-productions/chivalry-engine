<?php
require("globals.php");
if (!isset($_GET['action'])) {
    $_GET['action'] = '';
}
switch ($_GET['action']) {
    case 'reset':
        skill_reset();
        break;
	case 'skill1':
        skill(1,1);
        break;
	case 'skill2':
        skill(2,1);
        break;
	case 'skill3':
        skill(3,1);
        break;
	case 'skill4':
        skill(4,1);
        break;
	case 'skill5':
        skill(5,3);
        break;
	case 'skill6':
        skill(6,3);
        break;
	case 'skill7':
        skill(7,3);
        break;
	case 'skill8':
        skill(8,5);
        break;
	case 'skill9':
        skill(9,5);
        break;
	case 'skill10':
        skill(10,5);
        break;
	case 'skill11':
        skill(11,10);
        break;
    case 'skill12':
        skill(12,1);
        break;
    case 'skill13':
        skill(13,1);
        break;
    case 'skill14':
        skill(14,1);
        break;
	default:
        home();
        break;
}
function home()
{
	global $ir,$userid,$api;
	$loop=1;
	while ($loop != 15)
	{
		$button[$loop] = (canGetSkill($loop)) ? "<a href='?action=skill{$loop}'>Unlock/Upgrade</a>" : "<span class='text-danger'>Locked</span>";
		$locked[$loop] = (getSkillLevel($userid,$loop) > 0) ? "class='table-active'" : "";
		$loop=$loop+1;
	}
	echo "Here you may redeem your skill points for skills that will help you in-game. You can earn skill points by completing 
	<a href='achievements.php'>achievements</a>. You currently have {$ir['skill_points']} skill points.<br />
    [<a href='?action=reset'>Reset Tree</a>]";
	echo "<table class='table'>
	<tr>
		<td width='33%'>
		
		</td>
		<td width='33%' {$locked[1]}>
			{$ir['class']} Training (1 Point)<br />
			<small>Allows you to equip weapons specific to your class.</small><br />
			" . getSkillLevel($userid,1) . " / 1
			<br />
			{$button['1']}
		</td>
		<td width='33%'>
		
		</td>
	</tr>
	<tr>
		<td {$locked[2]}>
			Token Hoarder (1 Point)<br />
			<small>+2.5% Tokens from Hexbags/BOR<br /></small>
			" . getSkillLevel($userid,2) . " / 5
			<br />
			{$button['2']}
		</td>
		<td {$locked[3]}>
			Conditioning (1 Point)<br />
			<small>+2.5% Class Weak stat, -1% Class Strong Stat per level.<br /></small>
			" . getSkillLevel($userid,3) . " / 5
			<br />
			{$button['3']}
		</td>
		<td {$locked[4]}>
			Deep Reading (1 Point)<br />
			<small>+3% IQ from the temple, per level.<br /></small>
			" . getSkillLevel($userid,4) . " / 5
			<br />
			{$button['4']}
		</td>
	</tr>
	<tr>
		<td {$locked[5]}>
			Thievery (3 Points)<br />
			<small>+5% Copper stolen when you rob/mug per level.<br /></small>
			" . getSkillLevel($userid,5) . " / 3
			<br />
			{$button['5']}
		</td>
		<td {$locked[6]}>
			Perfection (3 Points)<br />
			<small>+3% Class Stat effectiveness in combat per level.<br /></small>
			" . getSkillLevel($userid,6) . " / 3
			<br />
			{$button['6']}
		</td>
		<td {$locked[7]}>
			Bargaining (3 Points)<br />
			<small>-5% lower pricing at Local Shops per level.<br /></small>
			" . getSkillLevel($userid,7) . " / 3
			<br />
			{$button['7']}
		</td>
	</tr>
	<tr>
		<td {$locked[8]}>
			Gambling Man (5 Points)<br />
			<small>+7.5% Max Bet while Gambling.<br /></small>
			" . getSkillLevel($userid,8) . " / 1
			<br />
			{$button['8']}
		</td>
		<td {$locked[9]}>
			Thickened Skin (5 Points)<br />
			<small>+7.5% armor value.<br /></small>
			" . getSkillLevel($userid,9) . " / 1
			<br />
			{$button['9']}
		</td>
		<td {$locked[10]}>
			Sneaky Bastard (5 Points)<br />
			<small>+10% Criminal success rate.<br /></small>
			" . getSkillLevel($userid,10) . " / 1
			<br />
			{$button['10']}
		</td>
	</tr>
	<tr>
		<td>
		
		</td>
		<td {$locked[11]}>
			Academic Potential (10 Points)<br />
			<small>For every 5,000 IQ you have, you decrease your course time by 1%, up to a maximum of 15%.</small><br />
			" . getSkillLevel($userid,11) . " / 1
			<br />
			{$button['11']}
		</td>
		<td>
		
		</td>
	</tr>
	<tr>
		<td {$locked[12]}>
			Overworked (1 Point)<br />
			<small>+3% Gains when you train Labor at the Chivalry Gym.<br /></small>
			" . getSkillLevel($userid,12) . " / 5
			<br />
			{$button['12']}
		</td>
		<td {$locked[13]}>
			Intelligent Miner (1 Point)<br />
			<small>-5% IQ Requirement while mining per level.<br /></small>
			" . getSkillLevel($userid,13) . " / 5
			<br />
			{$button['13']}
		</td>
		<td {$locked[14]}>
			Seasoned Warrior (1 Point)<br />
			<small>+1% Experience Gained while you hold the experience coin.<br /></small>
			" . getSkillLevel($userid,14) . " / 5
			<br />
			{$button['14']}
		</td>
	</tr>
	</table>";
}
function skill($id,$cost)
{
	global $db,$userid,$api,$h,$ir;
	if (getSkillLevel($userid,$id) == returnMaxLevelSkill($id))
	{
		alert('danger',"Uh Oh!","You already have maxed out this skill.",true,'skills.php');
		die($h->endpage());
	}
	if ($ir['skill_points'] < $cost)
	{
		alert('danger',"Uh Oh!","You do not have enough skill points to unlock this skill. You need {$cost}, but only have {$ir['skill_points']}.",true,'skills.php');
		die($h->endpage());
	}
	if (!canGetSkill($id))
	{
		alert('danger',"Uh Oh!","Please unlock the previous skill before attempting to unlock this one.",true,'skills.php');
		die($h->endpage());
	}
	giftSkill($id,$cost);
	alert('success',"Success!","Skill point was spent successfully.",true,'skills.php');
	
}
function returnMaxLevelSkill($skill)
{
	if ($skill == 1)
		return 1;
	if ($skill == 2)
		return 5;
	if ($skill == 3)
		return 5;
	if ($skill == 4)
		return 5;
	if ($skill == 5)
		return 3;
	if ($skill == 6)
		return 3;
	if ($skill == 7)
		return 3;
	if ($skill == 8)
		return 1;
	if ($skill == 9)
		return 1;
	if ($skill == 10)
		return 1;
	if ($skill == 11)
		return 1;
	if ($skill == 12)
		return 5;
	if ($skill == 13)
		return 5;
	if ($skill == 14)
		return 5;
}
function giftSkill($id,$cost)
{
	global $db,$userid;
	$q=$db->query("SELECT `skill_lvl` 
					FROM `user_skills` 
					WHERE `userid` = {$userid} 
					AND `skill_id` = {$id}");
	if ($db->num_rows($q) == 0)
	{
		$db->query("INSERT INTO `user_skills` 
					(`userid`, `skill_id`, `skill_lvl`) 
					VALUES 
					('{$userid}', '{$id}', '1')");
	}
	else
	{
		$db->query("UPDATE `user_skills` SET `skill_lvl` = `skill_lvl` + 1 WHERE `userid` = {$userid} AND `skill_id` = {$id}");
	}
	$db->query("UPDATE `user_settings` 
				SET `skill_points` = `skill_points` - {$cost} 
				WHERE `userid` = {$userid}");
}
function canGetSkill($id)
{
	global $db,$userid;
	if (getSkillLevel($userid,$id) != returnMaxLevelSkill($id))
	{
		$fixedid=$id-3;
		if ($id == 1)
		{
			return true;
		}
		elseif ($id == 11)
		{
			if ((getSkillLevel($userid,8) + getSkillLevel($userid,9) + getSkillLevel($userid,10)) >= 3)
				return true;
		}
		elseif ($id == 2 || $id == 3 || $id == 4)
		{
			return (getSkillLevel($userid,1) > 0);
		}
		elseif ($id == 12 || $id == 13 || $id == 14)
		{
			return (getSkillLevel($userid,11) > 0);
		}
		elseif (($id > 4) && ($id != 11))
		{
			return (getSkillLevel($userid,$fixedid) > 0);
		}
	}
	else
		return false;
}
function skill_reset()
{
    global $db,$ir,$api,$userid,$h;
    if ($ir['skillreset'] == 1)
    {
        alert('danger',"Uh Oh!","You may only reset your skill tree once for free.",true,'skills.php');
        die($h->endpage());
    }
    if (isset($_POST['confirm']))
    {
        if ($ir['labor'] < 25000)
        {
            alert('danger',"Uh Oh!","You need at least 25,000 labor to reset your skill tree.");
            die($h->endpage());
        }
        $q=$db->query("SELECT * FROM `user_skills` WHERE `userid` = {$userid}");
        if ($db->num_rows($q) == 0)
        {
            alert('danger',"Uh Oh!","You need to spend at least one skill point to be able to reset your skill tree.");
            die($h->endpage());
        }
        $q2=$db->fetch_single($db->query("SELECT COUNT(`userid`) FROM `achievements_done` WHERE `userid` = {$userid}"));
        $db->query("UPDATE `user_settings` SET `skill_points` = {$q2}, `skillreset` = `skillreset` + 1 WHERE `userid` = {$userid}");
        $db->query("DELETE FROM `user_skills` WHERE `userid` = {$userid}");
        $db->query("UPDATE `userstats` SET `labor` = `labor` - 25000 WHERE `userid` = {$userid}");
        alert('success',"Success!","Your skill tree has been reset successfully.",true,'skills.php');
    }
    else
    {
        echo "Are you sure you want to reset your skill tree? You will receive all your spent points back and 
        will be able to redistribute your points as you see fit. This can only be done once.  It will cost you 
        25,000 labor.<br />
        <form method='post'>
            <input type='hidden' value='yes' name='confirm'>
            <input type='submit' value='Reset' class='btn btn-primary'>
        </form>
        > <a href='skills.php'>Go Back</a>";
    }
}
$h->endpage();