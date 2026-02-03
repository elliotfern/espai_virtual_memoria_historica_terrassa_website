import { renderTaulaCercadorFiltres } from '../../../services/renderTaula/renderTaulaCercadorFiltres';
import { initDeleteHandlers, registerDeleteCallback } from '../../../services/fetchData/handleDelete';
import { getIsAdmin } from '../../../services/auth/getIsAdmin';
import { API_URLS } from '../../../services/api/ApiUrls';
import { formatDatesForm } from '../../../services/formatDates/dates';

interface ImatgeRow {
  id: number;
  idPersona: number | null;
  nomArxiu: string;
  nomImatge: string;
  tipus: number;
  mime: string | null;
  dateCreated: string; // YYYY-MM-DD
  dateModified: string | null; // YYYY-MM-DD | null
  tipus_label?: string;

  // 👇 datos persona (si aplica)
  nom?: string | null;
  cognom1?: string | null;
  cognom2?: string | null;
  slug?: string | null;
}

type Column<T> = {
  header: string;
  field: keyof T;
  render?: (value: T[keyof T], row: T) => string;
};

const TIPUS_IMATGE_LABELS: Record<number, string> = {
  1: 'Represaliat',
  2: 'Usuari web',
  3: 'Galeria multimèdia',
  4: 'Premsa',
};

export async function taulaImatges() {
  const isAdmin = await getIsAdmin();
  const reloadKey = 'reload-taula-taulaImatges';

  const columns: Column<ImatgeRow>[] = [
    {
      header: 'Preview',
      field: 'nomArxiu',
      render: (_: unknown, row: ImatgeRow) => {
        if (!row.nomArxiu || !row.mime) return '';

        let ext: string | null = null;

        switch (row.mime) {
          case 'image/jpeg':
            ext = 'jpg';
            break;
          case 'image/png':
            ext = 'png';
            break;
          case 'application/pdf':
            ext = 'pdf';
            break;
          default:
            return '';
        }

        let basePath = '';
        switch (row.tipus) {
          case 1:
          case 3:
            basePath = 'https://media.memoriaterrassa.cat/assets_represaliats/img/';
            break;
          case 2:
            basePath = 'https://media.memoriaterrassa.cat/assets_usuaris/';
            break;
          case 4:
            basePath = 'https://media.memoriaterrassa.cat/assets_premsa/';
            break;
          default:
            return '';
        }

        const url = `${basePath}${row.nomArxiu}.${ext}`;

        // 📄 PDF → icono
        if (ext === 'pdf') {
          return `
        <a href="${url}" target="_blank" rel="noopener noreferrer" title="Obrir PDF">
          <span class="badge bg-danger">PDF</span>
        </a>
      `;
        }

        // 🖼️ Imagen → preview
        return `
      <a href="${url}" target="_blank" rel="noopener noreferrer" title="Obrir imatge">
        <img
          src="${url}"
          alt="preview"
          loading="lazy"
          style="
            max-width:70px;
            max-height:50px;
            object-fit:cover;
            border-radius:6px;
            border:1px solid #ddd;
          "
        />
      </a>
    `;
      },
    },

    { header: 'ID', field: 'id' },

    { header: 'Nom', field: 'nomImatge' },

    { header: 'Arxiu', field: 'nomArxiu' },

    {
      header: 'Tipus',
      field: 'tipus',
      render: (_: unknown, row) => {
        switch (row.tipus) {
          case 1:
            return `<span class="badge bg-secondary">Represaliat</span>`;
          case 2:
            return `<span class="badge bg-info text-dark">Usuari web</span>`;
          case 3:
            return `<span class="badge bg-primary">Galeria multimèdia</span>`;
          case 4:
            return `<span class="badge bg-warning text-dark">Premsa</span>`;
          default:
            return `<span class="badge bg-light text-dark">—</span>`;
        }
      },
    },

    {
      header: 'MIME',
      field: 'mime',
      render: (_: unknown, row: ImatgeRow) => (row.mime ? String(row.mime) : ''),
    },

    {
      header: 'Persona',
      field: 'idPersona',
      render: (_: unknown, row: ImatgeRow) => {
        if (!row.idPersona || !row.slug) return '';

        const nom = [row.nom, row.cognom1, row.cognom2].filter(Boolean).join(' ');

        return `
      <a
        href="https://memoriaterrassa.cat/fitxa/${row.slug}"
        target="_blank"
        rel="noopener noreferrer"
        title="Obrir fitxa de ${nom}"
      >
        ${nom}
      </a>
    `;
      },
    },

    { header: 'Creat', field: 'dateCreated', render: (_: unknown, row: ImatgeRow) => formatDatesForm(row.dateCreated) },
    { header: 'Modificat', field: 'dateModified', render: (_: unknown, row: ImatgeRow) => (formatDatesForm(row.dateModified) ? String(formatDatesForm(row.dateModified)) : '') },
  ];

  if (isAdmin) {
    columns.push({
      header: 'Detalls',
      field: 'id',
      render: (_: unknown, row: ImatgeRow) =>
        `<a id="${row.id}" title="Detalls" href="https://${window.location.hostname}/gestio/auxiliars/fitxa-imatge/${row.id}">
          <button type="button" class="btn btn-warning btn-sm">Detalls</button>
        </a>`,
    });

    columns.push({
      header: 'Accions',
      field: 'id',
      render: (_: unknown, row: ImatgeRow) =>
        `<a id="${row.id}" title="Modifica" href="https://${window.location.hostname}/gestio/auxiliars/modifica-imatge/${row.id}">
          <button type="button" class="btn btn-warning btn-sm">Modifica</button>
        </a>`,
    });

    columns.push({
      header: '',
      field: 'id',
      render: (_: unknown, row: ImatgeRow) => `
        <button 
          type="button"
          class="btn btn-danger btn-sm delete-button"
          data-id="${row.id}" 
          data-url="/api/auxiliars/delete/imatge/${row.id}"
          data-reload-callback="${reloadKey}"
        >
          Elimina
        </button>`,
    });
  }

  renderTaulaCercadorFiltres<ImatgeRow>({
    url: API_URLS.GET.LLISTAT_IMATGES,
    containerId: 'taulaImatges',
    columns,
    filterKeys: ['nomImatge', 'nomArxiu'],
    filterByField: 'tipus',
    filterButtonLabel: (value) => TIPUS_IMATGE_LABELS[Number(value)] ?? String(value),
  });

  registerDeleteCallback(reloadKey, () => taulaImatges());
  initDeleteHandlers();
}
