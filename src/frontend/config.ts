
export const categorias: { [key: string]: string } = {
  1: "Afusellat",
  2: "Deportat",
  3: "Mort en combat",
  4: "",
  5: "",
  6: "",
  7: "",
  8: "",
  9: "",
  10: "Exiliat",
};

// Función para convertir fecha de formato DD/MM/YYYY a YYYY-MM-DD
export function convertirFecha(fecha: string): string | null {
  if (!fecha) return null;
  const partes = fecha.split('/');
  if (partes.length !== 3) return null;
  return `${partes[2]}-${partes[1]}-${partes[0]}`;
}

// Función para calcular la edad al morir
export function calcularEdadAlMorir(fechaNacimiento: string, fechaDefuncion: string): number | null {
  const nacimiento = new Date(fechaNacimiento);
  const defuncion = new Date(fechaDefuncion);

  if (isNaN(nacimiento.getTime()) || isNaN(defuncion.getTime())) return null;

  let edad = defuncion.getFullYear() - nacimiento.getFullYear();

  const mesNacimiento = nacimiento.getMonth();
  const diaNacimiento = nacimiento.getDate();
  const mesDefuncion = defuncion.getMonth();
  const diaDefuncion = defuncion.getDate();

  if (mesDefuncion < mesNacimiento || (mesDefuncion === mesNacimiento && diaDefuncion < diaNacimiento)) {
      edad--;
  }

  return edad;
}