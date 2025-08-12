import { missatgesBackend } from './missatgesBackend';
import { resetForm } from './resetForm';
import { Missatges } from '../textosIdiomes/missatges';

export async function transmissioDadesDB(event: Event, tipus: string, formId: string, urlAjax: string, neteja?: boolean): Promise<void> {
  event.preventDefault();

  const form = document.getElementById(formId) as HTMLFormElement;
  if (!form) {
    console.error(`Form with id ${formId} not found`);
    return;
  }

  // Crear un objeto para almacenar los datos del formulario
  const formDataRaw = new FormData(form);
  const formData: { [key: string]: FormDataEntryValue } = {};

  formDataRaw.forEach((value, key) => {
    formData[key] = value;
  });

  const jsonData = JSON.stringify(formData);

  try {
    const response = await fetch(urlAjax, {
      method: tipus,
      headers: {
        'Content-Type': 'application/json',
        Accept: 'application/json',
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

        if (neteja) {
          resetForm(formId);
        }
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
