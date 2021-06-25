<?php
require('globals.php');
if (!isset($_POST['do']))
{
    echo "Are you sure you want to dump your inventory? This cannot be done.
    <form method='post'>
        <input type='hidden' name='do' value='yes'>
        <input type='submit' value='Yes' class='btn btn-danger'>
    </form>";
}
else
{
    $q=$db->query("SELECT `iv`.*, `i`.* 
                    FROM `inventory` as `iv` 
                    INNER JOIN `items` AS `i`
                    ON `iv`.`inv_itemid` = `i`.`itmid` 
                    WHERE `iv`.`inv_userid` = {$userid}");
    echo "This might take a while...";
    $totalcost=0;
    while ($r=$db->fetch_row($q))
    {
        $totalcost=$totalcost+($r['itmbuyprice']*$r['inv_qty']);
        $db->query("DELETE FROM `inventory` WHERE `inv_id` = {$r['inv_id']}");
    }
    echo "Done. That's " . shortNumberParse($totalcost) . " Copper Coins down the drain.";
    if ($totalcost > 500000000)
    {
        $achieved=$db->query("/*qc=on*/SELECT * FROM `achievements_done` WHERE `userid` = {$userid} and `achievement` = 85");
        if ($db->num_rows($achieved) == 0)
        {
            echo "<br />Huh... you've dumped over 500M Copper Coins worth... you deserve the <b>Chump Change</b> achievement. We've given you a badge and a skill point.";
            $api->UserGiveItem($userid,245,1);
            $db->query("INSERT INTO `achievements_done` (`userid`, `achievement`) VALUES ('{$userid}', '85')");
            $db->query("UPDATE `user_settings` SET `skill_points` = `skill_points` + 1 WHERE `userid` = {$userid}");
        }
    }
    else
    {
        echo "<br />Huh. You're a little low.";
    }
}
$h->endpage();