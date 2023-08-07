<?php
/*
	File:		headers_nonauth.php
	Created: 	9/22/2019 at 4:29PM Eastern Time
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
class headers
{
	function startHeaders()
	{
	    $set['gameName']='Chivalry Engine ' . getEngineVersion();
		?>
			<!DOCTYPE html>
				<html>
					<head>
					<!-- Standard Meta -->
					<meta charset="utf-8">
					<meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
					<!-- Site Properties -->
					<title><?php echo returnGameTitle(); ?></title>
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
	    $set['gameName']='Chivalry Engine ' . getEngineVersion();
		echo '
		<header>
			<nav class="navbar navbar-expand-md navbar-dark fixed-top bg-dark">
				<a class="navbar-brand" href="login.php">' . $set['gameName'] . '</a>
				<button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#navbarCollapse" aria-controls="navbarCollapse" aria-expanded="false" aria-label="Toggle navigation">
				<span class="navbar-toggler-icon"></span>
				</button>
				<div class="collapse navbar-collapse" id="navbarCollapse">
					<ul class="navbar-nav mr-auto">
						<li class="nav-item">
							<a class="nav-link" href="register.php">Register</a>
						</li>
						<li class="nav-item">
							<a class="nav-link" href="gamerules.php" tabindex="-1" aria-disabled="true">Game Rules</a>
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
}