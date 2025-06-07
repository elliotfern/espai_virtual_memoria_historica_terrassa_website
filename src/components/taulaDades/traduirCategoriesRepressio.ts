type Category = { id: number; name: string };

/**
 * Convierte un string tipo "{1,2,3}" en una lista de nombres de categorías.
 * @param categoriaString El string de categorías tipo "{1,2,3}"
 * @param categorias El array de categorías con id y name
 * @returns Una string con los nombres separados por comas
 */
export function traduirCategoriesRepressio(categoriaString: string, categorias: Category[]): string {
  if (!categoriaString) return '';

  const categoriaIds = categoriaString.replace(/[{}]/g, '').split(',').map(Number);

  // Crear mapa para acceso rápido por id
  const categoriasMap = categorias.reduce((acc, cat) => {
    acc[cat.id] = cat.name;
    return acc;
  }, {} as { [key: number]: string });

  return categoriaIds
    .map((id) => categoriasMap[id] || '')
    .filter(Boolean)
    .join(', ');
}

/**
 * Convierte un string tipo "{1,2,3}" en un array de nombres de categorías.
 * @param categoriaString El string de categorías tipo "{1,2,3}"
 * @param categorias El array de categorías con id y name
 * @returns Un array con los nombres de categorías encontrados
 */
export function traduirCategoriesRepressioArray(categoriaString: string, categorias: Category[]): string[] {
  if (!categoriaString) return [];

  const categoriaIds = categoriaString.replace(/[{}]/g, '').split(',').map(Number);

  // Crear mapa para acceso rápido por id
  const categoriasMap = categorias.reduce((acc, cat) => {
    acc[cat.id] = cat.name;
    return acc;
  }, {} as { [key: number]: string });
  console.log('Mapa categorías:', categoriasMap);

  return categoriaIds.map((id) => categoriasMap[id] || '').filter(Boolean);
}
