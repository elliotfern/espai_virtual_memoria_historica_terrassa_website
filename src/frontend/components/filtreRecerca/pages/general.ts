// src/pages/exili.ts
import { BuscadorController } from '../controller';
import { GENERAL_FILTERS } from '../filters/registry-general';
import 'choices.js/public/assets/styles/choices.min.css';

export async function iniciarBuscadorGeneral() {
  const ctrl = new BuscadorController(GENERAL_FILTERS, {
    personas: { type: 'filtreGeneral', lang: 'ca' },
    opciones: { lang: 'ca' },
  });
  await ctrl.init();
}
