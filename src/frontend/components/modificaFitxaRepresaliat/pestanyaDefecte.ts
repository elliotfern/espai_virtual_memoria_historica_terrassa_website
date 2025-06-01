export function mostrarPestanyaPerDefecte(): void {
  // Ocultar todas las pestañas
  const tabcontent = document.getElementsByClassName('tabcontent') as HTMLCollectionOf<HTMLElement>;
  for (let i = 0; i < tabcontent.length; i++) {
    tabcontent[i].style.display = 'none';
  }

  // Mostrar tab1
  const primeraPestanya = document.getElementById('tab1');
  if (primeraPestanya) {
    primeraPestanya.style.display = 'block';
  }

  // Marcar el primer botón como activo
  const tabLinks = document.getElementsByClassName('tablinks');
  if (tabLinks.length > 0) {
    (tabLinks[0] as HTMLElement).classList.add('active');
  }
}
