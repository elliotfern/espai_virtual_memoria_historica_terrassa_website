import { Fitxa } from '../../types/types';

const base = `https://memoriaterrassa.cat/gestio/imatge-represaliat`;

/** Renderiza la pestaña 10 (imagen de perfil + acciones) en el contenedor dado */
export function tab10(fitxa: Fitxa): void {
  const container = document.getElementById('imatgePerfil');
  if (!container || !fitxa) return;

  const hasImage = !!fitxa.imatgePerfil; // existe valor no nulo/undefined

  const nomComplet = [fitxa.nom, fitxa.cognom1, fitxa.cognom2].filter(Boolean).join(' ').trim() || `ID ${fitxa.id}`;

  // HTML con estilos Bootstrap 5
  const htmlWhenHasImage = `
    <div class="card shadow-sm border-0">
      <div class="card-body">
        <h5 class="card-title mb-3">Imatge de perfil de ${nomComplet}</h5>

        <div class="mb-3">
          <img
            src="https://memoriaterrassa.cat/public/img/represaliats/${fitxa.img ?? ''}.jpg"
            alt="Imatge de perfil de ${nomComplet}"
            class="img-fluid rounded"
            style="max-height: 360px; object-fit: cover;"
            loading="lazy"
          />
        </div>

        <a href="${base}/modifica-imatge/${fitxa.id}" target="_blank" rel="noopener"
           class="btn btn-primary">
          Modificar imatge
        </a>
      </div>
    </div>
  `;

  const htmlWhenNoImage = `
    <div class="card shadow-sm border-0">
      <div class="card-body">
        <h5 class="card-title mb-3">Imatge de perfil</h5>
        <p class="text-muted mb-3">No hi ha cap imatge de perfil.</p>
        <a href="${base}/nova-imatge/${fitxa.id}" target="_blank" rel="noopener"
           class="btn btn-outline-primary">
          Subir imatge
        </a>
      </div>
    </div>
  `;

  container.innerHTML = hasImage ? htmlWhenHasImage : htmlWhenNoImage;
}
