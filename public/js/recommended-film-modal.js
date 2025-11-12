function showAddToListModal(id, title, entityType) {
    const modal = document.getElementById('addToListModal');
    if (!modal) return;

    const titleEl = document.getElementById('addToListModalTitle');
    if (titleEl) titleEl.textContent = title || 'Elemento';

    const et = document.getElementById('addToListEntityType');
    const ei = document.getElementById('addToListEntityId');
    if (et) et.value = entityType; 
    if (ei) ei.value = id;

    modal.style.display = 'flex';
}

function closeAddToListModal() {
    const modal = document.getElementById('addToListModal');
    const form = document.getElementById('addToListForm');
    if (modal) modal.style.display = 'none';
    if (form) form.reset();
}

function submitAddToListModal(event) {
    event.preventDefault();

    const form = document.getElementById('addToListForm');
    const entityType = document.getElementById('addToListEntityType')?.value;
    const id = document.getElementById('addToListEntityId')?.value;

    const formData = new FormData(form);
    const payload = {
        status: formData.get('status'),
        rating: formData.get('rating') || null,
        personal_notes: formData.get('personal_notes') || null
    };

    const csrf = document.querySelector('meta[name="csrf-token"]')?.content;
    if (!csrf) {
        if (typeof showAlert === 'function') showAlert('error', 'Token CSRF mancante.');
        else alert('❌ Token CSRF mancante.');
        return;
    }

    const url = entityType === 'film' ? `/my-lists/film/${id}` : `/my-lists/${id}`;

    fetch(url, {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': csrf,
            'Accept': 'application/json'
        },
        body: JSON.stringify(payload)
    })
    .then(r => {
        if (!r.ok) throw new Error('Errore HTTP ' + r.status);
        const ct = r.headers.get('content-type') || '';
        return ct.includes('application/json') ? r.json() : { success: true, message: 'Aggiunto!' };
    })
    .then(data => {
        if (!data.success) throw new Error(data.message || 'Operazione non riuscita');
        if (typeof showAlert === 'function') showAlert('success', data.message || 'Aggiunto alla lista!');
        else alert('✅ ' + (data.message || 'Aggiunto alla lista!'));
        closeAddToListModal();
        setTimeout(() => location.reload(), 700);
    })
    .catch(err => {
        console.error('Errore:', err);
        if (typeof showAlert === 'function') showAlert('error', err.message || 'Errore durante l\'aggiunta');
        else alert('❌ ' + (err.message || 'Errore'));
    });
}

window.onclick = function(event) {
    const modal = document.getElementById('addToListModal');
    if (event.target === modal) {
        closeAddToListModal();
    }
};

window.showAddToListModal = showAddToListModal;
window.closeAddToListModal = closeAddToListModal;
window.submitAddToListModal = submitAddToListModal;