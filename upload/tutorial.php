<?php
/*
	File:		tutorial.php
	Created: 	6/23/2019 at 6:11PM Eastern Time
	Info: 		Displays a very well detailed tutorial for new players.
	Author:		TheMasterGeneral
	Website: 	https://github.com/MasterGeneral156/chivalry-engine
	MIT License

	Copyright (c) 2019 TheMasterGeneral

	Permission is hereby granted, free of charge, to any person obtaining a copy
	of this software and associated documentation files (the "Software"), to deal
	in the Software without restriction, including without limitation the rights
	to use, copy, modify, merge, publish, distribute, sublicense, and/or sell
	copies of the Software, and to permit persons to whom the Software is
	furnished to do so, subject to the following conditions:

	The above copyright notice and this permission notice shall be included in all
	copies or substantial portions of the Software.

	THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND, EXPRESS OR
	IMPLIED, INCLUDING BUT NOT LIMITED TO THE WARRANTIES OF MERCHANTABILITY,
	FITNESS FOR A PARTICULAR PURPOSE AND NONINFRINGEMENT. IN NO EVENT SHALL THE
	AUTHORS OR COPYRIGHT HOLDERS BE LIABLE FOR ANY CLAIM, DAMAGES OR OTHER
	LIABILITY, WHETHER IN AN ACTION OF CONTRACT, TORT OR OTHERWISE, ARISING FROM,
	OUT OF OR IN CONNECTION WITH THE SOFTWARE OR THE USE OR OTHER DEALINGS IN THE
	SOFTWARE.
*/
require('globals.php');
echo "Welcome to the {$set['WebsiteName']} Tutorial, {$ir['username']}! Our hopes are that this tutorial will help you
to better understand our wonderful game. If you are confused by any of the terminology here, please contact a staff
member listed <a href='staff.php'>here</a>.";
echo "<hr />
In {$set['WebsiteName']} , you are entirely free to play as you see fit, so long as you don't break the game rules.
You're free to protect the weak, or exploit their shortcomings. Be charitable with your cash, or keep it all to
yourself. Declare war on a person, or a whole guild. The choice is yours.
<hr />
<h3>Glossary</h3>
	<a href='#basics'>Game Basics</a><br />
	<a href='#navigation'>Navigation</a><br />
	<a href='#explore'>Exploring</a><br />
	<a href='#training'>Training</a><br />
	<a href='#combat'>Combat</a><br />
	<a href='#guilds'>Guilds</a><br />
	<a href='#settings'>Account Settings</a><br />
<hr>
<a name='basics'><h4>Game Basics</h4></a>
	{$set['WebsiteName']} is a Text Themed RPG, meaning everything you do is by clicking on links, or by writing out
	respones. For example, to view your inventory, you would click the Inventory link at the top of the page.
	<br />
	<br />
	<u>Personal Information</u>
	<br />
	If you click the {$set['WebsiteName']} on the top left corner, it'll redirect you to the main index where you can
	view your personal information. This page shows your Stats, Level, {$_CONFIG['primary_currency']}, {$_CONFIG['secondary_currency']}, VIP Days,
	Health, Experience, Will, Brave and Energy. You can also update your Personal Notepad here as well.
	<br />Energy is used for training and attacking.
	<br />Will effects how much you gain while training, so in turn, a low will level means low gains in the gym.
	<br />Brave is used to commit crimes. The more difficult the crime is, the more brave it'll require. Do note that
	committing crimes have other requirements to succeed.
	<br />Experience is how close you are to leveling up.
	<br />Health shows how healthy your character is. You lose Health when you receive a hit in combat.
	<br />
	<br />
	<u>Personal Stats</u>
	<br />
	There are currently five stats in-game: {$_CONFIG['strength_stat']}, {$_CONFIG['agility_stat']}, {$_CONFIG['guard_stat']}, {$_CONFIG['iq_stat']} and {$_CONFIG['labor_stat']}.
	<br />Increasing your {$_CONFIG['strength_stat']} will increase how much damage you can dish out in combat.
	<br />Increasing {$_CONFIG['agility_stat']} will increase your chance of one of your strikes connecting with your opponent.
	<br />Increasing your {$_CONFIG['guard_stat']} will decrease the damage your opponents do to you.
	<br />{$_CONFIG['iq_stat']} and {$_CONFIG['labor_stat']} are miscellaneous stats used around the game. It's good to have these at a fairly decent level.
	<br />
	<br />
<hr />
<a name='navigation'><h4>Navigation</h4></a>
	Being able to view and navigate through {$set['WebsiteName']} is important! The pop-out menu is where you will find access to the rest of the game.
	<br />
	<br />
	<u>Pop-out Menu</u>
	<br />
	The Pop-out is your gateway to the game.
	<br />Clicking on the game name in the top left corner will take you to your Personal Info page.
	<br />Clicking on Explore will allow you to explore many of the features of the game. (More information on this later!)
	<br />Clicking the envelope icon will take you to your personal mailbox where you can write and read letters to others.
	<br />Clicking on the bell icon will take you to your notification box, where game events will be posted if they involve you.
	<br />Clicking Inventory will allow you to view the items you have in your inventory, along with the gear you have
	equipped.
	<br />
	<br />
<hr>
<a name='explore'><h4>Exploring</h4></a>
	Exploring is the best way around the game. The explore page is the highway to all other game features.
	<br />
	<br />
	<u>Shops</u>
	<br />Local Shops are the game-created shops in your town. You'll find an infinite amount of items in these shops.
	<br />However, if you're wanting something a little more flashy, the Item Market is a market ran exclusively by other players. You'll find all sorts
	of items for sale here. Note, that players also set their own pricing.
	<br />The {$_CONFIG['secondary_currency']} Market is an easy way to convert your Secondary
	Currency into {$_CONFIG['primary_currency']}, depending on the market demand.
	<br />
	<br />
	<u>Financial</u>
	<br />Work Center is where you begin your long life of working for 'the man'. You need to have special requirements
	to join some jobs, however.
	<br />The Bank will allow you safely store your {$_CONFIG['primary_currency']}. Storing in the Bank will keep your money safe from
	 being robbed from you, and even gain you interest at midnight each night.
	 <br />Clicking Estates will allow you to view the estates available to purchase. Buying an estate will increase
	 your Maximum Will, thus improving your gains while training.
	 <br />If you're starting to hate the town you're in, Horse Travel is your ticket to other towns. Mind you, towns
	 may have Level requirements, and varying tax levels.
	 <br />The Temple of Fortune is where you can spend your {$_CONFIG['secondary_currency']}. You can refill your Energy, Will, and
	 Brave here. You may also purchase {$_CONFIG['iq_stat']}.
	<br />
	<br />
	<u>Working</u>
    <br />Mining is a great place to find riches. Be careful though, as nearby warriors may get jealous of your haul.
    If you're too careless, you could also ignite a gas pocket, placing yourself into the Infirmary.
    <br />The Smeltery can be used to smelt your items gained while  mining, into better items. Obviously, you will need
     the required items for this to work. The NPC Battle List will allow you to quickly attack NPCs. If you mug the NPCs
     listed, you will be able to get a special item drop. Note that this can only happen once per each bot's cooldown
     time.
     <br />The Gym is the palce you'll want to visit if you have any hope of taking down an enemy.
     <br />The Criminal Center is where you may commit crimes to gain treasure or goods. You need Bravery and good {$_CONFIG['iq_stat']}
     for your level.
     <br />The Learning Academy is where you may enroll in a course that increase your stats in exchange for a lengthy
     studying period.
    <br />
    <br />
	<u>Administration</u>
	<br />Checking out User List will list all the registered users in-game, and allow you to organize them by Level,
	ID, {$_CONFIG['primary_currency']} or name.
	<br />Clicking Users Online will list the players online in the last 15 minutes. You can customize this to any
	duration you wish.
	<br />Staff List will list all in-game staff. These players uphold the law and order of the game. If you have any
	issues, you should contact them!
	<br />The Federal Dungeon is where bad folks go. If you follow the rules, you won't ever have to get locked up. If
	you get locked up, you will lose access to almost all game features.
	<br />Game Statistics will list your game statistics. There's a lot shown here, so check it out to get an idea!
	<br />If you suspect a player breaking a rule, use the Player Report link to report them. Reports here are
	anonymous, and your information won't be used in investigations against players. If you abuse this form, however,
	you will be dealt with harshly.
	<br />Announcements will show you announcements posted by staff members. A lot of important information or changes
	will be listed here.
	<br />The Item Appendix lists all in-game items. You can use this to see how many items are in circulation.
	<br />
	<br />
	<u>Games</u>
	<br />Russian Roulette is a deadly game that you can challenge your friends to. You point a gun to
	each other's heads until someone is shot. If you're shot, game over.
	<br />If that's too high risk for you, how about trying out High/Low? Simply decide if the next drawn number will be
	 higher or lower than the current number. If you're right, you pocket some extra {$_CONFIG['primary_currency']}.
	 <br />If you want a little more thrill, check out Roulette! Pick a number, bet on it, and hope you win!
	 <br />Finally, if you want to play a game of chance, how about betting at the Slot Machines?
	<br />
	<br />
	<u>Guilds</u>
	<br />Firstly, if you have joined a guild, 'Visit Your Guild' will take you to view your guild.
	<br />However, this is useless if you aren't in a guild. To join a guild, or view possible enemies of your own guild,
	 clicking on Guild List will list all the guilds in the game.
	 <br />Clicking on a guild's name will allow you to view detailed information about the guild.
	 <br />If you're curious about guild feuds, however, clicking on Guild Wars will list all active guild wars.
	 <br />You will find more about the usefulness of guilds later in the tutorial.
	<br />
	<br />
	<u>Social</u>
	<br />The Forums are a great place to talk to other members of the game. Please note that its likely someone will give
	 you a hard time. Simply don't take it too hard. It's all in the name of fun.
	<br />The Polling Center is used when the game administration wishes to receive input on something. Voting is optional,
	but recommended if you wish to have any input on changes in-game.
	<br />The Hall of Fame lists the top 20 players in each category. If you're not on this list, don't feel bad! Take some
	time to get yourself in a better state!
	<br />The Game Tutorial tells you how to play the game. Click this if you're confused... oh wait... you already did.
	<br />
	<br />
	<u>Referral Link</u>
	<br />
	Your referral link is your personal registration link. Giving this to your friends will allow you to receive
	rewards when your friends register. Post this link anywhere you see fit. Just don't be a jerk and spam it on
	other games.
	<br />
	<br />
<hr />
<a name='basics'><h4>Training</h4></a>
    Training is a great way to increase your stats. Remember, the higher your stats, the more people you will be able to
    beat in combat.
    <br />
    <br />
    <u>Basic Training</u>
    <br />
    In a nutshell, training involves spending your Will and Energy for stats. To increase your gains while training,
    you should increase your maximum will. You can do this by buying a new estate.
    <br />We recommend buying the best estate for your level. If you begin to have very low gains for your level, wait a
     few minutes for your Will to refill. If  the wait is too much, you can refill your Will at the Temple of Fortune
     found on Explore.
    <br />
    <br />
	<u>Power Training</u>
	<br />
	Power training is the term used to describe when you spend excessive amounts of time, patience and skill training
	your stats to have a significant increase. Power training blows through your resources quickly, so it's a good idea
	to make sure you have enough Secondary Currency before you start power training.
	<br />The idea is to have your Will maxed out, and train using all your Energy in one session, then refilling both
	your Energy and Will before training again. This is complex, so don't worry if you do not understand how this works.
	 <br />There's also many ways of doing this, so you may end up creating your own style of power training.
	<hr />
	<a name='combat'><h4>Combat</h4></a>
	To get almost anywhere in our game, you need to fight others in combat. The weak will fall, and the strong will
	reign supreme. Increasing your {$_CONFIG['agility_stat']}, {$_CONFIG['strength_stat']}, and {$_CONFIG['guard_stat']} will increase your chance of success in combat.
	<br />
	<br />
	<u>Robbing</u>
	<br />
	After successfully besting your opponent in combat, you can choose to Mug them. This will allow you to steal some
	of their {$_CONFIG['primary_currency']}, and place them into the Infirmary. The amount you steal is based upon how much you are
	able to snatch from their person. Obviously, if they have nothing on their person, you won't get anything.
	<br />
	<br />
	<u>Brutality</u>
	<br />
	If the person you've beat has pissed you off, you should Beat them up. This will increase their Infirmary time. Be
	careful, though, as they may end up getting a friend involved to do the same back to you.
	<br />
	<br />
	<u>Experience</u>
	<br />
	If you are just wanting to level up, Leave will allow you to end the fight honorably. You will gain experience, and your
	opponent will spend the least amount of time in the Infirmary out of all the options.
	<br />The experience you gain is based on the Level difference between you and them. Note that you will only receive
	 25% of the experience you would have if their stats are not within 90% of your total stats.
	<br />
	<br />
	<u>Losing A Fight</u>
    <br />
    Losing a fight happens to all of us. You will lose some XP, and be placed into the Infirmary. Take time to train,
    then try attacking them again!
    <br />
    <br />
    <u>Guild Warring</u>
    <br />
	If you are in a guild, you run the risk of having your guild being warred upon. Wars last 72 hours, and the guild
	with the most points win the war.
	<br />To get points, you must be successful in combat against the enemy guild members. Every time you beat an enemy
	guild member, you gain a single point. If you lose in battle, your enemy wins a single point.
	<br />After the conclusion of the war, you cannot redeclare on the enemy guild until 7 days later.
	<br />
	<br />
	<hr>
	<a name='guilds'><h4>Guilds</h4></a>
	Guilds are groups of highly skilled players that band together for a similar purpose. This may be robbing the weak,
	or fighting the strong. Guild shave many nice features. Since guilds are 'learn as you go', we'll only graze on what
	you can do with guilds.
	<br />
	<br />
	<u>Creation</u>
	<br />
	You can create a guild once you reach level {$set['GUILD_LEVEL']} for " . number_format($set['GUILD_PRICE']) . "
	{$_CONFIG['primary_currency']}. You are given an option to choose its name, and a friendly (or not so friendly) description. Once
	you purchase a guild, you become its owner. You have full control of that guild!
	<br />
	<br />
	<u>Increasing Membership</u>
	<br />
	To increase your guild's membership, you need to begin recruiting players. Its highly suggested that you get trustworthy
	players. Normally, if you spam players with invites without getting to know them, they'll just simply ignore you, or
	worse, attempt to attack you! You don't want that.
	<br />
	<br />
	<u>Guild Vault</u>
	<br />
	Your guild's vault is where you and other guild members may donate your cash toward's the guild. This cash can be
	used for many things. Most noteably, buying the armory. The cash here does not gain interest. Be alert though, as the
	vault does hold a finite amount of cash. You can increase this limit by leveling up your guild. You can learn out
	how later in this tutorial.
	<br />
	<br />
	<u>Guild Armory</u>
	<br />
	You can purchase your guild an armory which can hold donated items! You can then give out the items as you see fit. You
	do not need to worry about thieves, as only the owner and co-owner can give out items. If you wish to take an item, please
	contact your guild owners.
	<br />
	<br />
	<u>Guild Crimes</u>
	<br />
	Your guild can plan and commit crimes. Crimes are a great way to get cash for your guild's vault. Also note that successfully
	committing crimes gains your guild a small amount of experience.
	<br />
	<br />
	<hr>
	<a name='settings'><h4>Account Settings</h4></a>
	By going to your preferences page (by selecting the gear icon from the menu), you'll be able to change key information about your account.
	It is highly recommended you check out this page, as more options may end up becoming available.
	<br />
	<br />
	<u>Name Change</u>
	<br />
	Here you may change your name, free of charge. Note that this only changes your name displayed around the game, not
	your User ID. You will not be able to change your name to escape consequences.
	<br />
	<br />
	<u>Password Change</u>
	<br />
	You may change your password at will. We highly recommend using a password you haven't used elsewhere, and cycling
	your password out every 3 months or so. We take reasonable steps to ensure your password isn't stolen.
	<br />
	<br />
	<u>Email Opt Setting</u>
	<br />
	We respect your choice to opt in or out of our game emails. If you opt-in, you will receive our game emails. If you
	opt-out, you won't. Its simple, right?
	<br />
	<br />
	<u>Display Picture</u>
	<br />
	Your default display picture is the image linked to your Email Address when checking
	<a href='https://www.gravatar.com'>Gravatar</a>. You can change your picture there, or by inputting your own custom
	URL Picture. All pictures must be externally hosted. We recommend using Gravatar or Imgur. Note we do not have control
	of either of these websites.
	<br />
	<br />
	<u>Sex Change</u>
	<br />
	You can change your sex for free. Try getting that deal in the real world.
	<br />
	<br />
	<u>Forum Signature</u>
	<br />
	You can change your forum signature. You may use BBCode. Please keep it PG-13";
$h->endpage();