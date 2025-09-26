import { fetchData } from '../../../services/api/api';
import { formatNumberSpanish } from '../../../services/formatDates/formatNumber';

interface ApiTotalsRow {
  total: number;
  total_completades: number;
}
interface ApiTotalsResponse {
  status: string;
  message: string;
  errors: unknown[];
  data: ApiTotalsRow[]; // una fila con total y total_completades
}

export async function taulaQuadreGeneral() {
  // Mapa “endpoint -> id base” (id base = el que usas en la columna Total)
  // Para la columna Completats se usará el mismo id base con sufijo "_completades"
  const endpoints: Array<{ url: string; idBase: string }> = [
    { url: '/api/represaliats/get/totalCostHuma', idBase: 'totalCostHuma' },
    { url: '/api/represaliats/get/totalCombatentsRepublica', idBase: 'totalCombatentsRepublica' },
    { url: '/api/represaliats/get/totalCombatentsSollevats', idBase: 'totalCombatentsSollevats' },
    { url: '/api/represaliats/get/totalCombatentsSenseDefinir', idBase: 'totalCombatentsSenseDefinir' },
    { url: '/api/represaliats/get/totalCivilsBombardeigs', idBase: 'totalCivilsBombardeigs' },
    { url: '/api/represaliats/get/totalCivilsRepresaliaRepublicana', idBase: 'totalCivilsRepresaliaRepublicana' },

    { url: '/api/represaliats/get/totalDeportatsMorts', idBase: 'totalDeportatsMorts' },
    { url: '/api/represaliats/get/totalDeportatsAlliberats', idBase: 'totalDeportatsAlliberats' },
    { url: '/api/represaliats/get/totalDeportatsTotal', idBase: 'totalDeportatsTotal' },
    { url: '/api/represaliats/get/totalExiliatsTotal', idBase: 'totalExiliatsTotal' },
    { url: '/api/represaliats/get/totalExiliatsDeportatsTotal', idBase: 'totalExiliatsDeportatsTotal' },

    { url: '/api/represaliats/get/totalRepresaliats', idBase: 'totalRepresaliats' },
    { url: '/api/represaliats/get/totalAfusellats', idBase: 'totalAfusellats' },
    { url: '/api/represaliats/get/totalProcessats', idBase: 'totalProcessats' },
    { url: '/api/represaliats/get/totalProcessatsConsellGuerra', idBase: 'totalProcessatsConsellGuerra' },
    { url: '/api/represaliats/get/totalPresoModel', idBase: 'totalPresoModel' },
    { url: '/api/represaliats/get/totalGUTerrassa', idBase: 'totalGUTerrassa' },
    { url: '/api/represaliats/get/totalDMTerrassa', idBase: 'totalDMTerrassa' },
    { url: '/api/represaliats/get/totalResponsabilitats', idBase: 'totalResponsabilitats' },
    { url: '/api/represaliats/get/totalTPO', idBase: 'totalTPO' },
    { url: '/api/represaliats/get/totalComiteRelacions', idBase: 'totalComiteRelacions' },
    { url: '/api/represaliats/get/totalComiteSolidaritat', idBase: 'totalComiteSolidaritat' },
    { url: '/api/represaliats/get/totalCampsTreball', idBase: 'totalCampsTreball' },
    { url: '/api/represaliats/get/totalBatallonsPresos', idBase: 'totalBatallonsPresos' },
    { url: '/api/represaliats/get/totalRepresaliatsPendents', idBase: 'totalRepresaliatsPendents' },

    // Si tienes un endpoint para el total general, añádelo aquí:
    { url: '/api/represaliats/get/totalGeneral', idBase: 'totalGeneral' },
  ];

  await Promise.all(endpoints.map((e) => fetchAndUpdatePair(e.url, e.idBase)));
}

// Hace la consulta y actualiza ambas columnas (Completats y Total)
async function fetchAndUpdatePair(url: string, idBase: string): Promise<void> {
  try {
    const response = await fetchData<ApiTotalsResponse>(url, { noCache: true });

    if (response && response.data && response.data.length > 0) {
      const row = response.data[0];
      const total = Number(row.total ?? 0);
      const completades = Number(row.total_completades ?? 0);

      updateTotalsDOM(idBase, total, completades);
    } else {
      console.error(`[${url}] Respuesta vacía o estructura incorrecta.`);
    }
  } catch (error) {
    console.error(`[${url}] Error al realizar la consulta:`, error);
  }
}

function updateTotalsDOM(idBase: string, total: number, completades: number): void {
  // Columna “Total” (usa el id original)
  const totalEl = document.getElementById(idBase);
  if (totalEl) {
    totalEl.textContent = formatNumberSpanish(total);
  }

  // Columna “Completats” (mismo id con sufijo _completades)
  const completadesEl = document.getElementById(`${idBase}_completades`);
  if (completadesEl) {
    completadesEl.textContent = formatNumberSpanish(completades);
  }
}
