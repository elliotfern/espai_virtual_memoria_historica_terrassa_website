import { Fitxa } from '../../types/types';
import { carregarBiografies } from './carregarBiografies';

export function tab6(fitxa?: Fitxa) {
  if (fitxa) {
    carregarBiografies(fitxa.id);
  } else {
    const avisBio = document.getElementById('avisBiografia');
    if (avisBio) {
      avisBio.style.display = 'block';
      avisBio.textContent = 'Abans de poder afegir la biografia, primer has de crear la fitxa. Un cop hagis creat la fitxa, modifica-la per continuar amb el procés.';
    }
  }
}
