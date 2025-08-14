import { API_URLS } from '../../../services/api/ApiUrls';
import { renderTaulaCercadorFiltres } from '../../../services/renderTaula/renderTaulaCercadorFiltres';
import { initDeleteHandlers, registerDeleteCallback } from '../../../services/fetchData/handleDelete';
import { getIsAdmin } from '../../../services/auth/getIsAdmin';
import { getIsAutor } from '../../../services/auth/getIsAutor';
import { getIsLogged } from '../../../services/auth/getIsLogged';
import { categoriesRepressio } from '../../../components/taulaDades/categoriesRepressio';
import { traduirCategoriesRepressio } from '../../../components/taulaDades/traduirCategoriesRepressio';

interface EspaiRow {
  id: number;
  nom_complet: string;
  cognom1: string;
  data_naixement: string;
  data_defuncio: string;
  cognom2: string;
  categoria: string;
  es_mortCivil: string;
  slug: string;
  observacions_internes: string;
}

type Column<T> = {
  header: string;
  field: keyof T;
  render?: (value: T[keyof T], row: T) => string;
};

export async function taulaRevisio() {
  const isAdmin = await getIsAdmin();
  const isAutor = await getIsAutor();
  const isLogged = await getIsLogged();
  const colectiusRepressio = await categoriesRepressio('ca');
  const reloadKey = 'reload-taula-revisio';

  const columns: Column<EspaiRow>[] = [
    { header: 'ID', field: 'id' },
    { header: 'Nom i cognoms', field: 'id', render: (_: unknown, row: EspaiRow) => `<a id="${row.id}" title="Fitxa" href="https://${window.location.hostname}/fitxa/${row.slug}" target="_blank">${row.nom_complet}</a>` },

    { header: 'Notes', field: 'observacions_internes', render: (_: unknown, row: EspaiRow) => `${row.observacions_internes}` },

    { header: 'Categoria repressió', field: 'categoria', render: (_: unknown, row: EspaiRow) => `${traduirCategoriesRepressio(row.categoria, colectiusRepressio)}` },
  ];

  if (isAdmin || isAutor || isLogged) {
    columns.push({
      header: 'Accions',
      field: 'id',
      render: (_: unknown, row: EspaiRow) => `<a id="${row.id}" title="Modifica" target="_blank" href="https://${window.location.hostname}/gestio/base-dades/modifica-fitxa/${row.id}"><button type="button" class="btn btn-success btn-sm">Modifica Dades personals</button></a>`,
    });
  }

  if (isAdmin) {
    columns.push({
      header: '',
      field: 'id',
      render: (_: unknown, row: EspaiRow) => `
        <button 
          type="button"
          class="btn btn-danger btn-sm delete-button"
          data-id="${row.id}" 
          data-url="/api/dades_personals/delete/eliminaDuplicat?id=${row.id}"
          data-reload-callback="${reloadKey}"
        >
          Elimina
        </button>`,
    });
  }

  renderTaulaCercadorFiltres<EspaiRow>({
    url: API_URLS.GET.LLISTAT_MORTS_CIVILS,
    containerId: 'taulaLlistatRevisio',
    columns,
    filterKeys: ['nom_complet'],
    //filterByField: 'es_deportat',
  });

  // Registra el callback con una clave única
  registerDeleteCallback(reloadKey, () => taulaRevisio());

  // Inicia el listener una sola vez
  initDeleteHandlers();
}
