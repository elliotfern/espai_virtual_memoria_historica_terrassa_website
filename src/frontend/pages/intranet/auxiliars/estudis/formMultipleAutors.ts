import { fetchDataGet } from '../../../../services/fetchData/fetchDataGet';

interface AutorItem {
  id: number;
  nom: string;
}

interface ApiResponse<T> {
  status: string;
  message: string;
  data: T;
}

export async function auxiliarSelectMultipleAutors(selectedValues: number[] = [], selectId = 'autors'): Promise<void> {
  const select = document.getElementById(selectId) as HTMLSelectElement | null;
  if (!select) return;

  const response = await fetchDataGet<ApiResponse<AutorItem[]>>(`https://${window.location.host}/api/auxiliars/get/autors_estudis`, true);

  if (!response || !response.data || !Array.isArray(response.data)) {
    select.innerHTML = '';
    return;
  }

  const selectedSet = new Set(selectedValues.map(String));

  select.innerHTML = response.data
    .map((autor) => {
      const selected = selectedSet.has(String(autor.id)) ? ' selected' : '';
      return `<option value="${autor.id}"${selected}>${autor.nom}</option>`;
    })
    .join('');
}
