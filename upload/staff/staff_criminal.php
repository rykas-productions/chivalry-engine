<?php
require_once('sglobals.php');
echo "<h3>{$lang['STAFF_CRIME_TITLE']}</h3><hr />";
if (!isset($_GET['action']))
{
    $_GET['action'] = '';
}
switch ($_GET['action'])
{
case 'newcrime':
    new_crime_form();
    break;
case 'editcrime':
    edit_crime_begin();
    break;
case 'delcrime':
    delcrime();
    break;
case 'newcrimegroup':
    new_crimegroup_form();
    break;
case 'editcrimegroup':
    edit_crimegroup_begin();
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
	global $lang;
	echo "
	<a href='?action=newcrimegroup'>{$lang['STAFF_CRIME_MENU_CREATECG']}</a><br />
	<a href='?action=newcrime'>{$lang['STAFF_CRIME_MENU_CREATE']}</a>";
}
function new_crime_form()
{
	global $lang,$db,$userid,$api;
	if (!isset($_POST['name']))
	{
		$csrf = request_csrf_html('staff_newcrime');
		echo "{$lang['STAFF_CRIME_NEW_TITLE']}<br />
		<form method='post'>
			<table class='table table-bordered table-responsive'>
				<tr>
					<th width='33%'>
						{$lang['STAFF_CRIME_NEW_NAME']}
					</th>
					<td>
						<input type='text' class='form-control' required='1' name='name' />
					</td>
				</tr>
				<tr>
					<th>
						{$lang['STAFF_CRIME_NEW_BRAVECOST']}
					</th>
					<td>
						<input type='number' min='1' class='form-control' required='1' name='brave' />
					</td>
				</tr>
				<tr>
					<th>
						{$lang['STAFF_CRIME_NEW_SUCFOR']}
					</th>
					<td>
						<input type='text' class='form-control' required='1' placeholder='((WILL*0.8)/2.5)+(LEVEL/4)' value='((WILL*0.8)/2.5)+(LEVEL/4)' name='percform' />
					</td>
				</tr>
				<tr>
					<th>
						{$lang['STAFF_CRIME_NEW_SUCPRIMIN']}
					</th>
					<td>
						<input type='number' min='0' class='form-control' required='1' name='PRICURMIN' />
					</td>
				</tr>
				<tr>
					<th>
						{$lang['STAFF_CRIME_NEW_SUCPRIMAX']}
					</th>
					<td>
						<input type='number' min='0' class='form-control' required='1' name='PRICURMAX' />
					</td>
				</tr>
				<tr>
					<th>
						{$lang['STAFF_CRIME_NEW_SUCSECMIN']}
					</th>
					<td>
						<input type='number' min='0' class='form-control' required='1' name='SECURMIN' />
					</td>
				</tr>
				<tr>
					<th>
						{$lang['STAFF_CRIME_NEW_SUCSECMAX']}
					</th>
					<td>
						<input type='number' min='0' class='form-control' required='1' name='SECURMAX' />
					</td>
				</tr>
				<tr>
					<th>
						{$lang['STAFF_CRIME_NEW_SUCITEM']}
					</th>
					<td>
						" . item_dropdown('item')  . "
					</td>
				</tr>
				<tr>
					<th>
						{$lang['STAFF_CRIME_NEW_GROUP']}
					</th>
					<td>
						" . crimegroup_dropdown('group')  . "
					</td>
				</tr>
				<tr>
					<th>
						{$lang['STAFF_CRIME_NEW_ITEXT']}
					</th>
					<td>
						<textarea class='form-control' name='itext' placeholder='{$lang['STAFF_CRIME_NEW_ITEXT_PH']}' required='1'></textarea>
					</td>
				</tr>
				<tr>
					<th>
						{$lang['STAFF_CRIME_NEW_STEXT']}
					</th>
					<td>
						<textarea class='form-control' name='stext' placeholder='{$lang['STAFF_CRIME_NEW_STEXT_PH']}' required='1'></textarea>
					</td>
				</tr>
				<tr>
					<th>
						{$lang['STAFF_CRIME_NEW_JTEXT']}
					</th>
					<td>
						<textarea class='form-control' name='jtext' placeholder='{$lang['STAFF_CRIME_NEW_JTEXT_PH']}' required='1'></textarea>
					</td>
				</tr>
				<tr>
					<th>
						{$lang['STAFF_CRIME_NEW_JTIMEMIN']}
					</th>
					<td>
						<input type='number' min='0' class='form-control' required='1' name='jtimemin' />
					</td>
				</tr>
				<tr>
					<th>
						{$lang['STAFF_CRIME_NEW_JTIMEMAX']}
					</th>
					<td>
						<input type='number' min='0' class='form-control' required='1' name='jtimemax' />
					</td>
				</tr>
				<tr>
					<th>
						{$lang['STAFF_CRIME_NEW_JREASON']}
					</th>
					<td>
						<input type='text' class='form-control' required='1' name='jreason' />
					</td>
				</tr>
				<tr>
					<th>
						{$lang['STAFF_CRIME_NEW_XP']}
					</th>
					<td>
						<input type='number' min='1' class='form-control' required='1' name='xp' />
					</td>
				</tr>
				<td colspan='2'>
					<input type='submit' value='{$lang['STAFF_CRIME_NEW_BTN']}' class='btn btn-default'>
				</td>
				{$csrf}
			</table>
		</form>";
	}
	else
	{
		$_POST['name'] =  (isset($_POST['name']) && preg_match("/^[a-z0-9_]+([\\s]{1}[a-z0-9_]|[a-z0-9_])*$/i", $_POST['name'])) ? $db->escape(strip_tags(stripslashes($_POST['name']))) : '';
		$_POST['brave'] = (isset($_POST['brave']) && is_numeric($_POST['brave'])) ? abs(intval($_POST['brave'])) : '';
		$_POST['percform'] = (isset($_POST['percform'])) ? $db->escape(strip_tags(stripslashes($_POST['percform']))) : '';
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
			||  empty($_POST['group']) || empty($_POST['itext']) || empty($_POST['stext']) 
			|| empty($_POST['jtext']) || empty($_POST['jtimemin']) || empty($_POST['jtimemax']) 
			|| empty($_POST['jreason']) || empty($_POST['xp']))
		{
			alert('danger',"{$lang['ERROR_EMPTY']}","{$lang['STAFF_CRIME_NEW_FAIL1']}");
			die($h->endpage());
		}
		if (!isset($_POST['verf']) || !verify_csrf_code('staff_newcrime', stripslashes($_POST['verf'])))
		{
			alert('danger',"{$lang["CSRF_ERROR_TITLE"]}","{$lang["CSRF_ERROR_TEXT"]}");
			die($h->endpage());
		}
		if (!empty($_POST['item']))
		{
			$qi = $db->query("SELECT COUNT(`itmid`) FROM `items` WHERE `itmid` = {$_POST['item']}");
			$exist_check = $db->fetch_single($qi);
			$db->free_result($qi);
			if ($exist_check == 0)
			{
				alert('danger',"{$lang['ERROR_GENERIC']}","{$lang['STAFF_CRIME_NEW_FAIL2']}");
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
		alert('success',"{$lang['ERROR_SUCCESS']}","{$lang['STAFF_CRIME_NEW_SUCCESS']}");
		$api->SystemLogsAdd($userid,'staff',"Created crime {$_POST['name']}");
	}
}
function new_crimegroup_form()
{
	global $db,$lang,$h,$api,$userid;
	if (isset($_POST['cgNAME']))
	{
		$_POST['cgNAME'] = (isset($_POST['cgNAME']) && preg_match("/^[a-z0-9_]+([\\s]{1}[a-z0-9_]|[a-z0-9_])+$/i", $_POST['cgNAME'])) ? $db->escape(strip_tags(stripslashes($_POST['cgNAME']))) : '';
		$_POST['cgORDER'] = (isset($_POST['cgORDER']) && is_numeric($_POST['cgORDER'])) ? abs(intval($_POST['cgORDER'])) : '';
		if (empty($_POST['cgNAME']) || empty($_POST['cgORDER']))
		{
			alert('danger',"{$lang['ERROR_EMPTY']}","{$lang['STAFF_CRIMEG_NEW_FAIL1']}");
			die($h->endpage());
		}
		if (!isset($_POST['verf']) || !verify_csrf_code('staff_newcrimegroup', stripslashes($_POST['verf'])))
		{
			alert('danger',"{$lang["CSRF_ERROR_TITLE"]}","{$lang["CSRF_ERROR_TEXT"]}");
			die($h->endpage());
		}
		$d = $db->query("SELECT COUNT(`cgID`) FROM `crimegroups` WHERE `cgORDER` = {$_POST['cgORDER']}");
		if ($db->fetch_single($d) > 0)
		{
			$db->free_result($d);
			alert('danger',"{$lang['ERROR_GENERIC']}","{$lang['STAFF_CRIMEG_NEW_FAIL2']}");
			die($h->endpage());
		}
		$db->free_result($d);
		$db->query("INSERT INTO `crimegroups` (`cgNAME`, `cgORDER`) VALUES('{$_POST['cgNAME']}', '{$_POST['cgORDER']}')");
		alert('success',"{$lang['ERROR_SUCCESS']}","{$lang['STAFF_CRIMEG_NEW_SUCCESS']}");
		$api->SystemLogsAdd($userid,'staff',"Created Crime Group {$_POST['cgNAME']}");
		
	}
	else
	{
		$csrf = request_csrf_html('staff_newcrimegroup');
		echo "{$lang['STAFF_CRIMEG_NEW_TITLE']}<br />
		<form method='post'>
			<table class='table table-bordered table-responsive'>
				<tr>
					<th>
						{$lang['STAFF_CRIMEG_NEW_NAME']}
					</th>
					<td>
						<input type='text' name='cgNAME' class='form-control' required='1'>
					</td>
				</tr>
				<tr>
					<th>
						{$lang['STAFF_CRIMEG_NEW_ORDER']}
					</th>
					<td>
						<input type='number' name='cgORDER' min='0' class='form-control' required='1'>
					</td>
				</tr>
				<tr>
					<td colspan='2'>
						<input type='submit' value='{$lang['STAFF_CRIMEG_NEW_BTN']}' class='btn btn-default'>
					</td>
				</tr>
				{$csrf}
			</table>
		</form>
		";
	}
}
$h->endpage();