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

  const container = document.getElementById('exportToolbar');
  if (container) {
    // leerá la selección y el texto ‘q’ justo al hacer clic
    mountExportToolbar(container, () => ctrl.getSelectionForExport());
  }
}
