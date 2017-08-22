<?php
/*
    File:       itemauction.php
    Created:    4/20/2017 at 6:38 PM
    Info:       [Enter Info]
    Author:     TheMasterGeneral
    Website:    https://github.com/MasterGeneral156/chivalry-engine
*/
require('globals.php');
echo "<h3>Item Auction</h3> <hr />";
if (!isset($_GET['action']))
{
    $_GET['action'] = '';
}
switch ($_GET['action'])
{
    case 'list':
        add_listing();
        break;
    case 'bid':
        bid();
        break;
    case 'remove':
        remove_listing();
        break;
    default:
        home();
        break;
}
function home()
{
    global $db,$api,$userid;
    $q=$db->query("SELECT * FROM `itemauction` ORDER BY `ia_end` DESC");
    echo "<table class='table table-bordered table-striped'>
        <tr>
            <th>
                Lister
            </th>
            <th>
                Item x Qty
            </th>
            <th>
                Current Bid
            </th>
            <th>
                Current Bidder
            </th>
            <th>
                Closes
            </th>
            <th>
                Actions
            </th>
        </tr>";
    while ($r = $db->fetch_row($q))
    {
        if ($userid == $r['ia_adder'])
        {
            $action = "<a href='?action=remove&id={$r['ia_id']}'>Remove</a>";
        }
        else
        {
            $action = "<a href='?action=bid&id={$r['ia_id']}'>Bid</a>";
        }
        echo "<tr>
            <td>
                <a href='profile.php?user={$r['ia_adder']}'>
                    {$api->SystemUserIDtoName($r['ia_adder'])}
                </a>
            </td>
            <td>
                <a href='iteminfo.php?ID={$r['ia_item']}'>
                    {$api->SystemItemIDtoName($r['ia_item'])}
                </a>
                x " . number_format($r['ia_qty']) . "
            </td>
            <td>
                " .  number_format($r['ia_bid']) . " Primary Currency
            </td>
            <td>
                <a href='profile.php?user={$r['ia_bidder']}'>
                    {$api->SystemUserIDtoName($r['ia_bidder'])}
                </a>
            </td>
            <td>
                " . TimeUntil_Parse($r['ia_end']) . "
            </td>
            <td>
                {$action}
            </td>
            </tr>";
    }
    echo"</table>";
}
$h->endpage();