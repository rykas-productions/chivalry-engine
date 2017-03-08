<?php
$voterquery=1;
require_once('globals.php');
echo "<h3>{$lang['POLL_TITLE']}</h3><hr />";
if (!isset($_GET['action']))
{
    $_GET['action'] = '';
}
switch ($_GET['action'])
{
case "viewpolls":
    viewpolls();
    break;
default:
    home();
    break;
}
function home()
{
	global $db,$userid,$lang,$ir,$h;
	$voterquery=1;
	echo "{$lang['POLL_CYV']}<br />";
	
	$_POST['poll'] = (isset($_POST['poll']) && is_numeric($_POST['poll'])) ? abs($_POST['poll']) : '';
	$_POST['choice'] = (isset($_POST['choice']) && is_numeric($_POST['choice'])) ? abs($_POST['choice']) : '';
	$ir['voted'] = unserialize($ir['voted']);
	if (!$_POST['choice'] || !$_POST['poll'])
	{
		echo "<a href='?action=viewpolls'>{$lang['POLL_VOP']}</a>";
	}
	if ($_POST['choice'] && $_POST['poll'])
	{
		if ($ir['voted'][$_POST['poll']])
		{
			alert('danger',"{$lang['ERROR_GENERIC']}","{$lang['POLL_AVITP']}");
			die($h->endpage());
		}
		$check_q = $db->query("SELECT COUNT(`id`) FROM `polls`  WHERE `active` = '1' AND `id` = {$_POST['poll']}");
		if ($db->fetch_single($check_q) == 0)
		{
			$db->free_result($check_q);
			alert('danger',"{$lang['ERROR_GENERIC']}","{$lang['POLL_PCNT']}");
			die($h->endpage());
		}
		$db->free_result($check_q);
		$ir['voted'][$_POST['poll']] = $_POST['choice'];
		$ser = $db->escape(serialize($ir['voted']));
		$db->query(
				"UPDATE `uservotes`
				 SET `voted` = '$ser'
				 WHERE `userid` = $userid");
		$db->query("UPDATE `polls` SET `voted{$_POST['choice']}` = `voted{$_POST['choice']}` + 1 WHERE `active` = '1' AND `id` = {$_POST['poll']}");
		alert('success',"{$lang['ERROR_SUCCESS']}","{$lang['POLL_VOTE_SUCCESS']}");
	}
	else
	{
		$q = $db->query("SELECT * FROM `polls` WHERE `active` = '1'");
		if (!$db->num_rows($q))
		{
			echo "{$lang['POLL_VOTE_NOPOLL']}";
		}
		else
		{
			while ($r = $db->fetch_row($q))
			{
				$r['votes']=$r['voted1']+$r['voted2']+$r['voted3']+$r['voted4']+$r['voted5']+$r['voted6']+$r['voted7']+$r['voted8']+$r['voted9']+$r['voted10'];
				if (isset($ir['voted'][$r['id']]))
				{
					echo "<br />
					<table class='table table-bordered table-responsive'>
						<tr>
							<th>{$lang['POLL_VOTE_CHOICE']}</th>
							<th>{$lang['POLL_VOTE_VOTES']}</th>
							<th>{$lang['POLL_VOTE_PERCENT_VOTES']}</th>
						</tr>
						<tr>
							<th colspan='3'>{$r['question']} {$lang['POLL_VOTE_AV']}</th>
						</tr>";
					if (!$r['hidden'])
					{
						for ($i = 1; $i <= 10; $i++)
						{
							if ($r['choice' . $i])
							{
								$k = 'choice' . $i;
								$ke = 'voted' . $i;
								if ($r['votes'] != 0)
								{
									$perc = round(($r[$ke] / $r['votes'] * 100), 2);
								}
								else
								{
									$perc = 0;
								}
								echo "<tr>
									<td>{$r[$k]}</td>
									<td>{$r[$ke]}</td>
									<td>
										<div class='progress'>
											<div class='progress-bar' role='progressbar' aria-valuenow='{$perc}' aria-valuemin='0' aria-valuemax='100' style='width:{$perc}%'>
												{$perc}%
											</div>
										</div>
									</td>
								  </tr>";
							}
						}
					}
					else
					{
						echo "<tr>
							<td colspan='4' align='center'>
								{$lang['POLL_VOTE_HIDDEN']}
							</td>
						  </tr>";
					}
					$myvote = $r['choice' . $ir['voted'][$r['id']]];
					echo "<tr>
						<th colspan='2'>{$lang['POLL_VOTE_YVOTE']} {$myvote}</th>
						<th colspan='2'>{$lang['POLL_VOTE_TVOTE']} " . number_format($r['votes']) . "</th>
					  </tr>
				</table>";
				}
				else
				{
					echo "<br />
				<form method='post'>
					<input type='hidden' name='poll' value='{$r['id']}' />
					<table class='table table-bordered table-responsive'>
						<tr>
							<th>{$lang['POLL_VOTE_CHOICE']}</th>
							<th>{$lang['POLL_VOTE_VOTEC']}</th>
						</tr>
						<tr>
							<th colspan='2'>{$lang['POLL_VOTE_QUESTION']} {$r['question']} {$lang['POLL_VOTE_NV']}</th>
						</tr>";
					for ($i = 1; $i <= 10; $i++)
					{
						if ($r['choice' . $i])
						{
							$k = 'choice' . $i;
							if ($i == 1)
							{
								$c = "checked='checked'";
							}
							else
							{
								$c = "";
							}
							echo "<tr>
								<td>{$r[$k]}</td>
								<td><input type='radio' class='form-control' name='choice' value='$i' $c /></td>
							  </tr>";
						}
					}
					echo "<tr>
						<td colspan='2'><input type='submit' class='btn btn-default' value='{$lang['POLL_VOTE_CAST']}' /></td>
					  </tr>
				</table></form>";
				}
			}
		}
		$db->free_result($q);
	}
}
function viewpolls()
{
	global $db,$userid,$lang,$ir,$h;
	echo "<a href='polling.php'>{$lang['POLL_CYV']}</a><br />";
		$q =
			$db->query("SELECT * FROM `polls` WHERE `active` = '0' ORDER BY `id` DESC");
	if (!$db->num_rows($q))
	{
		alert('danger',"{$lang['ERROR_GENERIC']}","{$lang['POLL_VOTE_NOCLOSED']}");
	}
	else
	{
		while ($r = $db->fetch_row($q))
		{
			$r['votes']=$r['voted1']+$r['voted2']+$r['voted3']+$r['voted4']+$r['voted5']+$r['voted6']+$r['voted7']+$r['voted8']+$r['voted9']+$r['voted10'];
			echo "<table class='table table-bordered table-responsive'>
					<tr>
						<th>{$lang['POLL_VOTE_CHOICE']}</th>
						<th>{$lang['POLL_VOTE_VOTES']}</th>
						<th>{$lang['POLL_VOTE_PERCENT_VOTES']}</th>
					</tr>
					<tr>
						<th colspan='4'>{$lang['POLL_VOTE_QUESTION']} {$r['question']}</th>
					</tr>";
			for ($i = 1; $i <= 10; $i++)
			{
				if ($r['choice' . $i])
				{
					$k = 'choice' . $i;
					$ke = 'voted' . $i;
					if ($r['votes'] != 0)
					{
						$perc = $r[$ke] / $r['votes'] * 100;
					}
					else
					{
						$perc = 0;
					}
					echo "<tr>
							<td>{$r[$k]}</td>
							<td>{$r[$ke]}</td>
							<td>
								<div class='progress'>
											<div class='progress-bar' role='progressbar' aria-valuenow='{$perc}' aria-valuemin='0' aria-valuemax='100' style='width:{$perc}%'>
												{$perc}%
											</div>
										</div>
							</td>
						  </tr>";
				}
			}
			echo "<tr>
					<th colspan='4'>{$lang['POLL_VOTE_TVOTE']} {$r['votes']}</th>
				  </tr>
			</table><br />";
		}
	}
$db->free_result($q);
}
$h->endpage();
