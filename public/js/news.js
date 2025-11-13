function getCsrfToken() {
    const token = document.querySelector('meta[name="csrf-token"]');
    if (!token) {
        console.error('CSRF token not found');
        return null;
    }
    return token.getAttribute('content');
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
