<?php
/*
	File: 		lang/en_us.php
	Created: 	6/1/2016 at 6:06PM Eastern Time
	Info: 		The English language file.
	Author: 	TheMasterGeneral
	Website:	 https://github.com/MasterGeneral156/chivalry-engine
*/
 
static $lang = array();
global $ir,$fee,$gain,$set;

//Index
$lang['INDEX_TITLE']="General Info";
$lang['INDEX_WELCOME']="Welcome back,";
$lang['INDEX_YLVW']="Your last visit was on";
$lang['INDEX_LEVEL']="Level";
$lang['INDEX_CLASS']="Class";
$lang['INDEX_VIP']="VIP Days";
$lang['INDEX_PRIMCURR']="Primary Currency";
$lang['INDEX_SECCURR']="Secondary Currency";
$lang['INDEX_ENERGY']="Energy";
$lang['INDEX_BRAVE']="Brave";
$lang['INDEX_WILL']="Will";
$lang['INDEX_PN']="Personal Notepad";
$lang['INDEX_PNSUCCESS']="Your personal notepad has been updated successfully.";
$lang['INDEX_EXP']='XP';
$lang['INDEX_HP']='HP';

//Generic
$lang["GEN_HERE"] = "here";
$lang["GEN_back"] = "back";
$lang["GEN_INFIRM"] = "Unconscious!";
$lang["GEN_DUNG"] = "Locked Up!";
$lang["GEN_GREETING"] = "Hello";
$lang["GEN_MINUTES"] = "minutes.";
$lang['GEN_EXP']="Experience";
$lang['GEN_NEU']="Deleted Account";
$lang['GEN_AT']="at";
$lang['GEN_EDITED']="edited";
$lang['GEN_TIMES']="times.";
$lang['GEN_RANK']='Rank';
$lang['GEN_ONLINE']='Online';
$lang['GEN_OFFLINE']='Offline';
$lang['GEN_FOR']="for";
$lang['GEN_INDAH']="In the";
$lang['GEN_YES']="Yes";
$lang['GEN_NO']="No";
$lang['GEN_STR']="Strength";
$lang['GEN_AGL']="Agility";
$lang['GEN_GRD']="Guard";
$lang['GEN_IQ']="IQ";
$lang['GEN_LAB']="Labor";
$lang['GEN_GOHOME']="Go Home";
$lang['GEN_IUOF']="Invalid use of file!";
$lang['GEN_THEM']="Them";
$lang['GEN_CONTINUE']="Continue";
$lang['GEN_FOR_S']="for";
$lang['GEN_HAVE']="Have";
$lang['GEN_AND']="and";
$lang['GEN_NOPERM']="You do not have the proper user level to view this page. If this is wrong, please contact an admin immediately!";
$lang["GEN_STATS"] = "Stats";
$lang["GEN_RANKED"] = "Ranked";
$lang["GEN_BACK"] = "Go Back";
$lang["GEN_IN"] = "In";
$lang['GEN_INCREASE']="Increases";
$lang['GEN_DECREASE']="Decreases";
$lang['GEN_UPTODATE']="Chivalry Engine is up to date.";
$lang['GEN_OUTTADATE']="Chivalry Engine is out of date. Get the latest build ";
$lang['GEN_FAILEDTOCHECK']="Could not contact update checker.";

// Menu
$lang["MENU_EXPLORE"] = "Explore";
$lang["MENU_MAIL"] = "Mail";
$lang["MENU_EVENT"] = "Notifications";
$lang["MENU_INVENTORY"] = "Inventory";
$lang["MENU_OUT"] = "Powered with codes by TheMasterGeneral. View source on <a href='https://github.com/MasterGeneral156/chivalry-engine'>GitHub</a>. Used with permission.";
$lang['MENU_PROFILE']='Profile';
$lang['MENU_SETTINGS']='Settings';
$lang['MENU_STAFF']='Staff Panel';
$lang['MENU_LOGOUT']='Log Out';
$lang['MENU_TIN']='Time is now';
$lang['MENU_QE']='queries executed';
$lang['MENU_UNREADMAIL1']='Unread Mail!';
$lang['MENU_UNREADNOTIF']='Unread Notifications!';
$lang['MENU_FEDJAIL']="Federal Jail!";
$lang['MENU_FEDJAIL1']="You're in the federal jail for the next";
$lang['MENU_FEDJAIL2']="for the crime of:";
$lang['MENU_UNREADANNONCE']='Unread Announcements!';
$lang['MENU_UNREADANNONCE1']='There are';
$lang['MENU_UNREADANNONCE2']='announcements you have yet to read. Read them';
$lang['MENU_UNREADMAIL2']='You have';
$lang['MENU_UNREADMAIL3']='unread messages. Click';
$lang['MENU_UNREADMAIL4']='to read them.';
$lang['MENU_UNREADNOTIF1']='unread notifications. Click';
$lang['MENU_INFIRMARY1']='You are in the infirmary for the next';
$lang['MENU_DUNGEON1']='You are in the dungeon for the next';
$lang['MENU_XPLOST']="By running from the fight, you have lost all your {$lang['GEN_EXP']}!";
$lang['MENU_RULES']="Game Rules";
$lang['MENU_DONATE']="Donate to ";
$lang['MENU_DONATE2']=". You'll receive tons of benefits! Donate by clicking <a href='donator.php'>here</a>.";
$lang['MENU_SCRIPTTIME']="Page loaded in";
$lang['MENU_SCRIPTTIME1']="ms.";

// Preferences
$lang["PREF_CPASSWORD"] = "Change Password";
$lang["PREF_WELCOME_1"] = "Greetings there,";
$lang["PREF_WELCOME_2"] = ", and welcome to the Preferences Center. You may view and change information about your account!";
$lang["PREF_CNAME"] = "Change Username";
$lang["PREF_CTIME"] = "Change Timezone";
$lang["PREF_CLANG"] = "Change Language";
$lang["PREF_CPIC"] = "Change Display Picture";
$lang["PREF_CTHM"] = "Change Theme";
$lang["PREF_CTHM_FORM"] = "Select the theme you wish to change to. This action can be reverted at any time you want.";
$lang["PREF_CTHM_FORM1"]="Select your theme";
$lang["PREF_CTHM_FORMDD1"]="Day Mode";
$lang["PREF_CTHM_FORMDD2"]="Night Mode";
$lang['PREF_CTHM_FORMBTN']="Update Theme";
$lang['PREF_CTHM_SUB_ERROR']="You are trying to use an non-existent theme.";
$lang['PREF_CTHM_SUB_SUCCESS']="Your theme has been updated successfully. Effects will be noticeable on the next page load.";
$lang["PREF_CSIG"] = "Change Forum Signature";
$lang["PREF_CSEX"] = "Change Gender";

//Username Change
$lang["UNC_TITLE"] = "Changing your username...";
$lang["UNC_INTRO"] = "Here you can change your name that is shown throughout the game. Do not use an inappropriate name or you may find your privilege to change your name removed.";
$lang["PREF_CNAME"] = "Change Username";
$lang["UNC_ERROR_1"] = "You did not even enter a new username! Click ";
$lang["UNC_ERROR_2"] = " to try again";
$lang['UNC_LENGTH_ERROR'] = "A valid username must be, at minimum, three characters in length and, at maximum, twenty characters.";
$lang['UNC_INVALIDCHARCTERS'] = "A valid username only consists of numbers, letters, underscores and spaces!";
$lang['UNC_INUSE'] = "The username you have chosen is in use. Please select another username.";
$lang['UNC_GOOD'] = "You have successfully updated your username!";
$lang['UNC_NUN'] = "New Username:";
$lang['UNC_BUTTON'] = "Change Username";

//Password Change
$lang["PW_TITLE"] = "Changing your password...";
$lang['PW_CP'] = "Current Password";
$lang['PW_CNP'] = "Confirm New Password";
$lang['PW_NP'] = "New Password";
$lang['PW_BUTTON'] = "Update Password";
$lang['PW_INCORRECT'] = "What you entered as your old password is incorrect. Try again.";
$lang['PW_NOMATCH'] = "The new passwords you entered do not match. Go back and try again, please.";
$lang['PW_DONE'] = "Your password has been updated successfully.";

//Pic change
$lang['PIC_TITLE']="Display Picture Change";
$lang['PIC_NOTE']="Please note that this must be externally hosted, <a href='https://imgur.com/'>Imgur</a> is our recommendation.";
$lang['PIC_NOTE2']="Any images that are not 250x250 will be automatically scaled to 250x250.";
$lang['PIC_NEWPIC']="Link to new picture:";
$lang['PIC_TOOBIG']="Picture Too Large!";
$lang['PIC_BTN']="Change Picture";
$lang['PIC_TOOBIG2']="Your image's file size is too large. The maximum size an image can be is 1MB. Go back and try again, please.";
$lang['PIC_NOIMAGE']="You specified a URL that is not even an image. Go back and try again, please.";
$lang['PIC_SUCCESS']="You have successfully updated your display picture! It is shown below.";

//Signature Change
$lang['SIG_TITLE']="Changing Forum Signature. You may use BBCode.";
$lang['SIG_YSIG']="Your Signature";
$lang['SIG_BTN']="Change Signature";
$lang['SIG_ERR']="Your signature must be less than 1025 characters in length. This includes BBCode.";
$lang['SIG_SUCC']="You have successfully updated your forum signature.";

//Sex change
$lang['SEX_INFO']="Use this form to change your gender. Unlike the real world, changes here are free.";
$lang['SEX_BTN']="Change Gender";
$lang['SEX_ERR']="You specified an invalid or non-existent gender.";
$lang['SEX_ERR1']="You are already this gender!";
$lang['SEX_SUCC']="You have successfully changed your gender.";

//Login Page
$lang["LOGIN_REGISTER"] = "Register";
$lang["LOGIN_RULES"] = "Game Rules";
$lang["LOGIN_LOGIN"] = "Login";
$lang["LOGIN_AHA"] = "Already have an account?";
$lang["LOGIN_EMAIL"] = "Email address";
$lang["LOGIN_PASSWORD"] = "Password";
$lang["LOGIN_LWE"] = "Email Login! <a href='pwreset.php'>Forgot password?</a>";
$lang["LOGIN_SIGNIN"] = "Sign In";
$lang["LOGIN_NH"] = "New Here? <a href='register.php'>Join Us</a>!";
$lang['LOGIN_LA'] = "Latest Announcement";
$lang['LOGIN_TG'] = "Top 10 Guilds";
$lang['LOGIN_TP'] = "Top 10 Players";

//Register
$lang["REG_FORM"] = "Registration";
$lang["REG_USERNAME"] = "Username";
$lang["REG_EMAIL"] = "Email";
$lang["REG_PW"] = "Password";
$lang["REG_CPW"] = "Confirm Password";
$lang["REG_SEX"] = "Gender";
$lang["REG_CLASS"] = "Class";
$lang["REG_REFID"] = "Referral ID";
$lang["REG_PROMO"] = "Promo Code";
$lang['REG_WARRIORCLASS']="Warrior Class!";
$lang['REG_ROGUECLASS']="Rogue Class!";
$lang['REG_DEFENDERCLASS']="Defender Class!";
$lang['REG_NOCLASS']="We need you to select a class, please.";
$lang['REG_ROGUECLASS_INFO']="A rogue fighter starts with more agility and less strength. Throughout their adventures, they'll gain agility much quicker than any other stat, and strength much slower than the others.";
$lang['REG_DEFENDERCLASS_INFO']="A defender starts with more guard and less agility. Throughout their adventures, they'll gain guard much quicker than any other stat, and agility much slower than the others.";
$lang['REG_WARRIORCLASS_INFO']="A warrior tarts with more strength and less guard. Throughout their adventures, they'll gain strength way quicker than any other stat, and guard much slower than the others.";
$lang['REG_UNIUERROR']="The username you chose is already in use. Go back and try again.";
$lang['REG_SUCCESS']="You have successfully joined the game. Enjoy your stay and please be sure to read the game rules.";
$lang['REG_EIUERROR']="The email you chose is already in use. Go back and try again.";
$lang['REG_PWERROR']="You must enter a password and confirm it. Go back and try again.";
$lang['REG_REFERROR']="The referral you specified does not exist in-game. Go back and verify again.";
$lang['REG_REFMERROR']="The referral you specified shares the same IP as you. No creating multiple accounts. The admins have been alerted.";
$lang['REG_VPWERROR']="The passwords you entered do not match. Go back and try again.";
$lang['REG_CAPTCHAERROR']="You failed the captcha, or just didn't enter it. Go back and try again.";
$lang['REG_GENDERERROR']="You specified an invalid gender. Please go back and try again.";
$lang['REG_CLASSERROR']="You specified an invalid fighting class. Please go back and try again.";
$lang['REG_EMAILERROR']="You did not enter a valid email, or failed to enter the email field. Please go back and try again.";
$lang['REG_MULTIALERT']="Hold on there. We've detected that someone with your IP address has already registered. We're going to stop you here for now. If this is a false positive, please email the game owners.";
$lang['REG_USEREMPTY']="You didn't input a username. Go back and try again, please.";
$lang['REG_UNPLACE']="This is your in-game name.";
$lang['REG_EPLACE']="You will use this to log into the game.";
$lang['REG_PWPLACE']="Unique passwords use letters, symbols, and numbers.";
$lang['REG_PW1PLACE']="Now, confirm the previously entered password.";
$lang['REG_REFPLACE']="Refered by a friend? Enter their User ID here! (Optional)";
$lang['REG_PROMOPLACE']="Received a promo code? Enter it here. (Optional)";
$lang['REG_OPTIN']="By clicking Register, you accept you have read the <a href='gamerules2.php'>Game Rules</a> and our <a href='privacy.php'>Privacy Policy</a>. You also agree that you wish to opt-in to our game newsletter. You may opt-out at anytime by checking your in-game settings.";

//CSRF Error
$lang["CSRF_ERROR_TITLE"] = "Action Blocked!";
$lang["CSRF_PREF_MENU"] = "You can try the action again by going";
$lang["CSRF_ERROR_TEXT"] = "The action you were trying to do was blocked. It was blocked because you loaded another page on the game. If you have not loaded a different page during this time, change your password immediately, as another person may have access to your account!";

//Alert Titles
$lang['ERROR_EMPTY'] = "Empty Input!";
$lang['ERROR_LENGTH'] = "Check Input Length!";
$lang['ERROR_GENERIC'] = "Uh Oh!";
$lang['ERROR_SUCCESS'] = "Success!";
$lang['ERROR_INVALID'] = "Invalid Input!";
$lang['ERROR_SECURITY'] = "Security Error!";
$lang['ERROR_NONUSER'] = "Nonexistent User!";
$lang['ERROR_NOPERM'] ="No Permission!";
$lang['ERROR_UNKNOWN'] ="Unknown Error!";
$lang['ERROR_INFO']="Information!";

//Misc. Alerts Details
$lang['ALERT_INSTALLER']="The installer file could not be deleted. Please be sure to delete installer.php from your website's root folder, or you will risk another user running the installer and ruining your game.";

//Gym
$lang['GYM_INFIRM'] = "While you are unconscious, you cannot train! Come back after you are feeling healthy!";
$lang['GYM_DUNG'] = "The guards would normally let you work out, but, what you did was deemed too high of a crime. You cannot train right now...";
$lang['GYM_NEG'] = "Not Enough Energy!";
$lang['GYM_INVALIDSTAT'] = "You cannot train that stat!";
$lang['GYM_NEG_DETAIL'] = "You do not have enough energy to train that many times. Either wait for your energy to recover, or refill it manually!";
$lang['GYM_INFO']="Training";
$lang['GYM_FRM1']="Choose the stat you wish to train, and how many times you wish to train it. You can train up to";
$lang['GYM_FRM2']="times.";
$lang['GYM_TH']="Stat to Train";
$lang['GYM_TH1']="Training Duration";
$lang['GYM_BTN']="Train!";
$lang['GYM_STR']="You begin to start lifting heavy rocks. You've successfully gained";
$lang['GYM_STR1']="by doing";
$lang['GYM_STR2']="minutes of heavy rock lifting. You now have";
$lang['GYM_STR3']="energy remaining.";
$lang['GYM_AGL']="You begin running laps. You've successfully gained";
$lang['GYM_AGL1']="laps around town.";
$lang['GYM_GRD']="You jump into the river and begin swimming. You have successfully gained";
$lang['GYM_GRD1']="minutes of swimming.";
$lang['GYM_YNH']="You now have";
$lang['GYM_LAB']="You begin helping around your town. You have successfully gained";
$lang['GYM_LAB1']="minutes of helping around your town.";
$lang['GYM_ALL']="All Four";
$lang['GYM_ALL_BEGIN']="You begin training all four of your stats at once. You have gained";
$lang['GYM_ALL_BEGIN1']="in {$lang['GEN_STR']},";
$lang['GYM_ALL_BEGIN2']="in {$lang['GEN_AGL']},";
$lang['GYM_ALL_BEGIN3']="in {$lang['GEN_GRD']},";
$lang['GYM_ALL_BEGIN4']="in {$lang['GEN_LAB']} and have";

//Explore
$lang['EXPLORE_INTRO']='You begin exploring the town and find a few cool things to keep you occupied...';
$lang['EXPLORE_REF']="This is your referral link. Give it to friends, associates, and enemies. You'll receive 25 {$lang['INDEX_SECCURR']} upon them joining!";
$lang['EXPLORE_SHOP']="Shops";
$lang['EXPLORE_LSHOP']="Local Shops";
$lang['EXPLORE_POSHOP']="Player-Owned Shops";
$lang['EXPLORE_IMARKET']="Item Market";
$lang['EXPLORE_IAUCTION']="Item Auction";
$lang['EXPLORE_TRADE']="Trading";
$lang['EXPLORE_SCMARKET']="{$lang['INDEX_SECCURR']} Market";
$lang['EXPLORE_FD']="Financial";
$lang['EXPLORE_BANK']="Bank";
$lang['EXPLORE_ESTATES']="Estates";
$lang['EXPLORE_HL']="Labor";
$lang['EXPLORE_MINE']="Mining";
$lang['EXPLORE_SMELT']="Smeltery";
$lang['EXPLORE_WC']="Woodcutting";
$lang['EXPLORE_FARM']="Farming";
$lang['EXPLORE_ADMIN']="Administration";
$lang['EXPLORE_USERLIST']="User List";
$lang['EXPLORE_STAFFLIST']="Staff List";
$lang['EXPLORE_FED']="Federal Jail";
$lang['EXPLORE_STATS']="Game Stats";
$lang['EXPLORE_REPORT']="Player Report";
$lang['EXPLORE_GAMES']="Games";
$lang['EXPLORE_RR']="Russian Roulette";
$lang['EXPLORE_HILO']="High/Low";
$lang['EXPLORE_ROULETTE']="Roulette";
$lang['EXPLORE_GUILDS']="Guilds";
$lang['EXPLORE_DUNG']="Dungeon";
$lang['EXPLORE_INFIRM']="Infirmary";
$lang['EXPLORE_GYM']="Training";
$lang['EXPLORE_JOB']="Your Job";
$lang['EXPLORE_ACADEMY']="Local Academy";
$lang['EXPLORE_PINTER']="Social";
$lang['EXPLORE_FORUMS']="Forums";
$lang['EXPLORE_NEWSPAPER']="Newspaper";
$lang['EXPLORE_ACT']="Activities";
$lang['EXPLORE_ANNOUNCEMENTS']="Announcements";
$lang['EXPLORE_CRIMES']="Criminal Center";
$lang['EXPLORE_TRAVEL']="Horse Travel";
$lang['EXPLORE_GUILDLIST']="Guild List";
$lang['EXPLORE_YOURGUILD']="Your Guild";
$lang['EXPLORE_TOPTEN']="Top 10 Players";
$lang['EXPLORE_SLOTS']="Slot Machines";
$lang['EXPLORE_BOTS']="Bot List";
$lang['EXPLORE_TEMPLE']="Temple of Fortune";
$lang['EXPLORE_WARS']="Guild Wars";
$lang['EXPLORE_TUTORIAL']="Game Tutorial";

//Error Details
$lang['ERRDE_EXPLORE']="Since you are in the infirmary, you cannot visit the town!";
$lang['ERRDE_EXPLORE2']="Since you are in the dungeon, you cannot visit the town!";
$lang['ERRDE_PN']="Your personal notepad could not be updated due to the 65,655 character limit.";
$lang['ERROR_MAIL_UNOWNED']='This message does not exist or was not sent to you.';
$lang['ERROR_FORUM_VF']="Go back and try again for us, please. We done broke.";

//Form Buttons
$lang['FB_PN']="Update Notes";
$lang['FB_PR']="Submit Player Report";

//Player Report
$lang['PR_TITLE']="Player Report";
$lang['PR_INTRO']="Know someone who broke the rules, or is just being dishonorable? This is the place to report them. Report the user just once. Reporting the same user multiple times will slow down the process. If you are found to be abusing the player report system, you will be placed away in federal jail. Information you enter here will remain confidential and will only be read by senior staff members. If you wish to confess to a crime, this is also a great place too.";
$lang['PR_USER']="User?";
$lang['PR_CATEGORY']="Category?";
$lang['PR_REASON']="What have they done?";
$lang['PR_USER_PH']="User ID of the player being bad.";
$lang['PR_REASON_PH']="Please include as much information as possible.";
$lang['PR_CAT_1']='Bug Abuse';
$lang['PR_CAT_2']='Player Harassment';
$lang['PR_CAT_3']='Scamming';
$lang['PR_CAT_4']='Spamming';
$lang['PR_CAT_5']='Encouraging Rule Breaking';
$lang['PR_CAT_6']='Security Issue';
$lang['PR_CAT_7']='Other';
$lang['PR_CATBAD']='You specified an invalid category. Go back and try again, please.';
$lang['PR_MAXCHAR']='You are attempting to enter too long of a reason. This form will only allow you to enter, at maximum, 1250 total characters. Go back and try again, please.';
$lang['PR_INVALID_USER']='You are trying to report a player who just does not exist. Check the user ID you entered and try again.';
$lang['PR_SUCCESS']='You have successfully reported the user. Staff may send you a message asking questions about the report you just sent. Please answer them to the best of your ability.';

//Mail
$lang['MAIL_READ']='Read';
$lang['MAIL_DELETE']='Delete';
$lang['MAIL_REPORT']='Report';
$lang['MAIL_MSGREAD']='Read';
$lang['MAIL_MSGUNREAD']='Unread';
$lang['MAIL_USERDATE']='User/Info';
$lang['MAIL_PREVIEW']='Message Preview';
$lang['MAIL_ACTION']='Actions';
$lang['MAIL_USERINFO']='Sender Info';
$lang['MAIL_MSGSUB']='Subject/Message';
$lang['MAIL_STATUS']='Status';
$lang['MAIL_SENTAT']='Sent at';
$lang['MAIL_SENDTO']='To';
$lang['MAIL_FROM']='From';
$lang['MAIL_SUBJECT']='Subject';
$lang['MAIL_MESSAGE']='Message';
$lang['MAIL_REPLYTO']='Reply To';
$lang['MAIL_EMPTYINPUT']='It appears you did not enter a message to be sent. Please go back and enter a message!';
$lang['MAIL_INPUTLNEGTH']='It would appear that you are attempting to send a lengthy message. Remember that messages can only be 65,655 characters long, and subjects can only be 50 characters long.';
$lang['MAIL_NOUSER']='You must enter a recipient for this message! Go back and try again!';
$lang['MAIL_UDNE']='User Does Not Exist!';
$lang['MAIL_UDNE_TEXT']='You are attempting to send a message to a user who does not exist. Check your source and try again.';
$lang['MAIL_SUCCESS']='You have successfully sent a message!';
$lang['MAIL_TIMEERROR']='You must wait 60 seconds before you can send a message to this user using this form specifically. If you need to quickly reply to someone, you can still use the normal mail system.';
$lang['MAIL_READALL']='All your unread messages has been marked as read!';
$lang['MAIL_DELETECONFIRM']='Are you 100% sure you want to empty your inbox? This cannot be undone.';
$lang['MAIL_DELETEYES']='Yes, I am 100% sure';
$lang['MAIL_DELETENO']='Hold on, on second thought';
$lang['MAIL_DELETEDONE']='Your entire inbox has been successfully cleared.';
$lang['MAIL_QUICKREPLY']='Sending a quick reply...';
$lang['MAIL_MARKREAD']='Mark All as Read';
$lang['MAIL_SENDMSG']='Send Message';
$lang['MAIL_TH1_IN']="Inbox";
$lang['MAIL_TH1_OUT']="Outbox";
$lang['MAIL_TH1_COMP']="Compose";
$lang['MAIL_TH1_DEL']="Delete All";
$lang['MAIL_TH1_ARCH']="Archive";
$lang['MAIL_TH1_CONTACTS']="Contacts";
$lang['MAIL_TH1_ARC']="Select which archive you wish to download.";
$lang['MAIL_TH1_ARC1']="Inbox";
$lang['MAIL_TH1_ARC2']="Outbox";

//Contacts Page
$lang['CONTACT_ADD']="Add a Contact";
$lang['CONTACT_HOME']="These are the players you have added to your contact list.";
$lang['CONTACT_HOME1']="Username [ID]";
$lang['CONTACT_HOME2']="Message";
$lang['CONTACT_HOME3']="Remove";
$lang['CONTACT_ADD']="Adding a contact to your contacts list.";
$lang['CONTACT_ADD1']="Enter a User ID to add to your contacts list.";
$lang['CONTACT_ADD_BTN']="Add Contact";
$lang['CONTACT_ADD_ERR']="You cannot add the same person twice to your contact list.";
$lang['CONTACT_ADD_ERR1']="You cannot add yourself to your own contact list.";
$lang['CONTACT_ADD_ERR2']="You cannot add a non-existent user to your contact list.";
$lang['CONTACT_ADD_SUCC']="You have successfully added a user to your contact list.";
$lang['CONTACT_REMOVE_ERR']="You didn't specify a contact to delete.";
$lang['CONTACT_REMOVE_ERR1']="You cannot delete a contact who isn't on your list.";
$lang['CONTACT_REMOVE_SUCC']="You have successfully removed a contact from your contacts list.";

//Language menu
$lang['LANG_INTRO']='Here you may change your language. This is not saved to your account. This is saved via a cookie. If you change devices or wipe your cookies, you will need to reset your language again. Translations may not be 100% accurate.';
$lang['LANG_BUTTON']='Change Language';
$lang['LANG_UPDATE']='You specified a language that is not valid.';
$lang['LANG_UPDATE2']='You have successfully updated your language!';

//Notifications page
$lang['NOTIF_TABLE_HEADER1']='Notifications Info';
$lang['NOTIF_TABLE_HEADER2']='Notifications Text';
$lang['NOTIF_DELETE_SINGLE']='You have successfully deleted a notification.';
$lang['NOTIF_DELETE_SINGLE_FAIL']='You cannot delete this notification as it either does not exist or does not belong to you.';
$lang['NOTIF_TITLE']='Last fifteen notifications belonging to you...';
$lang['NOTIF_READ']='Read';
$lang['NOTIF_UNREAD']='Unread';
$lang['NOTIF_DELETE']='Delete';

//Bank
$lang['BANK_BUY1']='Open a bank account today, just ';
$lang['BANK_BUYYES']='Sign Me Up!';
$lang['BANK_SUCCESS']="Congratulations, you bought a bank account for";
$lang['BANK_SUCCESS1']='Start Using My Account!';
$lang['BANK_FAIL']="You do not have enough {$lang['INDEX_PRIMCURR']} to buy a bank account. Come back later when you have enough. You need ";
$lang['BANK_HOME']="You currently have ";
$lang['BANK_HOME1']=" in the bank.";
$lang['BANK_HOME2']="At the end of each day, your bank balance will increase by 2%.<br />You must be active within the past 24 hours to receive interest.";
$lang['BANK_DEPOSIT_WARNING']="It will cost you";
$lang['BANK_DEPOSITE_WARNING1']=" of the money you deposit, rounded up. The maximum fee is ";
$lang['BANK_AMOUNT']="Amount:";
$lang['BANK_DEPOSIT']="Deposit";
$lang['BANK_WITHDRAW_WARNING']="Luckily for you, there's no fee on withdrawals.";
$lang['BANK_WITHDRAW']="Withdraw";
$lang['BANK_D_ERROR']="You are trying to deposit money you do not even have!";
$lang['BANK_D_SUCCESS']="You hand over ";
$lang['BANK_D_SUCCESS1']=" to be deposited. After the fee (";
$lang['BANK_D_SUCCESS2']=") is taken, ";
$lang['BANK_D_SUCCESS3']=" is added to your bank account. <b>You now have ";
$lang['BANK_D_SUCCESS4']=" in your account.</b>";
$lang['BANK_W_FAIL']="You are trying to withdraw more {$lang['INDEX_PRIMCURR']} than you currently have in the bank.";
$lang['BANK_W_SUCCESS']="You successfully withdrew";
$lang['BANK_W_SUCCESS1']="from your bank account. You have";
$lang['BANK_W_SUCCESS2']="left in your bank account.";

//Forums
$lang['FORUM_EMPTY_REPLY']="You are trying to submit an empty reply, which you cannot do! Please make sure you filled in the reply form!";
$lang['FORUM_TOPIC_DNE_TITLE']="Non-existent Topic!";
$lang['FORUM_TOPIC_DNE_TEXT']="You are attempting to interact with a topic that does not exist. Check your source and try again.";
$lang['FORUM_FORUM_DNE_TITLE']="Non-existent Forum!";
$lang['FORUM_FORUM_DNE_TEXT']="You are attempting to interact with a forum that does not exist. Check your source and try again.";
$lang['FORUM_POST_DNE_TITLE']="Non-existent Post!";
$lang['FORUM_POST_DNE_TEXT']="You are attempting to interact with a post that does not exist. Check your source and try again.";
$lang['FORUM_NOPERMISSION']="You are attempting to interact with a forum you have no permission to interact with. If this is an error, please alert an admin right away!";
$lang['FORUM_FORUMS']="Forums";
$lang['FORUM_ON']="On";
$lang['FORUM_IN']="In:";
$lang['FORUM_BY']="By:";
$lang['FORUM_STAFFONLY']="Staff-Only";
$lang['FORUM_F_LP']="Latest Post";
$lang['FORUM_F_TC']="Topic Count";
$lang['FORUM_F_PC']="Post Count";
$lang['FORUM_F_FN']="Forum Name";
$lang['FORUM_FORUMSHOME']="Forums Home";
$lang['FORUM_TOPICNAME']="Topic Name";
$lang['FORUM_TOPICOPEN']="Topic Opened";
$lang['FORUM_TOPIC_MOVE']="Move Topic";
$lang['FORUM_PAGES']="Pages:";
$lang['FORUM_TOPIC_MTT']="Move Topic To:";
$lang['FORUM_TOPIC_PIN']="Pin/Unpin Topic";
$lang['FORUM_TOPIC_LOCK']="Lock/Unlock Topic";
$lang['FORUM_TOPIC_DELETE']="Delete Topic";
$lang['FORUM_POST_EDIT']="Edit";
$lang['FORUM_POST_QUOTE']="Quote";
$lang['FORUM_POST_DELETE']="Delete";
$lang['FORUM_POST_WARN']="Warn";
$lang['FORUM_POST_BAN']="Forum Ban";
$lang['FORUM_POST_EDIT_1']="This post was last edited by";
$lang['FORUM_NOSIG']="No Signature";
$lang['FORUM_POST_POSTED']="Posted";
$lang['FORUM_POST_POST']='Post';
$lang['FORUM_POST_REPLY']='Post Reply';
$lang['FORUM_POST_REPLY2']='Post Reply to Topic';
$lang['FORUM_POST_REPLY_INFO']='Enter your reply here. Remember, you can use BBCode! Please make sure you will not break any game rules when posting.';
$lang['FORUM_POST_TIL']='This topic is locked, and because of this, you cannot post a reply to this topic.';
$lang['FORUM_MAX_CHAR_REPLY']="When posting in the forum, your post may only contain 65,535 characters at maximum. Go back and try again!";
$lang['FORUM_REPLY_SUCCESS']="You have successfully posted your reply to this topic.";
$lang['FORUM_TOPIC_FORM_TITLE']="Topic Name";
$lang['FORUM_TOPIC_FORM_DESC']="Topic Description";
$lang['FORUM_TOPIC_FORM_TEXT']="Topic Text";
$lang['FORUM_TOPIC_FORM_BUTTON']="Post Topic";
$lang['FORUM_TOPIC_FORM_TITLE_LENGTH']="Topic names and descriptions can only be 255 characters in length, at maximum.";
$lang['FORUM_TOPIC_FORM_PAGE']="New Topic Form";
$lang['FORUM_TOPIC_FORM_SUCCESS']="You have successfully posted a new topic in the forums!";
$lang['FORUM_QUOTE_FORM_PAGENAME']="Quoting a Post";
$lang['FORUM_QUOTE_FORM_INFO']="Quoting a post...";
$lang['FORUM_EDIT_FORM_INFO']="Editing a post...";
$lang['FORUM_EDIT_FORM_PAGENAME']="Editing a Post";
$lang['FORUM_EDIT_NOPERMISSION']="You have no permission to edit this post. If you believe this to be wrong, please let an admin know ASAP!";
$lang['FORUM_EDIT_FORM_SUBMIT']="Edit Post";
$lang['FORUM_EDIT_SUCCESS']="You have successfully edited a post!";
$lang['FORUM_MOVE_TOPIC_DFDNE']="You are trying to move a topic to a forum that does not exist. Go back and try again, please.";
$lang['FORUM_MOVE_TOPIC_DONE']="You have successfully moved the topic.";
$lang['FORUM_UNLOCK_DONE']="The topic has been successfully unlocked.";
$lang['FORUM_LOCK_DONE']="The topic has been successfully locked.";
$lang['FORUM_UNPIN_DONE']="The topic has been successfully unpinned.";
$lang['FORUM_PIN_DONE']="The topic has been successfully pinned.";
$lang['FORUM_DELETE_DONE']="Post deleted successfully.";
$lang['FORUM_DELETE_TOPIC_DONE']="Topic has been deleted successfully.";
$lang['FORUM_BAN_INFO']="You have been banned from viewing the forums for the next";
$lang['FORUM_BAN_INFO1']="for the following reason:";
$lang['FORUM_BAN_INFO2']="You may appeal your case by talking with a staff member.";
$lang['FORUM_RECACHE_TOPIC']="Reaching Forum Thread ID #";
$lang['FORUM_RECACHE_FORUM']="Reaching Forum ID #";
$lang['FORUM_RECACHE_DONE']="Recached successfully.";

//Send Cash Form
$lang['SCF_POSCASH']="You need to send at least 1 {$lang['INDEX_PRIMCURR']} to use this form.";
$lang['SCF_UNE']="You cannot send {$lang['INDEX_PRIMCURR']} to a non-existent user!";
$lang['SCF_NEC']="You are trying to send more {$lang['INDEX_PRIMCURR']} than you currently have!";
$lang['SCF_SUCCESS']="{$lang['INDEX_PRIMCURR']} sent successfully.";
$lang['SCF_ERR']="You cannot send money to yourself, sorry.";

//Profile
$lang['PROFILE_UNF']="We could not find a user with the User ID you entered. You could be receiving this message because the player you are trying to view got deleted. Check your source again!";
$lang['PROFILE_PROFOR']="Profile For";
$lang['PROFILE_LOCATION']="Location:";
$lang['PROFILE_GUILD']="Guild";
$lang['PROFILE_PI']="Physical Information";
$lang['PROFILE_ACTION']="Actions";
$lang['PROFILE_FINANCIAL']="Financial Information";
$lang['PROFILE_STAFF']="Staff Area";
$lang['PROFILE_REGISTERED']="Registered";
$lang['PROFILE_ACTIVE']="Last Active";
$lang['PROFILE_LOGIN']="Last Login";
$lang['PROFILE_AGE']="Age";
$lang['PROFILE_DAYS_OLD']="old.";
$lang['PROFILE_REF']="Referrals";
$lang['PROFILE_FRI']="Friends";
$lang['PROFILE_ENE']="Enemies";
$lang['PROFILE_ATTACK']="Attack";
$lang['PROFILE_SPY']="Spy On";
$lang['PROFILE_POKE']="Poke";
$lang['PROFILE_CONTACT']="Add";
$lang['PROFILE_CONTACT1']="To Contacts List";
$lang['PROFILE_MSG1']="Sending";
$lang['PROFILE_MSG2']="a message";
$lang['PROFILE_MSG3']="Recipient:";
$lang['PROFILE_MSG4']="Message:";
$lang['PROFILE_MSG5']="Close Window";
$lang['PROFILE_MSG6']="Send Message";
$lang['PROFILE_CASH']="Send Cash";
$lang['PROFILE_STAFF_DATA']="Data";
$lang['PROFILE_STAFF_LOC']="Location";
$lang['PROFILE_STAFF_LH']="Last Hit";
$lang['PROFILE_STAFF_LL']="Last Login";
$lang['PROFILE_STAFF_REGIP']="Sign Up";
$lang['PROFILE_STAFF_THRT']="Threat?";
$lang['PROFILE_STAFF_RISK']="Risk Level";
$lang['PROFILE_STAFF_OS']="Browser/OS";
$lang['PROFILE_STAFF_LA']="Last Action";
$lang['PROFILE_STAFF_NOTES']="Staff Notes:";
$lang['PROFILE_STAFF_BTN']="Update Notes About";
$lang['PROFILE_BTN_MSG']="Send";
$lang['PROFILE_BTN_MSG1']="A Message";
$lang['PROFILE_BTN_SND']="Send";
$lang['PROFILE_RISK_1']="No Risk";
$lang['PROFILE_RISK_2']="Spam";
$lang['PROFILE_RISK_3']="Open Public Proxy";
$lang['PROFILE_RISK_4']="Tor Node";
$lang['PROFILE_RISK_5']="Honeypot / Botnet / DDOS Attack";

//Equip Items
$lang['EQUIP_NOITEM']="Item cannot be found, and as a result, you cannot equip it.";
$lang['EQUIP_NOITEM_TITLE']="Item does not exist!";
$lang['EQUIP_NOTWEAPON']="The item you are trying to equip cannot be equipped as a weapon.";
$lang['EQUIP_NOTWEAPON_TITLE']="Invalid Weapon!";
$lang['EQUIP_NOSLOT']="You are trying to equip this item to an invalid or non-existent slot.";
$lang['EQUIP_NOSLOT_TITLE']="Invalid Equipment Slot!";
$lang['EQUIP_WEAPON_SUCCESS1']="You have successfully equipped";
$lang['EQUIP_WEAPON_SUCCESS2']="as your";
$lang['EQUIP_WEAPON_SLOT1']='Primary Weapon';
$lang['EQUIP_WEAPON_SLOT2']='Secondary Weapon';
$lang['EQUIP_WEAPON_SLOT3']='Armor';
$lang['EQUIP_WEAPON_TITLE']="Equip a Weapon";
$lang['EQUIP_WEAPON_TEXT_FORM_1']="Please select the spot you wish to equip your";
$lang['EQUIP_WEAPON_TEXT_FORM_2']="to. If you're already holding a weapon in the slot you choose, it will be moved back to your inventory.";
$lang['EQUIP_WEAPON_EQUIPAS']="Equip As";
$lang['EQUIP_ARMOR_TITLE']="Equipping Armor";
$lang['EQUIP_ARMOR_TEXT_FORM_1']="You're attempting to equip your ";
$lang['EQUIP_ARMOR_TEXT_FORM_2']="to your armor slot. If you're already wearing armor, it will be moved back to your inventory.";
$lang['EQUIP_NOTARMOR']="The item you are trying to equip cannot be equipped as armor.";
$lang['EQUIP_NOTARMOR_TITLE']="Invalid Armor!";
$lang['EQUIP_OFF_ERROR1']="You are trying to unequip an item from a nonexistent slot.";
$lang['EQUIP_OFF_ERROR2']="You don't have an item in that slot.";
$lang['EQUIP_OFF_SUCCESS']="You've successfully unequipped the item from your";
$lang['EQUIP_OFF_SUCCESS1']="slot.";

//Polling Staff
$lang['STAFF_POLL_TITLE']="Polling Administration";
$lang['STAFF_POLL_TITLES']="Start a Poll";
$lang['STAFF_POLL_TITLEE']="End a Poll";
$lang['STAFF_POLL_START_INFO']="Ask a question, then give some possible responses.";
$lang['STAFF_POLL_START_CHOICE']="Choice #";
$lang['STAFF_POLL_START_QUESTION']="Question";
$lang['STAFF_POLL_START_HIDE']="Hide results until the end of the poll?";
$lang['STAFF_POLL_START_BUTTON']="Create Poll";
$lang['STAFF_POLL_START_ERROR']="You need to have a question, and at least two answers!";
$lang['STAFF_POLL_START_SUCCESS']="You have successfully opened a poll to the game.";
$lang['STAFF_POLL_END_SUCCESS']="You have successfully closed an active poll.";
$lang['STAFF_POLL_END_FORM']="Please select the poll you wish to close.";
$lang['STAFF_POLL_END_BTN']="Close Selected Poll";
$lang['STAFF_POLL_END_ERR']="You're attempting to close a non-existent poll.";

//Polling
$lang['POLL_TITLE']="Polling Booth";
$lang['POLL_CYV']="Cast your vote today!";
$lang['POLL_VOP']="View Previously Opened Polls";
$lang['POLL_AVITP']="You can only vote once per poll.";
$lang['POLL_PCNT']="You can't vote in a poll that does not exist, or has been previously closed.";
$lang['POLL_VOTE_SUCCESS']="You have successfully casted your vote in this poll.";
$lang['POLL_VOTE_NOPOLL']="There's no polls opened at this time. Come back later.";
$lang['POLL_VOTE_CHOICE']="Choice";
$lang['POLL_VOTE_VOTES']="Votes";
$lang['POLL_VOTE_PERCENT_VOTES']="Percentage";
$lang['POLL_VOTE_AV']="(Already Voted!)";
$lang['POLL_VOTE_NV']="(Not Voted!)";
$lang['POLL_VOTE_HIDDEN']="The results of this poll are hidden until its end.";
$lang['POLL_VOTE_QUESTION']="Question:";
$lang['POLL_VOTE_YVOTE']="Your Vote:";
$lang['POLL_VOTE_TVOTE']="Total Votes:";
$lang['POLL_VOTE_VOTEC']="Choose";
$lang['POLL_VOTE_CAST']="Cast Vote";
$lang['POLL_VOTE_NOCLOSED']="There are no closed polls at this moment. Come back later when the staff close a poll.";

//Forum Staff
$lang['STAFF_FORUM_ADD']="Add Forum Category";
$lang['STAFF_FORUM_EDIT']="Edit Forum Category";
$lang['STAFF_FORUM_DEL']="Delete Forum Category";
$lang['STAFF_FORUM_ADD_NAME']="Forum Name";
$lang['STAFF_FORUM_ADD_DESC']="Forum Description";
$lang['STAFF_FORUM_ADD_AUTHORIZE']="Authorization";
$lang['STAFF_FORUM_ADD_AUTHORIZEP']="Public";
$lang['STAFF_FORUM_ADD_AUTHORIZES']="Staff-Only";
$lang['STAFF_FORUM_ADD_BTN']="Create Forum";
$lang['STAFF_FORUM_ADD_ERRNAME']="The forum name input was either invalid or empty. Please recheck and try again.";
$lang['STAFF_FORUM_ADD_ERRDESC']="The forum description input was either invalid or empty. Please recheck and try again.";
$lang['STAFF_FORUM_ADD_ERRNIU']="The forum name you chose is already in use. Please try again with a new name.";
$lang['STAFF_FORUM_ADD_SUCCESS']="You have successfully added a forum category to the game.";
$lang['STAFF_FORUM_EDIT_ERRINV']="You specified an invalid forum ID. Try again.";
$lang['STAFF_FORUM_EDIT_BTN']="Edit Forum";
$lang['STAFF_FORUM_EDIT_ERREMPTY']="One or more inputs on the previous page is empty. Please fill the form and try again.";
$lang['STAFF_FORUM_EDIT_SUCCESS']="You have successfully edited the forum.";
$lang['STAFF_FORUM_DEL_BTN']="Delete Forum";
$lang['STAFF_FORUM_DEL_INFO']="Deleting forums are permanent. This will also remove the posts inside them as well.";
$lang['STAFF_FORUM_EDIT_ERRFDNE']="The forum you chose to delete does not exist. Go back and verify and try again.";
$lang['STAFF_FORUM_DEL_SUCCESS']="Successfully deleted the forum, along with whatever topics and posts were in them previously.";

//Item Use
$lang['IU_UI']="You are trying to use an unspecified item. Check your link and try again!";
$lang['IU_UNUSED_ITEM']="This item isn't configured to be used. You cannot use items with a configured use.";
$lang['IU_ITEM_NOEXIST']="The item you are trying to use does not exist. Check your sources and try again.";
$lang['IU_SUCCESS']="has been used successfully. Refresh for the changes to take effect.";

//Staff items
$lang['STAFF_ITEM_GIVE_TITLE']="Giving Item to User";
$lang['STAFF_ITEM_GIVE_FORM_USER']="User";
$lang['STAFF_ITEM_GIVE_FORM_ITEM']="Item";
$lang['STAFF_ITEM_GIVE_FORM_QTY']="Quantity";
$lang['STAFF_ITEM_GIVE_FORM_BTN']="Give Item";
$lang['STAFF_ITEM_GIVE_SUB_NOITEM']="You didn't specify the item you wish to give to the user.";
$lang['STAFF_ITEM_GIVE_SUB_NOQTY']="You didn't specify the amount of the item you wish to give to the user.";
$lang['STAFF_ITEM_GIVE_SUB_NOUSER']="You didn't specify the user you wish to give an item to.";
$lang['STAFF_ITEM_GIVE_SUB_ITEMDNE']="The item you are trying to give away does not exist.";
$lang['STAFF_ITEM_GIVE_SUB_USERDNE']="The user you are trying to give an item to does not exist.";
$lang['STAFF_ITEM_GIVE_SUB_SUCCESS']="Item(s) have been gifted successfully.";

//Staff Create Items
$lang['STAFF_CITEM_TH1']="Item Name";
$lang['STAFF_CITEM_TH2']="Item Info";
$lang['STAFF_CITEM_TH3']="Item Type";
$lang['STAFF_CITEM_TH4']="Item Purchasable?";
$lang['STAFF_CITEM_TH5']="Item Buy Price";
$lang['STAFF_CITEM_TH6']="Item Sell Price";
$lang['STAFF_CITEM_TH7']="Item Usage";
$lang['STAFF_CITEM_TH8']="Item Effect #";
$lang['STAFF_CITEM_TH9']="Enable Usage";
$lang['STAFF_CITEM_TH10']="True";
$lang['STAFF_CITEM_TH11']="False";
$lang['STAFF_CITEM_TH12']="Stat";
$lang['STAFF_CITEM_TH12_1']="Infirmary Time";
$lang['STAFF_CITEM_TH12_2']="Dungeon Time";
$lang['STAFF_CITEM_TH13']="Direction";
$lang['STAFF_CITEM_TH14']="Amount";
$lang['STAFF_CITEM_TH13_1']="Increase/Add";
$lang['STAFF_CITEM_TH13_2']="Decrease/Remove";
$lang['STAFF_CITEM_TH14_1']="Value";
$lang['STAFF_CITEM_TH14_2']="Percent";
$lang['STAFF_CITEM_TH15']="Combat Stats";
$lang['STAFF_CITEM_TH16']="Weapon Strength";
$lang['STAFF_CITEM_TH17']="Armor Defense";
$lang['STAFF_CITEM_BTN']="Create Item";
$lang['STAFF_CITEM_ERR']="You do not have permission to create an item. You need to be an admin.";
$lang['STAFF_CITEM_ERR1']="You are missing one, or more, required fields. Go back and correct this.";
$lang['STAFF_CITEM_ERR2']="You cannot create an item named after an existing item.";
$lang['STAFF_CITEM_ERR3']="The item group you specified is invalid or does not exist.";
$lang['STAFF_CITEM_ERR4']="You do not have permission to create an item. You need to be an admin.";
$lang['STAFF_CITEM_SUCC']="You have successfully created an item called ";

//Staff Create Item Group
$lang['STAFF_CITG_ERR']="You do not have permission to be here.";
$lang['STAFF_CITG_FRM']="Adding an item group to the game.";
$lang['STAFF_CITG_FRM1']="Item Group Name";
$lang['STAFF_CITG_BTN']="Create Item Group";
$lang['STAFF_CITG_ERR1']="The item group name you input is empty or invalid.";
$lang['STAFF_CITG_ERR2']="The item group name you input already exists. You cannot have two item groups with the same name.";
$lang['STAFF_CITG_SUCC']="You have successfully created an item group called";

//Staff Edit Items
$lang['STAFF_EITEM_P1_START']="Select the item you wish to edit, then click the button.";
$lang['STAFF_EITEM_P1_SELECT']="Item";
$lang['STAFF_EITEM_P1_BTN']="Edit Item";
$lang['STAFF_EITEM_P2_EMPTY']="Please select an item to edit from the previous form before continuing.";
$lang['STAFF_EITEM_P2_NO']="The item you chose does not exist. Check your source and try again.";
$lang['STAFF_EITEM_BTN']="Edit Item";
$lang['STAFF_EITEM_SUC']="You ahve successfully edited this item.";

//Staff Crimes
$lang['STAFF_CRIME_TITLE']="Crimes";
$lang['STAFF_CRIME_MENU_CREATE']="Create Crime";
$lang['STAFF_CRIME_MENU_EDIT']="Edit Crime";
$lang['STAFF_CRIME_MENU_DEL']="Delete Crime";
$lang['STAFF_CRIME_MENU_CREATECG']="Create Crime Group";
$lang['STAFF_CRIME_MENU_EDITCG']="Edit Crime Group";
$lang['STAFF_CRIME_MENU_DELCG']="Delete Crime Group";
$lang['STAFF_CRIME_NEW_TITLE']="Adding a new crime.";
$lang['STAFF_CRIME_NEW_NAME']="Crime Name";
$lang['STAFF_CRIME_NEW_BRAVECOST']="Bravery Cost";
$lang['STAFF_CRIME_NEW_SUCFOR']="Success Formula";
$lang['STAFF_CRIME_NEW_SUCPRIMIN']="Success Minimum {$lang['INDEX_PRIMCURR']}";
$lang['STAFF_CRIME_NEW_SUCPRIMAX']="Success Maximum {$lang['INDEX_PRIMCURR']}";
$lang['STAFF_CRIME_NEW_SUCSECMIN']="Success Minimum {$lang['INDEX_SECCURR']}";
$lang['STAFF_CRIME_NEW_SUCSECMAX']="Success Maximum {$lang['INDEX_SECCURR']}";
$lang['STAFF_CRIME_NEW_SUCITEM']="Success Item";
$lang['STAFF_CRIME_NEW_GROUP']="Crime Group";
$lang['STAFF_CRIME_NEW_ITEXT']="Initial Text";
$lang['STAFF_CRIME_NEW_ITEXT_PH']="The text that is shown on starting the crime.";
$lang['STAFF_CRIME_NEW_STEXT']="Success Text";
$lang['STAFF_CRIME_NEW_STEXT_PH']="The text that is shown if the player succeeds at committing the crime.";
$lang['STAFF_CRIME_NEW_JTEXT']="Failure Text";
$lang['STAFF_CRIME_NEW_JTEXT_PH']="The text that is shown if the player fails the crime.";
$lang['STAFF_CRIME_NEW_JTIMEMIN']="Minimum Dungeon Time";
$lang['STAFF_CRIME_NEW_JTIMEMAX']="Maximum Dungeon Time";
$lang['STAFF_CRIME_NEW_JREASON']="Dungeon Reason";
$lang['STAFF_CRIME_NEW_XP']="Success {$lang['GEN_EXP']}";
$lang['STAFF_CRIME_NEW_BTN']="Create Crime";
$lang['STAFF_CRIME_NEW_FAIL1']="You are missing one of the required inputs from the previous form.";
$lang['STAFF_CRIME_NEW_FAIL2']="The item you chose does not appear to exist in-game. Please select a new item.";
$lang['STAFF_CRIME_NEW_SUCCESS']="You have successfully added a crime to the game.";
$lang['STAFF_CRIMEG_NEW_TITLE']="Adding a new Crime Group.";
$lang['STAFF_CRIMEG_NEW_NAME']="Crime Group Name";
$lang['STAFF_CRIMEG_NEW_ORDER']="Crime Group Order";
$lang['STAFF_CRIMEG_NEW_BTN']="Create Crime Group";
$lang['STAFF_CRIMEG_NEW_FAIL1']="At least one of the two inputs on the previous form are empty. Go back and correct that, please.";
$lang['STAFF_CRIMEG_NEW_FAIL2']="You cannot have crime groups share order values.";
$lang['STAFF_CRIMEG_NEW_SUCCESS']="You have successfully created a crime group.";
$lang['STAFF_CRIME_EDIT_START']="Select a crime from the dropdown to edit.";
$lang['STAFF_CRIME_EDIT_START1']="Crime";
$lang['STAFF_CRIME_EDIT_START_BTN']="Edit Crime";
$lang['STAFF_CRIME_EDIT_FRM_ERR']="You must specify a crime to edit.";
$lang['STAFF_CRIME_EDIT_FRM_ERR1']="The crime you've selected doesn't appear to exist.";
$lang['STAFF_CRIME_EDIT_SUCCESS']="You have successfully edited this crime.";
$lang['STAFF_CRIME_DEL_FRM']="Select a crime from the dropdown to delete.";
$lang['STAFF_CRIME_DEL_FRM1']="Crime";
$lang['STAFF_CRIME_DEL_BTN']="Delete Crime";
$lang['STAFF_CRIME_DEL_ERR']="You need to select a crime you wish to delete.";
$lang['STAFF_CRIME_DEL_ERR1']="The crime you wish to delete doesn't exist. Success?";
$lang['STAFF_CRIME_DEL_SUCCESS']="You have successfully deleted this crime.";
$lang['STAFF_CRIMEG_EDIT_START']="Select a crime group to edit from the dropdown.";
$lang['STAFF_CRIMEG_EDIT_START1']="Crime Group";
$lang['STAFF_CRIMEG_EDIT_START_BTN']="Edit Crime Group";
$lang['STAFF_CRIMEG_EDIT_FRM_ERR']="You need to specify a crime group to edit.";
$lang['STAFF_CRIMEG_EDIT_FRM_ERR1']="The crime group you're trying to edit doesn't exist.";
$lang['STAFF_CRIMEG_EDIT_SUB_ERR']="One or more required inputs are empty.";
$lang['STAFF_CRIMEG_EDIT_SUB_SUCC']="Crime group has been edited successfully.";
$lang['STAFF_CRIMEG_DEL_FRM']="Select a crime group from the dropdown to delete.";
$lang['STAFF_CRIMEG_DEL_BTN']="Delete Crime Group";
$lang['STAFF_CRIMEG_DEL_ERR']="You need to select a crime group to delete.";
$lang['STAFF_CRIMEG_DEL_ERR1']="The crime group you wish to delete doesn't exist. Success?";
$lang['STAFF_CRIMEG_DEL_SUCCESS']="You have successfully deleted this crime group.";

//Staff Users
$lang['STAFF_USERS_EDIT_START']="When you submit this form, you will be able to edit any aspect of the player you select.";
$lang['STAFF_USERS_EDIT_USER']="User:";
$lang['STAFF_USERS_EDIT_ELSE']="Or, you can manually type in a User's ID.";
$lang['STAFF_USERS_EDIT_EMPTY']="You inputted an invalid user. Go back and try again.";
$lang['STAFF_USERS_EDIT_DND']="The user you input does not exist.";
$lang['STAFF_USERS_EDIT_BTN']="Edit User";
$lang['STAFF_USERS_DEL_BTN']="Delete User";
$lang['STAFF_USERS_EDIT_FORMTITLE']="Editing User";
$lang['STAFF_USERS_EDIT_FORM_INFIRM']="Infirmary Time";
$lang['STAFF_USERS_EDIT_FORM_INFIRM_REAS']="Infirmary Reason";
$lang['STAFF_USERS_EDIT_FORM_DUNG']="Dungeon Time";
$lang['STAFF_USERS_EDIT_FORM_DUNG_REAS']="Dungeon Reason";
$lang['STAFF_USERS_EDIT_FORM_ESTATE']="Estate";
$lang['STAFF_USERS_EDIT_FORM_STATS']="User Stats";
$lang['STAFF_USERS_EDIT_SUB_MISSINGSTUFF']="You are missing some required information from the previous page. Go back and try again, please.";
$lang['STAFF_USERS_EDIT_SUB_ULBAD']="You specified an invalid user level. Go back and try again.";
$lang['STAFF_USERS_EDIT_SUB_UNIU']="The specified username is already in use. Go back and specify a new one.";
$lang['STAFF_USERS_EDIT_SUB_HBAD']="The house you specified is invalid or non-existent. Go back and try again.";
$lang['STAFF_USERS_EDIT_SUB_EIU']="The email input is already in use by another account. Go back and input an unusued and valid email address.";
$lang['STAFF_USERS_EDIT_SUB_SUCCESS']="User's information has been updated successfully.";
$lang['STAFF_USERS_EDIT_SUB_WDNE']="One of the weapons you specified does not exist, or cannot be equipped as a weapon. Go back and try again.";
$lang['STAFF_USERS_EDIT_SUB_ADNE']="The armor you specified does not exist, or cannot be equipped as armor. Go back and try again.";
$lang['STAFF_USERS_EDIT_SUB_TDNE']="The town you chose does not exist. Go back and try again.";
$lang['STAFF_USERS_DEL_FORM_1']="You may use this form to delete a user from the game. This action is not reversible. Be 100% sure.";
$lang['STAFF_USERS_DEL_SUB_SECERROR']="You specified an invalid or non-existent user. Go back and try again.";
$lang['STAFF_USERS_DEL_SUBFORM_CONFIRM']="Please confirm that you wish to delete";
$lang['STAFF_USERS_DEL_SUBFORM_CONFIRM1']=". Once deleted, they will not be able to login from their account anymore.";
$lang['STAFF_USERS_DEL_SUB_INVALID']="You specified a user or command that's invalid.";
$lang['STAFF_USERS_DEL_SUB_FAIL']="User was not deleted.";
$lang['STAFF_USERS_DEL_SUB_SUCC']="User was deleted from the game.";
$lang['STAFF_USERS_FL_SUB_SUCC']="User was successfully logged out from the game.";
$lang['STAFF_USERS_FL_FORM_INFO']="Use this form to have a use auto-logged out when on their next action in game.";
$lang['STAFF_USERS_FL_FORM_BTN']="Force Logout User";

//Staff Academy
$lang['STAFF_ACADEMY_ADD_TH']="Use this form to add an academy course to the game.";
$lang['STAFF_ACADEMY_ADD']="Create Course";
$lang['STAFF_ACADEMY_DEL']="Remove Course";
$lang['STAFF_ACADEMY_NAME']="Academy Name";
$lang['STAFF_ACADEMY_DESC']="Academy Description";
$lang['STAFF_ACADEMY_COST']="Academy Cost";
$lang['STAFF_ACADEMY_LVL']="Academy Minimum Level";
$lang['STAFF_ACADEMY_DAYS']="Academy Days";
$lang['STAFF_ACADEMY_OPTION_1']="Academy Strength";
$lang['STAFF_ACADEMY_OPTION_2']="Academy Agility";
$lang['STAFF_ACADEMY_OPTION_3']="Academy Guard";
$lang['STAFF_ACADEMY_OPTION_4']="Academy Labor";
$lang['STAFF_ACADEMY_OPTION_5']="Academy IQ";
$lang['STAFF_ACADEMY_CREATE']="Create Academy";
$lang['STAFF_ACADEMY_DELETE_HEADER']="Deleting an Academy";
$lang['STAFF_ACADEMY_DELETE_NOTICE']="The academy you select will be deleted permanently. There isn't a confirmation prompt, so be 100% sure.";
$lang['STAFF_ACADEMY_DELETE_TITLE']="Academy";
$lang['STAFF_ACADEMY_DELETE_BUTTON']="Remove Academy";
$lang['STAFF_ACADEMY_ADD_ERR']="You are missing one or more required inputs.";
$lang['STAFF_ACADEMY_ADD_ERR1']="You must specify a stat that gets increased by completing this course.";
$lang['STAFF_ACADEMY_ADD_ERR2']="The course name is already in use.";
$lang['STAFF_ACADEMY_ADD_SUCC']="You have successfully created an academy course.";
$lang['STAFF_NOPERM']="You do not have permission to use this page. If this is false, please contact an admin.";
$lang['STAFF_ACADEMY_DEL_ERR']="You did not specify an academy course to delete.";
$lang['STAFF_ACADEMY_DEL_ERR1']="That course does not exist, or was already deleted.";
$lang['STAFF_ACADEMY_DEL_SUCC']="Successfully deleted the";
$lang['STAFF_ACADEMY_DEL_SUCC1']="course from the game.";

//Academy
$lang['ACA_LVL']="Required Level: ";
$lang['ACA_NAME']="Academy";
$lang['ACA_ALRDYDO']="You are currently studying the";
$lang['ACA_ALRDYDO1']="lesson. You will be completed in";
$lang['ACA_ACA']="Course";
$lang['ACA_DESC']="Description";
$lang['ACA_COST']="Cost";
$lang['ACA_LINK']="Action";
$lang['ACA_DONE']="Graduated";
$lang['ACA_ATTEND']="Attend Course";
$lang['ACA_ERR']="Please specify a course you wish to attend.";
$lang['ACA_ERR1']="You are trying to attend a non-existent course.";
$lang['ACA_ERR2']="Your level is too low to attend this course.";
$lang['ACA_ERR3']="You do not have enough {$lang['INDEX_PRIMCURR']} to attend this course.";
$lang['ACA_ERR4']="You cannot take the same course twice.";
$lang['ACA_SUCC']="You have enrolled in the ";
$lang['ACA_SUCC1']="course. You will graduate in";
$lang['ACA_SUCC2']="days.";

//Criminal Center
$lang['CRIME_TITLE']="Criminal Center";
$lang['CRIME_ERROR_JI']="Only the healthy and free individuals can commit crimes.";
$lang['CRIME_TABLE_CRIME']="Crime";
$lang['CRIME_TABLE_CRIMES']="Crimes";
$lang['CRIME_TABLE_COST']="Cost";
$lang['CRIME_TABLE_COMMIT']="Commit";
$lang['CRIME_NOREFRESH']="Please do not refresh while commiting crimes.";
$lang['CRIME_COMMIT_INVALID']="You are trying to commit either a non-existent crime, or an unfinished one. Try again, and if the issue persists, please contact an admin.";
$lang['CRIME_COMMIT_BRAVEBAD']="You aren't brave enough to commit this crime at this time. Come back later.";

$lang['ATTACK_START_NOREFRESH']="Refreshing while attacking is a federal jail offense. You can lose all your {$lang['GEN_EXP']} for that.";
$lang['ATTACK_START_NOUSER']="You can only attack players specified. Did you use the attack link on the user's profile?";
$lang['ATTACK_START_NOTYOU']="Depressed or not, you cannot attack yourself!";
$lang['ATTACK_START_THEYLOWLEVEL']="You cannot attack players under level 2, who are also online.";
$lang['ATTACK_START_YOUNOHP']="You need HP to fight someone. Come back when you have more health!";
$lang['ATTACK_START_YOUINFIRM']="How do you expect to fight someone when you're nursing an injury in the infirmary?";
$lang['ATTACK_START_YOUDUNG']="How do you expect to fight someone when you're serving your debt to society in the dungeon?";
$lang['ATTACK_START_YOUCHICKEN']="Chickening out from one fight, and running to start another is not an honorable way to play.";
$lang['ATTACK_START_NONUSER']="The person you have a grudge with does not exist. Check your source and try again.";
$lang['ATTACK_START_UNKNOWNERROR']="An unknown error has occurred. Go back and try again. If this error persists, contact an admin!";
$lang['ATTACK_START_OPPNOHP']=" is low on HP. Come back when they have more health.";
$lang['ATTACK_START_OPPINFIRM']=" is in the infirmary at the moment. Come back when they're out!";
$lang['ATTACK_START_OPPDUNG']=" is in the dungeon at the moment. Come back when they're out!";
$lang['ATTACK_START_OPPUNATTACK']="This user cannot be attacked by normal means.";
$lang['ATTACK_START_YOUUNATTACK']="A magical force prevents you from attacking anyone.";
$lang['ATTACK_FIGHT_STALEMATE']="Come back when you're stronger. This fight ends in stalemate.";
$lang['ATTACK_FIGHT_LOWENG1']="You do not have enough energy for this fight. You need at least";
$lang['ATTACK_FIGHT_LOWENG2']="%. You only have";
$lang['ATTACK_FIGHT_BUGABUSE']="Abusing game bugs is against the game rules. You're losing your {$lang['GEN_EXP']} and going to the infirmary for this one.";
$lang['ATTACK_FIGHT_BADWEAP']="The weapon you're trying to attack with doesn't exist or cannot be used as a weapon.";
$lang['ATTACK_FIGHT_ATTACKY_HIT1']="Using your";
$lang['ATTACK_FIGHT_ATTACKY_HIT2']="you hit";
$lang['ATTACK_FIGHT_ATTACKY_HIT3']="doing";
$lang['ATTACK_FIGHT_ATTACKY_HIT4']="damage.";
$lang['ATTACK_FIGHT_ATTACKY_MISS1']="You tried to hit";
$lang['ATTACK_FIGHT_ATTACKY_MISS2']="but missed.";
$lang['ATTACK_FIGHT_ATTACKY_WIN1']="You have bested";
$lang['ATTACK_FIGHT_ATTACKY_WIN2']="in battle. What do you wish to do with them now?";
$lang['ATTACK_FIGHT_OUTCOME1']="Mug";
$lang['ATTACK_FIGHT_OUTCOME2']="Beat";
$lang['ATTACK_FIGHT_OUTCOME3']="Leave";
$lang['ATTACK_FIGHT_ATTACK_HPREMAIN']="HP Remaining";
$lang['ATTACK_FIGHT_ATTACK_FISTS']="Fists";
$lang['ATTACK_FIGHT_ATTACKO_HIT1']="Using their";
$lang['ATTACK_FIGHT_ATTACKO_HIT2']="hit you doing";
$lang['ATTACK_FIGHT_ATTACKO_MISS']="tried to hit you, but, missed.";
$lang['ATTACK_FIGHT_FINAL_GUILD']="is in the same guild as you! You cannot attack your fellow guild mates!";
$lang['ATTACK_FIGHT_FINAL_CITY']="This player is not in the same town as you. You both need to be in the same town to fight each other.";
$lang['ATTACK_FIGHT_START1']="Choose a weapon to attack with.";
$lang['ATTACK_FIGHT_START2']="You do not have a weapon to attack with! You may want to go back.";
$lang['ATTACK_FIGHT_END']="You have bested";
$lang['ATTACK_FIGHT_END1']="You bested them in combat!";
$lang['ATTACK_FIGHT_END2']="An evil thought comes into your mind as you stare at their unconscious body. You break their neck, and kick them until they start bleeding.";
$lang['ATTACK_FIGHT_END3']="Your actions cause";
$lang['ATTACK_FIGHT_END4']="of infirmary time.";
$lang['ATTACK_FIGHT_END5']="You fell to";
$lang['ATTACK_FIGHT_END6']="You lost this fight and lost some of your {$lang['GEN_EXP']} as a warrior!";
$lang['ATTACK_FIGHT_END7']="Since you are an honorable warrior, you take them to the infirmary entrance. You leave their body there. This increases your {$lang['GEN_EXP']}.";
$lang['ATTACK_FIGHT_END8']="Being a greedy warrior, you take a look at their pockets and grab some of their {$lang['INDEX_PRIMCURR']}.";
$lang['ATTACK_FIGHT_POINT']="You have earned your guild one point.";
$lang['ATTACK_FIGHT_POINTL']="You have earned a point for the enemy guild.";
$lang['ATTACK_BEAT_TEXT']="Beat up by";
$lang['ATTACK_BEAT_NOTIF']="You were beat up by";
$lang['ATTACK_BEAT_NOTIF1']="and put into the {$lang['EXPLORE_INFIRM']} for";
$lang['ATTACK_BEAT_NOTIF3']="minutes.";
$lang['ATTACK_LOST_TEXT']="Picked a fight and lost";
$lang['ATTACK_LOST_NOTIF']="attacked you and lost, gaining you";
$lang['ATTACK_XP_TEXT']="Used for {$lang['GEN_EXP']} by";
$lang['ATTACK_XP_NOTIF']="attacked you and used you for experience.";
$lang['ATTACK_MUG_TEXT']="Robbed by";
$lang['ATTACK_MUG_NOTIF']="robbed you and stole";
$lang['ATTACK_MUG_ITEM']="For successfully robbing ";
$lang['ATTACK_MUG_ITEM1']=", you received 1";

//Item Info Page
$lang['ITEM_INFO_LUIF']="Displaying item information for";
$lang['ITEM_INFO_TYPE']="Type";
$lang['ITEM_INFO_SPRICE']="Sell Price";
$lang['ITEM_INFO_BPRICE_NO']="Item cannot be purchased in-game.";
$lang['ITEM_INFO_SPRICE_NO']="Item cannot be sold in-game.";
$lang['ITEM_INFO_BPRICE']="Buy Price";
$lang['ITEM_INFO_WEAPON_HURT']="Weapon Rating";
$lang['ITEM_INFO_ARMOR_HURT']="Armor Rating";
$lang['ITEM_INFO_INFO']="Info";
$lang['ITEM_INFO_ITEM']="Item";
$lang['ITEM_INFO_EFFECT']="Effect #";
$lang['ITEM_INFO_BY']="by";

//Item sell
$lang['ITEM_SELL_INFO']="Item Selling";
$lang['ITEM_SELL_FORM1']="You are attempting to sell";
$lang['ITEM_SELL_FORM2']="back to the game. Enter how many you wish to sell back. You have";
$lang['ITEM_SELL_FORM3']="to sell.";
$lang['ITEM_SELL_SUCCESS1']="You have successfully sold";
$lang['ITEM_SELL_SUCCESS2']="(s) for";
$lang['ITEM_SELL_BTN']="Sell Items";
$lang['ITEM_SELL_ERROR1_TITLE']="Missing Items!";
$lang['ITEM_SELL_BAD_QTY']="You are attempting to sell more items than you currently have in stock. Check your input and try again!";
$lang['ITEM_SELL_ERROR1']="You are attempting to sell an item that you don't have, or just doesn't exist. Check your source and try again.";

//Staff jobs
$lang['STAFF_JOB_CREATE_TITLE']="Create a Job";
$lang['STAFF_JOB_CREATE_FORM_NAME']="Job Name";
$lang['STAFF_JOB_CREATE_FORM_DESC']="Job Description";
$lang['STAFF_JOB_CREATE_FORM_BOSS']="Job Manager";
$lang['STAFF_JOB_CREATE_FORM_FIRST']="First Job Rank";
$lang['STAFF_JOB_CREATE_FORM_RNAME']="Rank Name";
$lang['STAFF_JOB_CREATE_FORM_PAYS']="Daily Payment";
$lang['STAFF_JOB_CREATE_FORM_ACT']="Required Activity";

//Staff logs
$lang['STAFF_LOGS_USERS_FORM']="Select the user whose logs you wish to view.";
$lang['STAFF_LOGS_USERS_FORM_BTN']="View Logs";

//Shops
$lang['SHOPS_HOME_INTRO']="You being looking though the town and you see a few shops.";
$lang['SHOPS_HOME_OH']="This city sure isn't developed far enough to have shops, eh?";
$lang['SHOPS_HOME_TH_1']="Shop's Name";
$lang['SHOPS_HOME_TH_2']="Shop's Description";
$lang['SHOPS_SHOP_TH_1']="Item Name";
$lang['SHOPS_SHOP_TH_2']="Price";
$lang['SHOPS_SHOP_TH_3']="Buy";
$lang['SHOPS_SHOP_TD_1']="Qty:";
$lang['SHOPS_SHOP_INFO']="You begin browsing the items at";
$lang['SHOPS_BUY_ERROR1']="You are attempting to use this file incorrectly. Be sure you have specified both an item to buy, along with a quantity.";
$lang['SHOPS_BUY_ERROR2']="YThe item you are trying to buy doesn't exist, isn't sold in this shop or just doesn't exist!";
$lang['SHOPS_SHOP_ERROR1']="You are trying to access a shop in a different town than you are currently in!";
$lang['SHOPS_SHOP_ERROR2']="You are trying to access a shop that is invalid or doesn't exist. Check your source and try again!";
$lang['SHOPS_BUY_ERROR3']="You do not have enough {$lang['INDEX_PRIMCURR']} to buy";
$lang['SHOPS_BUY_ERROR4']="The item you are trying to buy isn't purchasable via normal means.";
$lang['SHOPS_BUY_SUCCESS']="You have successfully purchased";
$lang['SHOPS_BUY_ERROR5']="You cannot buy items from shops outside of the city you are currently in. Check your source and try again.";

//Staff shops
$lang['STAFF_SHOP_FORM_TITLE']="Use this form to create a new shop.";
$lang['STAFF_SHOP_FORM_OPTION1']="Shop's Name";
$lang['STAFF_SHOP_FORM_OPTION2']="Shop's Description";
$lang['STAFF_SHOP_FORM_OPTION3']="Shop's Location";
$lang['STAFF_SHOP_FORM_BTN']="Create Shop";
$lang['STAFF_SHOP_SUB_ERROR1']="Shop name or description is empty. Go back and try again.";
$lang['STAFF_SHOP_SUB_ERROR2']="The location you chose for the shop to stay does not exist.";
$lang['STAFF_SHOP_SUB_ERROR3']="A shop with the name you specified already exists!";
$lang['STAFF_SHOP_SUB_SUCCESS']="Shop was created successfully.";
$lang['STAFF_SHOP_DELFORM_TITLE']="Deleting a shop from the game will remove it from the game. Be sure of this action, as there's no confirmation.";
$lang['STAFF_SHOP_DELFORM_FORM']="Shop:";
$lang['STAFF_SHOP_DELFORM_FORM_BTN']="Delete Shop";
$lang['STAFF_SHOP_DELFORM_SUB_ERROR1']="The shop is invalid or doesn't exist. Maybe you deleted it previously?";
$lang['STAFF_SHOP_DELFORM_SUB_SUCCESS']="Shop has been successfully removed from the game.";
$lang['STAFF_SHOP_IADDFORM_TITLE']="Use this form to add an item to a shop.";
$lang['STAFF_SHOP_IADDFORM_TD1']="Item:";
$lang['STAFF_SHOP_IADDFORM_BTN']="Add Item to Shop";
$lang['STAFF_SHOP_IADDSUB_ERROR']="You're attempting to add an item to an invalid shop, or an invalid item to a shop. Go back and try again.";
$lang['STAFF_SHOP_IADDSUB_ERROR2']="Item or shop is invalid or doesn't exist.";
$lang['STAFF_SHOP_IADDSUB_ERROR3']="The item you are trying to add to this shop is already listed in this shop. It makes no sense to list the same item twice.";
$lang['STAFF_SHOP_IADDSUB_SUCCESS']="Item has been successfully added to the stock of this shop.";

//Item Market
$lang['IMARKET_TITLE']="Item Market";
$lang['IMARKET_LISTING_TH1']="Listing Owner";
$lang['IMARKET_LISTING_TH2']="Item x Quantity";
$lang['IMARKET_LISTING_TH3']="Price/Item";
$lang['IMARKET_LISTING_TH4']="Total Price";
$lang['IMARKET_LISTING_TH5']="Links";
$lang['IMARKET_LISTING_TD1']="Remove Listing";
$lang['IMARKET_LISTING_TD2']="Buy Listing";
$lang['IMARKET_LISTING_TD3']="Gift Listing";
$lang['IMARKET_REMOVE_ERROR1']="You need to specify an item market listing you wish to effect.";
$lang['IMARKET_REMOVE_ERROR2']="The item market listing you wish to remove does not exist, or you are not its owner.";
$lang['IMARKET_REMOVE_SUCCESS']="The item market listing has been removed successfully.";
$lang['IMARKET_BUY_ERROR1']="The item market listing you wish to buy does not exist, or has been bought already.";
$lang['IMARKET_BUY_START']="Enter how many";
$lang['IMARKET_BUY_START1']="(s) you wish to purchase. There's currently";
$lang['IMARKET_BUY_START2']="available for purchase.";
$lang['IMARKET_BUY_SUB_ERROR1']="You cannot purchase your own items from the item market.";
$lang['IMARKET_BUY_SUB_ERROR2']="You do not have enough funds to buy this listing.";
$lang['IMARKET_BUY_SUB_ERROR3']="You cannot buy more than the quantity that was listed.";
$lang['IMARKET_BUY_SUB_ERROR4']="You cannot buy items from players who are on the same IP address as you.";
$lang['IMARKET_BUY_SUB_SUCCESS']="Item(s) have been bought! Check your inventory!";
$lang['IMARKET_GIFT_START1']="(s) you wish to purchase and send as a gift. There's currently";
$lang['IMARKET_GIFT_FORM_TH1']="Send Gift To:";
$lang['IMARKET_GIFT_SUB_ERROR1']="You are trying to send a gift to a user that does not exist!";
$lang['IMARKET_GIFT_SUB_ERROR2']="You cannot buy an item from the market and gift it back to the person who listed it.";
$lang['IMARKET_GIFT_SUB_ERROR3']="You cannot gift items from the item market to send to another user that shares the same IP address as you.";
$lang['IMARKET_GIFT_SUB_SUCCESS']="You have successfully bought the item and sent it out as a gift!";
$lang['IMARKET_ADD_TITLE']="Fill this form out to add your";
$lang['IMARKET_ADD_TITLE1']="to the item market.";
$lang['IMARKET_ADD_TH1']="Currency Type";
$lang['IMARKET_ADD_TH2']="Price per Item";
$lang['IMARKET_ADD_BTN']="Add to Market";
$lang['IMARKET_ADD_ERROR1']="You cannot add no items to the item market.";
$lang['IMARKET_ADD_ERROR2']="You are trying to add an item you do not own.";
$lang['IMARKET_ADD_ERROR3']="You do have not have enough of that item to add the quantity you wanted to onto the market.";
$lang['IMARKET_ADD_SUB_SUCCESS']="You have successfully listed this item on the item market.";

//Travel
$lang['TRAVEL_TITLE']="Horse Travel";
$lang['TRAVEL_TABLE']="Welcome to the horse stable. You can travel to other cities here, but at a cost. Where would you like to travel today? Note that as you progress further in the game, more locations will be made available to you. It will cost you ";
$lang['TRAVEL_TABLE2']="{$lang['INDEX_PRIMCURR']} to travel today.";
$lang['TRAVEL_TABLE_HEADER']="Town Name";
$lang['TRAVEL_TABLE_LEVEL']="Minimum Level";
$lang['TRAVEL_TABLE_GUILD']="Guild";
$lang['TRAVEL_TABLE_TAX']="Income Tax";
$lang['TRAVEL_TABLE_TRAVEL']="Travel";
$lang['TRAVEL_ERROR_CASHLOW']="You do not have enough {$lang['INDEX_PRIMCURR']} to travel to this location. Go back and try again.";
$lang['TRAVEL_ERROR_ALREADYTHERE']="You are already in this town! Why would you want to waste your money and travel to here again?";
$lang['TRAVEL_ERROR_ERRORGEN']="This town does not exist, or your level isn't high enough to visit this town. Go back and try again.";
$lang['TRAVEL_SUCCESS']="You have purchased a horse and traveled to";

//Staff towns
$lang['STAFF_TRAVEL_ADD']="Add a Town";
$lang['STAFF_TRAVEL_EDIT']="Edit a Town";
$lang['STAFF_TRAVEL_DEL']="Delete a Town";
$lang['STAFF_TRAVEL_ADDTOWN_TABLE']="Use this form to add a town into the game.";
$lang['STAFF_TRAVEL_ADDTOWN_TH1']="Town Name";
$lang['STAFF_TRAVEL_ADDTOWN_TH2']="Minimum Level";
$lang['STAFF_TRAVEL_ADDTOWN_TH3']="Tax Level";
$lang['STAFF_TRAVEL_ADDTOWN_BTN']="Create Town";
$lang['STAFF_TRAVEL_ADDTOWN_SUB_ERROR1']="You cannot name a new town after a town which already exists.";
$lang['STAFF_TRAVEL_ADDTOWN_SUB_ERROR2']="The town's tax rate must be between 0% and 20%";
$lang['STAFF_TRAVEL_ADDTOWN_SUB_ERROR3']="The town's minimum level requirement mus be greater than 0.";
$lang['STAFF_TRAVEL_ADDTOWN_SUB_SUCCESS']="You have successfully added this town into the game.";
$lang['STAFF_TRAVEL_DELTOWN_TABLE']="Use this form to delete a town from the game.";
$lang['STAFF_TRAVEL_DELTOWN_TH1']="Town";
$lang['STAFF_TRAVEL_DELTOWN_BTN']="Delete Town";
$lang['STAFF_TRAVEL_DELTOWN_SUB_ERROR1']="You cannot delete a non-existent town.";
$lang['STAFF_TRAVEL_DELTOWN_SUB_ERROR2']="You cannot delete the first town.";
$lang['STAFF_TRAVEL_DELTOWN_SUB_SUCCESS']="Town has been deleted successfully. Users and shops in this town have been moved to the starter town.";

//Guild Listing
$lang['GUILD_LIST']="Guild listing";
$lang['GUILD_LIST_TABLE1']="Guild Name";
$lang['GUILD_LIST_TABLE2']="Guild Level";
$lang['GUILD_LIST_TABLE3']="Member Count";
$lang['GUILD_LIST_TABLE5']="Guild Leader";
$lang['GUILD_LIST_TABLE4']="Hometown";

//Guild create
$lang['GUILD_CREATE']="Create a Guild";
$lang['GUILD_CREATE_ERROR']="You do not have enough {$lang['INDEX_PRIMCURR']} to buy purchase a guild. You need, at minimum, ";
$lang['GUILD_CREATE_ERROR1']="You are not a high enough level to purchase a guild. You need to be, at minimum, ";
$lang['GUILD_CREATE_ERROR2']="You cannot create a guild while you're currently a member of one.";
$lang['GUILD_CREATE_ERROR3']="You cannot create a guild named after an already existing guild.";
$lang['GUILD_CREATE_FORM']="Fill this form out to create your guild. Your guild's hometown will be set to the town you are currently located in.";
$lang['GUILD_CREATE_FORM1']="Guild Name";
$lang['GUILD_CREATE_FORM2']="Guild Description";
$lang['GUILD_CREATE_BTN']="Create Guild for ";
$lang['GUILD_CREATE_SUCCESS']="You have successfully created a guild!";

//Guild Viewing
$lang['GUILD_VIEW_GUILD']="Guild";
$lang['GUILD_VIEW_ERROR']="You are trying to view a non-existent guild. Check your source and try again.";
$lang['GUILD_VIEW_LEADER']="Guild Leader";
$lang['GUILD_VIEW_COLEADER']="Guild Co-Leader";
$lang['GUILD_VIEW_LEVEL']="Guild Level";
$lang['GUILD_VIEW_MEMBERS']="Guild Members";
$lang['GUILD_VIEW_LOCATION']="Guild Location";
$lang['GUILD_VIEW_USERS']="Guild Member List";
$lang['GUILD_VIEW_APPLY']="Apply to Guild";
$lang['GUILD_VIEW_LIST']="Members List for the";
$lang['GUILD_VIEW_LIST2']="guild";
$lang['GUILD_VIEW_ERROR']="You need to specify a guild you wish to view. Check your source and try again.";
$lang['GUILD_APP_TITLE']="Filling out Application to join";
$lang['GUILD_APP_INFO']="Type in a reason you should be in this guild. Be polite, honest and accurate with your information.";
$lang['GUILD_APP_ERROR']="You cannot send an application to a guild whilst you're currently in one. Leave your current guild and try again.";
$lang['GUILD_APP_BTN']="Submit Application";
$lang['GUILD_APP_ERROR1']="You have already sent an application to join this guild. Please wait until you get a response before sending in another.";
$lang['GUILD_APP_SUCC']="You have successfully sent in your application to join this guild!";
$lang['GUILD_VIEW_DESC']="Guild's Description";

//Guild Warring List
$lang['GUILD_WAR_TITLE']="Guild Wars";
$lang['GUILD_WAR_ERR']="There are currently no active guild wars.";
$lang['GUILD_WAR_TD']="(Points: ";
$lang['GUILD_WAR_TD1']=")";
$lang['GUILD_WAR_TD2']="VS";

//Staff rules
$lang['STAFF_RULES_ADD_FORM']="Use this form to add rules into the game. Be clear and concise. The more difficult language and terminology you use, the less people may understand.";
$lang['STAFF_RULES_ADD_BTN']="Add Rule";
$lang['STAFF_RULES_ADD_SUBFAIL']="You cannot add a rule an empty rule.";
$lang['STAFF_RULES_ADD_SUBSUCC']="You have successfully created a new rule.";

//Game rules
$lang['GAMERULES_TITLE']="Rules";
$lang['GAMERULES_TEXT']="You are expected to follow these rules. You are also expected to check back on these fairly frequently as these rules may change without notice. Staff will not accept ignorance as an excuse if you break one of these rules.";

//View Guild
$lang['VIEWGUILD_ERROR1']="You are not in a guild, so you cannot view your guild's information.";
$lang['VIEWGUILD_ERROR2']="It looks like your guild's been deleted. Check with staff to update your account.";
$lang['VIEWGUILD_TITLE']="Your Guild,";
$lang['VIEWGUILD_HOME_SUMMARY']="Guild Summary";
$lang['VIEWGUILD_HOME_DONATE']="Donate to Guild";
$lang['VIEWGUILD_HOME_CRIME']="Guild Crimes";
$lang['VIEWGUILD_HOME_USERS']="Guild Members List";
$lang['VIEWGUILD_HOME_LEAVE']="Leave Guild";
$lang['VIEWGUILD_HOME_ATKLOG']="Guild Attack Logs";
$lang['VIEWGUILD_HOME_ARMORY']="Guild Armory";
$lang['VIEWGUILD_HOME_STAFF']="Guild Staff Room";
$lang['VIEWGUILD_HOME_ANNOUNCE']="Guild Announcement";
$lang['VIEWGUILD_HOME_EVENT']="Last 10 Guild Events";
$lang['VIEWGUILD_HOME_EVENTTEXT']="Event Text";
$lang['VIEWGUILD_HOME_EVENTTIME']="Event Time";
$lang['VIEWGUILD_SUMMARY_TITLE']="Guild Summary";
$lang['VIEWGUILD_SUMMARY_OWNER']="Guild Leader";
$lang['VIEWGUILD_SUMMARY_COOWNER']="Guild Co-Leader";
$lang['VIEWGUILD_SUMMARY_MEM']="Members / Max Capacity";
$lang['VIEWGUILD_SUMMARY_LVL']="Guild Level";
$lang['VIEWGUILD_SUMMARY_XP']="Guild XP";
$lang['VIEWGUILD_NA']="N/A";
$lang['VIEWGUILD_DONATE_TITLE']="Enter the amount of currency you wish to donate to your guild. You currently have ";
$lang['VIEWGUILD_DONATE_BTN']="Donate to Guild";
$lang['VIEWGUILD_DONATE_ERR1']="You must fill out the previous form to donate.";
$lang['VIEWGUILD_DONATE_ERR2']="You cannot donate more {$lang['INDEX_PRIMCURR']} than you currently have.";
$lang['VIEWGUILD_DONATE_ERR3']="You cannot donate more {$lang['INDEX_SECCURR']} than you currently have.";
$lang['VIEWGUILD_DONATE_ERR4']="You are trying to donate more than your guild's vault can hold. Your guild's vault can only hold ";
$lang['VIEWGUILD_DONATE_SUCC']="You've successfully donated the specified amounts to your guild.";
$lang['VIEWGUILD_MEMBERS_TH1']="User";
$lang['VIEWGUILD_MEMBERS_TH2']="Level";
$lang['VIEWGUILD_MEMBERS_BTN']="Kick";
$lang['VIEWGUILD_IDX']="Guild Index";
$lang['VIEWGUILD_KICK_SUCCESSS']="You've successfully kicked this user from the guild.";
$lang['VIEWGUILD_KICK_ERR']="Sorry, but you cannot kick your guild's leader. If your leader is inactive, contact staff so that you may take their spot.";
$lang['VIEWGUILD_KICK_ERR1']="You cannot kick yourself from the guild. If you wish to leave, transfer your powers to someone else, then leave.";
$lang['VIEWGUILD_KICK_ERR2']="You're trying to kick a user who isn't in your guild or doesn't exist.";
$lang['VIEWGUILD_KICK_ERR3']="You do not have permission to kick users from this guild.";
$lang['VIEWGUILD_LEAVE_ERR']="You cannot leave while you're the owner/co-owner of your guild. Transfer your rights to another member in the guild and try again.";
$lang['VIEWGUILD_LEAVE_SUCC']="You have successfully left your guild.";
$lang['VIEWGUILD_LEAVE_SUCC1']="You've decided to stay in your guild for now.";
$lang['VIEWGUILD_LEAVE_INFO']="Are you 100% sure you wish to leave your guild? You will have to reapply if you leave and wish to come back.";
$lang['VIEWGUILD_LEAVE_BTN']="Yes, leave!";
$lang['VIEWGUILD_LEAVE_BTN1']="No, wait, stay!";
$lang['VIEWGUILD_ATKLOGS_INFO']="This table lists the last 50 attacks anyone in your guild was involved with.";
$lang['VIEWGUILD_ATKLOGS_TD1']="Time";
$lang['VIEWGUILD_ATKLOGS_TD2']="Attack Info";
$lang['VIEWGUILD_STAFF_ERROR']="Only the leader and co-leader of your guild can view this area.";
$lang['VIEWGUILD_STAFF_IDX_APP']="Application Management";
$lang['VIEWGUILD_STAFF_IDX_VAULT']="Vault Management";
$lang['VIEWGUILD_STAFF_IDX_COOWNER']="Transfer Co-Leader";
$lang['VIEWGUILD_STAFF_IDX_AMENT']="Change Guild Announcement";
$lang['VIEWGUILD_STAFF_IDX_MM']="Mass Mail Guild";
$lang['VIEWGUILD_STAFF_IDX_MP']="Mass Pay Guild";
$lang['VIEWGUILD_STAFF_IDX_DESC']="Change Guild Description";
$lang['VIEWGUILD_STAFF_IDX_LEADER']="Transfer Leader";
$lang['VIEWGUILD_STAFF_IDX_NAME']="Change Guild Name";
$lang['VIEWGUILD_STAFF_IDX_TOWN']="Change Guild Town";
$lang['VIEWGUILD_STAFF_IDX_UNTOWN']="Surrender Guild Town";
$lang['VIEWGUILD_STAFF_IDX_DECLAREWAR']="Declare War";
$lang['VIEWGUILD_STAFF_IDX_LVLUP']="Level Up Guild";
$lang['VIEWGUILD_STAFF_IDX_TAX']="Change Tax Level";
$lang['VIEWGUILD_STAFF_APP_TH0']="Filing Time";
$lang['VIEWGUILD_STAFF_APP_TH1']="Applicant";
$lang['VIEWGUILD_STAFF_APP_TH2']="Level";
$lang['VIEWGUILD_STAFF_APP_TH3']="Application Text";
$lang['VIEWGUILD_STAFF_APP_TH4']="Actions";
$lang['VIEWGUILD_STAFF_APP_BTN']="Accept";
$lang['VIEWGUILD_STAFF_APP_BTN1']="Decline";
$lang['VIEWGUILD_STAFF_APP_DENY_TEXT']="You have successfully declined this application.";
$lang['VIEWGUILD_STAFF_APP_ACC_ERR']="Your guild does not have the capacity to accept this member. Level your guild up to get more capacity.";
$lang['VIEWGUILD_STAFF_APP_ACC_ERR1']="This player is already in a guild.";
$lang['VIEWGUILD_STAFF_APP_ACC_ERR2']="This player's level is too low to access the town you own.";
$lang['VIEWGUILD_STAFF_APP_ACC_SUCC']="You have successfully accepted this user's application!";
$lang['VIEWGUILD_STAFF_APP_WOT']="We don't know how you got here... but yeah... you're kinda not supposed to be here.";
$lang['VIEWGUILD_GYM_ERR']="Your guild's personal gym will be open for use at guild level 3.";
$lang['VIEWGUILD_STAFF_VAULT']="Your guild's vault currently has";
$lang['VIEWGUILD_STAFF_VAULT1']="Select User";
$lang['VIEWGUILD_STAFF_VAULT_BTN']="Give From Vault";
$lang['VIEWGUILD_STAFF_VAULT_ERR']="Your vault does not have that much {$lang['INDEX_PRIMCURR']} to give out.";
$lang['VIEWGUILD_STAFF_VAULT_ERR1']="Your vault does not have that much {$lang['INDEX_SECCURR']} to give out.";
$lang['VIEWGUILD_STAFF_VAULT_ERR2']="You have to give out at least one piece of currency to use this form.";
$lang['VIEWGUILD_STAFF_VAULT_ERR3']="The user you're trying to give to doesn't exist, or isn't in your guild. Check your source and try again.";
$lang['VIEWGUILD_STAFF_VAULT_ERR4']="You cannot gift currency from the vault to players who share your IP address.";
$lang['VIEWGUILD_STAFF_VAULT_SUCC']="The user has been given the specified currency from the vault.";
$lang['VIEWGUILD_STAFF_COLEADER_INFO']="Select a user from the dropdown to give them co-leadership privileges.";
$lang['VIEWGUILD_STAFF_COLEADER_ERR']="You cannot give co-leadership of your guild to a user who doesn't exist, or isn't in your guild to begin with.";
$lang['VIEWGUILD_STAFF_COLEADER_SUCC']="Co-leadership privileges for the guild has been transferred successfully.";
$lang['VIEWGUILD_STAFF_AMENT_INFO']="Use this form to update your guild's announcement.";
$lang['VIEWGUILD_STAFF_AMENT_BTN']="Update Announcement";
$lang['VIEWGUILD_STAFF_AMENT_SUCC']="You have successfully updated your guild's announcement.";
$lang['VIEWGUILD_STAFF_MM_INFO']="Use this form to send a mass mail to each member of your guild.";
$lang['VIEWGUILD_STAFF_MM_SUCC']="The mass mail has been successfully sent to your guild.";
$lang['VIEWGUILD_STAFF_MP_INFO']="Use this form to pay your guild all at once.";
$lang['VIEWGUILD_STAFF_MP_TH']="Payment";
$lang['VIEWGUILD_STAFF_MP_BTN']="Mass Pay";
$lang['VIEWGUILD_STAFF_MP_SUCC']="Mass payment has been given out to your guild.";
$lang['VIEWGUILD_STAFF_MP_ERR']="Your guild's vault does not have enough {$lang['INDEX_PRIMCURR']} to give out to everyone in the guild.";
$lang['VIEWGUILD_STAFF_MP_ERR2']="could not be given a mass payment as you both share IP Addresses.";
$lang['VIEWGUILD_STAFF_MP_SUCC2']="was paid successfully.";
$lang['VIEWGUILD_STAFF_DESC_INFO']="Use this form to update your guild's description.";
$lang['VIEWGUILD_STAFF_DESC_BTN']="Change Description";
$lang['VIEWGUILD_STAFF_DESC_SUCC']="You have successfully updated your guild's description.";
$lang['VIEWGUILD_STAFF_LEADERONLY']="We're sorry, this area is for only the leader of the guild.";
$lang['VIEWGUILD_STAFF_LEADER_INFO']="Select a user from the dropdown to give them leadership privledges.";
$lang['VIEWGUILD_STAFF_LEADER_ERR']="You cannot give leadership of your guild to a user who doesn't exist, or isn't in your guild to begin with.";
$lang['VIEWGUILD_STAFF_LEADER_SUCC']="Leadership privileges for the guild has been transferred successfully.";
$lang['VIEWGUILD_STAFF_NAME_INFO']="Use this form to change your guild's name.";
$lang['VIEWGUILD_STAFF_NAME_BTN']="Change Name";
$lang['VIEWGUILD_STAFF_NAME_TH']="Guild Name";
$lang['VIEWGUILD_STAFF_NAME_ERR']="You cannot rename your guild after a guild that already exists.";
$lang['VIEWGUILD_STAFF_NAME_SUCC']="You have successfully changed your guild's name.";
$lang['VIEWGUILD_STAFF_TOWN_INFO']="Use this form to claim a town in the name of your guild. This town must be unowned, and must be accessible to all your guild members. If it is currently owned, you must declare war on the owning guild to get a chance to claim the town as yours.";
$lang['VIEWGUILD_STAFF_TOWN_TH']="Guild Town";
$lang['VIEWGUILD_STAFF_TOWN_BTN']="Claim Town";
$lang['VIEWGUILD_STAFF_TOWN_ERR']="You cannot own more than one town.";
$lang['VIEWGUILD_STAFF_TOWN_ERR1']="You cannot own a town that doesn't exist.";
$lang['VIEWGUILD_STAFF_TOWN_ERR2']="You cannot own a town that is owned by another guild. If you want this town, you will need to declare war on its owner.";
$lang['VIEWGUILD_STAFF_TOWN_ERR3']="The town you've selected cannot be accessed by one or more of your guild members.";
$lang['VIEWGUILD_STAFF_TOWN_SUCC']="You have successfully claimed this town in the name of your guild.";
$lang['VIEWGUILD_STAFF_UNTOWN_ERR']="Your guild does not currently own a town. To claim a town, you need to either claim it, or beat the owner guild in war.";
$lang['VIEWGUILD_STAFF_UNTOWN_SUCC']="You have successfully given up your guild's town willingly.";
$lang['VIEWGUILD_STAFF_UNTOWN_CHECK']="Are you sure you wish to surrender your guild's town? Doing so may not guarantee you will get this city back.";
$lang['VIEWGUILD_STAFF_WAR_FORM']="Use this form to declare war on. Be ready to reap what you sow.";
$lang['VIEWGUILD_STAFF_WAR_TH']="Guild";
$lang['VIEWGUILD_STAFF_WAR_BTN']="Declare War";
$lang['VIEWGUILD_STAFF_WAR_ERR']="You cannot declare war on your own guild. That'd be kinda weird.";
$lang['VIEWGUILD_STAFF_WAR_ERR1']="The guild you're trying to declare war upon does not exist.";
$lang['VIEWGUILD_STAFF_WAR_ERR2']="You are already at war with this guild. What would be the point of starting a second war?";
$lang['VIEWGUILD_STAFF_WAR_ERR3']="You've already warred this guild too recently. Come back seven days after the conclusion of the previous war.";
$lang['VIEWGUILD_STAFF_WAR_SUCC']="You have successfully declared war. Gather your guild to arms, and prepare for this bloodbath. The war will end in 72 hours. The guild with most points will win. Gain points by winning in combat.";
$lang['VIEWGUILD_STAFF_LVLUP']="Here you may level up your guild. Your guild will need the minimum required XP to do this. You may gain guild XP by going to war with another guild and gaining points in war. At your guild's level, your guild will need";
$lang['VIEWGUILD_STAFF_LVLUP1']="Guild XP to level up to the next level. Do you wish to attempt a level up?";
$lang['VIEWGUILD_STAFF_LVLUP_BTN']="Level Up";
$lang['VIEWGUILD_STAFF_LVLUP_ERR']="Your guild does not have enough {$lang['GEN_EXP']} to level up at this time.";
$lang['VIEWGUILD_STAFF_LVLUP_SUCC']="You have successfully leveled up your guild.";
$lang['VIEWGUILD_STAFF_TAX_ERR']="Your guild does not own a town to even put a tax level upon.";
$lang['VIEWGUILD_STAFF_TAX_FORM']="Use this form to set a tax level on the town your guild owns.";
$lang['VIEWGUILD_STAFF_TAX_TH']="Percentage";
$lang['VIEWGUILD_STAFF_TAX_BTN']="Set Tax Rate";
$lang['VIEWGUILD_STAFF_TAX_ERR2']="You can only have a tax rate between 0% and 20%";
$lang['VIEWGUILD_STAFF_TAX_SUCC']="Congratulations, you've set this town's tax rate. If its too high, you may risk getting warred upon by another guild.";
$lang['VIEWGUILD_WAR_ALERT']="Guild Wars in Progress!";
$lang['VIEWGUILD_WAR_INFO']="Your guild is currently fighting ";
$lang['VIEWGUILD_WAR_INFO1']="different guild wars. Find more information";
$lang['VIEWGUILD_WARVIEW_INFO']="These are the current wars your guild is participating in.";
$lang['VIEWGUILD_WARVIEW_TD1']="Declarer";
$lang['VIEWGUILD_WARVIEW_TD2']="Declared Upon";
$lang['VIEWGUILD_WARVIEW_TD3']="War Concludes";

//Hire Spy
$lang['SPY_ERROR1']="You must specify a user you wish to spy on!";
$lang['SPY_ERROR2']="There is no reason to spy on yourself.";
$lang['SPY_ERROR3']="The user you are attempting to spy on does not exist.";
$lang['SPY_ERROR4']="You do not have enough {$lang['INDEX_PRIMCURR']} to spy on this user!";
$lang['SPY_ERROR5']="You cannot spy on other players when you are in the dungeon!";
$lang['SPY_ERROR6']="You cannot spy on other players when you are in the infirmary, trying to feel better.";
$lang['SPY_ERROR7']="Why would you want to spy on your fellow guild mates?";
$lang['SPY_START']="You are attempting to send out a spy to gather information on";
$lang['SPY_START1']=". This will cost you 500 {$lang['INDEX_PRIMCURR']} multiplied by their level. (";
$lang['SPY_START2']="{$lang['INDEX_PRIMCURR']} in this case.) Please remember that success is not guaranteed. If you're wanting to assume the risk, press the button to send out a spy!";
$lang['SPY_BTN']="Send Spy";
$lang['SPY_FAIL1']="You attempt to get information on your target. Oh shoot! They spot you! Run, run, run! As fast as you can. I don't think they saw you. You got lucky this time, bud.";
$lang['SPY_FAIL2']="You attempt to get information on your target. Oh shoot! They spot you! Run! They can positively ID you, so they now know who tried to spy on them.";
$lang['SPY_FAIL3']="You attempt to get information on your target. You follow them closely, almost like you're a professional stalker. A guard notices this and punches you in the face. You wake up in a dungeon cell.";
$lang['SPY_SUCCESS']="At about";
$lang['SPY_SUCCESS1']="{$lang['INDEX_PRIMCURR']} per attempt, you have successfully found information on";
$lang['SPY_SUCCESS2']="! Here is that information.";

//Staff estates
$lang['STAFF_ESTATE_ADD']="Add Estate";
$lang['STAFF_ESTATE_EDIT']="Edit Estate";
$lang['STAFF_ESTATE_DEL']="Delete Estate";
$lang['STAFF_ESTATE_ADD_TABLE']="Use this form to add an estate into the game.";
$lang['STAFF_ESTATE_ADD_TH1']="Estate Name";
$lang['STAFF_ESTATE_ADD_TH2']="Estate Cost";
$lang['STAFF_ESTATE_ADD_TH3']="Estate Minimum Level";
$lang['STAFF_ESTATE_ADD_TH4']="Estate Will Level";
$lang['STAFF_ESTATE_ADD_BTN']="Create Estate";
$lang['STAFF_ESTATE_ADD_ERROR1']="You cannot have more than one estate with the same name.";
$lang['STAFF_ESTATE_ADD_ERROR2']="You cannot have an estate with the same will as another.";
$lang['STAFF_ESTATE_ADD_ERROR3']="You cannot have an estate with a level requirement lower than 1.";
$lang['STAFF_ESTATE_ADD_ERROR4']="You cannot have an estate with a will level equal to, or lower than, 100.";
$lang['STAFF_ESTATE_ADD_SUCCESS']="Estate has been added to the game successfully.";
$lang['STAFF_ESTATE_DEL_INFO']="Use this form to delete an estate. Players will be refunded and moved to the lowest estate if they own the one you delete.";
$lang['STAFF_ESTATE_DEL_BTN']="Delete Estate";
$lang['STAFF_ESTATE_DEL_ERR']="You are trying to delete a nonexistent estate!";
$lang['STAFF_ESTATE_DEL_ERR1']="You cannot delete the starter estate!";
$lang['STAFF_ESTATE_DEL_SUCC']="You have successfully deleted this estate!";
$lang['STAFF_ESTATE_EDIT_INFO']="Select an estate from the dropdown to edit.";
$lang['STAFF_ESTATE_EDIT_BTN']="Edit Estate";
$lang['STAFF_ESTATE_EDIT_ERR']="You are trying to edit a nonexistent estate!";
$lang['STAFF_ESTATE_EDIT_ERR1']="You forgot one or inputs on the previous form.";
$lang['STAFF_ESTATE_EDIT_ERR2']="You are trying to alter a non-existent estate.";
$lang['STAFF_ESTATE_EDIT_ERR3']="This estate must remain at 100 will. We're sorry.";
$lang['STAFF_ESTATE_EDIT_TABLE']="Use this form to edit an estate.";
$lang['STAFF_ESTATE_EDIT_SUCC']="You have successfully edited this estate!";

//Estates
$lang['ESTATES_START']="Your Current Estate:";
$lang['ESTATES_SELL']="Sell Your Estate for 75%";
$lang['ESTATES_TABLE1']="Estate Name";
$lang['ESTATES_TABLE2']="Level Requirement";
$lang['ESTATES_TABLE3']="Cost ({$lang['INDEX_PRIMCURR']})";
$lang['ESTATES_TABLE4']="Will Level";
$lang['ESTATES_ERROR1']="You are trying to purchase a non-existent estate. Check your source and try again.";
$lang['ESTATES_ERROR2']="You cannot buy an estate that has less will than your current estate. That just wouldn't make sense.";
$lang['ESTATES_ERROR3']="You do not have enough {$lang['INDEX_PRIMCURR']} to buy this estate";
$lang['ESTATES_ERROR4']="You cannot buy an estate that has the same will of your current estate. That just wouldn't make sense.";
$lang['ESTATES_ERROR5']="You cannot sell your estate if you're nude and proud, bud.";
$lang['ESTATES_ERROR6']="Your level is too low for this estate, my friend.";
$lang['ESTATES_SUCCESS1']="You have successfully bought the";
$lang['ESTATES_SUCCESS2']="You have sold your property for 75% of its original price and went back to being nude and proud.";
$lang['ESTATES_INFO']="List below are estates you can buy. Estates have a will level. the better the will level, the more stats you will gain while training. So it's recommended to buy the best estate for your level. Tap on the estate name to purchase the estate. Don't worry, you will be able to sell bought estates back to the game for 75% of its value.";

//Roulette
$lang['ROULETTE_TITLE']="Roulette";
$lang['ROULETTE_INFO']="Ready to test your luck? Awesome! Here at the roulette table, the house always wins. To combat players losing all their wealth in one go, we've put in a bet restriction. At your level, you can only bet";
$lang['ROULETTE_NOREFRESH']="Please do not refresh while playing roulette. Please use the links provided, thank you!";
$lang['ROULETTE_TABLE1']="Bet";
$lang['ROULETTE_TABLE2']="Pick #";
$lang['ROULETTE_ERROR1']="You cannot bet more {$lang['INDEX_PRIMCURR']} than you currently have.";
$lang['ROULETTE_ERROR2']="You are trying to place a bet higher than your currently allowed max bet.";
$lang['ROULETTE_ERROR3']="You can only bet on the numbers between 0 and 36.";
$lang['ROULETTE_ERROR4']="You must specify a bet larger than 0 {$lang['INDEX_PRIMCURR']}.";
$lang['ROULETTE_LOST']=". You lose your bet. Sorry man.";
$lang['ROULETTE_WIN']=" and won! You keep your bet, and pocket an extra";
$lang['ROULETTE_BTN1']="Place Bet!";
$lang['ROULETTE_BTN2']="Again. Same bet, please.";
$lang['ROULETTE_BTN3']="Again, but with a different bet.";
$lang['ROULETTE_BTN4']="I quit. I don't want to go broke.";
$lang['ROULETTE_START']="You put in your bet and pull the handle down. Around and around the wheel spins. It stops and lands on";

//High Low
$lang['HILOW_NOREFRESH']="Please do not refresh while playing High/Low. Use the links we provide, thank you!";
$lang['HILOW_INFO']="Welcome to High/Low. Here you will bet on whether or not the deal will draw a number lower or higher than the number shown. The number range is 1 through 100.";
$lang['HILOW_SHOWN']="The game operator shows the number";
$lang['HILOW_WATDO']="Select the button on how you feel the next number will be compared to this number.";
$lang['HILOW_NOBET']="You do not have enough {$lang['INDEX_PRIMCURR']} to play High/Low. You need at least";
$lang['HILOW_LOWER']="Lower";
$lang['HILOW_HIGHER']="Higher";
$lang['HIGHLOW_HIGH']="You've guessed the game operator would show a number higher than ";
$lang['HIGHLOW_REVEAL']="The game operator reveals the number";
$lang['HIGHLOW_LOSE']="You have lost this time, sorry bud.";
$lang['HIGHLOW_WIN']="You have won this time, congratulations.";
$lang['HIGHLOW_LOWER']="You've guessed the game operator would show a number lower than ";
$lang['HIGHLOW_TIE']="The game operator shows the exact number as last time. You lose nothing.";
$lang['HILOW_UNDEFINEDNUMBER']="The number from the last page wasn't defined... Weird. Stop tampering with shit, man.";

//ReCaptcha
$lang['RECAPTCHA_TITLE']="reCaptcha";
$lang['RECAPTCHA_INFO']="This is a needed evil. Just verify that you're not a bot.";
$lang['RECAPTCHA_BTN']="Verify";
$lang['RECAPTCHA_EMPTY']="You cannot leave the reCaptcha form empty! Re-directing you back in about 5 seconds.";
$lang['RECAPTCHA_FAIL']="You failed the reCaptcha. Re-directing you back in about 5 seconds.";
$lang['RECAPTCHA_NOTSET']="ReCaptcha did not return a response. You may not have an internet connection, or the administration hasn't setup ReCaptcha correctly. Re-directing you back in about 5 seconds.";

//Poke
$lang['POKE_TITLE']="Are you sure you wanna poke";
$lang['POKE_TITLE1']="? Please do not harass users using this. Staff will find out, and they can remove your privilege to poke others.";
$lang['POKE_ERROR1']="You need to specify a person you wish to poke.";
$lang['POKE_ERROR2']="No, you cannot poke yourself!";
$lang['POKE_ERROR3']="You cannot poke non-existent users!";
$lang['POKE_BTN']="POKE!";
$lang['POKE_SUCC']="You have successfully poked this user.";

//Staff Change PW
$lang['STAFF_USERS_CP_FORM_INFO']="Use this form to change a user's password.";
$lang['STAFF_USERS_CP_USER']="User";
$lang['STAFF_USERS_CP_FORM_BTN']="Change Password";
$lang['STAFF_USERS_CP_PW']="New Password";
$lang['STAFF_USERS_CP_ERROR']="You cannot change the password for the admin account this way.";
$lang['STAFF_USERS_CP_ERROR1']="You cannot change the password for other admin accounts this way.";
$lang['STAFF_USERS_CP_SUCCESS']="User's password has been changed successfully.";

//Item Send
$lang['ITEM_SEND_ERROR']="You are attempting to send a non-existent item, or you just do not have this item in your inventory.";
$lang['ITEM_SEND_ERROR1']="You are trying to send more of this item than you currently have.";
$lang['ITEM_SEND_ERROR2']="You are trying to send this item to a user that does not exist.";
$lang['ITEM_SEND_ERROR3']="It makes no sense to send yourself an item.";
$lang['ITEM_SEND_ERROR4']="Hold up. You cannot send items to players who share the same IP address as you.";
$lang['ITEM_SEND_SUCC']="You have successfully sent";
$lang['ITEM_SEND_SUCC1']="to";
$lang['ITEM_SEND_FORMTITLE']="Enter who you wish to send";
$lang['ITEM_SEND_FORMTITLE1']="along with the quantity you wish to send. You have";
$lang['ITEM_SEND_FORMTITLE2']="Alternatively, you can enter a user's id number.";
$lang['ITEM_SEND_TH']="User";
$lang['ITEM_SEND_TH1']="Quantity to Send";
$lang['ITEM_SEND_BTN']="Send Item(s)";

//Slots
$lang['SLOTS_INFO']="Welcome to the slots machine. Bet some of your hard earned cash for a slim chance to win big! At your level, we've imposed a betting restriction of ";
$lang['SLOTS_TABLE1']="Bet";
$lang['SLOTS_BTN']="Spin baby, spin!";
$lang['SLOTS_TITLE']="Slot Machine";
$lang['SLOTS_NOREFRESH']="Please do not refresh the page while gambling at the slot machines. Thank you!";

//Bot tent
$lang['BOTTENT_TITLE']="Bot Tent";
$lang['BOTTENT_DESC']="Welcome to the Bot Tent. Here you may challenge NPCs to battle. If you win, you'll receive an item. These items may or may not be useful in your adventures. To deter players getting massive amounts of items, you can only attack these NPCs every so often. Their cooldown is listed here as well. To receive the item, you must mug the bot.";
$lang['BOTTENT_TH']="Bot Name";
$lang['BOTTENT_TH1']="Bot Level";
$lang['BOTTENT_TH2']="Bot Cooldown";
$lang['BOTTENT_TH3']="Bot Item Drop";
$lang['BOTTENT_TH4']="Attack";
$lang['BOTTENT_WAIT']="Cooldown Remaining: ";
$lang['BOTTENT_CHANCE']="Success Chance:";

//Staff bots
$lang['STAFF_BOTS_TITLE']="Staff Bots";
$lang['STAFF_BOTS_ADD']="Add Bot";
$lang['STAFF_BOTS_DEL']="Delete Bot";
$lang['STAFF_BOTS_ADD_FRM1']="Use this form to add bots to the game that drop items when mugged.";
$lang['STAFF_BOTS_ADD_FRM2']="Bot User";
$lang['STAFF_BOTS_ADD_FRM3']="Item Dropped";
$lang['STAFF_BOTS_ADD_FRM4']="Cooldown Time (Seconds)";
$lang['STAFF_BOTS_ADD_BTN']="Add Bot";
$lang['STAFF_BOTS_ADD_ERROR']="You're missing one of the required inputs. Go back and try again.";
$lang['STAFF_BOTS_ADD_ERROR1']="You're trying to add a bot that already exists in the bot listing. Go back and try again.";
$lang['STAFF_BOTS_ADD_ERROR2']="You can only add NPCs to the bot listing. Go back and try again.";
$lang['STAFF_BOTS_ADD_ERROR3']="You cannot have a bot drop a non-existent item. Go back and try again.";
$lang['STAFF_BOTS_ADD_SUCCESS']="You've successfully added an NPC to the bot list.";
$lang['STAFF_BOTS_DEL_INFO']="Select a bot from the dropdown so it can be deleted.";
$lang['STAFF_BOTS_DEL_TH']="Bot to Delete";
$lang['STAFF_BOTS_DEL_BTN']="Delete Bot";
$lang['STAFF_BOTS_DEL_ERROR']="You didn't select a bot to delete.";
$lang['STAFF_BOTS_DEL_ERROR1']="This user isn't even on the bot list.";
$lang['STAFF_BOTS_DEL_SUCCESS']="You've successfully removed this NPC from the bot list.";

//VIP Donation Listing
$lang['VIP_LIST']="Buying a VIP Pack";
$lang['VIP_INFO']="If you purchase a VIP package from below, you'll be gifted the following depending on the pack you buy. If you commit fraud, you'll be permanently banned.";
$lang['VIP_TABLE_TH1']="Pack Info";
$lang['VIP_TABLE_TH2']="Pack Contents";
$lang['VIP_TABLE_TH3']="Link";
$lang['VIP_TABLE_VDINFO']="VIP Days disable ads around the game. You'll also receive 16% energy refill instead of 8%. You'll also receive a star by your name, and your name will change color. How awesome is that?";
$lang['VIP_THANKS']="Thank you for donating to";
$lang['VIP_CANCEL']="You have successfully cancelled your donation. Please donate later!";
$lang['VIP_SUCCESS']="We appreciate it completely. You can view a receipt of this transaction at <a href='http://www.paypal.com'>Paypal</a>. Your items should be given to you automatically fairly soon. If not, please contact an admin for help!";

//Staff punishments
$lang['STAFF_PUNISHED_FED']="Fedjail User";
$lang['STAFF_PUNISHED_UNFED']="Unfedjail User";
$lang['STAFF_PUNISHED_FWARN']="Forum Warn User";
$lang['STAFF_PUNISHED_IPSEARCH']="IP Search";
$lang['STAFF_PUNISHED_FBAN']="Forum Ban User";
$lang['STAFF_PUNISHED_UFBAN']="Unforum Ban User";
$lang['STAFF_PUNISHED_MASSMAIL']="Send Mass Mail";
$lang['STAFF_PUNISHED_MASSEMAIL']="Send Mass Email";
$lang['STAFF_PUNISHFED_FORM']="Jailing User";
$lang['STAFF_PUNISHFED_INFO']="Placing a user in federal jail will render their account virtually useless. They will not be able to do anything in-game.";
$lang['STAFF_PUNISHFED_TH']="User:";
$lang['STAFF_PUNISHFED_TH1']="Days:";
$lang['STAFF_PUNISHFED_TH2']="Reason:";
$lang['STAFF_PUNISHFED_BTN']="Place User in Federal Jail";
$lang['STAFF_PUNISHFED_ERR']="You cannot place a user that doesn't exist into the federal jail.";
$lang['STAFF_PUNISHFED_ERR1']="You need to fill in all the inputs on the previous page for this to work correctly.";
$lang['STAFF_PUNISHFED_ERR2']="You cannot place admins in the federal jail. Please remove their staff privileges them first before trying again.";
$lang['STAFF_PUNISHFED_ERR3']="This user is already in the federal jail. Please edit their current sentence.";
$lang['STAFF_PUNISHFED_SUCC']="The user has been placed into the federal jail successfully.";

//Fedjail listing
$lang['FJ_TITLE']="Federal Jail";
$lang['FJ_INFO']="This is where bad folks go when they break the rules. Be a smart person and don't break the rules or you may never see the light of day again.";
$lang['FJ_WHO']="Who";
$lang['FJ_TIME']="Time Remaining";
$lang['FJ_RS']="Reason";
$lang['FJ_JAILER']="Jailer";

//Mining
$lang['MINE_INFO']="Welcome to the dangerous mines, brainless fool. Riches are avaliable for you, if you have the skill. Each mine has its own requirements, and could even have a special pickaxe that you need to use.";
$lang['MINE_DUNGEON']="Only honorable warriors can mine. Come back when you've served your debt to society.";
$lang['MINE_INFIRM']="Only healthy warriors can mine. Come back when you've ripped that bandaid off your finger.";
$lang['MINE_LEVEL']="You currently have a mining level of";
$lang['MINE_POWER']="Mining Power";
$lang['MINE_XP']="Mining {$lang['GEN_EXP']}";
$lang['MINE_SPOTS']="Open Mines";
$lang['MINE_SETS']="Purchase Power Sets";
$lang['MINE_BUY_ERROR']="You are attempting to buy more power sets than you currently have available to you. Remember, you get a power set every time you level up your mining level.";
$lang['MINE_BUY_ERROR_IQ']="You do not have enough IQ to buy that many sets of power. You need ";
$lang['MINE_BUY_ERROR_IQ1']="yet, you only have ";
$lang['MINE_BUY_SUCCESS']="Congratulations! You've successfully traded ";
$lang['MINE_BUY_SUCCESS1']="sets of mining power.";
$lang['MINE_BUY_INFO']="As of this moment, you can buy";
$lang['MINE_BUY_INFO1']="sets of mining power. Remember, one set of mining power is equal to 10 mining power. You unlock additional sets by leveling up your mining level. As of now, each set will cost you";
$lang['MINE_BUY_INFO2']="IQ each. So, how many sets do you wish to purchase?";
$lang['MINE_BUY_BTN']="Purchase Power Sets";
$lang['MINE_DO_ERROR']="Invalid mining spot.";
$lang['MINE_DO_ERROR1']="You are trying to mine at a spot that does not exist.";
$lang['MINE_DO_ERROR2']="Your mining level is too low to mine here. You need to be, at minimum, mining level";
$lang['MINE_DO_ERROR3']="You can only mine at a mining spot if you're in the same location.";
$lang['MINE_DO_ERROR4']="Your IQ level is too low to mine here. You need to have, at minimum, ";
$lang['MINE_DO_ERROR5']="You do not have enough mining power to mine here. You need to have at least";
$lang['MINE_DO_ERROR6']="You do not have the required pickaxe to mine here. Come back when you have at least one";
$lang['MINE_DO_FAIL']="While mining away, you strike a gas pocket and ignite the whole mine. You're found later, barely breathing.";
$lang['MINE_DO_FAIL1']="You and another miner get into an argument over who saw this piece of ore first. Talking becomes yelling, and yelling becomes pushing, push goes to shove, and the next thing you know, you and him are both fighting on the ground. The guards nearby see this and arrest you both.";
$lang['MINE_DO_FAIL2']="How unlucky. Your mining attempts proved unsuccessful.";
$lang['MINE_DO_SUCC']="You struck a piece of rock to reveal a large vein of ore. After a few minutes of carefully excavating, you managed to obtain ";
$lang['MINE_DO_SUCC1']=" from this vein.";
$lang['MINE_DO_SUCC2']="While mining away, you managed to expertly mine a piece of ";
$lang['MINE_DO_BTN1']="Mine Again";
$lang['MINE_DO_BTN']="Go Back";

//Staff mining
$lang['STAFF_MINE_TITLE']="Mining Panel";
$lang['STAFF_MINE_ADD_ERROR']="None of the inputs on the previous form can remain empty. Go back and try again.";
$lang['STAFF_MINE_ADD_ERROR1']="The minimum mining level for this mine must be at least 1.";
$lang['STAFF_MINE_ADD_ERROR2']="The minimum output for the item outputs cannot be larger or equal to its maximum.";
$lang['STAFF_MINE_ADD_ERROR3']="The city you chose for the mine to be located in does not exist. Check and try again.";
$lang['STAFF_MINE_ADD_ERROR4']="The item you chose for the mine's pickaxe does not exist. Check your source and try again.";
$lang['STAFF_MINE_ADD_ERROR5']="The item you chose for the mine's Output #1 does not exist. Check your source and try again.";
$lang['STAFF_MINE_ADD_ERROR6']="The item you chose for the mine's Output #2 does not exist. Check your source and try again.";
$lang['STAFF_MINE_ADD_ERROR7']="The item you chose for the mine's Output #3 does not exist. Check your source and try again.";
$lang['STAFF_MINE_ADD_ERROR8']="The item you chose for the mine's gem does not exist. Check your source and try again.";
$lang['STAFF_MINE_ADD_SUCCESS']="You've successfully created a mine.";
$lang['STAFF_MINE_ADD_FRMINFO']="Use this form to add a mine to the game. The mine's name will be based loosely on the city its placed in.";
$lang['STAFF_MINE_FORM_LOCATION']="Mine's Location";
$lang['STAFF_MINE_FORM_LVL']="Minimum Mining Level";
$lang['STAFF_MINE_FORM_IQ']="Minimum IQ Required";
$lang['STAFF_MINE_FORM_PEPA']="Power Exhaust / Attempt";
$lang['STAFF_MINE_FORM_PICK']="Required Pickaxe";
$lang['STAFF_MINE_FORM_OP1']="Item #1";
$lang['STAFF_MINE_FORM_OP2']="Item #2";
$lang['STAFF_MINE_FORM_OP3']="Item #3";
$lang['STAFF_MINE_FORM_GEM']="Gem Item";
$lang['STAFF_MINE_FORM_OP1MIN']="Item #1 Minimum Output";
$lang['STAFF_MINE_FORM_OP2MIN']="Item #2 Minimum Output";
$lang['STAFF_MINE_FORM_OP3MIN']="Item #3 Minimum Output";
$lang['STAFF_MINE_FORM_OP1MAX']="Item #1 Maximum Output";
$lang['STAFF_MINE_FORM_OP2MAX']="Item #2 Maximum Output";
$lang['STAFF_MINE_FORM_OP3MAX']="Item #3 Maximum Output";
$lang['STAFF_MINE_EDIT1']="Select a mine to change.";
$lang['STAFF_MINE_EDIT2']="Editing an existing mine...";
$lang['STAFF_MINE_ADD_BTN']="Create Mine";
$lang['STAFF_MINE_EDIT_BTN']="Alter Mine";
$lang['STAFF_MINE_EDIT_SUCCESS']="The mine has been successfully edited.";
$lang['STAFF_MINE_EDIT_ERR']="You've selected a non-existent mine. Check your source and try again.";
$lang['STAFF_MINE_DEL_SUCCESS']="You've successfully deleted a mine";
$lang['STAFF_MINE_DEL1']="Select a mine to delete.";
$lang['STAFF_MINE_DEL_BTN']="Delete Mine! (No Prompt, Be Sure!)";

//Announcements
$lang['ANNOUNCEMENTS_TIME']="Time Posted";
$lang['ANNOUNCEMENTS_TEXT']="Announcement Text";
$lang['ANNOUNCEMENTS_READ']="Read";
$lang['ANNOUNCEMENTS_UNREAD']="Unread";
$lang['ANNOUNCEMENTS_POSTED']="Posted By:";

//Dungeon and Infirmary
$lang['DUNGINFIRM_TITLE']="Dungeon";
$lang['DUNGINFIRM_TITLE1']="Infirmary";
$lang['DUNGINFIRM_INFO']="There are currently";
$lang['DUNGINFIRM_INFO1']="players in the dungeon.";
$lang['DUNGINFIRM_INFO2']="players in the infirmary.";
$lang['DUNGINFIRM_TD1']="User / User ID";
$lang['DUNGINFIRM_TD2']="Reason";
$lang['DUNGINFIRM_TD3']="Check-in Time";
$lang['DUNGINFIRM_TD4']="Check-out Time";
$lang['DUNGINFIRM_TD5']="Actions";
$lang['DUNGINFIRM_ACC']="Bail";
$lang['DUNGINFIRM_ACC1']="Bust";
$lang['DUNGINFIRM_ACC2']="Heal";

//Staff Index
$lang['STAFF_IDX_TITLE']="Staff Panel";
$lang['STAFF_IDX_PHP']="PHP Version";
$lang['STAFF_IDX_DB']="Database Version";
$lang['STAFF_IDX_CENGINE']="Chivalry Engine Version";
$lang['STAFF_IDX_CE_UP']="Chivalry Engine Update Checker";
$lang['STAFF_IDX_API']="API Version";
$lang['STAFF_IDX_IFRAME']="My apologies, but your browser does not support iframes that are needed to use this update checker.";
$lang['STAFF_IDX_ADMIN_TITLE']="Admin Actions";
$lang['STAFF_IDX_ADMIN_LI']="Admin";
$lang['STAFF_IDX_ADMIN_LI1']="Modules";
$lang['STAFF_IDX_ADMIN_LI2']="Users";
$lang['STAFF_IDX_ADMIN_LI3']="Items";
$lang['STAFF_IDX_ADMIN_LI4']="Shops";
$lang['STAFF_IDX_ADMIN_LI5']="Academy";
$lang['STAFF_IDX_ADMIN_LI6']="NPCs";
$lang['STAFF_IDX_ADMIN_LI7']="Jobs";
$lang['STAFF_IDX_ADMIN_LI8']="Polls";
$lang['STAFF_IDX_ADMIN_LI9']="Towns";
$lang['STAFF_IDX_ADMIN_LI10']="Estates";
$lang['STAFF_IDX_ADMIN_LI11']="Academy";
$lang['STAFF_IDX_ADMIN_TAB1']="Game Settings";
$lang['STAFF_IDX_ADMIN_TAB2']="Create an Announcement";
$lang['STAFF_IDX_ADMIN_TAB3']="Game Diagnostics";
$lang['STAFF_IDX_ADMIN_TAB4']="Refresh Users";
$lang['STAFF_IDX_ADMIN_TAB5']="View Error Log";
$lang['STAFF_IDX_ADMIN_TAB6']="Set User Level";
$lang['STAFF_IDX_ADMIN_TAB7']="Add VIP Pack";
$lang['STAFF_IDX_ADMIN_TAB8']="Delete VIP Pack";
$lang['STAFF_IDX_MODULES_TAB1']="Crimes";
$lang['STAFF_IDX_USERS_TAB1']="Create User";
$lang['STAFF_IDX_USERS_TAB2']="Edit User";
$lang['STAFF_IDX_USERS_TAB3']="Delete User";
$lang['STAFF_IDX_USERS_TAB4']="Force Logout User";
$lang['STAFF_IDX_USERS_TAB5']="Change User Password";
$lang['STAFF_IDX_ITEMS_TAB1']="Create Item Group";
$lang['STAFF_IDX_ITEMS_TAB2']="Create Item";
$lang['STAFF_IDX_ITEMS_TAB3']="Delete Item";
$lang['STAFF_IDX_ITEMS_TAB4']="Edit Item";
$lang['STAFF_IDX_ITEMS_TAB5']="Give Item to User";
$lang['STAFF_IDX_SHOPS_TAB1']="Create Shop";
$lang['STAFF_IDX_SHOPS_TAB2']="Delete Shop";
$lang['STAFF_IDX_SHOPS_TAB3']="Add Stock to Shop";
$lang['STAFF_IDX_NPC_TAB1']="Add NPC Bot";
$lang['STAFF_IDX_NPC_TAB2']="Delete NPC Bot";
$lang['STAFF_IDX_ASSIST_TITLE']="Assistant Actions";
$lang['STAFF_IDX_ASSIST_LI']="Game Logs";
$lang['STAFF_IDX_ASSIST_LI1']="Permissions";
$lang['STAFF_IDX_ASSIST_LI2']="Mining";
$lang['STAFF_IDX_LOGS_TAB1']="General Logs";
$lang['STAFF_IDX_LOGS_TAB2']="User Logs";
$lang['STAFF_IDX_LOGS_TAB3']="Training Logs";
$lang['STAFF_IDX_LOGS_TAB4']="Attack Logs";
$lang['STAFF_IDX_LOGS_TAB5']="Login Logs";
$lang['STAFF_IDX_LOGS_TAB6']="Equipment Logs";
$lang['STAFF_IDX_LOGS_TAB7']="Banking Logs";
$lang['STAFF_IDX_LOGS_TAB8']="Criminal Logs";
$lang['STAFF_IDX_LOGS_TAB9']="Item Using Log";
$lang['STAFF_IDX_LOGS_TAB10']="Item Buying Logs";
$lang['STAFF_IDX_LOGS_TAB11']="Item Market Logs";
$lang['STAFF_IDX_LOGS_TAB12']="Staff Logs";
$lang['STAFF_IDX_LOGS_TAB13']="Travel Logs";
$lang['STAFF_IDX_LOGS_TAB14']="Verification Logs";
$lang['STAFF_IDX_LOGS_TAB15']="Spy Attempt Logs";
$lang['STAFF_IDX_LOGS_TAB16']="Gambling Logs";
$lang['STAFF_IDX_LOGS_TAB17']="Item Selling Logs";
$lang['STAFF_IDX_LOGS_TAB18']="Fedjail Logs";
$lang['STAFF_IDX_LOGS_TAB19']="Poke Logs";
$lang['STAFF_IDX_LOGS_TAB20']="Guild Logs";
$lang['STAFF_IDX_LOGS_TAB21']="Guild Vault Logs";
$lang['STAFF_IDX_LOGS_TAB22']="Leveling Logs";
$lang['STAFF_IDX_LOGS_TAB23']="Temple Logs";
$lang['STAFF_IDX_LOGS_TAB24']="{$lang['INDEX_SECCURR']} Market Logs";
$lang['STAFF_IDX_LOGS_TAB25']="Mining Logs";
$lang['STAFF_IDX_LOGS_TAB26']="Mail Logs";
$lang['STAFF_IDX_LOGS_TAB27']="Forum Warn Logs";
$lang['STAFF_IDX_LOGS_TAB28']="Forum Ban Logs";
$lang['STAFF_IDX_LOGS_TAB29']="Donation Logs";
$lang['STAFF_IDX_PERM_TAB1']="View Permissions";
$lang['STAFF_IDX_PERM_TAB2']="Reset Permissions";
$lang['STAFF_IDX_PERM_TAB3']="Edit Permissions";
$lang['STAFF_IDX_MINE_TAB1']="Add Mine";
$lang['STAFF_IDX_MINE_TAB2']="Edit Mine";
$lang['STAFF_IDX_MINE_TAB3']="Delete Mine";
$lang['STAFF_IDX_FM_TITLE']="Forum Moderator Actions";
$lang['STAFF_IDX_FM_LI']="Punishments";
$lang['STAFF_IDX_FM_LI1']="Forums";
$lang['STAFF_IDX_ACTIONS']="Last 15 Staff Actions";
$lang['STAFF_IDX_ACTIONS_TH']="Time";
$lang['STAFF_IDX_ACTIONS_TH1']="Staff Member";
$lang['STAFF_IDX_ACTIONS_TH2']="Log Text";
$lang['STAFF_IDX_ACTIONS_TH3']="IP";
$lang['STAFF_IDX_SMELT_TAB1']="Add Smelting Recipe";
$lang['STAFF_IDX_SMELT_TAB2']="Delete Smelting Recipe";
$lang['STAFF_IDX_SMELT_LIST']="Smelting";
$lang['STAFF_IDX_ACADEMY_ADD']="Create Academy";
$lang['STAFF_IDX_ACADEMY_DEL']="Delete Academy";

//User List
$lang['USERLIST_TITLE']="Userlist";
$lang['USERLIST_PAGE']="Pages";
$lang['USERLIST_ORDERBY']="Order By";
$lang['USERLIST_ORDER1']="User ID";
$lang['USERLIST_ORDER2']="Name";
$lang['USERLIST_ORDER3']="Level";
$lang['USERLIST_ORDER4']="{$lang['INDEX_PRIMCURR']}";
$lang['USERLIST_ORDER5']="Ascending";
$lang['USERLIST_ORDER6']="Descending";
$lang['USERLIST_TH1']="Gender";
$lang['USERLIST_TH2']="Active?";

//Stats Page
$lang['STATS_TITLE']="Statistics Center";
$lang['STATS_CHART']="User Operating Systems";
$lang['STATS_CHART1']="Gender Ratio";
$lang['STATS_CHART2']="Class Ratio";
$lang['STATS_CHART3']="User Browser Choice";
$lang['STATS_TH']="Statistic";
$lang['STATS_TH1']="Statistic Value";
$lang['STATS_TD']="Register Players";
$lang['STATS_TD1']="{$lang['INDEX_PRIMCURR']} Withdrawn";
$lang['STATS_TD2']="{$lang['INDEX_PRIMCURR']} in Banks";
$lang['STATS_TD3']="Total {$lang['INDEX_PRIMCURR']}";
$lang['STATS_TD4']="{$lang['INDEX_SECCURR']} in Circulation";
$lang['STATS_TD5']="{$lang['INDEX_PRIMCURR']} / Player (Average)";
$lang['STATS_TD6']="{$lang['INDEX_SECCURR']} / Player (Average)";
$lang['STATS_TD7']="Bank Balance / Player (Average)";
$lang['STATS_TD8']="Registered Guilds";

//Staff List
$lang['STAFFLIST_ADMIN']="Admins";
$lang['STAFFLIST_LS']="Last Seen";
$lang['STAFFLIST_CONTACT']="Contact";
$lang['STAFFLIST_ASSIST']="Assistants";
$lang['STAFFLIST_MOD']="Forum Moderators";

//Timezone Change
$lang['TZ_TITLE']="Changing Timezone";
$lang['TZ_BTN']="Change Timezone";
$lang['TZ_SUCC']="You have successfully updated your timezone settings.";
$lang['TZ_FAIL']="You have specified an invalid timezone setting.";
$lang['TZ_INFO']="Here, you may change your timezone. This will change all dates on the game for you. This won't speed up any processes. The default timezone is <u>(GMT) Greenwich Mean Time</u>. All game-wide announcements and features will be based on this timezone.";

//Newspaper
$lang['NP_TITLE']="Newspaper";
$lang['NP_AD']="Buy an ad";
$lang['NP_ERROR']="There doesn't appear to be any newspaper ads. Perhaps you should <a href='?action=buyad'>buy</a> and list one?";
$lang['NP_ADINFO']="Ad Info";
$lang['NP_ADTEXT']="Ad Text";
$lang['NP_ADINFO1']="Posted by";
$lang['NP_ADSTRT']="Start Date";
$lang['NP_ADEND']="End Date";
$lang['NP_BUY']="Buying an Ad";
$lang['NP_BUY_REMINDER']="Remember, buying an add is subject to the game rules. If you post something here that will break a game rule, you will be warned and your ad will be removed. If you find someone abusing the news paper, please let an admin know immediately!";
$lang['NP_BUY_TD1']="Initial Ad Cost";
$lang['NP_BUY_TD2']="Ad Runtime";
$lang['NP_BUY_TD3']="Ad Text";
$lang['NP_BUY_TD4']="Total Ad Cost";
$lang['NP_BUY_TD5']="A higher number will rank you higher on the ad list.";
$lang['NP_BUY_TD6']="Each day will add 1,250 {$lang['INDEX_PRIMCURR']} to your cost.";
$lang['NP_BUY_TD7']="Each character is worth 5 {$lang['INDEX_PRIMCURR']}.";
$lang['NP_BUY_BTN']="Place Ad";

//Smelting
$lang['SMELT_HOME']="Smeltery";
$lang['SMELT_TH']="Output Item";
$lang['SMELT_TH1']="Required Items x Quantity";
$lang['SMELT_TH2']="Action";
$lang['SMELT_DO']="Smelt Item";
$lang['SMELT_DONT']="Cannot craft";
$lang['SMELT_ERR']="You are trying to create an item with a non-existent smelting recipe.";
$lang['SMELT_ERR1']="You're missing one or more items required for this smelting recipe.";
$lang['SMELT_SUCC']="You've began creating your item. It'll be given to you shortly.";
$lang['SMELT_SUCC1']="You have successfully smelted this item.";

//Staff Smelting
$lang['STAFF_SMELT_HOME']="Staff Smeltery";
$lang['STAFF_SMELT_ADD_TH']="Value";
$lang['STAFF_SMELT_ADD_TH1']="Input";
$lang['STAFF_SMELT_ADD_TH2']="Smelted Item";
$lang['STAFF_SMELT_ADD_TH3']="Time to Complete";
$lang['STAFF_SMELT_ADD_TH4']="Item Required";
$lang['STAFF_SMELT_ADD_TH5']="Smelted Item Quantity";
$lang['STAFF_SMELT_ADD_TH6']="Item Required Quantity";
$lang['STAFF_SMELT_ADD_SELECT1']="Instantly";
$lang['STAFF_SMELT_ADD_SELECT2']="Seconds";
$lang['STAFF_SMELT_ADD_SELECT3']="Minutes";
$lang['STAFF_SMELT_ADD_SELECT4']="Hours";
$lang['STAFF_SMELT_ADD_SELECT5']="Days";
$lang['STAFF_SMELT_ADD_BTN']="Add Required Item";
$lang['STAFF_SMELT_ADD_BTN2']="Remove Required Item";
$lang['STAFF_SMELT_ADD_BTN3']="Add Smelted Item";
$lang['STAFF_SMELT_ADD_SUCC']="Smelting recipe has been added successfully.";
$lang['STAFF_SMELT_ADD_FAIL']="Missing a required input. Go back and try again.";
$lang['STAFF_SMELT_DEL_FORM']="Use this form to delete a smelting recipe.";
$lang['STAFF_SMELT_DEL_TH']="Smelting Recipe";
$lang['STAFF_SMELT_DEL_BTN']="Delete Recipe";
$lang['STAFF_SMELT_DEL_SUCC']="Smelting recipe has been successfully removed from the game.";

//Inventory
$lang['INVENT_EQUIPPED']="Your Equipment";
$lang['INVENT_ITEMS']="Your Items";
$lang['INVENT_ITEMS_INFO']="Your inventory is listed below.";
$lang['INVENT_UNEQUIP']="Unequip";
$lang['INVENT_NOPRIM']="You do not have a primary weapon equipped at this time.";
$lang['INVENT_NOSECC']="You do not have a secondary weapon equipped at this time.";
$lang['INVENT_NOARMOR']="You do not have an armor equipped at this time.";
$lang['INVENT_ITMNQTY']="Item Name (Qty)";
$lang['INVENT_ITMNCOST']="Item Cost (Total)";
$lang['INVENT_ITMNUSE']="Item Actions";
$lang['INVENT_ITMNUSE1']="Send";
$lang['INVENT_ITMNUSE2']="Sell";
$lang['INVENT_ITMNUSE3']="Add to Market";
$lang['INVENT_ITMNUSE4']="Use";
$lang['INVENT_ITMNUSE5']="Equip Weapon";
$lang['INVENT_ITMNUSE6']="Equip Armor";

//Authenticate
$lang['AUTH_ERROR1']="You have exceeded the maximum of times you can fail to login within the past day. Please try again in 24 hours.";
$lang['AUTH_ERROR2']="You have exceeded the maximum of times you can fail to login within the past hour. Please try again in an hour.";
$lang['AUTH_ERROR3']="You have exceeded the maximum of times you can fail to login within the past 15 minutes. Please try again in 15 minuntes.";
$lang['AUTH_ERROR4']="You left the login form empty. Go back and try again.";
$lang['AUTH_ERROR5']="Your account has been temporarily locked. Please try again in 24 hours.";
$lang['AUTH_ERROR6']="Your account has been temporarily locked. Please try again in an hour.";
$lang['AUTH_ERROR7']="Your account has been temporarily locked. Please try again in 15 minutes.";
$lang['AUTH_ERROR8']="Incorrect email and password combination.";
$lang['AUTH_ERROR9']="CSRF verifcation failed. Please login quickly.";

//Header
$lang['HDR_JS']="You need to enable Javascript for this game to work efficiently.";
$lang['HDR_REKT']="Your account may be broken. Please email help@";
$lang['HDR_REKT1']="stating your username and User ID.";
$lang['HDR_B2G']="Back to Game";
$lang['HDR_IPREKT']="We apologize, but your IP Address has been banned.";

//Password Reset Form
$lang['PWR_INFO']="Please enter the email adress tied to your account so we can send information on how to reset your password. Please be sure to check your junk folder.";
$lang['PWR_SUCC']="Thank you for submitting the form. If there's an account for the specified email address, it'll be email with a link to start the password reset process. This link will expire in 30 minutes.";
$lang['PWR_ERR']="Invalid or non-existent recovery token specified.";
$lang['PWR_SUCC1']="We've email your new password to you. Check your email and/or junk folder for it.";

//Script Errors
$lang['SCRIPT_ERR']="The email you specified is already taken.";
$lang['SCRIPT_ERR1']="The email you specified is in an incorrect format. Valid email addresses are written using @domain.com";
$lang['SCRIPT_ERR2']="You must enter an email address!";
$lang['SCRIPT_ERR3']="You need to enter an username.";
$lang['SCRIPT_ERR4']="The username you entered is too short.";
$lang['SCRIPT_ERR5']="The username you entered is too long.";
$lang['SCRIPT_ERR6']="The username you entered is already in use.";

//API
$lang['API_ERROR']="You are trying to view and/or alter a sensitive field, and we've blocked you from doing this.";

//Staff User
$lang['SCU_UL']="User Level";
$lang['SCU_UL1']="NPC";
$lang['SCU_UL2']="Member";
$lang['SCU_UL3']="Admin";
$lang['SCU_UL4']="Forum Moderator";
$lang['SCU_UL5']="Assistant";
$lang['SCU_UL6']="Web Developer";
$lang['SCU_BI']="Basic Information";
$lang['SCU_CU']="Creating A User";
$lang['SCU_INFO']="Fill out this form to create a user in-game.";
$lang['SCU_SEX']="Male";
$lang['SCU_SEX1']="Female";
$lang['SCU_CLASS']="Warrior";
$lang['SCU_CLASS1']="Rogue";
$lang['SCU_CLASS2']="Defender";
$lang['SCU_STAT']="Stats";
$lang['SCU_BTN']="Create User";
$lang['SCU_ERR']="The email you specified is invalid or does not exist.";
$lang['SCU_ERR1']="The gender specified is invalid or does not exist.";
$lang['SCU_ERR2']="The class specified is invalid or does not exist.";
$lang['SCU_ERR3']="The user level specified is invalid or does not exist.";
$lang['SCU_ERR4']="The user name you chose must be at least 3 characters long, and at most, 20.";
$lang['SCU_ERR5']="The weapon(s) you chose cannot be equipped, or does not exist.";
$lang['SCU_ERR6']="The armor you chose cannot be equipped, or does not exist.";
$lang['SCU_ERR7']="The town you chose cannot be visited, or does not exist.";
$lang['SCU_ERR8']="The username you input is already in use.";
$lang['SCU_ERR9']="The email address you input is already in use.";
$lang['SCU_ERR10']="You either didn't enter a password, or didn't confirm it.";
$lang['SCU_ERR11']="The passwords you inputted do not match.";
$lang['SCU_SUC']="You have successfully created a user!";
$lang['SCU_OTHER']="Other";
$lang['SEU_BTN']="Edit User";

//Dungeon Bust/Bail and Infirmary Heal
$lang['DUNG_BAILERR']='Invalid user specified.';
$lang['DUNG_BAILERR1']='Player cannot be bailed from the dungeon as they are not in the dungeon.';
$lang['DUNG_BAILERR2']="You do not have enough {$lang['INDEX_PRIMCURR']} to bail out this user. You need at least";
$lang['DUNG_BAILSUCC']="Player has been successfully bailed out.";
$lang['DUNG_BUSTERR']='Player cannot be broken out of the dungeon as they are not in the dungeon.';
$lang['DUNG_BUSTERR1']="You cannot bust others out of the dungeon when you're in the dungeon yourself.";
$lang['DUNG_BUSTERR2']="You need at least 10% {$lang['INDEX_BRAVE']} to bust someone out of the dungeon.";
$lang['DUNG_BUSTERR3']="You need at least 25% {$lang['INDEX_WILL']} to bust someone out of the dungeon.";
$lang['DUNG_BUSTERR4']="While trying to bust out your friend, the dungeon master saw you and threw you into a cell.";
$lang['DUNG_BUSTSUCC']="You ahve successfully busted your friend out of jail, and got a little {$lang['INDEX_EXP']} for it too!";
$lang['DUNG_HEALERR1']='Player cannot be healed from the infirmary as they are currently not checked in.';
$lang['DUNG_HEALERR2']="You do not have enough {$lang['INDEX_SECCURR']} to heal out this user. You need at least";
$lang['DUNG_HEALSUCC']="You have successfully healed this player out of the infirmary.";

//Temple
$lang['TEMPLE_TITLE']="Temple of Fortune";
$lang['TEMPLE_INTRO']="Welcome to the Temple of Fortune. Here you may spend your {$lang['INDEX_SECCURR']} as you see fit!";
$lang['TEMPLE_ENERGY']="Refill {$lang['INDEX_ENERGY']} - ";
$lang['TEMPLE_BRAVE']="Refill {$lang['INDEX_BRAVE']} - ";
$lang['TEMPLE_WILL']="Refill {$lang['INDEX_WILL']} - ";
$lang['TEMPLE_IQ']="Convert to {$lang['GEN_IQ']} - ";
$lang['TEMPLE_CASH']="Convert to {$lang['INDEX_PRIMCURR']}";
$lang['TEMPLE_ENERGY_ERR']="You do not have enough {$lang['INDEX_SECCURR']} to refill your {$lang['INDEX_ENERGY']}.";
$lang['TEMPLE_ENERGY_ERR1']="You already have 100% {$lang['INDEX_ENERGY']}.";
$lang['TEMPLE_ENERGY_SUCC']="You have successfully refilled your {$lang['INDEX_ENERGY']}.";
$lang['TEMPLE_BRAVE_ERR']="You do not have enough {$lang['INDEX_SECCURR']} to refill your {$lang['INDEX_BRAVE']}.";
$lang['TEMPLE_BRAVE_ERR1']="You already have 100% {$lang['INDEX_BRAVE']}.";
$lang['TEMPLE_BRAVE_SUCC']="You have successfully refilled your {$lang['INDEX_BRAVE']} by 5%.";
$lang['TEMPLE_WILL_ERR']="You do not have enough {$lang['INDEX_SECCURR']} to refill your {$lang['INDEX_WILL']}.";
$lang['TEMPLE_WILL_ERR1']="You already have 100% {$lang['INDEX_WILL']}.";
$lang['TEMPLE_WILL_SUCC']="You have successfully refilled your {$lang['INDEX_WILL']} by 5%.";
$lang['TEMPLE_IQ_INFO']="You can trade your {$lang['INDEX_SECCURR']} for {$lang['GEN_IQ']} here at a ratio of";
$lang['TEMPLE_IQ_INFO2']="{$lang['GEN_IQ']} per {$lang['INDEX_SECCURR']}. You currently have ";
$lang['TEMPLE_IQ_TH']="{$lang['INDEX_SECCURR']} To Trade";
$lang['TEMPLE_IQ_BTN']="Trade For {$lang['GEN_IQ']}";
$lang['TEMPLE_IQ_ERR']="Fill out the previous form.";
$lang['TEMPLE_IQ_ERR1']="You do not have enough {$lang['INDEX_SECCURR']}.";
$lang['TEMPLE_IQ_SUCC']="You have successfully traded in ";

//Users Online List
$lang['UOL_TITLE']="Users Online";
$lang['UOL_TH']="Username / User ID";
$lang['UOL_TH1']="Last On";
$lang['UOL_ACT']="5 Minutes";
$lang['UOL_ACT1']="15 Minutes";
$lang['UOL_ACT2']="1 Hour";
$lang['UOL_ACT3']="1 Day";

//Staff Settings
$lang['SS_GAME']="Game Name";
$lang['SS_NAME']="Game Owner";
$lang['SS_REF']="Referral Award";
$lang['SS_ENERGY']="Attack Energy Usage";
$lang['SS_HTTPS']="HTTPS Redirect";
$lang['SS_PW']="Password Effort";
$lang['SS_PP']="Paypal Email";
$lang['SS_FGU']="FraudGuard I/O Username";
$lang['SS_FGP']="FraudGuard I/O Password";
$lang['SS_GRPUB']="reCaptcha Public Key";
$lang['SS_GRPRIV']="reCaptcha Private Key";
$lang['SS_DESC']="Game Description";
$lang['SS_BANKFEE']="Bank Purchase Fee";
$lang['SS_BANKWFEE']="Max Bank Withdraw Fee";
$lang['SS_BANKWPERC']="Bank Withdraw Fee Percent";
$lang['SS_TIMEOUT']="Session Timeout";
$lang['SS_REVALID']="reCaptcha Revalidation Period";
$lang['SS_REVALID1']="5 Minutes";
$lang['SS_REVALID2']="15 Minutes";
$lang['SS_REVALID3']="Hourly";
$lang['SS_REVALID4']="Daily";
$lang['SS_REVALID5']="Never";
$lang['SS_ATT']="Max Moves / Attack Sequence";
$lang['SS_GUILDCOST']="Guild Creation Cost";
$lang['SS_GUILDLVL']="Minimum Level to Create Guild";
$lang['SS_REFILLE']="Energy Refill Cost ({$lang['INDEX_SECCURR']})";
$lang['SS_REFILLB']="Bravery Refill Cost ({$lang['INDEX_SECCURR']})";
$lang['SS_REFILLW']="Will Refill Cost ({$lang['INDEX_SECCURR']})";
$lang['SS_IQ']="{$lang['GEN_IQ']} Per {$lang['INDEX_SECCURR']}";
$lang['SS_BTN']="Update Settings";
$lang['SS_ERR1']="One or more inputs are missing. Go back and try again.";
$lang['SS_SUCC1']="You have successfully updated the game settings.";
$lang['SS_ANNOUNCE']="Use this form to post an announcement to the game. Please be sure you are clear and concise with your wording. Do not spam if possible.";
$lang['SS_ANNOUNCE_BTN']="Post Announcement";
$lang['SS_ANNOUNCE_ERR']="You cannot create an empty announcement.";
$lang['SS_ANNOUNCE_SUCC']="Announcement has been posted successfully.";
$lang['SS_DIAG']="Failed!";
$lang['SS_DIAG1']="Pass!";
$lang['SS_TEST']="Server PHP Version Greater than 5.5.0";
$lang['SS_WRITE']="Server Folder Writable";
$lang['SS_PDO']="PDO Detected";
$lang['SS_MYSQLI']="MySQLi Detected";
$lang['SS_HASH']="Password Hashing Function Detected";
$lang['SS_OPENSSL']="Open SSL Detected";
$lang['SS_CURL']="cURL Detected";
$lang['SS_FOPEN']="FOpen Detected";
$lang['SS_UPDATE']="Update Checker";
$lang['SS_RESTORE']="Press this button to restore your users stats to 100%, and remove them from the infirmary and dungeon.";
$lang['SS_RESTORE_BTN']="Restore Users";
$lang['SS_RESTORE_SUCC']="You've successfully restored your users.";

//Secondary Market
$lang['SMARKET_ADD']="Add Listing";
$lang['SMARKET_TH']="Lister";
$lang['SMARKET_TH1']="Cost";
$lang['SMARKET_TH2']="Actions";
$lang['SMARKET_TD']="Remove";
$lang['SMARKET_TD1']="Buy";
$lang['SMARKET_ERR']="You must specify a listing you wish to buy.";
$lang['SMARKET_ERR1']="You cannot purchase your own offer.";
$lang['SMARKET_ERR2']="The offer you are trying to buy does not exist!";
$lang['SMARKET_BERR']="You must specify an offer you wish to cancel.";
$lang['SMARKET_BERR1']="You are not the owner fo this offer, so you cannot cancel it.";
$lang['SMARKET_BERR2']="The offer you are trying to remove does not exist!";
$lang['SMARKET_ERR3']="You do not have enough {$lang['INDEX_PRIMCURR']} to buy this offer.";
$lang['SMARKET_SUCC']="You have successfully bought this offer!";
$lang['SMARKET_SUCC1']="You have successfully removed your {$lang['INDEX_SECCURR']} offer from the market.";
$lang['SMARKET_SUCC2']="You have successfully added an offer to the {$lang['INDEX_SECCURR']} Market.";
$lang['SMARKET_INFO']="use this form to add {$lang['INDEX_SECCURR']} to the market.";
$lang['SMARKET_TH']="{$lang['INDEX_PRIMCURR']} Each";
$lang['SMARKET_BTN']="Create Offer";
$lang['SMARKET_AERR']="You input some invalid values on the previous form.";
$lang['SMARKET_AERR1']="You do not have that much {$lang['INDEX_SECCURR']} to add to the market.";

//Staff Privledges
$lang['STAFF_PRIV_INFO']="Select a user, then set their permission level.";
$lang['STAFF_PRIV_USER']="User";
$lang['STAFF_PRIV_PRIVLIST']="Privilege";
$lang['STAFF_PRIV_PRIVBTN']="Give Privilege";
$lang['STAFF_PRIV_ERR']="You're trying to give an invalid or unknown privledge. Check your source and try again.";
$lang['STAFF_PRIV_ERR1']="The user you're trying to give privileges to is invalid or doesn't exist.";
$lang['STAFF_PRIV_SUCC']="You have successfully updated this user's privileges to";

//Auction
$lang['AUCTION_TITLE']="Item Auction";
$lang['AUCTION_TH']="Lister";
$lang['AUCTION_TH1']="Item x Qty";
$lang['AUCTION_TH2']="Current Bid";
$lang['AUCTION_TH3']="Current Bidder";
$lang['AUCTION_TH4']="Actions";
$lang['AUCTION_TH5']="Time Remaining";
$lang['AUCTION_ACT']="Remove";
$lang['AUCTION_ACT1']="Bid";

//Staff Unfedjail
$lang['STAFF_UNFED_TITLE']="Remove from Federal Jail";
$lang['STAFF_UNFED_INFO']="Select a user to remove them from the federal jail.";
$lang['STAFF_UNFED_BTN']="Remove from Fedjail";
$lang['STAFF_UNFED_ERR']="The user you're trying to remove from the federal jail isn't even sentenced.";
$lang['STAFF_UNFED_SUCC']="You have removed this user from the federal jail.";

//Staff forum warn
$lang['STAFF_FWARN_TITLE']="Forum Warn";
$lang['STAFF_FWARN_INFO']="Select a user to warn, and then give a reason.";
$lang['STAFF_FWARN_BTN']="Forum Warn";
$lang['STAFF_FWARN_REASON']="Forum Warn";
$lang['STAFF_FWARN_ERR']="The user you are trying to warn does not exist.";
$lang['STAFF_FWARN_ERR1']="You forgot to specify a user and/or a reason. Go back and try again.";
$lang['STAFF_FWARN_SUCC']="You have successfully forum warned this user.";

//Staff IP Search
$lang['STAFF_IP_TITLE']="IP Lookup";
$lang['STAFF_IP_INFO']="Input an IP Address to look up. This will list all the players on that IP Address.";
$lang['STAFF_IP_TH']="IP Address";
$lang['STAFF_IP_BTN']="List Users";
$lang['STAFF_IP_IP']="Invalid or non-existent IP Address specified. Go back and try again.";
$lang['STAFF_IP_HUINFO']="Searching for players with the IP:";
$lang['STAFF_IP_OUTTH']="Username [ID]";
$lang['STAFF_IP_OUTTH1']="Level";
$lang['STAFF_IP_OUTTH2']="Registered";

//Staff Mass Jail
$lang['STAFF_IP_MJ']="Fill out the form to place these users in federal jail with one action.";
$lang['STAFF_IP_MJ_BTN']="Mass Fedjail";
$lang['STAFF_MJ_ERR']="You're missing one or more inputs from the previous form.";
$lang['STAFF_MJ_INFO']="User ID";
$lang['STAFF_MJ_INFO1']="has been placed into the federal jail.";
$lang['STAFF_MJ_SUCC']="You have successfully mass jailed these users.";
$lang['STAFF_MJ_SUCC1']="No users were mass jailed.";

//Staff forum ban
$lang['STAFF_FBAN_TITLE']="Forum Ban Form";
$lang['STAFF_FBAN_INFO']="Forum banning a user will make the player unable to read or post in the in-game forums.";
$lang['STAFF_FBAN_BTN']="Forum Ban User";
$lang['STAFF_FBAN_ERR']="You cannot forum ban a non-existent user.";
$lang['STAFF_FBAN_ERR1']="You cannot forum ban admins. Please remove their staff privileges them first before trying again.";
$lang['STAFF_FBAN_ERR2']="You cannot forum ban a user while they're currently forum banned.";
$lang['STAFF_FBAN_SUCC']="You have successfully forum banned this user.";

//Staff unban forum.
$lang['STAFF_UFBAN_TITLE']="Remove Forum Ban";
$lang['STAFF_UFBAN_INFO']="Select a user to remove their forum ban.";
$lang['STAFF_UFBAN_BTN']="Remove Forum Ban";
$lang['STAFF_UFBAN_ERR']="The user you've selected is not currently forum banned. Check your source and try again.";
$lang['STAFF_UFBAN_SUCC']="This user has been successfully had their forum ban removed.";

//Staff notes
$lang['STAFF_NOTES_ERR']="You need to specific both a user and the information you wish to add to the staff notes.";
$lang['STAFF_NOTES_ERR1']="The user you specified does not exist. Go back and try again.";
$lang['STAFF_NOTES_SUCC']="You have successfully updated this user's staff notes.";

//Russian Roulette
$lang['RUSSIANROULETTE_TITLE'] = "Russian Roulette";
$lang['RUSSIANROULETTE_SELF'] = "Even if you are lonely and want to play a game you CANNOT play by yourself!";
$lang['RUSSIANROULETTE_NO_INVITE'] = "Error: You have not been challenged to russian roulette by";
$lang['RUSSIANROULETTE_INVALID_ACCOUNT'] = "Error: While you have not been challenged by this user. This account does not seem to exist";
$lang['RUSSIANROULETTE_INSUFFICIENT_CURRENCY'] = "Error: You have insufficient currency";
$lang['RUSSIANROULETTE_CHOICE'] = "You have entered russian roulette followed by";
$lang['RUSSIANROULETTE_FIRST'] = ", You sit down and chose to be player one...";
$lang['RUSSIANROULETTE_SECOND'] = ", You sit down and chose to be player two...";
$lang['RUSSIANROULETTE_WON'] = "Once";
$lang['RUSSIANROULETTE_WON2'] = "pulled the trigger on the";
$lang['RUSSIANROULETTE_WON3'] = "attempt the barrel lit off. You emerged with a sum of";
$lang['RUSSIANROULETTE_LOST'] = "You were left off with the";
$lang['RUSSIANROULETTE_LOST2'] = "attempt. Shaking, You pulled the trigger. the barrel lit off and you utterly bound by the cruel fate of the loser leaving you with";
$LANG['RUSSIANROULETTE_LOST3'] = "in the infirmary and losing";
$lang['RUSSIANROULETTE_USER_INSERT'] = "Which UserID do you want to challenge?";
$lang['RUSSIANROULETTE_REWARD_INSERT'] = "How much are YOU willing to pay?";
$lang['RUSSIANROULETTE_SEND'] = "Send Challenge";
$lang['RUSSIANROULETTE_FAILED_FORM'] = "Please insert a UserID!";
$lang['RUSSIANROULETTE_INVALID_ACCOUNT_SEND'] = "The account you are challenging does not exist";
$lang['RUSSIANROULETTE_VALID_ACCOUNT_SEND'] = "You have sent a request to";
$lang['RUSSIANROULETTE_DENIED'] = "You have successfully denied the current russian roulette challenge!";
$lang['RUSSIANROULETTE_SCAM'] = "This player offered the amount and ran off with it! They have been notified so please wait in the meantime!";

//Staff donate
$lang['STAFF_DONATE_TITLE']="Staff VIP Packs";
$lang['STAFF_DONATE_ADD_INFO']="Use this form to add a VIP Pack to the game.";
$lang['STAFF_DONATE_ADD_TH']="VIP Pack Item";
$lang['STAFF_DONATE_ADD_TH1']="VIP Pack Cost";
$lang['STAFF_DONATE_ADD_BTN']="Add Pack";
$lang['STAFF_DONATE_ADD_ERR']="You must select an item you wish to be added to the VIP Pack store.";
$lang['STAFF_DONATE_ADD_ERR2']="You must select a valid cost for this item.";
$lang['STAFF_DONATE_ADD_ERR3']="The VIP Pack Item you chose is invalid or does not exist.";
$lang['STAFF_DONATE_ADD_ERR4']="The VIP Pack Item you chose is already listed on the VIP Pack store.";
$lang['STAFF_DONATE_ADD_SUCC']="Item has been successfully added to the VIP Pack store.";
$lang['STAFF_DONATE_DEL_BTN']="Delete Pack";
$lang['STAFF_DONATE_DEL_INFO']="Use this form to remove a VIP Pack from the game.";
$lang['STAFF_DONATE_DEL_ERR']="You must input a VIP Pack you wish to delete.";
$lang['STAFF_DONATE_DEL_ERR1']="The VIP Pack you chose to delete is invalid or does not exist.";
$lang['STAFF_DONATE_DEL_SUCC']="You have successfully removed this VIP Pack from the VIP Pack Store.";

//Tutorial Information
$lang['TUT_WELCOME']="Welcome to the ";
$lang['TUT_WELCOME1']="Tutorial";
$lang['TUT_WELCOME2']="Our hopes are that this tutorial will help you to better understand our wonderful game. If you are confused by any of the terminology here, please contact a staff member listed <a href='staff.php'>here</a>.";
$lang['TUT_INFO']=", you are entirely free to play as you see fit, so long as you don't break the game rules. You're free to protect the weak, or exploit their shortcomings. Be charitable with your cash, or keep it all to yourself. Declare war on a person, or a whole guild. The choice is yours.";
$lang['TUT_GLOSSARY']="Glossary";
$lang['TUT_PT1']="Basics";
$lang['TUT_PT2']="Navigation";
$lang['TUT_PT3']="Explore";
$lang['TUT_PT4']="Training";
$lang['TUT_PT5']="Combat";
$lang['TUT_PT6']=$lang['EXPLORE_GUILDS'];
$lang['TUT_BASICS']="Our wonderous game is a Text Themed RPG, meaning everything you do is by clicking on links, or by writing out respones. For example, to view your inventory, you would click the Inventory link at the top of the page.";
$lang['TUT_STATS']="Personal Stats";
$lang['TUT_STATS_DETAIL']="There are currently five stats in-game: {$lang['GEN_STR']}, {$lang['GEN_AGL']}, {$lang['GEN_GRD']}, {$lang['GEN_IQ']} and {$lang['GEN_LAB']}. Increasing your {$lang['GEN_STR']} will increase how much damage you can dish out in combat. Increasing {$lang['GEN_AGL']} will increase your chance of one of your strikes connecting with your opponent. Increasing your {$lang['GEN_GRD']} will decrease the damage your opponents do to you. {$lang['GEN_IQ']} and {$lang['GEN_LAB']} are miscellaneous stats used around the game. It's good to have these at a fairly decent level.";
$lang['TUT_PINFO']="Personal Info";
$lang['TUT_PINFO_DETAIL']="If you click the game's name on the top left corner, it'll redirect you to the main index where you can view your personal information. This page shows your {$lang['GEN_STATS']}, {$lang['INDEX_LEVEL']}, {$lang['INDEX_PRIMCURR']}, {$lang['INDEX_SECCURR']}, {$lang['INDEX_VIP']}, {$lang['INDEX_HP']}, {$lang['INDEX_EXP']}, {$lang['INDEX_WILL']}, {$lang['INDEX_BRAVE']} and {$lang['INDEX_ENERGY']}. You can also update your {$lang['INDEX_PN']} here as well. {$lang['INDEX_ENERGY']} is used for training and attacking. {$lang['INDEX_WILL']} effects how much you gain while training, so in turn, a low will level means low gains in the gym. {$lang['INDEX_BRAVE']} is used to commit crimes. The more difficult the crime is, the more brave it'll require. Do note that committing crimes have other requirements to succeed. {$lang['INDEX_EXP']} is the {$lang['GEN_EXP']} you have, and how close you are to leveling up. {$lang['INDEX_HP']} shows how healthy your character is. You lose {$lang['INDEX_HP']} when you receive a hit in combat.";
$lang['TUT_NAVBAR']="Navigation Bar";
$lang['TUT_NAVBAR_DETAIL']="The navigation bar (or dropdown) is your gateway to the game. Clicking on the <b>game name in the top left corner</b> will take you to your Personal Info page. Clicking on <b>Explore</b> will allow you to explore many of the features of the game. (More information on this later!) Clicking <b>Mail</b> will take you to your personal mailbox where you can write and read letters to others. <b>Notifications</b> will take you to your notification box, where game events will be posted if they involve you. Clicking <b>Inventory</b> will allow you to view the items you have in your inventory, along with the gear you have equipped.";
$lang['TUT_NAV']="Being able to view and navigate through our game is important! The top navigation bar (or dropdown, if you're on mobile) is where you will find access to the rest of the game.";
$lang['TUT_NAVDROP']="Navigation Bar Dropdown";
$lang['TUT_NAVDROP_DETAIL']="Clicking the <b>Hello, [Your Name] Dropdown</b> will display more information. Clicking on <b>Profile</b> will take you to your personal profile page. This is what others see when they click on your name in-game. Clicking on <b>Settings</b> will take you to your account settings. Here you can change your display picture, password, username, language, template colors, and forum signature! Clicking <b>Game Rules</b> will show you the rules of the game. It's recommended that you read these over so you know what you can and cannot do. Finally, clicking <b>Logout</b> will terminate your session.";
$lang['TUT_EXPLORE']="Exploring is the best way around the game. The explore page is the highway to all other game features.";
$lang['TUT_SHOPS']="{$lang['EXPLORE_SHOP']}";
$lang['TUT_SHOPS_DETAIL']="Hovering over the <b>{$lang['EXPLORE_SHOP']}</b> category will display the numerous ways you can buy things in the game. <b>{$lang['EXPLORE_LSHOP']}</b> are the game-created shops in your town. You'll find a finite amount of items in these shops. However, if you're wanting something a little more flashy, the <b>{$lang['EXPLORE_IMARKET']}</b> is a market ran exclusively by other players. You'll find all sorts of items for sale here. Note, that players also set their own pricing. The <b>{$lang['EXPLORE_IAUCTION']}</b> is a great place to see if you can pick up some awesome prices on items by bidding on them, eBay style. <b>{$lang['EXPLORE_TRADE']}</b> will allow you to conduct business privately with another player. The <b>{$lang['EXPLORE_SCMARKET']}</b> is an easy way to convert your {$lang['INDEX_SECCURR']} into {$lang['INDEX_PRIMCURR']}, depending on the market demand.";
$lang['TUT_FINANCIAL']="{$lang['EXPLORE_FD']}";
$lang['TUT_FINANCIAL_DETAIL']="Hovering over <b>{$lang['EXPLORE_FD']}</b> will greet you with a category of ways to use your currency. The <b>{$lang['EXPLORE_BANK']}</b> will allow you safely store your {$lang['INDEX_PRIMCURR']}. Storing in the {$lang['EXPLORE_BANK']} will keep your money safe from being robbed from you, and even gain you interest at midnight each night. Clicking <b>{$lang['EXPLORE_ESTATES']}</b> will allow you to view the estates available to purchase. Buying an estate will increase your Maximum {$lang['INDEX_WILL']}, thus improving your gains while training. If you're starting to hate the town you're in, <b>{$lang['EXPLORE_TRAVEL']}</b> is your ticket to other towns. Mind you, towns may have {$lang['INDEX_LEVEL']} requirements, and varying tax levels. The <b>{$lang['EXPLORE_TEMPLE']}</b> is where you can spend your {$lang['INDEX_SECCURR']}. You can refill your {$lang['INDEX_ENERGY']}, {$lang['INDEX_WILL']}, and {$lang['INDEX_BRAVE']} here. You may also purchase {$lang['GEN_IQ']}.";
$lang['TUT_HL']=$lang['EXPLORE_HL'];
$lang['TUT_HL_DETAIL']="You will find that the labor category is where you will spend a lot of your time at. <b>{$lang['EXPLORE_MINE']}</b> is a great place to find riches. Be careful though, as nearby warriors may get jealous of your haul. If you're too careless, you could also ignite a gas pocket, placing yourself into the <b>{$lang['EXPLORE_INFIRM']}</b>. The <b>{$lang['EXPLORE_SMELT']}</b> can be used to smelt your items gained while mining, into better items. Obviously, you will need the required items for this to work. <b>{$lang['EXPLORE_WC']}</b> will allow you to chop trees to sell for cold, hard currency. <b>{$lang['EXPLORE_FARM']}</b> will allow you to channel your inner farmer. You can use the harvested crops to create better foods, or sell for a profit. Finally, the <b>{$lang['EXPLORE_BOTS']}</b> will allow you to quickly attack NPCs. If you mug the NPCs listed, you will be able to get a special item drop. Note that this can only happen once per each bot's cooldown time.";
$lang['TUT_ADMIN']=$lang['EXPLORE_ADMIN'];
$lang['TUT_ADMIN_DETAIL']="This category has no general theme, to be honest. Checking out <b>{$lang['EXPLORE_USERLIST']}</b> will list all the registered users in-game, and allow you to organize them by {$lang['INDEX_LEVEL']}, ID, {$lang['INDEX_PRIMCURR']} or name. Clicking <b>{$lang['UOL_TITLE']}</b> will list the players online in the last 15 minutes. You can customize this to any duration you wish. <b>{$lang['EXPLORE_STAFFLIST']}</b> will list all in-game staff. These players uphold the law and order of the game. If you have any issue, you should contact them! The <b>{$lang['EXPLORE_FED']}</b> is where bad folks go. If you follow the rules, you won't ever have to get locked up. If you get locked up, you will lose access to almost all game features. <b>{$lang['EXPLORE_STATS']}</b> will list your game statistics. There's a lot shown here, so check it out to get an idea! If you suspect a player breaking a rule, use the <b>{$lang['EXPLORE_REPORT']}</b> link to report them. Reports here are anonymous, and won't be used in investigations against players. If you abuse this form, however, you will be dealt with harshly. Finally, <b>{$lang['EXPLORE_ANNOUNCEMENTS']}</b> will show you announcements posted by staff members. A lot of important information or changes will be listed here.";
$lang['TUT_GAMES']=$lang['EXPLORE_GAMES'];
$lang['TUT_GAMES_DETAIL']="We have several games for you to play to keep your mind off the stress of the game. Games inside of a game. Isn't this how you destroy the world? In either case, <b>{$lang['EXPLORE_RR']}</b> is a deadly game that you can challenge your friends to. You point a gun to each other's heads until someone is shot. If you're shot, game over. If that's too high risk for you, how about trying out <b>{$lang['EXPLORE_HILO']}</b>? Simply decide if the next drawn number will be higher or lower than the current number. If you're right, you pocket some extra {$lang['INDEX_PRIMCURR']}. If you want a little more thrill, check out <b>{$lang['EXPLORE_ROULETTE']}</b>! Pick a number, bet on it, and hope you win! Finally, if you want to play a game of chance, how about betting at the <b>{$lang['EXPLORE_SLOTS']}</b>?";
$lang['TUT_GUILDS']=$lang['EXPLORE_GUILDS'];
$lang['TUT_GUILDS_DETAIL']="Hovering over the <b>{$lang['EXPLORE_GUILDS']}</b> section will display links pertaining to guilds. Firstly, if you have joined a guild, <b>{$lang['EXPLORE_YOURGUILD']}</b> will take you to view your guild. However, this is useless if you aren't in a guild. To join a guild, or view possible enemies of your own guild, clicking on <b>{$lang['EXPLORE_GUILDLIST']}</b> will list all the guilds in the game. Clicking on a guild's name will allow you to view detailed information about the guild. If you're curious about guild feuds, however, clicking on <b>{$lang['EXPLORE_WARS']}</b> will list all active guild wars. You will find more about the usefulness of guilds later in the tutorial.";
$lang['TUT_ACT']=$lang['EXPLORE_ACT'];
$lang['TUT_ACT_DETAIL']="Opening the activities section will allow you to view numerous things to do around the game. <br />Firstly, the <b>{$lang['EXPLORE_DUNG']}</b> will list the players in the dungeon. You will be placed here if you are caught committing crimes. <br />The <b>{$lang['EXPLORE_INFIRM']}</b> will show those who are receiving medical treatment. You will be placed here if you sustain excessive injuries.<br /> To get stronger in game, we suggest bulking up at the <b>{$lang['EXPLORE_GYM']}</b>. Here you may train your stats. The importance of these stats are explained in the <a href='#basics'>basics</a>. <br />You cannot get spending cash until you work, so we suggest finding work by clicking on <b>{$lang['EXPLORE_JOB']}</b>. Here you will be paid, based on your rank in the company you wish to work for. Different companies pay different rates, and have different requirements. More information about this later. <br />Visiting the <b>{$lang['EXPLORE_ACADEMY']}</b> will allow you to enroll in a course. Courses cost money, and after the course finishes, you will be awarded will a substantial amount of stats. Courses may have other requirements. <br />If you're wanting to make some quick, but risky money, try chekcing out the <b>{$lang['EXPLORE_CRIMES']}</b>. Here you can commit crimes, which cost {$lang['INDEX_BRAVE']}. There's the chance you will be caught. The success rate is determined by your {$lang['INDEX_LEVEL']}, {$lang['INDEX_WILL']}, and most definitely {$lang['GEN_IQ']}. <br />Finally, clicking <b>{$lang['EXPLORE_TUTORIAL']}</b> will load the game tutorial for you to read. If you are ever confused, view the tutorial. Oh wait... you already are.";
$lang['TUT_PINTER']=$lang['EXPLORE_PINTER'];
$lang['TUT_PINTER_DETAIL']="Opening the {$lang['EXPLORE_PINTER']} section will allow you to view ways you can interact with other players in a public setting. <b>{$lang['EXPLORE_FORUMS']}</b> will allow you to view the in-game forums. You can create your own threads, and reply to others. Remember, don't get upset if someone gives you a hard time, it's all in good fun. Viewing the game's <b>{$lang['EXPLORE_NEWSPAPER']}</b> will allow you to see ads posted by other players. You can, of course, post your own ads. You can write about anything here, awesome enough. Finally, the <b>{$lang['POLL_TITLE']}</b> will be home to the polls created by the staff members, of which, you are free vote in. You can also view the results of the previous polls here as well. Polls are used to get an idea of how other players feel about changes in-game.";
$lang['TUT_TOPTEN']=$lang['EXPLORE_TOPTEN'];
$lang['TUT_TOPTEN_DETAIL']="The {$lang['EXPLORE_TOPTEN']} listing will list the top ten strongest players in the game, in order of total stats. These are the players you want to beat. It's a high honor to be a part of this list. It takes lots of time and dedication to get here. Don't be upset if you can't get here.";
$lang['TUT_REFFERAL']="Referral Link";
$lang['TUT_REFFERAL_DETAIL']="Your referral link is your personal registration link. Giving this to your friends will allow you to receive rewards when your friends register. Post this link anywhere you see fit. Just don't be a dick and spam it on other games.";
$lang['TUT_TRAINING']="Training is a great way to increase your stats. Remember, the higher your stats, the more people you will be able to beat in combat.";
$lang['TUT_GYM']="Basic Training";
$lang['TUT_GYM_DETAIL']="In a nutshell, training involves spending your {$lang['INDEX_WILL']} and {$lang['INDEX_ENERGY']} for stats. To increase your gains while training, you should increase your maximum will. You can do this by buying a new estate. We recommend buying the best estate for your level. If you begin to have very low gains for your level, wait a few minutes for your {$lang['INDEX_WILL']} to refill. If the wait is too much, you can refill your {$lang['INDEX_WILL']} at the <b>{$lang['EXPLORE_TEMPLE']}</b> found on {$lang['MENU_EXPLORE']}.";
$lang['TUT_POWERTRAIN']="Power Training"; 
$lang['TUT_POWERTRAIN_DETAIL']="Power training is the term used to describe when you spend excessive amounts of time, patience and skill training your stats to have a signifcant increase. Power training blows through your resources quickly, so it's a good idea to make sure you have enough {$lang['INDEX_SECCURR']} before you start power training. The idea is to have your {$lang['INDEX_WILL']} maxed out, and train using all your {$lang['INDEX_ENERGY']} in one session, then refilling both your {$lang['INDEX_ENERGY']} and {$lang['INDEX_WILL']} before training again. This is complex, so don't worry if you do not understand how this works. There's also many ways of doing this, so you may end up creating your own style of power training.";
$lang['TUT_GGYM']="Guild Gym";
$lang['TUT_GGYM_DETAIL']="The guild gym becomes available for use when your guild level's level is 3 or above. The guild gym has the advantage of having increased stat output compared to the normal gym. You will gain 2% more stats per guild level, up to a maximum of double your maximum gains.";
$lang['TUT_COMBAT']="To get almost anywhere in our game, you need to fight others in combat. The weak will fall, and the strong will reign supreme. Increasing your {$lang['GEN_AGL']}, {$lang['GEN_STR']}, and {$lang['GEN_GRD']} will increase your chance of success in combat.";
$lang['TUT_ATTACK1']="Robbing";
$lang['TUT_ATTACK1_DETAIL']="After successfully besting your opponent in combat, you can choose to <b>{$lang['ATTACK_FIGHT_OUTCOME1']}</b> them. This will allow you to steal some of their {$lang['INDEX_PRIMCURR']}, and place them into the {$lang['EXPLORE_INFIRM']}. The amount you steal is based upon how much you are able to snatch from their person. Obviously, if they have nothing on their person, you won't get anything.";
$lang['TUT_ATTACK2']="Brutality";
$lang['TUT_ATTACK2_DETAIL']="If the person you've beat has pissed you off, you should <b>{$lang['ATTACK_FIGHT_OUTCOME2']}</b> them up. This will increase their {$lang['EXPLORE_INFIRM']} time. Be careful, though, as they may end up getting a friend involved to do the same back.";
$lang['TUT_ATTACK3']="{$lang['GEN_EXP']}";
$lang['TUT_ATTACK3_DETAIL']="If you are just wanting to level up, <b>{$lang['ATTACK_FIGHT_OUTCOME3']}</b> will allow you to end the fight honorably. You will gain {$lang['INDEX_EXP']}, and your opponent will spend the least amount of time in the {$lang['EXPLORE_INFIRM']} out of all the options. The {$lang['INDEX_EXP']} you gain is based on the {$lang['INDEX_LEVEL']} difference between you and them. Note that you will only receive 33% of the {$lang['INDEX_EXP']} you would have if their stats are not within 90% of your total stats.";
$lang['TUT_ATTACK4']="Losing A Fight";
$lang['TUT_ATTACK4_DETAIL']="Losing a fight happens to all of us. You will lose some {$lang['INDEX_EXP']}, and be placed into the {$lang['EXPLORE_INFIRM']}. Take time to train, then try attacking them again!";
$lang['TUT_ATTACK5']="Guild Warring";
$lang['TUT_ATTACK5_DETAIL']="If you are in a guild, you run the risk of having your guild being warred upon. Wars last 72 hours, and the guild with the most points win the war. To get points, you must be successful in combat against the enemy guild members. Every time you beat an enemy guild member, you gain a single point. If you lose in battle, your enemy wins a single point. After the conclusion of the war, you cannot redeclare on the enemy guild until 7 days later.";
$lang['TUT_GUILD']="{$lang['EXPLORE_GUILDS']} are a greay way for players to coordinate together under one unit. {$lang['EXPLORE_GUILDS']} cost 500,000 {$lang['INDEX_PRIMCURR']} to create. By default, {$lang['EXPLORE_GUILDS']} will only be able top hold 5 members. You can increase this by increasing the guild's level.";

//Staff mass mail
$lang['STAFF_MM_INFO']="Mass Mail";
$lang['STAFF_MM_TABLE']="Use this form to send a mass mail to your game. Do not use this on larger games. Create an announcement instead.";
$lang['STAFF_MM_TH']="Message";
$lang['STAFF_MM_BTN']="Send Mass Mail";
$lang['STAFF_MM_WORKING']="Sending message to";
$lang['STAFF_MM_FAIL']="Message failed to send.";
$lang['STAFF_MM_GOOD']="Message sent successfully.";
$lang['STAFF_MM_END']="messages have been sent successfully.";

//Staff Mass Email
$lang['STAFF_MEM_INFO']="Mass Email";
$lang['STAFF_MEM_TABLE']="Use this form to send a mass email to your players. Do not abuse this, or you'll find your domain blocked on email providers. You can use HTML.";
$lang['STAFF_MEM_BTN']="Send Mass Email";
$lang['STAFF_MEM_WORKING']="Sending Email to";
$lang['STAFF_MEM_FAIL']="Email failed to send.";
$lang['STAFF_MEM_GOOD']="Email sent successfully.";
$lang['STAFF_MEM_END']="Emails have been sent successfully.";

//Staff Ban IP
$lang['STAFF_BANIP_TITLE']="Ban IP";
$lang['STAFF_BANIP_INFO']="Enter an IP you wish to ban.";
$lang['STAFF_BANIP_IP']="IP Address";
$lang['STAFF_BANIP_ERR']="The IP Address you entered is not a valid IP Address.";
$lang['STAFF_BANIP_ERR1']="This IP Address is already banned.";
$lang['STAFF_BANIP_SUCC']="IP Address has been banned successfully.";
$lang['STAFF_UNBANIP_TH']="Link";
$lang['STAFF_UNBANIP_TITLE']="Unban IP";
$lang['STAFF_UNBANIP_ERR']="You are trying to unban a non-existent or invalid IP Address.";
$lang['STAFF_UNBANIP_ERR1']="That IP Address does not to be unbanned, as it was either already unbanned or wasn't banned to begin with.";
$lang['STAFF_UNBANIP_SUCC']="IP Address unbanned successfully.";

//Staff Logs
$lang['STAFF_LOGS_LOGS']="Logs";
$lang['STAFF_LOGS_LOGSLL']="logs.";
$lang['STAFF_LOGS_TIME']="Log Time";
$lang['STAFF_LOGS_PERSON']="User";
$lang['STAFF_LOGS_INFOTH']="Log Information";
$lang['STAFF_LOGS_INFO']="There isn't any logs recorded for the";
$lang['STAFF_LOGS_gambling']="Gambling";
$lang['STAFF_LOGS_login']="Authentication";
$lang['STAFF_LOGS_training']="Training";
$lang['STAFF_LOGS_attacking']="Combat";
$lang['STAFF_LOGS_equip']="Equipment";
$lang['STAFF_LOGS_bank']="Banking";
$lang['STAFF_LOGS_crime']="Criminal";
$lang['STAFF_LOGS_itemuse']="Item Use";
$lang['STAFF_LOGS_itembuy']="Item Buying";
$lang['STAFF_LOGS_itemsell']="Item Selling";
$lang['STAFF_LOGS_imarket']="Item Market";
$lang['STAFF_LOGS_travel']="Travel";
$lang['STAFF_LOGS_verify']="Verification";
$lang['STAFF_LOGS_spy']="Spy Attempt";
$lang['STAFF_LOGS_pokes']="Poke";
$lang['STAFF_LOGS_guilds']="Guild";
$lang['STAFF_LOGS_guild_vault']="Guild Vault";
$lang['STAFF_LOGS_level']="Leveling";
$lang['STAFF_LOGS_temple']="Temple";
$lang['STAFF_LOGS_secmarket']="Secondary Currency Market";
$lang['STAFF_LOGS_mining']="Mining";
$lang['STAFF_LOGS_staff']="Staff";
$lang['STAFF_LOGS_fedjail']="Federal Jail";
$lang['STAFF_LOGS_forumwarn']="Forum Warn";
$lang['STAFF_LOGS_forumban']="Forum Ban";
$lang['STAFF_LOGS_donate']="Donation";
$lang['STAFF_LOGS_ALL']="General Logs";
$lang['STAFF_LOGS_MAIL']="Message Logs";
$lang['STAFF_LOGS_ALL_NONE']="There isn't any logs recorded.";
$lang['STAFF_LOGS_MAIL_NONE']="There hasn't been any messages sent.";
$lang['STAFF_LOGS_MAIL_SEND']="Sender";
$lang['STAFF_LOGS_MAIL_RECEIVE']="Recipient";
$lang['STAFF_LOGS_MAIL_MSG']="Message Contents";
$lang['STAFF_LOGS_USER']="User Specific Logs";
$lang['STAFF_LOGS_USER_ERR']="The user you chose is invalid or non-existent.";
$lang['STAFF_LOGS_USER_ERR1']="This user does not exist, or has yet to do any in-game actions.";

//Hall of Fame
$lang['HOF_TITLE']="Hall of Fame";
$lang['HOF_RANK']="Rank";
$lang['HOF_USER']="User";
$lang['HOF_VALUE']="Value";
$lang['HOF_MINE']="Mining Level";
$lang['HOF_TOTAL']="Total Stats";

//Privacy Policy
$lang['PP_TITLE']="Privacy Policy";
$lang['PP_INTRO']="is seriously commited towards protecting the privacy of its users when processing personal data and considers this for all business practices.";
$lang['PP_INTRO2']="observes the pertinent data protection guidelines. These data protection provisions apply to all services offered on websites operated by"; 
$lang['PP_INTRO3']="as well as to portals and to all online games offered by";
$lang['PP_INTRO4']=" ('Website') but not to pages belonging to other providers to which the website may link. In this respect, the data provision conditions of the relevant provider shall apply.";
$lang['PP_INTRO5']="Here we would like to inform you about the way we collect and use data. By accessing and using the Website, you expressly declare that you approve of the following data protection provisions. At any time, you have the right to revoke any potentially granted approvals to be effective in the future. (Refer to clause 7 'Revocation option').";
$lang['PP_INTRO6']="If you have any questions as to how we use your personal data, please contact us. (refer to clause 8 'Questions on data protection and contact') .";
$lang['PP_SEC1']="1. Capturing and Use of Your Personal Data by";
$lang['PP_SEC11']="1.1 You are anonymous when visiting the website. Any access by a user of the website and any retrieval of a file always collects access data on this process in the protocol file on our server without personal relevance and is stored in our server as follows: Internet Protocol, address / IP address, page from which the file was requested, date, time, browser type and operating system, the page you visited, transferred data quantity, access status (file transferred, file not found, etc.). This data is used to generate statistics which help";
$lang['PP_SEC11_2']="to adapt the services offered to your requirements.";
$lang['PP_SEC12']="1.2 In principle,";
$lang['PP_SEC12_2']="only gains knowledge of, captures and utilises such data which you provide when using the website within the scope of your initial registration and full user registration and, if applicable, when making use of services subject to a charge. Upon initial registration and full user registration, all you need is to give a user name and an email address.";
$lang['PP_SEC13']="1.3 Within the scope of executing the concluded user agreement, in particular with regard to any services subject to a charge which you have selected, you may be required to provide further data, such as for example, full name, address, banking details, credit card numbers, etc. The processing of your requests or your support may also require us to ask for personal data, such as your name, address, email address and telephone number.";
$lang['PP_SEC13_2']="will treat this data confidentially and according to the legal data protection provisions.";
$lang['PP_SEC13_3']="will not pass such information to a third party without your authorization, other than to the extent required to execute the agreement, to process your requests or for your support. You will find more details regarding passing on data to third parties in clause 5 'Passing personal data to third parties'.";
$lang['PP_SEC14']="1.4 Here,";
$lang['PP_SEC14_2']="points out that inventory data, utilisation data and statement data according to Articles 14, 15 of the Telecommunications Act or Article 28 of the Federal Data Protection Act are collected, processed and used and this is done, as far as required, even beyond the fulfilment of an agreement.";
$lang['PP_SEC14_3']="reserves the right to store IP addresses and log files for a reasonable period of time after making use of the website to monitor whether the user complies with the conditions and game rules, in particular to ensure proper processing of the payment that you have authorized. This process especially serves to prevent or clarify any cases of abuse and to allow these data to be appropriately passed on to the investigative authorities in specific instances. For the rest, as far as possible, any other evaluation of the data takes place in an anonymized form. Provided there is no pending concrete prosecution action and no preliminary proceedings have been initiated for abuse, the IP addresses and log files are deleted in full at the end of this deadline.";
$lang['PP_SEC15']="1.5 At its own initiative or at your request,";
$lang['PP_SEC15_2']="can complete, correct or delete incomplete, incorrect and/or outdated personal data which";
$lang['PP_SEC15_3']="stores in connection with the operation of the website.";
$lang['PP_SEC16']="1.6 Within the scope of legal provisions,";
$lang['PP_SEC16_2']="immediately deletes personal data at the authorised person`s request, provided this does not contradict mandatory legal retention obligations.";
$lang['PP_SEC2']="2. Receipt of Information About the Game / Newsletter";
$lang['PP_SEC2_2']="With your registration, you confirm that you agree to receive regular email information about the game (e.g. information about updates, etc.) for which you have initially registered / fully registered. You can, at any time, send ";
$lang['PP_SEC2_3']="a message that you no longer wish to receive such information. In addition, you always have the option to de-register via your account settings directly in the game to stop any further receipt.";
$lang['PP_SEC2_4']="<u><b>You can revoke your approval to use your email address to receive information about the game and/or the newsletter at any time with effect for the future</b></u> (For contact information, refer to clause 7).";
$lang['PP_SEC3']="3. The Use of Cookies and Web Beacons";
$lang['PP_SEC31_INFO']="3.1 Own cookies and web beacons, advertising by third parties, links to other websites";
$lang['PP_SEC31']="In order to design our Internet offer in the most user-friendly manner for you, we, as well as many other companies, also use so-called permanent or temporary cookies and web beacons. Cookies are small text files which are stored on the user's computer and allow for an analysis of how he or she uses the website. This storage, for example, serves to enable an auto-login so that you do not have to identify yourself each time you access the site. No personal identification of the user takes place based on the data stored in the cookie.";
$lang['PP_SEC31_2']="If you do not want cookies to be stored on your computer or wish to be informed of their storage, you can prevent the installation of cookies by a corresponding adjustment to your browser software by selecting the option 'do not accept cookies' in your browser settings. Your browser manufacturer's instructions will give you more details on how this works. However, we would like to point out that by preventing cookies, you may find that you cannot use all of the website's functions to the full extent.";
$lang['PP_SEC321']="3.2.1 Google Analytics";
$lang['PP_SEC321_2']="Google Analytics, a web analysis service from the provider Google Incorporated ('Google'), collects and stores data on the company`s website, enabling the creation of user profiles under pseudonyms. These user profiles serve to analyse visitor behaviour and are evaluated in order to improve and design our offer so as to meet the demands. Cookies can be used for this purpose. You can prevent cookies being installed by making a corresponding adjustment to your browser software; however, we must point out that if you do so, you may not be able to use all the functions on this website to the full extent. The pseudonymous user profiles are not merged with personal data via the pseudonym carrier without an especially granted approval by the affected person. You can, at any time with effect for the future, object to the collection and storing of data for the purpose of web analysis by installing the browser add-on to deactivate Google Analytics. (<a href='http://tools.google.com/dlpage/gaoptout'>https://tools.google.com/dlpage/gaoptout</a>).";
$lang['PP_SEC33']="3.3. Advertising by Third Parties, Links to Other Websites";
$lang['PP_SEC331']="3.3.1 From time to time, the website may also contain advertising by a third party and interactive links to third party Internet appearances for which";
$lang['PP_SEC331_2']="is not responsible. In particular,";
$lang['PP_SEC331_3']="has no influence on the content and design of the linked external pages or the Internet appearances which you reach via these pages. The relevant providers are exclusively responsible for the content and design of these Internet appearances as well as their compliance with the provisions of the Data Protection Act.";
$lang['PP_SEC4']="4. Data security";
$lang['PP_SEC41']="4.1 At all times,";
$lang['PP_SEC41_2']="strives to take precautions, to a reasonable extent, to prevent unauthorised access to your personal data as well as the unauthorised use or falsification of theses data and to minimise the corresponding risks. Nevertheless, the provision of personal data, wheter this is done personally, by telephone or over the Internet, is always associated with risks and no technological system is fully exempt from the possibility of manipulation or sabotage.";
$lang['PP_SEC5']="5. Passing Personal Data to Third Parties";
$lang['PP_SEC51']="5.1";
$lang['PP_SEC51_2']="will in principle only pass your personal and/or billing-related data to third parties, that is to partner companies, other companies cooperating with";
$lang['PP_SEC51_3']=", external service providers and investigative authorities as far as this is required to fulfil the agreement and for the aforementioned purposes, in particular for payment processing as well as to protect other users or as provided by law. Payment processing takes place via external service providers (e.g. credit card companies, banks, PaySafe, Wallie, Clickandbuy, Moneybookers, Paypal, Global Collect). Your protected interests are considered according to the legal provisions. External service providers are obliged to treat your data confidentially and securely and may only use your data as far as this is required to fulfil their duties. We point out that";
$lang['PP_SEC51_4']="may be obliged to pass data on the basis of legal provisions (e.g. passing to investigative authorities). Data are only passed as far as this is required or prescribed by law.";
$lang['PP_SEC5_2']="In the event of payment arrears,";
$lang['PP_SEC5_3']="will, if necessary, instruct a collection company or a lawyer to collect the payable amount. The required data are passed for this purpose and utilised in compliance with all data protection guidelines.";
$lang['PP_SEC52']="5.2 In other instances,";
$lang['PP_SEC52_2']="will only pass your personal data which you provided to";
$lang['PP_SEC52_3']="during the course of your registration and to process your order if you have previously given your express approval.";
$lang['PP_SEC6']="6. Changes";
$lang['PP_SEC6_2']="can change these data protection provisions or change the content of the page or change or block access to these pages at any time.";
$lang['PP_SEC6_3']="will use suitable means to point out such changes.";
$lang['PP_SEC7']="7. Option of Revocation and Data Deletion";
$lang['PP_SEC71']="7.1 If you wish to revoke your approval to send information about the game or the newsletter, please send an in-game message to any in-game staff member (which can be found by visiting the <a href='staff.php'>staff page</a>) by stating your user name and the email address to which the information and/or the newsletter is being sent.";
$lang['PP_SEC72']="7.2 At your request,";
$lang['PP_SEC72_2']="will immediately delete personal data, that is data which include the user`s information about personal and factual circumstances, unless mandatory obligations to preserve records or other obligations contradict such a deletion.";
$lang['PP_SEC8']="8. Questions on Data Protection and Contact / Information, Correction, Deletion and Blocking";
$lang['PP_SEC8_2']="You are always entitled to request information about any of your data that";
$lang['PP_SEC8_3']="has stored, as well as their origin and recipient and the purpose it was stored for. If you have any questions about data protection or if you wish to correct, block or delete your profile or any of your personal data, or if you would like to revoke a granted authorization regarding the use of your customer data or e-mail address, please send an in-game message to any in-game staff member (which can be found by visiting the <a href='staff.php'>staff page</a>) and also provide your username and your e-mail address.";

//Email Opt
$lang['PREF_EMAIL_OPTIN']="Opt-In";
$lang['PREF_EMAIL_OPTOUT']="Opt-Out";
$lang['PREF_EMAIL_INFO']="Use this form to either opt-in or opt-out of emails from the game.";
$lang['PREF_EMAIL']="Opt-Setting";
$lang['PREF_EMAIL_BTN']="Change Email Opt-Setting";
$lang['PREF_EMAIL_ERR']="Email opt setting is invalid. You may only opt-in or opt-out.";
$lang['PREF_EMAIL_SUCC_1']="You have successfully chosen to opt-in to emails sent from the administration about the game.";
$lang['PREF_EMAIL_SUCC_0']="You have successfully chosen to opt-out of emails sent from the administration about the game.";
?>