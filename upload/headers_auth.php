<?php
/*
	File:		headers_auth.php
	Created: 	9/29/2019 at 7:38PM Eastern Time
	Author:		TheMasterGeneral
	Website: 	https://github.com/rykas-productions/chivalry-engine
	MIT License
	Copyright (c) 2019 TheMasterGeneral
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
$set['gameName']='Chivalry Engine v3.0.0-alpha1';
class headers
{
	function startHeaders()
	{
		global $set, $db;
		?>
			<!DOCTYPE html>
				<html>
					<head>
					<!-- Standard Meta -->
					<meta charset="utf-8">
					<meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
					<!-- Site Properties -->
					<title><?php echo $set['gameName']; ?></title>
		<?php
		$this->loadCSS();
		$this->loadTopMenu();
	}
	function loadCSS()
	{
		echo '<link rel="stylesheet" type="text/css" href="assets/css/bootstrap.css">
		<style type="text/css">
			main > .container 
			{
				padding: 60px 15px 0;
			}
		</style>';
	}
	function loadJS()
	{
		echo '<script src="https://code.jquery.com/jquery-3.4.1.min.js"></script>
		<script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.14.7/umd/popper.min.js"></script>
		<script src="assets/js/bootstrap.js"></script>
		</head>';
	}
	function loadTopMenu()
	{
		global $set;
		echo '
		<header>
			<nav class="navbar navbar-expand-md navbar-dark fixed-top bg-dark">
				<a class="navbar-brand" href="index.php">' . $set['gameName'] . '</a>
				<button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#navbarCollapse" aria-controls="navbarCollapse" aria-expanded="false" aria-label="Toggle navigation">
				<span class="navbar-toggler-icon"></span>
				</button>
				<div class="collapse navbar-collapse" id="navbarCollapse">
					<ul class="navbar-nav mr-auto">
						<li class="nav-item">
							<a class="nav-link" href="explore.php">Explore</a>
						</li>
						<li class="nav-item">
							<a class="nav-link" href="inventory.php">Inventory</a>
						</li>
					</ul>
				</div>
			</nav>
		</header>
		<body>
		<main role="main" class="flex-shrink-0">
			<div class="container">';
			$this->loadStatBar();
	}
	function endBody()
	{
		echo '</div>
		</body>';
	}
	function startFooter()
	{
		$this->loadJS();
	}
	function endFooter()
	{
		
	}
	function endHeaders()
	{
		$this->endBody();
		$this->startFooter();
		$this->endFooter();
	}
	function loadStatBar()
	{
		global $ir;
		$accBar="{$ir['username']} [{$ir['userid']}]<br />
		Level: {$ir['level']}<br />
		Experience: {$ir['experience']}<br />
		Primary Currency: {$ir['primaryCurrencyHeld']}<br />
		[<a href='logout.php'>Logout</a>]";
		$infoBar=returnFormattedUnreadMail() . "" . returnFormattedInfirmary();
		$statBar=createProgressBarLabel(returnPercentage($ir['energy'], $ir['maxEnergy']), "Energy: {$ir['energy']} / {$ir['maxEnergy']}") . "<br />" .
		createProgressBarLabel(returnPercentage($ir['brave'], $ir['maxBrave']), "Brave: {$ir['brave']} / {$ir['maxBrave']}") . "<br />" .
		createProgressBarLabel(returnPercentage($ir['will'], $ir['maxWill']), "Will: {$ir['will']} / {$ir['maxWill']}") . "<br />" .
		createProgressBarLabel(returnPercentage($ir['hp'], $ir['maxHP']), "HP: {$ir['hp']} / {$ir['maxHP']}");
		createThreeCols(createTitlelessCard($accBar), createTitlelessCard($infoBar), createTitlelessCard($statBar));
		echo "<hr />";
		
	}
}