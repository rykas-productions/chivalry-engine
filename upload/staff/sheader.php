<?php

/*
	File: staff/sheader.php
	Created: 6/1/2016 at 6:06PM Eastern Time
	Info: Loads the template, CSS, JS, etc. inside the staff panel.
	Author: TheMasterGeneral
	Website: https://github.com/MasterGeneral156/chivalry-engine/
*/

class headers
{
    function startheaders()
    {
        global $ir, $set, $h, $db, $menuhide, $userid, $api, $time;
        ?>
        <!DOCTYPE html>
        <html lang="en">
        <head>
            <center>
                <meta charset="utf-8">
                <meta http-equiv="X-UA-Compatible" content="IE=edge">
                <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
                <meta name="description" content="<?php echo $set['Website_Description']; ?>">
                <meta property="og:title" content="<?php echo $set['WebsiteName']; ?>"/>
                <meta property="og:description" content="<?php echo $set['Website_Description']; ?>"/>
                <meta property="og:image" content=""/>
                <link rel="shortcut icon" href="" type="image/x-icon"/>
                <!-- CSS -->
                <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-sRIl4kxILFvY47J16cr9ZwB07vP4J8+LH7qKQnuqkuIAvNWLzeN8tE5YBujZqJLB" crossorigin="anonymous">
                <meta name="theme-color" content="#e7e7e7">
                <meta name="author" content="<?php echo $set['WebsiteOwner']; ?>">
                <?php echo "<title>{$set['WebsiteName']}</title>"; ?>
        </head>
        <?php
        if (empty($menuhide)) {
            $ir['mail'] = $db->fetch_single($db->query("SELECT COUNT(`mail_id`) FROM `mail` WHERE `mail_to` = {$ir['userid']} AND `mail_status` = 'unread'"));
            $ir['notifications'] = $db->fetch_single($db->query("SELECT COUNT(`notif_id`) FROM `notifications` WHERE `notif_user` = {$ir['userid']} AND `notif_status` = 'unread'"));
            ?>
            <body>
            <!-- Navigation -->
            <nav class="navbar navbar-expand-lg navbar-light bg-light">
                <div class="container-fluid">
                    <a class="navbar-brand" href="index.php"><?php echo $set['WebsiteName']; ?></a>
                    <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#CENGINENav"
                            aria-controls="CENGINENav" aria-expanded="false" aria-label="Toggle navigation">
                        <span class="navbar-toggler-icon"></span>
                    </button>
                    <div class="collapse navbar-collapse" id="CENGINENav">
                        <ul class="navbar-nav me-auto mb-2 mb-lg-0">
                            <li class="nav-item">
                                <a class="nav-link" href="../index.php"><?php echo "Back to Game"; ?></a>
                            </li>
                        </ul>
                        <div class="d-flex">
                            <ul class="navbar-nav">
                                <li class="nav-item">
                                    <a class="nav-link" href="../inbox.php">
                                        <?php echo "Inbox <span class='badge bg-primary rounded-pill'>{$ir['mail']}</span>"; ?>
                                    </a>
                                </li>
                                <li class="nav-item">
                                    <a class="nav-link" href="../notifications.php">
                                        <?php echo "Notifications <span class='badge bg-primary rounded-pill'>{$ir['notifications']}</span>"; ?>
                                    </a>
                                </li>
                                <li class="nav-item">
                                    <a class="nav-link" href="../inventory.php"><?php echo "Inventory"; ?></a>
                                </li>
                                <li class="nav-item dropdown">
                                    <a class="nav-link dropdown-toggle" href="#" id="navbarDropdown" role="button"
                                       data-bs-toggle="dropdown" aria-expanded="false">
                                        <?php
                                        if ($ir['display_pic']) {
                                            echo "<img src='{$ir['display_pic']}' width='24' height='24'>";
                                        }
                                        echo " Hello, {$ir['username']}!";
                                        ?>
                                    </a>
                                    <ul class="dropdown-menu dropdown-menu-end" aria-labelledby="navbarDropdown">
                                        <li>
                                            <a class="dropdown-item" href="../profile.php?user=<?php echo "{$ir['userid']}"; ?>">
                                                <i class="fa fa-user"></i> <?php echo "Profile"; ?>
                                            </a>
                                        </li>
                                        <li>
                                            <a class="dropdown-item" href="../preferences.php?action=menu">
                                                <i class="fa fa-gear"></i> <?php echo "Preferences"; ?>
                                            </a>
                                        </li>
                                        <?php
                                        if (in_array($ir['user_level'], array('Admin', 'Forum Moderator', 'Web Developer', 'Assistant'))) {
                                            echo '<li><hr class="dropdown-divider"></li>';
                                            echo '<li>
                                                <a class="dropdown-item" href="index.php">
                                                    <i class="fa fa-terminal"></i> Staff Panel
                                                </a>
                                              </li>';
                                        }
                                        ?>
                                        <li><hr class="dropdown-divider"></li>
                                        <li>
                                            <a class="dropdown-item" href="../gamerules.php">
                                                <i class="fa fa-server"></i> <?php echo "Game Rules"; ?>
                                            </a>
                                        </li>
                                        <li>
                                            <a class="dropdown-item" href="../logout.php">
                                                <i class="fa fa-power-off"></i> <?php echo "Log Out"; ?>
                                            </a>
                                        </li>
                                    </ul>
                                </li>
                            </ul>
                        </div>
                    </div>
                </div>
            </nav>

            <!-- Page Content -->
            <div class="container">
            <div class="row">
            <div class="col-sm-12 text-center">
            <noscript>
                <?php alert('info', "Information!", "Please enable Javascript.", false); ?>
            </noscript>
            <?php
            $IP = $db->escape($_SERVER['REMOTE_ADDR']);
            $ipq = $db->query("SELECT `ip_id` FROM `ipban` WHERE `ip_ip` = '{$IP}'");
            if ($db->num_rows($ipq) > 0) {
                alert('danger', "Uh Oh!", "You have been IP Banned. Please contact support.", false);
                die($h->endpage());
            }
            $fed = $db->query("/*qc=on*/SELECT * FROM `fedjail`");
            //User's federal jail sentence is completed. Let them play again.
            if ($db->num_rows($fed) > 0)
            {
                $fd = $db->fetch_row($fed);
                if ($fd['fed_out'] < $time) 
                {
                    $db->query("UPDATE `users` SET `fedjail` = 0");
                    $db->query("DELETE FROM `fedjail` WHERE `fed_out` < {$time}");
                }
            }
            if ($ir['fedjail'] > 0) {
                alert('info', "Federal Dungeon!", "You have been placed in the Federal Dungeon for
                        " . TimeUntil_Parse($fed['fed_out']) . " You are in for the crime of <b>{$fed['fed_reason']}</b>", false);
                die($h->endpage());
            }
            if ($ir['mail'] > 0) {
                alert('info', "New Mail!", "You have {$ir['mail']} unread messages.", true, "../inbox.php", "View Inbox");
            }
            if ($ir['notifications'] > 0) {
                alert('info', "New Notifications!", "You have {$ir['notifications']} unread notifications.", true, '../notifications.php', "View Notifications");
            }
            if ($ir['announcements'] > 0) {
                alert('info', "New Announcements!", "You have {$ir['announcements']} unread announcements.", true, '../announcements.php', "View Announcements");
            }
            if ($api->UserStatus($ir['userid'], 'infirmary') == true) {
                $InfirmaryOut = $db->fetch_single($db->query("SELECT `infirmary_out` FROM `infirmary` WHERE `infirmary_user` = {$ir['userid']}"));
                $InfirmaryRemain = TimeUntil_Parse($InfirmaryOut);
                alert('info', "Unconscious!", "You are in the infirmary for the next {$InfirmaryRemain}.", true, '../inventory', 'View Inventory');
            }
            if ($api->UserStatus($ir['userid'], 'dungeon') == true) {
                $DungeonOut = $db->fetch_single($db->query("SELECT `dungeon_out` FROM `dungeon` WHERE `dungeon_user` = {$ir['userid']}"));
                $DungeonRemain = TimeUntil_Parse($DungeonOut);
                alert('info', "Locked Up!", "You are in the dungeon for the next {$DungeonRemain}.", true, '../inventory', 'View Inventory');
            }
            date_default_timezone_set($ir['timezone']);
        }
    }

    function userdata($ir, $dosessh = 1)
    {
        global $db, $userid;;
        $IP = $db->escape($_SERVER['REMOTE_ADDR']);
        $db->query("UPDATE `users` SET `laston` = {$_SERVER['REQUEST_TIME']}, `lastip` = '{$IP}'  WHERE `userid` = {$userid}");
        if (!$ir['email']) {
            global $domain;
            die("<body>Your account is likely broken. Please contact admin@{$domain} and include your User ID.");
        }
        if (!isset($_SESSION['attacking'])) {
            $_SESSION['attacking'] = 0;
        }
        if ($dosessh && ($_SESSION['attacking'] || $ir['attacking'])) {
            $hosptime = Random(10, 50);
            $api->UserStatusSet($userid, 'infirmary', $hosptime, "Ran from a fight");
            alert("warning", "Uh Oh!", "For leaving your previous fight, you were placed in the Infirmary for {$hosptime}
            minutes, and lost all your experience.", false);
            $db->query("UPDATE `users` SET `xp` = 0, `attacking` = 0 WHERE `userid` = $userid");
            $_SESSION['attacking'] = 0;
        }
    }

    function endpage()
    {
        global $db, $ir, $set;
        $query_extra = '';
    if (isset($_GET['mysqldebug']) && $ir['user_level'] == 'Admin')
    {
        ?>
        <pre class='pre-scrollable'> <?php var_dump($db->queries) ?> </pre> <?php
    }
    ?>
        </div>
        </div>
        <!-- /.row -->

        </div>
        <!-- /.container -->
        <link rel="stylesheet" href="https://code.jquery.com/ui/1.11.4/themes/smoothness/jquery-ui.min.css">
        <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.2/css/all.min.css">
        <link rel="stylesheet" href="../css/game.css">

        <!-- jQuery Version 3.3.1 -->
        <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.3.1/jquery.min.js"></script>

        <!-- Bootstrap Core JavaScript -->
        <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.11.8/dist/umd/popper.min.js" integrity="sha384-I7E8VVD/ismYTF4hNIPjVp/Zjvgyol6VFvRkX/vR+Vc4jQkC+hVqc2pM8ODewa9r" crossorigin="anonymous"></script>
        <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/js/bootstrap.min.js" integrity="sha384-G/EV+4j2dNv+tEPo3++6LCgdCROaejBqfUeNjuKAiuXbjrxilcCdDz6ZAVfHWe1Y" crossorigin="anonymous"></script>
        <!-- Removed bootstrap-hover-tabs as it's not needed in Bootstrap 5 -->

        <!-- Other JavaScript -->
        <script src="../js/game.js" async defer></script>
        <script src='https://www.google.com/recaptcha/api.js' async defer></script>
        </body>
        <footer>
            <p>
                <br/>
                <?php
                echo "<hr />
					Time is now " . date('F j, Y') . " " . date('g:i:s a') . "<br />
					{$set['WebsiteName']} &copy; " . date("Y") . " {$set['WebsiteOwner']}.";
                if ($ir['user_level'] == 'Admin' || $ir['user_level'] == 'Web Developer')
                    echo "<br/>{$db->num_queries} Queries Executed.{$query_extra}<br />";
                ?>
            </p>
        </footer>
        </html>
    <?php
    }
}
