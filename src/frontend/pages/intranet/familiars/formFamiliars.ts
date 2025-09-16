import { transmissioDadesDB } from '../../../services/fetchData/transmissioDades';
import { API_URLS } from '../../../services/api/ApiUrls';
import { fetchDataGet } from '../../../services/fetchData/fetchDataGet';
import { renderFormInputs } from '../../../services/fetchData/renderInputsForm';
import 'trix'; // carga Trix (extiende el DOM con <trix-editor>)
import { auxiliarSelect } from '../../../services/fetchData/auxiliarSelect';
import { wireForm } from '../../../helpers/transmissioHelper';

interface Fitxa {
  [key: string]: unknown;
  status: string;
  message: string;
  id: number; // id de la biografía (en update)
  ciutat: number;
  comarca: number;
  provincia: number;
  comunitat: number;
  idParent_old: number;
  relacio_parantiu: number;

  // usados para pintar título/enlace
  slug?: string;
  nom?: string;
  cognom1?: string;
  cognom2?: string;
  idParent: number;
  relacio_parentiu: number;

  idRepresaliat?: number; // si tu API lo devuelve
}

interface ApiResponse<T> {
  status: string;
  message: string;
  data: T;
}

type ApiResponse2<T> = {
  status: 'success' | 'error';
  message: string;
  errors: unknown[];
  data: T;
};

export async function formFamiliars(isUpdate: boolean, idParent?: number, id?: number) {
  const divTitol = document.getElementById('titolForm') as HTMLDivElement;
  const btnUsuari = document.getElementById('btnFamiliars') as HTMLButtonElement;
  const usuariForm = document.getElementById('familiarForm');

  let data: Partial<Fitxa> = {
    comarca: 0,
    provincia: 0,
    comunitat: 0,
    estat: 0,
  };

  if (!divTitol || !btnUsuari || !usuariForm) return;

  if (id && isUpdate) {
    const url = `https://memoriaterrassa.cat/api/familiars/get/familiarsFitxa?id=${id}`;
    const response = await fetchDataGet<ApiResponse<Fitxa>>(url, true);

    if (!response || !response.data) return;
    data = response.data;

    divTitol.innerHTML = `<h2>Modificació Usuari: ${data.espai_cat}</h2>`;

    renderFormInputs(data);

    btnUsuari.textContent = 'Modificar dades';

    usuariForm.addEventListener('submit', function (event) {
      transmissioDadesDB(event, 'PUT', 'familiarForm', 'https://memoriaterrassa.cat/api/familiars/put');
    });

    await auxiliarSelect(data.relacio_parentiu ?? 0, 'relacions_parentiu', 'relacio_parentiu', 'relacio_parentiu');
    await auxiliarSelect(data.idParent ?? 0, 'llistat_complert_represaliats', 'idParent', 'nom_complert');
  } else {
    // ——— CREAR
    // Si viene un id de represaliat, precargamos su ficha básica
    if (idParent) {
      const res = await fetchDataGet<ApiResponse2<Fitxa[]>>(API_URLS.GET.REPRESALIAT_ID(idParent), true);
      const fitxa = res?.data?.[0];
      if (!fitxa) {
        divTitol.innerHTML = `<h2>Relació de dades familiars: /h2><p>No s'han trobat dades de la fitxa.</p>`;
        return;
      }

      await auxiliarSelect(fitxa.relacio_parantiu ?? 0, 'relacions_parentiu', 'relacio_parentiu', 'relacio_parentiu');
      await auxiliarSelect(fitxa.idRepresaliat ?? 0, 'llistat_complert_represaliats', 'idParent', 'nom_complert');

      const nomComplet = [fitxa.nom as string, fitxa.cognom1 as string, fitxa.cognom2 as string].filter(Boolean).join(' ');
      const slug = (fitxa.slug as string) ?? '';

      divTitol.innerHTML = `
            <h2>Relació de dades familiars: creació de nou parent</h2>
            <h4>
              Fitxa represaliat:
              <a href="https://memoriaterrassa.cat/fitxa/${slug}" target="_blank" rel="noopener noreferrer">
                ${nomComplet}
              </a>
            </h4>
          `;

      btnUsuari.textContent = 'Inserir dades';

      // Ocultar el form en éxito y hacer scroll automático a #okMessage
      wireForm({
        formId: 'familiarForm',
        urlAjax: 'https://memoriaterrassa.cat/api/familiars/post',
        successBehavior: 'none', // oculta el formulario
        scrollOnSuccess: true, // opcional; ya se activa por defecto al usar 'hide'
        //scrollOffset: 96,             // si tienes un header fijo alto
        neteja: true,
      });
    }
  }
}
