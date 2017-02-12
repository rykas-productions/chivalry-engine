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
 
// Menu
$lang["MENU_EXPLORE"] = "Udforsk";
$lang["MENU_MAIL"] = "Mail";
$lang["MENU_EVENT"] = "Meddelelser";
$lang["MENU_INVENTORY"] = "Inventory";
$lang["MENU_OUT"] = "<i><small> Powered med koder fra <a href='https://twitter.com/MasterGeneralYT'> <font color=gray> TheMasterGeneral</font> </a> .. Brugt med tilladelse </small> </i> ";
$lang["MENU_PROFILE"] = "Profil";
$lang["MENU_SETTINGS"] = "Indstillinger";
$lang["MENU_STAFF"] = "Medarbejdere Panel ";
$lang["MENU_LOGOUT"] = "Log ud";
$lang["MENU_TIN"] = "Time er nu";
$lang["MENU_QE"] = "forespørgsler henrettet ";
$lang["MENU_UNREADMAIL1"] = "Ulæst Mail!";
$lang["MENU_UNREADNOTIF"] = "Ulæste meddelelser!";
$lang["MENU_FEDJAIL"] = "Federal fængsel!";
$lang["MENU_FEDJAIL1"] = "Du er i den føderale fængsel for den næste";
$lang["MENU_FEDJAIL2"] = "for forbrydelsen:";
$lang["MENU_UNREADANNONCE"] = "! Ulæste Announcments ";
$lang["MENU_UNREADANNONCE1"] = "Der er ";
$lang["MENU_UNREADANNONCE2"] = "annonceringer du endnu ikke har læst. Læs dem ";
$lang["MENU_UNREADMAIL2"] = "Du har ";
$lang["MENU_UNREADMAIL3"] = "ulæste beskeder. Klik på ";
$lang["MENU_UNREADMAIL4"] = "at læse dem. ";
$lang["MENU_UNREADNOTIF1"] = "ulæste meddelelser. Klik på ";
$lang["MENU_INFIRMARY1"] = "Du er på infirmeriet for den næste ";
$lang["MENU_DUNGEON1"] = "Du er i fangehullet for den næste ";
$lang["MENU_XPLOST"] = "Ved at køre fra kampen, har du mistet alle dine erfaringer!";
$lang["MENU_RULES"] = "Spilleregler";

// Preferences
$lang["PREF_CPASSWORD"] = "Skift adgangskode";
$lang["PREF_WELCOME_1"] = "Greetings der,";
$lang["PREF_WELCOME_2"] = "., Og velkommen til Center Indstillinger Du kan se og ændre oplysninger om din konto!";
$lang["PREF_CNAME"] = "Skift brugernavn";
$lang["PREF_CTIME"] = "Skift tidszone";
$lang["PREF_CLANG"] = "Skift sprog";
$lang["PREF_CPIC"] = "Change Display Picture";
$lang["PREF_CTHM"] = "Skift tema";
$lang["PREF_CTHM_FORM"] = "Vælg det tema, du ønsker at skifte til Denne handling kan fortrydes når som helst, du ønsker..";
$lang["PREF_CTHM_FORM1"] = "Vælg dit tema";
$lang["PREF_CTHM_FORMDD1"] = "Bright [Standard]";
$lang["PREF_CTHM_FORMDD2"] = "Mørk [Alternative]";
$lang["PREF_CTHM_FORMDD3"] = "Mørk [Purple navigationslinjetype]";
$lang["PREF_CTHM_FORMBTN"] = "Opdater tema";
$lang["PREF_CTHM_SUB_ERROR"] = "Du forsøger at bruge en ikke-eksisterende tema.";
$lang["PREF_CTHM_SUB_SUCCESS"] = "Din tema er blevet opdateret Effects vil blive mærkbar på næste side belastning..";

//Username Change
$lang["UNC_TITLE"] = "Ændring dit brugernavn ...";
$lang["UNC_INTRO"] = "Her kan du ændre dit navn, der vises i hele spillet Brug ikke en innappropriate navn eller du kan finde din privledge at ændre dit navn fjernet..";
$lang["PREF_CNAME"] = "Skift brugernavn";
$lang["UNC_ERROR_1"] = "Du har ikke engang indtaste et nyt brugernavn Klik!";
$lang["UNC_ERROR_2"] = "for at prøve igen";
$lang["UNC_LENGTH_ERROR"] = "Brugernavne skal være på mindst tre tegn, og ved maksimal, tyve tegn.";
$lang["UNC_INVALIDCHARCTERS"] = "Brugernavne kan kun bestå af tal, bogstaver, understregninger og rum!";
$lang["UNC_INUSE"] = "Brugernavnet du har valgt er i brug Vælg et andet brugernavn..";
$lang["UNC_GOOD"] = "Du har opdateret dit brugernavn!";
$lang["UNC_NUN"] = "Ny Brugernavn:";
$lang["UNC_BUTTON"] = "Skift brugernavn";

//Password Change
$lang["PW_TITLE"] = "Ændring af din adgangskode ...";
$lang["PW_CP"] = "Aktuel adgangskode";
$lang["PW_CNP"] = "Bekræft ny adgangskode";
$lang["PW_NP"] = "Ny adgangskode";
$lang["PW_BUTTON"] = "Update Password";
$lang["PW_INCORRECT"] = ". Hvad du har indtastet som din gamle adgangskode er forkert Prøv igen.";
$lang["PW_NOMATCH"] = "De nye indtastede adgangskoder er ikke ens Gå tilbage og prøv igen, tak..";
$lang["PW_DONE"] = "Dit password er blevet opdateret.";

//Pic change
$lang["PIC_TITLE"] = "Display Picture Change";
$lang["PIC_NOTE"] = "Bemærk, at dette skal hostes eksternt, <a href='https://imgur.com/'> Imgur </a> er vores anbefaling.";
$lang["PIC_NOTE2"] = "Eventuelle billeder, der ikke 250x250 automatisk ændres.";
$lang["PIC_NEWPIC"] = "Link til nyt billede:";
$lang["PIC_TOOBIG"] = "Picture for stor!";
$lang["PIC_BTN"] = "Skift billede";
$lang["PIC_TOOBIG2"] = "Dit billede er filstørrelsen er for stor Den maksimale størrelse et billede kan være er 1MB Gå tilbage og prøv igen, tak...";
$lang["PIC_NOIMAGE"] = "Du har angivet en webadresse, der er ikke engang et billede Gå tilbage og prøv igen, tak..";
$lang["PIC_SUCCESS"] = "Du har opdateret dit displaybillede Det er vist nedenfor!.";

//Login Page
$lang["LOGIN_REGISTER"] = "Registrer";
$lang["LOGIN_RULES"] = "Spilleregler";
$lang["LOGIN_LOGIN"] = "Log ind";
$lang["LOGIN_AHA"] = "Allerede har en konto?";
$lang["LOGIN_EMAIL"] = "E-mail-adresse";
$lang["LOGIN_PASSWORD"] = "Password";
$lang["LOGIN_LWE"] = "Login med e-mail";
$lang["LOGIN_SIGNIN"] = "Log ind";
$lang["LOGIN_NH"] = "Ny Her <a href='register.php'> Join Us </a>!";

//Register
$lang["REG_FORM"] = "Registrering";
$lang["REG_USERNAME"] = "Brugernavn";
$lang["REG_EMAIL"] = "Email";
$lang["REG_PW"] = "Password";
$lang["REG_CPW"] = "Bekræft adgangskode";
$lang["REG_SEX"] = "Køn";
$lang["REG_CLASS"] = "Class";
$lang["REG_REFID"] = "Referral-ID";
$lang["REG_PROMO"] = "Promo Code";
$lang["REG_WARRIORCLASS"] = "Warrior klasse!";
$lang["REG_ROGUECLASS"] = "Rogue klasse!";
$lang["REG_DEFENDERCLASS"] = "Defnder klasse!";
$lang["REG_NOCLASS"] = "Vi har brug for dig til at vælge en klasse, tak.";
$lang["REG_ROGUECLASS_INFO"] = "En rogue fighter starter med mere smidighed og mindre styrke Gennem deres eventyr, vil de vinde agility meget hurtigere end nogen anden stat, og styrke meget langsommere end de andre..";
$lang["REG_DEFENDERCLASS_INFO"] = "en forsvarsspiller starter med mere vagt og mindre smidighed Gennem deres eventyr, vil de få vagt meget hurtigere end nogen anden stat, og smidighed meget langsommere end de andre..";
$lang["REG_WARRIORCLASS_INFO"] = "en kriger tærter med mere styrke og mindre vagt hele deres eventyr, vil de vinde styrke måde hurtigere end nogen anden stat, og vogte meget langsommere end de andre..";
$lang["REG_UNIUERROR"] = "Brugernavnet du har valgt er allerede i brug Tilbage og prøv igen..";
$lang["REG_SUCCESS"] = "Du har nu sluttet sig til spillet Nyd dit ophold og du sørge for at læse spillets regler..";
$lang["REG_EIUERROR"] = "Den e-mail, du har valgt er allerede i brug Gå tilbage og prøv igen..";
$lang["REG_PWERROR"] = "Du skal indtaste en adgangskode og bekræfte den Gå tilbage og prøv igen..";
$lang["REG_REFERROR"] = "Den indbringelsen du angav eksisterer ikke i spillet Gå tilbage og kontrollere igen..";
$lang["REG_REFMERROR"] = ". Indbringelsen du angav deler samme IP, som du Ingen oprette flere konti De admins er blevet advaret..";
$lang["REG_VPWERROR"] = "De indtastede adgangskoder er ikke ens Gå tilbage og prøv igen..";
$lang["REG_CAPTCHAERROR"] = "Du undlod captcha, eller bare ikke indtaste den Gå tilbage og prøv igen..";
$lang["REG_GENDERERROR"] = "Du har angivet et ugyldigt køn Venligst gå tilbage og prøv igen..";
$lang["REG_CLASSERROR"] = "Du har angivet et ugyldigt kampene klasse Venligst gå tilbage og prøv igen..";
$lang["REG_EMAILERROR"] = "Du indtastede ikke en gyldig e-mail, eller har undladt at komme ind på e-mail feltet Venligst gå tilbage og prøv igen..";
$lang["REG_MULTIALERT"] = "Hold på der. Vi har opdaget, at en person med din IP-adresse allerede er registreret. Vi vil stoppe dig her for nu. Hvis det er en falsk positiv, kan du kontakte spillet ejere . ";

//CSRF Error
$lang["CSRF_ERROR_TITLE"] = "Action Blokeret!";
$lang["CSRF_PREF_MENU"] = "Du kan prøve handlingen igen ved at gå";
$lang["CSRF_ERROR_TEXT"] = "Handlingen du forsøgte at gøre blev blokeret. Det blev blokeret, fordi du har lagt en anden side på spillet. Hvis du ikke har indlæst en anden side i løbet af denne tid, skal du straks ændre din adgangskode, som en anden person kan få adgang til din konto! ";

//Alert Titles
$lang["ERROR_EMPTY"] = "Tøm Input!";
$lang["ERROR_LENGTH"] = "Check Input Længde!";
$lang["ERROR_GENERIC"] = "Uh Oh!";
$lang["ERROR_SUCCESS"] = "Succes!";
$lang["ERROR_INVALID"] = "Invalid Input!";
$lang["ERROR_SECURITY"] = "Security Error!";
$lang["ERROR_NONUSER"] = "Findes ikke Bruger!";
$lang["ERROR_NOPERM"] = "Ingen tilladelse!";
$lang["ERROR_UNKNOWN"] = "Ukendt fejl";
$lang["ERROR_INFO"] = "Information";

//Misc. Alerts Details
$lang["ALERT_INSTALLER"] = "Installationsprogrammet fil kunne ikke slettes Husk at slette installer.php fra din hjemmeside rodmappe, eller du vil risikere en anden bruger der kører installationsprogrammet og ødelægge dit spil..";

//Generic
$lang["GEN_HERE"] = "her";
$lang["GEN_back"] = "tilbage";
$lang["GEN_INFIRM"] = "Bevidstløs!";
$lang["GEN_DUNG"] = "Locked Up!";
$lang["GEN_GREETING"] = "Hej";
$lang["GEN_MINUTES"] = "minutter.";
$lang["GEN_EXP"] = "oplevelse";
$lang["GEN_NEU"] = "Slettet konto";
$lang["GEN_AT"] = "på";
$lang["GEN_EDITED"] = "redigeret";
$lang["GEN_TIMES"] = "tider.";
$lang["GEN_RANK"] = "Rank ";
$lang["GEN_ONLINE"] = "Online";
$lang["GEN_OFFLINE"] = "Offline";
$lang["GEN_FOR"] = "for";
$lang["GEN_INDAH"] = "I";
$lang["GEN_YES"] = "Ja";
$lang["GEN_NO"] = "Nej";
$lang["GEN_STR"] = "Strength";
$lang["GEN_AGL"] = "Agility";
$lang["GEN_GRD"] = "Guard";
$lang["GEN_IQ"] = "IQ";
$lang["GEN_LAB"] = "Labor";
$lang["GEN_GOHOME"] = "Go Home";
$lang["GEN_IUOF"] = "Ugyldig brug af fil!";
$lang["GEN_THEM"] = "Them";
$lang["GEN_CONTINUE"] = "Fortsæt";
$lang["GEN_FOR_S"] = "for";
$lang["GEN_NOPERM"] = "Du har ikke den rette brugerniveau for at se denne side Hvis det er forkert, skal du kontakte en admin med det samme.";

//Gym
$lang["GYM_INFIRM"] = "Mens du er bevidstløs, kan du ikke træne Kom tilbage efter du føler sundt!";
$lang["GYM_DUNG"] = "Vagterne vil normalt lade dig arbejde ud, men, hvad du gjorde blev anset for høj for en forbrydelse Du kan ikke træne lige nu ....";
$lang["GYM_NEG"] = "Ikke nok energi!";
$lang["GYM_INVALIDSTAT"] = "Du kan ikke træne, at stat!";
$lang["GYM_NEG_DETAIL"] = "Du har ikke nok energi til at træne at mange gange enten vente på din energi til at komme sig, eller refill det manuelt.";

//Explore
$lang["EXPLORE_INTRO"] = "Du begynder at udforske byen og finde et par cool ting at holde dig besat ... ";
$lang["EXPLORE_REF"] = "Det er din henvisning link Giv det til venner, fjender eller bare spam det rundt Du vil modtage 25 Sekundær valuta dem sammenføjning..!";
$lang["EXPLORE_SHOP"] = "Butikker";
$lang["EXPLORE_LSHOP"] = "Lokale butikker";
$lang["EXPLORE_POSHOP"] = "Player-ejede butikker";
$lang["EXPLORE_IMARKET"] = "Punkt marked";
$lang["EXPLORE_IAUCTION"] = "element auktion";
$lang["EXPLORE_TRADE"] = "Trading";
$lang["EXPLORE_SCMARKET"] = "Sekundær valuta marked";
$lang["EXPLORE_FD"] = "Finansiel";
$lang["EXPLORE_BANK"] = "Bank";
$lang["EXPLORE_ESTATES"] = "Estates";
$lang["EXPLORE_HL"] = "Labor";
$lang["EXPLORE_MINE"] = "Mining";
$lang["EXPLORE_SMELT"] = "Smeltery";
$lang["EXPLORE_WC"] = "hugge brænde";
$lang["EXPLORE_FARM"] = "Farming";
$lang["EXPLORE_ADMIN"] = "Administration";
$lang["EXPLORE_USERLIST"] = "Brugerliste";
$lang["EXPLORE_STAFFLIST"] = "Staff List";
$lang["EXPLORE_FED"] = "Federal Jail";
$lang["EXPLORE_STATS"] = "spilstatistik";
$lang["EXPLORE_REPORT"] = "Player rapport";
$lang["EXPLORE_GAMES"] = "Spil";
$lang["EXPLORE_RR"] = "Russian Roulette";
$lang["EXPLORE_HILO"] = "High / Low";
$lang["EXPLORE_ROULETTE"] = "Roulette";
$lang["EXPLORE_GUILDS"] = "Guilds";
$lang["EXPLORE_DUNG"] = "Dungeon";
$lang["EXPLORE_INFIRM"] = "Infirmary";
$lang["EXPLORE_GYM"] = "uddannelse";
$lang["EXPLORE_JOB"] = "Dit job";
$lang["EXPLORE_ACADEMY"] = "Local Academy";
$lang["EXPLORE_PINTER"] = "Social";
$lang["EXPLORE_FORUMS"] = "Forums";
$lang["EXPLORE_NEWSPAPER"] = "Avis";
$lang["EXPLORE_ACT"] = "Aktiviteter";
$lang["EXPLORE_ANNOUNCEMENTS"] = "Meddelelser";
$lang["EXPLORE_CRIMES"] = "Criminal Center";
$lang["EXPLORE_TRAVEL"] = "Travel Horse";
$lang["EXPLORE_GUILDLIST"] = "Guild List";
$lang["EXPLORE_YOURGUILD"] = "Din Guild";
$lang["EXPLORE_TOPTEN"] = "Top 10 spillere";
$lang["EXPLORE_SLOTS"] = "Spilleautomater";
$lang["EXPLORE_BOTS"] = "Bot List";

//Error Details
$lang["ERRDE_EXPLORE"] = "Da du er i infirmeriet, kan du ikke besøge byen!";
$lang["ERRDE_EXPLORE2"] = "Da du er i fangehullet, kan du ikke besøge byen!";
$lang["ERRDE_PN"] = "Din personlige notesblok kunne ikke opdateres på grund af grænsen på 65.655 tegn.";
$lang["ERROR_MAIL_UNOWNED"] = "Du kan ikke læse denne besked, da det ikke blev sendt til dig!";
$lang["ERROR_FORUM_VF"] = ". Gå tilbage og prøv igen for os, bedes Vi gjort brød.";

//Index
$lang["INDEX_TITLE"] = "Generel Info";
$lang["INDEX_WELCOME"] = "Velkommen tilbage,";
$lang["INDEX_YLVW"] = "Din sidste besøg var på";
$lang["INDEX_LEVEL"] = "Level";
$lang["INDEX_CLASS"] = "Class";
$lang["INDEX_VIP"] = "VIP dage";
$lang["INDEX_PRIMCURR"] = "Primær valuta";
$lang["INDEX_SECCURR"] = "Sekundær valuta";
$lang["INDEX_ENERGY"] = "Energi";
$lang["INDEX_BRAVE"] = "Brave";
$lang["INDEX_WILL"] = "Will";
$lang["INDEX_PN"] = "Personal Notepad";
$lang["INDEX_PNSUCCESS"] = "Din personlige notesblok er blevet opdateret.";
$lang["INDEX_EXP"] = "Erfaring";

//Form Buttons
$lang["FB_PN"] = "Opdater Notes";
$lang["FB_PR"] = "Submit Player rapport";

//Player Report
$lang["PR_TITLE"] = "Player rapport";
$lang["PR_INTRO"] = "Kender du nogen, der brød reglerne, eller er bare at være unhonorable? Dette er stedet at indberette dem. Rapporter brugeren bare én gang. Rapportering den samme bruger flere gange vil bremse processen. Hvis du er, der misbruger spilleren rapport system, vil du blive placeret væk i føderale fængsel. oplysninger, du indtaster her vil forblive fortroligt og vil kun blive læst af ledende medarbejdere. Hvis du ønsker at bekende til en forbrydelse, det er også en stor lægge for. ";
$lang["PR_USER"] = "Bruger?";
$lang["PR_CATEGORY"] = "Kategori?";
$lang["PR_REASON"] = "Hvad har de gjort?";
$lang["PR_USER_PH"] = "Bruger-ID på afspilleren bliver dårlig.";
$lang["PR_REASON_PH"] = "Vedlæg så mange oplysninger som muligt.";
$lang["PR_CAT_1"] = "Bug misbrug ";
$lang["PR_CAT_2"] = "Player Chikane ";
$lang["PR_CAT_3"] = "snydere";
$lang["PR_CAT_4"] = "Spamming";
$lang["PR_CAT_5"] = "Opmuntrende Rule Breaking ";
$lang["PR_CAT_6"] = "Sikkerhed Issue ";
$lang["PR_CAT_7"] = "Øvrige";
$lang["PR_CATBAD"] = "Du har angivet et ugyldigt kategori. Gå tilbage og prøv igen, skal du. ";
$lang["PR_MAXCHAR"] = "Du forsøger at komme ind for lang af en grund. Denne formular vil kun tillade dig at komme ind, ved maksimal, 1250 total tegn. Gå tilbage og prøv igen, skal du. ";
$lang["PR_INVALID_USER"] = "Du forsøger at rapportere en spiller, der bare ikke eksisterer. Kontroller bruger-id, du har indtastet, og prøv igen. ";
$lang["PR_SUCCESS"] = "Du har rapporteret brugeren. Personalet kan sende dig en besked at stille spørgsmål om den rapport, du lige har sendt. Vær venlig at besvare dem efter bedste af dine evner. ";

//Mail
$lang["MAIL_READ"] = "Læs";
$lang["MAIL_DELETE"] = "Slet";
$lang["MAIL_REPORT"] = "Rapport";
$lang["MAIL_MSGREAD"] = "Message Læs ";
$lang["MAIL_MSGUNREAD"] = "Message ulæste ";
$lang["MAIL_USERDATE"] = "Bruger / Info";
$lang["MAIL_PREVIEW"] = "Message Preview";
$lang["MAIL_ACTION"] = "Handlinger";
$lang["MAIL_USERINFO"] = "Sender Info";
$lang["MAIL_MSGSUB"] = "Emne / Message";
$lang["MAIL_STATUS"] = "status";
$lang["MAIL_SENTAT"] = "Sendt på ";
$lang["MAIL_SENDTO"] = "Til";
$lang["MAIL_FROM"] = "From";
$lang["MAIL_SUBJECT"] = "Emne";
$lang["MAIL_MESSAGE"] = "Message";
$lang["MAIL_REPLYTO"] = "Svar til";
$lang["MAIL_EMPTYINPUT"] = "Det ser du ikke indtaste en meddelelse, der skal sendes. Venligst gå tilbage og indtaste en besked! ";
$lang["MAIL_INPUTLNEGTH"] = "Det ser ud til, at du forsøger at sende en lang besked. Husk, at meddelelser kun kan være 65,655 tegn, og fag kan kun være 50 tegn lang. ";
$lang["MAIL_NOUSER"] = "Du skal indtaste en modtager til denne besked! Gå tilbage og prøv igen! ";
$lang["MAIL_UDNE"] = "Bruger eksisterer ikke! ";
$lang["MAIL_UDNE_TEXT"] = "Du forsøger at sende en besked til en bruger, der ikke eksisterer. Tjek din kilde, og prøv igen. ";
$lang["MAIL_SUCCESS"] = "Du har sendt en besked!";
$lang["MAIL_TIMEERROR"] = "Du skal vente 60 sekunder før du kan sende en besked til denne bruger hjælp af denne formular specifikt. Hvis du har brug for hurtigt besvare nogen, kan du stadig bruge det normale postsystem. ";
$lang["MAIL_READALL"] = "Alle dine ulæste meddelelser er blevet markeret som læst! ";
$lang["MAIL_DELETECONFIRM"] = "Er du 100% sikker på du vil tømme din indbakke? Dette kan ikke fortrydes.";
$lang["MAIL_DELETEYES"] = "Ja, jeg er 100% sikker på ";
$lang["MAIL_DELETENO"] = "Hold, ved nærmere eftertanke ";
$lang["MAIL_DELETEDONE"] = "Hele dit indbakke er blevet ryddet.";
$lang["MAIL_QUICKREPLY"] = "Afsendelse et hurtigt svar ... ";
$lang["MAIL_MARKREAD"] = "Marker alle som læste ";
$lang["MAIL_SENDMSG"] = "Send besked";

//Language menu
$lang["LANG_INTRO"] = "Her kan du ændre dit sprog. Dette er ikke gemt på din konto. Dette gemmes via en cookie. Hvis du ændrer enheder eller tørre dine cookies, skal du nulstille dit sprog igen. Oversættelser kan ikke være 100% korrekte. ";
$lang["LANG_BUTTON"] = "Skift sprog";
$lang["LANG_UPDATE"] = "Du har angivet et sprog, der ikke er gyldig.";
$lang["LANG_UPDATE2"] = "Du har opdateret dit sprog! ";

//Notifications page
$lang["NOTIF_TABLE_HEADER1"] = "påmindelse af Info";
$lang["NOTIF_TABLE_HEADER2"] = "påmindelse af tekst ";
$lang["NOTIF_DELETE_SINGLE"] = "Du har slettet en meddelelse. ";
$lang["NOTIF_DELETE_SINGLE_FAIL"] = "Du kan ikke slette denne meddelelse, da den enten ikke eksisterer eller ikke tilhører dig. ";
$lang["NOTIF_TITLE"] = "Sidste fifthteen meddelelser, der tilhører dig ... ";
$lang["NOTIF_READ"] = "Notification Læs ";
$lang["NOTIF_UNREAD"] = "Notification ulæste ";
$lang["NOTIF_DELETE"] = "Slet meddelelse ";

//Bank
$lang["BANK_BUY1"] = "Åbn en bankkonto i dag, bare";
$lang["BANK_BUYYES"] = "Tilmeld Me Up!";
$lang["BANK_SUCCESS"] = "Tillykke, du har købt en bankkonto for";
$lang["BANK_SUCCESS1"] = "Start Brug Min konto!";
$lang["BANK_FAIL"] = "Du har ikke nok {$lang['INDEX_PRIMCURR']} til at købe en bankkonto Kom tilbage senere, når du har nok Du har brug for..";
$lang["BANK_HOME"] = "Du har i øjeblikket";
$lang["BANK_HOME1"] = "i lav-niveau bank.";
$lang["BANK_HOME2"] = "I slutningen af ​​hver dag, din bank saldo vil stige med 2%.";
$lang["BANK_DEPOSIT_WARNING"] = "Det vil koste dig";
$lang["BANK_DEPOSITE_WARNING1"] = "af de penge du indbetaler, rundet op Den maksimale gebyr er.";
$lang["BANK_AMOUNT"] = "Beløb:";
$lang["BANK_DEPOSIT"] = "Deposit";
$lang["BANK_WITHDRAW_WARNING"] = "Heldigvis for dig, er der ingen gebyr til tilbagekøb.";
$lang["BANK_WITHDRAW"] = "Udbetal";
$lang["BANK_D_ERROR"] = "Du forsøger at indbetale penge, du behøver ikke engang!";
$lang["BANK_D_SUCCESS"] = "Du aflevere";
$lang["BANK_D_SUCCESS1"] = "skal deponeres Efter gebyr (.";
$lang["BANK_D_SUCCESS2"] = ") er taget,";
$lang["BANK_D_SUCCESS3"] = "føjes til din bankkonto <b> Du har nu.";
$lang["BANK_D_SUCCESS4"] = "på din konto </b>.";
$lang["BANK_W_FAIL"] = "Du forsøger at trække mere {$lang['INDEX_PRIMCURR']}, end du i øjeblikket har i banken.";
$lang["BANK_W_SUCCESS"] = "Du held trak";
$lang["BANK_W_SUCCESS1"] = ". Fra din bankkonto Du har";
$lang["BANK_W_SUCCESS2"] = "venstre på din bankkonto.";

//Forums
$lang["FORUM_EMPTY_REPLY"] = "Du forsøger at indsende en tom svar, som du ikke kan gøre Sørg for at du udfyldt svarblanketten!";
$lang["FORUM_TOPIC_DNE_TITLE"] = "Ikke-eksisterende emne!";
$lang["FORUM_TOPIC_DNE_TEXT"] = "Du forsøger at interagere med et emne, som ikke findes Tjek din kilde, og prøv igen..";
$lang["FORUM_FORUM_DNE_TITLE"] = "Ikke-eksisterende Forum!";
$lang["FORUM_FORUM_DNE_TEXT"] = "Du forsøger at interagere med et forum, der ikke eksisterer Tjek din kilde, og prøv igen..";
$lang["FORUM_POST_DNE_TITLE"] = "Ikke-eksisterende indlæg!";
$lang["FORUM_POST_DNE_TEXT"] = "Du forsøger at interagere med et indlæg, der ikke eksisterer Tjek din kilde, og prøv igen..";
$lang["FORUM_NOPERMISSION"] = "Du forsøger at interagere med et forum du har ingen tilladelse til at interagere med Hvis det er en fejl, skal du advare en admin lige væk.";
$lang["FORUM_FORUMS"] = "Forums";
$lang["FORUM_ON"] = "On";
$lang["FORUM_IN"] = "I:";
$lang["FORUM_BY"] = "Af:";
$lang["FORUM_STAFFONLY"] = "Staff-Only";
$lang["FORUM_F_LP"] = "Nyeste indlæg";
$lang["FORUM_F_TC"] = "Emne Count";
$lang["FORUM_F_PC"] = "Antal indlæg";
$lang["FORUM_F_FN"] = "Forum navn";
$lang["FORUM_FORUMSHOME"] = "Forums Home";
$lang["FORUM_TOPICNAME"] = "Emne navn";
$lang["FORUM_TOPICOPEN"] = "Emne åbnede";
$lang["FORUM_TOPIC_MOVE"] = "Flyt Emne";
$lang["FORUM_PAGES"] = "Sider:";
$lang["FORUM_TOPIC_MTT"] = "Flyt Topic Til:";
$lang["FORUM_TOPIC_PIN"] = "Pin / Frigør Emne";
$lang["FORUM_TOPIC_LOCK"] = "Lås / Lås op Emne";
$lang["FORUM_TOPIC_DELETE"] = "Slet Emne";
$lang["FORUM_POST_EDIT"] = "Rediger Post";
$lang["FORUM_POST_QUOTE"] = "Citér";
$lang["FORUM_POST_DELETE"] = "Slet Post";
$lang["FORUM_POST_EDIT_1"] = "Dette indlæg blev senest redigeret af";
$lang["FORUM_NOSIG"] = "Ingen signatur";
$lang["FORUM_POST_POSTED"] = "Svarede den:";
$lang["FORUM_POST_POST"] = "Post";
$lang["FORUM_POST_REPLY"] = "Besvar";
$lang["FORUM_POST_REPLY2"] = "Besvar til Emne";
$lang["FORUM_POST_REPLY_INFO"] = "Indtast dit svar her Husk, du kan bruge BBCode Sørg for at du vil ikke bryde nogen spillets regler, når udstationering.!.";
$lang["FORUM_POST_TIL"] = "Dette emne er låst, og på grund af dette, kan du ikke skrive et svar til dette emne.";
$lang["FORUM_MAX_CHAR_REPLY"] = "Når du skriver i forummet, kan dit indlæg kun indeholde 65.535 tegn ved maksimal Gå tilbage og prøv igen.";
$lang["FORUM_REPLY_SUCCESS"] = "Du har med succes sendt dit svar til dette emne.";
$lang["FORUM_TOPIC_FORM_TITLE"] = "Emne navn";
$lang["FORUM_TOPIC_FORM_DESC"] = "Emne Beskrivelse";
$lang["FORUM_TOPIC_FORM_TEXT"] = "Emne tekst";
$lang["FORUM_TOPIC_FORM_BUTTON"] = "Indlæg Emne";
$lang["FORUM_TOPIC_FORM_TITLE_LENGTH"] = "Emne navne og beskrivelser kan kun være 255 tegn, på maksimum.";
$lang["FORUM_TOPIC_FORM_PAGE"] = "Nyt emne Form";
$lang["FORUM_TOPIC_FORM_SUCCESS"] = "Du har indsendt et nyt emne i fora!";
$lang["FORUM_QUOTE_FORM_PAGENAME"] = "citere en Post";
$lang["FORUM_QUOTE_FORM_INFO"] = "citere et indlæg ...";
$lang["FORUM_EDIT_FORM_INFO"] = "Redigering af en post ...";
$lang["FORUM_EDIT_FORM_PAGENAME"] = "Redigering af en Post";
$lang["FORUM_EDIT_NOPERMISSION"] = "Du har ikke tilladelse til at redigere dette indlæg Hvis du mener at dette er forkert, så lad en admin vide ASAP.!";
$lang["FORUM_EDIT_FORM_SUBMIT"] = "Rediger Post";
$lang["FORUM_EDIT_SUCCESS"] = "Du har redigeret et indlæg!";
$lang["FORUM_MOVE_TOPIC_DFDNE"] = "Du forsøger at flytte et emne til et forum, der ikke eksisterer Gå tilbage og prøv igen, tak..";
$lang["FORUM_MOVE_TOPIC_DONE"] = "Du har nu flyttet emnet.";

//Send Cash Form
$lang["SCF_POSCASH"] = "Du skal sende mindst en {$lang['INDEX_PRIMCURR']} for at benytte denne form.";
$lang["SCF_UNE"] = "Du kan ikke sende {$lang['INDEX_PRIMCURR']} til en ikke-eksisterende bruger!";
$lang["SCF_NEC"] = "Du forsøger at sende mere {$lang['INDEX_PRIMCURR']}, end du har i øjeblikket!";
$lang["SCF_SUCCESS"] = "{$lang['INDEX_PRIMCURR']} sendt succuessfully.";

//Profile
$lang["PROFILE_UNF"] = "Vi kunne ikke finde en bruger med den bruger-id, du har indtastet Du kunne modtage denne besked, fordi den spiller du forsøger at visningen fik slettet Tjek din kilde igen..!";
$lang["PROFILE_PROFOR"] = "Profil For";
$lang["PROFILE_LOCATION"] = "Location:";
$lang["PROFILE_GUILD"] = "Guild";
$lang["PROFILE_PI"] = "Phyiscal Information";
$lang["PROFILE_ACTION"] = "Handlinger";
$lang["PROFILE_FINANCIAL"] = "Finansielle oplysninger";
$lang["PROFILE_STAFF"] = "Staff område";
$lang["PROFILE_REGISTERED"] = "registreret";
$lang["PROFILE_ACTIVE"] = "Sidst aktiv";
$lang["PROFILE_LOGIN"] = "Sidste login";
$lang["PROFILE_AGE"] = "Age";
$lang["PROFILE_DAYS_OLD"] = "gamle.";
$lang["PROFILE_REF"] = "Henvisninger";
$lang["PROFILE_FRI"] = "Friends";
$lang["PROFILE_ENE"] = "fjender";
$lang["PROFILE_ATTACK"] = "Attack";
$lang["PROFILE_SPY"] = "Spy On";
$lang["PROFILE_POKE"] = "Poke";
$lang["PROFILE_MSG1"] = "Sender";
$lang["PROFILE_MSG2"] = "en besked";
$lang["PROFILE_MSG3"] = "Modtager:";
$lang["PROFILE_MSG4"] = "Besked:";
$lang["PROFILE_MSG5"] = "Luk vindue";
$lang["PROFILE_MSG6"] = "Send besked";
$lang["PROFILE_CASH"] = "Send Cash";
$lang["PROFILE_STAFF_DATA"] = "Data";
$lang["PROFILE_STAFF_LOC"] = "Location";
$lang["PROFILE_STAFF_LH"] = "Sidste Hit";
$lang["PROFILE_STAFF_LL"] = "Sidste login";
$lang["PROFILE_STAFF_REGIP"] = "Tilmeld";
$lang["PROFILE_STAFF_THRT"] = "Trussel?";
$lang["PROFILE_STAFF_RISK"] = "Risk Level <br /> <small> 1 er lav, 5 er høj </small>";
$lang["PROFILE_STAFF_OS"] = "Browser / OS";
$lang["PROFILE_STAFF_NOTES"] = "Personale noter:";
$lang["PROFILE_STAFF_BTN"] = "Update Notes About";
$lang["PROFILE_BTN_MSG"] = "Send";
$lang["PROFILE_BTN_MSG1"] = "En besked";
$lang["PROFILE_BTN_SND"] = "Send";

//Equip Items
$lang["EQUIP_NOITEM"] = "Punkt ikke kan findes, og som et resultat, kan du ikke udstyre det.";
$lang["EQUIP_NOITEM_TITLE"] = "Punkt eksisterer ikke!";
$lang["EQUIP_NOTWEAPON"] = "Den genstand, du forsøger at udstyre ikke kan udstyres som et våben.";
$lang["EQUIP_NOTWEAPON_TITLE"] = "Ugyldig Weapon!";
$lang["EQUIP_NOSLOT"] = "Du forsøger at udstyre dette punkt til en ugyldig eller ikke-eksisterende slot.";
$lang["EQUIP_NOSLOT_TITLE"] = "Ugyldig Equipment Slot!";
$lang["EQUIP_WEAPON_SUCCESS1"] = "Du har nu udstyret";
$lang["EQUIP_WEAPON_SUCCESS2"] = "som din";
$lang["EQUIP_WEAPON_SLOT1"] = "Primær våben";
$lang["EQUIP_WEAPON_SLOT2"] = "Sekundær Våben";
$lang["EQUIP_WEAPON_SLOT3"] = "Armor";
$lang["EQUIP_WEAPON_TITLE"] = "Udstyre en Weapon";
$lang["EQUIP_WEAPON_TEXT_FORM_1"] = "Vælg det sted, du ønsker at udstyre din";
$lang["EQUIP_WEAPON_TEXT_FORM_2"] = ". Til Hvis du allerede holder et våben i sprækken, du vælger, vil det blive flyttet tilbage til din beholdning.";
$lang["EQUIP_WEAPON_EQUIPAS"] = "Equip As";
$lang["EQUIP_ARMOR_TITLE"] = "udstyre Armor";
$lang["EQUIP_ARMOR_TEXT_FORM_1"] = "Du forsøger at udstyre din";
$lang["EQUIP_ARMOR_TEXT_FORM_2"] = "til din rustning slot Hvis du allerede er iført rustning, vil det blive flyttet tilbage til din beholdning..";
$lang["EQUIP_NOTARMOR"] = "Den genstand, du forsøger at udstyre ikke kan udstyres som rustning.";
$lang["EQUIP_NOTARMOR_TITLE"] = "Ugyldig Armor!";
$lang["EQUIP_OFF_ERROR1"] = "Du forsøger at unequip et element fra et ikke-eksisterende slot.";
$lang["EQUIP_OFF_ERROR2"] = "Du har ikke en post i denne slot.";
$lang["EQUIP_OFF_SUCCESS"] = "Du har med succes unequipped varen fra din";
$lang["EQUIP_OFF_SUCCESS1"] = "slot.";

//Polling Staff
$lang["STAFF_POLL_TITLE"] = "Polling Administration";
$lang["STAFF_POLL_TITLES"] = "Start en afstemning";
$lang["STAFF_POLL_TITLEE"] = "End en afstemning";
$lang["STAFF_POLL_START_INFO"] = "Stil et spørgsmål, og derefter give nogle mulige svar.";
$lang["STAFF_POLL_START_CHOICE"] = "Valg #";
$lang["STAFF_POLL_START_QUESTION"] = "Spørgsmål";
$lang["STAFF_POLL_START_HIDE"] = "Skjul resultater indtil udgangen af afstemningen?";
$lang["STAFF_POLL_START_BUTTON"] = "Opret Afstemning";
$lang["STAFF_POLL_START_ERROR"] = "Du skal have et spørgsmål, og mindst to svar!";
$lang["STAFF_POLL_START_SUCCESS"] = "Du har nu åbnet en meningsmåling til spillet.";
$lang["STAFF_POLL_END_SUCCESS"] = "Du har lukket en aktiv meningsmåling.";
$lang["STAFF_POLL_END_FORM"] = "Vælg afstemningen du ønsker at lukke.";
$lang["STAFF_POLL_END_BTN"] = "Luk Selected Poll";
$lang["STAFF_POLL_END_ERR"] = "Du forsøger at lukke en ikke-eksisterende meningsmåling.";

//Polling
$lang["POLL_TITLE"] = "Polling Booth";
$lang["POLL_CYV"] = "Kast din stemme i dag!";
$lang["POLL_VOP"] = "Vis tidligere åbnede afstemninger";
$lang["POLL_AVITP"] = "Du kan kun stemme én gang pr meningsmåling.";
$lang["POLL_PCNT"] = "Du kan ikke stemme i en meningsmåling, som ikke eksisterer, eller tidligere var lukket.";
$lang["POLL_VOTE_SUCCESS"] = "Du har med succes støbt din stemme i denne afstemning.";
$lang["POLL_VOTE_NOPOLL"] = "Der er ingen afstemninger åbnes på dette tidspunkt Kom tilbage senere..";
$lang["POLL_VOTE_CHOICE"] = "Choice";
$lang["POLL_VOTE_VOTES"] = "Stemmer";
$lang["POLL_VOTE_PERCENT_VOTES"] = "Procentdel";
$lang["POLL_VOTE_AV"] = "(allerede stemt!)";
$lang["POLL_VOTE_NV"] = "(ikke stemt!)";
$lang["POLL_VOTE_HIDDEN"] = "Resultaterne af denne afstemning er skjult indtil det udløber.";
$lang["POLL_VOTE_QUESTION"] = "Spørgsmål:";
$lang["POLL_VOTE_YVOTE"] = "Din stemme:";
$lang["POLL_VOTE_TVOTE"] = "Stemmer i alt:";
$lang["POLL_VOTE_VOTEC"] = "Vælg";
$lang["POLL_VOTE_CAST"] = "Cast Stem";
$lang["POLL_VOTE_NOCLOSED"] = "Der er ingen lukkede meningsmålinger på dette tidspunkt Kom tilbage senere, når personalet lukke en meningsmåling..";

//Forum Staff
$lang["STAFF_FORUM_ADD"] = "Tilføj Forum Kategori";
$lang["STAFF_FORUM_EDIT"] = "Rediger Forum Kategori";
$lang["STAFF_FORUM_DEL"] = "Slet Forum Kategori";
$lang["STAFF_FORUM_ADD_NAME"] = "Forum navn";
$lang["STAFF_FORUM_ADD_DESC"] = "Forum Beskrivelse";
$lang["STAFF_FORUM_ADD_AUTHORIZE"] = "Autorisation";
$lang["STAFF_FORUM_ADD_AUTHORIZEP"] = "Offentlig";
$lang["STAFF_FORUM_ADD_AUTHORIZES"] = "Staff-Only";
$lang["STAFF_FORUM_ADD_BTN"] = "Opret Forum";
$lang["STAFF_FORUM_ADD_ERRNAME"] = "Forummet navn input var enten ugyldige eller tomme venligst kontrollere igen, og prøv igen..";
$lang["STAFF_FORUM_ADD_ERRDESC"] = "Forummet beskrivelse input var enten ugyldige eller tomme venligst kontrollere igen, og prøv igen..";
$lang["STAFF_FORUM_ADD_ERRNIU"] = "Dette forum navn, du har valgt er allerede i brug Prøv venligst igen med et nyt navn..";
$lang["STAFF_FORUM_ADD_SUCCESS"] = "Du har nu tilføjet et forum kategori til spillet.";
$lang["STAFF_FORUM_EDIT_ERRINV"] = "Du har angivet et ugyldigt forum id Prøv igen..";
$lang["STAFF_FORUM_EDIT_BTN"] = "Rediger Forum";
$lang["STAFF_FORUM_EDIT_ERREMPTY"] = "En eller flere indgange på forrige side er tom Udfyld formularen og prøv igen..";
$lang["STAFF_FORUM_EDIT_SUCCESS"] = "Du har redigeret forum.";
$lang["STAFF_FORUM_DEL_BTN"] = "Slet Forum";
$lang["STAFF_FORUM_DEL_INFO"] = "Sletning fora er permenant Dette vil også fjerne de stillinger inde i dem så godt..";
$lang["STAFF_FORUM_EDIT_ERRFDNE"] = "Dette forum du vælger at slette ikke eksisterer Gå tilbage og kontrollere, og prøv igen..";
$lang["STAFF_FORUM_DEL_SUCCESS"] = "Succesfuld slettet forummet, sammen med hvad emner og indlæg var i dem tidligere.";

//Item Use
$lang["IU_UI"] = "Du forsøger at bruge en uspecificeret element Tjek dit link, og prøv igen.";
$lang["IU_UNUSED_ITEM"] = "Dette emne er ikke konfigureret til at blive brugt Du kan ikke bruge elementer med en konfigureret brug..";
$lang["IU_ITEM_NOEXIST"] = "Den genstand, du forsøger at bruge eksisterer ikke Tjek dine kilder, og prøv igen..";
$lang["IU_SUCCESS"] = "er blevet anvendt med succes Opdater for at ændringerne kan træde i kraft..";

//Staff items
$lang["STAFF_ITEM_GIVE_TITLE"] = "Give Element til Bruger";
$lang["STAFF_ITEM_GIVE_FORM_USER"] = "Bruger";
$lang["STAFF_ITEM_GIVE_FORM_ITEM"] = "Item";
$lang["STAFF_ITEM_GIVE_FORM_QTY"] = "Mængde";
$lang["STAFF_ITEM_GIVE_FORM_BTN"] = "Giv Item";
$lang["STAFF_ITEM_GIVE_SUB_NOITEM"] = "Du har ikke angivet det element, du ønsker at give til brugeren.";
$lang["STAFF_ITEM_GIVE_SUB_NOQTY"] = "Du har ikke angive størrelsen af det emne, du ønsker at give til brugeren.";
$lang["STAFF_ITEM_GIVE_SUB_NOUSER"] = "Du har ikke angivet den bruger, du ønsker at give et element for at.";
$lang["STAFF_ITEM_GIVE_SUB_ITEMDNE"] = "Den genstand, du forsøger at give væk findes ikke.";
$lang["STAFF_ITEM_GIVE_SUB_USERDNE"] = "Brugeren du prøver at give et element for at findes ikke.";
$lang["STAFF_ITEM_GIVE_SUB_SUCCESS"] = "vare (r) er blevet begavet med succes.";

//Staff Crimes
$lang["STAFF_CRIME_TITLE"] = "Forbrydelser";
$lang["STAFF_CRIME_MENU_CREATE"] = "Opret kriminalitet";
$lang["STAFF_CRIME_MENU_CREATECG"] = "Opret Crime Group";
$lang["STAFF_CRIME_NEW_TITLE"] = "Tilføjelse af en ny forbrydelse.";
$lang["STAFF_CRIME_NEW_NAME"] = "Crime navn";
$lang["STAFF_CRIME_NEW_BRAVECOST"] = "Bravery Cost";
$lang["STAFF_CRIME_NEW_SUCFOR"] = "Succes Formula";
$lang["STAFF_CRIME_NEW_SUCPRIMIN"] = "Succes Minimum {$lang['INDEX_PRIMCURR']}";
$lang["STAFF_CRIME_NEW_SUCPRIMAX"] = "Succes Maximum {$lang['INDEX_PRIMCURR']}";
$lang["STAFF_CRIME_NEW_SUCSECMIN"] = "Succes Minimum {$lang['INDEX_SECCURR']}";
$lang["STAFF_CRIME_NEW_SUCSECMAX"] = "Succes Maximum {$lang['INDEX_SECCURR']}";
$lang["STAFF_CRIME_NEW_SUCITEM"] = "Succes Item";
$lang["STAFF_CRIME_NEW_GROUP"] = "Crime Group";
$lang["STAFF_CRIME_NEW_ITEXT"] = "Initial tekst";
$lang["STAFF_CRIME_NEW_ITEXT_PH"] = "Den tekst, der vises på start forbrydelsen.";
$lang["STAFF_CRIME_NEW_STEXT"] = "Succes tekst";
$lang["STAFF_CRIME_NEW_STEXT_PH"] = "Den tekst, der vises, hvis spilleren lykkes ved at begå forbrydelsen.";
$lang["STAFF_CRIME_NEW_JTEXT"] = "Fejl tekst";
$lang["STAFF_CRIME_NEW_JTEXT_PH"] = "Den tekst, der vises, hvis spilleren undlader forbrydelsen.";
$lang["STAFF_CRIME_NEW_JTIMEMIN"] = "Minimum Dungeon Time";
$lang["STAFF_CRIME_NEW_JTIMEMAX"] = "Maximum Dungeon Time";
$lang["STAFF_CRIME_NEW_JREASON"] = "Dungeon Årsag";
$lang["STAFF_CRIME_NEW_XP"] = "Succes oplevelse";
$lang["STAFF_CRIME_NEW_BTN"] = "Opret kriminalitet";
$lang["STAFF_CRIME_NEW_FAIL1"] = "Du mangler en af ​​de krævede input fra tidligere form.";
$lang["STAFF_CRIME_NEW_FAIL2"] = "Det element, du har valgt synes ikke at eksistere i spillet Vælg et nyt element..";
$lang["STAFF_CRIME_NEW_SUCCESS"] = "Du har nu tilføjet en forbrydelse til spillet.";
$lang["STAFF_CRIMEG_NEW_TITLE"] = "Tilføjelse af en ny Crime Group.";
$lang["STAFF_CRIMEG_NEW_NAME"] = "Crime Group Name";
$lang["STAFF_CRIMEG_NEW_ORDER"] = "Crime Group Order";
$lang["STAFF_CRIMEG_NEW_BTN"] = "Opret Crime Group";
$lang["STAFF_CRIMEG_NEW_FAIL1"] = "Mindst en af ​​de to indgange på tidligere form er tomme Gå tilbage og rette det, tak..";
$lang["STAFF_CRIMEG_NEW_FAIL2"] = "Du kan ikke have kriminelle grupper deler order værdier.";
$lang["STAFF_CRIMEG_NEW_SUCCESS"] = "Du har nu oprettet en forbrydelse gruppe.";

//Staff Users
$lang["STAFF_USERS_EDIT_START"] = "Når du indsender denne formular, vil du være i stand til at redigere alle aspekter af den spiller du vælger.";
$lang["STAFF_USERS_EDIT_USER"] = "Bruger:";
$lang["STAFF_USERS_EDIT_ELSE"] = "Eller, kan du manuelt indtaste en brugers id.";
$lang["STAFF_USERS_EDIT_EMPTY"] = "Du indtastes et ugyldigt bruger Gå tilbage og prøv igen..";
$lang["STAFF_USERS_EDIT_DND"] = "Brugeren du input findes ikke.";
$lang["STAFF_USERS_EDIT_BTN"] = "Rediger bruger";
$lang["STAFF_USERS_DEL_BTN"] = "Slet bruger";
$lang["STAFF_USERS_EDIT_FORMTITLE"] = "Redigering User";
$lang["STAFF_USERS_EDIT_FORM_INFIRM"] = "Infirmary Time";
$lang["STAFF_USERS_EDIT_FORM_INFIRM_REAS"] = "Infirmary Reason";
$lang["STAFF_USERS_EDIT_FORM_DUNG"] = "Dungeon Time";
$lang["STAFF_USERS_EDIT_FORM_DUNG_REAS"] = "Dungeon Årsag";
$lang["STAFF_USERS_EDIT_FORM_ESTATE"] = "Estate";
$lang["STAFF_USERS_EDIT_FORM_STATS"] = "Bruger Stats";
$lang["STAFF_USERS_EDIT_SUB_MISSINGSTUFF"] = "Du mangler nogle nødvendige oplysninger fra forrige side Gå tilbage og prøv igen, tak..";
$lang["STAFF_USERS_EDIT_SUB_ULBAD"] = "Du har angivet et ugyldigt brugerniveau Gå tilbage og prøv igen..";
$lang["STAFF_USERS_EDIT_SUB_UNIU"] = "Den angivne brugernavn er allerede i brug Tilbage og angiv en ny..";
$lang["STAFF_USERS_EDIT_SUB_HBAD"] = "Huset du har angivet, er ugyldig eller ikke-eksisterende Gå tilbage og prøv igen..";
$lang["STAFF_USERS_EDIT_SUB_EIU"] = "Den e-mail input er allerede i brug af en anden konto Tilbage og input en unusued og gyldig e-mail adresse..";
$lang["STAFF_USERS_EDIT_SUB_SUCCESS"] = "Brugerens oplysninger er blevet opdateret.";
$lang["STAFF_USERS_EDIT_SUB_WDNE"] = "En af de våben, du findes ikke, eller kan ikke være udstyret som et våben Gå tilbage og prøv igen..";
$lang["STAFF_USERS_EDIT_SUB_ADNE"] = "Den rustning du angav eksisterer ikke, eller kan ikke være udstyret som rustning Gå tilbage og prøv igen..";
$lang["STAFF_USERS_EDIT_SUB_TDNE"] = "Den by du har valgt eksisterer ikke Gå tilbage og prøv igen..";
$lang["STAFF_USERS_DEL_FORM_1"] = ". Du kan bruge denne formular til at slette en bruger fra spillet Denne handling er ikke reverisble være 100% sikker..";
$lang["STAFF_USERS_DEL_SUB_SECERROR"] = "Du har angivet en ugyldig eller ikke-eksisterende bruger Gå tilbage og prøv igen..";
$lang["STAFF_USERS_DEL_SUBFORM_CONFIRM"] = "Bekræft at du ønsker at slette";
$lang["STAFF_USERS_DEL_SUBFORM_CONFIRM1"] = "Når slettet, vil de ikke være i stand til at logge ind fra deres konto længere..";
$lang["STAFF_USERS_DEL_SUB_INVALID"] = "Du har angivet en bruger eller kommando, der er ugyldigt.";
$lang["STAFF_USERS_DEL_SUB_FAIL"] = "Brugeren blev ikke slettet.";
$lang["STAFF_USERS_DEL_SUB_SUCC"] = "Bruger blev slettet fra spillet.";
$lang["STAFF_USERS_FL_SUB_SUCC"] = "Brugeren blev logget ud fra spillet.";
$lang["STAFF_USERS_FL_FORM_INFO"] = "Brug denne formular til at have en anvendelse automatisk logget ud, når på deres næste handling i spillet.";
$lang["STAFF_USERS_FL_FORM_BTN"] = "Kraft Logud Bruger";

//Academy
$lang["STAFF_ACADEMY_ADD"] = "Opret Academic Course";
$lang["STAFF_ACADEMY_DEL"] = "Fjern Academic Course";
$lang["STAFF_ACADEMY_NAME"] = "Academy navn";
$lang["STAFF_ACADEMY_DESC"] = "Academy Beskrivelse";
$lang["STAFF_ACADEMY_COST"] = "Academy Cost";
$lang["STAFF_ACADEMY_LVL"] = "Academy Minimum Level";
$lang["STAFF_ACADEMY_DAYS"] = "Academy dage";
$lang["STAFF_ACADEMY_PERKS"] = "Academy Perks";
$lang["STAFF_ACADEMY_PERK"] = "Perk";
$lang["STAFF_ACADEMY_TOGGLE_DISP"] = "Giv Perk?";
$lang["STAFF_ACADEMY_TOGGLE_ON"] = "Yes!";
$lang["STAFF_ACADEMY_TOGGLE_OFF"] = "Nej!";
$lang["STAFF_ACADEMY_STAT"] = "I betragtning af Effect";
$lang["STAFF_ACADEMY_OPTION_1"] = "Strength";
$lang["STAFF_ACADEMY_OPTION_2"] = "Agility";
$lang["STAFF_ACADEMY_OPTION_3"] = "Guard";
$lang["STAFF_ACADEMY_OPTION_4"] = "Labor";
$lang["STAFF_ACADEMY_OPTION_5"] = "IQ";
$lang["STAFF_ACADEMY_DIRECTION"] = "Retning";
$lang["STAFF_ACADEMY_INCREASE"] = "Forøg";
$lang["STAFF_ACADEMY_DECREASE"] = "Formindsk";
$lang["STAFF_ACADEMY_AMOUNT"] = "given mængde";
$lang["STAFF_ACADEMY_VALUE"] = "værdi";
$lang["STAFF_ACADEMY_PERCENT"] = "Procentdel";
$lang["STAFF_ACADEMY_CREATE"] = "Opret Academy";
$lang["STAFF_ACADEMY_DELETE_HEADER"] = "Sletning af en Academy";
$lang["STAFF_ACADEMY_DELETE_NOTICE"] = "Akademiet, du vælger, vil blive slettet permanent Der er ikke en bekræftelse prompt, så være 100% sikker..";
$lang["STAFF_ACADEMY_DELETE_TITLE"] = "Academy";
$lang["STAFF_ACADEMY_DELETE_BUTTON"] = "Fjern Academy";
$lang["ACADEMY_DESCRIPTION_EFFECT_1"] = "Dette kursus";
$lang["ACADEMY_DESCRIPTION_EFFECT_2"] = "din";
$lang["ACADEMY_DESCRIPTION_EFFECT_3"] = "med";
$lang["ACADEMY_INFO_NAME"] = "Kursus navn:";
$lang["ACADEMY_INFO_DESC"] = "Kursus Beskrivelse:";
$lang["ACADEMY_INFO_COST"] = "Minimum Cost:";
$lang["ACADEMY_INFO_LEVEL"] = "Minimum krævede niveau:";
$lang["ACADEMY_INFO_DAYS"] = "dage til at fuldføre:";
$lang["ACADEMY_INFO_EFFECT"] = "Afslutning Effect #";
$lang["ACADEMY_STARTED_COURSE"] = "Kursus Succesfuld gang!";
$lang["ACADEMY_RETURN_HOME"] = "Vend hjem";
$lang["ACADEMY_LOW_LEVEL_1"] = "Low Level!";
$lang["ACADEMY_INSUFFICIENT_CURRENCY_1"] = "Kort på Valuta!";
$lang["ACADEMY_IN_COURSE_1"] = "I kursus";
$lang["ACADEMY_LOW_LEVEL_2"] = "Prøv at få flere niveauer, før du forsøger dette kursus";
$lang["ACADEMY_INSUFFICIENT_CURRENCY_2"] = "Prøv at få nogle mere primære valuta før han sluttede dette kursus";
$lang["ACADEMY_IN_COURSE_2"] = "Du er allerede i et kursus Vent til den er færdig, og prøv igen <br> Den slutter i:!";
$lang["ACADEMY_IN_COURSE_3"] = "dage";

//Criminal Center
$lang["CRIME_TITLE"] = "Criminal Center";
$lang["CRIME_ERROR_JI"] = "Kun sunde og frie individer kan begå forbrydelser.";
$lang["CRIME_TABLE_CRIME"] = "Crime";
$lang["CRIME_TABLE_CRIMES"] = "Forbrydelser";
$lang["CRIME_TABLE_COST"] = "Cost";
$lang["CRIME_TABLE_COMMIT"] = "Commit";
$lang["CRIME_COMMIT_INVALID"] = "Du forsøger at begå enten en ikke-eksisterende forbrydelse, eller en ufærdig én Prøv igen, og hvis problemet fortsætter, skal du kontakte en admin..";
$lang["CRIME_COMMIT_BRAVEBAD"] = "Du er ikke modig nok til at begå denne forbrydelse på denne tid Kom tilbage senere..";

$lang["ATTACK_START_NOREFRESH"] = "Forfriskende mens angribe en bannable forseelse Du kan miste alle dine erfaringer for det..";
$lang["ATTACK_START_NOUSER"] = "Du kan kun angribe spillere specificeret Brugte du angrebet link på brugerens profil.?";
$lang["ATTACK_START_NOTYOU"] = "Deprimeret eller ej, du kan ikke angribe dig selv!";
$lang["ATTACK_START_THEYLOWLEVEL"] = "Du kan ikke angribe spillere under niveau 2, som også er online.";
$lang["ATTACK_START_YOUNOHP"] = "Du skal HP til at kæmpe nogen Kom tilbage når du har mere sundhed.";
$lang["ATTACK_START_YOUINFIRM"] = "Hvordan forventer du at kæmpe nogen, når du ammer en injurt i sygeafdelingen?";
$lang["ATTACK_START_YOUDUNG"] = "Hvordan forventer du at kæmpe nogen, når du serverer din gæld til samfundet i fangehullet?";
$lang["ATTACK_START_YOUCHICKEN"] = "Chickeing ud fra den ene kamp, ​​og kører for at starte en anden er ikke en ærefuld måde at spille.";
$lang["ATTACK_START_NONUSER"] = "Den person, du har en nag med ikke eksisterer Tjek din kilde, og prøv igen..";
$lang["ATTACK_START_UNKNOWNERROR"] = "En ukendt fejl er opstået Gå tilbage og prøv igen Hvis denne fejl fortsætter, skal du kontakte en admin..!";
$lang["ATTACK_START_OPPNOHP"] = "er lav på HP Kom tilbage, når de har mere sundhed..";
$lang["ATTACK_START_OPPINFIRM"] = "er i sygeafdelingen i øjeblikket Kom tilbage, når de er ude.";
$lang["ATTACK_START_OPPDUNG"] = "er i fangehullet i det øjeblik Kom tilbage, når de er ude.";
$lang["ATTACK_START_OPPUNATTACK"] = "Denne bruger kan ikke angribes af normal vis.";
$lang["ATTACK_START_YOUUNATTACK"] = "En magisk kraft forhindrer dig i at angribe nogen.";
$lang["ATTACK_FIGHT_STALEMATE"] = ". Kom tilbage, når du er stærkere Denne kamp ender i dødvande.";
$lang["ATTACK_FIGHT_LOWENG1"] = "Du har ikke nok energi til denne kamp Du skal mindst.";
$lang["ATTACK_FIGHT_LOWENG2"] = "% Du behøver kun.";
$lang["ATTACK_FIGHT_BUGABUSE"] = "Misbruger spil bugs er imod spillets regler du mister din oplevelse og gå til sygeafdelingen for denne ene..";
$lang["ATTACK_FIGHT_BADWEAP"] = "Våbnet du forsøger at angribe med findes ikke eller kan ikke bruges som et våben.";
$lang["ATTACK_FIGHT_ATTACKY_HIT1"] = "Brug";
$lang["ATTACK_FIGHT_ATTACKY_HIT2"] = "du rammer";
$lang["ATTACK_FIGHT_ATTACKY_HIT3"] = "doing";
$lang["ATTACK_FIGHT_ATTACKY_HIT4"] = "skade.";
$lang["ATTACK_FIGHT_ATTACKY_MISS1"] = "Du forsøgte at ramme";
$lang["ATTACK_FIGHT_ATTACKY_MISS2"] = "men missede.";
$lang["ATTACK_FIGHT_ATTACKY_WIN1"] = "Du har bested";
$lang["ATTACK_FIGHT_ATTACKY_WIN2"] = "i kamp Hvad ønsker du at gøre med dem nu.?";
$lang["ATTACK_FIGHT_OUTCOME1"] = "Mug";
$lang["ATTACK_FIGHT_OUTCOME2"] = "Beat";
$lang["ATTACK_FIGHT_OUTCOME3"] = "Lad";
$lang["ATTACK_FIGHT_ATTACK_HPREMAIN"] = "HP Resterende";
$lang["ATTACK_FIGHT_ATTACK_FISTS"] = "Fists";
$lang["ATTACK_FIGHT_ATTACKO_HIT1"] = "Brug deres";
$lang["ATTACK_FIGHT_ATTACKO_HIT2"] = "hit laver";
$lang["ATTACK_FIGHT_ATTACKO_MISS"] = "forsøgte at ramme dig, men, savnet.";
$lang["ATTACK_FIGHT_FINAL_GUILD"] = "er i samme guild, som du Du kan ikke angribe dine medmennesker guild hjælpere!";
$lang["ATTACK_FIGHT_FINAL_CITY"] = "Denne spiller er ikke i samme by, som du du både skal være i den samme by for at bekæmpe hinanden..";
$lang["ATTACK_FIGHT_START1"] = "Vælg et våben til at angribe med.";
$lang["ATTACK_FIGHT_START2"] = "Du behøver ikke et våben til at angribe med Du kan gå tilbage!";
$lang["ATTACK_FIGHT_END"] = "Du har bested";
$lang["ATTACK_FIGHT_END1"] = "Du bested dem i kamp!";
$lang["ATTACK_FIGHT_END2"] = "En ond tanke kommer i dit sind, som du stirre på deres ubevidste krop Du bryde deres hals, og sparke dem indtil de begynder blødning..";
$lang["ATTACK_FIGHT_END3"] = "Dine handlinger forårsage";
$lang["ATTACK_FIGHT_END4"] = "af infirmary tid.";
$lang["ATTACK_FIGHT_END5"] = "Du faldt til";
$lang["ATTACK_FIGHT_END6"] = "Du tabte denne kamp og tabte noget af din erfaring som en kriger!";
$lang["ATTACK_FIGHT_END7"] = ".. Da du er en ærefuld kriger, du tager dem til infirmeriet indgangen Du forlader deres krop der Dette øger din oplevelse.";
$lang["ATTACK_FIGHT_END8"] = "At være en grådig kriger, du tager et kig på deres lommer og få fat i nogle af deres primære valuta.";

//Item Info Page
$lang["ITEM_INFO_LUIF"] = "Visning post information til";
$lang["ITEM_INFO_TYPE"] = "Type";
$lang["ITEM_INFO_SPRICE"] = "Sell pris";
$lang["ITEM_INFO_BPRICE_NO"] = "Vare kan ikke købes i spillet.";
$lang["ITEM_INFO_SPRICE_NO"] = "Vare kan ikke sælges i spillet.";
$lang["ITEM_INFO_BPRICE"] = "Køb pris";
$lang["ITEM_INFO_WEAPON_HURT"] = "Weapon Rating";
$lang["ITEM_INFO_ARMOR_HURT"] = "Armor Rating";
$lang["ITEM_INFO_INFO"] = "Info";
$lang["ITEM_INFO_ITEM"] = "Item";
$lang["ITEM_INFO_EFFECT"] = "Effekt #";
$lang["ITEM_INFO_BY"] = "med";

//Item sell
$lang["ITEM_SELL_INFO"] = "Item Salg";
$lang["ITEM_SELL_FORM1"] = "Du forsøger at sælge";
$lang["ITEM_SELL_FORM2"] = "tilbage til spillet Enter hvor mange du ønsker at sælge tilbage Du har..";
$lang["ITEM_SELL_FORM3"] = "for at sælge.";
$lang["ITEM_SELL_SUCCESS1"] = "Du har med succes solgt";
$lang["ITEM_SELL_SUCCESS2"] = "(er) for";
$lang["ITEM_SELL_BTN"] = "Sælg elementer";
$lang["ITEM_SELL_ERROR1_TITLE"] = "manglende elementer!";
$lang["ITEM_SELL_BAD_QTY"] = "Du forsøger at sælge flere varer end du i øjeblikket har på lager Kontroller din indtastning, og prøv igen.";
$lang["ITEM_SELL_ERROR1"] = "Du forsøger at sælge en vare, som du ikke har, eller bare ikke findes Tjek din kilde, og prøv igen..";

//Staff jobs
$lang["STAFF_JOB_CREATE_TITLE"] = "Opret et job";
$lang["STAFF_JOB_CREATE_FORM_NAME"] = "Job Name";
$lang["STAFF_JOB_CREATE_FORM_DESC"] = "Jobbeskrivelse";
$lang["STAFF_JOB_CREATE_FORM_BOSS"] = "Job Manager";
$lang["STAFF_JOB_CREATE_FORM_FIRST"] = "første job Rank";
$lang["STAFF_JOB_CREATE_FORM_RNAME"] = "Rank navn";
$lang["STAFF_JOB_CREATE_FORM_PAYS"] = "Daily Payment";
$lang["STAFF_JOB_CREATE_FORM_ACT"] = "Påkrævet aktivitet";

//Staff logs
$lang["STAFF_LOGS_USERS_FORM"] = "Vælg den bruger, hvis logfiler du ønsker at se.";
$lang["STAFF_LOGS_USERS_FORM_BTN"] = "Vis Logs";

//Shops
$lang["SHOPS_HOME_INTRO"] = "Du bliver leder om byen og du kan se et par butikker.";
$lang["SHOPS_HOME_OH"] = "Dette byen sikker er ikke udviklet langt nok til at have butikker, hva?";
$lang["SHOPS_HOME_TH_1"] = "Shop navn";
$lang["SHOPS_HOME_TH_2"] = "Shop s Beskrivelse";
$lang["SHOPS_SHOP_TH_1"] = "Item Name";
$lang["SHOPS_SHOP_TH_2"] = "Pris";
$lang["SHOPS_SHOP_TH_3"] = "Køb";
$lang["SHOPS_SHOP_TD_1"] = "Antal:";
$lang["SHOPS_SHOP_INFO"] = "Du begynder browsing punkterne på";
$lang["SHOPS_BUY_ERROR1"] = "Du forsøger at bruge denne fil forkert Vær sikker på at du har angivet både et element til at købe, sammen med en mængde..";
$lang["SHOPS_BUY_ERROR2"] = "ythe emne, du forsøger at købe ikke eksisterer, ikke sælges i denne butik eller bare ikke eksisterer!";
$lang["SHOPS_SHOP_ERROR1"] = "Du forsøger at få adgang til en butik i en anden by, end du er i øjeblikket i!";
$lang["SHOPS_SHOP_ERROR2"] = "Du forsøger at få adgang til en butik, der er ugyldig eller findes ikke Kontroller din kilde, og prøv igen.";
$lang["SHOPS_BUY_ERROR3"] = "Du har ikke nok Primær Valuta at købe";
$lang["SHOPS_BUY_ERROR4"] = "Den genstand, du forsøger at købe er ikke purchaseable via normale midler.";
$lang["SHOPS_BUY_SUCCESS"] = "Du har købt";
$lang["SHOPS_BUY_ERROR5"] = "Du kan ikke købe varer fra butikker uden for byen, du er i øjeblikket i Tjek din kilde, og prøv igen..";

//Staff shops
$lang["STAFF_SHOP_FORM_TITLE"] = "Brug denne formular til at oprette en ny butik.";
$lang["STAFF_SHOP_FORM_OPTION1"] = "Shop navn";
$lang["STAFF_SHOP_FORM_OPTION2"] = "Shop s Beskrivelse";
$lang["STAFF_SHOP_FORM_OPTION3"] = "Shop Placering";
$lang["STAFF_SHOP_FORM_BTN"] = "Opret Shop";
$lang["STAFF_SHOP_SUB_ERROR1"] = "Shop navn eller beskrivelse er tom Gå tilbage og prøv igen..";
$lang["STAFF_SHOP_SUB_ERROR2"] = "Den placering, du valgte til butikken at holde findes ikke.";
$lang["STAFF_SHOP_SUB_ERROR3"] = "En butik med det angivne navn findes allerede!";
$lang["STAFF_SHOP_SUB_SUCCESS"] = "Shop blev oprettet.";
$lang["STAFF_SHOP_DELFORM_TITLE"] = "Sletning af en butik fra spillet vil fjerne det fra spillet Sørg af denne aktion, da der ikke er nogen bekræftelse..";
$lang["STAFF_SHOP_DELFORM_FORM"] = "Shop:";
$lang["STAFF_SHOP_DELFORM_FORM_BTN"] = "Slet Shop";
$lang["STAFF_SHOP_DELFORM_SUB_ERROR1"] = "Butikken er ugyldig eller findes ikke Måske du slettet den tidligere.?";
$lang["STAFF_SHOP_DELFORM_SUB_SUCCESS"] = "Shop er blevet fjernet fra spillet.";
$lang["STAFF_SHOP_IADDFORM_TITLE"] = "Brug denne formular til at tilføje et element til en butik.";
$lang["STAFF_SHOP_IADDFORM_TD1"] = "Emne:";
$lang["STAFF_SHOP_IADDFORM_BTN"] = "Tilføj element til Shop";
$lang["STAFF_SHOP_IADDSUB_ERROR"] = "du forsøger at tilføje et element til en ugyldig butik, eller et ugyldigt element til en butik Gå tilbage og prøv igen..";
$lang["STAFF_SHOP_IADDSUB_ERROR2"] = "Item eller butik er ugyldig eller findes ikke.";
$lang["STAFF_SHOP_IADDSUB_ERROR3"] = ". Den genstand, du forsøger at tilføje til denne butik er allerede opført i denne butik Det giver ingen mening at liste den samme vare to gange.";
$lang["STAFF_SHOP_IADDSUB_SUCCESS"] = "Item er blevet tilføjet til bestanden af ​​denne butik.";

//Item Market
$lang["IMARKET_TITLE"] = "Punkt marked";
$lang["IMARKET_LISTING_TH1"] = "Listing Owner";
$lang["IMARKET_LISTING_TH2"] = "Item x Mængde";
$lang["IMARKET_LISTING_TH3"] = "Pris / Item";
$lang["IMARKET_LISTING_TH4"] = "Total pris";
$lang["IMARKET_LISTING_TH5"] = "Links";
$lang["IMARKET_LISTING_TD1"] = "Fjern Listing";
$lang["IMARKET_LISTING_TD2"] = "Køb Listing";
$lang["IMARKET_LISTING_TD3"] = "Gave Listing";
$lang["IMARKET_REMOVE_ERROR1"] = "Du skal angive et element marked notering du ønsker at effekt.";
$lang["IMARKET_REMOVE_ERROR2"] = "Posten marked notering du ønsker at fjerne ikke eksisterer, eller du ikke er dens ejer.";
$lang["IMARKET_REMOVE_SUCCESS"] = "Posten markedet notering er blevet fjernet.";
$lang["IMARKET_BUY_ERROR1"] = "Posten marked notering du vil købe ikke eksisterer, eller er blevet opkøbt allerede.";
$lang["IMARKET_BUY_START"] = "Indtast hvor mange";
$lang["IMARKET_BUY_START1"] = "(r) du ønsker at købe der er i øjeblikket.";
$lang["IMARKET_BUY_START2"] = "købes.";
$lang["IMARKET_BUY_SUB_ERROR1"] = "Du kan ikke købe dine egne emner fra elementet markedet.";
$lang["IMARKET_BUY_SUB_ERROR2"] = "Du har ikke nok penge til at købe denne liste.";
$lang["IMARKET_BUY_SUB_ERROR3"] = "Du kan ikke købe mere end den mængde, der blev opført.";
$lang["IMARKET_BUY_SUB_SUCCESS"] = "vare (r) er blevet købt Tjek din beholdning!";
$lang["IMARKET_GIFT_START1"] = "(r) du ønsker at købe og sende som en gave der er i øjeblikket.";
$lang["IMARKET_GIFT_FORM_TH1"] = "Send gave Til:";
$lang["IMARKET_GIFT_SUB_ERROR1"] = "Du forsøger at sende en gave til en bruger, der ikke eksisterer!";
$lang["IMARKET_GIFT_SUB_ERROR2"] = "Du kan ikke købe en vare fra markedet og gave det tilbage til den person, der er angivet det.";
$lang["IMARKET_GIFT_SUB_SUCCESS"] = "Du har købt varen og sendt det ud som en gave!";
$lang["IMARKET_ADD_TITLE"] = "Udfyld formularen ud for at tilføje et element til markedet.";
$lang["IMARKET_ADD_TH1"] = "Valuta Type";
$lang["IMARKET_ADD_TH2"] = "Pris pr Item";
$lang["IMARKET_ADD_BTN"] = "Tilføj til Market";
$lang["IMARKET_ADD_ERROR1"] = "Du kan ikke tilføje ingen varer til punktet markedet.";
$lang["IMARKET_ADD_ERROR2"] = "Du forsøger at tilføje en vare, du ikke ejer.";
$lang["IMARKET_ADD_ERROR3"] = "Du har ikke nok af denne post for at tilføje den mængde, du ønskede at på markedet.";
$lang["IMARKET_ADD_SUB_SUCCESS"] = "Du har noteret denne vare på elementet markedet.";

//Travel
$lang["TRAVEL_TITLE"] = "Travel Horse";
$lang["TRAVEL_TABLE"] = "Velkommen til hestestald. Du kan rejse til andre byer her, men til en pris. Hvor vil du gerne rejse i dag? Bemærk, at når du komme videre i spillet, vil flere steder være . til rådighed for dig Det vil koste dig ";
$lang["TRAVEL_TABLE2"] = "{$lang['INDEX_PRIMCURR']} til at rejse i dag.";
$lang["TRAVEL_TABLE_HEADER"] = "Town navn";
$lang["TRAVEL_TABLE_LEVEL"] = "Minimum Level";
$lang["TRAVEL_TABLE_GUILD"] = "Guild";
$lang["TRAVEL_TABLE_TAX"] = "indkomstskat";
$lang["TRAVEL_TABLE_TRAVEL"] = "Travel";
$lang["TRAVEL_ERROR_CASHLOW"] = "Du har ikke enoguh primær currecy at rejse til dette sted Gå tilbage og prøv igen..";
$lang["TRAVEL_ERROR_ALREADYTHERE"] = "Du er allerede i denne by Hvorfor ville du ønsker at spilde dine penge og rejse til her igen!?";
$lang["TRAVEL_ERROR_ERRORGEN"] = "Denne by eksisterer ikke, eller dit niveau er ikke højt nok til at besøge denne by Gå tilbage og prøv igen..";
$lang["TRAVEL_SUCCESS"] = "Du har købt en hest og rejste til";

//Staff towns
$lang["STAFF_TRAVEL_ADD"] = "Tilføj en by";
$lang["STAFF_TRAVEL_EDIT"] = "Rediger en by";
$lang["STAFF_TRAVEL_DEL"] = "Slet en by";
$lang["STAFF_TRAVEL_ADDTOWN_TABLE"] = "Brug denne formular til at tilføje en by i spillet.";
$lang["STAFF_TRAVEL_ADDTOWN_TH1"] = "Town navn";
$lang["STAFF_TRAVEL_ADDTOWN_TH2"] = "Minimum Level";
$lang["STAFF_TRAVEL_ADDTOWN_TH3"] = "Skat Level";
$lang["STAFF_TRAVEL_ADDTOWN_BTN"] = "Opret by";
$lang["STAFF_TRAVEL_ADDTOWN_SUB_ERROR1"] = "Du cnanot navngive en ny by efter en by, som allerede findes.";
$lang["STAFF_TRAVEL_ADDTOWN_SUB_ERROR2"] = "Byens skatteprocent skal være mellem 0% og 20%";
$lang["STAFF_TRAVEL_ADDTOWN_SUB_ERROR3"] = "Byens minimumsniveau krav om MUS være større end 0.";
$lang["STAFF_TRAVEL_ADDTOWN_SUB_SUCCESS"] = "Du har tilføjet denne by i spillet.";
$lang["STAFF_TRAVEL_DELTOWN_TABLE"] = "Brug denne formular til at slette en by fra spillet.";
$lang["STAFF_TRAVEL_DELTOWN_TH1"] = "by";
$lang["STAFF_TRAVEL_DELTOWN_BTN"] = "Slet by";
$lang["STAFF_TRAVEL_DELTOWN_SUB_ERROR1"] = "Du kan ikke slette en ikke-eksisterende by.";
$lang["STAFF_TRAVEL_DELTOWN_SUB_ERROR2"] = "Du kan ikke slette den første by.";
$lang["STAFF_TRAVEL_DELTOWN_SUB_SUCCESS"] = "Town er blevet slettet Brugere og butikker i denne by er blevet flyttet til starteren byen..";

//Guild Listing
$lang["GUILD_LIST"] = "Guild notering";
$lang["GUILD_LIST_TABLE1"] = "Guild navn";
$lang["GUILD_LIST_TABLE2"] = "Guild Level";
$lang["GUILD_LIST_TABLE3"] = "medlem Count";
$lang["GUILD_LIST_TABLE5"] = "Guild Leader";
$lang["GUILD_LIST_TABLE4"] = "Hometown";

//Guild create
$lang["GUILD_CREATE"] = "Opret en Guild";
$lang["GUILD_CREATE_ERROR"] = "Du har ikke nok Primær Valuta at købe købe en guild Du skal som minimum.";
$lang["GUILD_CREATE_ERROR1"] = "Du er ikke en høj nok niveau til at købe en guild Du skal være på minimum.";
$lang["GUILD_CREATE_ERROR2"] = "Du kan ikke oprette en guild, mens du er i øjeblikket medlem af en.";
$lang["GUILD_CREATE_ERROR3"] = "Du kan ikke oprette en guild opkaldt efter en allerede eksisterende orden.";
$lang["GUILD_CREATE_FORM"] = "Udfyld denne formular ud til at oprette din orden din orden hjemby vil blive sat til den by, du er i øjeblikket placeret i..";
$lang["GUILD_CREATE_FORM1"] = "Guild navn";
$lang["GUILD_CREATE_FORM2"] = "Guild Beskrivelse";
$lang["GUILD_CREATE_BTN"] = "Opret Guild for";
$lang["GUILD_CREATE_SUCCESS"] = "Du har nu oprettet en guild!";

//Guild Viewing
$lang["GUILD_VIEW_GUILD"] = "Guild";
$lang["GUILD_VIEW_ERROR"] = "Du forsøger at se en ikke-eksisterende guild Tjek din kilde, og prøv igen..";
$lang["GUILD_VIEW_LEADER"] = "Guild Leader";
$lang["GUILD_VIEW_COLEADER"] = "Guild Co-Leader";
$lang["GUILD_VIEW_LEVEL"] = "Guild Level";
$lang["GUILD_VIEW_MEMBERS"] = "Guild medlemmer";
$lang["GUILD_VIEW_LOCATION"] = "Guild Location";
$lang["GUILD_VIEW_USERS"] = "Guild Medlemsliste";
$lang["GUILD_VIEW_APPLY"] = "Anvend på Guild";
$lang["GUILD_VIEW_LIST"] = "Tilmeldte til";
$lang["GUILD_VIEW_LIST2"] = "guild";
$lang["GUILD_VIEW_ERROR"] = "Du skal angive en guild du ønsker at se Tjek din kilde, og prøv igen..";
$lang["GUILD_APP_TITLE"] = "Udfylde Application til at slutte";
$lang["GUILD_APP_INFO"] = ". Indtast en grund bør du være i denne guild Vær høflig, ærlig og præcis med dine oplysninger.";
$lang["GUILD_APP_ERROR"] = "Du kan ikke sende en ansøgning til et guild whilist du i øjeblikket i en Lad din nuværende guild, og prøv igen..";
$lang["GUILD_APP_BTN"] = "Send ansøgning";
$lang["GUILD_APP_ERROR1"] = "Du har allerede sendt en ansøgning at deltage i denne guild Vent til du får et svar, før du sender i en anden..";
$lang["GUILD_APP_SUCC"] = "Du har sendt i din ansøgning at deltage i denne guild!";
$lang["GUILD_VIEW_DESC"] = "guild s Beskrivelse";

//Staff rules
$lang["STAFF_RULES_ADD_FORM"] = "Brug denne formular til at tilføje regler i spillet Vær klar og kortfattet Jo mere svært sprog og terminologi, du bruger, kan færre mennesker forstår...";
$lang["STAFF_RULES_ADD_BTN"] = "Tilføj regel";
$lang["STAFF_RULES_ADD_SUBFAIL"] = "Du kan ikke tilføje en regel en tom regel.";
$lang["STAFF_RULES_ADD_SUBSUCC"] = "Du har nu oprettet en ny regel.";

//Game rules
$lang["GAMERULES_TITLE"] = "Regler";
$lang["GAMERULES_TEXT"] = "Du forventes at følge disse regler. Du forventes også at tjekke tilbage på disse temmelig ofte, da disse regler kan ændres uden varsel. Personalet vil ikke acceptere uvidenhed som en undskyldning, hvis du bryder en af disse regler. ";

//View GUILD_APP_BTN
$lang["VIEWGUILD_ERROR1"] = "Du er ikke i en guild, så du ikke kan se din orden oplysninger.";
$lang["VIEWGUILD_ERROR2"] = "Det ligner din orden er blevet slettet Check med personale til at opdatere din konto..";
$lang["VIEWGUILD_TITLE"] = "Din Guild,";
$lang["VIEWGUILD_HOME_SUMMARY"] = "Guild Summary";
$lang["VIEWGUILD_HOME_DONATE"] = "Doner til Guild";
$lang["VIEWGUILD_HOME_CRIME"] = "Guild forbrydelser";
$lang["VIEWGUILD_HOME_USERS"] = "Guild Tilmeldte";
$lang["VIEWGUILD_HOME_LEAVE"] = "Forlad Guild";
$lang["VIEWGUILD_HOME_ATKLOG"] = "Guild Attack Logs";
$lang["VIEWGUILD_HOME_ARMORY"] = "Guild Armory";
$lang["VIEWGUILD_HOME_STAFF"] = "Guild Staff Room";
$lang["VIEWGUILD_HOME_ANNOUNCE"] = "Guild Announcement";
$lang["VIEWGUILD_HOME_EVENT"] = "Sidste 10 Guild Events";
$lang["VIEWGUILD_HOME_EVENTTEXT"] = "Event tekst";
$lang["VIEWGUILD_HOME_EVENTTIME"] = "Event Time";
$lang["VIEWGUILD_SUMMARY_TITLE"] = "Guild Summary";
$lang["VIEWGUILD_SUMMARY_OWNER"] = "Guild Leader";
$lang["VIEWGUILD_SUMMARY_COOWNER"] = "Guild Co-Leader";
$lang["VIEWGUILD_SUMMARY_MEM"] = "medlemmer / max kapacitet";
$lang["VIEWGUILD_SUMMARY_LVL"] = "Guild Level";
$lang["VIEWGUILD_NA"] = "N / A";
$lang["VIEWGUILD_DONATE_TITLE"] = "Indtast den mængde valuta, du ønsker at donere til din orden Du har i øjeblikket.";
$lang["VIEWGUILD_DONATE_BTN"] = "Doner til Guild";
$lang["VIEWGUILD_DONATE_ERR1"] = "Du skal udfylde tidligere form til at donere.";
$lang["VIEWGUILD_DONATE_ERR2"] = "Du kan ikke donere mere primær valuta end du har i øjeblikket.";
$lang["VIEWGUILD_DONATE_ERR3"] = "Du kan ikke donere mere sekundær valuta end du har i øjeblikket.";
$lang["VIEWGUILD_DONATE_SUCC"] = "Du har med succes doneret de angivne mængder i din orden.";
$lang["VIEWGUILD_MEMBERS_TH1"] = "Bruger";
$lang["VIEWGUILD_MEMBERS_TH2"] = "Level";
$lang["VIEWGUILD_MEMBERS_BTN"] = "Kick";
$lang["VIEWGUILD_IDX"] = "Guild Index";
$lang["VIEWGUILD_KICK_SUCCESSS"] = "Du har med succes sparkede denne bruger fra lauget.";
$lang["VIEWGUILD_KICK_ERR"] = "Undskyld, men du kan ikke sparke din orden leder Hvis din leder er inaktiv, kontakt personale, så du kan tage deres plads..";
$lang["VIEWGUILD_KICK_ERR1"] = "Du kan ikke sparke dig selv fra lauget Hvis du ønsker at forlade, overføre dine beføjelser til en anden, så lad..";
$lang["VIEWGUILD_KICK_ERR2"] = "du prøver at sparke en bruger, der ikke er i din guild eller findes ikke.";
$lang["VIEWGUILD_KICK_ERR3"] = "Du har ikke tilladelse til at sparke brugere fra denne orden.";
$lang["VIEWGUILD_LEAVE_ERR"] = "Du kan ikke forlade, mens du er ejer / medejer af din orden Overfør dine rettigheder til et andet medlem i lauget, og prøv igen..";
$lang["VIEWGUILD_LEAVE_SUCC"] = "Du har forladt din orden.";
$lang["VIEWGUILD_LEAVE_SUCC1"] = "Du har besluttet at blive i din orden for nu.";
$lang["VIEWGUILD_LEAVE_INFO"] = "Er du 100% sikker på at du ønsker at forlade din orden Du bliver nødt til at genanvende, hvis du forlader og ønsker at komme tilbage?";
$lang["VIEWGUILD_LEAVE_BTN"] = "Ja, efterlade!";
$lang["VIEWGUILD_LEAVE_BTN1"] = "Nej, vent, ophold!";
$lang["VIEWGUILD_ATKLOGS_INFO"] = "Denne tabel viser de sidste 50 udgående angreb fra din orden.";
$lang["VIEWGUILD_ATKLOGS_TD1"] = "Time";
$lang["VIEWGUILD_ATKLOGS_TD2"] = "Attack info";
$lang["VIEWGUILD_STAFF_ERROR"] = "Kun den leder og co-leder af din orden kan se dette område.";
$lang["VIEWGUILD_STAFF_IDX_APP"] = "Application Management";
$lang["VIEWGUILD_STAFF_APP_TH0"] = "Filing Time";
$lang["VIEWGUILD_STAFF_APP_TH1"] = "Ansøger";
$lang["VIEWGUILD_STAFF_APP_TH2"] = "Level";
$lang["VIEWGUILD_STAFF_APP_TH3"] = "Application tekst";
$lang["VIEWGUILD_STAFF_APP_TH4"] = "Handlinger";
$lang["VIEWGUILD_STAFF_APP_BTN"] = "Accepter";
$lang["VIEWGUILD_STAFF_APP_BTN1"] = "Decline";
$lang["VIEWGUILD_STAFF_APP_DENY_TEXT"] = "Du har afvist denne ansøgning.";
$lang["VIEWGUILD_STAFF_APP_ACC_ERR"] = "Din guild har ikke kapacitet til at acceptere denne medlem Level din orden op for at få mere kapacitet..";
$lang["VIEWGUILD_STAFF_APP_ACC_ERR1"] = "Denne spiller er allerede i en guild.";
$lang["VIEWGUILD_STAFF_APP_ACC_SUCC"] = "Du har accepteret denne brugers ansøgning!";
$lang["VIEWGUILD_STAFF_APP_WOT"] = "Vi ved ikke, hvordan du kom her ... men ja ... du kinda ikke formodes at være her.";

//Hire Spy
$lang["SPY_ERROR1"] = "Du skal angive en bruger, du ønsker at udspionere!";
$lang["SPY_ERROR2"] = "Der er ingen grund til at spionere på dig selv.";
$lang["SPY_ERROR3"] = "Brugeren, du forsøger at udspionere findes ikke.";
$lang["SPY_ERROR4"] = "Du har ikke nok {$lang['INDEX_PRIMCURR']} at udspionere denne bruger!";
$lang["SPY_ERROR5"] = "Du kan ikke spionere på andre spillere, når du er i fangehullet!";
$lang["SPY_ERROR6"] = "Du kan ikke spionere på andre spillere, når du er i infirmeriet, forsøger at føle sig bedre.";
$lang["SPY_START"] = "Du forsøger at sende en spion til at indsamle oplysninger om";
$lang["SPY_START1"] = ". Dette vil koste dig 500 {$lang['INDEX_PRIMCURR']} ganget med deres niveau (.";
$lang["SPY_START2"] = "{$lang['INDEX_PRIMCURR']} i dette tilfælde.) Husk, at succes er ikke garanteret. Hvis du ønsker at påtage sig risikoen ved at trykke på knappen for at sende en spion! ";
$lang["SPY_BTN"] = "Send Spy";
$lang["SPY_FAIL1"] = "Du forsøger at få oplysninger om dit mål. Oh shoot! De spotte dig! Køre, køre, køre! Så hurtigt som du kan. Jeg tror ikke, de så dig. Du fik heldig denne tid, opløbet. ";
$lang["SPY_FAIL2"] = ".!!! Du forsøger at få oplysninger om dit mål Oh skyde de øje på dig Run De kan positivt ID dig, så de ved nu, som prøvede at udspionere dem.";
$lang["SPY_FAIL3"] = "Du forsøger at få oplysninger om dit mål Du følger dem nøje, næsten stalkerish ligesom en vagt bekendtgørelser denne og slag dig i ansigtet Du vågner op i et fangehul celle....";
$lang["SPY_SUCCESS"] = "Omkring";
$lang["SPY_SUCCESS1"] = "{$lang['INDEX_PRIMCURR']} per forsøg, du har med succes fundet oplysninger om";
$lang["SPY_SUCCESS2"] = "! Her er disse oplysninger.";

//Staff estates
$lang["STAFF_ESTATE_ADD"] = "Opret Estate";
$lang["STAFF_ESTATE_ADD_TABLE"] = "Brug denne formular til at tilføje en ejendom ind i spillet.";
$lang["STAFF_ESTATE_ADD_TH1"] = "Estate navn";
$lang["STAFF_ESTATE_ADD_TH2"] = "Estate Cost";
$lang["STAFF_ESTATE_ADD_TH3"] = "Estate Minimum Level";
$lang["STAFF_ESTATE_ADD_TH4"] = "Estate Will Level";
$lang["STAFF_ESTATE_ADD_BTN"] = "Opret Estate";
$lang["STAFF_ESTATE_ADD_ERROR1"] = "Du kan ikke oprette mere end én ejendom med samme navn.";
$lang["STAFF_ESTATE_ADD_ERROR2"] = "Du kan ikke tilføje en ejendom med samme vilje som en anden.";
$lang["STAFF_ESTATE_ADD_ERROR3"] = "Du kan ikke tilføje en ejendom med et krav om lavere niveau end 1.";
$lang["STAFF_ESTATE_ADD_ERROR4"] = "Du kan ikke tilføje en ejendom med en vilje niveau lig med eller lavere end, 100.";
$lang["STAFF_ESTATE_ADD_SUCCESS"] = "Estate er blevet tilføjet til spillet med succes.";

//Estates
$lang["ESTATES_START"] = "Dit nuværende Estate:";
$lang["ESTATES_SELL"] = "Sælg din ejendom for 75%";
$lang["ESTATES_TABLE1"] = "Estate navn";
$lang["ESTATES_TABLE2"] = "Niveau Krav";
$lang["ESTATES_TABLE3"] = "Cost ({$lang['INDEX_PRIMCURR']})";
$lang["ESTATES_TABLE4"] = "Vil Level";
$lang["ESTATES_ERROR1"] = "Du forsøger at købe en ikke-eksisterende ejendom Tjek din kilde, og prøv igen..";
$lang["ESTATES_ERROR2"] = "Du kan ikke købe en ejendom, der har mindre vilje end din nuværende ejendom, der bare ikke ville give mening..";
$lang["ESTATES_ERROR3"] = "Du har ikke nok {$lang['INDEX_PRIMCURR']} for at købe ejendom";
$lang["ESTATES_ERROR4"] = "Du kan ikke købe en ejendom, der har den samme vilje din nuværende ejendom, der bare ikke ville give mening..";
$lang["ESTATES_ERROR5"] = "Du kan ikke sælge din ejendom, hvis du er nøgen og stolt, opløbet.";
$lang["ESTATES_ERROR6"] = "Dit niveau er for lavt for denne ejendom, min ven.";
$lang["ESTATES_SUCCESS1"] = "Du har nu købt";
$lang["ESTATES_SUCCESS2"] = "Du har solgt din ejendom for 75% af den oprindelige pris og gik tilbage til at være nøgen og stolt.";
$lang["ESTATES_INFO"] = "List nedenfor er godser du kan købe. Estates har en vilje niveau. Den bedre viljen niveau, jo flere statistikker, du vil vinde mens uddannelse. Så sin anbefales at købe den bedste ejendom til dit niveau. . Tryk på godset navn for at købe ejendommen fortvivl ikke, vil du være i stand til at sælge købt godser tilbage til spillet for 75% af sin værdi. ";

//Roulette
$lang["ROULETTE_TITLE"] = "Roulette";
$lang["ROULETTE_INFO"] = "Klar til at prøve lykken? Awesome! Her på roulette-bordet, huset vinder altid. For at bekæmpe spillere miste al deres rigdom på én gang, har vi sat i en bet begrænsning. På din niveau, kan du kun satse ";
$lang["ROULETTE_NOREFRESH"] = ". Du må ikke opdatere mens du spiller roulette Brug venligst links, tak!";
$lang["ROULETTE_TABLE1"] = "Bet";
$lang["ROULETTE_TABLE2"] = "Pick #";
$lang["ROULETTE_ERROR1"] = "Du kan ikke satse mere {$lang['INDEX_PRIMCURR']}, end du har i øjeblikket.";
$lang["ROULETTE_ERROR2"] = "Du forsøger at placere et væddemål højere end din nuværende tidspunkt tilladt max bet.";
$lang["ROULETTE_ERROR3"] = "Du kan kun satse på de tal mellem 0 og 36.";
$lang["ROULETTE_ERROR4"] = "Du skal angive et bet større end 0 {$lang['INDEX_PRIMCURR']}.";
$lang["ROULETTE_LOST"] = ". Du mister din indsats Undskyld mand..";
$lang["ROULETTE_WIN"] = "og vandt Du holder din indsats, og lomme en ekstra!";
$lang["ROULETTE_BTN1"] = "Place Bet!";
$lang["ROULETTE_BTN2"] = "Igen samme indsats, tak..";
$lang["ROULETTE_BTN3"] = "Igen, men med en anden indsats.";
$lang["ROULETTE_BTN4"] = ". Jeg holdt op, jeg ønsker ikke at gå brød.";
$lang["ROULETTE_START"] = "Du lægger i din indsats og træk håndtaget ned Rundt og rundt om hjulet drejer det stopper og lander på..";

//High Low
$lang["HILOW_NOREFRESH"] = ". Du må nto opdatere mens du spiller High / Low Brug linkene vi leverer, tak!";
$lang["HILOW_INFO"] = "Velkommen til High / Low Her vil du satse på, hvorvidt handlen vil trække et nummer lavere eller højere end det antal vist Antallet interval er 1 til 100...";
$lang["HILOW_SHOWN"] = "Spillet operatør viser antallet";
$lang["HILOW_WATDO"] = "Vælg knappen på hvordan du føler det næste nummer vil blive sammenlignet med dette nummer.";
$lang["HILOW_NOBET"] = "Du har ikke nok {$lang['INDEX_PRIMCURR']} for at spille High / Low Du skal mindst.";
$lang["HILOW_LOWER"] = "Lavere";
$lang["HILOW_HIGHER"] = "Højere";
$lang["HIGHLOW_HIGH"] = "Du har gættet spillet operatør ville vise et tal højere end";
$lang["HIGHLOW_REVEAL"] = "Spillet operatør afslører tallet";
$lang["HIGHLOW_LOSE"] = "Du har mistet denne gang, sorry opløbet.";
$lang["HIGHLOW_WIN"] = "Du har vundet denne gang, tillykke.";
$lang["HIGHLOW_LOWER"] = "Du har gættet spillet operatør ville vise et tal lavere end";
$lang["HIGHLOW_TIE"] = "Spillet operatør viser det præcise antal som sidste gang Du mister intet..";
$lang["HILOW_UNDEFINEDNUMBER"] = "Antallet fra den sidste side blev ikke defineret ... Weird Stop manipulation med lort, mand..";

//ReCaptcha
$lang["RECAPTCHA_TITLE"] = "reCAPTCHA";
$lang["RECAPTCHA_INFO"] = "Dette er en nødvendig onde Bare kontrollere, at du ikke er en bot..";
$lang["RECAPTCHA_BTN"] = "Bekræft";
$lang["RECAPTCHA_EMPTY"] = "Du kan ikke forlade reCAPTCHA formularen tom!";
$lang["RECAPTCHA_FAIL"] = "Du mislykkedes reCAPTCHA Gå tilbage og prøv igen..";

//Poke
$lang["POKE_TITLE"] = "Er du sikker på du ønsker at poke";
$lang["POKE_TITLE1"] = "Du må ikke chikanere brugere ved hjælp af denne Personalet vil finde ud af, og de kan fjerne din privledge at stikke andre..";
$lang["POKE_ERROR1"] = "Du skal angive en person, du ønsker at stikke.";
$lang["POKE_ERROR2"] = "Nej, du kan ikke stikke dig selv!";
$lang["POKE_ERROR3"] = "Du kan ikke stikke ikke-eksisterende brugere!";
$lang["POKE_BTN"] = "poke";
$lang["POKE_SUCC"] = "Du har nu stak denne bruger.";

//Staff Change PW
$lang["STAFF_USERS_CP_FORM_INFO"] = "Brug denne formular til at ændre en brugers adgangskode.";
$lang["STAFF_USERS_CP_USER"] = "Bruger";
$lang["STAFF_USERS_CP_FORM_BTN"] = "Skift adgangskode";
$lang["STAFF_USERS_CP_PW"] = "Ny adgangskode";
$lang["STAFF_USERS_CP_ERROR"] = "Du kan ikke ændre adgangskoden til admin-kontoen på denne måde.";
$lang["STAFF_USERS_CP_ERROR1"] = "Du kan ikke ændre adgangskoden til andre admin konti på denne måde.";
$lang["STAFF_USERS_CP_SUCCESS"] = "brugerens adgangskode er blevet ændret med succes.";

//Item Send
$lang["ITEM_SEND_ERROR"] = "Du forsøger at sende en ikke-eksisterende emne, eller du bare ikke har dette element i din beholdning.";
$lang["ITEM_SEND_ERROR1"] = "Du forsøger at sende mere af dette element, end du har i øjeblikket.";
$lang["ITEM_SEND_ERROR2"] = "Du forsøger at sende artiklen til en bruger, der ikke eksisterer.";
$lang["ITEM_SEND_ERROR3"] = "Det giver ingen mening at sende dig selv et element.";
$lang["ITEM_SEND_SUCC"] = "Du har sendt";
$lang["ITEM_SEND_SUCC1"] = "til";
$lang["ITEM_SEND_FORMTITLE"] = "Indtast hvem du ønsker at sende";
$lang["ITEM_SEND_FORMTITLE1"] = ". Sammen med den mængde, du ønsker at sende Du har";
$lang["ITEM_SEND_FORMTITLE2"] = "Alternativt kan du indtaste en brugers id-nummer.";
$lang["ITEM_SEND_TH"] = "Bruger";
$lang["ITEM_SEND_TH1"] = "Mængde til Send";
$lang["ITEM_SEND_BTN"] = "Send vare (r)";

//Slots
$lang["SLOTS_INFO"] = ".! Velkommen til slots maskine Bet nogle af dine hårdt tjente penge for en lille chance for at vinde stort på dit niveau, vi har indført et betting begrænsning af";
$lang["SLOTS_TABLE1"] = "Bet";
$lang["SLOTS_BTN"] = "Spin baby, spinde!";
$lang["SLOTS_TITLE"] = "spilleautomaten";
$lang["SLOTS_NOREFRESH"] = ". Du må ikke opdatere siden, mens gambling på spilleautomater Tak!";

//Bot tent
$lang["BOTTENT_TITLE"] = "Bot telt";
$lang["BOTTENT_DESC"] = "Velkommen til Bot telt. Her kan du udfordre NPC'ere til kamp. Hvis du vinder, vil du modtage et element. Disse elementer kan eller ikke kan være nyttige i dine eventyr. At afskrække spillere få massive mængder af elementer, kan du kun angribe disse NPC'ere alle så ofte Deres cooldown er opført her, samt at modtage varen, skal du krus bot ";
$lang["BOTTENT_TH"] = "Bot navn";
$lang["BOTTENT_TH1"] = "Bot Level";
$lang["BOTTENT_TH2"] = "Bot Cooldown";
$lang["BOTTENT_TH3"] = "Bot Item Drop";
$lang["BOTTENT_TH4"] = "Attack";
$lang["BOTTENT_WAIT"] = "Cooldown Resterende:";

//Staff bots
$lang["STAFF_BOTS_TITLE"] = "Staff Bots";
$lang["STAFF_BOTS_ADD"] = "Tilføj Bot";
$lang["STAFF_BOTS_DEL"] = "Slet Bot";
$lang["STAFF_BOTS_ADD_FRM1"] = "Brug denne formular til at tilføje bots til det spil, drop elementer, når overfaldet.";
$lang["STAFF_BOTS_ADD_FRM2"] = "Bot bruger";
$lang["STAFF_BOTS_ADD_FRM3"] = "Item Droppet";
$lang["STAFF_BOTS_ADD_FRM4"] = "Cooldown (sekunder)";
$lang["STAFF_BOTS_ADD_BTN"] = "Tilføj Bot";
$lang["STAFF_BOTS_ADD_ERROR"] = "du mangler en af de krævede indgange Gå tilbage og prøv igen..";
$lang["STAFF_BOTS_ADD_ERROR1"] = "du prøver at tilføje en bot, der allerede findes i bot notering Gå tilbage og prøv igen..";
$lang["STAFF_BOTS_ADD_ERROR2"] = "Du kan kun tilføje NPC'ere til bot notering Gå tilbage og prøv igen..";
$lang["STAFF_BOTS_ADD_ERROR3"] = "Du kan ikke have en bot droppe en ikke-eksisterende emne Tilbage, og prøv igen..";
$lang["STAFF_BOTS_ADD_SUCCESS"] = "Du har føjet en NPC til bot listen.";

//VIP Donation Listing
$lang["VIP_LIST"] = "købe en VIP pakke";
$lang["VIP_INFO"] = "Hvis du køber en VIP-pakke nedefra, vil du blive begavet følgende afhængigt af pakken du køber Hvis du begår bedrageri, vil du blive permenantly forbudt..";
$lang["VIP_TABLE_TH1"] = "Pack info";
$lang["VIP_TABLE_TH2"] = "Pakkens indhold";
$lang["VIP_TABLE_TH3"] = "Link";
$lang["VIP_TABLE_VDINFO"] = "VIP dage deaktivere annoncer omkring spillet. Du vil også modtage 16% energi refill i stedet for 8%. Du vil også modtage en stjerne ved dit navn, og dit navn vil skifte farve. Hvordan fantastisk er, at? ";
$lang["VIP_THANKS"] = "Tak for at donere til";
$lang["VIP_CANCEL"] = "Du har annulleret din donation venligst donere senere.";
$lang["VIP_SUCCESS"] = "Vi sætter pris på den helt. Du kan se en kvittering på denne transaktion på <a href='http://www.paypal.com'>Paypal</a>. Bør gives Dine varer . til du automatisk ret hurtigt Hvis ikke, skal du kontakte en admin om hjælp!";

//Staff punishments
$lang["STAFF_PUNISHFED_FORM"] = "fængsle Bruger";
$lang["STAFF_PUNISHFED_INFO"] = "Placering af et bruger i føderal fængsel vil gøre deres konto næsten ubrugelig De vil ikke være i stand til at gøre noget i spillet..";
$lang["STAFF_PUNISHFED_TH"] = "Bruger:";
$lang["STAFF_PUNISHFED_TH1"] = "Days:";
$lang["STAFF_PUNISHFED_TH2"] = "Årsag:";
$lang["STAFF_PUNISHFED_BTN"] = "Place Bruger i Federal Jail";
$lang["STAFF_PUNISHFED_ERR"] = "Du kan ikke placere en bruger, der ikke eksisterer i den føderale fængsel.";
$lang["STAFF_PUNISHFED_ERR1"] = "Du skal udfylde alle indgange på forrige side for at dette virker korrekt.";
$lang["STAFF_PUNISHFED_ERR2"] = "Du kan ikke placere admins i den føderale fængsel venligst destaff dem først, før du prøver igen..";
$lang["STAFF_PUNISHFED_SUCC"] = "Brugeren er blevet placeret i den føderale fængsel med succes.";

//Fedjail listing
$lang["FJ_TITLE"] = "Federal Jail";
$lang["FJ_INFO"] = "Dette er hvor dårlige folk gå, når de bryder reglerne Være en smart person, og ikke bryde reglerne, eller du kan aldrig se dagens lys igen..";
$lang["FJ_WHO"] = "Hvem";
$lang["FJ_TIME"] = "Resterende tid";
$lang["FJ_RS"] = "Reason";
$lang["FJ_JAILER"] = "Jailer";

//Mining
$lang["MINE_INFO"] = "Velkommen til de farlige miner, hjernedøde fjols. Riches er Tilgængelig for dig, hvis du har evnerne. Hver mine har sine egne krav, og kunne endda have en særlig hakke, som du skal bruge. ";
$lang["MINE_DUNGEON"] = ". Kun ærefulde krigere kan mine Kom tilbage når du har tjent din gæld til samfundet.";
$lang["MINE_INFIRM"] = ". Kun raske krigere kan mine Kom tilbage når du har rippet at bandaid fra din finger.";
$lang["MINE_LEVEL"] = "Du har i øjeblikket en minedrift niveau";
$lang["MINE_POWER"] = "Mining Power";
$lang["MINE_XP"] = "Mining Experience";
$lang["MINE_SPOTS"] = "Åbn Mines";
$lang["MINE_SETS"] = "Purchase Power Sets";
$lang["MINE_BUY_ERROR"] = "Du forsøger at købe mere magt sæt, end du i øjeblikket har til rådighed for dig Husk, du får en magt sæt, hver gang du niveau op din minedrift niveau..";
$lang["MINE_BUY_ERROR_IQ"] = "Du har ikke nok IQ til at købe, at mange sæt strøm Du har brug for.";
$lang["MINE_BUY_ERROR_IQ1"] = "endnu, du kun har";
$lang["MINE_BUY_SUCCESS"] = "! Tillykke Du har med succes handlet";
$lang["MINE_BUY_SUCCESS1"] = "sæt minedrift magt.";
$lang["MINE_BUY_INFO"] = "Fra dette øjeblik, du kan købe";
$lang["MINE_BUY_INFO1"] = "sæt minedrift magt Husk, et sæt af minedrift magt er lig med 10 minedrift magt Du låse ekstra sæt ved opjustering din minedrift niveau Hvad nu, vil hvert sæt koste dig...";
$lang["MINE_BUY_INFO2"] = ". IQ hver Så hvor mange sæt kan du ønsker at købe?";
$lang["MINE_BUY_BTN"] = "Køb Power Sets";
$lang["MINE_DO_ERROR"] = "Ugyldig minedrift stedet.";
$lang["MINE_DO_ERROR1"] = "Du forsøger at minen på et sted, der ikke eksisterer.";
$lang["MINE_DO_ERROR2"] = "Din minedrift er for lavt til minen her Du skal være på minimum, minedrift niveau.";
$lang["MINE_DO_ERROR3"] = "Du kan kun mine i en minedrift stedet, hvis du er i samme placering.";
$lang["MINE_DO_ERROR4"] = "Din IQ er for lavt til minen her Du skal have som minimum.";
$lang["MINE_DO_ERROR5"] = ". Du har ikke nok minedrift magt til minen her Du skal have mindst";
$lang["MINE_DO_ERROR6"] = "Du har ikke den krævede hakke til minen her Kom tilbage når du har mindst én.";
$lang["MINE_DO_FAIL"] = "Mens minedrift væk, du finde en gas lomme og antænde hele minen du fundet senere, knap trække vejret..";
$lang["MINE_DO_FAIL1"] = "Du og en anden minearbejder komme ind i en arguement over hvem så dette stykke malm først. Talking bliver råben og råben bliver skubbe, skubbe går til stykket, og den næste ting du ved, du og ham begge kæmper på jorden vagterne nærheden se dette og arrestere jer begge. ";
$lang["MINE_DO_FAIL2"] = ". Hvor uheldig Dine forsøg minedrift viste sig forgæves.";
$lang["MINE_DO_SUCC"] = "Du slog et stykke af sten til at afsløre en stor vene malm Efter et par minutter af omhyggeligt udgravning, du formået at opnå.";
$lang["MINE_DO_SUCC1"] = "fra denne åre.";
$lang["MINE_DO_SUCC2"] = "Mens minedrift væk, lykkedes dig at mesterligt mine et stykke";
$lang["MINE_DO_BTN1"] = "Mine Again";
$lang["MINE_DO_BTN"] = "Gå tilbage";

//Staff mining
$lang["STAFF_MINE_TITLE"] = "Mining Panel";
$lang["STAFF_MINE_ADD_ERROR"] = "Ingen af ​​de indgange på tidligere form kan forblive tom Gå tilbage og prøv igen..";
$lang["STAFF_MINE_ADD_ERROR1"] = "Den mindste minedrift niveauet for denne mine skal være mindst 1.";
$lang["STAFF_MINE_ADD_ERROR2"] = "Den mindste output for posten udgange kan ikke være større end eller lig med sin maksimale.";
$lang["STAFF_MINE_ADD_ERROR3"] = "Den by, du har valgt for minen at være placeret i ikke eksisterer Kontroller, og prøv igen..";
$lang["STAFF_MINE_ADD_ERROR4"] = "Det element, du har valgt for minens hakke eksisterer ikke Tjek din kilde, og prøv igen..";
$lang["STAFF_MINE_ADD_ERROR5"] = "Det element, du har valgt for minens Output # 1 findes ikke Kontroller din kilde, og prøv igen..";
$lang["STAFF_MINE_ADD_ERROR6"] = "Det element, du har valgt for minens Output # 2 eksisterer ikke Tjek din kilde, og prøv igen..";
$lang["STAFF_MINE_ADD_ERROR7"] = "Det element, du har valgt for minens Output # 3 findes ikke Kontroller din kilde, og prøv igen..";
$lang["STAFF_MINE_ADD_ERROR8"] = "Det element, du har valgt for minens perle eksisterer ikke Tjek din kilde, og prøv igen..";
$lang["STAFF_MINE_ADD_SUCCESS"] = "Du har oprettet en mine.";
$lang["STAFF_MINE_ADD_FRMINFO"] = "Brug denne formular til at tilføje en mine til spillet Minen navn vil blive løst baseret på byen dens placeret i..";
$lang["STAFF_MINE_FORM_LOCATION"] = "minens Location";
$lang["STAFF_MINE_FORM_LVL"] = "Minimum Mining Level";
$lang["STAFF_MINE_FORM_IQ"] = "Minimum IQ kræves";
$lang["STAFF_MINE_FORM_PEPA"] = "Power Udstødning / Attempt";
$lang["STAFF_MINE_FORM_PICK"] = "Påkrævet hakke";
$lang["STAFF_MINE_FORM_OP1"] = "Vare # 1";
$lang["STAFF_MINE_FORM_OP2"] = "Vare # 2";
$lang["STAFF_MINE_FORM_OP3"] = "Vare # 3";
$lang["STAFF_MINE_FORM_GEM"] = "Perle Item";
$lang["STAFF_MINE_FORM_OP1MIN"] = "Vare # 1 Minimum udgang";
$lang["STAFF_MINE_FORM_OP2MIN"] = "Vare # 2 Minimum udgang";
$lang["STAFF_MINE_FORM_OP3MIN"] = "Vare # 3 Minimum udgang";
$lang["STAFF_MINE_FORM_OP1MAX"] = "Vare # 1 Maksimal ydelse";
$lang["STAFF_MINE_FORM_OP2MAX"] = "Vare # 2 Maksimal ydelse";
$lang["STAFF_MINE_FORM_OP3MAX"] = "Vare # 3 Maksimal ydelse";
$lang["STAFF_MINE_EDIT1"] = "Vælg en mine til at ændre.";
$lang["STAFF_MINE_EDIT2"] = "Redigering af en eksisterende mine ...";
$lang["STAFF_MINE_ADD_BTN"] = "Opret Mine";
$lang["STAFF_MINE_EDIT_BTN"] = "Alter Mine";
$lang["STAFF_MINE_EDIT_SUCCESS"] = "Minen er blevet redigeret.";
$lang["STAFF_MINE_EDIT_ERR"] = "Du har valgt en ikke-eksisterende mine Tjek din kilde, og prøv igen..";
$lang["STAFF_MINE_DEL_SUCCESS"] = "Du har slettet en mine";
$lang["STAFF_MINE_DEL1"] = "Vælg en mine til at slette.";
$lang["STAFF_MINE_DEL_BTN"] = "! Slet Mine (Ingen Prompt, Be Sure!)";

//Announcements
$lang["ANNOUNCEMENTS_TIME"] = "Time Sendt";
$lang["ANNOUNCEMENTS_TEXT"] = "Bekendtgørelse tekst";
$lang["ANNOUNCEMENTS_READ"] = "Læs";
$lang["ANNOUNCEMENTS_UNREAD"] = "Ulæst";
$lang["ANNOUNCEMENTS_POSTED"] = "Indsendt af:";

//Dungeon and Infirmary
$lang["DUNGINFIRM_TITLE"] = "Dungeon";
$lang["DUNGINFIRM_TITLE1"] = "Infirmary";
$lang["DUNGINFIRM_INFO"] = "Der er i øjeblikket";
$lang["DUNGINFIRM_INFO1"] = "spillere i fangehullet.";
$lang["DUNGINFIRM_INFO2"] = "spillere i infirmeriet.";
$lang["DUNGINFIRM_TD1"] = "Bruger / bruger-id";
$lang["DUNGINFIRM_TD2"] = "Reason";
$lang["DUNGINFIRM_TD3"] = "Check-in Time";
$lang["DUNGINFIRM_TD4"] = "Check-out tid";

//Staff Index
$lang["STAFF_IDX_TITLE"] = "Staff Panel";
$lang["STAFF_IDX_PHP"] = "PHP Version";
$lang["STAFF_IDX_DB"] = "Database Version";
$lang["STAFF_IDX_CENGINE"] = "Chivalry Engine Version";
$lang["STAFF_IDX_CE_UP"] = "Chivalry Engine opdatering Checker";
$lang["STAFF_IDX_API"] = "API Version";
$lang["STAFF_IDX_IFRAME"] = "Undskyld, men din browser understøtter ikke iframes, der er nødvendige for at bruge denne opdatering brik.";
$lang["STAFF_IDX_ADMIN_TITLE"] = "Admin Handlinger";
$lang["STAFF_IDX_ADMIN_LI"] = "Admin";
$lang["STAFF_IDX_ADMIN_LI1"] = "Moduler";
$lang["STAFF_IDX_ADMIN_LI2"] = "Brugere";
$lang["STAFF_IDX_ADMIN_LI3"] = "Emner";
$lang["STAFF_IDX_ADMIN_LI4"] = "Butikker";
$lang["STAFF_IDX_ADMIN_LI5"] = "Academy";
$lang["STAFF_IDX_ADMIN_LI6"] = "NPCs";
$lang["STAFF_IDX_ADMIN_LI7"] = "Jobs";
$lang["STAFF_IDX_ADMIN_LI8"] = "Afstemninger";
$lang["STAFF_IDX_ADMIN_LI9"] = "Byer";
$lang["STAFF_IDX_ADMIN_LI10"] = "Estates";
$lang["STAFF_IDX_ADMIN_TAB1"] = "Spilindstillinger";
$lang["STAFF_IDX_ADMIN_TAB2"] = "Opret en Announcement";
$lang["STAFF_IDX_ADMIN_TAB3"] = "Spil Diagnostics";
$lang["STAFF_IDX_ADMIN_TAB4"] = "Opdater brugere";
$lang["STAFF_IDX_MODULES_TAB1"] = "Forbrydelser";
$lang["STAFF_IDX_USERS_TAB1"] = "Opret bruger";
$lang["STAFF_IDX_USERS_TAB2"] = "Rediger bruger";
$lang["STAFF_IDX_USERS_TAB3"] = "Slet bruger";
$lang["STAFF_IDX_USERS_TAB4"] = "force Logud Bruger";
$lang["STAFF_IDX_USERS_TAB5"] = "Change User Password";
$lang["STAFF_IDX_ITEMS_TAB1"] = "Opret Element Group";
$lang["STAFF_IDX_ITEMS_TAB2"] = "Opret element";
$lang["STAFF_IDX_ITEMS_TAB3"] = "Slet element";
$lang["STAFF_IDX_ITEMS_TAB4"] = "Rediger element";
$lang["STAFF_IDX_ITEMS_TAB5"] = "Giv Vare til bruger";
$lang["STAFF_IDX_SHOPS_TAB1"] = "Opret Shop";
$lang["STAFF_IDX_SHOPS_TAB2"] = "Slet Shop";
$lang["STAFF_IDX_SHOPS_TAB3"] = "Føj Lager til Shop";
$lang["STAFF_IDX_NPC_TAB1"] = "Tilføj NPC Bot";
$lang["STAFF_IDX_NPC_TAB2"] = "Slet NPC Bot";
$lang["STAFF_IDX_ASSIST_TITLE"] = "Assistant aktioner";
$lang["STAFF_IDX_ASSIST_LI"] = "Game Logs";
$lang["STAFF_IDX_ASSIST_LI1"] = "Tilladelser";
$lang["STAFF_IDX_ASSIST_LI2"] = "Mining";
$lang["STAFF_IDX_LOGS_TAB1"] = "General Logs";
$lang["STAFF_IDX_LOGS_TAB2"] = "bruger logger";
$lang["STAFF_IDX_LOGS_TAB3"] = "Training Logs";
$lang["STAFF_IDX_LOGS_TAB4"] = "Attack Logs";
$lang["STAFF_IDX_LOGS_TAB5"] = "Login Logs";
$lang["STAFF_IDX_LOGS_TAB6"] = "Udstyr Logs";
$lang["STAFF_IDX_LOGS_TAB7"] = "Banking Logs";
$lang["STAFF_IDX_LOGS_TAB8"] = "Kriminelle Logs";
$lang["STAFF_IDX_LOGS_TAB9"] = "Item Brug Log";
$lang["STAFF_IDX_LOGS_TAB10"] = "Item købe Logs";
$lang["STAFF_IDX_LOGS_TAB11"] = "Item Marked Logs";
$lang["STAFF_IDX_LOGS_TAB12"] = "Staff Logs";
$lang["STAFF_IDX_LOGS_TAB13"] = "Travel Logs";
$lang["STAFF_IDX_LOGS_TAB14"] = "Verifikation Logs";
$lang["STAFF_IDX_LOGS_TAB15"] = "Spy Forsøg Logs";
$lang["STAFF_IDX_LOGS_TAB16"] = "Gambling Logs";
$lang["STAFF_IDX_LOGS_TAB17"] = "Item Selling Logs";
$lang["STAFF_IDX_PERM_TAB1"] = "Vis Tilladelser";
$lang["STAFF_IDX_PERM_TAB2"] = "Reset Tilladelser";
$lang["STAFF_IDX_PERM_TAB3"] = "Rediger Tilladelser";
$lang["STAFF_IDX_MINE_TAB1"] = "Tilføj Mine";
$lang["STAFF_IDX_MINE_TAB2"] = "Rediger Mine";
$lang["STAFF_IDX_MINE_TAB3"] = "Slet Mine";
$lang["STAFF_IDX_FM_TITLE"] = "Forum Moderator Handlinger";
$lang["STAFF_IDX_FM_LI"] = "Straf";
$lang["STAFF_IDX_FM_LI1"] = "Forums";
$lang["STAFF_IDX_ACTIONS"] = "Sidste 15 Personaleomkostninger Handlinger";
$lang["STAFF_IDX_ACTIONS_TH"] = "Time";
$lang["STAFF_IDX_ACTIONS_TH1"] = "medarbejder";
$lang["STAFF_IDX_ACTIONS_TH2"] = "Log tekst";
$lang["STAFF_IDX_ACTIONS_TH3"] = "IP";
$lang["STAFF_IDX_SMELT_TAB1"] = "Tilføj Smeltning opskrift";
$lang["STAFF_IDX_SMELT_TAB2"] = "Slet Smeltning opskrift";
$lang["STAFF_IDX_SMELT_LIST"] = "Smelting";

//User List
$lang["USERLIST_TITLE"] = "Brugerliste";
$lang["USERLIST_PAGE"] = "Sider";
$lang["USERLIST_ORDERBY"] = "Order By";
$lang["USERLIST_ORDER1"] = "Bruger-ID";
$lang["USERLIST_ORDER2"] = "Navn";
$lang["USERLIST_ORDER3"] = "Level";
$lang["USERLIST_ORDER4"] = "Primær valuta";
$lang["USERLIST_ORDER5"] = "Stigende";
$lang["USERLIST_ORDER6"] = "faldende";
$lang["USERLIST_TH1"] = "Køn";
$lang["USERLIST_TH2"] = "Aktiv?";

//Stats Page
$lang["STATS_TITLE"] = "Statistik Center";
$lang["STATS_CHART"] = "Bruger Operativsystemer";
$lang["STATS_CHART1"] = "Køn Ratio";
$lang["STATS_CHART2"] = "Class Ratio";
$lang["STATS_CHART3"] = "Bruger Browser Choice";
$lang["STATS_TH"] = "Statistik";
$lang["STATS_TH1"] = "Statistik Value";
$lang["STATS_TD"] = "Registrer Players";
$lang["STATS_TD1"] = "Primær Valuta Trukket tilbage";
$lang["STATS_TD2"] = "Primær valuta i banker";
$lang["STATS_TD3"] = "Total primære valuta";
$lang["STATS_TD4"] = "Sekundær valuta i Circulation";
$lang["STATS_TD5"] = "Primær Valuta / afspiller (Gennemsnit)";
$lang["STATS_TD6"] = "Sekundær valuta / afspiller (Gennemsnit)";
$lang["STATS_TD7"] = "Bank Balance / afspiller (Gennemsnit)";
$lang["STATS_TD8"] = "Registrerede Guilds";

//Staff List
$lang["STAFFLIST_ADMIN"] = "Admins";
$lang["STAFFLIST_LS"] = "Sidst set";
$lang["STAFFLIST_CONTACT"] = "Kontakt";
$lang["STAFFLIST_ASSIST"] = "assistenter";
$lang["STAFFLIST_MOD"] = "Forum Redaktører";

//Timezone Change
$lang["TZ_TITLE"] = "Ændring Tidszone";
$lang["TZ_BTN"] = "Skift tidszone";
$lang["TZ_SUCC"] = "Du har opdateret din tidszone indstillinger.";
$lang["TZ_FAIL"] = "Du har angivet en ugyldig tidszone indstilling.";
$lang["TZ_INFO"] = "Her kan du ændre din tidszone. Det vil ændre alle datoer på spillet for dig. Dette vil ikke fremskynde nogen processer. Standard tidszone er <u> (GMT) Greenwich Mean Time . </ u> Alle spil hele meddelelser og funktioner vil være baseret på denne tidszone. ";

//Newspaper
$lang["NP_TITLE"] = "Avis";
$lang["NP_AD"] = "Køb en annonce";
$lang["NP_ERROR"] = ". Der synes ikke at være nogen avisannoncer Måske du skal <a href='?action=buyad'> købe </a> liste én?";
$lang["NP_ADINFO"] = "Ad Info";
$lang["NP_ADTEXT"] = "Annoncetekst";
$lang["NP_ADINFO1"] = "Indsendt af";
$lang["NP_ADSTRT"] = "Start Date";
$lang["NP_ADEND"] = "Slutdato";
$lang["NP_BUY"] = "Købe en annonce";
$lang["NP_BUY_REMINDER"] = "Husk, at købe en add er underlagt spillets regler. Hvis du sender noget her, der vil bryde et spil regel, vil du blive advaret, og din annonce vil blive fjernet. Hvis du finder nogen misbruger nyheder papir, så lad en admin vide med det samme! ";
$lang["NP_BUY_TD1"] = "Initial Ad Cost";
$lang["NP_BUY_TD2"] = "Ad Runtime";
$lang["NP_BUY_TD3"] = "Annoncetekst";
$lang["NP_BUY_TD4"] = "Total Ad Cost";
$lang["NP_BUY_TD5"] = "En højere antal vil placere dig højere på annoncen listen.";
$lang["NP_BUY_TD6"] = "Hver dag vil tilføje 1250 Primær Valuta til din pris.";
$lang["NP_BUY_TD7"] = "Hver karakter er værd 5 primære valuta.";
$lang["NP_BUY_BTN"] = "Place annonce";

//Smelting
$lang["SMELT_HOME"] = "Smeltery";
$lang["SMELT_TH"] = "Output Item";
$lang["SMELT_TH1"] = "Nødvendige elementer x Mængde";
$lang["SMELT_TH2"] = "Handling";
$lang["SMELT_DO"] = "Smelt Item";
$lang["SMELT_DONT"] = "Kan ikke håndværk";
$lang["SMELT_ERR"] = "Du forsøger at oprette et element med en ikke-eksisterende smeltning opskrift.";
$lang["SMELT_ERR1"] = "du mangler et eller flere emner, der kræves for denne smeltning opskrift.";
$lang["SMELT_SUCC"] = "Du har begyndte at skabe din vare Det vil blive givet til dig snarest..";
$lang["SMELT_SUCC1"] = "Du har smeltes dette punkt.";

//Staff Smelting
$lang["STAFF_SMELT_HOME"] = "Staff Smeltery";
$lang["STAFF_SMELT_ADD_TH"] = "værdi";
$lang["STAFF_SMELT_ADD_TH1"] = "Input";
$lang["STAFF_SMELT_ADD_TH2"] = "smeltes Item";
$lang["STAFF_SMELT_ADD_TH3"] = "Tid til udførelse";
$lang["STAFF_SMELT_ADD_TH4"] = "Item kræves";
$lang["STAFF_SMELT_ADD_TH5"] = "smeltes Vare Mængde";
$lang["STAFF_SMELT_ADD_TH6"] = "Item nødvendige mængde";
$lang["STAFF_SMELT_ADD_SELECT1"] = "Straks";
$lang["STAFF_SMELT_ADD_SELECT2"] = "Sekunder";
$lang["STAFF_SMELT_ADD_SELECT3"] = "Minutes";
$lang["STAFF_SMELT_ADD_SELECT4"] = "Hours";
$lang["STAFF_SMELT_ADD_SELECT5"] = "dage";
$lang["STAFF_SMELT_ADD_BTN"] = "Tilføj nødvendig Item";
$lang["STAFF_SMELT_ADD_BTN2"] = "Fjern Obligatorisk element";
$lang["STAFF_SMELT_ADD_BTN3"] = "Tilføj smeltes Item";
$lang["STAFF_SMELT_ADD_SUCC"] = "Smeltning opskrift er blevet tilføjet.";
$lang["STAFF_SMELT_ADD_FAIL"] = "Missing en nødvendig indgang Gå tilbage og prøv igen..";
$lang["STAFF_SMELT_DEL_FORM"] = "Brug denne formular til at slette en smeltning opskrift.";
$lang["STAFF_SMELT_DEL_TH"] = "Smeltning opskrift";
$lang["STAFF_SMELT_DEL_BTN"] = "Slet opskrift";
$lang["STAFF_SMELT_DEL_SUCC"] = "Smeltning opskrift er blevet fjernet fra spillet.";
?>