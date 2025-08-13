// src/pages/exili.ts
import { BuscadorController } from '../controller';
import { EXILI_FILTERS } from '../filters/registry-exili';
import 'choices.js/public/assets/styles/choices.min.css';

export async function iniciarBuscadorExiliats() {
  const ctrl = new BuscadorController(EXILI_FILTERS, {
    personas: { type: 'filtreExiliats', lang: 'ca' },
    opciones: { lang: 'ca' },
  });
  await ctrl.init();
}
