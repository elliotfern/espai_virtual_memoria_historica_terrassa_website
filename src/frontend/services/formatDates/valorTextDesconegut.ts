export function valorTextDesconegut(valor: string | null, perDefecte: number = 1): string {
  const textos: Record<number, string> = {
    1: 'Sense dades',
    2: 'Desconegut',
    5: 'Desconeguda',
    3: '',
    4: 'Data desconeguda',
  };

  return typeof valor === 'string' && valor.trim() !== '' ? valor : textos[perDefecte];
}
