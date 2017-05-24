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
    exit;
}
$Version=('0.1.0');
$Build=('0100');
define('MONO_ON', 1);
session_name('CENGINE');
session_start();
if (!isset($_SESSION['started']))
{
    session_regenerate_id();
    $_SESSION['started'] = true;
}
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
    			<td>OpenSSL Random Pseudo Bytes avaliable?</td>
    			<td>{$ov}</td>
    		</tr>
    		<tr>
    			<td>Is Chivalry Engine up to date?</td>
    			<td>
        			" . get_cached_file("http://mastergeneral156.pcriot.com/update-checker.php?version={$Build}",'cache\update_check.txt') . "
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
    			<td align='center'>Database Driver</td>
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
    			</td>
    		</tr>
    		<tr>
    			<td align='center'>
    				Hostname<br />
    				<small>This is usually localhost</small>
    			</td>
    			<td><input type='text' name='hostname' class='form-control' value='localhost' required='1' /></td>
    		</tr>
    		<tr>
    			<td align='center'>
    				Username<br />
    				<small>The user must be able to use the database</small>
    			</td>
    			<td><input type='text' name='username' class='form-control' required='1' /></td>
    		</tr>
    		<tr>
    			<td align='center'>Password</td>
    			<td><input type='password' name='password' class='form-control' required='1' value='' /></td>
    		</tr>
    		<tr>
    			<td align='center'>
    				Database Name<br />
    				<small>The database should not have any other software using it.</small>
    			</td>
    			<td><input type='text' name='database' class='form-control' required='1' value='' /></td>
    		</tr>
    		<tr>
    			<th colspan='2'>Game Config</th>
    		</tr>
    		<tr>
    			<td align='center'>Game Name</td>
    			<td><input type='text' name='game_name' class='form-control' required='1' /></td>
    		</tr>
    		<tr>
    			<td align='center'>
    				Game Owner<br />
    				<small>This can be your nick, real name, or a company</small>
    			</td>
    			<td><input type='text' name='game_owner' class='form-control' required='1' /></td>
    		</tr>
    		<tr>
    			<td align='center'>
    				Game Description<br />
    				<small>This is shown on the login page.</small>
    			</td>
    			<td><textarea rows='6' cols='40' name='game_description' class='form-control' required='1'></textarea></td>
    		</tr>
    		<tr>
    			<td align='center'>
    				PayPal Address<br />
    				<small>This is where the payments for game DPs go.  Must be at least Premier.</small>
    			</td>
    			<td><input type='email' name='paypal' class='form-control' required='1' /></td>
    		</tr>
			<tr>
    			<td align='center'>
    				Password Cost<br />
    				<small>How much resources should you allocate towards generating a user's password?<br /> 
					Benchmark your server <a href='password_benchmark.php'>here</a>.</small>
    			</td>
    			<td><input type='number' class='form-control' value='10' required='1' min='5' max='15' name='password_effort'></td>
    		</tr>
			<tr>
    			<td align='center'>
    				Fraudguard IO Username<br />
    				<small><a href='https://fraudguard.io/'>https://fraudguard.io/</a></small>
    			</td>
    			<td><input type='text' name='fgun' class='form-control' required='1' /></td>
    		</tr>
			<tr>
    			<td align='center'>
    				Fraudguard IO Password<br />
    				<small><a href='https://fraudguard.io/'>https://fraudguard.io/</a></small>
    			</td>
    			<td><input type='password' name='fgpw' class='form-control' required='1' /></td>
    		</tr>
    		<tr>
    			<th colspan='2'>Admin User</th>
    		</tr>
    		<tr>
    			<td align='center'>Username</td>
    			<td><input type='text' name='a_username' minlength='3' maxlength='20' class='form-control' required='1' /></td>
    		</tr>
    		<tr>
    			<td align='center'>Password</td>
    			<td><input type='password' name='a_password' class='form-control' required='1' /></td>
    		</tr>
    		<tr>
    			<td align='center'>Confirm Password</td>
    			<td><input type='password' name='a_cpassword' class='form-control' required='1' /></td>
    		</tr>
    		<tr>
    			<td align='center'>E-Mail</td>
    			<td><input type='email' name='a_email' class='form-control' required='1' /></td>
    		</tr>
    		<tr>
    			<td align='center'>Gender</td>
    			<td>
    				<select name='gender' class='form-control' required='1' type='dropdown'>
    					<option value='Male'>Male</option>
    					<option value='Female'>Female</option>
    				</select>
    			</td>
    		</tr>
			<tr>
    			<td align='center'>Class</td>
    			<td>
    				<select name='class' class='form-control' required='1' type='dropdown'>
    					<option value='Warrior'>Warrior</option>
    					<option value='Rogue'>Rogue</option>
						<option value='Defender'>Defender</option>
    				</select>
    			</td>
    		</tr>
    		<tr>
    			<td colspan='2' align='center'>
    				<input type='submit' value='Install' class='btn btn-default' />
    			</td>
    		</tr>
    </table>
    </form>
       ";
}
if (!function_exists('get_magic_quotes_gpc'))
{

    function get_magic_quotes_gpc()
    {
        return 0;
    }
}
function gpc_cleanup($text)
{
	return strip_tags(stripslashes($text));
}
function install()
{
   global $Version,$Build;
   menuprint('sql');
	$fgun = (isset($_POST['fgun'])) ? gpc_cleanup($_POST['fgun']) : '';
	$fgpw = (isset($_POST['fgpw'])) ? gpc_cleanup($_POST['fgpw']) : '';
    $paypal = (isset($_POST['paypal']) && filter_input(INPUT_POST, 'paypal', FILTER_VALIDATE_EMAIL)) ? gpc_cleanup($_POST['paypal']) : '';
    $adm_email = (isset($_POST['a_email']) && filter_input(INPUT_POST, 'a_email', FILTER_VALIDATE_EMAIL)) ? gpc_cleanup($_POST['a_email']) : '';
    $adm_username = (isset($_POST['a_username']) && strlen($_POST['a_username']) > 3) ? gpc_cleanup($_POST['a_username']) : '';
    $adm_class = (isset($_POST['class']) && in_array($_POST['class'], array('Warrior', 'Rogue', 'Defender'), true)) ? $_POST['class'] : 'Warrior';
	$adm_gender = (isset($_POST['gender']) && in_array($_POST['gender'], array('Male', 'Female'), true)) ? $_POST['gender'] : 'Male';
    $description = (isset($_POST['game_description'])) ? gpc_cleanup($_POST['game_description']) : '';
    $owner = (isset($_POST['game_owner']) && strlen($_POST['game_owner']) > 3) ? gpc_cleanup($_POST['game_owner']) : '';
    $game_name = (isset($_POST['game_name'])) ? gpc_cleanup($_POST['game_name']) : '';
    $adm_pswd = (isset($_POST['a_password']) && strlen($_POST['a_password']) > 3) ? gpc_cleanup($_POST['a_password']) : '';
    $adm_cpswd = isset($_POST['a_cpassword']) ? gpc_cleanup($_POST['a_cpassword']) : '';
	$pweffort =  (isset($_POST['password_effort']) && is_numeric($_POST['password_effort'])) ? abs(intval($_POST['password_effort'])) : '10';
    $db_hostname = isset($_POST['hostname']) ? gpc_cleanup($_POST['hostname']) : '';
    $db_username = isset($_POST['username']) ? gpc_cleanup($_POST['username']) : '';
    $db_password = isset($_POST['password']) ? gpc_cleanup($_POST['password']) : '';
    $db_database = isset($_POST['database']) ? gpc_cleanup($_POST['database']) : '';
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
    if (empty($adm_username) || !preg_match("/^[a-z0-9_]+([\\s]{1}[a-z0-9_]|[a-z0-9_])+$/i", $adm_username))
    {
        $errors[] = 'Invalid admin username specified';
    }
    if (empty($adm_pswd))
    {
        $errors[] = 'Invalid admin password specified';
    }
    if ($adm_pswd !== $adm_cpswd)
    {
        $errors[] = 'The admin passwords did not match';
    }
    if (empty($adm_email))
    {
        $errors[] = 'Invalid admin email specified';
    }
    if (empty($owner)  || !preg_match("/^[a-z0-9_]+([\\s]{1}[a-z0-9_]|[a-z0-9_])+$/i", $owner))
    {
        $errors[] = 'Invalid game owner specified';
    }
    if (empty($game_name))
    {
        $errors[] = 'Invalid game name specified';
    }
    if (empty($description))
    {
        $errors[] = 'Invalid game description specified';
    }
    if (empty($paypal))
    {
        $errors[] = 'Invalid game PayPal specified';
    }
	if ((empty($pweffort)) || ($pweffort < 5) || ($pweffort > 20))
	{
		$errors[] = 'Password Effort either blank, lower than five or higher than twenty!';
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
    require_once("class/class_db_mysqli.php");
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
    $fo = fopen("cengine.sql", "r");
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
    echo '... done.<br />';
    echo 'Writing game configuration...';
	 $db->query(
            "INSERT INTO `settings`
             VALUES(NULL, 'Password_Effort', '{$pweffort}')");
    $ins_username = $db->escape(htmlentities($adm_username, ENT_QUOTES, 'ISO-8859-1'));
    $encpsw = password_hash(base64_encode(hash('sha256',$adm_pswd,true)), PASSWORD_DEFAULT);
    $e_encpsw = $db->escape($encpsw);
    $ins_email = $db->escape($adm_email);
	$profilepic="https://www.gravatar.com/avatar/" . md5(strtolower(trim($ins_email))) . "?s=250.jpg";
    $IP = $db->escape($_SERVER['REMOTE_ADDR']);
    $ins_game_name = $db->escape(htmlentities($game_name, ENT_QUOTES, 'ISO-8859-1'));
    $ins_game_desc = $db->escape(htmlentities($description, ENT_QUOTES, 'ISO-8859-1'));
    $ins_paypal = $db->escape($paypal);
    $ins_game_owner = $db->escape(htmlentities($owner, ENT_QUOTES, 'ISO-8859-1'));
	$CurrentTime=time();
	$db->query("INSERT INTO `users` 
	(`username`, `user_level`, `email`, `password`, `gender`, 
	`class`, `lastip`, `registerip`,
	`registertime`,`display_pic`) 
	VALUES ('{$ins_username}', 'Admin', '{$ins_email}', 
	'{$e_encpsw}', '{$adm_gender}', '{$adm_class}', '{$IP}', 
	'{$IP}', '{$CurrentTime}', '{$profilepic}');");
    $i = $db->insert_id();
	$e_class = $adm_class;
    if ($e_class == 'Warrior')
	{
		$db->query(
						"INSERT INTO `userstats`
						 VALUES($i, 1100, 1000, 900, 1000, 1000)");
	}
	if ($e_class == 'Rogue')
	{
				$db->query(
						"INSERT INTO `userstats`
						 VALUES($i, 900, 1100, 1000, 1000, 1000)");
	}
	if ($e_class == 'Defender')
	{
				$db->query(
						"INSERT INTO `userstats`
						 VALUES($i, 1000, 900, 1100, 1000, 1000)");
	}
	$db->query("INSERT INTO `settings` VALUES(NULL, 'FGUsername', '{$fgun}')");
	$db->query("INSERT INTO `settings` VALUES(NULL, 'FGPassword', '{$fgpw}')");
    $db->query("INSERT INTO `settings` VALUES(NULL, 'WebsiteName', '{$ins_game_name}')");
    $db->query("INSERT INTO `settings` VALUES(NULL, 'WebsiteOwner', '{$ins_game_owner}')");
    $db->query("INSERT INTO `settings` VALUES(NULL, 'PaypalEmail', '{$ins_paypal}')");
    $db->query("INSERT INTO `settings` VALUES(NULL, 'Website_Description', '{$ins_game_desc}')");
	$db->query("INSERT INTO `settings` VALUES(NULL, 'Version_Number', '{$Version}')");
	$db->query("INSERT INTO `settings` VALUES(NULL, 'BuildNumber', '{$Build}')");
	$db->query("INSERT INTO `infirmary` (`infirmary_user`, `infirmary_reason`, `infirmary_in`, `infirmary_out`) VALUES ('{$i}', 'N/A', '0', '0');");
	$db->query("INSERT INTO `dungeon` (`dungeon_user`, `dungeon_reason`, `dungeon_in`, `dungeon_out`) VALUES ('{$i}', 'N/A', '0', '0');");
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
	$CronsStart=strtotime("midnight tomorrow");
	$db->query("INSERT INTO `crons` (`file`, `nextUpdate`) VALUES ('crons/minute.php', $CronsStart),
	('crons/fivemins.php', $CronsStart), ('crons/day.php', $CronsStart), ('crons/hour.php', $CronsStart);");
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
	echo "<br />Crons have been set to start tomorrow at midnight.";
}
if ($_GET['code'] != 'install')
{
	require_once('installer_foot.php');
}
/* gets the contents of a file if it exists, otherwise grabs and caches */
function get_cached_file($url,$file,$hours=1) 
{
	$current_time = time(); 
	$expire_time = $hours * 60 * 60;
	if(file_exists($file))
	{
		$file_time = filemtime($file);
		if ($current_time - $expire_time < $file_time)
		{
			return file_get_contents($file);
		}
		else
		{
			$content = update_file($url,$file);
			file_put_contents($file,$content);
			return $content;
		}
	}
	else 
	{
		$content = update_file($url,$file);
		file_put_contents($file,$content);
		return $content;
	}
}
function update_file($url,$filename) 
{
	global $db,$set;
	$content = "404";
	$curl = curl_init();
	curl_setopt_array($curl, array(
		CURLOPT_URL => "{$url}",
		CURLOPT_SSL_VERIFYPEER => false,
		CURLOPT_RETURNTRANSFER => true));
	$content = curl_exec($curl);
	curl_close($curl);
	return $content;
}
