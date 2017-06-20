<?php
/*
	File: staff/staff_mine.php
	Created: 4/4/2017 at 7:03PM Eastern Time
	Info: Staff panel for handling/editing/creating the in-game mines.
	Author: TheMasterGeneral
	Website: https://github.com/MasterGeneral156/chivalry-engine/
*/
require('sglobals.php');
echo "<h2>{$lang['STAFF_MINE_TITLE']}</h2><hr />";
if (!isset($_GET['action']))
{
    $_GET['action'] = '';
}
switch ($_GET['action'])
{
case 'addmine':
    addmine();
    break;
case 'editmine':
    editmine();
    break;
case 'delmine':
    delmine();
    break;
default:
    echo '404'; die($h->endpage());
    break;
}
function addmine()
{
    global $db,$ir,$userid, $lang, $api, $h;
    if (isset($_POST['level']) && (!empty($_POST['level'])))
    {
        $level=(isset($_POST['level']) && is_numeric($_POST['level'])) ? abs(intval($_POST['level'])) : '';
        $power=(isset($_POST['power']) && is_numeric($_POST['power'])) ? abs(intval($_POST['power'])) : '';
		$iq=(isset($_POST['IQ']) && is_numeric($_POST['IQ'])) ? abs(intval($_POST['IQ'])) : '';
        $pick=(isset($_POST['pick']) && is_numeric($_POST['pick'])) ? abs(intval($_POST['pick'])) : '';
        $city=(isset($_POST['city']) && is_numeric($_POST['city'])) ? abs(intval($_POST['city'])) : '';
        $cflakes=(isset($_POST['cflakes']) && is_numeric($_POST['cflakes'])) ? abs(intval($_POST['cflakes'])) : '';
        $cflakesmin=(isset($_POST['cflakemin']) && is_numeric($_POST['cflakemin'])) ? abs(intval($_POST['cflakemin'])) : '';
        $cflakesmax=(isset($_POST['cflakemax']) && is_numeric($_POST['cflakemax'])) ? abs(intval($_POST['cflakemax'])) : '';
        $sflakes=(isset($_POST['sflakes']) && is_numeric($_POST['sflakes'])) ? abs(intval($_POST['sflakes'])) : '';
        $sflakesmin=(isset($_POST['sflakemin']) && is_numeric($_POST['sflakemin'])) ? abs(intval($_POST['sflakemin'])) : '';
        $sflakesmax=(isset($_POST['sflakemax']) && is_numeric($_POST['sflakemax'])) ? abs(intval($_POST['sflakemax'])) : '';
        $gflakes=(isset($_POST['gflakes']) && is_numeric($_POST['gflakes'])) ? abs(intval($_POST['gflakes'])) : '';
        $gflakesmin=(isset($_POST['gflakemin']) && is_numeric($_POST['gflakemin'])) ? abs(intval($_POST['gflakemin'])) : '';
        $gflakesmax=(isset($_POST['gflakemax']) && is_numeric($_POST['gflakemax'])) ? abs(intval($_POST['gflakemax'])) : '';
        $gem=(isset($_POST['gem']) && is_numeric($_POST['gem'])) ? abs(intval($_POST['gem'])) : '';
        if (empty($level) || empty($iq) || empty($pick) || empty($city) || empty($cflakes) 
            || empty($cflakesmin) || empty($cflakesmax) || empty($gflakes) || empty($gflakesmin) 
        || empty($gflakesmax) || empty($sflakesmin) || empty($sflakesmax) || empty($sflakes) || empty($gem) || empty($power))
        {
            alert('danger',$lang['ERROR_GENERIC'],$lang['STAFF_MINE_ADD_ERROR']);
            die($h->endpage());
        }
        elseif ($level < 1)
        {
			alert('danger',$lang['ERROR_GENERIC'],$lang['STAFF_MINE_ADD_ERROR1']);
            die($h->endpage());
        }
        elseif ($cflakesmin == 0 || $cflakesmax == 0 || $gflakesmin == 0 || 
        $gflakesmax == 0 || $sflakesmin == 0 || $sflakesmax == 0)
        {
            alert('danger',$lang['ERROR_GENERIC'],$lang['STAFF_MINE_ADD_ERROR1']);
            die($h->endpage());
        }
        elseif ($cflakesmin >= $cflakesmax || $sflakesmin >= $sflakesmax || $gflakesmin >= $gflakesmax)
        {
            alert('danger',$lang['ERROR_GENERIC'],$lang['STAFF_MINE_ADD_ERROR2']);
            die($h->endpage());
        }
        else
        {
            $CitySQL=($db->query("SELECT `town_name` FROM `town` WHERE `town_id` = {$city}"));
            $PickSQL=($db->query("SELECT `itmname` FROM `items` WHERE `itmid` = {$pick}"));
            $CFSQL=($db->query("SELECT `itmname` FROM `items` WHERE `itmid` = {$cflakes}"));
            $SFSQL=($db->query("SELECT `itmname` FROM `items` WHERE `itmid` = {$sflakes}"));
            $GFSQL=($db->query("SELECT `itmname` FROM `items` WHERE `itmid` = {$gflakes}"));
            $GemSQL=($db->query("SELECT `itmname` FROM `items` WHERE `itmid` = {$gem}"));
            
            if ($db->num_rows($CitySQL) == 0)
            {
                alert('danger',$lang['ERROR_GENERIC'],$lang['STAFF_MINE_ADD_ERROR3']);
				die($h->endpage());
            }
            elseif ($db->num_rows($PickSQL) == 0)
            {
                alert('danger',$lang['ERROR_GENERIC'],$lang['STAFF_MINE_ADD_ERROR4']);
				die($h->endpage());
            }
            elseif ($db->num_rows($CFSQL) == 0)
            {
                alert('danger',$lang['ERROR_GENERIC'],$lang['STAFF_MINE_ADD_ERROR5']);
				die($h->endpage());
            }
            elseif ($db->num_rows($SFSQL) == 0)
            {
                alert('danger',$lang['ERROR_GENERIC'],$lang['STAFF_MINE_ADD_ERROR6']);
				die($h->endpage());
            }
            elseif ($db->num_rows($GFSQL) == 0)
            {
                alert('danger',$lang['ERROR_GENERIC'],$lang['STAFF_MINE_ADD_ERROR7']);
				die($h->endpage());
            }
            elseif ($db->num_rows($GemSQL) == 0)
            {
                alert('danger',$lang['ERROR_GENERIC'],$lang['STAFF_MINE_ADD_ERROR8']);
				die($h->endpage());
            }
            else
            {
                $db->query("INSERT INTO `mining_data` 
                (`mine_id`, `mine_location`, `mine_level`, 
                `mine_copper_min`, `mine_copper_max`, 
                `mine_silver_min`, `mine_silver_max`, 
                `mine_gold_min`, `mine_gold_max`, 
                `mine_pickaxe`, `mine_iq`, 
                `mine_power_use`, `mine_copper_item`, 
                `mine_silver_item`, `mine_gold_item`, 
                `mine_gem_item`) 
                VALUES 
                (NULL, '{$city}', '{$level}', '{$cflakesmin}', 
                '{$cflakesmax}', '{$sflakesmin}', '{$sflakesmax}', 
                '{$gflakesmin}', '{$gflakesmax}', '{$pick}', '{$iq}', 
                '{$power}', '{$cflakes}', '{$sflakes}', '{$gflakes}', 
                '{$gem}');");
				$api->SystemLogsAdd($userid,"staff","Added a mine in " . $api->SystemTownIDtoName($city));
				alert('success',$lang['ERROR_SUCCESS'],$lang['STAFF_MINE_ADD_SUCCESS']);
				die($h->endpage());
            }
            
        }
    }
    else
    {
        echo "
        <table class='table table-bordered'>
            <form method='post'>
				<tr>
					<th colspan='2'>
						{$lang['STAFF_MINE_ADD_FRMINFO']}
					</th>
				</tr>
                <tr>
                    <th>
						{$lang['STAFF_MINE_FORM_LOCATION']}
                    </th>
                    <td>
                        " . location_dropdown("city") . "
                    </td>
                </tr>
                <tr>
                    <th>
						{$lang['STAFF_MINE_FORM_LVL']}
                    </th>
                    <td>
                        <input type='number' class='form-control' name='level' min='1' required='1'> 
                    </td>
                </tr>
                <tr>
                    <th>
						{$lang['STAFF_MINE_FORM_IQ']}
                    </th>
                    <td>
                        <input type='number' class='form-control' name='IQ' min='1' required='1'> 
                    </td>
                </tr>
                <tr>
                    <th>
						{$lang['STAFF_MINE_FORM_PEPA']}
                    </th>
                    <td>
                        <input type='number' class='form-control' name='power' min='1' required='1'> 
                    </td>
                </tr>
                <tr>
                    <th>
						{$lang['STAFF_MINE_FORM_PICK']}
                    </th>
                    <td>
                        " . item_dropdown("pick") . "
                    </td>
                </tr>
                <tr>
                    <th>
						{$lang['STAFF_MINE_FORM_OP1']}
                    </th>
                    <td>
                        " . item_dropdown("cflakes") . "
                    </td>
                </tr>
                <tr>
                    <th>
						{$lang['STAFF_MINE_FORM_OP2']}
                    </th>
                    <td>
                        " . item_dropdown("sflakes") . "
                    </td>
                </tr>
                <tr>
                    <th>
						{$lang['STAFF_MINE_FORM_OP3']}
                    </th>
                    <td>
                        " . item_dropdown("gflakes") . "
                    </td>
                </tr>
                <tr>
                    <th>
						{$lang['STAFF_MINE_FORM_GEM']}
                    </th>
                    <td>
                        " . item_dropdown("gem") . "
                    </td>
                </tr>
                <tr>
                    <th>
						{$lang['STAFF_MINE_FORM_OP1MIN']}
                    </th>
                    <td>
                        <input type='number' class='form-control' name='cflakemin' min='1' required='1'>
                    </td>
                </tr>
                <tr>
                    <th>
						{$lang['STAFF_MINE_FORM_OP1MAX']}
                    </th>
                    <td>
                        <input type='number' class='form-control' name='cflakemax' min='2' required='1'>
                    </td>
                </tr>
                <tr>
                    <th>
                        {$lang['STAFF_MINE_FORM_OP2MIN']}
                    </th>
                    <td>
                        <input type='number' class='form-control' name='sflakemin' min='1' required='1'>
                    </td>
                </tr>
                <tr>
                    <th>
                        {$lang['STAFF_MINE_FORM_OP2MAX']}
                    </th>
                    <td>
                        <input type='number' class='form-control' name='sflakemax' min='2' required='1'>
                    </td>
                </tr>
                <tr>
                    <th>
                        {$lang['STAFF_MINE_FORM_OP3MIN']}
                    </th>
                    <td>
                        <input type='number' class='form-control' name='gflakemin' min='1' required='1'>
                    </td>
                </tr>
                <tr>
                    <th>
                        {$lang['STAFF_MINE_FORM_OP3MAX']}
                    </th>
                    <td>
                        <input type='number' class='form-control' name='gflakemax' min='2' required='1'>
                    </td>
                </tr>
                <tr>
                    <td colspan='2'>
                        <input type='submit' class='btn btn-secondary' value='{$lang['STAFF_MINE_ADD_BTN']}'>
                    </td>
                </tr>
            </form>
        </table>";
    }
}
function editmine()
{
    if (!isset($_POST['step']))
    {
        $_POST['step'] = 0;
    }
    global $db,$ir,$userid,$h,$api,$lang;
    if (isset($_POST['level']) && (!empty($_POST['level'])) && $_POST['step'] == 2)
    {
        $mine=(isset($_POST['mineid']) && is_numeric($_POST['mineid'])) ? abs(intval($_POST['mineid'])) : '';
        $level=(isset($_POST['level']) && is_numeric($_POST['level'])) ? abs(intval($_POST['level'])) : '';
        $power=(isset($_POST['power']) && is_numeric($_POST['power'])) ? abs(intval($_POST['power'])) : '';
        $iq=(isset($_POST['iq']) && is_numeric($_POST['iq'])) ? abs(intval($_POST['iq'])) : '';
        $pick=(isset($_POST['pick']) && is_numeric($_POST['pick'])) ? abs(intval($_POST['pick'])) : '';
        $city=(isset($_POST['city']) && is_numeric($_POST['city'])) ? abs(intval($_POST['city'])) : '';
        $cflakes=(isset($_POST['cflakes']) && is_numeric($_POST['cflakes'])) ? abs(intval($_POST['cflakes'])) : '';
        $cflakesmin=(isset($_POST['cflakemin']) && is_numeric($_POST['cflakemin'])) ? abs(intval($_POST['cflakemin'])) : '';
        $cflakesmax=(isset($_POST['cflakemax']) && is_numeric($_POST['cflakemax'])) ? abs(intval($_POST['cflakemax'])) : '';
        $sflakes=(isset($_POST['sflakes']) && is_numeric($_POST['sflakes'])) ? abs(intval($_POST['sflakes'])) : '';
        $sflakesmin=(isset($_POST['sflakemin']) && is_numeric($_POST['sflakemin'])) ? abs(intval($_POST['sflakemin'])) : '';
        $sflakesmax=(isset($_POST['sflakemax']) && is_numeric($_POST['sflakemax'])) ? abs(intval($_POST['sflakemax'])) : '';
        $gflakes=(isset($_POST['gflakes']) && is_numeric($_POST['gflakes'])) ? abs(intval($_POST['gflakes'])) : '';
        $gflakesmin=(isset($_POST['gflakemin']) && is_numeric($_POST['gflakemin'])) ? abs(intval($_POST['gflakemin'])) : '';
        $gflakesmax=(isset($_POST['gflakemax']) && is_numeric($_POST['gflakemax'])) ? abs(intval($_POST['gflakemax'])) : '';
        $gem=(isset($_POST['gem']) && is_numeric($_POST['gem'])) ? abs(intval($_POST['gem'])) : '';
        if (empty($level) || empty($iq) || empty($pick) || empty($city) || empty($cflakes) 
            || empty($cflakesmin) || empty($cflakesmax) || empty($gflakes) || empty($gflakesmin) 
        || empty($gflakesmax) || empty($sflakesmin) || empty($sflakesmax) || empty($sflakes) || empty($gem) || empty($power))
        {
            alert('danger',$lang['ERROR_GENERIC'],$lang['STAFF_MINE_ADD_ERROR']);
            die($h->endpage());
        }
        elseif ($level < 1)
        {
			alert('danger',$lang['ERROR_GENERIC'],$lang['STAFF_MINE_ADD_ERROR1']);
            die($h->endpage());
        }
        elseif ($cflakesmin == 0 || $cflakesmax == 0 || $gflakesmin == 0 || 
        $gflakesmax == 0 || $sflakesmin == 0 || $sflakesmax == 0)
        {
            alert('danger',$lang['ERROR_GENERIC'],$lang['STAFF_MINE_ADD_ERROR1']);
            die($h->endpage());
        }
        elseif ($cflakesmin >= $cflakesmax || $sflakesmin >= $sflakesmax || $gflakesmin >= $gflakesmax)
        {
            alert('danger',$lang['ERROR_GENERIC'],$lang['STAFF_MINE_ADD_ERROR2']);
            die($h->endpage());
        }
        else
        {
            $CitySQL=($db->query("SELECT `town_name` FROM `town` WHERE `town_id` = {$city}"));
            $PickSQL=($db->query("SELECT `itmname` FROM `items` WHERE `itmid` = {$pick}"));
            $CFSQL=($db->query("SELECT `itmname` FROM `items` WHERE `itmid` = {$cflakes}"));
            $SFSQL=($db->query("SELECT `itmname` FROM `items` WHERE `itmid` = {$sflakes}"));
            $GFSQL=($db->query("SELECT `itmname` FROM `items` WHERE `itmid` = {$gflakes}"));
            $GemSQL=($db->query("SELECT `itmname` FROM `items` WHERE `itmid` = {$gem}"));
            if ($db->num_rows($CitySQL) == 0)
            {
                alert('danger',$lang['ERROR_GENERIC'],$lang['STAFF_MINE_ADD_ERROR3']);
				die($h->endpage());
            }
            elseif ($db->num_rows($PickSQL) == 0)
            {
                alert('danger',$lang['ERROR_GENERIC'],$lang['STAFF_MINE_ADD_ERROR4']);
				die($h->endpage());
            }
            elseif ($db->num_rows($CFSQL) == 0)
            {
                alert('danger',$lang['ERROR_GENERIC'],$lang['STAFF_MINE_ADD_ERROR5']);
				die($h->endpage());
            }
            elseif ($db->num_rows($SFSQL) == 0)
            {
                alert('danger',$lang['ERROR_GENERIC'],$lang['STAFF_MINE_ADD_ERROR6']);
				die($h->endpage());
            }
            elseif ($db->num_rows($GFSQL) == 0)
            {
                alert('danger',$lang['ERROR_GENERIC'],$lang['STAFF_MINE_ADD_ERROR7']);
				die($h->endpage());
            }
            elseif ($db->num_rows($GemSQL) == 0)
            {
                alert('danger',$lang['ERROR_GENERIC'],$lang['STAFF_MINE_ADD_ERROR8']);
				die($h->endpage());
            }
            else
            {
                $db->query("UPDATE `mining_data` SET `mine_location` = '{$city}', `mine_level` = '{$level}',
                `mine_copper_item` = '{$cflakes}', `mine_copper_max` = '{$cflakesmax}', `mine_copper_min` = '{$cflakesmin}', 
                `mine_silver_item` = '{$sflakes}', `mine_silver_max` = '{$sflakesmax}', `mine_silver_min` = '{$sflakesmin}', 
                `mine_gold_item` = '{$gflakes}', `mine_gold_max` = '{$gflakesmax}', `mine_gold_min` = '{$gflakesmin}', 
                `mine_pickaxe` = '{$pick}', `mine_iq` = '{$iq}', `mine_gem_item` = '{$gem}', `mine_power_use` = '{$power}' 
                WHERE `mine_id` = {$mine}");
                $api->SystemLogsAdd($userid,"staff","Edited Mine ID #{$mine}");
				alert('success',$lang['ERROR_SUCCESS'],$lang['STAFF_MINE_EDIT_SUCCESS'],true,'index.php');
				die($h->endpage());
            }
            
        }
    }
    elseif ($_POST['step'] == 1)
    {
        $mine=(isset($_POST['mine']) && is_numeric($_POST['mine'])) ? abs(intval($_POST['mine'])) : '';
        if ($db->num_rows($db->query("SELECT * FROM `mining_data` WHERE `mine_id` = {$mine}")) == 0)
        {
            alert('danger',$lang['ERROR_GENERIC'],$lang['STAFF_MINE_EDIT_ERR']);
			die($h->endpage());
        }
        else
        {
            $mi=$db->fetch_row($db->query("SELECT * FROM `mining_data` WHERE `mine_id` = {$mine}"));
            echo "{$lang['STAFF_MINE_EDIT2']}<br />
            <table class='table table-bordered'>
                <form method='post'>
                <input type='hidden' value='2' name='step'>
                <input type='hidden' value='{$mine}' name='mineid'>
                    <tr>
                        <th>
							{$lang['STAFF_MINE_FORM_LOCATION']}
                        </th>
                        <td>
                            " . location_dropdown("city", $mi['mine_location']) . "
                        </td>
                    </tr>
                    <tr>
                        <th>
                            {$lang['STAFF_MINE_FORM_LVL']}
                        </th>
                        <td>
                            <input type='number' class='form-control' name='level' min='1' value='{$mi['mine_level']}' required='1'> 
                        </td>
                    </tr>
                    <tr>
                        <th>
                            {$lang['STAFF_MINE_FORM_IQ']}
                        </th>
                        <td>
                            <input type='number' class='form-control' name='iq' min='1' value='{$mi['mine_iq']}' required='1'> 
                        </td>
                    </tr>
                    <tr>
                        <th>
                            {$lang['STAFF_MINE_FORM_PEPA']}
                        </th>
                        <td>
                            <input type='number' class='form-control' name='power' min='1' value='{$mi['mine_power_use']}' required='1'> 
                        </td>
                    </tr>
                    <tr>
                        <th>
                            {$lang['STAFF_MINE_FORM_PICK']}
                        </th>
                        <td>
                            " . item_dropdown("pick", $mi['mine_pickaxe']) . "
                        </td>
                    </tr>
                    <tr>
                        <th>
                            {$lang['STAFF_MINE_FORM_OP1']}
                        </th>
                        <td>
                            " . item_dropdown("cflakes", $mi['mine_copper_item']) . "
                        </td>
                    </tr>
                    <tr>
                        <th>
                            {$lang['STAFF_MINE_FORM_OP2']}
                        </th>
                        <td>
                            " . item_dropdown("sflakes", $mi['mine_silver_item']) . "
                        </td>
                    </tr>
                    <tr>
                        <th>
                            {$lang['STAFF_MINE_FORM_OP3']}
                        </th>
                        <td>
                            " . item_dropdown("gflakes", $mi['mine_gold_item']) . "
                        </td>
                    </tr>
                    <tr>
                        <th>
                            {$lang['STAFF_MINE_FORM_GEM']}
                        </th>
                        <td>
                            " . item_dropdown("gem", $mi['mine_gem_item']) . "
                        </td>
                    </tr>
                    <tr>
                        <th>
                            {$lang['STAFF_MINE_FORM_OP1MIN']}
                        </th>
                        <td>
                            <input type='number' class='form-control' name='cflakemin' value='{$mi['mine_copper_min']}' min='1' required='1'>
                        </td>
                    </tr>
                    <tr>
                        <th>
                            {$lang['STAFF_MINE_FORM_OP1MAX']}
                        </th>
                        <td>
                            <input type='number' class='form-control' name='cflakemax' value='{$mi['mine_copper_max']}' min='2' required='1'>
                        </td>
                    </tr>
                    <tr>
                        <th>
                            {$lang['STAFF_MINE_FORM_OP2MIN']}
                        </th>
                        <td>
                            <input type='number' class='form-control' name='sflakemin' value='{$mi['mine_silver_min']}' min='1' required='1'>
                        </td>
                    </tr>
                    <tr>
                        <th>
                            {$lang['STAFF_MINE_FORM_OP2MAX']}
                        </th>
                        <td>
                            <input type='number' class='form-control' name='sflakemax' value='{$mi['mine_silver_max']}' min='2' required='1'>
                        </td>
                    </tr>
                    <tr>
                        <th>
                            {$lang['STAFF_MINE_FORM_OP3MIN']}
                        </th>
                        <td>
                            <input type='number' class='form-control' name='gflakemin' value='{$mi['mine_gold_min']}' min='1' required='1'>
                        </td>
                    </tr>
                    <tr>
                        <th>
                            {$lang['STAFF_MINE_FORM_OP3MAX']}
                        </th>
                        <td>
                            <input type='number' class='form-control' name='gflakemax' value='{$mi['mine_gold_max']}' min='2' required='1'>
                        </td>
                    </tr>
                    <tr>
                        <td colspan='2'>
                            <center><input type='submit' class='btn btn-secondary' value='{$lang['STAFF_MINE_EDIT_BTN']}'></center>
                        </td>
                    </tr>
                </form>
            </table>";
        }
    }
    else
    {
        echo "{$lang['STAFF_MINE_EDIT1']}<br />
        <form method='post'>
        <input type='hidden' name='step' value='1'>
        " . mines_dropdown("mine") . "<br />
        <input type='submit' class='btn btn-secondary' value='{$lang['STAFF_MINE_EDIT_BTN']}'>
        ";
    }
}
function delmine()
{
    global $db,$lang,$userid,$lang,$api,$h;
    if (isset($_POST['mine']))
    {
        $mine=(isset($_POST['mine']) && is_numeric($_POST['mine'])) ? abs(intval($_POST['mine'])) : '';
        if ($db->num_rows($db->query("SELECT * FROM `mining_data` WHERE `mine_id` = {$mine}")) == 0)
        {
            alert('danger',$lang['ERROR_GENERIC'],$lang['STAFF_MINE_EDIT_ERR']);
			die($h->endpage());
        }
        else
        {
            $db->query("DELETE FROM `mining_data` WHERE `mine_id` = {$mine}");
            $api->SystemLogsAdd($userid,"staff","Deleted a mine.");
			alert('success',$lang['ERROR_SUCCESS'],$lang['STAFF_MINE_DEL_SUCCESS'],true,'index.php');
			die($h->endpage());
        }
    }
    else
    {
        echo "{$lang['STAFF_MINE_DEL1']}<br />
        <form method='post'>
        " . mines_dropdown("mine") . "<br />
        <input type='submit' class='btn btn-secondary' value='{$lang['STAFF_MINE_DEL_BTN']}'>
        ";
    }
}
function mines_dropdown($ddname = "mine", $selected = -1)
{
    global $db;
    $ret = "<select name='$ddname' class='form-control' type='dropdown'>";
    $q =
            $db->query(
                    "SELECT `mine_id`, `mine_location`, `mine_level`
                     FROM `mining_data`
                     ORDER BY `mine_level` ASC");
    if ($selected == -1)
    {
        $first = 0;
    }
    else
    {
        $first = 1;
    }
    while ($r = $db->fetch_row($q))
    {
        $CityName=$db->fetch_single($db->query("SELECT `town_name` FROM `town` WHERE `town_id` = {$r['mine_location']}"));
        $ret .= "\n<option value='{$r['mine_id']}'";
        if ($selected == $r['mine_id'] || $first == 0)
        {
            $ret .= " selected='selected'";
            $first = 1;
        }
        $ret .= ">{$CityName} - Level {$r['mine_level']}</option>";
    }
    $db->free_result($q);
    $ret .= "\n</select>";
    return $ret;
}
$h->endpage();