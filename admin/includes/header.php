<?php
require_once __DIR__ . '/../../path.php';
require_once CONFIG . '/auth.php';
require_once CONFIG . '/db.php';

/**
 * Admin header.
 * Expects these variables to be set before including:
 *   $page_title  string  e.g. 'Inquiries'  (shown in <title> and topbar)
 *   $page_route  string  e.g. 'inquiries'  (used to highlight active sidebar link)
 *
 * Expects $conn to be available (from config/db.php) — used for the inquiry-count badge.
 */

$page_title = $page_title ?? 'Admin';
$page_route = $page_route ?? '';

// ============================================================
// SIDEBAR BADGE: COUNT PENDING INQUIRIES
// ============================================================
$new_inq_count = 0;
if (isset($conn) && $conn) {
    $badge_query = "SELECT COUNT(*) AS c FROM bookings WHERE status = 'pending'";
    if ($result = @mysqli_query($conn, $badge_query)) {
        $row = mysqli_fetch_assoc($result);
        $new_inq_count = (int)$row['c'];
    }
}

$user_email = $_SESSION['user_email'] ?? '';

// Tiny helper: returns 'active' if route matches current page
function admin_link_class($route = '')
{
    global $page_route;
    return 'sidebar-link' . ($route === $page_route ? ' active' : '');
}

$current_url = (isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on' ? "https" : "http") . "://$_SERVER[HTTP_HOST]$_SERVER[REQUEST_URI]";
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= htmlspecialchars($page_title) ?> · <?= htmlspecialchars(APP_NAME) ?> Admin</title>
    <meta name="robots" content="noindex, nofollow">

    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Instrument+Serif:ital@0;1&family=Geist:wght@300;400;500;600&family=JetBrains+Mono:wght@400;500&display=swap" rel="stylesheet">

    <link rel="icon" type="image/x-icon" href="<?= APP_URL ?>assets/favicon.ico" />
    <link rel="stylesheet" href="<?= APP_URL ?>admin/assets/css/admin.css?v=<?= time() ?>">

    <!-- Notyf for toast notifications -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/notyf@3/notyf.min.css">

    <script src="https://cdn.tailwindcss.com"></script>
    <script src="<?= APP_URL ?>/assets/js/tailwind_config.js"></script>

    <style>
        /* Notyf override to match brand */
        .notyf {
            font-family: 'Geist', sans-serif !important;
            font-size: 12px !important;
        }
    </style>

</head>

<body class="min-h-screen flex">

    <!-- ═══════ SIDEBAR ═══════ -->
    <aside id="sidebar" class="w-60 bg-ink-2 border-r border-ink-3 flex flex-col h-screen sticky top-0 flex-shrink-0">

        <div class="px-6 py-6 border-b border-ink-3">
            <a href="<?= APP_URL ?>admin/" class="display text-2xl"><?= htmlspecialchars(APP_NAME) ?><span class="text-gold">.</span></a>
            <div class="eyebrow text-cream-3 mt-1">Producer · Admin</div>
        </div>

        <nav class="py-4 flex-1 overflow-y-auto">
            <div class="eyebrow text-cream-3 px-6 mb-2 mt-2">Dashboard</div>

            <a href="<?= APP_URL ?>admin/" class="<?= admin_link_class('overview') . ($current_url == APP_URL . 'admin/' ? ' active' : '') ?>">
                <span class="flex items-center gap-3">
                    <svg class="icon" viewBox="0 0 16 16" fill="none">
                        <rect x="2" y="2" width="5" height="5" stroke="currentColor" stroke-width="1.5" />
                        <rect x="9" y="2" width="5" height="5" stroke="currentColor" stroke-width="1.5" />
                        <rect x="2" y="9" width="5" height="5" stroke="currentColor" stroke-width="1.5" />
                        <rect x="9" y="9" width="5" height="5" stroke="currentColor" stroke-width="1.5" />
                    </svg>
                    Overview
                </span>
            </a>

            <div class="eyebrow text-cream-3 px-6 mb-2 mt-6">Manage</div>

            <a href="<?= APP_URL ?>admin/inquiries/" class="<?= admin_link_class('inquiries') . ($current_url == APP_URL . 'admin/inquiries/' ? ' active' : '') ?>">
                <span class="flex items-center gap-3">
                    <svg class="icon" viewBox="0 0 16 16" fill="none">
                        <path d="M2 4l6 5 6-5M2 4v8h12V4M2 4h12" stroke="currentColor" stroke-width="1.5" />
                    </svg>
                    Inquiries
                </span>
                <?php if ($new_inq_count > 0): ?>
                    <span class="text-xs font-mono bg-gold text-ink px-1.5 py-0.5 rounded"><?= $new_inq_count ?></span>
                <?php endif; ?>
            </a>

            <a href="<?= APP_URL ?>admin/tracks/" class="<?= admin_link_class('tracks') . (strpos($current_url, APP_URL . 'admin/tracks') === 0 ? ' active' : '') ?>">
                <span class="flex items-center gap-3">
                    <svg class="icon" viewBox="0 0 16 16" fill="none">
                        <path d="M3 13v-3a3 3 0 016 0v3M3 13a1.5 1.5 0 11-3 0 1.5 1.5 0 013 0zm6 0a1.5 1.5 0 11-3 0 1.5 1.5 0 013 0zM3 4l9-2v8" stroke="currentColor" stroke-width="1.5" />
                    </svg>
                    Tracks
                </span>
            </a>

            <a href="<?= APP_URL ?>admin/availability/" class="<?= admin_link_class('availability') . ($current_url == APP_URL . 'admin/availability/' ? ' active' : '') ?>">
                <span class="flex items-center gap-3">
                    <svg class="icon" viewBox="0 0 16 16" fill="none">
                        <rect x="2" y="3" width="12" height="11" stroke="currentColor" stroke-width="1.5" />
                        <path d="M2 6h12M5 1v3M11 1v3" stroke="currentColor" stroke-width="1.5" />
                    </svg>
                    Availability
                </span>
            </a>

            <div class="eyebrow text-cream-3 px-6 mb-2 mt-6">Account</div>

            <a href="<?= APP_URL ?>admin/settings/" class="<?= admin_link_class('settings') . ($current_url == APP_URL . 'admin/settings/' ? ' active' : '') ?>">
                <span class="flex items-center gap-3">
                    <svg class="icon" viewBox="0 0 16 16" fill="none">
                        <circle cx="8" cy="8" r="2.5" stroke="currentColor" stroke-width="1.5" />
                        <path d="M8 1v2M8 13v2M15 8h-2M3 8H1M12.95 3.05l-1.41 1.41M4.46 11.54l-1.41 1.41M12.95 12.95l-1.41-1.41M4.46 4.46L3.05 3.05" stroke="currentColor" stroke-width="1.5" />
                    </svg>
                    Settings
                </span>
            </a>
        </nav>

        <div class="px-3 py-4 border-t border-ink-3 space-y-1">
            <a href="<?= APP_URL ?>" target="_blank" class="sidebar-link">
                <span class="flex items-center gap-3">
                    <svg class="icon" viewBox="0 0 16 16" fill="none">
                        <path d="M6 3H3v10h10v-3M9 3h4v4M13 3l-7 7" stroke="currentColor" stroke-width="1.5" />
                    </svg>
                    View live site
                </span>
            </a>
            <a href="<?= APP_URL ?>admin/api/logout.php" class="sidebar-link">
                <span class="flex items-center gap-3">
                    <svg class="icon" viewBox="0 0 16 16" fill="none">
                        <path d="M9 3H3v10h6M11 5l3 3-3 3M7 8h7" stroke="currentColor" stroke-width="1.5" />
                    </svg>
                    Sign out
                </span>
            </a>
        </div>
    </aside>

    <!-- ═══════ MAIN AREA ═══════ -->
    <main class="flex-1 min-w-0">

        <header class="sticky top-0 z-30 bg-ink/90 backdrop-blur-md border-b border-ink-3 px-6 lg:px-10 py-4 flex items-center justify-between">
            <div class="flex items-center gap-4">
                <button id="menuToggle" class="lg:hidden btn-icon" aria-label="Toggle menu">
                    <svg width="20" height="20" viewBox="0 0 20 20" fill="none">
                        <path d="M3 5h14M3 10h14M3 15h14" stroke="currentColor" stroke-width="1.5" />
                    </svg>
                </button>
                <h1 class="display text-3xl"><?= htmlspecialchars($page_title) ?></h1>
            </div>
            <?php if ($user_email): ?>
                <div class="hidden md:flex items-center gap-2 eyebrow text-cream-2">
                    <span class="w-1.5 h-1.5 rounded-full bg-success"></span>
                    <?= htmlspecialchars($user_email) ?>
                </div>
            <?php endif; ?>
        </header>

        <div class="max-w-7xl mx-auto p-6 lg:p-10">
            <!-- page content begins immediately after this comment in each admin page -->