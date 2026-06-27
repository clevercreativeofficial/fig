/* ────────────── Catalogue data ──────────────
   Each entry maps to a real Spotify track ID for visual demo.
   Replace with your actual catalogue. The schema is intentional —
   mirrors the admin.html state shape so a future backend can sync.
*/
const catalogue = [
    { id: 't1', title: 'Free Mind', artist: 'Tems', year: 2023, roles: ['Production', 'Mix'], genres: ['R&B', 'Afrobeats'], spotifyId: '0XwFvcrWzcYK7OuotsXJW1', sync: true, label: 'Since 93 / RCA' },
    { id: 't2', title: 'Water', artist: 'Tyla', year: 2023, roles: ['Mix'], genres: ['Amapiano', 'R&B'], spotifyId: '7tNO3vJC34zZ48NTfIcSDD', sync: true, label: 'Epic Records' },
    { id: 't3', title: 'Sungba', artist: 'AÏSHA', year: 2023, roles: ['Production', 'Mix'], genres: ['Afrobeats'], spotifyId: '0XwFvcrWzcYK7OuotsXJW1', sync: false, label: 'Independent' },
    { id: 't4', title: 'Joha', artist: 'Asake', year: 2022, roles: ['Co-Production'], genres: ['Afrobeats', 'Amapiano'], spotifyId: '32tlLzZwcbqHUeeRIIjf5G', sync: true, label: 'YBNL / EMPIRE' },
    { id: 't5', title: 'Last Last', artist: 'Burna Boy', year: 2022, roles: ['Mix'], genres: ['Afrobeats'], spotifyId: '6PQ88X9TkUIAUIZJHW2upE', sync: true, label: 'Atlantic Records' },
    { id: 't6', title: 'Lavender Skies', artist: 'Saint Joy', year: 2022, roles: ['Production', 'Mix', 'Master'], genres: ['Alté', 'R&B'], spotifyId: '7tNO3vJC34zZ48NTfIcSDD', sync: true, label: 'Independent' },
    { id: 't7', title: 'Slow Hours', artist: 'Téo Maré', year: 2022, roles: ['Mix', 'Master'], genres: ['R&B', 'Soul'], spotifyId: '0XwFvcrWzcYK7OuotsXJW1', sync: true, label: 'Independent' },
    { id: 't8', title: 'Essence', artist: 'Wizkid ft. Tems', year: 2021, roles: ['Master'], genres: ['Afrobeats', 'R&B'], spotifyId: '1QylwwAEKnegzJ5IWkEkSx', sync: true, label: 'Starboy / RCA' },
    { id: 't9', title: 'Burundian Blue', artist: 'Ola Ray', year: 2021, roles: ['Production', 'Mix'], genres: ['Afro-soul'], spotifyId: '6PQ88X9TkUIAUIZJHW2upE', sync: true, label: 'Independent' },
    { id: 't10', title: 'Honey & Salt', artist: 'K. Adeyemi', year: 2021, roles: ['Production'], genres: ['R&B', 'Highlife'], spotifyId: '32tlLzZwcbqHUeeRIIjf5G', sync: false, label: 'Mavin Records' },
    { id: 't11', title: 'Moon & Sun', artist: 'Moon & Sun', year: 2020, roles: ['Production', 'Mix'], genres: ['Alté'], spotifyId: '7tNO3vJC34zZ48NTfIcSDD', sync: true, label: 'Independent' },
    { id: 't12', title: 'Lekki at 4am', artist: 'Venice', year: 2020, roles: ['Mix'], genres: ['Afrobeats', 'R&B'], spotifyId: '0XwFvcrWzcYK7OuotsXJW1', sync: false, label: 'Independent' },
    { id: 't13', title: 'Nala\'s Lullaby', artist: 'Nala', year: 2020, roles: ['Production', 'Mix', 'Master'], genres: ['R&B'], spotifyId: '6PQ88X9TkUIAUIZJHW2upE', sync: true, label: 'Independent' },
    { id: 't14', title: 'First Light', artist: 'Mayowa', year: 2019, roles: ['Co-Production'], genres: ['Afro-fusion'], spotifyId: '1QylwwAEKnegzJ5IWkEkSx', sync: false, label: 'Independent' },
    { id: 't15', title: 'Pigment', artist: 'AYÒ', year: 2019, roles: ['Production', 'Mix', 'Master'], genres: ['Instrumental'], spotifyId: '32tlLzZwcbqHUeeRIIjf5G', sync: true, label: 'Self-released' },
];

/* ────────────── State ────────────── */
let filters = { role: 'all', search: '', sync: false };
let view = 'editorial';
let playingId = null;

/* ────────────── Init stats ────────────── */
document.getElementById('statTotal').textContent = catalogue.length;
document.getElementById('statSync').textContent = catalogue.filter(t => t.sync).length;
const years = catalogue.map(t => t.year);
document.getElementById('statYears').textContent = (Math.max(...years) - Math.min(...years) + 1);

/* ────────────── Filtering ────────────── */
function getFiltered() {
    return catalogue.filter(t => {
        if (filters.role !== 'all' && !t.roles.some(r => r.startsWith(filters.role) || r === filters.role)) return false;
        if (filters.sync && !t.sync) return false;
        if (filters.search) {
            const q = filters.search.toLowerCase();
            const hay = `${t.title} ${t.artist} ${t.genres.join(' ')} ${t.label}`.toLowerCase();
            if (!hay.includes(q)) return false;
        }
        return true;
    });
}

/* ────────────── Render: editorial ────────────── */
function renderEditorial() {
    const filtered = getFiltered();
    if (!filtered.length) return showEmpty();

    // Group by year
    const byYear = {};
    filtered.forEach(t => { (byYear[t.year] ||= []).push(t); });
    const yearsDesc = Object.keys(byYear).sort((a, b) => b - a);

    const html = yearsDesc.map(year => `
    <div>
      <div class="year-marker">
        <h2 class="display text-6xl lg:text-7xl text-cream">${year}</h2>
        <div class="eyebrow text-cream-3">${byYear[year].length} release${byYear[year].length > 1 ? 's' : ''}</div>
      </div>
      ${byYear[year].map(t => trackRowHTML(t)).join('')}
    </div>
  `).join('');

    document.getElementById('editorialView').innerHTML = html;
    hideEmpty();
}

function trackRowHTML(t) {
    const isPlaying = playingId === t.id;
    return `
    <div>
      <div class="track-row ${isPlaying ? 'playing' : ''}" onclick="togglePlay('${t.id}')">
        <div class="play-btn">
          ${isPlaying
            ? `<svg width="12" height="12" viewBox="0 0 12 12" fill="none"><rect x="2" y="2" width="3" height="8" fill="currentColor"/><rect x="7" y="2" width="3" height="8" fill="currentColor"/></svg>`
            : `<svg width="12" height="12" viewBox="0 0 12 12" fill="currentColor"><path d="M3 2v8l7-4z"/></svg>`}
        </div>
        <div class="min-w-0">
          <div class="flex items-baseline gap-3 mb-1 flex-wrap">
            <h3 class="display text-2xl lg:text-3xl">${esc(t.title)}</h3>
            <span class="text-cream-3">·</span>
            <span class="text-cream-2">${esc(t.artist)}</span>
          </div>
          <div class="flex flex-wrap items-center gap-2 text-xs">
            ${t.roles.map(r => `<span class="tag">${esc(r)}</span>`).join('')}
            ${t.genres.map(g => `<span class="text-cream-3">${esc(g)}</span>`).join(' · ')}
            ${t.sync ? '<span class="tag sync">✦ Sync</span>' : ''}
          </div>
        </div>
        <div class="text-right hidden md:block">
          <div class="eyebrow text-cream-3">${esc(t.label)}</div>
        </div>
      </div>
      <div class="embed-slot ${isPlaying ? 'open' : ''}" id="embed-${t.id}">
        ${isPlaying ? `<iframe style="border-radius:6px" src="https://open.spotify.com/embed/track/${t.spotifyId}?utm_source=generator&theme=0" width="100%" height="152" frameborder="0" allow="autoplay; clipboard-write; encrypted-media; fullscreen; picture-in-picture" loading="lazy"></iframe>` : ''}
      </div>
    </div>
  `;
}

/* ────────────── Render: index (compact) ────────────── */
function renderIndex() {
    const filtered = getFiltered();
    if (!filtered.length) return showEmpty();

    const sorted = [...filtered].sort((a, b) => b.year - a.year);

    const header = `
    <div class="index-row eyebrow text-cream-3" style="border-bottom-color:#2A241F; padding-bottom:0.5rem">
      <div>#</div>
      <div>Title</div>
      <div class="col-artist">Artist</div>
      <div class="col-role">Role</div>
      <div>Year</div>
      <div></div>
    </div>
  `;

    const rows = sorted.map((t, idx) => `
    <div>
      <div class="index-row ${playingId === t.id ? 'playing' : ''}" onclick="togglePlay('${t.id}')">
        <div class="font-mono text-xs text-cream-3">${String(idx + 1).padStart(3, '0')}</div>
        <div>
          <div class="text-cream truncate">${esc(t.title)}${t.sync ? ' <span class="tag sync ml-1">Sync</span>' : ''}</div>
          <div class="text-xs text-cream-3 md:hidden mt-0.5">${esc(t.artist)} · ${esc(t.roles.join(' / '))}</div>
        </div>
        <div class="col-artist text-cream-2 truncate">${esc(t.artist)}</div>
        <div class="col-role text-cream-3 text-xs">${esc(t.roles.join(' / '))}</div>
        <div class="font-mono text-cream-3 text-xs">${t.year}</div>
        <div>
          <div class="play-btn" style="width:28px;height:28px">
            ${playingId === t.id
            ? `<svg width="9" height="9" viewBox="0 0 12 12" fill="currentColor"><rect x="2" y="2" width="3" height="8"/><rect x="7" y="2" width="3" height="8"/></svg>`
            : `<svg width="9" height="9" viewBox="0 0 12 12" fill="currentColor"><path d="M3 2v8l7-4z"/></svg>`}
          </div>
        </div>
      </div>
      <div class="embed-slot ${playingId === t.id ? 'open' : ''}">
        ${playingId === t.id ? `<iframe style="border-radius:6px" src="https://open.spotify.com/embed/track/${t.spotifyId}?utm_source=generator&theme=0" width="100%" height="152" frameborder="0" allow="autoplay; clipboard-write; encrypted-media; fullscreen; picture-in-picture" loading="lazy"></iframe>` : ''}
      </div>
    </div>
  `).join('');

    document.getElementById('indexView').innerHTML = header + rows;
    hideEmpty();
}

/* ────────────── Single-active embed pattern ──────────────
   Critical: only ONE Spotify iframe is mounted at a time.
   Mounting 15 iframes would load ~7MB of Spotify player code.
*/
function togglePlay(id) {
    playingId = playingId === id ? null : id;
    render();
    // Scroll the row into view if it was just opened
    if (playingId) {
        setTimeout(() => {
            const slot = document.getElementById(`embed-${id}`);
            if (slot) slot.scrollIntoView({ behavior: 'smooth', block: 'nearest' });
        }, 100);
    }
}

/* ────────────── Render dispatch ────────────── */
function render() {
    if (view === 'editorial') {
        document.getElementById('editorialView').classList.remove('hidden');
        document.getElementById('indexView').classList.add('hidden');
        renderEditorial();
    } else {
        document.getElementById('editorialView').classList.add('hidden');
        document.getElementById('indexView').classList.remove('hidden');
        renderIndex();
    }
}

function showEmpty() {
    document.getElementById('emptyState').classList.remove('hidden');
    document.getElementById('editorialView').classList.add('hidden');
    document.getElementById('indexView').classList.add('hidden');
}
function hideEmpty() { document.getElementById('emptyState').classList.add('hidden'); }

/* ────────────── Bindings ────────────── */
document.getElementById('roleFilters').addEventListener('click', (e) => {
    if (!e.target.matches('[data-role]')) return;
    filters.role = e.target.dataset.role;
    document.querySelectorAll('[data-role]').forEach(b => b.classList.toggle('active', b === e.target));
    playingId = null; render();
});

document.getElementById('syncToggle').addEventListener('click', (e) => {
    filters.sync = !filters.sync;
    e.target.classList.toggle('active', filters.sync);
    playingId = null; render();
});

document.getElementById('searchInput').addEventListener('input', (e) => {
    filters.search = e.target.value.trim();
    playingId = null; render();
});

document.querySelectorAll('[data-view]').forEach(btn => {
    btn.addEventListener('click', () => {
        view = btn.dataset.view;
        document.querySelectorAll('[data-view]').forEach(b => b.classList.toggle('active', b === btn));
        render();
    });
});

function resetFilters() {
    filters = { role: 'all', search: '', sync: false };
    document.getElementById('searchInput').value = '';
    document.querySelectorAll('[data-role]').forEach(b => b.classList.toggle('active', b.dataset.role === 'all'));
    document.getElementById('syncToggle').classList.remove('active');
    render();
}

/* ────────────── Utils ────────────── */
function esc(s) { return String(s ?? '').replace(/[&<>"']/g, m => ({ '&': '&amp;', '<': '&lt;', '>': '&gt;', '"': '&quot;', "'": '&#39;' }[m])); }

// Init
render();
