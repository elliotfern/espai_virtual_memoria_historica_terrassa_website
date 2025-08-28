// src/pages/general.ts
import { BuscadorController } from '../controller';
import { GENERAL_FILTERS } from '../filters/registry-general';
import { mountExportToolbar } from '../utils/export'; // ← nuevo
import 'choices.js/public/assets/styles/choices.min.css';

export async function iniciarBuscadorGeneral() {
  const ctrl = new BuscadorController(GENERAL_FILTERS, {
    personas: { type: 'filtreGeneral', lang: 'ca' },
    opciones: { lang: 'ca' },
  });

  await ctrl.init();

  // Monta la barra de exportación
  let container = document.getElementById('exportToolbar');
  if (!container) {
    container = document.createElement('div');
    container.id = 'exportToolbar';
    container.className = 'mb-2';
    document.getElementById('filtros')?.prepend(container);
  }

  // Usa SIEMPRE el método del controller
  mountExportToolbar(container, () => ctrl.getExportPayload());
}
