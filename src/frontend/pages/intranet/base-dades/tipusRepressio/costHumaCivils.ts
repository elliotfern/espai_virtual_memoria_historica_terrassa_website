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
  nom: string;
  cognom1: string;
  cognom2: string;
  cirscumstancies_mort: number | null;
  data_bombardeig: string | null;
  municipi_bombardeig: number | null;
  lloc_bombardeig: number | null;
  data_detencio: string | null;
  lloc_detencio: number | null;
  qui_detencio: string | null;
  qui_executa_afusellat: string | null;
  qui_ordena_afusellat: string | null;
  responsable_bombardeig: string;
  data_trobada_cadaver: string | null;
  lloc_trobada_cadaver: number | null;
}

export async function costHumaCivils(idRepresaliat: number) {
  let data: Partial<Fitxa> = {
    id: 0,
    idPersona: 0,
    nom: '',
    cognom1: '',
    cognom2: '',
    cirscumstancies_mort: 0,
    data_bombardeig: '',
    municipi_bombardeig: 0,
    lloc_bombardeig: 0,
    data_detencio: '',
    lloc_detencio: 0,
    qui_detencio: '',
    qui_executa_afusellat: '',
    qui_ordena_afusellat: '',
    responsable_bombardeig: '',
    data_trobada_cadaver: '',
    lloc_trobada_cadaver: 0,
  };

  const response = await fetchDataGet<Fitxa>(`/api/cost_huma_civils/get/fitxaRepressio?id=${idRepresaliat}`);
  const data2 = await fetchDataGet<Fitxa>(`/api/dades_personals/get/?type=nomCognoms&id=${idRepresaliat}`);
  const btn = document.getElementById('btnMortsCivils') as HTMLButtonElement;

  if (!response || !response.data) {
    if (btn) {
      btn.textContent = 'Inserir dades';
    }
  } else {
    if (btn) {
      btn.textContent = 'Modificar dades';
    }
    data = response.data as Partial<Fitxa>;
  }

  if (data2) {
    const container = document.getElementById('fitxaNomCognoms');

    if (!container) return;

    const nomComplet = `${data2.nom} ${data2.cognom1} ${data2.cognom2}`;
    const url = `https://memoriaterrassa.cat/fitxa/${data2.slug}`;

    container.innerHTML = `<h4>Fitxa: <a href="${url}" target="_blank">${nomComplet}</a></h4>`;
  }

  renderFormInputs(data);

  if (data2) {
    const inputIdPersona = document.getElementById('idPersona') as HTMLInputElement | null;
    if (inputIdPersona) {
      inputIdPersona.value = String(data2.id);
      inputIdPersona.setAttribute('value', String(data2.id));
    }
  }

  await auxiliarSelect(data?.cirscumstancies_mort, 'causa_defuncio_repressio?tipus=2', 'cirscumstancies_mort', 'causa_defuncio_ca');
  await auxiliarSelect(data?.lloc_trobada_cadaver, 'municipis', 'lloc_trobada_cadaver', 'ciutat');
  await auxiliarSelect(data?.lloc_detencio, 'municipis', 'lloc_detencio', 'ciutat');
  await auxiliarSelect(data?.municipi_bombardeig, 'municipis', 'municipi_bombardeig', 'ciutat');
  await auxiliarSelect(data?.lloc_bombardeig, 'llocs_bombardeig', 'lloc_bombardeig', 'lloc_bombardeig_ca');

  const btn1 = document.getElementById('refreshButtonMunicipi1');
  const btn2 = document.getElementById('refreshButtonMunicipi2');
  const btn3 = document.getElementById('refreshButtonMunicipi3');
  const valorBombardeig = document.querySelector<HTMLSelectElement>('#responsable_bombardeig');

  if (btn1 && btn2 && btn3 && valorBombardeig) {
    btn1.addEventListener('click', function (event) {
      event.preventDefault();
      auxiliarSelect(data?.lloc_trobada_cadaver, 'municipis', 'lloc_trobada_cadaver', 'ciutat');
    });

    btn2.addEventListener('click', function (event) {
      event.preventDefault();
      auxiliarSelect(data?.lloc_detencio, 'municipis', 'lloc_detencio', 'ciutat');
    });

    btn3.addEventListener('click', function (event) {
      event.preventDefault();
      auxiliarSelect(data?.municipi_bombardeig, 'municipis', 'municipi_bombardeig', 'ciutat');
    });
  }

  if (!response) {
    const mortCivilsForm = document.getElementById('mortCivilsForm');
    if (mortCivilsForm) {
      mortCivilsForm.addEventListener('submit', function (event) {
        transmissioDadesDB(event, 'POST', 'mortCivilsForm', '/api/cost_huma_civils/post', true);
      });
    }
  } else {
    const mortCivilsForm = document.getElementById('mortCivilsForm');
    if (mortCivilsForm) {
      mortCivilsForm.addEventListener('submit', function (event) {
        transmissioDadesDB(event, 'PUT', 'mortCivilsForm', '/api/cost_huma_civils/put');
      });
    }
  }
}
