// src/pages/exili.ts
import { BuscadorController } from '../controller';
import { EXILI_FILTERS } from '../filters/registry-exili';
import { mountExportToolbar } from '../utils/export';
import 'choices.js/public/assets/styles/choices.min.css';

export async function iniciarBuscadorExiliats() {
  const ctrl = new BuscadorController(EXILI_FILTERS, {
    personas: { type: 'filtreExili', lang: 'ca' }, // <- IMPORTANTE: type para el backend
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
