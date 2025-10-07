// labels-vtd.ts
import { makeDict } from './i18n';

export type VTDKey =
  | 'noData' // 1
  | 'unknownM' // 2
  | 'empty' // 3
  | 'unknownDate' // 4
  | 'unknownF' // 5
  | 'noImprisonment' // 6
  | 'noExile'; // 7

export const LABELS_VTD = makeDict<VTDKey>({
  ca: {
    noData: 'Sense dades',
    unknownM: 'Desconegut',
    empty: '',
    unknownDate: 'Data desconeguda',
    unknownF: 'Desconeguda',
    noImprisonment: 'No consta cap empresonament',
    noExile: "No consta que marxés a l'exili",
  },
  es: {
    noData: 'Sin datos',
    unknownM: 'Desconocido',
    empty: '',
    unknownDate: 'Fecha desconocida',
    unknownF: 'Desconocida',
    noImprisonment: 'No consta ningún encarcelamiento',
    noExile: 'No consta que se marchara al exilio',
  },
  en: {
    noData: 'No data',
    unknownM: 'Unknown',
    empty: '',
    unknownDate: 'Unknown date',
    unknownF: 'Unknown (female)',
    noImprisonment: 'No record of imprisonment',
    noExile: 'No record that they went into exile',
  },
  fr: {
    noData: 'Aucune donnée',
    unknownM: 'Inconnu',
    empty: '',
    unknownDate: 'Date inconnue',
    unknownF: 'Inconnue',
    noImprisonment: "Aucune trace d'emprisonnement",
    noExile: "Aucune indication d'un départ en exil",
  },
  it: {
    noData: 'Nessun dato',
    unknownM: 'Sconosciuto',
    empty: '',
    unknownDate: 'Data sconosciuta',
    unknownF: 'Sconosciuta',
    noImprisonment: 'Non risulta alcuna incarcerazione',
    noExile: 'Non risulta che sia andato/a in esilio',
  },
  pt: {
    noData: 'Sem dados',
    unknownM: 'Desconhecido',
    empty: '',
    unknownDate: 'Data desconhecida',
    unknownF: 'Desconhecida',
    noImprisonment: 'Não consta qualquer prisão',
    noExile: 'Não consta que tenha ido para o exílio',
  },
});
