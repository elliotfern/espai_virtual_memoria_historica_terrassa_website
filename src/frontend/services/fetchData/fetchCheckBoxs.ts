interface Partido {
  id: number;
  partit_politic?: string;
  sindicat?: string;
}

export async function fetchCheckBoxs(elementId: string, apiUrl: string, nodeElement: string, defaultCheckedId?: number): Promise<void> {
  try {
    const devDirectory = `https://${window.location.hostname}`;
    const urlAjax = `${devDirectory}/api/auxiliars/get/${apiUrl}`;

    const response = await fetch(urlAjax);
    const data: Partido[] = await response.json();

    renderCheckboxes(data, elementId, nodeElement, defaultCheckedId);
  } catch (error) {
    console.error(`Error al obtener los datos para ${apiUrl}:`, error);
  }
}

function renderCheckboxes(data: Partido[], elementId: string, nodeElement: string, defaultCheckedId?: number): void {
  const container = document.getElementById(nodeElement);
  if (!container) {
    console.error(`Contenedor con id '${nodeElement}' no encontrado.`);
    return;
  }

  container.innerHTML = '';

  // Procesar elementId para obtener array de IDs seleccionados
  let selectedPartits: number[] = [];

  if (elementId) {
    selectedPartits = elementId
      .replace(/[{}]/g, '')
      .split(',')
      .map((id) => parseInt(id, 10))
      .filter((num) => !isNaN(num));
  }
  // Si selectedPartits está vacío o contiene solo valores falsy (0, null, undefined)
  // entonces usamos defaultCheckedId como único seleccionado
  const hasValidSelection = selectedPartits.some((id) => id && id !== 0);
  if (!hasValidSelection && defaultCheckedId !== undefined) {
    selectedPartits = [defaultCheckedId];
  }

  let checkboxName = '';
  let nomElement = '';

  if (nodeElement === 'partit_politic') {
    checkboxName = 'partido';
    nomElement = 'partit_politic';
  } else if (nodeElement === 'sindicat') {
    checkboxName = 'sindicat';
    nomElement = 'sindicat';
  }

  data.forEach((partido) => {
    const checkbox = document.createElement('input');
    checkbox.type = 'checkbox';
    checkbox.id = `${checkboxName}-${partido.id}`;
    checkbox.name = checkboxName;
    checkbox.value = String(partido.id);
    checkbox.className = 'form-check-input me-2';

    if (selectedPartits.includes(partido.id)) {
      checkbox.checked = true;
    }

    const label = document.createElement('label');
    label.htmlFor = checkbox.id;
    label.textContent = String(partido[nomElement as keyof Partido] ?? '');
    label.className = 'form-check-label me-4';

    const div = document.createElement('div');
    div.className = 'd-flex align-items-center';
    div.appendChild(checkbox);
    div.appendChild(label);

    container.appendChild(div);
  });
}
