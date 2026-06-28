<!-- ─────────── FOOTER ─────────── -->
<footer class="px-6 lg:px-12 py-8 border-t border-ink-3">
    <div class="flex flex-col lg:flex-row lg:items-end lg:justify-between gap-12">
        <div>
            <div class="display text-4xl lg:text-6xl text-cream leading-none"><?= APP_NAME ?><span
                    class="text-gold">.</span></div>
            <div class="eyebrow text-cream-3 mt-4"><?= APP_DESCRIPTION ?></div>
        </div>
        <div class="grid grid-cols-2 md:grid-cols-3 gap-8 text-sm">
            <div>
                <div class="eyebrow text-cream-3 mb-3">Listen</div>
                <ul class="space-y-2 text-cream-2">
                    <li><a href="#" class="hover:text-gold transition-colors">Spotify</a></li>
                    <li><a href="#" class="hover:text-gold transition-colors">SoundCloud</a></li>
                </ul>
            </div>
            <div>
                <div class="eyebrow text-cream-3 mb-3">Follow</div>
                <ul class="space-y-2 text-cream-2">
                    <li><a href="#" class="hover:text-gold transition-colors">Instagram</a></li>
                    <li><a href="#" class="hover:text-gold transition-colors">YouTube</a></li>
                    <li><a href="#" class="hover:text-gold transition-colors">X / Twitter</a></li>
                    <li><a href="#" class="hover:text-gold transition-colors">TikTok</a></li>
                </ul>
            </div>
            <div class="hidden">
                <div class="eyebrow text-cream-3 mb-3">Business</div>
                <ul class="space-y-2 text-cream-2">
                    <li><a href="#" class="hover:text-gold transition-colors">Press kit</a></li>
                    <li><a href="#" class="hover:text-gold transition-colors">Sync licensing</a></li>
                    <li><a href="#" class="hover:text-gold transition-colors">Management</a></li>
                </ul>
            </div>
            <div>
                <div class="eyebrow text-cream-3 mb-3">Links</div>
                <ul class="space-y-2 text-cream-2">
                    <li><a href="#work" class="hover:text-gold transition-colors">Work</a></li>
                    <li><a href="#services" class="hover:text-gold transition-colors">Services</a></li>
                    <li><a href="#booking" class="hover:text-gold transition-colors">Booking</a></li>
                    <li><a href="<?= APP_URL ?>discography" class="hover:text-gold transition-colors">Discography</a></li>
                </ul>
            </div>
        </div>
    </div>
    <div
        class="mt-16 pt-8 border-t border-ink-3 flex flex-col md:flex-row md:justify-between gap-4 eyebrow text-cream-3">
        <div>© <span id="year"></span> <?= APP_NAME ?> · All rights reserved</div>
        <div>Designed by <a href="<?= APP_AUTHOR_URL ?>" target="_blank"
                class="hover:text-gold transition-colors"><?= APP_AUTHOR ?></a></div>
    </div>
</footer>

<script src="<?= APP_URL ?>assets/js/script.js"></script>

</body>

</html>