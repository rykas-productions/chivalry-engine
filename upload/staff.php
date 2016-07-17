<?php
require("globals.php");
$staff = array();
$q =
        $db->query(
                "SELECT `userid`, `laston`, `username`, `user_level`
 				 FROM `users`
 				 WHERE `user_level` IN('Admin', 'Forum Moderator', 'Assistant')
 				 ORDER BY `userid` ASC");
while ($r = $db->fetch_row($q))
{
    $staff[$r['userid']] = $r;
}
$db->free_result($q);
echo "<h3>Admins</h3>
<br />
<table class='table table-bordered table-hober'>
<thead>
	<th>Username [ID]</th>
	<th>Last Seen</th>
	<th>Online?</th>
	<th>Contact</th>
</thead>
<tbody>";
foreach ($staff as $r)
{
    if ($r['user_level'] == 'Admin')
    {
        $on =
                ($r['laston'] >= ($_SERVER['REQUEST_TIME'] - 900))
                        ? '<span style="color: green; font-weight:bold;">Online</span>'
                        : '<span style="color: green; font-weight:bold;">Offline</span>';
		echo "<tr>
				<td>
					<a href='profile.php?user={$r['userid']}'>{$r['username']}</a> [{$r['userid']}]
				</td>
				<td>
					" . date("F j, Y, g:i:s a", $r['laston']) . "
				</td>
				<td>
					{$on}
				</td>
				<td>
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
				</td>";
    }
}
echo '</table>';
echo "<h3>Assistants</h3>
<br />
<table class='table table-bordered table-hober'>
<thead>
	<th>Username [ID]</th>
	<th>Last Seen</th>
	<th>Online?</th>
	<th>Contact</th>
</thead>
<tbody>";
foreach ($staff as $r)
{
    if ($r['user_level'] == 'Assistant')
    {
        $on =
                ($r['laston'] >= ($_SERVER['REQUEST_TIME'] - 900))
                        ? '<span style="color: green; font-weight:bold;">Online</span>'
                        : '<span style="color: green; font-weight:bold;">Offline</span>';
		echo "<tr>
				<td>
					<a href='profile.php?user={$r['userid']}'>{$r['username']}</a> [{$r['userid']}]
				</td>
				<td>
					" . date("F j, Y, g:i:s a", $r['laston']) . "
				</td>
				<td>
					{$on}
				</td>
				<td>
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
				echo"</td>";
    }
}
echo '</table>';
echo "<h3>Forum Moderators</h3>
<br />
<table class='table table-bordered table-hober'>
<thead>
	<th>Username [ID]</th>
	<th>Last Seen</th>
	<th>Online?</th>
	<th>Contact</th>
</thead>
<tbody>";
foreach ($staff as $r)
{
    if ($r['user_level'] == 'Forum Moderator')
    {
        $on =
                ($r['laston'] >= ($_SERVER['REQUEST_TIME'] - 900))
                        ? '<span style="color: green; font-weight:bold;">Online</span>'
                        : '<span style="color: green; font-weight:bold;">Offline</span>';
		echo "<tr>
				<td>
					<a href='profile.php?user={$r['userid']}'>{$r['username']}</a> [{$r['userid']}]
				</td>
				<td>
					" . date("F j, Y, g:i:s a", $r['laston']) . "
				</td>
				<td>
					{$on}
				</td>
				<td>
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
				</td>";
    }
}
echo '</table>';




$h->endpage();