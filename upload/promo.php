<?php
//Used for users in game to redeem promo codes!
//CREATE TABLE `promo_codes_claimed` ( `userid` INT(11) UNSIGNED NOT NULL , `promo_id` INT(11) UNSIGNED NOT NULL ) ENGINE = InnoDB;
require('globals.php');
if (isset($_POST['code']))
{
    $code = (isset($_POST['code'])) ? $db->escape(strip_tags(stripslashes($_POST['code']))) : '';
    if (empty($code))
    {
        alert('danger','Uh Oh!',"Please input a valid promo code.", false);
    }
    $promocodereal = $db->query("/*qc=on*/SELECT * FROM `promo_codes` WHERE `promo_code` = '{$code}'");
    if ($db->num_rows($promocodereal) > 0) 
    {
        $pcrr = $db->fetch_row($promocodereal);
        $q = $db->query("SELECT * FROM `promo_codes_claimed` WHERE `userid` = {$userid} AND `promo_id` = {$pcrr['promo_id']}");
        if ($db->num_rows($q) > 0)
        {
            alert('danger','Uh Oh!',"It appears you've already claimed this code!", false);
        }
        else 
        {
            item_add($userid, $pcrr['promo_item'], 1);
            $db->query("UPDATE `promo_codes` SET `promo_use` = `promo_use` + 1 WHERE `promo_code` = '{$code}'");
            $db->query("INSERT INTO `promo_codes_claimed` (`userid`, `promo_id`) VALUES ('{$userid}', '{$pcrr['promo_id']}')");
            alert('success','Success!',"Your promo code <b>{$code}</b> was valid. Please enjoy your {$api->SystemItemIDtoName($pcrr['promo_item'])}!", false);
            $api->SystemLogsAdd($userid, "promo", "Claimed promo code {$code} for a {$api->SystemItemIDtoName($pcrr['promo_item'])}.");
        }
    }
    else
    {
        alert('danger','Uh Oh!',"That's not a valid code!", false);
    }
}
echo "<form method='post'>
    <div class='card'>
        <div class='card-body'>
            <div class='row'>
                <div class='col-12 col-md-3 col-xxl-2'>
                    Promo Code<br />
                </div>
                <div class='col-12 col-md-5'>
                    <input type='text' name='code' class='form-control' placeholder='Enter promo code' required='1'><br />
                </div>
                <div class='col-12 col-md-4 col-xxl-5'>
                    <input type='submit' value='Redeem' class='btn btn-primary btn-block'>
                </div>
            </div>
        </div>
    </div>
</form><br />";
$q2 = $db->query("SELECT * FROM `promo_codes_claimed` WHERE `userid` = {$userid}");
if ($db->num_rows($q2) > 0)
{
    echo "<div class='row'>
            <div class='col-12'>
                <div class='card'>
                    <div class='card-header'>
                        Redeemed Promo Codes
                    </div>
                    <div class='card-body'><div class='row'>";
                            while ($r = $db->fetch_row($q2))
                            {
                                $promoname = $db->fetch_single($db->query("SELECT `promo_code` FROM `promo_codes` WHERE `promo_id` = {$r['promo_id']}"));
                                echo "<div class='col-auto'>" . strtoupper($promoname). "</div>"; 
                            }
                   echo" </div></div>
                </div>
            </div>
           </div>";
}
$h->endpage();