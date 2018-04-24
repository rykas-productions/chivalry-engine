<?php
if (!isset($_GET['analytics']))
	$_GET['analytics']='';
if (!isset($acceptance))
	$acceptance=0;
if ($_GET['analytics'] == 1)
{
	$db->query("UPDATE `user_settings` SET `analytics` = 1, `acceptance` = 1 WHERE `userid` = {$userid}");
	$acceptance = 1;
}
if ($_GET['analytics'] == 2)
{
	$db->query("UPDATE `user_settings` SET `analytics` = 0, `acceptance` = 1 WHERE `userid` = {$userid}");
	$acceptance = 1;
}
if (($ir['acceptance'] == -1) && ($acceptance != 1))
{
	?>
	<div class="modal fade" id="data_collection" tabindex="-2" role="dialog" data-backdrop="static" data-keyboard="false" aria-labelledby="data_collection" aria-hidden="true">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="addShortcutLabel">Data Collection Agreement</h5>
            </div>
            <div class="modal-body" align="left">
                We would like your help:<br />
				Here at Chivalry is Dead, we take your privacy very serious. Full details of how we handle data can be found in our 
				privacy policy, reachable at 
				<a href='https://chivalryisdeadgame.com/privacy.php'>https://chivalryisdeadgame.com/privacy.php</a>.<br />
				We are very keen on the idea of being able to improve the Chivalry is Dead game to ensure that it provides the best possible 
				experience.<br />
				For this, we would like to collect and use information about how you play Chivalry is Dead, then analyse it to better understand 
				playing behavior, along with compile statistical reports regarding that activity. We will only do this if you provided your consent 
				using the buttons below.<br />
				The information would include: your browser user agent, your operating system, your browser of choice, pages of the game you load. This 
				data is collected against an internal unique identifier.<br />
				To collect this data, we would use our game analytics provider, Google Analytics, who would only collect and process this data in 
				accordance with our instructions.<br />
				Please click the button to select your choice of data sharing. You can change this at any time by checking your preferences menu.<br />
				<a href='?analytics=1' class='btn btn-success'>I Consent to Chivalry is Dead using my information for this purpose.</a><br />
				<a href='?analytics=2' class='btn btn-danger'>No thanks.</a><br />
				<a href='logout.php' class='btn btn-primary'>Logout</a>
            </div>
        </div>
    </div>
	</div>
	<?php
}