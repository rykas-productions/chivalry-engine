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
	 url: 'https://cdn.chivalryisdeadgame.com/assets/audio/music/ogg/traintrance.ogg',
	});
	
	var bittersweet = soundManager.createSound({
	 id: 'bittersweet',
	 autoload: true,
	 url: (soundManager.canPlayURL('https://cdn.chivalryisdeadgame.com/assets/audio/music/ogg/bittersweet-horror-vocals-a-sirens-melody.ogg') ? 'https://cdn.chivalryisdeadgame.com/assets/audio/music/ogg/bittersweet-horror-vocals-a-sirens-melody.ogg' : 'https://cdn.chivalryisdeadgame.com/assets/audio/music/mp3/bittersweet-horror-vocals-a-sirens-melody.mp3'),
	});
	
	var funny_error = soundManager.createSound({
		 id: 'funny_error',
		 autoload: true,
		 url: 'https://cdn.chivalryisdeadgame.com/assets/audio/music/ogg/funny-error.ogg',
	});
	
	var info_ding = soundManager.createSound({
		 id: 'info_ding',
		 autoload: true,
		 url: 'https://cdn.chivalryisdeadgame.com/assets/audio/music/ogg/ding.ogg',
	});
}