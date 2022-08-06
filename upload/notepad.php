<?php
/**
 * Created by PhpStorm.
 * User: Ryan
 * Date: 3/9/2018
 * Time: 8:25 PM
 */
require('globals.php');
$limit = ($ir['vip_days'] > 0) ? 9 : 2;
$correctedCount = $limit + 1;
if (isset($_POST['pn_update'])) 
{
    //Sanitize the notepad entry
    $_POST['pn_update'] = (isset($_POST['pn_update'])) ? strip_tags(stripslashes($_POST['pn_update'])) : '';
    //Notepad update is too large for the database storage
    if (strlen($_POST['pn_update']) > max_unsign_short) {
        alert('danger', "Uh Oh!", "Your notepad is too big to update.", false);
    } else {
        //Update the notepad after escaping the data entered.
        $pn_update_db = $db->escape($_POST['pn_update']);
        $db->query("UPDATE `users`
        			SET `personal_notes` = '{$pn_update_db}'
        			WHERE `userid` = {$userid}");
        $ir['personal_notes'] = $_POST['pn_update'];
        alert('success', "Success!", "Your notepad has been successfully updated.", false);
    }
}
if (isset($_GET['add'])) 
{
    $count = $db->query("/*qc=on*/SELECT `np_id` FROM `notepads` WHERE `np_owner` = {$userid}");
    if ($db->num_rows($count) >= $limit) 
    {
        alert('danger', "Uh Oh!", "You may only have {$correctedCount} maximum notepads at this time.", false);
        die($h->endpage());
    }
    $db->query("INSERT INTO `notepads` (`np_id`, `np_owner`, `np_text`) VALUES (NULL, '{$userid}', '')");
    alert('success', "Success!", "Notepad created successfully. You may need to refresh to see it.", false);
}
if (isset($_POST['update'])) {
    
    //Sanitize the notepad entry
    $_POST['update'] = (isset($_POST['update'])) ? strip_tags(stripslashes($_POST['update'])) : '';
    $_POST['np_id'] = (isset($_POST['np_id']) && is_numeric($_POST['np_id'])) ? abs($_POST['np_id']) : '';
    //Notepad update is too large for the database storage
    if (strlen($_POST['update']) > max_unsign_short) {
        alert('danger', "Uh Oh!", "Your notepad is too big to update.", false);
    } else {
        $count = $db->query("/*qc=on*/SELECT `np_id` FROM `notepads` WHERE `np_owner` = {$userid} AND `np_id` = {$_POST['np_id']}");
        if ($db->num_rows($count) == 0) {
            alert('danger', "Uh Oh!", "This notepad does not exist, or does not belong to you.", false);
        } else {
            //Update the notepad after escaping the data entered.
            $pn_update_db = $db->escape($_POST['update']);
            $db->query("UPDATE `notepads`
        			SET `np_text` = '{$pn_update_db}'
        			WHERE `np_owner` = {$userid}
        			AND `np_id` = {$_POST['np_id']}");
            alert('success', "Success!", "Your notepad has been successfully updated. You may need to refresh the page to see your changes.", false);
        }
    }
}
alert('info', "", "hese are your notepads. Store important information here as you see fit. Non VIP Players may only have a total of 3 notepads with VIP players allowed 10. If you run out of VIP days, your excessive notepads will simply be unaccessible. Your notes are not lost, they are just hidden 
until you get more VIP days.", true, '?add', 'Create Notepad');
echo "
<div class='row'>
    <div class='col-12 col-xxxl-6'>
        <div class='card'>
            <div class='card-header'>
                Personal Notepad
            </div>
            <div class='card-body'>
            <form method='post'>
                <div class='row'>
                    <div class='col-12'>
                        <textarea class='form-control' name='pn_update' id='pn_update'>{$ir['personal_notes']}</textarea><br />
                    </div>
                    <div class='col-12'>
                        <input type='submit' value='Update Notepad' class='btn btn-primary btn-block'>
                    </div>
                </div>
            </form>
            </div>
        </div>
        <br />
    </div>";
$q = $db->query("/*qc=on*/SELECT * FROM `notepads` WHERE `np_owner` = {$userid} ORDER BY `np_id` ASC LIMIT {$limit}");
while ($r = $db->fetch_row($q)) {
    echo "
    <div class='col-12 col-xxxl-6'>
        <div class='card'>
            <div class='card-header'>
                Notepad ID #{$r['np_id']}
            </div>
            <div class='card-body'>
            <form method='post'>
                <div class='row'>
                    <div class='col-12'>
                        <input type='hidden' value='{$r['np_id']}' name='np_id'>
                        <textarea class='form-control' name='pn_update' id='pn_update'>{$r['np_text']}</textarea><br />
                    </div>
                    <div class='col-12'>
                        <input type='submit' value='Update Notepad' class='btn btn-primary btn-block'>
                    </div>
                </div>
            </form>
            </div>
        </div>
        <br />
    </div>";
}
echo "</div>";
$h->endpage();