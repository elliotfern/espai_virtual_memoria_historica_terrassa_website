import { BuscadorController } from '../controller';
import { COST_HUMA_FILTERS } from '../filters/registry-cost-huma';

export async function iniciarBuscadorCostHuma(): Promise<void> {
  const ctrl = new BuscadorController(COST_HUMA_FILTERS, {
    personas: { type: 'filtreCostHuma', lang: 'ca' }, // <- AJUSTA 'type' AL QUE EXPONGA TU API
    opciones: { lang: 'ca' },
  });
  await ctrl.init();
}
