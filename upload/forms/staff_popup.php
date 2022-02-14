<div class="modal fade" id="staff_popup" tabindex="-2" role="dialog" aria-labelledby="staff_popup" aria-hidden="true">
	<div class="modal-dialog modal-xl" role="document">
		<div class="modal-content">
			<div class="modal-header">
				<h5 class="modal-title" id="addShortcutLabel">Staff Info</h5>
				<button type="button" class="close" data-dismiss="modal" aria-label="Close">
					<span aria-hidden="true">&times;</span>
				</button>
			</div>
			<div class="modal-body">
                <?php
                $fedjail = ($r['fedjail']) ? "<a href='staff/staff_punish.php?action=unfedjail&user={$r['userid']}' 
                                                class='btn btn-primary btn-block'>Un-Fedjail</a>" : 
                                                "<a href='staff/staff_punish.php?action=fedjail&user={$r['userid']}' 
                                                    class='btn btn-primary btn-block'>Fedjail</a>";
                $mailban = ($r['mbTIME']) ? "<a href='staff/staff_punish.php?action=unmailban&user={$r['userid']}'
                                                class='btn btn-primary btn-block'>Un-mailban</a>" :
                                                "<a href='staff/staff_punish.php?action=mailban&user={$r['userid']}'
                                                    class='btn btn-primary btn-block'>Mailban</a>";
                $forumban = ($r['fb_time']) ? "<a href='staff/staff_punish.php?action=unforumban&user={$r['userid']}'
                                                class='btn btn-primary btn-block'>Un-forumban</a>" :
                                                "<a href='staff/staff_punish.php?action=forumban&user={$r['userid']}'
                                                    class='btn btn-primary btn-block'>Forumban</a>";
				$fg = json_decode(get_fg_cache($r['lastip'], 72), true);
				$log = $db->fetch_single($db->query("/*qc=on*/SELECT `log_text` FROM `logs` WHERE `log_user` = {$r['userid']} ORDER BY `log_id` DESC"));
				if (empty($log))
				    $log = "N/A";
				echo "
				<div class='container'>
					<div class='row'>
                        <div class='col-12 col-sm-6 col-lg-4 col-xl'>
                            <div class='row'>
                                <div class='col-12'>
                                    <small>Location</small>
                                </div>
                                <div class='col-12'>
                                    {$fg['state']}, {$fg['country']}
                                </div>
                            </div>
                        </div>
                        <div class='col-12 col-sm-6 col-lg-4 col-xl'>
                            <div class='row'>
                                <div class='col-12'>
                                    <small>Last IP</small>
                                </div>
                                <div class='col-12'>
                                   <a href='staff/staff_punish.php?action=ipsearch&ip={$r['lastip']}'>{$r['lastip']}</a>
                                </div>
                            </div>
                        </div>
                        <div class='col-12 col-sm-6 col-lg-4 col-xl'>
                            <div class='row'>
                                <div class='col-12'>
                                    <small>Login IP</small>
                                </div>
                                <div class='col-12'>
                                   <a href='staff/staff_punish.php?action=ipsearch&ip={$r['loginip']}'>{$r['loginip']}</a>
                                </div>
                            </div>
                        </div>
                        <div class='col-12 col-sm-6 col-lg-4 col-xl'>
                            <div class='row'>
                                <div class='col-12'>
                                    <small>Risk Level</small>
                                </div>
                                <div class='col-12'>
                                   " . parseFraudGuardRisk($fg['risk_level']) . "
                                </div>
                            </div>
                        </div>
                        <div class='col-12 col-sm-6 col-lg-4 col-xl'>
                            <div class='row'>
                                <div class='col-12'>
                                    <small>Registration IP</small>
                                </div>
                                <div class='col-12'>
                                   <a href='staff/staff_punish.php?action=ipsearch&ip={$r['registerip']}'>{$r['registerip']}</a>
                                </div>
                            </div>
                        </div>
                        <div class='col-12 col-lg-12'>
                            <div class='row'>
                                <div class='col-12'>
                                    <small>Last Log</small>
                                </div>
                                <div class='col-12'>
                                   {$log}
                                </div>
                            </div>
                        </div>
                    </div>
					<div class='row'>
						<div class='col-12 col-sm-6 col-lg'>
							{$fedjail}
                            <br />
						</div>
						<div class='col-12 col-sm-6 col-lg'>
							{$forumban}
                            <br />
						</div>
                        <div class='col-12 col-sm-6 col-lg'>
							{$mailban}
						    <br />
                        </div>
                        <div class='col-12 col-sm-6 col-lg'>
							<a href='staff/staff_punish.php?action=forumwarn&user={$r['userid']}' class='btn btn-primary btn-block'>Forum Warn</a>
						    <br />
                        </div>
					</div>
					<form action='staff/staff_punish.php?action=staffnotes' method='post'>
					<div class='row'>
						<div class='col-12'>
							<b>Staff Notes</b>
						</div>
                        <div class='col'>
							<textarea class='form-control' name='staffnotes'>" . htmlentities($r['staff_notes'], ENT_QUOTES, 'ISO-8859-1') . "</textarea>
						</div>
                        <div class='col-12'>
							<input type='hidden' name='ID' value='{$_GET['user']}' /><br />
							<input type='submit' class='btn btn-primary btn-block' value='Update Notes' />
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