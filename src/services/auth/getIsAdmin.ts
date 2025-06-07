// Función para obtener el estado de admin, usando localStorage para evitar llamadas repetidas
export async function getIsAdmin() {
  // Comprobamos si ya hemos guardado el valor en localStorage
  const isAdmin = localStorage.getItem('isAdmin');

  if (isAdmin !== null) {
    // Si ya tenemos el valor, lo retornamos
    return JSON.parse(isAdmin); // Devolvemos el valor guardado como un booleano
  }

  // Si no lo tenemos, hacemos la llamada a la API
  const isAdminFromApi = await isAdminUser();

  // Guardamos el valor en localStorage para futuras consultas
  localStorage.setItem('isAdmin', JSON.stringify(isAdminFromApi));

  // Retornamos el valor obtenido
  return isAdminFromApi;
}

export async function isAdminUser(): Promise<boolean> {
  try {
    // Cridem a l'endpoint que verifica si l'usuari és admin
    const url = `https://${window.location.host}/api/auth/get/isAdmin`;
    const response = await fetch(url, {
      method: 'GET', // o 'POST' según el caso
      credentials: 'include', // Necessari per enviar les cookies amb la petició
    });

    if (!response.ok) {
      throw new Error('No es pot verificar si és admin');
    }

    const data = await response.json();
    return data.isAdmin;
  } catch (error) {
    console.error('Error al verificar admin:', error);
    return false;
  }
}
