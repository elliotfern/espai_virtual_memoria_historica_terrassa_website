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
  comarca: number;
  provincia: number;
  comunitat: number;
  estat: number;
}

interface ApiResponse<T> {
  status: string;
  message: string;
  data: T;
}

export async function formMunicipi(isUpdate: boolean, id?: number) {
  if (isUpdate && id) {
    const response = await fetchDataGet<ApiResponse<Fitxa>>(API_URLS.GET.MUNICIPI_ID(id), true);

    const btn1 = document.getElementById('refreshButtonComarca');
    const btn2 = document.getElementById('refreshButtonProvincia');
    const btn3 = document.getElementById('refreshButtonComunitat');
    const btn4 = document.getElementById('refreshButtonEstat');

    if (response && response.data && btn1 && btn2 && btn3 && btn4) {
      const data = response.data;

      const divTitol = document.getElementById('titolFormMunicipi') as HTMLDivElement;
      if (divTitol) {
        divTitol.innerHTML = `<h2>Modificació municipi: ${data.ciutat}</h2>`;
      }

      await auxiliarSelect(data.comarca, 'comarques', 'comarca', 'comarca');
      await auxiliarSelect(data.provincia, 'provincies', 'provincia', 'provincia');
      await auxiliarSelect(data.comunitat, 'comunitats', 'comunitat', 'comunitat');
      await auxiliarSelect(data.estat, 'estats', 'estat', 'estat');

      btn1.addEventListener('click', function (event) {
        event.preventDefault();
        auxiliarSelect(data.comarca, 'comarques', 'comarca', 'comarca');
      });
      btn2.addEventListener('click', function (event) {
        event.preventDefault();
        auxiliarSelect(data.provincia, 'provincies', 'provincia', 'provincia');
      });

      btn3.addEventListener('click', function (event) {
        event.preventDefault();
        auxiliarSelect(data.comunitat, 'comunitats', 'comunitat', 'comunitat');
      });

      btn4.addEventListener('click', function (event) {
        event.preventDefault();
        auxiliarSelect(data.estat, 'estats', 'estat', 'estat');
      });

      renderFormInputs(data);

      const btn = document.getElementById('btnFormMunicipi') as HTMLButtonElement;
      if (btn) {
        btn.textContent = 'Modificar dades';
      }
    }

    // auxiliarSelect;
    const municipiForm = document.getElementById('municipiForm');
    if (municipiForm) {
      municipiForm.addEventListener('submit', function (event) {
        transmissioDadesDB(event, 'PUT', 'municipiForm', API_URLS.PUT.MUNICIPI);
      });
    }
  } else {
    const btn1 = document.getElementById('refreshButtonComarca');
    const btn2 = document.getElementById('refreshButtonProvincia');
    const btn3 = document.getElementById('refreshButtonComunitat');
    const btn4 = document.getElementById('refreshButtonEstat');

    if (btn1 && btn2 && btn3 && btn4) {
      const divTitol = document.getElementById('titolFormMunicipi') as HTMLDivElement;
      if (divTitol) {
        divTitol.innerHTML = `<h2>Creació nou municipi:</h2>`;
      }

      await auxiliarSelect(0, 'comarques', 'comarca', 'comarca');
      await auxiliarSelect(0, 'provincies', 'provincia', 'provincia');
      await auxiliarSelect(0, 'comunitats', 'comunitat', 'comunitat');
      await auxiliarSelect(0, 'estats', 'estat', 'estat');

      btn1.addEventListener('click', function (event) {
        event.preventDefault();
        auxiliarSelect(0, 'comarques', 'comarca', 'comarca');
      });
      btn2.addEventListener('click', function (event) {
        event.preventDefault();
        auxiliarSelect(0, 'provincies', 'provincia', 'provincia');
      });

      btn3.addEventListener('click', function (event) {
        event.preventDefault();
        auxiliarSelect(0, 'comunitats', 'comunitat', 'comunitat');
      });

      btn4.addEventListener('click', function (event) {
        event.preventDefault();
        auxiliarSelect(0, 'estats', 'estat', 'estat');
      });

      const municipiForm = document.getElementById('municipiForm');
      if (municipiForm) {
        municipiForm.addEventListener('submit', function (event) {
          transmissioDadesDB(event, 'POST', 'municipiForm', API_URLS.POST.MUNICIPI, true);
        });
      }
    }
  }
}
