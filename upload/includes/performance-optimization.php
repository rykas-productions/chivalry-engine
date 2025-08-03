<?php
/*
    File: performance-optimization.php
    Created: Performance optimization utilities
    Info: Database optimization, caching, and performance helpers
*/

class PerformanceOptimizer {
    private $db;
    private static $queryCache = [];
    private static $cacheHits = 0;
    private static $cacheMisses = 0;
    
    public function __construct($database) {
        $this->db = $database;
    }
    
    /**
     * Execute a cached query - useful for frequent lookups
     */
    public function cachedQuery($sql, $cacheTime = 300) {
        $cacheKey = md5($sql);
        $cacheFile = sys_get_temp_dir() . "/chivalry_cache_{$cacheKey}.tmp";
        
        // Check if cache exists and is still valid
        if (file_exists($cacheFile) && (time() - filemtime($cacheFile)) < $cacheTime) {
            self::$cacheHits++;
            return unserialize(file_get_contents($cacheFile));
        }
        
        // Execute query and cache result
        $result = $this->db->query($sql);
        $data = [];
        
        if ($result) {
            while ($row = $this->db->fetch_row($result)) {
                $data[] = $row;
            }
            
            // Cache the result
            file_put_contents($cacheFile, serialize($data));
            self::$cacheMisses++;
        }
        
        return $data;
    }
    
    /**
     * Optimize user data loading with single query
     */
    public function getUserDataOptimized($userid) {
        $cacheKey = "user_data_{$userid}";
        
        if (isset(self::$queryCache[$cacheKey])) {
            return self::$queryCache[$cacheKey];
        }
        
        // Single optimized query to get all user data
        $sql = "SELECT u.*, 
                       COALESCE(i.infirmary_out, 0) as infirmary_time,
                       COALESCE(d.dungeon_out, 0) as dungeon_time,
                       COUNT(DISTINCT m.mail_id) as unread_mail,
                       COUNT(DISTINCT n.notif_id) as unread_notifications
                FROM users u
                LEFT JOIN infirmary i ON u.userid = i.infirmary_user AND i.infirmary_out > 0
                LEFT JOIN dungeon d ON u.userid = d.dungeon_user AND d.dungeon_out > 0
                LEFT JOIN mail m ON u.userid = m.mail_to AND m.mail_status = 'unread'
                LEFT JOIN notifications n ON u.userid = n.notif_user AND n.notif_status = 'unread'
                WHERE u.userid = {$userid}
                GROUP BY u.userid";
        
        $result = $this->db->fetch_row($this->db->query($sql));
        
        // Cache for this request
        self::$queryCache[$cacheKey] = $result;
        
        return $result;
    }
    
    /**
     * Batch update multiple user stats in single query
     */
    public function batchUpdateStats($userid, $stats) {
        $setParts = [];
        
        foreach ($stats as $stat => $value) {
            $stat = $this->db->escape($stat);
            $value = (int)$value;
            $setParts[] = "`{$stat}` = {$value}";
        }
        
        if (!empty($setParts)) {
            $sql = "UPDATE users SET " . implode(', ', $setParts) . " WHERE userid = {$userid}";
            return $this->db->query($sql);
        }
        
        return false;
    }
    
    /**
     * Get performance statistics
     */
    public function getPerformanceStats() {
        return [
            'cache_hits' => self::$cacheHits,
            'cache_misses' => self::$cacheMisses,
            'cache_ratio' => self::$cacheHits + self::$cacheMisses > 0 ? 
                round((self::$cacheHits / (self::$cacheHits + self::$cacheMisses)) * 100, 2) : 0,
            'memory_usage' => memory_get_usage(true),
            'peak_memory' => memory_get_peak_usage(true)
        ];
    }
    
    /**
     * Clean expired cache files
     */
    public function cleanCache() {
        $tempDir = sys_get_temp_dir();
        $pattern = $tempDir . '/chivalry_cache_*.tmp';
        $files = glob($pattern);
        $cleaned = 0;
        
        foreach ($files as $file) {
            // Remove files older than 1 hour
            if (time() - filemtime($file) > 3600) {
                unlink($file);
                $cleaned++;
            }
        }
        
        return $cleaned;
    }
    
    /**
     * Optimize database tables
     */
    public function optimizeTables() {
        $tables = ['users', 'logs', 'notifications', 'mail', 'inventory'];
        $optimized = [];
        
        foreach ($tables as $table) {
            $result = $this->db->query("OPTIMIZE TABLE `{$table}`");
            if ($result) {
                $optimized[] = $table;
            }
        }
        
        return $optimized;
    }
    
    /**
     * Generate performance report
     */
    public function generatePerformanceReport() {
        $stats = $this->getPerformanceStats();
        $report = [
            'timestamp' => date('Y-m-d H:i:s'),
            'performance' => $stats,
            'recommendations' => []
        ];
        
        // Add recommendations based on performance
        if ($stats['cache_ratio'] < 70) {
            $report['recommendations'][] = 'Consider increasing cache usage - current hit ratio is low';
        }
        
        if ($stats['memory_usage'] > 128 * 1024 * 1024) { // 128MB
            $report['recommendations'][] = 'Memory usage is high - consider optimizing queries';
        }
        
        return $report;
    }
}

/**
 * Initialize performance optimization
 */
function initPerformanceOptimization($db) {
    global $performanceOptimizer;
    $performanceOptimizer = new PerformanceOptimizer($db);
    
    // Clean cache periodically (1% chance)
    if (rand(1, 100) === 1) {
        $performanceOptimizer->cleanCache();
    }
    
    return $performanceOptimizer;
}

/**
 * Output performance headers
 */
function setPerformanceHeaders() {
    // Enable gzip compression if not already enabled
    if (!ob_get_level() && extension_loaded('zlib') && !headers_sent()) {
        ob_start('ob_gzhandler');
    }
    
    // Set caching headers for static content
    if (isset($_SERVER['REQUEST_URI'])) {
        $uri = $_SERVER['REQUEST_URI'];
        
        if (preg_match('/\.(css|js|png|jpg|jpeg|gif|svg|ico|woff|woff2|ttf)$/i', $uri)) {
            header('Cache-Control: public, max-age=31536000'); // 1 year
            header('Expires: ' . gmdate('D, d M Y H:i:s', time() + 31536000) . ' GMT');
        } elseif (preg_match('/\.(html|php)$/i', $uri)) {
            header('Cache-Control: public, max-age=3600'); // 1 hour
            header('Expires: ' . gmdate('D, d M Y H:i:s', time() + 3600) . ' GMT');
        }
    }
    
    // Enable HTTP/2 Server Push for critical resources
    if (function_exists('header_register_callback')) {
        header_register_callback(function() {
            if (isset($_SERVER['SERVER_PROTOCOL']) && strpos($_SERVER['SERVER_PROTOCOL'], '2') !== false) {
                header('Link: </css/master-combined.css>; rel=preload; as=style');
                header('Link: </css/themes.css>; rel=preload; as=style');
                header('Link: </js/theme-switcher.js>; rel=preload; as=script');
            }
        });
    }
}

// Auto-initialize if called directly
if (!function_exists('initPerformanceOptimization') && isset($db)) {
    initPerformanceOptimization($db);
    setPerformanceHeaders();
}
?>