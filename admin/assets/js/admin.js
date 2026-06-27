/* ────────────── State management ────────────── */
const STORAGE_KEY = 'ayo_admin_state_v1';

const seedState = () => ({
  inquiries: [
    { id: 'i1', name: 'Téo Maré', email: 'teo@maremusic.com', artist: 'Téo Maré', service: 'Mixing', timeline: 'Late June 2026', message: 'Working on a 6-track EP, mostly done tracking. Heard your work on Saint Joy\'s last record — exactly the warmth I\'m chasing. Ready to send stems whenever.', status: 'new', receivedAt: '2026-05-08T14:32:00Z' },
    { id: 'i2', name: 'Nala', email: 'nala@records.co', artist: 'Nala', service: 'Production', timeline: 'August 2026', message: 'Need someone who understands amapiano-meets-R&B. Three songs ready in skeleton form. Budget is real, deadline is firm — label release in October.', status: 'new', receivedAt: '2026-05-07T09:14:00Z' },
    { id: 'i3', name: 'Daniel Mukiza', email: 'd.mukiza@gmail.com', artist: 'Solo project', service: 'Mastering', timeline: 'ASAP', message: 'Single track, needs streaming master. Mix is at -8 LUFS, unsure if that\'s right. Can send WAV today.', status: 'replied', receivedAt: '2026-05-05T18:21:00Z' },
    { id: 'i4', name: 'Aïsha Konaté', email: 'aisha@k.studio', artist: 'AÏSHA', service: 'Album / EP bundle', timeline: 'Q3 2026', message: '8-track album. R&B leaning toward Afro-soul. Looking for a producer who can also mix and master — full pipeline. References: Tems, Ayra Starr, FKA twigs.', status: 'booked', receivedAt: '2026-05-02T11:45:00Z' },
    { id: 'i5', name: 'Kingston Marley', email: 'kmarley@yard.jm', artist: 'Kingston Marley', service: 'Mixing', timeline: 'Flexible', message: 'Reggae-Afrobeats fusion project. 12 songs. Want to discuss before committing.', status: 'declined', receivedAt: '2026-04-28T16:00:00Z' },
  ],
  tracks: [
    { id: 't1', title: 'Free Mind', artist: 'Tems', role: 'Production & Mix', year: 2023, spotifyUrl: 'https://open.spotify.com/track/0XwFvcrWzcYK7OuotsXJW1', featured: true, live: true },
    { id: 't2', title: 'Last Last', artist: 'Burna Boy', role: 'Mixing', year: 2022, spotifyUrl: 'https://open.spotify.com/track/6PQ88X9TkUIAUIZJHW2upE', featured: false, live: true },
    { id: 't3', title: 'Essence', artist: 'Wizkid ft. Tems', role: 'Mastering', year: 2021, spotifyUrl: 'https://open.spotify.com/track/1QylwwAEKnegzJ5IWkEkSx', featured: false, live: true },
    { id: 't4', title: 'Joha', artist: 'Asake', role: 'Co-Production', year: 2022, spotifyUrl: 'https://open.spotify.com/track/32tlLzZwcbqHUeeRIIjf5G', featured: false, live: true },
    { id: 't5', title: 'Water', artist: 'Tyla', role: 'Mix Engineering', year: 2023, spotifyUrl: 'https://open.spotify.com/track/7tNO3vJC34zZ48NTfIcSDD', featured: false, live: false },
  ],
  services: [
    { id: 's1', name: 'Production', price: 2400, unit: '/track', subtitle: 'Starting · negotiated per project', tier: 'A', items: ['Full beat & arrangement', 'Vocal direction sessions', 'Stems & project files delivered', 'Two rounds of revision', '3–5 week turnaround'], live: true },
    { id: 's2', name: 'Mixing', price: 850, unit: '/track', subtitle: 'Up to 60 stems', tier: 'B · Most booked', items: ['Hybrid analog/ITB chain', 'Vocal tuning & comping', 'Atmos / immersive on request', 'Three rounds of revision', '7–10 day turnaround'], live: true },
    { id: 's3', name: 'Mastering', price: 220, unit: '/track', subtitle: 'Streaming + DDP for vinyl', tier: 'C', items: ['Streaming, club & vinyl masters', 'Loudness-target compliant', 'Stem mastering available', 'Two rounds of revision', '48-hour turnaround'], live: true },
  ],
  availability: { accepting: true, period: 'June – August 2026', slots: 3 },
  settings: {
    alias: 'AYÒ', tagline: 'Producer · Mix Engineer', location: 'Bujumbura', studio: 'Home Studio',
    heroHeadline: 'Where afrobeats meets the sublime.',
    email: 'hello@ayo.studio', phone: '+257 00 000 000',
    instagram: '@ayostudio', spotify: '', youtube: '',
  },
});

let state = (() => {
  try {
    const stored = localStorage.getItem(STORAGE_KEY);
    if (stored) return JSON.parse(stored);
  } catch(e) {}
  const fresh = seedState();
  localStorage.setItem(STORAGE_KEY, JSON.stringify(fresh));
  return fresh;
})();

const save = () => localStorage.setItem(STORAGE_KEY, JSON.stringify(state));

const toast = (msg = 'Saved.') => {
  const el = document.getElementById('toast');
  document.getElementById('toastMsg').textContent = msg;
  el.classList.add('show');
  clearTimeout(toast._t);
  toast._t = setTimeout(() => el.classList.remove('show'), 2200);
};

/* ────────────── Routing ────────────── */
const routes = ['overview','inquiries','tracks','services','availability','settings'];
const titles = { overview: 'Overview', inquiries: 'Inquiries', tracks: 'Tracks', services: 'Services & Pricing', availability: 'Availability', settings: 'Settings' };

function navigate() {
  let route = (location.hash || '#overview').slice(1);
  if (!routes.includes(route)) route = 'overview';
  document.querySelectorAll('.section').forEach(s => s.classList.toggle('active', s.id === route));
  document.querySelectorAll('.sidebar-link').forEach(l => l.classList.toggle('active', l.dataset.route === route));
  document.getElementById('pageTitle').textContent = titles[route];
  document.getElementById('sidebar').classList.remove('open');

  // Section-specific render
  if (route === 'overview') renderOverview();
  if (route === 'inquiries') renderInquiries();
  if (route === 'tracks') renderTracks();
  if (route === 'services') renderServices();
  if (route === 'availability') renderAvailability();
  if (route === 'settings') renderSettings();
}
window.addEventListener('hashchange', navigate);

/* ────────────── Overview ────────────── */
function renderOverview() {
  const newCount = state.inquiries.filter(i => i.status === 'new').length;
  document.getElementById('statNewInq').textContent = newCount;
  document.getElementById('statTracksLive').textContent = state.tracks.filter(t => t.live).length;
  document.getElementById('statBooked').textContent = state.inquiries.filter(i => i.status === 'booked').length;
  document.getElementById('navInqCount').textContent = newCount;
  document.getElementById('navInqCount').style.display = newCount ? '' : 'none';

  // Recent inquiries
  const recent = [...state.inquiries].sort((a,b) => new Date(b.receivedAt) - new Date(a.receivedAt)).slice(0,4);
  document.getElementById('recentInquiriesList').innerHTML = recent.map(i => `
    <a href="#inquiries" onclick="selectInquiry('${i.id}')" class="block px-5 py-4 border-b border-ink-3 last:border-0 hover:bg-ink-3/40 transition-colors">
      <div class="flex items-start justify-between gap-4 mb-1">
        <div class="flex items-center gap-3 min-w-0">
          <div class="display text-lg truncate">${esc(i.name)}</div>
          <span class="badge ${i.status}"><span class="dot"></span>${i.status}</span>
        </div>
        <div class="eyebrow text-cream-3 flex-shrink-0">${timeAgo(i.receivedAt)}</div>
      </div>
      <div class="text-sm text-cream-2 truncate">${esc(i.service)} · ${esc(i.message.slice(0, 80))}…</div>
    </a>
  `).join('');

  // Mini availability widget
  const a = state.availability;
  document.getElementById('availToggleMini').classList.toggle('on', a.accepting);
  document.getElementById('availPeriodMini').textContent = a.period || '—';
  document.getElementById('availSlotsMini').textContent = String(a.slots).padStart(2, '0');
  document.getElementById('availToggleMini').onclick = () => {
    state.availability.accepting = !state.availability.accepting;
    save(); renderOverview(); toast('Availability updated.');
  };
}

/* ────────────── Inquiries ────────────── */
let currentFilter = 'all';
let selectedInquiryId = null;

function renderInquiries() {
  const filtered = currentFilter === 'all' ? state.inquiries : state.inquiries.filter(i => i.status === currentFilter);
  const sorted = [...filtered].sort((a,b) => new Date(b.receivedAt) - new Date(a.receivedAt));

  document.getElementById('inquiryList').innerHTML = sorted.length ? sorted.map(i => `
    <div class="inquiry-item ${i.id === selectedInquiryId ? 'active' : ''}" onclick="selectInquiry('${i.id}')">
      <div class="flex items-start justify-between gap-3 mb-1">
        <div class="display text-lg truncate">${esc(i.name)}</div>
        <span class="badge ${i.status}"><span class="dot"></span>${i.status}</span>
      </div>
      <div class="eyebrow text-cream-3 mb-2">${esc(i.service)} · ${timeAgo(i.receivedAt)}</div>
      <div class="text-sm text-cream-2 line-clamp-2" style="display:-webkit-box;-webkit-line-clamp:2;-webkit-box-orient:vertical;overflow:hidden">${esc(i.message)}</div>
    </div>
  `).join('') : `<div class="text-center text-cream-3 p-10"><div class="display text-2xl mb-2">No inquiries</div><div class="text-sm">Nothing matches this filter yet.</div></div>`;

  // Filter buttons
  document.querySelectorAll('.filter-btn').forEach(b => {
    b.onclick = () => {
      currentFilter = b.dataset.filter;
      document.querySelectorAll('.filter-btn').forEach(x => {
        x.classList.remove('border-gold','text-gold');
        x.classList.add('border-ink-3','text-cream-3');
      });
      b.classList.add('border-gold','text-gold');
      b.classList.remove('border-ink-3','text-cream-3');
      renderInquiries();
    };
  });
}

function selectInquiry(id) {
  selectedInquiryId = id;
  if (location.hash !== '#inquiries') location.hash = '#inquiries';
  const i = state.inquiries.find(x => x.id === id);
  if (!i) return;

  document.getElementById('inquiryDetail').innerHTML = `
    <div class="flex items-start justify-between gap-4 mb-6">
      <div>
        <div class="eyebrow text-cream-3 mb-2">${new Date(i.receivedAt).toLocaleString('en-GB', { dateStyle: 'long', timeStyle: 'short' })}</div>
        <div class="display text-3xl">${esc(i.name)}</div>
        <a href="mailto:${esc(i.email)}" class="text-cream-2 hover:text-gold transition-colors">${esc(i.email)}</a>
      </div>
      <span class="badge ${i.status}"><span class="dot"></span>${i.status}</span>
    </div>

    <div class="grid grid-cols-2 md:grid-cols-3 gap-4 mb-8 p-4 bg-ink-3/40 rounded">
      <div><div class="eyebrow text-cream-3 mb-1">Artist</div><div class="text-sm">${esc(i.artist || '—')}</div></div>
      <div><div class="eyebrow text-cream-3 mb-1">Service</div><div class="text-sm">${esc(i.service)}</div></div>
      <div><div class="eyebrow text-cream-3 mb-1">Timeline</div><div class="text-sm">${esc(i.timeline || '—')}</div></div>
    </div>

    <div class="mb-8">
      <div class="eyebrow text-cream-3 mb-3">— Message</div>
      <p class="text-cream leading-relaxed whitespace-pre-wrap">${esc(i.message)}</p>
    </div>

    <div class="border-t border-ink-3 pt-6">
      <div class="eyebrow text-gold mb-3">— Reply</div>
      <textarea class="field mb-4" placeholder="Type your reply — opens in your email client when sent." id="replyText"></textarea>
      <div class="flex flex-wrap items-center justify-between gap-3">
        <div class="flex gap-2">
          <select id="statusChange" class="field" style="width:auto">
            <option value="new" ${i.status==='new'?'selected':''}>Mark as new</option>
            <option value="replied" ${i.status==='replied'?'selected':''}>Mark as replied</option>
            <option value="booked" ${i.status==='booked'?'selected':''}>Mark as booked</option>
            <option value="declined" ${i.status==='declined'?'selected':''}>Mark as declined</option>
          </select>
          <button class="btn-secondary" onclick="updateStatus('${i.id}')">Update status</button>
        </div>
        <div class="flex gap-2">
          <button class="btn-secondary btn-danger" onclick="deleteInquiry('${i.id}')">Delete</button>
          <button class="btn-primary" onclick="sendReply('${i.id}')">
            <svg width="14" height="14" viewBox="0 0 14 14" fill="none"><path d="M1 7L13 1L8 13L7 8L1 7Z" stroke="currentColor" stroke-width="1.5"/></svg>
            Send via email
          </button>
        </div>
      </div>
    </div>
  `;
  renderInquiries();
}

function updateStatus(id) {
  const status = document.getElementById('statusChange').value;
  state.inquiries = state.inquiries.map(i => i.id === id ? { ...i, status } : i);
  save(); selectInquiry(id); toast('Status updated.');
}

function sendReply(id) {
  const i = state.inquiries.find(x => x.id === id);
  const body = document.getElementById('replyText').value || '';
  const subject = `Re: ${i.service} inquiry — ${i.artist || i.name}`;
  window.location.href = `mailto:${i.email}?subject=${encodeURIComponent(subject)}&body=${encodeURIComponent(body)}`;
  // Auto-mark as replied
  state.inquiries = state.inquiries.map(x => x.id === id ? { ...x, status: 'replied' } : x);
  save(); selectInquiry(id); toast('Reply opened in mail client.');
}

function deleteInquiry(id) {
  if (!confirm('Delete this inquiry permanently?')) return;
  state.inquiries = state.inquiries.filter(i => i.id !== id);
  selectedInquiryId = null;
  save();
  document.getElementById('inquiryDetail').innerHTML = `<div class="text-center text-cream-3 py-20"><div class="display text-2xl mb-2">Inquiry deleted</div></div>`;
  renderInquiries(); toast('Inquiry removed.');
}

/* ────────────── Tracks ────────────── */
function renderTracks() {
  const tbody = document.getElementById('trackTable');
  tbody.innerHTML = state.tracks.map((t, idx) => `
    <tr>
      <td class="text-cream-3 font-mono text-xs">${String(idx+1).padStart(2,'0')}</td>
      <td><div class="display text-lg">${esc(t.title)}</div></td>
      <td class="text-cream-2">${esc(t.artist)}</td>
      <td class="text-cream-2 text-sm">${esc(t.role)}</td>
      <td class="text-cream-3 font-mono text-sm">${t.year}</td>
      <td>${t.featured ? '<span class="badge new"><span class="dot"></span>Featured</span>' : '<span class="text-cream-3 text-xs">—</span>'}</td>
      <td>
        <div class="flex items-center gap-2">
          <div class="toggle ${t.live?'on':''}" onclick="toggleTrackLive('${t.id}')" style="transform:scale(0.85)"></div>
          <span class="text-xs text-cream-3">${t.live?'Live':'Draft'}</span>
        </div>
      </td>
      <td>
        <div class="flex items-center gap-1 justify-end">
          <button class="btn-icon" onclick="editTrack('${t.id}')" title="Edit">
            <svg width="14" height="14" viewBox="0 0 14 14" fill="none"><path d="M9 2l3 3L5 12H2v-3L9 2z" stroke="currentColor" stroke-width="1.5"/></svg>
          </button>
          <button class="btn-icon" onclick="deleteTrack('${t.id}')" title="Delete">
            <svg width="14" height="14" viewBox="0 0 14 14" fill="none" class="text-rust"><path d="M2 4h10M5 4V2h4v2M3 4l1 9h6l1-9" stroke="currentColor" stroke-width="1.5"/></svg>
          </button>
        </div>
      </td>
    </tr>
  `).join('') || `<tr><td colspan="8" class="text-center text-cream-3 py-12"><div class="display text-xl mb-2">No tracks yet</div><div class="text-sm">Click "Add track" to start building your discography.</div></td></tr>`;
}

function toggleTrackLive(id) {
  state.tracks = state.tracks.map(t => t.id === id ? { ...t, live: !t.live } : t);
  save(); renderTracks(); toast('Track status updated.');
}

function editTrack(id) {
  const t = state.tracks.find(x => x.id === id);
  if (!t) return;
  document.getElementById('modalTitle').textContent = 'Edit track';
  const form = document.getElementById('trackForm');
  form.id.value = t.id;
  form.title.value = t.title;
  form.artist.value = t.artist;
  form.role.value = t.role;
  form.year.value = t.year;
  form.spotifyUrl.value = t.spotifyUrl;
  form.featured.checked = t.featured;
  form.live.checked = t.live;
  openModal();
}

function deleteTrack(id) {
  if (!confirm('Remove this track from your portfolio?')) return;
  state.tracks = state.tracks.filter(t => t.id !== id);
  save(); renderTracks(); toast('Track removed.');
}

document.getElementById('addTrackBtn').onclick = () => {
  document.getElementById('modalTitle').textContent = 'Add track';
  document.getElementById('trackForm').reset();
  document.getElementById('trackForm').id.value = '';
  document.getElementById('trackForm').live.checked = true;
  openModal();
};

document.getElementById('trackForm').onsubmit = (e) => {
  e.preventDefault();
  const f = e.target;
  const data = {
    title: f.title.value.trim(),
    artist: f.artist.value.trim(),
    role: f.role.value.trim(),
    year: parseInt(f.year.value),
    spotifyUrl: f.spotifyUrl.value.trim(),
    featured: f.featured.checked,
    live: f.live.checked,
  };
  if (f.id.value) {
    state.tracks = state.tracks.map(t => t.id === f.id.value ? { ...t, ...data } : t);
    toast('Track updated.');
  } else {
    state.tracks.unshift({ id: 't' + Date.now(), ...data });
    toast('Track added.');
  }
  save(); closeModal(); renderTracks();
};

const openModal = () => document.getElementById('modal').classList.add('show');
const closeModal = () => document.getElementById('modal').classList.remove('show');
document.getElementById('modal').onclick = (e) => { if (e.target.id === 'modal') closeModal(); };

/* ────────────── Services ────────────── */
function renderServices() {
  document.getElementById('servicesGrid').innerHTML = state.services.map(s => `
    <div class="card p-6">
      <div class="flex items-start justify-between mb-6">
        <div class="display text-2xl">${esc(s.name)}</div>
        <div class="eyebrow text-gold">${esc(s.tier)}</div>
      </div>

      <div class="mb-1">
        <span class="display text-5xl">$</span><input type="number" value="${s.price}" onchange="updateService('${s.id}', 'price', parseInt(this.value))" class="display text-5xl bg-transparent border-0 w-32 focus:outline-none text-cream"/>
        <span class="text-cream-3 text-2xl">${esc(s.unit)}</span>
      </div>
      <input type="text" value="${esc(s.subtitle)}" onchange="updateService('${s.id}', 'subtitle', this.value)" class="field-inline eyebrow text-cream-3 mb-6 w-full" />

      <div class="space-y-2 mb-6">
        ${s.items.map((item, idx) => `
          <div class="flex items-center gap-2 text-sm">
            <span class="text-gold">→</span>
            <input type="text" value="${esc(item)}" onchange="updateServiceItem('${s.id}', ${idx}, this.value)" class="field-inline text-cream-2 flex-1" />
            <button class="btn-icon text-rust" onclick="removeServiceItem('${s.id}', ${idx})">×</button>
          </div>
        `).join('')}
        <button class="text-xs text-gold hover:text-cream mt-2" onclick="addServiceItem('${s.id}')">+ Add line item</button>
      </div>

      <div class="flex items-center justify-between pt-4 border-t border-ink-3">
        <span class="text-sm text-cream-2">Live on site</span>
        <div class="toggle ${s.live?'on':''}" onclick="updateService('${s.id}', 'live', ${!s.live})"></div>
      </div>
    </div>
  `).join('');
}

function updateService(id, field, value) {
  state.services = state.services.map(s => s.id === id ? { ...s, [field]: value } : s);
  save(); if (field === 'live') renderServices(); toast('Service updated.');
}
function updateServiceItem(id, idx, value) {
  const s = state.services.find(x => x.id === id);
  s.items[idx] = value;
  save(); toast('Updated.');
}
function addServiceItem(id) {
  const s = state.services.find(x => x.id === id);
  s.items.push('New line item');
  save(); renderServices();
}
function removeServiceItem(id, idx) {
  const s = state.services.find(x => x.id === id);
  s.items.splice(idx, 1);
  save(); renderServices();
}

/* ────────────── Availability ────────────── */
function renderAvailability() {
  const a = state.availability;
  document.getElementById('availToggle').classList.toggle('on', a.accepting);
  document.getElementById('availStatusText').textContent = a.accepting ? 'Currently open to new inquiries' : 'Currently closed — back soon';
  document.getElementById('availPeriod').value = a.period;
  document.getElementById('slotCount').textContent = String(a.slots).padStart(2, '0');
  document.getElementById('previewBar').textContent = a.accepting ? `Available · ${a.period}` : 'Currently closed';
  document.getElementById('previewBody').innerHTML = a.accepting ? `Booking ${esc(a.period)}.<br/>${a.slots === 1 ? 'One slot' : a.slots + ' slots'} remaining.` : 'Currently closed — back soon.';

  document.getElementById('availToggle').onclick = () => { state.availability.accepting = !state.availability.accepting; save(); renderAvailability(); };
  document.getElementById('availPeriod').oninput = (e) => { state.availability.period = e.target.value; save(); renderAvailability(); };
  document.getElementById('slotPlus').onclick = () => { state.availability.slots++; save(); renderAvailability(); };
  document.getElementById('slotMinus').onclick = () => { state.availability.slots = Math.max(0, state.availability.slots - 1); save(); renderAvailability(); };
}

/* ────────────── Settings ────────────── */
function renderSettings() {
  document.querySelectorAll('[data-setting]').forEach(input => {
    input.value = state.settings[input.dataset.setting] || '';
    input.oninput = () => {
      state.settings[input.dataset.setting] = input.value;
      save();
    };
  });
}

document.getElementById('exportData').onclick = () => {
  const blob = new Blob([JSON.stringify(state, null, 2)], { type: 'application/json' });
  const url = URL.createObjectURL(blob);
  const a = document.createElement('a');
  a.href = url; a.download = `ayo-studio-export-${Date.now()}.json`; a.click();
  toast('Data exported.');
};

/* ────────────── Misc ────────────── */
document.getElementById('menuToggle').onclick = () => document.getElementById('sidebar').classList.toggle('open');
document.getElementById('resetDemo').onclick = () => {
  if (!confirm('Reset all demo data to defaults? This cannot be undone.')) return;
  localStorage.removeItem(STORAGE_KEY);
  state = seedState();
  save();
  navigate();
  toast('Demo data reset.');
};
document.getElementById('signOutLink').onclick = () => sessionStorage.removeItem('ayo_session');

// Show user email from "session"
try {
  const session = JSON.parse(sessionStorage.getItem('ayo_session') || '{}');
  if (session.email) document.getElementById('userEmail').textContent = session.email;
} catch(e) {}

/* ────────────── Helpers ────────────── */
function esc(s) { return String(s ?? '').replace(/[&<>"']/g, m => ({'&':'&amp;','<':'&lt;','>':'&gt;','"':'&quot;',"'":'&#39;'}[m])); }
function timeAgo(iso) {
  const d = new Date(iso); const diff = Date.now() - d.getTime();
  const m = Math.floor(diff/60000), h = Math.floor(diff/3600000), days = Math.floor(diff/86400000);
  if (days > 7) return d.toLocaleDateString('en-GB', { day: 'numeric', month: 'short' });
  if (days > 0) return `${days}d ago`;
  if (h > 0) return `${h}h ago`;
  if (m > 0) return `${m}m ago`;
  return 'just now';
}

// Init
navigate();