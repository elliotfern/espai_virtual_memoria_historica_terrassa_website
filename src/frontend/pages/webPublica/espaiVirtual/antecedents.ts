import { API_URLS } from '../../../services/api/ApiUrls';
import { fetchDataGet } from '../../../services/fetchData/fetchDataGet';

type Lang = 'ca' | 'es' | 'en' | 'fr' | 'it' | 'pt';

interface AntecedentPublicRow {
  id: number;
  ordre: number;
  image_id: number | null;
  layout_image_left: number;
  show_in_timeline: number;
  any_text: string;
  titol: string;
  contingut_html: string;
  link_url: string | null;
  image_url: string | null;
}

interface ApiResponse<T> {
  status: string;
  message: string;
  data: T;
}

function escapeHtml(value: string): string {
  const div = document.createElement('div');
  div.textContent = value;
  return div.innerHTML;
}

function getLabels(lang: Lang) {
  const map = {
    ca: {
      sectionTitle: 'Antecedents',
      sectionSubtitle: "Explora els moments clau en la creació i evolució d'aquest<br> espai dedicat a la memòria històrica de Terrassa.",
      showMore: 'veure més',
      showLess: 'veure menys',
      moreInfo: 'més informació',
    },
    es: {
      sectionTitle: 'Antecedentes',
      sectionSubtitle: 'Explora los momentos clave en la creación y evolución de este<br> espacio dedicado a la memoria histórica de Terrassa.',
      showMore: 'ver más',
      showLess: 'ver menos',
      moreInfo: 'más información',
    },
    en: {
      sectionTitle: 'Background',
      sectionSubtitle: 'Explore the key moments in the creation and evolution of this<br> space dedicated to the historical memory of Terrassa.',
      showMore: 'see more',
      showLess: 'see less',
      moreInfo: 'more information',
    },
    fr: {
      sectionTitle: 'Antécédents',
      sectionSubtitle: "Explorez les moments clés de la création et de l'évolution de cet<br> espace consacré à la mémoire historique de Terrassa.",
      showMore: 'voir plus',
      showLess: 'voir moins',
      moreInfo: "plus d'informations",
    },
    it: {
      sectionTitle: 'Antecedenti',
      sectionSubtitle: 'Esplora i momenti chiave nella creazione e nell’evoluzione di questo<br> spazio dedicato alla memoria storica di Terrassa.',
      showMore: 'vedi di più',
      showLess: 'vedi di meno',
      moreInfo: 'maggiori informazioni',
    },
    pt: {
      sectionTitle: 'Antecedentes',
      sectionSubtitle: 'Explora os momentos-chave na criação e evolução deste<br> espaço dedicado à memória histórica de Terrassa.',
      showMore: 'ver mais',
      showLess: 'ver menos',
      moreInfo: 'mais informação',
    },
  };

  return map[lang] ?? map.ca;
}

function renderTimelineItem(item: AntecedentPublicRow, lang: Lang): string {
  const labels = getLabels(lang);

  return `
    <div class="timeline-item">
      <div class="card ${Number(item.layout_image_left) === 1 ? 'text-start' : ''}">
        <div class="card-body">
          <h5 class="card-title lora marro2">${escapeHtml(item.any_text || '')}</h5>
          <p class="card-text blanc raleway negreta">
            ${escapeHtml(item.titol || '')}
          </p>

          <button
            type="button"
            class="btn btn-primary btn-custom-2 w-auto align-self-start btn-antecedent-toggle"
            data-target="antecedent-detail-${item.id}"
            data-label-open="${escapeHtml(labels.showMore)}"
            data-label-close="${escapeHtml(labels.showLess)}"
          >
            ${escapeHtml(labels.showMore)}
          </button>
        </div>
      </div>
    </div>
  `;
}

function renderDetailImage(item: AntecedentPublicRow): string {
  if (!item.image_url) {
    return '';
  }

  return `
    <div class="col-md-4 text-center">
      <img src="https://media.memoriaterrassa.cat/assets_web/${escapeHtml(item.image_url)}.jpg" class="img-fluid" alt="${escapeHtml(item.titol || '')}">
    </div>
  `;
}

function renderDetailBlock(item: AntecedentPublicRow, lang: Lang): string {
  const labels = getLabels(lang);
  const textColClass = item.image_url ? 'col-md-8' : 'col-md-12';

  const textCol = `
    <div class="${textColClass} raleway d-flex flex-column d-grid gap-2">
      <span class="titol gran lora negreta">${escapeHtml(item.any_text || '')}</span>
      <span class="titol italic-text gran lora">${escapeHtml(item.titol || '')}</span>
      ${item.contingut_html || ''}
      ${
        item.link_url
          ? `
            <a
              href="${escapeHtml(item.link_url)}"
              target="_blank"
              rel="noopener noreferrer"
              class="btn btn-primary btn-custom-2 w-auto align-self-start"
            >
              ${escapeHtml(labels.moreInfo)}
            </a>
          `
          : ''
      }
    </div>
  `;

  const imageCol = renderDetailImage(item);

  return `
    <div
      id="antecedent-detail-${item.id}"
      class="container mt-4 antecedent-detail d-none"
      style="padding-top: 60px;padding-bottom:60px;"
      data-antecedent-id="${item.id}"
    >
      <div class="row">
        ${Number(item.layout_image_left) === 1 ? imageCol + textCol : textCol + imageCol}
      </div>
    </div>
  `;
}

function renderAntecedentsSection(items: AntecedentPublicRow[], lang: Lang): string {
  const labels = getLabels(lang);
  const visibleItems = items.filter((item) => Number(item.show_in_timeline) === 1);

  return `
    <div class="container-fluid" style="padding-top:60px;padding-bottom:60px;background-color:#EEEAD9">
      <div class="container py-5 d-flex flex-column">
        <span class="titol gran lora negreta">${escapeHtml(labels.sectionTitle)}</span>
        <span class="titol italic-text gran lora">${labels.sectionSubtitle}</span>
      </div>

      <div class="container py-5">
        <div class="timeline">
          ${visibleItems.map((item) => renderTimelineItem(item, lang)).join('')}
        </div>
      </div>

      ${visibleItems.map((item) => renderDetailBlock(item, lang)).join('')}
    </div>
  `;
}

function initAntecedentToggle(container: HTMLElement): void {
  const buttons = Array.from(container.querySelectorAll('.btn-antecedent-toggle')) as HTMLButtonElement[];
  const detailBlocks = Array.from(container.querySelectorAll('.antecedent-detail')) as HTMLDivElement[];

  const hideAllDetails = () => {
    detailBlocks.forEach((detail) => detail.classList.add('d-none'));

    buttons.forEach((button) => {
      const openLabel = button.dataset.labelOpen ?? 'veure més';
      button.textContent = openLabel;
    });
  };

  buttons.forEach((button) => {
    button.addEventListener('click', () => {
      const targetId = button.dataset.target;
      if (!targetId) return;

      const target = container.querySelector(`#${targetId}`) as HTMLDivElement | null;
      if (!target) return;

      const isHidden = target.classList.contains('d-none');

      hideAllDetails();

      if (isHidden) {
        target.classList.remove('d-none');
        button.textContent = button.dataset.labelClose ?? 'veure menys';

        window.setTimeout(() => {
          target.scrollIntoView({
            behavior: 'smooth',
            block: 'start',
          });
        }, 80);
      }
    });
  });
}

export async function blocAntecedentsPublic(lang: Lang): Promise<void> {
  const container = document.getElementById('blocAntecedents') as HTMLDivElement | null;
  if (!container) return;

  const response = await fetchDataGet<ApiResponse<AntecedentPublicRow[]>>(API_URLS.GET.PUBLIC_ANTECEDENTS(lang), false);

  if (!response || !response.data) {
    container.innerHTML = '';
    return;
  }

  container.innerHTML = renderAntecedentsSection(response.data, lang);
  initAntecedentToggle(container);
}
