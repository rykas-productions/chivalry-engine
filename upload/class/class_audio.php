<?php
if (!defined('MONO_ON')) {
    exit;
}
class sound
{
	function loadSystem()
	{
		cslog('log',"Loading CID Sound System.");
		echo "
		<script src='js/soundmanager2-nodebug-jsmin.js'></script>
		<script src='js/game-sounds-20.4.2.js'></script>
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
					soundManager.play('{$soundID}',{volume:50});
				});
		</script>";
	}
	function playBGM($soundID)
	{
		$this->play($soundID, 15);
	}
}