// ══════════════════════════════════════════════════
//  SHREK SITE — Efecte vizuale JavaScript (плавно)
// ══════════════════════════════════════════════════

(function () {
    'use strict';

    // ── 1. FIREFLY PARTICLES ─────────────────────────
    function initParticles() {
        const canvas = document.createElement('canvas');
        canvas.id = 'bg-particles';
        Object.assign(canvas.style, {
            position: 'fixed', top: '0', left: '0',
            width: '100%', height: '100%',
            pointerEvents: 'none', zIndex: '0', opacity: '.45'
        });
        document.body.prepend(canvas);
        const ctx = canvas.getContext('2d');
        function resize() { canvas.width = innerWidth; canvas.height = innerHeight; }
        resize();
        window.addEventListener('resize', resize);

        const pts = Array.from({ length: 48 }, () => ({
            x: Math.random() * innerWidth,
            y: Math.random() * innerHeight,
            r: Math.random() * 2.2 + 0.6,
            vx: (Math.random() - .5) * .18,   // mai lent
            vy: (Math.random() - .5) * .18,
            a: Math.random(),
            da: (Math.random() - .5) * .006,  // palpaie mai lent
            hue: Math.random() > .55 ? '#8cc63f' : '#c8f060'
        }));

        (function loop() {
            ctx.clearRect(0, 0, canvas.width, canvas.height);
            pts.forEach(p => {
                p.x += p.vx; p.y += p.vy;
                p.a += p.da;
                if (p.a <= 0 || p.a >= 1) p.da *= -1;
                if (p.x < 0) p.x = canvas.width;
                if (p.x > canvas.width) p.x = 0;
                if (p.y < 0) p.y = canvas.height;
                if (p.y > canvas.height) p.y = 0;
                ctx.beginPath();
                ctx.arc(p.x, p.y, p.r, 0, Math.PI * 2);
                ctx.fillStyle = p.hue;
                ctx.globalAlpha = p.a * .65;
                ctx.fill();
            });
            ctx.globalAlpha = 1;
            requestAnimationFrame(loop);
        })();
    }

    // ── 2. TYPING EFFECT — litera cu litera, mai lent ─
    function initTypingEffect() {
        const sub = document.querySelector('#header p');
        if (!sub) return;
        const original = sub.textContent.trim();
        sub.textContent = '';

        const cursorStyle = document.createElement('style');
        cursorStyle.textContent = `
      @keyframes blink { 0%,100%{opacity:1} 50%{opacity:0} }
      .typing-cursor { display:inline-block; border-right:2px solid #8cc63f; animation:blink 1s step-end infinite; margin-left:1px; }
    `;
        document.head.appendChild(cursorStyle);
        const cursor = document.createElement('span');
        cursor.className = 'typing-cursor';
        sub.appendChild(cursor);

        let i = 0;
        setTimeout(() => {
            const type = setInterval(() => {
                sub.insertBefore(document.createTextNode(original[i++]), cursor);
                if (i >= original.length) {
                    clearInterval(type);
                    setTimeout(() => cursor.remove(), 2500);
                }
            }, 110); // 110ms per litera — lent si vizibil
        }, 700);
    }

    // ── 3. SCROLL REVEAL — 1 secundă, плавно ─────────
    function initScrollReveal() {
        const targets = document.querySelectorAll(
            '.film-card, .highlight-card, .theme-card, #film-info, #story, #characters, .compare-table, #toc, #hero, #quiz-section, .char-table, .themes-grid'
        );

        const s = document.createElement('style');
        s.textContent = `
      .sr-hidden {
        opacity: 0;
        transform: translateY(40px);
        transition: opacity 1s ease, transform 1s cubic-bezier(0.22, 1, 0.36, 1);
      }
      .sr-visible {
        opacity: 1 !important;
        transform: translateY(0) !important;
      }
    `;
        document.head.appendChild(s);

        targets.forEach((el, i) => {
            el.classList.add('sr-hidden');
            el.style.transitionDelay = (i % 4) * 0.2 + 's'; // 0.2s intre elemente
        });

        const io = new IntersectionObserver((entries) => {
            entries.forEach(e => {
                if (e.isIntersecting) {
                    e.target.classList.add('sr-visible');
                    io.unobserve(e.target);
                }
            });
        }, { threshold: 0.1 });

        targets.forEach(el => io.observe(el));
    }

    // ── 4. RIPPLE pe click ────────────────────────────
    function initRipple() {
        const rStyle = document.createElement('style');
        rStyle.textContent = `@keyframes rippleAnim { to { transform: scale(5); opacity: 0; } }`;
        document.head.appendChild(rStyle);

        document.querySelectorAll('.btn-link, #nav ul li a').forEach(btn => {
            btn.addEventListener('click', function (e) {
                const rect = this.getBoundingClientRect();
                const ripple = document.createElement('span');
                Object.assign(ripple.style, {
                    position: 'absolute', borderRadius: '50%',
                    background: 'rgba(140,198,63,.35)', transform: 'scale(0)',
                    animation: 'rippleAnim .8s ease-out',
                    width: '60px', height: '60px',
                    left: (e.clientX - rect.left - 30) + 'px',
                    top:  (e.clientY - rect.top  - 30) + 'px',
                    pointerEvents: 'none'
                });
                this.style.position = 'relative';
                this.style.overflow = 'hidden';
                this.appendChild(ripple);
                setTimeout(() => ripple.remove(), 850);
            });
        });
    }

    // ── 5. COUNTER ANIMAT — 2.5s cu easing плавно ────
    function animateCounters() {
        document.querySelectorAll('#film-meta p').forEach(p => {
            const match = p.textContent.match(/(\$[\d,]+\s*milioane|[\d]+\s*minute)/);
            if (!match) return;
            const numMatch = p.textContent.match(/[\d]+/);
            if (!numMatch) return;

            const target   = parseInt(numMatch[0]);
            const before   = p.innerHTML.split(numMatch[0])[0];
            const after    = p.innerHTML.split(numMatch[0])[1];

            const el = document.createElement('span');
            el.style.color = '#c8f060';
            el.textContent = '0';
            p.innerHTML = '';
            p.insertAdjacentHTML('beforeend', before);
            p.appendChild(el);
            p.insertAdjacentHTML('beforeend', after);

            const io = new IntersectionObserver(entries => {
                if (!entries[0].isIntersecting) return;
                io.disconnect();

                const duration = 2500; // 2.5 secunde — lent si placut
                const startTime = performance.now();

                function easeOutCubic(t) {
                    return 1 - Math.pow(1 - t, 3);
                }

                function update(now) {
                    const progress = Math.min((now - startTime) / duration, 1);
                    el.textContent = Math.round(easeOutCubic(progress) * target);
                    if (progress < 1) requestAnimationFrame(update);
                    else el.textContent = target;
                }

                requestAnimationFrame(update);
            }, { threshold: 0.5 });

            io.observe(p);
        });
    }

    // ── 6. ACTIVE NAV ────────────────────────────────
    function fixActiveNav() {
        const page = location.pathname.split('/').pop() || 'index.html';
        document.querySelectorAll('#nav ul li a').forEach(a => {
            if (a.getAttribute('href') === page) a.classList.add('active');
        });
    }

    // ── INIT ─────────────────────────────────────────
    document.addEventListener('DOMContentLoaded', () => {
        initParticles();
        initTypingEffect();
        initScrollReveal();
        initRipple();
        animateCounters();
        fixActiveNav();
    });

})();