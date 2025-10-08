// labels-lists.ts
import { makeDict } from '../i18n';

type ListKeys = 'bibliographyList' | 'archivesList';

export const LABELS_LISTS = makeDict<ListKeys>({
  ca: {
    bibliographyList: 'Llistat de bibliografia',
    archivesList: "Llistat d'arxius",
  },
  es: {
    bibliographyList: 'Listado de bibliografía',
    archivesList: 'Listado de archivos',
  },
  en: {
    bibliographyList: 'Bibliography list',
    archivesList: 'List of archives',
  },
  fr: {
    bibliographyList: 'Liste de bibliographie',
    archivesList: 'Liste des archives',
  },
  it: {
    bibliographyList: 'Elenco della bibliografia',
    archivesList: 'Elenco degli archivi',
  },
  pt: {
    bibliographyList: 'Lista de bibliografia',
    archivesList: 'Lista de arquivos',
  },
});
