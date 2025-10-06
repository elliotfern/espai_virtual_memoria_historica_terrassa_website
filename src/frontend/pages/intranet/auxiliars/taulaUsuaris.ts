import { renderTaula } from '../../../services/renderTaula/renderTaula';

type Usuari = {
  nom: string;
  email: string;
  biografia_cat: string;
  tipus: string;
  id: number;
};

type ApiResponse<T> = {
  status: 'success' | 'error';
  message: string;
  errors: unknown[];
  data: T;
};

export async function taulaDadesUsuaris() {
  const url = 'https://memoriaterrassa.cat/api/auth/get/llistatUsuaris';

  const container = document.getElementById('tabla1');
  if (!container) {
    console.error('No existe el contenedor #tabla1');
    return;
  }

  const res = await fetch(url, { headers: { Accept: 'application/json' } });
  if (!res.ok) {
    console.error('Error HTTP', res.status, res.statusText);
    return;
  }

  const json: ApiResponse<Usuari[]> = await res.json();

  if (json.status !== 'success' || !Array.isArray(json.data)) {
    console.error('Respuesta inesperada de la API', json);
    return;
  }

  const rows = json.data;

  renderTaula(rows, container, {
    headersMap: {
      nom: 'Usuari',
      email: 'Email',
      bio_curta_ca: 'Biografia',
      tipus: 'Tipus',
      idBio: 'Accions',
      id: 'Accions',
    },
    columnRenderers: {
      nom: (val) => `<strong>${val}</strong>`,
      email: (val) => `${val}`,
      biografia_cat: (val) => `${val}`,
      tipus: (val) => `${val}`,
      idBio: (val, row) => {
        const id = row.id as string;
        return `<a href="https://${window.location.host}/gestio/auxiliars/modifica-bio-usuari/${id}">
                        <button class="btn btn-secondary btn-sm">Modifica biografia</button></a>`;
      },
      id: (val, row) => {
        const id = row.id as string;
        return `<a href="https://${window.location.host}/gestio/auxiliars/modifica-usuari/${id}">
                        <button class="btn btn-secondary btn-sm">Modifica</button></a>`;
      },
    },
  });
}
