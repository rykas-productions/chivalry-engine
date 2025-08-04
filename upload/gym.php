<?php
/*
	File:		gym_redesigned.php
	Created: 	Complete gym redesign with better UI
	Info: 		Modern training interface with improved layout
*/
$macropage = ('gym.php');
require("globals.php");
require_once("includes/vip-benefits.php");

// Initialize VIP benefits
$vipBenefits = getVIPBenefits($db, $userid);

// Check restrictions
if ($api->user->inInfirmary($ir['userid'])) {
    alert("danger", "Unconscious!", "You cannot train while you're in the infirmary.", true, 'index.php');
    die($h->endpage());
}

if ($api->user->inDungeon($ir['userid'])) {
    alert("danger", "Locked Up!", "You cannot train while you're in the dungeon.", true, 'index.php');
    die($h->endpage());
}

$statnames = array("Strength" => "strength", "Agility" => "agility", "Guard" => "guard", "Labor" => "labor");

// Process training if submitted
if (isset($_POST["stat"]) && isset($_POST["amnt"])) {
    $amnt = filter_input(INPUT_POST, 'amnt', FILTER_SANITIZE_NUMBER_INT) ?: 0;
    
    if (!isset($statnames[$_POST['stat']])) {
        alert("danger", "Error!", "Invalid stat selected.", true, 'gym.php');
        die($h->endpage());
    }
    
    if (!isset($_POST['verf']) || !checkCSRF('gym_train', stripslashes($_POST['verf']))) {
        alert('danger', "Security Error!", "Session expired. Please try again.", true, 'gym.php');
        die($h->endpage());
    }
    
    $stat = $statnames[$_POST['stat']];
    
    if ($amnt > $ir['energy']) {
        alert("danger", "Not Enough Energy!", "You need more energy to train that much.", false);
    } else {
        $gain = $api->user->train($userid, $_POST['stat'], $amnt);
        
        // Apply VIP training bonus
        $originalGain = $gain;
        $gain = $vipBenefits->applyTrainingBonus($gain);
        $bonus = $gain - $originalGain;
        
        $NewStatAmount = $ir[$stat] + $gain;
        $EnergyLeft = $ir['energy'] - $amnt;
        
        $db->query("UPDATE users SET energy = energy - {$amnt} WHERE userid = {$userid}");
        $ir['energy'] = $EnergyLeft;
        $ir[$stat] = $NewStatAmount;
        
        // Log VIP benefit usage if bonus was applied
        if ($bonus > 0) {
            $vipBenefits->logBenefitUsage('training_bonus', $bonus);
        }
        
        $message = "You gained <strong>{$gain}</strong> " . constant("stat_" . $stat) . "! Energy remaining: {$EnergyLeft}";
        if ($bonus > 0) {
            $message .= " <span class='text-warning'><i class='fas fa-crown'></i> VIP Bonus: +{$bonus}</span>";
        }
        
        alert('success', "Training Complete!", $message, false);
    }
}

// Calculate percentages and stats
$energyPercent = round($ir['energy'] / $ir['maxenergy'] * 100);
$hpPercent = round($ir['hp'] / $ir['maxhp'] * 100);
$willPercent = round($ir['will'] / $ir['maxwill'] * 100);
$bravePercent = round($ir['brave'] / $ir['maxbrave'] * 100);

// Get stat rankings
$strengthRank = getRank($ir['strength'], 'strength');
$agilityRank = getRank($ir['agility'], 'agility');
$guardRank = getRank($ir['guard'], 'guard');
$laborRank = getRank($ir['labor'], 'labor');
?>

<style>
/* Custom Gym Styles */
.gym-hero {
    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
    border-radius: 15px;
    padding: 40px;
    color: white;
    margin-bottom: 30px;
    box-shadow: 0 10px 30px rgba(0, 0, 0, 0.3);
}

.gym-hero h1 {
    font-size: 2.5rem;
    font-weight: 700;
    margin-bottom: 10px;
}

.gym-hero p {
    font-size: 1.2rem;
    opacity: 0.95;
}

.resource-grid {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
    gap: 20px;
    margin-bottom: 30px;
}

.resource-card {
    background: var(--bg-card);
    border: 1px solid var(--border-color);
    border-radius: 12px;
    padding: 20px;
    box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
    transition: transform 0.3s ease, box-shadow 0.3s ease;
}

.resource-card:hover {
    transform: translateY(-5px);
    box-shadow: 0 8px 20px rgba(0, 0, 0, 0.2);
}

.resource-header {
    display: flex;
    justify-content: space-between;
    align-items: center;
    margin-bottom: 15px;
}

.resource-icon {
    width: 40px;
    height: 40px;
    border-radius: 10px;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 20px;
}

.resource-value {
    font-size: 1.5rem;
    font-weight: 700;
    color: var(--text-primary);
}

.resource-label {
    font-size: 0.875rem;
    color: var(--text-muted);
    text-transform: uppercase;
    letter-spacing: 0.5px;
}

.modern-progress {
    height: 8px;
    background: rgba(0, 0, 0, 0.1);
    border-radius: 10px;
    overflow: hidden;
    margin-top: 10px;
}

.modern-progress-bar {
    height: 100%;
    border-radius: 10px;
    transition: width 0.6s ease;
    position: relative;
}

.modern-progress-bar::after {
    content: '';
    position: absolute;
    top: 0;
    left: 0;
    right: 0;
    bottom: 0;
    background: linear-gradient(90deg, transparent, rgba(255, 255, 255, 0.3), transparent);
    animation: shimmer 2s infinite;
}

@keyframes shimmer {
    0% { transform: translateX(-100%); }
    100% { transform: translateX(100%); }
}

.stats-showcase {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(280px, 1fr));
    gap: 25px;
    margin-bottom: 30px;
}

.stat-showcase-card {
    background: var(--bg-card);
    border: 2px solid var(--border-color);
    border-radius: 15px;
    padding: 25px;
    position: relative;
    overflow: hidden;
    transition: all 0.3s ease;
}

.stat-showcase-card:hover {
    border-color: var(--link-color);
    transform: scale(1.02);
}

.stat-showcase-card::before {
    content: '';
    position: absolute;
    top: 0;
    left: 0;
    right: 0;
    height: 4px;
}

.stat-showcase-card.strength::before {
    background: linear-gradient(90deg, #dc3545, #ff6b6b);
}

.stat-showcase-card.agility::before {
    background: linear-gradient(90deg, #28a745, #51cf66);
}

.stat-showcase-card.guard::before {
    background: linear-gradient(90deg, #007bff, #339af0);
}

.stat-showcase-card.labor::before {
    background: linear-gradient(90deg, #ffc107, #ffd43b);
}

.stat-header {
    display: flex;
    justify-content: space-between;
    align-items: flex-start;
    margin-bottom: 20px;
}

.stat-info h3 {
    margin: 0;
    font-size: 1.25rem;
    font-weight: 600;
    color: var(--text-primary);
}

.stat-value {
    font-size: 2rem;
    font-weight: 700;
    margin: 10px 0;
}

.stat-rank {
    display: inline-block;
    background: rgba(94, 114, 228, 0.1);
    color: var(--link-color);
    padding: 4px 12px;
    border-radius: 20px;
    font-size: 0.875rem;
    font-weight: 600;
}

.stat-icon-large {
    width: 60px;
    height: 60px;
    border-radius: 15px;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 28px;
    color: white;
}

.training-section {
    background: var(--bg-card);
    border: 1px solid var(--border-color);
    border-radius: 15px;
    padding: 30px;
    box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
}

.training-section h2 {
    margin-bottom: 25px;
    font-size: 1.75rem;
    font-weight: 600;
    color: var(--text-primary);
}

.quick-actions {
    display: grid;
    grid-template-columns: repeat(auto-fill, minmax(120px, 1fr));
    gap: 10px;
    margin-bottom: 20px;
}

.quick-action-btn {
    padding: 12px;
    background: var(--bg-secondary);
    border: 2px solid var(--border-color);
    border-radius: 10px;
    color: var(--text-primary);
    font-weight: 600;
    cursor: pointer;
    transition: all 0.3s ease;
}

.quick-action-btn:hover {
    background: var(--link-color);
    color: white;
    border-color: var(--link-color);
    transform: translateY(-2px);
}

.quick-action-btn.selected {
    background: var(--link-color);
    color: white;
    border-color: var(--link-color);
}

.form-modern {
    display: grid;
    gap: 20px;
}

.form-group-modern {
    display: grid;
    gap: 8px;
}

.form-group-modern label {
    font-weight: 600;
    color: var(--text-primary);
}

.form-control-modern {
    padding: 12px 16px;
    background: var(--input-bg);
    border: 2px solid var(--input-border);
    border-radius: 10px;
    color: var(--input-text);
    font-size: 16px;
    transition: all 0.3s ease;
}

.form-control-modern:focus {
    outline: none;
    border-color: var(--input-focus-border);
    box-shadow: 0 0 0 4px rgba(94, 114, 228, 0.1);
}

.btn-train {
    padding: 15px 30px;
    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
    color: white;
    border: none;
    border-radius: 10px;
    font-size: 1.125rem;
    font-weight: 600;
    cursor: pointer;
    transition: all 0.3s ease;
    text-transform: uppercase;
    letter-spacing: 1px;
}

.btn-train:hover {
    transform: translateY(-2px);
    box-shadow: 0 8px 20px rgba(102, 126, 234, 0.4);
}

.info-cards {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
    gap: 20px;
    margin-top: 30px;
}

.info-card {
    background: var(--bg-secondary);
    border-left: 4px solid var(--link-color);
    border-radius: 10px;
    padding: 20px;
}

.info-card h4 {
    margin-bottom: 10px;
    color: var(--text-primary);
}

.info-card p {
    margin: 0;
    color: var(--text-secondary);
    line-height: 1.6;
}
</style>

<div class="gym-container">
    <!-- Hero Section -->
    <div class="gym-hero">
        <h1><i class="fas fa-dumbbell"></i> Training Facility</h1>
        <p>Push your limits and become stronger!</p>
    </div>

    <!-- Resource Overview -->
    <div class="resource-grid">
        <div class="resource-card">
            <div class="resource-header">
                <div>
                    <div class="resource-label">Energy Available</div>
                    <div class="resource-value"><?php echo number_format($ir['energy']); ?>/<?php echo number_format($ir['maxenergy']); ?></div>
                </div>
                <div class="resource-icon" style="background: linear-gradient(135deg, #ffc107, #ff9800); color: white;">
                    <i class="fas fa-bolt"></i>
                </div>
            </div>
            <div class="modern-progress">
                <div class="modern-progress-bar" style="width: <?php echo $energyPercent; ?>%; background: linear-gradient(90deg, #ffc107, #ff9800);"></div>
            </div>
        </div>

        <div class="resource-card">
            <div class="resource-header">
                <div>
                    <div class="resource-label">Health Points</div>
                    <div class="resource-value"><?php echo number_format($ir['hp']); ?>/<?php echo number_format($ir['maxhp']); ?></div>
                </div>
                <div class="resource-icon" style="background: linear-gradient(135deg, #dc3545, #ff6b6b); color: white;">
                    <i class="fas fa-heart"></i>
                </div>
            </div>
            <div class="modern-progress">
                <div class="modern-progress-bar" style="width: <?php echo $hpPercent; ?>%; background: linear-gradient(90deg, #dc3545, #ff6b6b);"></div>
            </div>
        </div>

        <div class="resource-card">
            <div class="resource-header">
                <div>
                    <div class="resource-label">Willpower</div>
                    <div class="resource-value"><?php echo number_format($ir['will']); ?>/<?php echo number_format($ir['maxwill']); ?></div>
                </div>
                <div class="resource-icon" style="background: linear-gradient(135deg, #6f42c1, #a855f7); color: white;">
                    <i class="fas fa-brain"></i>
                </div>
            </div>
            <div class="modern-progress">
                <div class="modern-progress-bar" style="width: <?php echo $willPercent; ?>%; background: linear-gradient(90deg, #6f42c1, #a855f7);"></div>
            </div>
        </div>

        <div class="resource-card">
            <div class="resource-header">
                <div>
                    <div class="resource-label">Bravery</div>
                    <div class="resource-value"><?php echo number_format($ir['brave']); ?>/<?php echo number_format($ir['maxbrave']); ?></div>
                </div>
                <div class="resource-icon" style="background: linear-gradient(135deg, #20c997, #51cf66); color: white;">
                    <i class="fas fa-shield-alt"></i>
                </div>
            </div>
            <div class="modern-progress">
                <div class="modern-progress-bar" style="width: <?php echo $bravePercent; ?>%; background: linear-gradient(90deg, #20c997, #51cf66);"></div>
            </div>
        </div>
    </div>

    <!-- Stats Showcase -->
    <div class="stats-showcase">
        <div class="stat-showcase-card strength">
            <div class="stat-header">
                <div class="stat-info">
                    <h3><?php echo constant("stat_strength"); ?></h3>
                    <div class="stat-value"><?php echo number_format($ir['strength']); ?></div>
                    <span class="stat-rank">Rank #<?php echo $strengthRank; ?></span>
                </div>
                <div class="stat-icon-large" style="background: linear-gradient(135deg, #dc3545, #ff6b6b);">
                    <i class="fas fa-fist-raised"></i>
                </div>
            </div>
            <div class="modern-progress">
                <div class="modern-progress-bar" style="width: <?php echo min(100, $ir['strength'] / 1000 * 100); ?>%; background: linear-gradient(90deg, #dc3545, #ff6b6b);"></div>
            </div>
        </div>

        <div class="stat-showcase-card agility">
            <div class="stat-header">
                <div class="stat-info">
                    <h3><?php echo constant("stat_agility"); ?></h3>
                    <div class="stat-value"><?php echo number_format($ir['agility']); ?></div>
                    <span class="stat-rank">Rank #<?php echo $agilityRank; ?></span>
                </div>
                <div class="stat-icon-large" style="background: linear-gradient(135deg, #28a745, #51cf66);">
                    <i class="fas fa-running"></i>
                </div>
            </div>
            <div class="modern-progress">
                <div class="modern-progress-bar" style="width: <?php echo min(100, $ir['agility'] / 1000 * 100); ?>%; background: linear-gradient(90deg, #28a745, #51cf66);"></div>
            </div>
        </div>

        <div class="stat-showcase-card guard">
            <div class="stat-header">
                <div class="stat-info">
                    <h3><?php echo constant("stat_guard"); ?></h3>
                    <div class="stat-value"><?php echo number_format($ir['guard']); ?></div>
                    <span class="stat-rank">Rank #<?php echo $guardRank; ?></span>
                </div>
                <div class="stat-icon-large" style="background: linear-gradient(135deg, #007bff, #339af0);">
                    <i class="fas fa-shield-alt"></i>
                </div>
            </div>
            <div class="modern-progress">
                <div class="modern-progress-bar" style="width: <?php echo min(100, $ir['guard'] / 1000 * 100); ?>%; background: linear-gradient(90deg, #007bff, #339af0);"></div>
            </div>
        </div>

        <div class="stat-showcase-card labor">
            <div class="stat-header">
                <div class="stat-info">
                    <h3><?php echo constant("stat_labor"); ?></h3>
                    <div class="stat-value"><?php echo number_format($ir['labor']); ?></div>
                    <span class="stat-rank">Rank #<?php echo $laborRank; ?></span>
                </div>
                <div class="stat-icon-large" style="background: linear-gradient(135deg, #ffc107, #ffd43b);">
                    <i class="fas fa-hammer"></i>
                </div>
            </div>
            <div class="modern-progress">
                <div class="modern-progress-bar" style="width: <?php echo min(100, $ir['labor'] / 1000 * 100); ?>%; background: linear-gradient(90deg, #ffc107, #ffd43b);"></div>
            </div>
        </div>
    </div>

    <!-- Training Section -->
    <div class="training-section">
        <h2><i class="fas fa-play-circle"></i> Start Training Session</h2>
        
        <?php if ($vipBenefits->isVIP()): ?>
        <div class="alert alert-warning mb-3">
            <i class="fas fa-crown"></i> <strong>VIP Training Bonus:</strong> 
            You receive 25% bonus to all training gains! <?php echo $vipBenefits->getVIPBadge(); ?>
        </div>
        <?php endif; ?>
        
        <?php if ($ir['energy'] < 1): ?>
        <div class="alert alert-warning">
            <i class="fas fa-exclamation-triangle"></i> You don't have enough energy to train. 
            <a href="temple.php?action=energy" class="alert-link">Refill your energy</a> or wait for it to regenerate.
        </div>
        <?php else: ?>
        
        <form method="post" action="gym.php" class="form-modern">
            <input type="hidden" name="verf" value="<?php echo getCodeCSRF('gym_train'); ?>">
            
            <div class="form-group-modern">
                <label for="stat">Select Training Focus</label>
                <select name="stat" id="stat" class="form-control-modern">
                    <option value="Strength"><?php echo constant("stat_strength"); ?> - Increases melee damage</option>
                    <option value="Agility"><?php echo constant("stat_agility"); ?> - Improves dodge chance</option>
                    <option value="Guard"><?php echo constant("stat_guard"); ?> - Reduces damage taken</option>
                    <option value="Labor"><?php echo constant("stat_labor"); ?> - Boosts job income</option>
                </select>
            </div>
            
            <div class="form-group-modern">
                <label for="amnt">Energy Investment (Available: <?php echo $ir['energy']; ?>)</label>
                <input type="number" name="amnt" id="amnt" class="form-control-modern" 
                       min="1" max="<?php echo $ir['energy']; ?>" 
                       value="<?php echo min(10, $ir['energy']); ?>" required>
            </div>
            
            <div class="quick-actions">
                <button type="button" class="quick-action-btn" data-amount="1">1</button>
                <button type="button" class="quick-action-btn" data-amount="5">5</button>
                <button type="button" class="quick-action-btn" data-amount="10">10</button>
                <button type="button" class="quick-action-btn" data-amount="25">25</button>
                <button type="button" class="quick-action-btn" data-amount="50">50</button>
                <button type="button" class="quick-action-btn" data-amount="100">100</button>
                <button type="button" class="quick-action-btn" data-amount="<?php echo floor($ir['energy'] / 2); ?>">Half</button>
                <button type="button" class="quick-action-btn" data-amount="<?php echo $ir['energy']; ?>">Max</button>
            </div>
            
            <button type="submit" class="btn-train">
                <i class="fas fa-dumbbell"></i> Begin Training
            </button>
        </form>
        <?php endif; ?>
    </div>

    <!-- Info Cards -->
    <div class="info-cards">
        <div class="info-card">
            <h4><i class="fas fa-lightbulb text-warning"></i> Training Tips</h4>
            <p>Higher energy investments yield better training results. Balance your stats for optimal combat performance.</p>
        </div>
        
        <div class="info-card">
            <h4><i class="fas fa-clock text-info"></i> Energy Recovery</h4>
            <p>Energy regenerates over time. You can also refill it instantly at the temple for a small fee.</p>
        </div>
        
        <div class="info-card">
            <h4><i class="fas fa-trophy text-success"></i> Stat Rankings</h4>
            <p>Your rank shows how you compare to other players. Train regularly to climb the leaderboards!</p>
        </div>
    </div>
</div>

<script>
// Quick action buttons
document.querySelectorAll('.quick-action-btn').forEach(btn => {
    btn.addEventListener('click', function() {
        // Remove selected class from all buttons
        document.querySelectorAll('.quick-action-btn').forEach(b => b.classList.remove('selected'));
        // Add selected class to clicked button
        this.classList.add('selected');
        // Set the amount
        document.getElementById('amnt').value = this.dataset.amount;
    });
});

// Animate progress bars on load
window.addEventListener('load', function() {
    document.querySelectorAll('.modern-progress-bar').forEach(bar => {
        const width = bar.style.width;
        bar.style.width = '0%';
        setTimeout(() => {
            bar.style.width = width;
        }, 100);
    });
});
</script>

<?php
$h->endpage();
?>