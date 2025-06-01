import { Fitxa } from '../../types/types';
import { auxiliarSelect } from '../../services/fetchData/auxiliarSelect';

export function tab4(fitxa: Fitxa) {
  auxiliarSelect(fitxa.estudis_id, 'estudis', 'estudis', 'estudi_cat', '3');
  auxiliarSelect(fitxa.ofici_id, 'oficis', 'ofici', 'ofici_cat', '38');

  const refreshButtonOfici = document.getElementById('refreshButtonOfici');
  if (refreshButtonOfici) {
    refreshButtonOfici.addEventListener('click', (event: Event) => {
      event.preventDefault();
      auxiliarSelect(fitxa.ofici_id, 'oficis', 'ofici', 'ofici_cat');
    });
  }

  auxiliarSelect(fitxa.sector_id, 'sectors_economics', 'sector', 'sector_cat', '4');
  auxiliarSelect(fitxa.sub_sector_id, 'sub_sectors_economics', 'sub_sector', 'sub_sector_cat', '3');

  const refreshButtonSubSector = document.getElementById('refreshButtonSubSector');
  if (refreshButtonSubSector) {
    refreshButtonSubSector.addEventListener('click', (event: Event) => {
      event.preventDefault();
      auxiliarSelect(fitxa.sub_sector_id, 'sub_sectors_economics', 'sub_sector', 'sub_sector_cat');
    });
  }

  // Asegurarse que 'empresa' es un input
  const empresaInput = document.getElementById('empresa') as HTMLInputElement | null;
  if (empresaInput && fitxa.empresa !== undefined) {
    empresaInput.value = fitxa.empresa && fitxa.empresa.trim() !== '' ? fitxa.empresa : 'Desconeguda';
  }

  auxiliarSelect(fitxa.carrecs_empresa_id, 'carrecs_empresa', 'carrec_empresa', 'carrec_cat', '1');

  const refreshButtonCarrec = document.getElementById('refreshButtonCarrec');
  if (refreshButtonCarrec) {
    refreshButtonCarrec.addEventListener('click', (event: Event) => {
      event.preventDefault();
      auxiliarSelect(fitxa.carrecs_empresa_id, 'carrecs_empresa', 'carrec_empresa', 'carrec_cat');
    });
  }
}
