/**
 * NexSoft Hub - Main JavaScript
 */

document.addEventListener('DOMContentLoaded', function () {

    // ================================================================
    // 1. NAVBAR SCROLL GLASSMORPHISM
    // ================================================================
    const navbar = document.getElementById('mainNavbar');
    if (navbar) {
        const handleScroll = () => {
            if (window.scrollY > 60) {
                navbar.classList.add('scrolled');
            } else {
                navbar.classList.remove('scrolled');
            }
        };
        window.addEventListener('scroll', handleScroll, { passive: true });
        handleScroll(); // run on load
    }

    // ================================================================
    // 2. SCROLL REVEAL ANIMATIONS
    // ================================================================
    const revealElements = document.querySelectorAll('.reveal');
    if (revealElements.length > 0) {
        const revealObserver = new IntersectionObserver((entries) => {
            entries.forEach((entry, i) => {
                if (entry.isIntersecting) {
                    const delay = entry.target.dataset.delay || 0;
                    setTimeout(() => {
                        entry.target.classList.add('revealed');
                    }, delay);
                    revealObserver.unobserve(entry.target);
                }
            });
        }, { threshold: 0.12, rootMargin: '0px 0px -50px 0px' });

        revealElements.forEach((el, index) => {
            if (!el.dataset.delay) {
                el.dataset.delay = index % 4 * 100;
            }
            revealObserver.observe(el);
        });
    }

    // ================================================================
    // 3. COUNTER ANIMATION (Hero Stats)
    // ================================================================
    const counters = document.querySelectorAll('[data-count]');
    if (counters.length > 0) {
        const countObserver = new IntersectionObserver((entries) => {
            entries.forEach(entry => {
                if (entry.isIntersecting) {
                    animateCounter(entry.target);
                    countObserver.unobserve(entry.target);
                }
            });
        }, { threshold: 0.5 });

        counters.forEach(counter => countObserver.observe(counter));
    }

    function animateCounter(el) {
        const target = parseInt(el.dataset.count);
        const suffix = el.dataset.suffix || '';
        const duration = 2000;
        const step = target / (duration / 16);
        let current = 0;
        const timer = setInterval(() => {
            current += step;
            if (current >= target) {
                el.textContent = target + suffix;
                clearInterval(timer);
            } else {
                el.textContent = Math.floor(current) + suffix;
            }
        }, 16);
    }

    // ================================================================
    // 4. CONTACT FORM VALIDATION
    // ================================================================
    const contactForm = document.getElementById('contactForm');
    if (contactForm) {
        contactForm.addEventListener('submit', function (e) {
            let valid = true;
            const name = document.getElementById('contact_name');
            const email = document.getElementById('contact_email');
            const message = document.getElementById('contact_message');

            clearErrors([name, email, message]);

            if (!name.value.trim() || name.value.trim().length < 2) {
                showError(name, 'Please enter your full name (min 2 characters)');
                valid = false;
            }
            if (!isValidEmail(email.value.trim())) {
                showError(email, 'Please enter a valid email address');
                valid = false;
            }
            if (!message.value.trim() || message.value.trim().length < 10) {
                showError(message, 'Message must be at least 10 characters');
                valid = false;
            }

            if (!valid) e.preventDefault();
        });
    }

    // ================================================================
    // 5. REGISTRATION FORM VALIDATION
    // ================================================================
    const registerForm = document.getElementById('registerForm');
    if (registerForm) {
        registerForm.addEventListener('submit', function (e) {
            let valid = true;
            const name = document.getElementById('reg_name');
            const email = document.getElementById('reg_email');
            const phone = document.getElementById('reg_phone');
            const skills = document.getElementById('reg_skills');
            const msg = document.getElementById('reg_message');

            clearErrors([name, email, phone, skills, msg]);

            if (!name.value.trim() || name.value.trim().length < 2) {
                showError(name, 'Please enter your full name');
                valid = false;
            }
            if (!isValidEmail(email.value.trim())) {
                showError(email, 'Please enter a valid email');
                valid = false;
            }
            if (!phone.value.trim() || phone.value.trim().length < 7) {
                showError(phone, 'Please enter a valid phone number');
                valid = false;
            }
            if (!skills.value.trim()) {
                showError(skills, 'Please describe your skills');
                valid = false;
            }
            if (!msg.value.trim() || msg.value.trim().length < 10) {
                showError(msg, 'Please write a short message (min 10 chars)');
                valid = false;
            }

            if (!valid) e.preventDefault();
        });
    }

    // ================================================================
    // 6. NEWSLETTER FORM (footer)
    // ================================================================
    const newsletterBtn = document.querySelector('.newsletter-btn');
    if (newsletterBtn) {
        newsletterBtn.addEventListener('click', function () {
            const input = document.querySelector('.newsletter-input');
            if (input && isValidEmail(input.value.trim())) {
                showSuccessPopup('Thanks for subscribing!');
                input.value = '';
            } else {
                if (input) {
                    input.style.borderColor = '#ef4444';
                    setTimeout(() => { input.style.borderColor = ''; }, 2000);
                }
            }
        });
    }

    // ================================================================
    // 7. SUCCESS POPUP (auto-dismiss form messages)
    // ================================================================
    const successAlert = document.querySelector('.alert-success-custom');
    if (successAlert) {
        showSuccessPopup(successAlert.textContent);
        successAlert.style.display = 'none';
        window.scrollTo({ top: 0, behavior: 'smooth' });
    }

    // ================================================================
    // UTILITIES
    // ================================================================
    function isValidEmail(email) {
        return /^[^\s@]+@[^\s@]+\.[^\s@]+$/.test(email);
    }

    function showError(field, message) {
        field.classList.add('is-invalid');
        const feedback = document.createElement('div');
        feedback.className = 'invalid-feedback';
        feedback.textContent = message;
        field.parentNode.appendChild(feedback);
    }

    function clearErrors(fields) {
        fields.forEach(f => {
            f.classList.remove('is-invalid');
            const fb = f.parentNode.querySelector('.invalid-feedback');
            if (fb) fb.remove();
        });
    }

    function showSuccessPopup(message) {
        const popup = document.getElementById('successPopup');
        if (popup) {
            popup.querySelector('.popup-msg').textContent = message;
            popup.classList.add('show');
            setTimeout(() => popup.classList.remove('show'), 4000);
        } else {
            const el = document.createElement('div');
            el.className = 'success-popup';
            el.innerHTML = `<i class="bi bi-check-circle-fill"></i><span class="popup-msg">${message}</span>`;
            document.body.appendChild(el);
            setTimeout(() => el.classList.add('show'), 50);
            setTimeout(() => { el.classList.remove('show'); setTimeout(() => el.remove(), 400); }, 4000);
        }
    }

    // ================================================================
    // 8. SMOOTH HOVER LIFT (cards)
    // ================================================================
    document.querySelectorAll('.service-card, .project-card, .blog-card, .testimonial-card').forEach(card => {
        card.style.willChange = 'transform';
    });

    // ================================================================
    // 9. MOBILE MENU auto-close on link click
    // ================================================================
    document.querySelectorAll('.nexsoft-navbar .nav-link').forEach(link => {
        link.addEventListener('click', () => {
            const toggler = document.querySelector('.navbar-toggler');
            const collapse = document.getElementById('navbarMain');
            if (collapse && collapse.classList.contains('show') && toggler) {
                toggler.click();
            }
        });
    });

});
