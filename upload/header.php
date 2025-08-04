<?php
/*
	File:		header_modern.php
	Created: 	Modern version with Bootstrap 5 and enhanced UI
	Info: 		Modernized in-game template for logged in users
	Author:		Enhanced version of original by TheMasterGeneral
*/
class headers
{
    /**
     * Check if a database table exists
     */
    private function tableExists($table) {
        global $db;
        $result = $db->query("SHOW TABLES LIKE '{$table}'");
        return $db->num_rows($result) > 0;
    }
    
    function startheaders()
    {
        global $ir, $set, $h, $db, $menuhide, $userid, $macropage, $api, $time;
        
        // Check if v3.0.0 features are installed
        $v3_installed = false;
        if ($this->tableExists('achievements') && $this->tableExists('daily_rewards') && 
            $this->tableExists('guild_territories') && $this->tableExists('battle_royale_events')) {
            $v3_installed = true;
        }
        ?>
        <!DOCTYPE html>
        <html lang="en">
        <head>
            <meta charset="utf-8">
            <meta http-equiv="X-UA-Compatible" content="IE=edge">
            <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
            <meta name="description" content="<?php echo $set['Website_Description']; ?>">
            <meta property="og:title" content="<?php echo $set['WebsiteName']; ?>"/>
            <meta property="og:description" content="<?php echo $set['Website_Description']; ?>"/>
            <meta name="theme-color" content="#6366f1" media="(prefers-color-scheme: light)">
            <meta name="theme-color" content="#1f2937" media="(prefers-color-scheme: dark)">
            <meta name="color-scheme" content="light dark">
            <meta name="author" content="<?php echo $set['WebsiteOwner']; ?>">
            <?php echo "<title>{$set['WebsiteName']}</title>"; ?>
            
            <!-- Performance and Caching Headers -->
            <meta http-equiv="Cache-Control" content="public, max-age=86400">
            <meta name="format-detection" content="telephone=no">
            <meta name="msapplication-tap-highlight" content="no">
            
            <!-- Preload critical resources -->
            <link rel="preload" href="css/master-combined.css" as="style">
            <link rel="preload" href="css/themes.css" as="style">
            <link rel="preload" href="js/theme-switcher.js" as="script">
            <link rel="preload" href="js/sidebar-state.js" as="script">
            
            <!-- Preconnect to CDNs for faster loading -->
            <link rel="preconnect" href="https://cdn.jsdelivr.net">
            <link rel="preconnect" href="https://cdnjs.cloudflare.com">
            <link rel="preconnect" href="https://fonts.googleapis.com">
            <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
            
            <!-- Critical CSS - Load immediately -->
            <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
            <link rel="stylesheet" href="css/master-combined.css">
            <link rel="stylesheet" href="css/themes.css">
            
            <!-- Non-critical CSS - Load asynchronously -->
            <link rel="preload" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" as="style" onload="this.onload=null;this.rel='stylesheet'">
            <noscript><link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css"></noscript>
            
            <link rel="preload" href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" as="style" onload="this.onload=null;this.rel='stylesheet'">
            <noscript><link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap"></noscript>
            
            <link rel="preload" href="https://cdnjs.cloudflare.com/ajax/libs/animate.css/4.1.1/animate.min.css" as="style" onload="this.onload=null;this.rel='stylesheet'">
            <noscript><link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/animate.css/4.1.1/animate.min.css"></noscript>
            
            <!-- Pass user theme preference to JavaScript -->
            <script>
                window.userThemePreference = '<?php echo isset($ir['theme_preference']) ? $ir['theme_preference'] : 'dark'; ?>';
                
                // Register Service Worker for performance caching
                if ('serviceWorker' in navigator && 'caches' in window) {
                    window.addEventListener('load', () => {
                        navigator.serviceWorker.register('/sw.js')
                            .then(registration => {
                                console.log('SW registered: ', registration);
                            })
                            .catch(registrationError => {
                                console.log('SW registration failed: ', registrationError);
                            });
                    });
                }
            </script>
            
            <!-- Apply saved sidebar state immediately to prevent FOUC -->
            <script>
                (function() {
                    var state = localStorage.getItem('chivalry_sidebar_state');
                    if (state === 'open') {
                        document.documentElement.classList.add('sidebar-will-open');
                    }
                })();
            </script>
        </head>
        <?php
        if (empty($menuhide))
        {
            $ir['mail'] = $db->fetch_single($db->query("SELECT COUNT(`mail_id`) FROM `mail` WHERE `mail_to` = {$ir['userid']} AND `mail_status` = 'unread'"));
            $ir['notifications'] = $db->fetch_single($db->query("SELECT COUNT(`notif_id`) FROM `notifications` WHERE `notif_user` = {$ir['userid']} AND `notif_status` = 'unread'"));
            $energy = $api->user->getInfoPercent($userid, 'energy');
            $brave = $api->user->getInfoPercent($userid, 'brave');
            $will = $api->user->getInfoPercent($userid, 'will');
            $xp = round($ir['xp'] / $ir['xp_needed'] * 100);
            $hp = $api->user->getInfoPercent($userid, 'hp');
            // Ignore database preference - use localStorage instead
            $toggle = '';
            ?>
            <body>
            <div class="page-wrapper default-theme sidebar-bg">
            <script>
                // Apply saved state immediately after page wrapper is created
                (function() {
                    var state = localStorage.getItem('chivalry_sidebar_state');
                    var pageWrapper = document.querySelector('.page-wrapper');
                    if (pageWrapper) {
                        if (state === 'open') {
                            pageWrapper.classList.add('toggled');
                            document.body.classList.add('sidebar-open');
                        } else {
                            pageWrapper.classList.remove('toggled');
                            document.body.classList.remove('sidebar-open');
                        }
                    }
                })();
            </script>
                <!-- Restore Original Sidebar -->
                <div class="sidebar-overlay"></div>
                <div id="show-sidebar" class="btn btn-sm btn-primary">
                    <i class="fas fa-bars"></i>
                </div>
                <nav id="sidebar" class="sidebar-wrapper">
                    <div class="sidebar-content">
                        <div class="sidebar-item sidebar-brand">
                            <a href="index.php"><?php echo $set['WebsiteName']; ?></a>
                            <div id='close-sidebar'>
                                <i class='fas fa-times'></i>
                            </div>
                        </div>
                        
                        <!-- User Info Card -->
                        <div class="sidebar-item sidebar-header">
                            <div class="user-info">
                                <span class="user-name">
                                    <?php 
                                    echo $ir['username'];
                                    // Show VIP badge if user is VIP
                                    if (isset($ir['vip_days']) && $ir['vip_days'] > 0) {
                                        echo " <span class='badge bg-warning text-dark'><i class='fas fa-crown'></i> VIP</span>";
                                    }
                                    ?>
                                </span>
                                <span class="user-role">Level <?php echo $ir['level']; ?> Player</span>
                                <span class="user-status"><i class="fa fa-circle text-success"></i> Online</span>
                                <?php if (isset($ir['vip_days']) && $ir['vip_days'] > 0): ?>
                                <span class="user-vip"><i class="fas fa-crown text-warning"></i> <?php echo $ir['vip_days']; ?> VIP days remaining</span>
                                <?php endif; ?>
                            </div>
                        </div>
                        
                        <!-- Quick Stats -->
                        <div class="sidebar-item px-3 py-1">
                            <div class="quick-stats">
                                <div class="stat-item" style="margin-bottom: 2px;">
                                    <small class="text-muted d-block" style="font-size: 10px; margin-bottom: 1px;">HP</small>
                                    <div class="progress" style="height: 14px;">
                                        <div class="progress-bar bg-danger" id="sidebar-hp-bar" style="width: <?php echo $hp; ?>%"><?php echo $hp; ?>%</div>
                                    </div>
                                </div>
                                <div class="stat-item" style="margin-bottom: 2px;">
                                    <small class="text-muted d-block" style="font-size: 10px; margin-bottom: 1px;">Energy</small>
                                    <div class="progress" style="height: 14px;">
                                        <div class="progress-bar bg-warning" id="sidebar-energy-bar" style="width: <?php echo $energy; ?>%"><?php echo $energy; ?>%</div>
                                    </div>
                                </div>
                                <div class="stat-item" style="margin-bottom: 2px;">
                                    <small class="text-muted d-block" style="font-size: 10px; margin-bottom: 1px;">XP</small>
                                    <div class="progress" style="height: 14px;">
                                        <div class="progress-bar bg-info" id="sidebar-xp-bar" style="width: <?php echo $xp; ?>%"><?php echo $xp; ?>%</div>
                                    </div>
                                </div>
                            </div>
                            <div class="currency-display" style="margin-top: 8px;">
                                <div class="d-flex justify-content-between mb-1">
                                    <span><i class="fas fa-coins text-warning"></i> <?php echo constant("primary_currency"); ?></span>
                                    <span><?php echo number_format($ir['primary_currency']); ?></span>
                                </div>
                                <div class="d-flex justify-content-between">
                                    <span><i class="fas fa-gem text-info"></i> <?php echo constant("secondary_currency"); ?></span>
                                    <span><?php echo number_format($ir['secondary_currency']); ?></span>
                                </div>
                            </div>
                        </div>
                        
                        <!-- Navigation Menu -->
                        <div class="sidebar-item sidebar-menu">
                            <ul>
                                <li class="header-menu">
                                    <span>Main Navigation</span>
                                </li>
                                <!-- CORE FEATURES -->
                                <li>
                                    <a href="index.php" class="animate__animated animate__fadeIn">
                                        <i class="fas fa-home"></i>
                                        <span class="menu-text">Dashboard</span>
                                    </a>
                                </li>
                                <li>
                                    <a href="inventory.php">
                                        <i class="fas fa-bag-shopping"></i>
                                        <span class="menu-text">Inventory</span>
                                    </a>
                                </li>
                                <li>
                                    <a href="explore.php">
                                        <i class="fas fa-map"></i>
                                        <span class="menu-text">Explore City</span>
                                    </a>
                                </li>
                                <li>
                                    <a href="attackselect.php">
                                        <i class="fas fa-user-ninja"></i>
                                        <span class="menu-text">Attack Players</span>
                                    </a>
                                </li>
                                <li>
                                    <a href="gym.php">
                                        <i class="fas fa-dumbbell"></i>
                                        <span class="menu-text">Training</span>
                                    </a>
                                </li>
                                <li>
                                    <a href="travel.php">
                                        <i class="fas fa-plane"></i>
                                        <span class="menu-text">Travel</span>
                                    </a>
                                </li>
                                
                                <!-- ACTIVITIES -->
                                <li class="sidebar-dropdown">
                                    <a href="#">
                                        <i class="fas fa-tasks"></i>
                                        <span class="menu-text">Activities</span>
                                    </a>
                                    <div class="sidebar-submenu">
                                        <ul>
                                            <li><a href="criminal.php"><i class="fas fa-mask"></i> Crimes</a></li>
                                            <li><a href="job.php"><i class="fas fa-briefcase"></i> Your Job</a></li>
                                            <li><a href="mine.php"><i class="fas fa-mountain"></i> Mining</a></li>
                                            <li><a href="academy.php"><i class="fas fa-graduation-cap"></i> Academy</a></li>
                                            <li><a href="dungeons.php"><i class="fas fa-dungeon"></i> Dungeons</a></li>
                                            <li><a href="battle_royale.php"><i class="fas fa-crown"></i> Battle Royale</a></li>
                                        </ul>
                                    </div>
                                </li>
                                
                                <!-- ECONOMY -->
                                <li class="sidebar-dropdown">
                                    <a href="#">
                                        <i class="fas fa-coins"></i>
                                        <span class="menu-text">Economy</span>
                                    </a>
                                    <div class="sidebar-submenu">
                                        <ul>
                                            <li><a href="shops.php"><i class="fas fa-store"></i> Shops</a></li>
                                            <li><a href="itemmarket.php"><i class="fas fa-balance-scale"></i> Item Market</a></li>
                                            <li><a href="bank.php"><i class="fas fa-university"></i> Bank</a></li>
                                            <li><a href="estates.php"><i class="fas fa-home"></i> Estates</a></li>
                                        </ul>
                                    </div>
                                </li>
                                
                                <!-- SOCIAL -->
                                <li class="sidebar-dropdown">
                                    <a href="#">
                                        <i class="fas fa-users"></i>
                                        <span class="menu-text">Social</span>
                                    </a>
                                    <div class="sidebar-submenu">
                                        <ul>
                                            <li><a href="inbox.php"><i class="fas fa-envelope"></i> Messages <?php if($ir['mail'] > 0) echo "<span class='badge bg-danger'>{$ir['mail']}</span>"; ?></a></li>
                                            <li><a href="forums.php"><i class="fas fa-comments"></i> Forums</a></li>
                                            <li><a href="guilds.php"><i class="fas fa-shield-alt"></i> Guilds</a></li>
                                            <?php if ($ir['guild']): ?>
                                            <li><a href="guild_wars.php"><i class="fas fa-chess"></i> Guild Wars</a></li>
                                            <?php endif; ?>
                                            <li><a href="users.php"><i class="fas fa-user-friends"></i> Player List</a></li>
                                            <li><a href="marriage.php"><i class="fas fa-heart"></i> Marriage</a></li>
                                            <li><a href="stats.php"><i class="fas fa-trophy"></i> Hall of Fame</a></li>
                                            <li><a href="daily_rewards.php"><i class="fas fa-calendar-check"></i> Daily Rewards</a></li>
                                            <li><a href="achievements.php"><i class="fas fa-medal"></i> Achievements</a></li>
                                            <li><a href="pets.php"><i class="fas fa-paw"></i> Pets</a></li>
                                        </ul>
                                    </div>
                                </li>
                                
                                <!-- GAMES -->
                                <li class="sidebar-dropdown">
                                    <a href="#">
                                        <i class="fas fa-dice"></i>
                                        <span class="menu-text">Casino</span>
                                    </a>
                                    <div class="sidebar-submenu">
                                        <ul>
                                            <li><a href="slots.php"><i class="fas fa-coins"></i> Slots</a></li>
                                            <li><a href="roulette.php"><i class="fas fa-circle-notch"></i> Roulette</a></li>
                                            <li><a href="hilow.php"><i class="fas fa-sort"></i> High/Low</a></li>
                                            <li><a href="russianroulette.php"><i class="fas fa-skull"></i> Russian Roulette</a></li>
                                        </ul>
                                    </div>
                                </li>
                                
                                <!-- UTILITIES -->
                                <li class="sidebar-dropdown">
                                    <a href="#">
                                        <i class="fas fa-tools"></i>
                                        <span class="menu-text">More</span>
                                    </a>
                                    <div class="sidebar-submenu">
                                        <ul>
                                            <li><a href="infirmary.php"><i class="fas fa-hospital"></i> Infirmary</a></li>
                                            <li><a href="dungeon.php"><i class="fas fa-dungeon"></i> Dungeon</a></li>
                                            <li><a href="notifications.php"><i class="fas fa-bell"></i> Notifications <?php if($ir['notifications'] > 0) echo "<span class='badge bg-warning'>{$ir['notifications']}</span>"; ?></a></li>
                                        </ul>
                                    </div>
                                </li>
                                <li>
                                    <a href="preferences.php">
                                        <i class="fas fa-cog"></i>
                                        <span class="menu-text">Settings</span>
                                    </a>
                                </li>
                                <li>
                                    <a href="donator.php" class="text-warning">
                                        <i class="fas fa-crown"></i>
                                        <span class="menu-text">VIP Membership</span>
                                    </a>
                                </li>
                                <?php if ($api->user->getStaffLevel($userid, 'forum moderator')) { ?>
                                <li>
                                    <a href="staff.php" class="text-warning">
                                        <i class="fas fa-shield-alt"></i>
                                        <span class="menu-text">Staff Panel</span>
                                    </a>
                                </li>
                                <?php } ?>
                                <li>
                                    <a href="logout.php" class="text-danger">
                                        <i class="fas fa-sign-out-alt"></i>
                                        <span class="menu-text">Logout</span>
                                    </a>
                                </li>
                            </ul>
                        </div>
                    </div>
                </nav>
                
                <!-- Toast Container for Bootstrap 5 Notifications -->
                <div class="toast-container position-fixed top-0 end-0 p-3" style="z-index: 1080;">
                </div>
                
                <main class="page-content">
                    <div class="container-fluid">
                    <?php 
                    // Show upgrade notice if v3 features aren't installed
                    if (!$v3_installed && $api->user->getStaffLevel($userid, 'admin')): ?>
                        <div class="alert alert-warning alert-dismissible fade show" role="alert">
                            <i class="fas fa-exclamation-triangle"></i> <strong>New Features Available!</strong>
                            <p class="mb-2">Chivalry Engine v3.0 features are not installed. Install them to get:</p>
                            <ul class="mb-2">
                                <li>Achievement System with 30+ achievements</li>
                                <li>Daily Login Rewards with streaks</li>
                                <li>Guild Wars with territory control</li>
                                <li>Battle Royale events</li>
                                <li>Pet System with battles</li>
                                <li>Dungeons & Raids</li>
                                <li>Skill Trees & Crafting</li>
                                <li>Leaderboards & Seasons</li>
                            </ul>
                            <a href="uplift_check.php" class="btn btn-warning btn-sm">
                                <i class="fas fa-download"></i> Run Uplift Check Now
                            </a>
                            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                        </div>
                    <?php endif; ?>
            <?php
        }
    }

    function userdata($user, $showlast = true)
    {
        global $db, $userid, $set;
        // This function was used to display user data in header
        // In modern version, it's handled in the startheaders function
        // Keep empty for compatibility
    }
    
    function endpage()
    {
        global $db, $ir, $set, $menuhide, $userid, $api, $time;
        if (empty($menuhide)) {
            ?>
                    </div>
                </main>
            </div>
            
            <!-- Critical JS - Load immediately -->
            <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
            <script src="js/sidebar-state.js"></script>
            <script src="js/theme-switcher.js"></script>
            <script src="js/lazy-loading.js"></script>
            
            <!-- Non-critical JS - Load asynchronously -->
            <script src="https://code.jquery.com/jquery-3.6.0.min.js" defer></script>
            <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11" defer></script>
            <script src="js/game.js" defer></script>
            <script src="js/sidebar-improved.js" defer></script>
            <script src="js/modern-enhancements.js" defer></script>
            <script src="js/realtime-stats.js" defer></script>
            <script src="js/realtime-client.js" defer></script>
            
            <?php
        }
        ?>
        </body>
        </html>
        <?php
    }
}
$h = new headers;
?>