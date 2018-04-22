<?php
/**
 * Created by PhpStorm.
 * User: Ryan
 * Date: 3/9/2018
 * Time: 8:25 PM
 */
require('globals.php');
if (isset($_POST['pn_update'])) {
    //Sanitize the notepad entry
    $_POST['pn_update'] = (isset($_POST['pn_update'])) ? strip_tags(stripslashes($_POST['pn_update'])) : '';
    //Notepad update is too large for the database storage
    if (strlen($_POST['pn_update']) > 65535) {
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
if (isset($_GET['add'])) {
    $count = $db->query("/*qc=on*/SELECT `np_id` FROM `notepads` WHERE `np_owner` = {$userid}");
    if ($ir['vip_days'] == 0) {
        if ($db->num_rows($count) >= 2) {
            alert('danger', "Uh Oh!", "You may only have three maximum notepads if you have no VIP days.", false);
            die($h->endpage());
        }
        $db->query("INSERT INTO `notepads` (`np_id`, `np_owner`, `np_text`) VALUES (NULL, '{$userid}', '')");
        alert('success', "Success!", "Notepad created successfully. You may need to refresh to see it.");
    } else {
        if ($db->num_rows($count) >= 9) {
            alert('danger', "Uh Oh!", "You may only have nine maximum notepad.", false);
            die($h->endpage());
        }
        $db->query("INSERT INTO `notepads` (`np_id`, `np_owner`, `np_text`) VALUES (NULL, '{$userid}', '')");
        alert('success', "Success!", "Notepad created successfully. You may need to refresh to see it.");
    }
}
if (isset($_POST['update'])) {
    //Sanitize the notepad entry
    $_POST['update'] = (isset($_POST['update'])) ? strip_tags(stripslashes($_POST['update'])) : '';
    $_POST['np_id'] = (isset($_POST['np_id']) && is_numeric($_POST['np_id'])) ? abs($_POST['np_id']) : '';
    //Notepad update is too large for the database storage
    if (strlen($_POST['update']) > 65535) {
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
echo " <h3>Your Notepads</h3><hr />
 These are your notepads. Store important information here as you see fit. Non VIP Players may only have a total of 3 notepads. Players with VIP Days may only have 10.<br />
[<a href='?add'>New Notepad</a>]<hr />
<form method='post'>
    <div class='form-group'>
        <label for='pn_update'>Personal Notepad</label>
        <textarea class='form-control' rows='5' name='pn_update' id='pn_update'>{$ir['personal_notes']}</textarea>
    </div>
    <button type='submit' class='btn btn-primary'>Update Notepad</button>
</form>";
$q = $db->query("/*qc=on*/SELECT * FROM `notepads` WHERE `np_owner` = {$userid}");
while ($r = $db->fetch_row($q)) {
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