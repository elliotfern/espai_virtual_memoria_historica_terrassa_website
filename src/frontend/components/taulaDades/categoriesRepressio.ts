type Category = {
  id: number;
  name: string;
};

export async function categoriesRepressio(lang: string): Promise<Category[]> {
  const response = await fetch(`/api/auxiliars/get/categoriesRepressio?lang=${lang}`);
  if (!response.ok) throw new Error('Error al obtener las categorías');
  return await response.json();
}
