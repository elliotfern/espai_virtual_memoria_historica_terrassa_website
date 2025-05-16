export async function nameUser(): Promise<void> {
  const devDirectory = `https://${window.location.hostname}`;
  const urlAjax = `${devDirectory}/api/auth/get/nomUsuari`;

  const userDiv = document.getElementById('userDiv');
  if (userDiv) {
    try {
      const res = await fetch(urlAjax, {
        credentials: 'include', // importante si la cookie "token" es HttpOnly
      });

      if (!res.ok) {
        console.warn('No se pudo obtener el usuario');
        return;
      }

      const data = await res.json();

      const welcomeMessage = data.username ? `Hola, ${data.username}` : 'Usuari desconegut';
      userDiv.textContent = welcomeMessage;
    } catch (err) {
      console.error('Error al obtener el usuario:', err);
    }
  } else {
    console.warn('Elemento #userDiv no encontrado');
  }
}
