import { logout } from '../../services/cookies/cookiesUtils';

export async function nameUser(): Promise<void> {
  const devDirectory = `https://${window.location.hostname}`;
  const urlAjax = `${devDirectory}/api/auth/get/nomUsuari`;

  const userLoggedDiv = document.getElementById('userLogged');
  if (!userLoggedDiv) {
    console.warn('Elemento #userLogged no encontrado');
    return;
  }

  if (userLoggedDiv) {
    try {
      const res = await fetch(urlAjax, {
        credentials: 'include', // importante si la cookie "token" es HttpOnly
      });

      if (!res.ok) {
        console.warn('No se pudo obtener el usuario');
        return;
      }

      const data = await res.json();

      if (!data.username || !data.avatar) {
        console.warn('Datos incompletos del usuario');
        return;
      }

      // HTML dinámico con avatar, nombre y botón logout
      userLoggedDiv.innerHTML = `
      <div class="d-flex align-items-center gap-2 flex-wrap">
        <img src="https://memoriaterrassa.cat/public/img/usuaris-avatars/${data.avatar}.jpg" alt="Avatar de ${data.username}" class="rounded-circle" width="40" height="40">
        <span class="fw-bold text-white">${data.username}</span>
       <button id="btnSortir" class="btn btn-sm btn-outline-light">Sortir</button>
      </div>
    `;

      // Enganchar el evento DESPUÉS de insertar el HTML
      const btnLogout = document.getElementById('btnSortir') as HTMLButtonElement | null;
      if (btnLogout) {
        btnLogout.addEventListener('click', async (e) => {
          e.preventDefault();
          logout();
        });
      }
    } catch (err) {
      console.error('Error al obtener el usuario:', err);
    }
  } else {
    console.warn('Elemento #userDiv no encontrado');
  }
}
