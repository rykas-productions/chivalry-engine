<?php
require('globals.php');
$time = time();
$q=$db->query("SELECT * FROM `users_effects` WHERE `userid` = {$userid} AND `effectTimeOut` > {$time}");
echo "<div class='row'>";
while ($r = $db->fetch_row($q))
{
    echo "<div class='col'>
                <div class='card'>
                    <div class='card-body'>
                        <h3>" . strtoupper($r['effectName']) . "</h3>
                        <small><span class='text-muted'>Effective until" . TimeUntil_Parse($r['effectTimeOut']) . ".</span></small>
                    </div>
                </div>
          </div>";
}
echo "</div>";
$h->endpage();