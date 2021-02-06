<?php
if (!defined('MONO_ON')) {
    exit;
}
class sound
{
	function loadSystem()
	{
	    global $set;
		cslog('log',"Loading CID Sound System.");
		echo "
		<script src='js/soundmanager2-nodebug-jsmin.js'></script>
		<script src='js/game-sounds-{$set['game_audio_version']}.js'></script>
		<script>
			var soundManager = soundManager.setup({
				url: '../assets/audio/',
				flashVersion: 9,
				preferFlash: false,
				waitForWindowLoad: true,
				onready: function() {
					  loadSounds();
				  }
				});
		</script>";
	}

	function play($soundID, $vol)
	{
		echo "<script>
				soundManager.onready(function() {
					soundManager.play('{$soundID}',{volume:{$vol}});
				});
		</script>";
	}
	
	function playBGM($soundID)
	{
	    global $userid;
	    $audioBGM = 15;
	    if (!empty($userid))
            $audioBGM=getCurrentUserPref('audioBGM', 15);
	    if ($audioBGM != 0)
	       $this->play($soundID, $audioBGM);
	}
}