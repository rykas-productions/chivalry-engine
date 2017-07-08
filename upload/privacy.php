<?php
require('globals_nonauth.php');
echo "<h3>{$set['WebsiteName']} {$lang['PP_TITLE']}</h3><hr />";
echo "{$set['WebsiteName']} {$lang['PP_INTRO']} 
		{$set['WebsiteName']} {$lang['PP_INTRO2']} 
		{$set['WebsiteName']} {$lang['PP_INTRO3']} 
		{$set['WebsiteName']} {$lang['PP_INTRO4']}
		<br />
		<br />
		{$lang['PP_INTRO5']}
		<br />
		<br />
		{$lang['PP_INTRO6']}
		<hr />";
//Section 1
echo "<h4>{$lang['PP_SEC1']} {$set['WebsiteName']}</h4>
	{$lang['PP_SEC11']} {$set['WebsiteName']} {$lang['PP_SEC11_2']}<br /><br />
		{$lang['PP_SEC12']} {$set['WebsiteName']} {$lang['PP_SEC12_2']}<br /><br />
			{$lang['PP_SEC13']} {$set['WebsiteName']} {$lang['PP_SEC13_2']} {$set['WebsiteName']} {$lang['PP_SEC13_3']}<br /><br />
				{$lang['PP_SEC14']} {$set['WebsiteName']} {$lang['PP_SEC14_2']} {$set['WebsiteName']} {$lang['PP_SEC14_3']}<br /><br />
					{$lang['PP_SEC15']} {$set['WebsiteName']} {$lang['PP_SEC15_2']} {$set['WebsiteName']} {$lang['PP_SEC15_3']}<br /><br />
						{$lang['PP_SEC16']} {$set['WebsiteName']} {$lang['PP_SEC16_2']}<hr />";
//Section 2
echo "<h4>{$lang['PP_SEC2']}</h4>
	{$lang['PP_SEC2_2']} {$set['WebsiteName']} {$lang['PP_SEC2_3']}<br /><br />
		{$lang['PP_SEC2_4']}
		<hr />";
//Section 3
echo "<h4>{$lang['PP_SEC3']}</h4>
	{$lang['PP_SEC31_INFO']}<br />
		{$lang['PP_SEC31']}<br /><br />
			{$lang['PP_SEC31_2']}<br /><br />
				{$lang['PP_SEC321']}<br />
					{$lang['PP_SEC321_2']}<br /><br />
						{$lang['PP_SEC33']}<br />
							{$lang['PP_SEC331']} {$set['WebsiteName']} {$lang['PP_SEC331_2']} {$set['WebsiteName']} {$lang['PP_SEC331_3']}
							<hr />";
//Section 4
echo "<h4>{$lang['PP_SEC4']}</h4>
	{$lang['PP_SEC41']} {$set['WebsiteName']} {$lang['PP_SEC41_2']}<hr />";
//Section 5
echo "<h4>{$lang['PP_SEC5']}</h4>
	{$lang['PP_SEC51']} {$set['WebsiteName']} {$lang['PP_SEC51_2']} {$set['WebsiteName']}{$lang['PP_SEC51_3']} {$set['WebsiteName']} {$lang['PP_SEC51_4']}<br /><br />
		<u>{$lang['PP_SEC5_2']} {$set['WebsiteName']} {$lang['PP_SEC5_3']}</u><br /><br />
			{$lang['PP_SEC52']} {$set['WebsiteName']} {$lang['PP_SEC52_2']} {$set['WebsiteName']} {$lang['PP_SEC52_3']}<hr />";
//Section 6
echo "<h4>{$lang['PP_SEC6']}</h4>
	{$set['WebsiteName']} {$lang['PP_SEC6_2']} {$set['WebsiteName']} {$lang['PP_SEC6_3']}<hr />";
//Section 7
echo "<h4>{$lang['PP_SEC7']}</h4>
	{$lang['PP_SEC71']}<br /><br />
		{$lang['PP_SEC72']} {$set['WebsiteName']} {$lang['PP_SEC72_2']}<hr />";
//Section 8
echo "<h4>{$lang['PP_SEC8']}</h4>
	{$lang['PP_SEC8_2']} {$set['WebsiteName']} {$lang['PP_SEC8_3']}";
$h->endpage();