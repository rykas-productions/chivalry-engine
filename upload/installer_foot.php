<?php
/*
	File:		installer_foot.php
	Created: 	4/5/2016 at 12:13AM Eastern Time
	Info: 		The footer for the installer. Gets deleted after the
				install has completed.
	Author:		TheMasterGeneral
	Website: 	https://github.com/MasterGeneral156/chivalry-engine
*/
include_once 'lang/en_us.php';
if (!defined('MONO_ON'))
{
    exit;
}
?>
		</div>
	</div>
</div>
<script src="https://code.jquery.com/jquery-3.2.1.min.js" integrity="sha256-hwg4gsxgFZhOsEEamdOYGBf13FyQuiTwlAQgxVSNgt4=" crossorigin="anonymous"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/tether/1.4.0/js/tether.min.js" integrity="sha384-DztdAPBWPRXSA/3eYEEUWrWCy7G5KFbe8fFjk5JAIxUYHKkDx6Qin1DkWx51bBrb" crossorigin="anonymous"></script>
<script src="https://maxcdn.bootstrapcdn.com/bootstrap/4.0.0-alpha.6/js/bootstrap.min.js" integrity="sha384-vBWWzlZJ8ea9aCX4pEW3rVHjgjt7zpkNpZk+02D9phzyeVkE+jo0ieGizqPLForn" crossorigin="anonymous"></script>
<script src="js/register-min.js"></script>
<script src="js/game.js"></script>
<script src='https://www.google.com/recaptcha/api.js'></script>
<script src="https://cdn.rawgit.com/tonystar/bootstrap-hover-tabs/v3.1.1/bootstrap-hover-tabs.js"></script>
<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.1.1/jquery.min.js"></script>
</body>
<footer>
    <p>
        <br />
        <?php
        echo "<hr />
					{$lang['MENU_TIN']}
						" . date('F j, Y') . " " . date('g:i:s a') . "<br />
					{$lang['MENU_OUT']}";
        ?>
        &copy; <?php echo date("Y");
        ?>
    </p>
</footer>
</html>