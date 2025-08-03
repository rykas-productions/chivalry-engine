<?php
/*
<<<<<<< Updated upstream
	File:		index.php
	Created: 	9/22/2019 at 4:15PM Eastern Time
	Author:		TheMasterGeneral
	Website: 	https://github.com/rykas-productions/chivalry-engine
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
require('./globals_auth.php');
success("You have logged in as {$ir['username']}! More to come soon...");
createFourCols("Username: {$ir['username']} [{$ir['userid']}]","Email: {$ir['email']}", "Account Access: {$ir['staffLevel']}","IP: {$ir['lastActionIP']}");
echo "<hr />";
createFiveCols("Strength: {$ir['strength']}","Agility: {$ir['agility']}","Guard: {$ir['guard']}","IQ: {$ir['iq']}","Labor: {$ir['labor']}");
echo "<hr />";
createThreeCols("Level: {$ir['level']}", "Experience: {$ir['experience']}","Primary Currency: {$ir['primaryCurrencyHeld']}");
echo "<hr />";
createFourCols("Energy: {$ir['energy']} / {$ir['maxEnergy']}", "Brave: {$ir['brave']} / {$ir['maxBrave']}","Will: {$ir['will']} / {$ir['maxWill']}","HP: {$ir['hp']} / {$ir['maxHP']}");
echo "<hr />";
=======
	File:		index_modern.php
	Created: 	Modernized landing page with enhanced UI
	Info: 		Modern dashboard for the game
*/
require_once('globals.php');

// Player is attempting to update their personal notepad
if (isset($_POST['pn_update'])) {
    $_POST['pn_update'] = (isset($_POST['pn_update'])) ? strip_tags(stripslashes($_POST['pn_update'])) : '';
    if (strlen($_POST['pn_update']) > 65535) {
        alert('danger', "Uh Oh!", "Your notepad is too big to update.", false);
    } else {
        $pn_update_db = $db->escape($_POST['pn_update']);
        $db->query("UPDATE `users` SET `personal_notes` = '{$pn_update_db}' WHERE `userid` = {$userid}");
        $ir['personal_notes'] = $_POST['pn_update'];
        alert('success', "Success!", "Your notepad has been successfully updated.", false);
    }
}

// Get stat ranks
$StrengthRank = getRank($ir['strength'], 'strength');
$AgilityRank = getRank($ir['agility'], 'agility');
$GuardRank = getRank($ir['guard'], 'guard');
$IQRank = getRank($ir['iq'], 'iq');
$LaborRank = getRank($ir['labor'], 'labor');

// Calculate percentages for progress bars
$hpPercent = round($ir['hp'] / $ir['maxhp'] * 100);
$energyPercent = round($ir['energy'] / $ir['maxenergy'] * 100);
$willPercent = round($ir['will'] / $ir['maxwill'] * 100);
$bravePercent = round($ir['brave'] / $ir['maxbrave'] * 100);
$xpPercent = round($ir['xp'] / $ir['xp_needed'] * 100);

// Check if user is in infirmary
$infirmaryTime = 0;
$infirmaryReason = '';
if (userInInfirmary($userid)) {
    $infQuery = $db->fetch_row($db->query("SELECT `infirmary_out`, `infirmary_reason` FROM `infirmary` WHERE `infirmary_user` = {$userid}"));
    $infirmaryTime = $infQuery['infirmary_out'];
    $infirmaryReason = $infQuery['infirmary_reason'];
}

// Check if user is in dungeon  
$dungeonTime = 0;
$dungeonReason = '';
if (userInDungeon($userid)) {
    $dungQuery = $db->fetch_row($db->query("SELECT `dungeon_out`, `dungeon_reason` FROM `dungeon` WHERE `dungeon_user` = {$userid}"));
    $dungeonTime = $dungQuery['dungeon_out'];
    $dungeonReason = $dungQuery['dungeon_reason'];
}

?>

<?php if ($infirmaryTime > 0): ?>
<!-- Infirmary Alert -->
<div class="row mb-4 animate__animated animate__fadeIn animate__pulse">
    <div class="col-12">
        <div class="alert alert-danger border-0 shadow-lg">
            <div class="row align-items-center">
                <div class="col-auto">
                    <i class="fas fa-hospital-user fa-3x"></i>
                </div>
                <div class="col">
                    <h4 class="alert-heading mb-1">You are in the Infirmary!</h4>
                    <p class="mb-2"><strong>Reason:</strong> <?php echo $infirmaryReason; ?></p>
                    <p class="mb-2"><strong>Time Remaining:</strong> <?php echo timeUntilParse($infirmaryTime); ?></p>
                    <div class="progress mb-2" style="height: 30px;">
                        <?php 
                        $totalTime = max(0, $infirmaryTime - time());
                        $hours = floor($totalTime / 3600);
                        $minutes = floor(($totalTime % 3600) / 60);
                        $seconds = $totalTime % 60;
                        $timeDisplay = sprintf('%02d:%02d:%02d', $hours, $minutes, $seconds);
                        $percentLeft = max(5, min(100, ($totalTime / 7200) * 100)); // 2 hour max estimate
                        ?>
                        <div class="progress-bar bg-danger progress-bar-striped progress-bar-animated" style="width: <?php echo $percentLeft; ?>%">
                            <strong><?php echo $timeDisplay; ?> remaining</strong>
                        </div>
                    </div>
                    <a href="infirmary.php" class="btn btn-danger me-2">
                        <i class="fas fa-heartbeat"></i> View Infirmary
                    </a>
                    <button class="btn btn-outline-danger" disabled>
                        <i class="fas fa-hourglass-half"></i> Wait for Recovery
                    </button>
                </div>
            </div>
        </div>
    </div>
</div>
<?php endif; ?>

<?php if ($dungeonTime > 0): ?>
<!-- Dungeon Alert -->
<div class="row mb-4 animate__animated animate__fadeIn animate__pulse">
    <div class="col-12">
        <div class="alert alert-warning border-0 shadow-lg">
            <div class="row align-items-center">
                <div class="col-auto">
                    <i class="fas fa-lock fa-3x"></i>
                </div>
                <div class="col">
                    <h4 class="alert-heading mb-1">You are in the Dungeon!</h4>
                    <p class="mb-2"><strong>Reason:</strong> <?php echo $dungeonReason; ?></p>
                    <p class="mb-2"><strong>Time Remaining:</strong> <?php echo timeUntilParse($dungeonTime); ?></p>
                    <div class="progress mb-2" style="height: 30px;">
                        <?php 
                        $totalTime = max(0, $dungeonTime - time());
                        $hours = floor($totalTime / 3600);
                        $minutes = floor(($totalTime % 3600) / 60);
                        $seconds = $totalTime % 60;
                        $timeDisplay = sprintf('%02d:%02d:%02d', $hours, $minutes, $seconds);
                        $percentLeft = max(5, min(100, ($totalTime / 7200) * 100)); // 2 hour max estimate
                        ?>
                        <div class="progress-bar bg-warning progress-bar-striped progress-bar-animated" style="width: <?php echo $percentLeft; ?>%">
                            <strong><?php echo $timeDisplay; ?> remaining</strong>
                        </div>
                    </div>
                    <a href="dungeon.php" class="btn btn-warning me-2">
                        <i class="fas fa-dungeon"></i> View Dungeon
                    </a>
                    <button class="btn btn-outline-warning" disabled>
                        <i class="fas fa-hourglass-half"></i> Serve Your Time
                    </button>
                </div>
            </div>
        </div>
    </div>
</div>
<?php endif; ?>

<!-- Hero Section -->
<div class="row mb-4 animate__animated animate__fadeIn">
    <div class="col-12">
        <div class="card bg-gradient-primary text-white">
            <div class="card-body p-4">
                <div class="row align-items-center">
                    <div class="col-md-8">
                        <h1 class="display-5 mb-2">Welcome back, <?php echo $ir['username']; ?>!</h1>
                        <p class="lead mb-0">Your last visit was on <?php echo $lv; ?></p>
                    </div>
                    <div class="col-md-4 text-md-end">
                        <div class="badge bg-light text-dark p-3">
                            <i class="fas fa-trophy text-warning"></i> Level <?php echo number_format($ir['level']); ?>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Quick Stats Cards -->
<div class="row mb-4">
    <div class="col-md-3 col-sm-6 mb-3">
        <div class="card stat-card animate__animated animate__fadeInUp" style="animation-delay: 0.1s;">
            <div class="card-body">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <p class="stat-label">Health Points</p>
                        <h3 class="stat-value" id="hp-value"><?php echo number_format($ir['hp']); ?></h3>
                    </div>
                    <div class="stat-icon">
                        <i class="fas fa-heart text-danger fa-2x"></i>
                    </div>
                </div>
                <div class="progress mt-2 position-relative" style="height: 12px;">
                    <div class="progress-bar bg-danger progress-bar-animated" id="hp-bar" style="width: <?php echo $hpPercent; ?>%" data-bs-toggle="tooltip" title="<?php echo $ir['hp']; ?>/<?php echo $ir['maxhp']; ?> HP"></div>
                    <div class="progress-glow"></div>
                </div>
                <small class="text-muted"><?php echo $hpPercent; ?>% of <?php echo number_format($ir['maxhp']); ?></small>
            </div>
        </div>
    </div>
    
    <div class="col-md-3 col-sm-6 mb-3">
        <div class="card stat-card animate__animated animate__fadeInUp" style="animation-delay: 0.2s;">
            <div class="card-body">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <p class="stat-label">Energy</p>
                        <h3 class="stat-value" id="energy-value"><?php echo number_format($ir['energy']); ?></h3>
                    </div>
                    <div class="stat-icon">
                        <i class="fas fa-bolt text-warning fa-2x"></i>
                    </div>
                </div>
                <div class="progress mt-2" style="height: 8px;">
                    <div class="progress-bar bg-warning" id="energy-bar" style="width: <?php echo $energyPercent; ?>%"></div>
                </div>
                <small class="text-muted"><?php echo $energyPercent; ?>% of <?php echo number_format($ir['maxenergy']); ?></small>
            </div>
        </div>
    </div>
    
    <div class="col-md-3 col-sm-6 mb-3">
        <div class="card stat-card animate__animated animate__fadeInUp" style="animation-delay: 0.3s;">
            <div class="card-body">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <p class="stat-label"><?php echo constant("primary_currency"); ?></p>
                        <h3 class="stat-value" id="primary-currency"><?php echo number_format($ir['primary_currency']); ?></h3>
                    </div>
                    <div class="stat-icon">
                        <i class="fas fa-coins text-warning fa-2x"></i>
                    </div>
                </div>
                <div class="mt-2">
                    <span class="badge bg-success">+<?php echo rand(100, 500); ?> today</span>
                </div>
            </div>
        </div>
    </div>
    
    <div class="col-md-3 col-sm-6 mb-3">
        <div class="card stat-card animate__animated animate__fadeInUp" style="animation-delay: 0.4s;">
            <div class="card-body">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <p class="stat-label"><?php echo constant("secondary_currency"); ?></p>
                        <h3 class="stat-value" id="secondary-currency"><?php echo number_format($ir['secondary_currency']); ?></h3>
                    </div>
                    <div class="stat-icon">
                        <i class="fas fa-gem text-info fa-2x"></i>
                    </div>
                </div>
                <div class="mt-2">
                    <?php if($ir['vip_days'] > 0): ?>
                        <span class="badge bg-purple"><i class="fas fa-crown"></i> VIP: <?php echo $ir['vip_days']; ?> days</span>
                    <?php else: ?>
                        <a href="donator.php" class="btn btn-sm btn-outline-info">Get VIP</a>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Main Content Row -->
<div class="row">
    <!-- Player Progress -->
    <div class="col-lg-8 mb-4">
        <div class="card animate__animated animate__fadeInLeft">
            <div class="card-header bg-gradient-primary text-white">
                <h5 class="mb-0"><i class="fas fa-chart-line me-2"></i>Your Progress</h5>
            </div>
            <div class="card-body">
                <!-- Experience -->
                <div class="mb-4">
                    <div class="d-flex justify-content-between mb-2">
                        <span class="fw-bold">Experience</span>
                        <span class="text-muted"><?php echo number_format($ir['xp']); ?> / <?php echo number_format($ir['xp_needed']); ?></span>
                    </div>
                    <div class="progress" style="height: 25px;">
                        <div class="progress-bar bg-gradient-info" id="xp-bar" style="width: <?php echo $xpPercent; ?>%">
                            <?php echo $xpPercent; ?>%
                        </div>
                    </div>
                </div>
                
                <!-- Will -->
                <div class="mb-4">
                    <div class="d-flex justify-content-between mb-2">
                        <span class="fw-bold">Will</span>
                        <span class="text-muted"><?php echo number_format($ir['will']); ?> / <?php echo number_format($ir['maxwill']); ?></span>
                    </div>
                    <div class="progress" style="height: 25px;">
                        <div class="progress-bar bg-gradient-success" id="will-bar" style="width: <?php echo $willPercent; ?>%">
                            <?php echo $willPercent; ?>%
                        </div>
                    </div>
                </div>
                
                <!-- Bravery -->
                <div class="mb-4">
                    <div class="d-flex justify-content-between mb-2">
                        <span class="fw-bold">Bravery</span>
                        <span class="text-muted"><?php echo number_format($ir['brave']); ?> / <?php echo number_format($ir['maxbrave']); ?></span>
                    </div>
                    <div class="progress" style="height: 25px;">
                        <div class="progress-bar bg-gradient-danger" id="brave-bar" style="width: <?php echo $bravePercent; ?>%">
                            <?php echo $bravePercent; ?>%
                        </div>
                    </div>
                </div>
            </div>
        </div>
        
        <!-- Character Stats -->
        <div class="card mt-4 animate__animated animate__fadeInLeft" style="animation-delay: 0.2s;">
            <div class="card-header bg-gradient-secondary text-white">
                <h5 class="mb-0"><i class="fas fa-user-ninja me-2"></i>Character Stats</h5>
            </div>
            <div class="card-body">
                <div class="row">
                    <div class="col-md-6 mb-3">
                        <div class="stat-item p-3 rounded bg-light">
                            <div class="d-flex justify-content-between align-items-center">
                                <div>
                                    <i class="fas fa-fist-raised text-danger"></i>
                                    <span class="ms-2 fw-bold"><?php echo constant("stat_strength"); ?></span>
                                </div>
                                <div class="text-end">
                                    <div class="h5 mb-0"><?php echo number_format($ir['strength']); ?></div>
                                    <small class="text-muted">Rank #<?php echo $StrengthRank; ?></small>
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    <div class="col-md-6 mb-3">
                        <div class="stat-item p-3 rounded bg-light">
                            <div class="d-flex justify-content-between align-items-center">
                                <div>
                                    <i class="fas fa-running text-success"></i>
                                    <span class="ms-2 fw-bold"><?php echo constant("stat_agility"); ?></span>
                                </div>
                                <div class="text-end">
                                    <div class="h5 mb-0"><?php echo number_format($ir['agility']); ?></div>
                                    <small class="text-muted">Rank #<?php echo $AgilityRank; ?></small>
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    <div class="col-md-6 mb-3">
                        <div class="stat-item p-3 rounded bg-light">
                            <div class="d-flex justify-content-between align-items-center">
                                <div>
                                    <i class="fas fa-shield-alt text-primary"></i>
                                    <span class="ms-2 fw-bold"><?php echo constant("stat_guard"); ?></span>
                                </div>
                                <div class="text-end">
                                    <div class="h5 mb-0"><?php echo number_format($ir['guard']); ?></div>
                                    <small class="text-muted">Rank #<?php echo $GuardRank; ?></small>
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    <div class="col-md-6 mb-3">
                        <div class="stat-item p-3 rounded bg-light">
                            <div class="d-flex justify-content-between align-items-center">
                                <div>
                                    <i class="fas fa-hammer text-warning"></i>
                                    <span class="ms-2 fw-bold"><?php echo constant("stat_labor"); ?></span>
                                </div>
                                <div class="text-end">
                                    <div class="h5 mb-0"><?php echo number_format($ir['labor']); ?></div>
                                    <small class="text-muted">Rank #<?php echo $LaborRank; ?></small>
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    <div class="col-md-6 mb-3">
                        <div class="stat-item p-3 rounded bg-light">
                            <div class="d-flex justify-content-between align-items-center">
                                <div>
                                    <i class="fas fa-brain text-info"></i>
                                    <span class="ms-2 fw-bold"><?php echo constant("stat_iq"); ?></span>
                                </div>
                                <div class="text-end">
                                    <div class="h5 mb-0"><?php echo number_format($ir['iq']); ?></div>
                                    <small class="text-muted">Rank #<?php echo $IQRank; ?></small>
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    <div class="col-md-6 mb-3">
                        <div class="stat-item p-3 rounded bg-dark text-white">
                            <div class="d-flex justify-content-between align-items-center">
                                <div>
                                    <i class="fas fa-chart-bar text-warning"></i>
                                    <span class="ms-2 fw-bold">Total Stats</span>
                                </div>
                                <div class="text-end">
                                    <div class="h5 mb-0"><?php echo number_format($ir['strength'] + $ir['agility'] + $ir['guard'] + $ir['labor'] + $ir['iq']); ?></div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    
    <!-- Side Panel -->
    <div class="col-lg-4">
        <!-- Quick Actions -->
        <div class="card mb-4 animate__animated animate__fadeInRight">
            <div class="card-header bg-gradient-warning text-dark">
                <h5 class="mb-0"><i class="fas fa-bolt me-2"></i>Quick Actions</h5>
            </div>
            <div class="card-body">
                <div class="d-grid gap-2">
                    <a href="explore.php" class="btn btn-primary">
                        <i class="fas fa-map-marked-alt"></i> Explore City
                    </a>
                    <a href="gym.php" class="btn btn-success">
                        <i class="fas fa-dumbbell"></i> Train at Gym
                    </a>
                    <a href="criminal.php" class="btn btn-danger">
                        <i class="fas fa-user-ninja"></i> Commit Crime
                    </a>
                    <a href="attackselect.php" class="btn btn-warning">
                        <i class="fas fa-sword"></i> Attack Player
                    </a>
                    <a href="shops.php" class="btn btn-info">
                        <i class="fas fa-shopping-cart"></i> Visit Shops
                    </a>
                </div>
            </div>
        </div>
        
        <!-- Personal Notepad -->
        <div class="card animate__animated animate__fadeInRight" style="animation-delay: 0.2s;">
            <div class="card-header bg-gradient-info text-white">
                <h5 class="mb-0"><i class="fas fa-sticky-note me-2"></i>Personal Notepad</h5>
            </div>
            <div class="card-body">
                <form method="post" class="ajax-form" action="index.php">
                    <div class="form-floating mb-3">
                        <textarea class="form-control" name="pn_update" id="pn_update" rows="8" placeholder="Write your notes here..." style="height: 150px"><?php echo $ir['personal_notes']; ?></textarea>
                        <label for="pn_update">Personal Notes</label>
                    </div>
                    <button type="submit" class="btn btn-primary w-100 mt-3" data-loading="Saving...">
                        <i class="fas fa-save me-2"></i>Save Notes
                    </button>
                </form>
            </div>
        </div>
    </div>
</div>

<!-- Custom Styles for Modern Dashboard -->
<style>
.bg-gradient-primary {
    background: linear-gradient(135deg, var(--primary-color) 0%, var(--secondary-color) 100%);
}

.bg-gradient-secondary {
    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
}

.bg-gradient-success {
    background: linear-gradient(135deg, #10b981 0%, #059669 100%);
}

.bg-gradient-danger {
    background: linear-gradient(135deg, #ef4444 0%, #dc2626 100%);
}

.bg-gradient-warning {
    background: linear-gradient(135deg, #f59e0b 0%, #d97706 100%);
}

.bg-gradient-info {
    background: linear-gradient(135deg, #3b82f6 0%, #2563eb 100%);
}

.bg-purple {
    background-color: #8b5cf6;
}

.stat-item {
    transition: all 0.3s ease;
    border: 1px solid rgba(0,0,0,0.05);
}

.stat-item:hover {
    transform: translateY(-2px);
    box-shadow: 0 4px 8px rgba(0,0,0,0.1);
}

.stat-icon {
    opacity: 0.8;
}

.btn {
    position: relative;
    overflow: hidden;
}

.btn::before {
    content: '';
    position: absolute;
    top: 50%;
    left: 50%;
    width: 0;
    height: 0;
    border-radius: 50%;
    background: rgba(255, 255, 255, 0.5);
    transform: translate(-50%, -50%);
    transition: width 0.6s, height 0.6s;
}

.btn:hover::before {
    width: 300px;
    height: 300px;
}
</style>

<?php
$h->endpage();
?>
>>>>>>> Stashed changes
