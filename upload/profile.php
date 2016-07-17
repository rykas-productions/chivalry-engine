<?php
require("globals.php");
$code = request_csrf_code('inbox_send');
$_GET['user'] =
        (isset($_GET['user']) && is_numeric($_GET['user'])) ? abs(intval($_GET['user']))
                : '';
if (!$_GET['user'])
{
   alert("danger","Invalid Use","You must enter a User's ID to view their profile.");
}
else
{
	$q =
            $db->query(
                    "SELECT `userid`, `user_level`, `laston`, `last_login`,
                    `registertime`, `vip_days`, `username`, `gender`,
					`primary_currency`, `secondary_currency`, `level`, `class`,
					`display_pic`, `hp`, `maxhp`, `guild`,
                    `fedjail`, `bank`, `lastip`, `lastip`,
                    `loginip`, `registerip`, `staff_notes`, `town_name`,
                    `house_name`, `guild_name`, `fed_days`, `fed_reason`,
					`infirmary_reason`, `infirmary_out`, `dungeon_reason`, `dungeon_out`
                    FROM `users` `u`
                    INNER JOIN `town` AS `t`
                    ON `u`.`location` = `t`.`town_id`
					INNER JOIN `infirmary` AS `i`
					ON `u`.`userid` = `i`.`infirmary_user`
					INNER JOIN `dungeon` AS `d`
					ON `u`.`userid` = `d`.`dungeon_user`
                    INNER JOIN `estates` AS `e`
                    ON `u`.`maxwill` = e.`house_will`
                    LEFT JOIN `guild` AS `g`
                    ON `g`.`guild_id` = `u`.`guild`
                    LEFT JOIN `fedjail` AS `f`
                    ON `f`.`fed_userid` = `u`.`userid`
                    WHERE `u`.`userid` = {$_GET['user']}");
					
	if ($db->num_rows($q) == 0)
	{
		$db->free_result($q);
		alert("danger","Uh oh!","We could not find a user with the User ID you entered. You could be receiving this message because the player you are trying to view got deleted. Check your source again!");
	}
	else
    {
		$r = $db->fetch_row($q);
        $db->free_result($q);
		$lon =
                ($r['laston'] > 0) ? date('F j, Y g:i:s a', $r['laston'])
                        : "Never";
        $ula = ($r['laston'] == 0) ? 'Never' : DateTime_Parse($r['laston']);
        $ull =
                ($r['last_login'] == 0) ? 'Never'
                        : DateTime_Parse($r['last_login']);
        $sup = date('F j, Y g:i:s a', $r['registertime']);
		$user_name = ($r['vip_days']) ? "<span style='color:red; font-weight:bold;'>{$r['username']}</span> <span class='glyphicon glyphicon-star' data-toggle='tooltip' title='{$r['username']} has {$r['vip_days']} VIP Days remaining.'></span>" : $r['username'];
        $on =
                ($r['laston'] >= $_SERVER['REQUEST_TIME'] - 15 * 60)
                        ? '<font color="green"><b>Online</b></font>'
                        : '<font color="red"><b>Offline</b></font>';
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
		$r['daysold']=round((($CurrentTime-$r['registertime'])/(3600 * 24)));
		echo "<h3>Profile for {$r['username']}</h3>"; ?>
<div class="row">
	<div class="col-sm-4">
		<div class="panel panel-default">
			<div class="panel-heading">General Information</div>
			<?php echo "
			<div class='panel-body'>
				<b>Name:</b> {$user_name}<br />
				<b>User Level:</b> {$r['user_level']}<br />
				<b>Gender:</b> {$r['gender']}<br />
				<b>Class:</b> {$r['class']}<br />
				<hr />
				<b>Signed Up:</b> {$sup}<br />
                <b>Last Active:</b> {$lon}<br />
               <b>Last Action:</b> {$ula}<br />
                <b>Last Login:</b> {$ull}<br />
				<hr />
				<b>Online:</b> {$on}<br />
               <b>Days Old:</b> {$r['daysold']}<br />
                <b>Location:</b> {$r['town_name']}
			</div>
			";
			?>
		</div>
	</div>
	<div class="col-sm-4">
		<div class="panel panel-default">
			<div class="panel-heading">Financial Information</div>
			<?php echo "
			<div class='panel-body'>
				<b>Primary Currency:</b> " . number_format($r['primary_currency']) . "<br />
				<b>Secondary Currency:</b> " . number_format($r['secondary_currency']) . "<br />
				<b>Property:</b> {$r['house_name']}<br />
				<hr />
				<b>Referrals:</b> {$ref}<br />
                <b>Friends:</b> " . number_format($friend) . "<br />
               <b>Enemies:</b> " . number_format($enemy) . "
			</div>
			";
			?>
		</div>
	</div>
	<div class="col-sm-4">
		<div class="panel panel-default">
			<div class="panel-heading">Display Picture</div>
			<?php 
				echo ($r['display_pic']) ? "<img src='{$r['display_pic']}' class='img-thumbnail img-responsive' width='250' height='250'  alt='The display picture of {$r['username']}' title='The display picture of {$r['username']}' />" : 'No Image';
				if (in_array($ir['user_level'], array('Member', 'NPC')))
				{
					$sh="&nbsp;";
				}
				else
				{
					$sh="<a data-toggle='collapse' href='#staffinfo'>Staff Info</a>";
				}
			?>
		</div>
	</div>
</div>
<div class="row">
	<div class="col-sm-4">
		<div class="panel panel-default">
			<div class="panel-heading">Physical Information</div>
			<?php echo "
			<div class='panel-body'>
				<b>Level:</b> {$r['level']}<br />
				<b>Health:</b> {$r['hp']}/{$r['maxhp']}<br />";
				echo ($r['guild']) ? "<b>Guild:</b> <a href='#'>{$r['guild_name']}</a>" : '<b>Guild:</b> N/A';
				if ($r['fedjail'])
				{
					echo "<br /><span style='font-weight: bold; color: red;'>
					In federal jail for {$r['fed_days']} day(s).
					<br />
					{$r['fed_reason']}
					</span>";
				}
				if (user_infirmary($r['userid']))
				{
					$InfirmaryRemain=round((($r['infirmary_out'] - $CurrentTime) / 60), 2);
					echo "
					<br />
					<span style='font-weight: bold; color: red;'>
					In the infirmary for {$InfirmaryRemain} minutes.
					<br />
					{$r['infirmary_reason']}
					</span>";
				}
				if (user_dungeon($r['userid']))
				{
					$DungeonRemain=round((($r['dungeon_out'] - $CurrentTime) / 60), 2);
					echo "
					<br />
					<span style='font-weight: bold; color: red;'>
					In the dungeon for {$DungeonRemain} minutes.
					<br />
					{$r['dungeon_reason']}
					</span>";
				}
			?>
		</div>
	</div>
</div>
	<div class="col-sm-4">
		<div class="panel panel-default">
			<div class="panel-heading">Links</div>
			<?php echo "
			<div class='panel-body'>
				<button type='button' class='btn btn-primary' data-toggle='modal' data-target='#exampleModal' data-whatever='Admin'>Send {$r['username']} a Message</button>";
					?>
					<div class="modal fade" id="exampleModal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel">
					  <div class="modal-dialog" role="document">
						<div class="modal-content">
						  <div class="modal-header">
							<button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
							<div id="success"></div>
							<h4 class="modal-title" id="ModalLabel">New message</h4>
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
					<?php
					echo"
					<br /><br />
					[<a href='sendcash.php?ID={$r['userid']}'>Send {$r['username']} Cash</a>]
					<br /><br />
					[<a href='attack.php?user={$r['userid']}'>Attack {$r['username']}</a>]
					<br /><br />
					[<a href='contactlist.php?action=add&ID={$r['userid']}'>Add {$r['username']} to Contacts</a>]";
					if (!in_array($ir['user_level'], array('Member', 'NPC')))
					{
						echo "
					<br /><br />
					[<a href='jailuser.php?userid={$r['userid']}'>Fed Jail {$r['username']}</a>]
					<br /><br />
					[<a href='mailban.php?userid={$r['userid']}'>Mail Ban {$r['username']}</a>]
					   ";
					}
					if ($ir['vip_days'] > 0)
					{
						echo "
					<br /><br />
					[<a href='friendslist.php?action=add&ID={$r['userid']}'>Add {$r['username']} to Friends List</a>]
					<br /><br />
					[<a href='blacklist.php?action=add&ID={$r['userid']}'>Add {$r['username']} to Enemies List</a>]
					<br />
					   ";
					}
					echo"
			</div>
			";
			?>
		</div>
	</div>
	<?php
	if (!in_array($ir['user_level'], array('Member', 'NPC')))
	{
		?>
	<div class="col-sm-4">
		<div class="panel-group">
			<div class="panel panel-default">
				<div class="panel-heading"><?php echo $sh; ?></div>
				<div id="staffinfo" class="panel-collapse collapse">
			<?php echo "
			<div class='panel-body'>";
				
					$curl = curl_init();

					curl_setopt_array($curl, array(
						CURLOPT_URL => "https://api.fraudguard.io/ip/$r[loginip]",
						CURLOPT_USERPWD => "{$set['FGUsername']}:{$set['FGPassword']}",
						CURLOPT_SSL_VERIFYPEER => false,
						CURLOPT_RETURNTRANSFER => true
					));

					$resp = curl_exec($curl);
					curl_close($curl);
					$fg=json_decode($resp,true);
					echo "
					<h3>Internet Info</h3>
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
					   ";

			echo"</div>
			";
			?>
				</div>
			</div>
		</div>
		</div>
		</div>
<?php
		}
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