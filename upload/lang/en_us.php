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

//Send Cash Form
$lang['SCF_POSCASH']="You need to send at least 1 {$lang['INDEX_PRIMCURR']} to use this form.";
$lang['SCF_UNE']="You cannot send {$lang['INDEX_PRIMCURR']} to a non-existent user!";
$lang['SCF_NEC']="You are trying to send more {$lang['INDEX_PRIMCURR']} than you currently have!";
$lang['SCF_SUCCESS']="{$lang['INDEX_PRIMCURR']} sent successfully.";
$lang['SCF_ERR']="You cannot send money to yourself, sorry.";

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

//Staff rules
$lang['STAFF_RULES_ADD_FORM']="Use this form to add rules into the game. Be clear and concise. The more difficult language and terminology you use, the less people may understand.";
$lang['STAFF_RULES_ADD_BTN']="Add Rule";
$lang['STAFF_RULES_ADD_SUBFAIL']="You cannot add a rule an empty rule.";
$lang['STAFF_RULES_ADD_SUBSUCC']="You have successfully created a new rule.";

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

//Staff Change PW
$lang['STAFF_USERS_CP_FORM_INFO']="Use this form to change a user's password.";
$lang['STAFF_USERS_CP_USER']="User";
$lang['STAFF_USERS_CP_FORM_BTN']="Change Password";
$lang['STAFF_USERS_CP_PW']="New Password";
$lang['STAFF_USERS_CP_ERROR']="You cannot change the password for the admin account this way.";
$lang['STAFF_USERS_CP_ERROR1']="You cannot change the password for other admin accounts this way.";
$lang['STAFF_USERS_CP_SUCCESS']="User's password has been changed successfully.";

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

//Staff Privledges
$lang['STAFF_PRIV_INFO']="Select a user, then set their permission level.";
$lang['STAFF_PRIV_USER']="User";
$lang['STAFF_PRIV_PRIVLIST']="Privilege";
$lang['STAFF_PRIV_PRIVBTN']="Give Privilege";
$lang['STAFF_PRIV_ERR']="You're trying to give an invalid or unknown privledge. Check your source and try again.";
$lang['STAFF_PRIV_ERR1']="The user you're trying to give privileges to is invalid or doesn't exist.";
$lang['STAFF_PRIV_SUCC']="You have successfully updated this user's privileges to";

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
?>