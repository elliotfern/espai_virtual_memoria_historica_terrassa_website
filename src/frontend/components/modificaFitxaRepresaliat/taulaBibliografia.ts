import { renderTaulaCercadorFiltres } from '../../services/renderTaula/renderTaulaCercadorFiltres';
import { initDeleteHandlers, registerDeleteCallback } from '../../services/fetchData/handleDelete';
import { getIsAdmin } from '../../services/auth/getIsAdmin';
import { getIsAutor } from '../../services/auth/getIsAutor';
import { getIsLogged } from '../../services/auth/getIsLogged';

type Column<T> = {
  header: string;
  field: keyof T;
  render?: (value: T[keyof T], row: T) => string;
};

interface EntradaBibliografica {
  id: number;
  idParent: number;
  pagina: string;
  llibre: string;
  autor: string;
  ciutat: string;
  editorial: string;
  volum: string | number;
  any: string | number;
}

export async function taulaBibliografia(idRepressaliat: number) {
  const isAdmin = await getIsAdmin();
  const isAutor = await getIsAutor();
  const isLogged = await getIsLogged();
  const reloadKey = `reload-bibliografia-${idRepressaliat}`;

  const columns: Column<EntradaBibliografica>[] = [
    {
      header: 'Llibre/Article',
      field: 'llibre',
      render: (_value: string | number, row: EntradaBibliografica) => {
        const autor = row.autor || 'Desconegut';
        const llibre = row.llibre || 'Desconegut';
        const ciutat = row.ciutat || '';
        const editorial = row.editorial || '';
        const volum = row.volum ? `núm. ${row.volum}` : '';
        const any = row.any || '';

        return `${autor}. <i>${llibre}.</i> ${ciutat}${ciutat ? ', ' : ''}${editorial}${editorial ? ' ' : ''}${volum}${volum ? ', ' : ''}${any}`;
      },
    },

    {
      header: 'Pàgines',
      field: 'pagina',
      render: (_value: string | number, row: EntradaBibliografica) => {
        const pagina = row.pagina || '-';
        return `${pagina}`;
      },
    },
  ];

  if (isAdmin || isAutor || isLogged) {
    columns.push({
      header: 'Accions',
      field: 'id',
      render: (_: unknown, row: EntradaBibliografica) => `<a id="${row.id}" title="Modifica" href="https://${window.location.hostname}/gestio/fonts-documentals/fitxa/modifica-bibliografia/${idRepressaliat}/${row.id}/" target="_blank"><button type="button" class="btn btn-warning btn-sm">Modifica</button></a>`,
    });
  }

  if (isAdmin) {
    columns.push({
      header: '',
      field: 'id',
      render: (_: unknown, row: EntradaBibliografica) => `
    <button 
      type="button"
      class="btn btn-danger btn-sm delete-button"
      data-id="${row.id}" 
      data-url="/api/fonts_documentals/delete/ref_bibliografica/${row.id}"
      data-reload-callback="${reloadKey}"
    >
      Elimina
    </button>`,
    });
  }

  renderTaulaCercadorFiltres<EntradaBibliografica>({
    url: `https://${window.location.host}/api/fonts/get/fitxaRepressaliatBibliografia?id=${idRepressaliat}`,
    containerId: 'quadreFontsBibliografia',
    columns,
    filterKeys: ['llibre'],
    //filterByField: 'provincia',
    showSearch: false, // Desactiva el buscador
    showPagination: false, // Desactiva la paginación
  });

  // Registra el callback con una clave única
  registerDeleteCallback(reloadKey, () => taulaBibliografia(idRepressaliat));

  // Inicia el listener una sola vez
  initDeleteHandlers();
}
