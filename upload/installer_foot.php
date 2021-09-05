<?php
/*
	File:		installer_foot.php
	Created: 	4/5/2016 at 12:13AM Eastern Time
	Info: 		The footer for the installer. Gets deleted after the
				install has completed.
	Author:		TheMasterGeneral
	Website: 	https://github.com/MasterGeneral156/chivalry-engine
*/
if (!defined('MONO_ON'))
{
    exit;
}
?>
		</div>
	</div>
</div>
<link rel="stylesheet" href="https://code.jquery.com/ui/1.11.4/themes/smoothness/jquery-ui.min.css">
<link rel="stylesheet" href="css/game.css">
<link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/font-awesome/4.7.0/css/font-awesome.min.css">
<!-- jQuery Version 3.2.1 -->
<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.2.1/jquery.min.js"></script>

<!-- Bootstrap Core JavaScript -->
<script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.11.0/umd/popper.min.js"></script>
<script src="https://maxcdn.bootstrapcdn.com/bootstrap/4.0.0-beta/js/bootstrap.min.js"></script>

<!-- Other JavaScript -->
<script src="js/game.js"></script>
<script src='https://www.google.com/recaptcha/api.js' async defer></script>
<script src="https://cdn.rawgit.com/tonystar/bootstrap-hover-tabs/v3.1.1/bootstrap-hover-tabs.js" async defer></script>
</body>
<footer>
    <p>
        <br />
        <?php
        echo "<hr />
					Time is now " . date('F j, Y') . " " . date('g:i:s a') . "<br />
					Powered with codes by TheMasterGeneral. View source on <a href='https://github.com/MasterGeneral156/chivalry-engine'>Github</a>.";
        ?>
        &copy; <?php echo date("Y");
        ?>
    </p>
</footer>
</html>