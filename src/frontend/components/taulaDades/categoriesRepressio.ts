import { API_URLS } from '../../services/api/ApiUrls';

type Category = {
  id: number;
  name: string;
};

export async function categoriesRepressio(lang: string): Promise<Category[]> {
  const response = await fetch(API_URLS.GET.CATEGORIES_REPRESSIO(lang));
  if (!response.ok) throw new Error('Error al obtener las categorías');
  const result = await response.json();
  return result.data;
}
