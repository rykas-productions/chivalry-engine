<?php
function cacheQuery($query, $result, $dir = 'query')
{
    $serialzedData = serialize($result);
    $cacheName = "./cache/{$dir}/" . md5($query);
    file_put_contents($cacheName, $serialzedData);
}

function fetchCachedQuery($query, $dir = 'query', $ttl = 86400)
{
    $cacheName = "./cache/{$dir}/" . md5($query);
    if (file_exists($cacheName))
    {
        $file_time = filemtime($cacheName);
        if ($ttl + $file_time <= time())
        {
            return unserialize(file_get_contents($cacheName));
        }
    }
}

/*
 Gets the contents of a file if it exists, otherwise grabs and caches
 */
function get_fg_cache($ip, $hours = 1)
{
    $current_time = time();
    $expire_time = $hours * 60 * 60;
    if (!(filter_var($ip, FILTER_VALIDATE_IP)))
        $ip='127.0.0.1';
    $file = "./cache/ip/{$ip}.json";
    if (file_exists($file)) 
    {
        $file_time = filemtime($file);
        if ($current_time - $expire_time < $file_time) 
        {
            return file_get_contents($file);
        }
        else 
        {
            $content = update_fg_info($ip);
            file_put_contents($file, $content);
            return $content;
        }
    } 
    else 
    {
        $content = update_fg_info($ip);
        file_put_contents($file, $content);
        return $content;
    }
}

/*
 Gets content from a URL via curl
 */
function update_fg_info($ip)
{
    global $set;
    if (!(filter_var($ip, FILTER_VALIDATE_IP)))
        $ip='127.0.0.1';
    $curl = curl_init();
    curl_setopt_array($curl, array(
        CURLOPT_URL => "https://api.fraudguard.io/ip/$ip",
        CURLOPT_USERPWD => "{$set['FGUsername']}:{$set['FGPassword']}",
        CURLOPT_SSL_VERIFYPEER => false,
        CURLOPT_RETURNTRANSFER => true));
    $content = curl_exec($curl);
    curl_close($curl);
    return $content;
}