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
  .then(function (res) {
    if (!res.ok) throw new Error('HTTP ' + res.status);
    return res.headers.get('content-type')?.includes('application/json') ? res.json() : {};
  })
  .then(function () {
    window.location.reload();
  })
  .catch(function () {
    alert("Errore durante l'aggiunta alla lista");
  });

  // evita submit nativo del form
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
  .then(function (res) {
    if (!res.ok) throw new Error('HTTP ' + res.status);
    return res.headers.get('content-type')?.includes('application/json') ? res.json() : {};
  })
  .then(function () {
    window.location.reload();
  })
  .catch(function () {
    alert("Errore durante l'aggiunta ai preferiti");
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
  .then(function (res) {
    if (!res.ok) throw new Error('HTTP ' + res.status);
    return res.headers.get('content-type')?.includes('application/json') ? res.json() : {};
  })
  .then(function () {
    window.location.reload();
  })
  .catch(function () {
    alert('Errore durante la rimozione dai preferiti');
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
  .then(function (res) {
    if (!res.ok) throw new Error('HTTP ' + res.status);
    return res.headers.get('content-type')?.includes('application/json') ? res.json() : {};
  })
  .then(function () {
    window.location.reload();
  })
  .catch(function () {
    alert('Errore durante l\'invio del commento');
  });

  // evita submit nativo del form
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
  .then(function (res) {
    if (!res.ok) throw new Error('HTTP ' + res.status);
    return res.headers.get('content-type')?.includes('application/json') ? res.json() : {};
  })
  .then(function () {
    window.location.reload();
  })
  .catch(function () {
    alert('Errore durante l\'eliminazione del commento');
  });
}