
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

function logout() {
    event.preventDefault();
    
    const csrfToken = getCsrfToken();
    
    if (!csrfToken) {
        showAlert('error', 'Errore di sicurezza. Ricarica la pagina.');
        return;
    }
    
    fetch('/logout', {
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
            window.location.href = '/';
        } else {
            throw new Error(data.message || 'Logout fallito');
        }
    })
    .catch(error => {
        console.error('Logout error:', error);
        showAlert('error', 'Errore durante il logout. Riprova.');
    });
}

function showAlert(type, message) {
    const alertsContainer = document.getElementById('alerts-container');
    if (!alertsContainer) {
        console.warn('Alert container not found');
        return;
    }
    
    const alert = document.createElement('div');
    alert.className = `alert alert-${type}`;
    
    alert.textContent = message;
    
    alertsContainer.appendChild(alert);
    
    setTimeout(() => {
        alert.style.transition = 'opacity 0.3s';
        alert.style.opacity = '0';
        setTimeout(() => {
            if (alert.parentNode) {
                alert.parentNode.removeChild(alert);
            }
        }, 300);
    }, 5000);
}

document.addEventListener('DOMContentLoaded', function() {
    const alerts = document.querySelectorAll('.alert');
    alerts.forEach(alert => {
        setTimeout(() => {
            alert.style.transition = 'opacity 0.3s';
            alert.style.opacity = '0';
            setTimeout(() => {
                if (alert.parentNode) {
                    alert.parentNode.removeChild(alert);
                }
            }, 300);
        }, 5000);
    });
});

function validateRegistrationForm() {
    event.preventDefault();
    
    const form = event.target;
    const password = form.querySelector('#password').value;
    const passwordConfirm = form.querySelector('#password_confirmation').value;
    
    if (password.length < 8) {
        showAlert('error', 'La password deve contenere almeno 8 caratteri.');
        return false;
    }
    
    if (!/[A-Z]/.test(password)) {
        showAlert('error', 'La password deve contenere almeno una lettera maiuscola.');
        return false;
    }
    
    if (!/[!@#$%^&*(),.?":{}|<>]/.test(password)) {
        showAlert('error', 'La password deve contenere almeno un carattere speciale.');
        return false;
    }
    
    if (!/[0-9]/.test(password)) {
        showAlert('error', 'La password deve contenere almeno un numero.');
        return false;
    }
    
    if (password !== passwordConfirm) {
        showAlert('error', 'Le password non coincidono.');
        return false;
    }
    
    form.submit();
    return true;
}

function sanitizeInput(input) {
    const div = document.createElement('div');
    div.textContent = input;
    return div.textContent;
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

function removeFromList(newsId) {
    if (!confirm('Sei sicuro di voler rimuovere questo film dalla tua lista?')) {
        return;
    }

    fetch(`/my-lists/${newsId}`, {
        method: 'DELETE',
        headers: {
            'X-CSRF-TOKEN': getCsrfToken(),
            'Accept': 'application/json'
        }
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            showAlert('success', data.message);
            setTimeout(() => {
                location.reload();
            }, 1000);
        } else {
            showAlert('error', data.message || 'Errore durante la rimozione dalla lista');
        }
    })
    .catch(error => {
        console.error('Errore:', error);
        showAlert('error', 'Errore di connessione');
    });
}

function showAddToListModal(filmId) {
    const modal = document.getElementById('addToListModal');
    if (modal) {
        modal.style.display = 'flex';
    }
}

function closeAddToListModal() {
    const modal = document.getElementById('addToListModal');
    if (modal) {
        modal.style.display = 'none';
        document.getElementById('addToListForm').reset();
    }
}

window.onclick = function(event) {
    const modal = document.getElementById('addToListModal');
    if (event.target === modal) {
        closeAddToListModal();
    }
}

function addFilmToList(filmId) {
    const form = document.getElementById('addToListForm');
    const formData = new FormData(form);
    
    const data = {
        status: formData.get('status'),
        rating: formData.get('rating') || null,
        personal_notes: formData.get('personal_notes') || null,
        film_type: 'recommended' 
    };

    fetch(`/my-lists/film/${filmId}`, {
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
            closeAddToListModal();
            setTimeout(() => {
                location.reload();
            }, 1000);
        } else {
            showAlert('error', data.message || 'Errore durante l\'aggiunta alla lista');
        }
    })
    .catch(error => {
        console.error('Errore:', error);
        showAlert('error', 'Errore di connessione');
    });
}

function removeFromFilmList(filmId) {
    if (!confirm('Sei sicuro di voler rimuovere questo film dalla tua lista?')) {
        return;
    }

    fetch(`/my-lists/film/${filmId}`, {
        method: 'DELETE',
        headers: {
            'X-CSRF-TOKEN': getCsrfToken(),
            'Accept': 'application/json'
        }
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            showAlert('success', data.message);
            setTimeout(() => {
                location.reload();
            }, 1000);
        } else {
            showAlert('error', data.message || 'Errore durante la rimozione dalla lista');
        }
    })
    .catch(error => {
        console.error('Errore:', error);
        showAlert('error', 'Errore di connessione');
    });
}