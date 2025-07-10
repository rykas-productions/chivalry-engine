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
        <div class='col-12'>
            <div class='card'>
                <div class='card-header'>
                    {$set['WebsiteName']} Information
                </div>
                <div class='card-body'>
                    <div class='row'>
                        <div class='col-12 col-xl-7 col-xxl-6'>
                            <div class='row'>
                                <div class='col-12 col-sm-6 col-xxxl-4'>
                                    <div class='row'>
                                        <div class='col-12'>
                                            <small><b>PHP Version</b></small>
                                        </div>
                                        <div class='col-12'>
                                            " . phpversion_exact() . "
                                        </div>
                                    </div>
                                </div>
                                <div class='col-12 col-sm-6 col-xxxl-4'>
                                    <div class='row'>
                                        <div class='col-12'>
                                            <small><b>DB Version</b></small>
                                        </div>
                                        <div class='col-12'>
                                            " . $MySQLIVersion . "
                                        </div>
                                    </div>
                                </div>
                                <div class='col-12 col-sm-6 col-xxxl-4'>
                                    <div class='row'>
                                        <div class='col-12'>
                                            <small><b>Web Server</b></small>
                                        </div>
                                        <div class='col-12'>
                                            " . apache_get_version() . "
                                        </div>
                                    </div>
                                </div>
                                <div class='col-12 col-sm-6 col-xxxl-4'>
                                    <div class='row'>
                                        <div class='col-12'>
                                            <small><b>API Version</b></small>
                                        </div>
                                        <div class='col-12'>
                                            {$api->SystemReturnAPIVersion()}
                                        </div>
                                    </div>
                                </div>
                                <div class='col-12 col-sm-6 col-xxxl-4'>
                                    <div class='row'>
                                        <div class='col-12'>
                                            <small><b>Chivalry Engine Version</b></small>
                                        </div>
                                        <div class='col-12'>
                                            {$set['Version_Number']}
                                        </div>
                                    </div>
                                </div>
                                <div class='col-12 col-sm-6 col-xxxl-4'>
                                    <div class='row'>
                                        <div class='col-12'>
                                            <small><b>Debug mode</b></small>
                                        </div>
                                        <div class='col-12'>
                                            {$debugMode}
                                        </div>
                                    </div>
                                </div>
                                <div class='col-12 col-sm-6 col-xxxl-4'>
                                    <div class='row'>
                                        <div class='col-12'>
                                            <small><b>Dev mode</b></small>
                                        </div>
                                        <div class='col-12'>
                                            {$devMode}
                                        </div>
                                    </div>
                                </div>
                                <div class='col-12 col-xxxl-8'>
                                    <div class='row'>
                                        <div class='col-12'>
                                            <small><b>Engine Updates</b></small>
                                        </div>
                                        <div class='col-12'>
                                            " . version_json() . "
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class='col-12 col-lg-6 col-xl-3 col-xxl'>
                            <div class='row'>
                                <div class='col-12'>
                                    <div class='row'>
                                        <div class='col-12'>
                                            <small><b>Disk Free</b></small>
                                        </div>
                                        <div class='col-12'>
                                            <div class='row'>
                                                <div class='col-12'>
                                                    " . scaledColorProgressBar(disk_free_space("/"), 0, disk_total_space("/"), true) . "
                                                </div>
                                                <div class='col-12'>
                                                    <small><i>" . numberToByteParse(disk_free_space("/")) . " / " . numberToByteParse(disk_total_space("/")) . "</small></i>
                                                </div>
                                            </div>
                                            
                                        </div>
                                    </div>
                                </div>
                                <div class='col-12'>
                                    <div class='row'>
                                        <div class='col-12'>
                                            <small><b>Bandwidth Transferred</b></small>
                                        </div>
                                        <div class='col-12'>
                                            <div class='row'>
                                                <div class='col-12'>
                                                    " . scaledColorProgressBar(returnVPSBandwidth($_CONFIG['vpsAuth']), 0, 1024*1024*1024*1024*3, true) . "
                                                </div>
                                                <div class='col-12'>
                                                    <small><i>" . numberToByteParse(returnVPSBandwidth($_CONFIG['vpsAuth'])) . " / " . numberToByteParse(1024*1024*1024*1024*3) . "</i></small>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class='col-12 col-lg-6 col-xl-2'>
                            <div class='row'>
                                <div class='col-12 col-sm-6 col-xl-12'>
                                    <div class='row'>
                                        <div class='col-12'>
                                            <small><b>Moderation Logs</b></small>
                                        </div>
                                        <div class='col-12'>
                                            <a href='staff_moderation.php?action=listall'>" . number_format($db->fetch_single($db->query("SELECT COUNT(`mod_id`) FROM `staff_moderation_board`"))) . "</a>
                                        </div>
                                    </div>
                                </div>
                                <div class='col-12 col-sm-6 col-xl-12'>
                                    <div class='row'>
                                        <div class='col-12'>
                                            <small><b>Monthly Income</b></small>
                                        </div>
                                        <div class='col-12'>
                                            \${$set['MonthlyDonationGoal']}
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>";
}
if ($api->UserMemberLevelGet($userid, 'admin'))
{
    echo "
    <div class='col-12'>
            <div class='card'>
                <div class='card-header'>
                    Last 15 Staff Actions
                </div>
                <div class='card-body'>";
                    $q =
                    $db->query(
                        "/*qc=on*/SELECT `log_user`, `log_text`, `log_time`, `log_ip`
							 FROM `logs` AS `s`
							 WHERE `log_type` = 'staff'
							 ORDER BY `s`.`log_time` DESC
							 LIMIT 15");
                    while ($r = $db->fetch_row($q))
                    {
                        $r['username'] = parseUsername($r['log_user']);
                        echo "<div class='row'>
                        <div class='col-12 col-md col-xxxl-2'>
                            <div class='row'>
                                <div class='col-12'>
                                    <small><b>" . DateTime_Parse($r['log_time']) . "</b></small>
                                </div>
                                <div class='col-12'>
                                    <a href='../profile.php?user={$r['log_user']}'>{$r['username']}</a> [{$r['log_user']}]
                                </div>
                            </div>
                        </div>
                        <div class='col-12 col-md-9 col-xl-10 col-xxxl'>
                            <div class='row'>
                                <div class='col-12'>
                                    <small><b>{$r['log_ip']}</b></small>
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
$h->endpage();