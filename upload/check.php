<?php
if (isset($_SERVER['REQUEST_METHOD']) && is_string($_SERVER['REQUEST_METHOD']))
{
    if (strtoupper($_SERVER['REQUEST_METHOD']) !== 'POST')
    {
        // Ignore a GET request
        header('HTTP/1.1 400 Bad Request');
        exit;
    }
}
require_once('global_func.php');
if (!is_ajax())
{
    header('HTTP/1.1 400 Bad Request');
    exit;
}
if (!isset($_POST['password']))
{ // If they are trying to view this without ?password=password.
    die("Whats this document for?"); // Lawl what is this doccument for anyways?
}
elseif (isset($_POST['password']))
{ // ElseIf we cant to check the passwords strength.
    $PASS =
            stripslashes(
                    strip_tags(
                            htmlentities($_POST['password'], ENT_QUOTES,
                                    'ISO-8859-1'))); // Cleans all nasty input from the password.
    $strength = 1; // Sets their default amount of points to 1.

    if ($PASS != NULL)
    { // If the current password is not NULL (empty).
        $numbers =
                array( // Creates our array to store 1 - 9 in.
                        1 => "1", // 1.
                        2 => "2", // 2.
                        3 => "3", // 3.
                        4 => "4", // 4.
                        5 => "5", // 5.
                        6 => "6", // 6.
                        7 => "7", // 7.
                        8 => "8", // 8.
                        9 => "9", // 9.
                        0 => "0" // 0.
                ); // Closes the Array.

        $undercase =
                array( // Creates our array to store a - z in.
                        1 => "a", // a.
                        2 => "b", // b.
                        3 => "c", // c.
                        4 => "d", // d.
                        5 => "e", // e.
                        6 => "f", // f.
                        7 => "g", // g.
                        8 => "h", // h.
                        9 => "i", // i.
                        10 => "j", // j.
                        11 => "k", // k.
                        12 => "l", // l.
                        13 => "m", // m.
                        14 => "n", // n.
                        15 => "o", // o.
                        16 => "p", // p.
                        17 => "q", // q.
                        18 => "r", // r.
                        19 => "s", // s.
                        20 => "t", // t.
                        21 => "u", // u.
                        22 => "v", // v.
                        23 => "w", // w.
                        24 => "x", // x.
                        25 => "y", // y.
                        26 => "z" // z.
                ); // Closes the Array.

        $uppercase =
                array( // Creates our array to store A - Z in.
                        1 => "A", // A.
                        2 => "B", // B.
                        3 => "C", // C.
                        4 => "D", // D.
                        5 => "E", // E.
                        6 => "F", // F.
                        7 => "G", // G.
                        8 => "H", // H.
                        9 => "I", // I.
                        10 => "J", // J.
                        11 => "K", // K.
                        12 => "L", // L.
                        13 => "M", // M.
                        14 => "N", // N.
                        15 => "O", // O.
                        16 => "P", // P.
                        17 => "Q", // Q.
                        18 => "R", // R.
                        19 => "S", // S.
                        20 => "T", // T.
                        21 => "U", // U.
                        22 => "V", // V.
                        23 => "W", // W.
                        24 => "X", // X.
                        25 => "Y", // Y.
                        26 => "Z" // Z.
                ); // Closes the Array.
        $symbs =
                array('\\', '/', '"', "'", "{", "}", ")", "(", "|", "?", ".",
                        ",", "<", ">", "_", "-", "!", "#", "\$", "%", "^",
                        "&", "*");
        $strength = 0;
        if (strlen($PASS) >= 7)
        {
            $strength += 3;
        }
        $nc = 0;
        foreach ($numbers as $v)
        {
            if (strstr($PASS, $v))
            {
                $nc++;
            }
        }
        if ($nc >= 2)
        {
            $strength += 1;
        }
        if ($nc >= 5)
        {
            $strength += 1;
        }
        $nc = 0;
        foreach ($undercase as $v)
        {
            if (strstr($PASS, $v))
            {
                $nc++;
            }
        }
        if ($nc >= 2)
        {
            $strength += 1;
        }
        if ($nc >= 5)
        {
            $strength += 1;
        }
        $nc = 0;
        foreach ($uppercase as $v)
        {
            if (strstr($PASS, $v))
            {
                $nc++;
            }
        }
        if ($nc >= 2)
        {
            $strength += 1;
        }
        if ($nc >= 5)
        {
            $strength += 1;
        }
        $nc = 0;
        foreach ($symbs as $v)
        {
            if (strstr($PASS, $v))
            {
                $nc++;
            }
        }
        if ($nc >= 1)
        {
            $strength += 1;
        }
        if ($nc >= 2)
        {
            $strength += 1;
        }
        if ($nc >= 5)
        {
            $strength += 1;
        }
		$strengthbar=$strength*10;
		if ($strengthbar > 100)
		{
			$strengthbar = 100;
		}
		echo"<br />";
        if ($strength <= 2 || $strength == 0)
        { // If there total points are equal or less than 2.
            $overall = "<div class='progress-bar progress-bar-danger' role='progressbar' aria-valuenow='$strengthbar'
  aria-valuemin='0' aria-valuemax='11' style='width:$strengthbar%'>
  {$strengthbar}% Strong
  </div>"; // Eeek very week!
        }
        elseif ($strength <= 5)
        { // If there total points are equal or less than 5.
            $overall = "<div class='progress-bar progress-bar-warning' role='progressbar' aria-valuenow='$strengthbar'
  aria-valuemin='0' aria-valuemax='11' style='width:$strengthbar%'>
  {$strengthbar}% Strong
  </div>"; // Omg week.
        }
        elseif ($strength <= 8)
        { // If there total points are equal or less than 8.
            $overall = "<div class='progress-bar progress-bar-info' role='progressbar' aria-valuenow='$strengthbar'
  aria-valuemin='0' aria-valuemax='11' style='width:$strengthbar%'>
  {$strengthbar}% Strong
  </div>"; // Meh Moderate.
        }
        elseif ($strength >= 8)
        { // If there total points are greator than 10.
            $overall = "<div class='progress-bar progress-bar-success' role='progressbar' aria-valuenow='$strengthbar'
  aria-valuemin='0' aria-valuemax='11' style='width:$strengthbar%'>
  {$strengthbar}% Strong
  </div>"; // Thats the way Superman.
        } // End If.

        echo "<div class='progress'>{$overall}</div>"; // Tells them their passwords strength.

    }
    elseif ($PASS == NULL)
    { // ElseIf their password is NULL (empty).
        echo ''; // Dont display anything.
    } // End ElseIf.
} // End ElseIF.
