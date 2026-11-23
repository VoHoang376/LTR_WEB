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
