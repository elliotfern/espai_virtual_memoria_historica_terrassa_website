import { API_URLS } from '../../../services/api/ApiUrls';
import { fetchDataGet } from '../../../services/fetchData/fetchDataGet';

interface ApiResponse<T> {
  status: string;
  message: string;
  data: T;
}

interface UserInfo {
  id: number;
  nom: string | null;
  email: string | null;
}

interface MonthRow {
  y: number; // year
  m: number; // month 1..12
  hores: number;
}

interface YearRow {
  y: number;
  hores: number;
}

interface ResumUsuariPayload {
  user: UserInfo;
  months: MonthRow[];
  years: YearRow[];
  total: number;
}

const MONTHS_CA = ['Gener', 'Febrer', 'Març', 'Abril', 'Maig', 'Juny', 'Juliol', 'Agost', 'Setembre', 'Octubre', 'Novembre', 'Desembre'] as const;

function monthNameCa(m: number): string {
  if (!Number.isFinite(m) || m < 1 || m > 12) return 'Mes';
  return MONTHS_CA[m - 1];
}

function escapeHtml(input: unknown): string {
  return String(input ?? '')
    .replaceAll('&', '&amp;')
    .replaceAll('<', '&lt;')
    .replaceAll('>', '&gt;')
    .replaceAll('"', '&quot;')
    .replaceAll("'", '&#039;');
}

function formatHores(n: number): string {
  const v = Number(n);
  if (!Number.isFinite(v)) return '0';
  return String(Math.trunc(v));
}

export async function resumUsuariHores(userId: number) {
  const container = document.getElementById('resumUsuariHores') as HTMLDivElement | null;
  if (!container) return;

  if (!Number.isFinite(userId) || userId <= 0) {
    container.innerHTML = `<div class="alert alert-danger">ID d'usuari invàlid.</div>`;
    return;
  }

  const resp = await fetchDataGet<ApiResponse<ResumUsuariPayload>>(API_URLS.GET.HORES_RESUM_USUARI(userId), true);

  if (!resp || !resp.data) {
    container.innerHTML = `<div class="alert alert-danger">No s'ha pogut carregar el resum.</div>`;
    return;
  }

  const { user, months, years, total } = resp.data;

  // ---- Render usuario ----
  const userNom = escapeHtml(user?.nom ?? `Usuari ${userId}`);
  const userEmail = user?.email ? escapeHtml(user.email) : '';

  // ---- Render meses ----
  const monthsHtml =
    months && months.length
      ? `
        <ul class="list-group mb-4">
          ${months
            .map((r) => {
              const label = `${monthNameCa(r.m)} ${r.y}`;
              return `
                <li class="list-group-item d-flex justify-content-between align-items-center">
                  <span><strong>${escapeHtml(label)}:</strong></span>
                  <span>${formatHores(r.hores)} hores</span>
                </li>
              `;
            })
            .join('')}
        </ul>
      `
      : `<div class="alert alert-info mb-4">Aquest usuari encara no té registres d'hores.</div>`;

  // ---- Render totales por año ----
  const yearsHtml =
    years && years.length
      ? `
        <div class="mb-3">
          ${years
            .map((r) => {
              const label = `Total hores treballades (${r.y}):`;
              return `<div class="d-flex justify-content-between">
                  <strong>${escapeHtml(label)}</strong>
                  <span>${formatHores(r.hores)} hores</span>
                </div>`;
            })
            .join('')}
        </div>
      `
      : '';

  // ---- Total absoluto ----
  const totalHtml = `
    <div class="d-flex justify-content-between border-top pt-3">
      <strong>Total hores:</strong>
      <span>${formatHores(total)} hores</span>
    </div>
  `;

  container.innerHTML = `
    <div class="container" style="margin-bottom:50px;border: 1px solid gray;border-radius: 10px;padding:25px;background-color:#eaeaea">
      <div class="container">
        <div class="row">
          <div class="col-12">
            <h2>Resum registre horari</h2>
            <h4 class="mb-1">${userNom}</h4>
            ${userEmail ? `<div class="text-muted mb-3">${userEmail}</div>` : `<div class="mb-3"></div>`}

            <h5 class="mt-3">Per mesos</h5>
            ${monthsHtml}

            ${yearsHtml}

            ${totalHtml}
          </div>
        </div>
      </div>
    </div>
  `;
}
