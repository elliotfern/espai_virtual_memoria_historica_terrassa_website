import { makeDict } from '../i18n';

type PendingKeys = 'pendingNoData';

export const LABELS_PENDING = makeDict<PendingKeys>({
  ca: {
    pendingNoData: 'El grup de represaliats pendent de classificació no té dades per mostrar.',
  },
  es: {
    pendingNoData: 'El grupo de represaliados pendiente de clasificación no tiene datos para mostrar.',
  },
  en: {
    pendingNoData: 'The group of victims pending classification has no data to display.',
  },
  fr: {
    pendingNoData: "Le groupe de personnes réprimées en attente de classification n'a pas de données à afficher.",
  },
  it: {
    pendingNoData: 'Il gruppo di repressi in attesa di classificazione non ha dati da mostrare.',
  },
  pt: {
    pendingNoData: 'O grupo de reprimidos pendente de classificação não tem dados para mostrar.',
  },
});
