import { fetchDataGet } from '../../../../services/fetchData/fetchDataGet';
import { auxiliarSelect } from '../../../../services/fetchData/auxiliarSelect';
import { renderFormInputs } from '../../../../services/fetchData/renderInputsForm';
import { transmissioDadesDB } from '../../../../services/fetchData/transmissioDades';
import { getIsAdmin } from '../../../../services/auth/getIsAdmin';
import { getIsAutor } from '../../../../services/auth/getIsAutor';
import { formatDatesForm } from '../../../../services/formatDates/dates';
import { DOMAIN_API, DOMAIN_WEB } from '../../../../config/constants';
import { renderTaulaCercadorFiltres } from '../../../../services/renderTaula/renderTaulaCercadorFiltres';
import { initDeleteHandlers, registerDeleteCallback } from '../../../../services/fetchData/handleDelete';

interface Fitxa {
  [key: string]: unknown;
  status: string;
  message: string;
  id: number;
  idPersona: number;
  tipus_procediment: number;
  tipus_judici: number;
  jutjat: number;
  lloc_consell_guerra: number;
  acusacio: number;
  acusacio_2: number;
  sentencia: number;
  pena: number;
  lloc_detencio: number;
}

interface EspaiRow {
  id: number;
  data_empresonament: string;
  data_llibertat: string;
  motiu_empresonament: string;
  codi: string;
  arxiu: string;
  data_detencio: string;
  tipus_procediment: string;
  num_causa: string;
  tipus_judici: string;
}

type Column<T> = {
  header: string;
  field: keyof T;
  render?: (value: T[keyof T], row: T) => string;
};

export async function llistatDetingutConsellGuerra(idRepresaliat: number) {
  const isAdmin = await getIsAdmin();
  const isAutor = await getIsAutor();
  const reloadKey = 'reload-taula-taulaLlistatConsellGuerra';
  const container = document.getElementById('fitxaNomCognoms');

  const data2 = await fetchDataGet<Fitxa>(`/api/dades_personals/get/?type=nomCognoms&id=${idRepresaliat}`);

  if (data2) {
    if (!container) return;

    const nomComplet = `${data2.nom} ${data2.cognom1} ${data2.cognom2}`;
    const url = `${DOMAIN_WEB}/fitxa/${data2.slug}`;

    container.innerHTML = `<h4>Fitxa: <a href="${url}" target="_blank">${nomComplet}</a></h4>`;
  }

  const columns: Column<EspaiRow>[] = [
    { header: 'Data detenció', field: 'data_detencio', render: (_: unknown, row: EspaiRow) => `${formatDatesForm(row.data_detencio)}` },
    { header: 'Tipus procediment judicial', field: 'tipus_procediment', render: (_: unknown, row: EspaiRow) => `${row.tipus_procediment} - ${row.tipus_judici}` },
    { header: 'Núm. de causa', field: 'num_causa', render: (_: unknown, row: EspaiRow) => `${row.num_causa}` },
  ];

  if (isAdmin || isAutor) {
    columns.push({
      header: 'Accions',
      field: 'id',
      render: (_: unknown, row: EspaiRow) => `<a id="${row.id}" title="Modifica" href="${DOMAIN_WEB}/gestio/base-dades/detinguts-consell-guerra/modifica-detingut-consell-guerra/${idRepresaliat}/${row.id}"><button type="button" class="btn btn-warning btn-sm">Modifica</button></a>`,
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
            data-url="${DOMAIN_API}/presoModel/delete/${row.id}"
            data-reload-callback="${reloadKey}"
          >
            Elimina
          </button>`,
    });
  }

  renderTaulaCercadorFiltres<EspaiRow>({
    url: `${DOMAIN_API}/processats/get/fitxaId?id=${idRepresaliat}`,
    containerId: 'taulaLlistatConsellGuerra',
    columns,
    filterKeys: ['num_causa'],
    //filterByField: 'provincia',
  });

  // Registra el callback con una clave única
  registerDeleteCallback(reloadKey, () => llistatDetingutConsellGuerra(idRepresaliat));

  // Inicia el listener una sola vez
  initDeleteHandlers();
}

export async function formDetingutConsellGuerra(idRepresaliat: number, id?: number) {
  let data: Partial<Fitxa> = {
    tipus_procediment: 0,
    tipus_judici: 0,
    jutjat: 0,
    lloc_consell_guerra: 0,
    acusacio: 0,
    acusacio_2: 0,
    sentencia: 0,
    pena: 0,
    lloc_detencio: 0,
  };

  let response: Fitxa | null = null;

  const data2 = await fetchDataGet<Fitxa>(`/api/dades_personals/get/?type=nomCognoms&id=${idRepresaliat}`);

  if (id) {
    response = await fetchDataGet<Fitxa>(`/api/processats/get/fitxaIntranetId?id=${id}`);
  }

  const btnForm = document.getElementById('btnProcessat') as HTMLButtonElement;
  const btn1 = document.getElementById('refreshButton1');
  const btn2 = document.getElementById('refreshButton2');
  const btn3 = document.getElementById('refreshButton3');
  const btn4 = document.getElementById('refreshButton4');
  const btn5 = document.getElementById('refreshButton5');
  const btn6 = document.getElementById('refreshButton6');
  const btn7 = document.getElementById('refreshButton7');
  const btn8 = document.getElementById('refreshButton8');
  const btn10 = document.getElementById('refreshButton10');
  const container = document.getElementById('fitxaNomCognoms');
  const processatForm = document.getElementById('processatForm');
  const inputIdPersona = document.getElementById('idPersona') as HTMLInputElement | null;

  if (!response || !response.data) {
    if (btnForm) {
      btnForm.textContent = 'Inserir dades';
    }
  } else {
    if (btnForm) {
      btnForm.textContent = 'Modificar dades';
    }
    data = response.data;
  }

  if (data2) {
    if (!container) return;

    if (inputIdPersona) {
      inputIdPersona.value = String(data2.id);
    }

    const nomComplet = `${data2.nom} ${data2.cognom1} ${data2.cognom2}`;
    const url = `https://memoriaterrassa.cat/fitxa/${data2.slug}`;

    container.innerHTML = `<h4>Fitxa: <a href="${url}" target="_blank">${nomComplet}</a></h4>`;
  }

  renderFormInputs(data);

  if (btn1 && btn2 && btn3 && btn4 && btn5 && btn6 && btn7 && btn8 && btn10) {
    btn1.addEventListener('click', function (event) {
      event.preventDefault();
      auxiliarSelect(data?.tipus_procediment, 'procediments', 'tipus_procediment', 'procediment_ca');
    });

    btn2.addEventListener('click', function (event) {
      event.preventDefault();
      auxiliarSelect(data?.tipus_judici, 'tipusJudici', 'tipus_judici', 'tipusJudici_ca');
    });

    btn3.addEventListener('click', function (event) {
      event.preventDefault();
      auxiliarSelect(data?.sentencia, 'sentencies', 'sentencia', 'sentencia_ca');
    });

    btn4.addEventListener('click', function (event) {
      event.preventDefault();
      auxiliarSelect(data?.pena, 'penes', 'pena', 'pena_ca');
    });

    btn5.addEventListener('click', function (event) {
      event.preventDefault();
      auxiliarSelect(data?.jutjat, 'jutjats', 'jutjat', 'jutjat_ca');
    });

    btn6.addEventListener('click', function (event) {
      event.preventDefault();
      auxiliarSelect(data?.lloc_consell_guerra, 'municipis', 'lloc_consell_guerra', 'ciutat');
    });

    btn7.addEventListener('click', function (event) {
      event.preventDefault();
      auxiliarSelect(data?.acusacio, 'acusacions', 'acusacio', 'acusacio_ca');
    });

    btn8.addEventListener('click', function (event) {
      event.preventDefault();
      auxiliarSelect(data?.acusacio_2, 'acusacions', 'acusacio_2', 'acusacio_ca');
    });

    btn10.addEventListener('click', function (event) {
      event.preventDefault();
      auxiliarSelect(data?.lloc_detencio, 'municipis', 'lloc_detencio', 'ciutat');
    });
  }

  auxiliarSelect(data?.lloc_detencio, 'municipis', 'lloc_detencio', 'ciutat');
  await auxiliarSelect(data?.tipus_procediment, 'procediments', 'tipus_procediment', 'procediment_ca');
  await auxiliarSelect(data?.tipus_judici, 'tipusJudici', 'tipus_judici', 'tipusJudici_ca');
  await auxiliarSelect(data?.jutjat, 'jutjats', 'jutjat', 'jutjat_ca');
  await auxiliarSelect(data?.lloc_consell_guerra, 'municipis', 'lloc_consell_guerra', 'ciutat');
  await auxiliarSelect(data?.acusacio, 'acusacions', 'acusacio', 'acusacio_ca');
  await auxiliarSelect(data?.acusacio_2, 'acusacions', 'acusacio_2', 'acusacio_ca');
  await auxiliarSelect(data?.sentencia, 'sentencies', 'sentencia', 'sentencia_ca');
  await auxiliarSelect(data?.pena, 'penes', 'pena', 'pena_ca');

  if (!response) {
    if (processatForm) {
      processatForm.addEventListener('submit', function (event) {
        transmissioDadesDB(event, 'POST', 'processatForm', '/api/processats/post', true);
      });
    }
  } else {
    if (processatForm) {
      processatForm.addEventListener('submit', function (event) {
        transmissioDadesDB(event, 'PUT', 'processatForm', '/api/processats/put');
      });
    }
  }
}
