<?php
require_once __DIR__ . '/path.php';
require_once CONFIG . '/db.php';
require_once INCLUDES . '/header.php';

// ============================================================
// DATABASE CONNECTION CHECK
// ============================================================
if (!isset($conn)) {
    die("Database connection failed: " . mysqli_connect_error());
}

// ============================================================
// FETCH FEATURED TRACKS
// ============================================================
$featured_sql = "SELECT * FROM tracks WHERE featured = 1 AND live_on_site = 1 ORDER BY release_year DESC";
$featured_result = mysqli_query($conn, $featured_sql);

if (!$featured_result) {
    die("Featured tracks query failed: " . mysqli_error($conn));
}

$featured_tracks = [];
while ($featured_row = mysqli_fetch_assoc($featured_result)) {
    $featured_tracks[] = $featured_row;
}

// ============================================================
// FETCH ALL OTHER TRACKS (Non-Featured)
// ============================================================
$sql = "SELECT * FROM tracks WHERE featured = 0 AND live_on_site = 1 ORDER BY release_year DESC LIMIT 4";
$result = mysqli_query($conn, $sql);

if (!$result) {
    die("All tracks query failed: " . mysqli_error($conn));
}

$allTracks = [];
while ($row = mysqli_fetch_assoc($result)) {
    $allTracks[] = $row;
}

// ============================================================
// LAYOUT CALCULATIONS
// ============================================================
$hasFeatured  = count($featured_tracks) > 0;
$hasTrack0    = isset($allTracks[0]);
$hasTrack1    = isset($allTracks[1]);
$hasTrack2    = isset($allTracks[2]);
$hasTrack3    = isset($allTracks[3]);
$topCount     = ($hasTrack0 ? 1 : 0) + ($hasTrack1 ? 1 : 0);
$bottomCount  = ($hasTrack2 ? 1 : 0) + ($hasTrack3 ? 1 : 0);

// Top secondary block column span
$topBlockSpan = $hasFeatured ? 'lg:col-span-5' : 'col-span-12';
$topInnerCols = (!$hasFeatured && $topCount === 2) ? 'md:grid-cols-2' : 'grid-cols-1';
?>

<!-- ─────────── HERO ─────────── -->
<section id="top"
    class="relative min-h-screen px-6 lg:px-12 pt-32 lg:pt-28 pb-12 flex flex-col justify-between overflow-hidden">

    <!-- Main grid: headline + portrait -->
    <div class="rise grid grid-cols-1 lg:grid-cols-12 gap-10 lg:gap-12 items-center my-16 lg:my-12">

        <!-- Headline (left, 7 cols) -->
        <div class="lg:col-span-7 relative z-10">
            <h1 class="display text-6xl md:text-7xl lg:text-8xl text-cream mb-8 leading-tight">
                Where afrobeats <br />
                meets <span class="italic-em">the sublime.</span>
            </h1>

            <!-- Bottom row -->
            <div class="rise flex flex-col gap-8">
                <p class="max-w-md text-cream-2 text-base leading-relaxed">
                    Selective production, mixing and mastering for artists who care about how the record actually
                    feels at 2
                    a.m. Currently scheduling sessions for Q2 — June through August 2026.
                </p>
                <a href="#booking" class="btn-primary max-w-fit self-start lg:self-auto">
                    Book a session
                    <svg width="14" height="14" viewBox="0 0 14 14" fill="none">
                        <path d="M1 7H13M13 7L7 1M13 7L7 13" stroke="currentColor" stroke-width="1.5" />
                    </svg>
                </a>
            </div>
        </div>

        <!-- Portrait (right, 5 cols) -->
        <figure class="lg:col-span-5 relative group">
            <!-- Editorial corner label -->
            <div class="absolute -top-4 left-0 eyebrow text-gold z-10 bg-ink px-2">
                01 / Portrait
            </div>

            <div class="relative overflow-hidden border border-ink-3 group">
                <div class="relative">
                    <img src="assets/images/img_hands.png" alt="FIG Pro in studio, Bujumbura"
                        class="w-full aspect-[3/4] object-cover transition-all duration-1000 group-hover:scale-[1.03]"
                        style="filter: grayscale(15%) contrast(1.05) saturate(0.9) brightness(0.95);" />
                    <img src="assets/images/img_portrait.png" alt="FIG Pro in studio, Bujumbura"
                        class="absolute inset-0 w-full aspect-[3/4] object-cover transition-all duration-1000 group-hover:scale-[1.03] group-hover:opacity-0"
                        style="filter: grayscale(15%) contrast(1.05) saturate(0.9) brightness(0.95);" />
                </div>
                <!-- Warm overlay — keeps photo in the same color world as the page -->
                <div class="absolute inset-0 pointer-events-none" style="background: linear-gradient(180deg, transparent 60%, rgba(11,9,8,0.4) 100%);
                            mix-blend-mode: multiply;"></div>
            </div>

            <!-- Caption -->
            <figcaption class="mt-3 flex items-baseline justify-between eyebrow text-cream-3">
                <span>FIG Pro · Master Muzik</span>
                <span>Buja · 2026</span>
            </figcaption>
        </figure>
    </div>
</section>

<!-- ─────────── MARQUEE / CREDITS ─────────── -->
<section class="border-y border-ink-3 py-6 marquee-mask overflow-hidden">
    <div class="marquee display text-3xl lg:text-4xl text-cream-2 whitespace-nowrap">
        <span>Selected collaborations</span><span class="text-gold">&middot;</span>
        <span>Master Muzik</span><span class="text-gold">&middot;</span>
        <span>Sat B</span><span class="text-gold">&middot;</span>
        <span>Big Fizzo</span><span class="text-gold">&middot;</span>
        <span>Masterland</span><span class="text-gold">&middot;</span>
        <span>Chany Queen</span><span class="text-gold">&middot;</span>
        <span>Vania Ice</span><span class="text-gold">&middot;</span>
        <!-- Duplicate for seamless loop -->
        <span>Selected collaborations</span><span class="text-gold">&middot;</span>
        <span>Master Muzik</span><span class="text-gold">&middot;</span>
        <span>Sat B</span><span class="text-gold">&middot;</span>
        <span>Big Fizzo</span><span class="text-gold">&middot;</span>
        <span>Masterland</span><span class="text-gold">&middot;</span>
        <span>Chany Queen</span><span class="text-gold">&middot;</span>
        <span>Vania Ice</span><span class="text-gold">&middot;</span>
    </div>
</section>

<!-- ─────────── ABOUT ─────────── -->
<section id="about" class="px-6 lg:px-12 py-32 lg:py-48">
    <div class="grid grid-cols-12 gap-6">
        <div class="col-span-12 lg:col-span-2 reveal">
            <div class="eyebrow text-gold">01 / Studio Notes</div>
        </div>
        <div class="col-span-12 lg:col-span-10">
            <h2 class="display text-6xl md:text-7xl lg:text-8xl text-cream reveal">
                Records aren't <span class="italic-em">made</span>—<br />
                they're <span class="italic-em">arranged</span> into existence.
            </h2>
            <div class="mt-16 grid grid-cols-1 md:grid-cols-2 gap-12 lg:gap-24">
                <div class="reveal">
                    <p class="text-cream-2 leading-relaxed text-lg">
                        I produce, mix and master from Burundi, creating records that blend East African soul with contemporary global sound. Every beat, melody and moment is crafted with intention—because great music isn't about doing more, it's about making every choice count.
                    </p>
                </div>
                <div class="reveal">
                    <p class="text-cream-2 leading-relaxed text-lg">
                        My work spans Afro Fusion, Afrobeats, R&B, Hip-Hop, Gospel and everything in between. Whether you're an emerging artist or an established voice, I focus on bringing your vision to life with clarity, emotion and a sound that feels timeless. Every project starts with one question: what should people feel when they hear this?
                    </p>
                </div>
            </div>

            <!-- Stats row -->
            <div class="mt-20 grid grid-cols-2 lg:grid-cols-4 gap-8 reveal hidden">
                <div class="border-t border-ink-3 pt-6">
                    <div class="display text-5xl text-gold">62M+</div>
                    <div class="eyebrow mt-2">Streams produced</div>
                </div>
                <div class="border-t border-ink-3 pt-6">
                    <div class="display text-5xl text-gold">3×</div>
                    <div class="eyebrow mt-2">Billboard Afrobeats</div>
                </div>
                <div class="border-t border-ink-3 pt-6">
                    <div class="display text-5xl text-gold">14</div>
                    <div class="eyebrow mt-2">Sync placements</div>
                </div>
                <div class="border-t border-ink-3 pt-6">
                    <div class="display text-5xl text-gold">06</div>
                    <div class="eyebrow mt-2">Years in studio</div>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- ─────────── SERVICES ─────────── -->
<section id="services" class="px-6 lg:px-12 py-32 lg:py-48">
    <div class="grid grid-cols-12 gap-6 mb-20">
        <div class="col-span-12 lg:col-span-2 reveal">
            <div class="eyebrow text-gold">03 / Services</div>
        </div>
        <div class="col-span-12 lg:col-span-10 reveal">
            <h2 class="display text-6xl md:text-7xl lg:text-8xl text-cream">
                Three ways <span class="italic-em">to work together.</span>
            </h2>
        </div>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-3 border-t border-ink-3">
        <!-- Production -->
        <div class="service-card border-b lg:border-b-0 lg:border-r border-ink-3 p-8 lg:p-10 reveal">
            <div class="flex items-start justify-between mb-12">
                <div class="display text-3xl">Production</div>
                <div class="eyebrow text-gold">A</div>
            </div>
            <div class="display text-6xl mb-2">$500<span class="text-cream-3 text-2xl">/track</span></div>
            <div class="eyebrow text-cream-3">Starting · negotiated per project</div>

            <ul class="mt-12 space-y-4 text-cream-2 text-sm">
                <li class="flex gap-3"><span class="text-gold">→</span> Full beat &amp; arrangement</li>
                <li class="flex gap-3"><span class="text-gold">→</span> Vocal direction sessions</li>
                <li class="flex gap-3"><span class="text-gold">→</span> Stems &amp; project files delivered</li>
                <li class="flex gap-3"><span class="text-gold">→</span> Two rounds of revision</li>
                <li class="flex gap-3"><span class="text-gold">→</span> 3–5 week turnaround</li>
            </ul>

            <a href="#booking" class="btn-ghost mt-12">Enquire →</a>
        </div>

        <!-- Mixing -->
        <div class="service-card border-b lg:border-b-0 lg:border-r border-ink-3 p-8 lg:p-10 reveal"
            style="background: #13100E;">
            <div class="flex items-start justify-between mb-12">
                <div class="display text-3xl">Mixing</div>
                <div class="eyebrow text-gold">B · Most booked</div>
            </div>
            <div class="display text-6xl mb-2">$250<span class="text-cream-3 text-2xl">/track</span></div>
            <div class="eyebrow text-cream-3">Up to 60 stems</div>

            <ul class="mt-12 space-y-4 text-cream-2 text-sm">
                <li class="flex gap-3"><span class="text-gold">→</span> Hybrid analog/ITB chain</li>
                <li class="flex gap-3"><span class="text-gold">→</span> Vocal tuning &amp; comping</li>
                <li class="flex gap-3"><span class="text-gold">→</span> Atmos / immersive on request</li>
                <li class="flex gap-3"><span class="text-gold">→</span> Three rounds of revision</li>
                <li class="flex gap-3"><span class="text-gold">→</span> 7–10 day turnaround</li>
            </ul>

            <a href="#booking" class="btn-ghost mt-12">Enquire →</a>
        </div>

        <!-- Mastering -->
        <div class="service-card p-8 lg:p-10 reveal">
            <div class="flex items-start justify-between mb-12">
                <div class="display text-3xl">Mastering</div>
                <div class="eyebrow text-gold">C</div>
            </div>
            <div class="display text-6xl mb-2">$100<span class="text-cream-3 text-2xl">/track</span></div>
            <div class="eyebrow text-cream-3">Streaming + DDP for vinyl</div>

            <ul class="mt-12 space-y-4 text-cream-2 text-sm">
                <li class="flex gap-3"><span class="text-gold">→</span> Streaming, club &amp; vinyl masters</li>
                <li class="flex gap-3"><span class="text-gold">→</span> Loudness-target compliant</li>
                <li class="flex gap-3"><span class="text-gold">→</span> Stem mastering available</li>
                <li class="flex gap-3"><span class="text-gold">→</span> Two rounds of revision</li>
                <li class="flex gap-3"><span class="text-gold">→</span> 48-hour turnaround</li>
            </ul>

            <a href="#booking" class="btn-ghost mt-12">Enquire →</a>
        </div>
    </div>

    <p class="mt-12 text-cream-3 text-sm reveal">
        Album bundles, retainer arrangements and label deals quoted separately. EP/LP discounts after the first three tracks.
    </p>
</section>

<!-- ─────────── WORK ─────────── -->
<section id="work" class="px-6 lg:px-12 py-32 lg:py-48 bg-ink-2/30">
    <div class="grid grid-cols-12 gap-6 mb-20">
        <div class="col-span-12 lg:col-span-2 reveal">
            <div class="eyebrow text-gold">02 / Selected Work</div>
        </div>
        <div class="col-span-12 lg:col-span-10 reveal">
            <h2 class="display text-6xl md:text-7xl lg:text-8xl text-cream">
                Selected <span class="italic-em">Productions.</span>
            </h2>
            <p class="mt-6 text-cream-2 max-w-2xl">
                Every record tells a different story. From Afro Fusion and Hip-Hop to R&B, Gospel and contemporary African sounds, these projects reflect my approach to production—thoughtful arrangements, clean mixes and music built to connect with people.
            </p>
        </div>
    </div>

    <div class="grid grid-cols-12 gap-6">

        <!-- ============================================================ -->
        <!-- FEATURED TRACK -->
        <!-- ============================================================ -->
        <?php if ($hasFeatured): ?>
            <div class="col-span-12 lg:col-span-7 reveal embed-card p-6 lg:p-8">
                <div class="flex items-baseline justify-between mb-4">
                    <div>
                        <div class="eyebrow text-gold">Featured</div>
                        <div class="display text-3xl mt-1"><?= htmlspecialchars($featured_tracks[0]['title']) ?></div>
                        <div class="text-cream-3 text-sm mt-1">
                            <?= htmlspecialchars($featured_tracks[0]['artist']) ?> ·
                            <?= htmlspecialchars($featured_tracks[0]['role'] ?? 'Production & Mix') ?>
                        </div>
                    </div>
                    <div class="eyebrow hidden md:block"><?= htmlspecialchars($featured_tracks[0]['release_year']) ?></div>
                </div>
                <iframe style="border-radius:6px"
                    src="<?= htmlspecialchars($featured_tracks[0]['spotify_url']) ?>"
                    width="100%" height="432" frameborder="0"
                    allow="autoplay; clipboard-write; encrypted-media; fullscreen; picture-in-picture"
                    loading="lazy"></iframe>
            </div>
        <?php endif; ?>

        <!-- ============================================================ -->
        <!-- TOP SECONDARY CARDS (TRACKS 0 & 1) -->
        <!-- ============================================================ -->
        <?php if ($topCount > 0): ?>
            <div class="col-span-12 <?= $topBlockSpan ?> grid <?= $topInnerCols ?> gap-6">

                <?php if ($hasTrack0): ?>
                    <div class="reveal embed-card p-6">
                        <div class="flex items-baseline justify-between mb-4">
                            <div>
                                <div class="display text-2xl"><?= htmlspecialchars($allTracks[0]['title']) ?></div>
                                <div class="text-cream-3 text-xs mt-1">
                                    <?= htmlspecialchars($allTracks[0]['artist']) ?> ·
                                    <?= htmlspecialchars($allTracks[0]['role'] ?? 'Mixing') ?>
                                </div>
                            </div>
                            <div class="eyebrow hidden md:block"><?= htmlspecialchars($allTracks[0]['release_year']) ?></div>
                        </div>
                        <iframe style="border-radius:6px"
                            src="<?= htmlspecialchars($allTracks[0]['spotify_url']) ?>"
                            width="100%" height="152" frameborder="0"
                            allow="autoplay; clipboard-write; encrypted-media; fullscreen; picture-in-picture"
                            loading="lazy"></iframe>
                    </div>
                <?php endif; ?>

                <?php if ($hasTrack1): ?>
                    <div class="reveal embed-card p-6">
                        <div class="flex items-baseline justify-between mb-4">
                            <div>
                                <div class="display text-2xl"><?= htmlspecialchars($allTracks[1]['title']) ?></div>
                                <div class="text-cream-3 text-xs mt-1">
                                    <?= htmlspecialchars($allTracks[1]['artist']) ?> ·
                                    <?= htmlspecialchars($allTracks[1]['role'] ?? 'Production') ?>
                                </div>
                            </div>
                            <div class="eyebrow hidden md:block"><?= htmlspecialchars($allTracks[1]['release_year']) ?></div>
                        </div>
                        <iframe style="border-radius:6px"
                            src="<?= htmlspecialchars($allTracks[1]['spotify_url']) ?>"
                            width="100%" height="152" frameborder="0"
                            allow="autoplay; clipboard-write; encrypted-media; fullscreen; picture-in-picture"
                            loading="lazy"></iframe>
                    </div>
                <?php endif; ?>

            </div>
        <?php endif; ?>

        <!-- ============================================================ -->
        <!-- BOTTOM ROW (TRACKS 2 & 3) -->
        <!-- ============================================================ -->
        <?php if ($hasTrack2): ?>
            <div class="col-span-12 <?= $bottomCount === 2 ? 'lg:col-span-6' : '' ?> reveal embed-card p-6">
                <div class="flex items-baseline justify-between mb-4">
                    <div>
                        <div class="display text-2xl"><?= htmlspecialchars($allTracks[2]['title']) ?></div>
                        <div class="text-cream-3 text-xs mt-1">
                            <?= htmlspecialchars($allTracks[2]['artist']) ?> ·
                            <?= htmlspecialchars($allTracks[2]['role'] ?? 'Co-Production') ?>
                        </div>
                    </div>
                    <div class="eyebrow hidden md:block"><?= htmlspecialchars($allTracks[2]['release_year']) ?></div>
                </div>
                <iframe style="border-radius:6px"
                    src="<?= htmlspecialchars($allTracks[2]['spotify_url']) ?>"
                    width="100%" height="152" frameborder="0"
                    allow="autoplay; clipboard-write; encrypted-media; fullscreen; picture-in-picture"
                    loading="lazy"></iframe>
            </div>
        <?php endif; ?>

        <?php if ($hasTrack3): ?>
            <div class="col-span-12 <?= $bottomCount === 2 ? 'lg:col-span-6' : '' ?> reveal embed-card p-6">
                <div class="flex items-baseline justify-between mb-4">
                    <div>
                        <div class="display text-2xl"><?= htmlspecialchars($allTracks[3]['title']) ?></div>
                        <div class="text-cream-3 text-xs mt-1">
                            <?= htmlspecialchars($allTracks[3]['artist']) ?> ·
                            <?= htmlspecialchars($allTracks[3]['role'] ?? 'Mix Engineering') ?>
                        </div>
                    </div>
                    <div class="eyebrow hidden md:block"><?= htmlspecialchars($allTracks[3]['release_year']) ?></div>
                </div>
                <iframe style="border-radius:6px"
                    src="<?= htmlspecialchars($allTracks[3]['spotify_url']) ?>"
                    width="100%" height="152" frameborder="0"
                    allow="autoplay; clipboard-write; encrypted-media; fullscreen; picture-in-picture"
                    loading="lazy"></iframe>
            </div>
        <?php endif; ?>

    </div>

    <div class="mt-16 reveal">
        <a href="<?= htmlspecialchars(APP_URL) ?>discography" class="btn-ghost">View full discography →</a>
    </div>
</section>

<span class="italic">Catalog.</span>

<!-- ─────────── BOOKING ─────────── -->
<section id="booking" class="px-6 lg:px-12 py-32 lg:py-48 bg-ink-2/30">
    <div class="grid grid-cols-12 gap-6">
        <div class="col-span-12 lg:col-span-2 reveal">
            <div class="eyebrow text-gold">04 / Booking</div>
        </div>

        <div class="col-span-12 lg:col-span-6 reveal">
            <h2 class="display text-6xl md:text-7xl lg:text-8xl text-cream mb-4">
                Tell me about <br /><span class="italic-em">the record.</span>
            </h2>
            <p class="text-cream-2 text-lg max-w-lg mb-12">
                The more you can share — references, mood, deadline — the faster I can tell you whether we're a fit.
                I read every enquiry myself.
            </p>

            <form id="bookingForm" class="space-y-2">
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <input type="text" name="name" required placeholder="Your name" class="field" />
                    <input type="email" name="email" required placeholder="Email" class="field" />
                </div>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <input type="text" name="artist" placeholder="Artist / project name" class="field" />
                    <select name="service" required class="field bg-ink">
                        <option value="" disabled selected>Service needed</option>
                        <option>Production</option>
                        <option>Mixing</option>
                        <option>Mastering</option>
                        <option>Album / EP bundle</option>
                        <option>Other / not sure yet</option>
                    </select>
                </div>
                <input type="text" name="timeline" placeholder="Timeline or deadline" class="field" />
                <textarea name="message" required
                    placeholder="Tell me about the record — references, mood, what you're trying to make people feel."
                    class="field"></textarea>

                <div class="pt-8 flex flex-col md:flex-row md:items-center gap-6">
                    <button type="submit" id="submitBtn" class="btn-primary">
                        Send enquiry
                        <svg width="14" height="14" viewBox="0 0 14 14" fill="none">
                            <path d="M1 7H13M13 7L7 1M13 7L7 13" stroke="currentColor" stroke-width="1.5" />
                        </svg>
                    </button>
                    <p class="eyebrow text-cream-3">Typical reply · within 48 hours</p>
                </div>
                <div id="formMessages">
                    <p id="formMsg" class="text-gold mt-6 hidden">
                        Received. I'll be in touch within two business days. — A.
                    </p>
                    <p id="formError" class="text-red-400 mt-6 hidden">
                        Something went wrong. Please try again or email hello@figpro.com
                    </p>
                </div>
            </form>
        </div>

        <!-- Sidebar -->
        <div class="col-span-12 lg:col-span-3 lg:col-start-10 reveal">
            <div class="border-l border-ink-3 pl-8 space-y-12">
                <div>
                    <div class="eyebrow text-gold mb-3">Direct lines</div>
                    <a href="mailto:hello@figpro.com"
                        class="text-cream-2 leading-relaxed hover:text-gold transition-colors">hello@figpro.com</a>
                    <br>
                    <a href="wa.me/25769979642"
                        class="text-cream-2 leading-relaxed hover:text-gold transition-colors">
                        +257 69 979 642
                    </a>
                </div>
                <div>
                    <div class="eyebrow text-gold mb-3">Studios</div>
                    <p class="text-cream-2 leading-relaxed">
                        Kigobe, Av. Des Etats-Unis<br />
                        Bujumbura
                    </p>
                </div>
                <div>
                    <div class="eyebrow text-gold mb-3">Currently</div>
                    <p class="text-cream-2 leading-relaxed">
                        <span class="live-dot inline-block w-1.5 h-1.5 rounded-full bg-gold mr-2"></span>
                        Booking June – August 2026.<br />
                        Sessions Available.
                    </p>
                </div>
            </div>
        </div>
    </div>
</section>

<script>
    const bookingForm = document.getElementById('bookingForm');
    bookingForm.addEventListener('submit', async (e) => {
        e.preventDefault();

        const submitBtn = document.getElementById('submitBtn');
        const formMsg = document.getElementById('formMsg');
        const formError = document.getElementById('formError');

        submitBtn.disabled = true;
        submitBtn.textContent = 'Sending...';

        try {
            const formData = new FormData(bookingForm);
            console.log('Form data:', formData);

            const response = await fetch('<?= APP_URL ?>api/booking.php', {
                method: 'POST',
                body: formData
            });

            const result = await response.json();
            console.log('Response:', result);

            if (result.success) {
                formError.classList.add('hidden');
                formMsg.classList.remove('hidden');
                bookingForm.reset();
            } else {
                formMsg.classList.add('hidden');
                formError.classList.remove('hidden');
            }
        } catch (error) {
            console.error('Error:', error);
            formMsg.classList.add('hidden');
            formError.classList.remove('hidden');
        } finally {
            submitBtn.disabled = false;
            submitBtn.innerHTML = `
            Send enquiry
            <svg width="14" height="14" viewBox="0 0 14 14" fill="none">
                <path d="M1 7H13M13 7L7 1M13 7L7 13" stroke="currentColor" stroke-width="1.5" />
            </svg>
        `;
        }
    });
</script>
<?php require_once INCLUDES . '/footer.php'; ?>