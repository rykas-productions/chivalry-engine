<script>
    function total_cost() {
        var day = parseInt((document.getElementById("days").value) * 1250);
        var init = parseInt((document.getElementById("init").value));
        var charlength = parseInt((document.getElementById("chars").value.length) * 5);
        var totalcost = day + init + charlength;
        var output = document.getElementById("output").value = totalcost;
    }
</script>
<?php
/*
	File:		newspaper.php
	Created: 	4/5/2016 at 12:18AM Eastern Time
	Info: 		Allows players to place ads in a newspaper, and view
				currently running ads.
	Author:		TheMasterGeneral
	Website: 	https://github.com/MasterGeneral156/chivalry-engine
*/
require("globals.php");
require('lib/bbcode_engine.php');
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
    global $db, $h, $CurrentTime, $parser;
    $AdsQuery = $db->query("/*qc=on*/SELECT * FROM `newspaper_ads` WHERE `news_end` > {$CurrentTime} ORDER BY `news_cost` ASC");
    if ($db->num_rows($AdsQuery) == 0) {
        alert("danger", "Uh Oh!", "There aren't any newspaper ads at this time. Maybe you should <a href='?action=buyad'>list</a> one?", false);
        die($h->endpage());
    }
    echo "<h3>The Newspaper</h3>
	<small>List an ad <a href='?action=buyad'>here</a>.<hr />";
    echo "
		<table class='table table-bordered'>
			<thead>
				<tr>
					<th width='33%'>
						Ad Info
					</th>
					<th>
						Ad Content
					</th>
				</tr>
			</thead>
			<tbody>
	";
    while ($Ads = $db->fetch_row($AdsQuery)) {
        $parser->parse($Ads['news_text']);
        $UserName = $db->fetch_single($db->query("/*qc=on*/SELECT `username` FROM `users` WHERE `userid` = {$Ads['news_owner']}"));
        echo "	<tr>
					<td>
						Posted By <a href='profile.php?user={$Ads['news_owner']}'>" . parseUsername($Ads['news_owner']) . "</a> [{$Ads['news_owner']}]<br />
						<small>Posted At: " . DateTime_Parse($Ads['news_start']) . "<br />
						Ad Ends: " . date('F j, Y g:i:s a', $Ads['news_end']) . "</small>
					</td>
					<td>
						{$parser->getAsHtml()}
					</td>
				</tr>";
    }
    echo "</tbody></table>";
}

function news_buy()
{
    global $db, $api, $h, $userid, $CurrentTime;
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
        //Add up the costs
        $charcost = ((strlen($ad)) * 5);
        $daycost = $days * 1250;
        $totalcost = $daycost + $charcost + $initcost;
        //End Time
        $endtime=time()+(86400*$days);

        //Make sure user has the cash to buy this ad.
        if (!$api->UserHasCurrency($userid, 'primary', $totalcost)) {
            alert('danger', "Uh Oh!", "You do not have enough Copper Coins to place this ad.");
            die($h->endpage());
        }
        $api->UserTakeCurrency($userid,'primary',$totalcost);
        alert('success',"Success!","You have successfully purchased a newspaper ad.",true,'newspaper.php');
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
                        <input type='number' value='25000' min='25000' name='init_cost' required='1' id='init' onkeyup='total_cost();' class='form-control'>
                    </td>
                </tr>
                <tr>
                    <td>
                        Ad Runtime<br />
                        <small>Each day will add 1,250 Copper Coins to your cost.</small>
                    </td>
                    <td>
                        <input type='number' value='1' min='1' name='ad_length' id='days' onkeyup='total_cost();' required='1' class='form-control'>
                    </td>
                </tr>
                <tr>
                    <td>
                        Ad Text<br />
                        <small>Each character is worth 5 Copper Coins.</small>
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