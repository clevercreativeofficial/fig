<?php
require_once __DIR__ . '/../path.php';
require_once CONFIG . '/db.php';
require_once INCLUDES . '/header.php';

if (!isset($conn)) {
    die("Database connection failed: " . mysqli_connect_error());
}

/**
 * DISCOGRAPHY PAGE
 * Full track catalog with filtering, search, and advanced layout
 * Integrates directly with your tracks table
 */

// Get ALL live tracks (both featured and non-featured) from database
$sql = "SELECT * FROM tracks WHERE live_on_site = 1 ORDER BY release_year DESC, title ASC";
$result = mysqli_query($conn, $sql);

if (!$result) {
    die("Query failed: " . mysqli_error($conn));
}

$allTracks = [];
while ($row = mysqli_fetch_assoc($result)) {
    $allTracks[] = $row;
}

// Get unique filters from actual data
$roles = array_values(array_unique(array_filter(array_column($allTracks, 'role'))));
$genres = array_values(array_unique(array_filter(array_column($allTracks, 'genre'))));
$years = array_values(array_reverse(array_unique(array_column($allTracks, 'release_year'))));

// Get filter parameters from URL
$selectedRole = isset($_GET['role']) ? $_GET['role'] : null;
$selectedGenre = isset($_GET['genre']) ? $_GET['genre'] : null;
$selectedYear = isset($_GET['year']) ? $_GET['year'] : null;
$searchQuery = isset($_GET['search']) ? strtolower(trim($_GET['search'])) : null;

// Validate filters against actual data
if ($selectedRole && !in_array($selectedRole, $roles)) $selectedRole = null;
if ($selectedGenre && !in_array($selectedGenre, $genres)) $selectedGenre = null;
if ($selectedYear && !in_array((int)$selectedYear, $years)) $selectedYear = null;

// Filter tracks
$filteredTracks = array_filter($allTracks, function ($track) use ($selectedRole, $selectedGenre, $selectedYear, $searchQuery) {
    if ($selectedRole && $track['user_role'] !== $selectedRole) return false;
    if ($selectedGenre && $track['genre'] !== $selectedGenre) return false;
    if ($selectedYear && $track['release_year'] != $selectedYear) return false;
    if ($searchQuery) {
        $searchableText = strtolower($track['title'] . ' ' . $track['artist']);
        if (strpos($searchableText, $searchQuery) === false) return false;
    }
    return true;
});

// Sort by year (newest first), then by title
usort($filteredTracks, function ($a, $b) {
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

$trackCount = count($filteredTracks);
$totalCount = count($allTracks);

// Build filter URLs (preserving other filters)
function buildFilterUrl($filterName, $filterValue)
{
    global $selectedRole, $selectedGenre, $selectedYear, $searchQuery;
    $params = [];
    if ($filterName !== 'role' && $selectedRole) $params['role'] = urlencode($selectedRole);
    if ($filterName !== 'genre' && $selectedGenre) $params['genre'] = urlencode($selectedGenre);
    if ($filterName !== 'year' && $selectedYear) $params['year'] = $selectedYear;
    if ($filterName !== 'search' && $searchQuery) $params['search'] = urlencode($searchQuery);
    if ($filterValue) $params[$filterName] = urlencode($filterValue);
    $queryString = http_build_query($params);
    return "?" . ($queryString ? $queryString : 'discography');
}

function buildClearUrl()
{
    return "?discography";
}
?>

<section class="px-6 lg:px-12 mt-24 lg:mt-32 border-b border-[#3D3935]">
    <div class="grid grid-cols-12 gap-6 mb-12">
        <div class="col-span-12 lg:col-span-10 reveal">
            <h2 class="display text-6xl md:text-7xl lg:text-8xl text-cream reveal">
                Browse every <span class="italic-em">production.</span><br />
                Filter by craft.
            </h2>
            <p class="text-cream-2 max-w-3xl my-4">
                <?= $totalCount ?> production<?= $totalCount !== 1 ? 's' : '' ?> across multiple genres and production roles.
                Filter by role, genre, or year to explore my work.
            </p>
            <p class="text-cream-3 text-sm">
                Showing <strong><?= $trackCount ?></strong> of <strong><?= $totalCount ?></strong> track<?= $totalCount !== 1 ? 's' : '' ?>
                <?php if ($selectedRole || $selectedGenre || $selectedYear || $searchQuery): ?>
                    · <a href="<?= buildClearUrl() ?>" class="text-gold hover:text-cream transition-colors">Clear filters</a>
                <?php endif; ?>
            </p>
        </div>
    </div>

    <!-- SEARCH & FILTERS -->
    <div class="space-y-6 reveal pb-12">
        <!-- Search -->
        <div>
            <form method="GET" class="flex flex-col sm:flex-row gap-2">
                <input
                    type="text"
                    name="search"
                    placeholder="Search by title or artist..."
                    value="<?= htmlspecialchars($searchQuery ?? '', ENT_QUOTES) ?>"
                    class="flex-1 bg-[#242019] border border-[#3D3935] px-4 py-3 text-cream placeholder-cream-3 focus:outline-none focus:border-gold">
                <button type="submit" class="px-6 py-3 bg-gold text-black font-semibold hover:bg-[#D4B870] transition-colors">
                    Search
                </button>
            </form>
        </div>

        <!-- Filter Chips -->
        <div class="space-y-4">
            <!-- By Role -->
            <?php if (!empty($roles)): ?>
                <div>
                    <div class="eyebrow text-cream-3 mb-3">PRODUCTION ROLE</div>
                    <div class="flex flex-wrap gap-2">
                        <a href="<?= buildClearUrl() ?>" class="filter-chip <?= !$selectedRole ? 'active' : '' ?>">All Roles</a>
                        <?php foreach ($roles as $role): ?>
                            <a href="<?= buildFilterUrl('role', $role) ?>"
                                class="filter-chip <?= $selectedRole === $role ? 'active' : '' ?>">
                                <?= htmlspecialchars($role) ?>
                            </a>
                        <?php endforeach; ?>
                    </div>
                </div>
            <?php endif; ?>

            <!-- By Genre -->
            <?php if (!empty($genres)): ?>
                <div>
                    <div class="eyebrow text-cream-3 mb-3">GENRE</div>
                    <div class="flex flex-wrap gap-2">
                        <a href="<?= buildClearUrl() ?>" class="filter-chip <?= !$selectedGenre ? 'active' : '' ?>">All Genres</a>
                        <?php foreach ($genres as $genre): ?>
                            <a href="<?= buildFilterUrl('genre', $genre) ?>"
                                class="filter-chip <?= $selectedGenre === $genre ? 'active' : '' ?>">
                                <?= htmlspecialchars($genre) ?>
                            </a>
                        <?php endforeach; ?>
                    </div>
                </div>
            <?php endif; ?>

            <!-- By Year -->
            <?php if (!empty($years)): ?>
                <div>
                    <div class="eyebrow text-cream-3 mb-3">YEAR</div>
                    <div class="flex flex-wrap gap-4 text-muted">
                        <a href="<?= buildClearUrl() ?>" class="filter-chip <?= !$selectedYear ? 'active' : '' ?>">All Years</a>
                        <?php foreach ($years as $year): ?>
                            <a href="<?= buildFilterUrl('year', $year) ?>"
                                class="filter-chip <?= $selectedYear == $year ? 'active' : '' ?>">
                                <?= $year ?>
                            </a>
                        <?php endforeach; ?>
                    </div>
                </div>
            <?php endif; ?>
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
                                    <?php if ($track['featured']): ?>
                                        <span class="eyebrow text-gold text-xs px-2 py-1 bg-gold/10 rounded">Featured</span>
                                    <?php endif; ?>
                                </div>

                                <!-- Metadata -->
                                <div class="flex flex-wrap gap-2 mt-3 pt-3 border-t border-[#3D3935]">
                                    <?php if ($track['user_role']): ?>
                                        <span class="inline-block eyebrow text-gold text-xs">
                                            <?= htmlspecialchars($track['user_role']) ?>
                                        </span>
                                    <?php endif; ?>
                                </div>

                                <?php if (!empty($track['description'])): ?>
                                    <p class="text-cream-3 text-xs mt-3 leading-relaxed">
                                        <?= htmlspecialchars($track['description']) ?>
                                    </p>
                                <?php endif; ?>
                            </div>

                            <!-- Spotify Embed -->
                            <?php if (!empty($track['spotify_url'])): ?>
                                <iframe
                                    style="border-radius: 6px"
                                    src="<?= htmlspecialchars($track['spotify_url']) ?>"
                                    width="100%"
                                    height="152"
                                    frameborder="0"
                                    allow="autoplay; clipboard-write; encrypted-media; fullscreen; picture-in-picture"
                                    loading="lazy">
                                </iframe>
                            <?php endif; ?>
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
            <a href="<?= buildClearUrl() ?>" class="btn-ghost font-semibold">Reset filters →</a>
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
                <div class="eyebrow text-gold mb-2">Years</div>
                <div class="display text-3xl text-cream"><?= count(array_unique(array_column($filteredTracks, 'release_year'))) ?></div>
            </div>
        </div>
    </section>
<?php endif; ?>

<?php require_once INCLUDES . '/footer.php'; ?>