
function getCsrfToken() {
    const token = document.querySelector('meta[name="csrf-token"]');
    if (!token) {
        console.error('CSRF token not found');
        return null;
    }
    return token.getAttribute('content');
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

document.addEventListener('DOMContentLoaded', () => {
  const registerForm = document.querySelector('#register-form'); 
  if (registerForm) {
    registerForm.addEventListener('submit', (e) => {
      e.preventDefault();

      const pw = registerForm.querySelector('#password')?.value || '';
      const pc = registerForm.querySelector('#password_confirmation')?.value || '';

      const fail = (msg) => { showAlert('error', msg); };

      if (pw.length < 8) { e.preventDefault(); fail('La password deve contenere almeno 8 caratteri.'); return; }
      if (!/[A-Z]/.test(pw)) { e.preventDefault(); fail('La password deve contenere almeno una lettera maiuscola.'); return; }
      if (!/[0-9]/.test(pw)) { e.preventDefault(); fail('La password deve contenere almeno un numero.'); return; }
      if (!/[!@#$%^&*(),.?":{}|<>]/.test(pw)) { e.preventDefault(); fail('La password deve contenere almeno un carattere speciale.'); return; }
      if (pw !== pc) { e.preventDefault(); fail('Le password non coincidono.'); return; }

      registerForm.submit();
    });
  }
});


