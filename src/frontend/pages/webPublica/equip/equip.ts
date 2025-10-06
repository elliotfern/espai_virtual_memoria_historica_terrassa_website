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
}

interface ApiResponse<T> {
  status: string;
  message: string;
  data: T;
}

export async function equip(lang: string, slug: string) {
  let data: Partial<Fitxa> = {
    comarca: 0,
    provincia: 0,
    comunitat: 0,
    estat: 0,
  };

  const response = await fetchDataGet<ApiResponse<Fitxa>>(API_URLS.GET.USUARI_WEB_ID(slug, lang), true);

  if (!response || !response.data) return;
  data = response.data;

  renderFormInputs(data);
}
