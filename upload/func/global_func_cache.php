<?php
/**
 * @internal
 * Stores the query into file, to be read from later. Stored in the ./cache/ directory.
 * @param string $query Database query string.
 * @param object $result Database query result
 * @param string $dir Directory to store cache file, optional. Defaults to query.
 */
function cacheQuery($query, $result, $dir = 'query')
{
    $serialzedData = serialize($result);
    $cacheName = returnCacheDir() . "{$dir}/" . md5($query);
    file_put_contents($cacheName, $serialzedData);
}

/**
 * Fetches a cached query from the cache directory, if possible.
 * @param string $query Database query string.
 * @param string $dir Directory to store cache file, optional. Defaults to query.
 * @param int $ttl Cache expiration, in seconds. Default 86400, or 1 day.
 */
function fetchCachedQuery($query, $dir = 'query', $ttl = 86400)
{
    $cacheName = returnCacheDir() . "{$dir}/" . md5($query);
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
    $file = returnCacheDir() . "ip/{$ip}.json";
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

/**
 * Reads/grabs data from a URL input, to be cached on the game's servers, to be updated less often.
 * @param string $url URL to view and cache.
 * @param string $file Dir/Filename to store the data as cache.$this
 * @param int $hours Hours before we update our cache, default = 1.
 * @return string Content from URL.
 */
function get_cached_file($url, $file, $hours = 1)
{
    $current_time = time();
    $expire_time = $hours * 60 * 60;
    if (file_exists($file))
    {
        $file_time = filemtime($file);
        if ($current_time - $expire_time < $file_time)
        {
            return file_get_contents($file);
        }
        else
        {
            $content = curlOpenFile($url, $file);
            file_put_contents($file, $content);
            return $content;
        }
    }
    else
    {
        $content = curlOpenFile($url, $file);
        file_put_contents($file, $content);
        return $content;
    }
}

/**
 * Internal function, used to update recache the latest posts/topics after a
 * forum topic gets moved or deleted.
 * @param int $topic Forum topic to recache.
 */
function recache_topic($topic)
{
    global $db;
    $topic = abs((int)$topic);
    if ($topic <= 0) {
        return;
    }
    echo "Recaching Topic ID #{$topic} ... ";
    $q =
    $db->query(
        "/*qc=on*/SELECT `fp_poster_id`, `fp_poster_id`, `fp_time`
                     FROM `forum_posts`
                     WHERE `fp_topic_id` = {$topic}
                     ORDER BY `fp_time` DESC
                     LIMIT 1");
    if ($db->num_rows($q) == 0) {
        $db->free_result($q);
        $db->query(
            "UPDATE `forum_topics`
                 SET `ft_last_id` = 0, `ft_last_time` = 0, `ft_posts` = 0
                 WHERE `ft_id` = {$topic}");
    } else {
        $r = $db->fetch_row($q);
        $db->free_result($q);
        $posts_q =
        $db->query(
            "/*qc=on*/SELECT COUNT(`fp_id`)
        					   FROM `forum_posts`
        					   WHERE `fp_topic_id` = {$topic}");
        $posts = $db->fetch_single($posts_q);
        $db->free_result($posts_q);
        $db->query(
            "UPDATE `forum_topics`
                 SET `ft_last_id` = {$r['fp_poster_id']},
                 `ft_last_time` = {$r['fp_time']}, `ft_last_id` = '{$r['fp_poster_id']}',
                 `ft_posts` = {$posts}
                 WHERE `ft_id` = {$topic}");
    }
    echo " ... Recaching completed.<br />";
}

/**
 * Internal function, used to update recache the latest posts/topics after a
 * forum topic gets moved or deleted.
 * @param int $forum Forum category to recache.
 */
function recache_forum($forum)
{
    global $db;
    $forum = abs((int)$forum);
    if ($forum <= 0) {
        return;
    }
    echo "Recaching Forum ID #{$forum} ... ";
    $q =
    $db->query(
        "/*qc=on*/SELECT `p`.*, `t`.*
                     FROM `forum_posts` AS `p`
                     LEFT JOIN `forum_topics` AS `t`
                     ON `p`.`fp_topic_id` = `t`.`ft_id`
                     WHERE `p`.`ff_id` = {$forum}
                     ORDER BY `p`.`fp_time` DESC
                     LIMIT 1");
    if ($db->num_rows($q) == 0) {
        $db->free_result($q);
        $db->query(
            "UPDATE `forum_forums`
                 SET `ff_lp_time` = 0, `ff_lp_poster_id` = 0, `ff_lp_t_id` = 0,
                 `ff_lp_t_id` = 0
                  WHERE `ff_id` = {$forum}");
    } else {
        $r = $db->fetch_row($q);
        $db->free_result($q);
        $db->query(
            "UPDATE `forum_forums`
                 SET `ff_lp_time` = {$r['fp_time']},
                 `ff_lp_poster_id` = {$r['fp_poster_id']},
				 `ff_lp_t_id` = {$r['ft_id']}
                 WHERE `ff_id` = {$forum}");
    }
    echo " ... Recaching completed.<br />";
}

/**
 * Internal version check function for Chivalry Engine.
 * @param string $url URL for version checker. (Default = https://raw.githubusercontent.com/MasterGeneral156/Version/master/chivalry-engine.json)
 * @return string Update Status
 */
function version_json($url = 'https://raw.githubusercontent.com/MasterGeneral156/Version/master/chivalry-engine.json')
{
    global $set;
    $engine_version = $set['Version_Number'];
    $json = json_decode(get_cached_file($url, returnCacheDir() . "update_check.json"), true);
    if (is_null($json))
        return "Update checker failed.";
        if (version_compare($engine_version, $json['latest']) == 0 || version_compare($engine_version, $json['latest']) == 1)
            return "Chivalry Engine is up to date.";
            else
                return "Chivalry Engine update available. Download it <a href='{$json['download-latest']}'>here</a>.";
}

function fetchCIDDB()
{
    return get_cached_file("https://chivalryisdeadgame.com/cache/latest.sql", dirname(__DIR__) . "/cache/latest.sql", 24);
}

function getVPSData()
{
    global $_CONFIG;
    $cacheFile = returnCacheDir() . "/serv/vps.json";
    if (file_exists($cacheFile))
    {
        $file_time = filemtime($cacheFile);
        if (time() - 3600 < $file_time)
        {
            $return =  file_get_contents($cacheFile);
        }
        else
        {
            $curl = curl_init();
            curl_setopt_array($curl, array(
                CURLOPT_URL => "https://api.vps.net/ssd_virtual_machines/259426.api10json",
                CURLOPT_USERPWD => $_CONFIG['vpsAuth'],
                CURLOPT_SSL_VERIFYPEER => false,
                CURLOPT_HTTPHEADER => array("Accept: application/json"),
                CURLOPT_RETURNTRANSFER => true));
            $content = curl_exec($curl);
            file_put_contents($cacheFile, $content);
            $return =  file_get_contents($cacheFile);
            curl_close($curl);
        }
    }
    else
    {
        $curl = curl_init();
        curl_setopt_array($curl, array(
            CURLOPT_URL => "https://api.vps.net/ssd_virtual_machines/259426.api10json",
            CURLOPT_USERPWD => $_CONFIG['vpsAuth'],
            CURLOPT_SSL_VERIFYPEER => false,
            CURLOPT_HTTPHEADER => array("Accept: application/json"),
            CURLOPT_RETURNTRANSFER => true));
        $content = curl_exec($curl);
        file_put_contents($cacheFile, $content);
        $return =  file_get_contents($cacheFile);
        curl_close($curl);
    }
    return $return;
}

function returnVPSInfo()
{
    $vpsJson = json_decode(getVPSData(), true);
    return $vpsJson['virtual_machine'];
}

function returnVPSBandwidth()
{
    $vpsJson = json_decode(getVPSData(), true);
    return $vpsJson['virtual_machine']['bandwidth_used'] * 1024;
}

function returnCacheDir()
{
    return dirname(__DIR__) . "/cache/";
}
