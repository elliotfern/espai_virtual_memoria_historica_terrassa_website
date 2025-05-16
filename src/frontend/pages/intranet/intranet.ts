import { mostrarBotonsNomesAdmin } from '../../components/mostrarBotoAdmin/mostrarBotoAdmin';
import { nameUser } from '../../components/userName/userName';
import { getPageType } from '../../services/url/splitUrl';
import { login } from '../../services/auth/login';
import { logout } from '../../services/cookies/cookiesUtils';
import { auxiliars } from './auxiliars/auxiliars';
import { cargarTabla } from '../../components/taulaDades/taulaDades';
import { botonsEstat } from '../../components/taulaDades/botonsEstat';
import { transmissioDadesDB } from '../../services/fetchData/transmissioDades';

export function intranet() {
  const url = window.location.href;
  const pageType = getPageType(url);

  const btnLogout = document.querySelector('#btnSortir') as HTMLButtonElement;
  btnLogout?.addEventListener('click', () => {
    logout();
  });

  mostrarBotonsNomesAdmin();
  nameUser();

  if (pageType[1] === 'entrada') {
    const btnLogin = document.querySelector('#btnLogin') as HTMLButtonElement;

    btnLogin?.addEventListener('click', (event: Event) => {
      event.preventDefault();
      const userName = (document.querySelector('#username') as HTMLInputElement)?.value;
      const password = (document.querySelector('#password') as HTMLInputElement)?.value;

      if (userName && password) {
        login(userName, password);
      } else {
        console.log('Faltan datos para iniciar sesión.');
      }
    });
  } else if (pageType[1] === 'auxiliars') {
    auxiliars();
  } else if (pageType[1] === 'tots') {
    botonsEstat(pageType[1]);
    cargarTabla(pageType[1], 2);
  } else if (pageType[1] === 'represaliats') {
    botonsEstat(pageType[1]);
    cargarTabla(pageType[1], 2);
  } else if (pageType[1] === 'exiliats') {
    botonsEstat(pageType[1]);
    cargarTabla(pageType[1], 2);
  } else if (pageType[1] === 'cost-huma') {
    botonsEstat(pageType[1]);
    cargarTabla(pageType[1], 2);
  } else if (pageType[1] === 'crear-arxiu') {
    const llibre = document.getElementById('arxiuForm');
    if (llibre) {
      // Lanzar actualizador de datos
      llibre.addEventListener('submit', function (event) {
        transmissioDadesDB(event, 'POST', 'arxiuForm', '/api/fonts_documentals/post/arxiu');
      });
    }
  }
}
