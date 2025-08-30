import { fetchDataGet } from '../../../../services/fetchData/fetchDataGet';
import { auxiliarSelect } from '../../../../services/fetchData/auxiliarSelect';
import { renderFormInputs } from '../../../../services/fetchData/renderInputsForm';
import { transmissioDadesDB } from '../../../../services/fetchData/transmissioDades';
import { renderTaulaCercadorFiltres } from '../../../../services/renderTaula/renderTaulaCercadorFiltres';
import { initDeleteHandlers, registerDeleteCallback } from '../../../../services/fetchData/handleDelete';
import { getIsAdmin } from '../../../../services/auth/getIsAdmin';
import { getIsAutor } from '../../../../services/auth/getIsAutor';
import { formatDatesForm } from '../../../../services/formatDates/dates';
import { DOMAIN_API, DOMAIN_WEB } from '../../../../config/constants';

interface Fitxa {
  [key: string]: unknown;
  status: string;
  message: string;
  id: number;
  idPersona: number;
  tipus_professional: number;
  motiu_empresonament: number;
  observacions: string;
  top: number;
  qui_ordena_detencio: number;
  data_empresonament: string;
  trasllats: number;
  lloc_trasllat: string;
  data_trasllat: string;
  llibertat: number;
  data_llibertat: string;
  modalitat: number;
  vicissituds: string;
}

interface EspaiRow {
  id: number;
  data_empresonament: string;
  data_llibertat: string;
  motiu_empresonament: string;
  codi: string;
  arxiu: string;
}

type Column<T> = {
  header: string;
  field: keyof T;
  render?: (value: T[keyof T], row: T) => string;
};

export async function empresonatsPresoModel(idRepresaliat: number) {
  const isAdmin = await getIsAdmin();
  const isAutor = await getIsAutor();
  const reloadKey = 'reload-taula-taulaLlistatDetencionsPresoModel';
  const container = document.getElementById('fitxaNomCognoms');

  const data2 = await fetchDataGet<Fitxa>(`/api/dades_personals/get/?type=nomCognoms&id=${idRepresaliat}`);

  if (data2) {
    if (!container) return;

    const nomComplet = `${data2.nom} ${data2.cognom1} ${data2.cognom2}`;
    const url = `${DOMAIN_WEB}/fitxa/${data2.slug}`;

    container.innerHTML = `<h4>Fitxa: <a href="${url}" target="_blank">${nomComplet}</a></h4>`;
  }

  const columns: Column<EspaiRow>[] = [
    { header: 'Data entrada', field: 'data_empresonament', render: (_: unknown, row: EspaiRow) => `${formatDatesForm(row.data_empresonament)}` },
    {
      header: 'Data sortida',
      field: 'data_llibertat',
      render: (_: unknown, row: EspaiRow) => `${formatDatesForm(row.data_llibertat)}`,
    },
  ];

  if (isAdmin || isAutor) {
    columns.push({
      header: 'Accions',
      field: 'id',
      render: (_: unknown, row: EspaiRow) => `<a id="${row.id}" title="Modifica" href="${DOMAIN_WEB}/gestio/base-dades/empresonaments-preso-model/modifica-empresonament/${idRepresaliat}/${row.id}"><button type="button" class="btn btn-warning btn-sm">Modifica</button></a>`,
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
            data-url="${DOMAIN_API}/api/presoModel/delete/${row.id}"
            data-reload-callback="${reloadKey}"
          >
            Elimina
          </button>`,
    });
  }

  renderTaulaCercadorFiltres<EspaiRow>({
    url: `${DOMAIN_API}/api/preso_model/get/empresonatId?id=${idRepresaliat}`,
    containerId: 'taulaLlistatDetencionsPresoModel',
    columns,
    filterKeys: ['arxiu'],
    //filterByField: 'provincia',
  });

  // Registra el callback con una clave única
  registerDeleteCallback(reloadKey, () => empresonatsPresoModel(idRepresaliat));

  // Inicia el listener una sola vez
  initDeleteHandlers();
}

export async function formPresoModel(idRepresaliat: number, id?: number) {
  let data: Partial<Fitxa> = {
    trasllats: 0,
    llibertat: 0,
    modalitat: 0,
  };

  let response: Fitxa | null = null;

  if (id) {
    response = await fetchDataGet<Fitxa>(`/api/preso_model/get/fitxaRepressio?id=${id}`);
  }

  const data2 = await fetchDataGet<Fitxa>(`/api/dades_personals/get/?type=nomCognoms&id=${idRepresaliat}`);

  const btnForm = document.getElementById('btnPresoModel') as HTMLButtonElement;
  const container = document.getElementById('fitxaNomCognoms');
  const presoModelForm = document.getElementById('presoModelForm');
  const inputIdPersona = document.getElementById('idPersona') as HTMLInputElement | null;
  const btn1 = document.getElementById('refreshButton1');

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
    const url = `${DOMAIN_WEB}/fitxa/${data2.slug}`;

    container.innerHTML = `<h4>Fitxa: <a href="${url}" target="_blank">${nomComplet}</a></h4>`;
  }

  renderFormInputs(data);

  await auxiliarSelect(data?.trasllats, '', 'trasllats', '', '');
  await auxiliarSelect(data?.llibertat, '', 'llibertat', '', '');
  await auxiliarSelect(data?.modalitat, 'modalitatPreso', 'modalitat', 'modalitat_ca');

  if (btn1) {
    btn1.addEventListener('click', function (event) {
      event.preventDefault();
      auxiliarSelect(data?.modalitat, 'modalitatPreso', 'modalitat', 'modalitat_ca');
    });
  }

  if (!response) {
    if (presoModelForm) {
      presoModelForm.addEventListener('submit', function (event) {
        transmissioDadesDB(event, 'POST', 'presoModelForm', '/api/preso_model/post', true);
      });
    }
  } else {
    if (presoModelForm) {
      presoModelForm.addEventListener('submit', function (event) {
        transmissioDadesDB(event, 'PUT', 'presoModelForm', '/api/preso_model/put');
      });
    }
  }
}
