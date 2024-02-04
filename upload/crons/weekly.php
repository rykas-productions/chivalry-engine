<?php
$menuhide=1;
require_once(__DIR__ .'/../globals_nonauth.php');
if (!isset($_GET['code']) || $_GET['code'] !== $_CONFIG['code'])
{
    exit;
}
$maxitem=$db->query("/*qc=on*/SELECT `itmid` FROM `items` 
					WHERE `itmtype` != 6  
					AND `itmtype` != 3 
					AND `itmtype` != 13 
					AND `itmtype` != 14 
					AND `itmtype` != 10
                    AND `itmbuyable` = 'true'
					ORDER BY `itmid` DESC LIMIT 1");
$item=$db->fetch_single($maxitem);
$success = 0;
$blacklist=array(90,130,129,18,148,198,17,98,12,15,
    16,13,14,92,91,99,32,128,
    89,22,23,56,45,25,57,97,162,43,200,195,137,
    189,147,203,69,65,63,196,150,146,202,197,145,
    143,141,144,140,142,190,178,201,135,66,60,87,
    187,164,191,190,161,158,172,160,179,156,157,204,
    153,188,155,163,26,199,182,159,168,192,138,86,177,
    168,167,96,95,114,115,113,126,127,125,205,233,235,263,
    207,210,262,263,264,265,267,269,270,271,272,273,274,275,
    276,279,283,284,285,286,287,288,294,296,308,309,310,311,
    313,314,319,320,323,324,325,326,327,328,329,330,331,332,337,
    346,347,348,349,350,352,355,356,363,364,368,369,370,371,373,
    374,375,376,377,378,379,380,381,382,383,385,386,387,388,389,
    390,391,392,394,407,408,409,418,419,421,422,423,424,425,445,
    456,460,448,447,448,449,450,451,452,455,456
);
while ($success != 3)
{
    $random=Random(1,$item);
    $q=$db->query("/*qc=on*/SELECT * FROM `items` WHERE `itmid` = {$random}");
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
        $item[$success];
        $success=$success+1;
        echo "<b>Item ID: $random selected for Random Item #{$success} for the week.<br /></b>";
        $db->query("UPDATE `settings` SET `setting_value` = {$random} WHERE `setting_name` = 'itemweek{$success}'");
    }
}
doPlayerofWeekTick();
//$api->System
//$api->GameAddAnouncement("Its a brand new week, so that means three new items are available to be purchased at a 50% discount at the Item of the Week shop on Explore. This week, we have the ")