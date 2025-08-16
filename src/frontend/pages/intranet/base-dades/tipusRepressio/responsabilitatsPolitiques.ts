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
  lloc_empresonament: number;
  lloc_exili: number;
  observacions: string;
  top: number;
  qui_ordena_detencio: number;
}

export async function responsabilitatsPolitiques(idRepresaliat: number) {
  let data: Partial<Fitxa> = {
    lloc_empresonament: 0,
    lloc_exili: 0,
    top: 0,
    qui_ordena_detencio: 0,
  };

  const response = await fetchDataGet<Fitxa>(`/api/responsabilitats_politiques/get/fitxaRepressio?id=${idRepresaliat}`);
  const data2 = await fetchDataGet<Fitxa>(`/api/dades_personals/get/?type=nomCognoms&id=${idRepresaliat}`);

  const btnForm = document.getElementById('btnResponsabilitats') as HTMLButtonElement;
  const container = document.getElementById('fitxaNomCognoms');
  const htmlForm = document.getElementById('responsabilitatsPolitiquesForm');
  const inputIdPersona = document.getElementById('idPersona') as HTMLInputElement | null;
  const btn1 = document.getElementById('refreshButton1');
  const btn2 = document.getElementById('refreshButton2');

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

  await auxiliarSelect(data?.lloc_empresonament, 'llistatPresons', 'lloc_empresonament', 'preso');
  await auxiliarSelect(data?.lloc_exili, 'estats', 'lloc_exili', 'estat');

  if (btn1 && btn2) {
    btn1.addEventListener('click', function (event) {
      event.preventDefault();
      auxiliarSelect(data?.lloc_empresonament, 'llistatPresons', 'lloc_empresonament', 'preso');
    });

    btn2.addEventListener('click', function (event) {
      event.preventDefault();
      auxiliarSelect(data?.lloc_exili, 'estats', 'lloc_exili', 'estat');
    });
  }

  if (!response) {
    if (htmlForm) {
      htmlForm.addEventListener('submit', function (event) {
        transmissioDadesDB(event, 'POST', 'responsabilitatsPolitiquesForm', '/api/responsabilitats_politiques/post', true);
      });
    }
  } else {
    if (htmlForm) {
      htmlForm.addEventListener('submit', function (event) {
        transmissioDadesDB(event, 'PUT', 'responsabilitatsPolitiquesForm', '/api/responsabilitats_politiques/put');
      });
    }
  }
}
