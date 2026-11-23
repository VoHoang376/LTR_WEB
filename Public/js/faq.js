document.addEventListener('DOMContentLoaded', function () {
    var accordions = document.querySelectorAll('[data-faq-accordion]');

    accordions.forEach(function (accordion) {
        accordion.addEventListener('click', function (event) {
            var button = event.target.closest('.faq-toggle');
            if (!button || !accordion.contains(button)) {
                return;
            }

            var answer = document.getElementById(button.getAttribute('aria-controls'));
            if (!answer) {
                return;
            }

            var willExpand = button.getAttribute('aria-expanded') !== 'true';
            button.setAttribute('aria-expanded', willExpand ? 'true' : 'false');
            answer.hidden = !willExpand;
            button.querySelector('.faq-toggle-icon').textContent = willExpand ? '\u2212' : '+';

            if (willExpand) {
                accordion.querySelectorAll('.faq-toggle[aria-expanded="true"]').forEach(function (openButton) {
                    if (openButton === button) {
                        return;
                    }
                    openButton.setAttribute('aria-expanded', 'false');
                    var openAnswer = document.getElementById(openButton.getAttribute('aria-controls'));
                    if (openAnswer) {
                        openAnswer.hidden = true;
                    }
                    openButton.querySelector('.faq-toggle-icon').textContent = '+';
                });
            }
        });
    });
});
