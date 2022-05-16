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
if ($actualReset >= 1)
	$resetGains=$actualReset * 10;
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
                Energy <span id='ui_energy_perc'>{$energy}%</span>
            </div>
            <div class='col-4'>
                <a href='temple.php?action=energy'><i class='fas fa-sync'></i></a>
            </div>
        </div>
        <div class='row'>
            <div class='col-12'>
                " . scaledColorProgressBar($ir['energy'], 0, $ir['maxenergy']) . "
            </div>
        </div>
        <div class='row'>
            <div class='col-8' align='left'>
                Brave <span id='ui_brave_perc'>{$brave}%</span>
            </div>
            <div class='col-4'>
                <a href='temple.php?action=brave'><i class='fas fa-sync'></i></a>
            </div>
        </div>
        <div class='row'>
            <div class='col-12'>
                " . scaledColorProgressBar($ir['brave'], 0, $ir['maxbrave']) . "
            </div>
        </div>
        <div class='row'>
            <div class='col-8' align='left'>
                Will <span id='ui_will_perc'>{$will}%</span>
            </div>
            <div class='col-4'>
                <a href='temple.php?action=will'><i class='fas fa-sync'></i></a>
            </div>
        </div>
        <div class='row'>
            <div class='col-12'>
                " . scaledColorProgressBar($ir['will'], 0, $ir['maxwill']) . "
            </div>
        </div>
        <div class='row'>
            <div class='col-8' align='left'>
                XP <span id='ui_xp_perc'>{$xp}%</span>
            </div>
        </div>
        <div class='row'>
            <div class='col-12'>
                " . warningProgressBar($ir['xp'], 0, $ir['xp_needed']) . "
            </div>
        </div>
        <div class='row'>
            <div class='col-8' align='left'>
                HP <span id='ui_hp_perc'>{$hp}%</span>
            </div>
        </div>
        <div class='row'>
            <div class='col-12'>
                " . scaledColorProgressBar($ir['hp'], 0, $ir['maxhp']) . "
            </div>
        </div>
		<div class='row'>
            <div class='col-8' align='left'>
                Mining Energy {$mine}%
            </div>
        </div>
        <div class='row'>
            <div class='col-12'>
                " . scaledColorProgressBar($MUS['miningpower'], 0, $MUS['max_miningpower']) . "
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
						<span id='ui_copper'>" . shortNumberParse($ir['primary_currency']) . "</span>
					</div>
				</div>  
			</div>
			<div class='col-6'>
				<div class='row'>
					<div class='col-12'>
						<small><b>Chivalry Tokens</b> [<a href='alltoken.php'>Bank</a>]</small>
					</div>
					<div class='col-12'>
						<span id='ui_token'>" . shortNumberParse($ir['secondary_currency']) . "</span>
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
						 " . shortNumberParse($ir['level']) . "
					</div>
				</div>
			</div>
			<div class='col-6 col-sm-4'>
				<div class='row'>
					<div class='col-12'>
						<small><b>Mastery Rank</b> (-{$resetGains}% XP)</small>
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
						 " . shortNumberParse($ir['vip_days']) . "
					</div>
				</div>
			</div>
			<div class='col-4 col-sm-3'>
				<div class='row'>
					<div class='col-12'>
						<small><b>KDR</b></small>
					</div>
					<div class='col-12'>
						 " . shortNumberParse($ir['kills']) . " / " . shortNumberParse($ir['deaths']) . "
					</div>
				</div>
			</div>
			<div class='col-4 col-sm-3 col-md-2'>
				<div class='row'>
					<div class='col-12'>
						<small><b>Busts</b></small>
					</div>
					<div class='col-12'>
						 " . shortNumberParse($ir['busts']) . "
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
						" . shortNumberParse($ir['strength']) . "
					</div>
				</div>
			</div>
			<div class='col-6 col-sm-4 col-lg-3'>
				<div class='row'>
					<div class='col-12'>
						<small><b>Agility (Ranked: {$AgilityRank})</b></small>
					</div>
					<div class='col-12'>
						" . shortNumberParse($ir['agility']) . "
					</div>
				</div>
			</div>
			<div class='col-6 col-sm-4 col-lg-3'>
				<div class='row'>
					<div class='col-12'>
						<small><b>Guard (Ranked: {$GuardRank})</b></small>
					</div>
					<div class='col-12'>
						" . shortNumberParse($ir['guard']) . "
					</div>
				</div>
			</div>
			<div class='col-6 col-sm-4 col-lg-3'>
				<div class='row'>
					<div class='col-12'>
						<small><b>IQ (Ranked: {$IQRank})</b></small>
					</div>
					<div class='col-12'>
						" . shortNumberParse($ir['iq']) . "
					</div>
				</div>
			</div>
			<div class='col-6 col-sm-4 col-lg-3'>
				<div class='row'>
					<div class='col-12'>
						<small><b>Labor (Ranked: {$LaborRank})</b></small>
					</div>
					<div class='col-12'>
						" . shortNumberParse($ir['labor']) . "
					</div>
				</div>
			</div>
			<div class='col-6 col-sm-4 col-lg-3'>
				<div class='row'>
					<div class='col-12'>
						<small><b>Total (Ranked: {$AllStatRank})</b></small>
					</div>
					<div class='col-12'>
						" . shortNumberParse($ir['total_stats']) . "
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
            <div class='col-6 col-sm-4 col-lg-3'>
				<div class='row'>
					<div class='col-12'>
						<small><b>Status Effects</b></small>
					</div>
					<div class='col-12'>
						[<a href='status.php'>View</a>]
					</div>
				</div>
			</div>
		</div>
      </div>
    </div>
  </div>
</div>";