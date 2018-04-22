<?php
if ($ir['rickroll'] != 0)
{
    alert('danger',"Rick-Rolled!","You have been rick-rolled by {$api->SystemUserIDtoName($ir['rickroll'])}!!");
    ?>
    <div class="embed-responsive embed-responsive-16by9">
        <iframe class="embed-responsive-item" id="ytplayer"
            type="text/html"
            src="https://www.youtube.com/embed/dQw4w9WgXcQ?autoplay=1&origin=http://chivalryisdeadgame.com" frameborder="0">
        </iframe>
    </div>
    <?php
    $db->query("UPDATE `user_settings` SET `rickroll` = 0 WHERE `userid` = {$userid}");
    die($h->endpage());
}