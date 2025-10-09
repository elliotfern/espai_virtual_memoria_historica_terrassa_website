import { formatDatesForm } from '../../../services/formatDates/dates';
import { valorTextDesconegut } from '../../../services/formatDates/valorTextDesconegut';
import { LABELS_PRESO_MODEL, LABELS_TRI } from '../../../services/i18n/fitxaRepresaliatRepressio/label-tab12-presoModel';
import { DEFAULT_LANG, isLang, t } from '../../../services/i18n/i18n';
import { PresoModel } from '../../../types/PresoModel';

function triOption(val: string | number | null | undefined, lang: string): string {
  const l = isLang(lang) ? lang : DEFAULT_LANG;
  const v = String(val ?? '');
  if (v === '1') return t(LABELS_TRI, 'yes', l);
  if (v === '2') return t(LABELS_TRI, 'no', l);
  return t(LABELS_TRI, 'noData', l);
}

export function tab12PresoModel(input: PresoModel | PresoModel[], htmlContent: string, lang: string): string {
  const l = isLang(lang) ? lang : DEFAULT_LANG;
  const dades = Array.isArray(input) ? input : [input];

  dades.forEach((dada, idx) => {
    // Fechas (cuando faltan -> código 4 = unknownDate)
    const data_empresonament = valorTextDesconegut(dada.data_empresonament?.trim() ? formatDatesForm(dada.data_empresonament) : null, 4, l);
    const data_trasllat = valorTextDesconegut(dada.data_trasllat?.trim() ? formatDatesForm(dada.data_trasllat) : null, 4, l);
    const data_llibertat = valorTextDesconegut(dada.data_llibertat?.trim() ? formatDatesForm(dada.data_llibertat) : null, 4, l);

    // Tri-state + textos
    const trasllats = triOption(dada.trasllats, l);
    const llibertat = triOption(dada.llibertat, l);

    const lloc_trasllat = valorTextDesconegut(dada.lloc_trasllat, 1, l); // noData
    const modalitat = valorTextDesconegut(dada.modalitat, 1, l);
    const vicissituds = valorTextDesconegut(dada.vicissituds, 1, l);
    const observacions = valorTextDesconegut(dada.observacions, 1, l);

    const heading = t(LABELS_PRESO_MODEL, 'heading', l).replace('{n}', String(idx + 1));

    htmlContent += `
      <div class="negreta raleway" style="margin-bottom:10px; padding-bottom:30px">
        <h4 class="blau1">${heading}</h4>
        <div class="negreta raleway" style="margin-bottom:10px; padding-bottom:30px">
          <div style="margin-top:25px">
            <h5><span class="negreta blau1">${t(LABELS_PRESO_MODEL, 's1_title', l)}</span></h5>
            <p><span class='marro2'>${t(LABELS_PRESO_MODEL, 'prisonDate', l)}</span> <span class='blau1'>${data_empresonament}</span></p>
            <p><span class='marro2'>${t(LABELS_PRESO_MODEL, 'prisonMode', l)}</span> <span class='blau1'>${modalitat}</span></p>
            <p><span class='marro2'>${t(LABELS_PRESO_MODEL, 'freedom', l)}</span> <span class='blau1'>${llibertat}</span></p>
            <p><span class='marro2'>${t(LABELS_PRESO_MODEL, 'freedomDate', l)}</span> <span class='blau1'>${data_llibertat}</span></p>
          </div>

          <div style="margin-top:45px">
            <h5><span class="negreta blau1">${t(LABELS_PRESO_MODEL, 's2_title', l)}</span></h5>
            <p><span class='marro2'>${t(LABELS_PRESO_MODEL, 'transfers', l)}</span> <span class='blau1'>${trasllats}</span></p>
            <p><span class='marro2'>${t(LABELS_PRESO_MODEL, 'transferPlace', l)}</span> <span class='blau1'>${lloc_trasllat}</span></p>
            <p><span class='marro2'>${t(LABELS_PRESO_MODEL, 'transferDate', l)}</span> <span class='blau1'>${data_trasllat}</span></p>
          </div>

          <div style="margin-top:45px">
            <h5><span class="negreta blau1">${t(LABELS_PRESO_MODEL, 's3_title', l)}</span></h5>
            <p><span class='marro2'>${t(LABELS_PRESO_MODEL, 'vicissitudes', l)}</span> <span class='blau1'>${vicissituds}</span></p>
            <p><span class='marro2'>${t(LABELS_PRESO_MODEL, 'observations', l)}</span> <span class='blau1'>${observacions}</span></p>
          </div>
        </div>
      </div>
    `;
  });

  return htmlContent;
}
