<?php
$menuhide=1;
require_once(__DIR__ .'/../globals_nonauth.php');
if (!isset($_GET['code']) || $_GET['code'] !== $_CONFIG['code'])
{
    exit;
}
doPlayerofWeekTick();
//$api->System
//$api->GameAddAnouncement("Its a brand new week, so that means three new items are available to be purchased at a 50% discount at the Item of the Week shop on Explore. This week, we have the ")