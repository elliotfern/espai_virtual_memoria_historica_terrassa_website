// Tipus de la resposta de la API (missatge original)
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

// Resposta enviada des de la intranet (gestor)
interface RespostaData {
  id: number;
  missatge_id: number;
  usuari_id: number;
  resposta_subject: string;
  resposta_text: string;
  email_destinatari: string;
  data_resposta: string; // "YYYY-MM-DD HH:mm:ss"
  created_at: string;
  updated_at: string;
  nom: string; // nom de l'usuari gestor que ha respost
}

// Resposta que arriba per email (usuari)
interface RespostaEmailData {
  id: number;
  missatge_id: number;
  email_remitent: string;
  email_rebut: string;
  subject: string;
  body: string;
  rebut_a: string; // "YYYY-MM-DD HH:mm:ss"
  created_at: string;
}

// Conversa completa que retorna la API
interface ConversacioData {
  missatge: MissatgeData;
  respostes_gestor: RespostaData[];
  respostes_email: RespostaEmailData[];
}

interface ConversacioApiResponse {
  status: 'success' | 'error';
  message: string;
  errors: unknown[];
  data: ConversacioData | null;
}

// Tipus unificat per al fil cronològic
type TipusMissatgeFil = 'missatge_original' | 'resposta_gestor' | 'resposta_email_usuari';

interface ItemFil {
  id: string; // ej: "form_5", "gestor_12", "email_7"
  tipus: TipusMissatgeFil;
  autorNom: string;
  autorEmail: string;
  dataIso: string; // data original de la API
  dataFormatejada: string; // "dd/mm/aaaa HH:MM:SS"
  subject?: string;
  text: string;
}

// Escapar HTML per seguretat
function escapeHtml(str: string | null | undefined): string {
  if (!str) return '';
  return str.replace(/&/g, '&amp;').replace(/</g, '&lt;').replace(/>/g, '&gt;').replace(/"/g, '&quot;').replace(/'/g, '&#039;');
}

// --- HELPERS FECHA/HORA ---

// Parsea "YYYY-MM-DD HH:mm:ss" com a data UTC
function parseApiDateUtc(value: string): Date | null {
  if (!value) return null;
  const [datePart, timePart] = value.split(' ');
  if (!datePart || !timePart) return null;

  const [yearStr, monthStr, dayStr] = datePart.split('-');
  const [hourStr, minuteStr, secondStr] = timePart.split(':');

  const year = Number(yearStr);
  const month = Number(monthStr);
  const day = Number(dayStr);
  const hour = Number(hourStr);
  const minute = Number(minuteStr);
  const second = Number(secondStr);

  if (Number.isNaN(year) || Number.isNaN(month) || Number.isNaN(day) || Number.isNaN(hour) || Number.isNaN(minute) || Number.isNaN(second)) {
    return null;
  }

  return new Date(Date.UTC(year, month - 1, day, hour, minute, second));
}

// Formata la data a "dd/mm/aaaa HH:MM:SS" en zona horària Espanya
function formatDataHoraEs(dataEnviament: string): string {
  const d = parseApiDateUtc(dataEnviament);
  if (!d) return '';

  return d.toLocaleString('es-ES', {
    timeZone: 'Europe/Madrid',
    day: '2-digit',
    month: '2-digit',
    year: 'numeric',
    hour: '2-digit',
    minute: '2-digit',
    second: '2-digit',
    hour12: false,
  });
}

// --- CONSTRUCCIÓ DEL FIL DE CONVERSA ---

function construirFilConversacio(data: ConversacioData): ItemFil[] {
  const items: ItemFil[] = [];

  // 1) Missatge original
  const m = data.missatge;
  items.push({
    id: `form_${m.id}`,
    tipus: 'missatge_original',
    autorNom: m.nomCognoms,
    autorEmail: m.email,
    dataIso: m.dataEnviament,
    dataFormatejada: formatDataHoraEs(m.dataEnviament),
    subject: 'Missatge de contacte',
    text: m.missatge,
  });

  // 2) Respostes del gestor (intranet)
  for (const r of data.respostes_gestor) {
    items.push({
      id: `gestor_${r.id}`,
      tipus: 'resposta_gestor',
      autorNom: r.nom,
      // Si més endavant tens el correu del gestor, aquí el pots canviar.
      autorEmail: r.email_destinatari,
      dataIso: r.data_resposta,
      dataFormatejada: formatDataHoraEs(r.data_resposta),
      subject: r.resposta_subject,
      text: r.resposta_text,
    });
  }

  // 3) Respostes via email (usuari)
  for (const e of data.respostes_email) {
    items.push({
      id: `email_${e.id}`,
      tipus: 'resposta_email_usuari',
      autorNom: e.email_remitent,
      autorEmail: e.email_remitent,
      dataIso: e.rebut_a,
      dataFormatejada: formatDataHoraEs(e.rebut_a),
      subject: e.subject,
      text: e.body,
    });
  }

  // 4) Ordenar cronològicament
  items.sort((a, b) => {
    const da = parseApiDateUtc(a.dataIso)?.getTime() ?? 0;
    const db = parseApiDateUtc(b.dataIso)?.getTime() ?? 0;
    return da - db;
  });

  return items;
}

// --- RENDER DE LA CONVERSA COMPLETA ---

const CONVERSACIO_API_URL = 'https://memoriaterrassa.cat/api/form_contacte/get/conversacio';

function renderConversacio(resp: ConversacioApiResponse): void {
  const container = document.getElementById('missatgeId');
  if (!container) return;

  if (resp.status !== 'success' || !resp.data) {
    container.innerHTML = `
      <div class="alert alert-danger mb-0">
        No s'ha pogut carregar la conversa d'aquest missatge.
      </div>
    `;
    return;
  }

  const data = resp.data;
  const fil = construirFilConversacio(data);

  // Card del missatge original
  const m = data.missatge;
  const dataHoraFormatejada = formatDataHoraEs(m.dataEnviament);

  let html = `
    <div class="card shadow-sm mb-3">
      <div class="card-header d-flex justify-content-between align-items-center">
        <div>
          <h5 class="mb-0">${escapeHtml(m.nomCognoms)}</h5>
          <small class="text-muted">
            ID #${m.id} · ${escapeHtml(dataHoraFormatejada)}
          </small>
        </div>
        <span class="badge bg-success text-uppercase">Missatge</span>
      </div>

      <div class="card-body">
        <dl class="row mb-3">
          <dt class="col-sm-3">Email</dt>
          <dd class="col-sm-9 mb-1">
            <a href="mailto:${escapeHtml(m.email)}">${escapeHtml(m.email)}</a>
          </dd>

          <dt class="col-sm-3">Telèfon</dt>
          <dd class="col-sm-9 mb-1">
            ${m.telefon ? escapeHtml(m.telefon) : '<span class="text-muted">No informat</span>'}
          </dd>

          <dt class="col-sm-3">Data enviament</dt>
          <dd class="col-sm-9 mb-1">
            ${escapeHtml(dataHoraFormatejada)}
          </dd>
        </dl>

        <h6 class="fw-semibold">Missatge</h6>
        <div class="border rounded p-3 bg-light mb-3" style="white-space: pre-line;">
          ${escapeHtml(m.missatge)}
        </div>

        <a 
          href="https://memoriaterrassa.cat/gestio/missatges/respondre-missatge/${m.id}" 
          class="btn btn-primary"
        >
          Respondre missatge
        </a>
      </div>

      <div class="card-footer text-muted small d-flex flex-wrap gap-3">
        <span><strong>IP:</strong> ${escapeHtml(m.form_ip)}</span>
        <span class="text-truncate">
          <strong>User agent:</strong> ${escapeHtml(m.form_user_agent)}
        </span>
      </div>
    </div>
  `;

  // Bloc de conversa sota del missatge original
  html += `
    <div class="card mt-3">
      <div class="card-header">
        <h6 class="mb-0">Conversa completa</h6>
      </div>
      <div class="card-body">
  `;

  if (fil.length <= 1) {
    html += `
      <p class="text-muted mb-0">
        Encara no s'ha intercanviat cap resposta per aquest missatge.
      </p>
    `;
  } else {
    html += `<div class="list-group list-group-flush">`;

    for (const item of fil) {
      // El missatge original ja s'ha pintat a la card principal
      if (item.tipus === 'missatge_original') continue;

      const badge = item.tipus === 'resposta_gestor' ? '<span class="badge bg-info text-dark ms-2">Resposta gestor</span>' : '<span class="badge bg-secondary ms-2">Resposta usuari (email)</span>';

      // Botó "Respondre" només per missatges de l'usuari (via email)
      const respondButtonHtml =
        item.tipus === 'resposta_email_usuari'
          ? `
            <div class="mt-2 text-end">
              <a 
                href="https://memoriaterrassa.cat/gestio/missatges/respondre-missatge/${m.id}" 
                class="btn btn-sm btn-outline-primary"
              >
                Respondre
              </a>
            </div>
          `
          : '';

      html += `
        <div class="list-group-item">
          <div class="d-flex justify-content-between align-items-center mb-1">
            <div>
              <strong>${escapeHtml(item.autorNom || item.autorEmail)}</strong>
              ${badge}
            </div>
            <small class="text-muted">${escapeHtml(item.dataFormatejada)}</small>
          </div>
          ${
            item.subject
              ? `
            <p class="mb-1">
              <strong>Assumpte:</strong> ${escapeHtml(item.subject)}
            </p>
          `
              : ''
          }
          <div class="border rounded p-2 bg-light" style="white-space: pre-line;">
            ${escapeHtml(item.text)}
          </div>
          ${respondButtonHtml}
        </div>
      `;
    }

    html += `</div>`;
  }

  html += `
      </div>
    </div>
  `;

  container.innerHTML = html;
}

// --- FETCH A LA API DE CONVERSA ---

export async function carregarConversacioMissatge(id: number): Promise<void> {
  const container = document.getElementById('missatgeId');
  if (container) {
    container.innerHTML = `
      <div class="text-center py-4 text-muted">
        Carregant conversa...
      </div>
    `;
  }

  try {
    const response = await fetch(`${CONVERSACIO_API_URL}?id=${encodeURIComponent(id.toString())}`, {
      method: 'GET',
      headers: {
        Accept: 'application/json',
      },
      credentials: 'include', // per si cal cookie de sessió
    });

    if (!response.ok) {
      throw new Error(`Error HTTP: ${response.status}`);
    }

    const data: ConversacioApiResponse = await response.json();
    renderConversacio(data);
  } catch (error) {
    console.error('Error carregant la conversa:', error);
    if (container) {
      container.innerHTML = `
        <div class="alert alert-danger">
          S'ha produït un error en carregar la conversa del missatge.
        </div>
      `;
    }
  }
}
