<?php
/*
	File: staff/staff_rules.php
	Created: 4/4/2017 at 7:04PM Eastern Time
	Info: Staff panel for creating/editing/deleting in-game rules.
	Author: TheMasterGeneral
	Website: https://github.com/MasterGeneral156/chivalry-engine/
*/
require('sglobals.php');
if ($api->UserMemberLevelGet($userid, 'Admin') == false) {
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