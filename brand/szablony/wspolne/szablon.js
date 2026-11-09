// Edycja i siatka. Kliknij tekst, żeby go zmienić. G = pokaż/ukryj siatkę i strefy niebezpieczne.
document.querySelectorAll('[data-edytuj]').forEach(el => el.setAttribute('contenteditable', 'true'));
if (/[?&]siatka/.test(location.search)) document.documentElement.classList.add('siatka-on');
addEventListener('keydown', e => { if ((e.key === 'g' || e.key === 'G') && !e.target.isContentEditable) document.documentElement.classList.toggle('siatka-on'); });
