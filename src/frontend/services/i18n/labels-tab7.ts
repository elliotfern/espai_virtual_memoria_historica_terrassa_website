// labels-tab-info.ts
import { makeDict } from './i18n';

type TabInfoKeys = 'observations' | 'createdBy' | 'dataReview' | 'dataEntry' | 'creationDate' | 'lastUpdate';

export const LABELS_TAB7 = makeDict<TabInfoKeys>({
  ca: {
    observations: 'Observacions',
    createdBy: 'Fitxa creada per',
    dataReview: 'Revisió dades',
    dataEntry: 'Introducció dades',
    creationDate: 'Data de creació',
    lastUpdate: 'Darrera actualització',
  },
  es: {
    observations: 'Observaciones',
    createdBy: 'Ficha creada por',
    dataReview: 'Revisión de datos',
    dataEntry: 'Introducción de datos',
    creationDate: 'Fecha de creación',
    lastUpdate: 'Última actualización',
  },
  en: {
    observations: 'Observations',
    createdBy: 'Record created by',
    dataReview: 'Data review',
    dataEntry: 'Data entry',
    creationDate: 'Creation date',
    lastUpdate: 'Last updated',
  },
  fr: {
    observations: 'Observations',
    createdBy: 'Fiche créée par',
    dataReview: 'Révision des données',
    dataEntry: 'Saisie des données',
    creationDate: 'Date de création',
    lastUpdate: 'Dernière mise à jour',
  },
  it: {
    observations: 'Osservazioni',
    createdBy: 'Scheda creata da',
    dataReview: 'Revisione dei dati',
    dataEntry: 'Inserimento dati',
    creationDate: 'Data di creazione',
    lastUpdate: 'Ultimo aggiornamento',
  },
  pt: {
    observations: 'Observações',
    createdBy: 'Ficha criada por',
    dataReview: 'Revisão dos dados',
    dataEntry: 'Inserção de dados',
    creationDate: 'Data de criação',
    lastUpdate: 'Última atualização',
  },
});
