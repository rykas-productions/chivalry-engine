<?php
/*
	Aufrechtzuerhalten.
	Datei: lang / en_us.php
	Erstellt: 06.06.2016 um 06:06 Uhr Eastern Time
	Info: Die englische Sprachdatei.
	Verfasser / in: TheMasterGeneral
	Webseite: https://github.com/MasterGeneral156/chivalry-engine
*/
 
$lang = array();
global $ir, $fee, $gain;
 
// Menü
$lang["MENU_EXPLORE"] = "Entdecken";
$lang["MENU_MAIL"] = "Mail";
$lang["MENU_EVENT"] = "schmerzhaft";
$lang["MENU_INVENTORY"] = "Inventar";
$lang["MENU_OUT"] = "<i><small>Angetriben mit Codes von <a href='https://twitter.com/MasterGeneralYT'> <font color=grey>TheMasterGeneral</font></a> Mit Genehmigung. </small> </i> ";
$lang["MENU_PROFILE"] = "Profil";
$lang["MENU_SETTINGS"] = "Einstellungen";
$lang["MENU_STAFF"] = "Staff Panel";
$lang["MENU_LOGOUT"] = "Abmelden";
$lang["MENU_TIN"] = "Zeit ist jetzt";
$lang["MENU_QE"] = "Abfragen ausgeführt";
$lang["MENU_UNREADMAIL1"] = "Ungelesene Mail!";
$lang["MENU_UNREADNOTIF"] = "Ungelesene Mail";
$lang["MENU_FEDJAIL"] = "Bundesgefängis!";
$lang["MENU_FEDJAIL1"] = "Du bist im Bundesgefängnis für die nächste";
$lang["MENU_FEDJAIL2"] = "für das Verbrechen von:";
$lang["MENU_UNREADANNONCE"] = "Ungelesene Ankündigungen!";
$lang["MENU_UNREADANNONCE1"] = "Es gibt";
$lang["MENU_UNREADANNONCE2"] = "Ankündigungen, die Sie noch nicht gelesen haben. Lese sie";
$lang["MENU_UNREADMAIL2"] = "Sie haben";
$lang["MENU_UNREADMAIL3"] = "ungelesene Nachrichten. Klicken";
$lang["MENU_UNREADMAIL4"] = ", um sie zu lesen.";
$lang["MENU_UNREADNOTIF1"] = "ungelesene Benachrichtigungen. Klicken";
$lang["MENU_INFIRMARY1"] = "Sie befinden sich im Krankenhaus für das nächste";
$lang["MENU_DUNGEON1"] = "Du bist im Kerker für den nächsten";
$lang["MENU_XPLOST"] = "Wenn ihr vom Kampf ausgeht, habt ihr eure ganze Erfahrung verloren!";
$lang["MENU_RULES"] = "Spielregeln";

// Einstellungen
$lang["PREF_CPASSWORD"] = "Passwort ändern";
$lang["PREF_WELCOME_1"] = "Grüße dort";
$lang["PREF_WELCOME_2"] = "und willkommen im Preferences Center. Sie können die Informationen zu Ihrem Konto anzeigen und ändern!";
$lang["PREF_CNAME"] = "Benutzernamen ändern";
$lang["PREF_CTIME"] = "Zeitzone ändern";
$lang["PREF_CLANG"] = "Sprache ändern";
$lang["PREF_CPIC"] = "Anzeigebild ändern";
$lang["PREF_CTHM"] = "Thema ändern";
$lang["PREF_CTHM_FORM"] = "Wählen Sie das Thema aus, zu dem Sie wechseln möchten. Diese Aktion kann jederzeit wieder rückgängig gemacht werden.";
$lang["PREF_CTHM_FORM1"] = "Wähle dein Thema";
$lang["PREF_CTHM_FORMDD1"] = "Hell [Standard]";
$lang["PREF_CTHM_FORMDD2"] = "Dark [Alternative]";
$lang["PREF_CTHM_FORMDD3"] = "Dark [Purple navbar]";
$lang["PREF_CTHM_FORMBTN"] = "Thema aktualisieren";
$lang["PREF_CTHM_SUB_ERROR"] = "Sie versuchen, ein nicht existentes Design zu verwenden.";
$lang["PREF_CTHM_SUB_SUCCESS"] = "Dein Theme wurde erfolgreich aktualisiert und die Effekte werden beim nächsten Laden der Datei sichtbar.";

// Benutzername Ändern
$lang["UNC_TITLE"] = "Benutzernamen ändern ...";
$lang["UNC_INTRO"] = "Hier kannst du deinen Namen ändern, der während des Spiels angezeigt wird. Verwenden Sie nicht einen unangemessenen Namen oder Sie finden Ihre Privileg, Ihren Namen zu entfernen.";
$lang["PREF_CNAME"] = "Benutzernamen ändern";
$lang["UNC_ERROR_1"] = "Du hast noch keinen neuen Benutzernamen eingetragen!";
$lang["UNC_ERROR_2"] = "erneut versuchen";
$lang["UNC_LENGTH_ERROR"] = "Benutzernamen müssen mindestens drei Zeichen lang sein und höchstens zwanzig Zeichen lang sein.";
$lang["UNC_INVALIDCHARCTERS"] = "Benutzernamen können nur aus Zahlen, Buchstaben, Unterstrichen und Leerzeichen bestehen!";
$lang["UNC_INUSE"] = "Der von Ihnen gewählte Benutzername wird verwendet. Bitte wählen Sie einen anderen Benutzernamen.";
$lang["UNC_GOOD"] = "Du hast deinen Benutzernamen erfolgreich aktualisiert!";
$lang["UNC_NUN"] = "Neuer Nutzername:";
$lang["UNC_BUTTON"] = "Benutzernamen ändern";

//Passwortänderung
$lang["PW_TITLE"] = "Passwort ändern ...";
$lang["PW_CP"] = "Aktuelles Passwort";
$lang["PW_CNP"] = "Neues Passwort bestätigen";
$lang["PW_NP"] = "Neues Passwort";
$lang["PW_BUTTON"] = "Passwort aktualisieren";
$lang["PW_INCORRECT"] = "Was Sie als altes Passwort eingegeben haben, ist falsch.";
$lang["PW_NOMATCH"] = "Die eingegebenen neuen Passwörter stimmen nicht überein.";
$lang["PW_DONE"] = "Ihr Passwort wurde erfolgreich aktualisiert.";

// Bildwechsel
$lang["PIC_TITLE"] = "Bildwechsel anzeigen";
$lang["PIC_NOTE"] = "Bitte beachten Sie, dass dies extern gehostet werden muss, <a href='https://imgur.com/'>Imgur</a> ist unsere Empfehlung.";
$lang["PIC_NOTE2"] = "Alle Bilder, die nicht 250x250 sind, werden automatisch verkleinert.";
$lang["PIC_NEWPIC"] = "Link zum neuen Bild:";
$lang["PIC_TOOBIG"] = "Bild zu groß!";
$lang["PIC_BTN"] = "Bild ändern";
$lang["PIC_TOOBIG2"] = "Die Dateigröße Ihres Bildes ist zu groß, die maximale Größe eines Bildes kann 1 MB betragen.";
$lang["PIC_NOIMAGE"] = "Sie haben eine URL angegeben, die nicht einmal ein Bild ist.";
$lang["PIC_SUCCESS"] = "Sie haben Ihr Display-Bild erfolgreich aktualisiert!";

//Loginseite
$lang["LOGIN_REGISTER"] = "Registrieren";
$lang["LOGIN_RULES"] = "Spielregeln";
$lang["LOGIN_LOGIN"] = "Login";
$lang["LOGIN_AHA"] = "Haben Sie bereits ein Konto?";
$lang["LOGIN_EMAIL"] = "E-Mail-Adresse";
$lang["LOGIN_PASSWORD"] = "Passwort";
$lang["LOGIN_LWE"] = "Login mit Email";
$lang["LOGIN_SIGNIN"] = "Anmelden";
$lang["LOGIN_NH"] = "Neu hier? <a href='register.php'>Begleiten Sie uns</a>!";

//Neu registrieren
$lang["REG_FORM"] = "Registrierung";
$lang["REG_USERNAME"] = "Benutzername";
$lang["REG_EMAIL"] = "Email";
$lang["REG_PW"] = "Passwort";
$lang["REG_CPW"] = "Passwort bestätigen";
$lang["REG_SEX"] = "Geschlecht";
$lang["REG_CLASS"] = "Klasse";
$lang["REG_REFID"] = "Empfehlungs-ID";
$lang["REG_PROMO"] = "Promo-Code";
$lang["REG_WARRIORCLASS"] = "Kriegerklasse!";
$lang["REG_ROGUECLASS"] = "Rogue-Klasse!";
$lang["REG_DEFENDERCLASS"] = "Defnder Klasse!";
$lang["REG_NOCLASS"] = "Wir müssen eine Klasse auswählen, bitte.";
$lang["REG_ROGUECLASS_INFO"] = "Ein Schurkenkämpfer fängt mit mehr Beweglichkeit und weniger Kraft an. Während ihrer Abenteuer werden sie Agilität viel schneller gewinnen als jede andere Stat und Stärke viel langsamer als die anderen.";
$lang["REG_DEFENDERCLASS_INFO"] = "Ein Verteidiger startet mit mehr Wachsamkeit und weniger Agilität, während er während seiner Abenteuer viel schneller als jeder andere Wächter gewinnt und die Beweglichkeit viel langsamer ist als die anderen.";
$lang["REG_WARRIORCLASS_INFO"] = "Eine Krieger-Torte mit mehr Kraft und weniger Schutz. Während ihrer Abenteuer werden sie stärker als jeder andere Stand gewinnen und viel langsamer wachen als die anderen.";
$lang["REG_UNIUERROR"] = "Der von Ihnen gewählte Benutzername wird bereits verwendet. Gehen Sie zurück und versuchen Sie es erneut.";
$lang["REG_SUCCESS"] = "Sie haben dem Spiel erfolgreich beigetreten. Genießen Sie Ihren Aufenthalt und lesen Sie die Spielregeln.";
$lang["REG_EIUERROR"] = "Die von Ihnen ausgewählte E-Mail wird bereits verwendet. Gehen Sie zurück und versuchen Sie es erneut.";
$lang["REG_PWERROR"] = "Sie müssen ein Passwort eingeben und bestätigen.";
$lang["REG_REFERROR"] = "Die angegebene Überweisung existiert nicht im Spiel. Gehe zurück und überprüfe es erneut.";
$lang["REG_REFMERROR"] = "Die von Ihnen angegebene Empfehlung teilt dieselbe IP-Adresse wie Sie, ohne dass mehrere Administratoren angemeldet wurden.";
$lang["REG_VPWERROR"] = "Die eingegebenen Passwörter stimmen nicht überein. Gehen Sie zurück und versuchen Sie es erneut.";
$lang["REG_CAPTCHAERROR"] = "Sie haben das Captcha ausgefallen oder haben es nicht eingegeben. Gehen Sie zurück und versuchen Sie es erneut.";
$lang["REG_GENDERERROR"] = "Sie haben ein ungültiges Geschlecht angegeben. Bitte gehen Sie zurück und versuchen Sie es erneut.";
$lang["REG_CLASSERROR"] = "Du hast eine ungültige Kampfklasse angegeben, bitte geh zurück und versuche es erneut.";
$lang["REG_EMAILERROR"] = "Sie haben keine gültige E-Mail eingegeben oder das E-Mail-Feld nicht eingegeben. Bitte gehen Sie zurück und versuchen Sie es erneut.";
$lang["REG_MULTIALERT"] = "Wir haben festgestellt, dass sich jemand mit Ihrer IP-Adresse bereits registriert hat . ";

// CSRF-Fehler
$lang["CSRF_ERROR_TITLE"] = "Aktion blockiert!";
$lang["CSRF_PREF_MENU"] = "Sie können die Aktion erneut versuchen, indem Sie";
$lang["CSRF_ERROR_TEXT"] = "Die Aktion, die du versucht hast zu blockieren, wurde blockiert, weil du eine andere Seite im Spiel geladen hast. Wenn du während dieser Zeit keine andere Seite geladen hast, ändere dein Passwort sofort Person Zugang zu Ihrem Konto haben kann! ";

// Alert-Titel
$lang["ERROR_EMPTY"] = "Leere Eingabe!";
$lang["ERROR_LENGTH"] = "Eingabelänge prüfen!";
$lang["ERROR_GENERIC"] = "Uh Oh!";
$lang["ERROR_SUCCESS"] = "Erfolg!";
$lang["ERROR_INVALID"] = "Ungültige Eingabe!";
$lang["ERROR_SECURITY"] = "Sicherheitsfehler!";
$lang["ERROR_NONUSER"] = "Nicht vorhandener Benutzer!";
$lang["ERROR_NOPERM"] = "Keine Berechtigung!";
$lang["ERROR_UNKNOWN"] = "Unbekannter Fehler!";
$lang["ERROR_INFO"] = "Informationen!";

// Sonstiges Warnungsdetails
$lang["ALERT_INSTALLER"] = "Die Installerdatei konnte nicht gelöscht werden. Bitte löschen Sie installer.php aus dem Root-Ordner Ihrer Website oder riskieren Sie einen anderen Benutzer, der das Installationsprogramm ausführt und Ihr Spiel ruiniert.";

// Generic
$lang["GEN_HERE"] = "hier";
$lang["GEN_back"] = "zurück";
$lang["GEN_INFIRM"] = "Unbewusst!";
$lang["GEN_DUNG"] = "Locked Up!";
$lang["GEN_GREETING"] = "Hallo";
$lang["GEN_MINUTES"] = "Minuten".
$lang["GEN_EXP"] = "Erfahrung";
$lang["GEN_NEU"] = "Gelöschtes Konto";
$lang["GEN_AT"] = "at";
$lang["GEN_EDITED"] = "editiert";
$lang["GEN_TIMES"] = "mal".
$lang["GEN_RANK"] = "Rang";
$lang["GEN_ONLINE"] = "Online";
$lang["GEN_OFFLINE"] = "Offline";
$lang["GEN_FOR"] = "for";
$lang["GEN_INDAH"] = "In der";
$lang["GEN_YES"] = "Ja";
$lang["GEN_NO"] = "Nein";
$lang["GEN_STR"] = "Stärke";
$lang["GEN_AGL"] = "Beweglichkeit";
$lang["GEN_GRD"] = "Guard";
$lang["GEN_IQ"] = "IQ";
$lang["GEN_LAB"] = "Arbeit";
$lang["GEN_GOHOME"] = "Nach Hause gehen";
$lang["GEN_IUOF"] = "Ungültige Verwendung der Datei!";
$lang["GEN_THEM"] = "Them";
$lang["GEN_CONTINUE"] = "Weiter";
$lang["GEN_FOR_S"] = "für";
$lang["GEN_NOPERM"] = "Sie haben nicht die richtige Benutzer-Ebene, um diese Seite anzuzeigen.Wenn dies falsch ist, wenden Sie sich bitte sofort an einen Admin!";

//Fitnessstudio
$lang["GYM_INFIRM"] = "Während du bewusstlos bist, kannst du nicht trainieren, komm zurück, nachdem du gesund fühlst!";
$lang["GYM_DUNG"] = "Die Wächter würden Sie normalerweise trainieren lassen, aber was Sie getan haben, war zu hoch von einem Verbrechen.";
$lang["GYM_NEG"] = "Nicht genug Energie!";
$lang["GYM_INVALIDSTAT"] = "Sie können diesen Zug nicht trainieren!";
$lang["GYM_NEG_DETAIL"] = "Du hast nicht genug Energie, um das so oft zu trainieren, entweder warten Sie, bis sich Ihre Energie erholt hat, oder füllen Sie sie manuell wieder auf.";

//Erforschen
$lang["EXPLORE_INTRO"] = "Sie beginnen die Stadt zu erkunden und finden ein paar coole Dinge, um Sie zu beschäftigen ...";
$lang["EXPLORE_REF"] = "Das ist dein Empfehlungslink, gib es Freunden oder Feinden oder spam es einfach.";
$lang["EXPLORE_SHOP"] = "Geschäfte";
$lang["EXPLORE_LSHOP"] = "Lokale Geschäfte";
$lang["EXPLORE_POSHOP"] = "Mitspieler-Shops";
$lang["EXPLORE_IMARKET"] = "Objektmarkt";
$lang["EXPLORE_IAUCTION"] = "Item Auction";
$lang["EXPLORE_TRADE"] = "Handel";
$lang["EXPLORE_SCMARKET"] = "Sekundärer Währungsmarkt";
$lang["EXPLORE_FD"] = "Finanziell";
$lang["EXPLORE_BANK"] = "Bank";
$lang["EXPLORE_ESTATES"] = "Estates";
$lang["EXPLORE_HL"] = "Arbeit";
$lang["EXPLORE_MINE"] = "Mining";
$lang["EXPLORE_WC"] = "Holzschnitt";
$lang["EXPLORE_FARM"] = "Landwirtschaft";
$lang["EXPLORE_ADMIN"] = "Verwaltung";
$lang["EXPLORE_USERLIST"] = "Benutzerliste";
$lang["EXPLORE_STAFFLIST"] = "Staff List";
$lang["EXPLORE_FED"] = "Bundesgefängnis";
$lang["EXPLORE_STATS"] = "Spielstats";
$lang["EXPLORE_REPORT"] = "Spielerbericht";
$lang["EXPLORE_GAMES"] = "Spiele";
$lang["EXPLORE_RR"] = "Russisches Roulette";
$lang["EXPLORE_HILO"] = "Hoch / niedrig";
$lang["EXPLORE_ROULETTE"] = "Roulette";
$lang["EXPLORE_GUILDS"] = "Gilden";
$lang["EXPLORE_DUNG"] = "Dungeon";
$lang["EXPLORE_INFIRM"] = "Krankenhaus";
$lang["EXPLORE_GYM"] = "Training";
$lang["EXPLORE_JOB"] = "Ihr Job";
$lang["EXPLORE_ACADEMY"] = "Lokale Akademie";
$lang["EXPLORE_PINTER"] = "Social";
$lang["EXPLORE_FORUMS"] = "Foren";
$lang["EXPLORE_NEWSPAPER"] = "Zeitung";
$lang["EXPLORE_ACT"] = "Aktivitäten";
$lang["EXPLORE_ANNOUNCEMENTS"] = "Ankündigungen";
$lang["EXPLORE_CRIMES"] = "Kriminalzentrum";
$lang["EXPLORE_TRAVEL"] = "Pferdereisen";
$lang["EXPLORE_GUILDLIST"] = "Gildenliste";
$lang["EXPLORE_YOURGUILD"] = "Deine Gilde";
$lang["EXPLORE_TOPTEN"] = "Top 10 Spieler";
$lang["EXPLORE_SLOTS"] = "Spielautomaten";
$lang["EXPLORE_BOTS"] = "Bot-Liste";

//Fehlerdetails
$lang["ERRDE_EXPLORE"] = "Da Sie im Krankenhaus sind, können Sie die Stadt nicht besuchen!";
$lang["ERRDE_EXPLORE2"] = "Da du im Kerker bist, kannst du die Stadt nicht besuchen!";
$lang["ERRDE_PN"] = "Ihr persönlicher Merkzettel konnte aufgrund der 65 655 Zeichen nicht aktualisiert werden.";
$lang["ERROR_MAIL_UNOWNED"] = "Diese Nachricht kann nicht gelesen werden, da sie nicht an Sie gesendet wurde!";
$lang["ERROR_FORUM_VF"] = "Gehen Sie zurück und versuchen Sie es erneut für uns.";

//Index
$lang["INDEX_TITLE"] = "Allgemeine Informationen";
$lang["INDEX_WELCOME"] = "Willkommen zurück,";
$lang["INDEX_YLVW"] = "Dein letzter Besuch war an";
$lang["INDEX_LEVEL"] = "Level";
$lang["INDEX_CLASS"] = "Klasse";
$lang["INDEX_VIP"] = "VIP-Tage";
$lang["INDEX_PRIMCURR"] = "Primäre Währung";
$lang["INDEX_SECCURR"] = "Sekundärwährung";
$lang["INDEX_ENERGY"] = "Energie";
$lang["INDEX_BRAVE"] = "Brave";
$lang["INDEX_WILL"] = "Will";
$lang["INDEX_PN"] = "Persönlicher Notizblock";
$lang["INDEX_PNSUCCESS"] = "Ihr persönlicher Merkzettel wurde erfolgreich aktualisiert.";
$lang["INDEX_EXP"] = "XP";
$lang["INDEX_HP"] = "HP";

// Formular Schaltflächen
$lang["FB_PN"] = "Update Notes";
$lang["FB_PR"] = "Bericht senden";

// Spielerbericht
$lang["PR_TITLE"] = "Spielerbericht";
$lang["PR_INTRO"] = "Kennen Sie jemanden, der die Regeln gebrochen hat oder einfach nur unhonorable ist, dann können Sie diesen Bericht melden Die Sie hier eingeben, werden vertraulich bleiben und nur von leitenden Mitarbeitern gelesen werden.Wenn Sie zu einem Verbrechen bekennen wollen, ist dies auch ein großer Ort auch. ";
$lang["PR_USER"] = "Benutzer?";
$lang["PR_CATEGORY"] = "Kategorie?";
$lang["PR_REASON"] = "Was haben sie getan?";
$lang["PR_USER_PH"] = "Benutzer-ID des Spielers ist schlecht.";
$lang["PR_REASON_PH"] = "Bitte geben Sie möglichst viele Informationen ein.";
$lang["PR_CAT_1"] = "Bug Missbrauch";
$lang["PR_CAT_2"] = "Spielerbelästigung";
$lang["PR_CAT_3"] = "Scamming";
$lang["PR_CAT_4"] = "Spamming";
$lang["PR_CAT_5"] = "Ermutigende Regel brechen";
$lang["PR_CAT_6"] = "Sicherheitsproblem";
$lang["PR_CAT_7"] = "Andere";
$lang["PR_CATBAD"] = "Sie haben eine ungültige Kategorie angegeben. Gehen Sie zurück und versuchen Sie es erneut. ";
$lang["PR_MAXCHAR"] = "Sie versuchen, zu lange von einem Grund einzugeben. Mit diesem Formular können Sie maximal 1250 Zeichen eingeben. Gehen Sie zurück und versuchen Sie es erneut. ";
$lang["PR_INVALID_USER"] = "Sie versuchen einen Spieler zu melden, der gerade nicht existiert. Überprüfen Sie die eingegebene Benutzer-ID und versuchen Sie es erneut. ";
$lang["PR_SUCCESS"] = "Sie haben den Benutzer erfolgreich gemeldet. Das Personal kann Ihnen eine Nachricht senden, die Fragen zum gesendeten Bericht stellt. Bitte beantworten Sie sie nach bestem Wissen. ";

// Mail
$lang["MAIL_READ"] = "Lesen";
$lang["MAIL_DELETE"] = "Löschen";
$lang["MAIL_REPORT"] = "Bericht";
$lang["MAIL_MSGREAD"] = "Nachricht lesen";
$lang["MAIL_MSGUNREAD"] = "Nachricht ungelesen";
$lang["MAIL_USERDATE"] = "Benutzer / Info";
$lang["MAIL_PREVIEW"] = "Nachrichtenvorschau";
$lang["MAIL_ACTION"] = "Aktionen";
$lang["MAIL_USERINFO"] = "Senderinfo";
$lang["MAIL_MSGSUB"] = "Betreff / Nachricht";
$lang["MAIL_STATUS"] = "Status".
$lang["MAIL_SENTAT"] = "Gesendet bei";
$lang["MAIL_SENDTO"] = "An";
$lang["MAIL_FROM"] = "Von";
$lang["MAIL_SUBJECT"] = "Betreff";
$lang["MAIL_MESSAGE"] = "Nachricht";
$lang["MAIL_REPLYTO"] = "Antwort an";
$lang["MAIL_EMPTYINPUT"] = "Es scheint, dass Sie keine zu sendende Nachricht eingegeben haben. Bitte gehen Sie zurück und geben Sie eine Nachricht ein! ";
$lang["MAIL_INPUTLNEGTH"] = "Es scheint, dass Sie versuchen, eine lange Nachricht zu senden. Denken Sie daran, dass Nachrichten nur 65 655 Zeichen lang sein dürfen, und Themen können nur 50 Zeichen lang sein. ";
$lang["MAIL_NOUSER"] = "Sie müssen einen Empfänger für diese Nachricht eingeben! Gehen Sie zurück und versuchen Sie es erneut! ";
$lang["MAIL_UDNE"] = "Benutzer existiert nicht!";
$lang["MAIL_UDNE_TEXT"] = "Sie versuchen, eine Nachricht an einen Benutzer zu senden, der nicht existiert. Überprüfen Sie Ihre Quelle und versuchen Sie es erneut. ";
$lang["MAIL_SUCCESS"] = "Sie haben eine Nachricht erfolgreich gesendet!";
$lang["MAIL_TIMEERROR"] = "Sie müssen 60 Sekunden warten, bevor Sie eine Nachricht an diesen Benutzer senden können. Wenn Sie schnell auf jemanden antworten müssen, können Sie das normale Mail-System weiterhin verwenden. ";
$lang["MAIL_READALL"] = "Alle ungelesenen Nachrichten wurden als gelesen markiert!";
$lang["MAIL_DELETECONFIRM"] = "Sind Sie zu 100% sicher, dass Sie Ihren Posteingang leeren möchten? Das kann nicht rückgängig gemacht werden.";
$lang["MAIL_DELETEYES"] = "Ja, ich bin 100% sicher";
$lang["MAIL_DELETENO"] = "Hold on, auf den zweiten Gedanken";
$lang["MAIL_DELETEDONE"] = "Dein gesamter Posteingang wurde gelöscht.";
$lang["MAIL_QUICKREPLY"] = "Eine kurze Antwort senden ...";
$lang["MAIL_MARKREAD"] = "Alles als gelesen markieren";
$lang["MAIL_SENDMSG"] = "Nachricht senden";

// Menü Sprache
$lang["LANG_INTRO"] = "Hier können Sie Ihre Sprache ändern. Dies wird nicht in Ihrem Konto gespeichert. Diese wird über ein Cookie gespeichert. Wenn Sie Geräte wechseln oder Ihre Cookies löschen, müssen Sie Ihre Sprache erneut zurücksetzen. Übersetzungen dürfen nicht zu 100% korrekt sein. ";
$lang["LANG_BUTTON"] = "Sprache ändern";
$lang["LANG_UPDATE"] = "Sie haben eine ungültige Sprache angegeben.";
$lang["LANG_UPDATE2"] = "Sie haben Ihre Sprache erfolgreich aktualisiert!";

// Benachrichtigungen
$lang["NOTIF_TABLE_HEADER1"] = "Notifcations Info";
$lang["NOTIF_TABLE_HEADER2"] = "Notifcations Text";
$lang["NOTIF_DELETE_SINGLE"] = "Sie haben eine Benachrichtigung erfolgreich gelöscht.";
$lang["NOTIF_DELETE_SINGLE_FAIL"] = "Diese Meldung kann nicht gelöscht werden, da sie nicht existiert oder nicht zu Ihnen gehört.";
$lang["NOTIF_TITLE"] = "Letzte fünfte Benachrichtigungen, die zu Ihnen gehören ...";
$lang["NOTIF_READ"] = "Benachrichtigung lesen";
$lang["NOTIF_UNREAD"] = "Benachrichtigung ungelesen";
$lang["NOTIF_DELETE"] = "Benachrichtigung löschen";

//Bank
$lang["BANK_BUY1"] = "Bankkonto eröffnen, nur";
$lang["BANK_BUYYES"] = "Sign Me Up!";
$lang["BANK_SUCCESS"] = "Herzlichen Glückwunsch, Sie haben ein Bankkonto für gekauft";
$lang["BANK_SUCCESS1"] = "Benutzung meines Kontos!";
$lang["BANK_FAIL"] = "Du hast nicht genug Geld, um ein Bankkonto zu erwerben, komm später zurück, wenn du genug hast.";
$lang["BANK_HOME"] = "Sie haben zurzeit";
$lang["BANK_HOME1"] = "in der niedrigen Bank.";
$lang["BANK_HOME2"] = "Am Ende eines jeden Tages erhöht sich Ihr Kontostand um 2%.";
$lang["BANK_DEPOSIT_WARNING"] = "Es kostet Sie";
$lang["BANK_DEPOSITE_WARNING1"] = "des gezahlten Geldes, aufgerundet.";
$lang["BANK_AMOUNT"] = "Betrag:";
$lang["BANK_DEPOSIT"] = "Einzahlung";
$lang["BANK_WITHDRAW_WARNING"] = "Zum Glück gibt es keine Gebühren bei Abhebungen.";
$lang["BANK_WITHDRAW"] = "Zurückziehen";
$lang["BANK_D_ERROR"] = "Du versuchst Geld einzahlen, das du nicht hast!";
$lang["BANK_D_SUCCESS"] = "Sie überreichen";
$lang["BANK_D_SUCCESS1"] = "hinterlegt werden.";
$lang["BANK_D_SUCCESS2"] = ") wird genommen,";
$lang["BANK_D_SUCCESS3"] = "wird Ihrem Bankkonto hinzugefügt. <B> Sie haben jetzt";
$lang["BANK_D_SUCCESS4"] = "in Ihrem Konto. </ B>";
$lang["BANK_W_FAIL"] = "Sie versuchen, mehr {$lang['INDEX_PRIMCURR']} zurückzuziehen, als Sie derzeit in der Bank haben.";
$lang["BANK_W_SUCCESS"] = "Sie haben erfolgreich zurückgezogen";
$lang["BANK_W_SUCCESS1"] = "von Ihrem Bankkonto.";
$lang["BANK_W_SUCCESS2"] = "auf Ihrem Bankkonto belassen.";

//Forum-Mitarbeiter anzeigen
$lang["FORUM_EMPTY_REPLY"] = "Sie versuchen, eine leere Antwort einzureichen, die Sie nicht tun können! Bitte stellen Sie sicher, dass Sie das Antwortformular ausgefüllt haben!";
$lang["FORUM_TOPIC_DNE_TITLE"] = "Nicht existentes Thema!";
$lang["FORUM_TOPIC_DNE_TEXT"] = "Sie versuchen, mit einem nicht vorhandenen Thema zu interagieren. Überprüfen Sie Ihre Quelle und versuchen Sie es erneut.";
$lang["FORUM_FORUM_DNE_TITLE"] = "Nicht existentes Forum!";
$lang["FORUM_FORUM_DNE_TEXT"] = "Sie versuchen, mit einem Forum zu interagieren, das nicht existiert. Überprüfen Sie Ihre Quelle und versuchen Sie es erneut.";
$lang["FORUM_POST_DNE_TITLE"] = "Nicht vorhandener Beitrag!";
$lang["FORUM_POST_DNE_TEXT"] = "Sie versuchen, mit einem Post zu interagieren, der nicht existiert. Überprüfen Sie Ihre Quelle und versuchen Sie es erneut.";
$lang["FORUM_NOPERMISSION"] = "Du versuchst, mit einem Forum zu interagieren, für das du keine Berechtigung hast, mit denen du interagieren sollst. Sollte dies ein Fehler sein, benachrichtige dich bitte sofort!";
$lang["FORUM_FORUMS"] = "Foren";
$lang["FORUM_ON"] = "Ein";
$lang["FORUM_IN"] = "In:";
$lang["FORUM_BY"] = "Von:";
$lang["FORUM_STAFFONLY"] = "Nur Personal";
$lang["FORUM_F_LP"] = "Letzter Beitrag";
$lang["FORUM_F_TC"] = "Topic Count";
$lang["FORUM_F_PC"] = "Postzählung";
$lang["FORUM_F_FN"] = "Forum-Name";
$lang["FORUM_FORUMSHOME"] = "Foren-Startseite";
$lang["FORUM_TOPICNAME"] = "Themenname";
$lang["FORUM_TOPICOPEN"] = "Thema geöffnet";
$lang["FORUM_TOPIC_MOVE"] = "Thema verschieben";
$lang["FORUM_PAGES"] = "Seiten:";
$lang["FORUM_TOPIC_MTT"] = "Thema verschieben nach:";
$lang["FORUM_TOPIC_PIN"] = "Pin / Unpinthema";
$lang["FORUM_TOPIC_LOCK"] = "Thema sperren / entsperren";
$lang["FORUM_TOPIC_DELETE"] = "Thema löschen";
$lang["FORUM_POST_QUOTE"] = "Zitat Post";
$lang["FORUM_POST_DELETE"] = "Post löschen";
$lang["FORUM_POST_EDIT_1"] = "Dieser Beitrag wurde zuletzt bearbeitet von";
$lang["FORUM_NOSIG"] = "Keine Signatur";
$lang["FORUM_POST_POSTED"] = "Antworten bei:";
$lang["FORUM_POST_POST"] = "Post";
$lang["FORUM_POST_REPLY"] = "Antwort schreiben";
$lang["FORUM_POST_REPLY2"] = "Auf Beitrag antworten";
$lang["FORUM_POST_REPLY_INFO"] = "Geben Sie hier Ihre Antwort ein. Denken Sie daran, Sie können BBCode verwenden! Bitte stellen Sie sicher, dass Sie keine Spielregeln bei der Buchung brechen werden. ";
$lang["FORUM_POST_TIL"] = "Dieses Thema ist gesperrt, und deshalb kannst du keine Antwort zu diesem Thema posten.";
$lang["FORUM_MAX_CHAR_REPLY"] = "Wenn du im Forum Beiträge schreiben möchtest, darfst du nur maximal 65.535 Zeichen enthalten.";
$lang["FORUM_REPLY_SUCCESS"] = "Sie haben Ihre Antwort erfolgreich auf dieses Thema gepostet.";
$lang["FORUM_TOPIC_FORM_TITLE"] = "Themenname";
$lang["FORUM_TOPIC_FORM_DESC"] = "Themabeschreibung";
$lang["FORUM_TOPIC_FORM_TEXT"] = "Thementext";
$lang["FORUM_TOPIC_FORM_BUTTON"] = "Postthema";
$lang["FORUM_TOPIC_FORM_TITLE_LENGTH"] = "Themennamen und Beschreibungen können maximal 255 Zeichen lang sein.";
$lang["FORUM_TOPIC_FORM_PAGE"] = "Neues Themenformular";
$lang["FORUM_TOPIC_FORM_SUCCESS"] = "Du hast erfolgreich ein neues Thema in den Foren gepostet!";
$lang["FORUM_QUOTE_FORM_PAGENAME"] = "Quoting a Post";
$lang["FORUM_QUOTE_FORM_INFO"] = "Einen Beitrag zitieren ...";
$lang["FORUM_EDIT_FORM_INFO"] = "Post bearbeiten ...";
$lang["FORUM_EDIT_FORM_PAGENAME"] = "Bearbeiten eines Beitrags";
$lang["FORUM_EDIT_NOPERMISSION"] = "Sie haben keine Berechtigung, diesen Beitrag zu bearbeiten. Wenn Sie glauben, dass dies falsch ist, lassen Sie einen Admin wissen ASAP! ";
$lang["FORUM_EDIT_FORM_SUBMIT"] = "Beitrag bearbeiten";
$lang["FORUM_EDIT_SUCCESS"] = "Sie haben einen Beitrag erfolgreich bearbeitet!";
$lang["FORUM_MOVE_TOPIC_DFDNE"] = "Du versuchst, ein Thema in ein Forum zu verschieben, das nicht existiert. Gehe zurück und versuche es erneut.";
$lang["FORUM_MOVE_TOPIC_DONE"] = "Sie haben das Thema erfolgreich verschoben.";
$lang["FORUM_POST_EDIT"] = "Beitrag bearbeiten";

// Bargeldformular senden
$lang["SCF_POSCASH"] = "Sie müssen mindestens 1 {$lang['INDEX_PRIMCURR']} senden, um dieses Formular zu verwenden.";
$lang["SCF_UNE"] = "Sie können {$lang["INDEX_PRIMCURR"]} nicht an einen nicht existierenden Benutzer senden!";
$lang["SCF_NEC"] = "Sie versuchen, mehr {$lang['INDEX_PRIMCURR']} zu senden, als Sie derzeit haben!";
$lang["SCF_SUCCESS"] = "{$lang['INDEX_PRIMCURR']} wurde erfolgreich gesendet.";

//Profil
$lang["PROFILE_UNF"] = "Wir konnten keinen Benutzer mit der Benutzer-ID finden, die Sie eingegeben haben.Sie könnten diese Nachricht erhalten, da der Spieler, den Sie sehen wollen, gelöscht wurde.";
$lang["PROFILE_PROFOR"] = "Profil für";
$lang["PROFILE_LOCATION"] = "Ort:";
$lang["PROFILE_GUILD"] = "Gilde";
$lang["PROFILE_PI"] = "Phyiscal Information";
$lang["PROFILE_ACTION"] = "Aktionen";
$lang["PROFILE_FINANCIAL"] = "Finanzinformationen";
$lang["PROFILE_STAFF"] = "Personalbereich";
$lang["PROFILE_REGISTERED"] = "Registriert";
$lang["PROFILE_ACTIVE"] = "Zuletzt aktiv";
$lang["PROFILE_LOGIN"] = "Letzter Login";
$lang["PROFILE_AGE"] = "Age";
$lang["PROFILE_DAYS_OLD"] = "alt".
$lang["PROFILE_REF"] = "Verweise";
$lang["PROFILE_FRI"] = "Freunde";
$lang["PROFILE_ENE"] = "Feinde";
$lang["PROFILE_ATTACK"] = "Angriff";
$lang["PROFILE_SPY"] = "Spy On";
$lang["PROFILE_POKE"] = "Poke";
$lang["PROFILE_MSG1"] = "Senden";
$lang["PROFILE_MSG2"] = "eine Nachricht";
$lang["PROFILE_MSG3"] = "Empfänger:";
$lang["PROFILE_MSG4"] = "Nachricht:";
$lang["PROFILE_MSG5"] = "Fenster schliessen";
$lang["PROFILE_MSG6"] = "Nachricht senden";
$lang["PROFILE_CASH"] = "Geld senden";
$lang["PROFILE_STAFF_DATA"] = "Daten";
$lang["PROFILE_STAFF_LOC"] = "Ort";
$lang["PROFILE_STAFF_LH"] = "Last Hit";
$lang["PROFILE_STAFF_LL"] = "Letzter Login";
$lang["PROFILE_STAFF_REGIP"] = "Anmelden";
$lang["PROFILE_STAFF_THRT"] = "Bedrohung?";
$lang["PROFILE_STAFF_RISK"] = "Risiko-Ebene <br /> <small> 1 ist niedrig, 5 ist hoch </ small>";
$lang["PROFILE_STAFF_OS"] = "Browser / OS";
$lang["PROFILE_STAFF_NOTES"] = "Mitarbeiter Anmerkungen:";
$lang["PROFILE_STAFF_BTN"] = "Update Notes Über";
$lang["PROFILE_BTN_MSG"] = "Senden";
$lang["PROFILE_BTN_MSG1"] = "eine Nachricht";
$lang["PROFILE_BTN_SND"] = "Senden";

// Gegenstände ausstatten
$lang["EQUIP_NOITEM"] = "Item kann nicht gefunden werden, und als Ergebnis können Sie es nicht ausrüsten.";
$lang["EQUIP_NOITEM_TITLE"] = "Element existiert nicht!";
$lang["EQUIP_NOTWEAPON"] = "Der Gegenstand, den du ausrüsten willst, kann nicht als Waffe ausgerüstet werden.";
$lang["EQUIP_NOTWEAPON_TITLE"] = "Ungültige Waffe!";
$lang["EQUIP_NOSLOT"] = "Sie versuchen, dieses Element an einen ungültigen oder nicht vorhandenen Steckplatz auszurüsten.";
$lang["EQUIP_NOSLOT_TITLE"] = "Ungültiger Geräte-Steckplatz!";
$lang["EQUIP_WEAPON_SUCCESS1"] = "Sie haben erfolgreich ausgestattet";
$lang["EQUIP_WEAPON_SUCCESS2"] = "als Ihr";
$lang["EQUIP_WEAPON_SLOT1"] = "Primäre Waffe";
$lang["EQUIP_WEAPON_SLOT2"] = "Sekundärwaffe";
$lang["EQUIP_WEAPON_SLOT3"] = "Armor";
$lang["EQUIP_WEAPON_TITLE"] = "Eine Waffe ausrüsten";
$lang["EQUIP_WEAPON_TEXT_FORM_1"] = "Bitte wählen Sie die Stelle, an der Sie Ihr Gerät ausrüsten möchten";
$lang["EQUIP_WEAPON_TEXT_FORM_2"] = "to. Wenn du bereits eine Waffe in dem von dir gewählten Schlitz hältst, wird er wieder in dein Inventar verschoben.";
$lang["EQUIP_WEAPON_EQUIPAS"] = "Equip As";
$lang["EQUIP_ARMOR_TITLE"] = "Ausrüstung rüsten";
$lang["EQUIP_ARMOR_TEXT_FORM_1"] = "Du versuchst, deine Ausrüstung auszustatten";
$lang["EQUIP_ARMOR_TEXT_FORM_2"] = "zu deinem Rüstungsschlitz.Wenn du schon eine Rüstung tragst, wird sie wieder in dein Inventar verschoben.";
$lang["EQUIP_NOTARMOR"] = "Der Gegenstand, den du ausrüsten willst, kann nicht als Rüstung ausgerüstet werden.";
$lang["EQUIP_NOTARMOR_TITLE"] = "Ungültige Rüstung!";
$lang["EQUIP_OFF_ERROR1"] = "Sie versuchen, ein Element aus einem nicht vorhandenen Slot aufzuheben.";
$lang["EQUIP_OFF_ERROR2"] = "Sie haben kein Element in diesem Steckplatz.";
$lang["EQUIP_OFF_SUCCESS"] = "Sie haben das Element erfolgreich aus Ihrem";
$lang["EQUIP_OFF_SUCCESS1"] = "Steckplatz".

// Mitarbeiter abfragen
$lang["STAFF_POLL_TITLE"] = "Abfrageverwaltung";
$lang["STAFF_POLL_TITLES"] = "Eine Umfrage starten";
$lang["STAFF_POLL_TITLEE"] = "Eine Umfrage beenden";
$lang["STAFF_POLL_START_INFO"] = "Stellen Sie eine Frage, dann geben Sie einige mögliche Antworten.";
$lang["STAFF_POLL_START_CHOICE"] = "Auswahl #";
$lang["STAFF_POLL_START_QUESTION"] = "Frage";
$lang["STAFF_POLL_START_HIDE"] = "Ergebnisse bis zum Ende der Umfrage ausblenden";
$lang["STAFF_POLL_START_BUTTON"] = "Abfrage erstellen";
$lang["STAFF_POLL_START_ERROR"] = "Sie müssen eine Frage haben und mindestens zwei Antworten!";
$lang["STAFF_POLL_START_SUCCESS"] = "Sie haben eine Umfrage erfolgreich zum Spiel geöffnet.";
$lang["STAFF_POLL_END_SUCCESS"] = "Sie haben eine aktive Umfrage erfolgreich abgeschlossen.";
$lang["STAFF_POLL_END_FORM"] = "Bitte wählen Sie die Umfrage, die Sie schließen möchten.";
$lang["STAFF_POLL_END_BTN"] = "Ausgewählte Abfrage schließen";
$lang["STAFF_POLL_END_ERR"] = "Du versuchst, eine nicht existierende Umfrage zu schließen.";

// Abruf
$lang["POLL_TITLE"] = "Abrufstelle";
$lang["POLL_CYV"] = "Stimme heute ab!";
$lang["POLL_VOP"] = "Bisher geöffnete Umfragen anzeigen";
$lang["POLL_AVITP"] = "Du kannst nur einmal pro Umfrage abstimmen.";
$lang["POLL_PCNT"] = "Sie können nicht in einer Umfrage abstimmen, die nicht existiert oder zuvor geschlossen wurde.";
$lang["POLL_VOTE_SUCCESS"] = "In dieser Umfrage haben Sie erfolgreich Ihre Stimme abgegeben.";
$lang["POLL_VOTE_NOPOLL"] = "Es werden keine Umfragen geöffnet.";
$lang["POLL_VOTE_CHOICE"] = "Auswahl";
$lang["POLL_VOTE_VOTES"] = "Votes";
$lang["POLL_VOTE_PERCENT_VOTES"] = "Prozentsatz";
$lang["POLL_VOTE_AV"] = "(bereits abgestimmt!)";
$lang["POLL_VOTE_NV"] = "(nicht abgestimmt!)";
$lang["POLL_VOTE_HIDDEN"] = "Die Ergebnisse dieser Umfrage sind bis zu ihrem Ende verborgen.";
$lang["POLL_VOTE_QUESTION"] = "Frage:";
$lang["POLL_VOTE_YVOTE"] = "Ihre Abstimmung:";
$lang["POLL_VOTE_TVOTE"] = "Gesamtzahl Stimmen:";
$lang["POLL_VOTE_VOTEC"] = "Auswählen";
$lang["POLL_VOTE_CAST"] = "Stimmenbewertung";
$lang["POLL_VOTE_NOCLOSED"] = "In diesem Moment gibt es keine geschlossenen Umfragen. Kommen Sie später zurück, wenn die Mitarbeiter eine Umfrage schließen.";

// Forum Mitarbeiter
$lang["STAFF_FORUM_ADD"] = "Forum-Kategorie hinzufügen";
$lang["STAFF_FORUM_EDIT"] = "Forum-Kategorie bearbeiten";
$lang["STAFF_FORUM_DEL"] = "Forum-Kategorie löschen";
$lang["STAFF_FORUM_ADD_NAME"] = "Forum-Name";
$lang["STAFF_FORUM_ADD_DESC"] = "Forum Beschreibung";
$lang["STAFF_FORUM_ADD_AUTHORIZE"] = "Berechtigung";
$lang["STAFF_FORUM_ADD_AUTHORIZEP"] = "Öffentlich";
$lang["STAFF_FORUM_ADD_AUTHORIZES"] = "Nur Personal";
$lang["STAFF_FORUM_ADD_BTN"] = "Forum erstellen";
$lang["STAFF_FORUM_ADD_ERRNAME"] = "Die Eingabe des Forumsnamens war ungültig oder leer. Bitte prüfen Sie es erneut und versuchen Sie es erneut.";
$lang["STAFF_FORUM_ADD_ERRDESC"] = "Die Eingabe der Forenbeschreibung war ungültig oder leer. Bitte prüfen Sie es erneut und versuchen Sie es erneut.";
$lang["STAFF_FORUM_ADD_ERRNIU"] = "Der von Ihnen gewählte Forumname wird bereits verwendet. Bitte versuchen Sie es erneut mit einem neuen Namen.";
$lang["STAFF_FORUM_ADD_SUCCESS"] = "Sie haben dem Spiel eine Forumkategorie hinzugefügt.";
$lang["STAFF_FORUM_EDIT_ERRINV"] = "Du hast eine ungültige Forum-ID angegeben und versuche es erneut.";
$lang["STAFF_FORUM_EDIT_BTN"] = "Forum bearbeiten";
$lang["STAFF_FORUM_EDIT_ERREMPTY"] = "Ein oder mehrere Eingaben auf der vorherigen Seite sind leer. Bitte füllen Sie das Formular aus und versuchen Sie es erneut.";
$lang["STAFF_FORUM_EDIT_SUCCESS"] = "Sie haben das Forum erfolgreich bearbeitet.";
$lang["STAFF_FORUM_DEL_BTN"] = "Forum löschen";
$lang["STAFF_FORUM_DEL_INFO"] = "Das Löschen von Foren ist permenant, das auch die Beiträge in ihnen entfernen wird.";
$lang["STAFF_FORUM_EDIT_ERRFDNE"] = "Das Forum, das du gelöscht hast, existiert nicht. Gehe zurück und überprüfe es und versuche es erneut.";
$lang["STAFF_FORUM_DEL_SUCCESS"] = "Das Forum wurde gelöscht, zusammen mit allen Themen und Beiträgen, die zuvor in ihnen waren.";

// Element verwenden
$lang["IU_UI"] = "Sie versuchen, ein nicht spezifiziertes Element zu verwenden. Überprüfen Sie Ihren Link und versuchen Sie es erneut!";
$lang["IU_UNUSED_ITEM"] = "Dieses Element ist nicht für die Verwendung konfiguriert.";
$lang["IU_ITEM_NOEXIST"] = "Das Element, das Sie verwenden möchten, existiert nicht. Überprüfen Sie Ihre Quellen und versuchen Sie es erneut.";
$lang["IU_SUCCESS"] = "wurde erfolgreich verwendet. Aktualisieren, damit die Änderungen wirksam werden.";

// Personaleinzelheiten
$lang["STAFF_ITEM_GIVE_TITLE"] = "Artikel an Benutzer übergeben";
$lang["STAFF_ITEM_GIVE_FORM_USER"] = "Benutzer";
$lang["STAFF_ITEM_GIVE_FORM_ITEM"] = "Item";
$lang["STAFF_ITEM_GIVE_FORM_QTY"] = "Menge";
$lang["STAFF_ITEM_GIVE_FORM_BTN"] = "Objekt geben";
$lang["STAFF_ITEM_GIVE_SUB_NOITEM"] = "Sie haben den Artikel, den Sie dem Benutzer geben möchten, nicht angegeben.";
$lang["STAFF_ITEM_GIVE_SUB_NOQTY"] = "Sie haben nicht den Betrag des Artikels angegeben, den Sie dem Benutzer geben wollen.";
$lang["STAFF_ITEM_GIVE_SUB_NOUSER"] = "Sie haben keinen Benutzer angeben, dem Sie einen Eintrag geben wollen.";
$lang["STAFF_ITEM_GIVE_SUB_ITEMDNE"] = "Das Element, das du verschenkst, existiert nicht.";
$lang["STAFF_ITEM_GIVE_SUB_USERDNE"] = "Der Benutzer, den Sie versuchen, einen Eintrag zu geben, ist nicht vorhanden.";
$lang["STAFF_ITEM_GIVE_SUB_SUCCESS"] = "Item (s) wurden erfolgreich beworben.";

// Stabsverbrechen
$lang["STAFF_CRIME_TITLE"] = "Verbrechen";
$lang["STAFF_CRIME_MENU_CREATE"] = "Verbrechen schaffen";
$lang["STAFF_CRIME_MENU_CREATECG"] = "Crime-Gruppe erstellen";
$lang["STAFF_CRIME_NEW_TITLE"] = "Hinzufügen eines neuen Verbrechens.";
$lang["STAFF_CRIME_NEW_NAME"] = "Verbrechename";
$lang["STAFF_CRIME_NEW_BRAVECOST"] = "Tapferkeitskosten";
$lang["STAFF_CRIME_NEW_SUCFOR"] = "Erfolgsformel";
$lang["STAFF_CRIME_NEW_SUCPRIMIN"] = "Erfolg Minimum {$lang['INDEX_PRIMCURR']}";
$lang["STAFF_CRIME_NEW_SUCPRIMAX"] = "Erfolg Maximum {$lang['INDEX_PRIMCURR']}";
$lang["STAFF_CRIME_NEW_SUCSECMIN"] = "Erfolg Minimum {$lang['INDEX_SECCURR']}";
$lang["STAFF_CRIME_NEW_SUCSECMAX"] = "Erfolg Maximum {$lang['INDEX_SECCURR']}";
$lang["STAFF_CRIME_NEW_SUCITEM"] = "Erfolgsposition";
$lang["STAFF_CRIME_NEW_GROUP"] = "Verbrechen Gruppe";
$lang["STAFF_CRIME_NEW_ITEXT"] = "Initial Text";
$lang["STAFF_CRIME_NEW_ITEXT_PH"] = "Der Text, der beim Starten des Verbrechens angezeigt wird.";
$lang["STAFF_CRIME_NEW_STEXT"] = "Success Text";
$lang["STAFF_CRIME_NEW_STEXT_PH"] = "Der Text, der angezeigt wird, wenn es dem Spieler gelingt, das Verbrechen zu begehen.";
$lang["STAFF_CRIME_NEW_JTEXT"] = "Fehlertext";
$lang["STAFF_CRIME_NEW_JTEXT_PH"] = "Der Text, der angezeigt wird, wenn der Spieler das Verbrechen versäumt.";
$lang["STAFF_CRIME_NEW_JTIMEMIN"] = "Minimum Dungeon Time";
$lang["STAFF_CRIME_NEW_JTIMEMAX"] = "Maximale Dungeonzeit";
$lang["STAFF_CRIME_NEW_JREASON"] = "Dungeon Reason";
$lang["STAFF_CRIME_NEW_XP"] = "Erfolgserlebnis";
$lang["STAFF_CRIME_NEW_BTN"] = "Verbrechen schaffen";
$lang["STAFF_CRIME_NEW_FAIL1"] = "Sie fehlen eine der erforderlichen Eingaben aus dem vorherigen Formular.";
$lang["STAFF_CRIME_NEW_FAIL2"] = "Die von Ihnen gewählte Option scheint im Spiel nicht zu existieren.";
$lang["STAFF_CRIME_NEW_SUCCESS"] = "Du hast dem Spiel ein Verbrechen hinzugefügt.";
$lang["STAFF_CRIMEG_NEW_TITLE"] = "Hinzufügen einer neuen Verbrechengruppe.";
$lang["STAFF_CRIMEG_NEW_NAME"] = "Name der Verbrechengruppe";
$lang["STAFF_CRIMEG_NEW_ORDER"] = "Verbrechen Gruppe Ordnung";
$lang["STAFF_CRIMEG_NEW_BTN"] = "Crime-Gruppe erstellen";
$lang["STAFF_CRIMEG_NEW_FAIL1"] = "Mindestens einer der beiden Eingaben auf dem vorherigen Formular ist leer.";
$lang["STAFF_CRIMEG_NEW_FAIL2"] = "Sie können keine Verbrechengruppen-Aktienauftragswerte haben.";
$lang["STAFF_CRIMEG_NEW_SUCCESS"] = "Du hast erfolgreich eine Verbrechengruppe angelegt.";

// Mitarbeiter Benutzer
$lang["STAFF_USERS_EDIT_START"] = "Wenn Sie dieses Formular abschicken, können Sie einen beliebigen Aspekt des ausgewählten Spielers bearbeiten.";
$lang["STAFF_USERS_EDIT_USER"] = "Benutzer:";
$lang["STAFF_USERS_EDIT_ELSE"] = "Oder Sie können manuell eine Benutzer-ID eingeben.";
$lang["STAFF_USERS_EDIT_EMPTY"] = "Sie haben einen ungültigen Benutzer eingegeben. Gehen Sie zurück und versuchen Sie es erneut.";
$lang["STAFF_USERS_EDIT_DND"] = "Der von Ihnen eingegebene Benutzer existiert nicht.";
$lang["STAFF_USERS_EDIT_BTN"] = "Benutzer bearbeiten";
$lang["STAFF_USERS_DEL_BTN"] = "Benutzer löschen";
$lang["STAFF_USERS_EDIT_FORMTITLE"] = "Benutzer bearbeiten";
$lang["STAFF_USERS_EDIT_FORM_INFIRM"] = "Infirmary Time";
$lang["STAFF_USERS_EDIT_FORM_INFIRM_REAS"] = "Infirmary Reason";
$lang["STAFF_USERS_EDIT_FORM_DUNG"] = "Dungeon Time";
$lang["STAFF_USERS_EDIT_FORM_DUNG_REAS"] = "Dungeon Reason";
$lang["STAFF_USERS_EDIT_FORM_ESTATE"] = "Estate";
$lang["STAFF_USERS_EDIT_FORM_STATS"] = "Benutzerstatistik";
$lang["STAFF_USERS_EDIT_SUB_MISSINGSTUFF"] = "Auf der vorherigen Seite fehlen einige erforderliche Informationen, gehen Sie zurück und versuchen Sie es erneut.";
$lang["STAFF_USERS_EDIT_SUB_ULBAD"] = "Sie haben eine ungültige Benutzerebene festgelegt. Gehen Sie zurück und versuchen Sie es erneut.";
$lang["STAFF_USERS_EDIT_SUB_UNIU"] = "Der angegebene Benutzername wird bereits verwendet. Gehe zurück und speziere einen neuen.";
$lang["STAFF_USERS_EDIT_SUB_HBAD"] = "Das von Ihnen angegebene Haus ist ungültig oder nicht vorhanden. Gehen Sie zurück und versuchen Sie es erneut.";
$lang["STAFF_USERS_EDIT_SUB_EIU"] = "Die E-Mail-Eingabe wird bereits von einem anderen Konto verwendet. Gehen Sie zurück und geben Sie eine unausgerufene und gültige E-Mail-Adresse ein.";
$lang["STAFF_USERS_EDIT_SUB_SUCCESS"] = "Die Benutzerdaten wurden erfolgreich aktualisiert.";
$lang["STAFF_USERS_EDIT_SUB_WDNE"] = "Eine der von Ihnen angegebenen Waffen existiert nicht oder kann nicht als Waffe ausgerüstet werden. Gehen Sie zurück und versuchen Sie es erneut.";
$lang["STAFF_USERS_EDIT_SUB_ADNE"] = "Die Rüstung, die du angegeben hast, existiert nicht oder kann nicht als Rüstung ausgerüstet werden. Gehe zurück und versuche es erneut.";
$lang["STAFF_USERS_EDIT_SUB_TDNE"] = "Die Stadt, die Sie gewählt haben, existiert nicht, gehen Sie zurück und versuchen Sie es erneut.";
$lang["STAFF_USERS_DEL_FORM_1"] = "Sie können dieses Formular verwenden, um einen Benutzer aus dem Spiel zu löschen. Diese Aktion ist nicht reverisble.";
$lang["STAFF_USERS_DEL_SUB_SECERROR"] = "Sie haben einen ungültigen oder nicht vorhandenen Benutzer angegeben. Gehen Sie zurück und versuchen Sie es erneut.";
$lang["STAFF_USERS_DEL_SUBFORM_CONFIRM"] = "Bitte bestätigen Sie, dass Sie löschen möchten";
$lang["STAFF_USERS_DEL_SUBFORM_CONFIRM1"] = "Nachdem sie gelöscht wurden, können sie sich nicht mehr von ihrem Konto anmelden.";
$lang["STAFF_USERS_DEL_SUB_INVALID"] = "Sie haben einen Benutzer oder einen Befehl angegeben, der ungültig ist.";
$lang["STAFF_USERS_DEL_SUB_FAIL"] = "Benutzer wurde nicht gelöscht.";
$lang["STAFF_USERS_DEL_SUB_SUCC"] = "Benutzer wurde aus dem Spiel gelöscht.";
$lang["STAFF_USERS_FL_SUB_SUCC"] = "Benutzer wurde erfolgreich aus dem Spiel abgemeldet.";
$lang["STAFF_USERS_FL_FORM_INFO"] = "Verwenden Sie dieses Formular, um eine Verwendung automatisch abzumelden, wenn bei ihrer nächsten Aktion im Spiel.";
$lang["STAFF_USERS_FL_FORM_BTN"] = "Benutzer abmelden";

//Akademie
$lang["STAFF_ACADEMY_ADD"] = "Akademischer Kurs erstellen";
$lang["STAFF_ACADEMY_DEL"] = "Akademischer Kurs entfernen";
$lang["STAFF_ACADEMY_NAME"] = "Name der Akademie";
$lang["STAFF_ACADEMY_DESC"] = "Akademiebeschreibung";
$lang["STAFF_ACADEMY_COST"] = "Akademiekosten";
$lang["STAFF_ACADEMY_LVL"] = "Akademische Mindeststufe";
$lang["STAFF_ACADEMY_DAYS"] = "Akademie Tage";
$lang["STAFF_ACADEMY_PERKS"] = "Academy Perks";
$lang["STAFF_ACADEMY_PERK"] = "Perk";
$lang["STAFF_ACADEMY_TOGGLE_DISP"] = "Geben Sie Perk?";
$lang["STAFF_ACADEMY_TOGGLE_ON"] = "Ja!";
$lang["STAFF_ACADEMY_TOGGLE_OFF"] = "Nein!";
$lang["STAFF_ACADEMY_STAT"] = "Given Effect";
$lang["STAFF_ACADEMY_OPTION_1"] = "Stärke";
$lang["STAFF_ACADEMY_OPTION_2"] = "Beweglichkeit";
$lang["STAFF_ACADEMY_OPTION_3"] = "Guard";
$lang["STAFF_ACADEMY_OPTION_4"] = "Arbeit";
$lang["STAFF_ACADEMY_OPTION_5"] = "IQ";
$lang["STAFF_ACADEMY_DIRECTION"] = "Richtung";
$lang["STAFF_ACADEMY_INCREASE"] = "Erhöhen";
$lang["STAFF_ACADEMY_DECREASE"] = "Verkleinern";
$lang["STAFF_ACADEMY_AMOUNT"] = "Gegebener Betrag";
$lang["STAFF_ACADEMY_VALUE"] = "Wert";
$lang["STAFF_ACADEMY_PERCENT"] = "Prozentsatz";
$lang["STAFF_ACADEMY_CREATE"] = "Akademie erstellen";
$lang["STAFF_ACADEMY_DELETE_HEADER"] = "Löschen einer Akademie";
$lang["STAFF_ACADEMY_DELETE_NOTICE"] = "Die von Ihnen ausgewählte Akademie wird dauerhaft gelöscht. Es gibt keine Bestätigungsaufforderung, also seien Sie 100% sicher.";
$lang["STAFF_ACADEMY_DELETE_TITLE"] = "Akademie";
$lang["STAFF_ACADEMY_DELETE_BUTTON"] = "Akademie entfernen";
$lang["ACADEMY_DESCRIPTION_EFFECT_1"] = "Dieser Kurs";
$lang["ACADEMY_DESCRIPTION_EFFECT_2"] = "Ihr";
$lang["ACADEMY_DESCRIPTION_EFFECT_3"] = "nach";
$lang["ACADEMY_INFO_NAME"] = "Kursname:";
$lang["ACADEMY_INFO_DESC"] = "Kursbeschreibung:";
$lang["ACADEMY_INFO_COST"] = "Minimale Kosten:";
$lang["ACADEMY_INFO_LEVEL"] = "Mindestanforderung:";
$lang["ACADEMY_INFO_DAYS"] = "zu beendende Tage:";
$lang["ACADEMY_INFO_EFFECT"] = "Fertigstellungseffekt #";
$lang["ACADEMY_STARTED_COURSE"] = "Kurs erfolgreich gestartet!";
$lang["ACADEMY_RETURN_HOME"] = "Return Home";
$lang["ACADEMY_LOW_LEVEL_1"] = "Low Level!";
$lang["ACADEMY_INSUFFICIENT_CURRENCY_1"] = "Short on Currency!";
$lang["ACADEMY_IN_COURSE_1"] = "In Kurs";
$lang["ACADEMY_LOW_LEVEL_2"] = "Versuchen Sie, mehr Level zu gewinnen, bevor Sie diesen Kurs versuchen";
$lang["ACADEMY_INSUFFICIENT_CURRENCY_2"] = "Probieren Sie mehr Primärwährung, bevor Sie an diesem Kurs teilnehmen";
$lang["ACADEMY_IN_COURSE_2"] = "Sie befinden sich bereits in einem Kurs! Bitte warten Sie, bis der Vorgang abgeschlossen ist und versuchen Sie es erneut.";
$lang["ACADEMY_IN_COURSE_3"] = "Tage";

// Kriminalzentrum
$lang["CRIME_TITLE"] = "Kriminalzentrum";
$lang["CRIME_ERROR_JI"] = "Nur die gesunden und freien Individuen können Verbrechen begehen.";
$lang["CRIME_TABLE_CRIME"] = "Verbrechen";
$lang["CRIME_TABLE_CRIMES"] = "Verbrechen";
$lang["CRIME_TABLE_COST"] = "Kosten";
$lang["CRIME_TABLE_COMMIT"] = "Commit";
$lang["CRIME_COMMIT_INVALID"] = "Sie versuchen, entweder ein nicht existierendes Verbrechen oder ein unvollendetes zu begehen. Versuchen Sie es erneut, und wenn das Problem weiterhin besteht, wenden Sie sich bitte an einen Admin. ";
$lang["CRIME_COMMIT_BRAVEBAD"] = "Du bist nicht mutig genug, dieses Verbrechen zu begehen, komm später zurück.";

//Attack
$lang["ATTACK_START_NOREFRESH"] = "Das Erfrischen während des Angriffs ist ein bannbares Vergehen.";
$lang["ATTACK_START_NOUSER"] = "Du kannst nur Spieler angreifen, die angegriffen wurden.";
$lang["ATTACK_START_NOTYOU"] = "Depressiv oder nicht, du kannst dich nicht selbst angreifen!";
$lang["ATTACK_START_THEYLOWLEVEL"] = "Du kannst nicht Spieler unter Stufe 2 angreifen, die auch online sind.";
$lang["ATTACK_START_YOUNOHP"] = "Du brauchst HP, um jemanden zu bekämpfen, komm zurück, wenn du mehr Gesundheit hast!";
$lang["ATTACK_START_YOUINFIRM"] = "Wie erwarten Sie, jemanden zu bekämpfen, wenn Sie einen Krankenpfleger im Krankenhaus pflegen?";
$lang["ATTACK_START_YOUDUNG"] = "Wie erwarten Sie, jemanden zu bekämpfen, wenn Sie Ihre Schulden der Gesellschaft im Verlies dienen?";
$lang["ATTACK_START_YOUCHICKEN"] = "Chickening aus einem Kampf, und das Laufen, um ein anderes zu beginnen ist keine ehrenhafte Art zu spielen.";
$lang["ATTACK_START_NONUSER"] = "Die Person, mit der Sie einen Groll haben, existiert nicht. Überprüfen Sie Ihre Quelle und versuchen Sie es erneut.";
$lang["ATTACK_START_UNKNOWNERROR"] = "Es ist ein unbekannter Fehler aufgetreten, gehen Sie zurück und versuchen Sie es erneut.";
$lang["ATTACK_START_OPPNOHP"] = "auf HP niedrig. Kommen Sie zurück, wenn sie mehr Gesundheit haben.";
$lang["ATTACK_START_OPPINFIRM"] = "ist im Krankenhaus im Moment, komm zurück, wenn sie raus!";
$lang["ATTACK_START_OPPDUNG"] = "ist im Kerker im Moment, komm zurück, wenn sie raus!";
$lang["ATTACK_START_OPPUNATTACK"] = "Dieser Benutzer kann nicht mit normalen Mitteln angegriffen werden.";
$lang["ATTACK_START_YOUUNATTACK"] = "Eine magische Kraft verhindert, dass du jemanden angreifst.";
$lang["ATTACK_FIGHT_STALEMATE"] = "Kommt zurück, wenn ihr stärker seid. Dieser Kampf endet im Patt.";
$lang["ATTACK_FIGHT_LOWENG1"] = "Du hast nicht genug Energie für diesen Kampf.";
$lang["ATTACK_FIGHT_LOWENG2"] = "% Sie haben nur";
$lang["ATTACK_FIGHT_BUGABUSE"] = "Missbrauch von Spielfehlern ist gegen die Spielregeln. Du verlierst deine Erfahrung und gehst auf die Krankenstation.";
$lang["ATTACK_FIGHT_BADWEAP"] = "Die Waffe, mit der du angreifen willst, existiert nicht oder kann nicht als Waffe benutzt werden.";
$lang["ATTACK_FIGHT_ATTACKY_HIT1"] = "Mit Ihrem";
$lang["ATTACK_FIGHT_ATTACKY_HIT2"] = "Sie treffen";
$lang["ATTACK_FIGHT_ATTACKY_HIT3"] = "machen";
$lang["ATTACK_FIGHT_ATTACKY_HIT4"] = "Schaden";
$lang["ATTACK_FIGHT_ATTACKY_MISS1"] = "Du hast versucht zu schlagen";
$lang["ATTACK_FIGHT_ATTACKY_MISS2"] = "aber verpasst.";
$lang["ATTACK_FIGHT_ATTACKY_WIN1"] = "Sie haben bested";
$lang["ATTACK_FIGHT_ATTACKY_WIN2"] = "im Kampf, was willst du mit ihnen jetzt machen?";
$lang["ATTACK_FIGHT_OUTCOME1"] = "Becher";
$lang["ATTACK_FIGHT_OUTCOME2"] = "Beat";
$lang["ATTACK_FIGHT_OUTCOME3"] = "Verlassen";
$lang["ATTACK_FIGHT_ATTACK_HPREMAIN"] = "HP verbleibend";
$lang["ATTACK_FIGHT_ATTACK_FISTS"] = "Fists";
$lang["ATTACK_FIGHT_ATTACKO_HIT1"] = "Benutzung ihrer";
$lang["ATTACK_FIGHT_ATTACKO_HIT2"] = "schlägst du";
$lang["ATTACK_FIGHT_ATTACKO_MISS"] = "versucht, dich zu schlagen, aber verpasst.";
$lang["ATTACK_FIGHT_FINAL_GUILD"] = "ist in der selben Gilde wie ihr! Ihr könnt Eure Gildenkollegen nicht angreifen!";
$lang["ATTACK_FIGHT_FINAL_CITY"] = "Dieser Spieler ist nicht in der gleichen Stadt wie Ihr. Ihr müsst beide in der gleichen Stadt sein, um gegeneinander zu kämpfen.";
$lang["ATTACK_FIGHT_START1"] = "Wähle eine Waffe, mit der du angreifen kannst.";
$lang["ATTACK_FIGHT_START2"] = "Du hast keine Waffe, mit der du angreifen kannst.";
$lang["ATTACK_FIGHT_END"] = "Sie haben bested";
$lang["ATTACK_FIGHT_END1"] = "Du hast sie im Kampf besiegt!";
$lang["ATTACK_FIGHT_END2"] = "Ein böser Gedanke kommt dir in den Sinn, wenn du deinen unbewußten Körper anstarrst. Du brechst ihnen den Hals und kickst sie, bis sie bluten.";
$lang["ATTACK_FIGHT_END3"] = "Ihre Handlungen verursachen";
$lang["ATTACK_FIGHT_END4"] = "der Krankenstationzeit.";
$lang["ATTACK_FIGHT_END5"] = "Sie fielen auf";
$lang["ATTACK_FIGHT_END6"] = "Du hast diesen Kampf verloren und hast deine Erfahrung als Krieger verloren!";
$lang["ATTACK_FIGHT_END7"] = "Da du ein ehrenhafter Krieger bist, nimmst du sie zum Krankenflügel-Eingang.";
$lang["ATTACK_FIGHT_END8"] = "Als gieriger Krieger wirfst du einen Blick auf ihre Taschen und greifst einige ihrer Primärwährung.";

// Element-Info-Seite
$lang["ITEM_INFO_LUIF"] = "Elementinformationen anzeigen für";
$lang["ITEM_INFO_TYPE"] = "Typ";
$lang["ITEM_INFO_SPRICE"] = "Verkaufspreis";
$lang["ITEM_INFO_BPRICE_NO"] = "Item kann nicht im Spiel gekauft werden.";
$lang["ITEM_INFO_SPRICE_NO"] = "Item kann nicht im Spiel verkauft werden.";
$lang["ITEM_INFO_BPRICE"] = "Kaufpreis";
$lang["ITEM_INFO_WEAPON_HURT"] = "Waffenbewertung";
$lang["ITEM_INFO_ARMOR_HURT"] = "Rüstungswertung";
$lang["ITEM_INFO_INFO"] = "Info";
$lang["ITEM_INFO_ITEM"] = "Item";
$lang["ITEM_INFO_EFFECT"] = "Effekt #";
$lang["ITEM_INFO_BY"] = "nach";

// Item verkaufen
$lang["ITEM_SELL_INFO"] = "Artikelverkauf";
$lang["ITEM_SELL_FORM1"] = "Sie versuchen zu verkaufen";
$lang["ITEM_SELL_FORM2"] = "zurück zum Spiel.Geben Sie ein, wie viele Sie verkaufen möchten.";
$lang["ITEM_SELL_FORM3"] = "zu verkaufen.";
$lang["ITEM_SELL_SUCCESS1"] = "Sie haben erfolgreich verkauft";
$lang["ITEM_SELL_SUCCESS2"] = "(s) for";
$lang["ITEM_SELL_BTN"] = "Verkaufe Gegenstände";
$lang["ITEM_SELL_ERROR1_TITLE"] = "Fehlende Elemente!";
$lang["ITEM_SELL_BAD_QTY"] = "Sie versuchen, mehr Elemente zu verkaufen, als Sie derzeit auf Lager haben.";
$lang["ITEM_SELL_ERROR1"] = "Sie versuchen, ein Element zu verkaufen, das Sie nicht haben, oder es existiert nicht. Überprüfen Sie Ihre Quelle und versuchen Sie es erneut.";

// Stellenangebote
$lang["STAFF_JOB_CREATE_TITLE"] = "Job erstellen";
$lang["STAFF_JOB_CREATE_FORM_NAME"] = "Auftragsname";
$lang["STAFF_JOB_CREATE_FORM_DESC"] = "Jobbeschreibung";
$lang["STAFF_JOB_CREATE_FORM_BOSS"] = "Job-Manager";
$lang["STAFF_JOB_CREATE_FORM_FIRST"] = "Erster Jobrang";
$lang["STAFF_JOB_CREATE_FORM_RNAME"] = "Rangname";
$lang["STAFF_JOB_CREATE_FORM_PAYS"] = "Tägliche Zahlung";
$lang["STAFF_JOB_CREATE_FORM_ACT"] = "Erforderliche Aktivität";

// Mitarbeiterprotokolle
$lang["STAFF_LOGS_USERS_FORM"] = "Wählen Sie den Benutzer aus, dessen Protokolle Sie anzeigen möchten.";
$lang["STAFF_LOGS_USERS_FORM_BTN"] = "Protokolle anzeigen";

// Geschäfte
$lang["SHOPS_HOME_INTRO"] = "Du schaust durch die Stadt und siehst ein paar Geschäfte.";
$lang["SHOPS_HOME_OH"] = "Diese Stadt ist sicher nicht weit genug entwickelt, um Geschäfte zu haben, wie?";
$lang["SHOPS_HOME_TH_1"] = "Name des Geschäftes";
$lang["SHOPS_HOME_TH_2"] = "Beschreibung des Shops";
$lang["SHOPS_SHOP_TH_1"] = "Elementname";
$lang["SHOPS_SHOP_TH_2"] = "Preis";
$lang["SHOPS_SHOP_TH_3"] = "Kaufen";
$lang["SHOPS_SHOP_TD_1"] = "Menge:";
$lang["SHOPS_SHOP_INFO"] = "Sie beginnen mit dem Durchsuchen der Elemente auf";
$lang["SHOPS_BUY_ERROR1"] = "Sie versuchen, diese Datei nicht korrekt zu verwenden. Stellen Sie sicher, dass Sie sowohl einen Artikel als auch eine Menge angegeben haben.";
$lang["SHOPS_BUY_ERROR2"] = "YDas Element, das Sie kaufen möchten, existiert nicht, wird nicht in diesem Laden verkauft oder existiert nicht!";
$lang["SHOPS_SHOP_ERROR1"] = "Sie versuchen, auf einen Shop in einer anderen Stadt zuzugreifen, als Sie derzeit sind!";
$lang["SHOPS_SHOP_ERROR2"] = "Sie versuchen, auf einen Shop zuzugreifen, der ungültig ist oder nicht existiert. Überprüfen Sie Ihre Quelle und versuchen Sie es erneut!";
$lang["SHOPS_BUY_ERROR3"] = "Sie haben nicht genug Primärwährung, um zu kaufen";
$lang["SHOPS_BUY_ERROR4"] = "Das Element, das Sie kaufen möchten, ist nicht auf normale Weise zu erwerben.";
$lang["SHOPS_BUY_SUCCESS"] = "Sie haben erfolgreich gekauft";
$lang["SHOPS_BUY_ERROR5"] = "Sie können keine Artikel aus Geschäften außerhalb der Stadt kaufen, in der Sie sich gerade befinden. Überprüfen Sie Ihre Quelle und versuchen Sie es erneut.";

// Fachgeschäfte
$lang["STAFF_SHOP_FORM_TITLE"] = "Verwenden Sie dieses Formular, um einen neuen Shop zu erstellen.";
$lang["STAFF_SHOP_FORM_OPTION1"] = "Name des Geschäftes";
$lang["STAFF_SHOP_FORM_OPTION2"] = "Beschreibung des Shops";
$lang["STAFF_SHOP_FORM_OPTION3"] = "Shop's Location";
$lang["STAFF_SHOP_FORM_BTN"] = "Shop erstellen";
$lang["STAFF_SHOP_SUB_ERROR1"] = "Der Shop-Name oder die Beschreibung ist leer. Gehen Sie zurück und versuchen Sie es erneut.";
$lang["STAFF_SHOP_SUB_ERROR2"] = "Der Standort, den Sie für den Shop ausgewählt haben, existiert nicht.";
$lang["STAFF_SHOP_SUB_ERROR3"] = "Ein Shop mit dem von Ihnen angegebenen Namen ist bereits vorhanden!";
$lang["STAFF_SHOP_SUB_SUCCESS"] = "Shop wurde erfolgreich erstellt.";
$lang["STAFF_SHOP_DELFORM_TITLE"] = "Wenn Sie einen Laden aus dem Spiel löschen, wird er aus dem Spiel entfernt. Achten Sie auf diese Aktion, da es keine Bestätigung gibt.";
$lang["STAFF_SHOP_DELFORM_FORM"] = "Shop:";
$lang["STAFF_SHOP_DELFORM_FORM_BTN"] = "Shop löschen";
$lang["STAFF_SHOP_DELFORM_SUB_ERROR1"] = "Der Shop ist ungültig oder existiert nicht, vielleicht haben Sie ihn vorher gelöscht?";
$lang["STAFF_SHOP_DELFORM_SUB_SUCCESS"] = "Der Shop wurde erfolgreich aus dem Spiel entfernt.";
$lang["STAFF_SHOP_IADDFORM_TITLE"] = "Verwenden Sie dieses Formular, um ein Element zu einem Shop hinzuzufügen.";
$lang["STAFF_SHOP_IADDFORM_TD1"] = "Item:";
$lang["STAFF_SHOP_IADDFORM_BTN"] = "Artikel zum Shop hinzufügen";
$lang["STAFF_SHOP_IADDSUB_ERROR"] = "Sie versuchen, ein Element zu einem ungültigen Geschäft hinzuzufügen oder ein ungültiges Element für einen Shop. Gehen Sie zurück und versuchen Sie es erneut.";
$lang["STAFF_SHOP_IADDSUB_ERROR2"] = "Artikel oder Shop ist ungültig oder nicht vorhanden.";
$lang["STAFF_SHOP_IADDSUB_ERROR3"] = "Das Produkt, das Sie diesem Shop hinzufügen möchten, ist bereits in diesem Shop aufgeführt.";
$lang["STAFF_SHOP_IADDSUB_SUCCESS"] = "Item wurde dem Lagerbestand dieses Shops hinzugefügt.";

// Artikelmarkt
$lang["IMARKET_TITLE"] = "Objektmarkt";
$lang["IMARKET_LISTING_TH1"] = "Listing Owner";
$lang["IMARKET_LISTING_TH2"] = "Element x Menge";
$lang["IMARKET_LISTING_TH3"] = "Preis / Position";
$lang["IMARKET_LISTING_TH4"] = "Gesamtpreis";
$lang["IMARKET_LISTING_TH5"] = "Links";
$lang["IMARKET_LISTING_TD1"] = "Eintrag entfernen";
$lang["IMARKET_LISTING_TD2"] = "Kaufen Listing";
$lang["IMARKET_LISTING_TD3"] = "Geschenkliste";
$lang["IMARKET_REMOVE_ERROR1"] = "Sie müssen einen Artikelmarkt-Eintrag angeben, den Sie ausführen möchten.";
$lang["IMARKET_REMOVE_ERROR2"] = "Der Eintrag, den Sie entfernen möchten, existiert nicht oder Sie sind nicht dessen Eigentümer.";
$lang["IMARKET_REMOVE_SUCCESS"] = "Der Eintrag auf dem Markt wurde erfolgreich entfernt.";
$lang["IMARKET_BUY_ERROR1"] = "Die von Ihnen erworbene Artikelmarke existiert nicht oder wurde bereits gekauft.";
$lang["IMARKET_BUY_START"] = "Geben Sie ein, wie viele";
$lang["IMARKET_BUY_START1"] = "(s), die Sie kaufen möchten.";
$lang["IMARKET_BUY_START2"] = "zum Kauf verfügbar.";
$lang["IMARKET_BUY_SUB_ERROR1"] = "Sie können keine eigenen Artikel aus dem Artikelmarkt erwerben.";
$lang["IMARKET_BUY_SUB_ERROR2"] = "Sie haben nicht genügend Geld, um diese Liste zu kaufen.";
$lang["IMARKET_BUY_SUB_ERROR3"] = "Sie können nicht mehr kaufen als die angegebene Menge.";
$lang["IMARKET_BUY_SUB_SUCCESS"] = "Einzelteile wurden gekauft! Überprüfen Sie Ihr Inventar!";
$lang["IMARKET_GIFT_START1"] = "(s), die Sie kaufen und als Geschenk versenden möchten.";
$lang["IMARKET_GIFT_FORM_TH1"] = "Geschenk senden an:";
$lang["IMARKET_GIFT_SUB_ERROR1"] = "Sie versuchen, ein Geschenk an einen Benutzer zu schicken, der nicht existiert!";
$lang["IMARKET_GIFT_SUB_ERROR2"] = "Sie können nicht kaufen, ein Element aus dem Markt und Geschenk es zurück zu der Person, die es aufgelistet.";
$lang["IMARKET_GIFT_SUB_SUCCESS"] = "Sie haben den Artikel erfolgreich gekauft und als Geschenk verschickt!";
$lang["IMARKET_ADD_TITLE"] = "Füllen Sie dieses Formular aus, um ein Element auf den Markt zu bringen.";
$lang["IMARKET_ADD_TH1"] = "Währungstyp";
$lang["IMARKET_ADD_TH2"] = "Preis pro Element";
$lang["IMARKET_ADD_BTN"] = "Zum Markt hinzufügen";
$lang["IMARKET_ADD_ERROR1"] = "Sie können dem Artikelmarkt keine Elemente hinzufügen.";
$lang["IMARKET_ADD_ERROR2"] = "Sie versuchen, einen Eintrag hinzuzufügen, den Sie nicht besitzen.";
$lang["IMARKET_ADD_ERROR3"] = "Sie haben nicht genug von diesem Element, um die Menge hinzuzufügen, die Sie auf den Markt bringen wollten.";
$lang["IMARKET_ADD_SUB_SUCCESS"] = "Sie haben diesen Eintrag erfolgreich auf dem Positionsmarkt gelistet.";

//Reise
$lang["TRAVEL_TITLE"] = "Pferdereisen";
$lang["TRAVEL_TABLE"] = "Willkommen auf dem Pferdestall, wo Sie in andere Städte reisen können, aber zu einem Preis Es wird Ihnen kosten. ";
$lang["TRAVEL_TABLE2"] = "{$lang['INDEX_PRIMCURR']}, um heute zu reisen.";
$lang["TRAVEL_TABLE_HEADER"] = "Ortsname";
$lang["TRAVEL_TABLE_LEVEL"] = "Mindeststufe";
$lang["TRAVEL_TABLE_GUILD"] = "Gilde";
$lang["TRAVEL_TABLE_TAX"] = "Einkommensteuer";
$lang["TRAVEL_TABLE_TRAVEL"] = "Reise";
$lang["TRAVEL_ERROR_CASHLOW"] = "Sie haben keine enoguh primäre currecy, um zu diesem Ort zu reisen. Gehen Sie zurück und versuchen Sie es erneut.";
$lang["TRAVEL_ERROR_ALREADYTHERE"] = "Du bist schon in dieser Stadt, warum willst du dein Geld verschwenden und wieder hierher reisen?";
$lang["TRAVEL_ERROR_ERRORGEN"] = "Diese Stadt existiert nicht, oder Ihr Niveau ist nicht hoch genug, um diese Stadt zu besichtigen.";
$lang["TRAVEL_SUCCESS"] = "Du hast ein Pferd gekauft und reiste zu";

// Mitarbeiterstädte
$lang["STAFF_TRAVEL_ADD"] = "Stadt hinzufügen";
$lang["STAFF_TRAVEL_EDIT"] = "Stadt bearbeiten";
$lang["STAFF_TRAVEL_DEL"] = "Stadt löschen";
$lang["STAFF_TRAVEL_ADDTOWN_TABLE"] = "Verwenden Sie dieses Formular, um eine Stadt ins Spiel hinzuzufügen.";
$lang["STAFF_TRAVEL_ADDTOWN_TH1"] = "Ortsname";
$lang["STAFF_TRAVEL_ADDTOWN_TH2"] = "Minimum Level";
$lang["STAFF_TRAVEL_ADDTOWN_TH3"] = "Steuerebene";
$lang["STAFF_TRAVEL_ADDTOWN_BTN"] = "Stadt erstellen";
$lang["STAFF_TRAVEL_ADDTOWN_SUB_ERROR1"] = "Sie können eine neue Stadt nach einer bereits existierenden Stadt benennen.";
$lang["STAFF_TRAVEL_ADDTOWN_SUB_ERROR2"] = "Der Steuersatz der Gemeinde muss zwischen 0% und 20% liegen";
$lang["STAFF_TRAVEL_ADDTOWN_SUB_ERROR3"] = "Die Mindestanforderung der Stadt muss größer als 0 sein.";
$lang["STAFF_TRAVEL_ADDTOWN_SUB_SUCCESS"] = "Sie haben diese Stadt erfolgreich hinzugefügt.";
$lang["STAFF_TRAVEL_DELTOWN_TABLE"] = "Verwenden Sie dieses Formular, um eine Stadt aus dem Spiel zu löschen.";
$lang["STAFF_TRAVEL_DELTOWN_TH1"] = "Stadt";
$lang["STAFF_TRAVEL_DELTOWN_BTN"] = "Stadt löschen";
$lang["STAFF_TRAVEL_DELTOWN_SUB_ERROR1"] = "Sie können eine nicht existierende Stadt nicht löschen.";
$lang["STAFF_TRAVEL_DELTOWN_SUB_ERROR2"] = "Sie können die erste Stadt nicht löschen.";
$lang["STAFF_TRAVEL_DELTOWN_SUB_SUCCESS"] = "Stadt wurde erfolgreich gelöscht. Benutzer und Geschäfte in dieser Stadt wurden in die Starterstadt verlegt.";
// Gildenauflistung
$lang["GUILD_LIST"] = "Guildlisting";
$lang["GUILD_LIST_TABLE1"] = "Gildenname";
$lang["GUILD_LIST_TABLE2"] = "Gildenlevel";
$lang["GUILD_LIST_TABLE3"] = "Mitgliedszählung";
$lang["GUILD_LIST_TABLE5"] = "Guild Leader";
$lang["GUILD_LIST_TABLE4"] = "Heimatort";

// Gilden erstellen
$lang["GUILD_CREATE"] = "Eine Gilde erstellen";
$lang["GUILD_CREATE_ERROR"] = "Du hast nicht genug Primärwährung, um eine Gilde kaufen zu können.";
$lang["GUILD_CREATE_ERROR1"] = "Du bist nicht hoch genug, um eine Gilde zu erwerben.";
$lang["GUILD_CREATE_ERROR2"] = "Du kannst keine Gilde erstellen, solange du nicht Mitglied bist.";
$lang["GUILD_CREATE_ERROR3"] = "Du kannst keine Gilde erstellen, die nach einer bereits existierenden Gilde benannt ist.";
$lang["GUILD_CREATE_FORM"] = "Füllen Sie dieses Formular aus, um Ihre Gilde zu erstellen. Die Heimat Ihrer Gilde wird auf die Stadt gesetzt, in der Sie sich derzeit befinden.";
$lang["GUILD_CREATE_FORM1"] = "Gildenname";
$lang["GUILD_CREATE_FORM2"] = "Gildenbeschreibung";
$lang["GUILD_CREATE_BTN"] = "Gilden erstellen";
$lang["GUILD_CREATE_SUCCESS"] = "Du hast eine Gilde erfolgreich erstellt!";

// Gildenbetrachtung
$lang["GUILD_VIEW_GUILD"] = "Gilde";
$lang["GUILD_VIEW_ERROR"] = "Du versuchst, eine nicht existierende Gilde zu sehen. Überprüfe deine Quelle und versuche es erneut.";
$lang["GUILD_VIEW_LEADER"] = "Gildenleiter";
$lang["GUILD_VIEW_COLEADER"] = "Guild Co-Leader";
$lang["GUILD_VIEW_LEVEL"] = "Gildenlevel";
$lang["GUILD_VIEW_MEMBERS"] = "Gildenmitglieder";
$lang["GUILD_VIEW_LOCATION"] = "Gildenstandort";
$lang["GUILD_VIEW_USERS"] = "Gildenmitgliederliste";
$lang["GUILD_VIEW_APPLY"] = "Auf Gilde bewerben";
$lang["GUILD_VIEW_LIST"] = "Mitgliederliste für die";
$lang["GUILD_VIEW_LIST2"] = "Gilde";
$lang["GUILD_VIEW_ERROR"] = "Sie müssen eine Gilde angeben, die Sie anzeigen möchten. Überprüfen Sie Ihre Quelle und versuchen Sie es erneut.";
$lang["GUILD_APP_TITLE"] = "Ausfüllen der Bewerbung";
$lang["GUILD_APP_INFO"] = "Geben Sie einen Grund an, in dem Sie in dieser Gilde sein sollten. Seien Sie höflich, ehrlich und genau mit Ihren Informationen.";
$lang["GUILD_APP_ERROR"] = "Sie können keine Bewerbung an eine Gilde senden, die Sie derzeit in einer Gruppe haben. Verlassen Sie Ihre Gilde und versuchen Sie es erneut.";
$lang["GUILD_APP_BTN"] = "Antrag senden";
$lang["GUILD_APP_ERROR1"] = "Sie haben bereits eine Bewerbung gesendet, um dieser Gilde beizutreten. Bitte warten Sie, bis Sie eine Antwort erhalten, bevor Sie eine andere senden.";
$lang["GUILD_APP_SUCC"] = "Sie haben Ihre Bewerbung erfolgreich an diese Gilde gesendet!";
$lang["GUILD_VIEW_DESC"] = "Beschreibung der Gilde";

// Personalvorschriften
$lang["STAFF_RULES_ADD_FORM"] = "Verwenden Sie dieses Formular, um Regeln in das Spiel zu integrieren, klar und prägnant sein. Je schwieriger Sprache und Terminologie Sie verwenden, desto weniger Menschen können das verstehen.";
$lang["STAFF_RULES_ADD_BTN"] = "Regel hinzufügen";
$lang["STAFF_RULES_ADD_SUBFAIL"] = "Sie können keine Regel zu einer leeren Regel hinzufügen.";
$lang["STAFF_RULES_ADD_SUBSUCC"] = "Sie haben eine neue Regel erfolgreich erstellt.";

//Spielregeln
$lang["GAMERULES_TITLE"] = "Regeln";
$lang["GAMERULES_TEXT"] = "Es wird erwartet, dass Sie diesen Regeln folgen, und Sie werden auch erwartet, dass Sie diese regelmäßig wiederholen, da diese Regeln ohne vorherige Ankündigung geändert werden können Regeln. ";

// Anzeigen GUILD_APP_BTN
$lang["VIEWGUILD_ERROR1"] = "Du bist nicht in einer Gilde, deshalb kannst du die Informationen deiner Gilde nicht sehen.";
$lang["VIEWGUILD_ERROR2"] = "Es sieht so aus, als ob deine Gilde gelöscht wurde.";
$lang["VIEWGUILD_TITLE"] = "Ihre Gilde";
$lang["VIEWGUILD_HOME_SUMMARY"] = "Gildenübersicht";
$lang["VIEWGUILD_HOME_DONATE"] = "Spenden für die Gilde";
$lang["VIEWGUILD_HOME_CRIME"] = "Gildenverbrechen";
$lang["VIEWGUILD_HOME_USERS"] = "Gildenmitgliederliste";
$lang["VIEWGUILD_HOME_LEAVE"] = "Gilde verlassen";
$lang["VIEWGUILD_HOME_ATKLOG"] = "Guild Attack Logs";
$lang["VIEWGUILD_HOME_ARMORY"] = "Gildenrüstung";
$lang["VIEWGUILD_HOME_STAFF"] = "Guild Staff Room";
$lang["VIEWGUILD_HOME_ANNOUNCE"] = "Gildenankündigung";
$lang["VIEWGUILD_HOME_EVENT"] = "Letzte 10 Gilden-Ereignisse";
$lang["VIEWGUILD_HOME_EVENTTEXT"] = "Ereignistext";
$lang["VIEWGUILD_HOME_EVENTTIME"] = "Ereigniszeit";
$lang["VIEWGUILD_SUMMARY_TITLE"] = "Gildenübersicht";
$lang["VIEWGUILD_SUMMARY_OWNER"] = "Guild Leader";
$lang["VIEWGUILD_SUMMARY_COOWNER"] = "Guild Co-Leader";
$lang["VIEWGUILD_SUMMARY_MEM"] = "Mitglieder / Max. Kapazität";
$lang["VIEWGUILD_SUMMARY_LVL"] = "Gildenlevel";
$lang["VIEWGUILD_NA"] = "N / A";
$lang["VIEWGUILD_DONATE_TITLE"] = "Geben Sie den Betrag ein, den Sie Ihrer Gilde spenden möchten.";
$lang["VIEWGUILD_DONATE_BTN"] = "Spenden für die Gilde";
$lang["VIEWGUILD_DONATE_ERR1"] = "Sie müssen das vorherige Formular ausfüllen, um zu spenden.";
$lang["VIEWGUILD_DONATE_ERR2"] = "Sie können nicht mehr Primärwährung spenden, als Sie derzeit haben.";
$lang["VIEWGUILD_DONATE_ERR3"] = "Sie können nicht mehr Nebenwährung spenden, als Sie derzeit haben.";
$lang["VIEWGUILD_DONATE_SUCC"] = "Du hast der Gilde die angegebenen Summen gespendet.";
$lang["VIEWGUILD_MEMBERS_TH1"] = "Benutzer";
$lang["VIEWGUILD_MEMBERS_TH2"] = "Level";
$lang["VIEWGUILD_MEMBERS_BTN"] = "Kick";
$lang["VIEWGUILD_IDX"] = "Gildenindex";
$lang["VIEWGUILD_KICK_SUCCESSS"] = "Du hast diesen Benutzer erfolgreich aus der Gilde gekickt.";
$lang["VIEWGUILD_KICK_ERR"] = "Es tut uns leid, aber Ihr könnt den Anführer der Gilde nicht treten. Wenn Ihr Anführer inaktiv ist, wenden Sie sich an die Angestellten, damit Sie ihren Platz einnehmen können. ";
$lang["VIEWGUILD_KICK_ERR1"] = "Du kannst dich nicht von der Gilde stürzen, wenn du gehen willst, übertrage deine Kräfte auf einen anderen Spieler, dann geh.";
$lang["VIEWGUILD_KICK_ERR2"] = "Du versuchst, einen Benutzer zu treten, der nicht in deiner Gilde ist oder nicht existiert.";
$lang["VIEWGUILD_KICK_ERR3"] = "Sie sind nicht berechtigt, Benutzer aus dieser Gilde zu kicken.";
$lang["VIEWGUILD_LEAVE_ERR"] = "Du kannst nicht gehen, solange du der Besitzer / Miteigentümer deiner Gilde bist. Überweise deine Rechte auf ein anderes Mitglied in der Gilde und versuche es erneut.";
$lang["VIEWGUILD_LEAVE_SUCC"] = "Du hast deine Gilde erfolgreich verlassen.";
$lang["VIEWGUILD_LEAVE_SUCC1"] = "Ihr habt beschlossen, in Eurer Gilde zu bleiben.";
$lang["VIEWGUILD_LEAVE_INFO"] = "Sind Sie zu 100% sicher, dass Sie Ihre Gilde verlassen wollen, müssen Sie erneut anfangen, wenn Sie gehen und wiederkommen wollen.";
$lang["VIEWGUILD_LEAVE_BTN"] = "Ja, verlassen!";
$lang["VIEWGUILD_LEAVE_BTN1"] = "Nein, warte, bleibe!";
$lang["VIEWGUILD_ATKLOGS_INFO"] = "Diese Tabelle listet die letzten 50 ausgehenden Angriffe aus deiner Gilde auf.";
$lang["VIEWGUILD_ATKLOGS_TD1"] = "Zeit";
$lang["VIEWGUILD_ATKLOGS_TD2"] = "Attack Info";
$lang["VIEWGUILD_STAFF_ERROR"] = "Nur der Anführer und Co-Führer der Gilde kann diesen Bereich sehen.";
$lang["VIEWGUILD_STAFF_IDX_APP"] = "Anwendungsverwaltung";
$lang["VIEWGUILD_STAFF_APP_TH0"] = "Einreichzeit";
$lang["VIEWGUILD_STAFF_APP_TH1"] = "Antragsteller";
$lang["VIEWGUILD_STAFF_APP_TH2"] = "Level";
$lang["VIEWGUILD_STAFF_APP_TH3"] = "Anwendungstext";
$lang["VIEWGUILD_STAFF_APP_TH4"] = "Aktionen";
$lang["VIEWGUILD_STAFF_APP_BTN"] = "Akzeptieren";
$lang["VIEWGUILD_STAFF_APP_BTN1"] = "Decline";
$lang["VIEWGUILD_STAFF_APP_DENY_TEXT"] = "Sie haben diese Anwendung erfolgreich abgelehnt.";
$lang["VIEWGUILD_STAFF_APP_ACC_ERR"] = "Eure Gilde hat nicht die Fähigkeit, dieses Mitglied zu akzeptieren.";
$lang["VIEWGUILD_STAFF_APP_ACC_ERR1"] = "Dieser Spieler ist bereits in einer Gilde.";
$lang["VIEWGUILD_STAFF_APP_ACC_SUCC"] = "Sie haben diese Anwendung erfolgreich akzeptiert!";
$lang["VIEWGUILD_STAFF_APP_WOT"] = "Wir wissen nicht, wie du hierher gekommen bist ... aber ja ... du sollst nicht hier sein.";
// Leihen Sie Spy
$lang["SPY_ERROR1"] = "Sie müssen einen Benutzer angeben, den Sie ausspionieren möchten!";
$lang["SPY_ERROR2"] = "Es gibt keinen Grund, sich selbst auszuspionieren.";
$lang["SPY_ERROR3"] = "Der Benutzer, den Sie ausspionieren wollen, existiert nicht.";
$lang["SPY_ERROR4"] = "Du hast nicht genug {$lang['INDEX_PRIMCURR']}, um diesen Benutzer zu spionieren!";
$lang["SPY_ERROR5"] = "Du kannst andere Spieler nicht ausspionieren, wenn du im Kerker bist!";
$lang["SPY_ERROR6"] = "Du kannst nicht auf andere Spieler spionieren, wenn du im Krankenhaus bist und versuchst besser zu fühlen.";
$lang["SPY_START"] = "Du versuchst, einen Spion auszusenden, um Informationen über";
$lang["SPY_START1"] = "Dies wird Ihnen 500 {$lang['INDEX_PRIMCURR']} multipliziert mit ihrem Level (";
$lang["SPY_START2"] = "{$lang['INDEX_PRIMCURR']} in diesem Fall.) Bitte denken Sie daran, dass der Erfolg nicht garantiert ist.Wenn Sie das Risiko übernehmen wollen, drücken Sie die Taste, um einen Spion auszusenden! ";
$lang["SPY_BTN"] = "Spy senden";
$lang["SPY_FAIL1"] = "Du versuchst, Informationen über dein Ziel zu bekommen, und du wirst Glück haben, dass du dich entschieden hast, so schnell wie du kannst Zeit, Knospe ";
$lang["SPY_FAIL2"] = "Du versuchst, Informationen über dein Ziel zu bekommen, und du wirst sie sehen.";
$lang["SPY_FAIL3"] = "Du versuchst, Informationen über dein Ziel zu bekommen, du verfolgst sie genau, fast stalkerisch wie: Ein Wachmann bemerkt dies und schlägt dich ins Gesicht.";
$lang["SPY_SUCCESS"] = "Ungefähr";
$lang["SPY_SUCCESS1"] = "{$lang['INDEX_PRIMCURR']} pro Versuch haben Sie erfolgreich Informationen zu gefunden";
$lang["SPY_SUCCESS2"] = "! Hier ist die Information.";

// Stabsstände
$lang["STAFF_ESTATE_ADD"] = "Anlage erstellen";
$lang["STAFF_ESTATE_ADD_TABLE"] = "Verwenden Sie dieses Formular, um eine Eigenschaft in das Spiel hinzuzufügen.";
$lang["STAFF_ESTATE_ADD_TH1"] = "Nachname";
$lang["STAFF_ESTATE_ADD_TH2"] = "Grundstückskosten";
$lang["STAFF_ESTATE_ADD_TH3"] = "Mindestzustand des Objekts";
$lang["STAFF_ESTATE_ADD_TH4"] = "Estate Will Level";
$lang["STAFF_ESTATE_ADD_BTN"] = "Anlage erstellen";
$lang["STAFF_ESTATE_ADD_ERROR1"] = "Sie können nicht mehr als ein Objekt mit demselben Namen erstellen.";
$lang["STAFF_ESTATE_ADD_ERROR2"] = "Sie können kein Gut mit dem gleichen Willen wie ein anderes hinzufügen.";
$lang["STAFF_ESTATE_ADD_ERROR3"] = "Sie können keine Eigenschaft mit einer Levelanforderung unter 1 hinzufügen.";
$lang["STAFF_ESTATE_ADD_ERROR4"] = "Sie können kein Gut mit einem Willenniveau hinzufügen, der gleich oder kleiner als 100 ist.";
$lang["STAFF_ESTATE_ADD_SUCCESS"] = "Das Spiel wurde erfolgreich zum Spiel hinzugefügt.";

// Grundstücke
$lang["ESTATES_START"] = "Ihr derzeitiger Zustand:";
$lang["ESTATES_SELL"] = "Verkaufen Sie Ihr Anwesen für 75%";
$lang["ESTATES_TABLE1"] = "Nachname";
$lang["ESTATES_TABLE2"] = "Level-Anforderung";
$lang["ESTATES_TABLE3"] = "Kosten ({$lang['INDEX_PRIMCURR']})";
$lang["ESTATES_TABLE4"] = "Will Level";
$lang["ESTATES_ERROR1"] = "Sie versuchen, ein nicht vorhandenes Gut zu erwerben. Prüfen Sie Ihre Quelle und versuchen Sie es erneut.";
$lang["ESTATES_ERROR2"] = "Du kannst kein Landgut kaufen, das weniger hat als dein gegenwärtiges Anwesen.";
$lang["ESTATES_ERROR3"] = "Sie haben nicht genug {$lang['INDEX_PRIMCURR']}, um diese Immobilie zu kaufen";
$lang["ESTATES_ERROR4"] = "Du kannst kein Gut kaufen, das den gleichen Willen deines gegenwärtigen Standes hat.";
$lang["ESTATES_ERROR5"] = "Du kannst nicht verkaufen, wenn du nackt und stolz bist, Knospe.";
$lang["ESTATES_ERROR6"] = "Dein Level ist zu niedrig für dieses Anwesen, mein Freund.";
$lang["ESTATES_SUCCESS1"] = "Sie haben das";
$lang["ESTATES_SUCCESS2"] = "Sie haben Ihre Immobilie für 75% ihres ursprünglichen Preises verkauft und gingen zurück zu nackt und stolz.";
$lang["ESTATES_INFO"] = "Die unten stehende Liste ist eine Eigenschaft, die du kaufen kannst. Mach dir keine Sorgen, du kannst die gekauften Grundstücke für 75% seines Wertes wieder verkaufen. ";

//Roulette
$lang["ROULETTE_TITLE"] = "Roulette";
$lang["ROULETTE_INFO"] = "Bereit zum Testen deines Glücks, hier am Roulettetisch gewinnt das Haus immer, um Spieler zu verlieren, die ihren ganzen Reichtum in einem Rennen verlieren, haben wir eine Wettbeschränkung gesetzt Ebene, können Sie nur wetten ";
$lang["ROULETTE_NOREFRESH"] = "Bitte aktualisieren Sie sich nicht, während Sie Roulette spielen. Bitte benutzen Sie die Links, danke!";
$lang["ROULETTE_TABLE1"] = "Bet";
$lang["ROULETTE_TABLE2"] = "Pick #";
$lang["ROULETTE_ERROR1"] = "Sie können nicht mehr auf {$lang['INDEX_PRIMCURR']} setzen, als Sie derzeit haben.";
$lang["ROULETTE_ERROR2"] = "Du versuchst, eine Wette zu platzieren, die höher ist als deine derzeitige maximale Wette.";
$lang["ROULETTE_ERROR3"] = "Sie können nur auf die Zahlen zwischen 0 und 36 setzen.";
$lang["ROULETTE_ERROR4"] = "Sie müssen eine Wette größer als 0 {$lang['INDEX_PRIMCURR']} angeben.";
$lang["ROULETTE_LOST"] = "Sie verlieren Ihre Wette";
$lang["ROULETTE_WIN"] = "und gewonnen! Sie behalten Ihre Wette und tippen ein Extra";
$lang["ROULETTE_BTN1"] = "Platzieren Sie Wette!";
$lang["ROULETTE_BTN2"] = "Wieder, gleiche Wette, bitte.";
$lang["ROULETTE_BTN3"] = "Wieder, aber mit einer anderen Wette";
$lang["ROULETTE_BTN4"] = "Ich beendige, ich will nicht pleite gehen.";
$lang["ROULETTE_START"] = "Sie setzen Ihre Wette und ziehen Sie den Griff nach unten, um und um das Rad dreht, es stoppt und landet auf";

//Hoch niedrig
$lang["HILOW_NOREFRESH"] = "Bitte aktualisieren Sie nicht, während Sie High / Low spielen. Verwenden Sie die Links, die wir zur Verfügung stellen, danke!";
$lang["HILOW_INFO"] = "Willkommen bei High / Low Hier legen Sie fest, ob der Deal eine Zahl niedriger oder höher als die angegebene Zahl ziehen wird.";
$lang["HILOW_SHOWN"] = "Der Spieloperator zeigt die Zahl";
$lang["HILOW_WATDO"] = "Wählen Sie die Schaltfläche, auf der Sie fühlen, wie die nächste Zahl mit dieser Zahl verglichen wird.";
$lang["HILOW_NOBET"] = "Du hast nicht genug {$lang['INDEX_PRIMCURR']}, um High / Low zu spielen.";
$lang["HILOW_LOWER"] = "Lower";
$lang["HILOW_HIGHER"] = "Higher";
$lang["HIGHLOW_HIGH"] = "Sie haben erraten, dass der Spieloperator eine Zahl höher als";
$lang["HIGHLOW_REVEAL"] = "Der Spieloperator zeigt die Zahl an";
$lang["HIGHLOW_LOSE"] = "Du hast diese Zeit verloren, sorry Knospe.";
$lang["HIGHLOW_WIN"] = "Du hast diese Zeit gewonnen, Glückwünsche.";
$lang["HIGHLOW_LOWER"] = "Sie haben erraten, dass der Spielbetreiber eine Zahl kleiner als";
$lang["HIGHLOW_TIE"] = "Der Spielbetreiber zeigt die genaue Zahl wie beim letzten Mal an.";
$lang["HILOW_UNDEFINEDNUMBER"] = "Die Nummer von der letzten Seite wurde nicht definiert ... Weird.";

// ReCaptcha
$lang["RECAPTCHA_TITLE"] = "reCaptcha";
$lang["RECAPTCHA_INFO"] = "Dies ist ein notwendiges Übel, nur stellen Sie sicher, dass Sie kein Bot sind.";
$lang["RECAPTCHA_BTN"] = "Bestätigen";
$lang["RECAPTCHA_EMPTY"] = "Sie können das reCaptcha-Formular nicht leer lassen!";
$lang["RECAPTCHA_FAIL"] = "Du hast die reCaptcha fehlgeschlagen, geh zurück und versuche es erneut.";

//Sack
$lang["POKE_TITLE"] = "Sind Sie sicher, dass Sie poke wollen";
$lang["POKE_TITLE1"] = "Bitte belästigen Sie nicht die Benutzer, die dies verwenden. Die Mitarbeiter werden es herausfinden, und sie können Ihr Privileg entfernen, um andere zu stoßen.";
$lang["POKE_ERROR1"] = "Sie müssen eine Person angeben, die Sie pochen möchten.";
$lang["POKE_ERROR2"] = "Nein, du kannst dich nicht selbst stehlen!";
$lang["POKE_ERROR3"] = "Du kannst nicht existierende Benutzer stoßen!";
$lang["POKE_BTN"] = "POKE!";
$lang["POKE_SUCC"] = "Sie haben diesen Benutzer erfolgreich gestartet.";

// Mitarbeiterwechsel PW
$lang["STAFF_USERS_CP_FORM_INFO"] = "Verwenden Sie dieses Formular, um ein Benutzerpasswort zu ändern.";
$lang["STAFF_USERS_CP_USER"] = "Benutzer";
$lang["STAFF_USERS_CP_FORM_BTN"] = "Passwort ändern";
$lang["STAFF_USERS_CP_PW"] = "Neues Passwort";
$lang["STAFF_USERS_CP_ERROR"] = "Das Passwort für das Admin-Konto kann nicht geändert werden.";
$lang["STAFF_USERS_CP_ERROR1"] = "Sie können das Passwort für andere Admin-Konten nicht ändern.";
$lang["STAFF_USERS_CP_SUCCESS"] = "Benutzerkennwort wurde erfolgreich geändert.";

// Element Send
$lang["ITEM_SEND_ERROR"] = "Sie versuchen, ein nicht vorhandenes Element zu senden, oder Sie haben dieses Element nicht in Ihrem Inventar.";
$lang["ITEM_SEND_ERROR1"] = "Sie versuchen, mehr von diesem Element zu senden, als Sie derzeit haben.";
$lang["ITEM_SEND_ERROR2"] = "Sie versuchen, dieses Element an einen Benutzer zu senden, der nicht existiert.";
$lang["ITEM_SEND_ERROR3"] = "Es macht keinen Sinn, sich einen Artikel zu schicken.";
$lang["ITEM_SEND_SUCC"] = "Sie haben erfolgreich gesendet";
$lang["ITEM_SEND_SUCC1"] = "bis";
$lang["ITEM_SEND_FORMTITLE"] = "Geben Sie an, wem Sie senden möchten";
$lang["ITEM_SEND_FORMTITLE1"] = "zusammen mit der Menge, die Sie senden möchten.";
$lang["ITEM_SEND_FORMTITLE2"] = "Alternativ können Sie die ID-Nummer eines Benutzers eingeben.";
$lang["ITEM_SEND_TH"] = "Benutzer";
$lang["ITEM_SEND_TH1"] = "Menge zu senden";
$lang["ITEM_SEND_BTN"] = "Send Item (s)";

// Steckplätze
$lang["SLOTS_INFO"] = "Willkommen auf der Slots-Maschine und wetten Sie einige Ihrer hart verdienten Bargeld für eine schlanke Chance zu gewinnen groß! Auf Ihrer Ebene haben wir eine Wettbeschränkung von verhängt";
$lang["SLOTS_TABLE1"] = "Bet";
$lang["SLOTS_BTN"] = "Spin baby, spin!";
$lang["SLOTS_TITLE"] = "Slot Machine";
$lang["SLOTS_NOREFRESH"] = "Bitte aktualisieren Sie die Seite nicht, während Sie an den Spielautomaten spielen.";

// Botzelt
$lang["BOTTENT_TITLE"] = "Bot Tent";
$lang["BOTTENT_DESC"] = "Willkommen im Bot-Zelt Hier könnt ihr NPCs zum Kampf herausfordern, wenn ihr gewinnt, werdet ihr einen Gegenstand erhalten, der für eure Abenteuer nützlich ist Kann man diese NPCs nur so oft anzugreifen, wie es auch bei der Abkühlung der Fall ist.";
$lang["BOTTENT_TH"] = "Bot-Name";
$lang["BOTTENT_TH1"] = "Bot-Ebene";
$lang["BOTTENT_TH2"] = "Bot Cooldown";
$lang["BOTTENT_TH3"] = "Bot Item Drop";
$lang["BOTTENT_TH4"] = "Angriff";
$lang["BOTTENT_WAIT"] = "Abklingzeit:";

// Mitarbeiter bots
$lang["STAFF_BOTS_TITLE"] = "Staff Bots";
$lang["STAFF_BOTS_ADD"] = "Bot hinzufügen";
$lang["STAFF_BOTS_DEL"] = "Bot löschen";
$lang["STAFF_BOTS_ADD_FRM1"] = "Verwenden Sie dieses Formular, um dem Spiel Bots hinzuzufügen, die beim Beenden Objekte löschen.";
$lang["STAFF_BOTS_ADD_FRM2"] = "Bot User";
$lang["STAFF_BOTS_ADD_FRM3"] = "Item Dropped";
$lang["STAFF_BOTS_ADD_FRM4"] = "Abkühlzeit (Sekunden)";
$lang["STAFF_BOTS_ADD_BTN"] = "Bot hinzufügen";
$lang["STAFF_BOTS_ADD_ERROR"] = "Es fehlt eine der erforderlichen Eingaben. Gehen Sie zurück und versuchen Sie es erneut.";
$lang["STAFF_BOTS_ADD_ERROR1"] = "Du versuchst, einen Bot hinzuzufügen, der bereits in der Bot-Liste existiert. Gehe zurück und versuche es erneut.";
$lang["STAFF_BOTS_ADD_ERROR2"] = "Du kannst nur NPCs in die Bot-Liste aufnehmen. Gehe zurück und versuche es erneut.";
$lang["STAFF_BOTS_ADD_ERROR3"] = "Sie können nicht einen bot drop ein nicht vorhandenes Element. Gehen Sie zurück und versuchen Sie es erneut.";
$lang["STAFF_BOTS_ADD_SUCCESS"] = "Du hast erfolgreich einen NPC in die Botliste eingetragen.";

// VIP Spendenlisten
$lang["VIP_LIST"] = "VIP-Paket kaufen";
$lang["VIP_INFO"] = "Wenn Sie ein VIP-Paket von unten kaufen, werden Sie abhängig von der Packung, die Sie kaufen, folgendes bekommen: Wenn Sie Betrug begehen, werden Sie permenant verboten.";
$lang["VIP_TABLE_TH1"] = "Pack-Info";
$lang["VIP_TABLE_TH2"] = "Packungsinhalt";
$lang["VIP_TABLE_TH3"] = "Link";
$lang["VIP_TABLE_VDINFO"] = "VIP-Tage deaktivieren Anzeigen rund um das Spiel.Sie erhalten auch 16% Energie-Nachfüllung statt 8 %.Sie erhalten auch einen Stern von Ihrem Namen, und Ihr Name wird die Farbe ändern Ehrfürchtig ist das? ";
$lang["VIP_THANKS"] = "Vielen Dank für die Spende";
$lang["VIP_CANCEL"] = "Sie haben Ihre Spende erfolgreich storniert.";
$lang["VIP_SUCCESS"] = "Wir freuen uns, dass es Ihnen gelingt, den Erhalt dieser Transaktion bei <a href='http://www.paypal.com'>Paypal</a> zu sehen Wenn Sie sich nicht sicher sind.";

// Mitarbeiterstrafen
$lang["STAFF_PUNISHFED_FORM"] = "Jailing User";
$lang["STAFF_PUNISHFED_INFO"] = "Wenn Sie einen Benutzer in ein Bundesgefängnis stellen, wird sein Account praktisch nutzlos.";
$lang["STAFF_PUNISHFED_TH"] = "Benutzer:";
$lang["STAFF_PUNISHFED_TH1"] = "Tage:";
$lang["STAFF_PUNISHFED_TH2"] = "Grund:";
$lang["STAFF_PUNISHFED_BTN"] = "Platzieren Sie Benutzer im Bundesgefängnis";
$lang["STAFF_PUNISHFED_ERR"] = "Sie können keinen Benutzer platzieren, der nicht im Bundesgefängnis existiert.";
$lang["STAFF_PUNISHFED_ERR1"] = "Sie müssen alle Eingaben auf der vorherigen Seite ausfüllen, damit diese korrekt funktioniert.";
$lang["STAFF_PUNISHFED_ERR2"] = "Du darfst keine Admins im Bundesgefängnis platzieren, bevor du es erneut probierst.";
$lang["STAFF_PUNISHFED_SUCC"] = "Der Benutzer wurde erfolgreich in das Bundesgefängnis gestellt.";

// Fedjail-Auflistung
$lang["FJ_TITLE"] = "Bundesgefängnis";
$lang["FJ_INFO"] = "Dies ist, wo schlechte Leute gehen, wenn sie die Regeln brechen.Seien Sie eine intelligente Person und brechen Sie nicht die Regeln oder Sie können nie das Licht des Tages wieder sehen.";
$lang["FJ_WHO"] = "Wer";
$lang["FJ_TIME"] = "Verbleibende Zeit";
$lang["FJ_RS"] = "Grund";
$lang["FJ_JAILER"] = "Gefängniswärter";

//Bergbau
$lang["MINE_INFO"] = "Willkommen in den gefährlichen Minen, Gehirnloser Narr, Reichtümer sind für Sie verfügbar, wenn Sie die Fähigkeiten haben, jede Mine hat ihre eigenen Anforderungen und könnte sogar eine spezielle Spitzhacke haben, die Sie verwenden müssen. ";
$lang["MINE_DUNGEON"] = "Nur ehrenvolle Krieger können meins sein. Kommen Sie zurück, wenn Sie Ihre Schuld für die Gesellschaft gedient haben.";
$lang["MINE_INFIRM"] = "Nur gesunde Krieger können meins sein, komm zurück, wenn du dieses Bandaid aus deinem Finger gerissen hast.";
$lang["MINE_LEVEL"] = "Sie haben derzeit ein Mineniveau von";
$lang["MINE_POWER"] = "Mining Power";
$lang["MINE_XP"] = "Bergbauerfahrung";
$lang["MINE_SPOTS"] = "Minen öffnen";
$lang["MINE_SETS"] = "Kauf-Sets";
$lang["MINE_BUY_ERROR"] = "Du versuchst, mehr Power-Sets zu kaufen, als du dir zurzeit zur Verfügung stehst.";
$lang["MINE_BUY_ERROR_IQ"] = "Du hast nicht genug IQ, um so viele Sätze von Macht zu kaufen.";
$lang["MINE_BUY_ERROR_IQ1"] = "doch haben Sie nur";
$lang["MINE_BUY_SUCCESS"] = "Glückwunsch, Sie haben erfolgreich gehandelt";
$lang["MINE_BUY_SUCCESS1"] = "Mengen von Bergbau".
$lang["MINE_BUY_INFO"] = "Ab diesem Moment können Sie kaufen";
$lang["MINE_BUY_INFO1"] = "Sätze der Bergbau-Macht.Erinnerst du dich, ein Satz von Bergbau-Macht ist gleich 10 Bergbau-Macht.Sie ​​entsperren zusätzliche Sets, indem Sie Ihre Bergbau-Ebene.";
$lang["MINE_BUY_INFO2"] = "IQ. Also, wie viele Sätze möchten Sie kaufen?";
$lang["MINE_BUY_BTN"] = "Kauf-Energien-Sätze";
$lang["MINE_DO_ERROR"] = "Ungültiger Mining-Spot.";
$lang["MINE_DO_ERROR1"] = "Du versuchst an einer Stelle, die nicht existiert, zu mähen.";
$lang["MINE_DO_ERROR2"] = "Dein Bergbau ist zu niedrig, um hier zu Minen zu gehen.";
$lang["MINE_DO_ERROR3"] = "Sie können nur an einem Bergbauplatz angeln, wenn Sie am selben Ort sind.";
$lang["MINE_DO_ERROR4"] = "Dein IQ-Level ist hier zu niedrig, um es zu minimieren.";
$lang["MINE_DO_ERROR5"] = "Du hast nicht genug Bergwerk zu mir hier. Du musst mindestens haben";
$lang["MINE_DO_ERROR6"] = "Du hast nicht die erforderliche Spitzhacke, um hier zu kommen. Kommen Sie zurück, wenn Sie mindestens einen haben";
$lang["MINE_DO_FAIL"] = "Während du wegfährst, schlägst du eine Gassack und zündst die ganze Mine, du wirst später gefunden, kaum atmend.";
$lang["MINE_DO_FAIL1"] = "Sie und ein anderer Bergmann kommen in einen Streit darüber, wer dieses Stück Erz zuerst gesehen hat. Das Sprechen wird schreien, und das Schreien drängt, Push geht zu schieben, und das nächste, was Sie wissen, Sie und er Beide kämpfen auf dem Boden, die Wächter in der Nähe sehen das und verhaften beide. ";
$lang["MINE_DO_FAIL2"] = "Wie unglücklich. Ihre Bergbauversuche erwiesen sich als erfolglos.";
$lang["MINE_DO_SUCC"] = "Du hast ein Stück Stein geschlagen, um eine große Vene von Erz zu enthüllen. Nach einigen Minuten der sorgfältigen Ausgrabung hast du es geschafft";
$lang["MINE_DO_SUCC1"] = "aus dieser Vene.";
$lang["MINE_DO_SUCC2"] = "Während Sie wegfuhren, haben Sie es geschafft, ein Stück von mir fachmännisch zu mähen";
$lang["MINE_DO_BTN1"] = "Mine Again";
$lang["MINE_DO_BTN"] = "Zurück";

// Mitarbeiterabbau
$lang["STAFF_MINE_TITLE"] = "Mining Panel";
$lang["STAFF_MINE_ADD_ERROR"] = "Keiner der Eingaben auf dem vorherigen Formular kann leer bleiben. Gehen Sie zurück und versuchen Sie es erneut.";
$lang["STAFF_MINE_ADD_ERROR1"] = "Der Mindestbergbau für diese Mine muss mindestens 1 betragen.";
$lang["STAFF_MINE_ADD_ERROR2"] = "Die minimale Ausgabe für die Itemausgänge darf nicht größer oder gleich ihrem Maximum sein.";
$lang["STAFF_MINE_ADD_ERROR3"] = "Die Stadt, die Sie für die Mine ausgewählt haben, ist nicht vorhanden.";
$lang["STAFF_MINE_ADD_ERROR4"] = "Das Element, das Sie für die Spitzhacke der Mine ausgewählt haben, existiert nicht. Überprüfen Sie Ihre Quelle und versuchen Sie es erneut.";
$lang["STAFF_MINE_ADD_ERROR5"] = "Das Element, das Sie für den Ausgang Nr. 1 der Mine ausgewählt haben, existiert nicht. Überprüfen Sie Ihre Quelle und versuchen Sie es erneut.";
$lang["STAFF_MINE_ADD_ERROR6"] = "Das Element, das Sie für den Ausgang Nr. 2 der Mine ausgewählt haben, existiert nicht. Überprüfen Sie Ihre Quelle und versuchen Sie es erneut.";
$lang["STAFF_MINE_ADD_ERROR7"] = "Das Element, das Sie für den Ausgang Nr. 3 der Mine ausgewählt haben, existiert nicht. Überprüfen Sie Ihre Quelle und versuchen Sie es erneut.";
$lang["STAFF_MINE_ADD_ERROR8"] = "Das Element, das Sie für das Minenobjekt ausgewählt haben, existiert nicht. Überprüfen Sie Ihre Quelle und versuchen Sie es erneut.";
$lang["STAFF_MINE_ADD_SUCCESS"] = "Du hast erfolgreich eine Mine erstellt.";
$lang["STAFF_MINE_ADD_FRMINFO"] = "Verwenden Sie dieses Formular, um dem Spiel eine Mine hinzuzufügen. Der Name der Mine wird lose auf der Stadt basieren, auf der sie platziert ist.";
$lang["STAFF_MINE_FORM_LOCATION"] = "Mine's Location";
$lang["STAFF_MINE_FORM_LVL"] = "Minimum Mining Level";
$lang["STAFF_MINE_FORM_IQ"] = "Minimum IQ Erforderlich";
$lang["STAFF_MINE_FORM_PEPA"] = "Leistung Auspuff / Versuch";
$lang["STAFF_MINE_FORM_PICK"] = "Erforderliche Pickaxe";
$lang["STAFF_MINE_FORM_OP1"] = "Item # 1";
$lang["STAFF_MINE_FORM_OP2"] = "Item # 2";
$lang["STAFF_MINE_FORM_OP3"] = "Item # 3";
$lang["STAFF_MINE_FORM_GEM"] = "Gem Item";
$lang["STAFF_MINE_FORM_OP1MIN"] = "Item # 1 Minimum Output";
$lang["STAFF_MINE_FORM_OP2MIN"] = "Item # 2 Minimum Output";
$lang["STAFF_MINE_FORM_OP3MIN"] = "Item # 3 Minimum Output";
$lang["STAFF_MINE_FORM_OP1MAX"] = "Item # 1 Maximum Output";
$lang["STAFF_MINE_FORM_OP2MAX"] = "Item # 2 Maximum Output";
$lang["STAFF_MINE_FORM_OP3MAX"] = "Item # 3 Maximum Output";
$lang["STAFF_MINE_EDIT1"] = "Wähle eine zu ändernde Mine.";
$lang["STAFF_MINE_EDIT2"] = "Bearbeiten einer bestehenden Mine ...";
$lang["STAFF_MINE_ADD_BTN"] = "Mine erstellen";
$lang["STAFF_MINE_EDIT_BTN"] = "Alter Mine";
$lang["STAFF_MINE_EDIT_SUCCESS"] = "Die Mine wurde erfolgreich bearbeitet.";
$lang["STAFF_MINE_EDIT_ERR"] = "Sie haben eine nicht vorhandene Mine ausgewählt. Überprüfen Sie Ihre Quelle und versuchen Sie es erneut.";
$lang["STAFF_MINE_DEL_SUCCESS"] = "Sie haben eine Mine erfolgreich gelöscht";
$lang["STAFF_MINE_DEL1"] = "Wählen Sie eine zu löschende Grube.";
$lang["STAFF_MINE_DEL_BTN"] = "Mine löschen! (Keine Eingabeaufforderung, Sicher!";

// Ankündigungen
$lang["ANNOUNCEMENTS_TIME"] = "Zeit veröffentlicht";
$lang["ANNOUNCEMENTS_TEXT"] = "Ankündigungstext";
$lang["ANNOUNCEMENTS_READ"] = "Lesen";
$lang["ANNOUNCEMENTS_UNREAD"] = "Ungelesen";
$lang["ANNOUNCEMENTS_POSTED"] = "Geschrieben von:";

// Dungeon und Krankenhaus
$lang["DUNGINFIRM_TITLE"] = "Kerker";
$lang["DUNGINFIRM_TITLE1"] = "Krankenhaus";
$lang["DUNGINFIRM_INFO"] = "Zur Zeit";
$lang["DUNGINFIRM_INFO1"] = "Spieler im Verlies.";
$lang["DUNGINFIRM_INFO2"] = "Spieler im Krankenhaus.";
$lang["DUNGINFIRM_TD1"] = "Benutzer / Benutzerkennung";
$lang["DUNGINFIRM_TD2"] = "Grund";
$lang["DUNGINFIRM_TD3"] = "Check-in Zeit";
$lang["DUNGINFIRM_TD4"] = "Check-Out Time";

// Mitarbeiterindex
$lang["STAFF_IDX_TITLE"] = "Staff Panel";
$lang["STAFF_IDX_PHP"] = "PHP Version";
$lang["STAFF_IDX_DB"] = "Datenbankversion";
$lang["STAFF_IDX_CENGINE"] = "Chivalry Engine Version";
$lang["STAFF_IDX_CE_UP"] = "Chivalry Engine Update Checker";
$lang["STAFF_IDX_API"] = "API-Version";
$lang["STAFF_IDX_IFRAME"] = "Meine Entschuldigung, aber Ihr Browser unterstützt keine Iframes, die für die Verwendung dieses Update-Checkers benötigt werden.";
$lang["STAFF_IDX_ADMIN_TITLE"] = "Admin-Aktionen";
$lang["STAFF_IDX_ADMIN_LI"] = "Admin";
$lang["STAFF_IDX_ADMIN_LI1"] = "Module";
$lang["STAFF_IDX_ADMIN_LI2"] = "Benutzer";
$lang["STAFF_IDX_ADMIN_LI3"] = "Gegenstände";
$lang["STAFF_IDX_ADMIN_LI4"] = "Shops";
$lang["STAFF_IDX_ADMIN_LI5"] = "Akademie";
$lang["STAFF_IDX_ADMIN_LI6"] = "NPCs";
$lang["STAFF_IDX_ADMIN_LI7"] = "Jobs";
$lang["STAFF_IDX_ADMIN_LI8"] = "Polls";
$lang["STAFF_IDX_ADMIN_LI9"] = "Städte";
$lang["STAFF_IDX_ADMIN_LI10"] = "Estates";
$lang["STAFF_IDX_ADMIN_TAB1"] = "Spieleinstellungen";
$lang["STAFF_IDX_ADMIN_TAB2"] = "Ankündigung erstellen";
$lang["STAFF_IDX_ADMIN_TAB3"] = "Spieldiagnose";
$lang["STAFF_IDX_ADMIN_TAB4"] = "Benutzer aktualisieren";
$lang["STAFF_IDX_MODULES_TAB1"] = "Verbrechen";
$lang["STAFF_IDX_USERS_TAB1"] = "Benutzer erstellen";
$lang["STAFF_IDX_USERS_TAB2"] = "Benutzer bearbeiten";
$lang["STAFF_IDX_USERS_TAB3"] = "Benutzer löschen";
$lang["STAFF_IDX_USERS_TAB4"] = "Benutzer abmelden";
$lang["STAFF_IDX_USERS_TAB5"] = "Benutzerpasswort ändern";
$lang["STAFF_IDX_ITEMS_TAB1"] = "Artikelgruppe erstellen";
$lang["STAFF_IDX_ITEMS_TAB2"] = "Element erstellen";
$lang["STAFF_IDX_ITEMS_TAB3"] = "Element löschen";
$lang["STAFF_IDX_ITEMS_TAB4"] = "Element bearbeiten";
$lang["STAFF_IDX_ITEMS_TAB5"] = "Objekt an Benutzer senden";
$lang["STAFF_IDX_SHOPS_TAB1"] = "Shop erstellen";
$lang["STAFF_IDX_SHOPS_TAB2"] = "Shop löschen";
$lang["STAFF_IDX_SHOPS_TAB3"] = "Hinzufügen zum Einkaufen";
$lang["STAFF_IDX_NPC_TAB1"] = "NPC-Bot hinzufügen";
$lang["STAFF_IDX_NPC_TAB2"] = "NPC-Bot löschen";
$lang["STAFF_IDX_ASSIST_TITLE"] = "Assistant Actions";
$lang["STAFF_IDX_ASSIST_LI"] = "Spielprotokolle";
$lang["STAFF_IDX_ASSIST_LI1"] = "Berechtigungen";
$lang["STAFF_IDX_ASSIST_LI2"] = "Mining";
$lang["STAFF_IDX_LOGS_TAB1"] = "Allgemeine Logs";
$lang["STAFF_IDX_LOGS_TAB2"] = "Benutzerprotokolle";
$lang["STAFF_IDX_LOGS_TAB3"] = "Trainingsprotokolle";
$lang["STAFF_IDX_LOGS_TAB4"] = "Attack Logs";
$lang["STAFF_IDX_LOGS_TAB5"] = "Login Logs";
$lang["STAFF_IDX_LOGS_TAB6"] = "Geräteprotokolle";
$lang["STAFF_IDX_LOGS_TAB7"] = "Bankprotokolle";
$lang["STAFF_IDX_LOGS_TAB8"] = "Strafprotokolle";
$lang["STAFF_IDX_LOGS_TAB9"] = "Element mit Log";
$lang["STAFF_IDX_LOGS_TAB10"] = "Item-Kaufprotokolle";
$lang["STAFF_IDX_LOGS_TAB11"] = "Item Market Logs";
$lang["STAFF_IDX_LOGS_TAB12"] = "Mitarbeiterprotokolle";
$lang["STAFF_IDX_LOGS_TAB13"] = "Reiseprotokolle";
$lang["STAFF_IDX_LOGS_TAB14"] = "Bestätigungsprotokolle";
$lang["STAFF_IDX_LOGS_TAB15"] = "Spionierungsversuche";
$lang["STAFF_IDX_LOGS_TAB16"] = "Gambling Logs";
$lang["STAFF_IDX_LOGS_TAB17"] = "Postenverkauf von Protokollen";
$lang["STAFF_IDX_PERM_TAB1"] = "Berechtigungen anzeigen";
$lang["STAFF_IDX_PERM_TAB2"] = "Berechtigungen zurücksetzen";
$lang["STAFF_IDX_PERM_TAB3"] = "Berechtigungen bearbeiten";
$lang["STAFF_IDX_MINE_TAB1"] = "Mine hinzufügen";
$lang["STAFF_IDX_MINE_TAB2"] = "Edit Mine";
$lang["STAFF_IDX_MINE_TAB3"] = "Mine löschen";
$lang["STAFF_IDX_FM_TITLE"] = "Forum Moderator-Aktionen";
$lang["STAFF_IDX_FM_LI"] = "Strafen";
$lang["STAFF_IDX_FM_LI1"] = "Foren";
$lang["STAFF_IDX_ACTIONS"] = "Letzte 15 Mitarbeiteraktionen";
$lang["STAFF_IDX_ACTIONS_TH"] = "Zeit";
$lang["STAFF_IDX_ACTIONS_TH1"] = "Mitarbeiter";
$lang["STAFF_IDX_ACTIONS_TH2"] = "Protokolltext";
$lang["STAFF_IDX_ACTIONS_TH3"] = "IP".

//Benutzerliste
$lang["USERLIST_TITLE"] = "Benutzerliste";
$lang["USERLIST_PAGE"] = "Seiten";
$lang["USERLIST_ORDERBY"] = "Reihenfolge nach";
$lang["USERLIST_ORDER1"] = "Benutzer-ID";
$lang["USERLIST_ORDER2"] = "Name";
$lang["USERLIST_ORDER3"] = "Level";
$lang["USERLIST_ORDER4"] = "Primäre Währung";
$lang["USERLIST_ORDER5"] = "Aufsteigend";
$lang["USERLIST_ORDER6"] = "Absteigend";
$lang["USERLIST_TH1"] = "Geschlecht";
$lang["USERLIST_TH2"] = "Aktiv?";
// Statistik Seite
$lang["STATS_TITLE"] = "Statistikzentrum";
$lang["STATS_CHART"] = "Benutzerbetriebssysteme";
$lang["STATS_CHART1"] = "Geschlechterverhältnis";
$lang["STATS_CHART2"] = "Klassenverhältnis";
$lang["STATS_CHART3"] = "Benutzerbrowser-Auswahl";
$lang["STATS_TH"] = "Statistik";
$lang["STATS_TH1"] = "Statistikwert";
$lang["STATS_TD"] = "Spieler registrieren";
$lang["STATS_TD1"] = "Primärwährung zurückgezogen";
$lang["STATS_TD2"] = "Primärwährung in Banken";
$lang["STATS_TD3"] = "Gesamt-Primärwährung";
$lang["STATS_TD4"] = "Sekundärwährung im Umlauf";
$lang["STATS_TD5"] = "Primärwährung / Spieler (Durchschnitt)";
$lang["STATS_TD6"] = "Sekundäre Währung / Spieler (Durchschnitt)";
$lang["STATS_TD7"] = "Bankausgleich / Spieler (Durchschnitt)";
$lang["STATS_TD8"] = "Registrierte Gilden";

//Mitarbeiterliste
$lang["STAFFLIST_ADMIN"] = "Admins";
$lang["STAFFLIST_LS"] = "Last Seen";
$lang["STAFFLIST_CONTACT"] = "Kontakt";
$lang["STAFFLIST_ASSIST"] = "Assistenten";
$lang["STAFFLIST_MOD"] = "Forum Moderatoren";

// Zeitzone ändern
$lang["TZ_TITLE"] = "Zeitzone ändern";
$lang["TZ_BTN"] = "Zeitzone ändern";
$lang["TZ_SUCC"] = "Sie haben Ihre Zeitzoneneinstellungen erfolgreich aktualisiert.";
$lang["TZ_FAIL"] = "Sie haben eine ungültige Zeitzoneneinstellung angegeben.";
$lang["TZ_INFO"] = "Hier können Sie Ihre Zeitzone ändern, um alle Daten im Spiel zu ändern, so dass keine Prozesse beschleunigt werden können. Die Standardzeitzone ist <u> (GMT) Greenwich Mean Time </ U> Alle spielweiten Ankündigungen und Features werden auf dieser Zeitzone basieren. ";

//Zeitung
$lang["NP_TITLE"] = "Zeitung";
$lang["NP_AD"] = "Eine Anzeige kaufen";
$lang["NP_ERROR"] = "Es scheint keine Zeitungsanzeigen zu sein. Vielleicht sollten Sie <a href='?action=buyad'>Buy Ad</a> kaufen und eins auflisten?";
$lang["NP_ADINFO"] = "Anzeigen-Info";
$lang["NP_ADTEXT"] = "Anzeigentext";
$lang["NP_ADINFO1"] = "Geschrieben von";
$lang["NP_ADSTRT"] = "Startdatum";
$lang["NP_ADEND"] = "Enddatum";
$lang["NP_BUY"] = "Eine Anzeige kaufen";
$lang["NP_BUY_REMINDER"] = "Denken Sie daran, dass der Kauf eines Hinzufügens den Spielregeln unterliegt, wenn Sie etwas hier posten, das eine Spielregel brechen wird, werden Sie gewarnt und Ihre Anzeige wird entfernt News-Papier, lassen Sie einen Administrator sofort wissen! ";
$lang["NP_BUY_TD1"] = "Anfängliche Anzeigenkosten";
$lang["NP_BUY_TD2"] = "Anzeigenlaufzeit";
$lang["NP_BUY_TD3"] = "Anzeigentext";
$lang["NP_BUY_TD4"] = "Total Anzeigenkosten";
$lang["NP_BUY_TD5"] = "Eine höhere Zahl wird Sie in der Anzeigenliste höher stellen.";
$lang["NP_BUY_TD6"] = "Jeder Tag fügt Ihren Kosten 1.250 Primärwährung hinzu.";
$lang["NP_BUY_TD7"] = "Jedes Zeichen ist 5 Primärwährung wert.";
$lang["NP_BUY_BTN"] = "Platzieren";
// Schmelzen

$lang["SMELT_HOME"] = "Smeltery";
$lang["SMELT_TH"] = "Ausgabeobjekt";
$lang["SMELT_TH1"] = "Erforderliche Elemente x Menge";
$lang["SMELT_TH2"] = "Aktion";
$lang["SMELT_DO"] = "Smelt Item";
$lang["SMELT_DONT"] = "Handwerk nicht möglich";
$lang["SMELT_ERR"] = "Sie versuchen, ein Element mit einem nicht vorhandenen Schmelzrezept zu erstellen.";
$lang["SMELT_ERR1"] = "Du fehlst ein oder mehrere Elemente, die für dieses Schmelzrezept benötigt werden.";
$lang["SMELT_SUCC"] = "Ihr habt angefangen, euer Element zu erstellen, es wird euch kurz gegeben.";
$lang["SMELT_SUCC1"] = "Sie haben erfolgreich dieses Element geschmolzen.";

// Stabschmelzen
$lang["STAFF_SMELT_HOME"] = "Staff Smeltery";
$lang["STAFF_SMELT_ADD_TH"] = "Wert";
$lang["STAFF_SMELT_ADD_TH1"] = "Eingabe";
$lang["STAFF_SMELT_ADD_TH2"] = "Smelted Item";
$lang["STAFF_SMELT_ADD_TH3"] = "Zeit zum Abschließen";
$lang["STAFF_SMELT_ADD_TH4"] = "Element erforderlich";
$lang["STAFF_SMELT_ADD_TH5"] = "Verschmutzte Artikelmenge";
$lang["STAFF_SMELT_ADD_TH6"] = "Element Bedarfsmenge";
$lang["STAFF_SMELT_ADD_SELECT1"] = "Sofort";
$lang["STAFF_SMELT_ADD_SELECT2"] = "Sekunden";
$lang["STAFF_SMELT_ADD_SELECT3"] = "Minuten";
$lang["STAFF_SMELT_ADD_SELECT4"] = "Stunden";
$lang["STAFF_SMELT_ADD_SELECT5"] = "Tage";
$lang["STAFF_SMELT_ADD_BTN"] = "Erforderliches Element hinzufügen";
$lang["STAFF_SMELT_ADD_BTN2"] = "Erforderliches Element entfernen";
$lang["STAFF_SMELT_ADD_BTN3"] = "Smelted Item hinzufügen";
$lang["STAFF_SMELT_ADD_SUCC"] = "Schmelzrezept wurde erfolgreich hinzugefügt.";
$lang["STAFF_SMELT_ADD_FAIL"] = "Eine erforderliche Eingabe fehlt. Gehen Sie zurück und versuchen Sie es erneut.";
$lang["STAFF_SMELT_DEL_FORM"] = "Verwenden Sie dieses Formular, um ein Schmelzrezept zu löschen.";
$lang["STAFF_SMELT_DEL_TH"] = "Schmelzrezept";
$lang["STAFF_SMELT_DEL_BTN"] = "Rezept löschen";
$lang["STAFF_SMELT_DEL_SUCC"] = "Das Schmelzrezept wurde erfolgreich aus dem Spiel entfernt.";
?>