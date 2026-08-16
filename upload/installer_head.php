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
			<meta name="viewport" content="width=device-width, initial-scale=1">
			<title>Chivalry Engine v<?php echo $Version; ?> Installer</title>
			<!-- CSS -->
			<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-sRIl4kxILFvY47J16cr9ZwB07vP4J8+LH7qKQnuqkuIAvNWLzeN8tE5YBujZqJLB" crossorigin="anonymous">
			<meta name="theme-color" content="#e7e7e7">
		</head>
		<body>
			<div class="container">
				<div class="row">
					<div class="col-12 text-center">
