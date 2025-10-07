import { makeDict } from './i18n';

type Keys = 'title' | 'help';

export const TIPUS_TEXTS = makeDict<Keys>({
  ca: {
    title: 'Tipus de repressió:',
    help: 'Clica sobre els botons per ampliar la informació sobre el tipus de repressió que va patir:',
  },
  es: {
    title: 'Tipo de represión:',
    help: 'Haz clic en los botones para ampliar la información sobre el tipo de represión que sufrió:',
  },
  en: {
    title: 'Type of repression:',
    help: 'Click the buttons to see more information about the type of repression suffered:',
  },
  fr: {
    title: 'Type de répression :',
    help: 'Cliquez sur les boutons pour obtenir plus d’informations sur le type de répression subi :',
  },
  it: {
    title: 'Tipo di repressione:',
    help: 'Fai clic sui pulsanti per approfondire le informazioni sul tipo di repressione subita:',
  },
  pt: {
    title: 'Tipo de repressão:',
    help: 'Clique nos botões para ampliar as informações sobre o tipo de repressão sofrida:',
  },
});
