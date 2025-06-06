export async function fetchDataGet<T>(relativeUrl: string, url?: boolean): Promise<T | null> {
  let fullUrl = '';
  if (!url) {
    const devDirectory = `https://${window.location.hostname}`;
    fullUrl = `${devDirectory}${relativeUrl}`;
  } else {
    fullUrl = `${relativeUrl}`;
  }

  try {
    const response = await fetch(fullUrl, {
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
