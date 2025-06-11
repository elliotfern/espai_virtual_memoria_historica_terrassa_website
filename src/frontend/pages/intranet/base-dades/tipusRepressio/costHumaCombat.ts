import { fetchDataGet } from '../../../../services/fetchData/fetchDataGet';
import { auxiliarSelect } from '../../../../services/fetchData/auxiliarSelect';
import { renderFormInputs } from '../../../../services/fetchData/renderInputsForm';
import { transmissioDadesDB } from '../../../../services/fetchData/transmissioDades';

interface Fitxa {
  [key: string]: unknown;
  status: string;
  message: string;
  id: number;
  idPersona: number;
  condicio: number;
  bandol: number;
  any_lleva: string;
  unitat_inicial: string;
  cos: number;
  unitat_final: string;
  graduacio_final: string;
  periple_militar: string;
  circumstancia_mort: number;
  desaparegut_data: string;
  desaparegut_lloc: number;
  desaparegut_data_aparicio: string;
  desaparegut_lloc_aparicio: number;
}

export async function costHumaCombat(idRepresaliat: number) {
  let data: Partial<Fitxa> = {
    id: 0,
    idPersona: 0,
    condicio: 0,
    bandol: 0,
    any_lleva: '',
    unitat_inicial: '',
    cos: 0,
    unitat_final: '',
    graduacio_final: '',
    periple_militar: '',
    circumstancia_mort: 0,
    desaparegut_data: '',
    desaparegut_lloc: 0,
    desaparegut_data_aparicio: '',
    desaparegut_lloc_aparicio: 0,
  };

  const response = await fetchDataGet<Fitxa>(`/api/cost_huma_front/get/fitxaRepressio?id=${idRepresaliat}`);
  const data2 = await fetchDataGet<Fitxa>(`/api/dades_personals/get/?type=nomCognoms&id=${idRepresaliat}`);

  if (!response || !response.data) return;
  data = response.data;

  if (data2) {
    const container = document.getElementById('fitxaNomCognoms');
    const inputIdPersona = document.getElementById('idPersona') as HTMLInputElement | null;
    if (!container) return;

    const nomComplet = `${data2.nom} ${data2.cognom1} ${data2.cognom2}`;
    const url = `https://memoriaterrassa.cat/fitxa/${data2.id}`;

    container.innerHTML = `<h4>Fitxa: <a href="${url}" target="_blank">${nomComplet}</a></h4>`;

    if (inputIdPersona && data2.id !== undefined) {
      inputIdPersona.value = String(data2.id);
    }
  }

  renderFormInputs(data);

  await auxiliarSelect(data?.condicio, 'condicio_civil_militar', 'condicio', 'condicio_ca');
  await auxiliarSelect(data?.bandol, 'bandols_guerra', 'bandol', 'bandol_ca');
  await auxiliarSelect(data?.cos, 'cossos_militars', 'cos', 'cos_militar_ca');
  await auxiliarSelect(data?.circumstancia_mort, 'causa_defuncio_repressio?tipus=1', 'circumstancia_mort', 'causa_defuncio_ca');
  await auxiliarSelect(data?.desaparegut_lloc, 'municipis', 'desaparegut_lloc', 'ciutat');
  await auxiliarSelect(data?.desaparegut_lloc_aparicio, 'municipis', 'desaparegut_lloc_aparicio', 'ciutat');

  const btn1 = document.getElementById('refreshButton2');
  const btn2 = document.getElementById('refreshButton1');

  if (btn1 && btn2) {
    btn1.addEventListener('click', function (event) {
      event.preventDefault();
      auxiliarSelect(data?.desaparegut_lloc_aparicio, 'municipis', 'desaparegut_lloc_aparicio', 'ciutat');
    });

    btn2.addEventListener('click', function (event) {
      event.preventDefault();
      auxiliarSelect(data?.desaparegut_lloc, 'municipis', 'desaparegut_lloc', 'ciutat');
    });
  }

  if (!response) {
    const mortCombatForm = document.getElementById('mortCombatForm');
    if (mortCombatForm) {
      mortCombatForm.addEventListener('submit', function (event) {
        transmissioDadesDB(event, 'POST', 'mortCombatForm', '/api/cost_huma_front/post', true);
      });
    }
  } else {
    const btn = document.getElementById('btnMortsCombat') as HTMLButtonElement;
    if (btn) {
      btn.textContent = 'Modificar dades';
    }

    const mortCombatForm = document.getElementById('mortCombatForm');
    if (mortCombatForm) {
      mortCombatForm.addEventListener('submit', function (event) {
        transmissioDadesDB(event, 'PUT', 'mortCombatForm', '/api/cost_huma_front/put');
      });
    }
  }
}
