<?php
/*
	File:		installer.php
	Created: 	4/5/2016 at 12:12AM Eastern Time
	Info: 		The game installer. Run this to install the game. Will
				delete itself after the installation has completed.
	Author:		TheMasterGeneral
	Website: 	https://github.com/MasterGeneral156/chivalry-engine
*/
if (file_exists('./installer.lock'))
{
    header("Location: login.php");
    die();
}
fixExecutionTime();
$Version=('1.0.2');
$Build=('101b');
$set['Version_Number'] = $Version;
define('MONO_ON', 1);
session_name('CENGINE');
session_start();
if (!isset($_SESSION['started']))
{
    session_regenerate_id();
    $_SESSION['started'] = true;
}
require('global_func.php');
require_once('installer_head.php');
require_once('lib/installer_error_handler.php');
set_error_handler('error_php');
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

function menuprint($highlight)
{
    $items =
            array('diag' => '1. Diagnostics', 'input' => '2. Configuration',
                    'sql' => '3. Installation & Extras',);
    $c = 0;
    echo "<hr />";
    foreach ($items as $k => $v)
    {
        $c++;
        if ($c > 1)
        {
            echo ' >> ';
        }
        if ($k == $highlight)
        {
            echo '<span style="color: black;">' . $v . '</span>';
        }
        else
        {
            echo '<span style="color: gray;">' . $v . '</span>';
        }
    }
    echo '<hr />';
}

function diagnostics()
{
	global $Build;
    menuprint("diag");
    if (version_compare(phpversion(), '5.5.0') < 0)
    {
        $pv = '<span style="color: red">Failed</span>';
        $pvf = 0;
    }
    else
    {
        $pv = "<span style='color: green'>Pass! PHP Version is " . phpversion();  "!</span>";
        $pvf = 1;
    }
    if (is_writable('./'))
    {
        $wv = '<span style="color: green">Pass! Game folder is writable.</span>';
        $wvf = 1;
    }
    else
    {
        $wv = '<span style="color: red">Fail!</span>';
        $wvf = 0;
    }
	if (function_exists('openssl_random_pseudo_bytes'))
    {
        $ov = '<span style="color: green">Pass! OpenSSL Random Pseudo Bytes detected!</span>';
        $ovf = 1;
    }
    else
    {
        $ov = '<span style="color: red">Failed...</span>';
        $ovf = 0;
    }
	if (function_exists('password_hash'))
    {
        $hv = '<span style="color: green">Pass! Using stronger password hash method.</span>';
        $hvf = 1;
    }
    else
    {
        $hv = '<span style="color: red">Failed...</span>';
        $hvf = 0;
    }
	if (extension_loaded('pdo_mysql'))
    {
        $pdv = '<span style="color: green">PDO detected. Please use PDO!</span>';
        $pdf = 1;
    }
    elseif (function_exists('mysqli_connect'))
    {
        $pdv = '<span style="color: orange">PDO not detected. Use MySQLi!</span>';
        $pdf = 1;
    }
	else
	{
		$pdv = '<span style="color: red">No acceptable database handler found. Installer will not continue.</span>';
        $pdf = 0;
	}
	$maxTimeOut = ini_get('max_execution_time');
	if ($maxTimeOut <= 30)
	    $toclass = "danger";
	elseif ($maxTimeOut >= 60)
	   $toclass = "success";
	else
	    $toclass="";
	$dbFetch = (fetchCIDDB()) ? "Database downloaded" : "Could not download";
    echo "
    <h3>Basic Diagnostic Results:</h3>
    <table class='table table-bordered table-hover'>
    		<tr>
    			<td>Is the server's PHP Version greater than 5.5.0?</td>
    			<td>{$pv}</td>
    		</tr>
    		<tr>
    			<td>Is the game folder writable?</td>
    			<td>{$wv}</td>
    		</tr>
			<tr>
    			<td>Database Recommendation?</td>
    			<td>{$pdv}</td>
    		</tr>
			<tr>
    			<td>Password_Hash avaliable?</td>
    			<td>{$hv}</td>
    		</tr>
            <tr>
    			<td>PHP Execution Timeout<br />
                <small><i>https://stackoverflow.com/questions/3829403/how-to-increase-the-execution-timeout-in-php</i></small></td>
    			<td><span class='text-{$toclass}'>{$maxTimeOut} seconds</span><br />
                <small>Want to be higher than 30 seconds.</small></td>
    		</tr>
			<tr>
    			<td>OpenSSL Random Pseudo Bytes avaliable?</td>
    			<td>{$ov}</td>
    		</tr>
    		<tr>
    			<td>Is Chivalry Engine up to date?</td>
    			<td>
        			" . version_json() . "
        		</td>
        	</tr>
            <tr>
    			<td>CID SQL Downloaded?</td>
    			<td>
        			" . $dbFetch . "
        		</td>
        	</tr>
    </table>
       ";
    if ($pvf + $pdf + $wvf + $hvf + $ovf < 5)
    {
        echo "
		<hr />
		<span style='color: red; font-weight: bold;'>
		One of the basic diagnostics failed, so Setup cannot continue.
		Please fix the ones that failed and try again.
		</span>
		<hr />
   		";
    }
    else
    {
        echo "
		<hr />
		&gt; <a href='installer.php?code=config'>Next Step</a>
		<hr />
   		";
    }
}

function config()
{
    menuprint("input");
    echo "
    <h3>Configuration:</h3>
    <form action='installer.php?code=install' method='post'>
    <table class='table table-bordered table-hover'>
    		<tr>
    			<th colspan='2'>Database Config</th>
    		</tr>
    		<tr>
    			<th>Database Driver</td>
    			<td>
    				<select name='driver' class='form-control' type='dropdown'>
       ";
    if (function_exists('mysqli_connect'))
    {
        echo '<option value="mysqli">MySQLi Enhanced</option>';
    }
		else
	{
		echo '<option>MySQLi not detected on your server.</option>';
	}
	if (extension_loaded('pdo'))
    {
        echo '<option value="pdo">PHP Data Objects (PDO)</option>';
    }
	else
	{
		echo '<option>No acceptable database handler detected on your server.</option>';
	}
    echo "
    				</select>
    			</th>
    		</tr>
    		<tr>
    			<th>
    				Hostname<br />
    				<small>This is usually localhost</small>
    			</th>
    			<td><input type='text' name='hostname' class='form-control' value='localhost' required='1' /></td>
    		</tr>
    		<tr>
    			<th>
    				Username<br />
    				<small>The user must be able to use the database</small>
    			</th>
    			<td><input type='text' name='username' class='form-control' required='1' /></td>
    		</tr>
    		<tr>
    			<th>Password</th>
    			<td><input type='password' name='password' class='form-control' required='1' value='' /></td>
    		</tr>
    		<tr>
    			<th>
    				Database Name<br />
    				<small>The database should not have any other software using it.</small>
    			</th>
    			<td><input type='text' name='database' class='form-control' required='1' value='' /></td>
    		</tr>
    		<tr>
    			<th>
    				Send Install Info?<br />
    				<small>Just your domain name, codebase version, install date, game name and database type.</small>
    			</th>
    			<td>
    				<select name='analytics' class='form-control' required='1' type='dropdown'>
    					<option value='true'>True</option>
    					<option value='false'>False</option>
    				</select>
    			</td>
    		</tr>
            <tr>
    			<th>
    				Admin User<br />
    				<small>Input the User ID of the player you wish to make an admin.</small>
    			</th>
    			<td><input type='number' name='userid' class='form-control' min='1' value='1' required='1' /></td>
    		</tr>
            <tr>
                <td colspan='2'>
                    <input type='submit' class='btn btn-primary btn-block' value='Install'>
                </td>
            </tr>
    </table>
    </form>
       ";
}
function gpc_cleanup($text)
{
	return strip_tags(stripslashes($text));
}
function install()
{
   global $Version,$Build;
   menuprint('sql');
    $db_hostname = isset($_POST['hostname']) ? gpc_cleanup($_POST['hostname']) : '';
    $db_username = isset($_POST['username']) ? gpc_cleanup($_POST['username']) : '';
    $db_password = isset($_POST['password']) ? gpc_cleanup($_POST['password']) : '';
    $db_database = isset($_POST['database']) ? gpc_cleanup($_POST['database']) : '';
    $adminID = (isset($_POST['userid']) && is_numeric($_POST['userid'])) ? abs($_POST['userid']) : 1;
    $db_driver = (isset($_POST['driver'])  && in_array($_POST['driver'], array('pdo', 'mysqli'), true)) ? $_POST['driver'] : 'mysqli';
    $errors = array();
    if (empty($db_hostname))
    {
        $errors[] = 'No Database hostname specified';
    }
    if (empty($db_username))
    {
        $errors[] = 'No Database username specified';
    }
    if (empty($db_database))
    {
        $errors[] = 'No Database database specified';
    }
	if ($db_driver = 'mysqli')
	{
		if (!function_exists($db_driver . '_connect'))
		{
			$errors[] = 'Invalid database driver specified';
		}
	}
	elseif ($db_driver = 'pdo')
	{
		if (!extension_loaded('pdo_mysql'))
		{
			$errors[] = 'Invalid database driver specified';
		}
	}
	else
	{
		$errors[] = 'Invalid database driver specified';
	}
    if (count($errors) > 0)
    {
        echo "Installation failed.<br />
        There were one or more problems with your input.<br />
        <br />
        <b>Problem(s) encountered:</b>
        <ul>";
        foreach ($errors as $error)
        {
            echo "<li><span style='color: red;'>{$error}</span></li>";
        }
        echo "</ul>
        &gt; <a href='installer.php?code=config'>Go back to config</a>";
        require_once('installer_foot.php');
        exit;
    }
    // Try to establish DB connection first...
    echo 'Attempting DB connection...';
    require_once("class/class_db_{$db_driver}.php");
    $db = new database;
    $db->configure($db_hostname, $db_username, $db_password, $db_database, 0);
    $db->connect();
    $c = $db->connection_id;
    // Done, move on
    echo '... Successful.<br />';
    echo 'Writing game config file...';
    echo 'Write Config...';
    $code = sha1(openssl_random_pseudo_bytes(64));
    if (file_exists("config.php"))
    {
        unlink("config.php");
    }
    $e_db_hostname = addslashes($db_hostname);
    $e_db_username = addslashes($db_username);
    $e_db_password = addslashes($db_password);
    $e_db_database = addslashes($db_database);
    $lit_config = '$_CONFIG';
    $config_file =
            <<<EOF
<?php
            {$lit_config} = array(
	'hostname' => '{$e_db_hostname}',
	'username' => '{$e_db_username}',
	'password' => '{$e_db_password}',
	'database' => '{$e_db_database}',
	'persistent' => 0,
	'driver' => '{$db_driver}',
	'code' => '{$code}',
);
?>
EOF;
    $f = fopen('config.php', 'w');
    fwrite($f, $config_file);
    fclose($f);
    echo '... file written.<br />';
    echo 'Writing base database schema...';
    $sql = "./cache/latest.sql";
    execute_sql($sql, $db_database, $db_hostname, $db_username, $db_password, $db_driver, $c);
    echo '... done.<br />';
    if ($_POST['analytics'] == 'true')
    {
        echo "Sending install analytics...";
        sendData("Chivalry is Dead Dev", $db_driver);
        echo "Analytics have been sent. TheMasterGeneral thanks you!<br />";
    }
    echo '... Done.<br />';
    $path = dirname($_SERVER['SCRIPT_FILENAME']);
    echo "
    <h2>Installation Complete!</h2>
    <hr />
       ";
    echo "<h3>Installer Security</h3>
    Attempting to remove installer... ";
    @unlink('installer.php');
	@unlink('installer_head.php');
    @unlink('installer_foot.php');
	@unlink('password_benchmark.php');
    if (file_exists('installer.php'))
	{
		$success = false;
		echo "Failed.<br />";
	}
	else
	{
		$success = true;
		echo "Success!<br />";
	}
	@unlink('lib/installer_error_handler.php');
    if ($success == false)
    {
        echo "Attempting to lock installer... ";
        @touch('installer.lock');
        $success2 = file_exists('installer.lock');
        echo "<span style='color: " . ($success2 ? "green;'>Succeeded" : "red;'>Failed")
                . "</span><br />";
        if ($success2)
        {
            echo "<span style='font-weight: bold;'>"
                    . "You should now remove installer.php from your server."
                    . "</span>";
        }
        else
        {
            echo "<span style='font-weight: bold; font-size: 20pt;'>"
                    . "YOU MUST REMOVE installer.php "
                    . "from your server.<br />"
                    . "Failing to do so will allow other people "
                    . "to run the installer again and potentially "
                    . "mess up your game entirely." . "</span>";
        }
    }
    $db->query("UPDATE `users` SET `user_level` = 'Admin' WHERE `userid` = {$adminID}");
	echo "<br />Crons have been set to start tomorrow at midnight.";
}
if ($_GET['code'] != 'install')
{
	require_once('installer_foot.php');
}
/*
 * Function to send analytical data to TheMasterGeneral, if the installer chooses to.
 */
function sendData($gamename, $dbtype, $url='https://chivalryisdeadgame.com/chivalry-engine-analytics.php')
{
    global $Version;
    $postdata = "domain=" . getGameURL() . "&install=" . time() ."&gamename={$gamename}&dbtype={$dbtype}&version={$Version}";
    $ch = curl_init();
    curl_setopt ($ch, CURLOPT_URL, $url);
    curl_setopt ($ch, CURLOPT_SSL_VERIFYPEER, FALSE);
    curl_setopt ($ch, CURLOPT_USERAGENT, "Mozilla/5.0 (Windows; U; Windows NT 5.1; en-US; rv:1.8.1.6) Gecko/20070725 Firefox/2.0.0.6");
    curl_setopt ($ch, CURLOPT_TIMEOUT, 60);
    curl_setopt ($ch, CURLOPT_FOLLOWLOCATION, 0);
    curl_setopt ($ch, CURLOPT_RETURNTRANSFER, 1);
    curl_setopt ($ch, CURLOPT_REFERER, $url);
    curl_setopt ($ch, CURLOPT_POSTFIELDS, $postdata);
    curl_setopt ($ch, CURLOPT_POST, 1);
    $result = curl_exec ($ch);
    curl_close($ch);
}
function getGameURL()
{
    $domain = $_SERVER['HTTP_HOST'];
    $turi = $_SERVER['REQUEST_URI'];
    $turiq = '';
    for ($t = strlen($turi) - 1; $t >= 0; $t--) {
        if ($turi[$t] != '/') {
            $turiq = $turi[$t] . $turiq;
        } else {
            break;
        }
    }
    $turiq = '/' . $turiq;
    if ($turiq == '/') {
        $domain .= substr($turi, 0, -1);
    } else {
        $domain .= str_replace($turiq, '', $turi);
    }
    return $domain;
}

function fixExecutionTime()
{
    $maxTime = ini_get('max_execution_time');
    if ($maxTime < 60)
        ini_set('max_execution_time', 60);
}