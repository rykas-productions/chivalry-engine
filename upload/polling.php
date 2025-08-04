<?php
/*
	File:		polling.php
	Created: 	6/23/2019 at 6:11PM Eastern Time
	Info: 		Allows players to vote in staff-created polls, and view the 
				results of said polls.
	Author:		TheMasterGeneral
	Website: 	https://github.com/MasterGeneral156/chivalry-engine
	MIT License

	Copyright (c) 2019 TheMasterGeneral

	Permission is hereby granted, free of charge, to any person obtaining a copy
	of this software and associated documentation files (the "Software"), to deal
	in the Software without restriction, including without limitation the rights
	to use, copy, modify, merge, publish, distribute, sublicense, and/or sell
	copies of the Software, and to permit persons to whom the Software is
	furnished to do so, subject to the following conditions:

	The above copyright notice and this permission notice shall be included in all
	copies or substantial portions of the Software.

	THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND, EXPRESS OR
	IMPLIED, INCLUDING BUT NOT LIMITED TO THE WARRANTIES OF MERCHANTABILITY,
	FITNESS FOR A PARTICULAR PURPOSE AND NONINFRINGEMENT. IN NO EVENT SHALL THE
	AUTHORS OR COPYRIGHT HOLDERS BE LIABLE FOR ANY CLAIM, DAMAGES OR OTHER
	LIABILITY, WHETHER IN AN ACTION OF CONTRACT, TORT OR OTHERWISE, ARISING FROM,
	OUT OF OR IN CONNECTION WITH THE SOFTWARE OR THE USE OR OTHER DEALINGS IN THE
	SOFTWARE.
*/
$voterquery = 1;
require_once('globals.php');
echo "<h3>Polling Booth</h3><hr />";
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
        $check_q = $db->query("SELECT COUNT(`id`) FROM `polls`  WHERE `active` = '1' AND `id` = {$_POST['poll']}");
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
        $q = $db->query("SELECT * FROM `polls` WHERE `active` = '1'");
        if (!$db->num_rows($q)) {
            echo "<br />There's no polls open at this time.";
        } else {
            while ($r = $db->fetch_row($q)) {
                $r['votes'] = $r['voted1'] + $r['voted2'] + $r['voted3'] + $r['voted4'] + $r['voted5'] + $r['voted6'] + $r['voted7'] + $r['voted8'] + $r['voted9'] + $r['voted10'];
                if (isset($ir['voted'][$r['id']])) {
                    echo "<br />
					<table class='table table-bordered'>
						<tr>
							<th>Polling Options</th>
							<th>Votes</th>
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
                                    $perc = round(($r[$ke] / $r['votes'] * 100), 2);
                                } else {
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
					<div class='container'>
                        <div class='row'>
                                <div class='col-sm'>
                                    <h4>Polling Options</h4>
                                </div>
                                <div class='col-sm'>
                                    <h4>Select</h4>
                                </div>
                        </div>
                        <hr />
                        <div class='row'>
                            <div class='col-sm'>
                                <h5>Polling Question: {$r['question']} (Not Voted)</h5>
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
                            echo "
                            <div class='row'>
                                <div class='col-sm'>
                                    {$r[$k]}
                                </div>
                                <div class='col-sm'>
                                    <input type='radio' class='form-control' name='choice' value='$i' $c />
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
                             <hr />
				</div></form>";
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
        $db->query("SELECT * FROM `polls` WHERE `active` = '0' ORDER BY `id` DESC");
    if (!$db->num_rows($q)) {
        alert('danger', "Uh Oh!", "There are no closed polls.", true, 'polling.php');
    } else {
        while ($r = $db->fetch_row($q)) {
            $r['votes'] = $r['voted1'] + $r['voted2'] + $r['voted3'] + $r['voted4'] + $r['voted5'] + $r['voted6'] + $r['voted7'] + $r['voted8'] + $r['voted9'] + $r['voted10'];
            echo "<table class='table table-bordered'>
					<tr>
						<th>Polling Options</th>
						<th>Votes</th>
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
                        $perc = $r[$ke] / $r['votes'] * 100;
                    } else {
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
					<th colspan='4'>Total Votes: {$r['votes']}</th>
				  </tr>
			</table><br />";
        }
    }
    $db->free_result($q);
}

$h->endpage();
