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
	case 'skill30':
        skill(30,1);
        break;
	case 'skill33':
        skill(33,3);
        break;
	case 'skill36':
        skill(36,5);
        break;
    default:
        home();
        break;
}
function home()
{
    global $ir,$userid,$api;
    $loop=1;
    while ($loop != 39)
    {
        //$button[$loop] = (canGetSkill($loop)) ? "<a href='?action=skill{$loop}'>Unlock/Upgrade</a>" : "<span class='text-danger'>Locked</span>";
        $locked[$loop] = (getSkillLevel($userid,$loop) > 0) ? "class='table-active'" : "";
        if (getSkillLevel($userid,$loop) == returnMaxLevelSkill($loop))
            $button[$loop] = "<span class='text-danger'><i>Maxed</i></span>";
            elseif (!canGetSkill($loop))
            $button[$loop] = "<span class='text-danger'><b>Locked</b></span>";
            elseif (getSkillLevel($userid,$loop) == 0)
            $button[$loop] = "<a href='?action=skill{$loop}' class='text-success'>Unlock</a>";
            elseif (getSkillLevel($userid,$loop) > 0)
            $button[$loop] = "<a href='?action=skill{$loop}'>Upgrade</a>";
            else
                $button[$loop] = "<span class='text-muted'>N/A</span>";
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
            if ($ir['class'] == 'Warrior')
            {
                $statClass = 'strength';
                $weakStat = "guard";
            }
            if ($ir['class'] == 'Rogue')
            {
                $statClass = 'agility';
                $weakStat = "strength";
            }
            if ($ir['class'] == 'Guardian')
            {
                $statClass = 'guard';
                $weakStat = "agility";
            }
                echo "<div class='row'>
                    <div class='col-12 col-lg-6 col-xxl-4'>
                    <div class='card'>
                        <div class='card-body'>
                            <div class='row'>
                                <div class='col-12'>
                                    Perfection (1 Point)
                                </div>
                                <div class='col-12'>
                                    +3% {$statClass} effectiveness in combat per point.
                                </div>
                                <div class='col-12'>
                                    {$button['1']} (" . getSkillLevel($userid,1) . " / 5)
                                </div>
                            </div>
                        </div>
                    </div>
                    <br />
                    </div>
                    <div class='col-12 col-lg-6 col-xxl-4'>
                    <div class='card'>
                        <div class='card-body'>
                            <div class='row'>
                                <div class='col-12'>
                                    True Shot (3 Points)
                                </div>
                                <div class='col-12'>
                                    +5% damage increase with ranged weapons.
                                </div>
                                <div class='col-12'>
                                    {$button['4']} (" . getSkillLevel($userid,4) . " / 3)
                                </div>
                            </div>
                        </div>
                    </div>
                    <br />
                    </div>
                    <div class='col-12 col-lg-6 col-xxl-4'>
                    <div class='card'>
                        <div class='card-body'>
                            <div class='row'>
                                <div class='col-12'>
                                    Ammo Dispensery (5 Points)
                                </div>
                                <div class='col-12'>
                                    50% chance to not use ammo with ranged weapons.
                                </div>
                                <div class='col-12'>
                                    {$button['7']} (" . getSkillLevel($userid,7) . " / 1)
                                </div>
                            </div>
                        </div>
                    </div>
                    <br />
                    </div>
                </div>
                <div class='row'>
                    <div class='col-12 col-lg-6 col-xxl-4'>
                    <div class='card'>
                        <div class='card-body'>
                            <div class='row'>
                                <div class='col-12'>
                                    Conditioning (1 Point)
                                </div>
                                <div class='col-12'>
                                    +2.5% {$weakStat}, -1% {$statClass} while training, per point.
                                </div>
                                <div class='col-12'>
                                    {$button['2']} (" . getSkillLevel($userid,2) . " / 5)
                                </div>
                            </div>
                        </div>
                    </div>
                    <br />
                    </div>
                    <div class='col-12 col-lg-6 col-xxl-4'>
                    <div class='card'>
                        <div class='card-body'>
                            <div class='row'>
                                <div class='col-12'>
                                    Seasoned Warrior (3 Points)
                                </div>
                                <div class='col-12'>
                                    Increased experience gains while the Experience Coin is equipped.
                                </div>
                                <div class='col-12'>
                                    {$button['5']} (" . getSkillLevel($userid,5) . " / 3)
                                </div>
                            </div>
                        </div>
                    </div>
                    <br />
                    </div>
                    <div class='col-12 col-lg-6 col-xxl-4'>
                    <div class='card'>
                        <div class='card-body'>
                            <div class='row'>
                                <div class='col-12'>
                                    Optimized Training (5 Points)
                                </div>
                                <div class='col-12'>
                                    75% chance for Will not to be consumed, per energy point trained.
                                </div>
                                <div class='col-12'>
                                    {$button['8']} (" . getSkillLevel($userid,8) . " / 1)
                                </div>
                            </div>
                        </div>
                    </div>
                    <br />
                    </div>
                </div>
                <div class='row'>
                    <div class='col-12 col-lg-6 col-xxl-4'>
                    <div class='card'>
                        <div class='card-body'>
                            <div class='row'>
                                <div class='col-12'>
                                    Potent Potion (1 Point)
                                </div>
                                <div class='col-12'>
                                    +1.5% potion effectiveness when used in combat, per point.
                                </div>
                                <div class='col-12'>
                                    {$button['3']} (" . getSkillLevel($userid,3) . " / 5)
                                </div>
                            </div>
                        </div>
                    </div>
                    <br />
                    </div>
                    <div class='col-12 col-lg-6 col-xxl-4'>
                    <div class='card'>
                        <div class='card-body'>
                            <div class='row'>
                                <div class='col-8'>
                                    Thickened Skin (3 Points)
                                </div>
                                <div class='col-12'>
                                    +6.5% armor value, per point.
                                </div>
                                <div class='col-12'>
                                    {$button['6']} (" . getSkillLevel($userid,6) . " / 3)
                                </div>
                            </div>
                        </div>
                    </div>
                    <br />
                    </div>
                    <div class='col-12 col-lg-6 col-xxl-4'>
                    <div class='card'>
                        <div class='card-body'>
                            <div class='row'>
                                <div class='col-8'>
                                    Sharper Blades (5 Points)
                                </div>
                                <div class='col-12'>
                                    +20% Weapon value.
                                </div>
                                <div class='col-12'>
                                    {$button['9']} (" . getSkillLevel($userid,9) . " / 1)
                                </div>
                            </div>
                        </div>
                    </div>
                    <br />
                    </div>
                </div>";
            ?>
        </div>
        <div id="bartering" class="tab-pane">
            <?php
            echo "<div class='row'>
                    <div class='col-12 col-lg-6 col-xxl-4'>
                    <div class='card'>
                        <div class='card-body'>
                            <div class='row'>
                                <div class='col-12'>
                                    Token Hoarder (1 Point)
                                </div>
                                <div class='col-12'>
                                    +5 Chivalry Tokens from Hexbags/BOR per point.
                                </div>
                                <div class='col-12'>
                                    {$button['11']} (" . getSkillLevel($userid,11) . " / 5)
                                </div>
                            </div>
                        </div>
                    </div>
                    <br />
                    </div>
                    <div class='col-12 col-lg-6 col-xxl-4'>
                    <div class='card'>
                        <div class='card-body'>
                            <div class='row'>
                                <div class='col-12'>
                                    Thievery (3 Points)
                                </div>
                                <div class='col-12'>
                                    +5% Copper Coins stolen when you mob or rob a player.
                                </div>
                                <div class='col-12'>
                                    {$button['14']} (" . getSkillLevel($userid,14) . " / 3)
                                </div>
                            </div>
                        </div>
                    </div>
                    <br />
                    </div>
                    <div class='col-12 col-lg-6 col-xxl-4'>
                    <div class='card'>
                        <div class='card-body'>
                            <div class='row'>
                                <div class='col-12'>
                                    Sneaky Bastard (5 Points)
                                </div>
                                <div class='col-12'>
                                    +20% Criminal success rate.
                                </div>
                                <div class='col-12'>
                                    {$button['17']} (" . getSkillLevel($userid,17) . " / 1)
                                </div>
                            </div>
                        </div>
                    </div>
                    <br />
                    </div>
                </div>
                <div class='row'>
                    <div class='col-12 col-lg-6 col-xxl-4'>
                    <div class='card'>
                        <div class='card-body'>
                            <div class='row'>
                                <div class='col-12'>
                                    Deep Reading (1 Point)
                                </div>
                                <div class='col-12'>
                                    +5% IQ per Token from the Temple, per point.
                                </div>
                                <div class='col-12'>
                                    {$button['12']} (" . getSkillLevel($userid,12) . " / 5)
                                </div>
                            </div>
                        </div>
                    </div>
                    <br />
                    </div>
                    <div class='col-12 col-lg-6 col-xxl-4'>
                    <div class='card'>
                        <div class='card-body'>
                            <div class='row'>
                                <div class='col-12'>
                                    Intelligent Miner (3 Points)
                                </div>
                                <div class='col-12'>
                                    -10% Mine IQ Requirement, per point.
                                </div>
                                <div class='col-12'>
                                    {$button['15']} (" . getSkillLevel($userid,15) . " / 3)
                                </div>
                            </div>
                        </div>
                    </div>
                    <br />
                    </div>
                    <div class='col-12 col-lg-6 col-xxl-4'>
                    <div class='card'>
                        <div class='card-body'>
                            <div class='row'>
                                <div class='col-12'>
                                    Academic Potential (5 Points)
                                </div>
                                <div class='col-12'>
                                    For every 5,000 IQ you have, you decrease your course time by 1%, up to a maximum of 15%.
                                </div>
                                <div class='col-12'>
                                    {$button['18']} (" . getSkillLevel($userid,18) . " / 1)
                                </div>
                            </div>
                        </div>
                    </div>
                    <br />
                    </div>
                </div>
                <div class='row'>
                    <div class='col-12 col-lg-6 col-xxl-4'>
                    <div class='card'>
                        <div class='card-body'>
                            <div class='row'>
                                <div class='col-12'>
                                    Bargaining (1 Point)
                                </div>
                                <div class='col-12'>
                                    -5% Buy Price at local shops, per point.
                                </div>
                                <div class='col-12'>
                                    {$button['13']} (" . getSkillLevel($userid,13) . " / 5)
                                </div>
                            </div>
                        </div>
                    </div>
                    <br />
                    </div>
                    <div class='col-12 col-lg-6 col-xxl-4'>
                    <div class='card'>
                        <div class='card-body'>
                            <div class='row'>
                                <div class='col-8'>
                                    Scammer (3 Points)
                                </div>
                                <div class='col-12'>
                                    +2% sell value when selling item to the game, per point.
                                </div>
                                <div class='col-12'>
                                    {$button['16']} (" . getSkillLevel($userid,16) . " / 3)
                                </div>
                            </div>
                        </div>
                    </div>
                    <br />
                    </div>
                    <div class='col-12 col-lg-6 col-xxl-4'>
                    <div class='card'>
                        <div class='card-body'>
                            <div class='row'>
                                <div class='col-8'>
                                    Tax Free (5 Points)
                                </div>
                                <div class='col-12'>
                                    Removes the need to pay for Local Shop tax if you're in the same guild as the guild imposing the tax.
                                </div>
                                <div class='col-12'>
                                    {$button['19']} (" . getSkillLevel($userid,19) . " / 1)
                                </div>
                            </div>
                        </div>
                    </div>
                    <br />
                    </div>
                </div>";
            ?>
        </div>
        <div id="misc" class="tab-pane">
            <?php
            echo "<div class='row'>
                    <div class='col-12 col-lg-6 col-xxl-4'>
                    <div class='card'>
                        <div class='card-body'>
                            <div class='row'>
                                <div class='col-12'>
                                    Better Padding (1 Point)
                                </div>
                                <div class='col-12'>
                                    Increases chance of sleeping well with your spouse.
                                </div>
                                <div class='col-12'>
                                    {$button['21']} (" . getSkillLevel($userid,21) . " / 1)
                                </div>
                            </div>
                        </div>
                    </div>
                    <br />
                    </div>
                    <div class='col-12 col-lg-6 col-xxl-4'>
                    <div class='card'>
                        <div class='card-body'>
                            <div class='row'>
                                <div class='col-12'>
                                    Flirty Words (3 Points)
                                </div>
                                <div class='col-12'>
                                    2% chance sending a love letter will increase marriage happiness, per point.
                                </div>
                                <div class='col-12'>
                                    {$button['24']} (" . getSkillLevel($userid,24) . " / 3)
                                </div>
                            </div>
                        </div>
                    </div>
                    <br />
                    </div>
                    <div class='col-12 col-lg-6 col-xxl-4'>
                    <div class='card'>
                        <div class='card-body'>
                            <div class='row'>
                                <div class='col-12'>
                                    Enchanted Rings (5 Points)
                                </div>
                                <div class='col-12'>
                                    +10% ring effect when your spouse and you are online.
                                </div>
                                <div class='col-12'>
                                    {$button['27']} (" . getSkillLevel($userid,27) . " / 1)
                                </div>
                            </div>
                        </div>
                    </div>
                    <br />
                    </div>
                </div>
                <div class='row'>
                    <div class='col-12 col-lg-6 col-xxl-4'>
                    <div class='card'>
                        <div class='card-body'>
                            <div class='row'>
                                <div class='col-12'>
                                    Time Reduction (1 Point)
                                </div>
                                <div class='col-12'>
                                    -5% Dungeon and Infirmary time, per point.
                                </div>
                                <div class='col-12'>
                                    {$button['22']} (" . getSkillLevel($userid,22) . " / 5)
                                </div>
                            </div>
                        </div>
                    </div>
                    <br />
                    </div>
                    <div class='col-12 col-lg-6 col-xxl-4'>
                    <div class='card'>
                        <div class='card-body'>
                            <div class='row'>
                                <div class='col-12'>
                                    Item Potency (3 Points)
                                </div>
                                <div class='col-12'>
                                    +3% item effect increase, doesn't stack with Potent Potion
                                </div>
                                <div class='col-12'>
                                    {$button['25']} (" . getSkillLevel($userid,25) . " / 3)
                                </div>
                            </div>
                        </div>
                    </div>
                    <br />
                    </div>
                    <div class='col-12 col-lg-6 col-xxl-4'>
                    <div class='card'>
                        <div class='card-body'>
                            <div class='row'>
                                <div class='col-12'>
                                    Metabolism (5 Points)
                                </div>
                                <div class='col-12'>
                                    5% chance that eating food will also increase your energy.
                                </div>
                                <div class='col-12'>
                                    {$button['28']} (" . getSkillLevel($userid,28) . " / 1)
                                </div>
                            </div>
                        </div>
                    </div>
                    <br />
                    </div>
                </div>
                <div class='row'>
                    <div class='col-12 col-lg-6 col-xxl-4'>
                    <div class='card'>
                        <div class='card-body'>
                            <div class='row'>
                                <div class='col-12'>
                                    Overworked (1 Point)
                                </div>
                                <div class='col-12'>
                                    +5% gains when you train at the Chivalry Gym, per point.
                                </div>
                                <div class='col-12'>
                                    {$button['23']} (" . getSkillLevel($userid,23) . " / 5)
                                </div>
                            </div>
                        </div>
                    </div>
                    <br />
                    </div>
                    <div class='col-12 col-lg-6 col-xxl-4'>
                    <div class='card'>
                        <div class='card-body'>
                            <div class='row'>
                                <div class='col-8'>
                                    Lucky Day (3 Points)
                                </div>
                                <div class='col-12'>
                                    +1% minimum luck gained, per point.
                                </div>
                                <div class='col-12'>
                                    {$button['26']} (" . getSkillLevel($userid,26) . " / 3)
                                </div>
                            </div>
                        </div>
                    </div>
                    <br />
                    </div>
                    <div class='col-12 col-lg-6 col-xxl-4'>
                    <div class='card'>
                        <div class='card-body'>
                            <div class='row'>
                                <div class='col-8'>
                                    Gambling Man (5 Points)
                                </div>
                                <div class='col-12'>
                                    Increases max bet per hour by 25%.
                                </div>
                                <div class='col-12'>
                                    {$button['29']} (" . getSkillLevel($userid,29) . " / 1)
                                </div>
                            </div>
                        </div>
                    </div>
                    <br />
                    </div>
                    <div class='row'>
                    <div class='col-12 col-lg-6 col-xxl-4'>
                    <div class='card'>
                        <div class='card-body'>
                            <div class='row'>
                                <div class='col-12'>
                                    Well Capacity (1 Point)
                                </div>
                                <div class='col-12'>
                                    Receive an extra +5 well capacity each time your farming level increases, per point.
                                </div>
                                <div class='col-12'>
                                    {$button['30']} (" . getSkillLevel($userid,30) . " / 5)
                                </div>
                            </div>
                        </div>
                    </div>
                    <br />
                    </div>
                    <div class='col-12 col-lg-6 col-xxl-4'>
                    <div class='card'>
                        <div class='card-body'>
                            <div class='row'>
                                <div class='col-8'>
                                    Careful Tending (3 Points)
                                </div>
                                <div class='col-12'>
                                    Decreases maximum wellness change when interacting with plots by 25%.
                                </div>
                                <div class='col-12'>
                                    {$button['33']} (" . getSkillLevel($userid,33) . " / 3)
                                </div>
                            </div>
                        </div>
                    </div>
                    <br />
                    </div>
                    <div class='col-12 col-lg-6 col-xxl-4'>
                    <div class='card'>
                        <div class='card-body'>
                            <div class='row'>
                                <div class='col-8'>
                                    Crop Rotation (5 Points)
                                </div>
                                <div class='col-12'>
                                    -50% time required per plot stage.
                                </div>
                                <div class='col-12'>
                                    {$button['36']} (" . getSkillLevel($userid,36) . " / 1)
                                </div>
                            </div>
                        </div>
                    </div>
                    <br />
                    </div>
                </div>";
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
    $onepoint = array(1,2,3,11,12,13,22,23,30);
    $threepoint = array(4,5,6,14,15,16,24,25,26,33);
    $fivepoint = array(7,8,9,17,18,19,21,27,28,29,36);
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
        $alwaysbuy=array(1,2,3,11,12,13,21,22,23,30);
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
    if (isset($_POST['confirm']))
    {
        if ($ir['iq'] < 75000)
        {
            alert('danger',"Uh Oh!","You need at least 75,000 IQ to reset your skill tree.");
            die($h->endpage());
        }
        $q=$db->query("/*qc=on*/SELECT * FROM `user_skills` WHERE `userid` = {$userid}");
        if ($db->num_rows($q) == 0)
        {
            alert('danger',"Uh Oh!","You need to spend at least one skill point to be able to reset your skill tree.");
            die($h->endpage());
        }
		$well=5*getSkillLevel($userid,30);
		$db->query("UPDATE `farm_users` SET `farm_water_max` = `farm_water_max` - {$well} WHERE `userid` = {$userid}");
        $q2=$db->fetch_single($db->query("/*qc=on*/SELECT COUNT(`userid`) FROM `achievements_done` WHERE `userid` = {$userid}"));
        $db->query("DELETE FROM `user_skills` WHERE `userid` = {$userid}");
        $db->query("UPDATE `userstats` SET `iq` = `iq` - 75000 WHERE `userid` = {$userid}");
        $points=1+$q2;
        $db->query("UPDATE `user_settings` SET `skill_points` = {$points} WHERE `userid` = {$userid}");
        alert('success',"Success!","Your skill tree has been reset successfully. You now have {$points} to spend.",true,'skills.php');
    }
    else
    {
        echo "Are you sure you want to reset your skill tree? You will receive all your spent points back and 
        will be able to redistribute your points as you see fit. It will cost you 75,000 IQ.<br />
        <form method='post'>
            <input type='hidden' value='yes' name='confirm'>
            <input type='submit' value='Reset' class='btn btn-primary'>
        </form>
        > <a href='skills.php'>Go Back</a>";
    }
}
$h->endpage();