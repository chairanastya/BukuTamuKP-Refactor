class FormProtection {
    constructor() {
        this.submittingForms = new Set();
        this.rateLimiters = new Map();
    }

    protectForm(form, options = {}) {
        const {
            cooldownMs = 1000,
            disableButton = true,
            loadingText = 'Mengirim...',
            onSubmit = null,
            onError = null
        } = options;

        const formId = form.id || this.generateFormId(form);
        
        form.addEventListener('submit', (e) => {
            if (this.submittingForms.has(formId)) {
                e.preventDefault();
                console.warn('Form submission blocked: already submitting');
                return false;
            }

            if (this.isRateLimited(formId, cooldownMs)) {
                e.preventDefault();
                const waitTime = this.getRemainingCooldown(formId, cooldownMs);
                console.warn(`Rate limited: please wait ${waitTime}ms`);
                
                if (onError) {
                    onError(new Error(`Mohon tunggu ${Math.ceil(waitTime / 1000)} detik`));
                }
                return false;
            }

            this.submittingForms.add(formId);
            this.setRateLimit(formId);

            const submitButton = form.querySelector('button[type="submit"], input[type="submit"]');
            let originalButtonContent = null;

            if (disableButton && submitButton) {
                originalButtonContent = submitButton.innerHTML || submitButton.value;
                submitButton.disabled = true;
                
                if (submitButton.tagName === 'BUTTON') {
                    submitButton.innerHTML = `<span class="animate-pulse">${loadingText}</span>`;
                } else {
                    submitButton.value = loadingText;
                }
            }

            if (onSubmit) {
                onSubmit(form);
            }

            setTimeout(() => {
                this.submittingForms.delete(formId);
                
                if (disableButton && submitButton && originalButtonContent) {
                    submitButton.disabled = false;
                    if (submitButton.tagName === 'BUTTON') {
                        submitButton.innerHTML = originalButtonContent;
                    } else {
                        submitButton.value = originalButtonContent;
                    }
                }
            }, 15000);
        });
    }

    isRateLimited(key, cooldownMs) {
        const lastTime = this.rateLimiters.get(key);
        if (!lastTime) return false;
        
        return (Date.now() - lastTime) < cooldownMs;
    }

    getRemainingCooldown(key, cooldownMs) {
        const lastTime = this.rateLimiters.get(key);
        if (!lastTime) return 0;
        
        const remaining = cooldownMs - (Date.now() - lastTime);
        return Math.max(0, remaining);
    }

    setRateLimit(key) {
        this.rateLimiters.set(key, Date.now());
    }

    static debounce(func, waitMs) {
        let timeout;
        return function executedFunction(...args) {
            const later = () => {
                clearTimeout(timeout);
                func(...args);
            };
            clearTimeout(timeout);
            timeout = setTimeout(later, waitMs);
        };
    }

    static throttle(func, limitMs) {
        let inThrottle;
        return function executedFunction(...args) {
            if (!inThrottle) {
                func.apply(this, args);
                inThrottle = true;
                setTimeout(() => inThrottle = false, limitMs);
            }
        };
    }

    generateFormId(form) {
        return `form_${form.action}_${Date.now()}`;
    }

    async rateLimitedFetch(url, options = {}, cooldownMs = 1000) {
        const key = `fetch_${url}`;
        
        if (this.isRateLimited(key, cooldownMs)) {
            const waitTime = this.getRemainingCooldown(key, cooldownMs);
            throw new Error(`Rate limited: please wait ${Math.ceil(waitTime / 1000)}s`);
        }

        this.setRateLimit(key);
        
        try {
            const response = await fetch(url, options);

            if (response.status === 429) {
                const retryAfter = response.headers.get('Retry-After') || 60;
                throw new Error(`Terlalu banyak permintaan. Coba lagi dalam ${retryAfter} detik`);
            }
            
            return response;
        } catch (error) {
            console.error('Fetch error:', error);
            throw error;
        }
    }
}

window.formProtection = new FormProtection();

document.addEventListener('DOMContentLoaded', () => {
    document.querySelectorAll('form[data-protect]').forEach(form => {
        const cooldown = parseInt(form.dataset.protectCooldown) || 1000;
        const loadingText = form.dataset.protectLoading || 'Mengirim...';
        
        window.formProtection.protectForm(form, {
            cooldownMs: cooldown,
            loadingText: loadingText
        });
    });
});

if (typeof module !== 'undefined' && module.exports) {
    module.exports = FormProtection;
}
