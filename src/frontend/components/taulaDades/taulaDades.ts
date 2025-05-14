import { fetchData } from '../../services/api/api';
import { Represeliat } from '../../types/types';
import { categorias } from '../../config';
import { getIsAdmin } from '../../services/auth/getIsAdmin';

export async function cargarTabla(pag: string, context: number, completat: number | null = null) {
  let urlAjax = '';
  const devDirectory = `https://${window.location.hostname}`;

  const isAdmin = await getIsAdmin();

  // Validar el parámetro 'completat': si no es 1 o 2, asignar 3
  if (completat !== 1 && completat !== 2) {
    completat = 3;
  }

  if (pag === 'tots' || pag === 'general') {
    urlAjax = `${devDirectory}/api/dades_personals/get/?type=tots&completat=${completat}`;
  } else {
    urlAjax = `${devDirectory}/api/dades_personals/get/?type=totesCategories&categoria=${pag}&completat=${completat}`;
  }

  let currentPage = 1;
  const rowsPerPage = 10; // Número de filas por página
  let totalPages = 1;
  let datos: Represeliat[] = [];
  let filteredData: Represeliat[] = [];

  let webFitxa = '';
  let webTarget = '';

  if (context === 1) {
    webFitxa = `/fitxa/`;
    webTarget = '_self';
  } else {
    webFitxa = `/fitxa/`;
    webTarget = '_blank';
  }

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
      const tdMunicipiNaixement = document.createElement('td');
      const municipiNaixement = `${row.data_naixement ?? 'Desconegut'} (${row.ciutat ?? 'Desconegut'})`;
      tdMunicipiNaixement.textContent = municipiNaixement;
      tr.appendChild(tdMunicipiNaixement);

      // Municipio defunció
      const tdMunicipiDefuncio = document.createElement('td');
      const municipiDefuncio = `${row.data_defuncio ?? 'Desconegut'} (${row.ciutat2 ?? 'Desconegut'})`;
      tdMunicipiDefuncio.textContent = municipiDefuncio;
      tr.appendChild(tdMunicipiDefuncio);

      // Col·lectiu
      const tdCollectiu = document.createElement('td');
      const categoriasIds = row.categoria ? row.categoria.replace(/[{}]/g, '').split(',').map(Number) : [];
      const collectiuTexto = categoriasIds
        .map((num) => categorias[num] || '') // Usar la constante categorias
        .filter(Boolean)
        .join(', ');
      tdCollectiu.textContent = collectiuTexto;
      tr.appendChild(tdCollectiu);

      // COLUMNA ESTAT FITXA NOMES PELS USUARIS REGISTRATS

      // COLUMNA FONT DADES, NOMES USUARIS REGISTRATS
      // Verificar si el usuario es el admin con id 1
      if (isAdmin) {
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
      if (isAdmin) {
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
      if (isAdmin) {
        // Botón Modificar
        const tdModificar = document.createElement('td');
        const btnModificar = document.createElement('button');
        btnModificar.textContent = 'Modificar dades';
        btnModificar.classList.add('btn', 'btn-sm', 'btn-warning');
        btnModificar.onclick = function () {
          const link = document.createElement('a');
          link.href = `/gestio/tots/fitxa/modifica/${row.id}`;
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
      if (isAdmin) {
        const tdEliminar = document.createElement('td');
        const btnEliminar = document.createElement('button');
        btnEliminar.textContent = 'Eliminar';
        btnEliminar.classList.add('btn', 'btn-sm', 'btn-danger');
        btnEliminar.onclick = function () {
          if (confirm('¿Estás seguro de que deseas eliminar este registro?')) {
            window.location.href = `/afusellats/eliminar/${row.id}`;
          }
        };
        tdEliminar.appendChild(btnEliminar);
        tr.appendChild(tdEliminar);
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
        const municipiNaixement = `${row.data_naixement ?? 'Desconegut'} (${row.ciutat ?? 'Desconegut'})`.toLowerCase();
        const municipiDefuncio = `${row.data_defuncio ?? 'Desconegut'} (${row.ciutat2 ?? 'Desconegut'})`.toLowerCase();
        const categoriasIds = row.categoria ? row.categoria.replace(/[{}]/g, '').split(',').map(Number) : [];
        const collectiuTexto = categoriasIds
          .map((num) => categorias[num] || '') // Usar la constante categorias
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
