<?php
require_once __DIR__ . '/../../../path.php';
require_once ADMIN_PATH . '/includes/header.php';

// Validate and sanitize ID parameter
$id = $_GET['id'] ?? '';

if (empty($id)) {
    die("Invalid track ID");
}

if (!isset($conn)) {
    die("Database connection failed: " . mysqli_connect_error());
}


// Get the track details from the database
$sql = "SELECT * FROM tracks WHERE id = ?";
$stmt = mysqli_prepare($conn, $sql);
mysqli_stmt_bind_param($stmt, "s", $id);
mysqli_stmt_execute($stmt);
$result = mysqli_stmt_get_result($stmt);
$track = mysqli_fetch_assoc($result);

if (!$track) {
    die("Track not found");
}

?>

<!-- ─── EDIT TRACKS ─── -->
<section id="tracks" class="section max-w-2xl mx-auto">

    <div class="flex items-center justify-between mb-6">
        <p class="text-cream-2">Edit the track details in the <span class="italic-em">Selected Work</span> section.</p>
    </div>

    <form id="trackForm" class="space-y-4" method="POST">
        <input type="hidden" name="id" value="<?= $id ?>" />
        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
            <div>
                <label class="eyebrow text-cream-3 block mb-2">Title</label>
                <input type="text" name="title" value="<?= htmlspecialchars($track['title'], ENT_QUOTES, 'UTF-8') ?>" required class="field" />
            </div>
            <div>
                <label class="eyebrow text-cream-3 block mb-2">Artist</label>
                <input type="text" name="artist" value="<?= htmlspecialchars($track['artist'], ENT_QUOTES, 'UTF-8') ?>" required class="field" />
            </div>
            <div>
                <label class="eyebrow text-cream-3 block mb-2">Your role</label>
                <input type="text" name="user_role" value="<?= htmlspecialchars($track['user_role'], ENT_QUOTES, 'UTF-8') ?>" required class="field" placeholder="Production · Mix · Master" />
            </div>
            <div>
                <label class="eyebrow text-cream-3 block mb-2">Year</label>
                <input type="number" name="release_year" value="<?= htmlspecialchars($track['release_year'], ENT_QUOTES, 'UTF-8') ?>" required class="field" min="2000" max="2030" />
            </div>
        </div>
        <div>
            <label class="eyebrow text-cream-3 block mb-2">Spotify track URL</label>
            <input type="text" name="spotifyUrl" value="<?= htmlspecialchars($track['spotify_url'], ENT_QUOTES, 'UTF-8') ?>" required class="field" placeholder="https://open.spotify.com/track/..." />
            <p class="eyebrow text-cream-3 mt-2">Paste the share URL — embed code generated automatically.</p>
        </div>
        <div class="flex items-center gap-6 pt-2">
            <label class="flex items-center gap-2 text-sm cursor-pointer">
                <input type="checkbox" name="featured" <?= $track['featured'] ? 'checked' : '' ?> class="accent-gold" /> Featured (large display)
            </label>
            <label class="flex items-center gap-2 text-sm cursor-pointer">
                <input type="checkbox" name="live" <?= $track['live_on_site'] ? 'checked' : '' ?> class="accent-gold" /> Live on site
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
            const response = await fetch('<?= APP_URL; ?>/admin/tracks/api/update.php', {
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
                    window.location.href = "<?= APP_URL; ?>/admin/tracks";
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