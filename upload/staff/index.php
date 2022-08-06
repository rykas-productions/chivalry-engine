<?php
/*	File:		index2.php
	Created: 	Jan 18, 2022; 10:30:47 PM
	Info: 		
	Author:		Ryan
	Website: 	https://chivalryisdeadgame.com/
*/
require('sglobals.php');
echo "<h2>Staff Panel Index</h2>";
//Start sys only stuff...
if ($api->UserMemberLevelGet($userid, 'admin'))
{
    $versq = $db->query("/*qc=on*/SELECT VERSION()");
    $MySQLIVersion = $db->fetch_single($versq);
    $db->free_result($versq);
    $debugMode = (DEBUG) ? "Enabled" : "Disabled";
    $devMode = (DEV) ? "Enabled" : "Disabled";
    echo "
    <div class='row'>
        <div class='col-12 col-lg-6 col-xxl-5 col-xxxl-4'>
            <div class='card bg-dark mb-3'>
                <div class='card-header'>
                    System Info
                </div>
                <div class='card-body'>
                    <div class='row'>
                        <div class='col-12 col-sm-6'>
                            <div class='row'>
                                <div class='col-12'>
                                    <small>PHP Version</small>
                                </div>
                                <div class='col-12'>
                                    " . phpversion() . "
                                </div>
                            </div>
                        </div>
                        <div class='col-12 col-sm-6'>
                            <div class='row'>
                                <div class='col-12'>
                                    <small>Database Version</small>
                                </div>
                                <div class='col-12'>
                                    " . $MySQLIVersion . "
                                </div>
                            </div>
                        </div>
                        <div class='col-12 col-sm-6'>
                            <div class='row'>
                                <div class='col-12'>
                                    <small>Apache Version</small>
                                </div>
                                <div class='col-12'>
                                    " . apache_get_version() . "
                                </div>
                            </div>
                        </div>
                        <div class='col-12 col-sm-6'>
                            <div class='row'>
                                <div class='col-12'>
                                    <small>Disk Free</small>
                                </div>
                                <div class='col-12'>
                                    " . numberToByteParse(disk_free_space("/")) . " / " . numberToByteParse(disk_total_space("/")) . "
                                </div>
                            </div>
                        </div>
                        <div class='col-12 col-sm-6'>
                            <div class='row'>
                                <div class='col-12'>
                                    <small>Bandwidth Transferred</small>
                                </div>
                                <div class='col-12'>
                                    " . numberToByteParse(returnVPSBandwidth()) . "
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <br />
        </div>
        <div class='col-12 col-lg-6 col-xxl-4 col-xxxl-4'>
            <div class='card bg-dark mb-3'>
                <div class='card-header'>
                    Chivalry Engine Info
                </div>
                <div class='card-body'>
                    <div class='row'>
                        <div class='col-12 col-sm-6 col-xxxl-4'>
                            <div class='row'>
                                <div class='col-12'>
                                    <small>API Version</small>
                                </div>
                                <div class='col-12'>
                                    {$api->SystemReturnAPIVersion()}
                                </div>
                            </div>
                        </div>
                        <div class='col-12 col-sm-6 col-xxxl-4'>
                            <div class='row'>
                                <div class='col-12'>
                                    <small>Chivalry Engine Version</small>
                                </div>
                                <div class='col-12'>
                                    {$set['Version_Number']}
                                </div>
                            </div>
                        </div>
                        <div class='col-12 col-sm-6 col-xxxl-4'>
                            <div class='row'>
                                <div class='col-12'>
                                    <small>Debug mode</small>
                                </div>
                                <div class='col-12'>
                                    {$debugMode}
                                </div>
                            </div>
                        </div>
                        <div class='col-12 col-sm-6 col-xxxl-4'>
                            <div class='row'>
                                <div class='col-12'>
                                    <small>Dev mode</small>
                                </div>
                                <div class='col-12'>
                                    {$devMode}
                                </div>
                            </div>
                        </div>
                        <div class='col-12'>
                            <div class='row'>
                                <div class='col-12'>
                                    <small>Engine Updates</small>
                                </div>
                                <div class='col-12'>
                                    " . version_json() . "
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <br />
        </div>
        <div class='col-12 col-lg-6 col-xxl-3 col-xxxl-4'>
            <div class='card bg-dark mb-3'>
                <div class='card-header'>
                    {$set['WebsiteName']} Details
                </div>
                <div class='card-body'>
                    <div class='row'>
                        <div class='col-12 col-sm-6'>
                            <div class='row'>
                                <div class='col-12'>
                                    <small>Moderation Logs</small>
                                </div>
                                <div class='col-12'>
                                    <a href='staff_moderation.php?action=listall'>" . number_format($db->fetch_single($db->query("SELECT COUNT(`mod_id`) FROM `staff_moderation_board`"))) . "</a>
                                </div>
                            </div>
                        </div>
                        <div class='col-12 col-sm-6'>
                            <div class='row'>
                                <div class='col-12'>
                                    <small>Monthly Income</small>
                                </div>
                                <div class='col-12'>
                                    \${$set['MonthlyDonationGoal']}
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <br />
        </div>
    </div>
    <hr />";
}
//end sys only stuff...
//begin admin only stuff
echo "<div class='row'>";
if ($api->UserMemberLevelGet($userid, 'admin'))
{
    echo "<div class='col-12 col-lg-6 col-xxl-4 col-xxxl-2'>
            <div class='card'>
                <div class='card-header'>
                    Admin Options
                </div>
                <div class='card-body'>
                    <div class='row'>
                        <div class='col-12 col-sm-6 col-md-4 col-lg-6 col-xxxl-12'>
                            <a href='staff_settings.php?action=basicset' class='btn btn-primary btn-block'>Game Settings</a><br />
                        </div>
                        <div class='col-12 col-sm-6 col-md-4 col-lg-6 col-xxxl-12'>
                            <a href='staff_settings.php?action=announce' class='btn btn-primary btn-block'>Create Announcement</a><br />
                        </div>
                        <div class='col-12 col-sm-6 col-md-4 col-lg-6 col-xxxl-12'>
                            <a href='staff_settings.php?action=diagnostics' class='btn btn-primary btn-block'>Game Diagnostics</a><br />
                        </div>
                    </div>
                </div>
            </div>
            <br />
         </div>
        <div class='col-12 col-lg-6 col-xxl-4 col-xxxl-2'>
            <div class='card'>
                <div class='card-header'>
                    Game Rules
                </div>
                <div class='card-body'>
                    <div class='row'>
                        <div class='col-12 col-sm-6 col-md-4 col-lg-6 col-xxxl-12'>
                            <a href='staff_rules.php?action=addrule' class='btn btn-success btn-block'>Create Rule</a><br />
                        </div>
                        <div class='col-12 col-sm-6 col-md-4 col-lg-6 col-xxxl-12'>
                            <a href='staff_rules.php?action=editrule' class='btn btn-primary btn-block disabled'>Edit Rule</a><br />
                        </div>
                        <div class='col-12 col-sm-6 col-md-4 col-lg-6 col-xxxl-12'>
                            <a href='staff_rules.php?action=delrule' class='btn btn-danger btn-block disabled'>Delete Rule</a><br />
                        </div>
                    </div>
                </div>
            </div>
            <br />
         </div>
         <div class='col-12 col-lg-6 col-xxl-4 col-xxxl-2'>
            <div class='card'>
                <div class='card-header'>
                    VIP Packs
                </div>
                <div class='card-body'>
                    <div class='row'>
                        <div class='col-12 col-sm-6 col-md-4 col-lg-6 col-xxxl-12'>
                            <a href='staff_donate.php?action=addpack' class='btn btn-success btn-block'>Create VIP Pack</a><br />
                        </div>
                        <div class='col-12 col-sm-6 col-md-4 col-lg-6 col-xxxl-12'>
                            <a href='staff_donate.php?action=editpack' class='btn btn-primary btn-block'>Edit VIP Pack</a><br />
                        </div>
                        <div class='col-12 col-sm-6 col-md-4 col-lg-6 col-xxxl-12'>
                            <a href='staff_donate.php?action=delpack' class='btn btn-danger btn-block'>Delete VIP Pack</a><br />
                        </div>
                    </div>
                </div>
            </div>
            <br />
         </div>
        <div class='col-12 col-lg-6 col-xxl-4 col-xxxl-2'>
            <div class='card'>
                <div class='card-header'>
                    Promo Codes
                </div>
                <div class='card-body'>
                    <div class='row'>
                        <div class='col-12 col-sm-6 col-md-4 col-lg-6 col-xxxl-12'>
                            <a href='staff_promo.php?action=addpromo' class='btn btn-success btn-block'>Create Promo Code</a><br />
                        </div>
                        <div class='col-12 col-sm-6 col-md-4 col-lg-6 col-xxxl-12'>
                            <a href='staff_promo.php?action=viewpromo' class='btn btn-primary btn-block'>View Promo Codes</a><br />
                        </div>
                    </div>
                </div>
            </div>
            <br />
         </div>
         <div class='col-12 col-lg-6 col-xxl-4'>
            <div class='card'>
                <div class='card-header'>
                    Criminal
                </div>
                <div class='card-body'>
                    <div class='row'>
                        <div class='col-12 col-sm-6 col-md-4 col-lg-6'>
                            <a href='staff_criminal.php??action=newcrimegroup' class='btn btn-success btn-block'>Create Crime Group</a><br />
                        </div>
                        <div class='col-12 col-sm-6 col-md-4 col-lg-6'>
                            <a href='staff_criminal.php?action=newcrime' class='btn btn-success btn-block'>Create Crime</a><br />
                        </div>
                        <div class='col-12 col-sm-6 col-md-4 col-lg-6'>
                            <a href='staff_criminal.php?action=editcrime' class='btn btn-primary btn-block'>Edit Crime</a><br />
                        </div>
                        <div class='col-12 col-sm-6 col-md-4 col-lg-6'>
                            <a href='staff_criminal.php?action=delcrime' class='btn btn-danger btn-block'>Delete Crime</a><br />
                        </div>
                        <div class='col-12 col-sm-6 col-md-4 col-lg-6'>
                            <a href='staff_criminal.php?action=editcrimegroup' class='btn btn-primary btn-block'>Edit Crime Group</a><br />
                        </div>
                        <div class='col-12 col-sm-6 col-md-4 col-lg-6'>
                            <a href='staff_criminal.php?action=delcrimegroup' class='btn btn-danger btn-block'>Delete Crime Group</a><br />
                        </div>
                    </div>
                </div>
            </div>
            <br />
         </div>
         <div class='col-12 col-lg-6 col-xxl-4 col-xxxl-2'>
            <div class='card'>
                <div class='card-header'>
                    Item Shops
                </div>
                <div class='card-body'>
                    <div class='row'>
                        <div class='col-12 col-sm-6 col-md-4 col-lg-6 col-xxxl-12'>
                            <a href='staff_shops.php?action=newshop' class='btn btn-success btn-block'>Create Shop</a><br />
                        </div>
                        <div class='col-12 col-sm-6 col-md-4 col-lg-6 col-xxxl-12'>
                            <a href='staff_shops.php?action=newitem' class='btn btn-primary btn-block'>Add Stock</a><br />
                        </div>
                        <div class='col-12 col-sm-6 col-md-4 col-lg-6 col-xxxl-12'>
                            <a href='staff_shops.php?action=delshop' class='btn btn-danger btn-block'>Delete Shop</a><br />
                        </div>
                    </div>
                </div>
            </div>
            <br />
         </div>
        <div class='col-12 col-lg-6 col-xxl-4 col-xxxl-2'>
            <div class='card'>
                <div class='card-header'>
                    NPCs
                </div>
                <div class='card-body'>
                    <div class='row'>
                        <div class='col-12 col-sm-6 col-md-4 col-lg-6'>
                            <a href='staff_bots.php?action=addbot' class='btn btn-success btn-block'>Add Bot</a><br />
                        </div>
                        <div class='col-12 col-sm-6 col-md-4 col-lg-6'>
                            <a href='staff_bots.php?action=delbot' class='btn btn-danger btn-block'>Delete Bot</a><br />
                        </div>
                        <div class='col-12 col-sm-6 col-md-4 col-lg-6 col-xxxl-12'>
                            <a href='staff_boss.php?action=addboss' class='btn btn-success btn-block'>Add Boss</a><br />
                        </div>
                        <div class='col-12 col-sm-6 col-md-4 col-lg-6 col-xxxl-12'>
                            <a href='staff_boss.php?action=delboss' class='btn btn-danger btn-block'>Delete Boss</a><br />
                        </div>
                    </div>
                </div>
            </div>
            <br />
         </div>
        <div class='col-12 col-lg-6 col-xxl-4 col-xxxl-2'>
            <div class='card'>
                <div class='card-header'>
                    Towns
                </div>
                <div class='card-body'>
                    <div class='row'>
                        <div class='col-12 col-sm-6 col-md-4 col-lg-6 col-xxxl-12'>
                            <a href='staff_towns.php?action=addtown' class='btn btn-success btn-block'>Create Town</a><br />
                        </div>
                        <div class='col-12 col-sm-6 col-md-4 col-lg-6 col-xxxl-12'>
                            <a href='staff_towns.php?action=edittown' class='btn btn-primary btn-block'>Edit Town</a><br />
                        </div>
                        <div class='col-12 col-sm-6 col-md-4 col-lg-6 col-xxxl-12'>
                            <a href='staff_towns.php?action=deltown' class='btn btn-danger btn-block'>Delete Town</a><br />
                        </div>
                    </div>
                </div>
            </div>
            <br />
         </div>
        <div class='col-12 col-lg-6 col-xxl-4 col-xxxl-2'>
            <div class='card'>
                <div class='card-header'>
                    Academy
                </div>
                <div class='card-body'>
                    <div class='row'>
                        <div class='col-12 col-sm-6 col-md-4 col-lg-6 col-xxxl-12'>
                            <a href='staff_academy.php?action=add' class='btn btn-success btn-block'>Create Course</a><br />
                        </div>
                        <div class='col-12 col-sm-6 col-md-4 col-lg-6 col-xxxl-12'>
                            <a href='staff_academy.php?action=edit' class='btn btn-primary btn-block'>Edit Course</a><br />
                        </div>
                        <div class='col-12 col-sm-6 col-md-4 col-lg-6 col-xxxl-12'>
                            <a href='staff_academy.php?action=del' class='btn btn-danger btn-block'>Delete Course</a><br />
                        </div>
                    </div>
                </div>
            </div>
            <br />
         </div>
        <div class='col-12 col-lg-6 col-xxl-4'>
            <div class='card'>
                <div class='card-header'>
                    Jobs
                </div>
                <div class='card-body'>
                    <div class='row'>
                        <div class='col-12 col-sm-6 col-md-4 col-lg-6'>
                            <a href='staff_jobs.php?action=newjob' class='btn btn-success btn-block'>Create Job</a><br />
                        </div>
                        <div class='col-12 col-sm-6 col-md-4 col-lg-6'>
                            <a href='staff_jobs.php?action=newjobrank' class='btn btn-success btn-block'>Create Job Rank</a><br />
                        </div>
                        <div class='col-12 col-sm-6 col-md-4 col-lg-6'>
                            <a href='staff_jobs.php?action=jobedit' class='btn btn-primary btn-block'>Edit Job</a><br />
                        </div>
                        <div class='col-12 col-sm-6 col-md-4 col-lg-6'>
                            <a href='staff_jobs.php?action=jobdele' class='btn btn-danger btn-block'>Delete Job</a><br />
                        </div>
                        <div class='col-12 col-sm-6 col-md-4 col-lg-6'>
                            <a href='staff_jobs.php?action=jobrankedit' class='btn btn-primary btn-block'>Edit Job Rank</a><br />
                        </div>
                        <div class='col-12 col-sm-6 col-md-4 col-lg-6'>
                            <a href='staff_jobs.php?action=jobrankdele' class='btn btn-danger btn-block'>Delete Job Rank</a><br />
                        </div>
                    </div>
                </div>
            </div>
            <br />
         </div>
         <div class='col-12 col-lg-6 col-xxl-4 col-xxxl-2'>
            <div class='card'>
                <div class='card-header'>
                    Estates / Properties
                </div>
                <div class='card-body'>
                    <div class='row'>
                        <div class='col-12 col-sm-6 col-md-4 col-lg-6 col-xxxl-12'>
                            <a href='staff_estates.php?action=addestate' class='btn btn-success btn-block'>Create Estate</a><br />
                        </div>
                        <div class='col-12 col-sm-6 col-md-4 col-lg-6 col-xxxl-12'>
                            <a href='staff_estates.php?action=editestate' class='btn btn-primary btn-block'>Edit Estate</a><br />
                        </div>
                        <div class='col-12 col-sm-6 col-md-4 col-lg-6 col-xxxl-12'>
                            <a href='staff_estates.php?action=delestate' class='btn btn-danger btn-block'>Delete Estate</a><br />
                        </div>
                    </div>
                </div>
            </div>
            <br />
         </div>
        <div class='col-12 col-lg-6 col-xxl-4 col-xxxl-2'>
            <div class='card'>
                <div class='card-header'>
                    Mining
                </div>
                <div class='card-body'>
                    <div class='row'>
                        <div class='col-12 col-sm-6 col-md-4 col-lg-6 col-xxxl-12'>
                            <a href='staff_mine.php?action=addmine' class='btn btn-success btn-block'>Create Mine</a><br />
                        </div>
                        <div class='col-12 col-sm-6 col-md-4 col-lg-6 col-xxxl-12'>
                            <a href='staff_mine.php?action=editmine' class='btn btn-primary btn-block'>Edit Mine</a><br />
                        </div>
                        <div class='col-12 col-sm-6 col-md-4 col-lg-6 col-xxxl-12'>
                            <a href='staff_mine.php?action=delmine' class='btn btn-danger btn-block'>Delete Mine</a><br />
                        </div>
                    </div>
                </div>
            </div>
            <br />
         </div>
        <div class='col-12 col-lg-6 col-xxl-4 col-xxxl-2'>
            <div class='card'>
                <div class='card-header'>
                    Blacksmith Smeltery
                </div>
                <div class='card-body'>
                    <div class='row'>
                        <div class='col-12 col-sm-6 col-md-4 col-lg-6 col-xxxl-12'>
                            <a href='staff_smelt.php?action=add' class='btn btn-success btn-block'>Create Recipe</a><br />
                        </div>
                        <div class='col-12 col-sm-6 col-md-4 col-lg-6 col-xxxl-12'>
                            <a href='staff_smelt.php?action=del' class='btn btn-danger btn-block'>Delete Recipe</a><br />
                        </div>
                    </div>
                </div>
            </div>
            <br />
         </div>
        <div class='col-12 col-lg-6 col-xxl-4 col-xxxl-2'>
            <div class='card'>
                <div class='card-header'>
                    Farming
                </div>
                <div class='card-body'>
                    <div class='row'>
                        <div class='col-12 col-sm-6 col-md-4 col-lg-6 col-xxxl-12'>
                            <a href='../farm.php?action=createseed' class='btn btn-success btn-block'>Create Seed/Crop</a><br />
                        </div>
                        <div class='col-12 col-sm-6 col-md-4 col-lg-6 col-xxxl-12'>
                            <a href='../farm.php?action=editseed' class='btn btn-primary btn-block'>Edit Seed/Crop</a><br />
                        </div>
                        <div class='col-12 col-sm-6 col-md-4 col-lg-6 col-xxxl-12'>
                            <a href='../farm.php?action=delseed' class='btn btn-danger btn-block disabled'>Delete Seed/Crop</a><br />
                        </div>
                    </div>
                </div>
            </div>
            <br />
         </div>";
}
//end admin only stuff
//start assistant only stuff
if ($api->UserMemberLevelGet($userid, 'assistant'))
{
    echo "<div class='col-12 col-lg-6 col-xxl-4'>
            <div class='card'>
                <div class='card-header'>
                    Items
                </div>
                <div class='card-body'>
                    <div class='row'>";
                        //admin only item features...
                        if ($api->UserMemberLevelGet($userid, 'admin'))
                        {
                            echo "<div class='col-12 col-sm-6 col-md-4 col-lg-6'>
                                    <a href='staff_items.php?action=createitmgroup' class='btn btn-success btn-block'>Create Item Group</a><br />
                                </div>
                                <div class='col-12 col-sm-6 col-md-4 col-lg-6'>
                                    <a href='staff_items.php?action=create' class='btn btn-success btn-block'>Create Item</a><br />
                                </div>
                                <div class='col-12 col-sm-6 col-md-4 col-lg-6'>
                                    <a href='staff_items.php?action=edit' class='btn btn-primary btn-block'>Edit Item</a><br />
                                </div>
                                <div class='col-12 col-sm-6 col-md-4 col-lg-6'>
                                    <a href='staff_items.php?action=delete' class='btn btn-danger btn-block'>Delete Item</a><br />
                                </div>
                                <div class='col-12 col-sm-6 col-md-4 col-lg-6'>
                                    <a href='staff_items.php?action=edititmgroup' class='btn btn-primary btn-block disabled'>Edit Item Group</a><br />
                                </div>
                                <div class='col-12 col-sm-6 col-md-4 col-lg-6'>
                                    <a href='staff_items.php?action=deleteitmgroup' class='btn btn-danger btn-block disabled'>Delete Item Group</a><br />
                                </div>";
                        }
                        echo"
                        <div class='col-12 col-sm-6 col-md-4 col-lg-6'>
                            <a href='staff_items.php?action=giveitem' class='btn btn-primary btn-block'>Gift Item</a><br />
                        </div>
                    </div>
                </div>
            </div>
            <br />
         </div>
        <div class='col-12 col-lg-6 col-xxl-4 col-xxxl-2'>
            <div class='card'>
                <div class='card-header'>
                    Game Polls
                </div>
                <div class='card-body'>
                    <div class='row'>
                        <div class='col-12 col-sm-6 col-md-4 col-lg-6 col-xxxl-12'>
                            <a href='staff_polling.php?action=addpoll' class='btn btn-success btn-block'>Create Poll</a><br />
                        </div>
                        <div class='col-12 col-sm-6 col-md-4 col-lg-6 col-xxxl-12'>
                            <a href='staff_polling.php?action=closepoll' class='btn btn-danger btn-block'>End Poll</a><br />
                        </div>
                    </div>
                </div>
            </div>
            <br />
         </div>
        <div class='col-12 col-lg-6 col-xxl-6'>
            <div class='card'>
                <div class='card-header'>
                    Users
                </div>
                <div class='card-body'>
                    <div class='row'>";
                        //admin only item features...
                        if ($api->UserMemberLevelGet($userid, 'admin'))
                        {
                            echo "<div class='col-12 col-sm-6 col-md-4 col-lg-6 col-xxl-3'>
                                    <a href='staff_users.php?action=createuser' class='btn btn-success btn-block'>Create User</a><br />
                                </div>
                                <div class='col-12 col-sm-6 col-md-4 col-lg-6 col-xxl-3'>
                                    <a href='staff_users.php?action=edituser' class='btn btn-primary btn-block'>Edit User</a><br />
                                </div>
                                <div class='col-12 col-sm-6 col-md-4 col-lg-6 col-xxl-3'>
                                    <a href='staff_users.php?action=deleteuser' class='btn btn-danger btn-block'>Delete User</a><br />
                                </div>
                                <div class='col-12 col-sm-6 col-md-4 col-lg-6 col-xxl-3'>
                                    <a href='staff_users.php?action=changepw' class='btn btn-primary btn-block'>Change User PW</a><br />
                                </div>
                                <div class='col-12 col-sm-6 col-md-4 col-lg-6 col-xxl-3'>
                                    <a href='staff_settings.php?action=staff' class='btn btn-primary btn-block'>Set Account Level</a><br />
                                </div>";
                        }
                        echo"
                        <div class='col-12 col-sm-6 col-md-4 col-lg-6 col-xxl-3'>
                            <a href='staff_settings.php?action=restore' class='btn btn-primary btn-block'>Restore Stats</a><br />
                        </div>
                        <div class='col-12 col-sm-6 col-md-4 col-lg-6 col-xxl-3'>
                            <a href='staff_users.php?action=masspayment' class='btn btn-primary btn-block'>Mass Payment</a><br />
                        </div>
                        <div class='col-12 col-sm-6 col-md-4 col-lg-6 col-xxl-3'>
                            <a href='staff_settings.php?action=restore' class='btn btn-dark btn-block'>Player Reports</a><br />
                        </div>
                        <div class='col-12 col-sm-6 col-md-4 col-lg-6 col-xxl-3'>
                            <a href='staff_users.php?action=logout' class='btn btn-danger btn-block'>Force Logout</a><br />
                        </div>
                        <div class='col-12 col-sm-6 col-md-4 col-lg-6 col-xxl-3'>
                            <a href='staff_users.php?action=forcelogin' class='btn btn-light btn-block'>Account Control</a><br />
                        </div>
                    </div>
                </div>
            </div>
            <br />
         </div>
        <div class='col-12 col-lg-6 col-xxl-4 col-xxxl-2'>
            <div class='card'>
                <div class='card-header'>
                    Permissions
                </div>
                <div class='card-body'>
                    <div class='row'>
                        <div class='col-12 col-sm-6 col-md-4 col-lg-6 col-xxxl-12'>
                            <a href='staff_perms.php?action=viewperm' class='btn btn-success btn-block'>View Permissions</a><br />
                        </div>
                        <div class='col-12 col-sm-6 col-md-4 col-lg-6 col-xxxl-12'>
                            <a href='staff_perms.php?action=editperm' class='btn btn-primary btn-block'>Edit Permissions</a><br />
                        </div>
                        <div class='col-12 col-sm-6 col-md-4 col-lg-6 col-xxxl-12'>
                            <a href='staff_perms.php?action=resetperm' class='btn btn-danger btn-block'>Reset Permissions</a><br />
                        </div>
                    </div>
                </div>
            </div>
            <br />
         </div>
         <div class='col-12 col-lg-6 col-xxl-4 col-xxxl-2'>
            <div class='card'>
                <div class='card-header'>
                    Guilds
                </div>
                <div class='card-body'>
                    <div class='row'>
                        <div class='col-12 col-sm-6 col-md-4 col-lg-6 col-xxxl-12'>
                            <a href='staff_guilds.php?action=viewguild' class='btn btn-success btn-block'>View Guild</a><br />
                        </div>
                        <div class='col-12 col-sm-6 col-md-4 col-lg-6 col-xxxl-12'>
                            <a href='staff_guilds.php?action=creditguild' class='btn btn-primary btn-block'>Credit Guild</a><br />
                        </div>
                        <div class='col-12 col-sm-6 col-md-4 col-lg-6 col-xxxl-12'>
                            <a href='staff_guilds.php?action=viewwars' class='btn btn-danger btn-block'>View Wars</a><br />
                        </div>
                        <div class='col-12 col-sm-6 col-md-4 col-lg-6 col-xxxl-12'>
                            <a href='staff_guilds.php?action=editguild' class='btn btn-success btn-block'>Edit Guild</a><br />
                        </div>
                    </div>
                </div>
            </div>
            <br />
         </div>";
}
//end assistant only functions
//start all other staff stuff
echo "<div class='col-12 col-lg-6 col-xxl-4 col-xxxl-2'>
            <div class='card'>
                <div class='card-header'>
                    Forums
                </div>
                <div class='card-body'>
                    <div class='row'>
                        <div class='col-12 col-sm-6 col-md-4 col-lg-6 col-xxxl-12'>
                            <a href='staff_forums.php?action=addforum' class='btn btn-success btn-block'>Add Category</a><br />
                        </div>
                        <div class='col-12 col-sm-6 col-md-4 col-lg-6 col-xxxl-12'>
                            <a href='staff_forums.php?action=editforum' class='btn btn-primary btn-block'>Edit Category</a><br />
                        </div>
                        <div class='col-12 col-sm-6 col-md-4 col-lg-6 col-xxxl-12'>
                            <a href='staff_forums.php?action=delforum' class='btn btn-danger btn-block'>Delete Category</a><br />
                        </div>
                    </div>
                </div>
            </div>
            <br />
         </div>
        <div class='col-12 col-lg-6 col-xxl-6'>
            <div class='card'>
                <div class='card-header'>
                    Punishments
                </div>
                <div class='card-body'>
                    <div class='row'>
                        <div class='col-12 col-sm-6 col-md-4 col-lg-6 col-xxxl-3'>
                            <a href='staff_punish.php?action=fedjail' class='btn btn-danger btn-block'>Fed Dungeon</a><br />
                        </div>
                        <div class='col-12 col-sm-6 col-md-4 col-lg-6 col-xxxl-3'>
                            <a href='staff_punish.php?action=forumwarn' class='btn btn-primary btn-block'>Forum Warning</a><br />
                        </div>
                        <div class='col-12 col-sm-6 col-md-4 col-lg-6 col-xxxl-3'>
                            <a href='staff_punish.php?action=forumban' class='btn btn-warning btn-block'>Forum Ban</a><br />
                        </div>
                        <div class='col-12 col-sm-6 col-md-4 col-lg-6 col-xxxl-3'>
                            <a href='staff_punish.php?action=spamhammer' class='btn btn-dark btn-block'>Spam Hammer</a><br />
                        </div>";
                        //assistant only stuff
                        if ($api->UserMemberLevelGet($userid, 'assistant')) 
                        {
                            echo "<div class='col-12 col-sm-6 col-md-4 col-lg-6 col-xxxl-3'>
                                    <a href='staff_fedjail.php?action=viewappeal' class='btn btn-primary btn-block'>Fed Dungeon Appeals</a><br />
                                </div>
                                <div class='col-12 col-sm-6 col-md-4 col-lg-6 col-xxxl-3'>
                                    <a href='staff_punish.php?action=editfedjail' class='btn btn-warning btn-block'>Edit Fed Sentence</a><br />
                                </div>
                                <div class='col-12 col-sm-6 col-md-4 col-lg-6 col-xxxl-3'>
                                    <a href='staff_punish.php?action=mailban' class='btn btn-danger btn-block'>Mail Ban</a><br />
                                </div>
                                <div class='col-12 col-sm-6 col-md-4 col-lg-6 col-xxxl-3'>
                                    <a href='staff_punish.php?action=unfedjail' class='btn btn-success btn-block'>Remove Fed Sentence</a><br />
                                </div>
                                <div class='col-12 col-sm-6 col-md-4 col-lg-6 col-xxxl-3'>
                                    <a href='staff_punish.php?action=unforumban' class='btn btn-success btn-block'>Remove Forum Ban</a><br />
                                </div>
                                <div class='col-12 col-sm-6 col-md-4 col-lg-6 col-xxxl-3'>
                                    <a href='staff_punish.php?action=unmailban' class='btn btn-success btn-block'>Remove Mail Ban</a><br />
                                </div>
                                <div class='col-12 col-sm-6 col-md-4 col-lg-6 col-xxxl-3'>
                                    <a href='staff_punish.php?action=ipsearch' class='btn btn-primary btn-block'>IP Lookup</a><br />
                                </div>";
                        }
                        //admin only stuff
                        if ($api->UserMemberLevelGet($userid, 'admin')) 
                        {
                            echo "<div class='col-12 col-sm-6 col-md-4 col-lg-6 col-xxxl-3'>
                                    <a href='staff_punish.php?action=massemail' class='btn btn-primary btn-block'>Mass Email</a><br />
                                </div>
                                <div class='col-12 col-sm-6 col-md-4 col-lg-6 col-xxxl-3'>
                                    <a href='staff_punish.php?action=banip' class='btn btn-dark btn-block'>Ban IP</a><br />
                                </div>
                                <div class='col-12 col-sm-6 col-md-4 col-lg-6 col-xxxl-3'>
                                    <a href='staff_punish.php?action=unbanip' class='btn btn-light btn-block'>Pardon IP</a><br />
                                </div>";
                        }
                        echo "
                    </div>
                </div>
            </div>
            <br />
         </div>";

//start staff logs at end for admins.
if ($api->UserMemberLevelGet($userid, 'admin'))
{
    echo "
    <div class='col-12 col-lg-6 col-xxl-4 col-xxxl-6'>
            <div class='card'>
                <div class='card-header'>
                    Last 5 Staff Actions
                </div>
                <div class='card-body'>";
                    $q =
                    $db->query(
                        "/*qc=on*/SELECT `log_user`, `log_text`, `log_time`, `log_ip`, `username`
							 FROM `logs` AS `s`
							 INNER JOIN `users` AS `u`
							 ON `s`.`log_user` = `u`.`userid`
							 WHERE `log_type` = 'staff'
							 ORDER BY `s`.`log_time` DESC
							 LIMIT 5");
                    while ($r = $db->fetch_row($q))
                    {
                        echo "<div class='row'>
                        <div class='col-6 col-md-4'>
                            <div class='row'>
                                <div class='col-12'>
                                    <small>" . DateTime_Parse($r['log_time']) . "</small>
                                </div>
                                <div class='col-12'>
                                    <a href='../profile.php?user={$r['log_user']}'>{$r['username']}</a> [{$r['log_user']}]
                                </div>
                            </div>
                        </div>
                        <div class='col-6 col-md-8'>
                            <div class='row'>
                                <div class='col-12'>
                                    <small>{$r['log_ip']}</small>
                                </div>
                                <div class='col-12'>
                                    {$r['log_text']}
                                </div>
                            </div>
                            
                        <br />
                        </div>
                        </div>";
                    }
                    echo "
                </div>
            </div>
            <br />
        </div>";
}
echo "</div>";