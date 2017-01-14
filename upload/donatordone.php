<?php
require_once('globals.php');
if (!isset($_GET['action']))
{
    ob_get_clean();
    header('HTTP/1.1 400 Bad Request');
    exit;
}
if ($_GET['action'] == "cancel")
{
    alert("success",$lang['ERROR_SUCCESS'],$lang['VIP_CANCEL']);
}
else if ($_GET['action'] == "done")
{
    if (!$_POST['tx'])
    {
        die($h->endpage());
    }
    alert("success","{$lang['VIP_THANKS']} {$set['WebsiteName']}",$lang['VIP_SUCCESS']);
}
$h->endpage();
