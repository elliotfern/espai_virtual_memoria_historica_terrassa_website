export function joinValors(valors: unknown[], separador: string = ', ', envoltarAmbParentesis: boolean = false): string {
  const nets = valors.filter((v) => typeof v === 'string' && v.trim() !== '');

  if (nets.length === 0) return '';

  const text = nets.join(separador);
  return envoltarAmbParentesis ? `(${text})` : text;
}
