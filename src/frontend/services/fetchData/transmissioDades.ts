import { missatgesBackend } from './missatgesBackend';
import { resetForm } from './resetForm';
import { Missatges } from '../textosIdiomes/missatges';

// Comportamiento genérico en éxito
type SuccessBehavior = 'none' | 'hide' | 'disable';

function formDataToJson(form: HTMLFormElement): Record<string, unknown> {
  const formDataRaw = new FormData(form);
  const result: Record<string, unknown> = {};

  // Recoger claves únicas sin usar formData.keys()
  const keysSet = new Set<string>();
  formDataRaw.forEach((_, key) => {
    keysSet.add(key);
  });

  keysSet.forEach((key) => {
    const values = formDataRaw.getAll(key).map((v) => String(v));

    // Campos tipo autors[]
    if (key.endsWith('[]')) {
      const cleanKey = key.slice(0, -2);
      result[cleanKey] = values;
      return;
    }

    // Si hay más de un valor para la misma clave, guardamos array
    if (values.length > 1) {
      result[key] = values;
      return;
    }

    // Valor simple
    result[key] = values.length > 0 ? values[0] : '';
  });

  return result;
}

export async function transmissioDadesDB(event: Event, tipus: string, formId: string, urlAjax: string, neteja?: boolean, successBehavior: SuccessBehavior = 'none'): Promise<void> {
  event.preventDefault();

  const form = document.getElementById(formId) as HTMLFormElement | null;
  if (!form) {
    console.error(`Form with id ${formId} not found`);
    return;
  }

  const formData = formDataToJson(form);
  const jsonData = JSON.stringify(formData);

  try {
    const response = await fetch(urlAjax, {
      method: tipus,
      headers: {
        'Content-Type': 'application/json',
        Accept: 'application/json',
        'X-Requested-With': 'XMLHttpRequest',
        'User-Agent': 'Mozilla/5.0 (WebApp MemoriaTerrassa)',
      },
      body: jsonData,
    });

    const data = await response.json();

    const okMessageDiv = document.getElementById('okMessage');
    const okTextDiv = document.getElementById('okText');
    const errMessageDiv = document.getElementById('errMessage');
    const errTextDiv = document.getElementById('errText');

    if (!okMessageDiv || !okTextDiv || !errMessageDiv || !errTextDiv) return;

    if (response.ok) {
      if (data.status === 'success') {
        missatgesBackend({
          tipus: 'success',
          missatge: data.message || Missatges.success.default,
          contenidor: okMessageDiv,
          text: okTextDiv,
          altreContenidor: errMessageDiv,
        });

        if (successBehavior === 'hide') {
          form.hidden = true;
          history.replaceState({}, document.title, window.location.pathname);
        } else if (successBehavior === 'disable') {
          form.querySelectorAll<HTMLElement>('input,select,textarea,button,[contenteditable],trix-editor').forEach((el) => el.setAttribute('disabled', 'true'));
          history.replaceState({}, document.title, window.location.pathname);
        } else if (neteja) {
          resetForm(formId);
        }

        const ev = new CustomEvent('form:success', { detail: data });
        form.dispatchEvent(ev);
      } else {
        const missatge = `
          ${data.message ? `<p>${data.message}</p>` : ''}
          ${data.errors && data.errors.length > 0 ? `<ul>${data.errors.map((e: string) => `<li>${e}</li>`).join('')}</ul>` : `<p>${Missatges.error.default}</p>`}
        `;

        missatgesBackend({
          tipus: 'error',
          missatge,
          contenidor: errMessageDiv,
          text: errTextDiv,
          altreContenidor: okMessageDiv,
        });
      }
    } else {
      const missatge = `
        ${data.message ? `<p>${data.message}</p>` : ''}
        ${data.errors && data.errors.length > 0 ? `<ul>${data.errors.map((e: string) => `<li>${e}</li>`).join('')}</ul>` : ``}
      `;

      missatgesBackend({
        tipus: 'error',
        missatge,
        contenidor: errMessageDiv,
        text: errTextDiv,
        altreContenidor: okMessageDiv,
      });
    }
  } catch (error) {
    const errMessageDiv = document.getElementById('errMessage');
    const errTextDiv = document.getElementById('errText');

    if (!errMessageDiv || !errTextDiv) return;

    const errorMessage = typeof error === 'object' && error && 'message' in error ? String((error as { message: string }).message) : Missatges.error.xarxa;

    missatgesBackend({
      tipus: 'error',
      missatge: errorMessage,
      contenidor: errMessageDiv,
      text: errTextDiv,
    });
  }
}
