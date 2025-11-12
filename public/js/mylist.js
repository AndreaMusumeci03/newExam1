function removeFromList(id, type, el) {
  if (!['news','film'].includes(type)) return showAlert('error', 'Tipo non valido.');
  if (!confirm('Sei sicuro di voler rimuovere questo elemento dalla tua lista?')) return;

  const btn = el || document.querySelector(`[data-remove-id="${id}"][data-remove-type="${type}"]`);
  if (btn) btn.disabled = true;

  const csrf = document.querySelector('meta[name="csrf-token"]')?.content;
  if (!csrf) { showAlert('error', 'Token CSRF mancante.'); if (btn) btn.disabled = false; return; }

  const baseUrl = btn?.getAttribute('data-remove-url-base') || '/my-lists';
  const url = type === 'film' ? `${baseUrl}/film/${id}` : `${baseUrl}/${id}`;

  fetch(url, {
    method: 'DELETE',
    headers: { 'X-CSRF-TOKEN': csrf, 'Accept': 'application/json' },
    credentials: 'same-origin'
  })
  .then(r => {
    if (!r.ok) {
      if (r.status === 419) throw new Error('Sessione scaduta.');
      throw new Error('HTTP ' + r.status);
    }
    const ct = r.headers.get('content-type') || '';
    return ct.includes('application/json') ? r.json() : { success: true, message: 'Rimosso.' };
  })
  .then(data => {
    if (!data.success) throw new Error(data.message || 'Errore');
    showAlert('success', data.message || 'Elemento rimosso');
    const card = btn?.closest('[data-item-card]');
    if (card) {
      card.style.transition = 'opacity 0.3s';
      card.style.opacity = '0';
      setTimeout(() => card.remove(), 300);
    }
  })
  .catch(err => {
    console.error(err);
    showAlert('error', err.message || 'Errore durante la rimozione');
    if (btn) btn.disabled = false;
  });
}