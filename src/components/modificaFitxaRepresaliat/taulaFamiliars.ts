import { renderTaulaCercadorFiltres } from '../../services/renderTaula/renderTaulaCercadorFiltres';
import { initDeleteHandlers, registerDeleteCallback } from '../../services/fetchData/handleDelete';
import { getIsAdmin } from '../../services/auth/getIsAdmin';
import { getIsAutor } from '../../services/auth/getIsAutor';
import { getIsLogged } from '../../services/auth/getIsLogged';

interface EspaiRow {
  id: number;
  idParent: number;
  nom_complet: string;
  anyNaixement: string;
  relacio_parentiu: string;
}

type Column<T> = {
  header: string;
  field: keyof T;
  render?: (value: T[keyof T], row: T) => string;
};

export async function taulaFamiliars(idRepressaliat: number) {
  const isAdmin = await getIsAdmin();
  const isAutor = await getIsAutor();
  const isLogged = await getIsLogged();
  const reloadKey = `reload-familiar-${idRepressaliat}`;

  const columns: Column<EspaiRow>[] = [
    { header: 'Nom i cognoms', field: 'nom_complet' },
    {
      header: 'Any de naixement',
      field: 'anyNaixement',
      render: (value: string | number) => (value && String(value).trim() !== '' ? String(value) : 'Desconegut'),
    },

    { header: 'Relació de parentiu', field: 'relacio_parentiu' },
  ];

  if (isAdmin || isAutor || isLogged) {
    columns.push({
      header: 'Accions',
      field: 'id',
      render: (_: unknown, row: EspaiRow) => `<a id="${row.id}" title="Modifica" href="https://${window.location.hostname}/gestio/familiars/modifica-familiar/${row.idParent}/${row.id}/" target="_blank"><button type="button" class="btn btn-warning btn-sm">Modifica</button></a>`,
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
      data-url="/api/familiars/delete/${row.id}"
      data-reload-callback="${reloadKey}"
    >
      Elimina
    </button>`,
    });
  }

  renderTaulaCercadorFiltres<EspaiRow>({
    url: `https://${window.location.host}/api/familiars/get/fitxaFamiliars?id=${idRepressaliat}`,
    containerId: 'quadreFamiliars',
    columns,
    filterKeys: ['nom_complet'],
    //filterByField: 'provincia',
  });

  // Registra el callback con una clave única
  registerDeleteCallback(reloadKey, () => taulaFamiliars(idRepressaliat));

  // Inicia el listener una sola vez
  initDeleteHandlers();
}
