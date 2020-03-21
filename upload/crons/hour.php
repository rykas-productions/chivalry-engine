<?php
/*
	File: 		crons/hour.php
	Created: 	6/23/2019 at 6:11PM Eastern Time
	Info: 		Actions that run once an hour, server time.
	Author: 	TheMasterGeneral
	Website: 	https://github.com/MasterGeneral156/chivalry-engine
	
	MIT License

	Copyright (c) 2019 TheMasterGeneral

	Permission is hereby granted, free of charge, to any person obtaining a copy
	of this software and associated documentation files (the "Software"), to deal
	in the Software without restriction, including without limitation the rights
	to use, copy, modify, merge, publish, distribute, sublicense, and/or sell
	copies of the Software, and to permit persons to whom the Software is
	furnished to do so, subject to the following conditions:

	The above copyright notice and this permission notice shall be included in all
	copies or substantial portions of the Software.

	THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND, EXPRESS OR
	IMPLIED, INCLUDING BUT NOT LIMITED TO THE WARRANTIES OF MERCHANTABILITY,
	FITNESS FOR A PARTICULAR PURPOSE AND NONINFRINGEMENT. IN NO EVENT SHALL THE
	AUTHORS OR COPYRIGHT HOLDERS BE LIABLE FOR ANY CLAIM, DAMAGES OR OTHER
	LIABILITY, WHETHER IN AN ACTION OF CONTRACT, TORT OR OTHERWISE, ARISING FROM,
	OUT OF OR IN CONNECTION WITH THE SOFTWARE OR THE USE OR OTHER DEALINGS IN THE
	SOFTWARE.
*/
$file = 'crons/hour.php';

$ready_to_run = $db->query("SELECT `nextUpdate` FROM `crons` WHERE `file`='{$file}' AND `nextUpdate` <= unix_timestamp()");
//Place your queries inside the conditional
if ($db->num_rows($ready_to_run)) {
    $time = 3600;

    //Job crons!
    $db->query("UPDATE `users` AS `u`
        LEFT JOIN `job_ranks` as `jr` ON `jr`.`jrID` = `u`.`jobrank`
        SET `u`.`primary_currency` = `u`.`primary_currency` + `jr`.`jrPRIMPAY`,
        `u`.`secondary_currency` = `u`.`secondary_currency` + `jr`.`jrSECONDARY`
        WHERE `u`.`job` > 0 AND `u`.`jobrank` > 0");
    $db->query("UPDATE `users` SET `jobwork` = 0 WHERE `job` > 0 AND `jobrank` > 0");

    //Update queries!
    $db->query("UPDATE `crons` SET `nextUpdate`=`nextUpdate`+{$time} WHERE `file`='{$file}'");
}
?>
