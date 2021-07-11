<?php
if ($ir['rickroll'] != 0)
{
    alert('danger',"Rick-Rolled!","You have been rick-rolled by {$api->SystemUserIDtoName($ir['rickroll'])}!!");
    ?>
    <div class="embed-responsive embed-responsive-16by9">
        <video class="embed-responsive-item" id="ytplayer" autoplay>
        	<source src='./assets/video/rick-roll.mp4' type='video/mp4'>
        </video>
    </div>
    <?php
    $db->query("UPDATE `user_settings` SET `rickroll` = 0 WHERE `userid` = {$userid}");
    die($h->endpage());
}