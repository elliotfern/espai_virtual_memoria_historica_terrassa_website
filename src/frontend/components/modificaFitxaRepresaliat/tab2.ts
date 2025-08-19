import { Fitxa } from '../../types/types';
import { auxiliarSelect } from '../../services/fetchData/auxiliarSelect';
import { formatDatesForm } from '../../services/formatDates/dates';

export async function tab2(fitxa?: Fitxa) {
  // Asignar valores a los campos del formulario
  const nomInput = document.getElementById('nom') as HTMLInputElement;
  if (nomInput) nomInput.value = fitxa?.nom ?? '';

  const cognom1Input = document.getElementById('cognom1') as HTMLInputElement;
  if (cognom1Input) cognom1Input.value = fitxa?.cognom1 ?? '';

  const cognom2Input = document.getElementById('cognom2') as HTMLInputElement;
  if (cognom2Input) cognom2Input.value = fitxa?.cognom2 ?? '';

  const sexeSelect = document.getElementById('sexe') as HTMLSelectElement;
  if (sexeSelect && fitxa?.sexe) {
    sexeSelect.value = fitxa.sexe;
  }

  const dataNaixementInput = document.getElementById('data_naixement') as HTMLInputElement;
  const dataDefuncioInput = document.getElementById('data_defuncio') as HTMLInputElement;
  if (fitxa && dataNaixementInput && dataDefuncioInput) {
    dataNaixementInput.value = formatDatesForm(fitxa.data_naixement) ?? '';
    dataDefuncioInput.value = formatDatesForm(fitxa.data_defuncio) ?? '';
  } else {
    dataNaixementInput.value = '';
    dataDefuncioInput.value = '';
  }

  await auxiliarSelect(fitxa?.ciutat_naixement_id ?? 252, 'municipis', 'municipi_naixement', 'ciutat', '252');
  await auxiliarSelect(fitxa?.ciutat_defuncio_id ?? 252, 'municipis', 'municipi_defuncio', 'ciutat', '252');
  await auxiliarSelect(fitxa?.ciutat_residencia_id ?? 252, 'municipis', 'municipi_residencia', 'ciutat', '252');

  await auxiliarSelect(fitxa?.tipus_via_id, 'tipusVia', 'tipus_via', 'tipus_ca');

  const adrecaInput = document.getElementById('adreca') as HTMLInputElement;
  if (adrecaInput) adrecaInput.value = fitxa?.adreca ?? '';

  const adreca_num = document.getElementById('adreca_num') as HTMLInputElement;
  if (adreca_num) adreca_num.value = fitxa?.adreca_num ?? '';

  const adreca_antic = document.getElementById('adreca_antic') as HTMLInputElement;
  if (adreca_antic) adreca_antic.value = fitxa?.adreca_antic ?? '';

  // === Usa el mismo endpoint que en tu código principal ===
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
      credentials: 'include',
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

  function renderGeoButton(id: number) {
    const container = document.getElementById('geolocalitzacioBtn');
    if (!container) return;

    // Insertamos el botón en el DIV
    container.innerHTML = `
    <button type="button" class="btn btn-primary btn-sm js-geo" data-id="${id}">
      Calcular coordenades
    </button>
  `;

    const btn = container.querySelector<HTMLButtonElement>('.js-geo');
    if (!btn) return;

    btn.addEventListener('click', async () => {
      const originalHtml = btn.innerHTML;
      btn.disabled = true;
      btn.innerHTML = 'Calculant...';

      try {
        const resp = await geocodePersona(id);
        if (resp.data) {
          btn.title = `LAT: ${resp.data.lat ?? '—'} | LNG: ${resp.data.lng ?? '—'}`;
        }

        btn.classList.remove('btn-primary');
        btn.classList.add('btn-outline-success');
        btn.innerHTML = 'Fet ✓';
      } catch (err) {
        console.error(err);
        btn.classList.remove('btn-primary');
        btn.classList.add('btn-danger');
        btn.innerHTML = 'Error';
        setTimeout(() => {
          btn.classList.remove('btn-danger', 'btn-outline-success');
          btn.classList.add('btn-primary');
          btn.innerHTML = originalHtml;
          btn.disabled = false;
        }, 2000);
        return;
      }

      setTimeout(() => {
        btn.disabled = false;
      }, 500);
    });
  }

  if (fitxa) {
    renderGeoButton(fitxa.id);
  }

  // Agregar eventos a los botones de refresco
  const refreshButton1 = document.getElementById('refreshButton1');
  if (refreshButton1) {
    refreshButton1.addEventListener('click', async (event) => {
      event.preventDefault();
      await auxiliarSelect(fitxa?.ciutat_naixement_id ?? 252, 'municipis', 'municipi_naixement', 'ciutat', '252');
    });
  }

  const refreshButton2 = document.getElementById('refreshButton2');
  if (refreshButton2) {
    refreshButton2.addEventListener('click', (event: Event) => {
      event.preventDefault();
      auxiliarSelect(fitxa?.ciutat_defuncio_id ?? 252, 'municipis', 'municipi_defuncio', 'ciutat', '252');
    });
  }

  const refreshButton3 = document.getElementById('refreshButton3');
  if (refreshButton3) {
    refreshButton3.addEventListener('click', (event: Event) => {
      event.preventDefault();
      auxiliarSelect(fitxa?.ciutat_residencia_id ?? 252, 'municipis', 'municipi_residencia', 'ciutat', '252');
    });
  }

  auxiliarSelect(fitxa?.tipologia_lloc_defuncio_id ?? 2, 'tipologia_espais', 'tipologia_lloc_defuncio', 'tipologia_espai_ca', '2');

  const refreshButton4 = document.getElementById('refreshButton4');
  if (refreshButton4) {
    refreshButton4.addEventListener('click', (event: Event) => {
      event.preventDefault();
      auxiliarSelect(fitxa?.tipologia_lloc_defuncio_id ?? 2, 'tipologia_espais', 'tipologia_lloc_defuncio', 'tipologia_espai_ca');
    });
  }

  auxiliarSelect(fitxa?.causa_defuncio_id ?? 2, 'causa_defuncio', 'causa_defuncio', 'causa_defuncio_ca', '2');
  auxiliarSelect(fitxa?.causa_defuncio_detalls, 'causa_defuncio_detalls', 'causa_defuncio_detalls', 'defuncio_detalls_ca');

  const refreshButton5 = document.getElementById('refreshButton5');
  if (refreshButton5) {
    refreshButton5.addEventListener('click', (event: Event) => {
      event.preventDefault();
      auxiliarSelect(fitxa?.causa_defuncio_id, 'causa_defuncio', 'causa_defuncio', 'causa_defuncio_ca');
    });
  }
}
