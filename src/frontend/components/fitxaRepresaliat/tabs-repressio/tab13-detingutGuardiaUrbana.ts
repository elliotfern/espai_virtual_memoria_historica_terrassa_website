// 3) Render
import { formatDatesForm } from '../../../services/formatDates/dates';
import { valorTextDesconegut } from '../../../services/formatDates/valorTextDesconegut';
import { LABELS_TRI } from '../../../services/i18n/fitxaRepresaliatRepressio/label-tab12-presoModel';
import { LABELS_DETINGUT_GU } from '../../../services/i18n/fitxaRepresaliatRepressio/label-tab13-detingutGuardiaUrbana';
import { DEFAULT_LANG, isLang, t } from '../../../services/i18n/i18n';
import { DetingutGUData } from '../../../types/DetingutGuardiaUrbana';

function triOption(val: number | string | null | undefined, lang: string): string {
  const l = isLang(lang) ? lang : DEFAULT_LANG;
  const v = String(val ?? '');
  if (v === '1') return t(LABELS_TRI, 'yes', l);
  if (v === '2') return t(LABELS_TRI, 'no', l);
  return t(LABELS_TRI, 'noData', l);
}

export function tab13DetingutGU(dades: DetingutGUData[], htmlContent: string, lang: string): string {
  const l = isLang(lang) ? lang : DEFAULT_LANG;

  dades.forEach((dada, index) => {
    const data_empresonament = valorTextDesconegut(
      dada.data_empresonament?.trim() ? formatDatesForm(dada.data_empresonament) : null,
      4, // unknownDate
      l
    );
    const data_sortida = valorTextDesconegut(dada.data_sortida?.trim() ? formatDatesForm(dada.data_sortida) : null, 4, l);
    const motiu_empresonament = valorTextDesconegut(dada.motiu_empresonament, 1, l);
    const qui_ordena_detencio = valorTextDesconegut(dada.qui_ordena_detencio, 1, l);
    const nom_institucio = valorTextDesconegut(dada.nom_institucio, 1, l);
    const grup = valorTextDesconegut(dada.grup, 1, l);
    const topText = triOption(dada.top ?? null, l);
    const observacions = valorTextDesconegut(dada.observacions, 1, l);

    const heading = t(LABELS_DETINGUT_GU, 'heading', l).replace('{n}', String(index + 1));

    htmlContent += `
      <div class="negreta raleway" style="margin-bottom:10px; padding-bottom:30px">
        <h4 class="blau1">${heading}</h4>
        <div class="negreta raleway">
          <div style="margin-top:25px">
            <p><span class='marro2'>${t(LABELS_DETINGUT_GU, 'arrestDate', l)} </span><span class='blau1'>${data_empresonament}</span></p>
            <p><span class='marro2'>${t(LABELS_DETINGUT_GU, 'releaseDate', l)}</span> <span class='blau1'>${data_sortida}</span></p>
            <p><span class='marro2'>${t(LABELS_DETINGUT_GU, 'arrestReason', l)}</span> <span class='blau1'>${motiu_empresonament}</span></p>
            <p><span class='marro2'>${t(LABELS_DETINGUT_GU, 'detentionOrderedBy', l)}</span> <span class='blau1'>${qui_ordena_detencio} - ${nom_institucio}</span></p>
            <p><span class='marro2'>${t(LABELS_DETINGUT_GU, 'orderingInstitutionType', l)}</span> <span class='blau1'>${grup}</span></p>
            <p><span class='marro2'>${t(LABELS_DETINGUT_GU, 'orderedByTOP', l)}</span> <span class='blau1'>${topText}</span></p>
            <p><span class='marro2'>${t(LABELS_DETINGUT_GU, 'observations', l)}</span> <span class='blau1'>${observacions}</span></p>
          </div>
        </div>
      </div>
    `;
  });

  return htmlContent;
}
