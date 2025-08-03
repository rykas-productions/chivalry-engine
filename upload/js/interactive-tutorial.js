// Interactive Tutorial System for Chivalry Engine
class InteractiveTutorial {
    constructor() {
        this.currentStep = 0;
        this.steps = [];
        this.overlay = null;
        this.isActive = false;
        this.completedTutorials = JSON.parse(localStorage.getItem('completed_tutorials') || '[]');
    }
    
    // Start a tutorial sequence
    start(tutorialId, steps) {
        if (this.completedTutorials.includes(tutorialId)) {
            return; // Already completed
        }
        
        this.tutorialId = tutorialId;
        this.steps = steps;
        this.currentStep = 0;
        this.isActive = true;
        
        this.showStep(this.steps[0]);
    }
    
    // Show current step
    showStep(step) {
        this.createOverlay();
        this.highlightElement(step.target);
        this.showTooltip(step);
        
        // Add pulsing arrow if target exists
        if (step.target) {
            this.addPulsingArrow(step.target, step.arrowPosition || 'top');
        }
        
        // Auto-advance for certain steps
        if (step.autoAdvance) {
            setTimeout(() => {
                this.nextStep();
            }, step.autoAdvance);
        }
    }
    
    // Create tutorial overlay
    createOverlay() {
        if (this.overlay) {
            this.overlay.remove();
        }
        
        this.overlay = document.createElement('div');
        this.overlay.className = 'tutorial-overlay';
        this.overlay.style.cssText = `
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: rgba(0, 0, 0, 0.8);
            z-index: 10000;
            pointer-events: auto;
            animation: fadeIn 0.3s ease-out;
        `;
        
        document.body.appendChild(this.overlay);
    }
    
    // Highlight target element
    highlightElement(selector) {
        // Clear previous highlights
        document.querySelectorAll('.tutorial-highlight').forEach(el => {
            el.classList.remove('tutorial-highlight');
            el.style.boxShadow = '';
            el.style.position = '';
            el.style.zIndex = '';
        });
        
        if (selector) {
            const element = document.querySelector(selector);
            if (element) {
                element.classList.add('tutorial-highlight');
                element.style.boxShadow = '0 0 0 9999px rgba(0,0,0,0.8), 0 0 20px 5px #ffd700';
                element.style.position = 'relative';
                element.style.zIndex = '10001';
                element.style.borderRadius = '8px';
                
                // Scroll element into view
                element.scrollIntoView({ 
                    behavior: 'smooth', 
                    block: 'center',
                    inline: 'center'
                });
                
                // Add glow animation
                element.style.animation = 'tutorialGlow 2s ease-in-out infinite';
            }
        }
    }
    
    // Show tooltip with step information
    showTooltip(step) {
        const tooltip = document.createElement('div');
        tooltip.className = 'tutorial-tooltip';
        
        const target = step.target ? document.querySelector(step.target) : null;
        const rect = target ? target.getBoundingClientRect() : null;
        
        // Position tooltip
        let tooltipStyle = `
            position: fixed;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            padding: 25px;
            border-radius: 15px;
            max-width: 400px;
            min-width: 300px;
            box-shadow: 0 10px 30px rgba(0,0,0,0.3);
            z-index: 10002;
            animation: tooltipSlideIn 0.4s ease-out;
        `;
        
        // Calculate position based on target
        if (rect) {
            const spaceAbove = rect.top;
            const spaceBelow = window.innerHeight - rect.bottom;
            const spaceLeft = rect.left;
            const spaceRight = window.innerWidth - rect.right;
            
            if (spaceBelow > 200) {
                // Show below
                tooltipStyle += `top: ${rect.bottom + 20}px; left: ${Math.max(20, rect.left)}px;`;
            } else if (spaceAbove > 200) {
                // Show above
                tooltipStyle += `bottom: ${window.innerHeight - rect.top + 20}px; left: ${Math.max(20, rect.left)}px;`;
            } else if (spaceRight > 420) {
                // Show to the right
                tooltipStyle += `top: ${Math.max(20, rect.top)}px; left: ${rect.right + 20}px;`;
            } else {
                // Show to the left
                tooltipStyle += `top: ${Math.max(20, rect.top)}px; right: ${window.innerWidth - rect.left + 20}px;`;
            }
        } else {
            // Center on screen
            tooltipStyle += `
                top: 50%;
                left: 50%;
                transform: translate(-50%, -50%);
            `;
        }
        
        tooltip.style.cssText = tooltipStyle;
        
        // Create tooltip content
        tooltip.innerHTML = `
            <div class="tutorial-header">
                <h3 style="margin: 0 0 10px 0; font-size: 1.3rem;">
                    ${step.icon || '🎯'} ${step.title}
                </h3>
                <div class="tutorial-progress">
                    Step ${this.currentStep + 1} of ${this.steps.length}
                </div>
            </div>
            
            <div class="tutorial-content" style="margin: 15px 0;">
                <p style="margin: 0; line-height: 1.5; font-size: 1rem;">
                    ${step.description}
                </p>
                
                ${step.tip ? `
                    <div style="margin-top: 15px; padding: 10px; background: rgba(255,255,255,0.1); border-radius: 8px; font-size: 0.9rem;">
                        💡 <strong>Tip:</strong> ${step.tip}
                    </div>
                ` : ''}
            </div>
            
            <div class="tutorial-actions" style="display: flex; justify-content: space-between; align-items: center; margin-top: 20px;">
                <div class="tutorial-controls">
                    ${this.currentStep > 0 ? `
                        <button class="btn btn-outline-light btn-sm" onclick="tutorial.previousStep()">
                            ← Previous
                        </button>
                    ` : ''}
                    
                    <button class="btn btn-light btn-sm" onclick="tutorial.skipTutorial()" style="margin-left: 10px;">
                        Skip Tutorial
                    </button>
                </div>
                
                <div class="tutorial-navigation">
                    ${step.action ? `
                        <button class="btn btn-warning" onclick="${step.action}">
                            ${step.actionText || 'Try It!'}
                        </button>
                    ` : `
                        <button class="btn btn-success" onclick="tutorial.nextStep()">
                            ${this.currentStep === this.steps.length - 1 ? 'Finish' : 'Next'} →
                        </button>
                    `}
                </div>
            </div>
        `;
        
        this.overlay.appendChild(tooltip);
        
        // Add interactive elements if specified
        if (step.interactive) {
            this.addInteractiveElements(step);
        }
    }
    
    // Add pulsing arrow pointing to target
    addPulsingArrow(selector, position) {
        const target = document.querySelector(selector);
        if (!target) return;
        
        const arrow = document.createElement('div');
        arrow.className = 'tutorial-arrow';
        arrow.innerHTML = '▼';
        
        const rect = target.getBoundingClientRect();
        let arrowStyle = `
            position: fixed;
            font-size: 2rem;
            color: #ffd700;
            z-index: 10002;
            animation: tutorialPulse 2s ease-in-out infinite;
            text-shadow: 0 0 10px rgba(255, 215, 0, 0.8);
        `;
        
        switch (position) {
            case 'top':
                arrow.innerHTML = '▼';
                arrowStyle += `top: ${rect.top - 40}px; left: ${rect.left + rect.width / 2 - 15}px;`;
                break;
            case 'bottom':
                arrow.innerHTML = '▲';
                arrowStyle += `top: ${rect.bottom + 10}px; left: ${rect.left + rect.width / 2 - 15}px;`;
                break;
            case 'left':
                arrow.innerHTML = '▶';
                arrowStyle += `top: ${rect.top + rect.height / 2 - 15}px; left: ${rect.left - 40}px;`;
                break;
            case 'right':
                arrow.innerHTML = '◀';
                arrowStyle += `top: ${rect.top + rect.height / 2 - 15}px; left: ${rect.right + 10}px;`;
                break;
        }
        
        arrow.style.cssText = arrowStyle;
        this.overlay.appendChild(arrow);
    }
    
    // Add interactive elements for hands-on learning
    addInteractiveElements(step) {
        if (step.interactive.type === 'click') {
            const target = document.querySelector(step.target);
            if (target) {
                const originalHandler = target.onclick;
                target.onclick = (e) => {
                    if (originalHandler) originalHandler(e);
                    this.nextStep();
                };
            }
        }
    }
    
    // Navigate to next step
    nextStep() {
        if (this.currentStep < this.steps.length - 1) {
            this.currentStep++;
            this.showStep(this.steps[this.currentStep]);
        } else {
            this.completeTutorial();
        }
    }
    
    // Navigate to previous step
    previousStep() {
        if (this.currentStep > 0) {
            this.currentStep--;
            this.showStep(this.steps[this.currentStep]);
        }
    }
    
    // Skip entire tutorial
    skipTutorial() {
        if (confirm('Are you sure you want to skip this tutorial? You can restart it later from the help menu.')) {
            this.cleanup();
        }
    }
    
    // Complete tutorial
    completeTutorial() {
        // Mark as completed
        this.completedTutorials.push(this.tutorialId);
        localStorage.setItem('completed_tutorials', JSON.stringify(this.completedTutorials));
        
        // Show completion message
        showBootstrapToast('Tutorial Complete!', 'Great job! You\'ve completed the tutorial.', 'success', 5000);
        
        // Trigger celebration
        createCelebrationParticles();
        
        this.cleanup();
    }
    
    // Clean up tutorial elements
    cleanup() {
        this.isActive = false;
        
        if (this.overlay) {
            this.overlay.style.animation = 'fadeOut 0.3s ease-out';
            setTimeout(() => {
                if (this.overlay) {
                    this.overlay.remove();
                    this.overlay = null;
                }
            }, 300);
        }
        
        // Remove highlights
        document.querySelectorAll('.tutorial-highlight').forEach(el => {
            el.classList.remove('tutorial-highlight');
            el.style.boxShadow = '';
            el.style.position = '';
            el.style.zIndex = '';
            el.style.animation = '';
        });
    }
}

// Tutorial definitions
const TUTORIALS = {
    'getting-started': [
        {
            title: 'Welcome to Chivalry Engine!',
            description: 'This tutorial will guide you through the basics of the game. Let\'s start by exploring your dashboard.',
            icon: '👋',
            tip: 'You can skip this tutorial at any time, but we recommend completing it to get the most out of the game.'
        },
        {
            title: 'Your Stats',
            description: 'These bars show your current health, energy, and experience. Keep an eye on them as you play!',
            target: '.quick-stats',
            icon: '📊',
            tip: 'Your stats regenerate over time, so check back regularly.'
        },
        {
            title: 'Quick Actions',
            description: 'Use these buttons to quickly access the most important game features.',
            target: '.card .d-grid',
            icon: '⚡',
            tip: 'These are the actions you\'ll use most often in the game.'
        },
        {
            title: 'Navigation Menu',
            description: 'Click this button to open the main navigation menu where you can access all game features.',
            target: '#show-sidebar',
            icon: '🧭',
            action: 'document.getElementById("show-sidebar").click(); tutorial.nextStep();',
            actionText: 'Open Menu'
        },
        {
            title: 'Explore and Have Fun!',
            description: 'You\'re all set! Explore the game, complete missions, and become the ultimate champion!',
            icon: '🎉',
            tip: 'Remember, you can always access help and tutorials from the navigation menu.'
        }
    ],
    
    'combat-tutorial': [
        {
            title: 'Combat Basics',
            description: 'Learn how to fight other players and NPCs in the game.',
            icon: '⚔️'
        },
        {
            title: 'Attack Button',
            description: 'Click here to find opponents to fight. Choose your battles wisely!',
            target: 'a[href="attackselect.php"]',
            icon: '🎯'
        },
        {
            title: 'Training',
            description: 'Visit the gym to increase your stats and become stronger in combat.',
            target: 'a[href="gym.php"]',
            icon: '💪'
        }
    ]
};

// Initialize tutorial system
const tutorial = new InteractiveTutorial();

// Auto-start tutorial for new players
document.addEventListener('DOMContentLoaded', () => {
    // Check if this is a new player
    const isNewPlayer = !localStorage.getItem('tutorial_completed');
    
    if (isNewPlayer) {
        setTimeout(() => {
            tutorial.start('getting-started', TUTORIALS['getting-started']);
        }, 2000); // Wait 2 seconds for page to settle
    }
});

// Add tutorial CSS
const tutorialStyles = document.createElement('style');
tutorialStyles.textContent = `
    @keyframes tutorialGlow {
        0%, 100% { box-shadow: 0 0 0 9999px rgba(0,0,0,0.8), 0 0 20px 5px #ffd700; }
        50% { box-shadow: 0 0 0 9999px rgba(0,0,0,0.8), 0 0 30px 10px #ffd700; }
    }
    
    @keyframes tutorialPulse {
        0%, 100% { transform: scale(1); }
        50% { transform: scale(1.2); }
    }
    
    @keyframes tooltipSlideIn {
        from { opacity: 0; transform: translateY(-20px); }
        to { opacity: 1; transform: translateY(0); }
    }
    
    .tutorial-overlay {
        backdrop-filter: blur(2px);
    }
    
    .tutorial-tooltip {
        font-family: 'Inter', sans-serif;
    }
    
    .tutorial-progress {
        background: rgba(255,255,255,0.2);
        padding: 5px 10px;
        border-radius: 15px;
        font-size: 0.8rem;
        display: inline-block;
    }
    
    .tutorial-highlight {
        transition: all 0.3s ease;
    }
`;
document.head.appendChild(tutorialStyles);

// Export for global use
window.tutorial = tutorial;
window.TUTORIALS = TUTORIALS;