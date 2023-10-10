<?php
/*
	File:		newspaper.php
	Created: 	4/5/2016 at 12:18AM Eastern Time
	Info: 		Allows players to place ads in a newspaper, and view
				currently running ads.
	Author:		TheMasterGeneral
	Website: 	https://github.com/MasterGeneral156/chivalry-engine
*/
$disablespeed=1;
require("globals.php");
require('lib/bbcode_engine.php');
$baseCharCost = 7;
$baseDayCost = 2000;
$baseInitCost = 25000;

$adjustedCharCost = $baseCharCost * levelMultiplier($ir['level'], $ir['reset']);
$adjustedDayCost = $baseDayCost * levelMultiplier($ir['level'], $ir['reset']);
$adjustedInitCost = $baseInitCost * levelMultiplier($ir['level'], $ir['reset']);

?>
<script>
    function total_cost() {
        var day = parseInt((document.getElementById("days").value) * <?php echo $adjustedDayCost; ?>);
        var init = parseInt((document.getElementById("init").value));
        var charlength = parseInt((document.getElementById("chars").value.length) * <?php echo $adjustedCharCost; ?>);
        var totalcost = day + init + charlength;
        var output = document.getElementById("output").value = totalcost;
    }
</script>
<?php
$CurrentTime = time();
if (!isset($_GET['action'])) {
    $_GET['action'] = '';
}
function csrf_error()
{
    global $h;
    alert('danger', "Action Blocked!", "The action you were trying to do was blocked. It was blocked because you loaded
        another page on the game. If you have not loaded a different page during this time, change your password
        immediately, as another person may have access to your account!");
    die($h->endpage());
}

switch ($_GET['action']) {
    case 'buyad':
        news_buy();
        break;
    default:
        news_home();
        break;
}
function news_home()
{
    global $db, $h, $CurrentTime, $parser, $userid, $adjustedCharCost, $adjustedDayCost, $adjustedInitCost, $set;
    $AdsQuery = $db->query("/*qc=on*/SELECT * FROM `newspaper_ads` WHERE `news_end` > {$CurrentTime} ORDER BY `news_cost` DESC");
    if ($db->num_rows($AdsQuery) == 0) {
        alert("danger", "Uh Oh!", "There aren't any newspaper ads at this time. Maybe you should <a href='?action=buyad'>list</a> one?", false);
        die($h->endpage());
    }
    echo "<h3>The Newspaper</h3>
	<small>List an ad <a href='?action=buyad'>here</a>. Listings begin at " . shortNumberParse($adjustedInitCost) . " Copper Coins!</small><hr />";
    echo "  <div class='card'>
                <div class='card-header'>
                    {$set['WebsiteName']} Newspaper
                </div>
                <div class='card-body'>";
    while ($Ads = $db->fetch_row($AdsQuery)) 
    {
        $parser->parse($Ads['news_text']);
        $UserName = $db->fetch_single($db->query("/*qc=on*/SELECT `username` FROM `users` WHERE `userid` = {$Ads['news_owner']}"));
        echo "  <div class='row'>
                    <div class='col-12 col-sm col-xl col-xxl-3'>
                        <div class='row'>
                            <div class='col-12'>
                                <small><b>Listing Info</b></small>
                            </div>
                            <div class='col-12'>
                                <div class='row'>
                                    <div class='col-12'>
                                        Owner: <a href='profile.php?user={$Ads['news_owner']}'>" . parseUsername($Ads['news_owner']) . "</a> " . parseUserID($Ads['news_owner']) . "
                                    </div>
                                    <div class='col-12'>
                                        <small>Cost: " . shortNumberParse($Ads['news_cost']) . " Copper Coins</small>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class='col-12 col-sm col-xl col-xxl-3'>
                        <div class='row'>
                            <div class='col-12'>
                                <small><b>Listing Time</b></small>
                            </div>
                            <div class='col-12'>
                                <div class='row'>
                                    <div class='col-12'>
                                        Starts " . DateTime_Parse($Ads['news_start']) . "
                                    </div>
                                    <div class='col-12'>
                                        Ends: " . TimeUntil_Parse($Ads['news_end']) . "
                                    </div>
                                </div>
                            </div>  
                        </div>
                    </div>
                    <div class='col-12 col-xl'>
                        <div class='row'>
                            <div class='col-12'>
                                <small><b>Listing Content</b></small>
                            </div>
                            <div class='col-12'>
                                {$parser->getAsHtml()}
                            </div>
                        </div>
                    </div>
                </div>";
    }
    echo "</div>";
}

function news_buy()
{
    global $db, $api, $h, $userid, $CurrentTime, $ir, $adjustedCharCost, $adjustedDayCost, $adjustedInitCost;
    if (isset($_POST['init_cost'])) {
        //Make sure POST is safe to work with
        $ad = $db->escape(nl2br(htmlentities(stripslashes($_POST['ad_text']), ENT_QUOTES, 'ISO-8859-1')));
        $initcost = (isset($_POST['init_cost']) && is_numeric($_POST['init_cost'])) ? abs($_POST['init_cost']) : 0;
        $days = (isset($_POST['ad_length']) && is_numeric($_POST['ad_length'])) ? abs($_POST['ad_length']) : 0;

        //Verify CSRF check has passed.
        if (!isset($_POST['verf']) || !verify_csrf_code("buy_ad", stripslashes($_POST['verf']))) {
            alert('danger', "Action Blocked!", "Forms expire fairly quickly. Be quicker next time.");
            die($h->endpage());
        }
        //Make sure form is filled out completely.
        if (empty($initcost) || empty($days) || empty($ad)) {
            alert('danger', "Uh Oh!", "You need to fill out the form completely before submitting.");
            die($h->endpage());
        }
        if ($initcost < $adjustedInitCost)
        {
            alert('danger', "Uh Oh!", "You must spend at least " . shortNumberParse($adjustedInitCost) . " Copper Coins on a newspaper ad listing.");
            die($h->endpage());
        }
        $charcost = ((strlen($ad)) * $adjustedCharCost);
        $daycost = $days * $adjustedDayCost;
        $totalcost = $daycost + $charcost + $initcost;
        //End Time
        $endtime=time()+(86400*$days);
		addToEconomyLog('Newspaper Fees', 'copper', $totalcost*-1);

        //Make sure user has the cash to buy this ad.
        if (!$api->UserHasCurrency($userid, 'primary', $totalcost)) {
            alert('danger', "Uh Oh!", "You do not have enough Copper Coins to place this ad. You need " . shortNumberParse($totalcost) . " Copper Coins.");
            die($h->endpage());
        }
		if ($days > 7)
		{
			alert('danger', "Uh Oh!", "An ad's maximum runtime may only be 7 days.");
            die($h->endpage());
		}
        $api->UserTakeCurrency($userid,'primary',$totalcost);
        alert('success',"Success!","You have successfully purchased a newspaper ad for " . shortNumberParse($totalcost) ." Copper Coins.",true,'newspaper.php');
        $db->query("INSERT INTO `newspaper_ads`
                    (`news_cost`, `news_start`, `news_end`, `news_owner`, `news_text`)
                    VALUES
                    ('{$totalcost}', '{$CurrentTime}', '{$endtime}', '{$userid}', '{$ad}')");

    } else {
        $csrf = request_csrf_html('buy_ad');
        echo "<h3>Buying an Ad</h3>
        " . alert("info", "Information!", "Remember, buying an ad is subject to the game rules. If you post something
            here that will break a game rule, you will be warned and your ad will be removed. If you find someone abusing
            the newspaper, please let an admin know immediately!", false) . "<hr />";
        echo "
            <form method='post'>
            <table class='table table-bordered'>
                <tr>
                    <td width='33%'>
                        Initial Ad Cost<br />
                        <small>A higher number will rank you higher on the ad list.</small>
                    </td>
                    <td>
                        <input type='number' value='{$adjustedInitCost}' min='{$adjustedInitCost}' name='init_cost' required='1' id='init' onkeyup='total_cost();' class='form-control'>
                    </td>
                </tr>
                <tr>
                    <td>
                        Ad Runtime<br />
                        <small>Each day will add " . shortNumberParse($adjustedDayCost) . " Copper Coins to your cost.</small>
                    </td>
                    <td>
                        <input type='number' value='1' min='1' max='7' name='ad_length' id='days' onkeyup='total_cost();' required='1' class='form-control'>
                    </td>
                </tr>
                <tr>
                    <td>
                        Ad Text<br />
                        <small>Each character is worth " . shortNumberParse($adjustedCharCost) . " Copper Coins.</small>
                    </td>
                    <td>
                        <textarea class='form-control' name='ad_text' id='chars' onkeyup='total_cost();' required='1'></textarea>
                    </td>
                </tr>
                <tr>
                    <td>
                        Total Ad Cost
                    </td>
                    <td>
                        <input type='number' name='ad_cost' id='output' readonly='1' class='form-control'>
                    </td>
                </tr>
                <tr>
                    <td colspan='2'>
                        <input type='submit' class='btn btn-primary' value='List Ad'>
                    </td>
                </tr>
            </table>
            {$csrf}
            </form>";
    }
}

$h->endpage();