<?php
/*
	File: crons/minute.php
	Created: 6/15/2016 at 2:45PM Eastern Time
	Info: Runs the queries below every minute.
	Place queries below that you wish to have rand
	every minute.
	Author: TheMasterGeneral
	Website: https://github.com/MasterGeneral156/chivalry-engine
*/
$menuhide=1;
require_once(__DIR__ .'/../globals_nonauth.php');
if (!isset($argv))
{
    exit;
}
$_GET['code']=substr($argv[1],5);
if (!isset($_GET['code']) || $_GET['code'] !== $_CONFIG['code'])
{
    exit;
}
doAutoMiner();
sleepTick();
runMarketTick(5);   //high risk stock
doEffectTick();
?>
