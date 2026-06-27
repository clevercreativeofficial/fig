<?php
require_once __DIR__ . '/../../path.php';
require_once ADMIN_PATH . '/includes/header.php';
require_once CONFIG . '/db.php';

if (!isset($conn)) {
    die("Database connection failed: " . mysqli_connect_error());
}

// Fetch tracks from database
$sql = "SELECT * FROM tracks ORDER BY featured DESC, release_year DESC";
$result = mysqli_query($conn, $sql);
$tracks = [];
while ($row = mysqli_fetch_assoc($result)) {
    $tracks[] = $row;
}

?>

<!-- ─── TRACKS ─── -->
<section id="tracks" class="section">
    <div class="flex items-center justify-between mb-6">
        <p class="text-cream-2 max-w-xl">Manage tracks shown in the <span class="italic-em">Selected Work</span> section. Reorder, toggle live status, or paste a new Spotify URL.</p>
        <button class="btn-primary" onclick="window.location.href='<?= APP_URL; ?>admin/tracks/add'">
            <svg width="14" height="14" viewBox="0 0 14 14" fill="none">
                <path d="M7 1v12M1 7h12" stroke="currentColor" stroke-width="1.5" />
            </svg>
            Add track
        </button>
    </div>

    <div class="card overflow-hidden">
        <div class="overflow-x-auto">
            <table class="data-table">
                <thead>
                    <tr>
                        <th style="width:40px">#</th>
                        <th>Title</th>
                        <th>Artist</th>
                        <th>Role</th>
                        <th>Year</th>
                        <th>Featured</th>
                        <th>Status</th>
                        <th style="width:120px"></th>
                    </tr>
                </thead>
                <tbody id="trackTable">
                    <?php foreach ($tracks as $track): ?>
                        <tr>
                            <?php static $i = 1; ?>
                            <td class="text-cream-3 font-mono text-xs"><?= $i++ ?></td>
                            <td>
                                <div class="display text-lg"><?= $track['title'] ?></div>
                            </td>
                            <td class="text-cream-2"><?= $track['artist'] ?></td>
                            <td class="text-cream-2 text-sm"><?= $track['user_role'] ?></td>
                            <td class="text-cream-3 font-mono text-sm"><?= $track['release_year'] ?></td>
                            <td><span class="badge new"><span class="dot"></span><?php echo $track['featured'] ? 'Featured' : 'Not Featured'; ?></span></td>
                            <td>
                                <div class="flex items-center gap-2">
                                    <span class="text-xs text-cream-3"><?php echo $track['live_on_site'] ? 'Live' : 'Not Live'; ?></span>
                                </div>
                            </td>
                            <td>
                                <div class="flex items-center gap-1 justify-end">
                                    <button class="btn-icon" title="Edit" onclick="window.location.href='<?= APP_URL; ?>admin/tracks/edit?id=<?= htmlspecialchars($track['id'], ENT_QUOTES, 'UTF-8') ?>'">
                                        <svg width="14" height="14" viewBox="0 0 14 14" fill="none">
                                            <path d="M9 2l3 3L5 12H2v-3L9 2z" stroke="currentColor" stroke-width="1.5" />
                                        </svg>
                                    </button>
                                    <button class="btn-icon" type="button" title="Delete" onclick="deleteTrack('<?= htmlspecialchars($track['id'], ENT_QUOTES, 'UTF-8') ?>')">
                                        <svg width="14" height="14" viewBox="0 0 14 14" fill="none" class="text-rust">
                                            <path d="M2 4h10M5 4V2h4v2M3 4l1 9h6l1-9" stroke="currentColor" stroke-width="1.5" />
                                        </svg>
                                    </button>
                                </div>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </div>
</section>

<!-- Delete -->
<script>
    const deleteTrack = (id) => {
        if (!confirm('Are you sure you want to delete this track?')) return;

        fetch(`<?= APP_URL; ?>/admin/tracks/api/delete.php?id=${id}`)
            .then(res => res.json())
            .then(data => {
                if (data.success) {
                    notyf.success(data.message);

                    setTimeout(() => {
                        location.reload();
                    }, 500);

                } else {
                    notyf.error(data.message);
                }
            })
            .catch(err => {
                notyf.error('Error deleting track: ' + err.message);
            });
    };
</script>

<?php require_once ADMIN_PATH . '/includes/footer.php'; ?>