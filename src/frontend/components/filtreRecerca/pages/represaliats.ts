import { BuscadorController } from '../controller';
import { REPRESALIATS_FILTERS } from '../filters/registry-represaliats';

export async function iniciarBuscadorRepresaliats(): Promise<void> {
  const ctrl = new BuscadorController(REPRESALIATS_FILTERS, {
    personas: { type: 'filtreRepresaliats', lang: 'ca' }, // <- AJUSTA 'type' AL QUE EXPONGA TU API
    opciones: { lang: 'ca' },
  });
  await ctrl.init();
}
