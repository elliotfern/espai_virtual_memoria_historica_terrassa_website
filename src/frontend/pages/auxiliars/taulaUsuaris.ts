import { renderTaula } from '../../services/renderTaula/renderTaula';

export async function taulaDadesUsuaris() {
  const url = 'https://memoriaterrassa.cat/api/auxiliars/get/usuaris';

  const data1 = await fetch(url).then((r) => r.json());

  renderTaula(data1, document.getElementById('tabla1')!, {
    //excludeFields: ['id'],
    headersMap: {
      nom: 'Usuari',
      email: 'Email',
      biografia_cat: 'Biografia',
      tipus: 'Tipus',
      id: 'Accions',
    },
    columnRenderers: {
      nom: (val) => `<strong>${val}</strong>`,
      email: (val) => `${val}`,
      biografia_cat: (val) => `${val}`,
      tipus: (val) => `${val}`,
      id: (val, row) => {
        const id = row.id as string;
        return `<a href="https://${window.location.host}/gestio/auxiliars/modifica-usuari/${id}">
                        <button class="btn btn-secondary btn-sm">Modifica</button></a>`;
      },
    },
  });
}
