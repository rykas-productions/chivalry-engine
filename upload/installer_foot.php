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
<script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.11.8/dist/umd/popper.min.js" integrity="sha384-I7E8VVD/ismYTF4hNIPjVp/Zjvgyol6VFvRkX/vR+Vc4jQkC+hVqc2pM8ODewa9r" crossorigin="anonymous"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/js/bootstrap.min.js" integrity="sha384-G/EV+4j2dNv+tEPo3++6LCgdCROaejBqfUeNjuKAiuXbjrxilcCdDz6ZAVfHWe1Y" crossorigin="anonymous"></script>

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
