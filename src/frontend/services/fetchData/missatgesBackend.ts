export function missatgesBackend({ tipus, missatge, contenidor, text, altreContenidor }: { tipus: 'success' | 'error'; missatge: string; contenidor: HTMLElement; text: HTMLElement; altreContenidor?: HTMLElement }): void {
  if (altreContenidor) {
    altreContenidor.style.display = 'none';
    altreContenidor.classList.remove('alert-success', 'alert-danger');
  }

  text.innerHTML = missatge;
  contenidor.style.display = 'block';

  contenidor.classList.remove('alert-success', 'alert-danger');
  contenidor.classList.add(tipus === 'success' ? 'alert-success' : 'alert-danger');

  setTimeout(() => {
    contenidor.style.display = 'none';
    contenidor.classList.remove('alert-success', 'alert-danger');
  }, 5000);
}
