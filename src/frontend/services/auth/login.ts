interface LoginResponse {
  status: string;
  idUser: string;
  token?: string;
  message: string;
}

export function loginPage() {
  const btnLogin = document.querySelector('#btnLogin') as HTMLButtonElement;

  // Mostrar mensaje de error
  const loginMessageErr = document.querySelector('#loginMessageErr') as HTMLElement;

  const usernameInput = document.querySelector('#username') as HTMLInputElement;
  const passwordInput = document.querySelector('#password') as HTMLInputElement;

  const usernameFeedback = document.querySelector('#usernameFeedback') as HTMLElement;
  const passwordFeedback = document.querySelector('#passwordFeedback') as HTMLElement;

  btnLogin?.addEventListener('click', (event: Event) => {
    event.preventDefault();

    const userName = usernameInput.value.trim();
    const password = passwordInput.value.trim();

    // Reset styles and feedback
    resetValidation(usernameInput, usernameFeedback);
    resetValidation(passwordInput, passwordFeedback);
    loginMessageErr.style.display = 'none';
    loginMessageErr.innerHTML = '';

    const errors: string[] = [];

    if (!userName) {
      showValidationError(usernameInput, usernameFeedback, 'El correu electrònic és obligatori.');
      errors.push('Introdueix el correu electrònic.');
    }

    if (!password) {
      showValidationError(passwordInput, passwordFeedback, 'La contrasenya és obligatòria.');
      errors.push('Introdueix la contrasenya.');
    }

    if (errors.length > 0) {
      loginMessageErr.innerHTML = `<ul class="mb-0">${errors.map((err) => `<li>${err}</li>`).join('')}</ul>`;
      loginMessageErr.style.display = 'block';
      return;
    }

    // Si no hay errores, se intenta hacer login
    login(userName, password);
  });
}

export async function login(userName: string, password: string): Promise<void> {
  const devDirectory = `https://${window.location.hostname}`;
  const urlAjax = `${devDirectory}/api/auth/post/login`;

  localStorage.removeItem('isAdmin');
  localStorage.removeItem('isAutor');
  localStorage.removeItem('isLogged');

  // Obtener elementos para mostrar mensajes
  const loginMessageOk = document.querySelector('#loginMessageOk') as HTMLElement;
  const loginMessageErr = document.querySelector('#loginMessageErr') as HTMLElement;

  // Reiniciar visibilidad y contenido
  loginMessageOk.style.display = 'none';
  loginMessageOk.innerHTML = '';
  loginMessageErr.style.display = 'none';
  loginMessageErr.innerHTML = '';

  try {
    const response = await fetch(urlAjax, {
      method: 'POST',
      headers: {
        'Content-Type': 'application/json',
      },
      body: JSON.stringify({ userName, password }),
    });

    const data: LoginResponse = await response.json();

    if (response.ok && data.status === 'success') {
      // ✅ Login exitoso
      loginMessageOk.innerHTML = data.message || 'Has iniciat sessió correctament. Redirigint...';
      loginMessageOk.style.display = 'block';

      setTimeout(() => {
        window.location.href = `${devDirectory}/gestio/admin`;
      }, 1300);
    } else {
      // ❌ Login fallido: mostrar mensaje de error (puedes ajustar según el contenido del backend)
      const message = data?.status === 'error' ? 'Credencials incorrectes. Si us plau, torna-ho a intentar.' : 'Error desconegut en iniciar sessió.';

      loginMessageErr.innerHTML = message;
      loginMessageErr.style.display = 'block';
    }
  } catch (error) {
    console.error('Error al iniciar sessió:', error);

    // ❌ Error de red o excepción
    loginMessageErr.innerHTML = "No s'ha pogut connectar amb el servidor. Comprova la teva connexió.";
    loginMessageErr.style.display = 'block';
  }
}

function showValidationError(input: HTMLInputElement, feedback: HTMLElement, message: string) {
  input.classList.add('is-invalid');
  feedback.textContent = message;
}

function resetValidation(input: HTMLInputElement, feedback: HTMLElement) {
  input.classList.remove('is-invalid');
  feedback.textContent = '';
}
