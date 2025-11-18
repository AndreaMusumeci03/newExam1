function removeFromList(id, btn) {
    if (!confirm('Sei sicuro di voler rimuovere questo elemento dalla tua lista?')) {
        return;
    }

  
    fetch(`/my-lists/film/${id}`, {
        method: 'DELETE',
        headers: {
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
            'Accept': 'application/json'
        }
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            showAlert('success', data.message);
            if (btn) {
                var card = btn.closest('.news-card');
                if (card) {
                    card.style.transition = 'opacity 0.3s, transform 0.3s';
                    card.style.opacity = '0';
                    card.style.transform = 'scale(0.9)';
                    setTimeout(() => {
                        card.remove();
                        var remainingCards = document.querySelectorAll('.news-card');
                        if (remainingCards.length === 0) {
                            location.reload();
                        }
                        const counter = document.querySelector('.item-count');
                        if (counter) {
                            let currentCount = parseInt(counter.textContent) || 0;
                            counter.textContent = `${currentCount - 1} elementi in questa lista`;
                        }
                    }, 300);
                }
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