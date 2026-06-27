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