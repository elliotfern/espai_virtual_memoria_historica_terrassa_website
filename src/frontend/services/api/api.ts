export async function fetchData<T>(url: string): Promise<T> {
  const response = await fetch(url, {
    method: 'GET',
    headers: {
      // Aquí pueden ir los headers si los necesitas
    },
  });

  if (!response.ok) {
    throw new Error('Error en la llamada a la API');
  }

  return response.json(); // El tipo de respuesta será inferido como T
}
