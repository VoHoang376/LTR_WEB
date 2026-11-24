(function () {
    'use strict';

    function currentRelativeTarget() {
        var search = window.location.search || '';
        return 'index.php' + search;
    }

    /** Hiển thị bảng thông báo (modal) yêu cầu đăng nhập cho khách vãng lai. */
    window.showLoginRequiredModal = function (message, redirectUrl) {
        var notice = message || 'Vui lòng đăng nhập bằng tài khoản Thành viên (Khách hàng) để sử dụng chức năng này.';
        var target = redirectUrl || currentRelativeTarget();
        var modalEl = document.getElementById('loginRequiredModal');

        if (!modalEl) {
            window.alert(notice);
            window.location.href = 'index.php?controller=auth&action=login&redirect=' + encodeURIComponent(target);
            return;
        }

        var msgEl = document.getElementById('loginRequiredMessage');
        if (msgEl) {
            msgEl.textContent = notice;
        }

        var loginBtn = document.getElementById('loginRequiredLoginBtn');
        if (loginBtn) {
            loginBtn.setAttribute('href', 'index.php?controller=auth&action=login&redirect=' + encodeURIComponent(target));
        }

        var registerBtn = document.getElementById('loginRequiredRegisterBtn');
        if (registerBtn) {
            registerBtn.setAttribute('href', 'index.php?controller=auth&action=register&redirect=' + encodeURIComponent(target));
        }

        if (window.bootstrap && window.bootstrap.Modal) {
            window.bootstrap.Modal.getOrCreateInstance(modalEl).show();
        } else {
            window.alert(notice);
            window.location.href = 'index.php?controller=auth&action=login&redirect=' + encodeURIComponent(target);
        }
    };

    document.addEventListener('click', function (event) {
        var trigger = event.target.closest('[data-require-login]');
        if (!trigger) {
            return;
        }
        event.preventDefault();
        window.showLoginRequiredModal(
            trigger.getAttribute('data-require-login'),
            trigger.getAttribute('data-login-redirect') || ''
        );
    });
}());

document.addEventListener('DOMContentLoaded', function () {
    var contactForm = document.getElementById('contactForm');
    if (!contactForm) {
        return;
    }

    contactForm.addEventListener('submit', function (event) {
        var isValid = true;

        var fullname = contactForm.querySelector('#fullname');
        var email = contactForm.querySelector('#email');
        var phone = contactForm.querySelector('#phone');
        var message = contactForm.querySelector('#message');

        var fields = [fullname, email, phone, message];
        for (var i = 0; i < fields.length; i++) {
            if (fields[i]) {
                fields[i].classList.remove('is-invalid');
            }
        }

        if (fullname && (fullname.value.trim().length < 2 || fullname.value.trim().length > 100)) {
            fullname.classList.add('is-invalid');
            isValid = false;
        }

        var emailPattern = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
        if (email && (!email.value.trim() || !emailPattern.test(email.value.trim()))) {
            email.classList.add('is-invalid');
            isValid = false;
        }

        if (phone && phone.value.trim() !== '') {
            var phonePattern = /^[0-9+\-\s()]{7,20}$/;
            if (!phonePattern.test(phone.value.trim())) {
                phone.classList.add('is-invalid');
                isValid = false;
            }
        }

        if (message && message.value.trim().length < 10) {
            message.classList.add('is-invalid');
            isValid = false;
        }

        if (!isValid) {
            event.preventDefault();
        }
    });

    contactForm.querySelectorAll('input, textarea').forEach(function (el) {
        el.addEventListener('input', function () {
            this.classList.remove('is-invalid');
        });
    });
});
