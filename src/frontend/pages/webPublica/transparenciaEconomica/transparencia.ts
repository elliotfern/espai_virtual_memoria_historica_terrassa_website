import { fetchDataGet } from '../../../services/fetchData/fetchDataGet';

interface MovimentCompte {
  id: number;
  concepte: string;
  import: number;
  data: string | null;
  ordre: number;
}

interface TipusMoviments {
  moviments: MovimentCompte[];
  total: number;
}

interface ExerciciComptes {
  exercici: number;
  ingressos: TipusMoviments;
  despeses: TipusMoviments;
  resultat: number;
}

interface ResumGlobal {
  ingressos: number;
  despeses: number;
  resultat: number;
}

interface MovimentsComptesData {
  exercicis: ExerciciComptes[];
  global: ResumGlobal;
}

interface MovimentsComptesResponse {
  status: string;
  message: string;
  errors: string[];
  data: MovimentsComptesData;
}

export async function carregarMovimentsComptes(): Promise<MovimentsComptesData | null> {
  const response = await fetchDataGet<MovimentsComptesResponse>(
    '/api/transparencia/get/movimentsComptes'
  );

  if (!response || response.status !== 'success') {
    return null;
  }

  return response.data;
}

function formatImport(importValue: number): string {
  return (
    new Intl.NumberFormat('ca-ES', {
      minimumFractionDigits: 2,
      maximumFractionDigits: 2,
    }).format(importValue) + ' €'
  );
}

function formatResultat(resultat: number): string {
  const importFormatat = formatImport(Math.abs(resultat));

  return resultat >= 0 ? `+ ${importFormatat}` : `− ${importFormatat}`;
}

function renderTaulaMoviments(tipus: 'ingressos' | 'despeses', exercici: ExerciciComptes): string {
  const dades = exercici[tipus];

  const files = dades.moviments
    .map(
      (moviment) => `
        <tr>
            <td>${moviment.concepte}</td>
            <td class="text-end">${formatImport(moviment.import)}</td>
        </tr>
    `
    )
    .join('');

  const nomTipus = tipus === 'ingressos' ? 'ingressos' : 'despeses';

  return `
        <div class="table-responsive ${tipus === 'ingressos' ? 'mb-5' : ''}">
            <table class="table table-striped table-hover align-middle">
                <thead class="table-light">
                    <tr>
                        <th scope="col">Concepte</th>
                        <th scope="col" class="text-end">Import</th>
                    </tr>
                </thead>

                <tbody>
                    ${files}
                </tbody>

                <tfoot class="table-group-divider">
                    <tr class="fw-bold">
                        <td>Total ${nomTipus} ${exercici.exercici}</td>
                        <td class="text-end">
                            ${formatImport(dades.total)}
                        </td>
                    </tr>
                </tfoot>
            </table>
        </div>
    `;
}

function renderResultat(exercici: ExerciciComptes): string {
  const classeResultat = exercici.resultat >= 0 ? 'text-success' : 'text-danger';

  return `
        <div class="table-responsive mt-5">
            <table class="table table-bordered align-middle">
                <tbody>
                    <tr class="fw-bold">
                        <td>
                            Resultat de l'exercici ${exercici.exercici}
                            (ingressos − despeses)
                        </td>

                        <td class="text-end ${classeResultat}">
                            ${formatResultat(exercici.resultat)}
                        </td>
                    </tr>
                </tbody>
            </table>

            ${
              exercici.exercici === 2025
                ? `
                        <p class="text-muted small">
                            Part dels ingressos corresponents a aquest exercici es van cobrar el 2026.
                        </p>
                    `
                : ''
            }
        </div>
    `;
}

function renderExercici(exercici: ExerciciComptes): string {
  return `
        <div
            class="tab-pane fade"
            id="any-${exercici.exercici}"
            role="tabpanel"
            aria-labelledby="tab-${exercici.exercici}"
            tabindex="0"
        >
            <h2 class="h4">Ingressos ${exercici.exercici}</h2>

            ${renderTaulaMoviments('ingressos', exercici)}

            <h2 class="h4">Despeses ${exercici.exercici}</h2>

            ${renderTaulaMoviments('despeses', exercici)}

            ${renderResultat(exercici)}
        </div>
    `;
}

function renderResumGlobal(data: MovimentsComptesData): string {
  const files = data.exercicis
    .map((exercici) => {
      const classeResultat = exercici.resultat >= 0 ? 'text-success' : 'text-danger';

      return `
            <tr>
                <td>${exercici.exercici}</td>

                <td class="text-end">
                    ${formatImport(exercici.ingressos.total)}
                </td>

                <td class="text-end">
                    ${formatImport(exercici.despeses.total)}
                </td>

                <td class="text-end ${classeResultat}">
                    ${formatResultat(exercici.resultat)}
                </td>
            </tr>
        `;
    })
    .join('');

  const classeResultatGlobal = data.global.resultat >= 0 ? 'text-success' : 'text-danger';

  return `
        <div
            class="tab-pane fade"
            id="any-total"
            role="tabpanel"
            aria-labelledby="tab-total"
            tabindex="0"
        >
            <h2 class="h4">
                Resum global 2024-${data.exercicis[data.exercicis.length - 1]?.exercici ?? ''}
            </h2>

            <div class="table-responsive">
                <table class="table table-striped table-hover align-middle">
                    <thead class="table-light">
                        <tr>
                            <th scope="col">Exercici</th>
                            <th scope="col" class="text-end">Ingressos</th>
                            <th scope="col" class="text-end">Despeses</th>
                            <th scope="col" class="text-end">Resultat</th>
                        </tr>
                    </thead>

                    <tbody>
                        ${files}
                    </tbody>

                    <tfoot class="table-group-divider">
                        <tr class="fw-bold">
                            <td>Total acumulat</td>

                            <td class="text-end">
                                ${formatImport(data.global.ingressos)}
                            </td>

                            <td class="text-end">
                                ${formatImport(data.global.despeses)}
                            </td>

                            <td class="text-end ${classeResultatGlobal}">
                                ${formatResultat(data.global.resultat)}
                            </td>
                        </tr>
                    </tfoot>
                </table>
            </div>

            <p class="text-muted small mt-3">
               Les dades de ${data.exercicis[data.exercicis.length - 1]?.exercici ?? ''}
                són provisionals i poden variar fins al tancament de l'exercici.
            </p>
        </div>
    `;
}

function renderTransparencia(data: MovimentsComptesData): void {
  const container = document.getElementById('transparencia');

  if (!container) {
    return;
  }

  const exercicis = data.exercicis;

  const tabs = exercicis
    .map((exercici, index) => {
      const active = index === 0;

      return `
            <li class="nav-item" role="presentation">
                <button
                    class="nav-link${active ? ' active' : ''}"
                    id="tab-${exercici.exercici}"
                    data-bs-toggle="tab"
                    data-bs-target="#any-${exercici.exercici}"
                    type="button"
                    role="tab"
                    aria-controls="any-${exercici.exercici}"
                    aria-selected="${active}"
                >
                    Any ${exercici.exercici}
                </button>
            </li>
        `;
    })
    .join('');

  const panes = exercicis.map((exercici) => renderExercici(exercici)).join('');

  container.innerHTML = `
        <section class="container my-5" id="comptes">
            <h1 class="h2 mb-4">Rendició de Comptes</h1>

            <ul class="nav nav-tabs" id="comptesTabs" role="tablist">
                ${tabs}

                <li class="nav-item" role="presentation">
                    <button
                        class="nav-link"
                        id="tab-total"
                        data-bs-toggle="tab"
                        data-bs-target="#any-total"
                        type="button"
                        role="tab"
                        aria-controls="any-total"
                        aria-selected="false"
                    >
                        Resum global
                    </button>
                </li>
            </ul>

            <div class="tab-content pt-4" id="comptesTabsContent">
                ${panes}
                ${renderResumGlobal(data)}
            </div>
        </section>
    `;

  const primeraPestanya = container.querySelector<HTMLButtonElement>('#comptesTabs .nav-link');

  const primerContingut = container.querySelector<HTMLElement>('#comptesTabsContent .tab-pane');

  if (primeraPestanya && primerContingut) {
    primeraPestanya.classList.add('active');
    primeraPestanya.setAttribute('aria-selected', 'true');

    primerContingut.classList.add('show', 'active');
  }
}

export async function initTransparencia(): Promise<void> {
  const data = await carregarMovimentsComptes();

  if (!data) {
    return;
  }

  renderTransparencia(data);
}

void initTransparencia();
