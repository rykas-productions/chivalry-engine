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
if (isset($_POST['pn_update'])) {
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
if (isset($_POST['update'])) 
{
    
    //Sanitize the notepad entry
    $_POST['update'] = (isset($_POST['update'])) ? strip_tags(stripslashes($_POST['update'])) : '';
    $_POST['np_id'] = (isset($_POST['np_id']) && is_numeric($_POST['np_id'])) ? abs($_POST['np_id']) : '';
    $notepadLength = strlen($_POST['update']);
    //Notepad update is too large for the database storage
    if ($notepadLength > max_unsign_short) 
    {
        alert('danger', "Uh Oh!", "Your notepad is too big to update. It must be, at most, " . shortNumberParse(max_unsign_short) . " characters in length.", false);
    } 
    else 
    {
        $count = $db->query("/*qc=on*/SELECT `np_id` FROM `notepads` WHERE `np_owner` = {$userid} AND `np_id` = {$_POST['np_id']}");
        if ($db->num_rows($count) == 0) 
        {
            alert('danger', "Uh Oh!", "This notepad does not exist, or does not belong to you.", false);
        } 
        else 
        {
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
echo " <h3>Your Notepads</h3><hr />
 These are your notepads. Store important information here as you see fit. Non VIP Players may only have a total of 3 notepads. Players with VIP Days may only have 10. You may 
have a total of {$correctedCount} notepads at this time. If you run out of VIP days, your excessive notepads will simply be unaccessible. Your notes are not lost, they are just hidden 
until you get more VIP days.<br />
[<a href='?add'>New Notepad</a>]<hr />
<form method='post'>
    <div class='form-group'>
        <label for='pn_update'>Personal Notepad</label>
        <textarea class='form-control' rows='5' name='pn_update' id='pn_update'>{$ir['personal_notes']}</textarea>
    </div>
    <button type='submit' class='btn btn-primary'>Update Notepad</button>
</form>";
$q = $db->query("/*qc=on*/SELECT * FROM `notepads` WHERE `np_owner` = {$userid} ORDER BY `np_id` ASC LIMIT {$limit}");
while ($r = $db->fetch_row($q)) 
{
    echo "
    <form method='post'>
        <div class='form-group'>
            <label for='pn_update'>Notepad ID #{$r['np_id']}</label>
            <input type='hidden' value='{$r['np_id']}' name='np_id'>
            <textarea class='form-control' rows='5' name='update' id='update'>{$r['np_text']}</textarea>
        </div>
        <button type='submit' class='btn btn-primary'>Update Notepad</button>
    </form>";
}
$h->endpage();