<?php
require('globals.php');
if (!$api->UserHasItem($userid,249,1))
{
    alert('danger',"Uh Oh!","You need a Locked Spell Book to be here.",true,'inventory.php');
    die($h->endpage());
}
if (!$api->UserHasItem($userid,250,1))
{
    alert('danger',"Uh Oh!","You need a Locked Spell Book Key to be here.",true,'inventory.php');
    die($h->endpage());
}
$rng=Random(1,4);
if ($rng == 1)
{
    alert('success',"Success!","You open this book and learn the Lightning Spell. Congratz.",true,'inventory.php');
    $api->UserGiveItem($userid,180,1);
}
if ($rng == 2)
{
    alert('success',"Success!","You open this book and learn the Ice Spell. Congratz.",true,'inventory.php');
    $api->UserGiveItem($userid,251,1);
}
if ($rng == 3)
{
    alert('success',"Success!","You open this book and learn the Fire Spell. Congratz.",true,'inventory.php');
    $api->UserGiveItem($userid,252,1);
}
if ($rng == 4)
{
    alert('success',"Success!","You open this book and learn the Magic Shield Spell. Congratz.",true,'inventory.php');
    $api->UserGiveItem($userid,253,1);
}
$api->UserTakeItem($userid,249,1);
$api->UserTakeItem($userid,250,1);
$h->endpage();