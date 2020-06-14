<div class="modal fade" id="staff_popup" tabindex="-2" role="dialog" aria-labelledby="staff_popup" aria-hidden="true">
	<div class="modal-dialog modal-lg" role="document">
		<div class="modal-content">
			<div class="modal-header">
				<h5 class="modal-title" id="addShortcutLabel">Staff Info</h5>
				<button type="button" class="close" data-dismiss="modal" aria-label="Close">
					<span aria-hidden="true">&times;</span>
				</button>
			</div>
			<div class="modal-body">
                <?php
				$fg = json_decode(get_fg_cache($_SERVER['DOCUMENT_ROOT'] . "/cache/{$r['lastip']}.json", $r['lastip'], 65655), true);
				$log = $db->fetch_single($db->query("/*qc=on*/SELECT `log_text` FROM `logs` WHERE `log_user` = {$r['userid']} ORDER BY `log_id` DESC"));
				echo "
				<div class='container'>
					<div class='row'>
						<div class='col-md'>
							<b>Location</b>
						</div>
						<div class='col-md'>
							{$fg['state']}, {$fg['country']}
						</div>
					</div>
					<hr />
					<div class='row'>
						<div class='col-md'>
							<b>Last Known IP</b>
						</div>
						<div class='col-md'>
							{$r['lastip']}
						</div>
					</div>
					<hr />
					<div class='row'>
						<div class='col-md'>
							<b>Last Login IP</b>
						</div>
						<div class='col-md'>
							{$r['loginip']}
						</div>
					</div>
					<hr />
					<div class='row'>
						<div class='col-md'>
							<b>Registration IP</b>
						</div>
						<div class='col-md'>
							{$r['registerip']}
						</div>
					</div>
					<hr />
					<div class='row'>
						<div class='col-md'>
							<b>Last Action</b>
						</div>
						<div class='col-md'>
							{$log}
						</div>
					</div>
					<hr />
					<div class='row'>
						<div class='col-md'>
							<a href='staff/staff_punish.php?action=fedjail&user={$r['userid']}' class='btn btn-primary'>Fedjail</a>
						</div>
						<div class='col-md'>
							<a href='staff/staff_punish.php?action=forumban&user={$r['userid']}' class='btn btn-primary'>Forum Ban</a>
						</div>
					</div>
					<form action='staff/staff_punish.php?action=staffnotes' method='post'>
					<div class='row'>
						<div class='col-md'>
							<b>Staff Notes</b>
						</div>
					</div>
					<div class='row'>
						<div class='col-md'>
							<textarea class='form-control' name='staffnotes'>" . htmlentities($r['staff_notes'], ENT_QUOTES, 'ISO-8859-1') . "</textarea>
						</div>
					</div>
					<div class='row'>
						<div class='col-md'>
							<input type='hidden' name='ID' value='{$_GET['user']}' />
							<input type='submit' class='btn btn-primary' value='Update Notes' />
						</div>
					</div>
					</form>
					</div>
				</div>";
				?>
            </div>
        </div>
    </div>
</div>