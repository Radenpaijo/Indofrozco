<style>
    #toast-container {
        position: fixed !important;
        bottom: 1.5rem !important; /* Pojok kanan bawah */
        right: 1.5rem !important;
        z-index: 999999 !important;
        width: calc(100% - 3rem) !important;
        max-width: 24rem !important;
        display: flex !important;
        flex-direction: column-reverse !important; /* Notifikasi baru muncul di bawah, menumpuk ke atas */
        gap: 0.75rem !important;
        pointer-events: none !important;
    }

    .toast-card {
        position: relative !important;
        pointer-events: auto !important;
        background-color: #ffffff !important;
        color: #1e293b !important;
        border-radius: 0.875rem !important;
        box-shadow: 0 10px 25px -5px rgba(15, 23, 42, 0.12), 0 8px 10px -6px rgba(15, 23, 42, 0.06), 0 0 0 1px rgba(0, 0, 0, 0.05) !important;
        padding: 0.875rem 1rem !important;
        display: flex !important;
        align-items: flex-start !important;
        gap: 0.75rem !important;
        overflow: hidden !important;
        transform: translateX(120%) scale(0.96) !important;
        opacity: 0 !important;
        transition: transform 0.35s cubic-bezier(0.16, 1, 0.3, 1), opacity 0.3s ease !important;
    }

    .toast-card.toast-show {
        transform: translateX(0) scale(1) !important;
        opacity: 1 !important;
    }

    .toast-card.toast-hide {
        transform: translateX(120%) scale(0.96) !important;
        opacity: 0 !important;
    }

    /* Aksen border serasi status notifikasi */
    .toast-card.toast-success {
        border-left: 4px solid #10b981 !important;
        border-top: 1px solid #d1fae5 !important;
        border-right: 1px solid #d1fae5 !important;
        border-bottom: 1px solid #d1fae5 !important;
    }
    .toast-card.toast-error {
        border-left: 4px solid #f43f5e !important;
        border-top: 1px solid #ffe4e6 !important;
        border-right: 1px solid #ffe4e6 !important;
        border-bottom: 1px solid #ffe4e6 !important;
    }
    .toast-card.toast-warning {
        border-left: 4px solid #f59e0b !important;
        border-top: 1px solid #fef3c7 !important;
        border-right: 1px solid #fef3c7 !important;
        border-bottom: 1px solid #fef3c7 !important;
    }
    .toast-card.toast-info {
        border-left: 4px solid #0ea5e9 !important;
        border-top: 1px solid #e0f2fe !important;
        border-right: 1px solid #e0f2fe !important;
        border-bottom: 1px solid #e0f2fe !important;
    }

    .toast-icon-wrap {
        flex-shrink: 0 !important;
        padding: 0.45rem !important;
        border-radius: 0.5rem !important;
        display: flex !important;
        align-items: center !important;
        justify-content: center !important;
    }
    .toast-icon-success { background-color: #ecfdf5 !important; color: #059669 !important; }
    .toast-icon-error { background-color: #fff1f2 !important; color: #e11d48 !important; }
    .toast-icon-warning { background-color: #fffbeb !important; color: #d97706 !important; }
    .toast-icon-info { background-color: #f0f9ff !important; color: #0284c7 !important; }

    .toast-content {
        flex: 1 1 0% !important;
        min-width: 0 !important;
        padding-right: 0.25rem !important;
    }

    .toast-title {
        font-size: 0.75rem !important;
        font-weight: 700 !important;
        text-transform: uppercase !important;
        letter-spacing: 0.05em !important;
        line-height: 1.2 !important;
        margin: 0 !important;
    }
    .toast-title-success { color: #065f46 !important; }
    .toast-title-error { color: #9f1239 !important; }
    .toast-title-warning { color: #92400e !important; }
    .toast-title-info { color: #075985 !important; }

    .toast-msg {
        font-size: 0.8125rem !important;
        font-weight: 500 !important;
        color: #334155 !important;
        margin-top: 0.35rem !important;
        line-height: 1.4 !important;
        word-break: break-word !important;
    }

    .toast-close-btn {
        flex-shrink: 0 !important;
        color: #94a3b8 !important;
        background: transparent !important;
        border: none !important;
        cursor: pointer !important;
        padding: 0.25rem !important;
        border-radius: 0.375rem !important;
        line-height: 0 !important;
        transition: color 0.15s ease, background-color 0.15s ease !important;
    }
    .toast-close-btn:hover {
        color: #475569 !important;
        background-color: #f1f5f9 !important;
    }

    .toast-progress-track {
        position: absolute !important;
        bottom: 0 !important;
        left: 0 !important;
        right: 0 !important;
        height: 3px !important;
        background-color: #f1f5f9 !important;
        overflow: hidden !important;
    }

    .toast-progress-fill {
        height: 100% !important;
        width: 100% !important;
    }
    .toast-progress-success { background-color: #10b981 !important; }
    .toast-progress-error { background-color: #f43f5e !important; }
    .toast-progress-warning { background-color: #f59e0b !important; }
    .toast-progress-info { background-color: #0ea5e9 !important; }
</style>

<div id="toast-container" aria-live="polite" aria-atomic="true"></div>

<script>
    (function () {
        if (window.__toastEngineLoaded) return;
        window.__toastEngineLoaded = true;

        const ICONS = {
            success: `<svg style="width:1.25rem;height:1.25rem;" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7" /></svg>`,
            error: `<svg style="width:1.25rem;height:1.25rem;" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M6 18L18 6M6 6l12 12" /></svg>`,
            warning: `<svg style="width:1.25rem;height:1.25rem;" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" /></svg>`,
            info: `<svg style="width:1.25rem;height:1.25rem;" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" /></svg>`
        };

        const TITLES = {
            success: 'Berhasil',
            error: 'Gagal / Terjadi Kesalahan',
            warning: 'Peringatan',
            info: 'Informasi'
        };

        window.showToast = function (message, type = 'success', title = null, duration = 4500) {
            const container = document.getElementById('toast-container');
            if (!container) return;

            const safeType = ['success', 'error', 'warning', 'info'].includes(type) ? type : 'info';
            const iconSvg = ICONS[safeType];
            const displayTitle = title || TITLES[safeType];

            // Filter teks error database mentah menjadi kalimat ramah pengguna
            let safeMessage = message;
            if (typeof message === 'string' && (message.includes('SQLSTATE[') || message.includes('QueryException') || message.includes('Unknown column'))) {
                safeMessage = 'Terjadi kendala pada sistem database saat memproses data. Silakan coba kembali atau hubungi administrator.';
            }

            const toast = document.createElement('div');
            toast.className = `toast-card toast-${safeType}`;

            toast.innerHTML = `
                <div class="toast-icon-wrap toast-icon-${safeType}">
                    ${iconSvg}
                </div>
                <div class="toast-content">
                    <h5 class="toast-title toast-title-${safeType}">${displayTitle}</h5>
                    <div class="toast-msg">${safeMessage}</div>
                </div>
                <button type="button" aria-label="Tutup notifikasi" class="toast-close-btn">
                    <svg style="width:1rem;height:1rem;" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M6 18L18 6M6 6l12 12" />
                    </svg>
                </button>
                <div class="toast-progress-track">
                    <div class="toast-progress-fill toast-progress-${safeType}" style="transition: width ${duration}ms linear;"></div>
                </div>
            `;

            container.appendChild(toast);

            // Animate Slide In from bottom-right
            requestAnimationFrame(() => {
                toast.classList.add('toast-show');

                // Animate Progress Bar countdown
                setTimeout(() => {
                    const progressBar = toast.querySelector('.toast-progress-fill');
                    if (progressBar) progressBar.style.width = '0%';
                }, 50);
            });

            let timerId = null;
            let startTime = Date.now();
            let remainingTime = duration;

            function dismiss() {
                if (!toast.parentNode) return;
                toast.classList.remove('toast-show');
                toast.classList.add('toast-hide');
                setTimeout(() => {
                    if (toast.parentNode) toast.remove();
                }, 350);
            }

            function startTimer() {
                startTime = Date.now();
                timerId = setTimeout(dismiss, remainingTime);
            }

            function pauseTimer() {
                clearTimeout(timerId);
                remainingTime -= (Date.now() - startTime);
            }

            startTimer();

            toast.addEventListener('mouseenter', pauseTimer);
            toast.addEventListener('mouseleave', startTimer);

            const closeBtn = toast.querySelector('.toast-close-btn');
            if (closeBtn) {
                closeBtn.addEventListener('click', function (e) {
                    e.stopPropagation();
                    clearTimeout(timerId);
                    dismiss();
                });
            }
        };

        window.toast = {
            success: (msg, title, duration) => window.showToast(msg, 'success', title, duration),
            error: (msg, title, duration) => window.showToast(msg, 'error', title, duration),
            warning: (msg, title, duration) => window.showToast(msg, 'warning', title, duration),
            info: (msg, title, duration) => window.showToast(msg, 'info', title, duration)
        };

        function triggerFlashMessages() {
            @if(session('success'))
                window.toast.success({!! json_encode(session('success')) !!});
            @endif

            @if(session('error'))
                window.toast.error({!! json_encode(session('error')) !!});
            @endif

            @if(session('warning'))
                window.toast.warning({!! json_encode(session('warning')) !!});
            @endif

            @if(session('info'))
                window.toast.info({!! json_encode(session('info')) !!});
            @endif

            @if(session('status') && !in_array(session('status'), ['profile-updated', 'password-updated', 'verification-link-sent']))
                window.toast.info({!! json_encode(session('status')) !!});
            @endif

            @if(isset($errors) && $errors->any())
                @php
                    $errorList = implode('<br>• ', $errors->all());
                @endphp
                window.toast.error({!! json_encode('• ' . $errorList) !!}, 'Terdapat Kesalahan Validasi', 6000);
            @endif
        }

        if (document.readyState === 'loading') {
            document.addEventListener('DOMContentLoaded', triggerFlashMessages);
        } else {
            triggerFlashMessages();
        }
    })();
</script>
