<?php
/*	File:		global_func_style.php
	Created: 	Aug 2, 2021; 7:36:30 PM
	Info: 		
	Author:		MasterGeneral156
	Website: 	https://chivalryisdeadgame.com/
*/

function createProgressBar($barValue, $barMin = 0, $barMax = 100, $barType = 'primary', $hideBonus = false)
{
    if ($barMax == 0)
        $barMax = 1;
    $percent = round($barValue / $barMax * 100);
    $txt = ($hideBonus) ? "{$percent}%" : "{$percent}% (" . number_format($barValue) . "/" . number_format($barMax). ")";
    return "<div class='progress' style='height: 1rem;'>
				<div class='progress-bar bg-{$barType} progress-bar-striped progress-bar-animated' role='progressbar' aria-valuenow='{$barValue}' style='width:{$percent}%' aria-valuemin='0' aria-valuemax='{$barMax}'>
					<span>
						{$txt}
					</span>
				</div>
			</div>";
}

function successProgressBar($barValue, $barMin = 0, $barMax = 100, $hideBonus = false)
{
    return createProgressBar($barValue, $barMin, $barMax, 'success', $hideBonus);
}

function dangerProgressBar($barValue, $barMin = 0, $barMax = 100, $hideBonus = false)
{
    return createProgressBar($barValue, $barMin, $barMax, 'danger', $hideBonus);
}

function warningProgressBar($barValue, $barMin = 0, $barMax = 100, $hideBonus = false)
{
    return createProgressBar($barValue, $barMin, $barMax, 'warning', $hideBonus);
}

function infoProgressBar($barValue, $barMin = 0, $barMax = 100, $hideBonus = false)
{
    return createProgressBar($barValue, $barMin, $barMax, 'info', $hideBonus);
}

function secondaryProgressBar($barValue, $barMin = 0, $barMax = 100, $hideBonus = false)
{
    return createProgressBar($barValue, $barMin, $barMax, 'secondary', $hideBonus);
}

function lightProgressBar($barValue, $barMin = 0, $barMax = 100, $hideBonus = false)
{
    return createProgressBar($barValue, $barMin, $barMax, 'light', $hideBonus);
}

function darkProgressBar($barValue, $barMin = 0, $barMax = 100, $hideBonus = false)
{
    return createProgressBar($barValue, $barMin, $barMax, 'dark', $hideBonus);
}

function scaledColorProgressBar($barValue, $barMin = 0, $barMax = 100, $hideBonus = false)
{
    if ($barMax == 0)
        $barMax = 1;
    $percent = round($barValue / $barMax * 100);
    if ($percent <= 33)
        return dangerProgressBar($barValue, $barMin, $barMax, $hideBonus);
    elseif (($percent > 33) && ($percent <= 66))
        return warningProgressBar($barValue, $barMin, $barMax, $hideBonus);
    else
        return successProgressBar($barValue, $barMin, $barMax, $hideBonus);
}

function loadGamblingAlert()
{
    global $ir;
    alert('info',"","You have won " . shortNumberParse($ir['winnings_this_hour']) . "/" . shortNumberParse(calculateUserMaxBetReset($ir['userid'])) . " Copper Coins today.", false);
}

function createBadge($text, $theme = 'primary')
{
    return "<span class='badge badge-{$theme}'>{$text}</span>";
}

function createDangerBadge($text)
{
    return createBadge($text, 'danger');
}

function createPrimaryBadge($text)
{
    return createBadge($text);
}

function createSecondaryBadge($text)
{
    return createBadge($text, 'secondary');
}

function createWarningBadge($text)
{
    return createBadge($text, 'warning');
}

function createSuccessBadge($text)
{
    return createBadge($text, 'success');
}

function createInfoBadge($text)
{
    return createBadge($text, 'info');
}

function createRandomBadge($text)
{
    $rand = Random(1,6);
    if ($rand == 1)
        return createDangerBadge($text);
    elseif ($rand == 2)
        return createPrimaryBadge($text);
    elseif ($rand == 3)
        return createSecondaryBadge($text);
    elseif ($rand == 4)
        return createWarningBadge($text);
    elseif ($rand == 5)
        return createSuccessBadge($text);
    elseif ($rand == 6)
        return createInfoBadge($text);
}

function parseUserID($userid)
{
    return createBadge($userid);
}

function copperParse($int)
{
    return shortNumberParse($int) . " " . loadImageAsset("menu/coin-copper.svg");     
}

function tokenParse($int)
{
    return shortNumberParse($int) . " " . loadImageAsset("menu/coin-chivalry.svg");
}

function createAndLoadBook($jsonPath) {
    $data = json_decode(file_get_contents("./data/books/" . $jsonPath . ".json"), true);
    if (!$data || !isset($data['pages']) || !is_array($data['pages'])) {
        return '<div class="alert alert-danger">Invalid book file.</div>';
    }
    $id = 'book_' . md5($jsonPath);
    $title = htmlspecialchars($data['title'] . ' by ' . $data['author']);
    
    ob_start();
    ?>
    <!-- Trigger Button -->
    <button class="btn btn-primary" data-toggle="modal" data-target="#<?= $id ?>">Read “<?= htmlspecialchars($data['title']) ?>”</button>

    <!-- Modal -->
    <div class="modal fade" id="<?= $id ?>" tabindex="-1" role="dialog" aria-hidden="true">
      <div class="modal-dialog modal-lg modal-dialog-centered" role="document">
        <div class="modal-content">
          <div class="modal-header">
            <h5 class="modal-title"><?= $title ?></h5>
            <button type="button" class="close" data-dismiss="modal">&times;</button>
          </div>
          <div class="modal-body">
            <div id="<?= $id ?>_content"></div>
          </div>
          <div class="modal-footer">
            <button id="<?= $id ?>_prev" class="btn btn-secondary">Previous</button>
            <span id="<?= $id ?>_indicator" class="mx-3"></span>
            <button id="<?= $id ?>_next" class="btn btn-secondary">Next</button>
          </div>
        </div>
      </div>
    </div>

    <script>
    (function(){
      const pages = <?= json_encode($data['pages'], JSON_HEX_TAG) ?>;
      let idx = 0;
      const modal = $('#<?= $id ?>');
      const content = $('#<?= $id ?>_content');
      const prevBtn = $('#<?= $id ?>_prev');
      const nextBtn = $('#<?= $id ?>_next');
      const indicator = $('#<?= $id ?>_indicator');

      function render() {
        content.html(pages[idx]);
        indicator.text(`Page ${idx+1} of ${pages.length}`);
        prevBtn.prop('disabled', idx === 0);
        nextBtn.prop('disabled', idx >= pages.length - 1);
      }

      modal.on('shown.bs.modal', () => { idx = 0; render(); });
      prevBtn.click(() => { if (idx > 0) { idx--; render(); } });
      nextBtn.click(() => { if (idx < pages.length - 1) { idx++; render(); } });
    })();
    </script>
    <?php

    return ob_get_clean();
}