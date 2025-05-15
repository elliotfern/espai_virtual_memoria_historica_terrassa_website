export function deleteCookie(name: string, path?: string, domain?: string): void {
  if (getCookie(name)) {
    document.cookie = name + '=' + (path ? ';path=' + path : '') + (domain ? ';domain=' + domain : '') + ';expires=Thu, 01 Jan 1970 00:00:01 GMT';
  }
}

export function getCookie(name: string): string | undefined {
  const value = '; ' + document.cookie;
  const parts = value.split('; ' + name + '=');
  if (parts.length === 2) return parts.pop()?.split(';').shift();
  return undefined; // Asegúrate de manejar el caso donde no se encuentra la cookie
}

export function deleteAllCookies(): void {
  const cookies = document.cookie.split('; ');

  // Eliminar cookies en la ruta actual y todas las subrutas
  cookies.forEach((cookie) => {
    const eqPos = cookie.indexOf('=');
    const name = eqPos > -1 ? cookie.substr(0, eqPos) : cookie;
    const pathParts = location.pathname.split('/');
    for (let i = 0; i < pathParts.length; i++) {
      const path = pathParts.slice(0, i + 1).join('/') || '/';
      deleteCookie(name, path);
      deleteCookie(name, path, window.location.hostname);
      if (window.location.hostname.includes('.')) {
        deleteCookie(name, path, '.' + window.location.hostname);
      }
    }
  });

  // Eliminar cookies en el dominio raíz
  cookies.forEach((cookie) => {
    const eqPos = cookie.indexOf('=');
    const name = eqPos > -1 ? cookie.substr(0, eqPos) : cookie;
    deleteCookie(name, '/');
    deleteCookie(name, '/', window.location.hostname);
    if (window.location.hostname.includes('.')) {
      deleteCookie(name, '/', '.' + window.location.hostname);
    }
  });
}

export async function logout() {
  try {
    // Llamar al backend para realizar el logout
    const url = `https://${window.location.host}/api/auth/logout`;

    const response = await fetch(url, {
      method: 'GET',
      headers: {
        'Content-Type': 'application/json',
      },
    });
    const data = await response.json();

    if (data.message === 'OK') {
      // Elimina la clave isAdmin en localStorage
      localStorage.removeItem('isAdmin');
      localStorage.removeItem('isAutor');
      sessionStorage.clear();

      // Redirige al usuario a la página "elliot.cat"
      window.location.href = 'https://memoriaterrassa.cat';
    } else {
      console.error('Error al hacer logout:', data);
    }
  } catch (error) {
    console.error('Error en la llamada al backend:', error);
  }
}
