export function pestanyesInformacio(evt: MouseEvent, tabName: string): void {
  // Ocultar todas las pestañas
  const tabcontent = document.getElementsByClassName('tabcontent') as HTMLCollectionOf<HTMLElement>;
  for (let i = 0; i < tabcontent.length; i++) {
    tabcontent[i].style.display = 'none';
  }

  // Quitar clase active de todos los botones
  const tablinks = document.getElementsByClassName('tablinks') as HTMLCollectionOf<HTMLElement>;
  for (let i = 0; i < tablinks.length; i++) {
    tablinks[i].classList.remove('active');
  }

  // Mostrar la pestaña correspondiente
  const currentTab = document.getElementById(tabName);
  if (currentTab) {
    currentTab.style.display = 'block';
  }

  // Marcar el botón clicado como activo
  const target = evt.currentTarget as HTMLElement;
  if (target) {
    target.classList.add('active');
  }
}
