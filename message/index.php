<?php
require_once __DIR__ . '/../path.php';
require_once INCLUDES . '/header.php';
?>

<!-- Main -->
<main class="min-h-screen flex-1 flex flex-col items-center justify-center px-6 lg:px-12 py-20 text-center">
  <div class="rise max-w-3xl">
    <div class="eyebrow text-cream-3 mb-8">
      Temporary Issue
    </div>

    <h1 class="display text-[18vw] md:text-[14vw] lg:text-[10rem] text-gold flicker leading-none">
      Oops
    </h1>

    <h2 class="display text-4xl md:text-6xl text-cream mt-8 mb-6">
      Something isn't working <span class="italic-em">right now.</span>
    </h2>

    <p class="text-cream-2 text-lg max-w-lg mx-auto mb-12">
      We're having trouble loading this experience at the moment.
      Try again in a little while or return to the main catalogue.
    </p>

    <div class="flex flex-col sm:flex-row items-center justify-center gap-6">
      <a href="<?= APP_URL ?>" class="btn-primary">
        Back to home
        <svg width="14" height="14" viewBox="0 0 14 14" fill="none">
          <path d="M1 7H13M13 7L7 1M13 7L7 13" stroke="currentColor" stroke-width="1.5" />
        </svg>
      </a>

      <button onclick="window.location.reload();" class="btn-ghost">
        Try again →
      </button>
    </div>
  </div>
</main>

<?php require_once INCLUDES . '/footer.php'; ?>