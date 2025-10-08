// labels-actions.ts
import { makeDict } from './i18n';

type ActionKeys = 'downloadCsv' | 'downloadXlsx' | 'downloadPdf' | 'tabNotAvailable';

export const LABELS_ACTIONS = makeDict<ActionKeys>({
  ca: {
    downloadCsv: 'Descarregar dades en CSV',
    downloadXlsx: 'Descarregar dades en Excel',
    downloadPdf: 'Descarregar fitxa en PDF',
    tabNotAvailable: 'El contingut de la pestanya {tab} encara no està disponible.',
  },
  es: {
    downloadCsv: 'Descargar datos en CSV',
    downloadXlsx: 'Descargar datos en Excel',
    downloadPdf: 'Descargar ficha en PDF',
    tabNotAvailable: 'El contenido de la pestaña {tab} todavía no está disponible.',
  },
  en: {
    downloadCsv: 'Download data as CSV',
    downloadXlsx: 'Download data as Excel',
    downloadPdf: 'Download record as PDF',
    tabNotAvailable: 'The content of the "{tab}" tab is not available yet.',
  },
  fr: {
    downloadCsv: 'Télécharger les données en CSV',
    downloadXlsx: 'Télécharger les données en Excel',
    downloadPdf: 'Télécharger la fiche en PDF',
    tabNotAvailable: "Le contenu de l'onglet {tab} n'est pas encore disponible.",
  },
  it: {
    downloadCsv: 'Scarica i dati in CSV',
    downloadXlsx: 'Scarica i dati in Excel',
    downloadPdf: 'Scarica la scheda in PDF',
    tabNotAvailable: 'Il contenuto della scheda {tab} non è ancora disponibile.',
  },
  pt: {
    downloadCsv: 'Descarregar os dados em CSV',
    downloadXlsx: 'Descarregar os dados em Excel',
    downloadPdf: 'Descarregar a ficha em PDF',
    tabNotAvailable: 'O conteúdo do separador {tab} ainda não está disponível.',
  },
});
