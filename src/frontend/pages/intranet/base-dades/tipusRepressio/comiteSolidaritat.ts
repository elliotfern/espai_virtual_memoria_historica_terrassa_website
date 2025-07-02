import { fetchDataGet } from '../../../../services/fetchData/fetchDataGet';
import { auxiliarSelect } from '../../../../services/fetchData/auxiliarSelect';
import { renderFormInputs } from '../../../../services/fetchData/renderInputsForm';
import { transmissioDadesDB } from '../../../../services/fetchData/transmissioDades';
import { renderTaulaCercadorFiltres } from '../../../../services/renderTaula/renderTaulaCercadorFiltres';
import { initDeleteHandlers, registerDeleteCallback } from '../../../../services/fetchData/handleDelete';
import { getIsAdmin } from '../../../../services/auth/getIsAdmin';
import { getIsAutor } from '../../../../services/auth/getIsAutor';

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
  motiu: number;
}

interface EspaiRow {
  id: number;
  data_empresonament: string;
  data_sortida: string;
  motiu_empresonament: string;
  motiu: string;
  any_detencio: string;
}

type Column<T> = {
  header: string;
  field: keyof T;
  render?: (value: T[keyof T], row: T) => string;
};

export async function comiteSolidaritat(idRepresaliat: number) {
  const isAdmin = await getIsAdmin();
  const isAutor = await getIsAutor();
  const reloadKey = 'reload-taula-taulaLlistatDetencionsComiteSolidaritat';
  const container = document.getElementById('fitxaNomCognoms');

  const data2 = await fetchDataGet<Fitxa>(`/api/dades_personals/get/?type=nomCognoms&id=${idRepresaliat}`);

  if (data2) {
    if (!container) return;

    const nomComplet = `${data2.nom} ${data2.cognom1} ${data2.cognom2}`;
    const url = `https://memoriaterrassa.cat/fitxa/${data2.id}`;

    container.innerHTML = `<h4>Fitxa: <a href="${url}" target="_blank">${nomComplet}</a></h4>`;
  }

  const columns: Column<EspaiRow>[] = [
    {
      header: 'Any detenció',
      field: 'any_detencio',
      render: (_: unknown, row: EspaiRow) => `${row.any_detencio}`,
    },
    { header: 'Motiu empresonament', field: 'motiu' },
  ];

  if (isAdmin || isAutor) {
    columns.push({
      header: 'Accions',
      field: 'id',
      render: (_: unknown, row: EspaiRow) => `<a id="${row.id}" title="Modifica" href="https://${window.location.hostname}/gestio/base-dades/empresonaments-comite-solidaritat/modifica-empresonament/${idRepresaliat}/${row.id}"><button type="button" class="btn btn-warning btn-sm">Modifica</button></a>`,
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
            data-url="/api/comite_solidaritat/delete/${row.id}"
            data-reload-callback="${reloadKey}"
          >
            Elimina
          </button>`,
    });
  }

  renderTaulaCercadorFiltres<EspaiRow>({
    url: `/api/comite_solidaritat/get/empresonatId?id=${idRepresaliat}`,
    containerId: 'taulaLlistatDetencionsComiteSolidaritat',
    columns,
    filterKeys: ['motiu'],
    //filterByField: 'provincia',
  });

  // Registra el callback con una clave única
  registerDeleteCallback(reloadKey, () => comiteSolidaritat(idRepresaliat));

  // Inicia el listener una sola vez
  initDeleteHandlers();
}

export async function formcomiteSolidaritat(idRepresaliat: number, id?: number) {
  let data: Partial<Fitxa> = {
    motiu_empresonament: 0,
    professio: 0,
    top: 0,
    qui_ordena_detencio: 0,
  };

  let response: Fitxa | null = null;

  if (id) {
    response = await fetchDataGet<Fitxa>(`/api/comite_solidaritat/get/fitxaRepressio?id=${id}`);
  }

  const data2 = await fetchDataGet<Fitxa>(`/api/dades_personals/get/?type=nomCognoms&id=${idRepresaliat}`);

  const btnForm = document.getElementById('btnCS') as HTMLButtonElement;
  const container = document.getElementById('fitxaNomCognoms');
  const htmlForm = document.getElementById('comiteSolidaritatForm');
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
    const url = `https://memoriaterrassa.cat/fitxa/${data2.id}`;

    container.innerHTML = `<h4>Fitxa: <a href="${url}" target="_blank">${nomComplet}</a></h4>`;
  }

  renderFormInputs(data);

  await auxiliarSelect(data?.motiu, 'motiusEmpresonament', 'motiu', 'motiuEmpresonament_ca');

  if (btn1) {
    btn1.addEventListener('click', function (event) {
      event.preventDefault();
      auxiliarSelect(data?.motiu, 'motiusEmpresonament', 'motiu', 'motiuEmpresonament_ca');
    });
  }

  if (!response) {
    if (htmlForm) {
      htmlForm.addEventListener('submit', function (event) {
        transmissioDadesDB(event, 'POST', 'comiteSolidaritatForm', '/api/comite_solidaritat/post', true);
      });
    }
  } else {
    if (htmlForm) {
      htmlForm.addEventListener('submit', function (event) {
        transmissioDadesDB(event, 'PUT', 'comiteSolidaritatForm', '/api/comite_solidaritat/put');
      });
    }
  }
}
