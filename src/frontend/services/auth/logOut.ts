export async function logout() {
  try {
    // Llamar al backend para realizar el logout
    const url = `https://${window.location.host}/api/auth/get/logOut`;

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
      localStorage.removeItem('isLogged');

      sessionStorage.clear();

      // Redirige al usuario a la página inici
      window.location.href = 'https://memoriaterrassa.cat/inici';
    } else {
      console.error('Error al hacer logout:', data);
    }
  } catch (error) {
    console.error('Error en la llamada al backend:', error);
  }
}
