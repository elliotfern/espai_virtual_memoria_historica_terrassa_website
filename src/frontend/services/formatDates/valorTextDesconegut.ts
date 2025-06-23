export function valorTextDesconegut(valor: string | null, perDefecte: number = 1): string {
  const textos: Record<number, string> = {
    1: 'Sense dades',
    2: 'Desconegut',
    5: 'Desconeguda',
    3: '',
    4: 'Data desconeguda',
    6: 'No consta cap empresonament',
    7: "No consta que marxés a l'exili",
  };

  return typeof valor === 'string' && valor.trim() !== '' ? valor : textos[perDefecte];
}
