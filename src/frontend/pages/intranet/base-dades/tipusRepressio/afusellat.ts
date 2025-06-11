import { fetchDataGet } from '../../../../services/fetchData/fetchDataGet';
import { auxiliarSelect } from '../../../../services/fetchData/auxiliarSelect';
import { renderFormInputs } from '../../../../services/fetchData/renderInputsForm';
import { transmissioDadesDB } from '../../../../services/fetchData/transmissioDades';

interface Fitxa {
  [key: string]: unknown;
  status: string;
  message: string;
  id?: number;
  idPersona: number | null;
  copia_exp: string | null;
  procediment: number | null;
  num_causa: string | null;
  data_inici_proces: string | null;
  jutge_instructor: string | null;
  secretari_instructor: string | null;
  jutjat: string | null;
  any_inicial: string | null;
  consell_guerra_data: string | null;
  lloc_consell_guerra: number | null;
  president_tribunal: string | null;
  defensor: string | null;
  fiscal: string | null;
  ponent: string | null;
  tribunal_vocals: string | null;
  acusacio: string | null;
  acusacio_2: string | null;
  testimoni_acusacio: string | null;
  sentencia_data: string | null;
  sentencia: string | null;
  data_sentencia: string | null;
  data_execucio: string | null;
  enterrament_lloc: number | null;
  lloc_execucio_enterrament: number | null;
  ref_num_arxiu: string | null;
  font_1: string | null;
  font_2: string | null;
  familiars: string | null;
  observacions: string | null;
}

export async function afusellat(idAfusellat: number) {
  let data: Partial<Fitxa> = {
    id: 0,
    idPersona: 0,
    copia_exp: '',
    procediment: 0,
    num_causa: '',
    data_inici_proces: '',
    jutge_instructor: '',
    secretari_instructor: '',
    jutjat: '',
    any_inicial: '',
    consell_guerra_data: '',
    lloc_consell_guerra: 0,
    president_tribunal: '',
    defensor: '',
    fiscal: '',
    ponent: '',
    tribunal_vocals: '',
    acusacio: '',
    acusacio_2: '',
    testimoni_acusacio: '',
    sentencia_data: '',
    sentencia: '',
    data_sentencia: '',
    data_execucio: '',
    enterrament_lloc: 0,
    lloc_execucio_enterrament: 0,
    ref_num_arxiu: '',
    font_1: '',
    font_2: '',
    familiars: '',
    observacions: '',
  };

  const btn1 = document.getElementById('refreshButtonMunicipi1');
  const btn2 = document.getElementById('refreshButtonMunicipi2');
  const btn3 = document.getElementById('refreshButtonMunicipi3');
  const container = document.getElementById('fitxaNomCognoms');
  const btn = document.getElementById('btnAfusellat') as HTMLButtonElement;
  const afusellatForm = document.getElementById('afusellatForm');

  const response = await fetchDataGet<Fitxa>(`/api/afusellats/get/fitxa?id=${idAfusellat}`);
  const data2 = await fetchDataGet<Fitxa>(`/api/dades_personals/get/?type=nomCognoms&id=${idAfusellat}`);

  if (!response || !response.data) {
    if (btn) btn.textContent = 'Inserir dades';
  } else {
    data = response.data;
    if (btn) btn.textContent = 'Modificar dades';

    if (afusellatForm) {
      afusellatForm.onsubmit = function (event) {
        const isInsert = !response.data;
        transmissioDadesDB(event, isInsert ? 'POST' : 'PUT', 'afusellatForm', isInsert ? '/api/cost_huma_civils/post' : '/api/cost_huma_civils/put', isInsert);
      };
    }
  }

  if (data2) {
    const inputIdPersona = document.getElementById('idPersona') as HTMLInputElement | null;
    if (!container) return;

    const nomComplet = `${data2.nom} ${data2.cognom1} ${data2.cognom2}`;
    const url = `https://memoriaterrassa.cat/fitxa/${data2.id}`;

    container.innerHTML = `<h4>Fitxa: <a href="${url}" target="_blank">${nomComplet}</a></h4>`;

    if (inputIdPersona && data2.id !== undefined) {
      inputIdPersona.value = String(data2.id);
    }
  }

  await auxiliarSelect(data?.procediment, 'causa_defuncio_repressio?tipus=2', 'cirscumstancies_mort', 'causa_defuncio_ca');
  await auxiliarSelect(data?.lloc_consell_guerra, 'municipis', 'lloc_trobada_cadaver', 'ciutat');
  await auxiliarSelect(data?.enterrament_lloc, 'municipis', 'lloc_detencio', 'ciutat');
  await auxiliarSelect(data?.lloc_execucio_enterrament, 'municipis', 'municipi_bombardeig', 'ciutat');

  if (btn1 && btn2 && btn3) {
    btn1.addEventListener('click', function (event) {
      event.preventDefault();
      auxiliarSelect(data?.enterrament_lloc, 'municipis', 'lloc_trobada_cadaver', 'ciutat');
    });

    btn2.addEventListener('click', function (event) {
      event.preventDefault();
      auxiliarSelect(data?.lloc_execucio_enterrament, 'municipis', 'lloc_detencio', 'ciutat');
    });
  }

  renderFormInputs(data);
}
