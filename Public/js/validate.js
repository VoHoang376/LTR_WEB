/* ==========================================================================
   Kiểm tra dữ liệu phía client + tiện ích giao diện.
   - Form .js-validate: dùng HTML5 constraint (required, pattern, min, max...)
     kết hợp class .was-validated của Bootstrap để hiện lỗi ngay trên form.
   - Form có data-confirm: hỏi xác nhận trước khi gửi (xoá dữ liệu).
   ========================================================================== */
(function () {
    'use strict';

    /* --------------------------------------------------- validate form */
    document.addEventListener('submit', function (event) {
        var form = event.target;

        if (form.classList.contains('js-validate') && !form.checkValidity()) {
            event.preventDefault();
            event.stopPropagation();
            form.classList.add('was-validated');

            var firstInvalid = form.querySelector(':invalid');
            if (firstInvalid && typeof firstInvalid.focus === 'function') {
                firstInvalid.focus();
            }
            return;
        }

        if (form.classList.contains('js-validate')) {
            form.classList.add('was-validated');
        }

        var confirmMessage = form.getAttribute('data-confirm');
        if (confirmMessage && !window.confirm(confirmMessage)) {
            event.preventDefault();
            event.stopPropagation();
        }
    }, true);

    /* -------------------------------- tự gửi form khi đổi select lọc/sắp xếp */
    document.querySelectorAll('[data-auto-submit]').forEach(function (element) {
        element.addEventListener('change', function () {
            if (element.form) {
                element.form.submit();
            }
        });
    });

    /* -------------------------------- không cho nhập số lượng vượt tồn kho */
    document.querySelectorAll('input[name="quantity"][data-max]').forEach(function (input) {
        input.addEventListener('input', function () {
            var max = parseInt(input.getAttribute('data-max'), 10);
            if (!isNaN(max) && parseInt(input.value, 10) > max) {
                input.setCustomValidity('Chỉ còn ' + max + ' sản phẩm trong kho.');
                input.reportValidity();
            } else {
                input.setCustomValidity('');
            }
        });
    });

    /* ------------------------------------------- định dạng giá khi nhập liệu */
    document.querySelectorAll('[data-price-input]').forEach(function (input) {
        input.addEventListener('blur', function () {
            var digits = String(input.value).replace(/[^0-9]/g, '');
            if (digits === '') {
                return;
            }
            input.value = digits.replace(/\B(?=(\d{3})+(?!\d))/g, '.');
        });
    });

    /* ------------------------------------------ tự sinh slug từ tên sản phẩm */
    var slugSource = document.querySelector('[data-slug-source]');
    var slugTarget = document.querySelector('[data-slug-target]');
    if (slugSource && slugTarget) {
        var slugTouched = slugTarget.value.trim() !== '';
        slugTarget.addEventListener('input', function () {
            slugTouched = true;
        });
        slugSource.addEventListener('input', function () {
            if (slugTouched) {
                return;
            }
            var map = {
                'à': 'a', 'á': 'a', 'ả': 'a', 'ã': 'a', 'ạ': 'a', 'ă': 'a', 'â': 'a',
                'đ': 'd', 'è': 'e', 'é': 'e', 'ê': 'e', 'ì': 'i', 'í': 'i',
                'ò': 'o', 'ó': 'o', 'ô': 'o', 'ơ': 'o', 'ù': 'u', 'ú': 'u', 'ư': 'u',
                'ý': 'y', 'ỳ': 'y', 'ỷ': 'y', 'ỹ': 'y', 'ỵ': 'y'
            };
            var slug = slugSource.value.toLowerCase().split('').map(function (character) {
                return map[character] !== undefined ? map[character] : character;
            }).join('');
            slugTarget.value = slug
                .normalize('NFD')
                .replace(/[\u0300-\u036f]/g, '')
                .replace(/[^a-z0-9]+/g, '-')
                .replace(/^-+|-+$/g, '');
        });
    }

    /* ---------------------------------- xem trước ảnh trước khi tải lên */
    document.querySelectorAll('[data-image-input]').forEach(function (input) {
        var preview = document.querySelector(input.getAttribute('data-image-input'));
        if (!preview) {
            return;
        }
        input.addEventListener('change', function () {
            var file = input.files && input.files[0];
            if (!file) {
                return;
            }
            if (file.size > 2 * 1024 * 1024) {
                input.setCustomValidity('Ảnh không được vượt quá 2 MB.');
                input.reportValidity();
                input.value = '';
                return;
            }
            input.setCustomValidity('');
            preview.src = URL.createObjectURL(file);
        });
    });
}());
