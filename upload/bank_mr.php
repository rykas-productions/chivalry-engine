<?php
/*
 * File: bank_mr.php
 * Created: 7/22/2021 at 9:24PM
 * Info: The game bank players can store their currency in for safety, for mastery rank!
 * Author: TheMasterGeneral
 * Website: https://chivalryisdeadgame.com/
 */
$moduleID = "mastery_rank_bank";
require ("globals.php");

function initializeModule()
{
    global $moduleID;
    if (! readConfigFromDB($moduleID)) {
        $moduleConfigArray = array(
            'moduleID' => $moduleID,
            'moduleAuthor' => 'TheMasterGeneral',
            'moduleURL' => 'https://chivalryisdeadgame.com/',
            'moduleVersion' => 1,
            'bankOpeningFee' => 250000000,
            'bankWithdrawPercent' => 2.5,
            'bankWithdrawMaxFee' => 1000000,
            'bankMasteryRankRequirement' => 1
        );
        $defaultConfig = formatConfig($moduleConfigArray);
        writeConfigToDB($moduleID, $defaultConfig);
        echo "Installing default config...";
        header(getCurrentPage());
    }
}
echo "<h3><i class='game-icon game-icon-bank'></i> Mastery Rank Bank</h3>";
if (($ir['reset'] - 1) < $moduleConfig['bankMasteryRankRequirement']) {
    alert('danger', "Uh Oh!",
        "You need to achieve at least Mastery Rank 1 befor you can use this bank.",
        true, 'explore.php');
    die($h->endpage());
}
if ($api->UserStatus($userid, 'dungeon') || $api->UserStatus($userid,
    'infirmary')) {
    alert('danger', "Uh Oh!",
        "You cannot visit the bank while in the infirmary or dungeon.", true,
        'index.php');
    die($h->endpage());
}
if ($ir['bank'] > - 1) {
    if (! isset($_GET['action'])) {
        $_GET['action'] = '';
    }
    switch ($_GET['action'])
    {
        case "withdraw":
            withdraw();
            break;
        default:
            index();
            break;
    }
} // User needs to purchase bank account.
else {
    if (isset($_GET['buy'])) {
        // Player has the Copper Coins required to buy an account.
        if ($ir['primary_currency'] >= $bank_cost) {

            alert('success', "Success!",
                "You have successfully bought a bank account for " .
                number_format($bank_cost) . " Copper Coins!", true, 'bank.php');
            $api->UserTakeCurrency($userid, 'primary', $bank_cost);
            $api->UserInfoSet($userid, "bank", 0);
            $api->SystemLogsAdd($userid, 'bank',
                "[City Bank] Purchased account for " . number_format($bank_cost) .
                " Copper Coins.");
            addToEconomyLog('Bank Fees', 'copper', ($bank_cost) * - 1);
            item_add($userid, 157, 1);
        } // Player is too poor to afford account.
        else {
            alert('danger', "Uh oh!",
                "You do not have enough Copper Coins to buy a bank account. You need at least
                " .
                number_format($bank_cost) . " Copper Coins.", true, 'bank.php');
        }
    }
    else {
        echo "Do you wish to buy a bank account? It'll cost you " .
            number_format($bank_cost) . " Copper Coins.<br />
            <a href='?buy'>Yes, please!</a>";
    }
}