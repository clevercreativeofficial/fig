<?php
require_once __DIR__ . '/../../../path.php';
require_once ADMIN_PATH . '/includes/header.php';

?>

<!-- ─── ADD TRACKS ─── -->
<section id="tracks" class="section max-w-2xl mx-auto">

    <div class="flex items-center justify-between mb-6">
        <p class="text-cream-2">Add a new track to the <span class="italic-em">Selected Work</span> section. Paste a Spotify URL to automatically generate the embed code.</p>
    </div>

    <form id="trackForm" class="space-y-4" method="POST">
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
                <input type="text" name="user_role" required class="field" placeholder="Production · Mix · Master" />
            </div>
            <div>
                <label class="eyebrow text-cream-3 block mb-2">Year</label>
                <input type="number" name="release_year" required class="field" min="2000" max="2030" />
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
            <button type="button" class="btn-secondary" onclick="window.location.href='<?= APP_URL; ?>admin/tracks'">Cancel</button>
            <button type="submit" class="btn-primary">Save track</button>

        </div>
    </form>
</section>

<script>
    // Handle form submission
    const trackForm = document.getElementById('trackForm');
    const submitButton = document.querySelector('button[type="submit"]');

    trackForm.addEventListener('submit', async (e) => {
        e.preventDefault();

        const formData = new FormData(trackForm);
        const data = Object.fromEntries(formData.entries());

        // UI: lock button immediately
        submitButton.disabled = true;
        const originalText = submitButton.innerText;
        submitButton.innerText = 'Saving...';

        try {
            const response = await fetch('<?= APP_URL ?>/admin/tracks/api/save.php', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json'
                },
                body: JSON.stringify(data),
            });

            const text = await response.text();
            console.log('RAW RESPONSE:', text);

            let result;
            try {
                result = JSON.parse(text);
            } catch (err) {
                throw new Error('Invalid JSON from server');
            }

            if (result.success) {
                notyf.success(result.message);
                trackForm.reset();

                setTimeout(() => {
                    window.location.href = "<?= APP_URL ?>/admin/tracks";
                }, 2000);

            } else {
                notyf.error(result.message || 'Request failed');
            }

        } catch (error) {
            console.error(error);
            notyf.error('Server error or invalid response');

        } finally {
            // ALWAYS restore UI state
            submitButton.disabled = false;
            submitButton.innerText = originalText;
        }
    });
</script>

<?php require_once ADMIN_PATH . '/includes/footer.php'; ?>