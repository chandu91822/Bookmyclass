/* 
   BookMyClass - Landing Page Interactions 
*/

document.addEventListener('DOMContentLoaded', () => {

    // --- Sticky Navbar with Glass Effect ---
    const navbar = document.querySelector('.navbar');

    window.addEventListener('scroll', () => {
        if (window.scrollY > 50) {
            navbar.classList.add('scrolled');
        } else {
            navbar.classList.remove('scrolled');
        }
    });

    // --- Mobile Menu Toggle ---
    const toggleBtn = document.querySelector('.mobile-menu-toggle');
    const navLinks = document.querySelector('.nav-links');

    if (toggleBtn) {
        toggleBtn.addEventListener('click', () => {
            navLinks.style.display = navLinks.style.display === 'flex' ? 'none' : 'flex';
            if (navLinks.style.display === 'flex') {
                navLinks.style.flexDirection = 'column';
                navLinks.style.position = 'absolute';
                navLinks.style.top = '100%';
                navLinks.style.left = '0';
                navLinks.style.width = '100%';
                navLinks.style.background = 'var(--bg-dark)';
                navLinks.style.padding = '1rem';
                navLinks.style.borderBottom = '1px solid var(--glass-border)';
            }
        });
    }

    // --- Scroll Reveal Animations (Intersection Observer) ---
    const revealElements = document.querySelectorAll('.feature-card, .timeline-step, .stat-item, .hero-content, .hero-visual');

    const revealOptions = {
        threshold: 0.1,
        rootMargin: "0px 0px -50px 0px"
    };

    const revealOnScroll = new IntersectionObserver(function (entries, revealOnScroll) {
        entries.forEach(entry => {
            if (!entry.isIntersecting) {
                return;
            } else {
                entry.target.classList.add('visible');
                revealOnScroll.unobserve(entry.target);
            }
        });
    }, revealOptions);

    revealElements.forEach(el => {
        el.style.opacity = '0';
        el.style.transform = 'translateY(30px)';
        el.style.transition = 'all 0.6s ease-out';
        revealOnScroll.observe(el);
    });

    // Add CSS class for visible state dynamically
    const styleSheet = document.createElement("style");
    styleSheet.innerText = `
        .visible {
            opacity: 1 !important;
            transform: translateY(0) !important;
        }
    `;
    document.head.appendChild(styleSheet);


    // --- Animated Counters for Stats ---
    const counters = document.querySelectorAll('.stat-number');
    const speed = 200; // The lower the slower

    const countOptions = {
        threshold: 0.5
    };

    const runCounter = new IntersectionObserver((entries, observer) => {
        entries.forEach(entry => {
            if (entry.isIntersecting) {
                const counter = entry.target;
                const updateCount = () => {
                    const target = +counter.getAttribute('data-target');
                    const count = +counter.innerText;

                    // Lower inc to slow and higher to slow
                    const inc = target / speed;

                    if (count < target) {
                        // Add inc to count and output in counter
                        counter.innerText = Math.ceil(count + inc);
                        // Call function every ms
                        setTimeout(updateCount, 15);
                    } else {
                        counter.innerText = target;
                    }
                };
                updateCount();
                observer.unobserve(counter);
            }
        });
    }, countOptions);

    counters.forEach(counter => {
        runCounter.observe(counter);
    });

    // --- Interactive Availability Grid Hover Effects ---
    const gridRows = document.querySelectorAll('.mockup-row');

    gridRows.forEach(row => {
        row.addEventListener('mouseover', () => {
            row.style.background = 'rgba(255, 255, 255, 0.1)';
            row.style.transform = 'scale(1.02)';
            row.style.transition = 'all 0.2s';
        });
        row.addEventListener('mouseout', () => {
            row.style.background = 'rgba(255, 255, 255, 0.03)';
            row.style.transform = 'scale(1)';
        });
    });

});
