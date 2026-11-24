/* ==========================================================================
   Khu quản trị & CSKH: đóng/mở sidebar responsive + xác nhận thao tác xóa.
   ========================================================================== */
(function () {
    'use strict';

    var body = document.body;
    var sidebar = document.getElementById('adminSidebar');
    var toggle = document.getElementById('sidebarToggle');
    var backdrop = document.getElementById('adminBackdrop');
    var scrollStorageKey = body.classList.contains('staff-shell')
        ? 'ltr_staff_sidebar_scroll'
        : 'ltr_admin_sidebar_scroll';

    function saveSidebarScroll() {
        if (!sidebar) {
            return;
        }
        try {
            window.sessionStorage.setItem(scrollStorageKey, String(sidebar.scrollTop));
        } catch (error) {
            // Bỏ qua nếu trình duyệt chặn sessionStorage.
        }
    }

    function restoreSidebarScroll() {
        if (!sidebar) {
            return;
        }
        var saved = null;
        try {
            saved = window.sessionStorage.getItem(scrollStorageKey);
        } catch (error) {
            saved = null;
        }

        if (saved !== null) {
            var y = parseInt(saved, 10);
            if (!isNaN(y) && y >= 0) {
                sidebar.scrollTop = y;
            }
        }

        // Đảm bảo mục đang active vẫn nằm trong vùng nhìn thấy của sidebar.
        var activeLink = sidebar.querySelector('.sidebar-nav a.active');
        if (activeLink) {
            var sidebarRect = sidebar.getBoundingClientRect();
            var linkRect = activeLink.getBoundingClientRect();
            if (linkRect.top < sidebarRect.top + 12 || linkRect.bottom > sidebarRect.bottom - 12) {
                activeLink.scrollIntoView({ block: 'nearest' });
                saveSidebarScroll();
            }
        }
    }

    restoreSidebarScroll();

    if (sidebar) {
        sidebar.addEventListener('scroll', saveSidebarScroll, { passive: true });
        window.addEventListener('pagehide', saveSidebarScroll);
    }

    function setOpen(open) {
        body.classList.toggle('sidebar-open', open);
        if (toggle) {
            toggle.setAttribute('aria-expanded', open ? 'true' : 'false');
        }
    }

    if (toggle) {
        toggle.addEventListener('click', function () {
            setOpen(!body.classList.contains('sidebar-open'));
        });
    }

    if (backdrop) {
        backdrop.addEventListener('click', function () {
            setOpen(false);
        });
    }

    document.addEventListener('keydown', function (event) {
        if (event.key === 'Escape') {
            setOpen(false);
        }
    });

    document.querySelectorAll('.admin-sidebar a').forEach(function (link) {
        link.addEventListener('click', function () {
            saveSidebarScroll();
            setOpen(false);
        });
    });

    // Chỉ gắn xác nhận [data-confirm] nếu trang không tải validate.js (tránh hỏi 2 lần).
    var hasValidateScript = !!document.querySelector('script[src*="validate.js"]');
    if (!hasValidateScript) {
        document.addEventListener('submit', function (event) {
            var form = event.target;
            if (form && form.matches && form.matches('[data-confirm]') && !window.confirm(form.getAttribute('data-confirm'))) {
                event.preventDefault();
            }
        });
    }
}());
