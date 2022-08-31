<?php
/*
    File:       func_installer.php
	Created:	Mar 7, 2020 at 8:31:57 PM Eastern Time
	Author:     TheMasterGeneral
	Website:	https://github.com/rykas-productions/chivalry-engine
	MIT License
	Copyright (c) 2020 TheMasterGeneral
	Permission is hereby granted, free of charge, to any person obtaining a copy
	of this software and associated documentation files (the "Software"), to deal
	in the Software without restriction, including without limitation the rights
	to use, copy, modify, merge, publish, distribute, sublicense, and/or sell
	copies of the Software, and to permit persons to whom the Software is
	furnished to do so, subject to the following conditions:
	The above copyright notice and this permission notice shall be included in all
	copies or substantial portions of the Software.
	THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND, EXPRESS OR
	IMPLIED, INCLUDING BUT NOT LIMITED TO THE WARRANTIES OF MERCHANTABILITY,
	FITNESS FOR A PARTICULAR PURPOSE AND NONINFRINGEMENT. IN NO EVENT SHALL THE
	AUTHORS OR COPYRIGHT HOLDERS BE LIABLE FOR ANY CLAIM, DAMAGES OR OTHER
	LIABILITY, WHETHER IN AN ACTION OF CONTRACT, TORT OR OTHERWISE, ARISING FROM,
	OUT OF OR IN CONNECTION WITH THE SOFTWARE OR THE USE OR OTHER DEALINGS IN THE
	SOFTWARE.
*/
function refuseInstall()
{
    if (file_exists('./installer.lock'))
        exit;
}
function getInstallVersion()
{
    return "3.0.0-alpha2";
}
function loadFunc()
{
    include('func_startup.php');
    include('func_escape.php');
    include('func_template.php');
    include('func_format.php');
    include('func_alerts.php');
}
function doInstallerChecks()
{
    $op=array();
    $op['phpValidVersion'] = (version_compare(phpversion(), '7.2.0') > 0) ? true : false;
    $op['writable'] = (is_writable('./')) ? true : false;
    $op['openssl'] = (function_exists('openssl_random_pseudo_bytes')) ? true : false;
    $op['password'] = (function_exists('password_hash')) ? true : false;
    $op['pdo'] = (function_exists('pdo_mysql')) ? true : false;
    $op['mysqli'] = (function_exists('mysqli_connect')) ? true : false;
    $op['curl'] = (function_exists('curl_init')) ? true : false;
    $op['fopen'] = (function_exists('fopen')) ? true : false;
    return $op;
}

function checkPass($bool)
{
    if ($bool == true)
        return "<span class='text-success'>Test passed.</span>";
    else
        return "<b class='text-danger'>Test failed.</b>";
    
}

//Installer Styling functions
function startHeaders()
{
    global $set;
        ?>
		<!DOCTYPE html>
			<html>
				<head>
				<!-- Standard Meta -->
				<meta charset="utf-8">
				<meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
				<!-- Site Properties -->
				<title>Chivalry Engine Installer</title>
    	<?php
    	loadCSS();
    	loadTopMenu();
}
	
function loadCSS()
{
	echo '<link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/5.0.0-alpha1/css/bootstrap.min.css" integrity="sha384-r4NyP46KrjDleawBgD5tp8Y7UzmLA05oM1iAEQ17CSuDqnUK2+k9luXQOfXJCJ4I" crossorigin="anonymous">
	<style type="text/css">
		main > .container 
		{
			padding: 60px 15px 0;
		}
	</style>';
}

function loadJS()
{
	echo '
	<script src="https://cdn.jsdelivr.net/npm/popper.js@1.16.0/dist/umd/popper.min.js" integrity="sha384-Q6E9RHvbIyZFJoft+2mJbHaEWldlvI9IOYy5n3zV9zzTtmI3UksdQRVvoxMfooAo" crossorigin="anonymous"></script>
	<script src="https://stackpath.bootstrapcdn.com/bootstrap/5.0.0-alpha1/js/bootstrap.min.js" integrity="sha384-oesi62hOLfzrys4LxRF63OJCXdXDipiYWBnvTl9Y9/TRlw5xlKIEHpNyvvDShgf/" crossorigin="anonymous"></script>
	</head>';
}
function loadTopMenu()
{
	global $set;
	echo '
	<header>
		<nav class="navbar navbar-expand-md navbar-dark fixed-top bg-dark">
			<a class="navbar-brand" href="login.php">Chivalry Engine ' . getInstallVersion() . ' Installer</a>
			<button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#navbarCollapse" aria-controls="navbarCollapse" aria-expanded="false" aria-label="Toggle navigation">
			<span class="navbar-toggler-icon"></span>
			</button>
			<div class="collapse navbar-collapse" id="navbarCollapse">
				<ul class="navbar-nav mr-auto">
					<li class="nav-item">
						<a class="nav-link" href="https://github.com/rykas-productions/chivalry-engine/releases">Releases</a>
					</li>
					<li class="nav-item">
						<a class="nav-link" href="https://github.com/rykas-productions/chivalry-engine/tree/v3" tabindex="-1" aria-disabled="true">GitHub</a>
					</li>
				</ul>
			</div>
		</nav>
	</header>
	<body>
	<main role="main" class="flex-shrink-0">
		<div class="container">';
}

function endBody()
{
	echo '</div>
	</body>';
}

function startFooter()
{
	loadJS();
}

function endHeaders()
{
	endBody();
	startFooter();
	//endFooter();
}