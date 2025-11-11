
function showAddToListModal(filmId) {
    document.getElementById('addToListModal').style.display = 'flex';
}

function closeAddToListModal() {
    document.getElementById('addToListModal').style.display = 'none';
    document.getElementById('addToListForm').reset();
}

function addFilmToList(filmId) {
    const formData = new FormData(document.getElementById('addToListForm'));
    const data = {
        status: formData.get('status'),
        rating: formData.get('rating'),
        personal_notes: formData.get('personal_notes')
    };
    
    fetch(`/my-lists/film/${filmId}`, {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
            'Accept': 'application/json'
        },
        body: JSON.stringify(data)
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            alert('✅ ' + data.message);
            location.reload(); 
        } else {
            alert('❌ ' + (data.message || 'Errore'));
        }
    })
    .catch(error => {
        console.error('Errore:', error);
        alert('❌ Errore di connessione');
    });
}

function removeFromFilmList(filmId) {
    if (!confirm('Vuoi davvero rimuovere questo film dalla tua lista?')) {
        return;
    }
    
    fetch(`/my-lists/film/${filmId}`, {
        method: 'DELETE',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
            'Accept': 'application/json'
        }
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            alert('✅ ' + data.message);
            location.reload();
        } else {
            alert('❌ ' + (data.message || 'Errore'));
        }
    })
    .catch(error => {
        console.error('Errore:', error);
        alert('❌ Errore di connessione');
    });
}

window.onclick = function(event) {
    const modal = document.getElementById('addToListModal');
    if (event.target === modal) {
        closeAddToListModal();
    }
}

window.showAddToListModal = showAddToListModal;
window.removeFromFilmList = removeFromFilmList;