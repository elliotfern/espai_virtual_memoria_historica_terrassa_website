export async function fetchDataGet<T>(relativeUrl: string, url?: boolean): Promise<T | null> {
  if (url) {
    url = true;
  }

  try {
    const response = await fetch(relativeUrl, {
      method: 'GET',
      headers: { 'Content-Type': 'application/json' },
    });
    if (!response.ok) {
      console.error('Error en la respuesta HTTP', response.status);
      return null;
    }
    const result = await response.json();
    return result as T;
  } catch (error) {
    console.error('Error en fetchDataGet:', error);
    return null;
  }
}
