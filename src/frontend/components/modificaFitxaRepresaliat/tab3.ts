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
  const div = document.createElement('div');
  div.className = 'd-flex gap-2 mt-3 mb-3'; // usa flexbox + espacio entre hijos

  const link = document.createElement('a');
  link.href = `https://memoriaterrassa.cat/gestio/familiars/nou-familiar/${idPersona}`;
  link.className = 'btn btn-success';
  link.textContent = 'Afegir familiar';
  link.target = '_blank';

  const updateBtn = document.createElement('button');
  updateBtn.className = 'btn btn-secondary';
  updateBtn.textContent = 'Actualitza taula';
  updateBtn.style.marginRight = '10px';
  updateBtn.style.marginLeft = '10px';
  updateBtn.onclick = (event) => {
    event.preventDefault(); // Evita recargar la página

    // Limpiar la tabla antes de volver a crearla
    const cont = document.getElementById('quadreFamiliars'); // Usa el contenedor correcto donde va la tabla
    if (cont) {
      cont.innerHTML = ''; // Limpiar tabla existente
    }

    taulaFamiliars(Number(idPersona)); // Crear la tabla nuevamente
  };

  div.appendChild(link);
  div.appendChild(updateBtn);

  const container = document.getElementById('botonsFamiliars');
  if (container) {
    container.innerHTML = ''; // Limpiar los botones previos
    container.appendChild(div); // Añadir los nuevos botones
  }
}
