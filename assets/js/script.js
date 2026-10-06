// =====================================================
// SIGALING PADANG - MAIN JAVASCRIPT & UI UTILITIES
// =====================================================

// Eksekusi langsung
initLucideIcons();

document.addEventListener('DOMContentLoaded', function() {
    // 1. Inisialisasi Lucide Icons jika library tersedia
    initLucideIcons();

    // 2. Inisialisasi Sidebar Mobile Toggle (Admin / Petugas)
    initSidebarMobile();

    // 3. Highlight Nav Link Aktif
    setActiveNavLink();

    // 4. Inisialisasi Tooltip Bootstrap (jika bootstrap tersedia)
    initBootstrapComponents();
});

window.addEventListener('load', function() {
    initLucideIcons();
});

// =====================================================
// FUNCTION: Initialize Lucide Icons
// =====================================================
function initLucideIcons() {
    if (typeof lucide !== 'undefined' && lucide.createIcons) {
        try {
            lucide.createIcons();
        } catch (e) {
            console.warn('Lucide icon init warning:', e);
        }
    }
}

// =====================================================
// FUNCTION: Mobile Sidebar Drawer
// =====================================================
function initSidebarMobile() {
    const sidebar = document.getElementById('sidebar');
    const toggleBtn = document.getElementById('toggle-sidebar');
    let backdrop = document.querySelector('.sidebar-backdrop');

    if (!backdrop && sidebar) {
        backdrop = document.createElement('div');
        backdrop.className = 'sidebar-backdrop';
        document.body.appendChild(backdrop);
    }

    if (toggleBtn && sidebar) {
        toggleBtn.addEventListener('click', function(e) {
            e.stopPropagation();
            sidebar.classList.toggle('show');
            if (backdrop) {
                backdrop.style.display = sidebar.classList.contains('show') ? 'block' : 'none';
            }
        });
    }

    if (backdrop && sidebar) {
        backdrop.addEventListener('click', function() {
            sidebar.classList.remove('show');
            backdrop.style.display = 'none';
        });
    }
}

// =====================================================
// FUNCTION: Compact Toast Notification
// =====================================================
function showToast(message, type = 'info', duration = 3500) {
    let container = document.getElementById('toast-container');
    if (!container) {
        container = document.createElement('div');
        container.id = 'toast-container';
        document.body.appendChild(container);
    }

    const toast = document.createElement('div');
    toast.className = `toast-compact toast-${type}`;
    
    let iconName = 'info';
    if (type === 'success') iconName = 'check-circle';
    if (type === 'danger' || type === 'error') iconName = 'alert-circle';
    if (type === 'warning') iconName = 'alert-triangle';

    toast.innerHTML = `
        <div style="display:flex; align-items:center; gap:0.5rem;">
            <i data-lucide="${iconName}" class="lucide-sm"></i>
            <span>${message}</span>
        </div>
        <button type="button" style="background:none; border:none; cursor:pointer; color:#94A3B8; padding:0; line-height:1;" onclick="this.parentElement.remove()">
            <i data-lucide="x" style="width:14px; height:14px;"></i>
        </button>
    `;

    container.appendChild(toast);
    initLucideIcons();

    setTimeout(() => {
        toast.style.opacity = '0';
        toast.style.transform = 'translateY(-10px)';
        toast.style.transition = 'all 0.25s ease';
        setTimeout(() => toast.remove(), 250);
    }, duration);
}

// Alias kompatibilitas untuk showAlert lama
function showAlert(message, type = 'info', targetElement = null) {
    const alertType = (type === 'danger' || type === 'error') ? 'danger' : (type === 'success' ? 'success' : (type === 'warning' ? 'warning' : 'info'));
    
    // Jika ada alert-container di halaman, render inline bersih juga
    const container = targetElement || document.getElementById('alert-container');
    if (container) {
        container.innerHTML = `
            <div class="alert-clean alert-clean-${alertType} alert-dismissible fade show" role="alert">
                <i data-lucide="${alertType === 'success' ? 'check-circle' : (alertType === 'danger' ? 'alert-circle' : 'info')}" class="lucide-sm" style="flex-shrink:0; margin-top:2px;"></i>
                <div style="flex:1;">${message}</div>
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close" style="font-size:0.75rem;"></button>
            </div>
        `;
        initLucideIcons();
    }
    
    // Tampilkan juga sebagai toast non-intrusive
    showToast(message.replace(/<\/?[^>]+(>|$)/g, ""), alertType);
}

// =====================================================
// FUNCTION: Set Active Navigation Link
// =====================================================
function setActiveNavLink() {
    const currentLocation = location.pathname.split('/').pop() || 'index.php';
    const menuItems = document.querySelectorAll('.navbar-sigaling .nav-link, .admin-sidebar-nav li a');
    
    menuItems.forEach(link => {
        const href = link.getAttribute('href');
        if (href && (href === currentLocation || href.endsWith('/' + currentLocation))) {
            link.classList.add('active');
        }
    });
}

// =====================================================
// FUNCTION: Initialize Bootstrap Components
// =====================================================
function initBootstrapComponents() {
    if (typeof bootstrap !== 'undefined') {
        const tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'));
        tooltipTriggerList.map(function (tooltipTriggerEl) {
            return new bootstrap.Tooltip(tooltipTriggerEl);
        });
    }
}

// =====================================================
// FUNCTION: Password Visibility Toggle
// =====================================================
function togglePasswordVisibility(inputId, toggleIconId) {
    const input = document.getElementById(inputId);
    const icon = document.getElementById(toggleIconId);
    if (!input) return;

    const isPassword = input.getAttribute('type') === 'password';
    input.setAttribute('type', isPassword ? 'text' : 'password');
    if (icon) {
        icon.setAttribute('data-lucide', isPassword ? 'eye-off' : 'eye');
        initLucideIcons();
    }
}
    if (!input) return;

    const isPassword = input.getAttribute('type') === 'password';
    input.setAttribute('type', isPassword ? 'text' : 'password');
    if (icon) {
        icon.setAttribute('data-lucide', isPassword ? 'eye-off' : 'eye');
        initLucideIcons();
    }
}

// =====================================================
// FUNCTION: E-Government Realtime Clock (WIB)
// =====================================================
function initGovClock() {
    const clockEl = document.getElementById('gov-realtime-clock');
    if (!clockEl) return;

    const days = ['Minggu', 'Senin', 'Selasa', 'Rabu', 'Kamis', 'Jumat', 'Sabtu'];
    const months = ['Januari', 'Februari', 'Maret', 'April', 'Mei', 'Juni', 'Juli', 'Agustus', 'September', 'Oktober', 'November', 'Desember'];

    function updateClock() {
        const now = new Date();
        const dayName = days[now.getDay()];
        const date = now.getDate();
        const monthName = months[now.getMonth()];
        const year = now.getFullYear();
        const hours = String(now.getHours()).padStart(2, '0');
        const minutes = String(now.getMinutes()).padStart(2, '0');
        const seconds = String(now.getSeconds()).padStart(2, '0');

        clockEl.innerHTML = `${dayName}, ${date} ${monthName} ${year} &bull; ${hours}:${minutes}:${seconds} WIB`;
    }

    updateClock();
    setInterval(updateClock, 1000);
}

// =====================================================
// FUNCTION: Accessibility Font & Contrast Controller
// =====================================================
function initGovA11y() {
    const btnInc = document.querySelector('[data-a11y="increase"]');
    const btnReset = document.querySelector('[data-a11y="reset"]');
    const btnDec = document.querySelector('[data-a11y="decrease"]');
    const btnContrast = document.querySelector('[data-a11y="contrast"]');

    if (btnInc) {
        btnInc.addEventListener('click', function(e) {
            e.preventDefault();
            if (document.body.classList.contains('text-lg')) {
                document.body.classList.remove('text-lg');
                document.body.classList.add('text-xl');
            } else if (!document.body.classList.contains('text-xl')) {
                document.body.classList.add('text-lg');
            }
        });
    }

    if (btnDec) {
        btnDec.addEventListener('click', function(e) {
            e.preventDefault();
            if (document.body.classList.contains('text-xl')) {
                document.body.classList.remove('text-xl');
                document.body.classList.add('text-lg');
            } else if (document.body.classList.contains('text-lg')) {
                document.body.classList.remove('text-lg');
            }
        });
    }

    if (btnReset) {
        btnReset.addEventListener('click', function(e) {
            e.preventDefault();
            document.body.classList.remove('text-lg', 'text-xl');
        });
    }

    if (btnContrast) {
        btnContrast.addEventListener('click', function(e) {
            e.preventDefault();
            document.body.classList.toggle('contrast-high');
        });
    }
}

// =====================================================
// FUNCTION: Quick Tracker Submission Handler
// =====================================================
function initGovQuickTracker() {
    const forms = document.querySelectorAll('.gov-quick-tracker-form');
    forms.forEach(form => {
        form.addEventListener('submit', function(e) {
            e.preventDefault();
            const input = form.querySelector('input[name="kode"]');
            if (input && input.value.trim()) {
                const code = encodeURIComponent(input.value.trim().toUpperCase());
                // Deteksi relative path apakah dari root atau subfolder
                const isSubfolder = window.location.pathname.includes('/masyarakat/') || 
                                    window.location.pathname.includes('/admin/') || 
                                    window.location.pathname.includes('/petugas/') ||
                                    window.location.pathname.includes('/auth/');
                const targetUrl = isSubfolder ? `../masyarakat/tracking.php?kode=${code}` : `masyarakat/tracking.php?kode=${code}`;
                window.location.href = targetUrl;
            } else {
                showToast('Silakan masukkan Kode Laporan Anda terlebih dahulu.', 'warning');
            }
        });
    });
}
