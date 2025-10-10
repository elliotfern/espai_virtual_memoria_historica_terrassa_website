// services/i18n/home/labels-search.ts
import { makeDict } from '../i18n';

export type SearchKeys = 'noResults' | 'minChars';

export const LABELS_SEARCH = makeDict<SearchKeys>({
  ca: {
    noResults: "No s'ha trobat cap coincidència.",
    minChars: 'Escriu almenys 5 caràcters per començar la cerca.',
  },
  es: {
    noResults: 'No se ha encontrado ninguna coincidencia.',
    minChars: 'Escribe al menos 5 caracteres para empezar la búsqueda.',
  },
  en: {
    noResults: 'No matches found.',
    minChars: 'Type at least 5 characters to start searching.',
  },
  fr: {
    noResults: 'Aucune correspondance trouvée.',
    minChars: 'Saisissez au moins 5 caractères pour commencer la recherche.',
  },
  it: {
    noResults: 'Nessuna corrispondenza trovata.',
    minChars: 'Digita almeno 5 caratteri per iniziare la ricerca.',
  },
  pt: {
    noResults: 'Nenhuma correspondência encontrada.',
    minChars: 'Escreva pelo menos 5 caracteres para iniciar a pesquisa.',
  },
});
