<?php
require('globals.php');
if ($userid == 1)
{
    $maxitem=$db->query("SELECT `itmid` FROM `items` ORDER BY `itmid` DESC LIMIT 1");
    $item=$db->fetch_single($maxitem);
    $success = 0;
    $blacklist=array(90,130,129,18,148,198,132,131,17,98,12,15,
                     16,13,14,92,91,59,124,117,99,122,27,32,128,
                     89,22,23,56,45,25,57,97,162,43,200,195,137,
                     189,147,203,69,65,63,196,150,146,202,197,145,
                     143,141,144,140,142,190,178,201,135,66,60,87,
                     187,164,191,190,161,158,172,160,179,156,157,204,
                     153,188,155,163,26,199,182,159,168,192,138,86,177,168,167,96,95);
    while ($success != 3)
    {
        $random=Random(1,$item);
        $q=$db->query("SELECT * FROM `items` WHERE `itmid` = {$random}");
        if ($db->num_rows($q) == 0)
        {
            echo "No Item exists for Item ID: $random. Selecting next number.<br />";
        }
        elseif (in_array($random,$blacklist))
        {
            echo "Item ID: $random is a blacklisted item. Selecting next number.<br />";
        }
        else
        {
            $success=$success+1;
            echo "<b>Item ID: $random selected for Random Item #{$success} for the week.<br /></b>";
            $db->query("UPDATE `settings` SET `setting_value` = {$random} WHERE `setting_name` = 'itemweek{$success}'");
        }
    }
}
else
{
    alert('danger',"Uh Oh!","This isn't for you.",true,'explore.php');
}
$h->endpage();