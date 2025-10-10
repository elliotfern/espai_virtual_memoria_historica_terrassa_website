// services/i18n/equip/labels-equip.ts
import { makeDict } from '../../i18n/i18n';

export type EquipKeys =
  | 'groupTitleResearch' // 2
  | 'groupTitleWeb' // 1
  | 'groupTitleData' // 3
  | 'ctaViewBio'
  | 'altPhoto'
  | 'altDecoration'
  | 'errorLoad'
  | 'errorConn';

export const LABELS_EQUIP = makeDict<EquipKeys>({
  ca: {
    groupTitleResearch: 'Membres recerca històrica:',
    groupTitleWeb: 'Equip pàgina web:',
    groupTitleData: 'Col·laboradores introducció i processament de dades:',
    ctaViewBio: 'Veure biografia',
    altPhoto: 'Foto',
    altDecoration: 'Decoració',
    errorLoad: "No s'ha pogut carregar l'equip.",
    errorConn: 'Error de connexió amb la API.',
  },
  es: {
    groupTitleResearch: 'Miembros de la investigación histórica:',
    groupTitleWeb: 'Equipo de la página web:',
    groupTitleData: 'Colaboradoras de introducción y procesamiento de datos:',
    ctaViewBio: 'Ver biografía',
    altPhoto: 'Foto',
    altDecoration: 'Decoración',
    errorLoad: 'No se ha podido cargar el equipo.',
    errorConn: 'Error de conexión con la API.',
  },
  en: {
    groupTitleResearch: 'Historical research members:',
    groupTitleWeb: 'Website team:',
    groupTitleData: 'Data entry and processing collaborators:',
    ctaViewBio: 'View biography',
    altPhoto: 'Photo',
    altDecoration: 'Decoration',
    errorLoad: 'Could not load the team.',
    errorConn: 'Connection error with the API.',
  },
  fr: {
    groupTitleResearch: 'Membres de la recherche historique :',
    groupTitleWeb: 'Équipe du site web :',
    groupTitleData: 'Collaboratrices en saisie et traitement des données :',
    ctaViewBio: 'Voir la biographie',
    altPhoto: 'Photo',
    altDecoration: 'Décoration',
    errorLoad: "Impossible de charger l'équipe.",
    errorConn: "Erreur de connexion avec l'API.",
  },
  it: {
    groupTitleResearch: 'Membri della ricerca storica:',
    groupTitleWeb: 'Team del sito web:',
    groupTitleData: 'Collaboratrici per inserimento e trattamento dati:',
    ctaViewBio: 'Vedi biografia',
    altPhoto: 'Foto',
    altDecoration: 'Decorazione',
    errorLoad: "Impossibile caricare l'equipe.",
    errorConn: "Errore di connessione con l'API.",
  },
  pt: {
    groupTitleResearch: 'Membros da investigação histórica:',
    groupTitleWeb: 'Equipa do site:',
    groupTitleData: 'Colaboradoras de introdução e processamento de dados:',
    ctaViewBio: 'Ver biografia',
    altPhoto: 'Foto',
    altDecoration: 'Decoração',
    errorLoad: 'Não foi possível carregar a equipa.',
    errorConn: 'Erro de ligação à API.',
  },
});
