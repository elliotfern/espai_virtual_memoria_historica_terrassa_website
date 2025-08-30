import { fetchData } from '../../services/api/api';
import { Represeliat } from '../../types/types';
import { categoriesRepressio } from './categoriesRepressio';
import { traduirCategoriesRepressio } from './traduirCategoriesRepressio';
import { getIsAdmin } from '../../services/auth/getIsAdmin';
import { getIsAutor } from '../../services/auth/getIsAutor';
import { getIsLogged } from '../../services/auth/getIsLogged';
import { formatDatesForm } from '../../services/formatDates/dates';
import { registerDeleteCallback } from '../../services/fetchData/handleDelete';
import { initDeleteHandlers } from '../../services/fetchData/handleDelete';
import { DOMAIN_WEB } from '../../config/constants';

export async function cargarTabla(pag: string, context: number, completat: number | null = null) {
  const devDirectory = DOMAIN_WEB;

  const isAdmin = await getIsAdmin();
  const isAutor = await getIsAutor();
  const isLogged = await getIsLogged();
  const colectiusRepressio = await categoriesRepressio('ca');

  // --- Helpers de búsqueda (NUEVO) ---
  function stripDiacritics(s: string): string {
    return s.normalize('NFD').replace(/[\u0300-\u036f]/g, '');
  }
  function normalizeText(s: string): string {
    return stripDiacritics(s)
      .toLowerCase()
      .replace(/[^a-z0-9\s]/g, ' ')
      .replace(/\s+/g, ' ')
      .trim();
  }
  function buildNameVariants(row: Represeliat): string[] {
    const nom = row.nom ?? '';
    const c1 = row.cognom1 ?? '';
    const c2 = row.cognom2 ?? '';
    return [
      normalizeText(`${c1} ${c2} ${nom}`), // "cognom1 cognom2 nom"
      normalizeText(`${nom} ${c1} ${c2}`), // "nom cognom1 cognom2"
    ];
  }
  let nameIndex: Record<number, string[]> = {};
  function reindexNames(list: Represeliat[]): void {
    nameIndex = {};
    for (const row of list) nameIndex[row.id] = buildNameVariants(row);
  }
  function matchesName(row: Represeliat, qNorm: string): boolean {
    const variants = nameIndex[row.id] ?? buildNameVariants(row);
    const tokens = qNorm.split(' ').filter(Boolean);
    if (tokens.length === 0) return true;
    // Debe haber una variante que contenga TODOS los tokens
    return variants.some((v) => tokens.every((t) => v.includes(t)));
  }
  function debounce<T extends (...args: unknown[]) => void>(fn: T, wait = 150): T {
    let t: number | undefined;
    return function (this: unknown, ...args: unknown[]) {
      if (t) window.clearTimeout(t);
      t = window.setTimeout(() => fn.apply(this, args), wait);
    } as T;
  }
  // --- Fin helpers de búsqueda ---

  // Validar el parámetro 'completat'
  let webFitxa = '';
  let webTarget = '';
  let urlAjax = '';

  const pagNet = pag.split('#')[0];

  // context:
  // 1 => web pública, 2 => intranet
  if (context === 1) {
    webFitxa = `/fitxa/`;
    webTarget = '_self';

    if (pagNet === 'general') {
      urlAjax = `${devDirectory}/api/dades_personals/get/?type=llistatComplertWeb`;
    } else if (pagNet === 'represaliats' || pagNet === 'exiliats-deportats' || pagNet === 'cost-huma') {
      urlAjax = `${devDirectory}/api/dades_personals/get/?type=totesCategoriesWeb&categoria=${pagNet}`;
    }
  } else if (context === 2) {
    webFitxa = `/fitxa/`;
    webTarget = '_blank';

    if (completat === 3) {
      completat = 3;
    } else if (completat === 4) {
      completat = 4;
    }

    if (pagNet === 'general') {
      urlAjax = `${devDirectory}/api/dades_personals/get/?type=llistatComplertIntranet&completat=${completat}`;
    } else if (pagNet === 'represaliats' || pagNet === 'exiliats-deportats' || pagNet === 'cost-huma') {
      urlAjax = `${devDirectory}/api/dades_personals/get/?type=totesCategoriesIntranet&categoria=${pagNet}&completat=${completat}`;
    }
  }

  let currentPage = 1;
  const rowsPerPage = 10;
  let totalPages = 1;
  let datos: Represeliat[] = [];
  let filteredData: Represeliat[] = [];
  let searchActive = false; // NUEVO: para no volver a 'datos' cuando no hay resultados

  // Función para obtener los datos
  async function obtenerDatos() {
    try {
      datos = (await fetchData(urlAjax)) as Represeliat[];
      reindexNames(datos); // NUEVO: índice de búsqueda
      searchActive = false; // restablecer al cargar
      totalPages = Math.ceil(datos.length / rowsPerPage);
      document.getElementById('totalPages')!.textContent = totalPages.toString();
      renderizarTabla(currentPage);
    } catch (error) {
      console.error('Error al obtener los datos: ', (error as Error).message);
    }
  }

  // Función para renderizar la tabla con paginación
  function renderizarTabla(page: number) {
    const tbody = document.getElementById('represaliatsBody')!;
    tbody.innerHTML = '';
    const start = (page - 1) * rowsPerPage;
    const end = start + rowsPerPage;

    // NUEVO: usar dataSource en función del estado de búsqueda
    const dataSource = searchActive ? filteredData : datos;
    const datosPaginados = dataSource.slice(start, end);

    datosPaginados.forEach((row) => {
      const tr = document.createElement('tr');

      // Nombre completo
      const tdNombre = document.createElement('td');
      const nom = row.nom !== null ? row.nom : '';
      const cognom1 = row.cognom1 !== null ? row.cognom1 : '';
      const cognom2 = row.cognom2 !== null ? row.cognom2 : '';
      const nombreCompleto = `${cognom1} ${cognom2 ?? ''}, ${nom}`;
      tdNombre.innerHTML = `<strong><a href="${webFitxa}${row.slug}" target="${webTarget}">${nombreCompleto}</a></strong>`;
      tr.appendChild(tdNombre);

      // Municipio nacimiento
      const municipiNaixement = `${formatDatesForm(row.data_naixement) ?? 'Data desconeguda'} (${row.ciutat && row.ciutat !== '0' ? row.ciutat : 'Municipi desconegut'})`;
      const tdMunicipiNaixement = document.createElement('td');
      tdMunicipiNaixement.textContent = municipiNaixement;
      tr.appendChild(tdMunicipiNaixement);

      // Municipio defunció
      const municipiDefuncio = `${formatDatesForm(row.data_defuncio) ?? 'Data desconeguda'} (${row.ciutat2 && row.ciutat2 !== '0' ? row.ciutat2 : 'Municipi desconegut'})`;
      const tdMunicipiDefuncio = document.createElement('td');
      tdMunicipiDefuncio.textContent = municipiDefuncio;
      tr.appendChild(tdMunicipiDefuncio);

      // Col·lectiu
      const tdCollectiu = document.createElement('td');
      tdCollectiu.textContent = traduirCategoriesRepressio(row.categoria, colectiusRepressio);
      tr.appendChild(tdCollectiu);

      // COLUMNA FONT DADES (sólo intranet)
      if (context === 2 && (isAdmin || isAutor || isLogged)) {
        const fontInterna: number = row.font_intern;
        const fontTextMap: { [key: number]: string } = {
          0: 'Manel/Juan Antonio/José Luís',
          1: 'Llistat Represaliats Ajuntament',
          2: 'Manel: base dades antifranquista',
          3: 'Arxiu',
        };
        const tdModificar = document.createElement('td');
        tdModificar.textContent = fontTextMap[fontInterna] || 'Altres';
        tr.appendChild(tdModificar);
      }

      // COLUMNA ESTAT FITXA (sólo intranet)
      if (context === 2 && (isAdmin || isAutor || isLogged)) {
        const estatFitxa = row.completat;
        if (estatFitxa === 1) {
          const tdModificar = document.createElement('td');
          const btnModificar = document.createElement('button');
          btnModificar.textContent = 'PENDENT';
          btnModificar.classList.add('btn', 'btn-sm', 'btn-primary');
          tdModificar.appendChild(btnModificar);
          tr.appendChild(tdModificar);
        } else if (estatFitxa === 3) {
          const tdModificar = document.createElement('td');
          const btnModificar = document.createElement('button');
          btnModificar.textContent = 'CAL REVISIÓ';
          btnModificar.classList.add('btn', 'btn-sm', 'btn-danger');
          tdModificar.appendChild(btnModificar);
          tr.appendChild(tdModificar);
        } else if (estatFitxa === 2) {
          const tdModificar = document.createElement('td');
          const btnModificar = document.createElement('button');
          btnModificar.textContent = 'COMPLETADA';
          btnModificar.classList.add('btn', 'btn-sm', 'btn-success');
          tdModificar.appendChild(btnModificar);
          tr.appendChild(tdModificar);
        }
      }

      // COLUMNA VISIBILITAT FITXA (sólo intranet)
      if (context === 2 && (isAdmin || isAutor || isLogged)) {
        const visibilitatFitxa = row.visibilitat;
        if (visibilitatFitxa === 2) {
          const tdvisibilitat = document.createElement('td');
          const btnVisibilitat = document.createElement('button');
          btnVisibilitat.textContent = 'VISIBLE';
          btnVisibilitat.classList.add('btn', 'btn-sm', 'btn-success');
          tdvisibilitat.appendChild(btnVisibilitat);
          tr.appendChild(tdvisibilitat);
        } else {
          const tdvisibilitat = document.createElement('td');
          const btnVisibilitat = document.createElement('button');
          btnVisibilitat.textContent = 'NO VISIBLE';
          btnVisibilitat.classList.add('btn', 'btn-sm', 'btn-primary');
          tdvisibilitat.appendChild(btnVisibilitat);
          tr.appendChild(tdvisibilitat);
        }
      }

      // Botón Modificar (sólo intranet)
      if (context === 2 && (isAdmin || isAutor || isLogged)) {
        const tdModificar = document.createElement('td');
        const btnModificar = document.createElement('button');
        btnModificar.textContent = 'Modificar dades';
        btnModificar.classList.add('btn', 'btn-sm', 'btn-warning');
        btnModificar.onclick = function () {
          const link = document.createElement('a');
          link.href = `/gestio/base-dades/modifica-fitxa/${row.id}`;
          link.target = '_blank';
          link.click();
        };
        tdModificar.appendChild(btnModificar);
        tr.appendChild(tdModificar);
      }

      // Botón Eliminar (sólo admin en intranet)
      if (context === 2 && isAdmin) {
        const reloadKey = 'reload-taula-taulaLlistat';
        const tdEliminar = document.createElement('td');
        tdEliminar.innerHTML = `
          <button 
            type="button"
            class="btn btn-danger btn-sm delete-button"
            data-id="${row.id}" 
            data-url="/api/dades_personals/delete/eliminaDuplicat?id=${row.id}"
            data-reload-callback="${reloadKey}"
          >
            Elimina
          </button>
        `;
        tr.appendChild(tdEliminar);

        registerDeleteCallback(reloadKey, () => obtenerDatos());
        initDeleteHandlers();
      }

      tbody.appendChild(tr);
    });

    // Actualizar paginación
    const currentPageElement = document.getElementById('currentPage')!;
    currentPageElement.textContent = currentPage.toString();

    const prevPageButton = document.getElementById('prevPage') as HTMLButtonElement;
    prevPageButton.disabled = currentPage === 1;

    const nextPageButton = document.getElementById('nextPage') as HTMLButtonElement;
    nextPageButton.disabled = currentPage === totalPages;
  }

  // BÚSQUEDA: sólo por nombre completo (nom + cognoms), acentos/orden ignorados (NUEVO)
  function buscarEnTodosLosDatos() {
    const input = document.getElementById('searchInput') as HTMLInputElement;
    const query = input.value;
    const qNorm = normalizeText(query);

    if (qNorm === '') {
      filteredData = [];
      searchActive = false;
      currentPage = 1;
      totalPages = Math.ceil(datos.length / rowsPerPage);
      document.getElementById('totalPages')!.textContent = totalPages.toString();
      renderizarTabla(currentPage);
      return;
    }

    filteredData = datos.filter((row) => matchesName(row, qNorm));
    searchActive = true;

    totalPages = Math.ceil(filteredData.length / rowsPerPage);
    document.getElementById('totalPages')!.textContent = totalPages.toString();
    currentPage = 1;
    renderizarTabla(currentPage);
  }

  // Eventos (con debounce en el buscador)
  const onSearch = debounce(buscarEnTodosLosDatos, 150);
  document.getElementById('searchInput')!.addEventListener('input', onSearch);

  document.getElementById('prevPage')!.addEventListener('click', () => {
    if (currentPage > 1) {
      currentPage--;
      renderizarTabla(currentPage);
    }
  });
  document.getElementById('nextPage')!.addEventListener('click', () => {
    if (currentPage < totalPages) {
      currentPage++;
      renderizarTabla(currentPage);
    }
  });

  // Carga inicial de datos
  await obtenerDatos();
}
