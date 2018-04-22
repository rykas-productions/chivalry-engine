<?php
//If file is loaded directly.
if (strpos($_SERVER['PHP_SELF'], 'userinfo.php') !== false) {
    exit;
}
$energy = $api->UserInfoGet($userid, 'energy', true);
$brave = $api->UserInfoGet($userid, 'brave', true);
$will = $api->UserInfoGet($userid, 'will', true);
$xp = round($ir['xp'] / $ir['xp_needed'] * 100);
$hp = $api->UserInfoGet($userid, 'hp', true);
$StrengthRank = get_rank($ir['strength'], 'strength');
$AgilityRank = get_rank($ir['agility'], 'agility');
$GuardRank = get_rank($ir['guard'], 'guard');
$IQRank = get_rank($ir['iq'], 'iq');
$LaborRank = get_rank($ir['labor'], 'labor');
$AllStatRank = get_rank($ir['strength'] + $ir['agility'] + $ir['guard'] + $ir['labor'] + $ir['iq'], 'all');
$ir['total_stats'] = $ir['strength'] + $ir['agility'] + $ir['guard'] + $ir['labor'] + $ir['iq'];
echo "
<div class='modal fade' id='userInfo' tabindex='-1' role='dialog' aria-labelledby='User Info' aria-hidden='true'>
  <div class='modal-dialog' role='document'>
    <div class='modal-content'>
      <div class='modal-header'>
        <h5 class='modal-title' id='User Info'>Your Info</h5>
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
                    <div class='progress-bar bg-success' role='progressbar' aria-valuenow='{$ir['energy']}' style='width:{$energy}%' aria-valuemin='0' aria-valuemax='{$ir['maxenergy']}'></div>
                    <span>{$energy}% ({$ir['energy']} / {$ir['maxenergy']})</span>
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
                    <div class='progress-bar bg-success' role='progressbar' aria-valuenow='{$ir['brave']}' style='width:{$brave}%' aria-valuemin='0' aria-valuemax='{$ir['maxbrave']}'></div>
                    <span>{$brave}% ({$ir['brave']} / {$ir['maxbrave']})</span>
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
                    <div class='progress-bar bg-success' role='progressbar' aria-valuenow='{$ir['will']}' style='width:{$will}%' aria-valuemin='0' aria-valuemax='{$ir['maxwill']}'></div>
                    <span>{$will}% ({$ir['will']} / {$ir['maxwill']})</span>
                </div>
            </div>
        </div>
        <div class='row'>
            <div class='col-8' align='left'>
                XP {$xp}%
            </div>
        </div>
        <div class='progress' style='height: 1rem;'>
            <div class='progress-bar bg-warning' role='progressbar' aria-valuenow='{$ir['xp']}' style='width:{$xp}%' aria-valuemin='0' aria-valuemax='{$ir['xp_needed']}'></div>
            <span>{$xp}% ({$ir['xp']} / {$ir['xp_needed']})</span>
        </div>
        <div class='row'>
            <div class='col-8' align='left'>
                HP {$hp}%
            </div>
        </div>
        <div class='progress' style='height: 1rem;'>
            <div class='progress-bar bg-success' role='progressbar' aria-valuenow='{$ir['hp']}' style='width:{$hp}%' aria-valuemin='0' aria-valuemax='{$ir['maxhp']}'></div>
            <span>{$hp}% ({$ir['hp']} / {$ir['maxhp']})</span>
        </div>
        <hr />
        <div class='container-fluid'>
            <div class='row'>
                Copper Coins: 
                " . number_format($ir['primary_currency']) . " [<a href='allbank.php'>Bank</a>]
            </div>
        </div>
        <div class='container-fluid'>
            <div class='row'>
                Chivalry Tokens:
                " . number_format($ir['secondary_currency']) . "
            </div>
        </div>
        <hr />
        <div class='row'>
                <div class='col-8' align='left'>
                    Level: {$ir['level']}
                </div>
            </div>
            
        <div class='container-fluid'>
            <div class='row'>
                VIP Days: 
                " . number_format($ir['vip_days']) . "
            </div>
        </div>
        <div class='container-fluid'>
            <div class='row'>
                Kills/Deaths: 
                {$ir['kills']} / {$ir['deaths']}
            </div>
        </div>
        <div class='container-fluid'>
            <div class='row'>
                Busts: 
                {$ir['busts']}
            </div>
        </div>
        <div class='container-fluid'>
            <div class='row'>
                [<a href='skills.php'>View Skills</a>]
            </div>
        </div>
        <div class='container-fluid'>
            <div class='row'>
                [<a href='notepad.php'>View Notes</a>]
            </div>
        </div>
        <hr />
        <div class='container-fluid'>
            <div class='row'>
                Strength: 
                " . number_format($ir['strength']) . " (Ranked: {$StrengthRank})
            </div>
        </div>
        <div class='container-fluid'>
            <div class='row'>
                Agility: 
                " . number_format($ir['agility']) . " (Ranked: {$AgilityRank})
            </div>
        </div>
        <div class='container-fluid'>
            <div class='row'>
                Guard: 
                " . number_format($ir['guard']) . " (Ranked: {$GuardRank})
            </div>
        </div>
        <div class='container-fluid'>
            <div class='row'>
                Labor: 
                " . number_format($ir['labor']) . " (Ranked: {$LaborRank})
            </div>
        </div>
        <div class='container-fluid'>
            <div class='row'>
                IQ: 
                " . number_format($ir['iq']) . " (Ranked: {$IQRank})
            </div>
        </div>
        <div class='container-fluid'>
            <div class='row'>
                Total Stats: 
                " . number_format($ir['total_stats']) . " (Ranked: {$AllStatRank}) 
            </div>
        </div>
        <div class='container-fluid'>
            <div class='row'>
                Luck
                " . number_format($ir['luck']) . "%
            </div>
        </div>
      </div>
    </div>
  </div>
</div>";