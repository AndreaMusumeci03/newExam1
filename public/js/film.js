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
            formEl.reset();
        } else {
            showAlert('error', data.message || 'Errore durante la rimozione');
        }
    })
    .catch(error => {
        console.error('Errore:', error);
        showAlert('error', 'Impossibile aggiungere alla lista');
    });
}

function addToFavorites(filmId, btn) {
  if (!btn) {
    btn = event ? event.target : null;
  }
  
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
    
            if (btn) {
                btn.textContent = '💔 Rimuovi dai Preferiti';
                btn.className = 'btn btn-danger btn-block';
                btn.setAttribute('onclick', 'removeFromFavorites(' + filmId + ', this)');
            }
        } else {
            showAlert('info', data.message || 'Operazione non riuscita');
        }
    })
    .catch(error => {
        console.error('Errore:', error);
        showAlert('error', 'Impossibile aggiungere ai preferiti');
    });
}

function removeFromFavorites(filmId, btn) {

  if(window.location.pathname.includes('/favorites')){
    if (!confirm('Sei sicuro di voler rimuovere questo film dai tuoi preferiti?')) {
        return;
    }
  }

  if (!btn) {
    btn = event ? event.target : null;
  }
  
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
            
            if (!btn) return;
            
            var card = btn.closest('.film-card');
            if (card && window.location.pathname.includes('/favorites')) {
                card.style.transition = 'opacity 0.3s, transform 0.3s';
                card.style.opacity = '0';
                card.style.transform = 'scale(0.9)';
                setTimeout(() => {
                    card.remove();
                    var remainingCards = document.querySelectorAll('.film-card');
                    if (remainingCards.length === 0) {
                        location.reload();
                    }
                }, 300);
            } else {
                btn.textContent = '❤️ Aggiungi ai Preferiti';
                btn.className = 'btn btn-success btn-block';
                btn.setAttribute('onclick', 'addToFavorites(' + filmId + ', this)');
            }
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

   fetch('/films/' + filmId + '/comments', {
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
              const commentsSection = document.querySelector('.comments-section');
          
            const emptyState = commentsSection.querySelector('.empty-state');
            if (emptyState) {
                emptyState.remove();
            }

            commentsSection.insertAdjacentHTML('beforeend', data.html);

            const counter = commentsSection.querySelector('h3');
            if (counter) {
                let currentCount = parseInt(counter.innerText.match(/\d+/)[0]);
                counter.innerText = `💬 Commenti (${currentCount + 1})`;
            }

            formEl.reset(); 
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

function deleteComment(commentId, btn) {
  if (!confirm('Eliminare questo commento?')) return;

  if (!btn) {
    btn = event ? event.target : null;
  }

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
            
            if (!btn) return;
            
            var comment = btn.closest('.comment');
            if (comment) {
                comment.style.transition = 'opacity 0.3s, transform 0.3s';
                comment.style.opacity = '0';
                comment.style.transform = 'translateX(-20px)';
                setTimeout(() => {
                    comment.remove();
                    var counter = document.querySelector('.comments-section h3');
                    if (counter) {
                        var currentCount = parseInt(counter.textContent.match(/\d+/)[0]);
                        counter.textContent = '💬 Commenti (' + (currentCount - 1) + ')';
                    }
                }, 300);
            }
        } else {
            showAlert('error', data.message || 'Errore durante la rimozione');
        }
    })
    .catch(error => {
        console.error('Errore:', error);
        showAlert('error', 'Errore di connessione');
    });
}