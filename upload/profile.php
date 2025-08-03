<?php
/*
	File:		profile.php
	Created: 	6/23/2019 at 6:11PM Eastern Time
	Info: 		Allows the palyer to load player-based profiles.
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
require("globals.php");
require_once("includes/vip-benefits.php");

// Check if marriage system is available
$marriage_available = true;
try {
    $db->query("SELECT 1 FROM marriages LIMIT 1");
    require_once("includes/marriage-system.php");
} catch (Exception $e) {
    $marriage_available = false;
}
$code = getCodeCSRF('inbox_send');
$code2 = getCodeCSRF('cash_send');
$_GET['user'] = (isset($_GET['user']) && is_numeric($_GET['user'])) ? abs($_GET['user']) : '';
if (!$_GET['user']) {
    alert("danger", "Uh Oh!", "Please specify a user you wish to view.", true, 'index.php');
} else {
    $q =
        $db->query(
            "SELECT `u`.`userid`, `user_level`, `laston`, `last_login`,
                    `registertime`, `vip_days`, `username`, `gender`,
					`primary_currency`, `secondary_currency`, `level`,
					`display_pic`, `hp`, `maxhp`, `guild`,
                    `fedjail`, `lastip`, `lastip`,
                    `loginip`, `registerip`, `staff_notes`, `town_name`,
                    COALESCE(`house_name`, 'No House') as `house_name`, 
                    COALESCE(`guild_name`, 'No Guild') as `guild_name`, 
                    COALESCE(`fed_out`, 0) as `fed_out`, 
                    COALESCE(`fed_reason`, '') as `fed_reason`,
					COALESCE(`infirmary_reason`, '') as `infirmary_reason`, 
                    COALESCE(`infirmary_out`, 0) as `infirmary_out`, 
                    COALESCE(`dungeon_reason`, '') as `dungeon_reason`, 
                    COALESCE(`dungeon_out`, 0) as `dungeon_out`
                    FROM `users` `u`
                    LEFT JOIN `town` AS `t`
                    ON `u`.`location` = `t`.`town_id`
					LEFT JOIN `infirmary` AS `i`
					ON `u`.`userid` = `i`.`infirmary_user`
					LEFT JOIN `dungeon` AS `d`
					ON `u`.`userid` = `d`.`dungeon_user`
                    LEFT JOIN `estates` AS `e`
                    ON `u`.`maxwill` = e.`house_will`
                    LEFT JOIN `guild` AS `g`
                    ON `g`.`guild_id` = `u`.`guild`
                    LEFT JOIN `fedjail` AS `f`
                    ON `f`.`fed_userid` = `u`.`userid`
                    WHERE `u`.`userid` = {$_GET['user']}");

    if ($db->num_rows($q) == 0) {
        $db->free_result($q);
        alert("danger", "Uh Oh!", "The user you are trying to view does not exist, or has an account issue.", true, 'index.php');
    } else {
        $r = $db->fetch_row($q);
        $db->free_result($q);
        $lon = ($r['laston'] > 0) ? date('F j, Y g:i:s a', $r['laston']) : "Never";
        $ula = ($r['laston'] == 0) ? 'Never' : dateTimeParse($r['laston']);
        $ull = ($r['last_login'] == 0) ? 'Never' : dateTimeParse($r['last_login']);
        $sup = date('F j, Y g:i:s a', $r['registertime']);
        $displaypic = ($r['display_pic']) ? "<img src='{$r['display_pic']}' class='img-thumbnail img-responsive' width='250' height='250'>" : '';
        
        // Initialize VIP benefits for the viewed user
        $vipBenefits = getVIPBenefits($db, $r['userid']);
        $user_name = $vipBenefits->getVIPUsernameStyle($r['username']);
        $ref_q =
            $db->query(
                "SELECT COUNT(`referalid`)
                         FROM `referals`
                         WHERE `referal_userid` = {$r['userid']}");
        $ref = $db->fetch_single($ref_q);
        $db->free_result($ref_q);
        $friend_q =
            $db->query(
                "SELECT COUNT(`friend_id`)
                         FROM `friends`
                         WHERE `friended` = {$r['userid']}");
        $friend = $db->fetch_single($friend_q);
        $db->free_result($friend_q);
        $enemy_q =
            $db->query(
                "SELECT COUNT(`enemy_id`)
                         FROM `enemy`
                         WHERE `enemy_user` = {$r['userid']}");
        $enemy = $db->fetch_single($enemy_q);
        $db->free_result($enemy_q);
        $CurrentTime = time();
        $r['daysold'] = dateTimeParse($r['registertime'], false, true);

        $rhpperc = round($r['hp'] / $r['maxhp'] * 100);
        echo "<h3>{$user_name}'s Profile</h3>";
        ?>
		<div class="row">
			<div class="col-lg-2">
				<?php
        echo "{$displaypic}<br />
                        {$r['user_level']}<br />
						Location {$r['town_name']}<br />
                        Level: {$r['level']}<br />";
        echo ($r['guild']) ? "Guild: <a href='guilds.php?action=view&id={$r['guild']}'>{$r['guild_name']}</a><br />" : '';
        echo "Health: {$r['hp']}/{$r['maxhp']}<br />";
        
        // Show marriage information if available
        if ($marriage_available) {
            $marriageSystem = getMarriageSystem($db, $r['userid']);
            if ($marriageSystem->isMarried()) {
                $spouse = $marriageSystem->getSpouse();
                $stats = $marriageSystem->getMarriageStats();
                echo "<strong>Marriage:</strong><br />";
                echo "Married to: <a href='profile.php?user={$spouse['userid']}'>{$spouse['username']}</a><br />";
                echo "Since: " . date('M j, Y', $spouse['marriage_date']) . "<br />";
                echo "Days married: {$stats['days_married']}<br />";
            } else {
                echo "<strong>Relationship:</strong> Single<br />";
            }
        }

        ?>
			</div>
			<div class="col-lg-10">
				<ul class="nav nav-tabs nav-justified">
				  <li class="nav-item"><a class='nav-link active' data-bs-toggle="tab" href="#info"><?php echo "Physical Info"; ?></a></li>
				  <li class='nav-item'><a class='nav-link' data-bs-toggle="tab" href="#actions"><?php echo "Actions"; ?></a></li>
				  <li class='nav-item'><a class='nav-link' data-bs-toggle="tab" href="#financial"><?php echo "Financial Info"; ?></a></li>
				  <?php if ($marriage_available): ?>
				  <li class='nav-item'><a class='nav-link' data-bs-toggle="tab" href="#relationship"><?php echo "Relationship"; ?></a></li>
				  <?php endif; ?>
				  <?php
        if (!in_array($ir['user_level'], array('Member', 'NPC'))) {
            echo "<li class='nav-item'><a class='nav-link' data-bs-toggle='tab' href='#staff'>Staff</a></li>";
        }
        ?>
				</ul>
				<br />
				<div class="tab-content">
				  <div id="info" class="tab-pane fade show active">
					<p>
						<?php
        echo
        "
						<table class='table table-bordered'>
							<tr>
								<th width='25%'>Sex</th>
								<td>{$r['gender']}</td>
							</tr>
							<tr>
								<th>Registered</th>
								<td>{$sup}</td>
							</tr>
							<tr>
								<th>Last Active</th>
								<td>{$ula}</td>
							</tr>
							<tr>
								<th>Last Login</th>
								<td>{$ull}</td>
							</tr>
							<tr>
								<th>Age</th>
								<td>{$r['daysold']}</td>
							</tr>";
        if (userInInfirmary($r['userid'])) {
            echo "
							<tr>
								<th>Infirmary</th>
								<td>In the infirmary for " . timeUntilParse($r['infirmary_out']) . ".<br />
								{$r['infirmary_reason']}
								</td>
							</tr>";
        }
        if (userInDungeon($r['userid'])) {
            echo "
							<tr>
								<th>Dungeon</th>
								<td>In the dungeon for " . timeUntilParse($r['dungeon_out']) . ".<br />
								{$r['dungeon_reason']}
								</td>
							</tr>";
        }
        if ($r['fedjail']) {
            echo "
							<tr>
								<th>Federal Dungeon</th>
								<td>In the federal dungeon for " . timeUntilParse($r['fed_out']) . ".<br />
								{$r['fed_reason']}
								</td>
							</tr>";
        }

        echo "</table>
					</p>
				  </div>
				  <div id='actions' class='tab-pane fade'>
                    <a href='inbox.php?action=compose&user={$r['userid']}' class='btn btn-primary'>Message {$r['username']}</a>
                    <br />
				    <br />
				    <a href='sendcash.php?user={$r['userid']}' class='btn btn-primary'>Send {$r['username']} Cash</a>
				    <br />
				    <br />
					<a href='attack.php?user={$r['userid']}' class='btn btn-danger'>Attack {$r['username']}</a>
					<br />
					<br />
					<a href='hirespy.php?user={$r['userid']}' class='btn btn-primary'>Spy On {$r['username']}</a>
					<br />
					<br />
					<a href='contacts.php?action=add&user={$r['userid']}' class='btn btn-primary'>Add {$r['username']} to Contact List</a>
				  ";
        ?>
				  </div>
				  <div id="financial" class="tab-pane fade">
					<?php
        echo
            "
						<table class='table table-bordered'>
							<tr>
								<th width='25%'>" . constant("primary_currency") . "</th>
								<td> " . number_format($r['primary_currency']) . "</td>
							</tr>
							<tr>
								<th>" . constant("secondary_currency") . "</th>
								<td>" . number_format($r['secondary_currency']) . "</td>
							</tr>
							<tr>
								<th>Estate</th>
								<td>{$r['house_name']}</td>
							</tr>
							<tr>
								<th>Referrals</th>
								<td>" . number_format($ref) . "</td>
							</tr>
							<tr>
								<th>Friends</th>
								<td>" . number_format($friend) . "</td>
							</tr>
							<tr>
								<th>Enemies</th>
								<td>" . number_format($enemy) . "</td>
							</tr>
						</table>";

        ?>
				  </div>
				  <?php if ($marriage_available): ?>
				  <div id="relationship" class="tab-pane fade">
				    <?php
        $marriageSystem = getMarriageSystem($db, $r['userid']);
        if ($marriageSystem->isMarried()) {
            $spouse = $marriageSystem->getSpouse();
            $stats = $marriageSystem->getMarriageStats();
            
            echo "<div class='card'>";
            echo "<div class='card-header bg-success text-white'>";
            echo "<h5><i class='fas fa-heart'></i> Marriage Information</h5>";
            echo "</div>";
            echo "<div class='card-body'>";
            
            echo "<div class='row'>";
            echo "<div class='col-md-6'>";
            echo "<table class='table table-borderless'>";
            echo "<tr><td><strong>Spouse:</strong></td><td><a href='profile.php?user={$spouse['userid']}'>{$spouse['username']}</a></td></tr>";
            echo "<tr><td><strong>Married Since:</strong></td><td>" . date('F j, Y', $spouse['marriage_date']) . "</td></tr>";
            echo "<tr><td><strong>Days Married:</strong></td><td>{$stats['days_married']} days</td></tr>";
            echo "<tr><td><strong>Anniversary:</strong></td><td>" . date('F j', $spouse['marriage_date']) . " (annually)</td></tr>";
            echo "</table>";
            echo "</div>";
            
            echo "<div class='col-md-6'>";
            echo "<table class='table table-borderless'>";
            echo "<tr><td><strong>Gifts Sent:</strong></td><td>{$stats['gifts_sent']}</td></tr>";
            echo "<tr><td><strong>Gifts Received:</strong></td><td>{$stats['gifts_received']}</td></tr>";
            $spouse_online = $spouse['laston'] > 0 ? dateTimeParse($spouse['laston']) : 'Never';
            echo "<tr><td><strong>Spouse Last Seen:</strong></td><td>{$spouse_online}</td></tr>";
            echo "</table>";
            echo "</div>";
            echo "</div>";
            
            if ($r['userid'] != $userid) {
                echo "<div class='mt-3'>";
                echo "<p class='text-muted'><i class='fas fa-info-circle'></i> This player is married and not available for proposals.</p>";
                echo "</div>";
            }
            
            echo "</div>";
            echo "</div>";
            
        } else {
            echo "<div class='card'>";
            echo "<div class='card-header bg-info text-white'>";
            echo "<h5><i class='fas fa-heart'></i> Relationship Status</h5>";
            echo "</div>";
            echo "<div class='card-body text-center'>";
            echo "<i class='fas fa-heart-broken fa-3x text-muted mb-3'></i>";
            echo "<h4>Single</h4>";
            echo "<p class='text-muted'>This player is not currently married.</p>";
            
            if ($r['userid'] != $userid) {
                echo "<div class='mt-3'>";
                echo "<a href='marriage.php?action=propose&to={$r['userid']}' class='btn btn-primary'>";
                echo "<i class='fas fa-heart'></i> Send Marriage Proposal";
                echo "</a>";
                echo "</div>";
            }
            
            echo "</div>";
            echo "</div>";
        }
        ?>
				  </div>
				  <?php endif; ?>
				  <?php
        echo '<div id="staff" class="tab-pane fade">';
        if (!in_array($ir['user_level'], array('Member', 'NPC'))) {
            $log = $db->fetch_row($db->query("SELECT `log_text`,`log_time` FROM `logs` WHERE `log_user` = {$r['userid']} ORDER BY `log_id` DESC"));
            echo "<a href='staff/staff_punish.php?action=fedjail&user={$r['userid']}' class='btn btn-primary'>Fedjail</a>
                <a href='staff/staff_punish.php?action=forumban&user={$r['userid']}' class='btn btn-primary'>Forum Ban</a>";
            echo "<table class='table table-bordered'>
							<tr>
								<th width='33%'>Data</th>
								<th>Output</th>
								<th>Details</th>
							</tr>
							<tr>
								<td>Last Hit</td>
								<td>{$r['lastip']}</td>
								<td>" . gethostbyaddr($r['lastip']) . "</td>
							</tr>
							<tr>
								<td>Last Login</td>
								<td>{$r['loginip']}</td>
								<td>" . gethostbyaddr($r['loginip']) . "</td>
							</tr>
							<tr>
								<td>Sign Up</td>
								<td>{$r['registerip']}</td>
								<td>" . gethostbyaddr($r['registerip']) . "</td>
							</tr>
							<tr>
								<td>
									Last Action
								</td>
								<td>
									{$log['log_text']}
								</td>
								<td>
									" . dateTimeParse($log['log_time']) . "
								</td>
							</tr>
					</table>
					<form action='staff/staff_punish.php?action=staffnotes' method='post'>
						Staff Notes
						<br />
						<textarea class='form-control' name='staffnotes'>"
                . htmlentities($r['staff_notes'], ENT_QUOTES, 'ISO-8859-1')
                . "</textarea>
						<br />
						<input type='hidden' name='ID' value='{$_GET['user']}' />
						<input type='submit' class='btn btn-primary' value='Update Notes' />
					</form>";
        }
        ?>
				  
				  </div>
				</div>
			</div>
		</div>
		<?php
    }
}
$h->endpage();