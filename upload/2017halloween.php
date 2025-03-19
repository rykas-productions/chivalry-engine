<?php
require('globals.php');
if (!isset($_GET['action'])) {
    $_GET['action'] = '';
}
switch ($_GET['action']) {
    case "ticket":
        ticket();
        break;
    case "chuck":
        chuck();
        break;
	case "tnt":
        tnt();
        break;
    default:
        alert('danger',"Uh Oh!","Please specify an action.",true,'index.php');
        break;
}
function ticket()
{
	global $h,$db,$api,$userid;
	$needItem = 63;
	$lootJSON = "items/scratch/17_halloween_scratch";
	if (!$api->UserHasItem($userid,$needItem,1))
	{
		alert('danger',"Uh Oh!","You need a {$api->SystemItemIDtoName($needItem)} to be here.",true,'inventory.php');
		die($h->endpage());
	}
	if (isset($_GET['scratch']))
	{
	    $loot = giveUserLoot($userid, $lootJSON);
	    alert("success","Success!","You begin to scratch this spot off on a {$api->SystemItemIDtoName($needItem)}. {$loot} Congratulations!",true,'inventory.php');
		$api->UserTakeItem($userid,$needItem,1);
	}
	else
	{
	    echo "
        <div class='card'>
            <div class='card-header'>
                Scratching off a {$api->SystemItemIDtoName($needItem)}...
            </div>
            <div class='card-body'>
        		<div class='row'>
        			<div class='col-sm'>
        				<a href='?action=ticket&scratch=1'><img src='". returnAssetDir() ."img/pumpkin-halloween.png' class='img-fluid'></a>
        			</div>
        			<div class='col-sm'>
        				<a href='?action=ticket&scratch=1'><img src='". returnAssetDir() ."img/pumpkin-halloween.png' class='img-fluid'></a>
        			</div>
        			<div class='col-sm'>
        				<a href='?action=ticket&scratch=1'><img src='". returnAssetDir() ."img/pumpkin-halloween.png' class='img-fluid'></a>
        			</div>
        			<div class='col-sm'>
        				<a href='?action=ticket&scratch=1'><img src='". returnAssetDir() ."img/pumpkin-halloween.png' class='img-fluid'></a>
        			</div>
        			<div class='col-sm'>
        				<a href='?action=ticket&scratch=1'><img src='". returnAssetDir() ."img/pumpkin-halloween.png' class='img-fluid'></a>
        			</div>
        			<div class='col-sm'>
        				<a href='?action=ticket&scratch=1'><img src='". returnAssetDir() ."img/pumpkin-halloween.png' class='img-fluid'></a>
        			</div>
        		</div>
                <div class='row'>
                    " . parseLootTableOdds($lootJSON) . "
                </div>
            </div>
        </div>";
	}
}
$h->endpage();