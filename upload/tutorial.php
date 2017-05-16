<?php
/*
	File:		tutorial.php
	Created: 	5/14/2017 at 5:57 Eastern Time
	Info: 		A very detailed game tutorial.
	Author:		TheMasterGeneral
	Website: 	https://github.com/MasterGeneral156/chivalry-engine
*/
require('globals.php');
echo "{$lang['TUT_WELCOME']} {$set['WebsiteName']} {$lang['TUT_WELCOME1']}, {$ir['username']}! {$lang['TUT_WELCOME2']}";
echo "<hr />
{$lang['GEN_IN']} {$set['WebsiteName']}{$lang['TUT_INFO']}<hr />
<h3>{$lang['TUT_GLOSSARY']}</h3>
	<a href='#basics'>{$lang['TUT_PT1']}</a><br />
	<a href='#navigation'>{$lang['TUT_PT2']}</a><br />
	<a href='#explore'>{$lang['TUT_PT3']}</a><br />
<hr />
<a name='basics'><h4>{$lang['TUT_PT1']}</h4></a>
	{$lang['TUT_BASICS']}<br /><br />
	
	<u>{$lang['TUT_PINFO']}</u><br />
	{$lang['TUT_PINFO_DETAIL']}<br /><br />
	
	<u>{$lang['TUT_STATS']}</u><br />
	{$lang['TUT_STATS_DETAIL']}<br /><br />
<hr />
<a name='navigation'><h4>{$lang['TUT_PT2']}</h4></a>
	{$lang['TUT_NAV']}<br /><br />
	
	<u>{$lang['TUT_NAVBAR']}</u><br />
	{$lang['TUT_NAVBAR_DETAIL']}<br /><br />
	
	<u>{$lang['TUT_NAVDROP']}</u><br />
	{$lang['TUT_NAVDROP_DETAIL']}<br /><br />
<hr />
<a name='explore'><h4>{$lang['TUT_PT3']}</h4></a>
	{$lang['TUT_EXPLORE']}<br /><br />
	
	<u>{$lang['TUT_SHOPS']}</u><br />
	{$lang['TUT_SHOPS_DETAIL']}<br /><br />
	
	<u>{$lang['TUT_FINANCIAL']}</u><br />
	{$lang['TUT_FINANCIAL_DETAIL']}<br /><br />
	
	<u>{$lang['TUT_HL']}</u><br />
	{$lang['TUT_HL_DETAIL']}<br /><br />
	
	<u>{$lang['TUT_ADMIN']}</u><br />
	{$lang['TUT_ADMIN_DETAIL']}<br /><br />
	
	<u>{$lang['TUT_GAMES']}</u><br />
	{$lang['TUT_GAMES_DETAIL']}<br /><br />
<hr />";
$h->endpage();