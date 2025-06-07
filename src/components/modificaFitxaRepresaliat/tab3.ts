import { Fitxa } from '../../types/types';
import { auxiliarSelect } from '../../services/fetchData/auxiliarSelect';
import { taulaFamiliars } from './taulaFamiliars';

export function tab3(fitxa?: Fitxa) {
  auxiliarSelect(fitxa?.estat_civil_id ?? 4, 'estats_civils', 'estat_civil', 'estat_cat', '4');

  if (fitxa) {
    crearBotoAfegirFamiliar(fitxa.id);
    taulaFamiliars(fitxa.id);
  } else {
    const avisFamiliars = document.getElementById('avisFamiliars');
    if (avisFamiliars) {
      avisFamiliars.style.display = 'block';
      avisFamiliars.textContent = 'Abans de poder afegir els familiars, primer has de crear la fitxa. Un cop hagis creat la fitxa, modifica-la per continuar amb el procés.';
    }
  }
}

function crearBotoAfegirFamiliar(idPersona: number | string) {
  // Crear el contenedor div
  const div = document.createElement('div');
  div.className = 'col-md-4';
  div.style.marginTop = '20px';
  div.style.marginBottom = '20px';

  // Crear el enlace <a>
  const link = document.createElement('a');
  link.href = `https://memoriaterrassa.cat/gestio/familiars/nou-familiar/${idPersona}`;
  link.className = 'btn btn-success';
  link.textContent = 'Afegir familiar';
  link.target = '_blank';

  // Botón "Actualitza"
  const updateBtn = document.createElement('button');
  updateBtn.className = 'btn btn-secondary';
  updateBtn.textContent = 'Actualitza taula';
  updateBtn.style.marginRight = '10px';
  updateBtn.onclick = (event) => {
    event.preventDefault(); // Evita recargar la página
    taulaFamiliars(Number(idPersona));
  };

  // Añadir ambos botones al div
  div.appendChild(link);
  div.appendChild(updateBtn);

  // Añadir el link al div
  div.appendChild(link);

  // Insertar el div en el DOM, por ejemplo en un contenedor con id "botonsFamiliars"
  const container = document.getElementById('botonsFamiliars');
  if (container) {
    container.appendChild(div);
  }
}
