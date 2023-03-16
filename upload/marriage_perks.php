<?php
$rng=Random(1,100);
$mi=$db->query("/*qc=on*/SELECT * FROM `marriage_tmg` WHERE (`proposer_id` = {$userid} OR `proposed_id` = {$userid}) AND `together` = 1");
$mt=$db->fetch_row($mi);
//Assign the proper values.
if ($mt['proposer_id'] == $userid)
    $event=$mt['proposed_id'];
else
    $event=$mt['proposer_id'];
//Happiness 10 or above.
if ($mt['happiness'] > 9)
{
    //Marriage received last refill over 30 minutes ago.
    if ($mt['last_refill'] < time()-1800)
    {
        //Each person has a ring equipped.
        if (($mt['proposer_ring'] > 0) && ($mt['proposed_ring'] > 0))
        {
            //Ring is flawed
            $flawed=array(113,114,115);
            $durable=array(125,126,127);
            $great=array(116);
            $sapphire=array(113,125);
            $emerald=array(114,126);
            $ruby=array(115,127);
            $other=array(116);
            //Flawed
            if (in_array($mt['proposer_ring'],$flawed))
            {
                $increase=5;
            }
            if (in_array($mt['proposed_ring'],$flawed))
            {
                $increase2=5;
            }
            //Durable rings
            if (in_array($mt['proposer_ring'],$durable))
            {
                $increase=10;
            }
            if (in_array($mt['proposed_ring'],$durable))
            {
                $increase2=10;
            }
            //Great
            if (in_array($mt['proposer_ring'],$great))
            {
                $increase=20;
            }
            if (in_array($mt['proposed_ring'],$great))
            {
                $increase2=20;
            }
            //Ring is sapphire
            if (in_array($mt['proposer_ring'],$sapphire))
            {
                $stat='energy';
            }
            if (in_array($mt['proposed_ring'],$sapphire))
            {
                $stat2='energy';
            }
            //Ring is emerald
            if (in_array($mt['proposer_ring'],$emerald))
            {
                $stat='will';
            }
            if (in_array($mt['proposed_ring'],$emerald))
            {
                $stat2='will';
            }
            //Ring is emerald
            if (in_array($mt['proposer_ring'],$ruby))
            {
                $stat='brave';
            }
            if (in_array($mt['proposed_ring'],$ruby))
            {
                $stat2='brave';
            }
            //Ring is other
            if (in_array($mt['proposer_ring'],$other))
            {
                $stat='hp';
            }
            if (in_array($mt['proposed_ring'],$other))
            {
                $stat2='hp';
            }
            //Both users have been active in the last 15 minutes
            $proposedlo=$api->UserInfoGet($mt['proposed_id'],'laston');
            $proposerlo=$api->UserInfoGet($mt['proposer_id'],'laston');
            $newmarriage=time();
            if (($proposedlo > time()-600) && ($proposerlo > time()-600))
            {
                if (getSkillLevel($mt['proposed_id'],27) != 0)
                    $increase2=$increase2+10;
                if (getSkillLevel($mt['proposer_id'],27) != 0)
                    $increase=$increase+10;
                if ($rng < 6)
                {
                    $api->UserInfoSet($mt['proposed_id'],$stat2,$increase2);
                    $api->UserInfoSet($mt['proposer_id'],$stat,$increase);
                    $api->GameAddNotification($mt['proposed_id'],"Being around while your spouse is has increased your {$stat2} by {$increase2}%.");
                    $api->GameAddNotification($mt['proposer_id'],"Being around while your spouse is has increased your {$stat} by {$increase}%.");
                    $db->query("UPDATE `marriage_tmg` SET `last_refill` = {$newmarriage} WHERE `marriage_id` = {$mt['marriage_id']}");
                }
            }
        }
    }
}