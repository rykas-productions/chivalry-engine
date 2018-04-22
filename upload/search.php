<?php
require_once('globals.php');
echo "<h3><i class='fas fa-search'></i> Search</h3><hr />";
if (!isset($_GET['action'])) {
    $_GET['action'] = '';
}
switch ($_GET['action']) {
    case 'name':
        name();
        break;
    default:
        home();
        break;
}
function home()
{
    echo "Search by Username<br />
    <form>
        <input type='hidden' name='action' value='name'>
        <input class='form-control' required='1' name='name'>
        <button type='submit' class='btn btn-primary'><i class='fas fa-search'></i> Search</button>
    </form>
    <hr />";
    echo "Search by User ID<br />
    <form action='profile.php'>
        <input type='number' min='1' class='form-control' required='1' name='user'>
        <button type='submit' class='btn btn-primary'><i class='fas fa-search'></i> Search</button>
    </form>";
}
function name()
{
    global $db,$api,$h,$userid;
    $_GET['name'] = (isset($_GET['name']) && preg_match("/^[a-z0-9_]+([\\s]{1}[a-z0-9_]|[a-z0-9_])*$/i", $_GET['name'])) ? $db->escape(strip_tags(stripslashes($_GET['name']))) : '';
    if (empty($_GET['name']))
    {
        alert('danger',"Uh Oh!","Please fill out the form and try again.",true,'search.php');
        die($h->endpage());
    }
    if (((strlen($_GET['name']) > 20) OR (strlen($_GET['name']) < 3)))
    {
        alert('danger',"Uh Oh!","Usernames must be at least 3 characters in length, and a maximum of 20.",true,'search.php');
        die($h->endpage());
    }
    $name="%{$_GET['name']}%";
    $q = $db->query("/*qc=on*/SELECT `userid`, `username`, `level`, `laston`
                     FROM `users`
                     WHERE `username` LIKE ('{$name}')");
    echo $db->num_rows($q) . " players found.<br />
    <table class='table table-bordered table-striped'>
    <thead>
        <tr>
            <th>
                Username
            </th>
            <th>
                Level
            </th>
            <th>
                Last active
            </th>
        </tr>
    </thead>";
    while ($r=$db->fetch_row($q))
    {
        echo "<tr>
            <td>
                <a href='profile.php?user={$r['userid']}'>{$r['username']}</a> [{$r['userid']}]
            </td>
            <td>
                {$r['level']}
            </td>
            <td>
                " . DateTime_Parse($r['laston']) . "
            </td>
        </tr>";
    }
    
    echo"</table>
    <a href='search.php'>Go Back</a>";
}

$h->endpage();