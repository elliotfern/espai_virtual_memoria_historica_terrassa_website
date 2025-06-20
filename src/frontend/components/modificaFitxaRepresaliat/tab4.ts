import { Fitxa } from '../../types/types';
import { auxiliarSelect } from '../../services/fetchData/auxiliarSelect';

export function tab4(fitxa?: Fitxa) {
  auxiliarSelect(fitxa?.estudis_id ?? 3, 'estudis', 'estudis', 'estudi_cat', '3');
  auxiliarSelect(fitxa?.ofici_id ?? 38, 'oficis', 'ofici', 'ofici_cat', '38');

  const refreshButtonOfici = document.getElementById('refreshButtonOfici');
  if (refreshButtonOfici) {
    refreshButtonOfici.addEventListener('click', (event: Event) => {
      event.preventDefault();
      auxiliarSelect(fitxa?.ofici_id ?? 38, 'oficis', 'ofici', 'ofici_cat');
    });
  }

  auxiliarSelect(fitxa?.sector_id ?? 4, 'sectors_economics', 'sector', 'sector_cat', '4');
  auxiliarSelect(fitxa?.sub_sector_id ?? 3, 'sub_sectors_economics', 'sub_sector', 'sub_sector_cat', '3');

  const refreshButtonSubSector = document.getElementById('refreshButtonSubSector');
  if (refreshButtonSubSector) {
    refreshButtonSubSector.addEventListener('click', (event: Event) => {
      event.preventDefault();
      auxiliarSelect(fitxa?.sub_sector_id ?? 3, 'sub_sectors_economics', 'sub_sector', 'sub_sector_cat');
    });
  }

  // Asegurarse que 'empresa' es un input
  auxiliarSelect(fitxa?.empresa_id ?? 1, 'empreses', 'empresa', 'empresa_ca', '1');

  const refreshButtonEmpresa = document.getElementById('refreshButtonEmpresa');
  if (refreshButtonEmpresa) {
    refreshButtonEmpresa.addEventListener('click', (event: Event) => {
      event.preventDefault();
      auxiliarSelect(fitxa?.empresa_id ?? 1, 'empreses', 'empresa', 'empresa_ca');
    });
  }

  auxiliarSelect(fitxa?.carrecs_empresa_id ?? 1, 'carrecs_empresa', 'carrec_empresa', 'carrec_cat', '1');

  const refreshButtonCarrec = document.getElementById('refreshButtonCarrec');
  if (refreshButtonCarrec) {
    refreshButtonCarrec.addEventListener('click', (event: Event) => {
      event.preventDefault();
      auxiliarSelect(fitxa?.carrecs_empresa_id ?? 1, 'carrecs_empresa', 'carrec_empresa', 'carrec_cat');
    });
  }
}
