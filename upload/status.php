<?php
require('globals.php');
$time = time();
$q=$db->query("SELECT * FROM `users_effects` WHERE `userid` = {$userid} AND `effectTimeOut` > {$time}");
if ($db->num_rows($q) == 0)
{
    alert('danger',"","You have no active status effects at this time.",false);
    die($h->endpage());
}
alert('info',"","These are your current active status effects.",false);
echo "<div class='row'>";
while ($r = $db->fetch_row($q))
{
    echo "<div class='col-12 col-md-6 col-xxl-4'>
                <div class='card'>
                    <div class='card-body'>
                        <h3>" . strtoupper(effectNameParser($r['effectName'])) . " X {$r['effectMulti']}</h3>
                        " . effectDescParser($r['effectName'], $r['effectMulti']) . ".<br />
                        <small><span class='text-muted'>Wears off in " . TimeUntil_Parse($r['effectTimeOut']) . ".</span></small>
                    </div>
                </div>
                <br />
          </div>";
}
echo "</div>";
$h->endpage();

//func
function effectNameParser($effectID)
{
    $effectNameArray= array(mining_xp_boost => "Mining XP Boost",
        holiday_mining_energy => "Holiday Mining Energy",
        invisibility => "Invisibility",
        basic_protection => "Basic Protection",
        sleep => "Sleeping",
        wood_cut_cooldown => "Woodcutting Cooldown",
        effect_mysterious_potion => "Mysterious Potion",
        effect_posion => "Posioned",
        effect_regen => "Regeneration",
        farm_well_cooldown => "Well Cooldown",
        farm_well_less_cooldown => "Efficient Buckets",
        farm_well_cooldown_cutoff => "Community Well",
        effect_strength => "Strength",
        effect_agility => "Agility",
        effect_guard => "Guard",
        effect_injure_prim_wep => "Primary Hand Injured",
        effect_injure_sec_wep => "Secondary Hand Injured"
    );
    return $effectNameArray[$effectID];
}

function effectDescParser($effectID, $effectMulti = 1)
{
    $effectNameArray= array(mining_xp_boost => "Increases mining experience gains by " . number_format($effectMulti + 1) . "X",
        holiday_mining_energy => "Decreases mining energy required by " . number_format($effectMulti * 20) . "%",
        invisibility => "Appear offline, but does not hide actions",
        basic_protection => "Protection against most basic attacks and explosives",
        sleep => "Sleeping to regenerate stats.",
        wood_cut_cooldown => "Using the woodcutter excessively would be rude",
        effect_mysterious_potion => "Must recover before drinking another",
        effect_posion => "Losing " . number_format($effectMulti * 5) . "% HP per minute",
        effect_regen => "Regenerating " . number_format($effectMulti * 5) . "% HP per minute",
        farm_well_cooldown => "Using the farming well excessively would be rude",
        farm_well_less_cooldown => "-50% cooldown time, per bucket, when filling from the well",
        farm_well_cooldown_cutoff => "May now obtain up to five buckets before a cooldown with the well",
        effect_strength => "Increases strength in combat by " . number_format($effectMulti * 5) . "%",
        effect_agility => "Increases agility in combat by " . number_format($effectMulti * 5) . "%",
        effect_guard => "Increases guard in combat by " . number_format($effectMulti * 5) . "%",
        effect_injure_prim_wep => "Cannot equip primary weapon until healed",
        effect_injure_sec_wep => "Cannot equip secondary weapon until healed"
    );
    return $effectNameArray[$effectID];
}