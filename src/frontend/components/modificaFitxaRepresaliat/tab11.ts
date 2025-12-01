// src/pages/fitxa/tabs/tabAdjunts.ts
import { Fitxa } from '../../types/types';

export function tab11Adjunts(containerId: string, fitxa?: Fitxa | null): void {
  const container = document.getElementById(containerId);
  if (!container) return;

  const titleName = getNomComplet(fitxa);

  container.innerHTML = tab11({ titleName });
}

/** Card bàsica per a adjunts (JPG/PDF) */
function tab11(args: { titleName: string }): string {
  const { titleName } = args;

  return `
    <div class="card shadow-sm border-0">
      <div class="card-body">
        <h5 class="card-title mb-3">
          Galeria d'adjunts${titleName ? ` de ${titleName}` : ''}
        </h5>

        <!-- Aquí mostrarem la galeria (imatges + PDFs) -->
        <div id="galeriaWrapper">
          <p class="text-muted mb-3">
            Encara no hi ha fitxers a la galeria.
          </p>
        </div>

        <!-- Input hidden on guardarem els IDs dels fitxers (separats per comes) -->
        <input type="hidden" id="adjuntsHidden" value="">

        <!-- Caixa de pujada d'un sol fitxer (JPG o PDF) -->
        <div id="adjuntUploadBox" class="container"
             style="margin-bottom:12px;border:1px solid #ced4da;border-radius:10px;padding:16px;background-color:#f8f9fa">
          <div class="row g-3">
            <div class="col-12 d-flex align-items-center justify-content-between">
              <h6 class="mb-2" id="formTitleAdjunt">Afegir fitxer a la galeria</h6>
            </div>

            <!-- Missatges OK / Error -->
            <div class="alert alert-success d-none" role="alert" id="okMessageAdjunt" aria-live="polite">
              <div id="okTextAdjunt"></div>
            </div>
            <div class="alert alert-danger d-none" role="alert" id="errMessageAdjunt" aria-live="polite">
              <div id="errTextAdjunt"></div>
            </div>

            <!-- Nom lògic del fitxer -->
            <div class="col-md-4">
              <label for="nomAdjunt" class="form-label">Nom fitxer</label>
              <input type="text" class="form-control" id="nomAdjunt"
                     maxlength="120" placeholder="p. ex., retrat_extra_001">
            </div>

            <!-- Fitxer (JPG o PDF) -->
            <div class="col-md-5">
              <label for="arxiuAdjunt" class="form-label">
                Selecciona el fitxer (JPG o PDF, ≤ 3 MB)
              </label>
              <input class="form-control" type="file" id="arxiuAdjunt"
                     accept=".jpg,.jpeg,application/pdf">
              <div class="form-text">
                Només es permeten fitxers JPG o PDF. Mida màxima: 3 MB.
              </div>
            </div>

            <!-- Botó d'afegir (més endavant farà la pujada) -->
            <div class="col-md-3 d-flex align-items-end">
              <button class="btn btn-success w-100" id="btnAfegirAdjunt" type="button">
                Afegir fitxer
              </button>
            </div>

            <!-- Vista prèvia (només per imatges; ho farem al pas 2) -->
            <div class="col-12">
              <img id="previewAdjunt" class="img-thumbnail d-none mt-2" alt="Vista prèvia"
                   style="max-height:220px;object-fit:cover">
              <small id="previewAdjuntInfo" class="text-muted d-none mt-1"></small>
            </div>
          </div>

          <small class="text-muted d-block mt-2">
            Els fitxers pujats <strong>encara no estan vinculats</strong> a la fitxa.
            Es vincularan quan premis “Guardar fitxa”.
          </small>
        </div>
      </div>
    </div>
  `;
}

/** Reutilitzo la mateixa funció que tens al teu codi original */
function getNomComplet(fitxa?: Fitxa | null): string {
  const parts = [fitxa?.nom, fitxa?.cognom1, fitxa?.cognom2].filter(Boolean) as string[];
  return parts.join(' ').trim() || (fitxa?.id ? `ID ${fitxa.id}` : 'Nova fitxa');
}
