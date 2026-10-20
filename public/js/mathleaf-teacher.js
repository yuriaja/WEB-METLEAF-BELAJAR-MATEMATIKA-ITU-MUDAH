// Interaksi formulir dashboard guru. Data disimpan lewat form Laravel, bukan localStorage.
document.addEventListener('DOMContentLoaded', () => {
  document.querySelectorAll('.teacher-copy').forEach(button => {
    button.addEventListener('click', async () => {
      const code = button.dataset.code;
      try {
        await navigator.clipboard.writeText(code);
        const original = button.textContent;
        button.textContent = 'Tersalin ✓';
        setTimeout(() => button.textContent = original, 1600);
      } catch (error) {
        window.prompt('Salin kode kelas:', code);
      }
    });
  });
  const holder = document.querySelector('#teacher-questions');
  const add = document.querySelector('#teacher-add-question');
  if (!holder || !add) return;
  let index = 1;
  add.addEventListener('click', () => {
    if (index >= 50) { window.alert('Maksimal 50 soal.'); return; }
    const n = index++;
    const fieldset = document.createElement('fieldset');
    fieldset.className = 'teacher-question-card';
    // Generated indices, not user-generated content.
    fieldset.innerHTML = `<legend>Soal ${n + 1}</legend>
      <label>Pertanyaan</label><textarea name="questions[${n}][text]" rows="2" required></textarea>
      <div class="teacher-form-grid">${['A','B','C','D'].map((label,i) => `<div><label>Pilihan ${label}</label><input name="questions[${n}][options][${i}]" required></div>`).join('')}</div>
      <label>Jawaban benar</label><select name="questions[${n}][answer]">${['A','B','C','D'].map((label,i) => `<option value="${i}">${label}</option>`).join('')}</select>
      <label>Pembahasan (opsional)</label><textarea name="questions[${n}][explanation]" rows="2"></textarea>
      <button type="button" class="btn btn-outline teacher-remove-question">Hapus soal ini</button>`;
    fieldset.querySelector('.teacher-remove-question').addEventListener('click', () => fieldset.remove());
    holder.appendChild(fieldset);
  });
});



/* Navigasi aktif sidebar guru */
document.addEventListener('DOMContentLoaded', () => {
    const nav = document.querySelector('.guru-navigation');
    if (!nav) return;

    const links = [...nav.querySelectorAll('a')];

    function updateActiveMenu() {
        const hash = window.location.hash;

        const activeAnchor = hash
            ? links.find(link =>
                link.getAttribute('href') === hash
            )
            : null;

        links.forEach(link => {
            const active = activeAnchor
                ? link === activeAnchor
                : link.hasAttribute('data-guru-dashboard');

            link.classList.toggle('selected', active);

            if (active) {
                link.setAttribute(
                    'aria-current',
                    activeAnchor ? 'location' : 'page'
                );
            } else {
                link.removeAttribute('aria-current');
            }
        });
    }

    window.addEventListener('hashchange', updateActiveMenu);
    updateActiveMenu();
});
