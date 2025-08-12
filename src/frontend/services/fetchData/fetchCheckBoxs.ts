interface Partido {
  id: number;
  partit_politic?: string;
  sindicat?: string;
  sigles?: string | null; // nuevo en la API
}

interface ApiWrapper<T> {
  status?: string;
  message?: string;
  errors?: unknown[];
  data?: T[];
}

export async function fetchCheckBoxs(apiUrl: string, nodeElement: 'partit_politic' | 'sindicat', defaultCheckedId?: number, elementId?: string): Promise<void> {
  try {
    // Mejor que hostname: respeta http/https y el puerto
    const base = window.location.origin;
    const urlAjax = `${base}/api/auxiliars/get/${apiUrl}`;

    const response = await fetch(urlAjax);
    if (!response.ok) throw new Error(`HTTP ${response.status}`);

    const json = (await response.json()) as Partido[] | ApiWrapper<Partido>;

    // Soporta tanto la respuesta antigua (array) como la nueva ({data: [...]})
    const data = Array.isArray(json) ? json : json?.data ?? [];
    if (!Array.isArray(data)) throw new Error('Formato de respuesta inesperado');

    renderCheckboxes(data, nodeElement, defaultCheckedId, elementId);
  } catch (error) {
    console.error(`Error al obtener los datos para ${apiUrl}:`, error);
  }
}

function renderCheckboxes(data: Partido[], nodeElement: 'partit_politic' | 'sindicat', defaultCheckedId?: number, elementId?: string): void {
  const container = document.getElementById(nodeElement);
  if (!container) {
    console.error(`Contenedor con id '${nodeElement}' no encontrado.`);
    return;
  }

  container.innerHTML = '';

  // IDs seleccionados de entrada
  let selectedPartits: number[] = [];
  if (elementId) {
    selectedPartits = elementId
      .replace(/[{}]/g, '')
      .split(',')
      .map((id) => parseInt(id, 10))
      .filter((num) => !isNaN(num));
  }

  // Fallback al defaultCheckedId si no hay selección válida
  const hasValidSelection = selectedPartits.some((id) => id && id !== 0);
  if (!hasValidSelection && typeof defaultCheckedId === 'number') {
    selectedPartits = [defaultCheckedId];
  }

  const checkboxName = nodeElement === 'partit_politic' ? 'partido' : 'sindicat';
  const nomElement = nodeElement; // clave a leer en cada item

  for (const item of data) {
    const checkbox = document.createElement('input');
    checkbox.type = 'checkbox';
    checkbox.id = `${checkboxName}-${item.id}`;
    checkbox.name = checkboxName;
    checkbox.value = String(item.id);
    checkbox.className = 'form-check-input me-2';
    checkbox.checked = selectedPartits.includes(item.id);

    const label = document.createElement('label');
    label.htmlFor = checkbox.id;

    const nombre = (item[nomElement as keyof Partido] as string | undefined) ?? '';
    const sigles = item.sigles ? ` (${item.sigles})` : '';

    label.textContent = `${nombre}${sigles}`;
    label.className = 'form-check-label me-4';

    const div = document.createElement('div');
    div.className = 'd-flex align-items-center';
    div.appendChild(checkbox);
    div.appendChild(label);

    container.appendChild(div);
  }
}
