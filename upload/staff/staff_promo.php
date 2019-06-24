<?php
/*
	File: 		staff/staff_promo.php
	Created: 	6/23/2019 at 6:11PM Eastern Time
	Info: 		Allows staff to add or remove registration promotion codes.
	Author: 	TheMasterGeneral
	Website: 	https://github.com/MasterGeneral156/chivalry-engine/
	
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
require('sglobals.php');
//User is not an admin, so redirect them back to the main index.
if (!$api->user->getStaffLevel($userid, 'admin')) {
    alert('danger', "Uh Oh!", "You do not have permission to be here!", true, 'index.php');
    die($h->endpage());
}
//Action choice is not set, so set it to nothing.
if (!isset($_GET['action'])) {
    $_GET['action'] = '';
}
//Action choice switch. If its one of the predefined cases, load its function. If not, tell them something went wrong.
switch ($_GET['action']) {
    case "addpromo":
        add();
        break;
    case "deletepromo":
        deletepromo();
        break;
    case "viewpromo":
        viewpromo();
        break;
    default:
        alert('danger', "Uh Oh!", "Please select a valid action to perform.", true, 'index.php');
        die($h->endpage());
        break;;
}
//Function for adding a promo code to the game.
function add()
{
    echo "<h3>Add Promo Code</h3><hr />";
    global $db, $userid, $api, $h;
    //The form has been submitted.
    if (isset($_POST['code'])) {
        //Sanitize and validate the variables from the previous form.
        $code = (isset($_POST['code'])) ? $db->escape(strip_tags(stripslashes($_POST['code']))) : '';
        $item = (isset($_POST['item']) && is_numeric($_POST['item'])) ? abs(intval($_POST['item'])) : 0;
        //User has failed the CSRF check
        if (!isset($_POST['verf']) || !checkCSRF('staff_promo_add', stripslashes($_POST['verf']))) {
            alert('danger', "Action Blocked!", "We have blocked this action for your security. Please fill out the form
            quickly next time.");
            die($h->endpage());
        }
        //Promo code not set.
        if (empty($code)) {
            alert('danger', "Uh Oh!", "Please specify the promotion code.");
            die($h->endpage());
        }
        //Item not set.
        if (empty($item)) {
            alert('danger', "Uh Oh!", "Please specify the item the player should receive by redeeming this code.");
            die($h->endpage());
        }
        //Check that the item actually exists, and if it doesn't, stop the creation of this promo code.
        $q1 = $db->query("SELECT `itmname` FROM `items` WHERE `itmid` = {$item}");
        if ($db->num_rows($q1) == 0) {
            alert('danger', "Uh Oh!", "Please select an existing item to give.");
            die($h->endpage());
        }
        //Check that the promo code entered is not in use, and stop creation if it is.
        $q2 = $db->query("SELECT `promo_id` FROM `promo_codes` WHERE `promo_code` = '{$code}'");
        if ($db->num_rows($q2) > 0) {
            alert('danger', "Uh Oh!", "Please specify an unused name for this promo code..");
            die($h->endpage());
        }
        //All tests passed... so create the promo code?
        $db->query("INSERT INTO `promo_codes`
                    (`promo_code`, `promo_item`, `promo_use`)
                    VALUES  ('{$code}', {$item}, 0)");
        $api->game->addLog($userid, 'staff', "Added Promotion Code '{$code}'.'");
        alert('success', "Success!", "You have successfully added Promotion Code '{$code}' to the game.", true, "index.php");
        $h->endpage();
    } else {
        //Create the form
        $csrf = getHtmlCSRF('staff_promo_add');
        echo "<form method='post'>";
        echo "<table class='table table-bordered'>
        <tr>
            <th colspan='2'>
                Fill out this form to add a promo code to the game.
            </th>
        </tr>
        <tr>
            <th>
                Promo Code
            </th>
            <td>
                <input name='code' required='1' class='form-control'>
            </td>
        </tr>
        <tr>
            <th>
                Promo Item
            </th>
            <td>
                " . dropdownItem() . "
            </td>
        </tr>
        <tr>
            <td colspan='2'>
                <input type='submit' value='Add Promo Code' class='btn btn-primary'>
            </td>
        </tr>
        </table>
        {$csrf}
        </form>";
        $h->endpage();
    }
}

//Function to delete promo codes
function deletepromo()
{
    echo "<h3>Delete Promo Code</h3><hr />";
    global $db, $userid, $api, $h;
    //If the promotion code's ID is input
    if (isset($_GET['promo'])) {
        //Sanitize and validate that the promo code is a number.
        $code = (isset($_GET['promo']) && is_numeric($_GET['promo'])) ? abs(intval($_GET['promo'])) : 0;
        //User has failed the CSRF check
        if (!isset($_GET['verf']) || !checkCSRF('staff_promo_delete', stripslashes($_GET['verf']))) {
            alert('danger', "Action Blocked!", "We have blocked this action for your security. Please fill out the form
            quickly next time.");
            die($h->endpage());
        }
        //Code is empty/truncated.
        if (empty($code)) {
            alert('danger', "Uh Oh!", "Please input the promotion code you wish to delete.");
            die($h->endpage());
        }
        //Make sure the promotion code to be deleted actually exists.
        $q = $db->query("SELECT `promo_code` FROM `promo_codes` WHERE `promo_id` = {$code}");
        if ($db->num_rows($q) == 0) {
            alert('danger', "Uh Oh!", "You are trying to delete an invalid or non-existent promotion code.");
            die($h->endpage());
        }
        //All tests passed! Lets delete the code.
        $r = $db->fetch_single($q);
        $db->query("DELETE FROM `promo_codes` WHERE `promo_id` = {$code}");
        alert("success", "Success!", "You have successfully deleted the {$r} promotion code.", true, 'index.php');
        $api->game->addLog($userid, 'staff', "Deleted the {$r} promotion code.");
        $h->endpage();
    } else {
        alert('danger', "Uh Oh!", "Please select the promotion code you wish to delete.", true, '?action=viewpromo');
        $h->endpage();
    }
}

//Function to view promo codes.
function viewpromo()
{
    echo "<h3>View Promo Codes</h3><hr />";
    global $db, $h, $api;
    $q = $db->query("SELECT * FROM `promo_codes`");
    if ($db->num_rows($q) == 0) {
        alert('danger', "Uh Oh!", "There aren't any promotion codes in game.", true, 'index.php');
        die($h->endpage());
    } else {
        echo "<table class='table table-bordered'>
        <tr>
            <th>
                Promo Code
            </th>
            <th>
                Item
            </th>
            <th>
                Uses
            </th>
            <th>
                Delete
            </th>
        </tr>";
        //Request CSRF Code
        $csrf = getCodeCSRF('staff_promo_delete');
        while ($r = $db->fetch_row($q)) {
            echo "
            <tr>
                <td>
                    {$r['promo_code']}
                </td>
                <td>
                    " . $api->game->getItemNameFromID($r['promo_item']) . "
                </td>
                <td>
                    " . number_format($r['promo_use']) . "
                </td>
                <td>
                    <a href='?action=deletepromo&promo={$r['promo_id']}&verf={$csrf}'>Delete Promo</a>
                </td>
            </tr>";
        }

        echo "</table>";
    }
    $h->endpage();
}