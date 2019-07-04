<?php
/*
	File:		installer_foot.php
	Created: 	6/23/2019 at 6:11PM Eastern Time
	Info: 		The footer for the Chivalry Engine installer.
	Author:		TheMasterGeneral
	Website: 	https://github.com/MasterGeneral156/chivalry-engine
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
if (!defined('MONO_ON'))
{
    exit;
}
?>
		</div>
	</div>
</div>
<link rel="stylesheet" href="https://code.jquery.com/ui/1.11.4/themes/smoothness/jquery-ui.min.css">
<link rel="stylesheet" href="css/game.css">
<link rel="stylesheet" href="https://use.fontawesome.com/releases/v5.7.1/css/all.css">

<!-- jQuery Version 3.4.0 -->
<script src="https://code.jquery.com/jquery-3.4.0.min.js"></script>

<!-- Core Bootstrap Javascript -->
<script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.14.7/umd/popper.min.js"></script>
<script src="https://stackpath.bootstrapcdn.com/bootstrap/4.3.1/js/bootstrap.min.js"></script>

<!-- Other JavaScript -->
<script src="js/game.js"></script>
<script defer src="https://use.fontawesome.com/releases/v5.0.10/js/all.js"></script>
</body>
<footer>
    <p>
        <br />
        <?php
        echo "<hr />
					Time is now " . date('F j, Y') . " " . date('g:i:s a') . "<br />
					Powered with codes by TheMasterGeneral. View source on <a href='https://github.com/MasterGeneral156/chivalry-engine'>Github</a>.";
        ?>
        &copy; <?php echo date("Y");
        ?>
    </p>
</footer>
</html>