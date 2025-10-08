import { formatDatesForm } from '../../../services/formatDates/dates';
import { valorTextDesconegut } from '../../../services/formatDates/valorTextDesconegut';
import { LABELS_CIRC } from '../../../services/i18n/fitxaRepresaliatRepressio/label-tab4-mortCivil';
import { DEFAULT_LANG, isLang, t } from '../../../services/i18n/i18n';
import { MortCivilData } from '../../../types/MortCivilData';

export function tab4MortCivil(dada: MortCivilData, htmlContent: string, lang: string): string {
  const l = isLang(lang) ? lang : DEFAULT_LANG;

  const cirId = dada.cirscumstancies_mortId ?? null;

  // Títulos de municipio según causa
  const titolMunicipiCadaver = cirId === 5 ? t(LABELS_CIRC, 'muni_bombing', l) : cirId === 8 ? t(LABELS_CIRC, 'muni_extrajudicial', l) : cirId === 9 ? t(LABELS_CIRC, 'muni_execution', l) : t(LABELS_CIRC, 'muni_bodyFound', l);

  // ----------------------
  // Bloque: BOMBardeig (5)
  // ----------------------
  let contingutHtmlBombardeig = '';
  if (cirId === 5) {
    const data_bombardeig = valorTextDesconegut(
      dada.data_bombardeig?.trim() ? formatDatesForm(dada.data_bombardeig!) : null,
      4, // unknownDate
      l
    );
    const municipi_bombardeig = valorTextDesconegut(dada.municipi_bombardeig, 2, l); // unknownM
    const lloc_bombardeig = valorTextDesconegut(dada.lloc_bombardeig, 2, l); // unknownM

    // Mapa de responsables con i18n
    const responsablesMap: Record<string, string> = {
      '1': t(LABELS_CIRC, 'resp_it_fasc_air', l),
      '2': t(LABELS_CIRC, 'resp_de_nazi_air', l),
      '3': t(LABELS_CIRC, 'resp_es_franco_air', l),
    };
    const responsable_key = String(dada.responsable_bombardeig ?? '');
    const responsable_bombardeig = responsablesMap[responsable_key] ?? valorTextDesconegut(null, 2, l);

    contingutHtmlBombardeig = `
      <div class="negreta raleway">
        <div style="margin-top:25px">
          <h5><span class="negreta blau1">${t(LABELS_CIRC, 'causeOfDeath', l)}: ${t(LABELS_CIRC, 'bombing', l)}</span></h5>
          <p><span class='marro2'>${t(LABELS_CIRC, 'bombingDate', l)} </span><span class='blau1'>${data_bombardeig}</span></p>
          <p><span class='marro2'>${t(LABELS_CIRC, 'bombingMunicipality', l)}</span> <span class='blau1'>${municipi_bombardeig}</span></p>
          <p><span class='marro2'>${t(LABELS_CIRC, 'bombingPlaceType', l)}</span> <span class='blau1'>${lloc_bombardeig}</span></p>
          <p><span class='marro2'>${t(LABELS_CIRC, 'bombingResponsible', l)}</span> <span class='blau1'>${responsable_bombardeig}</span></p>
        </div>
      </div>`;
  }

  // ---------------------------------------
  // Bloque: Extra-judicial (assassinat) (8)
  // ---------------------------------------
  let contingutHtmlAssassinat = '';
  if (cirId === 8) {
    const data_detencio = valorTextDesconegut(dada.data_detencio?.trim() ? formatDatesForm(dada.data_detencio!) : null, 4, l);
    const lloc_detencio = valorTextDesconegut(dada.lloc_detencio, 2, l);
    const qui_detencio = valorTextDesconegut(dada.qui_detencio, 2, l);

    contingutHtmlAssassinat = `
      <div class="negreta raleway">
        <div style="margin-top:25px">
          <h5><span class="negreta blau1">${t(LABELS_CIRC, 'causeOfDeath', l)}: ${t(LABELS_CIRC, 'extrajudicial', l)}</span></h5>
          <p><span class='marro2'>${t(LABELS_CIRC, 'detentionDate', l)} </span><span class='blau1'>${data_detencio}</span></p>
          <p><span class='marro2'>${t(LABELS_CIRC, 'detentionPlace', l)}</span> <span class='blau1'>${lloc_detencio}</span></p>
          <p><span class='marro2'>${t(LABELS_CIRC, 'whoDetains', l)}</span> <span class='blau1'>${qui_detencio}</span></p>
        </div>
      </div>`;
  }

  // ---------------------------
  // Bloque: Afusellat (fusilado)
  // ---------------------------
  let contingutHtmlAfusellat = '';
  if (cirId === 9) {
    const qui_ordena_afusellat = valorTextDesconegut(dada.qui_ordena_afusellat, 2, l);
    const qui_executa_afusellat = valorTextDesconegut(dada.qui_executa_afusellat, 2, l);

    contingutHtmlAfusellat = `
      <div class="negreta raleway">
        <div style="margin-top:25px">
          <h5><span class="negreta blau1">${t(LABELS_CIRC, 'causeOfDeath', l)}: ${t(LABELS_CIRC, 'firingSquad', l)}</span></h5>
          <p><span class='marro2'>${t(LABELS_CIRC, 'whoOrdersExec', l)} </span><span class='blau1'>${qui_ordena_afusellat}</span></p>
          <p><span class='marro2'>${t(LABELS_CIRC, 'whoExecutes', l)}</span> <span class='blau1'>${qui_executa_afusellat}</span></p>
        </div>
      </div>`;
  }

  // ---------------------------
  // Bloque base (siempre)
  // ---------------------------
  const cirscumstancies_mort = valorTextDesconegut(dada.cirscumstancies_mort, 2, l);
  const data_trobada_cadaver = valorTextDesconegut(dada.data_trobada_cadaver?.trim() ? formatDatesForm(dada.data_trobada_cadaver!) : null, 4, l);
  const lloc_trobada_cadaver = valorTextDesconegut(dada.lloc_trobada_cadaver, 2, l);

  htmlContent += `
    <div class="negreta raleway">
      <div style="margin-top:25px">
        <h5><span class="negreta blau1">${t(LABELS_CIRC, 's1_title', l)}</span></h5>
        <p><span class='marro2'>${t(LABELS_CIRC, 'deathCirc', l)} </span><span class='blau1'>${cirscumstancies_mort}</span></p>
        <p><span class='marro2'>${t(LABELS_CIRC, 'bodyFoundDate', l)}</span> <span class='blau1'>${data_trobada_cadaver}</span></p>
        <p><span class='marro2'>${titolMunicipiCadaver}:</span> <span class='blau1'>${lloc_trobada_cadaver}</span></p>
      </div>
    </div>`;

  htmlContent += contingutHtmlBombardeig + contingutHtmlAssassinat + contingutHtmlAfusellat;
  return htmlContent;
}
