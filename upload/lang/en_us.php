<?php
/*
	File: lang/en_us.php
	Created: 6/1/2016 at 6:06PM Eastern Time
	Info: The English language file.
	Author: TheMasterGeneral
	Website: http://mastergeneral156.pcriot.com/
*/
 
$lang = array();
global $ir,$fee,$gain;
//Static
$lang["HEADER_TITLE"] = "Chivalry Engine Unreleased";
 
// Menu
$lang["MENU_EXPLORE"] = "Explore";
$lang["MENU_MAIL"] = "Mail";
$lang["MENU_EVENT"] = "Notifications";
$lang["MENU_INVENTORY"] = "Inventory";
$lang["MENU_OUT"] = "<i><small>Powered with codes by <a href='https://twitter.com/MasterGeneralYT'><font color=gray>TheMasterGeneral</font></a>. Used with permission.</small></i>";
$lang['MENU_PROFILE']='Profile';
$lang['MENU_SETTINGS']='Settings';
$lang['MENU_STAFF']='Staff Panel';
$lang['MENU_LOGOUT']='Log Out';
$lang['MENU_TIN']='Time is now';
$lang['MENU_QE']='queries executed';
$lang['MENU_UNREADMAIL1']='Unread Mail!';
$lang['MENU_UNREADNOTIF']='Unread Notifications!';
$lang['MENU_UNREADANNONCE']='Unread Announcments!';
$lang['MENU_UNREADANNONCE1']='There are';
$lang['MENU_UNREADANNONCE2']='announcements you have yet to read. Read them';
$lang['MENU_UNREADMAIL2']='You have';
$lang['MENU_UNREADMAIL3']='unread messages. Click';
$lang['MENU_UNREADMAIL4']='to read them.';
$lang['MENU_UNREADNOTIF1']='unread notifications. Click';
$lang['MENU_INFIRMARY1']='You are in the infirmary for the next';
$lang['MENU_DUNGEON1']='You are in the dungeon for the next';
$lang['MENU_XPLOST']="By running from the fight, you have lost all your experience!";

// Preferences
$lang["PREF_CPASSWORD"] = "Change Password";
$lang["PREF_WELCOME_1"] = "Greetings there,";
$lang["PREF_WELCOME_2"] = ", and welcome to the Preferences Center. You may view and change information about your account!";
$lang["PREF_CNAME"] = "Change Username";
$lang["PREF_CTIME"] = "Change Timezone";
$lang["PREF_CLANG"] = "Change Language";
$lang["PREF_CPIC"] = "Change Display Picture";

//Username Change
$lang["UNC_TITLE"] = "Changing your username...";
$lang["UNC_INTRO"] = "Here you can change your name that is shown throughout the game. Do not use an innappropriate name or you may find your privledge to change your name removed.";
$lang["PREF_CNAME"] = "Change Username";
$lang["UNC_ERROR_1"] = "You did not even enter a new username! Click ";
$lang["UNC_ERROR_2"] = " to try again";
$lang['UNC_LENGTH_ERROR'] = "Usernames must be, at minimum, three characters in length and, at maximum, twenty characters.";
$lang['UNC_INVALIDCHARCTERS'] = "Usernames can only consist of numbers, letters, underscores and spaces!";
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
$lang['PIC_NOTE']="Please note that this must be externally hosted, <a href='http://www.photobucket.com'>Photobucket</a> is our recommendation.";
$lang['PIC_NOTE2']="Any images that are not 250x250 will be automatically resized.";
$lang['PIC_NEWPIC']="Link to new picture:";
$lang['PIC_TOOBIG']="Picture Too Large!";
$lang['PIC_TOOBIG2']="Your image's filesize is too large. The maximum size an image can be is 1MB. Go back and try again, please.";
$lang['PIC_NOIMAGE']="You specified a URL that is not even an image. Go back and try again, please.";
$lang['PIC_SUCCESS']="You have successfully updated your display picture! It is shown below.";

//Login Page
$lang["LOGIN_REGISTER"] = "Register";
$lang["LOGIN_RULES"] = "Game Rules";
$lang["LOGIN_LOGIN"] = "Login";
$lang["LOGIN_AHA"] = "Already have an account?";
$lang["LOGIN_EMAIL"] = "Email address";
$lang["LOGIN_PASSWORD"] = "Password";
$lang["LOGIN_LWE"] = "Login with Email";
$lang["LOGIN_SIGNIN"] = "Sign In";
$lang["LOGIN_NH"] = "New Here? <a href='register.php'>Join Us</a>!";

//Register
$lang["REG_FORM"] = "Registration";
$lang["REG_USERNAME"] = "Username";
$lang["REG_EMAIL"] = "Email";
$lang["REG_PW"] = "Password";
$lang["REG_CPW"] = "Confirm Password";
$lang["REG_SEX"] = "Gender";
$lang["REG_CLASS"] = "Class";
$lang["REG_REFID"] = "Referral ID";
$lang['REG_WARRIORCLASS']="Warrior Class!";
$lang['REG_ROGUECLASS']="Rogue Class!";
$lang['REG_DEFENDERCLASS']="Defnder Class!";
$lang['REG_NOCLASS']="We need you to select a class, please.";
$lang['REG_ROGUECLASS_INFO']="A rogue fighter has an easier time training agility, however, at the cost of not knowing how to train strength as well. They start the game with more agility, and less strength.";
$lang['REG_DEFENDERCLASS_INFO']="A defending fighter has trained extensively in the art of blocking, thus making them a quick learner of the Guard skill, however, at the cost of being a slow learner at Agility. They start the game with more guard, but less agility.";
$lang['REG_WARRIORCLASS_INFO']="A normal warrior has been trained to use brute strength to win combat situations. Because of this, they learn strength a lot quicker, but do not know how to guard as well. They begin the game with more strength, but less guard than others.";
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
$lang['GEN_NOPERM']="You do not have the proper user level to view this page. If this is wrong, please contact an admin immediately!";

//Gym
$lang['GYM_INFIRM'] = "While you are unconscious, you cannot train! Come back after you are feeling healthy!";
$lang['GYM_DUNG'] = "The guards would normally let you work out, but, what you did was deemed too high of a crime. You cannot train right now...";
$lang['GYM_NEG'] = "Not Enough Energy!";
$lang['GYM_INVALIDSTAT'] = "You cannot train that stat!";
$lang['GYM_NEG_DETAIL'] = "You do not have enough energy to train that many times. Either wait for your energy to recover, or refill it manually!";

//Explore
$lang['EXPLORE_INTRO']='You begin exploring the town and find a few cool things to keep you occupied...';
$lang['EXPLORE_REF']="That is your referral link. Give it to friends, enemies or just spam it around. You'll receive 25 Secondary Currency upon them joining!";
$lang['EXPLORE_SHOP']="Shops";
$lang['EXPLORE_LSHOP']="Local Shops";
$lang['EXPLORE_POSHOP']="Player-Owned Shops";
$lang['EXPLORE_IMARKET']="Item Market";
$lang['EXPLORE_IAUCTION']="Item Auction";
$lang['EXPLORE_TRADE']="Trading";
$lang['EXPLORE_SCMARKET']="Secondary Currency Market";
$lang['EXPLORE_FD']="Financial";
$lang['EXPLORE_BANK']="Bank";
$lang['EXPLORE_ESTATES']="Estates";
$lang['EXPLORE_HL']="Labor";
$lang['EXPLORE_MINE']="Mining";
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

//Error Details
$lang['ERRDE_EXPLORE']="Since you are in the infirmary, you cannot visit the town!";
$lang['ERRDE_EXPLORE2']="Since you are in the dungeon, you cannot visit the town!";
$lang['ERRDE_PN']="Your personal notepad could not be updated due to the 65,655 character limit.";
$lang['ERROR_MAIL_UNOWNED']='You cannot read this message as it was not sent to you!';
$lang['ERROR_FORUM_VF']="Go back and try again for us, please. We done broke.";

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

//Form Buttons
$lang['FB_PN']="Update Notes";
$lang['FB_PR']="Submit Player Report";

//Player Report
$lang['PR_TITLE']="Player Report";
$lang['PR_INTRO']="Know someone who broke the rules, or is just being unhonorable? This is the place to report them. Report the user just once. Reporting the same user multiple times will slow down the process. If you are found to be abusing the player report system, you will be placed away in federal jail. Information you enter here will remain confidential and will only be read by senior staff members. If you wish to confess to a crime, this is also a great place too.";
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
$lang['MAIL_MSGREAD']='Message Read';
$lang['MAIL_MSGUNREAD']='Message Unread';
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

//Language menu
$lang['LANG_INTRO']='Here you may change your language. This is not saved to your account. This is saved via a cookie. If you change devices or wipe your cookies, you will need to reset your language again. Translations may not be 100% accurate.';
$lang['LANG_BUTTON']='Change Language';
$lang['LANG_UPDATE']='Language Updated!';
$lang['LANG_UPDATE2']='You have successfully updated your language!';

//Notifications page
$lang['NOTIF_TABLE_HEADER1']='Notifcations Info';
$lang['NOTIF_TABLE_HEADER2']='Notifcations Text';
$lang['NOTIF_DELETE_SINGLE']='You have successfully deleted a notification.';
$lang['NOTIF_DELETE_SINGLE_FAIL']='You cannot delete this notification as it either does not exist or does not belong to you.';
$lang['NOTIF_TITLE']='Last fifthteen notifications belonging to you...';
$lang['NOTIF_READ']='Notification Read';
$lang['NOTIF_UNREAD']='Notification unread';
$lang['NOTIF_DELETE']='Delete Notification';

//Bank
$lang['BANK_BUY1']='Open a bank account today, just ';
$lang['BANK_BUYYES']='Sign Me Up!';
$lang['BANK_SUCCESS']="Congratulations, you bought a bank account for";
$lang['BANK_SUCCESS1']='Start Using My Account!';
$lang['BANK_FAIL']="You do not have enough {$lang['INDEX_PRIMCURR']} to buy a bank account. Come back later when you have enough. You need ";
$lang['BANK_HOME']="You currently have ";
$lang['BANK_HOME1']=" in the low-level bank.";
$lang['BANK_HOME2']="At the end of each day, your bank balance will increase by 2%.";
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
$lang['FORUM_POST_EDIT']="Edit Post";
$lang['FORUM_POST_QUOTE']="Quote Post";
$lang['FORUM_POST_DELETE']="Delete Post";
$lang['FORUM_POST_EDIT_1']="This post was last edited by";
$lang['FORUM_NOSIG']="No Signature";
$lang['FORUM_POST_POSTED']="Replied At:";
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

//Send Cash Form
$lang['SCF_POSCASH']="You need to send at least 1 {$lang['INDEX_PRIMCURR']} to use this form.";
$lang['SCF_UNE']="You cannot send {$lang['INDEX_PRIMCURR']} to a non-existent user!";
$lang['SCF_NEC']="You are trying to send more {$lang['INDEX_PRIMCURR']} than you currently have!";
$lang['SCF_SUCCESS']="{$lang['INDEX_PRIMCURR']} sent succuessfully.";

//Profile
$lang['PROFILE_UNF']="We could not find a user with the User ID you entered. You could be receiving this message because the player you are trying to view got deleted. Check your source again!";
$lang['PROFILE_PROFOR']="Profile For";
$lang['PROFILE_LOCATION']="Location:";
$lang['PROFILE_GUILD']="Guild";
$lang['PROFILE_PI']="Phyiscal Information";
$lang['PROFILE_ACTION']="Actions";
$lang['PROFILE_FINANCIAL']="Financial Information";
$lang['PROFILE_STAFF']="Staff Area";
$lang['PROFILE_REGISTERED']="Registered";
$lang['PROFILE_ACTIVE']="Last Active";
$lang['PROFILE_LOGIN']="Last Login";
$lang['PROFILE_AGE']="Age";
$lang['PROFILE_DAYS_OLD']="days old.";
$lang['PROFILE_REF']="Referrals";
$lang['PROFILE_FRI']="Friends";
$lang['PROFILE_ENE']="Enemies";

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
$lang['EQUIP_WEAPON_TITLE']="Equip a Weapon";
$lang['EQUIP_WEAPON_TEXT_FORM_1']="Please select the spot you wish to equip your";
$lang['EQUIP_WEAPON_TEXT_FORM_2']="to. If you're already holding a weapon in the slot you choose, it will be moved back to your inventory.";
$lang['EQUIP_WEAPON_EQUIPAS']="Equip As";
$lang['EQUIP_ARMOR_TITLE']="Equipping Armor";
$lang['EQUIP_ARMOR_TEXT_FORM_1']="You're attempting to equip your ";
$lang['EQUIP_ARMOR_TEXT_FORM_2']="to your armor slot. If you're already wearing armor, it will be moved back to your inventory.";
$lang['EQUIP_NOTARMOR']="The item you are trying to equip cannot be equipped as armor.";
$lang['EQUIP_NOTARMOR_TITLE']="Invalid Armor!";

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
$lang['STAFF_FORUM_DEL_INFO']="Deleting forums are permenant. This will also remove the posts inside them as well.";
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

//Staff Crimes
$lang['STAFF_CRIME_TITLE']="Crimes";
$lang['STAFF_CRIME_MENU_CREATE']="Create Crime";
$lang['STAFF_CRIME_MENU_CREATECG']="Create Crime Group";
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
$lang['STAFF_CRIME_NEW_XP']="Success Experience";
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
$lang['STAFF_USERS_DEL_FORM_1']="You may use this form to delete a user from the game. This action is not reverisble. Be 100% sure.";
$lang['STAFF_USERS_DEL_SUB_SECERROR']="You specified an invalid or non-existent user. Go back and try again.";
$lang['STAFF_USERS_DEL_SUBFORM_CONFIRM']="Please confirm that you wish to delete";
$lang['STAFF_USERS_DEL_SUBFORM_CONFIRM1']=". Once deleted, they will not be able to login from their account anymore.";
$lang['STAFF_USERS_DEL_SUB_INVALID']="You specified a user or command that's invalid.";
$lang['STAFF_USERS_DEL_SUB_FAIL']="User was not deleted.";
$lang['STAFF_USERS_DEL_SUB_SUCC']="User was deleted from the game.";
$lang['STAFF_USERS_FL_SUB_SUCC']="User was successfully logged out from the game.";
$lang['STAFF_USERS_FL_FORM_INFO']="Use this form to have a use auto-logged out when on their next action in game.";
$lang['STAFF_USERS_FL_FORM_BTN']="Force Logout User";

//Criminal Center
$lang['CRIME_TITLE']="Criminal Center";
$lang['CRIME_ERROR_JI']="Only the healthy and free individuals can commit crimes.";
$lang['CRIME_TABLE_CRIME']="Crime";
$lang['CRIME_TABLE_CRIMES']="Crimes";
$lang['CRIME_TABLE_COST']="Cost";
$lang['CRIME_TABLE_COMMIT']="Commit";
$lang['CRIME_COMMIT_INVALID']="You are trying to commit either a non-existent crime, or an unfinished one. Try again, and if the issue persists, please contact an admin.";
$lang['CRIME_COMMIT_BRAVEBAD']="You aren't brave enough to commit this crime at this time. Come back later.";

$lang['ATTACK_START_NOREFRESH']="Refreshing while attacking is a bannable offense. You can lose all your experience for that.";
$lang['ATTACK_START_NOUSER']="You can only attack players specified. Did you use the attack link on the user's profile?";
$lang['ATTACK_START_NOTYOU']="Depressed or not, you cannot attack yourself!";
$lang['ATTACK_START_YOUNOHP']="You need HP to fight someone. Come back when you have more health!";
$lang['ATTACK_START_YOUINFIRM']="How do you expect to fight someone when you're nursing an injurt in the infirmary?";
$lang['ATTACK_START_YOUDUNG']="How do you expect to fight someone when you're serving your debt to society in the dungeon?";
$lang['ATTACK_START_YOUCHICKEN']="Chickeing out from one fight, and running to start another is not an honorable way to play.";
$lang['ATTACK_START_NONUSER']="The person you have a grudge with does not exist. Check your source and try again.";
$lang['ATTACK_START_UNKNOWNERROR']="An unknown error has occured. Go back and try again. If this error persists, contact an admin!";
$lang['ATTACK_START_OPPNOHP']=" is low on HP. Come back when they have more health.";
$lang['ATTACK_START_OPPINFIRM']=" is in the infirmary at the moment. Come back when they're out!";
$lang['ATTACK_START_OPPDUNG']=" is in the dungeon at the moment. Come back when they're out!";
$lang['ATTACK_START_OPPUNATTACK']="This user cannot be attacked by normal means.";
$lang['ATTACK_START_YOUUNATTACK']="A magical force prevents you from attacking anyone.";
$lang['ATTACK_FIGHT_STALEMATE']="Come back when you're stronger. This fight ends in stalemate.";
$lang['ATTACK_FIGHT_LOWENG1']="You do not have enough energy for this fight. You need at least";
$lang['ATTACK_FIGHT_LOWENG2']="%. You only have";
$lang['ATTACK_FIGHT_BUGABUSE']="Abusing game bugs is against the game rules. You're losing your experience and going to the infirmary for this one.";
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
$lang['ATTACK_FIGHT_END6']="You lost this fight and lost some of your experience as a warrior!";
$lang['ATTACK_FIGHT_END7']="Since you are an honorable warrior, you take them to the infirmary entrance. You leave their body there. This increases your experience.";
$lang['ATTACK_FIGHT_END8']="Being a greedy warrior, you take a look at their pockets and grab some of their Primary Currency.";

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
?>