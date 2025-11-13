function getCsrfToken() {
  var el = document.querySelector('meta[name="csrf-token"]');
  return el ? el.getAttribute('content') : '';
}

function addToFilmList(filmId, formEl) {
  var data = new FormData(formEl);

  return fetch('/my-lists/film/' + filmId, {
    method: 'POST',
    headers: {
      'X-CSRF-TOKEN': getCsrfToken(),
      'X-Requested-With': 'XMLHttpRequest',
      'Accept': 'application/json'
    },
    body: data
  })
  .then(response => response.json())
    .then(data => {
        if (data.success) {
            showAlert('success', data.message);
            setTimeout(() => {
                location.reload();
            }, 1000);
        } else {
            showAlert('error', data.message || 'Errore durante la rimozione');
        }
    })
    .catch(error => {
        console.error('Errore:', error);
        showAlert('error', 'Impossibile aggiungere alla lista');
    });
  return false;
}

function addToFavorites(filmId) {
  return fetch('/favorites/film/' + filmId, {
    method: 'POST',
    headers: {
      'X-CSRF-TOKEN': getCsrfToken(),
      'X-Requested-With': 'XMLHttpRequest',
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
            showAlert('error', data.message || 'Errore durante la rimozione');
        }
    })
    .catch(error => {
        console.error('Errore:', error);
        showAlert('error', 'Impossibile aggiungere ai preferiti');
    });
}

function removeFromFavorites(filmId) {
  return fetch('/favorites/film/' + filmId, {
    method: 'DELETE',
    headers: {
      'X-CSRF-TOKEN': getCsrfToken(),
      'X-Requested-With': 'XMLHttpRequest',
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
            showAlert('error', data.message || 'Errore durante la rimozione');
        }
    })
    .catch(error => {
        console.error('Errore:', error);
        showAlert('error', 'Impossibile rimuovere dai preferiti');
    });
}

function submitFilmComment(filmId, formEl) {
  var data = new FormData(formEl);

  return fetch('/films/' + filmId + '/comments', {
    method: 'POST',
    headers: {
      'X-CSRF-TOKEN': getCsrfToken(),
      'X-Requested-With': 'XMLHttpRequest',
      'Accept': 'application/json'
    },
    body: data
  })
  .then(response => response.json())
    .then(data => {
        if (data.success) {
            showAlert('success', data.message);
            setTimeout(() => {
                location.reload();
            }, 1000);
        } else {
            showAlert('error', data.message || 'Errore durante la rimozione');
        }
    })
    .catch(error => {
        console.error('Errore:', error);
        showAlert('error', 'Impossibile inviare il commento');
    });
  return false;
}

function deleteComment(commentId) {
  if (!confirm('Eliminare questo commento?')) return;

  return fetch('/comments/' + commentId, {
    method: 'DELETE',
    headers: {
      'X-CSRF-TOKEN': getCsrfToken(),
      'X-Requested-With': 'XMLHttpRequest',
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
            showAlert('error', data.message || 'Errore durante la rimozione');
        }
    })
    .catch(error => {
        console.error('Errore:', error);
        showAlert('error', 'Errore di connessione');
    });
}