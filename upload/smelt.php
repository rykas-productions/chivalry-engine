<?php
require('globals.php');
echo "<h3>{$lang['SMELT_HOME']}</h3><hr />";
if (!isset($_GET['action']))
{
    $_GET['action'] = '';
}
switch ($_GET['action'])
{
case 'smelt':
    smelt();
    break;
default:
    home();
    break;
}
function home()
{
	global $db,$userid,$api,$lang,$h;
	$q=$db->query("SELECT * FROM `smelt_recipes` ORDER BY `smelt_id` ASC");
	echo "<table class='table table-bordered table-striped'>
	<tr>
		<th>
			{$lang['SMELT_TH']}
		</th>
		<th>
			{$lang['SMELT_TH1']}
		</th>
		<th>
			{$lang['SMELT_TH2']}
		</th>
	</tr>";
	while ($r=$db->fetch_row($q))
	{
		$output_item=$api->SystemItemIDtoName($r['smelt_output']);
		echo "
		<tr>
			<td>
			{$r['smelt_qty_output']}x {$output_item} 
			</td>
			<td>
			</td>
			<td>
				{$action}
			</td>
		</tr>";
	}
	echo "</table>";
}
$h->endpage();