// src/pages/fitxaRepresaliat/mostrarInformacion.ts
import { DOMAIN_API, DOMAIN_IMG } from '../../config/constants';
import { fmt, t } from '../../services/i18n/i18n';
import { LABELS_ACTIONS } from '../../services/i18n/label-action';
import { cache } from './cache';
import { renderTab1 } from './tabs/tab1';
import { renderTab2 } from './tabs/tab2';
import { renderTab3 } from './tabs/tab3';
import { renderTab4 } from './tabs/tab4';
import { renderTab5 } from './tabs/tab5';
import { renderTab6 } from './tabs/tab6';
import { renderTab7 } from './tabs/tab7';
import { renderTab9 } from './tabs/tab9';
// aquí iremos importando más tabs: tab2, tab3, etc.

export function mostrarInformacion(tabId: string, id: number, label: string, lang: string): void {
  const fitxa = cache.getFitxa();
  const fitxaFam = cache.getFitxaFam();

  if (!fitxa) {
    console.error('mostrarInformacion: no fitxa in cache');
    return;
  }

  // imatge represaliat
  // Seleccionamos la imagen con el ID 'imatgeRepresaliat'
  const imagen = document.getElementById('imatgeRepresaliat') as HTMLImageElement;

  const divAdditionalInfo = document.getElementById('info');
  if (!divAdditionalInfo) return;

  // Comprobamos si la variable fitxa.img tiene un valor válido
  if (fitxa.img && fitxa.img !== '' && fitxa.img !== null && imagen) {
    imagen.src = DOMAIN_IMG + `/assets_represaliats/img/${fitxa.img}.jpg`; // Si es válida, usamos la imagen de la variable
  } else {
    imagen.src = DOMAIN_IMG + `/assets_represaliats/img/foto_defecte.jpg`; // Si no, mostramos la imagen por defecto
  }

  // Aquí puedes mantener el contenido de divAdditionalInfo si es necesario

  const nom = fitxa.nom !== null ? fitxa.nom : '';
  const cognom1 = fitxa.cognom1 !== null ? fitxa.cognom1 : '';
  const cognom2 = fitxa.cognom2 !== null ? fitxa.cognom2 : '';
  const nombreCompleto = `${nom} ${cognom1} ${cognom2 ?? ''}`;

  divAdditionalInfo.innerHTML = `<h4 class="titolRepresaliat"> ${nombreCompleto}</h4>`; // No se limpia el contenido
  document.title = `${nombreCompleto} - Espai Virtual de la Memòria Històrica de Terrassa`;

  // Boto exportar
  // ==== Tipado seguro de "fitxa" (sin any) ====
  interface PersonaLite {
    id?: number | null;
    slug?: string | null;
    img?: string | null;
    nom?: string | null;
    cognom1?: string | null;
    cognom2?: string | null;
  }

  // Guard para comprobar que "fitxa" tiene forma de PersonaLite
  function isPersonaLite(x: unknown): x is PersonaLite {
    return typeof x === 'object' && x !== null && ('id' in x || 'slug' in x);
  }

  // Extrae id/slug de forma tipada
  let personaId: number | undefined;
  let personaSlug: string | undefined;
  if (isPersonaLite(fitxa)) {
    if (typeof fitxa.id === 'number') personaId = fitxa.id;
    if (typeof fitxa.slug === 'string') personaSlug = fitxa.slug;
  }

  // ==== Botones de exportación (individual) ====
  const EXPORT_CSV_URL = DOMAIN_API + '/export/persones_csv';
  const EXPORT_XLSX_URL = DOMAIN_API + '/export/persones_xlsx';
  const EXPORT_PDF_URL = DOMAIN_API + '/export/persones_pdf';

  // evita duplicados si se re-renderiza
  const oldInline = divAdditionalInfo.querySelector('.export-inline');
  if (oldInline) oldInline.remove();

  // contenedor horizontal
  const exportWrap = document.createElement('div');
  exportWrap.className = 'export-inline';
  exportWrap.style.cssText = 'display:flex; gap:.5rem; margin-top:.5rem; flex-wrap:wrap;';

  // helper para POST “invisible” sin any
  function postExport(action: string): void {
    // necesitamos al menos id o slug
    if (personaId === undefined && !personaSlug) return;

    const form = document.createElement('form');
    form.method = 'POST';
    form.action = action;

    const add = (name: string, value: string) => {
      const input = document.createElement('input');
      input.type = 'hidden';
      input.name = name;
      input.value = value;
      form.appendChild(input);
    };

    add('type', 'filtreIndividual'); // nuevo caso backend
    add('full', '1'); // quita esta línea si no quieres export “completo”

    if (personaId !== undefined) add('ids[]', String(personaId));
    else if (personaSlug) add('slugs[]', personaSlug);

    document.body.appendChild(form);
    form.submit();
    form.remove();
  }

  // botón CSV
  const btnCsv = document.createElement('button');
  btnCsv.type = 'button';
  btnCsv.className = 'btn btn-primary btn-custom-2';
  btnCsv.textContent = t(LABELS_ACTIONS, 'downloadCsv', lang);
  btnCsv.addEventListener('click', () => postExport(EXPORT_CSV_URL));

  // botón Excel
  const btnXlsx = document.createElement('button');
  btnXlsx.type = 'button';
  btnXlsx.className = 'btn btn-primary btn-custom-2';
  btnXlsx.textContent = t(LABELS_ACTIONS, 'downloadXlsx', lang);
  btnXlsx.addEventListener('click', () => postExport(EXPORT_XLSX_URL));

  // deshabilita si no hay identificador disponible
  if (personaId === undefined && !personaSlug) {
    btnCsv.disabled = true;
    btnXlsx.disabled = true;
    btnCsv.title = btnXlsx.title = 'Falta identificador de la persona (id o slug).';
  }

  exportWrap.append(btnCsv, btnXlsx);
  divAdditionalInfo.appendChild(exportWrap);

  // boto PDF
  // crea botón PDF
  const btnPdf = document.createElement('button');
  btnPdf.type = 'button';
  btnPdf.className = 'btn btn-primary btn-custom-2';
  btnPdf.textContent = t(LABELS_ACTIONS, 'downloadPdf', lang);
  btnPdf.addEventListener('click', () => {
    if (personaId === undefined && !personaSlug) return;

    const form = document.createElement('form');
    form.method = 'POST';
    form.action = EXPORT_PDF_URL;

    const add = (name: string, value: string) => {
      const input = document.createElement('input');
      input.type = 'hidden';
      input.name = name;
      input.value = value;
      form.appendChild(input);
    };

    // El backend usa filtreIndividual internamente
    add('type', 'filtreIndividual');
    if (personaId !== undefined) add('ids[]', String(personaId));
    else if (personaSlug) add('slugs[]', personaSlug);

    document.body.appendChild(form);
    form.submit();
    form.remove();
  });

  // añade el botón PDF a tu contenedor existente junto a CSV/XLSX:
  exportWrap.appendChild(btnPdf);

  // fi botons exportacio dades
  const msg = fmt(t(LABELS_ACTIONS, 'tabNotAvailable', lang), { tab: label });

  switch (tabId) {
    case 'tab1':
      renderTab1(fitxa, label, lang);
      break;
    case 'tab2':
      renderTab2(fitxa, fitxaFam, label, lang);
      break;
    case 'tab3':
      renderTab3(fitxa, label, lang);
      break;
    case 'tab4':
      renderTab4(fitxa, label, lang);
      break;
    case 'tab5':
      renderTab5(fitxa, label, lang);
      break;
    case 'tab6':
      renderTab6(fitxa, lang);
      break;
    case 'tab9':
      renderTab9(fitxa, label, lang);
      break;
    case 'tab7':
      renderTab7(fitxa, label, lang);
      break;
    default:
      document.getElementById('fitxa')!.innerHTML = `<p>${msg}</p>`;
      break;
  }
}
