/**
 * Real-time Event Client
 * Handles Server-Sent Events for instant updates without page refresh
 */

class RealtimeClient {
    constructor() {
        this.eventSource = null;
        this.reconnectAttempts = 0;
        this.maxReconnectAttempts = 5;
        this.reconnectDelay = 1000;
        this.isConnected = false;
        
        this.init();
    }
    
    init() {
        // Only initialize if user is logged in
        if (typeof userid === 'undefined' || !userid) {
            return;
        }
        
        this.connect();
        this.setupEventListeners();
    }
    
    connect() {
        try {
            this.eventSource = new EventSource('realtime_events.php');
            
            // Connection opened
            this.eventSource.onopen = () => {
                console.log('Realtime connection established');
                this.isConnected = true;
                this.reconnectAttempts = 0;
                this.updateConnectionStatus('connected');
            };
            
            // Handle errors
            this.eventSource.onerror = (error) => {
                console.error('Realtime connection error:', error);
                this.isConnected = false;
                this.updateConnectionStatus('disconnected');
                this.handleReconnect();
            };
            
            // Handle different event types
            this.eventSource.addEventListener('connected', (e) => {
                const data = JSON.parse(e.data);
                console.log('Connected to realtime server:', data);
            });
            
            this.eventSource.addEventListener('notification', (e) => {
                const data = JSON.parse(e.data);
                this.handleNotification(data);
            });
            
            this.eventSource.addEventListener('resources', (e) => {
                const data = JSON.parse(e.data);
                this.updateResources(data);
            });
            
            this.eventSource.addEventListener('heartbeat', (e) => {
                // Keep connection alive
                const data = JSON.parse(e.data);
                this.lastHeartbeat = data.time;
            });
            
            this.eventSource.addEventListener('reconnect', (e) => {
                this.eventSource.close();
                setTimeout(() => this.connect(), 1000);
            });
            
        } catch (error) {
            console.error('Failed to create EventSource:', error);
        }
    }
    
    handleReconnect() {
        if (this.reconnectAttempts >= this.maxReconnectAttempts) {
            console.log('Max reconnection attempts reached');
            this.updateConnectionStatus('failed');
            return;
        }
        
        this.reconnectAttempts++;
        const delay = this.reconnectDelay * Math.pow(2, this.reconnectAttempts - 1);
        
        console.log(`Reconnecting in ${delay}ms (attempt ${this.reconnectAttempts})`);
        
        setTimeout(() => {
            if (!this.isConnected) {
                this.connect();
            }
        }, delay);
    }
    
    handleNotification(data) {
        // Show notification toast
        this.showToast('notification', data.text);
        
        // Update notification badge
        const notifBadge = document.querySelector('.notification-badge');
        if (notifBadge) {
            let count = parseInt(notifBadge.textContent) || 0;
            notifBadge.textContent = count + 1;
            notifBadge.style.display = 'inline-block';
        }
        
        // Play notification sound (optional)
        this.playNotificationSound();
    }
    
    updateResources(data) {
        // Update energy bar
        this.updateProgressBar('energy', data.energy, data.maxenergy);
        
        // Update HP bar
        this.updateProgressBar('hp', data.hp, data.maxhp);
        
        // Update will bar
        this.updateProgressBar('will', data.will, data.maxwill);
        
        // Update brave bar
        this.updateProgressBar('brave', data.brave, data.maxbrave);
        
        // Update currency displays
        this.updateCurrency('primary', data.money);
        this.updateCurrency('secondary', data.crystals);
        
        // Update any resource displays on the page
        this.updateResourceDisplays(data);
    }
    
    updateProgressBar(type, current, max) {
        const percentage = Math.round((current / max) * 100);
        
        // Update sidebar progress bars
        const sidebarBar = document.querySelector(`.sidebar-${type}-progress .progress-bar`);
        if (sidebarBar) {
            sidebarBar.style.width = percentage + '%';
            sidebarBar.textContent = percentage + '%';
        }
        
        // Update main page progress bars
        const mainBars = document.querySelectorAll(`[data-resource="${type}"]`);
        mainBars.forEach(bar => {
            bar.style.width = percentage + '%';
            const label = bar.querySelector('.progress-label');
            if (label) {
                label.textContent = `${current} / ${max}`;
            }
        });
        
        // Update text displays
        const displays = document.querySelectorAll(`[data-${type}-value]`);
        displays.forEach(display => {
            display.textContent = current;
        });
        
        const maxDisplays = document.querySelectorAll(`[data-${type}-max]`);
        maxDisplays.forEach(display => {
            display.textContent = max;
        });
    }
    
    updateCurrency(type, amount) {
        const displays = document.querySelectorAll(`[data-currency="${type}"]`);
        displays.forEach(display => {
            display.textContent = this.formatNumber(amount);
        });
    }
    
    updateResourceDisplays(data) {
        // Update any elements with data-resource attributes
        for (const [key, value] of Object.entries(data)) {
            const elements = document.querySelectorAll(`[data-resource-display="${key}"]`);
            elements.forEach(el => {
                el.textContent = this.formatNumber(value);
            });
        }
    }
    
    showToast(type, message) {
        // Create toast container if it doesn't exist
        let toastContainer = document.getElementById('toast-container');
        if (!toastContainer) {
            toastContainer = document.createElement('div');
            toastContainer.id = 'toast-container';
            toastContainer.style.cssText = `
                position: fixed;
                top: 20px;
                right: 20px;
                z-index: 9999;
                display: flex;
                flex-direction: column;
                gap: 10px;
            `;
            document.body.appendChild(toastContainer);
        }
        
        // Create toast element
        const toast = document.createElement('div');
        toast.className = `toast-notification toast-${type}`;
        toast.style.cssText = `
            background: var(--bg-card);
            color: var(--text-primary);
            padding: 15px 20px;
            border-radius: 8px;
            box-shadow: 0 4px 12px rgba(0,0,0,0.3);
            min-width: 300px;
            animation: slideIn 0.3s ease;
            border-left: 4px solid ${type === 'notification' ? '#58a6ff' : '#28a745'};
        `;
        
        toast.innerHTML = `
            <div style="display: flex; align-items: center; gap: 10px;">
                <i class="fas ${type === 'notification' ? 'fa-bell' : 'fa-check-circle'}"></i>
                <div style="flex: 1;">${message}</div>
                <button onclick="this.parentElement.parentElement.remove()" 
                        style="background: none; border: none; color: var(--text-muted); cursor: pointer;">
                    <i class="fas fa-times"></i>
                </button>
            </div>
        `;
        
        toastContainer.appendChild(toast);
        
        // Auto remove after 5 seconds
        setTimeout(() => {
            toast.style.animation = 'slideOut 0.3s ease';
            setTimeout(() => toast.remove(), 300);
        }, 5000);
    }
    
    playNotificationSound() {
        // Optional: Play a subtle notification sound
        if (localStorage.getItem('enableSounds') === 'true') {
            const audio = new Audio('data:audio/wav;base64,UklGRnoGAABXQVZFZm10IBAAAAABAAEAQB8AAEAfAAABAAgAZGF0YQoGAACBhYqFbF1fdJivrJBhNjVgodDbq2EcBj+a2/LDciUFLIHO8tiJNwgZaLvt559NEAxQp+PwtmMcBjiR1/LMeSwFJHfH8N2QQAoUXrTp66hVFApGn+DyvmwhBCt2w+3ijT0JS7XmzW8hBjeS1/LNeSUFJ3nH8NyPQAoUXrTq66hVFApGn+DyvmwhBCt2w+3ijT0JS7XmzW8hBjeS1/LNeSUFJ3nH8NyPQAoUXrTq66hVFApGn+DyvmwhBCt2w+3ijT0JS7XmzW8hBjeS1/LNeSUFJ3nH8NyPQAoUXrTq66hVFApGn+DyvmwhBCt2w+3ijT0JS7XmzW8hBjeS1/LNeSUFJ3nH8NyPQAoUXrTq66hVFApGn+DyvmwhBCt2w+3ijT0JS7XmzW8hBjeS1/LNeSUFJ3nH8NyPQAoUXrTq66hVFApGn+DyvmwhBCt2w+3ijT0JS7XmzW8hBjeS1/LNeSUFJ3nH8NyPQAoUXrTq66hVFApGn+DyvmwhBCt2w+3ijT0JS7XmzW8hBjeS1/LNeSUFJ3nH8NyPQAoUXrTq66hVFApGn+DyvmwhBCt2w+3ijT0JS7XmzW8hBjeS1/LNeSUFJ3nH8NyPQAoUXrTq66hVFApGn+DyvmwhBCt2w+3ijT0JS7XmzW8hBjeS1/LNeSUFJ3nH8NyPQAoUXrTq66hVFApGn+DyvmwhBCt2w+3ijT0JS7XmzW8hBjeS1/LNeSUFJ3nH8NyPQAoUXrTq66hVFApGn+DyvmwhBCt2w+3ijT0JS7XmzW8hBjeS1/LNeSUFJ3nH8NyPQAoUXrTq66hVFApGn+DyvmwhBCt2w+3ijT0JS7XmzW8hBjeS1/LNeSUFJ3nH8NyPQAoUXrTq66hVFApGn+DyvmwhBCt2w+3ijT0JS7XmzW8hBjeS1/LNeSUFJ3nH8NyPQAoUXrTq66hVFApGn+DyvmwhBCt2w+3ijT0JS7XmzW8hBjeS1/LNeSUFJ3nH8NyPQAoUXrTq66hVFApGn+DyvmwhBCt2w+3ijT0JS7XmzW8hBjeS1/LNeSUFJ3nH8NyPQAoUXrTq66hVFApGn+DyvmwhBCt2w+3ijT0JS7XmzW8hBjeS1/LNeSUFJ3nH8NyPQAoUXrTq66hVFApGn+DyvmwhBCt2w+3ijT0JS7XmzW8hBjeS1/LNeSUFJ3nH8NyPQAoUXrTq66hVFApGn+DyvmwhBCt2w+3ijT0JS7XmzW8hBjeS1/LNeSUFJ3nH8NyPQAoUXrTq66hVFApGn+DyvmwhBCt2w+3ijT0JS7XmzW8hBjeS1/LNeSUFJ3nH8NyPQAoUXrTq66hVFApGn+DyvmwhBCt2w+3ijT0JS7XmzW8hBjeS1/LNeSUFJ3nH8NyPQAoUXrTq66hVFApGn+DyvmwhBCt2w+3ijT0JS7XmzW8hBjeS1/LNeSUFJ3nH8NyPQAoUXrTq66hVFApGn+DyvmwhBCt2w+3ijT0JS7XmzW8hBjeS1/LNeSUFJ3nH8NyPQAoUXrTq66hVFApGn+DyvmwhBCt2w+3ijT0JS7XmzW8hBjeS1/LNeSUFJ3nH8NyPQAoUXrTq66hVFApGn+DyvmwhBCt2w+3ijT0JS7XmzW8hBjeS1/LNeSUFJ3nH8NyPQAoUXrTq66hVFApGn+DyvmwhBCt2w+3ijT0JS7XmzW8hBjeS1/LNeSUFJ3nH8NyPQAoUXrTq66hVFApGn+Dyvmw=');
            audio.volume = 0.3;
            audio.play().catch(() => {});
        }
    }
    
    updateConnectionStatus(status) {
        const indicator = document.getElementById('connection-status');
        if (indicator) {
            indicator.className = `connection-${status}`;
            indicator.title = status === 'connected' ? 'Connected' : 'Disconnected';
        }
    }
    
    formatNumber(num) {
        return new Intl.NumberFormat().format(num);
    }
    
    setupEventListeners() {
        // Reconnect when page becomes visible
        document.addEventListener('visibilitychange', () => {
            if (!document.hidden && !this.isConnected) {
                this.connect();
            }
        });
        
        // Cleanup on page unload
        window.addEventListener('beforeunload', () => {
            if (this.eventSource) {
                this.eventSource.close();
            }
        });
    }
    
    // Public methods
    disconnect() {
        if (this.eventSource) {
            this.eventSource.close();
            this.isConnected = false;
        }
    }
    
    reconnect() {
        this.disconnect();
        this.reconnectAttempts = 0;
        this.connect();
    }
}

// Add CSS for toast animations
const style = document.createElement('style');
style.textContent = `
    @keyframes slideIn {
        from {
            transform: translateX(100%);
            opacity: 0;
        }
        to {
            transform: translateX(0);
            opacity: 1;
        }
    }
    
    @keyframes slideOut {
        from {
            transform: translateX(0);
            opacity: 1;
        }
        to {
            transform: translateX(100%);
            opacity: 0;
        }
    }
    
    .connection-connected {
        width: 8px;
        height: 8px;
        border-radius: 50%;
        background: #28a745;
        animation: pulse 2s infinite;
    }
    
    .connection-disconnected {
        width: 8px;
        height: 8px;
        border-radius: 50%;
        background: #dc3545;
    }
    
    @keyframes pulse {
        0%, 100% {
            box-shadow: 0 0 0 0 rgba(40, 167, 69, 0.7);
        }
        50% {
            box-shadow: 0 0 0 10px rgba(40, 167, 69, 0);
        }
    }
`;
document.head.appendChild(style);

// Initialize realtime client when DOM is ready
if (document.readyState === 'loading') {
    document.addEventListener('DOMContentLoaded', () => {
        window.realtimeClient = new RealtimeClient();
    });
} else {
    window.realtimeClient = new RealtimeClient();
}