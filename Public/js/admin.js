document.addEventListener('DOMContentLoaded', function () {
    var sidebar = document.getElementById('admin-sidebar');
    var backdrop = document.getElementById('admin-sidebar-backdrop');
    var toggle = document.querySelector('[data-sidebar-toggle]');

    if (!sidebar || !backdrop || !toggle) {
        return;
    }

    function setSidebarOpen(isOpen) {
        sidebar.classList.toggle('is-open', isOpen);
        backdrop.hidden = !isOpen;
        toggle.setAttribute('aria-expanded', isOpen ? 'true' : 'false');
    }

    toggle.addEventListener('click', function () {
        setSidebarOpen(!sidebar.classList.contains('is-open'));
    });
    backdrop.addEventListener('click', function () {
        setSidebarOpen(false);
    });
    document.addEventListener('keydown', function (event) {
        if (event.key === 'Escape') {
            setSidebarOpen(false);
        }
    });
    document.addEventListener('submit', function (event) {
        var form = event.target;
        if (form.matches('[data-confirm]') && !window.confirm(form.getAttribute('data-confirm'))) {
            event.preventDefault();
        }
    });
    window.addEventListener('resize', function () {
        if (window.innerWidth >= 1280) {
            setSidebarOpen(false);
        }
    });
});
