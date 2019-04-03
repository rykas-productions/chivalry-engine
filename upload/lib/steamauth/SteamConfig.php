<?php
//Version 4.0
$steamauth['apikey'] = "DD6DD904F5AE302B8DEAA67790AA498B"; // Your Steam WebAPI-Key found at https://steamcommunity.com/dev/apikey
$steamauth['domainname'] = "https://chivalryisdeadgame.com/"; // The main URL of your website displayed in the login page
$steamauth['logoutpage'] = "https://chivalryisdeadgame.com/logout.php"; // Page to redirect to after a successfull logout (from the directory the SteamAuth-folder is located in) - NO slash at the beginning!
$steamauth['loginpage'] = "https://chivalryisdeadgame.com/auth_steam.php"; // Page to redirect to after a successfull login (from the directory the SteamAuth-folder is located in) - NO slash at the beginning!
$steamauth['loginpage2'] = "https://chivalryisdeadgame.com/preferences.php?action=steamlink";

// System stuff
if (empty($steamauth['apikey'])) {die("<div style='display: block; width: 100%; background-color: red; text-align: center;'>SteamAuth:<br>Please supply an API-Key!<br>Find this in steamauth/SteamConfig.php, Find the '<b>\$steamauth['apikey']</b>' Array. </div>");}
if (empty($steamauth['domainname'])) {$steamauth['domainname'] = $_SERVER['SERVER_NAME'];}
if (empty($steamauth['logoutpage'])) {$steamauth['logoutpage'] = $_SERVER['PHP_SELF'];}
if (empty($steamauth['loginpage'])) {$steamauth['loginpage'] = $_SERVER['PHP_SELF'];}
?>
