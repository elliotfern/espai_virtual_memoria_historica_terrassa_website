export async function fetchData<T>(url: string): Promise<T> {
  const response = await fetch(url, {
    method: 'GET',
    headers: {
      'Cache-Control': 'no-store, no-cache, must-revalidate',
      Pragma: 'no-cache',
    },
  });

  if (!response.ok) {
    throw new Error('Error en la llamada a la API');
  }

  return response.json(); // El tipo de respuesta será inferido como T
}
