import { auxiliarSelect } from '../../../services/fetchData/auxiliarSelect';
import { transmissioDadesDB } from '../../../services/fetchData/transmissioDades';
import { API_URLS } from '../../../services/api/ApiUrls';
import { fetchDataGet } from '../../../services/fetchData/fetchDataGet';
import { renderFormInputs } from '../../../services/fetchData/renderInputsForm';

interface Fitxa {
  [key: string]: unknown;
  status: string;
  message: string;
  id: number;
  espai_cat: string;
  municipi: number;
  comarca: number;
  provincia: number;
  comunitat: number;
  estat: number;
  user_type: number;
  avatar: number;
}

interface ApiResponse<T> {
  status: string;
  message: string;
  data: T;
}

export async function formUsuaris(isUpdate: boolean, id?: number) {
  const btn1 = document.getElementById('refreshButtonAvatar');
  const divTitol = document.getElementById('titolForm') as HTMLDivElement;
  const btnUsuari = document.getElementById('btnUsuari') as HTMLButtonElement;
  const usuariForm = document.getElementById('usuariForm');

  let data: Partial<Fitxa> = {
    comarca: 0,
    provincia: 0,
    comunitat: 0,
    estat: 0,
  };

  if (!btn1 || !divTitol || !btnUsuari || !usuariForm) return;

  if (id && isUpdate) {
    const response = await fetchDataGet<ApiResponse<Fitxa>>(API_URLS.GET.ESPAI_ID(id), true);

    if (!response || !response.data) return;
    data = response.data;

    divTitol.innerHTML = `<h2>Modificació Usuari: ${data.espai_cat}</h2>`;

    renderFormInputs(data);

    btnUsuari.textContent = 'Modificar dades';

    usuariForm.addEventListener('submit', function (event) {
      transmissioDadesDB(event, 'PUT', 'usuariForm', 'https://memoriaterrassa.cat/api/auxiliars/put/usuari');
    });
  } else {
    divTitol.innerHTML = `<h2>Creació de nou usuari</h2>`;
    btnUsuari.textContent = 'Inserir dades';

    usuariForm.addEventListener('submit', function (event) {
      transmissioDadesDB(event, 'POST', 'usuariForm', 'https://memoriaterrassa.cat/api/auxiliars/post/usuari', true);
    });
  }

  // Llenar selects con opciones
  await auxiliarSelect(data.user_type ?? 0, 'tipusUsuaris', 'user_type', 'tipus');
  await auxiliarSelect(data.avatar ?? 0, 'avatarsUsuaris', 'avatar', 'nomImatge');

  btn1.addEventListener('click', function (event) {
    event.preventDefault();
    auxiliarSelect(data.avatar ?? 0, 'avatarsUsuaris', 'avatar', 'nomImatge');
  });
}
