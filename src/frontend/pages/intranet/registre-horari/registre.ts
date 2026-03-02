import { getPageType } from '../../../services/url/splitUrl';
import { formHores } from './formRegistre';
import { taulaHoresMe } from './taulaRegistreHoresUsuari';

export function registreHorari() {
  const url = window.location.href;
  const pageType = getPageType(url);

  // Según tus ejemplos:
  // /gestio/registre-horari                     => pageType[2] = undefined (o '')
  // /gestio/registre-horari/nou-registre        => pageType[2] = 'nou-registre'
  // /gestio/registre-horari/modifica-registre/5 => pageType[2] = 'modifica-registre', pageType[3] = '5'
  const action = (pageType[2] ?? '').toString();

  if (action === 'nou-registre') {
    formHores(false);
    return;
  }

  if (action === 'modifica-registre') {
    const id = Number(pageType[3]);
    if (!Number.isFinite(id) || id <= 0) {
      // Si quieres: pinta un error en #errMessage
      console.error('ID invàlid a modifica-registre');
      return;
    }
    formHores(true, id);
    return;
  }

  // ✅ Ruta base (o cualquier otra) => tabla
  taulaHoresMe();
}
