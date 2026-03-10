interface AntecedentI18n {
  id: number;
  antecedent_id: number;
  lang: string;
  any_text: string;
  titol: string;
  resum_timeline: string | null;
  contingut_html: string | null;
  link_url: string | null;
}

interface AntecedentFitxa {
  id: number;
  ordre: number;
  image_id: number | null;
  layout_image_left: number;
  show_in_timeline: number;
  date_created: string;
  date_modified: string | null;
  idiomes: AntecedentI18n[];
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

function renderIdiomaBlock(item: AntecedentI18n): string {
  return `
    <div class="card mb-4">
      <div class="card-body">
        <h4 class="mb-3">${escapeHtml(item.lang.toUpperCase())}</h4>

        <div class="mb-3">
          <strong>Data / període:</strong><br>
          <span>${escapeHtml(item.any_text || '')}</span>
        </div>

        <div class="mb-3">
          <strong>Títol:</strong><br>
          <span>${escapeHtml(item.titol || '')}</span>
        </div>

        <div class="mb-3">
          <strong>Resum timeline:</strong><br>
          <span>${escapeHtml(item.resum_timeline || '')}</span>
        </div>

        <div class="mb-3">
          <strong>Enllaç:</strong><br>
          ${item.link_url ? `<a href="${escapeHtml(item.link_url)}" target="_blank" rel="noopener noreferrer">${escapeHtml(item.link_url)}</a>` : '<span></span>'}
        </div>

        <div class="mb-3">
          <strong>Contingut HTML:</strong><br>
          <div class="border rounded p-3 bg-light">
            ${item.contingut_html || ''}
          </div>
        </div>
      </div>
    </div>
  `;
}

function renderFitxa(data: AntecedentFitxa): string {
  return `
    <div class="container-fluid px-0">
      <div class="card mb-4">
        <div class="card-body">
          <h2 class="mb-4">Fitxa antecedent #${data.id}</h2>

          <div class="row g-3">
            <div class="col-md-3">
              <strong>ID</strong><br>
              <span>${data.id}</span>
            </div>

            <div class="col-md-3">
              <strong>Ordre</strong><br>
              <span>${data.ordre}</span>
            </div>

            <div class="col-md-3">
              <strong>ID imatge</strong><br>
              <span>${data.image_id ?? ''}</span>
            </div>

            <div class="col-md-3">
              <strong>Mostrar timeline</strong><br>
              <span>${Number(data.show_in_timeline) === 1 ? 'Sí' : 'No'}</span>
            </div>

            <div class="col-md-3">
              <strong>Imatge a l'esquerra</strong><br>
              <span>${Number(data.layout_image_left) === 1 ? 'Sí' : 'No'}</span>
            </div>

            <div class="col-md-3">
              <strong>Creat</strong><br>
              <span>${escapeHtml(data.date_created || '')}</span>
            </div>

            <div class="col-md-3">
              <strong>Modificat</strong><br>
              <span>${escapeHtml(data.date_modified || '')}</span>
            </div>
          </div>
        </div>
      </div>

      <div class="d-flex flex-column gap-3">
        ${data.idiomes.map(renderIdiomaBlock).join('')}
      </div>
    </div>
  `;
}

export async function fitxaAntecedent(id: number): Promise<void> {
  const container = document.getElementById('fitxaAntecedent') as HTMLDivElement | null;

  if (!container) {
    return;
  }

  if (!id) {
    container.innerHTML = `
      <div class="alert alert-danger" role="alert">
        ID d'antecedent no vàlid.
      </div>
    `;
    return;
  }

  container.innerHTML = `
    <div class="py-4">Carregant dades...</div>
  `;

  try {
    const response = await fetch(`https://${window.location.host}/api/antecedents/get/antecedentId?id=${id}`, {
      method: 'GET',
      credentials: 'include',
      headers: {
        Accept: 'application/json',
      },
    });

    if (!response.ok) {
      throw new Error(`HTTP ${response.status}`);
    }

    const json = (await response.json()) as ApiResponse<AntecedentFitxa>;

    if (!json?.data) {
      throw new Error('Resposta buida');
    }

    container.innerHTML = renderFitxa(json.data);
  } catch (error) {
    console.error('Error carregant la fitxa de l’antecedent:', error);

    container.innerHTML = `
      <div class="alert alert-danger" role="alert">
        No s'ha pogut carregar la fitxa de l'antecedent.
      </div>
    `;
  }
}
