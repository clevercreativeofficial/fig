<?php

$flashMessages = [];
$sessionKeys = [
    'flash_success',
    'flash_error',
    'flash_info',   // optional, for neutral messages
];

foreach ($sessionKeys as $key) {
    if (!empty($_SESSION[$key])) {
        $flashMessages[] = [
            'type' => (str_contains($key, 'success')) ? 'success' : 'error',
            'message' => $_SESSION[$key]
        ];
        unset($_SESSION[$key]); // clear AFTER reading
    }
}
?>

<?php if (!empty($flashMessages)): ?>
<script>
document.addEventListener("DOMContentLoaded", () => {

    const notyf = new Notyf({
        duration: 6000,
        position: { x: 'right', y: 'top' },
        dismissible: true,
        ripple: true,
    });

});
</script>
<?php endif; ?>