import { valorTextDesconegut } from '../../../services/formatDates/valorTextDesconegut';
import { LABELS_DEPUR_FEINA, PROFESSIONAL_TYPES, ProfTypeKey } from '../../../services/i18n/fitxaRepresaliatRepressio/label-tab7-depurat';
import { DEFAULT_LANG, isLang, t } from '../../../services/i18n/i18n';
import { Depurat } from '../../../types/Depurat';

export function tab7Depurat(dada: Depurat, htmlContent: string, lang: string): string {
  const l = isLang(lang) ? lang : DEFAULT_LANG;

  // Empresa / Profesión / Sanción / Observaciones
  const empresa = valorTextDesconegut(dada.empresa, 1, l); // noData
  const professio = valorTextDesconegut(dada.professio, 1, l); // noData
  const sancio = valorTextDesconegut(dada.sancio, 5, l); // unknownF (femenino)
  const observacions = valorTextDesconegut(dada.observacions, 3, l); // empty

  // Mapear el tipo profesional (1/2/3) a claves i18n
  type ProfessionalTypeId = 1 | 2 | 3;
  const mapType = (id?: number | null): ProfTypeKey | null => {
    switch (id as ProfessionalTypeId) {
      case 1:
        return 'publicCivilServant';
      case 2:
        return 'publicTeacher';
      case 3:
        return 'privateEmployee';
      default:
        return null;
    }
  };

  const profKey = mapType(dada.tipus_professional ?? null);
  const tipusProfessional = profKey ? t(PROFESSIONAL_TYPES, profKey, l) : valorTextDesconegut(null, 2, l); // unknownM

  // Render
  htmlContent += `
    <div class="negreta raleway">
      <div style="margin-top:25px">
        <h5><span class="negreta blau1">${t(LABELS_DEPUR_FEINA, 's1_title', l)}</span></h5>
        <p><span class='marro2'>${t(LABELS_DEPUR_FEINA, 'sectorProfessional', l)} </span><span class='blau1'>${tipusProfessional}</span></p>
        <p><span class='marro2'>${t(LABELS_DEPUR_FEINA, 'profession', l)}</span> <span class='blau1'>${professio}</span></p>
        <p><span class='marro2'>${t(LABELS_DEPUR_FEINA, 'company', l)}</span> <span class='blau1'>${empresa}</span></p>
        <p><span class='marro2'>${t(LABELS_DEPUR_FEINA, 'sanction', l)}</span> <span class='blau1'>${sancio}</span></p>
        <p><span class='marro2'>${t(LABELS_DEPUR_FEINA, 'observations', l)}</span> <span class='blau1'>${observacions}</span></p>
      </div>
    </div>`;

  return htmlContent;
}
