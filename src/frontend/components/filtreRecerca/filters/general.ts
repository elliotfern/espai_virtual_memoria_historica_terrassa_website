// src/pages/general.ts
import { BuscadorController } from '../controller';
import { GENERAL_FILTERS } from '../filters/registry-general';
import { mountExportToolbar } from '../utils/export';
import 'choices.js/public/assets/styles/choices.min.css';
import type { FilterSpec } from './types';

export async function iniciarBuscadorGeneral() {
  const ctrl = new BuscadorController(GENERAL_FILTERS, {
    personas: { type: 'filtreGeneral', lang: 'ca' }, // ← IMPORTANTE: el type viaja al backend
    opciones: { lang: 'ca' },
  });

  await ctrl.init();

  // Monta los botones de exportación en <div id="exportToolbar"></div>
  let container = document.getElementById('exportToolbar');
  if (!container) {
    // (por si acaso) créalo si no existe
    container = document.createElement('div');
    container.id = 'exportToolbar';
    container.className = 'mb-2';
    // inserta al principio del contenedor de filtros si te encaja
    document.getElementById('filtros')?.prepend(container);
  }

  // Lee filtros + texto + type en el momento del clic
  mountExportToolbar(container, () => ctrl.getExportPayload());
}

// Aquí puedes añadir filtros específicos de “General” si los hay.
export const GENERAL_FILTERS_ONLY: FilterSpec[] = [
  // Ejemplo: FILTER_ALGO,
];
