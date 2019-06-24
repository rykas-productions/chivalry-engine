<?php
/*
	File:		criminal.php
	Created: 	6/23/2019 at 6:11PM Eastern Time
	Info: 		Allows the player to view in-game crimes, and attempt to 
				commit one.
	Author:		TheMasterGeneral
	Website: 	https://github.com/MasterGeneral156/chivalry-engine
	MIT License

	Copyright (c) 2019 TheMasterGeneral

	Permission is hereby granted, free of charge, to any person obtaining a copy
	of this software and associated documentation files (the "Software"), to deal
	in the Software without restriction, including without limitation the rights
	to use, copy, modify, merge, publish, distribute, sublicense, and/or sell
	copies of the Software, and to permit persons to whom the Software is
	furnished to do so, subject to the following conditions:

	The above copyright notice and this permission notice shall be included in all
	copies or substantial portions of the Software.

	THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND, EXPRESS OR
	IMPLIED, INCLUDING BUT NOT LIMITED TO THE WARRANTIES OF MERCHANTABILITY,
	FITNESS FOR A PARTICULAR PURPOSE AND NONINFRINGEMENT. IN NO EVENT SHALL THE
	AUTHORS OR COPYRIGHT HOLDERS BE LIABLE FOR ANY CLAIM, DAMAGES OR OTHER
	LIABILITY, WHETHER IN AN ACTION OF CONTRACT, TORT OR OTHERWISE, ARISING FROM,
	OUT OF OR IN CONNECTION WITH THE SOFTWARE OR THE USE OR OTHER DEALINGS IN THE
	SOFTWARE.
*/
$macropage = ('criminal.php');
require('globals.php');
include('class/class_evalmath.php');
$m = new EvalMath;
echo "<h3>Criminal Center</h3>";
if ($api->user->inInfirmary($ir['userid']) || $api->user->inDungeon($ir['userid'])) {
    alert('danger', "Uh Oh!", "You cannot commit crimes while in the infirmary or dungeon.");
    die($h->endpage());
}
if (!isset($_GET['action'])) {
    $_GET['action'] = '';
}
switch ($_GET['action']) {
    case 'crime':
        crime();
        break;
    default:
        home();
        break;
}
function home()
{
    global $db, $h;
    $crimes = array();
    $q2 = $db->query("SELECT `crimeGROUP`, `crimeNAME`, `crimeBRAVE`, `crimeID` FROM `crimes` ORDER BY `crimeBRAVE` ASC");
    while ($r2 = $db->fetch_row($q2)) {
        $crimes[] = $r2;
    }
    //Anti-refresh RNG.
    $tresder = (randomNumber(100, 999));
    $db->free_result($q2);
    $q = $db->query("SELECT `cgID`, `cgNAME` FROM `crimegroups` ORDER BY `cgORDER` ASC");
    echo "<div class='cotainer'>
<div class='row'>
		<div class='col-sm'>
		    <h4>Crime</h4>
		</div>
		<div class='col-sm'>
		    <h4>Brave</h4>
		</div>
		<div class='col-sm'>
		    <h4>Action</h4>
		</div>
</div><hr />";
    while ($r = $db->fetch_row($q)) {
        echo "<div class='row'><div class='col-sm'>{$r['cgNAME']} Crimes</div></div>";
        foreach ($crimes as $v) {
            if ($v['crimeGROUP'] == $r['cgID']) {
                echo "<div class='row'>
						<div class='col-sm'>
							{$v['crimeNAME']}
						</div>
						<div class='col-sm'>
							{$v['crimeBRAVE']}
						</div>
						<div class='col-sm'>
							<a href='?action=crime&c={$v['crimeID']}&tresde={$tresder}'>
								Commit Crime
							</a>
						</div>
					</div>
					<hr />";
            }
        }
    }
    $db->free_result($q);
    echo "</div>";
    $h->endpage();
}

function crime()
{
    global $db, $userid, $ir, $h, $api, $m;
    $tresder = (randomNumber(100, 999));
	$tresde = filter_input(INPUT_GET, 'tresde', FILTER_SANITIZE_NUMBER_INT) ?: 0;
    if (!isset($c)) {
        $c = 0;
    }
	$c = filter_input(INPUT_GET, 'c', FILTER_SANITIZE_NUMBER_INT) ?: 0;
    if (!isset($_SESSION['tresde'])) {
        $_SESSION['tresde'] = 0;
    }
    if (($_SESSION['tresde'] == $tresde) || $tresde < 100) {
        alert('danger', "Uh Oh!", "Please do not refresh while committing crimes, thank you!", true, "?c={$c}&tresde={$tresder}");
        $_SESSION['number'] = 0;
        die($h->endpage());
    }
    $_SESSION['tresde'] = $tresde;
    if ($c <= 0) {
        alert('danger', "Invalid Crime!", "You have chosen to commit and invalid crime.", true, 'criminal.php');
    } else {
        $q = $db->query("SELECT * FROM `crimes` WHERE `crimeID` = {$c} LIMIT 1");
        if ($db->num_rows($q) == 0) {
            alert('danger', "Invalid Crime!", "You are trying to commit a non-existent crime.", true, 'criminal.php');
            die($h->endpage());
        }
        $r = $db->fetch_row($q);
        $db->free_result($q);
        if ($ir['brave'] < $r['crimeBRAVE']) {
            alert('danger', "Uh Oh!", "You do not have enough to commit this crime. You only have {$ir['brave']}", true, 'criminal.php');
            die($h->endpage());
        } else {
            //Fix from Kyle Massacre. Thanks!
            //https://github.com/KyleMassacre
            $ec = str_ireplace(array("LEVEL", "EXP", "WILL", "IQ"), array($ir['level'], $ir['xp'], $ir['will'], $ir['iq']), $r['crimePERCFORM']) . ";";
            $tokens = token_get_all("<?php {$ec}");
            $expr = '';
            foreach($tokens as $token)
            {
                if(is_string($token))
                {
                    if(in_array($token, array('(', ')', '+', '-', '/', '*'), true))
                        $expr .= $token;
                    continue;
                }
                list($id, $text) = $token;
                if(in_array($id, array(T_DNUMBER, T_LNUMBER)))
                    $expr .= $text;
            }
            try 
            {
                $sucrate=$m->evaluate($expr); 
            }
            catch (\Error $e)
            {
                alert('danger',"Uh Oh!","There's an issue with this crime. Please contact the game administration.",true,'criminal.php');
                die($h->endpage());
            }
            if (!$sucrate)
            {
                alert('danger',"Uh Oh!","There's an issue with this crime. Please contact the game administration.",true,'criminal.php');
                die($h->endpage());
            }
            $ir['brave'] -= $r['crimeBRAVE'];
            $api->user->setInfo($userid, "brave", "-{$r['crimeBRAVE']}");
            if (randomNumber(1, 100) <= $sucrate) {
                if (!empty($r['crimePRICURMIN'])) {
                    $prim_currency = randomNumber($r['crimePRICURMIN'], $r['crimePRICURMAX']);
                    $api->user->giveCurrency($userid, 'primary', $prim_currency);
                }
                if (!empty($r['crimeSECURMIN'])) {
                    $sec_currency = randomNumber($r['crimeSECURMIN'], $r['crimeSECCURMAX']);
                    $api->user->giveCurrency($userid, 'secondary', $sec_currency);
                }
                if (!empty($r['crimeSUCCESSITEM'])) {
                    $api->user->giveItem($userid, $r['crimeSUCCESSITEM'], 1);
                }
                if (empty($prim_currency)) {
                    $prim_currency = 0;
                }
                if (empty($sec_currency)) {
                    $sec_currency = 0;
                }
                if (empty($r['crimeSUCCESSITEM'])) {
                    $r['crimeSUCCESSITEM'] = 0;
                }
                $text = str_replace("{money}", $prim_currency, $r['crimeSTEXT']);
                $text = str_replace("{secondary}", $sec_currency, $r['crimeSTEXT']);
                $text = str_replace("{item}", $api->game->getItemNameFromID($r['crimeSUCCESSITEM']), $r['crimeSTEXT']);
                $title = "Success!";
                $type = 'success';
                $api->user->setInfo($userid, "xp", $r['crimeXP']);
                $api->game->addLog($userid, 'crime', "Successfully committed the {$r['crimeNAME']} crime.");
            } else {
                $title = "Uh Oh!";
                $type = 'danger';
                $dtime = randomNumber($r['crimeDUNGMIN'], $r['crimeDUNGMAX']);
                $text = str_replace("{time}", $dtime, $r['crimeFTEXT']);
                $api->user->setDungeon($userid, $dtime, $r['crimeDUNGREAS']);
                $api->game->addLog($userid, 'crime', "Failed to commit the {$r['crimeNAME']} crime.");
            }
            alert("{$type}", "{$title}", "{$r['crimeITEXT']} {$text}", true, 'criminal.php');
            die($h->endpage());
        }
    }
}