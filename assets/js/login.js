document.addEventListener('DOMContentLoaded', () => {

    document.getElementById('loginForm').addEventListener('submit', (e) => {
        e.preventDefault();

        const data         = new FormData(e.target);
        const email        = data.get('email');
        const password     = data.get('password');
        const demoEmail    = 'login@fighitmaker.com';
        const demoPassword = 'password';

        if (!email.includes('@') || email !== demoEmail || password !== demoPassword) {
            document.getElementById('errMsg').classList.remove('hidden');
            return;
        }

        sessionStorage.setItem('fig_session', JSON.stringify({
            email,
            loggedAt: Date.now()
        }));

        window.location.href = '/admin/';
    });

});