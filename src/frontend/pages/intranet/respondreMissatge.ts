const RESPOSTA_API_URL = 'https://memoriaterrassa.cat/api/form_contacte/resposta/post';

export function renderRespostaForm(missatgeId: number): void {
  const container = document.getElementById('missatgeRespostaId');
  if (!container) return;

  // HTML del formulario (Bootstrap 5)
  container.innerHTML = `
    <div class="card mt-4">
      <div class="card-header">
        <h5 class="mb-0">Respondre el missatge</h5>
      </div>
      <div class="card-body">
        <form id="formRespostaMissatge" novalidate>
          <div class="mb-3">
            <label for="respostaSubject" class="form-label">Assumpte</label>
            <input
              type="text"
              id="respostaSubject"
              name="subject"
              class="form-control"
              value="Resposta al teu missatge a Memòria Terrassa"
              maxlength="255"
              required
            />
            <div class="form-text">
              Es pot personalitzar l'assumpte si cal.
            </div>
          </div>

          <div class="mb-3">
            <label for="respostaText" class="form-label">Missatge de resposta</label>
            <textarea
              id="respostaText"
              name="resposta_text"
              class="form-control"
              rows="6"
              required
            ></textarea>
            <div class="form-text">
              Aquest text s'enviarà per correu a la persona que va fer el formulari.
            </div>
          </div>

          <div id="respostaAlert" class="mb-3" style="display:none;"></div>

          <button
            type="submit"
            class="btn btn-primary"
            id="btnEnviarResposta"
          >
            Enviar resposta
          </button>
        </form>
      </div>
    </div>
  `;

  const form = container.querySelector<HTMLFormElement>('#formRespostaMissatge');
  const subjectInput = container.querySelector<HTMLInputElement>('#respostaSubject');
  const textArea = container.querySelector<HTMLTextAreaElement>('#respostaText');
  const alertBox = container.querySelector<HTMLDivElement>('#respostaAlert');
  const submitBtn = container.querySelector<HTMLButtonElement>('#btnEnviarResposta');

  if (!form || !subjectInput || !textArea || !alertBox || !submitBtn) return;

  form.addEventListener('submit', async (event) => {
    event.preventDefault();

    // Limpiar alertas anteriores
    alertBox.style.display = 'none';
    alertBox.className = '';
    alertBox.innerHTML = '';

    const subject = subjectInput.value.trim();
    const respostaText = textArea.value.trim();

    if (!subject || !respostaText) {
      alertBox.style.display = 'block';
      alertBox.className = 'alert alert-warning';
      alertBox.textContent = "Cal indicar l'assumpte i el missatge de resposta.";
      return;
    }

    // Desactivar botón mientras enviamos
    submitBtn.disabled = true;
    submitBtn.textContent = 'Enviant resposta...';

    try {
      const payload = {
        missatge_id: missatgeId,
        subject: subject,
        resposta_text: respostaText,
      };

      const response = await fetch(RESPOSTA_API_URL, {
        method: 'POST',
        headers: {
          'Content-Type': 'application/json',
          Accept: 'application/json',
        },
        body: JSON.stringify(payload),
        credentials: 'include', // importante para enviar la cookie de sessió
      });

      const data = await response.json().catch(() => null);

      if (!response.ok || !data || data.status !== 'success') {
        const message = data && data.message ? data.message : "S'ha produït un error en enviar la resposta.";

        alertBox.style.display = 'block';
        alertBox.className = 'alert alert-danger';
        alertBox.textContent = message;
      } else {
        // Éxito
        alertBox.style.display = 'block';
        alertBox.className = 'alert alert-success';
        alertBox.textContent = 'Resposta enviada correctament.';

        // Si quieres, limpia el textarea
        textArea.value = '';

        // Incluso podrías desactivar el formulario si no quieres más respuestas
        // form.querySelectorAll("input, textarea, button").forEach(el => (el as HTMLInputElement).disabled = true);
      }
    } catch (error) {
      console.error('Error enviant la resposta:', error);
      alertBox.style.display = 'block';
      alertBox.className = 'alert alert-danger';
      alertBox.textContent = "S'ha produït un error de connexió amb el servidor.";
    } finally {
      submitBtn.disabled = false;
      submitBtn.textContent = 'Enviar resposta';
    }
  });
}
