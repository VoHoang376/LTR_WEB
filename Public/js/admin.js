/* ==========================================================================
   Khu quản trị: đóng/mở sidebar trên màn hình nhỏ.
   ========================================================================== */
(function () {
    'use strict';

    var body = document.body;
    var toggle = document.getElementById('sidebarToggle');
    var backdrop = document.getElementById('adminBackdrop');

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
            setOpen(false);
        });
    });
}());
