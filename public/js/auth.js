/* ============================================================
   SIGPA — Auth scripts
   Cubre: login, forgot-password, reset-password
   ============================================================ */

/**
 * Alterna la visibilidad de un campo de contraseña y actualiza el ícono.
 *
 * @param {string} inputId - ID del input de tipo password
 * @param {string} iconId  - ID del elemento SVG del ícono ojo
 */
function togglePass(inputId, iconId) {
  const input = document.getElementById(inputId);
  const icon  = document.getElementById(iconId);
  if (!input || !icon) return;

  if (input.type === 'password') {
    input.type = 'text';
    icon.innerHTML = `<path d="M17.94 17.94A10.07 10.07 0 0112 20c-7 0-11-8-11-8a18.45 18.45 0 015.06-5.94M9.9 4.24A9.12 9.12 0 0112 4c7 0 11 8 11 8a18.5 18.5 0 01-2.16 3.19M1 1l22 22" stroke="currentColor" stroke-width="1.8" stroke-linecap="round"/>`;
  } else {
    input.type = 'password';
    icon.innerHTML = `<path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z" stroke="currentColor" stroke-width="1.8"/><circle cx="12" cy="12" r="3" stroke="currentColor" stroke-width="1.8"/>`;
  }
}

/**
 * Evalúa la fortaleza de una contraseña y actualiza la barra indicadora.
 * Requiere los elementos #strengthBar y #strengthLabel en el DOM.
 *
 * @param {string} val - Valor actual del campo de contraseña
 */
function checkStrength(val) {
  const bar   = document.getElementById('strengthBar');
  const label = document.getElementById('strengthLabel');
  if (!bar || !label) return;

  let score = 0;
  if (val.length >= 8)          score++;
  if (/[A-Z]/.test(val))        score++;
  if (/[0-9]/.test(val))        score++;
  if (/[^A-Za-z0-9]/.test(val)) score++;

  const levels = [
    { w: '0%',   color: '#e2e8f0', text: '' },
    { w: '25%',  color: '#E0176C', text: 'Débil' },
    { w: '50%',  color: '#F76B1C', text: 'Regular' },
    { w: '75%',  color: '#1A4FD6', text: 'Buena' },
    { w: '100%', color: '#00B67A', text: 'Fuerte ✓' },
  ];
  const level = levels[score];
  bar.style.width      = level.w;
  bar.style.background = level.color;
  label.textContent    = level.text;
  label.style.color    = level.color;
}

document.addEventListener('DOMContentLoaded', () => {

  /**
   * Muestra el estado de carga al enviar cualquier formulario auth.
   * El botón de envío debe tener id="submitBtn".
   * Si existen #btnText y #btnSpinner se intercambian; si no, se deshabilita el botón.
   */
  const form    = document.querySelector('form');
  const btn     = document.getElementById('submitBtn');
  const txt     = document.getElementById('btnText');
  const spinner = document.getElementById('btnSpinner');

  if (form && btn) {
    form.addEventListener('submit', () => {
      btn.disabled = true;
      if (txt && spinner) {
        txt.classList.add('hidden');
        spinner.classList.remove('hidden');
        spinner.classList.add('flex');
      }
    });
  }

});
