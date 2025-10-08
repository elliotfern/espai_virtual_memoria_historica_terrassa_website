// labels-tab3.ts
import { makeDict } from '../i18n';

type Tab3Keys = 'studies' | 'occupation' | 'company' | 'position' | 'economicSector' | 'economicSubsector';

export const LABELS_TAB3 = makeDict<Tab3Keys>({
  ca: {
    studies: 'Estudis',
    occupation: 'Ofici',
    company: 'Empresa',
    position: 'Càrrec',
    economicSector: 'Sector econòmic',
    economicSubsector: 'Sub-sector econòmic',
  },
  es: {
    studies: 'Estudios',
    occupation: 'Oficio',
    company: 'Empresa',
    position: 'Cargo',
    economicSector: 'Sector económico',
    economicSubsector: 'Subsector económico',
  },
  en: {
    studies: 'Studies',
    occupation: 'Occupation',
    company: 'Company',
    position: 'Position',
    economicSector: 'Economic sector',
    economicSubsector: 'Economic subsector',
  },
  fr: {
    studies: 'Études',
    occupation: 'Métier',
    company: 'Entreprise',
    position: 'Fonction',
    economicSector: 'Secteur économique',
    economicSubsector: 'Sous-secteur économique',
  },
  it: {
    studies: 'Studi',
    occupation: 'Mestiere',
    company: 'Azienda',
    position: 'Incarico',
    economicSector: 'Settore economico',
    economicSubsector: 'Sottosettore economico',
  },
  pt: {
    studies: 'Estudos',
    occupation: 'Ofício',
    company: 'Empresa',
    position: 'Cargo',
    economicSector: 'Setor económico',
    economicSubsector: 'Subsector económico',
  },
});
