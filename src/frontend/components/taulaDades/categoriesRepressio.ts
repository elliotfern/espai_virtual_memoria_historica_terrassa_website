type Category = {
  id: number;
  name: string;
};

export async function categoriesRepressio(lang: string): Promise<Category[]> {
  const response = await fetch(`/api/auxiliars/get/categoriesRepressio?lang=${lang}`);
  if (!response.ok) throw new Error('Error al obtener las categorías');
  return await response.json();
}

/*
  1: 'Afusellat',
  2: 'Deportat',
  3: 'Mort en combat',
  4: 'Mort civil',
  5: 'Represàlia republicana',
  6: 'Processat/Empresonat',
  7: 'Depurat',
  8: 'Dona',
  9: ' ',
  10: 'Exiliat',
  11: 'Represaliats pendents classificació',
  12: 'Detinguts Presó Model',
  13: 'Detinguts Guàrdia Urbana',
  14: 'Detinguts Comitè Solidaritat',
  15: 'Responsabilitats Polítiques',
  16: 'Funcionaris depurats',
  17: 'Tribunal Orden Público',
  */
