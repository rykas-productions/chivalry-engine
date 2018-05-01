<?php
/*
	File:		polls.php
	Created: 	4/5/2016 at 12:22AM Eastern Time
	Info: 		Allows players to vote in game polls, and view closed
				polls.
	Author:		TheMasterGeneral
	Website: 	https://github.com/MasterGeneral156/chivalry-engine
*/
$voterquery = 1;
require_once('globals.php');
echo "<h3><i class='game-icon game-icon-vote'></i> Polling Booth</h3><hr />";
if (!isset($_GET['action'])) {
    $_GET['action'] = '';
}
switch ($_GET['action']) {
    case "viewpolls":
        viewpolls();
        break;
    default:
        home();
        break;
}
function home()
{
    global $db, $userid, $ir, $h;
    echo "Cast your vote today!<br />";

    $_POST['poll'] = (isset($_POST['poll']) && is_numeric($_POST['poll'])) ? abs($_POST['poll']) : '';
    $_POST['choice'] = (isset($_POST['choice']) && is_numeric($_POST['choice'])) ? abs($_POST['choice']) : '';
    $ir['voted'] = unserialize($ir['voted']);
    if (!$_POST['choice'] || !$_POST['poll']) {
        echo "<a href='?action=viewpolls'>View Closed Polls</a>";
    }
    if ($_POST['choice'] && $_POST['poll']) {
        if (isset($ir['voted'][$_POST['poll']])) {
            alert('danger', "Uh Oh!", "You have already voted in this poll.");
            die($h->endpage());
        }
        $check_q = $db->query("/*qc=on*/SELECT COUNT(`id`) FROM `polls`  WHERE `active` = '1' AND `id` = {$_POST['poll']} AND `visibility` = 0");
        if ($db->fetch_single($check_q) == 0) {
            $db->free_result($check_q);
            alert('danger', "Uh Oh!", "Poll does not exist, or is no longer active.");
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
        alert('success', "Success!", "You have successfully submitted your vote.", true, 'polling.php');
    } else {
        $q = $db->query("/*qc=on*/SELECT * FROM `polls` WHERE `active` = '1' AND `visibility` = 0");
        if (!$db->num_rows($q)) {
            echo "<br />There's no polls open at this time.";
        } else {
            while ($r = $db->fetch_row($q)) {
                $r['votes'] = $r['voted1'] + $r['voted2'] + $r['voted3'] + $r['voted4'] + $r['voted5'] + $r['voted6'] + $r['voted7'] + $r['voted8'] + $r['voted9'] + $r['voted10'];
                if (isset($ir['voted'][$r['id']])) {
                    echo "<br />
					<table class='table table-bordered'>
						<tr>
							<th width='40%'>Polling Options</th>
							<th width='10%'>Votes</th>
							<th>Percentage</th>
						</tr>
						<tr>
							<th colspan='3'>Polling Question: {$r['question']} (Already Voted!)</th>
						</tr>";
                    if (!$r['hidden']) {
                        for ($i = 1; $i <= 10; $i++) {
                            if ($r['choice' . $i]) {
                                $k = 'choice' . $i;
                                $ke = 'voted' . $i;
                                if ($r['votes'] != 0) {
                                    $perc = round(($r[$ke] / $r['votes'] * 100));
                                } else {
                                    $perc = 0;
                                }
                                echo "<tr>
									<td>{$r[$k]}</td>
									<td>{$r[$ke]}</td>
									<td>
										<div class='progress' style='height: 1rem;'>
											<div class='progress-bar' role='progressbar' aria-valuenow='{$perc}' style='width:{$perc}%' aria-valuemin='0' aria-valuemax='100'></div>
											<span>{$perc}%</span>
										</div>
									</td>
								  </tr>";
                            }
                        }
                    } else {
                        echo "<tr>
							<td colspan='4' align='center'>
								Results are hidden until the poll ends.
							</td>
						  </tr>";
                    }
                    $myvote = $r['choice' . $ir['voted'][$r['id']]];
                    echo "<tr>
						<th colspan='2'>You Voted: {$myvote}</th>
						<th colspan='2'>Total Votes " . number_format($r['votes']) . "</th>
					  </tr>
				</table>";
                } else {
                    echo "<br />
				<form method='post'>
					<input type='hidden' name='poll' value='{$r['id']}' />
					<table class='table table-bordered'>
						<tr>
							<th>Polling Options</th>
							<th>Select</th>
						</tr>
						<tr>
							<th colspan='2'>Polling Question: {$r['question']} (Not Voted)</th>
						</tr>";
                    for ($i = 1; $i <= 10; $i++) {
                        if ($r['choice' . $i]) {
                            $k = 'choice' . $i;
                            if ($i == 1) {
                                $c = "checked='checked'";
                            } else {
                                $c = "";
                            }
                            echo "<tr>
								<td>{$r[$k]}</td>
								<td><input type='radio' class='form-control' name='choice' value='$i' $c /></td>
							  </tr>";
                        }
                    }
                    echo "<tr>
						<td colspan='2'><input type='submit' class='btn btn-primary' value='Cast Vote' /></td>
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
    global $db;
    echo "<a href='polling.php'>Cast Your Vote!</a><br />";
    $q =
        $db->query("/*qc=on*/SELECT * FROM `polls` WHERE `active` = '0' AND `visibility` = 0 ORDER BY `id` DESC");
    if (!$db->num_rows($q)) {
        alert('danger', "Uh Oh!", "There are no closed polls.", true, 'polling.php');
    } else {
        while ($r = $db->fetch_row($q)) {
            $r['votes'] = $r['voted1'] + $r['voted2'] + $r['voted3'] + $r['voted4'] + $r['voted5'] + $r['voted6'] + $r['voted7'] + $r['voted8'] + $r['voted9'] + $r['voted10'];
            echo "<table class='table table-bordered'>
					<tr>
						<th width='40%'>Polling Options</th>
						<th width='10%'>Votes</th>
						<th>Percentage</th>
					</tr>
					<tr>
						<th colspan='4'>Polling Question: {$r['question']}</th>
					</tr>";
            for ($i = 1; $i <= 10; $i++) {
                if ($r['choice' . $i]) {
                    $k = 'choice' . $i;
                    $ke = 'voted' . $i;
                    if ($r['votes'] != 0) {
                        $perc = round($r[$ke] / $r['votes'] * 100);
                    } else {
                        $perc = 0;
                    }
                    echo "<tr>
							<td>{$r[$k]}</td>
							<td>{$r[$ke]}</td>
							<td>
								<div class='progress' style='height: 1rem;'>
									<div class='progress-bar' role='progressbar' aria-valuenow='{$perc}' style='width:{$perc}%' aria-valuemin='0' aria-valuemax='100'></div>
									<span>{$perc}%</span>
								</div>
							</td>
						  </tr>";
                }
            }
            echo "<tr>
					<th colspan='4'>Total Votes: {$r['votes']}</th>
				  </tr>
			</table><br />";
        }
    }
    $db->free_result($q);
}

$h->endpage();
