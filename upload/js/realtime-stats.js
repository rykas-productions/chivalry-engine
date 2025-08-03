// Real-time Stat Updates for Chivalry Engine
class RealtimeStats {
    constructor() {
        this.init();
    }
    
    init() {
        // Create global functions that can be called from any page
        window.updateSidebarStats = this.updateStats.bind(this);
        window.updateSingleStat = this.updateSingleStat.bind(this);
        window.refreshSidebarStats = this.refreshStats.bind(this);
        
        console.log('Real-time stats system initialized');
    }
    
    // Update multiple stats at once
    updateStats(statsData) {
        if (statsData.hp_percent !== undefined) {
            this.updateSingleStat('hp', statsData.hp_percent);
        }
        if (statsData.energy_percent !== undefined) {
            this.updateSingleStat('energy', statsData.energy_percent);
        }
        if (statsData.xp_percent !== undefined) {
            this.updateSingleStat('xp', statsData.xp_percent);
        }
    }
    
    // Update a single stat with animation
    updateSingleStat(statName, percent) {
        const progressBar = document.getElementById(`sidebar-${statName}-bar`);
        
        if (progressBar) {
            // Store old value for comparison
            const oldPercent = parseInt(progressBar.style.width) || 0;
            
            // Animate the progress bar change
            this.animateProgressBar(progressBar, percent);
            
            // Update the text content
            progressBar.textContent = `${percent}%`;
            
            // Add highlight effect if value changed significantly
            if (Math.abs(oldPercent - percent) > 5) {
                this.highlightStat(progressBar);
            }
            
            // Show floating damage for HP decreases
            if (statName === 'hp' && percent < oldPercent) {
                this.showFloatingDamage(progressBar, oldPercent - percent);
            }
            
            console.log(`Updated ${statName}: ${oldPercent}% → ${percent}%`);
        } else {
            console.warn(`Progress bar not found for stat: ${statName}`);
        }
    }
    
    // Animate progress bar width change
    animateProgressBar(progressBar, newPercent) {
        const targetWidth = Math.min(100, Math.max(0, newPercent));
        
        // Create smooth transition
        progressBar.style.transition = 'width 0.5s ease-out, background-color 0.3s ease';
        progressBar.style.width = `${targetWidth}%`;
        
        // Update HP color based on percentage
        if (progressBar.id === 'sidebar-hp-bar') {
            if (targetWidth < 25) {
                progressBar.className = 'progress-bar bg-danger';
            } else if (targetWidth < 50) {
                progressBar.className = 'progress-bar bg-warning';
            } else {
                progressBar.className = 'progress-bar bg-success';
            }
        }
        
        // Remove transition after animation
        setTimeout(() => {
            progressBar.style.transition = '';
        }, 500);
    }
    
    // Highlight stat with a brief glow effect
    highlightStat(progressBar) {
        const container = progressBar.closest('.stat-item') || progressBar.parentElement;
        
        // Add highlight class
        container.classList.add('stat-updated');
        
        // Remove after animation
        setTimeout(() => {
            container.classList.remove('stat-updated');
        }, 1000);
    }
    
    // Show floating damage number
    showFloatingDamage(progressBar, damagePercent) {
        const floatingNumber = document.createElement('div');
        floatingNumber.className = 'floating-damage';
        floatingNumber.textContent = `-${Math.round(damagePercent)}%`;
        floatingNumber.style.cssText = `
            position: fixed;
            color: #dc3545;
            font-weight: bold;
            font-size: 16px;
            pointer-events: none;
            z-index: 1000;
            text-shadow: 1px 1px 2px rgba(0,0,0,0.8);
            animation: floatUp 2s ease-out forwards;
        `;
        
        // Position relative to the progress bar
        const rect = progressBar.getBoundingClientRect();
        floatingNumber.style.left = (rect.right + 10) + 'px';
        floatingNumber.style.top = rect.top + 'px';
        
        document.body.appendChild(floatingNumber);
        
        // Remove after animation
        setTimeout(() => {
            if (floatingNumber.parentNode) {
                floatingNumber.parentNode.removeChild(floatingNumber);
            }
        }, 2000);
    }
    
    // Manual stat refresh
    refreshStats() {
        fetch('api/get_stats.php')
            .then(response => response.json())
            .then(data => {
                if (data.success && data.stats) {
                    this.updateStats(data.stats);
                    console.log('Stats refreshed successfully');
                } else {
                    console.error('Failed to refresh stats:', data.message);
                }
            })
            .catch(error => {
                console.error('Error refreshing stats:', error);
            });
    }
}

// Add CSS for stat animations
const statsCSS = document.createElement('style');
statsCSS.textContent = `
    .stat-updated {
        animation: statHighlight 1s ease-out;
    }
    
    @keyframes statHighlight {
        0% { 
            box-shadow: 0 0 0 2px rgba(13, 202, 240, 0.5);
            transform: scale(1);
        }
        50% { 
            box-shadow: 0 0 0 4px rgba(13, 202, 240, 0.3);
            transform: scale(1.02);
        }
        100% { 
            box-shadow: 0 0 0 0 rgba(13, 202, 240, 0);
            transform: scale(1);
        }
    }
    
    @keyframes floatUp {
        0% {
            opacity: 1;
            transform: translateY(0);
        }
        100% {
            opacity: 0;
            transform: translateY(-50px);
        }
    }
    
    .floating-damage {
        text-shadow: 1px 1px 2px rgba(0,0,0,0.8);
    }
    
    .stat-notification {
        animation: slideInFromTop 0.3s ease-out;
    }
    
    @keyframes slideInFromTop {
        from {
            opacity: 0;
            transform: translateX(-50%) translateY(-20px);
        }
        to {
            opacity: 1;
            transform: translateX(-50%) translateY(0);
        }
    }
`;
document.head.appendChild(statsCSS);

// Initialize the real-time stats system
document.addEventListener('DOMContentLoaded', () => {
    window.realtimeStats = new RealtimeStats();
});

// Export for use in other scripts
window.RealtimeStats = RealtimeStats;