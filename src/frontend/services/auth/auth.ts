interface LoginResponse {
  status: string;
  idUser: string;
  token?: string; // el token puede estar o no, dependiendo de la respuesta
}

export async function login(userName: string, password: string): Promise<void> {
  const devDirectory = `https://${window.location.hostname}`;
  const urlAjax = `${devDirectory}/api/auth/login`;

  localStorage.removeItem('isAdmin');
  localStorage.removeItem('isAutor');

  try {
    const response = await fetch(urlAjax, {
      method: 'POST',
      headers: {
        'Content-Type': 'application/json',
      },
      body: JSON.stringify({ userName, password }),
    });

    const data: LoginResponse = await response.json();

    if (data.status === 'success') {
      // Mostrar mensaje de éxito
      const loginMessageOk = document.querySelector('#loginMessageOk') as HTMLElement;
      const loginMessageErr = document.querySelector('#loginMessageErr') as HTMLElement;

      if (loginMessageOk && loginMessageErr) {
        loginMessageOk.style.display = 'block';
        loginMessageErr.style.display = 'none';
      }

      // Redirigir después de un pequeño retraso
      setTimeout(() => {
        window.location.href = `${devDirectory}/gestio/admin`;
      }, 1300);
    } else {
      // Mostrar mensaje de error
      const loginMessageOk = document.querySelector('#loginMessageOk') as HTMLElement;
      const loginMessageErr = document.querySelector('#loginMessageErr') as HTMLElement;

      if (loginMessageOk && loginMessageErr) {
        loginMessageOk.style.display = 'none';
        loginMessageErr.style.display = 'block';
      }
    }
  } catch (error) {
    console.error('Error al iniciar sesión:', error);
  }
}
