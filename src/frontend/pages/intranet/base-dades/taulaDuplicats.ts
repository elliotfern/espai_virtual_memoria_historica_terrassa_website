import { API_URLS } from '../../../services/api/ApiUrls';
import { renderTaulaCercadorFiltres } from '../../../services/renderTaula/renderTaulaCercadorFiltres';
import { initDeleteHandlers, registerDeleteCallback } from '../../../services/fetchData/handleDelete';
import { getIsAdmin } from '../../../services/auth/getIsAdmin';
import { getIsAutor } from '../../../services/auth/getIsAutor';
import { traduirCategoriesRepressio } from '../../../components/taulaDades/traduirCategoriesRepressio';
import { categoriesRepressio } from '../../../components/taulaDades/categoriesRepressio';

interface EspaiRow {
  id: number;
  nom: string;
  cognom1: string;
  data_naixement: string;
  cognom2: string;
  categoria: string;
}

type Column<T> = {
  header: string;
  field: keyof T;
  render?: (value: T[keyof T], row: T) => string;
};

export async function taulaDuplicats() {
  const isAdmin = await getIsAdmin();
  const isAutor = await getIsAutor();
  const colectiusRepressio = await categoriesRepressio('ca');
  const reloadKey = 'reload-taula-taulaLlistatDuplicats';

  const columns: Column<EspaiRow>[] = [
    { header: 'ID', field: 'id' },
    { header: 'Nom i cognoms', field: 'id', render: (_: unknown, row: EspaiRow) => `<a id="${row.id}" title="Fitxa" href="https://${window.location.hostname}/fitxa/${row.id}" target="_blank">${row.nom} ${row.cognom1} ${row.cognom2}</a>` },
    { header: 'Categoria', field: 'id', render: (_: unknown, row: EspaiRow) => `${traduirCategoriesRepressio(row.categoria, colectiusRepressio)}` },
  ];

  if (isAdmin || isAutor) {
    columns.push({
      header: 'Accions',
      field: 'id',
      render: (_: unknown, row: EspaiRow) => `<a id="${row.id}" title="Modifica" href="https://${window.location.hostname}/gestio/base-dades/modifica-fitxa/${row.id}"><button type="button" class="btn btn-warning btn-sm">Modifica</button></a>`,
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
    url: API_URLS.GET.LLISTAT_REGISTRES_DUPLICATS,
    containerId: 'taulaLlistatDuplicats',
    columns,
    filterKeys: ['cognom1'],
    //filterByField: 'es_deportat',
  });

  // Registra el callback con una clave única
  registerDeleteCallback(reloadKey, () => taulaDuplicats());

  // Inicia el listener una sola vez
  initDeleteHandlers();
}
