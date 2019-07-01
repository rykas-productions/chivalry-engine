<?php
require('globals_nonauth.php');
$q=$db->query("SELECT * FROM `ce_anal` ORDER BY `id` DESC");
echo "<table class='table table-bordered'>
<tr>
    <th>
        Game Info
    </th>
    <th>
        Install Date
    </th>
    <th>
        URL
    </th>
</tr>";
while ($r = $db->fetch_row($q))
{
    echo "<tr>
        <td>
        Name: {$r['gamename']}<br />
        Version: {$r['version']}
        </td>
        <td>
        " . DateTime_Parse($r['installtime']) . "
        </td>
        <td>
            <a href='http://{$r['url']}'>{$r['url']}</a>
        </td>
    </tr>";
}
echo "</table>";
$h->endpage();