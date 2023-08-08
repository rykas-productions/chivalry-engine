<?php
/*
	File:		login.php
	Created: 	9/22/2019 at 5:01PM Eastern Time
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
require('globals_nonauth.php');
echo "
<div class='row'>
	<div class='col-sm-4'>
		<div class='card'>
			<div class='card-header bg-dark text-white'>
				Log In Today!
			</div>
			<div class='card-body'>";
				createPostForm('authenticate.php',array(array('email','email','Email Address'), array('password','password','Password')), 'Log In');
			echo 
			"</div>
		</div>
	</div>
	<div class='col-sm-8'>
		<div class='card'>
			<div class='card-header bg-dark text-white'>
				Game Info
			</div>
			<div class='card-body'>
				This is a placeholder text for Chivalry Engine V3, Alpha 1
			</div>
		</div>
	</div>
</div>";
$h->endHeaders();