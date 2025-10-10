import { DOMAIN_API, DOMAIN_WEB } from '../../config/constants';
import { getApiArray } from '../../services/api/http';
import { normalizeText } from '../../services/formatDates/formatText';
import { LABELS_SEARCH } from '../../services/i18n/homePage/cercador';
import { t } from '../../services/i18n/i18n';

interface Persona {
  nom: string;
  cognom1: string;
  cognom2: string;
  id: number;
  slug: string;
}

function fitxaHref(slug: string, lang: string): string {
  // en catalán no se añade prefijo, en el resto sí
  return lang === 'ca' ? `${DOMAIN_WEB}/fitxa/${slug}` : `${DOMAIN_WEB}/${lang}/fitxa/${slug}`;
}

// Mostrar resultados
function mostrarResultats(resultats: Persona[], resultsDiv: HTMLElement, lang: string) {
  if (resultats.length === 0) {
    resultsDiv.innerHTML = `<p>${t(LABELS_SEARCH, 'noResults', lang)}</p>`;
    return;
  }

  const html = resultats
    .map(
      (p) => `
        <a href="${fitxaHref(p.slug, lang)}" class="card p-2 mb-2 text-decoration-none text-dark">
          <strong>${p.nom}</strong> ${p.cognom1} ${p.cognom2}
        </a>`
    )
    .join('');

  resultsDiv.innerHTML = html;
}

// Lógica de filtrado
function filtrarPersones(term: string, persones: Persona[], resultsDiv: HTMLElement, lang: string) {
  const normalizedTerm = normalizeText(term);
  const searchWords = normalizedTerm.split(' ').filter(Boolean);

  const resultats = persones.filter((p) => {
    const fullName = normalizeText(`${p.nom} ${p.cognom1} ${p.cognom2}`);
    return searchWords.every((word) => fullName.includes(word));
  });

  // Limita a 10 resultados
  mostrarResultats(resultats.slice(0, 10), resultsDiv, lang);
}

export async function initBuscador(lang: string): Promise<void> {
  const run = async () => {
    const searchInput = document.getElementById('searchInput') as HTMLInputElement | null;
    const resultsDiv = document.getElementById('results') as HTMLElement | null;

    if (!searchInput || !resultsDiv) return;

    const url = `${DOMAIN_API}/dades_personals/get/?llistatPersonesCercador`;
    const persones = await getApiArray<Persona>(url);

    searchInput.addEventListener('input', () => {
      const term = searchInput.value.trim();

      if (term.length < 5) {
        resultsDiv.innerHTML = `<p><span class="avis">${t(LABELS_SEARCH, 'minChars', lang)}</span></p>`;
        return;
      }

      filtrarPersones(term, persones, resultsDiv, lang);
    });
  };

  if (document.readyState === 'loading') {
    document.addEventListener(
      'DOMContentLoaded',
      () => {
        void run();
      },
      { once: true }
    );
  } else {
    void run();
  }
}
