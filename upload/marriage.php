<?php
/*
    File: marriage.php
    Created: Marriage system interface
    Info: Handles marriage proposals, viewing spouse, divorce, etc.
*/
require("globals.php");

// Check if marriage tables exist
$tables_exist = true;
try {
    $db->query("SELECT 1 FROM marriages LIMIT 1");
    $db->query("SELECT 1 FROM marriage_proposals LIMIT 1");
} catch (Exception $e) {
    $tables_exist = false;
}

if (!$tables_exist) {
    echo "<div class='container-fluid'>";
    echo "<div class='alert alert-warning'>";
    echo "<h4><i class='fas fa-exclamation-triangle'></i> Marriage System Not Set Up</h4>";
    echo "<p>The marriage system database tables need to be created first.</p>";
    if ($api->user->getStaffLevel($userid, 'admin')) {
        echo "<p><a href='create_marriage_tables.php' class='btn btn-primary'>Set Up Marriage System</a></p>";
    } else {
        echo "<p>Please contact an administrator to set up the marriage system.</p>";
    }
    echo "</div>";
    echo "</div>";
    $h->endpage();
    exit;
}

require_once("includes/marriage-system.php");

// Initialize marriage system
$marriageSystem = getMarriageSystem($db, $userid);

if (!isset($_GET['action'])) {
    $_GET['action'] = '';
}

switch ($_GET['action']) {
    case 'propose':
        propose();
        break;
    case 'respond':
        respond();
        break;
    case 'divorce':
        divorce();
        break;
    case 'send_gift':
        sendGift();
        break;
    default:
        home();
        break;
}

function home() {
    global $marriageSystem, $ir, $db;
    
    echo "<div class='container-fluid'>";
    echo "<div class='row mb-4'>";
    echo "<div class='col-12'>";
    echo "<div class='card bg-gradient-primary text-white'>";
    echo "<div class='card-body'>";
    echo "<h2 class='mb-0'><i class='fas fa-heart me-2'></i>Marriage Center</h2>";
    echo "<p class='mb-0 mt-2'>Find love, propose, and build relationships</p>";
    echo "</div>";
    echo "</div>";
    echo "</div>";
    echo "</div>";
    
    if ($marriageSystem->isMarried()) {
        // Show spouse information
        $spouse = $marriageSystem->getSpouse();
        $stats = $marriageSystem->getMarriageStats();
        
        echo "<div class='row mb-4'>";
        echo "<div class='col-md-6'>";
        echo "<div class='card'>";
        echo "<div class='card-header bg-success text-white'>";
        echo "<h4><i class='fas fa-ring'></i> Your Marriage</h4>";
        echo "</div>";
        echo "<div class='card-body'>";
        echo "<div class='d-flex align-items-center mb-3'>";
        if ($spouse['display_pic']) {
            echo "<img src='{$spouse['display_pic']}' class='rounded-circle me-3' width='60' height='60'>";
        } else {
            echo "<div class='bg-secondary rounded-circle me-3 d-flex align-items-center justify-content-center' style='width: 60px; height: 60px;'>";
            echo "<i class='fas fa-user fa-2x text-white'></i>";
            echo "</div>";
        }
        echo "<div>";
        echo "<h5 class='mb-1'><a href='profile.php?user={$spouse['userid']}'>{$spouse['username']}</a></h5>";
        echo "<p class='text-muted mb-0'>Level {$spouse['level']}</p>";
        echo "</div>";
        echo "</div>";
        
        echo "<div class='row text-center'>";
        echo "<div class='col-4'>";
        echo "<div class='border-end'>";
        echo "<h3 class='text-primary'>{$stats['days_married']}</h3>";
        echo "<small class='text-muted'>Days Married</small>";
        echo "</div>";
        echo "</div>";
        echo "<div class='col-4'>";
        echo "<div class='border-end'>";
        echo "<h3 class='text-success'>{$stats['gifts_sent']}</h3>";
        echo "<small class='text-muted'>Gifts Sent</small>";
        echo "</div>";
        echo "</div>";
        echo "<div class='col-4'>";
        echo "<h3 class='text-info'>{$stats['gifts_received']}</h3>";
        echo "<small class='text-muted'>Gifts Received</small>";
        echo "</div>";
        echo "</div>";
        
        echo "<div class='mt-3'>";
        echo "<p><strong>Married since:</strong> " . date('F j, Y', $spouse['marriage_date']) . "</p>";
        $lastOnline = $spouse['laston'] > 0 ? dateTimeParse($spouse['laston']) : 'Never';
        echo "<p><strong>Last seen:</strong> {$lastOnline}</p>";
        echo "</div>";
        
        echo "<div class='mt-3'>";
        echo "<a href='?action=send_gift' class='btn btn-primary me-2'><i class='fas fa-gift'></i> Send Gift</a>";
        echo "<a href='inbox.php?action=compose&to={$spouse['userid']}' class='btn btn-success me-2'><i class='fas fa-envelope'></i> Send Message</a>";
        echo "<a href='?action=divorce' class='btn btn-danger' onclick='return confirm(\"Are you sure you want to divorce? This cannot be undone!\")'><i class='fas fa-heart-broken'></i> Divorce</a>";
        echo "</div>";
        
        echo "</div>";
        echo "</div>";
        echo "</div>";
        
        // Recent gifts section
        echo "<div class='col-md-6'>";
        echo "<div class='card'>";
        echo "<div class='card-header bg-info text-white'>";
        echo "<h4><i class='fas fa-gifts'></i> Recent Gifts</h4>";
        echo "</div>";
        echo "<div class='card-body'>";
        
        $gifts = $db->query("
            SELECT g.*, u.username as sender_name, i.itmname 
            FROM marriage_gifts g
            LEFT JOIN users u ON g.sender_id = u.userid
            LEFT JOIN items i ON g.item_id = i.itmid
            WHERE g.marriage_id = {$stats['marriage_id']}
            ORDER BY g.sent_date DESC
            LIMIT 5
        ");
        
        if ($db->num_rows($gifts) > 0) {
            while ($gift = $db->fetch_row($gifts)) {
                echo "<div class='d-flex justify-content-between align-items-center mb-2 p-2 bg-light rounded'>";
                echo "<div>";
                echo "<strong>{$gift['sender_name']}</strong> sent ";
                if ($gift['gift_type'] == 'money') {
                    echo number_format($gift['gift_value']) . " " . constant('primary_currency');
                } elseif ($gift['gift_type'] == 'item') {
                    echo "{$gift['quantity']}x {$gift['itmname']}";
                } else {
                    echo "a message";
                }
                echo "</div>";
                echo "<small class='text-muted'>" . dateTimeParse($gift['sent_date']) . "</small>";
                echo "</div>";
            }
        } else {
            echo "<p class='text-muted'>No gifts exchanged yet.</p>";
        }
        
        echo "</div>";
        echo "</div>";
        echo "</div>";
        echo "</div>";
        
    } else {
        // Show proposal interface for single users
        echo "<div class='row'>";
        echo "<div class='col-md-8'>";
        
        // Pending proposals
        $proposals = $marriageSystem->getPendingProposals();
        if (!empty($proposals)) {
            echo "<div class='card mb-4'>";
            echo "<div class='card-header bg-warning text-dark'>";
            echo "<h4><i class='fas fa-heart'></i> Marriage Proposals</h4>";
            echo "</div>";
            echo "<div class='card-body'>";
            
            foreach ($proposals as $proposal) {
                echo "<div class='card mb-3'>";
                echo "<div class='card-body'>";
                echo "<div class='d-flex align-items-center mb-3'>";
                if ($proposal['proposer_pic']) {
                    echo "<img src='{$proposal['proposer_pic']}' class='rounded-circle me-3' width='50' height='50'>";
                } else {
                    echo "<div class='bg-secondary rounded-circle me-3 d-flex align-items-center justify-content-center' style='width: 50px; height: 50px;'>";
                    echo "<i class='fas fa-user text-white'></i>";
                    echo "</div>";
                }
                echo "<div class='flex-grow-1'>";
                echo "<h5><a href='profile.php?user={$proposal['proposer_id']}'>{$proposal['proposer_name']}</a></h5>";
                echo "<p class='text-muted mb-0'>Level {$proposal['proposer_level']} • Proposed " . dateTimeParse($proposal['proposal_date']) . "</p>";
                echo "</div>";
                echo "</div>";
                
                if ($proposal['proposal_message']) {
                    echo "<div class='alert alert-light'>";
                    echo "<strong>Message:</strong> " . htmlspecialchars($proposal['proposal_message']);
                    echo "</div>";
                }
                
                echo "<div class='d-flex gap-2'>";
                echo "<a href='?action=respond&id={$proposal['proposal_id']}&response=accept' class='btn btn-success'><i class='fas fa-heart'></i> Accept</a>";
                echo "<a href='?action=respond&id={$proposal['proposal_id']}&response=reject' class='btn btn-danger'><i class='fas fa-times'></i> Decline</a>";
                echo "</div>";
                echo "</div>";
                echo "</div>";
            }
            
            echo "</div>";
            echo "</div>";
        }
        
        // Propose to someone
        echo "<div class='card'>";
        echo "<div class='card-header bg-primary text-white'>";
        echo "<h4><i class='fas fa-heart'></i> Send Marriage Proposal</h4>";
        echo "</div>";
        echo "<div class='card-body'>";
        echo "<form method='post' action='?action=propose'>";
        echo "<input type='hidden' name='verf' value='" . getCodeCSRF('marriage_propose') . "'>";
        echo "<div class='mb-3'>";
        echo "<label for='proposed_to' class='form-label'>Player ID or Username</label>";
        echo "<input type='text' class='form-control' id='proposed_to' name='proposed_to' required>";
        echo "<div class='form-text'>Enter the player ID or username of who you want to propose to</div>";
        echo "</div>";
        echo "<div class='mb-3'>";
        echo "<label for='message' class='form-label'>Proposal Message (Optional)</label>";
        echo "<textarea class='form-control' id='message' name='message' rows='3' placeholder='Express your feelings...'></textarea>";
        echo "</div>";
        echo "<button type='submit' class='btn btn-primary'><i class='fas fa-heart'></i> Send Proposal</button>";
        echo "</form>";
        echo "</div>";
        echo "</div>";
        
        echo "</div>";
        
        echo "<div class='col-md-4'>";
        echo "<div class='card'>";
        echo "<div class='card-header bg-info text-white'>";
        echo "<h4><i class='fas fa-info-circle'></i> Marriage Benefits</h4>";
        echo "</div>";
        echo "<div class='card-body'>";
        echo "<ul class='list-unstyled'>";
        echo "<li class='mb-2'><i class='fas fa-gift text-primary'></i> Send gifts to your spouse</li>";
        echo "<li class='mb-2'><i class='fas fa-heart text-danger'></i> Special relationship status</li>";
        echo "<li class='mb-2'><i class='fas fa-users text-success'></i> Shared profile connection</li>";
        echo "<li class='mb-2'><i class='fas fa-envelope text-info'></i> Enhanced messaging</li>";
        echo "<li class='mb-2'><i class='fas fa-calendar text-warning'></i> Anniversary celebrations</li>";
        echo "</ul>";
        echo "</div>";
        echo "</div>";
        echo "</div>";
        
        echo "</div>";
    }
    
    echo "</div>";
}

function propose() {
    global $marriageSystem, $h;
    
    if (!checkCSRF('marriage_propose', $_POST['verf'])) {
        alert('danger', "Security Error!", "Session expired. Please try again.", true, 'marriage.php');
        die($h->endpage());
    }
    
    $proposed_to = trim($_POST['proposed_to']);
    $message = trim($_POST['message']);
    
    // Try to find user by username or ID
    if (is_numeric($proposed_to)) {
        $target_id = (int)$proposed_to;
    } else {
        global $db;
        $proposed_to = $db->escape($proposed_to);
        $user = $db->fetch_row($db->query("SELECT userid FROM users WHERE username = '{$proposed_to}'"));
        $target_id = $user ? $user['userid'] : 0;
    }
    
    if (!$target_id) {
        alert('danger', "Error!", "User not found.", true, 'marriage.php');
        die($h->endpage());
    }
    
    $result = $marriageSystem->sendProposal($target_id, $message);
    
    $type = $result['success'] ? 'success' : 'danger';
    $title = $result['success'] ? 'Proposal Sent!' : 'Error!';
    
    alert($type, $title, $result['message'], true, 'marriage.php');
    die($h->endpage());
}

function respond() {
    global $marriageSystem, $h;
    
    $proposal_id = (int)$_GET['id'];
    $response = $_GET['response'];
    
    if (!in_array($response, ['accept', 'reject'])) {
        alert('danger', "Error!", "Invalid response.", true, 'marriage.php');
        die($h->endpage());
    }
    
    $result = $marriageSystem->respondToProposal($proposal_id, $response);
    
    $type = $result['success'] ? 'success' : 'danger';
    $title = $result['success'] ? ($response === 'accept' ? 'Congratulations!' : 'Response Sent') : 'Error!';
    
    alert($type, $title, $result['message'], true, 'marriage.php');
    die($h->endpage());
}

function divorce() {
    global $marriageSystem, $h;
    
    $result = $marriageSystem->requestDivorce('Mutual agreement');
    
    $type = $result['success'] ? 'success' : 'danger';
    $title = $result['success'] ? 'Divorced' : 'Error!';
    
    alert($type, $title, $result['message'], true, 'marriage.php');
    die($h->endpage());
}

function sendGift() {
    global $marriageSystem, $ir, $h;
    
    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        if (!checkCSRF('marriage_gift', $_POST['verf'])) {
            alert('danger', "Security Error!", "Session expired. Please try again.", true, 'marriage.php');
            die($h->endpage());
        }
        
        $gift_type = $_POST['gift_type'];
        $amount = (int)$_POST['amount'];
        $message = trim($_POST['message']);
        
        if ($gift_type === 'money') {
            if ($amount > $ir['primary_currency']) {
                alert('danger', "Error!", "You don't have enough money.", true, 'marriage.php?action=send_gift');
                die($h->endpage());
            }
            $result = $marriageSystem->sendGiftToSpouse('money', $amount, 0, 1, $message);
        }
        
        $type = $result['success'] ? 'success' : 'danger';
        $title = $result['success'] ? 'Gift Sent!' : 'Error!';
        
        alert($type, $title, $result['message'], true, 'marriage.php');
        die($h->endpage());
    }
    
    // Show gift form
    echo "<div class='container-fluid'>";
    echo "<div class='row justify-content-center'>";
    echo "<div class='col-md-6'>";
    echo "<div class='card'>";
    echo "<div class='card-header bg-primary text-white'>";
    echo "<h4><i class='fas fa-gift'></i> Send Gift to Spouse</h4>";
    echo "</div>";
    echo "<div class='card-body'>";
    echo "<form method='post'>";
    echo "<input type='hidden' name='verf' value='" . getCodeCSRF('marriage_gift') . "'>";
    echo "<div class='mb-3'>";
    echo "<label class='form-label'>Gift Type</label>";
    echo "<select class='form-control' name='gift_type' required>";
    echo "<option value='money'>Money</option>";
    echo "</select>";
    echo "</div>";
    echo "<div class='mb-3'>";
    echo "<label class='form-label'>Amount</label>";
    echo "<input type='number' class='form-control' name='amount' min='1' max='{$ir['primary_currency']}' required>";
    echo "<div class='form-text'>You have " . number_format($ir['primary_currency']) . " " . constant('primary_currency') . "</div>";
    echo "</div>";
    echo "<div class='mb-3'>";
    echo "<label class='form-label'>Message (Optional)</label>";
    echo "<textarea class='form-control' name='message' rows='3'></textarea>";
    echo "</div>";
    echo "<button type='submit' class='btn btn-primary'><i class='fas fa-gift'></i> Send Gift</button>";
    echo "<a href='marriage.php' class='btn btn-secondary ms-2'>Cancel</a>";
    echo "</form>";
    echo "</div>";
    echo "</div>";
    echo "</div>";
    echo "</div>";
    echo "</div>";
}

$h->endpage();
?>