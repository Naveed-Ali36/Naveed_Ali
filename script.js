// Nav scroll
const nav = document.querySelector('.nav');
const navToggle = document.getElementById('nav-toggle');

window.addEventListener('scroll', () => {
    nav.classList.toggle('scrolled', window.scrollY > 60);
});

navToggle?.addEventListener('click', () => {
    nav.classList.toggle('open');
});

document.querySelectorAll('.nav-links a').forEach(link => {
    link.addEventListener('click', () => nav.classList.remove('open'));
});

// Cursor glow
const glow = document.getElementById('cursor-glow');
if (window.matchMedia('(pointer: fine)').matches && glow) {
    document.addEventListener('mousemove', (e) => {
        glow.style.left = e.clientX + 'px';
        glow.style.top = e.clientY + 'px';
    });
}

// Scroll reveal
const reveals = document.querySelectorAll('.reveal');
const revealObs = new IntersectionObserver((entries) => {
    entries.forEach(entry => {
        if (entry.isIntersecting) {
            entry.target.classList.add('visible');
        }
    });
}, { threshold: 0.12, rootMargin: '0px 0px -40px 0px' });

reveals.forEach(el => revealObs.observe(el));

// Hero role rotator
const roles = window.PORTFOLIO_ROLES || [
    'Web Developer & Tracking',
    'GHL CRM Automation',
    'Conversion & Funnel Expert',
    'WordPress Development',
    'React & React Native'
];
let roleIdx = 0;
const roleEl = document.getElementById('role-text');

function rotateRole() {
    if (!roleEl) return;
    roleEl.style.opacity = '0';
    roleEl.style.transform = 'translateY(8px)';
    setTimeout(() => {
        roleIdx = (roleIdx + 1) % roles.length;
        roleEl.textContent = roles[roleIdx];
        roleEl.style.opacity = '1';
        roleEl.style.transform = 'translateY(0)';
    }, 400);
}

if (roleEl) {
    roleEl.style.transition = 'opacity 0.4s, transform 0.4s';
    setInterval(rotateRole, 3500);
}

// Journey tabs
const tabs = document.querySelectorAll('.tab');
const panels = document.querySelectorAll('.journey-panel');

tabs.forEach(tab => {
    tab.addEventListener('click', () => {
        tabs.forEach(t => t.classList.remove('active'));
        panels.forEach(p => p.classList.remove('active'));
        tab.classList.add('active');
        document.getElementById(tab.dataset.tab)?.classList.add('active');
    });
});

// Contact form
const form = document.querySelector('.contact-form');
form?.addEventListener('submit', async (e) => {
    e.preventDefault();
    const btn = form.querySelector('button');
    const orig = btn.innerHTML;
    btn.disabled = true;
    btn.innerHTML = 'Sending...';

    const formData = new FormData(form);
    const payload = Object.fromEntries(formData.entries());

    try {
        let response;
        if (form.action.includes('formsubmit.co')) {
            const ajaxUrl = form.action.replace('https://formsubmit.co/', 'https://formsubmit.co/ajax/');
            response = await fetch(ajaxUrl, {
                method: 'POST',
                headers: { 'Accept': 'application/json' },
                body: formData
            });
        } else {
            response = await fetch(form.action || 'api/contact.php', {
                method: 'POST',
                body: formData
            });
        }

        const data = await response.json();
        if (data.success) {
            btn.innerHTML = 'Sent! ✓';
            btn.style.background = '#22c55e';
            form.reset();
        } else {
            btn.innerHTML = data.message || 'Error';
            btn.style.background = '#ef4444';
        }
    } catch {
        btn.innerHTML = 'Failed to send';
        btn.style.background = '#ef4444';
    }

    setTimeout(() => {
        btn.innerHTML = orig;
        btn.style.background = '';
        btn.disabled = false;
    }, 3000);
});

// Smooth scroll
document.querySelectorAll('a[href^="#"]').forEach(a => {
    a.addEventListener('click', (e) => {
        const href = a.getAttribute('href');
        if (href === '#') return;
        e.preventDefault();
        document.querySelector(href)?.scrollIntoView({ behavior: 'smooth' });
    });
});

// Active nav on scroll
const sections = document.querySelectorAll('section[id]');
const navLinks = document.querySelectorAll('.nav-links a');

window.addEventListener('scroll', () => {
    let current = '';
    sections.forEach(sec => {
        if (window.scrollY >= sec.offsetTop - 200) {
            current = sec.id;
        }
    });
    navLinks.forEach(link => {
        link.style.color = link.getAttribute('href') === `#${current}` ? '#f5f5f0' : '';
    });
});
