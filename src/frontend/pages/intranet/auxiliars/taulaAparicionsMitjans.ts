import { renderTaulaCercadorFiltres } from '../../../services/renderTaula/renderTaulaCercadorFiltres';
import { initDeleteHandlers, registerDeleteCallback } from '../../../services/fetchData/handleDelete';
import { getIsAdmin } from '../../../services/auth/getIsAdmin';
import { API_URLS } from '../../../services/api/ApiUrls';

interface AparicioRow {
  id: number;
  data_aparicio: string; // "YYYY-MM-DD"
  tipus_aparicio: string;
  mitja_id: number;
  url_noticia: string | null;
  image_id: number | null;
  destacat: number; // 0/1
  estat: 'draft' | 'publicat';
  created_at: string;
  updated_at: string | null;

  // i18n (vienen repetidas por idioma en el listado SQL)
  lang: string | null;
  titol: string | null;
  resum: string | null;
  notes: string | null;
  pdf_url: string | null;
}

type Column<T> = {
  header: string;
  field: keyof T;
  render?: (value: T[keyof T], row: T) => string;
};

export async function taulaAparicionsPremsa() {
  const isAdmin = await getIsAdmin();
  const reloadKey = 'reload-taula-taulaLlistatAparacions';

  const columns: Column<AparicioRow>[] = [
    { header: 'Data', field: 'data_aparicio' },

    { header: 'Tipus', field: 'tipus_aparicio' },

    { header: 'Títol', field: 'titol' },

    {
      header: 'Mitjà',
      field: 'mitja_id',
    },

    {
      header: 'Estat',
      field: 'estat',
      render: (_: unknown, row: AparicioRow) => (row.estat === 'publicat' ? `<span class="badge bg-success">Publicat</span>` : `<span class="badge bg-secondary">Draft</span>`),
    },

    {
      header: 'Destacat',
      field: 'destacat',
      render: (_: unknown, row: AparicioRow) => (row.destacat ? `<span class="badge bg-warning text-dark">Sí</span>` : `<span class="badge bg-light text-dark">No</span>`),
    },

    {
      header: 'Notícia',
      field: 'url_noticia',
      render: (_: unknown, row: AparicioRow) => {
        if (!row.url_noticia) return '';
        const safeUrl = String(row.url_noticia);
        return `<a href="${safeUrl}" target="_blank" rel="noopener noreferrer">
          <button type="button" class="btn btn-outline-primary btn-sm">Obre</button>
        </a>`;
      },
    },

    {
      header: 'PDF',
      field: 'pdf_url',
      render: (_: unknown, row: AparicioRow) => {
        if (!row.pdf_url) return '';
        const safeUrl = String(row.pdf_url);
        return `<a href="${safeUrl}" target="_blank" rel="noopener noreferrer">
          <button type="button" class="btn btn-outline-secondary btn-sm">PDF</button>
        </a>`;
      },
    },

    {
      header: 'Idioma',
      field: 'lang',
      render: (_: unknown, row: AparicioRow) => (row.lang ? `<span class="badge bg-info text-dark">${row.lang}</span>` : ''),
    },
  ];

  if (isAdmin) {
    columns.push({
      header: 'Detalls',
      field: 'id',
      render: (_: unknown, row: AparicioRow) =>
        `<a id="${row.id}" title="Detalls" href="https://${window.location.hostname}/gestio/auxiliars/fitxa-aparicio-premsa/${row.id}">
          <button type="button" class="btn btn-warning btn-sm">Detalls</button>
        </a>`,
    });

    columns.push({
      header: 'Accions',
      field: 'id',
      render: (_: unknown, row: AparicioRow) =>
        `<a id="${row.id}" title="Modifica" href="https://${window.location.hostname}/gestio/auxiliars/modifica-aparicio-premsa/${row.id}">
          <button type="button" class="btn btn-warning btn-sm">Modifica</button>
        </a>`,
    });

    columns.push({
      header: '',
      field: 'id',
      render: (_: unknown, row: AparicioRow) => `
        <button 
          type="button"
          class="btn btn-danger btn-sm delete-button"
          data-id="${row.id}" 
          data-url="/api/auxiliars/delete/aparicioPremsa/${row.id}"
          data-reload-callback="${reloadKey}"
        >
          Elimina
        </button>`,
    });
  }

  renderTaulaCercadorFiltres<AparicioRow>({
    url: API_URLS.GET.LLISTAT_APARICIONS, // añade esta constante igual que LLISTAT_MITJANS
    containerId: 'taulaLlistatAparacions',
    columns,
    filterKeys: ['titol', 'resum', 'notes'],
    filterByField: 'tipus_aparicio',
  });

  registerDeleteCallback(reloadKey, () => taulaAparicionsPremsa());
  initDeleteHandlers();
}
