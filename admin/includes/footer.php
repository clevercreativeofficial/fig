<!-- page content ends immediately before this comment -->
</div>
</main>

<!-- ═══════ MODAL ═══════ -->
<div id="modal" class="modal-backdrop">
  <div class="modal p-6 lg:p-8">
    <div class="flex items-start justify-between mb-6">
      <h3 id="modalTitle" class="display text-3xl">Add track</h3>
      <button class="btn-icon" onclick="closeModal()">
        <svg width="16" height="16" viewBox="0 0 16 16" fill="none">
          <path d="M3 3l10 10M13 3L3 13" stroke="currentColor" stroke-width="1.5" />
        </svg>
      </button>
    </div>

    <form id="trackForm" class="space-y-4">
      <input type="hidden" name="id" />
      <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
        <div>
          <label class="eyebrow text-cream-3 block mb-2">Title</label>
          <input type="text" name="title" required class="field" />
        </div>
        <div>
          <label class="eyebrow text-cream-3 block mb-2">Artist</label>
          <input type="text" name="artist" required class="field" />
        </div>
        <div>
          <label class="eyebrow text-cream-3 block mb-2">Your role</label>
          <input type="text" name="role" required class="field" placeholder="Production · Mix · Master" />
        </div>
        <div>
          <label class="eyebrow text-cream-3 block mb-2">Year</label>
          <input type="number" name="year" required class="field" min="2000" max="2030" />
        </div>
      </div>
      <div>
        <label class="eyebrow text-cream-3 block mb-2">Spotify track URL</label>
        <input type="text" name="spotifyUrl" required class="field" placeholder="https://open.spotify.com/track/..." />
        <p class="eyebrow text-cream-3 mt-2">Paste the share URL — embed code generated automatically.</p>
      </div>
      <div class="flex items-center gap-6 pt-2">
        <label class="flex items-center gap-2 text-sm cursor-pointer">
          <input type="checkbox" name="featured" class="accent-gold" /> Featured (large display)
        </label>
        <label class="flex items-center gap-2 text-sm cursor-pointer">
          <input type="checkbox" name="live" checked class="accent-gold" /> Live on site
        </label>
      </div>

      <div class="flex items-center justify-end gap-3 pt-6 border-t border-ink-3 mt-6">
        <button type="button" class="btn-secondary" onclick="closeModal()">Cancel</button>
        <button type="submit" class="btn-primary">Save track</button>
      </div>
    </form>
  </div>
</div>

<!-- Toast -->
<div id="toast" class="toast">
  <svg width="16" height="16" viewBox="0 0 16 16" fill="none">
    <circle cx="8" cy="8" r="6" stroke="#C8954B" stroke-width="1.5" />
    <path d="M5 8l2 2 4-4" stroke="#C8954B" stroke-width="1.5" />
  </svg>
  <span id="toastMsg">Saved.</span>
</div>

<!-- Notyf JS (loaded before notification.php which uses it) -->
<script src="https://cdn.jsdelivr.net/npm/notyf@3/notyf.min.js"></script>
<script src="<?= APP_URL ?>admin/assets/js/notification.js"></script>

<!-- Renders any flash messages from $_SESSION as Notyf toasts -->
<?php require_once CONFIG . '/notification.php'; ?>

<!-- Mobile sidebar toggle -->
<script>
  const menuBtn = document.getElementById('menuToggle');
  if (menuBtn) menuBtn.onclick = () => document.getElementById('sidebar').classList.toggle('open');
</script>

</body>

</html>