<?php
require('globals.php');
echo "<h3>Trade Center</h3><hr />";
if (!isset($_GET['action'])) {
    $_GET['action'] = '';
}
switch ($_GET['action']) {
    case 'create':
        create();
        break;
    default:
        home();
        break;
}
function home()
{
	global $db,$userid,$api,$h;
	echo "Welcome to the trade center. You may <a href='?action=create'>create</a> a trade, or view your current 
	trades.
	<br />
	These are trades that you are part of.
	<table class='table table-bordered'>
		<tr>
			<th width='10%'>
				Trade ID
			</th>
			<th>
				Trade User
			</th>
			<th>
				Link
			</th>
		</tr>";
	$q1=$db->query("SELECT * FROM `trading` WHERE `tradeuserb` = {$userid}");
	while ($r1=$db->fetch_row($q1))
	{
		echo "<tr>
			<td>
				{$r1['tradeid']}
			</td>
			<td>
				<a href='profile.php?user={$r1['tradeusera']}'>{$api->SystemUserIDtoName($r1['tradeusera'])}</a>
			</td>
			<td>
				<a href='?action=view&id={$r1['tradeid']}'>View</a>
			</td>
		</tr>";
	}
	$q2=$db->query("SELECT * FROM `trading` WHERE `tradeusera` = {$userid}");
	while ($r2=$db->fetch_row($q2))
	{
		echo "<tr>
			<td>
				{$r2['tradeid']}
			</td>
			<td>
				<a href='profile.php?user={$r2['tradeuserb']}'>{$api->SystemUserIDtoName($r2['tradeuserb'])}</a> [{$r2['tradeuserb']}]</a>
			</td>
			<td>
				<a href='?action=view&id={$r2['tradeid']}'>View</a>
			</td>
		</tr>";
	}
	echo "</table>";
}
function create()
{
	global $db,$userid,$api,$h;
	if (isset($_POST['diffitem']))
	{
		$_POST['diffitem'] = (isset($_POST['diffitem']) && is_numeric($_POST['diffitem'])) ? abs(intval($_POST['diffitem'])) : 0;
		if (isset($_POST['userid']))
		{
			$error=0;
			$itemerr=0;
			$itemsql='';
			$cntsql='';
			$cycle=-1;
			foreach($_POST['item'] as $item)
			{
				$cycle++;
				var_dump($_POST['item'][$cycle]);
				$item = (isset($item) && is_numeric($item)) ? abs(intval($item)) : 0;
				$q=$db->query("SELECT `itmid` FROM `items` WHERE `itmid` = {$item}");
				if ($db->num_rows($q) == 0)
				{
					$error=$error+1;
				}
				if ($_POST['item'][0] == $item)
					$itemsql.="{$item}";
				else
					$itemsql.=",{$item}";
			}
			foreach($_POST['qty'] as $qty)
			{
				$qty = (isset($qty) && is_numeric($qty)) ? abs(intval($qty)) : 0;
				foreach($_POST['item'] as $item)
				{
					if (!$api->UserHasItem($userid,$item,$qty))
						$error=$error+1;
				}
				if ($_POST['qty'][0] == $qty)
					$cntsql.="{$qty}";
				else
					$cntsql.=",{$qty}";
			}
			if ($error != 0)
			{
				alert('danger',"Uh Oh!","You are trying to trade an item you don't have, or have too little of.");
				die($h->endpage());
			}
		}
		else
		{
			echo "
			<form method='post'>
			<input type='hidden' value='{$_POST['diffitem']}' name='diffitem'>
			<table class='table table-bordered'>
				<tr>
					<th width='50%'>
						User
					</th>
					<td>
						" . user_dropdown('userid') . "
					</td>
				</tr>
				<tr>
					<th>
						Copper Coins
					</th>
					<td>
						<input type='number' value='0' required='1' name='copper' class='form-control'>
					</td>
				</tr>
				<tr>
					<th>
						Chivalry Tokens
					</th>
					<td>
						<input type='number' value='0' required='1' name='token' class='form-control'>
					</td>
				</tr>";
				for ($i = 1; $i <= $_POST['diffitem']; $i++) {
					echo "<tr>
						<td>
							" . inventory_dropdown("item[]") . "
						</td>
						<td>
							<input type='number' value='0' name='qty[]' class='form-control' required='1' min='1'>
						</td>
					</tr>";
				}
				echo"
				<tr>
					<td colspan='2'>
						<input type='submit' value='Submit Trade' class='btn btn-success'>
					</td>
				</tr>
			</table>
			</form>";
		}
	}
	else
	{
		echo "How many <b>different</b> items will you be traidning today?<br />
		<form method='post'>
			<input type='number' class='form-control' value='1' min='0' name='diffitem' required='1'>
			<input type='submit' class='btn btn-primary' value='Create Trade'>
		</form>";
	}
}
$h->endpage();