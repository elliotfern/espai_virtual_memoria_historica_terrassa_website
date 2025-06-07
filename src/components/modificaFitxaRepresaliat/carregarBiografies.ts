type Biografia = {
  id: number;
  idRepresaliat: number;
  biografiaCa?: string | null;
  biografiaEs?: string | null;
  biografiaEn?: string | null;
  biografiaFr?: string | null;
  biografiaIt?: string | null;
  biografiaPt?: string | null;
};
export async function carregarBiografies(id: number | string): Promise<void> {
  const quadre = document.getElementById('quadreBiografies');
  if (!quadre) return;

  quadre.innerHTML = 'Carregant biografies...';

  try {
    const response = await fetch(`https://memoriaterrassa.cat/api/biografies/get/fitxaBiografia?id=${id}`);
    const data: Biografia[] = await response.json();

    // Validar que hay al menos un resultado

    if (Array.isArray(data) && data.length > 0 && data[0].biografiaCa) {
      crearBoto(id, true);
    } else {
      crearBoto(id, false);
      quadre.innerHTML = '<div class="alert alert-warning">Biografia no disponible.</div>';
      return;
    }

    const bio = data[0];

    const traducciones: { [key: string]: string } = {
      biografiaCa: 'Català',
      biografiaEs: 'Castellà',
      biografiaEn: 'Anglès',
      biografiaFr: 'Francès',
      biografiaIt: 'Italià',
      biografiaPt: 'Portugués',
    };

    const biografiesDisponibles = Object.entries(traducciones)
      .filter(([key]) => {
        const valor = bio[key as keyof Biografia];
        return typeof valor === 'string' && valor.trim() !== '';
      })
      .map(([key, idioma]) => ({
        key,
        idioma,
        html: bio[key as keyof Biografia] as string,
      }));

    if (biografiesDisponibles.length === 0) {
      quadre.innerHTML = '<div class="alert alert-warning">Biografia no disponible.</div>';
      return;
    }

    // Crear tabs Bootstrap
    const navTabs = document.createElement('ul');
    navTabs.className = 'nav nav-tabs mb-3';
    navTabs.role = 'tablist';

    const tabContent = document.createElement('div');
    tabContent.className = 'tab-content';

    biografiesDisponibles.forEach((bio, index) => {
      const isActive = index === 0 ? 'active' : '';

      const li = document.createElement('li');
      li.className = 'nav-item';

      const button = document.createElement('button');
      button.className = `nav-link ${isActive}`;
      button.id = `tab-${bio.key}`;
      button.setAttribute('data-bs-toggle', 'tab');
      button.setAttribute('data-bs-target', `#pane-${bio.key}`);
      button.type = 'button';
      button.role = 'tab';
      button.setAttribute('aria-controls', `pane-${bio.key}`);
      button.setAttribute('aria-selected', `${index === 0}`);
      button.textContent = bio.idioma;

      li.appendChild(button);
      navTabs.appendChild(li);

      const pane = document.createElement('div');
      pane.style.padding = '20px';
      pane.className = `tab-pane fade ${isActive} show`;
      pane.id = `pane-${bio.key}`;
      pane.role = 'tabpanel';
      pane.setAttribute('aria-labelledby', `tab-${bio.key}`);
      pane.innerHTML = bio.html;

      tabContent.appendChild(pane);
    });

    quadre.innerHTML = '';
    quadre.appendChild(navTabs);
    quadre.appendChild(tabContent);
  } catch (error) {
    console.error('Error carregant biografies:', error);
    quadre.innerHTML = '<div class="alert alert-danger">Error carregant les dades.</div>';
  }
}

function crearBoto(idPersona: number | string, tieneBiografia: boolean) {
  const container = document.getElementById('botonsBiografies');
  if (!container) return;
  container.innerHTML = ''; // Limpiamos antes de añadir nuevo botón

  const div = document.createElement('div');
  div.className = 'd-flex gap-2 mt-3 mb-3';
  div.style.marginTop = '20px';
  div.style.marginBottom = '20px';

  const link = document.createElement('a');
  link.className = 'btn btn-success';
  link.target = '_blank';

  if (tieneBiografia) {
    link.href = `https://memoriaterrassa.cat/gestio/biografies/modifica-biografia/${idPersona}`;
    link.textContent = 'Modifica biografies';
  } else {
    link.href = `https://memoriaterrassa.cat/gestio/biografies/nova-biografia/${idPersona}`;
    link.textContent = 'Afegir biografia';
  }

  const updateBtn = document.createElement('button');
  updateBtn.className = 'btn btn-secondary';
  updateBtn.textContent = 'Actualitza biografies';
  updateBtn.style.marginRight = '10px';
  updateBtn.onclick = (event) => {
    event.preventDefault();
    carregarBiografies(Number(idPersona));
  };

  div.appendChild(updateBtn);
  div.appendChild(link);

  container.appendChild(div);
}
