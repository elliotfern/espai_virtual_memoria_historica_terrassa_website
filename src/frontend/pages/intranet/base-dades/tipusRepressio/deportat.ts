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
  situacio: number;
  data_alliberament: string;
  lloc_mort_alliberament: number;
  preso_tipus: number;
  preso_nom: string;
  preso_data_sortida: string;
  preso_localitat: number;
  preso_num_matricula: string;
  deportacio_nom_camp: string;
  deportacio_data_entrada: string;
  deportacio_num_matricula: string;
  deportacio_nom_subcamp: string;
  deportacio_data_entrada_subcamp: string;
  deportacio_nom_matricula_subcamp: string;
}

export async function deportat(idRepresaliat: number) {
  const data = await fetchDataGet<Fitxa>(`/api/deportats/get/fitxaRepressio?id=${idRepresaliat}`);

  const data2 = await fetchDataGet<Fitxa>(`/api/dades_personals/get/?type=nomCognoms&id=${idRepresaliat}`);

  if (data2) {
    const container = document.getElementById('fitxaNomCognoms');
    const inputIdPersona = document.getElementById('idPersona') as HTMLInputElement | null;
    if (!container) return;

    const nomComplet = `${data2.nom} ${data2.cognom1} ${data2.cognom2}`;
    const url = `https://memoriaterrassa.cat/fitxa/${data2.slug}`;

    container.innerHTML = `<h4>Fitxa: <a href="${url}" target="_blank">${nomComplet}</a></h4>`;

    if (inputIdPersona && data2.id !== undefined) {
      inputIdPersona.value = String(data2.id);
    }
  }

  if (!data || data.status === 'error') {
    const btn1 = document.getElementById('refreshButton1');
    const btn2 = document.getElementById('refreshButton2');

    if (btn1 && btn2) {
      btn1.addEventListener('click', function (event) {
        event.preventDefault();
        auxiliarSelect(data?.preso_localitat, 'municipis', 'preso_localitat', 'ciutat');
      });

      btn2.addEventListener('click', function (event) {
        event.preventDefault();
        auxiliarSelect(data?.lloc_mort_alliberament, 'municipis', 'lloc_mort_alliberament', 'ciutat');
      });
    }

    auxiliarSelect(data?.situacio, 'situacions_deportats', 'situacio', 'situacio_ca');
    auxiliarSelect(data?.lloc_mort_alliberament, 'municipis', 'lloc_mort_alliberament', 'ciutat');
    auxiliarSelect(data?.preso_tipus, 'tipus_presons', 'preso_tipus', 'tipus_preso_ca');
    auxiliarSelect(data?.preso_localitat, 'municipis', 'preso_localitat', 'ciutat');

    const deportatForm = document.getElementById('deportatForm');
    if (deportatForm) {
      deportatForm.addEventListener('submit', function (event) {
        transmissioDadesDB(event, 'POST', 'deportatForm', '/api/deportats/post');
      });
    }
  } else {
    const btn1 = document.getElementById('refreshButton1');
    const btn2 = document.getElementById('refreshButton2');

    if (btn1 && btn2) {
      btn1.addEventListener('click', function (event) {
        event.preventDefault();
        auxiliarSelect(data.preso_localitat, 'municipis', 'preso_localitat', 'ciutat');
      });

      btn2.addEventListener('click', function (event) {
        event.preventDefault();
        auxiliarSelect(data.lloc_mort_alliberament, 'municipis', 'lloc_mort_alliberament', 'ciutat');
      });
    }

    auxiliarSelect(data.situacio, 'situacions_deportats', 'situacio', 'situacio_ca');
    auxiliarSelect(data.lloc_mort_alliberament, 'municipis', 'lloc_mort_alliberament', 'ciutat');
    auxiliarSelect(data.preso_tipus, 'tipus_presons', 'preso_tipus', 'tipus_preso_ca');
    auxiliarSelect(data.preso_localitat, 'municipis', 'preso_localitat', 'ciutat');

    renderFormInputs(data);

    const btn = document.getElementById('btnDeportats') as HTMLButtonElement;
    if (btn) {
      btn.textContent = 'Modificar dades';
    }

    const deportatForm = document.getElementById('deportatForm');
    if (deportatForm) {
      deportatForm.addEventListener('submit', function (event) {
        transmissioDadesDB(event, 'PUT', 'deportatForm', '/api/deportats/put');
      });
    }
  }
}
