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
	<a href='#training'>{$lang['TUT_PT4']}</a><br />
	<a href='#combat'>{$lang['TUT_PT5']}</a><br />
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
	
	<u>{$lang['TUT_GUILDS']}</u><br />
	{$lang['TUT_GUILDS_DETAIL']}<br /><br />
	
	<u>{$lang['TUT_ACT']}</u><br />
	{$lang['TUT_ACT_DETAIL']}<br /><br />
	
	<u>{$lang['TUT_PINTER']}</u><br />
	{$lang['TUT_PINTER_DETAIL']}<br /><br />
	
	<u>{$lang['TUT_TOPTEN']}</u><br />
	{$lang['TUT_TOPTEN_DETAIL']}<br /><br />
	
	<u>{$lang['TUT_REFFERAL']}</u><br />
	{$lang['TUT_REFFERAL_DETAIL']}<br /><br />
<hr />
<a name='basics'><h4>{$lang['TUT_PT4']}</h4></a>
	{$lang['TUT_TRAINING']}<br /><br />
	
	<u>{$lang['TUT_GYM']}</u><br />
	{$lang['TUT_GYM_DETAIL']}<br /><br />
	
	<u>{$lang['TUT_GGYM']}</u><br />
	{$lang['TUT_GGYM_DETAIL']}<br /><br />
	
	<u>{$lang['TUT_POWERTRAIN']}</u><br />
	{$lang['TUT_POWERTRAIN_DETAIL']}<br /><br />
	<hr />
	<a name='combat'><h4>{$lang['TUT_PT5']}</h4></a>
	{$lang['TUT_COMBAT']}<br /><br />
	
	<u>{$lang['TUT_ATTACK1']}</u><br />
	{$lang['TUT_ATTACK1_DETAIL']}<br /><br />
	
	<u>{$lang['TUT_ATTACK2']}</u><br />
	{$lang['TUT_ATTACK2_DETAIL']}<br /><br />
	
	<u>{$lang['TUT_ATTACK3']}</u><br />
	{$lang['TUT_ATTACK3_DETAIL']}<br /><br />
	
	<u>{$lang['TUT_ATTACK4']}</u><br />
	{$lang['TUT_ATTACK4_DETAIL']}<br /><br />
	
	<u>{$lang['TUT_ATTACK5']}</u><br />
	{$lang['TUT_ATTACK5_DETAIL']}<br /><br />";
$h->endpage();