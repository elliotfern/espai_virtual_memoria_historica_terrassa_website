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

export async function cargarTabla(pag: string, context: number, completat: number | null = null) {
  const devDirectory = `https://${window.location.hostname}`;

  const isAdmin = await getIsAdmin();
  const isAutor = await getIsAutor();
  const isLogged = await getIsLogged();
  const colectiusRepressio = await categoriesRepressio('ca');

  // Validar el parámetro 'completat': si no es 1 o 2, asignar 3
  // completat 1 = PENDENT > visibilitat = 1
  // completat 2 = COMPLETADA > visibilitat = 2
  // completat 3 = TOTES
  // error: completat 1 > visibilitat 2
  // cal fer un endpoint independent per web publica i intranet

  let webFitxa: string = '';
  let webTarget: string = '';
  let urlAjax: string = '';

  const pagNet = pag.split('#')[0];

  // context:
  // context = 1 > es per la web publica (mostrar només fitxes amb visibilitat 2 i completat 2)
  // context = 2 > es per la intranet (mostrar només fitxes amb visibilitat 1 i completat 1)
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

    if (completat !== 1 && completat !== 2) {
      completat = 3;
    }

    if (pagNet === 'general') {
      urlAjax = `${devDirectory}/api/dades_personals/get/?type=llistatComplertIntranet&completat=${completat}`;
    } else if (pagNet === 'represaliats' || pagNet === 'exiliats-deportats' || pagNet === 'cost-huma') {
      urlAjax = `${devDirectory}/api/dades_personals/get/?type=totesCategoriesIntranet&categoria=${pagNet}&completat=${completat}`;
    }
  }

  let currentPage = 1;
  const rowsPerPage = 10; // Número de filas por página
  let totalPages = 1;
  let datos: Represeliat[] = [];
  let filteredData: Represeliat[] = [];

  // Función para obtener los datos
  async function obtenerDatos() {
    try {
      datos = (await fetchData(urlAjax)) as Represeliat[]; // Usa la función fetchData
      totalPages = Math.ceil(datos.length / rowsPerPage); // Calculamos el número total de páginas
      document.getElementById('totalPages')!.textContent = totalPages.toString(); // Actualizamos el número total de páginas
      renderizarTabla(currentPage); // Renderizamos la tabla para la página actual
    } catch (error) {
      console.error('Error al obtener los datos: ', (error as Error).message);
    }
  }

  // Función para renderizar la tabla con paginación
  function renderizarTabla(page: number) {
    const tbody = document.getElementById('represaliatsBody')!;
    tbody.innerHTML = ''; // Limpiar el contenido actual
    const start = (page - 1) * rowsPerPage;
    const end = start + rowsPerPage;
    const datosPaginados = filteredData.length > 0 ? filteredData.slice(start, end) : datos.slice(start, end); // Obtener el rango de datos para esta página

    datosPaginados.forEach((row) => {
      const tr = document.createElement('tr');

      // Nombre completo
      const tdNombre = document.createElement('td');
      const nom = row.nom !== null ? row.nom : '';
      const cognom1 = row.cognom1 !== null ? row.cognom1 : '';
      const cognom2 = row.cognom2 !== null ? row.cognom2 : '';
      const nombreCompleto = `${cognom1} ${cognom2 ?? ''}, ${nom}`;
      tdNombre.innerHTML = `<strong><a href="${webFitxa}${row.id}" target="${webTarget}">${nombreCompleto}</a></strong>`;
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

      // COLUMNA ESTAT FITXA NOMES PELS USUARIS REGISTRATS

      // COLUMNA FONT DADES, NOMES USUARIS REGISTRATS
      // Verificar si el usuario es el admin con id 1
      // mostrar nomes a la intranet context === 2
      if (context === 2 && (isAdmin || isAutor || isLogged)) {
        // Botó estat
        const fontInterna: number = row.font_intern;

        const fontTextMap: { [key: number]: string } = {
          0: 'Manel/Juan Antonio/José Luís',
          1: 'Llistat Represaliats Ajuntament',
          2: 'Manel: base dades antifranquista',
          3: 'Arxiu',
        };

        const tdModificar = document.createElement('td');
        tdModificar.textContent = fontTextMap[fontInterna] || 'Altres'; // Busca el valor en el objeto
        tr.appendChild(tdModificar);
      } else if (context === 1) {
        // nada
      } else {
        // Crear la fila vacía
      }

      // Verificar si el usuario es el admin con id 1
      if (context === 2 && (isAdmin || isAutor || isLogged)) {
        // Botó estat
        const estatFitxa = row.completat;
        if (estatFitxa === 1) {
          const tdModificar = document.createElement('td');
          const btnModificar = document.createElement('button');
          btnModificar.textContent = 'PENDENT';
          btnModificar.classList.add('btn', 'btn-sm', 'btn-primary');
          tdModificar.appendChild(btnModificar);
          tr.appendChild(tdModificar);
        } else {
          const tdModificar = document.createElement('td');
          const btnModificar = document.createElement('button');
          btnModificar.textContent = 'COMPLETADA';
          btnModificar.classList.add('btn', 'btn-sm', 'btn-success');
          tdModificar.appendChild(btnModificar);
          tr.appendChild(tdModificar);
        }
      } else if (context === 1) {
        // nada
      } else {
        // Crear la fila vacía
      }

      // Verificar si el usuario es el admin con id 1
      if (context === 2 && (isAdmin || isAutor || isLogged)) {
        // Botó VISIBILITAT FITXA
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
      } else if (context === 1) {
        // nada
      } else {
        // Crear la fila vacía
      }

      // Verificar si el usuario es el admin con id 1
      if (context === 2 && (isAdmin || isAutor || isLogged)) {
        // Botón Modificar
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
      } else if (context === 1) {
        // nada
      } else {
        // Crear la fila vacía
      }

      // Botón Eliminar
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

        // Registra el callback con una clave única
        registerDeleteCallback(reloadKey, () => obtenerDatos());

        // Inicia el listener una sola vez
        initDeleteHandlers();
      } else if (context === 1) {
        // nada
      } else {
        // Crear la fila vacía
      }

      // Añadir la fila a la tabla
      tbody.appendChild(tr);
    });

    // Actualizar el estado de la paginación
    const currentPageElement = document.getElementById('currentPage')!;
    currentPageElement.textContent = currentPage.toString();

    const prevPageButton = document.getElementById('prevPage') as HTMLButtonElement;
    prevPageButton.disabled = currentPage === 1;

    const nextPageButton = document.getElementById('nextPage') as HTMLButtonElement;
    nextPageButton.disabled = currentPage === totalPages;
  }

  // Función para buscar en todos los datos
  function buscarEnTodosLosDatos() {
    const input = document.getElementById('searchInput') as HTMLInputElement;
    const query = input.value.toLowerCase().trim();

    if (query === '') {
      // Si el campo de búsqueda está vacío, renderizar la tabla con paginación
      filteredData = [];
      currentPage = 1;
      totalPages = Math.ceil(datos.length / rowsPerPage);
      document.getElementById('totalPages')!.textContent = totalPages.toString();
      renderizarTabla(currentPage);
    } else {
      // Filtrar los datos que coincidan con la búsqueda
      filteredData = datos.filter((row) => {
        const nombreCompleto = `${row.cognom1} ${row.cognom2 ?? ''}, ${row.nom}`.toLowerCase();
        const municipiNaixement = `${formatDatesForm(row.data_naixement) ?? 'Desconegut'} (${row.ciutat ?? 'Desconegut'})`.toLowerCase();
        const municipiDefuncio = `${formatDatesForm(row.data_defuncio) ?? 'Desconegut'} (${row.ciutat2 ?? 'Desconegut'})`.toLowerCase();
        const categoriasIds = row.categoria ? row.categoria.replace(/[{}]/g, '').split(',').map(Number) : [];
        const collectiuTexto = categoriasIds
          .map((num) => colectiusRepressio[num] || '')
          .filter(Boolean)
          .join(', ')
          .toLowerCase();

        // Verifica si alguno de los campos coincide con la búsqueda
        return nombreCompleto.includes(query) || municipiNaixement.includes(query) || municipiDefuncio.includes(query) || collectiuTexto.includes(query);
      });

      // Calcular el número total de páginas para los resultados filtrados
      totalPages = Math.ceil(filteredData.length / rowsPerPage);
      document.getElementById('totalPages')!.textContent = totalPages.toString();

      // Renderizar la primera página de los resultados filtrados
      currentPage = 1;
      renderizarTabla(currentPage);
    }
  }

  // Eventos
  document.getElementById('searchInput')!.addEventListener('input', buscarEnTodosLosDatos);
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
