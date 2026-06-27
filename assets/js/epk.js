// Bio tab switching
const bios = {
    short: document.querySelector('[data-bio="short"].bio-content').innerText.trim(),
    medium: document.querySelector('[data-bio="medium"].bio-content').innerText.trim(),
    long: document.querySelector('[data-bio="long"].bio-content').innerText.trim(),
};
let activeBio = 'short';

document.querySelectorAll('.bio-tab').forEach(tab => {
    tab.addEventListener('click', () => {
        activeBio = tab.dataset.bio;
        document.querySelectorAll('.bio-tab').forEach(t => t.classList.toggle('active', t === tab));
        document.querySelectorAll('.bio-content').forEach(c => c.classList.toggle('active', c.dataset.bio === activeBio));
    });
});

// Copy bio
document.getElementById('copyBio').addEventListener('click', async (e) => {
    try {
        await navigator.clipboard.writeText(bios[activeBio]);
        const btn = e.currentTarget;
        btn.classList.add('copied');
        const toast = document.getElementById('toast');
        toast.classList.add('show');
        setTimeout(() => { btn.classList.remove('copied'); toast.classList.remove('show'); }, 2000);
    } catch (err) {
        alert('Could not copy — please select and copy manually.');
    }
});