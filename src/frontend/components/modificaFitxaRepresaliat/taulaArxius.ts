import { renderTaulaCercadorFiltres } from '../../services/renderTaula/renderTaulaCercadorFiltres';
import { initDeleteHandlers } from '../../services/fetchData/handleDelete';
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
  referencia: string;
  arxiu: string;
  codi: string;
  ciutat: string;
}

export async function taulaArxius(idRepressaliat: number) {
  const isAdmin = await getIsAdmin();
  const isAutor = await getIsAutor();
  const isLogged = await getIsLogged();

  const columns: Column<EntradaBibliografica>[] = [
    { header: 'Referència', field: 'referencia' },
    { header: 'Arxiu', field: 'arxiu' },
    { header: 'Codi', field: 'codi' },
    { header: 'Ciutat arxiu', field: 'ciutat' },
  ];

  if (isAdmin || isAutor || isLogged) {
    columns.push({
      header: 'Accions',
      field: 'id',
      render: (_: unknown, row: EntradaBibliografica) => `<a id="${row.id}" title="Modifica" href="https://${window.location.hostname}/gestio/familiars/modifica-familiar/${row.idParent}/${row.id}/" target="_blank"><button type="button" class="btn btn-warning btn-sm">Modifica</button></a>`,
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
      data-url="/api/familiars/delete/familiar/${row.id}"
    >
      Elimina
    </button>`,
    });
  }

  renderTaulaCercadorFiltres<EntradaBibliografica>({
    url: `https://${window.location.host}/api/fonts/get/fitxaRepressaliatArxius?id=${idRepressaliat}`,
    containerId: 'quadreFontsArxius',
    columns,
    filterKeys: ['arxiu'],
    //filterByField: 'provincia',
  });

  // Iniciar los listeners de borrado
  initDeleteHandlers(() => taulaArxius(idRepressaliat));
}
