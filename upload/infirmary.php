<?php
/*
    File: infirmary_modern.php
    Created: Modernized infirmary with better UI
    Info: View and heal players in the infirmary
*/
require("globals.php");

if (!isset($_GET['action'])) {
    $_GET['action'] = '';
}

switch ($_GET['action']) {
    case 'heal':
        heal();
        break;
    default:
        home();
        break;
}

function home()
{
    global $db, $api, $userid, $ir;
    
    $CurrentTime = time();
    $PlayerCount = $db->fetch_single($db->query("SELECT COUNT(`infirmary_user`) FROM `infirmary` WHERE `infirmary_out` > {$CurrentTime}"));
    
    // Check if current user is in infirmary
    $userInInfirmary = false;
    $userInfirmaryData = null;
    if (userInInfirmary($userid)) {
        $userInInfirmary = true;
        $userInfirmaryData = $db->fetch_row($db->query("SELECT * FROM `infirmary` WHERE `infirmary_user` = {$userid}"));
    }
    ?>
    
    <div class="container-fluid">
        <!-- Header -->
        <div class="row mb-4">
            <div class="col-12">
                <div class="card bg-gradient-danger text-white">
                    <div class="card-body">
                        <h2 class="mb-0"><i class="fas fa-hospital-user me-2"></i>The Infirmary</h2>
                        <p class="mb-0 mt-2">Currently treating <?php echo number_format($PlayerCount); ?> patient<?php echo $PlayerCount != 1 ? 's' : ''; ?></p>
                    </div>
                </div>
            </div>
        </div>
        
        <?php if ($userInInfirmary): ?>
        <!-- User is in infirmary alert -->
        <div class="row mb-4">
            <div class="col-12">
                <div class="alert alert-danger border-0 shadow">
                    <h4 class="alert-heading"><i class="fas fa-bed-pulse me-2"></i>You are currently in the infirmary!</h4>
                    <hr>
                    <p class="mb-2"><strong>Reason:</strong> <?php echo $userInfirmaryData['infirmary_reason']; ?></p>
                    <p class="mb-2"><strong>Time Remaining:</strong> <?php echo timeUntilParse($userInfirmaryData['infirmary_out']); ?></p>
                    <hr>
                    <p class="mb-0"><i class="fas fa-info-circle"></i> You cannot perform most actions while in the infirmary. Wait for recovery or have someone heal you.</p>
                </div>
            </div>
        </div>
        <?php endif; ?>
        
        <!-- Infirmary Patients -->
        <div class="row">
            <div class="col-12">
                <div class="card">
                    <div class="card-header bg-dark text-white">
                        <h5 class="mb-0"><i class="fas fa-users-medical me-2"></i>Current Patients</h5>
                    </div>
                    <div class="card-body">
                        <?php if ($PlayerCount == 0): ?>
                            <div class="text-center py-5">
                                <i class="fas fa-smile fa-4x text-success mb-3"></i>
                                <h4>No patients in the infirmary!</h4>
                                <p class="text-muted">Everyone is healthy and ready for action.</p>
                            </div>
                        <?php else: ?>
                            <div class="table-responsive">
                                <table class="table table-hover">
                                    <thead class="table-dark">
                                        <tr>
                                            <th>Patient</th>
                                            <th>Level</th>
                                            <th>Reason</th>
                                            <th>Time Remaining</th>
                                            <th>Recovery Progress</th>
                                            <th>Actions</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php
                                        $query = $db->query("SELECT i.*, u.username, u.level 
                                                           FROM `infirmary` i 
                                                           INNER JOIN `users` u ON i.infirmary_user = u.userid
                                                           WHERE i.`infirmary_out` > {$CurrentTime} 
                                                           ORDER BY i.`infirmary_out` ASC");
                                        
                                        while ($patient = $db->fetch_row($query)) {
                                            $timeLeft = $patient['infirmary_out'] - time();
                                            $percentComplete = max(0, min(100, 100 - (($timeLeft / 3600) * 100)));
                                            $isCurrentUser = ($patient['infirmary_user'] == $userid);
                                            ?>
                                            <tr <?php echo $isCurrentUser ? 'class="table-warning"' : ''; ?>>
                                                <td>
                                                    <a href="profile.php?user=<?php echo $patient['infirmary_user']; ?>" class="text-decoration-none">
                                                        <strong><?php echo $patient['username']; ?></strong>
                                                    </a>
                                                    <?php if ($isCurrentUser): ?>
                                                        <span class="badge bg-warning text-dark ms-1">You</span>
                                                    <?php endif; ?>
                                                </td>
                                                <td>
                                                    <span class="badge bg-secondary">Level <?php echo number_format($patient['level']); ?></span>
                                                </td>
                                                <td><?php echo $patient['infirmary_reason']; ?></td>
                                                <td>
                                                    <span class="text-danger">
                                                        <i class="fas fa-clock"></i> <?php echo timeUntilParse($patient['infirmary_out']); ?>
                                                    </span>
                                                </td>
                                                <td>
                                                    <div class="progress" style="height: 20px;">
                                                        <div class="progress-bar bg-success progress-bar-striped" 
                                                             style="width: <?php echo $percentComplete; ?>%">
                                                            <?php echo round($percentComplete); ?>%
                                                        </div>
                                                    </div>
                                                </td>
                                                <td>
                                                    <a href="?action=heal&user=<?php echo $patient['infirmary_user']; ?>" 
                                                       class="btn btn-sm btn-success">
                                                        <i class="fas fa-heart"></i> <?php echo $isCurrentUser ? 'Heal Yourself' : 'Heal'; ?>
                                                    </a>
                                                </td>
                                            </tr>
                                            <?php
                                        }
                                        ?>
                                    </tbody>
                                </table>
                            </div>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
        </div>
        
        <!-- Info Cards -->
        <div class="row mt-4">
            <div class="col-md-6">
                <div class="card">
                    <div class="card-body">
                        <h5 class="card-title"><i class="fas fa-info-circle text-info"></i> About the Infirmary</h5>
                        <p class="card-text">Players end up in the infirmary when they lose fights or suffer injuries. While in the infirmary:</p>
                        <ul>
                            <li>You cannot attack other players</li>
                            <li>You cannot perform most actions</li>
                            <li>Your HP slowly regenerates</li>
                            <li>Other players can heal you out early</li>
                        </ul>
                    </div>
                </div>
            </div>
            <div class="col-md-6">
                <div class="card">
                    <div class="card-body">
                        <h5 class="card-title"><i class="fas fa-coins text-warning"></i> Healing Costs</h5>
                        <p class="card-text">You can heal other players out of the infirmary using <?php echo constant("secondary_currency"); ?>:</p>
                        <ul>
                            <li>Base cost: 25 <?php echo constant("secondary_currency"); ?> per minute remaining</li>
                            <li>Your current balance: <strong><?php echo number_format($ir['secondary_currency']); ?> <?php echo constant("secondary_currency"); ?></strong></li>
                            <li>Healing someone builds reputation</li>
                            <li>You can heal yourself or others</li>
                        </ul>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <?php
}

function heal()
{
    global $api, $h, $userid, $ir, $db;
    
    if (isset($_GET['user'])) {
        $_GET['user'] = abs((int) $_GET['user']);
        
        
        if (!userInInfirmary($_GET['user'])) {
            alert('danger', "Uh Oh!", "This player is not in the infirmary!", true, 'infirmary.php');
            die($h->endpage());
        }
        
        $CurrentTime = time();
        $query = $db->query("SELECT * FROM `infirmary` WHERE `infirmary_user` = {$_GET['user']}");
        $Infirmary = $db->fetch_row($query);
        
        $TimeRemaining = $Infirmary['infirmary_out'] - $CurrentTime;
        $MinutesRemaining = ceil($TimeRemaining / 60);
        $cost = $MinutesRemaining * 25;
        
        if (isset($_POST['heal'])) {
            if (!isset($_POST['csrf']) || !checkCSRF('heal_' . $_GET['user'], stripslashes($_POST['csrf']))) {
                alert('danger', "Action Blocked!", "Your action was blocked for security reasons. Please try again.", true, 'infirmary.php');
                die($h->endpage());
            }
            
            if ($cost > $ir['secondary_currency']) {
                alert('danger', "Uh Oh!", "You need " . number_format($cost) . " " . constant("secondary_currency") . " to heal this player, but you only have " . number_format($ir['secondary_currency']) . ".", true, 'infirmary.php');
                die($h->endpage());
            }
            
            // Heal the player
            $db->query("UPDATE `infirmary` SET `infirmary_out` = 0 WHERE `infirmary_user` = {$_GET['user']}");
            $db->query("UPDATE `users` SET `secondary_currency` = `secondary_currency` - {$cost} WHERE `userid` = {$userid}");
            
            // Notify the healed player
            $api->user->addNotification($_GET['user'], "<a href='profile.php?user={$userid}'>{$ir['username']}</a> healed you out of the infirmary!");
            
            // Log the action
            $api->game->addLog($userid, 'healing', "Healed <a href='profile.php?user={$_GET['user']}'>{$api->user->getNamefromID($_GET['user'])}</a> out of the infirmary for {$cost} " . constant("secondary_currency"));
            
            alert('success', "Success!", "You have healed {$api->user->getNamefromID($_GET['user'])} out of the infirmary for " . number_format($cost) . " " . constant("secondary_currency") . ".", true, 'infirmary.php');
            die($h->endpage());
        } else {
            $csrf = getCodeCSRF('heal_' . $_GET['user']);
            ?>
            <div class="container-fluid">
                <div class="row justify-content-center">
                    <div class="col-md-6">
                        <div class="card">
                            <div class="card-header bg-success text-white">
                                <h4 class="mb-0"><i class="fas fa-heart-pulse"></i> Heal Player</h4>
                            </div>
                            <div class="card-body">
                                <div class="alert alert-info">
                                    <h5>Healing <?php echo $api->user->getNamefromID($_GET['user']); ?></h5>
                                    <hr>
                                    <p><strong>Time Remaining:</strong> <?php echo timeUntilParse($Infirmary['infirmary_out']); ?></p>
                                    <p><strong>Reason:</strong> <?php echo $Infirmary['infirmary_reason']; ?></p>
                                    <p><strong>Healing Cost:</strong> <span class="text-danger"><?php echo number_format($cost); ?> <?php echo constant("secondary_currency"); ?></span></p>
                                    <p><strong>Your Balance:</strong> <span class="text-success"><?php echo number_format($ir['secondary_currency']); ?> <?php echo constant("secondary_currency"); ?></span></p>
                                </div>
                                
                                <?php if ($cost <= $ir['secondary_currency']): ?>
                                    <form method="post">
                                        <input type="hidden" name="csrf" value="<?php echo $csrf; ?>">
                                        <div class="d-grid gap-2">
                                            <button type="submit" name="heal" class="btn btn-success btn-lg">
                                                <i class="fas fa-heart"></i> Confirm Healing (<?php echo number_format($cost); ?> <?php echo constant("secondary_currency"); ?>)
                                            </button>
                                            <a href="infirmary.php" class="btn btn-secondary">
                                                <i class="fas fa-arrow-left"></i> Cancel
                                            </a>
                                        </div>
                                    </form>
                                <?php else: ?>
                                    <div class="alert alert-danger">
                                        <i class="fas fa-exclamation-triangle"></i> You don't have enough <?php echo constant("secondary_currency"); ?> to heal this player!
                                    </div>
                                    <a href="infirmary.php" class="btn btn-secondary">
                                        <i class="fas fa-arrow-left"></i> Go Back
                                    </a>
                                <?php endif; ?>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <?php
        }
    } else {
        alert('danger', "Uh Oh!", "Please specify a player to heal.", true, 'infirmary.php');
    }
}

$h->endpage();
?>