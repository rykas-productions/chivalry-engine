<?php
/*
	File:		installer/index.php
	Created: 	02/01/2020 at 7:28PM Eastern Time
	Author:		TheMasterGeneral
	Website: 	https://github.com/rykas-productions/chivalry-engine
	MIT License
	Copyright (c) 2020 TheMasterGeneral
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
require('../functions/func_installer.php');
loadFunc();
$version = getInstallVersion();
define('MONO_ON', 1);
register_shutdown_function('shutdown');
enableErrorOutput();    //Comment out to disable raw PHP errors.
setSession('CEV3'); //Change to whatever session name you'd like.
require('../lib/basic_error_handler.php');
set_error_handler('error_php');
startHeaders();
$op=doInstallerChecks();
if (!isset($_GET['code']))
{
    $_GET['code'] = '';
}
switch ($_GET['code'])
{
    case "install":
        install();
        break;
    case "config":
        config();
        break;
    default:
        diagnostics();
        break;
}

function diagnostics()
{
    global $op;
   echo "Welcome to Chivalry Engine V3 by Rykas Productions! We hope to walk you through 
    the engine installation. Its fairly easy! But first, we need to make sure your 
    server meets our requirements. While we do that, here's some server info for you! 
    If we have an issue with anything, we'll let you know below.";
   createThreeCols("<h3>Property</h3>", "<h3>Value</h3>", "<h3>Status</h3>");
   echo "<hr />";
   createThreeCols("Server IP<br /><small>This is your server's IP address.</small>", "{$_SERVER['SERVER_ADDR']}", "");
   echo "<hr />";
   createThreeCols("Server Name<br /><small>This is your server's name.</small>", "{$_SERVER['SERVER_NAME']}", "");
   echo "<hr />";
   createThreeCols("Chivalry Engine Version<br /><small>Its recommended to run the latest version.</small>", getInstallVersion(), "");
   echo "<hr />";
   createThreeCols("PHP Version<br /><small>Chivalry Engine needs PHP Version >= 7.0.0.</small>", phpversion(), checkPass($op['phpValidVersion']));
   echo "<hr />";
   createThreeCols("Game Folder Writable?<br /><small>We need this for temp files, and for your config file.</small>", "", checkPass($op['writable']));
   echo "<hr />";
   createThreeCols("Password Hashing Functions Available?<br /><small>This is for better password storage.</small>", "", checkPass($op['password']));
   echo "<hr />";
   createThreeCols("OpenSSL Functions Available?<br /><small>This is for better randomization and security.</small>", "", checkPass($op['openssl']));
   echo "<hr />";
   createThreeCols("MySQLi Available?<br /><small>This is one of two database options.</small>", "", checkPass($op['mysqli']));
   echo "<hr />";
   createThreeCols("PHP Data Objects (PDO) Available?<br /><small>This is one of two database options.</small>", "", checkPass($op['pdo']));
   echo "<hr />";
   createThreeCols("File Functions Available?<br /><small>We need to read the files we write too, y'know?.</small>", "", checkPass($op['fopen']));
   echo "<hr />";
   createThreeCols("CURL Functions Available?<br /><small>This is so we can view information from remote websites.</small>", "", checkPass($op['curl']));
   echo "<hr />";
   $error=false;
   $errorInfo='';
   if (!($op['pdo']) && !($op['mysqli']))
   {
        $error=true;
        $errorInfo.="You need at least one database handler to use Chivalry Engine.<br />";
   }
   if (!($op['writable']))
   {
       $error=true;
       $errorInfo.="Chivalry Engine needs write access to the game folder.<br />";
   }
   if (!($op['fopen']))
   {
       $error=true;
       $errorInfo.="Chivalry Engine needs access to fopen.<br />";
   }
   if (!($op['curl']))
   {
       $error=true;
       $errorInfo.="Chivalry Engine needs access to cURL.<br />";
   }
   if (!($op['password']))
   {
       $error=true;
       $errorInfo.="Chivalry Engine needs access to password_hash.<br />";
   }
   if (!($op['openssl']))
   {
       $error=true;
       $errorInfo.="Chivalry Engine needs access to OpenSSL.<br />";
   }
   if (!($op['openssl']))
   {
       $error=true;
       $errorInfo.="Chivalry Engine needs access to OpenSSL.<br />";
   }
   if ($error)
   {
        danger("You have errors detected. Please fix them before attempting to run the installer again.<br /><b>{$errorInfo}</b>");
        exit;
   }
   else
   {
        successRedirect("No errors were detected. You may install Chivalry Engine V3.",'?code=config','Next Step');
   }
}
function config()
{
    //TODO: Move into the form class, somehow... maybe even expand the class.
    //TODO: Update to Bootstrap 5 is it releases.
    $dboptions = "";
    if (function_exists('mysqli_connect'))
        $dboptions.="<option value='mysqli'>MySQLi Enhanced</option>";
    if (function_exists('pdo'))
        $dboptions.="<option value='mysqli'>PHP Data Objects (PDO)</option>";
    if ($dboptions == "")
        $dboptions.="<option>No database handler detected.</option>";
    
    echo "Now that we've gotten the testing out of the way, how about you fill out this form and we'll 
        use the data you provide to get your game installed!<hr />
    <h3>Database Info</h3><hr />
    <form method='post' action='?code=install'>";
    createTwoCols("Database Driver<br /><small>MySQLi or PDO by default.</small>", "<select name='driver' class='form-control' type='dropdown'>{$dboptions}</select>");
    echo "<hr />";
    createTwoCols("Database Hostname<br /><small>Typically localhost</small>", "<input type='text' name='hostname' class='form-control' value='localhost' required='1' />");
    echo "<hr />";
    createTwoCols("Database Username<br /><small>The must be able to interact with the datbase.</small>", "<input type='text' name='username' class='form-control' required='1' />");
    echo "<hr />";
    createTwoCols("Database Name<br /><small>The name of the database the game will install to.</small>", "<input type='text' name='database' class='form-control' value='chivalry_engine_v3' required='1' />");
    echo "<hr />";
    createTwoCols("Database Password<br /><small>The user's password.</small>", "<input type='password' name='password' class='form-control' required='1' />");
    echo "<hr />";
    createTwoCols("Send Install Info?<br /><small>This will send us a little data about your game.</small>", "<select name='analytics' class='form-control' required='1' type='dropdown'>
    					<option value='true'>Send Info</option>
    					<option value='false'>Opt-Out</option>
    				</select>");
    echo "<hr />
    <h3>Game Configuration</h3><hr />";
    createTwoCols("Game Name<br /><small>What's the name of your game?</small>", "<input type='text' name='gameName' class='form-control' required='1' />");
    echo "<hr />";
    createTwoCols("Your Name<br /><small>Can be an alias. This will display around the game.</small>", "<input type='text' name='gameOwner' class='form-control' required='1' />");
    echo "<hr />";
    createTwoCols("Game Description<br /><small>Draw players into your game here.</small>", "<textarea name='gameDescription' class='form-control' required='1'></textarea>");
    echo "<hr />";
    createTwoCols("Paypal Address<br /><small>This is where donations will be sent.</small>", "<input type='email' name='paypal' class='form-control' required='1' />");
    echo "<hr />
    <h3>Game Details</h3><hr />";
    createTwoCols("Primary Currency Name<br /><small>What's the name of your game's most common currency?</small>", "<input type='text' name='currencyPrimary' value='Primary Currency' class='form-control' required='1' />");
    echo "<hr />";
    createTwoCols("Secondary Currency Name<br /><small>What's the name of your game's rarer currency?</small>", "<input type='text' name='currencySecondary' value='Secondary Currency' class='form-control' required='1' />");
    echo "<hr />";
    createTwoCols("Damage Stat Name<br /><small>What's the name of the stat that determines your damage.</small>", "<input type='text' name='strength' value='Strength' class='form-control' required='1' />");
    echo "<hr />";
    createTwoCols("Speed Stat Name<br /><small>What's the name of the stat that determines your speed.</small>", "<input type='text' name='agility' value='Agility' class='form-control' required='1' />");
    echo "<hr />";
    createTwoCols("Defense Stat Name<br /><small>What's the name of the stat that determines your defense.</small>", "<input type='text' name='guard' value='Guard' class='form-control' required='1' />");
    echo "<hr />";
    createTwoCols("Work Stat Name<br /><small>What's the name of the stat that determines your ability to work.</small>", "<input type='text' name='labor' value='Labor' class='form-control' required='1' />");
    echo "<hr />";
    createTwoCols("Intelligence Stat Name<br /><small>What's the name of the stat that determines your intelligence.</small>", "<input type='text' name='iq' value='IQ' class='form-control' required='1' />");
    echo "<hr />";
    echo "</form>";
}
endHeaders();