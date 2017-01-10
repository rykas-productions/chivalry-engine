<?php
require_once('globals.php');
		$enperc = round($ir['energy'] / $ir['maxenergy'] * 100);
        $wiperc = round($ir['will'] / $ir['maxwill'] * 100);
        $experc = round($ir['xp'] / $ir['xp_needed'] * 100);
        $brperc = round($ir['brave'] / $ir['maxbrave'] * 100);
        $hpperc = round($ir['hp'] / $ir['maxhp'] * 100);
        $enopp = 100 - $enperc;
        $wiopp = 100 - $wiperc;
        $exopp = 100 - $experc;
        $bropp = 100 - $brperc;
        $hpopp = 100 - $hpperc;
echo "<h3>{$lang['INDEX_TITLE']}</h3>";
$_POST['pn_update'] =
        (isset($_POST['pn_update']))
                ? strip_tags(stripslashes($_POST['pn_update'])) : '';
if (!empty($_POST['pn_update']))
{
    if (strlen($_POST['pn_update']) > 65655)
    {
        ?> <div class="alert alert-danger"> <a href="#" class="close" data-dismiss="alert" aria-label="close">&times;</a>  <strong><?php echo $lang['ERROR_GENERIC']; ?></strong> <?php echo $lang['ERRDE_PN']; ?></div><br /> <?php
    }
    else
    {
        $pn_update_db = $db->escape($_POST['pn_update']);
        $db->query(
                "UPDATE `users`
        			SET `personal_notes` = '{$pn_update_db}'
        			WHERE `userid` = {$userid}");
        $ir['personal_notes'] = $_POST['pn_update'];
        ?> <div class="alert alert-success"> <a href="#" class="close" data-dismiss="alert" aria-label="close">&times;</a>  <strong><?php echo $lang['ERROR_SUCCESS']; ?></strong> <?php echo $lang['INDEX_PNSUCCESS']; ?> </div><br /> <?php
    }
}
echo "
<div class='table-resposive'>
<table class='table table-hover table-bordered'>
<tbody>
	<tr>
		<td>
		{$lang['INDEX_LEVEL']}: " . number_format($ir['level']) . "
		</td>
		<td>
		XP: " . number_format($ir['xp']) . " / " . number_format($ir['xp_needed']) . " ({$experc}%)
		</td>
	</tr>
	<tr>
		<td>
		{$lang['INDEX_CLASS']}: {$ir['class']}
		</td>
		<td>
		{$lang['INDEX_VIP']}: " . number_format($ir['vip_days']) . "
		</td>
	</tr>
	<tr>
		<td>
		{$lang['INDEX_PRIMCURR']}: " . number_format($ir['primary_currency']) . "
		</td>
		<td>
		{$lang['INDEX_SECCURR']}: " . number_format($ir['secondary_currency']) . "
		</td>
	</tr>
	<tr>
		<td>
		HP: " . number_format($ir['hp']) . " / " . number_format($ir['maxhp']) . " ({$hpperc}%)
		</td>
		<td>
		{$lang['INDEX_ENERGY']}: " . number_format($ir['energy']) . " / " . number_format($ir['maxenergy']) . " ({$enperc}%)
		</td>
	</tr>
	<tr>
		<td>
		{$lang['INDEX_WILL']}: " . number_format($ir['will']) . " / " . number_format($ir['maxwill']) . " ({$wiperc}%)
		</td>
		<td>
		{$lang['INDEX_BRAVE']}: " . number_format($ir['brave']) . " / " . number_format($ir['maxbrave']) . " ({$brperc}%)
		</td>
	</tr>
</tbody>";
$StrengthRank=get_rank($ir['strength'],'strength');
$StrengthFormat=number_format($ir['strength']);

$AgilityRank=get_rank($ir['agility'],'agility');
$AgilityFormat=number_format($ir['agility']);

$GuardRank=get_rank($ir['guard'],'guard');
$GuardFormat=number_format($ir['guard']);

$IQRank=get_rank($ir['iq'],'iq');
$IQFormat=number_format($ir['iq']);

$LaborRank=get_rank($ir['labor'],'labor');
$LaborFormat=number_format($ir['labor']);
echo "</table>
<h3>Stats</h3>";
echo "<table class='table table-bordered'>
<tr>
	<th width='25%'>
		Strength:
	</th>
	<td>
		{$StrengthFormat} (Ranked: {$StrengthRank})
	</td>
</tr>
<tr>
	<th width='25%'>
		Agility:
	</th>
	<td>
		{$AgilityFormat} (Ranked: {$AgilityRank})
	</td>
</tr>
<tr>
	<th width='25%'>
		Guard:
	</th>
	<td>
		{$GuardFormat} (Ranked: {$GuardRank})
	</td>
</tr>
<tr>
	<th width='25%'>
		Labor:
	</th>
	<td>
		{$LaborFormat} (Ranked: {$LaborRank})
	</td>
</tr>
<tr>
	<th width='25%'>
		IQ:
	</th>
	<td>
		{$IQFormat} (Ranked: {$IQRank})
	</td>
</tr>

</table></div>";
?>
<form method="post">
<div class="form-group">
  <label for="pn_update"><?php echo $lang['INDEX_PN']; ?>:</label>
  <textarea class="form-control" rows="5" name="pn_update" id="pn_update"><?php echo"{$ir['personal_notes']}";?></textarea>
</div>
<button type="submit" class="btn btn-default"><?php echo $lang['FB_PN']; ?></button>
</form>
<?php

$h->endpage();