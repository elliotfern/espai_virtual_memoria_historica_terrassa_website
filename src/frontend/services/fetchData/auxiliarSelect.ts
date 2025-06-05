export async function auxiliarSelect(idAux: number | null | undefined, api: string, elementId: string, valorText: string, fallbackValue?: string): Promise<void> {
  const devDirectory = `https://${window.location.hostname}`;
  const urlAjax = `${devDirectory}/api/auxiliars/get/${api}`;

  try {
    const response = await fetch(urlAjax, {
      method: 'GET',
      headers: {
        'Content-Type': 'application/json',
      },
    });

    if (!response.ok) {
      throw new Error('Error en la solicitud');
    }

    type Item = { id: number; [key: string]: unknown };

    const jsonResponse = await response.json();

    // Detectamos si viene dentro de `data` o es el array directamente
    const data: Item[] = Array.isArray(jsonResponse.data) ? jsonResponse.data : jsonResponse;

    const selectElement = document.getElementById(elementId) as HTMLSelectElement;
    if (!selectElement) return;

    selectElement.innerHTML = '';

    const defaultOption = document.createElement('option');
    defaultOption.text = 'Selecciona una opció:';
    defaultOption.value = '';
    selectElement.appendChild(defaultOption);

    data.forEach((item) => {
      const option = document.createElement('option');
      option.value = String(item.id);

      const text = item[valorText];
      option.text = typeof text === 'string' ? text : String(text ?? '');

      selectElement.appendChild(option);
    });

    // Aquí usamos fallback si idAux es null, undefined o 0
    if (idAux !== null && idAux !== undefined && idAux !== 0) {
      selectElement.value = idAux.toString();
    } else if (fallbackValue !== undefined) {
      selectElement.value = fallbackValue;
    }
  } catch (error) {
    console.error('Error al cargar las opciones:', error);
  }
}
