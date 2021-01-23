<?php
//If file is loaded directly.
if (strpos($_SERVER['PHP_SELF'], 'userinfo.php') !== false) {
    exit;
}
if (!isset($MUS))
	$MUS = ($db->fetch_row($db->query("/*qc=on*/SELECT * FROM `mining` WHERE `userid` = {$userid} LIMIT 1")));
$energy = $api->UserInfoGet($userid, 'energy', true);
$brave = $api->UserInfoGet($userid, 'brave', true);
$will = $api->UserInfoGet($userid, 'will', true);
$xp = round($ir['xp'] / $ir['xp_needed'] * 100);
$hp = $api->UserInfoGet($userid, 'hp', true);
$mine = round($MUS['miningpower'] / $MUS['max_miningpower'] * 100);
$StrengthRank = get_rank($ir['strength'], 'strength');
$AgilityRank = get_rank($ir['agility'], 'agility');
$GuardRank = get_rank($ir['guard'], 'guard');
$IQRank = get_rank($ir['iq'], 'iq');
$LaborRank = get_rank($ir['labor'], 'labor');
$AllStatRank = get_rank($ir['strength'] + $ir['agility'] + $ir['guard'] + $ir['labor'] + $ir['iq'], 'all');
$ir['total_stats'] = $ir['strength'] + $ir['agility'] + $ir['guard'] + $ir['labor'] + $ir['iq'];
$actualReset = $ir['reset'] - 1;
$resetGains=0;
if ($actualReset == 1)
	$resetGains=50;
elseif ($actualReset == 2)
	$resetGains=75;
elseif ($actualReset == 3)
	$resetGains=87.5;
elseif ($actualReset == 4)
	$resetGains=93.75;
elseif ($actualReset == 5)
	$resetGains=96.88;
echo "
<div class='modal fade' id='userInfo' tabindex='-1' role='dialog' aria-labelledby='User Info' aria-hidden='true'>
  <div class='modal-dialog modal-lg' role='document'>
    <div class='modal-content'>
      <div class='modal-header'>
        <h5 class='modal-title' id='User Info'>Your Info [<a href='index.php'>Home</a>]</h5>
        <button type='button' class='close' data-dismiss='modal' aria-label='Close'>
          <span aria-hidden='true'>&times;</span>
        </button>
      </div>
      <div class='modal-body'>
        <div class='row'>
            <div class='col-8' align='left'>
                Energy {$energy}%
            </div>
            <div class='col-4'>
                <a href='temple.php?action=energy'><i class='fas fa-sync'></i></a>
            </div>
        </div>
        <div class='row'>
            <div class='col-12'>
                <div class='progress' style='height: 1rem;'>
                    <div class='progress-bar bg-success progress-bar-striped progress-bar-animated' role='progressbar' aria-valuenow='{$ir['energy']}' style='width:{$energy}%' aria-valuemin='0' aria-valuemax='{$ir['maxenergy']}'>
						<span>
							{$energy}% (" . number_format($ir['energy']) . " / " . number_format($ir['maxenergy']) .")
						</span>
					</div>
                </div>
            </div>
        </div>
        <div class='row'>
            <div class='col-8' align='left'>
                Brave {$brave}%
            </div>
            <div class='col-4'>
                <a href='temple.php?action=brave'><i class='fas fa-sync'></i></a>
            </div>
        </div>
        <div class='row'>
            <div class='col-12'>
                <div class='progress' style='height: 1rem;'>
                    <div class='progress-bar bg-secondary progress-bar-striped progress-bar-animated' role='progressbar' aria-valuenow='{$ir['brave']}' style='width:{$brave}%' aria-valuemin='0' aria-valuemax='{$ir['maxbrave']}'>
						<span>
							{$brave}% (" . number_format($ir['brave']) . " / " . number_format($ir['maxbrave']) . ")
						</span>
					</div>
                </div>
            </div>
        </div>
            <div class='row'>
                <div class='col-8' align='left'>
                    Will {$will}%
                </div>
                <div class='col-4'>
                    <a href='temple.php?action=will'><i class='fas fa-sync'></i></a>
                </div>
            </div>
        <div class='row'>
            <div class='col-12'>
                <div class='progress' style='height: 1rem;'>
                    <div class='progress-bar bg-info progress-bar-striped progress-bar-animated' role='progressbar' aria-valuenow='{$ir['will']}' style='width:{$will}%' aria-valuemin='0' aria-valuemax='{$ir['maxwill']}'>
						<span>
							{$will}% (" . number_format($ir['will']) . " / " . number_format($ir['maxwill']). ")
						</span>
					</div>
                </div>
            </div>
        </div>
        <div class='row'>
            <div class='col-8' align='left'>
                XP {$xp}%
            </div>
        </div>
        <div class='progress' style='height: 1rem;'>
            <div class='progress-bar bg-warning progress-bar-striped progress-bar-animated' role='progressbar' aria-valuenow='{$ir['xp']}' style='width:{$xp}%' aria-valuemin='0' aria-valuemax='{$ir['xp_needed']}'>
				<span>
					{$xp}% (" . number_format($ir['xp']) . " / " . number_format($ir['xp_needed']) . ")
				</span>
			</div>
        </div>
        <div class='row'>
            <div class='col-8' align='left'>
                HP {$hp}%
            </div>
        </div>
        <div class='progress' style='height: 1rem;'>
            <div class='progress-bar bg-danger progress-bar-striped progress-bar-animated' role='progressbar' aria-valuenow='{$ir['hp']}' style='width:{$hp}%' aria-valuemin='0' aria-valuemax='{$ir['maxhp']}'>
				<span>
					{$hp}% (" . number_format($ir['hp']) . " / " . number_format($ir['maxhp']) . ")
				</span>
			</div>
        </div>
		<div class='row'>
            <div class='col-8' align='left'>
                Mining Energy {$mine}%
            </div>
        </div>
        <div class='progress' style='height: 1rem;'>
            <div class='progress-bar bg-success progress-bar-striped progress-bar-animated' role='progressbar' aria-valuenow='{$MUS['miningpower']}' style='width:{$mine}%' aria-valuemin='0' aria-valuemax='{$MUS['max_miningpower']}'>
				<span>
					{$mine}% (" . number_format($MUS['miningpower']) . " / " . number_format($MUS['max_miningpower']) . ")
				</span>
			</div>
        </div>
        <hr />
		<div class='row'>
			<div class='col-6'>
				<div class='row'>
					<div class='col-12'>
						<small><b>Copper Coins</b> [<a href='allbank.php'>Bank</a>]</small>
					</div>
					<div class='col-12'>
						<span id='ui_copper'>" . number_format($ir['primary_currency']) . "</span>
					</div>
				</div>  
			</div>
			<div class='col-6'>
				<div class='row'>
					<div class='col-12'>
						<small><b>Chivalry Tokens</b> [<a href='alltoken.php'>Bank</a>]</small>
					</div>
					<div class='col-12'>
						<span id='ui_token'>" . number_format($ir['secondary_currency']) . "</span>
					</div>
				</div>
			</div>
		</div>
        <hr />
		<div class='row'>
			<div class='col-4 col-sm-3 col-md-2'>
				<div class='row'>
					<div class='col-12'>
						<small><b>Level</b></small>
					</div>
					<div class='col-12'>
						 " . number_format($ir['level']) . "
					</div>
				</div>
			</div>
			<div class='col-6 col-sm-4'>
				<div class='row'>
					<div class='col-12'>
						<small><b>Prestiege</b> (-{$resetGains}% XP)</small>
					</div>
					<div class='col-12'>
						 " . number_format($actualReset) . "
					</div>
				</div>
			</div>
			<div class='col-4 col-sm-3'>
				<div class='row'>
					<div class='col-12'>
						<small><b>VIP Days</b></small>
					</div>
					<div class='col-12'>
						 " . number_format($ir['vip_days']) . "
					</div>
				</div>
			</div>
			<div class='col-4 col-sm-3'>
				<div class='row'>
					<div class='col-12'>
						<small><b>KDR</b></small>
					</div>
					<div class='col-12'>
						 " . number_format($ir['kills']) . " / " . number_format($ir['deaths']) . "
					</div>
				</div>
			</div>
			<div class='col-4 col-sm-3 col-md-2'>
				<div class='row'>
					<div class='col-12'>
						<small><b>Busts</b></small>
					</div>
					<div class='col-12'>
						 " . number_format($ir['busts']) . "
					</div>
				</div>
			</div>
			<div class='col-4 col-sm-3'>
				<div class='row'>
					<div class='col-12'>
						<small><b>Location</b></small>
					</div>
					<div class='col-12'>
						 {$api->SystemTownIDtoName($ir['location'])}
					</div>
				</div>
			</div>
			<div class='col-4 col-sm-3'>
				<div class='row'>
					<div class='col-12'>
						<small><b>Skills</b></small>
					</div>
					<div class='col-12'>
						 [<a href='skills.php'>View Skills</a>]
					</div>
				</div>
			</div>
			<div class='col-4 col-sm-3'>
				<div class='row'>
					<div class='col-12'>
						<small><b>Notes</b></small>
					</div>
					<div class='col-12'>
						[<a href='notepad.php'>View Notes</a>]
					</div>
				</div>
			</div>
		</div>
        <hr />
		<div class='row'>
			<div class='col-6 col-sm-4 col-lg-3'>
				<div class='row'>
					<div class='col-12'>
						<small><b>Strength (Ranked: {$StrengthRank})</b></small>
					</div>
					<div class='col-12'>
						" . number_format($ir['strength']) . "
					</div>
				</div>
			</div>
			<div class='col-6 col-sm-4 col-lg-3'>
				<div class='row'>
					<div class='col-12'>
						<small><b>Agility (Ranked: {$AgilityRank})</b></small>
					</div>
					<div class='col-12'>
						" . number_format($ir['agility']) . "
					</div>
				</div>
			</div>
			<div class='col-6 col-sm-4 col-lg-3'>
				<div class='row'>
					<div class='col-12'>
						<small><b>Guard (Ranked: {$GuardRank})</b></small>
					</div>
					<div class='col-12'>
						" . number_format($ir['guard']) . "
					</div>
				</div>
			</div>
			<div class='col-6 col-sm-4 col-lg-3'>
				<div class='row'>
					<div class='col-12'>
						<small><b>IQ (Ranked: {$IQRank})</b></small>
					</div>
					<div class='col-12'>
						" . number_format($ir['iq']) . "
					</div>
				</div>
			</div>
			<div class='col-6 col-sm-4 col-lg-3'>
				<div class='row'>
					<div class='col-12'>
						<small><b>Labor (Ranked: {$LaborRank})</b></small>
					</div>
					<div class='col-12'>
						" . number_format($ir['labor']) . "
					</div>
				</div>
			</div>
			<div class='col-6 col-sm-4 col-lg-3'>
				<div class='row'>
					<div class='col-12'>
						<small><b>Total (Ranked: {$AllStatRank})</b></small>
					</div>
					<div class='col-12'>
						" . number_format($ir['total_stats']) . "
					</div>
				</div>
			</div>
			<div class='col-6 col-sm-4 col-lg-3'>
				<div class='row'>
					<div class='col-12'>
						<small><b>Luck</b></small>
					</div>
					<div class='col-12'>
						" . number_format($ir['luck']) . "%
					</div>
				</div>
			</div>
		</div>
      </div>
    </div>
  </div>
</div>";