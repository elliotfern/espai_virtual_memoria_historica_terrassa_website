export function formatDates(fecha: string): string {
  const date = new Date(fecha);
  const dia = String(date.getDate()).padStart(2, '0'); // Día con dos dígitos
  const mes = String(date.getMonth() + 1).padStart(2, '0'); // Mes (1-12), ajustamos con +1 porque getMonth() retorna un valor entre 0 y 11
  const any = date.getFullYear(); // Año completo

  return `${dia}-${mes}-${any}`;
}

export function formatDatesForm(fecha: string): string {
  const date = new Date(fecha);
  const dia = String(date.getDate()).padStart(2, '0'); // Día con dos dígitos
  const mes = String(date.getMonth() + 1).padStart(2, '0'); // Mes (1-12), ajustamos con +1 porque getMonth() retorna un valor entre 0 y 11
  const any = date.getFullYear(); // Año completo

  return `${dia}/${mes}/${any}`;
}
