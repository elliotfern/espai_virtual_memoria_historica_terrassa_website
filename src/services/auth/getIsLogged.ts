// Función para obtener el estado de admin, usando localStorage para evitar llamadas repetidas
export async function getIsLogged() {
  // Comprobamos si ya hemos guardado el valor en localStorage
  const isLogged = localStorage.getItem('isLogged');

  if (isLogged !== null) {
    // Si ya tenemos el valor, lo retornamos
    return JSON.parse(isLogged); // Devolvemos el valor guardado como un booleano
  }

  // Si no lo tenemos, hacemos la llamada a la API
  const isLoggedFromApi = await isLoggedUser();

  // Guardamos el valor en localStorage para futuras consultas
  localStorage.setItem('isLogged', JSON.stringify(isLoggedFromApi));

  // Retornamos el valor obtenido
  return isLoggedFromApi;
}

export async function isLoggedUser(): Promise<boolean> {
  try {
    // Cridem a l'endpoint que verifica si l'usuari és logged
    const url = `https://${window.location.host}/api/auth/get/isLogged`;
    const response = await fetch(url, {
      method: 'GET', // o 'POST' según el caso
      credentials: 'include', // Necessari per enviar les cookies amb la petició
    });

    if (!response.ok) {
      throw new Error('No es pot verificar si és usuari logged');
    }

    const data = await response.json();
    return data.isLogged;
  } catch (error) {
    console.error('Error al verificar logged:', error);
    return false;
  }
}
