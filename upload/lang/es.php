<?php
/*
	File: lang/es.php
	Created: 6/1/2016 at 6:06PM Eastern Time
	Info: The Spanish language file.
	Author: TheMasterGeneral
	Website: http://mastergeneral156.pcriot.com/
*/
 
$lang = array();
global $ir,$fee,$gain;
//Static
$lang["HEADER_TITLE"] = "La caballería del motor Unreleased";
 
// Menu
$lang["MENU_EXPLORE"] = "Explorar";
$lang["MENU_MAIL"] = "Correo";
$lang["MENU_EVENT"] = "Notificaciones";
$lang["MENU_INVENTORY"] = "Inventario";
$lang["MENU_OUT"] = "<i><small>Desarrollado con los códigos de <a href='https://twitter.com/MasterGeneralYT'><font color=gray>TheMasterGeneral</font></a>. Usado con permiso.</small></i>";
$lang['MENU_PROFILE']='Perfil';
$lang['MENU_SETTINGS']='Ajustes';
$lang['MENU_STAFF']='Panel del personal';
$lang['MENU_LOGOUT']='Cerrar sesión';
$lang['MENU_TIN']='El tiempo es ahora';
$lang['MENU_QE']='El tiempo es ahora';
$lang['MENU_UNREADMAIL1']='¡Correo no leido!';
$lang['MENU_UNREADNOTIF']='¡Notificaciones leidos!';
$lang['MENU_UNREADANNONCE']='¡Anuncios leidos!';
$lang['MENU_UNREADANNONCE1']='Existen';
$lang['MENU_UNREADANNONCE2']='anuncios que aún no se han leído. Leelo';
$lang['MENU_UNREADMAIL2']='Tienes';
$lang['MENU_UNREADMAIL3']='mensajes no leídos. Hacer clic';
$lang['MENU_UNREADMAIL4']='para leerlos.';
$lang['MENU_UNREADNOTIF1']='notificaciones no leídas. Hacer clic';
$lang['MENU_INFIRMARY1']='Usted está en la enfermería para la próxima';
$lang['MENU_DUNGEON1']='Estás en la mazmorra para el próximo';
$lang['MENU_XPLOST']="Al huir de la lucha, has perdido toda tu experiencia!";
$lang['MENU_RULES']="Reglas del juego";

// Preferences
$lang["PREF_CPASSWORD"] = "Cambia la contraseña";
$lang["PREF_WELCOME_1"] = "Saludos allí,";
$lang["PREF_WELCOME_2"] = ", Y bienvenidos al Centro de Preferencias. Usted puede ver y cambiar la información sobre su cuenta!";
$lang["PREF_CNAME"] = "Cambie el nombre de usuario";
$lang["PREF_CTIME"] = "Cambia Hora";
$lang["PREF_CLANG"] = "Cambiar idioma";
$lang["PREF_CPIC"] = "Cambiar imagen para mostrar";
$lang["PREF_CTHM"] = "Cambiar de tema";
$lang["PREF_CTHM_FORM"] = "Seleccione el tema al que desea cambiar. Esta acción se puede revertir en cualquier momento que desee.";
$lang["PREF_CTHM_FORM1"]="Selecciona tu tema";
$lang["PREF_CTHM_FORMDD1"]="Predeterminado (Brillante)";
$lang["PREF_CTHM_FORMDD2"]="Alternativa (oscura)";
$lang['PREF_CTHM_FORMBTN']="Actualizar Tema";
$lang['PREF_CTHM_SUB_ERROR']="Estás intentando usar un tema inexistente.";
$lang['PREF_CTHM_SUB_SUCCESS']="Su tema se ha actualizado correctamente. Los efectos se notarán en la carga de página siguiente.";

//Username Change
$lang["UNC_TITLE"] = "Cambiando su nombre de usuario ...";
$lang["UNC_INTRO"] = "Aquí puede cambiar su nombre que se muestra a través del juego. No utilice un nombre inapropiado o es posible que su privilegio de cambiar su nombre sea retirado.";
$lang["PREF_CNAME"] = "Cambie el nombre de usuario";
$lang["UNC_ERROR_1"] = "Ni siquiera se ha introducido un nuevo nombre de usuario! Hacer clic ";
$lang["UNC_ERROR_2"] = " para volver a intentarlo";
$lang['UNC_LENGTH_ERROR'] = "Los nombres de usuario deben ser, como mínimo, tres caracteres de longitud y, como máximo, veinte caracteres.";
$lang['UNC_INVALIDCHARCTERS'] = "Los nombres de usuario sólo pueden incluir números, letras, guiones y espacios!";
$lang['UNC_INUSE'] = "El nombre de usuario que ha elegido está en uso. Por favor seleccione otro nombre de usuario.";
$lang['UNC_GOOD'] = "Ha actualizado correctamente su nombre de usuario!";
$lang['UNC_NUN'] = "Nuevo nombre de usuario:";
$lang['UNC_BUTTON'] = "Cambie el nombre de usuario";

//Password Change
$lang["PW_TITLE"] = "Cambiando tu Contraseña...";
$lang['PW_CP'] = "Contraseña Actual";
$lang['PW_CNP'] = "Confirmar Nueva Contraseña";
$lang['PW_NP'] = "Nueva Contraseña";
$lang['PW_BUTTON'] = "Actualiza Contraseña";
$lang['PW_INCORRECT'] = "Lo que introdujo como su antigua contraseña es incorrecta. Inténtalo de nuevo.";
$lang['PW_NOMATCH'] = "Las nuevas contraseñas que ha introducido no coinciden. Volver y probar otra vez, por favor.";
$lang['PW_DONE'] = "La contraseña se ha actualizado correctamente.";

//Pic change
$lang['PIC_TITLE']="Cambio de visualización de imagen";
$lang['PIC_NOTE']="Tenga en cuenta que este debe estar alojado externamente, <a href='http://www.photobucket.com'>Photobucket</a> es nuestra recomendación.";
$lang['PIC_NOTE2']="Cualquier imagen que no son 250x250 serán redimensionadas automáticamente.";
$lang['PIC_NEWPIC']="Enlace a nueva foto:";
$lang['PIC_TOOBIG']="Imagen demasiado grande!";
$lang['PIC_TOOBIG2']="Tamaño de archivo de la imagen es demasiado grande. El tamaño máximo de una imagen proyectada es de 1 MB. Volver y probar otra vez, por favor.";
$lang['PIC_NOIMAGE']="Se ha especificado una dirección URL que no es ni siquiera una imagen. Volver y probar otra vez, por favor.";
$lang['PIC_SUCCESS']="Ha actualizado correctamente su imagen para mostrar! Se muestra a continuación.";

//Login Page
$lang["LOGIN_REGISTER"] = "Registro";
$lang["LOGIN_RULES"] = "Reglas del juego";
$lang["LOGIN_LOGIN"] = "Iniciar sesión";
$lang["LOGIN_AHA"] = "¿Ya tienes una cuenta?";
$lang["LOGIN_EMAIL"] = "Dirección de correo electrónico";
$lang["LOGIN_PASSWORD"] = "Contraseña";
$lang["LOGIN_LWE"] = "Iniciar sesión con el correo electrónico";
$lang["LOGIN_SIGNIN"] = "Registrarse";
$lang["LOGIN_NH"] = "¿Nuevo aquí? ¡<a href='register.php'>Únete a nosotros</a>!";

//Register
$lang["REG_FORM"] = "Registro";
$lang["REG_USERNAME"] = "Nombre de usuario";
$lang["REG_EMAIL"] = "Email";
$lang["REG_PW"] = "Contraseña";
$lang["REG_CPW"] = "Confirmar contraseña";
$lang["REG_SEX"] = "Género";
$lang["REG_CLASS"] = "Clase";
$lang["REG_REFID"] = "Remisión ID";
$lang["REG_PROMO"] = "Código promocional";
$lang['REG_WARRIORCLASS']="Clase Guerrero!";
$lang['REG_ROGUECLASS']="Clase Pícaro!";
$lang['REG_DEFENDERCLASS']="Clase Defensor!";
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

//CSRF Error
$lang["CSRF_ERROR_TITLE"] = "Bloqueado Acción!";
$lang["CSRF_PREF_MENU"] = "Se puede intentar la acción de ir";
$lang["CSRF_ERROR_TEXT"] = "El cambio que estaba tratando de hacer era bloqueado. Fue bloqueado porque cargó otra página en el juego. Si no ha cargado en una página diferente durante este tiempo, cambiar su contraseña inmediatamente, ya que otra persona puede tener acceso a su cuenta!";

//Alert Titles
$lang['ERROR_EMPTY'] = "Entrada vacío!";
$lang['ERROR_LENGTH'] = "Comprobar la longitud de entrada!";
$lang['ERROR_GENERIC'] = "¡UH oh!";
$lang['ERROR_SUCCESS'] = "¡Éxito!";
$lang['ERROR_INVALID'] = "¡Entrada inválida!";
$lang['ERROR_SECURITY'] = "¡Error de seguridad!";
$lang['ERROR_NONUSER'] = "Nonexistent User!";
$lang['ERROR_NOPERM'] ="¡Sin autorización!";
$lang['ERROR_UNKNOWN'] ="¡Error desconocido!";

//Generic
$lang["GEN_HERE"] = "aquí";
$lang["GEN_back"] = "espalda";
$lang["GEN_INFIRM"] = "¡Inconsciente!";
$lang["GEN_DUNG"] = "¡Bloqueado!";
$lang["GEN_GREETING"] = "Hola";
$lang["GEN_MINUTES"] = "minutos.";
$lang['GEN_EXP']="Experiencia";	
$lang['GEN_NEU']="Cuenta Eliminada";
$lang['GEN_AT']="a";
$lang['GEN_EDITED']="editado";
$lang['GEN_TIMES']="veces.";
$lang['GEN_RANK']='Rango';
$lang['GEN_ONLINE']='En línea';	
$lang['GEN_OFFLINE']='Desconectado';
$lang['GEN_FOR']="para";
$lang['GEN_INDAH']="En el";
$lang['GEN_YES']="Sí";
$lang['GEN_NO']="No";
$lang['GEN_STR']="Fuerza";
$lang['GEN_AGL']="Agilidad";
$lang['GEN_GRD']="Guardia";
$lang['GEN_IQ']="IQ";
$lang['GEN_LAB']="Trabajo";
$lang['GEN_GOHOME']="Vete a casa";
$lang['GEN_IUOF']="¡El uso del archivo no es válido!";
$lang['GEN_THEM']="Ellos";
$lang['GEN_CONTINUE']="Continuar";
$lang['GEN_FOR_S']="para";
$lang['GEN_NOPERM']="No tiene el nivel de usuario adecuado para ver esta página. Si esto no es correcto, póngase en contacto con un administrador inmediatamente!";

//Gym
$lang['GYM_INFIRM'] = "Mientras que usted está inconsciente, no se puede entrenar! Vuelve después de que se siente saludable!";
$lang['GYM_DUNG'] = "Los guardias normalmente permitirá trabajar, pero, lo que hizo fue considerado demasiado alto de un crimen. No se puede entrenar en este momento ...";
$lang['GYM_NEG'] = "¡No suficiente energía!";
$lang['GYM_INVALIDSTAT'] = "No se puede entrenar esa estadística!";
$lang['GYM_NEG_DETAIL'] = "Usted no tiene suficiente energía para entrenar que muchas veces. O bien esperar a que su energía para recuperarse, o rellenar manualmente!";

//Explore
$lang['EXPLORE_INTRO']='Se empieza a explorar la ciudad y encontrar algunas cosas interesantes para mantenerlo ocupado ...';
$lang['EXPLORE_REF']="Esa es su enlace de referencia. Dárselo a amigos, enemigos o simplemente correo basura a su alrededor. Recibirá 25 moneda secundaria sobre ellos unirse!";
$lang['EXPLORE_SHOP']="Tiendas";
$lang['EXPLORE_LSHOP']="Tiendas locales";
$lang['EXPLORE_POSHOP']="Tiendas jugador de vehículos usados";
$lang['EXPLORE_IMARKET']="Mercado de artículos";
$lang['EXPLORE_IAUCTION']="Artículo de la subasta";
$lang['EXPLORE_TRADE']="Comercio";
$lang['EXPLORE_SCMARKET']="Mercado de divisas secundaria";
$lang['EXPLORE_FD']="Distrito financiero";
$lang['EXPLORE_BANK']="Banco";
$lang['EXPLORE_ESTATES']="Estates";
$lang['EXPLORE_HL']="Trabajo duro";
$lang['EXPLORE_MINE']="Minería";
$lang['EXPLORE_WC']="La tala de árboles";
$lang['EXPLORE_FARM']="Agricultura";
$lang['EXPLORE_ADMIN']="Administración";
$lang['EXPLORE_USERLIST']="Lista de usuarios";
$lang['EXPLORE_STAFFLIST']="Lista de personal";
$lang['EXPLORE_FED']="Cárcel Federal";
$lang['EXPLORE_STATS']="Estadísticas del juego";
$lang['EXPLORE_REPORT']="Informe del jugador";
$lang['EXPLORE_GAMES']="Juegos";
$lang['EXPLORE_RR']="Ruleta rusa";
$lang['EXPLORE_HILO']="Alta baja";
$lang['EXPLORE_ROULETTE']="Ruleta";
$lang['EXPLORE_GUILDS']="Gremios";
$lang['EXPLORE_DUNG']="Mazmorra";
$lang['EXPLORE_INFIRM']="Enfermería";
$lang['EXPLORE_GYM']="Formación";
$lang['EXPLORE_JOB']="Tu trabajo";
$lang['EXPLORE_ACADEMY']="Academia local";
$lang['EXPLORE_ACT']="Ocupaciones";
$lang['EXPLORE_PINTER']="Interacción con los jugadores";
$lang['EXPLORE_FORUMS']="Foros";
$lang['EXPLORE_NEWSPAPER']="Periódico";			
$lang['EXPLORE_ANNOUNCEMENTS']="Announcements";
$lang['EXPLORE_CRIMES']="Centro Criminal";
$lang['EXPLORE_TRAVEL']="Viaje del caballo";
$lang['EXPLORE_GUILDLIST']="Lista de Guild";
$lang['EXPLORE_YOURGUILD']="Tu gremio";
$lang['EXPLORE_TOPTEN']="Top 10 Jugadores";

//Error Details
$lang['ERRDE_EXPLORE']="Ya que estás en la enfermería, no se puede visitar la ciudad!";
$lang['ERRDE_EXPLORE2']="Ya que estás en la mazmorra, no se puede visitar la ciudad!";
$lang['ERRDE_PN']="Su libreta personal no podía ser actualizado debido al límite de 65.655 caracteres.";
$lang['ERROR_MAIL_UNOWNED']='¡No puedes leer este mensaje porque no te lo enviaron!';
$lang['ERROR_FORUM_VF']="Vuelve e intenta nuevamente por nosotros, por favor. Hemos roto.";

//Index
$lang['INDEX_TITLE']="Información General";
$lang['INDEX_WELCOME']="Dar una buena acogida,";
$lang['INDEX_YLVW']="Su última visita fue el";
$lang['INDEX_LEVEL']="Nivel";
$lang['INDEX_CLASS']="Clase";
$lang['INDEX_VIP']="Días VIP";
$lang['INDEX_PRIMCURR']="Divisa Principal";
$lang['INDEX_SECCURR']="Moneda Secundaria";
$lang['INDEX_ENERGY']="Energía";
$lang['INDEX_BRAVE']="Valiente";
$lang['INDEX_WILL']="Será";
$lang['INDEX_PN']="Bloc de notas personales";
$lang['INDEX_PNSUCCESS']="Su libreta personal se ha actualizado correctamente.";
$lang['INDEX_EXP']='XP';
$lang['INDEX_HP']='HP';

//Form Buttons
$lang['FB_PN']="Actualizar Notas";
$lang['FB_PR']="Enviar informe jugador";

//Player Report
$lang['PR_TITLE']="Informe del jugador";
$lang['PR_INTRO']="¿Conoce a alguien que rompió las reglas, o sólo está siendo unhonorable? Este es el lugar de informar sobre ellos. Informar al usuario sólo una vez. Informar el mismo usuario en múltiples ocasiones se ralentizará el proceso. Si se descubre que abusen del sistema de informe de jugador, se le colocará distancia en la cárcel federal. Información que introduzca aquí será confidencial y sólo será leído por los miembros del personal de alto nivel. Si desea confesar un delito, esto es también un gran lugar también.";
$lang['PR_USER']="¿Usuario?";
$lang['PR_CATEGORY']="¿Categoría?";
$lang['PR_REASON']="¿Qué han hecho?";
$lang['PR_USER_PH']="ID de usuario del reproductor de ser malo.";
$lang['PR_REASON_PH']="Por favor incluya tanta información como sea posible.";
$lang['PR_CAT_1']='Abuso Bug';
$lang['PR_CAT_2']='Reproductor De Acoso';
$lang['PR_CAT_3']='Timos';
$lang['PR_CAT_4']='Envío de Correo Basura';
$lang['PR_CAT_5']='Infringir la Regla Alentadores';
$lang['PR_CAT_6']='Problema de Seguridad';
$lang['PR_CAT_7']='Otro';
$lang['PR_CATBAD']='Se ha especificado una categoría válida. Volver y probar otra vez, por favor.';
$lang['PR_MAXCHAR']='Está intentando entrar demasiado largo de un motivo. Esta forma sólo permitirá que ingrese, como máximo, 1250 caracteres en total. Volver y probar otra vez, por favor.';
$lang['PR_INVALID_USER']='Usted está tratando de informar a un jugador que simplemente no existe. Compruebe el ID de usuario que ha introducido y vuelve a intentarlo.';
$lang['PR_SUCCESS']='Has informado al usuario. El personal le puede enviar un mensaje a hacer preguntas sobre el informe que acaba de enviar. Por favor, responda a la medida de su capacidad.';

//Mail
$lang['MAIL_READ']='Leer';
$lang['MAIL_DELETE']='Eliminar';
$lang['MAIL_REPORT']='Informe';
$lang['MAIL_MSGREAD']='Mensaje Lee';
$lang['MAIL_MSGUNREAD']='Mensaje no Leído';
$lang['MAIL_USERDATE']='Usuario / Información';
$lang['MAIL_PREVIEW']='Vista Previa del Mensaje';
$lang['MAIL_ACTION']='Acciones';
$lang['MAIL_USERINFO']='Información Del Remitente';
$lang['MAIL_MSGSUB']='Asunto Del Mensajee';
$lang['MAIL_STATUS']='Estado';
$lang['MAIL_SENTAT']='Enviado A Las';
$lang['MAIL_SENDTO']='A';
$lang['MAIL_FROM']='De';
$lang['MAIL_SUBJECT']='Tema';
$lang['MAIL_MESSAGE']='Mensaje';
$lang['MAIL_REPLYTO']='Responder A';
$lang['MAIL_EMPTYINPUT']='Parece que no introduzca un mensaje que se enviará. Por favor, volver atrás e introducir un mensaje!';
$lang['MAIL_INPUTLNEGTH']='Parecería que está intentando enviar un mensaje muy largo. Recuerde que los mensajes sólo pueden ser 65,655 caracteres de longitud, y los sujetos sólo pueden ser 50 caracteres de longitud.';
$lang['MAIL_NOUSER']='Debe introducir un destinatario de este mensaje! Volver y probar otra vez!';
$lang['MAIL_UDNE']='¡El usuario no existe!';
$lang['MAIL_UDNE_TEXT']='Usted está intentando enviar un mensaje a un usuario que no existe. Comprobar su origen y vuelve a intentarlo.';
$lang['MAIL_SUCCESS']='Has enviado con éxito un mensaje!';
$lang['MAIL_TIMEERROR']='Debe esperar 60 segundos antes de poder enviar un mensaje a este usuario utilizando este formulario específicamente. Si usted necesita para responder rápidamente a alguien, aún puede utilizar el sistema de correo normal.';
$lang['MAIL_READALL']='Todos los mensajes no leídos se ha marcado como leído!';
$lang['MAIL_DELETECONFIRM']='¿Estás 100% seguro de que desea vaciar la bandeja de entrada? Esto no se puede deshacer.';
$lang['MAIL_DELETEYES']='Sí, estoy 100% seguro';
$lang['MAIL_DELETENO']='Un momento, pensándolo';
$lang['MAIL_DELETEDONE']='Toda la bandeja de entrada se ha limpiado correctamente.';
$lang['MAIL_QUICKREPLY']='Enviar una respuesta rápida ...';
$lang['MAIL_MARKREAD']='Marcar todo como leido';
$lang['MAIL_SENDMSG']='Enviar mensaje';

//Language menu
$lang['LANG_INTRO']='Aquí usted puede cambiar de idioma. Esto no se guarda en su cuenta. Esto se guarda a través de una cookie. Si cambia los dispositivos o secar sus cookies, tendrá que restablecer su idioma nuevo. Las traducciones pueden no ser fiable al 100%.';
$lang['LANG_BUTTON']='Cambiar idioma';
$lang['LANG_UPDATE']='Idioma Actualizado!';
$lang['LANG_UPDATE2']='Ha actualizado correctamente su idioma!';

//Notifications page
$lang['NOTIF_TABLE_HEADER1']='Notificaciones de Información';
$lang['NOTIF_TABLE_HEADER2']='Notificaciones de texto';
$lang['NOTIF_DELETE_SINGLE']='Ha eliminado con éxito una notificación.';
$lang['NOTIF_DELETE_SINGLE_FAIL']='No se puede eliminar esta notificación, ya que o bien no existe o no pertenece a usted.';
$lang['NOTIF_TITLE']='Últimas notificaciones fifthteen pertenecientes a usted ...';
$lang['NOTIF_READ']='Notificación de lectura';
$lang['NOTIF_UNREAD']='Sin notificación';
$lang['NOTIF_DELETE']='Eliminar Notificación';

//Bank
$lang['BANK_BUY1']='Abrir una cuenta de banco hoy en día, justo ';
$lang['BANK_BUYYES']='¡Inscríbeme!';
$lang['BANK_SUCCESS']="Felicitaciones, usted compraron una cuenta bancaria para";
$lang['BANK_SUCCESS1']='Empezar a usar mi cuenta!';
$lang['BANK_FAIL']="No tienes suficiente {$lang['INDEX_PRIMCURR']} para comprar una cuenta bancaria. Volver más tarde cuando se tiene suficiente. Necesitas ";
$lang['BANK_HOME']="Tu Actualmente tienes ";
$lang['BANK_HOME1']=" en el banco de bajo nivel.";
$lang['BANK_HOME2']="Al final de cada día, su saldo bancario se incrementará en un 2%.";
$lang['BANK_DEPOSIT_WARNING']="Te costará";
$lang['BANK_DEPOSITE_WARNING1']=" del dinero depositado, redondeando hacia arriba. La cuota máxima es de ";
$lang['BANK_AMOUNT']="Cantidad:";
$lang['BANK_DEPOSIT']="Depositar";
$lang['BANK_WITHDRAW_WARNING']="Por suerte para ti, no hay ningún cargo sobre los retiros.";
$lang['BANK_WITHDRAW']="Retirar";
$lang['BANK_D_ERROR']="Usted está tratando de depositar dinero que no tienen ni siquiera!";
$lang['BANK_D_SUCCESS']="De entregar ";
$lang['BANK_D_SUCCESS1']=" a depositar. Después de la cuota (";
$lang['BANK_D_SUCCESS2']=") se toma, ";
$lang['BANK_D_SUCCESS3']=" se añade a su cuenta bancaria. <b> Ahora tiene ";
$lang['BANK_D_SUCCESS4']=" en tu cuenta.</b>";
$lang['BANK_W_FAIL']="Está intentando retirar más {$lang['INDEX_PRIMCURR']} de los que tiene actualmente en el banco.";
$lang['BANK_W_SUCCESS']="Se retiró con éxito";
$lang['BANK_W_SUCCESS1']="De su cuenta bancaria. Tienes";
$lang['BANK_W_SUCCESS2']="Izquierda en su cuenta bancaria.";

//Forums
$lang['FORUM_EMPTY_REPLY']="Usted está tratando de presentar una respuesta vacía, lo que no se puede hacer! Por favor asegúrese de que ha rellenado el formulario de respuesta!";
$lang['FORUM_TOPIC_DNE_TITLE']="Inexistente Tema!";
$lang['FORUM_TOPIC_DNE_TEXT']="Usted está tratando de interactuar con un tema que no existe. Comprobar su origen y vuelve a intentarlo.";
$lang['FORUM_FORUM_DNE_TITLE']="Foro inexistente!";
$lang['FORUM_FORUM_DNE_TEXT']="Usted está tratando de interactuar con un foro que no existe. Comprobar su origen y vuelve a intentarlo.";
$lang['FORUM_POST_DNE_TITLE']="Inexistente Poste!";
$lang['FORUM_POST_DNE_TEXT']="Usted está tratando de interactuar con un puesto que no existe. Comprobar su origen y vuelve a intentarlo.";
$lang['FORUM_NOPERMISSION']="Usted está tratando de interactuar con un foro que no tienes permiso para interactuar con. Si esto es un error, por favor avisar a un administrador de inmediato!";
$lang['FORUM_FORUMS']="Foros";
$lang['FORUM_ON']="En";
$lang['FORUM_IN']="En:";
$lang['FORUM_BY']="Por:";
$lang['FORUM_STAFFONLY']="Sólo Personal";
$lang['FORUM_F_LP']="Última publicación";
$lang['FORUM_F_TC']="Conde tema";
$lang['FORUM_F_PC']="Recuento de entradas";
$lang['FORUM_F_FN']="Nombre del Foro";
$lang['FORUM_FORUMSHOME']="Inicio de los foros";
$lang['FORUM_TOPICNAME']="Nombre del tema";
$lang['FORUM_TOPICOPEN']="Tema abierto";
$lang['FORUM_TOPIC_MOVE']="Mover Tema";
$lang['FORUM_PAGES']="Páginas:";
$lang['FORUM_TOPIC_MTT']="Para mover Tema:";
$lang['FORUM_TOPIC_PIN']="Pin/Desanclar Tema";
$lang['FORUM_TOPIC_LOCK']="Bloqueo/Desbloqueo Tema";
$lang['FORUM_TOPIC_DELETE']="Eliminar Tema";
$lang['FORUM_POST_EDIT']="Editar post";
$lang['FORUM_POST_QUOTE']="Citar";
$lang['FORUM_POST_DELETE']="Eliminar mensaje";
$lang['FORUM_POST_EDIT_1']="Este mensaje fue editado por última";
$lang['FORUM_NOSIG']="Sin firma";
$lang['FORUM_POST_POSTED']="En replicó:";
$lang['FORUM_POST_POST']='Enviar';
$lang['FORUM_POST_REPLY']='Enviar respuesta';
$lang['FORUM_POST_REPLY2']='Enviar respuesta al tema';
$lang['FORUM_POST_REPLY_INFO']='Ingrese su respuesta aquí. Recuerde que puede usar BBCode! Por favor asegúrese de que no va a romper las reglas de juego de enviar el mensaje.';
$lang['FORUM_POST_TIL']='Este tema está cerrado, y debido a esto, no se puede enviar una respuesta a este tema.';
$lang['FORUM_MAX_CHAR_REPLY']="Al publicar en el foro, la entrada sólo puede contener 65.535 caracteres como máximo. Volver y probar otra vez!";
$lang['FORUM_REPLY_SUCCESS']="Usted ha publicado con éxito su respuesta a este tema.";
$lang['FORUM_TOPIC_FORM_TITLE']="Nombre del tema";
$lang['FORUM_TOPIC_FORM_DESC']="Tema Descripción";
$lang['FORUM_TOPIC_FORM_TEXT']="Texto tema";
$lang['FORUM_TOPIC_FORM_BUTTON']="Mensaje Tema";
$lang['FORUM_TOPIC_FORM_TITLE_LENGTH']="Nombres de los temas y descripciones sólo pueden ser de 255 caracteres de longitud, como máximo.";
$lang['FORUM_TOPIC_FORM_PAGE']="Nuevo formulario Tema";
$lang['FORUM_TOPIC_FORM_SUCCESS']="Usted ha publicado con éxito un nuevo tema en los foros!";
$lang['FORUM_QUOTE_FORM_PAGENAME']="Citando un Post";
$lang['FORUM_QUOTE_FORM_INFO']="Citando un Post...";
$lang['FORUM_EDIT_FORM_INFO']="Edición de un mensaje...";
$lang['FORUM_EDIT_FORM_PAGENAME']="Edición de un mensaje";
$lang['FORUM_EDIT_NOPERMISSION']="No tiene permiso para editar este post. Si usted cree que esto es incorrecto, por favor deje un administrador sabe lo antes posible!";
$lang['FORUM_EDIT_FORM_SUBMIT']="Editar post";
$lang['FORUM_EDIT_SUCCESS']="Ha editado con éxito un mensaje!";
$lang['FORUM_MOVE_TOPIC_DFDNE']="Usted está tratando de mover un tema a un foro que no existe. Volver y probar otra vez, por favor.";
$lang['FORUM_MOVE_TOPIC_DONE']="Se ha mudado con éxito el tema.";

//Send Cash Form
$lang['SCF_POSCASH']="Es necesario enviar al menos 1 {$lang['INDEX_PRIMCURR']} para utilizar esta forma.";
$lang['SCF_UNE']="No se puede enviar {$lang['INDEX_PRIMCURR']} a un usuario inexistente!";
$lang['SCF_NEC']="Usted está intentando enviar más {$lang['INDEX_PRIMCURR']} lo que actualmente tiene!";
$lang['SCF_SUCCESS']="{$lang['INDEX_PRIMCURR']} enviado correctamente.";

//Profile
$lang['PROFILE_UNF']="No pudimos encontrar un usuario con el ID de usuario que ha introducido. Usted podría estar recibiendo este mensaje porque el jugador que está tratando de vista se borraron. Compruebe que su fuente de nuevo!";
$lang['PROFILE_PROFOR']="Perfil Para";
$lang['PROFILE_LOCATION']="Ubicación:";
$lang['PROFILE_GUILD']="Gremio";
$lang['PROFILE_PI']="Información Física";
$lang['PROFILE_ACTION']="Comportamiento";
$lang['PROFILE_FINANCIAL']="Información financiera";
$lang['PROFILE_STAFF']="Área Personal de";
$lang['PROFILE_REGISTERED']="Registrado";
$lang['PROFILE_ACTIVE']="Último Activo";
$lang['PROFILE_LOGIN']="Último acceso";
$lang['PROFILE_AGE']="Años";
$lang['PROFILE_DAYS_OLD']="días de edad.";
$lang['PROFILE_REF']="Referencias";
$lang['PROFILE_FRI']="Amigos";
$lang['PROFILE_ENE']="Enemigos";

//Equip Items
$lang['EQUIP_NOITEM']="El elemento no se puede encontrar, y como resultado, no se puede equiparla.";
$lang['EQUIP_NOITEM_TITLE']="Elemento no existe!";
$lang['EQUIP_NOTWEAPON']="El artículo que usted está tratando de equipar no puede estar equipado como un arma.";
$lang['EQUIP_NOTWEAPON_TITLE']="Arma no válido!";
$lang['EQUIP_NOSLOT']="Usted está tratando de dotar a este elemento a una ranura no válida o no existe.";
$lang['EQUIP_NOSLOT_TITLE']="Ranura de equipo no válida!";
$lang['EQUIP_WEAPON_SUCCESS1']="Usted ha equipado con éxito";
$lang['EQUIP_WEAPON_SUCCESS2']="como tu";
$lang['EQUIP_WEAPON_SLOT1']='Arma primaria';
$lang['EQUIP_WEAPON_SLOT2']='Arma secundaria';
$lang['EQUIP_WEAPON_SLOT3']='Armadura';
$lang['EQUIP_WEAPON_TITLE']="Equipar un arma";
$lang['EQUIP_WEAPON_TEXT_FORM_1']="Por favor, seleccione el punto que desea equipar su";
$lang['EQUIP_WEAPON_TEXT_FORM_2']="a. Si ya está sosteniendo un arma en la ranura que elija, se trasladó de nuevo a su inventario.";
$lang['EQUIP_WEAPON_EQUIPAS']="Como equipar";
$lang['EQUIP_ARMOR_TITLE']="Armadura de armadura";
$lang['EQUIP_ARMOR_TEXT_FORM_1']="Estás intentando equipar tu ";
$lang['EQUIP_ARMOR_TEXT_FORM_2']="A su ranura de la armadura. Si ya está usando armadura, se moverá de nuevo a su inventario.";
$lang['EQUIP_NOTARMOR']="El artículo que intenta equipar no puede equiparse como armadura.";
$lang['EQUIP_NOTARMOR_TITLE']="¡Armadura no válida!";
$lang['EQUIP_OFF_ERROR1']="Está intentando descomponer un elemento de una ranura inexistente.";
$lang['EQUIP_OFF_ERROR2']="No tiene un elemento en esa ranura.";
$lang['EQUIP_OFF_SUCCESS']="Ha desagregado exitosamente el elemento de su";
$lang['EQUIP_OFF_SUCCESS1']="espacio.";

//Polling Staff
$lang['STAFF_POLL_TITLE']="Administración de votación";
$lang['STAFF_POLL_TITLES']="Iniciar una encuesta";
$lang['STAFF_POLL_TITLEE']="Finalizar una encuesta";
$lang['STAFF_POLL_START_INFO']="Hacer una pregunta, a continuación, dar algunas respuestas posibles.";
$lang['STAFF_POLL_START_CHOICE']="Opción #";
$lang['STAFF_POLL_START_QUESTION']="Pregunta";
$lang['STAFF_POLL_START_HIDE']="Ocultar resultados hasta el final de la encuesta?";
$lang['STAFF_POLL_START_BUTTON']="Crear Encuesta";
$lang['STAFF_POLL_START_ERROR']="Usted necesita tener una pregunta, y al menos dos respuestas!";
$lang['STAFF_POLL_START_SUCCESS']="Ha abierto con éxito una encuesta para el juego.";
$lang['STAFF_POLL_END_SUCCESS']="Ha cerrado con éxito una encuesta activa.";
$lang['STAFF_POLL_END_FORM']="Por favor, seleccione el sondeo que desea cerrar.";
$lang['STAFF_POLL_END_BTN']="Cerrar seleccionada encuesta";
$lang['STAFF_POLL_END_ERR']="Usted está tratando de cerrar una encuesta inexistente.";

//Polling
$lang['POLL_TITLE']="Cabina electoral";
$lang['POLL_CYV']="Emitir su voto hoy!";
$lang['POLL_VOP']="Ver Encuestas previamente abierto";
$lang['POLL_AVITP']="Sólo se puede votar una vez por encuesta.";
$lang['POLL_PCNT']="No se puede votar en una encuesta que no existe, o ha sido cerrado previamente.";
$lang['POLL_VOTE_SUCCESS']="Usted ha fundido con éxito su voto en esta encuesta.";
$lang['POLL_VOTE_NOPOLL']="No hay encuestas abiertas en este momento. Vuelve mas tarde.";
$lang['POLL_VOTE_CHOICE']="Elección";
$lang['POLL_VOTE_VOTES']="Votos";
$lang['POLL_VOTE_PERCENT_VOTES']="Porcentaje";
$lang['POLL_VOTE_AV']="(¡Ya votado!)";
$lang['POLL_VOTE_NV']="(¡No ha votado!)";
$lang['POLL_VOTE_HIDDEN']="Los resultados de esta encuesta están ocultas hasta su fin.";
$lang['POLL_VOTE_QUESTION']="Pregunta:";
$lang['POLL_VOTE_YVOTE']="Tu Voto:";
$lang['POLL_VOTE_TVOTE']="Total de votos:";
$lang['POLL_VOTE_VOTEC']="Escoger";
$lang['POLL_VOTE_CAST']="Voto emitido";
$lang['POLL_VOTE_NOCLOSED']="No hay encuestas cerradas en este momento. Volver más tarde cuando el personal se cierran una encuesta.";

//Forum Staff
$lang['STAFF_FORUM_ADD']="Añadir Categoría Foro";
$lang['STAFF_FORUM_EDIT']="Edición Foro Categoría";
$lang['STAFF_FORUM_DEL']="Eliminar Categoría Foro";
$lang['STAFF_FORUM_ADD_NAME']="Nombre del Foro";
$lang['STAFF_FORUM_ADD_DESC']="Descripción foro";
$lang['STAFF_FORUM_ADD_AUTHORIZE']="Autorización";
$lang['STAFF_FORUM_ADD_AUTHORIZEP']="Público";
$lang['STAFF_FORUM_ADD_AUTHORIZES']="Sólo Personal";
$lang['STAFF_FORUM_ADD_BTN']="Crear Foro";
$lang['STAFF_FORUM_ADD_ERRNAME']="La entrada de nombre del foro era válida o vacía. Por favor, vuelva a comprobar y probar de nuevo.";
$lang['STAFF_FORUM_ADD_ERRDESC']="La entrada Descripción foro fue válida o vacía. Por favor, vuelva a comprobar y probar de nuevo.";
$lang['STAFF_FORUM_ADD_ERRNIU']="El nombre del foro ha elegido ya está en uso. Por favor, inténtelo de nuevo con un nuevo nombre.";
$lang['STAFF_FORUM_ADD_SUCCESS']="Ha agregado una categoría de foro para el juego.";
$lang['STAFF_FORUM_EDIT_ERRINV']="Se ha especificado un ID válido foro. Inténtalo de nuevo.";
$lang['STAFF_FORUM_EDIT_BTN']="Edición Foro";
$lang['STAFF_FORUM_EDIT_ERREMPTY']="Una o más entradas de la página anterior está vacía. Por favor, rellena el formulario y vuelve a intentarlo.";
$lang['STAFF_FORUM_EDIT_SUCCESS']="Ha editado con éxito el foro.";
$lang['STAFF_FORUM_DEL_BTN']="Eliminar Foro";
$lang['STAFF_FORUM_DEL_INFO']="Eliminación de foros son permanentes. Esto también eliminará los mensajes dentro de ellos también.";
$lang['STAFF_FORUM_EDIT_ERRFDNE']="El foro ha decidido eliminar no existe. Volver atrás y comprobar y volver a intentarlo.";
$lang['STAFF_FORUM_DEL_SUCCESS']="Se ha eliminado el foro, junto con cualesquiera que sean los temas y mensajes estaban en ellos con anterioridad.";

//Item Use
$lang['IU_UI']="Está intentando utilizar un elemento no especificado. Revisa tu enlace y vuelve a intentarlo!";
$lang['IU_UNUSED_ITEM']="Este elemento no está configurado para utilizarse. No puede utilizar elementos con un uso configurado.";
$lang['IU_ITEM_NOEXIST']="El elemento que intenta utilizar no existe. Compruebe sus fuentes y vuelva a intentarlo.";
$lang['IU_SUCCESS']="hHa sido utilizado con éxito. Actualizar para que los cambios surtan efecto.";

//Staff items
$lang['STAFF_ITEM_GIVE_TITLE']="Dar un artículo al usuario";
$lang['STAFF_ITEM_GIVE_FORM_USER']="Usuario";
$lang['STAFF_ITEM_GIVE_FORM_ITEM']="ít";
$lang['STAFF_ITEM_GIVE_FORM_QTY']="Cantidad";
$lang['STAFF_ITEM_GIVE_FORM_BTN']="Dar un artículo";
$lang['STAFF_ITEM_GIVE_SUB_NOITEM']="No especificó el elemento que desea dar al usuario.";
$lang['STAFF_ITEM_GIVE_SUB_NOQTY']="No especificó la cantidad del elemento que desea dar al usuario.";
$lang['STAFF_ITEM_GIVE_SUB_NOUSER']="No especificó el usuario al que desea asignar un elemento.";
$lang['STAFF_ITEM_GIVE_SUB_ITEMDNE']="El elemento que intenta regalar no existe.";
$lang['STAFF_ITEM_GIVE_SUB_USERDNE']="El usuario al que intenta dar un elemento no existe.";
$lang['STAFF_ITEM_GIVE_SUB_SUCCESS']="Los artículos han sido dotados con éxito.";

//Staff Crimes
$lang['STAFF_CRIME_TITLE']="Crímenes";
$lang['STAFF_CRIME_MENU_CREATE']="Crear crimen";
$lang['STAFF_CRIME_MENU_CREATECG']="Crear grupo de crimen";
$lang['STAFF_CRIME_NEW_TITLE']="Añadir un nuevo crimen.";
$lang['STAFF_CRIME_NEW_NAME']="Nombre del crimen";
$lang['STAFF_CRIME_NEW_BRAVECOST']="Costo de la valentía";
$lang['STAFF_CRIME_NEW_SUCFOR']="Fórmula de éxito";
$lang['STAFF_CRIME_NEW_SUCPRIMIN']="Éxito Mínimo {$lang['INDEX_PRIMCURR']}";
$lang['STAFF_CRIME_NEW_SUCPRIMAX']="Máximo éxito {$lang['INDEX_PRIMCURR']}";
$lang['STAFF_CRIME_NEW_SUCSECMIN']="Máximo éxito {$lang['INDEX_SECCURR']}";
$lang['STAFF_CRIME_NEW_SUCSECMAX']="Máximo éxito {$lang['INDEX_SECCURR']}";
$lang['STAFF_CRIME_NEW_SUCITEM']="Artículo de éxito";
$lang['STAFF_CRIME_NEW_GROUP']="Grupo Del Delito";
$lang['STAFF_CRIME_NEW_ITEXT']="Texto inicial";
$lang['STAFF_CRIME_NEW_ITEXT_PH']="El texto que se muestra al iniciar el crimen.";
$lang['STAFF_CRIME_NEW_STEXT']="Texto de éxito";
$lang['STAFF_CRIME_NEW_STEXT_PH']="El texto que se muestra si el jugador logra cometer el crimen.";
$lang['STAFF_CRIME_NEW_JTEXT']="Texto de error";
$lang['STAFF_CRIME_NEW_JTEXT_PH']="El texto que se muestra si el jugador no comete el crimen.";
$lang['STAFF_CRIME_NEW_JTIMEMIN']="Tiempo mínimo de la mazmorra";
$lang['STAFF_CRIME_NEW_JTIMEMAX']="Tiempo máximo de la mazmorra";
$lang['STAFF_CRIME_NEW_JREASON']="Razón de la mazmorra";
$lang['STAFF_CRIME_NEW_XP']="Experiencia de éxito";
$lang['STAFF_CRIME_NEW_BTN']="Crear crimen";
$lang['STAFF_CRIME_NEW_FAIL1']="Falta una de las entradas requeridas del formulario anterior.";
$lang['STAFF_CRIME_NEW_FAIL2']="El elemento que ha elegido no parece existir en el juego. Seleccione un elemento nuevo.";
$lang['STAFF_CRIME_NEW_SUCCESS']="Has añadido con éxito un crimen al juego.";
$lang['STAFF_CRIMEG_NEW_TITLE']="Agregar un nuevo Grupo de Delitos.";
$lang['STAFF_CRIMEG_NEW_NAME']="Nombre del grupo de crimen";
$lang['STAFF_CRIMEG_NEW_ORDER']="Orden del grupo criminal";
$lang['STAFF_CRIMEG_NEW_BTN']="Crear grupo de crimen";
$lang['STAFF_CRIMEG_NEW_FAIL1']="Al menos una de las dos entradas del formulario anterior está vacía. Vuelve y corrige eso, por favor.";
$lang['STAFF_CRIMEG_NEW_FAIL2']="No puedes tener grupos de crimen que compartan valores de orden.";
$lang['STAFF_CRIMEG_NEW_SUCCESS']="Ha creado con éxito un grupo de delincuencia.";
?>