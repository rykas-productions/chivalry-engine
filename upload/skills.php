<?php
require('globals.php');
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
        skill(4,3);
        break;
    case 'skill5':
        skill(5,3);
        break;
    case 'skill6':
        skill(6,3);
        break;
    case 'skill7':
        skill(7,5);
        break;
    case 'skill8':
        skill(8,5);
        break;
    case 'skill9':
        skill(9,5);
        break;
    case 'skill11':
        skill(11,1);
        break;
    case 'skill12':
        skill(12,1);
        break;
    case 'skill13':
        skill(13,1);
        break;
    case 'skill14':
        skill(14,3);
        break;
    case 'skill15':
        skill(15,3);
        break;
    case 'skill16':
        skill(16,3);
        break;
    case 'skill17':
        skill(17,5);
        break;
    case 'skill18':
        skill(18,5);
        break;
    case 'skill19':
        skill(19,5);
        break;
    case 'skill21':
        skill(21,1);
        break;
    case 'skill22':
        skill(22,1);
        break;
    case 'skill23':
        skill(23,1);
        break;
    case 'skill24':
        skill(24,3);
        break;
    case 'skill25':
        skill(25,3);
        break;
    case 'skill26':
        skill(26,3);
        break;
    case 'skill27':
        skill(27,5);
        break;
    case 'skill28':
        skill(28,5);
        break;
    case 'skill29':
        skill(29,5);
        break;
    default:
        home();
        break;
}
function home()
{
    global $ir,$userid,$api;
    $loop=1;
    while ($loop != 30)
    {
        $button[$loop] = (canGetSkill($loop)) ? "<a href='?action=skill{$loop}'>Unlock/Upgrade</a>" : "<span class='text-danger'>Locked</span>";
        $locked[$loop] = (getSkillLevel($userid,$loop) > 0) ? "class='table-active'" : "";
        $loop=$loop+1;
    }
    echo "Here you may redeem your skill points for skills that will help you in-game. You can earn skill points by completing
	<a href='achievements.php'>achievements</a>. You currently have {$ir['skill_points']} skill points.<br />
    [<a href='?action=reset'>Reset Tree</a>]";
    ?>
    <ul class="nav nav-tabs nav-justified">
        <li class="active nav-item">
            <a class='nav-link' data-toggle="tab" href="#combat">Combat</a>
        </li>
        <li class='nav-item'>
            <a class='nav-link' data-toggle="tab" href="#bartering">Bartering</a>
        </li>
        <li class='nav-item'>
            <a class='nav-link' data-toggle="tab" href="#misc">Misc</a>
        </li>
    </ul>
    <br />
    <div class="tab-content">
        <div id="combat" class="tab-pane active">
            <?php
                echo "<table class='table'>
                    <tr>
                        <td {$locked[1]} width='33%'>
                            Perfection (1 Point)<br />
                            <small>+3% Class Stat effectiveness in combat per level.<br /></small>
                            " . getSkillLevel($userid,1) . " / 5
                            <br />
                            {$button['1']}
                        </td>
                        <td {$locked[2]} width='33%'>
                            Conditioning (1 Point)<br />
                            <small>+2.5% Class Weak stat, -1% Class Strong Stat per level.<br /></small>
                            " . getSkillLevel($userid,2) . " / 5
                            <br />
                            {$button['2']}
                        </td>
                        <td {$locked[3]} width='33%'>
                            Potent Potion (1 Point)<br />
                            <small>+1.5% more potion ability when used in combat, per level.<br /></small>
                            " . getSkillLevel($userid,3) . " / 5
                            <br />
                            {$button['3']}
                        </td>
                    </tr>
                    <tr>
                        <td {$locked[4]}>
                            True Shot (3 Points)<br />
                            <small>+5% damage increase with ranged weapons.<br /></small>
                            " . getSkillLevel($userid,4) . " / 3
                            <br />
                            {$button['4']}
                        </td>
                        <td {$locked[5]}>
                            Seasoned Warrior (3 Points)<br />
                            <small>Increases experience gains when holding the Experience Coin.<br /></small>
                            " . getSkillLevel($userid,5) . " / 3
                            <br />
                            {$button['5']}
                        </td>
                        <td {$locked[6]}>
                            Thickened Skin (3 Points)<br />
                            <small>+6.5% Armor Value, per level.<br /></small>
                            " . getSkillLevel($userid,6) . " / 3
                            <br />
                            {$button['6']}
                        </td>
                    </tr>
                    <tr>
                        <td {$locked[7]}>
                            Ammo Dispensery (5 Points)<br />
                            <small>25% chance to not use ammo with ranged weapons.<br /></small>
                            " . getSkillLevel($userid,7) . " / 1
                            <br />
                            {$button['7']}
                        </td>
                        <td {$locked[8]}>
                            Optimized Training (5 Points)<br />
                            <small>Potential for less Will usage while training.<br /></small>
                            " . getSkillLevel($userid,8) . " / 1
                            <br />
                            {$button['8']}
                        </td>
                        <td {$locked[9]}>
                            Sharper Blades (5 Points)<br />
                            <small>+13% Weapon Value.<br /></small>
                            " . getSkillLevel($userid,9) . " / 1
                            <br />
                            {$button['9']}
                        </td>
                    </tr>
                </table>";
            ?>
        </div>
        <div id="bartering" class="tab-pane">
            <?php
                echo "<table class='table'>
                    <tr>
                        <td {$locked[11]} width='33%'>
                            Token Hoarder (1 Point)<br />
                            <small>+5% extra Chivalry Tokens gained with BOR/Hexbags, per level.<br /></small>
                            " . getSkillLevel($userid,11) . " / 5
                            <br />
                            {$button['11']}
                        </td>
                        <td {$locked[12]} width='33%'>
                            Deep Reading (1 Point)<br />
                            <small>+5% IQ purchased at Temple of Fortune, per level.<br /></small>
                            " . getSkillLevel($userid,12) . " / 5
                            <br />
                            {$button['12']}
                        </td>
                        <td {$locked[13]} width='33%'>
                            Bargaining (1 Point)<br />
                            <small>-5% prices at local shops.<br /></small>
                            " . getSkillLevel($userid,13) . " / 5
                            <br />
                            {$button['13']}
                        </td>
                    </tr>
                    <tr>
                        <td {$locked[14]}>
                            Thievery (3 Points)<br />
                            <small>+5% Copper Coins stolen when mug/rob, per level.<br /></small>
                            " . getSkillLevel($userid,14) . " / 3
                            <br />
                            {$button['14']}
                        </td>
                        <td {$locked[15]}>
                            Intelligent Miner (3 Points)<br />
                            <small>-10% IQ Requirement when mining, per level.<br /></small>
                            " . getSkillLevel($userid,15) . " / 3
                            <br />
                            {$button['15']}
                        </td>
                        <td {$locked[16]}>
                            Scammer (3 Points)<br />
                            <small>+2% price when selling items to the game, per level.<br /></small>
                            " . getSkillLevel($userid,16) . " / 3
                            <br />
                            {$button['16']}
                        </td>
                    </tr>
                    <tr>
                        <td {$locked[17]}>
                            Sneaky Bastard (5 Points)<br />
                            <small>+20% Criminal success rate.<br /></small>
                            " . getSkillLevel($userid,17) . " / 1
                            <br />
                            {$button['17']}
                        </td>
                        <td {$locked[18]}>
                            Academic Potential (5 Points)<br />
                            <small>For every 5,000 IQ you have, you decrease your course time by 1%, up to a maximum of 15%.<br /></small>
                            " . getSkillLevel($userid,18) . " / 1
                            <br />
                            {$button['18']}
                        </td>
                        <td {$locked[19]}>
                            Tax Free (5 Points)<br />
                            <small>Removes the need to pay for Local Shop tax if you're in the same guild as the guild imposing the tax.<br /></small>
                            " . getSkillLevel($userid,19) . " / 1
                            <br />
                            {$button['19']}
                        </td>
                    </tr>
                </table>";
            ?>
        </div>
        <div id="misc" class="tab-pane">
            <?php
                echo "<table class='table'>
                    <tr>
                        <td {$locked[21]} width='33%'>
                            Better Padding (1 Point)<br />
                            <small>Increases chance of sleeping well with your spouse.<br /></small>
                            " . getSkillLevel($userid,21) . " / 1
                            <br />
                            {$button['21']}
                        </td>
                        <td {$locked[22]} width='33%'>
                            Time Reduction (1 Point)<br />
                            <small>-5% Dungeon/Infirmary time, per level.<br /></small>
                            " . getSkillLevel($userid,22) . " / 5
                            <br />
                            {$button['22']}
                        </td>
                        <td {$locked[23]} width='33%'>
                            Overworked (1 Point)<br />
                            <small>+3% Gains when you train Labor at the Chivalry Gym, per level.<br /></small>
                            " . getSkillLevel($userid,23) . " / 5
                            <br />
                            {$button['23']}
                        </td>
                    </tr>
                    <tr>
                        <td {$locked[24]}>
                            Flirty Words (3 Points)<br />
                            <small>+2% chance that sending your spouse a love letter will increase marriage happiness, per level.<br /></small>
                            " . getSkillLevel($userid,24) . " / 3
                            <br />
                            {$button['24']}
                        </td>
                        <td {$locked[25]}>
                            Item Potency (3 Points)<br />
                            <small>+3% item effect increase. (Doesn't stack with Potent Potion)<br /></small>
                            " . getSkillLevel($userid,25) . " / 3
                            <br />
                            {$button['25']}
                        </td>
                        <td {$locked[26]}>
                            Lucky Day (3 Points)<br />
                            <small>+1% Minimum luck gained, per level.<br /></small>
                            " . getSkillLevel($userid,26) . " / 3
                            <br />
                            {$button['26']}
                        </td>
                    </tr>
                    <tr>
                        <td {$locked[27]}>
                            Enchanted Rings (5 Points)<br />
                            <small>+10% effect when you and your spouse are online.<br /></small>
                            " . getSkillLevel($userid,27) . " / 1
                            <br />
                            {$button['27']}
                        </td>
                        <td {$locked[28]}>
                            Metabolism (5 Points)<br />
                            <small>5% chance that eating food will increase your energy.<br /></small>
                            " . getSkillLevel($userid,28) . " / 1
                            <br />
                            {$button['28']}
                        </td>
                        <td {$locked[29]}>
                            Gambling Man (5 Points)<br />
                            <small>+25% Maximum bet while gambling.<br /></small>
                            " . getSkillLevel($userid,29) . " / 1
                            <br />
                            {$button['29']}
                        </td>
                    </tr>
                </table>";
            ?>
        </div>
    
    </div>
    <?php
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
    $onepoint = array(1,2,3,11,12,13,22,23);
    $threepoint = array(4,5,6,14,15,16,24,25,26);
    $fivepoint = array(7,8,9,17,18,19,21,27,28,29);
    if (in_array($skill,$onepoint))
        return 5;
    if (in_array($skill,$threepoint))
        return 3;
    if (in_array($skill,$fivepoint))
        return 1;
}
function giftSkill($id,$cost)
{
	global $db,$userid;
	$q=$db->query("/*qc=on*/SELECT `skill_lvl` 
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
        $alwaysbuy=array(1,2,3,11,12,13,21,22,23);
        if (in_array($id,$alwaysbuy))
			return true;
		else
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
        $q=$db->query("/*qc=on*/SELECT * FROM `user_skills` WHERE `userid` = {$userid}");
        if ($db->num_rows($q) == 0)
        {
            alert('danger',"Uh Oh!","You need to spend at least one skill point to be able to reset your skill tree.");
            die($h->endpage());
        }
        $q2=$db->fetch_single($db->query("/*qc=on*/SELECT COUNT(`userid`) FROM `achievements_done` WHERE `userid` = {$userid}"));
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