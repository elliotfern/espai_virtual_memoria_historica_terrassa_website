// src/pages/fitxaRepresaliat/tabs/tab2.ts
import { valorTextDesconegut } from '../../../services/formatDates/valorTextDesconegut';
import { t } from '../../../services/i18n/i18n';
import { LABELS_TAB4 } from '../../../services/i18n/labels-tab4';
import { LABELS_VTD } from '../../../services/i18n/valor-desconegut';
import type { Fitxa } from '../../../types/types';
import { partitsPolitics } from '../partitsPolitics';
import { sindicats } from '../sindicats';

type IdLike = string | number | number[] | null | undefined;

function normalizeIds(input: IdLike): number[] {
  const isValid = (n: number) => Number.isFinite(n) && n > 0;

  if (Array.isArray(input)) {
    return input.map((n) => Number(n)).filter(isValid);
  }

  if (typeof input === 'string') {
    const nums = input.match(/\d+/g) ?? [];
    return nums.map((s) => Number(s)).filter(isValid);
  }

  if (typeof input === 'number') {
    return isValid(input) ? [input] : [];
  }

  return [];
}

// helper para quitar "Filiació desconeguda", "Desconegut", etc.
function filterUnknownLabels(list: string[]): string[] {
  const deaccent = (s: string) => s.normalize('NFD').replace(/[\u0300-\u036f]/g, '');
  return list.filter((name) => {
    // quita paréntesis tipo "(-)" para comparar
    const base = deaccent(name)
      .toLowerCase()
      .replace(/\s*\([^)]*\)/g, '')
      .trim();
    return !(base === 'filiacio desconeguda' || base === 'desconegut' || base === 'desconeguda' || base === 'sense afiliacio' || base === 'no consta');
  });
}

export async function renderTab4(fitxa: Fitxa, label: string, lang: string): Promise<void> {
  const divInfo = document.getElementById('fitxa');
  if (!divInfo) return;

  const idsPartidos = normalizeIds(fitxa.filiacio_politica);
  const idsSindicats = normalizeIds(fitxa.filiacio_sindical);

  const [nombresPartidos, nombresSindicats] = await Promise.all([partitsPolitics(idsPartidos), sindicats(idsSindicats)]);

  const partitsLimpios = filterUnknownLabels(nombresPartidos);
  const sindicatsLimpios = filterUnknownLabels(nombresSindicats);

  const partitPolitic = partitsLimpios.length ? partitsLimpios.join(', ') : t(LABELS_VTD, 'unknownM', lang);

  const sindicat = sindicatsLimpios.length ? sindicatsLimpios.join(', ') : t(LABELS_VTD, 'unknownM', lang);

  divInfo.innerHTML = `
  <h3 class="titolSeccio">${label}</h3>

  <div style="margin-top:30px;margin-bottom:30px">
    <h5 class="titolSeccio2">${t(LABELS_TAB4, 'preWarHeading', lang)}</h5>
    <p><span class='marro2'>${t(LABELS_TAB4, 'politicalAffiliation', lang)}:</span> <span class='blau1'>${partitPolitic}</span></p>
    <p><span class='marro2'>${t(LABELS_TAB4, 'unionAffiliation', lang)}:</span> <span class='blau1'>${sindicat}</span></p>
  </div>

  <div style="margin-top:30px;margin-bottom:30px">
    <h5 class="titolSeccio2">${t(LABELS_TAB4, 'warDictatorshipHeading', lang)}</h5>
    <p><span class='blau1'>${valorTextDesconegut(fitxa.activitat_durant_guerra, 1, lang)}</span></p>
  </div>
`;
}
