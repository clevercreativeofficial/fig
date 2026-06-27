<?php
require_once __DIR__ . '/../path.php';
require_once ADMIN_PATH . '/includes/header.php';

?>

<!-- ─── OVERVIEW ─── -->
<section id="overview" class="section active">

    <!-- Stat cards -->
    <div class="grid grid-cols-2 lg:grid-cols-4 gap-4 mb-10">
        <div class="card p-5">
            <div class="eyebrow text-cream-3 mb-3">New inquiries</div>
            <div class="display text-5xl text-gold" id="statNewInq">0</div>
            <div class="text-xs text-cream-3 mt-2">awaiting reply</div>
        </div>
        <div class="card p-5">
            <div class="eyebrow text-cream-3 mb-3">Tracks live</div>
            <div class="display text-5xl text-cream" id="statTracksLive">0</div>
            <div class="text-xs text-cream-3 mt-2">on portfolio</div>
        </div>
        <div class="card p-5">
            <div class="eyebrow text-cream-3 mb-3">Booked this Q</div>
            <div class="display text-5xl text-cream" id="statBooked">0</div>
            <div class="text-xs text-cream-3 mt-2">confirmed sessions</div>
        </div>
        <div class="card p-5">
            <div class="eyebrow text-cream-3 mb-3">Avg reply time</div>
            <div class="display text-5xl text-cream">14<span class="text-2xl text-cream-3">h</span></div>
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
            <div id="recentInquiriesList"></div>
        </div>

        <!-- Availability widget -->
        <div class="card">
            <div class="px-5 py-4 border-b border-ink-3">
                <h3 class="display text-xl">Availability</h3>
            </div>
            <div class="p-5 space-y-5">
                <div class="flex items-center justify-between">
                    <span class="text-sm">Accepting bookings</span>
                    <div id="availToggleMini" class="toggle"></div>
                </div>
                <div>
                    <div class="eyebrow text-cream-3 mb-2">Period</div>
                    <div id="availPeriodMini" class="display text-2xl">—</div>
                </div>
                <div>
                    <div class="eyebrow text-cream-3 mb-2">Slots remaining</div>
                    <div id="availSlotsMini" class="display text-4xl text-gold">—</div>
                </div>
                <a href="#availability" class="btn-secondary w-full justify-center mt-4" style="display:flex">Edit availability →</a>
            </div>
        </div>
    </div>
</section>

<?php require_once ADMIN_PATH . '/includes/footer.php'; ?>