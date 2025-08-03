<?php
/*
    File: marriage-system.php
    Created: Marriage system functionality
    Info: Handles all marriage-related operations
*/

class MarriageSystem {
    private $db;
    private $userid;
    
    public function __construct($database, $user_id) {
        $this->db = $database;
        $this->userid = $user_id;
    }
    
    /**
     * Check if user is married
     */
    public function isMarried($userid = null) {
        $userid = $userid ?: $this->userid;
        $result = $this->db->fetch_row($this->db->query("SELECT married_to FROM users WHERE userid = {$userid}"));
        return $result && $result['married_to'] > 0;
    }
    
    /**
     * Get spouse information
     */
    public function getSpouse($userid = null) {
        $userid = $userid ?: $this->userid;
        $user = $this->db->fetch_row($this->db->query("SELECT married_to, marriage_date FROM users WHERE userid = {$userid}"));
        
        if (!$user || $user['married_to'] == 0) {
            return null;
        }
        
        $spouse = $this->db->fetch_row($this->db->query("
            SELECT userid, username, level, laston, display_pic 
            FROM users 
            WHERE userid = {$user['married_to']}
        "));
        
        if ($spouse) {
            $spouse['marriage_date'] = $user['marriage_date'];
        }
        
        return $spouse;
    }
    
    /**
     * Send marriage proposal
     */
    public function sendProposal($proposed_to_id, $message = '') {
        // Validation checks
        if ($proposed_to_id == $this->userid) {
            return ['success' => false, 'message' => 'You cannot propose to yourself!'];
        }
        
        if ($this->isMarried()) {
            return ['success' => false, 'message' => 'You are already married!'];
        }
        
        if ($this->isMarried($proposed_to_id)) {
            return ['success' => false, 'message' => 'That person is already married!'];
        }
        
        // Check if user exists
        $target = $this->db->fetch_row($this->db->query("SELECT userid, username FROM users WHERE userid = {$proposed_to_id}"));
        if (!$target) {
            return ['success' => false, 'message' => 'User not found!'];
        }
        
        // Check for existing pending proposals
        $existing = $this->db->query("
            SELECT proposal_id FROM marriage_proposals 
            WHERE proposer_id = {$this->userid} AND proposed_to_id = {$proposed_to_id} 
            AND status = 'pending' AND expires_at > " . time()
        );
        
        if ($this->db->num_rows($existing)) {
            return ['success' => false, 'message' => 'You already have a pending proposal to this person!'];
        }
        
        // Create proposal (expires in 7 days)
        $expires_at = time() + (7 * 24 * 60 * 60);
        $message = $this->db->escape($message);
        
        $this->db->query("
            INSERT INTO marriage_proposals 
            (proposer_id, proposed_to_id, proposal_message, proposal_date, expires_at) 
            VALUES ({$this->userid}, {$proposed_to_id}, '{$message}', " . time() . ", {$expires_at})
        ");
        
        // Send notification
        $this->sendNotification($proposed_to_id, "You have received a marriage proposal!", 'fas fa-heart', 'info');
        
        return ['success' => true, 'message' => "Marriage proposal sent to {$target['username']}!"];
    }
    
    /**
     * Get pending proposals for user
     */
    public function getPendingProposals($userid = null) {
        $userid = $userid ?: $this->userid;
        
        $proposals = [];
        $query = $this->db->query("
            SELECT p.*, u.username as proposer_name, u.level as proposer_level, u.display_pic as proposer_pic
            FROM marriage_proposals p
            INNER JOIN users u ON p.proposer_id = u.userid
            WHERE p.proposed_to_id = {$userid} 
            AND p.status = 'pending' 
            AND p.expires_at > " . time() . "
            ORDER BY p.proposal_date DESC
        ");
        
        while ($row = $this->db->fetch_row($query)) {
            $proposals[] = $row;
        }
        
        return $proposals;
    }
    
    /**
     * Respond to marriage proposal
     */
    public function respondToProposal($proposal_id, $response, $response_message = '') {
        // Get proposal details
        $proposal = $this->db->fetch_row($this->db->query("
            SELECT * FROM marriage_proposals 
            WHERE proposal_id = {$proposal_id} 
            AND proposed_to_id = {$this->userid} 
            AND status = 'pending'
            AND expires_at > " . time()
        ));
        
        if (!$proposal) {
            return ['success' => false, 'message' => 'Proposal not found or expired!'];
        }
        
        $response_message = $this->db->escape($response_message);
        $status = ($response === 'accept') ? 'accepted' : 'rejected';
        
        // Update proposal
        $this->db->query("
            UPDATE marriage_proposals 
            SET status = '{$status}', response_date = " . time() . ", response_message = '{$response_message}'
            WHERE proposal_id = {$proposal_id}
        ");
        
        if ($response === 'accept') {
            // Create marriage
            return $this->createMarriage($proposal['proposer_id'], $this->userid);
        } else {
            // Send rejection notification
            $this->sendNotification($proposal['proposer_id'], "Your marriage proposal was declined.", 'fas fa-heart-broken', 'warning');
            return ['success' => true, 'message' => 'Marriage proposal declined.'];
        }
    }
    
    /**
     * Create marriage
     */
    private function createMarriage($proposer_id, $proposed_to_id) {
        $marriage_date = time();
        
        // Create marriage record
        $this->db->query("
            INSERT INTO marriages (proposer_id, proposed_to_id, marriage_date) 
            VALUES ({$proposer_id}, {$proposed_to_id}, {$marriage_date})
        ");
        
        // Update users table
        $this->db->query("UPDATE users SET married_to = {$proposed_to_id}, marriage_date = {$marriage_date} WHERE userid = {$proposer_id}");
        $this->db->query("UPDATE users SET married_to = {$proposer_id}, marriage_date = {$marriage_date} WHERE userid = {$proposed_to_id}");
        
        // Send notifications
        $this->sendNotification($proposer_id, "Congratulations! You are now married!", 'fas fa-heart', 'success');
        $this->sendNotification($proposed_to_id, "Congratulations! You are now married!", 'fas fa-heart', 'success');
        
        // Add to logs
        $this->addLog($proposer_id, 'marriage', 'Got married');
        $this->addLog($proposed_to_id, 'marriage', 'Got married');
        
        return ['success' => true, 'message' => 'Congratulations! You are now married!'];
    }
    
    /**
     * Divorce
     */
    public function requestDivorce($reason = '') {
        if (!$this->isMarried()) {
            return ['success' => false, 'message' => 'You are not married!'];
        }
        
        $spouse = $this->getSpouse();
        $reason = $this->db->escape($reason);
        
        // Update marriage record
        $this->db->query("
            UPDATE marriages 
            SET status = 'divorced', divorce_date = " . time() . ", divorce_reason = '{$reason}'
            WHERE (proposer_id = {$this->userid} OR proposed_to_id = {$this->userid}) 
            AND status = 'active'
        ");
        
        // Update users table
        $this->db->query("UPDATE users SET married_to = 0, marriage_date = 0 WHERE userid = {$this->userid}");
        $this->db->query("UPDATE users SET married_to = 0, marriage_date = 0 WHERE userid = {$spouse['userid']}");
        
        // Send notifications
        $this->sendNotification($spouse['userid'], "You have been divorced.", 'fas fa-heart-broken', 'danger');
        
        // Add to logs
        $this->addLog($this->userid, 'marriage', 'Got divorced');
        $this->addLog($spouse['userid'], 'marriage', 'Got divorced');
        
        return ['success' => true, 'message' => 'Divorce completed.'];
    }
    
    /**
     * Send gift to spouse
     */
    public function sendGiftToSpouse($gift_type, $value, $item_id = 0, $quantity = 1, $message = '') {
        if (!$this->isMarried()) {
            return ['success' => false, 'message' => 'You are not married!'];
        }
        
        $spouse = $this->getSpouse();
        $marriage = $this->db->fetch_row($this->db->query("
            SELECT marriage_id FROM marriages 
            WHERE (proposer_id = {$this->userid} OR proposed_to_id = {$this->userid}) 
            AND status = 'active'
        "));
        
        $message = $this->db->escape($message);
        
        // Record gift
        $this->db->query("
            INSERT INTO marriage_gifts 
            (marriage_id, sender_id, receiver_id, gift_type, gift_value, item_id, quantity, message, sent_date) 
            VALUES ({$marriage['marriage_id']}, {$this->userid}, {$spouse['userid']}, '{$gift_type}', {$value}, {$item_id}, {$quantity}, '{$message}', " . time() . ")
        ");
        
        // Process gift
        if ($gift_type === 'money') {
            // Transfer money
            $this->db->query("UPDATE users SET primary_currency = primary_currency - {$value} WHERE userid = {$this->userid}");
            $this->db->query("UPDATE users SET primary_currency = primary_currency + {$value} WHERE userid = {$spouse['userid']}");
            $gift_text = number_format($value) . " " . constant('primary_currency');
        } elseif ($gift_type === 'item') {
            // Transfer item
            $this->removeItem($this->userid, $item_id, $quantity);
            $this->giveItem($spouse['userid'], $item_id, $quantity);
            $item_name = $this->getItemName($item_id);
            $gift_text = $quantity . "x " . $item_name;
        } else {
            $gift_text = "a message";
        }
        
        // Send notification
        $this->sendNotification($spouse['userid'], "Your spouse sent you {$gift_text}!", 'fas fa-gift', 'info');
        
        return ['success' => true, 'message' => "Gift sent to your spouse!"];
    }
    
    /**
     * Get marriage statistics
     */
    public function getMarriageStats($userid = null) {
        $userid = $userid ?: $this->userid;
        
        if (!$this->isMarried($userid)) {
            return null;
        }
        
        $marriage = $this->db->fetch_row($this->db->query("
            SELECT * FROM marriages 
            WHERE (proposer_id = {$userid} OR proposed_to_id = {$userid}) 
            AND status = 'active'
        "));
        
        if (!$marriage) {
            return null;
        }
        
        // Calculate days married
        $days_married = floor((time() - $marriage['marriage_date']) / 86400);
        
        // Get gifts exchanged
        $gifts_sent = $this->db->fetch_single($this->db->query("
            SELECT COUNT(*) FROM marriage_gifts 
            WHERE marriage_id = {$marriage['marriage_id']} AND sender_id = {$userid}
        "));
        
        $gifts_received = $this->db->fetch_single($this->db->query("
            SELECT COUNT(*) FROM marriage_gifts 
            WHERE marriage_id = {$marriage['marriage_id']} AND receiver_id = {$userid}
        "));
        
        return [
            'marriage_date' => $marriage['marriage_date'],
            'days_married' => $days_married,
            'gifts_sent' => $gifts_sent,
            'gifts_received' => $gifts_received,
            'marriage_id' => $marriage['marriage_id']
        ];
    }
    
    /**
     * Helper functions
     */
    private function sendNotification($userid, $text, $icon = 'fas fa-info-circle', $color = 'primary') {
        $text = $this->db->escape($text);
        $icon = $this->db->escape($icon);
        $color = $this->db->escape($color);
        
        $this->db->query("
            INSERT INTO notifications (notif_user, notif_text, notif_time, notif_status, notif_icon, notif_color) 
            VALUES ({$userid}, '{$text}', " . time() . ", 'unread', '{$icon}', '{$color}')
        ");
    }
    
    private function addLog($userid, $type, $text) {
        $type = $this->db->escape($type);
        $text = $this->db->escape($text);
        $this->db->query("
            INSERT INTO logs (log_user, log_type, log_text, log_time) 
            VALUES ({$userid}, '{$type}', '{$text}', " . time() . ")
        ");
    }
    
    private function giveItem($userid, $itemid, $quantity) {
        $check = $this->db->query("SELECT * FROM inventory WHERE inv_userid = {$userid} AND inv_itemid = {$itemid}");
        if ($this->db->num_rows($check)) {
            $this->db->query("UPDATE inventory SET inv_qty = inv_qty + {$quantity} WHERE inv_userid = {$userid} AND inv_itemid = {$itemid}");
        } else {
            $this->db->query("INSERT INTO inventory (inv_userid, inv_itemid, inv_qty) VALUES ({$userid}, {$itemid}, {$quantity})");
        }
    }
    
    private function removeItem($userid, $itemid, $quantity) {
        $this->db->query("UPDATE inventory SET inv_qty = inv_qty - {$quantity} WHERE inv_userid = {$userid} AND inv_itemid = {$itemid}");
        $this->db->query("DELETE FROM inventory WHERE inv_userid = {$userid} AND inv_itemid = {$itemid} AND inv_qty <= 0");
    }
    
    private function getItemName($itemid) {
        return $this->db->fetch_single($this->db->query("SELECT itmname FROM items WHERE itmid = {$itemid}"));
    }
}

/**
 * Global marriage system helper function
 */
function getMarriageSystem($db, $userid) {
    static $marriageInstances = [];
    
    if (!isset($marriageInstances[$userid])) {
        $marriageInstances[$userid] = new MarriageSystem($db, $userid);
    }
    
    return $marriageInstances[$userid];
}
?>