<?php
require('globals.php');
$time=time();
if (userHasEffect($userid, effect_mysterious_potion))
{
    $comebacktime=returnEffectDone($userid, effect_mysterious_potion);
    alert('danger',"Uh Oh!","You must wait for the potion to clear out of your system first before you drink another. Come back in " . TimeUntil_Parse($comebacktime) . ".",true,'inventory.php');
    die($h->endpage());
}
if (!$api->UserHasItem($userid,123,1))
{
    alert('danger',"Uh Oh!","You need at least one Mysterious Potion before you can drink one.",true,'inventory.php');
    die($h->endpage());
}
$effect=Random(1,10);
if ($effect == 1)
{
    $effect='were poisoned. You have lost all your health.';
    $db->query("UPDATE `users` SET `hp` = 0 WHERE `userid` = {$userid}");
}
if ($effect == 2)
{
    $effect="had your health fully regenerated.";
    $db->query("UPDATE `users` SET `hp` = `maxhp` WHERE `userid` = {$userid}");
}
if ($effect == 3)
{
    $visittown=$db->fetch_single($db->query("/*qc=on*/SELECT `town_id` FROM `town` WHERE `town_min_level` <= {$ir['level']} AND `town_id` != {$ir['location']} ORDER BY RAND() LIMIT 1"));
    if ($ir['level'] < 5)
    {
        $town = 1;
    }
    else
    {
        $town = Random(1,$visittown);
    }
    $effect="fainted, waking up in {$api->SystemTownIDtoName($town)}.";
    $db->query("UPDATE `users` SET `location` = {$town} WHERE `userid` = {$userid}");
}
if ($effect == 4)
{
    $luck=Random(0,30);
    $db->query("UPDATE `userstats` SET `luck` = `luck` + '{$luck}' WHERE `userid` = {$userid}");
    if (($ir['luck'] + $luck) > 150)
    {
        $db->query("UPDATE `userstats` SET `luck` = 150 WHERE `userid` = {$userid}");
    }
    $effect="had your luck change by {$luck}%.";
}
if ($effect == 5)
{
    $effect="and it refilled your Will to maximum.";
    $db->query("UPDATE `users` SET `will` = `maxwill` WHERE `userid` = {$userid}");
}
if ($effect == 6)
{
    $db->query("UPDATE `mining` SET `miningpower` = `max_miningpower` WHERE `userid` = {$userid}");
    $effect="had your mining energy completely replenished.";
}
if ($effect == 7)
{
    $effect="nothing happens.";
}
if ($effect == 8)
{
    $newname=$db->escape(strip_tags(stripslashes(str_shuffle($ir['username']))));
    $effect="and it randomized your name! Nice to meet you, {$newname}.";
    $db->query("UPDATE `users` SET `username` = '{$newname}' WHERE `userid` = {$userid}");
}
if ($effect == 9)
{
   $rng=Random(2,12);
   $effect="had {$rng} hours removed from your course time.";
   $hour=($rng*60)*60;
   if ($ir['course'] > 0)
    $db->query("UPDATE `users` SET `course_complete` = `course_complete` - {$hour} WHERE `userid` = {$userid}");
}
if ($effect == 10)
{
    if ($ir['gender'] == 'Male')
        $gender='Female';
    else
        $gender='Male';
    $effect="and had your gender changed to {$gender}.";
    $db->query("UPDATE `users` SET `gender` = '{$gender}' WHERE `userid` = {$userid}");
}

userGiveEffect($userid, effect_mysterious_potion, 3600);
$api->UserTakeItem($userid,123,1);
alert('success',"Success!","You drink one Mysterious Potion and {$effect}",true,'inventory.php');
$api->SystemLogsAdd($userid, 'itemuse', "Used Mysterious Potion.");
$h->endpage();