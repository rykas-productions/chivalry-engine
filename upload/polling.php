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
                     echo "<div class='card'>
                            <div class='card-body'>
                            <div class='row'>
								<div class='col-12'>
									<h3>Poll Question</h3>
								</div>
								<div class='col-12'>
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
										<div class='col-12 col-xl-4'>
											{$r[$k]}
										</div>
										<div class='col-12 col-xl'>
											" . scaledColorProgressBar($r[$ke], 0, $r['votes']) . "
										</div>
									</div>
									<hr />";
                            }
                        }
                    } else {
                        echo "<div class='row'>
							<div class='col'>
								<h5>Results hidden until the poll closes.</h5>
							</div>
						</div>";
                    }
                    $myvote = $r['choice' . $ir['voted'][$r['id']]];
				echo "<div class='row'>
							<div class='col'>
                                <div class='row'>
                                    <div class='col-12'>
								        <h5>Total Votes</h5>
                                    </div>
                                    <div class='col'>
                                        <h6>" . shortNumberParse($r['votes']) . "</h6>
                                    </div>
                                </div>
							</div>
                            <div class='col'>
                                <div class='row'>
                                    <div class='col-12'>
								        <h5>Your Vote</h5>
                                    </div>
                                    <div class='col'>
                                        <i>{$myvote}</i>
                                    </div>
                                </div>
							</div>
						</div></div></div><br />";
                } else {
                    echo "
				<form method='post'>
                    <input type='hidden' name='poll' value='{$r['id']}' />
					<div class='card'>
                            <div class='card-body'>
                            <div class='row'>
								<div class='col-12 col-xl'>
									<h3>Poll Question</h3>
								</div>
								<div class='col-12 col-xl'>
									<h6>{$r['question']}</h6>
								</div>
							</div>
							<hr />";
                    $pollOptions = "";
                    for ($i = 1; $i <= 10; $i++) 
                    {
                        $k = 'choice' . $i;
                        if ($r['choice' . $i]) 
                        {
							$pollOptions.="<option value='{$i}'>{$r[$k]}</option>";
                        }
                    }
                    if (empty($pollOptions))
                        $pollOptions.="<option>No options available</option>";
                    echo "<div class='row'>
                            <div class='col-12 col-xl'>
                                <select name='choice' id='class' class='form-control' type='dropdown'>
                                    {$pollOptions}
        				        </select>
                            </div>
							<div class='col-12 col-xl'>
								<input type='submit' class='btn btn-primary' value='Cast Vote' />
							</div>
							</div>
						</form>
						</div></div><br />";
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
            if ($r['votes'] == 0)
                $r['votes'] = 1;
            echo "<div class='card'>
                    <div class='card-body'>
                    <div class='row'>
						<div class='col-12'>
							<h3>Poll Question</h3>
						</div>
						<div class='col-12'>
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
										<div class='col-12 col-xl-4'>
											{$r[$k]}
										</div>
										<div class='col-12 col-xl'>
											" . scaledColorProgressBar($r[$ke], 0, $r['votes']) . "
										</div>
									</div>
									<hr />";
                }
            }
			echo "<div class='row'>
							<div class='col'>
								<h5>Total Votes</h5>
							</div>
							<div class='col'>
								<h6>" . shortNumberParse($r['votes']) . "</h6>
							</div>
						</div>
						</div></div><br />";
        }
    }
    $db->free_result($q);
}

$h->endpage();
