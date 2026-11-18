let cleanupStorefront = () => {};

function initializeStorefront() {
    cleanupStorefront();
    if (!document.querySelector('.storefront')) return;
    const abort = new AbortController();
    const { signal } = abort;
    const reducedMotion = matchMedia('(prefers-reduced-motion: reduce)');
    const cleanups = [];

    document.querySelectorAll('[data-carousel]').forEach((carousel) => {
        const slides = [...carousel.querySelectorAll('[data-slide]')];
        if (slides.length < 2) return;
        const controls = carousel.querySelector('[data-carousel-controls]');
        const dots = [...carousel.querySelectorAll('[data-go]')];
        const pauseButton = carousel.querySelector('[data-pause]');
        const status = carousel.querySelector('[data-carousel-status]');
        let current = 0;
        let paused = reducedMotion.matches;
        
        let focused = false;
        let visible = true;
        let touchStart = null;
        let timer;

        const schedule = () => {
            clearTimeout(timer);
            if (!paused && !focused && visible && !document.hidden) {
                timer = setTimeout(() => show(current + 1), 3500);
            }
        };
        const show = (index, announce = false) => {
            current = (index + slides.length) % slides.length;
            slides.forEach((slide, position) => {
                const active = position === current;
                slide.classList.toggle('is-active', active);
                slide.inert = !active;
                slide.setAttribute('aria-hidden', String(!active));
                slide.tabIndex = active ? 0 : -1;
                if (active) slide.querySelector('img').loading = 'eager';
            });
            dots.forEach((dot, position) => dot.setAttribute('aria-pressed', String(position === current)));
            carousel.dataset.activeSlide = String(current);
            if (announce) status.textContent = `Imagen ${current + 1} de ${slides.length}`;
            schedule();
        };
        const updatePause = () => {
            pauseButton.textContent = paused ? 'Reproducir' : 'Pausar';
            pauseButton.setAttribute('aria-label', paused ? 'Reproducir carrusel' : 'Pausar carrusel');
            pauseButton.setAttribute('aria-pressed', String(paused));
            schedule();
        };
        controls.hidden = false;
        carousel.querySelector('[data-previous]').addEventListener('click', () => show(current - 1, true), { signal });
        carousel.querySelector('[data-next]').addEventListener('click', () => show(current + 1, true), { signal });
        dots.forEach((dot) => dot.addEventListener('click', () => show(Number(dot.dataset.go), true), { signal }));
        pauseButton.addEventListener('click', () => { paused = !paused; updatePause(); }, { signal });
        carousel.addEventListener('keydown', (event) => {
            if (event.key === 'ArrowRight' || event.key === 'ArrowLeft') {
                event.preventDefault();
                show(current + (event.key === 'ArrowRight' ? 1 : -1), true);
            }
        }, { signal });
        carousel.addEventListener('focusin', () => { focused = true; schedule(); }, { signal });
        carousel.addEventListener('focusout', (event) => { focused = carousel.contains(event.relatedTarget); schedule(); }, { signal });
        carousel.addEventListener('touchstart', (event) => {
            touchStart = { x: event.touches[0].clientX, y: event.touches[0].clientY };
            clearTimeout(timer);
        }, { passive: true, signal });
        carousel.addEventListener('touchend', (event) => {
            if (!touchStart) return;
            const dx = event.changedTouches[0].clientX - touchStart.x;
            const dy = event.changedTouches[0].clientY - touchStart.y;
            if (Math.abs(dx) > 50 && Math.abs(dx) > Math.abs(dy)) {
                show(current + (dx < 0 ? 1 : -1), true);
                carousel.addEventListener('click', (click) => click.preventDefault(), { once: true, signal });
            } else schedule();
            touchStart = null;
        }, { passive: true, signal });
        carousel.addEventListener('touchcancel', () => { touchStart = null; schedule(); }, { passive: true, signal });
        document.addEventListener('visibilitychange', schedule, { signal });
        reducedMotion.addEventListener('change', () => { paused = reducedMotion.matches; updatePause(); }, { signal });
        const visibility = new IntersectionObserver(([entry]) => { visible = entry.isIntersecting; schedule(); });
        visibility.observe(carousel);
        show(0);
        updatePause();
        cleanups.push(() => { clearTimeout(timer); visibility.disconnect(); });
    });

    const revealTargets = [...document.querySelectorAll('.landing-contained, .vk-footer-grid > div')];
    const revealObserver = new IntersectionObserver((entries) => {
        entries.forEach((entry) => {
            if (entry.isIntersecting) {
                entry.target.classList.add('is-revealed');
                revealObserver.unobserve(entry.target);
            }
        });
    }, { threshold: 0.04 });
    if (!reducedMotion.matches) {
        revealTargets.forEach((element) => {
            if (element.getBoundingClientRect().top > innerHeight) {
                element.classList.add('scroll-reveal');
                revealObserver.observe(element);
            }
        });
    }
    reducedMotion.addEventListener('change', () => {
        if (reducedMotion.matches) revealTargets.forEach((element) => element.classList.add('is-revealed'));
    }, { signal });
    cleanups.push(() => revealObserver.disconnect());

    const progress = document.querySelector('[data-scroll-progress]');
    const backTop = document.querySelector('[data-back-top]');
    let frame = null;
    const updateScroll = () => {
        frame = null;
        const total = document.documentElement.scrollHeight - innerHeight;
        if (progress) progress.style.transform = `scaleX(${total > 0 ? scrollY / total : 0})`;
        if (backTop) backTop.hidden = scrollY < 600;
    };
    const requestScroll = () => { if (frame === null) frame = requestAnimationFrame(updateScroll); };
    window.addEventListener('scroll', requestScroll, { passive: true, signal });
    window.addEventListener('resize', requestScroll, { passive: true, signal });
    backTop?.addEventListener('click', () => {
        window.scrollTo({ top: 0, behavior: reducedMotion.matches ? 'instant' : 'smooth' });
        document.querySelector('.store-skip')?.focus({ preventScroll: true });
    }, { signal });
    updateScroll();
    cleanupStorefront = () => {
        abort.abort();
        cleanups.forEach((cleanup) => cleanup());
        if (frame !== null) cancelAnimationFrame(frame);
    };
}

if (document.readyState === 'loading') document.addEventListener('DOMContentLoaded', initializeStorefront, { once: true });
else initializeStorefront();
document.addEventListener('livewire:navigated', initializeStorefront);
document.addEventListener('livewire:navigating', () => cleanupStorefront());
