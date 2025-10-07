// translations.ts
export async function loadTranslations(lang: string): Promise<Record<string, string>> {
  switch (lang) {
    case 'ca': // Català
      return {
        tab1: 'Dades personals',
        tab2: 'Dades familiars',
        tab3: 'Dades acadèmiques i laborals',
        tab4: 'Dades polítiques i sindicals',
        tab5: 'Biografia',
        tab6: 'Fonts documentals',
        tab7: 'Altres dades',
        tab8: 'Multimèdia',
        tab9: 'Geolocalització',
      };
    case 'es': // Español
      return {
        tab1: 'Datos personales',
        tab2: 'Datos familiares',
        tab3: 'Datos académicos y laborales',
        tab4: 'Datos políticos y sindicales',
        tab5: 'Biografía',
        tab6: 'Fuentes documentales',
        tab7: 'Otros datos',
        tab8: 'Multimedia',
        tab9: 'Geolocalitzación',
      };
    case 'en': // English
      return {
        tab1: 'Personal data',
        tab2: 'Family data',
        tab3: 'Academic and work data',
        tab4: 'Political and union data',
        tab5: 'Biography',
        tab6: 'Documentary sources',
        tab7: 'Other data',
        tab8: 'Multimedia',
        tab9: 'Geolocation',
      };
    case 'fr': // Français
      return {
        tab1: 'Données personnelles',
        tab2: 'Données familiales',
        tab3: 'Données académiques et professionnelles',
        tab4: 'Données politiques et syndicales',
        tab5: 'Biographie',
        tab6: 'Sources documentaires',
        tab7: 'Autres données',
        tab8: 'Multimédia',
        tab9: 'Géolocalisation',
      };
    case 'it': // Italiano
      return {
        tab1: 'Dati personali',
        tab2: 'Dati familiari',
        tab3: 'Dati accademici e lavorativi',
        tab4: 'Dati politici e sindacali',
        tab5: 'Biografia',
        tab6: 'Fonti documentarie',
        tab7: 'Altri dati',
        tab8: 'Multimedia',
        tab9: 'Geolocalizzazione',
      };
    case 'pt': // Português
      return {
        tab1: 'Dados pessoais',
        tab2: 'Dados familiares',
        tab3: 'Dados académicos e profissionais',
        tab4: 'Dados políticos e sindicais',
        tab5: 'Biografia',
        tab6: 'Fontes documentais',
        tab7: 'Outros dados',
        tab8: 'Multimédia',
        tab9: 'Geolocalização',
      };
    default: // Fallback → catalán
      return await loadTranslations('ca');
  }
}
