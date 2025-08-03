// Real-Time System for Chivalry Engine
class ChivalryRealTime {
    constructor() {
        this.wsConnection = null;
        this.reconnectAttempts = 0;
        this.maxReconnectAttempts = 5;
        this.reconnectDelay = 1000;
        this.lastActivity = Date.now();
        this.heartbeatInterval = null;
        this.init();
    }
    
    init() {
        this.connectWebSocket();
        this.startHeartbeat();
        this.bindEvents();
        this.startPolling(); // Fallback for when WebSocket isn't available
    }
    
    // WebSocket connection for real-time updates
    connectWebSocket() {
        if ('WebSocket' in window) {
            try {
                const protocol = window.location.protocol === 'https:' ? 'wss:' : 'ws:';
                const wsUrl = `${protocol}//${window.location.host}/ws/game`;
                
                this.wsConnection = new WebSocket(wsUrl);
                
                this.wsConnection.onopen = () => {
                    console.log('🔗 Real-time connection established');
                    this.reconnectAttempts = 0;
                    this.sendMessage('auth', { userId: window.userId || null });
                    showBootstrapToast('Connected', 'Real-time updates enabled', 'success', 3000);
                };
                
                this.wsConnection.onmessage = (event) => {
                    this.handleMessage(JSON.parse(event.data));
                };
                
                this.wsConnection.onclose = () => {
                    console.log('🔌 Real-time connection closed');
                    this.reconnect();
                };
                
                this.wsConnection.onerror = (error) => {
                    console.error('❌ WebSocket error:', error);
                };
            } catch (error) {
                console.log('WebSocket not available, using polling fallback');
                this.startPolling();
            }
        }
    }
    
    // Handle incoming real-time messages
    handleMessage(data) {
        switch (data.type) {
            case 'stats_update':
                this.updateStats(data.stats);
                break;
            case 'combat_result':
                this.handleCombatResult(data);
                break;
            case 'level_up':
                triggerLevelUp(data.newLevel);
                break;
            case 'notification':
                this.showRealTimeNotification(data);
                break;
            case 'player_online':
                this.updateOnlineStatus(data.players);
                break;
            case 'market_update':
                this.updateMarketPrices(data.items);
                break;
        }
    }
    
    // Update player stats with animations
    updateStats(stats) {
        Object.entries(stats).forEach(([stat, value]) => {
            const element = document.getElementById(`${stat}-value`);
            const bar = document.getElementById(`${stat}-bar`);
            
            if (element) {
                const oldValue = parseInt(element.textContent.replace(/,/g, ''));
                const newValue = parseInt(value);
                
                if (oldValue !== newValue) {
                    // Animate number change
                    animateValue(`${stat}-value`, newValue);
                    
                    // Animate progress bar if it exists
                    if (bar && stat.includes('percent')) {
                        const oldWidth = parseInt(bar.style.width) || 0;
                        animateHealthChange(`#${stat}-bar`, oldWidth, newValue, newValue > oldWidth);
                    }
                    
                    // Special effects for certain stats
                    if (stat === 'primary_currency' && newValue > oldValue) {
                        animateCoinCollection(newValue - oldValue, element.parentElement);
                    }
                }
            }
        });
    }
    
    // Handle combat results with epic animations
    handleCombatResult(result) {
        const targetElement = document.querySelector('.combat-area') || document.body;
        
        if (result.success) {
            triggerCombatAnimation(targetElement, result.damage, result.critical);
            
            // Show victory message
            if (result.victory) {
                this.showVictoryAnimation(result);
            }
        } else {
            // Show miss animation
            this.showMissAnimation(targetElement);
        }
    }
    
    // Victory animation sequence
    showVictoryAnimation(result) {
        const overlay = document.createElement('div');
        overlay.style.cssText = `
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: rgba(0, 0, 0, 0.8);
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            z-index: 10000;
            animation: fadeIn 0.5s ease-out;
        `;
        
        overlay.innerHTML = `
            <div style="text-align: center; color: gold;">
                <h1 style="font-size: 4rem; margin: 0; text-shadow: 2px 2px 4px rgba(0,0,0,0.8);">🏆 VICTORY! 🏆</h1>
                <div style="font-size: 1.5rem; margin-top: 20px;">
                    <div>Damage Dealt: ${result.damage}</div>
                    <div>XP Gained: +${result.xp}</div>
                    <div>Money Earned: +$${result.money}</div>
                </div>
            </div>
        `;
        
        document.body.appendChild(overlay);
        createCelebrationParticles();
        
        setTimeout(() => {
            overlay.style.animation = 'fadeOut 0.5s ease-out';
            setTimeout(() => overlay.remove(), 500);
        }, 3000);
    }
    
    // Miss animation
    showMissAnimation(element) {
        const missText = document.createElement('div');
        missText.textContent = 'MISS!';
        missText.style.cssText = `
            position: absolute;
            font-size: 2rem;
            color: #999;
            font-weight: bold;
            pointer-events: none;
            z-index: 1000;
            animation: damageNumber 1.5s ease-out forwards;
        `;
        
        const rect = element.getBoundingClientRect();
        missText.style.left = rect.left + rect.width / 2 + 'px';
        missText.style.top = rect.top + 'px';
        
        document.body.appendChild(missText);
        setTimeout(() => missText.remove(), 1500);
    }
    
    // Real-time notifications
    showRealTimeNotification(data) {
        showBootstrapToast(data.title, data.message, data.type || 'info');
        
        // Browser notification if permitted
        if (Notification.permission === 'granted') {
            new Notification(data.title, {
                body: data.message,
                icon: '/images/game-icon.png',
                badge: '/images/game-badge.png'
            });
        }
    }
    
    // Update online player count
    updateOnlineStatus(players) {
        const onlineCount = document.getElementById('online-count');
        if (onlineCount) {
            onlineCount.textContent = players.online;
            onlineCount.parentElement.classList.add('pulse');
            setTimeout(() => onlineCount.parentElement.classList.remove('pulse'), 1000);
        }
        
        // Update online players list if visible
        const playersList = document.getElementById('online-players-list');
        if (playersList) {
            playersList.innerHTML = players.list.map(player => `
                <div class="online-player">
                    <span class="status-indicator ${player.status}"></span>
                    ${player.name} (Level ${player.level})
                </div>
            `).join('');
        }
    }
    
    // Send message through WebSocket
    sendMessage(type, data) {
        if (this.wsConnection && this.wsConnection.readyState === WebSocket.OPEN) {
            this.wsConnection.send(JSON.stringify({ type, data }));
        }
    }
    
    // Heartbeat to keep connection alive
    startHeartbeat() {
        this.heartbeatInterval = setInterval(() => {
            if (this.wsConnection && this.wsConnection.readyState === WebSocket.OPEN) {
                this.sendMessage('ping', { timestamp: Date.now() });
            }
        }, 30000); // Every 30 seconds
    }
    
    // Reconnect logic
    reconnect() {
        if (this.reconnectAttempts < this.maxReconnectAttempts) {
            this.reconnectAttempts++;
            console.log(`🔄 Attempting to reconnect (${this.reconnectAttempts}/${this.maxReconnectAttempts})`);
            
            setTimeout(() => {
                this.connectWebSocket();
            }, this.reconnectDelay * this.reconnectAttempts);
        } else {
            console.log('❌ Max reconnection attempts reached, falling back to polling');
            this.startPolling();
        }
    }
    
    // Polling fallback when WebSocket unavailable
    startPolling() {
        setInterval(async () => {
            try {
                const response = await fetch('/api/realtime_updates.php', {
                    method: 'POST',
                    headers: { 'Content-Type': 'application/json' },
                    body: JSON.stringify({ lastUpdate: this.lastActivity })
                });
                
                const data = await response.json();
                if (data.updates) {
                    data.updates.forEach(update => this.handleMessage(update));
                }
                
                this.lastActivity = Date.now();
            } catch (error) {
                console.error('Polling error:', error);
            }
        }, 5000); // Poll every 5 seconds
    }
    
    // Bind page events
    bindEvents() {
        // Track user activity
        ['click', 'keypress', 'scroll', 'mousemove'].forEach(event => {
            document.addEventListener(event, () => {
                this.lastActivity = Date.now();
            }, { passive: true });
        });
        
        // Page visibility change
        document.addEventListener('visibilitychange', () => {
            if (document.hidden) {
                console.log('🔕 Page hidden, reducing activity');
            } else {
                console.log('👁️ Page visible, resuming activity');
                this.sendMessage('page_focus', { focused: true });
            }
        });
        
        // Before page unload
        window.addEventListener('beforeunload', () => {
            if (this.wsConnection) {
                this.wsConnection.close();
            }
        });
    }
}

// Initialize real-time system when DOM is ready
document.addEventListener('DOMContentLoaded', () => {
    window.chivalryRealTime = new ChivalryRealTime();
});

// Export for use in other scripts
window.ChivalryRealTime = ChivalryRealTime;