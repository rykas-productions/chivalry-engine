<?php
/*
    File: skill_tree.php
    Created: Skill Tree System
    Info: Allocate skill points and unlock abilities
*/
require_once('globals.php');

class SkillTreeSystem {
    private $db;
    private $userid;
    private $api;
    
    public function __construct($db, $userid, $api) {
        $this->db = $db;
        $this->userid = $userid;
        $this->api = $api;
    }
    
    /**
     * Get skill trees
     */
    public function getSkillTrees() {
        $trees = [];
        $query = $this->db->query("
            SELECT st.*,
                   (SELECT COUNT(*) FROM skills WHERE skill_tree = st.st_id) as total_skills,
                   (SELECT COUNT(*) FROM user_skills us 
                    INNER JOIN skills s ON us.us_skill = s.skill_id 
                    WHERE s.skill_tree = st.st_id AND us.us_user = {$this->userid} AND us.us_level > 0) as unlocked_skills
            FROM skill_trees st
            ORDER BY st.st_class
        ");
        
        while ($row = $this->db->fetch_row($query)) {
            $trees[] = $row;
        }
        
        return $trees;
    }
    
    /**
     * Get skills for a tree
     */
    public function getSkills($tree_id) {
        $skills = [];
        $query = $this->db->query("
            SELECT s.*,
                   COALESCE(us.us_level, 0) as user_level,
                   (SELECT skill_name FROM skills WHERE skill_id = s.skill_prereq) as prereq_name
            FROM skills s
            LEFT JOIN user_skills us ON s.skill_id = us.us_skill AND us.us_user = {$this->userid}
            WHERE s.skill_tree = {$tree_id}
            ORDER BY s.skill_tier, s.skill_name
        ");
        
        while ($row = $this->db->fetch_row($query)) {
            // Check if prerequisites are met
            $row['can_unlock'] = true;
            if ($row['skill_prereq']) {
                $prereq_level = $this->db->fetch_single($this->db->query("
                    SELECT us_level FROM user_skills 
                    WHERE us_user = {$this->userid} AND us_skill = {$row['skill_prereq']}
                "));
                $row['can_unlock'] = $prereq_level > 0;
            }
            
            // Calculate total effect
            $row['current_effect'] = $row['user_level'] * $row['skill_value_per_level'];
            $row['next_effect'] = ($row['user_level'] + 1) * $row['skill_value_per_level'];
            
            $skills[] = $row;
        }
        
        return $skills;
    }
    
    /**
     * Unlock or upgrade a skill
     */
    public function upgradeSkill($skill_id) {
        global $ir;
        
        // Get skill info
        $skill = $this->db->fetch_row($this->db->query("
            SELECT s.*, COALESCE(us.us_level, 0) as current_level
            FROM skills s
            LEFT JOIN user_skills us ON s.skill_id = us.us_skill AND us.us_user = {$this->userid}
            WHERE s.skill_id = {$skill_id}
        "));
        
        if (!$skill) {
            return ['success' => false, 'message' => 'Skill not found!'];
        }
        
        // Check if at max level
        if ($skill['current_level'] >= $skill['skill_max_level']) {
            return ['success' => false, 'message' => 'Skill is already at maximum level!'];
        }
        
        // Check skill points
        $cost = $skill['skill_cost_per_level'];
        if ($ir['skill_points'] < $cost) {
            return ['success' => false, 'message' => "You need {$cost} skill points!"];
        }
        
        // Check prerequisites
        if ($skill['skill_prereq']) {
            $prereq_level = $this->db->fetch_single($this->db->query("
                SELECT us_level FROM user_skills 
                WHERE us_user = {$this->userid} AND us_skill = {$skill['skill_prereq']}
            "));
            
            if (!$prereq_level || $prereq_level == 0) {
                return ['success' => false, 'message' => 'Prerequisite skill not unlocked!'];
            }
        }
        
        // Upgrade skill
        if ($skill['current_level'] == 0) {
            // Unlock new skill
            $this->db->query("
                INSERT INTO user_skills (us_user, us_skill, us_level)
                VALUES ({$this->userid}, {$skill_id}, 1)
            ");
            $message = "Unlocked {$skill['skill_name']}!";
        } else {
            // Upgrade existing skill
            $this->db->query("
                UPDATE user_skills 
                SET us_level = us_level + 1
                WHERE us_user = {$this->userid} AND us_skill = {$skill_id}
            ");
            $message = "Upgraded {$skill['skill_name']} to level " . ($skill['current_level'] + 1) . "!";
        }
        
        // Deduct skill points
        $this->db->query("
            UPDATE users SET skill_points = skill_points - {$cost} WHERE userid = {$this->userid}
        ");
        
        // Apply skill effects (simplified)
        $this->applySkillEffects($skill_id, $skill['skill_effect'], $skill['skill_value_per_level']);
        
        return ['success' => true, 'message' => $message];
    }
    
    /**
     * Apply skill effects to user
     */
    private function applySkillEffects($skill_id, $effect_type, $value) {
        switch($effect_type) {
            case 'strength_bonus':
                $this->db->query("UPDATE userstats SET strength = strength + {$value} WHERE userid = {$this->userid}");
                break;
            case 'agility_bonus':
                $this->db->query("UPDATE userstats SET agility = agility + {$value} WHERE userid = {$this->userid}");
                break;
            case 'guard_bonus':
                $this->db->query("UPDATE userstats SET guard = guard + {$value} WHERE userid = {$this->userid}");
                break;
            case 'max_hp':
                $this->db->query("UPDATE users SET maxhp = maxhp + {$value} WHERE userid = {$this->userid}");
                break;
            case 'max_energy':
                $this->db->query("UPDATE users SET maxenergy = maxenergy + {$value} WHERE userid = {$this->userid}");
                break;
        }
    }
    
    /**
     * Reset skill tree
     */
    public function resetSkills() {
        global $ir;
        
        // Check reset cost (increases each time)
        $reset_cost = 10000 * ($ir['skill_reset_count'] + 1);
        
        if ($ir['secondary_currency'] < 100) {
            return ['success' => false, 'message' => 'You need 100 gems to reset skills!'];
        }
        
        // Get total skill points spent
        $total_points = $this->db->fetch_single($this->db->query("
            SELECT SUM(us.us_level * s.skill_cost_per_level)
            FROM user_skills us
            INNER JOIN skills s ON us.us_skill = s.skill_id
            WHERE us.us_user = {$this->userid}
        "));
        
        // Reset all skills
        $this->db->query("DELETE FROM user_skills WHERE us_user = {$this->userid}");
        
        // Refund skill points
        $this->db->query("
            UPDATE users 
            SET skill_points = skill_points + {$total_points},
                skill_reset_count = skill_reset_count + 1,
                secondary_currency = secondary_currency - 100
            WHERE userid = {$this->userid}
        ");
        
        return ['success' => true, 'message' => "Skills reset! {$total_points} skill points refunded."];
    }
}

// Initialize system
$skill_system = new SkillTreeSystem($db, $userid, $api);

// Handle actions
if (isset($_POST['action'])) {
    $result = null;
    
    switch($_POST['action']) {
        case 'upgrade':
            $skill_id = abs((int)$_POST['skill_id']);
            $result = $skill_system->upgradeSkill($skill_id);
            break;
            
        case 'reset':
            $result = $skill_system->resetSkills();
            break;
    }
    
    if ($result) {
        alert($result['success'] ? 'success' : 'danger',
              $result['success'] ? 'Success!' : 'Failed!',
              $result['message'], false);
    }
}

// Get data
$skill_trees = $skill_system->getSkillTrees();
$selected_tree = isset($_GET['tree']) ? abs((int)$_GET['tree']) : 1;
$skills = $skill_system->getSkills($selected_tree);

// Add sample skills if none exist
if (empty($skills) && $selected_tree == 1) {
    // Add warrior skills
    $warrior_skills = [
        ['Blade Master', 'Increases sword damage', 1, 'damage_bonus', 5],
        ['Iron Skin', 'Increases defense', 1, 'guard_bonus', 3],
        ['Berserker Rage', 'Increases strength', 2, 'strength_bonus', 5],
        ['Shield Wall', 'Increases guard significantly', 2, 'guard_bonus', 10],
        ['Whirlwind', 'Area damage attack', 3, 'special_attack', 1]
    ];
    
    foreach ($warrior_skills as $idx => $sk) {
        $prereq = $idx > 1 ? $idx - 1 : 'NULL';
        $this->db->query("
            INSERT IGNORE INTO skills 
            (skill_tree, skill_name, skill_desc, skill_tier, skill_effect, skill_value_per_level, skill_prereq)
            VALUES (1, '{$sk[0]}', '{$sk[1]}', {$sk[2]}, '{$sk[3]}', {$sk[4]}, {$prereq})
        ");
    }
    
    $skills = $skill_system->getSkills($selected_tree);
}

?>

<div class="container-fluid">
    <!-- Header -->
    <div class="row mb-4">
        <div class="col-12">
            <div class="card bg-gradient-primary text-white">
                <div class="card-body">
                    <div class="row align-items-center">
                        <div class="col-md-6">
                            <h2 class="mb-0"><i class="fas fa-tree me-2"></i>Skill Trees</h2>
                            <p class="mb-0 mt-2">Customize your character with powerful abilities!</p>
                        </div>
                        <div class="col-md-6 text-md-end">
                            <div class="d-inline-block text-center mx-2">
                                <h4 class="mb-0"><?php echo $ir['skill_points']; ?></h4>
                                <small>Available Points</small>
                            </div>
                            <div class="d-inline-block text-center mx-2">
                                <h4 class="mb-0"><?php echo $ir['skill_reset_count']; ?></h4>
                                <small>Resets Used</small>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    
    <!-- Tree Selection -->
    <div class="card mb-4">
        <div class="card-body">
            <div class="row">
                <?php foreach ($skill_trees as $tree): 
                    $tree_color = '';
                    $tree_icon = '';
                    switch($tree['st_class']) {
                        case 'warrior':
                            $tree_color = 'danger';
                            $tree_icon = 'fa-sword';
                            break;
                        case 'mage':
                            $tree_color = 'primary';
                            $tree_icon = 'fa-hat-wizard';
                            break;
                        case 'rogue':
                            $tree_color = 'dark';
                            $tree_icon = 'fa-user-ninja';
                            break;
                        default:
                            $tree_color = 'secondary';
                            $tree_icon = 'fa-star';
                    }
                ?>
                <div class="col-md-4">
                    <a href="?tree=<?php echo $tree['st_id']; ?>" class="text-decoration-none">
                        <div class="card <?php echo $selected_tree == $tree['st_id'] ? 'border-primary border-3' : ''; ?>">
                            <div class="card-body text-center">
                                <i class="fas <?php echo $tree_icon; ?> text-<?php echo $tree_color; ?>" style="font-size: 3rem;"></i>
                                <h5 class="mt-3"><?php echo $tree['st_name']; ?></h5>
                                <p class="text-muted"><?php echo $tree['st_desc']; ?></p>
                                <div class="progress">
                                    <div class="progress-bar bg-<?php echo $tree_color; ?>" 
                                         style="width: <?php echo $tree['total_skills'] > 0 ? ($tree['unlocked_skills'] / $tree['total_skills']) * 100 : 0; ?>%">
                                        <?php echo $tree['unlocked_skills']; ?>/<?php echo $tree['total_skills']; ?> Skills
                                    </div>
                                </div>
                            </div>
                        </div>
                    </a>
                </div>
                <?php endforeach; ?>
            </div>
        </div>
    </div>
    
    <!-- Skills Grid -->
    <div class="card">
        <div class="card-header d-flex justify-content-between align-items-center">
            <h4><i class="fas fa-star"></i> Skills</h4>
            <?php if ($ir['skill_reset_count'] < 3): ?>
            <form method="POST" class="d-inline">
                <input type="hidden" name="action" value="reset">
                <?php echo getHtmlCSRF('skill_reset'); ?>
                <button type="submit" class="btn btn-warning btn-sm" 
                        onclick="return confirm('Reset all skills for 100 gems? You will get all skill points back.')">
                    <i class="fas fa-undo"></i> Reset Skills (100 gems)
                </button>
            </form>
            <?php endif; ?>
        </div>
        <div class="card-body">
            <?php if (empty($skills)): ?>
                <p class="text-muted">No skills available in this tree yet.</p>
            <?php else: ?>
                <!-- Group skills by tier -->
                <?php 
                $tiers = [];
                foreach ($skills as $skill) {
                    $tiers[$skill['skill_tier']][] = $skill;
                }
                ?>
                
                <?php foreach ($tiers as $tier => $tier_skills): ?>
                <div class="mb-4">
                    <h5 class="text-muted">Tier <?php echo $tier; ?></h5>
                    <div class="row">
                        <?php foreach ($tier_skills as $skill): 
                            $is_maxed = $skill['user_level'] >= $skill['skill_max_level'];
                            $is_unlocked = $skill['user_level'] > 0;
                            $can_afford = $ir['skill_points'] >= $skill['skill_cost_per_level'];
                        ?>
                        <div class="col-lg-4 col-md-6 mb-3">
                            <div class="card h-100 <?php echo $is_unlocked ? 'border-success' : ''; ?>">
                                <div class="card-header">
                                    <div class="d-flex justify-content-between align-items-center">
                                        <h6 class="mb-0">
                                            <i class="fas <?php echo $skill['skill_icon'] ?? 'fa-star'; ?>"></i>
                                            <?php echo $skill['skill_name']; ?>
                                        </h6>
                                        <span class="badge bg-<?php echo $skill['skill_type'] == 'passive' ? 'info' : ($skill['skill_type'] == 'active' ? 'warning' : 'danger'); ?>">
                                            <?php echo ucfirst($skill['skill_type']); ?>
                                        </span>
                                    </div>
                                </div>
                                <div class="card-body">
                                    <p class="small"><?php echo $skill['skill_desc']; ?></p>
                                    
                                    <?php if ($skill['skill_prereq'] && $skill['prereq_name']): ?>
                                    <div class="alert alert-info py-1 px-2 mb-2">
                                        <small><i class="fas fa-lock"></i> Requires: <?php echo $skill['prereq_name']; ?></small>
                                    </div>
                                    <?php endif; ?>
                                    
                                    <div class="mb-2">
                                        <div class="d-flex justify-content-between">
                                            <small>Level:</small>
                                            <small><?php echo $skill['user_level']; ?>/<?php echo $skill['skill_max_level']; ?></small>
                                        </div>
                                        <div class="progress" style="height: 10px;">
                                            <div class="progress-bar bg-success" 
                                                 style="width: <?php echo ($skill['user_level'] / $skill['skill_max_level']) * 100; ?>%"></div>
                                        </div>
                                    </div>
                                    
                                    <div class="text-muted small mb-2">
                                        <?php if ($is_unlocked): ?>
                                            Current: +<?php echo $skill['current_effect']; ?> <?php echo str_replace('_', ' ', $skill['skill_effect']); ?><br>
                                            <?php if (!$is_maxed): ?>
                                                Next: +<?php echo $skill['next_effect']; ?> <?php echo str_replace('_', ' ', $skill['skill_effect']); ?>
                                            <?php endif; ?>
                                        <?php else: ?>
                                            Effect: +<?php echo $skill['skill_value_per_level']; ?> <?php echo str_replace('_', ' ', $skill['skill_effect']); ?> per level
                                        <?php endif; ?>
                                    </div>
                                    
                                    <?php if ($is_maxed): ?>
                                        <button class="btn btn-secondary btn-sm w-100" disabled>
                                            <i class="fas fa-check"></i> Maxed
                                        </button>
                                    <?php elseif (!$skill['can_unlock']): ?>
                                        <button class="btn btn-secondary btn-sm w-100" disabled>
                                            <i class="fas fa-lock"></i> Locked
                                        </button>
                                    <?php elseif (!$can_afford): ?>
                                        <button class="btn btn-warning btn-sm w-100" disabled>
                                            Need <?php echo $skill['skill_cost_per_level']; ?> Points
                                        </button>
                                    <?php else: ?>
                                        <form method="POST">
                                            <input type="hidden" name="action" value="upgrade">
                                            <input type="hidden" name="skill_id" value="<?php echo $skill['skill_id']; ?>">
                                            <?php echo getHtmlCSRF('skill_upgrade'); ?>
                                            <button type="submit" class="btn btn-success btn-sm w-100">
                                                <i class="fas fa-plus"></i> 
                                                <?php echo $is_unlocked ? 'Upgrade' : 'Unlock'; ?> 
                                                (<?php echo $skill['skill_cost_per_level']; ?> pts)
                                            </button>
                                        </form>
                                    <?php endif; ?>
                                </div>
                            </div>
                        </div>
                        <?php endforeach; ?>
                    </div>
                </div>
                <?php endforeach; ?>
            <?php endif; ?>
        </div>
    </div>
    
    <!-- How to Get Skill Points -->
    <div class="card mt-4">
        <div class="card-body">
            <h5><i class="fas fa-info-circle"></i> How to Get Skill Points</h5>
            <ul>
                <li>Level up your character (1 point per level)</li>
                <li>Complete certain achievements</li>
                <li>Win Battle Royale events</li>
                <li>Complete difficult dungeons</li>
                <li>Purchase with gems in the shop</li>
            </ul>
        </div>
    </div>
</div>

<?php
$h->endpage();
?>