// ─── Live clock (Lagos time) ───
function updateClock() {
  const now = new Date();
  const opts = { hour: '2-digit', minute: '2-digit', hour12: false, timeZone: 'Africa/Bujumbura' };
  const time = new Intl.DateTimeFormat('en-GB', opts).format(now);
  const el = document.getElementById('clock');
  if (el) el.textContent = time + ' CAT'
}
updateClock();
setInterval(updateClock, 30000);

// ─── Footer year ───
document.getElementById('year').textContent = new Date().getFullYear();

// ─── Nav background on scroll ───
const nav = document.getElementById('nav');
const onScroll = () => {
  if (window.scrollY > 40) {
    nav.classList.add('bg-ink/80', 'backdrop-blur-md', 'border-b', 'border-ink-3', 'py-3');
    nav.classList.remove('py-5');
  } else {
    nav.classList.remove('bg-ink/80', 'backdrop-blur-md', 'border-b', 'border-ink-3', 'py-3');
    nav.classList.add('py-5');
  }
};
window.addEventListener('scroll', onScroll, { passive: true });

// ─── Mobile menu ───
const menuBtn = document.getElementById('menuBtn');
const menu = document.getElementById('menu');
menuBtn.addEventListener('click', () => menu.classList.toggle('open'));
document.querySelectorAll('.menu-link').forEach(l => l.addEventListener('click', () => menu.classList.remove('open')));

// ─── Reveal on scroll ───
const io = new IntersectionObserver((entries) => {
  entries.forEach(e => { if (e.isIntersecting) { e.target.classList.add('in'); io.unobserve(e.target); } });
}, { threshold: 0.12, rootMargin: '0px 0px -60px 0px' });
document.querySelectorAll('.reveal').forEach(el => io.observe(el));

// ─── Booking form (demo handler — replace with backend / Formspree / Resend etc.) ───
document.getElementById('bookingForm').addEventListener('submit', (e) => {
  e.preventDefault();
  document.getElementById('formMsg').classList.remove('hidden');
  e.target.reset();
});