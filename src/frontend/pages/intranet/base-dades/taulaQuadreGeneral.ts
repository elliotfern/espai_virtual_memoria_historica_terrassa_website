import { fetchData } from '../../../services/api/api';
import { formatNumberSpanish } from '../../../services/formatDates/formatNumber';

interface ApiTotalResponse {
  status: string;
  message: string;
  errors: unknown[]; // Aquí puedes especificar el tipo de error si es necesario
  data: { total: number }[]; // Un array de objetos con la propiedad 'total'
}

export async function taulaQuadreGeneral() {
  // Llamada reutilizable para obtener los datos y actualizar los elementos
  await fetchAndUpdateData('/api/represaliats/get/totalCostHuma', 'totalCostHuma');
  await fetchAndUpdateData('/api/represaliats/get/totalCombatentsRepublica', 'totalCombatentsRepublica');
  await fetchAndUpdateData('/api/represaliats/get/totalCombatentsSollevats', 'totalCombatentsSollevats');
  await fetchAndUpdateData('/api/represaliats/get/totalCombatentsSenseDefinir', 'totalCombatentsSenseDefinir');
  await fetchAndUpdateData('/api/represaliats/get/totalCivilsBombardeigs', 'totalCivilsBombardeigs');
  await fetchAndUpdateData('/api/represaliats/get/totalCivilsRepresaliaRepublicana', 'totalCivilsRepresaliaRepublicana');
  await fetchAndUpdateData('/api/represaliats/get/totalDeportatsMorts', 'totalDeportatsMorts');
  await fetchAndUpdateData('/api/represaliats/get/totalDeportatsAlliberats', 'totalDeportatsAlliberats');
  await fetchAndUpdateData('/api/represaliats/get/totalDeportatsTotal', 'totalDeportatsTotal');
  await fetchAndUpdateData('/api/represaliats/get/totalExiliatsTotal', 'totalExiliatsTotal');
  await fetchAndUpdateData('/api/represaliats/get/totalExiliatsDeportatsTotal', 'totalExiliatsDeportatsTotal');
  await fetchAndUpdateData('/api/represaliats/get/totalRepresaliats', 'totalRepresaliats');
  await fetchAndUpdateData('/api/represaliats/get/totalAfusellats', 'totalAfusellats');
  await fetchAndUpdateData('/api/represaliats/get/totalProcessats', 'totalProcessats');
}

// Función que hace la consulta y actualiza el valor en el elemento con el id proporcionado
async function fetchAndUpdateData(url: string, elementId: string) {
  try {
    const response = await fetchData<ApiTotalResponse>(url);

    // Verificamos que la respuesta es correcta
    if (response && response.data && response.data.length > 0) {
      const total = response.data[0].total; // Accedemos al valor de 'total' del primer objeto
      updateTotals(total, elementId);
    } else {
      console.error('No se encontraron datos o la estructura es incorrecta.');
    }
  } catch (error) {
    console.error('Error al realizar la consulta:', error);
  }
}

function updateTotals(total: number, divId: string) {
  // Obtener el elemento <span> por su id
  const totalElement = document.getElementById(divId);

  // Verificar que el elemento exista antes de intentar actualizar su contenido
  if (totalElement) {
    // Actualizar el contenido del <span> con el total recibido de la API
    const totalNumber = Number(total);

    const totalCostHumaFormat = formatNumberSpanish(totalNumber);
    totalElement.innerHTML = `${totalCostHumaFormat}`;
  }
}
