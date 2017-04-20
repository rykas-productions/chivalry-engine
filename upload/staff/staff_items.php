<?php
/*
	File: staff/staff_items.php
	Created: 6/1/2016 at 6:06PM Eastern Time
	Info: Allows admins to interact with the items in the game.
	Author: TheMasterGeneral
	Website: https://github.com/MasterGeneral156/chivalry-engine
*/

//Still not localized.
require('sglobals.php');
echo "<h3>Items</h3><hr />";
if (!isset($_GET['action']))
{
    $_GET['action'] = '';
}
switch ($_GET['action'])
{
case "create":
    create();
    break;
case "createitmgroup":
    createitmgroup();
    break;
case "delete":
    deleteitem();
    break;
case "edit":
    edititem();
    break;
case "giveitem":
    giveitem();
    break;
default:
    die();
    break;
}
function create()
{
	global $db,$ir,$h,$lang,$userid,$api;
	if ($ir['user_level'] != 'Admin')
    {
        alert('danger',$lang['ERROR_NOPERM'],$lang['STAFF_CITEM_ERR'],true,'index.php');
        die($h->endpage());
    }
	if (!isset($_POST['itemname']))
	{
		$csrf = request_csrf_html('staff_newitem');
		echo "<form method='post'>
		<table class='table table-bordered'>
			<tr>
				<th width='33%'>
					{$lang['STAFF_CITEM_TH1']}
				</th>
				<td>
					<input type='text' required='1' name='itemname' class='form-control'>
				</td>
			</tr>
			<tr>
				<th width='33%'>
					{$lang['STAFF_CITEM_TH2']}
				</th>
				<td>
					<input type='text' required='1' name='itemdesc' class='form-control'>
				</td>
			</tr>
			<tr>
				<th width='33%'>
					{$lang['STAFF_CITEM_TH3']}
				</th>
				<td>
					" . itemtype_dropdown('itmtype') . "
				</td>
			</tr>
			<tr>
				<th width='33%'>
					{$lang['STAFF_CITEM_TH4']}
				</th>
				<td>
					<input type='checkbox' class='form-control' checked='checked' name='itembuyable'>
				</td>
			</tr>
			<tr>
				<th width='33%'>
					{$lang['STAFF_CITEM_TH5']}
				</th>
				<td>
					<input type='number' required='1' name='itembuy' min='0' class='form-control'>
				</td>
			</tr>
			<tr>
				<th width='33%'>
					{$lang['STAFF_CITEM_TH6']}
				</th>
				<td>
					<input type='number' required='1' name='itemsell' min='0' class='form-control'>
				</td>
			</tr>
			<tr>
				<td colspan='2'>
					<h4>{$lang['STAFF_CITEM_TH7']}</h4>
				</td>
			</tr>";
			for ($i = 1; $i <= 3; $i++)
			{
				echo "
				<tr>
					<th>
						<b><u>{$lang['STAFF_CITEM_TH8']}{$i}</u></b>
					</th>
					<td>
					<b>{$lang['STAFF_CITEM_TH9']}</b>
						<input type='radio' class='form-control' name='effect{$i}on' value='true' /> {$lang['STAFF_CITEM_TH10']}
						<input type='radio' class='form-control' name='effect{$i}on' value='false' checked='checked' /> {$lang['STAFF_CITEM_TH11']}
					<br />
					<b>{$lang['STAFF_CITEM_TH12']}</b> <select name='effect{$i}stat' type='dropdown' class='form-control'>
						<option value='energy'>{$lang['INDEX_ENERGY']}</option>
						<option value='will'>{$lang['INDEX_WILL']}</option>
						<option value='brave'>{$lang['INDEX_BRAVE']}</option>
						<option value='hp'>{$lang['INDEX_HP']}</option>
						<option value='strength'>{$lang['GEN_STR']}</option>
						<option value='agility'>{$lang['GEN_AGL']}</option>
						<option value='guard'>{$lang['GEN_GRD']}</option>
						<option value='labor'>{$lang['GEN_LAB']}</option>
						<option value='IQ'>{$lang['GEN_IQ']}</option>
						<option value='infirmary'>{$lang['STAFF_CITEM_TH12_1']}</option>
						<option value='dungeon'>{$lang['STAFF_CITEM_TH12_1']}</option>
						<option value='primary_currency'>{$lang['INDEX_PRIMCURR']}</option>
						<option value='secondary_currency'>{$lang['INDEX_SECCURR']}</option>
						<option value='xp'>{$lang['INDEX_EXP']}</option>
						<option value='vip_days'>{$lang['INDEX_VIP']}</option>
					</select>
					<br />
					<b>{$lang['STAFF_CITEM_TH13']}</b> <select name='effect{$i}dir' class='form-control' type='dropdown'>
						<option value='pos'>{$lang['STAFF_CITEM_TH13_1']}</option>
						<option value='neg'>{$lang['STAFF_CITEM_TH13_2']}</option>
					</select>
					<br />
					<b>{$lang['STAFF_CITEM_TH14']}</b> <input type='number' min='0' class='form-control' name='effect{$i}amount' value='0' />
					<select name='effect{$i}type' class='form-control' type='dropdown'>
						<option value='figure'>{$lang['STAFF_CITEM_TH14_1']}</option>
						<option value='percent'>{$lang['STAFF_CITEM_TH14_2']}</option>
					</select>
					</td>
				</tr>";
			}
			
			echo"
			<tr>
				<td colspan='2'>
					<h4>{$lang['STAFF_CITEM_TH15']}</h4>
				</td>
			</tr>
			<tr>
				<th>
					{$lang['STAFF_CITEM_TH16']}
				</th>
				<td>
					<input type='number' class='form-control' name='weapon' min='0' value='0' />
				</td>
			</tr>
			<tr>
				<th>
					{$lang['STAFF_CITEM_TH17']}
				</th>
				<td>
					<input type='number' class='form-control' name='armor' min='0' value='0' />
				</td>
			</tr>
			<tr>
				<td colspan='2'>
					<input type='submit' value='{$lang['STAFF_CITEM_BTN']}' class='btn btn-default'>
				</td>
			</tr>
		</table>
		{$csrf}
		</form>";
	}
	else
	{
		if (!isset($_POST['verf']) || !verify_csrf_code('staff_newitem', stripslashes($_POST['verf'])))
		{
			alert('danger',$lang["CSRF_ERROR_TITLE"],$lang["CSRF_ERROR_TEXT"]);
			die($h->endpage());
		}
		$itmname = (isset($_POST['itemname']) && is_string($_POST['itemname'])) ? stripslashes($_POST['itemname']) : '';
		$itmdesc = (isset($_POST['itemdesc'])) ? $db->escape(strip_tags(stripslashes($_POST['itemdesc']))) : '';
		$weapon = (isset($_POST['weapon']) && is_numeric($_POST['weapon'])) ? abs(intval($_POST['weapon'])) : 0;
		$armor = (isset($_POST['armor']) && is_numeric($_POST['armor'])) ? abs(intval($_POST['armor'])) : 0;
		$itmtype = (isset($_POST['itmtype']) && is_numeric($_POST['itmtype'])) ? abs(intval($_POST['itmtype'])) : '';
		$itmbuyprice = (isset($_POST['itembuy']) && is_numeric($_POST['itembuy'])) ? abs(intval($_POST['itembuy'])) : 0;
		$itmsellprice = (isset($_POST['itemsell']) && is_numeric($_POST['itemsell'])) ? abs(intval($_POST['itemsell'])) : 0;
		if (empty($itmname) || empty($itmdesc) || empty($itmtype) || empty($itmbuyprice) || empty($itmsellprice))
		{
			alert('danger',$lang['ERROR_GEN'],$lang['STAFF_CITEM_ERR1']);
			die($h->endpage());
		}
		$inq=$db->query("SELECT `itmid` FROM `items` WHERE `itmname` = '{$itmname}'");
		if ($db->num_rows($inq) > 0)
		{
			$db->free_result($inq);
			alert('danger',$lang['ERROR_GEN'],$lang['STAFF_CITEM_ERR2']);
			die($h->endpage());
		}
		$q=$db->query("SELECT `itmtypeid` FROM `itemtypes` WHERE `itmtypeid` = '{$itmtype}'");
		if ($db->num_rows($q) == 0)
		{
			$db->free_result($q);
			alert('danger',$lang['ERROR_GEN'],$lang['STAFF_CITEM_ERR3']);
			die($h->endpage());
		}
		$itmbuy = ($_POST['itembuyable'] == 'on') ? 'true' : 'false';
		for ($i = 1; $i <= 3; $i++)
		{
			$efxkey = "effect{$i}";
			$_POST[$efxkey . 'stat'] =
					(isset($_POST[$efxkey . 'stat'])
							&& in_array($_POST[$efxkey . 'stat'],
									array('energy', 'will', 'brave', 'hp',
											'strength', 'agility', 'guard',
											'labor', 'IQ', 'infirmary', 'dungeon',
											'primary_currency', 'secondary_currency', 'xp', 'vip_days')))
							? $_POST[$efxkey . 'stat'] : 'energy';
			$_POST[$efxkey . 'dir'] =
					(isset($_POST[$efxkey . 'dir'])
							&& in_array($_POST[$efxkey . 'dir'],
									array('pos', 'neg'))) ? $_POST[$efxkey . 'dir']
							: 'pos';
			$_POST[$efxkey . 'type'] =
					(isset($_POST[$efxkey . 'type'])
							&& in_array($_POST[$efxkey . 'type'],
									array('figure', 'percent')))
							? $_POST[$efxkey . 'type'] : 'figure';
			$_POST[$efxkey . 'amount'] =
					(isset($_POST[$efxkey . 'amount'])
							&& is_numeric($_POST[$efxkey . 'amount']))
							? abs(intval($_POST[$efxkey . 'amount'])) : 0;
			$_POST[$efxkey . 'on'] =
					(isset($_POST[$efxkey . 'on'])
							&& in_array($_POST[$efxkey . 'on'], array('true', 'false')))
							? $_POST[$efxkey . 'on'] : 0;
			$effects[$i] =
					$db->escape(
							serialize(
									array("stat" => $_POST[$efxkey . 'stat'],
											"dir" => $_POST[$efxkey . 'dir'],
											"inc_type" => $_POST[$efxkey . 'type'],
											"inc_amount" => abs(
													(int) $_POST[$efxkey
															. 'amount']))));
		}
		$m =
            $db->query(
                    "INSERT INTO `items`
						VALUES(NULL, '{$itmtype}', '{$itmname}', '{$itmdesc}',
                     {$itmbuyprice}, {$itmsellprice}, '{$itmbuy}', 
					 '{$_POST['effect1on']}', '{$effects[1]}',
                     '{$_POST['effect2on']}', '{$effects[2]}',
                     '{$_POST['effect3on']}', '{$effects[3]}', 
					 {$weapon}, {$armor})");
		$api->SystemLogsAdd($userid,'staff',"Created item {$itmname}.");
		alert('success',$lang['ERROR_SUCCESS'],"{$lang['STAFF_CITEM_SUCC']} {$itmname}.",true,'index.php');
	}
	$h->endpage();
}
function createitmgroup()
{
	global $db,$lang,$h,$ir,$api,$userid;
	if ($ir['user_level'] != 'Admin')
    {
        alert('danger','No Permission!','You have no permission to be here. If this is false, please contact an admin for help!');
        die($h->endpage());
    }
	if (!isset($_POST['name']))
	{
		$csrf = request_csrf_html('staff_newitemtype');
		echo "
        <h4>Adding an item type...</h4>
		<form method='post'>
			<table class='table table-bordered'>
			<tr>
				<th width='33%'>
					Item Group Name
				</th>
				<td>
					<input type='text' class='form-control' required='1' name='name' />
				</td>
			</tr>
			<tr>
				<td colspan='2'>
					<input type='submit' class='btn btn-default' value='Add Item Group' />
				</td>
			</tr>
        	{$csrf}
			</table>
		</form>
           ";
	}
	else
	{
		$name = (isset($_POST['name']) && preg_match("/^[a-z0-9_]+([\\s]{1}[a-z0-9_]|[a-z0-9_])+$/i", $_POST['name'])) ? $db->escape(strip_tags(stripslashes($_POST['name']))) : '';
		if (!isset($_POST['verf']) || !verify_csrf_code('staff_newitemtype', stripslashes($_POST['verf'])))
		{
			alert('danger',"{$lang["CSRF_ERROR_TITLE"]}","{$lang["CSRF_ERROR_TEXT"]}");
			die($h->endpage());
		}
		if (empty($name))
		{
			alert('danger',"Uh Oh!","Item group name is empty.");
		}
		$q=$db->query("SELECT `itmtypeid` FROM `itemtypes` WHERE `itmtypename` = '{$name}'");
		if ($db->num_rows($q) > 0)
		{
			$db->free_result($q);
			alert("danger","Already Exists!","An item group with that name already exists. Please go back and enter a new name.");
			die($h->endpage());
		}
		$api->SystemLogsAdd($userid,'staff',"Added item type {$name}.");
		alert('success',"Success!","You have successfully created an item group called {$name}.",true,'index.php');
		$db->query("INSERT INTO `itemtypes` VALUES(NULL, '{$name}')");
		
	}
	$h->endpage();
}
function deleteitem()
{
	global $db,$ir,$h,$lang,$userid,$api;
	if ($ir['user_level'] != 'Admin')
    {
        alert('danger','No Permission!','You have no permission to be here. If this is false, please contact an admin for help!');
        die($h->endpage());
    }
	if (!isset($_POST['item']))
	{
		$csrf = request_csrf_html('staff_killitem');
		echo "<h4>Deleting an Item</h4>
		The item you select will be deleted permanently. There isn't a confirmation prompt, so be 100% sure.
		<form method='post'>
			<table class='table table-bordered'>
				<tr>
					<th width='33%'>
						Item
					</th>
					<td>
						" . item_dropdown('item') . "
					</td>
				</tr>
				<tr>
					<td colspan='2'>
						<input type='submit' class='btn btn-default' value='Delete Item'>
					</td>
				</tr>
			</table>
			{$csrf}
		</form>";
	}
	else
	{
		if (!isset($_POST['verf']) || !verify_csrf_code('staff_killitem', stripslashes($_POST['verf'])))
		{
			alert('danger',"{$lang["CSRF_ERROR_TITLE"]}","{$lang["CSRF_ERROR_TEXT"]}");
			die($h->endpage());
		}
		$_POST['item'] =(isset($_POST['item']) && is_numeric($_POST['item'])) ? abs(intval($_POST['item'])) : '';
		if (empty($_POST['item']))
		{
			alert('warning','Empty Input!','You did not specify an item to delete. Go back and try again.');
			die($h->endpage());
		}
		$d =
            $db->query(
                    "SELECT `itmname`
                     FROM `items`
                     WHERE `itmid` = {$_POST['item']}");
		if ($db->num_rows($d) == 0)
		{
			$db->free_result($d);
			alert('danger',"Uh oh!","The item you chose to delete does not exist!");
			die($h->endpage());
		}
		$itemname = $db->fetch_single($d);
		$db->free_result($d);
		$db->query("DELETE FROM `items` WHERE `itmid` = {$_POST['item']}");
		$db->query("DELETE FROM `inventory` WHERE `inv_itemid` = {$_POST['item']}");
		$api->SystemLogsAdd($userid,'staff',"Deleted item {$itemname}.");
		alert("success","Success!","The Item ({$itemname}) has been deleted from the game successfully.",true,'index.php');
		die($h->endpage());
	}
}
function giveitem()
{

	global $lang,$db,$userid,$h,$api;
	if (!isset($_POST['user']) || !isset($_POST['item']))
	{
		echo "<h3>{$lang['STAFF_ITEM_GIVE_TITLE']}</h3>";
		$csrf = request_csrf_html('staff_giveitem');
		echo "
		<form method='post'>
			<table class='table table-bordered table-responsive'>
				<tr>
					<th>
						{$lang['STAFF_ITEM_GIVE_FORM_USER']}
					</th>
					<td>
						" . user_dropdown('user') . "
					</td>
				</tr>
				<tr>
					<th>
						{$lang['STAFF_ITEM_GIVE_FORM_ITEM']}
					</th>
					<td>
						" . item_dropdown('item') . "
					</td>
				</tr>
				<tr>
					<th>
						{$lang['STAFF_ITEM_GIVE_FORM_QTY']}
					</th>
					<td>
						<input type='number' required='1' class='form-control' name='qty' value='1' min='1' />
					</td>
				</tr>
				<tr>
					<td colspan='2'>
						<input type='submit' class='btn btn-default' value='{$lang['STAFF_ITEM_GIVE_FORM_BTN']}' />
					</td>
				</tr>
				{$csrf}
			</table>
		</form>";
		$h->endpage();
	}
	else
	{
		if (!isset($_POST['verf']) || !verify_csrf_code('staff_giveitem', stripslashes($_POST['verf'])))
		{
			alert('danger',$lang["CSRF_ERROR_TITLE"],$lang["CSRF_ERROR_TEXT"]);
			die($h->endpage());
		}
		$_POST['item'] = (isset($_POST['item']) && is_numeric($_POST['item'])) ? abs(intval($_POST['item'])) : '';
		$_POST['user'] = (isset($_POST['user']) && is_numeric($_POST['user'])) ? abs(intval($_POST['user'])) : '';
		$_POST['qty'] = (isset($_POST['qty']) && is_numeric($_POST['qty'])) ? abs(intval($_POST['qty'])) : '';
		if (empty($_POST['item']))
		{
			alert('danger',$lang['ERROR_EMPTY'],$lang['STAFF_ITEM_GIVE_SUB_NOITEM']);
			die($h->endpage());
		}
		elseif (empty($_POST['user']))
		{
			alert('danger',$lang['ERROR_EMPTY'],$lang['STAFF_ITEM_GIVE_SUB_NOUSER']);
			die($h->endpage());
		}
		elseif (empty($_POST['qty']))
		{
			alert('danger',$lang['ERROR_EMPTY'],$lang['STAFF_ITEM_GIVE_SUB_NOQTY']);
			die($h->endpage());
		}
		else
		{
			 $q = $db->query("SELECT `itmid`,`itmname` FROM `items` WHERE `itmid` = {$_POST['item']}");
			 $q2 = $db->query("SELECT `userid`,`username` FROM `users` WHERE `userid` = {$_POST['user']}");
			 if ($db->num_rows($q) == 0)
			 {
				alert('danger',$lang['ERROR_GEN'],$lang['STAFF_ITEM_GIVE_SUB_ITEMDNE']);
				die($h->endpage());
			 }
			 elseif ($db->num_rows($q2) == 0)
			 {
				alert('danger',$lang['ERROR_GEN'],$lang['STAFF_ITEM_GIVE_SUB_USERDNE']);
				die($h->endpage());
			 }
			 else
			 {
				$item=$db->fetch_row($q);
				$user=$db->fetch_row($q2);
				$db->free_result($q);
				$db->free_result($q2);
				$api->UserGiveItem($_POST['user'], $_POST['item'], $_POST['qty']);
				$api->GameAddNotification($_POST['user'], "The administration has gifted you {$_POST['qty']}x {$item['itmname']}(s) to your inventory.");
				$api->SystemLogsAdd($userid,'staff',"Gave {$_POST['qty']}x <a href='../iteminfo.php'>{$item['itmname']}</a> to <a href='../profile.php?user={$_POST['user']}'>{$user['username']}</a>.");
				alert('success',$lang['ERROR_SUCCESS'],$lang['STAFF_ITEM_GIVE_SUB_SUCCESS'],true,'index.php');
				die($h->endpage());
			 }
		}
	}
}
function edititem()
{
	global $db,$ir,$api,$userid,$lang,$h;
	if (!isset($_POST['step']))
	{
		$_POST['step'] = 0;
	}
	if ($_POST['step'] == 2)
	{
		if (!isset($_POST['verf']) || !verify_csrf_code('staff_edititem1', stripslashes($_POST['verf'])))
		{
			alert('danger',$lang["CSRF_ERROR_TITLE"],$lang["CSRF_ERROR_TEXT"]);
			die($h->endpage());
		}
		$_POST['item'] = (isset($_POST['item']) && is_numeric($_POST['item'])) ? abs(intval($_POST['item'])) : 0;
		if (empty($_POST['item']))
		{
			alert('danger',$lang['ERROR_EMPTY'],$lang['STAFF_EITEM_P2_EMPTY']);
			die($h->endpage());
		}
		$d =  $db->query("SELECT * FROM `items` WHERE `itmid` = {$_POST['item']}");
		if ($db->num_rows($d) == 0)
		{
			$db->free_result($d);
			alert('danger',$lang['ERROR_GEN'],$lang['STAFF_EITEM_P2_NO']);
			die($h->endpage());
		}
		$itemi = $db->fetch_row($d);
		$db->free_result($d);
		$csrf = request_csrf_html('staff_edititem2');
		$itmname = addslashes($itemi['itmname']);
		$itmdesc = addslashes($itemi['itmdesc']);
		echo "<form method='post'>
					<input type='hidden' name='itemid' value='{$_POST['item']}' />
					<input type='hidden' name='step' value='3' />
		<table class='table table-bordered'>
			<tr>
				<th width='33%'>
					{$lang['STAFF_CITEM_TH1']}
				</th>
				<td>
					<input type='text' required='1' name='itemname' value='{$itmname}' class='form-control'>
				</td>
			</tr>
			<tr>
				<th width='33%'>
					{$lang['STAFF_CITEM_TH2']}
				</th>
				<td>
					<input type='text' required='1' name='itemdesc' value='{$itmdesc}' class='form-control'>
				</td>
			</tr>
			<tr>
				<th width='33%'>
					{$lang['STAFF_CITEM_TH3']}
				</th>
				<td>
					" . itemtype_dropdown('itmtype', $itemi['itmtype']) . "
				</td>
			</tr>
			<tr>
				<th width='33%'>
					{$lang['STAFF_CITEM_TH4']}
				</th>
				<td>
					<input type='checkbox' class='form-control' checked='checked' name='itembuyable'>
				</td>
			</tr>
			<tr>
				<th width='33%'>
					{$lang['STAFF_CITEM_TH5']}
				</th>
				<td>
					<input type='number' required='1' name='itembuy' min='0' value='{$itemi['itmbuyprice']}' class='form-control'>
				</td>
			</tr>
			<tr>
				<th width='33%'>
					{$lang['STAFF_CITEM_TH6']}
				</th>
				<td>
					<input type='number' required='1' name='itemsell' min='0' value='{$itemi['itmsellprice']}' class='form-control'>
				</td>
			</tr>
			<tr>
				<td colspan='2'>
					<h4>{$lang['STAFF_CITEM_TH7']}</h4>
				</td>
			</tr>";
			$stats =
            array("energy" => "{$lang['INDEX_ENERGY']}", "will" => "{$lang['INDEX_WILL']}", 
					"brave" => "{$lang['INDEX_BRAVE']}",
                    "hp" => "{$lang['INDEX_HP']}", "strength" => "{$lang['GEN_STR']}",
                    "agility" => "{$lang['GEN_AGL']}", "guard" => "{$lang['GEN_GRD']}",
                    "labour" => "{$lang['GEN_LAB']}", "IQ" => "{$lang['GEN_IQ']}",
                    "infirmary" => "{$lang['STAFF_CITEM_TH12_1']}", "dungeon" => "{$lang['STAFF_CITEM_TH12_2']}",
                    "primary_currency" => "{{$lang['INDEX_PRIMCURR']}}", "secondary_currency" 
					=> "{{$lang['INDEX_SECCURR']}}", "crimexp" => "{{$lang['INDEX_EXP']}}", "vip_days" => 
					"{$lang['INDEX_VIP']}}");
			for ($i = 1; $i <= 3; $i++)
			{
				if (!empty($itemi["effect" . $i]))
				{
					$efx = unserialize($itemi["effect" . $i]);
				}
				else
				{
					$efx = array("inc_amount" => 0);
				}
				$switch1 =
						($itemi['effect' . $i . '_on'] > 0) ? " checked='checked'" : "";
				$switch2 =
						($itemi['effect' . $i . '_on'] > 0) ? "" : " checked='checked'";
				echo "
				<tr>
					<th>
						<b><u>{$lang['STAFF_CITEM_TH8']}{$i}</u></b>
					</th>
					<td>
						<b>{$lang['STAFF_CITEM_TH9']}</b>
						<input type='radio' class='form-control' name='effect{$i}on' value='1'$switch1 /> {$lang['STAFF_CITEM_TH10']}
						<input type='radio' class='form-control' name='effect{$i}on' value='0'$switch2 /> {$lang['STAFF_CITEM_TH11']}
						<br /><b>Stat</b> <select class='form-control' name='effect{$i}stat' type='dropdown'>";
				foreach ($stats as $k => $v)
				{
					echo ($k == $efx['stat'])
							? '<option value="' . $k . '" selected="selected">' . $v
									. '</option>'
							: '<option value="' . $k . '">' . $v . '</option>';
				}
				$str =
						($efx['dir'] == "neg")
								? "<option value='pos'>{$lang['STAFF_CITEM_TH13_1']}</option>
									<option value='neg' selected='selected'>{$lang['STAFF_CITEM_TH13_2']}</option>"
								: "<option value='pos' selected='selected'>{$lang['STAFF_CITEM_TH13_1']}</option>
									<option value='neg'>{$lang['STAFF_CITEM_TH13_2']}</option>";
				$str2 =
						($efx['inc_type'] == "percent")
								? "<option value='figure'>{$lang['STAFF_CITEM_TH14_1']}</option>
									<option value='percent' selected='selected'>{$lang['STAFF_CITEM_TH14_2']}</option>"
								: "<option value='figure' selected='selected'>{$lang['STAFF_CITEM_TH14_1']}</option>
									<option value='percent'>{$lang['STAFF_CITEM_TH14_2']}</option>";

				echo "
				</select>
				<br />
					<b>{$lang['STAFF_CITEM_TH13']}</b> <select class='form-control' name='effect{$i}dir' type='dropdown'> {$str} </select>
				<br />
					<b>{$lang['STAFF_CITEM_TH14']}</b> <input type='text' class='form-control' name='effect{$i}amount' value='{$efx['inc_amount']}' />
						<select name='effect{$i}type' class='form-control' type='dropdown'>{$str2}</select>
				</td></tr>
				   ";
			}
			echo"
			<tr>
				<td colspan='2'>
					<h4>{$lang['STAFF_CITEM_TH15']}</h4>
				</td>
			</tr>
			<tr>
				<th>
					{$lang['STAFF_CITEM_TH16']}
				</th>
				<td>
					<input type='number' class='form-control' value='{$itemi['weapon']}' name='weapon' min='0' value='0' />
				</td>
			</tr>
			<tr>
				<th>
					{$lang['STAFF_CITEM_TH17']}
				</th>
				<td>
					<input type='number' class='form-control' value='{$itemi['armor']}' name='armor' min='0' value='0' />
				</td>
			</tr>
			<tr>
				<td colspan='2'>
					<input type='submit' value='{$lang['STAFF_EITEM_BTN']}' class='btn btn-default'>
				</td>
			</tr>
		</table>
		{$csrf}
		</form>";
	}
	elseif ($_POST['step'] == 3)
	{
		if (!isset($_POST['verf']) || !verify_csrf_code('staff_edititem2', stripslashes($_POST['verf'])))
		{
			alert('danger',$lang["CSRF_ERROR_TITLE"],$lang["CSRF_ERROR_TEXT"]);
			die($h->endpage());
		}
		$itemid = (isset($_POST['itemid']) && is_numeric($_POST['itemid'])) ? abs(intval($_POST['itemid'])) : 0;
		$itmname = (isset($_POST['itemname']) && is_string($_POST['itemname'])) ? stripslashes($_POST['itemname']) : '';
		$itmdesc = (isset($_POST['itemdesc'])) ? $db->escape(strip_tags(stripslashes($_POST['itemdesc']))) : '';
		$weapon = (isset($_POST['weapon']) && is_numeric($_POST['weapon'])) ? abs(intval($_POST['weapon'])) : 0;
		$armor = (isset($_POST['armor']) && is_numeric($_POST['armor'])) ? abs(intval($_POST['armor'])) : 0;
		$itmtype = (isset($_POST['itmtype']) && is_numeric($_POST['itmtype'])) ? abs(intval($_POST['itmtype'])) : '';
		$itmbuyprice = (isset($_POST['itembuy']) && is_numeric($_POST['itembuy'])) ? abs(intval($_POST['itembuy'])) : 0;
		$itmsellprice = (isset($_POST['itemsell']) && is_numeric($_POST['itemsell'])) ? abs(intval($_POST['itemsell'])) : 0;
		if (empty($itmname) || empty($itemid) || empty($itmdesc) || empty($itmtype) || empty($itmbuyprice) || empty($itmsellprice))
		{
			alert('danger',$lang['ERROR_GENERIC'],$lang['STAFF_CITEM_ERR1']);
			die($h->endpage());
		}
		$inq=$db->query("SELECT `itmid` FROM `items` WHERE `itmname` = '{$itmname}' AND `itmid` != {$itemid}");
		if ($db->num_rows($inq) > 0)
		{
			$db->free_result($inq);
			alert('danger',$lang['ERROR_GENERIC'],$lang['STAFF_CITEM_ERR2']);
			die($h->endpage());
		}
		$q=$db->query("SELECT `itmtypeid` FROM `itemtypes` WHERE `itmtypeid` = '{$itmtype}'");
		if ($db->num_rows($q) == 0)
		{
			$db->free_result($q);
			alert('danger',$lang['ERROR_GENERIC'],$lang['STAFF_CITEM_ERR3']);
			die($h->endpage());
		}
		$itmbuy = ($_POST['itembuyable'] == 'on') ? 'true' : 'false';
		for ($i = 1; $i <= 3; $i++)
		{
			$efxkey = "effect{$i}";
			$_POST[$efxkey . 'stat'] =
					(isset($_POST[$efxkey . 'stat'])
							&& in_array($_POST[$efxkey . 'stat'],
									array('energy', 'will', 'brave', 'hp',
											'strength', 'agility', 'guard',
											'labor', 'IQ', 'infirmary', 'dungeon',
											'primary_currency', 'secondary_currency', 'xp', 'vip_days')))
							? $_POST[$efxkey . 'stat'] : 'energy';
			$_POST[$efxkey . 'dir'] =
					(isset($_POST[$efxkey . 'dir'])
							&& in_array($_POST[$efxkey . 'dir'],
									array('pos', 'neg'))) ? $_POST[$efxkey . 'dir']
							: 'pos';
			$_POST[$efxkey . 'type'] =
					(isset($_POST[$efxkey . 'type'])
							&& in_array($_POST[$efxkey . 'type'],
									array('figure', 'percent')))
							? $_POST[$efxkey . 'type'] : 'figure';
			$_POST[$efxkey . 'amount'] =
					(isset($_POST[$efxkey . 'amount'])
							&& is_numeric($_POST[$efxkey . 'amount']))
							? abs(intval($_POST[$efxkey . 'amount'])) : 0;
			$_POST[$efxkey . 'on'] =
					(isset($_POST[$efxkey . 'on'])
							&& in_array($_POST[$efxkey . 'on'], array('true', 'false')))
							? $_POST[$efxkey . 'on'] : 0;
			$effects[$i] =
					$db->escape(
							serialize(
									array("stat" => $_POST[$efxkey . 'stat'],
											"dir" => $_POST[$efxkey . 'dir'],
											"inc_type" => $_POST[$efxkey . 'type'],
											"inc_amount" => abs(
													(int) $_POST[$efxkey
															. 'amount']))));
		}
		$db->query("UPDATE `items` SET `itmname` = '{$itmname}',
						`itmtype` = {$itmtype}, `itmdesc` = '{$itmdesc}',
						`itmbuyprice` = {$itmbuyprice}, `itmsellprice` = {$itmsellprice},
						`effect1_on` = '{$_POST['effect1on']}', `effect1` = '{$effects[1]}',
						`effect2_on` = '{$_POST['effect2on']}', `effect2` = '{$effects[2]}',
						`effect3_on` = '{$_POST['effect3on']}', `effect3` = '{$effects[3]}',
						`weapon` = {$weapon}, `armor` = {$armor} WHERE `itmid` = {$itemid }");
			alert('success',$lang['ERROR_SUCCESS'],$lang['STAFF_EITEM_SUC'],true,'index.php');
			$api->SystemLogsAdd($userid,'staff',"Edited Item ID {$itemid }.");
	}
	else
	{
		$csrf = request_csrf_html('staff_edititem1');
		echo "
	<table class='table table-bordered'>
		<form method='post'>
			<tr>
				<th colspan='2'>
					{$lang['STAFF_EITEM_P1_START']}
				</th>
			</tr>
			<tr>
				<th>
					{$lang['STAFF_EITEM_P1_SELECT']}
				</th>
				<td>
					" . item_dropdown('item') . "
				</td>
			</tr>
			<tr>
				<td colspan='2'>
					{$csrf}
					<input type='hidden' name='step' value='2'>
					<input type='submit' class='btn btn-default' value='{$lang['STAFF_EITEM_P1_BTN']}' />
				</th>
			</tr>
		</form>
	</table>
	";
	$h->endpage();
	}
}