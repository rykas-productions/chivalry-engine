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
<!-- jQuery -->
<script src="https://code.jquery.com/jquery-3.7.0.min.js"></script>

<!-- Bootstrap Core JavaScript -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.1/dist/js/bootstrap.bundle.min.js"></script>

<!-- Custom JavaScript -->
<script src="js/game.js"></script>
<script src='https://www.google.com/recaptcha/api.js' async defer></script>

</body>
<footer class="mt-4">
    <p>
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