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