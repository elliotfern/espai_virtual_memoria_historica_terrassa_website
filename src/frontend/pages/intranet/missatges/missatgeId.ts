// Tipos de la resposta de la API
interface MissatgeData {
  id: number;
  nomCognoms: string;
  email: string;
  telefon: string | null;
  missatge: string;
  form_ip: string;
  form_user_agent: string;
  dataEnviament: string; // "YYYY-MM-DD HH:mm:ss"
}

interface ApiResponse {
  status: 'success' | 'error';
  message: string;
  errors: unknown[];
  data: MissatgeData;
}

// Escapar HTML per seguretat
function escapeHtml(str: string | null | undefined): string {
  if (!str) return '';
  return str.replace(/&/g, '&amp;').replace(/</g, '&lt;').replace(/>/g, '&gt;').replace(/"/g, '&quot;').replace(/'/g, '&#039;');
}

// Pintar la card dins del div#missatgeId
function renderMissatge(response: ApiResponse): void {
  const container = document.getElementById('missatgeId');
  if (!container) return;

  if (response.status !== 'success' || !response.data) {
    container.innerHTML = `
      <div class="alert alert-danger mb-0">
        No s'ha pogut carregar el missatge.
      </div>
    `;
    return;
  }

  const { id, nomCognoms, email, telefon, missatge, form_ip, form_user_agent, dataEnviament } = response.data;

  const cardHtml = `
    <div class="card shadow-sm mb-3">
      <div class="card-header d-flex justify-content-between align-items-center">
        <div>
          <h5 class="mb-0">${escapeHtml(nomCognoms)}</h5>
          <small class="text-muted">
            ID #${id} · ${escapeHtml(dataEnviament)}
          </small>
        </div>
        <span class="badge bg-success text-uppercase">Missatge</span>
      </div>

      <div class="card-body">
        <dl class="row mb-3">
          <dt class="col-sm-3">Email</dt>
          <dd class="col-sm-9 mb-1">
            <a href="mailto:${escapeHtml(email)}">${escapeHtml(email)}</a>
          </dd>

          <dt class="col-sm-3">Telèfon</dt>
          <dd class="col-sm-9 mb-1">
            ${telefon ? escapeHtml(telefon) : '<span class="text-muted">No informat</span>'}
          </dd>

          <dt class="col-sm-3">Data enviament</dt>
          <dd class="col-sm-9 mb-1">
            ${escapeHtml(dataEnviament)}
          </dd>
        </dl>

        <h6 class="fw-semibold">Missatge</h6>
        <div class="border rounded p-3 bg-light" style="white-space: pre-line;">
          ${escapeHtml(missatge)}
        </div>
      </div>

      <div class="card-footer text-muted small d-flex flex-wrap gap-3">
        <span><strong>IP:</strong> ${escapeHtml(form_ip)}</span>
        <span class="text-truncate">
          <strong>User agent:</strong> ${escapeHtml(form_user_agent)}
        </span>
      </div>
    </div>
  `;

  container.innerHTML = cardHtml;
}

// --- FETCH A LA API ---

const API_URL = 'https://memoriaterrassa.cat/api/form_contacte/get/missatgeId';

export async function carregarMissatge(id: number): Promise<void> {
  const container = document.getElementById('missatgeId');
  if (container) {
    container.innerHTML = `
      <div class="text-center py-4 text-muted">
        Carregant missatge...
      </div>
    `;
  }

  try {
    const response = await fetch(`${API_URL}?id=${encodeURIComponent(id.toString())}`, {
      method: 'GET',
      headers: {
        Accept: 'application/json',
      },
    });

    if (!response.ok) {
      throw new Error(`Error HTTP: ${response.status}`);
    }

    const data: ApiResponse = await response.json();
    renderMissatge(data);
  } catch (error) {
    console.error('Error carregant el missatge:', error);
    if (container) {
      container.innerHTML = `
        <div class="alert alert-danger">
          S'ha produït un error en carregar el missatge.
        </div>
      `;
    }
  }
}
