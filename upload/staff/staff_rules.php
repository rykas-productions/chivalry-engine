<?php
/*
	File: 		staff/staff_rules.php
	Created: 	6/23/2019 at 6:11PM Eastern Time
	Info: 		Allows admins to add or remove rules from the game.
	Author: 	TheMasterGeneral
	Website: 	https://github.com/MasterGeneral156/chivalry-engine/
	
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
require('sglobals.php');
if ($api->user->getStaffLevel($userid, 'Admin') == false) {
    alert('danger', "Uh Oh!", "You do not have permission to be here.");
    die($h->endpage());
}
if (!isset($_GET['action'])) {
    $_GET['action'] = '';
}
switch ($_GET['action']) {
    case "addrule":
        addrule();
        break;
    case "editrule":
        editrule();
        break;
    case "delrule":
        delrule();
        break;
    default:
        alert('danger', "Uh Oh!", "Please select a valid action to perform.", true, 'index.php');
        die($h->endpage());
        break;
}
function addrule()
{
    global $db, $userid, $api, $h;
    if (!isset($_POST['rule'])) {
        $csrf = getHtmlCSRF('staff_addrule');
        echo "Use this form to add rules into the game. Be clear and concise. The more difficult language and terminology you use, the less people may understand.<br />
		<form method='post'>
			<textarea name='rule' rows='5' class='form-control'></textarea>
			<input type='submit' class='btn btn-primary' value='Add Rule'>
			{$csrf}
		</form>";
    } else {
        if (empty($_POST['rule'])) {
            alert('danger', "Uh Oh!", "Please specify the rule's text.");
            die($h->endpage());
        } else {
            if (!isset($_POST['verf']) || !checkCSRF('staff_addrule', stripslashes($_POST['verf']))) {
                alert('danger', "Action Blocked!", "This action was blocked for your security. Please fill out the form quicker next time.");
                die($h->endpage());
            }
            $time = time();
            $rule = $db->escape(str_replace("\n", "<br />", strip_tags(stripslashes($_POST['rule']))));
            $db->query("INSERT INTO `gamerules` (`rule_id`, `rule_text`) VALUES (NULL, '{$rule}');");
            alert('success', "Success!", "You have successfully added a new game rule.");
            $api->game->addLog($userid, 'staff', "Created a new rule.");
        }
    }
}

$h->endpage();