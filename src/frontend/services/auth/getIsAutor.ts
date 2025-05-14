// Función para obtener el estado de admin, usando localStorage para evitar llamadas repetidas
export async function getIsAutor() {
  // Comprobamos si ya hemos guardado el valor en localStorage
  const isAutor = localStorage.getItem('isAutor');

  if (isAutor !== null) {
    // Si ya tenemos el valor, lo retornamos
    return JSON.parse(isAutor); // Devolvemos el valor guardado como un booleano
  }

  // Si no lo tenemos, hacemos la llamada a la API
  const isAutorFromApi = await isAutorUser();

  // Guardamos el valor en localStorage para futuras consultas
  localStorage.setItem('isAutor', JSON.stringify(isAutorFromApi));

  // Retornamos el valor obtenido
  return isAutorFromApi;
}

export async function isAutorUser(): Promise<boolean> {
  try {
    // Cridem a l'endpoint que verifica si l'usuari és admin
    const url = `https://${window.location.host}/api/auth/isAutor`;
    const response = await fetch(url, {
      method: 'GET', // o 'POST' según el caso
      credentials: 'include', // Necessari per enviar les cookies amb la petició
    });

    if (!response.ok) {
      throw new Error('No es pot verificar si és admin');
    }

    const data = await response.json();
    return data.isAutor;
  } catch (error) {
    console.error('Error al verificar admin:', error);
    return false;
  }
}
