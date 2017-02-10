<?php
require('sglobals.php');
echo "<h3>{$lang['STAFF_SMELT_HOME']}</h3><hr />";
if (!isset($_GET['action']))
{
    $_GET['action'] = 'add';
}
switch ($_GET['action'])
{
case 'add':
    add();
    break;
}
function add()
{
	global $db,$api,$lang,$h,$userid;
	if (isset($_POST['smelted_item']))
	{
		$_POST['smelted_item'] = (isset($_POST['smelted_item']) && is_numeric($_POST['smelted_item']))  ? abs(intval($_POST['smelted_item'])) : 0;
		$_POST['smelted_item_qty'] = (isset($_POST['smelted_item_qty']) && is_numeric($_POST['smelted_item_qty']))  ? abs(intval($_POST['smelted_item_qty'])) : 0;
		$_POST['timetocomplete'] = (isset($_POST['timetocomplete']) && is_numeric($_POST['timetocomplete']))  ? abs(intval($_POST['timetocomplete'])) : 0;
		$_POST['required_item'] = (isset($_POST['required_item']) && is_numeric($_POST['required_item']))  ? abs(intval($_POST['required_item'])) : 0;
		$_POST['required_item_qty'] = (isset($_POST['required_item_qty']) && is_numeric($_POST['required_item_qty']))  ? abs(intval($_POST['required_item_qty'])) : 0;
		if ($_POST['required_item'] == 0 || $_POST['smelted_item'] == 0 || $_POST['smelted_item_qty'] == 0 || $_POST['required_item_qty'] == 0)
		{
			alert('danger',$lang['ERROR_GENERIC'],$lang['STAFF_SMELT_ADD_FAIL']);
			die($h->endpage());
		}
		$items = $_POST['required_item'];
		$qty = $_POST['required_item_qty'];
		for($i = 1; $i <= 5; $i++) 
		{
			$_POST['required_item'.$i] = (isset($_POST['required_item'.$i]) && is_numeric($_POST['required_item'.$i]))  ? abs(intval($_POST['required_item'.$i])) : 0;
			$_POST['required_item_qty'.$i] = (isset($_POST['required_item_qty'.$i]) && is_numeric($_POST['required_item_qty'.$i]))  ? abs(intval($_POST['required_item_qty'.$i])) : 0;
			if($_POST['required_item'.$i] > 0) 
			{
				if ($_POST['required_item_qty'.$i] == 0)
				{
					alert('danger',$lang['ERROR_GENERIC'],$lang['STAFF_SMELT_ADD_FAIL']);
					die($h->endpage());
				}
				$items .= ",". $_POST['required_item'.$i];
				$qty .= ",". $_POST['required_item_qty'.$i];
			}
		}
		$db->query("INSERT INTO `smelt_recipes` 
		(`smelt_time`, `smelt_items`, `smelt_quantity`, `smelt_output`, `smelt_qty_output`) 
		VALUES 
		('{$_POST['timetocomplete']}', '{$items}', '{$qty}', '{$_POST['smelted_item']}', '{$_POST['smelted_item_qty']}')");
		$api->SystemLogsAdd($userid,'staff',"Created smelting recipe for ".$api->SystemItemIDtoName($_POST['smelted_item']));
		alert('success',"{$lang['ERROR_SUCCESS']}","{$lang['STAFF_SMELT_ADD_SUCC']}");
	}
	else
	{
		echo "<form id='craft' method='post'>
			<table class='table table-bordered'>
				<tr>
					<th>
						{$lang['STAFF_SMELT_ADD_TH']}
					</th>
					<th>
						{$lang['STAFF_SMELT_ADD_TH1']}
					</th>
				</tr>
				<tr>
					<th>
						{$lang['STAFF_SMELT_ADD_TH2']}
					</th>
					<td>
						" . item_dropdown("smelted_item") . "
					</td>
				</tr>
				<tr>
					<th>
						{$lang['STAFF_SMELT_ADD_TH5']}
					</th>
					<td>
						<input type='number' class='form-control' required='1' name='smelted_item_qty' value='1' min='1'>
					</td>
				</tr>
				<tr>
					<th>
						{$lang['STAFF_SMELT_ADD_TH3']}
					</th>
					<td>
						<select class='form-control' name='timetocomplete'>
							<option value='0'>{$lang['STAFF_SMELT_ADD_SELECT1']}</option>
							<option value='5'>5 {$lang['STAFF_SMELT_ADD_SELECT2']}</option>
							<option value='30'>30 {$lang['STAFF_SMELT_ADD_SELECT2']}</option>
							<option value='60'>1 {$lang['STAFF_SMELT_ADD_SELECT3']}</option>
							<option value='300'>5 {$lang['STAFF_SMELT_ADD_SELECT3']}</option>
							<option value='600'>10 {$lang['STAFF_SMELT_ADD_SELECT3']}</option>
							<option value='3600'>1 {$lang['STAFF_SMELT_ADD_SELECT4']}</option>
							<option value='86400'>1 {$lang['STAFF_SMELT_ADD_SELECT5']}</option>
						</select>
					</td>
				</tr>
					<tr>
						<th>
							{$lang['STAFF_SMELT_ADD_TH4']}
						</th>
						<td>
							<div id='input1' class='clonedInput'>" . item_dropdown("required_item") . "<br /></div>
						</td>
					</tr>
				<tr>
				</div>
					<tr>
						<th>
							{$lang['STAFF_SMELT_ADD_TH6']}
						</th>
						<td>
							<div id='otherinput1' class='inputCloned'><input type='number' class='form-control' required='1' name='required_item_qty' value='1' min='1'><br /></div>
						</td>
					</tr>
				</tr>
				<tr>
					<td>
						<input type='button' class='btn btn-success' id='btnAdd' value='{$lang['STAFF_SMELT_ADD_BTN']}' />
					</td>
					<td>
						<input type='button' class='btn btn-danger' id='btnDel' value='{$lang['STAFF_SMELT_ADD_BTN2']}' />
					</td>
				</tr>
				<tr>
					<td colspan='2'>
						<input type='submit' class='btn btn-default' value='{$lang['STAFF_SMELT_ADD_BTN3']}' />
					</td>
				</tr>
			</table>
		</form>";
	}
}
$h->endpage();