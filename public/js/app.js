
function getCsrfToken() {
    const token = document.querySelector('meta[name="csrf-token"]');
    if (!token) {
        console.error('CSRF token not found');
        return null;
    }
    return token.getAttribute('content');
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


