import { API_URLS } from '../../../services/api/ApiUrls';
import { renderTaulaCercadorFiltres } from '../../../services/renderTaula/renderTaulaCercadorFiltres';
import { initDeleteHandlers, registerDeleteCallback } from '../../../services/fetchData/handleDelete';
import { getIsAdmin } from '../../../services/auth/getIsAdmin';
import { getIsAutor } from '../../../services/auth/getIsAutor';
import { getIsLogged } from '../../../services/auth/getIsLogged';

interface EspaiRow {
  id: number;
  nom_complet: string;
  categoria: string;
  es_mortCivil: string;
  slug: string;
  observacions_internes: string;
  nom: string;
  cognom1: string;
  cognom2: string;
  adreca: string;
  ciutat: string;
  lat: number;
  lng: number;
  tipus_ca: string;
}

type Column<T> = {
  header: string;
  field: keyof T;
  render?: (value: T[keyof T], row: T) => string;
};

// ==== Configura aquí TU endpoint real ====
const GEOCODE_ENDPOINT = `https://memoriaterrassa.cat/api/dades_personals/geo/put`;

type GeoSuccess = {
  status: 'success';
  message?: string;
  data?: { lat?: number; lng?: number };
};
function isGeoSuccess(x: unknown): x is GeoSuccess {
  return typeof x === 'object' && x !== null && (x as { status?: string }).status === 'success';
}

async function geocodePersona(id: number): Promise<GeoSuccess> {
  const url = `${GEOCODE_ENDPOINT}/?id=${encodeURIComponent(String(id))}`;
  const res = await fetch(url, {
    method: 'PUT',
    headers: { Accept: 'application/json' },
    credentials: 'include', // conserva sesión/login si usas cookies
  });
  if (!res.ok) {
    const text = await res.text();
    throw new Error(`HTTP ${res.status} ${res.statusText} - ${text}`);
  }
  const data: unknown = await res.json();
  if (!isGeoSuccess(data)) {
    throw new Error('Resposta inesperada del servidor');
  }
  return data;
}

// evitamos adjuntar el listener más de una vez
let geoClickHandlerAttached = false;

function attachGeocodeClickHandler(container: HTMLElement, onUpdated: () => void): void {
  if (geoClickHandlerAttached) return;
  geoClickHandlerAttached = true;

  container.addEventListener('click', async (ev) => {
    const target = ev.target as HTMLElement;
    const btn = target.closest<HTMLButtonElement>('.js-geo');
    if (!btn) return;

    const idAttr = btn.dataset.id;
    const id = idAttr ? Number(idAttr) : NaN;
    if (!Number.isFinite(id)) return;

    const originalHtml = btn.innerHTML;
    btn.disabled = true;
    btn.innerHTML = 'Calculant...';

    try {
      const resp = await geocodePersona(id);
      if (resp.data) {
        btn.title = `LAT: ${resp.data.lat ?? '—'} | LNG: ${resp.data.lng ?? '—'}`;
      }

      // feedback de éxito
      btn.classList.remove('btn-primary');
      btn.classList.add('btn-outline-success');
      btn.innerHTML = 'Fet ✓';

      // refresca la tabla para que se actualicen lat/lng y desaparezca de “pendents”
      onUpdated();
    } catch (err) {
      console.error(err);
      btn.classList.remove('btn-primary');
      btn.classList.add('btn-danger');
      btn.innerHTML = 'Error';
      // restablecer botón tras 2s
      setTimeout(() => {
        btn.classList.remove('btn-danger', 'btn-outline-success');
        btn.classList.add('btn-primary');
        btn.innerHTML = originalHtml;
        btn.disabled = false;
      }, 2000);
      return;
    }

    // opcional: volver a habilitar más tarde (aunque recargamos tabla)
    setTimeout(() => {
      btn.disabled = false;
    }, 500);
  });
}

export async function taulaRevisioGeolocalitzacio() {
  const isAdmin = await getIsAdmin();
  const isAutor = await getIsAutor();
  const isLogged = await getIsLogged();
  const reloadKey = 'reload-taula-revisio';

  const columns: Column<EspaiRow>[] = [
    { header: 'ID', field: 'id' },
    {
      header: 'Nom i cognoms',
      field: 'id',
      render: (_: unknown, row: EspaiRow) => `<a id="${row.id}" title="Fitxa" href="https://${window.location.hostname}/fitxa/${row.slug}" target="_blank">${row.cognom1} ${row.cognom2}, ${row.nom}</a>`,
    },
    {
      header: 'Adreça',
      field: 'adreca',
      render: (_: unknown, row: EspaiRow) => `${row.tipus_ca?.trim() || ''} ${row.adreca}, ${row.ciutat}`,
    },
    { header: 'Latitud', field: 'lat', render: (_: unknown, row: EspaiRow) => `${row.lat ?? ''}` },
    { header: 'Longitud', field: 'lng', render: (_: unknown, row: EspaiRow) => `${row.lng ?? ''}` },
  ];

  if (isAdmin || isAutor || isLogged) {
    columns.push(
      {
        header: 'Coordenades',
        field: 'id',
        render: (_: unknown, row: EspaiRow) =>
          `<button type="button" class="btn btn-primary btn-sm js-geo" data-id="${row.id}">
             Calcular coordenades
           </button>`,
      },
      {
        header: 'Accions',
        field: 'id',
        render: (_: unknown, row: EspaiRow) =>
          `<a id="${row.id}" title="Modifica" target="_blank" href="https://${window.location.hostname}/gestio/base-dades/modifica-fitxa/${row.id}">
             <button type="button" class="btn btn-success btn-sm">Modifica Dades personals</button>
           </a>`,
      }
    );
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

  // Renderiza la tabla
  renderTaulaCercadorFiltres<EspaiRow>({
    url: API_URLS.GET.LLISTAT_CASOS_REVISIO_GEOLOCALITZACIO,
    containerId: 'taulaLlistatRevisioGeolocalitzacio',
    columns,
    filterKeys: ['nom_complet'],
  });

  // Adjunta el listener del botón de geocodificación (una sola vez)
  const container = document.getElementById('taulaLlistatRevisioGeolocalitzacio');
  if (container) {
    attachGeocodeClickHandler(container, () => {
      // al terminar, recarga la tabla
      taulaRevisioGeolocalitzacio();
    });
  }

  // Delete: registrar callback y listeners (tu flujo existente)
  registerDeleteCallback(reloadKey, () => taulaRevisioGeolocalitzacio());
  initDeleteHandlers();
}
