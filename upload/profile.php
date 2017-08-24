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
$code = request_csrf_code('inbox_send');
$code2 = request_csrf_code('cash_send');
$_GET['user'] = (isset($_GET['user']) && is_numeric($_GET['user'])) ? abs($_GET['user']) : '';
if (!$_GET['user'])
{
   alert("danger","Uh Oh!","Please specify a user you wish to view.",true,'index.php');
}
else
{
	$q =
            $db->query(
                    "SELECT `u`.`userid`, `user_level`, `laston`, `last_login`,
                    `registertime`, `vip_days`, `username`, `gender`,
					`primary_currency`, `secondary_currency`, `level`, `class`,
					`display_pic`, `hp`, `maxhp`, `guild`,
                    `fedjail`, `bank`, `lastip`, `lastip`,
                    `loginip`, `registerip`, `staff_notes`, `town_name`,
                    `house_name`, `guild_name`, `fed_out`, `fed_reason`,
					`infirmary_reason`, `infirmary_out`, `dungeon_reason`, `dungeon_out`,
					`browser`, `os`, `screensize`
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
					LEFT JOIN `userdata` AS `ud`
                    ON `ud`.`userid` = `u`.`userid`
                    WHERE `u`.`userid` = {$_GET['user']}");
					
	if ($db->num_rows($q) == 0)
	{
		$db->free_result($q);
		alert("danger","Uh Oh!","The user you are trying to view does not exist, or has an account issue.",true,'index.php');
	}
	else
    {
		$r = $db->fetch_row($q);
        $db->free_result($q);
		$lon = ($r['laston'] > 0) ? date('F j, Y g:i:s a', $r['laston']) : "Never";
        $ula = ($r['laston'] == 0) ? 'Never' : DateTime_Parse($r['laston']);
        $ull = ($r['last_login'] == 0) ? 'Never'  : DateTime_Parse($r['last_login']);
        $sup = date('F j, Y g:i:s a', $r['registertime']);
		$displaypic = ($r['display_pic']) ? "<img src='{$r['display_pic']}' class='img-thumbnail img-responsive' width='250' height='250'>" : '';
		$user_name = ($r['vip_days']) ? "<span style='color:red; font-weight:bold;'>{$r['username']} <i class='fa fa-shield' data-toggle='tooltip' title='{$r['username']} has {$r['vip_days']} VIP Days remaining.'></i></span>" : $r['username'];
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
		$CurrentTime=time();
		$r['daysold']=DateTime_Parse($r['registertime'], false, true);
		
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
					if (!in_array($ir['user_level'], array('Member', 'NPC')))
					{
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
								<th>Class</th>
								<td>{$r['class']}</td>
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
						if (user_infirmary($r['userid']))
						{
							echo "
							<tr>
								<th>Infirmary</th>
								<td>In the infirmary for " . TimeUntil_Parse($r['infirmary_out']) . "<br />
								{$r['infirmary_reason']}
								</td>
							</tr>";
						}
						if (user_dungeon($r['userid']))
						{
							echo "
							<tr>
								<th>Dungeon</th>
								<td>In the dungeon for " . TimeUntil_Parse($r['dungeon_out']) . "<br />
								{$r['dungeon_reason']}
								</td>
							</tr>";
						}
						if ($r['fedjail'])
						{
							echo "
							<tr>
								<th>Federal Dungeon</th>
								<td>In the federal dungeon for " . TimeUntil_Parse($r['fed_out']) . " {$lang['MENU_FEDJAIL2']}<br />
								{$r['fed_reason']}
								</td>
							</tr>";
						}
						
						echo"</table>";
						?>
					</p>
				  </div>
				  <div id="actions" class="tab-pane">
				  <?php echo "<button type='button' class='btn btn-primary' data-toggle='modal' data-target='#message'>Send Message to {$r['username']}</button>"; ?>
				  <?php echo "<br /><br /><button type='button' class='btn btn-primary' data-toggle='modal' data-target='#cash'>Send Cash to {$r['username']}</button>
				  <br /><br /><form action='attack.php'>
					<input type='hidden' name='user' value='{$r['userid']}'>
					<input type='submit' class='btn btn-danger' value='Attack {$r['username']}'>
					</form><br />
					<form action='hirespy.php'>
						<input type='hidden' name='user' value='{$r['userid']}'>
						<input type='submit' class='btn btn-primary' value='Hire Spy on {$r['username']}'>
					</form>
					<br />
					<form action='poke.php'>
						<input type='hidden' name='user' value='{$r['userid']}'>
						<input type='submit' class='btn btn-primary' value='Poke {$r['username']}'>
					</form>
					<br />
					<form action='contacts.php'>
						<input type='hidden' name='action' value='add'>
						<input type='hidden' name='user' value='{$r['userid']}'>
						<input type='submit' class='btn btn-primary' value='Add {$r['username']} to Contact List'>
					</form>
				  "; ?>
					<div class="modal fade" id="message">
						<div class="modal-dialog" role="document">
							<div class="modal-content">
								<div class="modal-header">
									<h5 class="modal-title"><?php echo "Sending {$r['username']} a Message"; ?></h5>
									<button type="button" class="close" data-dismiss="modal" aria-label="Close">
									  <span aria-hidden="true">&times;</span>
									</button>
								</div>
								<div class="modal-body">
									<div id="success"></div>
									<div id="result"></div>
									<form id="mailpopupForm" name="mailpopupForm" action="js/script/sendmail.php">
										<div class="form-group">
											<div id="result"></div>
											<label for="recipient-name" class="control-label"><?php echo "Recipient"; ?></label>
											<input type="text" class="form-control" name="sendto" required="1" value="<?php echo $r['username']; ?>" id="recipient-name">
										</div>
										<div class="form-group">
											<label for="message-text" class="control-label"><?php echo "Message"; ?></label>
											<textarea class="form-control" name="msg" required="1" id="message-text"></textarea>
										</div>
								</div>
								<div class="modal-footer">
									<?php
										echo"<input type='hidden' name='verf' value='{$code}' />";
									?>
									<button type="button" class="btn btn-primary" data-dismiss="modal"><?php echo "Close Window"; ?></button>
									<input type="submit" value="<?php echo "Send Message"; ?>" id="sendmessage" class="btn btn-primary">
									</form>
								</div>
							</div>
						</div>
					</div>
				  <div class="modal fade" id="cash">
						<div class="modal-dialog" role="document">
							<div class="modal-content">
								<div class="modal-header">
									<h5 class="modal-title"><?php echo "Sending Cash to {$r['username']}"; ?></h5>
									<button type="button" class="close" data-dismiss="modal" aria-label="Close">
									  <span aria-hidden="true">&times;</span>
									</button>
								</div>
								<div class="modal-body">
									<div id="success2"></div>
									<div id="result2"></div>
									<form id="cashpopupForm" name="cashpopupForm" action="js/script/sendmail.php">
										<div class="form-group">
											<div id="result"></div>
											<label for="recipient-name" class="control-label"><?php echo "Recipient"; ?></label>
											<input type="text" class="form-control" name="sendto" required="1" value="<?php echo $r['username']; ?>" id="recipient-name">
										</div>
										<div class="form-group">
											<label for="message-text" class="control-label"><?php echo "Amount"; ?></label>
											<input type='number' min='0' max="<?php echo $ir['primary_currency']; ?>" class="form-control" name="cash" required="1" id="message-text">
										</div>
								</div>
								<div class="modal-footer">
									<?php
										echo"<input type='hidden' name='verf' value='{$code}' />";
									?>
									<button type="button" class="btn btn-primary" data-dismiss="modal"><?php echo "Close Window"; ?></button>
									<input type="submit" value="<?php echo "Send Cash"; ?>" id="sendcash" class="btn btn-primary">
									</form>
								</div>
							</div>
						</div>
					</div>
				  </div>
				  <div id="financial" class="tab-pane">
					<?php
						echo
						"
						<table class='table table-bordered'>
							<tr>
								<th width='25%'>Primary Currency</th>
								<td> " . number_format($r['primary_currency']) . "</td>
							</tr>
							<tr>
								<th>Secondary Currency</th>
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
					if (!in_array($ir['user_level'], array('Member', 'NPC')))
					{
						$fg=json_decode(get_fg_cache("cache/{$r['lastip']}.json","{$r['lastip']}",65655),true);
						$log=$db->fetch_single($db->query("SELECT `log_text` FROM `logs` WHERE `log_user` = {$r['userid']} ORDER BY `log_id` DESC"));
						echo "<table class='table table-bordered'>
							<tr>
								<th width='33%'>Data</th>
								<th>Output</th>
							</tr>
							<tr>
								<td>Location</td>
								<td>{$fg['city']}, {$fg['state']}, {$fg['country']}, ({$fg['isocode']})</td>
							</tr>
							<tr>
								<td>Risk Level</td>
								<td>" . parse_risk($fg['risk_level']) . "</td>
							</tr>
							<tr>
								<td>Last Hit</td>
								<td>{$r['lastip']} (" . @gethostbyaddr($r['lastip']) . ")</td>
							</tr>
							<tr>
								<td>Last Login</td>
								<td>{$r['loginip']} (" . @gethostbyaddr($r['loginip']) . ")</td>
							</tr>
							<tr>
								<td>Sign Up</td>
								<td>{$r['registerip']} (" . @gethostbyaddr($r['registerip']) . ")</td>
							</tr>
							<tr>
								<td>
									Last Action
								</td>
								<td>
									{$log}
								</td>
							</tr>
							<tr>
								<td>
									Browser/OS
								</td>
								<td>
									{$r['browser']}/{$r['os']}
								</td>
							</tr>
					</table>
					<form action='staff/staff_punish.php?action=staffnotes' method='post'>
						Staff Notes
						<br />
						<textarea rows='7' class='form-control' name='staffnotes'>"
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
function parse_risk($risk_level)
{
	switch ($risk_level)
	{
		case 2:
			return "Spam";
		case 3:
			return "Open Public Proxy";
		case 4:
			return "Tor Node";
		case 5:
			return "Honeypot / Botnet / DDOS Attack";
		default:
			return "No Risk";
	}
}
$h->endpage();