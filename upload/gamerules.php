<?php
/*
	File:		gamerules.php
	Created: 	4/5/2016 at 12:03AM Eastern Time
	Info: 		Lists the game rules to the player in-game.
	Author:		TheMasterGeneral
	Website: 	https://github.com/MasterGeneral156/chivalry-engine
*/
require('globals.php');
alert('info', "", "You are expected to follow this rules. You are also expected to check back on these fairly frequently as these rules
may change without notice. Staff will not accept ignorance as an excuse if you break one of these rules.",  false);
echo "<div class='card'>
        <div class='card-header'>
            {$set['WebsiteName']} Game Rules
        </div>
        <div class='card-body'>";
$q = $db->query("/*qc=on*/SELECT * FROM `gamerules` ORDER BY `rule_id` ASC");
$rulenumber = 1;
//List game rules.
while ($r = $db->fetch_row($q)) 
{
    echo "  <div class='row'>
                <div class='col-auto'>
                    <div class='row'>
                        <div class='col-12'>
                            <small><b>Rule #{$rulenumber}</b></small>
                        </div>
                        <div class='col-12'>
                            {$r['rule_text']}
                        </div>
                    </div>
                    <br />
                </div>
            </div>";
	$rulenumber = $rulenumber + 1;
}
echo "</div></div>";
$h->endpage();