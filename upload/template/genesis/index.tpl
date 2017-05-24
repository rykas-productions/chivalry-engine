<h3>{L_INDEX_TITLE}</h3>

<table class='table table-hover table-bordered'>
<tbody>
	<tr>
		<td>
		{L_INDEX_LEVEL}: {ir_level}
		</td>
		<td>
		XP: {ir_xp} / {ir_xp_needed} ({experc}%)
		</td>
	</tr>
	<tr>
		<td>
		{L_INDEX_CLASS}: {ir_class}
		</td>
		<td>
		{L_INDEX_VIP}: {ir_vip_days}
		</td>
	</tr>
	<tr>
		<td>
		{L_INDEX_PRIMCURR}: {ir_primary_currency}
		</td>
		<td>
		{L_INDEX_SECCURR}: {ir_secondary_currency}
		</td>
	</tr>
	<tr>
		<td>
		HP: {ir_hp} / {ir_maxhp} ({hpperc}%)
		</td>
		<td>
		{L_INDEX_ENERGY}: {ir_engery} / {ir_maxenergy} ({enperc}%)
		</td>
	</tr>
	<tr>
		<td>
		{L_INDEX_WILL}: {ir_will} / {ir_maxwill} ({wiperc}%)
		</td>
		<td>
		{L_INDEX_BRAVE}: {ir_brave} / {ir_maxbrave} ({brperc}%)
		</td>
	</tr>
</tbody>
</table>

<h3>{L_Stats}</h3>
<table class='table table-bordered'>
<tr>
	<th width='25%'>
		{L_Strength}:
	</th>
	<td>
		{StrengthFormat} ({L_Ranked}: {StrengthRank})
	</td>
</tr>
<tr>
	<th width='25%'>
		{L_Agility}:
	</th>
	<td>
		{AgilityFormat} ({L_Ranked}: {AgilityRank})
	</td>
</tr>
<tr>
	<th width='25%'>
		{L_Guard}:
	</th>
	<td>
		{GuardFormat} ({L_Ranked}: {GuardRank})
	</td>
</tr>
<tr>
	<th width='25%'>
		{L_Labor}:
	</th>
	<td>
		{LaborFormat} ({L_Ranked}: {LaborRank})
	</td>
</tr>
<tr>
	<th width='25%'>
		{L_IQ}:
	</th>
	<td>
		{IQFormat} ({L_Ranked}: {IQRank})
	</td>
</tr>

</table>

<form method="post">
<div class="form-group">
  <label for="pn_update">{L_INDEX_PN}:</label>
  <textarea class="form-control" rows="5" name="pn_update" id="pn_update">{ir_personal_notes}</textarea>
</div>
<button type="submit" class="btn btn-default">{L_FB_PN}</button>
</form>