// labels-execution.ts
import { makeDict } from '../i18n';

type ExecKeys = 'execDate' | 'execPlace' | 'burialPlace' | 'observations';

export const LABELS_EXEC = makeDict<ExecKeys>({
  ca: {
    execDate: "Data d'execució",
    execPlace: "Lloc d'execució",
    burialPlace: "Lloc d'enterrament",
    observations: 'Observacions',
  },
  es: {
    execDate: 'Fecha de ejecución',
    execPlace: 'Lugar de ejecución',
    burialPlace: 'Lugar de enterramiento',
    observations: 'Observaciones',
  },
  en: {
    execDate: 'Date of execution',
    execPlace: 'Place of execution',
    burialPlace: 'Place of burial',
    observations: 'Observations',
  },
  fr: {
    execDate: "Date d'exécution",
    execPlace: "Lieu d'exécution",
    burialPlace: "Lieu d'inhumation",
    observations: 'Observations',
  },
  it: {
    execDate: 'Data di esecuzione',
    execPlace: 'Luogo di esecuzione',
    burialPlace: 'Luogo di sepoltura',
    observations: 'Osservazioni',
  },
  pt: {
    execDate: 'Data de execução',
    execPlace: 'Local de execução',
    burialPlace: 'Local de inumação',
    observations: 'Observações',
  },
});
