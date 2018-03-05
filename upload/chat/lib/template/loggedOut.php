<?xml version="1.0" encoding="[CONTENT_ENCODING/]"?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="[LANG_CODE/]" lang="[LANG_CODE/]" dir="[BASE_DIRECTION/]">

<head>
	<meta name="viewport" content="width=device-width, initial-scale=1" />
	<meta http-equiv="Content-Type" content="[CONTENT_TYPE/]" />
	<title>Chivalry is Dead Chat</title>
	[STYLE_SHEETS/]
	<script src="js/chat.js" type="text/javascript" charset="UTF-8"></script>
	<script src="js/lang/[LANG_CODE/].js" type="text/javascript" charset="UTF-8"></script>
	<script src="js/config.js" type="text/javascript" charset="UTF-8"></script>
	<script type="text/javascript">
		// <![CDATA[
			function initializeLoginPage() {
				document.getElementById('userNameField').focus();
				if(!ajaxChat.isCookieEnabled()) {
					var node = document.createElement('div'),
						text = document.createTextNode(ajaxChatLang['errorCookiesRequired']);
					node.appendChild(text);
					document.getElementById('errorContainer').appendChild(node);
				}
			}
			
			ajaxChatConfig.sessionName = '[SESSION_NAME/]';
			ajaxChatConfig.cookieExpiration = parseInt('[COOKIE_EXPIRATION/]');
			ajaxChatConfig.cookiePath = '[COOKIE_PATH/]';
			ajaxChatConfig.cookieDomain = '[COOKIE_DOMAIN/]';
			ajaxChatConfig.cookieSecure = '[COOKIE_SECURE/]';

			ajaxChat.init(ajaxChatConfig, ajaxChatLang, true, true, false);
		// ]]>
	</script>
</head>
<body class="ajax-chat" onload="initializeLoginPage();">
	<div id="loginContent">
		<h1 id="loginHeadline">[LANG]title[/LANG]</h1>
		<div id="errorContainer">[ERROR_MESSAGES/]<noscript><div>[LANG]requiresJavaScript[/LANG]</div></noscript></div>
		<form id="loginForm" action="[LOGIN_URL/]" method="post" enctype="application/x-www-form-urlencoded">
			<input type="hidden" name="login" id="loginField" value="login"/>
			<input type="hidden" name="redirect" id="redirectField" value="[REDIRECT_URL/]"/>
			<div><label for="userNameField">[LANG]userName[/LANG]:</label><br />
			<input type="text" name="userName" id="userNameField" maxlength="[USER_NAME_MAX_LENGTH/]"/></div>
			<div><label for="passwordField">[LANG]password[/LANG]*:</label><br />
			<input type="password" name="password" id="passwordField"/></div>
			<div><input type="submit" name="submit" id="loginButton" value="[LANG]login[/LANG]"/></div>
		</form>
        <br /><a href='../index.php'>Return to the game</a>
	</div>
</body>
</html>