<?php
/*
	File:		tutorial.php
	Created: 	5/14/2017 at 5:57 Eastern Time
	Info: 		A very detailed game tutorial.
	Author:		TheMasterGeneral
	Website: 	https://github.com/MasterGeneral156/chivalry-engine
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
	view your personal information. This page shows your Stats, Level, Primary Currency, Secondary Currency, VIP Days,
	Health, Experience, Will, Brave and Energy. You can also update your Personal Notepad here as well. Energy is used
	for training and attacking. Will effects how much you gain while training, so in turn, a low will level means low
	gains in the gym. Brave is used to commit crimes. The more difficult the crime is, the more brave it'll require. Do
	note that committing crimes have other requirements to succeed. Experience is how close you are to leveling up.
	Health shows how healthy your character is. You lose Health when you receive a hit in combat.
	<br />
	<br />
	<u>Personal Stats</u>
	<br />
	There are currently five stats in-game: Strength, Agility, Guard, IQ and Labor. Increasing your Strength will
	increase how much damage you can dish out in combat. Increasing Agility will increase your chance of one of your
	strikes connecting with your opponent. Increasing your Guard will decrease the damage your opponents do to you.
	IQ and Labor are miscellaneous stats used around the game. It's good to have these at a fairly decent level.
	<br />
	<br />
<hr />
<a name='navigation'><h4>Navigation</h4></a>
	Being able to view and navigate through {$set['WebsiteName']} is important! The top navigation bar (or dropdown, if
	you're on mobile) is where you will find access to the rest of the game.
	<br />
	<br />
	<u>Navigation Bar</u>
	<br />
	The navigation bar (or dropdown) is your gateway to the game. Clicking on the game name in the top left corner will
	take you to your Personal Info page. Clicking on Explore will allow you to explore many of the features of the game.
	(More information on this later!) Clicking Mail will take you to your personal mailbox where you can write and read
	letters to others. Notifications will take you to your notification box, where game events will be posted if they
	involve you. Clicking Inventory will allow you to view the items you have in your inventory, along with the gear
	you have equipped.
	<br />
	<br />
	<u>Navigation Bar Dropdown</u>
	<br />
    Clicking the 'Hello, {$ir['username']}' Dropdown will display more information. Clicking on Profile will take you to
    your personal profile page. This is what others see when they click on your name in-game. Clicking on Settings will
    take you to your account settings. Here you can change your display picture, password, username, and forum signature!
     Clicking Game Rules will show you the rules of the game. It's recommended that you read these over so you know what
      you can and cannot do. Finally, clicking Logout will terminate your session.
    <br />
    <br />
<hr>
<a name='explore'><h4>Exploring</h4></a>
	Exploring is the best way around the game. The explore page is the highway to all other game features.
	<br />
	<br />
	<u>Shops</u>
	<br />
	Hovering over the Shops category will display the numerous ways you can buy things in the game. Local Shops are the
	game-created shops in your town. You'll find a finite amount of items in these shops. However, if you're wanting
	something a little more flashy, the Item Market is a market ran exclusively by other players. You'll find all sorts
	of items for sale here. Note, that players also set their own pricing. The Item Auction is a great place to see if
	you can pick up some awesome prices on items by bidding on them, eBay style. Trading will allow you to conduct
	business privately with another player. The Secondary Currency Market is an easy way to convert your Secondary
	Currency into Primary Currency, depending on the market demand.
	<br />
	<br />
	<u>Financial</u><br />
	Hovering over Financial will greet you with a category of ways to use your currency. The Bank will allow you safely
	store your Primary Currency. Storing in the Bank will keep your money safe from being robbed from you, and even gain
	you interest at midnight each night. Clicking Estates will allow you to view the estates available to purchase.
	Buying an estate will increase your Maximum Will, thus improving your gains while training. If you're starting to
	hate the town you're in, Horse Travel is your ticket to other towns. Mind you, towns may have Level requirements,
	and varying tax levels. The Temple of Fortune is where you can spend your Secondary Currency. You can refill your
	Energy, Will, and Brave here. You may also purchase IQ.
	<br />
	<br />
	<u>Labor</u>
	<br />
    You will find that the labor category is where you will spend a lot of your time at. Mining is a great place to find
    riches. Be careful though, as nearby warriors may get jealous of your haul. If you're too careless, you could also
    ignite a gas pocket, placing yourself into the Infirmary. The Smeltery can be used to smelt your items gained while
    mining, into better items. Obviously, you will need the required items for this to work. Woodcutting will allow you
    to chop trees to sell for cold, hard currency. Farming will allow you to channel your inner farmer. You can use the
    harvested crops to create better foods, or sell for a profit. Finally, the Bot List will allow you to quickly
    attack NPCs. If you mug the NPCs listed, you will be able to get a special item drop. Note that this can only
    happen once per each bot's cooldown time.
    <br />
    <br />
	<u>Administration</u>
	<br />
	This category has no general theme, to be honest. Checking out User List will list all the registered users in-game,
	and allow you to organize them by Level, ID, Primary Currency or name. Clicking Users Online will list the players
	online in the last 15 minutes. You can customize this to any duration you wish. Staff List will list all in-game
	staff. These players uphold the law and order of the game. If you have any issue, you should contact them! The
	Federal Jail is where bad folks go. If you follow the rules, you won't ever have to get locked up. If you get
	locked up, you will lose access to almost all game features. Game Stats will list your game statistics. There's a
	lot shown here, so check it out to get an idea! If you suspect a player breaking a rule, use the Player Report link
	to report them. Reports here are anonymous, and won't be used in investigations against players. If you abuse this
	form, however, you will be dealt with harshly. Finally, Announcements will show you announcements posted by staff
	members. A lot of important information or changes will be listed here.
	<br />
	<br />
	<u>Games</u><br />
	We have several games for you to play to keep your mind off the stress of the game. Games inside of a game. Isn't
	this how you destroy the world? In either case, Russian Roulette is a deadly game that you can challenge your
	friends to. You point a gun to each other's heads until someone is shot. If you're shot, game over. If that's too
	high risk for you, how about trying out High/Low? Simply decide if the next drawn number will be higher or lower
	than the current number. If you're right, you pocket some extra Primary Currency. If you want a little more thrill,
	check out Roulette! Pick a number, bet on it, and hope you win! Finally, if you want to play a game of chance,
	how about betting at the Slot Machines?
	<br />
	<br />
	<u>Guilds</u>
	<br />
	Hovering over the Guilds section will display links pertaining to guilds. Firstly, if you have joined a guild, Your
	Guild will take you to view your guild. However, this is useless if you aren't in a guild. To join a guild, or view
	possible enemies of your own guild, clicking on Guild List will list all the guilds in the game. Clicking on a
	guild's name will allow you to view detailed information about the guild. If you're curious about guild feuds,
	however, clicking on Guild Wars will list all active guild wars. You will find more about the usefulness of
	guilds later in the tutorial.
	<br />
	<br />
	<u>Activities</u>
	<br />
	Opening the activities section will allow you to view numerous things to do around the game. Firstly, the Dungeon
	will list the players in the dungeon. You will be placed here if you are caught committing crimes. The Infirmary
	will show those who are receiving medical treatment. You will be placed here if you sustain excessive injuries. To
	get stronger in game, we suggest bulking up at the Training. Here you may train your stats. The importance of these
	stats are explained in the basics. You cannot get spending cash until you work, so we suggest finding work by
	clicking on Your Job. Here you will be paid, based on your rank in the company you wish to work for. Different
	companies pay different rates, and have different requirements. More information about this later. Visiting the
	Local Academy will allow you to enroll in a course. Courses cost money, and after the course finishes, you will be
	awarded will a substantial amount of stats. Courses may have other requirements. If you're wanting to make some
	quick, but risky money, try chekcing out the Criminal Center. Here you can commit crimes, which cost Brave. There's
	the chance you will be caught. The success rate is determined by your Level, Will, and most definitely IQ. Finally,
	clicking Game Tutorial will load the game tutorial for you to read. If you are ever confused, view the tutorial.
	Oh wait... you already are.
	<br />
	<br />
	<u>Social</u>
	<br />
	Opening the Social section will allow you to view ways you can interact with other players in a public setting.
	Forums will allow you to view the in-game forums. You can create your own threads, and reply to others. Remember,
	don't get upset if someone gives you a hard time, it's all in good fun. Viewing the game's Newspaper will allow you
	to see ads posted by other players. You can, of course, post your own ads. You can write about anything here,
	awesome enough. Finally, the Polling Booth will be home to the polls created by the staff members, of which, you
	are free vote in. You can also view the results of the previous polls here as well. Polls are used to get an idea
	of how other players feel about changes in-game.
    <br />
    <br />
	<u>Top 10 Players</u>
	<br />
	The Top 10 Players listing will list the top ten strongest players in the game, in order of total stats. These are
	the players you want to beat. It's a high honor to be a part of this list. It takes lots of time and dedication to
	get here. Don't be upset if you can't get here.
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
    you should increase your maximum will. You can do this by buying a new estate. We recommend buying the best estate
    for your level. If you begin to have very low gains for your level, wait a few minutes for your Will to refill. If
    the wait is too much, you can refill your Will at the Temple of Fortune found on Explore.
    <br />
    <br />
	<u>Power Training</u>
	<br />
	Power training is the term used to describe when you spend excessive amounts of time, patience and skill training
	your stats to have a signifcant increase. Power training blows through your resources quickly, so it's a good idea
	to make sure you have enough Secondary Currency before you start power training. The idea is to have your Will
	maxed out, and train using all your Energy in one session, then refilling both your Energy and Will before
	training again. This is complex, so don't worry if you do not understand how this works. There's also many ways of
	doing this, so you may end up creating your own style of power training.
	<hr />
	<a name='combat'><h4>Combat</h4></a>
	To get almost anywhere in our game, you need to fight others in combat. The weak will fall, and the strong will
	reign supreme. Increasing your Agility, Strength, and Guard will increase your chance of success in combat.
	<br />
	<br />
	<u>Robbing</u>
	<br />
	After successfully besting your opponent in combat, you can choose to Mug them. This will allow you to steal some
	of their Primary Currency, and place them into the Infirmary. The amount you steal is based upon how much you are
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
	opponent will spend the least amount of time in the Infirmary out of all the options. The experience you gain is based on
	the Level difference between you and them. Note that you will only receive 25% of the experience you would have if
	their stats are not within 90% of your total stats.
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
	with the most points win the war. To get points, you must be successful in combat against the enemy guild members.
	Every time you beat an enemy guild member, you gain a single point. If you lose in battle, your enemy wins a single
	point. After the conclusion of the war, you cannot redeclare on the enemy guild until 7 days later.
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
	Primary Currency. You are given an option to choose its name, and a friendly (or not so friendly) description. Once
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
	By going to your preferences page (from the dropdown), you'll be able to change key information about your account.
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
	<u>Timezone Change</u>
	<br />
	You can change your timezone to be different than the server's time. Note that all internal server times are ran off of
	Unix Time, or GMT-0.
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