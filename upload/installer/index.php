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
    global $op, $version;
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
   createThreeCols("Chivalry Engine Version<br /><small>Its recommended to run the latest version.</small>", $version, "");
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
    $dboptions = "";
    if (function_exists('mysqli_connect'))
        $dboptions.="<option value='mysqli'>MySQLi Enhanced</option>";
    if (function_exists('pdo'))
        $dboptions.="<option value='pdo'>PHP Data Objects (PDO)</option>";
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
    echo "<hr />";
    $pwArray = "";
    if (!is_null(constant('PASSWORD_BCRYPT')))
        $pwArray .= "<option value='" . PASSWORD_BCRYPT . "'>BCRYPT</option>";
    if (!is_null(constant('PASSWORD_ARGON2I')))
        $pwArray .=  "<option value='" . PASSWORD_ARGON2I . "'>ARGON2I</option>";
    if (!is_null(constant('PASSWORD_ARGON2ID')))
        $pwArray .= "<option value='" . PASSWORD_ARGON2ID . "'>ARGON2ID</option>";
    createTwoCols("Password Storage<br /><small>Select password storage type, better security</small>", "<select name='pwtype' class='form-control' required='1' type='dropdown'>.
    				{$pwArray}
            </select>");
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
    echo "<hr />
    <h3>Admin Account</h3><hr />";
    createTwoCols("Username<br /><small>Shown around the game</small>", "<input type='text' name='adm_name' placeholder='Chivalrous Admin' class='form-control' required='1' />");
    echo "<hr />";
    createTwoCols("Login Email<br /><small>Log into the game with this</small>", "<input type='text' name='adm_email' value='admin@{$_SERVER['SERVER_NAME']}' class='form-control' required='1' />");
    echo "<hr />";
    createTwoCols("Password<br /><small>For your security!</small>", "<input type='password' name='adm_pw' class='form-control' required='1' />");
    echo "<hr />";
    createTwoCols("Confirm Password<br /><small>Confirm your password.</small>", "<input type='password' name='adm_cpw' class='form-control' required='1' />");
    echo "<hr />";
    echo "<input type='submit' value='Install Chivalry Engine' class='btn btn-primary'></form>";
}

function install()
{
    $pwArray = array();
    if (!is_null(constant('PASSWORD_BCRYPT')))
        array_push($pwArray, PASSWORD_BCRYPT);
    if (!is_null(constant('PASSWORD_ARGON2I')))
        array_push($pwArray, PASSWORD_ARGON2I);
    if (!is_null(constant('PASSWORD_ARGON2ID')))
        array_push($pwArray, PASSWORD_ARGON2ID);
    $paypal = (isset($_POST['paypal']) && filter_input(INPUT_POST, 'paypal', FILTER_VALIDATE_EMAIL)) ? stripAll($_POST['paypal']) : '';
    $description = (isset($_POST['gameDescription'])) ? stripAll($_POST['gameDescription']) : '';
    $owner = (isset($_POST['gameOwner']) && strlen($_POST['gameOwner']) > 3) ? stripAll($_POST['gameOwner']) : '';
    $gameName = (isset($_POST['gameName'])) ? stripAll($_POST['gameName']) : '';
    $db_hostname = isset($_POST['hostname']) ? stripAll($_POST['hostname']) : '';
    $db_username = isset($_POST['username']) ? stripAll($_POST['username']) : '';
    $db_password = isset($_POST['password']) ? stripAll($_POST['password']) : '';
    $db_database = isset($_POST['database']) ? stripAll($_POST['database']) : '';
    $db_driver = (isset($_POST['driver'])  && in_array($_POST['driver'], array('pdo', 'mysqli'), true)) ? $_POST['driver'] : 'mysqli';
    $pwType = (isset($_POST['pwtype'])  && in_array($_POST['pwtype'], $pwArray, true)) ? $_POST['pwtype'] : PASSWORD_BCRYPT;
    
    $strength = (isset($_POST['strength'])) ? stripAll($_POST['strength']) : '';
    $agility = (isset($_POST['agility'])) ? stripAll($_POST['agility']) : '';
    $guard = (isset($_POST['guard'])) ? stripAll($_POST['guard']) : '';
    $labor = (isset($_POST['labor'])) ? stripAll($_POST['labor']) : '';
    $iq = (isset($_POST['iq'])) ? stripAll($_POST['iq']) : '';
    
    $adm_name = (isset($_POST['adm_name'])) ? stripAll($_POST['adm_name']) : '';
    $adm_pw = (isset($_POST['adm_pw'])) ? stripAll($_POST['adm_pw']) : '';
    $adm_cpw = (isset($_POST['adm_cpw'])) ? stripAll($_POST['adm_cpw']) : '';
    $adm_email = (isset($_POST['adm_email']) && filter_input(INPUT_POST, 'adm_email', FILTER_VALIDATE_EMAIL)) ? stripAll($_POST['adm_email']) : '';
    
    $currprim = (isset($_POST['currencyPrimary'])) ? stripAll($_POST['currencyPrimary']) : '';
    $currsecd = (isset($_POST['currencySecondary'])) ? stripAll($_POST['currencySecondary']) : '';
    $errors = array();
    if (empty($db_hostname))
        $errors[] = 'No Database hostname specified<br />';
    if (empty($db_username))
        $errors[] = 'No Database username specified<br />';
    if (empty($db_database))
        $errors[] = 'No Database database specified<br />';
    if (empty($strength) || empty($agility) || empty($guard) || empty($labor) || empty($iq))
        $errors[] = 'Invalid game stat name(s) specified<br />';
    if (empty($currprim) || empty($currsecd))
       $errors[] = 'Invalid game currency name(s) specfied<br />';
    if ($db_driver == 'mysqli')
        if (!function_exists($db_driver . '_connect'))
            $errors[] = 'MySQLi is not a supported database wrapper<br />';
    elseif ($db_driver == 'pdo')
        if (!extension_loaded('pdo_mysql'))
            $errors[] = 'PDO is not a supported database wrapper<br />';
    else
        $errors[] = 'Invalid database driver specified<br />';
    
    if (empty($owner)  || !preg_match("/^[a-z0-9_]+([\\s]{1}[a-z0-9_]|[a-z0-9_])+$/i", $owner))
        $errors[] = 'Invalid game owner specified<br />';
    if (empty($gameName))
        $errors[] = 'Invalid game name specified<br />';
    if (empty($description))
        $errors[] = 'Invalid game description specified<br />';
    if (empty($paypal))
        $errors[] = 'Invalid PayPal donation address specified<br />';
    if (empty($adm_name)  || !preg_match("/^[a-z0-9_]+([\\s]{1}[a-z0-9_]|[a-z0-9_])+$/i", $adm_name))
        $errors[] = 'Invalid admin account name specified<br />';
    if (empty($adm_email))
        $errors[] = 'Invalid admin account email address specfied<br />';
    if (empty($adm_pw) || empty($adm_cpw))
        $errors[] = 'Invalid admin account password specified<br />';
    elseif ($adm_pw !== $adm_cpw)
        $errors[] = 'Differing passwords for admin account password confirmation<br />';
    
    if (!(in_array($pwType, $pwArray)))
        $errors[] = 'Invalid or unsupported password hash algorithim selected.<br />';
    if (count($errors) > 0)
    {
        danger("Chivalry Engine Installation failed with the following error(s)");
        echo "<textarea class='form-control' disabled='1'>There were one or more problems with your input.";
        foreach ($errors as $error)
        {
            echo "
                    ". stripAll($error) . ". ";
        }
        echo "</textarea><br />";
        info("Please go back and correct this information before installing again.");
        echo "
        &gt; <a href='?code=config'>Go back to config</a>";
        exit;
    }
    echo "Attempting database connection using the '{$db_driver}' driver: ";
    require_once("./../class/class_db_{$db_driver}.php");
    $db = new database;
    $db->configure($db_hostname, $db_username, $db_password, $db_database, 0);
    $db->connect();
    include('./../functions/func_account.php');
    include('./../functions/func_player.php');
    echo "Success!<br />";
    echo "Attempting to configure game:<br />";
    echo "Generating config file...";
    $code = sha1(openssl_random_pseudo_bytes(64));
    if (file_exists("config.php"))
    {
        unlink("config.php");
    }
    $e_db_hostname = addslashes($db_hostname);
    $e_db_username = addslashes($db_username);
    $e_db_password = addslashes($db_password);
    $e_db_database = addslashes($db_database);
    $e_prim_curr = addslashes($currprim);
    $e_secd_curr = addslashes($currsecd);
    $e_stat_str = addslashes($strength);
    $e_stat_agl = addslashes($agility);
    $e_stat_grd = addslashes($guard);
    $e_stat_lab = addslashes($labor);
    $e_stat_iq = addslashes($iq);
    
    $config_file =
<<<EOF
<?php
define('db_host','{$e_db_hostname}');
define('db_username','{$e_db_username}');
define('db_password','{$e_db_password}');
define('db_database','{$e_db_database}');
define('db_driver','{$db_driver}');		//mysqli or pdo
define('game_key','{$code}');
define('primary_currency', '{$e_prim_curr}');
define('secondary_currency', '{$e_secd_curr}');
define('stat_strength', '{$e_stat_str}');
define('stat_agility', '{$e_stat_agl}');
define('stat_labor', '{$e_stat_lab}');
define('stat_guard', '{$e_stat_grd}');
define('stat_iq','{$e_stat_iq}');
define('itemeffects', 3);               //increase or decrease as you see fit
define('sessionTimeoutSeconds', 1800);
define('sessionName', 'CEV3');
define('passwordSecurity', {$pwType});  //1 = bcrypt; 2 = argoni; 3 = argonid;
EOF;
    $f = fopen('config.php', 'w');
    fwrite($f, $config_file);
    fclose($f);
    echo " config file generated and saved to file system.<br />";
    echo "Attempting to setup the engine database:";
    $fo = fopen("./../cev3.sql", "r");
    $query = '';
    $lines = explode("\n", fread($fo, 1024768));
    fclose($fo);
    foreach ($lines as $line)
    {
        if (!(strpos($line, "--") === 0) && trim($line) != '')
        {
            $query .= $line;
            if (!(strpos($line, ";") === FALSE))
            {
                $db->query($query);
                $query = '';
            }
        }
    }
    echo " done!<br />";
    echo "Writing game configuration to database:";
    $e_gameName = $db->escape($gameName);
    $e_paypal = $db->escape($paypal);
    $e_gameDesc = $db->escape($description);
    $db->query("INSERT INTO `game_settings` VALUES(NULL, 'game:name', '{$e_gameName}')");
    $db->query("INSERT INTO `game_settings` VALUES(NULL, 'game:paypal_address', '{$e_paypal}')");
    $db->query("INSERT INTO `game_settings` VALUES(NULL, 'game:description', '{$e_gameDesc}')");
    $db->query("INSERT INTO `game_settings` VALUES(NULL, 'chiv-eng:version', '" . getEngineVersion() . "')");
    echo "done!<br />";
    echo "Creating admin account...";
    $UUID = createUserUUID();
    $encodedPassword=generatePassword($adm_pw);
    $db->query("INSERT INTO `users_core`
				(`userid`, `username`, `email`, `password`)
				VALUES
				('{$UUID}', '{$adm_name}', '{$adm_email}', '{$encodedPassword}')");
    $time=returnUnixTimestamp();
    $IP=getUserIP();
    $db->query("INSERT INTO `users_account_data` VALUES ('{$UUID}', '0', '{$time}', '{$time}', '', '1', '127.0.0.1', '{$IP}', '{$IP}', '0')");
    $db->query("INSERT INTO `users_stats`
		(`userid`, `level`, `experience`, `strength`, `agility`,
		`guard`, `labor`, `iq`, `energy`, `maxEnergy`, `will`,
		`maxWill`, `brave`, `maxBrave`, `hp`, `maxHP`,
		`primaryCurrencyHeld`, `primaryCurrencyBank`)
		VALUES ('{$UUID}', '1', '0', '10', '10', '10', '10',
		'10', '10', '10', '100', '100', '5', '5', '100', '100',
		'100', '-1')");
    echo "done!<br />";
    if ($_POST['analytics'])
    {
        echo "Sending install analytics:";
        sendData($e_gameName, $db_driver);
        echo " done, thank you! :)";
    }
    success("Installation complete!");
    echo "Removing the installer file:";
    @unlink('index.php');
    if (file_exists('index.php'))
    {
        $suc = false;
        echo " failed!<br />";
    }
    else
    {
        $suc = true;
        echo " Success!";
    }
    if (!$suc)
    {
        echo "Locking installer...";
        @touch('installer.lock');
        $suc2 = file_exists('installer.lock');
        if ($suc2)
        {
            echo "Installer locked, remove /installer/index.php at  your convience...";
        }
        else
        {
            echo "Installer failed to be locked! You MUST remove /installer/index.php from your server immediately or other users could run the installer again.";
        }
    }
}
endHeaders();