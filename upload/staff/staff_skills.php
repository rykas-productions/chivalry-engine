<?php
require('sglobals.php');
if ($ir['user_level'] != "Admin") {
    alert('danger', "Uh Oh!", "You do not have permission to be here.", true, 'index.php');
    die($h->endpage());
}
if (!isset($_GET['action'])) {
    $_GET['action'] = '';
}
switch ($_GET['action']) {
    case "add":
        addskill();
        break;
    case "del":
        delskill();
        break;
    case "edit":
        editskill();
        break;
    default:
        echo "404";
        die($h->endpage());
        break;
}

function addskill()
{
    global $db, $h, $api, $userid;
    if (isset($_POST['skillname']))
    {
        if (!isset($_POST['verf']) || !verify_csrf_code('staff_new_skill', stripslashes($_POST['verf']))) 
        {
            alert('danger', "Action Blocked!", "Forms expire fairly quickly. Go back and submit it quicker!");
            die($h->endpage());
        }
        $name = (isset($_POST['skillname']) && is_string($_POST['skillname'])) ? stripslashes($_POST['skillname']) : '';
        $desc = (isset($_POST['skilldesc']) && is_string($_POST['skilldesc'])) ? stripslashes($_POST['skilldesc']) : '';
        $desc = $db->escape($desc);
        $cost = (isset($_POST['skillcost']) && is_numeric($_POST['skillcost'])) ? abs(intval($_POST['skillcost'])) : 0;
        $multi = (isset($_POST['skillmulti']) && is_numeric($_POST['skillmulti'])) ? abs(intval($_POST['skillmulti'])) : 0;
        $max = (isset($_POST['skillmax']) && is_numeric($_POST['skillmax'])) ? abs(intval($_POST['skillmax'])) : 0;
        $skillReqs = (isset($_POST['skill']) && is_numeric($_POST['skill'])) ? abs(intval($_POST['skill'])) : 0;
        if (empty($name))
        {
            alert('danger',"Uh Oh!","Please input a valid skill name.");
            die($h->endpage());
        }
        if (empty($desc))
        {
            alert('danger',"Uh Oh!","Please input a valid skill description.");
            die($h->endpage());
        }
        if (($cost == 0) || ($cost < 0) || (empty($cost)))
        {
            alert('danger',"Uh Oh!","Please input a valid skill cost. Skill cost must be greater than zero.");
            die($h->endpage());
        }
        if (($multi == 0) || ($multi < 0) || (empty($multi)))
        {
            alert('danger',"Uh Oh!","Please input a valid skill bonus. Skill bonus must be greater than zero.");
            die($h->endpage());
        }
        if (($max == 0) || ($max < 0) || (empty($max)))
        {
            alert('danger',"Uh Oh!","Please input a valid skill bonus. Skill bonus must be greater than zero.");
            die($h->endpage());
        }
        if ($skillReqs > 0)
        {
            $q = $db->query("SELECT `skID` from `user_skills_define` WHERE `skID` = {$skillReqs}");
            if ($db->num_rows($q) == 0)
            {
                alert('danger',"Uh Oh!","The chosen skill requirement does not exist or is invalid.");
                die($h->endpage());
            }
            $db->free_result($q);
        }
        $q = $db->query("SELECT `skID` FROM `user_skills_define` WHERE `skName` = '{$name}'");
        if ($db->num_rows($q) > 0)
        {
            alert('danger',"Uh Oh!","You cannot create a skill with the name of an already existant skill.");
            die($h->endpage());
        }
        $db->free_result($q);
        $db->query("INSERT INTO `user_skills_define` 
                    (`skName`, `skDesc`, `skCost`, `skMultiplier`, `skMaxBuy`, `skRequired`) 
                    VALUES 
                    ('{$name}', '{$desc}', '{$cost}', '{$multi}', '{$max}', '{$skillReqs}')");
        alert('success','Success!',"You've successfully created a new skill called {$name} for {$cost} skill points!",true,'index.php');
        $api->SystemLogsAdd($userid, 'staff', "Created new skill called {$name} | Cost {$cost}");
    }
    else
    {
        $csrf = request_csrf_html('staff_new_skill');
        echo "<form method='post'>
              <div class='card'>
                <div class='card-header'>
                    Creating a Skill
                </div>
                <div class='card-body'>
                    <div class='row'>
                        <div class='col-auto'>
                            <div class='row'>
                                <div class='col-12'>
                                    <small><b>Skill Name</b></small>
                                </div>
                                <div class='col-12'>
                                    <input type='text' name='skillname' required='1' class='form-control'>
                                </div>
                            </div>
                        </div>
                        <div class='col-auto'>
                            <div class='row'>
                                <div class='col-12'>
                                    <small><b>Skill Desc</b></small>
                                </div>
                                <div class='col-12'>
                                    <input type='text' name='skilldesc' required='1' class='form-control'>
                                </div>
                            </div>
                        </div>
                        <div class='col-auto'>
                            <div class='row'>
                                <div class='col-12'>
                                    <small><b>Skill Cost</b></small>
                                </div>
                                <div class='col-12'>
                                    <input type='number' value='1' min='1' max='255' name='skillcost' required='1' class='form-control'>
                                </div>
                            </div>
                        </div>
                        <div class='col-auto'>
                            <div class='row'>
                                <div class='col-12'>
                                    <small><b>Skill Bonus</b></small>
                                </div>
                                <div class='col-12'>
                                    <input type='number' value='1' min='1' max='1000000' name='skillmulti' required='1' class='form-control'>
                                </div>
                            </div>
                        </div>
                        <div class='col-auto'>
                            <div class='row'>
                                <div class='col-12'>
                                    <small><b>Skill Max</b></small>
                                </div>
                                <div class='col-12'>
                                    <input type='number' value='1' min='1' max='1000000' name='skillmax' required='1' class='form-control'>
                                </div>
                            </div>
                        </div>
                        <div class='col-auto'>
                            <div class='row'>
                                <div class='col-12'>
                                    <small><b>Skill Required</b></small>
                                </div>
                                <div class='col-12'>
                                    " . skills_dropdown() . "
                                </div>
                            </div>
                        </div>
                        <div class='col-auto'>
                            <div class='row'>
                                <div class='col-12'>
                                    <small><b></b></small>
                                </div>
                                <div class='col-12'>
                                    <input type='submit' class='btn btn-primary btn-block' value='Create Skill'>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>{$csrf}
            </form>";
    }
}