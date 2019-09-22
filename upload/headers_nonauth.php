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
					<meta charset="utf-8" />
					<meta http-equiv="X-UA-Compatible" content="IE=edge,chrome=1" />
					<meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0">
					<!-- Site Properties -->
					<title><?php echo $set['gameName']; ?></title>
		<?php
		$this->loadCSS();
		$this->loadTopMenu();
	}
	function loadCSS()
	{
		echo '<link rel="stylesheet" type="text/css" href="semantic/semantic.min.css">
		<style type="text/css">
			.main.container 
			{
				margin-top: 7em;
			}
		</style>';
	}
	function loadJS()
	{
		echo '<script src="https://code.jquery.com/jquery-3.1.1.min.js"></script>
			<script src="semantic/semantic.min.js"></script>';
	}
	function loadTopMenu()
	{
		global $set;
		echo '
		</head>
		<body>
			<div class="ui fixed inverted menu">
				<div class="ui container">
					<a href="#" class="header item">
						<img class="logo" src="assets/images/logo.png">' . $set['gameName'] . '
					</a>
					<a href="login.php" class="item">Login</a>
					<a href="register.php" class="item">Register</a>
				</div>
			</div>
			<div class="ui main text container">';
	}
	function endBody()
	{
		echo '</div></body>';
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