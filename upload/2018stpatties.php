<?php
require('globals.php');
echo "<h3>2018 St. Patrick's Day Event</h3><hr />";
if (!isset($_GET['action'])) {
    $_GET['action'] = '';
}
switch ($_GET['action']) {
    case "exchange":
        exchange();
        break;
    case "doexchange1":
        doexchange(1,25);
    case "doexchange2":
        doexchange(5,200);
    case "doexchange3":
        doexchange(10,500);
    case "doexchange4":
        doexchange(25,1500);
    case "doexchange5":
        doexchange(100,5000);
    case "ticket":
        ticket();
        break;
    default:
        alert('danger',"Uh Oh!","Please specify an action.",true,'index.php');
        break;
}
function exchange()
{
    echo "Leprechauns are tricky folks, for sure, but, they're reasonable. They'll barter their freedom from you, you just list your price.<br />
    <a href='?action=doexchange1'>1 Leprechauns = 25 Chivalry Tokens</a><br />
    <a href='?action=doexchange2'>5 Leprechauns = 200 Chivalry Tokens</a><br />
    <a href='?action=doexchange3'>10 Leprechauns = 500 Chivalry Tokens</a><br />
    <a href='?action=doexchange4'>25 Leprechauns = 1,500 Chivalry Tokens</a><br />
    <a href='?action=doexchange5'>100 Leprechauns = 5,000 Chivalry Tokens</a><br />";
}
function doexchange($needed,$payout)
{
    global $db,$ir,$userid,$api,$h;
    if ($api->UserHasItem($userid,136,$needed))
    {
        $api->UserGiveCurrency($userid,'secondary',$payout);
        $api->UserTakeItem($userid,136,$needed);
        alert('success',"Success!","You've let {$needed} Leprechauns free in-exchange for {$payout} Chivalry Tokens.",false);
    }
    else
    {
        alert('danger',"Uh Oh!","You do not have enough Leprechauns for this deal.",false);
    }
    exchange();
    die($h->endpage());
}
function ticket()
{
    global $h,$db,$api,$userid;
    $needItem = 137;
    $lootJSON = "items/scratch/18_stpatties_scratch";
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
        				<a href='?action=ticket&scratch=1'><img src='". returnAssetDir() ."img/green-shamrock.png' class='img-fluid'></a>
        			</div>
        			<div class='col-sm'>
        				<a href='?action=ticket&scratch=1'><img src='". returnAssetDir() ."img/green-shamrock.png' class='img-fluid'></a>
        			</div>
        			<div class='col-sm'>
        				<a href='?action=ticket&scratch=1'><img src='". returnAssetDir() ."img/green-shamrock.png' class='img-fluid'></a>
        			</div>
        			<div class='col-sm'>
        				<a href='?action=ticket&scratch=1'><img src='". returnAssetDir() ."img/green-shamrock.png' class='img-fluid'></a>
        			</div>
        			<div class='col-sm'>
        				<a href='?action=ticket&scratch=1'><img src='". returnAssetDir() ."img/green-shamrock.png' class='img-fluid'></a>
        			</div>
        			<div class='col-sm'>
        				<a href='?action=ticket&scratch=1'><img src='". returnAssetDir() ."img/green-shamrock.png' class='img-fluid'></a>
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