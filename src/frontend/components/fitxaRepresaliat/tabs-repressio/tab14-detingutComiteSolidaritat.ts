import { valorTextDesconegut } from '../../../services/formatDates/valorTextDesconegut';
import { LABELS_DETINGUT_COMITE } from '../../../services/i18n/fitxaRepresaliatRepressio/label-tab14-detingutCS';
import { DEFAULT_LANG, isLang, t } from '../../../services/i18n/i18n';
import { DetingutComiteData } from '../../../types/DetingutComiteSolidaritat';

export function tab14DetingutComiteSolidaritat(dades: DetingutComiteData[], htmlContent: string, lang: string): string {
  const l = isLang(lang) ? lang : DEFAULT_LANG;

  dades.forEach((dada, index) => {
    // any_detencio: si falta -> 4 = unknownDate (puedes cambiar a 1 = noData si prefieres)
    const anyDetencio = valorTextDesconegut(dada.any_detencio, 4, l);
    const motiu_empresonament = valorTextDesconegut(dada.motiu, 1, l);
    const advocat = valorTextDesconegut(dada.advocat, 1, l);
    const observacions = valorTextDesconegut(dada.observacions, 1, l);

    const heading = t(LABELS_DETINGUT_COMITE, 'heading', l).replace('{n}', String(index + 1));

    htmlContent += `
      <div class="negreta raleway" style="margin-bottom:10px; padding-bottom:30px">
        <h4 class="blau1">${heading}</h4>
        <div class="negreta raleway">
          <div style="margin-top:25px">
            <p><span class='marro2'>${t(LABELS_DETINGUT_COMITE, 'yearOfDetention', l)} </span><span class='blau1'>${anyDetencio}</span></p>
            <p><span class='marro2'>${t(LABELS_DETINGUT_COMITE, 'arrestReason', l)}</span> <span class='blau1'>${motiu_empresonament}</span></p>
            <p><span class='marro2'>${t(LABELS_DETINGUT_COMITE, 'lawyer', l)}</span> <span class='blau1'>${advocat}</span></p>
            <p><span class='marro2'>${t(LABELS_DETINGUT_COMITE, 'observations', l)}</span> <span class='blau1'>${observacions}</span></p>
          </div>
        </div>
      </div>
    `;
  });

  return htmlContent;
}
