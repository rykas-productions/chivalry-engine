<?php
require('globals.php');
echo "<h4>Item of the Week</h4><hr />
Here you may buy the three Items of the Week for a 50% discount. This should change weekly, so check back often.<br />";
$item2=$db->fetch_row($db->query("SELECT * FROM `items` WHERE `itmid` = {$set['itemweek2']}"));
$item1=$db->fetch_row($db->query("SELECT * FROM `items` WHERE `itmid` = {$set['itemweek1']}"));
$item3=$db->fetch_row($db->query("SELECT * FROM `items` WHERE `itmid` = {$set['itemweek3']}"));
$item1price=$item1['itmbuyprice']-($item1['itmbuyprice']/2);
$item2price=$item2['itmbuyprice']-($item2['itmbuyprice']/2);
$item3price=$item3['itmbuyprice']-($item3['itmbuyprice']/2);
if (isset($_GET['buy1']))
{
    if ($api->UserHasCurrency($userid,'primary',$item1price))
    {
        $api->UserTakeCurrency($userid,'primary',$item1price);
        $api->UserGiveItem($userid,$item1['itmid'],1);
        alert('success',"Success!","You have successfully bought 1 {$item1['itmname']}.",false);
    }
    else
    {
        alert('danger',"Uh Oh!","You do not have enough Copper Coins to buy a {$item1['itmname']}.",false);
    }
}
if (isset($_GET['buy2']))
{
    if ($api->UserHasCurrency($userid,'primary',$item2price))
    {
        $api->UserTakeCurrency($userid,'primary',$item2price);
        $api->UserGiveItem($userid,$item2['itmid'],1);
        alert('success',"Success!","You have successfully bought 1 {$item2['itmname']}.",false);
    }
    else
    {
        alert('danger',"Uh Oh!","You do not have enough Copper Coins to buy a {$item2['itmname']}.",false);
    }
}
if (isset($_GET['buy3']))
{
    if ($api->UserHasCurrency($userid,'primary',$item3price))
    {
        $api->UserTakeCurrency($userid,'primary',$item3price);
        $api->UserGiveItem($userid,$item3['itmid'],1);
        alert('success',"Success!","You have successfully bought 1 {$item3['itmname']}.",false);
    }
    else
    {
        alert('danger',"Uh Oh!","You do not have enough Copper Coins to buy a {$item3['itmname']}.",false);
    }
}
echo "<div class='row'>
    <div class='col-sm-4'>
    <div class='card'>
        <div class='card-header box-shadow'>
            Price: <s>" . number_format($item1['itmbuyprice']) . "</s> <b>" . number_format($item1price) . "</b> Copper Coins
        </div>
        <div class='card-body'>
            <h1 class='card-title pricing-card-title'><a href='iteminfo.php?ID={$item1['itmid']}'>{$item1['itmname']}</a></h1>";
        $uhoh = 0;
		//List the item's effects.
		for ($enum = 1; $enum <= 3; $enum++) {
			if ($item1["effect{$enum}_on"] == 'true') {
				//Lets make the item's effects more user friendly to read, eh.
				$einfo = unserialize($item1["effect{$enum}"]);
				$einfo['inc_type'] = ($einfo['inc_type'] == 'percent') ? '%' : '';
				$einfo['dir'] = ($einfo['dir'] == 'pos') ? "Increases" : "Decreases";
				$stats =
					array("energy" => "Energy", "will" => "Will",
						"brave" => "Bravery", "level" => "Level",
						"hp" => "Health", "strength" => "Strength",
						"agility" => "Agility", "guard" => "Guard",
						"labor" => "Labor", "iq" => "IQ",
						"infirmary" => "Infirmary Time", "dungeon" => "Dungeon Time",
						"primary_currency" => "Copper Coins", "secondary_currency"
					=> "Chivalry Tokens", "crimexp" => "Experience", "vip_days" =>
						"VIP Days" , "premium_currency" => "Mutton", "luck" => "Luck");
				$statformatted = $stats["{$einfo['stat']}"];
				echo "{$einfo['dir']} {$statformatted} by " . number_format($einfo['inc_amount']) . "{$einfo['inc_type']}.<br />";
			} //If item has no effects, lets list the description instead.
			else {
				$uhoh++;
			}
			if ($uhoh == 3) {
				echo "{$item1['itmdesc']}";
			}
		}
        echo "<br />
        <a href='?buy1' class='btn btn-primary'>Buy</a></div>
		</div>
        </div>
        ";
echo "
    <div class='col-sm-4'>
    <div class='card'>
        <div class='card-header box-shadow'>
            Price: <s>" . number_format($item2['itmbuyprice']) . "</s> <b>" . number_format($item2price) . "</b> Copper Coins
        </div>
        <div class='card-body'>
            <h1 class='card-title pricing-card-title'><a href='iteminfo.php?ID={$item2['itmid']}'>{$item2['itmname']}</a></h1>";
        $uhoh = 0;
		//List the item's effects.
		for ($enum = 1; $enum <= 3; $enum++) {
			if ($item2["effect{$enum}_on"] == 'true') {
				//Lets make the item's effects more user friendly to read, eh.
				$einfo = unserialize($item2["effect{$enum}"]);
				$einfo['inc_type'] = ($einfo['inc_type'] == 'percent') ? '%' : '';
				$einfo['dir'] = ($einfo['dir'] == 'pos') ? "Increases" : "Decreases";
				$stats =
					array("energy" => "Energy", "will" => "Will",
						"brave" => "Bravery", "level" => "Level",
						"hp" => "Health", "strength" => "Strength",
						"agility" => "Agility", "guard" => "Guard",
						"labor" => "Labor", "iq" => "IQ",
						"infirmary" => "Infirmary Time", "dungeon" => "Dungeon Time",
						"primary_currency" => "Copper Coins", "secondary_currency"
					=> "Chivalry Tokens", "crimexp" => "Experience", "vip_days" =>
						"VIP Days" , "premium_currency" => "Mutton", "luck" => "Luck");
				$statformatted = $stats["{$einfo['stat']}"];
				echo "{$einfo['dir']} {$statformatted} by " . number_format($einfo['inc_amount']) . "{$einfo['inc_type']}.<br />";
			} //If item has no effects, lets list the description instead.
			else {
				$uhoh++;
			}
			if ($uhoh == 3) {
				echo "{$item2['itmdesc']}";
			}
		}
        echo "<br />
        <a href='?buy2' class='btn btn-primary'>Buy</a></div>
		</div>
        </div>
        ";
        echo "
    <div class='col-sm-4'>
    <div class='card'>
        <div class='card-header box-shadow'>
            Price: <s>" . number_format($item3['itmbuyprice']) . "</s> <b>" . number_format($item3price) . "</b> Copper Coins
        </div>
        <div class='card-body'>
            <h1 class='card-title pricing-card-title'><a href='iteminfo.php?ID={$item3['itmid']}'>{$item3['itmname']}</a></h1>";
        $uhoh = 0;
		//List the item's effects.
		for ($enum = 1; $enum <= 3; $enum++) {
			if ($item3["effect{$enum}_on"] == 'true') {
				//Lets make the item's effects more user friendly to read, eh.
				$einfo = unserialize($item3["effect{$enum}"]);
				$einfo['inc_type'] = ($einfo['inc_type'] == 'percent') ? '%' : '';
				$einfo['dir'] = ($einfo['dir'] == 'pos') ? "Increases" : "Decreases";
				$stats =
					array("energy" => "Energy", "will" => "Will",
						"brave" => "Bravery", "level" => "Level",
						"hp" => "Health", "strength" => "Strength",
						"agility" => "Agility", "guard" => "Guard",
						"labor" => "Labor", "iq" => "IQ",
						"infirmary" => "Infirmary Time", "dungeon" => "Dungeon Time",
						"primary_currency" => "Copper Coins", "secondary_currency"
					=> "Chivalry Tokens", "crimexp" => "Experience", "vip_days" =>
						"VIP Days" , "premium_currency" => "Mutton", "luck" => "Luck");
				$statformatted = $stats["{$einfo['stat']}"];
				echo "{$einfo['dir']} {$statformatted} by " . number_format($einfo['inc_amount']) . "{$einfo['inc_type']}.<br />";
			} //If item has no effects, lets list the description instead.
			else {
				$uhoh++;
			}
			if ($uhoh == 3) {
				echo "{$item3['itmdesc']}";
			}
		}
        echo "<br />
        <a href='?buy3' class='btn btn-primary'>Buy</a></div>
		</div>
        </div>
        </div>
        ";
        $h->endpage();