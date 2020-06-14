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
                     echo "<hr /><div class='row'>
								<div class='col-sm-3'>
									<h3>Poll Question</h3>
								</div>
								<div class='col-sm'>
									<h6>{$r['question']}</h6>
								</div>
							</div>
							<hr />";
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
                                echo "<div class='row'>
										<div class='col-sm-3'>
											{$r[$k]}
										</div>
										<div class='col-sm-1'>
											{$r[$ke]}
										</div>
										<div class='col-sm'>
											<div class='progress' style='height: 1rem;'>
												<div class='progress-bar' role='progressbar' aria-valuenow='{$perc}' style='width:{$perc}%' aria-valuemin='0' aria-valuemax='100'><span>{$perc}%</span></div>
											</div>
										</div>
									</div>
									<hr />";
                            }
                        }
                    } else {
                        echo "<div class='row'>
							<div class='col-sm-3'>
								<h5>Results hidden.</h5>
							</div>
						</div>
						<hr />";
                    }
                    $myvote = $r['choice' . $ir['voted'][$r['id']]];
				echo "<div class='row'>
							<div class='col-sm-3'>
								<h5>Total Votes</h5>
							</div>
							<div class='col-sm-1'>
								<h6>" . number_format($r['votes']) . "</h6>
							</div>
							<div class='col-sm-3'>
								<h5>Your Vote</h5>
							</div>
							<div class='col-sm-3'>
								<i>{$myvote}</i>
							</div>
						</div>
						<hr />";
                } else {
                    echo "<br />
				<form method='post'>
					<input type='hidden' name='poll' value='{$r['id']}' />
					<hr />
					<div class='row'>
						<div class='col-sm-3'>
							<h3>Poll Question</h3>
						</div>
						<div class='col-sm'>
							<h6>{$r['question']}</h6>
						</div>
					</div>
					<hr />";
                    for ($i = 1; $i <= 10; $i++) {
                        if ($r['choice' . $i]) {
                            $k = 'choice' . $i;
                            if ($i == 1) {
                                $c = "checked='checked'";
                            } else {
                                $c = "";
                            }
							echo "<div class='row'>
								<div class='col-sm-3'>
									{$r[$k]}
								</div>
								<div class='col-sm'>
										<input type='radio' class='form-control' name='choice' value='{$i}' {$c} />
									</div>
							</div>
							<hr />";
                        }
                    }
                    echo "<div class='row'>
							<div class='col-sm'>
								<input type='submit' class='btn btn-primary' value='Cast Vote' />
							</div>
							</div>
						</form>
						<hr />";
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
            echo "
			<hr /><div class='row'>
				<div class='col-sm-3'>
					<h3>Poll Question</h3>
				</div>
				<div class='col-sm'>
					<h6>{$r['question']}</h6>
				</div>
			</div>
			<hr />";
            for ($i = 1; $i <= 10; $i++) {
                if ($r['choice' . $i]) {
                    $k = 'choice' . $i;
                    $ke = 'voted' . $i;
                    if ($r['votes'] != 0) {
                        $perc = round($r[$ke] / $r['votes'] * 100);
                    } else {
                        $perc = 0;
                    }
					echo "<div class='row'>
							<div class='col-sm-3'>
								{$r[$k]}
							</div>
							<div class='col-sm-1'>
								{$r[$ke]}
							</div>
							<div class='col-sm'>
								<div class='progress' style='height: 1rem;'>
									<div class='progress-bar' role='progressbar' aria-valuenow='{$perc}' style='width:{$perc}%' aria-valuemin='0' aria-valuemax='100'><span>{$perc}%</span></div>
								</div>
							</div>
						</div>
						<hr />";
                }
            }
			echo "<div class='row'>
							<div class='col-sm-3'>
								<h5>Total Votes</h5>
							</div>
							<div class='col-sm-1'>
								<h6>" . number_format($r['votes']) . "</h6>
							</div>
						</div>
						<hr />";
        }
    }
    $db->free_result($q);
}

$h->endpage();
