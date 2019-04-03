<?php
require('globals.php');
$tq=$db->query("SELECT * FROM `tcl_userdata` WHERE `tsl_userid` = {$userid}");
if ($db->num_rows($tq) == 0)
{
	$db->query("INSERT INTO `tcl_userdata` (`tsl_userid`, `tsl_phase`, `tsl_phase_end`, `tsl_hp`, `tsl_maxhp`, `tsl_tokens`) VALUES ('{$userid}', '1', '0', '100', '100', '0');");
	$db->query("INSERT INTO `tsl_user_mercs` (`tsl_userid`, `tsl_merc_id`, `tsl_merc_weap`, `tsl_merc_hp`) VALUES ('{$userid}', '1', '1', '100');");
}
$tls=$db->fetch_row($tq);
$tls['merc_count']=$db->fetch_single($db->query("SELECT COUNT(*) FROM `tsl_user_mercs` WHERE `tsl_userid` = {$userid}"));
if (!isset($_GET['action'])) {
    $_GET['action'] = '';
}
switch ($_GET['action']) {
    case 'phase0':
        phase0();
        break;
    default:
        home();
        break;
}
function home()
{
	global $db,$ir,$h,$tls;
	echo "Redirecting...";
	if ($tsl['tsl_phase'] == 0)
		$newurl='?action=phase0';
	else
		$newurl='?action=phase1';
	header('Location: '.$newurl);
}

function phase0()
{
	global $db,$userid,$ir,$h,$tls;
	echo "You are currently in the <b>Planning Phase</b>.<hr />
	<h5>Your Structure Info</h5><hr />
		Structure HP: {$tls['tsl_hp']} / {$tls['tsl_maxhp']}.<br />
		Points: {$tls['tsl_tokens']}.<br />
		Hired Mercs: {$tls['merc_count']} / 5 [<a href='?action=hiremerc'>Hire Mercs</a>]
		<hr />
		Fill out the form 
	below to setup how you wish to plan for your structure's night of survival. After submitting the form, this cannot be changed.
	<br />
	<table class='table table-bordered'>
		<tr>
			<th>
				Repair Time*
			</th>
			<td>
				<input type='number' class='form-control' min='0' max='12' value='0'>
			</td>
		</tr>
		<tr>
			<th>
				First Aid*
			</th>
			<td>
				<input type='number' class='form-control' min='0' max='12' value='0'>
			</td>
		</tr>
		<tr>
			<th>
				Supply Search*
			</th>
			<td>
				<input type='number' class='form-control' min='0' max='12' value='0'>
			</td>
		</tr>
	</table>
	<small>*in hours.</small>";
}
$h->endpage();