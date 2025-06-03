export async function transmissioDadesDB(event: Event, tipus: string, formId: string, urlAjax: string, netejaForm?: boolean): Promise<void> {
  event.preventDefault();

  const form = document.getElementById(formId) as HTMLFormElement;
  if (!form) {
    console.error(`Form with id ${formId} not found`);
    return;
  }

  // Crear un objeto para almacenar los datos del formulario
  const formData: { [key: string]: FormDataEntryValue } = {};

  Array.from(form.elements).forEach((element) => {
    if (element instanceof HTMLInputElement || element instanceof HTMLTextAreaElement || element instanceof HTMLSelectElement) {
      if (element.name) {
        formData[element.name] = element.value;
      }
    }
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

    const okMessageDiv = document.getElementById('okMessage');
    const okTextDiv = document.getElementById('okText');
    const errMessageDiv = document.getElementById('errMessage');
    const errTextDiv = document.getElementById('errText');

    const responseData = await response.json();

    if (response.ok && responseData.status === 'success') {
      if (okMessageDiv && okTextDiv && errMessageDiv) {
        okMessageDiv.style.display = 'block';
        okTextDiv.textContent = responseData.message || "Les dades s'han desat correctament!";
        errMessageDiv.style.display = 'none';
        okMessageDiv.scrollIntoView({ behavior: 'smooth', block: 'center' });

        setTimeout(() => {
          okMessageDiv.style.display = 'none';
        }, 15000);

        if (netejaForm) {
          limpiarFormulario(formId);
        }
      }
    } else {
      if (errMessageDiv && errTextDiv) {
        errMessageDiv.style.display = 'block';
        errMessageDiv.scrollIntoView({ behavior: 'smooth', block: 'center' });

        // Manejo robusto del mensaje de error
        if (Array.isArray(responseData.message)) {
          // Mostrar como lista HTML si hay múltiples errores
          errTextDiv.innerHTML = `<ul>${responseData.message.map((msg: string) => `<li>${msg}</li>`).join('')}</ul>`;
        } else if (typeof responseData.message === 'string') {
          errTextDiv.textContent = responseData.message;
        } else {
          errTextDiv.textContent = "S'ha produït un error a la base de dades.";
        }

        setTimeout(() => {
          errMessageDiv.style.display = 'none';
        }, 15000);
      }
    }
  } catch (error) {
    console.error('Error:', error);
    const errMessageDiv = document.getElementById('errMessage');
    const errTextDiv = document.getElementById('errText');

    if (errMessageDiv && errTextDiv) {
      errMessageDiv.style.display = 'block';
      errTextDiv.textContent = 'Error de xarxa o resposta invàlida del servidor.';
      errMessageDiv.scrollIntoView({ behavior: 'smooth', block: 'center' });

      setTimeout(() => {
        errMessageDiv.style.display = 'none';
      }, 15000);
    }
  }
}

function limpiarFormulario(formId: string) {
  const formulario = document.getElementById(formId) as HTMLFormElement;
  const inputs = formulario.querySelectorAll('input, textarea, select, trix-editor');

  inputs.forEach((input) => {
    if (input instanceof HTMLInputElement || input instanceof HTMLTextAreaElement) {
      input.value = ''; // Limpiar el valor del campo
    }
    if (input instanceof HTMLSelectElement) {
      input.selectedIndex = 0; // Limpiar el select (poner el primer valor por defecto)
    }
    if (input instanceof HTMLElement && input.tagName === 'TRIX-EDITOR') {
      const trixEditor = input as HTMLTrixEditorElement;
      trixEditor.editor.loadHTML(''); // Limpiar el contenido del editor Trix
    }
  });
}

interface HTMLTrixEditorElement extends HTMLElement {
  editor: {
    loadHTML: (html: string) => void;
  };
}
