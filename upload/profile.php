<?php
require("globals.php");
$code = request_csrf_code('inbox_send');
$code2 = request_csrf_code('cash_send');
$_GET['user'] = (isset($_GET['user']) && is_numeric($_GET['user'])) ? abs(intval($_GET['user'])) : '';
if (!$_GET['user'])
{
   alert("danger","Invalid Use","You must enter a User's ID to view their profile.");
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
                    `house_name`, `guild_name`, `fed_days`, `fed_reason`,
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
		alert("danger","{$lang['ERROR_NONUSER']}","{$lang['PROFILE_UNF']}");
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
		$user_name = ($r['vip_days']) ? "<span style='color:red; font-weight:bold;'>{$r['username']}</span> <span class='glyphicon glyphicon-star' data-toggle='tooltip' title='{$r['username']} has {$r['vip_days']} VIP Days remaining.'></span>" : $r['username'];
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
		$r['daysold']=DateTime_Parse($r['registertime'], false);
		
		$rhpperc = round($r['hp'] / $r['maxhp'] * 100);
		echo "<h3>{$lang['PROFILE_PROFOR']} {$r['username']}</h3>";
		?>
		
		<div class="row">
			<div class="col-lg-2">
				<?php
					echo "{$displaypic}<br />
						<h3><b>{$r['username']} [{$r['userid']}]</b></h3>
							{$r['user_level']}<br />
							
						{$lang['PROFILE_LOCATION']} {$r['town_name']}<br />
							{$lang['INDEX_LEVEL']}: {$r['level']}<br />";
						echo ($r['guild']) ? "{$lang['PROFILE_GUILD']}: <a href='guilds.php?action=view&id={$r['guild']}'>{$r['guild_name']}</a><br />" : '';
						echo "{$lang['INDEX_HP']}: {$r['hp']}/{$r['maxhp']}<br />";
				
				?>
			</div>
			<div class="col-lg-10">
				<ul class='nav nav-pills nav-tabs'>
					<li class='nav-item'>
						<a data-toggle="tab" class='nav-link' href="#info"><?php echo $lang['PROFILE_PI']; ?></a>
					</li>
					<li class='nav-item'>
						<a data-toggle="tab" class='nav-link' href="#actions"><?php echo $lang['PROFILE_ACTION']; ?></a>
					</li>
					<li class='nav-item'>
						<a data-toggle="tab" class='nav-link' href="#financial"><?php echo $lang['PROFILE_FINANCIAL']; ?></a>
					</li>
					<?php
					if (!in_array($ir['user_level'], array('Member', 'NPC')))
					{
					  echo "<li class='nav-item'><a data-toggle='tab' class='nav-link' href='#staff'>{$lang['PROFILE_STAFF']}</a></li>";
					}
				  ?>
				</ul>
				<br />
				<div class="tab-content">
				  <div id="info" class="tab-pane fade in active">
					<p>
						<?php
						echo
						"
						<div class='table-resposive'>
						<table class='table table-bordered'>
							<tr>
								<th width='25%'>{$lang['REG_SEX']}</th>
								<td>{$r['gender']}</td>
							</tr>
							<tr>
								<th>{$lang['INDEX_CLASS']}</th>
								<td>{$r['class']}</td>
							</tr>
							<tr>
								<th>{$lang['PROFILE_REGISTERED']}</th>
								<td>{$sup}</td>
							</tr>
							<tr>
								<th>{$lang['PROFILE_ACTIVE']}</th>
								<td>{$ula}</td>
							</tr>
							<tr>
								<th>{$lang['PROFILE_LOGIN']}</th>
								<td>{$ull}</td>
							</tr>
							<tr>
								<th>{$lang['PROFILE_AGE']}</th>
								<td>{$r['daysold']} {$lang['PROFILE_DAYS_OLD']}</td>
							</tr>";
						if (user_infirmary($r['userid']))
						{
							$InfirmaryRemain=round((($r['infirmary_out'] - $CurrentTime) / 60), 2);
							echo "
							<tr>
								<th>{$lang['EXPLORE_INFIRM']}</th>
								<td>{$lang['GEN_INDAH']} {$lang['EXPLORE_INFIRM']} {$lang['GEN_FOR']} {$InfirmaryRemain} {$lang["GEN_MINUTES"]}<br />
								{$r['infirmary_reason']}
								</td>
							</tr>";
						}
						if (user_dungeon($r['userid']))
						{
							$DungeonRemain=round((($r['dungeon_out'] - $CurrentTime) / 60), 2);
							echo "
							<tr>
								<th>{$lang['EXPLORE_DUNG']}</th>
								<td>{$lang['GEN_INDAH']} {$lang['EXPLORE_DUNG']} {$lang['GEN_FOR']} {$DungeonRemain} {$lang["GEN_MINUTES"]}<br />
								{$r['dungeon_reason']}
								</td>
							</tr>";
						}
						if ($r['fedjail'])
						{
							echo "<br /><span style='font-weight: bold; color: red;'>
							In federal jail for {$r['fed_days']} day(s).
							<br />
							{$r['fed_reason']}
							</span>";
						}
						
						echo"</table></div>";
						?>
					</p>
				  </div>
				  <div id="actions" class="tab-pane fade">
				  <?php echo "<button type='button' class='btn btn-default' data-toggle='modal' data-target='#message' data-whatever='Admin'>Send {$r['username']} a Message</button>"; ?>
				  <?php echo "<br /><br /><button type='button' class='btn btn-default' data-toggle='modal' data-target='#cash' data-whatever='Admin'>Send {$r['username']} {$lang['INDEX_PRIMCURR']}</button>
				  <br /><br /><form action='attack.php'>
					<input type='hidden' name='user' value='{$r['userid']}'>
					<input type='submit' class='btn btn-danger' value='Attack {$r['username']}'>
					</form><br />
					<form action='hirespy.php'>
						<input type='hidden' name='user' value='{$r['userid']}'>
						<input type='submit' class='btn btn-default' value='Spy on {$r['username']}'>
					</form>
					<br />
					<form action='poke.php'>
						<input type='hidden' name='user' value='{$r['userid']}'>
						<input type='submit' class='btn btn-default' value='Poke {$r['username']}'>
					</form>
				  "; ?>
					<div class="modal fade" id="message" tabindex="-1" role="dialog" aria-labelledby="Sending a Message">
					  <div class="modal-dialog" role="document">
						<div class="modal-content">
						  <div class="modal-header">
							<button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
							<div id="success"></div>
							<h4 class="modal-title" id="ModalLabel"><?php echo "Sending {$r['username']} a Message"; ?></h4>
						  </div>
						  <div class="modal-body">
							<form id="mailpopupForm" name="mailpopupForm" action="js/script/sendmail.php">
							  <div class="form-group">
								<div id="result"></div>
								<label for="recipient-name" class="control-label">Recipient:</label>
								<input type="text" class="form-control" name="sendto" required="1" value="<?php echo $r['username']; ?>" id="recipient-name">
							  </div>
							  <div class="form-group">
								<label for="message-text" class="control-label">Message:</label>
								<textarea class="form-control" name="msg" required="1" id="message-text"></textarea>
							  </div>
							
						  </div>
						  <div class="modal-footer">
						  <?php
						  echo"
							<input type='hidden' name='verf' value='{$code}' />";
							?>
							<button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
							<input type="submit" value="Send Message" id="sendmessage" class="btn btn-primary">
							</form>
						  </div>
						</div>
					  </div>
					</div>
					<div class="modal fade" id="cash" tabindex="-1" role="dialog" aria-labelledby="Sending Cash">
					  <div class="modal-dialog" role="document">
						<div class="modal-content">
						  <div class="modal-header">
							<button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
							<div id="success2"></div>
							<h4 class="modal-title" id="ModalLabel"><?php echo "Sending {$r['username']} {$lang['INDEX_PRIMCURR']}"; ?></h4>
						  </div>
						  <div class="modal-body">
							<form id="cashpopupForm" name="cashpopupForm" action="js/script/sendcash.php">
							  <div class="form-group">
								<div id="result2"></div>
								<input type="hidden" name="sendto" required="1" value="<?php echo $r['userid']; ?>" id="recipient-name">
							  </div>
							  <div class="form-group">
								<label for="message-text" class="control-label">Cash:</label>
								<input type='number' min='0' max="<?php echo $ir['primary_currency']; ?>" class="form-control" name="cash" required="1" id="message-text"></textarea>
							  </div>
							
						  </div>
						  <div class="modal-footer">
						  <?php
						  echo"
							<input type='hidden' name='verf' value='{$code2}' />";
							?>
							<button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
							<input type="submit" value="Send Cash" id="sendcash" class="btn btn-primary">
							</form>
						  </div>
						</div>
					  </div>
					</div>
				  </div>
				  <div id="financial" class="tab-pane fade">
					<?php
						echo
						"
						<table class='table table-bordered'>
							<tr>
								<th width='25%'>{$lang['INDEX_PRIMCURR']}</th>
								<td> " . number_format($r['primary_currency']) . "</td>
							</tr>
							<tr>
								<th>{$lang['INDEX_SECCURR']}</th>
								<td>" . number_format($r['secondary_currency']) . "</td>
							</tr>
							<tr>
								<th>{$lang['STAFF_USERS_EDIT_FORM_ESTATE']}</th>
								<td>{$r['house_name']}</td>
							</tr>
							<tr>
								<th>{$lang['PROFILE_REF']}</th>
								<td>" . number_format($ref) . "</td>
							</tr>
							<tr>
								<th>{$lang['PROFILE_FRI']}</th>
								<td>" . number_format($friend) . "</td>
							</tr>
							<tr>
								<th>{$lang['PROFILE_ENE']}</th>
								<td>" . number_format($enemy) . "</td>
							</tr>
						</table>";
						
						?>
				  </div>
				  <?php
				  echo '<div id="staff" class="tab-pane fade in">';
					if (!in_array($ir['user_level'], array('Member', 'NPC')))
					{
						$fg=json_decode(get_fg_cache("cache/{$r['lastip']}.json","{$r['lastip']}",24),true);
						echo "
						<div class='table-resposive'>
						<table class='table table-bordered'>
							<tr>
								<th width='33%'>Data</th>
								<th>Output</th>
							</tr>
							<tr>
								<td>Location</td>
								<td>{$fg['city']}, {$fg['state']}, {$fg['country']}, ({$fg['isocode']})</td>
							</tr>
							<tr>
								<td>Last Hit</td>
								<td>$r[lastip]</td>
							</tr>
							<tr>
								<td>Last Login</td>
								<td>$r[loginip]</td>
							</tr>
							<tr>
								<td>Signup</td>
								<td>$r[registerip]</td>
							</tr>
							<tr>
								<td>Threat?</td>
								<td>{$fg['threat']}</td>
							</tr>
							<tr>
								<td>Risk Level<br />
								<small>1 is low, 5 is high</small></td>
								<td>{$fg['risk_level']}</td>
							</tr>
							<tr>
								<td>
									Broswer/OS
								</td>
								<td>
									{$r['browser']} on {$r['os']}
								</td>
							</tr>
					</table>
					<form action='staffnotes.php' method='post'>
						Staff Notes:
						<br />
						<textarea rows='7' class='form-control' name='staffnotes'>"
							. htmlentities($r['staff_notes'], ENT_QUOTES, 'ISO-8859-1')
							. "</textarea>
						<br />
						<input type='hidden' name='ID' value='{$_GET['user']}' />
						<input type='submit' class='btn btn-default' value='Update Notes About {$r['username']}' />
					</form>
					</div>";
					}
					?>
				  
				  </div>
				</div>
			</div>
		</div>
		<?php
		}
}
function checkblank($in)
{
    if (!$in)
    {
        return "N/A";
    }
    return $in;
}
$h->endpage();