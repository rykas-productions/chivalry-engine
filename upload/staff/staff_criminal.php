<?php
/*
	File: staff/staff_criminal.php
	Created: 4/4/2017 at 7:01PM Eastern Time
	Info: Staff panel for handling the criminal actions in-game.
	Author: TheMasterGeneral
	Website: https://github.com/MasterGeneral156/chivalry-engine/
*/
require_once('sglobals.php');
echo "<h3>Staff Criminal Center</h3><hr />";
if ($ir['user_level'] != "Admin") {
    alert('danger', "Uh Oh!", "You do not have permission to be here.", true, 'index.php');
    die($h->endpage());
}
if (!isset($_GET['action'])) {
    $_GET['action'] = '';
}
switch ($_GET['action']) {
    case 'newcrime':
        new_crime();
        break;
    case 'editcrime':
        edit_crime();
        break;
    case 'delcrime':
        delcrime();
        break;
    case 'newcrimegroup':
        new_crimegroup();
        break;
    case 'editcrimegroup':
        edit_crimegroup();
        break;
    case 'delcrimegroup':
        delcrimegroup();
        break;
    case 'reorder':
        reorder_crimegroups();
        break;
    default:
        home();
        break;
}
function home()
{
    echo "
	<a href='?action=newcrimegroup'>Create Crime Group</a><br />
	<a href='?action=newcrime'>Create Crime</a><br />
	<a href='?action=editcrime'>Edit Crime</a><br />
	<a href='?action=delcrime'>Delete Crime</a><br />
	<a href='?action=editcrimegroup'>Edit Crime Group</a><br />
	<a href='?action=delcrimegroup'>Delete Crime Group</a><br />
	";
}

function new_crime()
{
    global $db, $userid, $api, $h;
    if (!isset($_POST['name'])) {
        $csrf = request_csrf_html('staff_newcrime');
        echo "Adding a new Crime<br />
		<form method='post'>
			<table class='table table-bordered'>
				<tr>
					<th width='33%'>
						Crime Name
					</th>
					<td>
						<input type='text' class='form-control' required='1' name='name' />
					</td>
				</tr>
				<tr>
					<th>
						Bravery Cost
					</th>
					<td>
						<input type='number' min='1' class='form-control' required='1' name='brave' />
					</td>
				</tr>
				<tr>
					<th>
						Success Formula
					</th>
					<td>
						<input type='text' class='form-control' required='1' placeholder='((WILL*0.8)/2.5)+(LEVEL/4)' value='((WILL*0.8)/2.5)+(LEVEL/4)' name='percform' />
					</td>
				</tr>
				<tr>
					<th>
						Success Minimum Copper Coins
					</th>
					<td>
						<input type='number' min='0' class='form-control' required='1' name='PRICURMIN' />
					</td>
				</tr>
				<tr>
					<th>
						Success Maximum Copper Coins
					</th>
					<td>
						<input type='number' min='0' class='form-control' required='1' name='PRICURMAX' />
					</td>
				</tr>
				<tr>
					<th>
						Success Minimum Chivalry Tokens
					</th>
					<td>
						<input type='number' min='0' class='form-control' required='1' name='SECURMIN' />
					</td>
				</tr>
				<tr>
					<th>
						Success Maximum Seconary Currency
					</th>
					<td>
						<input type='number' min='0' class='form-control' required='1' name='SECURMAX' />
					</td>
				</tr>
				<tr>
					<th>
						Success Item
					</th>
					<td>
						" . item_dropdown('item') . "
					</td>
				</tr>
				<tr>
					<th>
						Crime Group
					</th>
					<td>
						" . crimegroup_dropdown('group') . "
					</td>
				</tr>
				<tr>
					<th>
						Initial Text
					</th>
					<td>
						<textarea class='form-control' name='itext' placeholder='Shown when you start the crime.' required='1'></textarea>
					</td>
				</tr>
				<tr>
					<th>
						Success Text
					</th>
					<td>
						<textarea class='form-control' name='stext' placeholder='Shown when you succeed.' required='1'></textarea>
					</td>
				</tr>
				<tr>
					<th>
						Failure Text
					</th>
					<td>
						<textarea class='form-control' name='jtext' placeholder='Shown when you fail.' required='1'></textarea>
					</td>
				</tr>
				<tr>
					<th>
						Minimum Dungeon Time
					</th>
					<td>
						<input type='number' min='0' class='form-control' required='1' name='jtimemin' />
					</td>
				</tr>
				<tr>
					<th>
						Maximum Dungeon Time
					</th>
					<td>
						<input type='number' min='0' class='form-control' required='1' name='jtimemax' />
					</td>
				</tr>
				<tr>
					<th>
						Dungeon Reason
					</th>
					<td>
						<input type='text' class='form-control' required='1' name='jreason' />
					</td>
				</tr>
				<tr>
					<th>
						Success Experience Points
					</th>
					<td>
						<input type='number' min='1' class='form-control' required='1' name='xp' />
					</td>
				</tr>
				<td colspan='2'>
					<input type='submit' value='Create Crime' class='btn btn-primary'>
				</td>
				{$csrf}
			</table>
		</form>";
    } else {
        $_POST['name'] = (isset($_POST['name']) && preg_match("/^[a-z0-9_]+([\\s]{1}[a-z0-9_]|[a-z0-9_])*$/i", $_POST['name'])) ? $db->escape(strip_tags(stripslashes($_POST['name']))) : '';
        $_POST['brave'] = (isset($_POST['brave']) && is_numeric($_POST['brave'])) ? abs(intval($_POST['brave'])) : '';
        $_POST['percform'] = (isset($_POST['percform']) && preg_match("/^[a-z0-9\p{Sm}\s()*.\/-_]+([\\s]{1}[a-z0-9\p{Sm}\s()*.\/-_]|[a-z0-9\p{Sm}\s()*.\/-_])*$/i", $_POST['percform'])) ? $db->escape(strip_tags(stripslashes($_POST['percform']))) : '';
        $_POST['PRICURMAX'] = (isset($_POST['PRICURMAX']) && is_numeric($_POST['PRICURMAX'])) ? abs(intval($_POST['PRICURMAX'])) : 0;
        $_POST['PRICURMIN'] = (isset($_POST['PRICURMIN']) && is_numeric($_POST['PRICURMIN'])) ? abs(intval($_POST['PRICURMIN'])) : 0;
        $_POST['SECURMAX'] = (isset($_POST['SECURMAX']) && is_numeric($_POST['SECURMAX'])) ? abs(intval($_POST['SECURMAX'])) : 0;
        $_POST['SECURMIN'] = (isset($_POST['SECURMIN']) && is_numeric($_POST['SECURMIN'])) ? abs(intval($_POST['SECURMIN'])) : 0;
        $_POST['item'] = (isset($_POST['item']) && is_numeric($_POST['item'])) ? abs(intval($_POST['item'])) : 0;
        $_POST['group'] = (isset($_POST['group']) && is_numeric($_POST['group'])) ? abs(intval($_POST['group'])) : '';
        $_POST['itext'] = (isset($_POST['itext'])) ? $db->escape(strip_tags(stripslashes($_POST['itext']))) : '';
        $_POST['stext'] = (isset($_POST['stext'])) ? $db->escape(strip_tags(stripslashes($_POST['stext']))) : '';
        $_POST['jtext'] = (isset($_POST['jtext'])) ? $db->escape(strip_tags(stripslashes($_POST['jtext']))) : '';
        $_POST['jtimemin'] = (isset($_POST['jtimemin']) && is_numeric($_POST['jtimemin'])) ? abs(intval($_POST['jtimemin'])) : '';
        $_POST['jtimemax'] = (isset($_POST['jtimemax']) && is_numeric($_POST['jtimemax'])) ? abs(intval($_POST['jtimemax'])) : '';
        $_POST['jreason'] = (isset($_POST['jreason']) && preg_match("/^[a-z0-9_]+([\\s]{1}[a-z0-9_]|[a-z0-9_])*$/i", $_POST['jreason'])) ? $db->escape(strip_tags(stripslashes($_POST['jreason']))) : '';
        $_POST['xp'] = (isset($_POST['xp']) && is_numeric($_POST['xp'])) ? abs(intval($_POST['xp'])) : '';
        if (empty($_POST['name']) || empty($_POST['brave']) || empty($_POST['percform'])
            || empty($_POST['group']) || empty($_POST['itext']) || empty($_POST['stext'])
            || empty($_POST['jtext']) || empty($_POST['jtimemin']) || empty($_POST['jtimemax'])
            || empty($_POST['jreason']) || empty($_POST['xp'])
        ) {
            alert('danger', "Uh Oh!", "You are missing one or more required inputs on the previous form.");
            die($h->endpage());
        }
        if (!isset($_POST['verf']) || !verify_csrf_code('staff_newcrime', stripslashes($_POST['verf']))) {
            alert('danger', "Action Blocked!", "We have blocked this action for your security. Forms expire quickly. Go back and try again!");
            die($h->endpage());
        }
        if (!empty($_POST['item'])) {
            $qi = $db->query("SELECT COUNT(`itmid`) FROM `items` WHERE `itmid` = {$_POST['item']}");
            $exist_check = $db->fetch_single($qi);
            $db->free_result($qi);
            if ($exist_check == 0) {
                alert('danger', "Uh Oh!", "The success item you've chosen does not exist.");
                die($h->endpage());
            }
        }
        $db->query("INSERT INTO `crimes` (`crimeNAME`, `crimeBRAVE`,
		`crimePERCFORM`, `crimePRICURMIN`, `crimePRICURMAX`, `crimeSECCURMIN`, 
		`crimeSECURMAX`, `crimeITEMSUC`, `crimeGROUP`, `crimeITEXT`, `crimeSTEXT`, 
		`crimeFTEXT`, `crimeDUNGMIN`, `crimeDUNGMAX`, `crimeDUNGREAS`, `crimeXP`) 
		VALUES ('{$_POST['name']}', '{$_POST['brave']}', '{$_POST['percform']}', 
		'{$_POST['PRICURMIN']}', '{$_POST['PRICURMAX']}', '{$_POST['SECURMIN']}', 
		'{$_POST['SECURMAX']}', '{$_POST['item']}', '{$_POST['group']}', '{$_POST['itext']}', 
		'{$_POST['stext']}', '{$_POST['jtext']}', '{$_POST['jtimemin']}', 
		'{$_POST['jtimemax']}', '{$_POST['jreason']}', '{$_POST['xp']}');");
        alert('success', "Success!", "You have successfully created the {$_POST['name']} crime.", true, 'index.php');
        $api->SystemLogsAdd($userid, 'staff', "Created crime {$_POST['name']}");
    }
}

function edit_crime()
{
    global $db, $userid, $h, $api;
    if (!isset($_POST['step'])) {
        $_POST['step'] = 0;
    }
    if ($_POST['step'] == 0) {
        $csrf = request_csrf_html('staff_editcrime1');
        echo "<form action='?action=editcrime' method='post'>";
        echo "
		<table class='table table-bordered'>
			<tr>
				<th colspan='2'>
					Select a crime you wish to edit.
				</th>
			</tr>
			<tr>
				<th>
					Crime
				</th>
				<td>
					" . crime_dropdown('crime') . "
				</td>
			</tr>
			<tr>
				<td colspan='2'>
					<input type='submit' class='btn btn-primary' value='Edit Crime'>
				</td>
			</tr>
		</table>
		<input type='hidden' name='step' value='1'>";
        echo $csrf . "</form>";
    }
    if ($_POST['step'] == 1) {
        $_POST['crime'] = (isset($_POST['crime']) && is_numeric($_POST['crime'])) ? abs(intval($_POST['crime'])) : '';
        if (!isset($_POST['verf']) || !verify_csrf_code('staff_editcrime1', stripslashes($_POST['verf']))) {
            alert('danger', "Action Blocked!", "We have blocked this action for your security. Forms expire quickly. Go back and try again!");
            die($h->endpage());
        }
        if (empty($_POST['crime'])) {
            alert('danger', "Uh Oh!", "Please specify a crime you wish to edit.");
            die($h->endpage());
        }
        $d = $db->query("SELECT * FROM `crimes` WHERE `crimeID` = {$_POST['crime']}");
        if ($db->num_rows($d) == 0) {
            $db->free_result($d);
            alert('danger', "Uh Oh!", "The crime you're trying to edit does not exist.");
            die($h->endpage());
        }
        $itemi = $db->fetch_row($d);
        $db->free_result($d);
        $csrf = request_csrf_html('staff_editcrime2');
        echo "Edit Crime Form<br />
		<form method='post'>
			<table class='table table-bordered'>
				<tr>
					<th width='33%'>
						Crime Name
					</th>
					<td>
						<input type='text' class='form-control' required='1' name='name' value='{$itemi['crimeNAME']}' />
					</td>
				</tr>
				<tr>
					<th>
						Bravery Cost
					</th>
					<td>
						<input type='number' min='1' class='form-control' required='1' name='brave' value='{$itemi['crimeBRAVE']}' />
					</td>
				</tr>
				<tr>
					<th>
						Success Formula
					</th>
					<td>
						<input type='text' class='form-control' required='1' value='{$itemi['crimePERCFORM']}' name='percform' />
					</td>
				</tr>
				<tr>
					<th>
						Success Minimum Copper Coins
					</th>
					<td>
						<input type='number' min='0' class='form-control' required='1' name='PRICURMIN' value='{$itemi['crimePRICURMIN']}' />
					</td>
				</tr>
				<tr>
					<th>
						Success Maximum Copper Coins
					</th>
					<td>
						<input type='number' min='0' class='form-control' required='1' name='PRICURMAX' value='{$itemi['crimePRICURMAX']}' />
					</td>
				</tr>
				<tr>
					<th>
						Sucess Minimum Chivalry Tokens
					</th>
					<td>
						<input type='number' min='0' class='form-control' required='1' name='SECURMIN' value='{$itemi['crimeSECCURMIN']}' />
					</td>
				</tr>
				<tr>
					<th>
						Success Maximum Chivalry Tokens
					</th>
					<td>
						<input type='number' min='0' class='form-control' required='1' name='SECURMAX' value='{$itemi['crimeSECURMAX']}' />
					</td>
				</tr>
				<tr>
					<th>
						Success Item
					</th>
					<td>
						" . item_dropdown('item', $itemi['crimeITEMSUC']) . "
					</td>
				</tr>
				<tr>
					<th>
						Crime Group
					</th>
					<td>
						" . crimegroup_dropdown('group', $itemi['crimeGROUP']) . "
					</td>
				</tr>
				<tr>
					<th>
						Initial Text
					</th>
					<td>
						<textarea class='form-control' name='itext' placeholder='Shown when you start the crime.' required='1'>{$itemi['crimeITEXT']}</textarea>
					</td>
				</tr>
				<tr>
					<th>
						Success Text
					</th>
					<td>
						<textarea class='form-control' name='stext' placeholder='Shown when you succeed the crime.' required='1'>{$itemi['crimeSTEXT']}</textarea>
					</td>
				</tr>
				<tr>
					<th>
						Failure Text
					</th>
					<td>
						<textarea class='form-control' name='jtext' placeholder='Shown when you fail the crime.' required='1'>{$itemi['crimeFTEXT']}</textarea>
					</td>
				</tr>
				<tr>
					<th>
						Minimum Dungeon Time
					</th>
					<td>
						<input type='number' min='0' class='form-control' required='1' name='jtimemin' value='{$itemi['crimeDUNGMIN']}' />
					</td>
				</tr>
				<tr>
					<th>
						Maximum Dungeon Time
					</th>
					<td>
						<input type='number' min='0' class='form-control' required='1' name='jtimemax' value='{$itemi['crimeDUNGMAX']}' />
					</td>
				</tr>
				<tr>
					<th>
						Dungeon Reason
					</th>
					<td>
						<input type='text' class='form-control' required='1' name='jreason' value='{$itemi['crimeDUNGREAS']}' />
					</td>
				</tr>
				<tr>
					<th>
						Success Experience
					</th>
					<td>
						<input type='number' min='1' class='form-control' required='1' name='xp' value='{$itemi['crimeXP']}' />
					</td>
				</tr>
				<td colspan='2'>
					<input type='submit' value='Edit Crime' class='btn btn-primary'>
				</td>
				{$csrf}
			</table>
			<input type='hidden' name='crimeID' value='{$_POST['crime']}' />
			<input type='hidden' name='step' value='2' />
		</form>";
    }
    if ($_POST['step'] == 2) {
        $_POST['name'] = (isset($_POST['name']) && preg_match("/^[a-z0-9_]+([\\s]{1}[a-z0-9_]|[a-z0-9_])*$/i", $_POST['name'])) ? $db->escape(strip_tags(stripslashes($_POST['name']))) : '';
        $_POST['brave'] = (isset($_POST['brave']) && is_numeric($_POST['brave'])) ? abs(intval($_POST['brave'])) : '';
        $_POST['crimeID'] = (isset($_POST['crimeID']) && is_numeric($_POST['crimeID'])) ? abs(intval($_POST['crimeID'])) : '';
        $_POST['percform'] = (isset($_POST['percform']) && preg_match("/^[a-z0-9\p{Sm}\s()*.\/-_]+([\\s]{1}[a-z0-9\p{Sm}\s()*.\/-_]|[a-z0-9\p{Sm}\s()*.\/-_])*$/i", $_POST['percform'])) ? $db->escape(strip_tags(stripslashes($_POST['percform']))) : '';
        $_POST['PRICURMAX'] = (isset($_POST['PRICURMAX']) && is_numeric($_POST['PRICURMAX'])) ? abs(intval($_POST['PRICURMAX'])) : 0;
        $_POST['PRICURMIN'] = (isset($_POST['PRICURMIN']) && is_numeric($_POST['PRICURMIN'])) ? abs(intval($_POST['PRICURMIN'])) : 0;
        $_POST['SECURMAX'] = (isset($_POST['SECURMAX']) && is_numeric($_POST['SECURMAX'])) ? abs(intval($_POST['SECURMAX'])) : 0;
        $_POST['SECURMIN'] = (isset($_POST['SECURMIN']) && is_numeric($_POST['SECURMIN'])) ? abs(intval($_POST['SECURMIN'])) : 0;
        $_POST['item'] = (isset($_POST['item']) && is_numeric($_POST['item'])) ? abs(intval($_POST['item'])) : 0;
        $_POST['group'] = (isset($_POST['group']) && is_numeric($_POST['group'])) ? abs(intval($_POST['group'])) : '';
        $_POST['itext'] = (isset($_POST['itext'])) ? $db->escape(strip_tags(stripslashes($_POST['itext']))) : '';
        $_POST['stext'] = (isset($_POST['stext'])) ? $db->escape(strip_tags(stripslashes($_POST['stext']))) : '';
        $_POST['jtext'] = (isset($_POST['jtext'])) ? $db->escape(strip_tags(stripslashes($_POST['jtext']))) : '';
        $_POST['jtimemin'] = (isset($_POST['jtimemin']) && is_numeric($_POST['jtimemin'])) ? abs(intval($_POST['jtimemin'])) : '';
        $_POST['jtimemax'] = (isset($_POST['jtimemax']) && is_numeric($_POST['jtimemax'])) ? abs(intval($_POST['jtimemax'])) : '';
        $_POST['jreason'] = (isset($_POST['jreason']) && preg_match("/^[a-z0-9_]+([\\s]{1}[a-z0-9_]|[a-z0-9_])*$/i", $_POST['jreason'])) ? $db->escape(strip_tags(stripslashes($_POST['jreason']))) : '';
        $_POST['xp'] = (isset($_POST['xp']) && is_numeric($_POST['xp'])) ? abs(intval($_POST['xp'])) : '';
        if (empty($_POST['name']) || empty($_POST['brave']) || empty($_POST['percform']) || empty($_POST['group']) || empty($_POST['itext']) || empty($_POST['stext'])
            || empty($_POST['jtext']) || empty($_POST['jtimemin']) || empty($_POST['jtimemax']) || empty($_POST['jreason']) || empty($_POST['xp'])
        ) {
            alert('danger', "Uh Oh!", "You are missing one or more of the required inputs on the previous form.");
            die($h->endpage());
        }
        if (empty($_POST['crimeID'])) {
            alert('danger', "Uh Oh!", "Please specify the crime you wish to edit.");
            die($h->endpage());
        }
        if (!isset($_POST['verf']) || !verify_csrf_code('staff_editcrime2', stripslashes($_POST['verf']))) {
            alert('danger', "Action Blocked!", "We have blocked this action for your security. Forms expire quickly. Go back and try again!");
            die($h->endpage());
        }
        if (!empty($_POST['item'])) {
            $qi = $db->query("SELECT COUNT(`itmid`) FROM `items` WHERE `itmid` = {$_POST['item']}");
            $exist_check = $db->fetch_single($qi);
            $db->free_result($qi);
            if ($exist_check == 0) {
                alert('danger', "Uh Oh!", "The crime you are trying to edit does not exist.");
                die($h->endpage());
            }
        }
        $db->query(
            "UPDATE `crimes`
             SET `crimeNAME` = '{$_POST['name']}', `crimeBRAVE` = '{$_POST['brave']}',
             `crimePERCFORM` = '{$_POST['percform']}', `crimePRICURMIN` = '{$_POST['PRICURMIN']}',
			 `crimePRICURMAX` = '{$_POST['PRICURMAX']}', `crimeSECCURMIN` = '{$_POST['SECURMIN']}',
			 `crimeSECURMAX` = '{$_POST['SECURMAX']}', `crimeITEMSUC` = {$_POST['item']},
             `crimeGROUP` = '{$_POST['group']}', `crimeITEXT` = '{$_POST['itext']}',
             `crimeSTEXT` = '{$_POST['stext']}', `crimeFTEXT` = '{$_POST['jtext']}',
             `crimeDUNGREAS` = '{$_POST['jreason']}', `crimeDUNGMIN` = {$_POST['jtimemin']},
			 `crimeDUNGMAX` = {$_POST['jtimemax']}, `crimeXP` = {$_POST['xp']}
             WHERE `crimeID` = {$_POST['crimeID']}");
        alert('success', "Success!", "You have successfully edited the {$_POST['name']} crime.", true, 'index.php');
        $api->SystemLogsAdd($userid, 'staff', "Edited crime {$_POST['name']}");
    }
}

function delcrime()
{
    global $db, $userid, $api, $h;
    if (isset($_POST['crime'])) {
        $_POST['crime'] = (isset($_POST['crime']) && is_numeric($_POST['crime'])) ? abs(intval($_POST['crime'])) : '';
        if (!isset($_POST['verf']) || !verify_csrf_code('staff_delcrime', stripslashes($_POST['verf']))) {
            alert('danger', "Action Blocked!", "We have blocked this action for your security. Forms expire quickly. Go back and try again!");
            die($h->endpage());
        }
        if (empty($_POST['crime'])) {
            alert('danger', "Uh Oh!", "Please specify a crime you wish to delete.");
            die($h->endpage());
        }
        $d = $db->query("SELECT * FROM `crimes` WHERE `crimeID` = {$_POST['crime']}");
        if ($db->num_rows($d) == 0) {
            $db->free_result($d);
            alert('danger', "Uh Oh!", "You are trying to delete a non-existent crime.");
            die($h->endpage());
        }
        $db->query("DELETE FROM `crimes` WHERE `crimeID` = {$_POST['crime']}");
        alert('success', "Success!", "You have successfully deleted Crime ID #{$_POST['crime']}.", true, 'index.php');
        $api->SystemLogsAdd($userid, 'staff', "Deleted Crime ID {$_POST['crime']}.");

    } else {
        $csrf = request_csrf_html('staff_delcrime');
        echo "<form method='post'>
		<table class='table table-bordered'>
			<tr>
				<th colspan='2'>
					Select the crime you wish to delete from the game.
				</th>
			</tr>
			<tr>
				<th>
					Crime
				</th>
				<td>
					" . crime_dropdown('crime') . "
				</td>
			</tr>
			<tr>
				<td colspan='2'>
					<input type='submit' value='Delete Crime' class='btn btn-primary'>
				</td>
			</tr>
			{$csrf}
		</table>
		</form>";
    }
}

function new_crimegroup()
{
    global $db, $h, $api, $userid;
    if (isset($_POST['cgNAME'])) {
        $_POST['cgNAME'] = (isset($_POST['cgNAME']) && preg_match("/^[a-z0-9_]+([\\s]{1}[a-z0-9_]|[a-z0-9_])+$/i", $_POST['cgNAME'])) ? $db->escape(strip_tags(stripslashes($_POST['cgNAME']))) : '';
        $_POST['cgORDER'] = (isset($_POST['cgORDER']) && is_numeric($_POST['cgORDER'])) ? abs(intval($_POST['cgORDER'])) : '';
        if (empty($_POST['cgNAME']) || empty($_POST['cgORDER'])) {
            alert('danger', "Uh Oh!", "Please fill out the form before submitting it.");
            die($h->endpage());
        }
        if (!isset($_POST['verf']) || !verify_csrf_code('staff_newcrimegroup', stripslashes($_POST['verf']))) {
            alert('danger', "Action Blocked!", "We have blocked this action for your security. Forms expire quickly. Go back and try again!");
            die($h->endpage());
        }
        $d = $db->query("SELECT COUNT(`cgID`) FROM `crimegroups` WHERE `cgORDER` = {$_POST['cgORDER']}");
        if ($db->fetch_single($d) > 0) {
            $db->free_result($d);
            alert('danger', "Uh Oh!", "You cannot have more than one crime group with the same order number.");
            die($h->endpage());
        }
        $db->free_result($d);
        $db->query("INSERT INTO `crimegroups` (`cgNAME`, `cgORDER`) VALUES('{$_POST['cgNAME']}', '{$_POST['cgORDER']}')");
        alert('success', "Success!", "You have successfully created the {$_POST['cgNAME']} Crime Group.", true, 'index.php');
        $api->SystemLogsAdd($userid, 'staff', "Created Crime Group {$_POST['cgNAME']}");

    } else {
        $csrf = request_csrf_html('staff_newcrimegroup');
        echo "Adding a new crime group<br />
		<form method='post'>
			<table class='table table-bordered'>
				<tr>
					<th>
						Crime Group Name
					</th>
					<td>
						<input type='text' name='cgNAME' class='form-control' required='1'>
					</td>
				</tr>
				<tr>
					<th>
						Crime Group Order
					</th>
					<td>
						<input type='number' name='cgORDER' min='0' class='form-control' required='1'>
					</td>
				</tr>
				<tr>
					<td colspan='2'>
						<input type='submit' value='Create Crime Group' class='btn btn-primary'>
					</td>
				</tr>
				{$csrf}
			</table>
		</form>
		";
    }
}

function edit_crimegroup()
{
    global $db, $h, $userid, $api;
    if (!isset($_POST['step'])) {
        $_POST['step'] = 0;
    }
    if ($_POST['step'] == 0) {
        $csrf = request_csrf_html('staff_editcrimegroup1');
        echo "<form method='post'>
		<table class='table table-bordered'>
			<tr>
				<th colspan='2'>
					Select a Crime Group you wish to edit.
				</th>
			</tr>
			<tr>
				<th>
					Crime Group
				</th>
				<td>
					" . crimegroup_dropdown('crimegroup') . "
				</td>
			</tr>
			<tr>
				<td colspan='2'>
					<input type='submit' class='btn btn-primary' value='Edit Crime Group'>
				</td>
			</tr>
		</table>
		{$csrf}
		<input type='hidden' value='1' name='step'>
		</form>";
    }
    if ($_POST['step'] == 1) {
        $_POST['crimegroup'] = (isset($_POST['crimegroup']) && is_numeric($_POST['crimegroup'])) ? abs(intval($_POST['crimegroup'])) : '';
        if (!isset($_POST['verf']) || !verify_csrf_code('staff_editcrimegroup1', stripslashes($_POST['verf']))) {
            alert('danger', "Action Blocked!", "We have blocked this action for your security. Forms expire quickly. Go back and try again!");
            die($h->endpage());
        }
        if (empty($_POST['crimegroup'])) {
            alert('danger', "Uh Oh!", "Please specify the crime group you wish to edit.");
            die($h->endpage());
        }
        $d = $db->query("SELECT `cgORDER`, `cgNAME` FROM `crimegroups` WHERE `cgID` = {$_POST['crimegroup']}");
        if ($db->num_rows($d) == 0) {
            $db->free_result($d);
            alert('danger', "Uh Oh!", "The Crime Group you've selected does not exist.");
            die($h->endpage());
        }
        $itemi = $db->fetch_row($d);
        $db->free_result($d);
        $csrf = request_csrf_html('staff_editcrimegroup2');
        echo "<form method='post'>
			<table class='table table-bordered'>
				<tr>
					<th>
						Crime Group Name
					</th>
					<td>
						<input type='text' name='cgNAME' class='form-control' required='1' value='{$itemi['cgNAME']}'>
					</td>
				</tr>
				<tr>
					<th>
						Crime Group Order
					</th>
					<td>
						<input type='number' name='cgORDER' min='0' class='form-control' required='1' value='{$itemi['cgORDER']}'>
					</td>
				</tr>
				<tr>
					<td colspan='2'>
						<input type='submit' value='Edit Crime Group' class='btn btn-primary'>
					</td>
				</tr>
				{$csrf}
				<input type='hidden' name='step' value='2'>
				<input type='hidden' name='cgID' value='{$_POST['crimegroup']}' />
			</table>
		</form>
		";
    }
    if ($_POST['step'] == 2) {
        $_POST['cgNAME'] = (isset($_POST['cgNAME']) && preg_match("/^[a-z0-9_]+([\\s]{1}[a-z0-9_]|[a-z0-9_])+$/i", $_POST['cgNAME'])) ? $db->escape(strip_tags(stripslashes($_POST['cgNAME']))) : '';
        $_POST['cgORDER'] = (isset($_POST['cgORDER']) && is_numeric($_POST['cgORDER'])) ? abs(intval($_POST['cgORDER'])) : '';
        $_POST['cgID'] = (isset($_POST['cgID']) && is_numeric($_POST['cgID'])) ? abs(intval($_POST['cgID'])) : '';
        if (!isset($_POST['verf']) || !verify_csrf_code('staff_editcrimegroup2', stripslashes($_POST['verf']))) {
            alert('danger', "Action Blocked!", "We have blocked this action for your security. Forms expire quickly. Go back and try again!");
            die($h->endpage());
        }
        if (empty($_POST['cgNAME']) || empty($_POST['cgORDER']) || empty($_POST['cgID'])) {
            alert('danger', "Uh Oh!", "Please fill out the form entirely before submitting.");
            die($h->endpage());
        } else {
            $d = $db->query("SELECT COUNT(`cgID`) FROM `crimegroups` WHERE `cgORDER` = {$_POST['cgORDER']} AND `cgID` != {$_POST['cgID']}");
            if ($db->fetch_single($d) > 0) {
                $db->free_result($d);
                alert('danger', "Uh Oh!", "You cannot have more than one crime group with the same order number.");
                die($h->endpage());
            }
            $db->free_result($d);
            $db->query("UPDATE `crimegroups` SET `cgNAME` = '{$_POST['cgNAME']}', `cgORDER` = '{$_POST['cgORDER']}' WHERE `cgID` = '{$_POST['cgID']}'");
            alert('success', "Success!", "You have successfully edited the {$_POST['cgNAME']} Crime Group.", true, 'index.php');
            $api->SystemLogsAdd($userid, 'staff', "Edited Crime Group {$_POST['cgNAME']}");
        }
    }
}

function delcrimegroup()
{
    global $db, $userid, $api, $h;
    if (isset($_POST['crimeGROUP'])) {
        $_POST['crimeGROUP'] = (isset($_POST['crimeGROUP']) && is_numeric($_POST['crimeGROUP'])) ? abs(intval($_POST['crimeGROUP'])) : '';
        if (!isset($_POST['verf']) || !verify_csrf_code('staff_delcrimegroup', stripslashes($_POST['verf']))) {
            alert('danger', "Action Blocked!", "We have blocked this action for your security. Forms expire quickly. Go back and try again!");
            die($h->endpage());
        }
        if (empty($_POST['crimeGROUP'])) {
            alert('danger', "Uh Oh!", "Please specify the crime group you wish to delete.");
            die($h->endpage());
        }
        $d = $db->query("SELECT * FROM `crimegroups` WHERE `cgID` = {$_POST['crimeGROUP']}");
        if ($db->num_rows($d) == 0) {
            $db->free_result($d);
            alert('danger', "Uh Oh!", "The Crime Group you wish to delete does not exist.");
            die($h->endpage());
        }
        $db->query("DELETE FROM `crimegroups` WHERE `cgID` = {$_POST['crimeGROUP']}");
        alert('success', "Success!", "You have successfully deleted Crime Group ID #{$_POST['crimeGROUP']}.", true, 'index.php');
        $api->SystemLogsAdd($userid, 'staff', "Deleted Crime Group ID {$_POST['crimeGROUP']}.");
    } else {
        $csrf = request_csrf_html('staff_delcrimegroup');
        echo "<form method='post'>
		<table class='table table-bordered'>
			<tr>
				<th colspan='2'>
					Select the Crime Group you wish to delete.
				</th>
			</tr>
			<tr>
				<th>
					Crime Group
				</th>
				<td>
					" . crimegroup_dropdown('crimeGROUP') . "
				</td>
			</tr>
			<tr>
				<td colspan='2'>
					<input type='submit' value='Delete Crime Group' class='btn btn-primary'>
				</td>
			</tr>
			{$csrf}
		</table>
		</form>";
    }
}

$h->endpage();