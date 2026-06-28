    <?php
    require_once __DIR__ . '/../path.php';
    require_once CONFIG . '/constants.php';

    ?>

    <!DOCTYPE html>
    <html lang="en">

    <head>
        <meta charset="UTF-8" />
        <meta name="viewport" content="width=device-width, initial-scale=1.0" />
        <title><?= APP_NAME ?> — <?= APP_DESCRIPTION ?></title>
        <meta name="description" content="<?= APP_DESCRIPTION ?>" />

        <!-- Css -->
        <link rel="stylesheet" href="<?= APP_URL ?>assets/css/style.css" />
        <link rel="stylesheet" href="<?= APP_URL ?>assets/css/epk.css" />
        <link rel="stylesheet" href="<?= APP_URL ?>assets/css/not_found.css" />
        <link rel="stylesheet" href="<?= APP_URL ?>assets/css/discography.css" />

        <link rel="icon" type="image/x-icon" href="<?= APP_URL ?>assets/favicon.ico" />

        <!-- Fonts: editorial serif + clean grotesque + technical mono -->
        <link rel="preconnect" href="https://fonts.googleapis.com" />
        <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin />
        <link
            href="https://fonts.googleapis.com/css2?family=Instrument+Serif:ital@0;1&family=Geist:wght@300;400;500;600&family=JetBrains+Mono:wght@400;500&display=swap"
            rel="stylesheet" />

        <script src="https://cdn.tailwindcss.com"></script>
        <script src="<?= APP_URL ?>assets/js/tailwind_config.js"></script>

    </head>

    <body class="grain vignette max-w-7xl mx-auto">

        <!-- ─────────── NAV ─────────── -->
        <nav id="nav"
            class="max-w-7xl mx-auto fixed top-0 left-0 right-0 z-50 px-6 lg:px-12 py-5 transition-all duration-500">
            <div class="flex items-center justify-between">
                <a href="<?= APP_URL ?>" class="display text-2xl tracking-tight">
                    <?= APP_NAME ?><span class="text-gold">.</span>
                </a>
                <ul class="hidden md:flex items-center gap-10 text-sm">
                    <li><a href="<?= APP_URL ?>#about" class="nav-link">About</a></li>
                    <li><a href="<?= APP_URL ?>#services" class="nav-link">Services</a></li>
                    <li><a href="<?= APP_URL ?>#work" class="nav-link">Work</a></li>
                    <li><a href="<?= APP_URL ?>#booking" class="nav-link">Booking</a></li>
                </ul>
                <a href="#booking"
                    class="hidden md:inline-flex items-center gap-2 eyebrow text-cream-2 hover:text-gold transition-colors">
                    <span class="live-dot inline-block w-1.5 h-1.5 rounded-full bg-gold"></span>
                    Available · Q2 2026
                </a>
            
                <button id="menuBtn" class="md:hidden flex flex-col gap-1.5">
                    <span class="w-6 h-px bg-cream"></span>
                    <span class="w-6 h-px bg-cream"></span>
                </button>
            </div>
        </nav>

        <!-- Mobile menu overlay -->
        <div id="menu"
            class="menu-overlay fixed inset-0 z-40 bg-ink flex flex-col justify-center items-center gap-8 md:hidden">
            <a href="<?= APP_URL ?>#about" class="display text-5xl menu-link">About</a>
            <a href="<?= APP_URL ?>#services" class="display text-5xl menu-link">Services</a>
            <a href="<?= APP_URL ?>#work" class="display text-5xl menu-link">Work</a>
            <a href="<?= APP_URL ?>#booking" class="display text-5xl menu-link">Booking</a>
            <span class="eyebrow mt-8 text-cream-2"><span
                    class="live-dot inline-block w-1.5 h-1.5 rounded-full bg-gold mr-2"></span>Available · Q2 2026</span>
        </div>