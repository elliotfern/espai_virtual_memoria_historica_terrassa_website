import { makeDict } from '../i18n';

// Etiquetas del bloque
type DepurFeinaKeys = 's1_title' | 'sectorProfessional' | 'profession' | 'company' | 'sanction' | 'observations';

export const LABELS_DEPUR_FEINA = makeDict<DepurFeinaKeys>({
  ca: {
    s1_title: '1) Dades sobre la depuració al lloc de treball',
    sectorProfessional: 'Sector professional:',
    profession: 'Professió:',
    company: 'Empresa:',
    sanction: 'Sanció:',
    observations: 'Observacions:',
  },
  es: {
    s1_title: '1) Datos sobre la depuración en el puesto de trabajo',
    sectorProfessional: 'Sector profesional:',
    profession: 'Profesión:',
    company: 'Empresa:',
    sanction: 'Sanción:',
    observations: 'Observaciones:',
  },
  en: {
    s1_title: '1) Data on workplace purge',
    sectorProfessional: 'Professional sector:',
    profession: 'Profession:',
    company: 'Company:',
    sanction: 'Sanction:',
    observations: 'Observations:',
  },
  fr: {
    s1_title: '1) Données sur l’épuration au poste de travail',
    sectorProfessional: 'Secteur professionnel :',
    profession: 'Profession :',
    company: 'Entreprise :',
    sanction: 'Sanction :',
    observations: 'Observations :',
  },
  it: {
    s1_title: '1) Dati sulla epurazione nel posto di lavoro',
    sectorProfessional: 'Settore professionale:',
    profession: 'Professione:',
    company: 'Azienda:',
    sanction: 'Sanzione:',
    observations: 'Osservazioni:',
  },
  pt: {
    s1_title: '1) Dados sobre a depuração no local de trabalho',
    sectorProfessional: 'Setor profissional:',
    profession: 'Profissão:',
    company: 'Empresa:',
    sanction: 'Sanção:',
    observations: 'Observações:',
  },
});

// Tipos profesionales
export type ProfTypeKey = 'publicCivilServant' | 'publicTeacher' | 'privateEmployee';

export const PROFESSIONAL_TYPES = makeDict<ProfTypeKey>({
  ca: {
    publicCivilServant: 'Empleat sector públic: (funcionari públic)',
    publicTeacher: 'Empleat sector públic: (professor educació pública)',
    privateEmployee: 'Empleat sector privat',
  },
  es: {
    publicCivilServant: 'Empleado sector público: (funcionario público)',
    publicTeacher: 'Empleado sector público: (profesor educación pública)',
    privateEmployee: 'Empleado sector privado',
  },
  en: {
    publicCivilServant: 'Public sector employee: (civil servant)',
    publicTeacher: 'Public sector employee: (public school teacher)',
    privateEmployee: 'Private sector employee',
  },
  fr: {
    publicCivilServant: 'Employé du secteur public : (fonctionnaire)',
    publicTeacher: "Employé du secteur public : (enseignant de l'éducation publique)",
    privateEmployee: 'Employé du secteur privé',
  },
  it: {
    publicCivilServant: 'Dipendente pubblico: (funzionario pubblico)',
    publicTeacher: 'Dipendente pubblico: (insegnante della scuola pubblica)',
    privateEmployee: 'Dipendente del settore privato',
  },
  pt: {
    publicCivilServant: 'Empregado do setor público: (funcionário público)',
    publicTeacher: 'Empregado do setor público: (professor da rede pública)',
    privateEmployee: 'Empregado do setor privado',
  },
});
