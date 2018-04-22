<?php
if ((date('j') == 1) || (date('j') == 2))
{
    if ($ir['holiday_time'] < time())
    {
        if (Random(1,5) == 3)
        {
            $item=Random(140,144);
            $easteregg=$api->SystemItemIDtoName($item);
            echo returnIcon($item,10);
            alert('info',"Egg Found!","You have found yourself an {$easteregg}! Hold onto it, you will be able to trade it in for prizes!");
            $api->UserGiveItem($userid,$item,1);
            $easter_time=time()+Random(300,900);
            $db->query("UPDATE `user_settings` SET `holiday_time` = {$easter_time} WHERE `userid` = {$userid}");
            die($h->endpage());
        }
    }
}