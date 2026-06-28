<?php
require_once __DIR__ . '/../path.php';
require_once CONFIG . '/db.php';
require_once ADMIN_PATH . '/includes/header.php';

// ============================================================
// VERIFY DATABASE CONNECTION
// ============================================================
if (!isset($conn)) {
    die("Database connection failed: " . mysqli_connect_error());
}

// ============================================================
// FETCH DASHBOARD DATA
// ============================================================

// 1. NEW INQUIRIES (awaiting reply)
$new_inq_sql = "
    SELECT COUNT(*) as count 
    FROM bookings 
    WHERE status = 'pending' 
    AND created_at >= DATE_SUB(NOW(), INTERVAL 30 DAY)
";
$new_inq_result = mysqli_query($conn, $new_inq_sql);
$new_inq_count = mysqli_fetch_assoc($new_inq_result)['count'] ?? 0;

// 2. TRACKS LIVE
$tracks_live_sql = "
    SELECT COUNT(*) as count 
    FROM tracks 
    WHERE live_on_site = 1
";
$tracks_live_result = mysqli_query($conn, $tracks_live_sql);
$tracks_live_count = mysqli_fetch_assoc($tracks_live_result)['count'] ?? 0;

// 3. BOOKED THIS QUARTER (confirmed bookings in current Q)
$quarter_start = date('Y-m-01', strtotime('first day of this quarter'));
$booked_sql = "
    SELECT COUNT(*) as count 
    FROM bookings 
    WHERE status = 'confirmed' 
    AND booking_date >= '$quarter_start'
";
$booked_result = mysqli_query($conn, $booked_sql);
$booked_count = mysqli_fetch_assoc($booked_result)['count'] ?? 0;

// 4. AVERAGE REPLY TIME (last 30 days)
$reply_time_sql = "
    SELECT AVG(HOUR(TIMEDIFF(replied_at, created_at))) as avg_hours
    FROM bookings 
    WHERE replied_at IS NOT NULL 
    AND created_at >= DATE_SUB(NOW(), INTERVAL 30 DAY)
";
$reply_time_result = mysqli_query($conn, $reply_time_sql);
$reply_time_row = mysqli_fetch_assoc($reply_time_result);
$avg_reply_hours = round($reply_time_row['avg_hours'] ?? 0);

// 5. RECENT INQUIRIES (last 5)
$recent_inq_sql = "
    SELECT id, name, artist, service, created_at, status
    FROM bookings 
    ORDER BY created_at DESC 
    LIMIT 5
";
$recent_inq_result = mysqli_query($conn, $recent_inq_sql);
$recent_inquiries = [];
while ($row = mysqli_fetch_assoc($recent_inq_result)) {
    $recent_inquiries[] = $row;
}

// 6. AVAILABILITY STATUS
$avail_sql = "
    SELECT is_accepting, available_from, available_to, max_slots, booked_slots
    FROM availability 
    ORDER BY created_at DESC 
    LIMIT 1
";
$avail_result = mysqli_query($conn, $avail_sql);
$availability = mysqli_fetch_assoc($avail_result);

$is_accepting = $availability['is_accepting'] ?? 0;
$available_from = $availability['available_from'] ?? null;
$available_to = $availability['available_to'] ?? null;
$slots_remaining = ($availability['max_slots'] ?? 0) - ($availability['booked_slots'] ?? 0);

// Format availability period
if ($available_from && $available_to) {
    $period_display = date('M d', strtotime($available_from)) . ' – ' . date('M d', strtotime($available_to));
} else {
    $period_display = 'Not set';
}
?>

<!-- ─── OVERVIEW ─── -->
<section id="overview" class="section active">
    <!-- Stat cards -->
    <div class="grid grid-cols-2 lg:grid-cols-4 gap-4 mb-10">
        <div class="card p-5">
            <div class="eyebrow text-cream-3 mb-3">New inquiries</div>
            <div class="display text-5xl text-gold" id="statNewInq"><?= $new_inq_count ?></div>
            <div class="text-xs text-cream-3 mt-2">awaiting reply</div>
        </div>
        <div class="card p-5">
            <div class="eyebrow text-cream-3 mb-3">Tracks live</div>
            <div class="display text-5xl text-cream" id="statTracksLive"><?= $tracks_live_count ?></div>
            <div class="text-xs text-cream-3 mt-2">on portfolio</div>
        </div>
        <div class="card p-5">
            <div class="eyebrow text-cream-3 mb-3">Booked this Q</div>
            <div class="display text-5xl text-cream" id="statBooked"><?= $booked_count ?></div>
            <div class="text-xs text-cream-3 mt-2">confirmed sessions</div>
        </div>
        <div class="card p-5">
            <div class="eyebrow text-cream-3 mb-3">Avg reply time</div>
            <div class="display text-5xl text-cream"><?= $avg_reply_hours ?><span class="text-2xl text-cream-3">h</span></div>
            <div class="text-xs text-cream-3 mt-2">last 30 days</div>
        </div>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        <!-- Recent inquiries -->
        <div class="card lg:col-span-2">
            <div class="px-5 py-4 border-b border-ink-3 flex items-center justify-between">
                <h3 class="display text-xl">Recent inquiries</h3>
                <a href="#inquiries" class="eyebrow text-gold hover:text-cream transition-colors">View all →</a>
            </div>
            <div id="recentInquiriesList">
                <?php if (empty($recent_inquiries)): ?>
                    <div class="px-5 py-8 text-center text-cream-3">
                        <p>No inquiries yet</p>
                    </div>
                <?php else: ?>
                    <div class="divide-y divide-ink-3">
                        <?php foreach ($recent_inquiries as $inquiry): ?>
                            <div class="px-5 py-4 hover:bg-ink-3/20 transition-colors">
                                <div class="flex items-start justify-between mb-2">
                                    <div>
                                        <div class="font-medium text-cream"><?= htmlspecialchars($inquiry['name']) ?></div>
                                        <div class="text-xs text-cream-3 mt-1"><?= htmlspecialchars($inquiry['artist'] ?? 'No artist') ?></div>
                                    </div>
                                    <span class="eyebrow text-xs px-2 py-1 rounded" style="background: <?= $inquiry['status'] === 'confirmed' ? '#10b981' : ($inquiry['status'] === 'pending' ? '#f59e0b' : '#6b7280') ?>">
                                        <?= ucfirst($inquiry['status']) ?>
                                    </span>
                                </div>
                                <div class="flex items-center justify-between">
                                    <div class="text-xs text-cream-2"><?= htmlspecialchars($inquiry['service']) ?></div>
                                    <div class="text-xs text-cream-3"><?= date('M d, H:i', strtotime($inquiry['created_at'])) ?></div>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    </div>
                <?php endif; ?>
            </div>
        </div>

        <!-- Availability widget -->
        <div class="card">
            <div class="px-5 py-4 border-b border-ink-3">
                <h3 class="display text-xl">Availability</h3>
            </div>
            <div class="p-5 space-y-5">
                <div class="flex items-center justify-between">
                    <span class="text-sm">Accepting bookings</span>
                    <div class="toggle <?= $is_accepting ? 'active' : '' ?>" style="pointer-events: none;">
                        <div class="toggle-switch"></div>
                    </div>
                </div>
                <div>
                    <div class="eyebrow text-cream-3 mb-2">Period</div>
                    <div id="availPeriodMini" class="display text-lg"><?= htmlspecialchars($period_display) ?></div>
                </div>
                <div>
                    <div class="eyebrow text-cream-3 mb-2">Slots remaining</div>
                    <div id="availSlotsMini" class="display text-4xl text-gold"><?= $slots_remaining >= 0 ? $slots_remaining : 0 ?></div>
                </div>
                <a href="#availability" class="btn-secondary w-full justify-center mt-4" style="display:flex">Edit availability →</a>
            </div>
        </div>
    </div>
</section>


<?php require_once ADMIN_PATH . '/includes/footer.php'; ?>