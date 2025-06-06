import { API_URLS } from '../../services/api/ApiUrls';

type Category = {
  id: number;
  name: string;
};

export async function categoriesRepressio(lang: string): Promise<Category[]> {
  const response = await fetch(API_URLS.GET.CATEGORIES_REPRESSIO(lang));
  if (!response.ok) throw new Error('Error al obtener las categorías');
  const result = await response.json(); // Aquí obtienes el objeto completo
  return result.data; // Accedes a la propiedad "data" donde están las categorías
}
