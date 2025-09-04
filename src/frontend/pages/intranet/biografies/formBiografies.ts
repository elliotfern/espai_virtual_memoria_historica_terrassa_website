import { transmissioDadesDB } from '../../../services/fetchData/transmissioDades';
import { API_URLS } from '../../../services/api/ApiUrls';
import { fetchDataGet } from '../../../services/fetchData/fetchDataGet';
import { renderFormInputs } from '../../../services/fetchData/renderInputsForm';

interface Fitxa {
  [key: string]: unknown;
  status: string;
  message: string;
  id: number;
  ciutat: number;
  comarca: number;
  provincia: number;
  comunitat: number;
  estat: number;
  arxiu: string;
}

interface ApiResponse<T> {
  status: string;
  message: string;
  data: T;
}

export async function formBiografies(isUpdate: boolean, id?: number) {
  const divTitol = document.getElementById('titolForm') as HTMLDivElement;
  const btnBiografies = document.getElementById('btnBiografies') as HTMLButtonElement;
  const biografiesForm = document.getElementById('BiografiesForm');

  let data: Partial<Fitxa> = {
    comarca: 0,
    provincia: 0,
    comunitat: 0,
    estat: 0,
    arxiu: '',
    ciutat: 0,
  };

  if (!divTitol || !btnBiografies || !biografiesForm) return;

  if (id && isUpdate) {
    const response = await fetchDataGet<ApiResponse<Fitxa>>(API_URLS.GET.BIOGRAFIES_ID(id), true);

    if (!response || !response.data) return;
    data = response.data;

    divTitol.innerHTML = `<h2>Biografies: modificació de biografies:
     <h4>Fitxa represaliat: <a href="https://memoriaterrassa.cat/fitxa/${data.slug}>" target="_blank">${data.nom} ${data.cognom1} ${data.cognom2}</a></h4>`;

    renderFormInputs(data);

    btnBiografies.textContent = 'Modificar dades';
  } else {
    if (id) {
      const response = await fetchDataGet<ApiResponse<Fitxa>>(API_URLS.GET.REPRESALIAT_ID(id), true);

      if (!response || !response.data) return;
      data = response.data;

      divTitol.innerHTML = `<h2>Biografies: creació de nova biografia</h2>
           <h4>Fitxa represaliat: <a href="https://memoriaterrassa.cat/fitxa/${data.slug}>" target="_blank">${data.nom} ${data.cognom1} ${data.cognom2}</a></h4>`;

      btnBiografies.textContent = 'Inserir dades';
    }

    biografiesForm.addEventListener('submit', function (event) {
      transmissioDadesDB(event, 'POST', 'BiografiesForm', API_URLS.POST.BIOGRAFIES, true);
    });
  }
}
