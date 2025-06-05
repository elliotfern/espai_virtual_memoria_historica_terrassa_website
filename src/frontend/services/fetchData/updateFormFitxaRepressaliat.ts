import { missatgesBackend } from './missatgesBackend';
import { resetForm } from './resetForm';
import { Missatges } from '../textosIdiomes/missatges';

export async function enviarDadesFormFitxaRepressaliat(event: Event, method: 'POST' | 'PUT' = 'PUT', neteja?: boolean): Promise<void> {
  event.preventDefault();

  const form = document.getElementById('formFitxaRepressaliat') as HTMLFormElement | null;
  const formId = 'formFitxaRepressaliat';

  if (!form) return;

  const formData: Record<string, string> = {};
  new FormData(form).forEach((value, key) => {
    formData[key] = value.toString();
  });

  // Categorías
  const selectedCategories: string[] = [];
  document.querySelectorAll<HTMLInputElement>('input[name="categoria"]:checked').forEach((checkbox) => {
    selectedCategories.push(checkbox.value.replace('categoria', ''));
  });
  formData['categoria'] = `{${selectedCategories.join(',')}}`;

  // Filiació política
  const selectedPartits: string[] = [];
  document.querySelectorAll<HTMLInputElement>('input[name="partido"]:checked').forEach((checkbox) => {
    selectedPartits.push(checkbox.value.replace('partido', ''));
  });
  formData['filiacio_politica'] = `{${selectedPartits.join(',')}}`;

  // Filiació sindical
  const selectedSindicats: string[] = [];
  document.querySelectorAll<HTMLInputElement>('input[name="sindicat"]:checked').forEach((checkbox) => {
    selectedSindicats.push(checkbox.value.replace('sindicat', ''));
  });
  formData['filiacio_sindical'] = `{${selectedSindicats.join(',')}}`;

  const tipusApi = method.toLowerCase();

  const jsonData = JSON.stringify(formData);
  const devDirectory = `https://${window.location.hostname}`;
  const urlAjax = `${devDirectory}/api/dades_personals/${tipusApi}`;

  try {
    const response = await fetch(urlAjax, {
      method,
      headers: {
        'Content-Type': 'application/json',
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
        if (neteja && formId) {
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
  } catch (error: unknown) {
    const errMessageDiv = document.getElementById('errMessage');
    const errTextDiv = document.getElementById('errText');

    if (!errMessageDiv || !errTextDiv) return;

    const errorMessage = typeof error === 'object' && error && 'message' in error ? String((error as { message: string }).message) : "S'ha produït un error a la xarxa.";

    missatgesBackend({
      tipus: 'error',
      missatge: errorMessage,
      contenidor: errMessageDiv,
      text: errTextDiv,
    });
  }
}
