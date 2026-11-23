/* ==========================================================================
   Giỏ hàng AJAX (fetch)
   - Mọi form .js-cart-form được gửi bằng fetch, không tải lại trang.
   - Nếu JavaScript lỗi/tắt, form vẫn POST bình thường (progressive enhancement).
   ========================================================================== */
(function () {
    'use strict';

    var csrfMeta = document.querySelector('meta[name="csrf-token"]');
    var csrfToken = csrfMeta ? csrfMeta.getAttribute('content') : '';

    /* ------------------------------------------------------------- toast */
    function toast(message, type) {
        var container = document.getElementById('toast-container');
        if (!container) {
            window.alert(message);
            return;
        }

        var variant = type === 'danger' ? 'text-bg-danger'
            : (type === 'warning' ? 'text-bg-warning' : 'text-bg-success');

        var wrapper = document.createElement('div');
        wrapper.className = 'toast align-items-center border-0 ' + variant;
        wrapper.setAttribute('role', 'status');
        wrapper.setAttribute('aria-live', 'polite');
        wrapper.setAttribute('aria-atomic', 'true');

        var inner = document.createElement('div');
        inner.className = 'd-flex';

        var body = document.createElement('div');
        body.className = 'toast-body';
        body.textContent = message;

        var close = document.createElement('button');
        close.type = 'button';
        close.className = 'btn-close btn-close-white me-2 m-auto';
        close.setAttribute('data-bs-dismiss', 'toast');
        close.setAttribute('aria-label', 'Đóng');

        inner.appendChild(body);
        inner.appendChild(close);
        wrapper.appendChild(inner);
        container.appendChild(wrapper);

        if (window.bootstrap && window.bootstrap.Toast) {
            var instance = new window.bootstrap.Toast(wrapper, { delay: 3500 });
            instance.show();
            wrapper.addEventListener('hidden.bs.toast', function () {
                wrapper.remove();
            });
        } else {
            window.setTimeout(function () {
                wrapper.remove();
            }, 3500);
        }
    }

    /* --------------------------------------------------- cập nhật giao diện */
    function updateBadges(count) {
        document.querySelectorAll('[data-cart-count]').forEach(function (element) {
            element.textContent = String(count);
            element.classList.toggle('is-empty', count === 0);
        });
    }

    function applyCartState(payload) {
        updateBadges(payload.count || 0);

        document.querySelectorAll('[data-cart-subtotal]').forEach(function (element) {
            element.textContent = payload.subtotal_text || '';
        });

        var items = payload.items || [];

        document.querySelectorAll('[data-cart-row]').forEach(function (row) {
            var id = row.getAttribute('data-cart-row');
            var found = items.some(function (item) {
                return String(item.product_id) === String(id);
            });

            if (!found) {
                row.remove();
                return;
            }

            items.forEach(function (item) {
                if (String(item.product_id) !== String(id)) {
                    return;
                }
                var lineTotal = row.querySelector('[data-line-total]');
                if (lineTotal) {
                    lineTotal.textContent = item.line_total_text;
                }
                var input = row.querySelector('input[name="quantity"]');
                if (input && document.activeElement !== input) {
                    input.value = item.quantity;
                }
            });
        });

        var emptyState = document.querySelector('[data-cart-empty]');
        var cartTable = document.querySelector('[data-cart-table]');
        if (payload.empty) {
            if (cartTable) {
                cartTable.classList.add('d-none');
            }
            if (emptyState) {
                emptyState.classList.remove('d-none');
            }
        }
    }

    /* --------------------------------------------------------- gửi request */
    async function sendForm(form) {
        var body = new FormData(form);
        if (csrfToken && !body.has('csrf_token')) {
            body.append('csrf_token', csrfToken);
        }

        var response = await fetch(form.getAttribute('action'), {
            method: 'POST',
            body: body,
            headers: {
                'X-Requested-With': 'XMLHttpRequest',
                'Accept': 'application/json'
            },
            credentials: 'same-origin'
        });

        var payload;
        try {
            payload = await response.json();
        } catch (error) {
            payload = { ok: false, message: 'Máy chủ trả về dữ liệu không hợp lệ.' };
        }

        return { status: response.status, payload: payload };
    }

    function setBusy(form, busy) {
        form.querySelectorAll('button, input').forEach(function (element) {
            element.disabled = busy;
        });
        form.classList.toggle('is-busy', busy);
    }

    /* --------------------------------------------------- submit bằng fetch */
    document.addEventListener('submit', async function (event) {
        var form = event.target.closest('.js-cart-form');
        if (!form) {
            return;
        }

        if (event.defaultPrevented) {
            return;
        }

        event.preventDefault();
        setBusy(form, true);

        try {
            var result = await sendForm(form);
            var payload = result.payload || {};

            if (result.status === 419) {
                toast('Phiên làm việc đã hết hạn, vui lòng tải lại trang.', 'danger');
                window.setTimeout(function () {
                    window.location.reload();
                }, 1500);
                return;
            }

            if (payload.ok) {
                applyCartState(payload);
                if (payload.message) {
                    toast(payload.message, 'success');
                }
            } else {
                toast(payload.message || 'Không thể cập nhật giỏ hàng.', 'danger');
                if (typeof payload.count === 'number') {
                    updateBadges(payload.count);
                }
            }
        } catch (error) {
            toast('Không kết nối được máy chủ, vui lòng thử lại.', 'danger');
        } finally {
            setBusy(form, false);
        }
    });

    /* --------------------------------- tự cập nhật khi đổi ô số lượng (trang giỏ hàng) */
    document.addEventListener('change', function (event) {
        var input = event.target.closest('.js-cart-form input[name="quantity"]');
        if (!input) {
            return;
        }

        var form = input.closest('form');
        if (!form || !form.classList.contains('js-cart-auto')) {
            return;
        }

        if (typeof form.requestSubmit === 'function') {
            form.requestSubmit();
        } else {
            form.dispatchEvent(new Event('submit', { cancelable: true, bubbles: true }));
        }
    });

    /* ------------------------------------------------ nút tăng/giảm số lượng */
    document.addEventListener('click', function (event) {
        var button = event.target.closest('[data-qty-step]');
        if (!button) {
            return;
        }

        var wrapper = button.closest('.qty-group');
        var input = wrapper ? wrapper.querySelector('input[name="quantity"]') : null;
        if (!input) {
            return;
        }

        event.preventDefault();
        var step = parseInt(button.getAttribute('data-qty-step'), 10) || 1;
        var min = parseInt(input.getAttribute('min'), 10) || 1;
        var max = parseInt(input.getAttribute('max'), 10) || 99;
        var next = (parseInt(input.value, 10) || min) + step;

        input.value = String(Math.min(max, Math.max(min, next)));
        input.dispatchEvent(new Event('change', { bubbles: true }));
    });

    /* ------------------------------------ thêm nhanh vào giỏ ở trang chi tiết */
    document.addEventListener('click', function (event) {
        var trigger = event.target.closest('[data-add-to-cart]');
        if (!trigger) {
            return;
        }

        event.preventDefault();
        var form = document.getElementById('add-to-cart-form');
        if (form) {
            form.dispatchEvent(new Event('submit', { cancelable: true, bubbles: true }));
        }
    });
}());
