<?php
/*
	File:		index.php
	Created: 	4/5/2016 at 12:10AM Eastern Time
	Info: 		Main directory file. Will redirect players to login
				if they're not logged in, otherwise will show players
				their stats and other useful information.
	Author:		TheMasterGeneral
	Website: 	https://github.com/MasterGeneral156/chivalry-engine
*/
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
$_POST['pn_update'] =
        (isset($_POST['pn_update']))
                ? strip_tags(stripslashes($_POST['pn_update'])) : '';
if (!empty($_POST['pn_update']))
{
    if (strlen($_POST['pn_update']) > 65655)
    {
		alert('danger',$lang['ERROR_GENERIC'],$lang['ERRDE_PN'],false);
    }
    else
    {
        $pn_update_db = $db->escape($_POST['pn_update']);
        $db->query(
                "UPDATE `users`
        			SET `personal_notes` = '{$pn_update_db}'
        			WHERE `userid` = {$userid}");
        $ir['personal_notes'] = $_POST['pn_update'];
		alert('success',$lang['ERROR_SUCCESS'],$lang['INDEX_PNSUCCESS'],false);
    }
}

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

$template = array(
                'L_INDEX_TITLE'         => $lang['INDEX_TITLE'],
                'L_INDEX_LEVEL'         => $lang['INDEX_LEVEL'],
                'ir_level'              => number_format($ir['level']),
                'ir_xp'                 => number_format($ir['xp']),
                'ir_xp_needed'          => number_format($ir['xp_needed']),
                'experc'                => $experc,
                'L_INDEX_CLASS'         => $lang['INDEX_CLASS'],
                'ir_class'              => $ir['class'],
		'L_INDEX_VIP'           => $lang['INDEX_VIP'],
                'ir_vip_days'           => number_format($ir['vip_days']),
                'L_INDEX_PRIMCURR'      => $lang['INDEX_PRIMCURR'],
                'ir_primary_currency'   => number_format($ir['primary_currency']),
                'L_INDEX_SECCURR'       => $lang['INDEX_SECCURR'],
                'ir_secondary_currency' => number_format($ir['secondary_currency']),
                'ir_hp'                 => number_format($ir['hp']),
                'ir_maxhp'              => number_format($ir['maxhp']),
                'hpperc'                => $hpperc,
                'L_INDEX_ENERGY'        => $lang['INDEX_ENERGY'],
                'ir_engery'             => number_format($ir['energy']),
                'ir_maxenergy'          => number_format($ir['maxenergy']),
                'enperc'                => $enperc,
                'L_INDEX_WILL'          => $lang['INDEX_WILL'],
                'ir_will'               => number_format($ir['will']),
                'ir_maxwill'            => number_format($ir['maxwill']),
                'wiperc'                => $wiperc,
                'L_INDEX_BRAVE'         => $lang['INDEX_BRAVE'],
                'ir_brave'              => number_format($ir['brave']),
                'ir_maxbrave'           => number_format($ir['maxbrave']),
                'brperc'                => $brperc,
                'L_Stats'               => $lang['GEN_STATS'],
                'L_Strength'            => $lang['GEN_STR'],
                'StrengthFormat'        => $StrengthFormat,
                'L_Ranked'              => $lang['GEN_RANKED'],
                'StrengthRank'          => $StrengthRank,
                'L_Agility'             => $lang['GEN_AGL'],
                'AgilityFormat'         => $AgilityFormat,
                'AgilityRank'           => $AgilityRank,
                'L_Guard'               => $lang['GEN_GRD'],
                'AgilityRank'           => $AgilityRank,
                'GuardFormat'           => $GuardFormat,
                'GuardRank'             => $GuardRank,
                'L_Labor'               => $lang['GEN_LAB'],
                'LaborFormat'           => $LaborFormat,
                'LaborRank'             => $LaborRank,
                'L_IQ'                  => $lang['GEN_IQ'],
                'IQFormat'              => $IQFormat,
                'IQRank'                => $IQRank,
                'L_INDEX_PN'            => $lang['INDEX_PN'],
                'ir_personal_notes'     => $ir['personal_notes'],
                'L_FB_PN'               => $lang['FB_PN']
);
run_template( $template, 'index' );
$h->endpage();