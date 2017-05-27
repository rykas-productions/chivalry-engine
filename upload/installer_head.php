<?php
/*
	File:		installer_head.php
	Created: 	4/5/2016 at 12:13AM Eastern Time
	Info: 		The header for the installer. Gets deleted after 
				completing the install.
	Author:		TheMasterGeneral
	Website: 	https://github.com/MasterGeneral156/chivalry-engine
*/
if (!defined('MONO_ON'))
{
    exit;
}
ob_start();
?>
<!DOCTYPE html>
	<html lang="en">
		<head>
			<center>
			<meta charset="utf-8">
			<meta http-equiv="X-UA-Compatible" content="IE=edge">
			<meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
			<title>Chivalry Engine Installer</title>
			<!-- CSS -->
			<link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.0.0-alpha.6/css/bootstrap.min.css" integrity="sha384-rwoIResjU2yc3z8GV/NPeZWAv56rSmLldC3R/AZzGRnGxQQKnKkoFVhFQhNUwEyJ" crossorigin="anonymous">
			<meta name="theme-color" content="#e7e7e7">
			<link rel="stylesheet" href="https://code.jquery.com/ui/1.11.4/themes/smoothness/jquery-ui.min.css">
			<link rel="stylesheet" href="css/bs2.css">
			<link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/font-awesome/4.5.0/css/font-awesome.min.css">
		</head>
		<body>
			<div class="container">
				<div class="row">
					<div class="col-sm-12 text-center">