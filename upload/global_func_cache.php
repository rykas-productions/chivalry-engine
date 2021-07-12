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