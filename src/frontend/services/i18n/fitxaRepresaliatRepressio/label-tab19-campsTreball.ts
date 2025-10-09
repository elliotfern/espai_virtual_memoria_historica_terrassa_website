import { makeDict } from '../i18n';

type RepressioEmptyKeys = 'categoryEmpty';

export const LABELS_REPRESSIO_EMPTY = makeDict<RepressioEmptyKeys>({
  ca: { categoryEmpty: 'Aquesta categoria de repressió encara no conté dades.' },
  es: { categoryEmpty: 'Esta categoría de represión aún no contiene datos.' },
  en: { categoryEmpty: 'This repression category does not contain data yet.' },
  fr: { categoryEmpty: 'Cette catégorie de répression ne contient pas encore de données.' },
  it: { categoryEmpty: 'Questa categoria di repressione non contiene ancora dati.' },
  pt: { categoryEmpty: 'Esta categoria de repressão ainda não contém dados.' },
});
