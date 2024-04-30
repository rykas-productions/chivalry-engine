<?php
require('globals_nonauth.php');
$q=$db->query("SELECT * FROM `ce_anal` ORDER BY `id` DESC");
echo "<div class='card'>
        <div class='card-header'>
        </div>
        <div class='card-body'>";
while ($r = $db->fetch_row($q))
{
    echo "  <div class='row'>
                <div class='col-12 col-sm-6 col-md-4'>
                    <div class='row'>
                        <div class='col-12'>
                            <small><b>Game Name</b></small>
                        </div>
                        <div class='col-12'>
                            {$r['gamename']}
                        </div>
                        <div class='col-12'>
                            <small><a href='http://{$r['url']}'>{$r['url']}</a></small>
                        </div>
                    </div>
                </div>
                <div class='col-12 col-sm-6 col-md-2 col-lg-1'>
                    <div class='row'>
                        <div class='col-12'>
                            <small><b>Version</b></small>
                        </div>
                        <div class='col-12'>
                            {$r['version']}
                        </div>
                    </div>
                </div>
                <div class='col-12 col-sm-6 col-md'>
                    <div class='row'>
                        <div class='col-12'>
                            <small><b>Install Time</b></small>
                        </div>
                        <div class='col-12'>
                            " . DateTime_Parse($r['installtime']) . "
                        </div>
                    </div>
                </div>
                <div class='col-12 col-sm-6 col-md'>
                    <div class='row'>
                        <div class='col-12'>
                            <small><b>Last Seen</b></small>
                        </div>
                        <div class='col-12'>
                            " . DateTime_Parse($r['lastseen']) . "
                        </div>
                    </div>
                </div>
            </div>";
}
echo "</div></div>";
$h->endpage();