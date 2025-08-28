import { BuscadorController } from '../controller';
import { COST_HUMA_FILTERS } from '../filters/registry-cost-huma';
import { mountExportToolbar } from '../utils/export'; // ← nuevo

let __costHumaBooted = false;

export async function iniciarBuscadorCostHuma() {
  if (__costHumaBooted) return; // evita doble init si el router dispara 2 veces
  __costHumaBooted = true;

  const ctrl = new BuscadorController(COST_HUMA_FILTERS, {
    personas: { type: 'filtreCostHuma', lang: 'ca' },
    opciones: { lang: 'ca' },
  });

  await ctrl.init();

  let container = document.getElementById('exportToolbar');
  if (!container) {
    container = document.createElement('div');
    container.id = 'exportToolbar';
    container.className = 'mb-2';
    document.getElementById('filtros')?.prepend(container);
  }
  mountExportToolbar(container, () => ctrl.getExportPayload());
}
