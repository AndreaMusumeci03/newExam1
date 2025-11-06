
function getCsrfToken() {
    const token = document.querySelector('meta[name="csrf-token"]');
    if (!token) {
        console.error('CSRF token not found');
        return null;
    }
    return token.getAttribute('content');
}

function showAddToListModal(filmId) {
    document.getElementById('quickAddFilmId').value = filmId;
    document.getElementById('quickAddModal').style.display = 'flex';
}

function closeQuickAddModal() {
    document.getElementById('quickAddModal').style.display = 'none';
    document.getElementById('quickAddForm').reset();
}

function quickAddToList() {
    const filmId = document.getElementById('quickAddFilmId').value;
    const status = document.getElementById('quick_status').value;
    
    fetch(`/my-lists/film/${filmId}`, {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
            'Accept': 'application/json'
        },
        body: JSON.stringify({ status: status })
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            showAlert('success', data.message);
            closeQuickAddModal();
        } else {
            showAlert('error', data.message || 'Errore');
        }
    })
    .catch(error => {
        console.error('Errore:', error);
        showAlert('error', 'Errore di connessione');
    });
}

window.onclick = function(event) {
    const modal = document.getElementById('quickAddModal');
    if (event.target === modal) {
        closeQuickAddModal();
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