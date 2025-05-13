import { renderTaula } from '../../services/renderTaula/renderTaula';

export async function TaulaDadesFonts() {
  const data1 = await fetch('/api/auxiliars/get/?llistatArxiusFonts').then((r) => r.json());
  const data2 = await fetch('api/auxiliars/get/?llistatBibliografia').then((r) => r.json());

  renderTaula(data1, document.getElementById('tabla1')!, {
    excludeFields: ['id'],
    headersMap: {
      arxiu: 'Arxiu/Font',
      descripcio: 'Descripció',
      codi: 'Acrònim',
    },
    columnRenderers: {
      arxiu: (val) => `<strong>${val}</strong>`,
      codi: (val) => `${val}`,
      web: (val, row) => {
        const url = row.web as string;
        return `<a href="${url}" target="_blank">${val}</a>`;
      },
    },
  });

  renderTaula(data2, document.getElementById('tabla2')!, {
    excludeFields: ['id'],
    headersMap: {
      llibre: 'Llibre',
      autor: 'Autor/a',
      editorial: 'Editorial',
      ciutat: 'Ciutat',
      any: 'Any',
    },
  });
}
