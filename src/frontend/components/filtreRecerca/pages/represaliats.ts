import { BuscadorController } from '../controller';
import { REPRESALIATS_FILTERS } from '../filters/registry-represaliats';
import { mountExportToolbar } from '../utils/export'; // ← nuevo

export async function iniciarBuscadorRepresaliats(): Promise<void> {
  const ctrl = new BuscadorController(REPRESALIATS_FILTERS, {
    personas: { type: 'filtreRepresaliats', lang: 'ca' }, // <- AJUSTA 'type' AL QUE EXPONGA TU API
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
