function getCsrfToken() {
    const token = document.querySelector('meta[name="csrf-token"]');
    if (!token) {
        console.error('CSRF token not found');
        return null;
    }
    return token.getAttribute('content');
}

function addToList(newsId) {
    const form = event.target;
    const formData = new FormData(form);
    
    const data = {
        status: formData.get('status'),
        rating: formData.get('rating') || null,
        personal_notes: formData.get('personal_notes') || null,
    };

    fetch(`/my-lists/${newsId}`, {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': getCsrfToken(),
            'Accept': 'application/json'
        },
        body: JSON.stringify(data)
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            showAlert('success', data.message);
            form.reset();
        } else {
            showAlert('error', data.message || 'Errore durante l\'aggiunta alla lista');
        }
    })
    .catch(error => {
        console.error('Errore:', error);
        showAlert('error', 'Errore di connessione');
    });
}

function removeFromList(id, type, el) {
    if (!['news','film'].includes(type)) {
        showAlert('error', 'Tipo non valido.');
        return;
    }
    
    if (!confirm('Sei sicuro di voler rimuovere questo elemento dalla tua lista?')) return;

    const btn = el || document.querySelector(`[data-remove-id="${id}"][data-remove-type="${type}"]`);
    if (btn) btn.disabled = true;

    const csrf = getCsrfToken();
    if (!csrf) {
        showAlert('error', 'Errore di sicurezza. Ricarica la pagina.');
        if (btn) btn.disabled = false;
        return;
    }

    const baseUrl = btn?.getAttribute('data-remove-url-base') || '/my-lists';
    const url = type === 'film' ? `${baseUrl}/film/${id}` : `${baseUrl}/${id}`;

    fetch(url, {
        method: 'DELETE',
        headers: {
            'X-CSRF-TOKEN': csrf,
            'Accept': 'application/json'
        },
        credentials: 'same-origin'
    })
    .then(res => {
        if (!res.ok) {
            if (res.status === 419) throw new Error('Sessione scaduta. Ricarica la pagina.');
            throw new Error('HTTP ' + res.status);
        }
        const ct = res.headers.get('content-type') || '';
        return ct.includes('application/json') ? res.json() : { success: true, message: 'Elemento rimosso.' };
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





function addToFavorites(newsId) {
    const button = event.target;
    const csrfToken = getCsrfToken();
    
    if (!csrfToken) {
        showAlert('error', 'Errore di sicurezza. Ricarica la pagina.');
        return;
    }
    
    button.disabled = true;
    
    fetch(`/favorites/${newsId}`, {
        method: 'POST',
        headers: {
            'X-CSRF-TOKEN': csrfToken,
            'Accept': 'application/json',
            'Content-Type': 'application/json'
        },
        credentials: 'include',
        body: JSON.stringify({})
    })
    .then(response => {
        if (!response.ok) {
            throw new Error('Errore nella richiesta');
        }
        return response.json();
    })
    .then(data => {
        if (data.success) {
            button.textContent = '💔 Rimuovi dai Preferiti';
            button.classList.remove('btn-success');
            button.classList.add('btn-danger');
            button.setAttribute('onclick', `removeFromFavorites(${newsId})`);
            
            showAlert('success', data.message || 'Aggiunto ai preferiti!');
        } else {
            throw new Error(data.message || 'Operazione fallita');
        }
    })
    .catch(error => {
        console.error('Errore:', error);
        showAlert('error', 'Si è verificato un errore. Riprova.');
    })
    .finally(() => {
        button.disabled = false;
    });
}

function removeFromFavorites(newsId) {
    const button = event.target;
    const csrfToken = getCsrfToken();
    
    if (!csrfToken) {
        showAlert('error', 'Errore di sicurezza. Ricarica la pagina.');
        return;
    }
    
    button.disabled = true;
    
    fetch(`/favorites/${newsId}`, {
        method: 'DELETE',
        headers: {
            'X-CSRF-TOKEN': csrfToken,
            'Accept': 'application/json',
            'Content-Type': 'application/json'
        },
        credentials: 'include',
        body: JSON.stringify({})
    })
    .then(response => {
        if (!response.ok) {
            throw new Error('Errore nella richiesta');
        }
        return response.json();
    })
    .then(data => {
        if (data.success) {
            button.textContent = '❤️ Aggiungi ai Preferiti';
            button.classList.remove('btn-danger');
            button.classList.add('btn-success');
            button.setAttribute('onclick', `addToFavorites(${newsId})`);
            
            showAlert('success', data.message || 'Rimosso dai preferiti!');
            setTimeout(() => {
                location.reload();
            }, 1);
        } else {
            throw new Error(data.message || 'Operazione fallita');
        }
    })
    .catch(error => {
        console.error('Errore:', error);
        showAlert('error', 'Si è verificato un errore. Riprova.');
    })
    .finally(() => {
        button.disabled = false;
    });
}

function submitComment(newsId) {
    event.preventDefault();
    
    const form = event.target;
    const textarea = form.querySelector('textarea[name="content"]');
    const submitButton = form.querySelector('button[type="submit"]');
    const content = textarea.value.trim();
    const csrfToken = getCsrfToken();
    
    if (!content) {
        showAlert('error', 'Il commento non può essere vuoto.');
        return;
    }
    
    if (!csrfToken) {
        showAlert('error', 'Errore di sicurezza. Ricarica la pagina.');
        return;
    }
    
    submitButton.disabled = true;
    
    fetch(`/news/${newsId}/comments`, {
        method: 'POST',
        headers: {
            'X-CSRF-TOKEN': csrfToken,
            'Accept': 'application/json',
            'Content-Type': 'application/json'
        },
        credentials: 'include',
        body: JSON.stringify({ content: content })
    })
    .then(response => {
        if (!response.ok) {
            throw new Error('Errore nella richiesta');
        }
        return response.json();
    })
    .then(data => {
        if (data.success) {
            showAlert('success', data.message || 'Commento aggiunto!');
            textarea.value = '';
            setTimeout(() => {
                window.location.reload();
            }, 1000);
        } else {
            throw new Error(data.message || 'Operazione fallita');
        }
    })
    .catch(error => {
        console.error('Errore:', error);
        showAlert('error', 'Si è verificato un errore. Riprova.');
    })
    .finally(() => {
        submitButton.disabled = false;
    });
}

function deleteComment(commentId) {
    if (!confirm('Sei sicuro di voler eliminare questo commento?')) {
        return;
    }
    
    const csrfToken = getCsrfToken();
    
    if (!csrfToken) {
        showAlert('error', 'Errore di sicurezza. Ricarica la pagina.');
        return;
    }
    
    fetch(`/comments/${commentId}`, {
        method: 'DELETE',
        headers: {
            'X-CSRF-TOKEN': csrfToken,
            'Accept': 'application/json',
            'Content-Type': 'application/json'
        },
        credentials: 'include',
        body: JSON.stringify({})
    })
    .then(response => {
        if (!response.ok) {
            throw new Error('Errore nella richiesta');
        }
        return response.json();
    })
    .then(data => {
        if (data.success) {
            showAlert('success', data.message || 'Commento eliminato!');
            setTimeout(() => {
                window.location.reload();
            }, 1000);
        } else {
            throw new Error(data.message || 'Operazione fallita');
        }
    })
    .catch(error => {
        console.error('Errore:', error);
        showAlert('error', 'Si è verificato un errore. Riprova.');
    });
}
