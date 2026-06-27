<?php
require_once __DIR__ . '/../path.php';
require_once CONFIG . '/db.php';
require_once INCLUDES . '/header.php';

if (!isset($conn)) {
    die("Database connection failed: " . mysqli_connect_error());
}

// Get featured tracks from database
$sql = "SELECT * FROM tracks WHERE featured = 1 AND live_on_site = 1 ORDER BY release_year DESC";
$result = mysqli_query($conn, $sql);
$tracks = [];
while ($row = mysqli_fetch_assoc($result)) {
    $tracks[] = $row;
}

// Get all tracks for admin table except featured tracks
$sql = "SELECT * FROM tracks WHERE featured = 0 AND live_on_site = 1 ORDER BY release_year DESC";
$result = mysqli_query($conn, $sql);
$allTracks = [];
while ($row = mysqli_fetch_assoc($result)) {
    $allTracks[] = $row;
}

/**
 * DISCOGRAPHY PAGE
 * Full track catalog with filtering, search, and advanced layout
 */

// Sample data structure - replace with your actual data source
$allTracks = [
    [
        'id' => 1,
        'title' => 'Midnight Reflections',
        'artist' => 'Amon Kune',
        'role' => 'Production & Mix',
        'genre' => 'Afro Fusion',
        'release_year' => 2024,
        'spotify_url' => 'https://open.spotify.com/embed/track/...',
        'description' => 'A contemplative blend of modern production with traditional African instrumentation.'
    ],
    [
        'id' => 2,
        'title' => 'Rise Up',
        'artist' => 'Chioma feat. Wizkid',
        'role' => 'Mixing',
        'genre' => 'Hip-Hop',
        'release_year' => 2024,
        'spotify_url' => 'https://open.spotify.com/embed/track/...',
        'description' => null
    ],
    [
        'id' => 3,
        'title' => 'Grace',
        'artist' => 'The Gospel Collective',
        'role' => 'Production',
        'genre' => 'Gospel',
        'release_year' => 2023,
        'spotify_url' => 'https://open.spotify.com/embed/track/...',
        'description' => null
    ],
    [
        'id' => 4,
        'title' => 'Urban Tales',
        'artist' => 'M.A.K',
        'role' => 'Co-Production',
        'genre' => 'Hip-Hop',
        'release_year' => 2023,
        'spotify_url' => 'https://open.spotify.com/embed/track/...',
        'description' => null
    ],
    [
        'id' => 5,
        'title' => 'Rhythm of the City',
        'artist' => 'Nonso',
        'role' => 'Mix Engineering',
        'genre' => 'R&B',
        'release_year' => 2023,
        'spotify_url' => 'https://open.spotify.com/embed/track/...',
        'description' => null
    ],
    // Add more tracks here...
];

// Get unique filters
$roles = array_unique(array_column($allTracks, 'role'));
$genres = array_unique(array_column($allTracks, 'genre'));
$years = array_unique(array_column($allTracks, 'release_year'));
rsort($years);

// Get filter parameters from URL
$selectedRole = isset($_GET['role']) ? $_GET['role'] : null;
$selectedGenre = isset($_GET['genre']) ? $_GET['genre'] : null;
$selectedYear = isset($_GET['year']) ? $_GET['year'] : null;
$searchQuery = isset($_GET['search']) ? strtolower(trim($_GET['search'])) : null;

// Filter tracks
$filteredTracks = array_filter($allTracks, function($track) use ($selectedRole, $selectedGenre, $selectedYear, $searchQuery) {
    if ($selectedRole && $track['role'] !== $selectedRole) return false;
    if ($selectedGenre && $track['genre'] !== $selectedGenre) return false;
    if ($selectedYear && $track['release_year'] != $selectedYear) return false;
    if ($searchQuery && !stripos($track['title'] . ' ' . $track['artist'], $searchQuery)) return false;
    return true;
});

// Sort by year (newest first), then by title
usort($filteredTracks, function($a, $b) {
    if ($a['release_year'] !== $b['release_year']) {
        return $b['release_year'] - $a['release_year'];
    }
    return strcmp($a['title'], $b['title']);
});

// Group by year for display
$tracksByYear = [];
foreach ($filteredTracks as $track) {
    $year = $track['release_year'];
    if (!isset($tracksByYear[$year])) {
        $tracksByYear[$year] = [];
    }
    $tracksByYear[$year][] = $track;
}
krsort($tracksByYear);

$trackCount = count($filteredTracks);
$totalCount = count($allTracks);
?>

<section class="px-6 lg:px-12 mt-24 lg:mt-32">
    <div class="grid grid-cols-12 gap-6">
        <div class="col-span-12 lg:col-span-10 reveal">
            <h1 class="display text-6xl md:text-7xl lg:text-8xl text-cream mb-6">
                Complete <span class="italic">Catalog.</span>
            </h1>
            <p class="text-cream-2 max-w-3xl mb-4">
                <?= $totalCount ?> productions across multiple genres and production roles. 
                Filter by role, genre, or year to explore my work.
            </p>
            <p class="text-cream-3 text-sm">
                Showing <?= $trackCount ?> of <?= $totalCount ?> tracks
                <?php if ($selectedRole || $selectedGenre || $selectedYear || $searchQuery): ?>
                    <a href="?discography" class="text-gold hover:text-cream transition-colors ml-2">Clear filters</a>
                <?php endif; ?>
            </p>
        </div>
    </div>
</section>

<!-- TRACKS SECTION -->
<section class="px-6 lg:px-12 py-24">
    <?php if ($trackCount > 0): ?>
        <?php foreach ($tracksByYear as $year => $tracks): ?>
            <div class="mb-20">
                <div class="eyebrow text-gold mb-8 reveal"><?= $year ?></div>
                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                    <?php foreach ($tracks as $track): ?>
                        <div class="reveal embed-card p-6 hover:border-gold transition-colors group">
                            <!-- Track Header -->
                            <div class="mb-4">
                                <div class="flex items-start justify-between gap-3 mb-2">
                                    <div class="flex-1">
                                        <h3 class="display text-xl text-cream group-hover:text-gold transition-colors">
                                            <?= htmlspecialchars($track['title']) ?>
                                        </h3>
                                        <p class="text-cream-3 text-sm mt-1">
                                            <?= htmlspecialchars($track['artist']) ?>
                                        </p>
                                    </div>
                                </div>
                                
                                <!-- Metadata -->
                                <div class="flex flex-wrap gap-2 mt-3 pt-3 border-t border-[#3D3935]">
                                    <span class="inline-block eyebrow text-gold text-xs">
                                        <?= htmlspecialchars($track['role']) ?>
                                    </span>
                                    <span class="inline-block eyebrow text-cream-3 text-xs">
                                        <?= htmlspecialchars($track['genre']) ?>
                                    </span>
                                </div>

                                <?php if ($track['description']): ?>
                                    <p class="text-cream-3 text-xs mt-3 leading-relaxed">
                                        <?= htmlspecialchars($track['description']) ?>
                                    </p>
                                <?php endif; ?>
                            </div>

                            <!-- Spotify Embed -->
                            <iframe 
                                style="border-radius: 6px" 
                                src="<?= htmlspecialchars($track['spotify_url']) ?>" 
                                width="100%" 
                                height="152" 
                                frameborder="0" 
                                allow="autoplay; clipboard-write; encrypted-media; fullscreen; picture-in-picture" 
                                loading="lazy">
                            </iframe>
                        </div>
                    <?php endforeach; ?>
                </div>
            </div>
        <?php endforeach; ?>
    <?php else: ?>
        <!-- Empty State -->
        <div class="text-center py-20 reveal">
            <div class="display text-3xl text-cream mb-3">No tracks found</div>
            <p class="text-cream-3 mb-6">Try adjusting your filters or search terms.</p>
            <a href="?discography" class="btn-ghost font-semibold">Reset filters →</a>
        </div>
    <?php endif; ?>
</section>

<!-- STATISTICS FOOTER -->
<?php if ($trackCount > 0): ?>
<section class="px-6 lg:px-12 py-16 lg:py-24 border-t border-[#3D3935] bg-ink-2/30">
    <div class="grid grid-cols-3 gap-6 reveal">
        <div>
            <div class="eyebrow text-gold mb-2">Tracks</div>
            <div class="display text-3xl text-cream"><?= $trackCount ?></div>
        </div>
        <div>
            <div class="eyebrow text-gold mb-2">Genres</div>
            <div class="display text-3xl text-cream"><?= count(array_unique(array_column($filteredTracks, 'genre'))) ?></div>
        </div>
        <div>
            <div class="eyebrow text-gold mb-2">Years</div>
            <div class="display text-3xl text-cream"><?= count(array_unique(array_column($filteredTracks, 'release_year'))) ?></div>
        </div>
    </div>
</section>
<?php endif; ?>

<?php require_once INCLUDES . '/footer.php'; ?>