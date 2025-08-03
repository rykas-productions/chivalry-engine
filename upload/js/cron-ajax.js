// Cron AJAX Runner - Runs crons periodically without blocking page loads
(function() {
    'use strict';
    
    // Configuration
    const CRON_INTERVAL = 5 * 60 * 1000; // 5 minutes
    const CRON_URL = 'cron_scheduler.php';
    
    // Check if we should run crons
    function shouldRunCron() {
        const lastRun = localStorage.getItem('lastCronRun');
        const now = Date.now();
        
        if (!lastRun) {
            return true;
        }
        
        return (now - parseInt(lastRun)) > CRON_INTERVAL;
    }
    
    // Run cron via AJAX
    function runCron() {
        if (!shouldRunCron()) {
            return;
        }
        
        // Update last run time immediately to prevent multiple runs
        localStorage.setItem('lastCronRun', Date.now().toString());
        
        // Run in background - don't block user interaction
        fetch(CRON_URL, {
            method: 'GET',
            headers: {
                'X-Requested-With': 'XMLHttpRequest'
            }
        })
        .then(response => {
            if (!response.ok) {
                console.log('Cron run failed:', response.status);
            }
        })
        .catch(error => {
            console.log('Cron error:', error);
        });
    }
    
    // Run cron check when page loads (but not blocking)
    if (document.readyState === 'loading') {
        document.addEventListener('DOMContentLoaded', function() {
            setTimeout(runCron, 1000); // Run 1 second after page load
        });
    } else {
        setTimeout(runCron, 1000);
    }
    
    // Set up periodic cron runs
    setInterval(runCron, CRON_INTERVAL);
    
    // Also run cron when user returns to tab (visibility API)
    document.addEventListener('visibilitychange', function() {
        if (!document.hidden) {
            runCron();
        }
    });
})();