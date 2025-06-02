export function deleteCookie(name: string, path?: string, domain?: string): void {
  if (getCookie(name)) {
    document.cookie = name + '=' + (path ? ';path=' + path : '') + (domain ? ';domain=' + domain : '') + ';expires=Thu, 01 Jan 1970 00:00:01 GMT';
  }
}

export function setCookie(name: string, value: string, days: number) {
  const expires = new Date(Date.now() + days * 864e5).toUTCString();
  document.cookie = `${name}=${value}; expires=${expires}; path=/`;
}

export function getCookie(name: string): string | null {
  return (
    document.cookie
      .split('; ')
      .find((row) => row.startsWith(name + '='))
      ?.split('=')[1] ?? null
  );
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
