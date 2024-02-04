<?php
/*
	File:		gclog.php
	Created: 	10/14/2017 at 12:30AM Eastern Time
	Info: 		Lists the guild crime logs for the user to read.
	Author:		TheMasterGeneral
	Website: 	https://github.com/MasterGeneral156/chivalry-engine
*/
require('globals.php');
//Secure the GET
$_GET['ID'] = (isset($_GET['ID']) && is_numeric($_GET['ID'])) ? abs(intval($_GET['ID'])) : 0;

//Verify that the GET is still set.
if (empty($_GET['ID'])) {
    alert('danger', "Uh Oh!", "Please specify the guild crime log you wish to view.", true, 'index.php');
    die($h->endpage());
}

//Select the log from the database.
$q = $db->query("/*qc=on*/SELECT * FROM `guild_crime_log` WHERE `gclID` = {$_GET['ID']}");

//Make sure the crime log exists.
if ($db->num_rows($q) == 0) {
    alert('danger', "Uh Oh!", "The guild crime log you wish to view does not exist.", true, 'index.php');
    die($h->endpage());
}
$r = $db->fetch_row($q);
$db->free_result($q);

//Check that the user is a part of the guild who committed this crime.
if ($ir['guild'] != $r['gclGUILD']) {
    alert('danger', "Uh Oh!", "You cannot view this log as you are not part of the guild who committed it.", true, 'index.php');
    die($h->endpage());
}
$name=$db->fetch_single($db->query("/*qc=on*/SELECT `gcNAME` FROM `guild_crimes` WHERE `gcID` = {$r['gclCID']}"));
echo "<div class='card'>
        <div class='card-header'>
            {$name} " . createBadge(DateTime_Parse($r['gclTIME'])) . "
        </div>
        <div class='card-body'>
            <div class='row'>
                <div class='col-12'>
                    <div class='row'>
                        <div class='col-12'>
                            <small><b>Summary</b></small>
                        </div>
                        <div class='col-12'>
                            {$r['gclLOG']}
                        </div>
                    </div>
                </div>
                <div class='col-12 col-sm'>
                    <div class='row'>
                        <div class='col-12'>
                            <small><b>Result</b></small>
                        </div>
                        <div class='col-12'>
                            " . ucfirst($r['gclRESULT']) . "
                        </div>
                    </div>
                </div>
                <div class='col-12 col-sm'>
                    <div class='row'>
                        <div class='col-12'>
                            <small><b>Copper Coins</b></small>
                        </div>
                        <div class='col-12'>
                            " . shortNumberParse($r['gclWINNING']) . "
                        </div>
                    </div>
                </div>
            </div>
        </div>
</div>";
$h->endpage();