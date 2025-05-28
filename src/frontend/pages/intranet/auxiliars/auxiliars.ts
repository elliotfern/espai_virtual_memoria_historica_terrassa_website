import { taulaDadesUsuaris } from './taulaUsuaris';
import { transmissioDadesDB } from '../../../services/fetchData/transmissioDades';
import { getPageType } from '../../../services/url/splitUrl';
import { taulaMunicipis } from './taulaMunicipis';
import { taulaPartits } from './taulaPartits';
import { taulaSindicats } from './taulaSindicats';

export function auxiliars() {
  const url = window.location.href;
  const pageType = getPageType(url);

  if (pageType[2] === 'llistat-usuaris') {
    taulaDadesUsuaris();
  } else if (pageType[2] === 'nou-usuari') {
    const peli = document.getElementById('usuariForm');
    if (peli) {
      peli.addEventListener('submit', function (event) {
        transmissioDadesDB(event, 'POST', 'usuariForm', '/api/auxiliars/post/usuari');
      });
    }
  } else if (pageType[2] === 'modifica-usuari') {
    const peli = document.getElementById('usuariForm');
    if (peli) {
      peli.addEventListener('submit', function (event) {
        transmissioDadesDB(event, 'PUT', 'usuariForm', '/api/auxiliars/put/usuari');
      });
    }
  } else if (pageType[2] === 'llistat-municipis') {
    taulaMunicipis();
  } else if (pageType[2] === 'llistat-partits-politics') {
    taulaPartits();
  } else if (pageType[2] === 'llistat-sindicats') {
    taulaSindicats();
  } else if (pageType[2] === 'nou-avatar-usuari') {
    document.getElementById('usuariForm')?.addEventListener('submit', async (e) => {
      e.preventDefault();

      const nomImatge = (document.getElementById('nomImatge') as HTMLInputElement).value;
      const tipus = (document.getElementById('tipus') as HTMLSelectElement).value;
      const fileInput = document.getElementById('fileToUpload') as HTMLInputElement;
      const file = fileInput.files?.[0];

      if (!file) {
        alert('Has de seleccionar un fitxer!');
        return;
      }

      if (!nomImatge) {
        alert("Has d'escriure un nom d'imatge!");
        return;
      }

      const formData = new FormData();
      formData.append('fileToUpload', file);
      formData.append('nomImatge', nomImatge);
      formData.append('tipus', tipus);

      try {
        const response = await fetch('/api/auxiliars/post/usuariAvatar', {
          method: 'POST',
          body: formData,
        });

        const result = await response.json();
        const missatgeOk = document.getElementById('okMessage');
        const missatgeErr = document.getElementById('errMessage');

        if (result.status === 'success') {
          if (missatgeOk && missatgeErr) {
            missatgeOk.style.display = 'block';
            missatgeErr.style.display = 'none';
            missatgeOk.textContent = "L'operació s'ha realizat correctament a la base de dades.";
          } else {
            if (missatgeOk && missatgeErr) {
              missatgeErr.style.display = 'block';
              missatgeOk.style.display = 'none';
              missatgeErr.textContent = "L'operació no s'ha pogut realizar correctament a la base de dades.";
            }
          }
        }
      } catch (error) {
        console.error('Error al pujar la imatge:', error);
      }
    });
  }
}
