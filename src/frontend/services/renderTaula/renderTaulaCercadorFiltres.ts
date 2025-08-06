type Column<T> = {
  header: string;
  field: keyof T;
  render?: (value: T[keyof T], row: T) => string;
};

type RenderTableOptions<T> = {
  url: string;
  columns: Column<T>[];
  containerId: string;
  rowsPerPage?: number;
  filterKeys?: (keyof T)[];
  filterByField?: keyof T;
  showSearch?: boolean;
  showPagination?: boolean;
  initialPage?: number;
  initialSearch?: string;
  initialFilterValue?: T[keyof T] | null;
};

export async function renderTaulaCercadorFiltres<T>({ url, columns, containerId, rowsPerPage = 15, filterKeys = [], filterByField, showSearch = true, showPagination = true, initialPage = 1, initialSearch = '', initialFilterValue = null }: RenderTableOptions<T>): Promise<{ page: number; search: string; filter: T[keyof T] | null }> {
  const container = document.getElementById(containerId);
  if (!container) {
    console.error(`Contenedor #${containerId} no encontrado`);
    return { page: initialPage, search: initialSearch, filter: initialFilterValue };
  }

  const response = await fetch(url);
  const result = await response.json();

  if (result.status === 'error') {
    container.innerHTML = `<div class="alert alert-info">${result.message || 'No hi ha dades.'}</div>`;
    return { page: initialPage, search: initialSearch, filter: initialFilterValue };
  }

  let data: T[];
  if (Array.isArray(result)) data = result;
  else if (Array.isArray(result.data)) data = result.data;
  else if (result.data) data = [result.data];
  else {
    container.innerHTML = `<div class="alert alert-info">No s'han trobat dades vàlides.</div>`;
    return { page: initialPage, search: initialSearch, filter: initialFilterValue };
  }

  let currentPage = initialPage;
  let filteredData = [...data];
  let activeButtonFilter: T[keyof T] | null = initialFilterValue;

  const searchInput = document.createElement('input');
  searchInput.style.marginBottom = '15px';
  searchInput.placeholder = 'Cercar...';
  searchInput.value = initialSearch;

  const buttonContainer = document.createElement('div');
  buttonContainer.className = 'filter-buttons';

  const table = document.createElement('table');
  table.classList.add('table', 'table-striped');
  const thead = document.createElement('thead');
  thead.classList.add('table-primary');
  const tbody = document.createElement('tbody');
  const pagination = document.createElement('div');
  pagination.id = 'pagination';

  const totalRecords = document.createElement('div');
  totalRecords.className = 'total-records';
  totalRecords.style.marginTop = '15px';
  totalRecords.style.fontSize = '12px';

  table.append(thead, tbody);

  const normalizeText = (text: string) =>
    text
      .normalize('NFD')
      .replace(/[\u0300-\u036f]/g, '')
      .toLowerCase();

  function applyFilters() {
    const search = normalizeText(searchInput.value);
    filteredData = data.filter((row) => !activeButtonFilter || row[filterByField!] === activeButtonFilter).filter((row) => (search.length === 0 ? true : filterKeys.some((key) => normalizeText(String(row[key])).includes(search))));

    const totalPages = Math.ceil(filteredData.length / rowsPerPage);
    if (currentPage > totalPages) currentPage = Math.max(1, totalPages);

    renderTable();
  }

  function renderFilterButtons() {
    if (!filterByField) return;

    const uniqueValues = Array.from(new Set(data.map((row) => row[filterByField]))).filter(Boolean) as T[keyof T][];
    uniqueValues.sort((a, b) => String(a).localeCompare(String(b), 'ca', { sensitivity: 'base' }));

    buttonContainer.innerHTML = '';

    const allButton = document.createElement('button');
    allButton.textContent = 'Tots';
    allButton.className = 'filter-btn';
    allButton.onclick = () => {
      activeButtonFilter = null;
      updateActiveButton(allButton);
      applyFilters();
    };
    buttonContainer.appendChild(allButton);

    uniqueValues.forEach((value) => {
      const button = document.createElement('button');
      button.textContent = String(value);
      button.className = 'filter-btn';
      button.onclick = () => {
        activeButtonFilter = value;
        updateActiveButton(button);
        applyFilters();
      };
      buttonContainer.appendChild(button);

      if (activeButtonFilter && activeButtonFilter === value) {
        updateActiveButton(button);
      }
    });

    if (!activeButtonFilter) updateActiveButton(allButton);
  }

  function updateActiveButton(activeButton: HTMLButtonElement) {
    const buttons = buttonContainer.querySelectorAll('.filter-btn');
    buttons.forEach((btn) => btn.classList.remove('active'));
    activeButton.classList.add('active');
  }

  function renderTable() {
    thead.innerHTML = `<tr>${columns.map((col) => `<th>${col.header}</th>`).join('')}</tr>`;

    const start = (currentPage - 1) * rowsPerPage;
    const end = start + rowsPerPage;
    const rowsToShow = filteredData.slice(start, end);

    tbody.innerHTML = rowsToShow
      .map(
        (row) =>
          `<tr>${columns
            .map((col) => {
              const value = row[col.field];
              return `<td>${col.render ? col.render(value, row) : value}</td>`;
            })
            .join('')}</tr>`
      )
      .join('');

    if (showPagination) {
      const totalPages = Math.ceil(filteredData.length / rowsPerPage);
      pagination.innerHTML = '';
      for (let i = 1; i <= totalPages; i++) {
        const link = document.createElement('a');
        link.textContent = i.toString();
        link.href = '#';
        link.className = 'pagination-link' + (i === currentPage ? ' current-page' : '');
        link.onclick = (e) => {
          e.preventDefault();
          currentPage = i;
          renderTable();
        };
        pagination.appendChild(link);
      }
    }

    totalRecords.textContent = `Número total de registres: ${filteredData.length}`;
  }

  container.innerHTML = '';

  if (showSearch) {
    searchInput.addEventListener('input', () => {
      currentPage = 1;
      applyFilters();
    });
    container.appendChild(searchInput);
  }

  if (filterByField) {
    container.appendChild(buttonContainer);
    renderFilterButtons();
  }

  container.appendChild(table);
  container.appendChild(totalRecords);

  if (showPagination) {
    container.appendChild(pagination);
  }

  // Al final de renderTaulaCercadorFiltres
  applyFilters();

  // <<-- Cambiar el return anterior por un getter dinámico >>
  return new Promise((resolve) => {
    // Esperar un ciclo de renderizado para devolver el estado final
    setTimeout(() => {
      resolve({
        page: currentPage,
        search: searchInput.value,
        filter: activeButtonFilter,
      });
    }, 0);
  });
}
