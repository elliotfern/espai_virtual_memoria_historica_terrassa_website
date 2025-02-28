export const obtenirIdioma = (): string => {
  const idiomasPermitidos = ['es', 'fr', 'en', 'it', 'pt'];
  const pathArray = window.location.pathname.split('/');
  const idiomaDetectado = pathArray[1];
  const idiomaFinal = idiomasPermitidos.includes(idiomaDetectado) ? idiomaDetectado : 'ca';
  return idiomaFinal;
};

let traducciones: Record<string, string> = {}; // Ya no la exportamos directamente

export const carregarTraduccions = async (): Promise<void> => {
  const idioma = obtenirIdioma();

  const devDirectory = `https://${window.location.hostname}`;
  const url = `${devDirectory}/locales/${idioma}.json`;

  try {
    const response = await fetch(url); // Ojo con la ruta, sin `../../`
    if (!response.ok) throw new Error('No se pudo cargar el archivo de idioma');
    traducciones = await response.json();
  } catch (error) {
    console.error('Error cargando traducciones:', error);
    traducciones = {}; // En caso de error, dejamos un objeto vacío
  }
};

// Nueva función para obtener traducciones en otros archivos
export const getTraducciones = (): Record<string, string> => traducciones;
