/*!
 File: js/game.js
 Created: 3/15/2016 at 10:46AM Eastern Time
 Info: Misc. javascript functions for use around the game.
 Author: TheMasterGeneral
 Website: https://github.com/MasterGeneral156/chivalry-engine
 */
function loadSounds()
{
	var traintrance = soundManager.createSound({
	 id: 'traintrance',
	 autoload: true,
	 url: 'assets/audio/music/ogg/traintrance.ogg',
	});
	
	var funny_error = soundManager.createSound({
		 id: 'funny_error',
		 autoload: true,
		 url: 'assets/audio/music/ogg/funny-error.ogg',
		});
	
	var info_ding = soundManager.createSound({
		 id: 'info_ding',
		 autoload: true,
		 url: 'assets/audio/music/ogg/ding.ogg',
		});
}