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
  data_exili: string | null;
  lloc_partida: number;
  lloc_pas_frontera: number | null;
  amb_qui_passa_frontera: string | null;
  primer_desti_exili: number | null;
  primer_desti_data: string | null;
  tipologia_primer_desti: number | null;
  dades_lloc_primer_desti: string | null;
  periple_recorregut: string | null;
  deportat: string;
  ultim_desti_exili: number | null;
  tipologia_ultim_desti: number | null;
  participacio_resistencia: string | null;
  dades_resistencia: string | null;
  activitat_politica_exili: string | null;
  activitat_sindical_exili: string | null;
  situacio_legal_espanya: string | null;
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

export async function exili(idRepresaliat: number) {
  const data = await fetchDataGet<Fitxa>(`/api/exiliats/get/fitxaRepressio?id=${idRepresaliat}`);

  const data2 = await fetchDataGet<Fitxa>(`/api/dades_personals/get/?type=nomCognoms&id=${idRepresaliat}`);

  if (data2) {
    const container = document.getElementById('fitxaNomCognoms');
    const inputIdPersona = document.getElementById('idPersona') as HTMLInputElement | null;
    if (!container) return;

    const nomComplet = `${data2.nom} ${data2.cognom1} ${data2.cognom2}`;
    const url = `https://memoriaterrassa.cat/fitxa/${data2.slug}`;

    container.innerHTML = `<h4>Fitxa: <a href="${url}" target="_blank">${nomComplet}</a></h4>`;

    if (inputIdPersona) {
      inputIdPersona.value = String(data2.id);
    }
  }

  if (!data || data.status === 'error') {
    const btn1 = document.getElementById('refreshButtonTipologia2');
    const btn2 = document.getElementById('refreshButton1');
    const btn3 = document.getElementById('refreshButton2');
    const btn4 = document.getElementById('refreshButton3');
    const btn5 = document.getElementById('refreshButtonTipologia22');
    const btn6 = document.getElementById('refreshButton4');

    if (btn1 && btn2 && btn3 && btn4 && btn5 && btn6) {
      btn1.addEventListener('click', function (event) {
        event.preventDefault();
        auxiliarSelect(data?.tipologia_ultim_desti, 'tipologia_espaisExili', 'tipologia_ultim_desti', 'tipologia_espai_ca');
      });

      btn2.addEventListener('click', function (event) {
        event.preventDefault();
        auxiliarSelect(data?.lloc_partida, 'municipis', 'lloc_partida', 'ciutat');
      });

      btn3.addEventListener('click', function (event) {
        event.preventDefault();
        auxiliarSelect(data?.lloc_pas_frontera, 'municipis', 'lloc_pas_frontera', 'ciutat');
      });

      btn4.addEventListener('click', function (event) {
        event.preventDefault();
        auxiliarSelect(data?.primer_desti_exili, 'municipis', 'primer_desti_exili', 'ciutat');
      });

      btn5.addEventListener('click', function (event) {
        event.preventDefault();
        auxiliarSelect(data?.tipologia_primer_desti, 'tipologia_espaisExili', 'tipologia_primer_desti', 'tipologia_espai_ca');
      });

      btn6.addEventListener('click', function (event) {
        event.preventDefault();
        auxiliarSelect(data?.ultim_desti_exili, 'municipis', 'ultim_desti_exili', 'ciutat');
      });
    }

    await auxiliarSelect(data?.lloc_partida, 'municipis', 'lloc_partida', 'ciutat');
    await auxiliarSelect(data?.lloc_pas_frontera, 'municipis', 'lloc_pas_frontera', 'ciutat');
    await auxiliarSelect(data?.primer_desti_exili, 'municipis', 'primer_desti_exili', 'ciutat');
    await auxiliarSelect(data?.tipologia_primer_desti, 'tipologia_espaisExili', 'tipologia_primer_desti', 'tipologia_espai_ca');
    await auxiliarSelect(data?.ultim_desti_exili, 'municipis', 'ultim_desti_exili', 'ciutat');
    await auxiliarSelect(data?.tipologia_ultim_desti, 'tipologia_espaisExili', 'tipologia_ultim_desti', 'tipologia_espai_ca');

    const exiliatForm = document.getElementById('exiliatForm');
    if (exiliatForm) {
      exiliatForm.addEventListener('submit', function (event) {
        transmissioDadesDB(event, 'POST', 'exiliatForm', '/api/exiliats/post', true);
      });
    }
  } else {
    const btn1 = document.getElementById('refreshButtonTipologia2');
    const btn2 = document.getElementById('refreshButton1');
    const btn3 = document.getElementById('refreshButton2');
    const btn4 = document.getElementById('refreshButton3');
    const btn5 = document.getElementById('refreshButtonTipologia22');
    const btn6 = document.getElementById('refreshButton4');

    if (btn1 && btn2 && btn3 && btn4 && btn5 && btn6) {
      btn1.addEventListener('click', function (event) {
        event.preventDefault();
        auxiliarSelect(data.tipologia_ultim_desti, 'tipologia_espaisExili', 'tipologia_ultim_desti', 'tipologia_espai_ca');
      });

      btn2.addEventListener('click', function (event) {
        event.preventDefault();
        auxiliarSelect(data.lloc_partida, 'municipis', 'lloc_partida', 'ciutat');
      });

      btn3.addEventListener('click', function (event) {
        event.preventDefault();
        auxiliarSelect(data.lloc_pas_frontera, 'municipis', 'lloc_pas_frontera', 'ciutat');
      });

      btn4.addEventListener('click', function (event) {
        event.preventDefault();
        auxiliarSelect(data.primer_desti_exili, 'municipis', 'primer_desti_exili', 'ciutat');
      });

      btn5.addEventListener('click', function (event) {
        event.preventDefault();
        auxiliarSelect(data.tipologia_primer_desti, 'tipologia_espaisExili', 'tipologia_primer_desti', 'tipologia_espai_ca');
      });

      btn6.addEventListener('click', function (event) {
        event.preventDefault();
        auxiliarSelect(data.ultim_desti_exili, 'municipis', 'ultim_desti_exili', 'ciutat');
      });
    }

    await auxiliarSelect(data.lloc_partida, 'municipis', 'lloc_partida', 'ciutat');
    await auxiliarSelect(data.lloc_pas_frontera, 'municipis', 'lloc_pas_frontera', 'ciutat');
    await auxiliarSelect(data.primer_desti_exili, 'municipis', 'primer_desti_exili', 'ciutat');
    await auxiliarSelect(data.tipologia_primer_desti, 'tipologia_espais', 'tipologia_primer_desti', 'tipologia_espai_ca');
    await auxiliarSelect(data.ultim_desti_exili, 'municipis', 'ultim_desti_exili', 'ciutat');
    await auxiliarSelect(data.tipologia_ultim_desti, 'tipologia_espais', 'tipologia_ultim_desti', 'tipologia_espai_ca');

    renderFormInputs(data);

    const btn = document.getElementById('btnExiliats') as HTMLButtonElement;
    if (btn) {
      btn.textContent = 'Modificar dades';
    }

    const exiliatForm = document.getElementById('exiliatForm');
    if (exiliatForm) {
      exiliatForm.addEventListener('submit', function (event) {
        transmissioDadesDB(event, 'PUT', 'exiliatForm', '/api/exiliats/put');
      });
    }
  }
}
