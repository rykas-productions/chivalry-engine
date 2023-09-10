<?php
require('globals.php');
if (!isset($_GET['action'])) {
    $_GET['action'] = '';
}
switch ($_GET['action']) {
    case 'reset':
        skill_reset();
        break;
    case 'buyskill':
        skill();
        break;
    case 'convertskill':
        convskill();
        break;
    default:
        home();
        break;
}
function home()
{
    global $ir,$userid,$api,$db;
    $q = $db->query("SELECT * FROM `user_skills_define` ORDER BY `skCost` ASC");
    alert('warning',"","Your skill tree is NOT permanent. You can pay 75,000 IQ to reset it at any time!", true, '?action=reset', "Reset Skills");
    echo "<div class='card'>
            <div class='card-header'>
                Skills " . createPrimaryBadge(shortNumberParse($ir['skill_points']) . " Skill Points") . "
            </div>
            <div class='card-body'>";
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
    while ($r = $db->fetch_row($q))
    {
        $currentSkillLevel = getUserSkill($userid, $r['skID']);
        $r['skDesc'] = str_ireplace(array("{BONUS}","{CLASS_STAT_WEAK}","{CLASS_STAT}"), array(shortNumberParse($r['skMultiplier']), $weakStat, $statClass), $r['skDesc']);
        if (!isset($skillBtn))
            $skillBtn = "";
            if (($currentSkillLevel > 0) && ($currentSkillLevel < $r['skMaxBuy']))
                $skillBtn = "<a href='?action=buyskill&id={$r['skID']}' class='btn btn-primary btn-block'>Upgrade Skill</a>";
                elseif ($r['skRequired'] > 0)
                {
                    if (getUserSkill($userid, $r['skRequired']) == 0)
                        $skillBtn = "<a href='#' class='btn btn-danger btn-block disabled'>Missing skill</a>";
                }
                elseif ($currentSkillLevel == 0)
                $skillBtn = "<a href='?action=buyskill&id={$r['skID']}' class='btn btn-success btn-block'>Unlock Skill</a>";
                elseif ($currentSkillLevel == $r['skMaxBuy'])
                $skillBtn = "<a href='#' class='btn btn-danger btn-block disabled'>Maxed Skill</a>";
                echo "  <div class='row'>
                            <div class='col-auto col-md-4 col-lg-3 col-xl-2'>
                                <div class='row'>
                                    <div class='col-12'>
            				            <small><b>Skill Name</b></small>
                                    </div>
                                    <div class='col-12'>
            				            {$r['skName']}
                                    </div>
                                </div>
                            </div>
                            <div class='col-auto col-md-8 col-lg'>
                                <div class='row'>
                                    <div class='col-12'>
            				            <small><b>Skill Information</b></small>
                                    </div>
                                    <div class='col-12'>
            				            <i>{$r['skDesc']}</i>
                                    </div>";
            				            if ($r['skRequired'] > 0)
            				            {
            				                echo"<div class='col-auto text-danger'>
                    				            <b>Skill Required:</b> " . getSkillName($r['skRequired']) . "
                                            </div>";
            				            }
            				            echo"
                                    <div class='col-auto'>
                                        Cost: " . shortNumberParse($r['skCost']) . " Skill Points
                                    </div>
                                </div>
                            </div>
                            <div class='col-auto col-sm col-lg-auto'>
                                <div class='row'>
                                    <div class='col-12'>
            				            <small><b>Skill Level</b></small>
                                    </div>
                                    <div class='col-12'>
            				            " . shortNumberParse($currentSkillLevel) . " / " . shortNumberParse($r['skMaxBuy']) . "
                                    </div>
                                </div>
                            </div>
                            <div class='col-auto col-sm col-lg-auto'>
                                <div class='row'>
                                    <div class='col-12'>
            				            <small><b>Link</b></small>
                                    </div>
                                    <div class='col-12'>
            				            {$skillBtn}
                                    </div>
                                </div>
                            </div>
                        </div>";
    }
    echo" </div>
    </div>";
}
function skill()
{
    global $db,$userid,$api,$h,$ir;
    $_GET['id'] = (isset($_GET['id']) && is_numeric($_GET['id'])) ? abs($_GET['id']) : 0;
    if (!isset($_GET['id']))
    {
        alert("danger", "Uh Oh!", "Please properly select the skill you wish to purchase.", true, 'index.php');
        die($h->endpage());
    }
    $q = $db->query("SELECT `skID` FROM `user_skills_define` WHERE `skID` = {$_GET['id']}");
    if ($db->num_rows($q) == 0)
    {
        alert('danger',"Uh Oh!","Non-existent skill.",true,'skills.php');
        die($h->endpage());
    }
    if (getUserSkill($userid, $_GET['id']) == getSkillMaxAllowed($_GET['id']))
    {
        alert('danger',"Uh Oh!","You already have maxed out this skill.",true,'skills.php');
        die($h->endpage());
    }
    if ($ir['skill_points'] < getSkillCost($_GET['id']))
    {
        alert('danger',"Uh Oh!","You do not have enough skill points to unlock this skill. You need " . shortNumberParse(getSkillCost($_GET['id'])) . " Skill Points, but only have " . shortNumberParse($ir['skill_points']) ." Skill Points.",true,'skills.php');
        die($h->endpage());
    }
    if (getSkillRequirement($_GET['id']) > 0)
    {
        if (getUserSkill($userid, getSkillRequirement($_GET['id'])) == 0)
        {
            alert('danger',"Uh Oh!","Please unlock the required skill before attempting to unlock this one.",true,'skills.php');
            die($h->endpage());
        }
    }
    purchaseSkill($userid, $_GET['id']);
    alert('success',"Success!","Skill point was spent successfully.",true,'skills.php');
    
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
        $well=5*getUserSkill($userid,30);
        $db->query("UPDATE `farm_users` SET `farm_water_max` = `farm_water_max` - {$well} WHERE `userid` = {$userid}");
        $q2=$db->fetch_single($db->query("/*qc=on*/SELECT COUNT(`userid`) FROM `achievements_done` WHERE `userid` = {$userid}"));
        $db->query("DELETE FROM `user_skills` WHERE `userid` = {$userid}");
        $db->query("UPDATE `userstats` SET `iq` = `iq` - 75000 WHERE `userid` = {$userid}");
        $points=1+$q2;
        $db->query("UPDATE `user_settings` SET `skill_points` = {$points} WHERE `userid` = {$userid}");
        alert('success',"Success!","You have spent 75K IQ and have successfully reset your Skill Tree. You now have  gained {$points} skill point.",true,'skills.php');
    }
    else
    {
        echo "<div class='card'>
            <div class='card-header'>
                Skill Tree Reset
            </div>
            <div class='card-body'>
                <div class='row'>
                    <div class='col-12'>
                        You may pay 75,000 IQ to reset your skill tree here. You may spend the gained points however you see fit.
                    </div>
                </div>
                <form method='post'>
                <div class='row'>
                    <div class='col-auto'>
                        <a href='skills.php' class='btn btn-danger btn-block'>Go Back</a>
                    </div>
                    <div class='col-auto'>
                        <input type='hidden' value='yes' name='confirm'>
                        <input type='submit' value='Reset Skills' class='btn btn-primary'>
                    </div>
                </div>
                </form>
            </div>
        </div>";
    }
}

function convskill()
{
    global $db, $userid, $h;
    if ($userid != 1)
    {
        alert('danger',"Uh Oh",'Go back... now....', true, 'skills.php');
        die($h->endpage());
    }
    else
    {
        $db->query("UPDATE `user_skills` SET `skill_id` = `skill_id` + 100 WHERE `skill_id` > 10");
        $db->query("UPDATE `user_skills` SET `skill_id` = 10 WHERE `skill_id` = 111");
        $db->query("UPDATE `user_skills` SET `skill_id` = 11 WHERE `skill_id` = 112");
        $db->query("UPDATE `user_skills` SET `skill_id` = 12 WHERE `skill_id` = 113");
        $db->query("UPDATE `user_skills` SET `skill_id` = 13 WHERE `skill_id` = 114");
        $db->query("UPDATE `user_skills` SET `skill_id` = 14 WHERE `skill_id` = 115");
        $db->query("UPDATE `user_skills` SET `skill_id` = 15 WHERE `skill_id` = 116");
        $db->query("UPDATE `user_skills` SET `skill_id` = 16 WHERE `skill_id` = 117");
        $db->query("UPDATE `user_skills` SET `skill_id` = 17 WHERE `skill_id` = 118");
        $db->query("UPDATE `user_skills` SET `skill_id` = 18 WHERE `skill_id` = 119");
        $db->query("UPDATE `user_skills` SET `skill_id` = 19 WHERE `skill_id` = 121");
        $db->query("UPDATE `user_skills` SET `skill_id` = 20 WHERE `skill_id` = 122");
        $db->query("UPDATE `user_skills` SET `skill_id` = 21 WHERE `skill_id` = 123");
        $db->query("UPDATE `user_skills` SET `skill_id` = 22 WHERE `skill_id` = 124");
        $db->query("UPDATE `user_skills` SET `skill_id` = 23 WHERE `skill_id` = 125");
        $db->query("UPDATE `user_skills` SET `skill_id` = 24 WHERE `skill_id` = 126");
        $db->query("UPDATE `user_skills` SET `skill_id` = 25 WHERE `skill_id` = 127");
        $db->query("UPDATE `user_skills` SET `skill_id` = 26 WHERE `skill_id` = 128");
        $db->query("UPDATE `user_skills` SET `skill_id` = 27 WHERE `skill_id` = 129");
        $db->query("UPDATE `user_skills` SET `skill_id` = 28 WHERE `skill_id` = 130");
        $db->query("UPDATE `user_skills` SET `skill_id` = 29 WHERE `skill_id` = 133");
        $db->query("UPDATE `user_skills` SET `skill_id` = 30 WHERE `skill_id` = 136");
        //$specialnumber = ((getUserSkill($userid, 12) * getSkillBonus(12)) / 100);
    }
}
$h->endpage();