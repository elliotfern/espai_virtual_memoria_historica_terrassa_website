export function renderTaula(
  data: Array<{ [key: string]: unknown }>,
  container: HTMLElement,
  options?: {
    excludeFields?: string[];
    tableClass?: string;
    headersMap?: Record<string, string>;
    columnRenderers?: Record<string, (value: unknown, row: Record<string, unknown>) => string>;
  }
): void {
  container.innerHTML = ''; // Limpiar contenido anterior

  if (!data || data.length === 0) {
    container.innerHTML = '<p>No hi ha dades disponibles.</p>';
    return;
  }

  const excludeFields = options?.excludeFields ?? [];
  const headersMap = options?.headersMap ?? {};
  const columnRenderers = options?.columnRenderers ?? {};
  const tableClass = options?.tableClass ?? 'table table-bordered';

  const columnas = Object.keys(data[0]).filter((key) => !excludeFields.includes(key));

  const table = document.createElement('table');
  table.className = tableClass;

  const thead = document.createElement('thead');
  thead.className = 'table-dark';
  const headerRow = document.createElement('tr');

  columnas.forEach((col) => {
    const th = document.createElement('th');
    th.textContent = headersMap[col] || col;
    headerRow.appendChild(th);
  });
  thead.appendChild(headerRow);
  table.appendChild(thead);

  const tbody = document.createElement('tbody');
  data.forEach((item) => {
    const row = document.createElement('tr');
    columnas.forEach((col) => {
      const td = document.createElement('td');
      const value = item[col];
      if (columnRenderers[col]) {
        td.innerHTML = columnRenderers[col](value, item);
      } else {
        td.textContent = String(value ?? '');
      }
      row.appendChild(td);
    });
    tbody.appendChild(row);
  });

  table.appendChild(tbody);
  container.appendChild(table);
}
