<?php
require_once __DIR__ . '/../path.php';
require_once INCLUDES . '/header.php';
?>

<!-- Main -->
<main class="min-h-screen flex-1 flex flex-col items-center justify-center px-6 lg:px-12 py-20 text-center">
  <div class="rise max-w-3xl">
    <div class="eyebrow text-cream-3 mb-8">Error · 404</div>

    <h1 class="display text-[26vw] md:text-[22vw] lg:text-[18rem] text-gold flicker leading-none">
      404
    </h1>

    <h2 class="display text-4xl md:text-6xl text-cream mt-8 mb-6">
      That track doesn't exist <span class="italic-em">on this album.</span>
    </h2>

    <p class="text-cream-2 text-lg max-w-lg mx-auto mb-12">
      You followed a link that points nowhere — or one that's been re-edited.
      No harm done. The catalogue is still here.
    </p>

    <div class="flex flex-col sm:flex-row items-center justify-center gap-6">
      <a href="index.html" class="btn-primary">
        Back to home
        <svg width="14" height="14" viewBox="0 0 14 14" fill="none">
          <path d="M1 7H13M13 7L7 1M13 7L7 13" stroke="currentColor" stroke-width="1.5" />
        </svg>
      </a>
      <a href="discography.html" class="btn-ghost">Browse discography →</a>
    </div>
  </div>
</main>

<?php require_once INCLUDES . '/footer.php'; ?>