<?php
/*
	File:		profile.php
	Created: 	4/5/2016 at 12:23AM Eastern Time
	Info: 		Allows players to view a player's profile page. This
				displays information about their level, location,
				gender, cash, estate, etc.
	Author:		TheMasterGeneral
	Website: 	https://github.com/MasterGeneral156/chivalry-engine
*/
require("globals.php");
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
                    `house_name`, `guild_name`, `fed_out`, `fed_reason`,
					`infirmary_reason`, `infirmary_out`, `dungeon_reason`, `dungeon_out`
                    FROM `users` `u`
                    INNER JOIN `town` AS `t`
                    ON `u`.`location` = `t`.`town_id`
					LEFT JOIN `infirmary` AS `i`
					ON `u`.`userid` = `i`.`infirmary_user`
					LEFT JOIN `dungeon` AS `d`
					ON `u`.`userid` = `d`.`dungeon_user`
                    INNER JOIN `estates` AS `e`
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
        $user_name = ($r['vip_days']) ? "<span class='text-danger'>{$r['username']} <i class='fas fa-shield-alt'
            data-toggle='tooltip' title='{$r['vip_days']} VIP Days remaining.'></i></span>" : $r['username'];
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

        ?>
			</div>
			<div class="col-lg-10">
				<ul class="nav nav-tabs nav-justified">
				  <li class="active nav-item"><a class='nav-link' data-toggle="tab" href="#info"><?php echo "Physical Info"; ?></a></li>
				  <li class='nav-item'><a class='nav-link' data-toggle="tab" href="#actions"><?php echo "Actions"; ?></a></li>
				  <li class='nav-item'><a class='nav-link' data-toggle="tab" href="#financial"><?php echo "Financial Info"; ?></a></li>
				  <?php
        if (!in_array($ir['user_level'], array('Member', 'NPC'))) {
            echo "<li class='nav-item'><a class='nav-link' data-toggle='tab' href='#staff'>Staff</a></li>";
        }
        ?>
				</ul>
				<br />
				<div class="tab-content">
				  <div id="info" class="tab-pane active">
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
				  <div id='actions' class='tab-pane'>
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
				  <div id="financial" class="tab-pane">
					<?php
        echo
            "
						<table class='table table-bordered'>
							<tr>
								<th width='25%'>{$_CONFIG['primary_currency']}</th>
								<td> " . number_format($r['primary_currency']) . "</td>
							</tr>
							<tr>
								<th>{$_CONFIG['secondary_currency']}</th>
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
				  <?php
        echo '<div id="staff" class="tab-pane">';
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