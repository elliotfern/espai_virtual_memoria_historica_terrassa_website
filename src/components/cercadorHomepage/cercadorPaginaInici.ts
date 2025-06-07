import { normalizeText } from '../../services/formatDates/formatText';

interface Persona {
  nom: string;
  cognom1: string;
  cognom2: string;
  id: number;
}

// Simulación de llamada a API (reemplázala con fetch real)
async function fetchPersones(): Promise<Persona[]> {
  try {
    const response = await fetch('https://memoriaterrassa.cat/api/dades_personals/get/?llistatPersonesCercador');
    if (!response.ok) throw new Error(`Error a l'API: ${response.status}`);
    return await response.json();
  } catch (error) {
    console.error("Error carregant dades de l'API:", error);
    return [];
  }
}

// Mostrar resultados

function mostrarResultats(resultats: Persona[], resultsDiv: HTMLElement) {
  if (resultats.length === 0) {
    resultsDiv.innerHTML = "<p>No s'ha trobat cap coincidència.</p>";
    return;
  }

  resultsDiv.innerHTML = resultats
    .map(
      (p) =>
        `<a href="https://memoriaterrassa.cat/fitxa/${p.id}" class="card p-2 mb-2 text-decoration-none text-dark">
          <strong>${p.nom}</strong> ${p.cognom1} ${p.cognom2}
        </a>`
    )
    .join('');
}

// Lógica de filtrado
function filtrarPersones(term: string, persones: Persona[], resultsDiv: HTMLElement) {
  const normalizedTerm = normalizeText(term);
  const searchWords = normalizedTerm.split(' ');

  const resultats = persones.filter((p) => {
    const fullName = normalizeText(`${p.nom} ${p.cognom1} ${p.cognom2}`);

    // Comprobamos que todas las palabras estén incluidas
    return searchWords.every((word) => fullName.includes(word));
  });

  // Limita a 10 resultados
  mostrarResultats(resultats.slice(0, 10), resultsDiv);
}

export async function initBuscador() {
  const searchInput = document.getElementById('searchInput') as HTMLInputElement;
  const resultsDiv = document.getElementById('results') as HTMLElement;

  const persones = await fetchPersones();

  searchInput.addEventListener('input', () => {
    const term = searchInput.value.trim();

    if (term.length < 5) {
      resultsDiv.innerHTML = '<p><span class="avis">Escriu almenys 5 caràcters per començar la cerca.</span></p>';
      return;
    }

    filtrarPersones(term, persones, resultsDiv); // 👈 ESTA línea faltaba
  });
}
