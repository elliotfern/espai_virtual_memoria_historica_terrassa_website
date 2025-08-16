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

export async function processat(idRepresaliat: number) {
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

  const response = await fetchDataGet<Fitxa>(`/api/processats/get/fitxaRepressio?id=${idRepresaliat}`);
  const data2 = await fetchDataGet<Fitxa>(`/api/dades_personals/get/?type=nomCognoms&id=${idRepresaliat}`);

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
