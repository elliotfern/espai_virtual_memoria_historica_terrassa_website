import { formatDatesForm } from '../../../services/formatDates/dates';
import { valorTextDesconegut } from '../../../services/formatDates/valorTextDesconegut';
import { LABELS_MORT_COMBAT } from '../../../services/i18n/fitxaRepresaliatRepressio/label-tab3-mortEnCombat';
import { t } from '../../../services/i18n/i18n';
import { MortEnCombatData } from '../../../types/mortEnCombatData';

export function tab3MortCombat(dada: MortEnCombatData, htmlContent: string, lang: string): string {
  const condicio = valorTextDesconegut(dada.condicio, 2, lang);
  const bandol = valorTextDesconegut(dada.bandol, 2, lang);
  const any_lleva = valorTextDesconegut(dada.any_lleva, 2, lang);

  const unitat_inicial = valorTextDesconegut(dada.unitat_inicial, 2, lang);
  const cos = valorTextDesconegut(dada.cos, 2, lang);
  const unitat_final = valorTextDesconegut(dada.unitat_final, 2, lang);
  const graduacio_final = valorTextDesconegut(dada.graduacio_final, 2, lang);
  const periple_militar = valorTextDesconegut(dada.periple_militar, 1, lang); // "Sense dades"

  // Femenino/fecha desconocida según tu intención:
  const circumstancia_mort = valorTextDesconegut(dada.circumstancia_mort, 5, lang); // "Desconeguda" (F)

  // Fechas -> usar código 4 ("Data desconeguda") cuando falte
  const desaparegut_data = valorTextDesconegut(dada.desaparegut_data?.trim() ? formatDatesForm(dada.desaparegut_data) : null, 4, lang);

  const desaparegut_lloc = valorTextDesconegut(dada.desaparegut_lloc, 2, lang);

  const desaparegut_data_aparicio = valorTextDesconegut(dada.desaparegut_data_aparicio?.trim() ? formatDatesForm(dada.desaparegut_data_aparicio) : null, 4, lang);

  const desaparegut_lloc_aparicio = valorTextDesconegut(dada.desaparegut_lloc_aparicio, 2, lang);

  const fragment =
    dada.reaparegut === 1
      ? `
          <div style="margin-top:25px">
            <h5><span class="negreta blau1">${t(LABELS_MORT_COMBAT, 's4_title', lang)}</span></h5>
            <p><span class='marro2'>${t(LABELS_MORT_COMBAT, 'reappearanceDate', lang)}</span> <span class='blau1'>${desaparegut_data_aparicio}</span></p>
            <p><span class='marro2'>${t(LABELS_MORT_COMBAT, 'reappearancePlace', lang)}</span> <span class='blau1'>${desaparegut_lloc_aparicio}</span></p>
            <p><span class='marro2'>${t(LABELS_MORT_COMBAT, 'observations', lang)}</span> <span class='blau1'>${dada.aparegut_observacions ?? ''}</span></p>
          </div>
        `
      : '';

  htmlContent += `
    <div class="negreta raleway">
      <div style="margin-top:25px">
        <h5><span class="negreta blau1">${t(LABELS_MORT_COMBAT, 's1_title', lang)}</span></h5>
        <p><span class='marro2'>${t(LABELS_MORT_COMBAT, 'condition', lang)}</span> <span class='blau1'>${condicio}</span></p>
        <p><span class='marro2'>${t(LABELS_MORT_COMBAT, 'warSide', lang)}</span> <span class='blau1'>${bandol}</span></p>
        <p><span class='marro2'>${t(LABELS_MORT_COMBAT, 'conscriptYear', lang)}</span> <span class='blau1'>${any_lleva}</span></p>
      </div>

      <div style="margin-top:25px">
        <h5><span class="negreta blau1">${t(LABELS_MORT_COMBAT, 's2_title', lang)}</span></h5>
        <p><span class='marro2'>${t(LABELS_MORT_COMBAT, 'initialUnit', lang)}</span> <span class='blau1'>${unitat_inicial}</span></p>
        <p><span class='marro2'>${t(LABELS_MORT_COMBAT, 'militaryCorps', lang)}</span> <span class='blau1'>${cos}</span></p>
        <p><span class='marro2'>${t(LABELS_MORT_COMBAT, 'finalUnit', lang)}</span> <span class='blau1'>${unitat_final}</span></p>
        <p><span class='marro2'>${t(LABELS_MORT_COMBAT, 'finalRank', lang)}</span> <span class='blau1'>${graduacio_final}</span></p>
        <p><span class='marro2'>${t(LABELS_MORT_COMBAT, 'militaryJourney', lang)}</span> <span class='blau1'>${periple_militar}</span></p>
      </div>

      <div style="margin-top:25px">
        <h5><span class="negreta blau1">${t(LABELS_MORT_COMBAT, 's3_title', lang)}</span></h5>
        <p><span class='marro2'>${t(LABELS_MORT_COMBAT, 'causeDeathMissing', lang)}</span> <span class='blau1'>${circumstancia_mort}</span></p>
        <h6><span class="negreta blau1">${t(LABELS_MORT_COMBAT, 'ifMissingHeading', lang)}</span></h6>
        <p><span class='marro2'>${t(LABELS_MORT_COMBAT, 'missingDate', lang)}</span> <span class='blau1'>${desaparegut_data}</span></p>
        <p><span class='marro2'>${t(LABELS_MORT_COMBAT, 'missingPlace', lang)}</span> <span class='blau1'>${desaparegut_lloc}</span></p>
      </div>

      ${fragment}
    </div>
  `;
  return htmlContent;
}
