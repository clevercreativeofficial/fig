<?php
require_once __DIR__ . '/../../path.php';
require_once ADMIN_PATH . '/includes/header.php';

if (!isset($conn)) {
    die("Database connection not established.");
}

// Fetch settings from DB
$result = mysqli_query($conn, "SELECT * FROM settings");

if (!$result) {
    error_log('Settings query failed: ' . mysqli_error($conn));
    $settings = [];
} else {
    $settings = [];
    while ($row = mysqli_fetch_assoc($result)) {
        $settings[$row['name']] = $row['value'];
    }
    mysqli_free_result($result);
}

?>

<!-- ─── SETTINGS ─── -->
<section id="settings" class="section">
    <p class="text-cream-2 mb-8 max-w-xl">Profile, contact lines, social presence, and the headline copy that drives the site.</p>
    <span><?= $settings['hero_headline'] ?? 's' ?></span>

    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6 max-w-5xl">

        <!-- Profile -->
        <div class="card p-6 lg:col-span-2">
            <div class="eyebrow text-gold mb-4">— Profile</div>
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div>
                    <label class="eyebrow text-cream-3 block mb-2">Alias</label>
                    <input type="text" data-setting="alias" class="field" value="<?= htmlspecialchars($settings['alias'] ?? '') ?>" />
                </div>
                <div>
                    <label class="eyebrow text-cream-3 block mb-2">Role / tagline</label>
                    <input type="text" data-setting="role_tagline" class="field" value="<?= htmlspecialchars($settings['role_tagline'] ?? '') ?>" />
                </div>
                <div>
                    <label class="eyebrow text-cream-3 block mb-2">Based in</label>
                    <input type="text" data-setting="based_in" class="field" value="<?= htmlspecialchars($settings['based_in'] ?? '') ?>" />
                </div>
                <div>
                    <label class="eyebrow text-cream-3 block mb-2">Studio name</label>
                    <input type="text" data-setting="studio_name" class="field" value="<?= htmlspecialchars($settings['studio_name'] ?? '') ?>" />
                </div>
                <div class="md:col-span-2">
                    <label class="eyebrow text-cream-3 block mb-2">Hero headline</label>
                    <input type="text" data-setting="hero_headline" class="field" value="<?= htmlspecialchars($settings['hero_headline'] ?? '') ?>" />
                </div>
            </div>
        </div>

        <!-- Contact -->
        <div class="card p-6">
            <div class="eyebrow text-gold mb-4">— Contact</div>
            <div class="space-y-4">
                <div>
                    <label class="eyebrow text-cream-3 block mb-2">Email</label>
                    <input type="email" data-setting="email" class="field" value="<?= htmlspecialchars($settings['email'] ?? '') ?>" />
                </div>
                <div>
                    <label class="eyebrow text-cream-3 block mb-2">Phone</label>
                    <input type="text" data-setting="phone" class="field" value="<?= htmlspecialchars($settings['phone'] ?? '') ?>" />
                </div>
            </div>
        </div>

        <!-- Social -->
        <div class="card p-6">
            <div class="eyebrow text-gold mb-4">— Social</div>
            <div class="space-y-4">
                <div>
                    <label class="eyebrow text-cream-3 block mb-2">Instagram</label>
                    <input type="text" data-setting="instagram" class="field" placeholder="@handle" value="<?= htmlspecialchars($settings['instagram'] ?? '') ?>" />
                </div>
                <div>
                    <label class="eyebrow text-cream-3 block mb-2">Spotify artist URL</label>
                    <input type="text" data-setting="spotify_url" class="field" value="<?= htmlspecialchars($settings['spotify_url'] ?? '') ?>" />
                </div>
                <div>
                    <label class="eyebrow text-cream-3 block mb-2">YouTube</label>
                    <input type="text" data-setting="youtube" class="field" value="<?= htmlspecialchars($settings['youtube'] ?? '') ?>" />
                </div>
            </div>
        </div>

    </div>

    <div class="mt-8 max-w-5xl flex items-center justify-between text-sm">
        <button type="submit" class="btn-primary">
            <svg width="14" height="14" viewBox="0 0 14 14" fill="none">
                <path d="M7 1v9M3 6l4 4 4-4M1 13h12" stroke="currentColor" stroke-width="1.5" />
            </svg>
                Save
        </button>
    </div>
</section>


<?php require_once ADMIN_PATH . '/includes/footer.php'; ?>