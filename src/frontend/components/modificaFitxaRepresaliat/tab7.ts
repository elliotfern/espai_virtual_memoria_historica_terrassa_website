import { Fitxa } from '../../types/types';
import { taulaBibliografia } from './taulaBibliografia';
import { taulaArxius } from './taulaArxius';

export function tab7(fitxa?: Fitxa) {
  if (fitxa) {
    const quadreFonts1 = document.getElementById('quadreFonts1');
    const quadreFonts2 = document.getElementById('quadreFonts2');

    if (quadreFonts1) {
      quadreFonts1.style.display = 'block';
      const h4 = document.createElement('h4');
      h4.textContent = 'Llistat de bibliografia';
      quadreFonts1.prepend(h4);
      crearBotoAfegirBibliografia(fitxa.id);
      taulaBibliografia(fitxa.id);
    }

    if (quadreFonts2) {
      quadreFonts2.style.display = 'block';
      const h4 = document.createElement('h4');
      h4.textContent = "Llistat d'arxius";
      quadreFonts2.prepend(h4);
      crearBotoAfegirArxiu(fitxa.id);
      taulaArxius(fitxa.id);
    }
  } else {
    const avisFonts = document.getElementById('avisFonts');
    if (avisFonts) {
      avisFonts.style.display = 'block';
      avisFonts.textContent = 'Abans de poder afegir les fonts bibliogràfiques i arxivístiques, primer has de crear la fitxa. Un cop hagis creat la fitxa, modifica-la per continuar amb el procés.';
    }
  }
}

function crearBotoAfegirBibliografia(idPersona: number | string) {
  // Crear el contenedor div
  const div = document.createElement('div');
  div.className = 'd-flex gap-2 mt-3 mb-3';
  div.style.marginTop = '20px';
  div.style.marginBottom = '20px';

  // Crear el enlace <a>
  const link = document.createElement('a');
  link.href = `https://memoriaterrassa.cat/gestio/familiars/nou-familiar/${idPersona}`;
  link.className = 'btn btn-success';
  link.textContent = 'Afegir bibliografia a la fitxa del repressaliat';
  link.target = '_blank';

  // Botón "Actualitza"
  const updateBtn = document.createElement('button');
  updateBtn.className = 'btn btn-secondary';
  updateBtn.textContent = 'Actualitza taula';
  updateBtn.style.marginRight = '10px';
  updateBtn.onclick = (event) => {
    event.preventDefault(); // Evita recargar la página
    taulaBibliografia(Number(idPersona));
  };

  // Añadir ambos botones al div
  div.appendChild(link);
  div.appendChild(updateBtn);

  // Añadir el link al div
  div.appendChild(link);

  // Insertar el div en el DOM, por ejemplo en un contenedor con id "botonsFamiliars"
  const container = document.getElementById('botonsFonts1');
  if (container) {
    container.appendChild(div);
  }
}

function crearBotoAfegirArxiu(idPersona: number | string) {
  // Crear el contenedor div
  const div = document.createElement('div');
  div.className = 'd-flex gap-2 mt-3 mb-3';
  div.style.marginTop = '20px';
  div.style.marginBottom = '20px';

  // Crear el enlace <a>
  const link = document.createElement('a');
  link.href = `https://memoriaterrassa.cat/gestio/familiars/nou-familiar/${idPersona}`;
  link.className = 'btn btn-success';
  link.textContent = 'Afegir font arxivística a la fitxa del repressaliat';
  link.target = '_blank';

  // Botón "Actualitza"
  const updateBtn = document.createElement('button');
  updateBtn.className = 'btn btn-secondary';
  updateBtn.textContent = 'Actualitza taula';
  updateBtn.style.marginRight = '10px';
  updateBtn.onclick = (event) => {
    event.preventDefault(); // Evita recargar la página
    taulaArxius(Number(idPersona));
  };

  // Añadir ambos botones al div
  div.appendChild(link);
  div.appendChild(updateBtn);

  // Añadir el link al div
  div.appendChild(link);

  // Insertar el div en el DOM, por ejemplo en un contenedor con id "botonsFamiliars"
  const container = document.getElementById('botonsFonts2');
  if (container) {
    container.appendChild(div);
  }
}
