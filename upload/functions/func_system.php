<?php
/*
	File:	    functions/func_system.php
	Created:	Mar 7, 2020 at 8:28:29 PM Eastern Time
	Author:	    TheMasterGeneral
	Website:	https://github.com/rykas-productions/chivalry-engine
	MIT License
	Copyright (c) 2020 TheMasterGeneral
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
//@todo Make this function
function getEngineVersion()
{
    
}

/**
 * @desc Check to see if Chivalry Engine is up to date or not.
 * @param string $currentVersion
 * @param string $url
 * @return string
 */
function getEngineUpdate(string $currentVersion, string $url = 'https://raw.githubusercontent.com/MasterGeneral156/Version/master/chivalry-engine.json')
{
    $json = json_decode(getCachedFile($url, "cev3_update.json"), true);
    if (empty($currentVersion))
        $currentVersion = getEngineVersion();
    if (is_null($json))
        return "Update checker failed.";
    if (version_compare($currentVersion, $json['latest-v3']) == 0 || version_compare($currentVersion, $json['latest-v3']) == 1)
        return "Chivalry Engine is up to date.";
    else
        return "Chivalry Engine version {$json['latest-v3']} available. Download it <a href='{$json['download-latest']}'>here</a>.";
}

/**
 * @desc Write a file from a remote server to the temp folder.
 * @param string $url URL of the file to download/save.
 * @param string $fileName The name of the file to save/read.
 * @param int $cacheHour How many hours before updating a cached file? [Default = 24]
 * @return string
 */
function getRemoteFile(string $url, string $fileName, $cacheHour = 24)
{
    $cacheExpireTime = $cacheHour * 60 * 60;
    $fileAbs=__DIR__ . "/assets/temp/$fileName";
    if (file_exists($fileAbs)) 
    {
        $fileTime = filemtime($fileAbs);
        if (time() - $cacheExpireTime < $fileTime) 
        {
            return file_get_contents($fileAbs);
        } 
        else 
        {
            $content = remoteConnect($url, $fileAbs);
            file_put_contents($fileAbs, $content);
            return $content;
        }
    } 
    else 
    {
        $content = remoteConnect($url, $fileAbs);
        file_put_contents($fileAbs, $content);
        return $content;
    }
}

/**
 * Connect to a remote server using cURL.
 * @param string $url URL to visit.
 * @return object Connection data.
 */
function remoteConnect(string $url)
{
    $curl = curl_init();
    curl_setopt_array($curl, array(
        CURLOPT_URL => "{$url}",
        CURLOPT_SSL_VERIFYPEER => false,
        CURLOPT_RETURNTRANSFER => true));
    $content = curl_exec($curl);
    curl_close($curl);
    return $content;
}