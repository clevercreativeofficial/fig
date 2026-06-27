    <?php
    require_once __DIR__ . '/../path.php';
    require_once CONFIG . '/constants.php';

    ?>

    <!DOCTYPE html>
    <html lang="en">

    <head>
      <meta charset="UTF-8" />
      <meta name="viewport" content="width=device-width, initial-scale=1.0" />
      <title>Studio Access · <?= APP_NAME ?></title>
      <meta name="robots" content="noindex, nofollow" />

      <link rel="preconnect" href="https://fonts.googleapis.com" />
      <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin />
      <link href="https://fonts.googleapis.com/css2?family=Instrument+Serif:ital@0;1&family=Geist:wght@300;400;500;600&family=JetBrains+Mono:wght@400;500&display=swap" rel="stylesheet" />

      <link rel="stylesheet" href="<?= APP_URL ?>assets/css/login.css" />
      <link rel="icon" type="image/x-icon" href="<?= APP_URL ?>assets/favicon.ico" />

      <script src="https://cdn.tailwindcss.com"></script>
      <script src="<?= APP_URL ?>assets/js/tailwind_config.js"></script>

      <!-- Notyf for toast notifications -->
      <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/notyf@3/notyf.min.css">

      <style>
        /* Notyf override to match brand */
        .notyf {
          font-family: 'Geist', sans-serif !important;
          font-size: 12px !important;
        }
      </style>
    </head>

    <body class="grain min-h-screen">

      <div class="grid grid-cols-1 lg:grid-cols-12 min-h-screen">

        <!-- ─── Left: editorial image panel ─── -->
        <aside class="hidden lg:block lg:col-span-7 relative h-64 lg:h-auto overflow-hidden border-b lg:border-b-0 lg:border-r border-ink-3">
          <!-- REPLACE src with your studio photo -->
          <img src="<?= APP_URL ?>assets/images/img_hands.png"
            alt="Studio"
            class="absolute inset-0 w-full h-full object-cover"
            style="filter: grayscale(20%) contrast(1.1) saturate(0.85) brightness(0.6);" />

          <!-- Warm overlay -->
          <div class="absolute inset-0" style="background: linear-gradient(135deg, rgba(11,9,8,0.4) 0%, rgba(11,9,8,0.7) 100%);"></div>

          <!-- Top label -->
          <div class="absolute top-6 lg:top-10 left-6 lg:left-12 eyebrow text-cream-2 z-10">
            <?= APP_NAME ?><span class="text-gold">.</span> &nbsp; / &nbsp; Producer
          </div>

          <!-- Bottom corner -->
          <div class="hidden lg:flex absolute bottom-10 left-12 right-12 items-end justify-between z-10">
            <div>
              <div class="display text-5xl text-cream max-w-md leading-tight">
                The room is <span class="italic-em">listening.</span>
              </div>
              <div class="eyebrow text-cream-3 mt-6">Members Only · Producer · Bujumbura</div>
            </div>
            <div class="vertical eyebrow text-cream-3">
              <span class="live-dot inline-block w-1.5 h-1.5 rounded-full bg-gold mb-3"></span>
              Session in progress
            </div>
          </div>
        </aside>

        <!-- ─── Right: form panel ─── -->
        <main class="lg:col-span-5 flex flex-col justify-between p-8 lg:p-14">

          <!-- Top -->
          <div class="flex items-center justify-between">
            <a href="<?= APP_URL ?>" class="display text-2xl"><?= APP_NAME ?><span class="text-gold">.</span></a>
            <a href="<?= APP_URL ?>" class="eyebrow text-cream-3 hover:text-gold transition-colors">← Back to site</a>
          </div>

          <!-- Form -->
          <div class="rise max-w-md w-full mx-auto py-12">
            <div class="eyebrow text-gold mb-4">— Studio Access</div>

            <h1 class="display text-5xl lg:text-6xl text-cream mb-3">
              Welcome back<span class="italic-em">.</span>
            </h1>

            <p class="text-cream-2 mb-12">Sign in to manage inquiries, tracks, availability and site content.</p>

            <form id="loginForm" method="POST" class="space-y-2">
              <input type="email" name="email" required placeholder="Email" class="field" autocomplete="email" />
              <input type="password" name="password" required placeholder="Password" class="field" autocomplete="current-password" />

              <div class="flex items-center justify-between pt-4 pb-8 eyebrow">
                <label class="flex items-center gap-2 text-cream-3 cursor-pointer">
                  <input type="checkbox" name="remember" class="accent-gold" /> Remember me
                </label>
              </div>

              <button type="submit" class="btn-primary">
                Enter studio
                <svg width="14" height="14" viewBox="0 0 14 14" fill="none">
                  <path d="M1 7H13M13 7L7 1M13 7L7 13" stroke="currentColor" stroke-width="1.5" />
                </svg>
              </button>

              <p id="errMsg" class="err-msg eyebrow mt-4 hidden">Incorrect credentials — please try again.</p>
            </form>

            <!-- Honest demo notice — visible to remind you this isn't real auth -->
            <div class="mt-12 p-4 border border-rust/30 bg-rust/5 text-cream-2 text-xs leading-relaxed hidden">
              <div class="eyebrow text-rust mb-2">⚠ Demo build</div>
              This is a UI prototype only — any credentials will work. Wire to Supabase, Firebase, or your own backend before deploying. See <span class="font-mono">README</span> in the dashboard.
            </div>
          </div>

          <!-- Bottom -->
          <div class="flex items-center justify-between flex-col md:flex-row eyebrow text-cream-3">
            <div>© 2026 <?= APP_NAME ?></div>
            <div>Bujumbura · Burundi</div>
          </div>
        </main>
      </div>

      <!-- Notyf JS (loaded before notification.php which uses it) -->
      <script src="https://cdn.jsdelivr.net/npm/notyf@3/notyf.min.js"></script>
      <script>
        const notyf = new Notyf({
          duration: 5000,
          position: {
            x: 'right',
            y: 'top',
          },
          dismissible: true,
        });

        const params = new URLSearchParams(window.location.search);
        const status = params.get('status');
        const message = params.get('message');

        if (message) {
          if (status === 'success') {
            notyf.success(message);
          } else {
            notyf.error(message);
          }

          // clean URL (important UX)
          window.history.replaceState({}, document.title, window.location.pathname);
        }
      </script>

      <script>
        // ─── Reusable JSON fetch helper ──────────────────────────────
        async function postJSON(url, data) {
          const response = await fetch(url, {
            method: 'POST',
            headers: {
              'Content-Type': 'application/json'
            },
            body: JSON.stringify(data)
          });

          const text = await response.text();

          let result;
          try {
            result = JSON.parse(text);
          } catch {
            console.error('Non-JSON response:', text);
            throw new Error('Something went wrong. Please try again.');
          }

          if (!response.ok) {
            throw new Error(result.error || 'Something went wrong. Please try again.');
          }

          return result;
        }

        // ─── Login handler ───────────────────────────────────────────
        const loginForm = document.getElementById('loginForm');
        const submitBtn = loginForm.querySelector('button[type=submit]');

        loginForm.addEventListener('submit', async (e) => {
          e.preventDefault();

          const submitText = submitBtn.innerText;
          submitBtn.disabled = true;
          submitBtn.textContent = 'Signing in…';

          const formData = new FormData(loginForm);
          const data = {
            email: formData.get('email'),
            password: formData.get('password'),
            remember: formData.get('remember') === 'on'
          };

          try {
            const result = await postJSON('<?= APP_URL ?>login/api/login.php', data);
            if (result.success) {
              window.location.href = '<?= APP_URL ?>admin/';
            }
          } catch (error) {
            console.error('Error:', error);
            notyf.error(error.message || 'Network error. Check your connection.');
          } finally {
            submitBtn.disabled = false;
            submitBtn.textContent = submitText;
          }
        });
      </script>


    </body>

    </html>