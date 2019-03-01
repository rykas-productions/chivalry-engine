<?php
/*
	File:		criminal.php
	Created: 	4/4/2016 at 11:55PM Eastern Time
	Info: 		Lists created crimes for players to commit.
	Author:		TheMasterGeneral
	Website: 	https://github.com/MasterGeneral156/chivalry-engine
*/
$macropage = ('criminal.php');
require('globals.php');
include('class/class_evalmath.php');
$m = new EvalMath;
echo "<h3>Criminal Center</h3>";
if ($api->UserStatus($ir['userid'], 'infirmary') || $api->UserStatus($ir['userid'], 'dungeon')) {
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
    $tresder = (Random(100, 999));
    $db->free_result($q2);
    $q = $db->query("SELECT `cgID`, `cgNAME` FROM `crimegroups` ORDER BY `cgORDER` ASC");
    echo "
	<table class='table table-bordered'>
		<tr>
			<th>
				Crime
			</th>
			<th>
				Bravery Cost
			</th>
			<th>
				Commit
			</th>
		</tr>";
    while ($r = $db->fetch_row($q)) {
        echo "<tr><td colspan='3' class='h'>{$r['cgNAME']} Crimes</td></tr>";
        foreach ($crimes as $v) {
            if ($v['crimeGROUP'] == $r['cgID']) {
                echo "<tr>
						<td>
							{$v['crimeNAME']}
						</td>
						<td>
							{$v['crimeBRAVE']}
						</td>
						<td>
							<a href='?action=crime&c={$v['crimeID']}&tresde={$tresder}'>
								Commit Crime
							</a>
						</td>
					</tr>";
            }
        }
    }
    $db->free_result($q);
    echo "</table>";
    $h->endpage();
}

function crime()
{
    global $db, $userid, $ir, $h, $api, $m;
    $tresder = (Random(100, 999));
    $_GET['tresde'] = (isset($_GET['tresde']) && is_numeric($_GET['tresde'])) ? abs($_GET['tresde']) : 0;
    if (!isset($_GET['c'])) {
        $_GET['c'] = 0;
    }
    $_GET['c'] = abs($_GET['c']);
    if (!isset($_SESSION['tresde'])) {
        $_SESSION['tresde'] = 0;
    }
    if (($_SESSION['tresde'] == $_GET['tresde']) || $_GET['tresde'] < 100) {
        alert('danger', "Uh Oh!", "Please do not refresh while committing crimes, thank you!", true, "?c={$_GET['c']}&tresde={$tresder}");
        $_SESSION['number'] = 0;
        die($h->endpage());
    }
    $_SESSION['tresde'] = $_GET['tresde'];
    if ($_GET['c'] <= 0) {
        alert('danger', "Invalid Crime!", "You have chosen to commit and invalid crime.", true, 'criminal.php');
    } else {
        $q = $db->query("SELECT * FROM `crimes` WHERE `crimeID` = {$_GET['c']} LIMIT 1");
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
            $api->UserInfoSet($userid, "brave", "-{$r['crimeBRAVE']}");
            if (Random(1, 100) <= $sucrate) {
                if (!empty($r['crimePRICURMIN'])) {
                    $prim_currency = Random($r['crimePRICURMIN'], $r['crimePRICURMAX']);
                    $api->user->giveCurrency($userid, 'primary', $prim_currency);
                }
                if (!empty($r['crimeSECURMIN'])) {
                    $sec_currency = Random($r['crimeSECURMIN'], $r['crimeSECCURMAX']);
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
                $text = str_replace("{item}", $api->SystemItemIDtoName($r['crimeSUCCESSITEM']), $r['crimeSTEXT']);
                $title = "Success!";
                $type = 'success';
                $api->user->setInfo($userid, "xp", $r['crimeXP']);
                $api->SystemLogsAdd($userid, 'crime', "Successfully committed the {$r['crimeNAME']} crime.");
            } else {
                $title = "Uh Oh!";
                $type = 'danger';
                $dtime = Random($r['crimeDUNGMIN'], $r['crimeDUNGMAX']);
                $text = str_replace("{time}", $dtime, $r['crimeFTEXT']);
                $api->user->setDungeon($userid, $dtime, $r['crimeDUNGREAS']);
                $api->SystemLogsAdd($userid, 'crime', "Failed to commit the {$r['crimeNAME']} crime.");
            }
            alert("{$type}", "{$title}", "{$r['crimeITEXT']} {$text}", true, 'criminal.php');
            die($h->endpage());
        }
    }
}